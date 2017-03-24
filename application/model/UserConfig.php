<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/24
 * Time: 16:15
 */

namespace app\model;


use think\Model;

class UserConfig extends Model
{
    protected $autoWriteTimestamp = true;
    /**
     * 获取用户扩展配置
     * @param $uid
     * @param string $type
     * @return mixed|null
     */
    public function getExtendConfig($uid , $type = 'wechat'){

        $config = $this->where('uid' , $uid)->find();
        if (!$config){
            return null;
        }else{
            $data = $config->getAttr($type);
            return $data ? json_decode($data , true) : null;
        }

    }

    public function setExtendConfig($uid , $type = 'wechat' , $data = []){

        $config = $this->where('uid' , $uid)->find();
        $data = is_array($data) ? json_encode($data , true) : $data;
        if ($config){

            $config->setAttr($type , $data);
            $res = $config->save();
        }else {
            $save['uid'] = $uid;
            $save[$type] = $data;
            $res = $this->save($save);
        }


        return $res;
    }
}