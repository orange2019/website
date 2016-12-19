<?php
namespace app\controller\dev;
use think\Request;
class Project extends Dev {
    
    public function index(){
        
        $uid = input('uid');
        
        $map['uid'] = $uid;
        $list = db('project')->where($map)->select();
        
        $this->assign('list',$list);
        return $this->fetch();
    }
    
    public function config(){
        
        $request = Request::instance();
        if ($request->isPost()){
            
        }else{
            $id = input('id');
            if (!$id){
                return $this->formError('请选择项目');
            }else{
                
                $data = db('Project')->find($id);
                $this->assign('data' , $data);
                
                return $this->fetch();
            }
        }
        
    }
}