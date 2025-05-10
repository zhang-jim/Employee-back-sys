<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    private $userModel;
    private $departmentService;
    public function __construct($pdo)
    {
        $this->userModel = new User($pdo);
        $this->departmentService = new DepartmentService($pdo);
    }
    public function register($data)
    {
        $email = $data['email'];
        $password = $data['password'];
        $name = $data['name'];
        $nickname = $data['nickname'];
        $sex = $data['sex'];
        $birthday = $data['birthday'];
        $phonenumber = $data['phonenumber'];
        $onBoardDate = $data['onBoardDate'];
        $department = $data['department'];
        // 使用DepartmentService判斷部門是否存在
        $departmentId = $this->departmentService->getDepartmentId($department);
        if (!$departmentId) {
            return ['success' => false, 'message' => '部門不存在'];
        }
        $fields = [
            'email' => $email,
            'phone_number' => $phonenumber,
            'name' => $name
        ];
        // 判斷Email、手機號碼、姓名是否被註冊，若存在則返回查詢到的使用者
        $existUser = $this->userModel->findByFields($fields);
        $error = [];
        // 返回的使用者資料與註冊使用者資料比對
        if ($existUser) {
            foreach ($fields as $key => $value) {
                if (isset($existUser[$key]) && $existUser[$key] === $value) {
                    $error[] = match ($key) {
                        "email" => "Email已被註冊",
                        "phone_number" => "手機號碼已被註冊",
                        "name" => "帳號已存在",
                    };
                }
            }
            return ['success' => false, 'message' => implode("。", $error)];
        }
        // 密碼雜湊
        $hashPassword = password_hash($password, PASSWORD_DEFAULT);
        // 新增帳號，處理預期外的資料庫錯誤。
        try {
            $this->userModel->create($email, $hashPassword, $name, $nickname, $sex, $birthday, $phonenumber, $onBoardDate, $departmentId);
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
        // 自動登入帳號 
        $user = $this->userModel->getByAccount($email);
        return ['success' => true, 'user' => $user];
    }

    public function login($account, $password)
    {
        $user = $this->userModel->getByAccount($account);
        if (!$user) {
            return ['success' => false, 'message' => '帳號不存在'];
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
        if($result['affected_rows'] > 0){
            return ['success' => true, 'message' => '資料更新成功'];
        }else{
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
}
