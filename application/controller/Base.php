<?php
namespace app\controller;
use think\Controller;
use think\Request;
class Base extends Controller {
    
    protected function jsonSuccess($data = [] , $msg = '', $url = '' ,$code = 1){
        if ($url){
            $data['url'] = $url;
        }
        return $this->result($data , $code , $msg , 'json');
    }
    
    protected function jsonError($data = [] , $msg = '' , $url = '' ,$code = 0 ){
        if ($url){
            $data['url'] = $url;
        }
        return $this->result($data , $code , $msg , 'json');
    
    }
    
    protected function formSuccess($msg = '' ,  $url = null ,$data = [] , $code = 1){
        if (Request::instance()->isAjax()){
            return $this->jsonSuccess($data , $msg , $url , $code);
        }else{
            return $this->success($msg , $url , $data);
        }
    }
    
    protected function formError($msg = '' ,  $url = null , $data = [] ,  $code = 0){
        if (Request::instance()->isAjax()){
            return $this->jsonError($data , $msg , $url , $code );
        }else{
            return $this->error($msg , $url , $data);
        }
    }
}