<?php
require COMMON_PATH."business.php";


//////////////////////////////////// 第三方支付--移动支付专用 开始 fan 2014-06-07 ////////////////////////////
//* 移动支付使用该方法
//获取客户端ip地址
//注意:如果你想要把ip记录到服务器上,请在写库时先检查一下ip的数据是否安全.
//*
function getIp() {
    if (getenv('HTTP_CLIENT_IP')) {
			$ip = getenv('HTTP_CLIENT_IP'); 
	}
	elseif (getenv('HTTP_X_FORWARDED_FOR')) { //获取客户端用代理服务器访问时的真实ip 地址
			$ip = getenv('HTTP_X_FORWARDED_FOR');
	}
	elseif (getenv('HTTP_X_FORWARDED')) { 
			$ip = getenv('HTTP_X_FORWARDED');
	}
	elseif (getenv('HTTP_FORWARDED_FOR')) {
			$ip = getenv('HTTP_FORWARDED_FOR'); 
	}
	elseif (getenv('HTTP_FORWARDED')) {
			$ip = getenv('HTTP_FORWARDED');
	}
	else if(!empty($_SERVER["REMOTE_ADDR"])){
			$cip = $_SERVER["REMOTE_ADDR"];  
	}else{
			$cip = "unknown";  
	}
	return $ip;
}

//移动支付MD5方式签名
function MD5sign($okey,$odata){
	$signdata=hmac("",$odata);			     
	return hmac($okey,$signdata);
}

function hmac ($key, $data){
  $key = iconv('gb2312', 'utf-8', $key);
  $data = iconv('gb2312', 'utf-8', $data);
  $b = 64;
  if (strlen($key) > $b) {
  		$key = pack("H*",md5($key));
  }
  $key = str_pad($key, $b, chr(0x00));
  $ipad = str_pad('', $b, chr(0x36));
  $opad = str_pad('', $b, chr(0x5c));
  $k_ipad = $key ^ $ipad ;
  $k_opad = $key ^ $opad;
  return md5($k_opad . pack("H*",md5($k_ipad . $data)));
} 
//////////////////////////////////// 第三方支付--移动支付专用 结束 fan 2014-06-07 ////////////////////////////	 


function cutstr($str, $start=0, $length, $suffix=true, $charset="utf-8") {
    if(function_exists("mb_substr"))
        $slice = mb_substr($str, $start, $length, $charset);
    elseif(function_exists('iconv_substr')) {
        $slice = iconv_substr($str,$start,$length,$charset);
    }else{
        $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("",array_slice($match[0], $start, $length));
    }
    return $suffix ? $slice.'...' : $slice;
}

function mask($str){
	if(strlen($str)<5){
		return substr($str,0,1).'**'.substr($str,-1);
	}elseif(strlen($str)>=5 && strlen($str)<8){
		return substr($str,0,2).'***'.substr($str,-2);
	}elseif(strlen($str)>=8 && strlen($str)<12){
		return substr($str,0,3).'*****'.substr($str,-3);
	}else{
		return substr($str,0,4).'*******'.substr($str,-4);
	}
}

// 自动转换字符集 支持数组转换
function auto_charset($fContents, $from='gbk', $to='utf-8') {
    $from = strtoupper($from) == 'UTF8' ? 'utf-8' : $from;
    $to = strtoupper($to) == 'UTF8' ? 'utf-8' : $to;
    if ( ($to=='utf-8'&&is_utf8($fContents)) || strtoupper($from) === strtoupper($to) || empty($fContents) || (is_scalar($fContents) && !is_string($fContents))) {
        //如果编码相同或者非字符串标量则不转换
        return $fContents;
    }
    if (is_string($fContents)) {
        if (function_exists('mb_convert_encoding')) {
            return mb_convert_encoding($fContents, $to, $from);
        } elseif (function_exists('iconv')) {
            return iconv($from, $to, $fContents);
        } else {
            return $fContents;
        }
    } elseif (is_array($fContents)) {
        foreach ($fContents as $key => $val) {
            $_key = auto_charset($key, $from, $to);
            $fContents[$_key] = auto_charset($val, $from, $to);
            if ($key != $_key)
                unset($fContents[$key]);
        }
        return $fContents;
    }
    else {
        return $fContents;
    }
}

//判断是否utf8
function is_utf8($string) {
	return preg_match('%^(?:
		 [\x09\x0A\x0D\x20-\x7E]            # ASCII
	   | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
	   |  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
	   | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
	   |  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
	   |  \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
	   | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
	   |  \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
   )*$%xs', $string);
}

//获取日期
/*			
case "yesterday";
	$date = date("Y-m-d",$now_time);//d,w,m分别表示天，周，月,后面的第三个参数选填，正数1表示后一天(d)的00:00:00到23:59:59负数表示前一天(d),-2表示前面第二天的00:00:00到23:59:59
	$day = get_date($date,'d',-1);//第三个参数表示时间段包含的天数
break;
*/
function get_date($date,$t='d',$n=0){
	if($t=='d'){
		$firstday = date('Y-m-d 00:00:00',strtotime("$n day"));
		$lastday = date("Y-m-d 23:59:59",strtotime("$n day"));
	}elseif($t=='w'){
		if($n!=0){$date = date('Y-m-d',strtotime("$n week"));}
		$lastday = date("Y-m-d 00:00:00",strtotime("$date Sunday"));
		$firstday = date("Y-m-d 23:59:59",strtotime("$lastday -6 days"));
	}elseif($t=='m'){
		if($n!=0){
			if(date("m",time())==1) $date = date('Y-m-d',strtotime("$n months -1 day"));//2特殊的2月份
			else $date = date('Y-m-d',strtotime("$n months"));
		}
		
		$firstday = date("Y-m-01 00:00:00",strtotime($date));
		$lastday = date("Y-m-d 23:59:59",strtotime("$firstday +1 month -1 day"));
	}
	return array($firstday,$lastday);

}

