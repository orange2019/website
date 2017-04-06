<?php
/**
 * Created by PhpStorm.
 * User: lucong
 * Date: 2017/4/3
 * Time: 下午1:20
 */

namespace app\model;
/**
 * p2p资金筹集表
 */

use LC\Money;
use LC\Time;
use think\Model;

class P2pRaise extends Model
{
    protected $autoWriteTimestamp = true;

    public function financeIn($financeId , $memberId  , $num){
        if (is_numeric($num)){
            $num = $num * 100;
        }else{
            $this->error = '投资数目输入不正确';
            return false;
        }
        // 查询fiannce lock 判断是否自己
        $finance = P2pFinance::lock(true)->find($financeId);
        if ($finance['member_id'] == $memberId){
            $this->error = '不符合投资规则';
            return false;
        }

        // 判断是否截止
        if ($finance['raise_end_time'] < time()){
            $this->error = '超过截止时间';
            return false;
        }


        $Finance = new P2pFinance();
        // 判断剩余，及投资数目是否大于剩余
        $raise = $Finance->getRaiseById($financeId);
        $total = $finance->num - $raise;
        if ($total <= 0){
            $this->error = '投资已满额度';
            return false;
        }elseif($total < $num) {
            $this->error = '超过可投额度';
            return false;
        }

        // 判断是否大于最低投资额
        if ($total >= $finance->min_num){
            if ($num < $finance->min_num){
                $this->error = '小于最低投资额';
                return false;
            }
        }

        // 创建买入数据
        $data['member_id'] = $memberId;
        $data['finance_id'] = $financeId;
        $data['num'] = $num;
        $p2pRaise = $this->data($data)->save();
        if ($p2pRaise === false){
            $this->error = '买入失败';
            return false;
        }

        // 我的钱数减少 memberValue moneyOut
        $MemberValue = new MemberValue();
        $money = $num * -1 / 100;
        $resMV = $MemberValue->moneyChange($memberId , $money, 'raise');
        if ($resMV === false){
            $this->error = '扣款失败';
            return false;
        }

        // 判断买入是否完成，完成将loan扭转至下一个步骤
        $raise = $Finance->getRaiseById($financeId);
        if ($raise == $finance->num){
            $loan = P2pLoan::where('finance_id' , $financeId)->find();
            $loan->step = $loan->step + 1;
            $resL = $loan->save();
            if ($resL === false){
                $this->error = '借款状态改变失败';
                return false;
            }

            // 改变finance的状态
            $finance->status = 1;
            $res = $finance->save();
            if ($res === false){
                $this->error = '改变项目状态失败';
                return false;
            }
        }

        return true;


    }

    public function updateInterest($loanId , $dateTime){

        $loan = P2pLoan::get($loanId);
        $financeId = $loan->finance_id;
        return $this->updateInterestByFinance($financeId , $dateTime);

    }

    /**
     * 更新收益利息
     * @param $financeId
     * @param $datetime
     * @return bool
     */
    public function updateInterestByFinance($financeId , $datetime ){

        $finance = P2pFinance::get($financeId);
        $rate = $finance->income_rate / 100;
        $month = $finance->time_limit;

        $map['finance_id'] = $financeId;
        $map['status'] = 0;
        $lists = $this->where($map)->select();
        foreach ($lists as $v){
            $money = $v->num / 100;
            $interest = Money::incomeInterest($money , $month , $rate);
            $sum = ($interest + $money) * 100;
            $recycleDate = Time::AddMonth($month , $datetime);

            $v->interest = $interest * 100;
            $v->sum = $sum;
            $v->recycle_date = $recycleDate;
            $res = $v->save();
            if ($res === false){
                return false;
            }
        }

        return true;
    }

    /**
     * 分享收益操作
     * @param null $time
     * @return bool
     */
    public function doShare($time = null){

        if ($time === null){
            $time = time();
        }

        $raiseBackDays = config('p2p.raise_back_days');
        $map['recycle_date'] = ['elt' , $time - ($raiseBackDays * 24 * 3600)];
        $map['status'] = 0;

        $raises = $this->where($map)->lock(true)->select();

        $MemberValue = new MemberValue();
        foreach ($raises as $raise){
            $memberId = $raise->member_id;
            $money = $raise->sum / 100;

            $resM = $MemberValue->moneyChange($memberId , $money , 'raise_share');
            if ($resM === 'false'){
                $this->error = $MemberValue->getError();
                return false;
//                throw new \Exception($MemberValue->getError());
            }

            $raise->status = 1;
            $raise->recycle_time = $time;
            $resR = $raise->save();
            if ($resR === false){
                $this->error = '结算错误';
                return false;
//                throw new \Exception('结算错误');
            }

        }
    }
}