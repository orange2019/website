<?php
namespace app\controller\dev;

use app\controller\Base;
use think\Request;
use think\Log;
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
                $devUid = config('dev.root_uid');
                if ($user->id == $devUid || $user->pid == $devUid){
                    // 记录
                    log_action($uid, 'dev_login', $data);
                    // session
                    session('dev_uid' , $uid);
                    session('dev_username' , $user->name);
                    session('dev_useremail' , $user->email);
                    
                    $this->formSuccess('登录成功',url('dev/index/index'));
                }else{
                    return $this->formError('仅限老司机开车');
                    
                }
                
            }else{
                $this->formError('用户名或密码错误，请重试');
            }
        }else{
            
            return $this->fetch();
        }
    
    }
    
    public function logout(){
        
        $uid = session('dev_uid');
        
        log_action($uid, 'dev_logout', []);
        
        session('dev_uid' , null);
        
        return $this->redirect('dev/auth/login');
        
    }

   
}