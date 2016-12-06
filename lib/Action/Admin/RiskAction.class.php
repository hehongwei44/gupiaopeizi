<?php
class RiskAction extends AdminAction{

    public function index(){
        if($_POST){
            $borrow = $this->borrow($_POST['id']);
            dump($_POST);
            if(intval($_POST['id'])>0){
                $profit = $_POST['total']-$borrow['deposit'];
                $log['borrow'] = $borrow['id'];
                if($profit>0){
                    $log['memo'] = "配资完结获得盈利，配资单号：PZ".$log['borrow'];
                    logMoney($borrow['borrow_uid'],1,$profit,$log);
                    $deposit = $borrow['deposit'];
                }else{
                    $deposit = $_POST['total'];
                }
                $log['memo'] = "配资完结返还保证金，配资单号：PZ".$log['borrow'];
                if(logMoney($borrow['borrow_uid'],81,$deposit,$log)){
                    D('Borrow')->where('id='.$borrow['id'])->save(array('borrow_status'=>7));
                    $this->success('配资方案已完结，HOMS帐号已回收。');
                }else{
                    $this->error('系统繁忙，请稍后重试。');
                }
            }

            exit;
        }

        if($_GET['do'] == 'reason' && !empty($_GET['id'])){

            $data = M('BorrowApply')->where(array('id'=>$_GET['id']))->getField('reason');
            $this->assign('data',$data);
            $this->display('reson');
            exit;
        }

        if($_GET['do']=='audit'){
            $borrow = $this->borrow(intval($_GET['id']));
            $this->assign('borrow', $borrow);
        }else{
            $map['b.homs_id'] = array('gt',0);
            $map['b.borrow_status'] = 6;
            if($_REQUEST['type']){
                $map['b.borrow_type'] = intval($_REQUEST['type']);
            }
            //分页处理
            import("ORG.Util.Page");
            $count = M('borrow b')->where($map)->count('b.id');
            $p = new Page($count, $this->pagesize);
            $page = $p->show();
            $Lsql = "{$p->firstRow},{$p->listRows}";
            //分页处理

            $field= 'b.id,b.borrow_name,b.borrow_uid,b.borrow_duration,b.borrow_type,b.updata,b.deadline,b.borrow_money,b.borrow_fee,b.borrow_interest_rate,b.deposit,b.deal_start,b.add_time,m.real_name,m.uid mid,b.risk_rate,h.name account';
            $list = M('borrow b')->field($field)->join("{$this->pre}member_info m ON m.uid=b.borrow_uid")->join("{$this->pre}homs h ON h.id=b.homs_id")->where($map)->limit($Lsql)->order("b.id DESC")->select();
            foreach($list as $k => $v){
                switch($v['borrow_type']){
                    case '9':
                        $setting = explode('|',$this->glo['stock_month']);
                        $v['warning'] = number_format($v['borrow_money']*$setting[2],2);
                        $v['finish'] = number_format($v['borrow_money']*$setting[3],2);
                        break;
                    case '8':
                        $setting = explode('|',$this->glo['stock_day']);
                        $v['warning'] = number_format($v['borrow_money']*$setting[2],2);
                        $v['finish'] = number_format($v['borrow_money']*$setting[3],2);
                        break;
                    case '7':
                        $setting = explode('|',$this->glo['stock_week']);
                        $v['warning'] = number_format($v['borrow_money']*$setting[2],2);
                        $v['finish'] = number_format($v['borrow_money']*$setting[3],2);
                        break;
                    case '6':
                        $setting = explode('|',$this->glo['stock_week']);
                        $v['warning'] = '/';
                        $v['finish'] = number_format($v['borrow_money']*1.07,2);
                        break;
                    case '5':
                        $setting = explode('|',$this->glo['stock_week']);
                        $v['warning'] = '/';
                        $v['finish'] = '/';
                        break;

                }
                $list[$k] = $v;
            }
            $this->assign("pagebar", $page);
            $this->assign("list", $list);
        }
        $this->display();

    }
    public function apply($type){
    	$map['b.homs_id'] = array('gt',0);
    	switch($type){
    		case 'renew':
    			$map['a.type'] = 0;
    			break;
    		case 'deposit':
    			$map['a.type'] = 1;
    			break;
    		case 'profit':
    			$map['a.type'] = 2;
    			break;
    		case 'stop':
    			$map['a.type'] = 9;
    			break;
    	}
		if($_REQUEST['status']!=''){
			$map['a.status'] = intval($_REQUEST['status']);
		}

		//分页处理
		import("ORG.Util.Page");
		$count = M('borrow b')->where($map)->count('b.id');
		$p = new Page($count, $this->pagesize);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理

        $field= 'a.id,a.total,a.status,a.date,b.id borrow,b.borrow_name,b.borrow_uid,b.borrow_duration,b.borrow_type,b.updata,b.deadline,b.borrow_money,b.borrow_fee,b.borrow_interest_rate,b.deposit,b.deal_start,b.add_time,m.user_name,m.id mid,b.risk_rate,h.name homs';
        $list = D('borrow b')->field($field)->join("{$this->pre}borrow_apply a ON a.bid=b.id")->join("{$this->pre}member m ON m.id=b.borrow_uid")->join("{$this->pre}homs h ON h.id=b.homs_id")->where($map)->limit($Lsql)->order("a.id DESC")->select();
        $this->assign("pagebar", $page);

        foreach($list as $k => $v){
            switch($v['borrow_type']){
                case '9':
                    $setting = explode('|',$this->glo['stock_month']);
                    $v['warning'] = number_format($v['borrow_money']*$setting[2],2);
                    $v['finish'] = number_format($v['borrow_money']*$setting[3],2);
                    break;
                case '8':
                    $setting = explode('|',$this->glo['stock_day']);
                    $v['warning'] = number_format($v['borrow_money']*$setting[2],2);
                    $v['finish'] = number_format($v['borrow_money']*$setting[3],2);
                    break;
                case '7':
                    $setting = explode('|',$this->glo['stock_week']);
                    $v['warning'] = number_format($v['borrow_money']*$setting[2],2);
                    $v['finish'] = number_format($v['borrow_money']*$setting[3],2);
                    break;
                case '6':
                    $setting = explode('|',$this->glo['stock_week']);
                    $v['warning'] = '/';
                    $v['finish'] = number_format($v['borrow_money']*1.07,2);
                    break;
                case '5':
                    $setting = explode('|',$this->glo['stock_week']);
                    $v['warning'] = '/';
                    $v['finish'] = '/';
                    break;

            }
            $list[$k] = $v;
        }

        $this->assign("list", $list);
        $this->display();
    }

