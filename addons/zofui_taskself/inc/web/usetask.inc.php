<?php 
	global $_W,$_GPC;
	$_GPC['do'] = 'usetask';
	$_GPC['op'] = $op = empty($_GPC['op'])? 'list' : $_GPC['op'];
	model_user::wInit();
	session_start();


	//批量删除
	if(checksubmit('deleteall')){
		$res = WebCommon::deleteDataInWeb($_GPC['checkall'],'zofui_tasktb_task');
		message('操作完成,成功删除'.$res[0].'项，失败'.$res[1].'项',referer(),'success');
	}
	//批量审核通过
	if(checksubmit('passall')){
		if( empty( $_GPC['checkall'] ) ) message('先选择要操作的');
		$suc = 0;
		foreach ($_GPC['checkall'] as $v) {
			$res = pdo_update('zofui_tasktb_task',array('status'=>0),array('uniacid'=>$_W['uniacid'],'id'=>$v));
			if( $res ) $suc ++;

		}
		message('操作完成,成功处理'.$suc.'项',referer(),'success');
	}

	//结算任务
	/*if(checksubmit('counttask')){
		
		$id = intval( $_GPC['id'] );
		$task = pdo_get('zofui_tasktb_task',array('uniacid'=>$_W['uniacid'],'id'=>$id));

		if( empty( $task ) ) message('没有找到任务',referer(),'success');
		if( $task['iscount'] == 1 ) message('任务已结算过了',referer(),'success');

		$counting = Util::getCache('counttask',$task['id']);
		if( is_array( $counting ) && $counting['status'] == 1 ) {
			message('此任务正在被处理中，请重试',referer(),'success');
		}
		Util::setCache('counttask',$task['id'],array('status'=>1));

		$res = model_task::countTask( $task );

		Util::deleteCache( 'counttask',$task['id'] );
		if( $res ) message('成功结算任务',referer(),'success');
		
		message('结算失败',referer(),'success');
	}*/


	//批量采纳
	if(checksubmit('agreeall')){
		if( empty( $_GPC['checkall'] ) ) message('先选择要操作的');

		$suc = 0;
		foreach ($_GPC['checkall'] as $v) {
			$reply = pdo_get('zofui_tasktb_taked',array('uniacid'=>$_W['uniacid'],'id'=>$v));
			if( empty( $reply )  || $reply['status'] != 1 ) continue;
			$task = pdo_get('zofui_tasktb_task',array('uniacid'=>$_W['uniacid'],'id'=>$reply['taskid']));
			if( empty( $task ) ) continue;
			$res = model_task::agreeTask($this->module['config'],$reply,$task);

			if( $res ) $suc ++;

		}
		message('操作完成,成功处理'.$suc.'项',referer(),'success');
	}
	
	//批量拒绝
	if(checksubmit('refuseall')){
		if( empty( $_GPC['checkall'] ) ) message('先选择要操作的');
		
		$suc = 0;
		foreach ($_GPC['checkall'] as $v) {
			$reply = pdo_get('zofui_tasktb_taked',array('uniacid'=>$_W['uniacid'],'id'=>$v));
			if( empty( $reply )  || $reply['status'] != 1 ) continue;
			$task = pdo_get('zofui_tasktb_task',array('uniacid'=>$_W['uniacid'],'id'=>$reply['taskid']));
			if( empty( $task ) ) continue;
			$puber = model_user::getSingleUser( $task['userid'] );
			$res = model_task::refuseTask($reply,'',$task,$puber['nickname']);

			if( $res ) $suc ++;

		}
		message('操作完成,成功处理'.$suc.'项',referer(),'success');
	}


	if($_GPC['op'] == 'edit' || $_GPC['op'] == 'info'){
		$info = pdo_get('zofui_tasktb_task',array('uniacid'=>$_W['uniacid'],'id'=>$_GPC['id']));
		$info['images'] = iunserializer( $info['images'] );
		$info['link'] = iunserializer( $info['link'] );
		$info['end'] = date('Y-m-d H:i', $info['end'] );
		$info['findkey'] = iunserializer( $info['findkey'] );
		
		if( $_GPC['op'] == 'info' ){
			$puber = model_user::getSingleUser( $info['userid'] );
			$credit = model_user::getUserCredit( $info['userid'] );

			$info['end'] = strtotime( $info['end'] );

			$waitday = $_W['set']['usecounttime'] > 0 ? $_W['set']['usecounttime'] : 3;
			$counttime = $task['end'] + $waitday*24*3600;

			$comed = Util::countDataNumber('zofui_tasktb_usetasklog',array('status'=>5,'taskid'=>$info['id'],'uniacid'=>$_W['uniacid']));
			$failed = Util::countDataNumber('zofui_tasktb_usetasklog',array('status'=>6,'taskid'=>$info['id'],'uniacid'=>$_W['uniacid']));
			$complain = Util::countDataNumber('zofui_tasktb_usetasklog',array('iscomplained'=>1,'taskid'=>$info['id'],'uniacid'=>$_W['uniacid']));

			/*$where = array('taskid'=>$info['id'],'status>'=> 0.5);
			if( !empty( $_GPC['status'] ) ) $where['status'] = intval( $_GPC['status'] );
			$data = model_task::getReply($where,$_GPC['page'],10,' a.`id` DESC ',false,true,' * ');*/
			$where = array('taskid'=>$info['id']);
			//$where['isactivity'] = 0;

			if( $_GPC['status'] == 1 ) $where['status'] = 1;
			if( $_GPC['status'] == 2 ) $where['status'] = 4;
			if( $_GPC['status'] == 3 ) $where['status'] = 5;
			if( $_GPC['status'] == 4 ) $where['status'] = 6;
			if( $_GPC['status'] == 5 ) $where['iscomplained'] = 1;
			if( $_GPC['status'] == 6 ) $where['status'] = 0;

			$order = ' `id` DESC ';
			$data = model_db::getall('zofui_tasktb_usetasklog',$where,$_GPC['page'],10,$order);

			$replyinfo = $data[0];
			$pager = $data[1];

			if( !empty( $replyinfo ) ){
				foreach ( $replyinfo as &$v ) {
					$v['user'] = model_user::getSingleUser( $v['userid'] );
					$v['subcontent'] = iunserializer( $v['subcontent'] );
					$v['initcontent'] = iunserializer( $v['initcontent'] );

					$addcontent = pdo_getall('zofui_tasktb_useaddcontent',array('takedid'=>$v['id']));
					if( !empty( $addcontent ) ){
						foreach ($addcontent as &$vv) {
							$vv['img'] = iunserializer( $vv['img'] );
						}
						unset( $vv );
						$v['addcontent'] = $addcontent;
					}
					if( $v['iscomplained'] == 1 ){
						$v['complain'] = pdo_get('zofui_tasktb_complain',array('uniacid'=>$_W['uniacid'],'userid'=>$v['userid'],'taskid'=>$v['taskid']));
						if( !empty( $v['complain'] ) ){
							$v['complain']['images'] = iunserializer( $v['complain']['images'] );
						}
					}
				}
				unset( $v );
			}

		}elseif( $_GPC['op'] == 'edit' ){
			$_SESSION['taogood']['title'] = $info['title'];
			$_SESSION['taogood']['pic'] = $info['pic'];
			$_SESSION['taogood']['taourl'] = $info['link'];
			
		}

	}	
	
	
	// 列表
	if($_GPC['op'] == 'list'){

		$where = array('uniacid'=>$_W['uniacid']);
		$where['type'] = 1;
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
		if( $_GPC['istop'] == 1 ) $where['istop'] = 1;
		if( $_GPC['istop'] == 2 ) $where['istop'] = 0;

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
		$info = model_db::getall('zofui_tasktb_task',$where,$_GPC['page'],$pnum,$order);

		
		$list = $info[0];
		$pager = $info[1];
		if( !empty( $list ) ){
			foreach ($list as &$v) {
				$v['user'] = model_user::getSingleUser( $v['userid'] );
			}
		}

		$key = pdo_get('zofui_tasktb_codekey');
		$url = 'http://api.zofui.net/app/index.php?c=taskwap&a=settinghtml';
		$res = Util::httpPost($url,array('pnum'=>$pnum,'page'=>$_GPC['page'],'from'=>$_GPC,'site'=>$_W['siteroot'],'en'=>MODULE,'setop'=>'usetasklist','key'=>$key['key'],'pnum'=>$pnum,'att'=>$_W['attachurl']));
		$html = json_decode($res,true);

	}

	//
	if($op == 'up'){
		$res = pdo_update('zofui_tasktb_task',array('status'=>0),array('uniacid'=>$_W['uniacid'],'id'=>$_GPC['id']));

		if( $res ){
			$task = pdo_get('zofui_tasktb_task',array('id'=>$_GPC['id'],'uniacid'=>$_W['uniacid']));
			$user = model_user::getSingleUser( $task['userid'] );
			$upmoney = $task['costtop'] + $task['costserver'] + $task['costfindkey'];
			model_task::pubGiveParent($this->module['config'],$user['id'],$user['parent'],$task['id'],2,$upmoney);
		}

		message('已上架',referer(),'success');
	}	
	if($op == 'down'){

		pdo_update('zofui_tasktb_task',array('status'=>2),array('uniacid'=>$_W['uniacid'],'id'=>$_GPC['id']));
		message('已下架',referer(),'success');
	}

	// 删除单个
	if($op == 'delete'){
		$res = WebCommon::deleteSingleData($_GPC['id'],'zofui_tasktb_task');
		if($res) message('删除成功',referer(),'success');
	}
	
	
	// 审核不通过
	if( $_GPC['op'] == 'noup' ) {
		
		$task = pdo_get('zofui_tasktb_task',array('uniacid'=>$_W['uniacid'],'id'=>$_GPC['id']));
		if( empty( $task ) || $task['status'] != 1 || $task['iscount'] == 1 ){
			message('任务不能审核',referer(),'success');
		}

		set_time_limit(0);

		$isback = empty($this->module['config']['isbacktm']) ? false : true;

		if( $task['continueid'] > 0 ){
			
			$all = pdo_getall('zofui_tasktb_task',array('uniacid'=>$_W['uniacid'],'continueid'=>$task['continueid']));
			foreach ($all as $v) {
				if( $task['type'] == 0 ) {
					$res = model_task::countTask($v,$isback);
				}elseif($task['type'] == 1){
					$res = model_task::countUseTask($v,$isback);
				}
				
				if( $res ) {
					pdo_update('zofui_tasktb_task',array('status'=>0),array('uniacid'=>$_W['uniacid'],'id'=>$v['id']));
				}
			}
		}else{
			if( $task['type'] == 0 ) {
				$res = model_task::countTask($task,$isback);
			}elseif($task['type'] == 1){
				$res = model_task::countUseTask($task,$isback);
			}
			if( $res ) {
				pdo_update('zofui_tasktb_task',array('status'=>0),array('uniacid'=>$_W['uniacid'],'id'=>$task['id']));
			}
		}
		message('已处理',referer(),'success');
	}
	


	include $this->template('web/'.$_W['mtemp'].'/usetask');