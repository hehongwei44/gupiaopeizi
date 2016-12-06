<?php
class HomsAction extends AdminAction{

    public function index(){
		$map['uid'] = intval($_GET['status']) == 0 ? 0 : array('gt',0);

		if($_GET['type']){
			$map['type'] = intval($_GET['type']);
		}

		//分页处理
		import("ORG.Util.Page");
		$count = M('Homs')->where($map)->count('id');
		$p = new Page($count, $this->pagesize);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";

		$list = M('Homs')->where($map)->limit($Lsql)->order("id DESC")->select();

        $this->assign("type", C('TRADE_TYPE'));
        $this->assign("list", $list);
        $this->display();
    }

    public function add(){
    	if($_POST){
            $_POST['date'] = time();
    		if(D('Homs')->add($_POST)){
    			$this->success('恒生账户添加成功，现在可以为配资的会员分配了');
    		}
    	}else{
    		$data['type'] = C('TRADE_TYPE');
    		$this->assign('data',$data);
    		$this->display();
    	}

    }

    public function edit(){
        $this->display();
    }

}
?>