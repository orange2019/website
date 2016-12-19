<?php
namespace app\controller;
class Index extends Base {
    
    public function index(){
        
        return $this->redirect('admin/index/index');
    }
}