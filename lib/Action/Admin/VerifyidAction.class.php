<?php
class VerifyIDAction extends AdminAction{
    public function index() {
        $pre = C('DB_PREFIX');
        $field = true;
        $map = array();

        if ($_REQUEST['status']) {
            $map['s.id_status'] = intval($_REQUEST['status']);
            $search['status'] = $map['s.id_status'];
        } else $map['s.id_status'] = array("in", "1,3");

        if ($_REQUEST['uname']) {
            $map['m.user_name'] = text($_REQUEST['uname']);
            $search['uname'] = $map['m.user_name'];
        }

        if ($_REQUEST['realname']) {
            $map['mi.real_name'] = urldecode($_REQUEST['realname']);
            $search['real_name'] = $map['mi.real_name'];
        }

        if ($_REQUEST['idcard']) {
            $map['mi.idcard'] = urldecode($_REQUEST['idcard']);
            $search['idcard'] = $map['mi.idcard'];
        }

        if (!empty($_REQUEST['start_time']) && !empty($_REQUEST['end_time'])) {
            $timespan = strtotime(urldecode($_REQUEST['start_time'])) . "," . strtotime(urldecode($_REQUEST['end_time']));
            $map['mi.up_time'] = array("between", $timespan);
            $search['start_time'] = urldecode($_REQUEST['start_time']);
            $search['end_time'] = urldecode($_REQUEST['end_time']);
        } elseif (!empty($_REQUEST['start_time'])) {
            $xtime = strtotime(urldecode($_REQUEST['start_time']));
            $map['mi.up_time'] = array("gt", $xtime);
            $search['start_time'] = $xtime;
        } elseif (!empty($_REQUEST['end_time'])) {
            $xtime = strtotime(urldecode($_REQUEST['end_time']));
            $map['mi.up_time'] = array("lt", $xtime);
            $search['end_time'] = $xtime;
        }

        //if(session('admin_is_kf')==1)	$map['m.customer_id'] = session('admin_id');

        import("ORG.Util.Page");
        $count = M('member_status s')->join("{$pre}member m ON m.id=s.uid")->join("{$pre}member_info mi ON mi.uid=s.uid")->where($map)->count('s.uid');
        $p = new Page($count, $this->pagesize);
        $page = $p->show();
        $Lsql = "{$p->firstRow},{$p->listRows}";
        $list = M('member_status s')->field('s.uid,s.id_status,mi.up_time,mi.card_img,mi.card_back_img,mi.real_name,mi.idcard,m.user_name')->join("{$pre}member m ON m.id=s.uid")->join("{$pre}member_info mi ON mi.uid=s.uid")->where($map)->order("mi.up_time DESC")->limit($Lsql)->select();
        $this->assign("status", array("1" => '已认证', "3" => '待认证'));
        $this->assign("search", $search);
        $this->assign("list", $list);
        $this->assign("pagebar", $page);
        $this->assign("query", http_build_query($search));
        $this->display();
    }

    public function export() {
        import("ORG.Io.Excel");
        alogs("Memberid", 0, 1, '执行了实名认证会员列表导出操作！');
         //管理员操作日志
        $pre = C('DB_PREFIX');

        $map = array();
        if ($_REQUEST['status']) {
            $map['s.id_status'] = intval($_REQUEST['status']);
            $search['status'] = $map['s.id_status'];
        } else $map['s.id_status'] = array("in", "1,3");

        if ($_REQUEST['uname']) {
            $map['m.user_name'] = text($_REQUEST['uname']);
            $search['uname'] = $map['m.user_name'];
        }

        if ($_REQUEST['realname']) {
            $map['mi.real_name'] = urldecode($_REQUEST['realname']);
            $search['real_name'] = $map['mi.real_name'];
        }

        if ($_REQUEST['idcard']) {
            $map['mi.idcard'] = urldecode($_REQUEST['idcard']);
            $search['idcard'] = $map['mi.idcard'];
        }

        if (!empty($_REQUEST['start_time']) && !empty($_REQUEST['end_time'])) {
            $timespan = strtotime(urldecode($_REQUEST['start_time'])) . "," . strtotime(urldecode($_REQUEST['end_time']));
            $map['mi.up_time'] = array("between", $timespan);
            $search['start_time'] = urldecode($_REQUEST['start_time']);
            $search['end_time'] = urldecode($_REQUEST['end_time']);
        } elseif (!empty($_REQUEST['start_time'])) {
            $xtime = strtotime(urldecode($_REQUEST['start_time']));
            $map['mi.up_time'] = array("gt", $xtime);
            $search['start_time'] = $xtime;
        } elseif (!empty($_REQUEST['end_time'])) {
            $xtime = strtotime(urldecode($_REQUEST['end_time']));
            $map['mi.up_time'] = array("lt", $xtime);
            $search['end_time'] = $xtime;
        }

        //if(session('admin_is_kf')==1)	$map['m.customer_id'] = session('admin_id');

        import("ORG.Util.Page");
        $count = M('member_status s')->join("{$pre}member m ON m.id=s.uid")->join("{$pre}member_info mi ON mi.uid=s.uid")->where($map)->count('s.uid');

        //$p = new Page($count, $this->pagesize);
        //$page = $p->show();
        //$Lsql = "{$p->firstRow},{$p->listRows}";
        $list = M('member_status s')->field('s.uid,s.id_status,mi.up_time,mi.card_img,mi.card_back_img,mi.real_name,mi.idcard,m.user_name')->join("{$pre}member m ON m.id=s.uid")->join("{$pre}member_info mi ON mi.uid=s.uid")->where($map)->order("mi.up_time DESC")->select();
         //->limit($Lsql)

        $row = array();
        $row[0] = array('序号', '用户ID', '用户名', '真实姓名', '身份证号', '上传时间', '认证状态');
        $i = 1;
        foreach ($list as $v) {
            $row[ $i ]['i'] = $i;
            $row[ $i ]['uid'] = $v['uid'];
            $row[ $i ]['user_name'] = $v['user_name'];
            $row[ $i ]['real_name'] = $v['real_name'];
            $row[ $i ]['idcard'] = $v['idcard'];
            $row[ $i ]['up_time'] = date("Y-m-d H:i:s", $v['up_time']);
            $row[ $i ]['id_status'] = ($v['id_status'] == 1) ? "已认证" : "待审核";
            $i++;
        }

        $xls = new Excel_XML('UTF-8', false, 'datalist');
        $xls->addArray($row);
        $xls->generateXML("datalistcard");
    }

    public function edit() {
        setBackUrl();
        $id = intval($_REQUEST['id']);
        $this->assign('uid', $id);
        $this->display();
    }

    public function doEdit() {
        $status = intval($_POST['status']);
        $uid = intval($_POST['id']);

        //$credits = intval($_POST['deal_credits']);

        $newxid = setMemberStatus($uid, 'id', $status, 2, '实名');
        if ($status == 1) {
            $data['status'] = 1;
            $data['deal_info'] = $deal_info;
            $new = M("apply_name")->where("uid={$uid}")->save($data);
        } else {
            $data['deal_info'] = $deal_info;
            $new = M("apply_name")->where("uid={$uid}")->save($data);
        }
        if ($newxid) {
            alogs("Memberid", $newxid, 1, '成功执行了会员实名认证的操作！备注信息：' . $deal_info);
             //管理员操作日志
            $this->success("审核成功", __URL__ . "/index" . session('listaction'));
        } else {
            alogs("Memberid", $newxid, 0, '执行会员实名认证的操作失败！备注信息：' . $deal_info);
             //管理员操作日志
            $this->error("审核失败");
        }
    }
}
?>