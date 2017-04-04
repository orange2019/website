<?php
namespace app\controller\admin;
use think\Request;
use app\model\AuthGroup;
use app\model\User;
use app\model\UserComponent;
use niklaslu\UnLimitTree;
use app\model\AuthUserGroup;
use app\model\Project;
use app\model\UserProject;
class System extends Admin {
    
    public function index(){

        $uid = session('admin_uid');
        $pid = session('admin_pid');
        
        return $this->fetch();
    }
    
    public function user(){
        $pid = session('admin_uid');
        
        // 只有顶级管理员才可以进行操作
        $admin = db('user')->find($pid);
        if ($admin['pid'] != 0){
            return $this->formError('无权限操作');
        }
        
        $map['pid'] = $pid;
        $list = db('user')->where($map)->select();
        
        $this->assign('list',$list);
        return $this->fetch();
    }
    
    public function userUpdate(){
        
        $request = Request::instance();
        if ($request->isPost()){
            
            $uid = session('admin_uid');
            $admin = db('user')->find($uid);
            
            $post = input('post.');
            
            if ($admin['pid'] == 0){
            
                // 检验重复
                if ($post['id']){
                    $where = "(`email` = '".$post['email']."' OR `name` = '".$post['name']."') and `id` != ".$post['id'];
                }else{
                    $where = "(`email` = '".$post['email']."' OR `name` = '".$post['name']."')";
                }
                $check = db('user')->where($where)->count();
            
                // 检验密码
                if ($post['password'] != $post['password_again']){
                    $this->formError('两次密码输入不一样');
                }else {
                    unset($post['password_again']);
                    if ($post['password']){
                        $post['password'] = md5($post['password']);
                    }else{
                        unset($post['password']);
                    }
                }
            
                if ($post['id']){
                    $res = \app\model\User::update($post);
                }else{
                    $post['pid'] = $uid;
                    $post['status'] = 0;
                    $res = \app\model\User::create($post);
                }
                if ($res){
                    $uid = $post['id'] ? $post['id'] : $res->id;
                    cache_clear('user_info_list' , $uid);
                    return $this->formSuccess('操作成功' , url('admin/system/user'));
                }else{
                    return $this->formError('操作失败');
                }
            }else{
                return $this->formError('无权限操作');
            }
        }else{
            
            $id = input('id' , 0);
            if ($id){
                $data = db('user')->find($id);
            }else{
                $data = null;
            }
            
            $this->assign('data' , $data);
            return $this->fetch();
        }
        
    }
    
    public function userStatus(){
        
        $uid = session('admin_uid');
        $admin = db('user')->find($uid);
        if ($admin['pid'] != 0){
            return $this->formError('无权限操作');
        }
        
        $id = input('id' ,0);
        $status = input('status');
        
        // 当审核通过时，判断是否分配角色组
        if ($status == 1){
            $haveGroup = db('AuthUserGroup')->where('user_id' , $id)->count();
            if ($haveGroup == 0){
                return $this->formError('请先分配角色组用户');
            }
        }
        $user = \app\model\User::get($id);
        $user->status = $status;
        $res = $user->save();
        if ($res){
            return $this->formSuccess('操作成功');
        }else{
            return $this->formError('操作失败');
        }
    }
    
    public function userGroup(){
        
        $uid = session('admin_uid');
        $admin = db('user')->find($uid);
        if ($admin['pid'] != 0){
            return $this->formError('无权限操作');
        }
        
        $request = Request::instance();
        if ($request->isPost()){
            $data = $request->post();
            
            $userGroup = AuthUserGroup::get(['user_id'=>$data['user_id']]);
            
            if ($userGroup){
                if ($data['group_id'] == 0){
                    $res = $userGroup->delete();
                }else{
                    $userGroup->group_id = $data['group_id'];
                    $res = $userGroup->save();
                }
                
            }else{
                if ($data['group_id'] != 0){
                    $res = AuthUserGroup::create($data);
                }
                
            }
            if ($res){
                
                cache_clear('user_group_list' , $data['user_id']);
                
                return $this->formSuccess('操作成功' ,url('user'));
            }else{
                return $this->formError('操作失败');
            }  
        }else{
            
            $id = input('id');
            $user = db('User')->find($id);
            $this->assign('user' , $user);
            
            $mapG['status'] = 1;
            $mapG['uid'] = $uid;
            $groups = db('AuthGroup')->where($mapG)->order('sort','asc')->select();

            if (count($groups) <= 0){
                return $this->formError('还未添加角色，请先去添加' , url('admin/system/groupUpdate'));
            }
            $this->assign('groups' , $groups);
            
            $userGroup = db('AuthUserGroup')->where('user_id' , $id)->find();
            $userGroupId = $userGroup ? $userGroup['group_id'] : 0;
            $this->assign('userGroup' , $userGroupId);
            
            return $this->fetch();
        }
        
    }
    
