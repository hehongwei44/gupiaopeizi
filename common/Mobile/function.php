<?php
/**
* wap版公共函数库
*/


//获取借款列表
function getBorrowList($parm=array()){
    $map= $parm['map'];
    $orderby= $parm['orderby'];
    
    if($parm['pagesize']){
        //分页处理
        import("ORG.Util.Page");
        $count = M('borrow b')
                ->where($map)->count('b.id');
        $p = new Page($count, $parm['pagesize']);
        $page = $p->show();
        $Lsql = "{$p->firstRow},{$p->listRows}";

        $row['page']['total'] = ceil($count/$parm['pagesize']);
        $row['page']['nowPage'] =  isset($_REQUEST['p'])?$_REQUEST['p']:1; 
        //分页处理
    }else{
        $page="";
        $Lsql="{$parm['limit']}";
    }
    $pre = C('DB_PREFIX');
    $suffix=C("URL_HTML_SUFFIX");
    $field = "b.id,b.borrow_name,b.borrow_type,b.updata,b.reward_type,b.borrow_times,b.borrow_status,b.borrow_money,b.borrow_use,b.repayment_type,b.borrow_interest_rate,b.borrow_duration,b.collect_time,b.add_time,b.province,b.has_borrow,b.has_vouch,b.city,b.area,b.reward_type,b.reward_num,b.password,m.user_name,m.id as uid,m.credits,m.customer_name,b.is_tuijian,b.deadline,b.danbao,b.borrow";
    $list = M('borrow b')->field($field)->join("{$pre}member m ON m.id=b.borrow_uid")->where($map)->order($orderby)->limit($Lsql)->select();
    $areaList = getArea();
    foreach($list as $key=>$v){
        $list[$key]['location'] = $areaList[$v['province']].$areaList[$v['city']];
        $list[$key]['biao'] = $v['borrow_times'];
        $list[$key]['need'] = $v['borrow_money'] - $v['has_borrow'];
        $list[$key]['leftdays'] = getLeftTime($v['collect_time']);
        $list[$key]['progress'] = getFloatValue($v['has_borrow']/$v['borrow_money']*100,2);
        $list[$key]['vouch_progress'] = getFloatValue($v['has_vouch']/$v['borrow_money']*100,2);
        $list[$key]['burl'] = MU("M/invest","invest",array("id"=>$v['id'],"suffix"=>$suffix));
        $img = unserialize($v['updata']);
        $list[$key]['image'] = $img['0']['img'];
    }
    $row['list'] = $list;
    return $row;
}

/**
* 格式化资金数据保持两位小数
* @desc intval $num  // 接受资金数据
*/
function MFormt($num)
{
    return number_format($num,2);
}

/**
* 根据接收到的状态输出状态按钮
* @desc intval $status  // 借款状态
* @return string
* @author 2014-03-25
*/
function borrow_status($borrow_id , $status=0)
{   
    switch($status){
        case 0:
            $href =  '<a  class="tz_bt">等待初审</a> '; 
            break; 
        case 2:
            //$href =  '<a href="/m'.getInvestUrl($borrow_id).'"  class="tz_bt">我要投资</a> '; 
			$href =  '<a href="'.U('M/Invest/detail', array('id'=>$borrow_id)).'"  class="tz_bt">我要投资</a> '; 
            break;
        case 4:
            $href =  '<a  class="tz_bt">等待复审</a> '; 
            break; 
        case 6:
            $href =  '<a  class="tz_btt">还款中</a> '; 
            break;
        default:
            $href =  '<a  class="tz_btt">已结束</a> '; 
    }
    
    return $href;
}

