<?php
class VerifyPhoneAction extends AdminAction{
    public function index(){
		$map=array();
		$map['m.user_phone'] = array('neq',"");
		if($_REQUEST['uname']){
			$map['m.user_name'] = array("like",urldecode($_REQUEST['uname'])."%");
			$search['uname'] = urldecode($_REQUEST['uname']);	
		}

		if($_REQUEST['realname']){
			$map['mi.real_name'] = urldecode($_REQUEST['realname']);
			$search['realname'] = $map['mi.real_name'];	
		}

		if($_REQUEST['user_phone']){
			$map['m.user_phone'] = urldecode($_REQUEST['user_phone']);
			$search['user_phone'] = $map['m.user_phone'];	
		}
		
		if(isset($_REQUEST['status'])){
			$map['ms.phone_status'] = $_REQUEST['status'];
			$search['status'] = $map['ms.phone_status'];	
		}
		
		if(!empty($_REQUEST['bj']) && !empty($_REQUEST['lx']) && !empty($_REQUEST['money'])){
			if($_REQUEST['lx']=='allmoney'){
				if($_REQUEST['bj']=='gt'){
					$bj = '>';
				}else if($_REQUEST['bj']=='lt'){
					$bj = '<';
				}else if($_REQUEST['bj']=='eq'){
					$bj = '=';
				}
				$map['_string'] = "(mm.account_money+mm.back_money) ".$bj.$_REQUEST['money'];
			}else{
				$map[$_REQUEST['lx']] = array($_REQUEST['bj'],$_REQUEST['money']);
			}
			$search['bj'] = $_REQUEST['bj'];	
			$search['lx'] = $_REQUEST['lx'];	
			$search['money'] = $_REQUEST['money'];	
		}
		//if(session('admin_is_kf')==1 && m.customer_id!='')	$map['m.customer_id'] = session('admin_id');
		
		//分页处理
		import("ORG.Util.Page");
		$count = M('member m')->join("{$this->pre}member_account mm ON mm.uid=m.id")->join("{$this->pre}member_info mi ON mi.uid=m.id")->join("{$this->pre}member_status ms ON ms.uid=m.id")->where($map)->count('m.id');
		$p = new Page($count, $this->pagesize);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";

		//分页处理		
		$field= 'm.id,m.user_phone,m.reg_time,m.user_name,mi.real_name,mm.money_freeze,mm.money_collect,(mm.account_money+mm.back_money) account_money,ms.phone_status,mi.uid';
		$list = M('member m')->field($field)->join("{$this->pre}member_account mm ON mm.uid=m.id")->join("{$this->pre}member_info mi ON mi.uid=m.id")->join("{$this->pre}member_status ms ON ms.uid=m.id")->where($map)->limit($Lsql)->order('m.id DESC')->select();
		
		$list = $this->_listFilter($list);

        $this->assign("status", array('待审核','已通过审核','未通过审核'));
        $this->assign("bj", array("gt"=>'大于',"eq"=>'等于',"lt"=>'小于'));
        $this->assign("lx", array("allmoney"=>'可用余额',"mm.money_freeze"=>'冻结金额',"mm.money_collect"=>'待收金额'));
        $this->assign("list", $list);
        $this->assign("pagebar", $page);
        $this->assign("search", $search);
        $this->assign("query", http_build_query($search));
		
        $this->display();
    }
	
