<?php 

/*
	用户表类
*/
class model_user 
{	
	
	static $oauth;

	//初始化用户数据
	static function initUserInfo(){
		global $_W,$_GPC;
		
		$_W['set'] = Util::getModuleConfig();
		$_W['set']['kefupo'] = iunserializer( $_W['set']['kefupo'] );
		self::wxLimit();

		if( !empty( $_W['openid'] ) && $_W['container'] == 'wechat' && strlen( $_W['openid'] ) > 15 ) {
			$_W['dev'] = 'wx';
		}else{
			$_W['dev'] = 'wap';
		}

		// 必须注册
		if( !in_array( $_GPC['do'] ,array('pagelist','ajaxdeal')) ) self::checkauth();

		// 不能用下面这个，如果账户绑定了openid在wap任然会有真实openid
		/*if( !empty( $_W['openid'] ) && is_numeric( $_W['openid'] ) ) {
			$_W['dev'] = 'wap';
		}elseif( !empty( $_W['openid'] ) && is_string( $_W['openid'] ) ){
			$_W['dev'] = 'wx';
		}else{
			self::alertWechatLogin();
		}*/
		// 不用能用is_string
					

		if( empty( $_W['member']['uid'] ) && !in_array( $_GPC['do'] ,array('pagelist','ajaxdeal')) ) self::alertWechatLogin();
		//$_W['dev'] = 'wx';			
		
		$inviteid = intval( $_GPC['zfuid'] );
		$userinfo = self::getSingleUser( $_W['member']['uid'] ); //查询缓存
							
		if( !in_array( $_GPC['do'] ,array('pagelist','ajaxdeal')) ){
			
			if(!empty($userinfo)){
				if($userinfo['status'] == 2){
					header( "Location: http://www.baidu.com" );
					exit();
				}
				
				// 设置借权
		        if ( $_W['dev'] == 'wx' && !empty( $_W['set']['appid'] ) && $_W['account']['key'] != $_W['set']['appid'] && strlen( $_W['openid'] ) > 10 ) {
					if (empty($userinfo['authopenid'])) {
						header("location: ".Util::createModuleUrl('auth'));
						die;
					}
		        }

		        // strpos 禁止微信里修改wap设置的头像
				if( ( ($userinfo['logintime'] < time()-12*3600 && strpos($userinfo['headimgurl'], 'http') !== false ) || empty( $userinfo['headimgurl'] )) && $_W['dev'] == 'wx' ){
					$fans = pdo_get('mc_mapping_fans',array('uniacid'=>$_W['uniacid'],'openid'=>$_W['openid']));
					$tag = iunserializer( base64_decode( $fans['tag'] ) );
					
					if( !empty( $tag['headimgurl'] ) ){
						$data = array('logintime'=>TIMESTAMP,'nickname' => $fans['nickname'],'headimgurl' => $tag['headimgurl']);
						pdo_update('zofui_task_user', $data, array('id' => $userinfo['id']));
						Util::deleteCache('u',$_W['member']['uid']);
						$userinfo = self::getSingleUser($_W['member']['uid']);
					}
				}
					
				if( empty( $userinfo['parent'] ) && $inviteid != $userinfo['id'] && !empty( $inviteid ) && $_W['set']['isdown'] == 0 ){
					$parent = pdo_get('zofui_task_user',array('uniacid'=>$_W['uniacid'],'id'=>$inviteid));
					if( !empty( $parent ) && $parent['parent'] != $userinfo['id'] ){ // 避免出现互相为上下级关系
						pdo_update('zofui_task_user',array('parent'=>$inviteid), array('id' => $userinfo['id']));
						Util::deleteCache('u',$_W['member']['uid']);
						$userinfo = self::getSingleUser($_W['member']['uid']);	
						Message::getdown($parent['uid'],$parent['openid'],$userinfo['nickname']);
					}		
				}

				if( empty( $userinfo['uid'] ) && !empty( $_W['member']['uid'] ) ) {
					pdo_update('zofui_task_user', array('uid'=>$_W['member']['uid']), array('id' => $userinfo['id']));
					Util::deleteCache('u',$_W['member']['uid']);
					$userinfo = self::getSingleUser($_W['member']['uid']);
				}

			}else{
				
				// 进入限制
				self::passwhite();
				
				$where = array('uid'=>$_W['member']['uid'],'uniacid'=>$_W['uniacid']);
				$userinfo = pdo_get('zofui_task_user',$where);
				if (empty($userinfo['id']) && !empty( $_W['member']['uid'] )) {

					if( $_W['dev'] == 'wx' ) {
						$fans = pdo_get('mc_mapping_fans',array('uniacid'=>$_W['uniacid'],'openid'=>$_W['openid']));
						$tag = iunserializer( base64_decode( $fans['tag'] ) );
					}

					$parentid = 0;
					if( !empty( $inviteid ) && $_W['set']['isdown'] != 2 ){
						$parent = pdo_get('zofui_task_user',array('uniacid'=>$_W['uniacid'],'id'=>$inviteid));
						if( !empty( $parent )  ) $parentid = $inviteid;	
					}
					
					$data = array(
						'uniacid' => $_W['uniacid'],
						'openid' => strlen($_W['openid']) > 15 ? $_W['openid'] : '',
						'nickname' => $fans['nickname'],
						'headimgurl' => $tag['headimgurl'],
						'city' => $tag['city'],
						'sex' => $tag['sex'],
						'logintime' => TIMESTAMP,
						'uid' => $_W['member']['uid'],
						'parent' => $parentid,
						'createtime' => TIMESTAMP,
						'qq' => $_W['member']['qq'],
					);
					$res = pdo_insert('zofui_task_user', $data);
					$userinfo = self::getSingleUser( $_W['member']['uid'] );
					
					if( $res && $_W['set']['newgive'] > 0 ){
						$mres = self::updateUserCredit($userinfo['uid'],$_W['set']['newgive'],2,1);
						if( $mres ){
							// 资金记录
							model_money::insertMoneyLog($userinfo['openid'],$_W['set']['newgive'],1,43,$userinfo['uid']);
							$_W['newgive'] = $_W['set']['newgive'];
						}
					}

					if( $parentid > 0 ) Message::getdown($parent['uid'],$parent['openid'],$userinfo['nickname']);
				}else{
					echo '数据丢了';die;
				}
			}
		}
	
		// 关注
		$_W['issub'] = 1;
		if( $_W['set']['issub'] == 1 && $_W['dev'] == 'wx' ){
			$fans = pdo_get('mc_mapping_fans',array('uniacid'=>$_W['uniacid'],'openid'=>$_W['openid']),array('follow'));
			if( $fans['follow'] == 0 ) $_W['issub'] = 0;
		}
		
		// 初始活跃度
		$_W['todystart'] = strtotime( date('Y-m-d',TIMESTAMP) );
		if( $userinfo['uptime'] < $_W['todystart'] ){
			$activity =  empty( $_W['set']['activity'] ) ? 10 : intval( $_W['set']['activity'] );

			$level = self::levelRes($userinfo,$_W['set']);
			if( $level == 1 && $_W['set']['activitya'] > 0 ){
				$activity = $_W['set']['activitya'];
			}
			if( $level == 2 && $_W['set']['activityb'] > 0 ){
				$activity = $_W['set']['activityb'];
			}
			pdo_update('zofui_task_user',array('activity'=>$activity,'uptime'=>TIMESTAMP),array('id'=>$userinfo['id']));
			
			Util::deleteCache('u',$_W['member']['uid']);
			$userinfo = self::getSingleUser( $_W['member']['uid'] );
		}

		// 获取区域
		$_W['islimit'] = 0; // 不需要获取
		$sessionstr = $_W['member']['uid'].'a'.$_W['uniacid'];
		if( empty( $_SESSION[$sessionstr] ) ){
			$_W['islimit'] = 1; // 需要获取
		}
		
		if( $_W['set']['ismobile'] == 1 && $_W['dev'] == 'wx' ) {
			
			if( empty( $_W['member']['mobile'] ) ){
				$_W['needbindmobile'] = 1;
			}
		}


		// 余额名称
		$_W['cname'] = $_W['set']['moneytype'] == 0 ? '余额' : '积分';
		$_W['cper'] = $_W['set']['moneytype'] == 0 ? '元' : '积分';
		// +访问记录
		self::insertTimes();

		return $userinfo;
	}

