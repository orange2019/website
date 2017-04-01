<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/1
 * Time: 11:24
 */
namespace app\validate;

use think\Validate;
class P2pLoan extends Validate {

    protected $rule = [
        'name' => 'require',
        'phone' => 'require',
        'sex'=>'require',
        'city' => 'require',
        'occupation' => 'require',
//        'assets' => 'require'
    ];

    protected $message = [

        'name.require' => '请填写您的姓名',
        'phone.require' => '请输入手机号码',
        'sex.require' => '请选择您的性别',
        'city.require' => '请输入您生活工作的城市',
        'occupation.require' => '请选择您的职业',
//        'assets.require' => '请选择您的资产情况'

    ];
}