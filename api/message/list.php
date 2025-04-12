<?php
header('Content-Type:application/json');

include_once '../../database/db.php';
include_once '../../Model/message.php';

$messageModel = new Message($pdo);
$messages = $messageModel->getAll();
echo json_encode(['success' => true, 'messages' => $messages]);
exit();
