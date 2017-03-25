<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/20
 * Time: 12:09
 */

namespace app\controller;


use app\model\Member;
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
        $request = Request::instance();
        if ($request->isPost()){

            $data = $request->post();
            if ($data['phone'] == ''){
                return $this->formError('请输入手机号码');
            }
            if ($data['code'] == ''){
                return $this->formError('请输入验证码');
            }
            // 检测手机验证码 TODO

            $Member = new Member();
            $res = $Member->regWechatBind($data['phone']);
            if ($res) {
                session('www_uid' , $res);
                return $this->formSuccess('绑定成功' , url('member/wechat/index'));
            }else {
                $error = $Member->getError();
                return $this->formError($error . '，请稍后重试！');
            }
        }else {

            $form = FormBuilder::init()
                ->setFormName('wx-bind')
                ->addClass('form-ajax')
                ->setAction($request->url())
                ->addText('phone' , '' , '' ,'请输入你的手机号码' , true )
                ->addText('code' , '' , '' , '请输入接收的验证码' , true)
                ->addSubmit('绑定')
                ->build();

            $this->assign('form' , $form);
            return $this->fetch('auth/wxBind');
        }
    }

    public function wxUnbind(){

        $uid = session('www_uid');

        $Member = new Member();
        $res = $Member->unbindWechat($uid);
        if ($res){
            session('www_uid' , null);
            return $this->formSuccess('解绑成功' , url('auth/wxBind'));
        }else {
            return $this->formError('解绑失败' . $Member->getError());
        }
    }

    public function wxInfo(){

    }

}
