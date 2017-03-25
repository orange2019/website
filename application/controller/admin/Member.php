<?php
/**
 * Created by PhpStorm.
 * User: lucong
 * Date: 2017/3/26
 * Time: 上午12:37
 */

namespace app\controller\admin;


class Member extends Admin
{
    public function index(){

        $order = 'create_time desc';
        $query = [];
        $map  = [];
        $list = db('member')->where($map)->order($order)->paginate(20 , false , ['query'=>$query]);
        $page = $list->render();

        $this->assign('list' , $list);
        $this->assign('page' , $page);

    }
}