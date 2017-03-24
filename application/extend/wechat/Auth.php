<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/20
 * Time: 12:07
 */

namespace wechat;

use think\Request;

class Auth
{
    /**
     * 获取微信配置
     * @return mixed
     */
    public function getConfig(){

        $request = Request::instance();
//        $config = config('service.wechat');
        // 修改为从数据库获取
        $config = $this->getConfigFromDbCache();

        $configWx['appid'] = $config['app_id'];
        $configWx['appsecret'] = $config['app_secret'];
        $configWx['redirect_uri'] = $request->url(true);
        $configWx['mch_id'] = $config['mch_id'];
        $configWx['pay_notify_url'] = $request->domain() . $config['pay_notify_url'];
        $configWx['pay_api_secret'] = $config['pay_api_secret'];
        $configWx['access_token_type'] = $config['access_token_type'];

        return $configWx;

    }

    public function getConfigFromDbCache(){

        $configCache = cache('wechat_config_data');
        $uid = session('www_project')['uid'];
        if ($configCache && isset($configCache[$uid])){
            $config = $configCache[$uid];
        }else{
            $userConfig = db('UserConfig')->where('uid' , $uid)->find();
            $config = $userConfig['wechat'] ? json_decode($userConfig['wechat']  , true) : null;

            $configData[$uid] = $config;
            cache('wechat_config_data' , $configData);
        }

        return $config;

    }
    /**
     * 通过code获取用户信息
     * @param $code
     * @return Ambigous
     */
    public function getUserInfoByCode($code){

        $config = $this->getConfig();
        $wx = new Api($config);

        $authAccessToken = $wx->get_auth_access_token($code);
        if ($authAccessToken){
            // 设置过期保存时间
            $authAccessToken['dead_time'] = time() + $authAccessToken['expires_in'] - 100;
            cache('auth_access_token_data' , $authAccessToken);

            $openid = $authAccessToken['openid'];
            // 获取用户信息
            $accessToken = $authAccessToken['access_token'];
            $userInfo = $wx->get_auth_user($openid , $accessToken);

            // 保存用户信息
            session('wechat_openid' , $openid);
            session('wechat_user_info' , $userInfo);

            return $userInfo;
        }else {
            echo 'code已过期,请返回重新操作';
        }

    }

    /**
     * 通过缓存获取微信用户信息
     * @return Ambigous
     */
    public function getUserInfoByCache(){

        $config = $this->getConfig();
        $wx = new Api($config);

        $openid = session('wechat_openid');

        if ($openid){

            $authAccessToken = cache('auth_access_token_data');
            if ($authAccessToken && isset($authAccessToken['dead_time']) && $authAccessToken['dead_time'] > time()){
                // 未过期

                $accessToken = $authAccessToken['access_token'];
                $userInfo = $wx->get_auth_user($openid, $accessToken);

                session('wechat_user_info' , $userInfo);
                return $userInfo;

            }
        }

        // 无法获取时,跳转授权
        $url = $wx->get_auth_url();
        header('Location: '.$url , true , 302);
        exit();
    }



}