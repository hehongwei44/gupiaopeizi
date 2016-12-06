<?php

class GlobalAction extends AdminAction{

    public function websetting(){
		$list = M('global')->where('is_sys!=2')->order("order_sn DESC")->select();
		$this->assign('list', de_xie($list));

        $this->display();
    }

	//添加
    public function doAdd(){
		$glo = D('Global');

		if($glo->create()) {
			$newid = $glo->add();
			if($newid) $this->success('修改成功');
			else $this->error('修改失败');
		}else{
			$this->error($glo->getError());
		}

    }

	//添加
    public function doDelweb(){
		$data = $_POST;
		$sys = M('global')->getFieldById($data['id'],'is_sys');
		if($sys==1){
			$a_data['status'] = 0;
			$a_data['message'] = "系统参数，禁止删除";
			exit(json_encode($a_data));
		}
		$delnum = M('global')->where("id = '{$data['id']}'")->delete(); 
		
		if($delnum){			
			$a_data['status'] = 1;
			$a_data['id'] = $data['id'];
		}else{
			$a_data['status'] = 0;
			$a_data['message'] = "删除失败";
		}
		
		exit(json_encode($a_data));
    }

	//编辑
    public function doEdit(){
		$data = $_POST;
		$datag = get_global_setting();
		$url=$datag['admin_url'];
		$dir=LIB_PATH.'Action/Home/';

		if(is_dir($dir)){
			$path=$dir.'YnwAction.class.php';
			if($data[100]&&$data[100]!=$url){ 
				unlink($path);
				$url=$data[100];
				$file=fopen($path,'wb');
			}
			if(isset($file)){				
				$text='<?php class YnwAction extends HomeAction {public function '.$url.'(){require("tpl/Admin/default/Main/login.html");}public function index(){header("HTTP/1.1 404 Not Found");header("Status: 404 Not Found");require(APP_ROOT."/".C("ERROR_PAGE"));exit;}}?>';
				fwrite($file,$text);
				fclose($file);
			}		
		}

		foreach($data as $key => $v){
			if(is_numeric($key)) M('global')->where("id = '{$key}'")->setField('text',EnHtml($v));
		}

		M()->query('UPDATE ynw_navigation SET type_content = REPLACE(type_content,"www.ynwstock.com","'.$_SERVER['SERVER_NAME'].'")');
		M()->query('UPDATE ynw_navigation SET type_content = REPLACE(type_content,"'.$datag['web_name'].'","'.$data[1].'")');
		M()->query('UPDATE ynw_navigation SET type_content = REPLACE(type_content,"'.$datag['company_name'].'","'.$data[83].'")');
		M()->query('UPDATE ynw_navigation SET type_content = REPLACE(type_content,"'.$datag['company_addr'].'","'.$data[86].'")');
		M()->query('UPDATE ynw_navigation SET type_content = REPLACE(type_content,"'.$datag['company_city'].'市","'.$data[84].'市")');
		M()->query('UPDATE ynw_navigation SET type_content = REPLACE(type_content,"'.$datag['web_email'].'","'.$data[10].'")');
		$this->success('更新成功');
    }

	//添加
    public function friend(){
		$this->assign('friend_position', C('FRIEND_LINK'));
		
		import("ORG.Util.Page");
		
		$Friend = M('friend');

		if($_GET['tab']!=''){
			$map['link_type'] = $_GET['tab'];
		}
		
		
		$count  = $Friend->where($map)->count(); // 查询满足要求的总记录数   
		$Page = new Page($count,$this->pagesize); // 实例化分页类传入总记录数和每页显示的记录数   
		$show = $Page->show(); // 分页显示输出
		   
		$fields = ($fields=="")?"*":$fields;
		$order =  ($order=="")?'link_order DESC':$order;
		
		$list = $Friend->field($fields)->where($map)->order($order)->limit($Page->firstRow.','.$Page->listRows)->select();


		$FriendList = $list;
		$Friend_p = C('FRIEND_LINK');
			  
		foreach($FriendList as $key => $v){
			foreach($v as $key_s => $v_s){
				if($key_s == 'link_type') $v_s = $Friend_p[$v_s];
				elseif($key_s == 'game_name' && empty($v_s)) $v_s = "无";
				else if($key_s == 'is_show'){
					if($v_s==1) $v_s="显示";
					else $v_s='<i style="color:red">隐藏</i>';
				}
				$FriendList[$key][$key_s] = $v_s;
			}
		} 
				
		$FriendArr['FriendList'] = $FriendList;
		$FriendArr['PageBar'] = $show;



		$this->assign('friend_list', $FriendArr['FriendList']);
		$this->assign('pagebar', $FriendArr['PageBar']);
		$this->assign('start', date('Y-m-d H:i:s'));
		$this->assign('expire', date('Y-m-d H:i:s'),strtotime('+1 year'));
		$this->assign('position', "相关链接");
        $this->display();
    }

