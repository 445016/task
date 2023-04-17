<?php 
	global $_W,$_GPC;
	$_GPC['do'] = 'tbtask';
	$_GPC['op'] = $op = empty($_GPC['op'])? 'list' : $_GPC['op'];
	$_W['set'] = $this->module['config'];



	

	//批量删除
	if(checksubmit('deleteall')){
		$res = WebCommon::deleteDataInWeb($_GPC['checkall'],'zofui_tasktb_tbtask');
		message('操作完成,成功删除'.$res[0].'项，失败'.$res[1].'项',referer(),'success');
	}

	// 批量删除
	if(checksubmit('deletealltaked')){
		$res = WebCommon::deleteDataInWeb($_GPC['checkall'],'zofui_tasktb_tbtaked');
		message('操作完成,成功删除'.$res[0].'项，失败'.$res[1].'项',referer(),'success');
	}	
	
	// 批量删除
	if(checksubmit('deletetbform')){
		$res = WebCommon::deleteDataInWeb($_GPC['checkall'],'zofui_tasktb_tbform');
		message('操作完成,成功删除'.$res[0].'项，失败'.$res[1].'项',referer(),'success');
	}	
	

	
	if($_GPC['op'] == 'edit' || $_GPC['op'] == 'info'){
		$info = model_tbtask::getTask( $_GPC['id'] );

		$step = array();
		foreach ( $info['step'] as $v ) {
			$step[] = $v['step'];
		}
		
		
		if( $_GPC['op'] == 'info' ){
			$puber = model_user::getSingleUser( $info['userid'] );
			$credit = model_user::getUserCredit( $info['userid'] );

			//$info['end'] = strtotime( $info['end'] );

			$alltaked = pdo_getall('zofui_tasktb_tbtaked',array('uniacid'=>$_W['uniacid'],'taskid'=>$info['id']),array('id','status','isend','step'));

			$totaltaked = $comed = $failed = $complained = 0;
			if( !empty( $alltaked ) ) {
				foreach ( $alltaked as $v ) {

					$totaltaked ++;
					if( $v['status'] == 3 || $v['status'] == 9 ) $comed ++;
					if( $v['status'] == 5 || $v['status'] == 8 ) $failed ++;
					if( $v['status'] == 6 || $v['status'] == 7 ) $complained ++;
					
				}
			}

			$where = array('taskid'=>$info['id']);
			$andstr = '';
			
			if( !empty( $_GPC['takedid'] ) ) $where['id'] = intval( $_GPC['takedid'] );
			
			if( isset( $_GPC['status'] ) ) $where['status'] = intval( $_GPC['status'] );

			if( $_GPC['status'] == 3 ) {
				unset( $where['status'] );
				$andstr = ' AND `status` IN(3,9) ';
			}
			if( $_GPC['status'] == 5 ) {
				unset( $where['status'] );
				$andstr = ' AND `status` IN(5,8) ';
			}

			$pnum = intval( $_GPC['pnum'] ) > 0 ? intval( $_GPC['pnum'] ) : 10;
			
			$data = model_db::getall('zofui_tasktb_tbtaked',$where,$_GPC['page'],$pnum,' `id` DESC ',' * ',$andstr);


			$alltaked = $data[0];
			$pager = $data[1];

			if( !empty( $alltaked ) ){
				foreach ( $alltaked as &$v ) {
					$v['tasklog'] = model_tbtask::structTakedStep( $v );
					$v['user'] = model_user::getSingleUser( $v['userid'] );
				}
			}

		}

	}	
	

	
	// 列表
	if($_GPC['op'] == 'list'){


		$where = array('uniacid'=>$_W['uniacid']);
		if( $_GPC['status'] == 1 ){ // 审核中
			$where['status'] = 1;
		}
		if( $_GPC['status'] == 2 ){ // 正常的
			$where['status'] = 0;
		}
		if( $_GPC['status'] == 3 ){ // 被下架
			$where['status'] = 2;
		}
		if( $_GPC['iscount'] == 1 ){ 
			$where['iscount'] = 1;
		}		
		if( $_GPC['iscount'] == 2 ){
			$where['iscount'] = 0;
		}	
		if( $_GPC['istop'] == 1 ) $where['topendtime>'] = TIMESTAMP;
		if( $_GPC['istop'] == 2 ) $where['topendtime<'] = TIMESTAMP;

		if( $_GPC['status'] == 4 ){ // 进行中
			$where['status'] = 0;
			$where['iscount'] = 0;
			$where['isempty'] = 0;
			$where['start<'] = TIMESTAMP;
		}

		$pnum = $_GPC['pnum'] <= 10 ? 10 : $_GPC['pnum'];

		$str = '';
		if( !empty( $_GPC['userid'] ) ) {
			$user = pdo_get('zofui_task_user',array('id'=>$_GPC['userid']));
			$where['userid'] = $user['uid'];
		}
		if( !empty( $_GPC['taskid'] ) ) $where['id'] = $_GPC['taskid'];


		$order = ' `id` DESC ';
		$info = model_db::getall('zofui_tasktb_tbtask',$where,$_GPC['page'],$pnum,$order,' * ');
		
		$list = $info[0];
		$pager = $info[1];
		if( !empty( $list ) ){
			foreach ($list as &$v) {
				if(  !empty( $v['userid'] ) ) $v['user'] = model_user::getSingleUser( $v['userid'] );
				$v['addtoptime'] = model_tbtask::countTopTime( $v );
			}
		}

		$key = pdo_get('zofui_tasktb_codekey');
		$url = 'http://api.zofui.net/app/index.php?c=taskwap&a=settinghtml';
		$res = Util::httpPost($url,array('pnum'=>$pnum,'page'=>$_GPC['page'],'from'=>$_GPC,'site'=>$_W['siteroot'],'en'=>MODULE,'setop'=>'tbtasklist','key'=>$key['key'],'pnum'=>$pnum,'att'=>$_W['attachurl']));
		$html = json_decode($res,true);

	}

	// 列表
	if($_GPC['op'] == 'take'){

		$where = array('uniacid'=>$_W['uniacid']);
		
		$andstr = '';
		if( $_GPC['status'] == 1 ) $where['status'] = 6;
		if( $_GPC['status'] == 2 ) $where['status'] = 7;
		if( $_GPC['status'] == 3 ) $where['status'] = 1;
		if( $_GPC['status'] == 4 ) {
			$andstr = ' AND `status` IN(3,9) ';
		}
		if( $_GPC['status'] == 5 ){
			$andstr = ' AND `status` IN(5,8) ';
		}

		if( !empty( $_GPC['taskid'] ) ) $where['taskid'] = intval( $_GPC['taskid'] );
		if( !empty( $_GPC['takedid'] ) ) $where['id'] = intval( $_GPC['takedid'] );

		$order = ' `id` DESC ';	
		$pnum = $_GPC['pnum'] <= 10 ? 10 : $_GPC['pnum'];

		$info = model_db::getall('zofui_tasktb_tbtaked',$where,$_GPC['page'],$pnum,$order,' * ',$andstr);

		$list = $info[0];
		$pager = $info[1];

		if( !empty( $list ) ) {

			foreach ( $list as &$v ) {
				$v['task'] = model_tbtask::getTask( $v['taskid'] );
			}

		}

		$key = pdo_get('zofui_tasktb_codekey');
		$url = 'http://api.zofui.net/app/index.php?c=taskwap&a=settinghtml';
		$res = Util::httpPost($url,array('pnum'=>$pnum,'page'=>$_GPC['page'],'from'=>$_GPC,'site'=>$_W['siteroot'],'en'=>MODULE,'setop'=>'tbtakeList','key'=>$key['key'],'pnum'=>$pnum,'att'=>$_W['attachurl']));
		$html = json_decode($res,true);


	
	}elseif( $_GPC['op'] == 'temp' ){

		$where = array('uniacid'=>$_W['uniacid']);

		$order = ' `id` DESC ';	

		$info = model_db::getall('zofui_tasktb_tbform',$where,$_GPC['page'],10,$order);
		
		$list = $info[0];
		$pager = $info[1];

	}



	// 删除单个
	if($op == 'up'){
		$res = pdo_update('zofui_tasktb_tbtask',array('status'=>0),array('uniacid'=>$_W['uniacid'],'id'=>$_GPC['id']));

		if( $res ){
			$task = pdo_get('zofui_tasktb_tbtask',array('id'=>$_GPC['id'],'uniacid'=>$_W['uniacid']));
			$user = model_user::getSingleUser( $task['userid'] );
			$upmoney = $task['costtop'] + $task['costserver'] + $task['costka'];
			model_task::pubGiveParent($this->module['config'],$user['id'],$user['parent'],$task['id'],3,$upmoney);
		}

		Util::deleteCache('tbtask',$_GPC['id']);
		message('已上架',referer(),'success');
	}	
	if($op == 'down'){

		pdo_update('zofui_tasktb_tbtask',array('status'=>2),array('uniacid'=>$_W['uniacid'],'id'=>$_GPC['id']));
		Util::deleteCache('tbtask',$_GPC['id']);
		message('已下架',referer(),'success');
	}

	// 删除单个
	if($op == 'delete'){
		$res = WebCommon::deleteSingleData($_GPC['id'],'zofui_tasktb_tbtask');
		message('已删除',referer(),'success');
	}

	// 删除单个
	if($op == 'deletetbform'){
		$res = WebCommon::deleteSingleData($_GPC['id'],'zofui_tasktb_tbform');
		message('已删除',referer(),'success');
	}	
	

	if($op == 'deletetaked'){

		$res = WebCommon::deleteSingleData($_GPC['id'],'zofui_tasktb_tbtaked');
		message('已删除',referer(),'success');
	}	

	if( $op == 'giveworker' || $op == 'giveboss' ) { // 判给雇员

		$taked = pdo_get('zofui_tasktb_tbtaked',array('uniacid'=>$_W['uniacid'],'id'=>$_GPC['id'],'status'=>6));
		if( empty( $taked ) || $taked['isend'] == 1 || $taked['status'] != 6 ) message('未找到任务',referer(),'error');

		$task = model_tbtask::getTask( $taked['taskid'] );
		if( empty( $task ) ) message('未找到任务',referer(),'error');

		$complainstep = 1;
		if( $op == 'giveboss' ) $complainstep = 2;

		$res = pdo_update('zofui_tasktb_tbtaked',array('status'=>7,'adminsettime'=>TIMESTAMP,'complainstep'=>$complainstep),array('id'=>$taked['id']));

		if( $res ){
			// 发消息
			$tbautotime = empty( $_W['set']['tbautotime7'] ) ? 24 : $_W['set']['tbautotime7'];
			$lasttime = TIMESTAMP + $tbautotime*3600;
			$lasttime = Util::lastTime( $lasttime );

			if( $op == 'giveboss' ){

				Message::admingiveTbtask($taked['userid'],$taked['openid'],$task['id'],$task['title'],$lasttime,1);
				Message::admingiveTbtask($taked['pubuid'],$taked['puber'],$task['id'],$task['title'],$lasttime,2);

			}elseif( $op == 'giveworker' ){

				Message::admingiveTbtask($taked['userid'],$taked['openid'],$task['id'],$task['title'],$lasttime,2);
				Message::admingiveTbtask($taked['pubuid'],$taked['puber'],$task['id'],$task['title'],$lasttime,1);

			}
			

			message('已审判',referer(),'success');
		}
		message('审判失败',referer(),'error');
	}


	if( $op == 'endgiveworker' || $op == 'endgiveboss' ) { // 判给雇员

		$taked = pdo_get('zofui_tasktb_tbtaked',array('uniacid'=>$_W['uniacid'],'id'=>$_GPC['id'],'status'=>7));
		if( empty( $taked ) || $taked['isend'] == 1 || $taked['status'] != 7 ) message('未找到任务',referer(),'error');

		$task = model_tbtask::getTask( $taked['taskid'] );
		if( empty( $task ) ) message('未找到任务',referer(),'error');

		
		if( $op == 'endgiveworker' ){ // 任务完成

			$res = model_tbtask::comTbtask( $taked,$task,9 );
		
		}elseif( $op == 'endgiveboss' ){ //任务失败

			$res = model_tbtask::confirmFailTbtask( $taked,$task,8 );
		} 
		
		if( $res ){

			message('已判',referer(),'success');
		}
		message('判定失败',referer(),'error');
	}

	include $this->template('web/'.$_W['mtemp'].'/tbtask');