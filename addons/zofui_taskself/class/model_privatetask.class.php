<?php 

class model_privatetask
{
	

	
	
	//雇员接受雇主的拒绝任务结果
	static function acceptRefuseRusultInAjaxAndCronb($taskinfo,$status,$module){		
		//改变任务状态
		$res = pdo_update('zofui_tasktb_privatetask',array('status'=>$status,'complaintime'=>time(),'isend'=>1),array('id'=>$taskinfo['id']));
		
		//退资金
		if( $res ) {
			$res = self::backMoneyToBossInPrivateTask($taskinfo);

			// 增加发布量
			$boss = model_user::getSingleUser( $taskinfo['bossuid'] );
			Util::addOrMinusOrUpdateData('zofui_task_user',array('pubnumber'=>1),$boss['id']);

			// 增加接任务量
			$worker = model_user::getSingleUser( $taskinfo['workeruid'] );
			Util::addOrMinusOrUpdateData('zofui_task_user',array('replynumber'=>1),$worker['id']);			

			// 平台发布、完成量
			pdo_query("UPDATE ".tablename('zofui_tasktb_scan')." SET `pubed` = `pubed` + 1,`privatepubed` = `privatepubed` + 1 WHERE `uniacid` = '{$_W['uniacid']}' ");

			Util::deleteCache( 'u',$taskinfo['bossuid'] );
			Util::deleteCache( 'u',$taskinfo['workeruid'] );

		}
		//发通知
		Message::cmessage($taskinfo['bossuid'],$taskinfo['bossopenid'],$taskinfo['tasktitle'],'acceptrefuse',$taskinfo['id']);
		
		return $res;
	}
		
	
	//雇主确认完成任务，自动处理雇主没有确认过期的任务 管理员判断投诉 app是来自前端
	static function completeTaskInajaxdealAndCrontab($taskinfo,$status,$module,$from = 'app'){
		global $_W;
		
		
		//发放资金
		$server = $taskinfo['taskmoney']*$module['priserverend']/100;
		

		$server = max( $server,$module['prileastend'] );
	
		if( $server >= $taskinfo['taskmoney'] ){
			$server = 0;
		}

		$res = model_user::updateUserCredit($taskinfo['workeruid'],$taskinfo['taskmoney'],2,1);
	
		if( $res ){

			// 上级收入
			$upmoney = 0;
			if( $module['privategive'] > 0 && $module['isdown'] != 2 ){

				$upmoney = $module['privategive']*$taskinfo['taskmoney']/100;
				$worker = model_user::getSingleUser( $taskinfo['workeruid'] );					
				if( $worker['parent'] > 0 && $upmoney >= 0.01 ){
					$parent = pdo_get( 'zofui_task_user' ,array('uniacid'=>$_W['uniacid'],'id'=>$worker['parent']) );
					if( !empty( $parent )  ){						
						model_user::updateUserCredit($parent['uid'],$upmoney,2,1);
						model_money::insertMoneyLog($parent['openid'],$upmoney,1,6,$parent['uid']);
						
						Util::addOrMinusOrUpdateData('zofui_task_user',array('giveparent'=>$upmoney),$worker['id']);
						//发通知
						Message::downgive($parent['uid'],$parent['openid'],$upmoney,$taskinfo['tasktitle'],$worker['nickname']);			
					}
				}else{
					$upmoney = 0;
				}
			}

			// 资金记录
			model_money::insertMoneyLog($taskinfo['workeropenid'],$taskinfo['taskmoney'],1,5,$taskinfo['workeruid']);
			// 扣服务费
			model_user::updateUserCredit($taskinfo['workeruid'],-$server,2,1);
			model_money::insertMoneyLog($taskinfo['workeropenid'],-$server,1,9,$taskinfo['workeruid']);

			//改变任务状态
			if($from == 'app')
		 		$res = pdo_update('zofui_tasktb_privatetask',array('status'=>$status,'bossdealtime'=>time(),'isend'=>1,'workerserver'=>$server,'giveparent'=>$upmoney),array('id'=>$taskinfo['id']));	

			// 增加发布量
			$boss = model_user::getSingleUser( $taskinfo['bossuid'] );
			Util::addOrMinusOrUpdateData('zofui_task_user',array('pubnumber'=>1,'acceptnumber'=>1),$boss['id']);

			// 增加接任务量
			$worker = model_user::getSingleUser( $taskinfo['workeruid'] );
			Util::addOrMinusOrUpdateData('zofui_task_user',array('replynumber'=>1,'acceptednumber'=>1),$worker['id']);			

			// 平台发布、完成量
			pdo_query("UPDATE ".tablename('zofui_tasktb_scan')." SET `pubed` = `pubed` + 1,`privatepubed` = `privatepubed` + 1,`comed` = `comed` + 1,`privatecomed` = `privatecomed` + 1 WHERE `uniacid` = '{$_W['uniacid']}' ");

			Util::deleteCache( 'u',$taskinfo['bossuid'] );
			Util::deleteCache( 'u',$taskinfo['workeruid'] );

			//发通知
			if($from == 'app') $str = 'confirmtask';
			if($from == 'web') $str = 'admindealtoworker';
			Message::cmessage($taskinfo['workeruid'],$taskinfo['workeropenid'],$taskinfo['tasktitle'],$str,$taskinfo['id']);
		}
		
		return $res;
	}
	
	
	//给私包任务雇主退钱。
	static function backMoneyToBossInPrivateTask($taskinfo){
		//退资金
		$res = model_user::updateUserCredit($taskinfo['bossuid'],$taskinfo['taskmoney'],2,1);
		Util::deleteCache( 'u', $taskinfo['bossuid'] );
		model_money::insertMoneyLog($taskinfo['bossopenid'],$taskinfo['taskmoney'],1,4,$taskinfo['bossuid']);
		return $res;
	}

	//雇员取消任务，自动处理没有执行任务过期的任务
	static function cancelTaskFuncInAjaxdealAndCrontab($taskinfo,$changestatus,$module){
		global $_W;
		//改变任务状态
		$res = pdo_update('zofui_tasktb_privatetask',array('status'=>$changestatus,'workerdealtime'=>time(),'isend'=>1),array('id'=>$taskinfo['id']));

		//退回资金
		if( $res ){
			$res = self::backMoneyToBossInPrivateTask($taskinfo); //这里已删除雇主缓存

			// 增加发布量
			$boss = model_user::getSingleUser( $taskinfo['bossuid'] );
			Util::addOrMinusOrUpdateData('zofui_task_user',array('pubnumber'=>1),$boss['id']);

			// 增加接任务量
			$worker = model_user::getSingleUser( $taskinfo['workeruid'] );
			Util::addOrMinusOrUpdateData('zofui_task_user',array('replynumber'=>1),$worker['id']);			

			// 平台发布量
			pdo_query("UPDATE ".tablename('zofui_tasktb_scan')." SET `pubed` = `pubed` + 1,`privatepubed` = `privatepubed` + 1 WHERE `uniacid` = '{$_W['uniacid']}' ");

			Util::deleteCache( 'u',$taskinfo['bossuid'] );
			Util::deleteCache( 'u',$taskinfo['workeruid'] );
		} 
		//发通知
		Message::cmessage($taskinfo['bossuid'],$taskinfo['bossopenid'],$taskinfo['tasktitle'],'canceltask',$taskinfo['id']);
		return $res;
	}
	
	
	
}

