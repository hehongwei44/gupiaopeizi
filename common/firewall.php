<?php
/////////////////////////////////////////////////防cc攻击  fan/////////////////////////////////////////////////
/***/
//一旦判断出是代理服务器访问网站会输出“Proxies Forbidden”，
//如果开启了高级匿名代理服务器则会显示“Forbidden:High Anonymous Proxy Connection”
/***/
//Ban Proxy for all soft.
$ipinfo = new IpInfo();
$ipinfo->banProxy(true);
//false 时,屏蔽超级匿名的代理
class IpInfo
{

var $clientIp;
var $proxy;
var $proxyIp;
function IpInfo()
{
$this->getIp();
$this->checkProxy();
}
function banProxy($banAll = true)
{
if (!$this->proxy)
{
return;
}
if ($banAll == true)
{
die("Forbidden:Proxy Connection");
}
else
{
if ($this->clientIp == $this->proxyIp)
{
die("Forbidden:High Anonymous Proxy Connection");
}
}
}
function checkProxy()
{
if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])
|| isset($_SERVER['HTTP_VIA'])
|| isset($_SERVER['HTTP_PROXY_CONNECTION'])
|| isset($_SERVER['HTTP_USER_AGENT_VIA'])
|| isset($_SERVER['HTTP_CACHE_CONTROL'])
|| isset($_SERVER['HTTP_CACHE_INFO']))
{
$this->proxy = true;
$this->proxyIp = preg_replace("/^([{0-9}\.]+).*/", "[url=file://\\1]\\1[/url]", $_SERVER['REMOTE_ADDR']);
return $this->proxy;
}
}
function getIp()
{
if (isset($_SERVER['HTTP_CLIENT_IP']) && $_SERVER['HTTP_CLIENT_IP'])
{
$ip = $_SERVER['HTTP_CLIENT_IP'];
}
elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'])
{
$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
}
else
{
$ip = $_SERVER['REMOTE_ADDR'];
}
$this->clientIp = preg_replace("/^([{0-9}\.]+).*/", "[url=file://\\1]\\1[/url]", $ip);
return $this->clientIp;
}
}
?>
