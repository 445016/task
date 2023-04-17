<?php 
	global $_W,$_GPC;
	$op = isset($_GPC['op'])?$_GPC['op']:'list';
	model_user::wInit();
	
	
	if( $_W['role'] == 'operator' ){
		die;
	}
	
	
	$info = pdo_get('zofui_tasktb_vauth',array('uniacid'=>$_W['uniacid']));
	$info['params'] = iunserializer($info['params']);
	
	$params = array();
	if( !empty($info['params']) ){
		foreach ($info['params'] as $k => $v) {
			$params[] = $k;
		}
	}
	

	include $this->template('web/'.$_W['mtemp'].'/vauth');