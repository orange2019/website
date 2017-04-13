<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/13
 * Time: 12:14
 */

namespace app\controller\cgi;


use think\Request;

class Auth extends Cgi
{
    public function access_token(){

        $request = Request::instance();
        $data = $request->post();

        return $this->jsonSuccess($data);
    }
}