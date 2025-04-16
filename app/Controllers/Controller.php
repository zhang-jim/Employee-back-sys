<?php
namespace App\Controllers;
class Controller
{
    protected function isLoggedIn(): bool
    {
        session_start();
        return isset($_SESSION['user_id']);
    }
    protected function jsonResponse($success, $message)
    {
        header('Content-Type:application/json');
        echo json_encode(['success' => $success, 'message' => $message]);
        exit();
    }
    protected function requireLogin() //需要登入，才能使用功能
    {
        // 有登入，不執行以下程式
        if (!$this->isLoggedIn()) {
            $this->jsonResponse(false, '請登入帳戶');
        }
    }
}
