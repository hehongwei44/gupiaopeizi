<?php

class ExpiredAction extends AdminAction
{
    /**
    +----------------------------------------------------------
    * 默认操作
    +----------------------------------------------------------
    */
	public function _initialize(){
	 	$this->assign("pagetabs",C('BORROW_TYPE'));
	 	parent::_initialize();
	}
    public function index(){
		$map=array();
		$map['d.status'] = array("neq",0);
		$map['b.homs_id'] = 0;
		if(intval($_REQUEST['tab'])>0){
			$map['b.borrow_type'] = intval($_REQUEST['tab']);
		}
		$map['d.repayment_time'] = 0;

		if(intval($_REQUEST['day'])>0){
			$this->assign("title", '未来 '.intval($_REQUEST['day']).' 天内待还款借款');
			$_REQUEST['start_time'] = date('Y-m-d 00:00:00');
			$_REQUEST['end_time'] = date('Y-m-d 23:59:59',strtotime("+".intval($_REQUEST[3])." day"));
		}else{
			$map['d.deadline'] = array("between","100000,".time());
		}


		if($_REQUEST['uid'] && $_REQUEST['uname']){
			$map['d.borrow_uid'] = $_REQUEST['uid'];
			$search['uid'] = $map['d.borrow_uid'];	
			$search['uname'] = urldecode($_REQUEST['uname']);	
		}
		
		if($_REQUEST['uname'] && !$search['uid']){
			$map['m.user_name'] = array("like",urldecode($_REQUEST['uname'])."%");
			$search['uname'] = urldecode($_REQUEST['uname']);	
		}
		
		if($_REQUEST['status']){
			if($_REQUEST['status']==1) $map['d.substitute_money'] = array("gt",0);
			elseif($_REQUEST['status']==2) $map['d.substitute_money'] = array("elt",0);
			$search['status'] = intval($_REQUEST['status']);	
		}

		if(!empty($_REQUEST['bj']) && !empty($_REQUEST['money'])){
			$map['capital'] = array($_REQUEST['bj'],$_REQUEST['money']);
			$search['bj'] = $_REQUEST['bj'];	
			$search['money'] = $_REQUEST['money'];	
		}



		if(!empty($_REQUEST['start_time']) && !empty($_REQUEST['end_time'])){
			$timespan = strtotime(urldecode($_REQUEST['start_time'])).",".strtotime(urldecode($_REQUEST['end_time']));
			$map['d.deadline'] = array("between",$timespan);
			$search['start_time'] = urldecode($_REQUEST['start_time']);	
			$search['end_time'] = urldecode($_REQUEST['end_time']);	
		}elseif(!empty($_REQUEST['start_time'])){
			$xtime = strtotime(urldecode($_REQUEST['start_time']));
			$map['d.deadline'] = array("between",$xtime.",".time());
			$search['start_time'] = $xtime;	
		}elseif(!empty($_REQUEST['end_time'])){
			$xtime = strtotime(urldecode($_REQUEST['end_time']));
			$map['d.deadline'] = array("between",time().",".$xtime);
			$search['end_time'] = $xtime;	
		}


		if(session('admin_is_kf')==1)	$map['m.customer_id'] = session('admin_id');
		//分页处理
		import("ORG.Util.Page");
		$buildSql = M('investor d')->field("d.id")->join("{$this->pre}borrow b ON b.id=d.borrow_id")->join("{$this->pre}member m ON m.id=b.borrow_uid")->where($map)->group('d.sort_order,d.borrow_id')->buildSql();
		$newsql = M()->query("select count(*) as tc from {$buildSql} as t");
		$count = $newsql[0]['tc'];
		$p = new Page($count,$this->pagesize);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理
		
		$field = "m.user_name,d.borrow_id as id,b.borrow_name,d.status,d.total,d.borrow_id,b.borrow_uid,d.sort_order,sum(d.capital) as capital,sum(d.interest) as interest,sum(d.substitute_money) as substitute_money,d.deadline,b.borrow_duration";
		$list = M('investor d')->field($field)->join("{$this->pre}borrow b ON b.id=d.borrow_id")->join("{$this->pre}member m ON m.id=b.borrow_uid")->where($map)->group('d.sort_order,d.borrow_id')->order('d.borrow_id,d.sort_order')->limit($Lsql)->select();
		$list = $this->_listFilter($list);
		
        $this->assign("bj", array("gt"=>'大于',"eq"=>'等于',"lt"=>'小于'));
        $this->assign("status", array("1"=>'已代还',"2"=>'未代还'));
        $this->assign("list", $list);
        $this->assign("pagebar", $page);
        $this->assign("search", $search);
        $this->assign("query", http_build_query($search));
		
        $this->display();
    }


