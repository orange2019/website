<?php
/**
 * Created by PhpStorm.
 * User: lucong
 * Date: 2017/4/4
 * Time: 下午5:04
 */

namespace LC;


class Money
{
    static public function calculateInterest($money , $month , $rate , $type = 1) {
        if ($type == 1){
            return self::debx($money , $month , $rate );
        }elseif ($type == 2){
            return self::debj($money , $month , $rate);
        }
    }

    static public function debx($money , $month , $rate){

        $dkm     = $month; //贷款月数，20年就是240个月
        $dkTotal = $money; //贷款总额
        $dknl    = $rate;  //贷款年利率
        $emTotal = $dkTotal * $dknl / 12 * pow(1 + $dknl / 12, $dkm) / (pow(1 + $dknl / 12, $dkm) - 1); //每月还款金额
        $emTotal = number_format($emTotal , 2 , '.' , '');
        $lxTotal = 0; //总利息
        $arr = [];
        for ($i = 0; $i < $dkm; $i++) {

            $lx      = number_format($dkTotal * $dknl / 12 , 2 , '.' ,'');   //每月还款利息
            $em      = $emTotal - $lx;  //每月还款本金
            $dkTotal = $dkTotal - $em;
            $lxTotal = $lxTotal + $lx;
            $arr[$i] = ['bj'=>$em ,'lx'=>$lx ,'ze' => $emTotal , 'sy' => $dkTotal];
        }

        $result['lxTotal'] = $lxTotal;
//        $result['avg'] = $emTotal;
        $result['data'] = $arr;
//        dump($result);
        return $result;

    }

    static public function debj($money , $month , $rate){
        $dkm     = $month; //贷款月数，20年就是240个月
        $dkTotal = $money; //贷款总额
        $dknl    = $rate;  //贷款年利率
        $em      = $dkTotal / $dkm; //每个月还款本金
        $lxTotal = 0; //总利息
        $arr = [];
        for ($i = 0; $i < $dkm; $i++) {
            $lx      = $dkTotal * $dknl / 12; //每月还款利息
//            echo "第" . ($i + 1) . "期", " 本金:", $em, " 利息:" . $lx, " 总额:" . ($em + $lx), "<br />";
            $arr[$i] = ['bj'=>$em ,'lx'=>$lx ,'ze' => ($em + $lx)];
            $dkTotal -= $em;
            $lxTotal = $lxTotal + $lx;
        }
        $result['lxTotal'] = $lxTotal;
        $result['data'] = $arr;
        return $result;
    }

    static public function incomeInterest($money , $month  , $rate){

        $rate = $rate / 12;
        $interest = $money * $month * $rate;
        return $interest;
    }
}