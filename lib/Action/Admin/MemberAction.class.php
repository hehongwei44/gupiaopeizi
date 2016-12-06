<?php
class MemberAction extends AdminAction{
    public function index(){
		$map=array();
		switch($_GET['type']){
			case 'black':
				$map['m.black_type'] = array('gt',0);
				break;
			case 'borrow':
				$level = FS("data/conf/grade");
				if($_GET['level']){
					$map['m.integral'] = array('between',array($level[$_GET['level']]['start'],$level[$_GET['level']]['end']));
				}
				$map['m.borrow_times'] = array('gt',0);
				break;
			case 'invest':
				$level = FS("data/conf/level");
				if($_GET['level']){
					$map['m.integral'] = array('between',array($level[$_GET['level']]['start'],$level[$_GET['level']]['end']));
				}
				$map['m.invest_times'] = array('gt',0);
				break;
			case 'stock':
				$map['m.stock_times'] = array('gt',0);
				break;
			case 'complex':
				$map['m.borrow_times'] = array('gt',0);
				$map['m.invest_times'] = array('gt',0);
				$map['m.stock_times'] = array('gt',0);
				break;
			default:
				$level = array (
					1 => array ( 'name' => '最近7天登录过', 'start' => strtotime('-6 day'),'end'=>time()),
					2 => array ( 'name' => '最近1月登录过', 'start' => strtotime('-30 day'),'end'=>strtotime('-6 day')),
					3 => array ( 'name' => '最近半年登录过', 'start' => strtotime('-180 day'),'end'=>strtotime('-30 day')),
					4 => array ( 'name' => '半年前登录过', 'start' => strtotime('-360 day'),'end'=>strtotime('-180 day')),
					5 => array ( 'name' => '一年前登录过', 'start' => strtotime('-720 day'),'end'=>strtotime('-360 day')),
					);
				if($_GET['level']){
					$map['m.last_log_time'] = array('between',array($level[$_GET['level']]['start'],$level[$_GET['level']]['end']));
				}
				break;
		}
		if($_REQUEST['uname']){
			$map['m.user_name'] = array("like",urldecode($_REQUEST['uname'])."%");
			$search['uname'] = urldecode($_REQUEST['uname']);
		}
		if($_REQUEST['realname']){
			$map['mi.real_name'] = urldecode($_REQUEST['realname']);
			$search['realname'] = $map['mi.real_name'];
		}
		if($_REQUEST['is_vip']=='yes'){
			$map['m.user_leve'] = 1;
			$map['m.time_limit'] = array('gt',time());
			$search['is_vip'] = 'yes';
		}elseif($_REQUEST['is_vip']=='no'){
			$map['_string'] = 'm.user_leve=0 OR m.time_limit<'.time();
			$search['is_vip'] = 'no';
		}
		if($_REQUEST['is_transfer']=='yes'){
			$map['m.is_transfer'] = 1;
		}elseif($_REQUEST['is_transfer']=='no'){
			$map['m.is_transfer'] = 0;
		}

		//if(session('admin_is_kf')==1){
		//		$map['m.customer_id'] = session('admin_id');
		//}else{
			if($_REQUEST['customer_name']){
				$map['m.customer_id'] = $_REQUEST['customer_id'];
				$search['customer_id'] = $map['m.customer_id'];
				$search['customer_name'] = urldecode($_REQUEST['customer_name']);
			}

			if($_REQUEST['customer_name']){
				$cusname = urldecode($_REQUEST['customer_name']);
				$kfid = M('users')->getFieldByUserName($cusname,'id');
				$map['m.customer_id'] = $kfid;
				$search['customer_name'] = $cusname;
				$search['customer_id'] = $kfid;
			}
		//}
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

		if(!empty($_REQUEST['start_time']) && !empty($_REQUEST['end_time'])){
			$timespan = strtotime(urldecode($_REQUEST['start_time'])).",".strtotime(urldecode($_REQUEST['end_time']));
			$map['m.reg_time'] = array("between",$timespan);
			$search['start_time'] = urldecode($_REQUEST['start_time']);
			$search['end_time'] = urldecode($_REQUEST['end_time']);
		}elseif(!empty($_REQUEST['start_time'])){
			$xtime = strtotime(urldecode($_REQUEST['start_time']));
			$map['m.reg_time'] = array("gt",$xtime);
			$search['start_time'] = $xtime;
		}elseif(!empty($_REQUEST['end_time'])){
			$xtime = strtotime(urldecode($_REQUEST['end_time']));
			$map['m.reg_time'] = array("lt",$xtime);
			$search['end_time'] = $xtime;
		}

		//分页处理
		import("ORG.Util.Page");
		$count = M('member m')->join("{$this->pre}member_account mm ON mm.uid=m.id")->join("{$this->pre}member_info mi ON mi.uid=m.id")->where($map)->count('m.id');
		$p = new Page($count, $this->pagesize);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理
		$field= 'm.id,m.user_phone,m.reg_time,m.user_name,m.customer_name,m.user_leve,m.time_limit,mi.real_name,mm.money_freeze,mm.money_collect,(mm.account_money+mm.back_money) account_money,m.user_email,m.recommend_id,m.is_borrow,m.is_vip';
		$list = M('member m')->field($field)->join("{$this->pre}member_account mm ON mm.uid=m.id")->join("{$this->pre}member_info mi ON mi.uid=m.id")->where($map)->limit($Lsql)->order('m.id DESC')->select();

		$list=$this->_listFilter($list);
        $this->assign("bj", array("gt"=>'大于',"eq"=>'等于',"lt"=>'小于'));
        $this->assign("lx", array("allmoney"=>'可用余额',"mm.money_freeze"=>'冻结金额',"mm.money_collect"=>'待收金额'));
        $this->assign("list", $list);
        $this->assign("level", $level);
        $this->assign("pagebar", $page);
        $this->assign("search", $search);
        $this->assign("query", http_build_query($search));

        $this->display();
    }

