<?php 

class model_task {


	static function hideKey($hidetxt,$content){

		if( !empty( $hidetxt ) ){
			$list = preg_split("/,|，/",$hidetxt);
			$content = str_replace($list, '*', $content);
		}

		return $content;
	}


	static function taskCode(){
		global $_W;
		$code = Util::randSole(6,5);
		$isset = pdo_get('zofui_tasktb_task',array('uniacid'=>$_W['uniacid'],'idcode'=>$code),array('id'));
		if( empty($isset) ){
			return $code;
		}else{
			return self::taskCode();
		}
	}

	//查询一条
	static function getFalsepub($id){
		global $_W;

		$cache = Util::getCache('fpuber',$id);
		
		if( empty( $cache['id'] ) ){
			$cache = pdo_get('zofui_tasktb_puber',array('id'=>$id,'uniacid'=>$_W['uniacid']));
			if( !empty( $cache ) ) { 
				Util::setCache('fpuber',$id,$cache);
			}
		}
		return $cache;
		//需删除缓存
	}

	// 发布任务给上级提成
	// type 1发布普通任务 2发布试用任务 3发布担保任务 4通过担保任务
	static function pubGiveParent($module,$userid,$parentid,$taskid,$type,$money){
		global $_W;
		
		// 先计算会员等级
		if( $parentid > 0 ) {
			$parent = pdo_get( 'zofui_task_user',array('uniacid'=>$_W['uniacid'],'id'=>$parentid) );
			$level = model_user::levelRes($parent,$module);
			if( $level == 1 ){
				$module['pcgive'] = $module['pcgivea'];
				$module['pugive'] = $module['pugivea'];
				$module['ptgive'] = $module['ptgivea'];
				$module['tbgives'] = $module['tbgivesa'];
			}
			if( $level == 2 ){
				$module['pcgive'] = $module['pcgiveb'];
				$module['pugive'] = $module['pugiveb'];
				$module['ptgive'] = $module['ptgiveb'];
				$module['tbgives'] = $module['tbgivesb'];
			}
		}
		if( $parent['parent'] > 0 ) {
			$two = pdo_get( 'zofui_task_user',array('uniacid'=>$_W['uniacid'],'id'=>$parent['parent']) );
			$level = model_user::levelRes($two,$module);
			if( $level == 1 ){
				$module['pcgivet'] = $module['pcgiveta'];
				$module['pugivet'] = $module['pugiveta'];
				$module['ptgivet'] = $module['ptgiveta'];
				$module['tbgivest'] = $module['tbgivesta'];
			}
			if( $level == 2 ){
				$module['pcgivet'] = $module['pcgivetb'];
				$module['pugivet'] = $module['pugivetb'];
				$module['ptgivet'] = $module['ptgivetb'];
				$module['tbgivest'] = $module['tbgivestb'];
			}
		}
		if( $two['parent'] > 0 ) {
			$three = pdo_get( 'zofui_task_user',array('uniacid'=>$_W['uniacid'],'id'=>$two['parent']) );
			$level = model_user::levelRes($three,$module);
			if( $level == 1 ){
				$module['pcgiveth'] = $module['pcgivetha'];
				$module['pugiveth'] = $module['pugivetha'];
				$module['ptgiveth'] = $module['ptgivetha'];
				$module['tbgivesth'] = $module['tbgivestha'];
			}
			if( $level == 2 ){
				$module['pcgiveth'] = $module['pcgivethb'];
				$module['pugiveth'] = $module['pugivethb'];
				$module['ptgiveth'] = $module['ptgivethb'];
				$module['tbgivesth'] = $module['tbgivesthb'];
			}
		}
		
		
		if( $type == 1 ){ // 普通任务
			$oneper = $module['pcgive'];
			$twoper = $module['pcgivet'];
			$threeper = $module['pcgiveth'];
		}elseif( $type == 2 ){
			$oneper = $module['pugive'];
			$twoper = $module['pugivet'];
			$threeper = $module['pugiveth'];
		}elseif( $type == 3 ){
			$oneper = $module['ptgive'];
			$twoper = $module['ptgivet'];
			$threeper = $module['ptgiveth'];
		}elseif( $type == 4 ){
			$oneper = $module['tbgives'];
			$twoper = $module['tbgivest'];
			$threeper = $module['tbgivesth'];
		}
		
		// 上级奖励
		$upmoney = $twoupmoney = $threeupmoney = 0;
		if( $parentid > 0 && $oneper > 0 && $module['isdown'] != 2 ){
			$upmoney = $money*$oneper/100;
			if( $upmoney >= 0.01 ){
				
				model_user::updateUserCredit($parent['uid'],$upmoney,2,1);
				model_money::insertMoneyLog($parent['openid'],$upmoney,1,30,$parent['uid']);

				Util::addOrMinusOrUpdateData('zofui_task_user',array('giveparent'=>$upmoney),$userid);
				
				// 发通知
				//Message::downgive($parent['openid'],$upmoney,'',$replyer['nickname']);

				// 二级奖励
				$twoupmoney = $money*$twoper/100;
				if( $twoupmoney >= 0.01 && $twoper > 0 && $module['downnum'] >= 1 && $parent['parent'] > 0 ){
					
					model_user::updateUserCredit($two['uid'],$twoupmoney,2,1);
					model_money::insertMoneyLog($two['openid'],$twoupmoney,1,31,$two['uid']);

					Util::addOrMinusOrUpdateData('zofui_task_user',array('givetwo'=>$twoupmoney),$userid);
				}else{
					$twoupmoney = 0;
				}

				// 三级奖励
				$threeupmoney = $money*$threeper/100;
				if( $threeupmoney >= 0.01 && $threeper > 0 && $module['downnum'] >= 2 && $two['parent'] > 0 ){
					
					model_user::updateUserCredit($three['uid'],$threeupmoney,2,1);
					model_money::insertMoneyLog($three['openid'],$threeupmoney,1,32,$three['uid']);

					Util::addOrMinusOrUpdateData('zofui_task_user',array('givethree'=>$threeupmoney),$userid);
				}else{
					$threeupmoney = 0;
				}
				
			}else{
				$upmoney = 0;
			}
		}

		$uparr = array();
		if( $upmoney > 0 ) $uparr['giveparent'] = $upmoney;
		if( $twoupmoney > 0 ) $uparr['givetwo'] = $twoupmoney;
		if( $threeupmoney > 0 ) $uparr['givethree'] = $threeupmoney;

		if( !empty( $uparr ) ) {
			if( $type == 1 || $type == 2 ) {
				pdo_update('zofui_tasktb_task',$uparr,array('id'=>$taskid));
			}elseif( $type == 3 ){
				pdo_update('zofui_tasktb_tbtask',$uparr,array('id'=>$taskid));
			}
		}
	}


