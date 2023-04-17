<?php 

class model_quest {

	
	static function initQuest(){
		global $_W;


		$arr = array(
			array(
				'title' => '如何发布任务？',
				'content' => self::$q1,
				'type' => 1,
				'settype' => 1,
				'number' => 100
			),
			array(
				'title' => '发布任务怎么置顶？',
				'content' => self::$q2,
				'type' => 1,
				'settype' => 1,
				'number' => 99
			),
			array(
				'title' => '为什么要交纳保证金、能否提现？',
				'content' => self::$q3,
				'type' => 1,
				'settype' => 1,
				'number' => 98
			),
			array(
				'title' => '未做完的任务赏金会退还吗？',
				'content' => self::$q4,
				'type' => 1,
				'settype' => 1,
				'number' => 97
			),
			array(
				'title' => '商家保证金提现多久能到账呢？',
				'content' => self::$q5,
				'type' => 1,
				'settype' => 1,
				'number' => 96
			),
			array(
				'title' => '任务没有做完，手续费和置顶费会退回吗？',
				'content' => self::$q6,
				'type' => 1,
				'settype' => 1,
				'number' => 95
			),
			array(
				'title' => '怎么用电脑发布管理任务?',
				'content' => self::$q7,
				'type' => 1,
				'settype' => 1,
				'number' => 94
			),

			array(
				'title' => '提现什么时候到账？',
				'content' => self::$q8,
				'type' => 0,
				'settype' => 1,
				'number' => 88
			),
			array(
				'title' => '任务多久更新一次？',
				'content' => self::$q9,
				'type' => 0,
				'settype' => 1,
				'number' => 87
			),

			array(
				'title' => '疲劳值是怎么使用的？',
				'content' => self::$q10,
				'type' => 0,
				'settype' => 1,
				'number' => 86
			),
			array(
				'title' => '为什么要扣服务费？',
				'content' => self::$q11,
				'type' => 0,
				'settype' => 1,
				'number' => 85
			),
			array(
				'title' => '赏金满多少可以提现？',
				'content' => self::$q12,
				'type' => 0,
				'settype' => 1,
				'number' => 84
			),
			array(
				'title' => '如何做任务获得佣金？',
				'content' => self::$q13,
				'type' => 0,
				'settype' => 1,
				'number' => 83
			),
			array(
				'title' => '商家恶意拒绝任务怎么办？',
				'content' => self::$q14,
				'type' => 0,
				'settype' => 1,
				'number' => 82
			),
			array(
				'title' => '怎么发展合伙人？有多少奖励？',
				'content' => self::$q15,
				'type' => 0,
				'settype' => 1,
				'number' => 81
			),
			array(
				'title' => '回答任务后商家不采纳任务怎么办？',
				'content' => self::$q16,
				'type' => 0,
				'settype' => 1,
				'number' => 80
			),
			array(
				'title' => '消息太多,怎么关闭消息提示？',
				'content' => self::$q17,
				'type' => 0,
				'settype' => 1,
				'number' => 79
			),

		);
		
		$q = pdo_get('zofui_tasktb_selfquest',array('uniacid'=>$_W['uniacid']));
		if( empty( $q ) ) {
			foreach ( $arr as $v ) {
				$data = array(
					'uniacid' => $_W['uniacid'],
					'title' => $v['title'],
					'type' => $v['type'],
					'content' => $v['content'],
					'settype' => $v['settype'],
				);
				pdo_insert('zofui_tasktb_selfquest',$data);
			}
		}


	}


	private static $q17 = <<<div
&lt;div class=&quot;text&quot;&gt;&lt;p&gt;在平台&quot;&lt;span style=&quot;color: rgb(255, 0, 0);&quot;&gt;我的&lt;/span&gt;&quot;页面中下拉到底部&quot;&lt;span style=&quot;color: rgb(255, 0, 0);&quot;&gt;通知设置&lt;/span&gt;&quot;中可以关闭对应的消息提示&lt;/p&gt;&lt;/div&gt;
div;


	private static $q16 = <<<div
&lt;div class=&quot;text&quot;&gt;&lt;p style=&quot;text-align: center;&quot;&gt;你回答的任务,如果商家长时间未采纳,那么会在任务结算后,自动采纳,并支付你的赏金!&lt;/p&gt;&lt;p style=&quot;text-align: center;&quot;&gt;&lt;span style=&quot;color: rgb(255, 0, 0);&quot;&gt;任务结算时间为:任务发布后24小时自动结算&lt;/span&gt;&lt;/p&gt;&lt;/div&gt;
div;


	private static $q15 = <<<div
&lt;div class=&quot;text&quot;&gt;&lt;p style=&quot;text-align: center;&quot;&gt;&lt;span style=&quot;color: rgb(255, 0, 0);&quot;&gt;分享平台任务首页&lt;/span&gt;,给你的小弟,或者你的微信群等交流的地方,&lt;span style=&quot;color: rgb(255, 0, 0);&quot;&gt;对方点击后,即可成为你的合伙人&lt;/span&gt;(目前合伙人无上限)&lt;/p&gt;&lt;p style=&quot;text-align: center;&quot;&gt;&lt;span style=&quot;color: rgb(255, 0, 0);&quot;&gt;&lt;br/&gt;&lt;/span&gt;&lt;/p&gt;&lt;p style=&quot;text-align: center;&quot;&gt;&lt;span style=&quot;color: rgb(255, 0, 0);&quot;&gt;合伙人在平台做任务,将会奖励他做任务赏金的10%给你&lt;/span&gt;&lt;/p&gt;&lt;/div&gt;
div;


