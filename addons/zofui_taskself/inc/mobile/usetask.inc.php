<?php 
	global $_W,$_GPC;
	$iswx = !empty( $_W['openid'] ) && $_W['container'] == 'wechat' && strlen( $_W['openid'] ) > 15;
	$_W['set'] = $this->module['config'];
	$_W['set']['kefupo'] = iunserializer( $_W['set']['kefupo'] );
	model_user::wxLimit();
	if( $iswx || (!$iswx && $this->module['config']['indexregist'] == 0) ) {
		$userinfo = model_user::initUserInfo();
	}
	model_user::devInit();
	
	if( $this->module['config']['isusetask'] == 0) message('试用功能已关闭');

	$slide = model_slider::getSlider('usetask');
	
	$tasksort = model_tasksort::getSort(); // 任务分类
	
	
	
	// 分享
	$sharetitle = str_replace('{nick}', $userinfo['nickname'], $this->module['config']['sharetitle']);
	$sharedesc = str_replace('{nick}', $userinfo['nickname'], $this->module['config']['sharedesc']);
	$settings = array(
		'sharetitle' => $sharetitle,
		'sharedesc' => $sharedesc,
		'shareimg' => tomedia($this->module['config']['shareimg']),
		'sharelink' => Util::createModuleUrl('usetask',array('zfuid'=>$userinfo['id'])),
		'do' => 'usetask',
		'title' => '试用任务',
		'issetpage' => 1,
	);

	include $this->template ('usetask');
