<?php
class CommentAction extends AdminAction{
   public function index(){
		$field= true;
        if($_GET['tab']!=''){
            $map['status'] = $_GET['tab'];
        }
		$map['type'] = 1;
		$this->_list(D('Comment'),$field,$map,'id','DESC');
        $this->display();
    }

    public function index2(){
		$field= true;
		$map['type'] = 2;
		$this->_list(D('Comment'),$field,$map,'id','DESC');
        $this->display('index');
    }

	public function _doEditFilter($m){
		$m->deal_time = time();
		return $m;
	}

}
?>