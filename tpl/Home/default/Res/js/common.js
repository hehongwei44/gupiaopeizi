var dlg;
$(document).ready(function(){
	$(".rankingList").each(function(){$(this).find(".nember:lt(3)").addClass("top3");});
});

function slide(obj,dir){
	var _wrap=$(obj);var _direct=dir;var _interval=3000;var _moving;_wrap.hover(function(){clearInterval(_moving)},function(){_moving=setInterval(function(){var _field=_wrap.find('li:first');var _h=_field.height();var _w=_field.width();if(_direct=='Top'){_d={marginTop:-_h+'px'}}else{_d={marginLeft:-_w+'px'}}_field.animate(_d,300,function(){_field.css('margin'+_direct,0).appendTo(_wrap)})},_interval)}).trigger('mouseleave');
}
function dialog(content,title,onclose){
    if(!title){
    	title = '提示：';
    	content='<div class="alert">'+content+'</div>';
	}else{
		if(title=='error'||title=='success'||title=='alert'){
			content='<div class="'+title+'">'+content+'</div>';
			title = '操作结果：';
		}
	}
	
	dlg = new jBox('Modal', {onClose:onclose,closeOnClick: false,blockScroll: false,minWidth: 250,minHeight: 80,title: title,content: content,closeButton: 'title',animation: 'pulse',overlay: true}).open();
}
