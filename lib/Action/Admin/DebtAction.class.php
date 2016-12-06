<?php
    class DebtAction extends AdminAction{
        private $Debt;
        function _initialize(){
            parent::_initialize();
            D("DebtBehavior");
            $this->Debt = new DebtBehavior();
        }
        public function index(){
            $get_status = $this->_get("status");
            
            $status = '1';
            $get_status && $status =  " d.status = ".$get_status;
            stripos($get_status, ',') && $status = " d.status in ({$get_status})";

            $list = $this->Debt->adminList($status);
            $this->assign('list', $list);
            
            $template = '';
            $get_status == 3 && $template='list3';
            
            $this->display($template);
        }
        
        public function audit(){
            if($_POST['dosubmit']){
                $status = intval($this->_post('status', 'strip_tags','99'));
                $debt_id = intval($this->_post('debt_id', 'strip_tags', 0));
                $remark = '管理员：'.$this->_post('remark', 'htmlspecialchars');  
                $data = array(
                    'status'=>$status,
                    'remark'=>$remark,
                );
                if($status == 2){
                    $data['valid'] = time()+60*60*24*7;
                    if(!$result = M("investor_detb")->where("id={$debt_id}")->save($data)){
                        $this->error("审核失败", U("debt/index"));
                    }
                }elseif($status == 3){
                    $debt_info = M("investor_detb")->field("invest_id")->where("id={$debt_id}")->find();
                    M("investor_detb")->where("id={$debt_id}")->save($data);
                    M("borrow_investor")->where("id={$debt_info['invest_id']}")->save(array('debt_status'=>0));
                }
                $this->success("审核成功！", U("debt/index")); 
            }else{
                $debt_id = $this->_get('debt_id','strip_tags');
                $this->assign("debt_id", $debt_id);
                $this->display();    
            }
            
        }
    }
?>
