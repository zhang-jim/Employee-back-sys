<?php
session_start();
if (isset($_SESSION['account']) && !empty($_POST['message'])) {
    $message = "[" . date("Y-m-d H:i:s", time()) . "]" . $_SESSION['account'] . "：" . $_POST['message'] . "\n";
    $file_path = './messageboard.txt';
    $file = fopen($file_path, 'a+');
    fwrite($file, $message);
    fclose($file);
    header("Location:index.php");
}
echo "未登入，無法留言 >><a href='Auth/login.php'>登入</a>";
