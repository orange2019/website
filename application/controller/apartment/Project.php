<?php
namespace app\controller\apartment;
use app\controller\Saas;
class project extends Saas {
    
    public function index(){
        
        $this->checkProject();
        
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