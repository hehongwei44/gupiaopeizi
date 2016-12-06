<?php
// +----------------------------------------------------------------------
// | ThinkPHP
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2012 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id$
class TagLibHtmlA extends TagLib{
    // 标签定义
    protected $tags   =  array(
        // 标签定义： attr 属性列表 close 是否闭合（0 或者1 默认1不闭合） alias 标签别名 level 嵌套层次
        'commonBtn'=>array('attr'=>'value,style,action,type','close'=>0),
        'input'=>array('attr'=>'id,style,value,tip,class','close'=>0),
        'timer'=>array('attr'=>'id,style,value,tip,arg','close'=>0),
        'radio'=>array('attr'=>'id,style,value,datakey,vt,tip,separator','close'=>0),
        'text'=>array('attr'=>'id,style,value,tip,class,addstr','close'=>0),
        'editor'=>array('attr'=>'id,style,w,h,type,value,type','close'=>0),
        'select'=>array('attr'=>'id,style,value,datakey,vt,tip,default,multiple,ishtml,NoDefalut,class','close'=>0),
        'checkbox'=>array('attr'=>'name,checkboxes,checked,separator','close'=>0),
        'user'=>array('attr'=>'id,uname','close'=>0),
		'tixianing'=>array('attr'=>'id,uname','close'=>0),
		'tixianwait'=>array('attr'=>'id,uname','close'=>0),
		'tixian'=>array('attr'=>'id,uname','close'=>0),//新增提现编辑信息扩展功能 添加人：fanyelei 添加时间：2012-12-02 09:10
		'grid'=>array('attr'=>'id,pk,style,action,actionlist,show,datasource','close'=>0),
        'list'=>array('attr'=>'id,pk,style,action,actionlist,show,datasource,checkbox','close'=>0),
        'imagebtn'=>array('attr'=>'id,name,value,type,style,click','close'=>0),
        );
 
    public function _user($attr)
    {
        $tag        = $this->parseXmlAttr($attr,'user');
        $uid      	= $tag['id'];                //文字
        $uname      = $tag['uname'];                //样式名
		$parseStr="";
		$parseStr = '<a onclick="loadUser({$'.$uid.'},\'{$'.$uname.'}\')" href="javascript:void(0);">{$'.$uname.'}</a>';
        return $parseStr;
    }

    /**
     +----------------------------------------------------------
     * commonBtn标签解析
     * 格式： <html:commonBtn type="" value="" />
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $attr 标签属性
     +----------------------------------------------------------
     * @return string|void
     +----------------------------------------------------------
     */
    public function _commonBtn($attr)
    {
        $tag        = $this->parseXmlAttr($attr,'commonBtn');
        $value      = $tag['value'];                //文字
        $style      = $tag['style'];                //样式名
        $action     = $tag['action'];                //点击
        $type       = $tag['type'];                //按钮类型
		
		$parseStr="";
		
        if($type=="jsfun") {
			$parseStr = '<a onclick="'.$action.'" class="btn_a" href="javascript:void(0);">';
			if(!empty($style)) $parseStr .= '<span class="'.$style.'">'.$value.'</span>';
			else  $parseStr .= '<span>'.$value.'</span>';
            $parseStr .= '</a>';
        }else if($type=="back") {
			$parseStr = '<a onclick="'.$action.'" class="btn_a" href="javascript:history.go(-1);">';
			if(!empty($style)) $parseStr .= '<span class="'.$style.'">'.$value.'</span>';
			else  $parseStr .= '<span>'.$value.'</span>';
            $parseStr .= '</a>';
        }else {
			$parseStr = '<a class="btn_a" href="'.$action.'">';
			if(!empty($style)) $parseStr .= '<span class="'.$style.'">'.$value.'</span>';
			else  $parseStr .= '<span>'.$value.'</span>';
            $parseStr .= '</a>';
        }

        return $parseStr;
    }
    /**
     +----------------------------------------------------------
     * input标签解析
     * 格式： <html:input type="" value="" />
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $attr 标签属性
     +----------------------------------------------------------
     * @return string|void
     +----------------------------------------------------------
     */
    public function _input($attr)
    {
        $tag        = $this->parseXmlAttr($attr,'input');
        $id      	= $tag['id'];                //name 和 id
        $value      = $tag['value']?$tag['value']:'';  //文本框值
        $addstr     = $tag['addstr']?$tag['addstr']:'';  //文本框值
        $tip     	= $tag['tip'];                //span tip提示内容
        $style      = $tag['style'];                //附加样式 style="widht:100"
        $className  = $tag['class']?" ".$tag['class']:'';                //附加样式 style="widht:100"
		
		$parseStr="";
		
        if($tip) {
			if($style) $style='style="'.$style.'"';
			$parseStr = '<input name="'.$id.'" id="'.$id.'" '.$style.' class="input'.$className.'" type="text" value="'.$value.'" '.$addstr.'><span id="tip_'.$id.'" class="tip">'.$tip.'</span>';
        }else {
			if($style) $style='style="'.$style.'"';
			$parseStr = '<input name="'.$id.'" id="'.$id.'" '.$style.' class="input'.$className.'" type="text" value="'.$value.'" '.$addstr.'>';
        }

        return $parseStr;
    }
    /**
     +----------------------------------------------------------
     * timer标签解析
     * 格式： <html:input type="" value="" />
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $attr 标签属性
     +----------------------------------------------------------
     * @return string|void
     +----------------------------------------------------------
     */
    public function _timer($attr)
    {
        $tag        = $this->parseXmlAttr($attr,'input');
        $id      	= $tag['id'];                //name 和 id
        $value      = $tag['value']?$tag['value']:'';  //文本框值
        $class      = $tag['class']?" ".$tag['class']:'';  //文本框值
        $arg      	= $tag['arg']?$tag['arg']:'';  //文本框值
        $tip     	= $tag['tip'];                //span tip提示内容
        $style      = $tag['style'];                //附加样式 style="widht:100"
		
		$parseStr="";
		
        if($tip) {
			if($style) $style='style="'.$style.'"';
			$parseStr = '<input onclick="WdatePicker('.$arg.');" name="'.$id.'" id="'.$id.'" '.$style.' class="input'.$class.'" type="text" value="'.$value.'"><span id="tip_'.$id.'" class="tip">'.$tip.'</span>';
        }else {
			if($style) $style='style="'.$style.'"';
			$parseStr = '<input onclick="WdatePicker('.$arg.');" name="'.$id.'" id="'.$id.'" '.$style.' class="input'.$class.'" type="text" value="'.$value.'">';
        }

        return $parseStr;
    }

