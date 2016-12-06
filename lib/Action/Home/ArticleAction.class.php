<?php
class ArticleAction extends HomeAction {
    public function index(){
		$this->display();
    }

    public function about(){
    	$nav = D('Navigation');
    	$uri = $_SERVER['REQUEST_URI'];    	
    	$this->data=$nav->field('id,layout,type_name `title`,type_content `content`,parent_id `parent`')->where('parent_id>0 AND is_hiden = 0 AND type_url="'.$uri.'"')->find();
    	$this->param['page_title'] = $this->data['title'];
    	$this->param['left_menu']=$nav->where('parent_id="'.$this->data['parent'].'" AND is_hiden = 0')->order('sort_order DESC')->select();

        if($this->data['layout']){
            $tpl = 'full';
        }
		$this->display($tpl);
    }

    public function help(){
    	$nav = D('Navigation');
    	$uri = $_SERVER['REQUEST_URI'];    	
    	$this->data=$nav->field('id,layout,type_name `title`,type_content `content`,parent_id `parent`')->where('is_hiden = 0 AND type_url="'.$uri.'"')->find();
    	$this->param['page_title'] = $this->data['title'];
    	$this->param['left_menu']=$nav->where('parent_id="'.$this->data['parent'].'" AND parent_id>0  AND is_hiden = 0')->order('sort_order DESC')->select();
        if($this->data['layout']){
            $tpl = 'full';
        }
        $this->display($tpl);
    }

    public function news(){
    	$nav = D('Navigation');
    	$this->about=$nav->where('id="8"')->find();
    	$this->param['page_title'] = $this->about['type_name'];
    	$this->param['left_menu']=$nav->where('parent_id="8" AND is_hiden = 0')->order('sort_order DESC')->select();
    	if($_GET['id']){
			$this->data = D('Article')->find($_GET['id']);
			$this->param['page_title'] = $this->data['title'];
			$this->display('show');
    	}else{
            $article = D('Article');
            import("ORG.Util.Page");
            $Page = new Page($article->count(),10);
            $this->param['pages'] = $Page->show();
			$this->data = $article->order('art_time DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
			$this->display();
    	}    	
		
    }

	
}