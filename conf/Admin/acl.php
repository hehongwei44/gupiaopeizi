<?php
/*array(菜单名，菜单url参数，是否显示)*/
//error_reporting(E_ALL);
/*
$acl_inc[$i]['low_leve']['global']  global是model
每个action前必须添加eqaction_前缀'eqaction_websetting'  => 'at1','at1'表示唯一标志,可独自命名,eqaction_后面跟的action必须统一小写


*/
$acl_inc =  array();
$i=0;
$acl_inc[$i]['low_title'][] = '配资管理';
$acl_inc[$i]['low_leve']['trade']= array( "按月配资" =>array(
												 "配资列表" 		=> 'index',
												 "配资审核" 		=> 'audit',
											 ),
											"按天配资" =>array(
												 "配资列表" 		=> 'index',
												 "配资审核" 		=> 'audit',
											 ),
											"待扣利息/费用" =>array(
												"按天待扣费用" 	=> 'mk3',
												 "按月待扣利息" 		=> 'mk4',
											),
										   "data" => array(
										   		
										   		'eqaction_index'  => 'index',
										   		'eqaction_audit'  => 'audit',		
											)
							);
$acl_inc[$i]['low_leve']['event']= array( "操盘达人" =>array(
												 "添加达人" 		=> 'add',
												 "操盘达人" 		=> 'rank',
												),
									   "data" => array(
												'eqaction_add'  => 'add',
												'eqaction_rank'  => 'rank',
											));
$i++;
$acl_inc[$i]['low_title'][] = '风控管理';
$acl_inc[$i]['low_leve']['risk']= array( "风控方案" =>array(
												"方案列表" 		=> 'index',
												"完结方案" 		=> 'finish',
												"持仓查询" 		=> 'stock',
											 ),
											"终结配资申请" =>array(
												"用户终结配资审核" 		=> 'stop',
											 ),
											"追加保证金申请" =>array(
												"追加保证金审核" 		=> 'deposit',
											 ),
											"提取利润申请" =>array(
												"提取配资利润审核" 		=> 'profit',
											 ),
											"配资续约申请" =>array(
												"配资续约审核" 		=> 'renew',
											 ),
										   "data" => array(
										   		'eqaction_index'  => 'index',
										   		'eqaction_finish'  => 'finish',
												'eqaction_finish'  => 'stock',
										   		'eqaction_stop'  => 'stop',
										   		'eqaction_deposit'  => 'deposit',
										   		'eqaction_profit'  => 'profit',
										   		'eqaction_renew'  => 'renew',
											)
							);
$acl_inc[$i]['low_leve']['homs']= array( "恒生账户" =>array(
												 "账户列表" 		=> 'index',
												 "添加新账户" 		=> 'add',
												 "编辑账户" 		=> 'edit',
												 "删除账户" 		=> 'edit',
												),
									   "data" => array(
												'eqaction_index'  => 'index',
												'eqaction_add'  => 'add',
												'eqaction_edit'  => 'edit',
												'eqaction_remove'  => 'remove',
											)
							);

$i++;
$acl_inc[$i]['low_title'][] = '会员管理';
$acl_inc[$i]['low_leve']['member']= array( "会员列表" =>array(
												 "列表" 		=> 'me1',
												 "调整余额" 	=> 'mx2',
												 "调整授信" 	=> 'mx3',
												 "删除会员" 	=> 'mxw',
												 "修改客户类型" 	=> 'xmxw',
												),
										   "会员资料" =>array(
												 "列表" 		=> 'me3',
												 "查看" 		=> 'me4',
											),
										   "额度申请待审核" =>array(
												 "列表" 		=> 'me7',
												 "审核" 		=> 'me6',
											),

									   "data" => array(
										   		
												'eqaction_index'  => 'me1',
												'eqaction_info' =>'me3',	
												'eqaction_viewinfom'  => 'me4',
												'eqaction_infowait'  => 'me7',
												'eqaction_viewinfo'  => 'me6',
												'eqaction_doeditcredit'  => 'me6',
												'eqaction_domoneyedit'  => 'mx2',
												'eqaction_moneyedit'  => 'mx2',
												'eqaction_creditedit'  => 'mx3',
												'eqaction_dodel'    => 'mxw',
												'eqaction_add'    => 'xmxw',
												'eqaction_edit'    => 'xmxw',
												'eqaction_doedit'    => 'xmxw',
												'eqaction_docreditedit'  => 'mx3',
												'eqaction_idcardedit'    => 'xmxw',
												'eqaction_doidcardedit'    => 'xmxw',
											)
							);