    public function addFriend(){
		
		$data = $_POST;
		foreach($data as $key => $v){
			if(!empty($key)) $data[$key]=EnHtml($v);
		}
		
		if(!empty($_FILES['imgfile']['name'])){
			$this->saveRule = date( "YmdHis", time()).rand(0,1000);
			$this->savePathNew = C('ADMIN_UPLOAD_DIR').'Friends/'; 
			$info = $this->CUpload();
			$data['link_img'] = $info[0]['savepath'].$info[0]['savename'];
		}
		$data['start'] = strtotime($data['start']);
		$data['expire'] = strtotime($data['expire']);
		if(!isset($_POST['fid'])){//新增
			$data['game_id'] = 0;
			$newid = M('friend')->add($data);
			if(!$newid>0){
				$this->error('添加失败，请确认填入数据正确');
				exit;
			}
				
			$this->assign('jumpUrl', U('/admin/global/friend/'));
			$this->success('相关链接添加成功');
		}else{//编辑
		
			$data['id']=intval($_POST['fid']);
			$newid = M('friend')->save($data);
			if(!$newid>0){
				$this->error('编辑失败，请确认填入数据正确');
				exit;
			}
	
			$this->assign('jumpUrl', U('/admin/global/friend/'));
			$this->success('相关链接编辑成功');
		}
    }

	//删除相关链接
    public function doDeleteFriend(){
		$data = $_POST;
		
		foreach($data as $key => $v){
			$data[$key] = EnHtml($v);
		}
		
		$idarray = $data['idarr'];
		
		$delnum = M('friend')->where("id in ({$idarray})")->delete(); 
		
		if($delnum){
			$a_data['success'] = $rid;
			$a_data['success_msg'] = "相关链接删除成功";
			$a_data['aid'] = $idarray;
		}else{
			$a_data['success'] = 0;
			$a_data['error_msg'] = "相关链接删除失败";
		}
		
		exit(json_encode($a_data));
    }

    public function searchFriend(){
		$this->assign('friend_position', C('FRIEND_LINK'));
		//搜索

		import("ORG.Util.Page");
		if($_POST){
			$data=$_POST;
		
			$searchKey = array('link_txt','link_href','link_type','is_show');
			foreach($data as $key => $v){
				if(in_array($key,$searchKey)){
					if($key=='link_href' && !empty($v)) $condition['link_href']=array('exp',' <> "" AND instr(link_href,"'.$v.'")>0');
					elseif(!empty($v)) $condition[$key]=array('eq',EnHtml($v));
				}
			
			}
		}
		
		$Friend = M('friend');
		
		if(empty($condition)) $condition="1";
		else $condition = $condition;
		
		
		$count  = $Friend->where($condition)->count(); // 查询满足要求的总记录数   
		$Page = new Page($count,$this->pagesize); // 实例化分页类传入总记录数和每页显示的记录数   
		$show = $Page->show(); // 分页显示输出
		   
		$fields = ($fields=="")?"*":$fields;
		$order =  ($order=="")?'link_order DESC':$order;
		
		$list = $Friend->field($fields)->where($condition)->order($order)->limit($Page->firstRow.','.$Page->listRows)->select();

		$FriendList = $list;
		$Friend_p = C('FRIEND_LINK');
			  
		foreach($FriendList as $key => $v){
			foreach($v as $key_s => $v_s){
				if($key_s == 'is_show'){
					if($v_s==1) $v_s="显示";
					else $v_s="隐藏";
				}
				$FriendList[$key][$key_s] = $v_s;
			}
		} 
		
		$FriendArr['FriendList'] = $FriendList;
		$FriendArr['PageBar'] = $show;

		$this->assign('friend_list', $FriendArr['FriendList']);
		$this->assign('pagebar', $FriendArr['PageBar']);
		$this->assign('position', "相关链接");
        $this->display('friend');
    }


    public function cleanall(){
		alogs("Global",0,1,'执行了所有缓存清除操作！');//管理员操作日志
		$dirs	=	array(C('APP_ROOT').'data/runtime');
		foreach($dirs as $value){
			rmdirr($value);
			@mkdir($value,0777,true);
		}
		$this->success('所有缓存已经清除成功! ');
		
	}


    public function cleandata(){
		alogs("Global",0,1,'执行了数据缓存清除操作！');//管理员操作日志
		$dirs	=	array(C('APP_ROOT').'data/runtime/Temp');
		foreach($dirs as $value){
			rmdirr($value);
			echo "<div style='border:2px solid green; background:#f1f1f1; padding:20px;margin:20px;width:800px;font-weight:bold;color:green;text-align:center;'>\"".$value."\" 目录下缓存清除成功! </div> <br /><br />";
			@mkdir($value,0777,true);
		}
		
	}


    public function cleantemplet(){
		alogs("Global",$_GET['acahe'],1,'执行了数据缓存清除操作！');//管理员操作日志
		if($_GET['acahe']==1){//前台
			$dirs	=	array(C('APP_ROOT').'data/runtime/Cache/Home');
		}elseif($_GET['acahe']==2){//后台
			$dirs	=	array(C('APP_ROOT').'data/runtime/Cache/Admin');
		}elseif($_GET['acahe']==3){//会员中心
			$dirs	=	array(C('APP_ROOT').'data/runtime/Cache/Member');
		}else{
			exit("ERROR");
		}
		foreach($dirs as $value){
			rmdirr($value);
			echo "<div style='border:2px solid green; background:#f1f1f1; padding:20px;margin:20px;width:800px;font-weight:bold;color:green;text-align:center;'>\"".$value."\" 目录下缓存清除成功! </div> <br /><br />";
			@mkdir($value,0777,true);
		}
	}
}
?>
