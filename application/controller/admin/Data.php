<?php
namespace app\controller\admin;
use app\model\Models;
use think\Request;
class Data extends Admin {
    
    public function _initialize(){
    
        parent::_initialize();
    
        $projectId = session('admin_project_id');
        if (!$projectId){
            $uid = (session('admin_pid') == 0) ? session('admin_uid') : session('admin_pid');
            $project = db('Project')->where('uid' , $uid)->find();
            if ($project){
                session('admin_project_id' , $project['id']);
            }else{
                $this->redirect('admin/project/add');
    
            }
        }
    }
    
    public function index(){
        
        $projectId = session('admin_project_id');
        $map['project_id'] = $projectId;
        $list = db('models')->where($map)->order('sort')->select();
        
        $this->assign('list' , $list);
        return $this->fetch();
    }
    
    public function lists(){
        $modelID = input('model' , 0);
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
            session('back_url'  , Request::instance()->url(true));
            return $this->fetch();
        }
        
    }
    
    public function update(){
        
        $request = Request::instance();
        if ($request->isPost()){
            $data = $request->post();
            
            $modelID = $data['model'];
            $model = db('Models')->find($modelID);
            $modelName = $model['name'];
            
            $info = $data['info'];
            if ($info['id']){
                $res = db($modelName)->update($info);
                
            }else{
                $res = db($modelName)->insert($info);
            }
            
            if ($res){
                return $this->formSuccess('操作成功' , session('back_url' ));
            }else{
                return $this->formError('操作失败');
            }
           
        }else{
            
            $modelID = input('model' , 0);
            $id = input('id' , 0);
            
            $M = new Models();
            $listField = $M->getFieldsArr($modelID);
            $this->assign('fields' , $listField);
            
            $model = db('Models')->find($modelID);
            $this->assign('model' , $model);
            
            $modelName = $model['name'];
            
            if ($id){
                $data = db($modelName)->find($id);
            }else{
                $data = null;
            }
            
            $this->assign('info' , $data);
            return $this->fetch();
        }
    }
    
    public function delete(){
        
        $modelID = input('model' , 0);
        $id = input('id' , 0);
        
        $model = db('Models')->find($modelID);
        $modelName = $model['name'];
        
        $res = db($modelName)->delete($id);
        if ($res){
            return $this->formSuccess('操作成功');
        }else{
            return $this->formError('操作失败');
        }
    }
}