<?php 
	global $_GPC,$_W;
	$_GPC = Util::trimWithArray($_GPC);
	$userinfo = model_user::initUserInfo(); //用户信息	
	
	$page = intval( $_GPC['page'] );

	if(in_array($_GPC['op'],array('moneylog','depositlog'))){
		
		$where['uniacid'] = $_W['uniacid'];
		$where['userid'] = $userinfo['uid'];
		$where['mtype'] = 1; // 余额
		if( $_GPC['type'] == 'deposit' ) $where['mtype'] = 2; // 保证金

		$data = Util::getAllDataInSingleTable('zofui_tasktb_moneylog',$where,$page,10,'`id` DESC',false,false);

		foreach((array)$data[0] as $k=>$v){
			$time = date('Y-m-d H:i',$v['time']);
			
			$status = '其他';
			$instr = '';
			if( $where['mtype'] == 1 ){

				if( $v['type'] == 1 ) $status = '提现支出';
				if( $v['type'] == 2 ) $status = '充值收入';
				if( $v['type'] == 3 ) $status = '发私包任务扣除';
				if( $v['type'] == 4 ) $status = '退回私包任务赏金';
				if( $v['type'] == 5 ) $status = '私包任务收入';
				if( $v['type'] == 6 ) $status = '下级获得收益提成';
				if( $v['type'] == 7 ) $status = '发普通任务扣除';
				if( $v['type'] == 8 ) $status = '普通任务收益';
				if( $v['type'] == 9 ) $status = '扣除服务费';
				if( $v['type'] == 10 ) $status = '结算普通任务退回';
				if( $v['type'] == 11 ) $status = '连续任务额外奖励';
				if( $v['type'] == 12 ) $status = '追加任务扣除';
				if( $v['type'] == 14 ) $status = '退回提现';
				if( $v['type'] == 15 ) $status = '提现手续费';
				
				if( $v['type'] == 16 ) $status = '查看联系方式扣除';
				if( $v['type'] == 17 ) $status = '联系方式被查看奖励';
				if( $v['type'] == 18 ) $status = '发布广告扣除';
				if( $v['type'] == 19 ) $status = '发布试用任务扣除';
				if( $v['type'] == 20 ) $status = '试用任务收益';
				if( $v['type'] == 21 ) $status = '试用任务结束退回';
				if( $v['type'] == 22 ) $status = '审核担保任务扣除';
				if( $v['type'] == 23 ) $status = '担保任务保证金';
				if( $v['type'] == 24 ) $status = '担保任务收益';
				if( $v['type'] == 25 ) $status = '退回担保任务资金';
				if( $v['type'] == 26 ) $status = '退回担保任务保证金';
				if( $v['type'] == 27 ) $status = '发布担保任务扣除';

				if( $v['type'] == 28 ) $status = '下下级获得收益提成';
				if( $v['type'] == 29 ) $status = '下下下级获得收益提成';
				if( $v['type'] == 30 ) $status = '下级发布任务提成';
				if( $v['type'] == 31 ) $status = '下下级发布任务提成';
				if( $v['type'] == 32 ) $status = '下下下级发布任务提成';
				if( $v['type'] == 33 ) $status = '下级升级会员提成';
				if( $v['type'] == 34 ) $status = '下下级升级会员提成';
				if( $v['type'] == 35 ) $status = '下下下级升级会员提成';

				if( $v['type'] == 36 ) $status = '查看答案扣除';
				if( $v['type'] == 37 ) $status = '查看答案分成';
				if( $v['type'] == 38 ) $status = '领取宝箱收入';
				if( $v['type'] == 39 ) $status = '回馈奖励收入';
				if( $v['type'] == 40 ) $status = '兑换活跃度扣除';
				if( $v['type'] == 41 ) $status = '恢复任务扣除';
				
			}else{
				if( $v['type'] == 1 ) $status = '提取支出';
				if( $v['type'] == 2 ) $status = '充值收入';	
				if( $v['type'] == 4 ) $status = '退回提取';
				if( $v['type'] == 5 ) $status = '提取保证金手续费';		
			}
			if( $v['money'] > 0 ) $instr = 'money_log_in';

			$str .= <<<div
					<div class="money_log_item ">
						<div class="money_log_item_in item_cell_box">
							<div class="item_cell_flex">
								<p class="font_13px_999">{$time}</p>
								<p>{$status}</p>
							</div>
							<div class="money_log_money {$instr}">{$v['money']}</div>
						</div>
					</div>
div;


		}
	
		
		
	}elseif( in_array($_GPC['op'],array('findpuber','findaccer')) ){

		$where['uniacid'] = $_W['uniacid'];
		$where['status'] = 0;
		$order = '`id` DESC';
		if( $_GPC['op'] == 'findpuber' ){
			$where['ispub'] = 1;
			$order = ' `acceptnumber` DESC,`id` DESC ';
			$type = 'puber';
		} 
		if( $_GPC['op'] == 'findaccer' ){
			$where['isacc'] = 1;
			$order = ' `acceptednumber` DESC,`id` DESC ';
			$type = 'accer';
		}
				
		$where['deposit>'] = $_W['set']['findneed'];

		if( $_GPC['type'] > 0 ){
			$sstr = ' AND b.sortid = '.$_GPC['type'];
			$select = ' a.id,a.pubnumber,a.acceptnumber,a.replynumber,a.acceptednumber,a.nickname,a.headimgurl,a.guydesc,a.verifyend,a.verifystatus ';
			$data = model_guysort::guryList($where,$page,10,$order,false,false,$select,$sstr);			
		}else{
			$select = ' id,pubnumber,acceptnumber,replynumber,acceptednumber,nickname,headimgurl,guydesc,verifyend,verifystatus ';
			$data = Util::getAllDataInSingleTable('zofui_task_user',$where,$page,10,$order,false,false,$select);
		}
		
		foreach((array)$data[0] as $k=>$v){

			$url = $this->createMobileUrl('guy',array('id'=>$v['id'],'op'=>$type));

			$guystr = '';
			if( $_GPC['op'] == 'findpuber' ){

				if( $v['pubnumber'] <= 0 ){
					$per = '还未发布';
				}else{
					$per = '采纳率 '. round( $v['acceptnumber']/$v['pubnumber']*100 ,0) .'%';
				}
				$guystr = '<span class="fr guy_per font_mini">'.$per.'</span>';

			}elseif( $_GPC['op'] == 'findaccer' ){
				
				if( $v['replynumber'] <= 0 ){
					$per = '还未接任务';
				}else{
					$per = '完成率 '. round( $v['acceptednumber']/$v['replynumber']*100 ,0) .'%';
				}
				$guystr = '<span class="fr guy_per font_mini">'.$per.'</span>';
			}

			$headimgurl = tomedia( $v['headimgurl'] );
			$nickname = $v['nickname'];
			$isauth = model_user::isAuth( $v,$_W['set'] );
			if( $isauth == 2 ){
				$nickname = '<span class="authname" >'.$v['nickname'].'<font class="authicon"></font></span>';
			}
			

			$str .= <<<div
				<a href="{$url}">
					<div class="find_list_item ">
						<div class="find_list_item_in item_cell_box">
							<div class="find_item_left">
								<img src="{$headimgurl}">
							</div>	
							<div class="find_item_mid item_cell_flex">
								<p class="find_item_nick">{$nickname}  {$guystr}</p>
								<p class="find_item_desc">{$v['guydesc']}</p>
							</div>
						</div>
					</div>
				</a>
div;


		}
		

	}elseif( in_array($_GPC['op'],array('privatelistpuber','privatelistaccer')) ){

		$where['uniacid'] = $_W['uniacid'];
		
		if( $_GPC['op'] == 'privatelistpuber' ){ // 我发起的
			$where['pubuid'] = $userinfo['uid'];
		}
		if( $_GPC['op'] == 'privatelistaccer' ){
			$where['acceptuid'] = $userinfo['uid'];
		}

		$select = ' id,tasktitle,createtime,taskmoney,isend,accepter,puber,acceptuid,pubuid ';
		$data = Util::getAllDataInSingleTable('zofui_tasktb_privatetask',$where,$page,10,' `isend` ASC, `id` DESC ',false,false,$select);

		foreach((array)$data[0] as $k=>$v){

			$url = $this->createMobileUrl('privatetask',array('id'=>$v['id']));
			if( $_GPC['op'] == 'privatelistpuber' ){ // 我发起的
				$user = model_user::getSingleUser( $v['acceptuid'] );
			}else{
				$user = model_user::getSingleUser( $v['pubuid'] );
			}
			$nickname = $user['nickname'];
			$isauth = model_user::isAuth( $user,$_W['set'] );
			if( $isauth == 2 ){
				$nickname = '<span class="authname" >'.$user['nickname'].'<font class="authicon"></font></span>';
			}

			$time = Util::formatTime( $v['createtime'] );

			if( $v['isend'] == 0 ){
				$status = '<span class="publist_task_status publist_task_ing">进行中</span>';
			}else{
				$status = '<span class="publist_task_status">已结束</span>';
			}

			$$user['headimgurl'] = tomedia( $user['headimgurl'] );
			if( empty($user['headimgurl']) ) $user['headimgurl'] = '../addons/zofui_taskself/public/images/dhead.png';

			$str .= <<<div
				<div class="publist_item">
					<div class="publist_item_in">
					<a href="{$url}">
						<div class="publist_item_bottom item_cell_box item_cell_start">		
							<div class="publist_item_headpic">
								<img class="lazy" src="{$user['headimgurl']}" >
							</div>
							<div class="item_cell_flex">
								<li class="">
									<span class="nickname">{$nickname}</span>
									<span class="font_13px_999 fr">{$time}</span>
								</li>
								<li class="publist_item_title">
									{$v['tasktitle']}
								</li>
								<p class="tl">
									<span class="font_13px_999">赏金:<font class="font_ff5f27">{$v['taskmoney']}</font></span>
									{$status}
								</p>
							</div>
						</div>
					</a>
					</div>
				</div>
div;


		}
		


	
	}elseif( $_GPC['op'] == 'down' ){
	
		$where['uniacid'] = $_W['uniacid'];
		if( empty($_GPC['type']) ) $where['parent'] = $userinfo['id'];
		if( $_GPC['type'] == 1 ) $where['pp'] = $userinfo['id'];
		if( $_GPC['type'] == 2 ) $where['ppp'] = $userinfo['id'];

		$select = ' id,headimgurl,nickname,giveparent,givetwo,givethree ';
		$data = Util::getAllDataInSingleTable('zofui_task_user',$where,$page,10,' `id` DESC ',false,false,$select);

		foreach((array)$data[0] as $k=>$v){
			if(empty($v)) continue; 

			$give = $v['giveparent'];
			if( $_GPC['type'] == 1 ) $give = $v['givetwo'];
			if( $_GPC['type'] == 2 ) $give = $v['givethree'];

			$v['headimgurl'] = empty($v['headimgurl']) ? '../addons/zofui_taskself/public/images/dhead.png' : tomedia( $v['headimgurl'] );

			$str .= <<<div
				<div class="downa_item twodown" data-id="{$v['id']}">
					<div class="downa_item_in item_cell_box">
						<div class=" downa_item_l">
							<img src="{$v['headimgurl']}">
						</div>
						<div class="item_cell_flex downa_item_r nickname">
							{$v['nickname']}
						</div>
						<div class="downa_item_r font_mini font_13px_999">
							累计提成 <span class="pri-color">{$give}</span>
						</div>						
					</div>
				</div>
div;


		}


	}elseif( $_GPC['op'] == 'getdown' ){

		$where['uniacid'] = $_W['uniacid'];
		if( !empty( $_GPC['id'] ) ) $where['parent'] = $_GPC['id'];

		$select = ' id,headimgurl,nickname,giveparent,givetwo,givethree ';
		$data = Util::getAllDataInSingleTable('zofui_task_user',$where,1,999,' `id` DESC ',false,false,$select);

		if( empty( $data[0] ) ){
			Util::echoResult(201,'他没有下级');
		}

		$v['headimgurl'] = tomedia( $v['headimgurl'] );

		foreach((array)$data[0] as $k=>$v){

			$class = $_GPC['type'] == 2 ? 'threedown' : '';

			if( $_GPC['type'] == 2 ) $m = $v['givetwo'];
			if( $_GPC['type'] == 3 ) $m = $v['givethree'];

			$str .= <<<div
				<div class=" down_item {$class}" data-id="{$v['id']}" data-type="{$_GPC['type']}">
					<div class="down_item_in item_cell_box">
						<div class="down_item_l">
							<img src="{$v['headimgurl']}">
						</div>
						<div class="item_cell_flex down_item_r nickname">
							{$v['nickname']}
						</div>
						<div class="down_item_r font_mini font_13px_999">
							累计提成 <span class="font_ff5f27">{$m}</span>
						</div>						
					</div>
				</div>
div;


		}


	}elseif( $_GPC['op'] == 'tasklist' ){

		$where['uniacid'] = $_W['uniacid'];
		$where['taskid'] = $_GPC['pid'];
		if( !in_array($_GPC['type'], array(1,2,3)) ) $where['status>'] = 0.1;

		if( $_GPC['type'] == 1 ) {
			$where['userid'] = $userinfo['uid'];
			$where['status>'] = 1;
		}
		if( $_GPC['type'] == 2 ) $where['status'] = 1;
		if( $_GPC['type'] == 3 ) $where['status'] = 2;
		if( $_GPC['type'] == 4 ) $where['status'] = 3;

		$select = ' * ';
		//if( !empty( $_GPC['type'] ) ) $where['sortid'] = intval( $_GPC['type'] );

		$order = ' `id` DESC ';
		$data = Util::getAllDataInSingleTable('zofui_tasktb_taked',$where,$page,10,$order,5,false,$select,'','idx');
		
		$task = pdo_get('zofui_tasktb_task',array('uniacid'=>$_W['uniacid'],'id'=>$where['taskid']));

		foreach((array)$data[0] as $k=>$v){
			$time = Util::formatTime( $v['replytime'] );
			
			if( empty($v['type']) ) {
				$user = model_user::getSingleUser( $v['userid'] );
			}else{
				$user = array('nickname'=>$v['nick'],'headimgurl'=>$v['headimg'],'id'=>$v['falseuid']);
			}
			
			$nickname = $user['nickname'].'<font class="userid">('.$user['id'].')</font>';
			$isauth = model_user::isAuth( $user,$_W['set'] );
			if( $isauth == 2 ){
				$nickname = '<span class="authname" >'.$user['nickname'].'<font class="authicon"></font></span>  <font class="userid useridpl02 pl05">('.$user['id'].')</font>';
			}

			$img = '';
			if( !empty( $v['images'] ) ) $v['images'] = iunserializer( $v['images'] );
			if( !empty( $v['images'] ) && is_array( $v['images'] ) ){
				foreach ($v['images'] as $vv) {
					$img .= '<li class="need_show_images_item fl"><img src="'.tomedia( $vv ).'"></li>';
				}
			}

			$reason = '';
			if( $v['status'] == 3 && !empty( $v['reason'] ) ){
				$reason = '<p class="font_mini">被拒理由：'.$v['reason'].'</p>';
			}

			if( $_W['set']['isanw'] == 1 ){
				$where = array('uid'=>$userinfo['uid'],'taskid'=>$task['id'],'endtime>'=>TIMESTAMP);
				$anwtime = ' OR `endtime` = 0 ';
				if( $_W['set']['anwtime'] > 0 ){
					$anwtime = '';
				}
				$isreaded = Util::countDataNumber('zofui_tasktb_anwread',$where,$anwtime);
			}

			// 提醒内容
			if( $userinfo['uid'] == $task['userid'] || $userinfo['uid'] == $v['userid'] || $isreaded > 0 ){
				$remindstr = '';
				$remind = pdo_getall('zofui_tasktb_remindlog',array('uniacid'=>$_W['uniacid'],'takedid'=>$v['id'],'mtype'=>0));
				$showremindbtn = true;
				if( !empty( $remind ) ){
					$remindstr = '<div class="remind_box">';
					foreach ( $remind as $r ) {

						if( $r['createtime'] > (TIMESTAMP - 10*60) && $r['type'] == 0 ){
							$showremindbtn = false;
						}

						$remindstr .= <<<div
						<div class="item_cell_box remind_item">
							<li class="remin_nick">提醒：</li>
							<li class="item_cell_flex">{$r['content']}</li>
						</div>
div;
					}
					$remindstr .= '</div>';
				}
			}

			// 补充内容
			if( $userinfo['uid'] == $task['userid'] || $userinfo['uid'] == $v['userid']  || $isreaded > 0 ){
				$addcontentstr = '';
				$addlist = pdo_getall('zofui_tasktb_remindlog',array('uniacid'=>$_W['uniacid'],'takedid'=>$v['id'],'mtype'=>1));
				$showaddcontentbtn = true;
				if( !empty( $addlist ) ){
					$addcontentstr = '<div class="addcontent_box">';
					foreach ( $addlist as $r ) {

						if( $r['createtime'] > (TIMESTAMP - 0.1*60) ){
							$showaddcontentbtn = false;
						}
						$addimg = '';
						if( !empty( $r['images'] ) ) $r['images'] = iunserializer( $r['images'] );
						if( !empty( $r['images'] ) && is_array( $r['images'] ) ){
							foreach ($r['images'] as $rr) {
								$addimg .= '<li class="need_show_images_item fl"><img src="'.tomedia( $rr ).'"></li>';
							}
						}
						$addcontentstr .= <<<div
						<div class="item_cell_box addcontent_item">
							<div class="remin_nick">补充：</div>
							<div class="item_cell_flex">
								<li>{$r['content']}</li>
								{$addimg}
							</div>
						</div>
div;
					}
					$addcontentstr .= '</div>';
				}
			}


			// formcontent
			$formcontent = '';
			$subform = iunserializer( base64_decode( $v['subform'] ) );
			if( !is_array( $subform ) ) {
				$subform = iunserializer( $v['subform'] );
			}

			if( !empty( $subform ) ) {
				foreach ($subform as $vv) {
					if( $vv['type'] == 'img' ){
						$itemimg = '';
						foreach ($vv['value'] as $vvv) {
							$itemimg .= '<li class="need_show_images_item fl"><img src="'.tomedia( $vvv ).'"></li>';
						}
						$formcontent .= <<<div
						<div class="item_cell_box addcontent_item subform_citem">
							<div class="remin_nick">{$vv['name']}：</div>
							<div class="item_cell_flex oh subform_cin">
								{$itemimg}
							</div>
						</div>
div;
					}else{
						$formcontent .= <<<div
						<div class="item_cell_box addcontent_item subform_citem">
							<div class="remin_nick">{$vv['name']}：</div>
							<div class="item_cell_flex oh subform_cin">
								<li>{$vv['value']}</li>
							</div>
						</div>
div;
					}
				}
			}

			$contentstr = '';


			if( $task['ishide'] == 0 || $userinfo['uid'] == $task['userid'] || $userinfo['uid'] == $v['userid'] || $isreaded > 0 ){
				$contentstr = '<div class="task_reply_title">'.$v['content'].'</div><div class="need_show_images oh">'.$img.'</div>'.$formcontent.$addcontentstr.$reason.$remindstr;
			}else{
				$contentstr = '<li class="hide_tips">内容被隐藏</li>';
			}

			if( $v['isscan'] == 1 ){
				$contentstr = '<li class="hide_tips">内容被禁</li>';
			}

			$zanstr = '';
			if( $v['ds'] > 0 ){
				$zanstr = '<div><span class="zanicon"></span><span class="zanmoney">'. ($v['ds'] > 0 ? $v['ds'] : '') .'</span></div>';
			}
			if( $_W['set']['isds'] == 1 && $v['status'] == 2 && $v['isscan'] == 0 && $task['isread'] == 1 && ($isreaded > 0 || $userinfo['uid'] == $task['userid']) ){
				$zanstr = '<div><span class="zanicon"></span><span class="zanmoney">'. ($v['ds'] > 0 ? $v['ds'] : '') .'</span><span class="zanbtn pri-color pri-border" reid="'.$v['id'].'">打赏</span></div>';
			}

			$moneystr = '';
			if( $v['status'] == 2 ){
				$ewaistr = '';
				if( $v['ewai'] > 0 ) $ewaistr = '+'.$v['ewai'];
				$moneystr = '<span class="task_replay_in">'.$v['money'].$ewaistr.'</span>';
			} 
			
			$status = '';
			if( $v['status'] == 1 ) $status = '<span class="task_replay_status">待采纳</span>';
			if( $v['status'] == 2 ) $status = '<span class="task_replay_status font_green">已采纳</span>';
			if( $v['status'] == 3 ) $status = '<span class="task_replay_status font_ff5f27">被拒绝</span>';

			$botstr = '<div class="task_replay_bottom item_cell_box" ><div class="item_cell_flex pl0">'.$zanstr.'</div><div>'.$moneystr.$status.'</div></div>';
			if( $v['status'] == 1 && $userinfo['uid'] == $v['userid'] && $showaddcontentbtn ) {
				$botstr = '<div class="task_replay_bottom item_cell_box" ><div class="item_cell_flex"></div><div class="puber_deal_btn addcontent" reid="'.$v['id'].'">补充内容</div></div>';
			}

			$user['headimgurl'] = tomedia( $user['headimgurl'] );
			if( empty($user['headimgurl']) ) $user['headimgurl'] = '../addons/zofui_taskself/public/images/dhead.png';

			$anwvalue = $_W['set']['isanw'] == 1 && $_W['set']['anwnick'] == 1 ? ',知识力量值<font class="font_red">'. ($user['anwm']*100/100) .'</font>' : '';
			

			if( $_W['set']['isanw'] == 1 && $task['isread'] == 1 && $userinfo['uid'] == $task['userid'] ){

				$hidestr = '<div class="puber_deal_btn hide" reid="'.$v['id'].'">隐藏</div>';
				if( $v['isscan'] == 1 ){
					$hidestr = '<div class="puber_deal_btn show" reid="'.$v['id'].'">显示</div>';
				}
				$botstr = '<div class="task_replay_bottom item_cell_box " ><div class="item_cell_flex pl0">'.$zanstr.'</div>'.$hidestr.'</div>';
			}

			if( $v['status'] == 1 && $userinfo['uid'] == $task['userid'] ){ // 是发布者

				$remindbtn = '<div class="puber_deal_btn remind" reid="'.$v['id'].'">提醒</div>';

				if( !$showremindbtn ) $remindbtn = '';
				$botstr = <<<div
						<div class="task_replay_bottom item_cell_box " >
							<div class="item_cell_flex"></div>
							{$remindbtn}
							{$hidestr}
							<div class="puber_deal_btn agree" reid="{$v['id']}">采纳</div>
							<div class="puber_deal_btn refuse" reid="{$v['id']}">拒绝</div>
							<div class="puber_deal_check weui_cells_checkbox">
								<label class="weui_cell weui_check_label needsclick " >
									<div class="weui_cell_hd needsclick">
										<input type="checkbox" class="weui_check" name="reply[]" value="{$v['id']}" >
										<i class="weui_icon_checked"></i>
									</div>
									<div class="weui_cell_bd tl weui_cell_primary needsclick">
										<span class="form_tips needsclick">选择</span>
									</div>
								</label>						
							</div>
						</div>
div;
			}



			$str .= <<<div
				<div class="task_reply_item">
					<div class="task_reply_in">
						<div class="item_cell_box">
							<div class="task_reply_headimg">
								<img src="{$user['headimgurl']}" >
							</div>
							<div class="item_cell_flex task_content_body">
								<div class="oh">
									<span class="font_bold_name task_content_nick">{$nickname}</span> 
									<span class="font_13px_999 fr">{$time}</span>
								</div>
								<div>
									{$contentstr}
								</div>
							</div>
						</div>
						{$botstr}
					</div>
				</div>
div;


		}
		

	}elseif( $_GPC['op'] == 'getmeesage' ){

		$where['uniacid'] = $_W['uniacid'];
		$where['taskid'] = $_GPC['type'];
		$where['parent'] = 0;
		$where['type'] = $_GPC['from'] == 'tbtask' ? 1 : ( $_GPC['tasktype'] == 1 ? 2 : 0 );

		$select = ' a.*,b.headimgurl,b.nickname,b.id AS uid ';
		$data = model_task::getMessage($where,$page,5,' a.`id` DESC ',false,false,$select);
			

		$task = null;

		foreach((array)$data[0] as $k=>$v){
			$time = Util::formatTime( $v['time'] );

			if( empty( $task ) ) {
				if( empty($v['type']) ) $task = pdo_get( 'zofui_tasktb_task',array('id'=>$where['taskid']) );
				if( $v['type'] == 1 ) $task = pdo_get( 'zofui_tasktb_tbtask',array('id'=>$where['taskid']) );
			}

			$restr = '';
			if( $task['userid'] == $userinfo['uid'] ){
				$restr = '<span class="reply_message" mid="'.$v['id'].'">回复</span>';
			}

			$allreply = pdo_getall('zofui_tasktb_taskmessage',array('uniacid'=>$_W['uniacid'],'parent'=>$v['id']));

			$v['headimgurl'] = tomedia( $v['headimgurl'] );

			$replystr = '';
			if( !empty( $allreply ) ){

				

				if( !empty( $task['userid'] ) ){
					$puber = model_user::getSingleUser( $task['userid'] );
				}else{
					$puber['nickname'] = $_W['set']['sitename'];
				}

				foreach ($allreply as $kk => $vv) {
					$replystr .= <<<div
  						<div class="reply_message_item">
  							<li class="nickname" > 
  								<span style="max-width:4rem;overflow:hidden;">{$puber['nickname']}</span>
  							</li>
  							<li class="reply_message_content">{$vv['content']}</li>
  						</div>
div;
				}
			}

			$str .= <<<div
	  			<div class="popup_message_item item_cell_box">
	  				<div class="popup_message_l">
	  					<img src="{$v['headimgurl']}">
	  				</div>
	  				<div class="popup_message_r item_cell_flex">
	  					<p class="popup_message_nick nickname">{$v['nickname']} <font class="userid">({$v['uid']})</font> </p>
	  					<p class="popup_message_content">{$v['content']}</p>
	  					<p class="popup_message_time font_13px_999">{$time}{$restr}</p>
	  					<div class="reply_message_list">{$replystr}</div>
	  				</div>
	  			</div>
div;

		}
		


	}elseif( $_GPC['op'] == 'index' || $_GPC['op'] == 'taskbak' ){
		
		if( empty( $_GPC['icantake'] ) ){
			if( $_W['set']['isshowcounted'] == 0 ) $where['iscount'] = 0;
			$where['status'] = 0;
			$where['type'] = 0;

			$sstr = '';
			if( $_GPC['pid'] == 1 ) $where['levellim'] = 0; // 普通会员的
			if( $_GPC['pid'] == 2 ) { // 一级
				$sstr = ' AND levellim IN (1,3) ';
			}
			if( $_GPC['pid'] == 3 ) { // 二级
				$sstr = ' AND levellim IN (2,3) ';
			}
			if( $_GPC['pid'] == 4 ) $where['levellim'] = 3;

			if( $_GPC['pid'] == 5 ) $where['levellim'] = 0;
			
			if( !empty( $_GPC['type'] ) ) $where['sortid'] = intval( $_GPC['type'] );
			if( !empty($_GPC['search']) ) {
				if( strpos($_GPC['search'], 'http') !== false ){
					$where['link@'] = $_GPC['search'];
				}else{
					$where['title@'] = $_GPC['search'];
				}
			}

			$select = ' id,title,money,scan,isempty,istop,start,end,puber,num,limitnum,iscount,ispause,userid,address,falsepuber,province,city,country,idcode,headimg ';

			$order = ' `iscount` ASC,`isstart` ASC,`isempty` ASC,`istop` DESC,`id` DESC ';
			$data = Util::getAllDataInSingleTable('zofui_tasktb_task',$where,$page,10,$order,10,false,$select,$sstr,'idx');
		
		}else{

			$ipl = model_task::ipLimit( $_W['set']['maxip'] );

			if( !$ipl ){
				$today = strtotime( date('Y-m-d',TIMESTAMP) );
				$endtime = TIMESTAMP;
				
				$select = ' id,title,money,scan,isempty,istop,start,end,puber,num,limitnum,iscount,userid,address,falsepuber,province,city,country,idcode,headimg ';
				$order = ' `iscount` ASC,`isstart` ASC,`isempty` ASC,`istop` DESC,`id` DESC ';

				if( in_array($userinfo['sex'], array(1,3)) ){
					$sexstr = ' AND sex IN (0,1) ';
				}
				if( in_array($userinfo['sex'], array(2,4)) ){
					$sexstr = ' AND sex IN (0,2) ';
				}
				if( !empty($_GPC['search']) ){
					
					if( strpos($_GPC['search'], 'http') !== false ){
						$sexstr .= ' AND `link` LIKE \'%'.$_GPC['search'].'%\' ';
						$sear = 1;

					}else{
						$sexstr .= ' AND `title` LIKE \'%'.$_GPC['search'].'%\'';
					}
				}

				if( !empty( $_GPC['type'] ) ) $sortstr = ' AND `sortid` = '.$_GPC['type'];

				$countstr = '';
				if( $_W['set']['isshowcounted'] == 0 ) $countstr = ' AND iscount = 0 ';


				$sstr = '';
				if( $_GPC['pid'] == 1 ) {
					$sstr = ' AND levellim = 0 ';
				}
				if( $_GPC['pid'] == 2 ) { // 一级
					$sstr = ' AND levellim IN (1,3) ';
				}
				if( $_GPC['pid'] == 3 ) { // 二级
					$sstr = ' AND levellim IN (2,3) ';
				}
				if( $_GPC['pid'] == 4 ) {
					$sstr = ' AND levellim = 3 ';
				}


				$selectStr = " SELECT $select FROM ".tablename('zofui_tasktb_task')." AS a WHERE a.`limitnum` > ( SELECT count(*) FROM ".tablename('zofui_tasktb_taked')." AS b WHERE b.uniacid = ".$_W['uniacid']." AND b.userid = '".$userinfo['uid']."'  AND ( b.status IN (2,3) OR b.endtime > ".TIMESTAMP." ) AND b.taskid = a.`id` ) ".$countstr." AND (userid != '".$userinfo['uid']."' || puber is null ) AND `uniacid` = ".$_W['uniacid']." AND status = 0 AND `type` = 0 AND `isempty` = 0 AND `ispause` = 0 ".$sexstr.$sortstr.$sstr;
				

				if( $sear == 1 ) {
					$selectStr = " SELECT $select FROM ".tablename('zofui_tasktb_task')." WHERE `uniacid` = ".$_W['uniacid']." AND `type` = 0 ".$sexstr.$sortstr.$sstr;
				}
				
				$data = Util::fetchFunctionInCommon($countStr,$selectStr,array(),$page,10,$order,false,false);
				
			}
		}

		foreach((array)$data[0] as $k=>$v){


			$tid = empty($_W['set']['tidtype']) || empty($v['idcode']) ? $v['id'] : $v['idcode'];
			if( empty($_W['set']['tidtype']) || empty($v['idcode']) ){
				$url = $this->createMobileUrl('task',array('id'=>$v['id']));
			}else{
				$url = $this->createMobileUrl('task',array('idcode'=>$v['idcode']));
			}

			if( !empty($_W['set']['hhbg']) ) $v['scan'] = ''; // 定制的

			// 屏蔽关键词
			$v['title'] = model_task::hideKey( $_W['set']['hidetxt'],$v['title'] );

			$user = array();
			if( !empty( $v['userid'] ) ){
				$user = model_user::getSingleUser( $v['userid'] );
				$user['headimgurl'] = tomedia( $user['headimgurl'] );
				if( empty($user['headimgurl']) ) $user['headimgurl'] = '../addons/zofui_taskself/public/images/dhead.png';
			}else{

				if( !empty($v['falsepuber']) ){
					$user = model_task::getFalsepub( $v['falsepuber'] );
					$user['headimgurl'] = tomedia($user['headimg']);
				}else{
					$user['headimgurl'] = tomedia( $_W['set']['logo'] );
					if( empty($user['headimgurl']) ) $user['headimgurl'] = '../addons/zofui_taskself/public/images/dhead.png';
					$user['nickname'] = $_W['set']['sitename'];
				}
			}
			if( !empty($v['headimg']) ){
				$user['headimgurl'] = tomedia($v['headimg']);
			}
			
			$thisstatus = model_task::getStatusInTask( $v,0,true );

			$iscantake = 1;
			$ising = 0;
			if( $v['isempty'] == 0 ){
				$statusstr = '<li class="index_item_status fr status_ing pri-color">任务进行中</li>';
				$ising = 1; // 进行中
			}else{
				$statusstr = '<li class="index_item_status fr status_no">已接完</li>';
				$iscantake = 0;
			}

			if( $v['ispause'] == 1 ){
				$statusstr = '<li class="index_item_status fr status_no">任务已关闭</li>';
				$iscantake = 0;
			}

			if( !empty( $thisstatus['status'] ) ){
				$statusstr = '<li class="index_item_status fr status_no">您不能接此任务</li>';
				if( $thisstatus['status'] == 1 ){
					$statusstr = '<li class="index_item_status fr status_no">您已接了此任务</li>';
				}
				$iscantake = 0;
			}

			if( $v['start'] > TIMESTAMP ){
				$statusstr = '<li class="index_item_status fr status_no">还未开始</li>';
				$iscantake = 0;
			}
			if( $v['iscount'] == 1 ) {
				$statusstr = '<li class="index_item_status fr status_no">已结束</li>';
				$iscantake = 0;
			}

			$topstr = '';
			if( $v['istop'] == 1 ){
				$topstr = '<span class="top_task">[顶]</span>';
			}

			$taskid = '<span class="font_mini">('.$tid.')</span>';

			$address = '';
			if( !empty($v['address']) && ($thisstatus['status'] != 2 || $userinfo['uid'] == $v['userid']) ){
				$address = '<div class="list_address">地址：'.$v['address'].'</div>';
			}
			
			$nickname = $user['nickname'];
			if( $_W['set']['isauth'] != 0 ){
				$isauth = model_user::isAuth( $user,$_W['set'] );
				if( $isauth == 2 ){
					$nickname = '<span class="authname" >'.$user['nickname'].'<font class="authicon"></font></span>';
				}
			}
			
			$liststy = empty($this->module['config']['liststy']) ? 'index_task_item1' : '';
			

			if( $_W['set']['lfsty'] == 1 ){
				$infostyle = empty($_W['set']['lfsty']) ? '' : 'infostylea';
				if( $ising == 1 ){
					$lasttime = $v['end'] - TIMESTAMP;
					if( $lasttime > 0 ) {
						$lastday = intval($lasttime/3600/24);
						$lastday = $lastday <= 1 ? '1天内' : $lastday.'天后';
						$statusstr = '<li class="index_item_status fr status_ing pri-color">'.$lastday.'到账</li>';
					}
				}
			}

			if( $_W['set']['liststy'] == 1 && $v['istop'] == 1 ){
				$topstr = '<span class="top_task">置顶</span>';
			}

			$isreadedstr = '';
			if( $_W['set']['rded'] == 1 ){
				$isrd = pdo_get('zofui_tasktb_readtask',array('type'=>0,'tid'=>$v['id'],'uid'=>$userinfo['uid']));
				if( !empty($isrd) ){
					$isreadedstr = 'readed';
				}
			}

		if( empty($this->module['config']['liststy']) ){
			$str .= <<<div
				<a href="{$url}">
					<div class="index_task_item {$infostyle}">
						<div class="index_task_item_in item_cell_box item_cell_start">
							<div class="index_item_left">
								<img src="{$user['headimgurl']}">
							</div>
							<div class="index_item_right item_cell_flex">
								<div class="index_item_top oh ">
									<li class="index_item_nick fl">{$nickname}</li>
									<li class="index_item_read fr">{$v['scan']}</li>
								</div>
								<div class="index_item_title {$isreadedstr}">{$topstr}{$v['title']}{$taskid}{$address}</div>
								<div class="index_item_bot oh">
									<li class="index_item_money fl">
										{$v['money']}
									</li>
									{$statusstr}
								</div>
							</div>
						</div>
					</div>
				</a>
div;
		}elseif( $this->module['config']['liststy'] == 1 ){

			$isact = $iscantake == 1 ? 'pri-color pri-border' : '';
			$str .= <<<div
				<a href="{$url}">
					<div class="index_task_item index_task_item1 {$infostyle}">
						{$topstr}
						<div class="index_task_item_in item_cell_box">
							<div class="index_item_left">
								<img src="{$user['headimgurl']}">
							</div>
							<div class="index_item_right item_cell_flex">
								<div class="index_item_top oh ">
									<li class="index_item_nick fl">{$nickname}</li>
								</div>
								<div class="index_item_title {$isreadedstr}">{$v['title']}</div>
								<div class="item1_icon">
									<span>{$v['money']}</span>
									{$statusstr}
								</div>
							</div>
							<div class="item1_btn {$isact}">接任务</div>
						</div>
					</div>
				</a>
div;
		} 

		}		

	}elseif( $_GPC['op'] == 'getconlist' ){

		$where['continueid'] = intval( $_GPC['type'] );
		
		$select = ' id,title,money,scan,isempty,iscount,istop,start,puber,userid,idcode ';

		$order = ' `isstart` ASC,`isempty` ASC,`istop` DESC,`start` ASC ';
		$data = Util::getAllDataInSingleTable('zofui_tasktb_task',$where,$page,100,$order,10,false,$select,'','idx');
		
		foreach((array)$data[0] as $k=>$v){
			
			$tid = empty($_W['set']['tidtype']) || empty($v['idcode']) ? $v['id'] : $v['idcode'];
			if( empty($_W['set']['tidtype']) || empty($v['idcode']) ){
				$url = $this->createMobileUrl('task',array('id'=>$v['id']));
			}else{
				$url = $this->createMobileUrl('task',array('idcode'=>$v['idcode']));
			}


			if( !empty( $v['userid'] ) ){
				$user = model_user::getSingleUser( $v['userid'] );
				$user['headimgurl'] = tomedia( $user['headimgurl'] );
				if( empty($user['headimgurl']) ) $user['headimgurl'] = '../addons/zofui_taskself/public/images/dhead.png';

			}else{
				$user['headimgurl'] = tomedia( $_W['set']['logo'] );
				$user['nickname'] = $_W['set']['sitename'];
			}

			if( $v['iscount'] == 0 ){
				if( $v['isempty'] == 0 ){
					$statusstr = '<li class="index_item_status fr status_ing">任务进行中</li>';
				}else{
					$statusstr = '<li class="index_item_status fr status_no">已接完</li>';
				}
				if( $v['start'] > TIMESTAMP ){
					$statusstr = '<li class="index_item_status fr status_no">还未开始</li>';
				}
			}else{
				$statusstr = '<li class="index_item_status fr status_no">已结束</li>';
			}

			$topstr = '';
			if( $v['istop'] == 1 ){
				$topstr = '<span class="top_task">[顶]</span>';
			}

			$str .= <<<div
				<a href="{$url}">
					<div class="index_task_item">
						<div class="index_task_item_in item_cell_box item_cell_start">
							<div class="index_item_left">
								<img src="{$user['headimgurl']}">
							</div>
							<div class="index_item_right item_cell_flex">
								<div class="index_item_top oh">
									<li class="index_item_nick fl">{$user['nickname']}</li>
									<li class="index_item_read fr">{$v['scan']}</li>
								</div>
								<div class="index_item_title">{$topstr}{$v['title']}</div>
								<div class="index_item_bot oh">
									<li class="index_item_money fl">
										{$v['money']}
									</li>
									{$statusstr}
								</div>
							</div>
						</div>
					</div>
				</a>
div;

		}


	}elseif( in_array($_GPC['op'], array('mypub0','mypub1','mypub2','mypub3','mypub4','mypub5') ) ){

		$where['uniacid'] = $_W['uniacid'];
		$where['userid'] = $userinfo['uid'];
		$orstr = '';
		if( $_GPC['op'] == 'mypub1' ){ // 未开始
			$where['iscount'] = 0;
			$orstr = ' AND ( `status` = 1 OR `isstart` = 1 ) ';
		}elseif( $_GPC['op'] == 'mypub2' ){ // 进行中
			$where['status'] = 0;
			$where['iscount'] = 0;
			$where['start<'] = TIMESTAMP;
		}elseif( $_GPC['op'] == 'mypub3' ){ // 已结算
			$where['iscount'] = 1;
		}elseif( $_GPC['op'] == 'mypub0' ){ // 全部
			
		}

		if( $_GPC['pid'] == 1 ) $where['type'] = 0;
		if( $_GPC['pid'] == 2 ) $where['type'] = 1;

		if( !empty($_GPC['type']) ) $where['sortid'] = $_GPC['type'];

		if( !empty($_GPC['search']) ) {
			if( strpos($_GPC['search'], 'http') !== false ){
				$where['link@'] = $_GPC['search'];
			}else{
				$where['title@'] = $_GPC['search'];
			}
		}

		//$orstr .= ' GROUP BY a.`id` ';
		//$select = ' a.id,a.title,a.money,a.scan,a.isempty,a.status,a.iscount,a.start,a.type,b.headimgurl,b.nickname,count(distinct a.id) ';
		//$data = model_task::getAllTask($where,$page,10,' a.`id` DESC ',false,false,$select,$orstr);

		$select = ' id,title,money,scan,isempty,status,iscount,start,type,address,idcode ';
		$data = Util::getAllDataInSingleTable('zofui_tasktb_task',$where,$page,10,' `id` DESC ',false,false,$select,$orstr);

		foreach((array)$data[0] as $k=>$v){

			$tid = empty($_W['set']['tidtype']) || empty($v['idcode']) ? $v['id'] : $v['idcode'];
			if( empty($_W['set']['tidtype']) || empty($v['idcode']) ){
				$url = $this->createMobileUrl('task',array('id'=>$v['id']));
			}else{
				$url = $this->createMobileUrl('task',array('idcode'=>$v['idcode']));
			}

			if( $v['iscount'] == 1 ){
				$statusstr = '<li class="index_item_status fr status_no">已结算</li>';
				if( $v['status'] == 2 ){
					$statusstr = '<li class="index_item_status fr font_ff5f27">未通过审核</li>';
				}
				
			}else{
				if( $v['status'] == 0 ){
					if( $v['isempty'] == 1 ){
						$statusstr = '<li class="index_item_status fr font_ff5f27">已被接完</li>';
					}else{
						$statusstr = '<li class="index_item_status fr status_ing">任务进行中</li>';
					}

				}elseif( $v['status'] == 1 ){
					$statusstr = '<li class="index_item_status fr font_ff5f27">审核中</li>';

				}elseif( $v['status'] == 2 ){
					$statusstr = '<li class="index_item_status fr font_ff5f27">未通过审核</li>';
				}
				if( $v['start'] > TIMESTAMP ){
					$statusstr = '<li class="index_item_status fr status_no">还未开始</li>';
				}
			}
			
			$typestr = '<span class="font_mini">[普通] </span>';
			if( $v['type'] == 1 ) $typestr = '<span class="font_mini font_green">[试用] </span>';

			$address = '';
			if( !empty($v['address']) ){
				$address = '<div class="list_address">'.$v['address'].'</div>';
			}

			$userinfo['headimgurl'] = tomedia( $userinfo['headimgurl'] );

			$nickname = $userinfo['nickname'];
			$isauth = model_user::isAuth( $userinfo,$_W['set'] );
			if( $isauth == 2 ){
				$nickname = '<span class="authname" >'.$userinfo['nickname'].'<font class="authicon"></font></span>';
			}

			$str .= <<<div
				<a href="{$url}">
					<div class="index_task_item">
						<div class="index_task_item_in item_cell_box item_cell_start">
							<div class="index_item_left">
								<img src="{$userinfo['headimgurl']}">
							</div>
							<div class="index_item_right item_cell_flex">
								<div class="index_item_top oh">
									<li class="index_item_nick fl">{$nickname}</li>
									<li class="index_item_read fr">{$v['scan']}</li>
								</div>
								<div class="index_item_title">{$typestr}{$v['title']}{$address}</div>
								<div class="index_item_bot oh">
									<li class="index_item_money fl">
										{$v['money']}
									</li>
									{$statusstr}
								</div>
							</div>
						</div>
					</div>
				</a>
div;

		}
		


	}elseif( in_array($_GPC['op'], array('myreply1','myreply2','myreply3','myreply4') ) ){

		$where['uniacid'] = $_W['uniacid'];
		$where['userid'] = $userinfo['uid'];

		if( $_GPC['op'] == 'myreply1' ){ // 待回复
			$where['status'] = 0;
			$where['endtime>'] = TIMESTAMP;
		}elseif( $_GPC['op'] == 'myreply2' ){ // 待采纳
			$where['status'] = 1;	
		}elseif( $_GPC['op'] == 'myreply3' ){ // 已采纳
			$where['status'] = 2;
		}elseif( $_GPC['op'] == 'myreply4' ){ // 已拒绝
			$where['status'] = 3;
		}
		
		$select = ' b.id,b.title,a.content,a.createtime,a.money,a.ewai,a.status,b.idcode ';
		$data = model_task::getAllReply($where,$page,10,' a.`id` DESC ',false,false,$select);
		
		foreach((array)$data[0] as $k=>$v){
			$time = Util::formatTime( $v['createtime'] );
			
			$tid = empty($_W['set']['tidtype']) || empty($v['idcode']) ? $v['id'] : $v['idcode'];
			if( empty($_W['set']['tidtype']) || empty($v['idcode']) ){
				$url = $this->createMobileUrl('task',array('id'=>$v['id']));
			}else{
				$url = $this->createMobileUrl('task',array('idcode'=>$v['idcode'],'type'=>1));
			}

			$moneystr = '';
			
			if( $v['status'] == 0 ){
				$statusstr = '<span class="status_no">待回复</span> ';
			}elseif( $v['status'] == 1 ){
				$statusstr = '<span class="status_no">待采纳</span> ';
			}elseif( $v['status'] == 2 ){
				$statusstr = '<span class="status_no">已采纳</span> ';
				$ewai = '';
				if( $v['ewai'] > 0 ){
					$ewai = ' + '.$v['ewai'];
				}
				$moneystr = '<span class="font_ff5f27 money_icon">'.$v['money'].$ewai.'</span> ';
			}elseif( $v['status'] == 3 ){
				$statusstr = '<span class="font_ff5f27">被拒绝</span> ';
			}
			
			$str .= <<<div
				<a href="{$url}">
					<div class="myreply_item">
						<div class="myreply_item_in">
							<div class="myreply_item_title">
								{$v['title']}
							</div>
							<div class="myreply_item_body font_mini">
								{$v['content']}
							</div>
							<div class="myreply_item_bot item_cell_box">
								<li class="item_cell_flex font_13px_999">
									{$statusstr}
									{$moneystr}
								</li>
								<li class="font_13px_999">{$time}</li>
							</div>						
						</div>
					</div>
				</a>
div;

		}
		

	// 试用任务回复列表
	}elseif( $_GPC['op'] == 'getusetaskreply' ){

		$where['uniacid'] = $_W['uniacid'];
		$where['taskid'] = $_GPC['taskid'];
		$where['isactivity'] = 0;
		//if( !in_array($_GPC['type'], array(1,2,3)) ) $where['status>'] = 0.1;

		if( $_GPC['status'] == 1 ) $where['status'] = 0;
		if( $_GPC['status'] == 2 ) $where['status'] = 1;
		if( $_GPC['status'] == 3 ) $where['status'] = 4; // 已提交订单
		if( $_GPC['status'] == 4 ) $where['status'] = 5; // 已完成
		if( $_GPC['status'] == 5 ) $where['status'] = 6; // 已失败		

		$select = ' * ';
		//if( !empty( $_GPC['type'] ) ) $where['sortid'] = intval( $_GPC['type'] );

		$order = ' `id` DESC ';
		$data = Util::getAllDataInSingleTable('zofui_tasktb_usetasklog',$where,$page,10,$order,false,false,$select,'','utask');

		$task = pdo_get('zofui_tasktb_task',array('uniacid'=>$_W['uniacid'],'id'=>$where['taskid']),array('userid','iscount'));

		$isadmin = model_user::isAdmin( $userinfo['uid'] );

		if( !$isadmin && $task['userid'] != $userinfo['uid'] ) die;

		foreach((array)$data[0] as $k=>$v){
			$time = Util::formatTime( $v['createtime'] );
			$user = model_user::getSingleUser( $v['userid'] );
			$user['headimgurl'] = tomedia( $user['headimgurl'] );
			if( empty($user['headimgurl']) ) $user['headimgurl'] = '../addons/zofui_taskself/public/images/dhead.png';
			
			$nickname = $user['nickname'].'<font class="userid">('.$user['id'].')</font>';
			$isauth = model_user::isAuth( $user,$_W['set'] );
			if( $isauth == 2 ){
				$nickname = '<span class="authname" >'.$user['nickname'].'<font class="authicon"></font></span>  <font class="userid useridpl02 pl05">('.$user['id'].')</font>';
			}

			$status = '';
			if( $v['status'] == 1 ) $status = '<span class="task_replay_status">等待提交订单</span>';
			if( $v['status'] == 2 ) $status = '<span class="task_replay_status ">已拒绝</span>';
			if( $v['status'] == 3 ) $status = '<span class="task_replay_status ">已取消</span>';
			if( $v['status'] == 5 ) $status = '<span class="task_replay_status ">已完成</span>';
			if( $v['status'] == 6 ) $status = '<span class="task_replay_status font_ff5f27">任务失败</span>';

			$botstr = '<div class="task_replay_bottom" >'.$status.'</div>';

			if( $v['status'] == 0 && $userinfo['uid'] == $task['userid'] && $task['iscount'] == 0 ){ // 是发布者
				
				$botstr = <<<div
					<div class="task_replay_bottom item_cell_box mt05" >
						<div class="item_cell_flex font_13px_999 font_mini"></div>
						<div class="puber_deal_btn passor" reid="{$v['id']}" type="1">通过</div>
						<div class="puber_deal_btn passor" reid="{$v['id']}" type="2">拒绝</div>
					</div>
div;
			}

			if( $v['status'] == 4 && $userinfo['uid'] == $task['userid'] && $task['iscount'] == 0 ){ // 是发布者
				
				$botstr = <<<div
					<div class="task_replay_bottom item_cell_box mt05" >
						<div class="item_cell_flex font_13px_999 font_mini"></div>
						<div class="puber_deal_btn notice" reid="{$v['id']}" >提醒</div>
						<div class="puber_deal_btn success" reid="{$v['id']}" type="1">完成</div>
						<div class="puber_deal_btn fail" reid="{$v['id']}" >失败</div>
					</div>
div;
			}

			if( $v['status'] == 6 && $userinfo['uid'] == $task['userid'] && $task['iscount'] == 0 ){ // 是发布者
				
				$botstr = <<<div
					<div class="task_replay_bottom item_cell_box " >
						<div class="item_cell_flex font_13px_999 font_mini"></div>
						<div class="puber_deal_btn tosuccess" reid="{$v['id']}" type="2">转为完成</div>
					</div>
div;
			}
						

			
			$applycontent = '';
			$applyimg = '';
			$v['initcontent'] = iunserializer( $v['initcontent'] );
			if( !empty( $v['initcontent']['content'] ) )  $applycontent = '<div>'.$v['initcontent']['content'].'</div>';
			if( !empty( $v['initcontent']['images'] ) ){
				 $applyimg .= '<div class="need_show_images oh">';
				foreach ( $v['initcontent']['images'] as $vv ) {
					$applyimg .= '<li class="need_show_images_item need_show_images_mini fl"><img src="'. tomedia( $vv ) .'"></li>';
				}
				$applyimg .= '</div>';
			}
			$logstr = '<div class="task_log_item item_cell_box"><div class="font_mini usetask_time">'. date('m-d H:i',$v['createtime']) .'</div><div class="item_cell_flex"><div>申请任务</div>'.$applycontent.$applyimg.'</div></div>';

			if( $v['passortime'] >0 ){
				$logstr .= '<div class="task_log_item item_cell_box"><li class="font_mini usetask_time">'. date('m-d H:i',$v['passortime']) .'</li><li class="item_cell_flex">审核通过</li></div>';
			}
			if( $v['subtime'] >0 ){
				$v['subcontent'] = iunserializer( $v['subcontent'] );
				$subimg = '';
				if( !empty( $v['subcontent']['img'] ) ){
					 $subimg .= '<div class="need_show_images oh">';
					foreach ( $v['subcontent']['img'] as $vv ) {
						$subimg .= '<li class="need_show_images_item need_show_images_mini fl"><img src="'. tomedia( $vv ) .'"></li>';
					}
					$subimg .= '</div>';
				}
				$subtime = date('m-d H:i',$v['subtime']);
				$logstr .= '<div class="task_log_item item_cell_box"><div class="font_mini usetask_time">'. $subtime .'</div><div class="item_cell_flex"> <span class="font_13px_999 font_mini">提交订单内容:</span>'.$v['subcontent']['content'].$subimg.'</div></div>';


				// 补充内容
				$addcontent = pdo_getall('zofui_tasktb_useaddcontent',array('takedid'=>$v['id']));
				if( !empty( $addcontent ) ){
					foreach ($addcontent as &$addimg) {
						$addimg['img'] = iunserializer( $addimg['img'] );
					}
					unset( $addimg );

					foreach ($addcontent as $additem) {
						$additemimg = '';
						if( !empty( $additem['img'] ) ){
							 $additemimg .= '<div class="need_show_images oh">';
							foreach ( (array)$additem['img'] as $vv ) {
								$additemimg .= '<li class="need_show_images_item need_show_images_mini fl"><img src="'. tomedia( $vv ) .'"></li>';
							}
							$additemimg .= '</div>';
						}
						$addtime = date('m-d H:i',$additem['createtime']);

						$strtype = '补充内容';
						if( $additem['type'] == 1 ) $strtype = '提醒雇员';

						$logstr .= '<div class="task_log_item item_cell_box"><div class="font_mini usetask_time">'. $addtime .'</div><div class="item_cell_flex"> <span class="font_13px_999 font_mini">'.$strtype.':</span>'.$additem['content'].$additemimg.'</div></div>';
					}

				}

			}

			if( $v['failtime'] >0 ){
				$logstr .= '<div class="task_log_item item_cell_box"><li class="font_mini usetask_time">'. date('m-d H:i',$v['failtime']) .'</li><li class="item_cell_flex">设置任务失败，原因：'.$v['reason'].'</li></div>';
			}		
			if( $v['suctime'] >0 ){
				$logstr .= '<div class="task_log_item item_cell_box"><li class="font_mini usetask_time">'. date('m-d H:i',$v['suctime']) .'</li><li class="item_cell_flex">任务完成,发放佣金 <span class="font_ff5f27">'.$v['prizemoney'].' </span>'.$_W['cname'].'</li></div>';
			}

			// 投诉
			if( $v['iscomplained'] == 1 ){
				$thiscomplain = pdo_get('zofui_tasktb_complain',array('uniacid'=>$_W['uniacid'],'userid'=>$v['userid'],'taskid'=>$v['taskid']));
				
				if( !empty( $thiscomplain['images'] ) ){
					$thiscomplain['images'] = iunserializer( $thiscomplain['images'] );
					$compimg = '';
					if( !empty( $thiscomplain['images'] ) ){
						 $compimg .= '<div class="need_show_images oh">';
						foreach ( $thiscomplain['images'] as $vv ) {
							$compimg .= '<li class="need_show_images_item need_show_images_mini fl"><img src="'. tomedia( $vv ) .'"></li>';
						}
						$compimg .= '</div>';
					}
					$comptime = date('m-d H:i',$thiscomplain['time']);
					$logstr .= '<div class="task_log_item item_cell_box"><div class="font_mini usetask_time">'. $comptime .'</div><div class="item_cell_flex"> <span class="font_13px_999 font_mini">雇员投诉:</span>'.$thiscomplain['content'].$compimg.'</div></div>';

				}
			}			
			if( $v['tosuctime'] >0 ){
				$logstr .= '<div class="task_log_item item_cell_box"><li class="font_mini usetask_time">'. date('m-d H:i',$v['tosuctime']) .'</li><li class="item_cell_flex">将任务转为完成,发放佣金 <span class="font_ff5f27">'.$v['prizemoney'].' </span>'.$_W['cname'].'</li></div>';
			}	

			$str .= <<<div
				<div class="task_reply_item">
					<div class="task_reply_in">
						<div class="item_cell_box">
							<div class="task_reply_headimg">
								<img src="{$user['headimgurl']}" >
							</div>
							<div class="item_cell_flex task_content_body">
								<div class="oh">
									<span class="font_bold_name task_content_nick">{$nickname}</span> 
									<span class="font_13px_999 fr">{$time}</span>
								</div>
								<div class="task_log_list">
									{$logstr}
								</div>
							</div>
						</div>
						{$botstr}
					</div>
				</div>
div;


		}

	// 试用任务
	}else if( $_GPC['op'] == 'getusetask' ){

		$where['iscount'] = 0;
		$where['status'] = 0;
		$where['type'] = 1;

		$order = ' `isstart` ASC,`isempty` ASC,`istop` DESC,`start` ASC,`id` DESC ';

		$select = ' id,title,money,scan,isempty,istop,start,pic ';
		$data = Util::getAllDataInSingleTable('zofui_tasktb_task',$where,$page,10,$order,10,false,$select,'','idx');
		
		foreach((array)$data[0] as $k=>$v){
			$url = $this->createMobileUrl('task',array('id'=>$v['id']));
			
			$v['title'] = model_task::hideKey( $_W['set']['hidetxt'],$v['title'] );

			$empty = '';
			if( $v['isempty'] == 1 ) $empty = '<li class="usegood_status_empty"><span>已接完</span></li>';

			$top = '';
			if( $v['istop'] == 1 ) $top = '<span class="font_ff5f27">[顶] </span>';

			$img = tomedia( $v['pic'] );

			$str .= <<<div
				<a href="{$url}">
					<div class="good_item">
						<div class="good_item_in">
							<div class="good_img">
								<img src="{$img}">
								{$empty}
							</div>
							<div class="good_title">{$top}{$v['title']}</div>
							<div class="good_bot item_cell_box">
								<li class="item_cell_flex price_icon">{$v['money']}</li>
								<li class="good_cart">{$v['scan']}</li>
							</div>
						</div>
					</div>
				</a>
div;

		}


	// 我的试用任务
	}else if( $_GPC['op'] == 'myuselist' ){

		$where['uniacid'] = $_W['uniacid'];
		$where['userid'] = $userinfo['uid'];
		
		if( $_GPC['type'] == 1 ) $where['status'] = 0;
		if( $_GPC['type'] == 2 ) $where['status'] = 1;
		if( $_GPC['type'] == 3 ) $where['status'] = 4;
		if( $_GPC['type'] == 4 ) $where['status'] = 5;
		if( $_GPC['type'] == 5 ) $where['status'] = 6;

		$order = ' b.`id` DESC ';
			
		$select = ' a.id,a.title,a.money,a.isempty,a.scan,a.pic,b.* ';
		$data = model_task::getAllMyUsetask($where,$page,10,$order,false,false,$select);

		foreach((array)$data[0] as $k=>$v){
			$url = $this->createMobileUrl('task',array('id'=>$v['taskid']));
			
			$empty = '';
			if( $v['isempty'] == 1 ) $empty = '<li class="usegood_status_empty"><span>已接完</span></li>';

			$top = '';
			if( $v['istop'] == 1 ) $top = '<span class="font_ff5f27">[顶] </span>';

			$str .= <<<div
				<a href="{$url}">
					<div class="good_item">
						<div class="good_item_in">
							<div class="good_img">
								<img src="{$v['pic']}">
								{$empty}
							</div>
							<div class="good_title">{$top}{$v['title']}</div>
							<div class="good_bot item_cell_box">
								<li class="item_cell_flex price_icon">{$v['money']}</li>
								<li class="good_cart">{$v['scan']}</li>
							</div>
						</div>
					</div>
				</a>
div;

		}


	////////////// 担保任务
	}elseif( in_array($_GPC['op'], array('tbtasksimple') ) ){

		$where['uniacid'] = $_W['uniacid'];
		$where['taskid'] = $_GPC['pid'];
		
		$select = ' openid,status,money,userid ';
		$order = ' `id` DESC ';
		$data = Util::getAllDataInSingleTable('zofui_tasktb_tbtaked',$where,$page,10,$order,30,false,$select,$andstr,'tbtaked');
		
		foreach((array)$data[0] as $k=>$v){
			$user = model_user::getSingleUser( $v['userid'] );
			$user['headimgurl'] = tomedia( $user['headimgurl'] );
			if( empty($user['headimgurl']) ) $user['headimgurl'] = '../addons/zofui_taskself/public/images/dhead.png';

			$nickname = $user['nickname'];
			$isauth = model_user::isAuth( $user,$_W['set'] );
			if( $isauth == 2 ){
				$nickname = '<span class="authname" >'.$user['nickname'].'<font class="authicon"></font></span>';
			}

			$money = $v['money']*100/100;
			
			$rightstr = '<div class="joined_notice">接到任务</div>';
			if( $v['status'] == 1 ) {
				$rightstr = '<div class="joined_notice">未通过审核</div>';
			}elseif( $v['status'] == 2 ) {
				$rightstr = '<div class="joined_notice">任务进行中</div>';
			}elseif( $v['status'] == 3 || $v['status'] == 9 ) {
				$rightstr = '<div class="joined_notice">任务完成 <font class="font_ff5f27">+'.$money.'</font></div>';
			}elseif( $v['status'] == 4 ) {
				$rightstr = '<div class="joined_notice font_ff5f27">雇主认定失败</div>';
			}elseif( $v['status'] == 5 || $v['status'] == 8 ) {
				$rightstr = '<div class="joined_notice font_ff5f27">任务失败</div>';
			}elseif( $v['status'] == 6 || $v['status'] == 7 ) {
				$rightstr = '<div class="joined_notice">申诉中</div>';
			}


			$str .= <<<div
				<div class="joined_item item_cell_box">
					<div class="joined_headimg">
						<img src="{$user['headimgurl']}">
					</div>
					<div class="joined_nick item_cell_flex">
						{$nickname}
					</div>
					{$rightstr}
				</div>
div;

		}


	// 担保任务
	}elseif( in_array( $_GPC['op'] , array('mypubtb1','mypubtb2','mypubtb3','tblisttbtask'))  ){

		$andstr = '';
		if( $_GPC['op'] == 'tblisttbtask' ){
			$where['iscount'] = 0;
			$where['status'] = 0;
			$where['end>'] = TIMESTAMP;
			
			$order = ' `isstart` ASC,`isempty` ASC,`istop` DESC,`topendtime` DESC,`start` ASC ';

		}elseif( $_GPC['op'] == 'mypubtb1' ){

			$where['userid'] = $userinfo['uid'];
			$where['iscount'] = 0;
			$andstr = ' AND ( `status` IN(1,2) OR `isstart` = 1 ) ';
			$order = ' `id` DESC ';
		}elseif( $_GPC['op'] == 'mypubtb2' ){

			$order = ' `id` DESC ';
			$where = array('status'=>0,'isstart'=>0,'iscount'=>0,'uniacid'=>$_W['uniacid'],'userid'=>$userinfo['uid']);
		
		}elseif( $_GPC['op'] == 'mypubtb3' ){

			$order = ' `id` DESC ';
			$where = array('iscount'=>1,'userid'=>$userinfo['uid']);
		}

		//$where['end>'] = TIMESTAMP;

		$select = ' id,title,money,scan,isempty,topendtime,start,puber,num,limitnum,tbmoney,iscount,istop,end,userid,address ';

		
		$data = Util::getAllDataInSingleTable('zofui_tasktb_tbtask',$where,$page,8,$order,10,false,$select,$andstr,'tblx');
		

		$cutword = iunserializer( $_W['set']['cutword'] );

		foreach((array)$data[0] as $k=>$v){
			$url = $this->createMobileUrl('tbtask',array('id'=>$v['id']));

			$v['title'] = model_task::hideKey( $_W['set']['hidetxt'],$v['title'] );

			$user = array();
			if( !empty( $v['userid'] ) ){
				$user = model_user::getSingleUser( $v['userid'] );
				$user['headimgurl'] = tomedia( $user['headimgurl'] );
				if( empty($user['headimgurl']) ) $user['headimgurl'] = '../addons/zofui_taskself/public/images/dhead.png';

			}else{
				$user['headimgurl'] = tomedia( $_W['set']['logo'] );
				if( empty($user['headimgurl']) ) $user['headimgurl'] = '../addons/zofui_taskself/public/images/dhead.png';
				$user['nickname'] = $_W['set']['sitename'];
			}
			$nickname = $user['nickname'];
			$isauth = model_user::isAuth( $user,$_W['set'] );
			if( $isauth == 2 ){
				$nickname = '<span class="authname" >'.$user['nickname'].'<font class="authicon"></font></span>';
			}

			if( $v['isempty'] == 0 ){
				$statusstr = '<li class="index_item_status fr status_ing">任务进行中</li>';
			}else{
				$statusstr = '<li class="index_item_status fr status_no">已接完</li>';
			}
			
			/*if( !empty( $thisstatus['status'] ) ){
				$statusstr = '<li class="index_item_status fr status_no">您不能接此任务</li>';
				if( $thisstatus['status'] == 1 ){
					$statusstr = '<li class="index_item_status fr status_no">您已接了此任务</li>';
				}
			}*/

			$address = '';
			if( !empty($v['address']) ){
				$ising = pdo_get('zofui_tasktb_tbtaked',array('status'=>2,'taskid'=>$v['id']),array('id'));
				if( !in_array($ising['status'], array(3,5,8,9)) || $userinfo['uid'] == $v['userid'] ){
					$address = '<div class="list_address">地址：'.$v['address'].'</div>';
				}
			}

			if( $v['start'] > TIMESTAMP ){
				$statusstr = '<li class="index_item_status fr status_no">还未开始</li>';
			}
			if( $v['isempty'] == 1 ){
				$statusstr = '<li class="index_item_status fr status_no">已接完</li>';
			}
			if( $v['end'] <= TIMESTAMP ){
				$statusstr = '<li class="index_item_status fr status_no">待结算</li>';
			}
			if( $v['iscount'] == 1 ){
				$statusstr = '<li class="index_item_status fr status_no">已结束</li>';
			}


			$topstr = '';
			if( $v['topendtime'] > TIMESTAMP || ( $v['istop'] == 1 && $v['createtime'] < 1506390630 ) ){
				$topstr = '<span class="top_task">[顶]</span>';
			}

			if( is_array( $cutword ) ){
				$v['title'] = str_replace($cutword,'*', $v['title']);
			}
			$money = $v['money']*100/100;
			$tbmoney = $v['tbmoney']*100/100;

			$str .= <<<div
				<a href="{$url}">
					<div class="index_task_item">
						<div class="index_task_item_in item_cell_box item_cell_start">
							<div class="index_item_left">
								<img src="{$user['headimgurl']}">
							</div>
							<div class="index_item_right item_cell_flex">
								<div class="index_item_top oh">
									<li class="index_item_nick fl">{$nickname}</li>
									<li class="index_item_read fr">{$v['scan']}</li>
								</div>
								<div class="index_item_title">{$topstr}{$v['title']}{$address}</div>
								<div class="index_item_bot oh">
									<li class="fl find_moneyinfo">
										<p class="font_mid">
											赏金:<span class="font_ff5f27">{$money}</span> 
											担保金:<span class="font_ff5f27">{$tbmoney}</span>
										</p>
									</li>
									{$statusstr}
								</div>
							</div>
						</div>
					</div>
				</a>
div;

		}

	// 担保任务列表	
	}elseif( $_GPC['op'] == 'tbtaskall' ) {

		$taskid = intval( $_GPC['pid'] );
		$task = model_tbtask::getTask( $taskid );

		$isadmin = model_user::isAdmin( $userinfo['uid'] );
		if( $task['userid'] != $userinfo['uid'] && !$isadmin ) die;

		$where['uniacid'] = $_W['uniacid'];
		$where['taskid'] = $taskid;
		$andstr = '';

		if( $_GPC['type'] == 1 ) $where['status'] = 0;
		if( $_GPC['type'] == 2 ) $where['status'] = 2;
		if( $_GPC['type'] == 3 ) {
			$andstr = ' AND `status` IN(5,8) ';
		}
		if( $_GPC['type'] == 4 ) {
			$andstr = ' AND `status` IN(3,9) ';
		}
		if( $_GPC['type'] == 5 ) {
			$andstr = ' AND `status` IN(6,7) ';
		}
		
		$select = ' * ';
		$order = ' `id` DESC ';
		$data = Util::getAllDataInSingleTable('zofui_tasktb_tbtaked',$where,$page,10,$order,5,false,$select,$andstr,'tbkd');

		foreach((array)$data[0] as $k=>$v){
			$time = Util::formatTime( $v['createtime'] );
			$user = model_user::getSingleUser( $v['userid'] );
			$user['headimgurl'] = tomedia( $user['headimgurl'] );
			if( empty($user['headimgurl']) ) $user['headimgurl'] = '../addons/zofui_taskself/public/images/dhead.png';

			$nickname = $user['nickname'].'<font class="userid">('.$user['id'].')</font>';
			$isauth = model_user::isAuth( $user,$_W['set'] );
			if( $isauth == 2 ){
				$nickname = '<span class="authname" >'.$user['nickname'].'<font class="authicon"></font></span>  <font class="userid useridpl02 pl05">('.$user['id'].')</font>';
			}

			$contentarr = model_tbtask::structTakedStep( $v );
			$contentstr = '';

			if( !empty( $contentarr ) ) {

				foreach ($contentarr as $vv) {

					$thiscontent = empty( $vv['content']['content'] ) ? '' : '<div class="tbtask_content_incontent">'.$vv['content']['content'].'</div>';

					$thisimg = '';
					if( !empty( $vv['content']['images'] ) ) 
						$vv['content']['images'] = iunserializer( $vv['content']['images'] );
					if( !empty( $vv['content']['images'] ) && is_array( $vv['content']['images'] ) ){
						foreach ($vv['content']['images'] as $vvv) {
							$thisimg .= '<li class="need_show_images_item fl"><img src="'.tomedia( $vvv ).'"></li>';
						}
					}
					
					$isbossclass = '';
					if( $vv['isboss'] ) $isbossclass = 'tbtask_log_content_boss';

					$contentstr .= <<<div
						<div class="tbtask_content_item {$isbossclass} ">
							<div class="tbtask_content_time font_mini">{$vv['time']} {$vv['str']}</div>
							<div class="tbtask_content_in">
								{$thiscontent}
								<div class="task_reply_images oh">
									{$thisimg}
								</div>
							</div>
						</div>
div;
				}
			}
			

			$bottimestr = '';
			$status = '';
			if( $v['status'] == 0 ){ // 是发布者

				if( $userinfo['uid'] == $v['pubuid'] ) {
					$status = <<<div
						<div class="item_cell_flex"></div>
						<div class="puber_deal_btn pass" reid="{$v['id']}">通过</div>
						<div class="puber_deal_btn nopass" reid="{$v['id']}">不通过</div>
						<div class="puber_deal_check weui_cells_checkbox">
							<label class="weui_cell weui_check_label needsclick " >
								<div class="weui_cell_hd needsclick">
									<input type="checkbox" class="weui_check" name="reply[]" value="{$v['id']}" >
									<i class="weui_icon_checked"></i>
								</div>
								<div class="weui_cell_bd tl weui_cell_primary needsclick">
									<span class="form_tips needsclick">选择</span>
								</div>
							</label>
						</div>
div;
				}
			}

			if( $v['status'] == 1 ) $status = '<span class="task_replay_status">未通过审核</span>';
			if( $v['status'] == 2 ) {
				if( $userinfo['uid'] == $v['pubuid'] )
					$status = '<div class="puber_deal_btn remind" reid="'.$v['id'].'" type="1">提醒</div>';

				if( $v['step'] == 6 ) {
					$tbautotime = $_W['set']['tbautotime2'] > 0 ? $_W['set']['tbautotime2'] : 24;
					$autotime = $v['subcomtime'] + $tbautotime*3600;
					$bottimestr = <<<div
						<div class="tr">
							<span class="font_mini lasttime" data-time="{$autotime}">距自动完成:
								<font class="day font_ff5f27">0</font>天
								<font class="hour font_ff5f27">0</font>时
								<font class="minite font_ff5f27">0</font>分
								<font class="second font_ff5f27">0</font>秒
							</span>
						</div>
div;


				if( $userinfo['uid'] == $v['pubuid'] )
					$status = <<<div
						<div class="item_cell_flex"></div>
						<div class="puber_deal_btn comtbtask" reid="{$v['id']}">完成</div>
						<div class="puber_deal_btn failtbtask" reid="{$v['id']}">失败</div>
						<div class="puber_deal_btn remind" reid="{$v['id']}" type="1">提醒</div>
div;
				}


				if( $v['islimitstep'] == 1 ) {
					$tbautotime = $_W['set']['step1time'] > 0 ? $_W['set']['step1time'] : 24;
					$autotime = $v['passtime'] + $tbautotime*3600;
					$bottimestr = <<<div
						<div class="tr">
							<span class="font_mini lasttime" data-time="{$autotime}">距自动失败:
								<font class="day font_ff5f27">0</font>天
								<font class="hour font_ff5f27">0</font>时
								<font class="minite font_ff5f27">0</font>分
								<font class="second font_ff5f27">0</font>秒
							</span>
						</div>
div;
				}

				if( $v['islimitstep'] == 0 && $v['step'] != 6 ) {
					$tbautotime = $_W['set']['tbautotime1'] > 0 ? $_W['set']['tbautotime1'] : 168;
					$autotime = $v['passtime'] + $tbautotime*3600;
					$bottimestr = <<<div
						<div class="tr">
							<span class="font_mini lasttime" data-time="{$autotime}">距自动失败:
								<font class="day font_ff5f27">0</font>天
								<font class="hour font_ff5f27">0</font>时
								<font class="minite font_ff5f27">0</font>分
								<font class="second font_ff5f27">0</font>秒
							</span>
						</div>
div;
				}


			}
			if( $v['status'] == 3 ) $status = '<span class="task_replay_status font_ff5f27">已完成</span>';
			if( $v['status'] == 4 ) {
				$tbautotime = $_W['set']['tbautotime4'] > 0 ? $_W['set']['tbautotime4'] : 24;
				$autotime = $v['setfailtime'] + $tbautotime*3600;
				$status = <<<div
					<span class="font_mini lasttime" data-time="{$autotime}">距自动失败:
						<font class="day font_ff5f27">0</font>天
						<font class="hour font_ff5f27">0</font>时
						<font class="minite font_ff5f27">0</font>分
						<font class="second font_ff5f27">0</font>秒
					</span>
div;
			}
			if( $v['status'] == 5 ) $status = '<span class="task_replay_status font_ff5f27">任务失败</span>';
			if( $v['status'] == 6 || $v['status'] == 7 ) {

				$timedesc = '';
				if( $v['status'] == 7 ){
					$tbautotime = $_W['set']['tbautotime7'] > 0 ? $_W['set']['tbautotime7'] : 24;
					$autotime = $v['adminsettime'] + $tbautotime*3600;

					if( $v['complainstep'] == 1 ) $text = '距发赏金';
					if( $v['complainstep'] == 2 ) $text = '距退赏金';

					$timedesc = <<<div
						<span class="font_mini lasttime" data-time="{$autotime}">{$text}:
							<font class="day font_ff5f27">0</font>天
							<font class="hour font_ff5f27">0</font>时
							<font class="minite font_ff5f27">0</font>分
							<font class="second font_ff5f27">0</font>秒
						</span>
div;
				}

			if( $userinfo['uid'] == $v['pubuid'] )
				$status = <<<div
					<div class="item_cell_flex"></div>
					<div class="puber_deal_btn sub_cert" type="2" reid="{$v['id']}">{$timedesc}上传凭证</div>
div;
			}
			if( $v['status'] == 8 ) $status = '<span class="task_replay_status">已失败</span>';
			if( $v['status'] == 9 ) $status = '<span class="task_replay_status font_ff5f27">已完成</span>';

			$botstr = $bottimestr.'<div class="task_replay_bottom item_cell_box" ><div class="item_cell_flex"></div>'.$moneystr.$status.'</div>';

			//$guyurl = $this->createMobileUrl('guy',array('id'=>$user['id']));
			$guyurl = 'javascript:;';

			$endcalss = $endstr = '';
			if( in_array( $v['status'] , array(3,5,8,9)) ){
				$endcalss = 'end_hide';
				$endstr = 	<<<div
					<div class="end_show_box">
						<span class="end_showmore icon icon-down"></span>
					</div>
					<div class="end_hide_box end_show_bot">
						<span class="end_showmore icon icon-up"></span>
					</div>
div;
			}

			$str .= <<<div
				<div class="task_reply_item">
					<div class="task_reply_in">
						<div class="item_cell_box">
							<div class="task_reply_headimg">
								<a href="{$guyurl}">
									<img src="{$user['headimgurl']}" >
								</a>
							</div>
							<div class="item_cell_flex task_content_body">
								<div class="oh">
									<a href="{$guyurl}">
										<span class="font_bold_name task_content_nick">
											{$nickname}
										</span>
									</a>
									<span class="font_13px_999 fr">{$time}</span>
								</div>
								<div class="tbtask_content_list {$endcalss}">
									{$endstr}
									<div class="end_hide_content">
										{$contentstr}
									</div>
								</div>
							</div>
						</div>
						{$botstr}
					</div>
				</div>

div;
		
		
		}


	// 接的担保任务
	}elseif( $_GPC['op']  == 'mytbtaskop' ){


		$andstr = '';
		$where['userid'] = $userinfo['uid'];
		if( $_GPC['type'] == 0 ) $where['status'] = 0;
		if( $_GPC['type'] == 1 ) $where['status'] = 1;
		if( $_GPC['type'] == 2 ) $where['status'] = 2;
		if( $_GPC['type'] == 3 ) {
			$andstr = ' AND `status` IN(3,9) ';
		}
		if( $_GPC['type'] == 4 ) $where['status'] = 4;
		if( $_GPC['type'] == 5 ) {
			$andstr = ' AND `status` IN(5,8) ';
		}
		if( $_GPC['type'] == 6 ) {
			$andstr = ' AND `status` IN(6,7) ';
		}

		$select = ' id,status,taskid,puber,userid ';
		$order = ' `id` DESC ';

		$data = Util::getAllDataInSingleTable('zofui_tasktb_tbtaked',$where,$page,10,$order,30,false,$select,$andstr,'mytbtaked');
		

		$cutword = iunserializer( $_W['set']['cutword'] );

		foreach((array)$data[0] as $k=>$v){
			$url = $this->createMobileUrl('tbtask',array('id'=>$v['taskid']));

			$task = model_tbtask::getTask( $v['taskid'] );

			if( !empty( $task['userid'] ) ){
				$user = model_user::getSingleUser( $v['userid'] );
				$user['headimgurl'] = tomedia( $user['headimgurl'] );
				if( empty($user['headimgurl']) ) $user['headimgurl'] = '../addons/zofui_taskself/public/images/dhead.png';

			}else{
				$user['headimgurl'] = tomedia( $_W['set']['logo'] );
				$user['nickname'] = $_W['set']['sitename'];
			}
			

			if( is_array( $cutword ) ){
				$task['title'] = str_replace($cutword,'*', $task['title']);
			}
			$money = $task['money']*100/100;
			$tbmoney = $task['tbmoney']*100/100;

			$str .= <<<div
				<a href="{$url}">
					<div class="index_task_item">
						<div class="index_task_item_in item_cell_box item_cell_start">
							<div class="index_item_left">
								<img src="{$user['headimgurl']}">
							</div>
							<div class="index_item_right item_cell_flex">
								<div class="index_item_top oh">
									<li class="index_item_nick fl">{$user['nickname']}</li>
									<li class="index_item_read fr">{$task['scan']}</li>
								</div>
								<div class="index_item_title">{$topstr}{$task['title']}</div>
								<div class="index_item_bot oh">
									<li class="fl find_moneyinfo">
										<p class="font_mini">
											赏金:<span class="font_ff5f27">{$money}</span> 
											担保金:<span class="font_ff5f27">{$tbmoney}</span>
										</p>
									</li>
									{$statusstr}
								</div>
							</div>
						</div>
					</div>
				</a>
div;

		}


	// 查看的答案
	}elseif( $_GPC['op'] == 'anw' ){

		$where['uniacid'] = $_W['uniacid'];
		$where['uid'] = $userinfo['uid'];

		$select = ' a.id,a.title ';
		$data = Util::structWhereStringOfAnd($where,'b');
		$commonstr = tablename('zofui_tasktb_task') ."  AS a LEFT JOIN ".tablename('zofui_tasktb_anwread')." AS b ON a.`id` = b.`taskid` AND a.uniacid = b.uniacid WHERE ".$data[0];
		
		$countStr = "SELECT COUNT(*) FROM ".$commonstr;
		$selectStr =  "SELECT $select FROM ".$commonstr;
		$res = Util::fetchFunctionInCommon($countStr,$selectStr,$data[1],$_GPC['page'],20,' b.id DESC ',false,false);

		if( !empty( $res[0] ) ){
			foreach((array)$res[0] as $k=>$v){
				$url = $this->createMobileUrl('task',array('id'=>$v['id']));
				$str .= <<<div
					<div class=" down_item">
						<a href="{$url}">
							<div class="down_item_in item_cell_box" style="padding:0.5rem 0">
								<div class="item_cell_flex down_item_r" style="color:#333;padding-left:0;">
									{$v['title']}
								</div>
								<div style="">查看</div>
							</div>
						</a>
					</div>
div;

			}
		}



	// 黑名单
	}elseif( $_GPC['op'] == 'black' ){

		$where['uniacid'] = $_W['uniacid'];
		$where['userid'] = $userinfo['uid'];

		$data = Util::getAllDataInSingleTable('zofui_tasktb_tbblack',$where,$page,10,' `id` DESC ',false,false,' * ');

		if( !empty( $data[0] ) ){
			foreach((array)$data[0] as $k=>$v){

				$user = model_user::getSingleUser( $v['target'] );
				$user['headimgurl'] = tomedia( $user['headimgurl'] );
				if( empty($user['headimgurl']) ) $user['headimgurl'] = '../addons/zofui_taskself/public/images/dhead.png';


				$str .= <<<div
					<div class=" down_item">
						<div class="down_item_in item_cell_box">
							<div class=" down_item_l">
								<img src="{$user['headimgurl']}">
							</div>
							<div class="item_cell_flex down_item_r nickname">
								{$user['nickname']}
								<font class="userid">({$user['id']})</font>
							</div>
							<div class="down_item_r font_mini font_13px_999">
								<a href="javascript:;" class="delete_fromblack" uid="{$user['id']}">删除</a>
							</div>						
						</div>
					</div>
div;

			}
		}

	


	// 查询需置顶的任务 作为发的任务来使用
	}elseif( $_GPC['op'] == 'gettbtasktotop' ){

		$where['uniacid'] = $_W['uniacid'];
		$where['userid'] = $userinfo['uid'];
		$where['status'] = 0;
		$where['iscount'] = 0;
		$where['end>'] = TIMESTAMP - $_W['set']['tbcounttime']*3600;

		$select = ' id,title,scan,topendtime,start,end ';

		$data = Util::getAllDataInSingleTable('zofui_tasktb_tbtask',$where,$page,100,' `end` ASC ',false,false,$select);
		

		$cutword = iunserializer( $_W['set']['cutword'] );

		if( !empty( $data[0] ) ){
		foreach((array)$data[0] as $k=>$v){
			$url = $this->createMobileUrl('tbtask',array('id'=>$v['id']));
			$user = $userinfo;
			$user['headimgurl'] = tomedia( $user['headimgurl'] );
			if( empty($user['headimgurl']) ) $user['headimgurl'] = '../addons/zofui_taskself/public/images/dhead.png';

			$topstr = '';
			if( $v['topendtime'] > TIMESTAMP ){
				$topstr = '<span class="top_task">[顶]</span>';
			}

			if( is_array( $cutword ) ){
				$v['title'] = str_replace($cutword,'*', $v['title']);
			}

			$toptime = model_tbtask::countTopTime( $v );

			$addbtnstr = '';
			if( $toptime['canadd'] > 0 ){
				$addbtnstr = <<<div
					<div class="tr mt025 addtoptime" taskid="{$v['id']}">
						<a href="javascript:;" class="tr " >增加置顶时间</a>
					</div>
div;
			}

			//if( $toptime['canadd'] > 0 ){
			$str .= <<<div
				
					<div class="index_task_item">
						<div class="index_task_item_in item_cell_box item_cell_start">
							<div class="index_item_left">
								<img src="{$user['headimgurl']}">
							</div>
							<div class="index_item_right item_cell_flex">
								<a href="{$url}">
								<div class="index_item_top oh">
									<li class="index_item_nick fl">{$user['nickname']}</li>
									<li class="index_item_read fr">{$v['scan']}</li>
								</div>
								<div class="index_item_title pd025 pl0">{$topstr}{$v['title']}</div>
								</a>
								<div class="index_item_bot oh">
									<a href="{$url}">
									<div class="font_mid task_top_desc">
										任务置顶剩余时间：<span class="last_toptime font_ff5f27">{$toptime['last']}</span>小时，当前还可增加置顶：<span class="add_toptime font_ff5f27">{$toptime['canadd']}</span>小时
									</div>
									</a>
									{$addbtnstr}
								</div>
							</div>
						</div>
					</div>
				</a>
div;
		
			//}
		}
		}

	}elseif( $_GPC['op'] == 'imesslist' ){

		$where['uniacid'] = $_W['uniacid'];
		$where['uid'] = $userinfo['uid'];
		$page = intval($_GPC['page']);

		$select = ' * ';

		$data = Util::getAllDataInSingleTable('zofui_tasktb_imess',$where,$page,100,' `status` ASC,`istop` DESC,`id` DESC ',false,false,$select);

		if( !empty( $data[0] ) ){
			foreach((array)$data[0] as $k=>$v){

				if( empty($v['url']) ){
					$url = 'javascript:;';
				}else{
					$aa = explode('/', $v['url']);
					$site = $aa[0].'//'.$aa[2].'/';
					$url = str_replace($site,$_W['siteroot'], $v['url']);					
				}
				
				$time= date( 'Y-m-d H:i',$v['createtime'] );

				$content = $v['content'];
				if( !empty($content) ){
					$item = explode('\n', $content);
					$header = $item[0];
					unset($item[0]);
					if( empty($item) ){
						$content = $header;
					}else{
						$constr = '';
						foreach ($item as $vv) {
							$constr .= $vv.',';
						}
						$constr = trim($constr,',');
						$content = $header.'。'.$constr;
					}
				}

				$btn = '<span class="imess_readed">已阅</span>';
				if($v['status'] == 0){
					$btn = '<span class="imess_readit pri-color" mid="'.$v['id'].'">阅览</span>';
				}

				$str .= <<<div
					<a href="{$url}">
						<div class="imess_item">
							<div class="imess_top item_cell_box">
								<div class="imess_left item_cell_flex">
									<div class="imess_title">
										{$v['type']}
									</div>
									<div class="imess_time font_13px_999">
										{$time}
									</div>
									<div class="imess_content">
										{$content}
									</div>
								</div>
								<div class="imess_status">
									{$btn}
								</div>
							</div>
						</div>
					</a>
div;
		
			}
		}

	}elseif( $_GPC['op'] == 'tbform' ){

		$where['uniacid'] = $_W['uniacid'];
		$where['status'] = 0;

		$select = ' * ';

		$data = Util::getAllDataInSingleTable('zofui_tasktb_tbform',$where,1,100,' `number` DESC ',false,false,$select);

		if( !empty( $data[0] ) ){
			foreach((array)$data[0] as $k=>$v){
				$data[0][$k]['content'] = htmlspecialchars_decode($v['content']);
				$data[0][$k]['step'] = iunserializer($v['step']);

				$str .= <<<div
					
					<div class="item_cell_box tbform_item">
						<div class="item_cell_flex">{$v['name']}</div>
						<div class="stbform" fid="{$v['id']}">
							<a href="javascript:;" >选择</a>
						</div>
					</div>
div;
		
			//}
			}
		}

		$data = array('status'=>$status,'data'=>$str,'obj'=>$data[0]);
		echo json_encode($data);die;

	}

	
	if(!empty($str)) $status = 'ok';
	if(empty($str)){
		$str = '<li class="no_data_notice"><span>到底了</span></li>';
	}
	
	$data = array('status'=>$status,'data'=>$str);
	echo json_encode($data);

?>