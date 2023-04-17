<?php 
	global $_W,$_GPC;
	$op = $_GPC['op'] = empty($_GPC['op']) ? 'list' : $_GPC['op'];
	model_user::wInit();
	
	
	//批量删除
	if(checksubmit('deleteall')){
		$res = WebCommon::deleteDataInWeb($_GPC['checkall'],'zofui_tasktb_privatetask');
		message('操作完成,成功删除'.$res[0].'项，失败'.$res[1].'项',referer(),'success');
	}
	
	
	if($op == 'list'){	
		$info = Util::getAllDataInSingleTable('zofui_tasktb_privatetask',array('uniacid'=>$_W['uniacid']),$_GPC['page'],10,' `id` DESC ');
		$list = $info[0];
		$pager = $info[1];


	}
	
	if($op == 'info'){
		$id = intval($_GPC['id']);
		$taskinfo = pdo_get('zofui_tasktb_privatetask',array('id'=>$_GPC['id'],'uniacid'=>$_W['uniacid']));
		if( empty( $taskinfo ) ) message('任务不存在');

		$taskinfo['images'] = iunserializer($taskinfo['images']);
		$taskinfo['completecontent'] = iunserializer($taskinfo['completecontent']);

		$bossinfo = model_user::getSingleUser( $taskinfo['bossuid'] );
		$workerinfo = model_user::getSingleUser( $taskinfo['workeruid'] );

	}
	
	if($op == 'delete'){
		$res = WebCommon::deleteSingleData($_GPC['id'],'zofui_tasktb_privatetask');
		if($res) message('删除成功',referer(),'success');
	}
	
	
	
	include $this->template('web/'.$_W['mtemp'].'/privatetask');