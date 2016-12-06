<?php
class MessageAction extends AdminAction{
    public function index(){
		$msgconfig = FS("data/conf/message");
		$type = $msgconfig['sms']['type'];// type=0 吉信通短信接口   type=1 漫道短信接口
		$uid1=$msgconfig['sms']['user1']; //分配给你的账号
		$pwd1=$msgconfig['sms']['pass1']; //密码 
		
		$uid2=$msgconfig['sms']['user2']; //分配给你的账号
		$pwd2=$msgconfig['sms']['pass2']; //密码 
		
		$uid3=$msgconfig['sms']['user3']; //分配给你的账号
		$pwd3=$msgconfig['sms']['pass3']; //密码 
		if($type==0){
			$d = @file_get_contents("http://service.winic.org:8009/webservice/public/remoney.asp?uid={$uid1}&pwd={$pwd1}",false);
			if($d<0) $d="用户名或密码错误";
			else $d = "￥".$d;
			$this->assign('winic',$d);
		}else if($type==1){
			$d=@file_get_contents("http://sdk2.zucp.net:8060/webservice.asmx/balance?sn={$uid2}&pwd={$pwd2}",false);
			preg_match('/<string.*?>(.*?)<\/string>/', $d, $matches);
			
			if($matches[1]<0){ 
				switch($matches[1]){
					case -2:
						$d="帐号/密码不正确或者序列号未注册";
					break;
					case -4:
						$d="余额不足";
					break;
					case -6:
						$d="参数有误";
					break;
					case -7:
						$d="权限受限,该序列号是否已经开通了调用该方法的权限";
					break;
					case -12:
						$d="序列号状态错误，请确认序列号是否被禁用";
					break;
					default:
						$d="用户名或密码错误";
					break;
				}
			}else{
				$d = $d."条";
			}
			$this->assign('zucp',$d);
		}else{
			$d = @file_get_contents("http://sdk229ws.eucp.b2m.cn:8080/sdkproxy/querybalance.action?cdkey={$uid3}&password={$pwd3}",false);
			preg_match_all('/<response>(.*)<\/response>/isU',$d,$arr);
			foreach($arr[1] as $k=>$v){
				preg_match_all('#<message>(.*)</message>#isU',$v,$ar[$k]);
				$data[]=$ar[$k][1];
			}
			
			$d = $data[0][0]*10;
			if($d<0) $d="用户名或密码错误";
			else $d = $d."条";
			$this->assign('emay',$d);
		}
		
		$this->assign('stmp_config',$msgconfig['stmp']);
		$this->assign('sms_config',$msgconfig['sms']);
		$this->assign('sms_config_type',$msgconfig['sms']['type']);
		$this->assign('baidu_config',$msgconfig['baidu']);
		$this->assign("type_list", array("3"=>'关闭短信平台服务',"1"=>'漫道短信提供商',"2"=>'亿美软通短信提供商',"0"=>'吉信通短信提供商'));
        $this->display();
    }
    public function save(){	$status = $_POST['msg']['sms']['type'];
		if($status=='1'){
			$pwd = $_POST['msg']['sms']['user2'].$_POST['msg']['sms']['pwd'];
			$_POST['msg']['sms']['pass2'] =strtoupper(md5($pwd));//$pwd
			$_POST['msg']['sms']['pwd'] = $_POST['msg']['sms']['pwd'];
		}
		
		FS("message",$_POST['msg'],"data/conf/");
		alogs("Message",0,1,'成功执行了通知信息接口的编辑操作！');//管理员操作日志
		$this->success("操作成功",__URL__."/index/");
    }
	
	
    public function templet()
    {
		$emailTxt = FS("data/conf/tpl/email");
		$smsTxt = FS("data/conf/tpl/sms");
		$msgTxt = FS("data/conf/tpl/message");

		$this->assign('emailTxt',de_xie($emailTxt));
		$this->assign('smsTxt',de_xie($smsTxt));
		$this->assign('msgTxt',de_xie($msgTxt));
        $this->display();
    }
	
    public function templetsave()
    {
		FS("email",$_POST['email'],"data/conf/tpl/");
		FS("sms",$_POST['sms'],"data/conf/tpl/");
		FS("message",$_POST['msg'],"data/conf/tpl/");
		alogs("Message",0,1,'成功执行了通知信息模板的编辑操作！');//管理员操作日志
		$this->success("操作成功",__URL__."/templet/");
    }
}
?>