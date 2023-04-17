<?php 
	global $_W,$_GPC;
	$_W['set'] = $this->module['config'];
	set_time_limit(0);

	if( $_GPC['op'] == 'start' ){

		if( empty( $_GPC['code'] ) || empty( $_GPC['taskid'] ) ) die;
		$taskid = intval( $_GPC['taskid'] );
		if( empty( $taskid ) ) die;
		
		// 验证
		$auth = md5( $_W['set']['sendmessauth'].$_W['authkey'].$taskid );
		if( $_GPC['code'] != $auth ) die;
		
		$flag = MODULE_ROOT.'/userfile/'.$_W['uniacid'].'/flag.file';
	
		if( !file_exists( $flag ) ) { // 没有存在文件
			$res = saveUser();
			$page = $res['page'];
			$members = $res['mem'];
			
		}else{ // 已经有文件
			
			$content = file_get_contents( $flag );
			$fansarray = unserialize( $content );

			if( $fansarray['time'] <= (TIMESTAMP - 3600*2) ) { // 2个小时更新一次

				$res = saveUser();
				$page = $res['page'];
				$members = $res['mem'];

			}else{ // 未过期

				$page = $fansarray['page'];
				$num = $fansarray['num'];
				$members = $fansarray['mem'];
			}

		}

		if( $members > 0 ) {

			// 缓存次数和类型
			$sendinfo = Util::getCache('sendmess','all');
			if( empty( $sendinfo ) ) die;
			$sendinfo['total'] = $members;
			Util::setCache('sendmess','all',$sendinfo);

			for ($i=1; $i <= $page; $i++) {
				$code = md5( $taskid.$i.$_W['set']['sendmessauth'].$_W['authkey'] );
				$url = Util::createModuleUrl('sendmess',array('op'=>'send','code'=>$code,'page'=>$i,'taskid'=>$taskid));

				$urlParmas = parse_url($url);
				$host = $urlParmas['host'];
				$path = $urlParmas['path'];
				$port = isset($urlParmas['port'])? $urlParmas['port'] :80;
				//$fp = fsockopen($host, $port, $errno, $errstr, 30);
				$fp = rf( $host, $port, $errno, $errstr );
				if (!$fp) {
					echo "$errstr ($errno)";die;
				} else {

					$out = "GET ".$url." HTTP/1.1\r\n";
					$out .= "host:".$host."\r\n";
					$out .= "content-length:".strlen($query)."\r\n";
					$out .= "content-type:application/x-www-form-urlencoded\r\n";
					$out .= "connection:close\r\n\r\n";
				 	
					fputs($fp, $out);
					usleep(1000);
					fclose($fp);
					
				}
				
			}
//file_put_contents(MODULE_ROOT."/paramsa.log", var_export( array( $page,date('H:i:s',time()) ) , true).PHP_EOL, FILE_APPEND);			
		}

		
		
		
	//file_put_contents(MODULE_ROOT."/params.log", var_export(2222222222, true).PHP_EOL, FILE_APPEND);
	
	

	}elseif( $_GPC['op'] == 'send' ){

		if(  empty( $_GPC['code'] ) || empty( $_GPC['page'] ) || empty( $_GPC['taskid'] ) ) die;
				
		$auth = md5( $_GPC['taskid'].$_GPC['page'].$_W['set']['sendmessauth'].$_W['authkey'] );
		if( $_GPC['code'] != $auth ) die;

		$taskid = intval( $_GPC['taskid'] );
		$page = intval( $_GPC['page'] );
		if( empty( $taskid ) || empty( $page ) ) die;
		
		$sendinfo = Util::getCache('sendmess','all');
		
		$task = $sendinfo['task'];
		if( empty( $task ) ) die;
		
		// 发消息
		if( $sendinfo['type'] == 1 ){ // 新任务通知

			$file = MODULE_ROOT.'/userfile/'.$_W['uniacid'].'/page'.$page.'.file';
			$content = file_get_contents( $file );
			$fansarray = unserialize( $content );

			$num = 0;
			foreach ( (array)$fansarray as $v ) {
				$num ++;
				//usleep(1000);
				Message::newTaskmess( $v['uid'],$v['openid'],$task['title'],$task['money'],$taskid,$sendinfo['topstr'],$sendinfo['botstr'] );
				
				if( is_int( $num/10 ) ) {
					$sendinfo = Util::getCache('sendmess','all');
					if( empty( $sendinfo ) ) die;
					Util::setCache('singlepro',$page,$num);
				}
			}
						
			Util::setCache('sendmesspro',$page,$num);
			
		}

		$numarr = cache_search('tstb:'.$_W['uniacid'].':sendmesspro');
		// 发消息结束
		if( count( $numarr ) >= $sendinfo['page'] ) {
			Util::deleteCache('sendmess','all');
			cache_clean('tstb:'.$_W['uniacid'].':sendmesspro');
//file_put_contents(MODULE_ROOT."/paramse.log", var_export( array( $page,date('H:i:s',time()) ) , true).PHP_EOL, FILE_APPEND);
		}
	}
	
	die;

	
	// 保存会员数据文件
	function savaUserFile( $uarr,$page,$num,$members ){
		global $_W;
		$userdir = MODULE_ROOT.'/userfile/'.$_W['uniacid'].'/';

		load()->func('file');
		if( is_dir( $userdir ) ){
			rmdirs( $userdir,false );
		}

		mkdirs($userdir);
		
		$flagfile = $userdir.'flag.file';
		file_put_contents($flagfile,serialize( array('time'=>TIMESTAMP,'page'=>$page,'num'=>$num,'mem'=>$members) ));//写入缓存

		for ($i=1; $i <= $page; $i++) { 
			$file = $userdir.'page'.$i.'.file';
			$arr = array_slice($uarr,($i-1)*$num,$num);

			file_put_contents($file,serialize($arr));//写入缓存

		}
		return true;
	}
	
	
	function saveUser(){
		global $_W;

		$where = array('uniacid'=>$_W['uniacid'],'follow'=>1);
		$fans = pdo_getall('mc_mapping_fans',$where,array('openid','fanid'));
		$members = count( $fans );

		$sendinfo = Util::getCache('sendmess','all');
		$page = $sendinfo['page'];

		$num = ceil( $members/$page ); // 每次查询的数量

		$res = savaUserFile( $fans,$page,$num,$members );

		if( $res ) return array('page'=>$page,'num'=>$num,'mem'=>$members);
	}

	function rf($host, $port, $errno, $errstr){
		return fsockopen($host, $port, $errno, $errstr, 30);
	}