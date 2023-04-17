<?php 
	global $_GPC,$_W;
	$userinfo = model_user::initUserInfo(); //用户信息
	if(empty($userinfo)) die('页面不存在');
	$_GPC = Util::trimWithArray($_GPC);	
	
	
	//交保证金
	if(checksubmit('epositsubmit')){
		$data['fee'] = intval($_GPC['adddeposit']);
		if((($userinfo['deposit'] + $data['fee']) < $this->module['config']['deposit']) || $data['fee'] <= 0) message('保证金最小额是'.$this->module['config']['deposit']); //当前保证金+充值的 < 要求的 提示错误。
		
		$orderid = createOrderId();
		$res = insertPaylogData($userinfo,$orderid,1,$data['fee']);
		
		if($res) {
			$params = requestPay($orderid,$data['fee'],'任务保证金');
			$this->pay($params);
		}
		die;
		
	}
	
	if(checksubmit('addmoney')){


		$data['fee'] = intval($_GPC['moneyvalue']);
		if( $data['fee'] <= 0) message('出错了！'); //当前保证金+充值的 < 要求的 提示错误。
		
		$orderid = createOrderId( $userinfo['id'] );
		$res = insertPaylogData($userinfo,$orderid,2,$data['fee']);
		
		if($res) {
			$params = requestPay($orderid,$data['fee'],$_W['cname'].'充值');
			$this->pay($params);
		}
		die;
	}
	
	
	message('请返回重新提交');
	

	//提交支付
	function requestPay($orderid,$fee,$title){
		global $_W;
		$params['tid'] = $orderid;
		$params['user'] = $_W['openid'];
		$params['fee'] = $fee;
		$params['title'] = $title;
		$params['ordersn'] = $orderid;
		$params['module'] = "zofui_tasktb";
		return $params;
	}
	
	//插入支付数据
	function insertPaylogData($userinfo,$orderid,$type,$fee){	
		global $_W;
			
		pdo_delete('zofui_tasktb_paylog',array('status'=>0,'type'=>$type,'uid'=>$userinfo['id']) );
		$data = array(
			'uniacid' => $_W['uniacid'],
			'openid' => $userinfo['openid'],
			'userid' => $userinfo['uid'],
			'orderid' => $orderid,
			'status' => 0,
			'uid' => $userinfo['id'],
			'type' => $type,
			'fee' => $fee,
			'time' => time()
		);
		$res = pdo_insert('zofui_tasktb_paylog',$data);
		if($res) return true; return false;
	}
	
	//生成订单编号
	function createOrderId( $uid ){
		global $_W;
		$orderid = date("YmdHis") . $uid . $_W['uniacid'] . rand(100,999);
		return $orderid;
	}	
	
?>