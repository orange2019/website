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
    public function goStep($id , $step , $info = [] , $pics = [] , $update = [] , $uid){

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

        $after = $this->afterStep($step);
        if (!$after){
            $this->error = '';
            return false;
        }


        $update['step'] = $step++;
        $update['id'] = $id;
        $res = $this->isUpdate(true)->update($update);
        if ($res){
            return true;
        }else{
            $this->error = '操作失败';
            return false;
        }



    }

    public function checkStepInfo($info  , $step){

        return true;
    }

    public function afterStep($step){

        return true;
    }

    public function logLoadInfo($id , $step , $info , $pics = [] , $uid){

        $data['load_id'] = $id;
        $data['step'] = $step;
        $data['info'] = is_array($info) ? json_encode($info) : $info;
        $data['pics'] = is_array($pics) ? implode(',' , $pics) : '';
        $data['uid'] = $uid;

        $res = P2pLoanLog::create($data);
        return $res;
    }
}