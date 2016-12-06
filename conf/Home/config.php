<?php
return array(
    'TOKEN_ON'=>false,  // 是否开启令牌验证
    'TOKEN_NAME'=>'__hash__',    // 令牌验证的表单隐藏字段名称
    'TOKEN_TYPE'=>'md5',  //令牌哈希验证规则 默认为MD5
    'TOKEN_RESET'=>false,  //令牌验证出错后是否重置令牌 默认为true

	'HTML_CACHE_ON'=>false,
	
	'HTML_CACHE_RULES'=> array('*'=>array('{:module}/{:action}/{$_SERVER.REQUEST_URI|md5}',86400),),

	'TMPL_ACTION_ERROR' =>"Public:error",//操作错误提示
	'TMPL_ACTION_SUCCESS' =>"Public:success",//操作正确提示
);
?>