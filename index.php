<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <form action="createmessage.php" method="post">
        使用者名稱：<input type="text" name="username" maxlength="20" placeholder="請輸入使用者名稱" required>
        留言內容：<textarea name="message" maxlength="100" required></textarea>
        <input type="submit" value="送出">
    </form>
    <ul>
        <!-- 顯示所有留言 -->
        <?php
        $file_path = './messageboard.txt';
        $message_array = array_reverse(file($file_path));
        if (filesize($file_path) != 0) {
            foreach ($message_array as $value) {
                echo "<li>$value</li>";
            }
        }
        ?>
    </ul>
</body>

</html>