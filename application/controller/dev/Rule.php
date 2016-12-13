<?php
namespace app\controller\dev;
use app\model\AuthRule;
use think\Request;
class Rule extends Dev {
    
    public function index(){

        $component = input('component' , 0);
        
        $this->assign('auth_rule_type' , config('dev.auth_rule_type'));
        
        $components = db('AuthComponent')->where('status' , 1)->select();
        foreach ($components as $k=>$v){
            $v['show'] = 'show';
            if ($component){
                $v['show'] = '';
            }
            if ($v['id'] == $component){
                $v['show'] = 'show';
            }
            $components[$k] = $v;
        }
        $this->assign('components' , $components);
        
        $baseShow = 'show';
        
        if ($component){
            $baseShow = '';
        }
        
        $this->assign('base_show' , $baseShow);
        return $this->fetch();
    }
    
    public function lists(){
        
        $type = input('type' , 'pc');
        $component = input('component' , 0);
        $types = config('dev.auth_rule_type');
        
        $this->assign('type' , $type);
        $this->assign('typeData' , $types[$type]);
        
        $Rule = new AuthRule();
        $list = $Rule->getRules($type , $component);
        
        // 
        if ($component){
            $componentData = db('AuthComponent')->find($component);
        }else{
            $componentData = null;
        }
        $this->assign('component' , $componentData);
        
        $this->assign('list' , $list);
        session('back_url' , Request::instance()->url());
        return $this->fetch();
    }
    
    public function update(){


        $request = Request::instance();
        if ($request->isPost()){
        
            $uid = session('dev_uid');
            $post = $request->post();
        
            // 检验重复
            if ($post['id']){
                $where = "(`title` = '".$post['title']."' OR `name` = '".$post['name']."') and `type` = '".$post['type']."' and `id` != ".$post['id'];
            }else{
                $where = "(`title` = '".$post['title']."' OR `name` = '".$post['name']."') and `type` = '".$post['type']."'";
            }
            $check = db('AuthRule')->where($where)->count();
        
            if ($post['id']){
                $res = AuthRule::update($post);
            }else{
                $post['uid'] = config('dev.root_uid');
                $res = AuthRule::create($post);
            }
            if ($res){
                
                return $this->formSuccess('操作成功' , session('back_url'));
            }else{
                return $this->formError('操作失败');
            }
        
        
        }else{
            $id = input('id' , 0);
            $type = input('type','pc');
            $component = input('component' , 0);
            if ($id){
                $data = db('AuthRule')->find($id);
                $type = $data['type']? $data['type'] : $type;
                $component = $data['component_id']? $data['component_id'] : $component;
            }else{
                $data = null;
            }
            
            $this->assign('data' , $data);
            
            // 权限平台类别
            $this->assign('type' , $type);
            $this->assign('auth_rule_type' , config('dev.auth_rule_type'));
            
            // 权限组件类别
            $components = db('authComponent')->where('status',1)->select();
            $this->assign('components' , $components);
            $this->assign('component' , $component);
            
            $Rule = new AuthRule();
            $list = $Rule->getRuleSelect($type , $id , $component);
//             dump($list);
            $this->assign('rules' , $list);
            return $this->fetch();
        }
        
        
    }
    
    public function status(){
    
        $id = input('id' , 0);
        
        $status = input('status');
        $rule = AuthRule::get($id);
        
        if ($status != -1){
            $rule->status = $status;
            $res = $rule->save();
        }else{
            // 删除
            $child = AuthRule::where('pid' , $id)->count();
            if ($child > 0){
                return $this->formError('存在下级权限，无法删除');
            }
            $res = $rule->delete();
        }
        
        if ($res){
            return $this->formSuccess('操作成功');
        }else{
            return $this->formError('操作失败');
        }
    }
}