	private static $q14 = <<<div
&lt;div class=&quot;text&quot;&gt;&lt;p&gt;&nbsp; &nbsp;如果你回答的任务严格按照要求来做了,商家还是进行拒绝,那么你可以在接到的任务页面,发起投诉,我们客服会审核资料,帮你维权.如果恶意多次投诉,我们也将对您进行惩罚.&lt;/p&gt;&lt;/div&gt;
div;

	private static $q13 = <<<div
&lt;div class=&quot;text&quot;&gt;&lt;p style=&quot;text-align: center;&quot;&gt;&lt;span style=&quot;font-size: 18px;&quot;&gt;满1元即可提现! 无任何限制,无任何门槛&lt;/span&gt;&lt;/p&gt;&lt;p style=&quot;text-align: center;&quot;&gt;&lt;span style=&quot;color: rgb(255, 0, 0); font-size: 18px;&quot;&gt;提现直接进入微信钱包,无任何条件限制&lt;/span&gt;&lt;/p&gt;&lt;p&gt;&lt;span style=&quot;color: rgb(255, 0, 0);&quot;&gt;&lt;br/&gt;&lt;/span&gt;&lt;/p&gt;&lt;p&gt;&lt;span style=&quot;color: rgb(255, 0, 0);&quot;&gt;&lt;img src=&quot;http://o7znfws9w.bkt.clouddn.com/QQ20170821-1@2x.jpg&quot; width=&quot;100%&quot;/&gt;&lt;/span&gt;&lt;/p&gt;&lt;p style=&quot;text-align: center;&quot;&gt;&lt;span style=&quot;color: rgb(0, 112, 192); font-size: 24px;&quot;&gt;&lt;strong&gt;&lt;br/&gt;&lt;/strong&gt;&lt;/span&gt;&lt;/p&gt;&lt;p style=&quot;text-align: center;&quot;&gt;&lt;span style=&quot;font-size: 24px; color: rgb(255, 0, 0); background-color: rgb(255, 255, 0);&quot;&gt;&lt;strong&gt;接任务前，先到个人中心&lt;/strong&gt;&lt;/span&gt;&lt;/p&gt;&lt;p style=&quot;text-align: center;&quot;&gt;&lt;span style=&quot;color: rgb(0, 112, 192); font-size: 24px;&quot;&gt;&lt;strong&gt;底部的账户设置，&lt;span style=&quot;font-size: 24px; color: rgb(255, 0, 0); background-color: rgb(255, 255, 0);&quot;&gt;设置好个人信息，否则部分任务不能做&lt;/span&gt;&lt;br/&gt;&lt;/strong&gt;&lt;/span&gt;&lt;/p&gt;&lt;p style=&quot;text-align: left;&quot;&gt;&lt;span style=&quot;color: rgb(0, 112, 192); font-size: 24px;&quot;&gt;&lt;strong&gt;&lt;br/&gt;&lt;/strong&gt;&lt;/span&gt;&lt;/p&gt;&lt;p style=&quot;text-align: left;&quot;&gt;&lt;span style=&quot;color: rgb(0, 112, 192); font-size: 24px;&quot;&gt;&lt;strong&gt;1.接单说明&lt;/strong&gt;&lt;/span&gt;&lt;span style=&quot;color: rgb(255, 0, 0);&quot;&gt;&lt;br/&gt;&lt;/span&gt;&lt;/p&gt;&lt;p style=&quot;text-align: left;&quot;&gt;&lt;span style=&quot;color: rgb(255, 0, 0); font-size: 18px;&quot;&gt;&lt;strong&gt;在任务首页点击任务,抢到任务即可开始做任务了&lt;/strong&gt;&lt;/span&gt;&lt;/p&gt;&lt;p style=&quot;text-align: left;&quot;&gt;&lt;span style=&quot;color: rgb(0, 112, 192); font-size: 24px;&quot;&gt;&lt;strong&gt;&lt;img src=&quot;http://o7znfws9w.bkt.clouddn.com/oLH6oMZSLOsTg222S2w3Bs614cC42s.png&quot; alt=&quot;images/1/2017/06/oLH6oMZSLOsTg222S2w3Bs614cC42s.png&quot; width=&quot;100%&quot;/&gt;&lt;/strong&gt;&lt;/span&gt;&lt;/p&gt;&lt;p style=&quot;text-align: left;&quot;&gt;&lt;span style=&quot;color: rgb(0, 176, 80);&quot;&gt;&lt;strong&gt;&lt;img src=&quot;http://o7znfws9w.bkt.clouddn.com/aXsSrimi8wR88SvmmjBiReNCIT88n5.png&quot; alt=&quot;images/1/2017/06/aXsSrimi8wR88SvmmjBiReNCIT88n5.png&quot; width=&quot;100%&quot;/&gt;&lt;/strong&gt;&lt;/span&gt;&lt;/p&gt;&lt;p style=&quot;text-align: left;&quot;&gt;&lt;span style=&quot;color: rgb(0, 176, 80);&quot;&gt;&lt;strong&gt;&lt;br/&gt;&lt;/strong&gt;&lt;/span&gt;&lt;/p&gt;&lt;p style=&quot;text-align: left;&quot;&gt;&lt;span style=&quot;color: rgb(0, 176, 80);&quot;&gt;&lt;strong&gt;&lt;br/&gt;&lt;/strong&gt;&lt;/span&gt;&lt;/p&gt;&lt;p style=&quot;text-align: center;&quot;&gt;&lt;span style=&quot;color: rgb(255, 0, 0); background-color: rgb(255, 255, 0); font-size: 20px;&quot;&gt;&lt;strong&gt;按照要求做好任务后,点击回复任务,上传图片点击确定即可&lt;/strong&gt;&lt;/span&gt;&lt;/p&gt;&lt;p&gt;&lt;span style=&quot;color: rgb(255, 0, 0); background-color: rgb(255, 255, 0); font-size: 20px;&quot;&gt;&lt;strong&gt;&lt;br/&gt;&lt;/strong&gt;&lt;/span&gt;&lt;/p&gt;&lt;p style=&quot;text-align: left;&quot;&gt;&lt;span style=&quot;color: rgb(0, 176, 80);&quot;&gt;&lt;strong&gt;&lt;br/&gt;&lt;/strong&gt;&lt;/span&gt;&lt;/p&gt;&lt;p style=&quot;text-align: left;&quot;&gt;&lt;span style=&quot;color: rgb(0, 176, 80);&quot;&gt;&lt;strong&gt;&lt;img src=&quot;http://o7znfws9w.bkt.clouddn.com/X4NFh5oDdChBAzDaK5ACAVbchGvI4C.png&quot; alt=&quot;images/1/2017/06/X4NFh5oDdChBAzDaK5ACAVbchGvI4C.png&quot; width=&quot;100%&quot;/&gt;&lt;/strong&gt;&lt;/span&gt;&lt;/p&gt;&lt;p style=&quot;text-align: left;&quot;&gt;&lt;span style=&quot;color: rgb(0, 112, 192); font-size: 24px;&quot;&gt;&lt;strong&gt;2.提现说明&lt;/strong&gt;&lt;/span&gt;&lt;/p&gt;&lt;p style=&quot;text-align: left;&quot;&gt;&lt;span style=&quot;color: rgb(0, 112, 192); font-size: 24px;&quot;&gt;&lt;strong&gt;每周1-周5 下午6点审核提现,审核后直接大入你的微信钱包&lt;/strong&gt;&lt;/span&gt;&lt;/p&gt;&lt;p style=&quot;text-align: left;&quot;&gt;&lt;span style=&quot;color: rgb(0, 176, 80);&quot;&gt;&lt;strong&gt;&lt;br/&gt;&lt;/strong&gt;&lt;/span&gt;&lt;/p&gt;&lt;p style=&quot;text-align: left;&quot;&gt;&lt;span style=&quot;color: rgb(0, 176, 80);&quot;&gt;&lt;strong&gt;&lt;img src=&quot;http://o7znfws9w.bkt.clouddn.com/Qql8JFE3D4CLn9xcBdb3cqnwfy8b8F.png&quot; alt=&quot;images/1/2017/06/Qql8JFE3D4CLn9xcBdb3cqnwfy8b8F.png&quot; width=&quot;100%&quot;/&gt;&lt;/strong&gt;&lt;/span&gt;&lt;/p&gt;&lt;/div&gt;
div;


