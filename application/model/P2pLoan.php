<?php
/**
 * Created by PhpStorm.
 * User: lucong
 * Date: 2017/3/31
 * Time: 下午11:11
 */

namespace app\model;

use think\Model;

class P2pLoan extends Model
{
    protected $autoWriteTimestamp = true;

    /**
     * @param $id
     * @param $step
     * @param array $info
     * @param array $pics
     * @param array $update
     * @param $uid
     * @return bool
     */
    public function goStep($id , $step , $info = '' , $pics = [] , $update = [] , $finance = [], $uid){

        $load = $this->find($id);
        if ($load->step != $step){
            $this->error = '错误';
            return false;
        }

        // 检查info
        $check = $this->checkStepInfo($info , $step);
        if (!$check){
            $this->error = '信息提交错误';
            return false;
        }

        $log =  $this->logLoadInfo($id , $step , $info , $pics , $uid);
        if (!$log->id){
            $this->error ='信息记录失败';
            return false;
        }

        $after = $this->afterStep($step , $update);
        if (!$after){
//            $this->error = '';
            return false;
        }

        // 生成p2p项目
        if ($finance){

            $financeId = $this->createFinanceData($finance , $update);
            if ($financeId){
                $update['finance_id'] = $financeId;
            }else{
                return false;
            }
        }

        $update = $this->formatUpdateData($update , $id , $step);
        $res = $this->save($update , ['id' => $id]);
        if ($res){
            return true;
        }else{
            $this->error = '操作失败';
            return false;
        }

    }

    protected function formatUpdateData($update , $id , $step) {
        if (isset($update['value_pre'])){
            $update['value_pre'] = $update['value_pre'] * 100;
        }
        if (isset($update['info_content'])){
            $update['info_content'] = implode(',' , $update['info_content']);
        }
        if (isset($update['examine'])){
            $update['examine'] = implode(',' , $update['examine']);
        }
        if (isset($update['guarantee'])){
            $update['guarantee'] = implode(',' , $update['guarantee']);
        }
        if (isset($update['info_complete']) && $update['info_complete'] == 0){
            $update['step'] = $step;
        }elseif (isset($update['check_complete']) && $update['check_complete'] == 0){
            $update['step'] = $step;
        }elseif (isset($update['raise_complete']) && $update['raise_complete'] == 0){
            unset($update['raise_complete']);
            $update['step'] = $step;
        }elseif (isset($update['loan_date'])){
            $update['loan_date'] = strtotime($update['loan_date']);
            $update['step'] = $step + 1;
        }else{
            $update['step'] = $step + 1;
        }

        $update['id'] = $id;
        $update['uid'] = (session('admin_pid') == 0) ? session('admin_uid') : session('admin_pid');
        return $update;
    }

    public function checkStepInfo($info  , $step){

        return true;
    }

    public function afterStep($step , $update){
        if (isset($update['level_value'])){
            // 设置用户评级
            $memberId = $update['member_id'];
            $memberInfo = MemberInfo::get($memberId);
            $memberInfo->level_value = $update['level_value'];
            $res = $memberInfo->save();
            if ($res){
                return true;
            }else{
                $this->error = '设置用户评级失败';
                return false;
            }
        }elseif (isset($update['loan_date'])){
            // 设置发放贷款日期
            // 生成还款账单
            $Order = new P2pOrder();
            $resO = $Order->createLists($update['id']);
            if ($resO === false){
                $this->error = '生成还款账单失败';
                return false;
            }
            // 生成收益账单
            $Raise = new P2pRaise();
            $resR = $Raise->updateInterest($update['id']);
            if ($resO === false){
                $this->error = '更新收益账单失败';
                return false;
            }

        }
        return true;
    }

    public function logLoadInfo($id , $step , $info , $pics = [] , $uid){

        $data['loan_id'] = $id;
        $data['step'] = $step;
        $data['info'] = $info;
        $data['pics'] = is_array($pics) ? implode(',' , $pics) : '';
        $data['uid'] = $uid;

        $res = P2pLoanLog::create($data);
        return $res;
    }

    public function createFinanceData($finance , $loan ){

        if ($finance['id']){
            $finance['raise_end_time'] = strtotime($finance['raise_end_time']) + 24 * 3600 -1;
            $res = P2pFinance::update($finance);
        }else {
            $finance['member_id'] = $loan['member_id'];
            $finance['num'] = $finance['num'] * 100;
            $finance['raise_start_time'] = strtotime($finance['raise_start_time']);
            $finance['raise_end_time'] = strtotime($finance['raise_end_time']) + 24 * 3600 -1;
            $finance['min_num'] = $finance['min_num'] * 100;
            $finance['uid'] = (session('admin_pid') == 0) ? session('admin_uid') : session('admin_pid');

            $res = P2pFinance::create($finance);
        }

        if ($res !== false){
          return $res->id;
        }else {
            $this->error = '更新金融项目失败';
            return false;
        }

    }
}