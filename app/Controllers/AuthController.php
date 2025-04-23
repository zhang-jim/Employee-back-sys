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
            $_SESSION['role_id'] = $user['role_id'];
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
            $_SESSION['role_id'] = $user['role_id'];
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
    // 檢視個人資料
    public function show()
    {
        // 判斷登入
        $this->requireLogin();
        $user = $this->userService->getinfo();
        $this->jsonResponse(true, ['user' => $user]);
    }
    // 編輯個人資料
    public function update(){
        // 判斷登入
        $this->requireLogin();
        $input = json_decode(file_get_contents('php://input'),true);
        $new_nickname = $input['nickname'] ?? null;
        $new_phone_number = $input['phone-number'] ?? null;

        $result = $this->userService->updateinfo($new_nickname,$new_phone_number);
    
        if($result['success']){
            $this->jsonResponse(true,$result['message']);
        }else{
            $this->jsonResponse(false,$result['message']);
        }
        
    }
    // 重設密碼
    public function resetPassword(){
        $this->requireLogin();
        $input = json_decode(file_get_contents('php://input'),true);
        $old_password = $input['password'] ?? null;
        $new_password = $input['new-password'] ?? null;
        // 驗證資料是否為空
        if(empty($old_password) || empty($new_password)){
            $this->jsonResponse(false,"失敗，密碼不得為空");
        }
        // 驗證密碼長度
        if(strlen($new_password) < 8){
            $this->jsonResponse(false,"密碼長度至少需要8個字元");
        }
        // 驗證密碼是否只包含英數
        if (!preg_match('/[A-Z]/', $new_password) || !preg_match('/[a-z]/', $new_password) || !preg_match('/[0-9]/', $new_password)) {
            $this->jsonResponse(false, '密碼需包含大小寫字母與數字');
        }

        $results = $this->userService->resetPassword($old_password,$new_password);
        if($results){
            $this->jsonResponse(true,$results['message']);
        }else{
            $this->jsonResponse(false,$results['message']);
        }
    }
}
