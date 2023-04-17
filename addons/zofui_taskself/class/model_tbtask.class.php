<?php 

class model_tbtask {


	static function getTask( $id ){
		global $_W;
		$cache = Util::getCache('tbtask',$id);

		if( empty( $cache['id'] ) ){
			$cache = pdo_get('zofui_tasktb_tbtask',array('id'=>$id,'uniacid'=>$_W['uniacid']));

			if( !empty( $cache ) ) {
				$cache['images'] = iunserializer( $cache['images'] );
				$cache['hideimages'] = iunserializer( $cache['hideimages'] );

				$cache['kakey'] = iunserializer( $cache['kakey'] );
				$cache['link'] = iunserializer( $cache['link'] );
				$cache['step'] = iunserializer( $cache['step'] );
				Util::setCache('tbtask',$id,$cache);
			}
			
		}
		return $cache;
	}

	static function isEmpty( $taskid,$totalnum ){
		global $_W;
		$taked = Util::countDataNumber('zofui_tasktb_tbtaked',array('taskid'=>$taskid));
		return  array('taked'=>$taked,'last'=>$totalnum - $taked);
	}

	static function taskStatus( $task ){
		global $_W;
		if( empty( $task ) || !is_array( $task ) ) return array('status'=>201,'res'=>'任务不存在');

		if( $task['status'] == 1 ) return array('status'=>201,'res'=>'任务还未通过审核');
		if( $task['status'] == 2 ) return array('status'=>201,'res'=>'任务已下架');
		if( $task['iscount'] == 1 ) return array('status'=>201,'res'=>'任务已结束');
		//if( $task['end'] <= TIMESTAMP ) return array('status'=>201,'res'=>'任务已结束');

		if( $task['isstart'] == 1 ) return array('status'=>201,'res'=>'任务还未开始');
		if( $task['start'] > TIMESTAMP ) return array('status'=>201,'res'=>'任务还未开始');

		return array('status'=>200,'res'=>'好'); 

	}


	static function getMyStatusByTbtask( $task,$alltaked,$uid,$sex ){
		global $_W;

		if( empty( $task ) || !is_array( $task ) ) return array('status'=>201,'res'=>'任务不存在');
		if( empty( $uid ) ) return array('status'=>201,'res'=>'没有会员数据');
		
		if( !empty( $alltaked ) ) {

			$takenum = 0;
			foreach ( $alltaked as $k => $v ) {
				if( $v['isend'] == 0 ) {
					return array('status'=>202,'res'=>'您已接了任务','acttaked'=>$v);
				}
				$takenum ++;
			}
			if( $takenum >= $task['limitnum'] ) return array('status'=>203,'res'=>'您不能再接了');
		}

		if( $task['sex'] == 1 && !in_array($sex,array('1','3')) ) return array('status'=>204,'res'=>'此任务仅限男性可接');
		if( $task['sex'] == 2 && !in_array($sex,array('2','4')) ) return array('status'=>204,'res'=>'此任务仅限女性可接');

		return array('status'=>200,'res'=>'');

	}

	// 审核通过
	static function passTask( $taked,$task,$server ){
		global $_W;

		if( empty( $taked ) ) return array('status'=>201,'res'=>'未找到任务');
		if( $taked['status'] != 0 ) return array('status'=>201,'res'=>'任务不能被审核');

		$steparr = array();
		foreach ($task['step'] as $v) {
			$steparr[] = $v['step'];
		}
		$step = min( $steparr );

		$update = array('status'=>2,'passtime'=>TIMESTAMP,'step'=>$step,'money'=>$task['money'],'tbmoney'=>$task['tbmoney'],'costserver'=>$server,'islimitstep'=>1,'mtype'=>1);
		$res = pdo_update('zofui_tasktb_tbtaked',$update,array('id'=>$taked['id']));
		
		if( $res ){
			// 发消息
			Message::passTbtask($taked['userid'],$taked['openid'],$task['id'],$task['title'],$task['money'],$task['tbmoney']);
			//Util::deleteCache('tbtask',$taked['taskid']);
			return array('status'=>200,'res'=>'审核成功');
		}
		return array('status'=>201,'res'=>'审核失败');
	}

