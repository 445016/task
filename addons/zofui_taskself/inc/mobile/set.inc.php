<?php 
	global $_W,$_GPC;
	$_GPC['do'] = 'set';
	$userinfo = model_user::initUserInfo();
	
	$guysort = model_guysort::getSort(); // 任务分类
	
	$instruct = pdo_get('zofui_tasktb_instruct',array('uniacid'=>$_W['uniacid']));

	$instruct['set'] = str_replace('{need}', $this->module['config']['findneed'], $instruct['set']);

	setcookie('loginzf',md5($_W['account']['key'].random(111,999)),TIMESTAMP+3600);
	
	$order = ' `number` DESC ';
	$where = array('uniacid'=>$_W['uniacid']);
	$info = Util::getAllDataInSingleTable('zofui_tasktb_authform',$where,1,999,$order);
	$form = $info[0];

	$isauthed = model_user::isAuth($userinfo,$_W['set']);

	$usersort = pdo_getall('zofui_tasktb_usersort',array('uid'=>$userinfo['uid']));
	
	$sharetitle = str_replace('{nick}', $userinfo['nickname'], $this->module['config']['sharetitle']);
	$sharedesc = str_replace('{nick}', $userinfo['nickname'], $this->module['config']['sharedesc']);

	$settings = array(
		'sharetitle' => $sharetitle,
		'sharedesc' => $sharedesc,
		'shareimg' => tomedia($this->module['config']['shareimg']),
		'sharelink' => Util::createModuleUrl('set',array('zfuid'=>$userinfo['id'])),
		'do' => 'set',
		'title' => '设置',
		'topexplain' => 1,
		'open'=>$_GPC['open'],
		'maxguydc' => $_W['set']['maxguydc'] > 0 ? $_W['set']['maxguydc'] : 0,
		'authneed' => $_W['set']['authneed'] > 0 && $userinfo['iscostauth'] == 0 ? $_W['set']['authneed'] : 0,
		'authtime' => $_W['set']['authtime'] > 0 ? $_W['set']['authtime'] : 0,
	);

	include $this->template ('set');
