<?php 
	global $_W,$_GPC;
	$_GPC['do'] = 'myuse';
	$_GPC['status'] = empty( $_GPC['status'] ) ? 1 : $_GPC['status'];
	$userinfo = model_user::initUserInfo();
	
	$tasksort = model_tasksort::getSort(); // 任务分类
	
	
	$sharetitle = str_replace('{nick}', $userinfo['nickname'], $this->module['config']['sharetitle']);
	$sharedesc = str_replace('{nick}', $userinfo['nickname'], $this->module['config']['sharedesc']);

	$settings = array(
		'sharetitle' => $sharetitle,
		'sharedesc' => $sharedesc,
		'shareimg' => tomedia($this->module['config']['shareimg']),
		'sharelink' => Util::createModuleUrl('index',array('op'=>$_GPC['op'],'zfuid'=>$userinfo['id'])),
		'do' => 'myuse',
		'op' => empty( $_GPC['op'] ) ? 'list' : $_GPC['op'],
		'title' => '我发试用',
		'open' => $_GPC['open'],
		'pagetype' => $_GPC['status'],
		'issetpage' => 1,
	);

	include $this->template ('myuse');
