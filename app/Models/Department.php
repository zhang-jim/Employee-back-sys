<?php

namespace App\Models;

use PDO;

class Department
{
    private $pdo;
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }
    public function getById($id){
        $stmt = $this->pdo->prepare("SELECT * FROM departments WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getByName($name){
        $stmt = $this->pdo->prepare("SELECT * FROM departments WHERE name = ?");
        $stmt->execute([$name]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getAll()
    {
        $stmt = $this->pdo->query("SELECT * FROM departments");
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function create($name, $work_start, $work_end)
    {
        $stmt = $this->pdo->prepare("INSERT INTO departments (name,work_start,work_end) VALUE (?,?,?)");
        $stmt->execute([$name, $work_start, $work_end]);
    }
    public function update($name, $work_start, $work_end, $departmentID)
    {
        $stmt = $this->pdo->prepare("UPDATE departments SET (name,work_start,work_end) VALUE (?,?,?) WHERE id = ?");
        $stmt->execute([$name, $work_start, $work_end, $departmentID]);
    }
    public function delete($departmentID)
    {
        $stmt = $this->pdo->prepare("DELETE FROM departments WHERE id = ?");
        $stmt->execute([$departmentID]);
    }
    // 計算部門人數
    public function countMembersByDepartmentID($departmentID){
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE department_id = ?");
        $stmt->execute([$departmentID]);
        return $stmt->fetchcolumn();
    }
}
