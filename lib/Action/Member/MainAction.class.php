<?php
class MainAction extends MemberAction {
    public function index(){
    	$this->param['page_title'] = '会员中心';
        $data['group'] = D('Navigation')->field('id,type_name `name`,type_url `url`')->where('parent_id = 9 AND is_hiden = 0')->select();
        foreach($data['group'] as $val){
            $group[$val['id']]='';
        }
        $item = D('Navigation')->field('id,type_name `name`,type_url `url`,parent_id `parent`')->where('parent_id IN('.implode(',',array_keys($group)).') AND is_hiden = 0')->select();
        foreach($item as $val){
            $data['item'][$val['parent']][] = $val;
        }

        $login = D('MemberLogin')->field('`date`')->where('uid='.$_SESSION['MEMBER']['ID'])->order('id DESC')->limit(2)->select();
        $this->param['last'] = $login[1];
        $this->data = $data;
		$this->display();
    }

    public function login(){
    	$this->param['page_title'] = '会员登录';
        if($_POST){
            if($_SESSION['MEMBER']['ERROR']&&$_SESSION['verify']!=$_POST['authcode']){
                $_SESSION['MEMBER']['ERROR'] = true;
                $this->ajaxReturn('ua',验证码错误,3);
            }
            $member=D('member')->field('id,user_name `name`,user_pass `password`,user_type `type`,is_vip `vip`,login_times `times`')->where($this->parse($_POST['username']))->find();
            if($member){
                if(md5($_POST['password'])!=$member['password']){
                    $_SESSION['MEMBER']['ERROR'] = true;
                    $this->ajaxReturn('up','密码不正确',2);
                }else{
                    $_SESSION['MEMBER']['ID'] = $member['id'];
                    $_SESSION['MEMBER']['NAME'] = $member['name'];
                    $_SESSION['MEMBER']['TYPE'] = $member['type'];
                    $_SESSION['MEMBER']['STATUS'] = D('MemberStatus')->field('phone_status `MOBILE`,id_status `IDCARD`,email_status `EMAIL`,account_status `BANK`,vip_status `VIP`')->where('uid='.$member['id'])->find();
                    D('member')->where('id='.$member['id'])->setInc('login_times',1);
                    $data['uid'] = $_SESSION['MEMBER']['ID'];
                    $data['ip'] = $_SERVER['REMOTE_ADDR'];
                    $data['agent'] = $_SERVER['HTTP_USER_AGENT'];
                    $data['times'] = $member['times']+1;
                    $data['entry'] = $_POST['from'];
                    $data['date'] = time();
                    D('MemberLogin')->add($data);
                    $this->ajaxReturn('ok','验证成功',0);
                }
            }else{
                $_SESSION['MEMBER']['ERROR'] = true;
                $this->ajaxReturn('un','用户名不存在',1);
            }

        }else{
            $referer = $_SERVER['HTTP_REFERER']==''?'/member/':$_SERVER['HTTP_REFERER'];
            $this->param['login_from'] = $_GET['from']==''?$referer:$_GET['from'];
            $this->param['error'] = $_SESSION['MEMBER']['ERROR'];
            $this->display();
        }

    }

	public function alogin(){
        if($_POST){
            $member=D('member')->field('id,user_name `name`,user_pass `password`,user_type `type`,is_vip `vip`,login_times `times`')->where($this->parse($_POST['username']))->find();
            if($member){
                if(md5($_POST['password'])!=$member['password']){
                    $_SESSION['MEMBER']['ERROR'] = true;
                    $this->ajaxReturn('up','密码不正确',2);
                }else{
                    $_SESSION['MEMBER']['ID'] = $member['id'];
                    $_SESSION['MEMBER']['NAME'] = $member['name'];
                    $_SESSION['MEMBER']['TYPE'] = $member['type'];
                    $_SESSION['MEMBER']['STATUS'] = D('MemberStatus')->field('phone_status `MOBILE`,id_status `IDCARD`,email_status `EMAIL`,account_status `BANK`,vip_status `VIP`')->where('uid='.$member['id'])->find();
                    D('member')->where('id='.$member['id'])->setInc('login_times',1);
                    $data['uid'] = $_SESSION['MEMBER']['ID'];
                    $data['ip'] = $_SERVER['REMOTE_ADDR'];
                    $data['agent'] = $_SERVER['HTTP_USER_AGENT'];
                    $data['times'] = $member['times']+1;
                    $data['entry'] = $_POST['from'];
                    $data['date'] = time();
                    D('MemberLogin')->add($data);
                    header('Location:/member/');
                }
            }else{
                header('Location:/member/login.html');
            }

        }else{
			header('Location:/member/login.html');
        }

    }

