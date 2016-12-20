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
        
        $request = Request::instance();
        if ($request->isPost()){
            
            $data = $request->post();
            $data['info'] = $data['info'] ? json_encode($data['info']) : '';
            
            $res = \app\model\Project::update($data);
            if ($res){
                cache_clear('user_project_list');
                return $this->formSuccess('操作成功',url('admin/project/index'));
            }else{
                return $this->formError('操作失败');
            }
            
        }else{
            
            $id = input('id');
            $data = db('Project')->find($id);
            $this->assign('data' , $data);
            
            $config = $data['config'];
            $configs = parse_config($config);
            $this->assign('configs' , $configs);
            
            $info = $data['info'] ? json_decode($data['info'] , true) : null;
            $this->assign('info' , $info);
            
            return $this->fetch();
        }

    }
    
    public function status(){
        
        $id = input('id');
        $status = input('status');
        
        $project = \app\model\Project::get($id);
        $project->status = $status;
        $res = $project->save();
        if ($res){
            cache_clear('user_project_list');
            return $this->formSuccess('操作成功');
        }else{
            return $this->formError('操作失败');
        }
    }
    
    public function delete(){
        
        $pid = session('admin_pid');
        if ($pid != 0){
            return $this->formError('无权限删除');
        }
        $id = input('id');
        $count = db('category')->where('project_id' , $id)->count();
        if ($count > 0){
            return $this->formError('存在栏目信息，无法删除');
        }else{
            $res = db('Project')->where('id' , $id)->delete();
            if ($res){
                cache_clear('user_project_list');
                return $this->formSuccess('操作成功');
            }else{
                return $this->formError('操作失败');
            }
        }
    }
}