<?php 
	global $_W,$_GPC;
	$_GPC['do'] = 'pubin';
	$userinfo = model_user::initUserInfo();
	
	$tasksort = model_tasksort::getSort(); // 任务分类
	


	$sharetitle = str_replace('{nick}', $userinfo['nickname'], $this->module['config']['sharetitle']);
	$sharedesc = str_replace('{nick}', $userinfo['nickname'], $this->module['config']['sharedesc']);

	$settings = array(
		'sharetitle' => $sharetitle,
		'sharedesc' => $sharedesc,
		'shareimg' => tomedia($this->module['config']['shareimg']),
		'sharelink' => Util::createModuleUrl('pubin',array('zfuid'=>$userinfo['id'])),
		'do' => 'pubin',
		'op' => '',
		'pagetype' => 0,
		'title' => '发任务',
		'adtype' => intval( $this->module['config']['adtype'] ),
		'open' => $_GPC['open'],
	);



	include $this->template ('pubin');
