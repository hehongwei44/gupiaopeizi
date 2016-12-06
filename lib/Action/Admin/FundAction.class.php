<?php
class FundAction extends AdminAction{

	public function index(){
		$map['b.is_show'] = 1;
		$map['b.borrow_status'] = 2;
		$map['b.is_jijin'] = 1;
		//分页处理
		import("ORG.Util.Page");
		$count = M('transfer_borrow b')->join("{$this->pre}member m ON m.id=b.borrow_uid")->where($map)->count('b.id');
		$p = new Page($count, $this->pagesize);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理
		$field= 'b.id,b.borrow_name,b.borrow_uid,b.borrow_duration,b.borrow_money,b.borrow_fee,b.borrow_interest_rate,b.repayment_type,b.transfer_out,b.transfer_total,b.add_time,m.user_name,b.level_can,b.borrow_max,progress,b.is_tuijian';
		$list = M('transfer_borrow b')->field($field)->join("{$this->pre}member m ON m.id=b.borrow_uid")->where($map)->limit($Lsql)->order("b.id DESC")->select();
		$list = $this->_listFilter($list);
		$this->assign("list", $list);
        $this->assign("pagebar", $page);
		$this->assign("xaction",ACTION_NAME);
        $this->display();
	}

	public function endtran(){
		$map['is_show'] = 0;
		$map['is_jijin'] = 1;
		$map['b.borrow_status'] = 7;
		$field ="id,borrow_name,borrow_uid,borrow_duration,borrow_money,borrow_interest_rate,repayment_type,transfer_out,transfer_total,add_time,is_tuijian";
		$this->_list(D("Tborrow"), $field, $map, "id", "DESC" );
		$this->display();
	}

