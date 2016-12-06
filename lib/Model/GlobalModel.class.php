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
class GlobalModel extends BaseModel {
	protected $_validate	=	array(
			array('code','',"参数代码不能为空",0,'unique',self::MODEL_INSERT),
			array('name','',"参数名称不能为空",0,'unique',self::MODEL_INSERT),
		);

    public function checkBindAccount() {
        $map['id']   = array('neq',$_POST['id']);
        $map['bind_account']    = $_POST['bind_account'];
        if($this->where($map)->find()) {
            return false;
        }
        return true;
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