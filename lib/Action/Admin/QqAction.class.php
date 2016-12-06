<?php

class QQAction extends AdminAction
{   
    //qq列表
    public function index()
    {
		import('ORG.Util.Page');
		$Qq = M('qq');
		
		if(empty($search)) $condition = "1";
		else $condition = $search;
		
		$count = $Qq->where($condition)->count();
		$Page = new Page($count,$this->pagesize);
		$show = $Page->show();
		
		$fields = ($fields=="")?"*":$fields;
		$order = ($order=="")?'qq_order DESC':$order;
		$list = $Qq->field($fields)->where("$condition and type = 0")->order($order)->limit($Page->firstRow.','.$Page->listRows)->select();
		
		$QqList = $list;
		foreach($QqList as $key => $v){
		    foreach($v as $key_s => $v_s){
			    if ($key_s == 'is_show'){
				    if($v_s == 1) $v_s="是";
					else $v_s = "否";
				}
				$QqList[$key][$key_s] = $v_s;
			}
		}
		
		$this->assign('qq_list',$QqList);
		$this->assign('pagebar',$show);
		$this->assign('position',"QQ在线客服");
		$this->display();
    }
	//添加、编辑qq
    public function addqq()
    {
				
		$data = $_POST;
		foreach($data as $key =>$v){
		    if(!empty($key)) $data[$key]=EnHtml($v);
		}
		
		if(!isset($_POST['fid'])){
		    $newid = M('qq')->add($data);
			if(!$newid>0){
			   alogs("QQ",$newid,0,'执行客服QQ的添加操作失败！');//管理员操作日志
		       $this->error('添加失败，请确认填入数据正确');
			   exit;
			}
			alogs("QQ",$newid,1,'执行了客服QQ的添加操作！');//管理员操作日志
			$this->assign('jumpUrl',U('/admin/QQ/index/'));
			$this->success('QQ客服添加成功');
		}else{
		    $data['id']=intval($_POST['fid']);
			$newid = M('qq')->save($data);
			if(!$newid>0){
				alogs("QQ",$newid,0,'执行客服QQ的编辑操作失败！');//管理员操作日志
			    $this->error('编辑失败，请确认填入数据正确');
				exit;
			}
			alogs("QQ",$newid,1,'执行了客服QQ的编辑操作！');//管理员操作日志
			$this->assign('jumpUrl',U('/admin/QQ/index/'));
			$this->success('QQ客服编辑成功');
		}
		
    }
	//删除qq
	public function dodeleteqq()
    {
		$data = $_POST;
		foreach($data as $key => $v){
			$data[$key] = EnHtml($v);
		}
		
		$idarray = $data['idarr'];
		
		$delnum = M('qq')->where("id in ({$idarray})")->delete(); 
		alogs("QQ",0,0,'执行了客服QQ的删除操作！');//管理员操作日志
		if($delnum){
			$a_data['success'] = $rid;
			$a_data['success_msg'] = "QQ在线客服删除成功";
			$a_data['aid'] = $idarray;
		}else{
			$a_data['success'] = 0;
			$a_data['error_msg'] = "QQ在线客服删除失败";
		}
		
		exit(json_encode($a_data));
	}
	//添加、修改qq群
	public function addqun()
    {
		alogs("QQ",0,0,'执行了客服QQ群的编辑操作！');//管理员操作日志		
		$data = $_POST;
		foreach($data as $key =>$v){
		    if(!empty($key)) $data[$key]=EnHtml($v);
		}
		
		if(!isset($_POST['fid'])){
		    $newid = M('qq')->add($data);
			if(!$newid>0){
				alogs("QQ",$newid,0,'执行客服QQ群的添加操作失败！');//管理员操作日志
		       $this->error('添加失败，请确认填入数据正确');
			   exit;
			}
			alogs("QQ",$newid,1,'执行了客服QQ群的添加操作！');//管理员操作日志
			$this->assign('jumpUrl',U('/admin/QQ/qun/'));
			$this->success('QQ群添加成功');
		}else{
		    $data['id']=intval($_POST['fid']);
			$newid = M('qq')->save($data);
			if(!$newid>0){
				alogs("QQ",$newid,0,'执行客服QQ群的编辑操作失败！');//管理员操作日志
			    $this->error('编辑失败，请确认填入数据正确');
				exit;
			}
			alogs("QQ",$newid,1,'执行了客服QQ群的编辑操作！');//管理员操作日志
			$this->assign('jumpUrl',U('/admin/QQ/qun/'));
			$this->success('QQ群编辑成功');
		}
		
    }
	//qq群列表
	 public function qun()
    {
		import('ORG.Util.Page');
		$Qq = M('qq');
		
		if(empty($search)) $condition = "1";
		else $condition = $search;
		
		$count = $Qq->where($condition)->count();
		$Page = new Page($count,$this->pagesize);
		$show = $Page->show();
		
		$fields = ($fields=="")?"*":$fields;
		$order = ($order=="")?'qq_order DESC':$order;
		$list = $Qq->field($fields)->where("$condition and type = 1")->order($order)->limit($Page->firstRow.','.$Page->listRows)->select();
		
		$QqList = $list;
		foreach($QqList as $key => $v){
		    foreach($v as $key_s => $v_s){
			    if ($key_s == 'is_show'){
				    if($v_s == 1) $v_s="是";
					else $v_s = "否";
				}
				$QqList[$key][$key_s] = $v_s;
			}
		}
		
		$this->assign('qq_list',$QqList);
		$this->assign('pagebar',$show);
		$this->assign('position',"QQ在线客服");
		$this->display();
    }
	//删除qq群
	public function dodeletequn()
    {
		$data = $_POST;
		foreach($data as $key => $v){
			$data[$key] = EnHtml($v);
		}
		
		$idarray = $data['idarr'];
		
		$delnum = M('qq')->where("id in ({$idarray})")->delete(); 
		alogs("QQ",0,0,'执行了客服QQ群的删除操作！');//管理员操作日志
		if($delnum){
			$a_data['success'] = $rid;
			$a_data['success_msg'] = "QQ群删除成功";
			$a_data['aid'] = $idarray;
		}else{
			$a_data['success'] = 0;
			$a_data['error_msg'] = "QQ群删除失败";
		}
		
		exit(json_encode($a_data));
	}
	
	
	//添加客服电话
	public function addtel()
    {
				
		$data = $_POST;
		foreach($data as $key =>$v){
		    if(!empty($key)) $data[$key]=EnHtml($v);
		}
		
		if(!isset($_POST['fid'])){
		    $newid = M('qq')->add($data);
			if(!$newid>0){
				alogs("QQ",$newid,0,'执行客服电话的添加操作失败！');//管理员操作日志
		       $this->error('添加失败，请确认填入数据正确');
			   exit;
			}
			alogs("QQ",$newid,1,'执行了客服电话的添加操作！');//管理员操作日志
			$this->assign('jumpUrl',U('/admin/QQ/tel/'));
			$this->success('客服电话添加成功');
		}else{
		    $data['id']=intval($_POST['fid']);
			$newid = M('qq')->save($data);
			if(!$newid>0){
				alogs("QQ",$newid,0,'执行客服电话的编辑操作失败！');//管理员操作日志
			    $this->error('编辑失败，请确认填入数据正确');
				exit;
			}
			alogs("QQ",$newid,1,'执行了客服电话的编辑操作！');//管理员操作日志
			$this->assign('jumpUrl',U('/admin/QQ/tel/'));
			$this->success('客服电话编辑成功');
		}
		
    }
	//客服电话列表
	 public function tel()
    {
		import('ORG.Util.Page');
		$Qq = M('qq');
		
		if(empty($search)) $condition = "1";
		else $condition = $search;
		
		$count = $Qq->where($condition)->count();
		$Page = new Page($count,$this->pagesize);
		$show = $Page->show();
		
		$fields = ($fields=="")?"*":$fields;
		$order = ($order=="")?'qq_order DESC':$order;
		$list = $Qq->field($fields)->where("$condition and type = 2")->order($order)->limit($Page->firstRow.','.$Page->listRows)->select();
		
		$QqList = $list;
		foreach($QqList as $key => $v){
		    foreach($v as $key_s => $v_s){
			    if ($key_s == 'is_show'){
				    if($v_s == 1) $v_s="是";
					else $v_s = "否";
				}
				$QqList[$key][$key_s] = $v_s;
			}
		}
		
		$this->assign('qq_list',$QqList);
		$this->assign('pagebar',$show);
		$this->assign('position',"客服电话");
		$this->display();
    }
	//删除客服电话
	public function dodeletetel()
    {
		$data = $_POST;
		foreach($data as $key => $v){
			$data[$key] = EnHtml($v);
		}
		
		$idarray = $data['idarr'];
		
		$delnum = M('qq')->where("id in ({$idarray})")->delete(); 
		alogs("QQ",0,0,'执行了客服电话的删除操作！');//管理员操作日志
		if($delnum){
			$a_data['success'] = $rid;
			$a_data['success_msg'] = "客服电话删除成功";
			$a_data['aid'] = $idarray;
		}else{
			$a_data['success'] = 0;
			$a_data['error_msg'] = "客服电话删除失败";
		}
		
		exit(json_encode($a_data));
	}
}
?>