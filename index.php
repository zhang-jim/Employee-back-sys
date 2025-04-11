<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>留言板</title>
</head>

<body>
    <h1>留言板</h1>
    <?php
    session_start();
    include_once 'database/db.php';
    include_once 'Model/message.php';
    #判斷並顯示已登入的使用者 
    if (isset($_SESSION['account'])) {
        echo "使用者：" . $_SESSION['account'] . "<a href='Auth/logout.php'>登出</a>";
        echo '
        <form action="createmessage.php" method="post">
            留言內容：<textarea name="message" maxlength="100" required></textarea>
            <input type="submit" value="送出">
        </form>';
    } else {
        echo "<a href='Auth/login.php'>登入</a>";
    }
    // 顯示所有留言
    $messageModel = new Message($pdo);
    $data = $messageModel->getAllMessages();

    foreach ($data as $key => $value) {
        echo "<li>[" . $value['created_at'] . "] " .
            htmlspecialchars($value['account']) . "：" .
            htmlspecialchars($value['content']);
        if (isset($_SESSION['user_id']) && $_SESSION['account'] == $value['account']) {
            echo "<a href='edit-message.php?message_id=" . $value['id'] . "'>編輯</a>";
            echo '
            <form action="delete-message.php" method="post">
                <input type="hidden" name="message_id" value="' . $value['id'] . '">
                <input type="submit" value="刪除">
            </form>';
        }
        echo "</li>";
    }
    ?>
</body>

</html>