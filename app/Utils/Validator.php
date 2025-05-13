<?php

namespace App\Utils;

class Validator
{
    // 必填欄位
    public static function required($value)
    {
        return isset($value) && trim($value) !== '';
    }
    // 只允許中文
    public static function isChinese($value)
    {
        return preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', $value);
    }
    // 只允許英文數字_
    public static function name($value)
    {
        return preg_match('/^[\w]+$/', $value);
    }
    //最小值
    public static function min($value, $min)
    {
        return strlen($value) >= $min;
    }
    // 最大值
    public static function max($value, $max)
    {
        return strlen($value) <= $max;
    }
    // 驗證是否為email格式
    public static function email($value)
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }
    public static function phonenumber($value)
    {
        return preg_match('/^09\d{8}$/', $value);
    }
    public static function date($value)
    {
        return preg_match('/^\d{4}-\d{2}-\d{2}$/', $value);
    }
    /**
     * 通用驗證方法，支援字串規則與 callable
     *
     * @param array $data  欄位資料
     * @param array $rules 驗證規則，如 [
     *                       'email'    => ['required', 'email'],
     *                       'password' => [['App\\Validators\\PasswordValidator', 'validate']]
     *                   ]
     * @return array 驗證錯誤訊息陣列
     */
    public static function validate(array $data, array $rules): array
    {
        $errors = [];

        foreach ($rules as $field => $ruleset) {
            $value = $data[$field] ?? '';

            // 檢查是否有 required 規則
            $isRequired = in_array('required', $ruleset);

            // 若非必填且值為空，直接略過該欄位所有驗證
            if (!$isRequired && trim($value) === '') {
                continue;
            }

            foreach ($ruleset as $rule) {
                // 若規則為陣列 callable 或匿名函式
                if (is_array($rule) || $rule instanceof \Closure) {
                    $result = call_user_func($rule, $value);
                    // 回傳陣列代表多個錯誤訊息
                    if (is_array($result) && !empty($result)) {
                        $errors[$field] = $result;
                        break;
                    }
                    // 回傳 false 代表一般錯誤
                    if ($result === false) {
                        $errors[$field] = [self::errorMessage($field, 'custom')];
                        break;
                    }
                    // 回傳 true 表示通過，自動跳到下一條規則
                    continue;
                }

                // 處理字串規則，如 min:8
                if (strpos($rule, ':') !== false) {
                    [$rulename, $param] = explode(':', $rule, 2);
                } else {
                    $rulename = $rule;
                    $param = null;
                }

                // 呼叫對應靜態方法驗證
                $isValid = match ($rulename) {
                    'required'    => self::required($value),
                    'email'       => self::email($value),
                    'min'         => self::min($value, $param),
                    'max'         => self::max($value, $param),
                    'name'        => self::name($value),
                    'chinese'     => self::isChinese($value),
                    'phonenumber' => self::phonenumber($value),
                    'date'        => self::date($value),
                    default       => true,
                };

                if (!$isValid) {
                    $errors[$field] = self::errorMessage($field, $rulename, $param);
                    break;
                }
            }
        }

        return $errors;
    }

    // 驗證規則的錯誤訊息處理
    public static function errorMessage($field, $rule, $param = null)
    {
        return match ($rule) {
            'required' => "{$field} 為必填欄位",
            'email' => "Email格式不符",
            'min' => "{$field} 至少需要{$param}個字元",
            'max' => "{$field} 不得超過{$param}個字元",
            'name' => "只允許英文數字底線",
            'chinese' => "只允許輸入中文",
            'phonenumber' => "手機號碼格式不符",
            'date' => "不符合日期格式",
            'validate' => "{$field} 驗證未通過",  // 自訂類別方法名稱預設
            'custom' => "{$field} 格式不正確",
            default => "{$field} 不合法"
        };
    }
}
