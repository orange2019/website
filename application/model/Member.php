<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/20
 * Time: 14:58
 */

namespace app\model;


use think\Model;
use think\Request;

class Member extends Model
{
    protected $autoWriteTimestamp = true;

    public function reg(){

    }

    public function login(){

    }

    public function regWechatBind($phone){

        $wechatUserInfo = session('wechat_user_info');

        // 检测该微信号是否注册过
        $openid = $wechatUserInfo['openid'];
        $member = $this->where('openid' , $openid)->find();
        if ($member){
            $this->error = '该微信号已经绑定';
            return false;
        }

        // 检测该电话号码是否注册过
        $member = $this->where('phone' , $phone)->find();
        if ($member){
            $member->openid = $wechatUserInfo['openid'];
            $member->unionid = isset($wechatUserInfo['unionid']) ? $wechatUserInfo['unionid'] : '';
            $member->wechat = json_encode($wechatUserInfo);
            $res = $member->save();
            if ($res){
                $this->updateTokenInfo($member->id);
                return $member->id;
            }else {
                $this->error = '绑定失败,更新数据出现异常';
                return false;
            }

        }else {

            $data['phone'] = $phone;
            $data['openid'] = $wechatUserInfo['openid'];
            $data['unionid'] = isset($wechatUserInfo['unionid']) ? $wechatUserInfo['unionid'] : '';
            $data['wechat'] = json_encode($wechatUserInfo);
            $data['avatar'] = $wechatUserInfo['headimgurl'];
            $data['name'] = $wechatUserInfo['nickname'];
            $data['sex'] = $wechatUserInfo['sex'];

            $member = $this->create($data);
            if ($member->id){
                $this->updateTokenInfo($member->id);
                return $member->id;
            }else {
                $this->error = '绑定失败,新增数据出现异常';
                return false;
            }

        }


    }

    public function unbindWechat($uid){

        $member = $this->find($uid);
        if ($member){
            $member->openid = '';
            $member->unionid = '';
            $member->wechat = '';
            $res = $member->save();
            if ($res){
                return true;
            }else {
                $this->error = '更新错误数据';
                return false;
            }
        }else {
            $this->error = '错误数据';
            return false;
        }
    }
    /**
     * 更新授权信息
     * @param $memberId
     * @param string $authType
     * @return mixed
     */
    protected function updateTokenInfo($memberId , $authType = 'wx'){

        $authIp = Request::instance()->ip();

        $memberAuth = MemberAuth::get(['member_id'=>$memberId]);
        $token = md5($memberId.time());
        if ($memberAuth){
            $memberAuth->type = $authType;
            $memberAuth->login_ip = $authIp;
            $memberAuth->login_time = time();
            $memberAuth->token = $token;
            $memberAuth->save();
        }else{
            $auth['member_id'] = $memberId;
            $auth['type'] = $authType;
            $auth['login_ip'] = $authIp;
            $auth['login_time'] = time();
            $auth['token'] = $token;
            $memberAuth = MemberAuth::create($auth);
        }

        $id = $memberAuth->id;
        session('www_auth_id' , $id);

        return $id;
    }
}
