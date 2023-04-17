<?php 
	global $_W,$_GPC;
	$_GPC['do'] = 'level';
	$userinfo = model_user::initUserInfo();
	
	$instruct = pdo_get('zofui_tasktb_instruct',array('uniacid'=>$_W['uniacid']));
	
	if( empty($_W['set']['ulevel']) ) die;


	$sharetitle = str_replace('{nick}', $userinfo['nickname'], $this->module['config']['sharetitle']);
	$sharedesc = str_replace('{nick}', $userinfo['nickname'], $this->module['config']['sharedesc']);

	$settings = array(
		'sharetitle' => $sharetitle,
		'sharedesc' => $sharedesc,
		'shareimg' => tomedia($this->module['config']['shareimg']),
		'sharelink' => Util::createModuleUrl('down',array('zfuid'=>$userinfo['id'])),
		'do' => 'level',
		'op' => '',
		'title' => '',
	);

	include $this->template ('level');
