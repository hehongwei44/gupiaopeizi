<?php
// 回收中的投资视图模型
class TenderListViewModel extends ViewModel {
	 public $viewFields = array(
		'BorrowInvestor'=>array('id','status','borrow_id','investor_uid','borrow_uid','investor_capital','investor_interest','receive_capital','receive_interest','substitute_money','expired_money','invest_fee','paid_fee','add_time'=>'invest_time','deadline','is_auto','reward_money','debt_uid','_type'=>'LEFT'),
		 'Borrow'=>array('id'=>'borrowid','borrow_name','borrow_duration','borrow_money','borrow_interest','borrow_interest_rate',
		 'borrow_fee','has_borrow','borrow_times','repayment_money','repayment_interest','expired_money','repayment_type','borrow_type',
		 'borrow_status','borrow_use','add_time'=>'borrow_time','collect_day','collect_time','full_time','first_verify_time','second_verify_time',
		 'add_ip','borrow_info','total','has_pay','substitute_money','reward_vouch_rate','reward_vouch_money','reward_type','reward_num',
		 'reward_money','borrow_min','borrow_max','province','city','area','vouch_member','has_vouch','password','is_tuijian','can_auto',
		 'updata','_on'=>'Borrow.id=BorrowInvestor.borrow_id','_type'=>'LEFT'),
		'Member'=>array('user_name'=>'borrow_user','credits','_on'=>'Member.id=Borrow.borrow_uid','_type'=>'LEFT'),
		 'investor_detb'=>array('status'=>'detb_status','period','_on'=>'investor_detb.invest_id=BorrowInvestor.id','_type'=>'LEFT'),
		 'InvestorDetail'=>array('status','deadline'=>'times','_on'=>'InvestorDetail.borrow_id=Borrow.id'),
	);
}
?>