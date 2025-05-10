<?php

namespace App\Requests;

use App\Utils\PasswordValidator;
use App\Utils\Validator;

class LoginRequest
{
    public static function validate($data)
    {
        $rules = [
            'account' => ['required'],
            'password' => ['required', [PasswordValidator::class, 'validate']]
        ];
        return Validator::validate($data,$rules);
    }
}