	// 参数
	static function wInit(){
		global $_W;
		$_W['set'] = Util::getModuleConfig();
		
		$_W['cname'] = $_W['set']['moneytype'] == 0 ? '余额' : '积分';
		$_W['cper'] = $_W['set']['moneytype'] == 0 ? '元' : '积分';
	}

	static function devInit(){
		global $_W;
		if( !empty( $_W['openid'] ) && $_W['container'] == 'wechat' && strlen( $_W['openid'] ) > 15 ) {
			$_W['dev'] = 'wx';
		}else{
			$_W['dev'] = 'wap';
		}
	}	

	// 会员等级
	static function levelRes($userinfo,$set){
		
		if( empty($set['ulevel']) ) return 0;
		if( empty( $userinfo ) ) return 0;
		if( empty( $userinfo['level'] ) ) return 0;
		if( $userinfo['level'] == 1 ){
			if( $userinfo['utime'] <= TIMESTAMP ){
				return 0;
			}
			return 1;
		}
		if( $userinfo['level'] == 2 ){
			if( $userinfo['utime'] <= TIMESTAMP ){
				return 0;
			}
			return 2;
		}
		return 0;
	}

	// 进入限制
	static function passwhite(){
		global $_W,$_GPC;

		if( $_W['set']['intype'] >= 1 && empty( $_GPC['zfuid'] ) ) {

			if($_W['set']['intype'] == 1){
				die;
			}
			if($_W['set']['intype'] == 2){
				header( "Location: http://www.baidu.com" );
				exit();
			}
			if($_W['set']['intype'] == 3){
				echo '<img src="../addons/zofui_taskself/public/images/error.png" style="width:100%">';
				exit();
			}				
		}
	}

