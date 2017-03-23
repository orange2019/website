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

        $config['ns'] = isset($config['ns']) ? $config['ns'] : 'o';
        $FormBuilder = new \niklaslu\FormBuilder($config);

        return $FormBuilder;
    }
}