<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/6
 * Time: 15:35
 */

namespace app\controller\admin;


use app\model\MemberValue;
use app\model\MemberValueLog;
use app\model\P2pFinance;
use app\model\P2pLoan;
use app\model\P2pLoanLog;
use app\model\P2pOrder;
use app\model\P2pRaise;
use think\Db;

class Init
{
    public function p2p(){
        set_time_limit(0);
        Db::startTrans();
        try {

            // 删除申请
            $loan = db('p2pLoan')->where('id' , '>' , 0)->delete();
            if ($loan === false){
                throw  new \Exception('删除loan表失败');
            }

            $loanLog = db('P2pLoanLog')->where('id' , '>' , 0)->delete();
            if ($loanLog === false){
                throw  new \Exception('删除loan_log表失败');
            }

            $finance = db('P2pFinance')->where('id' , '>' , 0)->delete();
            if ($finance === false){
                throw  new \Exception('删除finance表失败');
            }

            $raise = db('P2pRaise')->where('id' , '>' , 0)->delete();
            if ($raise === false){
                throw  new \Exception('删除raise表失败');
            }

            $order = db('P2pOrder')->where('id' , '>' , 0)->delete();
            if ($order === false){
                throw  new \Exception('删除order表失败');
            }

            // 设置membervalue
            $value = db('MemberValue')->Where('id' , '>' , 0)->setField('money' , 0);
            if ($value === false){
                throw  new \Exception('充值member_value表失败');
            }

            $valueLog = db('MemberValueLog')->where('id' , '>' , 0)->delete();
            if ($valueLog === false){
                throw  new \Exception('删除member_vlaue_log表失败');
            }

            Db::commit();
            dump('Success');
        }catch (\Exception $e){

            dump($e->getMessage());
            Db::rollback();
        }




    }
}