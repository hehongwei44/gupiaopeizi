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
class ArticleModel extends BaseModel {
	protected $_validate	=	array(
			array('title','require',"标题不能为空"),
			array('type_id','require',"所属分类不能为空"),
			array('type_nid','checkNid',"在此同级分类已有相同nid,请重新设置nid",0,'callback',self::MODEL_BOTH),
		);

    public function checkNid() {
        if(!empty($_POST['id'])) $map['id']   = array('neq',$_POST['id']);
        $map['parent_id']    = intval($_POST['parent_id']);
        $map['type_nid']    = $_POST['type_nid'];
        if($this->where($map)->find()) {
            return false;
        }
        return true;
    }
}
?>