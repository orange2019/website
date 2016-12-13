<?php
namespace app\validate;

use think\Validate;
class Login extends Validate {
    
    protected $rule = [
        'email' => 'email',
        'password' => 'require|min:6',
        'captcha'=>'require|captcha'
    ];
    
    protected $message = [
        
        'email.email' => '邮箱格式错误',
//         'email.token' => '请不要重复提交',
        'password.require' => '请输入密码',
        'password.min' => '密码长度不够',
        'captcha.require' => '请输入验证码',
        'captcha.captcha' => '验证码不正确'
        
    ];
}