### 缓存说明

#### 用户角色列表

+ name ：`user_group_list`
+ key : `uid` 管理员id
+ field : 'filed' 返回数组中的字段
+ 使用：
  + `controller--system/userGroup`
  + `controller--dev/user/group`

#### 用户项目列表
+ name ：`user_project_list`
+ key : `uid` 管理员id
+ status : 获取该状态的列表
+ 使用：
  + `controller--system/userProject`
  + `controller--apartment/project/update`

#### 栏目列表
+ name : 'category_info_list'
+ key : 'id' 栏目id
+ filed : 'field' 返回数据中的字段
+ 使用：
  + `controller--admin/category/edit`
  + `controller--admin/category/content`
  + `controller--admin/category/status`
  + `controller--admin/category/delete`
