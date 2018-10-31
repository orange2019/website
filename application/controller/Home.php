<?php
namespace app\controller;
use think\Request;
use think\Log;
use niklaslu\UnLimitTree;
class Home extends Base{

    public function _initialize()
    {
        parent::_initialize();

        $request = Request::instance();

        $controller = $request->controller();
        $this->controllerLimit($controller);

        // $domain = $request->server('HTTP_HOST');
//        $url = $request->baseUrl();

        $project = $this->getProject();
        $this->assign('project' , $project);
        session('www_project' , $project);

    }

    public function _empty(){
        
        $request = Request::instance();
//
//        $controller = $request->controller();
//        $this->controllerLimit($controller);
//
//        $domain = $request->server('HTTP_HOST');
        $url = $request->baseUrl();
//
//        $project = $this->getProject($domain);
        $project = session('www_project');
        if (!$project){
            $this->_404('no project');
        }else{
            // 获取全部category
            $categorys = $this->getAllCategorys($project['id']);
            $navs = UnLimitTree::unlimitedForLayer($categorys);
            $this->assign('navs' , $navs);

            // 二级栏目处理
            $navSub = [];
            foreach ($navs as $key => $value) {
                $navSub[$value['name']] = $value['child'];
            }
            $this->assign('navs_sub' , $navSub);
            
            // 获取栏目主题
            $themeId = $project['theme_id'];
            if (!$themeId){
                $this->_404('no theme');
            }else{
                $theme = db('Theme')->find($themeId);
                if (!$theme){
                    $this->_404('no theme');
                }else{
                    $themeName = $theme['name'];
                    session('home_theme' , $theme['name']);
                }
            }
        }
        
        // 获取当前url的category或者document
        $category = $this->getCategory($url , $categorys);
        if (!$category){
            // 查找文档
            $posts = $this->getPosts($url , $categorys);
            
            if ($posts){
                $this->postsAction($posts);
                // 查找文档所属栏目
                $category = db('category')->find($posts['category_id']);
                $this->categoryAction($category , true);
                
                $templateId = $category['template_sub'];
            }else{
                $this->_404('no content');
            }
        }else{
            // 处理栏目页面
            $this->categoryAction($category);
            // 栏目模板
            $templateId = $category['template'];
        }
        
        $param = $request->get();
        
        if (!$templateId){
            $this->_404('no template');
        }else{
            $template = db('Template')->find($templateId);
            if (!$template){
                $this->_404('no template');
            }else{

                $display = $themeName . '/' . $template['name'];

                return $this->fetch($display);
            }
           
        }
        
        
    }
    
    protected function controllerLimit($controller){
        if (strtolower($controller) == 'admin'){
            return $this->redirect('admin/index/index');
        }elseif (strtolower($controller) == 'dev'){
            return $this->redirect('dev/index/index');
        }
    }
    
    protected function getProject($domain = null){
        
        // $map['domain'] = $domain;
        $map['name'] = 'default';
        $map['status'] = 1;
        $project = db('Project')->where($map)->find();
        $project['info'] = $project['info'] ? json_decode($project['info'] , true) : null;
        
        session('home_project_id' , $project['id']);
        return $project;
    }
    
    protected function getAllCategorys($project_id){
        
        $map['project_id'] = $project_id;
        $map['status'] = 1;
        $categorys = db('category')->where($map)->order('sort' , 'asc')->select();
        
        return $categorys;
    }
    
    protected function getCategory($url , $categorys){
        
        if ($url == '' || $url == '/index.html'){
            $url = '/';
        }
        
        $result = null;
        foreach ($categorys as $v){
            if ($v['url'] == $url){
                $result = $v;
            }
        }
        
        return $result;
    }
    
    protected function getPosts($url , $categorys){

        if ($categorys){
            $url = urldecode($url);
            foreach ($categorys as $v){
                $ids[] = $v['id'];
            }

            $map['category_id'] = ['in' , $ids];
            $map['status'] = 1;
            $map['url'] = $url;

            $posts = db('posts')->where($map)->find();

            return $posts;
        }else {
            return null;
        }

       
    }
    
    protected function categoryAction($category , $detail = false){
        if ($detail == false){
            // 跳转下级处理
            $jump = $category['jump'];
            if ($jump == 1){
                // 下级栏目
                $mapChild['pid'] = $category['id'];
                $mapChild['status'] = 1;
                $childCategory = db('Category')->where($mapChild)->order('sort','asc')->find();
                if ($childCategory){
                    $url = $childCategory['url'];
                    $this->redirect($url , [] , 301);
                }else{
                    $mapChildP['category_id'] = $category['id'];
                    $mapChildP['status'] = 1;
                    $childPosts = db('posts')->where($mapChildP)->order('sort','asc')->find();
                    if ($childPosts){
                        $url = $childPosts['url'];
                        $this->redirect($url , [] , 301);
                    }else{
                        $this->_404('no child jump');
                    }
                }
            }
        }
        // 栏目数据
        $info = $category['info'] ? json_decode($category['info'] , true) : null;
        $category['info'] = $info;
        $this->assign('category' , $category);
        
        // 找到当前频道
        $channel = get_channel_by_category($category);
        $channel['info'] = $channel['info'] ? json_decode($channel['info'] , true) : null;
        $this->assign('channel' ,$channel);
        
        if ($detail == false){
            // 栏目seo
            $seo = $category['seo'] ? json_decode($category['seo'] , true) : null;
            $seo['title'] = (isset($seo['title']) && $seo['title']) ? $seo['title'] : $category['title'];
            $seo['keywords'] = (isset($seo['keywords']) && $seo['keywords']) ? $seo['keywords'] : '';
            $seo['description'] = (isset($seo['description']) && $seo['description'] ) ? $seo['description'] : '';
            $this->assign('seo' , $seo);
            
            // 栏目list
            $map['category_id'] = $category['id'];
            $map['status'] = 1;
            $listCount = $category['list_count'];
            $list = db('posts')->where($map)->order(['sort' => 'asc' , 'create_time' => 'desc'])->paginate($listCount,false);
            $page = $list->render();
            $this->assign('list' , $list);
            $this->assign('page' , $page);
        }
        
        
    }
    
    protected function postsAction($posts){
       
        $info = $posts['info'] ? json_decode($posts['info'] , true) : null;
        $posts['info'] = $info;
        $this->assign('detail' , $posts);
        
        // seo
        $seo = $posts['seo'] ? json_decode($posts['seo'] , true) : null;
        $seo['title'] = (isset($seo['title']) && $seo['title']) ? $seo['title'] : $posts['title'];
        $seo['keywords'] = (isset($seo['keywords']) && $seo['keywords']) ? $seo['keywords'] : '';
        $seo['description'] = (isset($seo['description']) && $seo['description'] ) ? $seo['description'] : '';
        $this->assign('seo' , $seo);
        
    }
    
    protected function _404($msg = ''){
        
        Log::write($msg , '404');
        echo  '404 Not Found!';
        exit();
        
    }
}