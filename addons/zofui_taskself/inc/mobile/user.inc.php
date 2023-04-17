<?php 
	global $_W,$_GPC;
	$userinfo = model_user::initUserInfo();
	$userinfo['isauth'] = model_user::isAuth( $userinfo,$_W['set'] );
				
	$mydata = model_user::getMyData( $userinfo['uid'],$userinfo['id'] );
	
	$tasksort = model_tasksort::getSort(); // 任务分类
	
	$money = model_user::getUserCredit( $userinfo['uid'] );

	$imesss = model_mess::getBigmess($_W['set'],$userinfo['uid']);
	
	if( $_W['dev'] == 'wx' ) {
		load()->model('mc');
		$user = mc_fetch($userinfo['uid'], array('uniacid'=>$_W['uniacid']));
		
	}
	
	$mustnick = 0;
	if( $_W['dev'] == 'wap' && ( empty($userinfo['nickname']) || empty($userinfo['headimgurl']) ) && empty($_W['set']['mustnick']) ){
		$mustnick = 1;
	}
	
	if( $_W['set']['isanw'] == 1 ){
		$mybox = pdo_getall('zofui_tasktb_anwbox',array('status'=>0,'uid'=>$userinfo['uid'],'uniacid'=>$_W['uniacid']),array(),'',array('endtime DESC'));
		if( $_W['set']['anwmb'] > 0 && $_W['set']['anwmbday'] > 0 ){
			
			$boxarr = model_slider::getBackMoney($userinfo['uid'],$_W['set']);
			$now = empty($_W['zfnow']) ? TIMESTAMP : $_W['zfnow']; // 测试用到		
	
			$yesday = strtotime(date('Y-m-d',$now));
			$isgeted = pdo_get('zofui_tasktb_anwgeted',array('uid'=>$userinfo['uid'],'createtime >='=>$yesday));
		
		}
	}

	$group = pdo_get('zofui_tasktb_group',array('status'=>0,'uniacid'=>$_W['uniacid']));
	if( !empty($group) ){
		$joined = pdo_count('zofui_tasktb_grouplog',array('uniacid'=>$_W['uniacid'],'gid'=>$group['id']));
	}

	if( $_W['set']['issign'] == 1 ){
		$today = date('Y-m-d',TIMESTAMP);
		$istoday = pdo_get('zofui_tasktb_sign',array('uid'=>$userinfo['uid'],'uniacid'=>$_W['uniacid'],'day'=>$today));

		$yesday = date('Y-m-d',TIMESTAMP - (3600*24*1));
		$yesdaysign = pdo_get('zofui_tasktb_sign',array('uid'=>$userinfo['uid'],'uniacid'=>$_W['uniacid'],'day'=>$yesday));

		$signday = 0;
		if( !empty($yesdaysign) ) {
			$signday = $yesdaysign['flag'] == 3 ? 0 : $yesdaysign['flag'];
		}
		if( !empty($istoday) ) $signday = $istoday['flag'];
	}

	$imess = Util::countDataNumber('zofui_tasktb_imess',array('uniacid'=>$_W['uniacid'],'status'=>0,'uid'=>$userinfo['uid']));

	// 分享
	$sharetitle = str_replace('{nick}', $userinfo['nickname'], $this->module['config']['sharetitle']);
	$sharedesc = str_replace('{nick}', $userinfo['nickname'], $this->module['config']['sharedesc']);
	$settings = array(
		'sharetitle' => $sharetitle,
		'sharedesc' => $sharedesc,
		'shareimg' => tomedia($this->module['config']['shareimg']),
		'sharelink' => Util::createModuleUrl('user',array('zfuid'=>$userinfo['id'])),
		'do' => 'user',
		'title' => empty($_W['set']['utitle']) ? '我的' : $_W['set']['utitle'],
		'groupin' => $_W['set']['groupin'],
		'groupnum' => $_W['set']['groupnum'],
		'minbs' => $_W['set']['minbs'] > 0 ? $_W['set']['minbs'] : 0,
	);

	include $this->template ('user');