$acl_inc[$i]['low_leve']['common']= array( "会员详细资料" =>array(
												 "查询" 		=> 'mex5',
												 "账户通讯" 		=> 'sms1',
												 "具体通讯" 		=> 'sms2',
												 "节日通讯" 		=> 'sms3',
												 "通讯记录" 		=> 'sms4',
												),
									   "data" => array(
												'eqaction_member'  => 'mex5',
												'eqaction_sms'  => 'sms1',
												'eqaction_sendsms'  => 'sms2',
												'eqaction_sendgala'  => 'sms3',
												'eqaction_smslog'  => 'sms4',
											)
							);
$acl_inc[$i]['low_leve']['remark']= array( "备注信息" =>array(
												 "列表" 		=> 'rm1',
												 "增加" 		=> 'rm2',
												 "修改" 		=> 'rm3',
												),
									   "data" => array(
												'eqaction_index'  => 'rm1',
												'eqaction_add'    => 'rm2',
												'eqaction_doadd'    => 'rm2',
												'eqaction_edit'    => 'rm3',
												'eqaction_doedit'    => 'rm3',
											)
							);
$acl_inc[$i]['low_leve']['comment']= array( "评论管理" =>array(
												 "列表" 		=> 'mkcom1',
												 "查看" 		=> 'mkcom2',
												 "删除" 		=> 'mkcom3',
											),
									   "data" => array(
												'eqaction_index'  => 'mkcom1',
											)
							);
$acl_inc[$i]['low_leve']['invite']= array("推荐人管理" =>array(
												 "列表" 		=> 'referee_1',
												 "导出" 		=> 'referee_2',
												),
											   "data" => array(
													
													'eqaction_index'  => 'referee_1',
													'eqaction_export'  => 'referee_2',
												)
							);
$acl_inc[$i]['low_leve']['jubao']= array( "举报信息" =>array(
												 "列表" 		=> 'me5',
												),
									   "data" => array(
										   		
												'eqaction_index'  => 'me5',
											)
							);
$acl_inc[$i]['low_leve']['agent']= array( "加盟管理" =>array(
												 "列表" 		=> 'agent1',
												 "查看" 		=> 'agent2',
												 "删除" 		=> 'agent3',
											 ),
										   "data" => array(
										   		
												'eqaction_index'  => 'agent1',
												'eqaction_edit'  => 'agent2',
												'eqaction_dodel'  => 'agent3',
											)
							);

$i++;
$acl_inc[$i]['low_title'][] = '认证及申请管理';
$acl_inc[$i]['low_leve']['verifyvip']= array( "VIP申请列表" =>array(
												 "列表" 		=> 'vip1',
												 "审核" 		=> 'vip2',
												),
										   "data" => array(
													
													'eqaction_index'  => 'vip1',
													'eqaction_edit' =>'vip2',	
													'eqaction_doedit'  => 'vip2',
												)
							);
$acl_inc[$i]['low_leve']['verifyid']= array( "会员实名认证管理" =>array(
												 "列表" 		=> 'me10',
												 "审核" 		=> 'me9',
												  "导出" 		=> 'me8',
												),
									   "data" => array(
										   		
												'eqaction_index'  => 'me10',
												'eqaction_edit'  => 'me9',
												'eqaction_doedit'  => 'me9',
												'eqaction_export'  => 'me8',
											)
							);
$acl_inc[$i]['low_leve']['verifyinfo']= array( "会员上传资料管理" =>array(
												 "列表" 		=> 'dat1',
												 "审核" 		=> 'dat3',
												 "上传资料" 	=> 'dat4',
												 "上传展示资料" => 'dat5',
												),
									   "data" => array(
										   		
												'eqaction_index'  => 'dat1',
												'eqaction_swfupload'  => 'dat1',
												'eqaction_edit'   => 'dat3',
												'eqaction_doedit'  => 'dat3',
												
												'eqaction_upload'  => 'dat4',
												'eqaction_doupload'  => 'dat4',
												'eqaction_uploadshow'  => 'dat5',
												'eqaction_douploadshow'  => 'dat5',
											)
							);
