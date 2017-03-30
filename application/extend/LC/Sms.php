<?php
/**
 * Created by PhpStorm.
 * User: lucong
 * Date: 2017/3/28
 * Time: 上午1:02
 */

namespace LC;


use niklaslu\Dayu;

class Sms
{
    static public function formatExtra($extra){

        $data = explode('|' , $extra);
        $result = [];
        foreach ($data as $vo){
            $arr = explode(':' , $vo);
            $result[$arr[0]] = $arr[1];
        }

        return $result;
    }

    static public function sentByTemplateId($templateId , $phone , $params , $config = null , $extend = ''){

        $template = db('SmsTemplate')->find($templateId);
        if (!$template){
            return false;
        }
        $res = self::sentByTemplate($template , $phone , $params , $config  , $extend );
        return $res;

    }

    static public function sentByTemplateName($templateName , $phone , $params , $config = null , $extend = ''){
        $template = db('SmsTemplate')->where('name' , $templateName)->where('status' , 1)->find();
        if (!$template){
            return false;
        }
        $res = self::sentByTemplate($template , $phone , $params , $config  , $extend );
        return $res;
    }

    static public function sentByTemplate($template , $phone , $params , $config = null , $extend = ''){

        if (!$config){
            $config = self::getConfig();
        }

        if ($template['type'] == 1){
            // 阿里大鱼
            $Dayu = new Dayu([
                'appkey' => $config['appkey'],
                'secret' => $config['secret']
            ]);

            $smsData = [
                'extend' => $extend,
                'sms_type' => 'normal',
                'sms_free_sign_name' => $config['sms_sing_name'],
                'sms_param' => $params,
                'rec_num' => $phone,
                'sms_template_code' => $template['id_no']
            ];

            $result = $Dayu->smsSend($smsData);
            if ($result['status'] == 1){
                self::recordDb($template , $phone , $params , $config);
                return true;
            }else {
                log_action(0 , 'sms_error' , $result);
                return false;
            }
        }
    }

    static public function checkCode($templateName , $phone , $code ){
        $template = db('SmsTemplate')->where('name' , $templateName)->where('status' , 1)->find();
        if (!$template){
            return false;
        }

        $map['phone'] = $phone;
        $map['code'] = $code;
        $map['status'] = 0;
        $sms = \app\model\Sms::where($map)->find();
        if ($sms){
            //
            $sms->status = 1;
            $sms->save();
            // 判断是否过期
            $deadline = $sms->deadline;
            if ($deadline < time() && $deadline != 0){

                return false;
            }else{
                return true;
            }
        }else {

            return false;
        }

    }

    static public function recordDb($template  , $phone , $params , $config = null){
        if (!$config){
            $config = self::getConfig();
        }

        $data['template_id'] = $template['id'];
        $data['template_name'] = $template['name'];
        $data['params'] = json_encode($params);
        $data['phone'] = $phone;
        if ($template['is_code'] == 1){
            $data['code'] = $params['code'];
            $data['deadline'] = time() + $config['sms_deadline'] * 60;
        }
        $data['uid'] = session('admin_uid') ? session('admin_uid') : 0;

        $res = \app\model\Sms::create($data);
        return $res;

    }



    static public function getSmsContent($content , $params){
        if (!is_array($params)){
            $params = json_decode($params , true);
        }

        foreach ($params as $k => $v){
            $content = str_replace('${'.$k.'}' , $v , $content);
        }

        return $content;
    }
    static public function getConfig($uid = null){
        if (!$uid){
            $project = session('www_project');
            $uid = $project['uid'];
        }

        $config = cache_get('sms_alidayu_config' , $uid);
        if ($config){
            return $config;
        }else {
            $userConfig = db('UserConfig')->where('uid' , $uid)->find();
            $config = json_decode($userConfig['sms_alidayu'] , true);
            cache_set('sms_alidayu_config' , $config , $uid);
        }
    }
}