	public function _addFilter(){
		//$btype = array( "3" => "流转担保贷");
		//$this->assign("borrow_type", $btype );
		
		$vo = M('member')->field("id,user_name")->where("is_transfer=1")->select();//查询出所有流转会员
		$userlist = array();
		if(is_array($vo)){
			foreach($vo as $key => $v){
				$userlist[$v['id']]=$v['user_name'];
			}
		}
		$this->assign("userlist",$userlist);//流转会员
		///////////////////////////////////////////////////////////////////////////////////
		$danbao = M('article')->field('id,title')->where('type_id=7')->select();
		$dblist = array();
		if(is_array($danbao)){
			foreach($danbao as $key => $v){
				$dblist[$v['id']]=$v['title'];
			}
		}
		$this->assign("danbao_list",$dblist);//新增担保
		//////////////////////////////////////////////////////////////////////////////
		$this->assign("borrow_duration_list",array("1"=>'1',"3"=>'3',"6"=>'6',"9"=>'9',"12"=>'12',"15"=>'15',"18"=>'18',"24"=>'24'));//基金期限
		$this->assign("min_month_list",array("3"=>'3',"6"=>'6',"9"=>'9',"12"=>'12',"15"=>'15',"18"=>'18'));//基金期限
		$this->assign("online_time",time()+3600*3);//
		$bid = M("transfer_borrow")->where("is_jijin=1")->count();
		if(isset($bid)){
			$Newid=$bid+1;
		}else{
			$Newid=1;
		}
		
		$this->assign("borrow_name","MYB-".str_repeat("0",8-strlen($Newid)).$Newid);
	}
    public function _doDelFilter($id){
		M('transfer_detail')->where("borrow_id={$id}")->delete();
	}
	public function doAdd( ){
		$model = M("transfer_borrow");
		$model2 = M("transfer_detail");
		if (false === $model->create()) {
			$this->error($model->getError());
		}
		if (false === $model2->create()) {
			$this->error($model->getError());
		}
		$model->startTrans();
		$model->total = $_POST['borrow_duration'];//共几期(分几次还)
		$model->min_month = $_POST['borrow_duration'];
		$model->is_jijin = 1;//是否是基金理财 0代表流转标 1代表基金理财
		$model->repayment_type = 0;
		$model->borrow_status = 2;
		$model->add_time = time();
		$model->deadline = time() + $_POST['borrow_duration'] * 30 * 24 * 3600;
		//$model->collect_day = strtotime($_POST['collect_day']);
		$model->online_time = strtotime($_POST['online_time']);//上线时间
		//$model->deadline = strtotime($model->deadline);
		$model->add_ip = get_client_ip();
		$model->level_can = intval($_POST['level_can']);
		$model->borrow_max = intval($_POST['borrow_max']);
		foreach($_POST['updata_name'] as $key=>$v){
			$updata[$key]['name'] = $v;
			$updata[$key]['time'] = $_POST['updata_time'][$key];
		}
		$model->updata = serialize($updata);
        
		 if(!empty($_FILES['picpath']['name'])){
			$this->saveRule = 'uniqid';
			//$this->saveRule = date("YmdHis",time()).rand(0,1000);
			$this->savePathNew = C('ADMIN_UPLOAD_DIR').'Hetong/';
			$this->thumbMaxWidth = C('HETONG_UPLOAD_H');
			$this->thumbMaxHeight = C('HETONG_UPLOAD_W');
			$info = $this->CUpload();
			$data['hetong_img'] = $info[0]['savepath'].$info[0]['savename'];
		}
		if($data['hetong_img']) {
			$model->hetong_img=$data['hetong_img'];//合同图章
			//$model->thumb_hetong_img=$data['thumb_hetong_img'];//合同图章缩略图
		}
		
		if(!empty($_FILES['imgfile']['name'])){
			$this->saveRule = date("YmdHis",time()).rand(0,1000);
			$this->savePathNew = C('ADMIN_UPLOAD_DIR').'Product/';
			$this->thumbMaxWidth = C('PRODUCT_UPLOAD_W');
			$this->thumbMaxHeight = C('PRODUCT_UPLOAD_H');
			$info = $this->CUpload();
			$data['b_img'] = $info[0]['savepath'].$info[0]['savename'];
		}
		if($data['b_img']) $model->b_img=$data['b_img'];//基金理财展示图
		$result = $model->add();
		//
		$suo=array();
		$suo['id']=$result; 
        $suo['suo']=0;
        $suoid = M("transfer_borrow_lock")->add($suo);
		foreach($_POST['swfimglist'] as $key=>$v){
			if($key>3) break;
			$row[$key]['img'] = substr($v,1);
			$row[$key]['info'] = $_POST['picinfo'][$key];
		}
		$model2->borrow_img=serialize($row);
		$model2->borrow_id = $result;
		$result2 = $model2->add();
		if ($result && $result2) { //保存成功
			$model->commit();
		  //新标提醒
			//newTip($result);
		  //自动投标
			//autoInvest($result);
			alogs("Tborrow",$result,1,'成功执行了基金理财信息的添加操作！');//管理员操作日志
		  //成功提示
			$this->assign('jumpUrl', __URL__);
			$this->success(L('新增成功'));
		}else{
			alogs("Tborrow",$result,0,'执行基金理财信息的添加操作失败！');//管理员操作日志
			$model->rollback();
			//失败提示
			$this->error(L('新增失败'));
		}
	}
	
	 public function edit() {
        $model = M('transfer_borrow');
        $model2 = M('transfer_detail');
        $id = intval($_REQUEST['id']);
        $vo = $model->find($id);
		///////////////////////////////////////////////////////////////////////////////////
		$danbao = M('article')->field('id,title')->where('type_id=7')->select();
		$dblist = array();
		if(is_array($danbao)){
			foreach($danbao as $key => $v){
				$dblist[$v['id']]=$v['title'];
			}
		}
		$this->assign("danbao_list",$dblist);//新增担保
		//////////////////////////////////////////////////////////////////////////////
		$this->assign("borrow_duration_list",array("1"=>'1',"3"=>'3',"6"=>'6',"9"=>'9',"12"=>'12',"15"=>'15',"18"=>'18',"24"=>'24'));//基金期限	
		$this->assign("min_month_list",array("3"=>'3',"6"=>'6',"9"=>'9',"12"=>'12',"15"=>'15',"18"=>'18'));//基金期限
		$vo['borrow_user'] =  M('member')->field('user_name')->find($vo['borrow_uid']);
		//if($vo['collect_time']>time()) $vo['is_show'] = 1;
		//else $vo['is_show'] = 0;
        $vo2 = $model2->find($id);
		foreach($vo2 as $key=>$v){
			if($key=="borrow_img") $vo[$key] = unserialize($v);
			else $vo[$key] = $v;
		}
        $this->assign('vo', $vo);
        $this->display();
    }
	
