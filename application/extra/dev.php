<?php
return [
  
  'code_name' => '老司机',
    
  'root_uid' => 1, 
    
   'admin_prefix' => 'admin',
   
   'auth_rule_type' => [
       'pc' => 'pc端',
       'app' => 'app端',
   ],
    
    'auth_component_level' => [
        '0' => '基础',
        '1' => '普通',
        '2' => '高阶'
    ],
    
   'action' => [
       'index' => [
           'name' => '首页',
           'url'  => 'dev/index/index',
           'code' => 'home',
           'child' => [
               'index-index' => [
                   'name' => '控制台',
                   'url'  => 'dev/index/index'
               ]
           ]
           
       ],
       'user' => [
           'name' => '成员',
           'url'  => 'dev/user/index',
           'code' => 'user',
           'child' => [
               'user-index' => [
                   'name' => '成员列表',
                   'url'  => 'dev/user/index'
               ],
               'user-update' => [
                   'name' => '成员更新',
                   'url'  => 'dev/user/update'
               ],
               'user-group' => [
                   'name' => '设置成员角色',
                   'url'  => ''
               ],
               'user-project'=>[
                   'name' => '用户项目',
                   'url'  => 'dev/user/project'
               ]
           ]
       ],
       'group' => [
           'name' => '角色',
           'url' => 'dev/group/index',
           'code' => 'users',
           'child' => [
               'group-index' => [
                   'name' => '角色列表',
                   'url'  => 'dev/group/index'
               ],
               'group-update' => [
                   'name' => '角色更新',
                   'url'  => 'dev/group/update'
               ],
               'group-rule' => [
                   'name' => '设置角色权限',
                   'url'  => ''
               ]
           ]
       ],
       'rule' => [
           'name' => '权限',
           'url'  => 'dev/rule/index',
           'code' => 'lock',
           'child' => [
               'rule-lists' => [
                 'name' => '权限列表',
                 'url'  => 'dev/rule/lists'
               ],
               'rule-update' => [
                   'name' => '权限更新',
                   'url'  => 'dev/rule/update'  
               ]
           ]
       ],
       'theme' => [
          'name' => '主题',
           'url' => 'dev/theme/index',
           'code' => 'photo',
           'child' => [
               'theme-update'=>[
                   'name' => '主题更新',
                   'url'  => 'dev/theme/update'
               ],
               'theme-template'=>[
                   'name' => '主题模板',
                   'url'  => 'dev/theme/template'
               ]
           ]
       ],
       'component' => [
           'name' => '组件',
           'url'  => 'dev/component/index',
           'code' => 'anchor',
           'child' => [
               'component-index' => [
                   'name' => '组件列表',
                   'url'  => 'dev/component/index'
               ],
               'component-update' => [
                   'name' => '组件更新',
                   'url'  => 'dev/component/update'
               ],
               'component-apply' => [
                   'name' => '组件申请审核',
                   'url'  => 'dev/component/apply'
               ]
           ]
       ]
   ],
];