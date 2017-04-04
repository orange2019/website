<?php
/**
 * Created by PhpStorm.
 * User: lucong
 * Date: 2017/4/2
 * Time: 下午7:52
 */

namespace app\model;


use think\Model;

class MemberInfo extends Model
{
    protected $autoWriteTimestamp = true;

    public function updateInfo($data){
        if (isset($data['income_year'])){
            $data['income_year'] = $data['income_year'] * 100;
        }
        $info = $this->where('member_id' , $data['member_id'])->find();
        if ($info){
            $res = $info->isUpdate(true)->save($data , ['id' => $info->id]);
        }else{
            $res = $this->data($data)->save();
        }
        return $res;
    }
}