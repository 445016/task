<?php 
	global $_W,$_GPC;
	$_GPC['do'] = 'group';
	$_GPC['op'] = empty( $_GPC['op'] ) ? 'site' : $_GPC['op'];
	model_user::wInit();
	$_W['set'] = $this->module['config'];
	
	//批量删除投诉
	if(checksubmit('deldsall')){
		$res = WebCommon::deleteDataInWeb($_GPC['checkall'],'zofui_tasktb_groupds');
		message('操作完成,成功删除'.$res[0].'项，失败'.$res[1].'项',referer(),'success');
	}
	//批量删除会员
	if(checksubmit('delgrall')){
		$res = WebCommon::deleteDataInWeb($_GPC['checkall'],'zofui_tasktb_group');
		message('操作完成,成功删除'.$res[0].'项，失败'.$res[1].'项',referer(),'success');
	}
	//批量删除订单
	if(checksubmit('delgrlogall')){
		$res = WebCommon::deleteDataInWeb($_GPC['checkall'],'zofui_tasktb_grouplog');
		message('操作完成,成功删除'.$res[0].'项，失败'.$res[1].'项',referer(),'success');
	}

	if(checksubmit('delduihall')){
		$res = WebCommon::deleteDataInWeb($_GPC['checkall'],'zofui_tasktb_geoupbs');
		message('操作完成,成功删除'.$res[0].'项，失败'.$res[1].'项',referer(),'success');
	}



	// 查看记录
	if( $_GPC['op'] == 'ds' ){

		$where = array('uniacid'=>$_W['uniacid']);

		$order = ' `id` DESC ';
		$info = Util::getAllDataInSingleTable('zofui_tasktb_groupds',$where,intval( $_GPC['page'] ),10,$order);

		$list = $info[0];
		$pager = $info[1];

		if( !empty( $list ) ){
			foreach ( $list as &$v ) {
				$v['task'] = pdo_get('zofui_tasktb_task',array('uniacid'=>$_W['uniacid'],'id'=>$v['taskid']),array('title','type'));
				$v['taked'] = pdo_get('zofui_tasktb_taked',array('uniacid'=>$_W['uniacid'],'id'=>$v['takedid']),array('content'));
				$v['user'] = model_user::getSingleUser( $v['uid'] );
			}
			unset($v);
		}

	// 删除投诉
	}elseif( $_GPC['op'] == 'delds' ){
		$res = WebCommon::deleteSingleData($_GPC['id'],'zofui_tasktb_groupds');
		if($res) message('删除成功',referer(),'success');
	

	// 查看记录
	}elseif( $_GPC['op'] == 'gr' ){

		$where = array('uniacid'=>$_W['uniacid']);

		$order = ' `id` DESC ';
		$info = Util::getAllDataInSingleTable('zofui_tasktb_group',$where,intval( $_GPC['page'] ),10,$order);

		$list = $info[0];
		$pager = $info[1];

	}elseif( $_GPC['op'] == 'delgr' ){

		pdo_delete('zofui_tasktb_group',array('id'=>$_GPC['id'],'uniacid'=>$_W['uniacid']));
		message('删除成功',referer(),'success');

	// 查看记录
	}elseif( $_GPC['op'] == 'join' ){

		$where = array('uniacid'=>$_W['uniacid']);

		if( !empty($_GPC['gid']) ) $where['gid'] = $_GPC['gid'];

		$order = ' `id` DESC ';
		$info = Util::getAllDataInSingleTable('zofui_tasktb_grouplog',$where,intval( $_GPC['page'] ),10,$order);

		$list = $info[0];
		$pager = $info[1];

		if( !empty( $list ) ){
			foreach ( $list as &$v ) {
				$v['user'] = model_user::getSingleUser( $v['uid'] );
			}
			unset($v);
		}

	}elseif( $_GPC['op'] == 'delgrlog' ){

		pdo_delete('zofui_tasktb_grouplog',array('id'=>$_GPC['id'],'uniacid'=>$_W['uniacid']));
		message('删除成功',referer(),'success');
	
	}elseif( $_GPC['op'] == 'duih' ){

		$where = array('uniacid'=>$_W['uniacid']);


		$order = ' `id` DESC ';
		$info = Util::getAllDataInSingleTable('zofui_tasktb_geoupbs',$where,intval( $_GPC['page'] ),10,$order);

		$list = $info[0];
		$pager = $info[1];

		if( !empty( $list ) ){
			foreach ( $list as &$v ) {
				$v['user'] = model_user::getSingleUser( $v['uid'] );
			}
			unset($v);
		}

	}elseif( $_GPC['op'] == 'delduih' ){

		pdo_delete('zofui_tasktb_geoupbs',array('id'=>$_GPC['id'],'uniacid'=>$_W['uniacid']));
		message('删除成功',referer(),'success');
	
	}elseif( $_GPC['op'] == 'data' ){

		
		$baoship = model_slider::getbaoshi($_W['set']['actsper']);
		$totalbaoshi = $baoship['totalbaoshi'];
		$totalactin = $baoship['totalactin'];
		$totalpayed = $baoship['totalpayed'];
		$last = $baoship['last'];
		$per = $baoship['per'];

	}
	
	
	include $this->template('web/'.$_W['mtemp'].'/group');