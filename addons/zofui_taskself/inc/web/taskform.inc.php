<?php 
	global $_W,$_GPC;
	$_GPC['do'] = 'taskform';
	$_GPC['op'] = empty( $_GPC['op'] ) ? 'list' : $_GPC['op'];
	model_user::wInit();


	//添加，编辑
	if(checksubmit('create')){
		$_GPC = Util::trimWithArray($_GPC);
		
		$data['name'] = $_GPC['title'];
		$data['number'] = intval( $_GPC['number'] );
		$data['uniacid'] = $_W['uniacid'];


		// 验证
		if( empty( $data['name'] ) ) itoast('未填写名称','','error');
		if( empty( $_GPC['aid'] ) ) itoast('填写表单内容','','error');

		$form = array();
		foreach($_GPC['aid'] as $v){
			$item = array();
			$item['id'] = $v;
			$item['name'] = $_GPC['name'][$v];
			$item['type'] = $_GPC['type'][$v];
			$item['pla'] = $_GPC['pla'][$v];
			$item['maxnum'] = intval( $_GPC['maxnum'][$v] );

			$form[] = $item;
		}
		
		$data['form'] = iserializer( $form );


		if(!empty($_GPC['id'])){
			$id = intval($_GPC['id']);
			$res = pdo_update('zofui_tasktb_taskform',$data,array('uniacid'=>$_W['uniacid'],'id'=>$id));	
		}else{

			$res = Util::inserData('zofui_tasktb_taskform',$data);
			$id = pdo_insertid();
		}
		
		message('操作成功',$this->createWebUrl('taskform',array('op'=>'list','page'=>$_GPC['page'])),'success');
	}


	//批量删除表单
	if(checksubmit('deleteallform')){
		$res = WebCommon::deleteDataInWeb($_GPC['checkall'],'zofui_tasktb_taskform');
		message('操作完成,成功删除'.$res[0].'项，失败'.$res[1].'项',referer(),'success');
	}


	// 表单列表
	if( $_GPC['op'] == 'list' ){

		$list = pdo_getall('zofui_tasktb_taskform',array('uniacid'=>$_W['uniacid']));

		$order = ' `number` DESC ';
		$where = array('uniacid'=>$_W['uniacid']);
		$info = model_db::getall('zofui_tasktb_taskform',$where,1,999,$order);
		
		$list = $info[0];

	}elseif( $_GPC['op'] == 'deleteform' ){

		pdo_delete('zofui_tasktb_taskform',array('id'=>$_GPC['id'],'uniacid'=>$_W['uniacid']));
		message('删除成功',referer(),'success');

	}elseif( $_GPC['op'] == 'edit' ){

		$info = pdo_get('zofui_tasktb_taskform',array('id'=>$_GPC['id'],'uniacid'=>$_W['uniacid']));
		if( empty( $info ) ) message('数据不存在');

		$info['form'] = iunserializer( $info['form'] );

	}
	
	
	
	include $this->template('web/'.$_W['mtemp'].'/taskform');