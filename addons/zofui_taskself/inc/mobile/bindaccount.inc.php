<?php 
	global $_W,$_GPC;
	$_GPC['do'] = 'bindaccount';

	$userinfo = model_user::initUserInfo();
	
	load()->model('mc');
	$user = mc_fetch($userinfo['uid'], array('uniacid'=>$_W['uniacid']));
	
	
	$sharetitle = str_replace('{nick}', $userinfo['nickname'], $this->module['config']['sharetitle']);
	$sharedesc = str_replace('{nick}', $userinfo['nickname'], $this->module['config']['sharedesc']);

	$settings = array(
		'sharetitle' => $sharetitle,
		'sharedesc' => $sharedesc,
		'shareimg' => tomedia($this->module['config']['shareimg']),
		'sharelink' => Util::createModuleUrl('bindaccount',array('zfuid'=>$userinfo['id'])),
		'do' => 'bindaccount',
		'op' => '',
		'pagetype' => 0,
		'title' => '绑定账户',
		'open' => $_GPC['open'],
		'issetpage' => 1,
	);



	include $this->template ('bindaccount');
