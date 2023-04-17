<?php 

class queue {
	
	private $islock = array('value'=>0,'expire'=>0);
	private $expiretime = 600; //锁过期时间，秒
	private $setting;
	//初始赋值
	public function __construct(){
		$lock = Util::getCache('queuelock','first');
		if(!empty($lock)) $this->islock = $lock;
		$this->setting = Util::getModuleConfig();
	}
	
	//加锁
	private function setLock(){
		$array = array('value'=>1,'expire'=>time());
		Util::setCache('queuelock','first',$array);
		$this->islock = $array;
	}
	
	//删除锁
	private function deleteLock(){
		Util::deleteCache('queuelock','first');
		$this->islock = array('value'=>0,'expire'=>time());
	}	
	
	//检查是否锁定
	public function checkLock(){
		$lock = $this->islock;	
		if($lock['value'] == 1 && $lock['expire'] < (time() - $this->expiretime )){ //过期了，删除锁
			$this->deleteLock();
			return false;
		}
		if(empty($lock['value'])){
			return false;
		}else{
			return true;
		}
	}
	
	public function queueMain(){
		global $_W;
		if($this->checkLock()){
			return false; //锁定的时候直接返回
		}else{
			$this->setLock(); //没锁的话锁定
		}
//file_put_contents(TSTB_ROOT."/params.log", var_export(date('Y-m-d H:i:s'), true).PHP_EOL, FILE_APPEND);
		//do something

		$this->dealPrivate(); //处理私包任务
		$this->dealTask(); //普通任务

		$this->sendMessage(); //发消息

		$this->deleteLock(); //执行完删除锁
	}
	
	

