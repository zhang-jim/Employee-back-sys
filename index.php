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
    } else {
        echo "<a href='Auth/login.php'>登入</a>";
    }
    ?>
    <?php
    if (isset($_SESSION['account'])) {
        echo '
        <form action="createmessage.php" method="post">
            留言內容：<textarea name="message" maxlength="100" required></textarea>
            <input type="submit" value="送出">
        </form>';
    }
    ?>
    <!-- 顯示所有留言 -->
    <?php
    $file_path = './messageboard.txt';
    if (filesize($file_path) > 0) {
        $message_array = array_reverse(file($file_path));
        echo "<ul>";
        foreach ($message_array as $value) {
            echo "<li>" . htmlspecialchars($value) . "</li>";
        }
        echo "</ul>";
    } else {
        echo "目前沒有任何留言！";
    }
    ?>
</body>

</html>