    public function member()
    {
		$map=array();
		//$map['_string'] = ' (d.repayment_time=0 AND d.deadline<'.time().' AND d.status=0)  OR ( d.substitute_time >0 ) ';
		$map['_string'] = ' (d.repayment_time=0 AND d.deadline <'.time().' AND d.status=7)';
		if($_REQUEST['uname']){
			if($_REQUEST['uid']){
				$map['d.borrow_uid'] = $_REQUEST['uid'];
				$search['uid'] = $map['d.borrow_uid'];	
				$search['uname'] = urldecode($_REQUEST['uname']);	
			}else{
				$uid = M("member")->getFieldByUserName(urldecode($_REQUEST['uname']),"id");
				$map['d.borrow_uid'] = $uid;
				$search['uid'] = $map['d.borrow_uid'];	
				$search['uname'] = urldecode($_REQUEST['uname']);	
			}
		}
		if(session('admin_is_kf')==1)	$map['m.customer_id'] = session('admin_id');
		
		//分页处理
		import("ORG.Util.Page");
		$xcount = M('investor d')->field("d.id")->where($map)->group('d.borrow_uid')->buildSql();
		$newxsql = M()->query("select count(*) as tc from {$xcount} as t");
		$count = $newxsql[0]['tc'];
		$p = new Page($count, $this->pagesize);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理
		
		$buildSql = M('investor d')->field("count(*) as num,sum(d.capital) as capital_all,borrow_uid")->where($map)->group('d.sort_order,d.borrow_id')->buildSql();
		$list = M()->query("select count(*) as tc,sum(t.capital_all) as total_expired,t.borrow_uid,t.borrow_uid as id,m.user_name  from {$buildSql} as t  left join {$this->pre}member m ON m.id=t.borrow_uid group by t.borrow_uid limit {$Lsql}");
		$list = $this->_listFilter($list);
		
        $this->assign("bj", array("gt"=>'大于',"eq"=>'等于',"lt"=>'小于'));
        $this->assign("status", array("1"=>'已代还',"2"=>'未代还'));
        $this->assign("list", $list);
        $this->assign("pagebar", $page);
        $this->assign("search", $search);
        $this->assign("query", http_build_query($search));
		
        $this->display();
    }


	public function doexpired(){
		$borrow_id = intval($_GET['id']);
		$sort_order = intval($_GET['sort_order']);
		$vo = M('investor')->where("borrow_id={$borrow_id} AND sort_order={$sort_order} AND substitute_money>0")->find();
		if(is_array($vo)) $this->error("已代还过了");
		else $newid = borrowRepayment($borrow_id,$sort_order,2);
		
		if($newid===true) $this->success("代还成功");
		elseif($newid) $this->error($newid);
		else  $this->error("代还失败，请重试");
	}

	private function _listFilter($list){
		$row=array();
		foreach($list as $key=>$v){
			$v['breakday'] = getExpiredDays($v['deadline']);
			$v['expired_money'] = getExpiredMoney($v['breakday'],$v['capital'],$v['interest']);
			$v['call_fee'] = getExpiredCallFee($v['breakday'],$v['capital'],$v['interest']);
			$row[$key]=$v;
		}
		return $row;
	}
	
	
	
}
?>