<?php

namespace App\Services;

use App\Models\User;
use Exception;

class UserService
{
    private $userModel;
    public function __construct($pdo)
    {
        $this->userModel = new User($pdo);
    }
    public function register($input)
    {
        $account = $input['account'];
        $password = $input['password'];
        // 查詢帳號是否存在，判斷是否註冊
        $user = $this->userModel->getByAccount($account);
        if ($user) {
            throw new Exception("帳號已被註冊");
        };
        // 密碼雜湊
        $hashPassword = password_hash($password, PASSWORD_DEFAULT);
        // 新增帳號
        $this->userModel->create($account, $hashPassword);
        // 自動登入帳號
        $user = $this->userModel->getByAccount($account);
        return $user;
    }

    public function login($account, $password)
    {
        $user = $this->userModel->getByAccount($account);
        if (!$user) {
            throw new Exception("帳號不存在");
        }

        if (!password_verify($password, $user['password'])) {
            throw new Exception("帳號或密碼錯誤！");
        }
        return $user;
    }
    // 取得單一用戶所有資訊
    public function getinfo(){
        $user = $this->userModel->getUser($_SESSION['user_id']);
        return $user;
    }
}
