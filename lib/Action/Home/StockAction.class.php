<?php
class StockAction extends HomeAction {
    private $type = null;
    public function init(){
        $this->type = C('TRADE_TYPE');
        if($_POST){
            if($_POST['done']=='true'){
                $this->done();
            }else{
                if(!$_SESSION['MEMBER']['ID']){
                    header('location:/member/login.html?from='.urlencode($_SERVER['HTTP_REFERER']));
                }
                $this->confirm();
            }
            exit;
        }

        $this->param['new'] = D('Borrow b')->field('m.user_name `name`,b.borrow_money `total`,b.borrow_type `type`,b.homs_id `homs`')->join('ynw_member m ON b.borrow_uid = m.id')->order('b.id DESC')->limit(20)->select();

        foreach($this->param['new'] as $key => $val){
            $name = $val['name'];
            $val['name'] = cutstr($name,0,2,false).'***';
            $val['name'] .= cutstr($name,3,-2,false);
            $val['total'] = ceil(number_format($val['total']/10000,1));

            if($val['homs']=='0'){
                switch ($val['type']) {
                    default:
                        $val['type'] = '申请了';
                        break;
                }
                $val['postfix'] = '借款';
            }else{
                switch ($val['type']) {
                    case '9':
                        $val['type'] = '按月申请';
                        break;
                    case '8':
                        $val['type'] = '按天申请';
                        break;
                    case '7':
                        $val['type'] = '按周申请';
                        break;
                    case '6':
                        $val['type'] = '参赛获得';
                        break;
                    default:
                        $val['type'] = '申请了';
                        break;
                }
                $val['postfix'] = '实盘资金';
            }

            $this->param['new'][$key] = $val;
        }
        $this->data = 'empty';
    }

    public function index(){
        $this->param['stock_month'] = $this->parse($this->param['stock_month']);
        $this->param['stock_week'] = $this->parse($this->param['stock_week']);
        $this->param['stock_day'] = $this->parse($this->param['stock_day']);
		$this->display();
    }

    public function experience(){
		$this->display('experience');
    }

    public function match(){
		$this->display();
    }

    public function month(){
        $this->param['page_title'] = '按月配资';
        $this->param['stock_month'] = $this->parse($this->param['stock_month']);
		$this->display();
    }

    public function week(){
        $this->param['page_title'] = '按周配资';
        $this->param['stock_week'] = $this->parse($this->param['stock_week']);
		$this->display();
    }

    public function day(){
        $this->param['page_title'] = '按天配资';
        $this->param['stock_day'] = $this->parse($this->param['stock_day']);
		$this->display();
    }

    public function confirm(){
        if($_SSESSION['MEMBER']['STATUS']['IDCARD']!='1'){
            //header('location:/member/?go=/validate/idcard.html');
        }
        $this->data['postfix'] = '天';
        $this->data['repayment'] = '到期还本。';
        $this->data['tips'] = '投资沪深A股，仓位不限制，盈利全归您';
        //判断第二天是否为休息日
        switch(date('w')){
            case '6':
                $this->data['start'] = strtotime('+2 day');
                break;
            case '5':
                $this->data['start'] = strtotime('+3 day');
                break;
            default:
                $this->data['start'] = strtotime('+1 day');
                break;
        }
        switch($_POST['type']){
            case '9':
            $param = $this->parse($this->param['stock_month']);
            $this->data['warning'] = '<em>'.number_format($_POST['quota']*$param[2]).'</em> 元';
            $this->data['lowest'] = '<em>'.number_format($_POST['quota']*$param[3]).'</em> 元';
            $this->data['postfix'] = '个月';
            $this->data['repayment'] = '每月还息，到期还本。';
            $this->data['deadline'] = strtotime('+'.$_POST['duration'].' month');
            break;
            case '8':
            $param = $this->parse($this->param['stock_day']);
            $this->data['warning'] = '<em>'.number_format($_POST['quota']*$param[2]).'</em> 元';
            $this->data['lowest'] = '<em>'.number_format($_POST['quota']*$param[3]).'</em> 元';
            $this->data['deadline'] = 0;
            $this->data['total'] = $_POST['fee']*$_POST['duration'];

            break;
            case '7':
            $param = $this->parse($this->param['stock_week']);
            $this->data['warning'] = '<em>'.number_format($_POST['quota']*$param[2]).'</em> 元';
            $this->data['lowest'] = '<em>'.number_format($_POST['quota']*$param[3]).'</em> 元';
            $this->data['tips'] = '投资沪深A股，仓位不限制，盈利七成归您';
            //周配资使用5天，减掉非交易日时间
            $j=1;
            $i=1;
            while ( $i<= 30) {
                $this->data['deadline'] = strtotime('+'.$i.' day');
                if(date('w',$this->data['deadline'])!=0&&date('w',$this->data['deadline'])!=6){
                    if($j>5){
                        break;
                    }
                    $j++;
                }
                $i++;
            }
            break;
            case '6':
            $param = $this->parse($this->param['stock_week']);
            $this->data['warning'] = '不限';
            //根据比赛时间确定交易结束时间
            $event=D('Event')->find($_POST['event']);
            $this->data['lowest'] = '<em>'.number_format($_POST['quota']*$event['lowest']).'</em> 元';
            $event = D('Event')->find($_POST['event']);
            $this->data['start'] = $event['begin'];
            $this->data['deadline'] = $event['end'];
            break;
            case '5':
            $param = $this->parse($this->param['stock_week']);
            //处理交易两天后的时间
            $j=1;
            $i=1;
            while ( $i<= 30) {
                $this->data['deadline'] = strtotime('+'.$i.' day');
                if(date('w',$this->data['deadline'])!=0&&date('w',$this->data['deadline'])!=6){
                    if($j>$_POST['duration']){
                        break;
                    }
                    $j++;
                }
                $i++;
            }

            $this->data['tips'] = '亏损我们承担，盈利全归您';
            $this->data['warning'] = '不限';
            $this->data['lowest'] = '不限';
            break;
        }

        $this->data['account'] = D('MemberAccount')->field('account_money `money`')->find(intval($_SESSION['MEMBER']['ID']));
        $this->data['interest'] = $_POST['interest']==0?'完全免费':'<i class="fs20">'.$_POST['interest'].'</i> 分 / 每月';
        $this->data['interest_month'] = ($_POST['interest']/100)*$_POST['quota'];
        $left = $this->data['account']['money']-$_POST['deposit']-$this->data['total']-$this->data['interest_month'];
        $this->data['account']['left'] = $left>0 ? '0' : abs($left);
        $this->data['fee'] = $_POST['fee']==0?'完全免费':'<i class="fs20">'.$_POST['fee'].'</i> 元/每天 总 '.$this->data['total'].' 元';
        $this->display('confirm');
    }

