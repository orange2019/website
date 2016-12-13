<?php
namespace app\controller\dev;
class Index extends Dev {
    
    public function index(){
        
        return $this->fetch();
    }
}