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
    require_once '../database/db.php';
    $account  = $_POST['account'];
    // 檢查帳號是否存在
    $stmt = $pdo->prepare("SELECT * FROM users WHERE account = ?");
    $stmt->execute([$account]); 
    if ($stmt->rowCount() > 0) {
        echo "帳號已存在，無法註冊";
        exit();
    }
    #建立使用者帳密
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); #密碼雜湊
    $stmt = $pdo->prepare("INSERT INTO users (account,password) VALUE (?,?)");
    $stmt->execute([$account, $password]);
    // 記錄已登入的使用者
    session_start();
    $_SESSION['account'] = $account;
    header("Location:../");
}
?>