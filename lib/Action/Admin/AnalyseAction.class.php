<?php
class AnalyseAction extends AdminAction{
	function _initialize(){
		if(!method_exists($this, ACTION_NAME)){
			$this->error('该模块下暂时还没有报表统计的内容');
		}		
		parent::_initialize();
	}
    public function borrow(){
    	$table = C('DB_PREFIX').'borrow';
    	$field = 'add_time';
		$Pconfig = FS("data/conf/borrow");
	 	$this->assign("type",C('BORROW_TYPE'));
	 	$this->assign("status",C('BORROW_STATUS'));
	 	$this->assign("repay",C('REPAYMENT_TYPE'));
	 	$this->assign("purpose",$Pconfig['BORROW_USE']);

	 	switch($_GET['tab']){
	 		case 'last':	 			
				$parse = 'd';
				$last = strtotime('-1 month');
	 			$where = 'AND `'.$field.'` BETWEEN '.strtotime(date('Y-m-01 00:00:00',$last)).' AND '.strtotime(date('Y-m-t 23:59:59',$last));
	 			break;
	 		case 'month':
				$parse = 'd';
				$where = 'AND `'.$field.'` BETWEEN '.strtotime(date('Y-m-01 00:00:00')).' AND '.strtotime(date('Y-m-t 23:59:59'));
	 			break;
	 		case 'year':
				$parse = 'm';
	 			$where = 'AND `'.$field.'` BETWEEN '.strtotime(date('Y-01-01 00:00:00')).' AND '.strtotime(date('Y-12-31 23:59:59'));
	 			break;
	 		default:
				$parse = 'm';
	 			break;
	 	}

		$chart['status']['money'] = M()->query('SELECT borrow_status `status`,SUM(borrow_money) total FROM '.$table.' WHERE homs_id=0 '.$where.' GROUP BY borrow_status ORDER BY total DESC');
		$chart['status']['member'] = M()->query('SELECT borrow_status `status`,COUNT(id) total FROM '.$table.' WHERE homs_id=0 '.$where.' GROUP BY borrow_status ORDER BY total DESC');
		$data = M()->query('SELECT FROM_UNIXTIME('.$field.',"%'.$parse.'") `day`,`borrow_status` `status`,COUNT(`id`) `count`,SUM(`borrow_money`) `money`  FROM '.$table.' WHERE homs_id=0 '.$where.' GROUP BY `day`,`borrow_status` ORDER BY `day` ASC');
		foreach($data as $val){
			$chart['status']['sum']['trend'][$val['status']][intval($val['day'])] = $val['money'];
			$chart['status']['count']['trend'][$val['status']][intval($val['day'])] = $val['count'];
		}
		$chart['status']['sum']['trend'] = $this->parse($chart['status']['sum']['trend'],$parse);
		$chart['status']['count']['trend'] = $this->parse($chart['status']['count']['trend'],$parse);

		$chart['type']['money'] = M()->query('SELECT borrow_type `type`,SUM(borrow_money) total FROM '.$table.' WHERE homs_id=0 '.$where.' GROUP BY borrow_type ORDER BY total DESC');
		$chart['type']['member'] = M()->query('SELECT borrow_type `type`,COUNT(id) total FROM '.$table.' WHERE homs_id=0 '.$where.' GROUP BY borrow_type ORDER BY total DESC');
		$data = M()->query('SELECT FROM_UNIXTIME('.$field.',"%'.$parse.'") `day`,`borrow_type` `type`,COUNT(`id`) `count`,SUM(`borrow_money`) `money`  FROM '.$table.' WHERE homs_id=0 '.$where.' GROUP BY `day`,`borrow_type` ORDER BY `day` ASC');
		foreach($data as $val){
			$total += $val['count'];
			$money += $val['money'];
			$chart['type']['sum']['trend'][$val['type']][intval($val['day'])] = $val['money'];
			$chart['type']['count']['trend'][$val['type']][intval($val['day'])] = $val['count'];
		}
		$chart['type']['sum']['trend'] = $this->parse($chart['type']['sum']['trend'],$parse);
		$chart['type']['count']['trend'] = $this->parse($chart['type']['count']['trend'],$parse);
		
		$chart['repay']['money'] = M()->query('SELECT repayment_type `repay`,SUM(borrow_money) total FROM '.$table.' WHERE homs_id=0 '.$where.' GROUP BY repay ORDER BY total DESC');
		$chart['repay']['member'] = M()->query('SELECT repayment_type `repay`,COUNT(id) total FROM '.$table.' WHERE homs_id=0 '.$where.' GROUP BY repay ORDER BY total DESC');
		$data = M()->query('SELECT FROM_UNIXTIME('.$field.',"%'.$parse.'") `day`,`repayment_type` `repay`,COUNT(`id`) `count`,SUM(`borrow_money`) `money`  FROM '.$table.' WHERE homs_id=0 '.$where.' GROUP BY `day`,`repay` ORDER BY `day` ASC');
		foreach($data as $val){
			$chart['repay']['sum']['trend'][$val['repay']][intval($val['day'])] = $val['money'];
			$chart['repay']['count']['trend'][$val['repay']][intval($val['day'])] = $val['count'];
		}
		$chart['repay']['sum']['trend'] = $this->parse($chart['repay']['sum']['trend'],$parse);
		$chart['repay']['count']['trend'] = $this->parse($chart['repay']['count']['trend'],$parse);	

		$chart['purpose']['money'] = M()->query('SELECT borrow_use `purpose`,SUM(borrow_money) total FROM '.$table.' WHERE homs_id=0 '.$where.' GROUP BY purpose ORDER BY total DESC');
		$chart['purpose']['member'] = M()->query('SELECT borrow_use `purpose`,COUNT(id) total FROM '.$table.' WHERE homs_id=0 '.$where.' GROUP BY purpose ORDER BY total DESC');
		$data = M()->query('SELECT FROM_UNIXTIME('.$field.',"%'.$parse.'") `day`,`borrow_use` `purpose`,COUNT(`id`) `count`,SUM(`borrow_money`) `money`  FROM '.$table.' WHERE homs_id=0 '.$where.' GROUP BY `day`,`purpose` ORDER BY `day` ASC');
		foreach($data as $val){
			$chart['purpose']['sum']['trend'][$val['purpose']][intval($val['day'])] = $val['money'];
			$chart['purpose']['count']['trend'][$val['purpose']][intval($val['day'])] = $val['count'];
		}
		$chart['purpose']['sum']['trend'] = $this->parse($chart['purpose']['sum']['trend'],$parse);
		$chart['purpose']['count']['trend'] = $this->parse($chart['purpose']['count']['trend'],$parse);	


		$data = M()->query('SELECT FROM_UNIXTIME('.$field.',"%'.$parse.'") `day`,`borrow_uid` `member`,COUNT(`id`) `count` FROM '.$table.' WHERE homs_id=0 '.$where.' GROUP BY `day`,`member` ORDER BY `day` ASC');
		foreach($data as $val){
			$chart['member']['count']['trend'][$val['purpose']][intval($val['day'])] = $val['count'];
		}
		$chart['member']['count']['trend'] = $this->parse($chart['member']['count']['trend'],$parse);

		$avg = ceil($money/$total);
		$sql = '
			  SELECT  
			  SUM(CASE WHEN borrow_money<'.$this->format(ceil($avg*0.02)).' THEN 1 ELSE 0 END) AS "'.$this->format(ceil($avg*0.02),true).'以下",  
			  SUM(CASE WHEN borrow_money>='.$this->format(ceil($avg*0.02)).' AND borrow_money<'.$this->format(ceil($avg*0.1)).' THEN 1 ELSE 0 END) AS "'.$this->format(ceil($avg*0.02),true).' - '.$this->format(ceil($avg*0.1),true).'",  
			  SUM(CASE WHEN borrow_money>='.$this->format(ceil($avg*0.1)).' AND borrow_money<'.$this->format(ceil($avg*0.3)).' THEN 1 ELSE 0 END) AS "'.$this->format(ceil($avg*0.1),true).' - '.$this->format(ceil($avg*0.3),true).'",  
			  SUM(CASE WHEN borrow_money>='.$this->format(ceil($avg*0.3)).' AND borrow_money<'.$this->format(ceil($avg*0.6)).' THEN 1 ELSE 0 END) AS "'.$this->format(ceil($avg*0.3),true).' - '.$this->format(ceil($avg*0.6),true).'",  
			  SUM(CASE WHEN borrow_money>='.$this->format(ceil($avg*0.6)).' THEN 1 ELSE 0 END) AS "'.$this->format(ceil($avg*0.6),true).'以上"  
			  FROM '.$table.' WHERE homs_id=0 '.$where;
		$chart['borrow']['area'] = M()->query($sql);

		if($parse=='d'){
			$where = 'AND `'.$field.'` BETWEEN '.strtotime(date('Y-m-01 00:00:00',strtotime('-1 month'))).' AND '.strtotime(date('Y-m-t 23:59:59',strtotime('-1 month')));
		}else{
			$where = 'AND `'.$field.'` BETWEEN '.strtotime(date('Y-01-01 00:00:00',strtotime('-1 year'))).' AND '.strtotime(date('Y-12-31 23:59:59',strtotime('-1 year')));
		}	 	
		$data = M()->query('SELECT FROM_UNIXTIME('.$field.',"%'.$parse.'") `day`,`borrow_uid` `member`,COUNT(`id`) `count` FROM '.$table.' WHERE homs_id=0 '.$where.' GROUP BY `day`,`member` ORDER BY `day` ASC');
		foreach($data as $val){
			$chart['member']['count']['last'][$val['purpose']][intval($val['day'])] = $val['count'];
		}
		$chart['member']['count']['last'] = $this->parse($chart['member']['count']['last'],$parse);


		$this->assign('chart',$chart);
        $this->display($tpl);
    }

