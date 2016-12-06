$.fn.extend({
	//判断对象是否可见
	visible:function(){return this.is(':visible');},
	//替换CSS
	cssR:function(c1,c2){this.removeClass(c1);this.addClass(c2);},
	//在对象内部存入数据
	htm:function(s){if(this.length==0)return false;if($.no(s))return this[0].innerHTML;for(var i=0;i<this.length;i++)this[i].innerHTML=s;return true;},
	//焦点延迟处理
	f:function(){var t=this,f=function(){t.focus()};setTimeout(f,100);}
});

$.extend({
	_:function(s){if($.no(s))s=$.t();document.title=s;},
	//获取对象(不带缓存)
	$:function(o){return $.isS(o)?$('#'+o):$(o);},
	//获取对象(带缓存)
	o:function(o){if(!$.isS(o))return $(o);var obj=$.os[o];if(obj)return obj;obj=$('#'+o);if(obj.length>0)$.os[o]=obj;return obj;},os:{},
	//获取对象值
	v:function(o,v){if(v)$.$(o).val(v);else{return $.trim($.$(o).val());}},
	//返回当前时间
	t:function(){return $.d().getTime();},
	//返回当前时间对象
	d:function(t){if(t)return new Date(t);return new Date()},
	//转换成整数
	n:function(s){return parseInt(s);},
	//转换成浮点数
	f:function(s){return parseFloat(s);},
	//判断对象是否存在
	no:function(){var as=arguments;for(var i=0;i<as.length;i++)if(as[i]==null || as[i]==undefined)return true;return false;},
	//判断对象类型
	isS:function(o){return typeof o=="string"},
	isN:function(o){return typeof o=="number"},
	isB:function(o){return typeof o=="boolean"},
	isO:function(o){return typeof o=="object"},
	isInt:function(o){return $.isN(o)&&Math.round(o)==o},
	//是否包含指定内容
	cc:function(cs,c,n){var e=!$.no(n);for(var i=0;i<cs.length;i++)if((e && cs[i][n]==c) || (!e && cs[i]==c))return i;return -1;},
	//返回RegExp
	re:function(s,c){var r=new RegExp(s);if(c)return r.test(c);return r;},
	//是否IE浏览器
	ie:function(v){if(!$.browser.msie)return false;if(v)return $.browser.version==v || $.browser.version.indexOf(v+'.')==0;return true;},
	//创建DOC对象
	ceok:false,
	ce:function(n){return $(document.createElement(n));},
	winPos:function(timestamp){return window.location.href+(timestamp?+"?t="+new Date().valueOf():"")},
	//从字符串中获取第一个数值
	nv:function(s,sv){var si=-1,ei=-1,i=0;if(sv){i=s.indexOf(sv);if(i<0)i=0;}for(;i<s.length;i++)if(si==-1){if(s.charAt(i)>='0' && s.charAt(i)<='9')si=i;}else{if(s.charAt(i)<'0' || s.charAt(i)>'9'){ei=i;break;}}return $.n(si==-1 && ei==-1 ? -1 : (ei==-1 ? s.substr(si) : s.substring(si,ei)));},
	//数值四舍五入
	round:function(n,mantissa){if(!mantissa)mantissa=0;if(mantissa<=0)return Math.round(n);var v=1;for(var i=0;i<mantissa;i++)v*=10;return Math.round(n*v)/v;},
	//金额格式化
	formatMoney : function(num,n) {
	    num = String(num.toFixed(n?n:2));
	    var re = /(-?\d+)(\d{3})/;
	    while(re.test(num)) num = num.replace(re,"$1,$2")
	    return n?num:num.replace(/^([0-9,]+\.[1-9])0$/,"$1").replace(/^([0-9,]+)\.00$/,"$1");;
	},
	//字符串替换
	replace:function(s,s1,s2){return s.replace(new RegExp(s1,'g'),s2);},
	//字符串长度(中文算2个)
	strlen:function(s){return s.replace(/[^\x00-\xff]/g,"**").length},
	//字符串是否包含中文
	strch:function(s){return /[^\x00-\xff]+/.test(s)},
	//清除字符串中的'"字符和头尾空格
	clear:function(){var as=arguments,s;if(as.length<1)return '';s=as[0];if(as.length<2)as=[s,"'",'"'];for(var i=1;i<as.length;i++)s=$.replace(s,as[i],'');return $.trim(s);},
	//cookie操作
	getCookie:function(name,dv){var d=document.cookie;var il1=d.indexOf(name+'=');if(il1==-1)return $.no(dv) ? null : dv;il1+=name.length+1;var il2=d.indexOf(';',il1);if(il2==-1)il2=d.length;return decodeURI(d.substring(il1,il2));},
	setCookie:function(name,value,expires,path,domain,secure){var s=new Text()._(name)._('=')._(encodeURI(value));if(!expires || (expires && expires!='temp')){var day=60*60*24*1000;if(expires=='day')expires=$.d($.t()+day);else if(expires=='week')expires=$.d($.t()+day*7);else if(expires=='month')expires=$.d($.t()+day*30);else if(expires=='year')expires=$.d($.t()+day*365);else{expires=$.d($.t()+day*365*100);}s._(';expires=')._(expires.toGMTString());}if(path)s._(';path=')._(path);if(domain)s._(';domain=')._(domain);if(secure)s._(';secure=')._(secure);document.cookie=s;},
	delCookie:function(name,path,domain){var s=new Text()._(name)._('=null;expires=')._($.d($.t()-100000000).toGMTString());if(path)s._(';path=')._(path);if(domain!=null)s._(';domain=')._(domain);document.cookie=s;},
	clrCookie:function(path,domain){var ds=document.cookie.split(';');for(var i=0;i<ds.length;i++)$.delCookie($.trim(ds[i].split('=')[0]),path,domain);},
	//获取Flash对象
	getFlash:function(name){if($.ie())return window[name];else if($.browser.mozilla)return document[name+'-1'];else{var fl=window[name+'-1'];if(!fl)fl=window[name];if(!fl)fl=document[name+'-1'];return fl;}},
	//初始化对象
	init:function(o,dv){if(!o)return dv;for(i in dv)if($.no(o[i]))o[i]=dv[i];return o;},
	stringify  : function stringify(obj) {
        var t = typeof (obj);
        if (t != "object" || obj === null) {
            // simple data type
            if (t == "string") obj = '"' + obj + '"';
            return String(obj);
        } else {
            // recurse array or object
            var n, v, json = [], arr = (obj && obj.constructor == Array);
 
            for (n in obj) {
                v = obj[n];
                t = typeof(v);
                if (obj.hasOwnProperty(n)) {
                    if (t == "string") v = '"' + v + '"'; else if (t == "object" && v !== null) v = jQuery.stringify(v);
                    json.push((arr ? "" : '"' + n + '":') + String(v));
                }
            }
            return (arr ? "[" : "{") + String(json) + (arr ? "]" : "}");
        }
    }
});
function Text(){this.s;this.b=[];};Text.prototype={
	_:function(s){var t=this;t.b.push(s);t.s=null;return t;},
	clear:function(){this.b=[];this.s=null;},
	length:function(){return this.ts().length;},
	toHtml:function(o){o=$.$(o);if(o.length==0)return;o[0].innerHTML=this.ts();},
	toString:function(){var t=this;if(!t.s)t.s=t.b.join('');return t.s;},
	ts:function(){return this.toString();}
};
var X={
	//收藏网站
	fav:function(){var u='http://www.yinuowang.com',n='一诺网';try{external.addFavorite(u,n);}catch(e){try{sidebar.addPanel(n,u,'');}catch(e){X.dialog.alert("请使用 Ctrl+D 收藏本站",1);}}},
	//返回当前可用z-index值
	zi:function(){return X._zi++;},_zi:10001,
	//ajax标准请求
	ajax:function(url,data,suc){var s={url:url,cache:false,success:suc?suc:X.as,error:X.ae};if(data){s.type='POST';s.data=data;}$.ajax(s);},
	//ajax请求成功处理
	as:function(d){d=$.trim(d),t=d.charAt(0);if(t==':'){X.dialog.alert(d.substr(1),1);return true;}if(t=='*'){eval(d.substr(1));return true;}return false;},
	//ajax请求错误处理
	aec:0,
	ae:function(){setTimeout(function(){X.dialog.alert('请求发生异常，请重试或稍后再试',3);$("input[type='button']:disabled").each(function(){X.form.enableBtn($(this));});},500);},
	ae_:function(r,s,t){$._('['+(++X.aec)+']请求发生异常['+s+'/'+t+']');},
	//返回页面空间
	pso:null,
	ps:function(){if(X.pso)return X.pso;X.wdb();X.pso={width:X.win.width(),height:X.win.height(),left:X.doc.scrollLeft(),top:X.doc.scrollTop()};return X.pso;},
	//设置内容高度
	h:function(obj,oy,min,max,auto){var o=$.$(obj),h=X.ps().height+(oy?oy:0)-37-15-15,ch;if(min||max){if(min&&h<min)h=min;else if(max&&h>max)h=max;o.css('height',h+'px');}else{ch=$(o.children()[0]).height();o.css('height',ch<h?(h+'px'):'auto');}if(!auto)X.hs.push([o,oy,min,max]);},hs:[],
	hr:function(){for(var i=0;i<X.hs.length;i++)X.h(X.hs[i][0],X.hs[i][1],X.hs[i][2],X.hs[i][3],true);},
	//初始化WinDocBody
	win:null,doc:null,body:null,
	wdb:function(){if(!X.win){X.win=$(window);X.doc=$(document);X.body=$(document.body);}},
	//回到顶部
	topo:null,
	top:function(init){
		var p=X.ps(),t=p.top,o=X.topo,a,b,l;
		if(t<300){
			if(o)o.hide();
		}else{
			if(!o){
				if(!$.ceok)return;
				X.topo=o=$.ce('div');
				o.addClass('top');
				o.attr('title','回到顶部');
				o.on('click',function(e){X.doc.scrollTop(0)});
				a=true;
			}
			if(a||init){
				b=$.o('helpCenter');
				if(b.length>0)
				{
					l=b.offset().left+b.width()+2;
					if(l+40+2>p.width){
						l=p.width-40-2;
					}
					o.css('left',l+'px');
					o.css('right','auto');
				}
			}
			if(a)X.body.append(o);else{o.show();}
		}
	},
	init:function(){
		//初始化基本事件
		X.wdb();
		//X.dialog.confirm('提醒：接受续约后无法再取消','',{title:'您确定要接受续约吗',width:260});
		X.win.bind({
			resize:function(e){X.pso=null;X.hr();X.dialog.resize();X.top(true);},
			scroll:function(e){X.pso=null;X.top();}
		});
	}
};

X.domain = ".yztz.com";

X.code = {
		argument		:	"301",
		unauthorized	:	"405",
		nameUnsettled	:	"407",
		notifyInterval	:	"601",
		schemeError		:	"700",
		balanceShortage	:	"800",
		rebate			:	"801",
		system			:	"900"
		
};
X.guide={
	show:function(){
		if($.getCookie('PROMPT','').indexOf('[guide-jyzh-tip]')>=0)return false;
		if($('#guide-mask').length==0)
			$("body").prepend('<div id="guide-mask"></div>');
		return true;
	},
	showStep1:function(){
		var a=$("a[d='opmenu']:contains('交易账户')").eq(0);
		if(a.length==0)return;
		if(!X.guide.show())return;
		$("#guide-mod").remove();
		$(".guide-outer").removeClass("guide-outer");
		a.addClass('guide-outer').click(function(){a.siblings('.opmenu').hide(); X.guide.showStep2();});
		$('<div id="guide-mod" class="guide-step1"><a href="#" onclick="X.guide.close(); return false;" class="btn-esc"></a></div>').appendTo("body").css({
			top:a.offset().top + 30,
			left:a.offset().left - 290
		});
	},
	showStep2:function(){
		var a=$("a[d='opmenu']:contains('交易账户')").eq(0).parents('.table_detail').siblings('.jyzh');
		if(a.length==0)return;
		if(!X.guide.show())return;
		a.slideDown();
		//a.show();
		$("#guide-mod").remove();
		$(".guide-outer").removeClass("guide-outer");
		a.addClass('guide-outer').click(function(){X.guide.close();});
		var st = a.attr('st');
		$('<div id="guide-mod" class="guide-step2"><a href="#" onclick="X.guide.close(); return false;" class="btn-esc"></a></div>').appendTo("body").css({
			top:a.offset().top + (st=='101'?0:(st=='102'?20:40)),
			left:a.offset().left + 12
		});
	},
	close:function(){
		var v = $.getCookie('PROMPT','');
		if(v.indexOf('[guide-jyzh-tip]')<0){
			$.setCookie('PROMPT',v+'[guide-jyzh-tip]');
		}
		$("#guide-mod").remove();
		$(".guide-outer").removeClass("guide-outer");
		$("#guide-mask").remove();
	}
};

