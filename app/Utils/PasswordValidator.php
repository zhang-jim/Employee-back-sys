<?php

namespace App\Utils;

class PasswordValidator
{
    public static function validate($password)
    {
        $errors = [];
        if (strlen($password) < 8 || strlen($password) > 20) {
            $errors[] = "密碼長度必須在8~20個字元之間";
        }
        if (!preg_match('/^[^\x{4e00}-\x{9fff}]+$/u', $password)) {
            $errors[] = "密碼不得包含中文";
        }
        if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[!@#$%^&*.])[\w!@#$%^&*.]+$/', $password)) {
            $errors[] = "密碼需包含大小寫字母、數字、特殊符號";
        }
        return $errors;
    }
}
