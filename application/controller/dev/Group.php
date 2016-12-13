<?php
namespace app\controller\dev;
use think\Request;
use app\model\AuthGroup;
use app\model\AuthRule;
class Group extends Dev {
    
    public function index(){
        
        $uid = input('uid', 0);
        if (!$uid){
            $uid = config('dev.root_uid');
        }
        $map['uid'] = $uid;
        $list = db('AuthGroup')->where($map)->order('sort','asc')->select();
        
        $this->assign('list' , $list);
        
        $this->assign('auth_rule_type' , config('dev.auth_rule_type'));
        return $this->fetch();
    }
    
    public function update(){
        
        $request = Request::instance();
        if ($request->isPost()){
        
            $uid = session('dev_uid');
            $post = $request->post();
        
            // 检验重复
            if ($post['id']){
                $where = "(`title` = '".$post['title']."' OR `name` = '".$post['name']."') and `id` != ".$post['id'];
            }else{
                $where = "(`title` = '".$post['title']."' OR `name` = '".$post['name']."')";
            }
            $check = db('AuthGroup')->where($where)->count();
    
            if ($post['id']){
                $res = AuthGroup::update($post);
            }else{
                $post['uid'] = config('dev.root_uid');
                $res = AuthGroup::create($post);
            }
            if ($res){
                return $this->formSuccess('操作成功' , url('dev/group/index'));
            }else{
                return $this->formError('操作失败');
            }
            
        
        }else{
            $id = input('id' , 0);
        
            if ($id){
                $data = db('AuthGroup')->find($id);
            }else{
                $data = null;
            }
            $this->assign('data' , $data);
            return $this->fetch();
        }
        
    }
    
    public function status(){
    
        
        $id = input('id' , 0);
        
        $status = input('status');
        $group = AuthGroup::get($id);
        
        if ($status == 1){
            $rules = $group->rules;
            if (!$rules){
                return $this->formError('还未分配权限，无法启用角色组');
            }
        }
        
        $group->status = $status;
        $res = $group->save();
        if ($res){
            return $this->formSuccess('操作成功');
        }else{
            return $this->formError('操作失败');
        }
    }
    
    public function rule(){
        
        $request = Request::instance();
        if ($request->isPost()){
            
            $data = $request->post();
            
            $group = AuthGroup::get($data['group_id']);
            $rules = isset($data['rules']) ? implode(',', $data['rules']) : '';
            $group->rules = $rules;
            $res = $group->save();
            if ($res){
                // 重置缓存
                return $this->formSuccess('操作成功' , url('dev/group/index'));
            }else{
                return $this->formError('操作失败');
            }
            
        }else{
            
            $id = input('id');
            $group = db('AuthGroup')->find($id);
            $this->assign('group' , $group);
            
            $groupRules = explode(',', $group['rules']);
            $this->assign('groupRules' , $groupRules);
            
            $type = input('type');
            $authRuleType = config('dev.auth_rule_type');
            $authType = $authRuleType[$type];
            
            
            $Rule = new AuthRule();
            $rules = $Rule->getRulesGroupByType();
            
            $this->assign('rules' , $rules);
            
            foreach ($authRuleType as $k=>$v){
                if ($type == $k){
                    $typeData['show'] = 'show';
                }else{
                    $typeData['show'] = '';
                }
                $typeData['name'] = $v;
                $typeData['rules'] = isset($rules[$k])? $rules[$k] : null;
                $types[] = $typeData;
            }
            
            $this->assign('types' , $types);
            return $this->fetch();
        }
    }
}