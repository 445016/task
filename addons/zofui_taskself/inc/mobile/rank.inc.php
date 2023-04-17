<?php 
	global $_W,$_GPC;
	$_GPC['do'] = 'rank';
	$userinfo = model_user::initUserInfo();
	
	$timestr = '';
	if( $_GPC['type'] == 1 ){
		$time = strtotime( date('Y-m',TIMESTAMP) );
		$timestr = ' AND b.`time` >= '.$time;
	}
	if( $_GPC['type'] == 2 ){
		$time = strtotime(date('Y-m-d', strtotime("this week Monday", TIMESTAMP)));	
		$timestr = ' AND b.`time` >= '.$time;
	}
	$sql = "SELECT a.id,a.headimgurl,a.nickname, SUM(b.money) AS `totalmoney` FROM ".tablename('zofui_tasktb_moneylog')." AS b LEFT JOIN ".tablename('zofui_task_user')." AS a ON a.uid = b.userid  WHERE b.type IN (6,28,29,30,31,32,33,34,35) ".$timestr." GROUP BY b.`userid` ORDER BY `totalmoney` DESC LIMIT 50";

	$rank = pdo_fetchall($sql);
	$myrank = 0;
	if( !empty($rank) ){
		foreach ($rank as $k => $v) {
			if( $v['id'] == $userinfo['id'] ){
				$myrank = $k + 1;
				break;
			}
		}
	}


	$str = ' AND type IN (6,28,29,30,31,32,33,34,35) ';
	$where = array('userid'=>$userinfo['uid']);
	if( !empty($time) ) $where['time>'] = $time;

	$mytotal = Util::countDataSum('zofui_tasktb_moneylog',$where,' SUM(money) ',$str);
	
	$sharetitle = str_replace('{nick}', $userinfo['nickname'], $this->module['config']['sharetitle']);
	$sharedesc = str_replace('{nick}', $userinfo['nickname'], $this->module['config']['sharedesc']);

	$settings = array(
		'sharetitle' => $sharetitle,
		'sharedesc' => $sharedesc,
		'shareimg' => tomedia($this->module['config']['shareimg']),
		'sharelink' => Util::createModuleUrl('down',array('zfuid'=>$userinfo['id'])),
		'do' => 'rank',
		'op' => '',
		'title' => '',
	);

	include $this->template ('rank');
