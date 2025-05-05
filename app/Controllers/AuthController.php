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

        $email  = $input['email'] ?? null;
        $password = $input['password'] ?? null;
        $name = $input['name'] ?? null;
        $nickname = $input['nickname'] ?? null;
        $sex = $input['sex'] ?? null;  // 0 or 1
        $birthday = $input['birthday'] ?? null;
        $phonenumber = $input['phonenumber'] ?? null;
        $onBoardDate = $input['onBoardDate'] ?? null;
        $department = $input['department'] ?? null;

        $data = [
            'email' => $email,
            'password' => $password,
            'name' => $name,
            'nickname' => $nickname,
            'sex' => $sex,
            'birthday' => $birthday,
            'phonenumber' => $phonenumber,
            'onBoardDate' => $onBoardDate,
            'department' => $department
        ];
        // 使用UserServices 註冊邏輯
        $user = $this->userService->register($data);
        if ($user['success']) {
            $_SESSION['user'] = $user['user']['name'];
            $_SESSION['user_id'] = $user['user']['id'];
            $_SESSION['role_id'] = $user['user']['role_id'];
            $_SESSION['logged_in'] = true;
            $this->jsonResponse(true, "註冊成功！");
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
        $account = $input['account'] ?? null;
        $password = $input['password'] ?? null;
        // 資料驗證
        if (!$account || !$password) {
            $this->jsonResponse(false, '資料輸入不完整');
        }
        // 使用UserServices 判斷登入是否成功，並拋出例外 
        $user = $this->userService->login($account, $password);
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
        $new_nickname = $input['nickname'] ?? null;
        $new_phone_number = $input['phone-number'] ?? null;

        $result = $this->userService->updateinfo($new_nickname, $new_phone_number);

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
        $old_password = $input['password'] ?? null;
        $new_password = $input['new-password'] ?? null;
        // 驗證資料是否為空
        if (empty($old_password) || empty($new_password)) {
            $this->jsonResponse(false, "失敗，密碼不得為空");
        }
        // 驗證密碼：包含至少1個大寫,1個小寫,1個數字,1個符號,長度8~20字元,不得輸入中文。
        if (strlen($new_password) < 8 || strlen($new_password) > 20) {
            $this->jsonResponse(false, "密碼長度必須在8~20個字元之間");
        }
        if(!preg_match('/^[^\x{4e00}-\x{9fff}]+$/u',$new_password)){
            $this->jsonResponse(false, '密碼不得包含中文');
        }
        if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[!@#$%^&*])[\w!@#$%^&*]+$/', $new_password)) {
            $this->jsonResponse(false, '密碼需包含大小寫字母、數字、特殊符號');
        }

        $results = $this->userService->resetPassword($old_password, $new_password);
        if ($results) {
            session_destroy();
            $this->jsonResponse(true, $results['message']);
        } else {
            $this->jsonResponse(false, $results['message']);
        }
    }
}
