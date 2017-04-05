<?php
/**
 * Created by PhpStorm.
 * User: lucong
 * Date: 2017/4/3
 * Time: 下午1:20
 */

namespace app\model;
/**
 * p2p借款订单表
 */

use LC\Money;
use LC\Time;
use think\Model;

class P2pOrder extends Model
{
    protected $autoWriteTimestamp = true;

    public function createLists($loanId){
        $loan = P2pLoan::get($loanId);
        $finance = P2pFinance::get($loan->finance_id);
        $date = $loan->loan_date;
//        $date = strtotime('2017-04-04');
        $memberId = $loan->member_id;
        $month = $finance->time_limit;
        $money = $finance->num / 100;
        $rate = $finance->interest_rate / 100;
        $repayment = $finance->repayment;

        $fees = Money::calculateInterest($money , $month , $rate , $repayment);

        $datas = [];
        for ($i =0 ; $i< $month ;$i++){
            $fee = $fees['data'][$i];
            $data['member_id'] = $memberId;
            $data['finance_id'] = $finance->id;
            $data['loan_id'] = $loanId;
            $data['bill_date'] = Time::AddMonth($i + 1 , $date);
            $data['pay_deadline'] = $data['bill_date'] + 24 * 3600 -1;
            $data['sum'] = $fee['ze'] * 100;
            $data['principal'] = $fee['bj'] * 100;
            $data['interest'] = $fee['lx'] * 100;
            $datas[] = $data;
        }

        // 设置已有账单状态为-1;
        $map['status'] = 0;
        $map['loan_id'] = $loanId;
        $this->where($map)->setField('status' , -1);

        $res = $this->saveAll($datas);
        if ($res === false){
            $this->error = '生成还款账单失败';
            return false;
        }

        $resM = $this->loanOut($memberId , $money);
        if ($resM === false){
            $this->error = '贷款发放失败';
            return false;
        }

        return true;
    }

    public function loanOut($memberId , $num){

        $MemberValue = new MemberValue();
        $res = $MemberValue->moneyChange($memberId , $num , 'loan');
        if ($res){
            return true;
        }else{
            $this->error = $MemberValue->getError();
            return false;
        }
    }
}