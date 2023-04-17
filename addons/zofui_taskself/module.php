<?php

global $_W;
$_W['asd'] = '123';
defined('IN_IA') or exit('Access Denied');
define('TSTB_ROOT', IA_ROOT . '/addons/zofui_taskself/');
define('TSTB_URL', $_W['siteroot'] . '/addons/zofui_taskself/');
define('MODULE', 'zofui_taskself');
require_once TSTB_ROOT . 'class/autoload.php';
class Zofui_taskselfModule extends WeModule
{
    public function __construct()
    {
        global $_W;
        if (defined('IN_SYS')) {
            if (method_exists(get_parent_class(), '__construct')) {
                parent::__construct();
            }
            $set = Util::getModuleConfig();
            $_W['mtemp'] = empty($set['webtemp']) ? 'bdui' : $set['webtemp'];
        }
    }
    public function settingsDisplay($settings)
    {
        global $_W, $_GPC;
        model_user::wInit();
        $key = pdo_get('zofui_tasktb_codekey');
        if ($_W['role'] != 'founder' && $_W['role'] != 'manager' && in_array('set', $settings['power'])) {
            message('您没有参数设置权限', 'refresh');
        }
        if (checksubmit('reset')) {
            $api = 'http://api.zofui.net/app/index.php?c=mauth&a=taskreset';
            $res = Util::httpPost($api, array("site" => $_W['siteroot'], "en" => MODULE, "for" => $_GPC, "key" => $key['key']));
            $res = json_decode($res, true);
            $dat = $res['data'];
            if (!is_array($dat) || empty($res)) {
                itoast('保存数据异常', 'refresh', 'error');
            }
            $istask = pdo_get('zofui_tasktb_tasksort', array("uniacid" => $_W['uniacid']));
            if (empty($istask)) {
                model_tasksort::initSort();
            }
            if ($this->saveSettings($dat)) {
                Util::getCache('webbar', 'web');
                message('保存成功', 'refresh', 'success');
            }
        }
        if (checksubmit('submit')) {
            $_GPC = Util::trimWithArray($_GPC);
            load()->func('file');
            $r = mkdirs(TSTB_ROOT . '/cert/' . $_W['uniacid']);
            if (!empty($_GPC['cert'])) {
                $ret = file_put_contents(TSTB_ROOT . '/cert/' . $_W['uniacid'] . '/apiclient_cert.pem', trim($_GPC['cert']));
                $r = $r && $ret;
            }
            if (!empty($_GPC['key'])) {
                $ret = file_put_contents(TSTB_ROOT . '/cert/' . $_W['uniacid'] . '/apiclient_key.pem', trim($_GPC['key']));
                $r = $r && $ret;
            }
            if (!empty($_GPC['rootca'])) {
                $ret = file_put_contents(TSTB_ROOT . '/cert/' . $_W['uniacid'] . '/rootca.pem', trim($_GPC['rootca']));
                $r = $r && $ret;
            }
            if (!empty($_GPC['alikey'])) {
                $ret = file_put_contents(TSTB_ROOT . '/cert/' . $_W['uniacid'] . '/alikey.txt', trim($_GPC['alikey']));
                $r = $r && $ret;
            }
            if (!$r) {
                message('证书保存失败, 请保证 /addons/zofui_taskself/cert/ 目录可写，如果无法解决请使用上传工具将证书文件上传至' . TSTB_ROOT . '/cert/' . $_W['uniacid'] . '目录下');
            }
            if (strlen($_GPC['usetaskstep']) >= 3000) {
                message('试用任务步骤字符太多了，请减少一些', 'refresh');
            }
            $api = 'http://api.zofui.net/app/index.php?c=taskwap&a=setting';
            $res = Util::httpPost($api, array("site" => $_W['siteroot'], "en" => MODULE, "for" => $_GPC, "setop" => "setting", "key" => $key['key']));
            $res = json_decode($res, true);
            $dat = $res['data'];
            if (!is_array($dat) || empty($res)) {
                itoast('保存数据异常', 'refresh', 'error');
            }
            if ($dat['isusetask'] == 1 && $dat['istbtask'] == 1) {
                message('试用任务和担保任务只能开启一个', 'refresh', 'error');
            }
            if ($this->saveSettings($dat)) {
                message('保存成功', 'refresh');
            }
        }
        set_time_limit(0);
        $url = 'http://api.zofui.net/app/index.php?c=taskwap&a=settinghtml';
        $html = Util::httpPost($url, array("setting" => $settings, "site" => $_W['siteroot'], "en" => MODULE, "setop" => "setting", "key" => $key['key'], "att" => $_W['attachurl']));
        $grouperr = Util::getCache('grouperr', 'all');
        $grouperr = empty($grouperr) ? '无' : $grouperr;
        $html = str_replace('{grouperr}', $grouperr, $html);
        $html = str_replace('{token}', $_W['token'], $html);
        $scan = pdo_get('zofui_tasktb_scan', array("uniacid" => $_W['uniacid']));
        if (empty($scan)) {
            $scandata = array("uniacid" => $_W['uniacid']);
            pdo_insert('zofui_tasktb_scan', $scandata);
        }
        if (!pdo_fieldexists('zofui_task_user', 'nickname')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_task_user') . ' ADD `nickname` varchar(64) DEFAULT NULL COMMENT \'淘宝版本里用到 昵称\';');
        }
        if (!pdo_fieldexists('zofui_task_user', 'headimgurl')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_task_user') . ' ADD  `headimgurl` varchar(300) DEFAULT NULL COMMENT \'淘宝版本里用到 头像\';');
        }
        if (!pdo_fieldexists('zofui_task_user', 'sex')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_task_user') . ' ADD  `sex` tinyint(1) unsigned NOT NULL DEFAULT \'0\' COMMENT \'淘宝版本里用到 1男 2女\';');
        }
        if (!pdo_fieldexists('zofui_task_user', 'activity')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_task_user') . ' ADD  `activity` int(11) unsigned NOT NULL DEFAULT \'0\' COMMENT \'淘宝版本里用到 活跃度\';');
        }
        if (!pdo_fieldexists('zofui_task_user', 'uptime')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_task_user') . ' ADD  `uptime` int(11) unsigned NOT NULL DEFAULT \'0\' COMMENT \'淘宝版本里用到 更新活跃度时间\';');
        }
        if (!pdo_fieldexists('zofui_task_user', 'ispub')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_task_user') . ' ADD  `ispub` tinyint(1) unsigned NOT NULL DEFAULT \'0\' COMMENT \'淘宝版本里用到 0不是发任务的 1是\';');
        }
        if (!pdo_fieldexists('zofui_task_user', 'isacc')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_task_user') . ' ADD  `isacc` tinyint(1) unsigned NOT NULL DEFAULT \'0\' COMMENT \'淘宝版本里用到 0不是接任务的 1是\';');
        }
        if (!pdo_fieldexists('zofui_task_user', 'conweixin')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_task_user') . ' ADD  `conweixin` tinyint(1) unsigned NOT NULL DEFAULT \'0\' COMMENT \'淘宝版本里用到 0不开启微信联系 1 开启\';');
        }
        if (!pdo_fieldexists('zofui_task_user', 'conmobile')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_task_user') . ' ADD  `conmobile` tinyint(1) unsigned NOT NULL DEFAULT \'0\' COMMENT \'淘宝版本里用到 0不开启电话联系 1开启\';');
        }
        if (!pdo_fieldexists('zofui_task_user', 'parent')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_task_user') . ' ADD  `parent` int(11) unsigned NOT NULL DEFAULT \'0\' COMMENT \'淘宝版本里用到 上级id\';');
        }
        if (!pdo_fieldexists('zofui_task_user', 'limitnum')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_task_user') . ' ADD `limitnum` int(5) unsigned NOT NULL DEFAULT \'0\' COMMENT \'回复次数限制\';');
        }
        if (!pdo_fieldexists('zofui_task_user', 'account')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_task_user') . ' ADD  `account` varchar(20) DEFAULT NULL COMMENT \'账户，绑定电话号码\';');
        }
        if (!pdo_fieldexists('zofui_task_user', 'createtime')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_task_user') . ' ADD  `createtime` int(11) unsigned NOT NULL DEFAULT \'0\' COMMENT \'创建时间\';');
        }
        if (!pdo_fieldexists('zofui_task_user', 'giveparent')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_task_user') . ' ADD  `giveparent` decimal(10,2) unsigned NOT NULL DEFAULT \'0.00\' COMMENT \'上级获得提成总额\';');
        }
        if (!pdo_fieldexists('zofui_task_user', 'authopenid')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_task_user') . ' ADD  `authopenid` varchar(64) DEFAULT NULL COMMENT \'被借权公众号的openid\';');
        }
        if (!pdo_fieldexists('zofui_tasktb_taskmessage', 'type')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_taskmessage') . ' ADD `type` tinyint(1) unsigned NOT NULL DEFAULT \'0\' COMMENT \'0普通任务\';');
        }
        if (!pdo_fieldexists('zofui_task_user', 'tbpub')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_task_user') . ' ADD `tbpub` int(11) unsigned NOT NULL DEFAULT \'0\' COMMENT \'发布担保任务数量\';');
        }
        if (!pdo_fieldexists('zofui_task_user', 'tbsuccess')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_task_user') . ' ADD `tbsuccess` int(11) unsigned NOT NULL DEFAULT \'0\' COMMENT \'采纳担保任务数量\';');
        }
        if (!pdo_fieldexists('zofui_task_user', 'tbtake')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_task_user') . ' ADD `tbtake` int(11) unsigned NOT NULL DEFAULT \'0\' COMMENT \'抢到担保任务数量\';');
        }
        if (!pdo_fieldexists('zofui_task_user', 'tbcom')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_task_user') . ' ADD `tbcom` int(11) unsigned NOT NULL DEFAULT \'0\' COMMENT \'完成担保任务数量\';');
        }
        if (!pdo_fieldexists('zofui_task_user', 'payqr')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_task_user') . ' ADD `payqr` varchar(500) DEFAULT NULL;');
        }
        if (!pdo_fieldexists('zofui_task_user', 'uselimitnum')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_task_user') . ' ADD `uselimitnum` int(11) unsigned NOT NULL DEFAULT \'0\' COMMENT \'限制抢试用任务数量\';');
        }
        if (!pdo_fieldexists('zofui_task_user', 'uselimitday')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_task_user') . ' ADD `uselimitday` int(11) unsigned NOT NULL DEFAULT \'0\' COMMENT \'限制抢试用任务数量\';');
        }
        if (!pdo_fieldexists('zofui_tasktb_complain', 'userid')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_complain') . ' ADD `userid` int(11) unsigned NOT NULL DEFAULT \'0\' COMMENT \'会员id\';');
        }
        if (!pdo_indexexists('zofui_tasktb_complain', 'userid')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_complain') . ' ADD INDEX userid(`userid`);');
        }
        if (!pdo_fieldexists('zofui_tasktb_draw', 'userid')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_draw') . ' ADD `userid` int(11) unsigned NOT NULL DEFAULT \'0\' COMMENT \'会员id\';');
        }
        if (!pdo_indexexists('zofui_tasktb_draw', 'userid')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_draw') . ' ADD INDEX userid(`userid`);');
        }
        if (!pdo_fieldexists('zofui_tasktb_mess', 'userid')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_mess') . ' ADD `userid` int(11) unsigned NOT NULL DEFAULT \'0\' COMMENT \'会员id\';');
        }
        if (!pdo_indexexists('zofui_tasktb_mess', 'userid')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_mess') . ' ADD INDEX userid(`userid`);');
        }
        if (!pdo_fieldexists('zofui_tasktb_message', 'userid')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_message') . ' ADD `userid` int(11) unsigned NOT NULL DEFAULT \'0\' COMMENT \'会员id\';');
        }
        if (!pdo_indexexists('zofui_tasktb_message', 'userid')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_message') . ' ADD INDEX userid(`userid`);');
        }
        if (!pdo_fieldexists('zofui_tasktb_moneylog', 'userid')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_moneylog') . ' ADD `userid` int(11) unsigned NOT NULL DEFAULT \'0\' COMMENT \'会员id\';');
        }
        if (!pdo_indexexists('zofui_tasktb_moneylog', 'userid')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_moneylog') . ' ADD INDEX userid(`userid`);');
        }
        if (!pdo_fieldexists('zofui_tasktb_paylog', 'userid')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_paylog') . ' ADD `userid` int(11) unsigned NOT NULL DEFAULT \'0\' COMMENT \'会员id\';');
        }
        if (!pdo_indexexists('zofui_tasktb_paylog', 'userid')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_paylog') . ' ADD INDEX userid(`userid`);');
        }
        if (!pdo_fieldexists('zofui_tasktb_privatetask', 'pubuid')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_privatetask') . ' ADD `pubuid` int(11) unsigned NOT NULL DEFAULT \'0\' COMMENT \'会员id\';');
        }
        if (!pdo_indexexists('zofui_tasktb_privatetask', 'pubuid')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_privatetask') . ' ADD INDEX pubuid(`pubuid`);');
        }
        if (!pdo_fieldexists('zofui_tasktb_privatetask', 'acceptuid')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_privatetask') . ' ADD `acceptuid` int(11) unsigned NOT NULL DEFAULT \'0\' COMMENT \'会员id\';');
        }
        if (!pdo_indexexists('zofui_tasktb_privatetask', 'acceptuid')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_privatetask') . ' ADD INDEX acceptuid(`acceptuid`);');
        }
        if (!pdo_fieldexists('zofui_tasktb_privatetask', 'bossuid')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_privatetask') . ' ADD `bossuid` int(11) unsigned NOT NULL DEFAULT \'0\' COMMENT \'会员id\';');
        }
        if (!pdo_indexexists('zofui_tasktb_privatetask', 'bossuid')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_privatetask') . ' ADD INDEX bossuid(`bossuid`);');
        }
        if (!pdo_fieldexists('zofui_tasktb_privatetask', 'workeruid')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_privatetask') . ' ADD `workeruid` int(11) unsigned NOT NULL DEFAULT \'0\' COMMENT \'会员id\';');
        }
        if (!pdo_indexexists('zofui_tasktb_privatetask', 'workeruid')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_privatetask') . ' ADD INDEX workeruid(`workeruid`);');
        }
        if (!pdo_fieldexists('zofui_tasktb_selfqrcode', 'userid')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_selfqrcode') . ' ADD `userid` int(11) unsigned NOT NULL DEFAULT \'0\' COMMENT \'会员id\';');
        }
        if (!pdo_indexexists('zofui_tasktb_selfqrcode', 'userid')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_selfqrcode') . ' ADD INDEX userid(`userid`);');
        }
        if (!pdo_fieldexists('zofui_tasktb_taked', 'userid')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_taked') . ' ADD `userid` int(11) unsigned NOT NULL DEFAULT \'0\' COMMENT \'会员id\';');
        }
        if (!pdo_indexexists('zofui_tasktb_taked', 'userid')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_taked') . ' ADD INDEX userid(`userid`);');
        }
        if (!pdo_fieldexists('zofui_tasktb_taked', 'pubuid')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_taked') . ' ADD `pubuid` int(11) unsigned NOT NULL DEFAULT \'0\' COMMENT \'会员id\';');
        }
        if (!pdo_indexexists('zofui_tasktb_taked', 'pubuid')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_taked') . ' ADD INDEX pubuid(`pubuid`);');
        }
        if (!pdo_fieldexists('zofui_tasktb_task', 'userid')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_task') . ' ADD `userid` int(11) unsigned NOT NULL DEFAULT \'0\' COMMENT \'会员id\';');
        }
        if (!pdo_indexexists('zofui_tasktb_task', 'userid')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_task') . ' ADD INDEX userid(`userid`);');
        }
        if (!pdo_fieldexists('zofui_tasktb_taskmessage', 'userid')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_taskmessage') . ' ADD `userid` int(11) unsigned NOT NULL DEFAULT \'0\' COMMENT \'会员id\';');
        }
        if (!pdo_indexexists('zofui_tasktb_taskmessage', 'userid')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_taskmessage') . ' ADD INDEX userid(`userid`);');
        }
        if (!pdo_fieldexists('zofui_tasktb_tbtaked', 'userid')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_tbtaked') . ' ADD `userid` int(11) unsigned NOT NULL DEFAULT \'0\' COMMENT \'会员id\';');
        }
        if (!pdo_indexexists('zofui_tasktb_tbtaked', 'userid')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_tbtaked') . ' ADD INDEX userid(`userid`);');
        }
        if (!pdo_fieldexists('zofui_tasktb_tbtaked', 'pubuid')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_tbtaked') . ' ADD `pubuid` int(11) unsigned NOT NULL DEFAULT \'0\' COMMENT \'会员id\';');
        }
        if (!pdo_indexexists('zofui_tasktb_tbtaked', 'pubuid')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_tbtaked') . ' ADD INDEX pubuid(`pubuid`);');
        }
        if (!pdo_fieldexists('zofui_tasktb_tbtask', 'userid')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_tbtask') . ' ADD `userid` int(11) unsigned NOT NULL DEFAULT \'0\' COMMENT \'会员id\';');
        }
        if (!pdo_indexexists('zofui_tasktb_tbtask', 'userid')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_tbtask') . ' ADD INDEX userid(`userid`);');
        }
        if (!pdo_fieldexists('zofui_tasktb_tbtaskstep', 'userid')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_tbtaskstep') . ' ADD `userid` int(11) unsigned NOT NULL DEFAULT \'0\' COMMENT \'会员id\';');
        }
        if (!pdo_indexexists('zofui_tasktb_tbtaskstep', 'userid')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_tbtaskstep') . ' ADD INDEX userid(`userid`);');
        }
        if (!pdo_fieldexists('zofui_tasktb_usetasklog', 'userid')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_usetasklog') . ' ADD `userid` int(11) unsigned NOT NULL DEFAULT \'0\' COMMENT \'会员id\';');
        }
        if (!pdo_indexexists('zofui_tasktb_usetasklog', 'userid')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_usetasklog') . ' ADD INDEX userid(`userid`);');
        }
        if (!pdo_fieldexists('zofui_tasktb_usetasklog', 'pubuid')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_usetasklog') . ' ADD `pubuid` int(11) unsigned NOT NULL DEFAULT \'0\' COMMENT \'会员id\';');
        }
        if (!pdo_indexexists('zofui_tasktb_usetasklog', 'pubuid')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_usetasklog') . ' ADD INDEX pubuid(`pubuid`);');
        }
        if (!pdo_fieldexists('zofui_tasktb_tbblack', 'userid')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_tbblack') . ' ADD `userid` int(11) unsigned NOT NULL DEFAULT \'0\' COMMENT \'会员id\';');
        }
        if (!pdo_indexexists('zofui_tasktb_tbblack', 'userid')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_tbblack') . ' ADD INDEX userid(`userid`);');
        }
        if (!pdo_fieldexists('zofui_tasktb_tbblack', 'targetuid')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_tbblack') . ' ADD `targetuid` int(11) unsigned NOT NULL DEFAULT \'0\' COMMENT \'会员id\';');
        }
        if (!pdo_indexexists('zofui_tasktb_tbblack', 'targetuid')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_tbblack') . ' ADD INDEX targetuid(`targetuid`);');
        }
        if (!pdo_fieldexists('zofui_tasktb_draw', 'server')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_draw') . ' ADD `server` decimal(10,2) unsigned NOT NULL DEFAULT \'0.00\' COMMENT \'服务费\';');
        }
        pdo_query('CREATE TABLE IF NOT EXISTS ' . tablename('zofui_tasktb_tabbar') . ' (
		  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		  `uniacid` int(10) unsigned NOT NULL DEFAULT \'0\',
		  `name` varchar(100) NOT NULL DEFAULT \'0\' COMMENT \'分类名称\',
		  `number` int(11) unsigned NOT NULL DEFAULT \'0\' COMMENT \'越大越前\',
		  `img` varchar(500) DEFAULT NULL,
		  `url` varchar(500) DEFAULT NULL,
		  PRIMARY KEY (`id`),
		  KEY `index` (`uniacid`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT=\'导航\';');
        if (!pdo_fieldexists('zofui_tasktb_tasksort', 'dmoney')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_tasksort') . ' ADD `dmoney` decimal(10,2) unsigned NOT NULL DEFAULT \'0.00\' COMMENT \'默认金额\';');
        }
        pdo_query('CREATE TABLE IF NOT EXISTS ' . tablename('zofui_tasktb_taskform') . ' (
		  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		  `uniacid` int(10) unsigned NOT NULL DEFAULT \'0\',
		  `name` varchar(100) NOT NULL DEFAULT \'0\' COMMENT \'分类名称\',
		  `number` int(11) unsigned NOT NULL DEFAULT \'0\' COMMENT \'越大越前\',
		  `form` text,
		  `type` tinyint(1) unsigned NOT NULL DEFAULT \'0\' COMMENT \'0普通任务\',
		  PRIMARY KEY (`id`),
		  KEY `index` (`uniacid`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;');
        if (!pdo_fieldexists('zofui_tasktb_taked', 'subform')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_taked') . ' ADD `subform` text;');
        }
        if (!pdo_fieldexists('zofui_tasktb_task', 'istaskform')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_task') . ' ADD `istaskform` tinyint(1) unsigned NOT NULL DEFAULT \'0\' COMMENT \'0不使用模板 1使用回复模板\';');
        }
        if (!pdo_fieldexists('zofui_tasktb_task', 'formid')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_task') . ' ADD `formid` int(11) unsigned NOT NULL DEFAULT \'0\' COMMENT \'模板id\';');
        }
        if (!pdo_fieldexists('zofui_tasktb_draw', 'payno')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_draw') . ' ADD `payno` varchar(64) DEFAULT NULL;');
        }
        if (!pdo_fieldexists('zofui_tasktb_taked', 'twoupmoney')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_taked') . ' ADD `twoupmoney` decimal(10,2) unsigned NOT NULL DEFAULT \'0.00\' COMMENT \'上上级提成\';');
        }
        if (!pdo_fieldexists('zofui_tasktb_taked', 'threeupmoney')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_taked') . ' ADD `threeupmoney` decimal(10,2) unsigned NOT NULL DEFAULT \'0.00\' COMMENT \'上上上级奖励\';');
        }
        if (!pdo_fieldexists('zofui_task_user', 'givetwo')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_task_user') . ' ADD `givetwo` decimal(10,2) unsigned NOT NULL DEFAULT \'0.00\' COMMENT \'累计给上上级的提成\';');
        }
        if (!pdo_fieldexists('zofui_task_user', 'givethree')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_task_user') . ' ADD `givethree` decimal(10,2) unsigned NOT NULL DEFAULT \'0.00\' COMMENT \'累计给上上级的提成\';');
        }
        if (!pdo_fieldexists('zofui_tasktb_task', 'giveparent')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_task') . ' ADD `giveparent` decimal(10,2) unsigned NOT NULL DEFAULT \'0.00\' COMMENT \'发任务上级提成\';');
        }
        if (!pdo_fieldexists('zofui_tasktb_task', 'givetwo')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_task') . ' ADD `givetwo` decimal(10,2) unsigned NOT NULL DEFAULT \'0.00\' COMMENT \'上上级\';');
        }
        if (!pdo_fieldexists('zofui_tasktb_task', 'givethree')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_task') . ' ADD `givethree` decimal(10,2) unsigned NOT NULL DEFAULT \'0.00\';');
        }
        if (!pdo_fieldexists('zofui_tasktb_tbtaked', 'givetwo')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_tbtaked') . ' ADD `givetwo` decimal(10,2) unsigned NOT NULL DEFAULT \'0.00\' COMMENT \'上上级\';');
        }
        if (!pdo_fieldexists('zofui_tasktb_tbtaked', 'givethree')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_tbtaked') . ' ADD `givethree` decimal(10,2) unsigned NOT NULL DEFAULT \'0.00\' COMMENT \'上上上级\';');
        }
        if (!pdo_fieldexists('zofui_tasktb_tbtask', 'giveparent')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_tbtask') . ' ADD `giveparent` decimal(10,2) unsigned NOT NULL DEFAULT \'0.00\' COMMENT \'发任务上级提成\';');
        }
        if (!pdo_fieldexists('zofui_tasktb_tbtask', 'givetwo')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_tbtask') . ' ADD `givetwo` decimal(10,2) unsigned NOT NULL DEFAULT \'0.00\' COMMENT \'上上级\';');
        }
        if (!pdo_fieldexists('zofui_tasktb_tbtask', 'givethree')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_tbtask') . ' ADD `givethree` decimal(10,2) unsigned NOT NULL DEFAULT \'0.00\';');
        }
        if (!pdo_fieldexists('zofui_tasktb_usetasklog', 'givetwo')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_usetasklog') . ' ADD `givetwo` decimal(10,2) unsigned NOT NULL DEFAULT \'0.00\' COMMENT \'上上级\';');
        }
        if (!pdo_fieldexists('zofui_tasktb_usetasklog', 'givethree')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_usetasklog') . ' ADD `givethree` decimal(10,2) unsigned NOT NULL DEFAULT \'0.00\' COMMENT \'上上上级\';');
        }
        if (!pdo_fieldexists('zofui_tasktb_taked', 'adminadd')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_taked') . ' ADD `adminadd` decimal(10,2) NOT NULL DEFAULT \'0.00\' COMMENT \'管理员增加余额数值\';');
        }
        if (!pdo_fieldexists('zofui_tasktb_tbtask', 'isarealimit')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_tbtask') . ' ADD `isarealimit` tinyint(1) unsigned NOT NULL DEFAULT \'0\' COMMENT \'0不做区域限制 1限制到区县 2限制市\';');
        }
        if (!pdo_fieldexists('zofui_tasktb_tbtask', 'province')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_tbtask') . ' ADD `province` varchar(64) DEFAULT NULL;');
        }
        if (!pdo_fieldexists('zofui_tasktb_tbtask', 'city')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_tbtask') . ' ADD `city` varchar(64) DEFAULT NULL;');
        }
        if (!pdo_fieldexists('zofui_tasktb_tbtask', 'country')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_tbtask') . ' ADD `country` varchar(64) DEFAULT NULL;');
        }
        if (!pdo_fieldexists('zofui_task_user', 'level')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_task_user') . ' ADD `level` tinyint(1) unsigned NOT NULL DEFAULT \'0\' COMMENT \'0普通 1一级 2二级\';');
        }
        if (!pdo_fieldexists('zofui_task_user', 'utime')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_task_user') . ' ADD `utime` int(11) unsigned NOT NULL DEFAULT \'0\' COMMENT \'会员到期时间\';');
        }
        if (!pdo_fieldexists('zofui_tasktb_paylog', 'utype')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_paylog') . ' ADD `utype` tinyint(3) unsigned NOT NULL DEFAULT \'0\' COMMENT \'升级会员类型\';');
        }
        if (!pdo_fieldexists('zofui_tasktb_instruct', 'level')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_instruct') . ' ADD `level` text;');
        }
        if (!pdo_fieldexists('zofui_tasktb_tbtask', 'tkl')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_tbtask') . ' ADD `tkl` varchar(500) DEFAULT NULL COMMENT \'淘口令\';');
        }
        if (!pdo_fieldexists('zofui_tasktb_task', 'address')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_task') . ' ADD `address` varchar(255) DEFAULT NULL COMMENT \'任务地址\';');
        }
        pdo_query('CREATE TABLE IF NOT EXISTS ' . tablename('zofui_tasktb_vauth') . ' (
		  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `uniacid` int(11) unsigned NOT NULL DEFAULT \'0\',
		  `params` text,
		  PRIMARY KEY (`id`),
		  KEY `uniacid` (`uniacid`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;');
        if (!pdo_fieldexists('zofui_tasktb_tbtaked', 'gid')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_tbtaked') . ' ADD `gid` varchar(32) DEFAULT NULL;');
        }
        if (!pdo_fieldexists('zofui_tasktb_tbtask', 'gid')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_tbtask') . ' ADD `gid` varchar(32) DEFAULT NULL;');
        }
        if (!pdo_indexexists('zofui_tasktb_tbtaked', 'gid')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_tbtaked') . ' ADD INDEX gid(`gid`);');
        }
        pdo_query('CREATE TABLE IF NOT EXISTS ' . tablename('zofui_tasktb_tbform') . ' (
		  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `uniacid` int(10) unsigned NOT NULL DEFAULT \'0\',
		  `name` varchar(66) DEFAULT NULL,
		  `number` int(11) unsigned NOT NULL DEFAULT \'0\' COMMENT \'越大越前\',
		  `title` varchar(255) DEFAULT NULL,
		  `num` int(11) unsigned NOT NULL DEFAULT \'0\',
		  `money` decimal(10,2) unsigned NOT NULL DEFAULT \'0.00\',
		  `tbmoney` decimal(10,2) unsigned NOT NULL DEFAULT \'0.00\',
		  `step` varchar(2222) DEFAULT NULL,
		  `content` text,
		  `status` tinyint(1) unsigned NOT NULL DEFAULT \'0\' COMMENT \'0正常 1下架\',
		  PRIMARY KEY (`id`),
		  KEY `uniacid` (`uniacid`),
		  KEY `status` (`status`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT=\'\';');
        if (!pdo_fieldexists('zofui_tasktb_tabbar', 'color')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_tabbar') . ' ADD `color` varchar(22) DEFAULT NULL;');
        }
        if (!pdo_fieldexists('zofui_tasktb_tabbar', 'actcolor')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_tabbar') . ' ADD `actcolor` varchar(22) DEFAULT NULL;');
        }
        if (!pdo_fieldexists('zofui_tasktb_tabbar', 'actimg')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_tabbar') . ' ADD `actimg` varchar(555) DEFAULT NULL;');
        }
        if (!pdo_fieldexists('zofui_tasktb_task', 'readprice')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_task') . ' ADD `readprice` decimal(10,2) unsigned NOT NULL DEFAULT \'0.00\' COMMENT \'查看答案价格\';');
        }
        if (!pdo_fieldexists('zofui_tasktb_task', 'isread')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_task') . ' ADD `isread` tinyint(3) unsigned NOT NULL DEFAULT \'0\' COMMENT \'0不可看 1可看\';');
        }
        pdo_query('CREATE TABLE IF NOT EXISTS ' . tablename('zofui_tasktb_instructa') . ' (
		  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `uniacid` int(10) unsigned NOT NULL DEFAULT \'0\',
		  `content` text,
		  `type` tinyint(1) unsigned NOT NULL DEFAULT \'0\' COMMENT \'0答案\',
		  PRIMARY KEY (`id`),
		  KEY `uniacid` (`uniacid`),
		  KEY `type` (`type`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT=\'说明\';');
        if (!pdo_fieldexists('zofui_task_user', 'anwm')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_task_user') . ' ADD `anwm` decimal(10,2) unsigned NOT NULL DEFAULT \'0.00\' COMMENT \'累计付费阅读\';');
        }
        pdo_query('CREATE TABLE IF NOT EXISTS ' . tablename('zofui_tasktb_anwbox') . ' (
		  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `uniacid` int(10) unsigned NOT NULL DEFAULT \'0\',
		  `uid` int(11) unsigned NOT NULL DEFAULT \'0\' COMMENT \'会员id\',
		  `taskid` int(11) unsigned NOT NULL DEFAULT \'0\' COMMENT \'任务id\',
		  `readid` int(11) unsigned NOT NULL DEFAULT \'0\',
		  `money` decimal(10,2) unsigned NOT NULL DEFAULT \'0.00\' COMMENT \'金额\',
		  `endtime` int(11) unsigned NOT NULL DEFAULT \'0\' COMMENT \'结束时间\',
		  `status` tinyint(1) unsigned NOT NULL DEFAULT \'0\' COMMENT \'0未领取 1已领取\',
		  `createtime` int(11) unsigned NOT NULL DEFAULT \'0\',
		  `gettime` int(11) unsigned NOT NULL DEFAULT \'0\' COMMENT \'领取时间\',
		  PRIMARY KEY (`id`),
		  KEY `uniacid` (`uniacid`),
		  KEY `uid` (`uid`),
		  KEY `taskdi` (`taskid`),
		  KEY `endtime` (`endtime`),
		  KEY `readid` (`readid`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;');
        pdo_query('CREATE TABLE IF NOT EXISTS ' . tablename('zofui_tasktb_anwgeted') . ' (
		  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `uniacid` int(10) unsigned NOT NULL DEFAULT \'0\',
		  `uid` int(11) unsigned NOT NULL DEFAULT \'0\' COMMENT \'会员id\',
		  `createtime` int(11) unsigned NOT NULL DEFAULT \'0\',
		  `money` decimal(10,2) unsigned NOT NULL DEFAULT \'0.00\',
		  `thisday` int(11) unsigned NOT NULL DEFAULT \'0\' COMMENT \'回馈的日期\',
		  PRIMARY KEY (`id`),
		  KEY `uniacid` (`uniacid`),
		  KEY `uid` (`uid`),
		  KEY `createtime` (`createtime`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;');
        pdo_query('CREATE TABLE IF NOT EXISTS ' . tablename('zofui_tasktb_anwread') . ' (
		  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `uniacid` int(10) unsigned NOT NULL DEFAULT \'0\',
		  `uid` int(11) unsigned NOT NULL DEFAULT \'0\' COMMENT \'会员uid\',
		  `taskid` int(11) unsigned NOT NULL DEFAULT \'0\' COMMENT \'任务id\',
		  `createtime` int(11) unsigned NOT NULL DEFAULT \'0\' COMMENT \'创建时间\',
		  `cost` decimal(10,2) unsigned NOT NULL DEFAULT \'0.00\' COMMENT \'花费\',
		  `endtime` int(11) unsigned NOT NULL DEFAULT \'0\' COMMENT \'到期时间\',
		  `puberfee` decimal(10,2) unsigned NOT NULL DEFAULT \'0.00\' COMMENT \'发布者的\',
		  `lrfee` decimal(10,2) unsigned NOT NULL DEFAULT \'0.00\' COMMENT \'系统利润\',
		  `boxfee` decimal(10,2) unsigned NOT NULL DEFAULT \'0.00\' COMMENT \'宝箱\',
		  `sysfee` decimal(10,2) unsigned NOT NULL DEFAULT \'0.00\' COMMENT \'系统成本\',
		  `isshared` tinyint(1) unsigned NOT NULL DEFAULT \'0\' COMMENT \'是否已分成宝箱 0未 1已分\',
		  `iscountbox` tinyint(1) unsigned NOT NULL DEFAULT \'0\' COMMENT \'0未计算宝箱 1已计算宝箱\',
		  PRIMARY KEY (`id`),
		  KEY `uniacid` (`uniacid`),
		  KEY `uid` (`uid`),
		  KEY `taskid` (`taskid`),
		  KEY `createtime` (`createtime`),
		  KEY `iscountbox` (`iscountbox`),
		  KEY `isshared` (`isshared`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;');
        if (!pdo_fieldexists('zofui_task_user', 'pp')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_task_user') . ' ADD `pp` int(11) unsigned NOT NULL DEFAULT \'0\' COMMENT \'上上级\';');
        }
        if (!pdo_fieldexists('zofui_task_user', 'ppp')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_task_user') . ' ADD `ppp` int(11) unsigned NOT NULL DEFAULT \'0\' COMMENT \'上上级\';');
        }
        if (!pdo_indexexists('zofui_task_user', 'pp')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_task_user') . ' ADD INDEX pp(`pp`);');
        }
        if (!pdo_indexexists('zofui_task_user', 'ppp')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_task_user') . ' ADD INDEX ppp(`ppp`);');
        }
        if (!pdo_fieldexists('zofui_task_user', 'yinp')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_task_user') . ' ADD `yinp` decimal(10,2) unsigned NOT NULL DEFAULT \'0.00\';');
        }
        if (!pdo_fieldexists('zofui_task_user', 'baoshi')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_task_user') . ' ADD `baoshi` decimal(10,2) unsigned NOT NULL DEFAULT \'0.00\';');
        }
        pdo_query('CREATE TABLE IF NOT EXISTS ' . tablename('zofui_tasktb_groupbs') . ' (
	  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `uniacid` int(10) unsigned NOT NULL DEFAULT \'0\',
	  `createtime` int(11) unsigned NOT NULL DEFAULT \'0\',
	  `uid` int(11) unsigned NOT NULL DEFAULT \'0\',
	  `money` decimal(10,2) unsigned NOT NULL DEFAULT \'0.00\',
	  `baoshi` decimal(10,2) unsigned NOT NULL DEFAULT \'0.00\',
	  PRIMARY KEY (`id`),
	  KEY `uniacid` (`uniacid`),
	  KEY `createtime` (`createtime`),
	  KEY `uid` (`uid`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8;');
        pdo_query('CREATE TABLE IF NOT EXISTS ' . tablename('zofui_tasktb_group') . ' (
	  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `uniacid` int(10) unsigned NOT NULL DEFAULT \'0\',
	  `createtime` int(11) unsigned NOT NULL DEFAULT \'0\',
	  `mem` int(11) unsigned NOT NULL DEFAULT \'0\',
	  `totalcost` decimal(10,2) unsigned NOT NULL DEFAULT \'0.00\' COMMENT \'总计花费\',
	  `totalback` decimal(10,2) unsigned NOT NULL DEFAULT \'0.00\' COMMENT \'总计返回\',
	  `endtime` int(11) unsigned NOT NULL DEFAULT \'0\' COMMENT \'结束时间\',
	  `status` tinyint(1) unsigned NOT NULL DEFAULT \'0\' COMMENT \'0正常 1已结束\',
	  PRIMARY KEY (`id`),
	  KEY `uniacid` (`uniacid`),
	  KEY `createtime` (`createtime`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8;');
        pdo_query('CREATE TABLE IF NOT EXISTS ' . tablename('zofui_tasktb_groupds') . ' (
	  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `uniacid` int(10) unsigned NOT NULL DEFAULT \'0\',
	  `createtime` int(11) unsigned NOT NULL DEFAULT \'0\',
	  `uid` int(11) unsigned NOT NULL DEFAULT \'0\',
	  `takedid` int(11) unsigned NOT NULL DEFAULT \'0\',
	  `taskid` int(11) unsigned NOT NULL DEFAULT \'0\',
	  `yinp` decimal(10,2) unsigned NOT NULL DEFAULT \'0.00\',
	  `givedser` decimal(10,2) unsigned NOT NULL DEFAULT \'0.00\' COMMENT \'给打赏者的\',
	  `givedsed` decimal(10,2) unsigned NOT NULL DEFAULT \'0.00\' COMMENT \'给被打赏者的\',
	  PRIMARY KEY (`id`),
	  KEY `uniacid` (`uniacid`),
	  KEY `createtime` (`createtime`),
	  KEY `uid` (`uid`),
	  KEY `takedid` (`takedid`),
	  KEY `taskid` (`taskid`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8;');
        pdo_query('CREATE TABLE IF NOT EXISTS ' . tablename('zofui_tasktb_grouplog') . ' (
	  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `uniacid` int(10) unsigned NOT NULL DEFAULT \'0\',
	  `createtime` int(11) unsigned NOT NULL DEFAULT \'0\',
	  `uid` int(11) unsigned NOT NULL DEFAULT \'0\',
	  `gid` int(11) unsigned NOT NULL DEFAULT \'0\',
	  `cost` decimal(10,2) unsigned NOT NULL DEFAULT \'0.00\',
	  `geted` decimal(10,2) unsigned NOT NULL DEFAULT \'0.00\' COMMENT \'最后活动\',
	  `status` tinyint(1) unsigned NOT NULL DEFAULT \'0\' COMMENT \'0未发放 1已发放\',
	  `endtime` int(11) unsigned NOT NULL DEFAULT \'0\',
	  PRIMARY KEY (`id`),
	  KEY `uniacid` (`uniacid`),
	  KEY `createtime` (`createtime`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8;');
        if (!pdo_fieldexists('zofui_tasktb_taked', 'ds')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_taked') . ' ADD `ds` decimal(10,2) unsigned NOT NULL DEFAULT \'0.00\';');
        }
        if (!pdo_fieldexists('zofui_tasktb_tasksort', 'other')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_tasksort') . ' ADD `other` mediumtext;');
        }
        pdo_query('CREATE TABLE IF NOT EXISTS ' . tablename('zofui_tasktb_sign') . ' (
	  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `uniacid` int(10) unsigned NOT NULL DEFAULT \'0\',
	  `createtime` int(11) unsigned NOT NULL DEFAULT \'0\',
	  `uid` int(11) unsigned NOT NULL DEFAULT \'0\' COMMENT \'uid\',
	  `day` varchar(22) DEFAULT NULL,
	  `give` decimal(10,2) unsigned NOT NULL DEFAULT \'0.00\',
	  `flag` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
	  PRIMARY KEY (`id`),
	  KEY `uniacid` (`uniacid`),
	  KEY `createtime` (`createtime`),
	  KEY `uid` (`uid`),
	  KEY `day` (`day`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8;');
        if (!pdo_fieldexists('zofui_tasktb_tbtaked', 'gived')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_tbtaked') . ' ADD `gived` decimal(10,2) unsigned NOT NULL DEFAULT \'0.00\' COMMENT \'已经发放的金额\';');
        }
        if (!pdo_fieldexists('zofui_tasktb_tbtaked', 'iscangive')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_tbtaked') . ' ADD `iscangive` tinyint(1) unsigned NOT NULL DEFAULT \'0\' COMMENT \'0不可发佣金 1可发佣金\';');
        }
        if (!pdo_fieldexists('zofui_tasktb_tbremind', 'type')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_tbremind') . ' ADD `type` tinyint(1) unsigned NOT NULL DEFAULT \'0\' COMMENT \'0提醒 1发佣金\';');
        }
        pdo_query('CREATE TABLE IF NOT EXISTS ' . tablename('zofui_tasktb_imess') . ' (
	  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `uniacid` int(10) unsigned NOT NULL DEFAULT \'0\',
	  `openid` varchar(64) DEFAULT NULL,
	  `uid` int(11) unsigned NOT NULL DEFAULT \'0\' COMMENT \'member uid\',
	  `type` varchar(120) NOT NULL DEFAULT \'0\' COMMENT \'类型\',
	  `targetid` int(11) unsigned NOT NULL DEFAULT \'0\',
	  `remark` varchar(255) DEFAULT \'\' COMMENT \'备注\',
	  `status` tinyint(1) unsigned NOT NULL DEFAULT \'0\' COMMENT \'0未读 1已读\',
	  `url` varchar(255) DEFAULT NULL,
	  `createtime` int(11) unsigned NOT NULL DEFAULT \'0\',
	  `content` varchar(2000) DEFAULT NULL,
	  PRIMARY KEY (`id`),
	  KEY `uniacid` (`uniacid`),
	  KEY `openid` (`openid`),
	  KEY `uid` (`uid`),
	  KEY `type` (`type`),
	  KEY `targetid` (`targetid`),
	  KEY `status` (`status`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8;');
        pdo_query('CREATE TABLE IF NOT EXISTS ' . tablename('zofui_tasktb_usersort') . ' (
	  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	  `uid` int(11) unsigned NOT NULL DEFAULT \'0\',
	  `sortid` int(11) unsigned NOT NULL DEFAULT \'0\',
	  `uniacid` int(11) unsigned NOT NULL DEFAULT \'0\',
	  PRIMARY KEY (`id`),
	  KEY `uid` (`uid`),
	  KEY `sortid` (`sortid`),
	  KEY `uniacid` (`uniacid`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8;');
        if (!pdo_fieldexists('zofui_task_user', 'verifyend')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_task_user') . ' ADD `verifyend` int(11) unsigned NOT NULL DEFAULT \'0\' COMMENT \'认证结束\';');
        }
        if (!pdo_fieldexists('zofui_task_user', 'iscostauth')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_task_user') . ' ADD `iscostauth` tinyint(1) unsigned NOT NULL DEFAULT \'0\' COMMENT \'0未付费认证 1已付\';');
        }
        if (!pdo_fieldexists('zofui_tasktb_slider', 'isguy')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_slider') . ' ADD `isguy` tinyint(1) unsigned NOT NULL DEFAULT \'0\' COMMENT \'找人页面\';');
        }
        if (!pdo_fieldexists('zofui_tasktb_slider', 'dayy')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_slider') . ' ADD `dayy` int(11) unsigned NOT NULL DEFAULT \'0\'');
        }
        if (!pdo_fieldexists('zofui_tasktb_tbtask', 'address')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_tbtask') . ' ADD `address` varchar(255) DEFAULT NULL COMMENT \'任务地址\';');
        }
        if (!pdo_fieldexists('zofui_tasktb_task', 'levellim')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_task') . ' ADD `levellim` tinyint(1) unsigned NOT NULL DEFAULT \'0\' COMMENT \'0不限制 1一级可接 2二级可接 3二三级可接\';');
        }
        if (!pdo_indexexists('zofui_tasktb_task', 'limitnum')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_task') . ' ADD INDEX limitnum(`limitnum`);');
        }
        if (!pdo_indexexists('zofui_tasktb_task', 'iscount')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_task') . ' ADD INDEX iscount(`iscount`);');
        }
        if (!pdo_indexexists('zofui_tasktb_task', 'type')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_task') . ' ADD INDEX type(`type`);');
        }
        if (!pdo_indexexists('zofui_tasktb_task', 'isempty')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_task') . ' ADD INDEX isempty(`isempty`);');
        }
        if (!pdo_indexexists('zofui_tasktb_task', 'ispause')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_task') . ' ADD INDEX ispause(`ispause`);');
        }
        if (!pdo_indexexists('zofui_tasktb_taked', 'status')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_taked') . ' ADD INDEX status(`status`);');
        }
        if (!pdo_indexexists('zofui_tasktb_task', 'levellim')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_task') . ' ADD INDEX levellim(`levellim`);');
        }
        pdo_query('CREATE TABLE IF NOT EXISTS ' . tablename('zofui_tasktb_banner') . ' (
	  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `uniacid` int(11) unsigned NOT NULL DEFAULT \'0\',
	  `img` varchar(500) DEFAULT NULL,
	  `number` int(11) unsigned NOT NULL DEFAULT \'0\' COMMENT \'排序，越大越前\',
	  `url` varchar(500) DEFAULT NULL,
	  `name` varchar(32) NOT NULL DEFAULT \'0\' COMMENT \'名称\',
	  PRIMARY KEY (`id`),
	  KEY `index` (`uniacid`,`number`),
	  KEY `uniacid` (`uniacid`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8;');
        if (!pdo_fieldexists('zofui_tasktb_imess', 'isbig')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_imess') . ' ADD `isbig` tinyint(1) unsigned NOT NULL DEFAULT \'0\' COMMENT \'0弹窗提醒 1不弹窗提醒\';');
        }
        if (!pdo_indexexists('zofui_tasktb_imess', 'isbig')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_imess') . ' ADD INDEX isbig(`isbig`);');
        }
        if (!pdo_indexexists('zofui_tasktb_imess', 'createtime')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_imess') . ' ADD INDEX createtime(`createtime`);');
        }
        if (!pdo_indexexists('zofui_tasktb_task', 'counttime')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_task') . ' ADD INDEX counttime(`counttime`);');
        }
        if (!pdo_fieldexists('zofui_tasktb_task', 'falsepuber')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_task') . ' ADD `falsepuber` int(11) unsigned NOT NULL DEFAULT \'0\' COMMENT \'虚拟id\';');
        }
        if (!pdo_indexexists('zofui_tasktb_task', 'falsepuber')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_task') . ' ADD INDEX falsepuber(`falsepuber`);');
        }
        pdo_query('CREATE TABLE IF NOT EXISTS ' . tablename('zofui_tasktb_puber') . ' (
	  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `uniacid` int(11) unsigned NOT NULL DEFAULT \'0\',
	  `headimg` varchar(500) DEFAULT NULL,
	  `nickname` varchar(32) DEFAULT NULL,
	  `falsepub` int(11) unsigned NOT NULL DEFAULT \'0\',
	  `falsetake` int(11) unsigned NOT NULL DEFAULT \'0\',
	  `falsedep` int(11) unsigned NOT NULL DEFAULT \'0\',
	  `pub` int(11) unsigned NOT NULL DEFAULT \'0\' COMMENT \'发布量\',
	  `take` int(11) unsigned NOT NULL DEFAULT \'0\' COMMENT \'采纳量\',
	  `cost` decimal(10,2) unsigned NOT NULL DEFAULT \'0.00\' COMMENT \'支出\',
	  `pubnum` int(11) unsigned NOT NULL DEFAULT \'0\' COMMENT \'发布任务个数\',
	  PRIMARY KEY (`id`),
	  KEY `uniacid` (`uniacid`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8;');
        if (!pdo_fieldexists('zofui_tasktb_taked', 'type')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_taked') . ' ADD `type` tinyint(1) unsigned NOT NULL DEFAULT \'0\' COMMENT \'0正常 1虚假的\';');
        }
        if (!pdo_fieldexists('zofui_tasktb_taked', 'nick')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_taked') . ' ADD `nick` varchar(32) DEFAULT NULL COMMENT \'虚假昵称\';');
        }
        if (!pdo_fieldexists('zofui_tasktb_taked', 'headimg')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_taked') . ' ADD `headimg` varchar(555) DEFAULT NULL COMMENT \'虚假头像\';');
        }
        if (!pdo_fieldexists('zofui_tasktb_taked', 'isfalse')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_taked') . ' ADD `isfalse` tinyint(1) unsigned NOT NULL DEFAULT \'0\' COMMENT \'0正常 1虚假的\';');
        }
        if (!pdo_fieldexists('zofui_tasktb_taked', 'falseuid')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_taked') . ' ADD `falseuid` int(11) unsigned NOT NULL DEFAULT \'0\' COMMENT \'虚拟会员id\';');
        }
        if (!pdo_fieldexists('zofui_tasktb_task', 'falsenum')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_task') . ' ADD `falsenum` int(11) unsigned NOT NULL DEFAULT \'0\' COMMENT \'虚拟数量\';');
        }
        if (!pdo_indexexists('zofui_tasktb_taked', 'type')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_taked') . ' ADD INDEX type(`type`);');
        }
        if (!pdo_fieldexists('zofui_tasktb_taked', 'isremind')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_taked') . ' ADD `isremind` tinyint(1) unsigned NOT NULL DEFAULT \'0\' COMMENT \'0未提醒 1已提醒\';');
        }
        if (!pdo_indexexists('zofui_tasktb_taked', 'isremind')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_taked') . ' ADD INDEX isremind(`isremind`);');
        }
        if (!pdo_fieldexists('zofui_tasktb_task', 'falsepuber')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_task') . ' ADD `falsepuber` int(11) unsigned NOT NULL DEFAULT \'0\' COMMENT \'虚拟id\';');
        }
        if (!pdo_fieldexists('zofui_tasktb_authform', 'useric')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_authform') . ' ADD `useric` varchar(2500) DEFAULT NULL COMMENT \'虚假昵称\';');
        }
        if (!pdo_fieldexists('zofui_tasktb_task', 'useric')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_task') . ' ADD `useric` varchar(200) DEFAULT NULL COMMENT \'虚假昵称\';');
        }
        pdo_query('CREATE TABLE IF NOT EXISTS ' . tablename('zofui_tasktb_useric') . ' (
	  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `uniacid` int(10) unsigned NOT NULL DEFAULT \'0\',
	  `name` varchar(32) DEFAULT NULL,
	  `number` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
	  PRIMARY KEY (`id`),
	  KEY `uniacid` (`uniacid`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT=\'支付记录\';');
        pdo_query('CREATE TABLE IF NOT EXISTS ' . tablename('zofui_tasktb_userics') . ' (
	  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `uniacid` int(10) unsigned NOT NULL DEFAULT \'0\',
	  `uid` int(11) unsigned NOT NULL DEFAULT \'0\' COMMENT \'会员uid\',
	  `icid` int(11) unsigned NOT NULL DEFAULT \'0\' COMMENT \'标签id\',
	  PRIMARY KEY (`id`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT=\'支付记录\';');
        if (!pdo_fieldexists('zofui_task_user', 'qq')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_task_user') . ' ADD `qq` varchar(20) DEFAULT NULL COMMENT \'\';');
        }
        if (!pdo_fieldexists('zofui_tasktb_task', 'mark')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_task') . ' ADD `mark` varchar(200) DEFAULT NULL COMMENT \'\';');
        }
        if (!pdo_fieldexists('zofui_tasktb_task', 'idcode')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_task') . ' ADD `idcode` varchar(15) DEFAULT NULL COMMENT \'\';');
        }
        if (!pdo_indexexists('zofui_tasktb_task', 'idcode')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_task') . ' ADD INDEX idcode(`idcode`);');
        }
        pdo_query('CREATE TABLE IF NOT EXISTS ' . tablename('zofui_tasktb_codekey') . ' (
	  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	  `key` varchar(33) DEFAULT NULL,
	  PRIMARY KEY (`id`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8;');
        if (!pdo_fieldexists('zofui_tasktb_banner', 'desc')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_banner') . ' ADD `desc` varchar(120) DEFAULT NULL COMMENT \'\';');
        }
        if (!pdo_fieldexists('zofui_tasktb_task', 'headimg')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_task') . ' ADD `headimg` varchar(255) DEFAULT NULL COMMENT \'\';');
        }
        pdo_query('CREATE TABLE IF NOT EXISTS ' . tablename('zofui_tasktb_readtask') . ' (
	  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `type` tinyint(3) unsigned NOT NULL DEFAULT \'0\',
	  `uid` int(11) unsigned NOT NULL DEFAULT \'0\',
	  `tid` int(11) unsigned NOT NULL DEFAULT \'0\',
	  PRIMARY KEY (`id`),
	  KEY `type` (`type`),
	  KEY `uid` (`uid`),
	  KEY `tid` (`tid`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8;');
        if (!pdo_fieldexists('zofui_tasktb_task', 'gid')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_task') . ' ADD `gid` varchar(32) DEFAULT NULL COMMENT \'\';');
        }
        if (!pdo_indexexists('zofui_tasktb_task', 'gid')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_task') . ' ADD INDEX gid(`gid`);');
        }
        if (!pdo_fieldexists('zofui_tasktb_taked', 'gid')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_taked') . ' ADD `gid` varchar(32) DEFAULT NULL COMMENT \'\';');
        }
        if (!pdo_indexexists('zofui_tasktb_taked', 'gid')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_taked') . ' ADD INDEX gid(`gid`);');
        }
        if (!pdo_fieldexists('zofui_tasktb_task', 'stepid')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_task') . ' ADD `stepid` int(11) unsigned NOT NULL DEFAULT \'0\';');
        }
        pdo_query('CREATE TABLE IF NOT EXISTS ' . tablename('zofui_tasktb_step') . ' (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `step` text,
	  `uniacid` int(11) unsigned NOT NULL DEFAULT \'0\',
	  `istemp` tinyint(1) unsigned NOT NULL DEFAULT \'0\' COMMENT \'0不是模板 1模板\',
	  `name` varchar(122) DEFAULT NULL,
	  PRIMARY KEY (`id`),
	  KEY `istemp` (`istemp`),
	  KEY `uniacid` (`uniacid`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8;');
        if (!pdo_indexexists('zofui_tasktb_task', 'status')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_task') . ' ADD INDEX status(`status`);');
        }
        if (!pdo_fieldexists('zofui_tasktb_imess', 'istop')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_imess') . ' ADD `istop` tinyint(1) unsigned NOT NULL DEFAULT \'0\' COMMENT \'0\';');
        }
        if (!pdo_indexexists('zofui_tasktb_imess', 'istop')) {
            pdo_query('ALTER TABLE ' . tablename('zofui_tasktb_imess') . ' ADD INDEX istop(`istop`);');
        }
        include $this->template('web/' . $_W['mtemp'] . '/setting');
    }
}