<?php 
	global $_W,$_GPC;
	$_GPC['do'] = 'activity';
	$userinfo = model_user::initUserInfo();
	
	
	
	
	// 分享
	$sharetitle = str_replace('{nick}', $userinfo['nickname'], $this->module['config']['sharetitle']);
	$sharedesc = str_replace('{nick}', $userinfo['nickname'], $this->module['config']['sharedesc']);
	$settings = array(
		'sharetitle' => $sharetitle,
		'sharedesc' => $sharedesc,
		'shareimg' => tomedia($this->module['config']['shareimg']),
		'sharelink' => Util::createModuleUrl('index',array('zfuid'=>$userinfo['id'])),
		'do' => 'order',
		'title' => '活动度',
		'open' => $_GPC['open'],
	);

	include $this->template ('activity');
