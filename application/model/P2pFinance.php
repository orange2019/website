<?php
/**
 * Created by PhpStorm.
 * User: lucong
 * Date: 2017/4/3
 * Time: 下午1:18
 */

namespace app\model;
/**
 * p2p金融项目表
 */

use think\Model;

class P2pFinance extends Model
{
    protected $autoWriteTimestamp = true;

    public function getListsByPid($productId , $p = 1, $pagesize = 5){

        $map['product_id'] = $productId;
        $map['status'] = 0;
        $list = $this->where($map)->order('create_time desc')->page($p , $pagesize)->select();
        foreach ($list as $k=>$v){
            $v['raise_num'] = $this->getRaiseById($v->id);
//            $v['raise_num'] = 19500;
            $v['percentage'] = $v['raise_num'] / $v->num;
            $list[$k] = $v;
        }
        return $list;

    }

    public function getRaiseById($financeId){
        $mapR['finance_id'] = $financeId;
        $mapR['status'] = ['egt' , 0];
        $count = P2pRaise::where($mapR)->sum('num');
        return $count ? $count : 0;
    }

    /**
     * 取消产品项目
     * @param $financeId
     * @return bool
     */
    public function cancel($financeId){

        if ($financeId == 0){
            return true;
        }

        $finance = $this->find($financeId);
        $finance->status = -1;
        $res = $finance->save();
        if ($res === false){
            $this->error = '取消对应产品项目失败';
            return false;
        }


        // 项目对应筹资状态改变
        $raises = P2pRaise::where('finance_id' , $financeId)->select();
        if (count($raises) > 0){
            $MemberValue = new MemberValue();
            foreach ($raises as $raise){
                // 改变状态
                $raise->status = -1;
                $resR = $raise->save();
                if ($resR === false){
                    $this->error = '改变项目筹资条目状态失败';
                    return false;
                }
                // 退回投资
                $num = $raise->num / 100;
                $memberId = $raise->member_id;
                $resM = $MemberValue->moneyChange($memberId , $num , 'raise_back');
                if (!$resM){
                    $this->error = '退回筹资失败';
                    return false;
                }

            }


        }

        return true;
    }

}