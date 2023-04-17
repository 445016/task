<?php 
	global $_W,$_GPC;
	$_GPC['do'] = 'pubtb';
	$userinfo = model_user::initUserInfo();
	
	if( $_W['set']['ispubtbtask'] == 1 ) message('发布担保任务已被关闭');

	$step = pdo_get('zofui_tasktb_tbtaskstep',array('userid'=>$userinfo['uid'],'uniacid'=>$_W['uniacid']));
	if( !empty( $step ) ) {
		$step = iunserializer( $step['step']  );
	}
	
	// 会员等级
	$level = model_user::levelRes($userinfo,$this->module['config']);
	if( $level == 1 ) {
		$_W['set']['tbpubserver'] = $_W['set']['tbpubservera'];
		$_W['set']['tbtopserver'] = $_W['set']['tbtopservera'];
		$_W['set']['tbkaserver'] = $_W['set']['tbkaservera'];
		$_W['set']['tbminmoney'] = $_W['set']['tbminmoneya'];
		$_W['set']['tbpubneedposit'] = $_W['set']['tbpubneedposita'];
	}
	if( $level == 2 ) {
		$_W['set']['tbpubserver'] = $_W['set']['tbpubserverb'];
		$_W['set']['tbtopserver'] = $_W['set']['tbtopserverb'];
		$_W['set']['tbkaserver'] = $_W['set']['tbkaserverb'];
		$_W['set']['tbminmoney'] = $_W['set']['tbminmoneyb'];
		$_W['set']['tbpubneedposit'] = $_W['set']['tbpubneedpositb'];
	}
	
	$pubrule = pdo_get('zofui_tasktb_instructa',array('uniacid'=>$_W['uniacid'],'type'=>1));
	
	$sharetitle = str_replace('{nick}', $userinfo['nickname'], $this->module['config']['sharetitle']);
	$sharedesc = str_replace('{nick}', $userinfo['nickname'], $this->module['config']['sharedesc']);
	
	$settings = array(
		'sharetitle' => $sharetitle,
		'sharedesc' => $sharedesc,
		'shareimg' => tomedia($this->module['config']['shareimg']),
		'sharelink' => Util::createModuleUrl('pubtb',array('zfuid'=>$userinfo['id'])),
		'do' => 'pubtb',
		'title' => '发布担保任务',
		'cname' => $_W['cname'],
		'tbtopserver' => sprintf('%.2f',$_W['set']['tbtopserver']),
		'tbkaserver' => sprintf('%.2f',$_W['set']['tbkaserver']),
		'tbpubserver' => sprintf('%.2f',$_W['set']['tbpubserver']),
		'tbminmoney' => sprintf('%.2f',$_W['set']['tbminmoney']),
		'tbpubneedposit' => sprintf('%.2f',$_W['set']['tbpubneedposit']),
		'maxtbmoney' => sprintf('%.2f',$_W['set']['maxtbmoney']),
		'mintbmoney' => sprintf('%.2f',$_W['set']['mintbmoney']),
		'tasktime' => sprintf('%.2f',$_W['set']['tbtasktime']),	
		'open' => $_GPC['open'],
		'rdrule' => intval($_W['set']['rdrule']),
	);
	
	$mustnick = 0;
	if( $_W['dev'] == 'wap' && ( empty($userinfo['nickname']) || empty($userinfo['headimgurl']) ) && empty($_W['set']['mustnick']) ){
		$mustnick = 1;
	}
	

	include $this->template ('pubtb');
