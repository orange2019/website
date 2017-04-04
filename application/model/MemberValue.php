<?php
/**
 * Created by PhpStorm.
 * User: lucong
 * Date: 2017/4/3
 * Time: 下午3:50
 */

namespace app\model;


use think\Model;

class MemberValue extends Model
{
    protected $autoWriteTimestamp = true;

    public function getMemberValueByType($memberId , $type = 'money'){

        $value = $this->where('member_id' , $memberId)->find();
        if ($value){
            if (isset($value->$type)){
                return $value->$type;
            }else {
                return 0;
            }
        }else{
            return 0;
        }
    }

    /**
     * 充值
     */
    public function moneyIn($memberId , $num){
        return $this->moneyChange($memberId , $num , 'in');
    }

    /**
     * 提现
     */
    public function moneyOut($memberId , $num){
        return $this->moneyChange($memberId , ($num * -1 ), 'out');
    }

    public function moneyChange($memberId , $num , $type = 'in'){

        $value = $this->where('member_id' , $memberId)->find();
        if ($value){
            $oldMoney = $value->money;
            $newMoney = $oldMoney + $num * 100;

            if ($newMoney < 0){
                $this->error = '超过额度';
                return false;
            }else{
                $value->money = $newMoney;
                $res = $value->save();
            }
        }else{
            // 只能充值
            if ($num <= 0){
                $this->error = '无法提现';
                return false;
            }

            $data['member_id'] = $memberId;
            $data['money'] = $num * 100;
            $res = $this->data($data)->save();

        }

        if ($res === false){
            $this->error = '操作失败';
            return false;
        }else{
            $log = $this->logValue($memberId , 'money' , $type , $num * 100);
            if ($log === false){
                $this->error = '记录失败';
                return false;
            }else {
                return true;
            }

        }
    }

    protected function logValue($memberId , $logType = 'money' , $type = 'in' , $num){

        $data['member_id'] = $memberId;
        $data['log_type'] = $logType;
        $data['type'] = $type;
        $data['num'] = $num;

        $res = MemberValueLog::create($data);

        return $res;
    }
}