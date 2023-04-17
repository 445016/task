<?php 
	global $_W,$_GPC;
	$_GPC['do'] = 'mess';
	$userinfo = model_user::initUserInfo();
	
	$guysort = model_guysort::getSort(); // 任务分类
	
	
	$set = model_mess::getMess( $_W['openid'] );
	

	$sharetitle = str_replace('{nick}', $userinfo['nickname'], $this->module['config']['sharetitle']);
	$sharedesc = str_replace('{nick}', $userinfo['nickname'], $this->module['config']['sharedesc']);
	$settings = array(
		'sharetitle' => $sharetitle,
		'sharedesc' => $sharedesc,
		'shareimg' => tomedia($this->module['config']['shareimg']),
		'sharelink' => Util::createModuleUrl('mess',array('zfuid'=>$userinfo['id'])),
		'do' => 'mess',
		'title' => '通知设置',
		'topexplain' => 0,
		'open'=>$_GPC['open'],
	);

	include $this->template ('mess');
