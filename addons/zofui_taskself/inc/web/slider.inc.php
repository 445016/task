<?php 
	global $_W,$_GPC;
	$op = isset($_GPC['op'])?$_GPC['op']:'list';
	model_user::wInit();
	

	//添加，编辑
	if(checksubmit('create')){
		$_GPC = Util::trimWithArray($_GPC);
		
		$data['img'] = $_GPC['img'];
		$data['number'] = $_GPC['number'];
		$data['uniacid'] = $_W['uniacid'];
		$data['url'] = $_GPC['url'];
		$data['isindex'] = intval( $_GPC['isindex'] );
		$data['isusetask'] = intval( $_GPC['isusetask'] );
		
		if(!empty($_GPC['id'])){
			$id = intval($_GPC['id']);
			$res = pdo_update('zofui_tasktb_slider',$data,array('uniacid'=>$_W['uniacid'],'id'=>$id));	
		}else{
			$res = Util::inserData('zofui_tasktb_slider',$data);
		}
		
		if($res){
			Util::deleteCache('slider','all');
			message('操作成功',referer(),'success');
		} 
	}
	
	
	//批量删除
	if(checksubmit('deleteall')){
		$res = WebCommon::deleteDataInWeb($_GPC['checkall'],'zofui_tasktb_slider');
		message('操作完成,成功删除'.$res[0].'项，失败'.$res[1].'项',referer(),'success');
	}
	
	
	if($op == 'list'){	
		$info = model_db::getall('zofui_tasktb_slider',array('uniacid'=>$_W['uniacid']),1,999,' `number` DESC ');
		
		$list = $info[0];
		$pager = $info[1];
	}
	
	if($op == 'edit'){
		$id = intval($_GPC['id']);
		$info = pdo_get('zofui_tasktb_slider',array('uniacid'=>$_W['uniacid'],'id'=>$id));

	}
	
	if($op == 'delete'){
		$res = WebCommon::deleteSingleData($_GPC['id'],'zofui_tasktb_slider');
		if($res) message('删除成功',referer(),'success');
	}
	
	
	
	include $this->template('web/'.$_W['mtemp'].'/slider');