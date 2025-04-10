<?php
session_start();
if (isset($_SESSION['account']) && !empty($_POST['message'])) {
    require_once 'database/db.php';
    $message = $_POST['message'];
    #取得user_id;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE account = ?");
    $stmt->execute([$_SESSION['account']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    #新增留言
    $stmt = $pdo->prepare("INSERT INTO messages (user_id,content) VALUE (?,?)");
    $stmt->execute([$user['id'],$message]);
    header("Location:index.php");
}
echo "未登入，無法留言 >><a href='Auth/login.php'>登入</a>";