    public function trade(){
    	$table = C('DB_PREFIX').'borrow';

		$Pconfig = FS("data/conf/borrow");
	 	$this->assign("type",C('TRADE_TYPE'));
	 	$this->assign("status",C('BORROW_STATUS'));

	 	switch($_GET['tab']){
	 		case 'last':	 			
				$parse = 'd';
				$last = strtotime('-1 month');
	 			$where = 'AND `add_time` BETWEEN '.strtotime(date('Y-m-01 00:00:00',$last)).' AND '.strtotime(date('Y-m-t 23:59:59',$last));
	 			break;
	 		case 'month':
				$parse = 'd';
				$where = 'AND `add_time` BETWEEN '.strtotime(date('Y-m-01 00:00:00')).' AND '.strtotime(date('Y-m-t 23:59:59'));
	 			break;
	 		case 'year':
				$parse = 'm';
				$where = 'AND `add_time` BETWEEN '.strtotime(date('Y-01-01 00:00:00')).' AND '.strtotime(date('Y-12-31 23:59:59'));
	 			break;
	 		default:
				$parse = 'm';
	 			break;
	 	}

		$chart['status']['money'] = M()->query('SELECT borrow_status `status`,SUM(borrow_money) total FROM '.$table.' WHERE homs_id>0 '.$where.' GROUP BY borrow_status ORDER BY total DESC');
		$chart['status']['member'] = M()->query('SELECT borrow_status `status`,COUNT(id) total FROM '.$table.' WHERE homs_id>0 '.$where.' GROUP BY borrow_status ORDER BY total DESC');

		$chart['type']['money'] = M()->query('SELECT borrow_type `type`,SUM(borrow_money) total FROM '.$table.' WHERE homs_id>0 '.$where.' GROUP BY borrow_type ORDER BY total DESC');
		$chart['type']['member'] = M()->query('SELECT borrow_type `type`,COUNT(id) total FROM '.$table.' WHERE homs_id>0 '.$where.' GROUP BY borrow_type ORDER BY total DESC');

		$data = M()->query('SELECT FROM_UNIXTIME(deadline,"%'.$parse.'") `day`,`borrow_status` `status`,COUNT(`id`) `count`,SUM(`borrow_money`) `money`  FROM '.$table.' WHERE homs_id>0 '.$where.' GROUP BY `day`,`borrow_status` ORDER BY `day` ASC');
		foreach($data as $val){
			$total += $val['count'];
			$money += $val['money'];
			$chart['status']['sum']['trend'][$val['status']][intval($val['day'])] = $val['money'];
			$chart['status']['count']['trend'][$val['status']][intval($val['day'])] = $val['count'];
		}
		$chart['status']['sum']['trend'] = $this->parse($chart['status']['sum']['trend'],$parse);
		$chart['status']['count']['trend'] = $this->parse($chart['status']['count']['trend'],$parse);

		$data = M()->query('SELECT FROM_UNIXTIME(deadline,"%'.$parse.'") `day`,`borrow_type` `type`,COUNT(`id`) `count`,SUM(`borrow_money`) `money`  FROM '.$table.' WHERE homs_id>0 '.$where.' GROUP BY `day`,`borrow_type` ORDER BY `day` ASC');
		foreach($data as $val){
			$chart['type']['sum']['trend'][$val['type']][intval($val['day'])] = $val['money'];
			$chart['type']['count']['trend'][$val['type']][intval($val['day'])] = $val['count'];
		}
		$chart['type']['sum']['trend'] = $this->parse($chart['type']['sum']['trend'],$parse);
		$chart['type']['count']['trend'] = $this->parse($chart['type']['count']['trend'],$parse);

		$avg = ceil($money/$total);
		$sql = '
			  SELECT  
			  SUM(CASE WHEN borrow_money<'.$this->format(ceil($avg*0.02)).' THEN 1 ELSE 0 END) AS "'.$this->format(ceil($avg*0.02),true).'以下",  
			  SUM(CASE WHEN borrow_money>='.$this->format(ceil($avg*0.02)).' AND borrow_money<'.$this->format(ceil($avg*0.1),true).' THEN 1 ELSE 0 END) AS "'.$this->format(ceil($avg*0.02),true).' - '.$this->format(ceil($avg*0.1),true).'",  
			  SUM(CASE WHEN borrow_money>='.$this->format(ceil($avg*0.1)).' AND borrow_money<'.$this->format(ceil($avg*0.3),true).' THEN 1 ELSE 0 END) AS "'.$this->format(ceil($avg*0.1),true).' - '.$this->format(ceil($avg*0.3),true).'",  
			  SUM(CASE WHEN borrow_money>='.$this->format(ceil($avg*0.3)).' AND borrow_money<'.$this->format(ceil($avg*0.6),true).' THEN 1 ELSE 0 END) AS "'.$this->format(ceil($avg*0.3),true).' - '.$this->format(ceil($avg*0.6),true).'",  
			  SUM(CASE WHEN borrow_money>='.$this->format(ceil($avg*0.6)).' THEN 1 ELSE 0 END) AS "'.$this->format(ceil($avg*0.6),true).'以上"  
			  FROM '.$table.' WHERE homs_id>0 '.$where;
		$chart['trade']['area'] = M()->query($sql);

		$this->assign('chart',$chart);
        $this->display();
    }