function rand_string($ukey="",$len=6,$type='1',$utype='1',$addChars='') {
    $str ='';
    switch($type) {
        case 0:
            $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'.$addChars;
            break;
        case 1:
            $chars= str_repeat('0123456789',3);
            break;
        case 2:
            $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZ'.$addChars;
            break;
        case 3:
            $chars='abcdefghijklmnopqrstuvwxyz'.$addChars;
            break;
        default :
            // 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
            $chars='ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789'.$addChars;
            break;
    }
    if($len>10 ) {//位数过长重复字符串一定次数
        $chars= $type==1? str_repeat($chars,$len) : str_repeat($chars,5);
    }
    $chars   =   str_shuffle($chars);
    $str     =   substr($chars,0,$len);
	if(!empty($ukey)){
		$vd['code'] = $str;
		$vd['send_time'] = time();
		$vd['ukey'] = $ukey;
		$vd['type'] = $utype;
		M('verify')->add($vd);
	}
    return $str;
}

/****************************
/*  手机短信接口（整合吉信通www.winic.org、漫道短信www.zucp.net和亿美短信www.zucp.net）
/* 参数：$mob  		手机号码
/*		$content   	短信内容 
*****************************/
function sendsms($mob,$content){
    $msgconfig = FS("data/conf/message");
    $type = $msgconfig['sms']['type'];// type=0 吉信通短信接口   type=1 漫道短信接口   type=2 亿美短信接口 
    if($type==0){	
          $uid=$msgconfig['sms']['user1']; //分配给你的账号
          $pwd=$msgconfig['sms']['pass1']; //密码
          $mob=$mob; //发送号码用逗号分隔
          if(PATH_SEPARATOR==':'){//如果是Linux系统，则执行linux短息接口
                $url="http://service.winic.org:8009/sys_port/gateway/?id=%s&pwd=%s&to=%s&content=%s&time=";
                $id = urlencode($uid);
                $pwd = urlencode($pwd);
                $to = urlencode($mob);    
                $content = iconv("UTF-8","GB2312",$content); 
                $rurl = sprintf($url, $id, $pwd, $to, $content);
                
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_URL,$rurl);
                curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
                $result = curl_exec($ch);
                curl_close($ch);
                $status = substr($result, 0,3);

                if($status==="000"){
                      return true;
                }else{
                      return false;
                }
          
          }else{
                $content=urlencode(auto_charset($content,"utf-8",'gbk'));  //短信内容
                $sendurl="http://service.winic.org:8009/sys_port/gateway/?";
                $sdata="id=".$uid."&pwd=".$pwd."&to=".$mob."&content=".$content."&time=";
                
                $xhr=new COM("MSXML2.XMLHTTP");   
                $xhr->open("POST",$sendurl,false);
                $xhr->setRequestHeader ("Content-type:","text/xml;charset=GB2312");
                $xhr->setRequestHeader ("Content-Type","application/x-www-form-urlencoded");
                $xhr->send($sdata);   
                $data = explode("/",$xhr->responseText);
                if($data[0]=="000"){
                      return true;
                }else{
                    return false;  
                }                         
          }
    }elseif($type==1){
          /////////////////////////////////////////漫道短信接口 开始///////////////////////////////////////////////////////////// 
          //如果您的系统是utf-8,请转成GB2312 后，再提交、
          $flag = 0; 
          //要post的数据 
          $argv = array( 
          'sn'=>$msgconfig['sms']['user2'], ////替换成您自己的序列号
          'pwd'=>$msgconfig['sms']['pass2'], //此处密码需要加密 加密方式为 md5(sn+password) 32位大写
          
          'mobile'=>$mob,//手机号 多个用英文的逗号隔开 post理论没有长度限制.推荐群发一次小于等于10000个手机号
          'content'=>iconv( "UTF-8", "gb2312//IGNORE" ,$content),//短信内容
          'ext'=>'',		
          'stime'=>'',//定时时间 格式为2011-6-29 11:09:21
          'rrid'=>''
          ); 
          //构造要post的字符串 
          foreach ($argv as $key=>$value) { 
                if ($flag!=0) { 
                      $params .= "&"; 
                      $flag = 1; 
                } 
                $params.= $key."="; $params.= urlencode($value); 
                $flag = 1; 
          } 
          $length = strlen($params); 
          //创建socket连接 
          $fp = fsockopen("sdk2.zucp.net",8060,$errno,$errstr,10) or exit($errstr."--->".$errno); 
          //构造post请求的头 
          $header = "POST /webservice.asmx/mt HTTP/1.1\r\n"; 
          $header .= "Host:sdk2.zucp.net\r\n"; 
          $header .= "Content-Type: application/x-www-form-urlencoded\r\n"; 
          $header .= "Content-Length: ".$length."\r\n"; 
          $header .= "Connection: Close\r\n\r\n"; 
          //添加post的字符串 
          $header .= $params."\r\n"; 
          //发送post的数据 
          fputs($fp,$header); 
          $inheader = 1; 
          while (!feof($fp)) { 
                $line = fgets($fp,1024); //去除请求包的头只显示页面的返回数据 
                if ($inheader && ($line == "\n" || $line == "\r\n")) { 
                      $inheader = 0; 
                } 
                if ($inheader == 0) { 
                // echo $line; 
                } 
          } 
          $line=str_replace("<string xmlns=\"http://tempuri.org/\">","",$line);
          $line=str_replace("</string>","",$line);
          $result=explode("-",$line);
          if(count($result)>1){
                return false;
          }else{
                return true;
          }
    /////////////////////////////////////////漫道短信接口 结束///////////////////////////////////////////////////////////// 
    }elseif($type==2){
          ////////////////////////////////////////////////////////亿美短信接口 开始/////////////////////////////////////////////
          $uid=$msgconfig['sms']['user3']; //分配给你的账号
          $pwd=$msgconfig['sms']['pass3']; //密码
          $mob=$mob; //发送号码用逗号分隔
          $content=urlencode(auto_charset($content,"utf-8",'gbk'));  //短信内容
          
          $sendurl="http://sdk229ws.eucp.b2m.cn:8080/sdkproxy/sendsms.action?";
          $sendurl.='cdkey='.$serialNumber.'&password='.$pwd.'&phone='.$mob.'&message='.$content.'&addserial=';
          
          $d = @file_get_contents($sendurl,false);
          
          preg_match_all('/<response>(.*)<\/response>/isU',$d,$arr);
          
          foreach($arr[1] as $k=>$v){
                preg_match_all('# <error>(.*)</error> #isU',$v,$ar[$k]);
                $data[]=$ar[$k][1];
          }
          
          if($data[0][0]=="0"){
                return true;
          }else{
                return false;
          } 
    ////////////////////////////////////////////////////////亿美短信接口 结束/////////////////////////////////////////////
    }else{
          return false;
    }
}
      

