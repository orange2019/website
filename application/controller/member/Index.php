<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/20
 * Time: 15:27
 */

namespace app\controller\member;


use app\controller\Home;

class Index extends Home
{
    public function index(){

        if (is_weixin()){
            $this->redirect('member/wechat/index');
        }
        $uid = $this->auth();

        dump($uid);
    }

    public function info(){

        $userInfo = session('wechat_user_info');
        dump($userInfo);
    }
}