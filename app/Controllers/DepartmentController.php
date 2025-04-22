<?php

namespace App\Controllers;

use App\Services\DepartmentService;

class DepartmentController extends Controller
{
    private $departmentService;
    public function __construct($pdo)
    {
        $this->departmentService = new DepartmentService($pdo);
    }
    public function index(){
        //  驗證登入、是否為管理員
        $this->requireLogin();
        $this->requireAdmin();
        $departments = $this->departmentService->showAllDepartment();
        if(!$departments){
            $this->jsonResponse(false,"尚未建立部門");
        }
        $this->jsonResponse(true,$departments);
    }
    public function store()
    {
        //  驗證登入、是否為管理員
        $this->requireLogin();
        $this->requireAdmin();
        $input = json_decode(file_get_contents('php://input', true));
        $name = $input['name'] ?? null;
        if (!$name) {
            $this->jsonResponse(false, '部門名稱未輸入');
        }
        try {
            $this->departmentService->createDepartment($input);
            $this->jsonResponse(true, "部門新增成功！");
        } catch (\Throwable $e) {
            $this->jsonResponse(false, $e->getMessage());
        }
    }
    public function update($id)
    {
        //  驗證登入、是否為管理員
        $this->requireLogin();
        $this->requireAdmin();
        if (!$id) {
            $this->jsonResponse(false, '請求失敗');
        }
        $input = json_decode(file_get_contents('php://input', true));
        $name = $input['name'] ?? null;
        $work_start = $input['work-start'] ?? null;
        $work_end = $input['work-end'] ?? null;
        if (!$name || !$work_start || !$work_end) {
            $this->jsonResponse(false, '資料不完整');
        }
        try {
            $this->departmentService->updateDepartment($id, $name, $work_start, $work_end);
            $this->jsonResponse(true, "部門資料修改成功");
        } catch (\Throwable $e) {
            $this->jsonResponse(false, $e->getMessage());
        }
    }
    public function delete($id)
    {
        //  驗證登入、是否為管理員
        $this->requireLogin();
        $this->requireAdmin();
        if (!$id) {
            $this->jsonResponse(false, '請求失敗');
        }
        try {
            $this->departmentService->deleteDepartment($id);
            $this->jsonResponse(true, "部門刪除成功");
        } catch (\Throwable $e) {
            $this->jsonResponse(false,$e->getMessage());
        }
    }
}
