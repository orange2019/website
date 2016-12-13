<?php
namespace app\model;
use think\Model;
class User extends Model {
    
    protected $autoWriteTimestamp = true;
    
    public function login($data){
    
        $map['email'] = $data['email'];
        $user = $this->where($map)->find();
    
        if ($user){
            $password = md5($data['password']);
            if ($user->password != $password){
                return false;
            }else{
                return $user;
            }
        }else{
            //             $this->error = '用户名或密码错误';
            return false;
        }
    }
    
    public function reg($data , $status = 0){
        
        // 检查有无重复
        $map['status'] = ['EGT' , 0];
        if (isset($data['email'])){
            $map['email'] = $data['email'];
            $count = $this->where($map)->count();
            if ($count > 0){
                $this->error = '邮箱已被注册,请使用其他可用邮箱';
                return false;
            }
        }
        
        $data['pid'] = 0;
        $data['status'] = $status;
        $user = $this->create($data);
        
        return $user;
    }
}