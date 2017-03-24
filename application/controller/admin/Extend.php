<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/24
 * Time: 15:49
 */

namespace app\controller\admin;


class Extend extends Admin
{
    public function index(){

        return $this->fetch();
    }
}