	static function setMessage( $set,$task,$topstr,$botstr,$fee,$taskname ){
		global $_W;
		// 新任务通知
		
		$sendinfo = Util::getCache('sendmess','all');
		if( !empty( $sendinfo ) ) return false;
		$set['threads'] = $set['threads'] > 0 ? $set['threads'] : 5;
		$set['threads'] = $set['threads'] > 50 ? 50 : $set['threads'];

		Util::setCache('sendmess','all',array('page'=>$set['threads'],'type'=>1,'task'=>$task,'total'=>0,'pro'=>array(),'topstr'=>$topstr,'botstr'=>$botstr,'fee'=>$fee,'taskname'=>$taskname));

		$code = md5( $set['sendmessauth'].$_W['authkey'].$task['id'] );
		$url = Util::createModuleUrl('sendmess',array('op'=>'start','code'=>$code,'taskid'=>$task['id']));
		Util::httpGet($url,'', 1);
		
	}

	static function isCanPub($data){
		
		if( $data['title'] == '' ) return '您还没填写标题';
		if( $data['num'] <= 0 ) return '任务总量不能小于等于0';
		if( $data['money'] <= 0 ) return '任务赏金不能小于等于0';
		if( $data['limitnum'] < 0 ) return '限制回复不能小于0';
		if( $data['iska'] == 1 && empty( $data['kagoodid'] ) ) return '请填写卡首屏的宝贝id';		
		if( $data['iska'] == 1 && empty( $data['kakey'] ) ) return '请填写卡首屏的关键字';

		return 200;
	}



	// 查询任务的回复
	static function getReply($where,$page,$num,$order,$iscache,$pager,$select,$str=''){
		global $_W;		
		$data = Util::structWhereStringOfAnd($where,'a');
		
		$commonstr = tablename('zofui_tasktb_taked') ." AS a LEFT JOIN ( SELECT openid,headimgurl,nickname,uid FROM ".tablename('zofui_task_user')." GROUP BY uid ) AS b ON a.userid = b.uid WHERE ".$data[0];

		$countStr = "SELECT COUNT(*) FROM ".$commonstr;
		$selectStr =  "SELECT $select FROM ".$commonstr;
		$res = Util::fetchFunctionInCommon($countStr,$selectStr,$data[1],$page,$num,$order,$iscache,$pager,$str);
		return $res;
	}

	// 查询提问
	static function getMessage($where,$page,$num,$order,$iscache,$pager,$select,$str=''){
		global $_W;		
		$data = Util::structWhereStringOfAnd($where,'a');
		$commonstr = tablename('zofui_tasktb_taskmessage') ."  AS a LEFT JOIN ".tablename('zofui_task_user')." AS b ON a.`userid` = b.`uid` AND a.uniacid = b.uniacid WHERE ".$data[0] .' GROUP BY a.`id` ';
		
		$countStr = "SELECT COUNT(*) FROM ".$commonstr;
		$selectStr =  "SELECT $select FROM ".$commonstr;
		$res = Util::fetchFunctionInCommon($countStr,$selectStr,$data[1],$page,$num,$order,$iscache,$pager,$str);
		return $res;
	}

	// 查询任务
	static function getAllTask($where,$page,$num,$order,$iscache,$pager,$select,$str=''){
		global $_W;		
		$data = Util::structWhereStringOfAnd($where,'a');
		$commonstr = tablename('zofui_tasktb_task') ."  AS a LEFT JOIN ".tablename('zofui_task_user')." AS b ON a.userid = b.uid WHERE ".$data[0].' GROUP BY a.`id` ';
		
		
		$countStr = "SELECT  COUNT(*) FROM ".$commonstr;
		$selectStr =  "SELECT $select FROM ".$commonstr;
		$res = Util::fetchFunctionInCommon($countStr,$selectStr,$data[1],$page,$num,$order,$iscache,$pager,$str);
		return $res;
	}

	// 查询我回复的
	static function getAllReply($where,$page,$num,$order,$iscache,$pager,$select,$str=''){
		global $_W;		
		$data = Util::structWhereStringOfAnd($where,'a');
		$commonstr = tablename('zofui_tasktb_task') ."  AS b LEFT JOIN ".tablename('zofui_tasktb_taked')." AS a ON b.`id` = a.`taskid` AND b.uniacid = a.uniacid WHERE ".$data[0] .' GROUP BY a.`id` ';

		$countStr = "SELECT COUNT(*) FROM ".$commonstr;
		$selectStr =  "SELECT $select FROM ".$commonstr;
		$res = Util::fetchFunctionInCommon($countStr,$selectStr,$data[1],$page,$num,$order,$iscache,$pager,$str);
		return $res;
	}

