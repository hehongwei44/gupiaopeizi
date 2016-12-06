<?php
require COMMON_PATH."base.php";

function acl_get_key(){
	empty($model)?$model=strtolower(MODULE_NAME):$model=strtolower($model);
	empty($action)?$action=strtolower(ACTION_NAME):$action=strtolower($action);
	
	$keys = array($model,'data','eqaction_'.$action);
	require CONF_PATH."Admin/acl.php";
	$inc = $acl_inc;
	
	$array = array();
	foreach($inc as $key => $v){
			if(isset($v['low_leve'][$model])){
				$array = $v['low_leve'];
				continue;
			}
	}//找到acl.inc中对当前模块的定义的数组
	
	$num = count($keys);
	$num_last = $num - 1;
	$this_array_0 = &$array;
	$last_key = $keys[$num_last];
	
	for ($i = 0; $i < $num_last; $i++){
		$this_key = $keys[$i];
		$this_var_name = 'this_array_' . $i;
		$next_var_name = 'this_array_' . ($i + 1);        
		if (!array_key_exists($this_key, $$this_var_name)) {            
			break;       
		}        
		$$next_var_name = &${$this_var_name}[$this_key];    
	}    
	/*取得条件下的数组  ${$next_var_name}得到data数组 $last_key即$keys = array($model,'data','eqaction_'.$action);里面的'eqaction_'.$action,所以总的组成就是，在acl.inc数组里找到键为$model的数组里的键为data的数组里的键为'eqaction_'.$action的值;*/
	$actions = ${$next_var_name}[$last_key];//这个值即为当前action的别名,然后用别名与用户的权限比对,如果是带有参数的条件则$actions是数组，数组里有相关的参数限制
	if(is_array($actions)){
		foreach($actions as $key_s => $v_s){
			$ma = true;
			if(isset($v_s['POST'])){
				foreach($v_s['POST'] as $pkey => $pv){
					switch($pv){
						case 'G_EMPTY';//必须为空
							if( isset($_POST[$pkey]) && !empty($_POST[$pkey]) ) $ma = false;
						break;
					
						case 'G_NOTSET';//不能设置
							if( isset($_POST[$pkey]) ) $ma = false;
						break;
					
						case 'G_ISSET';//必须设置
							if( !isset($_POST[$pkey]) ) $ma = false;
						break;
					
						default;//默认
							if( !isset($_POST[$pkey]) || strtolower($_POST[$pkey]) != strtolower($pv) ) $ma = false;
						break;
					}
				}
			}
			
			if(isset($v_s['GET'])){
				foreach($v_s['GET'] as $pkey => $pv){
					switch($pv){
						case 'G_EMPTY';//必须为空
							if( isset($_GET[$pkey]) && !empty($_GET[$pkey]) ) $ma = false;
						break;
					
						case 'G_NOTSET';//不能设置
							if( isset($_GET[$pkey]) ) $ma = false;
						break;
					
						case 'G_ISSET';//必须设置
							if( !isset($_GET[$pkey]) ) $ma = false;
						break;
					
						default;//默认
							if( !isset($_GET[$pkey]) || strtolower($_GET[$pkey]) != strtolower($pv) ) $ma = false;
						break;
					}
					
				}
			}
			if($ma)	return $key_s;
			else $actions="0";
		}//foreach
	}else{
		return $actions;
	}
}
//获取用户权限数组
function get_user_acl($uid=""){
	$model=strtolower(MODULE_NAME);

	if(empty($uid)) return false;
	$gid = M('users')->field('u_group_id')->find($uid);
	
	$al = get_group_data($gid['u_group_id']);
	
	$acl = $al['controller'];
	$acl_key = acl_get_key();
	
	if( array_keys($acl[$model],$acl_key) ) return true;
	else return false;
}

