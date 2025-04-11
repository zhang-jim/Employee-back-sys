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
    include_once "../database/db.php";
    $account = $_POST['account'];
    $password = $_POST['password'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE account = ?");
    $stmt->execute([$account]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['account'] = $_POST['account'];
        header("Location:../");
        exit();
    }
    echo "帳號或密碼錯誤！";
}
?>