X.kf={
	init:function(){
		var o=$.ce('div');
		o.addClass('kf');
		o.attr('title','客服');
		o.click(function(){
			X.kf.showDialog();
		});
		X.body.append(o);
		o.show();
	},
	showDialog:function(){
		window.open('http://wpa.b.qq.com/cgi/wpa.php?ln=1&key=XzkzODAxNTUxOV8xNDA5NzBfNDAwODgyOTg2Nl8yXw','在线客服','height=405,width=500,top=200,left=200,toolbar=no,menubar=no,scrollbars=yes, resizable=no,location=no, status=no');
	}
};
X.marquee={
	tim:3000,timer:"",m:null,
	init:function(obj,ps){
		var t=this,o=$.$(obj);
		t.m=o;
		o.hover(function () {
			clearTimeout(t.timer);
		}, function () {
			t.timer = setTimeout(t.start, t.tim);
		});
		setTimeout(t.start, t.tim);
		//t.start();
	},
	start:function(){
		clearTimeout(X.marquee.timer);
		var ul = X.marquee.m.find("ul:first");
		var d = ul.find("li:first").outerHeight();
		ul.animate({
			marginTop: -d
		}, 500, function () {
			ul.find("li:first").appendTo(ul);
			ul.css({
				marginTop: 0
			})
		});
		X.marquee.timer = setTimeout(X.marquee.start, X.marquee.tim);
	},
	stop:function(){
		var t=this;
		clearTimeout(t.timeout);
	}
};

X.slideUP={
		tim:3000,timer:500,timeout:"",currIndex:0,slider:[],len:0,
		init:function(obj,ps){
			var t=this,o=$.$(obj);
			t.slider=o.find("ul"),t.len=t.slider.length;
			o.hover(function () {
				clearTimeout(t.timeout);
			}, function () {
				if (t.len > 1) {
					t.timeout = setTimeout(t.start, t.tim);
				}
			});
			setTimeout(t.start, t.tim);
			//t.start();
		},
		start:function(){
			clearTimeout(X.slideUP.timeout);
			var nextIndex = X.slideUP.currIndex+1;
			if (nextIndex >= X.slideUP.len) {
	            nextIndex = 0;
	        };
			X.slideUP.slider.eq(X.slideUP.currIndex).animate({top:'-60px'},X.slideUP.timer);
			X.slideUP.slider.eq(nextIndex).css("top","60px").animate({top:'0px'},X.slideUP.timer);
			X.slideUP.currIndex++;
			if (X.slideUP.currIndex >= X.slideUP.len) {
	            X.slideUP.currIndex = 0;
	        };
			X.slideUP.timeout = setTimeout(X.slideUP.start, X.slideUP.tim);
		},
		stop:function(){
			var t=this;
			clearTimeout(t.timeout);
		}
	};

	X.slidebox={
		timer:null,
		ee:0,
		init:function(s){
			var ts = this;
			var b = $(s.box);
			var r = $(s.menu);
			var g = $(s.con);
			var o = g.length - 1;
			var q = g.width();
			b.hover(function () {
				clearInterval(ts.timer);
	            ts.showArrow("in")
	        }, function () {
				ts.setSlideTime(s.stay);
	            ts.showArrow("out")
	        });
			
			r.click(function () {
				var u = $(this).attr("data-type");
				var t = u == "right";
				var e = ts.ee;
				if (t && e == o) {
					g.eq(e).stop(true, false).animate({
						left: -q
					}, s.s, s.effect || "");
					ts.ee = 0;
					g.eq(ts.ee).css("left", q).stop(true, false).animate({
						left: 0
					}, s.s, s.effect || "")
				} else {
					if (!t && e == 0) {
						g.eq(e).stop(true, false).animate({
							left: q
						}, s.s, s.effect || "");
						ts.ee = o;
						g.eq(ts.ee).css("left", -q).stop(true, false).animate({
							left: 0
						}, s.s, s.effect || "")
					} else {
						g.eq(e).stop(true, false).animate({
							left: t ? -q : q
						}, s.s, s.effect || "");
						g.eq(e + (t ? 1 : -1)).css("left", t ? q : -q).stop(true, false).animate({
							left: 0
						}, s.s, s.effect || "");
						t ? ts.ee++ : ts.ee--
					}
				}
			})
			ts.setSlideTime(s.stay);
		},
		setSlideTime: function (g) {
			this.timer = setInterval(function () {
				$("#slide_right").click()
			}, g)
		},
		showArrow: function (o) {
			var g = o == "in" ? 0 : -32;
			var q = o == "in" ? 0 : 200;
			setTimeout(function () {
				$("#slide_left").stop(true, false).animate({
					left: g
				}, 200);
				$("#slide_right").stop(true, false).animate({
					right: g
				}, 200)
			}, q)
		}
	};

	X.slide={
		stay:6,fade:0.7,msec:1000,timer:400,timeout:"",currIndex:0,slider:[],slideindex:true,subslider:[],len:0,
		init:function(obj,ps){
			var t=this,o=$.$(obj),s=new Text();
			t.slider=o.find("li"),t.len=t.slider.length;
			if(t.slideindex){
				s._('<div id="slide-list" class="slide-list"><ul>');
				for(var i=1; i<=t.len; i++){
					s._('<li '+(i == 1 ? 'class="curr"' : '')+'>'+i+'</li>');
				}
				s._('</div></ul>');
			}
			o.after(s.ts());
			t.subslider=$('.slide-list').find('li');
			t.subslider.first().addClass('curr');
			t.subslider.bind('click', function () {
				t.currIndex = t.subslider.index($(this));
				clearTimeout(t.timeout);
				t.changePlay(t.currIndex);
				
			});
			o.hover(function () {
				clearTimeout(t.timeout)
			}, function () {
				if (t.len > 1) {
					t.timeout = setTimeout(t.autoPlay, t.stay * t.msec)
				}
			});
			if (t.len > 1) {
				t.autoPlay();
			}
		},
		changePlay:function(index){
			var t=this;
	        t.slider.eq(index).fadeIn('slow').siblings().fadeOut('slow');
	        t.subslider.eq(index).addClass('curr').siblings().removeClass('curr');
		},
		autoPlay:function () {
	        clearTimeout(X.slide.timeout);
	        X.slide.changePlay(X.slide.currIndex);
			X.slide.currIndex++;
	        if (X.slide.currIndex >= X.slide.len) {
	            X.slide.currIndex = 0;
	        };
	        X.slide.timeout = setTimeout(X.slide.autoPlay, X.slide.stay * X.slide.msec)
	    }
	};

///表单处理
X.form={
		//表单控件是否有处在焦点
		focus:false,
		//带提示输入框
		inputR:function(obj,remind,dc,rc,ffs){
			var o=$.$(obj),dfs;
			if(!dc)dc='#333';
			if(!rc)rc='#666';
			if(ffs)dfs=o.css('fontSize');
			o[0].r=remind;
			if($.v(o)=='')
			{
				o.css('color',rc);
				o.val(remind);
			}
			o.bind({
				focus:function(e){X.form.focus=true;if($.clear(o.val())==o[0].r)o.val('');o.css('color',dc);if(ffs)o.css('fontSize',ffs);},
				blur:function(e){X.form.focus=false;if($.clear(o.val()).length==0){o.val(o[0].r);o.css('color',rc);if(dfs)o.css('fontSize',dfs);}}
			});
		},
		//密码框
		inputPw:function(obj,retype){
			var o=$.$(obj);
			o.bind({
				focus:function(e){X.form.focus=true;if($.clear(o.val()).length==0)o.cssR('ui-input-pwd'+retype,'ui-input');},
				blur:function(e){X.form.focus=false;if($.clear(o.val()).length==0)o.cssR('ui-input','ui-input-pwd'+retype);}
			});
		},
		//单选框
		radio:function(save){
			var so=$.$(save),os=[],as=arguments,cs;
			for(var i=1;i<as.length;i++)
			{
				os[i-1]=$.$(as[i]);
				if(!cs)cs=os[i-1].hasClass('rab')||os[i-1].hasClass('rab-c')?['rab','rab-c']:['rab-14','rab-14-c'];
				os[i-1].on('click',function(e){for(var i=0;i<os.length;i++)if(os[i].length>0 && os[i][0]==this){so.val(os[i].attr('value'));os[i].cssR(cs[0],cs[1]);}else{os[i].cssR(cs[1],cs[0]);}});
			}
		},
		//复选框
		check:function(save,obj){
			var so=$.$(save),o=$.$(obj);
			o.on('click',function(e){if(so.val()==''){so.val(o.attr('value'));o.cssR('ui-chbox','ui-chbox-1');}else{so.val('');o.cssR('ui-chbox-1','ui-chbox');}});
		},
		tipInp:function(inp,tinp){
			inp = $.$(inp),tinp = $.$(tinp);
			if($.no(inp) || $.no(tinp))return;
			inp.bind({
				focus:function(){
					tinp.hide();
				},
				blur:function(){
					if(inp.attr('type')=='password'){
						if(inp.val()==""){
							tinp.show();
						}else{
							tinp.hide();}
					}else{
						if($.clear($.v(inp))==""){tinp.show();}else{tinp.hide();}
					}
					
				}
			});
			tinp.click(function(){
				inp.focus();
			});
		},
		disableBtn:function(btn,ps){
			var btn=$.$(btn);
			if(!btn)return;
			var dtxt=btn.attr('dis-text'),dcls=btn.attr('class');
			if(/btn\-\w+\-disable/.test($.trim(dcls))){
				return;
			}
			btn.data('ftext',btn.val());
			btn.data('cls',dcls);
			btn.attr({'class':dcls.replace(/(btn\-\w+)(?:\-\w+)*/,"$1-disable")});
			btn.val($.no(dtxt)?(ps&&ps.dtext?ps.dtext:'处理中...'):dtxt);
			btn.attr('disabled',true);
		},
		enableBtn:function(btn){
			var btn=$.$(btn);
			if(!btn)return;
			btn.attr('disabled',false);
			if(''!=$.trim(btn.data('cls'))){
				btn.attr({'class':btn.data('cls')});
			}
			if(''!=$.trim(btn.data('ftext'))){
				btn.val(btn.data('ftext')).removeData(['ftext','cls']);
			}
		},
		timeoutBtn:function(btn,msg,sec,ps){
			var btn = $.$(btn);
			if(btn&&btn.length>0){
				if(btn&&''==$.trim(btn.data('ftext'))){
					var dcls = btn.attr('class');
					btn.data("ftext",ps&&ps.ftext?ps.ftext:btn.val());
					btn.data('cls',dcls).attr("class",dcls.replace(/(btn\-\w+)(?:\-\w+)*/,"$1-disable"));
				}
				if(sec>0){
					var interval = ps&&ps.interval?ps.interval:1000;
					btn.val($.replace(msg,'{time}',sec--)).attr('disabled',true);
					var result = setTimeout("X.form.timeoutBtn('"+btn.attr("id")+"','"+msg+"',"+sec+",{interval:"+interval+"});",interval);
					btn.data("timeout",result);
				}else{
					btn.val(btn.data('ftext')).attr('disabled',false).attr("class",btn.data('cls'));
					btn.removeData("cls");
					btn.removeData("ftext");
				}
			}
		},
		uploadBtn:function(btn,interval,msg,ps){
			var btn = $.$(btn);
			if(btn&&btn.length>0){
				if(btn&&''==$.trim(btn.data('ftext'))){
					var dcls = btn.attr('class')
					btn.data("ftext",ps&&ps.ftext?ps.ftext:btn.val())
					.data('cls',dcls)
					.attr({'class':dcls.replace(/(btn\-\w+)(?:\-\w+)*/,"$1-disable")});
				}
				X.ajax(ctx.home+"/status/uploadProgress.html", {t:new Date().getTime()}, function(data){
					data = $.parseJSON(data);
					if(!$.isEmptyObject(data)){
						btn.val($.replace(msg,'{progress}',data.progress)).attr('disabled',true);
					}
					var result = setTimeout("X.form.uploadBtn('"+btn.attr("id")+"',"+interval+",'"+msg+"');",parseInt(interval,10));
					btn.data("timeout",result);
				});
			}
		},
		timeoutToggleBtn:function(btn,msgs,interval){
			var btn = $.$(btn);
			if(btn&&btn.length>0){
				if(btn&&''==$.trim(btn.data('ftext'))){
					btn.data("ftext",btn.val());
					btn.data("itext",0);
					var cls = btn.attr('class');
					btn.data('cls',cls).attr({'class':cls.replace(/(btn\-\w+)(?:\-\w+)*/,"$1-disable")});
					
				}
				if(interval>0){
					if(!$.isArray(msgs)){
						msgs = $.parseJSON(msgs);
					}
					var itext = btn.data("itext");
					btn.val(msgs[itext]).attr('disabled',true);
					var result = setTimeout("X.form.timeoutToggleBtn('"+btn.attr("id")+"','"+$.stringify(msgs)+"',"+interval+")",interval);
					btn.data("timeout",result);
					btn.data("itext",(itext+1)%msgs.length);
				}
			}
		},
		timeoutBtnBreak:function(btn){
			var btn = $.$(btn);
			if(btn&&btn.length>0){
				if(btn.data("timeout")){
					clearTimeout(btn.data("timeout"));
					if(btn.data("ftext")){
						X.form.enableBtn(btn);
					}
				}
			}
		}
	};

