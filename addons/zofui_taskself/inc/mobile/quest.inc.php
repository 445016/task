<?php 
	global $_W,$_GPC;
	$_GPC['do'] = 'quest';
	$_W['issub'] = 1;

	if( empty( $_GPC['op'] ) ) {

		$info = Util::getAllDataInSingleTable('zofui_tasktb_selfquest',array('uniacid'=>$_W['uniacid'],'status'=>0),1,999,' `number` DESC ',false,false,' title,id,type ');
		
		$quest = $info['0'];

		if( !empty( $quest ) ) {
			$arr = array();
			foreach ( $quest as $v ) {
				$arr[$v['type']][] = $v;
			}

			$num = max( count( $arr[0] ),count( $arr[1] ) );

			$newq = array();
			for ($i=0; $i < $num; $i++) {
				$arrin = array($arr[0][$i],$arr[1][$i]);
				$newq[] = $arrin;
			}
		}

		$settings = array(
			'sharetitle' => '平台答疑中心',
			'sharedesc' => '平台答疑中心',
			'shareimg' => tomedia($this->module['config']['shareimg']),
			'sharelink' => '',
			'do' => 'quest',
			'title' => '平台答疑中心',
		);

	}elseif( $_GPC['op'] == 'info' ) {

		$info = pdo_get('zofui_tasktb_selfquest',array('uniacid'=>$_W['uniacid'],'id'=>$_GPC['id']));
		if( empty( $info ) || $info['status'] == 1 ) message('没有找到数据');

		$info['content'] = htmlspecialchars_decode( $info['content'] );


		$settings = array(
			'sharetitle' => $info['title'],
			'sharedesc' => $info['title'],
			'shareimg' => tomedia($this->module['config']['shareimg']),
			'sharelink' => '',
			'do' => 'quest',
			'title' => $info['title'],
		);

	}
	
	include $this->template('quest');
