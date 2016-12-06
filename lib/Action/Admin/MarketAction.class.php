<?php
class MarketAction extends AdminAction{
    public function index(){
    	$type_list = C('INTEGRAL_LOG');
		$map=array();
		if($_REQUEST['uname'] && !$search['uid']){
			$map['m.user_name'] = array("eq",urldecode($_REQUEST['uname']));
			$search['uname'] = urldecode($_REQUEST['uname']);	
		}
		
		if(!empty($_REQUEST['start_time']) && !empty($_REQUEST['end_time'])){
			$timespan = strtotime($_REQUEST['start_time']).",".strtotime($_REQUEST['end_time']);
			$map['l.add_time'] = array("between",$timespan);
			$search['start_time'] = $_REQUEST['start_time'];	
			$search['end_time'] = $_REQUEST['end_time'];	
		}

		
		$mlogs = M('member_score l')->field("l.type,sum(l.affect_integral) AS integral")->join("{$this->pre}member m ON m.id=l.uid")->where($map)->group("l.type")->select();
		foreach ($mlogs as $k => $v) {
			$status_list[$v['type']] = $v['integral'];
		}

		if($status_list){
			$status_html = '<div style="float:right;font-weight:normal;font-size:14px">';
			if($map['l.add_time'])	$status_html .= '自 <font style="color:red;">'.$_REQUEST['start_time'].'</font> 至 <font style="color:red;">'.$_REQUEST['end_time'].'</font> ';

			for ($i=0; $i < 5; $i++) { 
				if($status_list[$i])	$status_html .= $type_list[$i].': <font style="color:red;">'.$status_list[$i].'</font> 分 ';
			}
			$status_html .= '</div>';

			$this->assign('status_html',$status_html);
		}

		if(isset($_REQUEST['type']) && $_REQUEST['type'] != ''){
			$map['l.type'] = intval($_REQUEST['type']);
			$search['type'] = $map['l.type'];	
		}

		//分页处理
		import("ORG.Util.Page");
		$count = M('member_score l')->join("{$this->pre}member m ON m.id=l.uid")->where($map)->count('l.id');
		$p = new Page($count, $this->pagesize);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理
		
		$field= 'l.id,l.uid,l.add_time,m.user_name,l.affect_integral,l.active_integral,l.account_integral,l.type,l.info';
		$order = "l.id DESC";
		$list = M('member_score l')->field($field)->join("{$this->pre}member m ON m.id=l.uid")->where($map)->order($order)->limit($Lsql)->select();
		
        $this->assign("bj", array("gt"=>'大于',"eq"=>'等于',"lt"=>'小于'));
        $this->assign("type", $type_list);
        $this->assign("list", $list);
        $this->assign("pagebar", $page);
        $this->assign("search", $search);
        $this->assign("query", http_build_query($search));
		
        $this->display();
    }

    public function top(){
    	switch ($_GET['type']) {
    		case 'year':
    			$title = date('Y-01-01 00:00:00').' 至 '.date('Y-12-31 23:59:59');
    			$map['l.add_time'] = array('BETWEEN',array(strtotime(date('Y-01-01 00:00:00')),strtotime(date('Y-12-31 23:59:59'))));
    			break;
			case 'week':
				$week = date('w');
				$zy = date('Y-m-d 00:00:00',strtotime('-'.($week==0?6:$week-1).' day'));
				$zr = date('Y-m-d 23:59:59',strtotime('+'.($week==0?0:7-$week).' day'));
				$title = $zy.' 至 '.$zr;
    			$map['l.add_time'] = array('BETWEEN',array(strtotime($zy),strtotime($zr)));
    			break;
    		case 'month':
    			$title = date('Y-m-01 00:00:00').' 至 '.date('Y-m-t 23:59:59');
    			$map['l.add_time'] = array('BETWEEN',array(strtotime(date('Y-m-01 00:00:00')),strtotime(date('Y-m-t 23:59:59'))));
    			break;    		
    		case 'day':
    			$title = date('Y-m-d 00:00:00').' 至 '.date('Y-m-d 23:59:59');
    			$map['l.add_time'] = array('BETWEEN',array(strtotime(date('Y-m-d 00:00:00')),strtotime(date('Y-m-d 23:59:59'))));
    			break;
    	}

		$data = M('member_score l')->where($map)->field("count(l.`id`) `times`,sum(l.affect_integral) `total`,m.user_name,m.reg_time,m.integral")->join("{$this->pre}member m ON m.id=l.uid")->where($map)->group("l.uid")->order('`total` DESC')->limit(20)->select();
		$this->assign("title", $title);
		$this->assign("data", $data);
        $this->display();
    }

