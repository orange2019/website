<?php
namespace app\controller;
use think\Config;
use think\Controller;
use think\Request;


class Base extends Controller {
    
    protected function jsonSuccess($data = [] , $msg = '', $url = '' ,$code = 1){
        if ($url){
            $data['url'] = $url;
        }
        return $this->result($data , $code , $msg , 'json');
    }
    
    protected function jsonError($data = [] , $msg = '' , $url = '' ,$code = 0 ){
        if ($url){
            $data['url'] = $url;
        }
        return $this->result($data , $code , $msg , 'json');
    
    }
    
    protected function formSuccess($msg = '' ,  $url = null ,$data = [] , $code = 1){
        if (Request::instance()->isAjax()){
            return $this->jsonSuccess($data , $msg , $url , $code);
        }else{
            return $this->success($msg , $url , $data);
        }
    }
    
    protected function formError($msg = '' ,  $url = null , $data = [] ,  $code = 0){
        if (Request::instance()->isAjax()){
            return $this->jsonError($data , $msg , $url , $code );
        }else{
            return $this->error($msg , $url , $data);
        }
    }

    /**
     * 用户授权
     */
    public function auth(){

        // 通过token授权
        $token = input('token' , '');
        $this->getUidByToken($token , 'wx');

        $uid = session('www_uid');;
        if (!$uid){

            if (is_weixin()){
                // 微信授权
                $code = input('code');
                $wxAuth = new \wechat\Auth();
                if ($code){
                    // 通过code获取授权信息
                    $userInfo = $wxAuth->getUserInfoByCode($code);

                }else {
                    $userInfo = $wxAuth->getUserInfoByCache();
                }

                //查找是否注册用户
                $member = $this->getUserInfoByOpenid($userInfo['openid']);
                if ($member){
                    return $member['id'];
                }else {
                    $this->redirect('auth/wxBind');
                    exit();
                }

            }else {
                // 去登陆

                $this->redirect('auth/login');
                exit();

            }
        }else {
            return $uid;
        }

    }

    /**
     * 通过token获取uid
     * @param $token
     * @param string $type
     * @return int|mixed
     */
    protected function getUidByToken($token , $type = 'wx'){

        $map['token'] = $token;
        $map['type'] = $type;

        $member = db('MemberAuth')->where($map)->find();

        if ($member){
            session('www_uid' , $member['member_id']);
            session('www_auth_id' , $member['id']);

            return $member['member_id'];
        }else {
            return 0;
        }
    }

    /**
     * 通过id获取用户信息
     * @param $uid
     * @return array|false|\PDOStatement|string|\think\Model
     */
    protected function getUserInfoByUid($uid){

        $member = db('Member')->find($uid);

        return $member;
    }

    /**
     * 通过openid获取用户
     * @param $openid
     * @return bool|static
     */
    protected function getUserInfoByOpenid($openid){

        $map['openid'] = $openid;
        $member = \app\model\Member::get($map);

        // 更新授权信息
        if (isset($member->id)){

            $userInfo = session('wechat_user_info');
            $userInfo = json_encode($userInfo);
            $member->wechat = $userInfo;
            $member->save();

            $this->updateTokenInfo($member->id);

            return $member->toArray();
        }else {
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
        $token = md5(session('wechat_openid').time());
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