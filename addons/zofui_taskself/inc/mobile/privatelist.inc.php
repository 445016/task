<?php 
	global $_W,$_GPC;
	$_GPC['do'] = 'privatelist';
	$_GPC['op'] = $_GPC['op'] == 'accer' ? 'accer' : 'puber';
	$userinfo = model_user::initUserInfo();
	
	$tasksort = model_tasksort::getSort(); // 任务分类
	
	
	$sharetitle = str_replace('{nick}', $userinfo['nickname'], $this->module['config']['sharetitle']);
	$sharedesc = str_replace('{nick}', $userinfo['nickname'], $this->module['config']['sharedesc']);

	$settings = array(
		'sharetitle' => $sharetitle,
		'sharedesc' => $sharedesc,
		'shareimg' => tomedia($this->module['config']['shareimg']),
		'sharelink' => Util::createModuleUrl('privatelist',array('op'=>$_GPC['op'],'zfuid'=>$userinfo['id'])),
		'do' => 'privatelist',
		'op' => $_GPC['op'],
		'title' => '发任务',
		'open' => $_GPC['open'],
		'issetpage' => 1,
	);

	include $this->template ('privatelist');
