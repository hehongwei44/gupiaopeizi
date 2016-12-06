<?php
class WithdrawAction extends AdminAction{
    public function index(){
		$map=array();
		if($_REQUEST['uid'] && $_REQUEST['uname']){
			$map['w.uid'] = $_REQUEST['uid'];
			$search['uid'] = $map['w.uid'];	
			$search['uname'] = urldecode($_REQUEST['uname']);	
		}
		
		if($_REQUEST['uname'] && !$search['uid']){
			$map['m.user_name'] = array("like",urldecode($_REQUEST['uname'])."%");
			$search['uname'] = urldecode($_REQUEST['uname']);	
		}
		
		if(isset($_REQUEST['status']) && $_REQUEST['status']!=""){
			$map['w.withdraw_status'] = intval($_REQUEST['status']);
			$search['status'] = $map['w.withdraw_status'];	
		}
		
		if($_REQUEST['deal_user']){
			$map['w.deal_user'] = urldecode($_REQUEST['deal_user']);
			$search['deal_user'] = $map['w.deal_user'];	
		}
		
		if(!empty($_REQUEST['bj']) && !empty($_REQUEST['money'])){
			$map['w.withdraw_money'] = array($_REQUEST['bj'],$_REQUEST['money']);
			$search['bj'] = $_REQUEST['bj'];	
			$search['money'] = $_REQUEST['money'];	
		}

		if(!empty($_REQUEST['start_time']) && !empty($_REQUEST['end_time'])){
			$timespan = strtotime(urldecode($_REQUEST['start_time'])).",".strtotime(urldecode($_REQUEST['end_time']));
			$map['w.add_time'] = array("between",$timespan);
			$search['start_time'] = urldecode($_REQUEST['start_time']);	
			$search['end_time'] = urldecode($_REQUEST['end_time']);	
		}elseif(!empty($_REQUEST['start_time'])){
			$xtime = strtotime(urldecode($_REQUEST['end_time']));
			$map['w.add_time'] = array("gt",$xtime);
			$search['start_time'] = $xtime;	
		}elseif(!empty($_REQUEST['end_time'])){
			$xtime = strtotime(urldecode($_REQUEST['end_time']));
			$map['w.add_time'] = array("lt",$xtime);
			$search['end_time'] = $xtime;	
		}
		//if(session('admin_is_kf')==1)	$map['m.customer_id'] = session('admin_id');
		
		//分页处理
		import("ORG.Util.Page");
		$count = M('member_withdraw w')->join("{$this->pre}member m ON w.uid=m.id")->where($map)->count('w.id');
		$p = new Page($count, $this->pagesize);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理
		//$field= 'w.*,m.user_name,mi.real_name,w.id,w.uid ';
		//$list = M('member_withdraw w')->field($field)->join("{$this->pre}member m ON w.uid=m.id")->join("{$this->pre}member_info mi ON w.uid=mi.uid")->where($map)->order(' w.id DESC ')->limit($Lsql)->select();
		$field= 'w.*,m.user_name,mi.real_name,w.id,w.uid,(mm.account_money+mm.back_money) all_money';
		$list = M('member_withdraw w')->field($field)->join("ynw_member m ON w.uid=m.id")->join("ynw_member_info mi ON w.uid=mi.uid")->join("ynw_member_account mm on w.uid = mm.uid")->where($map)->order(' w.id DESC ')->limit($Lsql)->select();
		
		$this->assign("bj", array("gt"=>'大于',"eq"=>'等于',"lt"=>'小于'));
        $this->assign("list", $list);
		$this->assign("status",C('WITHDRAW_STATUS'));
        $this->assign("pagebar", $page);
        $this->assign("search", $search);
        $this->assign("query", http_build_query($search));
        $this->display();
    }
	
