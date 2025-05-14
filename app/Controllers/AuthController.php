<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Requests\LoginRequest;
use App\Requests\RegisterRequest;
use App\Requests\ResetPasswordRequest;
use App\Requests\UpdateUserInfoRequest;
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
    // 信箱驗證
    public function verify()
    {
        $token = $_GET['token'] ?? null;
        if (empty($token)) {
            $this->jsonResponse(false, "Token不得為空");
        }
        $result = $this->userService->verifytoken($token);
        if ($result['success']) {
            $this->jsonResponse(true, $result['message']);
        } else {
            $this->jsonResponse(false, $result['message']);
        }
    }
    // 註冊
    public function store()
    {
        if ($this->isLoggedIn()) {
            $this->jsonResponse(true, '已登入');
        }
        // 取得前端JSON資料
        $input  = json_decode(file_get_contents('php://input'), true);

        $errors = RegisterRequest::validate($input);

        if (!empty($errors)) {
            $this->jsonResponse(false, $errors);
        }
        // 使用UserServices 註冊邏輯
        $user = $this->userService->register($input);
        if ($user['success']) {
            $this->jsonResponse(true, $user['message']);
        } else {
            $this->jsonResponse(false, $user['message']);
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
        $errors = LoginRequest::validate($input);
        if (!empty($errors)) {
            $this->jsonResponse(false, $errors);
        }
        // 使用UserServices 判斷登入是否成功，並拋出例外 
        $user = $this->userService->login($input['account'], $input['password']);
        if ($user['success']) {
            $_SESSION['user'] = $user['user']['name'];
            $_SESSION['user_id'] = $user['user']['id'];
            $_SESSION['role_id'] = $user['user']['role_id'];
            $_SESSION['logged_in'] = true;
            $this->jsonResponse(true, '登入成功！');
        } else {
            $this->jsonResponse(false, $user['message']);
        }
    }
    // 登出
    public function logout()
    {
        session_destroy();
        $this->jsonResponse(true, '登出成功！');
    }
    // 檢視個人資料 Page
    public function showInfo()
    {
        return view('/user/info');
    }
    // 檢視個人資料
    public function show()
    {
        $user = $this->userService->getinfo();
        $this->jsonResponse(true, ['user' => $user]);
    }
    // 編輯個人資料
    public function update()
    {
        $input = json_decode(file_get_contents('php://input'), true);

        $errors = UpdateUserInfoRequest::validate($input);
        if (!empty($errors)) {
            $this->jsonResponse(false, $errors);
        }

        $result = $this->userService->updateinfo($input);

        if ($result['success']) {
            $this->jsonResponse(true, $result['message']);
        } else {
            $this->jsonResponse(false, $result['message']);
        }
    }
    // 重設密碼
    public function resetPassword()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $errors = ResetPasswordRequest::validate($input);
        if (!empty($errors)) {
            $this->jsonResponse(false, $errors);
        }

        $results = $this->userService->resetPassword($input['password'], $input['new-password']);
        if ($results['success']) {
            session_destroy();
            $this->jsonResponse(true, $results['message']);
        } else {
            $this->jsonResponse(false, $results['message']);
        }
    }
}
