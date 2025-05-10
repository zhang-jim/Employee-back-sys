<?php

namespace App\Requests;

use App\Utils\PasswordValidator;
use App\Utils\Validator;

class ResetPasswordRequest
{
    public static function validate($data)
    {
        $rules = [
            'password' => ['required', [PasswordValidator::class, 'validate']],
            'new-password' => ['required', [PasswordValidator::class, 'validate']],
        ];
        return Validator::validate($data,$rules);
    }
}
