<?php
class TopAction extends AdminAction{
	function _initialize(){
		$this->assign('from',array('网站','手机站','微信平台','APP客户端',9=>'后台添加'));
		$level = FS("data/conf/level");
		foreach(FS("data/conf/level") as $key => $val){
			$level[$key] = $val['name'];
		}
		$this->assign('level',$level);
		parent::_initialize();
	}
    public function market(){
    	$map = $this->parse($_GET['type'],'l.add_time');
		$data = M('member_score l')->where($map)->field("COUNT(l.`id`) `times`,SUM(l.affect_integral) `total`,m.user_name,m.reg_time,m.integral,m.from,m.user_leve `level`")->join("{$this->pre}member m ON m.id=l.uid")->where($map)->group("l.uid")->order('`total` DESC')->limit(20)->select();
		$this->assign("title", $title);
		$this->assign("data", $data);
        $this->display();
    }

    public function borrow(){
    	$map = $this->parse($_GET['type'],'l.add_time');
		$map['l.type'] = '17';
		$data = M('member_money l')->where($map)->field("COUNT(l.`id`) `times`,SUM(l.affect_money) `total`,m.user_name,m.reg_time,a.money_freeze `freeze`,a.account_money `money`,m.from,m.user_leve `level`")->join("{$this->pre}member m ON m.id=l.uid")->join("{$this->pre}member_account a ON a.uid=l.uid")->where($map)->group("l.uid")->order('`total` DESC')->limit(20)->select();
		$this->assign("title", $title);
		$this->assign("data", $data);
        $this->display();
    }

    public function invest(){
    	$map = $this->parse($_GET['type'],'l.add_time');
		$map['l.type'] = '28';
		$data = M('member_money l')->where($map)->field("COUNT(l.`id`) `times`,SUM(l.affect_money) `total`,m.user_name,m.reg_time,a.money_freeze `freeze`,a.account_money `money`,m.from,m.user_leve `level`")->join("{$this->pre}member m ON m.id=l.uid")->join("{$this->pre}member_account a ON a.uid=l.uid")->where($map)->group("l.uid")->order('`total` DESC')->limit(20)->select();
		$this->assign("title", $title);
		$this->assign("data", $data);
        $this->display();
    }

    public function tender(){
    	$map = $this->parse($_GET['type'],'l.add_time');
		$map['l.type'] = '6';
		$data = M('member_money l')->where($map)->field("COUNT(l.`id`) `times`,ABS(SUM(l.affect_money)) `total`,m.user_name,m.reg_time,a.money_freeze `freeze`,a.account_money `money`,m.from,m.user_leve `level`")->join("{$this->pre}member m ON m.id=l.uid")->join("{$this->pre}member_account a ON a.uid=l.uid")->where($map)->group("l.uid")->order('`total` DESC')->limit(20)->select();
		$this->assign("title", $title);
		$this->assign("data", $data);
        $this->display();
    }

    public function recharge(){
    	$map = $this->parse($_GET['type'],'l.add_time');
		$map['l.type'] = '27';
		$data = M('member_money l')->where($map)->field("COUNT(l.`id`) `times`,ABS(SUM(l.affect_money)) `total`,m.user_name,m.reg_time,a.money_freeze `freeze`,a.account_money `money`,m.from,m.user_leve `level`")->join("{$this->pre}member m ON m.id=l.uid")->join("{$this->pre}member_account a ON a.uid=l.uid")->where($map)->group("l.uid")->order('`total` DESC')->limit(20)->select();
		$this->assign("title", $title);
		$this->assign("data", $data);
        $this->display();
    }

    public function withdraw(){
    	$map = $this->parse($_GET['type'],'l.add_time');
		$map['l.type'] = '29';
		$data = M('member_money l')->where($map)->field("COUNT(l.`id`) `times`,ABS(SUM(l.affect_money)) `total`,m.user_name,m.reg_time,a.money_freeze `freeze`,a.account_money `money`,m.from,m.user_leve `level`")->join("{$this->pre}member m ON m.id=l.uid")->join("{$this->pre}member_account a ON a.uid=l.uid")->where($map)->group("l.uid")->order('`total` DESC')->limit(20)->select();
		$this->assign("title", $title);
		$this->assign("data", $data);
        $this->display();
    }

    public function login(){
    	$map = $this->parse($_GET['type'],'reg_time');
		$map['login_times'] = array('gt',0);
		$data = M('member')->where($map)->field("login_times `total`,user_name,reg_time,integral,`from`,user_leve `level`")->where($map)->order('`total` DESC')->limit(20)->select();
		$this->assign("title", $title);
		$this->assign("data", $data);
        $this->display();
    }

    public function invite(){
    	$map = $this->parse($_GET['type'],'reg_time');
		$map['i.recommend_id'] = array('gt',0);
		$data = M('member m')->where($map)->field("COUNT(i.`id`) `total`,m.user_name,m.reg_time,m.integral,m.from,m.user_leve `level`")->join("{$this->pre}member i ON m.id=i.recommend_id")->where($map)->group('i.recommend_id')->order('`total` DESC')->limit(20)->select();
		$this->assign("title", $title);
		$this->assign("data", $data);
        $this->display();
    }

    private function parse($type,$field){
    	switch ($type) {
    		case 'year':
    			$title = date('Y-01-01 00:00:00').' 至 '.date('Y-12-31 23:59:59');
    			$map[$field] = array('BETWEEN',array(strtotime(date('Y-01-01 00:00:00')),strtotime(date('Y-12-31 23:59:59'))));
    			break;
			case 'week':
				$week = date('w');
				$zy = date('Y-m-d 00:00:00',strtotime('-'.($week==0?6:$week-1).' day'));
				$zr = date('Y-m-d 23:59:59',strtotime('+'.($week==0?0:7-$week).' day'));
				$title = $zy.' 至 '.$zr;
    			$map[$field] = array('BETWEEN',array(strtotime($zy),strtotime($zr)));
    			break;
    		case 'month':
    			$title = date('Y-m-01 00:00:00').' 至 '.date('Y-m-t 23:59:59');
    			$map[$field] = array('BETWEEN',array(strtotime(date('Y-m-01 00:00:00')),strtotime(date('Y-m-t 23:59:59'))));
    			break;    		
    		case 'day':
    			$title = date('Y-m-d 00:00:00').' 至 '.date('Y-m-d 23:59:59');
    			$map[$field] = array('BETWEEN',array(strtotime(date('Y-m-d 00:00:00')),strtotime(date('Y-m-d 23:59:59'))));
    			break;
    	}
    	return $map;
    }

}
?>