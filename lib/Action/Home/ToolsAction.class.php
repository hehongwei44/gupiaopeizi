<?php
class ToolsAction extends HomeAction {
    public function index(){
        $this->invest();
    }

    public function borrow(){
    	$this->data = 'empty';
        $this->param['page_title'] = '借款计算器';
        if($_POST){
            // var_dump($_POST);exit();
            $amount = round(floatval($_POST['amount']),4);//借款金额
            $date_limit = intval($_POST['date_limit']);//借款期限
            $rate = floatval($_POST['rate']);           //借款利率
            $reward_rate = floatval($_POST['reward_rate']);//借款奖励

            //$risk_reserve = floatval($_POST['risk_reserve']);//风险准备金
            $borrow_manage = floatval($_POST['borrow_manage']);//借款管理费

            $rate_type = (intval($_POST['rate_type'])==2)?2:1;//投资利率：1：年利率；2：日利率
            $date_type = (intval($_POST['date_type'])==2)?2:1;//投资类型：1：月；2：日

            $repayment_type = intval($_POST['repayment_type']);//借款类型
            if ($repayment_type !=1 && $rate_type==2)   $rate = $rate*365;//利率
            if ($repayment_type ==1 && $rate_type==1)   $rate = $rate/365;//
        
            $repay_detail['risk_reserve'] = 0;//round($amount*$risk_reserve/100,4);//风险准备金
            $repay_detail['borrow_manage'] = round($amount*$borrow_manage*$date_limit/100,2);//借款管理费
            $repay_detail['reward_money'] = round($amount*$reward_rate/100,2);//奖励
            $repay_detail['borrow_money'] = $amount - $repay_detail['risk_reserve'] - $repay_detail['borrow_manage'] - $repay_detail['reward_money'];
            switch ($repayment_type) {
                case '1'://按天到期还款
                    $repay_detail['repayment_money'] = round($amount*($rate*$date_limit+100)/100,2);
                    $repay_detail['interest'] = $repay_detail['repayment_money'] - $amount;
                    $repay_detail['day_apr'] = round(($repay_detail['repayment_money']-$repay_detail['borrow_money'])*100/($repay_detail['borrow_money']*$date_limit),2); 
                    $repay_detail['year_apr'] = round($repay_detail['day_apr']*365,2); 
                    $repay_detail['month_apr'] = round($repay_detail['day_apr']*365/12,2); 
                    break;
                case '4'://到期还本息
                    $repay_detail['repayment_money'] = round($amount*($date_limit*$rate/12+100)/100,2);
                    $repay_detail['interest'] = $repay_detail['repayment_money'] - $amount;
                    $repay_detail['month_apr'] = round(($repay_detail['repayment_money']-$repay_detail['borrow_money'])*100/($repay_detail['borrow_money']*$date_limit),2); 
                    $repay_detail['year_apr'] = round($repay_detail['month_apr']*12,2); 
                    $repay_detail['day_apr'] = round($repay_detail['month_apr']*12/365,2);
                    break;
                case '3'://每月还息到期还本
                    $repay_detail['repayment_money'] = round($amount*($rate*$date_limit/12+100)/100,2);
                    $repay_detail['interest'] = $repay_detail['repayment_money'] - $amount;
                    $repay_detail['month_apr'] = round(($repay_detail['repayment_money']-$repay_detail['borrow_money'])*100/($repay_detail['borrow_money']*$date_limit),2); 
                    $repay_detail['year_apr'] = round($repay_detail['month_apr']*12,2); 
                    $repay_detail['day_apr'] = round($repay_detail['month_apr']*12/365,2);

                    $interest = round($amount*$rate/12/100,2);//利息等于应还金额乘月利率
                    for($i=0;$i<$date_limit;$i++){
                        if ($i+1 == $date_limit)    $capital = $amount;//本金只在最后一个月还，本金等于借款金额除季度
                        else    $capital = 0;
      
                        $_result[$i]['repayment_money'] = $interest+$capital;
                        $_result[$i]['interest'] = $interest;
                        $_result[$i]['capital'] = $capital;
                    }
                    break;
                case '5'://先息后本
                    $repay_detail['interest'] = round($amount*$rate*$date_limit/12/100,2);
                    $repay_detail['borrow_money'] -= $repay_detail['interest'];
                    $repay_detail['repayment_money'] = $amount;

                    $repay_detail['month_apr'] = round(($repay_detail['repayment_money']-$repay_detail['borrow_money'])*100/($repay_detail['borrow_money']*$date_limit),2); 
                    $repay_detail['year_apr'] = round($repay_detail['month_apr']*12,2); 
                    $repay_detail['day_apr'] = round($repay_detail['month_apr']*12/365,2);
                    break;
                case '2'://按月分期还款
                default:
                    $month_apr = $rate/(12*100);
                    $_li = pow((1+$month_apr),$date_limit);
                    $repayment = ($_li!=1)?round($amount * ($month_apr * $_li)/($_li-1),2):round($amount/$date_limit,2);
                    $repay_detail['repayment_money'] = $repayment*$date_limit;
                    $repay_detail['interest'] = $repay_detail['repayment_money'] - $amount;

                    for($i=0;$i<$date_limit;$i++){
                        if ($i==0){
                            $interest = round($amount*$month_apr,2);
                        }else{
                            $_lu = pow((1+$month_apr),$i);
                            $interest = round(($amount*$month_apr - $repayment)*$_lu + $repayment,2);
                        }
                        $_result[$i]['repayment_money'] = getFloatValue($repayment,2);
                        $_result[$i]['interest'] = getFloatValue($interest,2);
                        $_result[$i]['capital'] = getFloatValue($repayment-$interest,2);
                    }

                    $month_apr2 = ($repay_detail['repayment_money']-$repay_detail['borrow_money'])/($repay_detail['borrow_money']*$date_limit);
                    $rekursiv = 0.001;
                    for ($i=0; $i < 100; $i++) { 
                        $_li2 = pow((1+$month_apr2),$date_limit);
                        $repay = $repay_detail['borrow_money'] * $date_limit * ($month_apr2 * $_li2)/($_li2-1);
                        if($repay<$repay_detail['repayment_money']*0.99999) {
                            $month_apr2 += $rekursiv;
                        }elseif($repay>$repay_detail['repayment_money']*1.00001) {
                            $month_apr2 -= $rekursiv*0.9;
                            $rekursiv *= 0.1;
                        }else break;
                    }
                    $repay_detail['month_apr'] = round($month_apr2*100,2); 
 
                    $repay_detail['year_apr'] = round($repay_detail['month_apr']*12,2); 
                    $repay_detail['day_apr'] = round($repay_detail['month_apr']*12/365,2);
                    break;
            }
            $repay_detail['total_interest'] = round($repay_detail['repayment_money'] - $repay_detail['borrow_money'],2);

            $this->assign('repayment_type',$repayment_type);
            $this->assign('month',$date_limit);
            $this->assign('repay_list',$_result);
            $this->assign('repay_detail',$repay_detail);
            
            $data['html'] = $this->fetch('borrow_res');
            exit(json_encode($data));
        }

        
		$this->display();
    }

