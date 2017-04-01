<?php
namespace app\controller\wechat;
use app\controller\Base;
use app\model\P2pLoan;
use LC\FormBuilder;
use think\Request;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/1
 * Time: 9:50
 */
class P2p extends Base
{
    public function index(){

        return $this->fetch();
    }

    public function apply(){

        $request = Request::instance();
        $uid = $this->auth();
        $userInfo = $this->getUserInfoByUid($uid);

        if ($request->isPost()){

            $data = $request->post();
            $data['assets'] = isset($data['assets']) ? implode(',' , $data['assets']) : '';
            // 检测手机号
//            $checkPhone = check_phone($data['phone']);
//            if (!$checkPhone){
//                return $this->formError('请输入正确的手机号码');
//            }

            // 验证
            $validate = validate('P2pLoan');
            if (!$validate->check($data)){
                return $this->formError($validate->getError());
            }

            $res = P2pLoan::create($data);
            if ($res){
                return $this->formSuccess('申请成功，请等待工作人员与您进行联系');
            }else {
                return $this->formError('申请失败，请稍后重试');
            }
        }else{

            $form = FormBuilder::init()
                ->setFormName('wechat-p2p-apply')
                ->setAction(url(''))
                ->addClass('form-ajax')
                ->addText('name' , '姓名' , '' , '请输入您的真实姓名' , true)
                ->addText('phone' , '电话' , $userInfo['phone'] , '请输入您的联系电话')
                ->addRadio('sex' , '性别' , $userInfo['sex'] ,[1=>'男',2=>'女'] )
                ->addText('city' , '城市' , '' , '请输入您生活工作的城市')
                ->addRadio('occupation' , '职业' , 1 , config('p2p.occupation'))
                ->addCheckbox('assets' , '资产情况' , [] , config('p2p.assets'))
                ->addHidden('member_id' , '' , $userInfo['id'])

                ->addSubmit('提交借款申请')
                ->build();

            $this->assign('form' , $form);
            return $this->fetch();
        }
    }

    public function myloan(){

    }

    public function finance(){

    }
}