	//编辑
    public function edit() {
        $model = D(ucfirst($this->getActionName()));
        $id = intval($_REQUEST['id']);
        $vo = $model->find($id);
		$vo['uname'] = M("member")->getFieldById($vo['uid'],'user_name');
	 	$listType = C('WITHDRAW_STATUS');
		$this->assign('type_list',$listType);
        $this->assign('vo', $vo);
        $this->display();
    }
	
	public function _doEditFilter($m){
		$m->deal_time=time();
		$m->deal_user=session('adminname');
		
		$vox = M("member_withdraw")->field(true)->find($m->id);
		if($vox['withdraw_status']<>3 && $m->withdraw_status==3){
			$lm = M('member')->getFieldById($vox['uid'],'account_money');
			sendMessage($uid,"您的提现申请审核未通过","您的提现申请审核未通过");
			logMoney($vox['uid'],12,$vox['withdraw_money'],"提现未通过,返还");
		}elseif($vox['withdraw_status']<>2 && $m->withdraw_status==2){
			$um = M('member')->field("user_name,user_phone")->find($vox['uid']);
			sendMessage($uid,"您的提现已完成","您的提现已完成");
			logMoney($vox['uid'],29,-($vox['withdraw_money']),"提现成功，减去冻结资金，到帐金额".($vox['withdraw_money']-intval($_POST['withdraw_fee'])),'0','@网站管理员@');
			SMStip("withdraw",$um['user_phone'],array("#USERANEM#","#MONEY#"),array($um['user_name'],($vox['withdraw_money']-intval($_POST['withdraw_fee']))));
		}elseif($vox['withdraw_status']<>1 && $m->withdraw_status==1){
			sendMessage($uid,"您的提现申请已通过","您的提现申请已通过，正在处理中");
		}
		
		return $m;
	}
	
	public function _listFilter($list){
	 	$listType = C('WITHDRAW_STATUS');
		$row=array();
		foreach($list as $key=>$v){
			$v['withdraw_status_num'] = $v['withdraw_status'];
			$v['withdraw_status'] = $listType[$v['withdraw_status']];
			$v['uname'] = M("member")->getFieldById($v['uid'],'user_name');
			$row[$key]=$v;
		}
		return $row;
	}

