<?php
namespace App\Controllers;
use App\Models\User;
use App\Controllers\Controller;

class AuthController extends Controller
{
    private $authModel;
    public function __construct($pdo)
    {
        $this->authModel = new User($pdo);
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
        if (!$account || !$password) {
            $this->jsonResponse(false, '資料輸入不完整');
        }
        $user = $this->authModel->getByAccount($account);
        // 判斷帳號是否被註冊
        if ($user) {
            $this->jsonResponse(false, '帳號已註冊');
        };
        // 密碼雜湊
        $hashPassword = password_hash($password, PASSWORD_DEFAULT);
        // 新增帳號
        $this->authModel->create($account, $hashPassword);
        // 自動登入帳號
        $user = $this->authModel->getByAccount($account);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['account'] = $user['account'];
        $this->jsonResponse(true, '註冊成功');
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
        if (!$account || !$password) {
            $this->jsonResponse(false, '資料輸入不完整');
        }
        $user = $this->authModel->getByAccount($account);
        if (!$user) {
            $this->jsonResponse(false, '帳號不存在');
        }
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['account'] = $account;
            $this->jsonResponse(true, '登入成功！');
        } else {
            $this->jsonResponse(false, '帳號或密碼錯誤！');
        }
    }
    // 登出
    public function logout()
    {
        // 判斷是否登入
        $this->requireLogin();
        session_destroy();
        $this->jsonResponse(true, '登出成功！');
    }
    // 取得單一用戶資訊
    public function show()
    {
        $this->requireLogin();
        $user = $this->authModel->getUser($_SESSION['user_id']);
        if (!$user) {
            $this->jsonResponse(false, '帳號不存在');
        }
        $this->jsonResponse(true, $user);
    }
}
