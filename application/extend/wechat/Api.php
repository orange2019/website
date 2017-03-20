<?php
namespace wechat;
use app\model\WxToken;
use think\Cache;
use think\Request;
class Api {

    public $version = '1.0.2';

    //appID
    private $appid = '';

    // 商户号
    private $mch_id = '';

    // 支付回调地址
    private $pay_notyfy_url = '';

    // 支付key
    private $pay_api_secret = '';


    //appsecret
    private $appsecret = '';

    //access_token
    private $access_token = '';

    private $access_token_type = 'cache';

    //授权回调地址
    private $redirect_uri = '';

    const OPEN_URI = 'https://open.weixin.qq.com/';
    const API_URI = 'https://api.weixin.qq.com/';

    const ACCESS_TOKEN_URI = 'cgi-bin/token';
    const IP_LIST_URI = 'cgi-bin/getcallbackip';
    const USER_INFO_URI = 'cgi-bin/user/info';
    const USER_LIST_URI = 'cgi-bin/user/get';
    const USER_LIST_INFO_URI = 'cgi-bin/user/info/batchget';


    const MENU_INFO = 'cgi-bin/get_current_selfmenu_info';
    const MENU_CREATE = 'cgi-bin/menu/create';
    const TEMPLATE_MESSAGE_SEND = 'cgi-bin/message/template/send';

    const AUTH_URI = 'connect/oauth2/authorize';
    const AUTH_ACCESS_TOKEN_URI = 'sns/oauth2/access_token';
    const AUTH_USER_URI = 'sns/userinfo';

    const SNSAPI_USERINFO = 'snsapi_userinfo';
    const SNSAPI_BASE = 'snsapi_base';

    // jssdk
    const JSSDK_URI = 'https://res.wx.qq.com/open/js/jweixin-1.0.0.js';
    const GET_TICKET = 'cgi-bin/ticket/getticket';

    // 微信支付
    // 统一下单接口
    const PAY_UNIfIED_ORDER = 'https://api.mch.weixin.qq.com/pay/unifiedorder';


    public $error = '';
    /**
     * 传入微信配置
     * @param array $config
     */
    public function __construct($config){

        $this->appid = $config['appid'];
        $this->appsecret = $config['appsecret'];
        $this->redirect_uri = $config['redirect_uri'];

        $this->access_token_type = isset($config['access_token_type']) ? $config['access_token_type'] : $this->access_token_type;
        $this->access_token = $this->get_access_token();

        $this->mch_id = $config['mch_id'];
        $this->pay_notyfy_url = $config['pay_notify_url'];
        $this->pay_api_secret = $config['pay_api_secret'];


    }

    /**
     * 返回access_token的值
     * @return  <string, boolean, mixed, unknown>
     */
    public function return_access_token(){

        return $this->access_token;
    }

    public function return_access_token_type(){

        return $this->access_token_type;
    }
    /**
     * 获取access_token
     */
    private function get_access_token(){

        $data = $this->getCacheAccessToken();

        if ($data){
            //从缓存中取
            $access_token = $data['access_token'];
            return $access_token;
        }else {
            $url = self::API_URI . self::ACCESS_TOKEN_URI;

            $param['appid'] = $this->appid;
            $param['secret'] = $this->appsecret;
            $param['grant_type'] = 'client_credential';

            $access_token_url = $this->create_url($url, $param);

            $data = $this->http_get($access_token_url);
            $data = $this->return_data($data);
            if ($data){
                $access_token = $data['access_token'];
                $this->setCacheAccessToken($data);
                return $access_token;
            }else{
                $this->clearCacheAccessToken();
                return false;
            }
        }


    }

    /**
     * 获取微信服务器的ip列表
     * @param string $access_token
     */
    public function get_ip_list(){

        $url = self::API_URI . self::IP_LIST_URI;
        $param['access_token'] = $this->access_token;

        $ip_list_url = $this->create_url($url, $param);

        $data = $this->http_get($ip_list_url);

        return $this->return_data($data);
    }