    /**
     +----------------------------------------------------------
     * radio标签解析
     * 格式： <html:radio type="" value="" />
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $attr 标签属性
     +----------------------------------------------------------
     * @return string|void
     +----------------------------------------------------------
     */
    public function _radio($attr)
    {
        $tag        = $this->parseXmlAttr($attr,'radio');
        $id      	= $tag['id'];                //name 和 id
        $style      = $tag['style']?$tag['style']:'';                //附加样式 style="widht:100"
        $tip      	= $tag['tip'];                //附加样式 style="widht:100"
        $value      = $tag['value']?$tag['value']:'';  //(key|value)|text,当前默认选中一维时key指键,value指值
        $default    = $tag['default']?$tag['default']:'';  //默认数据，不是动态获取的，以value|text,value1|text1的方式传入
        $datakey    = $tag['datakey'];                //要排列的内容以模板内赋值的名称传入,支持一维和二维
        $key     	= $tag['vt'];                //  valuekey|textkey,值键和文本健//二维数组时才需要
        $separator  = $tag['separator']?$tag['separator']:"&nbsp;&nbsp;&nbsp;&nbsp;";			//分隔符
        $addstr     = $tag['addstr']?$tag['addstr']:'';  //符加参数等
		$data = $this->tpl->get($datakey);//以名称获取模板变量

		$valueto = explode("|",$value);
 		if($valueto[0])	$valuekv = explode(".",$valueto[1]);
		$parseStr="";
		if($style) $style='style="'.$style.'"';
		$default_array=explode(",",$default);
		$default=array();
		foreach($default_array as $dkey=>$dv){
			$dxkv = explode("|",$dv);
			$default[$dxkv[0]] = $dxkv[1];
		}
		
        if($key) {
			if(empty($valuekv[0])) $valuekv[0]='_X';
			$keyto = explode("|",$key);
			$parseStr .='<php>$i=0;foreach($'.$datakey.' as $k=>$v){</php>';
			$parseStr .='<php> ';
			$parseStr .='if("!'.$valueto[0].'" && $i==0){';
			$parseStr .='</php>';
			$parseStr.='<input type="radio" name="'.$id.'" value="{$v["'.$keyto[0].'"]}" id="'.$id.'_{$i}" checked="checked" '.$addstr.'/>';
			$parseStr .='<php> ';
			$parseStr .='}elseif($'.$valuekv[0].'["'.$valuekv[1].'"]&&$v["'.$valueto[0].'"]==$'.$valuekv[0].'["'.$valuekv[1].'"]){';
			$parseStr .='</php>';
			$parseStr.='<input type="radio" name="'.$id.'" value="{$v["'.$keyto[0].'"]}" id="'.$id.'_{$i}" checked="checked" '.$addstr.'/>';
			$parseStr .='<php> ';
			$parseStr .='}else{';
			$parseStr .='</php>';
			$parseStr.='<input type="radio" name="'.$id.'" value="{$v["'.$keyto[0].'"]}" id="'.$id.'_{$i}" '.$addstr.'/>';	
			$parseStr .='<php>}</php>';
			$parseStr.='<label for="'.$id.'_{$i}">{$v["'.$keyto[1].'"]}</label>'.$separator;
			$parseStr .='<php>$i++;}</php>';
        }elseif($datakey && !empty($value)){
			$parseStr .='<php>$i=0;foreach($'.$datakey.' as $k=>$v){</php>';
			$parseStr .='<php> if(strlen("'.$valueto[0].'1")==1&&$i==0){</php>';
			$parseStr.='<input type="radio" name="'.$id.'" value="{$k}" id="'.$id.'_{$i}" checked="checked" '.$addstr.'/>';
			$parseStr .='<php> }elseif("'.$valueto[0].'1"=="key1"&&$k==$'.$valuekv[0].'["'.$valuekv[1].'"]){</php>';
			$parseStr.='<input type="radio" name="'.$id.'" value="{$k}" id="'.$id.'_{$i}" checked="checked" '.$addstr.'/>';
			$parseStr .='<php> }elseif("'.$valueto[0].'1"=="value1"&&$v==$'.$valuekv[0].'["'.$valuekv[1].'"]){</php>';
			$parseStr.='<input type="radio" name="'.$id.'" value="{$k}" id="'.$id.'_{$i}" checked="checked" '.$addstr.'/>';
			$parseStr .='<php> }else{ </php>';
			$parseStr.='<input type="radio" name="'.$id.'" value="{$k}" id="'.$id.'_{$i}" '.$addstr.'/>';	
			$parseStr .='<php> } </php>';
			$parseStr.='<label for="'.$id.'_{$i}">{$v}</label>'.$separator;
			$parseStr .='<php>$i++;}</php>';
		}elseif($datakey && empty($value)){
			$parseStr .='<php>$i=0;foreach($'.$datakey.' as $k=>$v){</php>';
			$parseStr .='<php> if($i==0){</php>';
			$parseStr.='<input type="radio" name="'.$id.'" value="{$k}" id="'.$id.'_{$i}" checked="checked" '.$addstr.'/>';
			$parseStr .='<php> }else{ </php>';
			$parseStr.='<input type="radio" name="'.$id.'" value="{$k}" id="'.$id.'_{$i}" '.$addstr.'/>';	
			$parseStr .='<php> } </php>';
			$parseStr.='<label for="'.$id.'_{$i}">{$v}</label>'.$separator;
			$parseStr .='<php>$i++;}</php>';
		}else{
			if(empty($valuekv[0])){
				$valuekv[0]='_X';
				$valuekv[1]='_Y';
			}
			$parseStr .='<php>$i=0;$___KEY='.var_export($default,true).';</php>';
			$parseStr .='<php>foreach($___KEY as $k=>$v){</php>';
			
			$parseStr .='<php>if(strlen("1'.$valueto[0].'")==1 && $i==0){</php>';
			$parseStr.='<input type="radio" name="'.$id.'" value="{$k}" id="'.$id.'_{$i}" checked="checked" '.$addstr.'/>';
			$parseStr .='<php>}elseif(("'.$valueto[0].'1"=="key1"&&$'.$valuekv[0].'["'.$valuekv[1].'"]==$k)||("'.$valueto[0].'"=="value"&&$'.$valuekv[0].'["'.$valuekv[1].'"]==$v)){';
			$parseStr .='</php>';
			$parseStr.='<input type="radio" name="'.$id.'" value="{$k}" id="'.$id.'_{$i}" checked="checked" '.$addstr.'/>';
			$parseStr .='<php> }else{</php>';
			$parseStr.='<input type="radio" name="'.$id.'" value="{$k}" id="'.$id.'_{$i}" '.$addstr.'/>';
			$parseStr .='<php>}</php>';
			$parseStr.='<label for="'.$id.'_{$i}">{$v}</label>'.$separator;
			$parseStr .='<php>$i++;</php>';
			$parseStr .='<php>}</php>';
        }
		if($tip) $parseStr.='<span id="tip_'.$id.'" class="tip">'.$tip.'</span>';
        return $parseStr;
    }
     /**
     +----------------------------------------------------------
     * text标签解析
     * 格式： <html:text type="" value="" />
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $attr 标签属性
     +----------------------------------------------------------
     * @return string|void
     +----------------------------------------------------------
     */
    public function _text($attr)
    {
        $tag        = $this->parseXmlAttr($attr,'input');
        $id      	= $tag['id'];                //name 和 id
        $value      = $tag['value']?$tag['value']:'';  //文本框值
        $tip     	= $tag['tip'];                //span tip提示内容
        $style      = $tag['style']?$tag['style']:'';                //附加样式 style="widht:100"
	    $className  = $tag['class']?" ".$tag['class']:'';                //附加样式 style="widht:100"
	    $addstr  	= $tag['addstr']?$tag['addstr']:'';                //附加样式 style="widht:100"
	
		$parseStr="";
		
        if($tip) {
			if($style) $style='style="'.$style.'"';
			$parseStr = '<textarea name="'.$id.'" id="'.$id.'" '.$style.' class="areabox'.$className.'" '.$addstr.'>'.$value.'</textarea><span id="tip_'.$id.'" class="tip">'.$tip.'</span>';
        }else {
			if($style) $style='style="'.$style.'"';
			$parseStr = '<textarea name="'.$id.'" id="'.$id.'" '.$style.' class="areabox'.$className.'" '.$addstr.'>'.$value.'</textarea>';
        }

        return $parseStr;
    }

