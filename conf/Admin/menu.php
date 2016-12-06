<?php
$i=0;
$j=0;
$menu_left =  array();
$menu_left[$i]=array('我的','#',1);
$menu_left[$i]['low_title'][$i."-".$j] = array('工作台','#',1);
$menu_left[$i][$i."-".$j][] = array('欢迎页',U('/admin/welcome/index'),1);

$j++;
$menu_left[$i]['low_title'][$i."-".$j] = array('个人设置','#',1);
$menu_left[$i][$i."-".$j][] = array('基本资料',U('/admin/main/profile'),1);
$menu_left[$i][$i."-".$j][] = array('修改密码',U('/admin/main/password'),1);
$menu_left[$i][$i."-".$j][] = array('使用偏好',U('/admin/main/setting'),1);
$i++;
$menu_left[$i]= array('股票配资','#',1);
$menu_left[$i]['low_title'][$i."-".$j] = array('按月配资','#',1);
$menu_left[$i][$i."-".$j][] = array('按月配资审核',U('/admin/trade/index','status=0&type=9'),1);
$menu_left[$i][$i."-".$j][] = array('已作废按月配资申请',U('/admin/trade/index','status=1&type=9'),1);
$menu_left[$i][$i."-".$j][] = array('操盘中按月配资',U('/admin/trade/index','status=6&type=9'),1);
$menu_left[$i][$i."-".$j][] = array('到期按月配资',U('/admin/trade/index','status=8&type=9'),1);
$menu_left[$i][$i."-".$j][] = array('按月配资完成',U('/admin/trade/index','status=7&type=9'),1);

// $j++;
// $menu_left[$i]['low_title'][$i."-".$j] = array('按周配资','#',1);
// $menu_left[$i][$i."-".$j][] = array('按周配资审核',U('/admin/trade/index','status=0&type=7'),1);
// $menu_left[$i][$i."-".$j][] = array('已作废按周配资申请',U('/admin/trade/index','status=1&type=7'),1);
// $menu_left[$i][$i."-".$j][] = array('操盘中按周配资',U('/admin/trade/index','status=6&type=7'),1);
// $menu_left[$i][$i."-".$j][] = array('到期按周配资',U('/admin/trade/index','status=8&type=7'),1);
// $menu_left[$i][$i."-".$j][] = array('按周配资完成',U('/admin/trade/index','status=7&type=7'),1);

$j++;
$menu_left[$i]['low_title'][$i."-".$j] = array('按天配资','#',1);
$menu_left[$i][$i."-".$j][] = array('按天配资审核',U('/admin/trade/index','status=0&type=8'),1);
$menu_left[$i][$i."-".$j][] = array('已作废按天配资申请',U('/admin/trade/index','status=1&type=8'),1);
$menu_left[$i][$i."-".$j][] = array('操盘中按天配资',U('/admin/trade/index','status=6&type=8'),1);
$menu_left[$i][$i."-".$j][] = array('到期按天配资',U('/admin/trade/index','status=8&type=8'),1);
$menu_left[$i][$i."-".$j][] = array('按天配资完成',U('/admin/trade/index','status=7&type=8'),1);

$j++;
$menu_left[$i]['low_title'][$i."-".$j] = array('操盘达人','#',1);
$menu_left[$i][$i."-".$j][] = array('添加达人',U('/admin/event/rank','id=0'),1);
$menu_left[$i][$i."-".$j][] = array('达人列表',U('/admin/event/rank'),1);


$i++;
$menu_left[$i]= array('风控管理','#',1);
$menu_left[$i]['low_title'][$i."-".$j] = array('风控方案','#',1);
$menu_left[$i][$i."-".$j][] = array('方案列表',U('/admin/risk/index'),1);
$menu_left[$i][$i."-".$j][] = array('方案终结审核',U('/admin/risk/stop','status=0'),1);
$menu_left[$i][$i."-".$j][] = array('追加配资保证金',U('/admin/risk/deposit','status=0'),1);
$menu_left[$i][$i."-".$j][] = array('提取配资利润',U('/admin/risk/profit','status=0'),1);

