<?php

class AreaAction extends AdminAction
{
	var $typeleve=1;
	var $typeleve_default=1;
    /**
    +----------------------------------------------------------
    * 默认操作
    +----------------------------------------------------------
    */
    public function index()
    {
		$field= 'id,name,sort_order,reid';
		$this->_list(M('area'),$field,array("reid"=>0),'sort_order');
        $this->display();
    }
	
    public function add()
    {
		$arealist = M('area')->field('name,id')->select();
		$this->assign('area_list',$arealist);
        $this->display();
    }
	public function addmultiple(){
		$typelist = M('area')->field('name,id')->select();
		$this->assign('area_list',$typelist);
        $this->display();
	}
	
	public function doAddMul(){
		$mul_area=explode(",",$_POST['area_name']);
		$Area=M("area");
		foreach($mul_area as $v){
			$data=array();
			$data['name'] = $v;
			$data['reid'] = $_POST['reid'];
			$newid = $Area->add($data);
		}
		
        if($newid){
			alogs("AreaAdd",$newid,1,'地区批量添加成功！');//管理员操作日志
			$this->success("地区批量添加成功");
		}else{
			alogs("AreaAdd",$newid,0,'地区批量添加失败！');//管理员操作日志
			$this->error("添加失败");
		}
	}
	
    public function listType()
    {
		$areaid=intval($_REQUEST['typeid']);
		$sonlist = M('area')->field($field)->where("reid={$areaid}")->select();
		$sonlist = $this->_listFilter($sonlist);
		$list="";
		foreach($sonlist as $key=>$v){
		$leve = $this->_typeLeve($v['id']);
		$haveson=$v['haveson'];
		$dom = ($v['is_open']==1)?$v['domain']:"否";
		$list.='<tr overstyle="on" id="list_'.$v['id'].'" class="leve_'.$leve.'" typeid="'.$v['id'].'" parentid="'.$v['reid'].'">
				<td><input type="checkbox" name="checkbox" id="checkbox2" onclick="checkon(this)" value="'.$v['id'].'"></td>
				<td>'.$v['id'].'</td>
				<td>'.($haveson?'<span class="typeson typeon" data="son">&nbsp;</span>':'<span class="typeson">&nbsp;</span>').$v['name'].'</td>
				<td>'.$v['sort_order'].'</td>
				<td>'.$dom.'</td>
				<td>
					<a href="javascript:void(0);" onclick="edit(\'?id='.$v['id'].'\');">编辑</a> 
					<a href="javascript:void(0);" onclick="del('.$v['id'].');">删除</a>  
				</td>
			  </tr>';
		}
		

		$data['inner'] = $list;
		$data['typeid'] = $areaid;
		$this->ajaxReturn($data,"");
    }

	public function _doAddFilter($m){
		return $m;
	}

	public function _doEditFilter($m){
		return $m;
	}

	public function _editFilter($id){
		$id=intval($_REQUEST['id']);
		$condition['id'] = array('neq',$id);
		$arealist = M('area')->field('name,id')->where($condition)->select();
		$this->assign('area_list',$arealist);
	}

	public function _doDelFilter($id){
		$n = M('area')->where("reid in ({$id})")->count();
		if($n>0){
			alogs("AreaDel",0,0,'删除失败,所删除的栏目包含有子地区！');//管理员操作日志
			$this->error("删除失败,所删除的栏目包含有子地区");
			exit;
		}
	}
	
	public function _listFilter($list){
		$row=array();
		foreach($list as $key=>$v){
			$v['haveson']  = $this->_typeSon($v['id']);
			$row[$key]=$v;
		}
		return $row;
	}
	//获取栏目的上下级别
	protected function _typeLeve($typeid){
		static $rt=0;//先声明要返回静态变量,不然在下面被赋值时是引用赋值
		$condition['id'] = $typeid;
		$v = M('area')->field('reid')->where($condition)->find();
		if($v['reid']>0){
			$this->typeleve++;
			$this->_typeLeve($v['reid']);
		}else{
			$rt = $this->typeleve;
			$this->typeleve = $this->typeleve_default;
		}
		return $rt;
	}
	//获取栏目的上下级别
	protected function _typeSon($typeid){
		$condition['reid'] = $typeid;
		$v = M('area')->field('id')->where($condition)->find();
		if($v['id']>0){
			return true;
		}else{
			return false;
		}
	}
	
}
?>