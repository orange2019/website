<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/23
 * Time: 9:24
 */

namespace LC;


class FormBuilder
{
    static public function init($config = []){

        $FormBuilder = new \niklaslu\FormBuilder($config);

        return $FormBuilder;
    }
}