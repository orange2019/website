<?php
namespace app\controller\wechat;
use app\controller\Base;
use app\model\P2pFinance;
use app\model\P2pLoan;
use app\model\P2pProduct;
use app\model\P2pRaise;
use LC\FormBuilder;
use think\Db;
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
        $uid = session('www_project')['uid'];

        $memberId = $this->auth();
        $loan = \LC\P2p::getMyLoan($memberId);
        $this->assign('loan' , $loan);

        $Product = new P2pProduct();
        $products = $Product->getListsDetailByUid($uid);
        $Finance = new P2pFinance();

        foreach ($products as $k=>$v){
            $v['finance_lists'] = $Finance->getListsByPid($v->id);
            $products[$k] = $v;
        }
        $this->assign('products' , $products);
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
                return $this->formSuccess('申请成功，请等待工作人员与您进行联系' , url('wechat/p2p/myLoan'));
            }else {
                return $this->formError('申请失败，请稍后重试');
            }
        }else{

            $loan = \LC\P2p::getMyLoan($uid);
            if ($loan){
                return $this->redirect('wechat/p2p/myLoan');
            }

            $form = FormBuilder::init()
                ->setFormName('wechat-p2p-apply')
                ->setAction(url(''))
                ->addClass('form-ajax')
                ->addText('name' , '姓名' , '' , '请输入您的真实姓名' , true)
                ->addText('phone' , '电话' , $userInfo['phone'] , '请输入您的联系电话')
                ->addRadio('sex' , '性别' , $userInfo['sex'] ,[1=>'男',2=>'女'] )
                ->addText('city' , '城市' , '' , '请输入您生活工作的城市')
                ->addRadio('occupation' , '职业' , 1 , config('p2p.occupation'))
                ->addCheckbox('assets[]' , '资产情况' , [] , config('p2p.assets'))
                ->addHidden('member_id' , '' , $userInfo['id'])

                ->addSubmit('提交借款申请')
                ->build();

            $this->assign('form' , $form);
            return $this->fetch();
        }
    }

    /**
     * 我的申请
     * @return mixed
     */
    public function myLoan(){

        $uid = $this->auth();
        $loan = \LC\P2p::getMyLoan($uid);
        $this->assign('loan' , $loan);

        return $this->fetch();
    }

    public function historyLoan(){

        $uid = $this->auth();
        $map['status'] = ['in' , [-1,1]];
        $map['member_id'] = $uid;

        $list = db('P2pLoan')->where($map)->order('create_time desc')->select();
        $this->assign('list' , $list);
        return $this->fetch();
    }



    public function finance(){
        $id = input('id' , 0);
        $memberId = $this->auth();
        $finance = db('P2pFinance')->find($id);
        $isSelf = ($memberId == $finance['member_id']) ? 1 : 0;
        $this->assign('is_self' , $isSelf);

        if ($finance){
            $P2pFinance = new P2pFinance();
            $raise = $P2pFinance->getRaiseById($finance['id']);
            $total = $finance['num'] - $raise;
            $loan = db('P2pLoan')->where('finance_id' , $finance['id'])->find();

            $this->assign('finance' , $finance);
            $this->assign('loan' , $loan);
            $this->assign('raise' , $raise);
            $this->assign('total' , $total);

            if ($isSelf == 0 && $total >0 && $finance['raise_end_time'] > time()){
                $this->assign('go_btn' , 1);
            }else{
                $this->assign('go_btn' , 0);
            }
            return $this->fetch();

        }else{
            return $this->formError('错误');
        }
    }

    public function raise(){
        $request  = Request::instance();
        $memberId = $this->auth();
        if ($request->isPost()){
            $data = $request->post();
            $num = $data['num'];
            $financeId = $data['finance_id'];

            Db::startTrans();
            try {
                $Raise = new P2pRaise();
                $res = $Raise->financeIn($financeId , $memberId  ,$num);
                if ($res === false){
                    throw new \Exception($Raise->getError());
                }

                Db::commit();

            }catch (\Exception $e){
                $error = $e->getMessage();
                Db::rollback();
                return $this->formError($error);
            }

            return $this->formSuccess('买入成功' , url('wechat/p2p/index'));

        }else {
            $id = input('finance_id' , 0);
            $finance = db('P2pFinance')->find($id);
            $isSelf = ($memberId == $finance['member_id']) ? 1 : 0;
            $this->assign('is_self' , $isSelf);

            $P2pFinance = new P2pFinance();
            $raise = $P2pFinance->getRaiseById($finance['id']);
            $total = $finance['num'] - $raise;

            $this->assign('finance' , $finance);
            $this->assign('raise' , $raise);
            $this->assign('total' , $total);

            $form = FormBuilder::init()->addClass('form-ajax')
                    ->setAction($request->url(true))
                    ->addText('num' , '买入金额'  , '' , '最低买入'.money($finance['min_num']) , true)
                    ->addHidden('finance_id' , '' , $finance['id'])
                    ->addSubmit('同意协议并购买')->build();

            $this->assign('form' , $form);

            return $this->fetch();
        }

    }
}