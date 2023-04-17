<?php 
	global $_W,$_GPC;
	$_GPC['do'] = 'privatetask';
	$userinfo = model_user::initUserInfo();
	
	$taskinfo = pdo_get('zofui_tasktb_privatetask',array('id'=>intval($_GPC['id'])));
			$queue = new queue;
			$queue -> queueMain();		
	if( $taskinfo['pubuid'] != $userinfo['uid'] && $taskinfo['acceptuid'] != $userinfo['uid'] ){
		message('当前任务与您不匹配');
	}
	
	$taskinfo['images'] = iunserializer($taskinfo['images']);
	$taskinfo['completecontent'] = iunserializer($taskinfo['completecontent']);
	
	
	$iamtheboss = 0;
	if( $taskinfo['bossuid'] == $userinfo['uid'] ){
		$iamtheboss = 1;
		$other = $taskinfo['workeruid'];
	}else{
		$other = $taskinfo['bossuid'];
	}
	$he = model_user::getSingleUser( $other );
	
	//需要自动处理的几个状态 0待确认,2执行中,3雇员提交完成待雇主确认,7待雇员确认拒绝
	
	if($taskinfo['status'] == 0) $autotime = $taskinfo['overtime0'];
	if($taskinfo['status'] == 2) $autotime = $taskinfo['overtime2'];
	if($taskinfo['status'] == 3) $autotime = $taskinfo['overtime3'];
	if($taskinfo['status'] == 7) $autotime = $taskinfo['overtime7'];
	
	
	$sharetitle = str_replace('{nick}', $userinfo['nickname'], $this->module['config']['sharetitle']);
	$sharedesc = str_replace('{nick}', $userinfo['nickname'], $this->module['config']['sharedesc']);

	$settings = array(
		'sharetitle' => $sharetitle,
		'sharedesc' => $sharedesc,
		'shareimg' => tomedia($this->module['config']['shareimg']),
		'sharelink' => Util::createModuleUrl('index',array('zfuid'=>$userinfo['id'])),
		'do' => 'privatetask',
		'id' => intval( $_GPC['id'] ),
		'title' => $taskinfo['tasktitle'],
		'taskmoney' => $taskinfo['taskmoney'],
		'taskid' => $taskinfo['id'],
		'priserver' => $_W['set']['priserver'],
		'prileast' => $_W['set']['prileast'],
		'priserverend' => $_W['set']['priserverend'],
		'prileastend' => $_W['set']['prileastend'],
		'open' => $_GPC['open'],
	);

	include $this->template ('privatetask');