    public function edit() {
        $model = D(ucfirst($this->getActionName()));
		setBackUrl();
        $id = intval($_REQUEST['id']);
        $vo = $model->find($id);
		$vx = M('member_info')->where("uid={$id}")->find();
		if(!is_array($vx)){
			M('member_info')->add(array("uid"=>$id));
		}else{
			foreach($vx as $key=>$vxe){
				$vo[$key]=$vxe;
			}
		}

		///////////////////////
		$vb = M('member_banks')->where("uid={$id}")->find();
		if(!is_array($vb)){
			//M('member_banks')->add(array("uid"=>$id));
		}else{
			foreach($vb as $key=>$vbe){
				$vo[$key]=$vbe;
			}
		}

		//////////////////////
        $this->assign('vo', $vo);

		$this->assign("utype", C('MEMBER_TYPE'));
		$this->assign("bank_list",$this->gloconf['BANK_NAME']);
        $this->display();
    }

	//添加数据
    public function doEdit() {
        $model = D(ucfirst($this->getActionName()));
        $model2 = M("member_info");
		$model3 = M("member_banks");

        if (false === $model->create()) {
            $this->error($model->getError());
        }
        if (false === $model2->create()) {
            $this->error($model2->getError());
        }
		if (false === $model3->create()) {
            $this->error($model3->getError());
        }

		$model->startTrans();
        if(!empty($model->user_pass)){
			$model->user_pass=md5($model->user_pass);
		}else{
			unset($model->user_pass);
		}
        if(!empty($model->pin_pass)){
			$model->pin_pass=md5($model->pin_pass);
		}else{
			unset($model->pin_pass);
		}

		$model->user_phone = $model2->cell_phone;
		$model3->add_ip = get_client_ip();
		$model3->add_time = time();

		$aUser = get_admin_name();
		$kfid = $model->customer_id;
		$model->customer_name = $aUser[$kfid];
		$result = $model->save();
		$result2 = $model2->save();
		$result3 = $model3->save();

        //保存当前数据对象
        if ($result || $result2 || $result3) { //保存成功
			$model->commit();
			alogs("Member",0,1,'成功执行了会员信息资料的修改操作！');//管理员操作日志
            //成功提示
            $this->assign('jumpUrl', __URL__."/".session('listaction'));
            $this->success(L('修改成功'));
        } else {
			alogs("Member",0,0,'执行会员信息资料的修改操作失败！');//管理员操作日志
			$model->rollback();
            //失败提示
            $this->error(L('修改失败'));
        }
    }

