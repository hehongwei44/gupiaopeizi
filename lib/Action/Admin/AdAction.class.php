<?php
class AdAction extends AdminAction{
	public $type = array(
		'0'=>'单图',
		'1'=>'幻灯片',
		'2'=>'跑马灯',	
		'8'=>'数据源',
		'9'=>'自定义',
		'3'=>'浮动',
		'4'=>'闪现',
		'5'=>'Flash动画',
		'6'=>'短视频',
		
		
	);
	public $position = array(
			'top'=>'顶部通栏',
			'nav'=>'导航通栏',
			'middle'=>'中部通栏',
			'main-top'=>'主区块顶',
			'main-middle'=>'主区块中',
			'main-bottom'=>'主区块底',
			'left-top'=>'左侧边顶',
			'left-middle'=>'左侧边中',
			'left-bottom'=>'左侧边底',
			'right-top'=>'右侧边顶',
			'right-middle'=>'右侧边中',
			'right-bottom'=>'右侧边底',
			'copy'=>'版权通栏',
			'bottom'=>'底部通栏',
	);

	public function _MyInit(){
		$this->assign('type',$this->type);
		$this->assign('position',$this->position);
	}

	public function index(){
		$map['type'] = intval($_GET['tab']);
		$field = "id,type,title,content,position,status,start,url,expire,date";
		$this->_list( M("ad"), $field, $map, "id", "DESC" );
		$this->display();
	}

	public function edit(){
		$id = intval( $_GET['id'] );
		$vo =M( "ad" )->find( $id );
		$this->assign( "vo", $vo );
		$this->display();
	}

	public function add(){
		$vo['start'] = time();
		$vo['expire'] = strtotime('+1 year');
		$this->assign( "vo", $vo );
		$this->display();
	}

	public function _doAddFilter($m){
		$m->date = time( );
		$m->start = strtotime( $_POST['start'] );
		$m->expire = strtotime( $_POST['expire'] );
		return $m;
	}

	public function _doEditFilter($m){
		$m->start = strtotime( $_POST['start'] );
		$m->expire = strtotime( $_POST['expire'] );
		return $m;
	}

	public function swfUpload( ){
		if ( $_POST['picpath'] ){
			$imgpath = substr( $_POST['picpath'], 1 );
			if ( in_array( $imgpath, $_SESSION['imgfiles'] ) ){
				unlink( C( "WEB_ROOT" ).$imgpath );
				$thumb = get_thumb_pic( $imgpath );
				$res = unlink(C("WEB_ROOT").$thumb );
				if ( $res ){
					$this->success("删除成功", "", $_POST['oid'] );
				}else{
					$this->error( "删除失败", "", $_POST['oid'] );
				}
			}else{
				$this->error( "图片不存在", "", $_POST['oid'] );
			}
		}else{
			$this->savePathNew = C( "ADMIN_UPLOAD_DIR" )."Ad/";
			$this->thumbMaxWidth = "100";
			$this->thumbMaxHeight = "100";
			$this->saveRule = date( "YmdHis", time()).rand(0,1000);
			$info = $this->CUpload();
			$data['product_thumb'] = $info[0]['savepath'].$info[0]['savename'];
			if ( !isset( $_SESSION['count_file'] ) ){
				$_SESSION['count_file'] = 1;
			}else{
				++$_SESSION['count_file'];
			}
			$_SESSION['imgfiles'][$_SESSION['count_file']] = $data['product_thumb'];
			echo "{$_SESSION['count_file']}:".__ROOT__."/".$data['product_thumb'];
		}
	}

}

?>