    public function register(){

    	$this->param['page_title'] = '免费注册';
        if($_POST){
            if($_POST['smscode']!=$_SESSION['MEMBER']['SMSCODE']){
                $this->ajaxReturn('ia','手机验证码不正确',7);
            }

            if($_POST['name']!=''){
                $member = D('Member')->where('user_name = "'.$_POST['name'].'"')->find();
                if($member){
                    $this->ajaxReturn('iu','用户名已存在',2);
                }
            }else{
                $this->ajaxReturn('iu','用户名必须填写',1);
            }

            if($_POST['mobile']!=''){
                if(!is_numeric($_POST['mobile'])||strlen($_POST['mobile'])!=11){
                    $this->ajaxReturn('im','手机号码不正确',1);
                }
                $member = D('Member')->where('user_phone = "'.$_POST['mobile'].'"')->find();
                if($member){
                    $this->ajaxReturn('im','该手机号已经存在',3);
                }
            }else{

            }

            if($_POST['password']!=''){
                if(strlen($_POST['password'])<6||strlen($_POST['password'])>20){
                    $this->ajaxReturn('ip','密码必须在6-20位之间',4);
                }
                if($_POST['password']!=$_POST['confirm']){
                    $this->ajaxReturn('ip','两次输入的密码不一致',5);
                }
            }else{
                $this->ajaxReturn('ip','必须设置登录密码',6);
            }

            if($_POST['invitor']!=''){
                $invitor = D('Member')->field('id')->where('MD5(id) = "'.$_POST['invitor'].'"')->find();
                $data['recommend_id'] = $invitor['id'];
            }

            $data['user_name'] = $_POST['name'];
            $data['user_phone'] = $_POST['mobile'];
            $data['user_pass'] = md5($_POST['confirm']);
            $data['reg_time'] = time();
            $data['reg_ip'] = $_SERVER['REMOTE_ADDR'];

            if($id = D('Member')->add($data)){
                D('MemberStatus')->add(array('uid'=>$id,'phone_status'=>1));
                $_SESSION['MEMBER']['ID'] = $id;
                $_SESSION['MEMBER']['NAME'] = $data['user_name'];
                $_SESSION['MEMBER']['TYPE'] = 1;

                $_SESSION['MEMBER']['STATUS']['MOBILE'] = 1;

                $this->ajaxReturn('ok','注册成功',0);
            }

        }else{
           $this->display();
       }
    }

    public function forget(){
        $this->param['page_title'] = '忘记密码';
        if($_POST){
            if($_POST['exists']){
                if($_POST['check']){
                    if($_POST['check']==$_SESSION['MEMBER']['MAILCODE']){
                        $this->ajaxReturn(null,'验证通过',0);
                    }else{
                        $this->ajaxReturn(null,'邮箱验证码不正确',3);
                    }
                }else{
                    $member = D('Member')->field('id,user_name')->where('user_email="'.$_POST['exists'].'"')->find();
                    if($member){
                        $key = md5($member['id'].$_POST['email']);
                        if($_SESSION['MEMBER']['MAILCODE']!=$key){
                            $_SESSION['MEMBER']['MAILCODE'] = $key;
                            $msgconfig = FS("data/conf/message");
                            import("ORG.Net.Email");
                            $smtp['port'] = $msgconfig['stmp']['port'];//25;
                            $smtp['host'] = $msgconfig['stmp']['server'];
                            $smtp['user'] = $msgconfig['stmp']['user'];
                            $smtp['pass'] = $msgconfig['stmp']['pass'];
                            $mailtype = "HTML";
                            $sender = $msgconfig['stmp']['user'];
                            $smtp = new smtp($smtp['host'],$smtp['port'],true,$smtp['user'],$smtp['pass'],$sender);
                            $content = '您在'.$this->param['web_name'].'注册用户名为'.$member['user_name'].'的帐号忘记了密码，现在使用该邮箱找回登录密码。<br/>验证码：'.$_SESSION['MEMBER']['MAILCODE'];
                            $send = $smtp->sendmail($_POST['exists'],$this->param['web_name'],'使用邮箱找回密码',$content,$mailtype);
                        }
                        $this->ajaxReturn(null,'验证码已经发送到邮箱，请将邮件内验证吗复制到下面验证码框',1);
                    }else{
                        $this->ajaxReturn(null,'邮箱还没有被绑定',2);
                    }
                }
            }else{
                if($_POST['mobile']){
                    $member = D('Member')->field('id,user_name,user_phone')->where('user_phone="'.$_POST['mobile'].'"')->find();
                    if($member['id']==''){
                        $this->ajaxReturn(null,'该手机号没被绑定',1);
                    }
                    if($_POST['check']!=$_SESSION['MEMBER']['SMSCODE']){
                        $this->ajaxReturn(null,'输入的验证码不正确',2);
                    }else{
                        $_SESSION['MEMBER']['PHONECODE'] = md5($member['id'].$member['user_phone']);
                        $this->ajaxReturn(null,'验证码正确进入下一步',0);
                    }
                }
                if($_POST['password']){
                    if($_POST['method']=='email'){
                        D('Member')->where('"'.$_SESSION['MEMBER']['MAILCODE'].'" = MD5(CONCAT(`id`,`user_email`))')->save(array('user_pass'=>md5($_POST['password'])));
                    }else{
                        D('Member')->where('"'.$_SESSION['MEMBER']['PHONECODE'].'" = MD5(CONCAT(`id`,`user_phone`))')->save(array('user_pass'=>md5($_POST['password'])));
                    }
                }
            }
        }
        $this->display();
    }

