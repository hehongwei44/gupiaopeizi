=================
说明书概述
=================
版本：$Id: readme.txt 159 2010-08-16 07:58:42Z horseluke@126.com $
程序名称：剥离UCenter的flash上传头像程序为单独程序
更新地址：http://code.google.com/p/discuzplugin-hl/

=================
！！！ 警告 ！！！
=================
该文件有代码包含了康盛创想（北京）科技有限公司Discuz!/UCenter的代码。根据相关协议的规定：
    “禁止在 Discuz! / UCenter 的整体或任何部分基础上以发展任何派生版本、修改版本或第三方版本用于重新分发。”
故在此声明如下：
    本程序仅为作者学习和研究软件内含的设计思想和原理而作，不以盈利为目的，同时也无意侵犯第三方软件作者/公司的权益。
    如若侵犯权益，请发邮件告知。在本人接获通知的48小时之内将会把自己所发布的代码进行撤回操作。
    同时提醒第三方下载者和使用者使用这些代码时考虑本程序的法律风险，第三方下载者和使用者的一切行为与本人无关。

Discuz!/UCenter头文件注释：
(C)2001-2009 Comsenz Inc.
This is NOT a freeware, use is subject to license terms


=================
概述
=================
欢迎查看Horse Luke（中文名称：微碌）所编写/修改的程序。

本程序受朋友邀请所写，主要是剥离UCenter的Flash头像上传程序为独立的程序，经过初步测试有效。
其中的swf文件来源自Discuz!NT程序。原因是Discuz!NT程序的同功能Flash文件，可指定任意的头像上传入口（即uc_api + 当前执行脚本文件名）。

本程序允许在PHP5.0及以上版本运行，编码为UTF-8。在Windows Server 2003 + IIS平台下测试通过。
本程序需要开启SPL库（因为config类继承了SPL的ArrayObject类；PHP5.0默认开启了SPL）

由于版权原因，本程序不建议使用到生产环境，仅用于了解其flash头像上传的原理。

类似的剥离请查看别人的成果：http://www.phpchina.com/bbs/thread-187941-1-1.html

=================
使用方法
=================
1、本程序部分遵循MVC架构开发，upload.php是该程序的指定唯一入口。
你可以将upload.php改为任意名字，而不影响该程序的运行。

2、打开upload.php，对config数组进行修改。
请使用Editplus等软件打开，不要使用Windows自带的记事本打开；
否则当保存时，windows记事本将自动往该文件加utf8 bom，从而可能导致本脚本无法运行（因为本脚本的编码是utf8）！
config数组含义：
（1）'tmpdir'：临时存放第一次上传文件的文件夹（相对于upload.php的位置而言），开头和结尾请不要加反斜杆。
请务必将该文件夹和upload.php存放于同一分区，同时不要超过upload.php所运行的网址的顶端目录，并且设置为可读可写，否则将出错！
（2）'avatardir'：存储头像的文件夹（相对于upload.php的位置而言），开头和结尾请不要加反斜杆。
请务必将该文件夹和upload.php存放于同一分区，同时不要超过upload.php所运行的网址的顶端目录，并且设置为可读可写，否则将出错！
（3）'authkey'：通讯密钥，推荐进行修改。
此项目必须填写，否则脚本将无法运行。
（4）'debug'：是否开启debug记录？
开启后，错误日志将存储在upload.php所在目录的Log文件夹下。
你可以通过修改upload.php文件的下面代码来更改日志位置：
Inter_Error::$conf['logDir'] = dirname(__FILE__). '/Log';
Inter_Error类的其它说明请参考对应附录。
（5）'uploadsize'：上传图片文件的最大值，单位是KB。
请勿超过php.ini所允许的最大上传值，否则flash将在上传过程中出现逻辑混乱而无法运行。
（6）'uc_api'：运行该脚本的网址，末尾请不要加反斜杠（比如http://www.aaa.com/avatar/upload）。
如果为空，系统将自动生成一个。但自动生成的话可能会有错误，导致无法上传头像。如果遇到此情况，请修改这里的值。


其它没提到的设置，属于系统设置。请不要随便进行修改，否则将引起程序安全隐患！

3、在浏览器输入：
http://127.0.0.1/uc_avatar_upload/upload.php?uid=9
其中uid必须存在，同时必须要指定为一个正整数。一切顺利的话将看到flash头像上传界面。
如果这么输入：
http://127.0.0.1/uc_avatar_upload/upload.php?uid=9&returnhtml=0
则将返回一段json数据，里面包含了创建flash所需要的变量。
（此时需要开启服务器对json的支持，否则php因为无法使用json_encode函数将返回fatal error错误）