	static function getStatusInTask( $task,$isarealimit = 0,$isindex=false ){
		global $_W;

		$tasklimitnum = $task['limitnum'] > $task['num'] ? $task['num'] : $task['limitnum'];

		$mytaked = pdo_getall('zofui_tasktb_taked',array('userid'=>$_W['member']['uid'],'taskid'=>$task['id'],'uniacid'=>$_W['uniacid']));
		$comnum = 0;
		if( !empty( $mytaked ) ){
			foreach ($mytaked as $k => $v) {
				if( $v['status'] == 0 && $v['endtime'] < TIMESTAMP ){
					pdo_delete('zofui_tasktb_taked',array('uniacid'=>$_W['uniacid'],'id'=>$v['id']));
				}
				if( $v['status'] == 0 && $v['endtime'] > TIMESTAMP ){
					return array('status'=>1,'last'=>0,'reply'=>$v); // 抢了还没完成
				}
				if( $v['status'] >= 1 ){
					$comnum++;
				}
			}
		}else{
			$res = self::timesLimit( $task['userid'] );
			if( !$res ) return array('status'=>3,'last'=>0); // 达到了此发布者的数量限制

			if( !$isindex ){
				$lastnum = self::isEmpty( $task['id'],$task['num'] );
				if( $lastnum <= 0 ) return array('status'=>4,'last'=>0); // 被抢完了
			}


			//return array('status'=>0,'last'=>$tasklimitnum,'totallast'=>$lastnum); // 还未抢过
		}

		if( $comnum >= $tasklimitnum ) return array('status'=>2,'last'=>0); //抢的次数达到限制数量

		//IP限制
		if( $_W['set']['maxip'] > 0 ){
			$ip = getIp();
			$today = strtotime( date('Y-m-d',TIMESTAMP) );
			$ipnum = Util::countDataNumber('zofui_tasktb_taked',array('uniacid'=>$_W['uniacid'],'createtime>'=>$today,'ip'=>$ip));
			if( $ipnum >= $_W['set']['maxip'] ) return array('status'=>2,'last'=>0); //抢的次数达到限制数量
		}

		// 区域限制
		if( $isarealimit ){
			$sessionstr = $_W['member']['uid'].'a'.$_W['uniacid'];
			if( !empty( $_SESSION[$sessionstr] ) ){
				$task = pdo_get('zofui_tasktb_task',array('uniacid'=>$_W['uniacid'],'id'=>$task['id']),array('province','city','country'));
				$res = Util::checkLocation($isarealimit,$task['province'],$task['city'],$task['country']);
				
				if( !$res ) return array('status'=>5,'last'=>0); //不在限定的区域内
			}else{
				return array('status'=>5,'last'=>0); //不在限定的区域内
			}
		}

		// 标签
		$useric = iunserializer($task['useric']);
		if( !empty($useric) ){
			$myuseric = pdo_getall('zofui_tasktb_userics',array('uid'=>$_W['member']['uid'],'uniacid'=>$_W['uniacid']));
			if( empty($myuseric) ) return array('status'=>6,'last'=>0); //不在符合标签

			$usericarr = array();
			foreach ($myuseric as $v) {
				$usericarr[] = $v['icid'];
			}
			foreach ($useric as $v) {
				if( !in_array( $v, $usericarr) ){
					return array('status'=>6,'last'=>0); //不在符合标签
					break;
				}
			}
		}

		$res = self::timesLimit( $task['userid'] );
		if( !$res ) return array('status'=>3,'last'=>0); // 达到了此发布者的数量限制

		if( !$isindex ){
			$lastnum = self::isEmpty( $task['id'],$task['num'] );
			if( $lastnum <= 0 ) return array('status'=>4,'last'=>0); // 被抢完了
		}

		return array('status'=>0,'last'=>$tasklimitnum - $comnum ,'totallast'=>$lastnum);

	}


	static function ipLimit( $maxip ){
		global $_W;
		if( $maxip > 0 ){
			$ip = getIp();
			$today = strtotime( date('Y-m-d',TIMESTAMP) );
			$ipnum = Util::countDataNumber('zofui_tasktb_taked',array('uniacid'=>$_W['uniacid'],'createtime>'=>$today,'ip'=>$ip));
			if( $ipnum >= $maxip ) return true;
		}
		return false;	
	}

	static function timesLimit($pubuid){
		global $_W;

		// 数量限制 需要先计算数量限制 不能判断是不是存在，因为可能是后台发布的
		
		if( empty( $pubuid ) ){
			$pubuid = '';
		}else{
			$user = model_user::getSingleUser( $pubuid );
		}
		
		$limit = 0;
		if( $_W['set']['permaxre'] > 0 ) $limit = $_W['set']['permaxre'];
		if( $user['limitnum'] > 0 )  $limit = $user['limitnum'];
		
		if( $limit > 0 ){
			$today = strtotime( date('Y-m-d',TIMESTAMP) );

			if( $_W['set']['permaxday'] > 0 ) $today = TIMESTAMP - $_W['set']['permaxday']*3600*24;
			if( $user['limitday'] > 0 ) $today = TIMESTAMP - $user['limitday']*3600*24;

			$sql = " SELECT `id` FROM ".tablename('zofui_tasktb_taked')." WHERE uniacid = :uniacid AND userid = :userid AND pubuid = :pubuid AND createtime >= :createtime AND endtime > :endtime GROUP BY `taskid` ";
			$allre = pdo_fetchall($sql,array(':uniacid'=>$_W['uniacid'],':userid'=>$_W['member']['uid'],':pubuid'=>$pubuid,':createtime'=>$today,'endtime'=>TIMESTAMP));

			if( count($allre) >= $limit ) return false; // 不能再抢此发布者的任务
		}
		
		return true;
	}


	// 是不是已经被抢完了
	static function isEmpty($taskid,$tasknum){
		global $_W;

		$taked = Util::countDataNumber('zofui_tasktb_taked',array('taskid'=>$taskid,'endtime>'=>TIMESTAMP));
		return $tasknum - $taked;
	}

	// 试用任务是不是已经被抢完了
	static function isEmptyUse($taskid,$tasknum){
		global $_W;

		$taked = Util::countDataNumber('zofui_tasktb_usetasklog',array('uniacid'=>$_W['uniacid'],'isactivity'=>0,'taskid'=>$taskid));;
		return $tasknum - $taked;
	}

