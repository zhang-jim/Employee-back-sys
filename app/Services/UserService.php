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
    public function getinfo()
    {
        return $this->userModel->getUser($_SESSION['user_id']);
    }
    // 編輯單一用戶資訊
    public function updateinfo($nickname, $phone_number)
    {
        // 取得用戶資料
        $user = $this->getinfo();
        $old_nickname = $user['nickname'];
        $old_phone_number = $user['phone_number'];

        $updates = [];
        // 判斷進來的資料都為空
        if (!empty($nickname) && $nickname !== $old_nickname) {
            $updates['nickname'] = $nickname;
        }
        if (!empty($phone_number) && $phone_number !== $old_phone_number) {
            $updates['phone_number'] = $old_phone_number;
        }
        if (empty($updates)) {
            return ['success' => false, 'message' => '資料無異動'];
        }
        // 進來的資料其中之一為空 => 正常執行
        $this->userModel->update($_SESSION['user_id'], $updates['nickname'] ?? null, $updates['phone_number'] ?? null);
        return ['success' => true, 'message' => '資料更新成功'];
    }
    // 重設密碼 => 需要先輸入原始密碼，以及要設置的新密碼 => 重設成功
    public function resetPassword($old_password, $new_password)
    {
        //取得使用者資料
        $user = $this->getinfo();
        // 判斷原始密碼是否正確
        if (!password_verify($old_password, $user['password'])) {
            return ['success' => false, 'message' => '密碼重設失敗'];
        }
        // 密碼雜湊
        $hashPassword = password_hash($new_password, PASSWORD_DEFAULT);
        try {
            $this->userModel->updatePassword($_SESSION['user_id'], $hashPassword);
        } catch (\Throwable) {
            return ['success' => false, 'message' => '重設失敗，請稍後在試'];
        }
        return ['success' => true, 'message' => '密碼重設成功'];
    }
}
