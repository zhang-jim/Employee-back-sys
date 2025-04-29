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
    // 顯示該用戶當月打卡紀錄
    public function index()
    {
        // 判斷登入
        $this->requireLogin();
        $results = $this->attendanceService->getRecord();
        if($results){
            $this->jsonResponse(true,$results['message']);
        }else{
            $this->jsonResponse(false,$results['message']);
        }
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
    // 打卡紀錄 Page
    public function show()
    {
        return view('check-record');
    }
}
