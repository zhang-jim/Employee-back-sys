<?php
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
    // public function getUser($userID)
    // {
    //     $stmt = $this->pdo->query("SELECT * FROM users WHERE id = ?");
    //     $stmt->execute([$userID]);
    //     return $stmt->fetch(PDO::FETCH_ASSOC);
    // }
    public function create($account, $password)
    {
        $stmt = $this->pdo->prepare("INSERT INTO users (account,password) VALUE (?,?)");
        $stmt->execute([$account, $password]);
    }
    // public function update($id){
    //     $stmt = $this->pdo->prepare("UPDATE users SET WHERE id = ?");
    //     $stmt->execute($id);
    // }
    public function delete($userID)
    {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$userID]);
    }
}