	//处理私包任务
	public function dealPrivate() {
		global $_W;
//file_put_contents(TSTB_ROOT."/params.log", var_export(date('Y-m-d H:i:s'), true).PHP_EOL, FILE_APPEND);
		//
		$where0 = array('status'=>0,'overtime0<' => time(),'isend'=>0,'uniacid'=>$_W['uniacid']);
		$privatetask0 = Util::getAllDataInSingleTable('zofui_tasktb_privatetask',$where0,1,210,'id ASC',false,false);
		
		if( !empty( $privatetask0[0] ) ){
			foreach($privatetask0[0] as $k=>$v){
				$res = pdo_update('zofui_tasktb_privatetask',array('status'=>1,'accepttime'=>time(),'isend'=>1),array('id'=>$v['id']));
				if( $res && $v['type'] == 2 ) model_privatetask::backMoneyToBossInPrivateTask($v);
			}
		}
		
		//2执行中的任务
		$where2 = array('status'=>2,'overtime2<' => time(),'isend'=>0,'uniacid'=>$_W['uniacid']);
		$privatetask2 = Util::getAllDataInSingleTable('zofui_tasktb_privatetask',$where2,1,210,'id ASC',false,false);
		
		if( !empty( $privatetask2[0] ) ){
			foreach($privatetask2[0] as $k=>$v){
				$res = model_privatetask::cancelTaskFuncInAjaxdealAndCrontab($v,5,$this->setting); //处理任务
			}
		}
		
		//3待雇主确认完成的任务
		$where3 = array('status'=>3,'overtime3<' => time(),'isend'=>0,'uniacid'=>$_W['uniacid']);
		$privatetask3 = Util::getAllDataInSingleTable('zofui_tasktb_privatetask',$where3,1,210,'id ASC',false,false);
		
		if( !empty( $privatetask3[0] ) ){
			foreach($privatetask3[0] as $k=>$v){
				
				model_privatetask::completeTaskInajaxdealAndCrontab( $v,11,$this->setting ); //处理任务
			}
		}		
		

		//7待雇主确认完成的任务
		$where7 = array( 'status'=>7,'overtime7<' => time(),'isend'=>0,'uniacid'=>$_W['uniacid'] );
		$privatetask7 = Util::getAllDataInSingleTable('zofui_tasktb_privatetask',$where7,1,210,'id ASC',false,false);
		
		if( !empty( $privatetask7[0] ) ){
			foreach($privatetask7[0] as $k=>$v){
				model_privatetask::acceptRefuseRusultInAjaxAndCronb($v,12,$this->setting);  //已发通知
			}
		}	

	}
	
	
	public function dealTask(){
		global $_W;
		
		
		// 改变是否已被抢完 普通和试用任务
		$where0 = array('status'=>0,'iscount' => 0,'isempty'=>1,'uniacid'=>$_W['uniacid']);
		$allempty = Util::getAllDataInSingleTable('zofui_tasktb_task',$where0,1,20110,' `end` DESC',false,false,' id,num,type ');
		if( !empty( $allempty[0] ) ){
			foreach($allempty[0] as $k=>$v){
				if( $v['type'] == 0 ){
					$last = model_task::isEmpty($v['id'],$v['num']);
				}elseif( $v['type'] == 1 ){
					$last = model_task::isEmptyUse($v['id'],$v['num']);
				}
				
				if( $last > 0 ){
					pdo_update('zofui_tasktb_task',array('isempty'=>0),array('id'=>$v['id']));
				}
			}
		}
		// 将已被抢完的改成 被抢完 普通和试用任务
		$where3 = array('status'=>0,'iscount' => 0,'isempty'=>0,'uniacid'=>$_W['uniacid']);
		$allemptys = Util::getAllDataInSingleTable('zofui_tasktb_task',$where3,1,20110,' `end` DESC',false,false,' id,num,type ');
		if( !empty( $allemptys[0] ) ){
			foreach($allemptys[0] as $k=>$v){

				if( $v['type'] == 0 ){
					$last = model_task::isEmpty($v['id'],$v['num']);
				}elseif( $v['type'] == 1 ){
					$last = model_task::isEmptyUse($v['id'],$v['num']);
				}

				if( $last <= 0 ){
					pdo_update('zofui_tasktb_task',array('isempty'=>1),array('id'=>$v['id']));
				}
			}
		}

		// 结算任务 普通任务
		/*$where1 = array('iscount' => 0,'uniacid'=>$_W['uniacid'],'end<'=>time(),'type'=>0);
		$needcount = Util::getAllDataInSingleTable('zofui_tasktb_task',$where1,1,20110,' `end` DESC',false,false,' * ');
		if( !empty( $needcount[0] ) ){
			foreach($needcount[0] as $k=>$v){
				
				$counting = Util::getCache('counttask',$v['id']);
				if( is_array( $counting ) && $counting['status'] == 1 ) {
					continue;
				}
				Util::setCache('counttask',$v['id'],array('status'=>1));
				$res = model_task::countTask( $v );
				Util::deleteCache( 'counttask',$v['id'] );
			}
		}*/		
		
		
		// 改变标识未开始的任务 不分任务类型
		$where2 = array('isstart'=>1,'start<'=>time(),'uniacid'=>$_W['uniacid']);
		$needstart = Util::getAllDataInSingleTable('zofui_tasktb_task',$where2,1,20110,' `end` DESC',false,false,' id ');
		if( !empty( $needstart[0] ) ){
			foreach($needstart[0] as $k=>$v){
				pdo_update('zofui_tasktb_task',array('isstart'=>0),array('id'=>$v['id']));
			}
		}

		// 即时改变任务剩余数量缓存 不分任务类型
		$where4 = array('status'=>0,'iscount' => 0,'uniacid'=>$_W['uniacid'],'isempty'=>0);
		$allemptya = Util::getAllDataInSingleTable('zofui_tasktb_task',$where4,1,20110,' `end` DESC',false,false,' id,num,type ');
		
		if( !empty( $allemptya[0] ) ){
			foreach($allemptya[0] as $k=>$v){
				if( $v['type'] == 0 ){
					$last = model_task::isEmpty($v['id'],$v['num']);
				}else if( $v['type'] == 1 ){
					$last = model_task::isEmptyUse($v['id'],$v['num']);
				}
								
				Util::setCache('takednum',$v['id'],$v['num']-$last);
			}
		}
		
		// 试用任务自动通过审核
		$useautotime = $_W['set']['useautotime'] > 0 ? $_W['set']['useautotime'] : 12;

		$where5 = array('status'=>0,'createtime<' => TIMESTAMP - $useautotime*3600,'uniacid'=>$_W['uniacid']);
		$allvery = Util::getAllDataInSingleTable('zofui_tasktb_usetasklog',$where5,1,20110,' `id` DESC',false,false);
		
		if( !empty( $allvery[0] ) ){
			foreach($allvery[0] as $k=>$v){
				$task = pdo_get('zofui_tasktb_task',array('id'=>$v['taskid']),array('id','title'));
				model_task::passUseTask( $task,$v );
			}
		}

		// 自动失败试用任务
		$where6 = array('status'=>1,'passortime<' => TIMESTAMP - $useautotime*3600,'uniacid'=>$_W['uniacid']);
		$allfail = Util::getAllDataInSingleTable('zofui_tasktb_usetasklog',$where6,1,20110,' `id` DESC',false,false);
		
		if( !empty( $allfail[0] ) ){
			foreach($allfail[0] as $k=>$v){
				$task = pdo_get('zofui_tasktb_task',array('id'=>$v['taskid']),array('id','title'));
				model_task::failUseTask( $v,'未在期限内提交订单内容，系统自动认定任务失败',$task );
			}
		}

		// 自动结算试用任务
		$waitday = $_W['set']['usecounttime'] > 0 ? $_W['set']['usecounttime'] : 3;
		
		$where7 = array('iscount'=>0,'end<' => TIMESTAMP - $waitday*3600*24,'uniacid'=>$_W['uniacid'],'type'=>1);
		$needcount = Util::getAllDataInSingleTable('zofui_tasktb_task',$where7,1,20110,' `id` DESC',false,false);
//file_put_contents(TSTB_ROOT."/params.log", var_export($where7, true).PHP_EOL, FILE_APPEND);
		if( !empty( $needcount[0] ) ){
			foreach($needcount[0] as $v){
				$counting = Util::getCache('counttask',$v['id']);

				if( is_array( $counting ) && $counting['status'] == 1 ) {
					continue;
				}

				Util::setCache('counttask',$v['id'],array('status'=>1));
				model_task::countUseTask($v);
				Util::deleteCache( 'counttask',$v['id'] );
			}
		}		


//file_put_contents(TSTB_ROOT."/params.log", var_export(date('Y-m-d H:i:s'), true).PHP_EOL, FILE_APPEND);
	}

	

/*************以下是发消息*****************/		
	
