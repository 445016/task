<?php 
	global $_W,$_GPC;
	$_GPC['do'] = 'pub';
	$userinfo = model_user::initUserInfo();
	
	if( $_W['set']['isclosepub'] == 1 ) message('发任务功能已被关闭');
	
	$sort = pdo_get('zofui_tasktb_tasksort',array('uniacid'=>$_W['uniacid'],'id'=>$_GPC['sid']));
	if( empty( $sort ) ) message('需要发布的任务分类不存在');
	$sort['other'] = iunserializer($sort['other']);
	if( !empty($sort['other']['taskimg']) ){
		$sort['other']['showtaskimg'] = array();
		foreach ($sort['other']['taskimg'] as $v) {
			$sort['other']['showtaskimg'][] = array('v'=>tomedia($v),'t'=>$v);
		}
	}
	
	$form = pdo_getall('zofui_tasktb_taskform',array('uniacid'=>$_W['uniacid']),array('id','name'),'','number DESC');

	if( $_W['set']['isic'] == 1 ){
		$allic = pdo_getall('zofui_tasktb_useric',array('uniacid'=>$_W['uniacid']));
	}

	$sharetitle = str_replace('{nick}', $userinfo['nickname'], $this->module['config']['sharetitle']);
	$sharedesc = str_replace('{nick}', $userinfo['nickname'], $this->module['config']['sharedesc']);

	// 会员等级
	$level = model_user::levelRes($userinfo,$this->module['config']);

	// 分类设置的
	$sortother = iunserializer($sort['other']);
	if( $sortother['pubsever'] > 0 ){
		$_W['set']['commonserver'] = $sortother['pubsever'];
	}
	if( $sortother['leastpubsever'] > 0 ){
		$_W['set']['commonserverleast'] = $sortother['leastpubsever'];
	}
	if( $sortother['topsever'] > 0 ){
		$_W['set']['topserver'] = $sortother['topsever'];
	}

	// 会员设置的
	if( $level == 1 ) {
		$_W['set']['kaserver'] = $_W['set']['kaservera'];
		$_W['set']['topserver'] = $_W['set']['topservera'];
		$_W['set']['leastcommoney'] = $_W['set']['leastcommoneya'];
		$_W['set']['commonserver'] = $_W['set']['commonservera'];
		$_W['set']['commonserverleast'] = $_W['set']['commonserverleasta'];
	}
	if( $level == 2 ) {
		$_W['set']['kaserver'] = $_W['set']['kaserverb'];
		$_W['set']['topserver'] = $_W['set']['topserverb'];
		$_W['set']['leastcommoney'] = $_W['set']['leastcommoneyb'];
		$_W['set']['commonserver'] = $_W['set']['commonserverb'];
		$_W['set']['commonserverleast'] = $_W['set']['commonserverleastb'];
	}
	
	$mustnick = 0;
	if( $_W['dev'] == 'wap' && ( empty($userinfo['nickname']) || empty($userinfo['headimgurl']) ) && empty($_W['set']['mustnick']) ){
		$mustnick = 1;
	}

	$pubrule = pdo_get('zofui_tasktb_instructa',array('uniacid'=>$_W['uniacid'],'type'=>1));
	
	$steptemp = pdo_getall('zofui_tasktb_step',array('uniacid'=>$_W['uniacid'],'istemp'=>1),array('id','name'),'id desc');
	
	$settings = array(
		'sharetitle' => $sharetitle,
		'sharedesc' => $sharedesc,
		'shareimg' => tomedia($this->module['config']['shareimg']),
		'sharelink' => Util::createModuleUrl('index',array('zfuid'=>$userinfo['id'])),
		'do' => 'pub',
		'title' => '发任务',
		'sortid' => $sort['id'],
		'commonserver' => sprintf('%.2f',$_W['set']['commonserver']),
		'commonserverleast' => sprintf('%.2f',$_W['set']['commonserverleast']),
		'kaserver' => sprintf('%.2f',$_W['set']['kaserver']),
		'endtime' => sprintf('%.2f',$_W['set']['endtime']),
		'topserver' => sprintf('%.2f',$_W['set']['topserver']),
		'leastcommoney' => sprintf('%.2f',$_W['set']['leastcommoney']),
		'cname' => $_W['cname'],
		'verifystatus' => $userinfo['verifystatus'],
		'isauth' => $_W['set']['isauth'],
		'isaddress' => intval($_W['set']['addr']),
		'anwleast' => sprintf('%.2f',$_W['set']['topserver']),
		'rdrule' => intval($_W['set']['rdrule']),
		'pubsethd' => intval($_W['set']['pubsethd']),
		
	);

	include $this->template ('pub');
