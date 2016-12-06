<?php
class EmptyAction extends Action{
	public function index($name) {
		if(APP_DEBUG){
			throw_exception('请求的 '.MODULE_NAME.' 模块不存在！');
		}else{
			send_http_status(404);
			require(APP_ROOT.'/'.C('ERROR_PAGE'));
			exit;
		}	
	}
}