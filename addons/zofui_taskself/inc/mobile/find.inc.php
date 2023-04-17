<?php 
	global $_W,$_GPC;
	$_GPC['do'] = 'find';
	$_GPC['op'] = $_GPC['op'] == 'accer' ? 'accer' : 'puber';
	$iswx = !empty( $_W['openid'] ) && $_W['container'] == 'wechat' && strlen( $_W['openid'] ) > 15;
	$_W['set'] = $this->module['config'];
	$_W['set']['kefupo'] = iunserializer( $_W['set']['kefupo'] );
	model_user::wxLimit();
	if( $iswx || (!$iswx && $this->module['config']['indexregist'] == 0) ) {
		$userinfo = model_user::initUserInfo();
	}
	model_user::devInit();
	
	$slide = model_slider::getSlider('guy');

	if( $_W['set']['isclosefind'] == 1 ) message('找人功能已被关闭');

	$banner = model_guysort::getSort('struct');
	
	$tasksort = model_tasksort::getSort(); // 任务分类

	$sharetitle = str_replace('{nick}', $userinfo['nickname'], $this->module['config']['sharetitle']);
	$sharedesc = str_replace('{nick}', $userinfo['nickname'], $this->module['config']['sharedesc']);

	$settings = array(
		'sharetitle' => $sharetitle,
		'sharedesc' => $sharedesc,
		'shareimg' => tomedia($this->module['config']['shareimg']),
		'sharelink' => Util::createModuleUrl('find',array('zfuid'=>$userinfo['id'])),
		'do' => 'find',
		'op' => $_GPC['op'],
		'title' => '找人',
		'pagetype' => 0,
		'issetpage' => 1,
	);

	include $this->template ('find');
