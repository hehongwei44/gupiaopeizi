<?php
/**
* 备份数据库，根据选择的表选择
* @author abc@qq.com 
* @date 2013-10-10
*/
    class DBReback
    {
        private $bkpath;
        private $config;
        private $content;
        private $conArr;
        private $fileSize;
        private $fn;
        const DIR_SEP = DIRECTORY_SEPARATOR;
        public function __construct($config)
        {   
            $this->config = $config;
            $this->fn = 0;  
            header ( "Content-type: text/html;charset=utf-8" );
            $this->connect();
        }
        /**
        * 链接数据库
        * 
        */
        private function connect()
        {
            if(mysql_connect($this->config['host']. ':' . $this->config['port'], $this->config['userName'], $this->config['userPassword'])){
                mysql_query("set NAMES '{$this->config['charset']}'");
                mysql_query("set interactive_timeout=24*3600");
                mysql_select_db($this->config['dbname']);
            }
            else
            {
                $this->throwException("无法连接到数据库");
            }
        }
        
        public function setFileSize($size)
        {
            $this->fileSize = $size*1024;
        }
        /**
        * 备份数据库
        * @param $tables array() // 要备份的数据表，数组的形式
        * @return boolean  //成功返回 true  失败返回 false
        */
        public function backup($tables=array())
        {   
            if(!count($tables)){
                $this->throwException("没有要备份的数据表");
            }
            $this->content = '/* This file is created by DBReback '.time("Y-m-d H:i:s").' */';
            $this->fname = $this->config['dbname'].'_'.date('YmdHis') . '_v';
            foreach($tables as $table){
                $table = $this->backquote($table); 
                $tableRs = mysql_query("SHOW CREATE TABLE {$table}");   
                if($tableRow = mysql_fetch_row($tableRs)){
                    $this->content .= " \r\n /* 数据表结构 {$table}*/";
                    $this->content .= " \r\n DROP TABLE IF EXISTS {$table};/* DBReback Separation */ \r\n {$tableRow[1]};/* DBReback Separation */";
                    $tableDateRs = mysql_query("SELECT * FROM {$table}");
                    $valuesArr = array();
                    $values = '';
                    while ($tableDateRow = mysql_fetch_row($tableDateRs)) {
                        foreach ($tableDateRow as &$v) {
                            $v = "'" . addslashes($v) . "'"; 
                        }
                        $value = '(' . implode(',', $tableDateRow) . ')';
                        $this->chunkArrayByByte($value, $table);
                    }
                } 
            }
            if(!empty($this->content)){
                $this->writeFile(); 
                $this->content='';
            }
            return true;
        }
        
        private function chunkArrayByByte($value, $table) {
            
            //$values = implode(',', $v) . ';/* DBReback Separation */';
            
            $this->content .= "\r\n /* 插入数据 {$table} */";
            $this->content .= "\r\n INSERT INTO {$table} VALUES {$value};/* DBReback Separation */";
            if(strlen($this->content) >= $this->fileSize){
                $this->writeFile();
                $this->content = null;
            }
        }
        
        private function writeFile()
        {
            $recognize = '';
            if(!empty($this->content)){
                $fileName = $this->trimPath($this->config['path'] . self::DIR_SEP . $this->fname.$this->fn.'.sql');
                $path = $this->setPath(dirname($fileName));
                if ($path !== true) {
                    $this->throwException("无法创建备份目录目录 '$path'");
                }
                if ($this->config['isCompress'] == 0) {
                    if (!file_put_contents($fileName, $this->content, LOCK_EX)) {
                        $this->throwException('写入文件失败,请检查磁盘空间或者权限!');
                    }
                } else {
                    if (function_exists('gzwrite')) {
                        $fileName .= '.gz';
                        if ($gz = gzopen($fileName, 'wb')) {
                            gzwrite($gz, $this->content);
                            gzclose($gz);
                        } else {
                            $this->throwException('写入文件失败,请检查磁盘空间或者权限!');
                        }
                    } else {
                        $this->throwException('没有开启gzip扩展!');
                    }
                }
                $this->fn++;    
            }
        }
        
        /**
        * 还原数据备份到数据库
        * @param filename string //还原的文件名
        * @return boolean //还原成功返回ture  返回失败返回 false
        */
        public function recover($fileName)
        {  
            set_time_limit(0);
            $this->getFile($fileName);  
            if (!empty($this->content)) {
                $content = explode(';/* DBReback Separation */', $this->content);
                //print_R($content);exit;
                foreach ($content as $i => $sql) {
                    $sql = trim($sql);
                    if (!empty($sql)) {
                        $dbName = $this->config['dbname'];   
                        if(!mysql_select_db($dbName)) $this->throwException('不存在的数据库!' . mysql_error());
                        $rs = mysql_query($sql);
                        if ($rs) {
                            if (strstr($sql, 'CREATE DATABASE')) {
                                $dbNameArr = sscanf($sql, 'CREATE DATABASE %s');
                                $dbName = trim($dbNameArr[0], '`');
                                mysql_select_db($dbName);
                            }
                        } else {
                            $this->throwException('备份文件被损坏!' . mysql_error());
                        }
                    }
                }
            } else {
                $this->throwException('无法读取备份文件!');
            }
            return true;    
        }
        

        
        private function setPath($fileName){ 
            if (!file_exists($fileName)){ 
                $this->setPath(dirname($fileName)); 
                mkdir($fileName, 0777); 
            } 
            return true;
        }
        
        private function getFile($fileName) {
            $this->content = '';
            $fileName = $this->trimPath($this->config['path'] . self::DIR_SEP .$fileName); 
            if (is_file($fileName)) {
                $ext = strrchr($fileName, '.');
                if ($ext == '.sql') {
                    $this->content = file_get_contents($fileName);
                } elseif ($ext == '.gz') {
                    $this->content = implode('', gzfile($fileName));
                } else {
                    $this->throwException('无法识别的文件格式!');
                }
            } else {
                $this->throwException('文件不存在!');
            }
        }
    
        private function trimPath($path) {
            return str_replace(array('/', '\\', '//', '\\\\'), self::DIR_SEP, $path);
        }
        
        
        
        public function tableColumns($table)
        {
            if(!strstr($table, '`')){
                $table = $this->backquote($table);
            }  
            mysql_query("SET SQL_QUOTE_SHOW_CREATE=1;");
            $result = mysql_query("SHOW columns from {$table}"); 
            while($row = mysql_fetch_array($result)){
                $columns[] = $row;
            }
            return $columns;
        }
        
        private function backquote($str)
        {
            return '`'.$str.'`';
        }
        private function throwException($error) {
            throw new Exception($error);
        }
    }
?>
