<?php 

class model_guysort {



	static function getSort($type=''){
		global $_W;

		$cache = Util::getCache('guysort','all');
		if( empty( $cache ) ){
			$where = array('uniacid'=>$_W['uniacid']);
			$data = Util::getAllDataInSingleTable('zofui_tasktb_guysort',$where,1,100,' `number` DESC ',false,false);
			
			$cache =  $data[0];
			Util::setCache('guysort','all',$cache);
		}
		if( $type == 'struct' ) return self::structSort( $cache );
		return $cache;
	}

	static function structSort($banner){
		if( empty( $banner ) || !is_array( $banner ) ) return false;

		$f = 0;
		$key = 0;
		$arr = array();
		foreach ($banner as $k => $v) {
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

	static function guryList($where,$page,$num,$order,$iscache,$pager,$select,$str=''){
		global $_W;
		$data = Util::structWhereStringOfAnd($where,'a');
		$commonstr = tablename('zofui_task_user') ." AS a RIGHT JOIN ".tablename('zofui_tasktb_usersort')." AS b ON a.`uid` = b.`uid` AND a.uniacid = b.uniacid WHERE ".$data[0];
		
		$countStr = "SELECT COUNT(*) FROM ".$commonstr;
		$selectStr =  "SELECT $select FROM ".$commonstr;
		$res = Util::fetchFunctionInCommon($countStr,$selectStr,$data[1],$page,$num,$order,$iscache,$pager,$str);
		return $res;
	}


}