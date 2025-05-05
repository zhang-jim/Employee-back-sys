<?php

namespace App\Middlewares;

class AuthMiddleware
{
    public static function handle()
    {
        if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
            header("Content-Type:application/json");
            echo json_encode(['success'=>false,'message'=>'請登入帳號']);
            exit;
        }
    }
}
