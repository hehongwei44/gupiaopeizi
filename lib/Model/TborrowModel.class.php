<?php
// +----------------------------------------------------------------------
// | ThinkPHP
// +----------------------------------------------------------------------
// | Copyright (c) 2008 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id$

// 管理用户模型
class TborrowModel extends BaseModel {
	protected $tableName = 'transfer_borrow'; 
/*	protected $_validate	=	array(
			array('type_name','require',"分类名称不能为空"),
			array('type_nid','require',"分类唯一标志不能为空"),
			array('type_nid','checkNid',"在此同级分类已有相同nid,请重新设置nid",0,'callback',self::MODEL_BOTH),
			array('type_nid','checkNids',"分类的唯一ID为系统路径有重复，会导致程序出错",0,'callback',self::MODEL_BOTH),
		);
*/
    public function checkNid() {
        if(!empty($_POST['id'])) $map['id']   = array('neq',$_POST['id']);
        $map['parent_id']    = intval($_POST['parent_id']);
        $map['type_nid']    = $_POST['type_nid'];
        if($this->where($map)->find()) {
            return false;
        }
        return true;
    }

    public function checkNids() {
        $map['parent_id']    = intval($_POST['parent_id']);
        $map['type_nid']    = strtolower($_POST['type_nid']);
		if(in_array($map['type_nid'],C('SYS_URL')) && $map['parent_id']==0) return false;
        else return true;
    }

	protected function pwdHash() {
		if(isset($_POST['password'])) {
			return pwdHash($_POST['password']);
		}else{
			return false;
		}
	}
}
?>