<?php
/**
 * Created by PhpStorm.
 * User: lucong
 * Date: 2017/3/31
 * Time: 下午11:12
 */

namespace app\controller\admin;


use think\Request;

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

    public function loanStep1(){

        return $this->step(1);
    }

    public function loanStep2(){
        return $this->step(2);
    }

    public function loanStep3(){
        return $this->step(3);
    }

    public function loanStep4(){
        return $this->step(4);
    }

    public function loanStep5(){
        return $this->step(5);
    }

    public function loanStep6(){
        return $this->step(6);
    }

    public function loanStep7(){
        return $this->step(7);
    }

    protected function step($step){

        $request = Request::instance();
        if ($request->isPost()){

        }else {
            $id = input('id' , 0);
            $loan = db('P2pLoan')->find($id);

            $this->assign('loan' , $loan);
            $this->assign('step' , $step);
            return $this->fetch('admin/p2p/step');
        }
    }

}