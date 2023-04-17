<?php 
	global $_W,$_GPC;
	$_GPC['do'] = 'deposit';
	$_GPC['op'] = empty( $_GPC['op'] ) ? 'in' : $_GPC['op'];
	$userinfo = model_user::initUserInfo();
	
	
	if( $_GPC['op'] == 'out' ){
		if ($this->module['config']['isdraw'] == 1 || $this->module['config']['isdraw'] == 3){
			die;
		}
	}
	$tasksort = model_tasksort::getSort(); // 任务分类
	
	// 分享
	$sharetitle = str_replace('{nick}', $userinfo['nickname'], $this->module['config']['sharetitle']);
	$sharedesc = str_replace('{nick}', $userinfo['nickname'], $this->module['config']['sharedesc']);
	$settings = array(
		'sharetitle' => $sharetitle,
		'sharedesc' => $sharedesc,
		'shareimg' => tomedia($this->module['config']['shareimg']),
		'sharelink' => Util::createModuleUrl('deposit',array('zfuid'=>$userinfo['id'])),
		'do' => 'deposit',
		'pagetype' => 'deposit',
		'op' => $_GPC['op'],
		'title' => '保证金',
		'server' => sprintf( '%.2f' , $_W['set']['drawpositserver'] ),
	);

	include $this->template ('deposit');
