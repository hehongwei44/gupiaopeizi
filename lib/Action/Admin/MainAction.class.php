<?php
class MainAction extends AdminAction {

	var $justlogin = true;
	public $check = array(
			'jkcs' => array('name'=>'借款初审','table'=>'borrow','where'=>'homs_id=0 AND borrow_status=0','url'=>'/admin/borrow/waitverify.html'),
			'jkfs' => array('name'=>'借款复审','table'=>'borrow','where'=>'homs_id=0 AND borrow_status=4','url'=>'/admin/borrow/waitrecheck.html'),
			'lbjk' => array('name'=>'流标借款','table'=>'borrow','where'=>'homs_id=0 AND borrow_status=3','url'=>'/admin/borrow/unfinish.html'),
			'hytx' => array('name'=>'会员提现','table'=>'member_withdraw','where'=>'withdraw_status=0','url'=>'/admin/withdraw/index.html'),
			'0' => '<br/>',
			'aypz' => array('name'=>'按月配资','table'=>'borrow','where'=>'homs_id>0 AND borror_type=9 AND borror_status=0','url'=>'/admin/trade/index/status/0/type/9.html'),
			'atpz' => array('name'=>'按天配资','table'=>'borrow','where'=>'homs_id>0 AND borror_type=8 AND borror_status=0','url'=>'/admin/trade/index/status/0/type/8.html'),
			'pzxy' => array('name'=>'配资续约','table'=>'borrow_apply','where'=>'`type`=0 AND `status` = 0','url'=>'/admin/risk/renew/status/0.html'),
			'zzpz' => array('name'=>'终止配资审核','table'=>'borrow_apply','where'=>'`type`=9 AND `status` = 0','url'=>'/admin/risk/stop/status/0.html'),
			'jbzj' => array('name'=>'追加保证金','table'=>'borrow_apply','where'=>'`type`=1 AND `status` = 0','url'=>'/admin/risk/deposit/status/0.html'),
			'tqlr' => array('name'=>'提取利润','table'=>'borrow_apply','where'=>'`type`=2 AND `status` = 0','url'=>'/admin/risk/profit/status/0.html'),
			'1' => '<br/>',
			'dljm' => array('name'=>'新代理加盟','table'=>'agent','where'=>'`status`=0','url'=>'/admin/agent/index.html'),
			'scdd' => array('name'=>'积分商城新订单','table'=>'market_order','where'=>'`status`=0','url'=>'/admin/market/order/status/0.html'),
			'gbsq' => array('name'=>'VIP认证申请','table'=>'apply_vip','where'=>'`status`=0','url'=>'/admin/verifyvip/index.html'),
			'smrz' => array('name'=>'实名认证申请','table'=>'apply_name','where'=>'`status`=0','url'=>'/admin/verifyid/index.html'),
			'zlsh' => array('name'=>'上传资料审核','table'=>'member_datum','where'=>'`status`=0','url'=>'/admin/verifyinfo/index.html'),
			'edsq' => array('name'=>'信用额度申请','table'=>'member_apply','where'=>'`apply_type`=1 AND `apply_status`=0','url'=>'/admin/member/infowait.html'),
			'jbts' => array('name'=>'举报投诉','table'=>'jubao','where'=>'`ststus`=0','url'=>'/admin/jubao/index.html'),
	);

    public function index(){
		require(CONF_PATH."Admin/acl.php");
		require(CONF_PATH."Admin/menu.php");
		$desktop = D('users_desktop');
		$iconcount = intval(cookie('iconcount'))>0 ? intval(cookie('iconcount')) : 7;
		$map['uid'] = $_SESSION['admin_id'];
		if(cookie('menutype')=='auto'){
			$map['add'] = 0;
		}else{
			$map['add'] = 1;
		}
		$menus = $desktop->where($map)->order('times DESC')->limit($iconcount)->select();
		foreach($menus as $menu){
			$menu_left[0]['0-0'][] = array($menu['name'],$menu['url'],1);
		}

		//检查有没有新内容
		if($_REQUEST['check']){
			if(cookie('nContent')==''){
				$check = array_keys($this->check);
			}else{
				$check = explode(',',cookie('nContent'));
			}			
			foreach($check as $key => $val){
				$item = $this->check[$val];
				$sum = D($item['table'])->where($item['where'])->count('id');
				if($sum){
					$data[] = array('name'=>$item['name'],'total'=>intval($sum),'id'=>$val,'url'=>$item['url']);
				}
			}
			$this->ajaxReturn($data);
		}


		if($_POST['menu']){			
			$data['uid'] = $_SESSION['admin_id'];
			$data['name'] = trim($_POST['menu']);
			$data['url'] = $_POST['url'];
			if($_POST['add']=='1'){
				$data['add'] = 1;
			}
			if($my = $desktop->where($data)->find()){
				$desktop->where($data)->setInc('times',1);
			}else{
				if($data['name']!='欢迎页'){
					
					$desktop->add($data);
				}				
			}	
			echo '该页面已经添加到了我的工作台！';		
		}else{
			$this->assign('menu_switch',cookie('menuswitch')==''?'one':cookie('menuswitch'));
			$this->assign('menu_style',cookie('menustyle')==''?'one':cookie('menustyle'));
	       	$this->assign('menu_left',$menu_left);
			$this->display();
		}
		
    }
	
	
	 public function check(){
		$code=$_GET["code"];
		$datag = get_global_setting();
		$codecheck=$datag['admin_url'];
			
	    if($code!=$codecheck){
			$this->assign('jumpUrl', '/');
            $this->error("非法请求");
	    }else{
			$this->redirect('login');
		}

	 }

	
	public function verify(){
		import("ORG.Util.Image");
		Image::buildImageVerify();
	}

