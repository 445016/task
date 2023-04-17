<?php 
	global $_W,$_GPC;
	$_GPC['do'] = 'deposit';
	$_GPC['op'] = $op = empty($_GPC['op'])? 'waitpay' : $_GPC['op'];
	$_GPC = Util::trimWithArray($_GPC);
	model_user::wInit();

	set_time_limit(0);

	$set = Util::getModuleConfig();
	$power = iunserializer( $set['power'] );
	if( $_W['role'] != 'founder' && $_W['role'] != 'manager' && !empty( $power ) && in_array('pay', $power) ) die;

	// 支付所有
	if( checkSubmit('payall') ){
		
		if( empty( $_GPC['checkall'] ) ) message('先选择要操作的');
		$suc = 0;
		foreach ($_GPC['checkall'] as $v) {
			$draw = pdo_get('zofui_tasktb_draw',array('status'=>0,'uniacid'=>$_W['uniacid'],'id'=>$v));
			if( empty( $draw ) ) continue;

	        $arr['openid'] = $draw['openid'];
	        if ( $_W['account']['key'] != $this->module['config']['appid'] ) {
	        	$auth = pdo_get('zofui_task_user',array('uniacid'=>$_W['uniacid'],'openid'=>$draw['openid']),array('authopenid'));
	        	if( !empty( $auth['authopenid'] ) ){
	        		$arr['openid'] = $auth['authopenid'];
	        	}
	        }
	        
	        $arr['fee'] = $draw['money'];
	        $pay = new WeixinPay;
	        
	        $no = $this->module['config']['mchid'].$draw['createtime'].$draw['id'];

	        if($this->module['config']['paytype'] == 0){ // 红包发放
	            $arr['hbname'] = '提现';
	            $arr['body'] = '提现';
	            $cres = $pay -> sendhongbaoto($arr,$this,$no);
	            $resstr = '资金以红包形式发放到您与公众的聊天框内';
	        }else{
	            $cres = $pay -> sendMoneyToUser($arr,$this,$no);
	            $resstr = '资金发放到您的微信钱包内';
	        }
	                
	        if($cres['result_code'] != 'SUCCESS'){
	            message('成功处理'.$suc.'项,遇到支付异常，提现id'.$draw['id'].'遇到错误：'.$cres['err_code_des'],referer(),'success');
	        }else{
	        	
				pdo_update('zofui_tasktb_draw',array('status'=>1,'dealtime'=>TIMESTAMP),array('uniacid'=>$_W['uniacid'],'id'=>$v));
				$suc ++;
				// 发消息
				Message::sucmoney($draw['userid'],$draw['openid'],$draw['money'],$resstr,'deposit');
	        }

		}
		message('操作完成,成功处理'.$suc.'项',referer(),'success');
		
	}

	// 支付宝支付所有
	if( checkSubmit('alipaytoall') ){
		
		if( empty( $_GPC['checkall'] ) ) message('先选择要操作的');
		$suc = 0;

		$keyfile = MODULE_ROOT.'/cert/'.$_W['uniacid'].'/alikey.txt';
		if( !is_file( $keyfile ) ) {
			message('还没有设置应用私钥，在参数设置-提现参数内设置');
		}

		$alikey = file_get_contents( $keyfile );
		if( empty( $alikey ) ) {
			message('还没有设置应用私钥，在参数设置-提现参数内设置');
		}

		$ali = new aliPay();

		foreach ($_GPC['checkall'] as $v) {
			$draw = pdo_get('zofui_tasktb_draw',array('status'=>0,'uniacid'=>$_W['uniacid'],'id'=>$v));
			if( empty( $draw ) ) continue;

	        $fee = $draw['money'];

			$per = 1;
			if( $this->module['config']['moneytype'] == 1 && !empty( $this->module['config']['creditper'] ) ){
				$per = $this->module['config']['creditper'];
				$fee = $fee/$per;
			}

			$res = $ali -> sendMoneyToUser($this->module['config']['aliappid'],trim($draw['alipay']),trim($draw['alipayname']),$fee,$alikey);
	                
	        if( !$res['status'] ){
	            message('成功处理'.$suc.'项,遇到支付异常，提现id'.$draw['id'].'遇到错误：'.$res['msg'],referer(),'success');
	        }else{
	        	
				pdo_update('zofui_tasktb_draw',array('status'=>1,'dealtime'=>TIMESTAMP,'payno'=>$res['orderid']),array('uniacid'=>$_W['uniacid'],'id'=>$v));
				$suc ++;
				// 发消息
				Message::sucmoney($draw['userid'],$draw['openid'],$draw['money'],$resstr,'money');
	        }

		}
		message('操作完成,成功处理'.$suc.'项',referer(),'success');
		
	}

 	// 拒绝所有
	if( checkSubmit('refuseall') ){
		if( empty( $_GPC['checkall'] ) ) message('先选择要操作的');
		$suc = 0;
		foreach ($_GPC['checkall'] as $v) {
			$draw = pdo_get('zofui_tasktb_draw',array('status'=>0,'uniacid'=>$_W['uniacid'],'id'=>$v));
			if( empty( $draw ) ) continue;

			$res = pdo_update('zofui_tasktb_draw',array('status'=>3,'refusereason'=>$_GPC['reason'],'dealtime'=>TIMESTAMP),array('uniacid'=>$_W['uniacid'],'id'=>$v));
			if( $res ) {
				$suc ++;
				// 发消息
				Message::refusemoney($draw['userid'],$draw['openid'],$draw['money'],$_GPC['reason'],'deposit');
			}

		}
		message('操作完成,成功处理'.$suc.'项',referer(),'success');
		
	}

 	// 退回所有
	if( checkSubmit('backall') ){
		
		if( empty( $_GPC['checkall'] ) ) message('先选择要操作的');
		$suc = 0;
		foreach ($_GPC['checkall'] as $v) {
			$draw = pdo_get('zofui_tasktb_draw',array('status'=>0,'uniacid'=>$_W['uniacid'],'id'=>$v));
			if( empty( $draw ) ) continue;

			$money = $draw['money'];
			if( $this->module['config']['backdp'] == 1 ){
				$money += $draw['server'];
			}

			$user = model_user::getSingleUser( $draw['userid'] );
			$res = Util::addOrMinusOrUpdateData('zofui_task_user',array('deposit'=>$money),$user['id']);
			if( $res ){
				model_money::insertMoneyLog($draw['openid'],$money,2,4 ,$draw['userid']);
				pdo_update('zofui_tasktb_draw',array('status'=>2,'backreason'=>$_GPC['reason'],'dealtime'=>TIMESTAMP),array('uniacid'=>$_W['uniacid'],'id'=>$v));
				$suc ++;
				// 发消息
				Message::backmoney($draw['userid'],$draw['openid'],$money,$_GPC['reason'],'deposit');
			}
		}
		message('操作完成,成功处理'.$suc.'项',referer(),'success');
		
	}

	// comall 恢复正常
	if( checkSubmit('comall') ){
		
		if( empty( $_GPC['checkall'] ) ) message('先选择要操作的');
		$suc = 0;
		foreach ($_GPC['checkall'] as $v) {
			$draw = pdo_get('zofui_tasktb_draw',array('status'=>3,'uniacid'=>$_W['uniacid'],'id'=>$v));
			if( empty( $draw ) ) continue;

			$res = pdo_update('zofui_tasktb_draw',array('status'=>0,'backreason'=>'','dealtime'=>TIMESTAMP),array('uniacid'=>$_W['uniacid'],'id'=>$v));
			if( $res ) {
				$suc ++;
				// 发消息

			}
		}
		message('操作完成,成功处理'.$suc.'项',referer(),'success');
		
	}


	//导入支付
	if(checksubmit('import')) {
		set_time_limit(0);
		

		$uploadfile = returnfile( $_FILES );
		if (empty($uploadfile)) message('请选择要导入的CSV文件！');

		$handle = fopen($uploadfile, 'r'); 
		
		$result = input_csv($handle); //解析csv 
		
		if(count($result) <= 1) message('没有数据,文件内顶部的标题栏不能删除！',referer(),'error');
		if(count($result[1]) <= 1) message('文件数据不对，文件编辑好快递后另存为:CSV(逗号分隔).*csv 文件格式',referer(),'error');
		
		$success = 0;
		foreach($result as $k => $v){

			if($k >= 1){ //第0个是上面的标题栏

				$id = iconv('gb2312', 'utf-8', trim($v[0])); //中文转码 
				$data['status'] = 1;
				$data['dealtime'] = TIMESTAMP;

				$info = pdo_get('zofui_tasktb_draw',array('uniacid'=>$_W['uniacid'],'id'=>$id));
				if($info['status'] != 0 ) continue; // 不是待支付
				if( $info['paytype'] != 1 ) continue; // 不是支付宝支付

				$res = pdo_update('zofui_tasktb_draw',$data,array('uniacid'=>$_W['uniacid'],'id'=>$id));
				
				if($res){
					// 发消息
					Message::sucmoney($info['userid'],$info['openid'],$info['money'],'支付到支付宝','deposit');
					$success ++;
				} 
			}
		}
		fclose($handle); //关闭指针 
		message('操作完成,成功支付'.$success.'项',referer(),'success');
		
	}

	// 批量支付支付宝
	if(checksubmit('alipaypayall')){
		if( empty( $_GPC['checkall'] ) ) message('先选择要操作的');
		$suc = 0;
		foreach ($_GPC['checkall'] as $v) {
			$draw = pdo_get('zofui_tasktb_draw',array('status'=>0,'uniacid'=>$_W['uniacid'],'id'=>$v));
			if( empty( $draw ) ) continue;

			$res = pdo_update('zofui_tasktb_draw',array('status'=>1,'backreason'=>'','dealtime'=>TIMESTAMP),array('uniacid'=>$_W['uniacid'],'id'=>$v));
			if( $res ) {
				$suc ++;
				// 发消息
				Message::sucmoney($draw['userid'],$draw['openid'],$draw['money'],'支付到支付宝','deposit');
			}
		}
		message('操作完成,成功处理'.$suc.'项',referer(),'success');
	}

	//批量删除
	if(checksubmit('deleteall')){
		$res = WebCommon::deleteDataInWeb($_GPC['checkall'],'zofui_tasktb_draw');
		message('操作完成,成功删除'.$res[0].'项，失败'.$res[1].'项',referer(),'success');
	}

	//批量删除保证金记录
	if(checksubmit('deletealllog')){
		$res = WebCommon::deleteDataInWeb($_GPC['checkall'],'zofui_tasktb_moneylog');
		message('操作完成,成功删除'.$res[0].'项，失败'.$res[1].'项',referer(),'success');
	}


	
	if( in_array($_GPC['op'],array('waitpay','payed','back','refuse','alipay','qrpay')) ){


		$where = array('uniacid'=>$_W['uniacid']);
		$where['type'] = 2;
		$order = ' `id` DESC ';
		$num = empty($_GPC['pnum']) ? 10 : $_GPC['pnum'];
		if( $_GPC['op'] == 'alipay' && $_GPC['download'] == 1 ){
			$num = 10000;
		}

		if( $_GPC['op'] == 'waitpay' ){
			$where['status'] = 0;
			$where['paytype'] = 0;
		} 
		if( $_GPC['op'] == 'payed' ) $where['status'] = 1;
		if( $_GPC['op'] == 'back' ) $where['status'] = 2;
		if( $_GPC['op'] == 'refuse' ) $where['status'] = 3;
		if( $_GPC['op'] == 'alipay' ){
			$where['status'] = 0;
			$where['paytype'] = 1;
		} 
		if( $_GPC['op'] == 'qrpay' ){
			$where['status'] = 0;
			$where['paytype'] = 2;
		}	

		$info = Util::getAllDataInSingleTable('zofui_tasktb_draw',$where,intval( $_GPC['page'] ),$num,$order,false,true,' * ');
		
		$list = $info[0];
		$pager = $info[1];
		
		if( !empty( $list ) ){
			foreach ($list as &$v) {
				$v['user'] = model_user::getSingleUser( $v['userid'] );
			}
		}

		if(!empty($_GPC['download'])){ // 下载
			downLoadOrder($list);
		}

	}elseif( $_GPC['op'] == 'log' ){
		
		
		$where = array('uniacid'=>$_W['uniacid']);
		$where['mtype'] = 2;
		$order = ' `id` DESC ';

		$info = Util::getAllDataInSingleTable('zofui_tasktb_moneylog',$where,intval( $_GPC['page'] ),10,$order,false,true,' * ');
		
		$list = $info[0];
		$pager = $info[1];
		
		if( !empty( $list ) ){
			foreach ($list as &$v) {
				$v['user'] = model_user::getSingleUser( $v['userid'] );
			}
		}


	}elseif( $op == 'deletelog' ){
		$res = WebCommon::deleteSingleData($_GPC['id'],'zofui_tasktb_moneylog');
		if($res) message('删除成功',referer(),'success');

	}

	

	// 删除单个
	if($op == 'delete'){
		$res = WebCommon::deleteSingleData($_GPC['id'],'zofui_tasktb_draw');
		if($res) message('删除成功',referer(),'success');
	}
	
	
	//下载表格
	function downLoadOrder($list){
		
		/* 输入到CSV文件 */
		$html = "\xEF\xBB\xBF".$html; //添加BOM
		/* 输出表头 */		
		$html .= '数据id(导入支付会根据这个id匹配数据，不要修改，否则造成数据出错)' . "\t,";	
		$html .= '提取人' . "\t,";		
		$html .= '提取金额' . "\t,";
		$html .= '提取时间' . "\t,";
		$html .= '支付宝名称' . "\t,";
		$html .= '支付宝账户' . "\t,";		
		$html .= "\n";
			
 		foreach((array)$list as $k => $v){	

 			$time = date('Y-m-d H:i:s',$v['createtime']);

			$html .= $v['id'] . "\t,";
			$html .= $v['user']['nickname'] . "\t,";					
			$html .= $v['money'] . "\t,";
			$html .= $time . "\t,";
			$html .= $v['alipayname'] . "\t,";
			$html .= $v['alipay'] . "\t,";			
			$html .= "\n"; 
			
		}
		/* 输出CSV文件 */
		header("Content-type:text/csv");
		header("Content-Disposition:attachment; filename=提保证金列表.csv");
		echo $html;
		exit;
		
	}

	function input_csv($handle) { 
		$out = array (); 
		$n = 0; 
		while ($data = fgetcsv($handle, 10000)) { 
			$num = count($data); 
			for ($i = 0; $i < $num; $i++) { 
				$out[$n][$i] = $data[$i]; 
			} 
			$n++; 
		} 
		return $out; 
	} 

	function returnfile( $file ){
		return 	$file['inputfile']['tmp_name'];
	}

	include $this->template('web/'.$_W['mtemp'].'/deposit');