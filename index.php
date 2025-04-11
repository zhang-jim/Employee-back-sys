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
    ?>
    <!-- 顯示所有留言 -->
    <?php
    include_once 'database/db.php';
    $stmt = $pdo->query("SELECT messages.id,messages.content,messages.created_at,users.account FROM messages JOIN users ON messages.user_id = users.id ORDER BY created_at DESC");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<li>[" . $row['created_at'] . "] " . 
        htmlspecialchars($row['account']) . "：" . 
        htmlspecialchars($row['content']);
        // 
        if(isset($_SESSION['user_id']) && $_SESSION['account'] == $row['account']){
            echo "<a href='edit-message.php?message_id=" . $row['id'] . "'>編輯</a>";
            echo '
            <form action="delete-message.php" method="post">
                <input type="hidden" name="message_id" value="'.$row['id'].'">
                <input type="submit" value="刪除">
            </form>';
        }
        echo "</li>";
    }


    ?>
</body>

</html>