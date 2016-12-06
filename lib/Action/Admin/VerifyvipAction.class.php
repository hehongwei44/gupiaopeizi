<?php
class VerifyVIPAction extends AdminAction{
    public function index()
    {
		$map=array();
		if($_REQUEST['uid'] && $_REQUEST['uname']){
			$map['v.uid'] = $_REQUEST['uid'];
			$search['uid'] = $map['v.uid'];	
			$search['uname'] = urldecode($_REQUEST['uname']);	
		}
		
		if($_REQUEST['uname'] && !$search['uid']){
			$map['m.user_name'] = array("like",urldecode($_REQUEST['uname'])."%");
			$search['uname'] = urldecode($_REQUEST['uname']);	
		}
		
		if($_REQUEST['realname']){
			$map['mi.real_name'] = urldecode($_REQUEST['realname']);
			$search['real_name'] = $map['mi.real_name'];	
		}
		
		if($_REQUEST['customer_id']){
			$map['v.kfid'] = $_REQUEST['customer_id'];
			$search['customer_id'] = $map['v.kfid'];	
			//$search['customer_name'] = urldecode($_REQUEST['customer_name']);	
		}
		
		if($_REQUEST['customer_name'] && !$search['customer_id']){
			$cusname = urldecode($_REQUEST['customer_name']);
			$kfid = M('users')->getFieldByUserName($cusname,'id');
			$map['v.kfid'] = $kfid;
			$search['customer_name'] = $cusname;	
			$search['customer_id'] = $kfid;	
		}
		
		if(isset($_REQUEST['status'])){
			$map['v.status'] = $_REQUEST['status'];
			$search['status'] = $map['v.status'];	
		}

		if(!empty($_REQUEST['start_time']) && !empty($_REQUEST['end_time'])){
			$timespan = strtotime(urldecode($_REQUEST['start_time'])).",".strtotime(urldecode($_REQUEST['end_time']));
			$map['v.add_time'] = array("between",$timespan);
			$search['start_time'] = urldecode($_REQUEST['start_time']);	
			$search['end_time'] = urldecode($_REQUEST['end_time']);	
		}elseif(!empty($_REQUEST['start_time'])){
			$xtime = strtotime(urldecode($_REQUEST['start_time']));
			$map['v.add_time'] = array("gt",$xtime);
			$search['start_time'] = $xtime;	
		}elseif(!empty($_REQUEST['end_time'])){
			$xtime = strtotime(urldecode($_REQUEST['end_time']));
			$map['v.add_time'] = array("lt",$xtime);
			$search['end_time'] = $xtime;	
		}
		//if(session('admin_is_kf')==1)	$map['v.kfid'] = session('admin_id');
		
		//分页处理
		import("ORG.Util.Page");
		$count = M('apply_vip v')->join("ynw_member m ON v.uid=m.id")->join("ynw_member_info mi ON mi.uid=m.id")->where($map)->count('m.id');
		$p = new Page($count, $this->pagesize);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理
		
		$field= 'v.*,m.user_name as uname,mi.real_name';
		$list = M('apply_vip v')->field($field)->join("{$this->pre}member m ON m.id=v.uid")->join("{$this->pre}member_info mi ON mi.uid=v.uid")->where($map)->limit($Lsql)->order('v.id DESC')->select();
		$list = $this->_listFilter($list);

        $this->assign("status", array('待审核','已通过审核','未通过审核'));
        $this->assign("list", $list);
        $this->assign("pagebar", $page);
        $this->assign("search", $search);
        $this->display();
    }
	public function edit(){
		setBackUrl();
		$id=intval($_GET['id']);
		$aUser = get_admin_name();
		$vo = M('apply_vip')->find($id);
		if($vo['status']!=0) $this->error("审核过的不能再次审核");
		$vo['kfuname'] = $aUser[$vo['kfid']];
		$vo['uname'] = M('member')->getFieldById($vo['uid'],'user_name');
		$this->assign("vo",$vo);
		$this->display();
	}

	public function doEdit(){
        $model = D(ucfirst($this->getActionName()));
		$info = $model->field('deal_time')->where('id='.intval($_POST['id']))->find();
        if($info['deal_time']){
            $this->error("此申请已处理过，请不要重复提交！");   
        }
        if (false === $model->create()) {
            $this->error($model->getError());
        }	
			$model->deal_time = time();
			$model->deal_user = session('admin_id');
        //保存当前数据对象
        if ($result = $model->save()) { //保存成功
			
			if($_POST['status']==1){
				$vx = M('apply_vip')->field("uid,kfid")->find(intval($_POST['id']));
				$uid = $vx['uid'];
				$datag = get_global_setting();
				$aUser = get_admin_name();
                
				$result = logMoney($uid,14,-$datag['fee_vip'],"升级VIP成功");
                $newx = setMemberStatus($uid, 'vip', $_POST['status'], 13, 'vip');  
                
				memberLimitLog($uid,11,$this->glo['limit_vip'],"VIP审核通过");
				sendMessage($uid,"您的VIP申请审核已通过","您的VIP申请审核已通过");
				$vo = M("member")->field("user_phone,user_name,recommend_id")->where("id = {$uid}")->find();
				
				SMStip("vip",$vo['user_phone'],array("#USERANEM#"),array($vo['user_name']));
				if($newx){
					$vmo = M('member')->field("user_leve,time_limit")->find($vx['uid']);
					$savex['customer_id'] = $vx['kfid'];
					$savex['customer_name'] = $aUser[$vx['kfid']];
					$savex['user_leve'] = 1;
					if($vmo['time_limit']>time()) $savex['time_limit'] = strtotime("+1 year",$vmo['time_limit']);
					else $savex['time_limit'] = strtotime("+1 year");
					M('member')->where("id={$uid}")->save($savex);
				}
				alogs("Vipapply",0,1,'VIP申请审核通过！');//管理员操作日志
			}else{
				sendMessage($uid,"您的VIP申请审核未通过","您的VIP申请审核未通过");
				alogs("Vipapply",0,0,'VIP申请审核未通过！');//管理员操作日志
			}
            //成功提示
            $this->assign('jumpUrl', __URL__."/".session('listaction'));
            $this->success(L('修改成功'));
        } else {
            //失败提示
            $this->error(L('修改失败'));
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
	
	public function getusername(){
		$uname = M("member")->getFieldById(intval($_POST['uid']),"user_name");
		if($uname) exit(json_encode(array("uname"=>"<span style='color:green'>".$uname."</span>")));
		else exit(json_encode(array("uname"=>"<span style='color:orange'>不存在此会员</span>")));
	}
	
}
?>