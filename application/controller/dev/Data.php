<?php
namespace app\controller\dev;

use think\Request;
use app\model\Models;
class Data extends Dev {
    
    public function index(){
        
        $project_id = input('project_id' , '');
        if ($project_id){
            $list = db('models')->where('project_id' , $project_id)->order('sort')->select();
        }else{
            $list = db('models')->order('sort')->select();
        }
        
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
            $count = db('models')->where($map)->count();
            if ($count > 0){
                return $this->formError('存在相同标识模型');
            }
            
            if ($data['id']){
                $res = Models::update($data);
            }else{
                $res = Models::create($data);
            }
            
            if ($res){
                return $this->formSuccess('操作成功' , url('dev/data/index'));
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
        
        $models = \app\model\Models::get($id);
        $models->status = $status;
        $res = $models->save();
        if ($res){
            return $this->formSuccess('操作成功');
        }else{
            return $this->formError('操作失败');
        }
    }
    
    public function lists(){
        
        $modelID = input('model_id' , 0);
        if(!$modelID){
            return $this->formError('请选择模型');
        }else{
            
            $M = new Models();
            $listField = $M->getListDisplayFields($modelID);
            $this->assign('fields' , $listField);
            
            $model = db('Models')->find($modelID);
            $this->assign('model' , $model);
            
            $modelName = $model['name'];
            $listCount = $model['list_count'];
//             $field = $model['list_display'];
            $order = $model['sort'];
            
            $query = db($modelName)->order($order);
            if ($listCount == 0){
                $list = $query->paginate($listCount);
                $page = $list->render();
            }else{
                $list = $query->select();
                $page = '';
            }
            $this->assign('list', $list);
            $this->assign('page', $page);
            // 渲染模板输出
            return $this->fetch();
        }
    }
    
    public function views(){
            
            $modelID = input('model');
            $id = input('id');
            
            $M = new Models();
            $listField = $M->getFieldsArr($modelID);
            $this->assign('fields' , $listField);
            
            $model = db('Models')->find($modelID);
            $this->assign('model' , $model);
            
            $modelName = $model['name'];
            
            $data = db($modelName)->find($id);
            $this->assign('data' , $data);
            
            return $this->fetch();
            
        
    }
}