	// 审核未通过
	static function nopassTask( $taked,$task,$reason ){
		global $_W;

		if( empty( $taked ) ) return false;
		if( $taked['status'] != 0 ) return false;
		$res = pdo_update('zofui_tasktb_tbtaked',array('status'=>1,'isend'=>1,'nopasstime'=>TIMESTAMP,'nopassreason'=>$reason),array('id'=>$taked['id']));
		
		if( $res ){
			// 发消息
			Message::nopassTbtask($taked['userid'],$taked['openid'],$task['id'],$task['title'],$task['money'],$reason);
			return true;
		}
		return false;
	}


	static function nextStep( $nowstep,$steparr ){
		global $_W;
		if( empty( $steparr ) || !is_array( $steparr ) ) return false;
		$steparr[] = array('step'=>6); // 增加最完成的一步
		$ltarr = array();
			
		foreach ($steparr as $v) {
			if( $v['step'] > $nowstep ) $ltarr[] = $v['step'];
		}
		return min( (array)$ltarr );
	}


	// 完成任务
	static function comTbtask( $taked,$task,$tostatus ){
		global $_W;
		if( empty( $task ) ) return false;
		if( empty( $taked ) || $taked['isend'] == 1 ) return false;

		$counting = Util::getCache('counttbtaked',$taked['id']);
		if( is_array( $counting ) && $counting['status'] == 1 && $counting['time'] > (time() - 60) ) {
			return false;
		}
		Util::setCache('counttbtaked',$taked['id'],array('status'=>1,'time'=>time()));
		
		$replyer = model_user::getSingleUser( $taked['userid'] );
		
		// 发赏金
		$res = model_user::updateUserCredit($taked['userid'],$taked['money'],2,1);
		// 资金记录
		if( $res ) model_money::insertMoneyLog($taked['openid'],$taked['money'],1,24,$taked['userid']);
		
		
		// 会员等级
		$level = model_user::levelRes($replyer,$_W['set']);
		if( $level == 1 ) {
			$_W['set']['tbtaskcomserver'] = $_W['set']['tbtaskcomservera'];
		}
		if( $level == 2 ) {
			$_W['set']['tbtaskcomserver'] = $_W['set']['tbtaskcomserverb'];
		}

		// 扣服务费
		$server = 0;
		if( $_W['set']['tbtaskcomserver'] > 0 ){
			$server = $_W['set']['tbtaskcomserver']*$taked['money']/100;
			if( $server >= 0.01 ) {
				
				model_user::updateUserCredit($taked['userid'],-$server,2,1);
				// 资金记录
				model_money::insertMoneyLog($taked['openid'],-$server,1,9,$taked['userid']);
			}
		}
		
		
		// 先计算会员等级
		if( $replyer['parent'] > 0 ) {
			$parent = pdo_get( 'zofui_task_user',array('uniacid'=>$_W['uniacid'],'id'=>$replyer['parent']) );
			$level = model_user::levelRes($parent,$_W['set']);
			if( $level == 1 ){
				$_W['set']['tbtaskgiveparent'] = $_W['set']['tbtaskgiveparenta'];
			}
			if( $level == 2 ){
				$_W['set']['tbtaskgiveparent'] = $_W['set']['tbtaskgiveparentb'];
			}
		}
		if( $parent['parent'] > 0 ) {
			$two = pdo_get( 'zofui_task_user',array('uniacid'=>$_W['uniacid'],'id'=>$parent['parent']) );
			$level = model_user::levelRes($two,$_W['set']);
			if( $level == 1 ){
				$_W['set']['tgivet'] = $_W['set']['tgiveta'];
			}
			if( $level == 2 ){
				$_W['set']['tgivet'] = $_W['set']['tgivetb'];
			}
		}
		if( $two['parent'] > 0 ) {
			$three = pdo_get( 'zofui_task_user',array('uniacid'=>$_W['uniacid'],'id'=>$two['parent']) );
			$level = model_user::levelRes($three,$_W['set']);
			if( $level == 1 ){
				$_W['set']['tgiveth'] = $_W['set']['tgivetha'];
			}
			if( $level == 2 ){
				$_W['set']['tgiveth'] = $_W['set']['tgivethb'];
			}
		}
		
		// 给上级奖励
		$upmoney = $twoupmoney = $threeupmoney = 0;
		if( $_W['set']['tbtaskgiveparent'] > 0 ){
			$upmoney = $_W['set']['tbtaskgiveparent']*$taked['money']/100;
			
			if( $replyer['parent'] > 0 && $_W['set']['isdown'] != 2 && $upmoney > 0.01 ){
				
				if( $_W['set']['veryfydown'] == 0 || ( $_W['set']['veryfydown'] == 1 && $parent['isdown'] == 1 ) ){
					
					model_user::updateUserCredit($parent['uid'],$upmoney,2,1);
					model_money::insertMoneyLog($parent['openid'],$upmoney,1,6,$parent['uid']);

					Util::addOrMinusOrUpdateData('zofui_task_user',array('giveparent'=>$upmoney),$replyer['id']);
					
					// 发通知
					Message::downgive($parent['uid'],$parent['openid'],$upmoney,$task['title'],$replyer['nickname']);
				}else{
					$upmoney = 0;
				}
				
				// 二级奖励
				$twoupmoney = $taked['money']*$_W['set']['tgivet']/100;
				if( $twoupmoney >= 0.01 && $_W['set']['tgivet'] > 0 && $_W['set']['downnum'] >= 1 && $parent['parent'] > 0 ){
					
					model_user::updateUserCredit($two['uid'],$twoupmoney,2,1);
					model_money::insertMoneyLog($two['openid'],$twoupmoney,1,28,$two['uid']);

					Util::addOrMinusOrUpdateData('zofui_task_user',array('givetwo'=>$twoupmoney),$replyer['id']);
				}else{
					$twoupmoney = 0;
				}

				// 三级奖励
				$threeupmoney = $taked['money']*$_W['set']['tgiveth']/100;
				if( $threeupmoney >= 0.01 && $_W['set']['tgiveth'] > 0 && $_W['set']['downnum'] >= 2 && $two['parent'] > 0 ){
					
					model_user::updateUserCredit($three['uid'],$threeupmoney,2,1);
					model_money::insertMoneyLog($three['openid'],$threeupmoney,1,29,$three['uid']);

					Util::addOrMinusOrUpdateData('zofui_task_user',array('givethree'=>$threeupmoney),$replyer['id']);
				}else{
					$threeupmoney = 0;
				}
				
			}

		}

		
		$puber = model_user::getSingleUser( $task['userid'] );
		// 发担保金
		if( $res && $taked['tbmoney'] > 0 ) {
			
			$res = model_user::updateUserCredit($taked['userid'],$taked['tbmoney'],2,1);
			// 资金记录
			if( $res ) model_money::insertMoneyLog($taked['openid'],$taked['tbmoney'],1,24,$taked['userid']);
			
		}
		

		if( $res ) { // 改变任务状态

			if( $tostatus == 3 ){
				$res = pdo_update('zofui_tasktb_tbtaked',array('comtime'=>TIMESTAMP,'isend'=>1,'giveparent'=>$upmoney,'givetwo'=>$twoupmoney,'givethree'=>$threeupmoney,'server'=>$server,'status'=>3),array('id'=>$taked['id']));
			}elseif( $tostatus == 9 ){
				$res = pdo_update('zofui_tasktb_tbtaked',array('complainentime'=>TIMESTAMP,'isend'=>1,'giveparent'=>$upmoney,'givetwo'=>$twoupmoney,'givethree'=>$threeupmoney,'server'=>$server,'status'=>9,'complainto'=>0),array('id'=>$taked['id']));
				
			}

			// 增加发布任务数量
			if( $res ){
				// 增加发布、采纳数量
				Util::addOrMinusOrUpdateData('zofui_task_user',array('tbpub'=>1,'tbsuccess'=>1),$puber['id']);
				
				// 增加完成数量
				Util::addOrMinusOrUpdateData('zofui_task_user',array('tbtake'=>1,'tbcom'=>1),$replyer['id']);

				// 平台发布量、完成量
				pdo_query("UPDATE ".tablename('zofui_tasktb_scan')." SET `pubed` = `pubed` + 1,`tbpubed` = `tbpubed` + 1, `comed` = `comed` + 1,`tbcomed` = `tbcomed` + 1 WHERE `uniacid` = '{$_W['uniacid']}' ");

				Util::deleteCache( 'u',$taked['userid'] );
				if(!empty( $task['userid'] )) Util::deleteCache( 'u',$task['userid'] );

				//给回复者发消息
				$totalmoney = $taked['money'] + $taked['tbmoney'];
				Message::comTbtask($taked['userid'],$taked['openid'],$task['id'],$totalmoney,$server,$task['title']);
			}

		}

		Util::deleteCache( 'counttbtaked',$taked['id'] );
		return $res;

	}


