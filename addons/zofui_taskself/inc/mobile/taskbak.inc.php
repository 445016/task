<?php 
	global $_W,$_GPC;
	$_GPC['do'] = 'taskbak';
	$userinfo = model_user::initUserInfo();
	
	$tasksort = model_tasksort::getSort(); // 任务分类

	$sharetitle = str_replace('{nick}', $userinfo['nickname'], $this->module['config']['sharetitle']);
	$sharedesc = str_replace('{nick}', $userinfo['nickname'], $this->module['config']['sharedesc']);

	$settings = array(
		'sharetitle' => $sharetitle,
		'sharedesc' => $sharedesc,
		'shareimg' => tomedia($this->module['config']['shareimg']),
		'sharelink' => Util::createModuleUrl('index',array('zfuid'=>$userinfo['id'])),
		'do' => 'taskbak',
		'op' => '',
		'pagetype' => 0,
		'title' => empty($_W['set']['ititle']) ? '任务列表' : $_W['set']['ititle'],
		'adtype' => intval( $this->module['config']['adtype'] ),
		'open' => $_GPC['open'],
		'issetpage' => 1,
		'icantake' => $this->module['config']['ist'] == 1 ? 1 : 0,
		'pid' => intval($_GPC['level']),
	);



	include $this->template ('taskbak');
