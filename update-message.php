<?php
session_start();
include_once 'database/db.php';
include_once 'Model/message.php';
// 訊息、ID是否存在
if (!isset($_POST["message_id"]) && !isset($_POST["new-message"])) {
    exit();
}
$messageID = $_POST["message_id"];
$newMessage = $_POST["new-message"];
$messageModel = new Message($pdo);
$message = $messageModel->getMessage($messageID);
if ($_SESSION['user_id'] !== $message['user_id']) {
    exit();
}
// 判斷資料是否有更動
if ($_POST['new-message'] === $message['content']) {  
    exit();
}
$messageModel = new Message($pdo);
$messageModel->update($messageID, $newMessage);
header("Location:index.php");
exit();
