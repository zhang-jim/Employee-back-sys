<?php
session_start();
if (isset($_SESSION['account']) && !empty($_POST['message'])) {
    require_once 'database/db.php';
    require_once 'Model/message.php';
    $message = $_POST['message'];
    #新增留言
    $messageModel = new Message($pdo);
    $messageModel->createMessage($_SESSION['user_id'], $message);
    header("Location:index.php");
}
echo "未登入，無法留言 >><a href='Auth/login.php'>登入</a>";