	// 采纳任务
	static function agreeTask($set,$taked,$task){
		global $_W;

		$counting = Util::getCache('counttaked',$taked['id']);
		if( is_array( $counting ) && $counting['status'] == 1 && $counting['time'] > (time() - 60) ) {
			return false;
		}
		Util::setCache('counttaked',$taked['id'],array('status'=>1,'time'=>time()));
		
		$replyer = model_user::getSingleUser( $taked['userid'] );
		
		// 任务分类
		$sort = pdo_get('zofui_tasktb_tasksort',array('uniacid'=>$_W['uniacid'],'id'=>$task['sortid']),array('other'));
		$sortother = iunserializer($sort['other']);
		if( $sortother['takesever'] > 0 ){
			$set['replyserver'] = $sortother['takesever'];
		}
		
		$server = 0;
		// 会员等级
		$level = model_user::levelRes($replyer,$set);
		if( $level == 1 ) $set['replyserver'] = $set['replyservera'];
		if( $level == 2 ) $set['replyserver'] = $set['replyserverb'];
		
		if( $set['replyserver'] > 0 ){
			$server = round( $taked['money']*$set['replyserver']/100 ,2);
			if( $server < 0.01 ) $server = 0;
		}

		

		// 发钱
		$res = model_user::updateUserCredit($taked['userid'],$taked['money'],2,1);
		model_money::insertMoneyLog($taked['openid'],$taked['money'],1,8,$taked['userid']);

		// 扣服务费
		if( $res && $server > 0 ){
			model_user::updateUserCredit($taked['userid'],-$server,2,1);
			model_money::insertMoneyLog($taked['openid'],-$server,1,9,$taked['userid']);
		}

		if( $res ){
			// 上级奖励
			$upmoney = $twoupmoney = $threeupmoney = 0;
			
			// 先计算会员等级
			if( $replyer['parent'] > 0 ) {
				$parent = pdo_get( 'zofui_task_user',array('uniacid'=>$_W['uniacid'],'id'=>$replyer['parent']) );
				$level = model_user::levelRes($parent,$set);
				if( $level == 1 ){
					$set['commongive'] = $set['commongivea'];
				}
				if( $level == 2 ){
					$set['commongive'] = $set['commongiveb'];
				}
			}
			if( $parent['parent'] > 0 ) {
				$two = pdo_get( 'zofui_task_user',array('uniacid'=>$_W['uniacid'],'id'=>$parent['parent']) );
				$level = model_user::levelRes($two,$set);
				if( $level == 1 ){
					$set['cgivet'] = $set['cgiveta'];
				}
				if( $level == 2 ){
					$set['cgivet'] = $set['cgivetb'];
				}
			}
			if( $two['parent'] > 0 ) {
				$three = pdo_get( 'zofui_task_user',array('uniacid'=>$_W['uniacid'],'id'=>$two['parent']) );
				$level = model_user::levelRes($three,$set);
				if( $level == 1 ){
					$set['cgiveth'] = $set['cgivetha'];
				}
				if( $level == 2 ){
					$set['cgiveth'] = $set['cgivethb'];
				}
			}
			
			if( $replyer['parent'] > 0 && $set['commongive'] > 0 && $set['isdown'] != 2 ){
				$upmoney = $taked['money']*$set['commongive']/100;
				if( $upmoney >= 0.01 ){
					
					model_user::updateUserCredit($parent['uid'],$upmoney,2,1);
					model_money::insertMoneyLog($parent['openid'],$upmoney,1,6,$parent['uid']);

					Util::addOrMinusOrUpdateData('zofui_task_user',array('giveparent'=>$upmoney),$replyer['id']);
					
					// 发通知
					Message::downgive($parent['uid'],$parent['openid'],$upmoney,$task['title'],$replyer['nickname']);


					// 二级奖励
					$twoupmoney = $taked['money']*$set['cgivet']/100;
					if( $twoupmoney >= 0.01 && $set['cgivet'] > 0 && $set['downnum'] >= 1 && $parent['parent'] > 0 ){
						
						model_user::updateUserCredit($two['uid'],$twoupmoney,2,1);
						model_money::insertMoneyLog($two['openid'],$twoupmoney,1,28,$two['uid']);

						Util::addOrMinusOrUpdateData('zofui_task_user',array('givetwo'=>$twoupmoney),$replyer['id']);
					}else{
						$twoupmoney = 0;
					}

					// 三级奖励
					$threeupmoney = $taked['money']*$set['cgiveth']/100;
					if( $threeupmoney >= 0.01 && $set['cgiveth'] > 0 && $set['downnum'] >= 2 && $two['parent'] > 0 ){
						
						model_user::updateUserCredit($three['uid'],$threeupmoney,2,1);
						model_money::insertMoneyLog($three['openid'],$threeupmoney,1,29,$three['uid']);

						Util::addOrMinusOrUpdateData('zofui_task_user',array('givethree'=>$threeupmoney),$replyer['id']);
					}else{
						$threeupmoney = 0;
					}

				}else{
					$upmoney = 0;
				}
			}

			// 连续奖励
			$iscanewai = 0; // 默认不给额外奖励
			$where = array('uniacid'=>$_W['uniacid'],'continueid'=>$task['continueid'],'userid'=>$taked['userid'],'ewai>'=>0.01);
			$payednum = Util::countDataNumber('zofui_tasktb_taked',$where);

			if( $task['continue'] == 1 && $payednum <= 0 ){
				$continue = pdo_get('zofui_tasktb_continue',array('uniacid'=>$_W['uniacid'],'id'=>$task['continueid'],'isback'=>0));
				if( !empty($continue) ){
					$alltask = pdo_getall('zofui_tasktb_task',array('uniacid'=>$_W['uniacid'],'continueid'=>$task['continueid']));	
					if( is_array( $alltask ) ){
						$totalewai = 0;
						$trueewai = 0;
						foreach ( $alltask as $v ) {
							if( $v['id'] != $task['id'] ){ // 只计算连续发布的其他任务
								$totalewai ++;
								$thisreply = pdo_get('zofui_tasktb_taked',array('status'=>2,'uniacid'=>$_W['uniacid'],'userid'=>$taked['userid'],'taskid'=>$v['id']));
								if( !empty( $thisreply ) && $thisreply['ewai'] <= 0 ){
									$trueewai ++;
								}
							}
						}
					}
					if( $totalewai == $trueewai && $totalewai > 0 ) $iscanewai = 1;
				}
			}
			$ewai = 0;
			if( $iscanewai == 1 ){
				$ewai = $continue['money'];
				if( $ewai >= 0.01 ){
					model_user::updateUserCredit($taked['userid'],$ewai,2,1);
					model_money::insertMoneyLog($taked['openid'],$ewai,1,11,$taked['userid']);

					// 增加已发金额
					Util::addOrMinusOrUpdateData('zofui_tasktb_continue',array('prizemoney'=>$ewai),$continue['id']);
				}
			}			

			// 更新回复
			$update = array('dealtime'=>TIMESTAMP,'server'=>$server,'status'=>2,'giveparent'=>$upmoney,'twoupmoney'=>$twoupmoney,'threeupmoney'=>$threeupmoney,'ewai'=>$ewai);
			pdo_update('zofui_tasktb_taked',$update,array('uniacid'=>$_W['uniacid'],'id'=>$taked['id']));

			// 增加发布、采纳数量
			$puber = model_user::getSingleUser( $taked['pubuid'] );
			Util::addOrMinusOrUpdateData('zofui_task_user',array('pubnumber'=>1,'acceptnumber'=>1),$puber['id']);
			
			if( !empty($task['falsepuber']) ){
				$falsepuber = model_task::getFalsepub( $task['falsepuber'] );
				Util::addOrMinusOrUpdateData('zofui_tasktb_puber',array('pub'=>1,'take'=>1,'cost'=>$taked['money']+$ewai),$falsepuber['id']);
				Util::deleteCache( 'fpuber',$falsepuber['id'] );
			}

			// 增加完成数量
			Util::addOrMinusOrUpdateData('zofui_task_user',array('replynumber'=>1,'acceptednumber'=>1),$replyer['id']);

			// 平台发布量、完成量
			pdo_query("UPDATE ".tablename('zofui_tasktb_scan')." SET `pubed` = `pubed` + 1,`commpubed` = `commpubed` + 1, `comed` = `comed` + 1,`commcomed` = `commcomed` + 1 WHERE `uniacid` = '{$_W['uniacid']}' ");

			Util::deleteCache( 'u',$taked['userid'] );
			Util::deleteCache( 'u',$taked['pubuid'] );

			//给回复者发消息
			Message::agreetask($taked['userid'],$taked['openid'],$task['id'],$taked['money'],$ewai,$server,$task['title'],$puber['nickname']);
			Util::deleteCache( 'counttaked',$taked['id'] );
			return true;
		}
		Util::deleteCache( 'counttaked',$taked['id'] );
		return false;

	}

