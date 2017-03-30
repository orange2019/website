<?php
/**
 * Created by PhpStorm.
 * User: lucong
 * Date: 2017/3/30
 * Time: 下午9:25
 */

namespace LC;


use niklaslu\Mail;

class Email
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

    static public function sentByTemplateId($templateId , $email , $params , $config = null , $extend = []){

        $template = db('EmailTemplate')->find($templateId);
        if (!$template){
            return false;
        }
        $res = self::sentByTemplate($template , $email , $params , $config  , $extend );
        return $res;

    }

    static public function sentByTemplateName($templateName , $email , $params , $config = null , $extend = []){
        $template = db('EmailTemplate')->where('name' , $templateName)->where('status' , 1)->find();
        if (!$template){
            return false;
        }
        $res = self::sentByTemplate($template , $email , $params , $config  , $extend );
        return $res;
    }

    static public function sentByTemplate($template , $email , $params , $config = null , $extend = []){

        if (!$config){
            $config = self::getConfig();
        }

        $mailConfig = [
            'debug' => false,
            'host' => $config['host'],
            'username' => $config['username'],
            'password' => $config['password'],
            'port' => $config['port'],
            'from' =>[
                'address' => $config['from_address'],
                'name' => $config['from_name']
            ]

        ];

        $to = $email;
        $subject = $template['title'];
        $body = self::getEmailContent($template['content'] , $params);

        $Mail = new Mail($mailConfig);

        $res = $Mail->sent($to , $subject , $body);
        if ($res){
            self::recordDb($template , $email , $subject ,$params , $extend);
            return true;
        }else {
            $error['email'] = $email;
            $error['msg'] = $Mail->getError();
            log_action( 0 , 'email_error' , $error);
            return false;
        }

    }

    static public function checkCode($templateName , $email , $code ){
        $template = db('SmsTemplate')->where('name' , $templateName)->where('status' , 1)->find();
        if (!$template){
            return false;
        }

        $map['email'] = $email;
        $map['code'] = $code;
        $map['status'] = 0;
        $mail = \app\model\Email::where($map)->find();
        if ($mail){
            //
            $mail->status = 1;
            $mail->save();
            // 判断是否过期
            $deadline = $mail->deadline;
            if ($deadline < time() && $deadline != 0){

                return false;
            }else{
                return true;
            }
        }else {

            return false;
        }

    }

    static public function recordDb($template  , $email , $subject , $params , $extend = []){

        $data['template_id'] = $template['id'];
        $data['template_name'] = $template['name'];
        $data['subject'] = $subject;
        $data['params'] = json_encode($params);
        $data['email'] = $email;
        $data['extend'] = $extend ? json_encode($extend) : '';
        if ($template['is_code'] == 1){
            $data['code'] = $params['code'];
            $data['deadline'] = 0;
        }
        $data['uid'] = session('admin_uid') ? session('admin_uid') : 0;

        $res = \app\model\Email::create($data);
        return $res;

    }

    static public function getEmailContent($content , $params){
        if (!is_array($params)){
            $params = json_decode($params , true);
        }

        foreach ($params as $k => $v){
            $content = str_replace('{$'.$k.'}' , $v , $content);
        }

        return $content;
    }
    static public function getConfig($uid = null){
        if (!$uid){
            $project = session('www_project');
            $uid = $project['uid'];
        }

        $config = cache_get('email_config_data' , $uid);
        if ($config){
            return $config;
        }else {
            $userConfig = db('UserConfig')->where('uid' , $uid)->find();
            $config = json_decode($userConfig['email'] , true);
            cache_set('email_config_data' , $config , $uid);
        }
    }


}