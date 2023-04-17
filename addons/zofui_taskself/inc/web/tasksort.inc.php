<?php 
	global $_W,$_GPC;
	$op = isset($_GPC['op'])?$_GPC['op']:'list';
	model_user::wInit();
	
	//添加，编辑
	if(checksubmit('create')){
		$_GPC = Util::trimWithArray($_GPC);
		
		$api = 'http://api.zofui.net/app/index.php?c=taskwap&a=addtasksort';
		$key = pdo_get('zofui_tasktb_codekey');
		$res = Util::httpPost($api,array('site'=>$_W['siteroot'],'en'=>MODULE,'for'=>$_GPC,'setop'=>'setting','key'=>$key['key']));			
		$res = json_decode( $res,true );
		if(  $res['status'] != 200 ){
			message('添加失败请重试',referer(),'success');
		}		
		$data = $res['data'];

		if(!empty($_GPC['id'])){
			$id = intval($_GPC['id']);
			$res = pdo_update('zofui_tasktb_tasksort',$data,array('uniacid'=>$_W['uniacid'],'id'=>$id));	
		}else{

			$data['uniacid'] = $_W['uniacid'];
			$res = Util::inserData('zofui_tasktb_tasksort',$data);
		}
		Util::deleteCache('tasksort','all');
		if($res) message('操作成功',referer(),'success');
	}
	
	
	//批量删除
	if(checksubmit('deleteall')){
		$res = WebCommon::deleteDataInWeb($_GPC['checkall'],'zofui_tasktb_tasksort');
		Util::deleteCache('tasksort','all');
		message('操作完成,成功删除'.$res[0].'项，失败'.$res[1].'项',referer(),'success');
	}
	
	$form = pdo_getall('zofui_tasktb_taskform',array('uniacid'=>$_W['uniacid']),array('id','name'),'number desc');
	
	if($op == 'list'){	
		$info = model_db::getall('zofui_tasktb_tasksort',array('uniacid'=>$_W['uniacid']),1,999,' `number` DESC ');
		
		$list = $info[0];
		$pager = $info[1];
	}
	
	if($op == 'edit' || $op == 'create'){
		
		if( $op == 'edit' ){
			$id = intval($_GPC['id']);
			$info = pdo_get('zofui_tasktb_tasksort',array('uniacid'=>$_W['uniacid'],'id'=>$id));
			if( empty($info) ) message('分类不存在',referer(),'success');
		}

		$url = 'http://api.zofui.net/app/index.php?c=taskwap&a=settinghtml';
		$key = pdo_get('zofui_tasktb_codekey');
		$html = Util::httpPost($url,array('setting'=>$this->module['config'],'form'=>$form,'info'=>$info,'for'=>$_GPC,'site'=>$_W['siteroot'],'en'=>MODULE,'setop'=>'tasksort','key'=>$key['key'],'att'=>$_W['attachurl']));

		$html = str_replace('{token}', $_W['token'], $html);

	}
	
	if($op == 'delete'){
		$res = WebCommon::deleteSingleData($_GPC['id'],'zofui_tasktb_tasksort');
		Util::deleteCache('tasksort','all');
		if($res) message('删除成功',referer(),'success');
	}
	
	
	
	include $this->template('web/'.$_W['mtemp'].'/tasksort');