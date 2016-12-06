<?php
class FilesAction extends AdminAction{
	private $inpath="";//默认打开目录
	private $currentdir="";//当前打开的目录
	private $updir="";//上级目录
	private $baseDir="";//根目录
	private $totalsize=0;//空间占用
	/*
	所有DIR传值都以带前后"/"传,所有文件都不带"/"
	*/
	public function _MyInit() {
	
		$this->baseDir = substr(C("WEB_ROOT"),0,strlen(C("WEB_ROOT"))-1);//不带最后的"/"
		
		$currentDir = isset($_REQUEST['newpath'])?$_REQUEST['newpath']:cookie("cpath");//用cookie保存当前目录,以支持ajax等不带目录参数的操作
		if(empty($currentDir)) $currentDir="/";//根目录"/"
		if(substr($currentDir,strlen($currentDir)-1,1)!="/") $currentDir.="/";//如果文件夹的传入没有加最后的"/"则自动加上
		
		$this->currentdir = $currentDir;//当前打开的目录
		cookie("cpath",$this->currentdir,36000);
		$this->inpath=$this->baseDir.$currentDir;//新目录/无新目录里默认打开baseDir
		if(!is_dir($this->inpath)){
			$this->inpath = $this->baseDir;//避免文件夹被删除后cpath没更新而导致出错
		}
		
		if($this->currentdir=="/"){
			$this->updir = "/";//上级目录
		}else{
			$d = explode("/",$this->currentdir);
			$len = count($d);
			unset($d[($len-1)],$d[($len-2)]);//删除最后一层目录
			$this->updir = implode("/",$d)."/";//上级目录
		}
		
		import('ORG.Util.File');
		FileManagement::currentDir($this->currentdir);//初始化位置信息//都是带最后一个"/"的
	}
    /**
    +----------------------------------------------------------
    * 默认操作
    +----------------------------------------------------------
    */
	
    public function index()
    {
		$this->_listdirfile();
        $this->assign('updir',$this->updir);
        $this->display();
    }
	//所有对文件及文件夹的操作都不需要考虑路径,只要传入当前要操作的对像的文件名或者文件夹名,路径都在FileManagement类里面处理
	
	//检测当前文件夹占用空间大小
    public function checksize()
    {
		$size = $this->_checksize($this->inpath);
		$this->assign('dir',$this->currentdir);
		$this->assign('size',$size);
        $this->display();
    }
	
	//列出文件夹ajax
	public function listdirchoose(){
			$this->_ListDir($listdir,$id);
			$this->display();
	}
	
	//列出文件夹ajax
	public function listdir(){
			$sid = $_REQUEST['sid']?$_REQUEST['sid']:'';
			$sdir = $_REQUEST['s_dir']?$_REQUEST['s_dir']:'';
			
			$listdir = $_REQUEST['l_dir']?$_REQUEST['l_dir']:'';
			$id = $_REQUEST['li_id']?intval($_REQUEST['li_id']):'0';
			$this->_ListDir($listdir,$id);
			$this->assign('sdir',$sdir);
			$this->assign('sid',$sid);
			$this->display();
	}

	protected function _buildtrdir($dir,$currentdir,$filesize,$filetime,$id){
		$tr = '<tr bgcolor="#FFFFFF" overstyle="on" align="left" id="line_'.$id.'">
				   <td style="text-align:left">
				   <a href="__URL__?newpath='.$currentdir.$dir.'/" target="_self">
				   <img src="__ROOT__/tpl/Admin/default/Res/images/ico/dir.png" width="20px" height="20px"align="absbottom" style="margin-right:5px">'.$dir.'</a>
				   </td>
				   <td> </td>
				   <td align="center">'.$filetime.'</td>
				   </tr>';
		
		return $tr;
	
	}
	