//获取权限列表
function get_group_data($gid=0){
	$gid=intval($gid);
	$list=array();
	
	if($gid==0){
		if( !S("ACL_all") ){
			$_acl_data = M('users_acl')->select();
			$acl_data=array();
			
			foreach($_acl_data as $key => $v){
				$acl_data[$v['id']] = $v;
				$acl_data[$v['id']]['controller'] = unserialize($v['controller']);
			}
			
			S("ACL_all",$acl_data,C('ADMIN_CACHE_TIME')); 
			$list = $acl_data;
		}else{
			$list = S("ACL_all");
		}
	}else{
		if( !S("ACL_".$gid) ){
			$_acl_data = M('users_acl')->find($gid);
			$_acl_data['controller'] = unserialize($_acl_data['controller']);
			$acl_data = $_acl_data;
			S("ACL_".$gid,$acl_data,C('ADMIN_CACHE_TIME')); 
			$list = $acl_data;
		}else{
			$list = S("ACL_".$gid);
		}
	}
	return $list;
}

//获取借款列表
function getTMemberList($search=array(),$size=''){
	$pre = C('DB_PREFIX');
	$map['m.is_transfer'] = '1';
	$map = array_merge($map,$search);


	//分页处理
	import("ORG.Util.Page");
	$count = M('member m')->where($map)->count('m.id');
	$p = new Page($count, $size);
	$page = $p->show();
	$Lsql = "{$p->firstRow},{$p->listRows}";
	//分页处理

	$field = "m.id,m.user_name,mf.info";
	$list = M('member m')->field($field)->join("{$pre}member_info mf ON m.id=mf.uid")->where($map)->limit($Lsql)->select();
	foreach($list as $key=>$v){
		$total = M('transfer_borrow')->field("sum(borrow_money) as tb,sum(transfer_out*per_transfer) as total")->where("borrow_uid={$v['id']}")->find();
		$list[$key]['transfer_total'] = $total['tb'];
		$list[$key]['transfer_total_out'] = $total['total'];
	}
	
	$row=array();
	$row['list'] = $list;
	$row['page'] = $page;
	return $row;
}

//获取借款列表
function getMemberInfoList($search=array(),$size=''){
	$pre = C('DB_PREFIX');
	$map = array();
	$map = array_merge($map,$search);


	//分页处理
	import("ORG.Util.Page");
	$count = M('member m')->where($map)->count('m.id');
	$p = new Page($count, $size);
	$page = $p->show();
	$Lsql = "{$p->firstRow},{$p->listRows}";
	//分页处理

	$field = "m.id,m.id as uid,m.user_name,mbank.uid as mbank_id,mi.uid as mi_id,mci.uid as mci_id,mhi.uid as mhi_id,mdpi.uid as mdpi_id,mei.uid as mei_id,mfi.uid as mfi_id";
	$list = M('member m')->field($field)->join("{$pre}member_banks mbank ON m.id=mbank.uid")->join("{$pre}member_contact mci ON m.id=mci.uid")->join("{$pre}member_branch mdpi ON m.id=mdpi.uid")->join("{$pre}member_house mhi ON m.id=mhi.uid")->join("{$pre}member_ensure mei ON m.id=mei.uid")->join("{$pre}member_info mi ON m.id=mi.uid")->join("{$pre}member_financial mfi ON m.id=mfi.uid")->where($map)->limit($Lsql)->order('m.id DESC')->select();
	foreach($list as $key=>$v){
		$is_data = M('member_datum')->where("uid={$v['uid']}")->count("id");
		$list[$key]['mbank'] = ($v['mbank_id']>0)?"<span style='color:green'>已填写</span>":"<span style='color:#ccc'>未填写</span>";
		$list[$key]['mci'] = ($v['mci_id']>0)?"<span style='color:green'>已填写</span>":"<span style='color:#ccc'>未填写</span>";
		$list[$key]['mdi'] = ($is_data>0)?"<span style='color:green'>已填写(<a href='".U('/admin/verifyinfo/index')."?uid={$v['uid']}'>查看</a>)</span>":"<span style='color:#ccc'>未填写</span>";
		$list[$key]['mhi'] = ($v['mhi_id']>0)?"<span style='color:green'>已填写</span>":"<span style='color:#ccc'>未填写</span>";
		$list[$key]['mdpi'] = ($v['mdpi_id']>0)?"<span style='color:green'>已填写</span>":"<span style='color:#ccc'>未填写</span>";
		$list[$key]['mei'] = ($v['mei_id']>0)?"<span style='color:green'>已填写</span>":"<span style='color:#ccc'>未填写</span>";
		$list[$key]['mfi'] = ($v['mfi_id']>0)?"<span style='color:green'>已填写</span>":"<span style='color:#ccc'>未填写</span>";
		$list[$key]['mi'] = ($v['mi_id']>0)?"<span style='color:green'>已填写</span>":"<span style='color:#ccc'>未填写</span>";
	}
	
	$row=array();
	$row['list'] = $list;
	$row['page'] = $page;
	return $row;
}