    public function stop(){
        //处理审核
        if($_POST){
            $ba = D('BorrowApply');
            $data = $ba->where('id='.intval($_POST['id']))->find();
            if($_POST['status']==2){
                $borrow = $this->borrow($data['bid']);
                $profit = $_POST['total']-$borrow['deposit'];
                if($profit>0){
                    $log['borrow'] = $data['bid'];
                    $log['memo'] = '申请终结配资获得盈利，配资单号：PZ'.$log['borrow'];
                    logMoney($data['uid'],1,$profit,$log);
                    $deposit = $borrow['deposit'];
                }else{
                    $deposit = $_POST['total'];
                }
                $log['borrow'] = $data['bid'];
                $log['memo'] = '申请终结配资返还保证金，配资单号：PZ'.$log['borrow'];
                if(logMoney($data['uid'],81,$data['total'],$log)){
                    D('Borrow')->where('id='.$data['bid'])->save(array('borrow_status'=>7));
                }
            }
            $_POST['deal_time'] = time();
            $_POST['deal_auser'] = $_SESSION['admin_id'];
            $ba->save($_POST);
            $this->success('终结配资方案申请审核完成。');
        }else{
            if($_GET['do']=='audit'){
                $data = D('BorrowApply')->where('id='.intval($_GET['id']))->find();
                $borrow = $this->borrow($data['bid']);
                $data['total'] = $data['reason'];
                $data['tip'] = array('title'=>'终结原因','unit'=>'','info'=>' '.$borrow['real_name'].' 本次配资结束，工作人员须进行平仓和划转利润处理。');
                $this->assign('borrow', $borrow);
                $this->assign('data', $data);
            }
            $this->apply('stop');
        }
    }