	// 拒绝回复
	static function refuseTask($taked,$reason,$task,$pubernick){
		global $_W;

		// 更新回复
		$update = array('dealtime'=>TIMESTAMP,'server'=>$server,'status'=>3,'reason'=>$reason);
		$res = pdo_update('zofui_tasktb_taked',$update,array('uniacid'=>$_W['uniacid'],'id'=>$taked['id']));


		// 增加发布数量
		if( !empty($taked['pubuid']) ){
			$puber = model_user::getSingleUser( $taked['pubuid'] );
			Util::addOrMinusOrUpdateData('zofui_task_user',array('pubnumber'=>1),$puber['id']);
		}

		if( !empty($task['falsepuber']) ){
			$falsepuber = model_task::getFalsepub( $task['falsepuber'] );
			Util::addOrMinusOrUpdateData('zofui_tasktb_puber',array('pub'=>1),$falsepuber['id']);
			Util::deleteCache( 'fpuber',$falsepuber['id'] );
		}

		// 增加回复数量
		$replyer = model_user::getSingleUser( $taked['userid'] );
		Util::addOrMinusOrUpdateData('zofui_task_user',array('replynumber'=>1),$replyer['id']);

		// 平台发布量
		pdo_query("UPDATE ".tablename('zofui_tasktb_scan')." SET `pubed` = `pubed` + 1,`commpubed` = `commpubed` + 1 WHERE `uniacid` = '{$_W['uniacid']}' ");

		Util::deleteCache( 'u',$taked['userid'] );
		Util::deleteCache( 'u',$taked['pubuid'] );

		
		//给回复者发消息
		Message::refusetask($taked['userid'],$taked['openid'],$taked['taskid'],$reason,$task['title'],$pubernick);
		return $res;
	}

	// 结算任务
	static function countTask($task,$backserver=false){
		global $_W;

		// 先采纳未处理的回复
		$taked = pdo_getall('zofui_tasktb_taked',array('status'=>1,'taskid'=>$task['id']));
		if( is_array( $taked ) ){
			foreach ($taked as $v) {
				self::agreeTask($_W['set'],$v,$task);
			}
		}

		// 退钱
		$backmoney = 0;
		$payed = 0;
		$allagree = pdo_getall('zofui_tasktb_taked',array('status'=>2,'taskid'=>$task['id']),array('money'));
		if( is_array( $allagree ) ){
			foreach ($allagree as $v) {
				$payed += $v['money'];
			}
		}
		$backmoney = $task['num']*$task['money'] - $payed;
		
		// 计算退还额外奖励
		$diff = 0;
		if( $task['continue'] == 1 ){
			$where = array('uniacid'=>$_W['uniacid'],'continueid'=>$task['continueid'],'iscount'=>0);
			$lasttask = Util::countDataNumber('zofui_tasktb_task',$where,' AND `id` != '.$task['id']);
			if( $lasttask <= 0 ){ // 是最后一个了 退还剩余额外奖励
				$continue = pdo_get('zofui_tasktb_continue',array('uniacid'=>$_W['uniacid'],'id'=>$task['continueid'],'isback'=>0));
				if( !empty($continue) && $continue['backmoney'] <= 0 ){ // 表示还没有退还
					$diff = $continue['totalmoney'] - $continue['prizemoney'];
					$backmoney += $diff;
				}
			}
		}

		if( $backserver ) {
			$backmoney = $backmoney + $task['costserver'] + $task['costka'] + $task['costtop'];
		}

		$backmoney = round($backmoney,2);
		if( $backmoney > 0 ){
			$res = model_user::updateUserCredit($task['userid'],$backmoney,2,1);
			model_money::insertMoneyLog($task['puber'],$backmoney,1,10,$task['userid']);
		}
		if( $diff > 0 ){
			pdo_update('zofui_tasktb_continue',array('backmoney'=>$diff,'isback'=>1),array('uniacid'=>$_W['uniacid'],'id'=>$continue['id']));
		}

		// 更新任务
		$update = array('counttime'=>TIMESTAMP,'iscount'=>1,'backmoney'=>$backmoney);
		$res = pdo_update('zofui_tasktb_task',$update,array('uniacid'=>$_W['uniacid'],'id'=>$task['id']));
				
		// 结算通知
		Message::counttask($task['userid'],$task['puber'],$task['id'],$backmoney,$task['title']);

		return $res;
	}

	// 验证是否可投诉
	static function isCanComplain($taskid){
		global $_W;

		$sql = "SELECT * FROM ". tablename('zofui_tasktb_taked') ." WHERE uniacid = :uniacid AND userid = :userid AND taskid = :taskid AND status IN(1,2,3) ";
		$where = array(':userid'=>$_W['member']['uid'],':uniacid'=>$_W['uniacid'],':taskid'=>$taskid);
		$cancomplain = pdo_fetch($sql,$where);
		if( empty( $cancomplain ) ) return '您还不能投诉，回复任务后才能投诉';

		$isset = pdo_get('zofui_tasktb_complain',array('uniacid'=>$_W['uniacid'],'userid'=>$_W['member']['uid'],'taskid'=>$taskid));
		if( !empty( $isset ) ) return '您已经投诉过了，不能再投诉';

		return 200;
	}