	// 确认任务失败
	static function confirmFailTbtask( $taked,$task,$tostatus ){
		global $_W;
		if( empty( $task ) || $taked['isend'] == 1 ) return false;
		
		if( !empty( $task['userid'] ) ) { // 判断不是管理员
			$puber = model_user::getSingleUser( $task['userid'] );

			// 退赏金
			$res = model_user::updateUserCredit($puber['uid'],$taked['money'],2,1);
			// 资金记录
			if( $res ) model_money::insertMoneyLog($puber['openid'],$taked['money'],1,25,$puber['uid']);

			// 退回保证金
			if( $res && $taked['tbmoney'] > 0 ) {

				if( $taked['mtype'] == 0 ){ // 用保证金支付的
					$res = Util::addOrMinusOrUpdateData( 'zofui_task_user',array('deposit'=>$taked['tbmoney']),$puber['id'] );
					if( $res ){
						model_money::insertMoneyLog($puber['openid'],$taked['tbmoney'],2,8,$puber['uid'] );
					}
				}elseif( $taked['mtype'] == 1 ){ // 用余额支付的
					$res = model_user::updateUserCredit($puber['uid'],$taked['tbmoney'],2,1);
					// 资金记录
					if( $res ) model_money::insertMoneyLog($puber['openid'],$taked['tbmoney'],1,26,$puber['uid']);
				}
				
			}
		}

		// 更新
		if( $tostatus == 5 ){ // 直接失败任务
			$update = array('isend'=>1,'status'=>5,'confirmfailtime'=>TIMESTAMP);
			$res = pdo_update('zofui_tasktb_tbtaked',$update,array('id'=>$taked['id'],'uniacid'=>$_W['uniacid']));

		}elseif( $tostatus == 8 ){ //申诉后失败

			$res = pdo_update('zofui_tasktb_tbtaked',array('isend'=>1,'status'=>8,'complainentime'=>TIMESTAMP,'complainto'=>1),array('id'=>$taked['id']));
		}


		// 增加发布数量
		$puber = model_user::getSingleUser( $taked['pubuid'] );
		Util::addOrMinusOrUpdateData('zofui_task_user',array('tbpub'=>1),$puber['id']);
		
		// 增加回复数量
		$replyer = model_user::getSingleUser( $taked['userid'] );
		Util::addOrMinusOrUpdateData('zofui_task_user',array('tbtake'=>1),$replyer['id']);

		// 平台发布量
		pdo_query("UPDATE ".tablename('zofui_tasktb_scan')." SET `pubed` = `pubed` + 1,`tbpubed` = `tbpubed` + 1 WHERE `uniacid` = '{$_W['uniacid']}' ");

		Util::deleteCache( 'u',$taked['userid'] );
		if(!empty( $task['userid'] )) Util::deleteCache( 'u',$task['userid'] );
		

		return $res;

	}

