<?php
class AclAction extends AdminAction{
    public function index(){
        import("ORG.Util.Page");
		
		$Acl = M('users_acl');		
		
		$count  = $Acl->count(); // 查询满足要求的总记录数   
		$Page = new Page($count,$this->pagesize); // 实例化分页类传入总记录数和每页显示的记录数   
		$show = $Page->show(); // 分页显示输出
		   
		$fields = "id,name,intro";
		$order = "`id` ASC";
		
		$list = $Acl->field($fields)->order($order)->limit($Page->firstRow.','.$Page->listRows)->select();
		$AclList = $list;
		
		$this->assign('position', '用户组权限');
		$this->assign('pagebar', $show);
		$this->assign('acl_list', $AclList);
        $this->display();
    }


    public function add(){
		require C('APP_ROOT')."conf/Admin/acl.php";
		$this->assign('acl_list', $acl_inc);
        $this->display();
    }


    public function doAdd(){
		$data['controller'] = serialize($_POST['model']);
		$data['groupname'] = EnHtml($_POST['groupname']);
		$newid = M('users_acl')->add($data);
		if(!$newid>0){
			alogs("AclAdd",$newid,0,'添加失败，请确认填入数据正确！');//管理员操作日志
			$this->error('添加失败，请确认填入数据正确');
			exit;
		}
		alogs("AclAdd",$newid,1,'用户组权限添加成功！');//管理员操作日志
		S("ACL_all",NULL);
		$this->assign('jumpUrl', U('/admin/acl/'));
		$this->success('用户组权限添加成功');
		
    }

    public function edit(){
		require C('APP_ROOT')."conf/Admin/acl.php";
		$aid = intval($_GET['a_id']);
		if($aid==0){
			$this->assign('jumpUrl', U('/Acl/'));
			$this->error('参数错误');
		}

		$data = D('UsersAcl')->find($aid);
		$data['controller'] = unserialize($data['controller']);

		$this->assign('aid', $aid);
		$this->assign('acldata', $data);
		$this->assign('acl_list', $acl_inc);
        $this->display();
    }


    public function doEdit(){
		$aid = intval($_POST['aid']);
		if($aid==0){
			$this->assign('jumpUrl', U('/admin/Acl/'));
			$this->error('参数错误');
		}

		$data['id'] = intval($_POST['aid']);
		$data['controller'] = serialize($_POST['model']);
		$data['name'] = EnHtml($_POST['name']);
		$data['intro'] = EnHtml($_POST['intro']);

		$rid = M('users_acl')->save($data);
		if($rid){
			alogs("AclEdit",$rid,1,'用户组权限修改成功！');//管理员操作日志
			S("ACL_".$aid,NULL);
			S("ACL_all",NULL);
			$this->assign('jumpUrl', U('/admin/Acl/'));
			$this->success('修改成功');
		}else{
			alogs("AclEdit",$rid,0,'用户组权限修改失败或者数据未做修改！');//管理员操作日志
			$this->assign('jumpUrl', U('/admin/Acl/'));
			$this->error('修改失败或者数据未做修改');
		}
    }



    public function doDelete(){

		$id = $_REQUEST['idarr'];
		$have_user = M('users')->where("u_group_id in ({$id})")->select();
		if(is_array($have_user) && count($have_user) > 0){
			alogs("Acl",0,0,'该用户组权限下有会员，不能删除！');//管理员操作日志
			$this->error(L('该用户组权限下有会员，不能删除'));
		}
		
		$delnum = M('acl')->where("id in ({$id})")->delete(); 

		if($delnum){
			alogs("AclDel",0,1,'用户组权限删除成功！');//管理员操作日志
			$this->success(L('用户组权限删除成功'),'',$id);
		}else{
			alogs("AclDel",0,0,'用户组权限删除失败！');//管理员操作日志
			$this->error(L('用户组权限删除失败'));
		}
		
    }

}
?>