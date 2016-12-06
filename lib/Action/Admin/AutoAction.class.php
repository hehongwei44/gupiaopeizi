<?php

class AutoAction extends AdminAction {
	private $updir = NULL;
	public function _MyInit(){
		Mheader("gbk");
		$this->updir = dirname(C("WEB_ROOT"))."/AutoDo/";
	}
	public function index(){
		$res = file_get_contents($this->updir."config.txt");
		$this->assign("vo",explode("|",$res));
		$this->display();
	}
	public function save(){
		$str = text($_POST['o_time'])."|";
		$str .= text($_POST['o_rate'])."|";
		$str .= text($_POST['o_key']);
		$res = file_put_contents($this->updir."config.txt",$str);
		if($res){
			alogs("Auto",0,1,'自动值守程序参数修改成功！');//管理员操作日志
			$this->success("保存成功,如执行时间有改动，请重启程序");
		}else{
			alogs("Auto",0,0,'自动值守程序参数修改失败！');//管理员操作日志
			$this->error("保存失败,请重试");
		}
	}
	
	public function start(){
		exec($this->updir."l_start_zs.exe -1",$out,$status);
		print_r($out);
	}
	public function close(){
		$s = exec($this->updir."l_close_zs.exe -1",$out,$status);
		print_r($out);
	}
	public function startServer(){
		exec($this->updir."startserver.exe -1",$out,$status);
		print_r($out);
	}
	public function stopServer(){
		exec($this->updir."stopserver.exe -1",$out,$status);
		print_r($out);
	}
	public function showstatus(){
		exec($this->updir."showstatus.exe -1",$out,$status);
		print_r($out);
	}
}