	public function order(){
		$type_list = C('MARKET_LOG');
		$map=array();
		if($_REQUEST['uname']){
			$map['m.user_name'] = array("like",urldecode($_REQUEST['uname'])."%");
			$search['uname'] = urldecode($_REQUEST['uname']);	
		}

		if(!empty($_REQUEST['start_time'])&& !empty($_REQUEST['end_time'])){
			$start_time = strtotime($_REQUEST['start_time']);
			$end_time = strtotime($_REQUEST['end_time']);
			$map['l.add_time'] = array("between","{$start_time},{$end_time}");
			$search['start_time'] = $_REQUEST['start_time'];
			$search['end_time'] = $_REQUEST['end_time'];
		}

		if($_REQUEST['type']){
			$map['l.type'] = intval($_REQUEST['type']);
			$search['type'] = intval($_REQUEST['type']);	
		}
		
		if($_REQUEST['status']!=""){
			$map['l.status'] = intval($_REQUEST['status']);
			$search['status'] = intval($_REQUEST['status']);	
		}
		$search['way'] = intval($_REQUEST['way']);
		$map['l.way'] = intval($_REQUEST['way']);


		//分页处理
		import("ORG.Util.Page");
		$count = M('market_order l')->join("{$this->pre}member m ON m.id=l.uid")->where($map)->count('l.id');
		$p = new Page($count, $this->pagesize);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理

		$mlogs = M('market_order l')->field("type,count(l.id) AS num,sum(l.price) AS money")->join("{$this->pre}member m ON m.id=l.uid")->where($map)->group("type")->select();
		foreach ($mlogs as $k => $v) {
			$ty_list[$v['type']] = $v;
		}

		$status_html = '<div style="float:right; padding-right: 25px;">';
		if($ty_list){
			if($map['l.add_time'])	$status_html .= '自<font style="color:red;">'.$_REQUEST['start_time'].'</font>至<font style="color:red;">'.$_REQUEST['end_time'].'</font> ';

			for ($i=0; $i < 4; $i++) { 
				if($ty_list[$i])	$status_html .= $type_list[$i].'<font style="color:red;">'.$ty_list[$i]['num'].'</font>次 总价值<font style="color:red;">'.$ty_list[$i]['money'].'</font>元 ';
			}
		}
		$money = M('market_order l')->join("{$this->pre}member m ON m.id=l.uid")->where($map)->sum("l.price");
		$status_html .= '当前选中<font style="color:red;">'.$count.'</font>项 总价值<font style="color:red;">'.$money.'</font>元 ';
		$status_html .= '</div>';
		$this->assign('status_html',$status_html);
		
		$logtype = C('MARKET_LOG');
		$logstatus = C('MARKET_TYPE');
		$logway = C('MARKET_WAY');
		$list = M('market_order l')->field("l.*,m.user_name")->join("{$this->pre}member m ON m.id=l.uid")->where($map)->order("l.id DESC")->limit($Lsql)->select();
		foreach($list as $key=>$v){
			$list[$key]['type_s'] = $logtype[$v['type']];
			$list[$key]['status_s'] = $logstatus[$v['status']];
			$list[$key]['way_s'] = $logway[$v['way']];
		}

        $this->assign("list", $list);
        $this->assign("pagebar", $page);
        $this->assign("search", $search);
        $this->assign("logtype", $logtype);
        $this->assign("logstatus", $logstatus);
        $this->assign("logway", $logway);
        $this->assign("query", http_build_query($search));
        $this->display();
	}


	public function order_edit(){
		$id=intval($_GET['id']);
		$vo = M('market_order')->find($id);
		$vo['uname'] = M('member')->getFieldById($vo['uid'],'user_name');
		$list = M('market_address')->field(true)->where("uid={$vo['uid']}")->find();
		$this->assign("vo",$vo);
		$this->assign("vl",$list);
		$this->assign("type_list",C('MARKET_TYPE'));
		$this->assign("way_list",C('MARKET_WAY'));
		$this->display();
	}

	public function doorder(){
		$model = D('market_order');
        if (false === $model->create()) {
            $this->error($model->getError());
        }

		$model->status = intval($_POST['status']);
        if ($result = $model->save()) { //保存成功
            $this->assign('jumpUrl', __URL__."/getlog");
            $this->success(L('审核成功'));
        } else {
            //失败提示
            $this->error(L('审核失败'));
        }
	}

	public function goods(){
		if($_GET['category']){
			$map['category'] = intval($_GET['category']);
		}
		//分页处理
		import("ORG.Util.Page");
		$count = M('market_goods')->where($map)->count('id');
		$p = new Page($count, $this->pagesize);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";

		$list = M('market_goods')->where($map)->select();
		$this->assign("list",$list);
        $this->assign("pagebar", $page);
		$this->display();
    }

