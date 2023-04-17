<?php 
	global $_W,$_GPC;
	if( empty( $_GPC['code'] ) || $this->module['config']['iscandownload'] == 0 ) die;


	if( !empty( $_GPC['id'] ) ) {

		$task = pdo_get('zofui_tasktb_task',array('uniacid'=>$_W['uniacid'],'id'=>$_GPC['id']),array('id','title','type','puber','userid'));
		if( empty( $task ) ) message('任务不存在');

		$code = md5( $task['userid'].$task['id'].$_W['account']['secret'].$_W['config']['setting']['authkey'] );
		if( $code != $_GPC['code'] ) message('错误的链接');

		if( empty( $_GPC['op'] ) ) {

			$where = array('uniacid'=>$_W['uniacid'],'taskid'=>$task['id']);
			$where['status>'] = 0.1;
			$page = $_GPC['page'] > 0 ? $_GPC['page'] : 1;
			
			$order = ' `id` DESC ';

			$by = ' id,createtime,openid,status,content,images,isscan,userid ';
			$info = Util::getAllDataInSingleTable('zofui_tasktb_taked',$where,$_GPC['page'],10,$order,false,true,$by);
			
			if( !empty( $info[0] ) ) {

				foreach ( $info[0] as $k => &$v ) {

					$v['no'] = ( $page-1 )*10 + $k + 1;
		 			$v['user'] = model_user::getSingleUser( $v['userid'] );

		 			$v['statusstr'] = '';
		 			if( $v['status'] == 1 ) $v['statusstr'] = '待采纳';
		 			if( $v['status'] == 2 ) $v['statusstr'] = '已采纳';
		 			if( $v['status'] == 3 ) $v['statusstr'] = '被拒绝';
		 			$v['time'] = date('Y-m-d H:i:s',$v['createtime']);

		 			$v['images'] = iunserializer( $v['images'] );

		 			if( !empty( $v['images'] ) ) {
		 				foreach ($v['images'] as $kk => $vv) {
		 					$v['images'][$kk] = tomedia( $vv );
		 				}
		 			}

					$addlist = pdo_getall('zofui_tasktb_remindlog',array('takedid'=>$v['id'],'mtype'=>1),array('content','createtime','images'));

					if( !empty( $addlist ) ) {

						foreach ( $addlist as &$vv ) {
							$vv['time'] = date('Y-m-d H:i:s',$vv['createtime']);
							$vv['images'] = iunserializer( $vv['images'] );
				 			if( !empty( $vv['images'] ) ) {
				 				foreach ($vv['images'] as $kkk => $vvv) {
				 					$vv['images'][$kkk] = tomedia( $vvv );
				 				}
				 			}
						}
						unset( $vv );
					}
					$v['addlist'] = $addlist;

				}

			}

			$title = $task['title'];
			$downurl = Util::createModuleUrl('downtask',array('id'=>$task['id'],'code'=>$code,'op'=>'down'));


			$user = model_user::getSingleUser( $task['userid'] );
			$code = md5( $user['uid'].$user['id'].$_W['account']['secret'].$_W['config']['setting']['authkey'] );
			$backurl = Util::createModuleUrl('downtask',array('uid'=>$user['id'],'code'=>$code));

		}elseif( $_GPC['op'] == 'down' ){

			model_task::downTask( $task['id'],'app',$this->module['config'] );
		}


	}elseif( !empty( $_GPC['uid'] ) ){

		$user = pdo_get('zofui_task_user',array('id'=>$_GPC['uid']));
		if( empty( $user ) ) message('错误的链接');

		$code = md5( $user['uid'].$user['id'].$_W['account']['secret'].$_W['config']['setting']['authkey'] );
		if( $_GPC['code'] != $code ) message('错误的链接');

		$where = array('uniacid'=>$_W['uniacid'],'userid'=>$user['uid'],'type'=>0);

		$order = ' `id` DESC ';

		$by = ' id,title,status,iscount ';
		$info = Util::getAllDataInSingleTable('zofui_tasktb_task',$where,$_GPC['page'],10,$order,false,true,$by);

		$title = '任务列表';

		if( !empty( $info[0] ) ) {
			foreach ( $info[0] as &$v ) {
				$v['code'] = md5( $user['uid'].$v['id'].$_W['account']['secret'].$_W['config']['setting']['authkey'] );
			}

		}

	}

	include $this->template('downtask');