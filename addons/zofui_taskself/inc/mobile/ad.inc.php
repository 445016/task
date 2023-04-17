<?php 
	global $_W,$_GPC;
	$_GPC['do'] = 'ad';
	$_W['issub'] = 1;

	if( !empty($_GPC['aaa']) ){
		Message::sendmessage(13583,'osfrpwgocqOOVDhKkNL5g39Un9Bc',$url,'抢到票了','抢到票了','抢到票了');
		echo 'ok';die;
	}

	$ad = pdo_get('zofui_tasktb_ad',array('uniacid'=>$_W['uniacid'],'id'=>$_GPC['id']));
	if( empty( $ad ) || $ad['status'] == 1 ) message('广播不存在');
	


	$settings = array(
		'sharetitle' => $ad['title'],
		'sharedesc' => $ad['title'],
		'shareimg' => tomedia($this->module['config']['shareimg']),
		'sharelink' => '',
		'do' => 'ad',
		'title' => $ad['title'],
	);
	
	include $this->template('ad');
