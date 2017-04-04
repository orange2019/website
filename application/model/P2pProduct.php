<?php
/**
 * Created by PhpStorm.
 * User: lucong
 * Date: 2017/4/3
 * Time: 下午1:19
 */

namespace app\model;
/**
 * p2p产品系类表
 */

use think\Model;

class P2pProduct extends Model
{
    protected $autoWriteTimestamp = true;

    public function getListsByUid($uid) {
        $list = $this->getListsDetailByUid($uid);
        $data = [];
        foreach ($list as $v){
            $data[$v->id] = $v->getAttr('name');
        }
        return $data;
    }

    public function getListsDetailByUid($uid){
        $map['uid'] = $uid;
        $map['status'] = 1;
        $list = $this->where($map)->select();
        return $list;
    }


}