	public function check()
    	{$map=array();
		if($_REQUEST['uid'] && $_REQUEST['uname']){
			$map['w.uid'] = $_REQUEST['uid'];
			$search['uid'] = $map['w.uid'];	
			$search['uname'] = urldecode($_REQUEST['uname']);	
		}
		
		if($_REQUEST['uname'] && !$search['uid']){
			$map['m.user_name'] = array("like",urldecode($_REQUEST['uname'])."%");
			$search['uname'] = urldecode($_REQUEST['uname']);	
		}
		
		if($_REQUEST['deal_user']){
			$map['w.deal_user'] = urldecode($_REQUEST['deal_user']);
			$search['deal_user'] = $map['w.deal_user'];	
		}
		
		if(!empty($_REQUEST['bj']) && !empty($_REQUEST['money'])){
			$map['w.withdraw_money'] = array($_REQUEST['bj'],$_REQUEST['money']);
			$search['bj'] = $_REQUEST['bj'];	
			$search['money'] = $_REQUEST['money'];	
		}

		if(!empty($_REQUEST['start_time']) && !empty($_REQUEST['end_time'])){
			$timespan = strtotime(urldecode($_REQUEST['start_time'])).",".strtotime(urldecode($_REQUEST['end_time']));
			$map['w.add_time'] = array("between",$timespan);
			$search['start_time'] = urldecode($_REQUEST['start_time']);	
			$search['end_time'] = urldecode($_REQUEST['end_time']);	
		}elseif(!empty($_REQUEST['start_time'])){
			$xtime = strtotime(urldecode($_REQUEST['end_time']));
			$map['w.add_time'] = array("gt",$xtime);
			$search['start_time'] = $xtime;	
		}elseif(!empty($_REQUEST['end_time'])){
			$xtime = strtotime(urldecode($_REQUEST['end_time']));
			$map['w.add_time'] = array("lt",$xtime);
			$search['end_time'] = $xtime;	
		}
		//if(session('admin_is_kf')==1)	$map['m.customer_id'] = session('admin_id');
	
		//分页处理
		import("ORG.Util.Page");
		$map['w.withdraw_status'] =0;
		$count = M('member_withdraw w')->join("{$this->pre}member m ON m.id=w.uid ")->where($map)->count('w.id');
		$p = new Page($count, $this->pagesize);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理

		$field= 'w.*,m.user_name,mi.real_name,w.id,w.uid,(mm.account_money+mm.back_money) all_money';
		$list = M('member_withdraw w')->field($field)->join("ynw_member m ON w.uid=m.id")->join("ynw_member_info mi ON w.uid=mi.uid")->join("ynw_member_account mm on w.uid = mm.uid")->where($map)->order(' w.id DESC ')->limit($Lsql)->select();
		
		$listType = C('WITHDRAW_STATUS');
		unset($listType[1],$listType[2],$listType[3]);
		$this->assign("bj", array("gt"=>'大于',"eq"=>'等于',"lt"=>'小于'));
        $this->assign("list", $list);
		$this->assign("status",$listType);
        $this->assign("pagebar", $page);
        $this->assign("search", $search);
        $this->assign("query", http_build_query($search));
		
        $this->display();
    }
	//编辑
    public function audit() {
        $model = M('member_withdraw');
        $id = intval($_REQUEST['id']);
        $vo = $model->find($id);
	 	$listType = C('WITHDRAW_STATUS');
		unset($listType[0],$listType[2]);
		$this->assign('type_list',$listType);

	 	$field= 'w.*,m.user_name,mi.real_name,w.id,w.uid,(mm.account_money+mm.back_money) all_money,mb.bank_num,mb.bank_province,mb.bank_city,mb.bank_address,mb.bank_name';
		$list = M('member_withdraw w')->field($field)->join("ynw_member m ON w.uid=m.id")->join("ynw_member_info mi ON w.uid=mi.uid")->join("ynw_member_account mm on w.uid = mm.uid")->join("ynw_member_banks mb on w.bank = mb.id")->where("w.id=$id")->order(' w.id ASC ')->limit($Lsql)->select();
		foreach($list as $v){
			$vo['uname'] =$v['user_name'];
			$vo['real_name'] = $v['real_name'];
			$vo['bank_num'] =$v['bank_num'];
			$vo['bank_province'] = $v['bank_province'];
			$vo['bank_city'] =$v['bank_city'];
			$vo['bank_address'] = $v['bank_address'];
			$vo['bank_name'] =$v['bank_name'];
			$vo['all_money'] =$v['all_money'];
			$vo['withdraw_fee'] =$v['withdraw_fee'];
		}
        $this->assign('vo', $vo);
        $this->display();
    }

