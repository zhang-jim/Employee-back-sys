<?php
namespace App\Controllers;
class Controller
{
    protected function isLoggedIn(): bool
    {
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
    // 需要是網站管理員，才可進行後續動作
    protected function requireAdmin(){
        if($_SESSION['role_id'] !== 1){
           $this->jsonResponse(false,"權限不足"); 
        }
    }
}
