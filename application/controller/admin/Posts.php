<?php
namespace app\controller\admin;
use think\Request;
use niklaslu\UnLimitTree;
use PY;
class Posts extends Admin {
    
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
        
        $categoryId = input('category_id','');
        $pagesize = 10;
        $query = [];
        $keyword = input('keyword' , '');
        if ($keyword){
            $map['title'] = ['like' , '%'.$keyword.'%'];
            $query['keyword'] = $keyword;
        }
        $this->assign('keyword' , $keyword);
        
        if ($categoryId){
            $map['category_id'] = $categoryId;
            $query['category_id'] = $categoryId;
            
            // 获取pagesize
            $category = db('Category')->find($categoryId);
            $pagesize = ($category['list_count'] > 0) ? $category['list_count'] : $pagesize;
        }else{
            $projectId = session('admin_project_id');
            $categoryIds = db('Category')->where('project_id',$projectId)->column('id');
            $map['category_id'] = ['in' , $categoryIds];
        }
        
        $map['status'] = ['>=' , 0];
        
        $list = db('posts')->where($map)->order('sort' , 'asc')->paginate($pagesize , false , $query);
        $page = $list->render();
        
        $this->assign('list' , $list);
        $this->assign('page' , $page);
        $this->assign('category_id' , $categoryId);
        
        return $this->fetch();
    }
    
    public function update(){
        
        $request = Request::instance();
        if ($request->isPost()){
            $data = $request->post();
//             $data['name'] = $data['name'] ? $data['name'] : $data['title'];
            $data['name'] = $data['name'] ? $data['name'] : PY\Pinyin::encode($data['title'],'all');
            
            // 检测是否存在相同标题
            $projectId = session('admin_project_id');
            $categoryIds = db('Category')->where('project_id',$projectId)->column('id');
            $map['category_id'] = ['in' , $categoryIds];
            $map['name'] = $data['name'];
            if ($data['id']){
                $map['id'] = ['<>' , $data['id']];
            }
            $count = db('Posts')->where($map)->count();
            if ($count > 0){
                return $this->error('存在相同名称文档');
            }
            
            $data['seo'] = json_encode($data['seo']);
            $data['info'] = json_encode($data['info']);
            
            $category = db('category')->find($data['category_id']);
            $data['url'] = $category['url'] . $data['name'] . '.html' ;
            
            if ($data['id']){
                $res = \app\model\Posts::update($data);
            }else{
                $res = \app\model\Posts::create($data);
            }
            if ($res){
                
                return $this->formSuccess('操作成功',url('admin/posts/index?category_id='.$data['category_id']));
            }else{
                return $this->formError('操作失败');
            }
            
        }else{
            $categoryId = input('category_id' , 0);
            
            $projectId = session('admin_project_id');
            $mapC['project_id'] = $projectId;
            $mapC['status'] = 1;
            $category = db('category')->where($mapC)->order('sort' , 'asc')->select();
            $cates = UnLimitTree::unlimitedForLevel($category);
            $this->assign('cates' , $cates);
            
            
            $id = input('id',0);
            if ($id){
                $data = db('Posts')->find($id);
                $info = $data['info'] ? json_decode($data['info'] , true) : null;
                $seo = $data['seo'] ? json_decode($data['seo'] , true) : null;
                if (!$categoryId){
                    $categoryId = $data['category_id'];
                }
               
                $this->assign('data' , $data);
                $this->assign('seo' , $seo);
                $this->assign('info' , $info);
                
            }else{
                $this->assign('data' , null);
                $this->assign('seo' , null);
                $this->assign('info' , null);
            }
            
            
            $cate = db('category')->find($categoryId);
            $config = $cate['config'];
            $configs = parse_config($config);
            $this->assign('configs' , $configs);
            $this->assign('category_id' , $categoryId);
            
            return $this->fetch();
        }
    }
    
    public function status(){
        
        $id = input('id');
        $status = input('status');
        
        $posts = \app\model\Posts::get($id);
        $posts->status = $status;
        $res = $posts->save();
        if ($res){
            return $this->formSuccess('操作成功');
        }else{
            return $this->formError('操作失败');
        }
    }
    
    public function delete(){
        
        $id = input('id');
        $status = -1;
        
        $posts = \app\model\Posts::get($id);
        $posts->status = $status;
        $res = $posts->save();
        if ($res){
            return $this->formSuccess('操作成功');
        }else{
            return $this->formError('操作失败');
        }
    }
}