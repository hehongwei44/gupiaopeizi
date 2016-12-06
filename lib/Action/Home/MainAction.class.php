<?php
class MainAction extends HomeAction {
    public function index(){
    	if(S('index_param')){
			$param = S('index_param');
    	}else{
	    	//获取最高年化率
	    	$total['ratio'] = D('Borrow')->max('borrow_interest_rate');
	    	//获取会员总数
			$total['member'] = D('Member')->count();
	    	//获取募集总金额
			$total['total'] = D('Borrow')->where('borrow_status>=2')->sum('borrow_money');
	    	//服务客户数量
	    	$serve = D('Borrow')->where('borrow_status>=2')->group('borrow_uid')->count();
	    	$total['serve'] = $serve+intval(D('BorrowInvestor')->group('investor_uid')->count()+1250);//+1250是初期多显示的
	    	//配资总额
	    	$total['stock'] = D('Borrow')->where('borrow_status>=2 AND borrow_type IN(7,8,9)')->sum('borrow_money')+38655000;//+8650000是初期多显示的
	    	//分配利息
			//$total['interest'] = D('Borrow')->where('borrow_status>=2')->sum('borrow_interest');
			//可配资金
			$total['canPZ'] = 24750000;
			foreach($total as $key => $val){
				$total[$key] = number_format(intval($val));
			}
			$param['total'] = $total;
			$param['news'] = D('Article')->field('id,title')->where('type_id = 1')->order('art_time DESC')->limit(6)->select();

			$links = D('Friend')->field('link_txt `name`,link_href `url`,link_img `logo`,link_type `type`')->where('is_show = 1 AND `start`< '.time().' AND `expire`> '.time())->order('link_order DESC')->select();
			foreach($links as $val){
				if($val['type']==2){
					$param['links']['cooperate'][]=$val;
				}else{
					$param['links']['friend'][]=$val;
				}
			}

	    	$trade = C('TRADE_TYPE');
			$rank = D('EventPeople')->field('`name`,`total`,`from`,`duration`,`reward`')->where('event=0')->order('`reward` DESC')->limit(10)->select();
			foreach($rank as $key => $val){
				
				switch($val['from']){
					case '9':
						$val['type'] = '<a href="/stock/month.html" target="_blank">按月配资</a>';
						$val['postfix'] = '月';
						break;
					case '8':
						$val['type'] = '<a href="/stock/day.html" target="_blank">按天配资</a>';
						$val['postfix'] = '天';
						break;
					case '7':
						$val['type'] = '<a href="/stock/week.html" target="_blank">按周配资</a>';
						$val['postfix'] = '周';
						break;

				}
				$name = $val['name'];
				$val['name'] = cutstr($name,0,2,false).'***';
				$val['name'] .= cutstr($name,3,-2,false);
				$val['total'] = number_format($val['total']/10000,1);
				$rank[$key] = $val;
			}
			$param['rank'] = $rank;
			S('index_param',$param);
    	}

    	$this->param = array_merge($this->param,$param);
		
        $this->data['stock'] = D('Borrow')->field('`id`,`borrow_name` `title`,`borrow_money` `total`,`borrow_type` `type`,`borrow_interest_rate` `rate`,`borrow_duration` `duration`,`repayment_type` `repayment`')->where('borrow_status = 2')->order('`add_time` DESC')->limit(1)->select();
        foreach($this->data['stock'] as $key => $val){
			switch($val['type']){
				case '9':
					$val['postfix'] = '月';
					break;				
				case '7':
					$val['postfix'] = '周';
					break;
				default:
					$val['postfix'] = '天';
					break;
			}
			$this->data['stock'][$key] = $val;
			$borrow[$val['id']] = $val['total'];
        }

        //获取投标的完成度
		$invest = D('BorrowInvestor')->field('`id`,`borrow_id` `borrow`,SUM(`investor_capital`) `total`')->where('borrow_id IN ('.implode(',',array_keys($borrow)).')')->group('`borrow_id`')->select();
        foreach($invest as $key => $val){
			$left = $borrow[$val['borrow']]-$val['total'];
			$ratio = intval($val['total']/$borrow[$val['borrow']]*100);
			$this->data['invest'][$val['borrow']] = array('total'=>$val['total'],'left'=>$left,'ratio'=>$ratio);
        }
        $this->data['other'] = 'empty';

        $this->display();
    }	
}
	