	//查询一条用户数据,传入openid
	static function getSingleUser($uid){
		global $_W;
		$cache = Util::getCache('u',$uid);
		if( empty( $cache['id'] ) ){
			$where = array('uniacid'=>$_W['uniacid']);
			$where['uid'] = $uid;
			$cache = pdo_get('zofui_task_user',$where);
			if( !empty( $cache ) ) {
				$cache['verifyform'] = iunserializer( $cache['verifyform'] );
				$cache['headimgurl'] = tomedia( $cache['headimgurl'] );
				if( empty($cache['headimgurl']) ){
					$cache['headimgurl'] = '../addons/zofui_taskself/public/images/dhead.png';
				}
				Util::setCache('u',$uid,$cache);
			}
		}
		return $cache;
		//需删除缓存
	}

	//传入openid
	static function openid2uid($openid){
		global $_W;
		$cache = Util::getCache('o2u',$openid);
		if( empty( $cache ) ){
			$where = array('uniacid'=>$_W['uniacid']);
			$where['openid'] = $openid;
			$u = pdo_get('zofui_task_user',$where,array('uid'));
			if( !empty( $u['uid'] ) ) {
				$cache = $u['uid'];
				Util::setCache('o2u',$openid,$cache);
			}
		}
		return $cache;
	}

