<?php 
	global $_W,$_GPC;
	$userinfo = model_user::initUserInfo();

	


	$settings = array(
		'sharetitle' => '看的答案',
		'sharedesc' => '看的答案',
		'shareimg' => tomedia($this->module['config']['shareimg']),
		'sharelink' => '',
		'do' => 'anw',
		'op' => '',
		'title' => '看的答案',
	);
	
	include $this->template('anw');
