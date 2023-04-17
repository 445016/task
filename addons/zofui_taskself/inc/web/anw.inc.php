<?php 
	global $_W,$_GPC;
	$_GPC['do'] = 'anw';
	$_GPC['op'] = empty( $_GPC['op'] ) ? 'site' : $_GPC['op'];
	model_user::wInit();

	
	//批量删除投诉
	if(checksubmit('delreadlogall')){
		$res = WebCommon::deleteDataInWeb($_GPC['checkall'],'zofui_tasktb_anwread');
		message('操作完成,成功删除'.$res[0].'项，失败'.$res[1].'项',referer(),'success');
	}
	//批量删除会员
	if(checksubmit('delboxall')){
		$res = WebCommon::deleteDataInWeb($_GPC['checkall'],'zofui_tasktb_anwbox');
		message('操作完成,成功删除'.$res[0].'项，失败'.$res[1].'项',referer(),'success');
	}
	//批量删除订单
	if(checksubmit('delbackall')){
		$res = WebCommon::deleteDataInWeb($_GPC['checkall'],'zofui_tasktb_anwgeted');
		message('操作完成,成功删除'.$res[0].'项，失败'.$res[1].'项',referer(),'success');
	}


	if( $_GPC['op'] == 'data' ){

		
		
		$where = array('uniacid'=>$_W['uniacid']);
		$sumstr = ' SUM(cost) AS totalmoney ';
		$totalread = Util::countDataSum('zofui_tasktb_anwread',$where,$sumstr);

		
		$where = array('uniacid'=>$_W['uniacid']);
		$sumstr = ' SUM(lrfee) AS totalmoney ';
		$totalrun = Util::countDataSum('zofui_tasktb_anwread',$where,$sumstr);		

		
		$where = array('uniacid'=>$_W['uniacid']);
		$sumstr = ' SUM(boxfee) AS totalmoney ';
		$totalbox = Util::countDataSum('zofui_tasktb_anwread',$where,$sumstr);			


		$where = array('uniacid'=>$_W['uniacid']);
		$sumstr = ' SUM(sysfee) AS totalmoney ';
		$totalsys = Util::countDataSum('zofui_tasktb_anwread',$where,$sumstr);	
	
		
		$where = array('status'=>1,'uniacid'=>$_W['uniacid']);
		$sumstr = ' SUM(money) AS totalmoney ';
		$totalboxed = Util::countDataSum('zofui_tasktb_anwbox',$where,$sumstr);	
		
		
		$where = array('uniacid'=>$_W['uniacid']);
		$sumstr = ' SUM(money) AS totalmoney ';
		$totalgeted = Util::countDataSum('zofui_tasktb_anwgeted',$where,$sumstr);


	// 查看记录
	}elseif( $_GPC['op'] == 'readlog' ){

		$where = array('uniacid'=>$_W['uniacid']);

		$order = ' `id` DESC ';
		$info = Util::getAllDataInSingleTable('zofui_tasktb_anwread',$where,intval( $_GPC['page'] ),10,$order);

		$list = $info[0];
		$pager = $info[1];

		if( !empty( $list ) ){
			foreach ( $list as &$v ) {
				$v['task'] = pdo_get('zofui_tasktb_task',array('uniacid'=>$_W['uniacid'],'id'=>$v['taskid']),array('title','type'));
				$v['user'] = model_user::getSingleUser( $v['uid'] );
			}
			unset($v);
		}

	// 删除投诉
	}elseif( $_GPC['op'] == 'delreadlog' ){
		$res = WebCommon::deleteSingleData($_GPC['id'],'zofui_tasktb_anwread');
		if($res) message('删除成功',referer(),'success');
	

	// 查看记录
	}elseif( $_GPC['op'] == 'box' ){

		$where = array('uniacid'=>$_W['uniacid']);

		$order = ' `id` DESC ';
		$info = Util::getAllDataInSingleTable('zofui_tasktb_anwbox',$where,intval( $_GPC['page'] ),10,$order);

		$list = $info[0];
		$pager = $info[1];

		if( !empty( $list ) ){
			foreach ( $list as &$v ) {
				$v['task'] = pdo_get('zofui_tasktb_task',array('uniacid'=>$_W['uniacid'],'id'=>$v['taskid']),array('title','type'));
				$v['user'] = model_user::getSingleUser( $v['uid'] );
			}
			unset($v);
		}

	}elseif( $_GPC['op'] == 'delbox' ){

		pdo_delete('zofui_tasktb_anwbox',array('id'=>$_GPC['id'],'uniacid'=>$_W['uniacid']));
		message('删除成功',referer(),'success');

	// 查看记录
	}elseif( $_GPC['op'] == 'back' ){

		$where = array('uniacid'=>$_W['uniacid']);

		$order = ' `id` DESC ';
		$info = Util::getAllDataInSingleTable('zofui_tasktb_anwgeted',$where,intval( $_GPC['page'] ),10,$order);

		$list = $info[0];
		$pager = $info[1];

		if( !empty( $list ) ){
			foreach ( $list as &$v ) {
				$v['user'] = model_user::getSingleUser( $v['uid'] );
			}
			unset($v);
		}

	}elseif( $_GPC['op'] == 'delback' ){

		pdo_delete('zofui_tasktb_anwgeted',array('id'=>$_GPC['id'],'uniacid'=>$_W['uniacid']));
		message('删除成功',referer(),'success');
	}
	
	
	include $this->template('web/'.$_W['mtemp'].'/anw');