    public function good_edit(){
    	if($_REQUEST['id']){
			$id = intval($_REQUEST['id']);
			//$good = M('market_goods')->where("is_sys=1 AND id={$id}")->find();
			$good = M('market_goods')->where("id={$id}")->find();
	        $this->assign("vo", $good);
		}
		
        $this->display();
    }
	
    public function doGoodEdit(){
    	// var_dump($_REQUEST);exit();
		$data['name'] = text($_REQUEST['name']);
		$data['description'] = text($_REQUEST['description']);
		$data['style'] = text($_REQUEST['style']);
		//$data['img'] = text($_REQUEST['img']);
		///////////////////////////////////////////////
		//积分商城首页产品图片展示处理
		/*if(!empty($_FILES['imgfile']['name'])){
			$this->fix = false;
			$this->saveRule = 'uniqid';
			$this->savePathNew = '/res/market/images/'. date("YmdHis",time()).rand(0,1000)."/" ;
			$this->thumbMaxWidth = 494;
			$this->thumbMaxHeight = 315;
			$info = $this->CUpload();
		
			$data['img'] = $info[0]['savepath'].$info[0]['savename'];
			$data['smallPic'] = $info[1]['savepath'].$info[1]['savename'];
		}*/
		///////////////////////////////////////////////
		//$data['show_img'] = text($_REQUEST['show_img']);

		$data['jianjie'] = get_remote_img($_REQUEST['jianjie']);
		$data['canshu'] = get_remote_img($_REQUEST['canshu']);
		
		$data['price'] = intval($_REQUEST['price']);
		$data['cost'] = intval($_REQUEST['cost']);
		$data['order_sn'] = intval($_REQUEST['order_sn']);
		$data['is_sys'] = intval($_REQUEST['is_sys']);
		
		$data['number'] = intval($_REQUEST['number']);
		$data['category'] = intval($_REQUEST['category']);
        $data['amount'] = intval($_REQUEST['amount']);
        $data['add_time'] = time();
		
        if(!empty($_REQUEST['id'])){
			$data['id'] = intval($_REQUEST['id']);
			$newid = M('market_goods')->save($data);
		}else{
			$newid = M('market_goods')->add($data);
		}
        
        if($newid){
			alogs("MarketAdd",$newid,1,'成功执行了积分商城商品的添加操作！');//管理员操作日志
            $this->assign('jumpUrl', __URL__."/goods");
        	$this->success("添加成功");
        }else {
			alogs("MarketAdd",$newid,0,'执行积分商城商品的添加操作失败！');//管理员操作日志
			$this->error("添加失败");
		}
    }
	
	//删除商品
	public function good_del(){
    	if(!empty($_REQUEST['delid'])){
			$id = intval($_REQUEST['delid']);
			$del = M('market_goods')->where("id={$id}")->delete();
		}
		
        if($del){
			alogs("MarketDel",0,1,'成功执行了积分商城商品的删除操作！');//管理员操作日志
            $this->assign('jumpUrl', __URL__."/goods");
        	$this->success("删除成功");
        }else{
			alogs("MarketDel",0,0,'执行积分商城商品的删除操作失败！');//管理员操作日志
			$this->error("删除失败");
		}
    }
	
	//////////////////////////////////////////////////////上传商品图片////////////////////////
	public function upload_shop_pic(){
		$shopid = intval($_GET['id']);
		$this->assign("shopid",$shopid);
		$this->display();
	}

	///////////////////////////////////////////////////////
	
	////////////////////////积分抽奖开始////////////////////////
	public function lottery(){

		//分页处理
		import("ORG.Util.Page");
		$count = M('market_lottery')->count('id');
		$p = new Page($count, $this->pagesize);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";

		$list = M('market_lottery')->limit($Lsql)->select();
		foreach($list as $key=>$v){
			if($v['category']=='1'){
				$list[$key]['category']='礼金';
			}elseif($v['category']=='2'){
				$list[$key]['category']='积分';
			}elseif($v['category']=='3'){
				$list[$key]['category']='奖品';
			}
			
			if($v['is_sys']=='1'){
				$list[$key]['is_sys']='正在抽奖';
			}else{
				$list[$key]['is_sys']='已下架';
			}
		}
		$this->assign("list",$list);
        $this->assign("pagebar", $page);
		$this->display();
    }

    public function lottery_edit(){
    	if($_REQUEST['id']){
			$id = intval($_REQUEST['id']);
			$good = M('market_lottery')->where("id={$id}")->find();
	        $this->assign("vo", $good);
		}
		
        $this->display();
    }
	
