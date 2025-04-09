<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登入頁面</title>
</head>

<body>
    <h1>登入頁面</h1>
    <form action="" method="post">
        帳號：<input type="text" name="account">
        密碼：<input type="password" name="password">
        <input type="submit" value="登入">
    </form>
    <a href="register.php">註冊</a>
</body>

</html>
<?php
// 判斷使用者是否已登入
session_start();
if (isset($_SESSION['account'])) {
    header("Location:../");
}
// 登入流程判斷
if (!empty($_POST['account']) && !empty($_POST['password'])) {
    $file_array = file('user.txt');
    foreach ($file_array as $value) {
        list($account, $hash) = explode("|", string: trim($value));
        if ($_POST['account'] === $account && password_verify($_POST['password'], $hash)) {
                echo "登入成功";
                $_SESSION['account'] = $_POST['account'];
                header("Location:../");
                exit();
        }
    }
    echo "帳號或密碼錯誤！";
}
?>