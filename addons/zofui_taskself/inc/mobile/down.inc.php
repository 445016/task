<?php 
	global $_W,$_GPC;
	$_GPC['do'] = 'down';
	$userinfo = model_user::initUserInfo();
	
	if( !empty($userinfo['parent']) ){
		$parent = pdo_get('zofui_task_user',array('uniacid'=>$_W['uniacid'],'id'=>$userinfo['parent']));
		
		if( !empty($parent['parent']) ){
			pdo_update('zofui_task_user',array('pp'=>$parent['parent']),array('id'=>$userinfo['id']));
			$pp = pdo_get('zofui_task_user',array('uniacid'=>$_W['uniacid'],'id'=>$parent['parent']));
			if( !empty($pp['parent']) ){
				pdo_update('zofui_task_user',array('ppp'=>$pp['parent']),array('id'=>$userinfo['id']));
			}
		}
	}
	

	$today = strtotime(date('Y-m-d',TIMESTAMP));
	$yestoday = $today - 24*3600;

	$where = array('userid'=>$userinfo['uid'],'time>'=>$today);
	$todayall = Util::countDataSum('zofui_tasktb_moneylog',$where,' SUM(money) ',' AND type IN (6,28,29,30,31,32,33,34,35) ');

	$where = array('userid'=>$userinfo['uid'],'time>'=>$yestoday,'time<'=>$today);
	$yestodayall = Util::countDataSum('zofui_tasktb_moneylog',$where,' SUM(money) ',' AND type IN (6,28,29,30,31,32,33,34,35) ');	

	$where = array('userid'=>$userinfo['uid']);
	$all = Util::countDataSum('zofui_tasktb_moneylog',$where,' SUM(money) ',' AND type IN (6,28,29,30,31,32,33,34,35) ');


	$sharetitle = str_replace('{nick}', $userinfo['nickname'], $this->module['config']['sharetitle']);
	$sharedesc = str_replace('{nick}', $userinfo['nickname'], $this->module['config']['sharedesc']);

	$wapshare = Util::createModuleUrl('user',array('zfuid'=>$userinfo['id']));

	$settings = array(
		'sharetitle' => $sharetitle,
		'sharedesc' => $sharedesc,
		'shareimg' => tomedia($this->module['config']['shareimg']),
		'sharelink' => Util::createModuleUrl('down',array('zfuid'=>$userinfo['id'])),
		'do' => 'down',
		'op' => '',
		'title' => '我的合伙人',
		'topexplain' => 1,
		'downnum' => intval( $_W['set']['downnum'] ),
	);

	include $this->template ('down');