//验证是否通过
function is_verify($uid,$code,$utype,$timespan){
	if(!empty($uid)) $vd['ukey'] = $uid;
	$vd['type'] = $utype;
	$vd['send_time'] = array("lt",time()+$timespan);
	$vd['code'] = $code;
	$vo = M("verify")->field('ukey')->where($vd)->find();
	if(is_array($vo)) return $vo['ukey'];
	else return false;
}


//删除文件夹并重建文件夹
function rmdirr($dirname) {

	if (!file_exists($dirname)) {
		return false;
	}

	if (is_file($dirname) || is_link($dirname)) {
		return unlink($dirname);
	}

	$dir = dir($dirname);

	while (false !== $entry = $dir->read()) {

		if ($entry == '.' || $entry == '..') {
			continue;
		}

		rmdirr($dirname . DIRECTORY_SEPARATOR . $entry);
	}

	$dir->close();

	return rmdir($dirname);
}

//删除文件夹及文件夹下所有内容
function Rmall($dirname) {
	if (!file_exists($dirname)) {
		return false;
	}
	if (is_file($dirname) || is_link($dirname)) {
		return unlink($dirname);
	}

	$dir = dir($dirname);//如果对像是目录

	while (false !== $file = $dir->read()) {

		if ($file == '.' || $file == '..') {
			continue;
		}
		if(!is_dir($dirname."/".$file)){
			unlink($dirname."/".$file);
		}else{
			Rmall($dirname."/".$file);
		}
		
		rmdir($dirname."/".$file);
	}

	$dir->close();
	
	rmdir($dirname);

	return true;
}

//取得文件内容
function ReadFiletext($filepath){
	$htmlfp=@fopen($filepath,"r");
	while($data=@fread($htmlfp,1000))
	{
		$string.=$data;
	}
	@fclose($htmlfp);
	return $string;
}

//生成文件
function MakeFile($con,$filename){//$filename是全物理路径加文件名
	MakeDir(dirname($filename));
	$fp=fopen($filename,"w");
	fwrite($fp,$con);
	fclose($fp);
}

//生成全路径文件夹
function MakeDir($dir){
	return is_dir($dir) or (MakeDir(dirname($dir)) and mkdir($dir,0777));
}

function getLeftTime($timeend,$type=1){
	if($type==1){
		$timeend = strtotime(date("Y-m-d",$timeend)." 23:59:59");
		$timenow = strtotime(date("Y-m-d",time())." 23:59:59");
		$left = ceil( ($timeend-$timenow)/3600/24 );
	}else{
		$left_arr = timediff(time(),$timeend);
		$left = $left_arr['day']."天 ".$left_arr['hour']."小时 ".$left_arr['min']."分钟 ".$left_arr['sec']."秒";
	}
	return $left;
}

function timediff($begin_time,$end_time )
{
    if ( $begin_time < $end_time ) {
        $starttime = $begin_time;
        $endtime = $end_time;
    } else {
        $starttime = $end_time;
        $endtime = $begin_time;
    }
    $timediff = $endtime - $starttime;
    $days = intval( $timediff / 86400 );
    $remain = $timediff % 86400;
    $hours = intval( $remain / 3600 );
    $remain = $remain % 3600;
    $mins = intval( $remain / 60 );
    $secs = $remain % 60;
    $res = array( "day" => $days, "hour" => $hours, "min" => $mins, "sec" => $secs );
    return $res;
}