    public function deposit(){
        //处理审核
        if($_POST){
            $ba = D('BorrowApply');
            $data = $ba->where('id='.intval($_POST['id']))->find();
            if($_POST['status']==2){
                D('Borrow')->where('id='.$data['bid'])->setInc('deposit',$data['total']);
                $log['borrow'] = $data['bid'];
                $log['memo'] = "保证金已划为操盘资金，配资单号：PZ".$log['borrow'];
                logMoney($data['uid'],80,$data['total'],$log);
            }
            $_POST['deal_time'] = time();
            $_POST['deal_auser'] = $_SESSION['admin_id'];
            $ba->save($_POST);
            $this->success('保证金追加申请，审核完成。');
        }else{
            if($_GET['do']=='audit'){
                $data = D('BorrowApply')->where('id='.intval($_GET['id']))->find();
                $borrow = $this->borrow($data['bid']);
                $data['tip'] = array('title'=>'追加金额','unit'=>'元','info'=>'将从 '.$borrow['real_name'].' 账户冻结款内扣除 <span style="color:red;font-size:17px">'.number_format($data['total'],2).'</span> 元');
                $this->assign('borrow', $borrow);
                $this->assign('data', $data);
            }
            $this->apply('deposit');
        }

    }

    public function renew(){
        //处理审核
        if($_POST){
            $ba = D('BorrowApply');
            $data = $ba->where('id='.intval($_POST['id']))->find();
            if($_POST['status']==2){
                $borrow = $this->borrow($data['bid']);
                D('Borrow')->where('id='.$data['bid'])->save(array('deadline'=>strtotime('+'.$data['total'].' month',$borrow['deadline'])));
            }
            $_POST['deal_time'] = time();
            $_POST['deal_auser'] = $_SESSION['admin_id'];
            $ba->save($_POST);
            $this->success('配资续约申请审核完成。');
        }else{
            if($_GET['do']=='audit'){
                $data = D('BorrowApply')->where('id='.intval($_GET['id']))->find();
                $borrow = $this->borrow($data['bid']);
                $data['tip'] = array('title'=>'配资时间','unit'=>'月','info'=>' '.$borrow['real_name'].' 本次配资将延长 <span style="color:red;font-size:17px">'.number_format($data['total']).'</span> 月');
                $this->assign('borrow', $borrow);
                $this->assign('data', $data);
            }
            $this->apply('renew');
        }
    }

    public function profit(){
        //处理审核
        if($_POST){
            $ba = D('BorrowApply');
            $data = $ba->where('id='.intval($_POST['id']))->find();
            if($_POST['status']==2){
                $log['borrow'] = $data['bid'];
                $log['memo'] = "提取配资盈利，配资单号：PZ".$log['borrow'];
                logMoney($data['uid'],1,$data['total'],$log);
            }
            $_POST['deal_time'] = time();
            $_POST['deal_auser'] = $_SESSION['admin_id'];
            $ba->save($_POST);
            $this->success('提取盈利申请审核完成。');
        }else{
            if($_GET['do']=='audit'){
                $data = D('BorrowApply')->where('id='.intval($_GET['id']))->find();
                $borrow = $this->borrow($data['bid']);
                $data['tip'] = array('title'=>'提取金额','unit'=>'元','info'=>'将从 '.$borrow['real_name'].' 操盘账户扣除 <span style="color:red;font-size:17px">'.number_format($data['total'],2).'</span> 元到可用余额');
                $this->assign('borrow', $borrow);
                $this->assign('data', $data);
            }
            $this->apply('profit');
        }
    }

    public function borrow($id){
        $type = C('TRADE_TYPE');
        $data = D('Borrow')->find(intval($id));
        $user = D('Member')->find(intval($data['borrow_uid']));
        $info = D('MemberInfo')->where('uid='.intval($data['borrow_uid']))->find();
        $data['user_name'] = $user['user_name'];
        $data['postfix'] = $data['borrow_type']=='9'?'月':'天';
        $data['borrow_type'] = $type[$data['borrow_type']];
        $data['real_name'] = $info['real_name'];
        $data['total'] = $data['deposit']+$data['borrow_fee'];
        $data['homs'] = D('homs')->where('`id`='.intval($data['homs_id']))->find();
        return $data;
    }
}
?>