$acl_inc[$i]['low_leve']['verifyphone']= array( "手机认证会员" =>array(
												 "列表" 		=> 'vphone1',
												 "导出" 		=> 'vphone2',
												 "审核" 		=> 'vphone3',
												),
									   "data" => array(
										   		
												'eqaction_index'   => 'vphone1',
												'eqaction_export'  => 'vphone2',
												'eqaction_edit'    => 'vphone3',	
												'eqaction_doedit'  => 'vphone3',
											)
							);
$i++;
$acl_inc[$i]['low_title'][] = '积分管理';
$acl_inc[$i]['low_leve']['market']= array( "投资积分管理" =>array(
												"积分排行" 		=> 'top',
												 "投资积分操作记录" => 'mk0',
												 "获取列表" 		=> 'mk1',
												 "获取操作" 		=> 'mk2',
												
												),
											"积分商城" =>array(
												"商城商品列表" 	=> 'mk3',
												 "商品操作" 		=> 'mk4',
												 "上传商品图片" 	=> 'mk5',
											),
											"抽奖管理" =>array(
												 "列表" 		=> 'mk6',
												 "编辑" 		=> 'mk7',
												 "删除" 		=> 'mk8',
											),

										   "data" => array(
													
													'eqaction_top'  => 'top',
													'eqaction_index'  => 'mk0',
													'eqaction_order'  => 'mk1',
													'eqaction_order_edit'  => 'mk2',
													'eqaction_doorder'  => 'mk2',
													'eqaction_goods'  => 'mk3',
													'eqaction_good_edit'  => 'mk4',
													'eqaction_dogoodedit'  => 'mk4',
													'eqaction_good_del'  => 'mk4',
													'eqaction_lottery'  => 'mk6',
													'eqaction_lottery_edit'  => 'mk7',
													'eqaction_dolotteryedit'  => 'mk7',
													'eqaction_lottery_del'  => 'mk8',
													'eqaction_upload_shop_pic'  => 'mk5',													
													'eqaction_dodel'  => 'mkcom3',
													'eqaction_edit'  => 'mkcom2',
													'eqaction_doedit'  => 'mkcom2',
												)
							);

$i++;
$acl_inc[$i]['low_title'][] = '充值提现';
$acl_inc[$i]['low_leve']['paylog']= array( "充值记录" =>array(
												 "列表" 		=> 'cz',
												 "充值处理" 		=> 'czgl',
												),
										   "data" => array(
													
													'eqaction_index'  => 'cz',
													'eqaction_online'  => 'cz',
													'eqaction_offline'  => 'cz',
													'eqaction_edit'  => 'czgl',
													'eqaction_doedit'  => 'czgl'
													   
												)
							);
$acl_inc[$i]['low_leve']['withdraw']= array("提现管理" =>array(
												 "列表" 		=> 'cg2',
												 "审核" 		=> 'cg3',
											),"待提现列表" =>array(
												 "列表" 		=> 'cg4',
												 "审核" 		=> 'cg5',
											),"提现处理中列表" =>array(
												 "列表" 		=> 'cg6',
												 "审核" 		=> 'cg7',
											),
										   "data" => array(
													
													'eqaction_index'  => 'cg2',
													'eqaction_edit' =>'cg3',	
													'eqaction_doedit'  => 'cg3',
													'eqaction_check'  => 'cg4',//待提现      新增加2012-12-02 fanyelei
													'eqaction_waiting'  => 'cg6',//提现处理中	新增加2012-12-02 fanyelei
													'eqaction_finish'  => 'cg2',//提现成功		新增加2012-12-02 fanyelei
													'eqaction_refuse'  => 'cg2',//提现失败		新增加2012-12-02 fanyelei
													'eqaction_audit' =>'cg5',	
													'eqaction_doaudit'  => 'cg5',
													'eqaction_process' =>'cg7',	
													'eqaction_doprocess'  => 'cg7',
													
												)
							);