/**
* @param intval $invest_uid // 投资人id  
* @param intval $borrow_id // 借款id
* @param intval $invest_money // 投资金额必须为整数
* @param string $paypass // 支付密码
* @param string $invest_pass='' //投资密码
*/
function checkInvest($invest_uid, $borrow_id, $invest_money, $paypass, $invest_pass='')
{
    $borrow_id = intval($borrow_id);
    $invest_uid = intval($invest_uid);
    if(!$paypass) return(L('please_enter').L('paypass')); 
    if(!$invest_money) return(L('please_enter').L('invest_money'));
    if(!is_numeric($invest_money)) return(L('invest_money').L('only_intval'));
    $vm = getMinfo($invest_uid,'m.pin_pass,mm.account_money,mm.back_money,mm.money_collect');
    
    $pin_pass = $vm['pin_pass'];
    if(md5($paypass) != $pin_pass) return L('paypass').L('error');  // 支付密码错误
    
    if(($vm['account_money']+$vm['back_money'])< $invest_money)
        return L('lack_of_balance');
    
    $borrow = M('borrow')
                ->field('id, borrow_uid, borrow_money, has_borrow, has_vouch, borrow_max,borrow_min, 
                            borrow_type, password, money_collect')
                ->where("id='{$borrow_id}'")
                ->find();
    if(!$borrow){ // 没有读取到借款数据
        return L('error_parameter');
    }
    $need = $borrow['borrow_money'] - $borrow['has_borrow'];
    if($borrow['borrow_uid'] == $invest_uid){// 不能投自己的标
        return L('not_cast_their_borrow');
    }
    if(!empty($borrow['password']) && $borrow['password']!= md5($invest_pass)){ // 定向密码
        return L('error_invest_password');
    }
    
    if($borrow['money_collect'] > 0 && $vm['money_collect'] < $borrow['money_collect']){  // 待收限制
        return L('amount_to_be_received');
    }
    
    if($borrow['borrow_min'] > $invest_money ){ // 最小投资
        return L('not_less_than_min').$borrow['borrow_min'].L('yuan');
    }
	if($invest_money%$borrow['borrow_min']){
		return "投标金额必须为最小投资的整数倍！";
	}
    if(($need - $invest_money) < 0 ){ // 超出了借款资金
        return L('error_max_invest_money').$need.L('yuan');
    }
	
    // 避免最后一笔投资剩余金额小于最小资金导致无法投递，再次最后一笔投资可以大于最大投资
    if($invest_money != $need && ($need-$invest_money) < $borrow['borrow_min']){ 
        return L('full_scale_investment').$need.L('yuan'); 
    }
    if($borrow['borrow_max'] && $need > ($borrow['borrow_min']*2) && $invest_money > $borrow['borrow_max']){
        return L('beyond_invest_max'); 
    }
    return 'TRUE';
}
/**
* @param intval $uid  用户id
* @param flaot $money 提现金额
* @param string $paypass 支付密码
*/
function checkCash($uid, $money, $paypass){   
    $pre = C('DB_PREFIX'); 
    $uid = intval($uid);
    if(!$money && !$paypass){
        die ('数据不完整'); 
    }
    $paypass = md5($paypass);
    $vo = M('member m')
        ->field('mm.account_money,mm.back_money,(mm.account_money+mm.back_money) all_money,m.user_leve,m.time_limit')
        ->join("{$pre}member_account mm on mm.uid = m.id")
        ->where("m.id={$uid} AND m.pin_pass='{$paypass}'")
        ->find();
    if(!is_array($vo)) return '支付密码不正确';
    
    $datag = get_global_setting(); 
    if($vo['all_money']<$money) return "提现额大于帐户余额";
   
    $start = strtotime(date("Y-m-d",time())." 00:00:00");
    $end = strtotime(date("Y-m-d",time())." 23:59:59");
    $wmap['uid'] = $uid;
    $wmap['withdraw_status'] = array("neq",3);
    $wmap['add_time'] = array("between","{$start},{$end}");
    $today_time = M('member_withdraw')->where($wmap)->count('id');
    $today_money = M('member_withdraw')->where($wmap)->sum('withdraw_money');
    
    $tqfee = explode("|",$datag['fee_tqtx']);
    $fee[0] = explode("-",$tqfee[0]);
    $fee[1] = explode("-",$tqfee[1]);
    $fee[2] = explode("-",$tqfee[2]);
    $one_limit = $fee[2][0]*10000;
    $today_limit = $fee[2][1]/$fee[2][0];  
    if($money < 100 || $money > $one_limit) return "单笔提现金额限制为100-{$one_limit}元";
    if($today_time>=$today_limit){
                return "一天最多只能提现{$today_limit}次";
    }
        if(($today_money+$money)>$fee[2][1]*10000){
            return  "单日提现上限为{$fee[2][1]}万元。您今日已经申请提现金额：{$today_money}元,当前申请金额为:{$money}元,已超出单日上限，请您修改申请金额或改日再申请提现";
        }
        $itime = strtotime(date("Y-m", time())."-01 00:00:00").",".strtotime( date( "Y-m-", time()).date("t", time())." 23:59:59");
        $wmapx['uid'] = $uid;
        $wmapx['withdraw_status'] = array("neq",3);
        $wmapx['add_time'] = array("between","{$itime}");
        $times_month = M("member_withdraw")->where($wmapx)->count("id");
        
    
        $tqfee1 = explode("|",$datag['fee_tqtx']);
        $fee1[0] = explode("-",$tqfee1[0]);
        $fee1[1] = explode("-",$tqfee1[1]);
        if(($money-$vo['back_money'])>=0){
            $maxfee1 = ($money-$vo['back_money'])*$fee1[0][0]/1000;
            if($maxfee1>=$fee1[0][1]){
                $maxfee1 = $fee1[0][1];
            }
            
            $maxfee2 = $vo['back_money']*$fee1[1][0]/1000;
            if($maxfee2>=$fee1[1][1]){
                $maxfee2 = $fee1[1][1];
            }
            
            $fee = $maxfee1+$maxfee2;
            $money = $money-$vo['back_money'];
        }else{
            $fee = $vo['back_money']*$fee1[1][0]/1000;
            if($fee>=$fee1[1][1]){
                $fee = $fee1[1][1];
            }
        }
        
        if(($vo['all_money']-$money - $fee)<0 ){
            
            $moneydata['withdraw_money'] = $money;
            $moneydata['withdraw_fee'] = $fee;
            $moneydata['second_fee'] = $fee;
            $moneydata['withdraw_status'] = 0;
            $moneydata['uid'] =$uid;
            $moneydata['add_time'] = time();
            $moneydata['add_ip'] = get_client_ip();
            $newid = M('member_withdraw')->add($moneydata);
            if($newid){
                logMoney($uid,4,-$money,"提现,默认自动扣减手续费".$fee."元",'0','@网站管理员@',0);
                remind('chk6',$uid);
                return 'TRUE';
            } 
            
        }else{
            $moneydata['withdraw_money'] = $money;
            $moneydata['withdraw_fee'] = $fee;
            $moneydata['second_fee'] = $fee;
            $moneydata['withdraw_status'] = 0;
            $moneydata['uid'] =$uid;
            $moneydata['add_time'] = time();
            $moneydata['add_ip'] = get_client_ip();
            $newid = M('member_withdraw')->add($moneydata);
            if($newid){
                logMoney($uid,4,-$money,"提现,默认自动扣减手续费".$fee."元",'0','@网站管理员@');
                remind('chk6',$uid);
                return 'TRUE';
            } 
        }
  
    
    return  '申请失败，请重试';
}


//获取企业直投借款列表
function getTBorrowList($parm =array())
{
    if(empty($parm['map'])) return;
    $map = $parm['map'];
    $orderby = $parm['orderby'];
    if($parm['pagesize'])
    {
        import( "ORG.Util.Page" );
        $count = M("transfer_borrow b")->where($map)->count("b.id");
        $p = new Page($count, $parm['pagesize']);
        $page = $p->show();
        $Lsql = "{$p->firstRow},{$p->listRows}";
    }else{
        $page = "";
        $Lsql = "{$parm['limit']}";
    }
    $pre = C("DB_PREFIX");
    $suffix =C("URL_HTML_SUFFIX");
    $field = "b.id,b.borrow_name,b.borrow_status,b.borrow_money,b.repayment_type,b.min_month,b.transfer_out,b.transfer_back,b.transfer_total,b.per_transfer,b.borrow_interest_rate,b.borrow_duration,b.increase_rate,b.reward_rate,b.deadline,b.is_show,m.province,m.city,m.area,m.user_name,m.id as uid,m.credits,m.customer_name,b.borrow_type,b.b_img,b.add_time,b.collect_day,b.danbao";
$list = M("transfer_borrow b")->field($field)->join("{$pre}member m ON m.id=b.borrow_uid")->where($map)->order($orderby)->limit($Lsql)->select();
    $areaList = getarea();
    foreach($list as $key => $v)
    {
        $list[$key]['location'] = $areaList[$v['province']].$areaList[$v['city']];
        $list[$key]['progress'] = getfloatvalue( $v['transfer_out'] / $v['transfer_total'] * 100, 2);
        $list[$key]['need'] = getfloatvalue(($v['transfer_total'] - $v['transfer_out'])*$v['per_transfer'], 2 );
        $list[$key]['burl'] = MU("Home/invest_transfer", "invest_transfer",array("id" => $v['id'],"suffix" => $suffix));    
        
        $temp=floor(("{$v['collect_day']}"*3600*24-time()+"{$v['add_time']}")/3600/24);
        $list[$key]['leftdays'] = "{$temp}".'天以上';
        $list[$key]['now'] = time();
        if($v['danbao']!=0 ){
            $list[$key]['danbaoid'] = intval($v['danbao']);
            $danbao = M('article')->field('id,title')->where("type_id=7 and id={$v['danbao']}")->find();
            $list[$key]['danbao']=$danbao['title'];//担保机构
        }else{
            $list[$key]['danbao']='暂无担保机构';//担保机构
        }    
    }
    $row = array();
    $row['list'] = $list;
    $row['page'] = $page;
    return $row;
}
?>