	protected function _buildtrfile($file,$currentdir,$filesize,$filetime,$id){
		preg_match('|\.(\w+)$|i',$file, $ext);
		switch (strtolower($ext[1])){
			case "php":
				$ico="php.png";
				break;
			case "css":
				$ico="css.png";
				break;
			case "js":
				$ico="js.png";
				break;
			case "txt":
				$ico="txt.png";
				break;
			case "swf":
				$ico="swf.png";
				break;
			case "docx":
			case "doc":
				$ico="word.png";
				break;
			case "xlsx":
			case "xls":
				$ico="excel.png";
				break;
			case "htm":
			case "html":
				$ico="html.png";
				break;
			case "png":
			case "gif":
			case "jpg":
			case "bmp":
				$ico="pic.png";
				break;
			default :
				$ico="other.png";
		}
	
		$tr = '<tr bgcolor="#FFFFFF" overstyle="on" align="left" id="line_'.$id.'">
				   <td style="text-align:left">
				   <img src="__ROOT__/tpl/Admin/default/Res/images/ico/'.$ico.'" width="20px" height="20px" align="absbottom" style="margin-right:5px">'.$file.'
				   </td>
				   <td style="text-align:right">'.(($filesize>0)?$filesize."KB":' ').'</td>
				   <td align="center">'.$filetime.'</td>
				  </tr>';
		
		return $tr;
	
	}
	
	protected function _listdirfile(){
		$inpath=$this->inpath;
		$updir=$this->updir;
		$currentdir=$this->currentdir;

		$dh = dir($inpath);
		$ty1=$ty2="";
		$files = $dirs = array();
		$i=0;
		while(($file = $dh->read())!== false)
		{
			$i++;
			
			if($file!="." && $file!=".." && !is_dir($inpath."/".$file))
			{
				$filesize = filesize($inpath.$file);
				$filesize=$filesize/1024;
				$filetime = filemtime($inpath.$file);
				$filetime = date("Y-m-d H:i:s",$filetime);
				if($filesize<0.1)
				{
					list($ty1,$ty2)=explode(".",$filesize);
					$filesize=$ty1.".".substr($ty2,0,2);
				}
				else
				{
					list($ty1,$ty2)=explode(".",$filesize);
					$filesize=$ty1.".".substr($ty2,0,1);
				}
			}
			$file=iconv("gb2312","UTF-8",$file);//避免中文乱码,要在读取后再转码,避免不能正常读取

			if($file == ".")
			{
				continue;
			}elseif($file == ".."){
				$dirs[] = '<tr overstyle="on" align="left"><td bgcolor="#ffffff" style="text-align:left"><a href="__URL__?newpath='.$updir.'" target="_self"><img src="__ROOT__/tpl/Admin/default/Res/images/ico/up.png" align="absbottom" width="20px" height="20px" style="margin-right:5px" />上级目录</a></td><td colspan="3" bgcolor="#ffffff" style="text-align:left">当前目录:'.$currentdir."</td></tr>";
			}elseif(is_dir($inpath.$file)){
				$dirs[] = $this->_buildtrdir($file,$currentdir,$filesize,$filetime,$i);
			}else{
				$files[] = $this->_buildtrfile($file,$currentdir,$filesize,$filetime,$i);
			}
		}
		$list = array_merge($dirs,$files);
		$this->assign('vo',$list);	
	}

	protected function _ListDir($dir="",$id)
	{
		$dirname = $this->baseDir.$dir;
		if(empty($dir)) $dirname.="/";
		$Ld = dir($dirname);
		$list="<ul>\r\n";
		$i=0;
		while (false !== ($entry = $Ld->read())) {
			$checkdir = $dirname.$entry;
			if (is_dir($checkdir)&&!preg_match("[^\.]",$entry)){
			   $i++;
			   $checkdir=str_replace($this->baseDir."/","",$checkdir);//替换掉根目录,保持目录结构
			   $list.="<li><span class='listdirimg diron' data='/".$checkdir."/'>&nbsp;</span><img src='".__ROOT__."/tpl/Admin/default/Res/images/ico/dir.png' width='20px' height='20px' align='absbottom' data='/".$checkdir."/'>".$entry."</li>\r\n";
			}else{
				//echo "<li><p>".$entry."当前不是目录</p></li>";
			}
		}
		$Ld->close();
		$list.="</ul>";
		$data['inner'] = $list;
		$data['liid'] = $id;
		if(empty($dir)) $this->assign("listdir",$list);
		else $this->ajaxReturn($data,"");
	}

    protected function _checksize($indir)
    {
        $dh=dir($indir);
        while($filename=$dh->read())
        {
            if(!preg_match("#^\.#", $filename))
            {
                if(is_dir("$indir/$filename"))
                {
                    $this->_checksize("$indir/$filename");
                }
                else
                {
                    $this->totalsize=$this->totalsize + filesize("$indir/$filename");
                }
            }
        }
		return $this->setmb($this->totalsize);
    }
	
    protected function setmb($size)
    {
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
			return $mbsize."MB";
		}
		
    }
	
	
}
?>