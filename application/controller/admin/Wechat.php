<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/24
 * Time: 15:50
 */

namespace app\controller\admin;


use app\model\UserConfig;
use LC\FormBuilder;
use think\Request;

class Wechat extends Admin
{

    public function index(){

        $uid = (session('admin_pid') == 0) ? session('admin_uid') : session('admin_pid');
        $Config = new UserConfig();

        $config = $Config->getExtendConfig($uid , 'wechat');

        $this->assign('config' , $config);

        return $this->fetch();
//        dump($config);
    }

    public function config(){

        $request = Request::instance();
        $uid = (session('admin_pid') == 0) ? session('admin_uid') : session('admin_pid');
        $Config = new UserConfig();

        if ($request->isPost()){

            $data = $request->post();
            $res = $Config->setExtendConfig($uid , 'wechat' , $data);
            if ($res){

                cache_clear('wechat_config_data' , $uid);
                return $this->formSuccess('设置成功' , url('admin/wechat/index'));
            }else {
                return $this->formError('设置失败');
            }

        }else {


            $config = $Config->getExtendConfig($uid , 'wechat');
            $this->assign('config' , $config);

            $form = FormBuilder::init(['ns'=>'we'])
                ->setFormName('wechat-config')
                ->addClass('form-ajax')
                ->setAction($request->url())
                ->addText('name' , '公众号名称' , $config['name'] , '请输入公众号名称' , true)
                ->addText('app_id' , 'AppID' , $config['app_id'] , '请输入公众号app_id' , true)
                ->addText('app_secret' , 'AppSecret' , $config['app_secret'] , '请输入公众号app_secret' , true)
                ->addText('mch_id' , '商户号Id' , $config['mch_id'] , '请输入支付商户号Id' , false , '无开通支付权限可不填')
                ->addText('pay_notify_url' ,'支付回调url' , $config['pay_notify_url'] , '支付回调url' )
                ->addText('pay_api_secret' ,'支付api_secret' , $config['pay_api_secret'] , '支付api_secret' )
                ->addHidden('access_token_type' , '' , 'db' )
                ->addSubmit('提交')
                ->build();

            $this->assign('form' , $form);
            return $this->fetch();
        }
    }

}