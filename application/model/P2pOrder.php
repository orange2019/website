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

        $loan->step = $loan->step + 1;
        $resL = $loan->save();
        if ($resL === false){
            $this->error = '借款状态更新失败';
            return false;
        }

        return true;
    }

    /**
     * 发放贷款
     * @param $memberId
     * @param $num
     * @return bool
     */
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

    /**
     * 支付账单
     * @param $orderId
     * @param int $type
     */
    public function payList($orderId , $type = 1){

        $order = $this->find($orderId);
        $memberId = $order->member_id;
        $money = $order->sum + $order->late_fee;
        if ($type == 1){
            // 钱包支付
            $MemberValue = new MemberValue();
            $res = $MemberValue->moneyChange($memberId , $money / 100 * -1 , 'pay_p2p');
            if ($res === false){
                $this->error = $MemberValue->getError();
                return false;
            }
        }

        $order->status = 1;
        $resO = $order->save();
        if ($resO === false){
            $this->error = '账单状态改变失败';
            return false;
        }

        // 判断是否全部支付完成
        $loanId = $order->loan_id;
        $map['loan_id'] = $loanId;
        $map['status'] = 0;
        $count = $this->where($map)->count();
        if($count == 0){
            $loan = P2pLoan::get($loanId);
            $loan->step = $loan->step + 1;
            $loan->status = 1;
            $resL = $loan->save();
            if ($resL === false){
                $this->error = '借款记录更新失败';
                return false;
            }
        }

        return true;

    }

    public function lateFeeCreate($time = null , $auto = 1){

        if ($time === null){
            $time = time();
        }

        $map['pay_deadline'] = ['elt' , $time];
        $map['status'] = 0;
        $orders = $this->where($map)->lock(true)->select();
        $MemberValue = new MemberValue();
        foreach ($orders as $order){
            if ($auto == 1){
                // 先从钱包扣款
                $memberId = $order->member_id;
                $money= ($order->sum + $order->late_fee)/100;
                $resM = $MemberValue->moneyChange($memberId , $money * -1 , 'pay_p2p_auto');
            }else{
                $resM = false;
            }

            if ($resM === false){
                $days = ceil( ($time - $order->pay_deadline) / 24 / 3600 );
                $num = $order->sum;
                $fee = $num * $days * config('p2p.late_fee_rate');

                $order->late_fee = $fee;
                $order->late_days = $days;
                $res = $order->save();
                if ($res === false){
//                    throw  new \Exception('计算错误，请重试');
                    $this->error = '计算错误，请重试';
                    return false;
                }

            }
        }

        return true;
    }
}