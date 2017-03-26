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

    public function reg($data){
        // 判断是否存在相同手机号注册的
        if (isset($data['phone'])){
            $phone = $data['phone'];
            $member = $this->where('phone' , $phone)->where('status' , '>=' , 0)->find();
            if ($member){
                // 存在 判断是否设置密码
                if ($member->password){
                    $this->error = '您的手机号已经被注册！';
                    return false;
                }else{
                    // 设置密码
                    $member->password = md5($data['password']);
                    $res = $member->save();
                    if ($res){
                        return $member;
                    }else {
                        $this->error = '数据更新失败';
                        return false;
                    }
                }
            }else {
                // 不存在，注册
                $insert['phone'] = $phone;
                $insert['password'] = md5($data['password']);

                $member = $this->create($insert);
                if ($member){
                    $this->updateTokenInfo($member->id , 'pc');
                    return $member;
                }else{
                    $this->error = '数据添加失败';
                    return false;
                }
            }
        }
    }

    public function login($data){
        if (isset($data['phone'])){
            $member = $this->where('phone' , $data['phone'])->where('status' , '>=' , 0)->find();
        }
        elseif (isset($data['email'])){
            $member = $this->where('email' , $data['email'])->where('status' , '>=' , 0)->find();
        }
        elseif (isset($data['name'])){
            $member = $this->where('name' , $data['name'])->where('status' , '>=' , 0)->find();
        }

        if ($member){
            $password = md5($data['password']);
            if ($password == $member->password){
                $this->updateTokenInfo($member->id , 'pc');
                return $member;
            }else {
                return false;
            }
        }else{
            return false;
        }
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
        $member = $this->where('phone' , $phone)->where('status' , '>=' , 0)->find();
        if ($member){
            $member->openid = $wechatUserInfo['openid'];
            $member->unionid = isset($wechatUserInfo['unionid']) ? $wechatUserInfo['unionid'] : '';
            $member->wechat = json_encode($wechatUserInfo);
            if (!$member->getAttr('name')){
                $member->setAttr('name' , $wechatUserInfo['nickname']);
            }
            if ($member->sex == 0){
                $member->sex = $wechatUserInfo['sex'];
            }
            if (!$member->avatar){
                $member->avatar = $wechatUserInfo['headimgurl'];
            }
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

        $memberAuth = MemberAuth::get(['member_id'=>$memberId , 'type' => $authType]);
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