    public function check($data){
        //检查个人资料是否完整

        //检查账户余额是否充足

        //检查是否已经存在同类型的借款

        //如果是体验，检查是否已经体验过了



        return $data;
    }

    public function done($data){

        $data['borrow_type'] = $_POST['type'];

        $data['borrow_name'] = $_SESSION['MEMBER']['NAME'].$this->type[$_POST['type']].$event['name'];
        $data['borrow_uid'] = intval($_SESSION['MEMBER']['ID']);
        $data['borrow_duration'] = $_POST['duration'];
        $data['borrow_money'] = $_POST['quota'];
        $data['borrow_interest_rate'] = $_POST['interest']*12;
        $data['borrow_interest'] = $_POST['interest']*$_POST['quota'];
        $data['borrow_fee'] = floatval($_POST['fee']);
        $data['deposit'] = $_POST['deposit'];
        $data['add_time'] = time();
        $data['add_ip'] = $_SERVER['REMOTE_ADDR'];
        $data['risk_rate'] = $_POST['risk'];
        //$data['borrow_info'] = $_POST['start'];
        $data['deadline'] = $_POST['deadline'];
        $data['deal_start'] = $_POST['start'];
        $data['homs_id'] = 1;


        if($data['borrow_type']==9){
            $data['repayment_type'] = 4;
        }else{
            $data['repayment_type'] = 7;
        }

        if($id = D('Borrow')->add($data)){
            //配资比赛
            if($data['borrow_type']==6){
                $event = D('Event')->find($_POST['event']);
                $people['event'] = $event['id'];
                $people['borrow'] = $id;
                $people['member'] = $data['borrow_uid'];
                $people['name'] = $_SESSION['MEMBER']['NAME'];
                $people['total'] = $data['borrow_money']+$data['deposit'];
                $people['duration'] = $data['borrow_duration'];
                $people['date'] = $data['add_time'];
                D('EventPeople')->add($people);
                D('Event')->where('id='.$event['id'])->setInc('people',1);
            }

            $log['borrow'] = $id;
            $log['memo'] = "冻结".$this->type[$_POST['type']]."配资保证金，配资单号：PZ".$id;
            logMoney($data['borrow_uid'],0,floatval($data['deposit']),$log);
            //扣除当月利息
            if($data['borrow_type']==9){
                $interest = ($_POST['interest']/100)*$_POST['quota'];
                $log['memo'] = "冻结配资当月利息，配资单号：PZ".$id;
                logMoney($data['borrow_uid'],0,floatval($interest),$log);
            }elseif($data['borrow_type']==8){
                $fee = $data['borrow_fee'];
                $log['memo'] = "冻结配资管理费，配资单号：PZ".$id;
                logMoney($data['borrow_uid'],0,floatval($fee),$log);
            }
        }

        header('location:/member/?go=/stock/');
    }

    public function parse($data){
        $data = explode('|',$data);
        foreach($data as $key=>$val){
            if(strpos($val,'-')){
                $data[$key] = explode('-',$val);
            }
            if(strpos($val,',')){
                $data[$key] = explode(',',$val);
            }
        }
        return $data;
    }

}