<?php
/**
 * Created by PhpStorm.
 * User: lucong
 * Date: 2017/3/26
 * Time: 下午10:22
 */

namespace app\controller\admin;


use app\model\SmsTemplate;
use app\model\UserConfig;
use LC\FormBuilder;
use think\Request;

class Sms extends Admin
{
    public function index(){

        $uid = (session('admin_pid') == 0) ? session('admin_uid') : session('admin_pid');
        $map['uid'] = $uid;
        $map['status'] = ['>=' , 0];
        $query = [];
        $list = db('SmsTemplate')->where($map)->order('create_time desc')->paginate(20 , false , ['query'=>$query]);
        $page = $list->render();

        $this->assign('list' , $list);
        $this->assign('page' , $page);
        return $this->fetch();
    }

    /**
     * 短信模板添加
     */
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
            $count = db('SmsTemplate')->where($map)->count();
            if ($count > 0){
                return $this->formError('存在相同标识的模板');
            }
            if ($data['id']){
                $res = SmsTemplate::update($data);
            }else {
                $data['uid'] = $uid;
                $res = SmsTemplate::create($data);
            }
            if ($res){

                return $this->formSuccess('设置成功', url('admin/sms/index'));
            }else {
                return $this->formError('设置失败');
            }

        }else {

            $id = input('id' , 0);
            if ($id){
                $data = db('SmsTemplate')->find($id);
            }else{
                $data = null;
            }

            $form = FormBuilder::init(['ns'=>'we'])
                ->setFormName('sms-template-update')
                ->addClass('form-ajax')
                ->setAction($request->url())
                ->addText('title' , '短信模板标题' , $data['title'] , '请输入短信模板标题' , true)
                ->addText('name' , '短信模板标识' , $data['name'] , '请输入短信模板标识' , true)
                ->addText('id_no' , '短信模板ID,模板申请获取' , $data['id_no'] , '请输入短信模板ID' , true)
                ->addSelect('type' , '模板类型' , $data['type'] , ['1'=>'阿里大鱼'] , true)
                ->addRadio('is_code' , '是否验证码（默认：否）' , $data['is_code'] , ['0'=>'否' , 1=> '是'])
                ->addTextarea( 'content' , '模板内容'  ,$data['content'] , '请输入短信模板内容' , true)
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
            $template = SmsTemplate::get($id);
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
            $template = SmsTemplate::get($id);
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
    /**
     * 短信发送记录
     */
    public function lists(){

        $templateId = input('template_id');
        $map['template_id'] = $templateId;
        $query = [];
        $list = db('sms')->where($map)->order('create_time desc')->paginate(20 , false , ['query' , $query]);
        $page = $list->render();

        $this->assign('list' , $list);
        $this->assign('page' , $page);

        $template = db('SmsTemplate')->find($templateId);
        $this->assign('template' , $template);
        return $this->fetch();
    }

    /**
     * 发送短信
     */
    public function sent(){

        $uid = (session('admin_pid') == 0) ? session('admin_uid') : session('admin_pid');
        $request = Request::instance();
        if ($request->isPost()){
            $data = $request->post();
            $phone = $data['phone'];
            $templateId = $data['template_id'];
            $params = $data['params'];
            $config = \LC\Sms::getConfig($uid);

            $res = \LC\Sms::sentByTemplateId($templateId , $phone , $params , $config);
            if ($res){
                return $this->formSuccess('操作成功');
            }else {
                return $this->formError('操作失败');
            }

        }else {

            $id = input('id' , 0);
            $template = db('SmsTemplate')->find($id);
            $this->assign('template' , $template);

            $form = FormBuilder::init(['ns'=>'we'])
                ->setFormName('sms-sent')
                ->addClass('form-ajax')
                ->setAction($request->url())
                ->addText('phone' , '电话号码' , '' , '请输入手机号码' , true);
            if ($template['extra']){
                $params = \LC\Sms::formatExtra($template['extra']);
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