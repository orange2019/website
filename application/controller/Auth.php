<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/20
 * Time: 12:09
 */

namespace app\controller;


use LC\FormBuilder;
use think\Request;

class Auth extends Home
{

    public function login(){

        $request = Request::instance();
        if ($request->isPost()){

        }else {

            $form = FormBuilder::init()
                ->addText('phone' , '手机号' , '' ,'请输入你的手机号码' , true )
                ->addPassword('password' , '密码' , '' , '请输入你的密码' , true)
                ->addText('captcha' , '验证码' , '' , '请输入验证码' , true)
                ->addSubmit('登录')
                ->build();

            $this->assign('form' , $form);
            return $this->fetch();
        }
    }

    public function logout(){

    }

    public function info(){

    }

    public function reg(){

    }

    public function wxBind(){

    }

    public function wxUnBind(){

    }

    public function wxInfo(){

    }

}