	// 绑定上下级 用在扫码和关键词内 openid 被推荐者，parentinfo 推荐者
	static function bindDown( $openid,$parentinfo ){
		global $_W;
		if( $openid == $parentinfo['openid'] ) return false;


		$userinfo = pdo_get('zofui_task_user',array('openid'=>$openid,'uniacid'=>$_W['uniacid']));
		$set = Util::getModuleConfig();

		if ( empty($userinfo['id']) && !empty($openid) ) {
			$fans = pdo_get('mc_mapping_fans',array( 'uniacid'=>$_W['uniacid'],'openid'=>$openid ));
			$tag = iunserializer( base64_decode( $fans['tag'] ) );

			if( empty( $fans ) ) return false;

			$parentid = 0;
			if( !empty( $parentinfo['id'] ) && $set['isdown'] != 2 && $parentinfo['parent'] != $userinfo['id'] ){
				$parentid = $parentinfo['id'];
			}
			
			$activity =  empty( $set['activity'] ) ? 10 : intval( $set['activity'] );

			$data = array(
				'uniacid' => $_W['uniacid'],
				'openid' => $openid,
				'nickname' => $fans['nickname'],
				'headimgurl' => $tag['headimgurl'],
				'city' => $tag['city'],
				'sex' => $tag['sex'],
				'logintime' => TIMESTAMP,
				'uid' => $fans['uid'],
				'parent' => $parentid,
				'createtime' => TIMESTAMP,
				'activity' => $activity,
			);
			$res = pdo_insert('zofui_task_user', $data);
			Util::deleteCache('u',$fans['uid']);

			if( $res && $set['newgive'] > 0 ){
				$mres = self::updateUserCredit($fans['uid'],$set['newgive'],2,1);
				if( $mres ){
					// 资金记录
					model_money::insertMoneyLog($openid,$set['newgive'],1,43,$fans['uid']);
				}
			}

			if( $parentid > 0 ) Message::getdown( $parentinfo['uid'],$parentinfo['openid'],$fans['nickname'] );
			
			return true;

		}elseif( !empty( $userinfo['id'] ) && empty( $userinfo['parent'] ) && $parentinfo['id'] != $userinfo['id'] && !empty( $parentinfo['id'] ) && $set['isdown'] == 0 ){
			
			if( !empty( $parentinfo ) && $parentinfo['parent'] != $userinfo['id'] ){ // 避免出现互相为上下级关系
				pdo_update('zofui_task_user',array('parent'=>$parentinfo['id']), array('id' => $userinfo['id']));
				Util::deleteCache('u',$userinfo['uid']);
				
				Message::getdown($parentinfo['uid'],$parentinfo['openid'],$userinfo['nickname']);

				return true;
			}

		}
		return false;

	}


	//查询会员余额和积分
	static function getUserCredit($openid){	
		global $_W;
		load() -> model('mc');
		$uid = mc_openid2uid($openid);

		$set = Util::getModuleConfig();

		$setting = uni_setting($_W['uniacid'], array('creditbehaviors'));
		$credtis =  mc_credit_fetch($uid);

		if( $set['moneytype'] == 0 ){
			$cache = array('uid'=>$uid,'credit1'=>$credtis[$setting['creditbehaviors']['activity']],'credit2'=>$credtis[$setting['creditbehaviors']['currency']]);; // 1是积分 2是余额
		}else{
			$cache = array('uid'=>$uid,'credit1'=>$credtis[$setting['creditbehaviors']['currency']],'credit2'=>$credtis[$setting['creditbehaviors']['activity']]);; // 1是积分 2是余额
		}
		
		return $cache;
	}

	// 改变会员余额 和 积分 type 1积分 2余额
	// from 1 提现
	static function updateUserCredit($openid,$value,$type,$from,$mark='zofui_tasktb'){
		global $_W;
		load() -> model('mc');
		$set = Util::getModuleConfig();

		if( is_string( $openid ) ) $uid = mc_openid2uid($openid);
		if( is_numeric( $openid ) ) $uid = $openid;

		$setting = uni_setting($_W['uniacid'], array('creditbehaviors'));
		if( $type == 1 ){
			
			if( $set['moneytype'] == 0 ){
				$creditbehaviors = $setting['creditbehaviors']['activity'];
			}else{
				$creditbehaviors = $setting['creditbehaviors']['currency'];
			}
		}elseif( $type == 2 ){
			if( $set['moneytype'] == 0 ){
				$creditbehaviors = $setting['creditbehaviors']['currency'];
			}else{
				$creditbehaviors = $setting['creditbehaviors']['activity'];
			}
		}else{
			return false;
		}
		$result = mc_credit_update($uid, $creditbehaviors, $value,array($uid,$mark,'zofui_tasktb',$from));
				
		$res = is_error($result);
		return !$res;
	}

	static function insertTimes(){
		global $_W;
		if( $_W['isajax'] ) return;
		pdo_query("UPDATE ".tablename('zofui_tasktb_scan')." SET `times` = `times` + 1 WHERE `uniacid` = '{$_W['uniacid']}' ");
		
	}

