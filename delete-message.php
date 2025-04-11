<?php
session_start();
include_once 'database/db.php';
#判斷是否登入
if (!isset($_SESSION['user_id'])) {
    exit();
}
#判斷ID是否存在
if (!isset($_POST['message_id'])) {
    exit();
}
$messageID = $_POST['message_id'];
#判斷使用者與留言者ID是否相符
$stmt = $pdo->prepare("SELECT * FROM messages WHERE id = ?");
$stmt->execute([$messageID]);
$message = $stmt->fetch(PDO::FETCH_ASSOC);
#判斷是否有資料
if (!$message) {
    exit();
}
#判斷使用者是否相符
if ($_SESSION['user_id'] !== $message['user_id']) {
    exit();
}

#執行刪除
$stmt = $pdo->prepare("DELETE FROM messages WHERE id = ?");
$stmt->execute([$messageID]);
header("Location:./");
exit();
