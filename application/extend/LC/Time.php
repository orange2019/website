<?php
/**
 * Created by PhpStorm.
 * User: LC
 * Date: 2017/2/21
 * Time: 10:47
 */

namespace LC;


class Time
{
    /**
     * 修复php加月份bug
     * @param $num  int  增加的月份
     * @param $date int 日期时间戳
     */
    public static function AddMonth($num,$date =null)
    {
        if(!isset($date))
        {
            $date = time();
        }
        $day = date('d',$date);
        $year = date('Y',$date);
        $month = date('m',$date);
        //获取相加月份后的年份
        $year = $year+(($num+$month)>0?(floor(($month+$num)/12)):(floor(($month+$num)/12)-1));
        //获取相加后的月份
        $month = ($num+$month)>0?(($month+$num)%12):(($month+$num)%12+12);
        $month = $month?$month:12;

        //是否闰年
        if($year%400==0 || ($year%4==0 && $year%100!=0))
        {
            $Months = [31,29,31,30,31,30,31,31,30,31,30,31];
        }
        else
        {
            $Months = [31,28,31,30,31,30,31,31,30,31,30,31];
        }

        //当前天数没有超出增加后月份的最大值
        $days = $Months[$month-1];
        if($days>=$day)
        {
            return strtotime($num.' month',$date);
        }
        else
        {
            $month = $month<10?'0'.$month:$month;
            return strtotime($year.$month.$days);
        }
    }

    /**
     * 获取某年某月的天数
     * @param $month
     * @param $year
     * @return int
     */
    public static function DaysInMonth($month,$year)
    {
        return $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year % 400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31);
    }

    public static function LastSecond($time)
    {
        return $time - $time%86400+86399;
    }
}