   /**
     +----------------------------------------------------------
     * editor标签解析 插入可视化编辑器
     * 格式： <html:editor id="editor" name="remark" type="FCKeditor" style="" >{$vo.remark}</html:editor>
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $attr 标签属性
     +----------------------------------------------------------
     * @return string|void
     +----------------------------------------------------------
     */
    public function _editor($attr,$content)
    {
        $tag        =	$this->parseXmlAttr($attr,'editor');
        $id			=	$tag['id'];
        $style   	=	$tag['style']?$tag['style']:'';
        $value   	=	$tag['value']?$tag['value']:'';
        $type   	=	$tag['type'];
        $width		=	!empty($tag['w'])?$tag['w']: '100%';
        $height     =	!empty($tag['h'])?$tag['h'] :'320px';
        $type       =   $tag['type'] ;
        switch(strtoupper($type)) {
            case 'KISSY':
                $parseStr   =	'<!-- 编辑器调用开始 -->
				<textarea name="'.$id.'" id="'.$id.'" style="width:'.$width.';height:'.$height.';'.$style.'">'.$value.'</textarea>
				<script>
				
					loadEditor("'.$id.'");
				
				</script>
				<!-- 编辑器调用结束 -->';
             break;
			 case 'FCKEDITOR':
                $parseStr   =	'<!-- 编辑器调用开始 --><script type="text/javascript" src="__ROOT__/Public/Js/FCKeditor/fckeditor.js"></script><textarea id="'.$id.'" style="width:'.$width.';height:'.$height.';'.$style.'">'.$value.'</textarea><script type="text/javascript"> var oFCKeditor = new FCKeditor( "'.$id.'","'.$width.'","'.$height.'" ) ; oFCKeditor.BasePath = "__ROOT__/Public/Js/FCKeditor/" ; oFCKeditor.ReplaceTextarea() ;function resetEditor(){setContents("'.$id.'",document.getElementById("'.$id.'").value)}; function saveEditor(){document.getElementById("'.$id.'").value = getContents("'.$id.'");} function InsertHTML(html){ var oEditor = FCKeditorAPI.GetInstance("'.$id.'") ;if (oEditor.EditMode == FCK_EDITMODE_WYSIWYG ){oEditor.InsertHtml(html) ;}else	alert( "FCK必须处于WYSIWYG模式!" ) ;}</script> <!-- 编辑器调用结束 -->';
                break;
            case 'FCKMINI':
                $parseStr   =	'<!-- 编辑器调用开始 --><script type="text/javascript" src="__ROOT__/Public/Js/FCKMini/fckeditor.js"></script><textarea id="'.$id.'" style="width:'.$width.';height:'.$height.';'.$style.'">'.$value.'</textarea><script type="text/javascript"> var oFCKeditor = new FCKeditor( "'.$id.'","'.$width.'","'.$height.'" ) ; oFCKeditor.BasePath = "__ROOT__/Public/Js/FCKMini/" ; oFCKeditor.ReplaceTextarea() ;function resetEditor(){setContents("'.$id.'",document.getElementById("'.$id.'").value)}; function saveEditor(){document.getElementById("'.$id.'").value = getContents("'.$id.'");} function InsertHTML(html){ var oEditor = FCKeditorAPI.GetInstance("'.$id.'") ;if (oEditor.EditMode == FCK_EDITMODE_WYSIWYG ){oEditor.InsertHtml(html) ;}else	alert( "FCK必须处于WYSIWYG模式!" ) ;}</script> <!-- 编辑器调用结束 -->';
                break;
            case 'EWEBEDITOR':
                $parseStr	=	"<!-- 编辑器调用开始 --><script type='text/javascript' src='__ROOT__/Public/Js/eWebEditor/js/edit.js'></script><input type='hidden'  id='{$id}' value='{$value}'><iframe src='__ROOT__/Public/Js/eWebEditor/ewebeditor.htm?id={$name}' frameborder=0 scrolling=no width='{$width}' height='{$height}'></iframe><script type='text/javascript'>function saveEditor(){document.getElementById('{$id}').value = getHTML();} </script><!-- 编辑器调用结束 -->";
                break;
            case 'NETEASE':
                $parseStr   =	'<!-- 编辑器调用开始 --><textarea id="'.$id.'" style="display:none">'.$value.'</textarea><iframe ID="Editor" name="Editor" src="__ROOT__/Public/Js/HtmlEditor/index.html?ID='.$id.'" frameBorder="0" marginHeight="0" marginWidth="0" scrolling="No" style="height:'.$height.';width:'.$width.'"></iframe><!-- 编辑器调用结束 -->';
                break;
            case 'UBB':
                $parseStr	=	'<script type="text/javascript" src="__ROOT__/Public/Js/UbbEditor.js"></script><div style="padding:1px;width:'.$width.';border:1px solid silver;float:left;"><script LANGUAGE="JavaScript"> showTool(); </script></div><div><TEXTAREA id="UBBEditor" style="clear:both;float:none;width:'.$width.';height:'.$height.'" >'.$value.'</TEXTAREA></div><div style="padding:1px;width:'.$width.';border:1px solid silver;float:left;"><script LANGUAGE="JavaScript">showEmot();  </script></div>';
                break;
            case 'KINDEDITOR':
                $parseStr   =  '<script type="text/javascript" src="__ROOT__/Public/Js/KindEditor/kindeditor.js"></script><script type="text/javascript"> KE.show({ id : \''.$id.'\'  ,urlType : "absolute"});</script><textarea id="'.$id.'" style="'.$style.'" >'.$value.'</textarea>';
                break;
            default :
                $parseStr  =  '<textarea  name="'.$id.'" id="'.$id.'" style="width:'.$width.';height:'.$height.';'.$style.'" >'.$value.'</textarea>';
        }

        return $parseStr;
    }

