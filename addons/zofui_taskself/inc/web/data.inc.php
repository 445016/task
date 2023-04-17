<?php 
	global $_W,$_GPC;
	$_GPC['do'] = 'data';
	$_GPC['op'] = empty( $_GPC['op'] ) ? 'site' : $_GPC['op'];
	model_user::wInit();

	/*$params = array();
	$params['result'] = 'success';
	$params['from'] = 'notify';
	$params['tid'] = '1534492912493';
	$params['fee'] = '1';
	pay::payResult($params);die;*/
	
	if(!pdo_fieldexists('zofui_tasktb_imess', 'istop')) {
	  pdo_query("ALTER TABLE ".tablename('zofui_tasktb_imess')." ADD `istop` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0';");
	}
	if(!pdo_indexexists('zofui_tasktb_imess', 'istop')) {
	  pdo_query("ALTER TABLE ".tablename('zofui_tasktb_imess')." ADD INDEX istop(`istop`);");
	}
	
	//批量删除投诉
	if(checksubmit('deleteallcomplain')){
		$res = WebCommon::deleteDataInWeb($_GPC['checkall'],'zofui_tasktb_complain');
		message('操作完成,成功删除'.$res[0].'项，失败'.$res[1].'项',referer(),'success');
	}
	//批量删除会员
	if(checksubmit('deletealluser')){
		$res = WebCommon::deleteDataInWeb($_GPC['checkall'],'zofui_task_user');
		message('操作完成,成功删除'.$res[0].'项，失败'.$res[1].'项',referer(),'success');
	}
	//批量删除订单
	if(checksubmit('deleteallorder')){
		$res = WebCommon::deleteDataInWeb($_GPC['checkall'],'zofui_tasktb_paylog');
		message('操作完成,成功删除'.$res[0].'项，失败'.$res[1].'项',referer(),'success');
	}

	//批量删除留言
	if(checksubmit('deleteallmessage')){
		$res = WebCommon::deleteDataInWeb($_GPC['checkall'],'zofui_tasktb_taskmessage');
		message('操作完成,成功删除'.$res[0].'项，失败'.$res[1].'项',referer(),'success');
	}
	//批量删除表单
	if(checksubmit('deleteallform')){
		$res = WebCommon::deleteDataInWeb($_GPC['checkall'],'zofui_tasktb_authform');
		message('操作完成,成功删除'.$res[0].'项，失败'.$res[1].'项',referer(),'success');
	}


	if( $_GPC['op'] == 'site' ){

		$st = $_GPC['datelimit']['start'] ? strtotime($_GPC['datelimit']['start']) : strtotime('-3day');
		$et = $_GPC['datelimit']['end'] ? strtotime($_GPC['datelimit']['end']) : strtotime(date('Y-m-d'));
		$starttime = min($st, $et);
		$endtime = max($st, $et);
		$day_num = intval(($endtime - $starttime) / 86400) + 2;
		$endtime += 86399;	
		

		if($_W['ispost'] && $_W['isajax']){
			$days = array();
			$datasets = array();
			for($i = 0; $i < $day_num; $i++){
				$key = date('m-d', $starttime + 86400 * $i); //$key是日期 9-22
				$days[$key] = 0;
				$datasets['flow1'][$key] = 0;
				$datasets['flow2'][$key] = 0;
				$datasets['flow3'][$key] = 0;
				$datasets['flow4'][$key] = 0;
				$datasets['flow5'][$key] = 0;
			}
			
			
			// 充值收入
			$where = array('time>'=>$starttime,'uniacid'=>$_W['uniacid'],'status'=>1);
			$str= ' AND  time <= '.$endtime;
			$data = Util::getAllDataInSingleTable('zofui_tasktb_paylog',$where,1,999999,'id DESC',false,false,' * ',$str);
			foreach((array)$data[0] as $da) { //
				$key = date('m-d', $da['time']);
				if(in_array($key, array_keys($days))) {
					$datasets['flow1'][$key] += $da['fee'];
				}
			}
			
			// 提现支出
			$where = array('dealtime>'=>$starttime,'uniacid'=>$_W['uniacid'],'status'=>1);
			$str= ' AND  dealtime <= '.$endtime;
			$data = Util::getAllDataInSingleTable('zofui_tasktb_draw',$where,1,999999,'id DESC',false,false,' * ',$str);
			foreach((array)$data[0] as $da) { 
				$key = date('m-d', $da['dealtime']);
				if(in_array($key, array_keys($days))) {
					$datasets['flow2'][$key] += $da['money'];
				}
			}

			// 发布普通任务
			$where = array('createtime>'=>$starttime,'uniacid'=>$_W['uniacid']);
			$str= ' AND  createtime <= '.$endtime;
			$data = Util::getAllDataInSingleTable('zofui_tasktb_task',$where,1,999999,'id DESC',false,false,' id,createtime ',$str);	
			foreach((array)$data[0] as $da) { 
				$key = date('m-d', $da['createtime']);
				if(in_array($key, array_keys($days))) {
					$datasets['flow3'][$key] ++;
				}
			}

			// 回复普通任务
			$where = array('createtime>'=>$starttime,'uniacid'=>$_W['uniacid']);
			$str= ' AND  createtime <= '.$endtime;
			$data = Util::getAllDataInSingleTable('zofui_tasktb_taked',$where,1,999999,'id DESC',false,false,' id,createtime ',$str);
			foreach((array)$data[0] as $da) { 
				$key = date('m-d', $da['createtime']);
				if(in_array($key, array_keys($days))) {
					$datasets['flow4'][$key] ++;
				}
			}
			
			// 发起私包任务
			$where = array('createtime>'=>$starttime,'uniacid'=>$_W['uniacid']);
			$str= ' AND  createtime <= '.$endtime;
			$data = Util::getAllDataInSingleTable('zofui_tasktb_privatetask',$where,1,999999,'id DESC',false,false,' id,createtime ',$str);
			foreach((array)$data[0] as $da) { 
				$key = date('m-d', $da['createtime']);
				if(in_array($key, array_keys($days))) {
					$datasets['flow5'][$key] ++;
				}
			}


			$shuju['label'] = array_keys($days);
			$shuju['datasets'] = $datasets;
			
			if ($day_num == 1) {
				$day_num = 2;
				$shuju['label'][] = $shuju['label'][0];
				
				foreach ($shuju['datasets']['flow1'] as $ky => $va) {
					$k = $ky;
					$v = $va;
				}
				$shuju['datasets']['flow1']['-'] = $v;
				
				foreach ($shuju['datasets']['flow2'] as $ky => $va) {
					$k = $ky;
					$v = $va;
				}
				$shuju['datasets']['flow2']['-'] = $v;
				
				foreach ($shuju['datasets']['flow3'] as $ky => $va) {
					$k = $ky;
					$v = $va;
				}
				$shuju['datasets']['flow3']['-'] = $v;
				
				foreach ($shuju['datasets']['flow4'] as $ky => $va) {
					$k = $ky;
					$v = $va;
				}
				$shuju['datasets']['flow4']['-'] = $v;

				foreach ($shuju['datasets']['flow5'] as $ky => $va) {
					$k = $ky;
					$v = $va;
				}
				$shuju['datasets']['flow5']['-'] = $v;

			}

			$shuju['datasets']['flow1'] = array_values($shuju['datasets']['flow1']);
			$shuju['datasets']['flow2'] = array_values($shuju['datasets']['flow2']);
			$shuju['datasets']['flow3'] = array_values($shuju['datasets']['flow3']);
			$shuju['datasets']['flow4'] = array_values($shuju['datasets']['flow4']);
			$shuju['datasets']['flow5'] = array_values($shuju['datasets']['flow5']);
			exit(json_encode($shuju));
		}
		
		$todystart = strtotime(date('Y-m-d',time()));
		
		//今日充值余额
		$where = array('status'=>1,'type'=>2,'time>'=>$todystart);
		$sumstr = ' SUM(fee) AS totalmoney ';
		$todayin = Util::countDataSum('zofui_tasktb_paylog',$where,$sumstr);

		//今日充值保证金
		$where = array('status'=>1,'type'=>1,'time>'=>$todystart);
		$sumstr = ' SUM(fee) AS totalmoney ';
		$todaydeposit = Util::countDataSum('zofui_tasktb_paylog',$where,$sumstr);		

		// 今日提现余额
		$where = array('type'=>1,'status'=>1,'dealtime>'=>$todystart);
		$sumstr = ' SUM(money) AS totalmoney ';
		$todaydrawmoneyed = Util::countDataSum('zofui_tasktb_draw',$where,$sumstr);			


		// 今日提现保证金
		$where = array('type'=>2,'status'=>1,'dealtime>'=>$todystart);
		$sumstr = ' SUM(money) AS totalmoney ';
		$todaydrawdeposited = Util::countDataSum('zofui_tasktb_draw',$where,$sumstr);	
	
		// 今日提现申请
		$where = array('type'=>1,'createtime>'=>$todystart);
		$sumstr = ' SUM(money) AS totalmoney ';
		$todaydrawmoney = Util::countDataSum('zofui_tasktb_draw',$where,$sumstr);	
		
		
		// 今日提保证金申请
		$where = array('type'=>2,'createtime>'=>$todystart);
		$sumstr = ' SUM(money) AS totalmoney ';
		$todaydrawdeposit = Util::countDataSum('zofui_tasktb_draw',$where,$sumstr);		
	
		
		$todayuser = 0;
		$todaypub = 0;
		$todaytaked = 0;
		$todaypri = 0;

		$where = array('createtime>'=>$todystart);
		$todayuser = Util::countDataNumber('zofui_task_user',$where);			

		$where = array('createtime>'=>$todystart);
		$todaypub = Util::countDataNumber('zofui_tasktb_task',$where);		

		$where = array('createtime>'=>$todystart);
		$todaytaked = Util::countDataNumber('zofui_tasktb_taked',$where);		

		$where = array('createtime>'=>$todystart);
		$todaypri = Util::countDataNumber('zofui_tasktb_privatetask',$where);


		$yesuser = 0;
		$yespub = 0;
		$yestaked = 0;
		$yespri = 0;

		$yesstart = $todystart - 3600*24;

		$where = array('createtime<'=>$todystart);
		$str = ' AND `createtime` >= '.$yesstart;

		$yesuser = Util::countDataNumber('zofui_task_user',$where,$str);			

		
		$yespub = Util::countDataNumber('zofui_tasktb_task',$where,$str);		

		
		$yestaked = Util::countDataNumber('zofui_tasktb_taked',$where,$str);		

		
		$yespri = Util::countDataNumber('zofui_tasktb_privatetask',$where,$str);


	// 投诉
	}elseif( $_GPC['op'] == 'complain' ){

		$where = array('uniacid'=>$_W['uniacid']);

		if( empty( $_GPC['status'] ) ) $where['status'] = 0;
		if( $_GPC['status'] == 1 ) $where['status'] = 1;

		$order = ' `id` DESC ';
		$info = model_db::getall('zofui_tasktb_complain',$where,intval( $_GPC['page'] ),10,$order);


		$list = $info[0];
		$pager = $info[1];

		if( !empty( $list ) ){
			foreach ( $list as &$v ) {
				$v['task'] = pdo_get('zofui_tasktb_task',array('uniacid'=>$_W['uniacid'],'id'=>$v['taskid']),array('title','type'));
				$v['images'] = iunserializer( $v['images'] );
				$v['user'] = model_user::getSingleUser( $v['userid'] );
			}
			unset($v);
		}

		$key = pdo_get('zofui_tasktb_codekey');
		$url = 'http://api.zofui.net/app/index.php?c=taskwap&a=settinghtml';
		$res = Util::httpPost($url,array('pnum'=>$pnum,'page'=>$_GPC['page'],'from'=>$_GPC,'site'=>$_W['siteroot'],'en'=>MODULE,'setop'=>'datacomplain','key'=>$key['key'],'pnum'=>$pnum,'att'=>$_W['attachurl']));
		$html = json_decode($res,true);


	// 删除投诉
	}elseif( $_GPC['op'] == 'deletecomplain' ){
		$res = WebCommon::deleteSingleData($_GPC['id'],'zofui_tasktb_complain');
		if($res) message('删除成功',referer(),'success');
		
	// 转为已处理 未处理
	}elseif( $_GPC['op'] == 'changetodealed' ){
		$status = 0;
		if( $_GPC['type'] == 1) $status = 1;

		$res = pdo_update('zofui_tasktb_complain',array('status'=>$status),array('uniacid'=>$_W['uniacid'],'id'=>$_GPC['id']));

		if($res) message('设置成功',referer(),'success');

	// 会员
	}elseif( $_GPC['op'] == 'user' ){
		$topbar = topbal::userList();
		$setting = uni_setting($_W['uniacid'], array('creditbehaviors'));

		if( $_W['set']['moneytype'] == 0 ){
			$creditbehaviors = $setting['creditbehaviors']['currency'];
		}else{
			$creditbehaviors = $setting['creditbehaviors']['activity'];
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

		$select = ' a.*,b.'.$creditbehaviors .' AS money ';

		$str = '';
		if( !empty( $_GPC['for'] ) ) $str = " AND a.nickname LIKE '%".$_GPC['for']."%' ";

		$info = model_user::getAllUser($where,intval( $_GPC['page'] ),10,$order,false,true,$select,$str);
				
		$list = $info[0];
		$pager = $info[1];
	
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


	// 订单
	}elseif( $_GPC['op'] == 'order' ){

		$where = array('uniacid'=>$_W['uniacid']);
		$order = ' `id` DESC ';

		if( $_GPC['status'] == 1 ) $where['status'] = 0;
		if( $_GPC['status'] == 2 ) $where['status'] = 1;

		if( $_GPC['type'] == 1 ) $where['type'] = 2;
		if( $_GPC['type'] == 2 ) $where['type'] = 1;

		$pnum = $_GPC['pnum'] <= 10 ? 10 : $_GPC['pnum'];

		$info = model_db::getall('zofui_tasktb_paylog',$where,intval( $_GPC['page'] ),$pnum,$order);
		
		$list = $info[0];
		$pager = $info[1];
		
		if( !empty( $list ) ){
			foreach ($list as &$v) {
				$v['user'] = model_user::getSingleUser( $v['userid'] );
			}
		}

		$key = pdo_get('zofui_tasktb_codekey');
		$url = 'http://api.zofui.net/app/index.php?c=taskwap&a=settinghtml';
		$res = Util::httpPost($url,array('pnum'=>$pnum,'page'=>$_GPC['page'],'from'=>$_GPC,'site'=>$_W['siteroot'],'en'=>MODULE,'setop'=>'dataorder','key'=>$key['key'],'pnum'=>$pnum,'att'=>$_W['attachurl']));
		$html = json_decode($res,true);
		
	// 留言
	}elseif( $_GPC['op'] == 'message' ){
		
		$where = array('uniacid'=>$_W['uniacid']);
		$order = ' `id` DESC ';

		$where['parent'] = 0;
		//$where['type'] = 0;
		
		if( !empty( $_GPC['taskid'] ) ) $where['taskid'] = intval( $_GPC['taskid'] );
		
		if( $_GPC['type'] == 1 ) $where['isadmin'] = 0;
		if( $_GPC['type'] == 2 ) $where['isadmin'] = 1;

		$info = model_db::getall('zofui_tasktb_taskmessage',$where,intval( $_GPC['page'] ),10,$order);
		
		$list = $info[0];
		$pager = $info[1];

		if( !empty( $list ) ){
			foreach ($list as &$v) {
				$v['user'] = model_user::getSingleUser( $v['userid'] );
				$v['reply'] = pdo_getall('zofui_tasktb_taskmessage',array('parent'=>$v['id'],'type'=>0));
			}
		}

		$key = pdo_get('zofui_tasktb_codekey');
		$url = 'http://api.zofui.net/app/index.php?c=taskwap&a=settinghtml';
		$res = Util::httpPost($url,array('pnum'=>$pnum,'page'=>$_GPC['page'],'from'=>$_GPC,'site'=>$_W['siteroot'],'en'=>MODULE,'setop'=>'datamessage','key'=>$key['key'],'pnum'=>$pnum,'att'=>$_W['attachurl']));
		$html = json_decode($res,true);


	}elseif( $_GPC['op'] == 'deleteorder' ){

		pdo_delete('zofui_tasktb_paylog',array('id'=>$_GPC['id'],'uniacid'=>$_W['uniacid']));
		message('删除成功',referer(),'success');
	

	// 删除留言
	}elseif( $_GPC['op'] == 'deletemess' ){

		pdo_delete('zofui_tasktb_taskmessage',array('id'=>$_GPC['id'],'uniacid'=>$_W['uniacid']));
		message('删除成功',referer(),'success');
	
	}elseif( $_GPC['op'] == 'sendmess' ) {

		$sendinfo = Util::getCache('sendmess','all');

		$allnum = cache_search('tstb:'.$_W['uniacid'].':singlepro');

		$pronum = 0;
		if( !empty( $allnum ) ) {
			
			foreach ($allnum as $v) {
				$pronum += $v;
			}

		}

	// 表单列表
	}elseif( $_GPC['op'] == 'authform' ){

		$list = pdo_getall('zofui_tasktb_authform',array('uniacid'=>$_W['uniacid']));

		$order = ' `number` DESC ';
		$where = array('uniacid'=>$_W['uniacid']);
		$info = model_db::getall('zofui_tasktb_authform',$where,1,999,$order);
		
		$list = $info[0];

	}elseif( $_GPC['op'] == 'deleteform' ){

		pdo_delete('zofui_tasktb_authform',array('id'=>$_GPC['id'],'uniacid'=>$_W['uniacid']));
		message('删除成功',referer(),'success');
	}


	
	
	include $this->template('web/'.$_W['mtemp'].'/data');