	//添加数据
    public function doEdit() {
        $model = M("transfer_borrow");
        $model2 = M("transfer_detail");
		
        if (false === $model->create()) {
            $this->error($model->getError());
        }
        if (false === $model2->create()) {
            $this->error($model->getError());
        }
		$model->startTrans();
        //保存当前数据对象
		$model->repayment_type = 5;
		if(intval($_POST['progress'])==100){
			$model->borrow_status = 6;
		}else{
			$model->borrow_status = 2;
		}
		//$model->collect_day = strtotime($_POST['collect_day']);
		$model->level_can = intval($_POST['level_can']);
		$model->borrow_max = intval($_POST['borrow_max']);
		//$model->transfer_total = intval($_POST['transfer_total']);
		//$model->min_month = $_POST['borrow_duration'];
		//$model->online_time = strtotime($_POST['online_time']);
		$model->online_time = strtotime($_POST['online_time']);//上线时间
		foreach($_POST['updata_name'] as $key=>$v){
			$updata[$key]['name'] = $v;
			$updata[$key]['time'] = $_POST['updata_time'][$key];
		}
		$model->updata = serialize($updata);
        
		 if(!empty($_FILES['picpath']['name'])){
			$this->saveRule = date("YmdHis",time()).rand(0,1000);
			$this->savePathNew = C('ADMIN_UPLOAD_DIR').'Hetong/';
			$this->thumbMaxWidth = C('PRODUCT_UPLOAD_W');
			$this->thumbMaxHeight = C('PRODUCT_UPLOAD_H');
			$info = $this->CUpload();
			$data['hetong_img'] = $info[0]['savepath'].$info[0]['savename'];
		}
		if($data['hetong_img']) $model->hetong_img=$data['hetong_img'];//修改公章
		
		if(!empty($_FILES['imgfile']['name'])){
			$this->saveRule = date("YmdHis",time()).rand(0,1000);
			$this->savePathNew = C('ADMIN_UPLOAD_DIR').'Product/';
			$this->thumbMaxWidth = C('PRODUCT_UPLOAD_W');
			$this->thumbMaxHeight = C('PRODUCT_UPLOAD_H');
			$info = $this->CUpload();
			$data['b_img'] = $info[0]['savepath'].$info[0]['savename'];
		}
		if($data['b_img']) $model->b_img=$data['b_img'];//修改基金理财展示图
		$result = $model->save();
		foreach($_POST['swfimglist'] as $key=>$v){
			$row[$key]['img'] = substr($v,1);
			$row[$key]['info'] = $_POST['picinfo'][$key];
		}
		$model2->borrow_img=serialize($row);
		$model2->borrow_id = intval($_POST['id']);

		$result2 = $model2->save();
		//$this->assign("waitSecond",1000);
        if ($result || $result2) { //保存成功
			$model->commit();
			alogs("Tborrow",0,1,'成功执行了基金理财信息的修改操作！');//管理员操作日志
          //成功提示
            $this->assign('jumpUrl', __URL__);
            $this->success(L('修改成功'));
        } else {
			alogs("Tborrow",0,0,'执行基金理财信息的修改操作失败！');//管理员操作日志
			$model->rollback();
            //失败提示
            $this->error(L('修改失败'));
        }
    }
				
	protected function _AfterDoEdit(){
		switch(strtolower(session('listaction'))){
			case "waitverify":
				$v = M('transfer_borrow')->field('borrow_uid,borrow_status,deal_time')->find(intval($_POST['id']));
				if(!empty( $v['deal_time'])){
					break;
				}
				if(empty($v['deal_time'])){
					$newid = M('member')->where("id={$v['borrow_uid']}")->setInc('credit_use',floatval($_POST['borrow_money']));
					if($newid) M('transfer_borrow')->where("id={$v['borrow_uid']}")->setField('deal_time',time());
				}
			break;
		}
	}

	public function _listFilter($list){
	 	$listType = C('REPAYMENT_TYPE');
		$row=array();
		foreach($list as $key=>$v){
			$v['repayment_type'] = $listType[$v['repayment_type']];
			
			$v['borrow_user'] =  M('member')->field('user_name')->find($v['borrow_uid']);
			$v['invest_num'] = M('transfer_borrow_investor')->where("borrow_id={$v['id']}")->count(id);//已投资纪录数量
			$row[$key]=$v;
		}
		return $row;
	}
			
