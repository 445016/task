<?php 
	global $_W,$_GPC;
	model_user::wInit();
	$op = $_GPC['op'] = empty($_GPC['op']) ? 'design' : $_GPC['op'];

	set_time_limit(1800);

	/*$set = Util::getModuleConfig();
	$power = iunserializer( $set['power'] );
	if( $_W['role'] != 'founder' && $_W['role'] != 'manager' && !empty( $power ) && in_array('poster', $power) ) die;*/

	// 订阅消息
    $model = pdo_get('modules',array('name'=>'zofui_taskself'),array('subscribes','mid'));

    $model['subscribes'] = iunserializer( $model['subscribes'] );
	
    $isneedadd1 = $isneedadd2 = $isneedadd3 = $isneedadd4 = 1; // 需要更新
	if( !empty( $model['subscribes'] ) ){
		foreach ($model['subscribes'] as $v) {
			if( $v == 'subscribe' ){
				$isneedadd1 = 0; // 不需要更新
			}
			if( $v == 'qr' ){
				$isneedadd2 = 0;
			}
			if( $v == 'text' ){
				$isneedadd3 = 0;
			}
			if( $v == 'click' ){
				$isneedadd4 = 0;
			}
		}
	}
	if( $isneedadd1 == 1 || $isneedadd2 ==1 || $isneedadd3 == 1 || $isneedadd4 == 1 ){
		if($isneedadd1 == 1) $model['subscribes'][] = 'subscribe';
		if($isneedadd2 == 1) $model['subscribes'][] = 'qr';
		if($isneedadd3 == 1) $model['subscribes'][] = 'text';
		if($isneedadd4 == 1) $model['subscribes'][] = 'click';

		$subscribe = iserializer( $model['subscribes'] );
		pdo_update('modules',array('subscribes'=>$subscribe),array('mid'=>$model['mid']));				
		cache_delete('module_receive_enable');
	}
	// ////////////////////
	
	
	
	$poster = pdo_get('zofui_tasktb_selfposter',array('uniacid'=>$_W['uniacid']));
	if( !empty( $poster ) ) {
		$poster['params'] = iunserializer( $poster['params'] );
	}else{
		$data = array(
			'uniacid' => $_W['uniacid'],
			'bgimg' => $_W['siteroot'].'addons/zofui_taskself/public/images/postertemp.jpg',
			'params' => 'a:1:{i:0;a:4:{s:2:"id";s:14:"i1506178693523";s:4:"name";s:6:"qrcode";s:5:"title";s:9:"二维码";s:6:"params";a:5:{s:3:"img";s:51:"./../addons/zofui_taskself/public/images/qrcode.png";s:5:"width";i:151;s:6:"height";i:152;s:4:"left";i:137;s:3:"top";i:257;}}}'
		);
		pdo_insert('zofui_tasktb_selfposter',$data);

		$poster = $data;
		$poster['params'] = iunserializer( $poster['params'] );
	}


	$pimg = model_poster::getPoster( array('id'=>0,'headimgurl'=>$_W['siteroot'].'addons/zofui_taskself/public/images/default_head.jpg','nickname'=>'会员昵称') );
	
	
	
	include $this->template('web/'.$_W['mtemp'].'/poster');