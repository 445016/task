<?php 
	global $_W,$_GPC;
	$_GPC['do'] = 'regist';
	$userinfo = model_user::initUserInfo();
	
	
	

	$sharetitle = str_replace('{nick}', $userinfo['nickname'], $this->module['config']['sharetitle']);
	$sharedesc = str_replace('{nick}', $userinfo['nickname'], $this->module['config']['sharedesc']);
	$settings = array(
		'sharetitle' => $sharetitle,
		'sharedesc' => $sharedesc,
		'shareimg' => tomedia($this->module['config']['shareimg']),
		'sharelink' => Util::createModuleUrl('index',array('zfuid'=>$userinfo['id'])),
		'do' => 'regist',
		'title' => '注册',
	);

	include $this->template ('regist');