    /**
     * 生成授权url
     */
    public function get_auth_url($scope = ''){

        $url = self::OPEN_URI . self::AUTH_URI;

        $param['appid'] = $this->appid;
        $param['redirect_uri'] = $this->redirect_uri;
        $param['response_type'] = 'code';
        $param['scope'] = $scope ? $scope : self::SNSAPI_USERINFO;
        $param['state'] = '1';
        $auth_url = $this->create_url($url, $param);
        $auth_url .= '#wechat_redirect';

        return $auth_url;
    }

    /**
     * 获取通过code获得access_token的链接
     * @param string $code
     * @return string
     */
    public function get_auth_access_token($code){

        $url = self::API_URI . self::AUTH_ACCESS_TOKEN_URI;

        $param['appid'] = $this->appid;
        $param['secret'] = $this->appsecret;
        $param['code'] = $code;
        $param['grant_type'] = 'authorization_code';

        $access_token_url = $this->create_url($url, $param);
        $data = $this->http_get($access_token_url);

        return $this->return_data($data);
    }

    /**
     * 生成签名包
     * @param null $url
     * @return array
     */
    public function getSignPackage($url = null){

        $data['jsapi_ticket'] = $this->getJsApiTicket();
        $data['noncestr'] = $nonceStr = $this->createNonceStr();
        $data['timestamp'] = $timestamp = time();
        if (!$url){
            $url = Request::instance()->url(true);
        }
        $data['url'] = $url;

        $signatureStr = $this->getSignnatureStr($data);
        $signature = sha1($signatureStr);

        $result = [
            "appId"     => $this->appid,
            "nonceStr"  => $nonceStr,
            "timestamp" => $timestamp,
            "url"       => $url,
            "signature" => $signature,
            "rawString" => $signatureStr
        ];

        return $result;
    }

    /**
     * 微信支付
     * @param $prepay_id
     * @return mixed
     */
    public function getPaySignPackage($prepay_id){

        $data['appId'] = $this->appid;
        $data['timeStamp'] = strval(time());
        $data['nonceStr'] = $this->createNonceStr();
        $data['package'] = 'prepay_id=' . $prepay_id;
        $data['signType'] = 'MD5';

        $paySign = $this->getSignnatureStr($data);
        $paySign .= '&key='.$this->pay_api_secret;
        $data['paySign'] = strtoupper(md5($paySign));

        return $data;
    }

    /**
     * 生成 签名字符串
     */
    public function getSignnatureStr($data){

        ksort($data);
        foreach ($data as $k=>$v){
            $arr[] = $k . '=' . $v;
        }
        $str = implode('&', $arr);
        return $str;
    }

    /**
     * 获取随机字符串
     * @param int $length
     * @return string
     */
    public function createNonceStr($length = 16) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    /**
     * 获取js api ticket
     * @return bool
     */
    public function getJsApiTicket(){

        // 先从缓存中取
        $data = cache('js_api_ticket');
        if ($data && $data['deadline'] > time()){
            $ticket = $data['ticket'];
            return $ticket;
        }else {
            $url = self::API_URI .self::GET_TICKET ;
            $param['access_token'] = $this->access_token;
            $param['type'] = 'jsapi';

            $url = $this->create_url($url, $param);

            $data = $this->http_get($url);
            $data = $this->return_data($data);
            if ($data){
                $ticket = $data['ticket'];
                $data['deadline'] = time() + 7100;
                cache('js_api_ticket' , $data);

                return $ticket;
            }else {
                return false;
            }
        }


    }

