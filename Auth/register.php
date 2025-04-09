<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>註冊頁面</title>
</head>

<body>
    <h1>註冊頁面</h1>
    <form action="" method="post">
        帳號：<input type="text" name="account" required>
        密碼：<input type="password" name="password" required>
        <input type="submit" value="註冊">
    </form>
</body>

</html>
<?php
if (!empty($_POST['account']) && !empty($_POST['password'])) {
    $file_path = 'user.txt';
    // 檢查帳號是否存在
    $file_array = file($file_path);
    foreach ($file_array as $value) {
        list($user,$hash) = explode("|",trim($value));
        if($_POST['account'] === $user){
            echo "帳號已存在，無法註冊";
            exit();
        }
    }
    $account  = $_POST['account'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); #密碼雜湊
    $data = "$account|$password\n";
    //將資料存入文字檔
    $file = fopen($file_path, 'a+');
    fwrite($file, $data);
    fclose($file);
    echo "註冊成功！";
    // 記錄已登入的使用者
    session_start();
    $_SESSION['account'] = $account;
    header("Location:../");
}
?>