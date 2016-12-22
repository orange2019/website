<?php
namespace app\controller;

use PY\Pinyin;
class Index extends Base {
    
    public function index(){
        
        return $this->redirect('admin/index/index');
    }
    
    public function test(){
        
//         echo Pinyin::encode('我是鲁聪' , 'all');

        $val = 18 / 10;
        echo $val;
    }
}