<?php 
	global $_W,$_GPC;
	$_GPC['do'] = 'index';

	$iswx = !empty( $_W['openid'] ) && $_W['container'] == 'wechat' && strlen( $_W['openid'] ) > 15;
	$_W['set'] = $this->module['config'];
	$_W['set']['kefupo'] = iunserializer( $_W['set']['kefupo'] );
	model_user::wxLimit();
	if( $iswx || (!$iswx && $this->module['config']['indexregist'] == 0) ) {
		$userinfo = model_user::initUserInfo();
	}
	model_user::devInit();

	$imesss = model_mess::getBigmess($_W['set'],$userinfo['uid']);
	$slide = model_slider::getSlider('index');
	
	$tasksort = model_tasksort::getSort(); // 任务分类

	if( $_W['set']['indexsort'] == 1 ){
		$sortnav = model_tasksort::structSort( $tasksort );
	}

	$banner = model_slider::getBanner();
	$ixbnnum = $_W['set']['ixbnnum'] <= 0 ? 4 : $_W['set']['ixbnnum'];
	
	if( $_W['set']['adtype'] != 2 ) $ad = model_ad::getAd(); // 广播

	$times = pdo_get('zofui_tasktb_scan',array('uniacid'=>$_W['uniacid']));

	$sharetitle = str_replace('{nick}', $userinfo['nickname'], $this->module['config']['sharetitle']);
	$sharedesc = str_replace('{nick}', $userinfo['nickname'], $this->module['config']['sharedesc']);

	$settings = array(
		'sharetitle' => $sharetitle,
		'sharedesc' => $sharedesc,
		'shareimg' => tomedia($this->module['config']['shareimg']),
		'sharelink' => Util::createModuleUrl('index',array('zfuid'=>$userinfo['id'])),
		'do' => 'index',
		'op' => '',
		'pagetype' => 0,
		'title' => empty($_W['set']['ititle']) ? '接任务' : $_W['set']['ititle'],
		'adtype' => intval( $this->module['config']['adtype'] ),
		'open' => $_GPC['open'],
		'issetpage' => 1,
		'icantake' => $this->module['config']['ist'] == 1 ? 1 : 0,
		'itlist' => intval($this->module['config']['itlist']),
	);



	include $this->template ('index');
