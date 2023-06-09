<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
	
/**
 * 其他系统请求格式：
 * 请求方式: GET
 * 形如：http://域名/api/accesstoken.php?type=TYPE&appid=APPID
 * TYPE支持：公众号：1，小程序：4
 * APPID为请求账号的appid
 */
error_reporting(0);
define('IN_SYS', true);
define('WECHATS', 1);
define('WXAPP', 4);

function account_tablename($type) {
	$account_types = array(
		WECHATS => 'account_wechats',
		WXAPP => 'account_wxapp',
	);
	return !empty($account_types[$type]) ? $account_types[$type] : '';
}

require '../framework/bootstrap.inc.php';
parse_str($_SERVER['QUERY_STRING'], $query);
if(is_array($query) && count($query) == 3 && in_array($query['type'], array(WECHATS, WXAPP)) && !empty($query['appid']) && !empty($query['secret'])) {
	$table_name = account_tablename($query['type']);
	if (empty($table_name)) {
		exit('Invalid Type');
	}
	$account_info = pdo_fetch('SELECT w.* FROM ' . tablename($table_name) . ' AS w LEFT JOIN ' . tablename('account') . ' AS a ON w.uniacid = a.uniacid WHERE w.key = :wxkey AND a.isdeleted = :isdeleted', array(':wxkey' => $query['appid'], ':isdeleted' => STATUS_OFF));
	if (empty($account_info) || empty($account_info['uniacid'])) {
		exit('Appid Not Found');
	}
	$account_api = WeAccount::createByUniacid($account_info['uniacid']);
		$result = array('accesstoken' => $account_api->getAccessToken());
	echo json_encode($result);
	exit;
	
}
exit('Invalid Request');