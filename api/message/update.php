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
$newMessage = $input['new_message'];
// 訊息、ID是否為空
if (empty($messageID) || empty($newMessage)) {
    echo json_encode(['success' => false, 'message' => '資料不完整，編輯失敗']);
    exit();
}
// 查詢該筆資料
$messageModel = new Message($pdo);
$message = $messageModel->getMessage($messageID);
// 資料是否存在
if(!$message){
    echo json_encode(['success' => false, 'message' => '資料不存在，編輯失敗']);
    exit();
}
// 判斷是否有權限
if ($_SESSION['user_id'] !== $message['user_id']) {
    echo json_encode(['success' => false, 'message' => '權限不足，無法編輯']);
    exit();
}
// 判斷資料是否有更動
if ($newMessage === $message['content']) {
    echo json_encode(['success' => false, 'message' => '資料無異動']);
    exit();
}
// 執行編輯
$messageModel->update($messageID, $newMessage);
echo json_encode(['success'=>true,'message'=>'編輯成功！']);
exit();
