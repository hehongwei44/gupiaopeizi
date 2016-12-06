<?php
class AccountAction extends MemberAction {
    public function index(){
        A('Member/Validate')->check('idcard');
        $this->param['account'] = $this->total(true);
        $this->param['member'] = D('Member')->where('id='.$_SESSION['MEMBER']['ID'])->find();
        $this->param['member']['number'] = str_pad($this->param['member']['id'],7,0,0);
        $this->param['member']['invitor'] = $this->param['member']['recommend_id']>0 ? str_pad($this->param['member']['recommend_id'],7,0,0) : '';
        $info = D('MemberInfo')->where('uid='.$this->param['member']['id'])->find();
        $this->param['member']=array_merge($this->param['member'],(array)$info,(array)$status);

		$this->display();
    }


    public function charge(){
        $payment = include(APP_ROOT.'/data/conf/payment.php');
        $this->data = $this->total();
        $this->data['payment'] = $payment;
        $this->display();
    }

    public function coupon(){
        $map['member'] = $_SESSION['MEMBER']['ID'];
        switch($_GET['tab']){
            case 'used':
                $map['apply'] = array('gt',0);
                break;
            case 'expire':
                $map['expire'] = array('lt',time());
                break;
            default:
                $map['apply'] = array('eq',0);
                $map['expire'] = array('gt',time());
                break;
        }

        $this->param['rows'] = M('MemberCoupon')->where($map)->count();
        import("ORG.Util.Page");
        $Page = new Page($this->param['rows'],10);
        $this->param['pages'] = $Page->show();

        $this->data = D('MemberCoupon')->where($map)->order('id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->display();
    }

    public function money(){
        $tab = array('dj'=>'0,4,6,15,24,37,15','lx'=>'23','ct'=>'27,29','jd'=>'11,17,18,19,30,31,37,47,48');
        $this->param['type'] = C('MONEY_LOG');
        $this->param['account'] = $this->total(true);

        $map['uid'] = $_SESSION['MEMBER']['ID'];
        if($_GET['tab']){
            $map['type']=array('IN',$tab[$_GET['tab']]);
        }else{
            $map['type']=array('NOT IN',$tab['dj']);
        }

        $this->param['total'] = M('MemberMoney')->where($map)->sum('affect_money');
        $this->param['rows'] = M('MemberMoney')->where($map)->count();
        $type=M('MemberMoney')->field('`type`')->where($map)->group('type')->select();
        foreach($type as $val){
            $this->param['option'][]=array('key'=>$val['type'],'name'=>$this->param['type'][$val['type']]);
        }

        import("ORG.Util.Page");
        $Page = new Page($this->param['rows'],10);
        $this->param['pages'] = $Page->show();

        $this->data = D('MemberMoney')->where($map)->order('id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();

        $this->display();
    }

    public function withdraw(){
        $member = D('Member')->field('user_phone `phone`,pin_pass `password`')->where('id='.$_SESSION['MEMBER']['ID'])->find();
        if($_POST){
            if(!is_numeric($_POST['money'])){
                $this->ajaxReturn(null,'请输入正确的提现金额。',2);
            }
            if(intval($_POST['bank'])<1){
                $this->ajaxReturn(null,'没有选择银行卡。',3);
            }
            if($_POST['smscode']!=$_SESSION['MEMBER']['SMSCODE']){
                $this->ajaxReturn(null,'手机验证码不正确。',4);
            }else{
                unset($_SESSION['MEMBER']['SMSCODE']);
            }
            if(md5($_POST['password'])!=$member['password']){
                $this->ajaxReturn(null,'支付密码不正确。',5);
            }

            $data['uid'] = $_SESSION['MEMBER']['ID'];
            $data['bank'] = $_POST['bank'];


            $data['withdraw_money'] = $_POST['money'];
            $data['withdraw_status'] = 0;
            $data['withdraw_fee'] = 0;
            $data['add_time'] = time();
            $data['add_ip'] = $_SERVER['REMOTE_ADDR'];

            if(D('MemberWithdraw')->add($data)){
                logMoney($_SESSION['MEMBER']['ID'],4,-floatval($_POST['money']));
                $this->ajaxReturn('/member/account/record.html','提现申请已经提交，请耐心等候工作人员处理。',0);
            }else{
                $this->ajaxReturn(null,'系统繁忙，请稍后重试。',1);
            }

        }else{
            $this->data = $this->total();
            $this->data['bank'] = D('MemberBanks')->field('id,bank_name `name`,RIGHT(bank_num,4) `number`')->where('uid='.$_SESSION['MEMBER']['ID'])->select();
            $this->data['phone'] = $member['phone'];
            $this->data['passowrd'] = $member['passowrd'];
            $this->display();
        }

    }

    public function transfer(){
        if($_POST){
            if($_POST['off_bank']==''){
                $this->ajaxReturn(null,'对不起，请选择转入的银行名称。',2);
            }
            if($_POST['money']<1){
                $this->ajaxReturn(null,'最少要冲入 1元，请修正后重新提交。',3);
            }
            $data['uid'] = $_SESSION['MEMBER']['ID'];
            $data['nid'] = 'offline';
            $data['money'] = $_POST['money'];
            $data['fee'] = 0;
            $data['way'] = 'off';
            $data['add_time'] = time();
            $data['add_ip'] = $_SERVER['REMOTE_ADDR'];
            $data['off_bank'] = $_POST['off_bank'];
            $data['off_way'] = $_POST['off_way'];

            if(D('MemberPayment')->add($data)){
                $this->ajaxReturn('/member/account/log.html','线下充值成功，工作人员核实后相应款项会转入您的账户。',0);
            }else{
                $this->ajaxReturn(null,'系统出现问题，请稍后重试！',1);
            }
        }else{
            $this->data = include(APP_ROOT.'/data/conf/banks.php');
            foreach($this->data['BANK'] as $k => $v){
                $val = explode('|',$v['bank']);
                $v['bank'] = $val[0];
                $v['icon'] = $val[1];
                $this->data['BANK'][$k] = $v;
            }
            $this->display();
        }
    }

    public function log(){
        $this->param['rows'] = M('MemberPayment')->where('uid='.$_SESSION['MEMBER']['ID'])->count();
        import("ORG.Util.Page");
        $Page = new Page($this->param['rows'],10);
        $this->param['pages'] = $Page->show();

        $this->data=D('MemberPayment')->where('uid='.$_SESSION['MEMBER']['ID'])->order('id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->display();
    }

    public function record(){
        $banks[] = 0;

        $this->param['rows'] = M('MemberWithdraw')->where('uid='.$_SESSION['MEMBER']['ID'])->count();
        import("ORG.Util.Page");
        $Page = new Page($this->param['rows'],10);
        $this->param['pages'] = $Page->show();

        $this->data=D('MemberWithdraw')->where('uid='.$_SESSION['MEMBER']['ID'])->order('id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
        foreach ($this->data as $key => $value) {
            $banks[] = $value['bank'];
        }
        $this->param['banks'] = D('MemberBanks')->where('id IN('.implode(',',array_unique($banks)).')')->getField('id,bank_name name,RIGHT(bank_num,4) `number`');
        $this->display();
    }

    public function password(){
        if($_POST['password']!=''){
            if($_POST['password']!=$_POST['confirm']){
                $this->ajaxReturn(null,'两次输入的密码不一致',2);
            }else{
                $data['id'] = $_SESSION['MEMBER']['ID'];
                $data['pin_pass'] = md5($_POST['password']);
                if(D('Member')->save($data)){
                    $this->ajaxReturn('','支付密码设置成功！',0);
                }else{
                    $this->ajaxReturn(null,'系统繁忙，请稍后重试。',1);
                }
            }
        }

        if($_POST['mobile']){
            if($_POST['smscode']==$_SESSION['MEMBER']['SMSCODE']&&$_POST['smscode']!=''){
                $this->assign('step','new');
                $data['title'] = '设置新密码';
                $data['content'] = $this->fetch('password');
                $this->ajaxReturn($data,'pop',0);
            }else{
                $this->ajaxReturn('','验证码不正确，请再次确认手机收到的验证码！',1);
            }

        }else{
            $this->data = D('Member')->field('user_phone `phone`,pin_pass `password`')->where('id='.$_SESSION['MEMBER']['ID'])->find();
            $this->display();
        }

    }


    public function bank(){
        $borrow = include(APP_ROOT.'/data/conf/borrow.php');
        $this->data=D('MemberBanks')->field('id,RIGHT(bank_num,4) `number`,bank_name `name`,bank_icon `icon`')->where('uid='.$_SESSION['MEMBER']['ID'])->select();
        if($_POST){
            if(count($this->data)>7){
                $this->ajaxReturn(null,"每人只能添加 8 张银行卡！",1);
            }
            $data['uid'] = $_SESSION['MEMBER']['ID'];
            $data['bank_num'] = $_POST['number'];
            $data['bank_province'] = $_POST['province'];
            $data['bank_city'] = $_POST['city'];
            $data['bank_address'] = $_POST['branch'];
            $data['bank_name'] = $_POST['bank'];
            $data['bank_icon'] = $_POST['bankCode'];
            $data['add_time'] = time();
            $data['add_ip'] = $_SERVER['REMOTE_ADDR'];
            if(D('MemberBanks')->add($data)){
                $this->ajaxReturn('/member/account/bank.html',"银行卡添加成功！",0);
            }else{
                $this->ajaxReturn(null,"银行卡添加失败，请检查是否填写正确！",1);
            }
        }else{
            $this->assign('banks',$borrow['BANK_NAME']);
            $this->param['info'] = D('MemberInfo')->where('uid='.$_SESSION['MEMBER']['ID'])->find();
            $this->display();
        }
    }

    public function total($deposit=false){
        $field = 'account_money `money`,money_freeze `freeze`,back_money `back`,money_collect `collect`,account_money+money_freeze+back_money `total`';
        $data = D('MemberAccount')->field($field)->where('uid='.$_SESSION['MEMBER']['ID'])->find();
        if($deposit){
            //可用余额
            $data['usable'] = $data['money']+$data['back'];
            //借款总额
            $data['borrow'] = D('Borrow')->where('borrow_status in(2,4,6) AND borrow_uid='.$_SESSION['MEMBER']['ID'])->sum('borrow_money');
            //配资资产
            $data['stock'] = D('Borrow')->where('homs_id>0 AND borrow_status = 6 AND borrow_uid='.$_SESSION['MEMBER']['ID'])->sum('borrow_money+deposit');
            //理财资产
            $data['invest'] = D('Investor')->where('investor_uid='.$_SESSION['MEMBER']['ID'])->sum('capital+interest-interest_fee-receive_interest-receive_capital');
            //净资产
            $data['asset'] = $data['stock']+$data['invest']+$data['total']-$data['borrow'];
        }
        return $data;
    }
}