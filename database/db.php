<?php
$dsn = 'mysql:host=localhost;dbname=message_board;charset=utf8mb4';
$username = 'message-board';
$password = 'F_dUnN5NQlsQhk_V';
try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );
} catch (PDOException $th) {
    echo "資料庫連線失敗".$th->getMessage();
    exit();
}