    /**
     +----------------------------------------------------------
     * select标签解析
     * 格式： <html:select options="name" selected="value" />
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $attr 标签属性
     +----------------------------------------------------------
     * @return string|void
     +----------------------------------------------------------
     */
    public function _select($attr)
    {
        $tag        = $this->parseXmlAttr($attr,'select');
        $id      	= $tag['id'];                //name 和 id
        $style      = $tag['style']?$tag['style']:'';                //附加样式 style="widht:100"
        $multiple   = $tag['multiple']?$tag['multiple']:'';
        $ishtml     = $tag['ishtml'];                //是用静态还是动态
        $tip      	= $tag['tip'];                //附加样式 style="widht:100"
        $value      = $tag['value']?$tag['value']:'';  //(key|value)|text,当前默认选中一维时'key'指键,'value'指值
        $NoDefalut  = $tag['nodefalut']?$tag['nodefalut']:false;  //(key|value)|text,当前默认选中一维时'key'指键,'value'指值
        $class      = $tag['class']?" ".$tag['class']:'';  //(key|value)|text,当前默认选中一维时'key'指键,'value'指值
        $default    = $tag['default']?$tag['default']:'--请选择--';  //(key|value)|text,当前默认选中一维时'key'指键,'value'指值
        $datakey    = $tag['datakey'];                //要排列的内容以模板内赋值的名称传入,支持一维和二维
        $vt     	= $tag['vt'];                //  valuekey|textkey,值键和文本健//二维数组时才需要
        $addstr     = $tag['addstr']?$tag['addstr']:'';  //符加参数等
		$data = $this->tpl->get($datakey);//以名称获取模板变量
		
		$parseStr="";
 		$valueto = explode("|",$value);
		if($style) $style='style="'.$style.'"';
 		if($valueto[0])	$valuekv = explode(".",$valueto[1]);
		
		if($ishtml){//静态
 		$datahtml = $this->tpl->get($valuekv[0]);//以名称获取模板变量
		$cvalue = $datahtml[$valuekv[1]];
       	if($vt) {
			$keyto = explode("|",$vt);
			$parseStr .='<select name="'.$id.'" id="'.$id.'" '.$style.' '.$addstr.' '.$multiple.' class="c_select'.$class.'">';
			if(!$NoDefalut) $parseStr .='<option value="">'.$default.'</option>';
			foreach($data as $k => $v){
				if($valueto[0] && $v[$valueto[0]]==$cvalue) $parseStr .='<option value="'.$v[$keyto[0]].'" selected="selected">'.$v[$keyto[1]].'</option>';
				else $parseStr .='<option value="'.$v[$keyto[0]].'">'.$v[$keyto[1]].'</option>';
			}
        }else{
			$parseStr .='<select name="'.$id.'" id="'.$id.'" '.$style.' '.$multiple.' '.$addstr.' class="c_select'.$class.'">';
			if(!$NoDefalut) $parseStr .='<option value="">'.$default.'</option>';
			foreach($data as $k => $v){
				if(($valueto[0]=='key'&&$cvalue==$k)||($valueto[0]=='value'&&$cvalue==$v)) $parseStr .='<option value="'.$k.'" selected="selected">'.$v.'</option>';
				else $parseStr .='<option value="'.$k.'">'.$v.'</option>';
			}
		}
        $parseStr   .= '</select>';
		}else{//静态END 动态START
        if($vt) {
			if(empty($valuekv[0])) $valuekv[0]='_X';
			$keyto = explode("|",$vt);
			$parseStr .='<select name="'.$id.'" id="'.$id.'" '.$style.' '.$multiple.' '.$addstr.' class="c_select'.$class.'">';
			if(!$NoDefalut) $parseStr .='<option value="">'.$default.'</option>';
			$parseStr .='<php>foreach($'.$datakey.' as $key=>$v){</php>';
			
			$parseStr .='<php> ';
			$parseStr .='if("'.$valueto[0].'" && $v["'.$valueto[0].'"]==$'.$valuekv[0].'["'.$valuekv[1].'"]){';
			$parseStr .='</php>';
			$parseStr .='<option value="{$v.'.$keyto[0].'}" selected="selected">{$v.'.$keyto[1].'}</option>';
			$parseStr .='<php> ';
			$parseStr .='}else{';
			$parseStr .='</php>';
			$parseStr .='<option value="{$v.'.$keyto[0].'}">{$v.'.$keyto[1].'}</option>';
			$parseStr .='<php> ';
			$parseStr .='}}';
			$parseStr .='</php>';
        }else{
			if(empty($valuekv[0])) $valuekv[0]='_X';
			$parseStr .='<select name="'.$id.'" id="'.$id.'" '.$style.' '.$multiple.' '.$addstr.' class="c_select'.$class.'">';
			if(!$NoDefalut) $parseStr .='<option value="">'.$default.'</option>';
			$parseStr .='<php>foreach($'.$datakey.' as $key=>$v){</php>';
			$parseStr .='<php> ';
			$parseStr .='if($'.$valuekv[0].'["'.$valuekv[1].'"]==$key && $'.$valuekv[0].'["'.$valuekv[1].'"]!=""){';
			$parseStr .='</php>';
			$parseStr .='<option value="{$key}" selected="selected">{$v}</option>';
			$parseStr .='<php> ';
			$parseStr .='}else{';
			$parseStr .='</php>';
			$parseStr .='<option value="{$key}">{$v}</option>';
			$parseStr .='<php> ';
			$parseStr .='}}';
			$parseStr .='</php>';
		}
        $parseStr   .= '</select>';
		}//动态END
		if($tip) $parseStr.='<span id="tip_'.$id.'" class="tip">'.$tip.'</span>';
        return $parseStr;
    }