//获取借款列表
function getMemberApplyList($search=array(),$size=''){
	$pre = C('DB_PREFIX');
	$map['ap.apply_status'] = '0';
	$map = array_merge($map,$search);


	//分页处理
	import("ORG.Util.Page");
	$count = M('member_apply ap')->where($map)->count('ap.id');
	$p = new Page($count, $size);
	$page = $p->show();
	$Lsql = "{$p->firstRow},{$p->listRows}";
	//分页处理

	$field = "ap.id,ap.apply_type,m.id as uid,m.user_name,mbank.uid as mbank_id,mhi.uid as mhi_id,mi.uid as mi_id,mci.uid as mci_id,mdpi.uid as mdpi_id,mei.uid as mei_id,mfi.uid as mfi_id,ap.add_time";
	$list = M('member_apply ap')->field($field)->join("{$pre}member m ON m.id=ap.uid")->join("{$pre}member_banks mbank ON m.id=mbank.uid")->join("{$pre}member_contact mci ON m.id=mci.uid")->join("{$pre}member_branch mdpi ON m.id=mdpi.uid")->join("{$pre}member_ensure mei ON m.id=mei.uid")->join("{$pre}member_house mhi ON m.id=mhi.uid")->join("{$pre}member_info mi ON m.id=mi.uid")->join("{$pre}member_financial mfi ON m.id=mfi.uid")->where($map)->limit($Lsql)->order('ap.id DESC')->select();
	foreach($list as $key=>$v){
		$is_data = M('member_datum')->where("uid={$v['uid']}")->count("id");
		$list[$key]['mbank'] = ($v['mbank_id']>0)?"<span style='color:green'>已填写</span>":"<span style='color:black'>未填写</span>";
		$list[$key]['mci'] = ($v['mci_id']>0)?"<span style='color:green'>已填写</span>":"<span style='color:black'>未填写</span>";
		$list[$key]['mdi'] = ($is_data>0)?"<span style='color:green'>已填写(<a href='".U('/admin/verifyinfo/index')."?uid={$v['uid']}'>查看</a>)</span>":"<span style='color:black'>未填写</span>";
		$list[$key]['mhi'] = ($v['mhi_id']>0)?"<span style='color:green'>已填写</span>":"<span style='color:black'>未填写</span>";
		$list[$key]['mdpi'] = ($v['mdpi_id']>0)?"<span style='color:green'>已填写</span>":"<span style='color:black'>未填写</span>";
		$list[$key]['mei'] = ($v['mei_id']>0)?"<span style='color:green'>已填写</span>":"<span style='color:black'>未填写</span>";
		$list[$key]['mfi'] = ($v['mfi_id']>0)?"<span style='color:green'>已填写</span>":"<span style='color:black'>未填写</span>";
		$list[$key]['mi'] = ($v['mi_id']>0)?"<span style='color:green'>已填写</span>":"<span style='color:black'>未填写</span>";
	}
	
	$row=array();
	$row['list'] = $list;
	$row['page'] = $page;
	return $row;
}


//获取借款列表
function getMemberInfoDetail($uid){
	$pre = C('DB_PREFIX');
	$map['m.id'] = $uid;
	$field = "*";
	$list = M('member m')->field($field)->join("{$pre}member_banks mbank ON m.id=mbank.uid")->join("{$pre}member_contact mci ON m.id=mci.uid")->join("{$pre}member_house mhi ON m.id=mhi.uid")->join("{$pre}member_branch mdpi ON m.id=mdpi.uid")->join("{$pre}member_ensure mei ON m.id=mei.uid")->join("{$pre}member_info mi ON m.id=mi.uid")->join("{$pre}member_financial mfi ON m.id=mfi.uid")->where($map)->limit($Lsql)->find();
	return $list;
}

