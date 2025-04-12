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

$input = json_decode(file_get_contents('php://input'), true);
$messageID = $input['message_id'];
// 判斷ID是否存在
if (!isset($messageID)) {
    echo json_encode(['success' => false, 'message' => '資料不完整，刪除失敗']);
    exit();
}
$messageModel = new Message($pdo);
$message = $messageModel->getMessage($messageID);
// 判斷是否有資料
if (!$message) {
    echo json_encode(['success' => false, 'message' => '資料不存在，刪除失敗']);
    exit();
}
// 判斷使用者是否相符
if ($_SESSION['user_id'] !== $message['user_id']) {
    echo json_encode(['success' => false, 'message' => '權限不足，無法刪除']);
    exit();
}
// 執行刪除
$messageModel->delete($messageID);
echo json_encode(['success' => true, 'message' => '刪除成功！']);
exit();
