<?php 

class pay {

	//file_put_contents(MODULE_ROOT."/params.log", var_export($params, true).PHP_EOL, FILE_APPEND);

	static function payResult($params){
		global $_W;
		
		if($params['result'] == 'success' && $params['from'] == 'notify'){
	
			$orderinfo = pdo_get('zofui_tasktb_paylog',array('status'=>0,'orderid'=>$params['tid']));
			$_W['uniacid'] = $orderinfo['uniacid'];
			
			if(!empty($orderinfo) && $orderinfo['fee'] == $params['fee']){
		
				$res = pdo_update('zofui_tasktb_paylog',array('status' => 1,'uorderid'=>$params['uniontid']),array('uniacid' => $orderinfo['uniacid'],'orderid' => $params['tid']));	
				//发送支付成功通知
				
				// 保证金
				if($res && $orderinfo['type'] == 1){			
					$res = Util::addOrMinusOrUpdateData('zofui_task_user',array('deposit'=>$params['fee']),$orderinfo['uid']);
					Util::deleteCache('u',$orderinfo['userid']);
					//增加记录
					if($res) $res = model_money::insertMoneyLog($orderinfo['openid'],$params['fee'],2,2,$orderinfo['userid'] );

					//发通知
				}
				//充值
				if($res && $orderinfo['type'] == 2){
					$set = Util::getModuleConfig();
					$money = $params['fee'];
					if( $set['moneytype'] == 1 ){
						$per =  empty( $set['creditper'] ) ? 1 : $set['creditper'];
						$money = $params['fee']*$per;
					}
					
					
					$res = model_user::updateUserCredit($orderinfo['userid'],$money,2,1);

					//增加记录
					if($res){
						Util::deleteCache('u',$orderinfo['userid']);
						model_money::insertMoneyLog($orderinfo['openid'],$money,1,2,$orderinfo['userid']);
					} 
					//发通知
				}

				if($res && $orderinfo['type'] == 3){ // 升级会员
					$set = Util::getModuleConfig();
					
					$level = 0;
					$month = 0;
					if( $orderinfo['utype'] == 1 ) {
						$level = 1;
						$month = 1;
					}
					if( $orderinfo['utype'] == 2 ) {
						$level = 1;
						$month = 6;
					}
					if( $orderinfo['utype'] == 3 ) {
						$level = 1;
						$month = 12;
					}
					if( $orderinfo['utype'] == 4 ) {
						$level = 2;
						$month = 1;
					}
					if( $orderinfo['utype'] == 5 ) {
						$level = 2;
						$month = 6;
					}
					if( $orderinfo['utype'] == 6 ) {
						$level = 2;
						$month = 12;
					}
					$time = TIMESTAMP + $month*3600*24*31;
					$res = pdo_update('zofui_task_user',array('level'=>$level,'utime'=>$time),array('uid'=>$orderinfo['userid']));

					if( $res ){
						Util::deleteCache('u',$orderinfo['userid']);

						// 发放上级奖励
						$user = model_user::getSingleUser($orderinfo['userid']);

						if( $user['parent'] > 0 ) {
							$parent = pdo_get( 'zofui_task_user',array('uniacid'=>$_W['uniacid'],'id'=>$user['parent']) );
							$ulevel = model_user::levelRes($parent,$set);
							if( $ulevel == 1 ){
								$set['uaddone'] = $set['uaddonea'];
							}
							if( $ulevel == 2 ){
								$set['uaddone'] = $set['uaddoneb'];
							}
						}
						if( $parent['parent'] > 0 ) {
							$two = pdo_get( 'zofui_task_user',array('uniacid'=>$_W['uniacid'],'id'=>$parent['parent']) );
							$ulevel = model_user::levelRes($two,$set);
							if( $ulevel == 1 ){
								$set['uaddtwo'] = $set['uaddtwoa'];
							}
							if( $ulevel == 2 ){
								$set['uaddtwo'] = $set['uaddtwob'];
							}
						}
						if( $two['parent'] > 0 ) {
							$three = pdo_get( 'zofui_task_user',array('uniacid'=>$_W['uniacid'],'id'=>$two['parent']) );
							$ulevel = model_user::levelRes($three,$set);
							if( $ulevel == 1 ){
								$set['uaddthree'] = $set['uaddthreea'];
							}
							if( $ulevel == 2 ){
								$set['uaddthree'] = $set['uaddthreeb'];
							}
						}

						if( $user['parent'] > 0 && $set['uaddone'] > 0 && $set['isdown'] != 2 ){
							$upmoney = sprintf('%.2f', $orderinfo['fee']*$set['uaddone']/100);
							if( $upmoney >= 0.01 ){

								model_user::updateUserCredit($parent['uid'],$upmoney,2,1);
								model_money::insertMoneyLog($parent['openid'],$upmoney,1,33,$parent['uid']);

								Util::addOrMinusOrUpdateData('zofui_task_user',array('giveparent'=>$upmoney),$user['id']);
								
								// 发通知
								Message::downgive($parent['uid'],$parent['openid'],$upmoney,'下级升级会员',$user['nickname']);


								// 二级奖励
								$twoupmoney = sprintf('%.2f', $orderinfo['fee']*$set['uaddtwo']/100);
								if( $twoupmoney >= 0.01 && $set['uaddtwo'] > 0 && $set['downnum'] >= 1 && $parent['parent'] > 0 ){

									model_user::updateUserCredit($two['uid'],$twoupmoney,2,1);
									model_money::insertMoneyLog($two['openid'],$twoupmoney,1,34,$two['uid']);

									Util::addOrMinusOrUpdateData('zofui_task_user',array('givetwo'=>$twoupmoney),$user['id']);
								}

								// 三级奖励
								$threeupmoney = sprintf('%.2f', $orderinfo['fee']*$set['uaddthree']/100);
								if( $threeupmoney >= 0.01 && $set['uaddthree'] > 0 && $set['downnum'] >= 2 && $two['parent'] > 0 ){
									model_user::updateUserCredit($three['uid'],$threeupmoney,2,1);
									model_money::insertMoneyLog($three['openid'],$threeupmoney,1,35,$three['uid']);

									Util::addOrMinusOrUpdateData('zofui_task_user',array('givethree'=>$threeupmoney),$user['id']);
								}

							}
						}

					}
					
					
				}

			}
		}
		
		if($params['from'] == 'return') {

			$orderinfo = pdo_get('zofui_tasktb_paylog',array('orderid'=>$params['tid']));

			$url = $_W['siteroot'] . 'app/index.php?i='.$orderinfo['uniacid'].'&c=entry&do=user&m=zofui_taskself';
			if( !empty($_COOKIE['oldurl']) ) {
				$url = $_COOKIE['oldurl'];
				setcookie("oldurl",'', -500,'/');
			}
			if ($params['result'] == 'success') {
				message('支付成功！',$url,'success');
			} else {
				message('支付失败！', $url, 'error');
			}
		}		
		
	}



