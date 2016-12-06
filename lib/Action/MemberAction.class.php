<?php
class MemberAction extends Action{
	var $data=NULL;
	var $param=NULL;
	//获取公共数据
	function _initialize(){
		$allow = array('login','alogin','register','forget','authcode','sendsms');
		if($_SESSION['MEMBER']['ID']==''&&!in_array(ACTION_NAME,$allow)){
			header('location:/member/login.html');
			exit;
		}

		//全局变量
		$param = get_global_setting();

		//导航
		if(S('nav')){
			$param['nav'] = S('nav');
		}else{
	    	$data=D('Navigation')->field('`id`,`type_name` `name`,`type_url` `url`,`parent_id` `parent`,`url_target` `target`')->where('is_hiden="0"')->order('sort_order DESC')->select();
			foreach($data as $val){
				$param['nav'][$val['parent']][] = $val;
			}
			S('nav',$param['nav'],86400);
		}

		foreach($param['nav'][0] as $key => $val){
			$nav .= is_array($param['nav'][$val['id']])&&$val['id']!=8&&$val['id']!=9?'<li class="sub">':'<li>';
			$uri = explode('/',$_SERVER['REQUEST_URI']);
			$now = explode('/',$val['url']);
			$class = $now[1]==$uri[1]?'class="now"':'';
			$nav .= '<a href="'.$val['url'].'" '.$class.' target="'.$val['target'].'">'.$val['name'].'</a>';
			$sub = $param['nav'][$val['id']];
			if(is_array($sub)&&$val['id']!=8&&$val['id']!=9){
				$nav .= '<p>';
				foreach($sub as $item){
					$nav .= '<a href="'.$item['url'].'" target="'.$item['target'].'">'.$item['name'].'</a>';
				}
				$nav .= '</p>';
			}
			$nav .= '</li>';
		}
		$this->param = $param;
		$this->param['nav'] = $nav;

		//空数据处理
		$this->assign('empty','<div style="text-align:center;padding:50px">暂时没有符合要求的数据</div>');

        if (method_exists($this, 'init')) {
            $this->init();
        }



        $this->setAd();
	}

	function setAd($id){
		$map['status'] = '1';
		$map['start'] = array('lt',time());
		$map['expire'] = array('gt',time());
		$url[] = '';
		$url[] = ''.substr($_SERVER['REQUEST_URI'],0,strrpos($_SERVER['REQUEST_URI'],'/')).'/';
		$url[] = ''.$_SERVER['REQUEST_URI'].'';
		$map['url']  = array('in',implode(',',array_unique($url)));


		$data = D('Ad')->where($map)->order('FIELD(`url`,"'.implode('","',array_unique($url)).'")')->select();
		foreach($data as $val){
			if($val['type']=='1'||$val['type']=='2'){
				$class = 'ad-'.$val['position'];
			}else{
				$class = '';
			}
			$ads = explode('|',$val['content']);
			$list = array();

			foreach($ads as $item){
				$list[] = explode(',',$item);
				if($val['type']=='1'||$val['type']=='2'){
					$html = '<ul class="'.$class.' adSlider">';
				}else{
					$html = '';
				}
				foreach($list as $img){
					if($val['type']=='1'||$val['type']=='2'){
						$html .= '<li>';
					}

					$html .= '<a href="'.($img[2]==''?'#nogo':$img[2]).'"><img class="adImg" src="'.$img[0].'" alt="'.$img[1].'"></a>';
					if($val['type']=='1'||$val['type']=='2'){
						$html .= '</li>';
					}
				}
				if($val['type']=='1'){
					$html .= '</ul><script>slide(".'.$class.'","Left");</script>';
				}
				if($val['type']=='2'){
					$html .= '</ul><script>slide(".'.$class.'","Top");</script>';
				}
			}
			switch($val['type']){
				case '8':
				$data[$val['position']] = $val['content'];
				break;
				case '9':
				$data[$val['position']] = $list;
				break;
				default:
				$data[$val['position']] = $html;
				break;
			}
		}

		$this->param['ad'] = $data;
	}

	//请求的方法不存在
	function unknown(){
		$html = $this->fetch(ACTION_NAME);
		if(!$html){
			if(APP_DEBUG){
				throw_exception('非法操作 '.ACTION_NAME);
			}else{
				send_http_status(404);
				require(APP_ROOT.'/'.C('ERROR_PAGE'));
				exit;
			}
		}
		echo $html;
	}

	//请求的数据不存在
	function blanks(){

	}

}
?>