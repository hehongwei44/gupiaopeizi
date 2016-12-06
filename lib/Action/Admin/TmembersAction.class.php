<?php

class TmembersAction extends AdminAction
{
    /**
    +----------------------------------------------------------
    * 默认操作
    +----------------------------------------------------------
    */
    public function index()
    {
		$list = getTMemberList(array(),10);
		$this->assign("list",$list['list']);
		$this->assign("pagebar",$list['page']);
        $this->display();
    }
	
    public function doAdd()
    {	
		$udata['user_name'] = text($_POST['user_name']);
		$cs = M('member')->where($udata)->count('id');
		if($cs>0){
			$this->error("添加失败，此用户名已被占用，请重试");
			exit;
		}
		if(empty($udata['user_name'])){
			$this->error("用户名不能为空，请重试");
			exit;
		}
		$udata['is_transfer'] = 1;
		$udata['reg_ip'] = get_client_ip();
		$udata['user_leve'] = 1;
		$udata['time_limit'] = time()+24*3600*365;
		$udata['user_pass'] = md5(time().rand(10,99));
		$udata['reg_time'] = strtotime( $_POST['reg_time']." ".date("H:i:s",time()) );
		$newid = M('member')->add($udata);
		if($newid){
			$idata['uid'] = $newid;
			$idata['real_name'] = text($_POST['real_name']);
			$idata['address'] = text($_POST['address']);
			$idata['info'] = text($_POST['info']);
			M('member_info')->add($idata);
			alogs("Tmember",$newid,1,'成功执行了流转会员的添加操作！');//管理员操作日志
			$this->success("添加成功",__URL__."/index");
		}else{
			logs("Tmember",$newid,0,'执行流转会员的添加操作失败！');//管理员操作日志
			$this->error("添加失败，请重试");
		}
    }
	
    public function edit()
    {	
		$pre = C('DB_PREFIX');
		$id = intval($_GET['id']);
		$vo = M('member m')->field("m.id,m.user_name,m.reg_time,m.user_email,mf.info,mf.address,mf.real_name")->join("{$pre}member_info mf ON m.id=mf.uid")->where("m.id={$id}")->find();
		$this->assign("vo",$vo);
        $this->display();
    }
	
	//////////////新增企业直投会员修改  fan 2013-01-30/////////////////////
	public function doEdit() {
        $model = M("member");
        $model2 = M("member_info");
		
        if (false === $model->create()) {
            $this->error($model->getError());
        }
        if (false === $model2->create()) {
            $this->error($model->getError());
        }
		$model->startTrans();
        //保存当前数据对象
		$model->user_name = $_POST['user_name'];
		$model->reg_time = strtotime( $_POST['reg_time']." ".date("H:i:s",time()) );
		$model2->real_name = text($_POST['real_name']);
		$model2->address = text($_POST['address']);
		$model2->info = text($_POST['info']);
		$model2->uid = $model->id;
		
		$result = $model->save();
		$result2 = $model2->save();
        if ($result&&$result2) { //保存成功
			$model->commit();
			alogs("Tmember",0,1,'成功执行了流转会员的编辑操作！');//管理员操作日志
          //成功提示
            $this->assign('jumpUrl', __URL__);
            $this->success(L('修改成功'));
        } else {
			alogs("Tmember",0,0,'执行流转会员的编辑操作失败！');//管理员操作日志
			$model->rollback() ;
            //失败提示
            $this->error(L('修改失败'));
        }
    }
	//////////////新增企业直投会员修改  fan 2013-01-30/////////////////////
}
?>