<?php
// +----------------------------------------------------------------------
// | ThinkPHP
// +----------------------------------------------------------------------
// | Copyright (c) 2008 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id$

// 借款与还款关联视图模型
class BorrowViewModel extends ViewModel {
	 public $viewFields = array(
		'Borrow'=>array('id','borrow_name','borrow_uid','borrow_duration','borrow_money','borrow_interest','borrow_interest_rate',
		 'borrow_fee','has_borrow','borrow_times','repayment_money','repayment_interest','expired_money','repayment_type','borrow_type',
		 'borrow_status','borrow_use','add_time','collect_day','collect_time','full_time','deadline','first_verify_time','second_verify_time',
		 'add_ip','borrow_info','total','has_pay','substitute_money','reward_vouch_rate','reward_vouch_money','reward_type','reward_num',
		 'reward_money','borrow_min','borrow_max','province','city','area','vouch_member','has_vouch','password','is_tuijian','can_auto',
		 'updata','_type'=>'LEFT'),
		'InvestorDetail'=>array('status','deadline'=>'times','_on'=>'InvestorDetail.borrow_id=Borrow.id'),
	);
}
?>