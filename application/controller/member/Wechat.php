<?php
/**
 * Created by PhpStorm.
 * User: lucong
 * Date: 2017/3/26
 * Time: 上午2:17
 */

namespace app\controller\member;


use app\controller\Home;
use app\model\MemberInfo;
use app\model\MemberValue;
use LC\FormBuilder;
use think\Db;
use think\Request;

class Wechat extends Home
{
    public function index(){

        $uid = $this->auth();
        $member = $this->getUserInfoByUid($uid);
        $this->assign('member' , $member);

        return $this->fetch();
    }

    /**
     * 用户资料编辑
     */
    public function info(){
        $memberId = $this->auth();
        $request = Request::instance();
        if ($request->isPost()){
            $data = $request->post();
            $Info = new MemberInfo();
            $res = $Info->updateInfo($data);
            if ($res){
                return $this->formSuccess('' , url('member/wechat/index'));
            }else{
                return $this->formError('操作失败');
            }
        }else {
            $member = $this->getUserInfoByUid($memberId);
            $info = db('MemberInfo')->where('member_id' , $memberId)->find();

            $form = FormBuilder::init()
                    ->setFormName('wechat-member-info')
                    ->addClass('form-ajax')
                    ->setAction(url(''))
                    ->addText('realname' , '真实姓名' , $info['realname'] , '请输入您的真实姓名' , true)
                    ->addText('identify_no' , '身份证号码' , $info['identify_no'] , '请输入您的身份证号码' , true)
                    ->addText('address' , '联系地址' , $info['address'] , '请输入您的联系地址' , true)
                    ->addText('company' , '公司名称' , $info['company'] , '请输入您工作的公司名称', true)
                    ->addNumber('income_year' , '年收入(元)' , $info['income_year'] / 100 , '请输入您的年收入(元)' , true)
                    ->addText('bank_card' , '银行卡号' , $info['bank_card'] , '请输入您的银行卡号' , true)
                    ->addText('bank_name' , '开户银行' , $info['bank_name'] , '请输入开户银行名称' , true)
                    ->addText('bank_branch' , '开户行支行名称' , $info['bank_branch'] , '请输入开户行支行名称' , true)
                    ->addText('contract_name' , '紧急联系人' , $info['contract_name'] , '请输入紧急联系人姓名' , true)
                    ->addText('contract_phone' , '联系人电话' , $info['contract_phone'] , '请输入紧急联系人电话' , true)
                    ->addTextarea('assets', '资产状况' , $info['assets'] , '请填写您的资产状况，有无房车，有无贷款' , false , true)
                    ->addHidden('member_id' , '' , $memberId)
                    ->addSubmit('提交')->build();

            $this->assign('form' , $form);
            $this->assign('member' , $member);
            $this->assign('info' , $info);

            return $this->fetch();
        }
    }

    public function assets(){
        $uid = $this->auth();
        $MemberValue = new MemberValue();
        $money = $MemberValue->getMemberValueByType($uid);
        $this->assign('money' , $money);

        return $this->fetch();
    }

    public function moneyIn(){
        $memberId = $this->auth();
        $request = Request::instance();
        if ($request->isPost()){
            $data = $request->post();
//            $data['num'] = floatval($data['num']);
            if ($data['num'] <= 0){
                return $this->formError('请输入正确的数字');
            }

            Db::startTrans();
            try {
               $MemberValue = new MemberValue();
               $res = $MemberValue->moneyIn($memberId , $data['num']);
               if ($res === false){
                   throw  new \Exception($MemberValue->getError());
               }else{
                   Db::commit();
               }
            }catch (\Exception $e){
                $error = $e->getMessage();
                Db::rollback();
                return $this->formError($error . ',请稍后再试');
            }

            return $this->formSuccess('操作成功' , url('member/wechat/assets'));
        }else{

            $form = FormBuilder::init()
                ->setFormName('wechat-member-money-in')
                ->addClass('form-ajax')
                ->setAction(url(''))
                ->addText('num' , '充值金额' , '' , '请输入充值金额' , true)
                ->addSubmit('提交')->build();

            $this->assign('form' , $form);
            return $this->fetch();
        }
    }

    public function moneyOut(){
        $memberId = $this->auth();
        $request = Request::instance();
        if ($request->isPost()){
            $data = $request->post();
//            $data['num'] = floatval($data['num']);
            if ($data['num'] <= 0){
                return $this->formError('请输入正确的数字');
            }

            Db::startTrans();
            try {
                $MemberValue = new MemberValue();
                $res = $MemberValue->moneyOut($memberId , $data['num']);
                if ($res === false){
                    throw  new \Exception($MemberValue->getError());
                }else{
                    Db::commit();
                }
            }catch (\Exception $e){
                $error = $e->getMessage();
                Db::rollback();
                return $this->formError($error . ',请稍后再试');
            }

            return $this->formSuccess('操作成功' , url('member/wechat/assets'));
        }else{

            $Value = new MemberValue();
            $value = $Value->getMemberValueByType($memberId);
            $this->assign('value' , $value);

            $form = FormBuilder::init()
                ->setFormName('wechat-member-money-out')
                ->addClass('form-ajax')
                ->setAction(url(''))
                ->addText('num' , '提现金额' , '' , '最多可提现'.money($value) , true)
                ->addSubmit('提交')->build();

            $this->assign('form' , $form);

            return $this->fetch();
        }
    }

}