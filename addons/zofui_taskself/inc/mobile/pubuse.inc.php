<?php 
	global $_W,$_GPC;
	$userinfo = model_user::initUserInfo();
	$tasksort = model_tasksort::getSort(); // 任务分类
	


	// 会员等级
	$level = model_user::levelRes($userinfo,$this->module['config']);
	if( $level == 1 ) {
		$_W['set']['usetaskserver'] = $_W['set']['usetaskservera'];
		$_W['set']['leastuseserver'] = $_W['set']['leastuseservera'];
		$_W['set']['usetopserver'] = $_W['set']['usetopservera'];
		$_W['set']['findkeyserver'] = $_W['set']['findkeyservera'];
	}
	if( $level == 2 ) {
		$_W['set']['usetaskserver'] = $_W['set']['usetaskserverb'];
		$_W['set']['leastuseserver'] = $_W['set']['leastuseserverb'];
		$_W['set']['usetopserver'] = $_W['set']['usetopserverb'];
		$_W['set']['findkeyserver'] = $_W['set']['findkeyserverb'];
	}

	// 分享
	$sharetitle = str_replace('{nick}', $userinfo['nickname'], $this->module['config']['sharetitle']);
	$sharedesc = str_replace('{nick}', $userinfo['nickname'], $this->module['config']['sharedesc']);
	$settings = array(
		'sharetitle' => $sharetitle,
		'sharedesc' => $sharedesc,
		'shareimg' => tomedia($this->module['config']['shareimg']),
		'sharelink' => Util::createModuleUrl('user',array('zfuid'=>$userinfo['id'])),
		'do' => 'pubuse',
		'title' => '发布试用任务',
		'usetaskserver' => sprintf('%.2f',$_W['set']['usetaskserver']),
		'leastuseserver' => sprintf('%.2f',$_W['set']['leastuseserver']),
		'usetopserver' => sprintf('%.2f',$_W['set']['usetopserver']),
		'cname' => $_W['cname'],
		'leastusemoney' => $_W['set']['leastusemoney']*100/100,
		'findkeyserver' => $_W['set']['findkeyserver']*100/100,
		'verifystatus' => $userinfo['verifystatus'],
		'isauth' => $_W['set']['isauth'],
	);

	$mustnick = 0;
	if( $_W['dev'] == 'wap' && ( empty($userinfo['nickname']) || empty($userinfo['headimgurl']) ) && empty($_W['set']['mustnick']) ){
		$mustnick = 1;
	}

	include $this->template ('pubuse');
