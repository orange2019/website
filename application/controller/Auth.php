<?php
namespace app\controller;
use think\Request;
use app\model\User;
class Auth extends Base {
    
    public function login(){
        
        $request = Request::instance();
        
        if ($request->isPost()){
            
            $data = $request->post();
            // 验证
            $check = $this->validate($data, 'Login');
            if ($check !== true){
                return $this->formError($check);
            }
            
            $User = new User();
            $user = $User->login($data);
            
            if ($user){
                
                $uid = $user->id;
                $pid = $user->pid;
                if ($user->status != 1){
                    return $this->formError('您还未通过审核！');
                }else{
                    // 记录
                    log_action($uid, 'saas_login', $data);
                    // session
                    session('admin_uid' , $uid);
                    session('admin_pid' , $pid);
                    session('admin_username' , $user->name);
                    session('admin_useremail' , $user->email);
                    
                    $this->formSuccess('登录成功',url('index/index'));
                }
                
            }else{
                $this->formError('用户名或密码错误，请重试');
            }
        }else{
            
            return $this->fetch();
        }
    }
    
    public function logout(){
        
        $uid = session('admin_uid');
        
        log_action($uid, 'saas_logout', []);
        
        session('admin_uid' , null);
        
        return $this->redirect('auth/login');
        
    }
    
    public function reg(){
        
        $request = Request::instance();
        if ($request->isPost()){
            $data = $request->post();
            
            $check = $this->validate($data, 'Reg');
            if ($check !== true){
                return $this->formError($check);
            }else{
                unset($data['captcha']);
                unset($data['__token__']);
            }
            // 密码
            if ($data['password'] != $data['password_again']){
                return $this->formError('两次密码输入不一样');
            }else{
                unset($data['password_again']);
                $data['password'] = md5($data['password']);
            }
            
            $data['name'] = explode('@', $data['email'])[0];
            $User = new User();
            $user = $User->reg($data);
            if ($user){
                log_action($user->id, 'saas_reg', $data);
                return $this->formSuccess('提交成功,请等待审核通过');
            }else{
                return $this->formError($User->getError());
            }
        }else{
            
            return $this->fetch();
        }
    }
    
    public function info(){
        
        $uid = session('admin_uid');
        if (!$uid){
            return $this->formError('您还未登录');
        }
        
        $request = Request::instance();
        if ($request->isPost()){
            $post = input('post.');
            // 检验重复
//             if ($post['id']){
//                 $where = "(`email` = '".$post['email']."' OR `name` = '".$post['name']."') and `id` != ".$post['id'];
//             }else{
//                 $where = "(`email` = '".$post['email']."' OR `name` = '".$post['name']."')";
//             }
//             $check = db('user')->where($where)->count();
            
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
            }
            
            if ($res){
                return $this->formSuccess('操作成功' , url('index/index'));
            }else{
                return $this->formError('操作失败');
            }
        }else{
            
            $id = session('admin_uid');
            if ($id){
                $data = db('user')->find($id);
            }else{
                $data = null;
            }
            
            $this->assign('data' , $data);
            $this->assign('tops' , null);
            $this->assign('subs' , null);
            $this->assign('title' , '修改信息');
            return $this->fetch();
        }
    }
}