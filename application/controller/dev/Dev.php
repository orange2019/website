<?php
namespace app\controller\dev;
use app\controller\Base;
use think\Request;
class Dev extends Base {
    
    public function _initialize(){
        
        if (!session('dev_uid')){
            
            $this->redirect('dev/auth/login');
        }
        
        // 记录
        if (Request::instance()->isPost()){
            $uid = session('dev_uid');
            $action = Request::instance()->url();
            $data = Request::instance()->post();
            log_action($uid, $action, $data);
        }
        
        $this->setNav();
    }
    
    protected function setNav(){
        
        $actions = config('dev.action');
        $controller = Request::instance()->controller();
        $controller = str_replace('Dev.', '', $controller);
        $action = Request::instance()->action();
        
        $ca = $controller . '-' .$action;
        $title = '开发者';
        
        foreach ($actions as $k=>$ac){
            $ac['active'] = ($controller == $k) ? 'active' : '';
            $actions[$k] = $ac;
            if ($controller == $k) {
                $title = $ac['name'];
            }
        }
        
        $topNav = $actions[$controller];
        $subNav = $topNav['child'];
        foreach ($subNav as $k=>$v){
            $v['active'] = ($k == $ca) ? 'active' : '';
            // 当url不为空时
            $subNav[$k] = $v;
            
            if ($k == $ca){
                $title = $v['name'];
            }
        }
        
        $this->assign('tops' , $actions);
        $this->assign('subs' , $subNav);
        $this->assign('title' , $title);
    }
}
