<?php 
	global $_W,$_GPC;
	
	$settings = $_W['set'] = $this->module['config'];

	if ($_W['account']['level'] == 3 || $_W['account']['key'] != $settings['appid']) {
		
		if (empty($_GPC['code'])) {
			$url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$settings['appid'].'&redirect_uri='.urlencode($_W['siteroot'].'app/'.substr($this->createMobileUrl('auth'), 2)).'&response_type=code&scope=snsapi_base&state=1#wechat_redirect';
			header("location: ".$url);
			exit;
		}

		load()->func('communication');
		

		$response = ihttp_get('https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$settings['appid'].'&secret='.$settings['secret'].'&code='.$_GPC['code'].'&grant_type=authorization_code');
		$res = json_decode($response['content'],1);
		
		$info = pdo_get('zofui_task_user',array('uniacid'=>$_W['uniacid'],'openid'=>$_W['openid']),array('id'));
		
		if ( !empty($info) ) {
			pdo_update('zofui_task_user', array('authopenid' => $res['openid']), array('id' => $info['id']));
			Util::deleteCache('u',$_W['openid']);
		}
		header("location: ".$this->createMobileUrl('index'));
		exit;

	}