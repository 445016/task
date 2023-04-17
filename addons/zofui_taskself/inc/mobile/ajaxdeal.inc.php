<?php 
	defined('IN_IA') or exit('Access Denied');
	set_time_limit(0);
	$_W['set'] = $this->module['config'];

	global $_W,$_GPC;
	$userinfo = model_user::initUserInfo();

	$ing = Util::getCache('ing',$_W['member']['uid']);

	if( is_array( $ing ) && $ing['status'] == 1 && $ing['time'] >= time() - 60 ) {
		$res = array('status'=>201,'res'=>'操作过于频繁，稍后再试');
		echo json_encode($res);
		exit;
	}
	Util::setCache('ing',$_W['member']['uid'],array('status'=>1,'time'=>time()));
	
	// 必须放前面 获取地址
	if($_GPC['op'] == 'location'){

		//if( empty( $_SESSION['isinweb'] ) ) Util::echoResult(201,'失败');

		$sessionstr = $_W['member']['uid'].'a'.$_W['uniacid'];
				
		if( empty($_SESSION[$sessionstr]) ){
			
			$ak = empty( $this->module['config']['ak'] ) ? 'F51571495f717ff1194de02366bb8da9' : $this->module['config']['ak'];
			$tourl = 'http://api.map.baidu.com/geoconv/v1/?coords='.$_GPC['longitude'].','.$_GPC['latitude'].'&from=1&to=5&ak='.$ak;
			$tores = Util::httpGet($tourl);
			$tores = json_decode($tores,true);

			if( $tores['status'] == '0' ){
				$_GPC['latitude'] = sprintf('%.5f',$tores['result'][0]['y']);
				$_GPC['longitude'] = sprintf('%.4f',$tores['result'][0]['x']);
			}			

			$_SESSION[$sessionstr] = array('lat'=>$_GPC['latitude'],'lng'=>$_GPC['longitude']);
			
			$url = 'http://api.map.baidu.com/geocoder/v2/?ak='.$ak.'&location='.$_GPC['latitude'].','.$_GPC['longitude'].'&output=json&pois=1';
			$opt=array('http'=>array('header'=>"Referer: ".$_W['siteroot'])); 
			$context=stream_context_create($opt); 
			$res = file_get_contents($url,false, $context);
			
			if( !$res ){
				$res = Util::httpGet($url);
			}
			
			$arr = json_decode($res,true);
			
			if($arr['status'] != '0'){
				Util::echoResult(201,'获取地理位置失败，可能是后台没有设置百度地理位置接口ak');
			}else{
				$_SESSION[$sessionstr]['province'] = str_replace(array('市','省','自治区'), array('','',''), $arr['result']['addressComponent']['province']);

				$_SESSION[$sessionstr]['city'] = str_replace(array('市','自治州','地区','自治区','自治县'), array('','','','',''), $arr['result']['addressComponent']['city']);

				$_SESSION[$sessionstr]['country'] = str_replace(array('市','区','县','自治区','自治县'), array('','','','',''), $arr['result']['addressComponent']['district']);
			}
		}
		
		Util::echoResult(200,'好');
	}	


	// 升级会员
	elseif($_GPC['op'] == 'addulevel'){

		$utype = intval( $_GPC['utype'] );
		if( $utype <= 0 || $utype >= 7 ) Util::echoResult(201,'类型不对');

		
		$oldorder = pdo_get('zofui_tasktb_paylog', array('status'=>0,'type'=>3,'uid'=>$userinfo['id']) );
		if( !empty( $oldorder ) ){
			pdo_delete('zofui_tasktb_paylog',array('id'=>$oldorder['id']));
			pdo_delete('core_paylog',array('tid'=>$oldorder['orderid']));
		}

		$fee = 0;
		if( $utype == 1 ) $fee = $_W['set']['upricea']*1;
		if( $utype == 2 ) $fee = $_W['set']['upriceb']*1;
		if( $utype == 3 ) $fee = $_W['set']['upricec']*1;
		if( $utype == 4 ) $fee = $_W['set']['upriceba']*1;
		if( $utype == 5 ) $fee = $_W['set']['upricebb']*1;
		if( $utype == 6 ) $fee = $_W['set']['upricebc']*1;

		if( $fee <= 0 ) Util::echoResult(201,'当前不可升级');

		$orderid = pay::createOrderId( $userinfo['id'] );
		$data = array(
			'uniacid' => $_W['uniacid'],
			'openid' => $userinfo['openid'],
			'userid' => $userinfo['uid'],
			'orderid' => $orderid,
			'status' => 0,
			'uid' => $userinfo['id'],
			'type' => 3,
			'fee' => $fee,
			'time' => time(),
			'utype' => $utype,
		);
		$res = pdo_insert('zofui_tasktb_paylog',$data);
		$params = pay::returnPay($orderid,$fee,'升级会员');
		pay::savePay( $params );

		$orderinfo['params'] = base64_encode(json_encode( $params ));
		$orderinfo['oid'] = $orderid;
		Util::echoResult(200,'好',$orderinfo);
	}	

	// 充值余额和保证金
	elseif($_GPC['op'] == 'addmoney'){

		$money = sprintf( '%2.f',$_GPC['money'] );
		if( $money < 0.01 ) Util::echoResult(201,'请输入正确的金额数值');

		$type = 2; // 充值
		$title = '充值'.$_W['cname'];
		if( $_GPC['type'] == 'deposit' ){
			$type = 1;
			$title = '充值保证金';
		} 

		// 充值积分的时候
		if( $type == 2 && $_W['set']['moneytype'] == 1 ){
			$per =  empty( $_W['set']['creditper'] ) ? 1 : $_W['set']['creditper'];
			$money = $money/$per;
			if( $money < 0.01 ) Util::echoResult(201,'充值的数值太小了');
		}

		$oldorder = pdo_get('zofui_tasktb_paylog', array('status'=>0,'type'=>$type,'uid'=>$userinfo['id']) );
		if( !empty( $oldorder ) ){
			pdo_delete('zofui_tasktb_paylog',array('id'=>$oldorder['id']));
			pdo_delete('core_paylog',array('tid'=>$oldorder['orderid']));
		}

		$orderid = pay::createOrderId( $userinfo['id'] );
		$data = array(
			'uniacid' => $_W['uniacid'],
			'openid' => $userinfo['openid'],
			'userid' => $userinfo['uid'],
			'orderid' => $orderid,
			'status' => 0,
			'uid' => $userinfo['id'],
			'type' => $type,
			'fee' => $money,
			'time' => time()
		);
		$res = pdo_insert('zofui_tasktb_paylog',$data);
		$params = pay::returnPay($orderid,$money,$title);
		pay::savePay( $params );

		$orderinfo['params'] = base64_encode(json_encode( $params ));
		$orderinfo['oid'] = $orderid;
		Util::echoResult(200,'好',$orderinfo);
	}

	// 提现
	elseif( $_GPC['op'] == 'outmoney' ){

		$money = sprintf( '%2.f',$_GPC['money'] );
		$type = $_GPC['type'];
		if( $money < 0.01 ) Util::echoResult(201,'请输入正确的金额数值');

		if( $_W['set']['paytype'] == 3 && empty( $userinfo['payqr'] ) ) Util::echoResult(201,'你还没有设置收款码');

		if( $_W['set']['maxdayd'] > 0 ){
			$today = strtotime(date('Y-m-d',TIMESTAMP));
			$times = pdo_count('zofui_tasktb_draw',array('uniacid'=>$_W['uniacid'],'userid'=>$userinfo['uid'],'createtime >'=>$today));
			if( $times >= $_W['set']['maxdayd'] ){
				Util::echoResult(201,'每天最多提现'.$_W['set']['maxdayd'].'次，你已不能再提现');
			}
		}

		if( $type == 'money' ){ // 提现余额
			$drwedinfo = pdo_get('zofui_tasktb_draw',array('userid'=>$userinfo['uid'],'type'=>1,'status'=>0));
			if(!empty($drwedinfo)){
				Util::echoResult(201,'您还有一笔提现没处理完，等处理完后再提现');
			}
			
			// 手机验证
			if( $this->module['config']['drawvm'] == 1 ){
				load()->model('mc');
				$user = mc_fetch($userinfo['uid'], array('uniacid'=>$_W['uniacid']));

				if( !empty( $user['mobile'] ) ) {
					if( empty( $_GPC['code'] ) ) Util::echoResult(201,'请填写验证码');
					if( $_GPC['code'] != $_SESSION['vertify'.$user['mobile']] ) Util::echoResult(201,'手机验证码不正确');
				}
			}

			$server = 0;
			if( $_W['set']['drawserver'] > 0 ){
				$server = round($_W['set']['drawserver']*$money/100,2);
			}
			$total = $money + $server;

			$credit = model_user::getUserCredit( $userinfo['uid'] );
			if($credit['credit2'] < $total){
				Util::echoResult(201,'您的'.$_W['cname'].'不足');
			}

			$mindraw = $_W['set']['mindraw'] > 1 ? $_W['set']['mindraw'] : 1;

			$per = 1;
			if( $_W['set']['moneytype'] == 1 && !empty( $_W['set']['creditper'] ) ){
				$per = $_W['set']['creditper'];
				if( $money/$per < 1 ) Util::echoResult(201,'提现数值不能小于'.$per.$_W['cper']);

				if( $money/$per > 2000 ){
					Util::echoResult(201,'提现数值不能大于' . 2000*$per);
				}
			}

			if( $money < $mindraw ){
				Util::echoResult(201,'提现数值不能小于'.$mindraw.$_W['cper']);
			} 
			
			if( empty($_W['set']['moneytype']) && $money > 2000 ) {
				Util::echoResult(201,'提现金额不能大于2000');
			}

			if( $money > 200 && $_W['set']['paytype'] == 0 ) Util::echoResult(201,'提现金额不能大于200');

			//扣钱
			$res = model_user::updateUserCredit($userinfo['uid'],-$money,2,1); // 提现
			if( $res ){
				// 提现记录
				model_money::insertDrawLog($userinfo['openid'],$money,1,$userinfo['uid'],$server);
				// 资金记录
				model_money::insertMoneyLog($userinfo['openid'],-$money,1,1,$userinfo['uid']);

				// 服务费
				model_user::updateUserCredit($userinfo['uid'],-$server,2,1);
				model_money::insertMoneyLog($userinfo['openid'],-$server,1,15,$userinfo['uid']);
				
				Util::echoResult(200,'提现成功，请等待客服处理');
			}
			
		}elseif( $type == 'deposit' ){ // 保证金

			$userinfo = pdo_get('zofui_task_user',array('uid'=>$userinfo['uid'],'uniacid'=>$_W['uniacid'])); //用户信息
			

			$server = 0;
			if( $_W['set']['drawpositserver'] > 0 ){
				$server = round($_W['set']['drawpositserver']*$money/100,2);
			}
			$total = $money + $server;

			if( $userinfo['deposit'] < $total ) Util::echoResult(201,'您的保证金不足'); 

			$can = $userinfo['deposit'] - $_W['set']['lastdeposit'];
			if( $can <= 0 ) Util::echoResult(201,'账户必须留存'.$_W['set']['lastdeposit'].'保证金，您不能提取'); 
			if( $can < $money ) Util::echoResult(201,'您最多能提取'.$can);

			//查询是否已有提取的
			$drwinfo = pdo_get('zofui_tasktb_draw',array('status'=>0,'userid'=>$userinfo['uid'],'type'=>2));
			if(!empty($drwinfo)) Util::echoResult(201,'您还有一笔提取没处理完，等处理完后再提取');
			
			$comtask = pdo_get('zofui_tasktb_task',array('uniacid'=>$_W['uniacid'],'userid'=>$userinfo['uid'],'iscount'=>0));
			if( !empty( $comtask ) ) Util::echoResult(201,'您还有没有结算的任务，等任务都结算完了再提保证金');

			if( $money < 1 ) Util::echoResult(201,'提取金额不能少于1'); 
			if( $money > 2000 ) Util::echoResult(201,'提取金额不能大于2000'); 
			if( $money > 200 && $_W['set']['paytype'] == 0 ) Util::echoResult(201,'提取金额不能大于200'); 

			// 减保证金
			$res = Util::addOrMinusOrUpdateData('zofui_task_user',array('deposit'=>-$money),$userinfo['id']);
			if( $res ){

				Util::deleteCache('u',$userinfo['uid']);
				// 提取记录
				model_money::insertDrawLog($userinfo['openid'],$money,2,$userinfo['uid'],$server);
				// 资金记录
				model_money::insertMoneyLog($userinfo['openid'],-$money,2,1,$userinfo['uid']);

				// 扣手续费
				Util::addOrMinusOrUpdateData('zofui_task_user',array('deposit'=>-$server),$userinfo['id']);
				model_money::insertMoneyLog($userinfo['openid'],-$server,2,5,$userinfo['uid']);

				Util::echoResult(200,'提取成功，请等待客服处理');

			}

		}


		Util::echoResult(201,'处理失败');	

	//上传图片
	}elseif($_GPC['op'] == 'uploadimages'){
 		load()->func('communication');
 		load()->model('account');
		
		//$access_token = WeAccount::token();

		$acc = WeAccount::create($_W['acid']);
		$token = $acc->getAccessToken();

		//$url = 'http://file.api.weixin.qq.com/cgi-bin/media/get?access_token='.$access_token.'&media_id='.$_GPC['serverId'];
		$url = 'https://api.weixin.qq.com/cgi-bin/media/get?access_token='.$token.'&media_id='.$_GPC['serverId'];
		
		$content = file_get_contents($url);
		$resp['content'] = $content;
		$type = 1;

		$content = json_decode($content,true);
		if( !empty( $content['errcode'] ) ) {
			Util::echoResult(201,$content['errmsg']);
		}
		
		
		if( !$resp['content'] ) {
			$resp = ihttp_get($url);
			$type = 2;
		}

		$res = Util::uploadImageInWeixin($resp,$type);
		if( $res['status'] == 201 ) {
			Util::echoResult(201,'上传图片失败，请重新上传,失败原因:'.$res['message'],$res);
		}
		Util::echoResult(200,'好',$res);

	

	//发送验证码	
	}elseif($_GPC['op'] == 'tovertify'){
		if(!empty($_SESSION['vertify'.$_GPC['mobile']]) && $_SESSION['mobilevertifytime'] > time()){
			Util::echoResult(200,'验证码已发送');
		}
		
		$code = rand(1000,9999);

/*$_SESSION['vertify'.$_GPC['mobile']] = $code;
$_SESSION['mobilevertifytime'] = time()+300;
Util::echoResult(200,'验证码已发送'.$code);*/
		
		if( $_GPC['key'] != $_COOKIE['loginzf'] ) {
			setcookie('loginzf',md5($_W['account']['key'].random(111,999)),TIMESTAMP+3600);
			Util::echoResult(201,'数据异常');
		}

		if( $_GPC['type'] == 'forget' ) {
			$isseta = pdo_get('mc_members',array('uniacid'=>$_W['uniacid'],'mobile'=>$_GPC['mobile']));
			if( empty($isseta) ) Util::echoResult(201,'手机账户不存在');
		}

		if( $_GPC['type'] == 'regist' ) {
			$isseta = pdo_get('mc_members',array('uniacid'=>$_W['uniacid'],'mobile'=>$_GPC['mobile']));
			if( !empty($isseta) ) Util::echoResult(201,'手机已经注册，请换一个');
		}
		

		if( $this->module['config']['messapi'] == 0 ){

			$c = new TopClient;
			$c->appkey = $this->module['config']['sendkey'];
			$c->secretKey = $this->module['config']['sendsecret'];
			$req = new AlibabaAliqinFcSmsNumSendRequest;
			$req->setExtend("123456");
			$req->setSmsType("normal");
			$req->setSmsFreeSignName($this->module['config']['sendsignature']);
			$req->setSmsParam('{"code":"'.$code.'","product":"'.$this->module['config']['sendproduct'].'","customer":"'.$userinfo['nickname'].'"}');
			$req->setRecNum($_GPC['mobile']);
			$req->setSmsTemplateCode($this->module['config']['sendtemplate']);
			$resp = $c->execute($req);

			$res = json_decode(json_encode($resp),true);		
			if($res['result']['success']){
				$_SESSION['vertify'.$_GPC['mobile']] = $code;
				$_SESSION['mobilevertifytime'] = time()+300;
				Util::echoResult(200,'验证码已发送');
			}else{
				Util::echoResult(201,$res['msg'].$res['sub_msg']);
			}

		}elseif( $this->module['config']['messapi'] == 1 ){

			$key = $this->module['config']['sendkey'];
			$secret = $this->module['config']['sendsecret'];
			$signName = $this->module['config']['sendsignature'];
			$templateCode = $this->module['config']['sendtemplate'];
			$phoneNumbers = $_GPC['mobile'];
			
			$smscode = empty($_W['set']['smscode']) ? 'number' : $_W['set']['smscode'];

			$templateParam = '{"'.$smscode.'":"'.$code.'"}';
			
			$sms = new model_sms();
			
		    $res = $sms->sendSms($key,$secret,$signName, $templateCode, $phoneNumbers, $templateParam);
		   	$res = json_decode($res,true);
			 		   	
		   	if( $res['Code'] == 'OK' ) {
				$_SESSION['vertify'.$_GPC['mobile']] = $code;
				$_SESSION['mobilevertifytime'] = time()+60;
				Util::echoResult(200,'验证码已发送');
		   	}else{
				Util::echoResult(201,$res['Message'].$res['Code']);
			}

		}



	}elseif( $_GPC['op'] == 'saveset' ){ // 保存设置

		$data['mobile'] = $_GPC['tel'];
		$data['qq'] = $_GPC['qq'];
		$data['qrcode'] = $_GPC['images'][0];
		//$data['guysort'] = $_GPC['guysort'];
		$data['guydesc'] = $_GPC['guydesc'];

		$data['alipayname'] = $_GPC['aliname'];	
		$data['alipay'] = $_GPC['alipay'];

		$data['ispub'] = $_GPC['ispub'];
		$data['isacc'] = $_GPC['isacc'];
		$data['conweixin'] = $_GPC['conweixin'];
		$data['conmobile'] = $_GPC['conmobile'];
		$data['limitnum'] = intval( $_GPC['limitnum'] );
		$data['limitday'] = intval( $_GPC['limitday'] ) > 0 ? intval( $_GPC['limitday'] ) : 1;

		$data['uselimitnum'] = intval( $_GPC['uselimitnum'] );
		$data['uselimitday'] = intval( $_GPC['uselimitday'] );

		if( $_W['dev'] == 'wap' ) {

			if( !empty( $_GPC['nickname'] ) ) $data['nickname'] = $_GPC['nickname'];
			if( !empty( $_GPC['headimgurl'][0] ) ) $data['headimgurl'] = $_GPC['headimgurl'][0];
			if( !empty( $_GPC['payqr'][0] ) ) $data['payqr'] = $_GPC['payqr'][0];
		}

		if( $_W['set']['iseditali'] == 1 ) { // 不可修改
			if( !empty( $userinfo['alipayname'] ) ) unset( $data['alipayname'] );
			if( !empty( $userinfo['alipay'] ) ) unset( $data['alipay'] );
			if( !empty( $userinfo['payqr'] ) ) unset( $data['payqr'] );
		}

		if( empty( $_GPC['aliname'] ) ) unset( $data['alipayname'] );
		if( empty( $_GPC['alipay'] ) ) unset( $data['alipay'] );
		if( empty( $_GPC['payqr'] ) ) unset( $data['payqr'] );

		/*if( $_W['set']['paytype'] == 3 ){
			if( empty( $_GPC['payqr'][0] ) ) Util::echoResult(201,'请上传设置微信收款二维码');
			$data['payqr'] = $_GPC['payqr'][0];
		}*/

		if( empty($data['qq']) ){
			Util::echoResult(201,'请填写qq，紧急情况可及时联系到你');
		}

		if( $_W['set']['maxguydc'] > 0 && mb_strlen($data['guydesc'],"utf-8") > $_W['set']['maxguydc'] ){
			Util::echoResult(201,'个人简述请保持在'.$_W['set']['maxguydc'].'个字符以内');
		}

		if( !empty( $data['alipay'] ) && $_W['set']['ismali'] == 1 ){
			$isset = pdo_get('zofui_task_user',array('uniacid'=>$_W['uniacid'],'alipay'=>$data['alipay'],'id !='=>$userinfo['id']));
			if( !empty( $isset ) ) Util::echoResult(201,'支付宝收款账户已被别人使用了，请换一个');
		}

		if( $_W['set']['maxguys'] > 0 && count($_GPC['guysorta']) > $_W['set']['maxguys'] ){
			Util::echoResult(201,'业务类型最多选择'.$_W['set']['maxguys'].'项');
		}

		if( !in_array($userinfo['sex'],array('3','4')) ) $data['sex'] = intval( $_GPC['sex'] );
		//if($userinfo['deposit'] < $_W['set']['findneed']) 
		//Util::echoResult(202,'您的保证金不足，账户需要有'.$_W['set']['findneed'].'元保证金才能设置并上找人页面，点击确定去充值保证金');
		
		$isauth = model_user::isAuth($userinfo,$_W['set']);

		if( $_GPC['isauth'] == 1 ){

			// 验证表单
			if( $isauth == 1 || ($isauth == 2 && $_W['set']['iseditauth'] == 0) ) { // 不可改变的情况

			}else{
				$form = pdo_getall('zofui_tasktb_authform',array('uniacid'=>$_W['uniacid']));
				if( !empty( $form ) ) {
					$varr = array();

					foreach ($form as $k => $v) {
						$varrin = array();
						$isin = 0;
						foreach ($_GPC['formidarr'] as $kk => $vv) {
							
							if( $vv == $v['id'] ) {			 
								if( empty( $_GPC['formarr'][$kk] ) ) Util::echoResult(201,'请设置'.$v['name']);

								if( $v['formtype'] == 'mobile' ) {
									if( !preg_match('#^13[\d]{9}$|14^[0-9]\d{8}|^15[0-9]\d{8}$|^18[0-9]\d{8}$#', $_GPC['formarr'][$kk]) ){
										Util::echoResult(201,'请设置正确的'.$v['name']);
									}
								}

								$isin = 1;
								$varrin['id'] = $v['id'];
								$varrin['name'] = $v['name'];
								$varrin['value'] = $_GPC['formarr'][$kk];
								$varrin['type'] = $v['formtype'];
								$varr[] = $varrin;
							}
							
						}
						if( $isin == 0 ) Util::echoResult(201,'数据未找到，请重新提交');
					}

				}
				$data['verifyform'] = iserializer( $varr ); 
			} 
			// 认证
			if( $_W['set']['isauth'] != 0 ) {
				if( $isauth == 0 ) {
					$data['verifystatus'] = 1;

					if( $_W['set']['authneed'] > 0 && empty($userinfo['iscostauth']) ){
						$credit = model_user::getUserCredit( $userinfo['uid'] );
						if( $credit['credit2'] < $_W['set']['authneed'] ){
							Util::echoResult(210,'你的'.$_W['cname'].'不足，认证需要扣除'.$_W['set']['authneed'].'，请先充值再认证',array('url'=>Util::createModuleUrl('money',array('op'=>'in'))));
						}
					}

				}
			}
		}
		
		//验证手机号码
		if($_W['set']['isverify'] == 1 && ( empty( $_GPC['code'] ) || $_GPC['code'] != $_SESSION[ 'vertify'.$_GPC['tel'] ] )) 
			Util::echoResult(201,'验证码不正确');
		
		// 扣除认证费
		if( $data['verifystatus'] == 1 && $_W['set']['authneed'] > 0 && empty($userinfo['iscostauth']) ){
			$resa = model_user::updateUserCredit($userinfo['uid'],-$_W['set']['authneed'],2,1);
			if( $resa ){
				// 资金记录
				model_money::insertMoneyLog($userinfo['openid'],-$_W['set']['authneed'],1,42,$userinfo['uid']);
				$data['iscostauth'] = 1;
			}else{
				Util::echoResult(201,'扣除余额失败');
			}
		}

		$res = pdo_update('zofui_task_user',$data,array('uniacid'=>$_W['uniacid'],'id'=>$userinfo['id']));
		Util::deleteCache('u',$userinfo['uid']);

		$nostr = '';
		$status = 200;
		if( $userinfo['deposit'] < $_W['set']['findneed'] ){
			$nostr = '，但您不能上找人页面，账户需要有'.$_W['set']['findneed'].'保证金才能上找人页面';
			$status = 210;
		}

		if( !empty($_GPC['guysorta']) ){
			pdo_delete('zofui_tasktb_usersort',array('uid'=>$userinfo['uid']));
			foreach ($_GPC['guysorta'] as $v) {
				$indata = array(
					'uid' => $userinfo['uid'],
					'uniacid' => $_W['uniacid'],
					'sortid' => $v,
				);
				pdo_insert('zofui_tasktb_usersort',$indata);
			}
		}

		Util::echoResult( $status,'设置成功'.$nostr ,array('url'=>Util::createModuleUrl('deposit',array('op'=>'in'))));	
		

	//处理发布私包任务和索要私包任务	
	}elseif($_GPC['op'] == 'pubprivatetask'){
		
		$data['tasktitle'] = $_GPC['tasktitle'];
		$data['taskmoney'] = $_GPC['taskmoney'];
		$data['limittime'] = intval($_GPC['tasktime']);
		$data['images'] = iserializer($_GPC['images']);
		$data['type'] = $_GPC['type'] == 'puber' ? 1 : 2; // 1索要 2发给
		$guyid = intval($_GPC['guyid']);
		
		//验证
		if( $data['taskmoney'] < $_W['set']['leastprimoney'] ) 
			Util::echoResult(201,'任务赏金不能小于'.$_W['set']['leastprimoney']);
		
		if( $data['taskmoney'] <= 0.01 ) Util::echoResult(201,'任务金额不能小于0.01');
		if( $data['limittime'] <= 0 ) Util::echoResult(201,'任务时限不能小于等于0');

		$guyinfo = pdo_get('zofui_task_user',array('id'=>$guyid,'uniacid'=>$_W['uniacid']));
		$userinfo = pdo_get('zofui_task_user',array('uid'=>$userinfo['uid'],'uniacid'=>$_W['uniacid']));
		
		
		if( $data['type'] == 1 && $guyinfo['ispub'] != 1 ) Util::echoResult(201,'对方未开启发任务功能');
		if( $data['type'] == 2 && $guyinfo['isacc'] != 1 ) Util::echoResult(201,'对方未开启接任务功能');
		
		

		if( $data['type'] == 2 ){ // 发给的需要计算钱
			$data['bossserver'] = $data['taskmoney']*$_W['set']['priserver']/100;
			if( $data['bossserver'] < $_W['set']['prileast'] ) $data['bossserver'] = $_W['set']['prileast'];

			$total = $data['bossserver'] + $data['taskmoney'];
			if( $total <= 0 ) Util::echoResult(200,'请刷新页面重试');

			$credit = model_user::getUserCredit( $userinfo['uid'] );
			if( $credit['credit2'] < $total ) 
				Util::echoResult(210,'您的'.$_W['cname'].'不足，点击确定去充值');
		}

		// 是不是给自己发的
		if( $guyinfo['id'] == $userinfo['id'] ) Util::echoResult(201,'不能和自己发起任务');

		//判断2人之间是否已有任务
		$isset = pdo_get('zofui_tasktb_privatetask',array('isend'=>0,'uniacid'=>$_W['uniacid'],'pubuid'=>$userinfo['uid'],'acceptuid'=>$guyinfo['uid']));		
		if( !empty( $isset ) ) Util::echoResult(201,'您和对方有一个任务还没完成，先完成了再发起新任务');
		
		// 是否有超过3个以上任务
		$where = array('uniacid'=>$_W['uniacid'],'pubuid'=>$userinfo['uid'],'isend'=>0);
		$num = Util::countDataNumber('zofui_tasktb_privatetask',$where);
		if( $num >= 3 ) Util::echoResult(201,'您有很多私包任务还没完成，完成了再发起新任务');


		$data['uniacid'] = $_W['uniacid'];
		$data['puber'] = $userinfo['openid'];
		$data['pubuid'] = $userinfo['uid'];
		$data['accepter'] = $guyinfo['openid'];
		$data['acceptuid'] = $guyinfo['uid'];
		$data['createtime'] = time();

		$data['overtime0'] = time() + $this->module['config']['privatedealtime']*3600;
		$data['status'] = 0;
		if($data['type'] == 1){
			$data['workeropenid'] = $userinfo['openid'];
			$data['bossopenid'] = $guyinfo['openid'];
			$data['workeruid'] = $userinfo['uid'];
			$data['bossuid'] = $guyinfo['uid'];
		}
		if($data['type'] == 2){
			$data['workeropenid'] = $guyinfo['openid'];
			$data['bossopenid'] = $userinfo['openid'];
			$data['workeruid'] = $guyinfo['uid'];
			$data['bossuid'] = $userinfo['uid'];		
		}
		
		$res = pdo_insert('zofui_tasktb_privatetask',$data);
		$insertid = pdo_insertid();
		if($res){
			if($data['type'] == 2){  //发给任务扣钱
				
				$res = model_user::updateUserCredit($userinfo['uid'],-$total,2,1);
				if( $res ){
					// 资金记录
					model_money::insertMoneyLog($userinfo['openid'],-$total,1,3,$userinfo['uid']);
				}
			}
			
			// 发通知
			Message::amessage($guyinfo['uid'],$guyinfo['openid'],$data['tasktitle'],$data['taskmoney'],$data['limittime'],$insertid,$data['type']);

			// 增加发布数据
			pdo_query("UPDATE ".tablename('zofui_tasktb_scan')." SET `pubed` = `pubed` + 1,`privatepubed` = `privatepubed` + 1 WHERE `uniacid` = '{$_W['uniacid']}' ");

			Util::echoResult(200,'好',array('id'=>$insertid));
		}
		Util::echoResult(201,'发起失败');


	//privatetask页面支付任务赏金
	}elseif($_GPC['op'] == 'paytaskmoney'){
		$taskid = intval($_GPC['taskid']);		
		$taskinfo = pdo_get('zofui_tasktb_privatetask',array('id'=>$taskid,'uniacid'=>$_W['uniacid']));		
		
		if($taskinfo['type'] != 1 || $taskinfo['bossuid'] != $userinfo['uid'] || $taskinfo['status'] != 0)
		Util::echoResult(201,'您不能操作此任务');
		
		$userinfo = pdo_get('zofui_task_user',array('uniacid'=>$_W['uniacid'],'id'=>$userinfo['id']));
		if($userinfo['credit2'] < $taskinfo['money']) die('1'); //没钱了
		
		
		$server = $taskinfo['taskmoney']*$_W['set']['priserver']/100;
		if( $server < $_W['set']['prileast'] ) $server = $_W['set']['prileast'];

		$total = $server + $taskinfo['taskmoney'];
		if( $total <= 0 ) Util::echoResult(200,'请刷新页面重试');

		$credit = model_user::getUserCredit( $userinfo['uid'] );
		if( $credit['credit2'] < $total ) 
			Util::echoResult(210,'您的'.$_W['cname'].'不足，点击确定去充值');
		
		$res = model_user::updateUserCredit($userinfo['uid'],-$total,2,1);
		if( $res ){
			// 资金记录
			model_money::insertMoneyLog($userinfo['openid'],-$total,1,3,$userinfo['uid']);

			$overtime2 = $taskinfo['limittime']*3600 + time();
			//改变任务状态
			pdo_update('zofui_tasktb_privatetask',array('status'=>2,'overtime2'=>$overtime2,'accepttime'=>time()),array('id'=>$taskinfo['id']));
			
			//发通知
			Message::cmessage($taskinfo['workeruid'],$taskinfo['workeropenid'],$taskinfo['tasktitle'],'paytaskmoney',$taskinfo['id']);
			Util::echoResult(200,'好');
		}
		Util::echoResult(201,'支付失败');
		
	//拒绝支付赏金任务
	}elseif($_GPC['op'] == 'refusegeivetask'){
		$taskid = intval($_GPC['taskid']);
		$taskinfo = pdo_get('zofui_tasktb_privatetask',array('id'=>$taskid,'uniacid'=>$_W['uniacid']));

		if($taskinfo['type'] != 1 || $taskinfo['bossuid'] != $userinfo['uid'] || $taskinfo['status'] != 0)
			Util::echoResult(201,'您不能操作此任务');

		//改变任务状态
		$res = pdo_update('zofui_tasktb_privatetask',array('status'=>1,'isend'=>1,'accepttime'=>time()),array('id'=>$taskinfo['id']));
		if( $res ){
			//发通知
			Message::cmessage($taskinfo['workeruid'],$taskinfo['workeropenid'],$taskinfo['tasktitle'],'refusegeivetask',$taskinfo['id']);

			Util::echoResult(200,'好');
		}
		Util::echoResult(201,'拒绝失败');

	//雇员主动取消任务
	}elseif($_GPC['op'] == 'canceltask'){
		$taskid = intval($_GPC['taskid']);
		$taskinfo = pdo_get('zofui_tasktb_privatetask',array('id'=>$taskid,'uniacid'=>$_W['uniacid']));

		if($taskinfo['workeruid'] != $userinfo['uid'] || $taskinfo['status'] != 2) 
			Util::echoResult(201,'您不能操作此任务');

		$res = model_privatetask::cancelTaskFuncInAjaxdealAndCrontab($taskinfo,4,$this); //处理任务 //此方法里已发通知
		
		if($res) Util::echoResult(200,'好');
		Util::echoResult(201,'取消失败');

	//雇员提交完成任务	
	}elseif($_GPC['op'] == 'completetask'){
		$taskid = intval($_GPC['taskid']);
		$content['title'] = $_GPC['completecontent'];
		$content['images'] = $_GPC['images'];
		$content = iserializer($content);
			
		$taskinfo = pdo_get('zofui_tasktb_privatetask',array('id'=>$taskid,'uniacid'=>$_W['uniacid']));
		if($taskinfo['workeruid'] != $userinfo['uid'] || $taskinfo['status'] != 2) 
			Util::echoResult(201,'您不能操作此任务');

		//改变任务状态
		$overtime3 = $this->module['config']['privatedealtime']*3600 + time();
		$res = pdo_update('zofui_tasktb_privatetask',array('status'=>3,'workerdealtime'=>time(),'overtime3'=>$overtime3,'completecontent'=>$content),array('id'=>$taskinfo['id']));
		
		if($res){
			Message::cmessage($taskinfo['bossuid'],$taskinfo['bossopenid'],$taskinfo['tasktitle'],'completetask',$taskinfo['id']);
			Util::echoResult(200,'好');
		} 	
		Util::echoResult(201,'操作失败');

	// 雇主确定完成任务
	}elseif($_GPC['op'] == 'confirmtask'){
		$taskid = intval($_GPC['taskid']);
		$taskinfo = pdo_get('zofui_tasktb_privatetask',array('id'=>$taskid,'uniacid'=>$_W['uniacid']));
		if($taskinfo['bossuid'] != $userinfo['uid'] || $taskinfo['status'] != 3) 
			Util::echoResult(201,'您不能操作此任务');
		
		$res = model_privatetask::completeTaskInajaxdealAndCrontab($taskinfo,6,$this->module['config']); // 此方法中已加入通知
		
		if($res) Util::echoResult(200,'好'); 

		Util::echoResult(201,'操作失败');


	// 雇主拒绝任务结果
	}elseif($_GPC['op'] == 'confirmrefuse'){
		$taskid = intval($_GPC['taskid']);
		$refusereason = $_GPC['refusereason'];
		
		$taskinfo = pdo_get('zofui_tasktb_privatetask',array('id'=>$taskid,'uniacid'=>$_W['uniacid']));
		if($taskinfo['bossuid'] != $userinfo['uid'] || $taskinfo['status'] != 3 )
			Util::echoResult(201,'您不能操作此任务');

		if( empty($refusereason) ) Util::echoResult(201,'请输入理由');
		
		//改变任务状态
		$overtime7 = $this->module['config']['privatedealtime']*3600 + time();
		$res = pdo_update('zofui_tasktb_privatetask',array('status'=>7,'bossdealtime'=>time(),'overtime7'=>$overtime7,'refusereason'=>$refusereason),array('id'=>$taskinfo['id']));
		
		if($res){
			//发通知
			Message::cmessage($taskinfo['workeruid'],$taskinfo['workeropenid'],$taskinfo['tasktitle'],'confirmrefuse',$taskinfo['id']);
			Util::echoResult(200,'好'); 	
		} 	
		Util::echoResult(201,'操作失败');

	//雇员接受雇主的拒绝 acceptrefuse
	}elseif($_GPC['op'] == 'acceptrefuse'){
		$taskid = intval($_GPC['taskid']);
		
		$taskinfo = pdo_get('zofui_tasktb_privatetask',array('id'=>$taskid,'uniacid'=>$_W['uniacid']));
		if($taskinfo['workeruid'] != $userinfo['uid'] || $taskinfo['status'] != 7) 
			Util::echoResult(201,'您不能操作此任务');
		
		$res = model_privatetask::acceptRefuseRusultInAjaxAndCronb($taskinfo,8,$this); //此方法里已发通知
		
		if($res) Util::echoResult(200,'好'); 

		Util::echoResult(201,'操作失败');

	//雇员投诉雇主的拒绝
	}elseif($_GPC['op'] == 'omplainboss'){
		$taskid = intval($_GPC['taskid']);
		$refusereason = $_GPC['explainreason'];
		
		$taskinfo = pdo_get('zofui_tasktb_privatetask',array('id'=>$taskid,'uniacid'=>$_W['uniacid']));
		if( $taskinfo['workeruid'] != $userinfo['uid'] || $taskinfo['status'] != 7 ) 
			Util::echoResult(201,'您不能操作此任务');
		
		if( empty($refusereason) ) Util::echoResult(201,'请输入理由');

		//改变任务状态
		$res = pdo_update('zofui_tasktb_privatetask',array('status'=>9,'complaintime'=>time(),'complainreason'=>$refusereason),array('id'=>$taskinfo['id']));
		
		if($res){
			//发通知
			Message::cmessage($taskinfo['bossuid'],$taskinfo['bossopenid'],$taskinfo['tasktitle'],'omplainboss',$taskinfo['id']);
			Util::echoResult(200,'好'); 
		} 

		Util::echoResult(201,'操作失败');

	//对雇主发来的任务雇员拒绝	
	}elseif($_GPC['op'] == 'workerrefusetask'){
		$taskid = intval($_GPC['taskid']);
		
		$taskinfo = pdo_get('zofui_tasktb_privatetask',array('id'=>$taskid,'uniacid'=>$_W['uniacid']));
		if($taskinfo['workeruid'] != $userinfo['uid'] || $taskinfo['status'] != 0) 
			Util::echoResult(201,'您不能操作此任务');
		
		//改变任务状态
		$res = pdo_update('zofui_tasktb_privatetask',array('status'=>1,'accepttime'=>time(),'isend'=>1),array('id'=>$taskinfo['id']));
		
		//退回资金
		if($res){
			if( $taskinfo['type'] == 2 ) model_privatetask::backMoneyToBossInPrivateTask($taskinfo); //这里已删除缓存
			//发通知
			Message::cmessage($taskinfo['bossuid'],$taskinfo['bossopenid'],$taskinfo['tasktitle'],'workerrefusetask',$taskinfo['id']);
			Util::echoResult(200,'好'); 
		} 	
		Util::echoResult(201,'拒绝失败'); 

	//对雇主发来的任务雇员接受任务
	}elseif($_GPC['op'] == 'workertaketask'){
		$taskid = intval($_GPC['taskid']);
		
		$taskinfo = pdo_get('zofui_tasktb_privatetask',array('id'=>$taskid,'uniacid'=>$_W['uniacid']));
		if($taskinfo['workeruid'] != $userinfo['uid'] || $taskinfo['status'] != 0) 
			Util::echoResult(201,'您不能操作此任务');
		
		$overtime2 = $taskinfo['limittime']*3600 + time();
		//改变任务状态
		$res = pdo_update('zofui_tasktb_privatetask',array('status'=>2,'accepttime'=>time(),'overtime2'=>$overtime2),array('id'=>$taskinfo['id']));
		
		if( $res ){
			Message::cmessage($taskinfo['bossuid'],$taskinfo['bossopenid'],$taskinfo['tasktitle'],'workertaketask',$taskinfo['id']);
			Util::echoResult(200,'好'); 
		}
		Util::echoResult(201,'操作失败'); ;	


	// 发布普通任务
	}elseif( $_GPC['op'] == 'addtask' ){
		$_GPC = Util::trimWithArray($_GPC);

		//if( $_W['set']['ismobile'] == 1 && empty( $userinfo['account'] ) ) Util::echoResult(220,'请先注册您的手机号');
		
		if( $_W['set']['ismobile'] == 1 && $_W['dev'] == 'wx' ) {
			load()->model('mc');
			$user = mc_fetch($userinfo['uid'], array('uniacid'=>$_W['uniacid']));
			if( empty( $user['mobile'] ) ){
				Util::echoResult(220,'请先绑定登录手机',array('url'=>Util::createModuleUrl('bindaccount')));
			}
		}

		// 是否已认证审核
		if( $_W['set']['isauth'] > 0 && ($userinfo['verifystatus'] == 0 || $userinfo['verifystatus'] == 1) ){
			$gourl = $this->createMobileUrl('set');
			if( $userinfo['verifystatus'] == 0 ) Util::echoResult(230,'请先提交资料认证',array('url'=>$gourl));
			if( $userinfo['verifystatus'] == 1 ) Util::echoResult(201,'您的认证还未审核，审核后才能发任务');
			if( $userinfo['verifystatus'] == 3 ) Util::echoResult(201,'您的认证未通过，不能发任务');
		}

		// 完成数量
		if( $_W['set']['puberned'] > 0 ){
			$comed = pdo_count('zofui_tasktb_taked',array('status'=>2,'uniacid'=>$_W['uniacid'],'userid'=>$userinfo['uid']));
			if( $comed < $_W['set']['puberned'] ) {
				$dis = $_W['set']['puberned'] - $comed;
				Util::echoResult(201,'发布任务前需要先完成'.$_W['set']['puberned'].'个任务，你还差'.$dis.'个');
			}
		}		

		// 注册会员限制
		if( ($userinfo['level'] <= 0 || $userinfo['utime'] <= TIMESTAMP)  && $_W['set']['upubcom'] == 1 && $_W['set']['ulevel'] == 1 ){
			Util::echoResult(220,'升级会员后才可以发布任务',array('url'=>$this->createMobileUrl('level')));
		}

		$continuemoney = sprintf('%.2f',$_GPC['continuemoney']);
		$data['sortid'] = intval( $_GPC['sortid'] );
		
		$data['title'] = $_GPC['title'];
		$data['mark'] = $_GPC['mark'];
		$data['content'] = $_GPC['content'];
		$data['hidecontent'] = $_GPC['hidecontent'];
		$data['images'] = $_GPC['images'];
		
		$data['num'] = intval( $_GPC['num'] );
		$data['money'] = sprintf('%.2f',$_GPC['money']);

		$data['replytime'] = intval( $_GPC['replytime'] );
		$data['limitnum'] = intval( $_GPC['limitnum'] );
		$data['sex'] = intval( $_GPC['sex'] );
		$data['ishide'] = intval( $_GPC['ishide'] );

		$data['continue'] = intval( $_GPC['continue'] );

		$data['iska'] = intval( $_GPC['iska'] );
		$data['kagoodid'] = $_GPC['kagood'];
		$data['kakey'] = $_GPC['kakey'];
		$data['headimg'] = $_GPC['head'];
		$data['address'] = $_GPC['address'];
		$data['istop'] = intval( $_GPC['istop'] );
		$data['gid'] = $_GPC['gid'];

		$data['isarealimit'] = intval( $_GPC['isarealimit'] );
		$data['istaskform'] = intval( $_GPC['isform'] );
		$data['formid'] = intval( $_GPC['formid'] );
		$data['levellim'] = intval( $_GPC['levellim'] );
		$data['useric'] = iserializer( $_GPC['ic'] );
		if( empty($_GPC['ic']) || !is_array($_GPC['ic']) ){
			$data['useric'] = '';
		}
		if( count( $_GPC['ic'] ) > 6 ){
			Util::echoResult(201,'标签最多选择6个');
		}

		if( $_W['set']['isanw'] == 1 && empty($_W['set']['anwtype']) ){
			$data['readprice'] = sprintf('%.2f',$_GPC['readprice']);
			if( $data['readprice']*1 > 0 && $data['ishide'] == 1 ){
				$data['isread'] = 1;
			}
		}

		if( $data['istaskform'] == 1 ) {
			$form = pdo_get('zofui_tasktb_taskform',array('id'=>$data['formid']));
			if( empty( $form ) ) Util::echoResult(201,'回复模板不存在,请换一个模板');
		}

		if( $data['isarealimit'] > 0 ){
			if( empty( $_GPC['area'] ) ) Util::echoResult(201,'请选择可接任务的区域');
			$list = explode(',',$_GPC['area']);
			$data['province'] = $list[0];
			$data['city'] = $list[1];
			$data['country'] = $list[2];
		}

		if( !empty( $_GPC['linkname'] ) ){
			$link = array();
			foreach ($_GPC['linkname'] as $k => $v) {
				$linkitem['text'] = $v;
				$linkitem['url'] = $_GPC['linkurl'][$k];
				$link[] =  $linkitem;
			}
			$data['link'] = iserializer( $link );
		}

		if( $_GPC['isstep'] == 1 ){

			if( empty($_GPC['stepid']) ){
				Util::echoResult(201,'还没选择任务步骤模板');
			}
			$data['stepid'] = $_GPC['stepid'];

		}elseif( $_GPC['isstep'] == 2 ){
			if( !empty($_GPC['step']) ){
				$step = json_decode(htmlspecialchars_decode($_GPC['step']),true);
				if( is_array($step) && !empty($step) ){
					foreach ($step as $k => $v) {
						if( empty($v['name']) ) Util::echoResult(201,'步骤没有填写内容');
					}
				}else{
					Util::echoResult(201,'步骤设置错误');
				}
				$stepdat = iserializer($step);
			}
		}

		if( empty($_GPC['content']) && empty($stepdat) && empty($data['stepid']) ){
			Util::echoResult(201,'还没有填写任务内容');
		}

		$check = model_task::isCanPub( $data );
		if( $check !== 200 ) Util::echoResult(201,$check);

		// 会员等级
		$level = model_user::levelRes($userinfo,$this->module['config']);
		if( $level == 1 ) $_W['set']['leastcommoney'] = $_W['set']['leastcommoneya'];
		if( $level == 2 ) $_W['set']['leastcommoney'] = $_W['set']['leastcommoneyb'];
		
		if( $data['money'] < $_W['set']['leastcommoney'] )
			Util::echoResult(201,'任务赏金至少'.$_W['set']['leastcommoney']);
		
		// 算钱
		$taskmoney = $data['num']*$data['money'];
		

		// 分类的
		$sort = pdo_get('zofui_tasktb_tasksort',array('uniacid'=>$_W['uniacid'],'id'=>$data['sortid']));
		if( empty($sort) ) Util::echoResult(201,'分类不存在');
		$sortother = iunserializer($sort['other']);
		if( $sortother['pubsever'] > 0 ){
			$_W['set']['commonserver'] = $sortother['pubsever'];
		}
		if( $sortother['leastpubsever'] > 0 ){
			$_W['set']['commonserverleast'] = $sortother['leastpubsever'];
		}
		if( $sortother['topsever'] > 0 ){
			$_W['set']['topserver'] = $sortother['topsever'];
		}
		

		// 会员等级
		$level = model_user::levelRes($userinfo,$this->module['config']);
		if( $level == 1 ){
			$_W['set']['commonserver'] = $_W['set']['commonservera'];
			$_W['set']['commonserverleast'] = $_W['set']['commonserverleasta'];
		}
		if( $level == 2 ){
			$_W['set']['commonserver'] = $_W['set']['commonserverb'];
			$_W['set']['commonserverleast'] = $_W['set']['commonserverleastb'];
		}
		
		$server = $taskmoney*$_W['set']['commonserver']/100;
		$server = max( $server,$_W['set']['commonserverleast'] );

		$ka = 0; // 卡首屏的钱
		// 会员等级
		$level = model_user::levelRes($userinfo,$this->module['config']);
		if( $level == 1 ) $_W['set']['kaserver'] = $_W['set']['kaservera'];
		if( $level == 2 ) $_W['set']['kaserver'] = $_W['set']['kaserverb'];
		
		if( $data['iska'] == 1 ) $ka = sprintf('%.2f',$_W['set']['kaserver']);

		$top = 0;
		// 会员等级
		$level = model_user::levelRes($userinfo,$this->module['config']);
		if( $level == 1 ) $_W['set']['topserver'] = $_W['set']['topservera'];
		if( $level == 2 ) $_W['set']['topserver'] = $_W['set']['topserverb'];
		
		if( $data['istop'] == 1 ) $top = sprintf('%.2f',$_W['set']['topserver']);

		
		$continueday = 0;
		$totalcontinuemoney = 0; //连续发布后几天的钱
		$ewai = 0; // 额外奖励的钱 = 任务数量X额外奖励
		if( $data['continue'] == 1 ){
			$continueday = intval( $_GPC['continueday'] );

			if( $continueday <= 0 ) Util::echoResult(201,'连续发布天数必须填正整数');

			$totalcontinuemoney = $continueday*( $taskmoney + $server + $ka + $top );
			$ewai = $continuemoney*$data['num'];
		}

		$total = $taskmoney + $server + $ka + $top + $totalcontinuemoney + $ewai;
		
		$credit = model_user::getUserCredit( $userinfo['uid'] );
		if($credit['credit2'] < $total){
			Util::echoResult(210,'您的'.$_W['cname'].'不足',array('url'=>Util::createModuleUrl('money',array('op'=>'in'))));
		}
		
		// 保证金
		$per = 1;
		if( $_W['set']['moneytype'] == 1 && !empty( $_W['set']['creditper'] ) ){
			$per = $_W['set']['creditper'];
		}

		$isdeposit = $_W['set']['isdeposit']*1;

		if( $userinfo['deposit']*$per < $total && empty( $isdeposit ) ){
			$thisneed = $total/$per;
			Util::echoResult(210,'您的保证金不足，发布此任务账户需留存'.$thisneed.'保证金',array('url'=>Util::createModuleUrl('deposit',array('op'=>'in'))));
		}


		if( $userinfo['deposit']*$per < $isdeposit && $isdeposit > 0 ) {
			Util::echoResult(210,'您的保证金不足，发布任务账户需留存'.$isdeposit.'保证金',array('url'=>Util::createModuleUrl('deposit',array('op'=>'in'))));
		}
		
		// 扣钱
		$res = model_user::updateUserCredit($userinfo['uid'],-$total,2,1);
		// 资金记录
		model_money::insertMoneyLog($userinfo['openid'],-$total,1,7,$userinfo['uid']);
		
		if( $sortother['tasktime'] > 0 ){
			$_W['set']['autoconfirm'] = $sortother['tasktime'];
		}
		
		if( $res ){
			$data['costtop'] = $top;
			$data['uniacid'] = $_W['uniacid'];
			$data['puber'] = $userinfo['openid'];
			$data['userid'] = $userinfo['uid'];
			$data['start'] = TIMESTAMP;
			$data['createtime'] = TIMESTAMP;
			$time = $_W['set']['autoconfirm'] > 0 ?  $_W['set']['autoconfirm'] : 1;
			$data['end'] = TIMESTAMP + $time*3600;
			$data['status'] = 0;
			$data['isstart'] = 0;
			if( $_W['set']['isverifytask'] == 1 ) $data['status'] = 1;

			$data['images'] = iserializer( $data['images'] );
			$data['kakey'] = iserializer( $data['kakey'] );
			$data['costserver'] = $server;
			$data['costka'] = $ka;

			if( !empty($stepdat) ){
				$ind = array(
					'uniacid' => $_W['uniacid'],
					'step' => $stepdat,
				);
				pdo_insert('zofui_tasktb_step',$ind);
				$data['stepid'] = pdo_insertid();
			}

			if( $data['continue'] == 1 ){
				$continue['uniacid'] = $_W['uniacid'];
				$continue['money'] = $continuemoney;
				$continue['totalnum'] = $data['num'];
				$continue['totalmoney'] = $data['num']*$continue['money'];
				pdo_insert('zofui_tasktb_continue',$continue);
				$data['continueid'] = pdo_insertid();
			}

			$data['idcode'] = model_task::taskCode();
			$res = pdo_insert('zofui_tasktb_task',$data);
			$id = pdo_insertid();

			if( $res && $data['status'] == 0 ){ // 给上级提成
				$upmoney = $data['costtop'] + $data['costserver'] + $data['costka'];
				model_task::pubGiveParent($_W['set'],$userinfo['id'],$userinfo['parent'],$id,1,$upmoney);
			}

			if( $data['continue'] == 1 ){

				$today = strtotime( date('Y-m-d',TIMESTAMP) );
				for ($i=0; $i < $continueday; $i++) {

					$newdata = $data;
					$newdata['isstart'] = 1;
					$newdata['start'] = $today + 24*3600*($i+1);
					$newdata['end'] = $newdata['end'] + 24*3600*($i+1);
					$newdata['idcode'] = model_task::taskCode();
					
					$newres = pdo_insert('zofui_tasktb_task',$newdata);
					$newid = pdo_insertid();

					if( $newres && $newdata['status'] == 0 ){ // 给上级提成
						$upmoney = $newdata['costtop'] + $newdata['costserver'] + $newdata['costka'];
						model_task::pubGiveParent($_W['set'],$userinfo['id'],$userinfo['parent'],$newid,1,$upmoney);
					}
				}
			}

			// 平台发布量
			//$totalpub = ($continueday + 1)*$data['num'];
			//pdo_query("UPDATE ".tablename('zofui_tasktb_scan')." SET `pubed` = `pubed` + ".$totalpub.",`commpubed` = `commpubed` + ".$totalpub." WHERE `uniacid` = '{$_W['uniacid']}' ");
			
			// 管理员通知
			Message::addMessage(1,$id);
			
			// 新任务通知
			//model_task::setMessage( $_W['set'],$id );

			Util::echoResult(200,'发布成功',array('taskid'=>$id));
		}
		Util::echoResult(201,'发布失败');

	// 接任务
	}elseif($_GPC['op'] == 'taketask'){

		//$gourl = $this->createMobileUrl('regist',array('op'=>'op'));
		//if( $_W['set']['ismobile'] == 1 && empty( $userinfo['account'] ) ) Util::echoResult(220,'请先注册您的手机号',array('url'=>$gourl));
		
		if( $_W['set']['ismobile'] == 1 && $_W['dev'] == 'wx' ) {
			load()->model('mc');
			$user = mc_fetch($userinfo['uid'], array('uniacid'=>$_W['uniacid']));
			if( empty( $user['mobile'] ) ){
				Util::echoResult(220,'请先绑定登录手机',array('url'=>Util::createModuleUrl('bindaccount')));
			}
		}

		// 是否已认证审核
		if( $_W['set']['isauth'] == 2 && ($userinfo['verifystatus'] == 0 || $userinfo['verifystatus'] == 1) ){
			$gourl = $this->createMobileUrl('set');
			if( $userinfo['verifystatus'] == 0 ) Util::echoResult(220,'请先提交资料认证',array('url'=>$gourl));
			if( $userinfo['verifystatus'] == 1 ) Util::echoResult(201,'您的认证还未审核，审核后才能再接任务');
			if( $userinfo['verifystatus'] == 3 ) Util::echoResult(201,'您的认证未通过，不能接任务');
		}
		
		$id = intval( $_GPC['taskid'] );
		$task = pdo_get('zofui_tasktb_task',array('uniacid'=>$_W['uniacid'],'id'=>$id));
		
		// 区域限制
		if( $task['isarealimit'] > 0 ){
			$res = Util::checkLocation($task['isarealimit'],$task['province'],$task['city'],$task['country']);
			if( !$res ) Util::echoResult(211,'您所在地区不能接此任务');
		}

		// 会员等级
		if( $_W['set']['ulevel'] == 1 ){

			if( $task['levellim'] == 1 && $userinfo['level'] != 1 ) {
				Util::echoResult(220, (empty($_W['set']['uonename']) ? '一级会员' : $_W['set']['uonename']) .'才可接此任务',array('url'=>$this->createMobileUrl('level')));
			}
			if( $task['levellim'] == 2 && $userinfo['level'] != 2 ) {
				Util::echoResult(220, (empty($_W['set']['utwoname']) ? '二级会员' : $_W['set']['utwoname']) .'才可接此任务',array('url'=>$this->createMobileUrl('level')));
			}
			
			if( $task['levellim'] == 3 && !in_array($userinfo['level'], array(1,2)) ) {
				Util::echoResult(220,  (empty($_W['set']['uonename']) ? '一级' : $_W['set']['uonename']) .','. empty($_W['set']['utwoname']) ? '二级会员' : $_W['set']['utwoname'] .'才可接此任务',array('url'=>$this->createMobileUrl('level')));
			}
				
		}

		// 注册会员限制
		if( ($userinfo['level'] <= 0 || $userinfo['utime'] <= TIMESTAMP)  && $_W['set']['ugetcom'] == 1 && $_W['set']['ulevel'] == 1 ){
			Util::echoResult(220,'升级会员后才可以接任务',array('url'=>$this->createMobileUrl('level')));
		}

		if( !empty($task['gid']) ){
			$ismore = pdo_get('zofui_tasktb_taked',array('uniacid'=>$_W['uniacid'],'gid'=>$task['gid'],'endtime >'=>TIMESTAMP));
			if( !empty($ismore) ){
				Util::echoResult(201,'此类任务你已经接过了，不能再接');
			}
		}

		// 
		$takenum = $_W['set']['takenned'] <= 0 ? 1 : $_W['set']['takenned'];
		if( $_W['set']['taketask'] == 1 ){

			$istakedd = pdo_count('zofui_tasktb_taked',array('uniacid'=>$_W['uniacid'],'status'=>array(0,1),'endtime >='=>TIMESTAMP,'userid'=>$userinfo['uid']));
			if( $istakedd >= $takenum ) Util::echoResult(201,'你有接的任务还没完成，请先完成已接的任务');
		}
		if( $_W['set']['taketask'] == 2 ){
			$istakedd = pdo_count('zofui_tasktb_taked',array('uniacid'=>$_W['uniacid'],'status'=>0,'endtime >='=>TIMESTAMP,'userid'=>$userinfo['uid']));
			if( $istakedd >= $takenum ) Util::echoResult(201,'你有接的任务还没提交，请先提交后已接的任务');
		}

		// 先看缓存 防止高并发多接 ，这里的takednum在queue.class.php内定时改变回来
		$num = Util::getCache('takednum',$id);
		if( $num >= $task['num'] ){
			Util::echoResult(201,'任务已被接完',array($num));
		}
		Util::setCache('takednum',$id,$num+1);

		if( empty( $task ) ) Util::echoResult(201,'任务不存在');
		if( $task['status'] != 0 ) Util::echoResult(201,'任务未上架');
		if( $task['start'] > TIMESTAMP ) Util::echoResult(201,'任务未开始');
		if( $task['end'] < TIMESTAMP ) Util::echoResult(201,'任务已结束');
		if( $task['iscount'] == 1 ) Util::echoResult(201,'任务已结束');
		if( $task['ispause'] == 1 ) Util::echoResult(201,'任务已关闭');

		if( $task['sex'] == 1 && !in_array($userinfo['sex'],array('1','3')) ) Util::echoResult(201,'此任务仅限男性可接');
		if( $task['sex'] == 2 && !in_array($userinfo['sex'],array('2','4')) ) Util::echoResult(201,'此任务仅限女性可接');
		
		if( $userinfo['activity'] <= 0 ) Util::echoResult(201,'您的活跃度不足，明天再来吧');
		
		$puber = model_user::getSingleUser($task['userid']);
		$mystatus = model_task::getStatusInTask( $task,0,false );

		//$lastnum = model_task::isEmpty( $task['id'],$task['num'] );
		//if( $lastnum <= 0 ) {
		//	Util::echoResult(201,'任务已被接完');
		//}

		if( $mystatus['status'] == 1 ) Util::echoResult(201,'你已接了此任务还没完成，请完成了再接');
		if( $mystatus['status'] == 2 ) Util::echoResult(201,'你已经接了很多次了，不能再接了');
		if( $mystatus['status'] == 3 ) Util::echoResult(201,'你不能再接'.$puber['nickname'].'发布的任务');
		if( $mystatus['status'] == 4 ) Util::echoResult(201,'任务已被接完');
		if( $mystatus['status'] == 5 ) Util::echoResult(201,'你的区域不能接此任务');
		if( $mystatus['status'] == 6 ) Util::echoResult(201,'你不符合任务要求');

		$endtime = $_W['set']['endtime'] > 0 ? $_W['set']['endtime'] : 60;
		$endtime = $endtime*60;
		$replytime = $task['replytime']*60; // 等待回复时间

		$last = $task['end'] - TIMESTAMP;
		if( $last <= $replytime ){ // 如果剩余时间小于设置的停留时间，停留时间设置为剩余的一半
			$replytime = $last/2;
		}
		if( $last <= $endtime ){ // 如果剩余时间小于设置的任务限时，任务限时
			$endtime = $last;
		}

		$data = array(
			'uniacid' => $_W['uniacid'],
			'openid' => $userinfo['openid'],
			'userid' => $userinfo['uid'],
			'taskid' => $task['id'],
			'continueid' => $task['continueid'],
			'createtime' => TIMESTAMP,
			'waittime' => TIMESTAMP + $replytime,
			'endtime' => TIMESTAMP + $endtime,
			'money' => $task['money'],
			'puber' => $task['puber'],
			'pubuid' => $task['userid'],
			'ip' => getIp(),
			'gid' => $task['gid'],
		);
		$res = pdo_insert('zofui_tasktb_taked',$data);

		if( $res ){
			if( $mystatus['totallast'] <= 1 ){
				pdo_update('zofui_tasktb_task',array('isempty'=>1),array('id'=>$task['id']));
			}
			Util::addOrMinusOrUpdateData('zofui_task_user',array('activity'=>-1),$userinfo['id']);
			Util::deleteCache( 'u',$userinfo['uid'] );
			Util::echoResult(200,'您已接到任务，请在规定的时间内完成并回复，否则任务失败');
		}
		Util::echoResult(201,'接任务失败了');


	// 回复
	}elseif($_GPC['op'] == 'reply'){

		$taskid = $_GPC['taskid'];
		$data['content'] = $_GPC['content'];
		$data['images'] = iserializer( $_GPC['images'] );
		$data['replytime'] = TIMESTAMP;
		$data['status'] = 1;

		$subform = json_decode( htmlspecialchars_decode( $_GPC['form'] ),true );
		
		$task = pdo_get('zofui_tasktb_task',array('uniacid'=>$_W['uniacid'],'id'=>$taskid));

		if( $task['istaskform'] == 1 ) { // 表单
			$form = pdo_get('zofui_tasktb_taskform',array('id'=>$task['formid']));
			$form['form'] = iunserializer( $form['form'] );

			if( !empty( $form['form'] ) ) {
				if( empty( $subform ) ) Util::echoResult(201,'请填写回复的内容');

				// 验证内容
				$inform = array();
				foreach ($form['form'] as $v) {
					$isin = 0;
					foreach ($subform as $vv) {

						if( $v['id'] == $vv['id'] ){
							$isin = 1;

							if( $v['type'] == 'img' && count( $vv['value'] ) < $v['maxnum'] ) {
								Util::echoResult(201,$v['name'].'必须上传'.$v['maxnum'].'张图片');
							}
							if( $v['type'] == 'tel' && !preg_match('/^1\d{10}$/', $vv['value']) ){
								Util::echoResult(201,'请填写正确的'.$v['name']);
							}
							if( $v['type'] == 'email' && !preg_match('/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/i', $vv['value']) ){
								Util::echoResult(201,'请填写正确的'.$v['name']);
							}
							if( $v['type'] == 'card' && strlen( $vv['value'] ) < 15 ){
								Util::echoResult(201,'请填写正确的'.$v['name']);
							}
							if( $v['type'] == 'text' && empty( $vv['value'] ) ){
								Util::echoResult(201,'请填写'.$v['name']);
							}
							if( $v['type'] == 'num' && !is_numeric( $vv['value'] ) ){
								Util::echoResult(201,$v['name'].'需填写数字');
							}
							$inform[] = array('name'=>$v['name'],'type'=>$v['type'],'value'=>$vv['value']);
						}
					}
					if( empty( $isin ) ) {
						Util::echoResult(201,'请填写'.$v['name']);
					}
				}
				$data['subform'] = base64_encode( iserializer( $inform ) );
			}else{
				if( empty( $data['content'] ) ) Util::echoResult(201,'请填写回复的内容');
			}

		}else{
			if( empty( $data['content'] ) ) Util::echoResult(201,'请填写回复的内容');
		}


		if( empty( $task ) ) Util::echoResult(201,'任务不存在');
		if( $task['status'] != 0 ) Util::echoResult(201,'任务未上架');
		if( $task['start'] > TIMESTAMP ) Util::echoResult(201,'任务未开始');
		if( $task['end'] < TIMESTAMP ) Util::echoResult(201,'任务已结束');
		if( $task['iscount'] == 1 ) Util::echoResult(201,'任务已结束');

		$taked = pdo_get('zofui_tasktb_taked',array('uniacid'=>$_W['uniacid'],'taskid'=>$task['id'],'userid'=>$userinfo['uid'],'status'=>0));
		if( empty( $taked ) ) Util::echoResult(201,'您还没接到此任务');
		if( $taked['endtime'] < TIMESTAMP ) Util::echoResult(201,'请刷新页面重新接任务');
		if( $taked['waittime'] > TIMESTAMP ) Util::echoResult(201,'还没到可回复任务的时间');

		$data['endtime'] = $task['end'] + 100*365*24*60*60;
		$res = pdo_update('zofui_tasktb_taked',$data,array('uniacid'=>$_W['uniacid'],'id'=>$taked['id']));

		if( $res ){
			
			// 给雇主发消息
			Message::toPuber($task['userid'],$task['puber'],$task['id'],$data['content'],$task['title'],$userinfo['nickname']);
			Util::echoResult(200,'您已成功回复任务，请等待雇主处理您的回复');
		}
		Util::echoResult(201,'回复失败');

	// 打赏	
	}elseif($_GPC['op'] == 'dashang'){

		$id = intval( $_GPC['reid'] );
		$taked = pdo_get('zofui_tasktb_taked',array('uniacid'=>$_W['uniacid'],'id'=>$id,'status'=>2));
		if( empty( $taked ) ) Util::echoResult(201,'未找到数据');

		if( $_GPC['num'] <= 0 ) Util::echoResult(201,'打赏银票必须大于0');
		if( $_GPC['num'] < $_W['set']['dsmin'] && $_W['set']['dsmin'] > 0 ) Util::echoResult(201,'打赏银票必须大于'.$_W['set']['dsmin']);
		if( $_GPC['num'] > $_W['set']['dsmax'] && $_W['set']['dsmax'] > 0 ) Util::echoResult(201,'打赏银票必须小于'.$_W['set']['dsmax']);

		$user = pdo_get('zofui_task_user',array('id'=>$userinfo['id']));
		if( empty($user) ) Util::echoResult(201,'未找到被打赏者数据');

		$isread = pdo_get('zofui_tasktb_anwread',array('uniacid'=>$_W['uniacid'],'taskid'=>$taked['taskid'],'uid'=>$userinfo['uid']));
		if( empty($isread) && $taked['userid'] != $userinfo['uid'] && $taked['pubuid'] != $userinfo['uid'] ) 
			Util::echoResult(201,'你不能打赏');

		if( $user['yinp'] < $_GPC['num'] ){
			Util::echoResult(201,'你只有'.$user['yinp'].'银票，不可打赏'.$_GPC['num'].'银票');
		}

		$res = Util::addOrMinusOrUpdateData('zofui_task_user',array('yinp'=>-$_GPC['num']),$userinfo['id']);

		if($res){

			if( $_W['set']['dsser'] > 0 ){
				$dsser = $_GPC['num']*$_W['set']['dsser']/100;
				if( $dsser >= 0.01 ){
					Util::addOrMinusOrUpdateData('zofui_task_user',array('baoshi'=>$dsser),$userinfo['id']);
				}
			}
			if( $_W['set']['dssed'] > 0 ){
				$dssed = $_GPC['num']*$_W['set']['dssed']/100;
				if( $dssed >= 0.01 ){
					$taker = model_user::getSingleUser($taked['userid']);
					Util::addOrMinusOrUpdateData('zofui_task_user',array('baoshi'=>$dssed),$taker['id']);
					Util::deleteCache('u',$taker['uid']);
				}
			}

			$indata = array(
				'uniacid' => $_W['uniacid'],
				'uid' => $userinfo['uid'],
				'taskid' => $taked['taskid'],
				'takedid' => $taked['id'],
				'yinp' => $_GPC['num'],
				'givedser' => $dsser,
				'givedsed' => $dssed,
				'createtime' => TIMESTAMP
			);
			pdo_insert('zofui_tasktb_groupds',$indata);

			Util::deleteCache('u',$userinfo['uid']);

			Util::addOrMinusOrUpdateData('zofui_tasktb_taked',array('ds'=>$_GPC['num']),$taked['id']);
			Util::echoResult(200,'打赏完成');
		}else{
			Util::echoResult(201,'打赏失败');
		}

	// 采纳
	}elseif($_GPC['op'] == 'agree'){

		if( empty( $_GPC['idlist'] ) || !is_array( $_GPC['idlist'] ) ) Util::echoResult(201,'请先选择要采纳的任务');

		$success = 0;
		foreach ( $_GPC['idlist'] as $v ) {
			
			$taked = pdo_get('zofui_tasktb_taked',array('uniacid'=>$_W['uniacid'],'id'=>$v,'status'=>1));
			if( empty( $taked ) ) continue;
			
			$task = pdo_get('zofui_tasktb_task',array('uniacid'=>$_W['uniacid'],'id'=>$taked['taskid']));
			if( empty( $taked ) ) continue;
			if( $task['userid'] != $userinfo['uid'] ) Util::echoResult(201,'您不是任务的发布者');
			
			if( $task['status'] != 0 ) Util::echoResult(201,'任务未上架');
			if( $task['start'] > TIMESTAMP ) Util::echoResult(201,'任务未开始');
			if( $task['end'] < TIMESTAMP ) Util::echoResult(201,'任务已结束');

			$res = model_task::agreeTask($_W['set'],$taked,$task);

			if( $res ) $success ++;

		}

		if( $success > 0 ){
			Util::echoResult(200,'成功采纳'.$success.'项回复');
		}
		Util::echoResult(201,'处理失败');

	// 拒绝任务
	}elseif($_GPC['op'] == 'refuse'){

		if( empty( $_GPC['idlist'] ) || !is_array( $_GPC['idlist'] ) ) Util::echoResult(201,'请先选择要拒绝的任务');
		if( empty( $_GPC['reason'] ) ) Util::echoResult(201,'请输入拒绝理由');

		$success = 0;
		foreach ( $_GPC['idlist'] as $v ) {
			
			$taked = pdo_get('zofui_tasktb_taked',array('uniacid'=>$_W['uniacid'],'id'=>$v,'status'=>1));
			if( empty( $taked ) ) continue;
			
			$task = pdo_get('zofui_tasktb_task',array('uniacid'=>$_W['uniacid'],'id'=>$taked['taskid']));
			if( empty( $taked ) ) continue;
			if( $task['userid'] != $userinfo['uid'] ) Util::echoResult(201,'您不是任务的发布者');
			
			if( $task['status'] != 0 ) Util::echoResult(201,'任务未上架');
			if( $task['start'] > TIMESTAMP ) Util::echoResult(201,'任务未开始');
			if( $task['end'] < TIMESTAMP ) Util::echoResult(201,'任务已结束');

			$res = model_task::refuseTask($taked,$_GPC['reason'],$task,$userinfo['nickname']);

			if( $res ) $success ++;

		}

		if( $success > 0 ){
			Util::echoResult(200,'成功拒绝'.$success.'项回复');
		}
		Util::echoResult(201,'处理失败');


	// 拒绝任务
	}elseif($_GPC['op'] == 'show' || $_GPC['op'] == 'hide'){

		$id = intval( $_GPC['idlist'][0] );
		if( empty( $id ) ) Util::echoResult(201,'请先选择任务');

		$taked = pdo_get('zofui_tasktb_taked',array('uniacid'=>$_W['uniacid'],'id'=>$id));
		if( empty( $taked ) ) Util::echoResult(201,'没有找到回复');

		$task = pdo_get('zofui_tasktb_task',array('uniacid'=>$_W['uniacid'],'id'=>$taked['taskid']));
		if( empty( $taked ) ) Util::echoResult(201,'没有找到任务');

		if( $task['userid'] != $userinfo['uid'] ) Util::echoResult(201,'您不是任务的发布者');
		
		if( $task['status'] != 0 ) Util::echoResult(201,'任务未上架');

		$isscan = $_GPC['op'] == 'show' ? 0 : 1;
		$nstr = $_GPC['op'] == 'show' ? '已显示' : '已隐藏';

		pdo_update('zofui_tasktb_taked',array('isscan'=>$isscan),array('id'=>$id));

		Util::echoResult(200,$nstr);

	// 普通任务提醒
	}elseif( $_GPC['op'] == 'remind' ){

		$id = intval( $_GPC['idlist'][0] );
		if( empty( $_GPC['idlist'][0] ) ) Util::echoResult(201,'请先选择任务');
		if( empty( $_GPC['content'] ) ) Util::echoResult(201,'请输入内容');

		$taked = pdo_get('zofui_tasktb_taked',array('uniacid'=>$_W['uniacid'],'id'=>$id,'status'=>1));
		if( empty( $taked ) ) Util::echoResult(201,'没有找到回复');

		$task = pdo_get('zofui_tasktb_task',array('uniacid'=>$_W['uniacid'],'id'=>$taked['taskid']));
		if( empty( $taked ) ) Util::echoResult(201,'没有找到任务');

		if( $task['userid'] != $userinfo['uid'] ) Util::echoResult(201,'您不是任务的发布者');
		
		if( $task['status'] != 0 ) Util::echoResult(201,'任务未上架');
		if( $task['start'] > TIMESTAMP ) Util::echoResult(201,'任务未开始');
		if( $task['end'] < TIMESTAMP ) Util::echoResult(201,'任务已结束');

		$sended = Util::countDataNumber('zofui_tasktb_remindlog',array( 'takedid'=>$taked['id'],'type'=>0,'mtype'=>0,'createtime>'=>(TIMESTAMP - 60*10) ));
		if( $sended > 0 ) Util::echoResult(201,'你已发过提醒，过段时间才能再提醒（10分钟内只能提醒对方1次）');

		Message::noticeusetask($taked['userid'],$taked['openid'],$task['id'],$task['title'],$_GPC['content']);

		$data = array(
			'uniacid' => $_W['uniacid'],
			'takedid' => $taked['id'],
			'createtime' => TIMESTAMP,
			'content' => $_GPC['content'],
		);
		pdo_insert('zofui_tasktb_remindlog',$data);

		Util::echoResult(201,'已发送提醒');

	// 关闭和开启任务
	}elseif( $_GPC['op'] == 'pause' ){

		$id = intval( $_GPC['taskid'] );
		$task = pdo_get('zofui_tasktb_task',array('uniacid'=>$_W['uniacid'],'id'=>$id));

		if( empty( $task ) ) Util::echoResult(201,'任务不存在');
		if( $task['status'] != 0 ) Util::echoResult(201,'任务未上架');
		if( $task['start'] > TIMESTAMP ) Util::echoResult(201,'任务未开始');
		if( $task['iscount'] == 1 ) Util::echoResult(201,'任务已结算过了');

		if( $task['userid'] != $userinfo['uid'] ) Util::echoResult(201,'您不能操作此任务');

		$type = empty($_GPC['type']) ? 1 : 0;
		
		if( $type == 1 && $task['ispause'] == 1 ) Util::echoResult(201,'任务已在关闭中');
		if( $type == 0 && $task['ispause'] == 0 ) Util::echoResult(201,'任务已在开启中');	

		pdo_update('zofui_tasktb_task',array('ispause'=>$type),array('id'=>$task['id']));

		Util::echoResult(200,'操作成功');

	// 结算任务
	}elseif($_GPC['op'] == 'counttask'){

		$id = intval( $_GPC['taskid'] );
		$task = pdo_get('zofui_tasktb_task',array('uniacid'=>$_W['uniacid'],'id'=>$id));

		if( empty( $task ) ) Util::echoResult(201,'任务不存在');
		if( $task['status'] != 0 ) Util::echoResult(201,'任务未上架');
		if( $task['start'] > TIMESTAMP ) Util::echoResult(201,'任务未开始');
		if( $task['iscount'] == 1 ) Util::echoResult(201,'任务已结算过了');

		if( $task['userid'] != $userinfo['uid'] ) Util::echoResult(201,'您不能结算此任务');

		$counting = Util::getCache('counttask',$task['id']);
		if( is_array( $counting ) && $counting['status'] == 1 ) {
			Util::echoResult(200,'此任务正在被处理中，请重试');
		}
		Util::setCache('counttask',$task['id'],array('status'=>1));

		$res = model_task::countTask( $task );

		Util::deleteCache( 'counttask',$task['id'] );
		if( $res ) Util::echoResult(200,'成功结算任务');
		Util::echoResult(201,'结算失败');

	// 追加任务
	}elseif($_GPC['op'] == 'subaddtask'){

		$id = intval( $_GPC['taskid'] );
		$task = pdo_get('zofui_tasktb_task',array('uniacid'=>$_W['uniacid'],'id'=>$id));

		if( empty( $task ) ) Util::echoResult(201,'任务不存在');
		if( $task['status'] != 0 ) Util::echoResult(201,'任务未上架');
		if( $task['start'] > TIMESTAMP ) Util::echoResult(201,'任务未开始');
		if( $task['end'] < TIMESTAMP ) Util::echoResult(201,'任务已结束');
		if( $task['iscount'] == 1 ) Util::echoResult(201,'任务已结算过了');

		if( $task['userid'] != $userinfo['uid'] ) Util::echoResult(201,'您不能操作此任务');

		$num = intval( $_GPC['value'] );
		if( $num <= 0 ) Util::echoResult(201,'追加数量必须是大于0的整数');

		$money = $task['money']*$num;
		
		// 会员等级
		$level = model_user::levelRes($userinfo,$this->module['config']);
		if( $level == 1 ){
			$_W['set']['commonserver'] = $_W['set']['commonservera'];
		}
		if( $level == 2 ){
			$_W['set']['commonserver'] = $_W['set']['commonserverb'];
		}
		
		$server = $_W['set']['commonserver']*$money/100;

		$ewai = 0;
		if( $task['continue'] == 1 ){
			$continue = pdo_get('zofui_tasktb_continue',array('uniacid'=>$_W['uniacid'],'id'=>$task['continueid']));
			$ewai = $continue['money']*$num;
		}
		$total = $money + $server + $ewai;
		if( $total <= 0 ) Util::echoResult(201,'请刷新页面重试');

		$credit = model_user::getUserCredit( $userinfo['uid'] );
		if($credit['credit2'] < $total){
			Util::echoResult(210,'您的'.$_W['cname'].'不足',array('url'=>Util::createModuleUrl('money',array('op'=>'in'))));
		}
		
		// 扣钱
		$res = model_user::updateUserCredit($userinfo['uid'],-$total,2,1);
		if( $res ){
			// 资金记录
			model_money::insertMoneyLog($userinfo['openid'],-$total,1,12,$userinfo['uid']);
			pdo_update('zofui_tasktb_task',array('isempty'=>0,'num'=>$num+$task['num']),array('id'=>$task['id']));

			if( $task['continue'] == 1 ){
				Util::addOrMinusOrUpdateData('zofui_tasktb_continue',array('totalmoney'=>$ewai,'totalnum'=>$num),$task['continueid']);
			}

			Util::echoResult(200,'追加成功',array('taskid'=>$id));
		}
		Util::echoResult(201,'发布失败');


	// 留言
	}elseif($_GPC['op'] == 'pubmessage'){

		$data['taskid'] = intval( $_GPC['taskid'] );
		$data['content'] = $_GPC['message'];

		if( empty( $data['content'] ) ) Util::echoResult(201,'请输入提问内容');

		if( $_GPC['from'] == 'task' || empty( $_GPC['from'] ) ){
			$type = 0;
			$task = pdo_get('zofui_tasktb_task',array('uniacid'=>$_W['uniacid'],'id'=>$data['taskid']));

			if( $task['type'] == 1 ){
				$type = 2;
			}

		}elseif( $_GPC['from'] == 'tbtask' ){
			$type = 1;
			$task = pdo_get('zofui_tasktb_tbtask',array('uniacid'=>$_W['uniacid'],'id'=>$data['taskid']));
		}


		if( empty( $task ) ) Util::echoResult(201,'任务不存在，不能再留言');
		if( $task['status'] != 0 ) Util::echoResult(201,'任务未上架，不能再留言');
		if( $task['end'] < TIMESTAMP ) Util::echoResult(201,'任务已结束，不能再留言');
		if( $task['iscount'] == 1 ) Util::echoResult(201,'任务已结束，不能再留言');		

		$where = array('uniacid'=>$_W['uniacid'],'userid'=>$userinfo['uid'],'taskid'=>$task['id'],'type'=>$type);
		$renum = Util::countDataNumber('zofui_tasktb_taskmessage',$where);
		if( $renum >= 5 ) Util::echoResult(201,'您已经留言很多次了，不能再留言');

		if( $_GPC['from'] == 'task' ){
			// 是否已接到
			if( $task['type'] == 0 ){
				$istaked = pdo_get('zofui_tasktb_taked',array('uniacid'=>$_W['uniacid'],'taskid'=>$task['id'],'userid'=>$userinfo['uid']));
				if( empty( $istaked ) ) Util::echoResult(201,'您还没接到任务，不能留言');
			}else{
				$istaked = pdo_get('zofui_tasktb_usetasklog',array('uniacid'=>$_W['uniacid'],'taskid'=>$task['id'],'userid'=>$userinfo['uid']));
				if( empty( $istaked ) ) Util::echoResult(201,'您还没申请试用任务，不能留言');
			}
		}elseif( $_GPC['from'] == 'tbtask' ){
			$istaked = pdo_get('zofui_tasktb_tbtaked',array('uniacid'=>$_W['uniacid'],'taskid'=>$task['id'],'userid'=>$userinfo['uid']));
			if( empty( $istaked ) ) Util::echoResult(201,'您还没接到任务，不能留言');
		}

		


		$data['uniacid'] = $_W['uniacid'];
		$data['openid'] = $userinfo['openid'];
		$data['userid'] = $userinfo['uid'];
		$data['time'] = TIMESTAMP;
		$data['type'] = $type;
		if( empty( $task['userid'] ) ){ // 管理员发布的
			$data['isadmin'] = 1;
		}

		$res = pdo_insert('zofui_tasktb_taskmessage',$data);

		if( $res ){
			// 给发布者发消息

			$time = Util::formatTime( TIMESTAMP );
			$str = <<<div
	  			<div class="popup_message_item item_cell_box">
	  				<div class="popup_message_l">
	  					<img src="{$userinfo['headimgurl']}">
	  				</div>
	  				<div class="popup_message_r item_cell_flex">
	  					<p class="popup_message_nick nickname">{$userinfo['nickname']}</p>
	  					<p class="popup_message_content">{$data['content']}</p>
	  					<p class="popup_message_time font_13px_999">{$time}</p>
	  				</div>
	  			</div>
div;

			Message::sendmsg($task['userid'],$task['puber'],$task['id'],$data['content'],$userinfo['nickname'],$type);
			Util::echoResult(200,'留言成功',array('str'=>$str));
		}
		Util::echoResult(201,'留言失败');


	// 回复留言
	}elseif($_GPC['op'] == 'replymessage'){

		$data['parent'] = intval( $_GPC['mid'] );
		$data['content'] = $_GPC['value'];

		if( empty( $data['content'] ) ) Util::echoResult(201,'请输入回复内容');

		$parent = pdo_get('zofui_tasktb_taskmessage',array('uniacid'=>$_W['uniacid'],'id'=>$data['parent']));
		if( empty( $parent ) ) Util::echoResult(201,'请重试');

		if( empty($parent['type']) ) 
			$task = pdo_get('zofui_tasktb_task',array('uniacid'=>$_W['uniacid'],'id'=>$parent['taskid']));
		if( $parent['type'] == 1 )
			$task = pdo_get('zofui_tasktb_tbtask',array('uniacid'=>$_W['uniacid'],'id'=>$parent['taskid']));
		
		if( empty( $task ) ) Util::echoResult(201,'任务不存在，不能再回复');
		if( $task['status'] != 0 ) Util::echoResult(201,'任务未上架，不能再回复');
		if( $task['end'] < TIMESTAMP ) Util::echoResult(201,'任务已结束，不能再回复');
		if( $task['iscount'] == 1 ) Util::echoResult(201,'任务已结束，不能再回复');			
		if( $task['userid'] != $userinfo['uid'] ) Util::echoResult(201,'您不能回复此留言');

		$data['uniacid'] = $_W['uniacid'];
		$data['openid'] = $userinfo['openid'];
		$data['userid'] = $userinfo['uid'];
		$data['taskid'] = $task['id'];
		$data['time'] = TIMESTAMP;
		$data['type'] = $parent['type'];

		$res = pdo_insert('zofui_tasktb_taskmessage',$data);

		if( $res ){
		// 给回复者发消息

			$str = <<<div
				<div class="item_cell_box reply_message_item">
					<li class="nickname">{$userinfo['nickname']}</li>
					<li class="reply_message_content item_cell_flex">{$data['content']}</li>
				</div>
div;
			Message::replymsg($parent['userid'],$parent['openid'],$task['id'],$data['content'],$task['title'],$userinfo['nickname'],$parent['type']);
			Util::echoResult(200,'回复成功',array('str'=>$str));
		}
		Util::echoResult(201,'回复失败');		

	// 投诉
	}elseif($_GPC['op'] == 'complain'){

		$data['taskid'] = intval( $_GPC['taskid'] );
		$data['content'] = $_GPC['content'];
		$data['images'] = iserializer( $_GPC['images'] );

		if( empty( $data['content'] ) ) Util::echoResult(201,'请输入投诉内容');

		$task = pdo_get('zofui_tasktb_task',array('uniacid'=>$_W['uniacid'],'id'=>$data['taskid']));
		if( empty( $task ) ) Util::echoResult(201,'任务不存在，不能投诉');
		if( $task['status'] != 0 ) Util::echoResult(201,'任务未上架，不能投诉');
		//if( $task['end'] < TIMESTAMP ) Util::echoResult(201,'任务已结束，不能再投诉');
		//if( $task['iscount'] == 1 ) Util::echoResult(201,'任务已结束，不能再投诉');			
		
		$iscan = model_task::isCanComplain( $data['taskid'] );
		if( $iscan != 200 ) Util::echoResult(201,$iscan);

		$data['uniacid'] = $_W['uniacid'];
		$data['openid'] = $userinfo['openid'];
		$data['userid'] = $userinfo['uid'];
		$data['time'] = TIMESTAMP;

		$res = pdo_insert('zofui_tasktb_complain',$data);

		if( $res ){
			Util::echoResult(200,'投诉成功');
		}
		Util::echoResult(201,'投诉失败');


	// 审核任务
	}elseif($_GPC['op'] == 'verifytask'){

		$taskid = intval( $_GPC['taskid'] );
		$data['closereason'] = $_GPC['value'];

		$task = pdo_get('zofui_tasktb_task',array('uniacid'=>$_W['uniacid'],'id'=>$taskid));
		if( empty( $task ) ) Util::echoResult(201,'任务不存在，不能审核');
		if( $task['status'] != 1 ) Util::echoResult(201,'任务不能审核');
		if( $task['end'] < TIMESTAMP ) Util::echoResult(201,'任务已结束，不能再审核');
		if( $task['iscount'] == 1 ) Util::echoResult(201,'任务已结束，不能再审核');			
		
		$canverify = 0;
		if( !empty( $_W['set']['admin'] ) ){
			$admin = iunserializer( $_W['set']['admin'] );
			if( is_array( $admin ) ){
				foreach ( $admin as $v ) {
					if( $v['userid'] == $userinfo['uid'] ){
						$canverify = 1;
						break;
					}
				}
			}
		}
		if( $canverify == 0 ) Util::echoResult(201,'任务不能被您审核');

		// 通过
		if( $_GPC['type'] == 1 ) {
			$data['status'] = 0;
			if( $task['continueid'] > 0 ){

				$all = pdo_getall('zofui_tasktb_task',array('uniacid'=>$_W['uniacid'],'continueid'=>$task['continueid'],'status'=>1));
				foreach ($all as $v) {
					$res = pdo_update('zofui_tasktb_task',$data,array('uniacid'=>$_W['uniacid'],'id'=>$v['id']));
					
					if( $res && $task['type'] == 0 ) { // 给上级奖励
						$user = model_user::getSingleUser( $v['userid'] );
						$upmoney = $v['costtop'] + $v['costserver'] + $v['costka'];
						model_task::pubGiveParent($this->module['config'],$user['id'],$user['parent'],$v['id'],1,$upmoney);
					}
				}

			}else{
				$res = pdo_update('zofui_tasktb_task',$data,array('uniacid'=>$_W['uniacid'],'id'=>$task['id']));

				if( $res ) { // 给上级奖励

					$user = model_user::getSingleUser( $task['userid'] );

					if( $task['type'] == 0 ) { // 普通任务
						$upmoney = $task['costtop'] + $task['costserver'] + $task['costka'];
						model_task::pubGiveParent($this->module['config'],$user['id'],$user['parent'],$task['id'],1,$upmoney);

					}elseif($task['type'] == 1){ // 试用任务

						$upmoney = $task['costtop'] + $task['costserver'] + $task['costfindkey'];
						model_task::pubGiveParent($this->module['config'],$user['id'],$user['parent'],$task['id'],2,$upmoney);
					}


				}

			}
		}

		// 不通过
		if( $_GPC['type'] == 2 ) {
			
			set_time_limit(0);
			$isback = empty($this->module['config']['isbacktm']) ? false : true;

			if( $task['continueid'] > 0 ){

				$all = pdo_getall('zofui_tasktb_task',array('uniacid'=>$_W['uniacid'],'continueid'=>$task['continueid'],'status'=>1));

				foreach ($all as $v) {
					if( $task['type'] == 0 ) {
						$res = model_task::countTask($v,$isback);
					}elseif($task['type'] == 1){
						$res = model_task::countUseTask($v,$isback);
					}
					if( $res ) {
						pdo_update('zofui_tasktb_task',array('status'=>0),array('uniacid'=>$_W['uniacid'],'id'=>$v['id']));
					}
				}
			}else{
				if( $task['type'] == 0 ) {
					$res = model_task::countTask($task,$isback);
				}elseif($task['type'] == 1){
					$res = model_task::countUseTask($task,$isback);
				}
				if( $res ) {
					pdo_update('zofui_tasktb_task',array('status'=>0),array('uniacid'=>$_W['uniacid'],'id'=>$task['id']));
				}
			}
		}
		
		
		if( $res ){
			// 发消息
			Message::verifytask($task['userid'],$task['puber'],$task['id'],$task['title'],$_GPC['type'],$data['closereason']);
			Util::echoResult(200,'审核成功');
		}
		Util::echoResult(201,'审核失败');

	// 注册
	}elseif($_GPC['op'] == 'regist'){

		$mobile = $_GPC['mobile'];
		$code = $_GPC['code'];
		
		//验证手机号码
		if( $_GPC['code'] != $_SESSION[ 'vertify'.$mobile ] ) Util::echoResult(201,'验证码不正确');
		$res = pdo_update('zofui_task_user',array('account'=>$mobile),array('uniacid'=>$_W['uniacid'],'id'=>$userinfo['id']));
		
		if( $res ){
			Util::deleteCache('u',$userinfo['uid']);
			Util::echoResult(200,'注册成功');
		}
		Util::echoResult(201,'注册失败');

	// 设置接收消息
	}elseif($_GPC['op'] == 'setmess'){

		
		$data = array(
			'uniacid' => $_W['uniacid'],
			'openid' => $userinfo['openid'],
			'userid' => $userinfo['uid'],
			'taked' => intval( $_GPC['taked'] ) == 2 ? 1 : 0,
			'messaged' => intval( $_GPC['messaged'] ) == 2 ? 1 : 0,
			'count' => intval( $_GPC['count'] ) == 2 ? 1 : 0,
			'reply' => intval( $_GPC['reply'] ) == 2 ? 1 : 0,
			'getmessage' => intval( $_GPC['getmessage'] ) == 2 ? 1 : 0,
			'getpri' => intval( $_GPC['getpri'] ) == 2 ? 1 : 0,
			'newdown' => intval( $_GPC['newdown'] ) == 2 ? 1 : 0,
			'downmoney' => intval( $_GPC['downmoney'] ) == 2 ? 1 : 0,
			'downact' => intval( $_GPC['downact'] ) == 2 ? 1 : 0,
			'usesuborder' => intval( $_GPC['usesuborder'] ) == 2 ? 1 : 0,
			'useaddcontent' => intval( $_GPC['useaddcontent'] ) == 2 ? 1 : 0,
			'newtask' => intval( $_GPC['newtask'] ) == 2 ? 1 : 0,
		);
		
		$isset = pdo_get('zofui_tasktb_mess',array('uniacid'=>$_W['uniacid'],'userid'=>$userinfo['uid']));
		if( empty( $isset ) ){
			$res = pdo_insert('zofui_tasktb_mess',$data);
		}else{
			$res = pdo_update('zofui_tasktb_mess',$data,array('id'=>$isset['id']));
		}

		if( $res ){
			Util::deleteCache('mess',$userinfo['openid']);
			Util::echoResult(200,'设置成功');
		}
		Util::echoResult(201,'设置失败');

	// 保存支付宝
	}elseif($_GPC['op'] == 'savealipay'){

		if( $_GPC['type'] == 1 ){
			$data['alipayname'] = $_GPC['name'];
			$data['alipay'] = $_GPC['account'];
			
			if( empty( $data['alipayname'] ) ) Util::echoResult(201,'请设置账户名称');
			if( empty( $data['alipay'] ) ) Util::echoResult(201,'请设置账户');

			// 限制
			if( !empty( $data['alipay'] ) && $_W['set']['ismali'] == 1 ){
				$isset = pdo_get('zofui_task_user',array('uniacid'=>$_W['uniacid'],'alipay'=>$data['alipay'],'id !='=>$userinfo['id']));
				if( !empty( $isset ) ) Util::echoResult(201,'支付宝收款账户已被别人使用了，请换一个');
			}

		}elseif( $_GPC['type'] == 2 ){
			$data['payqr'] = $_GPC['images'];
			if( empty( $data['payqr'] ) ) Util::echoResult(201,'请设置收款二维码');
		}

		$res = pdo_update('zofui_task_user',$data,array('uniacid'=>$_W['uniacid'],'id'=>$userinfo['id']));
		
		if( $res ){
			Util::deleteCache('u',$userinfo['uid']);
			Util::echoResult(200,'设置成功');
		}
		Util::echoResult(201,'设置失败');


	// 获取导报商品
	}elseif($_GPC['op'] == 'gettao'){

		$tao = model_taobao::getGood( $_GPC['url'] );
		
		if( $tao ){

			$_SESSION['taogood'] = $tao;
			Util::echoResult(200,'设置成功',$tao);
		}

		Util::echoResult(201,'查询商品失败，请输入正确的淘宝或天猫商品链接');


	// 发布试用任务
	}elseif($_GPC['op'] == 'addusetask'){
		$_GPC = Util::trimWithArray($_GPC);

		if( $_W['set']['isusetask'] == 0) Util::echoResult(220,'试用功能已关闭');
		//$gourl = $this->createMobileUrl('regist');
		//if( $_W['set']['ismobile'] == 1 && empty( $userinfo['account'] ) ) Util::echoResult(220,'请先注册您的手机号',array('url'=>$gourl));

		if( $_W['set']['ismobile'] == 1 && $_W['dev'] == 'wx' ) {
			load()->model('mc');
			$user = mc_fetch($userinfo['uid'], array('uniacid'=>$_W['uniacid']));
			if( empty( $user['mobile'] ) ){
				Util::echoResult(220,'请先绑定登录手机',array('url'=>Util::createModuleUrl('bindaccount')));
			}
		}

		// 是否已认证审核
		if( $_W['set']['isauth'] > 0 && ($userinfo['verifystatus'] == 0 || $userinfo['verifystatus'] == 1) ){
			$gourl = $this->createMobileUrl('set');
			if( $userinfo['verifystatus'] == 0 ) Util::echoResult(220,'请先提交资料认证',array('url'=>$gourl));
			if( $userinfo['verifystatus'] == 1 ) Util::echoResult(201,'您的认证还未审核，审核后才能发任务');
			if( $userinfo['verifystatus'] == 3 ) Util::echoResult(201,'您的认证未通过，不能发任务');
		}

		// 注册会员限制
		if( ($userinfo['level'] <= 0 || $userinfo['utime'] <= TIMESTAMP)  && $_W['set']['upubuse'] == 1 && $_W['set']['ulevel'] == 1 ){
			Util::echoResult(220,'升级会员后才可以发布任务',array('url'=>$this->createMobileUrl('level')));
		}

		$data['link'] = $_GPC['link']; // 淘宝商品链接
		$data['money'] = sprintf('%.2f',$_GPC['money']);// 返还赏金

		$data['content'] = $_GPC['content']; // 备注内容
		$data['images'] = iserializer( $_GPC['images'] ); // 备注图片
		
		$data['num'] = intval( $_GPC['num'] ); // 试用数量
		$data['paymoney'] = sprintf('%.2f',$_GPC['paymoney']); // 需支付金额，淘宝商品价格

		$data['sex'] = intval( $_GPC['sex'] ); // 性别
		$data['istop'] = intval( $_GPC['istop'] ); // 置顶

		$data['prizetype'] = intval( $_GPC['prizetype'] ); // 奖励类型 0赏金 ， 1实物
		$data['prizetitle'] = $_GPC['prizetitle'];
		$data['prizeimg'] = $_GPC['prizeimg'][0];

		$data['title'] = $_GPC['title']; // 任务标题
		$data['pic'] = $_GPC['gpic']; // 淘宝商品图片
		$data['gtitle'] = $_GPC['gtitle']; // 淘宝商品标题

		$data['findtype'] = intval( $_GPC['findtype'] );

		$data['isform'] = intval( $_GPC['isform'] );
		$data['address'] = $_GPC['address']; 
		
		if( $data['findtype'] == 1 && empty( $_GPC['findkey'] ) ) Util::echoResult(201,'请填写搜商品关键词');
		
		if( !empty( $_GPC['findkey'] ) ) {

			$data['findkey'] = array();
			foreach ($_GPC['findkey'] as $v) {
				$data['findkey'][] = array('name'=>$v);
			}

		}	


		// 验证
		if( empty( $data['link'] ) ) Util::echoResult(201,'请填写商品链接');
		//if( $data['link'] != $_SESSION['taogood']['taourl'] ) Util::echoResult(201,'提交的商品链接和淘宝(天猫)商品链接不一致');
		
		if( $data['money'] < $_W['set']['leastusemoney']*100/100 ) Util::echoResult(201,'返还金额不能小于'.$_W['set']['leastusemoney']*100/100 );
		if( $data['num'] <= 0 ) Util::echoResult(201,'试用数量必须大于0');
		if( $data['paymoney'] <= 0 ) Util::echoResult(201,'拍下价格必须大于0');
		if( empty( $data['title'] ) ) Util::echoResult(201,'请填写任务标题');

		$leastusemoney = $_W['set']['leastusemoney'];
		if( $data['prizetype'] == 0 && $data['money'] < $leastusemoney ){
			Util::echoResult(201,'返还赏金不能小于'.$leastusemoney);
		}

		if( $data['prizetype'] == 1 && ( empty($data['prizetitle']) || empty($data['prizeimg']) ) ){
			Util::echoResult(201,'请填写完整奖励物品信息');
		}

		// 口令
		if( $_W['set']['gotaobaotype'] == 1 ){

			if( $data['findtype'] == 0 ) { // 直接跳转

				$res = getTaoWord::getLink( $data['link'],$_GPC['gtitle'], tomedia($data['pic']) );
				if( $res['model'] ) {
					$data['taokey'] = $res['model'];
				}else{
					Util::echoResult(201,'生成口令失败:'.$res['sub_msg']);
				}

			}elseif( $data['findtype'] == 1 ){ // 关键词

				$taokey = array();
				foreach ($data['findkey'] as $k => $v) {

					preg_match('/&id=(\d+)/', $data['url'], $ida);
					$gid = $ida[1];
					if( empty( $gid ) ) {
						preg_match('/\?id=(\d+)/', $data['url'], $idaa);
						$gid = $idaa[1];
					}
					if( empty( $gid ) ){ // 
						preg_match('/itemId=(\d+)/', $data['url'], $idaaa);
						$gid = $idaaa[1];	
					}

					$key = rawurlencode( $v['name'] );
					$url = 'http://s.m.taobao.com/h5?q='.$key.'&nid='.$gid.'&closeP4P=true';

					if( !empty( $this->module['config']['usetourl'] ) ){
						$url = str_replace(array('{key}','{gid}'), array($key,$gid), $this->module['config']['usetourl']);
					}

					$res = getTaoWord::getLink( $url,$_GPC['gtitle'], tomedia($data['pic']) );
					if( $res['model'] ) {
						$data['findkey'][$k]['taokey'] = $res['model'];
					}else{
						Util::echoResult(201,'生成口令失败:'.$res['sub_msg']);
					}
				}
			}
		}
		$data['findkey'] = iserializer( $data['findkey'] );

		
		$per = 1;
		if( $_W['set']['moneytype'] == 1 && !empty( $_W['set']['creditper'] ) ){
			$per = $_W['set']['creditper'];
		}
		
		if( empty( $_W['set']['pubusep'] ) ) {
			// 算保证金
			$needdeposit = ( $data['money']/$per + $data['paymoney'] )*$data['num']; 
			if( $userinfo['deposit'] < $needdeposit ){
				Util::echoResult(210,'您的保证金不足，发布此任务需留存保证金'.$needdeposit.'元',array('url'=>Util::createModuleUrl('deposit',array('op'=>'in'))));
			}

			$beforetask = pdo_getall('zofui_tasktb_task',array('uniacid'=>$_W['uniacid'],'userid'=>$userinfo['uid'],'iscount'=>0,'type'=>1),array('money','paymoney','num'));

			if( !empty( $beforetask ) ){
				foreach ($beforetask as $v) {
					$needdeposit += ( $v['money']/$per + $v['paymoney'] )*$v['num'];
				}
				if( $userinfo['deposit'] < $needdeposit ){
					Util::echoResult(210,'您有还没结算的试用任务占用保证金，发布此任务需留存保证金'.$needdeposit.'元',array('url'=>Util::createModuleUrl('deposit',array('op'=>'in'))));
				}
			}
		}

		// 会员等级
		$level = model_user::levelRes($userinfo,$this->module['config']);
		if( $level == 1 ) {
			$_W['set']['usetaskserver'] = $_W['set']['usetaskservera'];
			$_W['set']['leastuseserver'] = $_W['set']['leastuseservera'];
			$_W['set']['usetopserver'] = $_W['set']['usetopservera'];
			$_W['set']['findkeyserver'] = $_W['set']['findkeyservera'];
		}
		if( $level == 2 ) {
			$_W['set']['usetaskserver'] = $_W['set']['usetaskserverb'];
			$_W['set']['leastuseserver'] = $_W['set']['leastuseserverb'];
			$_W['set']['usetopserver'] = $_W['set']['usetopserverb'];
			$_W['set']['findkeyserver'] = $_W['set']['findkeyserverb'];
		}
		
		// 算钱
		$taskmoney = $data['num']*$data['money'];
		$data['costserver'] = $taskmoney*$_W['set']['usetaskserver']/100;
		$data['costserver'] = max( $data['costserver'],$_W['set']['leastuseserver'] );


		$data['costtop'] = 0;
		if( $data['istop'] == 1 ) $data['costtop'] = sprintf('%.2f',$_W['set']['usetopserver']);

		$data['costfindkey'] = 0;
		if( $data['findtype'] == 1 && $_W['set']['findkeyserver'] > 0 ) $data['costfindkey'] = $_W['set']['findkeyserver'];

		$total = $taskmoney + $data['costserver'] + $data['costtop'] + $data['costfindkey'];
		
		$credit = model_user::getUserCredit( $userinfo['uid'] );
		if($credit['credit2'] < $total){
			Util::echoResult(210,'您的'.$_W['cname'].'不足',array('url'=>Util::createModuleUrl('money',array('op'=>'in'))));
		}
		
		// 扣钱
		$res = model_user::updateUserCredit($userinfo['uid'],-$total,2,1);
		// 资金记录
		model_money::insertMoneyLog($userinfo['openid'],-$total,1,19,$userinfo['uid']);
		

		if( $res ){
			$data['uniacid'] = $_W['uniacid'];
			$data['type'] = 1; // 试用任务
			$data['puber'] = $userinfo['openid'];
			$data['userid'] = $userinfo['uid'];
			$data['start'] = TIMESTAMP;

			$endtime = empty( $_W['set']['useontime'] ) ? 10 : $_W['set']['useontime'];
			$data['end'] = TIMESTAMP + $endtime*3600*24;
			$data['createtime'] = TIMESTAMP;
			
			$data['status'] = 0;
			$data['isstart'] = 0;
			if( $_W['set']['isverifytask'] == 1 ) $data['status'] = 1;
			
			$res = pdo_insert('zofui_tasktb_task',$data);
			$id = pdo_insertid();
			
			if( $res && $data['status'] == 0 ) {
				$upmoney = $data['costtop'] + $data['costserver'] + $data['costfindkey'];
				model_task::pubGiveParent($_W['set'],$userinfo['id'],$userinfo['parent'],$id,2,$upmoney);
			}

			// 管理员通知
			Message::addMessage(1,$id);
			// 新任务通知
			//model_task::setMessage( $_W['set'],$id );
			
			Util::echoResult(200,'发布成功',array('taskid'=>$id));
		}
		Util::echoResult(201,'发布失败');


	// 接试用任务
	}elseif($_GPC['op'] == 'applyusetask'){
		
		//$gourl = $this->createMobileUrl('regist');
		//if( $_W['set']['ismobile'] == 1 && empty( $userinfo['account'] ) ) Util::echoResult(220,'请先注册您的手机号',array('url'=>$gourl));

		if( $_W['set']['ismobile'] == 1 && $_W['dev'] == 'wx' ) {
			load()->model('mc');
			$user = mc_fetch($userinfo['uid'], array('uniacid'=>$_W['uniacid']));
			if( empty( $user['mobile'] ) ){
				Util::echoResult(220,'请先绑定登录手机',array('url'=>Util::createModuleUrl('bindaccount')));
			}
		}

		$id = intval( $_GPC['taskid'] );

		// 是否已认证审核
		if( $_W['set']['isauth'] == 2 && ($userinfo['verifystatus'] == 0 || $userinfo['verifystatus'] == 1) ){
			$gourl = $this->createMobileUrl('set');
			if( $userinfo['verifystatus'] == 0 ) Util::echoResult(220,'请先提交资料认证',array('url'=>$gourl));
			if( $userinfo['verifystatus'] == 1 ) Util::echoResult(201,'您的认证还未审核，审核后才能再接任务');
			if( $userinfo['verifystatus'] == 3 ) Util::echoResult(201,'您的认证未通过，不能接任务');
		}

		// 注册会员限制
		if( ($userinfo['level'] <= 0 || $userinfo['utime'] <= TIMESTAMP)  && $_W['set']['ugetuse'] == 1 && $_W['set']['ulevel'] == 1 ){
			Util::echoResult(220,'升级会员后才可以接任务',array('url'=>$this->createMobileUrl('level')));
		}

		$task = pdo_get('zofui_tasktb_task',array('uniacid'=>$_W['uniacid'],'type'=>1,'id'=>$id));
		if( empty( $task ) ) Util::echoResult(201,'没有找到任务');

		// 内容
		if( $task['isform'] == 1 && empty( $_GPC['content'] ) && empty( $_GPC['images'] ) ) {
			Util::echoResult(201,'请填写内容');
		}

		// 数量限制
		$limitnum = $limitday = 0;
		if( $_W['set']['uselimitnum'] > 0 ) $limitnum = $_W['set']['uselimitnum'];
		if( $_W['set']['uselimitday'] > 0 )  $limitday = $_W['set']['uselimitday'];

		if( empty( $task['userid'] ) ) {
			$pubuid = '';

		}else{
			$pubuid = $task['userid'];
			$puber = model_user::getSingleUser( $task['userid'] );
			if( $puber['uselimitday'] > 0 && $puber['uselimitnum'] > 0 ) {
				$limitnum = $puber['uselimitnum'];
				$limitday = $puber['uselimitday'];
			}
		}
		
		if( $limitnum > 0 ){

			$daytime = TIMESTAMP - $limitday*3600*24;

			$sql = " SELECT `id` FROM ".tablename('zofui_tasktb_usetasklog')." WHERE uniacid = :uniacid AND userid = :userid AND pubuid = :pubuid AND createtime >= :createtime GROUP BY `taskid` ";
			$allre = pdo_fetchall($sql,array(':uniacid'=>$_W['uniacid'],':userid'=>$userinfo['uid'],':pubuid'=>$pubuid,':createtime'=>$daytime));
			
			if( count($allre) >= $limitnum ) {
				Util::echoResult(201,'你已经接了此发布者发布的很多任务了，过段时间再来接。');
			}
			
		}



		// 先看缓存 防止高并发多接 ，这里的takednum在queue.class.php内定时改变回来
		$num = Util::getCache('takednum',$id);
		if( $num >= $task['num'] ){
			Util::echoResult(201,'任务已被接完',array($num));
		}
		Util::setCache('takednum',$id,$num+1);



		// 保证金
		if( $_W['set']['takeuserpo'] > 0 && $userinfo['deposit'] < $_W['set']['takeuserpo'] ) {
			$gourl = $this->createMobileUrl('deposit',array('op'=>'in'));
			$diff = $_W['set']['takeuserpo'] - $userinfo['deposit'];

			Util::echoResult(220,'账户必须留存'.$_W['set']['takeuserpo'].'保证金才能接任务，你还差'.$diff.'，请充值保证金后再来接任务',array('url'=>$gourl));
		}


		if( empty( $task ) ) Util::echoResult(201,'任务不存在');
		if( $task['status'] != 0 ) Util::echoResult(201,'任务未上架');
		if( $task['start'] > TIMESTAMP ) Util::echoResult(201,'任务未开始');
		if( $task['end'] < TIMESTAMP ) Util::echoResult(201,'任务已不能再申请试用');
		if( $task['iscount'] == 1 ) Util::echoResult(201,'任务已结束');

		if( $task['sex'] == 1 && !in_array($userinfo['sex'],array('1','3')) ) Util::echoResult(201,'此任务仅限男性可接');
		if( $task['sex'] == 2 && !in_array($userinfo['sex'],array('2','4')) ) Util::echoResult(201,'此任务仅限女性可接');

		if( $userinfo['activity'] <= 0 ) Util::echoResult(201,'您的活跃度不足，明天再来吧');

		// 已接过了
		$istaked = pdo_get('zofui_tasktb_usetasklog',array('uniacid'=>$_W['uniacid'],'userid'=>$userinfo['uid'],'taskid'=>$id));
		if( !empty( $istaked ) ) Util::echoResult(201,'您已申请过任务，不能再申请');

		// 已接完
		$takednum = Util::countDataNumber('zofui_tasktb_usetasklog',array('uniacid'=>$_W['uniacid'],'isactivity'=>0,'taskid'=>$id));
		if( $takednum >= $task['num'] ) Util::echoResult(201,'此任务已经没有申请名额了');

		$data = array(
			'uniacid' => $_W['uniacid'],
			'openid' => $userinfo['openid'],
			'userid' => $userinfo['uid'],
			'puber' => $task['puber'],
			'pubuid' => $task['userid'],
			'taskid' => $id,
			'createtime' => TIMESTAMP,
			'initcontent' => $task['isform'] ? iserializer( array('content'=>$_GPC['content'],'images'=> $_GPC['images'] ) ) : '',
		);

		$res = pdo_insert('zofui_tasktb_usetasklog',$data);

		if( $res ){
			if( $takednum + 1 >= $task['num'] ){ // 设置接完了
				pdo_update('zofui_tasktb_task',array('isempty'=>1),array('id'=>$task['id']));
			}
			// 发消息
			Message::getUsetask($task['userid'],$task['puber'],$task['id'],$task['title'],$userinfo['nickname']);
			Util::echoResult(200,'已申请，请等待雇主确认',array('url'=>$this->createMobileUrl('task',array('id'=>$id))));
		}
		Util::echoResult(201,'申请失败');

	// 放弃试用任务
	}elseif($_GPC['op'] == 'cancelusetask'){
		$id = intval( $_GPC['taskid'] );

		$task = pdo_get('zofui_tasktb_task',array('uniacid'=>$_W['uniacid'],'type'=>1,'id'=>$id));
		if( empty( $task ) ) Util::echoResult(201,'没有找到任务');

		$taked = pdo_get('zofui_tasktb_usetasklog',array('isactivity'=>0,'userid'=>$userinfo['uid'],'taskid'=>$id));

		if( empty( $taked ) ) Util::echoResult(201,'您还没申请试用任务');
		if( $taked['status'] != 0 && $taked['status'] != 1 ) Util::echoResult(201,'任务已不能放弃');

		$res = pdo_update('zofui_tasktb_usetasklog',array('status'=>3,'isactivity'=>1,'canceltime'=>TIMESTAMP),array('id'=>$taked['id']));

		if( $res ){
			if( $task['isempty'] == 1){
				pdo_update('zofui_tasktb_task',array('isempty'=>0),array('id'=>$task['id']));
			}
			Util::echoResult(200,'好',array('url'=>$this->createMobileUrl('task',array('id'=>$id))));
		}
		Util::echoResult(201,'放弃失败');		

	// 通过审核 或 拒绝 
	}elseif($_GPC['op'] == 'passorapply'){
		$id = intval( $_GPC['reid'] );

		$type = intval( $_GPC['type'] );

		$taked = pdo_get('zofui_tasktb_usetasklog',array('uniacid'=>$_W['uniacid'],'id'=>$id));

		$task = pdo_get('zofui_tasktb_task',array('uniacid'=>$_W['uniacid'],'type'=>1,'id'=>$taked['taskid']));
		if( empty( $task ) ) Util::echoResult(201,'没有找到任务');
		if( $task['userid'] != $userinfo['uid'] ) Util::echoResult(201,'您不能操作此申请');

		if( $taked['status'] != 0 ) Util::echoResult(201,'此申请不能被操作');

		if( $type == 1 ){ // 通过
			$res = model_task::passUseTask( $task,$taked );

		}elseif( $type == 2 ){ // 不通过
			$res = model_task::passfailUseTask( $task,$taked );
		}

		if( $res ){
			Util::echoResult(200,'好',array('url'=>$this->createMobileUrl('task',array('id'=>$id))));
		}
		Util::echoResult(201,'操作失败');

	// 提交订单 投诉
	}elseif($_GPC['op'] == 'replyusetask'){
		$id = intval( $_GPC['taskid'] );

		$task = pdo_get('zofui_tasktb_task',array('uniacid'=>$_W['uniacid'],'type'=>1,'id'=>$id));

		if( empty( $task ) ) Util::echoResult(201,'没有找到任务');
		if( $task['iscount'] == 1 ) Util::echoResult(201,'任务已结束不能再提交');

		$taked = pdo_get('zofui_tasktb_usetasklog',array('uniacid'=>$_W['uniacid'],'userid'=>$userinfo['uid'],'taskid'=>$task['id']));
		
		if( $taked['userid'] != $userinfo['uid'] ) Util::echoResult(201,'此申请不能被操作');

		if( empty( $_GPC['content'] ) ) Util::echoResult(201,'请填写提交内容');
		if( strlen( $_GPC['content'] ) > 200 ) Util::echoResult(201,'提交的内容太长了');

		
		if( $_GPC['type'] == 'sub' ){
			if( $taked['status'] != 1 ) Util::echoResult(201,'此申请不能被操作');
			if( !empty( $taked['subcontent'] ) || $taked['subtime'] > 0 ) Util::echoResult(201,'此申请不能被操作');

			$content = array('content'=>$_GPC['content'],'img'=>$_GPC['images']);
			$update = array('status'=>4,'subtime'=>TIMESTAMP,'subcontent'=>iserializer( $content ));
			$res = pdo_update('zofui_tasktb_usetasklog', $update ,array('id'=>$taked['id']));
			
			if( $res ){
				// 发消息
				Message::subOrder($task['userid'],$task['puber'],$task['id'],$task['title'],$userinfo['nickname'],$_GPC['content']);
			}

		}elseif( $_GPC['type'] == 'add' ){

			$addnum = Util::countDataNumber('zofui_tasktb_useaddcontent',array('takedid'=>$taked['id'],'type'=>0,'createtime>'=> TIMESTAMP- 60*10 ));
			if( $addnum > 0 ) Util::echoResult(201,'每10分钟只能补充一次，最近10分钟您已补充过，稍等会再补充');

			$data = array(
				'uniacid' => $_W['uniacid'],
				'takedid' => $taked['id'],
				'taskid' => $task['id'],
				'content' => $_GPC['content'],
				'img' => iserializer( $_GPC['images'] ),
				'createtime' => TIMESTAMP,                                                                                                                                          
			);
			$res = pdo_insert('zofui_tasktb_useaddcontent',$data);
			if( $res ){
				// 发消息
				Message::useAddContent($task['userid'],$task['puber'],$task['id'],$task['title'],$userinfo['nickname'],$_GPC['content']);
			}
		}else if( $_GPC['type'] == 'complain' ){ // 投诉

			if( $taked['status'] != 6 ) Util::echoResult(201,'此申请不能被操作');
			$data = array(
				'uniacid' => $_W['uniacid'],
				'content' => $_GPC['content'],
				'images' => iserializer( $_GPC['images'] ),
				'time' => TIMESTAMP,
				'openid' => $userinfo['openid'],
				'userid' => $userinfo['uid'],
				'taskid' => $task['id'],
			);
			$res = pdo_insert('zofui_tasktb_complain',$data);
			if( $res ) {
				pdo_update('zofui_tasktb_usetasklog',array('iscomplained'=>1),array('id'=>$taked['id']));
			}
		}

		if( $res ){
			Util::echoResult(200,'已提交',array('url'=>$this->createMobileUrl('task',array('id'=>$id))));
		}
		Util::echoResult(201,'提交失败');		


	// 任务失败
	}elseif($_GPC['op'] == 'failusetask'){
		$id = intval( $_GPC['reid'] );

		$taked = pdo_get('zofui_tasktb_usetasklog',array('uniacid'=>$_W['uniacid'],'id'=>$id));

		$task = pdo_get('zofui_tasktb_task',array('uniacid'=>$_W['uniacid'],'type'=>1,'id'=>$taked['taskid']));
		if( empty( $task ) ) Util::echoResult(201,'没有找到任务');
		if( $task['userid'] != $userinfo['uid'] ) Util::echoResult(201,'您不能操作此申请');

		if( $taked['status'] != 4 ) Util::echoResult(201,'此申请不能被操作');
		if( empty( $_GPC['value'] ) ) Util::echoResult(201,'请填写失败原因');

		$res = model_task::failUseTask( $taked,$_GPC['value'],$task );

		if( $res ){

			Util::echoResult(200,'好',array('url'=>$this->createMobileUrl('task',array('id'=>$id))));
		}
		Util::echoResult(201,'操作失败');

	// 完成任务
	}elseif($_GPC['op'] == 'successusetask'){
		$id = intval( $_GPC['reid'] );

		$type = intval( $_GPC['type'] );

		$taked = pdo_get('zofui_tasktb_usetasklog',array('uniacid'=>$_W['uniacid'],'id'=>$id));

		$task = pdo_get('zofui_tasktb_task',array('uniacid'=>$_W['uniacid'],'type'=>1,'id'=>$taked['taskid']));
		if( empty( $task ) ) Util::echoResult(201,'没有找到任务');
		if( $task['userid'] != $userinfo['uid'] ) Util::echoResult(201,'您不能操作此申请');

		if( $type == 1 && $taked['status'] != 4 ) Util::echoResult(201,'此申请不能被操作');
		if( $type == 2 && $taked['status'] != 6 ) Util::echoResult(201,'此申请不能被操作');

		if( $task['iscount'] == 1 ) Util::echoResult(201,'任务已结算，不能再操作');

		if( $type == 1 ){ // 完成

			$res = model_task::sucUseTask($_W['set'],$taked,$task,1);
		}elseif( $type == 2 ){ // 转为完成

			$res = model_task::sucUseTask($_W['set'],$taked,$task,2);
		}

		if( $res ){

			Util::echoResult(200,'好',array('url'=>$this->createMobileUrl('task',array('id'=>$id))));
		}
		Util::echoResult(201,'操作失败');

	// 提醒对方
	}elseif($_GPC['op'] == 'noticeusetask'){
		$id = intval( $_GPC['reid'] );

		$taked = pdo_get('zofui_tasktb_usetasklog',array('uniacid'=>$_W['uniacid'],'id'=>$id));

		$task = pdo_get('zofui_tasktb_task',array('uniacid'=>$_W['uniacid'],'type'=>1,'id'=>$taked['taskid']));
		if( empty( $task ) ) Util::echoResult(201,'没有找到任务');
		if( $task['userid'] != $userinfo['uid'] ) Util::echoResult(201,'您不能操作此申请');

		if( $task['iscount'] == 1 ) Util::echoResult(201,'任务已结算，不能再操作');

		if( empty( $_GPC['value'] ) ) Util::echoResult(201,'请填写提醒内容');

		$noticenum = Util::countDataNumber('zofui_tasktb_useaddcontent',array('takedid'=>$taked['id'],'type'=>1,'createtime>'=> TIMESTAMP- 60*10 ));
		if( $noticenum > 0 ) Util::echoResult(201,'每10分钟只能提醒一次，最近10分钟您已提醒过，稍等会再提醒');

		$res = model_task::noticeUsetask( $taked,$task,$_GPC['value'] );
		if( $res ){
			Util::echoResult(200,'好',array('url'=>$this->createMobileUrl('task',array('id'=>$id))));
		}
		Util::echoResult(201,'操作失败');


	// 结算试用任务
	}elseif($_GPC['op'] == 'countusetask'){
		$id = intval( $_GPC['taskid'] );

		$task = pdo_get('zofui_tasktb_task',array('uniacid'=>$_W['uniacid'],'type'=>1,'id'=>$id));
		if( empty( $task ) ) Util::echoResult(201,'没有找到任务');
		if( $task['userid'] != $userinfo['uid'] ) Util::echoResult(201,'您不能操作此任务');

		if( $task['iscount'] == 1 ) Util::echoResult(201,'任务已结算，不能再结算');

		// 是否有还未提交订单号的
		$isset1 = pdo_get('zofui_tasktb_usetasklog',array('uniacid'=>$_W['uniacid'],'taskid'=>$task['id'],'status'=>1));
		if( !empty( $isset1 ) ) Util::echoResult(201,'还存在试用没有提交订单内容，请等待处理完再结算');

		// 是否有已提交订单号未处理的
		$isset2 = pdo_get('zofui_tasktb_usetasklog',array('uniacid'=>$_W['uniacid'],'taskid'=>$task['id'],'status'=>4));
		if( !empty( $isset2 ) ) Util::echoResult(201,'还存在提交了订单内容，但您还未处理的试用，请处理完再结算');

		// 是否在结算期时间之内有设为失败的试用
		$waitday = $_W['set']['usecounttime'] > 0 ? $_W['set']['usecounttime'] : 3;
		$failnum = Util::countDataNumber('zofui_tasktb_usetasklog',array('status'=>6,'taskid'=>$task['id'],'failtime>'=> TIMESTAMP - $waitday*3600*24 ));
		if( $failnum > 0 ) Util::echoResult(201,'此任务在近'.$waitday.'天内存在失败的申请，暂不能自主结算');

		$counting = Util::getCache('counttask',$task['id']);
		if( is_array( $counting ) && $counting['status'] == 1 ) {
			Util::echoResult(200,'此任务正在被处理中，请重试');
		}
		Util::setCache('counttask',$task['id'],array('status'=>1));

		$res = model_task::countUseTask( $task );

		Util::deleteCache( 'counttask',$task['id'] );
		if( $res ) 
			Util::echoResult(200,'成功结算任务',array('url'=>$this->createMobileUrl('task',array('id'=>$task['id']))));
		Util::echoResult(201,'结算失败');


	}elseif($_GPC['op'] == 'addcontenttask'){	

		$takedid = intval( $_GPC['takedid'] );
		$taked = pdo_get('zofui_tasktb_taked',array('uniacid'=>$_W['uniacid'],'userid'=>$userinfo['uid'],'id'=>$takedid));
		if( empty( $taked ) ) Util::echoResult(201,'您还没接任务');
		if( $taked['status'] != 1 ) Util::echoResult(201,'您已经不能再补充内容了');

		$task = pdo_get('zofui_tasktb_task',array('uniacid'=>$_W['uniacid'],'id'=>$taked['taskid']));
		if( empty( $task ) ) Util::echoResult(201,'没有找到任务');
		if( $task['iscount'] == 1 ) Util::echoResult(201,'任务已结束不能再提交');

		
		if( $taked['userid'] != $userinfo['uid'] ) Util::echoResult(201,'此回复不能被操作');

		if( empty( $_GPC['content'] ) && empty( $_GPC['images'] ) ) Util::echoResult(201,'请填写提交内容');
		
		// 时间限制
		$added = Util::countDataNumber('zofui_tasktb_remindlog',array( 'takedid'=>$taked['id'],'mtype'=>1,'createtime>'=>(TIMESTAMP - 60*1) ));
		if( $added > 0 ) Util::echoResult(201,'你已提交过补充内容（1分钟内只能补充1次）');

		$data = array(
			'uniacid' => $_W['uniacid'],
			'createtime' => TIMESTAMP,
			'takedid' => $takedid,
			'content' => $_GPC['content'],
			'images' => iserializer( $_GPC['images'] ),
			'mtype' => 1,
		);
		pdo_insert('zofui_tasktb_remindlog',$data);

		Util::echoResult(200,'已提交补充');

	}elseif( $_GPC['op'] == 'getmyposter' ){

		$appacid = intval($_GPC['appacid']);

		$res = model_poster::getPoster($userinfo,$appacid);
		
		if( $res['status'] == 200 ) {
			Util::echoResult(200,'好',array('url'=>$res['res']['url']));
		}
		Util::echoResult(201,$res['res']);


	// 保存步骤
	}elseif( $_GPC['op'] == 'savestep' ){

		if( empty( $_GPC['step'] ) ) Util::echoResult(201,'请填写完整的步骤内容');

		$k = 1;
		$nullnum = 0;
		$steparr = array();
		foreach ( $_GPC['step'] as $v ) {
			//if( empty( $v ) ) Util::echoResult(201,'请填写完整的步骤内容');
			if( empty( $v ) ) {
				$steparr[] = array('ischecked'=>0,'name'=>'','step'=>$k);
				$nullnum ++;
			}else{
				$steparr[] = array('ischecked'=>1,'name'=>$v,'step'=>$k);
			}
			$k++;
		}
		if( $nullnum >= 5 ) Util::echoResult(201,'至少需要一个步骤');

		$data = array(
			'uniacid' => $_W['uniacid'],
			'openid' => $userinfo['openid'],
			'userid' => $userinfo['uid'],
			'step' => iserializer( $steparr ),
		);

		$step = pdo_get('zofui_tasktb_tbtaskstep',array('userid'=>$userinfo['uid'],'uniacid'=>$_W['uniacid']));

		if( empty( $step ) ) {
			pdo_insert('zofui_tasktb_tbtaskstep',$data);
		}else{
			pdo_update('zofui_tasktb_tbtaskstep',$data,array('id'=>$step['id']));
		}
		Util::echoResult(200,'好');


	///////////////////
	// 发布担保任务
	}elseif( $_GPC['op'] == 'addtbtask' ){
		$_GPC = Util::trimWithArray($_GPC);

		//if( $_W['set']['ismobile'] == 1 && empty( $userinfo['account'] ) ) Util::echoResult(220,'请先注册您的手机号');

		if( $_W['set']['ismobile'] == 1 && $_W['dev'] == 'wx' ) {
			load()->model('mc');
			$user = mc_fetch($userinfo['uid'], array('uniacid'=>$_W['uniacid']));
			if( empty( $user['mobile'] ) ){
				Util::echoResult(220,'请先绑定登录手机',array('url'=>Util::createModuleUrl('bindaccount')));
			}
		}
		
		// 是否已认证审核
		if( $_W['set']['isauth'] > 0 && ($userinfo['verifystatus'] == 0 || $userinfo['verifystatus'] == 1) ){
			$gourl = $this->createMobileUrl('set');
			if( $userinfo['verifystatus'] == 0 ) Util::echoResult(230,'请先提交资料认证',array('url'=>$gourl));
			if( $userinfo['verifystatus'] == 1 ) Util::echoResult(201,'您的认证还未审核，审核后才能发任务');
			if( $userinfo['verifystatus'] == 3 ) Util::echoResult(201,'您的认证未通过，不能发任务');
		}

		// 注册会员限制
		if( ($userinfo['level'] <= 0 || $userinfo['utime'] <= TIMESTAMP)  && $_W['set']['upubtb'] == 1 && $_W['set']['ulevel'] == 1 ){
			Util::echoResult(220,'升级会员后才可以发任务',array('url'=>$this->createMobileUrl('level')));
		}

		$data['title'] = $_GPC['title'];
		$data['content'] = $_GPC['content'];
		$data['hidecontent'] = $_GPC['hidecontent'];
		$data['images'] = $_GPC['images'];
		$data['hideimages'] = $_GPC['hideimages'];
		$data['gid'] = $_GPC['gid'];

		$data['num'] = intval( $_GPC['num'] );
		$data['money'] = sprintf('%2.f',$_GPC['money']);
		$data['tbmoney'] = sprintf('%2.f',$_GPC['tbmoney']);

		$data['limitnum'] = intval( $_GPC['limitnum'] );
		$data['sex'] = intval( $_GPC['sex'] );

		$data['iska'] = intval( $_GPC['iska'] );
		$data['kagoodid'] = $_GPC['kagood'];
		$data['kakey'] = $_GPC['kakey'];
		$data['tkl'] = $_GPC['tkl'];
		$data['address'] = $_GPC['address'];
		$data['istop'] = intval( $_GPC['istop'] );
		$data['skiptype'] = intval( $_GPC['skiptype'] );

		$data['isarealimit'] = intval( $_GPC['isarealimit'] );
		if( $data['isarealimit'] > 0 ){
			if( empty( $_GPC['area'] ) ) Util::echoResult(201,'请选择可接任务的区域');
			$list = explode(',',$_GPC['area']);
			$data['province'] = $list[0];
			$data['city'] = $list[1];
			$data['country'] = $list[2];
		}

		if( $data['money'] <= 0 ) Util::echoResult(201,'赏金不能小于等于0');

		if( $_W['set']['maxtbmoney'] > 0 && $data['tbmoney'] > $_W['set']['maxtbmoney'] ) 
			Util::echoResult(201,'担保金额最大'.$_W['set']['maxtbmoney']);

		if( $_W['set']['mintbmoney'] > 0 && $data['tbmoney'] < $_W['set']['mintbmoney'] ) 
			Util::echoResult(201,'担保金额最小'.$_W['set']['mintbmoney']);		

		$step = array();
		foreach ( $_GPC['step'] as $k => $v ) {
			$key = $v - 1;
			$step[] = array( 'step'=>$v,'name'=>$_GPC['stepname'][$key] );
			if( empty( $_GPC['stepname'][$key] ) ) Util::echoResult(201,'勾选的任务步骤必须填写名称');
		}
		if( empty( $_GPC['step'] ) ){
			Util::echoResult(201,'至少选择一项任务步骤');
		}else{
			
			$mystep = pdo_get('zofui_tasktb_tbtaskstep',array('userid'=>$userinfo['uid'],'uniacid'=>$_W['uniacid']));
			if( !empty( $step ) ) {
				$mysteparr = iunserializer( $mystep['step'] );
				if( !empty( $mysteparr ) ) {
					foreach ( $mysteparr as &$v ) {
						$in = 0;
						foreach ( $step as $vv ) {
							if( $vv['step'] == $v['step'] ) {
								$in = 1;break;
							}
						}
						$v['ischecked'] = 0;
						if( $in ) $v['ischecked'] = 1;
					}
				}
				
				$mysteparr = iserializer( $mysteparr );
				pdo_update('zofui_tasktb_tbtaskstep',array('step'=>$mysteparr),array('id'=>$mystep['id']));
			}

		}
		$data['step'] = iserializer( $step );


		if( $data['iska'] == 1 && $_W['set']['tbkatype'] == 1 ){
			
			$kakey = array();
			foreach ((array)$data['kakey'] as $v) {
				$tokey = rawurlencode( $v );

				$url = 'https://s.m.taobao.com/h5?q='.$tokey.'&nid='.$data['kagoodid'].'&closeP4P=true';

				//$url = 'https://s.m.taobao.com/h5?q=';

				if( !empty( $this->module['config']['tbtourl'] ) ){
					$url = str_replace(array('{key}','{gid}'), array($tokey,$data['kagoodid']), $this->module['config']['tbtourl']);
				}

				$res = getTaoWord::getLink( $url , $tokey );
				
				if( $res['model'] ){
					$kakeytemp = array();
					$kakeytemp['key'] = $v;
					$kakeytemp['tao'] = $res['model'];
					$kakey[] = $kakeytemp;
				}else{
					Util::echoResult(201,'生成口令失败:'.$res['sub_msg']);
				}
			}
			$data['kakey'] = iserializer( $kakey );
		}else{
			$data['kakey'] = iserializer( $data['kakey'] );
		}


		if( !empty( $_GPC['linkname'] ) ){
			$link = array();
			foreach ($_GPC['linkname'] as $k => $v) {
				$linkitem['text'] = $v;
				$linkitem['url'] = $_GPC['linkurl'][$k];
				$link[] =  $linkitem;
			}
			$data['link'] = iserializer( $link );
		}
		
		// 会员等级
		$level = model_user::levelRes($userinfo,$this->module['config']);
		if( $level == 1 ) {
			$_W['set']['tbpubserver'] = $_W['set']['tbpubservera'];
			$_W['set']['tbtopserver'] = $_W['set']['tbtopservera'];
			$_W['set']['tbkaserver'] = $_W['set']['tbkaservera'];
			$_W['set']['tbminmoney'] = $_W['set']['tbminmoneya'];
			$_W['set']['tbpubneedposit'] = $_W['set']['tbpubneedposita'];
		}
		if( $level == 2 ) {
			$_W['set']['tbpubserver'] = $_W['set']['tbpubserverb'];
			$_W['set']['tbtopserver'] = $_W['set']['tbtopserverb'];
			$_W['set']['tbkaserver'] = $_W['set']['tbkaserverb'];
			$_W['set']['tbminmoney'] = $_W['set']['tbminmoneyb'];
			$_W['set']['tbpubneedposit'] = $_W['set']['tbpubneedpositb'];
		}
		
		if( $data['money'] < $_W['set']['tbminmoney'] )
			Util::echoResult(201,'任务赏金至少'.$_W['set']['tbminmoney'].'元');
		
		
		// 算钱
		$server = $_W['set']['tbpubserver'];
			
		$ka = 0; // 卡首屏的钱
		if( $data['iska'] == 1 ) $ka = sprintf('%.2f',$_W['set']['tbkaserver']);

		$top = 0;
		$data['topendtime'] = 0;
		if( $_GPC['istop'] == 1 ) {
			$toptime = intval( $_GPC['toptime'] );
			if( $toptime <= 0 ) Util::echoResult(201,'置顶时间必须大于0');

			$top = $_W['set']['tbtopserver']*$toptime;
			$data['topendtime'] = TIMESTAMP + $toptime*3600;
		}

		$total = $server + $ka + $top;

		if( $total < 0 ) Util::echoResult(201,'发布任务资金异常');

		$credit = model_user::getUserCredit( $userinfo['uid'] );
		if($credit['credit2'] < $total){
			Util::echoResult(210,'您的余额不足',array('url'=>Util::createModuleUrl('money',array('op'=>'in'))));
		}
		
		// 保证金
		if( $_W['set']['tbpubneedposit'] > 0 && $userinfo['deposit'] < $_W['set']['tbpubneedposit'] ){
			Util::echoResult(210,'您的保证金不足，保证金不小于'.$_W['set']['tbpubneedposit'].'才能发布任务',array('url'=>Util::createModuleUrl('deposit',array('op'=>'in'))));
		}

		// 扣钱
		$res = model_user::updateUserCredit($userinfo['uid'],-$total,2,1);
		// 资金记录
		model_money::insertMoneyLog($userinfo['openid'],-$total,1,27,$userinfo['uid']);
		
		if( $res ){
			$data['uniacid'] = $_W['uniacid'];
			$data['puber'] = $userinfo['openid'];
			$data['userid'] = $userinfo['uid'];
			$data['start'] = TIMESTAMP;
			$data['createtime'] = TIMESTAMP;

			// 自动结束时间，单位/h
			$time = $_W['set']['tbtasktime'] > 0 ? $_W['set']['tbtasktime'] : 72;
			
			$data['end'] = TIMESTAMP + $time*3600;
			$data['status'] = 0;
			
			if( $_W['set']['isverifytask'] == 1 ) $data['status'] = 1;
			
			$data['images'] = iserializer( $data['images'] );
			$data['hideimages'] = iserializer( $data['hideimages'] );
			$data['costserver'] = $server;
			$data['costka'] = $ka;
			$data['costtop'] = $top;

			$res = pdo_insert('zofui_tasktb_tbtask',$data);
			$id = pdo_insertid();
			
			if( $res && $data['status'] == 0 ) { // 上级提成
				$upmoney = $data['costtop'] + $data['costserver'] + $data['costka'];
				model_task::pubGiveParent($_W['set'],$userinfo['id'],$userinfo['parent'],$id,3,$upmoney);
			}

			// 管理员通知
			//Message::addMessage(1,$id);
			
			Util::echoResult(200,'发布成功',array('taskid'=>$id));
		}
		Util::echoResult(201,'发布失败');

	// 结算担保任务
	}elseif( $_GPC['op'] == 'counttbtask' ){

		$task = model_tbtask::getTask( $_GPC['taskid'] );
		if( empty( $task ) ) Util::echoResult(201,'未找到任务');
		if( $task['userid'] != $userinfo['uid'] ) Util::echoResult(201,'您不是任务的发布者');
		
		if( $task['iscount'] == 1 ) Util::echoResult(201,'任务已经结算，不能再结算');
		
		$alltaked = pdo_getall('zofui_tasktb_tbtaked',array('uniacid'=>$_W['uniacid'],'taskid'=>$task['id']),array('status'));
		
		if( !empty( $alltaked ) ) {
			foreach ( $alltaked as $v ) {
				if( $v['status'] == 2 ) Util::echoResult(201,'还有任务正在执行中，不能结算');
				if( $v['status'] == 4 ) Util::echoResult(201,'还有任务待雇员确认，不能结算');
				if( $v['status'] == 6 || $v['status'] == 7 ) Util::echoResult(201,'还有任务申诉中，不能结算');
			}
		}

		model_tbtask::countTbtask( $task );

		Util::echoResult(200,'已提交结算');

	// 追加任务
	}elseif( $_GPC['op'] == 'subaddtbtask' ){

		$num = intval( $_GPC['value'] );
		if( $num <= 0 ) Util::echoResult(201,'请填写数字');

		$task = model_tbtask::getTask( $_GPC['taskid'] );
		if( empty( $task ) ) Util::echoResult(201,'未找到任务');
		if( $task['userid'] != $userinfo['uid'] ) Util::echoResult(201,'您不是任务的发布者');
		
		if( $task['iscount'] == 1 || $task['end'] <= TIMESTAMP ) Util::echoResult(201,'任务已经结束，不能再追加');
			
		$res = Util::addOrMinusOrUpdateData('zofui_tasktb_tbtask',array('num'=>$num),$task['id']);

		Util::deleteCache('tbtask',$task['id']);

		Util::echoResult(200,'已追加任务数量');

	// 追加任务置顶时间
	}elseif( $_GPC['op'] == 'subaddtasktoptime' ){

		$num = intval( $_GPC['value'] );
		if( $num <= 0 ) Util::echoResult(201,'请填写时间');

		$task = model_tbtask::getTask( $_GPC['taskid'] );
		if( empty( $task ) ) Util::echoResult(201,'未找到任务');
		if( $task['userid'] != $userinfo['uid'] ) Util::echoResult(201,'您不是任务的发布者');
		
		if( $task['iscount'] == 1 || $task['end'] <= TIMESTAMP ) Util::echoResult(201,'任务已经结束，不能再追加');
		
		$toptime = model_tbtask::countTopTime( $task );
		if( $num > $toptime['canadd'] ) Util::echoResult(201,'最多能追加置顶'.$toptime['canadd'].'小时');

		$money = $num*$_W['set']['tbtopserver'];
		if( $money > 0 ) {
			$credit = model_user::getUserCredit( $userinfo['uid'] );

			if( $credit['credit2'] < $money )
				Util::echoResult(210,'您的余额不足,需'.$money.'余额,您还差'. ($money - $credit['credit2']),array('url'=>$this->createMobileUrl('money',array('op'=>'in'))));

			// 扣钱
			$res = model_user::updateUserCredit($userinfo['uid'],-$money,2,1);
			// 资金记录
			model_money::insertMoneyLog($userinfo['openid'],-$money,1,25,$userinfo['uid']);
		}else{
			$res = true;
		}

		if( $res ){
			$time = $task['topendtime'] + $num*3600;
			if( $task['topendtime'] <= 0 ) $time = TIMESTAMP + $num*3600;

			pdo_update('zofui_tasktb_tbtask',array('topendtime'=>$time),array('id'=>$task['id']));
			
			Util::deleteCache('tbtask',$task['id']);
			Util::echoResult(200,'已追加置顶任务');
		}
		
		Util::echoResult(201,'追加失败');

	// 接担保任务
	}elseif( $_GPC['op'] == 'taketbtask' ){

		//if( $_W['set']['ismobile'] == 1 && empty( $userinfo['account'] ) ) Util::echoResult(220,'请先注册您的手机号');

		if( $_W['set']['ismobile'] == 1 && $_W['dev'] == 'wx' ) {
			load()->model('mc');
			$user = mc_fetch($userinfo['uid'], array('uniacid'=>$_W['uniacid']));
			if( empty( $user['mobile'] ) ){
				Util::echoResult(220,'请先绑定登录手机',array('url'=>Util::createModuleUrl('bindaccount')));
			}
		}

		// 是否已认证审核
		if( $_W['set']['isauth'] == 2 && ($userinfo['verifystatus'] == 0 || $userinfo['verifystatus'] == 1) ){
			$gourl = $this->createMobileUrl('set');
			if( $userinfo['verifystatus'] == 0 ) Util::echoResult(230,'请先提交资料认证',array('url'=>$gourl));
			if( $userinfo['verifystatus'] == 1 ) Util::echoResult(201,'您的认证还未审核，审核后才能再接任务');
			if( $userinfo['verifystatus'] == 3 ) Util::echoResult(201,'您的认证未通过，不能接任务');
		}
		
		// 注册会员限制
		if( ($userinfo['level'] <= 0 || $userinfo['utime'] <= TIMESTAMP)  && $_W['set']['ugettb'] == 1 && $_W['set']['ulevel'] == 1 ){
			Util::echoResult(220,'升级会员后才可以接任务',array('url'=>$this->createMobileUrl('level')));
		}

		// 会员等级
		$level = model_user::levelRes($userinfo,$this->module['config']);
		if( $level == 1 ) {
			$_W['set']['tbtakeneedposit'] = $_W['set']['tbtakeneedposita'];
		}
		if( $level == 2 ) {
			$_W['set']['tbtakeneedposit'] = $_W['set']['tbtakeneedpositb'];
		}
		
		if( $_W['set']['tbtakeneedposit'] > 0 ) {
			if( $userinfo['deposit'] < $_W['set']['tbtakeneedposit'] ) {
				Util::echoResult(201,'账户需留存'.$_W['set']['tbtakeneedposit'].'保证金才能接任务',array('url'=>$this->createMobileUrl('deposit',array('op'=>'in'))));
				
			}
		}

		$id = intval( $_GPC['taskid'] );
		$tbtask = model_tbtask::getTask( $id );

		// 区域限制
		if( $tbtask['isarealimit'] > 0 ){
			$res = Util::checkLocation($tbtask['isarealimit'],$tbtask['province'],$tbtask['city'],$tbtask['country']);
			if( !$res ) Util::echoResult(211,'您所在地区不能接此任务');
		}


		// 先看缓存 防止高并发多接 ，这里的takednum在queue.class.php内定时改变回来
		$num = Util::getCache('tbtakednum',$id);
		if( $num >= $tbtask['num'] ){
			Util::echoResult(201,'任务已被接完',array($num));
		}
		Util::setCache('tbtakednum',$id,$num+1);

		$taskstatus = model_tbtask::taskStatus( $tbtask );
		if( $taskstatus['status'] == 201 ) Util::echoResult(201,$taskstatus['res']);
		
		if( $tbtask['end'] <= TIMESTAMP ) Util::echoResult(201,'任务已进入结算期，不能再接');

		$alltaked = pdo_getall('zofui_tasktb_tbtaked',array('uniacid'=>$_W['uniacid'],'userid'=>$userinfo['uid'],'taskid'=>$tbtask['id']));
		$mystatus = model_tbtask::getMyStatusByTbtask( $tbtask,$alltaked,$userinfo['uid'],$userinfo['sex'] );
		
		if( $mystatus['status'] != 200 ) Util::echoResult(201,$mystatus['res']);
		
		if( $userinfo['activity'] <= 0 ) Util::echoResult(201,'您的疲劳值不足，明天再来吧');

		if( !empty($tbtask['gid']) ){
			$ismore = pdo_get('zofui_tasktb_tbtaked',array('uniacid'=>$_W['uniacid'],'gid'=>$tbtask['gid']));
			if( !empty($ismore) ){
				Util::echoResult(201,'此类任务你已经接过了，不能再接');
			}
		}

		//  黑名单
		$isblack = pdo_get('zofui_tasktb_tbblack',array('userid'=>$tbtask['userid'],'targetuid'=>$userinfo['uid'],'uniacid'=>$_W['uniacid']));
		if( !empty( $isblack ) ) Util::echoResult(201,'您不能接他的任务');

		$data = array(
			'uniacid' => $_W['uniacid'],
			'openid' => $userinfo['openid'],
			'userid' => $userinfo['uid'],
			'createtime' => TIMESTAMP,
			'puber' => $tbtask['puber'],
			'pubuid' => $tbtask['userid'],
			'taskid' => $id,
			'takecontent' => iserializer( array('images'=>$_GPC['images'],'content'=>$_GPC['content']) ),
		);
		
		$res = pdo_insert('zofui_tasktb_tbtaked',$data);
		
		if( $res ){
			Util::addOrMinusOrUpdateData('zofui_task_user',array('activity'=>-1),$userinfo['id']);
			Util::deleteCache( 'u',$userinfo['uid'] );

			Message::takeTbtask($tbtask['userid'],$tbtask['puber'],$tbtask['id'],$tbtask['title'],$tbtask['money'],$tbtask['tbmoney']);
	 		Util::echoResult(200,'已接到任务，等待审核');		
		}
		Util::echoResult(201,'接任务失败');
		

	// 通过审核
	}elseif( $_GPC['op'] == 'pass' ){

		if( empty( $_GPC['idlist'] ) || !is_array( $_GPC['idlist'] ) ) Util::echoResult(201,'请先选择要操作的任务');
		set_time_limit(0);

		$success = 0;
		$fail = 0;
		$sucarr = array();
		foreach ( $_GPC['idlist'] as $v ) {
			
			$taked = pdo_get('zofui_tasktb_tbtaked',array('uniacid'=>$_W['uniacid'],'id'=>$v,'status'=>0));
			if( empty( $taked ) ) continue;
			
			$task = model_tbtask::getTask( $taked['taskid'] );
			if( empty( $task ) ) continue;
			if( $task['userid'] != $userinfo['uid'] ) Util::echoResult(201,'您不是任务的发布者');
			
			if( $task['status'] != 0 ) Util::echoResult(201,'任务未上架');
			if( $task['start'] > TIMESTAMP ) Util::echoResult(201,'任务未开始');
			if( $task['iscount'] == 1 ) Util::echoResult(201,'任务已结束');
				
			
			// 扣余额，查保证金
			$user= model_user::getSingleUser( $task['userid'] );
			$credit = model_user::getUserCredit( $user['uid'] );

			// 会员等级
			$level = model_user::levelRes($userinfo,$this->module['config']);
			if( $level == 1 ) {
				$_W['set']['tbgivetaskserver'] = $_W['set']['tbgivetaskservera'];
			}
			if( $level == 2 ) {
				$_W['set']['tbgivetaskserver'] = $_W['set']['tbgivetaskserverb'];
			}
			
			$server = $_W['set']['tbgivetaskserver']*$task['money']/100;
			$total = $server + $task['money'] + $task['tbmoney'];

			if( $credit['credit2'] < $total )
				Util::echoResult(210,'您的余额不足,审核此任务需'.$total.'余额,您还差'. ($total - $credit['credit2']),array('url'=>$this->createMobileUrl('money',array('op'=>'in')),'suc'=>$sucarr));


			/*$allneed = model_money::needdePosit( $user['openid'] );
			$needdeposit = $task['money'] + $allneed;
			$diffdeposit =  $needdeposit - $userinfo['deposit'];
			if( $user['deposit'] < $needdeposit )
				Util::echoResult(210,'您的保证金不足,审核此任务账户需留存'.$needdeposit.'元保证金(其他任务已占用'.$allneed.'元保证金),您还差'. $diffdeposit,array('url'=>$this->createMobileUrl('deposit',array('op'=>'in')),'suc'=>$sucarr));*/

			// 扣钱
			$res = model_user::updateUserCredit($userinfo['uid'],-$task['money'],2,1);
			// 资金记录
			model_money::insertMoneyLog($userinfo['openid'],-$task['money'],1,22,$userinfo['uid']);
			
			if( $server > 0 ) {
				model_user::updateUserCredit($userinfo['uid'],-$server,2,1);
				// 资金记录
				model_money::insertMoneyLog($userinfo['openid'],-$server,1,9,$userinfo['uid']);
			}

			// 扣保证金
			if( $res && $task['tbmoney'] > 0 ) {
				
				$res = model_user::updateUserCredit($userinfo['uid'],-$task['tbmoney'],2,1);
				// 资金记录
				model_money::insertMoneyLog($userinfo['openid'],-$task['tbmoney'],1,23,$userinfo['uid']);
			}

			if( $res ){
				$res = model_tbtask::passTask( $taked,$task,$server );
				if( $res['status'] == 200 ) {

					// 上级提成
					$upmoney = $server;
					model_task::pubGiveParent($_W['set'],$userinfo['id'],$userinfo['parent'],'',4,$upmoney);

					$success ++;
					$sucarr[] = $taked['id'];
				}else{
					$fail ++;
					$failres = '。失败的原因：'.$res['res'];
				}
			}
			
		}
		
		if( $success > 0 ){
			Util::echoResult(200,'成功通过'.$success.'项任务'.$failres,array('suc'=>$sucarr));
		}
		Util::echoResult(201,'处理失败'.$failres);

	// 未通过审核
	}elseif( $_GPC['op'] == 'nopass' ){

		if( empty( $_GPC['idlist'] ) || !is_array( $_GPC['idlist'] ) ) Util::echoResult(201,'请先选择要操作的任务');
		set_time_limit(0);

		$success = 0;
		foreach ( $_GPC['idlist'] as $v ) {
			
			$taked = pdo_get('zofui_tasktb_tbtaked',array('uniacid'=>$_W['uniacid'],'id'=>$v,'status'=>0));
			if( empty( $taked ) ) continue;
			
			$task = model_tbtask::getTask( $taked['taskid'] );
			if( empty( $task ) ) continue;
			if( $task['userid'] != $userinfo['uid'] ) Util::echoResult(201,'您不是任务的发布者');
			
			if( $task['status'] != 0 ) Util::echoResult(201,'任务未上架');
			if( $task['start'] > TIMESTAMP ) Util::echoResult(201,'任务未开始');
			if( $task['iscount'] == 1 ) Util::echoResult(201,'任务已结束');
				
			
			$res = model_tbtask::nopassTask( $taked,$task,$_GPC['reason'] );
			if( $res ) $success ++;
			
		}
		
		if( $success > 0 ){
			Util::echoResult(200,'成功处理'.$success.'项任务');
		}
		Util::echoResult(201,'处理失败');

	// 提交浏览图片等步骤
	}elseif( $_GPC['op'] == 'tbtaskstep' ){

		if( empty( $_GPC['images'] ) ) Util::echoResult(201,'您还没上传图片');
		$takedid = intval( $_GPC['takedid'] );
		$dealstep = intval( $_GPC['dealstep'] );

		$taked = pdo_get('zofui_tasktb_tbtaked',array('uniacid'=>$_W['uniacid'],'id'=>$takedid));
		if( $taked['step'] != $dealstep ) Util::echoResult(201,'任务步骤不正确');
		if( $taked['userid'] != $userinfo['uid'] ) Util::echoResult(201,'你不能操作此任务');

		if( $taked['status'] !=  2 || $taked['step'] >= 6 ) Util::echoResult(201,'此任务不能被操作');

		$task = model_tbtask::getTask( $taked['taskid'] );
		$taskstatus = model_tbtask::taskStatus( $task );

		if( $taskstatus['status'] != 200 ) Util::echoResult(201,$taskstatus['res']);


		$stepcontent = iunserializer( $taked['stepcontent'] );

		$stepcontent[] = array(
			'content'=>$_GPC['content'],
			'images'=>$_GPC['images'],
			'time'=>TIMESTAMP,
			'step' => $dealstep,
		);
		$nextstep = model_tbtask::nextStep( $taked['step'],$task['step'] );
		if( !$nextstep ) Util::echoResult(201,'此任务不能被操作');

		$update = array('step'=>$nextstep,'stepcontent'=> iserializer( $stepcontent ),'islimitstep'=>0 );
		if( $nextstep >= 6 ) $update['subcomtime'] = TIMESTAMP; // 完成时间

		$res = pdo_update('zofui_tasktb_tbtaked',$update,array('id'=>$taked['id']));

		if( $nextstep >= 6 ) { // 完成了 发通知
			Message::tbtaskCom( $task['userid'],$task['puber'],$task['title'],$task['id'],$userinfo['nickname'] );
		}

		if( $res ) Util::echoResult(200,'已提交');
		Util::echoResult(201,'提交失败');	

	// 提醒对方
	}elseif( $_GPC['op'] == 'tbremind' ){

		if( empty( $_GPC['content'] ) ) Util::echoResult(201,'请填写提醒内容');

		$data = array(
			'uniacid' => $_W['uniacid'],
			'takedid' => $_GPC['takedid'],
			'content' => $_GPC['content'],
			'createtime' => TIMESTAMP,
		);

		if( $_GPC['type'] == 1 ){ // 来自担保任务提醒

			$taked = pdo_get('zofui_tasktb_tbtaked',array('uniacid'=>$_W['uniacid'],'id'=>$_GPC['takedid']));
			if( $taked['status'] != 2 ) Util::echoResult(201,'不可提醒对方');

			$time = empty( $_W['set']['remindtime'] ) ? 30 : $_W['set']['remindtime'];
			$rangetime = TIMESTAMP - 60*$time;

			$isset = Util::countDataNumber('zofui_tasktb_tbremind',array('createtime>'=>$rangetime,'takedid'=>$taked['id'],'type'=>0));
			if( $isset > 0 ) Util::echoResult(201,'最近'.$time.'分钟内您已提醒过对方，过段时间再提醒他吧。');

			$task = model_tbtask::getTask( $taked['taskid'] );
			$taskstatus = model_tbtask::taskStatus( $task );

			if( $taskstatus['status'] != 200 ) Util::echoResult(201,$taskstatus['res']);

			$data['from'] = 0;
			$toopenid = $taked['openid'];
			$tasktitle = $task['title'];
			$content = $_GPC['content'];
			$url = Util::createModuleUrl('tbtask',array('id'=>$task['id']));
		}


		$res = pdo_insert('zofui_tasktb_tbremind',$data);
		if( $res ) {
			Message::remindUser($taked['userid'],$toopenid,$tasktitle,$content,$url);
			Util::echoResult(200,'已提醒');
		}
		Util::echoResult(201,'提醒失败');	

	// 完成
	}elseif( $_GPC['op'] == 'tbtaskcom' ){

		$takedid = intval( $_GPC['takedid'] );

		$taked = pdo_get('zofui_tasktb_tbtaked',array('uniacid'=>$_W['uniacid'],'id'=>$takedid));
		if( $taked['status'] != 2 || $taked['step'] != 6 ) Util::echoResult(201,'不可操作任务');

		$task = model_tbtask::getTask( $taked['taskid'] );
		$taskstatus = model_tbtask::taskStatus( $task );

		if( $taskstatus['status'] != 200 ) Util::echoResult(201,$taskstatus['res']);

		if( $task['userid'] != $userinfo['uid'] ) Util::echoResult(201,'不可操作任务');

		$res = model_tbtask::comTbtask( $taked,$task,3 );

		if( $res ) {
			Util::echoResult(200,'已完成');
		}
		Util::echoResult(201,'操作失败');

	// 设为任务失败
	}elseif( $_GPC['op'] == 'tbtaskfail' ){

		$takedid = intval( $_GPC['takedid'] );
		if( empty( $_GPC['reason'] ) ) Util::echoResult(201,'请填写失败原因');

		$taked = pdo_get('zofui_tasktb_tbtaked',array('uniacid'=>$_W['uniacid'],'id'=>$takedid));
		if( $taked['status'] != 2 || $taked['step'] != 6 ) Util::echoResult(201,'不可操作任务');

		$task = model_tbtask::getTask( $taked['taskid'] );
		$taskstatus = model_tbtask::taskStatus( $task );

		if( $taskstatus['status'] != 200 ) Util::echoResult(201,$taskstatus['res']);

		if( $task['userid'] != $userinfo['uid'] ) Util::echoResult(201,'不可操作任务');


		$res = pdo_update('zofui_tasktb_tbtaked',array('status'=>4,'setfailtime'=>TIMESTAMP,'failreason'=>$_GPC['reason']),array('id'=>$taked['id']));

		if( $res ){
			// 发消息
			Message::setFailTbtask($taked['userid'],$taked['openid'],$taked['taskid'],$_GPC['reason'],$task['title']);
			Util::echoResult(200,'请等待对方确认');
		}
		Util::echoResult(201,'操作失败');

	// 确认失败
	}elseif( $_GPC['op'] == 'tbconfirmfail' ){

		$takedid = intval( $_GPC['takedid'] );

		$taked = pdo_get('zofui_tasktb_tbtaked',array('uniacid'=>$_W['uniacid'],'id'=>$takedid));
		if( $taked['status'] != 4 || $taked['step'] != 6 ) Util::echoResult(201,'不可操作任务');

		$task = model_tbtask::getTask( $taked['taskid'] );
		$taskstatus = model_tbtask::taskStatus( $task );

		if( $taskstatus['status'] != 200 ) Util::echoResult(201,$taskstatus['res']);

		if( $taked['userid'] != $userinfo['uid'] ) Util::echoResult(201,'不可操作任务');

		// 确认任务失败
		$res = model_tbtask::confirmFailTbtask( $taked,$task,5 );
		
		if( $res ){
			Util::echoResult(200,'已确认');
		}
		Util::echoResult(201,'操作失败');

	// 申诉
	}elseif( $_GPC['op'] == 'tbcomplain' ){

		$takedid = intval( $_GPC['takedid'] );

		$taked = pdo_get('zofui_tasktb_tbtaked',array('uniacid'=>$_W['uniacid'],'id'=>$takedid));
		if( $taked['status'] != 4 || $taked['step'] != 6 ) Util::echoResult(201,'不可操作任务');

		$task = model_tbtask::getTask( $taked['taskid'] );
		$taskstatus = model_tbtask::taskStatus( $task );

		if( $taskstatus['status'] != 200 ) Util::echoResult(201,$taskstatus['res']);

		if( $taked['userid'] != $userinfo['uid'] ) Util::echoResult(201,'不可操作任务');

		if( empty( $_GPC['images'] ) && empty( $_GPC['content'] ) ) Util::echoResult(201,'请填写申诉内容');

		// 确认任务失败
		$res = pdo_update('zofui_tasktb_tbtaked',array('complaintime'=>TIMESTAMP,'status'=>6),array('id'=>$taked['id']));
		
		if( $res ){

			$data = array(
				'uniacid' => $_W['uniacid'],
				'takedid' => $taked['id'],
				'content' => $_GPC['content'],
				'images' => iserializer( $_GPC['images'] ),
				'type' => 1,
				'createtime' => TIMESTAMP,
			);
			pdo_insert('zofui_tasktb_tbcert',$data);

			// 给雇主发消息
			Message::tbComplain($task['userid'],$task['puber'],$task['id'],$userinfo['nickname'],$task['title']);
			Util::echoResult(200,'已申诉');
		}
		Util::echoResult(201,'操作失败');

	}elseif( $_GPC['op'] == 'subtbcert' ){

		$takedid = intval( $_GPC['takedid'] );

		$taked = pdo_get('zofui_tasktb_tbtaked',array('uniacid'=>$_W['uniacid'],'id'=>$takedid));
		if( ($taked['status'] != 6 && $taked['status'] != 7 ) || $taked['step'] != 6 ) Util::echoResult(201,'不可操作任务');

		$task = model_tbtask::getTask( $taked['taskid'] );
		$taskstatus = model_tbtask::taskStatus( $task );

		if( $taskstatus['status'] != 200 ) Util::echoResult(201,$taskstatus['res']);

		if( $_GPC['type'] == 1 )  if( $taked['userid'] != $userinfo['uid'] ) Util::echoResult(201,'不可操作任务');
		if( $_GPC['type'] == 2 )  if( $taked['pubuid'] != $userinfo['uid'] ) Util::echoResult(201,'不可操作任务');


		if( empty( $_GPC['images'] ) && empty( $_GPC['content'] ) ) Util::echoResult(201,'请填写凭证内容');

		$data = array(
			'uniacid' => $_W['uniacid'],
			'takedid' => $taked['id'],
			'content' => $_GPC['content'],
			'images' => iserializer( $_GPC['images'] ),
			'type' => $_GPC['type'],
			'createtime' => TIMESTAMP,
		);
		$res = pdo_insert('zofui_tasktb_tbcert',$data);

		if( $res ) Util::echoResult(200,'已提交');
		Util::echoResult(201,'提交失败');

	// 下架任务
	}elseif( $_GPC['op'] == 'downanduptbtask' ){

		$taskid = intval( $_GPC['taskid'] );
		$task = model_tbtask::getTask( $taskid );

		if( empty( $task ) ) Util::echoResult(201,'任务不存在，不能下架');
			
		
		$isadmin = model_user::isAdmin( $userinfo['uid'] );
		if( !$isadmin ) Util::echoResult(201,'您不能操作任务');
	
		if( $_GPC['type'] == 1 ){ // 下架
			if( $task['status'] != 0 ) Util::echoResult(201,'任务未上架，不能下架');	

			$res = pdo_update('zofui_tasktb_tbtask',array('status'=>2),array('uniacid'=>$_W['uniacid'],'id'=>$task['id']));

		}else{ // 上架
			if( $task['status'] != 2 ) Util::echoResult(201,'任务未上架，不能下架');	
			$res = pdo_update('zofui_tasktb_tbtask',array('status'=>0),array('uniacid'=>$_W['uniacid'],'id'=>$task['id']));
		}

		if( $res ){
			Util::deleteCache('tbtask',$taskid);
			Util::echoResult(200,'操作成功');
		}
		Util::echoResult(201,'操作失败');




	// 审核任务
	}elseif($_GPC['op'] == 'verifytbtask'){

		$taskid = intval( $_GPC['taskid'] );
		$data['closereason'] = $_GPC['value'];

		$task = model_tbtask::getTask( $taskid );
		if( empty( $task ) ) Util::echoResult(201,'任务不存在，不能审核');
		if( $task['status'] != 1 ) Util::echoResult(201,'任务不能审核');
		if( $task['iscount'] == 1 ) Util::echoResult(201,'任务已结束，不能再审核');			
		
		$canverify = 0;
		if( !empty( $_W['set']['admin'] ) ){
			$admin = iunserializer( $_W['set']['admin'] );
			if( is_array( $admin ) ){
				foreach ( $admin as $v ) {
					if( $v['userid'] == $userinfo['uid'] ){
						$canverify = 1;
						break;
					}
				}
			}
		}
		if( $canverify == 0 ) Util::echoResult(201,'任务不能被您审核');

		if( $_GPC['type'] == 1 ) $data['status'] = 0;
		if( $_GPC['type'] == 2 ) $data['status'] = 2;
		$res = pdo_update('zofui_tasktb_tbtask',$data,array('uniacid'=>$_W['uniacid'],'id'=>$task['id']));
		

		if( $res ){

			if( $_GPC['type'] == 1 ) { // 审核通过
				$puber = model_user::getSingleUser( $task['userid'] );
				$upmoney = $task['costtop'] + $task['costserver'] + $task['costka'];
				model_task::pubGiveParent($this->module['config'],$puber['id'],$puber['parent'],$task['id'],3,$upmoney);
			}

			// 发消息
			Message::verifytask($task['userid'],$task['puber'],$task['id'],$task['title'],$_GPC['type'],$data['closereason'],1);

			Util::deleteCache('tbtask',$task['id']);
			Util::echoResult(200,'审核成功');
		}
		Util::echoResult(201,'审核失败');

	//拉黑和恢复
	}elseif( $_GPC['op'] == 'touserblack' ){

		$user = pdo_get('zofui_task_user',array('uniacid'=>$_W['uniacid'],'id'=>$_GPC['guyid']),array('id','openid','uid'));
		if( empty( $user ) ) Util::echoResult(201,'未找到会员');

		if( $user['uid'] == $userinfo['uid'] ) Util::echoResult(201,'不能对自己操作');
		if( $_GPC['type'] == 1 ){

			$isblack = pdo_get('zofui_tasktb_tbblack',array('uniacid'=>$_W['uniacid'],'userid'=>$userinfo['uid'],'targetuid'=>$user['uid']));
			if( !empty( $isblack ) ) Util::echoResult(201,'对方已在黑名单列表内');

			$data = array(
				'uniacid' => $_W['uniacid'],
				'openid' => $userinfo['openid'],
				'userid' => $userinfo['uid'],
				'target' => $user['openid'],
				'targetuid' => $user['uid'],
			);
			pdo_insert('zofui_tasktb_tbblack',$data);
			Util::echoResult(200,'已加入黑名单');

		}elseif( $_GPC['type'] == 2 ){

			pdo_delete('zofui_tasktb_tbblack',array('uniacid'=>$_W['uniacid'],'userid'=>$userinfo['uid'],'targetuid'=>$user['uid']));
			Util::echoResult(200,'已从黑名单删除');

		}

	// 搜索会员
	}elseif( $_GPC['op'] == 'findguy' ){

		$uid = intval( $_GPC['uid'] );
		$user = pdo_get('zofui_task_user',array('uniacid'=>$_W['uniacid'],'id'=>$uid));
		
		if( empty( $user ) ) Util::echoResult(201,'未找到会员');
		
		Util::echoResult(200,'好',array('uid'=>$user['id'],'img'=>$user['headimgurl'],'nickname'=>$user['nickname']));

	}elseif( $_GPC['op'] == 'loginout' ){

		isetcookie('__stsessiona', false, -100);
		unset( $_SESSION['uid'] );

		$url = $this->createMobileUrl('user');

		Util::echoResult(200,'已退出登录',array('url'=>$url));


	// 绑定账户
	}elseif( $_GPC['op'] == 'bindaccount' ){

		if( $_W['dev'] != 'wx' ) Util::echoResult(201,'数据不正确');
		load()->model('mc');
		$user = mc_fetch($userinfo['uid'], array('uniacid'=>$_W['uniacid']));

		if( empty( $user ) ) Util::echoResult(201,'账户不存在');

		if( !empty( $user['mobile'] ) ){
			Util::echoResult(201,'你已经绑定了账户'.$user['mobile']);
		}

		if( !preg_match('/^1\d{10}$/', $_GPC['mobile']) ){
			Util::echoResult(201,'请输入正确的手机号');
		}
		if( empty( $_GPC['pass1'] ) || empty( $_GPC['pass2'] ) ) {
			Util::echoResult(201,'密码不能为空');
		}

		if( strlen( $_GPC['pass1'] ) < 6 ) {
			Util::echoResult(201,'密码至少6位字符');
		}
		if( $_GPC['pass1'] !== $_GPC['pass2'] ) {
			Util::echoResult(201,'两次密码不一致');
		}

		$isset = pdo_get('mc_members',array('uniacid'=>$_W['uniacid'],'mobile'=>$_GPC['mobile']));
		if( !empty( $isset ) ) Util::echoResult(201,'手机号已经被使用了，请换一个');

		$password = md5($_GPC['pass1'] . $user['salt'] . $_W['config']['setting']['authkey']);
		$res = mc_update($user['uid'], array('mobile'=>$_GPC['mobile'],'password' => $password));

		Util::echoResult(200,'已绑定');


	// 搜索会员
	}elseif( $_GPC['op'] == 'viewform' ){

		$id = intval( $_GPC['fid'] );
		$form = pdo_get('zofui_tasktb_taskform',array('uniacid'=>$_W['uniacid'],'id'=>$id));
		
		if( empty( $form ) ) Util::echoResult(201,'未找到数据');
		
		$form['form'] = iunserializer( $form['form'] );

		if( empty( $form['form'] ) ) Util::echoResult(201,'此模板没有数据');

		$str = '';
		foreach ($form['form'] as $v) {
			if( $v['type'] == 'img' ){
				$str .= <<<div
					<div class="pub_content mt05">
						<div class="pub_content_title">{$v['name']}</div>
						<div class="pub_images_list">
							<div class="upload_images_views"></div>			
							<div class="uploader_input"></div>
						</div>
					</div>
div;

			}elseif( $v['type'] == 'text' ){
				$str .= <<<div
					<div class="form_group mt05 item_cell_box">
						<div class="form_title">{$v['name']}</div>
						<div class="form_right item_cell_flex">
							<textarea style="border:0;" class="pub_task_content reply_task_content formitem" readonly placeholder="{$v['pla']}"></textarea>
						</div>
					</div>
div;
			}else{
				$str .= <<<div
					<div class="form_group mt05 item_cell_box">
						<div class="form_title">{$v['name']}</div>
						<div class="form_right item_cell_flex item_cell_box">
							<li class="item_cell_flex">
								<input type="text" readonly name="taskmoney" class="form_input form_into formitem" value="" placeholder="{$v['pla']}">
							</li>
						</div>
					</div>
div;
			}
		}	

		Util::echoResult(200,'',array('str'=>$str));


	}elseif( $_GPC['op'] == 'viewstep' ){

		$id = intval( $_GPC['fid'] );
		$form = pdo_get('zofui_tasktb_step',array('uniacid'=>$_W['uniacid'],'istemp'=>1,'id'=>$id));
		
		if( empty( $form ) ) Util::echoResult(201,'未找到数据');
		
		$form['step'] = iunserializer( $form['step'] );

		if( empty( $form['step'] ) ) Util::echoResult(201,'此模板没有数据');

		$str = '<div class="infostep_t">任务步骤</div>';

		foreach ($form['step'] as $k => $v) {
			$no = $k + 1;
					
			$copy = '';
			if( !empty($v['copy']) ){
				$copy = '<div class="infostep_in">';
				foreach ($v['copy'] as $kk => $vv) {
					$copy .= '<a href="javascript:;" data-clipboard-text="'.$vv.'" class="infostep_copy copythis pri-bg">'.$vv.'</a>';
				}
				$copy .= '</div>';
			}

			$url = '';
			if( !empty($v['url']) ){
				$url = '<div class="infostep_in">';
				foreach ($v['url'] as $kk => $vv) {
					$url .= '<a href="'.$vv['url'].'" class="infostep_url">'.$vv['text'].'</a>';
				}
				$url .= '</div>';
			}
			$img = '';
			if( !empty($v['img']) ){
				$img = '<div class="infostep_in"><div class="need_show_images oh">';
				foreach ($v['img'] as $kk => $vv) {
					$img .= '<li class="need_show_images_item fl" ><img src="'.tomedia($vv).'" ></li>';
				}
				$img .= '</div></div>';
			}						
			$str .= <<<div
				<div class="infostep_item">
					<div class="infostep_line"></div>
					<div class="infostep_l item_cell_box">
						<div class="infostep_no pri-bg">{$no}</div>
						<div class="item_cell_flex">{$v['name']}</div>
					</div>
					{$copy}
					{$url}
					{$img}
				</div>
div;
		}	
		
		Util::echoResult(200,'',array('str'=>$str));


	}elseif( $_GPC['op'] == 'login' ){

		$forward = url('mc');

		$username = trim($_GPC['account']);
		$password = trim($_GPC['pass']);
		
		if( $_GPC['key'] != $_COOKIE['loginzf'] ) {
			setcookie('loginzf',md5($_W['account']['key'].random(111,999)),TIMESTAMP+3600);
			Util::echoResult(201,'数据异常');
		}

		if (empty($username)) {
			setcookie('loginzf',md5($_W['account']['key'].random(111,999)),TIMESTAMP+3600);
			Util::echoResult(201,'用户名不能为空');
		}
		if (empty($password)) {
			setcookie('loginzf',md5($_W['account']['key'].random(111,999)),TIMESTAMP+3600);
			Util::echoResult(201,'密码不能为空');
		}
		
		$sql = 'SELECT `uid`,`salt`,`password` FROM ' . tablename('mc_members') . ' WHERE `uniacid`=:uniacid';
		$pars = array();
		$pars[':uniacid'] = $_W['uniacid'];
		if ($item == 'mobile') {
			if (preg_match(REGULAR_MOBILE, $username)) {
				$sql .= ' AND `mobile`=:mobile';
				$pars[':mobile'] = $username;
			} else {
				message('请输入正确的手机', '', 'error');
			}
		} elseif ($item == 'email') {
			if (preg_match(REGULAR_EMAIL, $username)) {
				$sql .= ' AND `email`=:email';
				$pars[':email'] = $username;
			} else {
				message('请输入正确的邮箱', '', 'error');
			}
		} else {
			if (preg_match(REGULAR_MOBILE, $username)) {
				$sql .= ' AND `mobile`=:mobile';
				$pars[':mobile'] = $username;
			} else {
				$sql .= ' AND `email`=:email';
				$pars[':email'] = $username;
			}
		}
		$user = pdo_fetch($sql, $pars);

		$hash = md5($password . $user['salt'] . $_W['config']['setting']['authkey']);
		if ($user['password'] != $hash) {

			setcookie('loginzf',md5($_W['account']['key'].random(111,999)),TIMESTAMP+3600);
			Util::echoResult(201,'密码错误');
		}

		if (empty($user)) {
			setcookie('loginzf',md5($_W['account']['key'].random(111,999)),TIMESTAMP+3600);
			Util::echoResult(201,'该帐号尚未注册');
		}

		$cookie = array();
		$cookie['uid'] = $user['uid'];
		$cookie['hash'] = md5($user['password'] . $_W['config']['setting']['authkey'].'vrewewvw');
		$session = authcode(json_encode($cookie), 'encode');

		$time = $_W['set']['logintime'] <= 0 ? 86400 : $_W['set']['logintime']*3600;

		$setres = isetcookie('__stsessiona', $session, $time , true);

		if ($setres) {
			setcookie('loginzf',md5($_W['account']['key'].random(111,999)),TIMESTAMP+3600);
			Util::echoResult(200,'登录成功！');
		}

	}elseif($_GPC['op'] == 'register'){

		if( $_GPC['key'] != $_COOKIE['loginzf'] ) {
			setcookie('loginzf',md5($_W['account']['key'].random(111,999)),TIMESTAMP+3600);
			Util::echoResult(201,'数据异常');
		}


	
		$sql = 'SELECT `uid` FROM ' . tablename('mc_members') . ' WHERE `uniacid`=:uniacid';
		$pars = array();
		$pars[':uniacid'] = $_W['uniacid'];
		$code = trim($_GPC['code']);
		$username = trim($_GPC['account']);
		$password = trim($_GPC['pass1']);
		$repassword = trim($_GPC['pass2']);
		$qq = trim($_GPC['qq']);

		if(preg_match(REGULAR_MOBILE, $username)) {
			$type = 'mobile';
			$sql .= ' AND `mobile`=:mobile';
			$pars[':mobile'] = $username;
		} else {
			setcookie('loginzf',md5($_W['account']['key'].random(111,999)),TIMESTAMP+3600);
			Util::echoResult(201,'手机号格式不正确');
		}

		if( empty($password) ) {
			setcookie('loginzf',md5($_W['account']['key'].random(111,999)),TIMESTAMP+3600);
			Util::echoResult(201,'密码不能为空');
		}
		if( $password != $repassword ) {
			setcookie('loginzf',md5($_W['account']['key'].random(111,999)),TIMESTAMP+3600);
			Util::echoResult(201,'两次密码不一致');
		}
		if(empty($qq) && $this->module['config']['isrqq'] == 1 ) {
			setcookie('loginzf',md5($_W['account']['key'].random(111,999)),TIMESTAMP+3600);
			Util::echoResult(201,'请填写你的qq号码');
		}
		if( $this->module['config']['isloginv'] == 1 ){
			if( empty($_GPC['code']) || $_GPC['code'] != $_SESSION[ 'vertify'.$_GPC['account'] ] ){
				setcookie('loginzf',md5($_W['account']['key'].random(111,999)),TIMESTAMP+3600);
				Util::echoResult(201,'验证码不正确');
			}
		}

		$user = pdo_fetch($sql, $pars);
		if(!empty($user)) {
			setcookie('loginzf',md5($_W['account']['key'].random(111,999)),TIMESTAMP+3600);
			Util::echoResult(201,'该用户名已被注册');
		}
		
		if(!empty($_W['openid'])) {
			$fan = mc_fansinfo($_W['openid']);
			if (!empty($fan)) {
				$map_fans = $fan['tag'];
			}
			if (empty($map_fans) && isset($_SESSION['userinfo'])) {
				$map_fans = iunserializer(base64_decode($_SESSION['userinfo']));
			}
		}

		$default_groupid = pdo_fetchcolumn('SELECT groupid FROM ' .tablename('mc_groups') . ' WHERE uniacid = :uniacid AND isdefault = 1', array(':uniacid' => $_W['uniacid']));
		$data = array(
			'uniacid' => $_W['uniacid'], 
			'salt' => random(8),
			'groupid' => $default_groupid, 
			'createtime' => TIMESTAMP,
			'qq' => $qq,
		);
		
		$data['mobile'] = $username;
		if (!empty($password)) {
			$data['password'] = md5($password . $data['salt'] . $_W['config']['setting']['authkey']);
		}
		

		if(!empty($map_fans)) {
			$data['nickname'] = strip_emoji($map_fans['nickname']);
			$data['gender'] = $map_fans['sex'];
			$data['residecity'] = $map_fans['city'] ? $map_fans['city'] . '市' : '';
			$data['resideprovince'] = $map_fans['province'] ? $map_fans['province'] . '省' : '';
			$data['nationality'] = $map_fans['country'];
			$data['avatar'] = $map_fans['headimgurl'];
		}
		
		pdo_insert('mc_members', $data);
		$user['uid'] = pdo_insertid();
		if (!empty($fan) && !empty($fan['fanid'])) {
			pdo_update('mc_mapping_fans', array('uid'=>$user['uid']), array('fanid'=>$fan['fanid']));
		}
		if(_mc_login($user)) {
			setcookie('loginzf',md5($_W['account']['key'].random(111,999)),TIMESTAMP+3600);
			Util::echoResult(200,'注册成功！');
		}

		setcookie('loginzf',md5($_W['account']['key'].random(111,999)),TIMESTAMP+3600);
		Util::echoResult(201,'注册失败');
	
	}elseif($_GPC['op'] == 'checkcode'){

		if( $_GPC['key'] != $_COOKIE['loginzf'] ) {
			setcookie('loginzf',md5($_W['account']['key'].random(111,999)),TIMESTAMP+3600);
			Util::echoResult(201,'数据异常');
		}

		if( empty($_GPC['account']) ) Util::echoResult(201,'请输入手机号');
		if( empty($_GPC['code']) ) Util::echoResult(201,'请输入验证码');

		if( empty($_GPC['code']) || $_GPC['code'] != $_SESSION[ 'vertify'.$_GPC['account'] ] ){
			setcookie('loginzf',md5($_W['account']['key'].random(111,999)),TIMESTAMP+3600);
			Util::echoResult(201,'验证码不正确');
		}

		Util::echoResult(200,'');


	}elseif($_GPC['op'] == 'resetpass'){

		if( $_GPC['key'] != $_COOKIE['loginzf'] ) {
			setcookie('loginzf',md5($_W['account']['key'].random(111,999)),TIMESTAMP+3600);
			Util::echoResult(201,'数据异常');
		}

		$username = trim($_GPC['account']);
		$password = trim($_GPC['pass1']);
		$repassword = trim($_GPC['pass2']);

		if( empty($username) ){
			setcookie('loginzf',md5($_W['account']['key'].random(111,999)),TIMESTAMP+3600);
			Util::echoResult(201,'请输入账户');
		}

		if( empty($password) ){
			setcookie('loginzf',md5($_W['account']['key'].random(111,999)),TIMESTAMP+3600);
			Util::echoResult(201,'密码不能为空');
		}

		if ($repassword != $password) {
			setcookie('loginzf',md5($_W['account']['key'].random(111,999)),TIMESTAMP+3600);
			Util::echoResult(201,'密码输入不一致');
		}

		if( empty($_GPC['code']) || $_GPC['code'] != $_SESSION[ 'vertify'.$username ] ){
			setcookie('loginzf',md5($_W['account']['key'].random(111,999)),TIMESTAMP+3600);
			Util::echoResult(201,'验证码不正确');
		}

		$sql = 'SELECT `uid`,`salt` FROM ' . tablename('mc_members') . ' WHERE `uniacid`=:uniacid';
		$pars = array();
		$pars[':uniacid'] = $_W['uniacid'];
		if(preg_match('/^\d{11}$/', $username)) {
			$type = 'mobile';
			$sql .= ' AND `mobile`=:mobile';
			$pars[':mobile'] = $username;
		} elseif(preg_match("/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/", $username)) {
			$type = 'email';
			$sql .= ' AND `email`=:email';
			$pars[':email'] = $username;
		} else {
			setcookie('loginzf',md5($_W['account']['key'].random(111,999)),TIMESTAMP+3600);
			Util::echoResult(201,'用户名格式不正确');
		}
		$user = pdo_fetch($sql, $pars);
		if(empty($user)) {
			setcookie('loginzf',md5($_W['account']['key'].random(111,999)),TIMESTAMP+3600);
			Util::echoResult(201,'用户不存在');
		} else {
			$password = md5($password . $user['salt'] . $_W['config']['setting']['authkey']);
			mc_update($user['uid'], array('password' => $password));
		}

		Util::echoResult(200,'');		

	}elseif( $_GPC['op'] == 'setnick' ){

		if( empty($_GPC['nick']) ) Util::echoResult(201,'请填写昵称');
		//if( empty($_GPC['headimg']) ) Util::echoResult(201,'请上传头像');

		$res = pdo_update('zofui_task_user',array('nickname'=>$_GPC['nick'],'headimgurl'=>$_GPC['headimg']),array('id'=>$userinfo['id']));

		Util::deleteCache('u',$userinfo['uid']);

		Util::echoResult(200,'设置完成');

	}elseif($_GPC['op'] == 'readanswer'){
		$task = pdo_get('zofui_tasktb_task',array('uniacid'=>$_W['uniacid'],'id'=>$_GPC['id']));
		if( empty($task) ) Util::echoResult(201,'任务不存在');

		if( $task['status'] != 0 ) Util::echoResult(201,'任务还未上架');
		if( empty( $task['isread'] ) || $task['readprice'] <= 0 ) Util::echoResult(201,'任务不可查看答案');
		if( empty($_W['set']['isanw']) ) Util::echoResult(201,'任务不可查看答案');

		$where = array('uid'=>$userinfo['uid'],'taskid'=>$task['id'],'endtime>'=>TIMESTAMP);
		$anwtime = ' OR `endtime` = 0 ';
		if( $_W['set']['anwtime'] > 0 ){
			$anwtime = '';
		}
		$isreaded = Util::countDataNumber('zofui_tasktb_anwread',$where,$anwtime);
		if( !empty($isreaded) && $isreaded['endtime'] > 0 && empty($_W['set']['anwtimes']) ) {
			Util::echoResult(201,'你已经支付过了，不能再支付查看');
		}
		if( $_W['set']['anwtimes'] == 1 && $_W['set']['anwjg'] > 0 ) {
			$laatime = TIMESTAMP - $_W['set']['anwjg']*3600;
			$isrrr = pdo_get('zofui_tasktb_anwread',array('uid'=>$userinfo['uid'],'taskid'=>$task['id'],'createtime >'=>$laatime));
			if( !empty($isrrr) ) Util::echoResult(201,'为了保障您的权益，您最近有查看过该答案，为了避免您重复支付费用，请稍后再尝试');
		}

		if( $_W['set']['anwscann'] > 0 ){
			$taked = pdo_count('zofui_tasktb_taked',array('taskid'=>$task['id'],'status'=>2));
			if( $taked <= $_W['set']['anwscann'] ){
				Util::echoResult(201,'任务还不可查看答案，己釆纳数大于'.$_W['set']['anwscann'].'才可以查看');
			}
		}

		$credit = model_user::getUserCredit( $userinfo['uid'] );
		if($credit['credit2'] < $task['readprice']){
			Util::echoResult(210,'你的'.$_W['cname'].'不足',array('url'=>Util::createModuleUrl('money',array('op'=>'in'))));
		}
		
		// 扣钱
		$res = model_user::updateUserCredit($userinfo['uid'],-$task['readprice'],2,1);
		if( $res ){
			// 资金记录
			model_money::insertMoneyLog($userinfo['openid'],-$task['readprice'],1,36,$userinfo['uid']);
			
			$time = $_W['set']['anwtime'] <= 0 ? 0 : $_W['set']['anwtime']*3600*24;
			$endtime = $time > 0 ? (TIMESTAMP + $time): 0;

			$puberfee = $task['readprice']*$_W['set']['anwmpub']/100;
			$lrfee = $task['readprice']*$_W['set']['anwmin']/100;
			$boxfee = $task['readprice']*$_W['set']['anwmbox']/100;
			$sysfee = $task['readprice'] - $puberfee - $lrfee - $boxfee;

			$indata = array(
				'uniacid' => $_W['uniacid'],
				'uid' => $userinfo['uid'],
				'taskid' => $task['id'],
				'cost' => $task['readprice'],
				'createtime' => TIMESTAMP,
				'endtime' => $endtime,
				'puberfee' => $puberfee,
				'lrfee' => $lrfee,
				'boxfee' => $boxfee,
				'sysfee' => $sysfee,
			);
			$res = pdo_insert('zofui_tasktb_anwread',$indata);
			if( $res ){
				// 发放金额
				$puber = model_user::getSingleUser($task['userid']);
				model_user::updateUserCredit($puber['uid'],$puberfee,2,1);
				model_money::insertMoneyLog($puber['openid'],$puberfee,1,37,$puber['uid']);

				$addp = 0;
				if( $_W['set']['yinpanw'] > 0 ){
					$addp = $task['readprice']*$_W['set']['yinpanw'];
				}

				Util::addOrMinusOrUpdateData('zofui_task_user',array('anwm'=>$task['readprice'],'yinp'=>$addp),$userinfo['id']);
				Util::deleteCache('u',$userinfo['uid']);
			}
			
			Util::echoResult(200,'查看成功');
		}

	}elseif( $_GPC['op'] == 'getbox' ){

		$isset = pdo_get('zofui_tasktb_anwbox',array('uniacid'=>$_W['uniacid'],'uid'=>$userinfo['uid'],'id'=>$_GPC['bid']));
		if( empty($isset) ){
			Util::echoResult(201,'宝箱不存在');
		}
		if( !empty($isset['status']) ){
			Util::echoResult(201,'宝箱已经领取了');
		}
		if( $isset['endtime'] <= TIMESTAMP ) Util::echoResult(201,'宝箱已过期');

		$isbox = Util::getCache('isbox',$userinfo['uid']);
		if( !empty($isbox) ){
			Util::echoResult(201,'系统繁忙，请重试');
		}
		Util::setCache('isbox',$userinfo['uid'],1);

		// 钱
		$res = model_user::updateUserCredit($userinfo['uid'],$isset['money'],2,1);
		if( $res ){
			// 资金记录
			model_money::insertMoneyLog($userinfo['openid'],$isset['money'],1,38,$userinfo['uid']);
			pdo_update('zofui_tasktb_anwbox',array('status'=>1,'gettime'=>TIMESTAMP),array('id'=>$isset['id']));

			Util::deleteCache('isbox',$userinfo['uid']);
			Util::echoResult(200,'已领取,宝箱金额'.$isset['money'],array('m'=>$isset['money']));
		}

		Util::deleteCache('isbox',$userinfo['uid']);
		Util::echoResult(201,'领取失败');

	}elseif( $_GPC['op'] == 'userupdate' ){

		Util::deleteCache('u',$userinfo['uid']);
		Util::echoResult(200,'已更新数据');

	}elseif( $_GPC['op'] == 'getanwback' ){

		$yesday = strtotime(date('Y-m-d',TIMESTAMP));
		$isgeted = pdo_get('zofui_tasktb_anwgeted',array('uid'=>$userinfo['uid'],'createtime >='=>$yesday));

		if( !empty($isgeted) ){
			Util::echoResult(201,'昨天的你已经领取了，明天再来吧');
		}

		$mydata = model_slider::getBackMoney($userinfo['uid'],$_W['set']);
		if( $mydata['yesdayrunmy'] < 0.01 ){
			Util::echoResult(201,'您昨日的回馈奖励为0，不能领取');
		}

		$isbox = Util::getCache('isboxa',$userinfo['uid']);
		if( !empty($isbox) ){
			Util::echoResult(201,'系统繁忙，请重试');
		}
		Util::setCache('isboxa',$userinfo['uid'],1);


		$thisday = $yesday - 3600*24;
		$indata = array(
			'uniacid' => $_W['uniacid'],
			'uid' => $userinfo['uid'],
			'money' => $mydata['yesdayrunmy'],
			'createtime' => TIMESTAMP,
			'thisday' => $thisday,
		);
		$res = pdo_insert('zofui_tasktb_anwgeted',$indata);

		// 钱
		if( $res ) {
			$res = model_user::updateUserCredit($userinfo['uid'],$mydata['yesdayrunmy'],2,1);
		}
		if( $res ){
			// 资金记录
			model_money::insertMoneyLog($userinfo['openid'],$mydata['yesdayrunmy'],1,39,$userinfo['uid']);

			Util::deleteCache('isboxa',$userinfo['uid']);
			Util::echoResult(200,'已领取');
		}

		Util::deleteCache('isboxa',$userinfo['uid']);
		Util::echoResult(201,'领取失败');

	// 充值活跃度
	}elseif( $_GPC['op'] == 'addactivity' ){

		$act = intval($_GPC['act']);
		if( $act <= 0 ) Util::echoResult(201,'数值不正确1');
		if( $_W['set']['actper'] <= 0 ) Util::echoResult(201,'充值接口功能已关闭');

		$last = $act%$_W['set']['actper'];
		
		if( $act < $_W['set']['actper'] || $last != 0 ) Util::echoResult(201,'兑换数量必须是'.$_W['set']['actper'].'的倍数');
		$need = intval( $act/$_W['set']['actper'] );
		
		if( $need <= 0 ) Util::echoResult(201,'数值不正确2');

		if( $_W['set']['maact'] > 0 ){
			$today = strtotime(date('Y-m-d',TIMESTAMP));
			$geted = -Util::countDataSum('zofui_tasktb_moneylog',array('uniacid'=>$_W['uniacid'],'userid'=>$userinfo['uid'],'mtype'=>1,'type'=>40,'time>'=>$today),' SUM(`money`) ');
			$getedact = $_W['set']['actper']*$geted;
			if( $getedact + $act >= $_W['set']['maact'] ){
				Util::echoResult(201,'每天最多兑换'.$_W['set']['maact'].'活跃度,你已经兑换'.$getedact.'，剩余可兑换'. ($_W['set']['maact']-$getedact) );
			}
		}

		$credit = model_user::getUserCredit( $userinfo['uid'] );
		if($credit['credit2'] < $need){
			Util::echoResult(210,'你的'.$_W['cname'].'不足',array('url'=>Util::createModuleUrl('money',array('op'=>'in'))));
		}

		// 钱
		$res = model_user::updateUserCredit($userinfo['uid'],-$need,2,1);

		if( $res ){
			// 资金记录
			model_money::insertMoneyLog($userinfo['openid'],-$need,1,40,$userinfo['uid']);

			$addp = 0;
			if( $_W['set']['yinpact'] > 0 ){
				$addp = $act*$_W['set']['yinpact'];
			}

			Util::addOrMinusOrUpdateData('zofui_task_user',array('activity'=>$act,'yinp'=>$addp),$userinfo['id']);
			Util::deleteCache('u',$userinfo['uid']);
			Util::echoResult(200,'已兑换成功');
		}

		Util::echoResult(201,'兑换失败');

	// 参团
	}elseif( $_GPC['op'] == 'joinpin' ){
		
		/*$iscuting = Util::getCache('agroup','all');
		if( $iscuting > TIMESTAMP ) Util::echoResult(201,'参与的人太多，请重试');
		Util::setCache('agroup','all',TIMESTAMP+30);*/

		$user = pdo_get('zofui_task_user',array('id'=>$userinfo['id']));
		if($_W['set']['groupnum'] <= 1 || empty($_W['set']['isgroup']) || $_W['set']['groupin'] <= 0){
			Util::echoResult(201,'功能已关闭');
		}

		if( $user['yinp'] < $_W['set']['groupin'] ){
			Util::echoResult(201,'你的银票不够，参与需要'.$_W['set']['groupin'].'银票');
		}
		
		$sql = "LOCK TABLES ".tablename('zofui_tasktb_group').' WRITE,'.tablename('zofui_tasktb_grouplog').' WRITE ';
		pdo_query($sql);

		$today = strtotime( date('Y-m-d',TIMESTAMP) );
		$times = pdo_count('zofui_tasktb_grouplog',array('uniacid'=>$_W['uniacid'],'uid'=>$userinfo['uid'],'createtime >'=>$today));
		if( $times >= $_W['set']['groupts'] && $_W['set']['groupts'] > 0 ){
			Util::unlock();
			Util::echoResult(201,'每天最多参与'.$_W['set']['groupts'].'次，你今天次数已经满了');
		}

		$group = pdo_get('zofui_tasktb_group',array('status'=>0,'uniacid'=>$_W['uniacid']));
		if( empty($group) ){

			$ingroup = array(
				'uniacid' => $_W['uniacid'],
				'createtime' => TIMESTAMP,
				'mem' => 1,
				'status' => 0
			);
			pdo_insert('zofui_tasktb_group',$ingroup);
			$ingroup['id'] = pdo_insertid();
			$group = $ingroup;
		}else{
			$isjoined = pdo_get('zofui_tasktb_grouplog',array('gid'=>$group['id'],'uniacid'=>$_W['uniacid'],'uid'=>$userinfo['uid']));
			if( !empty($isjoined) ){
				Util::unlock();
				Util::echoResult(201,'你已经参与过了，不能再参与');
			}
		}

		$joined = pdo_count('zofui_tasktb_grouplog',array('uniacid'=>$_W['uniacid'],'gid'=>$group['id']));
		if( $joined >= $_W['set']['groupnum'] ){

			Util::unlock();
			Util::echoResult(201,'此团已经满了，请等会再参与');
		}
		
		$joinlog = array(
			'uniacid' => $_W['uniacid'],
			'uid' => $userinfo['uid'],
			'gid' => $group['id'],
			'cost' => $_W['set']['groupin'],
			'createtime' => TIMESTAMP,
		);
		$res = pdo_insert('zofui_tasktb_grouplog',$joinlog);
		
		
		if( $res ){
			
			if( $joined + 1 >= $_W['set']['groupnum'] ){ // 满了
				pdo_update('zofui_tasktb_group',array('status'=>1,'endtime'=>TIMESTAMP),array('id'=>$group['id']));
				$all = pdo_getall('zofui_tasktb_grouplog',array('uniacid'=>$_W['uniacid'],'gid'=>$group['id']));
				Util::unlock();

				if( !empty($all) ){
					$total = 0;
					$totalnum = 0;
					foreach ($all as $v) {
						$totalnum ++;
						$total += $v['cost'];
					}
					$total -= $_W['set']['groupcb'];
						
					if( $total > 0 ){

						$sharearr = array();
						foreach ($all as $v) {
							$totalnum -= 1;

							$thism = model_slider::countBox($totalnum,$total);
							$total -= $thism;
							$sharearr[] = array('log'=>$v,'fee'=>$thism);	
						}
						foreach ($sharearr as $v) {
							$user = model_user::getSingleUser($v['log']['uid']);
							Util::addOrMinusOrUpdateData('zofui_task_user',array('baoshi'=>$v['fee']),$user['id']);
							pdo_update('zofui_tasktb_grouplog',array('status'=>1,'geted'=>$v['fee'],'endtime'=>TIMESTAMP),array('id'=>$v['log']['id']));
						}
					}
				}
			}

			Util::unlock();
			Util::addOrMinusOrUpdateData('zofui_task_user',array('yinp'=>-$_W['set']['groupin']),$userinfo['id']);
			Util::deleteCache('u',$userinfo['uid']);
			if( !empty($all) ){
				foreach ($all as $v) {
					Util::deleteCache('u',$v['uid']);
				}
			}
			Util::echoResult(200,'已成功参与');
		}

		Util::unlock();
		Util::echoResult(201,'参与失败');
	
	// 兑换宝石
	}elseif( $_GPC['op'] == 'getbaoshi' ){

		if( $_GPC['baoshi'] <= 0 ) Util::echoResult(201,'请输入需要兑换的宝石');
		if( $_W['set']['actsper'] <= 0 ) Util::echoResult(201,'兑换功能已关闭');
		if( $_W['set']['minbs'] > 0 && $_GPC['baoshi']%$_W['set']['minbs'] > 0 ) Util::echoResult(201,'兑换数量必须是'.$_W['set']['minbs'].'的整数倍');

		$iscance = Util::getCache('getbaoshi','all');
		if( $iscance >= TIMESTAMP ){
			Util::echoResult(201,'系统繁忙，请重试');
		}
		Util::setCache('getbaoshi','all',TIMESTAMP+30);

		$user = pdo_get('zofui_task_user',array('id'=>$userinfo['id']));
		if( $user['baoshi'] < $_GPC['baoshi'] ){
			Util::deleteCache('getbaoshi','all');
			Util::echoResult(201,'你的宝石不够');
		} 

		/*$totalbaoshi = Util::countDataSum('zofui_task_user',array('uniacid'=>$_W['uniacid']),' SUM(`baoshi`) ');
		$totalactin = Util::countDataSum('zofui_tasktb_moneylog',array('uniacid'=>$_W['uniacid'],'mtype'=>1),' SUM(`money`) ',' AND type = 40 ');
		$totalpayed = Util::countDataSum('zofui_tasktb_geoupbs',array('uniacid'=>$_W['uniacid']),' SUM(`money`) ');
		$last = (-$totalactin - $totalpayed)*$_W['set']['actsper']/100;*/

		$baoship = model_slider::getbaoshi($_W['set']['actsper']);
		$totalbaoshi = $baoship['totalbaoshi'];
		$totalactin = $baoship['totalactin'];
		$totalpayed = $baoship['totalpayed'];
		$last = $baoship['last'];
		$per = $baoship['per'];

		if( $last <= 0 ){
			Util::deleteCache('getbaoshi','all');
			Util::echoResult(201,'当前可分配资金为0，不可兑换');
		} 
		
		if( $per <= 0 ){
			Util::deleteCache('getbaoshi','all');
			Util::echoResult(201,'可分配宝石为0，不可兑换');
		} 

		$money = sprintf('%.2f',$per*$_GPC['baoshi']) * 1;
		if( $money < 0.01 ) {
			Util::deleteCache('getbaoshi','all');
			Util::echoResult(201,'计算结果小于0.01，不可兑换');
		} 

		$res = Util::addOrMinusOrUpdateData('zofui_task_user',array('baoshi'=>-$_GPC['baoshi']),$userinfo['id']);
		if( $res ){
			$resa = model_user::updateUserCredit($userinfo['uid'],$money,2,1);

			if( $resa ){
				// 资金记录
				model_money::insertMoneyLog($userinfo['openid'],$money,1,41,$userinfo['uid']);
			}

			$indata = array(
				'uniacid' => $_W['uniacid'],
				'uid' => $userinfo['uid'],
				'money' => $money,
				'baoshi' => $_GPC['baoshi'],
				'createtime' => TIMESTAMP,
			);
			pdo_insert('zofui_tasktb_geoupbs',$indata);

			Util::deleteCache('getbaoshi','all');
			Util::deleteCache('u',$userinfo['uid']);
			Util::echoResult(200,'已成功兑换'.$money.'余额，扣除'.$_GPC['baoshi'].'宝石');
		}

		Util::deleteCache('getbaoshi','all');
		Util::echoResult(201,'兑换失败');

	// 签到
	}elseif( $_GPC['op'] == 'todaysign' ){

		$today = date('Y-m-d',TIMESTAMP);
		$yes = date('Y-m-d',TIMESTAMP-(3600*24));
		$yesf = date('Y-m-d',TIMESTAMP-(3600*24*2));
		$yesff = date('Y-m-d',TIMESTAMP-(3600*24*3));

		if( empty($_W['set']['issign']) ) Util::echoResult(201,'功能已关闭');

		$istoday = pdo_get('zofui_tasktb_sign',array('uid'=>$userinfo['uid'],'uniacid'=>$_W['uniacid'],'day'=>$today));
		if( !empty($istoday) ) {
			Util::echoResult(201,'你今天已经签到了，明天再来吧');
		}

		$isyes = pdo_get('zofui_tasktb_sign',array('uid'=>$userinfo['uid'],'uniacid'=>$_W['uniacid'],'day'=>$yes));
		$isyesf = pdo_get('zofui_tasktb_sign',array('uid'=>$userinfo['uid'],'uniacid'=>$_W['uniacid'],'day'=>$yesf));
		$isyesff = pdo_get('zofui_tasktb_sign',array('uid'=>$userinfo['uid'],'uniacid'=>$_W['uniacid'],'day'=>$yesff));

		$fee = $_W['set']['signa'];
		$flag = 1;

		if( !empty($isyes) ) {
			if( $isyes['flag'] == 1 ){
				$flag = 2;
				$fee = $_W['set']['signb'];
			}
			if( $isyes['flag'] == 2 ){
				$flag = 3;
				$fee = $_W['set']['signc'];
			}
			if( $isyes['flag'] == 3 ){
				$flag = 1;
				$fee = $_W['set']['signa'];
			}
		}

		$indata = array(
			'uniacid' => $_W['uniacid'],
			'uid' => $userinfo['uid'],
			'createtime' => TIMESTAMP,
			'day' => $today,
			'give' => $fee,
			'flag' => $flag,
		);

		$res = pdo_insert('zofui_tasktb_sign',$indata);
		if( $res ){
			Util::addOrMinusOrUpdateData('zofui_task_user',array('yinp'=>$fee),$userinfo['id']);
			Util::deleteCache('u',$userinfo['uid']);
			Util::echoResult(200,'成功签到');
		}
		Util::echoResult(201,'签到失败');

	// 恢复任务
	}elseif( $_GPC['op'] == 'restart' ){

		if( $_W['set']['restart'] != 1 ) Util::echoResult(201,'功能已关闭');

		$task = pdo_get('zofui_tasktb_task',array('uniacid'=>$_W['uniacid'],'id'=>$_GPC['tid'],'userid'=>$userinfo['uid']));
		if( empty($task) ) Util::echoResult(201,'未找到任务');

		if( $task['iscount'] != 1 ) Util::echoResult(201,'任务未结算，不可恢复');

		if( $task['backmoney'] > 0 ){

			$credit = model_user::getUserCredit( $userinfo['uid'] );
			if($credit['credit2'] < $task['backmoney']){
				$url = Util::createModuleUrl('money',array('op'=>'in'));
				Util::echoResult(210,'恢复需要扣除'.$task['backmoney'].'资金，你的资金不够，请先充值',array('url'=>$url));
			}
			
			// 扣钱
			$res = model_user::updateUserCredit($userinfo['uid'],-$task['backmoney'],2,1);
			if( $res ){
				// 资金记录
				model_money::insertMoneyLog($userinfo['openid'],-$task['backmoney'],1,41,$userinfo['uid']);
				Util::deleteCache('u',$userinfo['uid']);
			}else{
				Util::echoResult(201,'恢复失败');
			}
		}

		$_W['set']['autoconfirm'] = empty($_W['set']['autoconfirm']) ? 24 : $_W['set']['autoconfirm'];
		$end = TIMESTAMP + $_W['set']['autoconfirm']*3600;
		$res = pdo_update('zofui_tasktb_task',array('iscount'=>0,'end'=>$end,'backmoney'=>0),array('id'=>$task['id']));
		if( $res ){
			Util::echoResult(200,'已恢复');
		}else{
			Util::echoResult(201,'恢复失败');
		}

	// 恢复任务
	}elseif( $_GPC['op'] == 'readimess' ){

		if( $_GPC['type'] == 'all' ){
			$res = pdo_update('zofui_tasktb_imess',array('status'=>1),array('uid'=>$userinfo['uid']));
		}else{
			$imess = pdo_get('zofui_tasktb_imess',array('uniacid'=>$_W['uniacid'],'id'=>$_GPC['mid'],'uid'=>$userinfo['uid']));
			if( empty($imess) ) Util::echoResult(201,'未找到数据');

			$res = pdo_update('zofui_tasktb_imess',array('status'=>1),array('id'=>$imess['id']));
		}

		Util::echoResult(200,'已设为阅览');


	}elseif($_GPC['op'] == 'queue'){
		
		for( $i = 0;$i<3;$i++ ){
			$cache = Util::getCache('queue','q');
					
			if( empty( $cache ) || $cache['time'] < ( time() - 40 ) ){
				if( $i == 2 ){
					$url = Util::createModuleUrl('message',array('op'=>1));
					$res = Util::httpGet($url,'', 1);
					Util::echoResult(200,'好',array($url));
				}
				sleep(1);
			}else{
				Util::echoResult(201,'好',array($url));
			}			
			
		}

	}


	Util::echoResult(201,'没有任何数据');


