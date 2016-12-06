base 框架核心
common 常用函数库
conf 系统级配置目录
data 缓存文件目录（可读写权限）
files 上传文件目录（可读写权限）
lib 系统类库所在目录（lib/action/Home/YnwAction.class.php可读写权限）
public 公共静态页
res 公共插件目录
tpl 系统模板目录
.htaccess APACHE 重写规则
error.html 404及系统错误页面
favicon.ico 网站ICO图标
index.php 网站入口
web.config IIS 配置文件

网站后台
http://域名/ynw/admin/
为了安全着想非登录状态访问http://域名/admin/返回404错误

管理员
账户：admin
密码：admin
口令：admin
以上密码及口令，生产环境上务必要更改

服务器安装
如果是IIS需要需要URL重写支持，重新规则参考根目录下的 web.config
如果是Apache服务器也需要rewrite模块开启
否则网站除了首页其它页面都无法打开

修改/index.php里的DEBUG选项为TRUE，系统任何问题都是显示，否则都会跳转到404错误页


网站的所有模板都在根目录下的/tpl里面
Home为前台模板
Admin为后台模板
Member为会员中心模板

用户可以自行设计复合企业的模板，默认模板都是default
无论前台后台还是会员中心，Res里存放的是css,js和img文件
模板目录里Public是公共头公共尾文件所在目录