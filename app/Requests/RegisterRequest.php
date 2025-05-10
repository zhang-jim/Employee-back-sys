<?php

namespace App\Requests;

use App\Utils\PasswordValidator;
use App\Utils\Validator;

class RegisterRequest
{
    public static function validate($data)
    {
        $rules = [
            'email' => ['required','email'],
            'password' => ['required', [PasswordValidator::class, 'validate']],
            'phonenumber' => ['required','phonenumber'],
            'name' => ['required','chinese'],
            'nickname' => ['required','name','min:2','max:20'],   
            'birthday' => ['required','date'],
            'onBoardDate' => ['required','date'],
            'department' => ['required','chinese']
        ];
        return Validator::validate($data,$rules);
    }
}
