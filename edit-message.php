<?php
session_start();
include_once 'database/db.php';
include_once 'Model/message.php';
#判斷是否登入、ID是否存在
if (!isset($_SESSION['user_id']) || empty($_GET['message_id'])) {
    header("Location:./");
    exit();
}
$messageID = $_GET['message_id'];
$messageModel = new Message($pdo);
$message = $messageModel->getMessage($messageID);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>編輯留言</title>
</head>

<body>
    <h1>編輯留言</h1>
    <form action="update-message.php" method="post">
        <input type="hidden" name="message_id" value="<?= $message['id'] ?>">
        留言內容：<textarea name="new-message" maxlength="100" required><?= $message['content'] ?></textarea>
        <input type="submit" value="送出">
    </form>
</body>

</html>