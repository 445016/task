<?php 
	global $_W,$_GPC;
	$_GPC['do'] = 'tabbar';
	$_GPC['op'] = $op = empty($_GPC['op'])? 'list' : $_GPC['op'];
	model_user::wInit();

	

	//批量删除
	if(checksubmit('deleteall')){
		$res = WebCommon::deleteDataInWeb($_GPC['checkall'],'zofui_tasktb_tabbar');
		Util::deletecache('tabbar','all');
		message('操作完成,成功删除'.$res[0].'项，失败'.$res[1].'项',referer(),'success');
	}
	
	
	if($op == 'list'){	
		$info = model_db::getall('zofui_tasktb_tabbar',array('uniacid'=>$_W['uniacid']),1,999,' `number` DESC ');
				
		$list = $info[0];
		$pager = $info[1];
	}
	
	if($op == 'edit'){
		$id = intval($_GPC['id']);
		$info = pdo_get('zofui_tasktb_tabbar',array('uniacid'=>$_W['uniacid'],'id'=>$id));

	}
	
	if($op == 'delete'){
		$res = WebCommon::deleteSingleData($_GPC['id'],'zofui_tasktb_tabbar');
		Util::deletecache('tabbar','all');
		if($res) message('删除成功',referer(),'success');
	}
    
    
	include $this->template('web/'.$_W['mtemp'].'/tabbar');