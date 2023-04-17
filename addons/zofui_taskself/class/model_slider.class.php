<?php 

class model_slider {

	
	static function getSlider($type){
		global $_W;

		$cache = Util::getCache('slider','all');
		if( empty( $cache ) ){
			$where = array('uniacid'=>$_W['uniacid']);
			$str = ' AND dayy = 0 OR ( dayy > 0 AND dayy > '. TIMESTAMP .' )';
			$data = Util::getAllDataInSingleTable('zofui_tasktb_slider',$where,1,100,' `number` DESC ',false,false,' * ',$str);
			$cache = $data[0];
			Util::setCache('slider','all',$cache);
		}

		$arr = array();
		if( !empty( $cache ) ){
			foreach ($cache as $k => $v) {

				if( $v['dayy'] > 0 && $v['dayy'] <= TIMESTAMP ){
					unset($cache[$k]);
					continue;
				}

				if( $type == 'index' && $v['isindex'] == 1 ){
					$arr[] = $v;
				}elseif( $type == 'usetask' && $v['isusetask'] == 1 ){
					$arr[] = $v;
				}elseif( $type == 'tbtask' && $v['istbtask'] == 1 ){
					$arr[] = $v;
				}elseif( $type == 'guy' && $v['isguy'] == 1 ){
					$arr[] = $v;
				}


			}
		}

		return $arr;
	}

	static function countBox($thisnum,$lastmoney,$min=0.01){
		
		$diffmoney = $lastmoney - $thisnum*0.01;

		if( $thisnum == 0 ){
			$money = $diffmoney;
		}else{
			$average = $diffmoney/$thisnum;
			if( $average*1.5 >  $diffmoney){
				$average = $diffmoney;
			}else{
				$average = $average*1.5;
			}
			$money = rand(1,$average*100)/100;	
		}
		return $money;

	} 

	static function getBackMoney($uid,$set){
		global $_W;
		if( $set['isanw'] != 1 ) return false;
		if( $set['anwmb'] <= 0 || $set['anwmbday'] <= 0 ) return false;

		// 所有利润
		$allrun = Util::countDataSum('zofui_tasktb_anwread',array('uniacid'=>$_W['uniacid']),' SUM(`lrfee`) ');

		// 所有查看答案金额
		$totalm = Util::countDataSum('zofui_tasktb_anwread',array('uniacid'=>$_W['uniacid']),' SUM(`cost`) ');
		// 我的查看答案金额
		$mym = Util::countDataSum('zofui_tasktb_anwread',array('uniacid'=>$_W['uniacid'],'uid'=>$uid),' SUM(`cost`) ');

		// 我的力量比
		$mymper = $totalm <= 0 ? 0 : $mym/$totalm;
		$mymper = $mymper <= 0 ? 0 : sprintf('%.2f',$mymper);

		$now = empty($_W['zfnow']) ? TIMESTAMP : $_W['zfnow']; // 测试用到
		$yesday = strtotime(date('Y-m-d',$now));

		// 截止今天凌晨利润
		$yesdayrun = Util::countDataSum('zofui_tasktb_anwread',array('uniacid'=>$_W['uniacid'],'createtime<'=>$yesday),' SUM(`lrfee`) ');
		
		// 所有已支付的回馈
		$geted = Util::countDataSum('zofui_tasktb_anwgeted',array('uniacid'=>$_W['uniacid'],'createtime<'=>$yesday),' SUM(`money`) ');
		
		
		$yesdayruna = $yesdayrun*$set['anwmb']/100; // 截止凌晨 回馈金额
		$lastrun = $yesdayruna - $geted + $set['ewaimb']; // 截止凌晨 剩余可回馈金额
		$yesdayrunb = $lastrun*$set['anwmbday']/100; // 截止凌晨 当天可回馈金额

		$yesdayrunmy = $yesdayrunb*$mymper; // 截止凌晨 我可以领取的回馈金额
		
		return array(
			'allrun'=> empty($allrun) ? 0 : $allrun,
			'totalm'=>empty($totalm) ? 0 : $totalm,
			'mym'=>empty($mym) ? 0 : $mym,
			'mymper'=>empty($mymper) ? 0 : $mymper,
			'yesdayrun'=>$yesdayrun, empty($yesdayrun) ? 0 : $yesdayrun,
			'geted'=>empty($geted) ? 0 : $geted,
			'yesdayruna'=>empty($yesdayruna) ? 0 : $yesdayruna,
			'lastrun' => empty($lastrun) ? 0 : $lastrun,
			'yesdayrunb'=>empty($yesdayrunb) ? 0 : $yesdayrunb,
			'yesdayrunmy' => empty($yesdayrunmy) ? 0 : $yesdayrunmy,
		);

	}

	static function getbaoshi($actsper){
		global $_W;

		$totalbaoshi = Util::countDataSum('zofui_task_user',array('uniacid'=>$_W['uniacid']),' SUM(`baoshi`) ');
		$totalactin = -Util::countDataSum('zofui_tasktb_moneylog',array('uniacid'=>$_W['uniacid'],'mtype'=>1),' SUM(`money`) ',' AND type = 40 ');
		$totalpayed = Util::countDataSum('zofui_tasktb_geoupbs',array('uniacid'=>$_W['uniacid']),' SUM(`money`) ');

		$last = ($totalactin - $totalpayed)*$_W['set']['actsper']/100;

		$per = sprintf('%.2f',$last/$totalbaoshi);

		return array(
			'totalbaoshi' => $totalbaoshi,
			'totalactin' => $totalactin,
			'totalpayed' => $totalpayed,
			'last' => $last,
			'per' => $per,
		);
	}

	static function getBanner(){
		global $_W;

		$cache = Util::getCache('banner','all');
		if( empty( $cache ) ){
			$where = array('uniacid'=>$_W['uniacid']);
			$data = Util::getAllDataInSingleTable('zofui_tasktb_banner',$where,1,100,' `number` DESC ',false,false);
			$cache = $data[0];
			Util::setCache('banner','all',$cache);
		}

		if( !empty($cache) ){
			foreach ($cache as $k => $v) {
				$cache[$k]['desc'] = str_replace("\n", "<br/>", $v['desc']);
			}
		}
		return $cache;
	}


}