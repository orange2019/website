<?php
namespace app\controller;
use think\Request;
use think\Log;
class Home extends Base{
    
    public function _empty(){
        
        $request = Request::instance();
        
        $controller = $request->controller();
        $this->controllerLimit($controller);
        
        $domain = $request->server('HTTP_HOST');
        $url = $request->baseUrl();
        
        $project = $this->getProject($domain);
        if (!$project){
            $this->_404('no project');
        }else{
            // 获取全部category
            $categorys = $this->getAllCategorys($project['id']);
            
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
                $category = db('category')->find($posts['category_id']);
                $templateId = $category['template_sub'];
            }else{
                $this->_404('no content');
            }
        }else{
            // 处理栏目页面TODO
            
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
    
    protected function getProject($domain){
        
        $map['domain'] = $domain;
        $map['status'] = 1;
        $project = db('Project')->where($map)->find();
        
        session('home_project_id' , $project['id']);
        return $project;
    }
    
    protected function getAllCategorys($project_id){
        
        $map['project_id'] = $project_id;
        $map['status'] = 1;
        $categorys = db('category')->where($map)->select();
        
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
        
        foreach ($categorys as $v){
            $ids[] = $v['id'];
        }
        
        $map['category_id'] = ['in' , $ids];
        $map['status'] = 1;
        
        $posts = db('posts')->where($map)->find();
        
        return $posts;
       
    }
    
    protected function categoryAction($category){
        // TODO
    }
    
    protected function _404($msg = ''){
        
        Log::write($msg , '404');
        echo  '404 Not Found!';
        exit();
        
    }
}