	static function getMyData( $uuid,$uid ){
		global $_W;

		// 发的任务
		//$cache = Util::getCache('d',$openid);

		if( empty( $cache ) ){
			// 未开始的
			$cache['pubed1'] = Util::countDataNumber('zofui_tasktb_task',array('uniacid'=>$_W['uniacid'],'userid'=>$uuid),' AND ( `status` = 1 OR `isstart` = 1 ) ');
			// 进行中的
			$cache['pubed2'] = Util::countDataNumber('zofui_tasktb_task',array('status'=>0,'start<'=>TIMESTAMP,'iscount'=>0,'uniacid'=>$_W['uniacid'],'userid'=>$uuid));			
			// 已结算的
			$cache['pubed3'] = Util::countDataNumber('zofui_tasktb_task',array('iscount'=>1,'uniacid'=>$_W['uniacid'],'userid'=>$uuid));

			// 待回复的任务
			$cache['takeda'] = Util::countDataNumber('zofui_tasktb_taked',array('status'=>0,'endtime>'=>TIMESTAMP,'uniacid'=>$_W['uniacid'],'userid'=>$uuid));
			// 待审核
			$cache['takedb'] = Util::countDataNumber('zofui_tasktb_taked',array('status'=>1,'endtime>'=>TIMESTAMP,'uniacid'=>$_W['uniacid'],'userid'=>$uuid));
			// 已完成
			$cache['takedc'] = Util::countDataNumber('zofui_tasktb_taked',array('status'=>2,'endtime>'=>TIMESTAMP,'uniacid'=>$_W['uniacid'],'userid'=>$uuid));

			// 私包任务
			$cache['ptask'] = Util::countDataNumber( 'zofui_tasktb_privatetask',array('isend'=>0,'uniacid'=>$_W['uniacid'])," AND ( `pubuid` = '".$uuid."' OR `acceptuid` = '".$uuid."' )" );

			// 我的小弟
			$cache['down'] = Util::countDataNumber('zofui_task_user',array('uniacid'=>$_W['uniacid'],'parent'=>$uid));

			// 未读消息
			/*$cache['imess'] = Util::countDataNumber('zofui_tasktb_imess',array('uniacid'=>$_W['uniacid'],'status'=>0));*/			

			// 我的试用
			if( $_W['set']['isusetask'] == 1 ){
				$cache['myuse1'] = Util::countDataNumber('zofui_tasktb_usetasklog',array('status'=>0,'uniacid'=>$_W['uniacid'],'userid'=>$uuid));
				$cache['myuse2'] = Util::countDataNumber('zofui_tasktb_usetasklog',array('status'=>1,'uniacid'=>$_W['uniacid'],'userid'=>$uuid));
				$cache['myuse3'] = Util::countDataNumber('zofui_tasktb_usetasklog',array('status'=>4,'uniacid'=>$_W['uniacid'],'userid'=>$uuid));
				$cache['myuse4'] = Util::countDataNumber('zofui_tasktb_usetasklog',array('status'=>5,'uniacid'=>$_W['uniacid'],'userid'=>$uuid));
				$cache['myuse5'] = Util::countDataNumber('zofui_tasktb_usetasklog',array('status'=>6,'uniacid'=>$_W['uniacid'],'userid'=>$uuid));
			}

			if( $_W['set']['istbtask'] == 1 ){

				// 未开始
				$cache['tbpubed1'] = Util::countDataNumber('zofui_tasktb_tbtask',array('iscount'=>0,'uniacid'=>$_W['uniacid'],'userid'=>$uuid),' AND ( `status` IN(1,2) OR `isstart` = 1 ) ');
				// 进行中的
				$cache['tbpubed2'] = Util::countDataNumber('zofui_tasktb_tbtask',array('status'=>0,'isstart'=>0,'iscount'=>0,'uniacid'=>$_W['uniacid'],'userid'=>$uuid));		
				// 已结算
				$cache['tbpubed3'] = Util::countDataNumber('zofui_tasktb_tbtask',array('iscount'=>1,'uniacid'=>$_W['uniacid'],'userid'=>$uuid));

				// 待回复的任务
				$cache['taked'] = Util::countDataNumber('zofui_tasktb_taked',array('status'=>0,'endtime>'=>TIMESTAMP,'uniacid'=>$_W['uniacid'],'userid'=>$uuid));

				// 待处理的担保任务
				$cache['takedtb'] = Util::countDataNumber('zofui_tasktb_tbtaked',array('isend'=>0,'uniacid'=>$_W['uniacid'],'userid'=>$uuid),' AND `status` IN(2,4,6,7) ');
				
			}

		}
		return $cache;
	}