	private static $q12 = <<<div
&lt;div class=&quot;text&quot;&gt;&lt;p style=&quot;text-align: center;&quot;&gt;赏金满1元即可提现, 提现等待系统处理完毕后,直接进入到你的微信钱包中!&lt;/p&gt;&lt;/div&gt;
div;


	private static $q11 = <<<div
&lt;div class=&quot;text&quot;&gt;&lt;p style=&quot;text-align: center;&quot;&gt;首先感谢您信任本平台,毕竟如此低门槛,就能一个月拿到1500-3000一个月的收入,要相信这种天上掉馅饼的事太难了!&lt;/p&gt;&lt;p style=&quot;text-align: center;&quot;&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;&nbsp; &nbsp;我们是合法经营,所有的收入都需要缴纳国税,地税,企业所得税等等,其次还需要支付员工工资,办公产地,宽带服务器,等等费用,在你们看来感觉收的比例不少,但是&lt;span style=&quot;color: rgb(255, 0, 0);&quot;&gt;税收是按照收入直接收取的,不是按照利润收取的&lt;/span&gt;,所以整个平台实际算下来,依然是亏损运营!这个平台的存在,只是为了解决我们淘宝商家的问题!所以我们不会扩大规模,增加没必要的麻烦,只需要保障我们商家能解决问即可,只要商家不死,平台就不会关闭,用户放心做任务合作共赢!&lt;/p&gt;&lt;/div&gt;
div;


	private static $q10 = <<<div
&lt;div class=&quot;text&quot;&gt;&lt;p&gt;疲劳值每天零点更新,最大值为100点,每抢一个任务,消耗1个疲劳值.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;增加疲劳值的办法&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;当你有合伙人后,你的合伙人&lt;span style=&quot;color: rgb(255, 0, 0);&quot;&gt;消耗掉90个疲劳值&lt;/span&gt;时,系统将会赠&lt;span style=&quot;color: rgb(255, 0, 0);&quot;&gt;送你10个疲劳值&lt;/span&gt;,如果有2个合伙人各消耗了90个疲劳值,那么您将得到20个疲劳值(上限为100)&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;合伙人除了给你贡献疲劳值,&lt;span style=&quot;color: rgb(255, 0, 0);&quot;&gt;还会给你共享他做任务的10%的赏金奖励给你哦&lt;/span&gt;&lt;/p&gt;&lt;/div&gt;
div;

	private static $q9 = <<<div
&lt;div class=&quot;text&quot;&gt;&lt;p style=&quot;text-align: center;&quot;&gt;任务是实时更新的,商家发布任务后就会在前台展现,您可以抢到任务后就可以做任务了!&lt;/p&gt;&lt;/div&gt;
div;

