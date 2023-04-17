<?php 
	global $_W,$_GPC;
	$_GPC['do'] = 'userdata';
	$_GPC['op'] = empty( $_GPC['op'] ) ? 'list' : $_GPC['op'];
	model_user::wInit();


	//批量删除会员
	if(checksubmit('deletealluser')){
		$res = WebCommon::deleteDataInWeb($_GPC['checkall'],'zofui_task_user');
		message('操作完成,成功删除'.$res[0].'项，失败'.$res[1].'项',referer(),'success');
	}
	//批量删除表单
	if(checksubmit('deleteallform')){
		$res = WebCommon::deleteDataInWeb($_GPC['checkall'],'zofui_tasktb_authform');
		message('操作完成,成功删除'.$res[0].'项，失败'.$res[1].'项',referer(),'success');
	}

	//批量删除表单
	if(checksubmit('deleteallic')){
		$res = WebCommon::deleteDataInWeb($_GPC['checkall'],'zofui_tasktb_useric');
		message('操作完成,成功删除'.$res[0].'项，失败'.$res[1].'项',referer(),'success');
	}

	//批量验证通过
	if(checksubmit('pass')){
		
		if( empty( $_GPC['checkall'] ) ) message('请先选择数据',referer(),'success');
		$suc = 0;
		foreach ( $_GPC['checkall'] as $v ) {
			$user = pdo_get('zofui_task_user',array('uniacid'=>$_W['uniacid'],'id'=>$v));
			if( $user['verifystatus'] != 1 || empty( $user ) ) continue;

			$end = 0;
			if( $_W['set']['authtime'] > 0 ) $end = TIMESTAMP + $_W['set']['authtime']*24*3600;

			pdo_update('zofui_task_user',array('verifystatus'=>2,'verifyend'=>$end,'iscostauth'=>0),array('id'=>$v,'uniacid'=>$_W['uniacid']));

			model_user::intoUserIc($user,false,true);

			Util::deleteCache('u',$user['uid']);
			// 发通知
			Message::authpassmess( $user['uid'],$user['openid'] );
			$suc ++;
		}

		message('操作完成,成功审核'.$suc.'项',referer(),'success');
	}



	if( $_GPC['op'] == 'list' ){

		$setting = uni_setting($_W['uniacid'], array('creditbehaviors'));
		
		if( $_W['set']['moneytype'] == 0 ){
			$creditbehaviors = $setting['creditbehaviors']['currency'];
		}else{
			$creditbehaviors = $setting['creditbehaviors']['activity'];
		}
		if( empty( $creditbehaviors ) ) {
			$creditbehaviors = 'credit2';
		}
		
		$where = array('uniacid'=>$_W['uniacid']);
		$order = ' a.`id` DESC ';
		
		if( !empty( $_GPC['userid'] ) ) $where['id'] = intval( $_GPC['userid'] );
		
		if( $_GPC['status'] == 1 ) $where['status'] = 0;
		if( $_GPC['status'] == 2 ) $where['status'] = 2;
		
		if( $_GPC['order'] == 1 ) $order = ' b.`'.$creditbehaviors.'` DESC ';
		if( $_GPC['order'] == 2 ) $order = ' a.`deposit` DESC ';
		if( $_GPC['order'] == 3 ) $order = ' a.`pubnumber` DESC ';
		if( $_GPC['order'] == 4 ) $order = ' a.`replynumber` DESC ';
		if( $_GPC['order'] == 5 ) $order = ' a.`yinp` DESC ';
		if( $_GPC['order'] == 6 ) $order = ' a.`baoshi` DESC ';

		if( !empty($_GPC['level']) ) $where['level'] = $_GPC['level'] - 1;

		$select = ' a.*,a.nickname AS nicknamea,a.qq AS qqa,a.mobile AS mobilea,b.'.$creditbehaviors .' AS money,b.mobile AS loginmobile,b.* ';
		
		$str = '';
		if( !empty( $_GPC['for'] ) ) $str = " AND a.nickname LIKE '%".$_GPC['for']."%' ";

		if( !empty($_GPC['account']) ) $str = ' AND b.mobile = '.$_GPC['account'];

		$pnum = $_GPC['pnum'] <= 10 ? 10 : $_GPC['pnum'];
		
		$info = model_user::getAllUser($where,intval( $_GPC['page'] ),$pnum,$order,false,true,$select,$str);
		
		$list = $info[0];
		$pager = $info[1];

		if( !empty($list) ){
			foreach ($list as &$v) {
				$v['isauth'] = model_user::isAuth($v,$_W['set']);

				if( $this->module['config']['isic'] == 1 ){
					$v['useric'] = pdo_getall('zofui_tasktb_userics',array('uniacid'=>$_W['uniacid'],'uid'=>$v['uid']));
				}

			}
		}

		if( $this->module['config']['isic'] == 1 ){
			$useric = pdo_getall('zofui_tasktb_useric',array('uniacid'=>$_W['uniacid']));
		}
		
		$key = pdo_get('zofui_tasktb_codekey');
		$url = 'http://api.zofui.net/app/index.php?c=taskwap&a=settinghtml';
		$res = Util::httpPost($url,array('pnum'=>$pnum,'page'=>$_GPC['page'],'from'=>$_GPC,'site'=>$_W['siteroot'],'en'=>MODULE,'setop'=>'userdatalist','key'=>$key['key'],'pnum'=>$pnum,'att'=>$_W['attachurl']));
		$html = json_decode($res,true);


	}elseif( $_GPC['op'] == 'verify' ){

		$where = array('uniacid'=>$_W['uniacid'],'verifystatus'=>1);
		$order = ' `id` DESC ';

		if( !empty( $_GPC['status'] ) ) $where['verifystatus'] = 2;

		if( !empty( $_GPC['userid'] ) ) {
			$where['id'] = intval( $_GPC['userid'] );
			unset( $where['verifystatus'] );
		}
		$pnum = $_GPC['pnum'] <= 10 ? 10 : $_GPC['pnum'];
		$info = model_db::getall('zofui_task_user',$where,intval( $_GPC['page'] ),$pnum,$order);
		
		$list = $info[0];
		$pager = $info[1];

		if( !empty( $list ) ) {

			foreach ( $list as &$v ) {
				$v['verifyform'] = iunserializer( $v['verifyform'] );
			}
			unset( $v );
		}

		$key = pdo_get('zofui_tasktb_codekey');
		$url = 'http://api.zofui.net/app/index.php?c=taskwap&a=settinghtml';
		$res = Util::httpPost($url,array('pnum'=>$pnum,'page'=>$_GPC['page'],'from'=>$_GPC,'site'=>$_W['siteroot'],'en'=>MODULE,'setop'=>'userdataverify','key'=>$key['key'],'pnum'=>$pnum,'att'=>$_W['attachurl']));
		$html = json_decode($res,true);

	// 拉进黑名单
	}elseif( $_GPC['op'] == 'toblack' ){

		$user = pdo_get('zofui_task_user',array('uniacid'=>$_W['uniacid'],'id'=>$_GPC['id']));

		pdo_update('zofui_task_user',array('status'=>2),array('id'=>$user['id']));

		Util::deleteCache('u',$user['uid']);
		
		message('已拉入黑名单',referer(),'success');

	// 恢复正常
	}elseif( $_GPC['op'] == 'tocom' ){

		$user = pdo_get('zofui_task_user',array('uniacid'=>$_W['uniacid'],'id'=>$_GPC['id']));

		pdo_update('zofui_task_user',array('status'=>0),array('id'=>$user['id']));

		Util::deleteCache('u',$user['uid']);
		
		message('已恢复正常',referer(),'success');

	}elseif( $_GPC['op'] == 'deleteuser' ){

		$user = pdo_get('zofui_task_user',array('uniacid'=>$_W['uniacid'],'id'=>$_GPC['id']));

		pdo_delete('zofui_task_user',array('id'=>$user['id']));

		Util::deleteCache('u',$user['uid']);
		
		message('删除成功',referer(),'success');


	// 表单列表
	}elseif( $_GPC['op'] == 'authform' ){


		$order = ' `number` DESC ';
		$where = array('uniacid'=>$_W['uniacid']);
		$info = model_db::getall('zofui_tasktb_authform',$where,1,1110,$order);
		
		$list = $info[0];

		$useric = pdo_getall('zofui_tasktb_useric',array('uniacid'=>$_W['uniacid']));


	// 
	}elseif( $_GPC['op'] == 'useric' ){

		$order = ' `number` DESC ';
		$where = array('uniacid'=>$_W['uniacid']);
		$info = model_db::getall('zofui_tasktb_useric',$where,$_GPC['page'],30,$order);
		
		$list = $info[0];
		$pager = $info[1];


		if( !empty($list) ){
			foreach ($list as &$v) {
				$v['nownum'] = pdo_count('zofui_tasktb_userics',array('icid'=>$v['id'],'uniacid'=>$_W['uniacid']));
			}
		}	

	}elseif( $_GPC['op'] == 'deleteform' ){

		pdo_delete('zofui_tasktb_authform',array('id'=>$_GPC['id'],'uniacid'=>$_W['uniacid']));
		message('删除成功',referer(),'success');

	}elseif( $_GPC['op'] == 'deleteic' ){

		$res = pdo_delete('zofui_tasktb_useric',array('id'=>$_GPC['id'],'uniacid'=>$_W['uniacid']));

		if( $res ){
			pdo_delete('zofui_tasktb_userics',array('icid'=>$_GPC['id']));
		}

		message('删除成功',referer(),'success');		
	
	// 验证通过
	}elseif( $_GPC['op'] == 'passverify' ){
		

		$user = pdo_get('zofui_task_user',array('uniacid'=>$_W['uniacid'],'id'=>$_GPC['id']));
		if( $user['verifystatus'] != 1 ) message('对方还没有提交认证资料',referer(),'error');

		$end = 0;
		if( $_W['set']['authtime'] > 0 ) $end = TIMESTAMP + $_W['set']['authtime']*24*3600;

		pdo_update('zofui_task_user',array('verifystatus'=>2,'verifyend'=>$end,'iscostauth'=>0),array('id'=>$_GPC['id'],'uniacid'=>$_W['uniacid']));

		model_user::intoUserIc($user,false,true);
		
		Util::deleteCache('u',$user['uid']);
		// 发通知
		Message::authpassmess( $user['uid'],$user['openid'] );
		message('已设为通过',referer(),'success');
	
	// 还原
	}elseif( $_GPC['op'] == 'resetverify' ) {

		$user = pdo_get('zofui_task_user',array('uniacid'=>$_W['uniacid'],'id'=>$_GPC['id']));
		if( empty( $user ) ) message('没有找到会员',referer(),'error');

		pdo_update('zofui_task_user',array('verifystatus'=>0,'verifyend'=>0,'iscostauth'=>0),array('id'=>$_GPC['id'],'uniacid'=>$_W['uniacid']));
		Util::deleteCache('u',$user['uid']);
		message('已还原',referer(),'success');

	}
	
	
	
	include $this->template('web/'.$_W['mtemp'].'/userdata');