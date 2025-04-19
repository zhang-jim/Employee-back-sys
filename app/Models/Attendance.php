<?php

namespace App\Models;

use PDO;

class Attendance
{
    private $pdo;
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }
    // 查詢 單一用戶當日打卡紀錄
    public function getToday($userID, $date)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM attendances WHERE user_id = ? and date = ?");
        $stmt->execute([$userID, $date]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // 查詢 單一用戶所有打卡紀錄
    public function getAttendance($userID)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM attendances WHERE user_id = ? ORDER BY date DESC LIMIT 20");
        $stmt->execute([$userID]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // 查詢 所有用戶所有打卡紀錄(限管理員)
    public function getAll()
    {
        $stmt = $this->pdo->query("SELECT attendances.id,attendances.date,attendances.check_in,attendances.check_out,user.account FROM attendances JOIN users ON attendances.user_id = users.id ORDER BY date DESC LIMIT 20");
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // 新增 打卡紀錄(上班打卡)
    public function create($userID, $date, $checkIn)
    {
        $stmt = $this->pdo->prepare("INSERT INTO attendances (user_id,date,check_in) VALUE (?,?,?)");
        $stmt->execute([$userID, $date, $checkIn]);
    }
    // 編輯 打卡紀錄(下班打卡)
    public function update($userID,$date,$checkOut) {
        $stmt = $this->pdo->prepare("UPDATE attendances SET check_out = ? WHERE user_id = ? and date = ?");
        $stmt->execute([$checkOut,$userID,$date]);
    }
    public function delete($attendanceID)
    {
        $stmt = $this->pdo->prepare("DELETE FROM attendances WHERE id =?");
        $stmt->execute([$attendanceID]);
    }
}