$i++;
$acl_inc[$i]['low_title'][] = '网站内容管理';
$acl_inc[$i]['low_leve']['navigation']= array("导航菜单" =>array(
												 "列表"      => 'nav1',
												 "添加" 		=> 'nav2',
												 "批量添加" 	=> 'nav5',
												 "删除" 		=> 'nav3',
												 "修改" 		=> 'nav4',
											),
										   "data" => array(
													
													'eqaction_index'  => 'nav1',
													'eqaction_listtype'  => 'nav1',
													'eqaction_add'  => 'nav2',
													'eqaction_doadd'  => 'nav2',
													'eqaction_dodel'  => 'nav3',
													'eqaction_edit'  => 'nav4',
													'eqaction_doedit'  => 'nav4',
													'eqaction_addmultiple'  => 'nav5',
													'eqaction_doaddmul'  => 'nav5',
												)
							);
$acl_inc[$i]['low_leve']['global']= array( "网站设置" =>array(
												 "列表" 		=> 'at1',
												 "增加" 		=> 'at2',
												 "删除" 		=> 'at3',
												 "修改" 		=> 'at4',
												),
											"友情链接" =>array(
												 "列表" 		=> 'at5',
												 "增加" 		=> 'at6',
												 "删除" 		=> 'at7',
												 "修改" 		=> 'at8',
												 "搜索" 		=> 'att8',
											),
											"所有缓存" =>array(
												 "清除" 		=> 'at22',
											),

										   "data" => array(
										   		
												'eqaction_websetting'  => 'at1',
												'eqaction_doadd'    => 'at2',
												'eqaction_dodelweb'    => 'at3',
												'eqaction_doedit'   => 'at4',
												'eqaction_friend'  	   => 'at5',
												'eqaction_dodeletefriend'    => 'at7',
												'eqaction_searchfriend'    => 'att8',
												'eqaction_addfriend'   => array(
																'at6'=>array(
																	'POST'=>array(
																		"fid"=>'G_NOTSET',
																	),
																 ),	
																'at8'=>array(
																	'POST'=>array(
																		"fid"=>'G_ISSET',
																	),
																),
													),
										   		//清除缓存
												'eqaction_cleanall'  => 'at22',

											)
							);
$acl_inc[$i]['low_leve']['ad']= array( "广告管理" =>array(
												 "列表" 		=> 'ad1',
												 "增加" 		=> 'ad2',
												 "删除" 		=> 'ad4',
												 "修改" 		=> 'ad3',
												),
										   "data" => array(
										   		
												'eqaction_index'  => 'ad1',
												'eqaction_add'    => 'ad2',
												'eqaction_doadd'    => 'ad2',
												'eqaction_edit'    => 'ad3',
												'eqaction_doedit'    => 'ad3',
												'eqaction_swfupload'    => 'ad3',
												'eqaction_dodel'    => 'ad4',
											)
							);

$acl_inc[$i]['low_title'][] = '文章管理';
$acl_inc[$i]['low_leve']['article']= array( "文章管理" =>array(
												 "列表" 		=> 'at1',
												 "添加" 		=> 'at2',
												 "删除" 		=> 'at3',
												 "修改" 		=> 'at4',
												),
										   "data" => array(
													
													'eqaction_index'  => 'at1',
													'eqaction_add'  => 'at2',
													'eqaction_doadd'  => 'at2',
													'eqaction_dodel'  => 'at3',
													'eqaction_edit'  => 'at4',
													'eqaction_doedit'  => 'at4',
												)
							);
$acl_inc[$i]['low_leve']['category']= array("文章分类" =>array(
												 "列表" 		=> 'act1',
												 "添加" 		=> 'act2',
												 "批量添加" 	=> 'act5',
												 "删除" 		=> 'act3',
												 "修改" 		=> 'act4',
											),
										   "data" => array(
													
													'eqaction_index'  => 'act1',
													'eqaction_listtype'  => 'act1',
													'eqaction_add'  => 'act2',
													'eqaction_doadd'  => 'act2',
													'eqaction_dodel'  => 'act3',
													'eqaction_edit'  => 'act4',
													'eqaction_doedit'  => 'act4',
													'eqaction_addmultiple'  => 'act5',
													'eqaction_doaddmul'  => 'act5',
												)
							);


