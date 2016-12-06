<?php
class EventAction extends AdminAction{
	
    public function rank(){
    	if($_POST){
    		if($_POST['id']>0){
    			if(D('EventPeople')->save($_POST)){
    				$this->success(L('达人修改成功'),'',$id);
    			}else{
					$this->success(L('达人修改成功'),'',$id);
    			}
    		}else{
				if(D('EventPeople')->add($_POST)){
    				$this->success(L('达人添加成功'),'',$id);
    			}else{
					$this->success(L('达人添加成功'),'',$id);
    			}
    		}
    	}else{
	    	$this->assign('type',C('TRADE_TYPE'));
	        if($_GET['id']!=''){
	        	$this->assign('data',D('EventPeople')->find($_GET['id']));
				$this->display('rank');
	        }else{
	        	$this->people(0);
	        }
    	}
    }

    public function people($event=''){
    	if($event!=''){
    		$map['event'] = $event;
    	}
    	if($_GET['event']){
    		$this->assign('event',D('Event')->find(intval($_GET['event'])));
    		$map['event'] = intval($_GET['event']);
    	}
        import("ORG.Util.Page");		
		$event = D('EventPeople');		
		$count  = $event->where($map)->count(); // 查询满足要求的总记录数   
		$Page = new Page($count,$this->pagesize); // 实例化分页类传入总记录数和每页显示的记录数   
		$show = $Page->show(); // 分页显示输出
		   
		$fields = "*";
		$order = "`reward` DESC";

		$list = $event->field($fields)->where($map)->order($order)->limit($Page->firstRow.','.$Page->listRows)->select();

		$this->assign('pagebar', $show);
		$this->assign('list', $list);
        $this->display('people');
    }

}
?>