    public function userProject(){
        
        $uid = session('admin_uid');
        $admin = db('user')->find($uid);
        if ($admin['pid'] != 0){
            return $this->formError('无权限操作');
        }
        
        $request = Request::instance();
        if ($request->isPost()){
            $data = $request->post();
            
            $user_id = $data['user_id'];
            $projects = isset($data['projects']) ? implode(',', $data['projects']) : '';
            $data['projects'] = $projects;
            
            $userProject = UserProject::get(['user_id'=>$user_id]);
            if ($userProject){
                $userProject->projects = $projects;
                $res = $userProject->save();
            }else{
                $res = UserProject::create($data);
            }
            
            if ($res){
                
                cache_clear('user_project_list' , $data['user_id']);
                return $this->formSuccess('设置成功' ,url('user'));
            }else{
                return $this->formError('设置失败');
            }
        }else{
            $id = input('id');
            $user = db('User')->find($id);
            $this->assign('user' , $user);
            
            $projects = db('Project')->where('uid' , $uid)->select();
            $this->assign('projects' , $projects);
            
            $userProject = db('UserProject')->where('user_id',$id)->find();
            $userProjects = $userProject ? explode(',', $userProject['projects']) : [];
            $this->assign('userProjects' , $userProjects);
            
            return $this->fetch();
            
        }
        
    }
    
    public function group(){
        
        $uid = session('admin_uid');
        $admin = db('user')->find($uid);
        if ($admin['pid'] != 0){
            return $this->formError('无权限操作');
        }
        
        $map['uid'] = $uid;
        $list = db('AuthGroup')->where($map)->order('sort','asc')->select();
        
        $this->assign('list' , $list);
        
        $this->assign('auth_rule_type' , config('dev.auth_rule_type'));
        return $this->fetch();
        
    }
    
