<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/13
 * Time: 11:46
 */

namespace app\controller\dev;


use app\model\App;
use think\Request;

class Api extends Dev
{
    public function index(){


        $list = db('app')->order('create_time desc')->select();

        $this->assign('list' , $list);

        return $this->fetch();

    }

    public function update(){

        $request = Request::instance();
        if ($request->isPost()){
            $data = $request->post();

            // 检查重名
            $map['name'] = $data['name'];
            if ($data['id']){
                $map['id'] = ['<>' , $data['id']];
            }
            $count = db('app')->where($map)->count();
            if ($count > 0){
                return $this->formError('存在相同名称app');
            }

            // 生成appid

            if ($data['id']){
                $res = App::update($data);
            }else{
                $data['app_id'] = md5($data['name'].time());
                $data['app_secret'] = base64_encode($data['app_id']);
                $res = App::create($data);
            }

            if ($res){
                return $this->formSuccess('操作成功' , url('dev/api/index'));
            }else{
                return $this->formError('操作失败');
            }

        }else{

            $id = input('id');
            $projectId = 0;
            if ($id){
                $data = db('models')->find($id);
                $projectId = $data['project_id'];
            }else{
                $data = null;
            }

            $projects = db('Project')->where('status' , '>=' ,0)->select();
            $this->assign('projects' , $projects);

            $this->assign('data' , $data);
            $this->assign('project_id' , $projectId);
            return $this->fetch();
        }
    }

    public function status(){

        $id = input('id');
        $status = input('status');

        $models = \app\model\App::get($id);
        $models->status = $status;
        $res = $models->save();
        if ($res){
            return $this->formSuccess('操作成功');
        }else{
            return $this->formError('操作失败');
        }
    }
}