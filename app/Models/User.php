<?php

namespace App\Models;

use PDO;

class User
{
    private $pdo;
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }
    public function findByFields($fields)
    {
        $conditions = [];
        $params = [];
        foreach ($fields as $key => $value) {
            $conditions[] = "$key = ?";
            $params[] = $value;
        }
        $sql = "SELECT * FROM users WHERE " . implode(" OR ", $conditions) . " LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getByAccount($account)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = :account OR phone_number = :account");
        $stmt->execute(['account' => $account]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // 查詢用戶資料
    public function getUser($userID)
    {
        $stmt = $this->pdo->prepare("SELECT departments.name, users.name, users.nickname, users.phone_number, users.created_at FROM users JOIN departments ON users.department_id = departments.id WHERE users.id = ?");
        $stmt->execute([$userID]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // 查詢私密資料
    public function privateGetUser($userID)
    {
        $stmt = $this->pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$userID]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function create($email, $password, $name, $nickname, $sex, $birthday, $phonenumber, $onBoardDate, $departmentId)
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO users (email,password,name,nickname,sex,birthday,phone_number,on_board_date,department_id) VALUE (?,?,?,?,?,?,?,?,?)"
        );
        $stmt->execute([$email, $password, $name, $nickname, $sex, $birthday, $phonenumber, $onBoardDate, $departmentId]);
    }
    // 編輯用戶資料
    public function update($userID, $nickname = null, $phone_number = null)
    {
        $stmt = $this->pdo->prepare("UPDATE users SET nickname = ?, phone_number = ? WHERE id = ?");
        $stmt->execute($nickname, $phone_number, $userID);
    }
    // 重設密碼
    public function updatePassword($userID, $password)
    {
        $stmt = $this->pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$password, $userID]);
    }
    public function delete($userID)
    {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$userID]);
    }
}