    /**
     +----------------------------------------------------------
     * checkbox标签解析
     * 格式： <htmlA:checkbox type="" value="" />
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $attr 标签属性
     +----------------------------------------------------------
     * @return string|void
     +----------------------------------------------------------
     */
    public function _checkbox($attr)
    {
        $tag        = $this->parseXmlAttr($attr,'checkbox');
        $id      	= $tag['id'];                //name 和 id
        $style      = $tag['style'];                //附加样式 style="widht:100"
        $tip      	= $tag['tip'];                //附加样式 style="widht:100"
        $value      = $tag['value']?$tag['value']:'';  //(key|value)|text,当前默认选中一维时key指键,value指值
        $datakey    = $tag['datakey'];                //要排列的内容以模板内赋值的名称传入,支持一维和二维
        $key     	= $tag['vt'];                //  valuekey|textkey,值键和文本健//二维数组时才需要
        $separator  = $tag['separator']?$tag['separator']:"&nbsp;&nbsp;&nbsp;&nbsp;";			//分隔符

		$valueto = explode("|",$value);
		$parseStr="";
		if($style) $style='style="'.$style.'"';
        if($key) {
			$keyto = explode("|",$key);
			$parseStr .='<php>$i=0;foreach($'.$datakey.' as $key=>$v){';
			$parseStr .='if("'.$valueto[0].'" && in_array($v["'.$keyto[0].'"],$'.$valueto[1].'){';
			$parseStr .='</php>';
			$parseStr .='<input type="checkbox" name="'.$id.'[]" id="'.$id.'_{$i}" value="{$v[\''.$keyto[0].'\']}" checked="checked">';
			$parseStr .='<php>}else{</php>';
			$parseStr .='<input type="checkbox" name="'.$id.'[]" id="'.$id.'_{$i}" value="{$v[\''.$keyto[0].'\']}">';
			$parseStr .='<php>}</php>';
			$parseStr .='<label for="'.$id.'_{$i}">{$v[\''.$keyto[1].'\']}</label>'.$separator;
			$parseStr .='<php>$i++;}</php>';
        }else {
			$i=0;
			$parseStr .='<php>$i=0;foreach($'.$datakey.' as $key=>$v){';
			$parseStr .='if(is_array($'.$valueto[1].') && in_array($key,$'.$valueto[1].')){';
			$parseStr .='</php>';
			$parseStr .='<input type="checkbox" name="'.$id.'[]" id="'.$id.'_{$i}" value="{$key}" checked="checked">';
			$parseStr .='<php>}else{</php>';
			$parseStr .='<input type="checkbox" name="'.$id.'[]" id="'.$id.'_{$i}" value="{$key}">';
			$parseStr .='<php>}</php>';
			$parseStr .='<label for="'.$id.'_{$i}">{$v}</label>'.$separator;
			$parseStr .='<php>$i++;}</php>';
        }
		if($tip) $parseStr.='<span id="tip_'.$id.'" class="tip">'.$tip.'</span>';
        return $parseStr;
    }
/*****************************新增提现编辑信息扩展功能 添加人：fanyelei 添加时间：2012-12-02 09:10**********************/
 	public function _tixian($attr)
    {
        $tag        = $this->parseXmlAttr($attr,'user');
        $uid      	= $tag['id'];                //文字
        $uname      = $tag['uname'];                //样式名
		$parseStr="";
		$parseStr = '<a onclick="loadTixian({$'.$uid.'},\'{$'.$uname.'}\')" href="javascript:void(0);">审核</a>';
        return $parseStr;
    }
	public function _tixianing($attr)
    {
        $tag        = $this->parseXmlAttr($attr,'user');
        $uid      	= $tag['id'];                //文字
        $uname      = $tag['uname'];                //样式名
		$parseStr="";
		$parseStr = '<a onclick="loadTixianing({$'.$uid.'},\'{$'.$uname.'}\')" href="javascript:void(0);">审核</a>';
        return $parseStr;
    }

public function _tixianwait($attr)
    {
        $tag        = $this->parseXmlAttr($attr,'user');
        $uid      	= $tag['id'];                //文字
        $uname      = $tag['uname'];                //样式名
		$parseStr="";
		$parseStr = '<a onclick="loadTixianwait({$'.$uid.'},\'{$'.$uname.'}\')" href="javascript:void(0);">审核</a>';
        return $parseStr;
    }
/*****************************新增提现编辑信息扩展功能 添加人：fanyelei 添加时间：2012-12-02 09:10**********************/

