<?php

class AgeAction extends AdminAction
{
    /**
    +----------------------------------------------------------
    * 默认操作
    +----------------------------------------------------------
    */
    public function index()
    {
		$ageconfig = FS("data/conf/age");

		$this->assign('leve',$ageconfig);
        $this->display();
    }
    public function save()
    {
		FS("age",$_POST['leve'],"data/conf/");
		alogs("Age",0,1,'会员年龄别称操作成功！');//管理员操作日志
		$this->success("操作成功",__URL__."/index/");
    }
}
?>