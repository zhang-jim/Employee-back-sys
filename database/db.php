<?php
$dsn = 'mysql:host=db;dbname=employee_back_sys;charset=utf8mb4';
$username = 'employee_back_sys';
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