//对话框
X.dialog={
		//打开的对话框、提示框、选择框、消息框、加载框索引
		dbs:[],pbs:[],sel:null,mbs:[],ls:[],
		//打开对话框 ps:topic(对话框主题名称),width,notify
		//标准内容间距，控件上下20/左右30，文字四周30，带topic的上15
		open:function(content,ps){
			ps=$.init(ps,{topic:'',width:400,notify:null});
			var t=this,w=ps.width,bgi=X.zi(),di=X.zi(),db=[di,w],p=X.ps(),ww=p.width,wh=p.height,w1=w-14-25,w2=w-15,dl=$.round((ww-w)/2),dt=-30,s=new Text();
			if(dl<6)dl=6;
			s._('<div class="db-bg-in">');
			if(ps.topic){
				s._('<div class="db-t"><div class="db-close" onclick="X.dialog.close(')._(di)._(')";></div>')._(ps.topic)._('</div>');
			}else{
				s._('<div class="db-alt-close" onclick="X.dialog.close(')._(di)._(')";></div>');
			}
			s._('<div class="db-m">');
			if(content)s._($.replace(content,'#di#',di));
			s._('</div></div>');
			db[3]=$.ce('div');
			db[3].addClass('db-bg');
			db[3].css('zIndex',bgi);
			db[3].css('width',ww+'px');
			db[3].css('height',wh+'px');
			db[3].attr('id','dialog-bg-'+di);
			db[4]=$.ce('div');
			db[4].addClass('db');
			db[4].css('zIndex',di);
			db[4].css('width',w+'px');
			db[4].css('left',dl+'px');
			db[4].css('display','none');
			db[4].attr('id','dialog-'+di);
			db[4].htm(s);
			db[5]=ps.notify;
			X.body.append(db[3],db[4]);
			db[2]=db[4].height();
			dt=$.round((wh-db[2])/2+dt);
			if(dt<6)dt=6;
			if(!$.ie(6)){db[4].css('top',dt+'px');}
			db[4].show();
			db[4].find('input:first').focus();
			t.dbs.push(db);
		},
		//提醒对话框 ps:title,msg,icon,width,btn(按钮名称),notify
		alert:function(msg,icon,ps){
			ps=$.init(ps,{title:'',msg:msg,icon:icon,width:400,btn:'确定'});
			if(ps.width<150)ps.width=150;
			var t=this,is=['info','help','error','ok'],s=new Text();
			s._('<div class="db-alt-close" onclick="X.dialog.close(#di#)";></div>');
			s._('<table width="100%"><tr>');
			if(ps.title)
			{
				if(ps.icon==0)
					s._('<td width="1" rowspan="2" align="left"></td>');
				else
					s._('<td width="40" rowspan="2" align="left" valign="top"><div class="db-')._(is[ps.icon-1])._('"></div></td>');
				s._('<td height="20" align="left" valign="top" class="f16 b">');
				s._(ps.title);
				s._('</td></tr><tr><td height="30" align="left" class="f16 h24">');
				s._(msg);
				s._('</td></tr><tr><td></td><td style="padding-top:16px;" align="center" valign="bottom">');
			}
			else
			{
				if(ps.icon==0)
					s._('<td width="1" height="30" align="left"></td>');
				else
					s._('<td width="45" valign="top" style="padding-right:20px;"><div class="db-icon db-icon-')._(is[ps.icon-1])._('"></div></td>');
				s._('<td align="left" valign="top" style=" line-height:150%; color:#333333;padding-right:40px;">');
				s._(msg);
				s._('</td>');
			}
			s._('</tr></table>');
			s._('<div style="padding-top:20px; text-align:center;">');
			t.addBtn(s,ps.btn,'a',1);
			if(ps.cfm)
				t.addBtn(s,ps.btn1,'b',2,true);
			s._('</div>');
			t.open(s.ts(),ps);
		},
		//确认对话框 ps:title,msg,icon,width,btn(按钮名称),btn1(第二个按钮名称),notify
		confirm:function(msg,icon,ps){
			ps=$.init(ps,{msg:msg,icon:icon,btn1:'取消'});
			ps.cfm=true;
			this.alert(msg,icon,ps)
		},
		//添加按钮代码(对话框按钮)
		addBtn:function(s,name,bt,nt,space){
			var style=space?'margin-left:10px;':null;
			var css = nt==2?'btn btn-l-grey':'btn btn-l';
			this.addButton(s,{id:'dialog-btn-'+bt+'-#di#',name:name,css:css,style:style,click:'X.dialog.close(#di#,'+nt+');'});
		},
		//添加按钮代码 ps:id,name,css(按钮CSS),style,click(点击事件),effects(点击效果CSS),type(是按钮或者连接样式)
		addButton:function(s,ps){
			s._('<input type="button"');
			if(ps.id)
				s._(' id="')._(ps.id)._('"');
			if(ps.name)
				s._(' value="')._(ps.name)._('"');
			if(ps.css)
				s._(' class="')._(ps.css)._('"');
			if(ps.style)
				s._(' style="')._(ps.style)._('"');
			if(ps.click)
				s._(' onclick="')._(ps.click)._('"');
			if(ps.effects && ps.css)
				s._(' onmousedown="$(this).cssR(&quot;')._(ps.css)._('&quot;,&quot;')._(ps.effects)._('&quot;);" onmouseup="$(this).cssR(&quot;')._(ps.effects)._('&quot;,&quot;')._(ps.css)._('&quot;);" onmouseout="$(this).cssR(&quot;')._(ps.effects)._('&quot;,&quot;')._(ps.css)._('&quot;);"');
			s._('>');
		},
		addTips:function(s,ps){
			var icons=['help','info','error','ok','warning'];
			s._('<div class="db-tips"><table width="100%"><tr>');
			if(ps.icon){
				s._('<td width="40" valign="top"><span class="icon icon-')._(icons[ps.icon])._('"></span></td>');
			}
			s._('<td style="padding:5px 5px 5px 0px;">')._(ps.info)._('</td>');
			s._('</tr></table></div>');
		},
		//操作通知
		notify:function(di,nt){
			var db=this.get(di);
			if(db[5])db[5]($.no(nt)?0:nt);
		},
		//关闭对话框或提示框(0不关闭,1关闭,2关闭并关闭上级对话框)
		close:function(di,nt){
			var t=this,b,l,c=1,cn,cv;
			if($.no(nt))nt=0;
			if(!$.no(di))
			{
				b=t.get(di);
				if(b)
				{
					if($.isN(di))
					{
						if(b[5]){c=b[5](nt);if($.no(c))c=1;}
						if(c>0){t.get(di,true);b[4].remove();b[3].remove();}
						if(c<2)return;
					}
					else
					{
						if(b[6]){c=b[6](nt);if($.no(c))c=1;}
						if(c>0){t.get(di,true);if(b[3]){cn='PROMPT';cv='['+b[0]+']';l=$.getCookie(cn,'');if(l.indexOf(cv)<0)$.setCookie(cn,l+cv,null,'/');}b[5].slideUp(function(){b[5].remove();});}
						return;
					}
				}else{return;}
			}
			//关闭最后打开的对话框
			l=t.dbs.length;
			if(l>0)t.close(t.dbs[l-1][0],0);
		},
		//返回对话框
		get:function(di,del){
			var t=this,bs=$.isN(di)?t.dbs:t.pbs,b;
			for(var i=0;i<bs.length;i++)
				if(bs[i][0]==di)
				{
					b=bs[i];
					if(del)
						bs.splice(i,1);
					break;
				}
			return b;
		},
		//调整大小位置
		resize:function(){
			var t=this,dbs=t.dbs,pbs=t.pbs,mbs=t.mbs;
			if(dbs.length==0 && pbs.length==0 && mbs.length==0)
				return;
			var p=X.ps(),ww=p.width,wh=p.height,dl,dt,top=-30,o,obj;
			if($.ie(6))top+=p.top;
			for(var i=0;i<dbs.length;i++)
			{
				o=dbs[i];
				dl=$.round((ww-o[1])/2);
				if(dl<6)dl=6;
				dt=$.round((wh-o[2])/2+top);
				if(dt<6)dt=6;
				o[3].css('width',ww+'px');
				o[3].css('height',wh+'px');
				o[4].css('top',dt+'px');
				o[4].css('left',dl+'px');
			}
			for(var i=0;i<pbs.length;i++)
			{
				o=pbs[i];
				obj=$.$(o[4]);
				if(obj.length>0)
				{
					p=obj.offset();
					ww=p.left+obj.width()/2;
					wh=p.top+obj.height()+5;
					if(o[2]%2==1)ww-=15;else{ww-=o[1]-15;}
					o[5].css('top',wh+'px');
					o[5].css('left',ww+'px');
				}
			}
			for(var i=0;i<mbs.length;i++)
			{
				o=mbs[i];
				if(o!=null && o.length>4)
				{
					dl=$.round((ww-o[5])/2);
					o[4].css('left',dl+'px');
				}
			}
		}
	};
	//选型卡g='异步数据请求方法名'
	X.tab={
		init:function(id){
			id=id||'tab1';
			var tab=$.$(id),tts=tab.find('div.tabtitle span'),tis=tab.find('div.tabcon div.subtab');
			tts.first().addClass('curr');
			tis.first().show().siblings().hide();
			tts.click(function(){
				var i = tts.index(this),currTab = tts.eq(i),currTabCon=tis.eq(i),g=currTab.attr('g');
				if(currTab.hasClass('curr'))return;
				currTab.addClass('curr').siblings().removeClass('curr');
				if(g){eval(g);}
				currTabCon.show().siblings().hide();
			});
		}
	};
	
	X.suggest={
			//datas[联想输入文本框，输入内容，匹配数据，提示框，选择数据]
			focus:false,datas:[],keyCount:0,
			bind:function(inp){
				var t=this;
				inp=$.$(inp);
				inp.bind({
					focus:function(e){t.focus=true;t.change(inp);},
					blur:function(e){t.focus=false;},
					keyup:function(e){var k=e.keyCode;if(k==13)t.go();else if(k<37||k>40)t.change(inp);else if(k==38||k==40)return false;},
					keydown:function(e){var k=e.keyCode;if(k==38||k==40){t._ud(k==38);return false;}}
				});
			},
			//搜索内容变化处理
			change:function(inp){
				var t=this,ds=t.datas,o=ds[3],v=$.clear(inp.val()),s=new Text();
				ds[0]=inp,ds[1]=v,p=inp.offset(),ps=X.ps(),iet=$.ie(6)||$.ie(7);
				var mails=t.createEd(v);
				if(v.length==0 || mails.length==0){if(o)o.hide();return;}		
				if(!o)
				{
					o=$.ce('div');
					o.addClass('suggest-box');
					o.css('zIndex',X.zi());
					ds[3]=o;
					//inp.after(o);
					$(document.body).append(o);
				}
				//显示结果框
				o.css('left',p.left+'px');
				o.css('top',(p.top+inp.innerHeight())+'px');
				if(mails.length>0){
					s._('<ul>');
					for(i in mails){
						s._('<li class="')._(i==0?'selected':'')._('" onmouseover="X.suggest._ud(')._(i)._(');" onclick="X.suggest.go();">')._(mails[i])._('</li>');
					}
					s._('</ul>');
					o.show();
				}
				o.htm(s);
			},
			//创建Email联想数据
			createEd:function(v){
				var v1=v.substring(v.lastIndexOf('@')+1),v2=v.substring(0,v.lastIndexOf('@')+1);
				var mails=['qq.com','163.com','126.com','189.cn','sina.com','hotmail.com','gmail.com','sohu.com','21cn.com'],ds=[];
				for(i in mails){
					if(v.indexOf('@')!=-1 && mails[i].indexOf(v1)!=-1){ds.push(v2+mails[i]);}
				}
				return ds;
			},
			//搜索结果上下选择
			_ud:function(up){
				var t=this,ds=t.datas,c='selected',n=$.isN(up),os,o=ds[3];
				if(!o||!o.visible())return;
				os=o.children().children('li');if(os.length<=1)return;
				
				for(var i=0;i<os.length;i++)
				{
					o=$(os[i]);
					if(o.hasClass(c))
					{
						if(n&&i==up)return;
						o.removeClass(c);
						if(n)o=$(os[up]);else{o=$(os[up?(i==0?os.length-1:i-1):(i==os.length-1?0:i+1)]);}
						o.addClass(c);
						break;
					}
				}
			},
			resize:function(){
				var t=this,ds=t.datas,o=ds[3],inp=ds[0];
				if(!o)return;
				//显示结果框
				var p=inp.offset();
				o.css('left',p.left+'px');
				o.css('top',(p.top+inp.innerHeight())+'px');
			},
			//搜索结果执行
			go:function(){
				var t=this,ds=t.datas,o=ds[0],v=ds[1],bo=ds[3];
				if(!v)return;
				o.val(bo.find('li.selected').text());
				t.close(true);
			},
			//关闭搜索结果框
			close:function(abs){
				var t=this,ds=t.datas;if((abs||!t.focus)&&ds[3]){ds[3].hide();}
			}
		};
	
	//重点提示框 ps:dire(提示指针方向),width,close(关闭是否允许),remem(记住关闭到Cookie),delay,zIndex,notify
	X.prompt={
		dbs:[],
		init:function(obj,msg,ps){
			ps=$.init(ps,{dire:0,width:200,close:true,remem:false,reget:false,delay:1000,ox:0,oy:0});
			var t=this,o=$.$(obj),cn,cv;
			if(!o)return;
			if(!ps.close){
				o.css('cursor','pointer');
				o.bind({
					mouseover:function(){
						var b = t.get(obj);
						if(!b){
							t.open(obj,'',ps);
							b = t.get(obj);
						}
						if(ps.reget){
							var pobj = t.getPosition(o,ps.dire,b[1].width(),b[1].height());
							b[1].css('left',pobj.left+ps.ox+'px');
							b[1].css('top',pobj.top+ps.oy+'px');
						}
						b[1].show();
					},
					mouseout:function(){
						var b = t.get(obj);
						if(!b)return;
						b[1].hide();
					}
				});
			}else{
				if(ps.remem){cn='PROMPT';cv='[db-p-'+obj+']';if($.getCookie(cn,'').indexOf(cv)>=0)return;}
				t.open(obj,msg,ps);
				var b = t.get(obj);
				b[1].show();
			}
		},
		open:function(obj,msg,ps){
			var o=$.$(obj);
			if(!o)return;
			var cls='db-p-'+ps.dire,db=['db-p-'+obj],s=new Text();
			
			if(ps.close){
				s._('<em></em><span></span><div class="db-p-con">')._(msg)._('</div>');
				s._('<div class="db-p-close" onclick="X.prompt.close(\'')._(obj)._('\')"></div>');
			}else{
				s._('<em></em><span></span><div class="db-p-con">')._(o.attr('data-text'))._('</div>');
			}
			db[1]=$.ce('div');
			db[1].addClass('db-p');
			db[1].addClass(cls);
			db[1].css('width',ps.width+'px');
			db[1].css('display','none');
			db[1].html(s.ts());
			$(document.body).append(db[1]);
			
			var pobj = this.getPosition(o,ps.dire,db[1].width(),db[1].height());
			db[1].css('left',pobj.left+ps.ox+'px');
			db[1].css('top',pobj.top+ps.oy+'px');
			this.dbs.push(db);
			//db[1].show();
		},
		getPosition:function(obj,dire,w,h){
			var p=obj.offset(),W=obj.width(),H=obj.height(),L=p.left,T=p.top,l,t;
			if(dire==0){
				l=Math.round(L+W/2-w/2),t=T+H+9;
				return {'left':l,'top':t};
			}
			if(dire==1){
				l=Math.round(L+W/2+25-w),t=T+H+9;
				return {'left':l,'top':t};
			}
			if(dire==2){
				l=L-9-w,t=Math.round(T+H/2-h/2);
				return {'left':l,'top':t};
			}
			if(dire==3){
				l=Math.round(L+W/2+25-w),t=T-9-h;
				return {'left':l,'top':t};
			}
			if(dire==4){
				l=Math.round(L+W/2-w/2),t=T-9-h;
				return {'left':l,'top':t};
			}
			if(dire==5){
				l=Math.round(L+W/2-25),t=T-9-h;
				return {'left':l,'top':t};
			}
			if(dire==6){
				l=L+W+9,t=Math.round(T+H/2-h/2);
				return {'left':l,'top':t};
			}
			if(dire==7){
				l=Math.round(L+W/2-25),t=T+H+9;
				return {'left':l,'top':t};
			}
		},
		//返回对话框
		get:function(id){
			var t=this,bs=t.dbs,b;
			for(var i=0;i<bs.length;i++)
				if(bs[i][0]=='db-p-'+id)
				{
					b=bs[i];
					break;
				}
			return b;
		},
		close:function(id){
			var b=this.get(id),cn,cv,l;
			if(b){
				cn='PROMPT';
				cv='['+b[0]+']';
				l=$.getCookie(cn,'');
				if(l.indexOf(cv)<0){
					$.setCookie(cn,l+cv);
				}
				b[1].remove();
			}
		}
	}

	//执行引擎
	X.engine={
		//引擎ID
		id:null,
		//执行间隔
		interval:222,
		//任务列表
		tasks:[],
		//启动引擎
		start:function(){
			if(this.id || this.tasks.length==0)
				return;
			this.exec();
			this.id=setInterval(function(){X.engine.exec();},this.interval);
		},
		//停止引擎
		stop:function(){
			clearInterval(this.id);
			this.id=null;
		},
		//执行任务
		exec:function(){
			for(var i=0;i<this.tasks.length;i++)
				this.tasks[i].exec();
		},
		//绝对执行
		absExec:function(type){
			for(var i=0;i<this.tasks.length;i++)
				this.tasks[i].exec(type);
		},
		//添加任务
		addTask:function(t){
			return this.tasks.push(t)-1;
		}
	};
	
  function Update(){
	//执行间隔
	this.interval=10000;
	//执行时间
	this.execTime=$.t();
  };
  Update.prototype={
	exec:function(){
		var t=this,ct=$.t();
		if(ct-t.execTime<t.interval)
			return false;
		t.execTime=ct;
		t.upd();
	},
	upd:function(){
		var t=this;
		var s=new Text();
		$.get("/sectorNews.html",{},function(data){
			for(var i=0; i<3; i++){
				$('#gpInfo').find('tr').eq(1).remove();
			}
			var decr = data.decr, incr = data.incr;
			for(var i=0; i<decr.length; i++){
				s._('<tr>');
				s._('<td><a target="_blank" href="http://quote.yztz.com/quote/sector_')._(incr[i].id)._('">')._(incr[i].name)._('</a></td>');
				s._('<td>')._(incr[i].data)._('</td>');
				s._('<td><a target="_blank" href="http://quote.yztz.com/quote/sector_')._(decr[i].id)._('">')._(decr[i].name)._('</a></td>');
				s._('<td class="text-right">')._(decr[i].data)._('</td>');
				s._('</tr>')
			}
			$('#gpInfo').append(s.ts());
			t.updColor();
		},"json");
		
	},
	updColor:function(){
		for(var i=1; i<4; i++){
			var td = $('#gpInfo').find('tr').eq(i).find('td');
			if(td.eq(1).text().indexOf('+')>-1)td.eq(1).addClass('c_up');
			if(td.eq(1).text().indexOf('-')>-1)td.eq(1).addClass('c_down');
			if(td.eq(3).text().indexOf('+')>-1)td.eq(3).addClass('c_up');
			if(td.eq(3).text().indexOf('-')>-1)td.eq(3).addClass('c_down');
		}
	}
  };
  function updateQuote(id,p,r){
		var c=r.charAt(0),udt=c=='+'?'c_up':(c=='-'?'c_down':''),s=new Text();
		s._('<i class="ml15 fs16 ')._(udt)._('">')._(p)._('</i><i class="ml15 fs16 ')._(udt)._('">')._(r)._('</i>');
		$("#g_"+id).html(s.ts());
  }
	
	X.keypress={
		numKeyPress:function(e){
			var k = e.keyCode || e.which;
			if(k>=48&&k<=57||k==8){
				return true;
			}
			return false;
		}
	}
	X.uc={};