//获取远程图片
function get_remote_img($content){
	$rt = C("WEB_ROOT");
	$img_dir = C("REMOTE_IMGDIR")?C("REMOTE_IMGDIR"):"/files/remote";//img_dir远程图片的保存目录，带前"/"不带后"/"
	$base_dir = substr($rt,0,strlen($rt)-1);//$base_dir网站根目录物理路径，不带后"/"
	
	$content = stripslashes($content); 
	$img_array = array(); 
	preg_match_all("/(src|SRC)=[\"|'| ]{0,}(http:\/\/(.*)\.(gif|jpg|jpeg|bmp|png|ico))/isU",$content,$img_array); //获取内容中的远程图片
	$img_array = array_unique($img_array[2]); //把重复的图片去掉
	set_time_limit(0); 
	$imgUrl = $img_dir."/".strftime("%Y%m%d",time()); //img_dir远程图片的保存目录，带前"/"不带后"/"
	$imgPath = $base_dir.$imgUrl; //$base_dir网站根目录物理路径，不带后"/"
	$milliSecond = strftime("%H%M%S",time()); 
	if(!is_dir($imgPath)) MakeDir($imgPath,0777);//如果路径不存在则创建
	foreach($img_array as $key =>$value) 
	{ 
		$value = trim($value); 
		$get_file = @file_get_contents($value); 
		$rndFileName = $imgPath."/".$milliSecond.$key.".".substr($value,-3,3); 
		$fileurl = $imgUrl."/".$milliSecond.$key.".".substr($value,-3,3); 

		if($get_file) 
		{ 
			$fp = @fopen($rndFileName,"w"); 
			@fwrite($fp,$get_file); 
			@fclose($fp); 
		} 
		$content = ereg_replace($value,$fileurl,$content); 
	} 
	//$content = addslashes($content); 
	return $content;
}

//把秒换成小时或者天数
function second2string($second,$type=0){
	$day = floor($second/(3600*24));
	$second = $second%(3600*24);//除去整天之后剩余的时间
	$hour = floor($second/3600);
	$second = $second%3600;//除去整小时之后剩余的时间 
	$minute = floor($second/60);
	$second = $second%60;//除去整分钟之后剩余的时间 
	
	switch($type){
		case 0:
			if($day>=1) $res = $day."天";
			elseif($hour>=1) $res = $hour."小时";
			else  $res = $minute."分钟";
		break;
		case 1:
			if($day>=5) $res = date("Y-m-d H:i",time()+$second);
			elseif($day>=1&&$day<5) $res = $day."天前";
			elseif($hour>=1) $res = $hour."小时前";
			else  $res = $minute."分钟前";
		break;
	}
	//返回字符串
	return $res;
}

//对提交的参数进行过滤
function EnHtml($v){
	return $v;
}

function mydate($format,$time,$default=''){
	if(intval($time)>10000) return date($format,$time);	
	else return $default;
}

function textPost($data){
	if(is_array($data)){
		foreach($data as $key => $v){
			$x[$key]=text($v);
		}
	}
	return $x;
}


/*$url：要生成的地址,$vars:参数数组,$domain：是否带域名*/
function MU($url,$type,$vars=array(),$domain=false){
	//获得基础地址START
	$path = explode("/",trim($url,"/"));
	$model = strtolower($path[1]);
	$action = isset($path[2])?strtolower($path[2]):"";
	//获得基础地址START
	//获取前缀根目录及分组
	$http = UD($path,$domain);
	//获取前缀根目录及分组
	switch($type){
		case "article":
		default:
			if(!isset($vars['id'])){//特殊栏目,用nid来区分,不用ID
				unset($path[0]);//去掉分组名
				$url = implode("/",$path)."/";
				$newurl=$url;
			}else{//普通栏目,带ID
				if(1==1||strtolower(GROUP_NAME) == strtolower(C('DEFAULT_GROUP'))) {//如果是默认分组则去掉分组名
					unset($path[0]);//去掉分组名
					$url = implode("/",$path)."/";
				}
				$newurl=$url.$vars['id'].$vars['suffix'];
			}
		break;
		case "typelist":
				if(1==1||strtolower(GROUP_NAME) == strtolower(C('DEFAULT_GROUP'))) {//如果是默认分组则去掉分组名
					unset($path[0]);//去掉分组名
					$url = implode("/",$path);
				}
				$newurl=$url.$vars['suffix'];
		break;
	}
	
	return $http.$newurl;
	
}
// URL组装 支持不同模式
// 格式：UD('url参数array('分组','model','action')','显示域名')在传入的url数组中，只用到分组
function UD($url=array(),$domain = false) {
    // 解析URL
	$isDomainGroup = true;//当值为true时,不对任何链接加分组前缀,当为false时,自动判断分组及域名等,加前缀
	$isDomainD = false;
	$asdd = C('APP_SUB_DOMAIN_DEPLOY');
	//###########修复START#############，增加对当前分组分配了二级域名的判断,变量给下面用
	if($asdd){
		foreach (C('APP_SUB_DOMAIN_RULES') as $keyr => $ruler) {
			if(strtolower($url[0]."/") == strtolower($ruler[0])){
				$isDomainGroup = true;//分组分配了二级域名
				$isDomainD = true;
				break;
			}
		}
	}

	//#########及默认分组不需要加分组名 都转换成小写来比较，避免在linux上出问题
	if(strtolower(GROUP_NAME) == strtolower(C('DEFAULT_GROUP'))) $isDomainGroup = true;
	//###########修复END#############，增加对当前分组分配了二级域名的判断
    // 解析子域名
    if($domain===true){
        $domain = $_SERVER['HTTP_HOST'];
        if($asdd) { // 开启子域名部署
			//###########修复START#############，增加对没带前缀域名的判断
			$xdomain = explode(".",$_SERVER['HTTP_HOST']);
			if(!isset($xdomain[2])) $ydomain="www.".$_SERVER['HTTP_HOST'];
			else  $ydomain=$_SERVER['HTTP_HOST'];
			//###########修复END#############，增加对没带前缀域名的判断
            $domain = $domain=='localhost'?'localhost':'www'.strstr($ydomain,'.');
            // '子域名'=>array('项目[/分组]');
            foreach (C('APP_SUB_DOMAIN_RULES') as $key => $rule) {
                if(false === strpos($key,'*') && $isDomainD) {
                    $domain = $key.strstr($domain,'.'); // 生成对应子域名
                    $url   =  substr_replace($url,'',0,strlen($rule[0]));
                    break;
                }
            }
        }
    }
	
	if(!$isDomainGroup) $gpurl = __APP__."/".$url[0]."/";
	else $gpurl = __APP__."/";

    if($domain) {
        $url   =  'http://'.$domain.$gpurl;
    }else{
        $url   =  $gpurl;
	}

	return $url;
}

