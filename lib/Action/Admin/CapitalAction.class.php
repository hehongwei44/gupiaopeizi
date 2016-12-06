<?php

class CapitalAction extends AdminAction{
    public function account(){
		$map=array();
		if($_REQUEST['uname']){
			$map['m.user_name'] = array("like",urldecode($_REQUEST['uname'])."%");
			$search['uname'] = urldecode($_REQUEST['uname']);	
		}
		if($_REQUEST['realname']){
			$map['mi.real_name'] = urldecode($_REQUEST['realname']);
			$search['realname'] = $map['mi.real_name'];	
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
		
		//分页处理
		import("ORG.Util.Page");
		$count = M('member m')->join("{$this->pre}member_account mm ON mm.uid=m.id")->join("{$this->pre}member_info mi ON mi.uid=m.id")->where($map)->count('m.id');
		$p = new Page($count, $this->pagesize);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理
		$pre = $this->pre;
		$field= 'm.id,m.reg_time,m.user_name,mi.real_name,mm.money_freeze,mm.money_collect,(mm.account_money+mm.back_money) total_money,mm.account_money,mm.back_money';
		$list = M('member m')->field($field)->join("{$this->pre}member_account mm ON mm.uid=m.id")->join("{$this->pre}member_info mi ON mi.uid=m.id")->where($map)->limit($Lsql)->order("m.id DESC")->select();
	
		foreach($list as $key=>$v){
			$uid = $v['id'];
			$list[$key]['benefit'] = get_personal_benefit($uid);

			$list[$key]['out'] = get_personal_out($uid);
			$list[$key]['count'] = get_personal_count($uid);
			$money_log = get_money_log($uid);
			$list[$key]['glycz']=$money_log['17']['money'];

			$withdraw0 = M('member_withdraw')->where("uid={$uid} AND withdraw_status=0")->sum('withdraw_money');//待提现
			$withdraw1 = M('member_withdraw')->where("uid={$uid} AND withdraw_status=1")->sum('withdraw_money');//提现处理中
			$withdraw3 = M('member_withdraw')->where("uid={$uid} AND withdraw_status=0")->sum('second_fee');//待提现手续费
			$withdraw4 = M('member_withdraw')->where("uid={$uid} AND withdraw_status=1")->sum('second_fee');//处理中提现手续费
			$list[$key]['dshtx'] = $withdraw0 + $withdraw3;
			$list[$key]['chulizhong'] = $withdraw1+$withdraw4;
			
		}
        $this->assign("bj", array("gt"=>'大于',"eq"=>'等于',"lt"=>'小于'));
        $this->assign("lx", array("allmoney"=>'可用余额',"mm.money_freeze"=>'冻结金额',"mm.money_collect"=>'待收金额'));
        $this->assign("list", $list);
        $this->assign("pagebar", $page);
        $this->assign("search", $search);
        $this->assign("query", http_build_query($search));
		
        $this->display();
    }
	
	public function accountexport(){
		import("ORG.Io.Excel");
		alogs("CapitalAccount",0,1,'执行了所有会员资金列表导出操作！');//管理员操作日志
		$map=array();
		if($_REQUEST['uname']){
			$map['m.user_name'] = array("like",urldecode($_REQUEST['uname'])."%");
			$search['uname'] = urldecode($_REQUEST['uname']);	
		}
		if($_REQUEST['realname']){
			$map['mi.real_name'] = urldecode($_REQUEST['realname']);
			$search['realname'] = $map['mi.real_name'];	
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
		
		//分页处理
		import("ORG.Util.Page");
		$count = M('member m')->join("{$this->pre}member_account mm ON mm.uid=m.id")->join("{$this->pre}member_info mi ON mi.uid=m.id")->where($map)->count('m.id');
		$p = new Page($count, $this->pagesize);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理
		$pre = $this->pre;
		$field= 'm.id,m.reg_time,m.user_name,mi.real_name,mm.money_freeze,mm.money_collect,(mm.account_money+mm.back_money) total_money,mm.account_money,mm.back_money';
		$list = M('member m')->field($field)->join("{$this->pre}member_account mm ON mm.uid=m.id")->join("{$this->pre}member_info mi ON mi.uid=m.id")->where($map)->order("m.id DESC")->select();
		
		foreach($list as $key=>$v){
			$uid = $v['id'];
			//$umoney = M('member')->field('account_money,reward_money')->find($uid);
			
			//待确认投标
			$investing = M()->query("select sum(investor_capital) as capital from {$pre}borrow_investor where investor_uid={$uid} AND status=1");
			
			//待收金额
			$invest = M()->query("select sum(investor_capital-receive_capital) as capital,sum(reward_money) as jiangli,sum(investor_interest-receive_interest) as interest from {$pre}borrow_investor where investor_uid={$uid} AND status =4");
			//$invest = M()->query("SELECT sum(capital) as capital,sum(interest) as interest FROM {$pre}investor WHERE investor_uid={$uid} AND `status` =7");
			//待付金额
			$borrow = M()->query("select sum(borrow_money-repayment_money) as repayment_money,sum(borrow_interest-repayment_interest) as repayment_interest from {$pre}borrow where borrow_uid={$uid} AND borrow_status=6");
			
			
			$withdraw0 = M('member_withdraw')->where("uid={$uid} AND withdraw_status=0")->sum('withdraw_money');//待提现
			$withdraw1 = M('member_withdraw')->where("uid={$uid} AND withdraw_status=1")->sum('withdraw_money');//提现处理中
			$withdraw2 = M('member_withdraw')->where("uid={$uid} AND withdraw_status=2")->sum('withdraw_money');//已提现
			
			$withdraw3 = M('member_withdraw')->where("uid={$uid} AND withdraw_status=0")->sum('second_fee');//待提现手续费
			$withdraw4 = M('member_withdraw')->where("uid={$uid} AND withdraw_status=1")->sum('second_fee');//处理中提现手续费
		
		
			$borrowANDpaid = M()->query("select status,sort_order,borrow_id,sum(capital) as capital,sum(interest) as interest from {$pre}investor where borrow_uid={$uid} AND status in(1,2,3)");
			$investEarn = M('borrow_investor')->where("investor_uid={$uid} and status in(4,5,6)")->sum('receive_interest');
			$investPay = M('borrow_investor')->where("investor_uid={$uid} status<>2")->sum('investor_capital');
			$investEarn1 = M('borrow_investor')->where("investor_uid={$uid} and status in(4,5,6)")->sum('invest_fee');//投资者管理费
			
			$payonline = M('member_payment')->where("uid={$uid} AND status=1")->sum('money');
			
			//累计支付佣金
			$commission1 = M('borrow_investor')->where("investor_uid={$uid}")->sum('paid_fee');
			$commission2 = M('borrow')->where("borrow_uid={$uid} AND borrow_status in(2,4)")->sum('borrow_fee');
			
			$uplevefee = M('member_money')->where("uid={$uid} AND type=2")->sum('affect_money');
			$adminop = M('member_money')->where("uid={$uid} AND type=7")->sum('affect_money');
			
			$txfee = M('member_withdraw')->where("uid={$uid} AND withdraw_status=2")->sum('second_fee');
			$czfee = M('member_payment')->where("uid={$uid} AND status=1")->sum('fee');
		
			$interest_needpay = M()->query("select sum(borrow_interest-repayment_interest) as need_interest from {$pre}borrow where borrow_uid={$uid} AND borrow_status=6");
			$interest_willget = M()->query("select sum(investor_interest-receive_interest) as willget_interest from {$pre}borrow_investor where investor_uid={$uid} AND status=4");
			
			$interest_jiliang =M('borrow_investor')->where("borrow_uid={$uid}")->sum('reward_money');//累计支付投标奖励
			
			$moneylog = M("member_money")->field("type,sum(affect_money) as money")->where("uid={$uid}")->group("type")->select();
			$listarray=array();
			foreach($moneylog as $vs){
				$listarray[$vs['type']]['money']= ($vs['money']>0)?$vs['money']:$vs['money']*(-1);
			}
	
			
			//$money['kyxjje'] = $umoney['account_money'];//可用现金金额
			$money['kyxjje'] = $v['account_money'];//可用现金金额
			$money['dsbx'] = floatval($invest[0]['capital']+$invest[0]['interest']);//待收本息
			$money['dsbj'] = $invest[0]['capital'];//待收本金
			$money['dslx'] = $invest[0]['interest'];//待收利息
			$money['dfbx'] = floatval($borrow[0]['repayment_money']+$borrow[0]['repayment_interest']);//待付本息
			$money['dfbj'] = $borrow[0]['repayment_money'];//待付本金
			$money['dflx'] = $borrow[0]['repayment_interest'];//待付利息
			$money['dxrtb'] = $investing[0]['capital'];//待确认投标
			
			$money['dshtx'] = $withdraw0+$withdraw3;//待审核提现
			$money['clztx'] = $withdraw1+$withdraw4;//处理中提现
			
			//$money['jzlx'] = $investEarn;//净赚利息
			$money['jzlx'] = $investEarn-$investEarn1;//净赚利息
			$money['jflx'] = $borrowANDpaid[0]['interest'];//净付利息
			$money['ljjj'] = $umoney['reward_money'];//累计收到奖金
			$money['ljhyf'] = $uplevefee;//累计支付会员费
			$money['ljtxsxf'] = $txfee;//累计提现手续费
			$money['ljczsxf'] = $czfee;//累计充值手续费
			$money['total_2'] = $money['jzlx']-$money['jflx']-$money['ljhyf']-$money['ljtxsxf']-$money['ljczsxf'];
			
			$money['ljtzje'] = $investPay;//累计投资金额
			$money['ljjrje'] = $borrowANDpaid[0]['borrow_money'];//累计借入金额
			$money['ljczje'] = $payonline;//累计充值金额
			$money['ljtxje'] = $withdraw2;//累计提现金额
			$money['ljzfyj'] = $commission1 + $commission2;//累计支付佣金
			$money['glycz'] = $listarray['7']['money'];//管理员操作资金
		//
			$money['dslxze'] = $interest_willget[0]['willget_interest'];//待收利息总额
			$money['dflxze'] = $interest_needpay[0]['need_interest'];//待付利息总额
			$money['ljtbjl'] = $listarray['20']['money'];//累计投标奖励
			
			$list[$key]['xmoney'] = $money;
			
			
		}

		$row=array();
		$row[0]=array('ID','用户名','真实姓名','总余额','可用余额','冻结金额','待收本息金额','待收本金金额','待收利息金额','待付本息金额','待付本金金额','待付利息金额','待确认投标','待审核提现+手续费','处理中提现+手续费','累计提现手续费','累计充值手续费','累计提现金额','累计充值金额','累计支付佣金','累计投标奖励','净赚利息','净付利息','管理员操作资金');
		$i=1;
		foreach($list as $v){
				$row[$i]['uid'] = $v['id'];
				$row[$i]['card_num'] = $v['user_name'];
				$row[$i]['card_pass'] = $v['real_name'];
				$row[$i]['card_mianfei'] = $v['money_freeze'] + $v['total_money'] + $v['money_collect'];
				$row[$i]['card_mianfei1'] = $v['total_money'];
				$row[$i]['card_mianfei2'] = $v['money_freeze'];
				$row[$i]['dsbx'] = $v['xmoney']['dsbx'];
				$row[$i]['dsbj'] = $v['xmoney']['dsbj'];
				$row[$i]['dslx'] = $v['xmoney']['dslx'];
				
				$row[$i]['dfbx'] = $v['xmoney']['dfbx'];
				$row[$i]['dfbj'] = $v['xmoney']['dfbj'];
				$row[$i]['dflx'] = $v['xmoney']['dflx'];
				$row[$i]['dxrtb'] = $v['xmoney']['dxrtb'];
				$row[$i]['dshtx'] = $v['xmoney']['dshtx'];
				$row[$i]['clztx'] = $v['xmoney']['clztx'];
				
				$row[$i]['ljtxsxf'] = $v['xmoney']['ljtxsxf'];
				$row[$i]['ljczsxf'] = $v['xmoney']['ljczsxf'];
				$row[$i]['ljtxje'] = $v['xmoney']['ljtxje'];
				$row[$i]['ljczje'] = $v['xmoney']['ljczje'];
				$row[$i]['ljzfyj'] = $v['xmoney']['ljzfyj'];
				$row[$i]['ljtbjl'] = $v['xmoney']['ljtbjl'];
				$row[$i]['jzlx'] = $v['xmoney']['jzlx'];
				$row[$i]['jflx'] = $v['xmoney']['jflx'];
				$row[$i]['glycz'] = $v['xmoney']['glycz'];
				$i++;
			}
		$xls = new Excel_XML('UTF-8', false, 'datalist');
		$xls->addArray($row);
		$xls->generateXML("datalistcard");
	}

  	public function detail(){
		$map=array();
		if($_REQUEST['uid'] && $_REQUEST['uname']){
			$map['l.uid'] = $_REQUEST['uid'];
			$search['uid'] = $map['l.uid'];	
			$search['uname'] = urldecode($_REQUEST['uname']);	
		}
		
		if($_REQUEST['uname'] && !$search['uid']){
			$map['m.user_name'] = array("like",urldecode($_REQUEST['uname'])."%");
			$search['uname'] = urldecode($_REQUEST['uname']);	
		}

		if($_REQUEST['target_uname']){
			$map['l.target_uname'] = urldecode($_REQUEST['target_uname']);
			$search['target_uname'] = $map['l.target_uname'];	
		}

		if(isset($_REQUEST['type']) && $_REQUEST['type'] != ''){
			$map['l.type'] = intval($_REQUEST['type']);
			$search['type'] = $map['l.type'];	
		}
		
		if(!empty($_REQUEST['bj']) && !empty($_REQUEST['money'])){
			$map['l.affect_money'] = array($_REQUEST['bj'],$_REQUEST['money']);
			$search['bj'] = $_REQUEST['bj'];	
			$search['money'] = $_REQUEST['money'];	
		}

		if(!empty($_REQUEST['start_time']) && !empty($_REQUEST['end_time'])){
			$timespan = strtotime(urldecode($_REQUEST['start_time'])).",".strtotime(urldecode($_REQUEST['end_time']));
			$map['l.add_time'] = array("between",$timespan);
			$search['start_time'] = urldecode($_REQUEST['start_time']);	
			$search['end_time'] = urldecode($_REQUEST['end_time']);	
		}elseif(!empty($_REQUEST['start_time'])){
			$xtime = strtotime(urldecode($_REQUEST['start_time']));
			$map['l.add_time'] = array("gt",$xtime);
			$search['start_time'] = $xtime;	
		}elseif(!empty($_REQUEST['end_time'])){
			$xtime = strtotime(urldecode($_REQUEST['end_time']));
			$map['l.add_time'] = array("lt",$xtime);
			$search['end_time'] = $xtime;	
		}
		//if(session('admin_is_kf')==1)	$map['m.customer_id'] = session('admin_id');
		//分页处理
		import("ORG.Util.Page");
		$count = M('member_money l')->join("{$this->pre}member m ON m.id=l.uid")->where($map)->count('l.id');
		$p = new Page($count, $this->pagesize);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理
		
		$field= 'l.id,l.add_time,m.user_name,l.affect_money,l.freeze_money,l.collect_money,(l.account_money+l.back_money) account_money,l.target_uname,l.type,l.info';
		$order = "l.id DESC";
		$list = M('member_money l')->field($field)->join("{$this->pre}member m ON m.id=l.uid")->where($map)->limit($Lsql)->order($order)->select();
		
        $this->assign("bj", array("gt"=>'大于',"eq"=>'等于',"lt"=>'小于'));
        $this->assign("type", C('MONEY_LOG'));
        $this->assign("list", $list);
        $this->assign("pagebar", $page);
        $this->assign("search", $search);
        $this->assign("query", http_build_query($search));
		
        $this->display();
    }
	
	public function detailexport(){
		import("ORG.Io.Excel");
		alogs("CapitalDetail",0,1,'执行了会员资金明细列表导出操作！');//管理员操作日志
		$map=array();
		if($_REQUEST['uid'] && $_REQUEST['uname']){
			$map['l.uid'] = $_REQUEST['uid'];
			$search['uid'] = $map['l.uid'];	
			$search['uname'] = urldecode($_REQUEST['uname']);	
		}
		
		if($_REQUEST['uname'] && !$search['uid']){
			$map['m.user_name'] = array("like",urldecode($_REQUEST['uname'])."%");
			$search['uname'] = urldecode($_REQUEST['uname']);	
		}

		if($_REQUEST['target_uname']){
			$map['l.target_uname'] = urldecode($_REQUEST['target_uname']);
			$search['target_uname'] = $map['l.target_uname'];	
		}

		if(isset($_REQUEST['type']) && $_REQUEST['type'] != ''){
			$map['l.type'] = intval($_REQUEST['type']);
			$search['type'] = $map['l.type'];	
		}
		
		if(!empty($_REQUEST['bj']) && !empty($_REQUEST['money'])){
			$map['l.affect_money'] = array($_REQUEST['bj'],$_REQUEST['money']);
			$search['bj'] = $_REQUEST['bj'];	
			$search['money'] = $_REQUEST['money'];	
		}

		if(!empty($_REQUEST['start_time']) && !empty($_REQUEST['end_time'])){
			$timespan = strtotime(urldecode($_REQUEST['start_time'])).",".strtotime(urldecode($_REQUEST['end_time']));
			$map['l.add_time'] = array("between",$timespan);
			$search['start_time'] = urldecode($_REQUEST['start_time']);	
			$search['end_time'] = urldecode($_REQUEST['end_time']);	
		}elseif(!empty($_REQUEST['start_time'])){
			$xtime = strtotime(urldecode($_REQUEST['start_time']));
			$map['l.add_time'] = array("gt",$xtime);
			$search['start_time'] = $xtime;	
		}elseif(!empty($_REQUEST['end_time'])){
			$xtime = strtotime(urldecode($_REQUEST['end_time']));
			$map['l.add_time'] = array("lt",$xtime);
			$search['end_time'] = $xtime;	
		}
		if(session('admin_is_kf')==1)	$map['m.customer_id'] = session('admin_id');

		$field= 'l.id,l.add_time,m.user_name,l.affect_money,l.freeze_money,l.collect_money,(l.account_money+l.back_money) account_money,l.target_uname,l.type,l.info';
		$list = M('member_money l')->field($field)->join("{$this->pre}member m ON m.id=l.uid")->where($map)->limit($Lsql)->select();
		
		$type = C('MONEY_LOG');
		$row=array();
		$row[0]=array('序号','用户ID','用户名','交易对方','交易类型','影响金额','可用余额','冻结金额','待收金额','发生时间','备注');
		$i=1;
		foreach($list as $v){
				$row[$i]['i'] = $i;
				$row[$i]['uid'] = $v['id'];
				$row[$i]['card_num'] = $v['user_name'];
				$row[$i]['card_pass'] = $v['target_uname'];
				$row[$i]['card_mianfei'] = $type[$v['type']];
				$row[$i]['card_mianfei0'] = $v['affect_money'];
				$row[$i]['card_mianfei1'] = $v['account_money'];
				$row[$i]['card_mianfei2'] = $v['freeze_money'];
				$row[$i]['card_mianfei3'] = $v['collect_money'];
				$row[$i]['card_timelimit'] = date("Y-m-d H:i:s",$v['add_time']);
				$row[$i]['info'] = $v['info'];
				$i++;
		}
		
		$xls = new Excel_XML('UTF-8', false, 'datalist');
		$xls->addArray($row);
		$xls->generateXML("datalistcard");
	}

 	public function repay(){
		$map=array();
		if(!empty($_REQUEST['start_time']) && !empty($_REQUEST['end_time'])){
			$timespan = strtotime(urldecode($_REQUEST['start_time'])).",".strtotime(urldecode($_REQUEST['end_time']));
			$map['b.deadline'] = array("between",$timespan);
			$search['start_time'] = strtotime(urldecode($_REQUEST['start_time']));	
			$search['end_time'] = strtotime(urldecode($_REQUEST['end_time']));
			$query ="start_time=".$_REQUEST['start_time']."&amp;end_time=".$_REQUEST['end_time'];	
		}elseif(!empty($_REQUEST['start_time'])){
			$xtime = strtotime(urldecode($_REQUEST['start_time']));
			$map['b.deadline'] = array("gt",$xtime);
			$search['start_time'] = $xtime;
			$query = "start_time=".$_REQUEST['start_time'];	
		}elseif(!empty($_REQUEST['end_time'])){
			$xtime = strtotime(urldecode($_REQUEST['end_time']));
			$map['b.deadline'] = array("lt",$xtime);
			$search['end_time'] = $xtime;
			$query = $query."end_time=".$_REQUEST['end_time'];	
		}else{
			if(!empty($_REQUEST['day'])){
			    $day = $_REQUEST['day'];
			}else{
			    $day = 1;
			}
			$query = "day=".$day;
			$start = strtotime("+1 day",strtotime(date("Y-m-d",time())." 00:00:00"));
			$end = strtotime("+{$day} day",strtotime(date("Y-m-d",time())." 23:59:59"));
			$map['b.deadline'] = array(
						"between",
						"{$start},{$end}"
			);
			$search['start_time'] = $start;
			$search['end_time'] = $end;	
		}
		
		$map['b.status'] = 7;
		//$map['i.progress'] = 100;
	
		$list = M("transfer_investor b")->join("{$this->pre}transfer_borrow i ON i.id=b.borrow_id")->field('i.borrow_name,b.id,b.borrow_id,sum(b.capital) as bmoney,sum(b.interest) as interest, sum(b.capital+b.interest) as total,b.deadline')->where($map)->group('borrow_id')->select();
		$this->assign("search",$search);
		$this->assign('list',$list);
		$this->assign('query',$query);
		
        $this->display();
    }

	public function repayexport(){
		import("ORG.Io.Excel");

		$map=array();
		if(!empty($_REQUEST['start_time']) && !empty($_REQUEST['end_time'])){
			$timespan = strtotime(urldecode($_REQUEST['start_time'])).",".strtotime(urldecode($_REQUEST['end_time']));
			$map['b.deadline'] = array("between",$timespan);
			$search['start_time'] = strtotime(urldecode($_REQUEST['start_time']));	
			$search['end_time'] = strtotime(urldecode($_REQUEST['end_time']));	
		}elseif(!empty($_REQUEST['start_time'])){
			$xtime = strtotime(urldecode($_REQUEST['start_time']));
			$map['b.deadline'] = array("gt",$xtime);
			$search['start_time'] = $xtime;	
		}elseif(!empty($_REQUEST['end_time'])){
			$xtime = strtotime(urldecode($_REQUEST['end_time']));
			$map['b.deadline'] = array("lt",$xtime);
			$search['end_time'] = $xtime;	
		}else{
			if(!empty($_REQUEST['day'])){
			    $day = $_REQUEST['day'];
			}else{
			    $day = 1;
			}
			$start = strtotime("+1 day",strtotime(date("Y-m-d",time())." 00:00:00"));
			$end = strtotime("+{$day} day",strtotime(date("Y-m-d",time())." 23:59:59"));
			$map['b.deadline'] = array(
						"between",
						"{$start},{$end}"
			);
			$search['start_time'] = $start;
			$search['end_time'] = $end;	
		}
		
		$map['b.status'] = 7;
		$map['i.progress'] = 100;
		$pre = $this->pre;
		$list = M("transfer_investor b")->join("{$this->pre}transfer_borrow i ON i.id=b.borrow_id")->field('i.borrow_name,b.id,b.borrow_id,sum(b.capital) as bmoney,sum(b.interest) as interest, sum(b.capital+b.interest) as total,b.deadline')->where($map)->group('borrow_id')->select();
		$row=array();
		$row[0]=array('序号','标号ID','明日待还本金','明日待还利息','明日总待还金额','明日待还时间');
		$i=1;
		foreach($list as $v){
				$row[$i]['i'] = $i;
				$row[$i]['borrow_id'] = $v['borrow_id'];
				$row[$i]['borrow_name'] = $v['borrow_name'];
				$row[$i]['bmoney'] = $v['bmoney'];
				$row[$i]['interest'] = $v['interest'];
				$row[$i]['total'] = $v['total'];
				$row[$i]['deadline'] = date("Y-m-d H:i:s",$v['deadline']);
				$i++;
		}
		
		$xls = new Excel_XML('UTF-8', false, 'datalist');
		$xls->addArray($row);
		$xls->generateXML("datalistcard");
	}

	public function charge(){
		$map=array();
		if($_REQUEST['uid'] && $_REQUEST['uname']){
			$map['p.uid'] = $_REQUEST['uid'];
			$search['uid'] = $map['p.uid'];	
			$search['uname'] = urldecode($_REQUEST['uname']);	
		}
		
		if($_REQUEST['uname'] && !$search['uid']){
			$map['m.user_name'] = array("like",urldecode($_REQUEST['uname'])."%");
			$search['uname'] = urldecode($_REQUEST['uname']);	
		}
		
		if($_REQUEST['tran_id']){
			$map['p.tran_id'] = urldecode($_REQUEST['realname']);
			$search['tran_id'] = $map['p.tran_id'];	
		}
		
		if(isset($_REQUEST['status']) && $_REQUEST['status']!=""){
			$map['p.status'] = intval($_REQUEST['status']);
			$search['status'] = $map['p.status'];	
		}
		
		if($_REQUEST['way']){
			$map['p.way'] = $_REQUEST['way'];
			$search['way'] = $map['p.way'];	
		}
		
		if(!empty($_REQUEST['bj']) && !empty($_REQUEST['money'])){
			$map['p.money'] = array($_REQUEST['bj'],$_REQUEST['money']);
			$search['bj'] = $_REQUEST['bj'];	
			$search['money'] = $_REQUEST['money'];	
		}

		if(!empty($_REQUEST['start_time']) && !empty($_REQUEST['end_time'])){
			$timespan = strtotime(urldecode($_REQUEST['start_time'])).",".strtotime(urldecode($_REQUEST['end_time']));
			$map['p.add_time'] = array("between",$timespan);
			$search['start_time'] = urldecode($_REQUEST['start_time']);	
			$search['end_time'] = urldecode($_REQUEST['end_time']);	
		}elseif(!empty($_REQUEST['start_time'])){
			$xtime = strtotime(urldecode($_REQUEST['start_time']));
			$map['p.add_time'] = array("gt",$xtime);
			$search['start_time'] = $xtime;	
		}elseif(!empty($_REQUEST['end_time'])){
			$xtime = strtotime(urldecode($_REQUEST['end_time']));
			$map['p.add_time'] = array("lt",$xtime);
			$search['end_time'] = $xtime;	
		}
		//if(session('admin_is_kf')==1)	$map['m.customer_id'] = session('admin_id');

		
		//分页处理
		import("ORG.Util.Page");
		$count = M('member_payment p')->join("{$this->pre}member m ON p.uid=m.id")->where($map)->count('p.id');
		$p = new Page($count, $this->pagesize);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理
		
		$field= 'p.*,m.user_name';
		$list = M('member_payment p')->field($field)->join("{$this->pre}member m ON p.uid=m.id")->where($map)->limit($Lsql)->order("p.id DESC")->select();
		
        $this->assign("way", array('off'=>'线下充值','gfb'=>'国付宝','ips'=>'环迅支付','chinabank'=>'网银在线','baofoo'=>'宝付','tenpay'=>'财付通','ecpss'=>'汇潮支付','easypay'=>'易生支付','cmpay'=>'中国移动支付','allinpay'=>'通联支付'));
        $this->assign("bj", array("gt"=>'大于',"eq"=>'等于',"lt"=>'小于'));
        $this->assign("list", $list);
		$this->assign("status",C('PAYLOG_TYPE'));
        $this->assign("pagebar", $page);
        $this->assign("search", $search);
        $this->assign("query", http_build_query($search));
		
        $this->display();
    }
	
	public function chargeexport(){
		import("ORG.Io.Excel");
		alogs("Charge",0,1,'执行了会员充值记录列表导出操作！');//管理员操作日志
		$map=array();
		if($_REQUEST['uid'] && $_REQUEST['uname']){
			$map['p.uid'] = $_REQUEST['uid'];
			$search['uid'] = $map['p.uid'];	
			$search['uname'] = urldecode($_REQUEST['uname']);	
		}
		
		if($_REQUEST['uname'] && !$search['uid']){
			$map['m.user_name'] = array("like",urldecode($_REQUEST['uname'])."%");
			$search['uname'] = urldecode($_REQUEST['uname']);	
		}
		
		if($_REQUEST['tran_id']){
			$map['p.tran_id'] = urldecode($_REQUEST['realname']);
			$search['tran_id'] = $map['p.tran_id'];	
		}
		
		if(isset($_REQUEST['status']) && $_REQUEST['status']!=""){
			$map['p.status'] = intval($_REQUEST['status']);
			$search['status'] = $map['p.status'];	
		}
		
		if($_REQUEST['way']){
			$map['p.way'] = $_REQUEST['way'];
			$search['way'] = $map['p.way'];	
		}
		
		if(!empty($_REQUEST['bj']) && !empty($_REQUEST['money'])){
			$map['p.money'] = array($_REQUEST['bj'],$_REQUEST['money']);
			$search['bj'] = $_REQUEST['bj'];	
			$search['money'] = $_REQUEST['money'];	
		}

		if(!empty($_REQUEST['start_time']) && !empty($_REQUEST['end_time'])){
			$timespan = strtotime(urldecode($_REQUEST['start_time'])).",".strtotime(urldecode($_REQUEST['end_time']));
			$map['p.add_time'] = array("between",$timespan);
			$search['start_time'] = urldecode($_REQUEST['start_time']);	
			$search['end_time'] = urldecode($_REQUEST['end_time']);	
		}elseif(!empty($_REQUEST['start_time'])){
			$xtime = strtotime(urldecode($_REQUEST['start_time']));
			$map['p.add_time'] = array("gt",$xtime);
			$search['start_time'] = $xtime;	
		}elseif(!empty($_REQUEST['end_time'])){
			$xtime = strtotime(urldecode($_REQUEST['end_time']));
			$map['p.add_time'] = array("lt",$xtime);
			$search['end_time'] = $xtime;	
		}
		if(session('admin_is_kf')==1)	$map['m.customer_id'] = session('admin_id');

		$field= 'p.*,m.user_name';
		$list = M('member_payment p')->field($field)->join("{$this->pre}member m ON p.uid=m.id")->where($map)->limit($Lsql)->select();

		$status = C('PAYLOG_TYPE');
		$row=array();
		$row[0]=array('序号','用户ID','用户名','充值金额','充值手续费','充值状态','对账订单号','充值方式','充值时间');
		$i=1;
		foreach($list as $v){
				$row[$i]['i'] = $i;
				$row[$i]['uid'] = $v['id'];
				$row[$i]['card_1'] = $v['user_name'];
				$row[$i]['card_2'] = $v['money'];
				$row[$i]['card_3'] = $v['fee'];
				$row[$i]['card_4'] = $status[$v['status']];
				$row[$i]['card_5'] = $v['tran_id'];
				$row[$i]['card_6'] = $v['way'];
				$row[$i]['card_7'] = date("Y-m-d H:i:s",$v['add_time']);
				$i++;
		}
		
		$xls = new Excel_XML('UTF-8', false, 'datalist');
		$xls->addArray($row);
		$xls->generateXML("datalistcard");
	}

     public function withdraw(){
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
		
		$field= 'w.*,m.user_name,mm.account_money';
		$list = M('member_withdraw w')->field($field)->join("{$this->pre}member m ON w.uid=m.id")->join("ynw_member_account mm on w.uid = mm.uid")->where($map)->limit($Lsql)->order("w.id DESC")->select();
		
        $this->assign("bj", array("gt"=>'大于',"eq"=>'等于',"lt"=>'小于'));
        $this->assign("list", $list);
		$this->assign("status",C('WITHDRAW_STATUS'));
        $this->assign("pagebar", $page);
        $this->assign("search", $search);
        $this->assign("query", http_build_query($search));
		
        $this->display();
    }

	
	public function withdrawexport(){
		import("ORG.Io.Excel");
		alogs("Withdraw",0,1,'执行了会员提现记录列表导出操作！');//管理员操作日志
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
		if(session('admin_is_kf')==1)	$map['m.customer_id'] = session('admin_id');

		$field= 'w.*,m.user_name,mm.account_money';
		$list = M('member_withdraw w')->field($field)->join("{$this->pre}member m ON w.uid=m.id")->join("ynw_member_account mm on w.uid = mm.uid")->where($map)->limit($Lsql)->order("w.id DESC")->select();

		$status = C('WITHDRAW_STATUS');
		$row=array();
		$row[0]=array('序号','用户ID','用户名','提现金额','提现手续费','到账金额','提现状态','提现时间','处理时间','处理人');
		$i=1;
		foreach($list as $v){
				$row[$i]['i'] = $i;
				$row[$i]['uid'] = $v['id'];
				$row[$i]['card_1'] = $v['user_name'];
				$row[$i]['card_2'] = $v['withdraw_money'];
				$row[$i]['card_3'] = $v['second_fee'];
				$row[$i]['card_8'] =($v['withdraw_status']==3)?0:$v['success_money'];
				$row[$i]['card_4'] = $status[$v['withdraw_status']];
				$row[$i]['card_5'] = date("Y-m-d H:i:s",$v['add_time']);
				$row[$i]['card_6'] = ($v['deal_time']>0)?date("Y-m-d H:i:s",$v['deal_time']):"未处理";
				$row[$i]['card_7'] = (!empty($v['deal_user']))?$v['deal_user']:'';
				$i++;
		}
		
		$xls = new Excel_XML('UTF-8', false, 'datalist');
		$xls->addArray($row);
		$xls->generateXML("datalistcard");
	}
}
?>