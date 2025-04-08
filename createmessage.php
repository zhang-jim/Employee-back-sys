<?php
if (!empty($_POST['username']) && !empty($_POST['message'])) {
    $message = "[" . date("Y-m-d H:i:s", time()) . "]" . $_POST['username'] . "：" . $_POST['message'] . "\n";
    $file_path = './messageboard.txt';
    $file = fopen($file_path, 'a+');
    fwrite($file, $message);
    fclose($file);
    header("Location:index.php");
}