function Mheader($type){
	header("Content-Type:text/html;charset={$type}"); 
}



//网站基本设置
function get_global_setting(){
	$list=array();
	if(!S('global_setting')){
		$list_t = M('global')->field('code,text')->select();
		foreach($list_t as $key => $v){
			$list[$v['code']] = de_xie($v['text']);
		}
		S('global_setting',$list);
		S('global_setting',$list,3600*C('TTXF_TMP_HOUR')); 
	}else{
		$list = S('global_setting');
	}
	
	return $list;
}

/*
栏目相关函数
End
*/
//在前台显示时去掉反斜线,传入数组，最多二维
function de_xie($arr){
	$data=array();
	if(is_array($arr)){
		foreach($arr as $key=>$v){
			if(is_array($v)){
				foreach($v as $skey=>$sv){
					if(is_array($sv)){
							
					}else{
						$v[$skey] = stripslashes($sv);
					}
				}
				$data[$key] = $v;
			}else{
				$data[$key] = stripslashes($v);
			}
		}
	}else{
		$data = stripslashes($arr);
	}
	return $data;
}


//输出纯文本
function text($text,$parseBr=false,$nr=false){
    $text = htmlspecialchars_decode($text);
    $text	=	safe($text,'text');
    if(!$parseBr&&$nr){
        $text	=	str_ireplace(array("\r","\n","\t","&nbsp;"),'',$text);
        $text	=	htmlspecialchars($text,ENT_QUOTES);
    }elseif(!$nr){
        $text	=	htmlspecialchars($text,ENT_QUOTES);
	}else{
        $text	=	htmlspecialchars($text,ENT_QUOTES);
        $text	=	nl2br($text);
    }
    $text	=	trim($text);
    return $text;
}

function safe($text,$type='html',$tagsMethod=true,$attrMethod=true,$xssAuto = 1,$tags=array(),$attr=array(),$tagsBlack=array(),$attrBlack=array()){

    //无标签格式
    $text_tags	=	'';

    //只存在字体样式
    $font_tags	=	'<i><b><u><s><em><strong><font><big><small><sup><sub><bdo><h1><h2><h3><h4><h5><h6>';

    //标题摘要基本格式
    $base_tags	=	$font_tags.'<p><br><hr><a><img><map><area><pre><code><q><blockquote><acronym><cite><ins><del><center><strike>';

    //兼容Form格式
    $form_tags	=	$base_tags.'<form><input><textarea><button><select><optgroup><option><label><fieldset><legend>';

    //内容等允许HTML的格式
    $html_tags	=	$base_tags.'<ul><ol><li><dl><dd><dt><table><caption><td><th><tr><thead><tbody><tfoot><col><colgroup><div><span><object><embed>';

    //专题等全HTML格式
    $all_tags	=	$form_tags.$html_tags.'<!DOCTYPE><html><head><title><body><base><basefont><script><noscript><applet><object><param><style><frame><frameset><noframes><iframe>';

    //过滤标签
    $text	=	strip_tags($text, ${$type.'_tags'} );

        //过滤攻击代码
        if($type!='all'){
            //过滤危险的属性，如：过滤on事件lang js
            while(preg_match('/(<[^><]+) (onclick|onload|unload|onmouseover|onmouseup|onmouseout|onmousedown|onkeydown|onkeypress|onkeyup|onblur|onchange|onfocus|action|background|codebase|dynsrc|lowsrc)([^><]*)/i',$text,$mat)){
                $text	=	str_ireplace($mat[0],$mat[1].$mat[3],$text);
            }
            while(preg_match('/(<[^><]+)(window\.|javascript:|js:|about:|file:|document\.|vbs:|cookie)([^><]*)/i',$text,$mat)){
                $text	=	str_ireplace($mat[0],$mat[1].$mat[3],$text);
            }
        }
        return $text;
}


