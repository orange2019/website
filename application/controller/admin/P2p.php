<?php
/**
 * Created by PhpStorm.
 * User: lucong
 * Date: 2017/3/31
 * Time: 下午11:12
 */

namespace app\controller\admin;


use app\model\MemberValue;
use app\model\P2pFinance;
use app\model\P2pLoan;
use app\model\P2pLoanLog;
use app\model\P2pOrder;
use app\model\P2pRaise;
use LC\FormBuilder;
use think\Db;
use think\Request;

class P2p extends Admin
{
    public function index(){

        $this->redirect('admin/p2p/loan');
    }

    public function loan(){

        $map['status'] = ['EGT' , 0];
        $query = [];
        $list = db('P2pLoan')->where($map)->order('create_time desc')->paginate(20 , false , ['query'=>$query]);
        $page = $list->render();

        $this->assign('list' , $list);
        $this->assign('page' , $page);

        $this->assign('steps' , config('p2p.loan_step'));
        session('loan_back' , Request::instance()->url(true));
        return $this->fetch();

    }

    public function loanCancel(){

        $request = Request::instance();
        if ($request->isPost()){
            $data = $request->post();
            $loanId = $data['id'];
            $cancelReason = $data['cancel_reason'];

            Db::startTrans();
            try {
                $Loan = new P2pLoan();
                $res = $Loan->cancel($loanId , $cancelReason);
                if ($res === false){
                    throw new \Exception($Loan->getError());
                }
                Db::commit();
            }catch (\Exception $e){
                Db::rollback();
                return $this->formError($e->getMessage());
            }

            return $this->formSuccess('操作成功' , session('loan_back'));


        }
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

    public function loanStep8(){
        return $this->step(8);
    }

    protected function step($step){

        $request = Request::instance();
        if ($request->isPost()){

            $data = $request->post();
            $loan = $data['loan'];
            $info = $data['info'];
            $finance = isset($data['finance']) ? $data['finance'] : [];
            $uid = session('admin_uid');

            Db::startTrans();
            try {
                $loanData = db('P2pLoan')->lock(true)->find($loan['id']);
                $Loan = new P2pLoan();
                $res = $Loan->goStep($loan['id'] , $loanData['step'] , $info , [], $loan , $finance, $uid);
                if (!$res){
                    $error = $Loan->getError();
                    throw new \Exception($error);
                }

                Db::commit();
            }
            catch (\Exception $e){
                $error = $e->getMessage();
                Db::rollback();
                return $this->formError($error);
            }

            return $this->formSuccess('操作成功' , session('loan_back'));
        }else {
            $id = input('id' , 0);
            $loan = db('P2pLoan')->find($id);
            $memberInfo = db('MemberInfo')->where('member_id' , $loan['member_id'])->find();
            $logs = db('P2pLoanLog')->where('loan_id' , $id)->select();
            $examine = \LC\P2p::examine($loan['examine']);
            $guarantee = \LC\P2p::guarantee($loan['guarantee']);
            $finance = $loan['finance_id'] ? db('P2pFinance')->find($loan['finance_id']) : null;
            if ($finance){
                $Finance = new P2pFinance();
                $raise = $Finance->getRaiseById($finance['id']);
                $percentage = $raise / $finance['num'];
            }else{
                $raise = 0;
                $percentage = 0;
            }

            // 还款账单
            $map['loan_id'] = $id;
            $map['status'] = 0;
            $order = db('P2pOrder')->where($map)->order('id asc')->find();

            $this->assign('loan' , $loan);
            $this->assign('step' , $step);
            $this->assign('info' , $memberInfo);
            $this->assign('logs' , $logs);
            $this->assign('examines' , $examine);
            $this->assign('guarantees' , $guarantee);
            $this->assign('finance' , $finance);
            $this->assign('raise' , $raise);
            $this->assign('percentage' , $percentage);
            $this->assign('order' , $order);

            if ($step == 5){
                $loan['raise_end_time'] = date('Y-m-d' , $finance['raise_end_time']);
            }
            $form = \LC\P2p::stepInfoForm($step , $loan);
            $this->assign('form' , $form);

            if ($loan['step'] <= 5){
                $formCancel = FormBuilder::init(['ns'=>'we'])->setFormName('p2p-load-cancel')
                    ->addClass('form-ajax')->setAction(url('admin/p2p/loanCancel'))
                    ->addTextarea('cancel_reason' , '取消原因' , '' , '请输入取消受理此处借款申请的原因' , true)
                    ->addHidden('id' , '' , $loan['id'])
                    ->addSubmit('确认取消受理')->build();
            }else {
                $formCancel = null;
            }

            $this->assign('form_cancel' , $formCancel);
            return $this->fetch('admin/p2p/step');
        }
    }

    /**
     * 筹资项目
     */
    public function finance(){

        $size = input('size' , 12);
        $map['a.status'] = ['EGT' , 0];
        $query = [];

        $list = db('P2pFinance')->alias('a')
            ->join('t_p2p_product b' , 'a.product_id = b.id')
            ->join('t_member c' , 'a.member_id = c.id')
            ->where($map)
            ->field('a.* , b.name,c.name member_name')
            ->order('create_time desc')
            ->paginate($size , false , ['query'=>$query]);
        $page = $list->render();

        $this->assign('list' , $list);
        $this->assign('page' , $page);
        return $this->fetch();
    }
    /*
     * 还款账单
     */
    public function order(){

        $size = input('size' , 12);
        $map['a.status'] = ['EGT' , 0];
        $query = [];

        $financeId = input('finance_id' , 0);
        if ($financeId){
            $map['a.finance_id'] = $financeId;
            $query['finance_id'] = $financeId;
        }
        $loanId = input('loan_id' , 0);
        if ($loanId){
            $map['a.loan_id'] = $loanId;
            $query['loan_id'] = $loanId;
        }


        $list = db('P2pOrder')->alias('a')
            ->join('t_member b' , 'a.member_id = b.id')
            ->where($map)
            ->field('a.* , b.name')
            ->order('create_time desc')
            ->paginate($size , false , ['query'=>$query]);
        $page = $list->render();

        $this->assign('list' , $list);
        $this->assign('page' , $page);
        return $this->fetch();

    }

    /**
     * 生成滞纳金,优先从钱包扣款
     */
    public function lateFeeCreate(){

        set_time_limit(0);
        $time = time();
//        $time = strtotime('2017-05-07 23:59:59');

        Db::startTrans();
        try {
            $Order = new P2pOrder();
            $res = $Order->lateFeeCreate($time);
            if ($res === false){
                throw  new \Exception($Order->getError());
            }
//            $map['pay_deadline'] = ['elt' , $time];
//            $map['status'] = 0;
//            $orders = P2pOrder::where($map)->lock(true)->select();
//            foreach ($orders as $order){
//                $days = ceil( ($time - $order->pay_deadline) / 24 / 3600 );
//                $num = $order->sum;
//                $fee = $num * $days * config('p2p.late_fee_rate');
//
//                $order->late_fee = $fee;
//                $order->late_days = $days;
//                $res = $order->save();
//                if ($res === false){
//                   throw  new \Exception('计算错误，请重试');
//                }
//
//            }

            Db::commit();
        }catch (\Exception $e){
            Db::rollback();
            return $this->formError($e->getMessage());
        }

        return $this->formSuccess('Success');


    }

    /*
     * 投资明细
     */
    public function raise(){

        $size = input('size' , 12);
        $map['a.status'] = ['EGT' , 0];
        $query = [];

        $financeId = input('finance_id' , 0);
        if ($financeId){
            $map['a.finance_id'] = $financeId;
            $query['finance_id'] = $financeId;
        }

        $list = db('P2pRaise')
            ->join('t_member b' , 'a.member_id = b.id')
            ->join('t_p2p_finance c' , 'c.id = a.finance_id')
            ->where($map)
            ->field('a.* , b.name , c.code')
            ->order('create_time desc')
            ->alias('a')
            ->paginate($size , false , ['query'=>$query]);
        $page = $list->render();

        $this->assign('list' , $list);
        $this->assign('page' , $page);
        return $this->fetch();
    }

    /**
     * 出借收益结算
     */
    public function raiseShare(){

        set_time_limit(0);
        $time = time();
        $time = strtotime('2017-07-09 00:00:00');

        Db::startTrans();
        try {

            $Raise = new P2pRaise();
            $res = $Raise->doShare($time);
            if ($res === false){

                throw new \Exception($Raise->getError());
            }
//            $raiseBackDays = config('p2p.raise_back_days');
//            $map['recycle_date'] = ['elt' , $time - ($raiseBackDays * 24 * 3600)];
//            $map['status'] = 0;
//            $raises = P2pRaise::where($map)->lock(true)->select();
//
//            $MemberValue = new MemberValue();
//            foreach ($raises as $raise){
//                $memberId = $raise->member_id;
//                $money = $raise->sum / 100;
//
//                $resM = $MemberValue->moneyChange($memberId , $money , 'raise_share');
//                if ($resM === 'false'){
//                    throw new \Exception($MemberValue->getError());
//                }
//
//                $raise->status = 1;
//                $raise->recycle_time = $time;
//                $resR = $raise->save();
//                if ($resR === false){
//                    throw new \Exception('结算错误');
//                }
//
//            }

            Db::commit();

        }catch (\Exception $e){

            Db::rollback();
            return $this->formError($e->getMessage());
        }

        return $this->formSuccess('Success');
    }

}