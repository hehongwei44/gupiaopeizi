<?php
class TradeAction extends AdminAction{

    public function index(){
		$map=array();
		$map['b.borrow_status'] = $_GET['status'];
		$map['b.homs_id'] = array('gt',0);
		if($_REQUEST['type']){
			$map['b.borrow_type'] = intval($_REQUEST['type']);
		}
		if($_REQUEST['status']==8){
			D('borrow')->where('deadline < '.time().' AND borrow_status=6')->save(array('borrow_status=8'));
		}
		if(!empty($_REQUEST['uname'])&&!$_REQUEST['uid'] || $_REQUEST['uname']!=$_REQUEST['olduname']){
			$uid = M("member")->getFieldByUserName(text($_REQUEST['uname']),'id');
			$map['b.borrow_uid'] = $uid;
			$search['uid'] = $map['b.borrow_uid'];
			$search['uname'] = $_REQUEST['uname'];
		}
		if( !empty($_REQUEST['uid'])&&!isset($search['uname']) ){
			$map['b.borrow_uid'] = intval($_REQUEST['uid']);
			$search['uid'] = $map['b.borrow_uid'];
			$search['uname'] = $_REQUEST['uname'];
		}

		if(!empty($_REQUEST['bj']) && !empty($_REQUEST['money'])){
			$map['b.borrow_money'] = array($_REQUEST['bj'],$_REQUEST['money']);
			$search['bj'] = $_REQUEST['bj'];
			$search['money'] = $_REQUEST['money'];
		}

		if(!empty($_REQUEST['start_time']) && !empty($_REQUEST['end_time'])){
			$timespan = strtotime(urldecode($_REQUEST['start_time'])).",".strtotime(urldecode($_REQUEST['end_time']));
			$map['b.add_time'] = array("between",$timespan);
			$search['start_time'] = urldecode($_REQUEST['start_time']);
			$search['end_time'] = urldecode($_REQUEST['end_time']);
		}elseif(!empty($_REQUEST['start_time'])){
			$xtime = strtotime(urldecode($_REQUEST['start_time']));
			$map['b.add_time'] = array("gt",$xtime);
			$search['start_time'] = $xtime;
		}elseif(!empty($_REQUEST['end_time'])){
			$xtime = strtotime(urldecode($_REQUEST['end_time']));
			$map['b.add_time'] = array("lt",$xtime);
			$search['end_time'] = $xtime;
		}

		//if(session('admin_is_kf')==1){
		//		$map['m.customer_id'] = session('admin_id');
		//}else{
			if($_REQUEST['customer_id'] && $_REQUEST['customer_name']){
				$map['m.customer_id'] = $_REQUEST['customer_id'];
				$search['customer_id'] = $map['m.customer_id'];
				$search['customer_name'] = urldecode($_REQUEST['customer_name']);
			}

			if($_REQUEST['customer_name'] && !$search['customer_id']){
				$cusname = urldecode($_REQUEST['customer_name']);
				$kfid = M('users')->getFieldByUserName($cusname,'id');
				$map['m.customer_id'] = $kfid;
				$search['customer_name'] = $cusname;
				$search['customer_id'] = $kfid;
			}
		//}
		//分页处理
		import("ORG.Util.Page");
		$count = M('borrow b')->join("{$this->pre}member m ON m.id=b.borrow_uid")->where($map)->count('b.id');
		$p = new Page($count, $this->pagesize);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理

		$field= 'b.id,b.borrow_name,b.borrow_uid,b.borrow_duration,b.borrow_type,b.updata,b.borrow_money,b.borrow_fee,b.borrow_interest_rate,b.deposit,b.deal_start,b.add_time,m.user_name,m.id mid,b.is_tuijian,risk_rate';
		$list = M('borrow b')->field($field)->join("{$this->pre}member m ON m.id=b.borrow_uid")->where($map)->limit($Lsql)->order("b.id DESC")->select();

		$list = $this->_listFilter($list);

        $this->assign("bj", array("gt"=>'大于',"eq"=>'等于',"lt"=>'小于'));
        $this->assign("list", $list);
        $this->assign("pagebar", $page);
        $this->assign("search", $search);
		$this->assign("xaction",ACTION_NAME);

        $this->assign("query", http_build_query($search));
        $this->display();
    }


