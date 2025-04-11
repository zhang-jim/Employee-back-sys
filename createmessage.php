<?php
session_start();
require_once 'database/db.php';
require_once 'Model/message.php';

if (!isset($_SESSION['user_id'])){
    header("Location:./");
    exit();
}

if (!empty($_POST['message'])) {
    $message = $_POST['message'];
    $messageModel = new Message($pdo);
    $messageModel->create($_SESSION['user_id'], $message);
}
header("Location:index.php");
exit();
