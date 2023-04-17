<?php 
	global $_W,$_GPC;
	$_GPC['do'] = 'guy';
	$_GPC['op'] = $_GPC['op'] == 'puber' ? 'puber' : 'accer';
	$userinfo = model_user::initUserInfo(); //用户信息	
	
	if( $_W['set']['isclosefind'] == 1 ) message('找人功能已被关闭');
	
	$guy = pdo_get('zofui_task_user',array('uniacid'=>$_W['uniacid'],'id'=>$_GPC['id']));
	$guy['isauth'] = model_user::isAuth($guy,$_W['set']);

	if( empty( $guy ) ) message('您要找的人不存在');

	if( $_GPC['op'] == 'puber' && $guy['ispub'] != 1 && $guy['isacc'] == 1 ) {
		$_GPC['op'] == 'accer';
	}

	if( $_GPC['op'] == 'puber' && $guy['ispub'] != 1 ) message('对方未开启发布任务功能');
	if( $_GPC['op'] == 'accer' && $guy['isacc'] != 1 ) message('对方未开启接任务功能');
	

	//$sharetitle = str_replace('{nick}', $userinfo['nickname'], $this->module['config']['sharetitle']);
	//$sharedesc = str_replace('{nick}', $userinfo['nickname'], $this->module['config']['sharedesc']);

	$settings = array(
		'sharetitle' => $guy['nickname'].'的主页',
		'sharedesc' => $guy['nickname'].'的主页',
		'shareimg' => tomedia($this->module['config']['shareimg']),
		'sharelink' => Util::createModuleUrl('guy',array('op'=>$_GPC['op'],'zfuid'=>$userinfo['id'])),
		'do' => 'task',
		'op' => $_GPC['op'],
		'title' => $guy['nickname'],
		'priserver' => $_W['set']['priserver'],
		'prileast' => $_W['set']['prileast'],
		'priserverend' => $_W['set']['priserverend'],
		'prileastend' => $_W['set']['prileastend'],
		'guyid' => $guy['id'],
		'leastprimoney' => sprintf('%.2f',$_W['set']['leastprimoney']),
		'cname' => $_W['cname'],
	);
	
	include $this->template('guy');