//在线客服
function get_qq($type){
    $list = M('qq')->where("type = $type and is_show = 1")->order("qq_order DESC")->select();
	return $list;
}

if(get_magic_quotes_gpc()){
    function stripslashes_deep($value)
    {
        $value = is_array($value)? array_map('stripslashes_deep', $value): stripslashes($value); 
        return $value;
    }
    $_POST = array_map('stripslashes_deep', $_POST);
    $_GET = array_map('stripslashes_deep', $_GET);
    $_COOKIE = array_map('stripslashes_deep', $_COOKIE);
}




//获取某栏目下的所有子栏目以nid-nid顺次链接
function get_type_leve($id="0"){
	$model = D('Category');
	if(!S("type_son_type")){
		$allid=array();
		$data = $model->field('id,type_nid')->where("parent_id = {$id}")->select();
		if(count($data)>0){
			foreach($data as $v){
				//二级
				$allid[$v['type_nid']]=$v['id'];
				$data_1=array();//清空,避免下面判断错误
				$data_1 = $model->field('id,type_nid')->where("parent_id = {$v['id']}")->select();
				if(count($data_1)>0){
					foreach($data_1 as $v1){
						//三级
						$allid[$v['type_nid']."-".$v1['type_nid']]=$v1['id'];
						$data_2=array();//清空,避免下面判断错误
						$data_2 = $model->field('id,type_nid')->where("parent_id = {$v1['id']}")->select();
						if(count($data_2)>0){
							foreach($data_2 as $v2){
								//四级
								$allid[$v['type_nid']."-".$v1['type_nid']."-".$v2['type_nid']]=$v2['id'];
								$data_3=array();//清空,避免下面判断错误
								$data_3 = $model->field('id,type_nid')->where("parent_id = {$v2['id']}")->select();
	
								if(count($data_3)>0){
									foreach($data_3 as $v3){
										$allid[$v['type_nid']."-".$v1['type_nid']."-".$v2['type_nid']."-".$v3['type_nid']]=$v3['id'];
									}
								}
								//四级
							}
						}
						//三级
					}
				}
				//二级
			}
	
		}
		S("type_son_type",$allid,3600*C('HOME_CACHE_TIME')); 
	}else{
		$allid = S("type_son_type");
	}
	
	return $allid;
}

//获取某栏目下的所有子栏目以nid-nid顺次链接
function get_area_type_leve($id="0",$area_id=0){

	$model = D('Ccategory');
	if(!S("type_son_type_area".$area_id)){
		$allid=array();
		$data = $model->field('id,type_nid')->where("parent_id = {$id} AND area_id={$area_id}")->select();
		if(count($data)>0){
			foreach($data as $v){
				//二级
				$allid[$area_id.$v['type_nid']]=$v['id'];
				$data_1=array();//清空,避免下面判断错误
				$data_1 = $model->field('id,type_nid')->where("parent_id = {$v['id']}")->select();
				if(count($data_1)>0){
					foreach($data_1 as $v1){
						//三级
						$allid[$area_id.$v['type_nid']."-".$v1['type_nid']]=$v1['id'];
						$data_2=array();//清空,避免下面判断错误
						$data_2 = $model->field('id,type_nid')->where("parent_id = {$v1['id']}")->select();
						if(count($data_2)>0){
							foreach($data_2 as $v2){
								//四级
								$allid[$area_id.$v['type_nid']."-".$v1['type_nid']."-".$v2['type_nid']]=$v2['id'];
								$data_3=array();//清空,避免下面判断错误
								$data_3 = $model->field('id,type_nid')->where("parent_id = {$v2['id']}")->select();
	
								if(count($data_3)>0){
									foreach($data_3 as $v3){
										$allid[$area_id.$v['type_nid']."-".$v1['type_nid']."-".$v2['type_nid']."-".$v3['type_nid']]=$v3['id'];
									}
								}
								//四级
							}
						}
						//三级
					}
				}
				//二级
			}
	
		}
		S("type_son_type_area".$area_id,$allid,3600*C('HOME_CACHE_TIME')); 
	}else{
		$allid = S("type_son_type_area".$area_id);
	}
	return $allid;
}

