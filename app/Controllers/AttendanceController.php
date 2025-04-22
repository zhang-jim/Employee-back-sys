<?php

namespace App\Controllers;

use App\Services\AttendanceService;
use App\Controllers;

class AttendanceController extends Controller
{
    private $attendanceService;
    public function __construct($pdo)
    {
        $this->attendanceService = new AttendanceService($pdo);
    }
    // 打卡頁面
    public function index() {
        return view('/check-in');
    }
    // 上班打卡
    public function store()
    {
        // 判斷登入
        $this->requireLogin();
        // 取前端值
        $input = json_decode(file_get_contents('php://input'), true);
        $type = $input['type'] ?? null;
        //新增打卡紀錄時，檢查type是否為上班
        if ($type !== 'check-in') {
            $this->jsonResponse(false, '類型錯誤');
        }
        try {
            $this->attendanceService->checkIn();
            $this->jsonResponse(true, "上班打卡成功");
        } catch (\Throwable $e) {
            $this->jsonResponse(false, $e->getMessage());
        }
    }
    // 下班打卡
    public function update()
    {
        $this->requireLogin(); //判斷登入
        $input = json_decode(file_get_contents('php://input'), true);
        $type = $input['type'] ?? null;
        // 新增打卡紀錄時，檢查type是否為下班
        if ($type !== 'check-out') {
            $this->jsonResponse(false, '類型錯誤');
        }
        try {
            $this->attendanceService->checkOut();
            $this->jsonResponse(true, "下班打卡成功！");
        } catch (\Throwable $e) {
            $this->jsonResponse(false, $e->getMessage());
        }
    }
}
