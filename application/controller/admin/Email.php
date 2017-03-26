<?php
/**
 * Created by PhpStorm.
 * User: lucong
 * Date: 2017/3/26
 * Time: 下午10:56
 */

namespace app\controller\admin;


use app\model\UserConfig;
use LC\FormBuilder;
use think\Request;

class Email extends Admin
{
    public function index(){
        $this->redirect('admin/email/config');
    }

    public function config(){

        $request = Request::instance();
        $uid = (session('admin_pid') == 0) ? session('admin_uid') : session('admin_pid');
        $Config = new UserConfig();

        if ($request->isPost()){

            $data = $request->post();
            $res = $Config->setExtendConfig($uid , 'email' , $data);
            if ($res){

                cache_clear('email_config_data' , $uid);
                return $this->formSuccess('设置成功');
            }else {
                return $this->formError('设置失败');
            }

        }else {

            $config = $Config->getExtendConfig($uid , 'email');
            $this->assign('config' , $config);

            $form = FormBuilder::init(['ns'=>'we'])
                ->setFormName('email-config')
                ->addClass('form-ajax')
                ->setAction($request->url())
                ->addSelect('debug' , '是否开启调试模式' , $config['debug'] , [1=>'是',0=>'否'] , true)
                ->addText('host' , 'smtp服务器host' , $config['host'] , 'smtp服务器host' , true)
                ->addText('username' , '用户名' , $config['username'] , '请输入用户名' , true)
                ->addText('password' , '密码'  ,$config['password'] , '请输入密码' , true)
                ->addNumber('port', '端口号'  ,$config['port'] , '请输入端口号' , true)
                ->addEmail('form_address', '发件人地址'  ,$config['form_address'] , '发件人地址' , true )
                ->addText('from_name', '发件人名称'  ,$config['from_name'] , '发件人名称' , true)
                ->addSubmit('提交')
                ->build();

            $this->assign('form' , $form);
            return $this->fetch();
        }
    }
}