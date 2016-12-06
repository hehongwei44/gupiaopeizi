<style type="text/css">
	#think_page_trace{position: fixed;bottom:0;right:0;font-size:14px;width:100%;z-index: 999999;color: #000;text-align:left;font-family:'微软雅黑'}
	#think_page_trace .red{color: #fc0}
	#think_page_trace_tab{display: none;background:white;margin:0;height: 250px}
	#think_page_trace_tab_tit{height:30px;padding: 6px 12px 0;border-bottom:1px solid #ececec;border-top:1px solid #ececec;font-size:16px}
	#think_page_trace_tab_tit span{color:#000;padding-right:12px;height:30px;line-height: 30px;display:inline-block;margin-right:3px;cursor: pointer;font-weight:700}
	#think_page_trace_tab_cont{overflow:auto;height:212px;padding: 0; line-height: 24px}
	#think_page_trace_tab_cont div{display:none}
	#think_page_trace_tab_cont ol{padding: 0; margin:0}
	#think_page_trace_tab_cont ol li{border-bottom:1px solid #EEE;font-size:14px;padding:0 12px}
	#think_page_trace_close{display: block; text-align: right; height: 15px; position: absolute; top: 10px; right: 12px; cursor: pointer}
	#think_page_trace_close img{vertical-align:top}
	#think_page_trace_open{height:30px;float:right;text-align: right;overflow:hidden;position:fixed;bottom:0;right:0;color:#000;line-height:30px;cursor:pointer}
	#think_page_trace_open div{background:#232323;color:#FFF;padding:0 6px;float:right;line-height:30px;font-size:14px}
</style>
<div id="think_page_trace">
	<div id="think_page_trace_tab">
		<div id="think_page_trace_tab_tit">
		<?php foreach($trace as $key => $value){ ?>
			<span><?php echo $key ?></span>
		<?php } ?>
		</div>
	<div id="think_page_trace_tab_cont">
	<?php foreach($trace as $info) { ?>
		<div>
			<ol>
			<?php 
			if(is_array($info)){
				foreach ($info as $k=>$val){
		echo '<li>' . (is_numeric($k) ? '' : $k.' : ') . htmlentities($val,ENT_COMPAT,'utf-8') .'</li>';
				}
			}
			?>
			</ol>
		</div>
	<?php } ?>
	</div>
</div>
<div id="think_page_trace_close"><img src="<?php echo $close_btn_img ?>" /></div>
<div id="think_page_trace_open">
	<div>
		<?php echo G('beginTime','viewEndTime').'s ';?>
	</div>
</div>
<script type="text/javascript">
	(function(){
		var tab_tit  = document.getElementById('think_page_trace_tab_tit').getElementsByTagName('span');
		var tab_cont = document.getElementById('think_page_trace_tab_cont').getElementsByTagName('div');
		var open     = document.getElementById('think_page_trace_open');
		var close    = document.getElementById('think_page_trace_close').childNodes[0];
		var trace    = document.getElementById('think_page_trace_tab');
		var cookie   = document.cookie.match(/thinkphp_show_page_trace=(\d\|\d)/);
		var history  = (cookie && typeof cookie[1] != 'undefined' && cookie[1].split('|')) || [0,0];
		open.onclick = function(){
			trace.style.display = 'block';
			this.style.display = 'none';
			close.parentNode.style.display = 'block';
			history[0] = 1;
			document.cookie = 'thinkphp_show_page_trace='+history.join('|')
		}
		close.onclick = function(){
			trace.style.display = 'none';
		this.parentNode.style.display = 'none';
			open.style.display = 'block';
			history[0] = 0;
			document.cookie = 'thinkphp_show_page_trace='+history.join('|')
		}
		for(var i = 0; i < tab_tit.length; i++){
			tab_tit[i].onclick = (function(i){
				return function(){
					for(var j = 0; j < tab_cont.length; j++){
						tab_cont[j].style.display = 'none';
						tab_tit[j].style.color = '#999';
					}
					tab_cont[i].style.display = 'block';
					tab_tit[i].style.color = '#000';
					history[1] = i;
					document.cookie = 'thinkphp_show_page_trace='+history.join('|')
				}
			})(i)
		}
		parseInt(history[0]) && open.click();
		tab_tit[history[1]].click();
	})();	
</script>