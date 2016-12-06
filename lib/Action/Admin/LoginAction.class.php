<?php

class LoginAction extends AdminAction
{
    /**
    +----------------------------------------------------------
    * 默认操作
    +----------------------------------------------------------
    */
    public function index()
    {
		$loginconfig = FS("data/conf/login");

		$this->assign('qq_config',$loginconfig['qq']);
		$this->assign('sina_config',$loginconfig['sina']);
		$this->assign('uc_config',$loginconfig['uc']);
		$this->assign('cookie_config',$loginconfig['cookie']);
        $this->display();
    }
    public function save()
    {	alogs("Login",0,1,'执行了登陆接口管理参数编辑操作！');//管理员操作日志
		FS("login",$_POST['login'],"data/conf/");
		$this->success("操作成功",__URL__."/index/");
    }
}
?>