//获取某栏目的所有父栏目的type_nid,按由远到近的顺序出现在数组中1/2
function get_type_leve_nid($id="0"){
	if(empty($id)) return;
	global $allid;
	static $r=array();//先声明要返回静态变量,不然在下面被赋值时是引用赋值
	get_type_leve_nid_run($id);
	
	$r = $allid;
	$GLOBALS['allid'] = NULL;
	
	return array_reverse($r);
}

//获取某栏目的所有父栏目的type_nid,按由远到近的顺序出现在数组中2/2
function get_type_leve_nid_run($id="0"){
	global $allid;
	$data_parent = $data = "";
	$data = D('Category')->field('parent_id,type_nid')->find($id);
	$data_parent = D('Category')->field('id,type_nid')->where("id = {$data['parent_id']}")->find();
	if(isset($data_parent['type_nid'])>0){
		if(!isset($allid[0])) $allid[]=$data['type_nid'];
		$allid[]=$data_parent['type_nid'];
		get_type_leve_nid_run($data_parent['id']);
	}else{
		if(!isset($allid[0])) $allid[]=$data['type_nid'];
	}
}

//获取某栏目的所有父栏目的type_nid,按由远到近的顺序出现在数组中1/2
function get_type_leve_area_nid($id="0",$area_id=0){
	if(empty($id)||empty($area_id)) return;
	global $allid_area;
	static $r=array();//先声明要返回静态变量,不然在下面被赋值时是引用赋值

	get_type_leve_area_nid_run($id);
	
	$r = $allid_area;
	$GLOBALS['allid_area'] = NULL;
	
	return array_reverse($r);
}

//获取某栏目的所有父栏目的type_nid,按由远到近的顺序出现在数组中2/2
function get_type_leve_area_nid_run($id="0"){
	global $allid_area;
	$data_parent = $data = "";
	$data = D('Aacategory')->field('parent_id,type_nid,area_id')->find($id);
	$data_parent = D('Aacategory')->field('id,type_nid,area_id')->where("id = {$data['parent_id']}")->find();
	if(isset($data_parent['type_nid'])>0){
		if(!isset($allid_area[0])) $allid_area[]=$data['type_nid'];
		$allid_area[]=$data_parent['type_nid'];
		get_type_leve_area_nid_run($data_parent['id']);
	}else{
		if(!isset($allid_area[0])) $allid_area[]=$data['type_nid'];
	}
}

//获取某栏目下的所有子栏目,查询次数较少，查询效率更高,入口函数1/2
function get_son_type($id){
	$tempname = "type_sfs_son_all".$id;
	if(!S($tempname)){
		$row = get_son_type_run($id);
		S($tempname,$row,3600*C('HOME_CACHE_TIME')); 
	}else{
		$row = S($tempname);
	}
	return $row;
}

//获取某栏目下的所有子栏目,查询次数较少，查询效率更高2/2
function get_son_type_run($id){
	static $rerow;
	global $allid;
	$data = M('type')->field('id')->where("parent_id in ({$id})")->select();
	if(count($data)>0){
		foreach($data as $key=>$v){
			$allid[]=$v['id'];
			$nowid[]=$v['id'];
		}
		$id = implode(",",$nowid);
		get_son_type_run($id);
	}
//递归函数不要加else来返回内容，不然得不到返回值
//	else{
//		return $allid;
//	}
	$rerow = $allid;
	$allid=array();
	return $rerow;
}

//获取某栏目下所有的子栏目,以数组的形式返回,入口函数1/2
function get_type_son($id=0){
	$tempname = "type_son_all".$id;
	if(!S($tempname)){
		$row = get_type_son_all($id);
		S($tempname,$row,3600*C('HOME_CACHE_TIME')); 
	}else{
		$row = S($tempname);
	}
	return $row;
}

//获取某栏目下所有的子栏目2/2
function get_type_son_all($id="0"){
	static $rerow;
	global $get_type_son_all_row;
	
	if(empty($id)) exit;
	
	$data = M('type')->where("parent_id = {$id}")->field('id')->select();
	foreach($data as $key=>$v){
		$get_type_son_all_row[]=$v['id'];
		$data_son = M('type')->where("parent_id = {$v['id']}")->field('id')->select();
		if(count($data_son)>0){
			get_type_son_all($v['id']);
		}
	}
	
	$rerow = $get_type_son_all_row;
	$get_type_son_all_row = array();
	return $rerow;
}

