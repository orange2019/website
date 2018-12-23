<?php
namespace app\controller\dev;
use think\Request;
use app\model\Template;
class Theme extends Dev {
    
    public function index(){
        
        $list = db('Theme')->order('create_time' , 'desc')->paginate(10 , false);
        $page = $list->render();
        
        $this->assign('list' , $list);
        $this->assign('page' , $page);
        return $this->fetch();
    }
    
    public function update(){
        
        $request = Request::instance();
        if ($request->isPost()){
            
            $data = $request->post();
            $map['name'] = $data['name'];
            if ($data['id']){
                $map['id'] = ['<>' , $data['id']];
            }
            $count = db('Theme')->where($map)->count();
            if ($count > 0){
                return $this->formError('存在相同标识的');
            }
            
            if ($data['id']){
                $res = \app\model\Theme::update($data);
            }else{
                $res = \app\model\Theme::create($data);
            }
            
            if ($res){
                return $this->formSuccess('操作成功',url('dev/theme/index'));
            }else{
                return $this->formError('操作失败');
            }
        }else{
            
            $id = input('id' , 0);
            if ($id){
                $data = db('Theme')->find($id);
            }else{
                $data = null;
            }
            $this->assign('data' , $data);
            return $this->fetch();
        }
    }
    
    public function status(){
        
        $id = input('id');
        $status = input('status');
        
        $theme = \app\model\Theme::get($id);
        $theme->status = $status;
        $res = $theme->save();
        if ($res){
            return $this->formSuccess('操作成功');
        }else{
            return $this->formError('操作失败');
        }
    }
    
    public function delete(){
        
        $id = input('id');
        
        // 检查是否存在模板
        $countT = db('Template')->where('theme_id' , $id)->count();
        if ($countT > 0){
            return $this->formError('主题下存在模板，无法删除');
        }
        
        // 检查是否有项目使用
        $countP = db('Project')->where('theme_id' , $id)->count();
        if ($countP > 0){
            return $this->formError('主题有项目正在使用，无法删除');
        }
        
        $res = db('Theme')->where('id' , $id)->delete();
        if ($res){
            return $this->formSuccess('操作成功');
        }else{
            return $this->formError('操作失败');
        }
    }
    
    public function template(){
        
        $themeId = input('theme_id');
        $map['theme_id'] = $themeId;
        
        $list = db('Template')->where($map)->order('type' , 'asc')->select();
        $this->assign('list' , $list);
        
        $theme = db('Theme')->find($themeId);
        $this->assign('theme' ,$theme);
        
        $this->assign('types' , config('dev.template_type'));
        return $this->fetch();
    }
    
    public function templateUpdate(){
        
        $request = Request::instance();
        if ($request->isPost()){
        
            $data = $request->post();
            $map['name'] = $data['name'];
            $map['theme_id'] = $data['theme_id'];
            if ($data['id']){
                $map['id'] = ['<>' , $data['id']];
            }
            $count = db('Template')->where($map)->count();
            if ($count > 0){
                return $this->formError('存在相同标识的');
            }
        
            if ($data['id']){
                $res = Template::update($data);
            }else{
                $res = Template::create($data);
            }
        
            if ($res){
                return $this->formSuccess('操作成功',url('dev/theme/template?theme_id='.$data['theme_id']));
            }else{
                return $this->formError('操作失败');
            }
        }else{
        
            $id = input('id' , 0);
            $themeId = input('theme_id' , 0);
            if ($id){
                $data = db('Template')->find($id);
                $themeId = $data['theme_id'];
            }else{
                $data = null;
            }
        
            $this->assign('data' , $data);
            $this->assign('theme_id' , $themeId);
            
            $this->assign('types' , config('dev.template_type'));
            return $this->fetch();
        }
    }
    
    public function templateStatus(){
        

        $id = input('id');
        $status = input('status');
        
        $theme = Template::get($id);
        $theme->status = $status;
        $res = $theme->save();
        if ($res){
            return $this->formSuccess('操作成功');
        }else{
            return $this->formError('操作失败');
        }
        
    }
    
    public function templateDelete(){
        
        $id = input('id');
        
        // 查询是否有使用模板的栏目
        $count = db('category')->where('template',$id)->whereOr('template_sub' , $id)->count();
        if ($count){
            return $this->formError('模板被使用，无法删除');
        }
        
        $res = db('Template')->where('id' ,$id)->delete();
        if ($res){
            return $this->formSuccess('操作成功');
        }else{
            return $this->formError('操作失败');
        }
    }
}