<?php

class Message 
{
	
	
	/*
	任务处理通知
	{{first.DATA}}
	任务名称：{{keyword1.DATA}}
	通知类型：{{keyword2.DATA}}
	{{remark.DATA}}
	*/
	public static function sendmessage($uid,$openid,$url,$messagestr,$name,$type,$mark='',$time=1) {
		global $_W;

		$data = array(
			'uniacid' => $_W['uniacid'],
			'openid' => $openid,
			'uid' => $uid,
			'type' => $type,
			'remark' => $name,
			'url' => $url,
			'createtime' => TIMESTAMP,
			'content' => $messagestr,
			'isbig' => in_array($type, array('收到私包任务提醒','回复任务通知','任务失败通知','任务审核通知','试用任务失败通知','任务通过审核通知','任务提交通知','审核任务通知','通过担保任务通知','担保任务通知','系统提醒')) ? 1 : 0,
			'istop' => in_array($type, array('系统提醒')) ? 1 : 0,
		);
		pdo_insert('zofui_tasktb_imess',$data);
		
		if( empty( $openid ) || is_numeric( $openid ) ) return false;
		$set = Util::getModuleConfig();

		$name = empty( $name ) ? '无' : $name;

		$msg_json = '{
           	"touser":"' . $openid . '",
           	"template_id":"' . $set['mid'] . '",
           	"url":"' . $url . '",
           	"topcolor":"#173177",
           	"data":{
               	"first":{
                   "value":"' . $messagestr .'",
                   "color":"#777777"
               	},
               	"keyword1":{
					"value":"'.$name.'",
               		"color":"#000000"
				},				
               	"keyword2":{
					"value":"' . $type .'",
               		"color":"#777777"
				},
               	"keyword3":{
					"value":"无",
               		"color":"#777777"
				},				
               	"remark":{
                   "value":"' . $mark . '",
                   "color":"#777777"
               	}
           	}
        }';
		
