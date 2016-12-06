<?php
// 管理员管理
class UsersAction extends AdminAction{
    public function index(){
        import("ORG.Util.Page");
		
		$AdminU = M('users');
		
		$count  = $AdminU->count(); // 查询满足要求的总记录数   
		$Page = new Page($count,$this->pagesize); // 实例化分页类传入总记录数和每页显示的记录数   
		$show = $Page->show(); // 分页显示输出
		   
		$fields = "id,user_name,u_group_id,real_name,is_ban,times,area_name,is_kf,qq,phone,user_word";
		$order = "id ASC,u_group_id DESC";
		
		$list = $AdminU->field($fields)->order($order)->limit($Page->firstRow.','.$Page->listRows)->select();

		$AdminUserList = $list;
		
		$GroupArr = get_group_data();
		foreach($AdminUserList as $key => $v){
			$AdminUserList[$key]['groupname'] = $GroupArr[$v['u_group_id']]['name'];
		}

		$this->assign('position', '管理员管理');
		$this->assign('pagebar', $show);
		$this->assign('admin_list', $AdminUserList);
		$this->assign('group_list', $GroupArr);
        $this->display();
    }

    public function addAdmin(){
	
		$data = $_POST;

		if(!isset($_POST['uid'])){//新增
			foreach($data as $key => $v){
				if($key == "user_pass") $data[$key] = md5(strtolower($data['user_pass']));
				else $data[$key] = EnHtml($v);
			}
			$data['area_name'] = M("area")->getFieldById(1,'name');
			$newid = M('users')->add($data);
			if(!$newid>0){
				alogs("UsersAdd",$newid,0,'管理员添加失败！');//管理员操作日志
				$this->error('添加失败，请确认填入数据正确');
				exit;
			}
			alogs("usersAdd",$newid,1,'管理员添加成功！');//管理员操作日志
			$this->assign('jumpUrl', U('/admin/Adminuser/'));
			$this->success('管理员添加成功');
		}else{
			$data['id'] = intval($_POST['uid']);
			$data['user_pass'] = trim($data['user_pass']);
			if( empty($data['user_pass']) ) unset($data['user_pass']);
			foreach($data as $key => $v){
				if($key == "user_pass") $data[$key] = md5(strtolower($data['user_pass']));
				else $data[$key] = EnHtml($v);
			}
			$newid = M('users')->save($data);
			if(!$newid>0){
				alogs("UsersEdit",$newid,0,'管理员修改失败！');//管理员操作日志
				$this->error('修改失败，数据没有改动或者改动未成功');
				exit;
			}
			alogs("UsersEdit",$newid,1,'管理员修改成功！');//管理员操作日志
			$this->assign('jumpUrl', U('/admin/Adminuser/'));
			$this->success('管理员修改成功');
		}
		
    }



    public function doDelete(){
		$id=$_REQUEST['idarr'];
		$delnum = M('users')->where("id in ({$id})")->delete(); 

		if($delnum){
			alogs("UsersDel",$newid,1,'管理员删除失败！');//管理员操作日志
			$this->success("管理员删除成功",'',$id);
		}else{
			alogs("UsersDel",$newid,0,'管理员删除失败！');//管理员操作日志
			$this->success("管理员删除失败");
		}
		
    }
	
	public function header(){
		$kfuid = intval($_GET['id']);
		$this->assign("kfuid",$kfuid);
		$this->display();
	}
	
	public function memberheaderuplad(){
		$uid = intval($_GET['uid']) + 10000000;
		if($uid<=10000000) exit;
		else{
			alogs("UsersEditHead",0,0,'编辑管理员头像！');//管理员操作日志
			redirect(__ROOT__."/res/header/upload.php?uid={$uid}");
		}
		exit;
	}


}
?>