//获取所有栏目每个栏目的父栏目的nid,以栏目ID为键名
function get_type_parent_nid(){
	$row=array();
	$p_nid_new=array();
	if(!S("type_parent_nid_temp")){
		$data = M('type')->field('id')->select();
		if(count($data)>0){
			foreach($data as $key => $v){
				$p_nid = get_type_leve_nid($v['id']);
				$i=$n=count($p_nid);
				//倒序处理
				if($i>1){
					for($j=0;$j<$n;$j++,$i--){
						$p_nid_new[($i-1)]=$p_nid[$j];
					}
				}else{
					$p_nid_new = $p_nid;
				}
				//倒序处理
				$row[$v['id']] = $p_nid_new;
			}
		}
		S("type_parent_nid_temp",$row,3600*C('HOME_CACHE_TIME')); 
	}else{
		$row = S("type_parent_nid_temp");
	}
	
	return $row;
}

//获取以栏目ID为键的所有栏目数组,二维,如果field只有两个，并且其中一个是id，那么就会自动成为一维数组
function get_type_list($model,$field=true){
	$acaheName=md5("type_list_temp".$model.$field);
	if(!S($acaheName)){
		$list = D(ucfirst($model))->getField($field);
		S($acaheName,$list,3600*C('HOME_CACHE_TIME')); 
	}else{
		$list = S($acaheName);
	}
	return $list;
}

//通过网址获取栏目相关信息
function get_type_infos(){
	$row=array();
	$type_list = get_type_list('Category','id,type_nid,type_set');
	if(!isset($_GET['typeid'])){
		$type_nid = get_type_leve();//获得所有栏目自己的nid的组合 
		$rurl = explode("?",$_SERVER['REQUEST_URI']); 
		$xurl_tmp = explode("/",str_replace(array("index.html",".html"),array('',''),$rurl[0]));//获得组合的type_nid
		$zu = implode("-",array_filter($xurl_tmp));//组合
		//print_r($type_nid);
		$typeid = $type_nid[$zu];
		$typeset = $type_list[$typeid]['type_set'];
	}else{
		$typeid = intval($_GET['typeid']);
		$typeset = $type_list[$typeid]['type_set'];
	}

	if($typeset==1){//列表
		$templet = "list_index";
	}else{//单页
		$templet = "index_index";
	}
	
	$row['typeset'] = $typeset;
	$row['templet'] = $templet;
	$row['typeid'] = $typeid;
	
	return $row;
}

//通过网址获取栏目相关信息
function get_area_type_infos($area_id=0){
	$row=array();
	$type_list = get_type_list('aacategory','id,type_nid,type_set,area_id');
	if(!isset($_GET['typeid'])){

		$type_nid = get_area_type_leve(0,$area_id);//获得所有栏目自己的nid的组合 
		$rurl = explode("?",$_SERVER['REQUEST_URI']); 
		$xurl_tmp = explode("/",str_replace(array("index.html",".html"),array('',''),$rurl[0]));//获得组合的type_nid
		$zu = implode("-",array_filter($xurl_tmp));//组合
		//print_r($type_nid);
		$typeid = $type_nid[$area_id.$zu];
		$typeset = $type_list[$typeid]['type_set'];
	}else{
		$typeid = intval($_GET['typeid']);
		$typeset = $type_list[$typeid]['type_set'];
	}

	if($typeset==1){//列表
		$templet = "list_index";
	}else{//单页
		$templet = "index_index";
	}
	
	$row['typeset'] = $typeset;
	$row['templet'] = $templet;
	$row['typeid'] = $typeid;
	
	return $row;
}

//获取栏目列表,按栏目分级,有缩进,入口函数1/2
function get_type_leve_list($id=0,$modelname=false, $type){
	static $rerow;
	global $get_type_leve_list_run_row;


	if(!$modelname) $model = D("type");
	else $model=D(ucfirst($modelname));
	$stype = $modelname."home_type_leve_list".$id;
	if(!S($stype)){
		get_type_leve_list_run($id,$model, $type);
		$rerow = $get_type_leve_list_run_row;//把全局变量赋值给静态变量，避免引用清空
		$GLOBALS['get_type_leve_list_run_row']=NULL;//清空全局变量避免影响其他数据,不能用unset,unset只能清空单个变量或者数组中的某一元素,并且unset只能清空局部变量，清空全局变量要用unset($GLOBALS
		$data = $rerow;
		//S($stype,$data,3600*C('HOME_CACHE_TIME')); 
	}else{
		$data = S($stype);
	}
	return $data;
}