$j++;
$menu_left[$i]['low_title'][$i."-".$j] = array('续约申请表','#',1);
$menu_left[$i][$i."-".$j][] = array('配资续约申请审核',U('/admin/risk/renew','status=0'),1);
$menu_left[$i][$i."-".$j][] = array('配资续约申请记录',U('/admin/risk/renew','status=2'),1);

$j++;
$menu_left[$i]['low_title'][$i."-".$j] = array('待扣利息/费用','#',1);
$menu_left[$i][$i."-".$j][] = array('按天配资待扣费用',U('/admin/trade/index'),1);
$menu_left[$i][$i."-".$j][] = array('按月配资待扣利息',U('/admin/trade/index'),1);

$j++;
$menu_left[$i]['low_title'][$i."-".$j] = array('恒生账户','#',1);
$menu_left[$i][$i."-".$j][] = array('添加恒生账户',U('/admin/homs/add'),1);
$menu_left[$i][$i."-".$j][] = array('使用中账户列表',U('/admin/homs/index','status=1'),1);
$menu_left[$i][$i."-".$j][] = array('恒生清仓账户',U('/admin/homs/index','status=0'),1);

$i++;
$menu_left[$i]= array('会员管理','#',1);
$menu_left[$i]['low_title'][$i."-".$j] = array('会员管理','#',1);
$menu_left[$i][$i."-".$j][] = array('全部会员',U('/admin/member/index'),1);
$menu_left[$i][$i."-".$j][] = array('黑名单会员',U('/admin/member/index','type=black'),1);
$menu_left[$i][$i."-".$j][] = array('举报信息',U('/admin/jubao/index'),1);


$j++;
$menu_left[$i]['low_title'][$i."-".$j] = array('推荐人管理','#',1);
$menu_left[$i][$i."-".$j][] = array('投资记录',U('/admin/invite/index'),1);

$j++;

$menu_left[$i]['low_title'][$i."-".$j] = array('认证及申请管理','#',1);
$menu_left[$i][$i."-".$j][] = array('VIP申请管理',U('/admin/verifyvip/index'),1);
$menu_left[$i][$i."-".$j][] = array('会员实名认证申请',U('/admin/verifyid/index'),1);
$menu_left[$i][$i."-".$j][] = array('上传资料管理',U('/admin/verifyinfo/index'),1);

$j++;
$menu_left[$i]['low_title'][$i."-".$j] = array('代理加盟','#',1);
$menu_left[$i][$i."-".$j][] = array('加盟申请',U('/admin/agent/index'),1);

$i++;
$menu_left[$i]= array('积分体系','#',1);
$menu_left[$i]['low_title'][$i."-".$j] = array('投资积分','#',1);
$menu_left[$i][$i."-".$j][] = array('积分排行榜',U('/admin/top/market'),1);
$menu_left[$i][$i."-".$j][] = array('积分记录',U('/admin/market/index'),1);


$j++;
$menu_left[$i]['low_title'][$i."-".$j] = array('积分兑换订单','#',1);
$menu_left[$i][$i."-".$j][] = array('未领取兑换订单',U('/admin/market/order','status=0'),1);
$menu_left[$i][$i."-".$j][] = array('正在发送中兑换订单',U('/admin/market/order','status=1'),1);
$menu_left[$i][$i."-".$j][] = array('已成功领取兑换订单',U('/admin/market/order','status=2'),1);
$menu_left[$i][$i."-".$j][] = array('领取失败兑换订单',U('/admin/market/order','status=3'),1);