$i++;
$acl_inc[$i]['low_title'][] = '系统';
$acl_inc[$i]['low_leve']['login']= array( "登陆接口管理" =>array(
												 "查看" 		=> 'dl1',
												 "修改" 		=> 'dl2',
												),
										   "data" => array(
										   		
												'eqaction_index'  => 'dl1',
												'eqaction_save'    => 'dl2',
											)
							);
/*
$acl_inc[$i]['low_leve']['auto'] = array("自动执行参数" => array( 
												"查看" => "atjb1", 
												"修改" => "atjb2", 
												"开启程序" => "atjb3", 
												"关闭程序" => "atjb4", 
												"开启服务" => "atjb5", 
												"卸载服务" => "atjb7", 
												"当前运行状态" => "atjb6",
												),
												"data" => array( 
												"eqaction_index" => "atjb1", 
												"eqaction_save" => "atjb2", 
												"eqaction_start" => "atjb3", 
												"eqaction_close" => "atjb4", 
												"eqaction_startserver" => "atjb5", 
												"eqaction_stopserver" => "atjb7", 
												"eqaction_showstatus" => "atjb6",
												)
							);
*/
$acl_inc[$i]['low_leve']['files']= array( "文件管理" =>array(
												 "文件管理" 		=> 'at82',
												 "空间检查"					=>'at83',
												),
										   	  "data" => array(
										   		//文件管理
												'eqaction_index'  => 'at82',
												'eqaction_checksize'  => 'at83',
											  )
							);
//权限管理
$acl_inc[$i]['low_title'][] = '权限管理';
$acl_inc[$i]['low_leve']['acl']= array( "权限管理" =>array(
												 "列表" 		=> 'at73',
												 "增加" 		=> 'at74',
												 "删除" 		=> 'at75',
												 "修改" 		=> 'at76',
												),
										   "data" => array(
										   		//权限管理
												'eqaction_index'  => 'at73',
												'eqaction_doadd'    => 'at74',
												'eqaction_add'    => 'at74',
												'eqaction_dodelete'    => 'at75',
												'eqaction_doedit'   => 'at76',
												'eqaction_edit'   	=> 'at76',
											)
							);
//管理员管理
$acl_inc[$i]['low_title'][] = '管理员管理';
$acl_inc[$i]['low_leve']['users']= array( "管理员管理" =>array(
												 "列表" 		=> 'at77',
												 "增加" 		=> 'at78',
												 "删除" 		=> 'at79',
												 "上传头像"	=> 'at99',
												 "修改" 		=> 'at80',
												),
											
										   	  "data" => array(
										   		//权限管理
												'eqaction_index'  => 'at77',
												'eqaction_dodelete'    => 'at79',
												'eqaction_header'    => 'at99',
												'eqaction_memberheaderuplad'    => 'at99',
												'eqaction_addadmin' =>array(
																'at78'=>array(//增加
																	'POST'=>array(
																		"uid"=>'G_NOTSET',
																	),
																 ),	
																'at80'=>array(//修改
																	'POST'=>array(
																		"uid"=>'G_ISSET',
																	),
																 ),	
												),

											)
							);


$acl_inc[$i]['low_title'][] = '数据库管理';
$acl_inc[$i]['low_leve']['db']= array( "数据库信息" =>array(
												 "查看" 		=> 'db1',
												 "备份" 		=> 'db2',
												 "查看表结构" => 'db3',
												),
									   "数据库备份管理" =>array(
											 "备份列表" 		=> 'db4',
											 "删除备份" 		=> 'db5',
											 "恢复备份" 		=> 'db6',
											 "打包下载" 		=> 'db7',
										),
									   "清空数据" =>array(
											 "清空数据" 		=> 'db8',
										),
										   "data" => array(
										   		//权限管理
												'eqaction_index'  => 'db1',
												'eqaction_set'  => 'db2',
												'eqaction_backup'  => 'db2',
												'eqaction_tables'  => 'db3',
												'eqaction_browse'  => 'db4',
												'eqaction_delbak'  => 'db5',
												'eqaction_restore'  => 'db6',
												'eqaction_dozip'  => 'db7',
												'eqaction_download'  => 'db7',
												'eqaction_truncate'  => 'db8',
											)
							);
