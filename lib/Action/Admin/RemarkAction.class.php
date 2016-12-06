<?php

class RemarkAction extends AdminAction
{
    public function index(){
        if($_GET['user_name']) $search['user_name'] = text($_GET['user_name']);
        else $search=array();

        import("ORG.Util.Page");
        $count = M('member_remark')->where($search)->count();
        $p = new Page($count, $size);
        $page = $p->show();
        $Lsql = "{$p->firstRow},{$p->listRows}";

        $list = M('member_remark')->where($search)->limit($Lsql)->order('id DESC')->select();
        $this->assign("list",$list);
        $this->assign("pagebar",$page);
        $this->assign("search", $search);
        $this->display();
    }

    public function edit(){
        $data['user_name'] = text($_GET['user_name']);
        $this->assign("vo", $data);
        $this->display();
    }
	
    public function doEdit(){
        $data['user_name'] = text($_POST['user_name']);
        $data['user_id'] = M('member')->getFieldByUser_name($data['user_name'],"id");
        if(!$data['user_id']) $this->error("找不到你要备注的会员");

        $data['remark'] = text($_POST['remark']);
        if(!$data['remark']) $this->error("备注信息不可为空");

        $data['admin_id'] = $_SESSION['admin_id'];
        $data['admin_real_name'] = $_SESSION['admin_user_name'];
        $data['add_time'] = time();

		
        $newid = M('member_remark')->add($data);
        if($newid){
			alogs("Remark",$newid,1,'成功执行了备注信息的添加操作！');//管理员操作日志
			$this->success("添加成功");
        }else{
			alogs("Remark",$newid,0,'执行备注信息的添加操作失败！');//管理员操作日志
			$this->error("添加失败");
		}
    }
}
?>