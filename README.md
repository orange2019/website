### 开发文档

#### 摘要

+ 本系统基于thinkphp 5.0进行开发 [ThinkPHP5.0完全开发手册](http://www.kancloud.cn/manual/thinkphp5/118003)
+ php包管理采用composer [composer中文网](http://docs.phpcomposer.com/)
+ 前端资源采用npm进行管理 [nodejs](https://nodejs.org/)
+ 项目自动化流程 Gulp + Browersync
+ js开发可采用CommonJS规范
+ css编译采用 less [less中文网](http://lesscss.cn/)
+ 前端css部分引用 [AmazeUI](http://amazeui.org/css/)


#### 项目安装部署

项目仓库地址  

  + HTTPS `https://github.com/niklauslu/orange.git`
  + SSH `git@github.com:niklauslu/orange.git`  


  1.克隆项目到本地文件夹 `my-project`
  2.打开项目文件夹进行composer包和npm包获取
  ```
  composer install
  npm install
  ```
  3.启动项目
  ```
  gulp start
  ```
  ps: composer速度慢可配置composer文中镜像，npm慢可改用cnpm,方法自行百度  


#### 项目结构说明  

项目采用thinkphp的单模块形式，项目模块划分利用多级控制器来控制，具体参看thinkphp开发手册【[架构-模块设计](http://www.kancloud.cn/manual/thinkphp5/118013)】和【[控制器-多级控制器](http://www.kancloud.cn/manual/thinkphp5/118054)】  

```txt
|-- application 应用目录 （后台开发在这里）
|  |--  controller                  控制器
|  |  |-- dev                       开发模块控制器文件夹
|  |--  model                       模型
|  |--  view                        模板文件(不用管)
|  |--  extra                       配置文件
|  |--  validate                    验证器
|  |----  command.php               命令行工具配置文件 （不用）
|  |----  common.php                自定义函数
|  |----  config.php                公共配置
|  |----  database.php              数据库配置
│  |----  tags.php                  应用行为扩展定义文件
│  |----  route.php                 路由配置文件 （用不到）
|-- extend                          扩展类库目录（可定义）建议采用composer
|-- public                          web目录，域名，ip解析到这个目录
|  |--  asset                       静态资源文件，自动生成
|  |----  index.php                 伪静态
|  |----  .htaccess                 Apache伪静态
|-- src                             静态资源构建目录（前端开发在这里）
|  |-- html
|  |-- js
|  |-- less
|  |-- images
|--  doc                            文档目录（文档都写到这里）
|--  runtime                        
|  |--  log                         日志文件，本地查看
|----  .gitignore                   例外文件
|----  composer.json                composer配置文件
|----  gulpfile.js                  gulp配置文件
|----  package.json                 npm配置文件
```

### 功能开发流程

+ 配置功能权限，分配角色权限  
  首先到开发者后台添加功能权限，添加权限时将是否显示设置为`否`（测试环境另当别论），然后给相应系统管理添加权限

+ 新建对应代码文件  
  在`application/controller`文件下新建对用的`controller/action`,开发测试可以使用链接进行访问

+ 修改权限可见，上线  
  功能测试通过后将说添加功能权限可显示的改为`是`

### 后台开发注意事项

+ 所有控制器继承`saas.php`
+ 返回数据方法 `success`->`formSuccess`,`error`->'formError',兼容ajax和跳转
+ 直接返回json的方法 'jsonSuccess'，'jsonError'
+ 数据库查询建议用db()方法，返回数组，数据库操作建议用model模型，返回对象

### 前端开发说明  

+ `pjax`页面和模板`include`
  后台页面模板结构demo `src/html/index/demo.html`
  ```html
  <!DOCTYPE html>
  <html>
    <head>
      <!--include "../include/head.html"-->
      <title>{$title}</title>
      <!--include "../include/css.html"-->

    </head>
    <body>
      <div class="container" id="pjax-container">
        <div class="container" id="pjax-content">
          <!--include "../include/navbar.html"-->

          <div class="container-content">
            <!--include "../include/top.html"-->
            <div class="container-wrap">
              <!-- 在这里写页面代码 -->
            </div>
          </div>
        </div>
      </div>

      <!--include "../include/script.html"-->
    </body>
  </html>
  ```

### 缓存

为了提高程序效率，部分数据需要缓存，使用cache函数时写明文档，以便其他人员查看，文档路径`doc/cache.md`  

### js require
js支持require引入，例如jquery-pjax  
1.首先我用npm安装jQuery-pjax `npm install jquery-pjax --save`  
2.在app.js中引入
```
var pjax = require('jquery-pjax');
```
就可以使用了，该方法适用于符合commonjs规范的js库

### less import
less默认引入了amazeui的less文件和font-awesome.less
使用amazeui css命名空间改为 `we` 写法就改为`we-*`  
开发是要新建less文件，记得在app.less import ,例如我建立一个`demo.less`,在app.less中加入
```less
@import "demo";
```
