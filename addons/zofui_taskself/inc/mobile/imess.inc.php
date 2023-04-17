<?php 
	global $_W,$_GPC;
	$_GPC['do'] = 'imess';
	$_GPC['op'] = empty( $_GPC['op'] ) ? 'list' : $_GPC['op'];
	$userinfo = model_user::initUserInfo();
	
	$imess = Util::countDataNumber('zofui_tasktb_imess',array('uniacid'=>$_W['uniacid'],'status'=>0,'uid'=>$userinfo['uid']));
	
	$sharetitle = str_replace('{nick}', $userinfo['nickname'], $this->module['config']['sharetitle']);
	$sharedesc = str_replace('{nick}', $userinfo['nickname'], $this->module['config']['sharedesc']);

	$settings = array(
		'sharetitle' => $sharetitle,
		'sharedesc' => $sharedesc,
		'shareimg' => tomedia($this->module['config']['shareimg']),
		'sharelink' => Util::createModuleUrl('imess',array('op'=>$_GPC['op'],'zfuid'=>$userinfo['id'])),
		'do' => 'imess',
		'op' => $_GPC['op'],
		'title' => '通知消息',
		'open' => $_GPC['open'],
		'issetpage' => 1,
	);

	include $this->template ('imess');