//输出安全的html
function h($text, $tags = null){
	$text	=	trim($text);
	$text	=	preg_replace('/<!--?.*-->/','',$text);
	//完全过滤注释
	$text	=	preg_replace('/<!--?.*-->/','',$text);
	//完全过滤动态代码
	$text	=	preg_replace('/<\?|\?'.'>/','',$text);
	//完全过滤js
	$text	=	preg_replace('/<script?.*\/script>/','',$text);

	$text	=	str_replace('[','&#091;',$text);
	$text	=	str_replace(']','&#093;',$text);
	$text	=	str_replace('|','&#124;',$text);
	//过滤换行符
	$text	=	preg_replace('/\r?\n/','',$text);
	//br
	$text	=	preg_replace('/<br(\s\/)?'.'>/i','[br]',$text);
	$text	=	preg_replace('/(\[br\]\s*){10,}/i','[br]',$text);
	//过滤危险的属性，如：过滤on事件lang js
	while(preg_match('/(<[^><]+) (lang|on|action|background|codebase|dynsrc|lowsrc)[^><]+/i',$text,$mat)){
		$text=str_replace($mat[0],$mat[1],$text);
	}
	while(preg_match('/(<[^><]+)(window\.|javascript:|js:|about:|file:|document\.|vbs:|cookie)([^><]*)/i',$text,$mat)){
		$text=str_replace($mat[0],$mat[1].$mat[3],$text);
	}
	if(empty($tags)) {
		$tags = 'table|tbody|td|th|tr|i|b|u|strong|img|p|br|div|span|em|ul|ol|li|dl|dd|dt|a|alt|h[1-9]?';
		$tags.= '|object|param|embed';	// 音乐和视频
	}
	//允许的HTML标签
	$text	=	preg_replace('/<(\/?(?:'.$tags.'))( [^><\[\]]*)?>/i','[\1\2]',$text);
	//过滤多余html
	$text	=	preg_replace('/<\/?(html|head|meta|link|base|basefont|body|bgsound|title|style|script|form|iframe|frame|frameset|applet|id|ilayer|layer|name|style|xml)[^><]*>/i','',$text);
	//过滤合法的html标签
	while(preg_match('/<([a-z]+)[^><\[\]]*>[^><]*<\/\1>/i',$text,$mat)){
		$text=str_replace($mat[0],str_replace('>',']',str_replace('<','[',$mat[0])),$text);
	}
	//转换引号
	while(preg_match('/(\[[^\[\]]*=\s*)(\"|\')([^\2\[\]]+)\2([^\[\]]*\])/i',$text,$mat)){
		$text = str_replace($mat[0], $mat[1] . '|' . $mat[3] . '|' . $mat[4],$text);
	}
	//过滤错误的单个引号
	// 修改:2011.05.26 kissy编辑器中表情等会包含空引号, 简单的过滤会导致错误
//	while(preg_match('/\[[^\[\]]*(\"|\')[^\[\]]*\]/i',$text,$mat)){
//		$text=str_replace($mat[0],str_replace($mat[1],'',$mat[0]),$text);
//	}
	//转换其它所有不合法的 < >
	$text	=	str_replace('<','&lt;',$text);
	$text	=	str_replace('>','&gt;',$text);
    $text   =   str_replace('"','&quot;',$text);
    //$text   =   str_replace('\'','&#039;',$text);
	 //反转换
	$text	=	str_replace('[','<',$text);
	$text	=	str_replace(']','>',$text);
	$text	=	str_replace('|','"',$text);
	//过滤多余空格
	$text	=	str_replace('  ',' ',$text);
	return $text;
}
//根据原图片地址得到缩略图地址
function get_thumb_pic($str){
	$path = explode("/",$str);
	$sc = count($path);
	$path[($sc-1)] = "thumb_".$path[($sc-1)];
	return implode("/",$path);
}

/*
* 中文截取，支持gb2312,gbk,utf-8,big5 
*
* @param string $str 要截取的字串
* @param int $start 截取起始位置
* @param int $length 截取长度
* @param string $charset utf-8|gb2312|gbk|big5 编码
* @param $suffix 是否加尾缀
*/
function cnsubstr($str, $length, $start=0, $charset="utf-8", $suffix=true){
	   $str = strip_tags($str);
	   if(function_exists("mb_substr"))
	   {
			   if(mb_strlen($str, $charset) <= $length) return $str;
			   $slice = mb_substr($str, $start, $length, $charset);
	   }
	   else
	   {
			   $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
			   $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
			   $re['gbk']          = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
			   $re['big5']          = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
			   preg_match_all($re[$charset], $str, $match);
			   if(count($match[0]) <= $length) return $str;
			   $slice = join("",array_slice($match[0], $start, $length));
	   }
	   if($suffix) return $slice."…";
	   return $slice;
}

/*
	格式化显示时间
*/
function getLastTimeFormt($time,$type=0){
	if($type==0) $f="m-d H:i"; 
	else if($type==1) $f="Y-m-d H:i";
	$agoTime = time() - $time;
    if ( $agoTime <= 60&&$agoTime >=0 ) {
        return $agoTime.'秒前';
    }elseif( $agoTime <= 3600 && $agoTime > 60 ){
        return intval($agoTime/60) .'分钟前';
    }elseif ( date('d',$time) == date('d',time()) && $agoTime > 3600){
		return '今天 '.date('H:i',$time);
    }elseif( date('d',$time+86400) == date('d',time()) && $agoTime < 172800){
		return '昨天 '.date('H:i',$time);
    }else{
        return date($f,$time);
    }

}

