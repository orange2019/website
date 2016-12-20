<?php
namespace app\controller\dev;
use think\Request;
use app\model\AuthUserGroup;
use app\model\AuthGroup;
class User extends Dev {
    
    public function index(){
        
        $pid = input('pid' , 0);
        
        $map['pid'] = $pid;
        $list = db('user')->where($map)->select();
        
        $this->assign('list',$list);
        return $this->fetch();
    }
    
    public function update(){
        
        $request = Request::instance();
        if ($request->isPost()){
            
            $uid = session('dev_uid');
            $post = input('post.');
            
            if ($uid == config('dev.root_uid') || $post['id'] == $uid){
                
                // 检验重复
                if ($post['id']){
                    $where = "(`email` = '".$post['email']."' OR `name` = '".$post['name']."') and `id` != ".$post['id'];
                }else{
                    $where = "(`email` = '".$post['email']."' OR `name` = '".$post['name']."')";
                }
                $check = db('user')->where($where)->count();
                
                // 检验密码
                if ($post['password'] != $post['password_again']){
                     $this->formError('两次密码输入不一样');
                }else {
                    unset($post['password_again']);
                    if ($post['password']){
                        $post['password'] = md5($post['password']);
                    }else{
                        unset($post['password']);
                    }
                }
                
                if ($post['id']){
                    $res = \app\model\User::update($post);
                }else{
                    $post['pid'] = $uid;
                    $res = \app\model\User::create($post);
                }
                if ($res){
                    return $this->formSuccess('操作成功' , url('dev/user/index?pid='.config('dev.root_uid')));
                }else{
                    return $this->formError('操作失败');
                }
            }else{
                return $this->formError('无权限操作');
            }
            
        }else{
            $id = input('id' , 0);
            
            if ($id){
                $data = db('user')->find($id);
            }else{
                $data = null;
            }
            $this->assign('data' , $data);
            return $this->fetch();
        }
    }
    
    public function status(){
        
        // 只有root dev可以操作
        $uid = session('dev_uid');
        if ($uid != config('dev.root_uid')){
            return $this->formError('只有超级管理员可以操作');
        }
        $id = input('id' , 0);
        // 超级管理不能操作自己
        if ($id == config('dev.root_uid')){
            return $this->formError('超级管理不能操作自己');
        }
        
        $status = input('status');
        
        // 当审核通过时，判断是否分配角色组
        if ($status == 1){
            $haveGroup = db('AuthUserGroup')->where('user_id' , $id)->count();
            if ($haveGroup == 0){
                return $this->formError('请先分配角色组用户');
            }
        }
        $user = \app\model\User::get($id);
        $user->status = $status;
        $res = $user->save();
        if ($res){
            return $this->formSuccess('操作成功');
        }else{
            return $this->formError('操作失败');
        }
    }
    
    /**
     * 用户分组
     */
    public function group(){
        
        $request = Request::instance();
        if ($request->isPost()){
            
            $data = $request->post();
            
            $userGroup = AuthUserGroup::get(['user_id'=>$data['user_id']]);
            
            if ($userGroup){
                $userGroup->group_id = $data['group_id'];
                $res = $userGroup->save();
            }else{
                $res = AuthUserGroup::create($data);
            }
            if ($res){

                cache_clear('user_group_list' , $data['user_id']);
                return $this->formSuccess('操作成功');
            }else{
                return $this->formError('操作失败');
            }
        }else{
            
            $id = input('id');
            $user = db('User')->find($id);
            $this->assign('user' , $user);
            
            $mapG['status'] = 1;
            $mapG['uid'] = config('dev.root_uid');
            $groups = db('AuthGroup')->where($mapG)->order('sort','asc')->select();
            $this->assign('groups' , $groups);
            
            $userGroup = db('AuthUserGroup')->where('user_id' , $id)->find();
            $userGroupId = $userGroup ? $userGroup['group_id'] : 0;
            $this->assign('userGroup' , $userGroupId);
            
            return $this->fetch();
        }
    }
    
    /**
     * 用户组件
     */
    public function component(){
        
    }
    
    public function project(){
        $uid = input('uid');
        
        $map['uid'] = $uid;
        $list = db('project')->where($map)->select();
        
        $this->assign('list',$list);
        return $this->fetch();
    }
    
    public function projectConfig(){
        
        $request = Request::instance();
        if ($request->isPost()){
            $data = $request->post();
            
            $res = db('Project')->where('id='.$data['id'])->setField('config' , $data['config']);
            if ($res){
                return $this->formSuccess('操作成功');
            }else{
                return $this->formError('操作失败');
            }
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
    
    public function projectTheme(){
        
        $request = Request::instance();
        if ($request->isPost()){
            $data = $request->post();
        
            $res = db('Project')->where('id='.$data['id'])->setField('theme_id' , $data['theme_id']);
            if ($res){
                return $this->formSuccess('操作成功');
            }else{
                return $this->formError('操作失败');
            }
        }else{
            $id = input('id');
            if (!$id){
                return $this->formError('请选择项目');
            }else{
        
                $data = db('Project')->find($id);
                $this->assign('data' , $data);
                
                $themes = db('Theme')->where('status' , 1)->select();
                $this->assign('themes' , $themes);
                return $this->fetch();
            }
        }
        
    }

}