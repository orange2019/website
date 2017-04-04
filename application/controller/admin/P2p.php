<?php
/**
 * Created by PhpStorm.
 * User: lucong
 * Date: 2017/3/31
 * Time: 下午11:12
 */

namespace app\controller\admin;


use app\model\P2pFinance;
use app\model\P2pLoan;
use LC\FormBuilder;
use think\Db;
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
        session('loan_back' , Request::instance()->url(true));
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

}