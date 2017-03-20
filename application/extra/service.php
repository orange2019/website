<?php
return [
    'wechat' => [

        'app_id' => 'wx8d46158d0a0b5f26',
        'app_secret' => 'ae12a206a30d5061d433758bafcbe877',
        'redirect_uri' =>'',
        'mch_id' => '1445475902',
        'pay_notify_url' => '/pay/wxNotify',
        'pay_api_secret' => 'e6d91ce13d421863e67cda73a79b158e',
        'access_token_type' => 'db', // db:数据库 cache 缓存
    ],
    'mail' =>  [
        'debug' => false, // 是否开启debug,调试模式下可开启
        'host' => 'smtp.exmail.qq.com', // 邮件发送smtp服务器host
        'username' => 'niklaslu@warmjar.com', // 用户名
        'password' => 'warm88', // 密码
        'port' => 465, // 端口号
        // 发送人
        'from' =>[
            'address' => 'niklaslu@warmjar.com',
            'name' => '云管家'
        ],
    
    ],
    'sms' =>  [
        'appkey' => '23623781',
        'secret' => '48677c8acb6f5c7f38bb25f258c860e4'
    ],
    // 短信签名
    'sms_sing_name' => 'ORANGE橙子',
    // 短信过期时间
    'sms_deadline' => 3
];