	/**
     +----------------------------------------------------------
     * imageBtn标签解析
     * 格式： <html:imageBtn type="" value="" />
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $attr 标签属性
     +----------------------------------------------------------
     * @return string|void
     +----------------------------------------------------------
     */
    public function _imageBtn($attr) {
        $tag        = $this->parseXmlAttr($attr,'imageBtn');
        $name       = $tag['name'];                //名称
        $value      = $tag['value'];                //文字
        $id         = isset($tag['id'])?$tag['id']:'';                //ID
        $style      = isset($tag['style'])?$tag['style']:'';                //样式名
        $click      = isset($tag['click'])?$tag['click']:'';                //点击
        $type       = empty($tag['type'])?'button':$tag['type'];                //按钮类型

        if(!empty($name)) {
            $parseStr   = '<div class="'.$style.'" ><input type="'.$type.'" id="'.$id.'" name="'.$name.'" value="'.$value.'" onclick="'.$click.'" class="'.$name.' imgButton"></div>';
        }else {
        	$parseStr   = '<div class="'.$style.'" ><input type="'.$type.'" id="'.$id.'"  name="'.$name.'" value="'.$value.'" onclick="'.$click.'" class="button"></div>';
        }

        return $parseStr;
    }
  /**
     +----------------------------------------------------------
     * list标签解析
     * 格式： <html:grid datasource="" show="vo" />
     *
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $attr 标签属性
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    public function _grid($attr) {
        $tag        = $this->parseXmlAttr($attr,'grid');
        $id         = $tag['id'];                       //表格ID
        $datasource = $tag['datasource'];               //列表显示的数据源VoList名称
        $pk         = empty($tag['pk'])?'id':$tag['pk'];//主键名，默认为id
        $style      = $tag['style'];                    //样式名
        $name       = !empty($tag['name'])?$tag['name']:'vo';                 //Vo对象名
        $action     = !empty($tag['action'])?$tag['action']:false;                   //是否显示功能操作
        $key         =  !empty($tag['key'])?true:false;
        if(isset($tag['actionlist'])) {
            $actionlist = explode(',',trim($tag['actionlist']));    //指定功能列表
        }

        if(substr($tag['show'],0,1)=='$') {
            $show   = $this->tpl->get(substr($tag['show'],1));
        }else {
            $show   = $tag['show'];
        }
        $show       = explode(',',$show);                //列表显示字段列表

        //计算表格的列数
        $colNum     = count($show);
        if(!empty($action))     $colNum++;
        if(!empty($key))  $colNum++;

        //显示开始
		$parseStr	= "<!-- Think 系统列表组件开始 -->\n";
        $parseStr  .= '<table id="'.$id.'" class="'.$style.'" cellpadding=0 cellspacing=0 >';
        $parseStr  .= '<tr><td height="5" colspan="'.$colNum.'" class="topTd" ></td></tr>';
        $parseStr  .= '<tr class="row" >';
        //列表需要显示的字段
        $fields = array();
        foreach($show as $val) {
        	$fields[] = explode(':',$val);
        }

        if(!empty($key)) {
            $parseStr .= '<th width="12">No</th>';
        }
        foreach($fields as $field) {//显示指定的字段
            $property = explode('|',$field[0]);
            $showname = explode('|',$field[1]);
            if(isset($showname[1])) {
                $parseStr .= '<th width="'.$showname[1].'">';
            }else {
                $parseStr .= '<th>';
            }
            $parseStr .= $showname[0].'</th>';
        }
        if(!empty($action)) {//如果指定显示操作功能列
            $parseStr .= '<th >操作</th>';
        }
        $parseStr .= '</tr>';
        $parseStr .= '<volist name="'.$datasource.'" id="'.$name.'" ><tr class="row" >';	//支持鼠标移动单元行颜色变化 具体方法在js中定义

        if(!empty($key)) {
            $parseStr .= '<td>{$i}</td>';
        }
        foreach($fields as $field) {
            //显示定义的列表字段
            $parseStr   .=  '<td>';
            if(!empty($field[2])) {
                // 支持列表字段链接功能 具体方法由JS函数实现
                $href = explode('|',$field[2]);
                if(count($href)>1) {
                    //指定链接传的字段值
                    // 支持多个字段传递
                    $array = explode('^',$href[1]);
                    if(count($array)>1) {
                        foreach ($array as $a){
                            $temp[] =  '\'{$'.$name.'.'.$a.'|addslashes}\'';
                        }
                        $parseStr .= '<a href="javascript:'.$href[0].'('.implode(',',$temp).')">';
                    }else{
                        $parseStr .= '<a href="javascript:'.$href[0].'(\'{$'.$name.'.'.$href[1].'|addslashes}\')">';
                    }
                }else {
                    //如果没有指定默认传编号值
                    $parseStr .= '<a href="javascript:'.$field[2].'(\'{$'.$name.'.'.$pk.'|addslashes}\')">';
                }
            }
            if(strpos($field[0],'^')) {
                $property = explode('^',$field[0]);
                foreach ($property as $p){
                    $unit = explode('|',$p);
                    if(count($unit)>1) {
                        $parseStr .= '{$'.$name.'.'.$unit[0].'|'.$unit[1].'} ';
                    }else {
                        $parseStr .= '{$'.$name.'.'.$p.'} ';
                    }
                }
            }else{
                $property = explode('|',$field[0]);
                if(count($property)>1) {
                    $parseStr .= '{$'.$name.'.'.$property[0].'|'.$property[1].'}';
                }else {
                    $parseStr .= '{$'.$name.'.'.$field[0].'}';
                }
            }
            if(!empty($field[2])) {
                $parseStr .= '</a>';
            }
            $parseStr .= '</td>';

        }
        if(!empty($action)) {//显示功能操作
            if(!empty($actionlist[0])) {//显示指定的功能项
                $parseStr .= '<td>';
                foreach($actionlist as $val) {
					if(strpos($val,':')) {
						$a = explode(':',$val);
						if(count($a)>2) {
                            $parseStr .= '<a href="javascript:'.$a[0].'(\'{$'.$name.'.'.$a[2].'}\')">'.$a[1].'</a>&nbsp;';
						}else {
							$parseStr .= '<a href="javascript:'.$a[0].'(\'{$'.$name.'.'.$pk.'}\')">'.$a[1].'</a>&nbsp;';
						}
					}else{
						$array	=	explode('|',$val);
						if(count($array)>2) {
							$parseStr	.= ' <a href="javascript:'.$array[1].'(\'{$'.$name.'.'.$array[0].'}\')">'.$array[2].'</a>&nbsp;';
						}else{
							$parseStr .= ' {$'.$name.'.'.$val.'}&nbsp;';
						}
					}
                }
                $parseStr .= '</td>';
            }
        }
        $parseStr	.= '</tr></volist><tr><td height="5" colspan="'.$colNum.'" class="bottomTd"></td></tr></table>';
        $parseStr	.= "\n<!-- Think 系统列表组件结束 -->\n";
        return $parseStr;
    }

    /**
     +----------------------------------------------------------
     * list标签解析
     * 格式： <html:list datasource="" show="" />
     *
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $attr 标签属性
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    public function _list($attr) {
        $tag        = $this->parseXmlAttr($attr,'list');
        $id         = $tag['id'];                       //表格ID
        $datasource = $tag['datasource'];               //列表显示的数据源VoList名称
        $pk         = empty($tag['pk'])?'id':$tag['pk'];//主键名，默认为id
        $style      = $tag['style'];                    //样式名
        $name       = !empty($tag['name'])?$tag['name']:'vo';                 //Vo对象名
        $action     = $tag['action']=='true'?true:false;                   //是否显示功能操作
        $key         =  !empty($tag['key'])?true:false;
        $sort      = $tag['sort']=='false'?false:true;
        $checkbox   = $tag['checkbox'];                 //是否显示Checkbox
        if(isset($tag['actionlist'])) {
            $actionlist = explode(',',trim($tag['actionlist']));    //指定功能列表
        }

        if(substr($tag['show'],0,1)=='$') {
            $show   = $this->tpl->get(substr($tag['show'],1));
        }else {
            $show   = $tag['show'];
        }
        $show       = explode(',',$show);                //列表显示字段列表

        //计算表格的列数
        $colNum     = count($show);
        if(!empty($checkbox))   $colNum++;
        if(!empty($action))     $colNum++;
        if(!empty($key))  $colNum++;

        //显示开始
		$parseStr	= "<!-- Think 系统列表组件开始 -->\n";
        $parseStr  .= '<table id="'.$id.'" class="'.$style.'" cellpadding=0 cellspacing=0 >';
        $parseStr  .= '<tr><td height="5" colspan="'.$colNum.'" class="topTd" ></td></tr>';
        $parseStr  .= '<tr class="row" >';
        //列表需要显示的字段
        $fields = array();
        foreach($show as $val) {
        	$fields[] = explode(':',$val);
        }
        if(!empty($checkbox) && 'true'==strtolower($checkbox)) {//如果指定需要显示checkbox列
            $parseStr .='<th width="8"><input type="checkbox" id="check" onclick="CheckAll(\''.$id.'\')"></th>';
        }
        if(!empty($key)) {
            $parseStr .= '<th width="12">No</th>';
        }
        foreach($fields as $field) {//显示指定的字段
            $property = explode('|',$field[0]);
            $showname = explode('|',$field[1]);
            if(isset($showname[1])) {
                $parseStr .= '<th width="'.$showname[1].'">';
            }else {
                $parseStr .= '<th>';
            }
            $showname[2] = isset($showname[2])?$showname[2]:$showname[0];
            if($sort) {
                $parseStr .= '<a href="javascript:sortBy(\''.$property[0].'\',\'{$sort}\',\''.ACTION_NAME.'\')" title="按照'.$showname[2].'{$sortType} ">'.$showname[0].'<eq name="order" value="'.$property[0].'" ><img src="../Public/images/{$sortImg}.gif" width="12" height="17" border="0" align="absmiddle"></eq></a></th>';
            }else{
                $parseStr .= $showname[0].'</th>';
            }

        }
        if(!empty($action)) {//如果指定显示操作功能列
            $parseStr .= '<th >操作</th>';
        }

        $parseStr .= '</tr>';
        $parseStr .= '<volist name="'.$datasource.'" id="'.$name.'" ><tr class="row" ';	//支持鼠标移动单元行颜色变化 具体方法在js中定义
        if(!empty($checkbox)) {
            $parseStr .= 'onmouseover="over(event)" onmouseout="out(event)" onclick="change(event)" ';
        }
        $parseStr .= '>';
        if(!empty($checkbox)) {//如果需要显示checkbox 则在每行开头显示checkbox
            $parseStr .= '<td><input type="checkbox" name="key"	value="{$'.$name.'.'.$pk.'}"></td>';
        }
        if(!empty($key)) {
            $parseStr .= '<td>{$i}</td>';
        }
        foreach($fields as $field) {
            //显示定义的列表字段
            $parseStr   .=  '<td>';
            if(!empty($field[2])) {
                // 支持列表字段链接功能 具体方法由JS函数实现
                $href = explode('|',$field[2]);
                if(count($href)>1) {
                    //指定链接传的字段值
                    // 支持多个字段传递
                    $array = explode('^',$href[1]);
                    if(count($array)>1) {
                        foreach ($array as $a){
                            $temp[] =  '\'{$'.$name.'.'.$a.'|addslashes}\'';
                        }
                        $parseStr .= '<a href="javascript:'.$href[0].'('.implode(',',$temp).')">';
                    }else{
                        $parseStr .= '<a href="javascript:'.$href[0].'(\'{$'.$name.'.'.$href[1].'|addslashes}\')">';
                    }
                }else {
                    //如果没有指定默认传编号值
                    $parseStr .= '<a href="javascript:'.$field[2].'(\'{$'.$name.'.'.$pk.'|addslashes}\')">';
                }
            }
            if(strpos($field[0],'^')) {
                $property = explode('^',$field[0]);
                foreach ($property as $p){
                    $unit = explode('|',$p);
                    if(count($unit)>1) {
                        $parseStr .= '{$'.$name.'.'.$unit[0].'|'.$unit[1].'} ';
                    }else {
                        $parseStr .= '{$'.$name.'.'.$p.'} ';
                    }
                }
            }else{
                $property = explode('|',$field[0]);
                if(count($property)>1) {
                    $parseStr .= '{$'.$name.'.'.$property[0].'|'.$property[1].'}';
                }else {
                    $parseStr .= '{$'.$name.'.'.$field[0].'}';
                }
            }
            if(!empty($field[2])) {
                $parseStr .= '</a>';
            }
            $parseStr .= '</td>';

        }
        if(!empty($action)) {//显示功能操作
            if(!empty($actionlist[0])) {//显示指定的功能项
                $parseStr .= '<td>';
                foreach($actionlist as $val) {
					if(strpos($val,':')) {
						$a = explode(':',$val);
						if(count($a)>2) {
                            $parseStr .= '<a href="javascript:'.$a[0].'(\'{$'.$name.'.'.$a[2].'}\')">'.$a[1].'</a>&nbsp;';
						}else {
							$parseStr .= '<a href="javascript:'.$a[0].'(\'{$'.$name.'.'.$pk.'}\')">'.$a[1].'</a>&nbsp;';
						}
					}else{
						$array	=	explode('|',$val);
						if(count($array)>2) {
							$parseStr	.= ' <a href="javascript:'.$array[1].'(\'{$'.$name.'.'.$array[0].'}\')">'.$array[2].'</a>&nbsp;';
						}else{
							$parseStr .= ' {$'.$name.'.'.$val.'}&nbsp;';
						}
					}
                }
                $parseStr .= '</td>';
            }
        }
        $parseStr	.= '</tr></volist><tr><td height="5" colspan="'.$colNum.'" class="bottomTd"></td></tr></table>';
        $parseStr	.= "\n<!-- Think 系统列表组件结束 -->\n";
        return $parseStr;
    }
}
?>