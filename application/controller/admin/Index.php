<?php
namespace app\controller\admin;

class Index extends Admin {
    
    public function Index(){
        
        $this->checkProject();

        $uid = session('admin_uid');
        $categorys = $this->getMenus($uid , 'admin/index/index')['menus'];
        $categorys = \niklaslu\UnLimitTree::unlimitedForLayer($categorys);
        $this->assign('categorys' , $categorys);
        
        return $this->fetch();
    }
    
    protected function checkProject(){
    
        $pid = session('admin_pid');
        $uid = session('admin_uid');
        if ($pid == 0){
            $project = db('project')->where('uid' , $uid)->count();
        }else{
            $data = db('userProject')->where('user_id' , $uid)->find();
            $project = $data ? $data['projects'] : 0;
        }
    
        if (!$project){
            if ($pid == 0){
                return $this->formError('未添加项目','admin/project/add');
            }else{
                return $this->formError('未添加项目，请联系系统管理员');
            }
        }
    }
}