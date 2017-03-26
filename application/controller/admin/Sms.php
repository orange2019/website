<?php
/**
 * Created by PhpStorm.
 * User: lucong
 * Date: 2017/3/26
 * Time: 下午10:22
 */

namespace app\controller\admin;


use app\model\UserConfig;
use LC\FormBuilder;
use think\Request;

class Sms extends Admin
{
    public function index(){

        $this->redirect('admin/sms/alidayu');
    }

    public function alidayu(){

        $request = Request::instance();
        $uid = (session('admin_pid') == 0) ? session('admin_uid') : session('admin_pid');
        $Config = new UserConfig();

        if ($request->isPost()){

            $data = $request->post();
            $res = $Config->setExtendConfig($uid , 'sms_alidayu' , $data);
            if ($res){

                cache_clear('sms_alidayu_config' , $uid);
                return $this->formSuccess('设置成功');
            }else {
                return $this->formError('设置失败');
            }

        }else {

            $config = $Config->getExtendConfig($uid , 'sms_alidayu');
            $this->assign('config' , $config);

            $form = FormBuilder::init(['ns'=>'we'])
                ->setFormName('sms-alidayu')
                ->addClass('form-ajax')
                ->setAction($request->url())
                ->addText('appkey' , 'appkey' , $config['appkey'] , '请输入appkey' , true)
                ->addText('secret' , 'secret' , $config['secret'] , '请输入secret' , true)
                ->addText('sms_sing_name' , '短信签名' , $config['sms_sing_name'] , '请输入短信签名' , true)
                ->addNumber( 'sms_deadline' , '短信过期时间'  ,$config['sms_deadline'] , '请输入短信过期时间' , true)
                ->addSubmit('提交')
                ->build();

            $this->assign('form' , $form);
            return $this->fetch();
        }
    }

}