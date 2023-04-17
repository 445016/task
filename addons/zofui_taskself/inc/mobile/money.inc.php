<?php 
	global $_W,$_GPC;
	$_GPC['do'] = 'money';
	$_GPC['op'] = empty( $_GPC['op'] ) ? 'in' : $_GPC['op'];
	$userinfo = model_user::initUserInfo();
	
	
	if( $_GPC['op'] == 'out' ){
		if ($this->module['config']['isdraw'] == 2 || $this->module['config']['isdraw'] == 3){
			die;
		}
		$money = model_user::getUserCredit( $userinfo['uid'] );
	} 
	
	if( $this->module['config']['drawvm'] == 1 ){
		load()->model('mc');
		$user = mc_fetch($userinfo['uid'], array('uniacid'=>$_W['uniacid']));
	}

	// 分享
	$sharetitle = str_replace('{nick}', $userinfo['nickname'], $this->module['config']['sharetitle']);
	$sharedesc = str_replace('{nick}', $userinfo['nickname'], $this->module['config']['sharedesc']);
	$settings = array(
		'sharetitle' => $sharetitle,
		'sharedesc' => $sharedesc,
		'shareimg' => tomedia($this->module['config']['shareimg']),
		'sharelink' => Util::createModuleUrl('money',array('op'=>$_GPC['op'],'zfuid'=>$userinfo['id'])),
		'do' => 'money',
		'op' => $_GPC['op'],
		'title' => $_GPC['op'] == 'in' ? $_W['cname'].'充值' : ($_GPC['op'] == 'out' ? $_W['cname'].'提现' : $_W['cname'].'记录'),
		'server' => sprintf( '%.2f' , $_W['set']['drawserver'] ),
		'leastdraw' => $_W['set']['mindraw'] >= 1 ? $_W['set']['mindraw'] : 1,
		'open' => $_GPC['open'],
		'cname' => $_W['cname'],
	);

	
	include $this->template ('money');
