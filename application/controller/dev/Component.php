<?php
namespace app\controller\dev;
use app\model\AuthComponent;
use think\Request;
use app\model\UserComponent;
class Component extends Dev {
    
    public function index(){
        
        $list = db('AuthComponent')->order('create_time' , 'desc')->select();
        $this->assign('list' , $list);
        return $this->fetch();
    }
    
    public function update(){
        
        $request = Request::instance();
        if ($request->isPost()){
        
            $uid = session('dev_uid');
            $post = $request->post();
        
            // 检验重复
            if ($post['id']){
                $where = "(`title` = '".$post['title']."' OR `name` = '".$post['name']."') and `id` != ".$post['id'];
            }else{
                $where = "(`title` = '".$post['title']."' OR `name` = '".$post['name']."')";
            }
            $check = db('AuthComponent')->where($where)->count();
    
            if ($post['id']){
                $res = AuthComponent::update($post);
            }else{
                $post['uid'] = config('dev.root_uid');
                $res = AuthComponent::create($post);
            }
            if ($res){
                return $this->formSuccess('操作成功' , url('dev/component/index'));
            }else{
                return $this->formError('操作失败');
            }
            
        
        }else{
            $id = input('id' , 0);
        
            if ($id){
                $data = db('AuthComponent')->find($id);
            }else{
                $data = null;
            }
            $this->assign('data' , $data);
            return $this->fetch();
        }
        
    }
    
    public function status(){
        $id = input('id' , 0);
        
        $status = input('status');
        $component = AuthComponent::get($id);
        
        $component->status = $status;
        $res = $component->save();
        if ($res){
            return $this->formSuccess('操作成功');
        }else{
            return $this->formError('操作失败');
        }
    }
    
    public function apply(){
        $uid = input('uid' , 0);
        if ($uid > 0 && $uid != config('dev.root_uid')){
            $map['a.user_id'] = $uid;
        }
        
        $status = input('status', '');
        if ($status !== ''){
            $map['a.status'] = $status;
        }else{
            $map['a.status'] = 0;
        }
        
        $field = 'a.*,b.title title,c.email user_email';
        $list = db('UserComponent')->alias('a')
                ->join('t_auth_component b','a.component_id = b.id')
                ->join('t_user c','a.user_id = c.id')
                ->where($map)
                ->order('a.create_time' , 'desc')
                ->field($field)
                ->select();
        
        $this->assign('list' , $list);
        return $this->fetch();
        
        
    }
    
    public function applyCheck(){
        
        $id = input('id' , 0);
        
        $status = input('status');
        $component = UserComponent::get($id);
        
        $component->status = $status;
        $res = $component->save();
        if ($res){
            return $this->formSuccess('操作成功');
        }else{
            return $this->formError('操作失败');
        }
    }
}