4、测试头像上传。

=================
后续开发和思考建议
=================
1、本程序只有一个控制器：Controller_AvatarFlashUpload类（Lib/Controller/AvatarFlashUpload.php）。
该类有3个action：
（1）showuploadAction：获取显示上传flash的代码
（2）uploadavatarAction：头像上传第一步，上传原文件到临时文件夹
（3）rectavatarAction：头像上传第二步，上传到头像存储位置
建议开发者在执行这些action之前（甚至是实例化该例之前），对uid进行权限判断。

2、你可以利用Controller_AvatarFlashUpload类中的clear_avatar_file方法一次性清除指定uid的头像。

3、如果你想实时了解上传的结果，可以在Lib/Controller文件夹下面将如下代码保存为“showuploadAction.html”
----->>>>>代码开始<<<<<-----
<?php
!defined('IN_INTER') && exit('Fobbiden!');
$avatarsize = array( 1 => 'big', 2 => 'middle', 3 => 'small');
$avatartype = array( 'real', 'virtual' );

foreach ( $avatarsize as $size ){
    foreach ( $avatartype as $type ){
        $avatarurlpath = $this->config->uc_api. '/'. $this->config->avatardir. '/'. $this->get_avatar_filepath($uid, $size, $type);
        $result .= '<div>Avatar Type:'. $type. ' & Avatar Size:'. $size .'</div>'.
                   '<div id="'. $size. '_' .$type. '">'.
                   '<img src="'. $avatarurlpath. '" onerror="this.onerror=null;this.src=\''. $this->config->uc_api. '/images/noavatar_'. $size. '.gif\'" />'.
                   '</div><br />';
    }
}

$result .= '<script type="text/javascript">
function updateavatar() {
	window.location.reload();
}
</script>';
----->>>>>代码结束<<<<<-----

然后在Lib/Controller/AvatarFlashUpload.php中的：
----->>>>>代码开始<<<<<-----
            return $result;
        } else {
            return array(
            'width', '450',
            'height', '253',
            'scale', 'exactfit',
            'src', $uc_avatarflash,
            'id', 'mycamera',
            'name', 'mycamera',
            'quality','high',
----->>>>>代码结束<<<<<-----
前面加上：
----->>>>>代码开始<<<<<-----
            require('showuploadAction.html');
----->>>>>代码结束<<<<<-----
即可实时查看结果了（上传成功会自动刷新）。

4、其它事项，请阅读文件最开始的“警告”。



=================
附录：Inter_Error类说明书
=================
版本：0Intro_Error.txt 113 2010-03-04 16:14:23Z horseluke@126.com
Inter_Error类下载和更新地址：http://code.google.com/p/horseluke-code/

<?php
//本类文件可单独使用。PHP版本要求：>=5.0.0。使用方法（代码示例）：
date_default_timezone_set('PRC');    //设置时区，PRC内容请替换为合适的时区，详细的请自查php手册。PHP版本 >= 5.1.0的时候一定要做这步骤，否则很可能会导致记录时间不准（除非在引用前已经设置时区）；版本 < 5.1.0则不需要。
require_once("Error.php");     //在适当的地方以require / require_once正确引用该文件
//然后接管PHP的错误处理机制
set_exception_handler(array('Inter_Error', 'exception_handler'));
set_error_handler(array('Inter_Error', 'error_handler'), E_ALL);
//然后可选择地使用如下方式进行设置（假如保持默认值，可以不需配置。默认值请查看Error.php里面关于静态属性$debugMode的说明）：
Inter_Error::$conf['debugMode'] = true;
Inter_Error::$conf['friendlyExceptionPage']='1234.htm';
Inter_Error::$conf['logType'] = 'simple';
Inter_Error::$conf['logDir'] = dirname(__FILE__).'/Log';

//可错误的代码，这时候就会调出Inter_Error来处理了
$variable1 = '1111';

function a(){
    b();
}

function b(){
    echo 1/0;
}

function c(){
	throw new exception('Exception Occur!');
}

a();


//假如代码没有错误，但是你又想看看一些变量值。那么就可以设置好Inter_Error::$conf['variables']，然后静态调用show_variables方法，以显示变量。
//注意：一旦有php代码出错，此方法会自动调用
/*
//以数组加入要检测的变量名即可。
Inter_Error::$conf['variables'] = array("_GET", "_POST", "_SESSION", "_COOKIE", "variable1", "variable2");
echo '<hr />';
Inter_Error::show_variables();
echo '<hr />';
*/

c();