<?php 

class model_tabbar {



	static function getBar($uid=0){
		global $_W;
		
		$cache = Util::getCache('tabbar','all');
		if( empty( $cache ) ){
			$where = array('uniacid'=>$_W['uniacid']);
			$data = Util::getAllDataInSingleTable('zofui_tasktb_tabbar',$where,1,100,' `number` DESC ',false,false);
			
			$cache = $data[0];
			Util::setCache('tabbar','all',$cache);
		}

		$uid = $uid > 0 ? $uid : $_W['member']['uid'];
		if( $uid > 0 ){
			$imess = Util::countDataNumber('zofui_tasktb_imess',array('uniacid'=>$_W['uniacid'],'status'=>0,'uid'=>$uid));
		}
		
		return array('tabbar'=>$cache,'imess'=>$imess);
	}
	
	
}