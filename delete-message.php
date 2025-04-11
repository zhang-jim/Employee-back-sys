<?php
session_start();
include_once 'database/db.php';
include_once 'Model/message.php';
#判斷是否登入
if (!isset($_SESSION['user_id'])) {
    exit();
}
#判斷ID是否存在
if (!isset($_POST['message_id'])) {
    exit();
}
$messageID = $_POST['message_id'];
$messageModel = new Message($pdo);
$message = $messageModel->getMessage($messageID);
#判斷是否有資料
if (!$message) {
    exit();
}
#判斷使用者是否相符
if ($_SESSION['user_id'] !== $message['user_id']) {
    exit();
}

#執行刪除
$messageModel = new Message($pdo);
$messageModel->deleteMessage($messageID);
header("Location:./");
exit();
