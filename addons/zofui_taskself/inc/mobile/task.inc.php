<?php 
	global $_W,$_GPC;
	$_GPC['do'] = 'task';
	$userinfo = model_user::initUserInfo();
	
	if( !empty($_GPC['idcode']) ){
		$task = pdo_get('zofui_tasktb_task',array('uniacid'=>$_W['uniacid'],'idcode'=>$_GPC['idcode']));
	}else{
		$task = pdo_get('zofui_tasktb_task',array('uniacid'=>$_W['uniacid'],'id'=>$_GPC['id']));
	}
	
	if( empty( $task ) ) message('任务不存在');
	$task['title'] = model_task::hideKey( $_W['set']['hidetxt'],$task['title'] );
	$task['content'] = model_task::hideKey( $_W['set']['hidetxt'],$task['content'] );
	
	
	if( $task['status'] == 1 || $task['status'] == 2 ){
		$pass = 0;
		$canverify = 0;
		if( $task['userid'] == $userinfo['uid'] ) $pass = 1;
		$isadmin = model_user::isAdmin( $userinfo['uid'] );
		if( $isadmin ){
			$pass = 1;
			if( $task['status'] == 1 ) $canverify = 1;
		}
		if( $pass == 0 ) message('此任务还在审核中');
	}
	if( $task['status'] == 2 && $pass == 0 ) message('此任务已下架');




	$task['images'] = iunserializer( $task['images'] );
	if( !empty($task['stepid']) ){
		$step = pdo_get('zofui_tasktb_step',array('id'=>$task['stepid']));
		$step['step'] = iunserializer($step['step']);

		if( !empty($step['step']) ){
			foreach ($step['step'] as &$v) {
				$v['name'] = str_replace("\n", "<br/>", $v['name']);
			}
			unset($v);
		}
	}

	// 发布者
	$puber = model_user::getSingleUser( $task['userid'] );
	$puber['isauth'] = model_user::isAuth($puber,$_W['set']);

	if( !empty($task['falsepuber']) ){
		$falsepuber = model_task::getFalsepub( $task['falsepuber'] );
	}

	if( $task['type']  == 0 ){ // 抢答任务
		
		$task['kakey'] = iunserializer( $task['kakey'] );
		$task['link'] = iunserializer( $task['link'] );
		$task['taked'] = Util::countDataNumber('zofui_tasktb_taked',array('taskid'=>$task['id'],'endtime>'=>TIMESTAMP));

		if( $task['end'] > TIMESTAMP ) $autotime = $task['end'];
		if( $task['start'] > TIMESTAMP ) $autotime = $task['start'];
		
		// 是否已抢过
		$mystatus = model_task::getStatusInTask( $task,$task['isarealimit'],false );
//var_dump($mystatus );
		if( $mystatus['status'] == 1 ){
			$myreply = $mystatus['reply'];
		}

		// 是否抢过，用于显示图片
		$istaked = pdo_get('zofui_tasktb_taked',array('uniacid'=>$_W['uniacid'],'taskid'=>$task['id'],'userid'=>$userinfo['uid']));
		
		if( $task['continue'] == 1 ) {
			$where = array('uniacid'=>$_W['uniacid'],'continueid'=>$task['continueid']);
			$continuenum = Util::countDataNumber('zofui_tasktb_task',$where);
			$continue = pdo_get('zofui_tasktb_continue',array('uniacid'=>$_W['uniacid'],'id'=>$task['continueid']));
		}
		
		//是否可投诉
		if( $task['userid'] != $userinfo['uid'] )$iscomplain = model_task::isCanComplain( $task['id'] );

		if( $task['userid'] == $userinfo['uid'] ){ // 是发布者
			// 下载链接
			if( $_W['set']['iscandownload'] == 1 ){
				$code = md5( $task['userid'].$task['id'].$_W['account']['secret'].$_W['config']['setting']['authkey'] );
				$surl = Util::createModuleUrl('downtask',array('id'=>$task['id'],'code'=>$code));

				$downurl = Util::shortUrl( $surl );
			}
		}

		if( $task['istaskform'] == 1 ) {
			$form = pdo_get('zofui_tasktb_taskform',array('id'=>$task['formid']));
			$form['form'] = iunserializer( $form['form'] );
		}

		if( $task['isread'] == 1 ){
			if($userinfo['uid'] != $task['userid']){
				$isread = pdo_get('zofui_tasktb_anwread',array('uid'=>$userinfo['uid'],'taskid'=>$task['id'],'endtime >'=>TIMESTAMP));
			} 
			$allread = pdo_count('zofui_tasktb_anwread',array('taskid'=>$task['id']));
			$allfee = Util::countDataSum('zofui_tasktb_anwread',array('taskid'=>$task['id']),' SUM(`cost`) ');
		}

		// 是否已查看
		if( $_W['set']['rded'] == 1 ){
			$isrd = pdo_get('zofui_tasktb_readtask',array('type'=>0,'tid'=>$task['id'],'uid'=>$userinfo['uid']));
			if( empty($isrd) ){
				pdo_insert('zofui_tasktb_readtask',array('type'=>0,'tid'=>$task['id'],'uid'=>$userinfo['uid']));
			}
		}

		
	}else if( $task['type']  == 1 ){ // 试用任务
		
		$task['taked'] = Util::countDataNumber('zofui_tasktb_usetasklog',array('taskid'=>$task['id'],'isactivity'=>0));

		if( $task['end'] > TIMESTAMP ) $autotime = $task['end'];
		if( $task['start'] > TIMESTAMP ) $autotime = $task['start'];

		$waitday = $_W['set']['usecounttime'] > 0 ? $_W['set']['usecounttime'] : 3;
		$counttime = $task['end'] + $waitday*24*3600;

		$usetaskautotime = $_W['set']['useautotime'] > 0 ? $_W['set']['useautotime'] : 12;

		if( TIMESTAMP < $counttime && TIMESTAMP > $task['end'] ) $autotime = $counttime;

		$istaked = pdo_get('zofui_tasktb_usetasklog',array('uniacid'=>$_W['uniacid'],'taskid'=>$task['id'],'userid'=>$userinfo['uid']));
		if( !empty( $istaked['subcontent'] ) ){
			$istaked['subcontent'] = iunserializer( $istaked['subcontent'] );

			$addcontent = pdo_getall('zofui_tasktb_useaddcontent',array('takedid'=>$istaked['id']));
			if( !empty( $addcontent ) ){
				foreach ($addcontent as &$v) {
					$v['img'] = iunserializer( $v['img'] );
				}
				unset( $v );
			}
		}

		if( !empty( $istaked['initcontent'] ) ){
			$istaked['initcontent'] = iunserializer( $istaked['initcontent'] );

			if( !empty( $istaked['initcontent']['images'] ) ){
				foreach ($istaked['initcontent']['images'] as &$vvv) {
					$vvv = tomedia( $vvv );
				}
				unset( $vvv );
			}

		}

		$mycomplain = pdo_get('zofui_tasktb_complain',array('uniacid'=>$_W['uniacid'],'userid'=>$userinfo['uid'],'taskid'=>$task['id']));
		
		if( !empty( $mycomplain['images'] ) ){
			$mycomplain['images'] = iunserializer( $mycomplain['images'] );
		}

		if( $task['findtype'] == 1 ){
			$task['findkey'] = iunserializer( $task['findkey'] );
			if( is_array( $task['findkey'] ) ){
				shuffle( $task['findkey'] );
			}

			preg_match('/&id=(\d+)/', $task['link'], $id);
			$gid = $id[1];
			
			if( empty( $id ) ){
				preg_match('/\?id=(\d+)/', $rr, $id);
				$gid = $id[1];					
			}
		}
		
		// 步骤
		if( !empty( $this->module['config']['usetaskstep'] ) ){
			$usetaskstep = htmlspecialchars_decode( $this->module['config']['usetaskstep'] );
			$usetaskstep = str_replace('{pay}', $task['paymoney'], $usetaskstep);
			$usetaskstep = str_replace('{get}', $task['money'], $usetaskstep);
		}
		
	}

	// 留言
	$where = array('uniacid'=>$_W['uniacid'],'taskid'=>$task['id'],'parent'=>0);
	$messagenum = Util::countDataNumber('zofui_tasktb_taskmessage',$where);

	$warn = 0;
	if( $_W['set']['warn'] > 0 ){
		$where = array('uniacid'=>$_W['uniacid'],'taskid'=>$task['id']);
		$complain = Util::countDataNumber('zofui_tasktb_complain',$where);
		$per = $complain/$task['num']*100;
		if( $per >= $_W['set']['warn'] ) $warn = 1;
	}
	
	// 浏览量
	Util::addOrMinusOrUpdateData('zofui_tasktb_task',array('scan'=>1),$task['id']);

	$_SESSION['isinweb'] = 1;

	// 会员等级
	$level = model_user::levelRes($userinfo,$this->module['config']);
	if( $level == 1 ){
		$_W['set']['commonserver'] = $_W['set']['commonservera'];
		$_W['set']['commonserverleast'] = $_W['set']['commonserverleasta'];
	}
	if( $level == 2 ){
		$_W['set']['commonserver'] = $_W['set']['commonserverb'];
		$_W['set']['commonserverleast'] = $_W['set']['commonserverleastb'];
	}
	
	$mustnick = 0;
	if( $_W['dev'] == 'wap' && ( empty($userinfo['nickname']) || empty($userinfo['headimgurl']) ) && empty($_W['set']['mustnick']) ){
		$mustnick = 1;
	}

	$settings = array(
		'sharetitle' => $task['title'],
		'sharedesc' => $task['title'],
		'shareimg' => tomedia($this->module['config']['shareimg']),
		'sharelink' => Util::createModuleUrl('task',array('id'=>$_GPC['id'],'zfuid'=>$userinfo['id'])),
		'taskid' => $task['id'],
		'pid' => $task['id'],
		'do' => 'task',
		'tasktype' => $task['type'],
		'op' => 'list',
		'pagetype' => $_W['set']['tlshow'] == 1 && $userinfo['uid'] != $task['userid'] ? 1 : $_GPC['type'],
		'title' => $task['title'],
		'open' => $_GPC['open'],
		'commonserver' => sprintf('%.2f',$_W['set']['commonserver']),
		'commonserverleast' => sprintf('%.2f',$_W['set']['commonserverleast']),
		'continue' => intval( $task['continue'] ),
		'continuemoney' => $continue['money'],
		'money' => sprintf('%.2f',$task['money']),
		'agent' => $_W['agent'],
		'continueid' => $task['continueid'],
		'cname' => $_W['cname'],
		'scroll' => $isadmin || $userinfo['uid'] == $task['userid'] ? 1 : 0,
		'gotype' => $_W['set']['gotaobaotype'],
		'taourl' => $task['type'] == 1 ? urlencode( $task['link'] ) : '',
		'taokey' => $task['taokey'],
		'findtype' => $task['findtype'],
		'islimit' => $task['isarealimit'] > 0 ? $_W['islimit'] : 0,
		'downurl' => $downurl,
		'isform' => intval( $task['isform'] ),
		'istaskform' => $task['istaskform'] == 1 && !empty( $form['form'] ) ? 1 : 0,
		'readprice' => $task['readprice'],
		'backed' => $task['backmoney'],
		'taketip' => intval($_W['set']['taketip']),
	);

	if( $settings['taketip'] == 1 ){
		$takerule = pdo_get('zofui_tasktb_instructa',array('uniacid'=>$_W['uniacid'],'type'=>2));
	}

	if( !empty($_GPC['idcode']) ){
		$settings['sharelink'] = Util::createModuleUrl('task',array('idcode'=>$_GPC['idcode'],'zfuid'=>$userinfo['id']));
	}
	
	if( $task['type']  == 0 ){
		include $this->template ('task');
	}else if( $task['type']  == 1 ){
		include $this->template ('usetaskinfo');
	}