$j++;
$menu_left[$i]['low_title'][$i."-".$j] = array('积分商城','#',1);
$menu_left[$i][$i."-".$j][] = array('可兑换商品',U('/admin/market/goods','status=1'),1);
$menu_left[$i][$i."-".$j][] = array('已下架商品',U('/admin/market/goods','status=0'),1);
$menu_left[$i][$i."-".$j][] = array('抽奖商品列表',U('/admin/market/lottery'),1);
$menu_left[$i][$i."-".$j][] = array('商品评论',U('/admin/market/comment'),1);

$i++;
$menu_left[$i]= array('充值提现','#',1);
$menu_left[$i]['low_title'][$i."-".$j] = array('充值管理','#',1);
$menu_left[$i][$i."-".$j][] = array('待审在线充值',U('/admin/paylog/online','status=0'),1);
$menu_left[$i][$i."-".$j][] = array('已审在线充值',U('/admin/paylog/online','status=1'),1);
$menu_left[$i][$i."-".$j][] = array('未通过在线充值',U('/admin/paylog/online','status=3'),1);
$menu_left[$i][$i."-".$j][] = array('待审线下充值',U('/admin/paylog/offline','status=0'),1);
$menu_left[$i][$i."-".$j][] = array('已审线下充值',U('/admin/paylog/offline','status=1'),1);
$menu_left[$i][$i."-".$j][] = array('未通过线下充值',U('/admin/paylog/offline','status=3'),1);
$menu_left[$i][$i."-".$j][] = array('所有充值记录',U('/admin/paylog/index'),1);


$j++;
$menu_left[$i]['low_title'][$i."-".$j] = array('提现管理','#',1);
$menu_left[$i][$i."-".$j][] = array('待审核提现',U('/admin/withdraw/check'),1);
$menu_left[$i][$i."-".$j][] = array('审核未通过提现',U('/admin/withdraw/refuse'),1);
$menu_left[$i][$i."-".$j][] = array('正在处理中提现',U('/admin/withdraw/waiting'),1);
$menu_left[$i][$i."-".$j][] = array('已成功提现 ',U('/admin/withdraw/finish'),1);
$menu_left[$i][$i."-".$j][] = array('提现总列表',U('/admin/withdraw/index'),1);

$i++;
$menu_left[$i]= array('报表统计','#',1);
$menu_left[$i]['low_title'][$i."-".$j] = array('报表分析','#',1);
$menu_left[$i][$i."-".$j][] = array('配资分析',U('/admin/analyse/trade'),1);
$menu_left[$i][$i."-".$j][] = array('投资者收益分析',U('/admin/analyse/profit'),1);
$menu_left[$i][$i."-".$j][] = array('资金进出分析',U('/admin/analyse/paylog'),1);
$menu_left[$i][$i."-".$j][] = array('网站收益分析',U('/admin/analyse/income'),1);
$menu_left[$i][$i."-".$j][] = array('会员分析',U('/admin/analyse/member'),1);

$j++;
$menu_left[$i]['low_title'][$i."-".$j] = array('会员帐户','#',1);
$menu_left[$i][$i."-".$j][] = array('会员帐户',U('/admin/capital/account'),1);
$menu_left[$i][$i."-".$j][] = array('资金变动记录',U('/admin/capital/detail'),1);
$menu_left[$i][$i."-".$j][] = array('充值总记录',U('/admin/capital/charge'),1);
$menu_left[$i][$i."-".$j][] = array('提现总记录',U('/admin/capital/withdraw'),1);

$j++;
$menu_left[$i]['low_title'][$i."-".$j] = array('排行榜','#',1);
$menu_left[$i][$i."-".$j][] = array('会员借款排行',U('/admin/top/borrow'),1);
$menu_left[$i][$i."-".$j][] = array('会员投标排行',U('/admin/top/tender'),1);
$menu_left[$i][$i."-".$j][] = array('会员收益排行',U('/admin/top/invest'),1);
$menu_left[$i][$i."-".$j][] = array('会员充值排行',U('/admin/top/recharge'),1);
$menu_left[$i][$i."-".$j][] = array('会员提现排行',U('/admin/top/withdraw'),1);
$menu_left[$i][$i."-".$j][] = array('会员登录次数排行',U('/admin/top/login'),1);
$menu_left[$i][$i."-".$j][] = array('会员推荐人排行',U('/admin/top/invite'),1);
$menu_left[$i][$i."-".$j][] = array('会员积分排行',U('/admin/top/market'),1);