$acl_inc[$i]['low_leve']['kissy']= array( "图片上传" =>array(
												 "图片上传" 		=> 'at81',
												),
										   	  "data" => array(
										   		//权限管理
												'eqaction_index'  => 'at81',
											  )
							);
$acl_inc[$i]['low_leve']['safety']= array( "系统安全" =>array(
                                                 "安全检测"         => 'scan1',
                                                ),
                                            "data" => array(
                                                'eqaction_index'  => 'scan1',
                                                'eqaction_scancom'=>'scan1',
                                                'eqaction_updateconfig'=>'scan1',
                                                'eqaction_filefilter'  => 'scan1',
                                                'eqaction_filefunc' =>'scan1',
                                                'eqaction_filecode' =>'scan1',
                                                'eqaction_report'=>'scan1',
                                                'eqaction_view'=>'scan1',
                                              )
                            );
$acl_inc[$i]['low_leve']['logs']= array( "操作日志" =>array(
                                                 "查看日志" 		=> 'at23',
												 "清空日志"			=>'at24',
												 "删除一月前日志"=>'at25',
                                                ),
                                            "data" => array(
                                                'eqaction_index'  => 'at23',
												'eqaction_clear'=>'at24',
												'eqaction_delete'=>'at25',//删除近期一个月内的后台操作日志
                                              )
                            );

$i++;
$acl_inc[$i]['low_title'][] = '报表统计';

							
$acl_inc[$i]['low_leve']['analyse']= array("统计分析" =>array(
												 "借款分析" 		=> 'borrow',
												 "股票配资分析" 		=> 'trade',
												 "资金进出分析" 		=> 'paylog',
												 "投资者收益分析" 		=> 'profit',
												 "网站收益分析" 		=> 'income',
												 "会员分析" 		=> 'member',
												),
											   "data" => array(
													'eqaction_borrow'  => 'borrow',
													'eqaction_trade'  => 'trade',
													'eqaction_profit'  => 'profit',
													'eqaction_paylog'  => 'paylog',
													'eqaction_withdrawlogwait'  => 'paylog',
													'eqaction_withdrawloging'  => 'paylog',
													'eqaction_withdrawlog'  => 'paylog',
													'eqaction_income'  => 'income',
													'eqaction_member'  => 'member',

													'eqaction_verifyvip'  => 'member',
													'eqaction_verifyphone'  => 'member',
													'eqaction_verifyid'  => 'member',
													'eqaction_verifyinfo'  => 'member',
												)
							);

$acl_inc[$i]['low_leve']['top']= array("排行榜" =>array(		
												 "积分排行" 		=> 'market',
												 "收益排行" 		=> 'invest',
												 "借款排行" 		=> 'borrow',
												 "投标排行" 		=> 'tender',
												 "充值排行" 		=> 'recharge',
												 "提现排行" 		=> 'withdraw',
												 "登录次数排行" 	=> 'login',
												 "推荐人排行" 		=> 'invite',
												),
											   "data" => array(													
													'eqaction_market'  => 'market',
													'eqaction_invest'  => 'invest',
													'eqaction_borrow'  => 'borrow',
													'eqaction_tender'  => 'tender',
													'eqaction_recharge'  => 'recharge',
													'eqaction_withdraw'  => 'withdraw',
													'eqaction_login'  => 'login',
													'eqaction_invite'  => 'invite',
												)
							);

$acl_inc[$i]['low_leve']['capital']= array( "会员帐户" =>array(
												 "列表" 		=> 'capital_1',
												 "导出" 		=> 'capital_2',
												),
											"资金变动记录" =>array(
												 "列表" 		=> 'capital_7',
												 "导出" 		=> 'capital_8',
												),
												"充值记录" =>array(
												 "列表" 		=> 'capital_3',
												 "导出" 		=> 'capital_4',
												),
												"提现记录" =>array(
													 "列表" 		=> 'capital_5',
													 "导出" 		=> 'capital_6',
												),
										   "data" => array(
										   			'eqaction_account'  => 'capital_1',
													'eqaction_accountexport'  => 'capital_2',
													'eqaction_detail'  => 'capital_7',
													'eqaction_detailexport'  => 'capital_8',
													'eqaction_charge'  => 'capital_3',
													'eqaction_chargeexport'  => 'capital_4',
													'eqaction_withdraw'  => 'capital_5',													
													'eqaction_withdrawexport'  => 'capital_6',
												)
							);



