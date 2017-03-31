<?php
/**
 * Created by PhpStorm.
 * User: lucong
 * Date: 2017/3/31
 * Time: 下午10:47
 */

namespace app\controller\admin;


class Func extends Admin
{

    public function index(){

        return $this->fetch();
    }
}