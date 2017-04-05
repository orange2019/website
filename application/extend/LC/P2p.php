<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/1
 * Time: 11:47
 */

namespace LC;


use app\model\P2pFinance;
use app\model\P2pProduct;
use think\Request;

class P2p
{
    static public function occupation($value){
        $data = config('p2p.occupation');
        return isset($data[$value]) ? $data[$value] : '';
    }

    static public function assets($value){
        if (!$value){
            return '';
        }
        if (!is_array($value)){
            $value = explode(',' , $value);
        }

        $data = config('p2p.assets');
        $res = [];
        foreach ($value as $v){
            $res[] = $data[$v];
        }

        return implode(',',$res);
    }

    /**
     * 获取抵押担保信息
     * @param $value
     * @return array|string
     */
    static public function guarantee($value){
        if (!$value){
            return '';
        }
        if (!is_array($value)){
            $value = explode(',' , $value);
        }

        $data = config('p2p.guarantee');
        $res = [];
        foreach ($data as $k=>$v){
            if (in_array($k , $value)){
                $res[$k] = ['name'=>$v, 'value' => 1];
            }else{
                $res[$k] = ['name'=>$v, 'value' => 0];
            }
        }

        return $res;
//        return implode(',',$res);
    }

    /**
     * 获取审核验证信息
     * @param $value
     * @return array|string
     */
    static public function examine($value){
        if (!$value){
            return '';
        }
        if (!is_array($value)){
            $value = explode(',' , $value);
        }

        $data = config('p2p.examine');
        $res = [];
        foreach ($data as $k=>$v){
            if (in_array($k , $value)){
                $res[$k] = ['name'=>$v, 'value' => 1];
            }else{
                $res[$k] = ['name'=>$v, 'value' => 0];
            }
        }

        return $res;
    }

    static public function repayment($value){
        $data = config('p2p.repayment');
        return isset($data[$value]) ? $data[$value] : '';
    }

    static public function levelValue($value){
        $data = config('p2p.level_value');
        return isset($data[$value]) ? $data[$value] : '';
    }

    static public function step($value){
        $data = config('p2p.loan_step');
        return $data[$value];
    }

    static public function getMyLoan($uid){
        $map['member_id'] = $uid;
        $map['status'] = 0;
        $loan = db('P2pLoan')->where($map)->find();
        if ($loan){
            if ($loan['finance_id']){
                $Finance = new P2pFinance();
                $loan['finance'] = db('P2pFinance')->find($loan['finance_id']);
                $loan['raise_num'] = $Finance->getRaiseById($loan['finance_id']);
            }else {
                $loan['finance'] = null;
                $loan['raise_num'] = 0;
            }
            return $loan;
        }else{
            return false;
        }


    }

    static public function getFinanceNum($finance){
        if (is_object($finance)){
            $id = $finance->id + 100000;
            $productId = $finance->product_id;
        }else{
            $id = $finance['id'] + 100000;
            $productId = $finance['product_id'];
        }
        $product = P2pProduct::get($productId);
        return $product->getAttr('name') . $id;
    }

