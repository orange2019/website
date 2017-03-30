<?php
/**
 * Created by PhpStorm.
 * User: lucong
 * Date: 2017/3/26
 * Time: 下午10:56
 */

namespace app\controller\admin;


use app\model\EmailTemplate;
use app\model\UserConfig;
use LC\FormBuilder;
use think\Request;

class Email extends Admin
{
    public function index(){
        $uid = (session('admin_pid') == 0) ? session('admin_uid') : session('admin_pid');
        $map['uid'] = $uid;
        $map['status'] = ['>=' , 0];
        $query = [];
        $list = db('EmailTemplate')->where($map)->order('create_time desc')->paginate(20 , false , ['query'=>$query]);
        $page = $list->render();

        $this->assign('list' , $list);
        $this->assign('page' , $page);
        return $this->fetch();
    }

    public function templateUpdate(){

        $request = Request::instance();
        $uid = (session('admin_pid') == 0) ? session('admin_uid') : session('admin_pid');
        if ($request->isPost()){

            $data = $request->post();
            // 检测重复
            $map['name'] = $data['name'];
            if ($data['id']){
                $map['id'] = ['<>' , $data['id']];
            }
            $count = db('EmailTemplate')->where($map)->count();
            if ($count > 0){
                return $this->formError('存在相同标识的模板');
            }
            if ($data['id']){
                $res = EmailTemplate::update($data);
            }else {
                $data['uid'] = $uid;
                $res = EmailTemplate::create($data);
            }
            if ($res){

                return $this->formSuccess('设置成功', url('admin/email/index'));
            }else {
                return $this->formError('设置失败');
            }

        }else {

            $id = input('id' , 0);
            if ($id){
                $data = db('EmailTemplate')->find($id);
            }else{
                $data = null;
            }

            $form = FormBuilder::init(['ns'=>'we'])
                ->setFormName('email-template-update')
                ->addClass('form-ajax')
                ->setAction($request->url())
                ->addText('title' , '邮件模板标题' , $data['title'] , '请输入模板标题' , true)
                ->addText('name' , '邮件模板标识' , $data['name'] , '请输入模板标识' , true)
                ->addSelect('type' , '模板类型' , $data['type'] , config('base.email_template_type') , true)
                ->addRadio('is_code' , '是否验证码（默认：否）' , $data['is_code'] , ['0'=>'否' , 1=> '是'])
                ->addTextarea( 'content' , '模板内容'  ,$data['content'] , '请输入邮件模板内容' , true)
                ->addTextarea('extra' , '模板参数配置' , $data['extra'] , '请输入参数配置' , false , false , '配置格式 key:value|key:value')
                ->addHidden('id' , '' , $data['id'])
                ->addSubmit('提交')
                ->build();

            $this->assign('form' , $form);
            return $this->fetch();
        }
    }

    public function templateStatus(){
        $id = input('id' , 0);
        $status = input('status');
        if ($id){
            $template = EmailTemplate::get($id);
            $template->status = $status;
            $res = $template->save();
            if ($res){
                return $this->formSuccess('操作成功');
            }else {
                return $this->formError('操作失败');
            }
        }else {
            return $this->formError('无效数据');
        }
    }

    public function templateDelete(){
        $id = input('id' , 0);
        if ($id){
            $template = EmailTemplate::get($id);
            $template->status = -1;
            $res = $template->save();
            if ($res){
                return $this->formSuccess('操作成功');
            }else {
                return $this->formError('操作失败');
            }
        }else {
            return $this->formError('无效数据');
        }
    }

    public function lists(){
        $templateId = input('template_id');
        $map['template_id'] = $templateId;
        $query = [];
        $list = db('email')->where($map)->order('create_time desc')->paginate(20 , false , ['query' , $query]);
        $page = $list->render();

        $this->assign('list' , $list);
        $this->assign('page' , $page);

        $template = db('EmailTemplate')->find($templateId);
        $this->assign('template' , $template);
        return $this->fetch();
    }

    public function sent(){

        $uid = (session('admin_pid') == 0) ? session('admin_uid') : session('admin_pid');
        $request = Request::instance();
        if ($request->isPost()){
            $data = $request->post();
            $email = $data['email'];
            $templateId = $data['template_id'];
            $params = $data['params'];

            $config = \LC\Email::getConfig($uid);
            $res = \LC\Email::sentByTemplateId($templateId , $email , $params , $config);
            if ($res){
                return $this->formSuccess('操作成功');
            }else {
                return $this->formError('操作失败');
            }

        }else {

            $id = input('id' , 0);
            $template = db('EmailTemplate')->find($id);
            $this->assign('template' , $template);

            $form = FormBuilder::init(['ns'=>'we'])
                ->setFormName('email-sent')
                ->addClass('form-ajax')
                ->setAction($request->url())
                ->addEmail('email' , '邮箱地址' , '' , '请输入邮箱地址' , true);
            if ($template['extra']){
                $params = \LC\Email::formatExtra($template['extra']);
                foreach ($params as $k=>$v){
                    $form = $form->addText('params['.$k.']' , $v , '' , $v , true);
                }
            }
            $form = $form->addHidden('template_id' , '' , $template['id'])
                ->addSubmit('提交')->build();

            $this->assign('form' , $form);
            return $this->fetch();
        }
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
                return $this->formSuccess('设置成功' , url('admin/email/index'));
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
                ->addEmail('from_address', '发件人地址'  ,$config['from_address'] , '发件人地址' , true )
                ->addText('from_name', '发件人名称'  ,$config['from_name'] , '发件人名称' , true)
                ->addSubmit('提交')
                ->build();

            $this->assign('form' , $form);
            return $this->fetch();
        }
    }
}