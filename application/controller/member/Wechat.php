<?php
/**
 * Created by PhpStorm.
 * User: lucong
 * Date: 2017/3/26
 * Time: 上午2:17
 */

namespace app\controller\member;


use app\controller\Home;

class Wechat extends Home
{
    public function index(){

        $uid = $this->auth();
        $member = $this->getUserInfoByUid($uid);
        $this->assign('member' , $member);

        return $this->fetch();
    }

}