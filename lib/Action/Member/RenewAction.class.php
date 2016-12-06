<?php
class RenewAction extends MemberAction {
    public function init(){
    }

    public function index(){
        $map['type'] = 0;
        $map['uid'] = intval($_SESSION['MEMBER']['ID']);
        if($_GET['tab']==''){
            $map['status'] = 0;
        }else{
            $map['status'] = $_GET['tab'];
        }
        import("ORG.Util.Page");
        $Page = new Page(M('BorrowApply')->where($map)->count(),10);
        $this->param['pages'] = $Page->show();

        $this->data=D('BorrowApply')->where($map)->order('id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->display();
    }

    public function add(){
        $this->data['borrow'] = $_POST['data'];
        $this->display();
    }

    public function save(){
        if(is_numeric($_POST['total'])>0){
            $apply = D('BorrowApply')->where('`type`=0 AND `bid`='.$_POST['borrow'].' AND `status`=0')->find();
            if($apply){
                $this->ajaxReturn('','该配资方案已存在未审核的续约申请！',2);
            }
            $data['type'] = 0;
            $data['uid'] = $_SESSION['MEMBER']['ID'];
            $data['bid'] = $_POST['borrow'];
            $data['total'] = $_POST['total'];
            $data['date'] = time();
            if($id=D('BorrowApply')->add($data)){
                $this->ajaxReturn('','配资续约申请提交成功，请等待我们工作人员审核！',0);
            }else{
               $this->ajaxReturn('','系统暂时出现异常，请稍后重试！',9);
            }
        }else{
            $this->ajaxReturn('','请选择要续约的时间！',1);
        }

    }

}