    public function detail() {
        $model = D('trade');
        $id = intval($_REQUEST['id']);

        $vo = $model->find($id);
        $vo['account_all'] = $vo['account']+$vo['account_pz'];

        $user = D('member')->find($vo['trade_uid']);;
        $vo['user_name'] = $user['user_name'];
		$f_type =C('FEEDBACK_TYPE');
		$vo['type'] = $f_type[$vo['type']];
		$model->where("id={$id}")->setField('status',1);
        $this->assign('vo', $vo);
        $this->display();
    }

    public function audit() {
        $borrow = D('borrow');
        $member = D('member');
        $homs = D('homs');
		$data = $borrow->find(intval($_REQUEST['id']));

        if($_POST){
        	$post['id'] = $data['id'];
        	$post['borrow_status'] = $_POST['status'];
        	$post['deadline'] = strtotime($_POST['deadline']);
        	$post['first_verify_time'] = time();
    		//如果审核通过
    		if($post['borrow_status']=='6'){
    			$post['homs_id'] = $_POST['homs'];
				if(intval($_POST['homs'])<1){
	        		$this->error('不选择恒生帐号，无法审核通过该配资申请！');
	        	}
    			$hd = $homs->find($_POST['homs']);
    			$save['uid'] = $data['borrow_uid'];
    			$save['times'] = $hd['times']+1;
    			$save['start'] = time();
    			$save['expire'] = $data['deadline'];
    			$homs->where('`id` = '.$_POST['homs'].' AND uid=0')->save($save);

    			$log['borrow'] = $data['borrow_uid'];
    			$log['memo'] = "保证金已划为操盘资金，配资单号：PZ".$data['id'];
    			logMoney($save['uid'],80,$data['deposit'],$log);

				if($data['borrow_fee']>0){
		            $log['memo'] = "扣除".$this->type[$_POST['type']]."配资管理费，配资单号：PZ".$data['borrow_uid'];
		            logMoney($data['borrow_uid'],82,-floatval($data['borrow_fee']),$log);
				}

				if($data['borrow_interest_rate']>0){
					$interest = ($data['borrow_interest_rate']/12/100)*$data['borrow_money'];
		            $log['memo'] = "解冻按月配资当月利息，配资单号：PZ".$data['borrow_uid'];
		            logMoney($data['borrow_uid'],82,-floatval($interest),$log);
				}

				if($data['borrow_type']==9){
					$data['borrow_duration'] = 30;
				}
				$data['deadline'] = strtotime('+'.$data['borrow_duration'].' day');

	            D('Member')->where('id='.$data['borrow_uid'])->setInc('stock_times',1);

    			$message = '审核成功，保证金已划为操盘资金。';
    		}else{
    			$data['deadline'] = time();
    			$total = $data['deposit']+$data['borrow_fee']+(($data['borrow_interest_rate']/12/100)*$data['borrow_money']);
				$log['borrow'] = $post['id'];
    			$log['memo'] = "该配资申请已经作废，配资单号：PZ".$data['id'];
				logMoney($data['borrow_uid'],16,$total,$log);
    			$message = '审核成功，该配资申请已经作废。';
    		}
			if($borrow->save($post)){
				$this->success($message);
			}
        }else{
	        $user = $member->find($data['borrow_uid']);
	        $data['user_name'] = $user['user_name'];
			$data['type_name'] = $data['borrow_type']=='8' ? '按天' : '按月';
			$data['deal_day'] = $data['deal_start']=='1' ? '下个交易日' : '今天';
			$data['total'] = $data['deposit']+$data['borrow_fee'];
			$data['homs'] = D('homs')->where('`uid`=0 AND `type`='.$data['borrow_type'])->getField('`id`,`name`');
	        $this->assign('data', $data);
	        $this->display();
        }

    }

	public function _listFilter($list){
		foreach($list as $key=>$v){
			$v['borrow_interest_rate'] = $v['borrow_interest_rate']/12;
			$v['risk_money'] = intval($v['borrow_money']/$v['risk_rate']).'.00';
			$row[$key]=$v;
		}
		return $row;
	}

}
?>