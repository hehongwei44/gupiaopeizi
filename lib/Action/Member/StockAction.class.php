<?php
class StockAction extends MemberAction {
    public function init(){
        //$this->data='dddddd';
    }

    public function index(){
        $map['homs_id'] = array('gt',0);
        $map['borrow_uid'] = $_SESSION['MEMBER']['ID'];

        if($_REQUEST['id']!=''){
            $map['id'] = intval(str_replace('PZ','',$_REQUEST['id']));
        }

        if($_REQUEST['type']!=''){
            $map['borrow_type'] = $_REQUEST['type'];
        }

        if($_REQUEST['tab']!=''){
            if($_REQUEST['tab'] == '-'){
                $map['deadline'] = array('BETWEEN',array(time(),strtotime('+3 day')));
            }else{
                $map['borrow_status'] = intval($_REQUEST['tab']);
            }

        }


        import("ORG.Util.Page");
        $Page = new Page(M('Borrow')->where($map)->count(),10);
        $this->param['pages'] = $Page->show();

        $field = 'b.`id`,borrow_type `type`,borrow_money `money`,borrow_fee `fee`,`deposit`,borrow_money+deposit `total`,borrow_status `status`,homs_id `homs`,first_verify_time `begin`,deal_start `start`,deadline,add_time `date`';
        $this->data=D('Borrow `b`')->field($field)->where($map)->order('id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
        foreach($this->data as $key=>$val){
            switch($val['type']){
                case '9':
                    $param = explode('|',$this->param['stock_month']);
                    $val['warning'] = $param[2]*$val['money'];
                    $val['break'] = $param[3]*$val['money'];
                    break;
                case '8':
                    $param = explode('|',$this->param['stock_day']);
                    $val['warning'] = $param[2]*$val['money'];
                    $val['break'] = $param[3]*$val['money'];
                    break;
                case '7':
                    $param = explode('|',$this->param['stock_week']);
                    $val['warning'] = $param[2]*$val['money'];
                    $val['break'] = $param[3]*$val['money'];
                    break;
            }
            $this->data[$key] = $val;
        }

        $this->assign('type',C('TRADE_TYPE'));
		$this->display();
    }

    public function detail(){
        $repayment = C('REPAYMENT_TYPE');
        $this->data = D('Borrow')->where('id='.$_GET['i'].' AND borrow_uid='.$_SESSION['MEMBER']['ID'])->find();
        $this->data['repayment'] = $repayment[$this->data['repayment_type']];
        if($this->data['borrow_type']=='9'){
            $this->data['borrow_duration'] = $this->data['borrow_duration'].' 月';
        }else{
            $this->data['borrow_duration'] = $this->data['borrow_duration'].' 天';
        }
        $this->data['homs'] = D('Homs')->where('id='.intval($this->data['homs_id']))->find();

        $this->data['log'] = D('MemberMoney')->field('`id`,`type`,`affect_money` `affect`,`add_time` `time`')->where('borrow='.intval($this->data['id']))->select();
        $this->assign('type',C('MONEY_LOG'));
        $this->display();
    }

    public function contract(){
        $repayment = C('REPAYMENT_TYPE');
        $this->data = D('Borrow')->where('id='.$_GET['i'])->find();
        $this->data['member'] = D('Member m')->field('m.user_name `login`,i.real_name `name`,i.idcard')->join('ynw_member_info i ON i.uid = m.id')->where('uid='.$this->data['borrow_uid'])->find();
        $this->data['repayment'] = $repayment[$this->data['repayment_type']];
        if($this->data['borrow_type']=='9'){
            $this->data['borrow_duration'] = $this->data['borrow_duration'].' 月';
        }else{
            $this->data['borrow_duration'] = $this->data['borrow_duration'].' 天';
        }

        $this->display(APP_ROOT.'/data/conf/tpl/contract.html');
    }

    public function now(){
        $map['b.homs_id'] = array('gt',0);

        if($_REQUEST['id']!=''){
            $map['b.id'] = intval(str_replace('PZ','',$_REQUEST['id']));
        }

        if($_REQUEST['homs']!=''){
            $map['h.name'] = $_REQUEST['homs'];
        }

        if($_REQUEST['type']!=''){
            $map['b.borrow_type'] = $_REQUEST['type'];
        }

        $map['borrow_uid'] = $_SESSION['MEMBER']['ID'];

        import("ORG.Util.Page");
        $Page = new Page(D('Borrow b')->join('ynw_homs h ON h.bid=b.id')->where($map)->count(),10);
        $this->param['pages'] = $Page->show();

        $field = 'b.`id`,h.`name`,borrow_type `type`,borrow_money `money`,borrow_status `status`,homs_id `homs`,first_verify_time `begin`,add_time `date`';
        $this->data=D('Borrow `b`')->field($field)->where($map)->join('ynw_homs h ON h.id=b.homs_id')->order('id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
        foreach($this->data as $key=>$val){
            switch($val['type']){
                case '9':
                    $param = explode('|',$this->param['stock_month']);
                    $val['warning'] = $param[2]*$val['money'];
                    $val['break'] = $param[3]*$val['money'];;
                    break;
                case '8':
                    $param = explode('|',$this->param['stock_day']);
                    $val['warning'] = $param[2]*$val['money'];
                    $val['break'] = $param[3]*$val['money'];;
                    break;
                case '7':
                    $param = explode('|',$this->param['stock_week']);
                    $val['warning'] = $param[2]*$val['money'];
                    $val['break'] = $param[3]*$val['money'];;
                    break;
            }
            $this->data[$key] = $val;
        }
        $this->assign('type',C('TRADE_TYPE'));
        $this->display();
    }

    public function homs(){
        $homs = explode('-',$_POST['data']);
        $this->data = D('Homs')->where('id='.$homs[1].' AND uid='.$_SESSION['MEMBER']['ID'])->find();
        $this->display();
    }

    public function stop(){
        if($_POST['reason']){
            if(strlen($_POST['reason'])<10){
                $this->ajaxReturn('','最少要输入10个字符！',1);
            }
            if(strlen($_POST['reason'])>120){
                $this->ajaxReturn('','输入的内容过多，不能超过120个字符！',2);
            }
            $apply = D('BorrowApply')->where('`type`=9 AND `bid`='.$_POST['borrow'].' AND `status`=0')->find();
            if($apply){
                $this->ajaxReturn('','该配资方案已存在未审核的终止申请！',3);
            }
            $data['type'] = 9;
            $data['uid'] = $_SESSION['MEMBER']['ID'];
            $data['bid'] = $_POST['borrow'];
            $data['total'] = 0;
            $data['reason'] = $_POST['reason'];
            $data['date'] = time();
            if(D('BorrowApply')->add($data)){
                $this->ajaxReturn('','终止配资申请提交成功，请等待我们工作人员审核！',0);
            }else{
               $this->ajaxReturn('','系统暂时出现异常，请稍后重试！',9);
            }
        }else{
            $this->data['borrow'] = $_POST['data'];
            $this->display();
        }
    }

}