    public function invest(){
    	$this->data = 'empty';
        $this->param['page_title'] = '投资计算器';
        if($_POST){
            // var_dump($_POST);exit();
            $amount = round(floatval($_POST['amount']),2);//投资金额
            $date_limit = intval($_POST['date_limit']);//投资期限
            $rate = floatval($_POST['rate']);//投资利率
            $reward_rate = floatval($_POST['reward_rate']);//借款奖励
            $invest_manage = floatval($_POST['invest_manage']);//利息管理费

            $rate_type = (intval($_POST['rate_type'])==2)?2:1;//投资利率：1：年利率；2：日利率
            $date_type = (intval($_POST['date_type'])==2)?2:1;//投资类型：1：月；2：日

            $repayment_type = intval($_POST['repayment_type']);
            if ($repayment_type !=1 && $rate_type==2)   $rate = $rate*365;
            if ($repayment_type ==1 && $rate_type==1)   $rate = $rate/365;
        
            $repay_detail['reward_money'] = round($amount*$reward_rate/100,2);
            $repay_detail['invest_money'] = $amount - $repay_detail['reward_money'];
            switch ($repayment_type) {
                case '1'://按天到期还款
                    $repay_detail['repayment_money'] = round($amount*($rate*$date_limit*(100-$invest_manage)/100+100)/100,2);
                    $repay_detail['interest'] = $repay_detail['repayment_money'] - $amount;
                    $repay_detail['day_apr'] = round(($repay_detail['repayment_money']-$repay_detail['invest_money'])*100/($repay_detail['invest_money']*$date_limit),2); 
                    $repay_detail['year_apr'] = round($repay_detail['day_apr']*365,2); 
                    $repay_detail['month_apr'] = round($repay_detail['day_apr']*365/12,2); 
                    break;
                case '4'://到期还本息
                    $repay_detail['repayment_money'] = round(($amount+$amount*($date_limit*$rate/12/100)*(100-$invest_manage)/100),2); 
                    $repay_detail['interest'] = $repay_detail['repayment_money'] - $amount;
                    $repay_detail['month_apr'] = round(($repay_detail['repayment_money']-$repay_detail['invest_money'])*100/($repay_detail['invest_money']*$date_limit),2); 
                    $repay_detail['year_apr'] = round($repay_detail['month_apr']*12,2); 
                    $repay_detail['day_apr'] = round($repay_detail['month_apr']*12/365,2);
                    break;
                case '3'://每月还息到期还本
                    $repay_detail['repayment_money'] = round($amount*($rate*$date_limit*(100-$invest_manage)/100/12+100)/100,2);
                    $repay_detail['interest'] = $repay_detail['repayment_money'] - $amount;
                    $repay_detail['month_apr'] = round(($repay_detail['repayment_money']-$repay_detail['invest_money'])*100/($repay_detail['invest_money']*$date_limit),2); 
                    $repay_detail['year_apr'] = round($repay_detail['month_apr']*12,2); 
                    $repay_detail['day_apr'] = round($repay_detail['month_apr']*12/365,2);

                    $interest = round($amount*$rate*(100-$invest_manage)/100/12/100,2);//利息等于应还金额乘月利率
                    $repay = $repay_detail['repayment_money'];
                    for($i=0;$i<$date_limit;$i++){
                        if ($i+1 == $date_limit){
                            $capital = $amount;//本金只在最后一个月还，本金等于借款金额除季度
                            $repay = $interest+$capital;
                        }else{
                            $capital = 0;
                            $repay = $repay- $interest;
                        }   
      
                        $_result[$i]['repayment_money'] = $interest+$capital;
                        $_result[$i]['interest'] = $interest;
                        $_result[$i]['capital'] = $capital;
                        $_result[$i]['last_money'] = $repay;
                    }
                    break;
                case '5'://先息后本
                    $repay_detail['interest'] = round(($amount*($rate/12/100)*$date_limit)*((100-$invest_manage)/100),2);
                    $repay_detail['invest_money'] -= $repay_detail['interest'];
                    $repay_detail['repayment_money'] = $amount; 

                    $repay_detail['month_apr'] = round(($repay_detail['repayment_money']-$repay_detail['invest_money'])*100/($repay_detail['invest_money']*$date_limit),2); 
                    $repay_detail['year_apr'] = round($repay_detail['month_apr']*12,2); 
                    $repay_detail['day_apr'] = round($repay_detail['month_apr']*12/365,2);
                    break;
                case '2'://按月分期还款
                default:
                    $month_apr = $rate/(12*100);
                    $_li = pow((1+$month_apr),$date_limit);
                    $repayment = ($_li!=1)?round($amount * ($month_apr * $_li)/($_li-1),2):round($amount/$date_limit,2);
                    $repay_detail['repayment_money'] = round(($repayment*$date_limit-$amount)*(100-$invest_manage)/100+$amount,2);
                    $repay_detail['interest'] = $repay_detail['repayment_money'] - $amount;

                    $repay = $repay_detail['repayment_money'];
                    for($i=0;$i<$date_limit;$i++){
                        if ($i==0){
                            $interest = round($amount*$month_apr,2);
                        }else{
                            $_lu = pow((1+$month_apr),$i);
                            $interest = round(($amount*$month_apr - $repayment)*$_lu + $repayment,2);
                        }
                        $fee = $interest*$invest_manage/100;

                        $_result[$i]['repayment_money'] = getFloatValue($repayment-$fee,2);
                        $_result[$i]['interest'] = getFloatValue($interest-$fee,2);
                        $_result[$i]['capital'] = getFloatValue($repayment-$interest,2);

                        if($i+1 != $date_limit) $repay = $repay-$_result[$i]['repayment_money'];
                        else $repay = 0;
                        $_result[$i]['last_money'] = $repay;
                    }

                    $month_apr2 = ($repay_detail['repayment_money']-$repay_detail['invest_money'])/($repay_detail['invest_money']*$date_limit);
                    $rekursiv = 0.001;
                    for ($i=0; $i < 100; $i++) { 
                        $_li2 = pow((1+$month_apr2),$date_limit);
                        $repay = $repay_detail['invest_money'] * $date_limit * ($month_apr2 * $_li2)/($_li2-1);
                        if($repay<$repay_detail['repayment_money']*0.99999) {
                            $month_apr2 += $rekursiv;
                        }elseif($repay>$repay_detail['repayment_money']*1.00001) {
                            $month_apr2 -= $rekursiv*0.9;
                            $rekursiv *= 0.1;
                        }else break;
                    }
                    $repay_detail['month_apr'] = round($month_apr2*100,2); 
 
                    $repay_detail['year_apr'] = round($repay_detail['month_apr']*12,2); 
                    $repay_detail['day_apr'] = round($repay_detail['month_apr']*12/365,2);
                    break;
            }
            $repay_detail['total_interest'] = round($repay_detail['repayment_money'] - $repay_detail['invest_money'],2);

            $this->assign('repayment_type',$repayment_type);
            $this->assign('month',$date_limit);
            $this->assign('repay_list',$_result);
            $this->assign('repay_detail',$repay_detail);
            
            $data['html'] = $this->fetch('invest_res');
            exit(json_encode($data));
        }

        $this->display('invest');
    }

}