/**
 * 获取指定uid的头像文件规范路径
 * 来源：Ucenter base类的get_avatar方法
 *
 * @param int $uid
 * @param string $size 头像尺寸，可选为'big', 'middle', 'small'
 * @param string $type 类型，可选为real或者virtual
 * @return unknown
 */
function get_avatar($uid, $size = 'middle', $type = '') {
	$size = in_array($size, array('big', 'middle', 'small')) ? $size : 'big';
	$uid = abs(intval($uid));
	$uid = sprintf("%09d", $uid);
	$dir1 = substr($uid, 0, 3);
	$dir2 = substr($uid, 3, 2);
	$dir3 = substr($uid, 5, 2);
	$typeadd = $type == 'real' ? '_real' : '';
	$path = __ROOT__.'/files/avatars/'.$dir1.'/'.$dir2.'/'.$dir3.'/'.substr($uid, -2).$typeadd."_avatar_$size.jpg";
	if(!file_exists(C("WEB_ROOT").$path)) $path = __ROOT__.'/res/header/images/'."noavatar_$size.gif";
	return  $path;
}
/**
 * 获取地区列表，id为键，地区名为值的二维数组
 */
function get_Area_list($id="") {
	$cacheName = "temp_area_list_s";
	if(!S($cacheName)){
		$list = M('area')->getField('id,name');
		S($cacheName,$list,3600*1000000); 
	}else{
		$list = S($cacheName);
	}
	if(!empty($id)) return $list[$id];
	else return $list;
}

/**
 * IP转换成地区
 */
function ip2area($ip="") {
	if(strlen($ip)<6) return;
	import("ORG.Net.IpLocation");
	$Ip = new IpLocation("CoralWry.dat"); 
	$area = $Ip->getlocation($ip);
	$area = auto_charset($area);
	if($area['country']) $res = $area['country'];
	if($area['area']) $res = $res."(".$area['area'].")";
	if(empty($res)) $res = "未知";
	return $res;
}


//快速缓存调用和储存
function FS($filename,$data="",$path=""){
	$path = C("WEB_ROOT").$path;
	if($data==""){
		$f = explode("/",$filename);
		$num = count($f);
		if($num>2){
			$fx = $f;
			array_pop($f);
			$pathe = implode("/",$f);
			$re = F($fx[$num-1],'',$pathe."/");
		}else{
			isset($f[1])?$re = F($f[1],'',C("WEB_ROOT").$f[0]."/"):$re = F($f[0]);
		}
		return $re;
	}else{
		if(!empty($path)) $re = F($filename,$data,$path);
		else $re = F($filename,$data);
	}
}

//格式化URL，只判断域名，前台后台共用，前台生成供判断的URL，后台生成供储存以便对比的URL
function formtUrl($url){
	if(!stristr($url,"http://")) $url = str_replace("http://","",$url);
	
	$fourl = explode("/",$url);
	$domain = get_domain("http://".$fourl[0]);
	$perfix = str_replace($domain,'',$fourl[0]);
	return $perfix.$domain;
}

function get_domain($url){
	$pattern = "/[/w-]+/.(com|net|org|gov|biz|com.tw|com.hk|com.ru|net.tw|net.hk|net.ru|info|cn|com.cn|net.cn|org.cn|gov.cn|mobi|name|sh|ac|la|travel|tm|us|cc|tv|jobs|asia|hn|lc|hk|bz|com.hk|ws|tel|io|tw|ac.cn|bj.cn|sh.cn|tj.cn|cq.cn|he.cn|sx.cn|nm.cn|ln.cn|jl.cn|hl.cn|js.cn|zj.cn|ah.cn|fj.cn|jx.cn|sd.cn|ha.cn|hb.cn|hn.cn|gd.cn|gx.cn|hi.cn|sc.cn|gz.cn|yn.cn|xz.cn|sn.cn|gs.cn|qh.cn|nx.cn|xj.cn|tw.cn|hk.cn|mo.cn|org.hk|is|edu|mil|au|jp|int|kr|de|vc|ag|in|me|edu.cn|co.kr|gd|vg|co.uk|be|sg|it|ro|com.mo)(/.(cn|hk))*/";
	preg_match($pattern, $url, $matches);
	if(count($matches) > 0)
	{
		return $matches[0];
	}else{
		$rs = parse_url($url);
		$main_url = $rs["host"];
		if(!strcmp(long2ip(sprintf("%u",ip2long($main_url))),$main_url))
		{
			return $main_url;
		}else{
			$arr = explode(".",$main_url);
			$count=count($arr);
			$endArr = array("com","net","org");//com.cn net.cn 等情况
			if (in_array($arr[$count-2],$endArr))
			{
				$domain = $arr[$count-3].".".$arr[$count-2].".".$arr[$count-1];
			}else{
				$domain = $arr[$count-2].".".$arr[$count-1];
			}
			return $domain;
		}
	}
} 

function getFloatValue($f,$len){
  return  number_format($f,$len,'.','');   
} 