	 public function doAudit() {
        $model = D("member_withdraw");
		$status = intval($_POST['withdraw_status']);
		$id = intval($_POST['id']);
		$deal_info = $_POST['deal_info'];
		$secondfee = floatval($_POST['withdraw_fee']);
		$info = $model->field('add_time')->where("id={$id} and (withdraw_status!=0)")->find();
        if($info['add_time']){
            $this->error("此提现初审已处理过，请不要重复处理！");   
        }
        if (false === $model->create()) {
            $this->error($model->getError());
        }
        //保存当前数据对象
		$model->withdraw_status = $status;
		$model->deal_info = $deal_info;
		$model->deal_time=time();
		$model->deal_user=session('adminname');
		////////////////////////
		$field= 'w.*,w.id,w.uid,(mm.account_money+mm.back_money) all_money';
		$vo = M("member_withdraw w")->field($field)->join("ynw_member_account mm on w.uid = mm.uid")->find($id);
		$um = M('member')->field("user_name,user_phone")->find($vo['uid']);
		if($vo['withdraw_status']<>3 && $status==3){
			sendMessage($vo['uid'],"您的提现申请审核未通过","您的提现申请审核未通过，处理说明：".$deal_info);
			logMoney($vo['uid'],12,$vo['withdraw_money'],"提现未通过,返还",'0','@网站管理员@');
			$model->success_money = 0;
			
		}else if($vo['withdraw_status']<>2 && $status==2){
			sendMessage($vo['uid'],"您的提现已完成","您的提现已完成");
			if( ($vo['all_money'] - $vo['second_fee'])<0 ){
				logMoney($vo['uid'],29,-($vo['withdraw_money']-$vo['second_fee']),"提现成功,扣除实际手续费".$vo['second_fee']."元，减去冻结资金，到帐金额".($vo['withdraw_money']-$vo['second_fee'])."元",'0','@网站管理员@',0,-$vo['second_fee']);
				$model->success_money = $vo['withdraw_money'];
				//SMStip("withdraw",$um['user_phone'],array("#USERANEM#","#MONEY#"),array($um['user_name'],($vo['withdraw_money']-$vo['second_fee'])));
			}else{
				logMoney($vo['uid'],29,-($vo['withdraw_money']),"提现成功,扣除实际手续费".$vo['second_fee']."元，减去冻结资金，到帐金额".($vo['withdraw_money'])."走下",'0','@网站管理员@');
				$model->success_money = $vo['withdraw_money'];
				//SMStip("withdraw",$um['user_phone'],array("#USERANEM#","#MONEY#"),array($um['user_name'],$vo['withdraw_money']));
			}
		}elseif($vo['withdraw_status']<>1 && $status==1){
			sendMessage($vo['uid'],"您的提现申请已通过","您的提现申请已通过，正在处理中");
			
			if($vo['all_money']  <=$secondfee ){
				logMoney($vo['uid'],36,-($vo['withdraw_money']),"提现申请已通过，扣除实际手续费".$secondfee."元，到帐金额".($vo['withdraw_money']-$secondfee)."元",'0','@网站管理员@',-$secondfee);
				$model->success_money = $vo['withdraw_money']-$secondfee;
			}else{
				logMoney($vo['uid'],36,-$vo['withdraw_money'],"提现申请已通过，扣除实际手续费".$secondfee."元，到帐金额".($vo['withdraw_money'])."元",'0','@网站管理员@',-$secondfee);
				$model->success_money = $vo['withdraw_money'];
			}

			$model->withdraw_fee = $vo['withdraw_fee'];
			$model->second_fee = $secondfee;
		}
		//////////////////////////
		$result = $model->save();
		
        if ($result) { //保存成功
			alogs("withdraw",$id,$status,$deal_info);//管理员操作日志
			//成功提示
            $this->assign('jumpUrl', __URL__);
            $this->success(L('修改成功'));
        } else {
			alogs("withdraw",$id,$status,'提现处理操作失败！');//管理员操作日志
			//$this->assign("waitSecond",10000);
            //失败提示
            $this->error(L('修改失败'));
        }
		$vm = M("member_money")->field("info")->where("uid = {$vo['uid']} and type=36")->limit(1)->order('id desc')->select();
		if(!empty($um['user_phone'])){
			//sendsms($um['user_phone'],$vm[0]['info']."【友情提醒】");
		}
		
    }
    /*
    处理中
    */
	public function waiting(){		
		$map=array();
		if($_REQUEST['uid'] && $_REQUEST['uname']){
			$map['w.uid'] = $_REQUEST['uid'];
			$search['uid'] = $map['w.uid'];	
			$search['uname'] = urldecode($_REQUEST['uname']);	
		}
		
		if($_REQUEST['uname'] && !$search['uid']){
			$map['m.user_name'] = array("like",urldecode($_REQUEST['uname'])."%");
			$search['uname'] = urldecode($_REQUEST['uname']);	
		}
		
		if($_REQUEST['deal_user']){
			$map['w.deal_user'] = urldecode($_REQUEST['deal_user']);
			$search['deal_user'] = $map['w.deal_user'];	
		}
		
		if(!empty($_REQUEST['bj']) && !empty($_REQUEST['money'])){
			$map['w.withdraw_money'] = array($_REQUEST['bj'],$_REQUEST['money']);
			$search['bj'] = $_REQUEST['bj'];	
			$search['money'] = $_REQUEST['money'];	
		}

		if(!empty($_REQUEST['start_time']) && !empty($_REQUEST['end_time'])){
			$timespan = strtotime(urldecode($_REQUEST['start_time'])).",".strtotime(urldecode($_REQUEST['end_time']));
			$map['w.add_time'] = array("between",$timespan);
			$search['start_time'] = urldecode($_REQUEST['start_time']);	
			$search['end_time'] = urldecode($_REQUEST['end_time']);	
		}elseif(!empty($_REQUEST['start_time'])){
			$xtime = strtotime(urldecode($_REQUEST['end_time']));
			$map['w.add_time'] = array("gt",$xtime);
			$search['start_time'] = $xtime;	
		}elseif(!empty($_REQUEST['end_time'])){
			$xtime = strtotime(urldecode($_REQUEST['end_time']));
			$map['w.add_time'] = array("lt",$xtime);
			$search['end_time'] = $xtime;	
		}
		//if(session('admin_is_kf')==1)	$map['m.customer_id'] = session('admin_id');
		
		//分页处理
		import("ORG.Util.Page");
		$map['w.withdraw_status'] =1;
		$count = M('member_withdraw w')->join("{$this->pre}member m ON m.id=w.uid")->where($map)->count('w.id');
		$p = new Page($count, $this->pagesize);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理

		$field= 'w.*,m.user_name,mi.real_name,w.id,w.uid,(mm.account_money+mm.back_money) all_money';
		$list = M('member_withdraw w')->field($field)->join("ynw_member m ON w.uid=m.id")->join("ynw_member_info mi ON w.uid=mi.uid")->join("ynw_member_account mm on w.uid = mm.uid")->where($map)->order(' w.id DESC ')->limit($Lsql)->select();
		
		
		$listType = C('WITHDRAW_STATUS');
		unset($listType[0],$listType[2],$listType[3]);
		$this->assign("bj", array("gt"=>'大于',"eq"=>'等于',"lt"=>'小于'));
        $this->assign("list", $list);
		$this->assign("status",$listType);
        $this->assign("pagebar", $page);
        $this->assign("search", $search);
        $this->assign("query", http_build_query($search));
		
        $this->display();
    }
	
