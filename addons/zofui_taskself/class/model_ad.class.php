<?php 

class model_ad {



	static function getAd(){
		global $_W;

		$cache = Util::getCache('ad','all');
		if( empty( $cache ) ){
			$where = array('uniacid'=>$_W['uniacid'],'status'=>0);
			$data = Util::getAllDataInSingleTable('zofui_tasktb_ad',$where,1,100,' `number` DESC ',false,false,' id,title ');
			
			$cache = $data[0];
			Util::setCache('ad','all',$cache);
		}
		return $cache;
	}

	

}