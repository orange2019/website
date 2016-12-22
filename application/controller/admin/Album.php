<?php
namespace app\controller\admin;
use think\Request;
class Album extends Admin {
    
    public function index(){
        
    }
    
    public function lists(){
        
        $relation = input('relation');
        $rid = input('rid');
      
        $map['relation_type'] = $relation;
        $map['relation_id'] = $rid;
        
        $list = db('Album')->where($map)->order('sort' , 'asc')->select();
        $this->assign('list' , $list);
        
        $this->assign('relation' , $relation);
        $this->assign('rid' , $rid);
        return $this->fetch();
    } 
    
    public function update(){
        $request = Request::instance();
        if ($request->isPost()){
            $data = $request->post();
            if ($data['id']){
                $res = \app\model\Album::update($data);
            }else{
                $res = \app\model\Album::create($data);
            }
            if ($res){
                $this->formSuccess('操作成功' , url('admin/album/lists?relation='.$data['relation_type'].'&rid='.$data['relation_id']));
            }else {
                $this->formError('操作失败');
            }
        }else{
            
            $id = input('id');
            if ($id){
                $data = db('Album')->find($id);
                $relationType = $data['relation_type'];
                $relationId = $data['relation_id'];
            }else{
                $data = null;
                $relationType = input('relation');
                $relationId = input('rid');
            }
            
            $this->assign('data' , $data);
            $this->assign('relation_type' , $relationType);
            $this->assign('relation_id' , $relationId);
            
            $types = config('dev.album_type');
            $this->assign('types' , $types);
            return $this->fetch();
        }
        
    }
    
    public function status(){
        
        $id = input('id');
        $status = input('status');
        
        $album = \app\model\Album::get($id);
        $album->status = $status;
        $res = $album->save();
        if ($res){
            
            return $this->formSuccess('操作成功');
        }else{
            return $this->formError('操作失败');
        }
    }
    
    public function delete(){
        
        $id = input('id' , 0);
        $res = db('album')->where('id' , $id)->delete();
        if ($res){
            return $this->formSuccess('操作成功');
        }else{
            return $this->formError('操作失败');
        }
    }
}