    public function login(){
		require CONF_PATH."Admin/menu.php";
		if( session("admin") > 0){
			$this->redirect('index');
			exit;
		}
		if($_POST){
			if(md5($_SESSION['verify']) != md5($_POST['code'])){
				$this->error("验证码错误!");
			}
			$data['user_name'] = text($_POST['admin_name']);
			$data['user_pass'] = md5(strtolower($_POST['admin_pass']));
			$data['is_ban'] = array('neq','1');
			$data['user_word'] = text($_POST['user_word']);
			$admin = M('users')->field('id,user_name,u_group_id,real_name,is_kf,times,area_id,user_word,last_log_time,last_log_ip')->where($data)->find();
			
			if(is_array($admin) && count($admin)>0 ){
				foreach($admin as $key=>$v){
					session("admin_{$key}",$v);
				}
				if(session("admin_area_id")==0) session("admin_area_id","-1");
				session('admin',$admin['id']);
				session('adminname',$admin['user_name']);
				$info['last_log_time'] = time();
				$info['times'] = $admin['times']+1;
                $info['last_log_ip'] = get_client_ip();
				M("users")->where('id='.$admin['id'])->save($info);
				 
				alogs("login",'','1',"管理员登陆成功");//管理员操作日志之登陆日志
				$this->assign('jumpUrl','/admin/');
				$this->success('登陆成功，现在转向管理主页');
			}else{
				alogs("login",'','0',"管理员登陆失败",$admin['real_name']);
				$this->error('用户名或密码或口令错误，登陆失败');
			}
		}else{
			$this->error("非法请求");
		}
		
    }

    public function profile(){
		$user = D('users');
		if($_POST){
			$_POST['id'] = $_SESSION['admin_id'];
			if($user->save($_POST)){
				$this->success('个人资料修改成功');
			}else{
				$this->error('修改失败，没有任何修改或请稍后重试');
			}
		}else{
			$data=$user->find($_SESSION['admin_id']);
			$this->assign('data', $data);
			$this->display();
		}
    }

    public function password(){
		$user = D('users');
		if($_POST){

			$data=$user->find($_SESSION['admin_id']);
			
			if(md5($_POST['password'])!=$data['user_pass']){
				$this->error('输入的旧密码不正确，请修正后再次尝试');
			}else{
				if(strlen($_POST['user_pass'])<5){
					$this->error('修改失败，新密码不能少于6个字符');
				}else{
					if($_POST['user_pass']!=$_POST['confirm']){
						$this->error('修改失败，两次输入的密码不一致');
					}
				}				
			}

			$_POST['id'] = $_SESSION['admin_id'];
			$_POST['user_pass'] = md5($_POST['user_pass']);
			if($user->save($_POST)){
				$this->success('密码修改成功，下次登录即可生效');
			}else{
				$this->error('修改失败，请稍后重试');
			}
		}else{
			$this->display();
		}
    }

    public function setting(){
		$user = D('users');

		if($_POST){			
			if($_POST['iconcount']>20){
				$_POST['iconcount'] = 20;
			}
			if($_POST['pagesize']>50){
				$_POST['pagesize'] = 50;
			}
			if($_POST['menutype']!=cookie('menutype')){
				D('users_desktop')->where('uid="'.$_SESSION['admin_id'].'"')->delete();
			}
			cookie('menutype',$_POST['menutype'],1036800);
			cookie('menustyle',$_POST['menustyle'],1036800);
			cookie('menuswitch',$_POST['menuswitch'],1036800);
			cookie('iconcount',$_POST['iconcount'],1036800);
			cookie('pagesize',$_POST['pagesize'],1036800);
			cookie('pagewait',$_POST['pagewait'],1036800);
			cookie('nContent',implode(',',$_POST['nContent']),1036800);
			cookie('nPeriod',$_POST['nPeriod'],1036800);
			cookie('nKeep',$_POST['nKeep'],1036800);
			$this->success('偏好已保存成功，请刷新页面设置才会生效');
		}else{	
			$data['menutype'] = cookie('menutype');		
			$data['menustyle'] = cookie('menustyle');
			$data['menuswitch'] = cookie('menuswitch');
			$data['iconcount'] = cookie('iconcount');
			$data['pagesize'] = cookie('pagesize');
			$data['pagewait'] = cookie('pagewait');
			$data['nContent'] = cookie('nContent');
			$data['nPeriod'] = cookie('nPeriod');
			$data['nKeep'] = cookie('nKeep');
			$data['check'] = $this->check;
			$this->assign('data', $data);
			$this->display();
		}
    }

    public function logout(){
		alogs("logout",'','1',"管理员退出");
		session(null);
		$this->assign('jumpUrl', '/');
		$this->success('注销成功，现在转向首页');
    }
	
}