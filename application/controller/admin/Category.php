<?php
namespace app\controller\admin;
use think\Request;
use niklaslu\UnLimitTree;
class Category extends Admin {
    
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
        $project = db('Project')->find($projectId);
        $this->assign('project' , $project);
        
        $map['project_id'] = $projectId;
        $category = db('category')->where($map)->order('sort','asc')->select();
        
        $cates = UnLimitTree::unlimitedForLevel($category);
        $this->assign('list' , $cates);
        
        $types = config('dev.category_type');
        $this->assign('types' , $types);
        
        return $this->fetch();
    }
    
    public function add(){
        
        $request = Request::instance();
        if ($request->isPost()){
            
            $data = $request->post();
            
            // 检测是否存在相同标识
            $map['name'] = $data['name'];
            $map['project_id'] = $data['project_id'];
            $count = db('category')->where($map)->count();
            if ($count > 0){
                return $this->formError('存在相同标识的页面，请重新添加');    
            }
            
            $type = $data['type'];
            if ($type == 'index'){
                // 先检测是否存在首页index
                $mapI['type'] = 'index';
                $mapI['project_id'] = $data['project_id'];
                $count = db('category')->where($mapI)->count();
                if ($count > 0){
                    return $this->formError('存在首页，请重新添加');
                }
            }
            
            $pid = $data['pid'];
            if ($type == 'page' || $type == 'list' || $type == 'jump'){
                if ($pid == 0){
                    $data['url'] = '/' . $data['name'] . '/';
                }else{
                    $fa = db('category')->find($pid);
                    $data['url'] = $fa['url'] . $data['name'] . '/';
                }
            }elseif ($type == 'index') {
                $data['url'] = '/';
            }else{
                $data['url'] = '';
            }
            
            $res = \app\model\Category::create($data);
            if ($res){
                return $this->formSuccess('添加成功' ,url('admin/category/index'));
            }else{
                return $this->formError('添加失败');
            }
            
        }else {
            
            $types = config('dev.category_type');
            $this->assign('types' , $types);
            
            $projectId = session('admin_project_id');
            $this->assign('project_id' , $projectId);
            
            $category = db('category')->where('project_id' , $projectId)->order('sort','asc')->select();
            
            if ($category){
                
                $cates = UnLimitTree::unlimitedForLevel($category);
                
                $this->assign('cates' , $cates);
            }else{
                $this->assign('cates' ,null);
            }
            return $this->fetch();
        }
    }
    
    public function edit(){
       
        $request = Request::instance();
        if ($request->isPost()){
            
            $data = $request->post();
            $old = db('Category')->find($data['id']);
            
            // 检测有无相同代号栏目
            $mapC['id'] = ['<>' , $data['id']];
            $mapC['project_id'] = $data['project_id'];
            $mapC['name'] = $data['name'];
            $count = db('category')->where($mapC)->count();
            if ($count > 0){
                return $this->error('存在相同标识页面');
            }
            
            // 设置url
            $type = $data['type'];
            if ($old['name'] != $data['name']){
                $pid = $data['pid'];
                if ($type == 'page' || $type == 'list' || $type == 'jump'){
                    if ($pid == 0){
                        $data['url'] = '/' . $data['name'] . '/';
                    }else{
                        $fa = db('category')->find($pid);
                        $data['url'] = $fa['url'] . $data['name'] . '/';
                    }
                }elseif ($type == 'index') {
                    $data['url'] = '/';
                }else{
                    $data['url'] = '';
                }
            }
            
            // seo
            $data['seo'] = json_encode($data['seo']);
            
            $res = \app\model\Category::update($data);
            if ($res){
                return $this->formSuccess('编辑成功' ,url('admin/category/index'));
            }else{
                return $this->formError('编辑失败');
            }
        }else {
            
            $id = input('id');
            $data = db('Category')->find($id);
            
            $type = input('type', '');
            if ($type){
                $data['type'] = $type;
            }
            
            $this->assign('data' , $data);
            
            $types = config('dev.category_type');
            $this->assign('types' , $types);
            
            $seo = $data['seo'] ? json_decode($data['seo'] , true) : null;
            $this->assign('seo' , $seo);
            
            $category = db('category')->where('project_id' , $data['project_id'])->where('id','<>',$id)->order('sort','asc')->select();
            if ($category){
                $cates = UnLimitTree::unlimitedForLevel($category);
                $this->assign('cates' , $cates);
            }else{
                $this->assign('cates' ,null);
            }
            
            $project = db('Project')->find($data['project_id']);
            $themeId = $project['theme_id'];
            if ($themeId){
                $mapT['status'] = 1;
                $mapT['theme_id'] = $themeId;
                $template = db('Template')->where($mapT)->select();
                
                $templates = [];
                $templateSubs = [];
                foreach ($template as $v){
                    if ($v['type'] == 'posts'){
                        $templateSubs[] = $v;
                    }elseif ($v['type'] == 'index'){
                        if ($data['type'] == 'index'){
                            $templates[] = $v;
                        }
                    }elseif ($v['type'] == 'category'){
                        if ($data['type'] == 'page' || $data['type'] == 'list'){
                            $templates[] = $v;
                        }
                    }
                    
                }
                $this->assign('templates' , $templates);
                $this->assign('templateSubs' , $templateSubs);
                
            }else{
                $this->assign('templates' , null);
                $this->assign('templateSubs' , null);
            }
            
            return $this->fetch();
        }
    }
    
    public function content(){
        
        $request = Request::instance();
        if ($request->isPost()){
            
            $data = $request->post();
            if (isset($data['info'])){
                $data['info'] = json_encode($data['info']);
            }
            
            $res = \app\model\Category::update($data);
            if ($res){
                return $this->formSuccess('操作成功',url('admin/category/index'));
            }else{
                return $this->formError('操作失败');
            }
        }else{
            
            $id = input('id');
            $data = db('category')->find($id);
            $info = $data['info'] ? json_decode($data['info'] , true) : null;
            $this->assign('data' , $data);
            $this->assign('info' , $info);
            
            $templateId = $data['template'];
            if(!$templateId){
                return $this->formError('没有配置页面模板');
            }
            
            $template = db('Template')->find($templateId);
            $config = $template['config'];
            $configs = parse_config($config);
            $this->assign('configs' , $configs);
            
            return $this->fetch();
        }
    }
    
    public function status(){
        
        $id = input('id');
        $status = input('status');
        
        $category = \app\model\Category::get($id);
        $category->status = $status;
        $res = $category->save();
        if ($res){
            return $this->formSuccess('操作成功');
        }else{
            return $this->formError('操作失败');
        }
    }
    
    public function delete(){
        
        $id = input('id');
        
        // 存在下级栏目不可删除
        $count = db('category')->where('pid' , $id)->count();
        if ($count > 0){
            return $this->formError('存在下级栏目，不可删除');
        }
        
        // 存在文档不可删除
        $count = db('posts')->where('category_id' , $id)->count();
        if ($count > 0){
            return $this->formError('该栏目下添加过文档，不可删除');
        }
        
        $res = db('category')->where('id' , $id)->delete();
        if ($res){
            return $this->formSuccess('操作成功');
        }else{
            return $this->formError('操作失败');
        }
    }
    
}