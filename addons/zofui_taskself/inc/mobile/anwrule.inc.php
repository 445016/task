<?php 
	global $_W,$_GPC;
	$_GPC['do'] = 'anwrule';
	$userinfo = model_user::initUserInfo();

	if( $_W['set']['isanw'] == 1 ){
		
		if( $_W['set']['anwmb'] > 0 && $_W['set']['anwmbday'] > 0 ){
			
			$boxarr = model_slider::getBackMoney($userinfo['uid'],$_W['set']);
			$now = empty($_W['zfnow']) ? TIMESTAMP : $_W['zfnow']; // 测试用到		
	
			$yesday = strtotime(date('Y-m-d',$now));
			
			$rule = pdo_get('zofui_tasktb_instructa',array('uniacid'=>$_W['uniacid'],'type'=>0));
			$rule['content'] = htmlspecialchars_decode($rule['content']);

			$content = str_replace(array(), array(), $rule['content']);
			
		}else{
			die('功能已关闭');
		}
	}else{
		die('功能已关闭');
	}
	


	$settings = array(
		'sharetitle' => '规则',
		'sharedesc' => '规则',
		'shareimg' => tomedia($this->module['config']['shareimg']),
		'sharelink' => '',
		'do' => 'anwrule',
		'title' => '规则',
	);
	
	include $this->template('anwrule');
