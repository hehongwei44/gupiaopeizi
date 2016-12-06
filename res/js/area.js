//地区
function GetAreaSelect(provice,city,district,pid,cid,did){
	var o = new Object();
	o.provice 	= provice;
	o.city	 	= city;
	o.district 	= district;
	o.defaultPid 	= pid||0;
	o.defaultCid	= cid||0;
	o.defaultDid 	= did||0;
	o.changed	= null;
	
	o.bind = function(){
		$(o.provice).change(function(){
			pid = $(o.provice).val();
			o.changed = 'p';
			o.getAlist(pid);
		});
		$(o.city).change(function(){
			cid = $(o.city).val();
			o.changed = 'c';
			o.getAlist(cid);
		});
		//有默认地区 只能以第一位一步一步触发，不然一起执行会导致o.changed值在上一步还没执行完就立即改变，导致出错
		if(o.defaultPid>0){
			o.changed = 'dp';
			o.getAlist(1);//初始化的时候省份上级ID默认为1,因为是调用省份本身，上一级是中国
		}
	};
	o.getAlist=function(rid){
		var p={"rid":rid};
        $.ajax({
            url: areaurl,
            data: p,
            timeout: 5000,
            cache: false,
            type: "get",
            dataType: "json",
            success: function (d, s, r) {
                if(d) o.displayA(d);//更新客户端竞拍商品信息 作个判断，避免报错
            }
		})
	};
	o.displayA=function(d){
		//初始化
		if(o.changed=='dp'){
			$(o.provice).empty();
			$(d.option).appendTo(o.provice);
			
			$(o.provice+" option[value='"+o.defaultPid+"']").find("option:selected").removeAttr("selected");
			$(o.provice+" option[value='"+o.defaultPid+"']").attr("selected","selected");
			//省份加载完后加载城市
			o.changed = 'dc';
			o.getAlist(o.defaultPid);//调用当前省份的下级才是当前市的同级
		}else if(o.changed=='dc'){
			$(o.city).empty();
			$(d.option).appendTo(o.city);

			$(o.city+" option[value='"+o.defaultCid+"']").find("option:selected").removeAttr("selected");
			$(o.city+" option[value='"+o.defaultCid+"']").attr("selected","selected");
			//城市加载完后加载地区
			o.changed = 'dd';
			o.getAlist(o.defaultCid);//调用当前市的下级才是当前区的同级

		}else if(o.changed=='dd'){
			$(o.district).empty();
			$(d.option).appendTo(o.district);

			$(o.district+" option[value='"+o.defaultDid+"']").find("option:selected").removeAttr("selected");
			$(o.district+" option[value='"+o.defaultDid+"']").attr("selected","selected");
		}
		//省份变动
		else if(o.changed=='p'){
			d.option = "<option value=''>--请选择城市--</option>\r\n"+d.option;
			$(o.city).empty();
			$(d.option).appendTo(o.city);
			
			$(o.district).empty();
			$("<option value=''>--请先选择城市--</option>\r\n").appendTo(o.district);
		}else if(o.changed=='c'){
			d.option = "<option value=''>--请选择地区--</option>\r\n"+d.option;
			$(o.district).empty();
			$(d.option).appendTo(o.district);
		}
	};
	o.bind(o.provice,o.city,o.district);
}
