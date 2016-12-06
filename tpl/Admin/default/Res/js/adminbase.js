//字符串长度-中文和全角符号为1，英文、数字和半角为0.5
//鼠标移动表格效果
$(document).ready(function(){
	$("tr[overstyle='on']").hover(
	  function () {
		$(this).addClass("bg_hover");
	  },
	  function () {
		$(this).removeClass("bg_hover");
	  }
	);
});

function checkon(o){
	if( o.checked == true ){
		$(o).parents('tr').addClass('bg_on') ;
	}else{
		$(o).parents('tr').removeClass('bg_on') ;
	}
}

function checkAll(o){
	if( o.checked == true ){
		$('input[name="checkbox"]').attr('checked','true');
		$('tr[overstyle="on"]').addClass("bg_on");
	}else{
		$('input[name="checkbox"]').removeAttr('checked');
		$('tr[overstyle="on"]').removeClass("bg_on");
	}
}

//获取已选择用户的ID数组
function getChecked() {
	var gids = new Array();
	$.each($('input:checked'), function(i, n){
		if($(n).val()!=0) gids.push( $(n).val() );
	});
	return gids;
}
//鼠标移动表格效果----------END-------------
//删除及影响
function del(aid) {
	if(!confirm("删除后不可恢复，确定要删除吗?")) return;
	aid = aid ? aid : getChecked();
	aid = aid.toString();
	if(aid == '') return false;

	//提交修改
	var datas = {'idarr':aid};
	$.post(delUrl, datas, delResponse,'json');
}

function delResponse(res){
	if(res.status == '0') {
		ui.error(res.info);
	}else {
		aid = res.data.split(',');
		$.each(aid, function(i,n){
			$('#list_'+n).remove();
		});
		ui.success(res.info);
	}
}	
//删除及影响--------------END----------
//ajax添加
function add(){
	ui.box.load(addUrl, {title:addTitle});
}
//ajax编辑
function edit(url_arg){
	ui.box.load(editUrl+url_arg, {title:editTitle});
}
//tab切换
$(".page_tab span").bind("click", function(){
		var tab = $(".page_tab span");
		$.each(tab, function(i,n){
			var tid = $(n).attr('data');
			$(n).removeClass('active');
			$("#"+tid).hide();
		});
		var current = $(this).attr('data');
		$(this).addClass('active');
		$("#"+current).show();
}); 
//跳转
function goto(url){
	window.location.href=url;
}

//跳转
function loadUser(id,uname){
	ui.box.load("/admin/common/member?id="+id, {title:uname+" 详细信息"});
}

//搜索
function dosearch() {
	if(isSearchHidden == 1) {
		$("#search_div").slideDown("fast");
		$(".search_action").html("搜索完毕");
		isSearchHidden = 0;
	}else {
		$("#search_div").slideUp("fast");
		$(".search_action").html();
		isSearchHidden = 1;
	}
}

function folder(type, _this) {
	$('#search_'+type).slideToggle('fast');
	if ($(_this).html() == '展开') {
		$(_this).html('收起');
	}else {
		$(_this).html('展开');
	}
	
}
//返回数字
function NumberCheck(t){
	var num = t.value;
	var re=/^\d+\.?\d*$/;
	if(!re.test(num)){
		isNaN(parseFloat(num))?t.value=0:t.value=parseFloat(num);
	}
}
/*****************************新增提现编辑加弹出框信息功能 添加人：fanyelei 添加时间：2012-12-02 08:50**********************/
function loadTixian(id,uname){
	ui.box.load("/admin/withdraw/audit?id="+id, {title:"提现申请审核"});
}
function loadTixianwait(id,uname){
	ui.box.load("/admin/withdraw/edit?id="+id, {title:"提现申请审核"});
}
function loadTixianing(id,uname){
	ui.box.load("/admin/withdraw/process?id="+id, {title:"提现申请审核"});
}
/*****************************新增加提现编辑弹出框信息功能 添加人：fanyelei 添加时间：2012-12-02 08:50**********************/

function is_vouch_do(){
	var b_type = $(":input[name='borrow_type']:checked").val();
	if(b_type==2){
		$("#danbaojigou").slideDown();
	}else{
		$("#danbaojigou").slideUp();
		$("#danbao")[0].selectedIndex = 0;
		$("#vouch_money").val(0);
	}
}