    public function groupUpdate(){
        
        $request = Request::instance();
        if ($request->isPost()){
            
            $uid = session('admin_uid');
            $post = $request->post();
            
            // 检验重复
            if ($post['id']){
                $where = "(`title` = '".$post['title']."' OR `name` = '".$post['name']."') and `id` != ".$post['id'];
            }else{
                $where = "(`title` = '".$post['title']."' OR `name` = '".$post['name']."')";
            }
            $check = db('AuthGroup')->where('uid' , $uid)->where($where)->count();
            
            if ($post['id']){
                $res = AuthGroup::update($post);
            }else{
                $post['uid'] = $uid;
                $res = AuthGroup::create($post);
            }
            if ($res){
                return $this->formSuccess('操作成功' , url('admin/system/group'));
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
    
    public function groupStatus(){
        
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
    
    public function groupRules(){
        
        $request = Request::instance();
        if ($request->isPost()){
            $data = $request->post();
            
            $group = AuthGroup::get($data['group_id']);
            $rules = isset($data['rules']) ? implode(',', $data['rules']) : '';
            $group->rules = $rules;
            $res = $group->save();
            if ($res){
                // 重置缓存
                return $this->formSuccess('操作成功' , url('admin/system/group'));
            }else{
                return $this->formError('操作失败');
            }
            
        }else{
            
            $id = input('id');
            $type = input('type' , 'pc');
            $authRuleType = config('dev.auth_rule_type');
            
            // 查找用户角色
            $group = db('AuthGroup')->find($id);
            $groupRules = explode(',', $group['rules']);
            $this->assign('group' , $group);
            $this->assign('groupRules' , $groupRules);
            
            // 查找用户的组件
            $userId = $group['uid'];
            $userComponentIds = [0];
            $user = db('User')->find($userId);
            $userComponent = db('UserComponent')->where('status',1)->select();
            foreach ($userComponent as $k=>$v){
                $userComponentIds[] = $v['id'];
            }
            $component = db('authComponent')->where('id' , 'in' , $userComponentIds)->select();
            $components = [];
            foreach ($component as $v){
                $components[$v['id']] = $v['title'];
            }
            $this->assign('components' , $components);
            
            // 查找所拥有的权限
            $uid = (session('admin_pid') == 0) ? session('admin_uid') : session('admin_pid');
            $pUserGroup = db('authUserGroup')->find($uid);
            $pGroup = db('AuthGroup')->find($pUserGroup['group_id']);
            $pGroupRules = explode(',' , $pGroup['rules']);
            $mapRule['id'] = ['in' , $pGroupRules];
            $mapRule['status'] = 1;
            $mapRule['component_id'] = ['in' , $userComponentIds];
            $rule = db('AuthRule')->where($mapRule)->order('sort' , 'asc')->select();
            
            // 权限按类型分类
            $rules = [];
            foreach ($rule as $v){
                $rules[$v['type']][] = $v;
            }
            
            $types = [];
            foreach ($authRuleType as $k=>$v){
                if ($type == $k){
                    $typeData['show'] = 'show';
                }else{
                    $typeData['show'] = '';
                }
                $typeData['name'] = $v;
                $ruleData = isset($rules[$k])? $rules[$k] : null;
                
                // 权限按组件分类
                $ruleDataC = [];
                foreach ($ruleData as $v){
                    $ruleDataC[$v['component_id']][] = $v;
                }
                // 分级
                foreach ($ruleDataC as $k=>$v){
                    $ruleDataC[$k] = UnLimitTree::unlimitedForLevel($v);
                }
                
                $typeData['rules'] = $ruleDataC;
                $types[] = $typeData;
            }
            
            $this->assign('types' , $types);
            return $this->fetch();
        }
    }
    
    public function component(){
       
        $uid = session('admin_uid');
        $admin = db('user')->find($uid);
        if ($admin['pid'] != 0){
            return $this->formError('无权限操作');
        }
        
        $map['a.user_id'] = $uid;
        $list = db('UserComponent')->alias('a')
                ->join('t_auth_component b' , 'a.component_id = b.id')
                ->where($map)
                ->order('b.sort' ,'asc')
                ->field('a.* ,b.title title')
                ->select();
        
        $this->assign('list' , $list);
        return $this->fetch();
    }
    
    public function componentApply(){
        
        $request = Request::instance();
        if ($request->isPost()){
            
        }else{
            $uid = session('admin_uid');
            $admin = db('user')->find($uid);
            if ($admin['pid'] != 0){
                return $this->formError('无权限操作');
            }
            
            $map['user_id'] = $uid;
            $userComponentIds = db('UserComponent')->where($map)->column('component_id');
            
            if ($userComponentIds){
                $mapC['id'] = ['not in' , $userComponentIds];
            }
            
            $mapC['status'] = 1;
            $components = db('AuthComponent')->where($mapC)->select();
            
            $this->assign('list' , $components);
            return $this->fetch();
        }
        
    }
    
    public function componentApplySubmit(){
        
        $uid = session('admin_uid');
        $admin = db('user')->find($uid);
        if ($admin['pid'] != 0){
            return $this->formError('无权限操作');
        }
        
        $id = input('id',0);
        if ($id){
            // 查找是否申请过
            
            $map['user_id'] = $uid;
            $map['component_id'] = $id;
            $map['status'] = ['EGT' , 0];
            
            $isApply = db('UserComponent')->where($map)->count();
            if ($isApply > 0){
                return $this->formError('已经申请过，请不要重复申请');
            }
            
            $data['user_id'] = $uid;
            $data['component_id'] = $id;
            $data['status'] = 0;
            $data['deadline'] = 0;
            
            $res = UserComponent::create($data);
            if ($res){
                return $this->formSuccess('申请成功' , url('component'));
            }else{
                return $this->formError('申请失败');
            }
        }
    }
    
    public function projectAdd(){
        
        $uid = (session('admin_pid') == 0) ? session('admin_uid') : session('admin_pid');
        
        $request = Request::instance();
        if ($request->isPost()){
            $post = $request->post();
            
            $check = db('project')->where('title' , $post['title'])->whereOr('name' , $post['name'])->count();
            if ($check > 0){
                return $this->formError('存在相同名称或者代号的项目，请重新添加');
            }else{
                $post['uid'] = $uid;
                $res = Project::create($post);
                if ($res){
                    cache_clear('user_project_list' , $uid);
                    return $this->formSuccess('添加成功');
                }else{
                    return $this->formError('添加失败');
                }
            }
            
            
        }else{
            
            return $this->fetch();
        }
    }

}