    static public function stepInfoForm($step , $data = null){
        $form = '';
        if ($step == 1){
            $form = FormBuilder::init(['ns'=>'we'])
                ->setFormName('p2p-step1')
                ->addClass('form-ajax')
                ->setAction(Request::instance()->url())
                ->addText('loan[use_info]' , '借款用途' , $data['use_info'] , '请输入借款用途' , true)
                ->addNumber('loan[value_pre]' , '预计借款金额' , $data['value_pre'] , '请输入预计借款金额' , true)
                ->addNumber('loan[time_limit]' , '借款期限' , $data['time_limit'] , '请输入借款期限（月）' , true)
                ->addSelect('loan[repayment]' , '还款方式' , $data['repayment'] , config('p2p.repayment') , true)
                ->addTextarea('info' , '备注信息' , '' , '请输入备注信息')
                ->addHidden('loan[id]' , '' , $data['id'])
                ->addSubmit('提交')->build();
        }elseif ($step == 2){
            $form = FormBuilder::init(['ns'=>'we'])
                ->setFormName('p2p-step2')
                ->addClass('form-ajax')
                ->setAction(Request::instance()->url())
                ->addCheckbox('loan[info_content][]' , '用户提供资料' , $data['info_content'] , config('p2p.info_content'))
                ->addSelect('loan[info_complete]' , '用户资料是否提供完毕' , $data['info_complete'] , [1=>'是',0=>'否'])
                ->addSelect('loan[level_value]' , '用户评级' , $data['level_value'] , config('p2p.level_value'))
                ->addTextarea('info' , '备注信息' , '' , '请输入备注信息')
                ->addHidden('loan[id]' , '' , $data['id'])
                ->addHidden('loan[member_id]' , '' ,$data['member_id'])
                ->addSubmit('确定资料提交')->build();
        }elseif ($step == 3){
            $form = FormBuilder::init(['ns'=>'we'])
                ->setFormName('p2p-step3')
                ->addClass('form-ajax')
                ->setAction(Request::instance()->url())
                ->addCheckbox('loan[examine][]' , '审核项目是否验证，已验证打钩' , $data['examine'] , config('p2p.examine'))
                ->addCheckbox('loan[guarantee][]' , '是否有无担保项，有打钩' , $data['guarantee'] , config('p2p.guarantee'))
                ->addSelect('loan[check_complete]' , '用户审核是否通过' , $data['check_complete'] , [1=>'是',0=>'否'])
                ->addTextarea('info' , '备注信息' , '' , '请输入备注信息')
                ->addHidden('loan[id]' , '' , $data['id'])
                ->addHidden('loan[member_id]' , '' ,$data['member_id'])
                ->addSubmit('提交评估审核')->build();
        }elseif ($step == 4){
            $Product = new P2pProduct();
            $products = $Product->getListsByUid($data['uid']);
            $form = FormBuilder::init(['ns'=>'we'])
                ->setFormName('p2p-step4')
                ->addClass('form-ajax')
                ->setAction(Request::instance()->url())
                ->addSelect('finance[product_id]' , '产品选择' , '' , $products , true)
                ->addText('finance[code]' , '项目备案编号' , '' , '请输入项目备案编号' , true)
                ->addNumber('finance[time_limit]' , '借款期限' , $data['time_limit'] , '请填写借款期限（月）' , true)
                ->addNumber('finance[num]', '筹集资金' , $data['value_pre'] / 100 , '请填写筹集资金数额' , true)
                ->addText('finance[interest_rate]' , '还款年利率' , '' , '亲填写年利率数字，保留两位小数' ,true)
                ->addSelect('finance[repayment]' , '还款方式' , $data['repayment'] , config('p2p.repayment'))
                ->addText('finance[raise_start_time]' , '筹资开始时间' , '' , '填写格式 2000-01-01' , true)
                ->addText('finance[raise_end_time]' , '筹资结束时间' , '' , '填写格式 2000-01-01' , true)
                ->addText('finance[income_rate]' , '投资收益年利率' , '' , '请填写年利率数字，保留两位小数' ,true)
                ->addNumber('finance[min_num]' , '最低起投金额' , 0 , '请输入最低起投金额' , true)
                ->addTextarea('info' , '备注信息' , '' , '请输入备注信息')
                ->addHidden('loan[id]' , '' , $data['id'])
                ->addHidden('loan[member_id]' , '' ,$data['member_id'])
                ->addSubmit('生成金融协议')->build();
        }elseif ($step == 5){
            $form = FormBuilder::init(['ns'=>'we'])
                ->setFormName('p2p-step5')
                ->addClass('form-ajax')
                ->setAction(Request::instance()->url())
                ->addText('finance[raise_end_time]' , '筹资结束时间' , $data['raise_end_time'] , '填写格式 2000-01-01' , true)
                ->addTextarea('info' , '备注信息' , '' , '请输入备注信息')
                ->addHidden('loan[id]' , '' , $data['id'])
                ->addHidden('loan[member_id]' , '' ,$data['member_id'])
                ->addHidden('finance[id]' , '' ,$data['finance_id'])
                ->addHidden('loan[raise_complete]' , '' ,0)
                ->addSubmit('修改筹资结束时间')->build();
        }elseif ($step == 6){
            $form = FormBuilder::init(['ns'=>'we'])
                ->setFormName('p2p-step6')
                ->addClass('form-ajax')
                ->setAction(Request::instance()->url())
                ->addText('loan[loan_date]' , '贷款发放日期' , date('Y-m-d') , '填写格式 2000-01-01' , true)
                ->addTextarea('info' , '备注信息' , '' , '请输入备注信息')
                ->addHidden('loan[id]' , '' , $data['id'])
                ->addHidden('loan[member_id]' , '' ,$data['member_id'])
                ->addSubmit('确认')->build();
        }

        return $form;
    }
}