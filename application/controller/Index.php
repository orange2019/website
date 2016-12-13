<?php
namespace app\controller;
class Index extends Saas {
    
    public function index(){
        
        $uid = session('admin_uid');
        $pid = session('admin_pid');
        
        $list = get_user_project($uid);
        $this->assign('list' , $list);
        
        return $this->fetch();
    }
}