$i++;
$menu_left[$i]= array('系统','#',1);
$menu_left[$i]['low_title'][$i."-".$j] = array('业务参数','#',1);

$menu_left[$i][$i."-".$j][] = array('业务相关选项',U('/admin/config/index'),1);
$menu_left[$i][$i."-".$j][] = array('线下充值银行管理',U('/admin/payment/offline'),1);
$menu_left[$i][$i."-".$j][] = array('线上支付接口管理',U('/admin/payment/online'),1);
$menu_left[$i][$i."-".$j][] = array('第三方会员登陆接口',U('/admin/login/'),1);
$menu_left[$i][$i."-".$j][] = array('会员级别及分类',U('/admin/leve/index'),1);

$j++;
$menu_left[$i]['low_title'][$i."-".$j] = array('网站内容管理','#',1);
$menu_left[$i][$i."-".$j][] = array('网站参数',U('/admin/global/websetting'),1);
$menu_left[$i][$i."-".$j][] = array('导航及菜单',U('/admin/navigation/index'),1);
$menu_left[$i][$i."-".$j][] = array('文章资讯列表',U('/admin/article/'),1);
$menu_left[$i][$i."-".$j][] = array('文章分类设置',U('/admin/category/'),1);
$menu_left[$i][$i."-".$j][] = array('会员评论',U('/admin/comment/index'),1);
$menu_left[$i][$i."-".$j][] = array('站内广告',U('/admin/ad/'),1);
$menu_left[$i][$i."-".$j][] = array('相关链接',U('/admin/global/friend'),1);
$menu_left[$i][$i."-".$j][] = array('缓存管理',U('/admin/global/cleanall'),1);


$j++;
$menu_left[$i]['low_title'][$i."-".$j] = array('在线客服管理','#',1);
$menu_left[$i][$i."-".$j][] = array('在线QQ客服',U('/admin/qq/index'),1);
$menu_left[$i][$i."-".$j][] = array('客服电话',U('/admin/qq/tel/'),1);

$j++;
$menu_left[$i]['low_title'][$i."-".$j] = array('在线消息与通知','#',1);
$menu_left[$i][$i."-".$j][] = array('通知信息接口',U('/admin/message/'),1);
$menu_left[$i][$i."-".$j][] = array('通知信息模板',U('/admin/message/templet/'),1);
$menu_left[$i][$i."-".$j][] = array('手机客户端云推送',U('/admin/baidupush/'),0);

$j++;
$menu_left[$i]['low_title'][$i."-".$j] = array('系统用户权限',"#",1);
$menu_left[$i][$i."-".$j][] = array('管理员用户',U('/admin/users/'),1);
$menu_left[$i][$i."-".$j][] = array('用户组权限管理',U('/admin/acl/'),1);

//$menu_left[$i][$i."-".$j][] = array("自动执行参数",U("/admin/auto/"),1);


$j++;
$menu_left[$i]['low_title'][$i."-".$j] = array('系统安全','#',1);
$menu_left[$i][$i."-".$j][] = array("操作日志",U("/admin/logs/"),1);
$menu_left[$i][$i."-".$j][] = array('文件管理',U('/admin/files/'),1);
$menu_left[$i][$i."-".$j][] = array('木马查杀',U('/admin/safety/'),1);
$menu_left[$i][$i."-".$j][] = array('数据库信息',U('/admin/db/'),1);
$menu_left[$i][$i."-".$j][] = array('备份管理',U('/admin/db/browse'),1);

?>