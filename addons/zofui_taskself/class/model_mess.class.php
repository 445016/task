<?php 

class model_mess {



	static function getMess($openid){
		global $_W;

		$cache = Util::getCache('mess',$openid);
		if( empty( $cache ) ){
			$where = array('uniacid'=>$_W['uniacid']);
			$cache = pdo_get('zofui_tasktb_mess',array('uniacid'=>$_W['uniacid'],'openid'=>$openid));
			Util::setCache('mess',$openid,$cache);
		}
		return $cache;
	}

	static function getBigmess($set,$uid){
		global $_W;

		$mess = pdo_getall('zofui_tasktb_imess',array('uid'=>$uid,'status'=>0,'istop'=>1),array(),'',array('id DESC'),array(20));
		
		if( empty($mess) ){
			if( $set['bigmess'] == 1 || empty($uid) ) {
				return false;
			}
		}
		$mess = pdo_getall('zofui_tasktb_imess',array('uid'=>$uid,'status'=>0,'isbig'=>1),array(),'',array('istop DESC','id DESC'),array(20));
		
		if( !empty($mess) ){
			foreach ($mess as  &$v) {
				$v['content'] = explode('\n', $v['content']);
				$v['mess'] = $v['content'][0];
			}
			unset($v);
		}

		return $mess;
	}

}