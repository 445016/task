<?php 

class model_tasksort {



	static function getSort(){
		global $_W;

		$cache = Util::getCache('tasksort','all');
		if( empty( $cache ) ){
			$where = array('uniacid'=>$_W['uniacid']);
			$data = Util::getAllDataInSingleTable('zofui_tasktb_tasksort',$where,1,100,' `number` DESC ',false,false);
			
			$cache = $data[0];
			Util::setCache('tasksort','all',$cache);
		}
		return $cache;
	}

	static function structSort($sort){
		if( empty( $sort ) || !is_array( $sort ) ) return false;

		$f = 0;
		$key = 0;
		$arr = array();
		foreach ($sort as $k => $v) {
			$arr[$key][] = $v;

			if( $f >= 3 ){
				$f = 0;
				$key++;
			}else{
				$f ++;
			}
		}	
		return $arr;
	}
	
	static function initSort(){
		global $_W;
		$arr = array('点击任务','注册任务','投票任务','扫码任务');

		foreach ($arr as $v) {
			$data = array();
			$data['uniacid'] = $_W['uniacid'];
			$data['name'] = $v;
			Util::inserData('zofui_tasktb_tasksort',$data);
		}

		Util::deleteCache('tasksort','all');
	}


}