    /**
     * 统一下单接口
     * @param $body
     * @param $orderNo
     * @param $totalFee
     * @param $attach
     * @param $ip
     * @param string $deviceInfo
     * @param string $tradeType
     */
    public function payUnifiedOrder($openid , $title  , $orderNo , $totalFee , $attach , $ip ,$deviceInfo = 'WEB' , $tradeType = 'JSAPI'){

        $order['appid'] = $this->appid;
        $order['mch_id'] = $this->mch_id;
        $order['device_info'] = $deviceInfo;
        $order['nonce_str'] = $this->createNonceStr();
        $order['body'] = is_array($title) ? implode('-' , $title) : $title;
        $order['attach'] = $attach;
        $order['out_trade_no'] = $orderNo;
        $order['total_fee'] = $totalFee;
        $order['spbill_create_ip'] = $ip;
        $order['notify_url'] = $this->pay_notyfy_url;
        $order['trade_type'] = $tradeType;
        $order['openid'] = $openid;


        $sign = $this->getSignnatureStr($order);
        $sign .= '&key='.$this->pay_api_secret;
        $order['sign'] = strtoupper(md5($sign));

        $xml = $this->createXmlData($order);
        $url = self::PAY_UNIfIED_ORDER;
        $result = $this->http_post($url , $xml , 'xml');

        return $result;

    }


    /**
     * 获取用户信息
     * @param string $openid
     * @param string $lang
     * @return Ambigous <\Org\Com\boolean, \Org\Com\unknown>
     */
    public function get_user_info($openid , $lang = 'zh_CN'){

        $url = self::API_URI . self::USER_INFO_URI;
        $param['access_token'] = $this->access_token;
        $param['openid'] = $openid;
        $param['lang'] = $lang;

        $user_info_url = $this->create_url($url, $param);
        $data = $this->http_get($user_info_url);

        return $this->return_data($data);
    }

    /**
     * 获取用户列表信息
     * @param unknown $data
     * @return boolean|
     */
    public function get_user_list_info($data){

        $url = self::API_URI . self::USER_LIST_INFO_URI;
        $param['access_token'] = $this->access_token;

        $user_list_url = $this->create_url($url, $param);

        $postData['user_list'] = $data;
        $postData = json_encode($postData,true);

        $data = $this->http_post($user_list_url, $postData);

        return $this->return_data($data);
    }
    /**
     * 获取用户列表
     */
    public function get_user_list($next = ''){

        $url = self::API_URI . self::USER_LIST_URI;
        $param['access_token'] = $this->access_token;
        $param['next_openid'] = $next;


        $user_list_url = $this->create_url($url, $param);
        $data = $this->http_get($user_list_url);

        return $this->return_data($data);
    }

    /**
     * 网页授权获取用户信息
     * @param string $openid
     * @param string $access_token
     * @param string $lang
     * @return Ambigous <\Org\Com\boolean, \Org\Com\unknown>
     */
    public function get_auth_user($openid , $access_token , $lang = 'zh_CN'){

        $url = self::API_URI . self::AUTH_USER_URI;
        $param['access_token'] = $access_token;
        $param['openid'] = $openid;
        $param['lang'] = $lang;

        $user_info_url = $this->create_url($url, $param);
        $data = $this->http_get($user_info_url);

        return $this->return_data($data);
    }

    /**
     * 查询自定义菜单
     * @return boolean
     */
    function get_menu_info(){

        $url = self::API_URI . self::MENU_INFO;
        $param['access_token'] = $this->access_token;
        $menu_url = $this->create_url($url, $param);

        $data = $this->http_get($menu_url);

        return $this->return_data($data);
    }

    /**
     * 创建自定义菜单
     * @param unknown $menu
     * @return boolean|\niklaslu\unknown
     */
    function create_menu($menu){

        $url = self::API_URI . self::MENU_CREATE;
        $param['access_token'] = $this->access_token;
        $menu_url = $this->create_url($url, $param);

        $fields = self::json_encode($menu);
        $data = $this->http_post($menu_url, $fields);
        return $this->return_data($data);

    }

    /**
     * 发送模板信息
     * @param unknown $openid
     * @param unknown $template_id
     * @param unknown $template_url
     * @param unknown $datas
     * @return boolean|\niklaslu\unknown
     */
    public function send_template_msg($openid , $template_id , $template_url , $datas = NULL){

        $url = self::API_URI . self::TEMPLATE_MESSAGE_SEND;
        $param['access_token'] = $this->get_access_token();

        $template_send_url = $this->create_url($url, $param);

        $fields['touser'] = $openid;
        $fields['template_id'] = $template_id;
        $fields['url'] = $template_url;
        $data = array();
        foreach ($datas as $k=>$v){
            $d['value'] = $v;
            $d['color'] = '#173177';

            $data[$k] = $d;
        }
        if ($data){
            $fields['data'] = $data;
        }

        $fields = json_encode($fields , true);
        $res = $this->http_post($template_send_url, $fields);

        return $this->return_data($res);
    }
    /**
     * 返回data
     * @param array $data
     * @return boolean|unknown
     */
    public function return_data($data){

        if (isset($data['errcode']) && $data['errcode'] > 0){
            $this->error = $data['errmsg'];
            return false;
        }else{
            return $data;
        }
    }

