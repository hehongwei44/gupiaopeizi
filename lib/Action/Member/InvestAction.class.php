<?php
class InvestAction extends MemberAction {
    public $debt = null;

    public function init(){
        $this->debt = new DebtBehavior($_SESSION['MEMBER']['ID']); 
    }

    public function index(){
        $map['i.investor_uid']=$_SESSION['MEMBER']['ID'];
        $map['b.borrow_status'] = $_GET['tab']==''||$_GET['tab']==2 ? array('IN','2,4') : intval($_GET['tab']);
        $field = 'b.id `borrow`,b.borrow_money `money`,b.borrow_type `type`,b.borrow_duration `duration`,b.borrow_interest_rate `rate`,b.borrow_name `name`,i.investor_capital `capital`,i.investor_capital `capital`,i.add_time `start`,i.deadline';
        $this->data=D('BorrowInvestor i')->field($field)->where($map)->join('ynw_borrow b ON b.id=i.borrow_id')->order('i.id DESC')->select();
        $this->display();
    }

    public function transfer(){
        if($_POST){
            $this->data=D('BorrowInvestor i')->field('i.id,i.investor_capital,i.investor_interest,b.total')->join("ynw_borrow b ON b.id = i.borrow_id")->where('i.id='.$_POST['data'])->find();
            $this->data['count'] = $this->debt->countDebt($this->data['id']);
            $this->data['invest'] = intval($this->data['id']);            
            $this->display('sell');            
        }else{
            switch($_GET['tab']){
                case 'ing':
                    $data = $this->debt->onBonds();
                    $tpl='transfer_ing';
                    break;
                case 'out':
                    $data = $this->debt->successDebt();
                    $tpl='transfer_log';
                    break;
                case 'in':
                    $data = $this->debt->buyDetb();
                    $tpl='transfer_log';
                    break;
                default:
                    $data = $this->debt->canTransfer(); 
                    break;
            }           
            $this->data=$data['data'];
            $this->param['pages']=$data['page'];
            $this->display($tpl);
        }

    }

    public function auto(){
        if($_POST){
            $_POST['uid'] = $_SESSION['MEMBER']['ID'];
            if($_POST['rate_min']>$_POST['rate_max']){
                $this->ajaxReturn(null,'保存失败，年化收益率最大值不能小于最小值！',2);
            }
            if($_POST['duration_min']>$_POST['duration_max']){
                $this->ajaxReturn(null,'保存失败，投标借款期限最大值不能小于最小值！',1);
            }
            if(intval($_POST['id'])==0){
                $ret = D('InvestorAuto')->add($_POST);
            }else{
                $ret = D('InvestorAuto')->save($_POST);
            }
            if($ret){
                $this->ajaxReturn(null,'已成功保存自动投标设置！',0);
            }else{
                $this->ajaxReturn(null,'自动投标设置保存失败，请确认是否修改了设置！',1);
            }            
        }
        $this->data=D('InvestorAuto')->where('uid='.intval($_SESSION['MEMBER']['ID']))->find();
        $this->data['account'] = D('MemberAccount')->field('account_money `money`')->where('uid='.intval($_SESSION['MEMBER']['ID']))->find();
        $this->display();
    }

    public function sell(){
        if(intval($_POST['price'])==0){
            $this->ajaxReturn('','必须输入转让价格！',1);
        }

        $ret = $this->debt->sell($_POST['invest'],$_POST['price'],$_POST['password']);
        if($ret=='1'){
            $this->ajaxReturn('','债权转让成功！',0);
        }else{
            $this->ajaxReturn('',$ret,2);
        }
    }

    public function cancel(){
        if(intval($_POST['data'])>0){
            if($this->debt->cancelDebt($_POST['data'],1)){
                echo '债权转让已撤销！';
            }else{
                echo '债权转让撤销失败！';
            }
        }
    }
}