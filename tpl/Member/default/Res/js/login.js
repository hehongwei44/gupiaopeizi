$(function(){
	$('#mobile').focus();
	$('.codeimg').click(function(){
		$('.yzmPic').attr('src','/member/authcode.html#'+new Date().getTime());
	});
	$('#login').click(function(){
		if(!$('#username').val()){
			$('#username').focus();
			$('#un').html('帐号不能为空');
		}else{
			$('#un').html('');
		}
		if(!$('#password').val()){
			$('#up').html('密码不能为空');
		}else{
			$('#up').html('');
		}
		if(!$('#authcode').val()){
			$('#ua').html('验证码不能为空');
		}else{
			$('#ua').html('');
		}
		if($('#un').html()==''&&$('#up').html()==''){
			$.post("/member/login.html", $("#loginForm").serialize(),function(res){
				if(res.status>0){
					//if(res.data=='ua'){
						$('.yzmPic').attr('src','/member/authcode.html#'+new Date().getTime());
					//}
					$('.authcode').show();
					$('#'+res.data).html(res.info);
					
				}else{
					window.location=$('#from').val();
				}
				
			},'json');
		}			
	});

	$('#name').blur(function(){
		if($('#name').val().length<6||$('#name').val().length>20){
			$('#iu').html($('#iu').attr('data'));
			$('#iu').attr('class','error');
		}else{
			$('#iu').attr('class','pass');
			$('#iu').html('通过');				
		}
	});
	$('#mobile').blur(function(){
		if(!$('#mobile').val()||$('#mobile').val().length!=11||isNaN($('#mobile').val())){
			$('#im').html($('#im').attr('data'));
			$('#im').attr('class','error');
		}else{
			$('#im').attr('class','pass');
			$('#im').html('通过');				
		}
	});
	$('#password').blur(function(){
		if(!$('#password').val()||$('#password').val().length<6||$('#password').val().length>20){
			$('#ip').html($('#ip').attr('data'));
			$('#ip').attr('class','error');
		}else{
			$('#ip').attr('class','pass');
			$('#ip').html('通过');								
		}
	});

	$('#confirm').blur(function(){
		if($('#password').val()!=$('#confirm').val()){
			$('#ic').html($('#ic').attr('data'));
			$('#ic').attr('class','error');
		}else{
			$('#ic').attr('class','pass');
			$('#ic').html('通过');	
		}
	});

	$('#invitor').blur(function(){
		if($('#invitor').val()){
			if($('#invitor').val().length!=32){
				$('#ii').html($('#ii').attr('data'));
				$('#ii').attr('class','error');
			}else{
				$('#ii').attr('class','pass');
				$('#ii').html('通过');				
			}
		}else{
			$('#ii').attr('class','tip');
			$('#ii').html('非推荐注册可不填');
		}
	});
	$('#smscode').blur(function(){
		if($('#smscode').val()!=''&&$('#smscode').val().length!=4){
			$('#ia').html($('#is').attr('data'));
			$('#ia').attr('class','error');
		}else{
			$('#ia').attr('class','pass');
			$('#ia').html('通过');				
		}
	});
	$('#register').click(function(){
		if($('.error').size()==0){
			$.post("/member/register.html", $("#registerForm").serialize(),function(res){
				if(res.status>0){
					$('#'+res.data).html(res.info);
					$('#'+res.data).attr('class','error');						
				}else{
					var _mvq = window._mvq || []; 
					window._mvq = _mvq;
					_mvq.push(['$setAccount', 'm-145312-0']);
					
					_mvq.push(['$setGeneral', 'registered', '', /*用户名*/ $("#name").val(), /*用户id*/ $("#mobile").val()]);
					_mvq.push(['$logConversion']);
					window.location='/member/';
				}
				
			},'json');
		}
	});
});
$(document).keypress(function(e) {
	if(e.which == 13) { 
		$('#login').click();
		$('#register').click();
	}
}); 