	// 完成试用任务 type=1,直接完成，type=2转为完成
	static function sucUseTask($set,$taked,$task,$type){
		global $_W;
		if( !is_array( $taked ) || !is_array( $task ) ) return false;


		$replyer = model_user::getSingleUser( $taked['userid'] );

		// 发钱
		$res = model_user::updateUserCredit($taked['userid'],$task['money'],2,1);
		model_money::insertMoneyLog($taked['openid'],$task['money'],1,20,$taked['userid']);
		
		if( $res ){

			// 更新试用回复
			if( $type == 1 ){ // 直接完成
				$update = array('suctime'=>TIMESTAMP,'prizemoney'=>$task['money'],'status'=>5);
			}else{ // 转为完成
				$update = array('tosuctime'=>TIMESTAMP,'prizemoney'=>$task['money'],'status'=>5);
			}
		
			// 上级奖励
			$upmoney = $twoupmoney = $threeupmoney = 0;
			
			// 先计算会员等级
			if( $replyer['parent'] > 0 ) {
				$parent = pdo_get( 'zofui_task_user',array('uniacid'=>$_W['uniacid'],'id'=>$replyer['parent']) );
				$level = model_user::levelRes($parent,$set);
				if( $level == 1 ){
					$set['usetaskgiveparent'] = $set['usetaskgiveparenta'];
				}
				if( $level == 2 ){
					$set['usetaskgiveparent'] = $set['usetaskgiveparentb'];
				}
			}
			if( $parent['parent'] > 0 ) {
				$two = pdo_get( 'zofui_task_user',array('uniacid'=>$_W['uniacid'],'id'=>$parent['parent']) );
				$level = model_user::levelRes($two,$set);
				if( $level == 1 ){
					$set['ugivet'] = $set['ugiveta'];
				}
				if( $level == 2 ){
					$set['ugivet'] = $set['ugivetb'];
				}
			}
			if( $two['parent'] > 0 ) {
				$three = pdo_get( 'zofui_task_user',array('uniacid'=>$_W['uniacid'],'id'=>$two['parent']) );
				$level = model_user::levelRes($three,$set);
				if( $level == 1 ){
					$set['ugiveth'] = $set['ugivetha'];
				}
				if( $level == 2 ){
					$set['ugiveth'] = $set['ugivethb'];
				}
			}
			
			if( $replyer['parent'] > 0 && $set['isdown'] != 2 && $set['usetgivetype'] > 0 && $set['usetaskgiveparent'] > 0 ){
				$upmoney = $set['usetaskgiveparent'];
				if( $set['usetgivetype'] == 1 ) $upmoney = $task['money']*$set['usetaskgiveparent']/100;

				if( $upmoney >= 0.01 ){
					
					model_user::updateUserCredit($parent['uid'],$upmoney,2,1);
					model_money::insertMoneyLog($parent['openid'],$upmoney,1,6,$parent['uid']);

					Util::addOrMinusOrUpdateData('zofui_task_user',array('giveparent'=>$upmoney),$replyer['id']);
					
					// 发通知
					Message::downgive($parent['uid'],$parent['openid'],$upmoney,$task['title'],$replyer['nickname']);
				}else{
					$upmoney = 0;
				}

				// 二级奖励
				$twoupmoney = sprintf('%.2f',$set['ugivet']);
				if( $set['usetgivetype'] == 1 ) $twoupmoney = $task['money']*$set['ugivet']/100;

				if( $twoupmoney >= 0.01 && $set['ugivet'] > 0 && $set['downnum'] >= 1 && $parent['parent'] > 0 ){
					
					model_user::updateUserCredit($two['uid'],$twoupmoney,2,1);
					model_money::insertMoneyLog($two['openid'],$twoupmoney,1,28,$two['uid']);

					Util::addOrMinusOrUpdateData('zofui_task_user',array('givetwo'=>$twoupmoney),$replyer['id']);
				}else{
					$twoupmoney = 0;
				}

				// 三级奖励
				$threeupmoney = sprintf('%.2f',$set['ugiveth']);
				if( $set['usetgivetype'] == 1 ) $threeupmoney = $task['money']*$set['ugiveth']/100;

				if( $threeupmoney >= 0.01 && $set['ugiveth'] > 0 && $set['downnum'] >= 2 && $two['parent'] > 0 ){
					
					model_user::updateUserCredit($three['uid'],$threeupmoney,2,1);
					model_money::insertMoneyLog($three['openid'],$threeupmoney,1,29,$three['uid']);

					Util::addOrMinusOrUpdateData('zofui_task_user',array('givethree'=>$threeupmoney),$replyer['id']);
				}else{
					$threeupmoney = 0;
				}

			}
			$update['giveparent'] = $upmoney;
			$update['givetwo'] = $twoupmoney;
			$update['givethree'] = $threeupmoney;

			pdo_update('zofui_tasktb_usetasklog',$update,array('uniacid'=>$_W['uniacid'],'id'=>$taked['id']));

			// 增加发布、采纳数量
			$puber = model_user::getSingleUser( $task['userid'] );
			Util::addOrMinusOrUpdateData('zofui_task_user',array('pubnumber'=>1,'acceptnumber'=>1),$puber['id']);
			
			// 增加完成数量
			Util::addOrMinusOrUpdateData('zofui_task_user',array('replynumber'=>1,'acceptednumber'=>1),$replyer['id']);

			// 平台发布量、完成量
			pdo_query("UPDATE ".tablename('zofui_tasktb_scan')." SET `pubed` = `pubed` + 1,`usepubed` = `usepubed` + 1, `comed` = `comed` + 1,`usecomed` = `usecomed` + 1 WHERE `uniacid` = '{$_W['uniacid']}' ");


			Util::deleteCache( 'u',$taked['userid'] );
			Util::deleteCache( 'u',$task['userid'] );

			//给回复者发消息
			Message::sucusetask($taked['userid'],$taked['openid'],$task['id'],$task['money'],$task['title'],$puber['nickname']);
			return true;
		}
		return false;
	}



	// 试用任务失败
	static function failUseTask( $taked,$reason,$task ){
		global $_W;

		$update = array('status'=>6,'failtime'=>TIMESTAMP,'reason'=>$reason);
		$res = pdo_update('zofui_tasktb_usetasklog', $update ,array('id'=>$taked['id']));
		if( $res ){
			// 发消息
			Message::failusetask($taked['userid'],$taked['openid'],$taked['taskid'],$reason,$task['title']);
		}
		return $res;
	}

