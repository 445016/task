<?php 
	global $_W,$_GPC;
	$_GPC['do'] = 'myreply';
	$_GPC['op'] = empty( $_GPC['op'] ) ? 2 : $_GPC['op'];
	$userinfo = model_user::initUserInfo();
	
	$tasksort = model_tasksort::getSort(); // 任务分类
	
	
	$sharetitle = str_replace('{nick}', $userinfo['nickname'], $this->module['config']['sharetitle']);
	$sharedesc = str_replace('{nick}', $userinfo['nickname'], $this->module['config']['sharedesc']);

	$settings = array(
		'sharetitle' => $sharetitle,
		'sharedesc' => $sharedesc,
		'shareimg' => tomedia($this->module['config']['shareimg']),
		'sharelink' => Util::createModuleUrl('myreply',array('op'=>$_GPC['op'],'zfuid'=>$userinfo['id'])),
		'do' => 'myreply',
		'op' => $_GPC['op'],
		'title' => '我回复的',
		'open' => $_GPC['open'],
		'issetpage' => 1,
	);

	include $this->template ('myreply');
