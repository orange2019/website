<?php
/**
 * Created by PhpStorm.
 * User: lucong
 * Date: 2017/3/31
 * Time: 下午10:20
 */

namespace app\controller\admin;


use app\controller\Base;
use think\Cache;

class Clear extends Base
{
    public function index(){

        Cache::clear();
        $this->formSuccess('操作成功');
    }
}