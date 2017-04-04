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

}