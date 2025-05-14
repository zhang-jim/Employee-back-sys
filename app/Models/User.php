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
    // 登入
    public function getByAccount($account)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = :account OR phone_number = :account");
        $stmt->execute(['account' => $account]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // 查詢用戶Token, is_verified
    public function getByToken($token)
    {
        $stmt = $this->pdo->prepare("SELECT id, is_verified FROM users WHERE token = ?");
        $stmt->execute([$token]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // 查詢用戶開放資料
    public function getUser($userID)
    {
        $stmt = $this->pdo->prepare("SELECT 
        departments.name,
        user.email,
        users.name,
        users.nickname,
        users.sex,
        users.birthday,
        users.phone_number,
        users.created_at 
        FROM users JOIN departments ON users.department_id = departments.id WHERE users.id = ?");
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
    public function create($data)
    {
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');
        $values = array_values($data);

        $stmt = $this->pdo->prepare(
            "INSERT INTO users (" . implode(",", $columns) . ") VALUE (" . implode(",", $placeholders) . ")"
        );
        return $stmt->execute($values);
    }
    // 編輯用戶資料
    public function update($userID, $data)
    {
        $columns = array_keys($data);
        $values = array_values($data);
        $values[] = $userID;

        $stmt = $this->pdo->prepare("UPDATE users SET " . implode(',', $columns) . " WHERE id = ?");
        $stmt->execute($values);
        return [
            'affected_rows' => $stmt->rowCount()
        ];
    }
    // 重設密碼
    public function updatePassword($userID, $password)
    {
        $stmt = $this->pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$password, $userID]);
    }
    // 更新Email驗證
    public function updateEmailVerify($userID)
    {
        $stmt = $this->pdo->prepare("UPDATE users SET token = NULL, is_verified = 1 WHERE id = ?");
        return $stmt->execute([$userID]);
    }
    public function delete($userID)
    {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$userID]);
    }
}
