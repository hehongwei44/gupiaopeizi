<?php
$info.="这是定时执行的demo，test1"."\r\n".date("Y-m-d H:i:s",time());
file_put_contents("D:/a.txt", $info);
?>