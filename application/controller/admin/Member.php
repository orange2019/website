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

        $keyword = input('keyword' , '');
        if ($keyword){
            $where = "`name` like '%".$keyword."%' or `phone` like '%".$keyword."%'";
            $query['keyword'] = $keyword;
        }else{
            $where = "";
        }
        $this->assign('keyword' , $keyword);

        $list = db('member')->where($map)->where($where)->order($order)->paginate(20 , false , ['query'=>$query]);
        $page = $list->render();

        $this->assign('list' , $list);
        $this->assign('page' , $page);

        return $this->fetch();
    }
}