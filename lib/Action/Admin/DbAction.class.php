<?php
class DbAction extends AdminAction{
	var $b_filesize = "1024";//默认每个备份大小以k为单位
	var $waitbaktime = "0";//默认每组备份还原等待时间以秒为单位

    public function truncate(){
		if($_POST){
			$ar = file(C('DB_BAKPATH')."/truncate.txt");
			foreach($ar as $v){
				if(!empty($v)) M()->query($v);
			}
			$this->success("成功清空数据表");
		}else{
			$this->display();	
		}
    }
    public function index(){
		$all_table_info = M()->query("SHOW TABLE STATUS");
		$this->assign("tablelist",$all_table_info);//数据库内所有表信息
        $this->display();
    }
	//删除备份
	public function delbak(){
		$db_dir = C("DB_BAKPATH");
		$dirname = explode(",",$_REQUEST['idarr']);
        foreach($dirname as $v){
			Rmall($db_dir."/".$v);
		}
		$this->success(L('删除成功'),'',$_REQUEST['idarr']);
	}
	//查看表结构
	public function tables(){
		$table=str_replace(";","",$_REQUEST['tables']);
        $mr = $this->DBReback();
        $r = $mr->tableColumns($table);
		$this->assign("tablecontent",$r);
        $this->display();
	}
	//数据备份打包
	public function dozip(){
		$zippath = C("WEB_ROOT").C("ZIP_PATH");
		$dbpath = C("DB_BAKPATH");
		MakeDir($zippath);
		$bakup=$_REQUEST['bakup'];
		//zip文件名
		$zipname=date("YmdHis",time()).rand(1,100).$bakup.".zip";
		import('ORG.Util.Phpzip');
		$z=new PHPZip(); //新建立一个zip的类
		$res = $z->Zip($dbpath."/".$bakup,$zippath."/".$zipname); //添加指定目录
		if($res==1){
			$this->success($zipname,'',__APP__."/".C("ZIP_PATH")."/".$zipname);
		}else{
			$this->error("压缩失败");
		}
	}
	//下载备份
	public function download(){
		$down=$_REQUEST['url'];
		$zipname=$_REQUEST['zipname'];
		$this->assign("downurl",$down);
		$this->assign("zipname",$zipname);
        $this->display();
	}
	//删除备份
	public function delzip(){
		$zipname=$_REQUEST['zipname'];
		$zippath = C("WEB_ROOT").C("ZIP_PATH")."/";
		$zipfile=$zippath.$zipname;
		if(is_file($zipfile)) $res = unlink($zipfile);
		if($res==1){
			$this->success("zip备份删除成功");
		}else{
			$this->error("zip备份删除失败，或者文件不存在");
		}
		
	}
	//备份参数
    public function set(){
        $this->display();
    }
	//恢复数据
	public function restore(){
        $data_dir = C("DB_BAKPATH").'/'.$_REQUEST['path']."/";
        
        $mr = $this->DBReback($data_dir);
       
        $_SESSION['recover_sql_file'] = $this->getFileName($data_dir); 
        $fileArray = $_SESSION['recover_sql_file']; 
        $n = isset($_GET['n'])?$_GET['n']:0;
        if(!empty($fileArray[$n])){   
            $mr->recover($fileArray[$n]);
            $n++;
            echo "<meta http-equiv=\"refresh\" content=\"".$this->waitbaktime.";
                  url=".__URL__."/restore?path=".$_REQUEST['path']."&n=".$n."\">还原数据库进行中，请不要其他操作以保数据完整第($n)卷......";
        }else{
            unset($_SESSION['recover_sql_file']);
            $this->success("数据还原成功", u('index'));
        }
	}
    //执行备份(按文件大小)
    //$savepath 文件保存路径 
    //fnum当前表的字段数
    public function backup(){
        $savepath=$_REQUEST['savepath']?$_REQUEST['savepath']:date("YmdHis",time());
        $savepath = C("DB_BAKPATH").'/'.$savepath."/"; 
        $mr = $this->DBReback($savepath);
        $mr->setFileSize($this->b_filesize);
        
        if($_REQUEST['baktable']){
            $b_table = explode(",", $_REQUEST['baktable']);//要备份的表checkbox$_POST['checkbox'];
        }
        $mr->backup($b_table);
        $this->writeInfo($_REQUEST['info'], $data_dir.$savepath.'/');
        $this->success( '数据备份完成' );
        exit; 
    }

    private function DBReback($savePath=''){
        $data_dir = $savePath;
        import("ORG.Net.DBReback");
        $config = array(
            'host'            => C('DB_HOST'),
            'port'            => C('DB_PORT'),
            'userName'        => C('DB_USER'),
            'userPassword'    => C('DB_PWD'),
            'dbprefix'        => C('DB_PREFIX'),
            'dbname'          => C('DB_NAME'),
            'charset'        => 'UTF8',
            'path'            => $data_dir,
        );
        
        return new DBReback($config);
    }
    
    private function getFileName($db_dir){   
        if(!empty($db_dir) && $od=opendir($db_dir))   //$d是目录名
        {
            $fileArray = array();
            while(($file=readdir($od))!==false)  //读取目录内文件
            {
                preg_match('|\.(\w+)$|i',$file, $ext);
                $extend = strtolower($ext[1]);//文件后缀
                if($file!="." && $file!=".." && !is_dir($db_dir."/".$file) && $extend=="sql"){
                    $fileArray[] = $file; 
                }   
            }
        }
        return $fileArray;
    }
    
	//已备份数据
    public function browse(){
		$list=array();
		$db_dir = C("DB_BAKPATH");
		if(!empty($db_dir) && $od=opendir($db_dir))   //$d是目录名
		{		
				while(($file=readdir($od))!==false)  //读取目录内文件
				{
					if($file!="." && $file!=".." && is_dir($db_dir."/".$file)){
						$row=array();
						$row['dirname'] = $file;
						//备份文件夹内部文件
						if($od2=opendir($db_dir."/".$file) ){
							while(($file2=readdir($od2))!==false)  //读取目录内文件
							{
								preg_match('|\.(\w+)$|i',$file2, $ext);
								$extend = strtolower($ext[1]);//文件后缀
								
								if($file2!="." && $file2!=".." && !is_dir($db_dir."/$file/".$file2)){
									if($extend=="txt"){
										$row['baktime'] = date("Y-m-d H:i:s",filemtime("$db_dir/$file/$file2"));
										$row['bakdetail'] = ReadFiletext("$db_dir/$file/$file2");
									}
									$row['baksize'] = $row['baksize'] + filesize("$db_dir/$file/$file2");
								}
							}
						}
						//备份文件夹内部文件
						$list[]=$row;
					}
				}//while
		}

		$this->assign("baklist",$list);
        $this->display();
    }

    private function writeInfo($info, $path){
        if(is_dir($path)){
            $filename = $path."info.txt";
            $info = !empty($info)?$info:'无';
            if(!file_put_contents($filename, $info)){
                $this->error("说明信息写入失败！");
            }
        }
    }
}
//得到文件大小
function getMb($size){
	$mbsize=$size/1024/1024;
	if($mbsize>0)
	{
		list($t1,$t2)=explode(".",$mbsize);
		$mbsize=$t1.".".substr($t2,0,2);
	}
	if($mbsize<1){
		$kbsize=$size/1024;
		list($t1,$t2)=explode(".",$kbsize);
		$kbsize=$t1.".".substr($t2,0,2);
		return $kbsize."KB";
	}else{
		return "<span style='color:blue'>".$mbsize."MB</span>";
	}
}

?>