	// 查询任务
	static function getAllUser($where,$page,$num,$order,$iscache,$pager,$select,$str=''){
		global $_W;		
		$data = Util::structWhereStringOfAnd($where,'a');

		$commonstr = tablename('zofui_task_user') ."  AS a LEFT JOIN ".tablename('mc_members')." AS b ON a.uid = b.uid AND a.uniacid = b.uniacid WHERE ".$data[0];
		$countStr = "SELECT  COUNT(*) FROM ".$commonstr;
		$selectStr =  "SELECT $select FROM ".$commonstr;
		$res = Util::fetchFunctionInCommon($countStr,$selectStr,$data[1],$page,$num,$order,$iscache,$pager,$str);
		return $res;
	}

	static function agent(){
		if(strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone') || strpos($_SERVER['HTTP_USER_AGENT'], 'iPad') || strpos($_SERVER['HTTP_USER_AGENT'], 'ipod') ){
		    return 'iphone';
		}else{
		    return 'android';
		}
	}

	static function isWechat(){
		if (stripos( $_SERVER['HTTP_USER_AGENT'] , 'MicroMessenger') !== false) {
			return true;
		} else {
			return false;
		}
	}

	static function intoUserIc($user,$icarr,$isauth){
		global $_W;
		
		$arr = array();

		if( !empty($icarr) ){
			foreach ($icarr as $v) {
				$arr[] = $v;
			}
		}

		if( $isauth ){
			$verifyform = iunserializer($user['verifyform']);
			if( !empty($verifyform) ){
				foreach ($verifyform as $v) {

					$form = pdo_get('zofui_tasktb_authform',array('id'=>$v['id']));
					if( !empty($form['useric']) ){
						$usericarr = iunserializer($form['useric']);
					}

					if( !empty($usericarr) ){
						foreach ($usericarr as $vv) {
							
							if( !in_array($vv, $arr) ){
								$arr[] = $vv;
							}
							
						}
					}
				}
			}
		}
		
		if( !empty($arr) ){
			foreach ($arr as $v) {
				$isset = pdo_get('zofui_tasktb_userics',array('uid'=>$user['uid'],'icid'=>$v));
				if( empty($isset) ){
					$useric = array(
						'uniacid' => $_W['uniacid'],
						'uid' => $user['uid'],
						'icid' => $v,
					);
					pdo_insert('zofui_tasktb_userics',$useric);
				}
			}
		}
	}

	static function wxLimit(){
		global $_W,$_GPC;

		if( $_W['set']['isinwx'] == 1 && $_W['container'] == 'wechat' && $_GPC['do'] != 'bindaccount' ){
			$agent = model_user::agent();
			if( $agent == 'iphone' ){
				echo '<div style="width:100%;height:100%;background:#000000;"><img src="../addons/zofui_taskself/public/images/weixin2.png" style="width:100%;background:#000000;"></div>';die;
			}else{
				echo '<div style="width:100%;height:100%;background:#000000;"><img src="../addons/zofui_taskself/public/images/weixin1.png" style="width:100%;background:#000000;"></div>';die;
			}
		}		
	}

	// 是否管理员
	static function isAdmin( $uid ){
		global $_W;
		if( !empty( $_W['set']['admin'] ) ){
			$admin = iunserializer( $_W['set']['admin'] );
			if( is_array( $admin ) ){
				foreach ( $admin as $v ) {
					if( $v['userid'] == $uid ){
						return true;
						break;
					}
				}
			}
		}
		return false;
	}
	

