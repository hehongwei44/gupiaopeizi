<?php
class InviteAction extends MemberAction {
    public function index(){
		$this->display();
    }
    public function member(){
        $map['recommend_id'] = $_SESSION['MEMBER']['ID'];

        $this->param['rows'] = M('Member')->where($map)->count();
        import("ORG.Util.Page");
        $Page = new Page($this->param['rows'],10);
        $this->param['pages'] = $Page->show();

        $this->data = D('Member')->where($map)->order('id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->display();
    }    
}