		return self::commonPostMessage($msg_json,$time);
	}	
	
	
	//组合字符串
	static function structMessage($array){
		$str = '';
		if(is_array($array)){
			foreach($array as $k => $v){
				$v = cutstr($v,30, false);
				$str .= $k.'：'.$v.'\n';
			}
		}
		return trim($str,'\\n');
	}	

	// 测试通知
	static function testmess( $uid,$openid){
		global $_W;
		
		$url = Util::createModuleUrl('index');
		$i_item = '测试消息';
		$array = array(
			'消息内容' => '测试消息',
		);
				
		$str = $i_item.'\n'.self::structMessage($array);
		return self::sendmessage($uid,$openid,$url,$str,$title,'测试消息',$mark,60);

	}


	// 系统提醒
	static function sysmess( $uid,$openid,$mess){
		global $_W;
		$url = Util::createModuleUrl('imess');
		$i_item = '系统提醒:'.$mess;
		$array = array(
			'提醒内容' => $mess,
		);
				
		$str = $i_item.'\n'.self::structMessage($array);
		return self::sendmessage($uid,$openid,$url,$str,$title,'系统提醒',$mark,60);
	}

	// 新任务通知
	static function newTaskmess( $uid,$openid,$title,$fee,$taskid,$item,$mark){
		global $_W;
		
		$url = Util::createModuleUrl('task',array('id'=>$taskid));
		$i_item = empty( $item ) ? '有人发布了一项新任务，点击接任务' : $item;
		$array = array(
			'任务赏金' => $fee,
		);
		
		$str = $i_item.'\n'.self::structMessage($array);
		return self::sendmessage($uid,$openid,$url,$str,$title,'任务提醒',$mark);

	}

	// 回复投诉
	static function complainMess( $uid,$openid,$content,$title,$item='',$mark=''){
		global $_W;
		
		$url = Util::createModuleUrl('index');
		$i_item = empty( $item ) ? '你的投诉已处理' : $item;
		$array = array(
			'投诉内容' => $content,
			'处理结果' => $title,
		);
		
		$str = $i_item.'\n'.self::structMessage($array);
		return self::sendmessage($uid,$openid,$url,$str,$title,'投诉处理通知',$mark);

	}

	// 认证通过通知
	static function authpassmess( $uid,$openid ){
		global $_W;
		
		$url = Util::createModuleUrl('index');
		$i_item = '您已通过认证';
		
		$str = $i_item.'\n'.self::structMessage($array);
		return self::sendmessage($uid,$openid,$url,$str,$title,'认证提醒',$mark);

	}
	// 认证不通过通知
	static function authnopassmess( $uid,$openid,$reason){
		global $_W;
		
		$url = Util::createModuleUrl('set');
		$i_item = '您未通过认证，可点击此处填写资料重新认证';
		if( !empty( $reason ) ) {
			$array = array(
				'失败原因' => $reason,
			);
		}
		
		$str = $i_item.'\n'.self::structMessage($array);
		return self::sendmessage($uid,$openid,$url,$str,$title,'认证提醒',$mark);

	}

	//收到私包任务提醒 type1是索要的 type2是发送的
	static function amessage($uid,$openid,$title,$fee,$limittime,$taskid,$type=1){
		global $_W;
		$mess = model_mess::getMess( $openid );
		if( $mess['getpri'] == 1 ) return false;

		$url = Util::createModuleUrl('privatetask',array('id'=>$taskid));
		$i_item = ($type == 1)?'有人向你索要一个私包任务，您可以点击此处去处理任务。':'有人给你发了一个私包任务，您可以点击此处去处理任务';
		$array = array(
			'悬赏金额' => $fee,
			'完成时间' => '任务限时'. $limittime .'小时'
		);
		
		$str = $i_item.'\n'.self::structMessage($array);
		$mark = '可在我的-通知设置内关闭此通知';
		return self::sendmessage($uid,$openid,$url,$str,$title,'收到私包任务提醒',$mark);
	}


	//私包任务变化通知
	static function cmessage($uid,$openid,$title,$from,$taskid){
		global $_W;
		$url = Util::createModuleUrl('privatetask',array('id'=>$taskid));
		if($from == 'paytaskmoney'){
			$statusstr = '等待您完成任务';
			$i_item = '您索要的任务被雇主接受并支付了赏金，点击此处可去任务页面查看详情。';
		}elseif($from == 'refusegeivetask'){
			$statusstr = '任务已取消';
			$i_item = '您索要的任务被雇主拒绝了，点击此处可去任务页面查看详情。';			
		}elseif($from == 'completetask'){
			$statusstr = '等待您审核任务';
			$i_item = '您有任务被雇员完成了，点击此处可去任务页面查看详情。';			
		}elseif($from == 'canceltask'){
			$statusstr = '任务已取消';
			$i_item = '雇员主动取消了正在执行的任务，资金已退回到您的'.$_W['cname'].'中。点击此处可去任务页面查看详情。';			
		}elseif($from == 'confirmtask'){
			$statusstr = '任务已完成';
			$i_item = '雇主确认完成了任务，奖励已发放到您的'.$_W['cname'].'中，点击此处可去任务页面查看详情。';			
		}elseif($from == 'confirmrefuse'){
			$statusstr = '等待您确认';
			$i_item = '雇主拒绝了您执行的任务结果，请及时与雇主沟通，以免产生矛盾。点击此处可去任务页面查看详情。';			
		}elseif($from == 'acceptrefuse'){
			$statusstr = '任务已取消';
			$i_item = '雇员接受了您对任务结果的拒绝。点击此处可去任务页面查看详情。';			
		}elseif($from == 'omplainboss'){
			$statusstr = '客服协调中';
			$i_item = '您对任务的结果的拒绝被雇员投诉，请等待客服的处理。点击此处可去任务页面查看详情。';			
		}elseif($from == 'workertaketask'){
			$statusstr = '任务执行中';
			$i_item = '您发送的任务被雇员接受了，点击此处可去任务页面查看详情。';			
		}elseif($from == 'workerrefusetask'){
			$statusstr = '任务已取消';
			$i_item = '您发送的任务被雇员拒绝了，点击此处可去任务页面查看详情。';			
		}elseif($from == 'admindealtoboss'){
			$statusstr = '任务已取消';
			$i_item = '您有一个申诉中的任务已经管理员处理后将资金退还到您的'.$_W['cname'].'中。点击此处可去任务页面查看详情。';			
		}elseif($from == 'admindealtoworker'){
			$statusstr = '任务已完成';
			$i_item = '您有一个申诉中的任务已经管理员处理后将资金发放到您的'.$_W['cname'].'中。点击此处可去任务页面查看详情。';	
		}elseif($from == 'pubtask'){
			$statusstr = '已发布';
			$i_item = '有会员发布了一个普通任务，点击此处可去任务页面查看详情。';	
			$url = Util::createModuleUrl('task',array('id'=>$taskid));
		}
				
		$array = array(
			'任务状态' => $statusstr
		);
		
		$str = $i_item.'\n'.self::structMessage($array);
		return self::sendmessage($uid,$openid,$url,$str,$title,'任务处理通知');
	}

	// 留言提问
	static function sendmsg($uid,$openid,$taskid,$content,$nick,$type=0){
		$mess = model_mess::getMess( $openid );
		if( $mess['messaged'] == 1 ) return false;

		$url = Util::createModuleUrl('task',array('id'=>$taskid));
		if( $type == 1 ) {
			$url = Util::createModuleUrl('tbtask',array('id'=>$taskid));
		}
		
		$array = array(
			'留言好友' => $nick,
			'留言内容' => $content,
		);
		$title = '无';
		$str = '有人在您发布的任务提问，点击进入任务\n'.self::structMessage($array);
		$mark = '可在我的-通知设置内关闭此通知';
		return self::sendmessage($uid,$openid,$url,$str,$title,'留言通知',$mark);
	}	
	
	// 回复留言
	static function replymsg($uid,$openid,$taskid,$content,$title,$nick,$type=0){
		$mess = model_mess::getMess( $openid );
		if( $mess['getmessage'] == 1 ) return false;

		if( empty( $type ) ) $url = Util::createModuleUrl('task',array('id'=>$taskid));
		if( $type == 1 ) $url = Util::createModuleUrl('tbtask',array('id'=>$taskid));

		$array = array(
			'回复内容' => $content,
		);
		$str = $nick.'回复了您的提问留言，点击查看\n'.self::structMessage($array);
		$mark = '可在我的-通知设置内关闭此通知';
		return self::sendmessage($uid,$openid,$url,$str,$title,'留言通知',$mark);
	}	

	// 回复通知
	static function toPuber($uid,$openid,$taskid,$content,$title,$nick){
		$mess = model_mess::getMess( $openid );
		if( $mess['taked'] == 1 ) return false;
		
		$url = Util::createModuleUrl('task',array('id'=>$taskid));	
		$array = array(
			'回复内容' => $content, 
		);
		$str = $nick.'回复了您的任务，点击处理\n'.self::structMessage($array);
		$mark = '可在我的-通知设置内关闭此通知';
		return self::sendmessage($uid,$openid,$url,$str,$title,'回复任务通知',$mark);
	}

	// 采纳任务
	static function agreetask($uid,$openid,$taskid,$money,$ewai,$server,$title,$nick){
		global $_W;
		$mess = model_mess::getMess( $openid );
		if( $mess['reply'] == 1 ) return false;

		$url = Util::createModuleUrl('task',array('id'=>$taskid));	
		$array = array(
			'任务赏金' => $money.' '.$_W['cper'],
			'额外奖励' => $ewai.' '.$_W['cper'],
			'扣服务费' => $server.' '.$_W['cper'],
		);
		$str = $nick.'采纳了您的回复，点击查看\n'.self::structMessage($array);
		$mark = '可在我的-通知设置内关闭此通知';
		return self::sendmessage($uid,$openid,$url,$str,$title,'任务成功通知',$mark);
	}

	// 拒绝任务
	static function refusetask($uid,$openid,$taskid,$reason,$title,$nick){
		$mess = model_mess::getMess( $openid );
		if( $mess['reply'] == 1 ) return false;

		$url = Util::createModuleUrl('task',array('id'=>$taskid));	
		$array = array(
			'拒绝理由' => $reason,
		);
		$str = $nick.'拒绝了您的回复，点击查看\n'.self::structMessage($array);
		$mark = '可在我的-通知设置内关闭此通知';
		return self::sendmessage($uid,$openid,$url,$str,$title,'任务失败通知',$mark);
	}	

	// 结算任务
	static function counttask($uid,$openid,$taskid,$backmoney,$title){
		global $_W;
		$mess = model_mess::getMess( $openid );
		if( $mess['count'] == 1 ) return false;

		$url = Util::createModuleUrl('task',array('id'=>$taskid));	
		$array = array(
			'剩余赏金' => $backmoney . $_W['cper'],
		);
		$str = '您发布的任务已被结算，点击查看任务\n'.self::structMessage($array);
		$mark = '可在我的-通知设置内关闭此通知';
		return self::sendmessage($uid,$openid,$url,$str,$title,'任务结算通知',$mark);
	}		

	// 审核任务
	static function verifytask($uid,$openid,$taskid,$title,$type,$reason=''){
		$url = Util::createModuleUrl('task',array('id'=>$taskid));	

		if( $type == 1 ){ // 审核通过
			$str = '您发布的任务已被审核通过，点击查看任务\n';
		}else{
			$array = array(
				'未过原因' => empty($reason) ? '无' : $reason,
			);
			$str = '您发布的任务审核未通过，点击查看任务\n'.self::structMessage($array);
		}
		return self::sendmessage($uid,$openid,$url,$str,$title,'任务审核通知');
	}

	// 获得下级收益提成
	static function downgive($uid,$openid,$money,$title,$nick){
		$mess = model_mess::getMess( $openid );
		if( $mess['downmoney'] == 1 ) return false;

		$url = Util::createModuleUrl('money',array('op'=>'log'));	

		$array = array(
			'收益金额' => $money,
			'收益类型' => '下级收益提成',
		);
		$str = '您的合伙人['.$nick.']给您交保护费啦，点击查看详情\n'.self::structMessage($array);
		$mark = '可在我的-通知设置内关闭此通知';
		return self::sendmessage($uid,$openid,$url,$str,$title,'收益通知',$mark);
	}

	// 退回提现
	static function backmoney($uid,$openid,$money,$reason,$type){
		global $_W;
		$url = Util::createModuleUrl('money',array('op'=>'log'));	

		$array = array(
			'退回金额' => $money,
			'退回原因' => empty( $reason ) ? '无' : $reason,
		);
		$typestr = $_W['cname'];
		if( $type == 'deposit' ) $typestr = '保证金';

		$str = '您提交的'.$typestr.'提现申请被退回\n'.self::structMessage($array);

		return self::sendmessage($uid,$openid,$url,$str,$title,'退回提现通知');
	}

	// 拒绝提现
	static function refusemoney($uid,$openid,$money,$reason,$type){
		global $_W;
		$url = Util::createModuleUrl('money',array('op'=>'log'));	

		$array = array(
			'提现金额' => $money,
			'拒绝原因' => empty( $reason ) ? '无' : $reason,
		);
		$typestr = $_W['cname'];
		if( $type == 'deposit' ) $typestr = '保证金';

		$str = '您提交的'.$typestr.'提现申请被拒绝\n'.self::structMessage($array);

		return self::sendmessage($uid,$openid,$url,$str,$title,'拒绝提现通知');
	}	

	// 提现成功
	static function sucmoney($uid,$openid,$money,$resstr,$type){
		global $_W;
		$url = Util::createModuleUrl('money',array('op'=>'log'));	

		$array = array(
			'提现金额' => $money,
			'到账方式' => $resstr,
		);
		$typestr = $_W['cname'];
		if( $type == 'deposit' ) $typestr = '保证金';

		$str = '您提交的'.$typestr.'提现申请已通过并支付到账\n'.self::structMessage($array);

		return self::sendmessage($uid,$openid,$url,$str,$title,'提现成功通知');
	}
	

	// 新增一名下级
	static function getdown($uid,$openid,$nick){
		$mess = model_mess::getMess( $openid );
		if( $mess['newdown'] == 1 ) return false;

		$url = Util::createModuleUrl('down',array('op'=>'down'));	
		$array = array(
			'合伙人昵称' => $nick,
		);
		$str = '恭喜您新增一名合伙人，合伙人做任务获得赏金您也可以获得提成\n'.self::structMessage($array);
		$mark = '可在我的-通知设置内关闭此通知';
		return self::sendmessage($uid,$openid,$url,$str,$title,'新增合伙人通知',$mark);
	}
	
	// 获赠疲劳值
	static function getAct($uid,$openid,$nick,$limi,$num){
		$mess = model_mess::getMess( $openid );
		if( $mess['downact'] == 1 ) return false;

		$url = Util::createModuleUrl('index',array('op'=>'index'));	
		$array = array(
			'合伙人昵称' => $nick,
			'增加数量' => $num,
		);
		$str = '您的合伙人今日已消耗'.$limi.'疲劳值，系统赠送您'.$num.'疲劳值，赶紧去接任务吧\n'.self::structMessage($array);
		$mark = '可在我的-通知设置内关闭此通知';
		return self::sendmessage($uid,$openid,$url,$str,$title,'赠送疲劳值',$mark);
	}	

	// 完成试用任务
	static function sucusetask($uid,$openid,$taskid,$money,$title,$nick){
		global $_W;
		
		$url = Util::createModuleUrl('task',array('id'=>$taskid));	
		$array = array(
			'任务赏金' => $money.' '.$_W['cper'],
		);
		$str = $nick.'将您的试用任务设为完成，点击查看\n'.self::structMessage($array);
		//$mark = '可在我的-通知设置内关闭此通知';
		return self::sendmessage($uid,$openid,$url,$str,$title,'试用任务完成通知');
	}

	// 试用任务失败
	static function failusetask($uid,$openid,$taskid,$reason,$title){
		global $_W;
		
		$url = Util::createModuleUrl('task',array('id'=>$taskid));	
		$array = array(
			'失败原因' => $reason,
		);
		$str = '您申请的一个试用任务已失败，点击查看\n'.self::structMessage($array);
		//$mark = '可在我的-通知设置内关闭此通知';
		return self::sendmessage($uid,$openid,$url,$str,$title,'试用任务失败通知');
	}	

	// 试用任务通过审核
	static function passusetask($uid,$openid,$taskid,$title){
		global $_W;
		
		$url = Util::createModuleUrl('task',array('id'=>$taskid));	
		$array = array();
		$str = '您申请的试用任务已通过审核，请按提示完成任务，点击查看\n'.self::structMessage($array);
		//$mark = '可在我的-通知设置内关闭此通知';
		return self::sendmessage($uid,$openid,$url,$str,$title,'任务通过审核通知');
	}

	// 试用任务发提醒 普通任务发提醒
	static function noticeusetask($uid,$openid,$taskid,$title,$content){
		global $_W;
		
		$url = Util::createModuleUrl('task',array('id'=>$taskid,'type'=>1));	
		$array = array(
			'提醒内容' => $content,
		);
		$str = '雇主给您发了一条提醒消息，点击查看\n'.self::structMessage($array);
		//$mark = '可在我的-通知设置内关闭此通知';
		return self::sendmessage($uid,$openid,$url,$str,$title,'任务提醒通知');
	}

	// 抢试用任务 给雇主发消息getUsetask
	static function getUsetask($uid,$openid,$taskid,$title,$nick){
		global $_W;
		
		$url = Util::createModuleUrl('task',array('id'=>$taskid));	
		$array = array(
			'申请会员' => $nick,
		);
		$str = '您的试用任务有人申请，请审核。点击查看\n'.self::structMessage($array);
		//$mark = '可在我的-通知设置内关闭此通知';
		return self::sendmessage($uid,$openid,$url,$str,$title,'任务被申请通知');
	}	

	// 提交订单内容通知
	static function subOrder($uid,$openid,$taskid,$title,$nick,$content){
		global $_W;
		$mess = model_mess::getMess( $openid );
		if( $mess['usesuborder'] == 1 ) return false;

		$url = Util::createModuleUrl('task',array('id'=>$taskid));	
		$array = array(
			'提交会员' => $nick,
			'订单内容' => $content,
		);
		$str = '您的试用任务有人提交了订单内容。点击查看\n'.self::structMessage($array);
		//$mark = '可在我的-通知设置内关闭此通知';
		return self::sendmessage($uid,$openid,$url,$str,$title,'任务提交通知');
	}
	
	// 执行者补充内容通知
	static function useAddContent($uid,$openid,$taskid,$title,$nick,$content){
		global $_W;
		$mess = model_mess::getMess( $openid );
		if( $mess['useaddcontent'] == 1 ) return false;

		$url = Util::createModuleUrl('task',array('id'=>$taskid));	
		$array = array(
			'提交会员' => $nick,
			'补充内容' => $content,
		);
		$str = '您的试用任务有人补充提交了内容。点击查看\n'.self::structMessage($array);
		//$mark = '可在我的-通知设置内关闭此通知';
		return self::sendmessage($uid,$openid,$url,$str,$title,'任务补充通知');
	}	
	


		
	// 回复投诉
	static function replyComplain($uid,$openid,$taskid,$content,$title){

		$url = Util::createModuleUrl('task',array('id'=>$taskid));	
		$array = array(
			'回复内容' => $content,
		);
		$str = '管理员回复了您的投诉，点击查看任务\n'.self::structMessage($array);
		
		return self::sendmessage($uid,$openid,$url,$str,$title,'回复投诉通知',$mark);
	}	


	// 待审核担保任务
	static function takeTbtask($uid,$openid,$taskid,$title,$money,$tbmoney){
		$mess = model_mess::getMess( $openid );
		if( $mess['tbverify'] == 1 ) return false;

		$url = Util::createModuleUrl('tbtask',array('id'=>$taskid));	
		$array = array(
			'任务赏金' => $money.' 元',
			'担保金额' => $tbmoney.' 元',
		);
		$str = '您的担保任务已有人申请，请审核，点击查看\n'.self::structMessage($array);
		
		return self::sendmessage($uid,$openid,$url,$str,$title,'审核任务通知',$mark);
	}		


	// 审核通过担保任务
	static function passTbtask($uid,$openid,$taskid,$title,$money,$tbmoney){

		$url = Util::createModuleUrl('tbtask',array('id'=>$taskid));	
		$array = array(
			'任务赏金' => $money.' 元',
			'担保金额' => $tbmoney.' 元',
		);
		$str = '您接的担保任务已通过审核，请立即执行任务，点击查看\n'.self::structMessage($array);
		
		return self::sendmessage($uid,$openid,$url,$str,$title,'通过担保任务通知',$mark);
	}	

	// 审核不通过担保任务
	static function nopassTbtask($uid,$openid,$taskid,$title,$money,$reason){

		$url = Util::createModuleUrl('tbtask',array('id'=>$taskid));	
		$array = array(
			'任务赏金' => $money.' 元',
		);
		if( !empty( $reason ) ) $array['未过原因'] = $reason;

		$str = '您接的担保任务未通过审核，点击查看\n'.self::structMessage($array);
		
		return self::sendmessage($uid,$openid,$url,$str,$title,'未通过担保任务通知',$mark);
	}	

	// 提醒对方
	static function remindUser($uid,$openid,$title,$content,$url){
			
		$array = array(
			'提醒内容' => $content,
		);

		$str = '有人给你发了一个提醒，点击查看任务\n'.self::structMessage($array);
		return self::sendmessage($uid,$openid,$url,$str,$title,'担保任务提醒通知',$mark);
	}	

	// 发担保任务完成通知
	static function tbtaskCom($uid, $openid,$title,$taskid,$nick ){
		$url = Util::createModuleUrl('tbtask',array('id'=>$taskid));
		$array = array(
			'任务雇员' => $nick,
		);
		$str = '您的一项任务已有人提交完成任务，点击查看任务\n'.self::structMessage($array);
		return self::sendmessage($uid,$openid,$url,$str,$title,'担保任务通知',$mark);

	}

	// 完成担保任务
	static function comTbtask($uid,$openid,$taskid,$money,$server,$title){

		$url = Util::createModuleUrl('tbtask',array('id'=>$taskid));	
		$array = array(
			'任务赏金' => $money.' 元',
			'扣服务费' => $server.' 元',
		);
		$str = '您的担保任务已完成，点击查看详情\n'.self::structMessage($array);
		
		return self::sendmessage($uid,$openid,$url,$str,$title,'担保任务通知',$mark);
	}

	// 担保任务设为失败
	static function setFailTbtask($uid,$openid,$taskid,$reason,$title){

		$url = Util::createModuleUrl('tbtask',array('id'=>$taskid));	
		$array = array(
			'失败原因' => $reason,
		);
		$str = '您的担保任务已被雇主设为失败，请立即处理任务，点击查看\n'.self::structMessage($array);
		return self::sendmessage($uid,$openid,$url,$str,$title,'担保任务通知',$mark);
	}

	// 管理员初步判断申诉担保任务
	static function admingiveTbtask($uid,$openid,$taskid,$title,$lasttime,$type){

		$url = Util::createModuleUrl('tbtask',array('id'=>$taskid));	

		$array = array(
			'剩余时间' => $lasttime,
		);

		if( $type == 1 ) $str = '您的担保任务通过申诉，已被管理员初步判定对方胜诉，您可继续上传凭证，点击查看\n';
		if( $type == 2 ) $str = '您的担保任务通过申诉，已被管理员初步判定您胜诉，点击查看\n';		

		return self::sendmessage($uid,$openid,$url,$str,$title,'担保任务通知',$mark);
	}	


	// 担保任务申诉给雇主发消息
	static function tbComplain($uid,$openid,$taskid,$nick,$title){

		$url = Util::createModuleUrl('tbtask',array('id'=>$taskid));
		$array = array(
			'任务雇员' => $nick,
		);
		$str = '您的担保任务雇员提交申诉请求，请上传凭证供管理员判断任务归属，点击查看\n'.self::structMessage($array);
		
		return self::sendmessage($uid,$openid,$url,$str,$title,'担保任务通知',$mark);
	}


	//模板消息url
	static function getUrl1(){
		
		load() -> model('account');
		$access_token = WeAccount::token();
		$url1 = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=" . $access_token . "";		
		return $url1;
	}
	
	static function commonPostMessage($msg_json,$time=1){
		$url1 = self::getUrl1();

		$res = Util::httpPost($url1, $msg_json,'',$time);
		$res = json_decode((string)$res,true);	
						
		if($res['errmsg'] == 'ok') {
			return array('status'=>true);
		}else{
			return array('status'=>false,'msg'=>$res['errmsg']);
		}
	}	
	



/*************以下是发消息******************/	

	//增加待发消息
	// 1发给管理员的有会员发布消息
	static public function addMessage($type,$str,$openid='',$uid=''){
		global $_W;
		$data = array(
			'uniacid' => $_W['uniacid'],
			'type' => $type,
			'str' => $str,
			'openid' => $openid,
			'userid' => $uid,
		);
		$res = pdo_insert('zofui_tasktb_message',$data);
		return $res;
	}
	



	
}
