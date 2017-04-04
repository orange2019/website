<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/23
 * Time: 9:29
 */

namespace app\controller;


use app\model\P2pOrder;
use LC\FormBuilder;
use LC\Money;

class Test extends Base
{
    public function formBuilder(){


        $form = FormBuilder::init()
                ->addText('name' , '名称' , '' ,'请输入名称')
                ->build();

        echo $form;
    }

    public function money(){

        $i = Money::calculateInterest(200000 , 20 * 12 , 4.2 / 100 , 2);
        dump($i);
    }

    public function loan(){
        $Order = new P2pOrder();
        $list = $Order->createLists(1);
    }
}