	// 结算试用任务
	static function countUseTask($task,$backserver=false){
		global $_W;

		// 先完成未处理的回复
		$taked = pdo_getall('zofui_tasktb_usetasklog',array('status'=>4,'taskid'=>$task['id']));
		if( is_array( $taked ) ){
			foreach ($taked as $v) {
				self::sucUseTask($_W['set'],$v,$task,1);
			}
		}

		// 先失败雇员未处理的任务
		$taked = pdo_getall('zofui_tasktb_usetasklog',array('taskid'=>$task['id'],'status'=>array('0','1')));
		if( is_array( $taked ) ){
			foreach ($taked as $v) {
				self::failUseTask( $v,'未及时处理，系统自动设为失败',$task );
			}
		}

		// 退钱
		$backmoney = 0;
		$payed = 0;
		$allsuc = pdo_getall('zofui_tasktb_usetasklog',array('status'=>5,'taskid'=>$task['id']),array('prizemoney'));
		if( is_array( $allsuc ) ){
			foreach ($allsuc as $v) {
				$payed += $v['prizemoney'];
			}
		}
		$backmoney = $task['num']*$task['money'] - $payed;

		if( $backserver ) {
			$backmoney = $backmoney + $task['costserver'] + $task['costka'] + $task['costtop'] + $task['costfindkey'];
		}

		// 计算退还额外奖励
		$backmoney = round($backmoney,2);
		if( $backmoney > 0 ){
			$res = model_user::updateUserCredit($task['userid'],$backmoney,2,1);
			model_money::insertMoneyLog($task['puber'],$backmoney,1,21,$task['userid']);
		}
				
		// 更新任务
		$update = array('counttime'=>TIMESTAMP,'iscount'=>1,'backmoney'=>$backmoney);
		$res = pdo_update('zofui_tasktb_task',$update,array('uniacid'=>$_W['uniacid'],'id'=>$task['id']));
			
		// 结算通知
		Message::counttask($task['userid'],$task['puber'],$task['id'],$backmoney,$task['title']);

		return $res;
	}


	// 审核通过试用任务
	static function passUseTask( $task,$taked ){
		global $_W;
		if( !is_array( $task ) || !is_array( $taked ) ) return false;

		if( $taked['status'] != 0 ) return false;

		$update = array('status'=>1,'passortime'=>TIMESTAMP);
		$res = pdo_update('zofui_tasktb_usetasklog', $update ,array('id'=>$taked['id']));
		if( $res ){
			Message::passusetask($taked['userid'],$taked['openid'],$task['id'],$task['title']);
		}
		return $res;
	}

	// 审核不通过试用任务
	static function passfailUseTask( $task,$taked ){
		global $_W;
		if( !is_array( $task ) || !is_array( $taked ) ) return false;

		if( $taked['status'] != 0 ) return false;

		$update = array('status'=>2,'passortime'=>TIMESTAMP,'isactivity'=>1);
		$res = pdo_update('zofui_tasktb_usetasklog', $update ,array('id'=>$taked['id']));

		if( $res && $task['isempty'] == 1 ){
			pdo_update('zofui_tasktb_task',array('isempty'=>0),array('id'=>$task['id']));
		}
		
		return $res;
	}

	// 给试用任务者发提醒
	static function noticeUsetask( $taked,$task,$content ){
		global $_W;
		if( empty( $content ) ) return false;
		$data = array(
			'uniacid' => $_W['uniacid'],
			'takedid' => $taked['id'],
			'taskid' => $task['id'],
			'content' => $content,
			'type' => 1,
			'createtime' => TIMESTAMP,
		);
		$res = pdo_insert('zofui_tasktb_useaddcontent',$data);
		if( $res ){
			Message::noticeusetask($taked['userid'],$taked['openid'],$taked['taskid'],$task['title'],$content);
		}
		return $res;
	}

	// 我的试用任务
	static function getAllMyUsetask($where,$page,$num,$order,$iscache,$pager,$select,$str=''){
		global $_W;		
		$data = Util::structWhereStringOfAnd($where,'b');
		$commonstr = tablename('zofui_tasktb_task') ."  AS a LEFT JOIN ".tablename('zofui_tasktb_usetasklog')." AS b ON a.id = b.taskid WHERE ".$data[0];

		$countStr = "SELECT  COUNT(*) FROM ".$commonstr;
		$selectStr =  "SELECT $select FROM ".$commonstr;
		$res = Util::fetchFunctionInCommon($countStr,$selectStr,$data[1],$page,$num,$order,$iscache,$pager,$str);
		return $res;
	}	


	static function downTask( $taskid,$from,$set ){
		global $_W;
		set_time_limit(0);
		ini_set('memory_limit','100M');

		$info = pdo_get('zofui_tasktb_task',array('uniacid'=>$_W['uniacid'],'id'=>$taskid),array('id','title','type'));
		if( empty( $info ) || $info['type'] != 0 ) message('未找到任务',referer(),'error');

		$where = array('uniacid'=>$_W['uniacid'],'taskid'=>$info['id']);
		$where['status>'] = 0.1;

		$order = ' `id` DESC ';

		$by = ' id,createtime,openid,status,content,images,isscan,userid ';
		$info = Util::getAllDataInSingleTable('zofui_tasktb_taked',$where,1,99999,$order,false,false,$by);
		$list = $info[0];


		/* 输入到CSV文件 */
		$html = "\xEF\xBB\xBF".$html; //添加BOM
		/* 输出表头 */
		$html .= '编号' . ",";
		$html .= '回复者' . ",";
		if( ( $from == 'app' && $set['isdowntel'] == 1 ) || $from == 'web' ) $html .= '手机号' . ",";
		$html .= '回复内容' . ",";
		$html .= '回复时间' . ",";
		$html .= '状态' . ",";
		$html .= "\n";
		
		if( !empty( $list ) ) {
			foreach ( $list as $k => $v ) {
				
	 			$user = model_user::getSingleUser( $v['userid'] );
	 			
	 			$subform = iunserializer( base64_decode( $v['subform'] ) );
				if( !is_array( $subform ) ) {
					$subform = iunserializer( $v['subform'] );
				}

	 			$substr = '';
	 			if( !empty( $subform ) ) {

	 				foreach ($subform as $vv) {
	 					if( $vv['type'] != 'img' ) {
	 						$substr .= $vv['name'].':'.$vv['value']." ";
	 					}
	 				}
	 			}

	 			$status = '';
	 			if( $v['status'] == 1 ) $status = '待采纳';
	 			if( $v['status'] == 2 ) $status = '已采纳';
	 			if( $v['status'] == 3 ) $status = '被拒绝';
	 			$time = date('Y-m-d H:i:s',$v['createtime']);

	 			$html .= ($k + 1) . ",";
				$html .= $user['nickname'] . ",";
				if( ( $from == 'app' && $set['isdowntel'] == 1 ) || $from == 'web' )  $html .= $user['mobile'] . ",";
				$html .= $v['content'].$substr.",";
				$html .= $time.",";
				$html .= $status.",";
				$html .= "\n";

				$addlist = pdo_getall('zofui_tasktb_remindlog',array('takedid'=>$v['id'],'mtype'=>1),array('content','createtime','images'));

				if( !empty( $addlist ) ) {

					foreach ( $addlist as $vv ) {
						$time = date('Y-m-d H:i:s',$vv['createtime']);

						$html .= ",";
						$html .= ",";
						if( ( $from == 'app' && $set['isdowntel'] == 1 ) || $from == 'web' )  $html .= ",";
						$html .= $vv['content'].",";
						$html .= $time.",";
						$html .= ",";
						$html .= "\n";
					}
				}

			}
		}

		/* 输出CSV文件 */
		header("Content-type:text/csv");
		header("Content-Disposition:attachment; filename=任务数据.csv");
		//header("Content-Type: application/vnd.ms-excel; charset=utf8"); 
		//header("Content-Disposition: attachment; filename=任务数据.xls"); 

		echo $html;
		exit;
	}