    public function doLotteryEdit(){
    	// var_dump($_REQUEST);exit();
//		$data['title'] = text($_REQUEST['title']);
//		$data['num'] = intval($_REQUEST['num']);
//		$data['last_num'] = intval($_REQUEST['last_num']);
//		$data['rate'] = intval($_REQUEST['rate']);
//		$data['hits'] = intval($_REQUEST['num'])-intval($_REQUEST['last_num']);
//		$data['value'] = intval($_REQUEST['value']);
//		$data['order_sn'] = intval($_REQUEST['order_sn']);
//		$data['is_sys'] = intval($_REQUEST['is_sys']);
//		$data['category'] = $_REQUEST['category'];
//		$data['add_ip'] = get_client_ip();
//      $data['add_time'] = time();
		$model = M('market_lottery');
		if(false === $model->create()){
		    $this->error($model->getError());
		}
		$model->startTrans();
		$model->title = text($_REQUEST['title']);
		$model->num = intval($_REQUEST['num']);
		$model->last_num = intval($_REQUEST['last_num']);
		$model->rate = intval($_REQUEST['rate']);
		$model->hits = intval($_REQUEST['num'])-intval($_REQUEST['last_num']);
		$model->value = intval($_REQUEST['value']);
		$model->order_sn = intval($_REQUEST['order_sn']);
		$model->is_sys = intval($_REQUEST['is_sys']);
		$model->category = $_REQUEST['category'];
		$model->add_ip = get_client_ip();
        $model->add_time = time();
		
		
		//奖品图片
		if(!empty($_FILES['imgfile']['name'])){
			$this->saveRule = date("YmdHis",time()).rand(0,1000);
			$this->savePathNew = C('ADMIN_UPLOAD_DIR').'Product/';
			$this->thumbMaxWidth = C('PRODUCT_UPLOAD_W');
			$this->thumbMaxHeight = C('PRODUCT_UPLOAD_H');
			$info = $this->CUpload();
			$data['b_img'] = $info[0]['savepath'].$info[0]['savename'];
		}
		if($data['b_img']) $model->b_img = $data['b_img'];
        if(!empty($_REQUEST['id'])){
			$data['id'] = intval($_REQUEST['id']);
			$newid = $model->save();
		}else{
			$newid = $model->add();
		}
        
        if($newid){
			$model->commit();
			alogs("Market_lottery_Add",$newid,1,'成功执行了积分商城抽奖奖品的添加操作！');//管理员操作日志
            $this->assign('jumpUrl', __URL__."/lottery");
        	$this->success("添加成功");
        }else {
			$model->rollback();
			alogs("Market_lottery_Add",$newid,0,'执行积分商城抽奖奖品的添加操作失败！');//管理员操作日志
			$this->error("添加失败");
		}
    }
	
	//删除商品
	public function lottery_del(){
    	if(!empty($_REQUEST['delid'])){
			$id = intval($_REQUEST['delid']);
			$del = M('market_lottery')->where("id={$id}")->delete();
		}
		
        if($del){
			alogs("Market_lottery_Del",0,1,'成功执行了积分商城抽奖奖品的删除操作！');//管理员操作日志
            $this->assign('jumpUrl', __URL__."/goods");
        	$this->success("删除成功");
        }else{
			alogs("Market_lottery_Del",0,0,'执行积分商城抽奖奖品的删除操作失败！');//管理员操作日志
			$this->error("删除失败");
		}
    }
	////////////////////////积分抽奖结束////////////////////////
	//评论列表
	 public function comment()
    {
		$field= true;
		$map['type'] = 3;
		$this->_list(D('Comment'),$field,$map,'id','DESC');
        $this->display();
    }
	//评论修改页面输出
	 public function edit() {
        $model = D('Comment');
        $id = intval($_REQUEST['id']);

        $vo = $model->find($id);
        $this->assign('vo', $vo);
        $this->display();
    }
	//评论修改
	public function doEdit() {
	    $model = D('Comment');
		if ($model->create()){
			$model->deal_time = time();
		    if($result = $model->save()){
				alogs("MarketPinglun",0,1,'成功执行了积分商城商品评论的回复操作！');//管理员操作日志
		        $this->assign("jumpUrl","/admin/market/comment");
			    $this->success("修改成功");
		    } else {
				alogs("MarketPinglun",0,0,'执行积分商城商品评论的回复操作失败！');//管理员操作日志
		        $this->error("修改失败");
		    }
		}else {
		    $this->error($model->getError());
		}
		
	}
	//评论删除	
	public function doDel(){
        $model = D('comment');
        if (!empty($model)) {
			$id = $_REQUEST['idarr'];
            if (isset($id)) {
				$pk = $model->getPk();
				if (false !== $model->where("{$pk} in ({$id})")->delete()) {
					alogs("MarketPinglun",0,1,'成功执行了积分商城商品评论的删除操作！');//管理员操作日志
					$this->success(L('删除成功'),'',$id);
				} else {
					alogs("MarketPinglun",0,0,'执行积分商城商品评论的删除操作失败！');//管理员操作日志
					$this->error(L('删除失败'));
				}
            } else {
                $this->error('非法操作');
            }
        }
	}
}
?>