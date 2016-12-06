<?php   
/**
 * 文件管理逻辑类
 *
 * @version        $Id: file_class.php 1 19:09 2010年7月12日Z tianya $
 * @package        DedeCMS.Administrator
 * @copyright      Copyright (c) 2007 - 2010, DesDev, Inc.
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */
class FileManagement
{
    private $baseDir="";
    private $currentDir="";

    //是否允许文件管理器删除目录；
    //默认为不允许 0 ,如果希望可能管理整个目录,请把值设为 1 ；
    private $allowDeleteDir=0;
	
	function currentDir($b=""){
		$this->baseDir=substr(C("WEB_ROOT"),0,strlen(C("WEB_ROOT"))-1);//不带最后的"/"
		$this->currentDir = $b;
	}
	
    //更改文件名
    function RenameFile($oldname,$newname)
    {	
		$r=false;
        $oldname = $this->baseDir.$this->currentDir.$oldname;
        $newname = $this->baseDir.$this->currentDir.$newname;
        if(($newname!=$oldname) && is_writable($oldname))
        {
            $r = rename($oldname,$newname);
        }
        return $r;
    }

    //创建新目录
    function NewDir($dirname)
    {
        $newdir = $dirname;
        $dirname = $this->baseDir.$this->currentDir.$dirname;
        if(is_writable($this->baseDir.$this->currentDir))
        {
            MakeDir($dirname);
            return 1;
        }
        else
        {
            return 0;
        }
    }

    /**
     *  移动文件
     *
     * @access    public
     * @param     string  $mfile  文件
     * @param     string  $mpath  路径
     * @return    string
     */
    function MoveFile($mfile, $mpath)
    {
        if($mpath!="" && !preg_match("#\.\.#", $mpath))
        {
            $oldfile = $this->baseDir.$mfile;//带路径的mfile
			$truepath = $this->baseDir.$mpath;//最后带/的truepath
            if(is_readable($oldfile) && is_readable($truepath) && is_writable($truepath))
            {
                if(is_dir($truepath))
                {
                    $r = copy($oldfile, $truepath.basename($mfile));
                }
                else
                {
                    MakeDir($truepath);
                    copy($oldfile,$truepath.basename($mfile));
                }
                if($r){
					unlink($oldfile);
					return 1;
				}else{
					return 0;
				}
            }
            else
            {
                return "移动文件$oldfile -&gt; ".$truepath.basename($mfile)." 失败，可能是某个位置权限不足！";
            }
        }
        else
        {
            return "对不起，你移动的路径不合法！";
        }
    }

    /**
     * 删除目录
     *
     * @param unknown_type $indir
     */
    function RmDirFiles($indir)
    {
		$r=false;
		if(!empty($indir)) $indir = $this->baseDir.$this->currentDir.$indir;
		else return false;

        if(!is_dir($indir))
        {
            return ;
        }
        $dh = dir($indir);
        while($filename = $dh->read())
        {
            if($filename == "." || $filename == "..")
            {
                continue;
            }
            else if(is_file("$indir/$filename"))
            {
                @unlink("$indir/$filename");
            }
            else
            {
                self::RmDirFiles("$indir/$filename/");
            }
        }
        $dh->close();
        @$r = rmdir($indir);
		return $r;
    }

    /**
     * 获得某目录合符规则的文件
     *
     * @param unknown_type $indir
     * @param unknown_type $fileexp
     * @param unknown_type $filearr
     */
    function GetMatchFiles($indir, $fileexp, &$filearr)
    {
        $dh = dir($indir);
        while($filename = $dh->read())
        {
            $truefile = $indir.'/'.$filename;
            if($filename == "." || $filename == "..")
            {
                continue;
            }
            else if(is_dir($truefile))
            {
                $this->GetMatchFiles($truefile, $fileexp, $filearr);
            }
            else if(preg_match("/\.(".$fileexp.")/i",$filename))
            {
                $filearr[] = $truefile;
            }
        }
        $dh->close();
    }

    /**
     * 删除文件
     *
     * @param unknown_type $filename
     * @return unknown
     */
    function DeleteFile($filename)
    {
        $filename = $this->baseDir.$this->currentDir."/".$filename;
        if(is_file($filename))
        {
            @$r = unlink($filename);
        }
        return $r;
    }
}