function getSubSite(){
	$map['is_open'] = 1;
	$list = M("area")->field(true)->where($map)->select();
	$cdomain = explode(".",$_SERVER['HTTP_HOST']);
	$cpx = array_pop($cdomain);
	$doamin = array_pop($cdomain);
	$host = ".".$doamin.".".$cpx;
	foreach($list as $key=>$v){
		$list[$key]['host'] = "http://".$v['domain'].$host;
	}
	return $list;
}

function getBrowser($Agent) {
    $browseragent="";   //浏览器
    $browserversion=""; //浏览器的版本
    if (ereg('MSIE ([0-9].[0-9]{1,2})',$Agent,$version)) {
         $browserversion=$version[1];
         $browseragent="Internet Explorer";
    } else if (ereg( 'Opera/([0-9]{1,2}.[0-9]{1,2})',$Agent,$version)) {
         $browserversion=$version[1];
         $browseragent="Opera";
    } else if (ereg( 'Firefox/([0-9.]{1,5})',$Agent,$version)) {
         $browserversion=$version[1];
         $browseragent="Firefox";
    }else if (ereg( 'Chrome/([0-9.]{1,3})',$Agent,$version)) {
         $browserversion=$version[1];
         $browseragent="Chrome";
    }else if (ereg( 'Safari/([0-9.]{1,3})',$Agent,$version)) {
         $browseragent="Safari";
         $browserversion="";
    }else {
        $browserversion="";
        $browseragent="Unknown";
    }
    return $browseragent." ".$browserversion;
}

function getPlatform($Agent){
    $browserplatform=='';
    if (eregi('win',$Agent) && strpos($Agent, '95')) {
        $browserplatform="Windows 95";
    }elseif (eregi('win 9x',$Agent) && strpos($Agent, '4.90')) {
        $browserplatform="Windows ME";
    }elseif (eregi('win',$Agent) && ereg('98',$Agent)) {
        $browserplatform="Windows 98";
    }elseif (eregi('win',$Agent) && eregi('nt 5.0',$Agent)) {
        $browserplatform="Windows 2000";
    }elseif (eregi('win',$Agent) && eregi('nt 5.1',$Agent)) {
        $browserplatform="Windows XP";
    }elseif (eregi('win',$Agent) && eregi('nt 6.0',$Agent)) {
        $browserplatform="Windows Vista";
    }elseif (eregi('win',$Agent) && eregi('nt 6.1',$Agent)) {
        $browserplatform="Windows 7";
    }elseif (eregi('win',$Agent) && ereg('32',$Agent)) {
        $browserplatform="Windows 32";
    }elseif (eregi('win',$Agent) && eregi('nt',$Agent)) {
        $browserplatform="Windows NT";
    }elseif (eregi('Mac OS',$Agent)) {
        $browserplatform="Mac OS";
    }elseif (eregi('linux',$Agent)) {
        $browserplatform="Linux";
    }elseif (eregi('unix',$Agent)) {
        $browserplatform="Unix";
    }elseif (eregi('sun',$Agent) && eregi('os',$Agent)) {
        $browserplatform="SunOS";
    }elseif (eregi('ibm',$Agent) && eregi('os',$Agent)) {
        $browserplatform="IBM OS/2";
    }elseif (eregi('Mac',$Agent) && eregi('PC',$Agent)) {
        $browserplatform="Macintosh";
    }elseif (eregi('PowerPC',$Agent)) {
        $browserplatform="PowerPC";
    }elseif (eregi('AIX',$Agent)) {
        $browserplatform="AIX";
    }elseif (eregi('HPUX',$Agent)) {
        $browserplatform="HPUX";
    }elseif (eregi('NetBSD',$Agent)) {
        $browserplatform="NetBSD";
    }elseif (eregi('BSD',$Agent)) {
        $browserplatform="BSD";
    }elseif (ereg('OSF1',$Agent)) {
        $browserplatform="OSF1";
    }elseif (ereg('IRIX',$Agent)) {
        $browserplatform="IRIX";
    }elseif (eregi('FreeBSD',$Agent)) {
        $browserplatform="FreeBSD";
    }
    if ($browserplatform=='') {
      $browserplatform = "Unknown"; 
    }
    return $browserplatform;
}

function password($str,$strong=true){ 
    $score = 0; 
     if(preg_match("/[0-9]+/",$str)){ 
        $score ++; 
     } 
     if(preg_match("/[0-9]{3,}/",$str)){ 
        $score ++; 
     } 
     if(preg_match("/[a-z]+/",$str)){ 
        $score ++; 
     } 
     if(preg_match("/[a-z]{3,}/",$str)){ 
        $score ++; 
     } 
     if(preg_match("/[A-Z]+/",$str)){ 
        $score ++; 
     } 
     if(preg_match("/[A-Z]{3,}/",$str)){ 
        $score ++; 
     } 
     if(preg_match("/[_|\-|+|=|*|!|@|#|$|%|^|&|(|)]+/",$str)){ 
        $score += 2; 
     } 
     if(preg_match("/[_|\-|+|=|*|!|@|#|$|%|^|&|(|)]{3,}/",$str)){ 
        $score ++ ; 
     } 
     if(strlen($str) >= 10){ 
        $score ++; 
     } 
     if($strong){
       if($score<4){
          $score = '弱';
       }elseif($score>=4&&$score<7){
          $score = '中';
       }else{
          $score = '强';
       }  
     }

     return $score; 
}
?>