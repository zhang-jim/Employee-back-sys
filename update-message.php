<?php
session_start();
include_once 'database/db.php';
if (!empty($_POST)) {
    $messageID = $_POST["messageID"];
    $stmt = $pdo->prepare("SELECT * FROM messages WHERE id = ?");
    $stmt->execute([$messageID]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($_SESSION['user_id'] === $data['user_id']) { #比對當前使用者與留言者身分是否相符
        if ($_POST['new-message'] === $data['content']) { #判斷資料是否有更動
            echo "資料無異動";
            exit();
        } else {
            $stmt = $pdo->prepare("UPDATE messages SET content = ? WHERE id = ?");
            $stmt->execute([$_POST['new-message'], $messageID]);
            header("Location:index.php");
        }
    } else {
        echo "權限不夠，無法編輯";
        exit();
    };
}
