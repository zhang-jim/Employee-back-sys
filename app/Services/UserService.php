<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    private $userModel;
    private $departmentService;
    private $mailService;
    public function __construct($pdo)
    {
        $this->userModel = new User($pdo);
        $this->mailService = new MailService();
        $this->departmentService = new DepartmentService($pdo);
    }
    public function register($data)
    {
        $creates = [];
        $fieldsMap = [
            'email' => 'email',
            'name' => 'name',
            'nickname' => 'nickname',
            'sex' => 'sex',
            'birthday' => 'birthday',
            'phonenumber' => 'phone_number',
            'onBoardDate' => 'on_board_date',
        ];
        foreach ($data as $key => $value) {
            if (isset($fieldsMap[$key]) && trim($value) !== '') {
                $creates[$fieldsMap[$key]] = $value;
            }
        }
        // 使用DepartmentService判斷部門是否存在
        $departmentId = $this->departmentService->getDepartmentId($data['department']);
        if (!$departmentId) {
            return ['success' => false, 'message' => '部門不存在'];
        }
        // 判斷Email、手機號碼、姓名是否被註冊，若存在則返回查詢到的使用者
        $fields = [
            'email' => $data['email'],
            'phone_number' => $data['phonenumber'],
            'name' => $data['name']
        ];
        $check = $this->checkDuplicateFields($fields);
        if (!$check['success']) {
            return $check;
        }
        // 密碼雜湊、產生驗證token
        $hashPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        $token = bin2hex(random_bytes(16));

        $creates['department_id'] = $departmentId;
        $creates['password'] = $hashPassword;
        $creates['token'] = $token;

        // 新增帳號，處理預期外的資料庫錯誤。
        try {
            $this->userModel->create($creates);
            $link = $this->buildVerificationLink($token);
            $send = $this->mailService->sendVerificationEmail($creates['email'], $creates['name'], $link);
            if ($send) {
                return ['success' => true, 'message' => '註冊成功！請前往信箱驗證啟動帳號'];
            } else {
                return ['success' => false, 'message' => '發信功能異常，請洽網站管理員'];
            }
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function login($account, $password)
    {
        $user = $this->userModel->getByAccount($account);
        if (!$user) {
            return ['success' => false, 'message' => '帳號不存在'];
        }
        if (!$user['is_verified']) {
            return ['success' => false, 'message' => '帳號未進行信箱驗證，無法登入'];
        }

        if (!password_verify($password, $user['password'])) {
            return ['success' => false, 'message' => '帳號或密碼錯誤！'];
        }
        return ['success' => true, 'user' => $user];
    }
    // 取得單一用戶所有資訊
    public function getinfo()
    {
        return $this->userModel->getUser($_SESSION['user_id']);
    }
    // 編輯單一用戶資訊
    public function updateinfo($data)
    {
        $updates = [];

        //欄位對應表 對應前端參數轉換為後端資料庫設定的欄位，也等於白名單功能，只針對特定欄位設定對應參數
        $fieldsMap = [
            'email' => 'email',
            'name' => 'name',
            'nickname' => 'nickname',
            'sex' => 'sex',
            'phonenumber' => 'phone_number',
            'birthday' => 'birthday',
        ];
        foreach ($data as $key => $value) {
            if (isset($fieldsMap[$key]) && trim($value) !== '') {
                $updates[$fieldsMap[$key]] = $value;
            }
        }
        if (empty($updates)) {
            return ['success' => false, 'message' => '更新資料不得為空'];
        }

        $result = $this->userModel->update($_SESSION['user_id'], $updates);
        if ($result['affected_rows'] > 0) {
            return ['success' => true, 'message' => '資料更新成功'];
        } else {
            return ['success' => false, 'message' => '資料未更動'];
        }
    }
    // 重設密碼 => 需要先輸入原始密碼，以及要設置的新密碼 => 重設成功
    public function resetPassword($old_password, $new_password)
    {
        if ($old_password === $new_password) {
            return ['success' => false, 'message' => '新密碼與舊密碼相同，重設失敗'];
        }
        //取得使用者資料
        $user = $this->userModel->privateGetUser($_SESSION['user_id']);
        // 判斷原始密碼是否正確
        if (!password_verify($old_password, $user['password'])) {
            return ['success' => false, 'message' => '密碼錯誤，重設失敗'];
        }
        // 密碼雜湊
        $hashPassword = password_hash($new_password, PASSWORD_DEFAULT);
        try {
            $this->userModel->updatePassword($_SESSION['user_id'], $hashPassword);
        } catch (\Throwable) {
            return ['success' => false, 'message' => '伺服器繁忙中，請稍後在試'];
        }
        return ['success' => true, 'message' => '密碼重設成功，請重新登入！'];
    }
    // 檢查Email,Name,Phonenumber 是否存在
    private function checkDuplicateFields($fields)
    {
        $existUser = $this->userModel->findByFields($fields);
        if (!$existUser) {
            return ['success' => true];
        }
        $messages = [
            'email' => 'Email已被註冊',
            'phone_number' => '手機號碼已被註冊',
            'name' => '帳號已存在',
        ];

        $errors = [];
        foreach ($messages as $key => $message) {
            if (isset($existUser[$key]) && $existUser[$key] === $fields[$key]) {
                $errors[] = $message;
            }
        }

        if (!empty($errors)) {
            return ['success' => false, 'message' => implode('，', $errors)];
        }
        return ['success' => true];
    }
    private function buildVerificationLink($token)
    {
        $baseUrl = rtrim($_ENV['APP_URL'], '/');
        $encodedToken = urlencode($token);
        return "{$baseUrl}/auth/verify?token={$encodedToken}";
    }
    // 驗證Token
    public function verifytoken($token)
    {
        $user = $this->userModel->getByToken($token);
        if (!$user) {
            return ['success' => false, 'message' => '無效的驗證連結'];
        }
        if ($user['is_verified']) {
            return ['success' => false, 'message' => '帳號已完成驗證'];
        }

        $result = $this->userModel->updateEmailVerify($user['id']);
        if ($result) {
            return ['success' => true, 'message' => '信箱驗證成功，您現在可以登入'];
        } else {
            return ['success' => false, 'message' => '資料庫發生錯誤，請聯繫網站管理員'];
        }
    }
}
