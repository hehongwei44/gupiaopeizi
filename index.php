<?php
    function is_mobile() {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $mobile_agents = Array("240x320","acer","acoon","acs-","abacho","ahong","airness","alcatel","amoi","android","applewebkit/525","applewebkit/532","asus","audio","au-mic","avantogo","becker","benq","bilbo","bird","blackberry","blazer","bleu","cdm-","compal","coolpad","danger","dbtel","dopod","elaine","eric","etouch","fly ","fly_","fly-","go.web","goodaccess","gradiente","grundig","haier","hedy","hitachi","htc","huawei","hutchison","inno","ipad","ipaq","ipod","jbrowser","kddi","kgt","kwc","lenovo","lg ","lg2","lg3","lg4","lg5","lg7","lg8","lg9","lg-","lge-","lge9","longcos","maemo","mercator","meridian","micromax","midp","mini","mitsu","mmm","mmp","mobi","mot-","moto","nec-","netfront","newgen","nexian","nf-browser","nintendo","nitro","nokia","nook","novarra","obigo","palm","panasonic","pantech","philips","phone","pg-","playstation","pocket","pt-","qc-","qtek","rover","sagem","sama","samu","sanyo","samsung","sch-","scooter","sec-","sendo","sgh-","sharp","siemens","sie-","softbank","sony","spice","sprint","spv","symbian","tablet","talkabout","tcl-","teleca","telit","tianyu","tim-","toshiba","tsm","up.browser","utec","utstar","verykool","virgin","vk-","voda","voxtel","vx","wap","wellco","wig browser","wii","windows ce","wireless","xda","xde","zte");
        $is_mobile = false;
        foreach ($mobile_agents as $device) {
            if (stristr($user_agent, $device)) {
                $is_mobile = true;
                break;
            }
        }
        return $is_mobile;
    }


    /*
    if(is_mobile() && !(strtolower($url[1])=='m' or strtolower($url[1])=='m.html')){
        Header("HTTP/1.1 301 Moved Permanently");
        Header("Location: http://".$_SERVER['SERVER_NAME'].'/m.html');
    }


    $sd = explode('.',$_SERVER['HTTP_HOST']);
    if(!in_array($sd[0],array('','www'))&&count($sd)>=3){
        $_GET['t'] = $sd[0];
    }else{
        //$_GET['t'] = 'default';
    }
*/

    if($_GET['k']){
        //header('location:/member/register.html?invitor='.$_GET['k']);
		session_start();
		$_SESSION['MEMBER']['FRIENDCODE']=$_GET['k'];
		//echo $_COOKIE['FRIENDCODE'];
        //exit;
    }
    define('APP_DEBUG',false);
    define('APP_NAME','YNW');
    define('APP_ROOT',dirname(__FILE__));
    define('APP_PATH',APP_ROOT.'/');
    define('THINK_PATH',APP_PATH.'base/');
    define('CONF_PATH',APP_PATH.'conf/');
    define('LIB_PATH',APP_PATH.'lib/');
    define('COMMON_PATH',APP_PATH.'common/');
    define('HTML_PATH',APP_PATH.'data/html/');
    define('RUNTIME_PATH',APP_PATH.'data/runtime/');
    define('APP_PUBLIC_PATH',APP_PATH.'.public/');
    define('TMPL_PATH',APP_PATH.'tpl/');
	define('BUILD_DIR_SECURE',true);
	define('DIR_SECURE_FILENAME', 'index.html');
	define('DIR_SECURE_CONTENT', 'Deney Access!');
    ini_set("session.gc_maxlifetime", "180");
    require(THINK_PATH.'Core.php');
?>