    /**
     * 获取错误
     * @return string|array
     */
    public function get_error(){

        return $this->error;
    }
    /**
     * 生成url
     * @param string $url
     * @param 参数 $param
     */
    public function create_url($url , $param){

        $url .= "?";
        $i = 0;
        foreach ($param as $k=>$v){
            $i++;
            if ($i == count($param)){
                $url .= $k . '=' . $v;
            }else{
                $url .= $k . '=' . $v . '&';
            }

        }

        return $url;
    }

    /**
     * http curl get
     * @param string $url
     * @param string $data_type
     * @return mixed|boolean
     */
    public function http_get($url, $data_type='json') {

        $cl = curl_init();
        if(stripos($url, 'https://') !== FALSE) {
            curl_setopt($cl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($cl, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($cl, CURLOPT_SSLVERSION, 1);
        }
        curl_setopt($cl, CURLOPT_URL, $url);
        curl_setopt($cl, CURLOPT_RETURNTRANSFER, 1 );
        $content = curl_exec($cl);
        $status = curl_getinfo($cl);
        curl_close($cl);
        if (isset($status['http_code']) && $status['http_code'] == 200) {
            if ($data_type == 'json') {
                $content = json_decode($content , true);
            }
            if ($data_type == 'xml'){
                $content = $this->xmlToArr($content);
            }
            return $content;
        } else {
            return FALSE;
        }
    }

    /**
     * http curl post
     * @param string $url
     * @param unknown $fields
     * @param string $data_type
     * @return mixed|boolean
     */
    public function http_post($url, $fields, $data_type='json') {

        $cl = curl_init();
        if(stripos($url, 'https://') !== FALSE) {
            curl_setopt($cl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($cl, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($cl, CURLOPT_SSLVERSION, 1);
        }
        curl_setopt($cl, CURLOPT_URL, $url);
        curl_setopt($cl, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt($cl, CURLOPT_POST, true);
        // convert @ prefixed file names to CurlFile class
        // since @ prefix is deprecated as of PHP 5.6
        //         if (class_exists('\CURLFile')) {
        //             foreach ($fields as $k => $v) {
        //                 if (strpos($v, '@') === 0) {
        //                     $v = ltrim($v, '@');
        //                     $fields[$k] = new \CURLFile($v);
        //                 }
        //             }
        //         }
        curl_setopt($cl, CURLOPT_POSTFIELDS, $fields);
        $content = curl_exec($cl);
        $status = curl_getinfo($cl);
        curl_close($cl);
        if (isset($status['http_code']) && $status['http_code'] == 200) {
            if ($data_type == 'json') {
                $content = json_decode($content ,true);
            }
            if ($data_type == 'xml'){
                $content = $this->xmlToArr($content);
            }
            return $content;
        } else {
            return FALSE;
        }
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
     * 微信api不支持中文转义的json结构
     * @param array $arr
     */
    static function json_encode($arr) {
        if (count($arr) == 0) return "[]";
        $parts = array ();
        $is_list = false;
        //Find out if the given array is a numerical array
        $keys = array_keys ( $arr );
        $max_length = count ( $arr ) - 1;
        if (($keys [0] === 0) && ($keys [$max_length] === $max_length )) { //See if the first key is 0 and last key is length - 1
            $is_list = true;
            for($i = 0; $i < count ( $keys ); $i ++) { //See if each key correspondes to its position
                if ($i != $keys [$i]) { //A key fails at position check.
                    $is_list = false; //It is an associative array.
                    break;
                }
            }
        }
        foreach ( $arr as $key => $value ) {
            if (is_array ( $value )) { //Custom handling for arrays
                if ($is_list)
                    $parts [] = self::json_encode ( $value ); /* :RECURSION: */
                else
                    $parts [] = '"' . $key . '":' . self::json_encode ( $value ); /* :RECURSION: */
            } else {
                $str = '';
                if (! $is_list)
                    $str = '"' . $key . '":';
                //Custom handling for multiple data types
                if (!is_string ( $value ) && is_numeric ( $value ) && $value<2000000000)
                    $str .= $value; //Numbers
                elseif ($value === false)
                    $str .= 'false'; //The booleans
                elseif ($value === true)
                    $str .= 'true';
                else
                    $str .= '"' . addslashes ( $value ) . '"'; //All other things
                // :TODO: Is there any more datatype we should be in the lookout for? (Object?)
                $parts [] = $str;
            }
        }
        $json = implode ( ',', $parts );
        if ($is_list)
            return '[' . $json . ']'; //Return numerical JSON
        return '{' . $json . '}'; //Return associative JSON
    }

    public function getCacheAccessToken(){


//         $file = dirname(__FILE__).'/../../../../access_token.json';
//         $accessTokenData = file_get_contents($file);
        $type = $this->access_token_type;

        if ($type == 'cache'){
            $appId = $this->appid;
            $data = Cache::get('access_token_info');
            $accessTokenData = (isset($data[$appId]) && $data[$appId]) ? $data[$appId] : null;
            if (!$accessTokenData){
                return false;
            }else{
                $accessTokenData = $accessTokenData ? json_decode($accessTokenData , true) : '';
                if ($accessTokenData && isset($accessTokenData['access_token'])){
                    if (isset($accessTokenData['time_out']) && $accessTokenData['time_out'] > time()){
                        return $accessTokenData;
                    }else{
                        return false;
                    }

                }else{
                    return false;
                }
            }
        }elseif ($type == 'db'){

            $map['appid'] = $this->appid;
            $map['type'] = 'access_token';

            $token = db('WxToken')->where($map)->find();
            if ($token && isset($token['access_token'])){
                if (isset($token['time_out']) && $token['time_out'] > time()){
                    return $token;
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }

    }

    public function setCacheAccessToken($data){

//         $file = dirname(__FILE__).'/../../../../access_token.json';
        $data['expires_in'] = $data['expires_in'] - 100;
        $data['time_out'] = time() + $data['expires_in'];

        $type = $this->access_token_type;
        if ($type == 'cache'){
            $appid = $this->appid;
            $accessTokenData = json_encode($data , true);

            $dataToken[$appid] = $accessTokenData;
            Cache::set('access_token_info', $dataToken);
        }elseif ($type == 'db'){

            $map['appid'] = $this->appid;
            $map['type'] = 'access_token';

            $token = WxToken::where($map)->find();
            if (isset($token->id)){
                $token->access_token = $data['access_token'];
                $token->time_out = $data['time_out'];

                $token->save();
            }else{
                $saveData['appid'] = $this->appid;
                $saveData['access_token'] = $data['access_token'];
                $saveData['time_out'] = $data['time_out'];
                $saveData['type'] = 'access_token';

                WxToken::create($saveData);
            }
        }else{
            return false;
        }

//         file_put_contents($file, $accessTokenData);
    }

    public function clearCacheAccessToken(){

        $type = $this->access_token_type;
        if ($type == 'cache'){
            $data = Cache::get('access_token_info');
            $appid = $this->appid;
            $data[$appid] = null;
            Cache::set('access_token_info', $data);
        }elseif ($type == 'db'){

            $map['appid'] = $this->appid;
            $map['type'] = 'access_token';

            $token = WxToken::where($map)->find();
            $token->access_token = '';
            $token->time_out = 0;
            $token->save();
        }else {
            return false;
        }

//         $file = dirname(__FILE__).'/../../../../access_token.json';
//         file_put_contents($file, '');
    }

    public function clearCacheJsApiTicket(){

        cache('js_api_ticket' , null);
    }


}