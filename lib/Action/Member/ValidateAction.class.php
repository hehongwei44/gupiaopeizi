<?php
class ValidateAction extends MemberAction {
    public function phone(){
    	if($_POST){
    		$member = D('Member');
    		if($_POST['smscode']!=$_SESSION['MEMBER']['SMSCODE']){
				$this->ajaxReturn(null,'验证码不正确，请重新输入手机收到的验证码！',1);
    		}
    		if($member->where('user_phone="'.$_POST['mobile'].'"')->find()){
				$this->ajaxReturn(null,'绑定的手机号已经被绑定，请确认号码后再次尝试！',1);
    		}
    		$data['id'] = $_SESSION['MEMBER']['ID'];
    		$data['user_phone'] = $_POST['mobile'];

			if($member->save($data)){
                $_SESSION['MEMBER']['STATUS']['MOBILE'] = 1;
                D('MemberStatus')->where('uid='.$data['id'])->save(array('phone_status'=>1));
				$this->ajaxReturn(null,'手机号 '.$_POST['mobile'].' 绑定成功，以后也可以用手机号登录啦！',0);
			}else{
				$this->ajaxReturn(null,'手机号绑定失败，请检查号码是否正确！',0);
			}
    	}else{
    		$this->display();
    	}

    }

    public function idcard(){
        $info = D('MemberInfo');
        $exists = $info->where('uid='.$_SESSION['MEMBER']['ID'])->find();

    	if($_POST){
            if($_POST['name']==''){
                $this->ajaxReturn(null,'没有输入真实姓名！',1);
            }
            if($_POST['number']==''){
                $this->ajaxReturn(null,'没有输入身份证号！',2);
            }
            if($_POST['front']==''){
                $this->ajaxReturn(null,'身份证正面图片没有上传！',3);
            }
            if($_POST['back']==''){
                $this->ajaxReturn(null,'身份证背面图片没有上传！',4);
            }
            $data['uid'] = $_SESSION['MEMBER']['ID'];
            $data['real_name'] = $_POST['name'];
            $data['sex'] = $_POST['sex'];
            $data['birthday'] = $_POST['birthday'];
            $data['age'] = $_POST['age'];
            $data['idcard'] = $_POST['number'];
            $data['card_img'] = $_POST['front'];
            $data['card_back_img'] = $_POST['back'];
            $data['up_time'] = time();
            D('MemberStatus')->where('uid='.$_SESSION['MEMBER']['ID'])->save(array('id_status'=>3));
            if($exists){
                $res = $info->where('uid='.$_SESSION['MEMBER']['ID'])->save($data);
            }else{
                $res = $info->add($data);
            }

            if($res){
                $_SESSION['MEMBER']['STATUS']['IDCARD'] = 3;
                $this->ajaxReturn('/member/account/','实名认证申请已提交，请担心等待工作人员核实！',0);
            }

    	}else{
            $ms = D('MemberStatus')->field('id_status `status`,id_time `time`')->where('uid='.$_SESSION['MEMBER']['ID'])->find();
            $exists['status'] = intval($ms['status']);
            $exists['time'] = $exists['status'] == 3 ? $exists['up_time'] : $ms['time'];
            $this->data = $exists;
            $this->data['idcard'] = substr($this->data['idcard'],0,4).'**********'.substr($this->data['idcard'],-4);
    		$this->display();
    	}

    }
    public function upload(){
        if($_FILES){
            //dump($_FILES);
            if($_FILES['idCardFrontFile']!=''){
                $file = '/files/idcard/'.$_SESSION['MEMBER']['ID'].'_z_'.md5($_SESSION['MEMBER']['ID'].'front').'.jpg';
                if(move_uploaded_file($_FILES['idCardFrontFile']['tmp_name'],APP_ROOT.$file)){
                    $this->ajaxReturn($file,'',0);
                }
            }
            if($_FILES['idCardBackFile']!=''){
                $file = '/files/idcard/'.$_SESSION['MEMBER']['ID'].'_f_'.md5($_SESSION['MEMBER']['ID'].'back').'.jpg';
                if(move_uploaded_file($_FILES['idCardBackFile']['tmp_name'],APP_ROOT.$file)){
                    $this->ajaxReturn($file,'',0);
                }
            }
        }

    }
    public function email(){
        if($_POST){
            $member = D('Member')->where('id!='.$_SESSION['MEMBER']['ID'].' AND user_email="'.$_POST['email'].'"')->find();
            if($member){
                $this->ajaxReturn(null,'该邮箱已经绑定了，不能重复绑定。',1);
            }
            $key = md5($_SESSION['MEMBER']['ID'].$_POST['email']);
            if($_POST['action']=='send'){
                //防止重复发送
                if($_SESSION['MEMBER']['MAILCODE']!=$key){
                    $msgconfig = FS("data/conf/message");
                    import("ORG.Net.Email");
                    $smtp['port'] = $msgconfig['stmp']['port'];//25;
                    $smtp['host'] = $msgconfig['stmp']['server'];
                    $smtp['user'] = $msgconfig['stmp']['user'];
                    $smtp['pass'] = $msgconfig['stmp']['pass'];
                    $mailtype = "HTML";
                    $sender = $msgconfig['stmp']['user'];
                    $smtp = new smtp($smtp['host'],$smtp['port'],true,$smtp['user'],$smtp['pass'],$sender);

                    $_SESSION['MEMBER']['MAILCODE'] = $key;
                    $send = $smtp->sendmail($_POST['email'],$this->param['web_name'],$this->param['web_name'].'邮箱绑定验证','您在'.$this->param['web_name'].'注册用户名为'.$_SESSION['MEMBER']['NAME'].'的帐号申请绑定邮箱。<br/>验证码：'.$_SESSION['MEMBER']['MAILCODE'],$mailtype);
                }
                $this->ajaxReturn(null,'验证码已经发送，请到邮箱查收！',0);
            }else{
                if($key!=$_POST['verifyCode']){
                    $this->ajaxReturn(null,'您输入的验证码不正确，请确认后重新提交！',2);
                }
                if($_SESSION['MEMBER']['STATUS']['EMAIL']){
                    $email = '';
                    $info = '邮箱解绑成功，可以绑定新邮箱了。';
                    D('Member')->where('id='.$_SESSION['MEMBER']['ID'])->save(array('user_email'=>''));
                    $_SESSION['MEMBER']['STATUS']['EMAIL'] = 0;
                }else{
                    $email = $_POST['email'];
                    $info = '邮箱绑定成功，使用绑定邮箱可以找回密码。';
                    $_SESSION['MEMBER']['STATUS']['EMAIL'] = 1;
                }
                D('Member')->where('id='.$_SESSION['MEMBER']['ID'])->save(array('user_email'=>$email));
                D('MemberStatus')->where('uid='.$_SESSION['MEMBER']['ID'])->save(array('email_status'=>$_SESSION['MEMBER']['STATUS']['EMAIL']));
                $this->ajaxReturn(null,$info,0);
            }

        }else{
            $this->data = D('Member')->where('id='.$_SESSION['MEMBER']['ID'])->find();
            $this->display();
        }

    }

    public function check($type,$uid=''){
        if($_SESSION['MEMBER']['STATUS'][strtoupper($type)]==1){
            return true;
        }else{
            $uid = $uid=='' ? $_SESSION['MEMBER']['ID'] : $uid;
            $field = $type == 'idcard' ? 'id' : $type;
            $data = D('MemberStatus')->field($field.'_status `status`')->where('uid='.$uid)->find();
            if($data['status']=='1'){
                $_SESSION['MEMBER']['STATUS'][strtoupper($type)]=1;
            }
            return intval($data['status']);
        }

    }
}