<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/6
 * Time: 12:00
 */
return [

    'type' => [
        1 => '钱包',
//        2 => '微信',
//        3 => '支付宝'
    ],
    'log_type' => [
        'in' => '充值',
        'out' => '提现',
        'raise' => '出借',
        'raise_back' => '出借返回',
        'raise_share' => '出借结算',
        'loan'  => '发放贷款',
        'pay_p2p' => 'p2p还款',
        'pay_p2p_auto' => 'p2p自动还款'

    ]

];