	// 删除任务图片
	static function deleteTaskImg($id,$type){
		global $_W;
		set_time_limit(0);

		if( $type == 'task' ){
			if( is_array($id) ){
				$task = $id;
			}else{
				$task = pdo_get('zofui_tasktb_task',array('uniacid'=>$_W['uniacid'],'id'=>$id));
			}  
			
			if( empty($task) ) return false;

			$images = iunserializer($task['images']);
			if( !empty($images) ){
				foreach ($images as $v) {
					Util::deleteImage($v);
				}
			}

			$taked = pdo_getall('zofui_tasktb_taked',array('taskid'=>$task['id']));

			if( !empty($taked) ){
				foreach ($taked as $v) {
					$images = iunserializer($v['images']);
					if( !empty($images) ){
						foreach ($images as $vv) {
							Util::deleteImage($vv);
						}
					}

					// 表单内容
					$subform = iunserializer( base64_decode( $v['subform'] ) );
					if( !empty( $subform ) ) {
						foreach ($subform as $vv) {
							if( $vv['type'] == 'img' ){
								foreach ($vv['value'] as $vvv) {
									Util::deleteImage($vvv);
								}
							}
						}
					}

					// 补充内容
					$addlist = pdo_getall('zofui_tasktb_remindlog',array('uniacid'=>$_W['uniacid'],'takedid'=>$v['id']));
					if( !empty( $addlist ) ){
						foreach ( $addlist as $vv ) {

							if( !empty( $vv['images'] ) ) $images = iunserializer( $vv['images'] );
							if( !empty( $images ) && is_array( $images ) ){
								foreach ($images as $vvv) {
									Util::deleteImage($vvv);
								}
							}

						}
					}

				}
			}

		}elseif( $type == 'tbtask' ){
			
			if( is_array($id) ){
				$task = $id;
			}else{
				$task = pdo_get('zofui_tasktb_tbtask',array('uniacid'=>$_W['uniacid'],'id'=>$id));
			} 

			if( empty($task) ) return false;

			$images = iunserializer($task['images']);
			if( !empty($images) ){
				foreach ($images as $v) {
					Util::deleteImage($v);
				}
			}
			$hideimages = iunserializer($task['hideimages']);
			if( !empty($hideimages) ){
				foreach ($hideimages as $v) {
					Util::deleteImage($v);
				}
			}

			$taked = pdo_getall('zofui_tasktb_tbtaked',array('taskid'=>$task['id']));
			if( !empty($taked) ){
				foreach ($taked as $v) {

					if( !empty( $v['takecontent'] ) ){
						$content = iunserializer( $v['takecontent'] );
						if( !empty( $content['images'] ) && is_array( $content['images'] ) ){
							foreach ($content['images'] as $vv) {
								Util::deleteImage($vv);
							}
						}
					}
					if( !empty( $v['stepcontent'] ) ){
						$content = iunserializer( $v['stepcontent'] );
						if( !empty( $content ) && is_array( $content ) ){
							foreach ($content as $vv) {
								foreach ((array)$vv['images'] as $vvv) {
									Util::deleteImage($vvv);
								}
							}
						}
					}
				}
			}

		// 试用任务
		}elseif( $type == 'usetask' ){	
			
			if( is_array($id) ){
				$task = $id;
			}else{
				$task = pdo_get('zofui_tasktb_task',array('uniacid'=>$_W['uniacid'],'id'=>$id));
			} 

			if( empty($task) ) return false;
			
			$images = iunserializer($task['images']);
			if( !empty($images) ){
				foreach ($images as $v) {
					Util::deleteImage($v);
				}
			}
			if( !empty($task['pic']) ){
				Util::deleteImage($task['pic']);
			}
			if( !empty($task['prizeimg']) ){
				Util::deleteImage($task['prizeimg']);
			}

			$taked = pdo_getall('zofui_tasktb_usetasklog',array('taskid'=>$task['id']));
			if( !empty($taked) ){
				foreach ($taked as $v) {

					if( !empty( $v['subcontent'] ) ){
						$content = iunserializer( $v['subcontent'] );
						if( !empty( $content['img'] ) && is_array( $content['img'] ) ){
							foreach ($content['img'] as $vv) {
								Util::deleteImage($vv);
							}
						}
					}
					
					$addcontent = pdo_getall('zofui_tasktb_useaddcontent',array('takedid'=>$v['id']));
					if( !empty( $addcontent ) ){
						foreach ($addcontent as $addimg) {
							$img = iunserializer( $addimg['img'] );
							if( !empty( $img ) && is_array( $img ) ){
								foreach ($img as $vv) {
									Util::deleteImage($vv);
								}
							}
						}
					}

				}
			}	
		}
		return true;

	}



}