	static function createOrderId($uid){
		return time() .$uid . rand(10,99);
	}
	
	static function returnPay($orderid,$fee,$title){
		global $_W;
		$params['tid'] = $orderid;
		$params['user'] = $_W['openid'];
		$params['fee'] = $fee;
		$params['title'] = cutstr($title,40, false);
		$params['ordersn'] = $orderid;
		$params['module'] = "zofui_taskself";		
		return $params;
	}


	static function savePay($params = array(), $mine = array()) {
		global $_W;
		
		$pars = array();
		$pars[':uniacid'] = $_W['uniacid'];
		$pars[':module'] = $params['module'];
		$pars[':tid'] = $params['tid'];
		if($params['fee'] <= 0) {
			$pars['from'] = 'return';
			$pars['result'] = 'success';
			$pars['type'] = '';
			$pars['tid'] = $params['tid'];
			$site = WeUtility::createModuleSite($pars[':module']);
			$method = 'payResult';
			if (method_exists($site, $method)) {
				exit($site->$method($pars));
			}
		}

		$sql = 'SELECT * FROM ' . tablename('core_paylog') . ' WHERE `uniacid`=:uniacid AND `module`=:module AND `tid`=:tid';
		$log = pdo_fetch($sql, $pars);
		if (empty($log)) {
			$log = array(
				'uniacid' => $_W['uniacid'],
				'acid' => $_W['acid'],
				'openid' => $_W['member']['uid'],
				'module' => $params['module'],
				'tid' => $params['tid'],
				'fee' => $params['fee'],
				'card_fee' => $params['fee'],
				'status' => '0',
				'is_usecard' => '0',
			);
			pdo_insert('core_paylog', $log);
		}
		if($log['status'] == '1') {
			message('这个订单已经支付成功, 不需要重复支付.');
		}
		/*$setting = uni_setting($_W['uniacid'], array('payment', 'creditbehaviors'));
		if(!is_array($setting['payment'])) {
			message('没有有效的支付方式, 请联系网站管理员.');
		}*/
	}



}