	//添加数据
    public function add() {
    	if($_POST){
			$model = D('Member');
	        $model2 = M("member_info");
			$model3 = M("member_banks");

			$_POST['reg_time'] = time();
			$_POST['reg_ip'] = get_client_ip();

			if($model->where('user_name="'.$_POST['user_name'].'"')->find()){
				$this->error('会员帐号已经存在，请更改会员名后再提交');
			}

	        if (false === $model->create()) {
	            $this->error($model->getError());
	        }
	        if (false === $model2->create()) {
	            $this->error($model2->getError());
	        }
			if (false === $model3->create()) {
	            $this->error($model3->getError());
	        }

			$model->startTrans();
	        if(!empty($model->user_pass)){
				$model->user_pass=md5($model->user_pass);
			}else{
				unset($model->user_pass);
			}
	        if(!empty($model->pin_pass)){
				$model->pin_pass=md5($model->pin_pass);
			}else{
				unset($model->pin_pass);
			}

			$model->user_phone = $model2->cell_phone;
			$model->from = 9;
			$model3->add_ip = get_client_ip();
			$model3->add_time = time();

			$aUser = get_admin_name();
			$kfid = $model->customer_id;
			$model->customer_name = $aUser[$kfid];
			$result = $model->add();
			$model2->uid = $result;
			$result2 = $model2->add();
			$model3->uid = $result;
			$result3 = $model3->add();

	        //保存当前数据对象
	        if ($result && ($result2 || $result3)) {
				$model->commit();
				alogs("Member",0,1,'成功添加会员信息！');
	            $this->success('会员资料添加成功');
	        } else {
				alogs("Member",0,0,'执行会员信息资料的修改操作失败！');
				$model->rollback();
	            $this->error('会员资料添加失败');
	        }
	    }else{
	    	$this->assign("bank_list",$this->gloconf['BANK_NAME']);
			$this->display();
	    }

    }

    public function info()
    {
		if($_GET['user_name']) $search['m.user_name'] = text($_GET['user_name']);
		else $search=array();
		$list = getMemberInfoList($search,$this->pagesize);
		$this->assign("list",$list['list']);
		$this->assign("pagebar",$list['page']);
        $this->assign("search", $search);
        $this->display();
    }

    public function infowait()
    {
		if($_GET['user_name']) $search['m.user_name'] = text($_GET['user_name']);
		else $search=array();
		$list = getMemberApplyList($search, $this->pagesize);
		$this->assign("aType",C('APPLY_TYPE'));
		$this->assign("list",$list['list']);
		$this->assign("pagebar",$list['page']);
        $this->display();
    }

    public function viewinfo()
    {
		$this->assign("aType",C('APPLY_TYPE'));
		setBackUrl();
		$id = intval($_GET['id']);
		$vx = M('member_apply')->field(true)->find($id);
		$uid = $vx['uid'];
		$vo = getMemberInfoDetail($uid);
		$this->assign("vx",$vx);
		$this->assign("vo",$vo);
		$this->assign("id",$id);
        $this->display();
    }

    public function viewinfom()
    {
		$id = intval($_GET['id']);
		$vo = getMemberInfoDetail($id);
		$this->assign("vo",$vo);
        $this->display();
    }