    //编辑
    public function process() {
        $model = M('member_withdraw');
        $id = intval($_REQUEST['id']);
        $vo = $model->find($id);
		//$vo['uname'] = M("member")->getFieldById($vo['uid'],'user_name');
	 	$listType = C('WITHDRAW_STATUS');
		unset($listType[0],$listType[1]);
		$this->assign('type_list',$listType);
	 	$field= 'w.*,m.user_name,mi.real_name,w.id,w.uid,(mm.account_money+mm.back_money) all_money,mb.bank_num,mb.bank_province,mb.bank_city,mb.bank_address,mb.bank_name';
		$list = M('member_withdraw w')->field($field)->join("ynw_member m ON w.uid=m.id")->join("ynw_member_info mi ON w.uid=mi.uid")->join("ynw_member_account mm on w.uid = mm.uid")->join("ynw_member_banks mb on w.bank = mb.id")->where("w.id=$id")->order(' w.id ASC ')->limit($Lsql)->select();
		foreach($list as $v){
			$vo['uname'] =$v['user_name'];
			$vo['real_name'] = $v['real_name'];
			$vo['bank_num'] =$v['bank_num'];
			$vo['bank_province'] = $v['bank_province'];
			$vo['bank_city'] =$v['bank_city'];
			$vo['bank_address'] = $v['bank_address'];
			$vo['bank_name'] =$v['bank_name'];
			$vo['all_money'] =$v['all_money'];
			$vo['withdraw_fee'] =$v['withdraw_fee'];
		}
        $this->assign('vo', $vo);
        $this->display();
    }
	
