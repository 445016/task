<?php 
	global $_W,$_GPC;
	$_GPC['do'] = 'downexplain';
	$userinfo = model_user::initUserInfo();
	
	$instruct = pdo_get('zofui_tasktb_instruct',array('uniacid'=>$_W['uniacid']));
	


	$settings = array(
		'sharetitle' => $sharetitle,
		'sharedesc' => $sharedesc,
		'shareimg' => tomedia($this->module['config']['shareimg']),
		'sharelink' => Util::createModuleUrl('downexplain',array('zfuid'=>$userinfo['id'])),
		'pagetype' => 0,
		'do' => 'downexplain',
		'op' => '',
		'title' => '合伙人',
		'downnum' => intval( $_W['set']['downnum'] ),
	);

	include $this->template ('downexplain');
