<?php
namespace app\controller;
use niklaslu\Auth;
use think\Request;
use niklaslu\UnLimitTree;
class Saas extends Base {
    
    public function _initialize(){
        
        if (!session('admin_uid')){
            return $this->redirect('auth/login');
        }
        
        // 
        $uid = session('admin_uid');
        $pid = session('admin_pid');
        
        if ($uid == config('dev.root_uid')){
            // 当为开发者时 
        }else{
            // 检测权限
            $request = Request::instance();
            $name = $request->controller() . '/' . $request->action();
            $name = strtolower(str_replace('.', '/', $name));
            
            if ($pid == 0){
                // 顶级管理
                $Auth = new Auth();
                $check = $Auth->check($name, $uid);
                if (!$check){
                    // 检测组件权限 TODO
                    $components = db('UserComponent')->where('status' , 1)->column('component_id');
                    $rule = db('AuthRule')->where('name' ,$name)->where('status' , 1)->find();
                    if ($rule){
                        $componentId = $rule['component_id'];
                        if (in_array($componentId, $components)){
                            $check = 1;
                        }else{
                            $check = 0;
                        }
                    }else{
                        $check = 0;
                    }
                }
                
            }else{
                // 子管理
                $Auth = new Auth();
                $check = $Auth->check($name, $uid); 
                
            }
            
            if (!$check){
                return $this->formError('无权限' , null ,[] , -201 );
            }else{
                // 获取用户的菜单
                $menu = $this->getMenus($uid , $name);
                $this->assign('tops' , $menu['tops']);
                $this->assign('subs' , $menu['subs']);
                $this->assign('title' , $menu['title']);

            }
        }
    }
    
    protected function getMenus($uid , $current){
        
        $Auth = new Auth();
        $userGroupId = $Auth->getGroupIds($uid)[0];
        
//         $data = cache_get('admin_menu_data' , $userGroupId);
        
        $ids = $Auth->getRuleIds($uid);
        
        $map['id'] = ['in' , $ids];
        $map['type'] = 'pc';
        $map['component_id'] = 0;
        $map['status'] = 1;
        //         $map['pid'] = 0;
        
        $title = [];
        $tops = [];
        $subs = [];
        $topId = 0;
        
        $menus = db('authRule')->where($map)->order('sort','asc')->select();
        $menus = menu_format($menus, $current);
        
        // 寻找顶级
        foreach ($menus as $top){
            if ($top['pid'] == 0){
                $tops[] = $top;
                if ($top['active'] == 'active'){
                    $topId = $top['id'];
                    $title[] = $top['title'];
                }
            }
        }
        
        // 寻找二级
        if ($topId){
            foreach ($menus as $sub){
                if ($sub['pid'] == $topId){
                    $subs[] = $sub;
                    if ($sub['active'] == 'active'){
                        $title[] = $sub['title'];
                    }
        
                }
            }
        }
        
        $result['menus'] = $menus;
        $result['tops'] = $tops;
        $result['subs'] = $subs;
        $result['title'] = implode('-', $title);
        
        return $result;
        
    }
}