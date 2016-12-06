<?php
   class LogsAction extends AdminAction{

  //后台操作日志
   public function index(){
      $map=array();
      if($_GET['tab']!=''){
        $map['tstatus'] = $_GET['tab'];
      }
      if($_REQUEST['id']){
        $map['l.id'] = intval($_REQUEST['id']);
        $search['id'] = intval($_REQUEST['id']);  
      }
      
      if($_REQUEST['deal_ip']!=""){
        $map['l.deal_ip'] = intval($_REQUEST['deal_ip']);
        $search['deal_ip'] = intval($_REQUEST['deal_ip']);  
      }
      if(!empty($_REQUEST['start_time'])&& !empty($_REQUEST['end_time'])){
        $start_time = strtotime($_REQUEST['start_time']);
        $end_time = strtotime($_REQUEST['end_time']);
        $map['l.deal_time'] = array("between","{$start_time},{$end_time}");
        $search['start_time'] = $_REQUEST['start_time'];
        $search['end_time'] = $_REQUEST['end_time'];
      }
      //分页处理
      import("ORG.Util.Page");
      $count = M('users_log l')->where($map)->count('l.id');
      $p = new Page($count, $this->pagesize);
      $page = $p->show();
      $Lsql = "{$p->firstRow},{$p->listRows}";
      //分页处理

      $list = M('users_log l')->field(true)->where($map)->order("l.id DESC")->limit($Lsql)->select();
      $this->assign("list", $list);
      $this->assign("pagebar", $page);
      $this->assign("search", $search);
      $this->assign("query", http_build_query($search));
      $this->display();
   }
  //后台操作日志
  //删除后台操作日志
    public function clear(){
      $data = $_POST;
      
      foreach($data as $key => $v){
        $data[$key] = EnHtml($v);
      }
      
      $idarray = $data['idarr'];
      
      $delnum = M('users_log')->where("id in ({$idarray})")->delete(); 
      
      if($delnum){
        $a_data['success'] = $rid;
        $a_data['success_msg'] = "后台操作日志删除成功";
        $a_data['aid'] = $idarray;
      }else{
        $a_data['success'] = 0;
        $a_data['error_msg'] = "后台操作日志删除失败";
      }
      
      exit(json_encode($a_data));
    }
    //删除后台操作日志
  
    //删除近期一个月内的后台操作日志
    public function delete(){
      $map=array();
      $start = strtotime("-1 month",strtotime(date("Y-m-d",time())." 00:00:00"));
      $end = strtotime(date("Y-m-d",time())." 23:59:59");
      $map['deal_time'] = array(
              "between",
              "{$start},{$end}"
      );
      $delnum = M('users_log')->where($map)->delete(); 
      
      if($delnum){
        $this->success("近期一个月的后台操作日志删除成功");
      }else{
        $this->error("近期一个月的后台操作日志删除失败");
      }
    }
}
?>