	 public function doProcess() {
        $model = D("member_withdraw");
		$status = intval($_POST['withdraw_status']);
		$id = intval($_POST['id']);
		$deal_info = $_POST['deal_info'];
		$secondfee = floatval($_POST['withdraw_fee']);
		$info = $model->field('add_time')->where("id={$id} and (withdraw_status=2 or withdraw_status=3)")->find();
        if($info['add_time']){
            $this->error("此提现复审已处理过，请不要重复处理！");   
        }
        if (false === $model->create()) {
            $this->error($model->getError());
        }
        //保存当前数据对象
		$model->withdraw_status = $status;
		$model->deal_info = $deal_info;
		$model->deal_time=time();
		$model->deal_user=session('adminname');
		////////////////////////
		$field= 'w.*,w.id,w.uid,(mm.account_money+mm.back_money) all_money';
		$vo = M("member_withdraw w")->field($field)->join("ynw_member_account mm on w.uid = mm.uid")->find($id);
		$um = M('member')->field("user_name,user_phone")->find($vo['uid']);
		if($vo['withdraw_status']<>3 && $status==3){
			sendMessage($vo['uid'],"您的提现申请审核未通过","说明：".$deal_info);
			logMoney($vo['uid'],12,$vo['withdraw_money'],"提现未通过,返还,其中提现金额：".$vo['withdraw_money']."元，手续费：".$vo['second_fee']."元",'0','@网站管理员@',$vo['second_fee']);
			
			$model->success_money = 0;
			
		}else if($vo['withdraw_status']<>2 && $status==2){
			sendMessage($vo['uid'],"您的提现已完成","您的提现已完成");
			if( ($vo['all_money'] - $vo['second_fee'])<0 ){
				logMoney($vo['uid'],29,-($vo['withdraw_money']-$vo['second_fee']),"提现成功,扣除实际手续费".$vo['second_fee']."元，减去冻结资金，到帐金额".($vo['withdraw_money']-$vo['second_fee'])."元",'0','@网站管理员@',0,-$vo['second_fee']);
				$model->success_money = $vo['withdraw_money'];
				//SMStip("withdraw",$um['user_phone'],array("#USERANEM#","#MONEY#"),array($um['user_name'],($vo['withdraw_money']-$vo['second_fee'])));
			}else{
				logMoney($vo['uid'],29,-($vo['withdraw_money']),"提现成功,扣除实际手续费".$vo['second_fee']."元，减去冻结资金，到帐金额".($vo['withdraw_money'])."元",'0','@网站管理员@');
				$model->success_money = $vo['withdraw_money'];
				//SMStip("withdraw",$um['user_phone'],array("#USERANEM#","#MONEY#"),array($um['user_name'],$vo['withdraw_money']));
			}
				
			//SMStip("withdraw",$um['user_phone'],array("#USERANEM#","#MONEY#"),array($um['user_name'],($vo['withdraw_money']-$vo['second_fee'])));
		}elseif($vo['withdraw_status']<>1 && $status==1){
			sendMessage($vo['uid'],"您的提现申请已通过","您的提现申请已通过，正在处理中");
			
			if($vo['all_money']  <=$secondfee ){
				logMoney($vo['uid'],36,-($vo['withdraw_money']),"提现申请已通过，扣除实际手续费".$secondfee."元，到帐金额".($vo['withdraw_money'])."元",'0','@网站管理员@',-$secondfee);
				$model->success_money = $vo['withdraw_money']-$secondfee;
			}else{
				logMoney($vo['uid'],36,-$vo['withdraw_money'],"提现申请已通过，扣除实际手续费".$secondfee."元，到帐金额".($vo['withdraw_money'])."元",'0','@网站管理员@',-$secondfee);
				$model->success_money = $vo['withdraw_money'];
			}

			$model->withdraw_fee = $vo['withdraw_fee'];
			$model->second_fee = $secondfee;
		}
		//////////////////////////
		$result = $model->save();
		
        if ($result) { //保存成功
			alogs("withdraw",$id,$status,$deal_info);//管理员操作日志
          //成功提示
            $this->assign('jumpUrl', __URL__);
            $this->success(L('修改成功'));
        } else {
			alogs("withdraw",$id,$status,'提现处理操作失败！');//管理员操作日志
			//$this->assign("waitSecond",10000);
            //失败提示
            $this->error(L('修改失败'));
        }
		
		$vm = M("member_money")->field("info")->where("uid = {$vo['uid']} and type=29")->limit(1)->order('id desc')->select();
		//sendsms($um['user_phone'],$vm[0]['info']."【友情提醒】");
    }

