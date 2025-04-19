<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Services\UserService;

class AuthController extends Controller
{
    private $userService;
    public function __construct($pdo)
    {
        $this->userService = new UserService($pdo);
    }
    // 登入 Page
    public function index()
    {
        view('user/login');
    }
    // 註冊 Page
    public function create()
    {
        view('user/register');
    }
    // 註冊
    public function store()
    {
        if ($this->isLoggedIn()) {
            $this->jsonResponse(true, '已登入');
        }
        // 取得前端JSON資料
        $input  = json_decode(file_get_contents('php://input'), true);
        $account  = $input['account'] ?? null;
        $password = $input['password'] ?? null;
        // 資料驗證
        if (!$account || !$password) {
            $this->jsonResponse(false, '資料輸入不完整');
        }
        // 使用UserServices 註冊邏輯
        try {
            $user = $this->userService->register($input);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['account'] = $user['account'];
            $this->jsonResponse(true,"註冊成功！");
        } catch (\Throwable $e) {
            $this->jsonResponse(false,$e->getMessage());
        }
    }
    // 登入
    public function login()
    {
        // 防呆 判斷是否登入=>當使用者有登入時，不會執行下面程式
        if ($this->isLoggedIn()) {
            $this->jsonResponse(true, '已登入');
        }
        // 取得前端JSON資料
        $input  = json_decode(file_get_contents('php://input'), true);
        $account = $input['account'] ?? null;
        $password = $input['password'] ?? null;
        // 資料驗證
        if (!$account || !$password) {
            $this->jsonResponse(false, '資料輸入不完整');
        }
        // 使用UserServices 判斷登入是否成功，並拋出例外 
        try {
            $user = $this->userService->login($account, $password);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['account'] = $account;
            $this->jsonResponse(true, '登入成功！');
        } catch (\Throwable $e) {
            $this->jsonResponse(false, $e->getMessage());
        }
    }
    // 登出
    public function logout()
    {
        // 判斷登入
        $this->requireLogin();
        session_destroy();
        $this->jsonResponse(true, '登出成功！');
    }
    // 取得單一用戶資訊
    public function show()
    {
        // 判斷登入
        $this->requireLogin();
        $user = $this->userService->getinfo();
        $this->jsonResponse(true, $user);
    }
}
