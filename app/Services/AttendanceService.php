<?php

namespace App\Services;

use App\Models\Attendance;
use Exception;

class AttendanceService
{
    private $attendanceModel;
    private $userID;
    private $date;
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
        $time = date('Y-m-d H:i:s');
        $this->attendanceModel->create($this->userID, $this->date, $time);
    }
    public function checkOut(){
        $todayAttendance = $this->todayCheck();
        if(!$todayAttendance){
            throw new Exception("今日未打卡上班，無法打卡下班");
        }
        if(!empty($todayAttendance['check_out'])){
            throw new Exception("今日已打過下班卡");
        }
        $time = date('Y-m-d H:i:s');
        $this->attendanceModel->update($this->userID, $this->date, $time);
    }
}
