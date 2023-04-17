<?php 
	global $_W,$_GPC;
	$op = isset($_GPC['op'])?$_GPC['op']:'list';
	model_user::wInit();
	
	
	model_quest::initQuest();
	
	//批量删除
	if(checksubmit('deleteall')){
		$res = WebCommon::deleteDataInWeb($_GPC['checkall'],'zofui_tasktb_selfquest');
		message('操作完成,成功删除'.$res[0].'项，失败'.$res[1].'项',referer(),'success');
	}
	
	
	if($op == 'list'){	
		$info = model_db::getall('zofui_tasktb_selfquest',array('uniacid'=>$_W['uniacid']),1,999,' type DESC,`number` DESC ');
		
		$list = $info[0];
	}
	
	if($op == 'edit'){
		$id = intval($_GPC['id']);
		$info = pdo_get('zofui_tasktb_selfquest',array('uniacid'=>$_W['uniacid'],'id'=>$id));

	}
	
	if($op == 'delete'){
		$res = WebCommon::deleteSingleData($_GPC['id'],'zofui_tasktb_selfquest');
		if($res) message('删除成功',referer(),'success');
	}
	
	if( $op == 'hide' || $op == 'show' ) {
		$status = $op == 'hide' ? 1 : 0;
		pdo_update('zofui_tasktb_selfquest',array('status'=>$status),array('id'=>$_GPC['id'],'uniacid'=>$_W['uniacid']));

		message('已更新',referer(),'success');
	}
	
	include $this->template('web/'.$_W['mtemp'].'/quest');


