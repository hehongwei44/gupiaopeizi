<?php
class DepositAction extends MemberAction {
    public function init(){
    }

    public function index(){
        $map['type'] = 1;
        $map['uid'] = intval($_SESSION['MEMBER']['ID']);
        if($_GET['tab']==''){
            $map['status'] = 0;
        }else{
            $map['status'] = $_GET['tab'];
        }

        import("ORG.Util.Page");
        $Page = new Page(M('BorrowApply')->where($map)->count(),10);
        $this->param['pages'] = $Page->show();

        $this->data=D('BorrowApply')->where($map)->order('id DESC')->select();
		$this->display();
    }

    public function add(){
        $this->data=D('MemberAccount')->field('money_freeze `freeze`,account_money `money`')->where('uid='.intval($_SESSION['MEMBER']['ID']))->find();
        $this->data['total'] = $this->data['money']+$this->data['freeze'];
        $this->data['borrow'] = $_POST['data'];
        $this->display();
    }

    public function save(){
        $account=D('MemberAccount')->field('money_freeze `freeze`,account_money `money`')->where('uid='.intval($_SESSION['MEMBER']['ID']))->find();
        if(is_numeric($_POST['total'])){
            if(intval($_POST['total'])>$account['money']){
                $this->ajaxReturn('','对不起，您账户的可用余额不足！',2);
            }else{
                $apply = D('BorrowApply')->where('`type`=1 AND `bid`='.$_POST['borrow'].' AND `status`=0')->find();
                if($apply){
                    $this->ajaxReturn('','该配资方案已存在未审核的保证金追加申请！',2);
                }
                $data['type'] = 1;
                $data['uid'] = $_SESSION['MEMBER']['ID'];
                $data['bid'] = $_POST['borrow'];
                $data['total'] = $_POST['total'];
                $data['date'] = time();
                if($id=D('BorrowApply')->add($data)){
                    $log['borrow'] = $data['bid'];
                    $log['memo'] = "冻结追加保证金，配资单号：".$data['bid'];
                    logMoney($data['uid'],0,$data['total'],$log);
                    $this->ajaxReturn('','追加保证金申请提交成功，请等待我们工作人员审核！',0);
                }else{
                   $this->ajaxReturn('','系统暂时出现异常，请稍后重试！',9);
                }
            }
        }else{
            $this->ajaxReturn('','请输入追加的保证金数额！',1);
        }

    }

}