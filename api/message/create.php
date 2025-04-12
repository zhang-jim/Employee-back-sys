<?php
session_start();
header('Content-Type:application/json');
// 判斷是否登入
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => '請登入帳戶']);
    exit();
}
include_once '../../database/db.php';
include_once '../../Model/message.php';
// 取得前端JSON
$input = json_decode(file_get_contents('php://input'), true);
$message = trim($input['message'] ?? '');
// 判斷留言是否為空
if (empty($message)) {
    echo json_encode(['success' => false, 'message' => '留言文字為空，無法新增']);
    exit();
}
// 新增留言
$messageModel = new Message($pdo);
$messageModel->create($_SESSION['user_id'], $message);
echo json_encode(['success' => true, 'message' => '留言新增成功！']);
exit();