	public function doEditCredit(){
		$id = intval($_POST['id']);
		$uid = intval($_POST['uid']);
		$data['id'] = $id;
		$data['deal_info'] = text($_POST['deal_info']);
		$data['apply_status'] = intval($_POST['apply_status']);
		$data['credit_money'] = floatval($_POST['credit_money']);
		$newid = M('member_apply')->save($data);

		if($newid){
			//审核通过后资金授信改动
			if($data['apply_status']==1){
				$vx = M('member_apply')->field(true)->find($id);
				$umoney = M('member_account')->field(true)->find($vx['uid']);

				$moneyLog['uid'] = $vx['uid'];
				if($vx['apply_type']==1){
					$moneyLog['credit_limit'] = floatval($umoney['credit_limit']) + $data['credit_money'];
					$moneyLog['credit_cuse'] = floatval($umoney['credit_cuse']) + $data['credit_money'];
				}elseif($vx['apply_type']==2){
					$moneyLog['borrow_vouch_limit'] = floatval($umoney['borrow_vouch_limit']) + $data['credit_money'];
					$moneyLog['borrow_vouch_cuse'] = floatval($umoney['borrow_vouch_cuse']) + $data['credit_money'];
				}elseif($vx['apply_type']==3){
					$moneyLog['invest_vouch_limit'] = floatval($umoney['invest_vouch_limit']) + $data['credit_money'];
					$moneyLog['invest_vouch_cuse'] = floatval($umoney['invest_vouch_cuse']) + $data['credit_money'];
				}

				if(!is_array($umoney))	M('member_account')->add($moneyLog);
				else M('member_account')->where("uid={$vx['uid']}")->save($moneyLog);
			}//审核通过后资金授信改动
			alogs("Member",0,1,'成功执行了会员资料通过后资金授信改动的审核操作！');//管理员操作日志
			$this->success("审核成功",__URL__."/infowait".session('listaction'));
		}else{
			alogs("Member",0,0,'执行会员资料通过后资金授信改动的审核操作失败！');//管理员操作日志
			$this->error("审核失败");
		}
	}

    public function moneyedit()
    {
		setBackUrl();
		$this->assign("id",intval($_GET['id']));
		$this->display();
    }

    public function doMoneyEdit()
    {
		$id = intval($_POST['id']);
		$uid = $id;
		$info = text($_POST['info']);
		$done=false;
		if(floatval($_POST['account_money'])!=0){
			$done=logMoney($uid,71,floatval($_POST['account_money']),$info);
		}
		if(floatval($_POST['money_freeze'])!=0){
			$done=false;
			$done=logMoney($uid,72,floatval($_POST['money_freeze']),$info);
		}
		if(floatval($_POST['money_collect'])!=0){
			$done=false;
			$done=logMoney($uid,73,floatval($_POST['money_collect']),$info);
		}
		//记录

        $this->assign('jumpUrl', __URL__."/index".session('listaction'));
		if($done){
			alogs("Member",0,1,'成功执行了会员余额调整的操作！');//管理员操作日志
			$this->success("操作成功");
		}else{
			alogs("Member",0,0,'执行会员余额调整的操作失败！');//管理员操作日志
			$this->error("操作失败");
		}
    }

    public function creditedit()
    {
		setBackUrl();
		$this->assign("id",intval($_GET['id']));
		$this->display();
    }

    public function doCreditEdit()
    {
		$id = intval($_POST['id']);

		$umoney = M('member_account')->field(true)->find($id);
		if(intval($_POST['credit_limit'])!=0){
			$moneyLog['uid'] = $id;
			$moneyLog['credit_limit'] = floatval($umoney['credit_limit']) + floatval($_POST['credit_limit']);
			$moneyLog['credit_cuse'] = floatval($umoney['credit_cuse']) + floatval($_POST['credit_limit']);
			if(!is_array($umoney))	$newid = M('member_account')->add($moneyLog);
			else $newid = M('member_account')->where("uid={$id}")->save($moneyLog);
		}
		if(intval($_POST['borrow_vouch_limit'])!=0){
			$moneyLog=array();
			$moneyLog['uid'] = $id;
			$moneyLog['borrow_vouch_limit'] = floatval($umoney['borrow_vouch_limit']) + floatval($_POST['borrow_vouch_limit']);
			$moneyLog['borrow_vouch_cuse'] = floatval($umoney['borrow_vouch_cuse']) + floatval($_POST['borrow_vouch_limit']);
			if(!is_array($umoney) && !$newid)	$newid = M('member_account')->add($moneyLog);
			else $newid = M('member_account')->where("uid={$id}")->save($moneyLog);
		}
		if(intval($_POST['invest_vouch_limit'])!=0){
			$moneyLog=array();
			$moneyLog['uid'] = $id;
			$moneyLog['invest_vouch_limit'] = floatval($umoney['invest_vouch_limit']) + floatval($_POST['invest_vouch_limit']);
			$moneyLog['invest_vouch_cuse'] = floatval($umoney['invest_vouch_cuse']) + floatval($_POST['invest_vouch_limit']);
			if(!is_array($umoney) && !$newid)	$newid = M('member_account')->add($moneyLog);
			else $newid = M('member_account')->where("uid={$id}")->save($moneyLog);
		}

		//修改会员信用等级积分（E级->AAA级）
		$userCredits = M('member')->field(true)->find($id);
		if(intval($_POST['credits'])!=0){
			$moneyLog=array();
			$moneyLog['id'] = $id;
			$moneyLog['credits'] = intval($userCredits['credits'])+intval($_POST['credits']);
			if(!is_array($userCredits) && !$newid)	$newid = M('member')->add($moneyLog);
			else $newid = M('member')->where("id={$id}")->save($moneyLog);
		}

        $this->assign('jumpUrl', __URL__."/index".session('listaction'));
		if($newid){
			alogs("Member",0,1,'成功执行了会员授信调整的操作！');//管理员操作日志
			$this->success("操作成功");
		}else{
			alogs("Member",0,0,'执行会员授信调整的操作失败！');//管理员操作日志
			$this->error("操作失败");
		}
    }


