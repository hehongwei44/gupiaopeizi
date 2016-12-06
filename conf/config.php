<?php
return array(
	'DEFAULT_GROUP'     =>'Home',
    'APP_GROUP_LIST'    => 'Home,Admin,Member,Mobile',
    'DEFAULT_THEME'     =>'default',
	'TMPL_DETECT_THEME' => true,
	'LANG_SWITCH_ON'	=>false,
    'URL_MODEL'=>2,
	'URL_CASE_INSENSITIVE'=>true,
    'TMPL_CACHE_ON'     => FALSE,
    'TMPL_STRIP_SPACE'  => false,

    'DATA_CACHE_TIME'  => 0,
    'DATA_CACHE_SUBDIR' => true,

	'APP_ROOT'=>str_replace(array('\\','conf','config.php','//'), array('/','/','','/'), dirname(__FILE__)),
	'WEB_ROOT'=>str_replace("\\", '/', substr(str_replace('\\conf\\', '/', dirname(__FILE__)),0,-4)),
	'WEB_URL'=>"http://".$_SERVER['HTTP_HOST'],
	'CUR_URI'=>$_SERVER['REQUEST_URI'],
	'URL_HTML_SUFFIX'=>".html",


	'ERROR_PAGE'	=>'/error.html',
	'LOAD_EXT_CONFIG' => 'crons,business',

	//'SYS_URL'	=>array('admin','borrow','member','invest','tinvest','tool','feedback','service','bid','Market','main','mcenter','debt','m','guide','fund','trade','agent'),
	//'EXC_URL'	=>array('invest/tool/index.html','borrow/tool/index.html','borrow/tool/tool2.html','borrow/tool/tool2.html'),


	//数据库配置
	'DB_TYPE'           => 'mysql',
	'DB_HOST'           => 'localhost',
	'DB_NAME'           => 'com_ynwstock_hn',
	'DB_USER'           => 'haoniu',
	'DB_PWD'            => 'H!@#niu14',
	'DB_PORT'           => '3306',
	'DB_PREFIX'         => 'ynw_',
	//'DB_PARAMS'			=>array('persist'=>true),


	'URL_ROUTER_ON'		=>true,
	'URL_ROUTE_RULES'	=>array(
		'/^help\/([a-zA-z]+).html$/' => 'Home/article/help',
		'/^about\/([a-zA-z]+).html$/' => 'Home/article/about',
		'/^news\/(\d+).html$/' => 'Home/article/news?id=:1',
		'/^member\/([a-zA-z]+).html$/' => 'Member/main/:1',
		'/^member\/([a-zA-z]+).html\?(.*)$/' => 'Member/main/:1?:2',
	),
)
?>