<?php
/**
 * Created by PhpStorm.
 * User: lucong
 * Date: 2017/3/31
 * Time: 下午11:12
 */

namespace app\controller\admin;


class P2p extends Admin
{
    public function index(){

    }

    public function loan(){

        $map['status'] = ['EGT' , 0];
        $query = [];
        $list = db('P2pLoan')->where($map)->order('create_time desc')->paginate(20 , false , ['query'=>$query]);
        $page = $list->render();

        $this->assign('list' , $list);
        $this->assign('page' , $page);

        $this->assign('steps' , config('p2p.loan_step'));

        return $this->fetch();

    }
}