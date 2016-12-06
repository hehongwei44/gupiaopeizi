<?php
    /**
    * 普通标债权转让处理类
    * 
    * @author  abc@qq.com
    * @time 2013-12-25 17:19
    * @copyright 超级版
    * @link www.abc.com
    */
   class DebtBehavior{
       public $uid;
       private $pre;
       private $pagesize = 15;
       private $Model;
       private $time ;
           
       public function __construct($uid=0){
           $this->pre = C('DB_PREFIX');
           $this->uid = intval($uid); 
           $this->time = 7*24*60*60;
           $this->Model = M('investor_detb');
           $this->checkTime();
           import("ORG.Util.Page");   
       }
       /**
       * 检测转让是否超时
       * 
       */
       public function checkTime(){
           $time = time();
           $result = $this->Model->field("invest_id")->where("status=2 and valid <= {$time}")->select();
           if(count($result) && is_array($result)){
               foreach($result as $k=>$v){
                   $this->cancelDebt($v['invest_id'], 3);
               }
           }
       }
        /**
        * 可以流转的普通标
        * 
        */
        public function  canTransfer(){  
            $count = M('borrow_investor')->where("investor_uid = ".$this->uid."  and status = 1 and debt_status=0")->count();

            $Page = new Page($count, $this->pagesize);
            $transfer = M('borrow_investor i')
                ->join("{$this->pre}borrow b ON b.id = i.borrow_id")
                ->join("{$this->pre}member m ON i.borrow_uid = m.id")
                ->field("i.id, i.borrow_id, i.add_time, i.deadline, i.investor_interest, i.investor_capital, b.borrow_name,b.borrow_money, b.borrow_interest_rate, m.user_name")
                ->where("i.investor_uid = ".$this->uid."  and i.status = 1 and i.debt_status=0")
                ->limit($Page->firstRow. ',' . $Page->listRows)
                ->order('i.id')
                ->select();

            foreach($transfer as $k=>$v){
               $arr = $this->countDebt($v['id']);
               $transfers['data'][$k] = $arr+ $v;   
            }
            $transfers['page'] = $Page->show();
            return $transfers;
        }
        
        /**
        * 统计债权回购情况
        * @param intval $invest_id  // 投资id
        */
        public function countDebt($invest_id){
            $debt = array();
           $invest_id = intval($invest_id); 
           if(!$invest_id){
               return $debt;
           }
           //$condition = "invest_id= '".$invest_id."' and status in (6,7)";
           $condition = "invest_id= '".$invest_id."'";
           //可转让期数、统计待收本金和利息
           $debt = M("investor")->field("count(id) as re_num, sum(capital) as capital, sum(interest) as interest ")->where($condition)->find();
           $debt['total'] = M("investor")->where("invest_id=".$invest_id)->count('id'); //总共多少期
           return $debt;
           
        }
        /**
        * 债权转让操作
        * 
        * @param int $invest_id   // 债权id
        * @param float $price    // 出售价格
        * @param string $paypass // 支付密码
        * @return mixed        // 成功返回TRUE 失败返回失败状态
        */
        public function sell($invest_id, $price, $paypass){
            $invest_id = intval($invest_id);
            $price = floatval($price);
            $paypass = md5($paypass);
            
            $check = $this->checkSell($invest_id, $price, $paypass);  
            if($check==='TRUE'){ // 检测通过
                $count_invest = $this->countDebt($invest_id);
                $info['invest_id'] = $invest_id;
                $info['sell_uid'] = $this->uid;
                $info['transfer_price'] = $price;
                $info['money'] =  $count_invest['capital'] + $count_invest['interest'];
                $info['period'] = $count_invest['re_num'];
                $info['total_period'] = $count_invest['total'];
                $info['addtime'] = time();
                $info['ip'] = get_client_ip();
                
                $datag = get_global_setting();
                $debt_audit = $datag['debt_audit'];
                if($debt_audit){
                   $info['status'] = 9; //审核
                }else{
                   $info['status'] = 2; 
                   $info['valid'] = time()+$this->time ;
                }
                
                
                //如果存在转让记录 则直接更新
                $record = $this->Model->where("invest_id=".$invest_id)->getField('id');

                $this->Model->startTrans();
                if($record){
                    $debt = $this->Model->where("id=".$record)->save($info);      
                }else{
                    $debt = $this->Model->add($info);
                }
                $investor = M("borrow_investor")->where("id=".$invest_id)->save(array('debt_status'=>1));
                if($debt && $investor){
                   $this->Model->commit();
                   //return 'TRUE';
				   return '1';
                }else{
                    $this->Model->rollback();
                    //return 'published_transfers_fail';
					return '债权转让失败！';
                } 
            }else{
                return $check;
            }
        }
        
        /**
        * 债权转让检查是否可以转让
        * 价格是否大于债权价格
        * 支付密码是否正确
        * 余额是否够支付转让手续费
        * @param intval $invest_id  // 投资id
        * @param float $price // 转让价格
        * @param password $paypass // 支付密码
        */
        private function checkSell($invest_id, $price, $paypass){
            //$condition = "invest_id= '".$invest_id."' and status in (6,7)";
            $condition = "invest_id= '".$invest_id."'";
            $total = M("investor")->field("sum(capital+interest) as total_money ")->where($condition)->find();
            if($price > $total['total_money']){ // 有没有超出价格上限
                //return 'beyond_the_price';
				return '债权转让不允许超出价格上限';
                exit;
            }
            $vm = getMinfo($this->uid,'m.pin_pass,mm.account_money,mm.back_money'); 
            if($paypass != $vm['pin_pass']){
                //return 'payment_password_error';
				//return '支付密码错误';
                //exit;
            }
            $datag = get_global_setting();
            $datag['debt_fee'];
            $fee = round($price * $datag['debt_fee'] / 100, 2);
            if($fee > ($vm['account_money'] + $vm['back_money'])){
                //return 'balance_insufficient_pay_fees';
				return '对不起，您的账户余额不足以支付本次转让手续费';
                exit;
            }
            
            return 'TRUE'; 
        }
        
        /**
        * 转让中的债权
        * 
        */
        public function onBonds(){    
            $count = M('investor_detb')->where("sell_uid = ".$this->uid."  and status in (2,9)")->count();
            $Page = new Page($count, $this->pagesize);
            $Bonds['data'] = M('investor_detb d')
                ->join("{$this->pre}borrow_investor i ON d.invest_id = i.id")
                ->join("{$this->pre}borrow b ON i.borrow_id = b.id")
                ->field("d.invest_id, d.status, i.borrow_id, d.money, d.transfer_price, d.addtime, d.period, d.total_period, b.borrow_name, b.borrow_interest_rate, b.total, b.has_pay")
                ->where("d.sell_uid = ".$this->uid."  and d.status in (2,9) ")
                ->limit($Page->firstRow. ',' . $Page->listRows)
                ->order('d.id')
                ->select();

            $Bonds['page'] = $Page->show();
            return $Bonds;
        }
        
        /**
        * 取消转让
        * 
        * @param intval $invest_id   //债权id
        * @param strval $paypass // 支付密码
        */
        public function cancel($invest_id, $paypass){
            $invest_id = intval($invest_id);  
            $paypass = md5($paypass);
            $vm = getMinfo($this->uid,'m.pin_pass'); 
            if($paypass != $vm['pin_pass']){  
                return false; exit;
            }
            if($this->cancelDebt($invest_id, 1)){
               return true; 
            }else{
                return false;
            }
            
        }
        /**
        * 撤销转让
        * 
        * @param mixed $invest_id  // 债权id
        * @param mixed $type     状态 1 债权人撤销，2债权还款撤销  3转让超时
        */
        public function cancelDebt($invest_id, $type){
            if(!$this->Model->where("invest_id={$invest_id}")->count('id')){
                return false;
            }
            $remark = array(
                '1'=>'债权人撤销',
                '2'=>'债权还款撤销',
                '3'=>'转让超时',
            );
            $update = array(
                       'status'=>3,
                       'cancel_times'=>array('exp','cancel_times+1'),
                       'cancel_time'=>time(),
                       'remark' =>$remark[$type],
                    );
            
            $condition1 = " id={$invest_id} and debt_status=1";
            $condition2 =  " invest_id={$invest_id} and (status=2 OR status=9)";
            $this->Model->startTrans();
            $borrow_investor = M("borrow_investor")->where($condition1)->save(array("debt_status"=>'0')); 

            $invest_detb = $this->Model->where($condition2)->save($update);
            if($borrow_investor && $invest_detb){
                $this->Model->commit();
                return true;
            }else{
                $this->Model->rollback();
                return false;
            }
        }
        
        public function cancelList(){  
            $count = M('investor_detb')->where("sell_uid = ".$this->uid."  and status = 3")->count();
            $Page = new Page($count, $this->pagesize);
            $Bonds['data'] = M('investor_detb d')
                ->join("{$this->pre}borrow_investor i ON d.invest_id = i.id")
                ->join("{$this->pre}borrow b ON i.borrow_id = b.id")
                ->field("d.invest_id, d.remark, i.borrow_id, d.money, d.transfer_price, d.cancel_time, 
                            d.cancel_times, d.period, d.total_period, b.borrow_name, b.borrow_interest_rate, b.total, b.has_pay")
                ->where("d.sell_uid = ".$this->uid."  and d.status = 3 ")
                ->limit($Page->firstRow. ',' . $Page->listRows)
                ->order('d.id')
                ->select();

            $Bonds['page'] = $Page->show();
            return $Bonds;
        }
        
        /**
        * 成功转让的债权
        * 
        */
        public function successDebt(){  
            $count = M('investor_detb')->where("sell_uid = ".$this->uid."  and status in (1,4)")->count();
            $Page = new Page($count, $this->pagesize);
            $lists['data'] = M('investor_detb d')
                ->join("{$this->pre}borrow_investor i ON d.invest_id = i.id")
                ->join("{$this->pre}borrow b ON i.borrow_id = b.id")
                ->join("{$this->pre}member m ON d.buy_uid=m.id")
                ->field("d.invest_id, i.borrow_id, d.money, d.transfer_price, d.buy_time,d.addtime, d.status, m.user_name,  
                            d.cancel_times, d.period, d.total_period, d.serialid, b.borrow_name, b.borrow_interest_rate, b.total, b.has_pay")
                ->where("d.sell_uid = ".$this->uid."  and d.status in (1,4) ")
                ->limit($Page->firstRow. ',' . $Page->listRows)
                ->order('d.id')
                ->select();

            $lists['page'] = $Page->show();
            return $lists;
        }
        
        /**
        * 已购买的债权
        * 
        */
        public function buyDetb(){
            $count = M('investor_detb')->where("buy_uid = ".$this->uid."  and status in (1,4)")->count();
            $Page = new Page($count, $this->pagesize);
            $lists['data'] = M('investor_detb d')
                ->join("{$this->pre}borrow_investor i ON d.invest_id = i.id")
                ->join("{$this->pre}borrow b ON i.borrow_id = b.id")
                ->join("{$this->pre}member m ON d.sell_uid=m.id")
                ->field("d.invest_id, i.borrow_id, d.money, d.transfer_price, d.buy_time,d.addtime, d.status, d.serialid, m.user_name,  
                            d.cancel_times, d.period, d.total_period, b.borrow_name, b.borrow_interest_rate, b.total, b.has_pay")
                ->where("d.buy_uid = ".$this->uid."  and d.status in (1,4) ")
                ->limit($Page->firstRow. ',' . $Page->listRows)
                ->order('d.status')
                ->select();

            $lists['page'] = $Page->show();
            return $lists;
        }
        /**
        * 回收中的转让
        * 
        */
        public function onDetb(){
            $count = M('investor_detb')->where("buy_uid = ".$this->uid."  and status = 1")->count();
            $Page = new Page($count, $this->pagesize);
            $lists['data'] = M('investor_detb d')
                ->join("{$this->pre}borrow_investor i ON d.invest_id = i.id")
                ->join("{$this->pre}borrow b ON i.borrow_id = b.id")
                ->join("{$this->pre}member m ON b.borrow_uid=m.id")
                ->field("d.invest_id, i.borrow_id, d.money, d.serialid, m.user_name,  
                            d.total_period, b.borrow_name, b.total, b.has_pay")
                ->where("d.buy_uid = ".$this->uid."  and d.status = 1 ")
                ->limit($Page->firstRow. ',' . $Page->listRows)
                ->order('d.id')
                ->select();
            $list = array();
            foreach($lists['data'] as $k=>$v){
                $info = $AlsoPeriods = $this->getAlso($v['invest_id']);
                $data = array_merge($v, $info);
                $lists['data'][$k] = $data;
            }
            $lists['page'] = $Page->show();
            return $lists;
        }
        /**
        * 以还资金和利息
        * 
        * @param int $invest_id
        * @return array
        */
        public function getAlso($invest_id){
            $invest_id = intval($invest_id);
            $count = array();
            $count = M("investor")->field("sum(interest) as interest, sum(capital) as capital")
                                ->where("invest_id={$invest_id}  and status in (1,2,3,4,5)")
                                ->find();
            $info = M("investor")->field("deadline, sort_order, expired_days")
                                ->where("invest_id={$invest_id} and status in (6,7)")
                                ->order("sort_order asc")
                                ->find();
            $info = array_merge($info ,$count);
            
            return $info;
        }
        /**
        * 待款汇总和待回款  (貌似该坏了，调用得地方太多，不能删除)
        * 
        * @param int $invest_id
        * @return array
        */
        public function getAlsoPeriods($invest_id){
            $invest_id = intval($invest_id);
            $count = array();
            $count = M("investor")->field("sum(interest) as interest, sum(capital) as capital")
                                ->where("invest_id={$invest_id}  and status in (6,7)")
                                ->find();
            $info = M("investor")->field("deadline, sort_order, expired_days")
                                ->where("invest_id={$invest_id} and status in (6,7)")
                                ->order("sort_order asc")
                                ->find();
            $info = array_merge($info ,$count);
            if(!$info){
                return array();
            }
            return $info;
                                
        }
        
        /**
        * 列出所有进行中的债权转让
        * 
        * @param intval $size //每次读取数量，$page=true 每页数量，false 指定读取数量
        * @param boolean $pstatus  // 分页开关 true 分页  false 不分页
        */
        public function listAll($parm,$size=0, $pstatus=true){
			if(empty($parm['map'])) return;
			$map= $parm['map'];
			
			$map['d.status']=array("in","2,4");
	
            //$condition = "d.status in (2, 4)";
            $size && $this->pagesize = intval($size); 
            $field = "d.transfer_price, d.status, d.money, d.total_period, d.period, d.valid, d.id as debt_id, i.id as invest_id,
                     i.investor_uid, i.deadline, b.id, b.borrow_name, b.borrow_interest_rate,b.borrow_status,b.borrow_duration,m.credits, m.user_name";
            if($pstatus){ 
                //$count = M("investor_detb")->where("status in (2, 4)")->count();
				$count = M("investor_detb d")->where($map)->count();
                $Page = new Page($count, $this->pagesize); 
                $list['data'] = M("investor_detb d")
                        ->join("{$this->pre}borrow_investor i ON d.invest_id=i.id")
                        ->join("{$this->pre}borrow b ON i.borrow_id = b.id")
                        ->join("{$this->pre}member m ON i.investor_uid=m.id")
                        ->field($field)
                        ->where($map)
                        ->limit($Page->firstRow .','. $Page->listRows)
                        ->order("d.status asc")
                        ->select();
                $list['page'] = $Page->show();
                 
            }else{
                $list = M("investor_detb d")
                        ->join("{$this->pre}borrow_investor i ON d.invest_id=i.id")
                        ->join("{$this->pre}borrow b ON i.borrow_id = b.id")
                        ->join("{$this->pre}member m ON i.investor_uid=m.id")
                        ->field($field)
                        ->where($map)
                        ->limit($this->pagesize)
                        ->order("d.status desc")
                        ->select();
            }

            return $list;
        }
            
        /**
        * 购买债权
        * 
        * @param string $paypass   // 密码
        * @param int $invest_id   //债权id
        * @return  boolean 
        */
        public function buy($paypass, $invest_id){
            $check_result = $this->checkBuy($paypass, $invest_id);
            if($check_result === 'TRUE'){
                $debt_info = $this->Model->field("transfer_price, sell_uid, money")->where("invest_id={$invest_id}")->find();
                $buy_user = M("member_account")->field("account_money, back_money, money_collect")->where("uid={$this->uid}")->find();
                $sell_user = M("member_account")->field("account_money, money_collect")->where("uid={$debt_info['sell_uid']}")->find();
                if($debt_info['transfer_price'] > ($buy_user['account_money']+$buy_user['back_money'])){
                    return 'insufficient_account_balance';
                }   
                
                $buy_user['back_money'] = $buy_user['back_money'] - $debt_info['transfer_price'];
                if($buy_user['back_money'] < 0){
                    $buy_user['account_money'] = $buy_user['account_money'] + $buy_user['back_money'];
                    $buy_user['back_money'] = 0;
                }
                $buy_user['money_collect'] += $debt_info['money']; 
                // 转让手续费扣除
                $datag = get_global_setting();
                $debt_fee = $datag['debt_fee'];// 百分比
                $fee = round(($debt_info['transfer_price'] * $debt_fee)/100, 2);
                //转让方增加资金
				$sell_user['account_money'] += $debt_info['transfer_price'];
                $sell_user['money_collect'] = $sell_user['money_collect'] - $debt_info['money'];// 减去待收
                
                
                // 转让序列号
                $serial = "ZQZR-".date("YmdHis").mt_rand(1000,9999);
                $serialid_count = $this->Model->where("serialid>{$serial}")->count();
                $serialid = $serial + $serialid_count+1;
                $debt = array(
                    'buy_time'=>time(),
                    'buy_uid' =>$this->uid,
                    'status' =>1,
                    'serialid' => $serialid,
                );
                
                $this->Model->startTrans();
                $investor_status = M("borrow_investor")->where("id={$invest_id}")->save(array("debt_status" => 2, 'debt_uid'=>$this->uid));
                $detail_status = M("investor")->where("invest_id={$invest_id} and status in (6,7)")->save(array('investor_uid'=>$this->uid));
                $debt_status = $this->Model->where("invest_id={$invest_id}")->save($debt);
                $sell_user_status = M("member_account")->where("uid={$debt_info['sell_uid']}")->save($sell_user);
                $buy_user_status = M("member_account")->where("uid={$this->uid}")->save($buy_user);
                if($investor_status && $detail_status && $debt_status && $sell_user_status && $buy_user_status){
                    $this->Model->commit();
                    $this->moneyLog($this->uid, 46, -$debt_info['transfer_price'], $debt_info['money'], "购买{$serialid}号债权", $debt_info['sell_uid']);
                    $this->moneyLog($debt_info['sell_uid'], 47, $debt_info['transfer_price'], $debt_info['money'], "转让{$serialid}号债权", $this->uid);
                    logMoney($debt_info['sell_uid'],48,-$fee,"转让{$serialid}号债权手续费（转让金额的{$debt_fee}%）");
                    //return 'TRUE';  
					return '购买成功';
                }else{
                    $this->Model->rollback();
                    //return 'buy_error';  
					return '购买失败';  
                }
            }else{
                return $check_result;
            }
        }
        
        /**
        * 检测购买条件
        * 1 是否自己的债权
        * 2、是否自己的债务
        * 3、支付密码是否正确
        * 4、余额是否足够
        * 
        * @param string $paypass   // 支付密码
        * @param int $invest_id   //债权id
        * @return  boolean 
        */
        private function checkBuy($paypass, $invest_id) {
            
            $invest = $this->Model->field("transfer_price, sell_uid, valid")->where("invest_id = {$invest_id}")->find();
            if($invest['sell_uid']==$this->uid){
                //return 'creditors_can_not_buy';
				return '不能购买自己发布的债权';
            }
            if(time() >= $invest['valid']){
                //return 'debt_timeout';
				return '本债权转让已过期';
            }
            $isBorrow = M("borrow_investor")->where("id = {$invest_id} and borrow_uid = {$this->uid} ")->count();
            if($isBorrow){
                //return 'debtor_can_not_buy';
				return '原借款人不能购买债权';
            }
            
            $user_info = M("member m")
                            ->join("{$this->pre}member_account money ON m.id = money.uid")
                            ->field("m.pin_pass, money.account_money, money.back_money")
                            ->where("m.id={$this->uid}")
                            ->find();
            if(md5($paypass) != $user_info['pin_pass']){
                //return 'payment_password_error';
				//return '支付密码错误';
            }
            
            if($invest['transfer_price'] > ($user_info['account_money'] + $user_info['back_money'])){
                //return 'insufficient_account_balance';
				return '您的账户余额不足';
            }
            
            return 'TRUE';
        }
        /**
        * 债权转让资金操作记录日志
        * @param int  $uid  // 用户id
        * @param int  $type // 操作类型
        * @param float $money  //操作资金
        * @param float $$debt_money // 债权金额
        * @param string $info //日志说明
        * @param int  $target_uid // 交易对方uid
        */
        private function moneyLog($uid, $type, $money, $debt_money, $info, $target_uid){
            $user = M("member")->field("user_name")->where("id={$target_uid}")->find();
            $money_log = M("member_money")
                            ->field("account_money, back_money, collect_money, freeze_money")
                            ->where("uid={$uid}")
                            ->order("id desc")
                            ->find();
                            
            $money_log['affect_money'] = $money;
            $money_log['uid'] = $uid;
            $money_log['type'] = $type;
            $money_log['info'] = $info;
            $money_log['add_time'] = time();
            $money_log['add_ip'] = get_client_ip();
            $money_log['target_uid'] = $target_uid;
            $money_log['target_uname'] = $user['user_name'];
            
            if($money > 0){  // 增加资金
                $money_log['account_money'] +=  $money;
                M("member_money")->add($money_log);
                $money_log['collect_money'] = $money_log['collect_money'] - $debt_money;//待收资金减少债权金额
                $money_log['affect_money'] = -$debt_money;
                $money_log['info'] = $info.",减少待收资金";
            }else{
                $money_log['back_money'] += $money;
                if($money_log['back_money'] < 0){
                    $money_log['account_money'] =  $money_log['account_money'] + $money_log['back_money']; 
                    $money_log['back_money'] = 0.0;
                }
                M("member_money")->add($money_log);
                $money_log['collect_money'] += $debt_money;//待收资金增加债权金额
                $money_log['affect_money'] = $debt_money;
                $money_log['info'] = $info.",增加待收资金"; 
            }
            
            
            
            $id = M("member_money")->add($money_log);
            return $id; 
        }
        /**
        * 后台管理列表
        * 
        * @param mixed $status
        * @return array
        */
        public function adminList($status='1'){
            $count = $this->Model->where("1 and {$status}")->count();
            $Page = new Page($count, $this->pagesize); 
            $list['data'] = M("investor_detb d")
                ->join("{$this->pre}borrow_investor i ON d.invest_id=i.id")
                ->join("{$this->pre}borrow b ON i.borrow_id= b.id")
                ->join("{$this->pre}member m ON d.sell_uid=m.id")
                ->field("d.id as debt_id, d.remark, d.cancel_time, d.invest_id, d.status, m.user_name, b.borrow_name, b.id, d.addtime, b.borrow_interest_rate, b.total, b.has_pay, d.period, d.transfer_price")
                ->where("1 and {$status}")
                ->limit($Page->firstRow .','. $Page->listRows) 
                ->order("d.addtime desc")
                ->select();
            $list['page'] = $Page->show(); 
            foreach($list['data'] as $k=>$v){
                $info = $this->getAlsoPeriods($v['invest_id']);
                $list['data'][$k] = array_merge($list['data'][$k],$info);
            }             
            return $list;
            
        }

   }
?>