	// 结算任务
	static function countTbtask( $task ){
		global $_W;
		if( $task['iscount'] == 1 || empty( $task ) ) return false;
		$counting = Util::getCache('counttbtask',$task['id']);
		if( is_array( $counting ) && $counting['status'] == 1 && $counting['time'] > (TIMESTAMP - 60) ) {
			return false;
		}
		Util::setCache('counttbtask',$task['id'],array('status'=>1,'time'=>TIMESTAMP));
		
		$alltaked = pdo_getall('zofui_tasktb_tbtaked',array('uniacid'=>$_W['uniacid'],'taskid'=>$task['id'],'isend'=>0));

		if( !empty( $alltaked ) ) {
			set_time_limit(0);

			foreach ( $alltaked as $v ) {
				if( $v['status'] == 0 ) { // 待审核的 变为审核未通过

					self::nopassTask( $v,$task,'' );

				}elseif( $v['status'] == 2 ) { // 判定任务失败

					if( $v['step'] == 6 ){
						self::comTbtask( $v,$task,3 );
					}else{
						self::confirmFailTbtask( $v,$task,5 );
					} 

				}elseif( $v['status'] == 4 ) { // 认定失败 雇员没确认

					self::confirmFailTbtask( $v,$task,5 );

				}elseif( $v['status'] == 6 ) { // 申诉中，认定失败

					self::confirmFailTbtask( $v,$task,5 );

				}elseif( $v['status'] == 7 ) { // 已判，根据判断来

					if( $v['complainstep'] == '1' ){ // 任务完成
						self::comTbtask( $v,$task,9 );

					}else{ //任务失败
						self::confirmFailTbtask( $v,$task,8 );

					} 
				}
			}
		}

		pdo_update('zofui_tasktb_tbtask',array('iscount'=>1),array('id'=>$task['id']));
		Util::deleteCache('tbtask',$task['id']);
		Util::deleteCache( 'counttbtask',$task['id'] );
		
	}
	
	
	
