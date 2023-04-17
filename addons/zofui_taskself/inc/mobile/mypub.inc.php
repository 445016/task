<?php 
	global $_W,$_GPC;
	$_GPC['do'] = 'mypub';
	$_GPC['op'] = empty( $_GPC['op'] ) ? 0 : $_GPC['op'];
	$userinfo = model_user::initUserInfo();
	
	$tasksort = model_tasksort::getSort(); // 任务分类
	
	
	$sharetitle = str_replace('{nick}', $userinfo['nickname'], $this->module['config']['sharetitle']);
	$sharedesc = str_replace('{nick}', $userinfo['nickname'], $this->module['config']['sharedesc']);

	$settings = array(
		'sharetitle' => $sharetitle,
		'sharedesc' => $sharedesc,
		'shareimg' => tomedia($this->module['config']['shareimg']),
		'sharelink' => Util::createModuleUrl('mypub',array('op'=>$_GPC['op'],'zfuid'=>$userinfo['id'])),
		'do' => 'mypub',
		'op' => $_GPC['op'],
		'title' => '我发布的',
		'issetpage' => 1,
		'pagetype'=> intval($_GPC['sid']),
	);

	include $this->template ('mypub');
