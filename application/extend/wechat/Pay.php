<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/25
 * Time: 10:05
 */

namespace wechat;
use app\model\FinanceOrder;
use app\model\PayOrderLog;
use think\Request;

/**
 * 微信支付处理类
 * Class Pay
 * @package wechat
 */

class Pay
{

    public $error = '';

    /**
     * 处理微信下单
     * @param $data $bill , title , total_fee , attach
     *
     */
    public function unifiedOrder($openid , $bill , $title , $totalFee , $attach){

        $api = new Api($this->getConfig());
        $orderNo = $bill['id'].'-'.time();
        $deviceInfo = 'WEB';
        $tradeType = 'JSAPI';
        $ip = Request::instance()->ip(0);
        $attach = $this->formatAttach($attach);

        $result = $api->payUnifiedOrder($openid ,$title  , $orderNo , $totalFee , $attach , $ip ,$deviceInfo , $tradeType );
//        return $result;

        if ($result['return_code'] == 'SUCCESS'){

            if ($result['result_code'] == 'SUCCESS'){
                $status = 1;
                $msg = '';

            }else{
                $status = 0;
                $msg = $result['err_code'] . '-' . $result['err_code_des'];

            }

        }else{
            $returnMsg = $result['return_msg'];

            $status = 0;
            $msg = $returnMsg;
        }


        $data = $result;
        $data['out_trade_no'] = $orderNo;
        $data['title'] = $title;
        $data['bill_id'] = $bill['id'];
        $data['total_fee'] = $totalFee;
        $data['attach'] = $attach;

        $this->saveLog($data , $status , $msg );

        $this->error = $msg;
        if ($status == 1){

            $prepay_id = $data['prepay_id'];
            // 签名
            $sign = $api->getPaySignPackage($prepay_id);
            $result['sign_data'] = $sign;
            return $result;
        }else{
            return false;
        }

    }

    /**
     * 处理微信回调
     */
    public function notifyData($xml){

        $data = $this->xmlToArr($xml);
        $data['attach'] = $this->unFormatAttach($data['attach']);

        return $data;

    }
    /**
     * @param $attach
     * @return array
     */
    public function unFormatAttach($attach){

        $arr = explode('&' , $attach);
        $result = [];
        foreach ($arr as $v){

            $val = explode('=' , $v);
            $result[$val[0]] = isset($val[1]) ? $val['1'] : '';
        }

        return $result;
    }

    /**
     * 创建xml字符串
     * @param $data
     */
    public function createXmlData($data){

        $str = '<xml>';
        foreach ($data as $k=>$v){
            $str .= '<'.$k.'>'.$v.'</'.$k.'>';
        }
        $str .= '</xml>';

        return $str;
    }

    /**
     * 解析xml数据
     * @param $xml
     * @return mixed
     */
    public function xmlToArr($xml){

        $p = xml_parser_create();
        xml_parse_into_struct($p , $xml , $data , $index);
        xml_parser_free($p);
        $result = [];

        foreach($index as $key=>$value)
        {
            if (isset($data[$value[0]]['value'])){
                $result[strtolower($key)]=$data[$value[0]]['value'];
            }

        }

        return $result;
    }
    /**
     * @param $attach
     * @return string
     */
    protected function formatAttach($attach){

        $arr = [];
        foreach ($attach as $k=>$v){
            $arr[] = $k . '=' .$v;
        }
        return implode('&' , $arr);
    }

    /**
     * 保存记录
     * @param string $type
     */
    public function saveLog($log , $status , $msg , $type = 'wx'){

        $data['member_id'] = session('www_uid') ? session('www_uid') : 0;
        $data['type'] = $type;
        $data['bill_id'] = $log['bill_id'];
        $data['log'] = json_encode($log);
        $data['datetime'] = time();
        $data['status'] = $status;
        $data['msg'] = $msg;

        $res = PayOrderLog::create($data);
        return$res;
    }

    protected function getConfig(){

        $request = Request::instance();
        $config = config('service.wechat');

        $configWx['appid'] = $config['app_id'];
        $configWx['appsecret'] = $config['app_secret'];
        $configWx['redirect_uri'] = $request->url(true);
        $configWx['mch_id'] = $config['mch_id'];
        $configWx['pay_notify_url'] = $request->domain() . $config['pay_notify_url'];
        $configWx['pay_api_secret'] = $config['pay_api_secret'];
        $configWx['access_token_type'] = $config['access_token_type'];

        return $configWx;
    }
}