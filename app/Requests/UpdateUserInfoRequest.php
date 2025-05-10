<?php 
namespace App\Requests;

use App\Utils\Validator;

class UpdateUserInfoRequest{
    public static function validate($data){
        $rules = [
            'email' => ['email'],
            'phonenumber' => ['phonenumber'],
            'name' => ['chinese'],
            'nickname' => ['name','min:2','max:20'],   
            'birthday' => ['date'],
        ];
        return Validator::validate($data,$rules);
    }
}