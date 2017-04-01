<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/1
 * Time: 11:47
 */

namespace LC;


class P2p
{
    static public function occupation($value){
        $data = config('p2p.occupation');
        return $data[$value];
    }

    static public function assets($value){
        if (!$value){
            return '';
        }
        if (!is_array($value)){
            $value = explode(',' , $value);
        }

        $data = config('p2p.assets');
        $res = [];
        foreach ($value as $v){
            $res[] = $data[$v];
        }

        return implode(',',$res);
    }

    static public  function step($value){
        $data = config('p2p.loan_step');
        return $data[$value];
    }
}