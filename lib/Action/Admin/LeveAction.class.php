<?php

class LeveAction extends AdminAction
{
    public function index()
    {
		$leveconfig = FS("data/conf/grade");

		$this->assign('leve',$leveconfig);
        $this->display();
    }

    public function save()
    {
		alogs("Leve",0,1,'执行了信用积分等级数据编辑操作！');//管理员操作日志
		FS("grade",$_POST['leve'],"data/conf/");
		$this->success("操作成功",__URL__."/index/");
    }

    public function invest()
    {
        $leveconfig = FS("data/conf/level");

        $this->assign('leve',$leveconfig);
        $this->display();
    }

    public function investsave()
    {
		alogs("Leve",0,2,'执行了投资积分等级数据编辑操作！');//管理员操作日志
        FS("level",$_POST['leve'],"data/conf/");
        $this->success("操作成功",__URL__."/invest/");
    }
}
?>