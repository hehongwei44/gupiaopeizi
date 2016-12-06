<?php
class ProfitAction extends MemberAction {
    public function init(){
    }
    public function index(){
        $map['type'] = 2;
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
        if(is_numeric($_POST['total'])){
            $apply = D('BorrowApply')->where('`type`=2 AND `bid`='.$_POST['borrow'].' AND `status`=0')->find();
            if($apply){
                $this->ajaxReturn('','该配资方案已存在未审核的盈利提取申请！',1);
            }
            $data['uid'] = $_SESSION['MEMBER']['ID'];
            $data['bid'] = $_POST['borrow'];
            $data['type'] = 2;
            $data['total'] = $_POST['total'];
            $data['date'] = time();
            if(D('BorrowApply')->add($data)){
                $this->ajaxReturn('','盈利提取申请提交成功，请等待我们工作人员审核！',0);
            }else{
               $this->ajaxReturn('','系统暂时出现异常，请稍后重试！',9);
            }
        }else{
            $this->ajaxReturn('','请输入要提取的盈利金额！',1);
        }

    }

}