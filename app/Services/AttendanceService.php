<?php

namespace App\Services;

use App\Models\Attendance;
use DateTime;
use Exception;

class AttendanceService
{
    private $attendanceModel;
    private $userID;
    private $date;
    private $workStatus = [];
    public function __construct($pdo)
    {
        $this->attendanceModel = new Attendance($pdo);
    }
    // 判斷當日是否有紀錄
    private function todayCheck()
    {
        $this->userID = $_SESSION['user_id'];
        $this->date = date('Y-m-d');
        $attendance = $this->attendanceModel->getToday($this->userID, $this->date);
        return $attendance;
    }
    public function checkIn()
    {
        if ($this->todayCheck()) {
            throw new Exception("今日已打卡上班");
        }
        $now = new DateTime();
        $datetime = $now->format('Y-m-d H:i:s');
        $lateArray = $this->getStatus('late', $now);

        $this->workStatus = $lateArray['status'] ?? null;
        $lateMinutes = $lateArray['minute-late'] ?? 0;

        $this->attendanceModel->create($this->userID, $this->date, $datetime, $this->workStatus, $lateMinutes);
    }
    public function checkOut()
    {
        $todayAttendance = $this->todayCheck();
        if (!$todayAttendance) {
            throw new Exception("今日未打卡上班，無法打卡下班");
        }
        if (!empty($todayAttendance['check_out'])) {
            throw new Exception("今日已打過下班卡");
        }
        $now = new DateTime();
        $datetime = $now->format('Y-m-d H:i:s');
        $earlyArray = $this->getStatus('early', $now);
        $earlyArray['status'] ??= null;
        // 將值加入狀態陣列
        array_push($this->workStatus, $todayAttendance['status'], $earlyArray['status']);
        // 去除null
        $this->workStatus = array_filter($this->workStatus);
        // 陣列轉為字串
        $this->workStatus = implode(',', $this->workStatus);

        if (empty($this->workStatus)) {
            $this->workStatus = 'normal';
        }

        $this->attendanceModel->update($this->userID, $this->workStatus, $this->date, $datetime);
    }
    // 當日打卡狀態判斷(遲到、早退、)
    private function getStatus($status, $now)
    {
        $user_department_schedule = $this->attendanceModel->getUserDepartmentSchedule($_SESSION['user_id']);
        switch ($status) {
            case 'late':
                $workStart = new DateTime($now->format('Y-m-d') . $user_department_schedule['work_start']);
                // 判斷是否遲到
                if ($now > $workStart) {
                    // 計算遲到時間
                    $intervalInSeconds = $now->getTimestamp() - $workStart->getTimestamp();
                    $minutesLate = floor($intervalInSeconds / 60);
                    $this->workStatus = ['status' => 'late', 'minute-late' => $minutesLate];
                }
                break;
            case 'early':
                $workEnd = new DateTime($now->format('Y-m-d') . $user_department_schedule['work_end']);
                // 判斷是否早退
                if ($now < $workEnd) {
                    $this->workStatus = ['status' => 'early'];
                }
                break;
        }
        return $this->workStatus;
    }
}
