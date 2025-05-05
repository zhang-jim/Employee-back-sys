<?php
namespace App\Models;
use PDO;
class Message
{
    private $pdo; 
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }
    // 查詢所有留言
    public function getAll()
    {
        $stmt = $this->pdo->query("SELECT messages.id,messages.content,messages.created_at,users.name FROM messages JOIN users ON messages.user_id = users.id ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // 查詢單一留言
    public function getMessage($messageID){
        $stmt = $this->pdo->prepare("SELECT * FROM messages WHERE id = ?");
        $stmt->execute([$messageID]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // 新增留言
    public function create($userID, $message)
    {
        $stmt = $this->pdo->prepare("INSERT INTO messages (user_id,content) VALUE (?,?)");
        $stmt->execute([$userID, $message]);
    }
    //編輯留言
    public function update($messageID, $newMessage)
    {
        $stmt = $this->pdo->prepare("UPDATE messages SET content = ? WHERE id = ?");
        $stmt->execute([$newMessage, $messageID]);
    }
    //刪除留言
    public function delete($messageID)
    {
        $stmt = $this->pdo->prepare("DELETE FROM messages WHERE id = ?");
        $stmt->execute([$messageID]);
    }
}
