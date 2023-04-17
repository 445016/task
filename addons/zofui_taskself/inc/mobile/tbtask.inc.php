<?php 
	global $_W,$_GPC;
	$_GPC['do'] = 'tbtask';
	$userinfo = model_user::initUserInfo();
	

	$task = model_tbtask::getTask( $_GPC['id'] );
	$task['title'] = model_task::hideKey( $_W['set']['hidetxt'],$task['title'] );
	$task['content'] = model_task::hideKey( $_W['set']['hidetxt'],$task['content'] );

	if( empty( $task ) ) message('任务不存在');
	
	$isadmin = model_user::isAdmin( $userinfo['uid'] );
	if( $task['status'] == 1 || $task['status'] == 2 ){
		$pass = 0;
		$canverify = 0;
		if( $task['userid'] == $userinfo['uid'] ) $pass = 1;
		if( $isadmin ){
			$pass = 1;
			if( $task['status'] == 1 ) $canverify = 1;
		}
		if( $pass == 0 ) message('此任务还在审核中');
	}
	
	if( $task['status'] == 2 && $pass == 0 ) message('此任务已下架');
	
	$task['taked'] = Util::countDataNumber('zofui_tasktb_tbtaked',array('taskid'=>$task['id']),' AND `status` != 1 ');

	if( !empty( $task['kakey'] ) ){
		shuffle( $task['kakey'] );
	}

	$cutword = iunserializer( $_W['set']['cutword'] );
	if( is_array( $cutword ) ){
		$task['title'] = str_replace($cutword,'*', $task['title']);
		$task['content'] = str_replace($cutword,'*', $task['content']);
	}
	
	
	// 发布者
	if( !empty($task['userid']) ){
		$puber = model_user::getSingleUser( $task['userid'] );
		$puber['isauth'] = model_user::isAuth($puber,$_W['set']);
	}

	$counttime = $task['end'] + $_W['set']['tbcounttime']*3600;

	if( $task['end'] > TIMESTAMP ) $autotime = $task['end']; //剩余可抢
	if( $task['end'] <= TIMESTAMP && $counttime >= TIMESTAMP ) $autotime = $counttime; // 距离结算
	if( $task['start'] > TIMESTAMP ) $autotime = $task['start'];


	// 是否已抢过
	$takedlist = array();
	if( $task['userid'] != $userinfo['uid'] ){

		$alltaked = pdo_getall('zofui_tasktb_tbtaked',array('uniacid'=>$_W['uniacid'],'userid'=>$userinfo['uid'],'taskid'=>$task['id']));

		$mystatus = model_tbtask::getMyStatusByTbtask( $task,$alltaked,$userinfo['uid'],$userinfo['sex'] );

		if( !empty( $alltaked ) ) {
			foreach ( $alltaked as $k => $v ) {
				if( $v['isend'] == 0 ) {
					$acttaked = $v;
				}elseif( $v['isend'] == 1 ) {
					$takedlist[] = $v;
				}
			}
		}
		
		// 步骤
		if( !empty( $acttaked ) ) {
			$acttaked['tasklog'] = model_tbtask::structTakedStep( $acttaked );

			// 提交后自动完成时间
			if( $acttaked['status'] == 2 && $acttaked['step'] == 6 ){
				$tbautotime = $_W['set']['tbautotime2'] > 0 ? $_W['set']['tbautotime2'] : 24;
				$autocomtime = $acttaked['subcomtime'] + $tbautotime*3600;
			}
			// 第一步没做
			if( $acttaked['status'] == 2 && $acttaked['islimitstep'] == 1 ){ //限制第一步
				$tbautotime = $_W['set']['step1time'] > 0 ? $_W['set']['step1time'] : 24;
				$autofailtime = $acttaked['passtime'] + $tbautotime*3600;
			}
			if( $acttaked['status'] == 2 && $acttaked['islimitstep'] == 0 && $acttaked['step'] != 6 ){ //总的步骤
				$tbautotime = $_W['set']['tbautotime1'] > 0 ? $_W['set']['tbautotime1'] : 168;
				$autofailtime = $acttaked['passtime'] + $tbautotime*3600;
			}			
			// 等待确认时间
			if( $acttaked['status'] == 4 ){ 
				$tbautotime = $_W['set']['tbautotime4'] > 0 ? $_W['set']['tbautotime4'] : 24;
				$autofailtime = $acttaked['setfailtime'] + $tbautotime*3600;
			}
			if( $acttaked['status'] == 7 ){
				$tbautotime = $_W['set']['tbautotime7'] > 0 ? $_W['set']['tbautotime7'] : 24;
				$autocomplainendtime = $acttaked['adminsettime'] + $tbautotime*3600;
			}

			
		}
		if( !empty( $takedlist ) ) {
			foreach ($takedlist as &$v) {
				$v['tasklog'] = model_tbtask::structTakedStep( $v );	
			}
			unset( $v );
		}
	}

	// 是否抢过，用于显示图片
	$istaked = pdo_get('zofui_tasktb_tbtaked',array('uniacid'=>$_W['uniacid'],'taskid'=>$task['id'],'userid'=>$userinfo['uid']));
	

	$where = array('uniacid'=>$_W['uniacid'],'taskid'=>$task['id'],'parent'=>0,'type'=>1);
	$messagenum = Util::countDataNumber('zofui_tasktb_taskmessage',$where);



	// 能置顶的时间
	$cantoptime = $lasttoptime = 0;
	if( $userinfo['uid'] == $task['userid'] ){
		$toptime = model_tbtask::countTopTime( $task );
		$lasttoptime = $toptime['last'];
		$cantoptime = $toptime['canadd'];
	}

	
	Util::addOrMinusOrUpdateData('zofui_tasktb_tbtask',array('scan'=>1),$task['id']);

	// 会员等级
	$level = model_user::levelRes($userinfo,$this->module['config']);
	if( $level == 1 ) {
		$_W['set']['tbtopserver'] = $_W['set']['tbtopservera'];
		$_W['set']['tbgivetaskserver'] = $_W['set']['tbgivetaskservera'];
		$_W['set']['tbtakeneedposit'] = $_W['set']['tbtakeneedposita'];
	}
	if( $level == 2 ) {
		$_W['set']['tbtopserver'] = $_W['set']['tbtopserverb'];
		$_W['set']['tbgivetaskserver'] = $_W['set']['tbgivetaskserverb'];
		$_W['set']['tbtakeneedposit'] = $_W['set']['tbtakeneedpositb'];
	}
	
	$settings = array(
		'sharetitle' => $task['title'],
		'sharedesc' => $task['title'],
		'shareimg' => tomedia($this->module['config']['shareimg']),
		'sharelink' => Util::createModuleUrl('tbtask',array('id'=>$_GPC['id'],'zfuid'=>$userinfo['id'])),
		'taskid' => $task['id'],
		'takedid' => (int)$mystatus['acttaked']['id'],
		'pid' => $task['id'],
		'do' => 'tbtask',
		'op' => ($task['userid'] == $userinfo['uid'] || $isadmin ) ? 'all' : 'simple',
		'pagetype' => $_GPC['type'],
		'title' => $task['title'],
		'money' => $task['money'],
		'tbmoney' => $task['tbmoney'],
		'open' => $_GPC['open'],
		'tbtakeneedposit' => sprintf('%.2f',$_W['set']['tbtakeneedposit']),
		'deposit' => $userinfo['deposit'],
		'tbgivetaskserver' => sprintf('%.2f',$_W['set']['tbgivetaskserver']),
		'tbtopserver' => sprintf('%.2f',$_W['set']['tbtopserver']),
		'lasttoptime' => $lasttoptime,
		'cantoptime' => $cantoptime,
		'islimit' => $task['isarealimit'] > 0 ? $_W['islimit'] : 0,
	);
	
	$mustnick = 0;
	if( $_W['dev'] == 'wap' && ( empty($userinfo['nickname']) || empty($userinfo['headimgurl']) ) && empty($_W['set']['mustnick']) ){
		$mustnick = 1;
	}
	
	include $this->template ('tbtask');