    public function profit(){
    	$table = C('DB_PREFIX').'investor';

	 	switch($_GET['tab']){
	 		case 'last':	 			
				$parse = 'd';
				$last = strtotime('-1 month');
	 			$where = 'AND `repayment_time` BETWEEN '.strtotime(date('Y-m-01 00:00:00',$last)).' AND '.strtotime(date('Y-m-t 23:59:59',$last));
	 			break;
	 		case 'month':
				$parse = 'd';
				$where = 'AND `repayment_time` BETWEEN '.strtotime(date('Y-m-01 00:00:00')).' AND '.strtotime(date('Y-m-t 23:59:59'));
	 			break;
	 		case 'year':
				$parse = 'm';
				$where = 'AND `repayment_time` BETWEEN '.strtotime(date('Y-01-01 00:00:00')).' AND '.strtotime(date('Y-12-31 23:59:59'));
	 			break;
	 		default:
				$parse = 'm';
	 			break;
	 	}

		$data = M()->query('SELECT FROM_UNIXTIME(repayment_time,"%'.$parse.'") `day`,`status`,`investor_uid` `member`,COUNT(`id`) `count`,SUM(`receive_interest`) `money`  FROM '.$table.' WHERE `status`=2 '.$where.' GROUP BY `day`,`member` ORDER BY `day` ASC');
		foreach($data as $val){
			$total += $val['count'];
			$money += $val['money'];
			$member[$val['member']] += 1;
			$chart['money']['sum']['trend'][$val['status']][intval($val['day'])] = $val['money'];
			$chart['member']['sum']['trend'][$val['status']][intval($val['day'])] = $member[$val['member']];
		}
		$chart['money']['sum']['trend'] = $this->parse($chart['money']['sum']['trend'],$parse);
		$chart['member']['sum']['trend'] = $this->parse($chart['member']['sum']['trend'],$parse);

		$avg = ceil($money/$total);
		$chart['param']['money'] = $money;
		$chart['param']['avg'] = $avg;
		$chart['param']['per'] = $money/count($member);
		$chart['param']['member'] = count($member);
		$sql = '
			  SELECT  
			  SUM(CASE WHEN receive_interest<'.$this->format(ceil($avg*0.02)).' THEN 1 ELSE 0 END) AS "'.$this->format(ceil($avg*0.02),true).'以下",  
			  SUM(CASE WHEN receive_interest>='.$this->format(ceil($avg*0.02)).' AND receive_interest<'.$this->format(ceil($avg*0.1),true).' THEN 1 ELSE 0 END) AS "'.$this->format(ceil($avg*0.02),true).' - '.$this->format(ceil($avg*0.1),true).'",  
			  SUM(CASE WHEN receive_interest>='.$this->format(ceil($avg*0.1)).' AND receive_interest<'.$this->format(ceil($avg*0.3),true).' THEN 1 ELSE 0 END) AS "'.$this->format(ceil($avg*0.1),true).' - '.$this->format(ceil($avg*0.3),true).'",  
			  SUM(CASE WHEN receive_interest>='.$this->format(ceil($avg*0.3)).' AND receive_interest<'.$this->format(ceil($avg*0.6),true).' THEN 1 ELSE 0 END) AS "'.$this->format(ceil($avg*0.3),true).' - '.$this->format(ceil($avg*0.6),true).'",  
			  SUM(CASE WHEN receive_interest>='.$this->format(ceil($avg*0.6)).' THEN 1 ELSE 0 END) AS "'.$this->format(ceil($avg*0.6),true).'以上"  
			  FROM '.$table.' WHERE `status`=2 '.$where.' ';
		$chart['profit']['area'] = M()->query($sql);

		$this->assign('chart',$chart);

		$this->display();
    }

