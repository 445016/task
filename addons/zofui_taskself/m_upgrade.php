<?php 
pdo_query("CREATE TABLE IF NOT EXISTS `ims_zofui_task_user` (
`id` int(11) NOT NULL  AUTO_INCREMENT,
`openid` varchar(50) NOT NULL,
`uid` int(10) NOT NULL,
`logintime` int(11) NOT NULL,
`status` tinyint(3) NOT NULL   COMMENT '默认0 正常 1认证（已缴纳保证金） 2拉黑',
`uniacid` int(10) NOT NULL,
`deposit` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '保证金',
`city` varchar(20) NOT NULL,
`mobile` varchar(15) NOT NULL   COMMENT '手机号码',
`qrcode` varchar(200) NOT NULL   COMMENT '微信二维码',
`pubnumber` int(10) NOT NULL   COMMENT '发布数量',
`acceptnumber` int(10) NOT NULL   COMMENT '采纳数量',
`creditscore` decimal(6,1) NOT NULL DEFAULT NULL DEFAULT '10.0'  COMMENT '信誉分数',
`guytype` tinyint(1) NOT NULL   COMMENT '人物类型，1是发任务的，2是接任务的',
`guydesc` varchar(200)    COMMENT '个人描述',
`guysort` int(5) NOT NULL   COMMENT '所属人物分类',
`contacttype` tinyint(1) NOT NULL   COMMENT '联系方式 0没有联系方式，1手机 2微信二维码 3两者都支持',
`replynumber` int(9) NOT NULL   COMMENT '回复数量',
`acceptednumber` int(9) NOT NULL   COMMENT '被采纳数量',
`nickname` varchar(64)    COMMENT '淘宝版本里用到 昵称',
`headimgurl` varchar(300)    COMMENT '淘宝版本里用到 头像',
`sex` tinyint(1) NOT NULL   COMMENT '淘宝版本里用到 1男 2女',
`activity` int(11) NOT NULL   COMMENT '淘宝版本里用到 活跃度',
`uptime` int(11) NOT NULL   COMMENT '淘宝版本里用到 更新活跃度时间',
`ispub` tinyint(1) NOT NULL   COMMENT '淘宝版本里用到 0不是发任务的 1是',
`isacc` tinyint(1) NOT NULL   COMMENT '淘宝版本里用到 0不是接任务的 1是',
`conweixin` tinyint(1) NOT NULL   COMMENT '淘宝版本里用到 0不开启微信联系 1 开启',
`conmobile` tinyint(1) NOT NULL   COMMENT '淘宝版本里用到 0不开启电话联系 1开启',
`parent` int(11) NOT NULL   COMMENT '淘宝版本里用到 上级id',
`limitnum` int(5) NOT NULL   COMMENT '回复次数限制',
`account` varchar(20)    COMMENT '账户，绑定电话号码',
`createtime` int(11) NOT NULL   COMMENT '创建时间',
`giveparent` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '上级获得提成总额',
`authopenid` varchar(64)    COMMENT '被借权公众号的openid',
`alipayname` varchar(32)    COMMENT '支付宝姓名',
`alipay` varchar(64)    COMMENT '支付宝账户',
`verifystatus` tinyint(1) NOT NULL   COMMENT '0未提交认证 1已提交认证 2认证通过 3认证不通过',
`verifyform` text()    COMMENT '额外提交的表单',
`mark` varchar(200)    COMMENT '标记备注内容',
`limitday` int(5) NOT NULL   COMMENT '限制抢任务的天数',
`tbpub` int(11) NOT NULL   COMMENT '发布担保任务数量',
`tbsuccess` int(11) NOT NULL   COMMENT '采纳担保任务数量',
`tbtake` int(11) NOT NULL   COMMENT '抢到担保任务数量',
`tbcom` int(11) NOT NULL   COMMENT '完成担保任务数量',
`payqr` varchar(500),
`uselimitnum` int(11) NOT NULL   COMMENT '限制抢试用任务数量',
`uselimitday` int(11) NOT NULL   COMMENT '限制抢试用任务数量',
`givetwo` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '累计给上上级的提成',
`givethree` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '累计给上上级的提成',
`level` tinyint(1) NOT NULL   COMMENT '0普通 1一级 2二级',
`utime` int(11) NOT NULL   COMMENT '会员到期时间',
`anwm` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '累计付费阅读',
`yinp` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00',
`baoshi` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00',
`verifyend` int(11) NOT NULL   COMMENT '认证结束',
`iscostauth` tinyint(1) NOT NULL   COMMENT '0未付费认证 1已付',
`qq` varchar(20),
`pp` int(11) NOT NULL   COMMENT '上上级',
`ppp` int(11) NOT NULL   COMMENT '上上级',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_zofui_tasktb_ad` (
`id` int(10) NOT NULL  AUTO_INCREMENT,
`uniacid` int(10) NOT NULL,
`title` varchar(2000) NOT NULL   COMMENT '标题',
`number` int(10) NOT NULL   COMMENT '分类排序，越大越前',
`content` mediumtext()    COMMENT '广告内容',
`time` int(11) NOT NULL   COMMENT '发布时间',
`status` tinyint(1) NOT NULL   COMMENT '0显示 1下架',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_zofui_tasktb_anwbox` (
`id` int(11) NOT NULL  AUTO_INCREMENT,
`uniacid` int(10) NOT NULL,
`uid` int(11) NOT NULL   COMMENT '会员id',
`taskid` int(11) NOT NULL   COMMENT '任务id',
`readid` int(11) NOT NULL,
`money` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '金额',
`endtime` int(11) NOT NULL   COMMENT '结束时间',
`status` tinyint(1) NOT NULL   COMMENT '0未领取 1已领取',
`createtime` int(11) NOT NULL,
`gettime` int(11) NOT NULL   COMMENT '领取时间',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_zofui_tasktb_anwgeted` (
`id` int(11) NOT NULL  AUTO_INCREMENT,
`uniacid` int(10) NOT NULL,
`uid` int(11) NOT NULL   COMMENT '会员id',
`createtime` int(11) NOT NULL,
`money` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00',
`thisday` int(11) NOT NULL   COMMENT '回馈的日期',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_zofui_tasktb_anwread` (
`id` int(11) NOT NULL  AUTO_INCREMENT,
`uniacid` int(10) NOT NULL,
`uid` int(11) NOT NULL   COMMENT '会员uid',
`taskid` int(11) NOT NULL   COMMENT '任务id',
`createtime` int(11) NOT NULL   COMMENT '创建时间',
`cost` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '花费',
`endtime` int(11) NOT NULL   COMMENT '到期时间',
`puberfee` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '发布者的',
`lrfee` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '系统利润',
`boxfee` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '宝箱',
`sysfee` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '系统成本',
`isshared` tinyint(1) NOT NULL   COMMENT '是否已分成宝箱 0未 1已分',
`iscountbox` tinyint(1) NOT NULL   COMMENT '0未计算宝箱 1已计算宝箱',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_zofui_tasktb_authform` (
`id` int(10) NOT NULL  AUTO_INCREMENT,
`uniacid` int(10) NOT NULL,
`name` varchar(150)    COMMENT '表单名称',
`number` int(11) NOT NULL   COMMENT '排序序号',
`formtype` varchar(32)    COMMENT '类型',
`useric` varchar(2500)    COMMENT '虚假昵称',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_zofui_tasktb_banner` (
`id` int(11) NOT NULL  AUTO_INCREMENT,
`uniacid` int(11) NOT NULL,
`img` varchar(500),
`number` int(11) NOT NULL   COMMENT '排序，越大越前',
`url` varchar(500),
`name` varchar(32) NOT NULL   COMMENT '名称',
`desc` varchar(120),
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_zofui_tasktb_codekey` (
`id` int(10) NOT NULL  AUTO_INCREMENT,
`key` varchar(33),
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_zofui_tasktb_complain` (
`id` int(11) NOT NULL  AUTO_INCREMENT,
`uniacid` int(10) NOT NULL,
`content` varchar(1500),
`images` varchar(2000),
`time` int(11) NOT NULL   COMMENT '时间',
`openid` varchar(64),
`taskid` int(11) NOT NULL   COMMENT '任务id',
`status` tinyint(1) NOT NULL   COMMENT '0未处理 1已处理',
`userid` int(11) NOT NULL   COMMENT '会员id',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_zofui_tasktb_continue` (
`id` int(11) NOT NULL  AUTO_INCREMENT,
`uniacid` int(10) NOT NULL,
`money` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '连续奖励金额',
`totalmoney` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '奖励的总金额',
`totalnum` int(11) NOT NULL   COMMENT '数量',
`backmoney` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '结算退回金额',
`prizemoney` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '已给的奖励金额',
`isback` tinyint(1) NOT NULL   COMMENT '0未结算 1已结算',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_zofui_tasktb_draw` (
`id` int(11) NOT NULL  AUTO_INCREMENT,
`uniacid` int(11) NOT NULL,
`openid` varchar(64) NOT NULL,
`money` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '金额',
`status` tinyint(1) NOT NULL   COMMENT '状态 0待确认 1已支付 2退回 3拒绝',
`createtime` int(11) NOT NULL   COMMENT '创建时间',
`dealtime` int(11) NOT NULL   COMMENT '处理时间',
`backreason` varchar(125) NOT NULL   COMMENT '退回理由',
`type` tinyint(1) NOT NULL   COMMENT '1余额 2保证金',
`refusereason` varchar(255)    COMMENT '拒绝支付理由',
`paytype` tinyint(1) NOT NULL   COMMENT '0微信支付 1支付宝支付',
`alipayname` varchar(32),
`alipay` varchar(64),
`userid` int(11) NOT NULL   COMMENT '会员id',
`payno` varchar(64),
`server` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '服务费',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_zofui_tasktb_group` (
`id` int(11) NOT NULL  AUTO_INCREMENT,
`uniacid` int(10) NOT NULL,
`createtime` int(11) NOT NULL,
`mem` int(11) NOT NULL,
`totalcost` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '总计花费',
`totalback` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '总计返回',
`endtime` int(11) NOT NULL   COMMENT '结束时间',
`status` tinyint(1) NOT NULL   COMMENT '0正常 1已结束',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_zofui_tasktb_groupbs` (
`id` int(11) NOT NULL  AUTO_INCREMENT,
`uniacid` int(10) NOT NULL,
`createtime` int(11) NOT NULL,
`uid` int(11) NOT NULL,
`money` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00',
`baoshi` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_zofui_tasktb_groupds` (
`id` int(11) NOT NULL  AUTO_INCREMENT,
`uniacid` int(10) NOT NULL,
`createtime` int(11) NOT NULL,
`uid` int(11) NOT NULL,
`takedid` int(11) NOT NULL,
`taskid` int(11) NOT NULL,
`yinp` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00',
`givedser` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '给打赏者的',
`givedsed` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '给被打赏者的',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_zofui_tasktb_grouplog` (
`id` int(11) NOT NULL  AUTO_INCREMENT,
`uniacid` int(10) NOT NULL,
`createtime` int(11) NOT NULL,
`uid` int(11) NOT NULL,
`gid` int(11) NOT NULL,
`cost` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00',
`geted` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '最后活动',
`status` tinyint(1) NOT NULL   COMMENT '0未发放 1已发放',
`endtime` int(11) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_zofui_tasktb_guysort` (
`id` int(11) NOT NULL  AUTO_INCREMENT,
`uniacid` int(11) NOT NULL,
`img` varchar(500),
`number` int(11) NOT NULL   COMMENT '排序，越大越前',
`name` varchar(64),
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_zofui_tasktb_imess` (
`id` int(11) NOT NULL  AUTO_INCREMENT,
`uniacid` int(10) NOT NULL,
`openid` varchar(64),
`uid` int(11) NOT NULL   COMMENT 'member uid',
`type` varchar(120) NOT NULL   COMMENT '类型',
`targetid` int(11) NOT NULL,
`remark` varchar(255)    COMMENT '备注',
`status` tinyint(1) NOT NULL   COMMENT '0未读 1已读',
`url` varchar(255),
`createtime` int(11) NOT NULL,
`content` varchar(2000),
`isbig` tinyint(1) NOT NULL   COMMENT '0弹窗提醒 1不弹窗提醒',
`istop` tinyint(1) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_zofui_tasktb_instruct` (
`id` int(11) NOT NULL  AUTO_INCREMENT,
`uniacid` int(10) NOT NULL,
`down` text(),
`set` text(),
`level` text(),
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_zofui_tasktb_instructa` (
`id` int(11) NOT NULL  AUTO_INCREMENT,
`uniacid` int(10) NOT NULL,
`content` text(),
`type` tinyint(1) NOT NULL   COMMENT '0答案',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_zofui_tasktb_mess` (
`id` int(11) NOT NULL  AUTO_INCREMENT,
`uniacid` int(10) NOT NULL,
`openid` varchar(64),
`taked` tinyint(1) NOT NULL   COMMENT '任务被回复通知 0开启 1关闭',
`messaged` tinyint(1) NOT NULL   COMMENT '被留言提问',
`count` tinyint(1) NOT NULL   COMMENT '任务被结算',
`reply` tinyint(1) NOT NULL   COMMENT '回复被处理',
`getmessage` tinyint(1) NOT NULL   COMMENT '留言被回复',
`getpri` tinyint(1) NOT NULL   COMMENT '获得私包任务',
`newdown` tinyint(1) NOT NULL   COMMENT '新增小弟',
`downmoney` tinyint(1) NOT NULL   COMMENT '小弟提成',
`downact` tinyint(1) NOT NULL   COMMENT '获赠活跃度通知',
`usesuborder` tinyint(1) NOT NULL   COMMENT '私包任务 提交订单内容',
`useaddcontent` tinyint(1) NOT NULL   COMMENT '使用任务补充内容',
`newtask` tinyint(1) NOT NULL   COMMENT '新任务通知',
`userid` int(11) NOT NULL   COMMENT '会员id',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_zofui_tasktb_message` (
`id` int(11) NOT NULL  AUTO_INCREMENT,
`uniacid` int(10) NOT NULL,
`type` tinyint(1) NOT NULL,
`str` varchar(64),
`openid` varchar(64),
`userid` int(11) NOT NULL   COMMENT '会员id',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_zofui_tasktb_moneylog` (
`id` int(11) NOT NULL  AUTO_INCREMENT,
`uniacid` int(10) NOT NULL,
`openid` varchar(64) NOT NULL,
`taskid` int(10) NOT NULL   COMMENT '对应的任务id',
`money` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '金额',
`status` tinyint(3) NOT NULL   COMMENT '资金记录状态',
`time` int(10) NOT NULL   COMMENT '变化时间',
`type` tinyint(3) NOT NULL   COMMENT '资金记录类型 1任务资金，2平台使用费，3提现扣除余额，4任务完成收入 5私包任务支出 6私包任务被取消退回资金 7私包任务收益 8任务自动结束退回资金，9加急扣除，10管理员变动，11提现退回，12充值收入，13任务不通过退回',
`aftermoney` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '变动后余额',
`mtype` tinyint(1) NOT NULL   COMMENT '1余额记录 2保证金记录',
`userid` int(11) NOT NULL   COMMENT '会员id',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_zofui_tasktb_paylog` (
`id` int(11) NOT NULL  AUTO_INCREMENT,
`uniacid` int(10) NOT NULL,
`openid` varchar(64) NOT NULL,
`uid` int(10) NOT NULL,
`time` int(11) NOT NULL   COMMENT '支付或者创建时间',
`status` tinyint(1) NOT NULL   COMMENT '类型 0未支付  1已支付',
`type` tinyint(1) NOT NULL   COMMENT '类型 1任务保证金 2充值',
`fee` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '金额',
`orderid` varchar(64) NOT NULL   COMMENT '内部订单编号',
`uorderid` varchar(64) NOT NULL   COMMENT '微信订单编号',
`userid` int(11) NOT NULL   COMMENT '会员id',
`utype` tinyint(3) NOT NULL   COMMENT '升级会员类型',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_zofui_tasktb_privatetask` (
`id` int(11) NOT NULL  AUTO_INCREMENT,
`uniacid` int(10) NOT NULL,
`puber` varchar(64) NOT NULL   COMMENT '发起者',
`accepter` varchar(64),
`workeropenid` varchar(64) NOT NULL   COMMENT '任务执行者openid',
`bossopenid` varchar(64) NOT NULL   COMMENT '任务雇主openid',
`tasktitle` varchar(255) NOT NULL   COMMENT '任务内容',
`taskmoney` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '任务金额',
`workerserver` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '扣做任务者 平台使用费',
`images` varchar(1500) NOT NULL   COMMENT '任务图片',
`limittime` int(11) NOT NULL   COMMENT '限时，小时为单位',
`createtime` int(11) NOT NULL   COMMENT '创建时间',
`accepttime` int(11) NOT NULL   COMMENT '接受者处理时间',
`workerdealtime` int(11) NOT NULL   COMMENT '执行者提交完成.或取消时间',
`bossdealtime` int(11) NOT NULL   COMMENT '雇主确认或拒绝时间',
`complaintime` int(11) NOT NULL   COMMENT '雇员同意拒绝或投诉时间',
`admindealtime` int(11) NOT NULL   COMMENT '管理员对投诉处理时间',
`status` tinyint(2) NOT NULL   COMMENT '任务状态 0等待确认，1任务被拒绝，2执行中，3雇员提交完成待雇主确认， 4雇员主动取消执行任务，5雇员没有提交完成而自动取消，6雇主确认后任务完成，7任务完成效果不好被雇主拒绝，8任务被雇员同意拒绝而结束，9任务被雇员投诉阶段，10管理员处理后结束,11雇主没有确认完成任务自动确认完成，12待雇员接受拒绝或投诉状态而没有处理自动接受，13管理员判给雇员而结束，14管理员判给雇主而结束',
`type` tinyint(1) NOT NULL   COMMENT '类型，1索要的任务。2主动发给对方任务，2是已支付的',
`complainreason` varchar(200) NOT NULL   COMMENT '投诉内容',
`admindealresult` varchar(200) NOT NULL   COMMENT '管理员处理结果',
`refusereason` varchar(200) NOT NULL   COMMENT '雇主拒绝理由',
`overtime0` int(11) NOT NULL   COMMENT '待确认的任务自动取消时间',
`overtime2` int(11) NOT NULL   COMMENT '执行中任务自动结束时间',
`overtime3` int(11) NOT NULL   COMMENT '待雇主确认状态自动确认时间',
`overtime7` int(11) NOT NULL   COMMENT '雇员确认拒绝状态自动确认时间',
`completecontent` text()    COMMENT '回复的内容',
`bossserver` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '扣雇主服务费',
`isend` tinyint(1) NOT NULL   COMMENT '0未结束 1已结束',
`giveparent` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '上级奖励',
`pubuid` int(11) NOT NULL   COMMENT '会员id',
`acceptuid` int(11) NOT NULL   COMMENT '会员id',
`bossuid` int(11) NOT NULL   COMMENT '会员id',
`workeruid` int(11) NOT NULL   COMMENT '会员id',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_zofui_tasktb_puber` (
`id` int(11) NOT NULL  AUTO_INCREMENT,
`uniacid` int(11) NOT NULL,
`headimg` varchar(500),
`nickname` varchar(32),
`falsepub` int(11) NOT NULL,
`falsetake` int(11) NOT NULL,
`falsedep` int(11) NOT NULL,
`pub` int(11) NOT NULL   COMMENT '发布量',
`take` int(11) NOT NULL   COMMENT '采纳量',
`cost` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '支出',
`pubnum` int(11) NOT NULL   COMMENT '发布任务个数',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_zofui_tasktb_readtask` (
`id` int(11) NOT NULL  AUTO_INCREMENT,
`type` tinyint(3) NOT NULL,
`uid` int(11) NOT NULL,
`tid` int(11) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_zofui_tasktb_remindlog` (
`id` int(10) NOT NULL  AUTO_INCREMENT,
`uniacid` int(10) NOT NULL,
`createtime` int(11) NOT NULL   COMMENT '时间',
`takedid` int(11) NOT NULL   COMMENT '回复id',
`content` varchar(500)    COMMENT '内容',
`type` tinyint(1) NOT NULL   COMMENT '0前端发的 1后端发的',
`images` varchar(2500)    COMMENT '图片',
`mtype` tinyint(1) NOT NULL   COMMENT '0提醒 1补充的内容',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_zofui_tasktb_scan` (
`id` int(10) NOT NULL  AUTO_INCREMENT,
`uniacid` int(10) NOT NULL,
`times` int(11) NOT NULL   COMMENT '访问次数',
`pubed` int(11) NOT NULL   COMMENT '已发布数量',
`comed` int(11) NOT NULL   COMMENT '已完成',
`commpubed` int(11) NOT NULL   COMMENT '普通任务发布数量',
`commcomed` int(11) NOT NULL   COMMENT '普通任务完成数量',
`privatepubed` int(11) NOT NULL   COMMENT '私包任务发布数量',
`privatecomed` int(11) NOT NULL   COMMENT '私包任务完成数量',
`usepubed` int(11) NOT NULL   COMMENT '试用任务发布数量',
`usecomed` int(11) NOT NULL   COMMENT '试用任务完成数量',
`tbpubed` int(11) NOT NULL,
`tbcomed` int(11) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_zofui_tasktb_selfposter` (
`id` int(11) NOT NULL  AUTO_INCREMENT,
`uniacid` int(11) NOT NULL,
`params` mediumtext(),
`bgimg` varchar(500),
`key` varchar(100)    COMMENT '关键词',
`pid` int(11) NOT NULL   COMMENT '规则id',
`content` varchar(2000)    COMMENT '扫码后提示',
`ccontent` varchar(2000)    COMMENT '生成海报后提示',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_zofui_tasktb_selfqrcode` (
`id` int(11) NOT NULL  AUTO_INCREMENT,
`uniacid` int(11) NOT NULL,
`openid` varchar(64),
`qrcodeid` int(11) NOT NULL   COMMENT 'qrcode表里的id',
`sence` varchar(64),
`expire` int(11) NOT NULL   COMMENT '过期时间',
`userid` int(11) NOT NULL   COMMENT '会员id',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_zofui_tasktb_selfquest` (
`id` int(11) NOT NULL  AUTO_INCREMENT,
`uniacid` int(11) NOT NULL,
`title` varchar(500),
`type` tinyint(1) NOT NULL   COMMENT '0用户答疑 1商家答疑',
`number` int(11) NOT NULL   COMMENT '排序序号，越大越前',
`content` mediumtext()    COMMENT '内容',
`settype` tinyint(1) NOT NULL   COMMENT '1系统添加的 0自己添加的',
`status` tinyint(1) NOT NULL   COMMENT '0正常 1隐藏',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_zofui_tasktb_sign` (
`id` int(11) NOT NULL  AUTO_INCREMENT,
`uniacid` int(10) NOT NULL,
`createtime` int(11) NOT NULL,
`uid` int(11) NOT NULL   COMMENT 'uid',
`day` varchar(22),
`give` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00',
`flag` tinyint(1) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_zofui_tasktb_slider` (
`id` int(11) NOT NULL  AUTO_INCREMENT,
`uniacid` int(11) NOT NULL,
`img` varchar(500),
`number` int(11) NOT NULL   COMMENT '排序，越大越前',
`url` varchar(500),
`isindex` tinyint(1) NOT NULL DEFAULT NULL DEFAULT '1'  COMMENT '0不显示 1主页显示',
`isusetask` tinyint(1)  DEFAULT NULL DEFAULT '1'  COMMENT '0不显示 1试用任务页面显示',
`istbtask` tinyint(3) NOT NULL   COMMENT '0不显示',
`isguy` tinyint(1) NOT NULL   COMMENT '找人页面',
`dayy` int(11) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_zofui_tasktb_step` (
`id` int(11) NOT NULL  AUTO_INCREMENT,
`step` text(),
`uniacid` int(11) NOT NULL,
`istemp` tinyint(1) NOT NULL   COMMENT '0不是模板 1模板',
`name` varchar(122),
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_zofui_tasktb_tabbar` (
`id` int(10) NOT NULL  AUTO_INCREMENT,
`uniacid` int(10) NOT NULL,
`name` varchar(100) NOT NULL   COMMENT '分类名称',
`number` int(11) NOT NULL   COMMENT '越大越前',
`img` varchar(500),
`url` varchar(500),
`color` varchar(22),
`actcolor` varchar(22),
`actimg` varchar(555),
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_zofui_tasktb_taked` (
`id` int(11) NOT NULL  AUTO_INCREMENT,
`uniacid` int(10) NOT NULL,
`openid` varchar(64)    COMMENT '抢的人',
`taskid` int(11) NOT NULL   COMMENT '任务id',
`createtime` int(11) NOT NULL   COMMENT '创建时间',
`waittime` int(11) NOT NULL   COMMENT '能回复的时间',
`endtime` int(11) NOT NULL   COMMENT '释放时间，如果时间大于现在占用，小于现在已释放',
`status` tinyint(1) NOT NULL   COMMENT '0刚抢到还没回复 1已回复 2已采纳 3已被拒绝',
`content` text()    COMMENT '回复内容',
`money` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '赏金',
`server` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '扣除平台费',
`ewai` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '额外奖励',
`replytime` int(11) NOT NULL   COMMENT '回复时间',
`dealtime` int(11) NOT NULL   COMMENT '雇主或系统处理时间',
`images` varchar(2000)    COMMENT '回复的图片',
`puber` varchar(64)    COMMENT '发布者openid  方便计算数量限制',
`giveparent` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '给上级奖励',
`continueid` int(11) NOT NULL   COMMENT '任务对于的连续id',
`reason` varchar(200)    COMMENT '拒绝理由',
`ip` varchar(42),
`isscan` tinyint(1) NOT NULL   COMMENT '0可以浏览看见 1不允许看见',
`subform` text(),
`userid` int(11) NOT NULL   COMMENT '会员id',
`pubuid` int(11) NOT NULL   COMMENT '会员id',
`twoupmoney` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '上上级提成',
`threeupmoney` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '上上上级奖励',
`adminadd` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '管理员增加余额数值',
`ds` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00',
`type` tinyint(1) NOT NULL   COMMENT '0正常 1虚假的',
`nick` varchar(32)    COMMENT '虚假昵称',
`headimg` varchar(555)    COMMENT '虚假头像',
`isfalse` tinyint(1) NOT NULL   COMMENT '0正常 1虚假的',
`falseuid` int(11) NOT NULL   COMMENT '虚拟会员id',
`isremind` tinyint(1) NOT NULL   COMMENT '0未提醒 1已提醒',
`gid` varchar(32),
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_zofui_tasktb_task` (
`id` int(11) NOT NULL  AUTO_INCREMENT,
`uniacid` int(10) NOT NULL,
`title` varchar(500),
`content` text()    COMMENT '任务内容',
`images` varchar(2000)    COMMENT '图片',
`iska` tinyint(1) NOT NULL   COMMENT '0不卡首屏 1卡首屏',
`num` int(11) NOT NULL   COMMENT '任务数量',
`money` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '任务单价',
`replytime` int(3) NOT NULL   COMMENT '等待回复时间',
`limitnum` int(6) NOT NULL   COMMENT '限制回复数量',
`sex` tinyint(1) NOT NULL   COMMENT '0不限制性别 1男性可回复 2女性可回复',
`ishide` tinyint(1) NOT NULL   COMMENT '0不隐藏 1隐藏',
`continue` tinyint(1) NOT NULL   COMMENT '0不连续发布 1连续发布',
`continueid` int(11) NOT NULL   COMMENT '连续发布任务的id',
`kagoodid` varchar(64)    COMMENT '卡首屏商品id',
`kakey` varchar(2000)    COMMENT '卡首屏关键字',
`start` int(11) NOT NULL   COMMENT '开始时间',
`end` int(11) NOT NULL   COMMENT '结束时间',
`status` tinyint(3) NOT NULL   COMMENT '0正常 1审核中 2下架',
`closereason` varchar(300)    COMMENT '审核不通过原因',
`istop` tinyint(1) NOT NULL   COMMENT '0未置顶 1置顶',
`puber` varchar(64)    COMMENT '发布者',
`sortid` int(11) NOT NULL   COMMENT '分类id',
`isimage` tinyint(1) NOT NULL   COMMENT '0不限制图片 1抢到后才可见',
`iscount` tinyint(1) NOT NULL   COMMENT '0还未结算 1已结算结束',
`backmoney` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '结算退回钱',
`counttime` int(11) NOT NULL   COMMENT '结算时间',
`costserver` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '花费的服务费',
`costka` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '花费的卡首屏',
`isempty` tinyint(1) NOT NULL   COMMENT '0没有被抢光 1被抢光了',
`scan` int(11) NOT NULL   COMMENT '浏览量',
`isstart` tinyint(1) NOT NULL   COMMENT '0已开始 1未开始',
`costtop` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '置顶费用',
`createtime` int(11) NOT NULL,
`link` varchar(2000),
`hidecontent` text()    COMMENT '定制的 隐藏内容',
`autoaddnum` int(8) NOT NULL   COMMENT '定制的 自动追加数量',
`autotime` int(8) NOT NULL   COMMENT '定制的 自动追加的次数',
`costautoadd` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '定制的 自动追加花费',
`backautoadd` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '定制的 退回自动追加',
`skiptype` tinyint(1) NOT NULL   COMMENT '定制的 0链接跳转 1口令跳转',
`isautoadd` tinyint(1) NOT NULL   COMMENT '定制的 0不自动追加 1自动追加',
`totalauto` int(11) NOT NULL   COMMENT '定制的 总计自动追加数量',
`autoadded` int(11) NOT NULL   COMMENT '定制的 已增加的自动追加',
`paymoney` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '需支付金额',
`prizetype` tinyint(1) NOT NULL   COMMENT '奖励类型 0赏金 1实物',
`prizetitle` varchar(500)    COMMENT '奖励标题',
`prizeimg` varchar(350)    COMMENT '奖励图片',
`type` tinyint(1) NOT NULL   COMMENT '0普通任务 1试用任务',
`pic` varchar(350)    COMMENT '淘宝商品图片',
`taokey` varchar(150)    COMMENT '下单商品淘口令',
`findtype` tinyint(1) NOT NULL   COMMENT 'WQ 搜商品方式 0直接跳转 1关键字搜 2都关闭',
`findkey` varchar(2500)    COMMENT '搜商品关键词',
`isarealimit` tinyint(1) NOT NULL   COMMENT '0不做区域限制 1限制到区县 2限制到市',
`province` varchar(64)    COMMENT '限制的省份',
`city` varchar(64)    COMMENT '城市',
`country` varchar(64)    COMMENT '区县',
`costfindkey` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '试用任务花费的关键词搜',
`ispause` tinyint(1) NOT NULL   COMMENT '开启任务',
`isform` tinyint(1) NOT NULL   COMMENT '0不用提交表单 1提交表单',
`gtitle` varchar(500)    COMMENT '淘宝商品标题',
`istaskform` tinyint(1) NOT NULL   COMMENT '0不使用模板 1使用回复模板',
`formid` int(11) NOT NULL   COMMENT '模板id',
`userid` int(11) NOT NULL   COMMENT '会员id',
`giveparent` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '发任务上级提成',
`givetwo` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '上上级',
`givethree` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00',
`address` varchar(255)    COMMENT '任务地址',
`readprice` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '查看答案价格',
`isread` tinyint(3) NOT NULL   COMMENT '0不可看 1可看',
`falsepuber` int(11) NOT NULL   COMMENT '虚拟id',
`falsenum` int(11) NOT NULL   COMMENT '虚拟数量',
`useric` varchar(200)    COMMENT '虚假昵称',
`idcode` varchar(15),
`headimg` varchar(255),
`levellim` tinyint(1) NOT NULL   COMMENT '0不限制 1一级可接 2二级可接 3二三级可接',
`mark` varchar(200),
`gid` varchar(32),
`stepid` int(11) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_zofui_tasktb_taskform` (
`id` int(10) NOT NULL  AUTO_INCREMENT,
`uniacid` int(10) NOT NULL,
`name` varchar(100) NOT NULL   COMMENT '分类名称',
`number` int(11) NOT NULL   COMMENT '越大越前',
`form` text(),
`type` tinyint(1) NOT NULL   COMMENT '0普通任务',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_zofui_tasktb_taskmessage` (
`id` int(11) NOT NULL  AUTO_INCREMENT,
`uniacid` int(10) NOT NULL,
`content` varchar(1000),
`openid` varchar(64),
`parent` int(11) NOT NULL   COMMENT '回复的id',
`taskid` int(11) NOT NULL   COMMENT '任务id',
`time` int(11) NOT NULL   COMMENT '留言时间',
`isadmin` tinyint(1) NOT NULL   COMMENT '0不是管理员发布的任务，1是的',
`type` tinyint(1) NOT NULL   COMMENT '0普通任务',
`userid` int(11) NOT NULL   COMMENT '会员id',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_zofui_tasktb_tasksort` (
`id` int(10) NOT NULL  AUTO_INCREMENT,
`uniacid` int(10) NOT NULL,
`name` varchar(100) NOT NULL   COMMENT '分类名称',
`order` int(10) NOT NULL   COMMENT '分类排序，越大越前',
`title` varchar(1000)    COMMENT '默认发布任务的标题',
`content` text()    COMMENT '默认 任务内容',
`number` int(11) NOT NULL   COMMENT '越大越前',
`status` tinyint(1) NOT NULL   COMMENT '0正常 1下线',
`img` varchar(500),
`dmoney` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '默认金额',
`other` mediumtext(),
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_zofui_tasktb_tbblack` (
`id` int(10) NOT NULL  AUTO_INCREMENT,
`uniacid` int(10) NOT NULL,
`openid` varchar(64)    COMMENT '会员openid',
`target` varchar(64)    COMMENT '黑名单目标',
`userid` int(11) NOT NULL   COMMENT '会员id',
`targetuid` int(11) NOT NULL   COMMENT '会员id',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_zofui_tasktb_tbcert` (
`id` int(10) NOT NULL  AUTO_INCREMENT,
`uniacid` int(10) NOT NULL,
`createtime` int(11) NOT NULL   COMMENT '时间',
`takedid` int(11) NOT NULL   COMMENT '回复id',
`content` varchar(500)    COMMENT '内容',
`type` tinyint(1) NOT NULL   COMMENT '0前端发的 1后端发的',
`images` varchar(2500)    COMMENT '图片',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_zofui_tasktb_tbform` (
`id` int(11) NOT NULL  AUTO_INCREMENT,
`uniacid` int(10) NOT NULL,
`name` varchar(66),
`number` int(11) NOT NULL   COMMENT '越大越前',
`title` varchar(255),
`num` int(11) NOT NULL,
`money` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00',
`tbmoney` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00',
`step` varchar(2222),
`content` text(),
`status` tinyint(1) NOT NULL   COMMENT '0正常 1下架',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_zofui_tasktb_tbremind` (
`id` int(10) NOT NULL  AUTO_INCREMENT,
`uniacid` int(10) NOT NULL,
`createtime` int(11) NOT NULL   COMMENT '时间',
`takedid` int(11) NOT NULL   COMMENT '回复id',
`content` varchar(500)    COMMENT '内容',
`pos` tinyint(1) NOT NULL   COMMENT '0前端发的 1后端发的',
`from` tinyint(1) NOT NULL   COMMENT '0担保任务提醒',
`type` tinyint(1) NOT NULL   COMMENT '0提醒 1发佣金',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_zofui_tasktb_tbtaked` (
`id` int(11) NOT NULL  AUTO_INCREMENT,
`uniacid` int(10) NOT NULL,
`openid` varchar(64)    COMMENT '抢的人',
`taskid` int(11) NOT NULL   COMMENT '任务id',
`createtime` int(11) NOT NULL   COMMENT '创建时间',
`status` tinyint(1) NOT NULL   COMMENT '0抢到任务待审核 1审核未通过 2审核通过进行中 3任务完成 4失败待雇员确认 5任务失败 6申诉中 7管理员已判 8申诉失败任务失败 9申诉成功任务完成',
`content` text()    COMMENT '回复内容',
`money` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '扣除的赏金',
`server` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '收益扣除平台费',
`puber` varchar(64)    COMMENT '发布者openid  方便计算数量限制',
`giveparent` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '给上级奖励',
`reason` varchar(200)    COMMENT '拒绝理由',
`isend` tinyint(1) NOT NULL   COMMENT '0任务进行中 1任务结束',
`takecontent` varchar(4000)    COMMENT '抢任务提交的内容',
`passtime` int(11) NOT NULL   COMMENT '通过时间',
`nopasstime` int(11) NOT NULL   COMMENT '未通过时间',
`nopassreason` varchar(200)    COMMENT '未过审核原因',
`step` tinyint(2) NOT NULL   COMMENT '任务步骤 1上传浏览图片 2上传下单图片 3上传签收图片 4上传评价图片 5备注留言 6完成',
`stepcontent` text()    COMMENT '每个步骤的内容',
`tbmoney` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '扣除的保证金',
`costserver` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '审核任务扣除服务费',
`comtime` int(11) NOT NULL   COMMENT '完成时间',
`setfailtime` int(11) NOT NULL   COMMENT '雇主设为失败的时间',
`failreason` varchar(150)    COMMENT '失败原因',
`confirmfailtime` int(11) NOT NULL   COMMENT '雇员确认失败时间',
`complaintime` int(11) NOT NULL   COMMENT '雇员申诉时间',
`complainstep` tinyint(3) NOT NULL   COMMENT '0申诉中 1已判给雇员 2已判给雇主',
`adminsettime` int(11) NOT NULL   COMMENT '管理员判断时间',
`complainentime` int(11) NOT NULL   COMMENT '申诉结束时间',
`complainto` tinyint(1) NOT NULL   COMMENT '申诉最终判给谁 0雇员 1雇主',
`subcomtime` int(11) NOT NULL   COMMENT '上传完资料，提交完成时间',
`islimitstep` int(1) NOT NULL   COMMENT '0不限定第一步结束时间 1限定',
`mtype` tinyint(1) NOT NULL   COMMENT '0用保证金支付的 1用余额支付的',
`userid` int(11) NOT NULL   COMMENT '会员id',
`pubuid` int(11) NOT NULL   COMMENT '会员id',
`givetwo` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '上上级',
`givethree` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '上上上级',
`gid` varchar(32),
`gived` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '已经发放的金额',
`iscangive` tinyint(1) NOT NULL   COMMENT '0不可发佣金 1可发佣金',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_zofui_tasktb_tbtask` (
`id` int(11) NOT NULL  AUTO_INCREMENT,
`uniacid` int(10) NOT NULL,
`title` varchar(500),
`content` text()    COMMENT '任务内容',
`images` varchar(2000)    COMMENT '图片',
`hideimages` varchar(2000)    COMMENT '隐藏的图片',
`iska` tinyint(1) NOT NULL   COMMENT '0不卡首屏 1卡首屏',
`num` int(11) NOT NULL   COMMENT '任务数量',
`money` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '任务单价',
`limitnum` int(6) NOT NULL   COMMENT '限制抢的次数',
`sex` tinyint(1) NOT NULL   COMMENT '0不限制性别 1男性可回复 2女性可回复',
`kagoodid` varchar(64)    COMMENT '卡首屏商品id',
`kakey` varchar(2000)    COMMENT '卡首屏关键字',
`start` int(11) NOT NULL   COMMENT '开始时间',
`end` int(11) NOT NULL   COMMENT '结束时间',
`status` tinyint(3) NOT NULL   COMMENT '0正常 1审核中 2下架',
`istop` tinyint(1) NOT NULL   COMMENT '0未置顶 1置顶',
`puber` varchar(64)    COMMENT '发布者',
`iscount` tinyint(1) NOT NULL   COMMENT '0还未结算 1已结算结束',
`backmoney` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '结算退回钱',
`counttime` int(11) NOT NULL   COMMENT '结算时间',
`costserver` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '花费的服务费',
`costka` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '花费的卡首屏',
`isempty` tinyint(1) NOT NULL   COMMENT '0没有被抢光 1被抢光了',
`scan` int(11) NOT NULL   COMMENT '浏览量',
`costtop` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '置顶费用',
`createtime` int(11) NOT NULL,
`link` varchar(2000),
`hidecontent` text()    COMMENT '定制的 隐藏内容',
`skiptype` tinyint(1) NOT NULL   COMMENT '定制的 0链接跳转 1口令跳转',
`tbmoney` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '担保金额',
`isstart` tinyint(1) NOT NULL   COMMENT '0 开始中 1未开始',
`step` varchar(2000),
`closereason` varchar(255)    COMMENT '审核不通过原因',
`topendtime` int(11) NOT NULL   COMMENT '置顶结束时间',
`userid` int(11) NOT NULL   COMMENT '会员id',
`giveparent` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '发任务上级提成',
`givetwo` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '上上级',
`givethree` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00',
`isarealimit` tinyint(1) NOT NULL   COMMENT '0不做区域限制 1限制到区县 2限制市',
`province` varchar(64),
`city` varchar(64),
`country` varchar(64),
`tkl` varchar(500)    COMMENT '淘口令',
`gid` varchar(32),
`address` varchar(255)    COMMENT '任务地址',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_zofui_tasktb_tbtaskstep` (
`id` int(10) NOT NULL  AUTO_INCREMENT,
`uniacid` int(10) NOT NULL,
`openid` varchar(64),
`step` varchar(2000),
`userid` int(11) NOT NULL   COMMENT '会员id',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_zofui_tasktb_useaddcontent` (
`id` int(11) NOT NULL  AUTO_INCREMENT,
`uniacid` int(11) NOT NULL,
`content` varchar(800),
`img` varchar(2000),
`takedid` int(11) NOT NULL   COMMENT '回复的id',
`createtime` int(11) NOT NULL   COMMENT '创建时间',
`type` tinyint(1) NOT NULL   COMMENT '0补充内容 1提醒内容',
`taskid` int(11) NOT NULL   COMMENT '任务id',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_zofui_tasktb_useric` (
`id` int(11) NOT NULL  AUTO_INCREMENT,
`uniacid` int(10) NOT NULL,
`name` varchar(32),
`number` tinyint(1) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_zofui_tasktb_userics` (
`id` int(11) NOT NULL  AUTO_INCREMENT,
`uniacid` int(10) NOT NULL,
`uid` int(11) NOT NULL   COMMENT '会员uid',
`icid` int(11) NOT NULL   COMMENT '标签id',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_zofui_tasktb_usersort` (
`id` int(10) NOT NULL  AUTO_INCREMENT,
`uid` int(11) NOT NULL,
`sortid` int(11) NOT NULL,
`uniacid` int(11) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_zofui_tasktb_usetasklog` (
`id` int(11) NOT NULL  AUTO_INCREMENT,
`uniacid` int(11) NOT NULL,
`openid` varchar(64),
`taskid` int(11) NOT NULL   COMMENT '任务编号',
`createtime` int(11) NOT NULL   COMMENT '创建时间',
`status` tinyint(1) NOT NULL   COMMENT '0申请中 1已通过 2审核不通过 3雇员主动取消 4已提交订单号 5已完成 6已拒绝结果失败',
`isactivity` tinyint(1) NOT NULL   COMMENT '是否有效的试用 0有效 1无效 ，当被拒绝或取消后变为1记为无效',
`passortime` int(11) NOT NULL   COMMENT '通过或拒绝时间',
`canceltime` int(11) NOT NULL   COMMENT '雇员主动取消任务时间',
`subcontent` varchar(2000)    COMMENT '提交内容',
`subtime` int(11) NOT NULL   COMMENT '提交订单时间',
`suctime` int(11) NOT NULL   COMMENT '设为成功时间',
`prizemoney` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '赏金金额',
`reason` varchar(200)    COMMENT '拒绝和失败原因',
`tosuctime` int(11) NOT NULL   COMMENT '转为成功时间',
`failtime` int(11) NOT NULL   COMMENT '任务失败时间',
`iscomplained` tinyint(1) NOT NULL   COMMENT '0未被投诉 1被投诉',
`giveparent` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '给上级的提成',
`initcontent` varchar(2000)    COMMENT '抢任务提交的内容',
`puber` varchar(64),
`userid` int(11) NOT NULL   COMMENT '会员id',
`pubuid` int(11) NOT NULL   COMMENT '会员id',
`givetwo` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '上上级',
`givethree` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '上上上级',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_zofui_tasktb_vauth` (
`id` int(11) NOT NULL  AUTO_INCREMENT,
`uniacid` int(11) NOT NULL,
`params` text(),
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
if(pdo_tableexists('ims_zofui_task_user')) {
 if(!pdo_fieldexists('ims_zofui_task_user',  'id')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_task_user')." ADD `id` int(11) NOT NULL  AUTO_INCREMENT;");
 }
}
if(pdo_tableexists('ims_zofui_task_user')) {
 if(!pdo_fieldexists('ims_zofui_task_user',  'openid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_task_user')." ADD `openid` varchar(50) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_task_user')) {
 if(!pdo_fieldexists('ims_zofui_task_user',  'uid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_task_user')." ADD `uid` int(10) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_task_user')) {
 if(!pdo_fieldexists('ims_zofui_task_user',  'logintime')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_task_user')." ADD `logintime` int(11) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_task_user')) {
 if(!pdo_fieldexists('ims_zofui_task_user',  'status')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_task_user')." ADD `status` tinyint(3) NOT NULL   COMMENT '默认0 正常 1认证（已缴纳保证金） 2拉黑';");
 }
}
if(pdo_tableexists('ims_zofui_task_user')) {
 if(!pdo_fieldexists('ims_zofui_task_user',  'uniacid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_task_user')." ADD `uniacid` int(10) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_task_user')) {
 if(!pdo_fieldexists('ims_zofui_task_user',  'deposit')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_task_user')." ADD `deposit` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '保证金';");
 }
}
if(pdo_tableexists('ims_zofui_task_user')) {
 if(!pdo_fieldexists('ims_zofui_task_user',  'city')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_task_user')." ADD `city` varchar(20) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_task_user')) {
 if(!pdo_fieldexists('ims_zofui_task_user',  'mobile')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_task_user')." ADD `mobile` varchar(15) NOT NULL   COMMENT '手机号码';");
 }
}
if(pdo_tableexists('ims_zofui_task_user')) {
 if(!pdo_fieldexists('ims_zofui_task_user',  'qrcode')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_task_user')." ADD `qrcode` varchar(200) NOT NULL   COMMENT '微信二维码';");
 }
}
if(pdo_tableexists('ims_zofui_task_user')) {
 if(!pdo_fieldexists('ims_zofui_task_user',  'pubnumber')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_task_user')." ADD `pubnumber` int(10) NOT NULL   COMMENT '发布数量';");
 }
}
if(pdo_tableexists('ims_zofui_task_user')) {
 if(!pdo_fieldexists('ims_zofui_task_user',  'acceptnumber')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_task_user')." ADD `acceptnumber` int(10) NOT NULL   COMMENT '采纳数量';");
 }
}
if(pdo_tableexists('ims_zofui_task_user')) {
 if(!pdo_fieldexists('ims_zofui_task_user',  'creditscore')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_task_user')." ADD `creditscore` decimal(6,1) NOT NULL DEFAULT NULL DEFAULT '10.0'  COMMENT '信誉分数';");
 }
}
if(pdo_tableexists('ims_zofui_task_user')) {
 if(!pdo_fieldexists('ims_zofui_task_user',  'guytype')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_task_user')." ADD `guytype` tinyint(1) NOT NULL   COMMENT '人物类型，1是发任务的，2是接任务的';");
 }
}
if(pdo_tableexists('ims_zofui_task_user')) {
 if(!pdo_fieldexists('ims_zofui_task_user',  'guydesc')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_task_user')." ADD `guydesc` varchar(200)    COMMENT '个人描述';");
 }
}
if(pdo_tableexists('ims_zofui_task_user')) {
 if(!pdo_fieldexists('ims_zofui_task_user',  'guysort')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_task_user')." ADD `guysort` int(5) NOT NULL   COMMENT '所属人物分类';");
 }
}
if(pdo_tableexists('ims_zofui_task_user')) {
 if(!pdo_fieldexists('ims_zofui_task_user',  'contacttype')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_task_user')." ADD `contacttype` tinyint(1) NOT NULL   COMMENT '联系方式 0没有联系方式，1手机 2微信二维码 3两者都支持';");
 }
}
if(pdo_tableexists('ims_zofui_task_user')) {
 if(!pdo_fieldexists('ims_zofui_task_user',  'replynumber')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_task_user')." ADD `replynumber` int(9) NOT NULL   COMMENT '回复数量';");
 }
}
if(pdo_tableexists('ims_zofui_task_user')) {
 if(!pdo_fieldexists('ims_zofui_task_user',  'acceptednumber')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_task_user')." ADD `acceptednumber` int(9) NOT NULL   COMMENT '被采纳数量';");
 }
}
if(pdo_tableexists('ims_zofui_task_user')) {
 if(!pdo_fieldexists('ims_zofui_task_user',  'nickname')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_task_user')." ADD `nickname` varchar(64)    COMMENT '淘宝版本里用到 昵称';");
 }
}
if(pdo_tableexists('ims_zofui_task_user')) {
 if(!pdo_fieldexists('ims_zofui_task_user',  'headimgurl')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_task_user')." ADD `headimgurl` varchar(300)    COMMENT '淘宝版本里用到 头像';");
 }
}
if(pdo_tableexists('ims_zofui_task_user')) {
 if(!pdo_fieldexists('ims_zofui_task_user',  'sex')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_task_user')." ADD `sex` tinyint(1) NOT NULL   COMMENT '淘宝版本里用到 1男 2女';");
 }
}
if(pdo_tableexists('ims_zofui_task_user')) {
 if(!pdo_fieldexists('ims_zofui_task_user',  'activity')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_task_user')." ADD `activity` int(11) NOT NULL   COMMENT '淘宝版本里用到 活跃度';");
 }
}
if(pdo_tableexists('ims_zofui_task_user')) {
 if(!pdo_fieldexists('ims_zofui_task_user',  'uptime')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_task_user')." ADD `uptime` int(11) NOT NULL   COMMENT '淘宝版本里用到 更新活跃度时间';");
 }
}
if(pdo_tableexists('ims_zofui_task_user')) {
 if(!pdo_fieldexists('ims_zofui_task_user',  'ispub')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_task_user')." ADD `ispub` tinyint(1) NOT NULL   COMMENT '淘宝版本里用到 0不是发任务的 1是';");
 }
}
if(pdo_tableexists('ims_zofui_task_user')) {
 if(!pdo_fieldexists('ims_zofui_task_user',  'isacc')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_task_user')." ADD `isacc` tinyint(1) NOT NULL   COMMENT '淘宝版本里用到 0不是接任务的 1是';");
 }
}
if(pdo_tableexists('ims_zofui_task_user')) {
 if(!pdo_fieldexists('ims_zofui_task_user',  'conweixin')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_task_user')." ADD `conweixin` tinyint(1) NOT NULL   COMMENT '淘宝版本里用到 0不开启微信联系 1 开启';");
 }
}
if(pdo_tableexists('ims_zofui_task_user')) {
 if(!pdo_fieldexists('ims_zofui_task_user',  'conmobile')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_task_user')." ADD `conmobile` tinyint(1) NOT NULL   COMMENT '淘宝版本里用到 0不开启电话联系 1开启';");
 }
}
if(pdo_tableexists('ims_zofui_task_user')) {
 if(!pdo_fieldexists('ims_zofui_task_user',  'parent')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_task_user')." ADD `parent` int(11) NOT NULL   COMMENT '淘宝版本里用到 上级id';");
 }
}
if(pdo_tableexists('ims_zofui_task_user')) {
 if(!pdo_fieldexists('ims_zofui_task_user',  'limitnum')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_task_user')." ADD `limitnum` int(5) NOT NULL   COMMENT '回复次数限制';");
 }
}
if(pdo_tableexists('ims_zofui_task_user')) {
 if(!pdo_fieldexists('ims_zofui_task_user',  'account')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_task_user')." ADD `account` varchar(20)    COMMENT '账户，绑定电话号码';");
 }
}
if(pdo_tableexists('ims_zofui_task_user')) {
 if(!pdo_fieldexists('ims_zofui_task_user',  'createtime')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_task_user')." ADD `createtime` int(11) NOT NULL   COMMENT '创建时间';");
 }
}
if(pdo_tableexists('ims_zofui_task_user')) {
 if(!pdo_fieldexists('ims_zofui_task_user',  'giveparent')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_task_user')." ADD `giveparent` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '上级获得提成总额';");
 }
}
if(pdo_tableexists('ims_zofui_task_user')) {
 if(!pdo_fieldexists('ims_zofui_task_user',  'authopenid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_task_user')." ADD `authopenid` varchar(64)    COMMENT '被借权公众号的openid';");
 }
}
if(pdo_tableexists('ims_zofui_task_user')) {
 if(!pdo_fieldexists('ims_zofui_task_user',  'alipayname')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_task_user')." ADD `alipayname` varchar(32)    COMMENT '支付宝姓名';");
 }
}
if(pdo_tableexists('ims_zofui_task_user')) {
 if(!pdo_fieldexists('ims_zofui_task_user',  'alipay')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_task_user')." ADD `alipay` varchar(64)    COMMENT '支付宝账户';");
 }
}
if(pdo_tableexists('ims_zofui_task_user')) {
 if(!pdo_fieldexists('ims_zofui_task_user',  'verifystatus')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_task_user')." ADD `verifystatus` tinyint(1) NOT NULL   COMMENT '0未提交认证 1已提交认证 2认证通过 3认证不通过';");
 }
}
if(pdo_tableexists('ims_zofui_task_user')) {
 if(!pdo_fieldexists('ims_zofui_task_user',  'verifyform')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_task_user')." ADD `verifyform` text()    COMMENT '额外提交的表单';");
 }
}
if(pdo_tableexists('ims_zofui_task_user')) {
 if(!pdo_fieldexists('ims_zofui_task_user',  'mark')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_task_user')." ADD `mark` varchar(200)    COMMENT '标记备注内容';");
 }
}
if(pdo_tableexists('ims_zofui_task_user')) {
 if(!pdo_fieldexists('ims_zofui_task_user',  'limitday')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_task_user')." ADD `limitday` int(5) NOT NULL   COMMENT '限制抢任务的天数';");
 }
}
if(pdo_tableexists('ims_zofui_task_user')) {
 if(!pdo_fieldexists('ims_zofui_task_user',  'tbpub')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_task_user')." ADD `tbpub` int(11) NOT NULL   COMMENT '发布担保任务数量';");
 }
}
if(pdo_tableexists('ims_zofui_task_user')) {
 if(!pdo_fieldexists('ims_zofui_task_user',  'tbsuccess')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_task_user')." ADD `tbsuccess` int(11) NOT NULL   COMMENT '采纳担保任务数量';");
 }
}
if(pdo_tableexists('ims_zofui_task_user')) {
 if(!pdo_fieldexists('ims_zofui_task_user',  'tbtake')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_task_user')." ADD `tbtake` int(11) NOT NULL   COMMENT '抢到担保任务数量';");
 }
}
if(pdo_tableexists('ims_zofui_task_user')) {
 if(!pdo_fieldexists('ims_zofui_task_user',  'tbcom')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_task_user')." ADD `tbcom` int(11) NOT NULL   COMMENT '完成担保任务数量';");
 }
}
if(pdo_tableexists('ims_zofui_task_user')) {
 if(!pdo_fieldexists('ims_zofui_task_user',  'payqr')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_task_user')." ADD `payqr` varchar(500);");
 }
}
if(pdo_tableexists('ims_zofui_task_user')) {
 if(!pdo_fieldexists('ims_zofui_task_user',  'uselimitnum')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_task_user')." ADD `uselimitnum` int(11) NOT NULL   COMMENT '限制抢试用任务数量';");
 }
}
if(pdo_tableexists('ims_zofui_task_user')) {
 if(!pdo_fieldexists('ims_zofui_task_user',  'uselimitday')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_task_user')." ADD `uselimitday` int(11) NOT NULL   COMMENT '限制抢试用任务数量';");
 }
}
if(pdo_tableexists('ims_zofui_task_user')) {
 if(!pdo_fieldexists('ims_zofui_task_user',  'givetwo')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_task_user')." ADD `givetwo` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '累计给上上级的提成';");
 }
}
if(pdo_tableexists('ims_zofui_task_user')) {
 if(!pdo_fieldexists('ims_zofui_task_user',  'givethree')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_task_user')." ADD `givethree` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '累计给上上级的提成';");
 }
}
if(pdo_tableexists('ims_zofui_task_user')) {
 if(!pdo_fieldexists('ims_zofui_task_user',  'level')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_task_user')." ADD `level` tinyint(1) NOT NULL   COMMENT '0普通 1一级 2二级';");
 }
}
if(pdo_tableexists('ims_zofui_task_user')) {
 if(!pdo_fieldexists('ims_zofui_task_user',  'utime')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_task_user')." ADD `utime` int(11) NOT NULL   COMMENT '会员到期时间';");
 }
}
if(pdo_tableexists('ims_zofui_task_user')) {
 if(!pdo_fieldexists('ims_zofui_task_user',  'anwm')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_task_user')." ADD `anwm` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '累计付费阅读';");
 }
}
if(pdo_tableexists('ims_zofui_task_user')) {
 if(!pdo_fieldexists('ims_zofui_task_user',  'yinp')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_task_user')." ADD `yinp` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00';");
 }
}
if(pdo_tableexists('ims_zofui_task_user')) {
 if(!pdo_fieldexists('ims_zofui_task_user',  'baoshi')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_task_user')." ADD `baoshi` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00';");
 }
}
if(pdo_tableexists('ims_zofui_task_user')) {
 if(!pdo_fieldexists('ims_zofui_task_user',  'verifyend')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_task_user')." ADD `verifyend` int(11) NOT NULL   COMMENT '认证结束';");
 }
}
if(pdo_tableexists('ims_zofui_task_user')) {
 if(!pdo_fieldexists('ims_zofui_task_user',  'iscostauth')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_task_user')." ADD `iscostauth` tinyint(1) NOT NULL   COMMENT '0未付费认证 1已付';");
 }
}
if(pdo_tableexists('ims_zofui_task_user')) {
 if(!pdo_fieldexists('ims_zofui_task_user',  'qq')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_task_user')." ADD `qq` varchar(20);");
 }
}
if(pdo_tableexists('ims_zofui_task_user')) {
 if(!pdo_fieldexists('ims_zofui_task_user',  'pp')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_task_user')." ADD `pp` int(11) NOT NULL   COMMENT '上上级';");
 }
}
if(pdo_tableexists('ims_zofui_task_user')) {
 if(!pdo_fieldexists('ims_zofui_task_user',  'ppp')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_task_user')." ADD `ppp` int(11) NOT NULL   COMMENT '上上级';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_ad')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_ad',  'id')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_ad')." ADD `id` int(10) NOT NULL  AUTO_INCREMENT;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_ad')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_ad',  'uniacid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_ad')." ADD `uniacid` int(10) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_ad')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_ad',  'title')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_ad')." ADD `title` varchar(2000) NOT NULL   COMMENT '标题';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_ad')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_ad',  'number')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_ad')." ADD `number` int(10) NOT NULL   COMMENT '分类排序，越大越前';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_ad')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_ad',  'content')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_ad')." ADD `content` mediumtext()    COMMENT '广告内容';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_ad')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_ad',  'time')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_ad')." ADD `time` int(11) NOT NULL   COMMENT '发布时间';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_ad')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_ad',  'status')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_ad')." ADD `status` tinyint(1) NOT NULL   COMMENT '0显示 1下架';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_anwbox')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_anwbox',  'id')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_anwbox')." ADD `id` int(11) NOT NULL  AUTO_INCREMENT;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_anwbox')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_anwbox',  'uniacid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_anwbox')." ADD `uniacid` int(10) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_anwbox')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_anwbox',  'uid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_anwbox')." ADD `uid` int(11) NOT NULL   COMMENT '会员id';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_anwbox')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_anwbox',  'taskid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_anwbox')." ADD `taskid` int(11) NOT NULL   COMMENT '任务id';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_anwbox')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_anwbox',  'readid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_anwbox')." ADD `readid` int(11) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_anwbox')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_anwbox',  'money')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_anwbox')." ADD `money` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '金额';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_anwbox')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_anwbox',  'endtime')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_anwbox')." ADD `endtime` int(11) NOT NULL   COMMENT '结束时间';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_anwbox')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_anwbox',  'status')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_anwbox')." ADD `status` tinyint(1) NOT NULL   COMMENT '0未领取 1已领取';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_anwbox')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_anwbox',  'createtime')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_anwbox')." ADD `createtime` int(11) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_anwbox')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_anwbox',  'gettime')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_anwbox')." ADD `gettime` int(11) NOT NULL   COMMENT '领取时间';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_anwgeted')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_anwgeted',  'id')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_anwgeted')." ADD `id` int(11) NOT NULL  AUTO_INCREMENT;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_anwgeted')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_anwgeted',  'uniacid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_anwgeted')." ADD `uniacid` int(10) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_anwgeted')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_anwgeted',  'uid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_anwgeted')." ADD `uid` int(11) NOT NULL   COMMENT '会员id';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_anwgeted')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_anwgeted',  'createtime')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_anwgeted')." ADD `createtime` int(11) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_anwgeted')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_anwgeted',  'money')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_anwgeted')." ADD `money` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_anwgeted')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_anwgeted',  'thisday')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_anwgeted')." ADD `thisday` int(11) NOT NULL   COMMENT '回馈的日期';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_anwread')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_anwread',  'id')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_anwread')." ADD `id` int(11) NOT NULL  AUTO_INCREMENT;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_anwread')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_anwread',  'uniacid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_anwread')." ADD `uniacid` int(10) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_anwread')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_anwread',  'uid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_anwread')." ADD `uid` int(11) NOT NULL   COMMENT '会员uid';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_anwread')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_anwread',  'taskid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_anwread')." ADD `taskid` int(11) NOT NULL   COMMENT '任务id';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_anwread')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_anwread',  'createtime')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_anwread')." ADD `createtime` int(11) NOT NULL   COMMENT '创建时间';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_anwread')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_anwread',  'cost')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_anwread')." ADD `cost` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '花费';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_anwread')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_anwread',  'endtime')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_anwread')." ADD `endtime` int(11) NOT NULL   COMMENT '到期时间';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_anwread')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_anwread',  'puberfee')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_anwread')." ADD `puberfee` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '发布者的';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_anwread')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_anwread',  'lrfee')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_anwread')." ADD `lrfee` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '系统利润';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_anwread')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_anwread',  'boxfee')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_anwread')." ADD `boxfee` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '宝箱';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_anwread')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_anwread',  'sysfee')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_anwread')." ADD `sysfee` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '系统成本';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_anwread')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_anwread',  'isshared')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_anwread')." ADD `isshared` tinyint(1) NOT NULL   COMMENT '是否已分成宝箱 0未 1已分';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_anwread')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_anwread',  'iscountbox')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_anwread')." ADD `iscountbox` tinyint(1) NOT NULL   COMMENT '0未计算宝箱 1已计算宝箱';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_authform')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_authform',  'id')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_authform')." ADD `id` int(10) NOT NULL  AUTO_INCREMENT;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_authform')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_authform',  'uniacid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_authform')." ADD `uniacid` int(10) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_authform')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_authform',  'name')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_authform')." ADD `name` varchar(150)    COMMENT '表单名称';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_authform')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_authform',  'number')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_authform')." ADD `number` int(11) NOT NULL   COMMENT '排序序号';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_authform')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_authform',  'formtype')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_authform')." ADD `formtype` varchar(32)    COMMENT '类型';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_authform')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_authform',  'useric')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_authform')." ADD `useric` varchar(2500)    COMMENT '虚假昵称';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_banner')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_banner',  'id')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_banner')." ADD `id` int(11) NOT NULL  AUTO_INCREMENT;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_banner')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_banner',  'uniacid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_banner')." ADD `uniacid` int(11) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_banner')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_banner',  'img')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_banner')." ADD `img` varchar(500);");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_banner')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_banner',  'number')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_banner')." ADD `number` int(11) NOT NULL   COMMENT '排序，越大越前';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_banner')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_banner',  'url')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_banner')." ADD `url` varchar(500);");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_banner')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_banner',  'name')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_banner')." ADD `name` varchar(32) NOT NULL   COMMENT '名称';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_banner')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_banner',  'desc')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_banner')." ADD `desc` varchar(120);");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_codekey')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_codekey',  'id')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_codekey')." ADD `id` int(10) NOT NULL  AUTO_INCREMENT;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_codekey')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_codekey',  'key')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_codekey')." ADD `key` varchar(33);");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_complain')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_complain',  'id')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_complain')." ADD `id` int(11) NOT NULL  AUTO_INCREMENT;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_complain')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_complain',  'uniacid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_complain')." ADD `uniacid` int(10) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_complain')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_complain',  'content')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_complain')." ADD `content` varchar(1500);");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_complain')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_complain',  'images')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_complain')." ADD `images` varchar(2000);");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_complain')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_complain',  'time')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_complain')." ADD `time` int(11) NOT NULL   COMMENT '时间';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_complain')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_complain',  'openid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_complain')." ADD `openid` varchar(64);");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_complain')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_complain',  'taskid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_complain')." ADD `taskid` int(11) NOT NULL   COMMENT '任务id';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_complain')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_complain',  'status')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_complain')." ADD `status` tinyint(1) NOT NULL   COMMENT '0未处理 1已处理';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_complain')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_complain',  'userid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_complain')." ADD `userid` int(11) NOT NULL   COMMENT '会员id';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_continue')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_continue',  'id')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_continue')." ADD `id` int(11) NOT NULL  AUTO_INCREMENT;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_continue')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_continue',  'uniacid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_continue')." ADD `uniacid` int(10) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_continue')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_continue',  'money')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_continue')." ADD `money` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '连续奖励金额';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_continue')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_continue',  'totalmoney')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_continue')." ADD `totalmoney` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '奖励的总金额';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_continue')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_continue',  'totalnum')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_continue')." ADD `totalnum` int(11) NOT NULL   COMMENT '数量';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_continue')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_continue',  'backmoney')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_continue')." ADD `backmoney` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '结算退回金额';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_continue')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_continue',  'prizemoney')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_continue')." ADD `prizemoney` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '已给的奖励金额';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_continue')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_continue',  'isback')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_continue')." ADD `isback` tinyint(1) NOT NULL   COMMENT '0未结算 1已结算';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_draw')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_draw',  'id')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_draw')." ADD `id` int(11) NOT NULL  AUTO_INCREMENT;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_draw')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_draw',  'uniacid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_draw')." ADD `uniacid` int(11) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_draw')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_draw',  'openid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_draw')." ADD `openid` varchar(64) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_draw')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_draw',  'money')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_draw')." ADD `money` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '金额';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_draw')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_draw',  'status')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_draw')." ADD `status` tinyint(1) NOT NULL   COMMENT '状态 0待确认 1已支付 2退回 3拒绝';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_draw')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_draw',  'createtime')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_draw')." ADD `createtime` int(11) NOT NULL   COMMENT '创建时间';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_draw')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_draw',  'dealtime')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_draw')." ADD `dealtime` int(11) NOT NULL   COMMENT '处理时间';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_draw')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_draw',  'backreason')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_draw')." ADD `backreason` varchar(125) NOT NULL   COMMENT '退回理由';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_draw')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_draw',  'type')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_draw')." ADD `type` tinyint(1) NOT NULL   COMMENT '1余额 2保证金';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_draw')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_draw',  'refusereason')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_draw')." ADD `refusereason` varchar(255)    COMMENT '拒绝支付理由';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_draw')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_draw',  'paytype')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_draw')." ADD `paytype` tinyint(1) NOT NULL   COMMENT '0微信支付 1支付宝支付';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_draw')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_draw',  'alipayname')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_draw')." ADD `alipayname` varchar(32);");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_draw')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_draw',  'alipay')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_draw')." ADD `alipay` varchar(64);");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_draw')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_draw',  'userid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_draw')." ADD `userid` int(11) NOT NULL   COMMENT '会员id';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_draw')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_draw',  'payno')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_draw')." ADD `payno` varchar(64);");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_draw')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_draw',  'server')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_draw')." ADD `server` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '服务费';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_group')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_group',  'id')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_group')." ADD `id` int(11) NOT NULL  AUTO_INCREMENT;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_group')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_group',  'uniacid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_group')." ADD `uniacid` int(10) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_group')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_group',  'createtime')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_group')." ADD `createtime` int(11) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_group')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_group',  'mem')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_group')." ADD `mem` int(11) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_group')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_group',  'totalcost')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_group')." ADD `totalcost` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '总计花费';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_group')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_group',  'totalback')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_group')." ADD `totalback` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '总计返回';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_group')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_group',  'endtime')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_group')." ADD `endtime` int(11) NOT NULL   COMMENT '结束时间';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_group')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_group',  'status')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_group')." ADD `status` tinyint(1) NOT NULL   COMMENT '0正常 1已结束';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_groupbs')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_groupbs',  'id')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_groupbs')." ADD `id` int(11) NOT NULL  AUTO_INCREMENT;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_groupbs')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_groupbs',  'uniacid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_groupbs')." ADD `uniacid` int(10) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_groupbs')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_groupbs',  'createtime')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_groupbs')." ADD `createtime` int(11) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_groupbs')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_groupbs',  'uid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_groupbs')." ADD `uid` int(11) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_groupbs')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_groupbs',  'money')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_groupbs')." ADD `money` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_groupbs')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_groupbs',  'baoshi')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_groupbs')." ADD `baoshi` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_groupds')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_groupds',  'id')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_groupds')." ADD `id` int(11) NOT NULL  AUTO_INCREMENT;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_groupds')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_groupds',  'uniacid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_groupds')." ADD `uniacid` int(10) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_groupds')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_groupds',  'createtime')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_groupds')." ADD `createtime` int(11) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_groupds')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_groupds',  'uid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_groupds')." ADD `uid` int(11) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_groupds')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_groupds',  'takedid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_groupds')." ADD `takedid` int(11) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_groupds')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_groupds',  'taskid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_groupds')." ADD `taskid` int(11) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_groupds')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_groupds',  'yinp')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_groupds')." ADD `yinp` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_groupds')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_groupds',  'givedser')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_groupds')." ADD `givedser` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '给打赏者的';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_groupds')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_groupds',  'givedsed')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_groupds')." ADD `givedsed` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '给被打赏者的';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_grouplog')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_grouplog',  'id')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_grouplog')." ADD `id` int(11) NOT NULL  AUTO_INCREMENT;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_grouplog')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_grouplog',  'uniacid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_grouplog')." ADD `uniacid` int(10) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_grouplog')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_grouplog',  'createtime')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_grouplog')." ADD `createtime` int(11) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_grouplog')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_grouplog',  'uid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_grouplog')." ADD `uid` int(11) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_grouplog')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_grouplog',  'gid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_grouplog')." ADD `gid` int(11) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_grouplog')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_grouplog',  'cost')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_grouplog')." ADD `cost` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_grouplog')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_grouplog',  'geted')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_grouplog')." ADD `geted` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '最后活动';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_grouplog')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_grouplog',  'status')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_grouplog')." ADD `status` tinyint(1) NOT NULL   COMMENT '0未发放 1已发放';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_grouplog')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_grouplog',  'endtime')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_grouplog')." ADD `endtime` int(11) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_guysort')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_guysort',  'id')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_guysort')." ADD `id` int(11) NOT NULL  AUTO_INCREMENT;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_guysort')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_guysort',  'uniacid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_guysort')." ADD `uniacid` int(11) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_guysort')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_guysort',  'img')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_guysort')." ADD `img` varchar(500);");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_guysort')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_guysort',  'number')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_guysort')." ADD `number` int(11) NOT NULL   COMMENT '排序，越大越前';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_guysort')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_guysort',  'name')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_guysort')." ADD `name` varchar(64);");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_imess')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_imess',  'id')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_imess')." ADD `id` int(11) NOT NULL  AUTO_INCREMENT;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_imess')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_imess',  'uniacid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_imess')." ADD `uniacid` int(10) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_imess')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_imess',  'openid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_imess')." ADD `openid` varchar(64);");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_imess')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_imess',  'uid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_imess')." ADD `uid` int(11) NOT NULL   COMMENT 'member uid';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_imess')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_imess',  'type')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_imess')." ADD `type` varchar(120) NOT NULL   COMMENT '类型';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_imess')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_imess',  'targetid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_imess')." ADD `targetid` int(11) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_imess')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_imess',  'remark')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_imess')." ADD `remark` varchar(255)    COMMENT '备注';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_imess')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_imess',  'status')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_imess')." ADD `status` tinyint(1) NOT NULL   COMMENT '0未读 1已读';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_imess')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_imess',  'url')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_imess')." ADD `url` varchar(255);");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_imess')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_imess',  'createtime')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_imess')." ADD `createtime` int(11) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_imess')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_imess',  'content')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_imess')." ADD `content` varchar(2000);");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_imess')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_imess',  'isbig')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_imess')." ADD `isbig` tinyint(1) NOT NULL   COMMENT '0弹窗提醒 1不弹窗提醒';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_imess')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_imess',  'istop')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_imess')." ADD `istop` tinyint(1) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_instruct')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_instruct',  'id')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_instruct')." ADD `id` int(11) NOT NULL  AUTO_INCREMENT;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_instruct')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_instruct',  'uniacid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_instruct')." ADD `uniacid` int(10) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_instruct')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_instruct',  'down')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_instruct')." ADD `down` text();");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_instruct')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_instruct',  'set')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_instruct')." ADD `set` text();");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_instruct')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_instruct',  'level')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_instruct')." ADD `level` text();");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_instructa')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_instructa',  'id')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_instructa')." ADD `id` int(11) NOT NULL  AUTO_INCREMENT;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_instructa')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_instructa',  'uniacid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_instructa')." ADD `uniacid` int(10) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_instructa')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_instructa',  'content')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_instructa')." ADD `content` text();");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_instructa')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_instructa',  'type')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_instructa')." ADD `type` tinyint(1) NOT NULL   COMMENT '0答案';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_mess')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_mess',  'id')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_mess')." ADD `id` int(11) NOT NULL  AUTO_INCREMENT;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_mess')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_mess',  'uniacid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_mess')." ADD `uniacid` int(10) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_mess')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_mess',  'openid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_mess')." ADD `openid` varchar(64);");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_mess')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_mess',  'taked')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_mess')." ADD `taked` tinyint(1) NOT NULL   COMMENT '任务被回复通知 0开启 1关闭';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_mess')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_mess',  'messaged')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_mess')." ADD `messaged` tinyint(1) NOT NULL   COMMENT '被留言提问';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_mess')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_mess',  'count')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_mess')." ADD `count` tinyint(1) NOT NULL   COMMENT '任务被结算';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_mess')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_mess',  'reply')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_mess')." ADD `reply` tinyint(1) NOT NULL   COMMENT '回复被处理';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_mess')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_mess',  'getmessage')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_mess')." ADD `getmessage` tinyint(1) NOT NULL   COMMENT '留言被回复';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_mess')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_mess',  'getpri')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_mess')." ADD `getpri` tinyint(1) NOT NULL   COMMENT '获得私包任务';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_mess')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_mess',  'newdown')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_mess')." ADD `newdown` tinyint(1) NOT NULL   COMMENT '新增小弟';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_mess')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_mess',  'downmoney')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_mess')." ADD `downmoney` tinyint(1) NOT NULL   COMMENT '小弟提成';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_mess')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_mess',  'downact')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_mess')." ADD `downact` tinyint(1) NOT NULL   COMMENT '获赠活跃度通知';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_mess')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_mess',  'usesuborder')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_mess')." ADD `usesuborder` tinyint(1) NOT NULL   COMMENT '私包任务 提交订单内容';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_mess')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_mess',  'useaddcontent')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_mess')." ADD `useaddcontent` tinyint(1) NOT NULL   COMMENT '使用任务补充内容';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_mess')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_mess',  'newtask')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_mess')." ADD `newtask` tinyint(1) NOT NULL   COMMENT '新任务通知';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_mess')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_mess',  'userid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_mess')." ADD `userid` int(11) NOT NULL   COMMENT '会员id';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_message')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_message',  'id')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_message')." ADD `id` int(11) NOT NULL  AUTO_INCREMENT;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_message')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_message',  'uniacid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_message')." ADD `uniacid` int(10) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_message')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_message',  'type')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_message')." ADD `type` tinyint(1) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_message')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_message',  'str')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_message')." ADD `str` varchar(64);");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_message')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_message',  'openid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_message')." ADD `openid` varchar(64);");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_message')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_message',  'userid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_message')." ADD `userid` int(11) NOT NULL   COMMENT '会员id';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_moneylog')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_moneylog',  'id')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_moneylog')." ADD `id` int(11) NOT NULL  AUTO_INCREMENT;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_moneylog')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_moneylog',  'uniacid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_moneylog')." ADD `uniacid` int(10) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_moneylog')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_moneylog',  'openid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_moneylog')." ADD `openid` varchar(64) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_moneylog')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_moneylog',  'taskid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_moneylog')." ADD `taskid` int(10) NOT NULL   COMMENT '对应的任务id';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_moneylog')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_moneylog',  'money')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_moneylog')." ADD `money` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '金额';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_moneylog')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_moneylog',  'status')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_moneylog')." ADD `status` tinyint(3) NOT NULL   COMMENT '资金记录状态';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_moneylog')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_moneylog',  'time')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_moneylog')." ADD `time` int(10) NOT NULL   COMMENT '变化时间';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_moneylog')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_moneylog',  'type')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_moneylog')." ADD `type` tinyint(3) NOT NULL   COMMENT '资金记录类型 1任务资金，2平台使用费，3提现扣除余额，4任务完成收入 5私包任务支出 6私包任务被取消退回资金 7私包任务收益 8任务自动结束退回资金，9加急扣除，10管理员变动，11提现退回，12充值收入，13任务不通过退回';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_moneylog')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_moneylog',  'aftermoney')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_moneylog')." ADD `aftermoney` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '变动后余额';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_moneylog')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_moneylog',  'mtype')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_moneylog')." ADD `mtype` tinyint(1) NOT NULL   COMMENT '1余额记录 2保证金记录';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_moneylog')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_moneylog',  'userid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_moneylog')." ADD `userid` int(11) NOT NULL   COMMENT '会员id';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_paylog')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_paylog',  'id')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_paylog')." ADD `id` int(11) NOT NULL  AUTO_INCREMENT;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_paylog')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_paylog',  'uniacid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_paylog')." ADD `uniacid` int(10) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_paylog')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_paylog',  'openid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_paylog')." ADD `openid` varchar(64) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_paylog')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_paylog',  'uid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_paylog')." ADD `uid` int(10) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_paylog')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_paylog',  'time')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_paylog')." ADD `time` int(11) NOT NULL   COMMENT '支付或者创建时间';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_paylog')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_paylog',  'status')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_paylog')." ADD `status` tinyint(1) NOT NULL   COMMENT '类型 0未支付  1已支付';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_paylog')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_paylog',  'type')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_paylog')." ADD `type` tinyint(1) NOT NULL   COMMENT '类型 1任务保证金 2充值';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_paylog')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_paylog',  'fee')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_paylog')." ADD `fee` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '金额';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_paylog')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_paylog',  'orderid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_paylog')." ADD `orderid` varchar(64) NOT NULL   COMMENT '内部订单编号';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_paylog')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_paylog',  'uorderid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_paylog')." ADD `uorderid` varchar(64) NOT NULL   COMMENT '微信订单编号';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_paylog')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_paylog',  'userid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_paylog')." ADD `userid` int(11) NOT NULL   COMMENT '会员id';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_paylog')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_paylog',  'utype')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_paylog')." ADD `utype` tinyint(3) NOT NULL   COMMENT '升级会员类型';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_privatetask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_privatetask',  'id')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_privatetask')." ADD `id` int(11) NOT NULL  AUTO_INCREMENT;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_privatetask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_privatetask',  'uniacid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_privatetask')." ADD `uniacid` int(10) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_privatetask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_privatetask',  'puber')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_privatetask')." ADD `puber` varchar(64) NOT NULL   COMMENT '发起者';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_privatetask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_privatetask',  'accepter')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_privatetask')." ADD `accepter` varchar(64);");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_privatetask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_privatetask',  'workeropenid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_privatetask')." ADD `workeropenid` varchar(64) NOT NULL   COMMENT '任务执行者openid';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_privatetask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_privatetask',  'bossopenid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_privatetask')." ADD `bossopenid` varchar(64) NOT NULL   COMMENT '任务雇主openid';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_privatetask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_privatetask',  'tasktitle')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_privatetask')." ADD `tasktitle` varchar(255) NOT NULL   COMMENT '任务内容';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_privatetask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_privatetask',  'taskmoney')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_privatetask')." ADD `taskmoney` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '任务金额';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_privatetask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_privatetask',  'workerserver')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_privatetask')." ADD `workerserver` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '扣做任务者 平台使用费';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_privatetask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_privatetask',  'images')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_privatetask')." ADD `images` varchar(1500) NOT NULL   COMMENT '任务图片';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_privatetask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_privatetask',  'limittime')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_privatetask')." ADD `limittime` int(11) NOT NULL   COMMENT '限时，小时为单位';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_privatetask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_privatetask',  'createtime')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_privatetask')." ADD `createtime` int(11) NOT NULL   COMMENT '创建时间';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_privatetask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_privatetask',  'accepttime')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_privatetask')." ADD `accepttime` int(11) NOT NULL   COMMENT '接受者处理时间';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_privatetask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_privatetask',  'workerdealtime')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_privatetask')." ADD `workerdealtime` int(11) NOT NULL   COMMENT '执行者提交完成.或取消时间';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_privatetask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_privatetask',  'bossdealtime')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_privatetask')." ADD `bossdealtime` int(11) NOT NULL   COMMENT '雇主确认或拒绝时间';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_privatetask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_privatetask',  'complaintime')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_privatetask')." ADD `complaintime` int(11) NOT NULL   COMMENT '雇员同意拒绝或投诉时间';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_privatetask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_privatetask',  'admindealtime')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_privatetask')." ADD `admindealtime` int(11) NOT NULL   COMMENT '管理员对投诉处理时间';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_privatetask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_privatetask',  'status')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_privatetask')." ADD `status` tinyint(2) NOT NULL   COMMENT '任务状态 0等待确认，1任务被拒绝，2执行中，3雇员提交完成待雇主确认， 4雇员主动取消执行任务，5雇员没有提交完成而自动取消，6雇主确认后任务完成，7任务完成效果不好被雇主拒绝，8任务被雇员同意拒绝而结束，9任务被雇员投诉阶段，10管理员处理后结束,11雇主没有确认完成任务自动确认完成，12待雇员接受拒绝或投诉状态而没有处理自动接受，13管理员判给雇员而结束，14管理员判给雇主而结束';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_privatetask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_privatetask',  'type')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_privatetask')." ADD `type` tinyint(1) NOT NULL   COMMENT '类型，1索要的任务。2主动发给对方任务，2是已支付的';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_privatetask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_privatetask',  'complainreason')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_privatetask')." ADD `complainreason` varchar(200) NOT NULL   COMMENT '投诉内容';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_privatetask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_privatetask',  'admindealresult')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_privatetask')." ADD `admindealresult` varchar(200) NOT NULL   COMMENT '管理员处理结果';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_privatetask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_privatetask',  'refusereason')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_privatetask')." ADD `refusereason` varchar(200) NOT NULL   COMMENT '雇主拒绝理由';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_privatetask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_privatetask',  'overtime0')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_privatetask')." ADD `overtime0` int(11) NOT NULL   COMMENT '待确认的任务自动取消时间';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_privatetask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_privatetask',  'overtime2')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_privatetask')." ADD `overtime2` int(11) NOT NULL   COMMENT '执行中任务自动结束时间';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_privatetask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_privatetask',  'overtime3')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_privatetask')." ADD `overtime3` int(11) NOT NULL   COMMENT '待雇主确认状态自动确认时间';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_privatetask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_privatetask',  'overtime7')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_privatetask')." ADD `overtime7` int(11) NOT NULL   COMMENT '雇员确认拒绝状态自动确认时间';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_privatetask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_privatetask',  'completecontent')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_privatetask')." ADD `completecontent` text()    COMMENT '回复的内容';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_privatetask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_privatetask',  'bossserver')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_privatetask')." ADD `bossserver` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '扣雇主服务费';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_privatetask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_privatetask',  'isend')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_privatetask')." ADD `isend` tinyint(1) NOT NULL   COMMENT '0未结束 1已结束';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_privatetask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_privatetask',  'giveparent')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_privatetask')." ADD `giveparent` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '上级奖励';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_privatetask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_privatetask',  'pubuid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_privatetask')." ADD `pubuid` int(11) NOT NULL   COMMENT '会员id';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_privatetask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_privatetask',  'acceptuid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_privatetask')." ADD `acceptuid` int(11) NOT NULL   COMMENT '会员id';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_privatetask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_privatetask',  'bossuid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_privatetask')." ADD `bossuid` int(11) NOT NULL   COMMENT '会员id';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_privatetask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_privatetask',  'workeruid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_privatetask')." ADD `workeruid` int(11) NOT NULL   COMMENT '会员id';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_puber')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_puber',  'id')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_puber')." ADD `id` int(11) NOT NULL  AUTO_INCREMENT;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_puber')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_puber',  'uniacid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_puber')." ADD `uniacid` int(11) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_puber')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_puber',  'headimg')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_puber')." ADD `headimg` varchar(500);");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_puber')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_puber',  'nickname')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_puber')." ADD `nickname` varchar(32);");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_puber')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_puber',  'falsepub')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_puber')." ADD `falsepub` int(11) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_puber')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_puber',  'falsetake')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_puber')." ADD `falsetake` int(11) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_puber')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_puber',  'falsedep')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_puber')." ADD `falsedep` int(11) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_puber')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_puber',  'pub')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_puber')." ADD `pub` int(11) NOT NULL   COMMENT '发布量';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_puber')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_puber',  'take')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_puber')." ADD `take` int(11) NOT NULL   COMMENT '采纳量';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_puber')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_puber',  'cost')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_puber')." ADD `cost` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '支出';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_puber')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_puber',  'pubnum')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_puber')." ADD `pubnum` int(11) NOT NULL   COMMENT '发布任务个数';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_readtask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_readtask',  'id')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_readtask')." ADD `id` int(11) NOT NULL  AUTO_INCREMENT;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_readtask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_readtask',  'type')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_readtask')." ADD `type` tinyint(3) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_readtask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_readtask',  'uid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_readtask')." ADD `uid` int(11) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_readtask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_readtask',  'tid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_readtask')." ADD `tid` int(11) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_remindlog')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_remindlog',  'id')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_remindlog')." ADD `id` int(10) NOT NULL  AUTO_INCREMENT;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_remindlog')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_remindlog',  'uniacid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_remindlog')." ADD `uniacid` int(10) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_remindlog')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_remindlog',  'createtime')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_remindlog')." ADD `createtime` int(11) NOT NULL   COMMENT '时间';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_remindlog')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_remindlog',  'takedid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_remindlog')." ADD `takedid` int(11) NOT NULL   COMMENT '回复id';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_remindlog')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_remindlog',  'content')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_remindlog')." ADD `content` varchar(500)    COMMENT '内容';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_remindlog')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_remindlog',  'type')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_remindlog')." ADD `type` tinyint(1) NOT NULL   COMMENT '0前端发的 1后端发的';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_remindlog')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_remindlog',  'images')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_remindlog')." ADD `images` varchar(2500)    COMMENT '图片';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_remindlog')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_remindlog',  'mtype')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_remindlog')." ADD `mtype` tinyint(1) NOT NULL   COMMENT '0提醒 1补充的内容';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_scan')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_scan',  'id')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_scan')." ADD `id` int(10) NOT NULL  AUTO_INCREMENT;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_scan')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_scan',  'uniacid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_scan')." ADD `uniacid` int(10) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_scan')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_scan',  'times')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_scan')." ADD `times` int(11) NOT NULL   COMMENT '访问次数';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_scan')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_scan',  'pubed')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_scan')." ADD `pubed` int(11) NOT NULL   COMMENT '已发布数量';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_scan')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_scan',  'comed')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_scan')." ADD `comed` int(11) NOT NULL   COMMENT '已完成';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_scan')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_scan',  'commpubed')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_scan')." ADD `commpubed` int(11) NOT NULL   COMMENT '普通任务发布数量';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_scan')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_scan',  'commcomed')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_scan')." ADD `commcomed` int(11) NOT NULL   COMMENT '普通任务完成数量';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_scan')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_scan',  'privatepubed')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_scan')." ADD `privatepubed` int(11) NOT NULL   COMMENT '私包任务发布数量';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_scan')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_scan',  'privatecomed')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_scan')." ADD `privatecomed` int(11) NOT NULL   COMMENT '私包任务完成数量';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_scan')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_scan',  'usepubed')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_scan')." ADD `usepubed` int(11) NOT NULL   COMMENT '试用任务发布数量';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_scan')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_scan',  'usecomed')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_scan')." ADD `usecomed` int(11) NOT NULL   COMMENT '试用任务完成数量';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_scan')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_scan',  'tbpubed')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_scan')." ADD `tbpubed` int(11) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_scan')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_scan',  'tbcomed')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_scan')." ADD `tbcomed` int(11) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_selfposter')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_selfposter',  'id')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_selfposter')." ADD `id` int(11) NOT NULL  AUTO_INCREMENT;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_selfposter')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_selfposter',  'uniacid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_selfposter')." ADD `uniacid` int(11) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_selfposter')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_selfposter',  'params')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_selfposter')." ADD `params` mediumtext();");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_selfposter')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_selfposter',  'bgimg')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_selfposter')." ADD `bgimg` varchar(500);");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_selfposter')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_selfposter',  'key')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_selfposter')." ADD `key` varchar(100)    COMMENT '关键词';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_selfposter')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_selfposter',  'pid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_selfposter')." ADD `pid` int(11) NOT NULL   COMMENT '规则id';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_selfposter')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_selfposter',  'content')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_selfposter')." ADD `content` varchar(2000)    COMMENT '扫码后提示';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_selfposter')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_selfposter',  'ccontent')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_selfposter')." ADD `ccontent` varchar(2000)    COMMENT '生成海报后提示';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_selfqrcode')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_selfqrcode',  'id')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_selfqrcode')." ADD `id` int(11) NOT NULL  AUTO_INCREMENT;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_selfqrcode')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_selfqrcode',  'uniacid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_selfqrcode')." ADD `uniacid` int(11) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_selfqrcode')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_selfqrcode',  'openid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_selfqrcode')." ADD `openid` varchar(64);");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_selfqrcode')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_selfqrcode',  'qrcodeid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_selfqrcode')." ADD `qrcodeid` int(11) NOT NULL   COMMENT 'qrcode表里的id';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_selfqrcode')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_selfqrcode',  'sence')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_selfqrcode')." ADD `sence` varchar(64);");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_selfqrcode')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_selfqrcode',  'expire')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_selfqrcode')." ADD `expire` int(11) NOT NULL   COMMENT '过期时间';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_selfqrcode')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_selfqrcode',  'userid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_selfqrcode')." ADD `userid` int(11) NOT NULL   COMMENT '会员id';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_selfquest')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_selfquest',  'id')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_selfquest')." ADD `id` int(11) NOT NULL  AUTO_INCREMENT;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_selfquest')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_selfquest',  'uniacid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_selfquest')." ADD `uniacid` int(11) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_selfquest')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_selfquest',  'title')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_selfquest')." ADD `title` varchar(500);");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_selfquest')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_selfquest',  'type')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_selfquest')." ADD `type` tinyint(1) NOT NULL   COMMENT '0用户答疑 1商家答疑';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_selfquest')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_selfquest',  'number')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_selfquest')." ADD `number` int(11) NOT NULL   COMMENT '排序序号，越大越前';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_selfquest')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_selfquest',  'content')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_selfquest')." ADD `content` mediumtext()    COMMENT '内容';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_selfquest')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_selfquest',  'settype')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_selfquest')." ADD `settype` tinyint(1) NOT NULL   COMMENT '1系统添加的 0自己添加的';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_selfquest')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_selfquest',  'status')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_selfquest')." ADD `status` tinyint(1) NOT NULL   COMMENT '0正常 1隐藏';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_sign')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_sign',  'id')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_sign')." ADD `id` int(11) NOT NULL  AUTO_INCREMENT;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_sign')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_sign',  'uniacid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_sign')." ADD `uniacid` int(10) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_sign')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_sign',  'createtime')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_sign')." ADD `createtime` int(11) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_sign')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_sign',  'uid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_sign')." ADD `uid` int(11) NOT NULL   COMMENT 'uid';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_sign')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_sign',  'day')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_sign')." ADD `day` varchar(22);");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_sign')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_sign',  'give')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_sign')." ADD `give` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_sign')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_sign',  'flag')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_sign')." ADD `flag` tinyint(1) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_slider')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_slider',  'id')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_slider')." ADD `id` int(11) NOT NULL  AUTO_INCREMENT;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_slider')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_slider',  'uniacid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_slider')." ADD `uniacid` int(11) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_slider')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_slider',  'img')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_slider')." ADD `img` varchar(500);");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_slider')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_slider',  'number')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_slider')." ADD `number` int(11) NOT NULL   COMMENT '排序，越大越前';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_slider')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_slider',  'url')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_slider')." ADD `url` varchar(500);");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_slider')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_slider',  'isindex')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_slider')." ADD `isindex` tinyint(1) NOT NULL DEFAULT NULL DEFAULT '1'  COMMENT '0不显示 1主页显示';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_slider')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_slider',  'isusetask')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_slider')." ADD `isusetask` tinyint(1)  DEFAULT NULL DEFAULT '1'  COMMENT '0不显示 1试用任务页面显示';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_slider')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_slider',  'istbtask')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_slider')." ADD `istbtask` tinyint(3) NOT NULL   COMMENT '0不显示';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_slider')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_slider',  'isguy')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_slider')." ADD `isguy` tinyint(1) NOT NULL   COMMENT '找人页面';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_slider')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_slider',  'dayy')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_slider')." ADD `dayy` int(11) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_step')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_step',  'id')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_step')." ADD `id` int(11) NOT NULL  AUTO_INCREMENT;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_step')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_step',  'step')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_step')." ADD `step` text();");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_step')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_step',  'uniacid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_step')." ADD `uniacid` int(11) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_step')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_step',  'istemp')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_step')." ADD `istemp` tinyint(1) NOT NULL   COMMENT '0不是模板 1模板';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_step')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_step',  'name')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_step')." ADD `name` varchar(122);");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tabbar')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tabbar',  'id')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tabbar')." ADD `id` int(10) NOT NULL  AUTO_INCREMENT;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tabbar')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tabbar',  'uniacid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tabbar')." ADD `uniacid` int(10) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tabbar')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tabbar',  'name')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tabbar')." ADD `name` varchar(100) NOT NULL   COMMENT '分类名称';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tabbar')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tabbar',  'number')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tabbar')." ADD `number` int(11) NOT NULL   COMMENT '越大越前';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tabbar')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tabbar',  'img')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tabbar')." ADD `img` varchar(500);");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tabbar')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tabbar',  'url')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tabbar')." ADD `url` varchar(500);");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tabbar')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tabbar',  'color')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tabbar')." ADD `color` varchar(22);");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tabbar')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tabbar',  'actcolor')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tabbar')." ADD `actcolor` varchar(22);");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tabbar')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tabbar',  'actimg')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tabbar')." ADD `actimg` varchar(555);");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_taked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_taked',  'id')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_taked')." ADD `id` int(11) NOT NULL  AUTO_INCREMENT;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_taked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_taked',  'uniacid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_taked')." ADD `uniacid` int(10) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_taked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_taked',  'openid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_taked')." ADD `openid` varchar(64)    COMMENT '抢的人';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_taked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_taked',  'taskid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_taked')." ADD `taskid` int(11) NOT NULL   COMMENT '任务id';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_taked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_taked',  'createtime')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_taked')." ADD `createtime` int(11) NOT NULL   COMMENT '创建时间';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_taked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_taked',  'waittime')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_taked')." ADD `waittime` int(11) NOT NULL   COMMENT '能回复的时间';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_taked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_taked',  'endtime')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_taked')." ADD `endtime` int(11) NOT NULL   COMMENT '释放时间，如果时间大于现在占用，小于现在已释放';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_taked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_taked',  'status')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_taked')." ADD `status` tinyint(1) NOT NULL   COMMENT '0刚抢到还没回复 1已回复 2已采纳 3已被拒绝';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_taked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_taked',  'content')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_taked')." ADD `content` text()    COMMENT '回复内容';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_taked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_taked',  'money')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_taked')." ADD `money` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '赏金';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_taked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_taked',  'server')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_taked')." ADD `server` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '扣除平台费';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_taked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_taked',  'ewai')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_taked')." ADD `ewai` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '额外奖励';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_taked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_taked',  'replytime')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_taked')." ADD `replytime` int(11) NOT NULL   COMMENT '回复时间';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_taked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_taked',  'dealtime')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_taked')." ADD `dealtime` int(11) NOT NULL   COMMENT '雇主或系统处理时间';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_taked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_taked',  'images')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_taked')." ADD `images` varchar(2000)    COMMENT '回复的图片';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_taked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_taked',  'puber')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_taked')." ADD `puber` varchar(64)    COMMENT '发布者openid  方便计算数量限制';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_taked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_taked',  'giveparent')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_taked')." ADD `giveparent` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '给上级奖励';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_taked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_taked',  'continueid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_taked')." ADD `continueid` int(11) NOT NULL   COMMENT '任务对于的连续id';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_taked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_taked',  'reason')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_taked')." ADD `reason` varchar(200)    COMMENT '拒绝理由';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_taked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_taked',  'ip')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_taked')." ADD `ip` varchar(42);");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_taked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_taked',  'isscan')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_taked')." ADD `isscan` tinyint(1) NOT NULL   COMMENT '0可以浏览看见 1不允许看见';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_taked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_taked',  'subform')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_taked')." ADD `subform` text();");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_taked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_taked',  'userid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_taked')." ADD `userid` int(11) NOT NULL   COMMENT '会员id';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_taked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_taked',  'pubuid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_taked')." ADD `pubuid` int(11) NOT NULL   COMMENT '会员id';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_taked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_taked',  'twoupmoney')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_taked')." ADD `twoupmoney` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '上上级提成';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_taked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_taked',  'threeupmoney')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_taked')." ADD `threeupmoney` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '上上上级奖励';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_taked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_taked',  'adminadd')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_taked')." ADD `adminadd` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '管理员增加余额数值';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_taked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_taked',  'ds')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_taked')." ADD `ds` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_taked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_taked',  'type')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_taked')." ADD `type` tinyint(1) NOT NULL   COMMENT '0正常 1虚假的';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_taked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_taked',  'nick')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_taked')." ADD `nick` varchar(32)    COMMENT '虚假昵称';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_taked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_taked',  'headimg')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_taked')." ADD `headimg` varchar(555)    COMMENT '虚假头像';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_taked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_taked',  'isfalse')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_taked')." ADD `isfalse` tinyint(1) NOT NULL   COMMENT '0正常 1虚假的';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_taked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_taked',  'falseuid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_taked')." ADD `falseuid` int(11) NOT NULL   COMMENT '虚拟会员id';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_taked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_taked',  'isremind')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_taked')." ADD `isremind` tinyint(1) NOT NULL   COMMENT '0未提醒 1已提醒';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_taked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_taked',  'gid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_taked')." ADD `gid` varchar(32);");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'id')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `id` int(11) NOT NULL  AUTO_INCREMENT;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'uniacid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `uniacid` int(10) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'title')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `title` varchar(500);");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'content')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `content` text()    COMMENT '任务内容';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'images')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `images` varchar(2000)    COMMENT '图片';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'iska')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `iska` tinyint(1) NOT NULL   COMMENT '0不卡首屏 1卡首屏';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'num')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `num` int(11) NOT NULL   COMMENT '任务数量';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'money')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `money` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '任务单价';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'replytime')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `replytime` int(3) NOT NULL   COMMENT '等待回复时间';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'limitnum')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `limitnum` int(6) NOT NULL   COMMENT '限制回复数量';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'sex')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `sex` tinyint(1) NOT NULL   COMMENT '0不限制性别 1男性可回复 2女性可回复';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'ishide')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `ishide` tinyint(1) NOT NULL   COMMENT '0不隐藏 1隐藏';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'continue')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `continue` tinyint(1) NOT NULL   COMMENT '0不连续发布 1连续发布';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'continueid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `continueid` int(11) NOT NULL   COMMENT '连续发布任务的id';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'kagoodid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `kagoodid` varchar(64)    COMMENT '卡首屏商品id';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'kakey')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `kakey` varchar(2000)    COMMENT '卡首屏关键字';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'start')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `start` int(11) NOT NULL   COMMENT '开始时间';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'end')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `end` int(11) NOT NULL   COMMENT '结束时间';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'status')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `status` tinyint(3) NOT NULL   COMMENT '0正常 1审核中 2下架';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'closereason')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `closereason` varchar(300)    COMMENT '审核不通过原因';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'istop')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `istop` tinyint(1) NOT NULL   COMMENT '0未置顶 1置顶';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'puber')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `puber` varchar(64)    COMMENT '发布者';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'sortid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `sortid` int(11) NOT NULL   COMMENT '分类id';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'isimage')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `isimage` tinyint(1) NOT NULL   COMMENT '0不限制图片 1抢到后才可见';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'iscount')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `iscount` tinyint(1) NOT NULL   COMMENT '0还未结算 1已结算结束';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'backmoney')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `backmoney` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '结算退回钱';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'counttime')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `counttime` int(11) NOT NULL   COMMENT '结算时间';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'costserver')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `costserver` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '花费的服务费';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'costka')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `costka` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '花费的卡首屏';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'isempty')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `isempty` tinyint(1) NOT NULL   COMMENT '0没有被抢光 1被抢光了';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'scan')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `scan` int(11) NOT NULL   COMMENT '浏览量';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'isstart')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `isstart` tinyint(1) NOT NULL   COMMENT '0已开始 1未开始';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'costtop')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `costtop` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '置顶费用';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'createtime')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `createtime` int(11) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'link')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `link` varchar(2000);");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'hidecontent')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `hidecontent` text()    COMMENT '定制的 隐藏内容';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'autoaddnum')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `autoaddnum` int(8) NOT NULL   COMMENT '定制的 自动追加数量';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'autotime')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `autotime` int(8) NOT NULL   COMMENT '定制的 自动追加的次数';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'costautoadd')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `costautoadd` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '定制的 自动追加花费';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'backautoadd')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `backautoadd` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '定制的 退回自动追加';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'skiptype')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `skiptype` tinyint(1) NOT NULL   COMMENT '定制的 0链接跳转 1口令跳转';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'isautoadd')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `isautoadd` tinyint(1) NOT NULL   COMMENT '定制的 0不自动追加 1自动追加';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'totalauto')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `totalauto` int(11) NOT NULL   COMMENT '定制的 总计自动追加数量';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'autoadded')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `autoadded` int(11) NOT NULL   COMMENT '定制的 已增加的自动追加';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'paymoney')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `paymoney` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '需支付金额';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'prizetype')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `prizetype` tinyint(1) NOT NULL   COMMENT '奖励类型 0赏金 1实物';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'prizetitle')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `prizetitle` varchar(500)    COMMENT '奖励标题';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'prizeimg')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `prizeimg` varchar(350)    COMMENT '奖励图片';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'type')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `type` tinyint(1) NOT NULL   COMMENT '0普通任务 1试用任务';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'pic')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `pic` varchar(350)    COMMENT '淘宝商品图片';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'taokey')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `taokey` varchar(150)    COMMENT '下单商品淘口令';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'findtype')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `findtype` tinyint(1) NOT NULL   COMMENT 'WQ 搜商品方式 0直接跳转 1关键字搜 2都关闭';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'findkey')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `findkey` varchar(2500)    COMMENT '搜商品关键词';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'isarealimit')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `isarealimit` tinyint(1) NOT NULL   COMMENT '0不做区域限制 1限制到区县 2限制到市';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'province')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `province` varchar(64)    COMMENT '限制的省份';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'city')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `city` varchar(64)    COMMENT '城市';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'country')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `country` varchar(64)    COMMENT '区县';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'costfindkey')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `costfindkey` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '试用任务花费的关键词搜';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'ispause')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `ispause` tinyint(1) NOT NULL   COMMENT '开启任务';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'isform')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `isform` tinyint(1) NOT NULL   COMMENT '0不用提交表单 1提交表单';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'gtitle')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `gtitle` varchar(500)    COMMENT '淘宝商品标题';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'istaskform')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `istaskform` tinyint(1) NOT NULL   COMMENT '0不使用模板 1使用回复模板';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'formid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `formid` int(11) NOT NULL   COMMENT '模板id';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'userid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `userid` int(11) NOT NULL   COMMENT '会员id';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'giveparent')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `giveparent` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '发任务上级提成';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'givetwo')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `givetwo` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '上上级';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'givethree')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `givethree` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'address')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `address` varchar(255)    COMMENT '任务地址';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'readprice')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `readprice` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '查看答案价格';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'isread')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `isread` tinyint(3) NOT NULL   COMMENT '0不可看 1可看';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'falsepuber')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `falsepuber` int(11) NOT NULL   COMMENT '虚拟id';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'falsenum')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `falsenum` int(11) NOT NULL   COMMENT '虚拟数量';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'useric')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `useric` varchar(200)    COMMENT '虚假昵称';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'idcode')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `idcode` varchar(15);");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'headimg')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `headimg` varchar(255);");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'levellim')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `levellim` tinyint(1) NOT NULL   COMMENT '0不限制 1一级可接 2二级可接 3二三级可接';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'mark')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `mark` varchar(200);");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'gid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `gid` varchar(32);");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_task')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_task',  'stepid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_task')." ADD `stepid` int(11) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_taskform')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_taskform',  'id')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_taskform')." ADD `id` int(10) NOT NULL  AUTO_INCREMENT;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_taskform')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_taskform',  'uniacid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_taskform')." ADD `uniacid` int(10) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_taskform')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_taskform',  'name')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_taskform')." ADD `name` varchar(100) NOT NULL   COMMENT '分类名称';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_taskform')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_taskform',  'number')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_taskform')." ADD `number` int(11) NOT NULL   COMMENT '越大越前';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_taskform')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_taskform',  'form')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_taskform')." ADD `form` text();");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_taskform')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_taskform',  'type')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_taskform')." ADD `type` tinyint(1) NOT NULL   COMMENT '0普通任务';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_taskmessage')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_taskmessage',  'id')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_taskmessage')." ADD `id` int(11) NOT NULL  AUTO_INCREMENT;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_taskmessage')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_taskmessage',  'uniacid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_taskmessage')." ADD `uniacid` int(10) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_taskmessage')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_taskmessage',  'content')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_taskmessage')." ADD `content` varchar(1000);");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_taskmessage')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_taskmessage',  'openid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_taskmessage')." ADD `openid` varchar(64);");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_taskmessage')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_taskmessage',  'parent')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_taskmessage')." ADD `parent` int(11) NOT NULL   COMMENT '回复的id';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_taskmessage')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_taskmessage',  'taskid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_taskmessage')." ADD `taskid` int(11) NOT NULL   COMMENT '任务id';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_taskmessage')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_taskmessage',  'time')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_taskmessage')." ADD `time` int(11) NOT NULL   COMMENT '留言时间';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_taskmessage')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_taskmessage',  'isadmin')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_taskmessage')." ADD `isadmin` tinyint(1) NOT NULL   COMMENT '0不是管理员发布的任务，1是的';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_taskmessage')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_taskmessage',  'type')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_taskmessage')." ADD `type` tinyint(1) NOT NULL   COMMENT '0普通任务';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_taskmessage')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_taskmessage',  'userid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_taskmessage')." ADD `userid` int(11) NOT NULL   COMMENT '会员id';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tasksort')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tasksort',  'id')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tasksort')." ADD `id` int(10) NOT NULL  AUTO_INCREMENT;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tasksort')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tasksort',  'uniacid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tasksort')." ADD `uniacid` int(10) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tasksort')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tasksort',  'name')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tasksort')." ADD `name` varchar(100) NOT NULL   COMMENT '分类名称';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tasksort')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tasksort',  'order')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tasksort')." ADD `order` int(10) NOT NULL   COMMENT '分类排序，越大越前';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tasksort')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tasksort',  'title')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tasksort')." ADD `title` varchar(1000)    COMMENT '默认发布任务的标题';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tasksort')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tasksort',  'content')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tasksort')." ADD `content` text()    COMMENT '默认 任务内容';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tasksort')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tasksort',  'number')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tasksort')." ADD `number` int(11) NOT NULL   COMMENT '越大越前';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tasksort')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tasksort',  'status')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tasksort')." ADD `status` tinyint(1) NOT NULL   COMMENT '0正常 1下线';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tasksort')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tasksort',  'img')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tasksort')." ADD `img` varchar(500);");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tasksort')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tasksort',  'dmoney')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tasksort')." ADD `dmoney` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '默认金额';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tasksort')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tasksort',  'other')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tasksort')." ADD `other` mediumtext();");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbblack')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbblack',  'id')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbblack')." ADD `id` int(10) NOT NULL  AUTO_INCREMENT;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbblack')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbblack',  'uniacid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbblack')." ADD `uniacid` int(10) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbblack')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbblack',  'openid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbblack')." ADD `openid` varchar(64)    COMMENT '会员openid';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbblack')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbblack',  'target')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbblack')." ADD `target` varchar(64)    COMMENT '黑名单目标';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbblack')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbblack',  'userid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbblack')." ADD `userid` int(11) NOT NULL   COMMENT '会员id';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbblack')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbblack',  'targetuid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbblack')." ADD `targetuid` int(11) NOT NULL   COMMENT '会员id';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbcert')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbcert',  'id')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbcert')." ADD `id` int(10) NOT NULL  AUTO_INCREMENT;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbcert')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbcert',  'uniacid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbcert')." ADD `uniacid` int(10) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbcert')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbcert',  'createtime')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbcert')." ADD `createtime` int(11) NOT NULL   COMMENT '时间';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbcert')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbcert',  'takedid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbcert')." ADD `takedid` int(11) NOT NULL   COMMENT '回复id';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbcert')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbcert',  'content')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbcert')." ADD `content` varchar(500)    COMMENT '内容';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbcert')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbcert',  'type')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbcert')." ADD `type` tinyint(1) NOT NULL   COMMENT '0前端发的 1后端发的';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbcert')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbcert',  'images')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbcert')." ADD `images` varchar(2500)    COMMENT '图片';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbform')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbform',  'id')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbform')." ADD `id` int(11) NOT NULL  AUTO_INCREMENT;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbform')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbform',  'uniacid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbform')." ADD `uniacid` int(10) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbform')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbform',  'name')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbform')." ADD `name` varchar(66);");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbform')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbform',  'number')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbform')." ADD `number` int(11) NOT NULL   COMMENT '越大越前';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbform')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbform',  'title')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbform')." ADD `title` varchar(255);");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbform')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbform',  'num')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbform')." ADD `num` int(11) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbform')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbform',  'money')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbform')." ADD `money` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbform')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbform',  'tbmoney')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbform')." ADD `tbmoney` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbform')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbform',  'step')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbform')." ADD `step` varchar(2222);");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbform')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbform',  'content')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbform')." ADD `content` text();");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbform')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbform',  'status')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbform')." ADD `status` tinyint(1) NOT NULL   COMMENT '0正常 1下架';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbremind')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbremind',  'id')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbremind')." ADD `id` int(10) NOT NULL  AUTO_INCREMENT;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbremind')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbremind',  'uniacid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbremind')." ADD `uniacid` int(10) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbremind')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbremind',  'createtime')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbremind')." ADD `createtime` int(11) NOT NULL   COMMENT '时间';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbremind')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbremind',  'takedid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbremind')." ADD `takedid` int(11) NOT NULL   COMMENT '回复id';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbremind')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbremind',  'content')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbremind')." ADD `content` varchar(500)    COMMENT '内容';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbremind')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbremind',  'pos')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbremind')." ADD `pos` tinyint(1) NOT NULL   COMMENT '0前端发的 1后端发的';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbremind')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbremind',  'from')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbremind')." ADD `from` tinyint(1) NOT NULL   COMMENT '0担保任务提醒';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbremind')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbremind',  'type')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbremind')." ADD `type` tinyint(1) NOT NULL   COMMENT '0提醒 1发佣金';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtaked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtaked',  'id')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtaked')." ADD `id` int(11) NOT NULL  AUTO_INCREMENT;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtaked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtaked',  'uniacid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtaked')." ADD `uniacid` int(10) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtaked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtaked',  'openid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtaked')." ADD `openid` varchar(64)    COMMENT '抢的人';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtaked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtaked',  'taskid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtaked')." ADD `taskid` int(11) NOT NULL   COMMENT '任务id';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtaked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtaked',  'createtime')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtaked')." ADD `createtime` int(11) NOT NULL   COMMENT '创建时间';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtaked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtaked',  'status')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtaked')." ADD `status` tinyint(1) NOT NULL   COMMENT '0抢到任务待审核 1审核未通过 2审核通过进行中 3任务完成 4失败待雇员确认 5任务失败 6申诉中 7管理员已判 8申诉失败任务失败 9申诉成功任务完成';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtaked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtaked',  'content')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtaked')." ADD `content` text()    COMMENT '回复内容';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtaked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtaked',  'money')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtaked')." ADD `money` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '扣除的赏金';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtaked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtaked',  'server')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtaked')." ADD `server` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '收益扣除平台费';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtaked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtaked',  'puber')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtaked')." ADD `puber` varchar(64)    COMMENT '发布者openid  方便计算数量限制';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtaked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtaked',  'giveparent')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtaked')." ADD `giveparent` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '给上级奖励';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtaked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtaked',  'reason')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtaked')." ADD `reason` varchar(200)    COMMENT '拒绝理由';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtaked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtaked',  'isend')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtaked')." ADD `isend` tinyint(1) NOT NULL   COMMENT '0任务进行中 1任务结束';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtaked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtaked',  'takecontent')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtaked')." ADD `takecontent` varchar(4000)    COMMENT '抢任务提交的内容';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtaked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtaked',  'passtime')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtaked')." ADD `passtime` int(11) NOT NULL   COMMENT '通过时间';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtaked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtaked',  'nopasstime')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtaked')." ADD `nopasstime` int(11) NOT NULL   COMMENT '未通过时间';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtaked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtaked',  'nopassreason')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtaked')." ADD `nopassreason` varchar(200)    COMMENT '未过审核原因';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtaked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtaked',  'step')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtaked')." ADD `step` tinyint(2) NOT NULL   COMMENT '任务步骤 1上传浏览图片 2上传下单图片 3上传签收图片 4上传评价图片 5备注留言 6完成';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtaked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtaked',  'stepcontent')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtaked')." ADD `stepcontent` text()    COMMENT '每个步骤的内容';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtaked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtaked',  'tbmoney')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtaked')." ADD `tbmoney` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '扣除的保证金';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtaked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtaked',  'costserver')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtaked')." ADD `costserver` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '审核任务扣除服务费';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtaked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtaked',  'comtime')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtaked')." ADD `comtime` int(11) NOT NULL   COMMENT '完成时间';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtaked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtaked',  'setfailtime')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtaked')." ADD `setfailtime` int(11) NOT NULL   COMMENT '雇主设为失败的时间';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtaked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtaked',  'failreason')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtaked')." ADD `failreason` varchar(150)    COMMENT '失败原因';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtaked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtaked',  'confirmfailtime')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtaked')." ADD `confirmfailtime` int(11) NOT NULL   COMMENT '雇员确认失败时间';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtaked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtaked',  'complaintime')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtaked')." ADD `complaintime` int(11) NOT NULL   COMMENT '雇员申诉时间';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtaked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtaked',  'complainstep')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtaked')." ADD `complainstep` tinyint(3) NOT NULL   COMMENT '0申诉中 1已判给雇员 2已判给雇主';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtaked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtaked',  'adminsettime')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtaked')." ADD `adminsettime` int(11) NOT NULL   COMMENT '管理员判断时间';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtaked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtaked',  'complainentime')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtaked')." ADD `complainentime` int(11) NOT NULL   COMMENT '申诉结束时间';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtaked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtaked',  'complainto')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtaked')." ADD `complainto` tinyint(1) NOT NULL   COMMENT '申诉最终判给谁 0雇员 1雇主';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtaked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtaked',  'subcomtime')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtaked')." ADD `subcomtime` int(11) NOT NULL   COMMENT '上传完资料，提交完成时间';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtaked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtaked',  'islimitstep')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtaked')." ADD `islimitstep` int(1) NOT NULL   COMMENT '0不限定第一步结束时间 1限定';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtaked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtaked',  'mtype')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtaked')." ADD `mtype` tinyint(1) NOT NULL   COMMENT '0用保证金支付的 1用余额支付的';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtaked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtaked',  'userid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtaked')." ADD `userid` int(11) NOT NULL   COMMENT '会员id';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtaked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtaked',  'pubuid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtaked')." ADD `pubuid` int(11) NOT NULL   COMMENT '会员id';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtaked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtaked',  'givetwo')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtaked')." ADD `givetwo` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '上上级';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtaked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtaked',  'givethree')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtaked')." ADD `givethree` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '上上上级';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtaked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtaked',  'gid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtaked')." ADD `gid` varchar(32);");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtaked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtaked',  'gived')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtaked')." ADD `gived` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '已经发放的金额';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtaked')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtaked',  'iscangive')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtaked')." ADD `iscangive` tinyint(1) NOT NULL   COMMENT '0不可发佣金 1可发佣金';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtask',  'id')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtask')." ADD `id` int(11) NOT NULL  AUTO_INCREMENT;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtask',  'uniacid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtask')." ADD `uniacid` int(10) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtask',  'title')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtask')." ADD `title` varchar(500);");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtask',  'content')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtask')." ADD `content` text()    COMMENT '任务内容';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtask',  'images')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtask')." ADD `images` varchar(2000)    COMMENT '图片';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtask',  'hideimages')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtask')." ADD `hideimages` varchar(2000)    COMMENT '隐藏的图片';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtask',  'iska')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtask')." ADD `iska` tinyint(1) NOT NULL   COMMENT '0不卡首屏 1卡首屏';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtask',  'num')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtask')." ADD `num` int(11) NOT NULL   COMMENT '任务数量';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtask',  'money')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtask')." ADD `money` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '任务单价';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtask',  'limitnum')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtask')." ADD `limitnum` int(6) NOT NULL   COMMENT '限制抢的次数';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtask',  'sex')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtask')." ADD `sex` tinyint(1) NOT NULL   COMMENT '0不限制性别 1男性可回复 2女性可回复';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtask',  'kagoodid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtask')." ADD `kagoodid` varchar(64)    COMMENT '卡首屏商品id';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtask',  'kakey')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtask')." ADD `kakey` varchar(2000)    COMMENT '卡首屏关键字';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtask',  'start')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtask')." ADD `start` int(11) NOT NULL   COMMENT '开始时间';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtask',  'end')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtask')." ADD `end` int(11) NOT NULL   COMMENT '结束时间';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtask',  'status')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtask')." ADD `status` tinyint(3) NOT NULL   COMMENT '0正常 1审核中 2下架';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtask',  'istop')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtask')." ADD `istop` tinyint(1) NOT NULL   COMMENT '0未置顶 1置顶';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtask',  'puber')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtask')." ADD `puber` varchar(64)    COMMENT '发布者';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtask',  'iscount')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtask')." ADD `iscount` tinyint(1) NOT NULL   COMMENT '0还未结算 1已结算结束';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtask',  'backmoney')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtask')." ADD `backmoney` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '结算退回钱';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtask',  'counttime')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtask')." ADD `counttime` int(11) NOT NULL   COMMENT '结算时间';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtask',  'costserver')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtask')." ADD `costserver` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '花费的服务费';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtask',  'costka')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtask')." ADD `costka` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '花费的卡首屏';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtask',  'isempty')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtask')." ADD `isempty` tinyint(1) NOT NULL   COMMENT '0没有被抢光 1被抢光了';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtask',  'scan')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtask')." ADD `scan` int(11) NOT NULL   COMMENT '浏览量';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtask',  'costtop')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtask')." ADD `costtop` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '置顶费用';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtask',  'createtime')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtask')." ADD `createtime` int(11) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtask',  'link')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtask')." ADD `link` varchar(2000);");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtask',  'hidecontent')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtask')." ADD `hidecontent` text()    COMMENT '定制的 隐藏内容';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtask',  'skiptype')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtask')." ADD `skiptype` tinyint(1) NOT NULL   COMMENT '定制的 0链接跳转 1口令跳转';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtask',  'tbmoney')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtask')." ADD `tbmoney` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '担保金额';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtask',  'isstart')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtask')." ADD `isstart` tinyint(1) NOT NULL   COMMENT '0 开始中 1未开始';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtask',  'step')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtask')." ADD `step` varchar(2000);");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtask',  'closereason')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtask')." ADD `closereason` varchar(255)    COMMENT '审核不通过原因';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtask',  'topendtime')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtask')." ADD `topendtime` int(11) NOT NULL   COMMENT '置顶结束时间';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtask',  'userid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtask')." ADD `userid` int(11) NOT NULL   COMMENT '会员id';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtask',  'giveparent')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtask')." ADD `giveparent` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '发任务上级提成';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtask',  'givetwo')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtask')." ADD `givetwo` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '上上级';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtask',  'givethree')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtask')." ADD `givethree` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtask',  'isarealimit')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtask')." ADD `isarealimit` tinyint(1) NOT NULL   COMMENT '0不做区域限制 1限制到区县 2限制市';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtask',  'province')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtask')." ADD `province` varchar(64);");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtask',  'city')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtask')." ADD `city` varchar(64);");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtask',  'country')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtask')." ADD `country` varchar(64);");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtask',  'tkl')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtask')." ADD `tkl` varchar(500)    COMMENT '淘口令';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtask',  'gid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtask')." ADD `gid` varchar(32);");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtask')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtask',  'address')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtask')." ADD `address` varchar(255)    COMMENT '任务地址';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtaskstep')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtaskstep',  'id')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtaskstep')." ADD `id` int(10) NOT NULL  AUTO_INCREMENT;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtaskstep')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtaskstep',  'uniacid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtaskstep')." ADD `uniacid` int(10) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtaskstep')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtaskstep',  'openid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtaskstep')." ADD `openid` varchar(64);");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtaskstep')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtaskstep',  'step')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtaskstep')." ADD `step` varchar(2000);");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_tbtaskstep')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_tbtaskstep',  'userid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_tbtaskstep')." ADD `userid` int(11) NOT NULL   COMMENT '会员id';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_useaddcontent')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_useaddcontent',  'id')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_useaddcontent')." ADD `id` int(11) NOT NULL  AUTO_INCREMENT;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_useaddcontent')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_useaddcontent',  'uniacid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_useaddcontent')." ADD `uniacid` int(11) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_useaddcontent')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_useaddcontent',  'content')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_useaddcontent')." ADD `content` varchar(800);");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_useaddcontent')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_useaddcontent',  'img')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_useaddcontent')." ADD `img` varchar(2000);");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_useaddcontent')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_useaddcontent',  'takedid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_useaddcontent')." ADD `takedid` int(11) NOT NULL   COMMENT '回复的id';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_useaddcontent')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_useaddcontent',  'createtime')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_useaddcontent')." ADD `createtime` int(11) NOT NULL   COMMENT '创建时间';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_useaddcontent')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_useaddcontent',  'type')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_useaddcontent')." ADD `type` tinyint(1) NOT NULL   COMMENT '0补充内容 1提醒内容';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_useaddcontent')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_useaddcontent',  'taskid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_useaddcontent')." ADD `taskid` int(11) NOT NULL   COMMENT '任务id';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_useric')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_useric',  'id')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_useric')." ADD `id` int(11) NOT NULL  AUTO_INCREMENT;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_useric')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_useric',  'uniacid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_useric')." ADD `uniacid` int(10) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_useric')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_useric',  'name')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_useric')." ADD `name` varchar(32);");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_useric')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_useric',  'number')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_useric')." ADD `number` tinyint(1) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_userics')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_userics',  'id')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_userics')." ADD `id` int(11) NOT NULL  AUTO_INCREMENT;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_userics')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_userics',  'uniacid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_userics')." ADD `uniacid` int(10) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_userics')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_userics',  'uid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_userics')." ADD `uid` int(11) NOT NULL   COMMENT '会员uid';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_userics')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_userics',  'icid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_userics')." ADD `icid` int(11) NOT NULL   COMMENT '标签id';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_usersort')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_usersort',  'id')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_usersort')." ADD `id` int(10) NOT NULL  AUTO_INCREMENT;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_usersort')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_usersort',  'uid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_usersort')." ADD `uid` int(11) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_usersort')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_usersort',  'sortid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_usersort')." ADD `sortid` int(11) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_usersort')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_usersort',  'uniacid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_usersort')." ADD `uniacid` int(11) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_usetasklog')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_usetasklog',  'id')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_usetasklog')." ADD `id` int(11) NOT NULL  AUTO_INCREMENT;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_usetasklog')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_usetasklog',  'uniacid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_usetasklog')." ADD `uniacid` int(11) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_usetasklog')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_usetasklog',  'openid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_usetasklog')." ADD `openid` varchar(64);");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_usetasklog')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_usetasklog',  'taskid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_usetasklog')." ADD `taskid` int(11) NOT NULL   COMMENT '任务编号';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_usetasklog')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_usetasklog',  'createtime')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_usetasklog')." ADD `createtime` int(11) NOT NULL   COMMENT '创建时间';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_usetasklog')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_usetasklog',  'status')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_usetasklog')." ADD `status` tinyint(1) NOT NULL   COMMENT '0申请中 1已通过 2审核不通过 3雇员主动取消 4已提交订单号 5已完成 6已拒绝结果失败';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_usetasklog')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_usetasklog',  'isactivity')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_usetasklog')." ADD `isactivity` tinyint(1) NOT NULL   COMMENT '是否有效的试用 0有效 1无效 ，当被拒绝或取消后变为1记为无效';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_usetasklog')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_usetasklog',  'passortime')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_usetasklog')." ADD `passortime` int(11) NOT NULL   COMMENT '通过或拒绝时间';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_usetasklog')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_usetasklog',  'canceltime')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_usetasklog')." ADD `canceltime` int(11) NOT NULL   COMMENT '雇员主动取消任务时间';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_usetasklog')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_usetasklog',  'subcontent')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_usetasklog')." ADD `subcontent` varchar(2000)    COMMENT '提交内容';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_usetasklog')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_usetasklog',  'subtime')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_usetasklog')." ADD `subtime` int(11) NOT NULL   COMMENT '提交订单时间';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_usetasklog')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_usetasklog',  'suctime')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_usetasklog')." ADD `suctime` int(11) NOT NULL   COMMENT '设为成功时间';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_usetasklog')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_usetasklog',  'prizemoney')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_usetasklog')." ADD `prizemoney` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '赏金金额';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_usetasklog')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_usetasklog',  'reason')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_usetasklog')." ADD `reason` varchar(200)    COMMENT '拒绝和失败原因';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_usetasklog')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_usetasklog',  'tosuctime')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_usetasklog')." ADD `tosuctime` int(11) NOT NULL   COMMENT '转为成功时间';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_usetasklog')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_usetasklog',  'failtime')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_usetasklog')." ADD `failtime` int(11) NOT NULL   COMMENT '任务失败时间';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_usetasklog')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_usetasklog',  'iscomplained')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_usetasklog')." ADD `iscomplained` tinyint(1) NOT NULL   COMMENT '0未被投诉 1被投诉';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_usetasklog')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_usetasklog',  'giveparent')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_usetasklog')." ADD `giveparent` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '给上级的提成';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_usetasklog')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_usetasklog',  'initcontent')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_usetasklog')." ADD `initcontent` varchar(2000)    COMMENT '抢任务提交的内容';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_usetasklog')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_usetasklog',  'puber')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_usetasklog')." ADD `puber` varchar(64);");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_usetasklog')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_usetasklog',  'userid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_usetasklog')." ADD `userid` int(11) NOT NULL   COMMENT '会员id';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_usetasklog')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_usetasklog',  'pubuid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_usetasklog')." ADD `pubuid` int(11) NOT NULL   COMMENT '会员id';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_usetasklog')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_usetasklog',  'givetwo')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_usetasklog')." ADD `givetwo` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '上上级';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_usetasklog')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_usetasklog',  'givethree')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_usetasklog')." ADD `givethree` decimal(10,2) NOT NULL DEFAULT NULL DEFAULT '0.00'  COMMENT '上上上级';");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_vauth')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_vauth',  'id')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_vauth')." ADD `id` int(11) NOT NULL  AUTO_INCREMENT;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_vauth')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_vauth',  'uniacid')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_vauth')." ADD `uniacid` int(11) NOT NULL;");
 }
}
if(pdo_tableexists('ims_zofui_tasktb_vauth')) {
 if(!pdo_fieldexists('ims_zofui_tasktb_vauth',  'params')) {
  pdo_query("ALTER TABLE ".tablename('ims_zofui_tasktb_vauth')." ADD `params` text();");
 }
}