	private static $q8 = <<<div
&lt;div class=&quot;text&quot;&gt;&lt;p style=&quot;white-space: normal; text-align: center;&quot;&gt;账户余额&lt;span style=&quot;color: rgb(255, 0, 0);&quot;&gt;满1元即可提现&lt;/span&gt;！&lt;/p&gt;&lt;p style=&quot;white-space: normal; text-align: center;&quot;&gt;&lt;strong&gt;周一到周五余额提现：24小时之内审核到账，当天17点前的提现当天审核，17点后提现次日下午审核。&lt;/strong&gt;&lt;/p&gt;&lt;p style=&quot;white-space: normal;&quot;&gt;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 【&lt;strong&gt;&lt;span style=&quot;color: rgb(0, 112, 192);&quot;&gt;保证金提现每个礼拜星期五下午统一审核&lt;/span&gt;&lt;/strong&gt;】！&lt;/p&gt;&lt;p style=&quot;white-space: normal; text-align: center;&quot;&gt;&lt;strong&gt;&lt;span style=&quot;color: rgb(255, 0, 0);&quot;&gt;周6周7与国家法定节假日放假不审核&lt;/span&gt;&lt;/strong&gt;&lt;/p&gt;&lt;p style=&quot;white-space: normal; text-align: center;&quot;&gt;您的提现将会在正常工作日审核后发放到您微信钱包&lt;br/&gt;&lt;/p&gt;&lt;p style=&quot;white-space: normal;&quot;&gt;&lt;br/&gt;&lt;/p&gt;&lt;p style=&quot;white-space: normal;&quot;&gt;&lt;span style=&quot;color: rgb(255, 0, 0); background-color: rgb(255, 255, 0);&quot;&gt;提示&lt;/span&gt;:&lt;/p&gt;&lt;p style=&quot;white-space: normal;&quot;&gt;在&lt;span style=&quot;color: rgb(255, 0, 0);&quot;&gt;&lt;strong&gt;个人中心&lt;/strong&gt;&lt;/span&gt;页面中，下滑到底部，&lt;strong&gt;&lt;span style=&quot;color: rgb(255, 0, 0);&quot;&gt;账户设置&lt;/span&gt;&lt;/strong&gt;中完善你的身份信息，否则部分要求性别的任务将会无法抢！&lt;/p&gt;&lt;p style=&quot;white-space: normal;&quot;&gt;&lt;br/&gt;&lt;/p&gt;&lt;p style=&quot;white-space: normal;&quot;&gt;&lt;span style=&quot;color: rgb(255, 0, 0); background-color: rgb(255, 255, 0);&quot;&gt;如果你嫌消息太多&lt;/span&gt;，比较烦人，可以在“&lt;span style=&quot;color: rgb(255, 0, 0);&quot;&gt;个人中心&lt;/span&gt;”页面中下滑到底“&lt;span style=&quot;color: rgb(255, 0, 0);&quot;&gt;通知设置&lt;/span&gt;”中关闭提示信息&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;/div&gt;

div;


	private static $q7 = <<<div
&lt;div class=&quot;text&quot;&gt;&lt;p&gt;下载 微信PC客户端 (百度搜索)然后用自己的微信登陆,找到公众号 即可进入任务系统,操作管理任务,与手机微信一模一样!&lt;br/&gt;&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p style=&quot;text-align: center;&quot;&gt;&lt;span style=&quot;color: rgb(255, 0, 0);&quot;&gt;如果上传不了图片,请重新安装PC微信即可!&lt;/span&gt;&lt;br/&gt;&lt;/p&gt;&lt;/div&gt;

div;

	private static $q6 = <<<div
&lt;div class=&quot;text&quot;&gt;&lt;p style=&quot;text-align: center;&quot;&gt;任务发布后,仅退还未回答的任务数量金额+拒绝的任务数量金额,其余金额都不退还!&lt;/p&gt;&lt;/div&gt;

div;


	private static $q5 = <<<div
&lt;p&gt;&lt;div class=&quot;text&quot;&gt;&lt;/p&gt;&lt;p&gt;&lt;span class=&quot;Apple-tab-span&quot; style=&quot;white-space: pre;&quot;&gt;		&lt;/span&gt;&lt;p style=&quot;text-align: center;&quot;&gt;保证金提现：&lt;span style=&quot;color: rgb(255, 0, 0);&quot;&gt;每周五审核&lt;/span&gt;,审核过后,直接打入进你的微信钱包内&lt;/p&gt;&lt;span class=&quot;Apple-tab-span&quot; style=&quot;white-space: pre;&quot;&gt;	&lt;/span&gt;&lt;/div&gt;&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;

div;


	private static $q4 = <<<div
&lt;div class=&quot;text&quot;&gt;&lt;p&gt;未做完的任务,将会在任务结算后(1.主动结算 2.任务发布后24小时后)退还到账户余额内,退还比例为:未回复的任务数量金额+拒绝的任务金额数量&lt;/p&gt;&lt;/div&gt;

div;

	private static $q3 = <<<div
&lt;div class=&quot;text&quot;&gt;&lt;p style=&quot;text-align: center;&quot;&gt;主要为了让平台有规有据,才能良好的发展平台,其次才是为了阻止恶意商家提高犯罪成本!&lt;/p&gt;&lt;p style=&quot;text-align: center;&quot;&gt;共建良好的平台是离不开大家的&lt;/p&gt;&lt;p style=&quot;text-align: center;&quot;&gt;请大家遵守平台规则,发布合理任务,才能稳定,长久&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p style=&quot;text-align: center;&quot;&gt;&lt;span style=&quot;color: rgb(255, 0, 0);&quot;&gt;保证金随时可提现(所有任务完结的情况下)&lt;/span&gt;&lt;/p&gt;&lt;/div&gt;

div;


	private static $q2 = <<<div
&lt;div class=&quot;text&quot;&gt;&lt;p style=&quot;text-align: center;&quot;&gt;发布任务过程中,有置顶按钮,勾选后即可置顶&lt;/p&gt;&lt;p style=&quot;text-align: center;&quot;&gt;置顶为收费服务,仅对当前任务生效(24小时)&lt;/p&gt;&lt;p style=&quot;text-align: center;&quot;&gt;&lt;span style=&quot;color: rgb(255, 0, 0);&quot;&gt;置顶后可加快任务的完成速度&lt;/span&gt;&lt;/p&gt;&lt;/div&gt;
div;