    public function logout(){
        unset($_SESSION['MEMBER']);
        header('location:/member/login.html');
        exit;
    }

    public function parse($str){
         if (strpos($str,'@')&&strpos($str,'.')){
            //屏蔽邮箱登录
            //$map['user_email'] = $str;
            $map['user_name'] = $str;
         }elseif(is_numeric($str)&&strlen($str)==11){
            $map['user_phone'] = $str;
         }else{
            $map['user_name'] = $str;
         }
         return $map;
    }

    public function profile(){
        $mi = D('MemberInfo');
        $this->data = $mi->where('uid='.$_SESSION['MEMBER']['ID'])->find();
        if($_POST){
            $data['zy'] = $_POST['vocation'];
            $data['job'] = $_POST['job'];
            $data['marry'] = $_POST['marriage'];
            $data['education'] = $_POST['education'];
            $data['income'] = $_POST['income'];
            $data['address'] = $_POST['workAddress'];
            $data['province'] = $_POST['workProvince'];
            $data['city'] = $_POST['workCity'];
            $data['area'] = $_POST['workArea'];
            $data['address_now'] = $_POST['liveAddress'];
            $data['province_now'] = $_POST['liveProvince'];
            $data['city_now'] = $_POST['liveCity'];
            $data['area_now'] = $_POST['liveArea'];
            if($this->data){
                $mi->where('uid='.$_SESSION['MEMBER']['ID'])->save($data);
            }else{
                $data['uid'] = $_SESSION['MEMBER']['ID'];
                $mi->add($data);
            }
            $this->ajaxReturn(null,'个人信息保存成功。',0);
        }else{
            $this->display();
        }
    }

    Public function password(){
        if($_POST){
            $data['id'] = $_SESSION['MEMBER']['ID'];
            $member = D('Member')->find($data['id']);
            if($_POST['oldPwd']==''||$member['user_pass']!=MD5($_POST['oldPwd'])){
                $this->ajaxReturn('io','输入的原密码不正确',1);
            }

            if($_POST['newPwd']==''||strlen($_POST['newPwd'])<6){
                $this->ajaxReturn('io','新密码长度不够6位',2);
            }


            $data['user_pass'] = md5($_POST['newPwd']);

            if($member['user_pass']==$data['user_pass']){
                $this->ajaxReturn('io','新密码和就密码不能一致',3);
            }

            if(D('Member')->save($data)){
                $this->ajaxReturn(null,'新密码已经保存，下次登录即生效。',0);
            }else{
                $this->ajaxReturn('io','系统繁忙，请稍后重新尝试。',9);
            }
        }else{
            $this->display();
        }
    }

    Public function authcode(){
        import('ORG.Util.Image');
        Image::buildImageVerify();
    }

    Public function log(){
        $map['uid'] = $_SESSION['MEMBER']['ID'];
        $this->param['rows'] = M('MemberLogin')->where($map)->count();
        import("ORG.Util.Page");
        $Page = new Page($this->param['rows'],10);
        $this->param['pages'] = $Page->show();
        $this->data = M('MemberLogin')->where($map)->order('id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->display();
    }

    public function area(){
        if(is_numeric($_GET['parent'])){
            $city=D('Area')->where('reid='.intval($_GET['parent']))->order('sort_order ASC')->select();
            foreach($city as $key){
                echo '<option value="'.$key['id'].'" '.($_GET['init']==$key['id']?'selected':'').'>'.$key['name'].'</option>';
            }
        }
    }

    public function sendsms(){
        $member = D('Member')->field('id')->where('user_phone="'.$_POST['mobile'].'"')->find();
        if($_POST['bind']=='true'){
            if($member['id']){
               $this->ajaxReturn('ia','该手机号已经被绑定过。',1);
            }
        }else{
            if(!$member['id']){
               $this->ajaxReturn('ia','该手机号还没有绑定',2);
            }
        }
        $_SESSION['MEMBER']['SMSCODE'] = rand_string($_SESSION['MEMBER']['ID'],4,1);
        $sms = '您在'.$this->param['web_name'].'需要验证手机号，本次的验证码：'.$_SESSION['MEMBER']['SMSCODE'].'。如果非本人操作，请尽快联系客服。';
        if(sendsms($_POST['mobile'],$sms)){
            $this->ajaxReturn(null,'',0);
        }

    }

    public function notice(){
        $check = D('MemberNotice')->where('uid='.$_SESSION['MEMBER']['ID'])->find();
        $this->data['check'] = $check['check'];

        if($_POST){
            if($this->data['check']){
                D('MemberNotice')->where('uid='.$_SESSION['MEMBER']['ID'])->save(array('check'=>implode(',',$_POST['notice'])));
            }else{
                D('MemberNotice')->add(array('uid'=>$_SESSION['MEMBER']['ID'],'check'=>implode(',',$_POST['notice'])));
            }
            $this->ajaxReturn(null,'通知点设置保存成功。',0);
        }else{
            $this->data['list'] = C('NOTICE_TYPE');
            $this->display();
        }

    }

}