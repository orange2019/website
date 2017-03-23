<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/23
 * Time: 9:29
 */

namespace app\controller;


use LC\FormBuilder;

class Test extends Base
{
    public function formBuilder(){


        $form = FormBuilder::init()
                ->addText('name' , '名称' , '' ,'请输入名称')
                ->build();

        echo $form;
    }
}