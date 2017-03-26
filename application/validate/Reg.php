<?php
namespace app\validate;

use think\Validate;
class Reg extends Validate {
    
    protected $rule = [
//        'email' => 'email',
        'phone' => 'require',
        'password' => 'require|min:6',
        'code' => 'require',
//        'captcha'=>'require|captcha'
    ];
    
    protected $message = [
        
//        'email.email' => '邮箱格式错误',
//         'email.token' => '请不要重复提交',
        'phone.require' => '请输入电话号码',
        'code.require' => '请输入短信验证码',
        'password.require' => '请输入密码',
        'password.min' => '密码长度不够',

//        'captcha.require' => '请输入验证码',
//        'captcha.captcha' => '验证码不正确'
        
    ];
}