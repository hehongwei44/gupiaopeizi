<?php

class VerifyVideoAction extends AdminAction
{
    /**
    +----------------------------------------------------------
    * 默认操作
    +----------------------------------------------------------
    */
    public function index()
    {
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
		if(isset($_REQUEST['status'])&&$_REQUEST['status']!=''){
			$map['v.apply_status'] = $_REQUEST['status'];
			$search['status'] = $map['v.apply_status'];	
		}
		//if(session('admin_is_kf')==1)	$map['m.customer_id'] = session('admin_id');
		
		//分页处理
		import("ORG.Util.Page");
		$count = M('apply_video v')->join("{$this->pre}member m ON m.id=v.uid")->join("{$this->pre}member_account mm ON mm.uid=m.id")->join("{$this->pre}member_info mi ON mi.uid=m.id")->where($map)->count('m.id');
		$p = new Page($count, $this->pagesize);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理
		
		$field= 'v.id,v.add_time,v.uid,v.apply_status,m.user_phone,m.reg_time,m.user_name,mi.real_name,mm.money_freeze,mm.money_collect,(mm.account_money+mm.back_money) account_money';
		$list = M('apply_video v')->field($field)->join("{$this->pre}member m ON m.id=v.uid")->join("{$this->pre}member_account mm ON mm.uid=m.id")->join("{$this->pre}member_info mi ON mi.uid=m.id")->where($map)->limit($Lsql)->order('v.id DESC')->select();
		
        $this->assign("status", array('待审核','已通过审核','未通过审核'));
        $this->assign("bj", array("gt"=>'大于',"eq"=>'等于',"lt"=>'小于'));
        $this->assign("lx", array("allmoney"=>'可用余额',"mm.money_freeze"=>'冻结金额',"mm.money_collect"=>'待收金额'));
        $this->assign("list", $list);
        $this->assign("pagebar", $page);
        $this->assign("search", $search);
        $this->assign("query", http_build_query($search));
		
        $this->display();
    }


	public function edit(){
		setBackUrl();
		$id=intval($_GET['id']);
		$vo = M('apply_video')->find($id);
		if($vo['apply_status']!=0) $this->error("审核过的不能再次审核");

		$this->assign("vo",$vo);
		$this->display();
	}

	public function doEdit(){
        $model = D('apply_video');
        if (false === $model->create()) {
            $this->error($model->getError());
        }		
		$model->deal_user = session('admin_id');
		$model->deal_time = time();
        //保存当前数据对象
        if ($result = $model->save()) { //保存成功
		
			$uid = M('apply_video')->getFieldById($_POST['id'],'uid');
			
			if($_POST['apply_status'] == 1){
                setMemberStatus($uid, 'video', $_POST['apply_status'], 7, '视频');
				sendMessage($uid,"您的视频认证审核已通过","您的视频认证审核已通过");
				alogs("VerifyVideo",0,1,'视频认证审核通过！');//管理员操作日志
			}else{
				sendMessage($uid,"您的视频认证审核未通过","您的视频认证审核未通过");
				alogs("VerifyVideo",0,0,'视频认证审核未通过！');//管理员操作日志
			}
			
            //成功提示
            $this->assign('jumpUrl', __URL__."/index".session('listaction'));
            $this->success(L('审核成功'));
        } else {
            //失败提示
            $this->error(L('审核失败'));
        }
	}

}
?>