    public function income(){
    	$table = C('DB_PREFIX').'member_money';

	 	switch($_GET['tab']){
	 		case 'last':	 			
				$parse = 'd';
				$last = strtotime('-1 month');
	 			$where = 'AND `add_time` BETWEEN '.strtotime(date('Y-m-01 00:00:00',$last)).' AND '.strtotime(date('Y-m-t 23:59:59',$last));
	 			break;
	 		case 'month':
				$parse = 'd';
				$where = 'AND `add_time` BETWEEN '.strtotime(date('Y-m-01 00:00:00')).' AND '.strtotime(date('Y-m-t 23:59:59'));
	 			break;
	 		case 'year':
				$parse = 'm';
				$where = 'AND `add_time` BETWEEN '.strtotime(date('Y-01-01 00:00:00')).' AND '.strtotime(date('Y-12-31 23:59:59'));
	 			break;
	 		default:
				$parse = 'm';
	 			break;
	 	}

		$data = M()->query('SELECT FROM_UNIXTIME(`add_time`,"%'.$parse.'") `day`,`type`,ABS(SUM(`affect_money`)) `money`  FROM '.$table.' WHERE `type` IN(18,22,23,25,26,30,31) '.$where.' GROUP BY `day`,`type` ORDER BY `day` ASC');
		foreach($data as $val){
			$chart['type'][$val['type']] += $val['money'];
			$chart['fee']['sum']['trend'][$val['type']][intval($val['day'])] = $val['money'];
		}
		$chart['fee']['sum']['trend'] = $this->parse($chart['fee']['sum']['trend'],$parse);

		$chart['borrow']['type'] = M()->query('SELECT borrow_type `type`,SUM(borrow_fee) total FROM '.C('DB_PREFIX').'borrow WHERE homs_id=0 AND `borrow_status`>0 '.$where.' GROUP BY borrow_type ORDER BY total DESC');
		$data = M()->query('SELECT FROM_UNIXTIME(add_time,"%'.$parse.'") `day`,`borrow_type` `type`,SUM(`borrow_fee`) `money`  FROM '.C('DB_PREFIX').'borrow WHERE homs_id=0 AND `borrow_status`>0 '.$where.' GROUP BY `day`,`borrow_type` ORDER BY `day` ASC');
		foreach($data as $val){
			$chart['borrow']['fee']['trend'][$val['type']][intval($val['day'])] = $val['money'];
		}
		$chart['borrow']['fee']['trend'] = $this->parse($chart['borrow']['fee']['trend'],$parse);

		$type['borrow'] = C('BORROW_TYPE');
		$type['fee'] = C('MONEY_LOG');

		$this->assign('chart',$chart);
		$this->assign('type',$type);
		$this->display();
    }