	public function export(){
		import("ORG.Io.Excel");

		$map=array();
		$map['m.user_phone'] = array('neq',"");
		if($_REQUEST['uname']){
			$map['m.user_name'] = array("like",urldecode($_REQUEST['uname'])."%");
			$search['uname'] = urldecode($_REQUEST['uname']);	
		}
		if($_REQUEST['realname']){
			$map['mi.real_name'] = urldecode($_REQUEST['realname']);
			$search['realname'] = $map['mi.real_name'];	
		}
		if($_REQUEST['user_phone']){
			$map['m.user_phone'] = urldecode($_REQUEST['user_phone']);
			$search['user_phone'] = $map['m.user_phone'];	
		}
		
		if(isset($_REQUEST['status'])){
			$map['ms.phone_status'] = $_REQUEST['status'];
			$search['status'] = $map['ms.phone_status'];	
		}
		if(!empty($_REQUEST['bj']) && !empty($_REQUEST['lx']) && !empty($_REQUEST['money'])){
			if($_REQUEST['lx']=='allmoney'){
				if($_REQUEST['bj']=='gt'){
					$bj = '>';
				}else if($_REQUEST['bj']=='lt'){
					$bj = '<';
				}else if($_REQUEST['bj']=='eq'){
					$bj = '=';
				}
				$map['_string'] = "(mm.account_money+mm.back_money) ".$bj.$_REQUEST['money'];
			}else{
				$map[$_REQUEST['lx']] = array($_REQUEST['bj'],$_REQUEST['money']);
			}
			$search['bj'] = $_REQUEST['bj'];	
			$search['lx'] = $_REQUEST['lx'];	
			$search['money'] = $_REQUEST['money'];	
		}
		//if(session('admin_is_kf')==1 && m.customer_id!='')	$map['m.customer_id'] = session('admin_id');

		$field= 'm.id,m.user_phone,m.reg_time,m.user_name,mi.real_name,mm.money_freeze,mm.money_collect,(mm.account_money+mm.back_money) account_money,ms.phone_status,ms.uid';
		$list = M('member m')->field($field)->join("{$this->pre}member_account mm ON mm.uid=m.id")->join("{$this->pre}member_info mi ON mi.uid=m.id")->join("{$this->pre}member_status ms ON ms.uid=m.id")->where($map)->limit($Lsql)->order('m.id DESC')->select();

		$row=array();
		$row[0]=array('序号','用户ID','用户名','真实姓名','认证手机','总余额','可用余额','冻结金额','待收金额','注册时间');
		$i=1;
		foreach($list as $v){
				$row[$i]['i'] = $i;
				$row[$i]['uid'] = $v['id'];
				$row[$i]['card_num'] = $v['user_name'];
				$row[$i]['card_pass'] = $v['real_name'];
				$row[$i]['card_pass1'] = "{$v['user_phone']}";
				$row[$i]['card_mianfei'] = $v['money_freeze'] + $v['account_money'] + $v['money_collect'];
				$row[$i]['card_mianfei1'] = $v['account_money'];
				$row[$i]['card_mianfei2'] = $v['money_freeze'];
				$row[$i]['card_mianfei3'] = $v['money_collect'];
				$row[$i]['card_timelimit'] = date("Y-m-d",$v['reg_time']);
				$i++;
		}
		
		$xls = new Excel_XML('UTF-8', false, 'datalist');
		$xls->addArray($row);
		$xls->generateXML("datalistcard");
	}
	////////////////////////////添加手动手机验证  fan 2013-11-27 ///////////////////////////////////////

	public function edit(){
		$id=intval($_GET['id']);
		$aUser = get_admin_name();
		$vo = M('member_status')->find($id);
		if($vo['phone_status']==1) $this->error("审核通过的不能再次审核");
		$vo['uname'] = M('member')->getFieldById($vo['uid'],'user_name');
		$this->assign("vo",$vo);
		$this->display();
	}

	public function doEdit(){
       	$uid=intval($_REQUEST['uid']);
        
        $newid = setMemberStatus($uid, 'phone', $_POST['status'], 10, '手机');  
        
		if($newid){
			sendMessage($uid,"您的手机验证审核已通过","您的手机验证审核已通过");
			$this->assign('jumpUrl', __URL__."/".session('listaction'));
            $this->success(L('修改成功'));
		}else{
			sendMessage($uid,"您的手机验证审核未通过","您的手机验证审核未通过");
			 //失败提示
			$this->assign('jumpUrl', __URL__."/".session('listaction'));
           $this->success(L('修改成功'));
		}
	}

	public function _listFilter($list){
		$row=array();
		$aUser = get_admin_name();
		foreach($list as $key=>$v){
			$v['a_kfName'] = $aUser[$v['kfid']];
			$row[$key]=$v;
		}
		return $row;
	}
	////////////////////////////添加手动手机验证  fan 2013-11-27 ///////////////////////////////////////
	
}
?>