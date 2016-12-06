$(function(){
    $('a[in="pop"]').click(function(){
    	title = !$(this).attr('title') ? $(this).html() : $(this).attr('title');
        $.post($(this).attr('href'),{data:$(this).attr('data')},function(res){
            top.dialog(res,title);                
        },'html');            
        return false;
    });

    var rs = 60;
    $('.smscode').click(function(){
    	if($('#mobile').val().length!=11){
    		top.dialog('请输入正确的手机号！');
    		return false;
    	}
        if ($(this).attr('data')=='bind') {
            bind = 'true';
        }else{
            bind = 'false';
        }
        $(this).val('重新发送('+rs+')');
        var int = setInterval(function(){
            rs--;
            if(rs<1){
                rs = 60;
                clearInterval(int);
                $('.smscode').val('发送验证码');
                $('.smscode').removeAttr('disabled');
            }else{
                $('.smscode').attr('disabled',true);
                $('.smscode').val('重新发送('+rs+')');
            }            
        },1000);
		$.post('/member/sendsms.html',{mobile:$('#mobile').val(),bind:bind},function(ret){
            if(ret.status>0){
                $('.smscode').val('发送验证码');
                $('#'+ret.data).html(ret.info);
                $('.smscode').attr('disabled',false);
                clearInterval(int);
            }
		},'json');     

    });
});