	//删除消息队列
	public function deleteMessage($id){
		global $_W;		
		pdo_delete('zofui_tasktb_message',array('uniacid'=>$_W['uniacid'],'id'=>$id),'AND');
	}
	
	//查询需要发消息的记录
	public function getNeedMessageItem(){
		global $_W;
		$array = array(':uniacid'=>$_W['uniacid']);
		return pdo_fetchall("SELECT * FROM ".tablename('zofui_tasktb_message')." WHERE `uniacid` = :uniacid ORDER BY `id` ASC ",$array);
	}
	
	//发消息
	public function sendMessage(){
		global $_W;
		$message = $this->getNeedMessageItem();
		
		foreach($message as $k=>$v){
			if($v['type'] == 1){ //发货消息
				$task = pdo_get( 'zofui_tasktb_task',array( 'uniacid'=>$_W['uniacid'],'id'=>$v['str'] ) );
				if( !empty( $task ) ){
					$admin = iunserializer( $this->setting['admin'] );

					if( !empty( $admin ) && is_array( $admin ) ){
						foreach ( $admin as $vv ) {
							if( $vv['ismess'] == 1 ) Message::cmessage($vv['userid'],$vv['openid'],$task['title'],'pubtask',$task['id']);
						}
					}
				}
			}
				

			$this->deleteMessage($v['id']); //删除已发的
		}
	}
		
	
	

	
}

