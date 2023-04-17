<?php 

class model_money
{	
	
	
	//插入提取数据 type 1余额提现 2保证金提现
	static function insertDrawLog($openid,$money,$type,$uid,$server=0){
		global $_W;
		if( $_W['dev'] == 'wx' ) {
			$paytype = intval( $_W['set']['paytype'] );
			$paytype = $paytype == 2 ? 1 : ( $paytype == 3 ? 2 : 0 );
		}
		if( $_W['dev'] == 'wap' ) {
			$paytype = intval( $_W['set']['wappaytype'] );
			$paytype = $paytype == 0 ? 1 : 2;
		}

		$data =array(
			'uniacid' => $_W['uniacid'],
			'openid'=> $openid,
			'money' => $money,
			'status' => 0,
			'createtime' => time(),
			'type' => $type,
			'paytype' => $paytype,
			'userid' => $uid,
			'server' => $server,
		);
		if( $data['paytype'] == 1 ){
			$user = model_user::getSingleUser( $uid );
			$data['alipayname'] = $user['alipayname'];
			$data['alipay'] = $user['alipay'];
		}

		$res = pdo_insert('zofui_tasktb_draw',$data);
		return $res;
	}

	// 资金记录
	// mtype 1余额 2保证金
	// type 1提现 2充值增加 3发私包任务扣除 4退回私包任务赏金 5私包任务收入 6下级获得收益提成 7发布普通任务扣除
	//		8普通任务收益 9扣除服务费 10普通任务结束退回 11连续任务额外奖励 12追加任务扣除 13管理员操作 14退回提现 	15扣除提现手续费 16查看联系方式扣除 17联系方式被查看奖励 18发布广告扣除 19发布试用任务扣除 20试用任务收益 21试用任务结束退回
	// 22审核担保任务扣除 23担保任务保证金 24担保任务收益 25退回担保任务资金 26退回担保任务保证金 27发布担保任务扣除 28下下级获得收益提成 29下下下级获得收益提成 30下级发布任务提成 31下下级发布任务提成  32下下下级发布任务提成 33下级升级会员提成 34下下级升级会员提成 35下下下级升级会员提成  36查看答案扣除 37查看答案分成 38领取宝箱收入 39利润回馈的金额 40兑换活跃度扣除 41恢复任务扣除 42认证扣除 43新用户赠送

	// type 1提取 2充值增加 3管理员操作 4提现退回 5提取保证金手续费
	static function insertMoneyLog($openid,$money,$mtype,$type,$uid,$taskid=''  ){
		global $_W;
		if( $money == 0 ) return false;
		Util::deleteCache( 'u',$uid ); // 先删缓存
		if( $mtype == 1 ){
			$credit = model_user::getUserCredit( $uid );
			$aftermoney = $credit['credit2'];
		}else{
			$user = model_user::getSingleUser($uid);
			$aftermoney = $user['deposit'];
		}
		
		$logarray = array(
			'uniacid'=>$_W['uniacid'],
			'openid'=>$openid,
			'taskid'=>$taskid,
			'time'=>time(),
			'money'=>$money,
			'aftermoney' => $aftermoney,
			'type'=>$type,
			'mtype'=>$mtype,
			'userid' => $uid,
		);
		$res = pdo_insert('zofui_tasktb_moneylog',$logarray);	
		return $res;
		
	}
	
	
}