	public function _listFilter($list){
		$row=array();
		foreach($list as $key=>$v){
			if($v['recommend_id']<>0){
				$v['recommend_name'] = M("member")->getFieldById($v['recommend_id'],"user_name");
			 }
			 if($v['is_vip']==1){
				$v['is_vip'] = "<span style='color:red'>内标</span>";
			 }else{
				$v['is_vip'] ="个人";
			 }
			$row[$key]=$v;
		}
		return $row;
	}

	public function getusername(){
		$uname = M("member")->getFieldById(intval($_POST['uid']),"user_name");
		if($uname) exit(json_encode(array("uname"=>"<span style='color:green'>".$uname."</span>")));
		else exit(json_encode(array("uname"=>"<span style='color:orange'>不存在此会员</span>")));
	}

	 public function idcardedit() {
        $model = D(ucfirst($this->getActionName()));
		setBackUrl();
        $id = intval($_REQUEST['id']);
        $vo = $model->find($id);
		$vx = M('member_info')->where("uid={$id}")->find();
		if(!is_array($vx)){
			M('member_info')->add(array("uid"=>$id));
		}else{
			foreach($vx as $key=>$vxe){
				$vo[$key]=$vxe;
			}
		}
        $this->assign('vo', $vo);
		$this->assign("utype", C('MEMBER_TYPE'));
        $this->display();
    }

	//添加身份证信息
    public function doIdcardEdit() {
        $model = D(ucfirst($this->getActionName()));
        $model2 = M("member_info");

        if (false === $model->create()) {
            $this->error($model->getError());
        }
        if (false === $model2->create()) {
            $this->error($model->getError());
        }

		$model->startTrans();
		/////////////////////////////
		if(!empty($_FILES['imgfile']['name'])){
			$this->fix = false;
			//设置上传文件规则
			$this->saveRule = 'uniqid';
			//$this->saveRule = date("YmdHis",time()).rand(0,1000)."_".$model->id;
			$this->savePathNew = C('ADMIN_UPLOAD_DIR').'Idcard/';
			$this->thumbMaxWidth = C('IDCARD_UPLOAD_H');
			$this->thumbMaxHeight = C('IDCARD_UPLOAD_W');
			$info = $this->CUpload();
			$data['card_img'] = $info[0]['savepath'].$info[0]['savename'];
			$data['card_back_img'] = $info[1]['savepath'].$info[1]['savename'];

			if($data['card_img']&&$data['card_back_img']){
				$model2->card_img=$data['card_img'];
				$model2->card_back_img=$data['card_back_img'];
			}
		}
		///////////////////////////
		$result = $model->save();
		$result2 = $model2->save();

        //保存当前数据对象
        if ($result || $result2) { //保存成功
			$model->commit();
			alogs("Member",0,1,'成功执行了会员身份证代传的操作！');//管理员操作日志
            //成功提示
            $this->assign('jumpUrl', __URL__."/".session('listaction'));
            $this->success(L('修改成功'));
        } else {
			$model->rollback();
			alogs("Member",0,0,'执行会员身份证代传的操作失败！');//管理员操作日志
            //失败提示
            $this->error(L('修改失败'));
        }
    }
	///////////////////////////////////
}
?>