//获取栏目列表,按栏目分级,有缩进2/2
function get_type_leve_list_run($id=0,$model, $type){
	global $get_type_leve_list_run_row;
	//全局变量的定义都要放在最前面
	$spa = "----";
	if(count($get_type_leve_list_run_row)<1) $get_type_leve_list_run_row=array();

	$typelist = $model->where("parent_id={$id}")->field('type_name,id,parent_id')->order('sort_order DESC')->select();//上级栏目

	foreach($typelist as $k=>$v){
		$leve = intval(get_typeLeve($v['id'],$model));
		$v['type_name'] = str_repeat($spa,$leve).$v['type_name'];
		$get_type_leve_list_run_row[]=$v;
		
		$typelist_s1 = $model->where("parent_id={$v['id']} and model='{$type}'")->field('type_name,id')->select();//上级栏目
		if(count($typelist_s1)>0){
			get_type_leve_list_run($v['id'],$model, $type);
		}
	}
}//id


//获取栏目列表地区性的,按栏目分级,有缩进,入口函数1/2
function get_type_leve_list_area($id=0,$modelname=false,$area_id=0){
	static $rerow;
	global $get_type_leve_list_area_run_row;


	if(!$modelname) $model = D("type");
	else $model=D(ucfirst($modelname));
	$stype = $modelname."home_type_leve_list_area".$id.$area_id;
	if(!S($stype)){
		get_type_leve_list_area_run($id,$model,$area_id);
		$rerow = $get_type_leve_list_area_run_row;//把全局变量赋值给静态变量，避免引用清空
		$GLOBALS['get_type_leve_list_area_run_row']=NULL;//清空全局变量避免影响其他数据,不能用unset,unset只能清空单个变量或者数组中的某一元素,并且unset只能清空局部变量，清空全局变量要用unset($GLOBALS
		$data = $rerow;
		S($stype,$data,3600*C('HOME_CACHE_TIME')); 
	}else{
		$data = S($stype);
	}
	return $data;
}

//获取栏目列表,按栏目分级,有缩进2/2
function get_type_leve_list_area_run($id=0,$model,$area_id){
	global $get_type_leve_list_area_run_row;
	//全局变量的定义都要放在最前面
	$spa = "----";
	if(count($get_type_leve_list_area_run_row)<1) $get_type_leve_list_area_run_row=array();

	$typelist = $model->where("parent_id={$id} AND area_id={$area_id}")->field('type_name,id,parent_id')->order('sort_order DESC')->select();//上级栏目

	foreach($typelist as $k=>$v){
		$leve = intval(get_typeLeve($v['id'],$model));
		$v['type_name'] = str_repeat($spa,$leve).$v['type_name'];
		$get_type_leve_list_area_run_row[]=$v;
		
		$typelist_s1 = $model->where("parent_id={$v['id']}")->field('type_name,id')->select();//上级栏目
		if(count($typelist_s1)>0){
			get_type_leve_list_area_run($v['id'],$model,$area_id);
		}
	}
}//id


//获取栏目的级别1/2
function get_typeLeve($typeid,$model){
	$typeleve = 0;
	global $typeleve;
	static $rt=0;//先声明要返回静态变量,不然在下面被赋值时是引用赋值
	get_typeLeve_run($typeid,$model);
	$rt = $typeleve;
	unset($GLOBALS['typeleve']);
	return $rt;
}
//获取栏目的级别2/2
function get_typeLeve_run($typeid,$model){
	global $typeleve;
	$condition['id'] = $typeid;
	$v = $model->field('parent_id')->where($condition)->find();
	if($v['parent_id']>0){
		$typeleve++;
		get_typeLeve_run($v['parent_id'],$model);
	}
}



