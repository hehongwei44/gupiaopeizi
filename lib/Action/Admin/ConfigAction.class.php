<?php
header("Content-type:text/html;charset=utf-8");
	
class ConfigAction extends AdminAction{
    public function index(){
		$integration = FS("data/conf/integration");
		$this->assign('integration', $integration);
		
		$borrowconfig = FS("data/conf/borrow");
		
		$bc=array_values($borrowconfig);
		$buse=$borrowconfig['BORROW_USE'];
		$bmin=$borrowconfig['BORROW_MIN'];
		$bmax=$borrowconfig['BORROW_MAX'];
		$btime=$borrowconfig['BORROW_TIME'];
		//$brepa=$borrowconfig['REPAYMENT_TYPE'];
		//$btype=$borrowconfig['BORROW_TYPE'];
		//$breward=$borrowconfig['IS_REWARD'];
		//$bstatus=$borrowconfig['BORROW_STATUS'];
		$bsearch=$borrowconfig['MONEY_SEARCH'];
		$bdatatype=$borrowconfig['DATA_TYPE'];
		$bbankname=$borrowconfig['BANK_NAME'];
		
		
		
		$this->assign('buse',$buse);
		$this->assign('bmin',$bmin);
		$this->assign('bmax',$bmax);
		$this->assign('btime',$btime);
		//$this->assign('brepa',$brepa);
		//$this->assign('btype',$btype);
		//$this->assign('breward',$breward);
		//$this->assign('bstatus',$bstatus);
		$this->assign('bsearch',$bsearch);
		$this->assign('bdatatype',$bdatatype);
		$this->assign('bbankname',$bbankname);
		
		$list = M('global')->where('is_sys=2')->order("order_sn DESC")->select();
		$this->assign('list', de_xie($list));

        $this->display();
    }
    
    public function save(){
		
		function array_combines($arr){
			$avv=array();
			$auu=array();
			
			foreach($arr as $key=>$v){
				if($v===''){
					exit('<script> alert(\'填入数据不能为空\'); window.location.href="/admin/Bdata/conf/index";</script>');
					
				}
				if($key%2==0){
				$avv[]=$v;
					if(count(array_unique(array_values(array_count_values($avv))))>1){
						//dump($avv);
						exit('<script> alert("该值已存在，参数不允许重复！"); window.location.href="/admin/Bdata/conf/index";</script>');
					}
				}else{
					if(count(array_unique(array_values(array_count_values($avv))))>1){
						exit('<script> alert("该值已存在，参数不允许重复！"); window.location.href="/admin/Bdata/conf/index";</script>');
					}
					$auu[]=$v;
				}
			}
			$amm=array_combine($avv,$auu);
			return $amm;
		}
		
		
		$arr1=$_POST['borrow']['BORROW_USE'];
		$_POST['borrow']['BORROW_USE']=array_combines($arr1);
		
		$arr2=$_POST['borrow']['BORROW_MIN'];
		$_POST['borrow']['BORROW_MIN']=array_combines($arr2);
		
		$arr3=$_POST['borrow']['BORROW_MAX'];
		$_POST['borrow']['BORROW_MAX']=array_combines($arr3);
		
		$arr4=$_POST['borrow']['BORROW_TIME'];
		$_POST['borrow']['BORROW_TIME']=array_combines($arr4);
		
		$arr9=$_POST['borrow']['MONEY_SEARCH'];
		$_POST['borrow']['MONEY_SEARCH']=array_combines($arr9);
		
		$arr10=$_POST['borrow']['DATA_TYPE'];
		$_POST['borrow']['DATA_TYPE']=array_combines($arr10);
		
		$arr11=$_POST['borrow']['BANK_NAME'];
		$_POST['borrow']['BANK_NAME']=array_combines($arr11);
		
		FS("borrow",$_POST['borrow'],"data/conf/");
        
        $integration = $this->integration_array($_POST['integration']);
		FS("integration",$integration,"data/conf/"); 

		foreach($_POST['gid'] as $k => $v){
			if(is_numeric($k)) M('Global')->where("id = '{$k}'")->setField('text',EnHtml($v));
		}
	
		$this->success("操作成功",__URL__."/index/");
    }
	
	 /**
    * 将多维数组合并
    * 
    * @param mixed $arr
    */
    private function  integration_array($arr){
        if(!is_array($arr['parameter'])){
            return false;
        } 
        foreach($arr['parameter'] as $key=>$val){
            if(empty($val)){
                continue;
            }
            $array[$val]['fraction'] = $arr['fraction'][$key]; 
            $array[$val]['description'] = $arr['description'][$key]; 
        }
        
        return $array;
    }
}
?>