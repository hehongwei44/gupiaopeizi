<?php
$loginconfig = FS("data/conf/login");
$uc_mcfg  = $loginconfig['uc'];

define('UC_CONNECT', 'mysql');				// 连接 UCenter 的方式: mysql/NULL, 默认为空时为 fscoketopen()
							// mysql 是直接连接的数据库, 为了效率, 建议采用 mysql

//数据库相关 (mysql 连接时, 并且没有设置 UC_DBLINK 时, 需要配置以下变量)
define('UC_DBHOST', $uc_mcfg['UC_DBHOST']);			// UCenter 数据库主机
define('UC_DBUSER', $uc_mcfg['UC_DBUSER']);				// UCenter 数据库用户名
define('UC_DBPW', $uc_mcfg['UC_DBPW']);					// UCenter 数据库密码
define('UC_DBNAME', $uc_mcfg['UC_DBNAME']);				// UCenter 数据库名称
define('UC_DBCHARSET', $uc_mcfg['UC_DBCHARSET']);				// UCenter 数据库字符集
define('UC_DBTABLEPRE', $uc_mcfg['UC_DBTABLEPRE']);			// UCenter 数据库表前缀

//通信相关
define('UC_KEY', $uc_mcfg['UC_KEY']);				// 与 UCenter 的通信密钥, 要与 UCenter 保持一致
define('UC_API', $uc_mcfg['UC_API']);	// UCenter 的 URL 地址, 在调用头像时依赖此常量
define('UC_CHARSET', $uc_mcfg['UC_CHARSET']);				// UCenter 的字符集
define('UC_IP', $uc_mcfg['UC_IP']);					// UCenter 的 IP, 当 UC_CONNECT 为非 mysql 方式时, 并且当前应用服务器解析域名有问题时, 请设置此值
define('UC_APPID', $uc_mcfg['UC_APPID']);					// 当前应用的 ID

//同步登录 Cookie 设置
$cookiedomain = ''; 			// cookie 作用域
$cookiepath = '/';			// cookie 作用路径