	// 结构化步骤
	static function structTakedStep( $taked ){
		global $_W;

		$arr = array();
		if( $taked['createtime'] > 0 ) {
			if( !empty( $taked['takecontent'] ) ){
				$takecontent = iunserializer( $taked['takecontent'] );
				if( !empty( $takecontent['images'] ) ) {

					foreach ($takecontent['images'] as &$v) {
						$v = tomedia( $v );
					}
					unset( $v );
				}
			}
			$arr[] = array('str'=>'接任务','time'=>date('m-d H:i',$taked['createtime']),'content'=>$takecontent,'ordertime'=>$taked['createtime'],'isboss'=>0 );
		}

		if( $taked['passtime'] > 0 ) {
			$arr[] = array('str'=>'通过审核','time'=>date('m-d H:i',$taked['passtime']),'ordertime'=>$taked['passtime'],'isboss'=>1 );
		}
		if( $taked['nopasstime'] > 0 ) {
			$reason = empty( $taked['nopassreason'] ) ? '' : '原因：'.$taked['nopassreason'];
			$tcontent = array('content'=>$reason);
			$arr[] = array('str'=>'未通过审核','time'=>date('m-d H:i',$taked['nopasstime']),'content'=>$tcontent,'ordertime'=>$taked['nopasstime'],'isboss'=>1 );
		}

		if( !empty( $taked['stepcontent'] ) ){
			$stepcontent = iunserializer( $taked['stepcontent'] );  
			
			if( !empty( $stepcontent ) ) {
				foreach ($stepcontent as $v) {
					switch ( $v['step'] ) {
						case 1:
							$thisstr = '提交浏览图片';
							break;
						case 2:
							$thisstr = '提交下单图片';
							break;
						case 3:
							$thisstr = '提交签收图片';
							break;
						case 4:
							$thisstr = '提交评价图片';
							break;
						case 5:
							$thisstr = '备注留言';
							break;
					}
					$arr[] = array(
						'str'=>$thisstr,
						'time'=>date('m-d H:i',$v['time']),
						'content'=>array('content'=>$v['content'],'images'=>$v['images']),
						'ordertime'=>$v['time'],
						'isboss'=>0
					);
				}
			}

		}

		$remindlist = pdo_getall('zofui_tasktb_tbremind',array('uniacid'=>$_W['uniacid'],'takedid'=>$taked['id'],'from'=>0));

		if( !empty( $remindlist ) ) {
			foreach ( $remindlist as $v ) {
				$arr[] = array('str'=>'雇主提醒：'.$v['content'],'time'=>date('m-d H:i',$v['createtime']),'ordertime'=>$v['createtime'],'isboss'=>1 );
			}
		}
			
		if( $taked['comtime'] > 0 ) {
			$arr[] = array('str'=>'任务完成，发放赏金 <span class="font_ff5f27">+'.$taked['money'].'</span>元','time'=>date('m-d H:i',$taked['comtime']),'ordertime'=>$taked['comtime'],'isboss'=>1 );
		}

		if( $taked['setfailtime'] > 0 ) {
			$arr[] = array('str'=>'雇主设为失败，原因：'.$taked['failreason'],'time'=>date('m-d H:i',$taked['setfailtime']),'ordertime'=>$taked['setfailtime'],'isboss'=>1 );
		}

		if( $taked['confirmfailtime'] > 0 ) {
			$arr[] = array('str'=>'确认任务失败','time'=>date('m-d H:i',$taked['confirmfailtime']),'ordertime'=>$taked['confirmfailtime'],'isboss'=>0 );
		}
		
		if( $taked['complaintime'] > 0 ) {
			$arr[] = array('str'=>'雇员申诉','time'=>date('m-d H:i',$taked['complaintime']),'ordertime'=>$taked['complaintime'],'isboss'=>0 );
			$allcert = pdo_getall('zofui_tasktb_tbcert',array('uniacid'=>$_W['uniacid'],'takedid'=>$taked['id']));
			if( !empty( $allcert ) ) {
				foreach ($allcert as $v) {
					$strstr = '雇员提交凭证';
					$isboss = 0;
					if( $v['type'] == 2 ){
						$strstr = '雇主提交凭证';
						$isboss = 1;
					}  

					$tcontent = array('content'=>$v['content'],'images'=>iunserializer( $v['images'] ));
					$arr[] = array('str'=>$strstr,'time'=>date('m-d H:i',$v['createtime']),'ordertime'=>$v['createtime'],'content'=>$tcontent,'isboss'=>$isboss );

				}
			}
		}

		if( $taked['adminsettime'] > 0 ){
			$strstr = '管理员初步判定给雇员';
			if( $taked['complainstep'] == 2 ) $strstr = '管理员初步判定给雇主';

			$arr[] = array('str'=>$strstr,'time'=>date('m-d H:i',$taked['adminsettime']),'ordertime'=>$taked['adminsettime'],'isboss'=>0 );
		}

		if( $taked['complainentime'] > 0 ){
			$strstr = '任务申诉结束，雇员胜诉，为雇员发放赏金'.'<span class="font_ff5f27">+'.$taked['money'].'</span>';
			if( $taked['complainto'] == 1 ) $strstr = '任务申诉结束，雇主胜诉，为雇主退还赏金';

			$arr[] = array('str'=>$strstr,'time'=>date('m-d H:i',$taked['complainentime']),'ordertime'=>$taked['complainentime'],'isboss'=>0 );
		}		
		

		$flag=array();
		foreach($arr as $v){
		    $flag[] = $v["ordertime"];
		}
		array_multisort($flag,SORT_ASC,$arr);
		
		return $arr;
	}


	static function countTopTime( $task ){
		global $_W;
		$cantoptime = $lasttoptime = 0;
		if( $task['end'] > TIMESTAMP ){
			$starttime = $task['topendtime'] > TIMESTAMP ? $task['topendtime'] : TIMESTAMP;

			$lasttoptime = sprintf('%.2f', ($starttime - TIMESTAMP)/3600 ) *100/100;
			$cantoptime = floor( ($task['end'] - $starttime)/3600 );
		}
		return array('canadd'=>$cantoptime,'last'=>$lasttoptime);
	}

}