	// 是否认证 0未认证 1审核中 2已认证
	static function isAuth( $userinfo,$set ){
		global $_W;
		if( empty($set['isauth']) ) return 0;
		if( $set['authtime'] > 0  ){ //有时间限制
			if( $userinfo['verifyend'] > TIMESTAMP ) return 2; //已认证
			if( $userinfo['verifystatus'] == 1 ) return 1; //审核中
		}else{
			if( $userinfo['verifystatus'] == 1 ) return 1; //审核中
			if( $userinfo['verifystatus'] == 2 ) return 2; //已认证
		}
		return 0;
	}

	static function isKa(){
		global $_W;
		if( in_array($_W['siteroot'], array('http://127.0.0.4/','http://www.tmzhe.com/')) ){
			return true;
		}
		return false;
	}

	//非微信端提示
	static function alertWechatLogin(){
		global $_W;
		$falg = '';
		
		$qrcode = tomedia('qrcode_'.$_W['acid'].'.jpg');
		die("<!DOCTYPE html>
            <html><head><meta name='viewport' content='width=device-width, initial-scale=1, user-scalable=0'>
                <title>提示</title><meta charset='utf-8'><meta name='viewport' content='width=device-width, initial-scale=1, user-scalable=0'><link rel='stylesheet' type='text/css' href='https://res.wx.qq.com/connect/zh_CN/htmledition/style/wap_err1a9853.css'>
            </head>
            <body>
            <div class='page_msg'><div class='inner'><span class='msg_icon_wrp'><i class='icon80_smile'></i></span><div class='msg_content'><h4>请关注公众号后再打开页面".$falg."</h4><br><img width='200px' src='".$qrcode."'></div></body></html></div></div></div>
            </body></html>");
	}
	
	static function checkauth() {
		global $_W,$_GPC, $engine;
		load()->model('mc');
		load()->model('account');
		
		if( $_W['set']['waptype'] == 1 && $_W['dev'] == 'wap' ){

			$session = json_decode(authcode($_GPC['__stsessiona']), true);

			if (is_array($session)) {
				$user = pdo_get('mc_members',array('uniacid'=>$_W['uniacid'],'uid'=>$session['uid']));
				if (is_array($user) && !empty($user['password']) && $session['hash'] == md5($user['password'] . $_W['config']['setting']['authkey'].'vrewewvw')) {
					if(_mc_login(array('uid' => intval($user['uid'])))) {
						$_W['member']['qq'] = $user['qq'];
						return true;
					}
				} else {
					isetcookie('__stsessiona', false, -100);
				}
				unset($user);
			}
			unset($session);

		}else{
			if(!empty($_W['member']) && (!empty($_W['member']['mobile']) || !empty($_W['member']['email']))) {
				return true;
			}

			if(!empty($_W['openid'])) {
				$fan = mc_fansinfo($_W['openid'], $_W['acid'], $_W['uniacid']);
				if (empty($fan) && $_W['account']['level'] == ACCOUNT_SERVICE_VERIFY) {
					$fan = mc_oauth_userinfo();
					if (!empty($fan['openid'])) {
						$fan = mc_fansinfo($fan['openid']);
					}
				}

				if (empty($fan['uid'])) {
					$setting = uni_setting($_W['uniacid'], array('passport'));
					if (!isset($setting['passport']) || empty($setting['passport']['focusreg'])) {
						$reg_members = mc_init_fans_info($_W['openid'], true);
						$fan['uid'] = $reg_members['uid'];
					}
				}

				if(_mc_login(array('uid' => intval($fan['uid'])))) {
					return true;
				}
				if (defined('IN_API')) {
					$GLOBALS['engine']->died("抱歉，您需要先登录才能使用此功能，点击此处 <a href='".__buildSiteUrl(url('auth/login')) ."'>【登录】</a>");
				}
			}
		}
		
		$forward = base64_encode($_SERVER['QUERY_STRING']);
		if($_W['isajax']) {
			$result = array();
			$result['url'] = Util::createModuleUrl('auth_login', array('forward' => $forward), true);
			$result['act'] = 'redirect';
			exit(json_encode($result));
		} else {
			
			header("location: " . Util::createModuleUrl('auth_login', array('forward' => $forward)), true);
		}
		exit;
	}

	
}
	
	
	
