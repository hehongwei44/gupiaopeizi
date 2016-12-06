<?php
class EventAction extends HomeAction {
    public function init(){
        if(is_numeric(ACTION_NAME)){
            $this->show(ACTION_NAME);
            exit;
        }
    }

    public function index(){
    	$this->data = 'empty';
		$this->experience();
    }

    public function experience(){
    	$this->data=explode('|',$this->param['stock_try']);
		$this->display('experience');
    }

    public function match(){
        $now = time();
        //右侧排行
        $nowTopMap['date'] = array('between',array(strtotime(date('Y-m-01 00:00:00')),strtotime(date('Y-m-t 23:59:59'))));
        $this->param['top']['now'] = D('EventPeople')->field('CONCAT(left(`name`,2),"***",right(`name`,1)) `name`,`total`+`income` `capital`')->where()->order('`capital` DESC')->limit(10)->select();
        $this->param['top']['month'][] = date('m');

        $last = strtotime('-1 month');
        $nowTopMap['date'] = array('between',array(strtotime(date('Y-m-01 00:00:00',$last)),strtotime(date('Y-m-t 23:59:59',$last))));
        $this->param['top']['pre'] = D('EventPeople')->field('CONCAT(left(`name`,2),"***",right(`name`,1)) `name`,`total`+`income` `capital`')->where()->order('`capital` DESC')->limit(10)->select();
        $this->param['top']['month'][] = date('m',$last);

        //大赛数据
        $map['status'] = array('gt',0);
    	$data = D('Event')->where($map)->order('id DESC')->limit(12)->select();
        foreach($data as $val){
            if($now<$val['finish']&&$now>$val['tart']){
                $val['status']='报名中';
                $val['flag']='';
                $val['left'] = ceil(($val['finish']-time())/86400).'天';
                $val['button']='马上报名';
                $val['class']='btnBg3';
            }
            if($now>$val['begin']&&$now<$val['end']){
                $val['status']='进行中';
                $val['flag']='in';
                $val['button']='查看比赛';
                $val['class']='btnBg1';
            }
            if($now>$val['end']){
                $val['status']='已结束';
                $val['flag']='over';
                $val['button']='比赛结果';
                $val['class']='btnBg2';
            }
            $this->data[]=$val;
        }
        $this->param['reward'] = explode(',',$this->data[0]['reward']);
		$this->display();
    }

    public function show($id){
        $this->data = D('Event')->find($id);
        $this->data['left'] = ceil(($this->data['finish']-time())/86400);
        $this->data['reward'] = explode(',',$this->data['reward']);
        $this->data['total'] = $this->data['quota']+$this->data['fee'];
        $this->data['lowest'] = $this->data['lowest']*$this->data['quota'];
        $this->data['duration'] = ceil(($this->data['end']-$this->data['begin'])/86400);

        //右侧排行
        $nowTopMap['date'] = array('between',array(strtotime(date('Y-m-01 00:00:00'),$this->data['begin']),strtotime(date('Y-m-t 23:59:59',$this->data['begin']))));
        $this->param['top']['now'] = D('EventPeople')->field('CONCAT(left(`name`,2),"***",right(`name`,1)) `name`,`total`+`income` `capital`')->where()->order('`capital` DESC')->limit(10)->select();
        $this->param['top']['month'][] = date('m',$this->data['begin']);

        $this->display('show');
    }


}