	public function getusername(){
		$uname = M("member")->field("is_transfer,user_name")->find(intval($_POST['uid']));
		if($uname['user_name'] && $uname['is_transfer']==1) exit(json_encode(array("uname"=>"<span style='color:green'>".$uname['user_name']."</span>")));
		elseif($uname['user_name'] && $uname['is_transfer']==0) exit(json_encode(array("uname"=>"<span style='color:black'>此会员不是流转会员</span>")));
		elseif(!is_array($uname)) exit(json_encode(array("uname"=>"<span style='color:orange'>不存在此会员</span>")));
	}
			
		//swf上传图片
	public function swfUpload(){
		if($_POST['picpath']){
			$imgpath = substr($_POST['picpath'],1);
			if(in_array($imgpath,$_SESSION['imgfiles'])){
					 unlink(C("WEB_ROOT").$imgpath);
					 $thumb = get_thumb_pic($imgpath);
				$res = unlink(C("WEB_ROOT").$thumb);
				if($res) $this->success("删除成功","",$_POST['oid']);
				else $this->error("删除失败","",$_POST['oid']);
			}else{
				$this->error("图片不存在","",$_POST['oid']);
			}
		}else{
			$this->savePathNew = C('ADMIN_UPLOAD_DIR').'Product/' ;
			$this->thumbMaxWidth = C('PRODUCT_UPLOAD_W');
			$this->thumbMaxHeight = C('PRODUCT_UPLOAD_H');
			$this->saveRule = date("YmdHis",time()).rand(0,1000);
			$info = $this->CUpload();
			$data['product_thumb'] = $info[0]['savepath'].$info[0]['savename'];
			if(!isset($_SESSION['count_file'])) $_SESSION['count_file']=1;
			else $_SESSION['count_file']++;
			$_SESSION['imgfiles'][$_SESSION['count_file']] = $data['product_thumb'];
			echo "{$_SESSION['count_file']}:".__ROOT__."/".$data['product_thumb'];//返回给前台显示缩略图
		}
	}
	
	
	//每个借款标的投资人记录
	 public function doinvest(){
		$borrow_id = intval($_REQUEST['borrow_id']);
		$map=array();
		$map['bi.borrow_id'] = $borrow_id;
		$map['bi.is_jijin'] = 1;
		//分页处理
		import("ORG.Util.Page");
		$count = M('transfer_borrow_investor bi')->join("{$this->pre}member m ON m.id=bi.investor_uid")->where($map)->count('bi.id');
		$p = new Page($count, $this->pagesize);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理

		$field= 'bi.id bid,b.id,bi.investor_capital,bi.investor_interest,bi.invest_fee,bi.add_time,m.user_name,m.id mid,m.user_phone,b.borrow_duration,b.repayment_type,m.customer_name,b.borrow_name,bi.transfer_month';
		$list = M('transfer_borrow_investor bi')->field($field)->join("{$this->pre}member m ON m.id=bi.investor_uid")->join("{$this->pre}transfer_borrow b ON b.id=bi.borrow_id")->where($map)->limit($Lsql)->order("bi.id DESC")->select();
		$list = $this->_listFilter($list);
		
		//dump($list);exit;
        $this->assign("list", $list);
        $this->assign("pagebar", $page);
        $this->display();
    }
	public function repayment(){
		$map['b.is_show'] = 0 ;
		$map['b.borrow_status'] = 2;
		$map['b.is_jijin'] = 1;
		//分页处理
		import("ORG.Util.Page");
		$count = M('transfer_borrow b')->join("{$this->pre}member m ON m.id=b.borrow_uid")->where($map)->count('b.id');
		$p = new Page($count, $this->pagesize);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理
		$field= 'b.id,b.borrow_name,b.borrow_uid,b.borrow_duration,b.borrow_money,b.borrow_fee,b.borrow_interest_rate,b.repayment_type,b.transfer_out,b.transfer_total,b.add_time,m.user_name,b.level_can,b.borrow_max,progress,b.is_tuijian';
		$list = M('transfer_borrow b')->field($field)->join("{$this->pre}member m ON m.id=b.borrow_uid")->where($map)->limit($Lsql)->order("b.id DESC")->select();
		$list = $this->_listFilter($list);
		$this->assign("list", $list);
        $this->assign("pagebar", $page);
		$this->assign("xaction",ACTION_NAME);
        $this->display();
	}
}

?>
