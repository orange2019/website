<?php
namespace app\controller;
class Apartment extends Saas {
    
    public function index(){
        
        $this->checkProject();
        
        $uid = session('admin_uid');
        $pid = session('admin_pid');
        
//         if ($pid == 0){
//             $project = db('project')->where('uid',$uid)->order('create_time' , 'desc')->select();
//         }else{
//             $project = db('userProject')->where('user_id' , $pid)->find();
//             $projectIds = $project ? $project['projects'] : [];
//             $project = db('project')->where('id' , 'in' , $projectIds)->select();
//         }
        
//         $this->assign('list' , $project);
        
        $list = get_user_project($uid);
        $this->assign('list' , $list);
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
                return $this->error('未添加项目','system/projectAdd');
            }else{
                return $this->formError('未添加项目，请联系系统管理员');
            }
        }
    }
}