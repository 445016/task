<?php 
	global $_W,$_GPC;
	session_start();

	model_user::wInit();
	if($_GPC['op'] == 'deletecache'){ 
		if( $_GPC['type'] == 'cache' ) {
			$res = cache_clean();
			
			$userdir = MODULE_ROOT.'/userfile/'.$_W['uniacid'].'/';

			load()->func('file');
			if( is_dir( $userdir ) ){
				rmdirs( $userdir,false );
			}
		}elseif( $_GPC['type'] == 'poster' ){
			Util::rmdirs( TSTB_ROOT.'poster/'.$_W['uniacid'].'/' );
			Util::rmdirs( TSTB_ROOT.'posterwap/'.$_W['uniacid'].'/' );
		}

		
		die('1');
	}
	
	
	elseif($_GPC['op'] == 'clearauth'){ //清除借权	

		$res = pdo_update('zofui_task_user',array('authopenid'=>''),array('uniacid'=>$_W['uniacid']));

		if( $res ){
			cache_clean();
			
		}
		Util::echoResult(200,'已清除');

	}elseif($_GPC['op'] == 'editvalue'){ //修改
		$id = intval($_GPC['id']);
	
		
		if($_GPC['name'] == 'slidernumber'){
			pdo_update('zofui_tasktb_slider',array('number'=>intval( $_GPC['value'] )),array('id'=>$id,'uniacid'=>$_W['uniacid']));
			Util::deleteCache('slider','all');

		}elseif($_GPC['name'] == 'tasksortnumber'){
			pdo_update('zofui_tasktb_tasksort',array('number'=>intval( $_GPC['value'] )),array('id'=>$id,'uniacid'=>$_W['uniacid']));
			Util::deleteCache('tasksort','all');

		}elseif($_GPC['name'] == 'tasksortname'){
			pdo_update('zofui_tasktb_tasksort',array('name'=>$_GPC['value']),array('id'=>$id,'uniacid'=>$_W['uniacid']));
			Util::deleteCache('tasksort','all');
			
		}elseif($_GPC['name'] == 'adnumber'){
			pdo_update('zofui_tasktb_ad',array('number'=>intval( $_GPC['value'] )),array('id'=>$id,'uniacid'=>$_W['uniacid']));
			Util::deleteCache('ad','all');
		
		}elseif($_GPC['name'] == 'stepname'){
			pdo_update('zofui_tasktb_step',array('name'=>$_GPC['value']),array('id'=>$id,'uniacid'=>$_W['uniacid']));
		}


	}

	elseif($_GPC['op'] == 'dealprivate'){ 	


		$type = intval($_GPC['type']);
		$reason = $_GPC['reason'];
		$taskid = intval($_GPC['taskid']);
		
		$taskinfo = pdo_get('zofui_tasktb_privatetask',array('id'=>$taskid,'uniacid'=>$_W['uniacid']));
		if($taskinfo['status'] != 9) Util::echoResult(201,'此任务不能操作');
		
		if($type == 1) $status = 13; //判给雇员
		if($type == 2) $status = 14; //判给雇主
		
		//改变任务状态
		$res = pdo_update('zofui_tasktb_privatetask',array('status'=>$status,'admindealtime'=>time(),'admindealresult'=>$reason,'isend'=>1),array('id'=>$taskinfo['id']));
		
		if($type == 1) $res = model_privatetask::completeTaskInajaxdealAndCrontab($taskinfo,$status,$this->module['config'],'web'); //判给雇员，发放资金等、这里已发通知
		
		if($type == 2) {
			$res = model_privatetask::backMoneyToBossInPrivateTask($taskinfo); //退资金
			Message::cmessage($taskinfo['bossuid'],$taskinfo['bossopenid'],$taskinfo['tasktitle'],'admindealtoboss',$taskinfo['id']);	//发通知
		}
		
		if($res) Util::echoResult(200,'好');
		Util::echoResult(201,'操作失败');

	}
	elseif($_GPC['op'] == 'checkqueue'){ //检查计划任务
		
		$cache = Util::getCache('queue','q');

		$back = Util::getCache('back','status');

		if( empty( $cache ) ){
			$res['queue']['status'] = 201;
		}else{
			$res['queue']['status'] = 200;
		}

		if( empty( $back ) ){
			$res['back']['status'] = 201;
		}elseif( $back == 1 ){
			$res['back']['status'] = 200;
		}else{
			$res['back']['status'] = 202;
			$res['back']['res'] = $back;
		}

		Util::echoResult(200,'好',$res);
	}	


	// 回复留言
	elseif ($_GPC['op'] == 'replymess') {

		$data['parent'] = intval( $_GPC['id'] );
		$data['content'] = $_GPC['content'];

		if( empty( $data['content'] ) ) Util::echoResult(201,'请输入回复内容');

		$parent = pdo_get('zofui_tasktb_taskmessage',array('uniacid'=>$_W['uniacid'],'id'=>$data['parent']));
		if( empty( $parent ) ) Util::echoResult(201,'请重试');

		if( empty($parent['type']) || $parent['type'] == 2 ) {
			$task = pdo_get('zofui_tasktb_task',array('uniacid'=>$_W['uniacid'],'id'=>$parent['taskid']));
		}else{
			$task = pdo_get('zofui_tasktb_tbtask',array('uniacid'=>$_W['uniacid'],'id'=>$parent['taskid']));
		}
		
		if( empty( $task ) ) Util::echoResult(201,'任务不存在，不能再回复');
		if( $task['status'] != 0 ) Util::echoResult(201,'任务未上架，不能再回复');
		if( $task['end'] < TIMESTAMP ) Util::echoResult(201,'任务已结束，不能再回复');
		if( $task['iscount'] == 1 ) Util::echoResult(201,'任务已结束，不能再回复');

		$data['type'] = $parent['type'];
		$data['uniacid'] = $_W['uniacid'];
		$data['openid'] = $task['puber'];
		$data['userid'] = $task['userid'];
		$data['taskid'] = $task['id'];
		$data['time'] = TIMESTAMP;

		$res = pdo_insert('zofui_tasktb_taskmessage',$data);
		
		if( $res ) {
			Message::replymsg($parent['userid'],$parent['openid'],$task['id'],$data['content'],$task['title'],'');
			Util::echoResult(200,'已回复',$res);
		}
		Util::echoResult(200,'回复失败',$res);
		
	}


	// 拉黑
	elseif ($_GPC['op'] == 'findadmin') {

		$nickname = $_GPC['nick'];
		$sql = " SELECT * FROM ".tablename('zofui_task_user')." WHERE uniacid = ".$_W['uniacid']." AND `id` = ".$nickname;
		$user = pdo_fetch($sql);
		
		if(empty($user)){
			Util::echoResult(201,'没有找到');
		}else{

			if( empty($user['openid']) ) Util::echoResult(201,'此会员没有微信粉丝数据，不能绑定。微信内的粉丝会员才能绑定');

			$admin['headimgurl'] = $user['headimgurl'];
			$admin['nick'] = $user['nickname'];
			$admin['uid'] = $user['uid'];
			
			Util::echoResult(200,'好',$admin);
		}
		
		
	}

	elseif( $_GPC['op'] == 'addtask' ){

		$set = Util::getModuleConfig();
		$power = iunserializer( $set['power'] );
		if( $_W['role'] != 'founder' && $_W['role'] != 'manager' && !empty( $power ) && in_array('pub', $power) ) die;

		$_GPC = Util::trimWithArray($_GPC);

		$api = 'http://api.zofui.net/app/index.php?c=taskwap&a=addtask';
		$key = pdo_get('zofui_tasktb_codekey');
		$res = Util::httpPost($api,array('site'=>$_W['siteroot'],'en'=>MODULE,'for'=>$_GPC,'setop'=>'setting','key'=>$key['key']));			
		$res = json_decode( $res,true );
		$data = $res['data'];

		if(  $_GPC['taskid'] > 0 || $_GPC['gewqgww'] == 2 ){
			if( !empty($_GPC['step']) && is_array($_GPC['step']) ){
				$ind = array(
					'uniacid' => $_W['uniacid'],
					'step' => iserializer($_GPC['step']),
				);
				pdo_insert('zofui_tasktb_step',$ind);
				$data['stepid'] = pdo_insertid();
			}
		}else{
			if( $_GPC['gewgwecfwqf'] > 0 ){
				$data['stepid'] = $_GPC['gewgwecfwqf'];
			}
		}

		if( $data['isarealimit'] == 1 && empty( $data['country'] ) ) Util::echoResult(201,'请选择可接任务的区县');
		if( $data['isarealimit'] == 2 && empty( $data['city'] ) ) Util::echoResult(201,'请选择可接任务的城市');

		if( $data['num'] <= 0 )  Util::echoResult(201,'任务数量不能小于等于0');
		if( $data['money'] <= 0 )  Util::echoResult(201,'任务赏金不能小于等于0');
		if( $data['end'] <= time() )  Util::echoResult(201,'结束时间必须大于现在时间');
		if( $data['end'] < $data['start'] )  Util::echoResult(201,'结束时间必须大于开始时间');

		$continueday = intval( $_GPC['days'] );
		if( $data['continue'] == 1 && $continueday <= 0 ) Util::echoResult(201,'还没设置连续任务天数');

		if( $data['replytime'] >= $this->module['config']['autoconfirm']*60 ) Util::echoResult(201,'停留时间不能大于平台设置的自动结束时间');

		if( $_GPC['taskid'] > 0 ){ // 编辑

			$task = pdo_get('zofui_tasktb_task',array('uniacid'=>$_W['uniacid'],'id'=>$_GPC['taskid']));
			if( empty( $task ) ) Util::echoResult(201,'任务不存在');
			if( $task['iscount'] == 1 ) Util::echoResult(201,'已结算任务不能再编辑');

			$res = pdo_update('zofui_tasktb_task',$data,array('uniacid'=>$_W['uniacid'],'id'=>$task['id']));
		}else{

			$data['continue'] = intval( $_GPC['continue'] );
			$data['createtime'] = TIMESTAMP;
			$data['idcode'] = model_task::taskCode();
			$data['uniacid'] = $_W['uniacid'];

			if( $data['continue'] == 1 ){ //连续发布
				$continue['uniacid'] = $_W['uniacid'];
				$continue['money'] = sprintf( '%.2f', $_GPC['ewai']);
				$continue['totalnum'] = $data['num'];
				$continue['totalmoney'] = $data['num']*$continue['money'];
				pdo_insert('zofui_tasktb_continue',$continue);
				$data['continueid'] = pdo_insertid();
			}
			
			$res = pdo_insert('zofui_tasktb_task',$data);
			$id = pdo_insertid();

			if( $data['continue'] == 1 ){
				$today = strtotime( date('Y-m-d',TIMESTAMP) );

				for ($i=0; $i < $continueday; $i++) {
					$newdata = $data;
					$newdata['isstart'] = 1;
					$newdata['start'] = $data['start'] + 24*3600*($i+1);
					$newdata['end'] = $newdata['end'] + 24*3600*($i+1);
					$newdata['idcode'] = model_task::taskCode();
					
					pdo_insert('zofui_tasktb_task',$newdata);
				}
			}

			// 新任务通知
			//model_task::setMessage( $this->module['config'],$id );
			if( $res && $data['falsepuber'] > 0 ){
				Util::addOrMinusOrUpdateData('zofui_tasktb_puber',array('pubnum'=>1),$data['falsepuber']);
			}
		}

		if($res){

			Util::echoResult(200,'已保存');
		}
		Util::echoResult(201,'保存失败');


	}elseif( $_GPC['op'] == 'dealtask'){

		$reply = pdo_get('zofui_tasktb_taked',array('uniacid'=>$_W['uniacid'],'id'=>$_GPC['replyid']));
		if( empty( $reply ) ) Util::echoResult(201,'没有找到回复');
		$task = pdo_get('zofui_tasktb_task',array('uniacid'=>$_W['uniacid'],'id'=>$reply['taskid']));
		if( empty( $task ) ) Util::echoResult(201,'没有找到任务');


		if($_GPC['type'] == 'accept'){
			if( $reply['status'] != 1 ) Util::echoResult(201,'此回复不能被采纳');
			$res = model_task::agreeTask($this->module['config'],$reply,$task);

		}elseif( $_GPC['type'] == 'refuse' ){
			

			if( $reply['status'] != 1 ) Util::echoResult(201,'此回复不能被拒绝');
			$puber = model_user::getSingleUser( $task['userid'] );
			$res = model_task::refuseTask($reply,$_GPC['reason'],$task,$puber['nickname']);

		}elseif( $_GPC['type'] == 'noscan' ){
			$res = pdo_update('zofui_tasktb_taked',array('isscan'=>1),array('id'=>$reply['id']) );
		}elseif( $_GPC['type'] == 'delete' ){
			$res = pdo_delete('zofui_tasktb_taked',array('id'=>$reply['id']) );

		}elseif( $_GPC['type'] == 'allowscan' ){
			$res = pdo_update('zofui_tasktb_taked',array('isscan'=>0),array('id'=>$reply['id']) );
		
		}elseif( $_GPC['type'] == 'remind' ){

			Message::noticeusetask($reply['userid'],$reply['openid'],$task['id'],$task['title'],$_GPC['reason']);

			$data = array(
				'uniacid' => $_W['uniacid'],
				'takedid' => $reply['id'],
				'createtime' => TIMESTAMP,
				'content' => $_GPC['reason'],
				'type' => 1,
			);
			pdo_insert('zofui_tasktb_remindlog',$data);

			Util::echoResult(200,'已发送提醒');
		}
		
		if($res){
			Util::echoResult(200,'操作成功');
		}
		Util::echoResult(201,'操作失败');


	}elseif( $_GPC['op'] == 'changemoney'){		
		$type = intval( $_GPC['type'] );
		$value = sprintf('%.2f',$_GPC['value']);
		$id = intval( $_GPC['id'] );

		$set = Util::getModuleConfig();
		$power = iunserializer( $set['power'] );
		if( $_W['role'] != 'founder' && $_W['role'] != 'manager' && !empty( $power ) && in_array('money', $power) ) die;


		$user = pdo_get('zofui_task_user',array('uniacid'=>$_W['uniacid'],'id'=>$id));

		if( empty( $user ) ) Util::echoResult(201,'会员不存在');

		if( $value == 0 ) Util::echoResult(201,'改变数值不能等于0');

		if( $type == 1 ){ // 余额

			$money = model_user::getUserCredit( $user['uid'] );
				
			if( $value < 0 && $money['credit2'] < abs( $value ) )  Util::echoResult(201,'对方'.$_W['cname'].'不足，减少点');
			$res = model_user::updateUserCredit($user['uid'],$value,2,1);
			if( $res ){
				model_money::insertMoneyLog($user['openid'],$value,1,13 ,$user['uid']);
			}

		}elseif( $type == 2 ){ // 保证金

			if( $value < 0 && $user['deposit'] < abs( $value ) )  Util::echoResult(201,'对方保证金不足，减少点');
			$res = Util::addOrMinusOrUpdateData( 'zofui_task_user',array('deposit'=>$value),$user['id'] );
			if( $res ){
				model_money::insertMoneyLog($user['openid'],$value,2,3 ,$user['uid'] );
			}			

		}elseif( $type == 3 ){ // 活跃度

			if( $value < 0 && $user['activity'] < abs( $value ) )  Util::echoResult(201,'对方活跃值不足，减少点');
			$res = Util::addOrMinusOrUpdateData( 'zofui_task_user',array('activity'=>$value),$user['id'] );
		}

		if($res){
			Util::deleteCache('u',$user['uid']);
			Util::echoResult(200,'操作成功');
		}
		Util::echoResult(201,'操作失败');

	}elseif( $_GPC['op'] == 'dealusetask'){		

		$type = intval( $_GPC['type'] );
		$rid = intval( $_GPC['rid'] );

		$taked = pdo_get('zofui_tasktb_usetasklog',array('uniacid'=>$_W['uniacid'],'id'=>$rid));
		if( empty( $taked ) ) Util::echoResult(201,'没有找到试用记录');

		$task = pdo_get('zofui_tasktb_task',array('id'=>$taked['taskid'],'uniacid'=>$_W['uniacid']));
		if( $task['iscount'] == 1 ) Util::echoResult(201,'任务已结算，不能再操作');
		if( empty( $task ) ) Util::echoResult(201,'没有找到试用任务');

		if( $type == 1 ){ // 通过
			$res = model_task::passUseTask( $task,$taked );
		
		}elseif( $type == 2 ){ // 不通过
			$res = model_task::passfailUseTask( $task,$taked );
		
		}elseif( $type == 5 ){ // 提醒对方
			if( empty( $_GPC['value'] ) ) Util::echoResult(201,'请填写提醒内容');
			
			$res = model_task::noticeUsetask( $taked,$task,$_GPC['value'] );
		}elseif( $type == 3 ){ // 设为失败

			if( $taked['status'] != 4 && $taked['status'] != 1 ) Util::echoResult(201,'此任务不能操作');
			if( empty( $_GPC['value'] ) ) Util::echoResult(201,'请填写失败原因');

			$res = model_task::failUseTask( $taked,$_GPC['value'],$task );

		}elseif( $type == 6 ){ // 转为完成

			if( $taked['status'] != 6 ) Util::echoResult(201,'此任务不能操作');
			
			$res = model_task::sucUseTask($_W['set'],$taked,$task,2);

		}elseif( $type == 4 ){ // 直接完成

			if( $taked['status'] != 4 ) Util::echoResult(201,'此任务不能操作');
			
			$res = model_task::sucUseTask($_W['set'],$taked,$task,1);
		}



		if($res){
			Util::echoResult(200,'操作成功');
		}
		Util::echoResult(201,'操作失败');

	}elseif( $_GPC['op'] == 'gettao' ){	

		$tao = model_taobao::getGood( $_GPC['url'] );
		
		if( $tao ){
			$_SESSION['taogood'] = $tao;
			Util::echoResult(200,'设置成功',$_SESSION['taogood']);
		}

		Util::echoResult(201,'查询商品失败，请输入正确的淘宝或天猫商品链接');

	}elseif( $_GPC['op'] == 'pubusetask'  ){	

		$set = Util::getModuleConfig();
		$power = iunserializer( $set['power'] );
		if( $_W['role'] != 'founder' && $_W['role'] != 'manager' && !empty( $power ) && in_array('pub', $power) ) die;


		$data['link'] = $_GPC['taourl']; // 淘宝商品链接
		$data['money'] = sprintf('%.2f',$_GPC['money']);// 返还赏金

		$data['content'] = $_GPC['content']; // 备注内容
		$data['images'] = iserializer( $_GPC['upimages'] ); // 备注图片
		
		$data['num'] = intval( $_GPC['num'] ); // 试用数量
		$data['paymoney'] = sprintf('%.2f',$_GPC['paymoney']); // 需支付金额，淘宝商品价格

		$data['sex'] = intval( $_GPC['sex'] ); // 性别
		$data['istop'] = intval( $_GPC['istop'] ); // 置顶

		$data['prizetype'] = intval( $_GPC['prizetype'] ); // 奖励类型 0赏金 ， 1实物
		$data['prizetitle'] = $_GPC['prizetitle'];
		$data['prizeimg'] = $_GPC['prizeimg'];

		$data['title'] = $_GPC['title']; // 任务标题
		$data['pic'] = $_GPC['pic']; // 淘宝商品图片
		$data['findtype'] = intval( $_GPC['findtype'] );
		
		$data['isform'] = intval( $_GPC['isform'] );
		$data['gtitle'] = $_GPC['gtitle'];
		$data['address'] = $_GPC['address'];
		
		// 验证
		if( empty( $data['link'] ) ) Util::echoResult(201,'请填写商品链接');
		//if( $data['link'] != $_SESSION['taogood']['taourl'] ) Util::echoResult(201,'提交的商品链接和淘宝(天猫)商品链接不一致');
		
		if( $data['money'] < 0 ) Util::echoResult(201,'返还金额不能小于0' );
		if( $data['num'] <= 0 ) Util::echoResult(201,'任务总量必须大于0');
		//if( $data['paymoney'] < $_SESSION['taogood']['nowprice'] ) Util::echoResult(201,'拍下价格不能小于商品价格');
		if( empty( $data['title'] ) ) Util::echoResult(201,'请填写任务标题');

		
		if( $data['prizetype'] == 1 && ( empty($data['prizetitle']) || empty($data['prizeimg']) ) ){
			Util::echoResult(201,'请填写完整奖励物品信息');
		}
		
		$end = strtotime( $_GPC['end'] );
		if( $end <= TIMESTAMP )  Util::echoResult(201,'结束时间必须大于现在时间');

		$data['uniacid'] = $_W['uniacid'];
		$data['type'] = 1; // 试用任务
		
		$data['start'] = TIMESTAMP;

		$data['end'] = $end;
		$data['createtime'] = TIMESTAMP;
		
		$data['status'] = 0;
		$data['isstart'] = 0;
		
		// 口令
		/*if( $_W['set']['gotaobaotype'] == 1 ){
			$res = getTaoWord::getLink( $data['link'],$_GPC['gtitle'],$data['pic'] );
			if( $res ) $data['taokey'] = $res['model'];
		}*/

		if( $data['findtype'] == 1 && empty( $_GPC['findkey'] ) ) Util::echoResult(201,'请填写搜商品关键词');
		if( !empty( $_GPC['findkey'] ) ) {
			$data['findkey'] = array();
			foreach ($_GPC['findkey'] as $v) {
				$data['findkey'][] = array('name'=>$v);
			}
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
		
		
		if( $_GPC['taskid'] > 0 ){
			
			$res = pdo_update('zofui_tasktb_task',$data,array('uniacid'=>$_W['uniacid'],'type'=>1,'id'=>$_GPC['taskid']));
		}else{
			$res = pdo_insert('zofui_tasktb_task',$data);
			$id = pdo_insertid();
			// 新任务通知
			//model_task::setMessage( $this->module['config'],$id );
		}
		
		
		if( $res ){

			Util::echoResult(200,'操作成功',array('taskid'=>$id));
		} 
		
		Util::echoResult(201,'操作失败');

	// 发消息
	}elseif( $_GPC['op'] == 'sendmess' ){  

		$tid = intval( $_GPC['tid'] );
		$task = pdo_get('zofui_tasktb_task',array('uniacid'=>$_W['uniacid'],'id'=>$tid));

		if( empty( $task )  ) Util::echoResult(201,'任务不存在');

		if( $task['iscount'] == 1 ) Util::echoResult(201,'任务已结算，不能发消息');
		if( $task['start'] > TIMESTAMP ) Util::echoResult(201,'任务还没开始，不能发消息');
		if( $task['end']< TIMESTAMP ) Util::echoResult(201,'任务已结束，不能发消息');
		if( $task['status'] == 1 ) Util::echoResult(201,'任务已下架，不能发消息');

		if( $_GPC['type'] == 'all' ) {

			$sendinfo = Util::getCache('sendmess','all');
			if( !empty( $sendinfo ) ) Util::echoResult(201,'还有通知正在发送中，等处理完才能再发');

			model_task::setMessage( $this->module['config'],$task,$_GPC['topstr'],$_GPC['botstr'],$_GPC['fee'],$_GPC['name'] );
			
			Util::echoResult(200,'已发送');
		}elseif( $_GPC['type'] == 'admin' ){

			$admin = iunserializer( $this->module['config']['admin'] );
			
			if( empty( $admin ) ) Util::echoResult(201,'没有设置管理员');
			foreach ( $admin as $v ) {
				$user = model_user::getSingleUser($v['userid']);
				Message::newTaskmess( $user['uid'],$user['openid'],$_GPC['name'],$_GPC['fee'],$tid,$_GPC['topstr'],$_GPC['botstr'] );
			}
			Util::echoResult(200,'已发送');
		}

		Util::echoResult(201,'发送失败');

	}elseif( $_GPC['op'] == 'uppro' ){

		$sendinfo = Util::getCache('sendmess','all');
		if( empty( $sendinfo ) ) Util::echoResult(201,'没有发送的消息');

		//$allnum = cache_search('tstb:'.$_W['uniacid'].':singlepro');
		$pronum = 0;
		
		for ($i=1; $i <= $sendinfo['page']; $i++) { 
			$singlepro = Util::getCache('singlepro',$i) * 1;
			$pronum += $singlepro;
		}
		
		$per = 0;
		if( $sendinfo['total'] > 0 ) $per = sprintf('%.2f', $pronum/$sendinfo['total']*100 );

		Util::echoResult(200,'好',array('sendinfo'=>$sendinfo,'pronum'=>$pronum,'per'=>$per));

	}elseif( $_GPC['op'] == 'stopsendmess' ){

		Util::deleteCache('sendmess','all');
		cache_clean('tstb:'.$_W['uniacid'].':sendmesspro');		
		cache_clean('tstb:'.$_W['uniacid'].':singlepro');
		Util::echoResult(200,'已提交停止指令');

	// 添加和编辑表单
	}elseif( $_GPC['op'] == 'addform' ){

		$fid = intval( $_GPC['fid'] );
		if( $fid > 0 ){
			$form = pdo_get('zofui_tasktb_authform',array('uniacid'=>$_W['uniacid'],'id'=>$fid));
			if( empty( $form ) ) Util::echoResult(201,'没有找到表单');
		}
		if( empty( $_GPC['name'] ) ) Util::echoResult(201,'请填写表单名称');
		if( empty( $_GPC['formtype'] ) ) Util::echoResult(201,'请选择表单类型');

		$data = array(
			'uniacid' => $_W['uniacid'],
			'name' => $_GPC['name'],
			'formtype' => $_GPC['formtype'],
			'number' => intval( $_GPC['number'] ),
			'useric' => iserializer($_GPC['usericarr']),
		);
		if( $fid > 0 ){ 
			
			$res = pdo_update('zofui_tasktb_authform',$data,array('id'=>$fid));
		}else{

			$res = pdo_insert('zofui_tasktb_authform',$data);
		}
		if( $res ) Util::echoResult(200,'已保存');
		Util::echoResult(201,'保存失败');

	}elseif( $_GPC['op'] == 'findform' ){

		$form = pdo_get('zofui_tasktb_authform',array('uniacid'=>$_W['uniacid'],'id'=>$_GPC['fid']));
		if( empty( $form ) ) Util::echoResult(201,'没有找到表单');

		$form['useric'] = iunserializer($form['useric']);

		Util::echoResult(200,'好',$form);

	// 添加和编辑表单
	}elseif( $_GPC['op'] == 'adduseric' ){

		$fid = intval( $_GPC['fid'] );
		if( $fid > 0 ){
			$form = pdo_get('zofui_tasktb_useric',array('uniacid'=>$_W['uniacid'],'id'=>$fid));
			if( empty( $form ) ) Util::echoResult(201,'没有找到数据');
		}
		if( empty( $_GPC['name'] ) ) Util::echoResult(201,'请填写名称');

		$data = array(
			'uniacid' => $_W['uniacid'],
			'name' => $_GPC['name'],
			'number' => intval( $_GPC['number'] ),
		);
		if( $fid > 0 ){ 
			
			$res = pdo_update('zofui_tasktb_useric',$data,array('id'=>$fid));
		}else{

			$res = pdo_insert('zofui_tasktb_useric',$data);
		}
		if( $res ) Util::echoResult(200,'已保存');
		Util::echoResult(201,'保存失败');

	}elseif( $_GPC['op'] == 'finduseric' ){

		$form = pdo_get('zofui_tasktb_useric',array('uniacid'=>$_W['uniacid'],'id'=>$_GPC['fid']));
		if( empty( $form ) ) Util::echoResult(201,'没有找到数据');
		
		Util::echoResult(200,'好',$form);
		
		
	}elseif( $_GPC['op'] == 'delic' ){

		pdo_delete('zofui_tasktb_userics',array('uniacid'=>$_W['uniacid'],'id'=>$_GPC['cid']));

		Util::echoResult(200,'已删除');

	// 添加和编辑表单
	}elseif( $_GPC['op'] == 'addtabbar' ){

		$fid = intval( $_GPC['fid'] );
		if( $fid > 0 ){
			$form = pdo_get('zofui_tasktb_tabbar',array('uniacid'=>$_W['uniacid'],'id'=>$fid));
			if( empty( $form ) ) Util::echoResult(201,'没有找到表单');
		}
		if( empty( $_GPC['name'] ) ) Util::echoResult(201,'请填写名称');
		if( empty( $_GPC['url'] ) ) Util::echoResult(201,'请填写链接');

		$data = array(
			'uniacid' => $_W['uniacid'],
			'name' => $_GPC['name'],
			'img' => $_GPC['img'],
			'actimg' => $_GPC['actimg'],
			'color' => $_GPC['color'],
			'actcolor' => $_GPC['actcolor'],
			'url' => $_GPC['url'],
			'number' => intval( $_GPC['number'] ),
		);
		if( $fid > 0 ){ 
			
			$res = pdo_update('zofui_tasktb_tabbar',$data,array('id'=>$fid));
		}else{

			$res = pdo_insert('zofui_tasktb_tabbar',$data);
		}
		Util::deletecache('tabbar','all');
		if( $res ) Util::echoResult(200,'已保存');
		Util::echoResult(201,'保存失败');

	}elseif( $_GPC['op'] == 'findtabbar' ){

		$form = pdo_get('zofui_tasktb_tabbar',array('uniacid'=>$_W['uniacid'],'id'=>$_GPC['fid']));
		$form['showimg'] = tomedia( $form['img'] );

		$form['showactimg'] = tomedia( $form['actimg'] );
		if( empty( $form ) ) Util::echoResult(201,'没有找到数据');

		Util::echoResult(200,'好',$form);

	// 
	}elseif( $_GPC['op'] == 'addbannerform' ){

		$fid = intval( $_GPC['fid'] );
		if( $fid > 0 ){
			$form = pdo_get('zofui_tasktb_banner',array('uniacid'=>$_W['uniacid'],'id'=>$fid));
			if( empty( $form ) ) Util::echoResult(201,'没有找到数据');
		}
		if( empty( $_GPC['img'] ) ) Util::echoResult(201,'请上传图片');

		$data['number'] = $_GPC['number'];
		$data['uniacid'] = $_W['uniacid'];
		$data['url'] = $_GPC['url'];
		$data['name'] = $_GPC['name'];
		$data['desc'] = $_GPC['desc'];
		$data['img'] = $_GPC['img'];

		if( $fid > 0 ){ 
			$res = pdo_update('zofui_tasktb_banner',$data,array('id'=>$fid));
		}else{
			$res = pdo_insert('zofui_tasktb_banner',$data);
		}
		if( $res ){
			Util::deleteCache('banner','all');
			Util::echoResult(200,'已保存');
		} 
		Util::echoResult(201,'保存失败');

	}elseif( $_GPC['op'] == 'findbanner' ){

		$form = pdo_get('zofui_tasktb_banner',array('uniacid'=>$_W['uniacid'],'id'=>$_GPC['fid']));
		if( empty( $form ) ) Util::echoResult(201,'没有找到数据');

		$form['showimg'] = tomedia( $form['img'] );

		Util::echoResult(200,'好',$form);
		

	}elseif( $_GPC['op'] == 'verifynopass' ){

		$uid = intval( $_GPC['uid'] );

		$user = pdo_get('zofui_task_user',array('uniacid'=>$_W['uniacid'],'id'=>$uid));
		if( $user['verifystatus'] != 1 ) Util::echoResult(201,'对方还没有提交认证资料');

		pdo_update('zofui_task_user',array('verifystatus'=>0),array('id'=>$uid,'uniacid'=>$_W['uniacid']));
		Util::deleteCache('u',$user['uid']);

		// 发通知
		Message::authnopassmess( $user['uid'],$user['openid'],$_GPC['reason'] );

		Util::echoResult(200,'已更改');

	// 标记会员备注
	}elseif( $_GPC['op'] == 'givemark' ){

		$uid = intval( $_GPC['uid'] );

		$user = pdo_get('zofui_task_user',array('uniacid'=>$_W['uniacid'],'id'=>$uid));
		if( empty( $user ) ) Util::echoResult(201,'没找到会员');

		if( empty( $_GPC['mark'] ) ) Util::echoResult(201,'请填写备注内容');

		pdo_update('zofui_task_user',array('mark'=>$_GPC['mark']),array('id'=>$uid,'uniacid'=>$_W['uniacid']));
		Util::deleteCache('u',$user['uid']);

		Util::echoResult(200,'已保存');

	// 添加人物分类
	}elseif( $_GPC['op'] == 'addguryform' ){

		$fid = intval( $_GPC['fid'] );
		if( $fid > 0 ){
			$form = pdo_get('zofui_tasktb_guysort',array('uniacid'=>$_W['uniacid'],'id'=>$fid));
			if( empty( $form ) ) Util::echoResult(201,'没有找到数据');
		}
		if( empty( $_GPC['name'] ) ) Util::echoResult(201,'请填写名称');

		$data = array(
			'uniacid' => $_W['uniacid'],
			'name' => $_GPC['name'],
			'img' => $_GPC['img'],
			'number' => intval( $_GPC['number'] ),
		);
		if( $fid > 0 ){ 
			$res = pdo_update('zofui_tasktb_guysort',$data,array('id'=>$fid));
		}else{
			$res = pdo_insert('zofui_tasktb_guysort',$data);
		}
		if( $res ) {
			Util::deleteCache('guysort','all');
			Util::echoResult(200,'已保存');
		}
		Util::echoResult(201,'保存失败');

	}elseif( $_GPC['op'] == 'findguysort' ){

		$form = pdo_get('zofui_tasktb_guysort',array('uniacid'=>$_W['uniacid'],'id'=>$_GPC['fid']));
		if( empty( $form ) ) Util::echoResult(201,'没有找到数据');

		$form['showimg'] = tomedia( $form['img'] );

		Util::echoResult(200,'好',$form);


	// 任务分类
	}elseif( $_GPC['op'] == 'addtasksortform' ){

		$fid = intval( $_GPC['fid'] );
		if( $fid > 0 ){
			$form = pdo_get('zofui_tasktb_tasksort',array('uniacid'=>$_W['uniacid'],'id'=>$fid));
			if( empty( $form ) ) Util::echoResult(201,'没有找到数据');
		}
		if( empty( $_GPC['name'] ) ) Util::echoResult(201,'请填写名称');

		$data['number'] = $_GPC['number'];
		$data['uniacid'] = $_W['uniacid'];
		$data['name'] = $_GPC['name'];
		$data['title'] = $_GPC['title'];
		$data['content'] = $_GPC['content'];
		$data['status'] = $_GPC['status'];
		$data['img'] = $_GPC['img'];
		$data['dmoney'] = $_GPC['dmoney'];

		$link = array();
		if( !empty( $_GPC['urlname'] ) ){
			
			foreach ($_GPC['urlname'] as $k => $v) {
				$linkitem['text'] = $v;
				$linkitem['url'] = $_GPC['urlurl'][$k];
				$link[] =  $linkitem;
			}
		}

		$other = array(
			'hcontent' => $_GPC['hcontent'],
			'taskimg' => $_GPC['taskimg'],
			'wait' => $_GPC['wait'],
			'canget' => $_GPC['canget'],
			'num' => $_GPC['num'],
			'formid' => $_GPC['formid'],
			'hide' => $_GPC['hide'],
			'urlarr' => $link,
		);
		
		$data['other'] = iserializer($other);

		if( $fid > 0 ){
			$res = pdo_update('zofui_tasktb_tasksort',$data,array('id'=>$fid));
		}else{
			$res = pdo_insert('zofui_tasktb_tasksort',$data);
		}
		if( $res ){
			Util::deleteCache('tasksort','all');
			Util::echoResult(200,'已保存');
		} 
		Util::echoResult(201,'保存失败');

	}elseif( $_GPC['op'] == 'findtasksort' ){

		$form = pdo_get('zofui_tasktb_tasksort',array('uniacid'=>$_W['uniacid'],'id'=>$_GPC['fid']));
		if( empty( $form ) ) Util::echoResult(201,'没有找到数据');

		$form['showimg'] = tomedia( $form['img'] );
		$form['content'] = htmlspecialchars_decode( $form['content'] );

		$form['other'] = iunserializer($form['other']);

		$form['showtaskimg'] = array();
		if( !empty($form['other']['taskimg']) ){
			foreach ($form['other']['taskimg'] as $v) {
				$form['showtaskimg'][] = array('t'=>$v,'v'=>tomedia($v));
			}
		}
		$form['other']['hcontent'] = htmlspecialchars_decode( $form['other']['hcontent'] );

		if( !empty($form['other']['formid']) ){
			$formif = pdo_getall('zofui_tasktb_taskform',array('uniacid'=>$_W['uniacid']),array('id','name'),'number desc');
			foreach ($formif as $v) {
				if($v['id'] == $form['other']['formid']){
					$form['other']['formname'] = $v['name'];
				}
			}
		}
		


		Util::echoResult(200,'好',$form);


	// 轮播
	}elseif( $_GPC['op'] == 'addsliderform' ){

		$fid = intval( $_GPC['fid'] );
		if( $fid > 0 ){
			$form = pdo_get('zofui_tasktb_slider',array('uniacid'=>$_W['uniacid'],'id'=>$fid));
			if( empty( $form ) ) Util::echoResult(201,'没有找到数据');
		}
		if( empty( $_GPC['img'] ) ) Util::echoResult(201,'请上传图片');

		$data['number'] = $_GPC['number'];
		$data['uniacid'] = $_W['uniacid'];
		$data['url'] = $_GPC['url'];
		$data['isindex'] = intval( $_GPC['isindex'] );
		$data['isusetask'] = intval( $_GPC['isusetask'] );
		$data['istbtask'] = intval( $_GPC['istbtask'] );
		$data['isguy'] = intval( $_GPC['isguy'] );
		$data['img'] = $_GPC['img'];

		if($_GPC['dayy'] > 0) $data['dayy'] = TIMESTAMP + $_GPC['dayy']*24*3600;

		if( $fid > 0 ){
			
			$res = pdo_update('zofui_tasktb_slider',$data,array('id'=>$fid));
		}else{
			
			$res = pdo_insert('zofui_tasktb_slider',$data);
		}
		if( $res ){
			Util::deleteCache('slider','all');
			Util::echoResult(200,'已保存');
		} 
		Util::echoResult(201,'保存失败');

	}elseif( $_GPC['op'] == 'findslider' ){

		$form = pdo_get('zofui_tasktb_slider',array('uniacid'=>$_W['uniacid'],'id'=>$_GPC['fid']));
		if( empty( $form ) ) Util::echoResult(201,'没有找到数据');

		$form['showimg'] = tomedia( $form['img'] );
		
		if( $form['dayy'] > 0 ) $form['dayy'] = sprintf('%.6f',($form['dayy'] - TIMESTAMP)/24/3600);


		Util::echoResult(200,'好',$form);


	// 广告
	}elseif( $_GPC['op'] == 'addadform' ){

		$fid = intval( $_GPC['fid'] );
		if( $fid > 0 ){
			$form = pdo_get('zofui_tasktb_ad',array('uniacid'=>$_W['uniacid'],'id'=>$fid));
			if( empty( $form ) ) Util::echoResult(201,'没有找到数据');
		}
		if( empty( $_GPC['title'] ) ) Util::echoResult(201,'请填写标题');

		$data['number'] = $_GPC['number'];
		$data['uniacid'] = $_W['uniacid'];
		$data['title'] = $_GPC['title'];
		$data['content'] = $_GPC['content'];
		$data['status'] = $_GPC['status'];		
		$data['time'] = TIMESTAMP;

		if( $fid > 0 ){ 
			$res = pdo_update('zofui_tasktb_ad',$data,array('id'=>$fid));
		}else{
			$res = pdo_insert('zofui_tasktb_ad',$data);
		}
		if( $res ){
			Util::deleteCache('ad','all');
			Util::echoResult(200,'已保存');
		} 
		Util::echoResult(201,'保存失败');

	}elseif( $_GPC['op'] == 'findad' ){

		$form = pdo_get('zofui_tasktb_ad',array('uniacid'=>$_W['uniacid'],'id'=>$_GPC['fid']));
		if( empty( $form ) ) Util::echoResult(201,'没有找到数据');

		$form['showimg'] = tomedia( $form['img'] );
		$form['content'] = htmlspecialchars_decode( $form['content'] );

		Util::echoResult(200,'好',$form);


	// 保存海报
	}elseif( $_GPC['op'] == 'savapostertemp' ){

		if( empty( $_GPC['data'] ) ) Util::echoResult(201,'先添加一些元素再提交');

		$params = htmlspecialchars_decode($_GPC['data']);
		$params = json_decode($params,true);

		$data = array(
			'uniacid' => $_W['uniacid'],
			'params' => iserializer( $params ),
			'bgimg' => $_GPC['bgimg'],
		);

		$poster = pdo_get('zofui_tasktb_selfposter',array('uniacid'=>$_W['uniacid']));
		if( empty( $poster ) ){
			$res = pdo_insert('zofui_tasktb_selfposter',$data);

		}else{
			$res = pdo_update('zofui_tasktb_selfposter',$data,array('id'=>$poster['id'],'uniacid'=>$_W['uniacid']));
		}
		if( $res ){
			model_poster::deletePoster( 1 );
			Util::echoResult(200,'已保存');
		} 
		Util::echoResult(201,'保存失败，可能是未改数据的原因');

	// 保存关键字
	}elseif( $_GPC['op'] == 'saveposterkey' ){

		$rulename = '自助任务海报关键词'.$_W['uniacid'];

		if( empty( $_GPC['key'] ) ) Util::echoResult(201,'请填写关键词');

		$poster = pdo_get('zofui_tasktb_selfposter',array('uniacid'=>$_W['uniacid']));
		if( empty( $poster ) ) 	Util::echoResult(201,'请先设计海报再设置关键词');

		// 和之前设置的不同再检查
		if( $poster['key'] != $_GPC['key'] ) {
			$sql = 'SELECT `rid` FROM ' . tablename('rule_keyword') . " WHERE `uniacid` = :uniacid  AND `content` = :content";
			$result = pdo_fetchall($sql, array(':uniacid' => $_W['uniacid'], ':content' => $_GPC['key']));
			if( !empty( $result ) ) Util::echoResult(201,'此关键词已存在，请换一个');
		}


	    $pid = WebCommon::doRule($rulename,$_GPC['key'],1);
	    $data = array(
	        'key' => $_GPC['key'],
	        'pid' => $pid,
	        'content' => $_GPC['content'],
	        'ccontent' => $_GPC['ccontent'],
	    );
		$res = pdo_update('zofui_tasktb_selfposter',$data,array('id'=>$poster['id'],'uniacid'=>$_W['uniacid']));

		if( $res ){
			Util::echoResult(200,'已保存');
		} 
		Util::echoResult(201,'已保存');	

	// 添加答疑
	}elseif( $_GPC['op'] == 'addquest' ){

		$fid = intval( $_GPC['fid'] );
		if( $fid > 0 ){
			$form = pdo_get('zofui_tasktb_selfquest',array('uniacid'=>$_W['uniacid'],'id'=>$fid));
			if( empty( $form ) ) Util::echoResult(201,'没有找到答疑');
		}
		if( empty( $_GPC['title'] ) ) Util::echoResult(201,'请填写答疑标题');

		$data = array(
			'uniacid' => $_W['uniacid'],
			'title' => $_GPC['title'],
			'type' => $_GPC['type'],
			'number' => intval( $_GPC['number'] ),
			'content' => $_GPC['content'],
			'status' => intval( $_GPC['status'] )
		);
		if( $fid > 0 ){ 
			
			$res = pdo_update('zofui_tasktb_selfquest',$data,array('id'=>$fid));
		}else{

			$res = pdo_insert('zofui_tasktb_selfquest',$data);
		}
		if( $res ) Util::echoResult(200,'已保存');
		Util::echoResult(201,'保存失败');

	}elseif( $_GPC['op'] == 'findquest' ){

		$form = pdo_get('zofui_tasktb_selfquest',array('uniacid'=>$_W['uniacid'],'id'=>$_GPC['fid']));
		if( empty( $form ) ) Util::echoResult(201,'没有找到数据');

		$form['content'] = htmlspecialchars_decode( $form['content'] );

		Util::echoResult(200,'好',$form);


	// 担保任务模板
	}elseif( $_GPC['op'] == 'addtbform' ){

		$fid = intval( $_GPC['fid'] );
		if( $fid > 0 ){
			$form = pdo_get('zofui_tasktb_tbform',array('uniacid'=>$_W['uniacid'],'id'=>$fid));
			if( empty( $form ) ) Util::echoResult(201,'没有找到数据');
		}
		if( empty( $_GPC['name'] ) ) Util::echoResult(201,'请填写名称');

		$data = array(
			'uniacid' => $_W['uniacid'],
			'name' => $_GPC['name'],
			'number' => intval( $_GPC['number'] ),
			'title' => $_GPC['title'],
			'num' => $_GPC['num'],
			'money' => $_GPC['money'],
			'tbmoney' => $_GPC['tbmoney'],
			'step' => iserializer(array('step1'=>$_GPC['tbstep1'],'step2'=>$_GPC['tbstep2'],'step3'=>$_GPC['tbstep3'],'step4'=>$_GPC['tbstep4'],'step5'=>$_GPC['tbstep5'])),

			'content' => $_GPC['content'],
			'status' => intval( $_GPC['status'] )
		);
		if( $fid > 0 ){ 
			
			$res = pdo_update('zofui_tasktb_tbform',$data,array('id'=>$fid));
		}else{

			$res = pdo_insert('zofui_tasktb_tbform',$data);
		}
		if( $res ) Util::echoResult(200,'已保存');
		Util::echoResult(201,'保存失败');

	}elseif( $_GPC['op'] == 'findtbform' ){

		$form = pdo_get('zofui_tasktb_tbform',array('uniacid'=>$_W['uniacid'],'id'=>$_GPC['fid']));
		if( empty( $form ) ) Util::echoResult(201,'没有找到数据');

		$form['content'] = htmlspecialchars_decode( $form['content'] );
		$form['step'] = iunserializer( $form['step'] );

		Util::echoResult(200,'好',$form);

	// 
	}elseif( $_GPC['op'] == 'addpuberform' ){

		$fid = intval( $_GPC['fid'] );
		if( $fid > 0 ){
			$form = pdo_get('zofui_tasktb_puber',array('uniacid'=>$_W['uniacid'],'id'=>$fid));
			if( empty( $form ) ) Util::echoResult(201,'没有找到表单');
		}
		if( empty( $_GPC['name'] ) ) Util::echoResult(201,'请填写名称');

		$data = array(
			'uniacid' => $_W['uniacid'],
			'nickname' => $_GPC['name'],
			'headimg' => $_GPC['headimg'],
			'falsepub' => intval( $_GPC['falsepub'] ),
			'falsetake' => intval( $_GPC['falsetake'] ),
			'falsedep' => intval( $_GPC['falsedep'] ),
		);
		if( $fid > 0 ){ 
			
			$res = pdo_update('zofui_tasktb_puber',$data,array('id'=>$fid));
		}else{

			$res = pdo_insert('zofui_tasktb_puber',$data);
		}

		Util::deleteCache('fpuber',$fid);

		if( $res ) Util::echoResult(200,'已保存');
		Util::echoResult(201,'保存失败');

	}elseif( $_GPC['op'] == 'findpuberr' ){

		$form = pdo_get('zofui_tasktb_puber',array('uniacid'=>$_W['uniacid'],'id'=>$_GPC['fid']));
		if( empty( $form ) ) Util::echoResult(201,'没有找到表单');

		$form['showimg'] = tomedia($form['headimg']);

		Util::echoResult(200,'好',$form);	


	// 发测试消息
	}elseif( $_GPC['op'] == 'testmess' ){

		$admin = iunserializer( $this->module['config']['admin'] );

		if( empty( $admin ) ) {
			Util::echoResult(201,'请先设置好管理员');
		}

		foreach ( $admin as $v ) {
			$user = model_user::getSingleUser( $v['userid'] );
			
			if( empty( $user['openid'] ) ) {
				Util::echoResult(201,'发送失败，原因：会员'.$user['nickname'].'未关联到粉丝数据。无法发送消息');
			}

			$res = Message::testmess( $user['uid'],$user['openid'] );

			if( !$res['status'] ) {
				Util::echoResult(201,'发送失败，原因：'.$res['msg']);
			}
		}

		Util::echoResult(200,'已发送');

	}elseif( $_GPC['op'] == 'upuserdata' ){

		set_time_limit(0);
		$all = pdo_getall('zofui_task_user',array('uniacid'=>$_W['uniacid']),array('id','openid','uid'));

		$isnew = pdo_tableexists('mc_fans_tag');

		if( !empty( $all ) ) {

			foreach ($all as $v) {
				$uid = 0;

				if( $isnew ){
					$fans = pdo_get('mc_fans_tag',array('openid'=>$v['openid']),array('headimgurl','nickname'));
					if( empty($fans['headimgurl']) ) {
						continue;
					}

					$tag['headimgurl'] = $fans['headimgurl'];

					$uid = pdo_get('mc_mapping_fans',array('uniacid'=>$_W['uniacid'],'openid'=>$v['openid']),array('uid'));

					if( !empty($uid['uid']) ){
						$uid = $uid['uid'];
					}

				}else{
					$fans = pdo_get('mc_mapping_fans',array('uniacid'=>$_W['uniacid'],'openid'=>$v['openid']),array('uid','tag','nickname'));
					if( empty( $fans ) || empty( $fans['nickname'] ) || empty( $fans['uid'] ) || empty( $fans['openid'] ) || empty( $v['openid'] ) ) continue;
					
					$tag = iunserializer( base64_decode( $fans['tag'] ) );

					if( empty( $tag['headimgurl'] ) ) continue;

					if( !empty($fans['uid']) ){
						$uid = $fans['uid'];
					}

				}

				$data = array('nickname' => $fans['nickname'],'headimgurl' => $tag['headimgurl']);

				if( !empty($uid) ){
					$data['uid'] = $uid;
				}

				pdo_update('zofui_task_user', $data, array('id' => $v['id']));
				Util::deleteCache('u',$v['uid']);

			}

		}

		Util::echoResult(200,'已更新');


	// 编辑担保任务
	}elseif( $_GPC['op'] == 'addtbtask' ){


		$_GPC = Util::trimWithArray($_GPC);
		//$continueday = intval( $_GPC['days'] );
		
		$data = array(
			'uniacid' => $_W['uniacid'],
			'title' => $_GPC['title'],
			'num' => intval($_GPC['num']),
			'money' => sprintf('%.2f',$_GPC['money']),
			'tbmoney' => sprintf('%.2f',$_GPC['tbmoney']),
			'limitnum' => intval($_GPC['limitnum']),
			'sex' => intval($_GPC['sex']),
			//'istop' => intval($_GPC['istop']),
			'iska' => intval($_GPC['iska']),
			'kagoodid' => $_GPC['kagoodid'],
			'content' => $_GPC['content'],
			'hidecontent' => $_GPC['hidecontent'],
			'end' => strtotime( $_GPC['end'] ),
			'images' => iserializer( $_GPC['upimages'] ),
			'hideimages' => iserializer( $_GPC['uphideimages'] ),
			'skiptype' => intval( $_GPC['skiptype'] ),
			'kakey' => $_GPC['upkakey'],
		);
		
		
		if( $data['iska'] == 1 && $data['skiptype'] == 1 ){

			$kakey = array();
			foreach ((array)$data['kakey'] as $v) {
				$tokey = rawurlencode( $v );
				$res = getTaoWord::getLink( $data['kagoodid'] , $tokey );
									
				$kakeytemp = array();
				$kakeytemp['key'] = $v;
				$kakeytemp['tao'] = $res['model'];
				$kakey[] = $kakeytemp;
			}
			$data['kakey'] = iserializer( $kakey );
		}else{
			$data['kakey'] = iserializer( $data['kakey'] );
		}

		$num = 0;
		$step = array();
		foreach ($_GPC['upstep'] as $k => $v ) {
			if( !empty( $v ) ){
				$step[] = array('step'=>$k+1,'name'=>$v);
				$num++;
			}
		}
		if( $num <= 0 ) Util::echoResult(201,'至少填写一个步骤');
					
		$data['step'] = iserializer( $step );	

		if( $data['num'] <= 0 )  Util::echoResult(201,'任务数量不能小于等于0');
		if( $data['money'] <= 0 )  Util::echoResult(201,'任务赏金不能小于等于0');
		if( $data['end'] <= time() )  Util::echoResult(201,'结束时间必须大于现在时间');

		$taskid = intval( $_GPC['taskid'] );
		if( $taskid <= 0 )	Util::echoResult(201,'请重新进入页面编辑');


		$task = pdo_get('zofui_tasktb_tbtask',array('uniacid'=>$_W['uniacid'],'id'=>$taskid));
		if( empty( $task ) ) Util::echoResult(201,'任务不存在');
		if( $task['iscount'] == 1 ) Util::echoResult(201,'已结算任务不能再编辑');

		$res = pdo_update('zofui_tasktb_tbtask',$data,array('uniacid'=>$_W['uniacid'],'id'=>$task['id']));

		if($res){
			Util::deleteCache('tbtask',$taskid);
			Util::echoResult(200,'已保存');
		}
		Util::echoResult(201,'保存失败');

	// 结算担保任务
	}elseif( $_GPC['op'] == 'counttbtask' ){

		$taskid = intval( $_GPC['taskid'] );

		$task = model_tbtask::getTask( $taskid );

		if( empty( $taskid ) ) Util::echoResult(201,'未找到任务');

		model_tbtask::countTbtask( $task );

		Util::echoResult(200,'已提交结算');

	// 提醒
	}elseif( $_GPC['op'] == 'sendremind' ){

		$id = intval( $_GPC['id'] );
		if( empty( $_GPC['content'] ) ) Util::echoResult(201,'还没填写提醒内容');

		if( $_GPC['type'] == 1 ) { // 提醒雇主

			$task = model_tbtask::getTask( $id );
			$openid = $task['puber'];
			$uid = $task['userid'];
			$title = $task['title'];
			$url = Util::createModuleUrl('tbtask',array('id'=>$task['id']));
		}elseif( $_GPC['type'] == 2 ){

			$taked = pdo_get('zofui_tasktb_tbtaked',array('id'=>$id,'uniacid'=>$_W['uniacid']));
			$task = model_tbtask::getTask( $taked['taskid'] );

			$openid = $taked['openid'];
			$uid = $taked['userid'];
			$url = Util::createModuleUrl('tbtask',array('id'=>$task['id']));
		}elseif( $_GPC['type'] == 3 ){ // 普通任务提醒商家

			$task = pdo_get('zofui_tasktb_task',array('id'=>$id,'uniacid'=>$_W['uniacid']));
			$openid = $task['puber'];
			$uid = $task['userid'];
			
			if( empty( $openid ) ) {
				Util::echoResult(201,'任务发布者未绑定微信号');
			}

			$url = Util::createModuleUrl('task',array('id'=>$task['id']));
		}
		
		
		$title = $task['title'];
		
		Message::remindUser($uid,$openid,$title,$_GPC['content'],$url);

		Util::echoResult(200,'已提交提醒');
		

	}elseif( $_GPC['op'] == 'addtoptime' ){
		
		$id = intval( $_GPC['id'] );
		$num = intval( $_GPC['value'] );

		if( $num == 0 ) Util::echoResult(201,'不能填0');

		$task = model_tbtask::getTask( $id );
		if( empty( $task ) || $task['end'] <= TIMESTAMP ) Util::echoResult(201,'任务已结束，不能再置顶');


		if( $task['topendtime'] <= TIMESTAMP ) { // 提醒雇主
			$time = TIMESTAMP + $num*3600;
			
		}else{
			$time = $task['topendtime'] + $num*3600;
		}
		
		pdo_update('zofui_tasktb_tbtask',array('topendtime'=>$time),array('id'=>$task['id']));
		
		Util::deleteCache('tbtask',$id);
		Util::echoResult(200,'操作完成');

	}elseif( $_GPC['op'] == 'setpayed'){

		$draw = pdo_get('zofui_tasktb_draw',array('id'=>$_GPC['oid'],'uniacid'=>$_W['uniacid']));

		if( empty( $draw ) || $draw['paytype'] != 2 || !empty($draw['status']) ){
			Util::echoResult(201,'数据不存在');
		}

		$res = pdo_update('zofui_tasktb_draw',array('status'=>1,'backreason'=>'','dealtime'=>TIMESTAMP),array('uniacid'=>$_W['uniacid'],'id'=>$draw['id']));
		if( $res ) {
			// 发消息
			$type = $draw['type'] == 1 ? 'money' : 'deposit';
			Message::sucmoney($draw['userid'],$draw['openid'],$draw['money'],'支付到收款码',$type);
		}
		Util::echoResult(200,'已改变');

	// 修改会员支付宝
	}elseif( $_GPC['op'] == 'edituserali' ){

		$user = pdo_get('zofui_task_user',array('id'=>$_GPC['id'],'uniacid'=>$_W['uniacid']));
		if( empty( $user ) ) Util::echoResult(201,'会员不存在');

		pdo_update('zofui_task_user',array('alipay'=>$_GPC['account'],'alipayname'=>$_GPC['name']),array('id'=>$user['id']));

		Util::deleteCache('u',$user['uid']);

		Util::echoResult(200,'已修改');
		

	// 修改会员密码
	}elseif( $_GPC['op'] == 'edituserpass' ){

		load()->model('mc');
		$user = mc_fetch($_GPC['uid'], array('uniacid'=>$_W['uniacid']));

		if( empty( $user ) ) Util::echoResult(201,'账户不存在');
		if( empty( $_GPC['pass'] ) ) Util::echoResult(201,'密码不能为空');

		$password = md5($_GPC['pass'] . $user['salt'] . $_W['config']['setting']['authkey']);
		$res = mc_update($user['uid'], array('password' => $password));

		Util::echoResult(200,'已修改');

	// 修改会员等级
	}elseif( $_GPC['op'] == 'edituserlevel' ){

		$user = pdo_get('zofui_task_user', array('uniacid'=>$_W['uniacid'],'id'=>$_GPC['uid']));

		if( empty( $user ) ) Util::echoResult(201,'账户不存在');
		if( empty( $_GPC['level'] ) ) Util::echoResult(201,'先选择会员等级');
		if( empty( $_GPC['time'] ) ) Util::echoResult(201,'先填写会员期限');	

		$time = $_GPC['time']*3600*24*31 + TIMESTAMP;
		pdo_update('zofui_task_user',array('level'=>$_GPC['level']-1,'utime'=>$time),array('id'=>$user['id']));

		Util::deleteCache('u',$user['uid']);

		Util::echoResult(200,'已修改');
	
	}elseif( $_GPC['op'] == 'sendmoneyall' ){

		if( empty( $_GPC['data'] ) ) Util::echoResult(201,'请选择要操作的数据');
		foreach ($_GPC['data'] as $v) {
			$taked = pdo_get('zofui_tasktb_taked',array('uniacid'=>$_W['uniacid'],'id'=>$v));
			if( empty( $taked ) ) Util::echoResult(201,'选择的数据不存在');
			

			$user = pdo_get('zofui_task_user',array('uniacid'=>$_W['uniacid'],'uid'=>$taked['userid']));
			if( empty( $user ) ) Util::echoResult(201,'会员不存在');

			$value = sprintf('%.2f',$_GPC['money']) * 1;

			if( $value == 0 ) Util::echoResult(201,'改变数值不能等于0');

			$money = model_user::getUserCredit( $user['uid'] );
				
			if( $value < 0 && $money['credit2'] < abs( $value ) )  Util::echoResult(201,'会员'.$user['nickname'].$_W['cname'].'不足');
			$res = model_user::updateUserCredit($user['uid'],$value,2,1);
			if( $res ){
				model_money::insertMoneyLog($user['openid'],$value,1,13 ,$user['uid']);

				Util::addOrMinusOrUpdateData('zofui_tasktb_taked',array('adminadd'=>$value),$taked['id']);
			}
		}

		Util::echoResult(200,'操作完成');


	}elseif( $_GPC['op'] == 'changeauth' ){

		$auth = pdo_get('zofui_tasktb_vauth',array('uniacid'=>$_W['uniacid']));
		if( !empty($auth) ){
			$params = iunserializer($auth['params']);
		}
		if( empty($params) ){
			$params = array();
		}

		if( $_GPC['v'] == 'true' ){
			$params[$_GPC['opp']] = 1;
		}else{
			unset($params[$_GPC['opp']]);
		}

		$inparams = iserializer($params);

		if( empty($auth) ){
			pdo_insert('zofui_tasktb_vauth',array('uniacid'=>$_W['uniacid'],'params'=>$inparams));
		}else{
			pdo_update('zofui_tasktb_vauth',array('params'=>$inparams),array('id'=>$auth['id']));
		}

		Util::echoResult(200,'已更新');

	}elseif( $_GPC['op'] == 'addbarone' ){

		$img1 = '../addons/zofui_taskself/public/images/bar_a.png';
		$actimg1 = '../addons/zofui_taskself/public/images/bar_a1.jpg';
		$img2 = '../addons/zofui_taskself/public/images/bar_b.png';
		$actimg2 = '../addons/zofui_taskself/public/images/bar_b1.jpg';
		$img3 = '../addons/zofui_taskself/public/images/bar_c.png';
		$actimg3 = '../addons/zofui_taskself/public/images/bar_c1.jpg';
		$img4 = '../addons/zofui_taskself/public/images/bar_d.png';
		$actimg4 = '../addons/zofui_taskself/public/images/bar_d1.jpg';

		$url1 = Util::createModuleUrl('index');
		$url2 = Util::createModuleUrl('level');
		$url3 = Util::createModuleUrl('down');
		$url4 = Util::createModuleUrl('user');

		$str = 'a:4:{i:0;a:9:{s:2:"id";s:1:"5";s:7:"uniacid";s:1:"1";s:4:"name";s:6:"任务";s:6:"number";s:1:"4";s:3:"img";s:51:"'.$img1.'";s:3:"url";s:68:"'.$url1.'";s:5:"color";s:7:"#808080";s:8:"actcolor";s:7:"#333333";s:6:"actimg";s:51:"'.$actimg1.'";}i:1;a:9:{s:2:"id";s:1:"6";s:7:"uniacid";s:1:"1";s:4:"name";s:6:"会员";s:6:"number";s:1:"3";s:3:"img";s:51:"'.$img2.'";s:3:"url";s:68:"'.$url2.'";s:5:"color";s:7:"#808080";s:8:"actcolor";s:7:"#333333";s:6:"actimg";s:51:"'.$actimg2.'";}i:2;a:9:{s:2:"id";s:1:"7";s:7:"uniacid";s:1:"1";s:4:"name";s:9:"合伙人";s:6:"number";s:1:"2";s:3:"img";s:51:"'.$img3.'";s:3:"url";s:67:"'.$url3.'";s:5:"color";s:7:"#808080";s:8:"actcolor";s:7:"#333333";s:6:"actimg";s:51:"'.$actimg3.'";}i:3;a:9:{s:2:"id";s:1:"8";s:7:"uniacid";s:1:"1";s:4:"name";s:6:"我的";s:6:"number";s:1:"1";s:3:"img";s:51:"'.$img4.'";s:3:"url";s:67:"'.$url4.'";s:5:"color";s:7:"#808080";s:8:"actcolor";s:7:"#333333";s:6:"actimg";s:51:"'.$actimg4.'";}}';

		pdo_delete('zofui_tasktb_tabbar',array('uniacid'=>$_W['uniacid']));
		$arr = iunserializer($str);
		foreach ($arr as $v) {
			$indata = $v;
			$indata['uniacid'] = $_W['uniacid'];
			unset($indata['id']);
			pdo_insert('zofui_tasktb_tabbar',$indata);
		}

		Util::deleteCache('tabbar','all');
		Util::echoResult(200,'已更新');

	// 恢复任务
	}elseif( $_GPC['op'] == 'restart' ){

		$task = pdo_get('zofui_tasktb_task',array('uniacid'=>$_W['uniacid'],'id'=>$_GPC['tid']));
		if( empty($task) ) Util::echoResult(201,'未找到任务');

		if( $task['iscount'] != 1 ) Util::echoResult(201,'任务未结算，不可恢复');

		if( !empty($task['userid']) && $task['backmoney'] > 0 ){

			$user = model_user::getSingleUser( $task['userid'] );

			$credit = model_user::getUserCredit( $task['userid'] );
			if($credit['credit2'] < $task['backmoney']){
				Util::echoResult(201,'恢复需要扣除发布者'.$task['backmoney'].'余额，发布者的余额不够');
			}
			
			// 扣钱
			$res = model_user::updateUserCredit($task['userid'],-$task['backmoney'],2,1);
			if( $res ){
				// 资金记录
				model_money::insertMoneyLog($user['openid'],-$task['backmoney'],1,41,$user['uid']);
				Util::deleteCache('u',$user['uid']);
			}else{
				Util::echoResult(201,'扣钱失败');
			}
		}

		$_W['set']['autoconfirm'] = empty($_W['set']['autoconfirm']) ? 24 : $_W['set']['autoconfirm'];
		$end = TIMESTAMP + $_W['set']['autoconfirm']*3600;
		$res = pdo_update('zofui_tasktb_task',array('iscount'=>0,'end'=>$end,'backmoney'=>0),array('id'=>$task['id']));
		if( $res ){
			Util::echoResult(200,'已恢复');
		}else{
			Util::echoResult(201,'扣钱失败');
		}
		
	}elseif( $_GPC['op'] == 'deleteallimg' ){

		model_task::deleteTaskImg($_GPC['tid'],$_GPC['type']);
		Util::echoResult(200,'已删除');
			
	}elseif( $_GPC['op'] == 'topordown' ){

		if( $_GPC['type'] == 'top' ){
			$up = array('istop'=>1);
		}else{
			$up = array('istop'=>0);
		}
		pdo_update('zofui_tasktb_task',$up,array('uniacid'=>$_W['uniacid'],'id'=>$_GPC['tid']));

		Util::echoResult(200,'操作完成');


	}elseif( $_GPC['op'] == 'addreply' ){

		$task = pdo_get('zofui_tasktb_task',array('uniacid'=>$_W['uniacid'],'id'=>$_GPC['tid']));
		if( empty($task) ) Util::echoResult(201,'任务不存在');

		$num = intval($_GPC['num']);
		if( $num <= 0 ) Util::echoResult(201,'请填写数量');

		$taked = Util::countDataNumber('zofui_tasktb_taked',array('taskid'=>$task['id'],'endtime>'=>TIMESTAMP));
		$last = $task['num'] - $taked;

		if( $num > $last )  Util::echoResult(201,'任务数量不够，最多可增加'.$last.'个');

		$nickname = $_GPC['nick'];
		$sql = " SELECT * FROM ".tablename('mc_mapping_fans')." WHERE uniacid = ".$_W['uniacid']." ORDER BY rand() LIMIT ".$num;
		$user = pdo_fetchall($sql);
		
		if( count($user) < $num ){
			Util::echoResult(201,'会员数据不够');
		}

		foreach ($user as $v) {
			
			$tag = iunserializer( base64_decode( $v['tag'] ) );

			$data = array(
				'uniacid' => $_W['uniacid'],
				'openid' => '',
				'taskid' => $task['id'],
				'continueid' => $task['continueid'],
				'content' => $_GPC['content'],
				'createtime' => TIMESTAMP,
				'waittime' => TIMESTAMP,
				'replytime' => TIMESTAMP,
				'dealtime' => TIMESTAMP,
				'endtime' => $task['end'] + 100*365*24*60*60,
				'money' => $task['money'],
				'puber' => $task['puber'],
				'ip' => getIp(),
				'status' => 2,
				'type' => 1,
				'nick' => $v['nickname'],
				'headimg' => $tag['headimgurl'],
				'falseuid' => rand(10000,99999),
			);
			$res = pdo_insert('zofui_tasktb_taked',$data);

		}

		Util::echoResult(200,'已添加');


	}elseif( $_GPC['op'] == 'icbtnuser' ){

		$isset = pdo_get('zofui_tasktb_userics',array('uid'=>$_GPC['uid'],'icid'=>$_GPC['cid'],'uniacid'=>$_W['uniacid']));
		if( empty($isset) ){
			$in = array(
				'uniacid' => $_W['uniacid'],
				'icid' => $_GPC['cid'],
				'uid' => $_GPC['uid']
			);
			pdo_insert('zofui_tasktb_userics',$in);
		}else{
			pdo_delete('zofui_tasktb_userics',array('id'=>$isset['id']));
		}

		Util::echoResult(200,'已修改');

	}elseif( $_GPC['op'] == 'tosteptemp' ){

		$isset = pdo_get('zofui_tasktb_step',array('id'=>$_GPC['id'],'uniacid'=>$_W['uniacid']));
		$istemp = $_GPC['type'] == 1 ? 1 : 0;

		if( empty($isset) ) Util::echoResult(201,'数据不存在');

		if( $istemp == 1 ){

			$indata = array(
				'uniacid' => $_W['uniacid'],
				'istemp' => 1,
				'step' => $isset['step'],
			);
			pdo_insert('zofui_tasktb_step',$indata);
		}else{
			pdo_update('zofui_tasktb_step',array('istemp'=>0),array('id'=>$isset['id']));
		}

		Util::echoResult(200,'已修改');

	}elseif( $_GPC['op'] == 'recomplain' ){

		$complain = pdo_get('zofui_tasktb_complain',array('uniacid'=>$_W['uniacid'],'id'=>$_GPC['id']));
		if( empty($complain) ) Util::echoResult(201,'数据不存在');

		$res = pdo_update('zofui_tasktb_complain',array('status'=>1),array('uniacid'=>$_W['uniacid'],'id'=>$_GPC['id']));

		if( !empty($_GPC['tou']) ){
			Message::complainMess($complain['userid'],$complain['openid'],$complain['content'],$_GPC['tou']);
		}

		Util::echoResult(200,'已处理');

	}elseif( $_GPC['op'] == 'messuser' ){

		$user = pdo_get('zofui_task_user',array('uniacid'=>$_W['uniacid'],'id'=>$_GPC['id']));
		if( empty($user) ) Util::echoResult(201,'会员数据不存在');

		if( empty($_GPC['mess']) ) Util::echoResult(201,'还没填写发送的内容');

		Message::sysmess( $user['uid'],$user['openid'],$_GPC['mess']);
		Util::echoResult(200,'已发送');

	}elseif( $_GPC['op'] == 'queue'){

		for( $i = 0;$i<3;$i++ ){
			$cache = Util::getCache('queue','q');
			if( empty( $cache ) || $cache['time'] < ( time() - 40 ) ){
				if( $i == 2 ){
					$url = Util::createModuleUrl('message',array('op'=>1));
					$res = Util::httpGet($url,'', 1);
					die('2');
				}
				sleep(1);
			}else{
				die('1');
			}			
			
		}

		

	}