$i++;
$acl_inc[$i]['low_title'][] = '扩展功能管理';
$acl_inc[$i]['low_leve']['config']= array( "业务参数管理" =>array(
												 "查看" 		=> 'fb1',
												 "修改" 		=> 'fb2',
												),
										   "data" => array(
										   		
												'eqaction_index'  => 'fb1',
												'eqaction_save'    => 'fb2',
											)
							);
$acl_inc[$i]['low_leve']['leve']= array( "信用级别管理" =>array(
												 "查看" 		=> 'jb1',
												 "修改" 		=> 'jb2',
												),
										 "投资级别管理" =>array(
												 "查看" 		=> 'jb3',
												 "修改" 		=> 'jb4',
												),
										   "data" => array(
										   		
												'eqaction_index'  => 'jb1',
												'eqaction_save'    => 'jb2',
												'eqaction_invest'    => 'jb3',
												'eqaction_investsave'  => 'jb4',
											)
							);
$acl_inc[$i]['low_leve']['age']= array( "会员年龄别称" =>array(
												 "查看" 		=> 'bc1',
												 "修改" 		=> 'bc2',
												),
										   "data" => array(
										   		
												'eqaction_index'  => 'bc1',
												'eqaction_save'    => 'bc2',
											)
							);

$acl_inc[$i]['low_title'][] = '在线客服管理';
$acl_inc[$i]['low_leve']['qq']= array("QQ客服管理" =>array(
												 "列表" 		=> 'qq5',
												 "增加" 		=> 'qq6',
												 "删除" 		=> 'qq7',
												 
												),
									  "QQ群管理" =>array(
												 "列表" 		=> 'qun5',
												 "增加" 		=> 'qun6',
												 "删除" 		=> 'qun7',
												 
												),
									  "客服电话管理" =>array(
												 "列表" 		=> 'tel5',
												 "增加" 		=> 'tel6',
												 "删除" 		=> 'tel7',
												 
												),
									   "data" => array(
										   		
												'eqaction_index'   => 'qq5',
												'eqaction_addqq'   => 'qq6',
												'eqaction_dodeleteqq'    => 'qq7',
												'eqaction_qun'   => 'qun5',
												'eqaction_addqun'   => 'qun6',
												'eqaction_dodeletequn'    => 'qun7',
												'eqaction_tel'   => 'tel5',
												'eqaction_addtel'   => 'tel6',
												'eqaction_dodeletetel'    => 'tel7',
											
											)
							);

//$acl_inc[$i]['low_title'][] = '在线通知管理';
$acl_inc[$i]['low_leve']['payment']= array( 
											"线上支付接口管理" =>array(
												 "查看" 		=> 'jk1',
												 "修改" 		=> 'jk2',
												),
											"线下充值银行管理" =>array(
												 "查看" 		=> 'offline1',
												 "修改" 		=> 'offline2',
												),
										   "data" => array(
												'eqaction_online'  => 'jk1',
												'eqaction_save'    => 'jk2',
												'eqaction_offline'  => 'offline1',
												'eqaction_saveconfig' => 'offline2', 
											)
							);
$acl_inc[$i]['low_leve']['message']= array( "通知信息接口管理" =>array(
												 "查看" 		=> 'jk3',
												 "修改" 		=> 'jk4',
												),
											 "通知信息模板管理" =>array(
												 "查看" 		=> 'jk5',
												 "修改" 		=> 'jk6',
											),
									   "data" => array(
										   		
												'eqaction_index'  => 'jk3',
												'eqaction_save'    => 'jk4',
												'eqaction_templet'  => 'jk5',
												'eqaction_templetsave'    => 'jk6',
											)
							);
							

			

?>