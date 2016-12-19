<?php
namespace app\controller\admin;
use think\Request;
class project extends Admin {
    
    public function index(){
        
        $uid = session('admin_uid');
        $pid = session('admin_pid');

        $list = get_user_project($uid);
        $this->assign('list' , $list);
        return $this->fetch();
    }
     
    public function add(){
        
        $uid = (session('admin_pid') == 0) ? session('admin_uid') : session('admin_pid');
        
        $request = Request::instance();
        if ($request->isPost()){
            $post = $request->post();
        
            $check = db('project')->where('title' , $post['title'])->whereOr('name' , $post['name'])->count();
            if ($check > 0){
                return $this->formError('存在相同名称或者代号的项目，请重新添加');
            }else{
                $post['uid'] = $uid;
                $res = \app\model\Project::create($post);
                if ($res){
                    cache_clear('user_project_list' , $uid);
                    return $this->formSuccess('添加成功' , url('admin/project/index'));
                }else{
                    return $this->formError('添加失败');
                }
            }
        
        }else{
            
            return $this->fetch();
        }
    }
    
    public function update(){


        $id = input('id');
        $data = db('Project')->find($id);
        
        $config = $data['config'];dump($config);
        $config = parse_config($config);
        
        dump($config);
    }
    
    public function status(){
        
    }
    
    public function delete(){
        
    }
}