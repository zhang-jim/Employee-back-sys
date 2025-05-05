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
    // 需要是網站管理員，才可進行後續動作
    protected function requireAdmin(){
        if($_SESSION['role_id'] !== 1){
           $this->jsonResponse(false,"權限不足"); 
        }
    }
}
