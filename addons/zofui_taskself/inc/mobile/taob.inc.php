<?php 
	global $_W,$_GPC;
	$_GPC['do'] = 'taob';
	
	if( empty( $_GPC['op'] ) ){
		$key = rawurlencode( $_GPC['key'] );
		
		$url = 'http://s.m.taobao.com/h5?q='.$key.'&nid='.$_GPC['gid'].'&closeP4P=true';

		if( !empty( $this->module['config']['kaurl'] ) ){
			$url = str_replace(array('{key}','{gid}'), array($key,$_GPC['gid']), $this->module['config']['kaurl']);
		}

	}elseif( $_GPC['op'] == 'usetask' ){
		$url = $_GPC['url'];

	}elseif( $_GPC['op'] == 'usefindkey' ){
		$key = rawurlencode( $_GPC['key'] );

		$url = 'http://s.m.taobao.com/h5?q='.$key.'&nid='.$_GPC['gid'].'&closeP4P=true';

		if( !empty( $this->module['config']['usetourl'] ) ){
			$url = str_replace(array('{key}','{gid}'), array($key,$_GPC['gid']), $this->module['config']['usetourl']);
		}

	}


	
	
	$iswechat = model_user::isWechat();
	$agent = model_user::agent();
	
	//$iswechat = true;
	//$$agent = 'a';

	if( $iswechat ){
		include $this->template ('taob');
	}else{
		header( "Location: ".$url );
		exit();
	}

	