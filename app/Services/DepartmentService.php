<?php
namespace App\Services;

use App\Models\Department;
use Exception;

class DepartmentService{
    private $departmentModel;
    public function __construct($pdo){
        $this->departmentModel = new Department($pdo);
    }
    public function showAllDepartment(){
        return $this->departmentModel->getAll();
    }
    public function createDepartment($input){
        $name = $input['name'];
        $check_in_time = $input['check-in-time'];
        $check_out_time = $input['check-out-time'];
        if($this->departmentModel->getByName($name)){
            throw new Exception("該部門已存在，無法新增");
        };
        $this->departmentModel->create($name,$check_in_time,$check_out_time);
    }
    public function updateDepartment($id,$name,$check_in_time,$check_out_time){
        if(!$this->departmentModel->getById($id)){
            throw new Exception("部門不存在，無法編輯");
        };
        $this->departmentModel->update($name,$check_in_time,$check_out_time,$id);
    }
    public function deleteDepartment($id){
        // 判斷ID是否存在
        if(!$this->departmentModel->getById($id)){
            throw new Exception("部門不存在，無法刪除");
        }
        // 計算部門人數
        $count = $this->departmentModel->countMembersByDepartmentID($id);
        if($count > 0){
            throw new Exception("部門仍有員工，無法刪除");
        }
        $this->departmentModel->delete($id);
    }
}