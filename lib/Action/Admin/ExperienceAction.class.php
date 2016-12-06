<?php
class ExperienceAction extends AdminAction{

    public function index(){
		$field= true;
		$map=array();
		$map['status'] = $_GET['status'];
		if($_REQUEST['type']) $map['type'] = intval($_REQUEST['type']);
		//$this->_list(D('trade'),$field,$map,'id','DESC');
		
		
		//分页处理
		import("ORG.Util.Page");
		$count = M('trade')->count('id');
		$p = new Page($count, $this->pagesize);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理
		
		$field= 't.*,m.user_name';
		$list = M('trade t')->field($field)->join("{$this->pre}member m ON m.id=t.trade_uid")->where($map)->limit($Lsql)->order("t.id DESC")->select();

		$f_type =C('FEEDBACK_TYPE');
		foreach($list as $key=>$v){
			$list[$key]['type'] = $f_type[$v['type']];
		}
		
		$this->assign("list",$list);		
		$this->assign("f_type",C('TRADE_TYPE'));
        $this->display();
    }


    public function audit() {
        $this->display();
    }


}
?>