	private static $q1 = <<<div
&lt;div class=&quot;text&quot;&gt;&lt;section class=&quot;_wxbEditor&quot;&gt;&lt;section class=&quot;RankEditor&quot; style=&quot;white-space: normal; font-family: &quot; microsoft=&quot;&quot;&gt;&lt;section style=&quot;margin-top: 20px; margin-bottom: -23px;&quot;&gt;&lt;section class=&quot;white&quot; style=&quot;padding-right: 15px; padding-left: 15px; display: inline-block; height: 30px; overflow: hidden; line-height: 30px; background: rgb(225, 50, 66); vertical-align: bottom;&quot;&gt;&lt;p style=&quot;font-size: 18px; color: rgb(255, 255, 255); min-width: 1px; text-align: center;&quot;&gt;小卖家互助平台使用说明&lt;/p&gt;&lt;/section&gt;&lt;span style=&quot;margin-top: 2px; margin-left: -5px; display: inline-block; border-width: 5px; border-style: solid; border-color: rgb(217, 29, 47) transparent transparent; vertical-align: top; transform: rotate(45deg);&quot;&gt;&lt;/span&gt;
 &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;/section&gt;&lt;section style=&quot;padding: 30px 10px 15px; border-width: 1px; border-style: solid; border-color: rgb(192, 192, 192);&quot;&gt;&lt;section style=&quot;text-align: center;&quot;&gt;&lt;span style=&quot;display: inline-block; vertical-align: middle; width: 30px; height: 1px; background: rgb(192, 192, 192);&quot;&gt;&lt;/span&gt;
 &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;section style=&quot;margin-right: 3px; margin-left: 3px; display: inline-block; vertical-align: middle;&quot;&gt;&lt;p class=&quot;titlea active&quot; style=&quot;font-size: 18px; color: rgb(6, 6, 6); min-width: 1px;&quot;&gt;&lt;span style=&quot;color: rgb(225, 50, 66); white-space: pre-wrap;&quot;&gt;第一步│发布任务&lt;/span&gt;
 &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;/p&gt;&lt;/section&gt;&lt;span style=&quot;display: inline-block; vertical-align: middle; width: 30px; height: 1px; background: rgb(192, 192, 192);&quot;&gt;&lt;/span&gt;
 &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;/section&gt;&lt;section style=&quot;margin-top: 10px; display: flex; align-items: center;&quot;&gt;&lt;section style=&quot;margin-right: 10px; flex: 1 1 0%; text-align: center;&quot;&gt;&lt;p class=&quot;titlea active&quot; style=&quot;color: rgb(225, 50, 66);&quot;&gt;&lt;span style=&quot;color: rgb(66, 66, 66); font-size: 14px; text-align: left;&quot;&gt;选择你要发布的任务类型，或者任选一个自己编辑任务说明！&lt;/span&gt;&lt;strong style=&quot;color: rgb(66, 66, 66); font-size: 14px; text-align: left;&quot;&gt;任务说明内容为&lt;/strong&gt;&lt;span style=&quot;color: rgb(66, 66, 66); font-size: 14px; text-align: left;&quot;&gt;：要求用户做的动作，比如收藏加购停留时间等等，并让用户截图证明（&lt;/span&gt;&lt;span style=&quot;font-size: 14px; text-align: left; color: rgb(247, 23, 23);&quot;&gt;最多只能上传6张图&lt;/span&gt;&lt;span style=&quot;color: rgb(66, 66, 66); font-size: 14px; text-align: left;&quot;&gt;）&lt;/span&gt;&lt;br/&gt;
 &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;/p&gt;&lt;/section&gt;&lt;section style=&quot;padding: 8px 6px; width: 178.188px; background: rgb(192, 192, 192); border-radius: 10px;&quot;&gt;&lt;img src=&quot;http://o7znfws9w.bkt.clouddn.com/1.png&quot; data-type=&quot;png&quot; style=&quot; vertical-align: top; width:auto !important;max-width:100% !important;height:auto !important;&quot;/&gt;
 &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;/section&gt;&lt;/section&gt;&lt;/section&gt;&lt;/section&gt;&lt;section class=&quot;_wxbEditor&quot;&gt;&lt;p style=&quot;text-align: center;&quot;&gt;&lt;span style=&quot;font-size: 18px; color: rgb(247, 23, 23);&quot;&gt;&lt;strong&gt;第二步&lt;span style=&quot;font-family: &quot; microsoft=&quot;&quot; white-space:=&quot;&quot;&gt;│编辑说明和要求&lt;/span&gt;&lt;/strong&gt;&lt;/span&gt;
 &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;/p&gt;&lt;section class=&quot;v5bjq&quot; label=&quot;Copyright © 2016 www.v5bjq.com All Rights Reserved.&quot; style=&quot;margin: 5px auto;white-space: normal;&quot;&gt;&lt;section style=&quot;border: 0px; box-sizing: border-box; width: 100%; clear: both; overflow: hidden;&quot;&gt;&lt;section style=&quot;box-sizing: border-box; width: 50%; float: left; padding-right: 0.5em;&quot; data-width=&quot;50%&quot;&gt;&lt;img src=&quot;http://o7znfws9w.bkt.clouddn.com/682689-temp-201705-19-1495170989457.png&quot; style=&quot;box-sizing: border-box;width:auto !important;max-width:100% !important;height:auto !important;&quot; class=&quot; js_catchingremoteimage&quot; data-remoteid=&quot;c1472612585279&quot;/&gt;
 &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;/section&gt;&lt;section style=&quot;box-sizing: border-box; width: 50%; float: right; padding-left: 0.5em;&quot; data-width=&quot;50%&quot;&gt;&lt;img data-width=&quot;100%&quot; src=&quot;http://o7znfws9w.bkt.clouddn.com/682689-temp-201705-19-1495170990379.png&quot; style=&quot;box-sizing: border-box; width: 100%;&quot; class=&quot; js_catchingremoteimage&quot; data-remoteid=&quot;c1472612585280&quot; border=&quot;0&quot;/&gt;
 &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;/section&gt;&lt;/section&gt;&lt;/section&gt;&lt;/section&gt;&lt;section class=&quot;_wxbEditor&quot;&gt;&lt;section style=&quot;white-space: normal;&quot;&gt;&lt;p class=&quot;wihudong&quot; style=&quot;color: rgb(216, 25, 25); font-size: 18px;&quot;&gt;&lt;strong&gt;详细说明&lt;/strong&gt;
 &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;/p&gt;&lt;/section&gt;&lt;section style=&quot;padding: 1em; white-space: normal; border-top: 1px solid rgb(202, 202, 202); background-color: rgb(244, 244, 244); display: inline-block;&quot;&gt;&lt;section style=&quot;display: inline-block;&quot;&gt;&lt;section class=&quot;xhr&quot; style=&quot;height: 3em; width: 3em; background-color: rgb(216, 25, 25);&quot;&gt;&lt;section style=&quot;text-align: center;&quot;&gt;&lt;p style=&quot;margin-top: 8px; display: inline-block; font-size: 20px; color: white;&quot;&gt;1&lt;/p&gt;&lt;/section&gt;&lt;/section&gt;&lt;section style=&quot;margin-top: -0.8em;&quot;&gt;&lt;br/&gt;
 &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;/section&gt;&lt;section&gt;&lt;/section&gt;&lt;/section&gt;&lt;section style=&quot;vertical-align: top; display: inline-block;&quot;&gt;&lt;/section&gt;&lt;section style=&quot;margin-top: -3.2em; margin-left: 1.5em; padding-bottom: 1em; padding-left: 2em; border-left: 1px solid rgb(202, 202, 202);&quot;&gt;&lt;section style=&quot;padding: 0.5em; background-color: white;&quot;&gt;&lt;p&gt;内容编写要求&lt;/p&gt;&lt;/section&gt;&lt;p style=&quot;margin-top: 0.5em; line-height: 1.5em;&quot;&gt;任务要求可以参考内置的模版，&lt;strong&gt;超链接中不能输入中文，否则用户点不开&lt;/strong&gt;，如果要放淘口令，直接在内容中写入即可，使用卡首屏功能，可以很好的保护你的产品。&lt;span style=&quot;color: rgb(64, 179, 230);&quot;&gt;禁止发布刷单内容&lt;/span&gt;
 &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;/p&gt;&lt;/section&gt;&lt;section style=&quot;display: inline-block;&quot;&gt;&lt;section class=&quot;xhr&quot; style=&quot;height: 3em; width: 3em; background-color: rgb(216, 25, 25);&quot;&gt;&lt;section style=&quot;text-align: center;&quot;&gt;&lt;p style=&quot;margin-top: 8px; display: inline-block; font-size: 20px; color: white;&quot;&gt;2&lt;/p&gt;&lt;/section&gt;&lt;/section&gt;&lt;section style=&quot;margin-top: -0.8em;&quot;&gt;&lt;p style=&quot;margin-top: -2.5em; margin-left: 12px; color: rgb(202, 202, 202); font-size: 12px;&quot;&gt;&lt;br/&gt;
 &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;/p&gt;&lt;/section&gt;&lt;section&gt;&lt;/section&gt;&lt;/section&gt;&lt;section style=&quot;vertical-align: top; display: inline-block;&quot;&gt;&lt;/section&gt;&lt;section style=&quot;margin-top: -4.7em; margin-left: 1.5em; padding-top: 1.5em; padding-left: 2em; border-left: 1px solid rgb(202, 202, 202);&quot;&gt;&lt;section style=&quot;padding: 0.5em; background-color: white;&quot;&gt;&lt;p&gt;任务配图说明&lt;/p&gt;&lt;/section&gt;&lt;p style=&quot;margin-top: 0.5em; line-height: 1.5em;&quot;&gt;可以在这里放你的产品图片，方便用户找到你，或者任务要求说明，也可以放你的卡首屏二维码（可以保护你的产品只能给抢了任务的人看到）。&lt;span style=&quot;color: rgb(64, 179, 230);&quot;&gt;禁止放微信QQ等任何形势的联系方式&lt;/span&gt;
 &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;/p&gt;&lt;/section&gt;&lt;/section&gt;&lt;section class=&quot;_wxbEditor&quot;&gt;&lt;section style=&quot;padding: 1em; white-space: normal; border-top: 1px solid rgb(202, 202, 202); background-color: rgb(244, 244, 244); display: inline-block;&quot;&gt;&lt;section style=&quot;display: inline-block;&quot;&gt;&lt;section class=&quot;xhr&quot; style=&quot;height: 3em; width: 3em; background-color: rgb(216, 25, 25);&quot;&gt;&lt;section style=&quot;text-align: center;&quot;&gt;&lt;p style=&quot;margin-top: 8px; display: inline-block; font-size: 20px; color: white;&quot;&gt;3&lt;/p&gt;&lt;/section&gt;&lt;/section&gt;&lt;section style=&quot;margin-top: -0.8em;&quot;&gt;&lt;br/&gt;
 &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;/section&gt;&lt;section&gt;&lt;/section&gt;&lt;/section&gt;&lt;section style=&quot;vertical-align: top; display: inline-block;&quot;&gt;&lt;/section&gt;&lt;section style=&quot;margin-top: -3.2em; margin-left: 1.5em; padding-bottom: 1em; padding-left: 2em; border-left: 1px solid rgb(202, 202, 202);&quot;&gt;&lt;section style=&quot;padding: 0.5em; background-color: white;&quot;&gt;&lt;p&gt;卡首屏说明&lt;/p&gt;&lt;/section&gt;&lt;p style=&quot;margin-top: 0.5em; line-height: 1.5em;&quot;&gt;卡首屏功能，可以将你的产品中的任意关键字卡在搜索结果首屏，方便用户接任务的时候点击后直接找到你的产品。&lt;span style=&quot;color:#40b3e6&quot;&gt;卡首屏产生的流量为搜索流量，且计算搜索权重（是黑玩法必备的神器）&lt;/span&gt;
 &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;/p&gt;&lt;/section&gt;&lt;section style=&quot;display: inline-block;&quot;&gt;&lt;section class=&quot;xhr&quot; style=&quot;height: 3em; width: 3em; background-color: rgb(216, 25, 25);&quot;&gt;&lt;section style=&quot;text-align: center;&quot;&gt;&lt;p style=&quot;margin-top: 8px; display: inline-block; font-size: 20px; color: white;&quot;&gt;4&lt;/p&gt;&lt;/section&gt;&lt;/section&gt;&lt;section style=&quot;margin-top: -0.8em;&quot;&gt;&lt;p style=&quot;margin-top: -2.5em; margin-left: 12px; color: rgb(202, 202, 202); font-size: 12px;&quot;&gt;&nbsp;&nbsp;&nbsp;&nbsp;&lt;br/&gt;
 &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;/p&gt;&lt;/section&gt;&lt;section&gt;&lt;/section&gt;&lt;/section&gt;&lt;section style=&quot;vertical-align: top; display: inline-block;&quot;&gt;&lt;/section&gt;&lt;section style=&quot;margin-top: -4.7em; margin-left: 1.5em; padding-top: 1.5em; padding-left: 2em; border-left: 1px solid rgb(202, 202, 202);&quot;&gt;&lt;section style=&quot;padding: 0.5em; background-color: white;&quot;&gt;&lt;p&gt;其他说明&lt;/p&gt;&lt;/section&gt;&lt;p style=&quot;margin-top: 0.5em; line-height: 1.5em;&quot;&gt;&lt;span style=&quot;color: rgb(64, 179, 230);&quot;&gt;停留时间&lt;/span&gt;为用户抢到任务后多长时间后才可以回复。&lt;span style=&quot;color: rgb(64, 179, 230);&quot;&gt;限制回复&lt;/span&gt;，限制用户回复当前任务的次数，如果是1就智能回一次，系统默认用户每天只能接每个商家1次任务，比如，商家发布了10个任务，用户只能接其中1个，其他9个无法接取。&lt;span style=&quot;color: rgb(64, 179, 230);&quot;&gt;隐藏回复&lt;/span&gt;，建议勾选，这样用户回复的内容只有商家自己能看到其他人看不到。&lt;span style=&quot;color: rgb(64, 179, 230);&quot;&gt;置顶任务&lt;/span&gt;，当你急需完成流量要求的时候，可以置顶，这样所有的人都能看到你的任务，从而快速完成目标任务。&lt;/p&gt;&lt;/section&gt;&lt;/section&gt;&lt;p&gt;&lt;br/&gt;
 &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;/p&gt;&lt;p style=&quot;text-align: center;&quot;&gt;&lt;strong style=&quot;color: rgb(247, 23, 23); font-size: 18px; white-space: pre-wrap;&quot;&gt;第三步&lt;span style=&quot;font-family: &quot; microsoft=&quot;&quot;&gt;│管理任务&lt;/span&gt;&lt;/strong&gt;
 &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;/p&gt;&lt;section class=&quot;_wxbEditor&quot;&gt;&lt;section id=&quot;shifu_p_023&quot; donone=&quot;shifuMouseDownPic(&#39;shifu_p_023&#39;)&quot; label=&quot;Copyright Reserved by PLAYHUDONG.&quot; style=&quot;background:#ffffff;border-style: none; clear: both;margin: 1em auto;&quot;&gt;&lt;section style=&quot;padding: 0.5em; vertical-align: bottom; -webkit-box-shadow: rgba(0, 0, 0, 0.498039) 0px 0px 4px; box-shadow: rgba(0, 0, 0, 0.498039) 0px 0px 4px; width: 49%; margin-left: 1%; box-sizing: border-box; display: inline-block; background-image: initial; background-attachment: initial; background-size: initial; background-origin: initial; background-clip: initial; background-position: initial; background-repeat: initial;&quot;&gt;&lt;img style=&quot;vertical-align: middle; width: 100%; display: inline-block;&quot; src=&quot;http://o7znfws9w.bkt.clouddn.com/682689-temp-201705-19-1495172972542.png&quot; class=&quot; js_catchingremoteimage&quot; data-remoteid=&quot;c1470878594043&quot;/&gt;
 &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;/section&gt;&lt;section style=&quot;display: inline-block; vertical-align: bottom; padding-top: 10px; padding-left: 8px; line-height: 1.5; font-size: 14px; width: 50%; box-sizing: border-box;&quot;&gt;&lt;section style=&quot;font-size: 20px&quot;&gt;&lt;section style=&quot;font-size: 18px; display: inline-block;&quot; class=&quot;color&quot;&gt;◀&lt;/section&gt;&lt;p style=&quot;display: inline-block;&quot;&gt;采纳管理任务&lt;/p&gt;&lt;/section&gt;&lt;p&gt;在自己的任务页面，可以采纳符合要求的任务也可以拒绝不符合要求的任务（可批量操作）&lt;span style=&quot;color: rgb(64, 179, 230);&quot;&gt;点击操作按钮，可以追加任务&lt;/span&gt;，比如之前发布了30个任务，已经被执行完了，现在又想放20个出去，那么点击追加任务即可。&lt;span style=&quot;color: rgb(64, 179, 230);&quot;&gt;结算任务&lt;/span&gt;即对当前任务进行结算，&lt;span style=&quot;color: rgb(64, 179, 230);&quot;&gt;结算后任何人都不能进行回复&lt;/span&gt;（包过追加）结算任务前务必拒绝掉不符合要求的任务，否则所有任务都会自动采纳。&lt;/p&gt;&lt;/section&gt;&lt;/section&gt;&lt;section class=&quot;_wxbEditor&quot;&gt;&lt;section class=&quot;_wxbEditor&quot;&gt;&lt;p&gt;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;/p&gt;&lt;/section&gt;&lt;p style=&quot;text-align: center;&quot;&gt;&lt;br/&gt;
 &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;/p&gt;&lt;/section&gt;&lt;section class=&quot;_wxbEditor&quot;&gt;&lt;section label=&quot;Powered by 135editor.com&quot; style=&quot;font-family: 微软雅黑;&quot; class=&quot;active&quot;&gt;&lt;section class=&quot;135editor&quot; style=&quot;position: static; box-sizing: border-box; border: 0px none;&quot; data-id=&quot;86012&quot;&gt;&lt;section style=&quot;margin-top: 2em; margin-bottom: 10px; position: static; box-sizing: border-box;margin-top:35px&quot;&gt;&lt;section style=&quot;border-top-width: 1px;border-top-style: dashed;border-top-color: rgb(204, 204, 204);border-right-width: 1px;border-right-style: dashed;border-right-color: rgb(204, 204, 204);border-bottom-width: 1px;border-bottom-style: dashed;border-bottom-color: rgb(204, 204, 204);line-height: normal;text-align: center;padding: 1px;box-sizing: border-box;margin-bottom: 50px;&quot;&gt;&lt;section style=&quot;border: 1px dashed rgb(204, 204, 204); text-align: left; box-sizing: border-box;&quot;&gt;&lt;section style=&quot;display: inline-block; vertical-align: top;  padding: 6px; width: 165px; color: rgb(255, 255, 255); margin-bottom: -10px; float: left; margin-left: -4px; box-sizing: border-box; background-color: rgb(139, 162, 176);&quot; data-width=&quot;40%&quot;&gt;&lt;p style=&quot;line-height: 0; white-space: normal;&quot;&gt;&lt;img src=&quot;http://o7znfws9w.bkt.clouddn.com/682689-temp-201705-19-1495174494562.png&quot; opacity=&quot;&quot; mapurl=&quot;&quot; title=&quot;&quot; alt=&quot;&quot; style=&quot;width: 100%;&quot; data-width=&quot;100%&quot; width=&quot;100%&quot; border=&quot;0&quot; height=&quot;&quot;/&gt;
 &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;/p&gt;&lt;/section&gt;&lt;section style=&quot;width: 15px; height: 15px; border-radius: 50%; float: right; margin-top: -6px; margin-right: -6px; color: rgb(255, 255, 255); box-sizing: border-box; background-color: rgb(66, 85, 96);&quot; data-bgless=&quot;darken&quot;&gt;&lt;/section&gt;&lt;section style=&quot;width: 40px; height: 40px; border-radius: 50%; margin-top: -20px; float: left; margin-left: -15px; color: rgb(255, 255, 255); box-sizing: border-box; background-color: rgb(66, 85, 96);&quot; data-bgless=&quot;darken&quot;&gt;&lt;span style=&quot;color: rgb(241, 136, 35);&quot;&gt;&lt;/span&gt;
 &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;/section&gt;&lt;section style=&quot;vertical-align: top; padding: 20px 5px; display: inline-block; width: 45%; box-sizing: border-box;&quot; data-width=&quot;45%&quot;&gt;&lt;section style=&quot;border-bottom-width: 2px; border-bottom-style: solid; border-color: rgb(86, 111, 127); width: 100%; margin-bottom: 10px; box-sizing: border-box;&quot; data-bcless=&quot;darken&quot; data-width=&quot;100%&quot;&gt;&lt;span style=&quot;line-height: 1.75em;&quot; class=&quot;135brush&quot; data-brushtype=&quot;text&quot;&gt;保证金&lt;/span&gt;
 &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;/section&gt;&lt;p style=&quot;line-height: 1.5em; white-space: normal;&quot;&gt;&lt;span style=&quot;font-size: 14px;&quot; class=&quot;135brush&quot; data-brushtype=&quot;text&quot;&gt;发布任务之前需要缴纳保证金，为的是防止恶意商家保护大家的利益，保证金金额充值100元，就可以发布100元以内的任务（&lt;span style=&quot;font-size: 14px; color: rgb(255, 0, 0);&quot;&gt;最低缴纳50元保证金&lt;/span&gt;）。保证金不能作为余额使用，随时可提现！&lt;span style=&quot;color: rgb(64, 179, 230);&quot;&gt;账户内要有保证金+余额才可以发布任务&lt;/span&gt;！&lt;/span&gt;
 &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;/p&gt;&lt;/section&gt;&lt;/section&gt;&lt;/section&gt;&lt;/section&gt;&lt;/section&gt;&lt;/section&gt;&lt;/section&gt;&lt;p&gt;&lt;br/&gt;
 &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;/p&gt;&lt;p&gt;&lt;br/&gt;
 &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;/p&gt;&lt;/section&gt;&lt;/section&gt;&lt;/section&gt;&lt;/section&gt;&lt;p&gt;&lt;br/&gt;
 &nbsp; &nbsp;&lt;/p&gt;&lt;/div&gt;

div;

}