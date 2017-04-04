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
use LC\Sms;
use think\Request;

class Auth extends Home
{

    public function login(){

        $request = Request::instance();
        if ($request->isPost()){

            $data = $request->post();
            // 验证
            $validate = validate('LoginMember');
            if (!$validate->check($data)){
                return $this->formError($validate->getError());
            }

            // 登录
            $Member = new Member();
            $member = $Member->login($data);
            if ($member){
                $this->afterLogin($member);
                $url = is_mobile() ? url('member/wechat/index') : url('member/index/index');
                return $this->formSuccess('登录成功' , $url);
            }else{
                return $this->formError('登录失败，用户名或密码错误');
            }

        }else {

            $form = FormBuilder::init()
                ->setAction($request->url())
                ->addClass('form-ajax')
                ->addText('phone' , '手机号' , '' ,'请输入你的手机号码' , true )
                ->addPassword('password' , '密码' , '' , '请输入你的密码' , true)
                ->addText('captcha' , '验证码' , '' , '请输入验证码' , true)
                ->addSubmit('登录')
                ->build();

            $this->assign('form' , $form);
            return $this->fetch();
        }
    }

    protected function afterLogin($member){
        session('www_uid' , $member->id);
    }

    public function reg(){

        $request = Request::instance();
        if ($request->isPost()){
            $data = $request->post();

            // 验证
            $validate = validate('RegMember');
            if (!$validate->check($data)){
                return $this->formError($validate->getError());
            }

            // 验证两次密码是否输入一样
            if ($data['password'] != $data['password_again']){
                return $this->formError('两次输入密码不一致，请重新输入');
            }

            // 验证短信码
//            $check = Sms::checkCode('sms_check_code', $data['phone'] , $data['code']);
//            if (!$check){
//                return $this->formError('验证码错误或已过期');
//            }

            $Member = new Member();
            $member = $Member->reg($data);
            if ($member){
                $this->afterReg($member);
                $url = is_mobile() ? url('member/wechat/index') : url('member/index/index');
                return $this->formSuccess('注册成功' , $url);
            }else{
                return $this->formError('注册失败,' . $Member->getError());
            }
        }else {
            $form = FormBuilder::init()
                ->setFormName('pc-reg')
                ->setAction($request->url())
                ->addClass('form-ajax')
                ->addText('phone' , '手机号' , '' ,'请输入你的手机号码' , true )
                ->addPassword('password' , '密码' , '' , '请输入你的密码' , true)
                ->addPassword('password_again' , '再次输入密码' , '' , '请确认输入你的密码' , true)
                ->addText('code' , '验证码' , '' , '请输入接受的手机验证码' , true)
                ->addSubmit('点击进行注册！')
                ->build();

            $this->assign('form' , $form);
            return $this->fetch();

        }
    }

    public function afterReg($member){
        session('www_uid' , $member->id);
    }

    public function logout(){
        session('www_uid' , null);
        return $this->formSuccess('退出成功' , url('auth/login'));
    }

    public function info(){

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
            // 检测手机验证码
            $check = Sms::checkCode('sms_check_code', $data['phone'] , $data['code']);
            if (!$check){
                return $this->formError('验证码错误或已过期');
            }

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

    public function sentSms(){
        $request = Request::instance();
        if ($request->isPost()){
            $phone = $request->post('phone');
            // 正则验证手机号码
            $pattern = '/^(1(([35][0-9])|(47)|[8][012356789]))\d{8}$/';
            $check = preg_match($pattern, $phone);
            if (!$check){
                return $this->formError('无效手机号码');
            }

            $number = mt_rand(100000 , 999999);
            $number = strval($number);
            $params['code'] = $number;
            $config = Sms::getConfig();
            $res = Sms::sentByTemplateName('sms_check_code' , $phone , $params , $config);
            if ($res){
                return $this->formSuccess();
            }else {
                return $this->formError('发送失败');
            }
        }
    }

}
