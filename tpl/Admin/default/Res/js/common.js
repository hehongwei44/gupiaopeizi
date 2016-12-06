//字符串长度-中文和全角符号为1，英文、数字和半角为0.5
var getLength = function(str, shortUrl) {
	if (true == shortUrl) {
		return Math.ceil(str.replace(/((news|telnet|nttp|file|http|ftp|https):\/\/){1}(([-A-Za-z0-9]+(\.[-A-Za-z0-9]+)*(\.[-A-Za-z]{2,5}))|([0-9]{1,3}(\.[0-9]{1,3}){3}))(:[0-9]*)?(\/[-A-Za-z0-9_\$\.\+\!\*\(\),;:@&=\?\/~\#\%]*)*/ig, 'http://goo.gl/fkKB ')
							.replace(/^\s+|\s+$/ig,'').replace(/[^\x00-\xff]/ig,'xx').length/2);
	} else {
		return Math.ceil(str.replace(/^\s+|\s+$/ig,'').replace(/[^\x00-\xff]/ig,'xx').length/2);
	}
};
var subStr = function (str, len) {
    if(!str) { return ''; }
        len = len > 0 ? len*2 : 280;
    var count = 0,	//计数：中文2字节，英文1字节
        temp = '';  //临时字符串
    for (var i = 0;i < str.length;i ++) {
    	if (str.charCodeAt(i) > 255) {
        	count += 2;
        } else {
        	count ++;
        }
        //如果增加计数后长度大于限定长度，就直接返回临时字符串
        if(count > len) { return temp; }
        //将当前内容加到临时字符串
         temp += str.charAt(i);
    }
    return str;
};


ui = window.ui ||{
	success:function(message,error){
		var style = (error==1)?"html_clew_box clew_error ":"html_clew_box";
		var html   =   '<div class="" id="ui_messageBox" style="display:none">'
					   + '<div class="html_clew_box_close"><a href="javascript:void(0)" onclick="$(this).parents(\'.html_clew_box\').hide()" title="关闭">关闭</a></div>'
					   + '<div class="html_clew_box_con" id="ui_messageContent">&nbsp;</div></div>';
		var init      =  0;
		
		var showMessage = function( message ){		
			if( !init ){
				$('body').append( html );
				init = 1;
			}
			

			
			$( '#ui_messageContent' ).html( message );
			$('#ui_messageBox').attr('class',style);
			
			var v =  ui.box._viewport() ;
			
			jQuery('<div class="boxy-modal-blackout"></div>')
	        .css(jQuery.extend(ui.box._cssForOverlay(), {
	            zIndex: 99, opacity: 0.2
	        })).appendTo(document.body);
			
			
			$( '#ui_messageBox' ).css({
				left:( v.left + v.width/2  - $( '#ui_messageBox' ).outerWidth()/2 ) + "px",
				top:(  v.top  + v.height/2 - $( '#ui_messageBox' ).outerHeight()/2 ) + "px"
			});			
			
			$( '#ui_messageBox' ).fadeIn("slow");
		}
		
		
		var closeMessage = function(){
			setTimeout( function(){  
				$( '#ui_messageBox' ).fadeOut("fast",function(){
					//jQuery('.boxy-modal-blackout').remove(); 
				});
			} , 2500);
		}
		
		showMessage( message );
		closeMessage();

	},
	
	error:function(message){
		ui.success(message,1);
	},

	load:function(){
		var init = 0
		var loadingBox = '<div class="html_clew_box" id="ui_loading" style="display:none"><div class="html_clew_box_con"><span class="ico_waiting">加载中……</span></div></div>';
		if( !init ){
			$('body').append( loadingBox );
			init = 1;
		}
		
		$( '#ui_loading' ).css({
			right:100+"px",
			top:($(document).scrollTop())+"px"
		});
		$( '#ui_loading' ).fadeIn("slow");
	},
	
	loaded:function(){
		var loadingBox = '#ui_loading';
		$( loadingBox ).fadeOut("slow");
	},
	
	quicklogin:function(){
		ui.box.load( U('home/public/quick_login') ,{title:'快速登录'});
	},
	
	sendmessage:function(touid){
		touid = touid || '';
		ui.box.load(U('home/Message/post',['touid='+touid]), {title:'发私信'});
	},
	
	confirm:function(o,text){
		var callback = $(o).attr('callback');
		text = text || '确定要做此项操作吗？';
		this.html = '<div id="ts_ui_confirm" class="ts_confirm"><span class="txt"></span><br><input type="button" value="确定"  class="btn_b mr5"><input type="button" value="取消"  class="btn_w"></div>';
		// 修改原因: ts_ui_confirm .btn_b按钮会重复提交
		//if( $('#ts_ui_confirm').html()==null ){
			$('body').append(this.html);
		//}
		var position = $(o).offset();
		$('#ts_ui_confirm').css({"top":position.top+"px","left":position.left-($("#ts_ui_confirm").width()/2)+"px","display":"none"});
		$("#ts_ui_confirm .txt").html(text);
		$('#ts_ui_confirm').fadeIn("fast");
		$("#ts_ui_confirm .btn_w").one('click',function(){
			$('#ts_ui_confirm').fadeOut("fast");
			// 修改原因: ts_ui_confirm .btn_b按钮会重复提交
			$('#ts_ui_confirm').remove();
		});
		$("#ts_ui_confirm .btn_b").one('click',function(){
			$('#ts_ui_confirm').fadeOut("fast");
			// 修改原因: ts_ui_confirm .btn_b按钮会重复提交
			$('#ts_ui_confirm').remove();
			eval(callback);
		});
	},
	
	emotions:function(o){
		var $talkPop = $('div.talkPop');
		var $body = $('body');
		var $o = $(o);
		if (1 != $talkPop.data('type')) {
			$talkPop.hide();
		}
		this.emotdata = $body.data("emotdata");
		this.html = '<div class="talkPop alL" id="emotions" style="*padding-top:20px;">'
				 + '<div style="position: relative; height: 7px; line-height: 3px;">'
				 + '<img src="http://develop.thinksns.com/ts_beta_2_0/public/themes/classic/images/zw_img.gif" style="margin-left: 10px; position: absolute;" class="talkPop_arrow"></div>'
				 + '<div class="talkPop_box">'
				 + '<div class="close" style="height:20px"><a onclick=" $(\'#emotions\').remove()" class="del" href="javascript:void(0)" title="关闭"> </a><span class="pl5">点击插入表情</span></div>'
				 + '<div class="faces_box" id="emot_content"><img src="'+ _THEME_+'/images/icon_waiting.gif" width="20" class="alM"></div></div></div>';
		target_set = $o.attr('target_set');
		$body.append(this.html);
		var position = $o.offset();
		$('#emotions').css({"top":position.top+"px","left":position.left+"px","z-index":1000});
		
		var _this = this;
		if(!this.emotdata){
			$.post( U('home/user/emotions'),{target:$(this).attr('target_set')} ,function(txt){
				txt = eval('('+txt+')');
				$body.data("emotdata",txt);
				_this.showContent(txt);
			})
		}else{
			_this.showContent(this.emotdata);
		};

		this.showContent = function(data){  //显示表情内容
			var content ='';
			$.each(data,function(i,n){
				content+='<a href="javascript:void(0)" onclick="ui.emotions_c(\''+n.emotion+'\',\''+target_set+'\')"><img src="'+ _THEME_ +'/images/expression/miniblog/'+ n.filename +'" /></a>';
			});
			content+='<div class="c"></div>';
			$('#emot_content').html(content);
		};
		
		$body.live('click',function(event){
			if( $(event.target).attr('target_set')!=target_set ){
				$('#emotions').remove();
			}
		})
	},
	
	emotions_c:function(emot,target){
		$("#"+target).val( $("#"+target).val()+emot+' ' );
		
		var textArea = document.getElementById(target);
		var strlength = textArea.value.length;
		if (document.selection) { //IE
			 var rng = textArea.createTextRange();
			 rng.collapse(true);
			 rng.moveStart("character",strlength)
		}else if (textArea.selectionStart || (textArea.selectionStart == '0')) { // Mozilla/Netscape…
	        textArea.selectionStart = strlength;
	        textArea.selectionEnd = strlength;
	    }
		if(target=='content_publish'){
			weibo.checkInputLength('#content_publish',140);
		}
		$("#"+target).focus();
		$('#emotions').remove();
	},	
	
	countNew:function(){
		$.getJSON( U('home/User/countNew') ,function(txt){
			if(txt.total!="0"){
				$("#count_total").html( txt.total );
				$("#count_total_div").show();
			}
			if(txt.message!="0"){
				$("#count_message").html('('+txt.message+')');
			}
			
			if(txt.appmessage!="0"){
				$("#count_appmessage").html('('+txt.appmessage+')');
			}
			
			if(txt.notify!="0"){
				$("#count_notify").html('('+txt.notify+')');
			}
			
			if(txt.comment!="0"){
				$("#count_comment").html('('+txt.comment+')');
				$("#app_left_count_comment").html("(<font color=\"red\">"+txt.comment+"</font>)");
			}
			if(txt.atme!="0"){
				$("#count_atme").html('('+txt.atme+')');
				$("#app_left_count_atme").html("(<font color=\"red\">"+txt.atme+"</font>)");
			}
		});
	},
	
	getarea:function(prefix,init_style,init_p,init_c){
		var style = (init_style)?'class="'+init_style+'"':'';
		var html = '<select name="'+prefix+'_province" '+style+'><option>省/直辖市</option></select> <select name="'+prefix+'_city" '+style+'><option value=0>不限</option></select>';
		document.write(html);
		// _PUBLIC_+'/js/area.js'
		$.getJSON(U('home/Public/getArea'), function(json){
			json = json.provinces;
			var province ='<option>省/直辖市</option>';
			$.each(json,function(i,n){
				var pselected='';
				var cselected='';
				var city='<option>不限</option>';
				if(n.id==init_p){
					 pselected = 'selected="true"';
					 $.each(n.citys,function(j,m){
							for(var p in m){
								cselected = (p==init_c)?'selected="true"':'';
								city+='<option value="'+p+'" '+cselected+'>'+m[p]+'</option>';
							};
					 });
					 $("select[name='"+prefix+"_city']").html(city);
				}
				province+='<option value="'+n.id+'" rel="'+i+'" '+pselected+'>'+n.name+'</option>';
			});
			
			$("select[name='"+prefix+"_province']").live('change',function(){
				var city='<option>不限</option>';
				var handle =  $(this).find('option:selected').attr('rel');
				if( handle ){
					var t =  json[handle].citys;
					$.each(t,function(j,m){
						for(var p in m){
							city+='<option value='+p+'>'+m[p]+'</option>';
						};
					});
				};
				$("select[name='"+prefix+"_city']").html(city);
			});
			$("select[name='"+prefix+"_province']").html(province);
		}); 
	}

	
};

function AutoResizeImage(maxWidth,maxHeight,objImg){
	var img = new Image();
	img.src = objImg.src;
	var hRatio;
	var wRatio;
	var Ratio = 1;
	var w = img.width;
	var h = img.height;
	wRatio = maxWidth / w;
	hRatio = maxHeight / h;
	if (maxWidth ==0 && maxHeight==0){
		Ratio = 1;
	}else if (maxWidth==0){//
		if (hRatio<1) Ratio = hRatio;
	}else if (maxHeight==0){
		if (wRatio<1) Ratio = wRatio;
	}else if (wRatio<1 || hRatio<1){
		Ratio = (wRatio<=hRatio?wRatio:hRatio);
	}
	if (Ratio<1){
		w = w * Ratio;
		h = h * Ratio;
	}
	objImg.height = h;
	objImg.width = w;
}

//模拟ts U函数
function U(url,params){
	var website = _ROOT_+'/index.php';
	url = url.split('/');
	if(url[0]=='' || url[0]=='@')
		url[0] = APPNAME;
	if (!url[1])
		url[1] = 'Index';
	if (!url[2])
		url[2] = 'index';
	website = website+'?app='+url[0]+'&mod='+url[1]+'&act='+url[2];
	if(params){
		params = params.join('&');
		website = website + '&' + params;
	}
	return website;
}


/**
 * http://www.openjs.com/scripts/events/keyboard_shortcuts/
 * Version : 1.00.A
 * By Binny V A
 * 键盘绑定事件
 * License : BSD
 */
function shortcut(shortcut,callback,opt) {
	//Provide a set of default options
	var default_options = {
		'type':'keydown',
		'propagate':false,
		'target':document
	}
	if(!opt) opt = default_options;
	else {
		for(var dfo in default_options) {
			if(typeof opt[dfo] == 'undefined') opt[dfo] = default_options[dfo];
		}
	}

	var ele = opt.target
	if(typeof opt.target == 'string') ele = document.getElementById(opt.target);
	var ths = this;

	//The function to be called at keypress
	var func = function(e) {
		e = e || window.event;

		//Find Which key is pressed
		if (e.keyCode) code = e.keyCode;
		else if (e.which) code = e.which;
		var character = String.fromCharCode(code).toLowerCase();

		var keys = shortcut.toLowerCase().split("+");
		//Key Pressed - counts the number of valid keypresses - if it is same as the number of keys, the shortcut function is invoked
		var kp = 0;
		
		//Work around for stupid Shift key bug created by using lowercase - as a result the shift+num combination was broken
		var shift_nums = {
			"`":"~",
			"1":"!",
			"2":"@",
			"3":"#",
			"4":"$",
			"5":"%",
			"6":"^",
			"7":"&",
			"8":"*",
			"9":"(",
			"0":")",
			"-":"_",
			"=":"+",
			";":":",
			"'":"\"",
			",":"<",
			".":">",
			"/":"?",
			"\\":"|"
		}
		//Special Keys - and their codes
		var special_keys = {
			'esc':27,
			'escape':27,
			'tab':9,
			'space':32,
			'return':13,
			'enter':13,
			'backspace':8,

			'scrolllock':145,
			'scroll_lock':145,
			'scroll':145,
			'capslock':20,
			'caps_lock':20,
			'caps':20,
			'numlock':144,
			'num_lock':144,
			'num':144,
			
			'pause':19,
			'break':19,
			
			'insert':45,
			'home':36,
			'delete':46,
			'end':35,
			
			'pageup':33,
			'page_up':33,
			'pu':33,

			'pagedown':34,
			'page_down':34,
			'pd':34,

			'left':37,
			'up':38,
			'right':39,
			'down':40,

			'f1':112,
			'f2':113,
			'f3':114,
			'f4':115,
			'f5':116,
			'f6':117,
			'f7':118,
			'f8':119,
			'f9':120,
			'f10':121,
			'f11':122,
			'f12':123
		}


		for(var i=0; k=keys[i],i<keys.length; i++) {
			//Modifiers
			if(k == 'ctrl' || k == 'control') {
				if(e.ctrlKey) kp++;

			} else if(k ==  'shift') {
				if(e.shiftKey) kp++;

			} else if(k == 'alt') {
					if(e.altKey) kp++;

			} else if(k.length > 1) { //If it is a special key
				if(special_keys[k] == code) kp++;

			} else { //The special keys did not match
				if(character == k) kp++;
				else {
					if(shift_nums[character] && e.shiftKey) { //Stupid Shift key bug created by using lowercase
						character = shift_nums[character]; 
						if(character == k) kp++;
					}
				}
			}
		}

		if(kp == keys.length) {
			if (lock == 0) {
				lock = 1;
				setTimeout(function(){
					lock = 0;
				}, 1500);
			} else {
				return false;
			}
			callback(e);

			if(!opt['propagate']) { //Stop the event
				//e.cancelBubble is supported by IE - this will kill the bubbling process.
				e.cancelBubble = true;
				e.returnValue = false;

				//e.stopPropagation works only in Firefox.
				if (e.stopPropagation) {
					e.stopPropagation();
					e.preventDefault();
				}
				return false;
			}
		}
	}

	//Attach the function with the event
	var lock = 0;
	if(ele.addEventListener) ele.addEventListener(opt['type'], func, false);
	else if(ele.attachEvent) ele.attachEvent('on'+opt['type'], func);
	else ele['on'+opt['type']] = func;
}

// 图片缩放
function photo_resize(name,sizeNum){
	var newWidth = $(name).width();
    $(name +" img").each(function(){
        
        var width = sizeNum || 728;
        var images = $(this);
        
        //判断是否是IE
        if (-[1, ]) {
            image = new Image();
            image.src = $(this).attr('src');
            image.onload = function(){
                if (image.width >= width) {
                    images.click(function(){
                        tb_show("", this.src, false);
                    });
                    images.width(width);
                    images.height(width / image.width * image.height);
                }
            }
        }
        else {
            if (images.width() >= width) {
                images.click(function(){
                    tb_show("", this.src, false);
                });
                images.width(width);
                images.height(width / images.width() * images.height());
            }
        }

		
		//image.attr('rel','imageGroup');

    });
}

// 输入框 @用户 列表
(function(){

var config = {
		boxID:"autoTalkBox",
		valuepWrap:'autoTalkText',
		wrap:'recipientsTips',
		listWrap:"autoTipsUserList",
		position:'autoUserTipsPosition',
		positionHTML:'<span id="autoUserTipsPosition">&nbsp;123</span>',
		className:'autoSelected'
	};
//var html = '<div id="autoTalkBox" style="text-align:left;z-index:-2000;top:$top$px;left:$left$px;width:$width$px;height:$height$px;z-index:1;position:absolute;scroll-top:$SCTOP$px;overflow:hidden;overflow-y:auto;visibility:hidden;word-break:break-all;word-wrap:break-word;"><span id="autoTalkText" style="font-size:14px;margin:0;"></span></div><div id="recipientsTips" class="recipients-tips"><div style="font-size:12px;text-align:left;font-weight: bold;padding:2px;">格式：@+W3账号、姓名</div><ul id="autoTipsUserList"></ul></div>';
//var listHTML = '<li style="text-align:left"><a title="$ACCOUNT$" rel="$ID$" >$NAME$(@$SACCOUNT$)</a></li>';
var html = '<div id="autoTalkBox" style="text-align:left;z-index:-2000;top:$top$px;left:$left$px;width:$width$px;height:$height$px;z-index:1;position:absolute;scroll-top:$SCTOP$px;overflow:hidden;overflow-y:auto;visibility:hidden;word-break:break-all;word-wrap:break-word;"><span id="autoTalkText" style="font-size:14px;margin:0;"></span></div><div id="recipientsTips" class="recipients-tips"><div style="font-size:12px;text-align:left;font-weight: bold;padding:2px;">格式：@用户昵称</div><ul id="autoTipsUserList"></ul></div>';
var listHTML = '<li style="text-align:left"><a rel="$ID$" >@$SACCOUNT$</a></li>';


/*
 * D 基本DOM操作
 * $(ID)
 * DC(tn) TagName
 * EA(a,b,c,e)
 * ER(a,b,c)
 * BS()
 * FF
 */
var D = {
	$:function(ID){
		return document.getElementById(ID)
	},
	DC:function(tn){
		return document.createElement(tn);
	},
    EA:function(a, b, c, e) {
        if (a.addEventListener) {
            if (b == "mousewheel") b = "DOMMouseScroll";
            a.addEventListener(b, c, e);
            return true
        } else return a.attachEvent ? a.attachEvent("on" + b, c) : false
    },
    ER:function(a, b, c) {
        if (a.removeEventListener) {
            a.removeEventListener(b, c, false);
            return true
        } else return a.detachEvent ? a.detachEvent("on" + b, c) : false
    },
	BS:function(){
		var db=document.body,
			dd=document.documentElement,
			top = db.scrollTop+dd.scrollTop;
			left = db.scrollLeft+dd.scrollLeft;
		return { 'top':top , 'left':left };
	},
	
	FF:(function(){
		var ua=navigator.userAgent.toLowerCase();
		return /firefox\/([\d\.]+)/.test(ua);
	})()
};

/*
 * TT textarea 操作函数
 * info(t) 基本信息
 * getCursorPosition(t) 光标位置
 * setCursorPosition(t, p) 设置光标位置
 * add(t,txt) 添加内容到光标处
 */
var TT = {
	
	info:function(t){
		var o = t.getBoundingClientRect();
		var w = t.offsetWidth;
		var h = t.offsetHeight;
		var s = t.style;
		return {top:o.top, left:o.left, width:w, height:h , style:s};
	},
	
	getCursorPosition: function(t){
		if (document.selection) {
			t.focus();
			var ds = document.selection;
			var range = null;
			range = ds.createRange();
			var stored_range = range.duplicate();
			stored_range.moveToElementText(t);
			stored_range.setEndPoint("EndToEnd", range);
			t.selectionStart = stored_range.text.length - range.text.length;
			t.selectionEnd = t.selectionStart + range.text.length;
			return t.selectionStart;
		} else return t.selectionStart
	},
	
	setCursorPosition:function(t, p){
		var n = p == 'end' ? t.value.length : p;
		if(document.selection){
			var range = t.createTextRange();
			range.moveEnd('character', -t.value.length);         
			range.moveEnd('character', n);
			range.moveStart('character', n);
			range.select();
		}else{
			t.setSelectionRange(n,n);
			t.focus();
		}
	},
	
	add:function (t, txt){
		var val = t.value;
		var wrap = wrap || '' ;
		if(document.selection){
			document.selection.createRange().text = txt;  
		} else {
			var cp = t.selectionStart;
			var ubbLength = t.value.length;
			t.value = t.value.slice(0,t.selectionStart) + txt + t.value.slice(t.selectionStart, ubbLength);
			this.setCursorPosition(t, cp + txt.length); 
		};
	},
	
	del:function(t, n){
		var p = this.getCursorPosition(t);
		var s = t.scrollTop;
		t.value = t.value.slice(0,p - n) + t.value.slice(p);
		this.setCursorPosition(t ,p - n);
		D.FF && setTimeout(function(){t.scrollTop = s},10);
		
	}

}


/*
 * DS 数据查找
 * inquiry(data, str, num) 数据, 关键词, 个数
 * 
 */

var DS = {
	inquiry:function(data, str, num){
		//if(str == '') return friendsData.slice(0, num);

		var reg = new RegExp(str, 'i');
		var i = 0;
		//var dataUserName = {};
		var sd = [];

		while(sd.length < num && i < data.length){
			if(reg.test(data[i]['user'])){
				sd.push(data[i]);
				//dataUserName[data[i]['user']] = true;
			}
			i++;
		}			
		return sd;
	}
}


/*
 * selectList
 * _this
 * index
 * list
 * selectIndex(code) code : e.keyCode
 * setSelected(ind) ind:Number
 */
var selectList = {
	_this:null,
	index:-1,
	list:null,
	selectIndex:function(code){
		if(D.$(config.wrap).style.display == 'none') return true;
		var i = selectList.index;
		switch(code){
		   case 40:
			 i = i + 1;
			 break
		   case 38:
			 i = i - 1;
			 break
		   case 13:
			return selectList._this.enter();
			break
		   case 32:
			return selectList._this.enter();
			break
		}

		i = i >= selectList.list.length ? 0 : i < 0 ? selectList.list.length-1 : i;
		return selectList.setSelected(i);
	},
	setSelected:function(ind){
		if(selectList.index >= 0) selectList.list[selectList.index].className = '';
		selectList.list[ind].className = config.className;
		selectList.index = ind;
		return false;
	}

}

var AutoTips = function(A){
	var elem = A.id ? D.$(A.id) : A.elem;
	var checkLength = 10;
	var _this = {};
	var key = '';

	_this.start = function(){
		if(!D.$(config.boxID)){
			var h = html.slice();
			var info = TT.info(elem);
			var div = D.DC('DIV');
			var bs = D.BS();
			h = h.replace('$top$',(info.top + bs.top)).
					replace('$left$',(info.left + bs.left)).
					replace('$width$',info.width).
					replace('$height$',info.height).
					replace('$SCTOP$','0');
			div.innerHTML = h;
			document.body.appendChild(div);
		}else{
			_this.updatePosstion();
		}
	}
	
  	_this.keyupFn = function(e){
		var e = e || window.event;
		var code = e.keyCode;
		if(code == 38 || code == 40 || code == 13) {
			if(code==13 && D.$(config.wrap).style.display != 'none'){
				//_this.enter();
			}
			return false;
		}
		var cp = TT.getCursorPosition(elem);
		if(!cp) return _this.hide();
		var valuep = elem.value.slice(0, cp);
		var val = valuep.slice(-checkLength);
		var chars = val.match(/(\w+)?@(\S+)$|@$/);
		if(chars == null) return _this.hide();
		var char = chars[2] ? chars[2] : '';
		D.$(config.valuepWrap).innerHTML = valuep.slice(0,valuep.length - char.length).replace(/\n/g,'<br/>').
											replace(/\s/g,'&nbsp;') + config.positionHTML;
		_this.showList(char);
	}

	_this.showList = function(char){
		key = char;
		if( key.length<1 ){
			return;
		}

		$.get(A.url,{key:key},function(txt){
			if( txt=='' ){_this.hide();return;}
			var data = eval("(" + txt + ")");
			//var data = DS.inquiry(txt, char, 5);
			var html = listHTML.slice();
			var h = '';
			var len = data.length;
			if(len == 0){_this.hide();return;}
			var reg = new RegExp(char);
			var em = '<em>'+ char +'</em>';
			for(var i=0; i<len; i++){
				var hm = data[i]['uname'].replace(reg,em);
				h += html.replace('$SACCOUNT$',hm).replace('$ID$',data[i]['uname']);
				/*h += html.replace(/\$ACCOUNT\$|\$NAME\$/g,data[i]['fullname']).
							replace('$SACCOUNT$',hm).replace('$ID$',data[i]['uname']);*/
			}
			
			_this.updatePosstion();
			var p = D.$(config.position).getBoundingClientRect();
			var bs = D.BS();
			var d = D.$(config.wrap).style;
			d.top = p.top + 20 + bs.top + 'px';
			d.left = p.left - 5 + 'px';
			D.$(config.listWrap).innerHTML = h;
			_this.show();
		});
	}
	
	
	_this.KeyDown = function(e){
		var e = e || window.event;
		var code = e.keyCode;
		if(code == 38 || code == 40 || code == 13 || code==32){
			return selectList.selectIndex(code);
		}
		return true;
	}
	
	_this.updatePosstion = function(){
		var p = TT.info(elem);
		
		var bs = D.BS();
		var d = D.$(config.boxID).style;
		d.top = p.top + bs.top +'px';
		d.left = p.left + bs.left + 'px';
		d.width = p.width+'px';
		d.height = p.height+'px';
		D.$(config.boxID).scrollTop = elem.scrollTop;
	}

	_this.show = function(){
		selectList.list = D.$(config.listWrap).getElementsByTagName('li');
		selectList.index = -1;
		selectList._this = _this;
		_this.cursorSelect(selectList.list);
		elem.onkeydown = _this.KeyDown;
		selectList.setSelected(0);
		//D.$(config.wrap).style.display = 'block';
		$("#"+config.wrap).fadeIn("fast");
	}

	_this.cursorSelect = function(list){
		for(var i=0; i<list.length; i++){
			list[i].onmouseover = (function(i){
				return function(){selectList.setSelected(i)};
			})(i);
			list[i].onclick = _this.enter;
			//D.EA(list[i], 'click', function(){alert(1)}, false);
		}
	}

	_this.hide = function(){
		selectList.list = null;
		selectList.index = -1;
		selectList._this = null;
		D.ER(elem, 'keydown', _this.KeyDown);
		//D.$(config.wrap).style.display = 'none';
		$("#"+config.wrap).fadeOut("fast");
	}
	
	_this.bind = function(){

		elem.onkeyup = _this.keyupFn;
		elem.onclick = _this.keyupFn;
		if (navigator.userAgent.indexOf("MSIE") == -1){
        	elem.oninput = _this.keyupFn;
        }

		$('body').live('click',function(){
			setTimeout(_this.hide, 100)
		});
		//elem.onblur = function(){setTimeout(_this.hide, 100)}
	}
	
	_this.enter = function(){
		TT.del(elem, key.length, key);
		TT.add(elem, selectList.list[selectList.index].getElementsByTagName('A')[0].rel+' ');
		_this.hide();
		return false;
	}

	return _this;
	
}

window.userAutoTips = function(args){
		var a = AutoTips(args);
			a.start();
			a.bind();
	}
})()