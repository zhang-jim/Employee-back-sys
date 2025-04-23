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

    public function getByAccount($account){
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE account = ?");
        $stmt->execute([$account]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // 查詢用戶資料
    public function getUser($userID)
    {
        $stmt = $this->pdo->prepare("SELECT departments.name, users.account, users.nickname, users.phone_number, users.created_at FROM users JOIN departments ON users.department_id = departments.id WHERE users.id = ?");
        $stmt->execute([$userID]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function create($account, $password)
    {
        $stmt = $this->pdo->prepare("INSERT INTO users (account,password) VALUE (?,?)");
        $stmt->execute([$account, $password]);
    }
    // 編輯用戶資料
    public function update($userID,$nickname = null,$phone_number = null){
        $stmt = $this->pdo->prepare("UPDATE users SET nickname = ?, phone_number = ? WHERE id = ?");
        $stmt->execute($nickname,$phone_number,$userID);
    }
    // 重設密碼
    public function updatePassword($userID,$password){
        $stmt = $this->pdo->prepare("UPDATE users SET password =? WHERE id = ?");
        $stmt->execute([$password,$userID]);
    }
    public function delete($userID)
    {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$userID]);
    }
}