X.scheme={
	resolveToken:function(tokenName,callback){
		if(callback&&''!=$.trim(tokenName)){
			$.post(ctx.trade+"/tokenResolver.html",
					{token:tokenName},function(data){
						if(data.token){
							callback(data.token);
						}
					},'json');
		}
	},
	/**
	 * 股票配资仓位限制
	 */
	getStockLimit:function(money){
		if($.isNumeric(money)){
			if(money<=500000){
				return "";
			}else if(money<=1000000){
				return "单股不超总操盘资金的50%";
			}else{
				return "单股不超总操盘资金的50%（创业板33%）";
			}
		}
		return "";
	},
	getEverwinLimit:function(money,lever){
		if($.isNumeric(money)&&$.isNumeric(lever)){
			if(lever==5){
				if(money<=500000){
					return "";
				}else if(money<=1000000){
					return "单股不超总操盘资金的50%";
				}else{
					return "单股不超总操盘资金的50%（创业板33%）";
				}
			}else if(lever==10){
				if(money<=200000){
					return "";
				}else if(money<=1000000){
					return "单股不超总操盘资金的50%";
				}
			}else if(lever==15){
				if(money<=100000){
					return "";
				}else{
					return "单股不超总操盘资金的50%";
				}
			}
		}
		return "";
	},
	getWeekwinLimit:function(money){
		if($.isNumeric(money)){
			if(money<=500000){
				return "";
			}else if(money<=1000000){
				return "单股不超总操盘资金的50%";
			}else{
				return "单股不超总操盘资金的50%（创业板33%）";
			}
		}
		return "";
	},
	/**
	 * 计算配资月利率以及预警平仓线
	 */
	getScheme:function(money,cycle,fixRate){
		var interest = 190;
		if(fixRate&&fixRate>0.1){
			interest = fixRate;
		}else{
			if(money>=1000000){
				if(cycle>=3){
					interest = 160;
				}else if(cycle>=1){
					interest = 170;
				}
			}else if(money>=100000){
				if(cycle>=3){
					interest = 170;
				}else if(cycle>=1){
					interest = 180;
				}
			}else if(money>=1000){
				if(cycle>=3){
					interest = 180;
				}else if(cycle>=1){
					interest =190;
				}
			}
		}
		return {interest:interest,openLine:Math.round(money*1.07),warningLine:Math.round(money*1.1)};
	},
	getEverwin:function(money,lever){
		if($.isInt(money)&&$.isInt(lever)){
			if(lever==5||lever==10||lever==15){
				if(lever==5){
					return {warning:money*110/100,open:money*107/100,principal:Math.floor(money/lever),fee:$.round(money*1.2/1000,4),interest:1.2};
				}else if(lever==10){
					return {warning:money*106/100,open:money*104/100,principal:Math.floor(money/lever),fee:$.round(money*1.5/1000,4),interest:1.5};
				}
				return {warning:money*105/100,open:money*103/100,principal:Math.floor(money/lever),fee:$.round(money*2/1000,4),interest:2};
			}
		}
		return {warning:0,open:0,principal:0,fee:0,interest:0};;
	},
	getWeekwin:function(money){
		if($.isInt(money)){
			return {warning:money*110/100,open:money*107/100,principal:Math.floor(money/5),fee:$.round(money*1.2/1000,4),interest:1.2};
		}
		return {warning:0,open:0,principal:0,fee:0,interest:0};;
	},
	/**
	 * 检测方案发起的参数是否合法
	 * 
	 */
	initiateCheck : function(config){
		if($.isPlainObject(config)){
			//principal
			if($.trim(config.principal)==''){
				alert("请输入投资本金",1,{notify:function(){$("#principal").focus();}});
				return false;
			}else if(!X.valid.isInt(config.principal,true)){
				alert("投资本金填写错误，金额必须是整数",1,{notify:function(){$("#principal").focus();}});
				return false;
			}
			var principal = parseInt(config.principal,10);
			var maxPrincipal = Math.floor(schemeConfig.maxMoney)
			if(principal<schemeConfig.minMoney||principal>maxPrincipal){
				alert("投资本金最少1千元，最多"+$.formatMoney(maxPrincipal/10000)+"万元",1,{notify:function(){$("#principal").focus();}});
				return false;
			}else if(principal-Math.round(principal)!=0||principal%1000!=0){
				alert("投资本金必须是1000的整数倍",1,{notify:function(){$("#principal").focus();}});
				return false;
			}
			
			//lever
			if($.trim(config.lever)==''){
				alert("请输入配资倍数",1);
				return false;
			}else if(!X.valid.isInt(config.lever,true)){
				alert("配资倍数必须是整数",1);
				return false;
			}
			var lever = parseInt(config.lever,10);
			if(lever<schemeConfig.minLever||lever>schemeConfig.maxLever){
				alert("配资倍数必须在"+schemeConfig.minLever+"-"+schemeConfig.maxLever+"倍",1);
				return false;
			}
			
			//cycle
			if($.trim(config.cycle)==''){
				alert("请选择借款期限",1);
				return false;
			}else if(!X.valid.isInt(config.cycle,true)){
				alert("借款期限必须是整数",1);
				return false;
			}
			var cycle = parseInt(config.cycle,10);
			if(cycle<1||cycle>schemeConfig.maxCycle){
				alert("借款期限必须在1-"+schemeConfig.maxCycle+"个月",1);
				return false;
			}
			
			if(config.intention){
				if(!$.isInt(config.intention)||config.intention<0){
					alert("意向金必须是非负整数",1);
					return false;
				}
				var intention = Math.round(principal*schemeConfig.intentionPercent/100);
			
				if(intention<schemeConfig.minIntention){
					intention = schemeConfig.minIntention;
				}else if(intention>schemeConfig.maxIntention){
					intention = schemeConfig.maxIntention;
				}
				if(Math.abs(config.intention-intention)>=0.01){
					alert("意向金必须为"+intention+"元",1);
					return false;
				}
			}
			if(!config.agree){
				alert("请先阅读并同意签署《借款协议》",1,{notify:function(){$("input[name='agree']").focus();}});
				return false;
			}
			return true;
		}
		alert("数据对象错误",3);
		return false;
	},
	/**
	 * 检查并发起方案
	 */
	checkAndInitiate:function(config){
		if(X.scheme.initiateCheck(config)&&X.user.checkAndShowLogin()){
			config.action="initiate";
			
			X.form.disableBtn("initBtn");
			X.ajax(ctx.trade+"/schemeInitiate.html",config,function(data){
				data = $.parseJSON(data);
				if(data.success){
					window.location.href=ctx.trade+"/schemeInitiateComplete.html?schemeId="+data.schemeId;
				}else{
					X.form.enableBtn("initBtn");
					if(X.code.unauthorized==data.code){
						X.user.clearUserCookie();
						X.user.checkAndShowLogin();
					}else if(X.code.nameUnsettled==data.code){
						X.uc.user.legalizeID(false,function(){initiate();});
					}else if(X.code.balanceShortage==data.code){
						var ps = {};
						if(data.shortage){
							ps.shortage = data.shortage;
						}
						X.pay.showCharge(function(balance){
							X.user.loadPageHeader();
							$(".chooseBuyType .curr").click();
							showRebate();
						},$.np,ps);
					}else if(X.code.rebate==data.code){
						X.user.loadBalance(function(){
							X.user.loadPageHeader();
							$(".chooseBuyType .curr").click();
							showRebate();
						});
						X.dialog.alert(data.resultMsg,1);
					}else{
						X.dialog.alert(data.resultMsg,1);
					}
					if(data.token){
						$("input[name='token']").val(data.token);
					}
				}
			});
		}
	},
	everWinInitiateCheck:function(config){
		if($.trim(config.money)==''){
			X.dialog.alert("请输入实盘资金",1,{notify:function(){$("#tm").focus();}});
			return false;
		}
		if(!/^\d+$/.test(config.money)||parseInt(config.money,10)%1000!=0){
			X.dialog.alert("实盘资金必须是1000的整数倍",1,{notify:function(){$("#tm").focus();}});
			return false;
		}
		var money = parseInt(config.money,10);
		if(money<2000||money>config.maxMoney){
			X.dialog.alert("实盘资金最少2千，最多"+$.formatMoney(config.maxMoney/10000)+"万",1,{notify:function(){$("#tm").focus();}});
			return false;
		}
		
		if($.trim(config.lever)==''){
			X.dialog.alert("请选择风险保证金",1);
			return false;
		}
		if(config.lever!='5'&&config.lever!='10'&&config.lever!='15'){
			X.dialog.alert("风险保证金错误",1);
			return false;
		}
		if(!config.agree){
			X.dialog.alert("请先阅读并同意签署《天天赢合作操盘协议》",1,{notify:function(){$("#agree").focus();}});
			return false;
		}
		return true;
	},
	weekWinInitiateCheck:function(config){
		if($.trim(config.money)==''){
			X.dialog.alert("请输入实盘资金",1,{notify:function(){$("#tm").focus();}});
			return false;
		}
		if(!/^\d+$/.test(config.money)||parseInt(config.money,10)%1000!=0){
			X.dialog.alert("实盘资金必须是1000的整数倍",1,{notify:function(){$("#tm").focus();}});
			return false;
		}
		var money = parseInt(config.money,10);
		if(money<10000||money>config.maxMoney){
			X.dialog.alert("实盘资金最少1万，最多"+$.formatMoney(config.maxMoney/10000)+"万",1,{notify:function(){$("#tm").focus();}});
			return false;
		}
		if(!config.agree){
			X.dialog.alert("请先阅读并同意签署《周周盈合作操盘协议》",1,{notify:function(){$("#agree").focus();}});
			return false;
		}
		return true;
	},
	checkAndInitiateEverWin:function(config){
		config.agree=true;
		config.action='initiate';
		
		X.form.disableBtn("initBtn");
		if(X.scheme.everWinInitiateCheck(config)){
			X.ajax(ctx.trade+"/everwin/initiate.html",config,function(data){
				data = $.parseJSON(data);
				if(data.success){
					window.location.href=ctx.trade+"/everwin/complete.html?schemeId="+data.schemeId;
				}else{
					X.form.enableBtn("initBtn");
					if(X.code.unauthorized==data.code){
						X.user.clearUserCookie();
						X.user.checkAndShowLogin();
					}else if(X.code.nameUnsettled==data.code){
						X.uc.user.legalizeID(false,function(){initiate();});
					}else if(X.code.balanceShortage==data.code){
						var ps = {};
						if(data.shortage){
							ps.shortage = data.shortage;
						}
						X.pay.showCharge(function(balance){
							X.user.loadPageHeader();
							showBalanceTip();
						},$.np,ps);
					}else if(X.code.rebate==data.code){
						X.user.loadBalance(function(){
							X.user.loadPageHeader();
							showBalanceTip();
						});
						X.dialog.alert(data.resultMsg,1);
					}else if(X.code.schemeError==data.code){
						X.dialog.alert(data.resultMsg,1);
					}else{
						X.dialog.alert(data.resultMsg,1);
					}
					if(data.token){
						$("input[name='token']").val(data.token);
					}
				}
			});
		}
	},
	checkAndInitiateWeekWin:function(config){
		config.agree=true;
		config.action='initiate';
		
		X.form.disableBtn("initBtn");
		if(X.scheme.weekWinInitiateCheck(config)){
			X.ajax(ctx.trade+"/weekwin/initiate.html",config,function(data){
				data = $.parseJSON(data);
				if(data.success){
					window.location.href=ctx.trade+"/weekwin/complete.html?schemeId="+data.schemeId;
				}else{
					X.form.enableBtn("initBtn");
					if(X.code.unauthorized==data.code){
						X.user.clearUserCookie();
						X.user.checkAndShowLogin();
					}else if(X.code.nameUnsettled==data.code){
						X.uc.user.legalizeID(false,function(){initiate();});
					}else if(X.code.balanceShortage==data.code){
						var ps = {};
						if(data.shortage){
							ps.shortage = data.shortage;
						}
						X.pay.showCharge(function(balance){
							X.user.loadPageHeader();
							showBalanceTip();
						},$.np,ps);
					}else if(X.code.rebate==data.code){
						X.user.loadBalance(function(){
							X.user.loadPageHeader();
							showBalanceTip();
						});
						X.dialog.alert(data.resultMsg,1);
					}else if(X.code.schemeError==data.code){
						X.dialog.alert(data.resultMsg,1);
					}else{
						X.dialog.alert(data.resultMsg,1);
					}
					if(data.token){
						$("input[name='token']").val(data.token);
					}
				}
			});
		}
	},
	/**
	 * 体验方案
	 */
	startExperience:function(){
		X.form.disableBtn("experienceBtn");
		X.ajax(ctx.trade+"/initiateExperience.html",{},function(data){
			data = $.parseJSON(data);
			X.form.enableBtn("experienceBtn");
			if(data.success){
				X.dialog.alert("免费体验参与成功",4,{notify:function(){
					window.location.href = ctx.trade+"/user/userEverwin.html";
				}});
			}else{
				if(X.code.unauthorized==data.code){
					X.user.clearUserCookie();
					X.user.checkAndShowLogin(function(){X.scheme.startExperience();});
				}else if(X.code.nameUnsettled==data.code){
					X.uc.user.legalizeID(false,function(){X.scheme.startExperience();});
				}else if(X.code.balanceShortage==data.code){
					var ps = {};
					if(data.shortage){
						ps.shortage = data.shortage;
					}
					X.pay.showCharge(function(balance){
						X.user.loadPageHeader();
					},$.np,ps);
				}else{
					X.dialog.alert(data.resultMsg,1);
				}
				if(data.token){
					$("input[name='token']").val(data.token);
				}
			}
		});
	},
	startMatch:function(match){
		X.form.disableBtn($("div[name=n"+match+"] input[type='button']"));
		X.ajax(ctx.trade+"/match.html",{action:"apply",match:match},function(data){
			X.form.enableBtn($("div[name=n"+match+"] input[type='button']"));
			data = $.parseJSON(data);
			if(data.success){
				X.dialog.alert("您已成功参与了全民实盘炒股大赛",4,{notify:function(){
					X.form.disableBtn($("div[name=n"+match+"] input[type='button']"),{dtext:"已参与"});
					window.location.href = ctx.trade+"/user/userScheme.html";
				}});
			}else{
				if(X.code.unauthorized==data.code){
					X.user.clearUserCookie();
					X.user.checkAndShowLogin();
				}else if(X.code.nameUnsettled==data.code){
					X.uc.user.legalizeID(false,function(){X.scheme.startMatch(match);});
				}else if(X.code.balanceShortage==data.code){
					var ps = {};
					if(data.shortage){
						ps.shortage = data.shortage;
					}
					X.pay.showCharge(function(balance){
						X.user.loadPageHeader();
						X.scheme.startMatch(match);
					},$.np,ps);
				}else{
					X.dialog.alert(data.resultMsg,1);
				}
			}
			if(data.token){
				$("input[name='token']").val(data.token);
			}
		});
	},
	checkMatchJoined:function(match,callback){
		X.ajax(ctx.trade+"/match.html",{action:"getMatchJoined",match:match},function(data){
			data = $.parseJSON(data);
			if(data.joined){
				X.form.disableBtn($("div[name=n"+match+"] input"),{dtext:"已参加"});
			}
			if(callback){
				callback(data.joined);
			}
		});
	},
	/**
	 * 修改发起方案
	 */
	modifyInitiate:function(data){
		var param = "";
		for(key in data){
			param += key+'='+encodeURI(encodeURI(data[key]))+'&';
		}
		window.location.href = ctx.trade+"/?"+param;
	},
	modifyEverWinInitiate:function(data){
		var param = "";
		for(key in data){
			param += key+'='+encodeURI(encodeURI(data[key]))+'&';
		}
		
		window.location.href = ctx.trade+"/everwin/?"+param;
	},
	modifyWeekWinInitiate:function(data){
		var param = "";
		for(key in data){
			param += key+'='+encodeURI(encodeURI(data[key]))+'&';
		}
		window.location.href = ctx.trade+"/weekwin/?"+param;
	},
	/*
	 * 方案支付补偿金
	 **/
	payCompensation:function(schemeId,total,paid){
		var t=this,d=X.dialog,s=new Text();
		s._('<div class="db-con">');
		s._('<div>超额亏损补偿金：<b>'+total+'</b>元'+(paid>0?'（已补偿：<b>'+paid+'</b>元）':'')+'</div>');
		s._('<div>还需支付：<b style="color:#FF6600; font-size:18px;">'+(total-paid)+'</b>元</div>');
		s._('<div style="padding:30px 0 10px; text-align:center;"><input id="compensationPayBtn" type="button" onclick="X.dialog.notify(#di#,1);" class="btn btn-l" value="确定"><input type="button" onclick="X.dialog.close(#di#,0);" class="btn btn-l-grey ml10" value="取消"></div>');
		s._('</div>');
		d.open(s.ts(),{topic:'支付补偿金',width:450,notify:function(nt){
			if(nt==0)return;
			X.form.disableBtn("compensationPayBtn");
			X.ajax(ctx.trade+"/schemeOper.html",{schemeId:schemeId,action:'payCompensation'},function(data){
				data = $.parseJSON(data);
				X.form.enableBtn("compensationPayBtn");
				if(data.success){
					d.close();
					X.dialog.alert("补偿金支付成功",4,{notify:function(){
						window.location.reload();
					}});
				}else{
					if(X.code.unauthorized==data.code){
						X.user.clearUserCookie();
						X.user.checkAndShowLogin();
					}else if(X.code.balanceShortage==data.code){
						var ps = {};
						if(data.shortage){
							ps.shortage = data.shortage;
						}
						X.pay.showCharge(function(balance){
							X.user.loadPageHeader();
							showBalanceTip();
						},$.np,ps);
					}else if(X.code.schemeError==data.code){
						X.dialog.alert(data.resultMsg,1);
					}else{
						X.dialog.alert(data.resultMsg,1);
					}
				}
			});
		}});
	},
	/**
	 * 方案详情页面不良记录跳转
	 */
	detailBadRecordPageJump:function(userId,page){
		X.ajax(ctx.trade+"/scheme/schemeDetailBadRecord.html",{page:page,userId:userId,pageSize:5},function(data){
			var txt = new Text();
			txt._(data);
			txt.toHtml("badRecordContent");
		});
	},
	/**
	 * 投标记录页面跳转
	 */
	detailBidsPageJump:function(schemeId,page){
		X.ajax(ctx.trade+"/scheme/schemeDetailBid.html",{page:page,status:bidConfig.statusBidding+","+bidConfig.statusBidWon,schemeId:schemeId,pageSize:10},function(data){
			var txt = new Text();
			txt._(data);
			txt.toHtml("bidsContent");
			$("#_bidMoney").text($.v("bidMoney"));
			$("#bidsContent").data("page",page);
			$("#bidsContent").data("schemeId",schemeId);
		});
	},
	detailBidsPageReload:function(){
		X.scheme.detailBidsPageJump($("#bidsContent").data("schemeId"),$("#bidsContent").data("page"));
	},
	detailLoansPageJump:function(schemeId,page){
		X.ajax(ctx.trade+"/scheme/schemeDetailBid.html",{action:"loans",page:page,
									status:bidConfig.statusBidWon+","+bidConfig.statusTransfer+","+bidConfig.statusTransferRenewal,
									schemeId:schemeId,
									pageSize:10},function(data){
			var txt = new Text();
			txt._(data);
			txt.toHtml("loansContent");
			$("#_money").text($.v("money"));
			$("#loansContent").data("page",page);
			$("#loansContent").data("schemeId",schemeId);
		});
	},
	detailLoansPageReload:function(){
		X.scheme.detailLoansPageJump($("#loansContent").data("schemeId"),$("#loansContent").data("page"));
	},
	detailInterestsPageJump:function(schemeId,page,status){
		if('0'==page&&$("#interestsContent").data("loaded")){
			return true;
		}
		X.ajax(ctx.trade+"/scheme/schemeDetailInterest.html",{page:page,schemeId:schemeId,pageSize:10,status:(status?status:'')},function(data){
			var txt = new Text();
			txt._(data);
			txt.toHtml("interestsContent");
			$("#interestsContent").data("loaded",true);
		});
	},
	everwinInterestsPageJump:function(schemeId,page,status){
		if('0'==page&&$("#interestsContent").data("loaded")){
			return true;
		}
		X.ajax(ctx.trade+"/scheme/everwinDetailInterest.html",{page:page,schemeId:schemeId,pageSize:10,status:(status?status:'')},function(data){
			var txt = new Text();
			txt._(data);
			txt.toHtml("interestsContent");
			$("#interestsContent").data("loaded",true);
		});
	},
	/**
	 * 债权转让页面跳转
	 */
	detailTransfersPageJump:function(schemeId,page){
		if('0'==page&&$("#transfersContent").data("loaded")){
			return true;
		}
		X.ajax(ctx.trade+"/scheme/schemeDetailTransfer.html",{page:page,schemeId:schemeId,pageSize:10},function(data){
			var txt = new Text();
			txt._(data);
			txt.toHtml("transfersContent");
			$("#transfersContent").data("loaded",true);
		});
	},
	/**
	 * 方案资金明细页面跳转
	 */
	schemeFundRecordPageJump:function(schemeId,page){
		if('0'==page&&$("#fundRecordsContent").data("loaded")){
			return true;
		}
		X.ajax(ctx.trade+"/scheme/fundRecord.html",{action:"schemeFundRecord",page:page,schemeId:schemeId},function(data){
			var txt = new Text();
			txt._(data);
			txt.toHtml("fundRecordsContent");
			$("#fundRecordsContent").data("loaded",true);
		});
	},
	/**
	 * 方案资金明细页面跳转
	 */
	everwinFundRecordPageJump:function(schemeId,page){
		if('0'==page&&$("#fundRecordsContent").data("loaded")){
			return true;
		}
		X.ajax(ctx.trade+"/scheme/fundRecord.html",{action:"everwinFundRecord",page:page,schemeId:schemeId},function(data){
			var txt = new Text();
			txt._(data);
			txt.toHtml("fundRecordsContent");
			$("#fundRecordsContent").data("loaded",true);
		});
	},
	/**
	 * 方案资金明细页面跳转
	 */
	weekwinFundRecordPageJump:function(schemeId,page){
		if('0'==page&&$("#fundRecordsContent").data("loaded")){
			return true;
		}
		X.ajax(ctx.trade+"/scheme/fundRecord.html",{action:"weekwinFundRecord",page:page,schemeId:schemeId},function(data){
			var txt = new Text();
			txt._(data);
			txt.toHtml("fundRecordsContent");
			$("#fundRecordsContent").data("loaded",true);
		});
	},
	/**
	 * 续约跳转
	 */
	detailRenewalsPageJump:function(schemeId,page){
		if('0'==page&&$("#renewalsContent").data("loaded")){
			return true;
		}
		X.ajax(ctx.trade+"/scheme/schemeDetailRenewal.html",{page:page,schemeId:schemeId,pageSize:10},function(data){
			var txt = new Text();
			txt._(data);
			txt.toHtml("renewalsContent");
			$("#renewalsContent").data("loaded",true);
		});
	}
	,
	//修改方案
	updScheme:function(){
		var t=this,d=X.dialog,f=X.form,s=new Text();
		//投标方案信息
		s._('<div class="db-con-1">');
		s._('<div class="ui-sub-form"><label class="ui-label">投资本金</label><em class="h">')._($.formatMoney(modifyContext.principal))._('</em><em class="unit">元</em></div>');
		s._('<div class="ui-sub-form"><label class="ui-label">配资倍数</label><em class="h">')._(modifyContext.lever)._('</em><em class="unit">倍</em></div>');
		s._('<div class="ui-sub-form"><label class="ui-label">借款金额</label><em class="h">')._($.formatMoney(modifyContext.money))._('</em><em class="unit">元</em></div>');
		s._('<div class="ui-sub-form"><label class="ui-label">借款期限</label><em class="h">')._(modifyContext.cycle)._('</em><em class="unit">个月</em></div>');
		s._('<div class="ui-sub-form"><label class="ui-label">借款月利息</label>');
		var tx1=modifyContext.interest;
		t.addSelect(s,{css:'sel-b w100',id:"_interest"},tx1,modifyContext.interestSelect);
		s._('</div>');
		s._('<div class="ui-sub-form"><label class="ui-label">筹款期限</label>');
		var tx2=[['1天',1],['2天',2],['3天',3],['4天',4],['5天',5],['6天',6],['7天',7]];
		t.addSelect(s,{css:'sel-b w100',id:"_timeLimit"},tx2,modifyContext.timeLimitSelect);	
		s._('</div>');
		s._('<div class="ui-sub-form"><label class="ui-label">借款说明</label><textarea id="_explain" style="width:290px;height:100px;color:#999999;">')._(modifyContext.explain)._('</textarea></div>');
		s._('<div class="ui-sub-form" style=" padding-top:10px; padding-bottom:10px;">');
		d.addButton(s,{id:'btn-sure',name:'确定修改',css:'btn btn-l',click:'X.dialog.notify(#di#,1);'});
		d.addButton(s,{id:'btn-cel',name:'取消修改',css:'btn btn-l-grey ml20',click:'X.dialog.close(#di#,0);'});
		s._('</div>');
		s._('</div>');
		
		d.open(s.ts(),{topic:'修改方案',width:550,notify:function(nt){
			if(nt==0)return;
			var explain = $.v("_explain");
			if(explain.length>100){
				X.dialog.alert("借款说明字数不能超过100个字",1,{notify:function(){$("#_explain").focus();}});
				return;
			}
			X.form.disableBtn("btn-sure");
			//提交修改方案信息
			X.ajax(ctx.trade+"/schemeOper.html",{action:"updateScheme",schemeId:modifyContext.schemeId,interest:$.v("_interest"),timeLimit:$.v("_timeLimit"),explain:explain},
					function(data){
				data = $.parseJSON(data);
				if(data.success){
					d.close();
					X.dialog.alert("方案修改成功",4,{notify:function(){
						window.location.reload();
					}});
				}else{
					X.form.enableBtn("btn-sure");
					X.dialog.alert(data.resultMsg,3);
				}
			});
		}});
		f.inputR("_explain","给理财人看的，可不填写");
	},
	//删除方案
	cancelScheme:function(){
		var t=this,d=X.dialog,s=new Text();
		d.confirm('您确定要撤销方案吗',2,{notify:function(nt){
			if(nt==1){
				X.form.disableBtn("cancelBtn");
				X.ajax(ctx.trade+"/schemeOper.html",{action:"cancelScheme",schemeId:modifyContext.schemeId},function(data){
					X.form.enableBtn("cancelBtn");
					data = $.parseJSON(data);
					if(data.success){
						X.dialog.alert("方案撤消成功",4,{notify:function(){window.location.reload();}});
					}else{
						X.dialog.alert(data.resultMsg,3);
					}
				});
			}
		}});
	},
	//续约
	renewal:function(schemeId,interest){
		var t=this,d=X.dialog,s=new Text();
		var text=new Text();
		text._('<div style=" line-height:150%;">申请续约后，我们会进行审核，不保证续约一定成功</div>');
		d.addTips(s,{icon:4,info:text.ts()});
		s._('<div class="db-con" style="padding-top:20px;">');
		s._('<div class="ui-sub-form"><label class="ui-label">续约时间</label>');
		var tx2=[['1个月',1]];
		t.addSelect(s,{css:'sel-b w100',id:'renewalCycle'},tx2,2);	
		s._('</div>');
		s._('<div class="ui-sub-form" style="padding-bottom:10px;">');
		d.addButton(s,{id:'renewalSure',name:'确定续约',css:'btn btn-l',click:'X.dialog.notify(#di#,1);'});
		d.addButton(s,{id:'btn-cel',name:'取消续约',css:'btn btn-l-grey ml20',click:'X.dialog.close(#di#,0);'});
		s._('</div>');
		d.open(s.ts(),{topic:'我要续约',width:500,notify:function(nt){
			if(nt==0)return;
			X.form.disableBtn("renewalSure");
			X.ajax(ctx.trade+"/renewal.html",
					{action:"applyRenewal",schemeId:schemeId,cycle:$.v("renewalCycle")},
					function(data){
						data = $.parseJSON(data);
						X.form.enableBtn("renewalSure");
						if(data.success){
							d.close();
							X.dialog.alert("续约申请成功，我们会尽快为您处理",4,{notify:function(){window.location.reload();}});
						}else if(X.code.balanceShortage==data.code){
							X.pay.showCharge(function(balance){
								X.user.loadPageHeader();
							},$.noop,{title:'账户余额不足够支付利息'+interest+'元，请先充值'});
						}else{
							X.dialog.alert(data.resultMsg,1);
						}
					});
		}});
	},
	//提取利润
	profitWithdraw:function(schemeId,money){
		var minMoney = Math.max(Math.floor(money*0.03),100);
		var t=this,d=X.dialog,s=new Text(),text=new Text();
		text._('<div style=" line-height:150%;">提取您投资账户已盈利的资金，且确保这些资金在投资账户里处于可转账状态，且每次提取的盈利金额不能小于当前方案总操盘资金的3%，最低100元。</div>');
		d.addTips(s,{icon:4,info:text.ts()});
		s._('<div class="db-con-1">');
		s._('<div class="ui-sub-form" style="padding-top:20px;"><label class="ui-label">提取金额</label><input type="text" id="withdrawMoney" class="ui-input" style="width:215px"/> <em class="unit">元</em> <em class="tip">最少'+$.formatMoney(minMoney)+'元</em></div>');
		s._('<div class="ui-sub-form" style="padding-top:10px; padding-bottom:10px;">');
		d.addButton(s,{id:'sure',name:'确定提取',css:'btn btn-l',click:'X.dialog.notify(#di#,1);'});
		d.addButton(s,{id:'btn-cel',name:'取消提取',css:'btn btn-l-grey ml10',click:'X.dialog.close(#di#,0);'});
		s._('</div>');
		s._('</div>');
		
		d.open(s.ts(),{topic:'提取利润',width:550,notify:function(nt){
			if(nt==0)return;
			var moneyStr = $.trim($.v("withdrawMoney"));
			if(moneyStr==''){
				X.dialog.alert("请填写提取金额",1,{notify:function(){$("#withdrawMoney").focus();}});
				return;
			}
			if(!/^\d{1,}$/.test(moneyStr)){
				X.dialog.alert("提取金额填写错误，金额必须是整数",1,{notify:function(){$("#withdrawMoney").focus();}});
				return;
			}
			var money = parseInt(moneyStr,10);
			if(money<minMoney){
				X.dialog.alert("利润提取金额最小为"+minMoney+"元",1,{notify:function(){$("#withdrawMoney").focus();}});
				return;
			}
			X.form.disableBtn("sure");
			X.ajax(ctx.trade+"/schemeOper.html",{action:"profitWithdraw",schemeId:schemeId,money:money},function(data){
				data = $.parseJSON(data);
				if(data.success){
					d.close();
					X.dialog.alert("利润提取申请成功",4,{notify:function(){window.location.reload();}});
				}else{
					X.form.enableBtn("sure");
					X.dialog.alert(data.resultMsg,1);
				}
			});
		}});
	},
	//取消提取利润
	celProfitWithdraw:function(id){
		var t=this,d=X.dialog;
		d.confirm('您确定要取消提取利润吗',2,{notify:function(nt){
			if(nt==1){
				X.ajax(ctx.trade+"/schemeOper.html",{action:"cancelProfitWithdraw",id:id},function(data){
					data = $.parseJSON(data);
					if(data.success){
						X.dialog.alert("利润提取取消成功",4,{notify:function(){window.location.reload();}});
					}else{
						X.dialog.alert(data.resultMsg,3);
					}
				});
			}
		}});
	},//保证金追加
	appendPrincipal:function(schemeId,money){
		var minMoney = Math.max(Math.floor(money*0.01),100);
		var t=this,d=X.dialog,s=new Text(),text=new Text();
		text._('<div style=" line-height:150%;">每次追加保证金不能小于当前方案总操盘资金的1%，最低100元，且每天最多追加5次(包括取消次数)。</div>');
		d.addTips(s,{icon:4,info:text.ts()});
		s._('<div class="db-con-1">');
		s._('<div class="ui-sub-form" style="padding-top:20px;"><label class="ui-label">追加金额</label><input type="text" id="addMoney" class="ui-input" style="width:215px"/> <em class="unit">元</em>　<em class="tip">最少'+minMoney+'元</em></div>');
		s._('<div class="ui-sub-form" style="padding-top:10px; padding-bottom:10px;">');
		d.addButton(s,{id:'appendBtn',name:'确定追加',css:'btn btn-l',click:'X.dialog.notify(#di#,1);'});
		d.addButton(s,{id:'btn-cel',name:'取消追加',css:'btn btn-l-grey ml10',click:'X.dialog.close(#di#,0);'});
		s._('</div>');
		s._('</div>');
		
		d.open(s.ts(),{topic:'追加保证金',width:550,notify:function(nt){
			if(nt==0)return;
			var moneyStr = $.trim($.v("addMoney"));
			if(moneyStr==''){
				X.dialog.alert("请填写追加金额",1,{notify:function(){$("#addMoney").focus();}});
				return;
			}
			if(!/^\d{1,}$/.test(moneyStr)){
				X.dialog.alert("追加金额填写错误，金额必须是整数",1,{notify:function(){$("#addMoney").focus();}});
				return;
			}
			var money = parseInt(moneyStr,10);
			if(money<minMoney){
				X.dialog.alert("追加的保证金金额不能小于"+minMoney+"元",1,{notify:function(){$("#addMoney").focus();}});
				return;
			}
			X.form.disableBtn("appendBtn");
			X.ajax(ctx.trade+"/schemeOper.html",{action:"principalAppend",schemeId:schemeId,money:money},function(data){
				X.form.enableBtn("appendBtn");
				data = $.parseJSON(data);
				if(data.success){
					d.close();
					X.dialog.alert("保证金追加申请成功",4,{notify:function(){window.location.reload();}});
				}else if(X.code.balanceShortage==data.code){
					var ps = {};
					if(data.shortage){
						ps.shortage = data.shortage;
					}
					X.pay.showCharge(function(balance){
						X.user.loadPageHeader();
					},$.np,ps);
				}else{
					X.dialog.alert(data.resultMsg,1);
				}
			});
		}});
	},
	//取消保证金追加
	celPrincipalAppend:function(id){
		var t=this,d=X.dialog;
		d.confirm('您确定要取消保证金追加吗',2,{notify:function(nt){
			if(nt==1){
				X.ajax(ctx.trade+"/schemeOper.html",{action:"principalAppendCancel",id:id},function(data){
					data = $.parseJSON(data);
					if(data.success){
						X.dialog.alert("保证金追加取消成功",4,{notify:function(){window.location.reload();}});
					}else{
						X.dialog.alert(data.resultMsg,3);
					}
				});
			}
		}});
	},finishScheme:function(id){
		var t=this,d=X.dialog,s=new Text();
		d.confirm('<div>您确定要申请完结方案吗</div><br/><div class="red">请确保您的交易账户股票已经全部卖出，否则我们将有权把您的股票进行平仓处理（不保证平仓价格）</div>',2,{width:500,notify:function(nt){
			if(nt==1){
				X.ajax(ctx.trade+"/schemeOper.html",{action:"finishApply",schemeId:id},function(data){
					data = $.parseJSON(data);
					if(data.success){
						X.dialog.alert("方案完结申请成功",4,{notify:function(){window.location.reload();}});
					}else{
						X.dialog.alert(data.resultMsg,1);
					}
				});
			}
		}});
	},
	//ps:id name style css,d:key value
	addSelect:function(s,ps,d,seld){
		s._('<select');
		if(ps.id)
			s._(' id="')._(ps.id)._('"');
		if(ps.name)
			s._(' name="')._(ps.name)._('"');
		if(ps.style)
			s._(' style="')._(ps.style)._('"');
		if(ps.css)
			s._(' class="')._(ps.css)._('">');
		for(i=0;i<d.length;i++){
			s._('<option ')._((i==seld?'selected':''))._(' value="')._(d[i][1])._('">')._(d[i][0])._('</option>');
		}
		s._('</select>');
	},limitStock:function(data){
		var t=this,d=X.dialog,s=new Text(),l=data.length,a;
		a=l%4==0?0:4-l%4;
		while(a>0){data.push(["",""]);a--;}
		s._('<div class="limitStock"><ul class="clearfix">');
			for(var i in data){
				s._('<li>'+data[i][0]+'　'+data[i][1]+'</li>');
			}
		s._('</ul></div>');
		s._('<div style="padding-top:20px; text-align:center;"><input id="btn" type="button" onclick="X.dialog.close(#di#);" class="btn btn-l" value="我知道了"></div>');
		d.open(s.ts(),{topic:'今日限制购买的股票',width:740});
	}
}


