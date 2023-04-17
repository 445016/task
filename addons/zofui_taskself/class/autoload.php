<?php
	
	function autoLoad ($classname){

		$file = TSTB_ROOT.'class/'.$classname.".class.php";
		if( file_exists($file) ){
			include_once ($file);
			return true;
		}
		return false;
	}
	spl_autoload_register('autoLoad',false);
	
	
	
	class Data {
		
		static function webMenu(){
			global $_W,$_GPC;
			
			if( function_exists( 'buildframes' ) ){
				$myframes = buildframes('account');
				$seturl = $myframes['section']['platform_module_common']['menu']['platform_module_settings']['url'];
			}
			if( empty( $seturl ) ) $seturl = $_W['siteroot'].'web/index.php?c=profile&a=module&do=setting&op=set&m='.MODULE;

		  	$arr = array(
		  		/*'setting' => array(
		  			'name' => '参数设置',
		  			'icon' => 'https://res.wx.qq.com/mpres/htmledition/images/icon/menu/icon_menu_setup.png',
		  			'url' => $frames['rule']['items'][0]['url']
		  		),*/
		  		'setting' => array(
		  			'name' => '参数设置',
		  			'iconbd' => 'iconfont icon-setting',
		  			'icon' => 'https://res.wx.qq.com/mpres/htmledition/images/icon/menu/icon_menu_setup.png',
		  			'list'=>array(
		  				array('op'=>'set','name'=>'参数设置','url'=>$seturl),
		  				array('op'=>'instruct','name'=>'说明设置','url'=>Util::webUrl('setting',array('op'=>'instruct'))),
		  			),
		  			'toplist' => array()
		  		),
		  		'data' => array(
		  			'name' => '数据信息',
		  			'iconbd' => 'iconfont icon-data-graph',
		  			'icon' => 'https://res.wx.qq.com/mpres/htmledition/images/icon/menu/icon_menu_statistics.png',
		  			'list'=>array(
		  				array('op'=>'site','name'=>'平台数据','url'=>Util::webUrl('data',array('op'=>'site'))),
		  				
		  				array('op'=>'order','name'=>'订单记录','url'=>Util::webUrl('data',array('op'=>'order'))),
		  				array('op'=>'complain','name'=>'投诉记录','url'=>Util::webUrl('data',array('op'=>'complain'))),
		  				array('op'=>'message','name'=>'留言数据','url'=>Util::webUrl('data',array('op'=>'message'))),
		  				
		  				array('op'=>'sendmess','name'=>'群发进度','url'=>Util::webUrl('data',array('op'=>'sendmess'))),
		  			),
		  			'toplist' => array('site','user','order','complain','message','sendmess','authform')
		  		),
		  		'userdata' => array(
		  			'name' => '会员数据',
		  			'iconbd' => 'iconfont icon-myaccount',
		  			'icon' => 'https://res.wx.qq.com/mpres/htmledition/images/icon/menu/icon_menu_statistics.png',
		  			'list'=>array(
		  				array('op'=>'list','name'=>'会员列表','url'=>Util::webUrl('userdata',array('op'=>'list'))),
		  				array('op'=>'verify','name'=>'审核会员','url'=>Util::webUrl('userdata',array('op'=>'verify'))),
		  				array('op'=>'authform','name'=>'认证表单','url'=>Util::webUrl('userdata',array('op'=>'authform'))),
		  			),
		  			'toplist' => array('list','verify','authform')
		  		),		  		
		  		'money' => array(
		  			'name'=>'提现管理',
		  			'iconbd' => 'iconfont icon-overview-balance',
		  			'icon' => 'https://res.wx.qq.com/mpres/htmledition/images/icon/menu/icon_menu_management.png',
		  			'list'=>array(
		  				array('op'=>'waitpay','name'=>'待支付(微信)','url'=>Util::webUrl('money',array('op'=>'waitpay'))),
		  				array('op'=>'alipay','hide'=>1,'name'=>'待支付(支付宝)','url'=>Util::webUrl('money',array('op'=>'alipay'))),
		  				array('op'=>'qrpay','hide'=>1,'name'=>'待支付(扫码付)','url'=>Util::webUrl('money',array('op'=>'qrpay'))),
		  				array('op'=>'payed','hide'=>1,'name'=>'已支付','url'=>Util::webUrl('money',array('op'=>'payed'))),
		  				array('op'=>'back','hide'=>1,'name'=>'已退回','url'=>Util::webUrl('money',array('op'=>'back'))),
		  				array('op'=>'refuse','hide'=>1,'name'=>'已拒绝','url'=>Util::webUrl('money',array('op'=>'refuse'))),
		  				array('op'=>'log','hide'=>1,'name'=>$_W['cname'].'记录','url'=>Util::webUrl('money',array('op'=>'log'))),
		  			),
		  			'toplist' => array('waitpay','payed','back','refuse','log','alipay','qrpay')
		  		),
		  		'deposit' => array(
		  			'name'=>'保证金管理',
		  			'iconbd' => 'iconfont icon-authinfo',
		  			'icon' => 'https://res.wx.qq.com/mpres/htmledition/images/icon/menu/icon_menu_management.png',
		  			'list'=>array(
		  				array('op'=>'waitpay','name'=>'提取列表(微信)','url'=>Util::webUrl('deposit',array('op'=>'waitpay'))),
		  				array('op'=>'alipay','hide'=>1,'name'=>'提取列表(支付宝)','url'=>Util::webUrl('deposit',array('op'=>'alipay'))),
		  				array('op'=>'qrpay','hide'=>1,'name'=>'提取列表(扫码付)','url'=>Util::webUrl('deposit',array('op'=>'qrpay'))),
		  				array('op'=>'payed','hide'=>1,'name'=>'已提取','url'=>Util::webUrl('deposit',array('op'=>'payed'))),
		  				array('op'=>'back','hide'=>1,'name'=>'已退回','url'=>Util::webUrl('deposit',array('op'=>'back'))),
		  				array('op'=>'refuse','hide'=>1,'name'=>'已拒绝','url'=>Util::webUrl('deposit',array('op'=>'refuse'))),
		  				
		  				array('op'=>'log','hide'=>1,'name'=>'保证金记录','url'=>Util::webUrl('deposit',array('op'=>'log'))),
		  			),
		  			'toplist' => array('waitpay','payed','back','refuse','log','alipay','qrpay')
		  		),		  				  		
		  		'comtask' => array(
		  			'name'=>'普通任务',
		  			'iconbd' => 'iconfont icon-yuezhangdan',
		  			'icon' => 'https://res.wx.qq.com/mpres/htmledition/images/icon/menu/icon_menu_management.png',
		  			'list'=>array(
		  				array('op'=>'list','name'=>'任务列表','url'=>Util::webUrl('comtask',array('op'=>'list'))),
		  				array('op'=>'create','name'=>'发布任务','url'=>Util::webUrl('comtask',array('op'=>'create'))),
		  				array('op'=>'reply','name'=>'回复列表','url'=>Util::webUrl('comtask',array('op'=>'reply'))),
		  			),
		  			'toplist' => array('create','list','reply')
		  		),
		  		'tbtask' => array(
		  			'name'=>'担保任务',
		  			'iconbd' => 'iconfont icon-zhangdanguanli',
		  			'icon' => 'https://res.wx.qq.com/mpres/htmledition/images/icon/menu/icon_menu_management.png',
		  			'list'=>array(
		  				//array('op'=>'create','name'=>'发布任务','url'=>Util::webUrl('tbtask',array('op'=>'create'))),
		  				array('op'=>'list','name'=>'任务列表','url'=>Util::webUrl('tbtask',array('op'=>'list'))),
		  				array('op'=>'take','name'=>'执行列表','url'=>Util::webUrl('tbtask',array('op'=>'take'))),
		  			),
		  			'toplist' => array('create','list','take')
		  		),	  		
		  		'usetask' => array(
		  			'name'=>'试用任务',
		  			'iconbd' => 'iconfont icon-overview-desc',
		  			'icon' => 'https://res.wx.qq.com/mpres/htmledition/images/icon/menu/icon_menu_management.png',
		  			'list'=>array(
		  				array('op'=>'list','name'=>'任务列表','url'=>Util::webUrl('usetask',array('op'=>'list'))),
		  				array('op'=>'create','name'=>'发布任务','url'=>Util::webUrl('usetask',array('op'=>'create'))),
		  			),
		  			'toplist' => array('create','list'),
		  		),
		  		'privatetask' => array(
		  			'name'=>'私包任务',
		  			'iconbd' => 'iconfont icon-api',
		  			'icon' => 'https://res.wx.qq.com/mpres/htmledition/images/icon/menu/icon_menu_management.png',
		  			'list'=>array(
		  				array('op'=>'list','name'=>'全部任务','url'=>Util::webUrl('privatetask',array('op'=>'list')))
		  			),
		  			'toplist' => array('list')
		  		),
		  		'other' => array(
		  			'name'=>'其他功能',
		  			'iconbd' => 'iconfont icon-data-collection',
		  			'icon' => 'https://res.wx.qq.com/mpres/htmledition/images/icon/menu/icon_menu_management.png',
		  			'list'=>array(
		  				//array('op'=>'create','name'=>'添加人物分类','url'=>Util::webUrl('guysort',array('op'=>'create'))),
		  				array('do'=>'poster','op'=>'design','name'=>'设计海报','url'=>Util::webUrl('poster',array('op'=>'design'))),
		  				array('do'=>'quest','op'=>'list','name'=>'答疑中心','url'=>Util::webUrl('quest',array('op'=>'list'))),
		  				array('do'=>'guysort','op'=>'list','name'=>'人物分类','url'=>Util::webUrl('guysort',array('op'=>'list'))),
		  				array('do'=>'tasksort','op'=>'list','name'=>'任务分类','url'=>Util::webUrl('tasksort',array('op'=>'list'))),
		  				array('do'=>'slider','op'=>'list','name'=>'轮播轮播','url'=>Util::webUrl('slider',array('op'=>'list'))),
		  				array('do'=>'ad','op'=>'list','name'=>'公告管理','url'=>Util::webUrl('ad',array('op'=>'list'))),
		  				array('do'=>'tabbar','op'=>'list','name'=>'设置导航','url'=>Util::webUrl('tabbar',array('op'=>'list'))),
		  				array('do'=>'taskform','op'=>'list','name'=>'任务表单','url'=>Util::webUrl('taskform',array('op'=>'list'))),
		  			),
		  			'toplist' => array('design')
		  		),		  		
		  			
		  		'user' => array(
		  			'name'=>'会员信息',
		  			'iconbd' => 'iconfont icon-ai-setting',
		  			'icon' => 'https://res.wx.qq.com/mpres/htmledition/images/icon/menu/icon_menu_management.png',
		  			'list'=>array(
		  				array('op'=>'info','name'=>'基本','url'=>Util::webUrl('user',array('op'=>'info','id'=>$_GPC['id']))),
		  				array('op'=>'money','name'=>'收支','url'=>Util::webUrl('user',array('op'=>'money','id'=>$_GPC['id']))),
		  				array('op'=>'deposit','name'=>'保证金','url'=>Util::webUrl('user',array('op'=>'deposit','id'=>$_GPC['id']))),
		  				array('op'=>'pubpri','name'=>'发的私包','url'=>Util::webUrl('user',array('op'=>'pubpri','id'=>$_GPC['id']))),
		  				array('op'=>'taked','name'=>'收的私包','url'=>Util::webUrl('user',array('op'=>'taked','id'=>$_GPC['id']))),
		  				array('op'=>'pub','name'=>'发的任务','url'=>Util::webUrl('user',array('op'=>'pub','id'=>$_GPC['id']))),
		  				array('op'=>'reply','name'=>'回复任务','url'=>Util::webUrl('user',array('op'=>'reply','id'=>$_GPC['id']))),
		  				array('op'=>'pubtb','name'=>'发的担保','url'=>Util::webUrl('user',array('op'=>'pubtb','id'=>$_GPC['id']))),
		  				array('op'=>'dotb','name'=>'做的担保','url'=>Util::webUrl('user',array('op'=>'dotb','id'=>$_GPC['id']))),
		  				array('op'=>'pay','name'=>'支付','url'=>Util::webUrl('user',array('op'=>'pay','id'=>$_GPC['id']))),
		  				array('op'=>'down','name'=>'下级','url'=>Util::webUrl('user',array('op'=>'down','id'=>$_GPC['id']))),		  				
		  			),
		  			'toplist' => array('info','money','deposit','pubpri','taked','pub','reply','pay','down'),
		  			'hide' => 1,
		  		),	  		  		  				  			  		
		  		'explain' => array(
		  			'name'=>'模块说明',
		  			'iconbd' => 'iconfont icon-information',
		  			'icon' => 'https://res.wx.qq.com/mpres/htmledition/images/icon/menu/icon_menu_ad.png',
		  			'url' => Util::webUrl('explain'),
		  		),
		  	);
			

			return $arr;
			
		}
		

	}


	class topbal
	{
		

		static function comtaskList(){
			global $_W;
			$tasksort = model_tasksort::getSort();
			$list[] = array('value'=>'','name'=>'任务类型','url'=>WebCommon::logUrl('sort',''));
			foreach ($tasksort as $k => $v) {
				$list[] = array('value'=>$v['id'],'name'=>$v['name'],'url'=>WebCommon::logUrl('sort',$v['id']));
			}
			return array(
				'status' => array(
					array('value'=>'','name'=>'任务状态','url'=>WebCommon::logUrl('status','')),
					array('value'=>4,'name'=>'进行中','url'=>WebCommon::logUrl('status','4')),
					array('value'=>1,'name'=>'审核中','url'=>WebCommon::logUrl('status','1')),
					array('value'=>2,'name'=>'正常的','url'=>WebCommon::logUrl('status','2')),
					array('value'=>3,'name'=>'被下架','url'=>WebCommon::logUrl('status','3')),
				),
				'iscount' => array(
					array('value'=>'','name'=>'是否结算','url'=>WebCommon::logUrl('iscount','')),
					array('value'=>1,'name'=>'已结算','url'=>WebCommon::logUrl('iscount','1')),
					array('value'=>2,'name'=>'未结算','url'=>WebCommon::logUrl('iscount','2')),
				),	
				'istop' => array(
					array('value'=>'','name'=>'是否置顶','url'=>WebCommon::logUrl('istop','')),
					array('value'=>1,'name'=>'置顶的','url'=>WebCommon::logUrl('istop','1')),
					array('value'=>2,'name'=>'未置顶','url'=>WebCommon::logUrl('istop','2')),
				),			
				'shopname' => $list,
				'search' => array(
					array(
						'do'=>'comtask',
						'op' => 'list',
						'for' => 'userid',
						'placeholder' => '输入会员openid'
					),
					array(
						'do'=>'comtask',
						'op' => 'list',
						'for' => 'taskid',
						'placeholder' => '输入任务编号'
					),
					array(
						'do'=>'comtask',
						'op' => 'list',
						'for' => 'title',
						'placeholder' => '输入任务标题'
					),		
				)
			);
		}

		static function tbtaskList(){
			global $_W;
			return array(
				'status' => array(
					array('value'=>'','name'=>'任务状态','url'=>WebCommon::logUrl('status','')),
					array('value'=>4,'name'=>'进行中','url'=>WebCommon::logUrl('status','4')),
					array('value'=>1,'name'=>'审核中','url'=>WebCommon::logUrl('status','1')),
					array('value'=>2,'name'=>'正常的','url'=>WebCommon::logUrl('status','2')),
					array('value'=>3,'name'=>'被下架','url'=>WebCommon::logUrl('status','3')),
				),
				'iscount' => array(
					array('value'=>'','name'=>'是否结算','url'=>WebCommon::logUrl('iscount','')),
					array('value'=>1,'name'=>'已结算','url'=>WebCommon::logUrl('iscount','1')),
					array('value'=>2,'name'=>'未结算','url'=>WebCommon::logUrl('iscount','2')),
				),	
				'istop' => array(
					array('value'=>'','name'=>'是否置顶中','url'=>WebCommon::logUrl('istop','')),
					array('value'=>1,'name'=>'置顶中','url'=>WebCommon::logUrl('istop','1')),
					array('value'=>2,'name'=>'未置顶','url'=>WebCommon::logUrl('istop','2')),
				),
				'search' => array(
					array(
						'do'=>'tbtask',
						'op' => 'list',
						'for' => 'userid',
						'placeholder' => '输入发布者openid'
					),
					array(
						'do'=>'tbtask',
						'op' => 'list',
						'for' => 'taskid',
						'placeholder' => '输入任务编号'
					),					
				),

			);
		}


		static function tbtakeList(){
			global $_W;
			return array(
				'status' => array(
					array('value'=>'','name'=>'任务状态','url'=>WebCommon::logUrl('status','')),
					array('value'=>1,'name'=>'待初判','url'=>WebCommon::logUrl('status','1')),
					array('value'=>2,'name'=>'待终判','url'=>WebCommon::logUrl('status','2')),					
					array('value'=>3,'name'=>'待审核','url'=>WebCommon::logUrl('status','3')),
					array('value'=>4,'name'=>'已完成','url'=>WebCommon::logUrl('status','4')),
					array('value'=>5,'name'=>'已失败','url'=>WebCommon::logUrl('status','5')),
				),			
				'search' => array(
					/*array(
						'do'=>'tbtask',
						'op' => 'take',
						'for' => 'userid',
						'placeholder' => '输入执行者openid'
					),*/
					array(
						'do'=>'tbtask',
						'op' => 'take',
						'for' => 'taskid',
						'placeholder' => '输入任务编号'
					),
					array(
						'do'=>'tbtask',
						'op' => 'take',
						'for' => 'takedid',
						'placeholder' => '输入执行编号'
					),				
				),

			);
		}

		static function usetaskList(){
			global $_W;

			return array(
				'status' => array(
					array('value'=>'','name'=>'任务状态','url'=>WebCommon::logUrl('status','')),
					array('value'=>4,'name'=>'进行中','url'=>WebCommon::logUrl('status','4')),
					array('value'=>1,'name'=>'审核中','url'=>WebCommon::logUrl('status','1')),
					array('value'=>2,'name'=>'正常的','url'=>WebCommon::logUrl('status','2')),
					array('value'=>3,'name'=>'被下架','url'=>WebCommon::logUrl('status','3')),
				),
				'iscount' => array(
					array('value'=>'','name'=>'是否结算','url'=>WebCommon::logUrl('iscount','')),
					array('value'=>1,'name'=>'已结算','url'=>WebCommon::logUrl('iscount','1')),
					array('value'=>2,'name'=>'未结算','url'=>WebCommon::logUrl('iscount','2')),
				),	
				'istop' => array(
					array('value'=>'','name'=>'是否置顶','url'=>WebCommon::logUrl('istop','')),
					array('value'=>1,'name'=>'置顶的','url'=>WebCommon::logUrl('istop','1')),
					array('value'=>2,'name'=>'未置顶','url'=>WebCommon::logUrl('istop','2')),
				),							
				'search' => array(
					array(
						'do'=>'usetask',
						'op' => 'list',
						'for' => 'userid',
						'placeholder' => '输入会员openid'
					),
					array(
						'do'=>'usetask',
						'op' => 'list',
						'for' => 'taskid',
						'placeholder' => '输入任务编号'
					),
							
				)
			);
		}
		
		static function verifyuserList() {
			global $_W,$_GPC;

			return array(
				'status' => array(
					array('value'=>'','name'=>'待审核','url'=>WebCommon::logUrl('status','')),
					array('value'=>1,'name'=>'已通过','url'=>WebCommon::logUrl('status','1')),
				),
				'search' => array(
					array(
						'do'=>'userdata',
						'op' => $_GPC['op'],
						'for' => 'userid',
						'placeholder' => '输入会员编号'
					),							
				)
			);
		}

		static function replyList(){
			global $_W;

			return array(
				'status' => array(
					array('value'=>'','name'=>'回复状态','url'=>WebCommon::logUrl('status','')),
					array('value'=>1,'name'=>'待采纳','url'=>WebCommon::logUrl('status','1')),
					array('value'=>2,'name'=>'已采纳','url'=>WebCommon::logUrl('status','2')),
					array('value'=>3,'name'=>'被拒绝','url'=>WebCommon::logUrl('status','3')),
				)
			);
		}

		static function userList(){
			global $_W;

			return array(
				'status' => array(
					array('value'=>'','name'=>'会员状态','url'=>WebCommon::logUrl('status','')),
					array('value'=>1,'name'=>'正常','url'=>WebCommon::logUrl('status','1')),
					array('value'=>2,'name'=>'黑名单','url'=>WebCommon::logUrl('status','2')),
				),
				'order' => array(
					array('value'=>'','name'=>'排序方式','url'=>WebCommon::logUrl('order','')),
					array('value'=>1,'name'=>'按'.$_W['cname'].'排序','url'=>WebCommon::logUrl('order','1')),
					array('value'=>2,'name'=>'按保证金排序','url'=>WebCommon::logUrl('order','2')),
					array('value'=>3,'name'=>'按发布量排序','url'=>WebCommon::logUrl('order','3')),
					array('value'=>4,'name'=>'按回复量排序','url'=>WebCommon::logUrl('order','4')),
				),
				'search' => array(
					array(
						'do'=>'userdata',
						'op' => 'list',
						'for' => 'for',
						'placeholder' => '输入会员昵称',
					),
					array(
						'do'=>'userdata',
						'op' => 'list',
						'for' => 'userid',
						'placeholder' => '输入会员编号'
					),							
				)
			);
		}

		static function orderList(){
			global $_W;

			return array(
				'status' => array(
					array('value'=>'','name'=>'支付状态','url'=>WebCommon::logUrl('status','')),
					array('value'=>1,'name'=>'未支付','url'=>WebCommon::logUrl('status','1')),
					array('value'=>2,'name'=>'已支付','url'=>WebCommon::logUrl('status','2')),
				),
				'type' => array(
					array('value'=>'','name'=>'订单类型','url'=>WebCommon::logUrl('type','')),
					array('value'=>1,'name'=>'充值'.$_W['cname'],'url'=>WebCommon::logUrl('type','1')),
					array('value'=>2,'name'=>'充值保证金','url'=>WebCommon::logUrl('type','2')),
				),
			);
		}

		// 投诉
		static function complainList(){
			global $_W;

			return array(
				'status' => array(
					array('value'=>'','name'=>'未处理','url'=>WebCommon::logUrl('status','')),
					array('value'=>1,'name'=>'已处理','url'=>WebCommon::logUrl('status','1')),
				),
			);
		}


		static function messList(){
			global $_W;

			return array(
				'type' => array(
					array('value'=>'','name'=>'任务发布者','url'=>WebCommon::logUrl('type','')),
					array('value'=>1,'name'=>'前端会员','url'=>WebCommon::logUrl('type','1')),
					array('value'=>2,'name'=>'后台管理员','url'=>WebCommon::logUrl('type','2')),
				),
				'search' => array(
					array(
						'do'=>'data',
						'op' => 'message',
						'for' => 'taskid',
						'placeholder' => '输入任务编号'
					),							
				)
			);
		}
	}