    public function member(){
    	$table = C('DB_PREFIX').'member';

	 	switch($_GET['tab']){
	 		case 'last':	 			
				$parse = 'd';
				$last = strtotime('-1 month');
	 			$where = 'AND `m`.`reg_time` BETWEEN '.strtotime(date('Y-m-01 00:00:00',$last)).' AND '.strtotime(date('Y-m-t 23:59:59',$last));
	 			break;
	 		case 'month':
				$parse = 'd';
				$where = 'AND `m`.`reg_time` BETWEEN '.strtotime(date('Y-m-01 00:00:00')).' AND '.strtotime(date('Y-m-t 23:59:59'));
	 			break;
	 		case 'year':
				$parse = 'm';
				$where = 'AND `m`.`reg_time` BETWEEN '.strtotime(date('Y-01-01 00:00:00')).' AND '.strtotime(date('Y-12-31 23:59:59'));
	 			break;
	 		default:
				$parse = 'm';
	 			break;
	 	}

		$data = M()->query('SELECT FROM_UNIXTIME(`reg_time`,"%'.$parse.'") `day`,`user_type` `type`,COUNT(`id`) `total`  FROM '.$table.' `m` WHERE  2>1 '.$where.' GROUP BY `day`,`type` ORDER BY `day` ASC');
		foreach($data as $val){
			$chart['type'][$val['type']] += $val['total'];
			$chart['member']['type']['trend'][$val['type']][intval($val['day'])] = $val['total'];
		}
		$chart['member']['type']['trend'] = $this->parse($chart['member']['type']['trend'],$parse);
		arsort($chart['type']);

		$data = M()->query('SELECT FROM_UNIXTIME(`reg_time`,"%'.$parse.'") `day`,`from`,COUNT(`id`) `total`  FROM '.$table.' `m` WHERE  2>1 '.$where.' GROUP BY `day`,`from` ORDER BY `day` ASC');
		foreach($data as $val){
			$chart['from'][$val['from']] += $val['total'];
			$chart['member']['from']['trend'][$val['from']][intval($val['day'])] = $val['total'];
		}
		$chart['member']['from']['trend'] = $this->parse($chart['member']['from']['trend'],$parse);
		arsort($chart['from']);


		$data = M()->query('SELECT FROM_UNIXTIME(`m`.`reg_time`,"%'.$parse.'") `day`,`i`.`sex`,COUNT(`id`) `total`  FROM '.$table.' `m` LEFT JOIN `ynw_member_info` `i` ON `m`.`id`=`i`.`uid` WHERE  2>1 '.$where.' GROUP BY `day`,`sex` ORDER BY `day` ASC');
		foreach($data as $val){
			$chart['sex'][$val['sex']] += $val['total'];
			$chart['member']['sex']['trend'][$val['sex']][intval($val['day'])] = $val['total'];
		}
		$chart['member']['sex']['trend'] = $this->parse($chart['member']['sex']['trend'],$parse);
		arsort($chart['sex']);

		$data = M()->query('SELECT FROM_UNIXTIME(`m`.`reg_time`,"%'.$parse.'") `day`,`i`.`marry`,COUNT(`id`) `total`  FROM '.$table.' `m` LEFT JOIN `ynw_member_info` `i` ON `m`.`id`=`i`.`uid` WHERE  2>1 '.$where.' GROUP BY `day`,`marry` ORDER BY `day` ASC');
		foreach($data as $val){
			$chart['marry'][$val['marry']] += $val['total'];
			$chart['member']['marry']['trend'][$val['marry']][intval($val['day'])] = $val['total'];
		}
		$chart['member']['marry']['trend'] = $this->parse($chart['member']['marry']['trend'],$parse);
		arsort($chart['marry']);

		$data = M()->query('SELECT FROM_UNIXTIME(`m`.`reg_time`,"%'.$parse.'") `day`,`i`.`education`,COUNT(`id`) `total`  FROM '.$table.' `m` LEFT JOIN `ynw_member_info` `i` ON `m`.`id`=`i`.`uid` WHERE  2>1 '.$where.' GROUP BY `day`,`education` ORDER BY `day` ASC');
		foreach($data as $val){
			$chart['education'][$val['education']] += $val['total'];
			$chart['member']['education']['trend'][$val['education']][intval($val['day'])] = $val['total'];
		}
		$chart['member']['education']['trend'] = $this->parse($chart['member']['education']['trend'],$parse);
		arsort($chart['education']);

		$data = M()->query('SELECT FROM_UNIXTIME(`m`.`reg_time`,"%'.$parse.'") `day`,`i`.`income`,COUNT(`id`) `total`  FROM '.$table.' `m` LEFT JOIN `ynw_member_info` `i` ON `m`.`id`=`i`.`uid` WHERE  2>1 '.$where.' GROUP BY `day`,`income` ORDER BY `day` ASC');
		foreach($data as $val){
			$chart['income'][$val['income']] += $val['total'];
			$chart['member']['income']['trend'][$val['income']][intval($val['day'])] = $val['total'];
		}
		$chart['member']['income']['trend'] = $this->parse($chart['member']['income']['trend'],$parse);
		arsort($chart['income']);

		$data = M()->query('SELECT FROM_UNIXTIME(`m`.`reg_time`,"%'.$parse.'") `day`,`i`.`age`,COUNT(`id`) `total`  FROM '.$table.' `m` LEFT JOIN `ynw_member_info` `i` ON `m`.`id`=`i`.`uid` WHERE  2>1 '.$where.' GROUP BY `day`,`age` ORDER BY `age` ASC');
		foreach($data as $val){
			$chart['age'][intval($val['age'])] += $val['total'];
			$chart['member']['age']['trend'][intval($val['age'])][intval($val['day'])] = $val['total'];
		}
		$chart['member']['age']['trend'] = $this->parse($chart['member']['age']['trend'],$parse);
		//arsort($chart['age']);

		//dump($data);

		$this->assign('chart',$chart);
		$this->assign('from',array('网站','手机站','微信平台','APP客户端',9=>'后台添加'));
		$this->assign('type',C('MEMBER_TYPE'));
		$this->display('member');
    }
    public function verifyphone(){
    	$this->member();
    }
    public function memberid(){
    	$this->member();
    }
    public function verifyinfo(){
    	$this->member();
    }
    public function verifyvip(){
    	$this->member();
    }
    public function paylog(){
    	$table = C('DB_PREFIX').'member_payment';
	 	$this->assign("payment",C("PAY_TYPE"));
	 	$this->assign('withdraw',array('1'=>'未审核','3'=>'审核未通过','2'=>'已提现'));

	 	switch($_GET['tab']){
	 		case 'last':	 			
				$parse = 'd';
				$last = strtotime('-1 month');
	 			$where = 'AND `add_time` BETWEEN '.strtotime(date('Y-m-01 00:00:00',$last)).' AND '.strtotime(date('Y-m-t 23:59:59',$last));
	 			break;
	 		case 'month':
				$parse = 'd';
				$where = 'AND `add_time` BETWEEN '.strtotime(date('Y-m-01 00:00:00')).' AND '.strtotime(date('Y-m-t 23:59:59'));
	 			break;
	 		case 'year':
				$parse = 'm';
				$where = 'AND `add_time` BETWEEN '.strtotime(date('Y-01-01 00:00:00')).' AND '.strtotime(date('Y-12-31 23:59:59'));
	 			break;
	 		default:
				$parse = 'm';
	 			break;
	 	}

		$chart['payment']['type'] = M()->query('SELECT COUNT(id) `times`, way `payment`,SUM(`money`) total FROM '.$table.' WHERE status=1 '.$where.' GROUP BY payment ORDER BY total DESC');
		$data = M()->query('SELECT COUNT(id) `total`,way `payment`,FROM_UNIXTIME(add_time,"%'.$parse.'") `day`,SUM(`money`) `money`  FROM '.$table.' WHERE `status`=1 '.$where.' GROUP BY `day`,`way` ORDER BY `day` ASC');
		foreach($data as $val){
			$total += $val['total'];
			$money += $val['money'];
			$chart['payment']['money']['trend'][$val['payment']][intval($val['day'])] = $val['money'];
			$chart['payment']['count']['trend'][$val['payment']][intval($val['day'])] = $val['total'];
		}
		$chart['payment']['money']['trend'] = $this->parse($chart['payment']['money']['trend'],$parse);

		$avg = ceil($money/$total);
		$chart['payment']['area'] = M()->query('
			  SELECT  
			  SUM(CASE WHEN money<'.$this->format(ceil($avg*0.02)).' THEN 1 ELSE 0 END) AS "小于'.$this->format(ceil($avg*0.02),true).'",  
			  SUM(CASE WHEN money>='.$this->format(ceil($avg*0.02)).' AND money<'.$this->format(ceil($avg*0.1)).' THEN 1 ELSE 0 END) AS "'.$this->format(ceil($avg*0.02),true).' - '.$this->format(ceil($avg*0.1),true).'",  
			  SUM(CASE WHEN money>='.$this->format(ceil($avg*0.1)).' AND money<'.$this->format(ceil($avg*0.3)).' THEN 1 ELSE 0 END) AS "'.$this->format(ceil($avg*0.1),true).' - '.$this->format(ceil($avg*0.2),true).'",  
			  SUM(CASE WHEN money>='.$this->format(ceil($avg*0.2)).' AND money<'.$this->format(ceil($avg*0.6)).' THEN 1 ELSE 0 END) AS "'.$this->format(ceil($avg*0.2),true).' - '.$this->format(ceil($avg*0.6),true).'",  
			  SUM(CASE WHEN money>='.$this->format(ceil($avg*0.6)).' THEN 1 ELSE 0 END) AS "大于'.$this->format(ceil($avg*0.6),true).'"  
			  FROM '.$table.' WHERE `status`=1 '.$where
		);


		$total = 0;
		$money = 0;
		$table = C('DB_PREFIX').'member_withdraw';
		$chart['withdraw']['status'] = M()->query('SELECT COUNT(id) `times`,`withdraw_status` `status`,SUM(`withdraw_money`) total FROM '.$table.' WHERE withdraw_status>0 '.$where.' GROUP BY `withdraw_status` ORDER BY total DESC');
		$data = M()->query('SELECT COUNT(id) `total`,SUM(`success_money`) `money`,FROM_UNIXTIME(add_time,"%'.$parse.'") `day`,SUM(`success_money`) `money`  FROM '.$table.' WHERE `withdraw_status`=2 '.$where.' GROUP BY `day` ORDER BY `day` ASC');
		foreach($data as $val){
			$total += $val['total'];
			$money += $val['money'];
			$chart['withdraw']['money']['trend'][$val['payment']][intval($val['day'])] = $val['money'];
			$chart['withdraw']['count']['trend'][$val['payment']][intval($val['day'])] = $val['total'];
		}
		$chart['withdraw']['money']['trend'] = $this->parse($chart['withdraw']['money']['trend'],$parse);

		$avg = ceil($money/$total);
		$chart['withdraw']['area'] = M()->query('
			  SELECT  
			  SUM(CASE WHEN success_money<'.$this->format(ceil($avg*0.02)).' THEN 1 ELSE 0 END) AS "小于'.$this->format(ceil($avg*0.02),true).'",  
			  SUM(CASE WHEN success_money>='.$this->format(ceil($avg*0.02)).' AND success_money<'.$this->format(ceil($avg*0.1)).' THEN 1 ELSE 0 END) AS "'.$this->format(ceil($avg*0.02),true).' - '.$this->format(ceil($avg*0.1),true).'",  
			  SUM(CASE WHEN success_money>='.$this->format(ceil($avg*0.1)).' AND success_money<'.$this->format(ceil($avg*0.3)).' THEN 1 ELSE 0 END) AS "'.$this->format(ceil($avg*0.1),true).' - '.$this->format(ceil($avg*0.2),true).'",  
			  SUM(CASE WHEN success_money>='.$this->format(ceil($avg*0.2)).' AND success_money<'.$this->format(ceil($avg*0.6)).' THEN 1 ELSE 0 END) AS "'.$this->format(ceil($avg*0.2),true).' - '.$this->format(ceil($avg*0.6),true).'",  
			  SUM(CASE WHEN success_money>='.$this->format(ceil($avg*0.6)).' THEN 1 ELSE 0 END) AS "大于'.$this->format(ceil($avg*0.6)).'"  
			  FROM '.$table.' WHERE `withdraw_status`=2 '.$where
		);


		$this->assign('chart',$chart);
		$this->display('paylog');
    }
    public function format($number,$postfix=''){
    	if($postfix!=''){
    		$number = str_pad(substr($number,0,1),strlen($number),0);
    		$m = $number/10000;
    		if($m>=1){
				$number = ceil($m).'万';
    		}
    	}else{
			$number = str_pad(substr($number,0,1),strlen($number),0);
    	}
    	return $number;
    }
    public function paylogonline(){
    	$this->paylog();
    }
    public function paylogoffline(){
    	$this->paylog();
    }
    public function withdrawlogwait(){
    	$this->paylog();
    }
    public function Withdrawlog(){
    	$this->paylog();
    }
    public function withdrawloging(){
    	$this->paylog();
    }

    function parse($data,$type){    	
    	if($type=='d'){
    		$param['prefix'] = intval(date('m')).'月';
    		$param['postfix'] = '日';
			$limit = date('t');
			$param['region'] = date('Y年m月1日').' - '.date('Y年m月t日');
    	}elseif($type=='m'){
    		$param['prefix'] = date('Y').'年';
    		$param['postfix'] = '月';
    		$limit = 12;
			$param['region'] = date('Y年1月1日').' - '.date('Y年12月31日');
    	}
    	if($_GET['tab']=='last'){
    		$time = strtotime('-1 month');
    		$limit = date('t',$time);
			$param['region'] = date('Y年m月1日',$time).' - '.date('Y年m月t日',$time);
    	}
    	if($_GET['tab']=='all'||$_GET['tab']==''){
    		$limit = 12;
			$param['region'] = '网站运营开始至今';
    	}
    	foreach($data as $key => $val){
			for($i=1;$i<=$limit;$i++){
				$data[$key][$i] = intval($data[$key][$i]);
			}
			ksort($data[$key]);
    	}
		for($i=1;$i<=$limit;$i++){
			$param['day'][] = $i;
		}
		$this->assign('param',$param);
    	return $data;
    }
}
?>