/**


*/


String.prototype.endsWith = function(suffix) {
    return this.indexOf(suffix, this.length - suffix.length) !== -1;
};

X.valid={
		isNumber:function(data,isPositive){
			return isPositive?/^\d+(\.\d{1,})?$/.test(data)&&parseFloat(data)>0:/^(-)?\d+(\.\d{1,})?$/.test(data);
		},
		isMoney:function(data,isPositive){
			return isPositive?/^\d+(\.\d{1,2})?$/.test(data)&&parseFloat(data)>0:/^(-)?\d+(\.\d{1,2})?$/.test(data);
		},
		isInt:function(data,isPositive){
			return isPositive?/^\d+$/.test(data)&&parseInt(data,10)>0:/^(-)?\d+$/.test(data);
		},
		isIdentityNumber : function(number) {
			if($.trim(number)==''||!/^[0-9]{17}[0-9X]$/.test(number)){
				return false;
			}
			var weights = new Array(7,9,10,5,8,4,2,1,6,3,7,9,10,5,8,4,2);
			var parityBits = new Array("1", "0", "X", "9", "8", "7", "6", "5", "4","3", "2");
			var power = 0;
			for ( var i = 0; i < 17; i++) {
				power += parseInt(number.charAt(i),10)*weights[i];
			}
			return parityBits[power%11]==number.substr(17);
		},
		isMobile:function(mobile){
			return mobile&&/^1[3-9]\d{9}$/.test(mobile);
		},
		isEmail:function(email){
			return email&&/^[0-9a-zA-Z_\-]+@[0-9a-zA-Z_\-]+\.\w{1,5}(\.\w{1,5})?$/.test(email);
		},
		isBankCard:function(cardNumber){
			return cardNumber&&/^\d{16,30}$/.test(cardNumber);
		},
		isChinaName:function(name){
			return name&&$.trim(name).length>=2&&!/^.*\\d{1,}.*$/.test(name);
		},
		isImg:function(filename){
			var imgs = ['.png','.bmp','.jpg','.jpeg','.gif'];
			for(var i=0;i<imgs.length;i++){
				if($.trim(filename).toLowerCase().endsWith(imgs[i]))
					return true;
			}
			return false;
		},
		isPwdValid:function(pwd){
			if($.trim(pwd).length<6){
				return {valid:false,msg:"密码必须由6-16个字符组成"};
			}else{
				if(/^\d+$/.test(pwd)){
					return {valid:false,msg:"密码不能全为数字"};
				}
			}
			return {valid:true,msg:''};
		},
		isUsernameValid:function(username){
			if($.trim(username)==''){
				return {valid:false,msg:'用户名不能为空'};
			}else{
				if($.trim(username).length!=username.length){
					return {valid:false,msg:'用户名不能带有空格'};
				}else if($.strlen(username)>16||$.strlen(username)<4){
					return {valid:false,msg:"4-16个字符，中文算2个字符"};
				}else if(!/^[0-9a-zA-Z_\u4e00-\u9fa5]+$/.test(username)){
					return {valid:false,msg:'4-16位字母、数字、下划线或中文'};
				}else if(X.valid.isMobile(username)){
					return {valid:false,msg:'用户名不能是手机'};
				}else if(X.valid.isEmail(username)){
					return {valid:false,msg:"用户名不能是邮箱"};
				}else if(/.*?(\d+).*?/.test(username)&&X.valid.isMobile(RegExp.$1)){
					return {valid:false,msg:'用户名不能包含手机'};
				}
			}
			return {valid:true,msg:''};
		}
}