	/**
	*提现成功
	*/
	public function finish(){
		$map=array();
		if($_REQUEST['uid'] && $_REQUEST['uname']){
			$map['w.uid'] = $_REQUEST['uid'];
			$search['uid'] = $map['w.uid'];	
			$search['uname'] = urldecode($_REQUEST['uname']);	
		}
		
		if($_REQUEST['uname'] && !$search['uid']){
			$map['m.user_name'] = array("like",urldecode($_REQUEST['uname'])."%");
			$search['uname'] = urldecode($_REQUEST['uname']);	
		}
		
		if($_REQUEST['deal_user']){
			$map['w.deal_user'] = urldecode($_REQUEST['deal_user']);
			$search['deal_user'] = $map['w.deal_user'];	
		}
		
		if(!empty($_REQUEST['bj']) && !empty($_REQUEST['money'])){
			$map['w.withdraw_money'] = array($_REQUEST['bj'],$_REQUEST['money']);
			$search['bj'] = $_REQUEST['bj'];	
			$search['money'] = $_REQUEST['money'];	
		}

		if(!empty($_REQUEST['start_time']) && !empty($_REQUEST['end_time'])){
			$timespan = strtotime(urldecode($_REQUEST['start_time'])).",".strtotime(urldecode($_REQUEST['end_time']));
			$map['w.add_time'] = array("between",$timespan);
			$search['start_time'] = urldecode($_REQUEST['start_time']);	
			$search['end_time'] = urldecode($_REQUEST['end_time']);	
		}elseif(!empty($_REQUEST['start_time'])){
			$xtime = strtotime(urldecode($_REQUEST['end_time']));
			$map['w.add_time'] = array("gt",$xtime);
			$search['start_time'] = $xtime;	
		}elseif(!empty($_REQUEST['end_time'])){
			$xtime = strtotime(urldecode($_REQUEST['end_time']));
			$map['w.add_time'] = array("lt",$xtime);
			$search['end_time'] = $xtime;	
		}
		//if(session('admin_is_kf')==1)	$map['m.customer_id'] = session('admin_id');
		
		//分页处理
		import("ORG.Util.Page");
		$map['w.withdraw_status'] =2;
		$count = M('member_withdraw w')->join("{$this->pre}member m ON w.uid=m.id")->where($map)->count('w.id');
		$p = new Page($count, $this->pagesize);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理
		
		//$field= 'w.*,m.user_name,mi.real_name,w.id,w.uid ';
		//$list = M('member_withdraw w')->field($field)->join("{$this->pre}member m ON w.uid=m.id")->join("{$this->pre}member_info mi ON w.uid=mi.uid")->where($map)->order(' w.id DESC ')->limit($Lsql)->select();
		$field= 'w.*,m.user_name,mi.real_name,w.id,w.uid,(mm.account_money+mm.back_money) all_money';
		$list = M('member_withdraw w')->field($field)->join("ynw_member m ON w.uid=m.id")->join("ynw_member_info mi ON w.uid=mi.uid")->join("ynw_member_account mm on w.uid = mm.uid")->where($map)->order(' w.id DESC ')->limit($Lsql)->select();
		
		$listType = C('WITHDRAW_STATUS');
		unset($listType[0],$listType[1],$listType[3]);
		$this->assign("bj", array("gt"=>'大于',"eq"=>'等于',"lt"=>'小于'));
        $this->assign("list", $list);
		$this->assign("status",$listType);
        $this->assign("pagebar", $page);
        $this->assign("search", $search);
        $this->assign("query", http_build_query($search));
		
        $this->display();
    }
	/**
	*提现失败
	*/
	public function refuse()
    {
		$map=array();
		if($_REQUEST['uid'] && $_REQUEST['uname']){
			$map['w.uid'] = $_REQUEST['uid'];
			$search['uid'] = $map['w.uid'];	
			$search['uname'] = urldecode($_REQUEST['uname']);	
		}
		
		if($_REQUEST['uname'] && !$search['uid']){
			$map['m.user_name'] = array("like",urldecode($_REQUEST['uname'])."%");
			$search['uname'] = urldecode($_REQUEST['uname']);	
		}
		
		if($_REQUEST['deal_user']){
			$map['w.deal_user'] = urldecode($_REQUEST['deal_user']);
			$search['deal_user'] = $map['w.deal_user'];	
		}
		
		if(!empty($_REQUEST['bj']) && !empty($_REQUEST['money'])){
			$map['w.withdraw_money'] = array($_REQUEST['bj'],$_REQUEST['money']);
			$search['bj'] = $_REQUEST['bj'];	
			$search['money'] = $_REQUEST['money'];	
		}

		if(!empty($_REQUEST['start_time']) && !empty($_REQUEST['end_time'])){
			$timespan = strtotime(urldecode($_REQUEST['start_time'])).",".strtotime(urldecode($_REQUEST['end_time']));
			$map['w.add_time'] = array("between",$timespan);
			$search['start_time'] = urldecode($_REQUEST['start_time']);	
			$search['end_time'] = urldecode($_REQUEST['end_time']);	
		}elseif(!empty($_REQUEST['start_time'])){
			$xtime = strtotime(urldecode($_REQUEST['end_time']));
			$map['w.add_time'] = array("gt",$xtime);
			$search['start_time'] = $xtime;	
		}elseif(!empty($_REQUEST['end_time'])){
			$xtime = strtotime(urldecode($_REQUEST['end_time']));
			$map['w.add_time'] = array("lt",$xtime);
			$search['end_time'] = $xtime;	
		}
		//if(session('admin_is_kf')==1)	$map['m.customer_id'] = session('admin_id');
		
		//分页处理
		import("ORG.Util.Page");
		$map['w.withdraw_status'] =3;
		$count = M('member_withdraw w')->join("{$this->pre}member m ON w.uid=m.id")->where($map)->count('w.id');
		$p = new Page($count, $this->pagesize);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理
		
		//$field= 'w.*,m.user_name,mi.real_name,w.id,w.uid ';
		//$list = M('member_withdraw w')->field($field)->join("{$this->pre}member m ON w.uid=m.id")->join("{$this->pre}member_info mi ON w.uid=mi.uid")->where($map)->order(' w.id DESC ')->limit($Lsql)->select();
		$field= 'w.*,m.user_name,mi.real_name,w.id,w.uid,(mm.account_money+mm.back_money) all_money';
		$list = M('member_withdraw w')->field($field)->join("ynw_member m ON w.uid=m.id")->join("ynw_member_info mi ON w.uid=mi.uid")->join("ynw_member_account mm on w.uid = mm.uid")->where($map)->order(' w.id DESC ')->limit($Lsql)->select();
		
		$listType = C('WITHDRAW_STATUS');
		unset($listType[0],$listType[1],$listType[2]);
		$this->assign("bj", array("gt"=>'大于',"eq"=>'等于',"lt"=>'小于'));
        $this->assign("list", $list);
		$this->assign("status",$listType);
        $this->assign("pagebar", $page);
        $this->assign("search", $search);
        $this->assign("query", http_build_query($search));
		
        $this->display();
    }
	
}
?>