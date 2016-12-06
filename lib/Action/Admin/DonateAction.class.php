<?php

class DonateAction extends AdminAction
{
    /**
    +----------------------------------------------------------
    * 默认操作
    +----------------------------------------------------------
    */
    public function index()
    {
		$field= 'id,donate_name,need_money,title,resource';
		$this->_list(D('Donate'),$field,'','id','DESC');
        $this->display();
    }
	
    public function _addFilter()
    {
		$this->assign('type_list',C('DONATE_TYPE'));
		$this->assign('area_list',C('DONATE_AREA'));
    }

	public function _doAddFilter($m){
		if(!empty($_FILES['imgfile']['name'])){
			$this->saveRule = date("YmdHis",time()).rand(0,1000);
			$this->savePathNew = C('ADMIN_UPLOAD_DIR').'Article/' ;
			$this->thumbMaxWidth = C('ARTICLE_UPLOAD_W');
			$this->thumbMaxHeight = C('ARTICLE_UPLOAD_H');
			$info = $this->CUpload();
			$data['thubm'] = $info[0]['savepath'].$info[0]['savename'];
		}
		if($data['thubm']) $m->thumb=$data['thubm'];
		if($_POST['is_remote']==1) $m->info = get_remote_img($m->info);
		return $m;
	}

	public function _doEditFilter($m){
		if(!empty($_FILES['imgfile']['name'])){
			$this->saveRule = date("YmdHis",time()).rand(0,1000);
			$this->savePathNew = C('ADMIN_UPLOAD_DIR').'Article/' ;
			$this->thumbMaxWidth = C('ARTICLE_UPLOAD_W');
			$this->thumbMaxHeight = C('ARTICLE_UPLOAD_H');
			$info = $this->CUpload();
			$data['thubm'] = $info[0]['savepath'].$info[0]['savename'];
		}
		if($data['thubm']) $m->thumb=$data['thubm'];
		if($_POST['is_remote']==1) $m->info = get_remote_img($m->info);
		return $m;
	}

	public function _editFilter($id){
		$this->assign('type_list',C('DONATE_TYPE'));
		$this->assign('area_list',C('DONATE_AREA'));
	}
	
	public function _listFilter($list){
		return $list;
	}
	
}
?>