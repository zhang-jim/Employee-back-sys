<?php
session_start();
include_once 'database/db.php';
#判斷是否登入
if(!isset($_SESSION['user_id'])){
    header("Location:./"); 
    exit();
}
if (isset($_GET['message_id'])) {
    $messageID = $_GET['message_id'];
    $stmt = $pdo->prepare("SELECT * FROM messages WHERE id = ?");
    $stmt->execute([$messageID]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
};

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
        <input type="hidden" name="messageID" value="<?= $data['id'] ?>">
        留言內容：<textarea name="new-message" maxlength="100" required><?= $data['content'] ?></textarea>
        <input type="submit" value="送出">
    </form>
</body>

</html>