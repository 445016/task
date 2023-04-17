<?php 
	global $_W,$_GPC;

	
	set_time_limit(0);

	//////////升级需要更新的
	$all = pdo_getall('zofui_tasktb_complain',array('userid'=>''),array('openid','id','uniacid'));
	if( !empty( $all ) ) {
		foreach ($all as $v) {
			$u = pdo_get('zofui_task_user',array('openid'=>$v['openid'],'uniacid'=>$v['uniacid']),array('uid'));
			if( !empty( $u ) ) {
				pdo_update('zofui_tasktb_complain',array('userid'=>$u['uid']),array('id'=>$v['id']));
			}
		}
	}
	$all = pdo_getall('zofui_tasktb_draw',array('userid'=>''),array('openid','id','uniacid'));
	if( !empty( $all ) ) {
		foreach ($all as $v) {
			$u = pdo_get('zofui_task_user',array('openid'=>$v['openid'],'uniacid'=>$v['uniacid']),array('uid'));
			if( !empty( $v['openid'] ) && !empty( $u ) ) {
				pdo_update('zofui_tasktb_draw',array('userid'=>$u['uid']),array('id'=>$v['id']));
			}
		}
	}
	$all = pdo_getall('zofui_tasktb_mess',array('userid'=>''),array('openid','id','uniacid'));
	if( !empty( $all ) ) {
		foreach ($all as $v) {
			$u = pdo_get('zofui_task_user',array('openid'=>$v['openid'],'uniacid'=>$v['uniacid']),array('uid'));
			if( !empty( $v['openid'] ) && !empty( $u ) ) {
				pdo_update('zofui_tasktb_mess',array('userid'=>$u['uid']),array('id'=>$v['id']));
			}
		}
	}
	$all = pdo_getall('zofui_tasktb_message',array('userid'=>''),array('openid','id','uniacid'));
	if( !empty( $all ) ) {
		foreach ($all as $v) {

			$u = pdo_get('zofui_task_user',array('openid'=>$v['openid'],'uniacid'=>$v['uniacid']),array('uid'));
			
			if( !empty( $v['openid'] ) && !empty( $u ) ) {
				pdo_update('zofui_tasktb_message',array('userid'=>$u['uid']),array('id'=>$v['id']));
			}
		}
	}		
	$all = pdo_getall('zofui_tasktb_moneylog',array('userid'=>''),array('openid','id','uniacid'));
	if( !empty( $all ) ) {
		foreach ($all as $v) {
			$u = pdo_get('zofui_task_user',array('openid'=>$v['openid'],'uniacid'=>$v['uniacid']),array('uid'));
			if( !empty( $v['openid'] ) &&  !empty( $u ) ) {
				pdo_update('zofui_tasktb_moneylog',array('userid'=>$u['uid']),array('id'=>$v['id']));
			}
		}
	}
	$all = pdo_getall('zofui_tasktb_paylog',array('userid'=>''),array('openid','id','uniacid'));
	if( !empty( $all ) ) {
		foreach ($all as $v) {
			$u = pdo_get('zofui_task_user',array('openid'=>$v['openid'],'uniacid'=>$v['uniacid']),array('uid'));
			if( !empty( $v['openid'] ) &&  !empty( $u ) ) {
				pdo_update('zofui_tasktb_paylog',array('userid'=>$u['uid']),array('id'=>$v['id']));
			}
		}
	}		
	$all = pdo_getall('zofui_tasktb_privatetask',array('pubuid'=>''),array('puber','accepter','workeropenid','bossopenid','id','uniacid'));
	if( !empty( $all ) ) {
		foreach ($all as $v) {
			$u = pdo_get('zofui_task_user',array('openid'=>$v['puber'],'uniacid'=>$v['uniacid']),array('uid'));
			$u1 = pdo_get('zofui_task_user',array('openid'=>$v['accepter'],'uniacid'=>$v['uniacid']),array('uid'));
			if( !empty( $v['puber'] ) && !empty( $u ) ) {
				$where = array('pubuid'=>$u['uid'],'acceptuid'=>$u1['uid']);
				$where['bossuid'] = $v['puber'] == $v['workeropenid'] ? $u['uid'] : $u1['uid'];
				$where['workeruid'] = $v['puber'] == $v['workeropenid'] ? $u['uid'] : $u1['uid'];

				pdo_update('zofui_tasktb_privatetask',$where,array('id'=>$v['id']));
			}
		}
	}
	$all = pdo_getall('zofui_tasktb_selfqrcode',array('userid'=>''),array('openid','id','uniacid'));
	if( !empty( $all ) ) {
		foreach ($all as $v) {
			$u = pdo_get('zofui_task_user',array('openid'=>$v['openid'],'uniacid'=>$v['uniacid']),array('uid'));
			if( !empty( $v['openid'] ) && !empty( $u ) ) {
				pdo_update('zofui_tasktb_selfqrcode',array('userid'=>$u['uid']),array('id'=>$v['id']));
			}
		}
	}

	$all = pdo_getall('zofui_tasktb_taked',array('userid'=>''),array('openid','puber','id','uniacid'));
	if( !empty( $all ) ) {
		foreach ($all as $v) {
			$u = pdo_get('zofui_task_user',array('openid'=>$v['openid'],'uniacid'=>$v['uniacid']),array('uid'));
			$u1 = pdo_get('zofui_task_user',array('openid'=>$v['puber'],'uniacid'=>$v['uniacid']),array('uid'));
			
			if( !empty( $v['openid'] ) &&  !empty( $u ) ) {

				$where = array('userid'=>$u['uid']);
				if( !empty( $v['puber'] ) &&  !empty( $u1 ) ) $where['pubuid'] = $u1['uid'];

				pdo_update('zofui_tasktb_taked',$where,array('id'=>$v['id']));
			}
		}
	}
	$all = pdo_getall('zofui_tasktb_task',array('userid'=>''),array('puber','id','uniacid'));
	if( !empty( $all ) ) {
		foreach ($all as $v) {

			$u = pdo_get('zofui_task_user',array('openid'=>$v['puber'],'uniacid'=>$v['uniacid']),array('uid'));
			
			if( !empty( $v['puber'] ) &&  !empty( $u ) ) {
				pdo_update('zofui_tasktb_task',array('userid'=>$u['uid']),array('id'=>$v['id']));
			}
		}
	}
	$all = pdo_getall('zofui_tasktb_taskmessage',array('userid'=>''),array('openid','id','uniacid'));
	if( !empty( $all ) ) {
		foreach ($all as $v) {

			$u = pdo_get('zofui_task_user',array('openid'=>$v['openid'],'uniacid'=>$v['uniacid']),array('uid'));
			if( !empty( $v['openid'] ) && !empty( $u ) ) {
				pdo_update('zofui_tasktb_taskmessage',array('userid'=>$u['uid']),array('id'=>$v['id']));
			}
		}
	}
	
	$all = pdo_getall('zofui_tasktb_tbtaked',array('userid'=>''),array('openid','puber','id','uniacid'));
	if( !empty( $all ) ) {
		foreach ($all as $v) {
			$u = pdo_get('zofui_task_user',array('openid'=>$v['openid'],'uniacid'=>$v['uniacid']),array('uid'));
			$u1 = pdo_get('zofui_task_user',array('openid'=>$v['puber'],'uniacid'=>$v['uniacid']),array('uid'));
			
			if( !empty( $v['openid'] ) && !empty( $u ) ) {
				pdo_update('zofui_tasktb_tbtaked',array('userid'=>$u['uid'],'pubuid'=>$u1['uid']),array('id'=>$v['id']));
			}
		}
	}
	$all = pdo_getall('zofui_tasktb_tbtask',array('userid'=>''),array('puber','id','uniacid'));
	if( !empty( $all ) ) {
		foreach ($all as $v) {
			
			$u = pdo_get('zofui_task_user',array('openid'=>$v['puber'],'uniacid'=>$v['uniacid']),array('uid'));
			
			if( !empty( $v['puber'] ) &&  !empty( $u ) ) {
				pdo_update('zofui_tasktb_tbtask',array('userid'=>$u['uid']),array('id'=>$v['id']));
			}
		}
	}
	$all = pdo_getall('zofui_tasktb_tbtaskstep',array('userid'=>''),array('openid','id','uniacid'));
	if( !empty( $all ) ) {
		foreach ($all as $v) {
			$u = pdo_get('zofui_task_user',array('openid'=>$v['openid'],'uniacid'=>$v['uniacid']),array('uid'));
			if( !empty( $v['openid'] ) && !empty( $u ) ) {
				pdo_update('zofui_tasktb_tbtaskstep',array('userid'=>$u['uid']),array('id'=>$v['id']));
			}
		}
	}
	$all = pdo_getall('zofui_tasktb_usetasklog',array('userid'=>''),array('openid','puber','id','uniacid'));
	if( !empty( $all ) ) {
		foreach ($all as $v) {
			$u = pdo_get('zofui_task_user',array('openid'=>$v['openid'],'uniacid'=>$v['uniacid']),array('uid'));
			
			$u1 = pdo_get('zofui_task_user',array('openid'=>$v['puber'],'uniacid'=>$v['uniacid']),array('uid'));
			
			if( !empty( $v['openid'] ) && !empty( $u ) ) {
				$where = array('userid'=>$u['uid']);
				if( !empty( $u1 ) ) {
					$where['pubuid'] = $u1['uid'];
				}
				pdo_update('zofui_tasktb_usetasklog',$where,array('id'=>$v['id']));
			}
		}
	}
	//////////升级需要更新的

	echo '已更新，请返回';die;