/**
	scheme.js
*/


X.uc.trade={
		schemeIngPageJump:function(page,orderBy){
			if('0'==page&&$("#schemeIng").data("loaded")){
				return;
			}else if('-1'==page){
				page = $("#schemeIng input[name='page']").val();
			}
			$.post(ctx.trade+'/user/userScheme.htm',{action:'ing',page:page,pageSize:10,orderBy:orderBy?orderBy:"","__af":"scriptRedirect",entrance:$.winPos()},function(data){
				$("#schemeIng").html(data);
				$("#schemeIng").data("loaded",true);
			},'text');
		},
		schemeCompletePageJump:function(page,orderBy){
			if('0'==page&&$("#schemeComplete").data("loaded")){
				return;
			}else if('-1'==page){
				page = $("#schemeComplete input[name='page']").val();
			}
			$.post(ctx.trade+'/user/userScheme.htm',{action:'complete',page:page,pageSize:10,orderBy:orderBy?orderBy:"","__af":"scriptRedirect",entrance:$.winPos()},function(data){
				$("#schemeComplete").html(data);
				$("#schemeComplete").data("loaded",true);
			},'text');
		},
		schemeMisbirthPageJump:function(page){
			if('0'==page&&$("#schemeMisbirth").data("loaded")){
				return;
			}else if('-1'==page){
				page = $("#schemeMisbirth input[name='page']").val();
			}
			$.post(ctx.trade+'/user/userScheme.htm',{action:'misbirth',page:page,pageSize:10,"__af":"scriptRedirect",entrance:$.winPos()},function(data){
				$("#schemeMisbirth").html(data);
				$("#schemeMisbirth").data("loaded",true);
			},'text');
		},
		everwinIngPageJump:function(page){
			if('0'==page&&$("#everwinIng").data("loaded")){
				return;
			}else if('-1'==page){
				page = $("#everwinIng input[name='page']").val();
			}
			$.post(ctx.trade+'/user/userEverwin.htm',{action:'ing',page:page,pageSize:10,"__af":"scriptRedirect",entrance:$.winPos()},function(data){
				$("#everwinIng").html(data);
				$("#everwinIng").data("loaded",true);
			},'text');
		},
		everwinCompletePageJump:function(page){
			if('0'==page&&$("#everwinComplete").data("loaded")){
				return;
			}else if('-1'==page){
				page = $("#everwinComplete input[name='page']").val();
			}
			$.post(ctx.trade+'/user/userEverwin.htm',{action:'complete',page:page,pageSize:10,"__af":"scriptRedirect",entrance:$.winPos()},function(data){
				$("#everwinComplete").html(data);
				$("#everwinComplete").data("loaded",true);
			},'text');
		},
		everwinMisbirthPageJump:function(page){
			if('0'==page&&$("#everwinMisbirth").data("loaded")){
				return;
			}else if('-1'==page){
				page = $("#everwinMisbirth input[name='page']").val();
			}
			$.post(ctx.trade+'/user/userEverwin.htm',{action:'misbirth',page:page,pageSize:10,"__af":"scriptRedirect",entrance:$.winPos()},function(data){
				$("#everwinMisbirth").html(data);
				$("#everwinMisbirth").data("loaded",true);
			},'text');
		},
		bidLoanPageJump:function(page,orderBy){
			if('0'==page&&$("#bidLoan").data("loaded")){
				return;
			}else if('-1'==page){
				page = $("#bidLoan input[name='page']").val();
			}
			$.post(ctx.trade+'/user/userBid.htm',{action:"loan",page:page,pageSize:10,orderBy:orderBy?orderBy:'interestNetTime',"__af":"scriptRedirect",entrance:$.winPos()},function(data){
				$("#bidLoan").html(data);
				$("#bidLoan").data("loaded",true);
			},'text');
		},
		bidBiddingPageJump:function(page){
			if('0'==page&&$("#bidBidding").data("loaded")){
				return;
			}else if('-1'==page){
				page = $("#bidBidding input[name='page']").val();
			}
			$.post(ctx.trade+'/user/userBid.htm',{action:"bidding",page:page,pageSize:10,"__af":"scriptRedirect",entrance:$.winPos()},function(data){
				$("#bidBidding").html(data);
				$("#bidBidding").data("loaded",true);
			},'text');
		},
		bidCompletePageJump:function(page,orderBy){
			if('0'==page&&$("#bidComplete").data("loaded")){
				return;
			}else if('-1'==page){
				page = $("#bidComplete input[name='page']").val();
			}
			$.post(ctx.trade+'/user/userBid.htm',{action:"complete",orderBy:orderBy?orderBy:"bidTime",page:page,pageSize:10,"__af":"scriptRedirect",entrance:$.winPos()},function(data){
				$("#bidComplete").html(data);
				$("#bidComplete").data("loaded",true);
			},'text');
		},
		bidMissPageJump:function(page){
			if('0'==page&&$("#bidMiss").data("loaded")){
				return;
			}else if('-1'==page){
				page = $("#bidMiss input[name='page']").val();
			}
			$.post(ctx.trade+'/user/userBid.htm',{action:"miss",page:page,pageSize:10,"__af":"scriptRedirect",entrance:$.winPos()},function(data){
				$("#bidMiss").html(data);
				$("#bidMiss").data("loaded",true);
			},'text');
		},
		transferIngPageJump:function(page){
			if('0'==page&&$("#transferIng").data("loaded")){
				return;
			}else if('-1'==page){
				page = $("#transferIng input[name='page']").val();
			}
			$.post(ctx.trade+'/user/userTransfer.htm',{action:'ing',page:page,pageSize:10,"__af":"scriptRedirect",entrance:$.winPos()},function(data){
				$("#transferIng").html(data);
				$("#transferIng").data("loaded",true);
			},'text');
		},
		transferAblePageJump:function(page){
			if('0'==page&&$("#transferAble").data("loaded")){
				return;
			}else if('-1'==page){
				page = $("#transferAble input[name='page']").val();
			}
			$.post(ctx.trade+'/user/userTransfer.htm',{action:'able',page:page,pageSize:10,"__af":"scriptRedirect",entrance:$.winPos()},function(data){
				$("#transferAble").html(data);
				$("#transferAble").data("loaded",true);
			},'text');
		},
		transferOutPageJump:function(page){
			if('0'==page&&$("#transferOut").data("loaded")){
				return;
			}else if('-1'==page){
				page = $("#transferOut input[name='page']").val();
			}
			$.post(ctx.trade+'/user/userTransfer.htm',{action:'out',page:page,pageSize:10,"__af":"scriptRedirect",entrance:$.winPos()},function(data){
				$("#transferOut").html(data);
				$("#transferOut").data("loaded",true);
			},'text');
		},
		transferInPageJump:function(page){
			if('0'==page&&$("#transferIn").data("loaded")){
				return;
			}else if('-1'==page){
				page = $("#transferIn input[name='page']").val();
			}
			$.post(ctx.trade+'/user/userTransfer.htm',{action:'in',page:page,pageSize:10,"__af":"scriptRedirect",entrance:$.winPos()},function(data){
				$("#transferIn").html(data);
				$("#transferIn").data("loaded",true);
			},'text');
		},
		/**
		 * 检查并更新自动投标
		 */
		checkAndUpateAutoBid:function(param){
			
			if(param.bidMoneyMin==''){
				X.dialog.alert("请填写投标最小金额",1,{notify:function(){$("#bidMoneyMin").focus();}});
				return;
			}
			if(!X.valid.isInt(param.bidMoneyMin,true)){
				X.dialog.alert("投标最小金额必须大于等于1元",1);
				return false;
			}
			
			if(param.bidMoneyMax==''){
				X.dialog.alert("请填写投标最大金额",1,{notify:function(){$("#bidMoneyMax").focus();}});
				return;
			}
			
			if(!X.valid.isInt(param.bidMoneyMax,true)){
				X.dialog.alert("投标最大金额必须大于等于1元",1,{notify:function(){$("#bidMoneyMax").focus();}});
				return false;
			}
			
			var maxMoney = parseInt(param.bidMoneyMax,10);
			var minMoney = parseInt(param.bidMoneyMin,10);
			if(minMoney>maxMoney){
				X.dialog.alert("投标最大金额不能小于最小金额",1,{notify:function(){$("#bidMoneyMax").focus();}});
				return false;
			}
			
			var maxMode = maxMoney-maxMoney%100;
			if(maxMode<100){
				X.dialog.alert("投标金额将会是100的整数倍，您选择的范围无法投标",1);
				return false;
			}
			var minMode = minMoney%100==0?minMoney:(minMoney+(100-minMoney%100));
			if(minMode>maxMoney){
				X.dialog.alert("投标金额将会是100的整数倍，您选择的范围无法投标",1);
				return false;
			}
			
			if(param.remain==''){
				X.dialog.alert("请填写账户保留金额",1,{notify:function(){$("#remain").focus();}});
				return false;
			}
			if(!X.valid.isInt(param.remain)||parseInt(param.remain)<0){
				X.dialog.alert("账户保留金额必须是大于等于0的整数",1);
				return false;
			}
			
			if(!X.valid.isInt(param.minInterest,true)){
				X.dialog.alert("最小年化利率必须是大于0的整数",1);
				return false;
			}
			if(!X.valid.isInt(param.maxInterest,true)){
				X.dialog.alert("最大年化利率必须是大于0的整数",1);
				return false;
			}
			
			if(parseInt(param.minInterest,10)>parseInt(param.maxInterest,10)){
				X.dialog.alert("最小年化利率不能大于最大年化利率",1);
				return false;
			}
			
			
			if(!X.valid.isInt(param.minCycle,true)){
				X.dialog.alert("最小借款期限必须是大于0的整数",1);
				return false;
			}
			if(!X.valid.isInt(param.maxCycle,true)){
				X.dialog.alert("最大借款期限必须是大于0的整数",1);
				return false;
			}
			if(parseInt(param.minCycle,10)>parseInt(param.maxCycle,10)){
				X.dialog.alert("最小借款期限必须小于等于借款期限",1);
				return false;
			}
			if(!param.agree){
				X.dialog.alert("请先阅读并同意签署《借款协议》《债权转让协议》",1);
				return false;
			}
			
			X.form.disableBtn("save");
			X.ajax(ctx.trade+"/user/autoBidOper.html",param,function(data){
				data = $.parseJSON(data);
				if(data.success){
					window.location.href=ctx.trade+"/user/autoBid.html?action=info";
				}else{
					X.form.enableBtn("save");
					X.dialog.alert(data.resultMsg,1);
				}
			});
		},
		doAutoBidOn:function(){
			X.ajax(ctx.trade+"/user/autoBidOper.html",
					{action:"updateValid",valid:"true"},
					function(data){
						data = $.parseJSON(data);
						if(data.success){
							X.dialog.alert("自动理财启用成功",4,{notify:function(){window.location.reload();}})
						}else{
							if(X.code.nameUnsettled==data.code){
								X.uc.user.legalizeID(false,function(){X.uc.trade.doAutoBidOn();});
							}else{
								X.dialog.alert(data.resultMsg,1);
							}
						}
					});
		},
		autoBidOn:function(){
			X.dialog.confirm('您确定要启用自动理财吗',2,{notify:function(nt){
				if(nt==1){
					X.uc.trade.doAutoBidOn();
				}
			}});
		},
		autoBidOff:function(){
			X.dialog.confirm('您确定要暂停自动理财吗',2,{notify:function(nt){
				if(nt==1){
					X.ajax(ctx.trade+"/user/autoBidOper.html",
							{action:"updateValid",valid:"false"},
							function(data){
								data = $.parseJSON(data);
								if(data.success){
									X.dialog.alert("自动理财暂停成功",4,{notify:function(){window.location.reload();}})
								}else{
									X.dialog.alert(data.resultMsg,1);
								}
							});
				}
			}});
		},
		pzMustRead:function(){
			
			var t=this,d=X.dialog,f=X.form,s=new Text(),r='请输入手机号或邮箱',r1='请填写留言内容';
			
			s._('<table width="100%" class="mustread">');
			s._('<tr><td width="150" vlaign="middle" align="right">股票交易规则</td><td><div style="line-height:150%;">1、不得购买S、ST、*ST、S*ST、SST、以及被交易所特别处理的股票；<br>');
			s._('2、不得购买权证类可以T+0交易的证券；<br>');
			s._('3、不得购买首日上市新股（或复牌首日股票）等当日不设涨跌停板限制的股票；<br>');
			s._('4、借款金额100万或以上主板单只股票不得超过账户总资产的50%（100万以下不受限制）；<br>');
			s._('5、借款金额100万或以上创业板单只股票不得超过账户总资产的30%（100万以下不受限制）；<br>');
			s._('6、单只股票持仓总市值不得超过该股前5个交易日日均成交额的30%；<br>');
			s._('7、不得进行坐庄、对敲、接盘、大宗交易、内幕信息等违反股票交易法律法规及证券公司规定的交易；<br>');
			s._('违背以上任一协定，我们将有权以任何可以成交的价格完全卖出违反协定之类别股票。');
			s._('</div></td></tr>');
			s._('<tr><td vlaign="middle" align="right" rowspan="2">账户资金亏损警戒线</td><td>借款的110%，例如：20万本金+100万借款 = 100万借款*110% = 警戒线是110万</td></tr>');
			s._('<tr><td><div style="line-height:150%;">当账户总资产到警戒线以下时，只能平仓不能建仓，必须在次日上午10点前将本金补到警戒线之上，否则我们将有权把您的股票减仓到剩余本金的6倍。</div></td></tr>');
			s._('<tr><td vlaign="middle" align="right" rowspan="2">账户资金亏损平仓线</td><td>借款的107%，例如：20万本金+100万借款 = 100万借款*107% = 平仓线是107万</td></tr>');
			s._('<tr><td>当账户总资产到平仓线以下时，我们将有权把您的股票进行平仓处理。</td></tr>');
			s._('<tr><td vlaign="middle" align="right">借款到期处理</td><td><div style="line-height:150%;">借款到期前一个交易日，应将账户内总资产全部变现为货币资金（卖出全部股票），借款期满当日，我们将账户内资产进行清算。当然您也可以在快到期的最后7天内，通过申请续约来延长借款时间。</div></td></tr>');
			s._('</table>');
			s._('<div style="text-align:center;line-height:100%;padding-top:20px;;">以上内容来自借款协议，更多内容请阅读<a href="javascript:X.uc.trade.showBorrowContract()">《借款协议》</a></div>');
			s._('<div style="padding-top:30px; padding-bottom:10px; text-align:center;"><input id="btn" type="button" onclick="X.dialog.notify(#di#,1);" class="btn btn-l" dis-text="处理中..." value="确定"></div>');
			d.open(s.ts(),{topic:'配资交易前必读',width:870,notify:function(nt){
				if(nt!=1){return;}												 
				d.close();
			}});
		},
		showBorrowContract:function(){
			window.open('/trade/borrowContract.html','借款协议','height=800,width=1000,top=0,left=200,toolbar=no,menubar=no,scrollbars=yes, resizable=no,location=no, status=no');
		},
		showTransferContract:function(){
			window.open('/trade/transferContract.html','债权转让协议','height=800,width=1000,top=0,left=200,toolbar=no,menubar=no,scrollbars=yes, resizable=no,location=no, status=no')
		},
		showEverWinContract:function(){
			window.open('/trade/everwinContract.html','合作操盘协议','height=800,width=1000,top=0,left=200,toolbar=no,menubar=no,scrollbars=yes, resizable=no,location=no, status=no');
		}
}


