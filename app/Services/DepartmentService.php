<?php
namespace App\Services;

use App\Models\Department;
use Exception;

class DepartmentService{
    private $departmentModel;
    public function __construct($pdo){
        $this->departmentModel = new Department($pdo);
    }
    public function getDepartmentId($name){
        $department = $this->departmentModel->getByName($name);
        return $department['id'];
    }
    public function showAllDepartment(){
        return $this->departmentModel->getAll();
    }
    public function createDepartment($input){
        $name = $input['name'];
        $work_start = $input['work-start'];
        $work_end = $input['work-end'];
        if($this->departmentModel->getByName($name)){
            throw new Exception("部門已存在，無法新增");
        };
        $this->departmentModel->create($name,$work_start,$work_end);
    }
    public function updateDepartment($id,$name,$work_start,$work_end){
        if(!$this->departmentModel->getById($id)){
            throw new Exception("部門不存在，無法編輯");
        };
        $this->departmentModel->update($name,$work_start,$work_end,$id);
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