<?php

class ArticleAction extends AdminAction
{
    /**
    +----------------------------------------------------------
    * 默认操作
    +----------------------------------------------------------
    */
    public function index()
    {
		$tlist = M("article a")->field("ac.type_name,a.type_id,ac.parent_id")->join("{$this->pre}article_category ac ON ac.id=a.type_id ")->group("a.type_id")->limit(10)->select();
    	foreach ($tlist as $k => $v) {
    		$tArr[$v['type_id']] = $v['type_name'];
    	}

		if(isset($_REQUEST['type_id']) && $_REQUEST['type_id']) {
		   $type_id= intval($_GET['type_id']);
           $Allid = M("article_category")->field("id")->where("parent_id = {$type_id}")->select();
		   $newlist = array();
		   array_push($newlist,$_GET['type_id']);
		  
		   foreach ($Allid as $ka => $v) {
		       array_push($newlist,$v["id"]);
		   }
		   $map['type_id']= array("in",$newlist);
           $this->assign('type_id', $_REQUEST['type_id']);  
		}
        if(isset($_REQUEST['title']) && !empty($_REQUEST['title']))
        {
            $map['title'] = array('like', '%'.$_REQUEST['title'].'%');
            $this->assign('title', $_REQUEST['title']);
        }
    	$sort = "id";
    	if(in_array($_GET['type_id'], array(1,2,3,4,5))){
    		$sort = "sort_order";
    	}
 
		$field= 'id,title,type_id,art_writer,art_time,sort_order';
		$this->_list(D('Article'),$field,$map,$sort,'DESC',$map);
        
		$this->assign('tArr',$tArr);
        
        $this->_addFilter();
        $this->display();
    }
	
    public function _addFilter()
    {
		$typelist = get_type_leve_list('0','Category', 'article');//分级栏目
		$this->assign('type_list',$typelist);
    }

	public function _doAddFilter($m){
		if(!empty($_FILES['imgfile']['name'])){
			$this->saveRule = date("YmdHis",time()).rand(0,1000);
			$this->savePathNew = C('ADMIN_UPLOAD_DIR').'Article/' ;
			$this->thumbMaxWidth = C('ARTICLE_UPLOAD_W');
			$this->thumbMaxHeight = C('ARTICLE_UPLOAD_H');
			$info = $this->CUpload();
			$data['art_img'] = $info[0]['savepath'].$info[0]['savename'];
		}
		if($data['art_img']) $m->art_img=$data['art_img'];
		$m->art_time=time();
		$m->art_writer = session("admin_user_name");
		if($_POST['is_remote']==1) $m->art_content = get_remote_img($m->art_content);
		return $m;
	}

	public function _doEditFilter($m){
		if(!empty($_FILES['imgfile']['name'])){
			$this->saveRule = date("YmdHis",time()).rand(0,1000);
			$this->savePathNew = C('ADMIN_UPLOAD_DIR').'Article/' ;
			$this->thumbMaxWidth = C('ARTICLE_UPLOAD_W');
			$this->thumbMaxHeight = C('ARTICLE_UPLOAD_H');
			$info = $this->CUpload();
			$data['art_img'] = $info[0]['savepath'].$info[0]['savename'];
		}
		if($data['art_img']) $m->art_img=$data['art_img'];
		if($_POST['is_remote']==1) $m->art_content = get_remote_img($m->art_content);
		return $m;
	}

	public function _editFilter($id){
		$typelist = get_type_leve_list('0','Category', 'article');//分级栏目
		$this->assign('type_list',$typelist);
	}
	
	public function _listFilter($list){
	 	$listType = D('Category')->getField('id,type_name');
		$row=array();
		foreach($list as $key=>$v){
			$v['type_name'] = $listType[$v['type_id']];
			$row[$key]=$v;
		}
		return $row;
	}
	
}
?>