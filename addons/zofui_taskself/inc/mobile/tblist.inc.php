<?php 
	global $_W,$_GPC;
	$_GPC['do'] = 'tblist';
	
	$iswx = !empty( $_W['openid'] ) && $_W['container'] == 'wechat' && strlen( $_W['openid'] ) > 15;
	$_W['set'] = $this->module['config'];
	$_W['set']['kefupo'] = iunserializer( $_W['set']['kefupo'] );
	model_user::wxLimit();
	if( $iswx || (!$iswx && $this->module['config']['indexregist'] == 0) ) {
		$userinfo = model_user::initUserInfo();
	}
	model_user::devInit();
	
	$tasksort = model_tasksort::getSort(); // 任务分类
	
	$slide = model_slider::getSlider('tbtask');

	$sharetitle = str_replace('{nick}', $userinfo['nickname'], $this->module['config']['sharetitle']);
	$sharedesc = str_replace('{nick}', $userinfo['nickname'], $this->module['config']['sharedesc']);

	// 会员等级
	$level = model_user::levelRes($userinfo,$this->module['config']);
	if( $level == 1 ) {
		$_W['set']['tbtopserver'] = $_W['set']['tbtopservera'];
	}
	if( $level == 2 ) {
		$_W['set']['tbtopserver'] = $_W['set']['tbtopserverb'];
	}	
	
	$settings = array(
		'sharetitle' => $sharetitle,
		'sharedesc' => $sharedesc,
		'shareimg' => tomedia($this->module['config']['shareimg']),
		'sharelink' => Util::createModuleUrl('tblist',array('zfuid'=>$userinfo['id'])),
		'do' => 'tblist',
		'op' => 'tbtask',
		'title' => '担保任务',
		'pagetype' => 0,
		'adlist' => $adlist['list'],
		'issetpage' => 1,
		'findtopserver' => sprintf('%.2f',$_W['set']['findtopserver']),
		'leasttoptime' => sprintf('%.2f',$_W['set']['leasttoptime']),
		'open' => $_GPC['open'],
		'tbtopserver' => sprintf('%.2f',$_W['set']['tbtopserver']),
	);

	include $this->template ('tblist');
