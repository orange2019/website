<?php
namespace app\controller\dev;
class Theme extends Dev {
    
    public function index(){
        
        return $this->fetch();
    }
}