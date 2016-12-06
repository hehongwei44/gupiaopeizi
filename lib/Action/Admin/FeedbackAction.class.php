<?php

class FeedbackAction extends AdminAction
{
    /**
    +----------------------------------------------------------
    * 默认操作
    +----------------------------------------------------------
    */
    public function index()
    {
		$field= true;
		$map=array();
		if($_REQUEST['type']) $map['type'] = intval($_REQUEST['type']);
		$this->_list(D('feedback'),$field,$map,'id','DESC');
		$this->assign("f_type",C('FEEDBACK_TYPE'));
        $this->display();
    }
    public function edit() {
        $model = D(ucfirst($this->getActionName()));
        $id = intval($_REQUEST['id']);

        $vo = $model->find($id);
		$f_type =C('FEEDBACK_TYPE');
		$vo['type'] = $f_type[$vo['type']];
		$model->where("id={$id}")->setField('status',1);
        $this->assign('vo', $vo);
        $this->display();
    }
	public function _listFilter($m){
		$f_type =C('FEEDBACK_TYPE');
		foreach($m as $key=>$v){
			$m[$key]['type'] = $f_type[$v['type']];
		}
		return $m;
	}

}
?>