//获取下级或者同级栏目列表
function getTypeList($parm){
	$Osql="sort_order DESC";
	$field="id,type_name,type_set,add_time,type_url,type_nid,parent_id";
	//查询条件 
	$Lsql="{$parm['limit']}";
	$pc = D('navigation')->where("parent_id={$parm['type_id']} and model='navigation'")->count('id');
	if($pc>0){
		$map['is_hiden'] = 0;
		$map['parent_id'] = $parm['type_id'];
        $map['model']  = 'navigation';
		$data = D('navigation')->field($field)->where($map)->order($Osql)->limit($Lsql)->select();
	}elseif(!isset($parm['notself'])){
		$map['is_hiden'] = 0;
		$map['parent_id'] = D('Category')->getFieldById($parm['type_id'],'parent_id');
		$data = D('Category')->field($field)->where($map)->order($Osql)->limit($Lsql)->select();
	}

	//链接处理
	$typefix = get_type_leve_nid($parm['type_id']);
	$typeu = $typefix[0];
	$suffix=C("URL_HTML_SUFFIX");
	foreach($data as $key=>$v){
		if($v['type_set']==2){
			if(empty($v['type_url'])) $data[$key]['turl']="javascript:alert('请在后台添加此栏目链接');";
			else $data[$key]['turl'] = $v['type_url'];
		}
		elseif($parm['model']=='navigation'||($v['parent_id']==0)) $data[$key]['turl'] = MU("Home/{$v['type_nid']}/index","typelist",array("suffix"=>$suffix));
		elseif($parm['model']=='article'||($v['parent_id']==0)) $data[$key]['turl'] = MU("Home/{$v['type_nid']}/index","typelist",array("suffix"=>$suffix));
		else $data[$key]['turl'] = MU("Home/{$typeu}/{$v['type_nid']}","typelist",array("suffix"=>$suffix));
	}
	$row=array();
	$row = $data;

	return $row;
}

//获取下级或者同级栏目列表 文章栏目
function getTypeListActa($parm){
	//if(empty($parm['type_id'])) return;
	$Osql="sort_order DESC";
	$field="id,type_name,type_set,add_time,type_url,type_nid,parent_id";
	//查询条件 
	$Lsql="{$parm['limit']}";
	//$pc = D('Category')->where("parent_id={$parm['type_id']} and model='navigation'")->count('id');
	$pc = D('Category')->where("parent_id={$parm['type_id']} and model='article'")->count('id');
	if($pc>0){
		$map['is_hiden'] = 0;
		$map['parent_id'] = $parm['type_id'];
        $map['model']  = 'article';
		//$data = D('Acategory')->field($field)->where($map)->order($Osql)->limit($Lsql)->select();
		$data = D('Category')->field($field)->where($map)->order($Osql)->limit($Lsql)->select();
	}elseif(!isset($parm['notself'])){
		$map['is_hiden'] = 0;
		$map['parent_id'] = D('Category')->getFieldById($parm['type_id'],'parent_id');
		//$data = D('Acategory')->field($field)->where($map)->order($Osql)->limit($Lsql)->select();
		$data = D('Category')->field($field)->where($map)->order($Osql)->limit($Lsql)->select();
	}

	//链接处理
	$typefix = get_type_leve_nid($parm['type_id']);
	$typeu = $typefix[0];
	$suffix=C("URL_HTML_SUFFIX");
	foreach($data as $key=>$v){
		if($v['type_set']==2){
			if(empty($v['type_url'])) $data[$key]['turl']="javascript:alert('请在后台添加此栏目链接');";
			else $data[$key]['turl'] = $v['type_url'];
		}
		//elseif($parm['type_id']==0||($v['parent_id']==0&&count($typefix)==1)) $data[$key]['turl'] = MU("Home/{$v['type_nid']}/index","typelist",array("suffix"=>$suffix));
		elseif($parm['model']=='article'||($v['parent_id']==0)) $data[$key]['turl'] = MU("Home/{$v['type_nid']}/index","typelist",array("suffix"=>$suffix));
		else $data[$key]['turl'] = MU("Home/{$typeu}/{$v['type_nid']}","typelist",array("suffix"=>$suffix));
	}
	$row=array();
	$row = $data;
	
	return $row;
}
