$(function(){


	common();

	queueueue();
	// 计划任务
	function queueueue(){
		Http('post','html','queue',{},function(data){},false);
		setInterval(function(){
			Http('post','html','queue',{},function(data){},false);
		},10000);		
	}
	
	$('.mark_complain').click(function(){
		var _this = $(this);
		var postdata={};
		postdata.tou = _this.parent().find('textarea[name="tou"]').val();
		postdata.id = _this.attr('id');
		
		Http('post','json','recomplain',postdata,function(data){
			webAlert(data.res);
			if( data.status == 200 ){
				_this.parents('tr').remove();
			}
		},true);
	});

	// 修改支付宝
	$('#editali').click(function(){
		var _this = $(this);
		var postdata={};
		
		postdata.name = _this.parent().find('input[name="aliname"]').val();
		postdata.account = _this.parent().find('input[name="alipay"]').val();
		postdata.id = _this.attr('uid');
		
		Http('post','json','edituserali',postdata,function(data){
			webAlert(data.res);
		},true);
	});

	// 修改账户密码
	$('.epass_user').click(function(){
		var _this = $(this);
		var postdata={};
		postdata.pass = _this.parents('.dropdown_data_list').find('input').val();
		postdata.uid = _this.attr('uid');
		
		Http('post','json','edituserpass',postdata,function(data){
			webAlert(data.res);
		},true);
	});

	//
	$('#addbarone').click(function(){
		if(confirm('使用此导航会将已经添加的导航清空，确定使用吗？')){
			Http('post','json','addbarone',{},function(data){
				webAlert(data.res);
				if(data.status == 200){
					setTimeout(function(){
						location.href = "";
					},500);
				}
			},true);
		}
	});	

	$('.mess_user').click(function(){
		var _this = $(this);
		var postdata={};
		postdata.mess = _this.parents('.dropdown_data_list').find('textarea').val();
		postdata.id = _this.attr('id');
		
		if(confirm('确定发送吗？')){
			Http('post','json','messuser',postdata,function(data){
				webAlert(data.res);
			},true);
		}
	});

	$('.restart').click(function(){
		var postdata = {
			tid : $(this).attr('tid'),
			puber : $(this).attr('puber'),
		};
		var str = '确定要将此任务重新恢复到未结算状态吗？';
		if( postdata.puber > 0 ) str = '此任务是会员在前端发布，如果恢复需要扣除发布者相应退回的资金，确定要将此任务重新恢复到未结算状态吗？';
		if(confirm(str)){
			Http('post','json','restart',postdata,function(data){
				webAlert(data.res);
				if(data.status == 200){
					setTimeout(function(){
						location.href = "";
					},500);
				}
			},true);
		}
	});	

	// clearauth
	$('#clearauth').click(function(){
		
		if(confirm('此功能是在更换发红包公众号参数后，清除以前绑定的借权数据，解决发红包提示appid和openid不匹配的问题，确定要清除吗？')){
			Http('post','json','clearauth',{},function(data){
				webAlert(data.res);
			},true);
		}
	});

	// 
	$('.addreply').click(function(){
		var postdata = {
			tid : $(this).attr('id'),
			content : $(this).parents('.dropdown_data_list').find('textarea').val(),
			num : $(this).parents('.dropdown_data_list').find('input').val(),
		}
		if( confirm('确定添加吗？') ) {
			Http('post','json','addreply',postdata,function(data){
				webAlert(data.res);
			},true);
		}
	});	
	
	$('.deleteallimg').click(function(){
		var postdata = {
			tid : $(this).attr('tid'),
			type : $(this).attr('type'),
		}
		if(confirm('清理图片后此任务和对应的接任务的图片都会被删除，确定要删除吗？')){
			Http('post','json','deleteallimg',postdata,function(data){
				webAlert(data.res);
			},true);
		}
	});

	$('.tosteptemp').click(function(){
		var postdata = {
			id : $(this).attr('id'),
			type : $(this).attr('type'),
		}
		
		Http('post','json','tosteptemp',postdata,function(data){
			webAlert(data.res);
		},true);
		
	});

	// 修改会员类型
	$('.elevel_user').click(function(){
		var _this = $(this);
		var postdata={};
		postdata.level = _this.parents('.dropdown_data_list').find('input[name="level"]').val();
		postdata.time = _this.parents('.dropdown_data_list').find('input[name="time"]').val();
		postdata.uid = _this.attr('uid');
		
		Http('post','json','edituserlevel',postdata,function(data){
			webAlert(data.res);
			if(data.status == 200){
				setTimeout(function(){
					location.href = "";
				},500);
			}
		},true);
	});

	//回复表单
	$('.add_appoint').click(function(){
		var type = $(this).attr('type');
		var name = $(this).text();
		var id = 'i' + $('.appoint_box_item').length + new Date().getTime();

		var se = '<div class="appoint_additem">'
				+'</div>'
				+'<div class="appoint_additem">'
					+'<span class="frm_input_box frm_input_150">'
						+'<input type="text" class="frm_input"  name="editvalue" >'
					+'</span>'
					+'<a href="javascript:;" class="add_appoint_item">添加选项</a>'
				+'</div>';

		if( type != 'single' && type != 'multi' ) {
			se = '';
		}

		var typestr = ' 提示文字 <span class="frm_input_box frm_input_150">'
						+'<input type="text" class="frm_input"  name="pla['+id+']" value="输入'+name+'">'
					+'</span>	<span class="font_mini">'+name+'</span>'
					+'<a href="javascript:;" class="delete_appoint_item">删除</a>	';
		if( type == 'img' ) {
			typestr = ' 需传图片 <span class="frm_input_box frm_input_150">'
						+'<input type="text" class="frm_input"  name="maxnum['+id+']" value="1" placeholder="填数字">'
					+'</span>	<span class="font_mini">'+name+'</span>'
					+'<a href="javascript:;" class="delete_appoint_item">删除</a>	';			
		}			


		var str = '<div class="good_rule_item appoint_box_item" ng-repeat="item in rule">'
						+'<input type="hidden" name="aid[]" value="'+id+'">'
						+'<input type="hidden" name="type['+id+']" value="'+type+'">'
						+'<div class="appoint_form_item">'
							+'<div>'
								+'名称<span class="frm_input_box frm_input_150">'
									+'<input type="text" class="frm_input"  name="name['+id+']" value="'+name+'">'
								+'</span>'
								+typestr
							+'</div>'+se
						+'</div>'
					+'</div>';
		$('.appoint_list').append(str);

	});

	$('body').on('click','.delstep',function(){
		$(this).parent().remove();
	});

	$('#add_step').click(function(){
		var h = $('.step_itemtemp').html();
		$(this).before('<div class="edit_right_list step_item step_itemn input_600 ">'+h+'</div>');
	})

	$('body').on('click','.addcopy',function(){
		var _this = $(this);
		var t = _this.prev().find('input').val();
		if( t == '' || !t ){
			return false;
		}

		var html = '<span class="ccitem copy_item">'+t+'<input type="hidden" name="copyitem" value="'+t+'"></span>';
		$(this).parents('.step_item').find('.copylist').append(html);
		_this.prev().find('input').val('');
	})

	$('body').on('click','.addlink',function(){
		var _this = $(this);
		var t = _this.parents('.item_cell_box').find('input[name="grehre"]').val();
		var u = _this.parents('.item_cell_box').find('input[name="ghervwev"]').val();

		if( t == '' || !t || u == '' || !u ){
			return false;
		}

		var html = '<span class="ccitem link_item">'+t+'<input type="hidden" name="titem" value="'+t+'"><input type="hidden" name="uitem" value="'+u+'"></span>';
		$(this).parents('.step_item').find('.urllist').append(html);
		_this.parents('.item_cell_box').find('input[name="grehre"]').val('');
		_this.parents('.item_cell_box').find('input[name="ghervwev"]').val('');
	})

	$('body').on('click','.ccitem',function(){
		$(this).remove();
	});


	$('body').on('click','.add_appoint_item',function(){
		var item = $(this).prev().find('input').val();
		var id = $(this).parents('.appoint_box_item').find('input[name="aid[]"]').val();
		if( item == '' ) return false;
		$(this).parent().prev().append( '<span class="appoint_additem_item">'+item+'<input type="hidden" name="sitem['+id+'][]" value="'+item+'"></span>' );
		$(this).prev().find('input').val('');
	});

	$('body').on('click','.appoint_additem_item',function(){
		$(this).remove();
	});

	$('body').on('click','.delete_appoint_item',function(){
		$(this).parents('.appoint_box_item').remove();
	});

	
	////////// 担保任务
	// 编辑担保任务
	$('input[name=addtbtask]').click(function(){
		var serializeArr = $('form').serializeArray();
		var postdata={};
		for (i in serializeArr) {
			postdata[ serializeArr[i].name ] = serializeArr[i].value;
		}
		postdata.upimages = arrval( $('input[name="images[]"]') );
		postdata.uphideimages = arrval( $('input[name="hideimages[]"]') );
		postdata.upkakey = arrval( $('input[name="kakey[]"]') );
		postdata.upstep = arrval( $('input[name="step[]"]') );
		
		Http('post','json','addtbtask',postdata,function(data){
			webAlert(data.res);
			if(data.status == 200){
				setTimeout(function(){
					location.href = "";
				},500);
			}
		},true);
	});
	

	// 回复 投诉
	$('.confirm_sendcomplain').click(function(){
		var thisclass = $(this);
		var postdata = {
			id: thisclass.attr('id'),
			content : thisclass.parents('.dropdown_menu_box').find('.drop_down_textarea').val(),
		};
		if( postdata.content == '' ) {webAlert('请输入回复内容');return false;}
		
		
		if(confirm('确定要回复吗？')){
			Http('post','json','replycomplain',postdata,function(data){
				webAlert(data.res);
				if(data.status == 200){
					setTimeout(function(){
						location.href = "";
					},500);
				}
			},true);
		}
	});

	$('.payqr').click(function(){
		var img = $(this).attr('src');
		var html = '<div><div class="hidepayqr mask ui-draggable" style="display: block;"></div><img style="position: fixed;top: 10%;z-index: 999;width: 300px;left: 50%;margin-left: -150px;" src="'+img+'"></div>';
		$('body').append(html);
	});
	$('body').on('click','.hidepayqr',function(){
		$(this).parent().fadeOut();
	});

	$('.setpayed').click(function(){
		var _this = $(this);
		var postdata = {
			oid: $(this).attr('oid'),
		};
		if(confirm('确定要设为已支付状态吗？')){
			Http('post','json','setpayed',postdata,function(data){
				webAlert(data.res);
				if(data.status == 200){
					_this.parents('tr').remove();
				}
			},true);
		}
	});

	$('#count_tbtask').click(function(){
		var thisclass = $(this);
		var postdata = {
			taskid: thisclass.attr('taskid'),
		};
		
		if(confirm('确定结算吗？')){
			Http('post','json','counttbtask',postdata,function(data){
				webAlert(data.res);
				if(data.status == 200){
					setTimeout(function(){
						location.href = "";
					},500);
				}
			},true);
		}
	});

	$('.icbtnuser').click(function(){
		var thisclass = $(this);
		var postdata = {
			uid: thisclass.attr('uid'),
			cid: thisclass.attr('cid'),
		};
		
		Http('post','json','icbtnuser',postdata,function(data){
			webAlert(data.res);
			if(data.status == 200){
				if( thisclass.hasClass('skin-ok-button') ){
					thisclass.removeClass('skin-ok-button');
				}else{
					thisclass.addClass('skin-ok-button');
				}
			}
		},true);
		
	});

	// 置顶
	$('.topordown').click(function(){
		var thisclass = $(this);
		var postdata = {
			tid: thisclass.attr('tid'),
			type : thisclass.attr('type'),
		};
		
		if(confirm('确定操作吗？')){
			Http('post','json','topordown',postdata,function(data){
				webAlert(data.res);
				if(data.status == 200){
					if(postdata.type == 'top') thisclass.text('取消置顶').attr('type','down');
					if(postdata.type == 'down') thisclass.text('置顶任务').attr('type','top');;
				}
			},true);
		}
	});

	// 编辑留言
	$('.confirm_editmess').click(function(){
		var thisclass = $(this);
		var postdata = {
			id: thisclass.attr('id'),
			content : thisclass.parents('.dropdown_menu_box').find('.drop_down_textarea').val(),
		};
		if( postdata.content == '' ) {webAlert('请输入留言内容');return false;}
		
		
		if(confirm('确定要修改吗？')){
			Http('post','json','editmess',postdata,function(data){
				webAlert(data.res);
				if(data.status == 200){
					setTimeout(function(){
						location.href = "";
					},500);
				}
			},true);
		}
	});

	// 提交提醒
	$('.confirm_remind').click(function(){
		var thisclass = $(this);
		var postdata = {
			id: thisclass.attr('id'),
			type : thisclass.attr('type'),
			content : thisclass.parents('.dropdown_menu_box').find('.drop_down_textarea').val(),
		};
		
		if(confirm('确定提交吗？')){
			Http('post','json','sendremind',postdata,function(data){
				webAlert(data.res);
				if( data.status == 200 ) {
					thisclass.parents('.jsDropdownsList').hide();
				}
			},true);
		}
	});
	////////////


	// 批量发放余额
	$('.addmoneyall').click(function(){
		var arr = [];
		$('input[name="checkall[]"]:checked').each(function(){
			arr.push( $(this).val() );
		});
		var money = $(this).parents('.addmoneyall_box').find('input').val();
		if( arr.length <= 0 ) {
			webAlert('还没选择数据');
			return false;
		}
		if( confirm('确定要为选择的提交者发放余额吗？') ){
			Http('post','json','sendmoneyall',{data:arr,money:money},function(data){
				webAlert(data.res);
				if( data.status == 200 ) {
					$('.addmoneyall_box').hide();
				}
			},true);
		}
	});

	// 发测试消息
	$('#testmess').click(function(){
		Http('post','json','testmess',{},function(data){
			alert( data.res );
		},true);
	});

	// 更新粉丝数据
	$('#upuserdata').click(function(){
		Http('post','json','upuserdata',{},function(data){
			alert( data.res );
		},true);
	});

	// 发消息
	$('.send_mess').click(function(){
		$('.my_model').show();
	});

	// 
	window.sendtid = 0;
	window.ttitle = '';
	window.fee = 0;
	$('.send_mess').click(function(){
		
		window.fee = $(this).attr('tfee');
		window.ttitle = $(this).attr('ttitle');
		window.sendtid = $(this).attr('tid');
		$('input[name=messname]').val(ttitle);
		$('input[name=messfee]').val(fee);

	});
	$('#confirm_sendall,#confirm_sendadmin').click(function(){
		var postdata = {
			tid : sendtid,
			name : $('input[name=messname]').val(),
			fee : $('input[name=messfee]').val(),
			botstr : $('textarea[name=messbot]').val(),
			topstr : $('textarea[name=messtitle]').val(),
			type : $(this).attr('type'),
		}
		if( confirm('确定发送吗？') ) {
			Http('post','json','sendmess',postdata,function(data){
				webAlert(data.res);
				if(data.status == 200 && postdata.type == 'all'){
					setTimeout(function(){
						location.href = "";
					},500);
				}
			},true);
		}
	});

	$('#stop_send').click(function(){
		if( confirm('确定停止吗？') ) {
			Http('post','json','stopsendmess',{},function(data){
				webAlert(data.res);
				if( data.status == 200 ){
					setTimeout(function(){
						location.href = "";
					},500);
				}
			},true);
		}
	});

	// 群发进度
	if( $('.sendmess_ing').length > 0 ) {

		function uppro(){
			Http('post','json','uppro',{},function(data){
				if(data.status == 200){
					$('.sendmess_topstr').text('顶部：'+data.obj.sendinfo.topstr).next().text('名称：'+data.obj.sendinfo.task.title)
					.next().text('赏金：'+data.obj.sendinfo.fee).next().text('底部：'+data.obj.sendinfo.botstr);
					$('.sendmess_proper').text( data.obj.per );
					$('.sendmess_pronum').text( data.obj.pronum ).next( data.obj.sendinfo.total );
				}else{
					$('.no_sendmess').show().next().hide();
				}
				setTimeout(function(){
					uppro();
				},1000);

			},false);
		}
		uppro();
	}

	// 审核不通过
	$('.verify_nopass').click(function(){
		var postdata = {
			uid : $(this).attr('id'),
			reason : $(this).parents('.dropdown_data_list').find('textarea').val(),
		}
		if( confirm('确定设为不通过吗？') ) {
			Http('post','json','verifynopass',postdata,function(data){
				webAlert(data.res);
				if( data.status == 200 ){
					setTimeout(function(){
						location.href = "";
					},500);
				}
			},true);
		}
	});
	// 备注
	$('.mark_user').click(function(){
		var postdata = {
			uid : $(this).attr('id'),
			mark : $(this).parents('.dropdown_data_list').find('textarea').val(),
		}
		if( confirm('确定设置吗？') ) {
			Http('post','json','givemark',postdata,function(data){
				webAlert(data.res);
				if( data.status == 200 ){
					setTimeout(function(){
						location.href = "";
					},500);
				}
			},true);
		}
	});	

	$('.ic_item').click(function(){
		var _this = $(this);
		var postdata = {
			cid : _this.attr('cid'),
		}
		if( confirm('确定删除吗？') ) {
			Http('post','json','delic',postdata,function(data){
				webAlert(data.res);
				if( data.status == 200 ){
					_this.remove();
				}
			},true);
		}
	});


	// 添加任务
	$('input[name=addtask]').click(function(){
		var serializeArr = $('form').serializeArray();
		var postdata={};
		for (i in serializeArr) {
			postdata[ serializeArr[i].name ] = serializeArr[i].value;
		}
		postdata.upgewvdv = arrval( $('input[name="gewvdv[]"]') );
		postdata.upkakey = arrval( $('input[name="kakey[]"]') );
		postdata.upurlname = arrval( $('input[name="urlname[]"]') );
		postdata.upurlurl = arrval( $('input[name="urlurl[]"]') );
		postdata.upic = arrval( $('input[name="ic[]"]:checked') );

		var step = [];
		$('.step_itemn').each(function(){
			var stepname = $(this).find('textarea[name="rehebe"]').val();

			var url = [];
			$(this).find('.link_item').each(function(){					
				url.push({text:$(this).find('input[name="titem"]').val(),url:$(this).find('input[name="uitem"]').val()});
			})

			var copy = [];
			$(this).find('.copy_item').each(function(){
				copy.push($(this).find('input[name="copyitem"]').val());
			})

			var img = [];
			$(this).find('input[name="jergwegwe[]"]').each(function(){
				img.push($(this).val());
			})
			step.push( {name:stepname,url:url,copy:copy,img:img} );
		});
		if( step.length > 0 ){
			postdata.step = step;
			//postdata.step = JSON.stringify(step);
		}

		Http('post','json','addtask',postdata,function(data){
			webAlert(data.res);
			if(data.status == 200){
				setTimeout(function(){
					location.href = "";
				},500);
			}
		},true);

	});

	// 发布试用任务
	var taogood = {};
	$('#get_tao_good').click(function(){
		var url = $('input[name="taourl"]').val();
		if( url == '' ){
			webAlert('请填写链接再点确定');
			return false;
		}
		Http('post','json','gettao',{url:url},function(data){
			if(data.status == 200){
				taogood = data.obj;
				$('input[name="pic"]').val( data.obj.pic );
				$('input[name="pic"]').parent().next().show().find('img').attr('src',data.obj.pic);
				$('textarea[name="gtitle"]').val( data.obj.title );
				//$('.usetask_box .task_taogood_img img').attr('src',data.obj.pic);
				//$('.usetask_box .task_taogood_title').text(data.obj.title);
				//$('input[name=paymoney]').val( taogood.nowprice );
				$('.usetask_box').show();
			}else{
				webAlert(data.res);
			}
		},true);		

	});
	// 设置下单商品
	$('#set_buygood').click(function(){
		$('textarea[name=prizetitle]').val( taogood.title );
		$('input[name=prizeimg]').val( taogood.pic );

		$('#insert_prizeimgbox .input-group').show().find('img').attr('src',taogood.pic);
		//$('#insert_prizeimgbox .input-group').eq(1).show().html('<img src="'+taogood.pic+'" class="img-responsive img-thumbnail" width="80px" height="80px">\
				//<em class="close" style="position:absolute; top: 0px; right: -14px;" title="删除这张图片" onclick="deleteImage(this)">×</em>')
	});

	$('input[name=pubusetask]').click(function(){
		var serializeArr = $('form').serializeArray();
		var postdata={};
		for (i in serializeArr) {
			postdata[ serializeArr[i].name ] = serializeArr[i].value;
		}
		postdata.upimages = arrval( $('input[name="images[]"]') );	
		postdata.findkey = arrval( $('input[name="kakey[]"]') );

		Http('post','json','pubusetask',postdata,function(data){
			webAlert(data.res);
			if(data.status == 200){
				setTimeout(function(){
					location.href = "";
				},500);
			}
		},true);
	});	

	// 处理投诉任务
	$('.deal_btn').click(function(){
		var thisclass = $(this);
		var postdata = {
			type: thisclass.attr('data-type'),
			taskid : thisclass.parent().attr('data-taskid'),
			reason : $('textarea[name=reason]').val()
		};
		if(postdata.reason == '') {webAlert('请输入理由');return false;}

		if(confirm('处理后将不可恢复状态，确定要进行此操作吗？')){
			Http('post','json','dealprivate',postdata,function(data){
				if(data.status == 200){
					location.href = "";
				}else{
					webAlert(data.res);
				}
			},true);
		}
	});

	// 处理试用任务
	$('body').on('click','.deal_usetask',function(){
		var postdata = {
			type : $(this).attr('type'),
			rid : $(this).attr('rid'),
		}
		var noticestr = '确定要通过审核吗？';
		if( postdata.type == 2 ) noticestr = '确定要审核不通过吗？';
		if( postdata.type == 3 ) noticestr = '确定要设为失败吗？';
		if( postdata.type == 4 ) noticestr = '确定要设为完成吗？';
		if( postdata.type == 5 ) noticestr = '确定要提醒对方吗？';
		if( postdata.type == 6 ) noticestr = '确定要转为完成吗？';

		if( postdata.type == 5 || postdata.type == 3 ){
			var thisnotice = postdata.type == 5 ? '请输入提醒内容' : '请输入失败原因';
			postdata.value = prompt(thisnotice,"");
			if( postdata.value == null ){
				return false;
			}
		}

		if(confirm( noticestr )){
			Http('post','json','dealusetask',postdata,function(data){
				if(data.status == 200){
					webAlert('操作成功');
					setTimeout(function(){
						location.href = "";
					},600);
				}else{
					webAlert(data.res);
				}
			},true);
		}		

	});

	// 改变资金
	$('.confirm_money').click(function(){
		var thisclass = $(this);
		var postdata = {
			id: thisclass.attr('id'),
			type : thisclass.parents('.dropdown_menu_box').find('.drop_down_select').val(),
			value : thisclass.parents('.dropdown_menu_box').find('.drop_down_input').val(),
		};
		if( postdata.value == '' ) {webAlert('请输入改变的数值');return false;}
		var str = '余额';
		if( postdata.type == 2 ) str = '保证金';
		if( postdata.type == 3 ) str = '活跃度';


		if(confirm('确定要改变他的'+str+'吗？')){
			Http('post','json','changemoney',postdata,function(data){
				webAlert(data.res);
				if(data.status == 200){
					setTimeout(function(){
						location.href = "";
					},500);
				}
			},true);
		}


	});



	// 回复
	$('.confirm_reply').click(function(){
		var thisclass = $(this);
		var postdata = {
			id: thisclass.attr('id'),
			content : thisclass.parents('.dropdown_menu_box').find('.drop_down_input').val(),
		};
		if( postdata.content == '' ) {webAlert('请输入回复内容');return false;}
		
		
		if(confirm('确定要回复吗？')){
			Http('post','json','replymess',postdata,function(data){
				webAlert(data.res);
				if(data.status == 200){
					setTimeout(function(){
						location.href = "";
					},500);
				}
			},true);
		}


	});


	// 增加置顶时间
	$('.confirm_addtoptime').click(function(){
		var thisclass = $(this);
		var postdata = {
			id: thisclass.attr('id'),
			value : thisclass.parents('.dropdown_menu_box').find('.drop_down_input').val(),
		};
		if( postdata.value == '' ) {webAlert('请输入数值');return false;}
		if(confirm('确定提交吗？')){
			Http('post','json','addtoptime',postdata,function(data){
				webAlert(data.res);
				if(data.status == 200){
					setTimeout(function(){
						location.href = "";
					},500);
				}
			},true);
		}
	});

	// 处理回复
	$('.reply_bottom .dealreply').click(function(){
		var thisclass = $(this);
		var httpdata = {
			type: thisclass.attr('data-type'),
			replyid : thisclass.parents('.reply_bottom').attr('data-replyid'),
			alertstr : thisclass.attr('data-alert'),
			reason : thisclass.parents('.dropdown_data_list').find('.drop_down_textarea').val(),
		};
		if(confirm(httpdata.alertstr)){
			Http('post','json','dealtask',httpdata,function(data){
				webAlert(data.res);
				if(data.status == 200){
					
					if(httpdata.type == 'accept'){
						thisclass.parent().hide();
						thisclass.parents('.media').find('.reply_status').text('已采纳');
					} 
					if(httpdata.type == 'refuse') {
						thisclass.parents('.opclass').hide();
						thisclass.parents('.media').find('.reply_status').text('已拒绝');
					} 
					if(httpdata.type == 'noscan') {
						thisclass.parents('.media').find('.reply_status').text('禁止浏览');
						thisclass.hide().next().show();
					}
					if(httpdata.type == 'allowscan'){
						thisclass.parents('.media').find('.reply_status').text('已恢复浏览');
						thisclass.hide().prev().show();
					}
					if(httpdata.type == 'delete'){
						thisclass.parents('.taskinfo_itemaaa').remove();
					}
					if(httpdata.type == 'remind'){
						setTimeout(function(){
							window.location.reload();
						},500);
					}
				}
			},true);
		}
		
	});


	// 删除价格
	$('body').on('click','.delete_price',function(){
		$(this).parents('.edit_right_item').remove();
		if( $('.group_price_box .edit_right_item').length > 1 ){
			$('.group_price_box .edit_right_item').last().append(' <div class="btn btn_warn btn_mini delete_price"> 删除</div>');
		}
	});

	// 拖拽
	$( ".multi-img-details" ).sortable();
	$( ".multi-img-details" ).disableSelection();	




	//参数设置
	$('#add_params').click(function(){
		var html = '<div class="edit_right_item">'
					+'名称<span class="frm_input_box frm_input_box_150">'
						+'<input type="text" class="frm_input"  name="paramsname[]" value="">'
					+'</span>属性<span class="frm_input_box frm_input_box_300">'
						+'<input type="text" class="frm_input"  name="paramspro[]" value="">'
					+'</span>'
					+'<a href="javascript:;" class="delete_params"> 删除</a>'
				+'</div>';
		$('.group_params_box').append(html);
	});

	// 删除属性
	$('body').on('click','.delete_params',function(){
		$(this).parents('.edit_right_item').remove();
	});

	// 超链接add_url
	$('#add_url').click(function(){
		var html = '<div class="edit_right_item">'
					+'文字<span class="frm_input_box frm_input_box_150">'
						+'<input type="text" class="frm_input"  name="urlname[]" value="">'
					+'</span>链接<span class="frm_input_box frm_input_box_300">'
						+'<input type="text" class="frm_input"  name="urlurl[]" value="">'
					+'</span>'
					+'<a href="javascript:;" class="delete_params"> 删除</a>'
				+'</div>';
		$('.group_params_box').append(html);
	});

	//标签
	$('#add_icon').click(function(){
		var html = ' <span class="frm_input_box frm_input_box_100" >'
						+'<font class="delete_icon delete_c">x</font>'
						+'<input type="text" class="frm_input"  name="kakey[]" value="">'
					+'</span>';
		$(this).next().append(html);
	});

	$('body').on('click','.delete_icon',function(){
		$(this).parent().remove();		
	});

	// 规格
	$('#add_rule').click(function(){
		var num = $('.group_rule_box .edit_right_item').length;
		var html = '<div class="edit_right_item">'
							+'<font class="delete_icon delete_c">x</font>'
							+'<div class="group_rule_item">'
								+'规格名称 <span class="frm_input_box frm_input_box_100" >'
									+'<input type="text" class="frm_input"  name="rulename['+num+'][]" value="">'
								+'</span>'
							+'</div>'
							+'<div class="item_cell_box group_rule_item">'
								+'<div class="">'
									+'规格属性&nbsp;'
								+'</div>'
								+'<div class="item_cell_flex rule_list">'
									+'<span class="frm_input_box frm_input_box_100" >'
										+'<input type="text" class="frm_input" name="rulepro['+num+'][]" value="">'
									+'</span>'
									+'<a href="javascript:;" class="add_rule_item" data-no="'+num+'">添加</a>'
								+'</div>'
							+'</div>'
						+'</div>';
		$('.group_rule_box').append(html);
	});

	$('body').on('click','.add_rule_item',function(){
		var num = $(this).attr('data-no');
		var html = '<span class="frm_input_box frm_input_box_100" >'
						+'<input type="text" class="frm_input" name="rulepro['+num+'][]" value="">'
					+'</span>';
		$(html).insertBefore($(this));
	});

	$('body').on('dblclick','.rule_list .frm_input',function(){
		$(this).parent().remove();
	});


	// 选择区域
	$('body').on('click','#js_selectarea_opt a',function(){
		var type = $('input[name=isarealimit]:checked').val();
		if( type ==1 ){
			$('.delivery_county,.delivery_city').show();
		}else if( type ==2 ){
			$('.delivery_county').hide();
			$('.delivery_city').show();
		}else if( type ==3 ){
			$('.delivery_county,.delivery_city').hide();
		}

		var  obj = new areaSelect($(this).next());
		obj.init();
		$('.ui-draggable').show();
	});

	// 管理员
	$('#add_admin').click(function(){
		var num = $('.admin_item').length;
		var html = '<div class="edit_right_item admin_item">'
						+'管理员昵称<span class="frm_input_box frm_input_box_150">'
							+'<input type="text" class="frm_input"  name="adminnick[]" value="">'
							+'<input type="hidden" class="frm_input"  name="adminhead[]" value="">'
							+'<input type="hidden" class="frm_input"  name="adminuid[]" value="">'
						+'</span>'
						+'<a href="javascript:;" class="search_admin"> 搜索</a> 管理员头像 '
						+'<span class="frm_input_box_70" style="display: inline-block;">'
							+' <img src="" width="40px" height="40px">'
						+'</span>'
			      			+' <label class="frm_checkbox_label" > '
			     				+' <i class="icon_checkbox"></i> '
			     				+'<span class="lbl_content">接通知</span>'
			     				+'<input type="checkbox" class="frm_checkbox" value="1" name="ismess['+ (num+1) +']" /> '
			     			+'</label>'
						+'<a href="javascript:;" class="delete_params"> 删除</a>'
					+'</div>';
		$('.group_admin_box').append(html);
	});

	$('body').on('click','.search_admin',function(){
		var _this = $(this);
		var postdata = {
			nick : _this.parents('.admin_item').find('input[name="adminnick[]"]').val()
		};
		Http('post','json','findadmin',postdata,function(data){
			if(data.status == 200){
				_this.prev().find('input[name="adminnick[]"]').val(data.obj.nick).next().val(data.obj.headimgurl).next().val(data.obj.uid);
				_this.next().find('img').attr('src',data.obj.headimgurl);
				webAlert('已搜索到，保存后才生效');
			}else{
				webAlert(data.res);
			}
		},true);
	});

	// 操作订单
	$('.order_deal_btn').click(function(){
		var $this = $(this);
		var postdata = {
			id : $this.attr('oid'),
			type : $this.attr('type'),
			expressname : $this.parents('.dropdown_data_list').find('input[name="expressname"]').val(),
			expressnum : $this.parents('.dropdown_data_list').find('input[name="expressnum"]').val(),
			refundmoney : $this.parents('.dropdown_data_list').find('input[name="refundmoney"]').val()
		};
		
		if(confirm('确定要操作吗？')){
			Http('post','json','dealorder',postdata,function(data){
				if(data.status == 200){
					webAlert(data.res);
					setTimeout(function(){
						if( postdata.type == 'delete' ){
							location.href = window.sysinfo.siteroot + 'web/index.php?c=site&a=entry&op=1&do=order&m=zofui_taskself';
						}else{
							location.href = "";
						}
					},500);
				}else{
					webAlert(data.res);
				}
			},true);
		}

	});


	// 修改地址
	$('#js_editAddress').click(function(){
		var $this = $(this);
		var data = {
			id : $this.attr('oid'),
			name : $this.parents('.msg').find('input[name="name"]').val(),
			tel : $this.parents('.msg').find('input[name="tel"]').val(),
			address : $this.parents('.msg').find('input[name="address"]').val(),
		};
		
		if(confirm('确定要修改吗？')){
			Http('post','json','editaddress',data,function(data){
				if(data.status == 200){
					webAlert(data.res);
					setTimeout(function(){
						location.href = "";
					},500);
				}else{
					webAlert(data.res);
				}
			},true);
		}		

	});


	// 复制链接
	require(['jquery.zclip'], function(){
		$('.copy_url').zclip({
			path: './resource/components/zclip/ZeroClipboard.swf',
			copy: function(){
				return $(this).attr('data-href');
			},
			afterCopy: function(){
				webAlert('复制成功');
			}
		});
	});	

	// 清理缓存
	$('.deletecache').click(function(){
		var type = $(this).attr('type');
		if(confirm('确定删除吗？')){
			Http('post','html','deletecache',{type:type},function(data){
				if(data == 1){
					webAlert('已删除');
				}else{
					webAlert('删除失败');
				}
			},true);
		}
	});
	//设置计划任务
	$('.queue_btn').click(function(){
		Http('post','json','setqueue',{},function(data){
			if(data.status == 200){
				webAlert('已设置');
			}else{
				webAlert('设置异常，删除缓存然后再试下。');
			}
		},true);
	});


	// 导入数据 改变显示文字
	$('input[name=inputfile]').change(function(){
		var v = $(this).val();
		$(this).prev().text(v);
	});


	// 编辑排序
	var nowvalue;
	$('.edit_number_input').focus(function(){
		$(this).addClass('edit_number_input_act');
		nowvalue = $(this).val();
	});
	$('.edit_number_input').blur(function(){
		$(this).removeClass('edit_number_input_act');
		if(nowvalue == $(this).val()) return false;
		var data = {
			type : $(this).attr('inputtype'),
			value : $(this).val(),
			name : $(this).attr('inputname'),
			id : $(this).attr('id')
		};
		Http('post','html','editvalue',data,function(data){},true);
	});


	// 搜索
	$('.js_search').click(function(){
		$(this).parents('form').submit();
	});

	// 拉黑和恢复
	$('.edit_user').click(function(){
		var data = {
			type : $(this).attr('type'),
			id : $(this).attr('id')
		};
		if(confirm('确定执行操作吗？')){
			Http('post','html','edituser',data,function(data){
				if(data == 1){
					alert('操作完成');
					location.href = "";
				}else{
					alert('操作失败');
				}
			},true);
		}
	});
	

	// 切换参数设置
	$('.js_top').click(function(){
		$('.js_top').removeClass('selected');
		var thisclass = $(this).attr('showme');
		$(this).addClass('selected');

		util.cookie.set('setjs',thisclass);

		$('.settings_group').each(function(){
			if($(this).hasClass(thisclass)){
				$(this).show();
			}else{
				$(this).hide();
			}
		})
	});

	var setclass = util.cookie.get('setjs');
	if( setclass ){
		$('.js_top').removeClass('selected');
		$('.js_top[showme="'+setclass+'"]').addClass('selected').show();
		$('.settings_group').hide();
		$('.settings_group.'+setclass).show();
	}
	

})

	function common(){
		
		$('body').on('mouseenter','.show_good_qrcode',function(){
			$('.actimg').hide();
			$(this).next().show();
		});

		$('body').on('mouseleave','.show_good_qrcode',function(){
			$('.actimg').hide();
		});	

		$('.topbar_jsbtn').on('click',function(){
			var type = $(this).attr('js');
			$('.my_model['+type+']').show();
		});

		$('body').on('click','.hide_item',function(){
			var elem = $(this).attr('hideitem');
			if( elem ){
				var arr = elem.split(",");
				for (var i = 0; i < arr.length; i++) {
					$(arr[i]).hide();
				}
			}
		});
		$('body').on('click','.show_item',function(){
			var elem = $(this).attr('showitem');
			if( elem ){
				var arr = elem.split(",");
				for (var i = 0; i < arr.length; i++) {
					$(arr[i]).show();
				}
			}
		});
		//
		$('.model_close').click(function(){
			$(this).parents('.my_model').hide();
		});

		//下拉选择
		$('body').on('click','.radio_list_item',function(){
			var txt = $(this).text();
			$(this).find('input').prop('checked',true);
			$(this).parents('.dropdown_menu').find('.jsBtLabel').text(txt).end().addClass('open');

		});
		$('.radio_list_input:checked').each(function(){
			var txt = $(this).parent().text();
			$(this).parents('.dropdown_menu').find('.jsBtLabel').text(txt);
		});

		//点击相应位置隐藏筛选/下拉
		$('body').on('click',function(e) {
			if($(e.target).parents('.dropdown_topbar').length <= 0){
				$('.dropdown_menu').each(function(){
					var $this = $(this);
					if($this.hasClass('open')) $this.removeClass('open');
				})
			}
		});	

		// 切换参数设置
		$('.js_top').click(function(){
			$('.js_top').removeClass('selected');
			var thisclass = $(this).attr('showme');
			$(this).addClass('selected');
			$('.settings_group').each(function(){
				if($(this).hasClass(thisclass)){
					$(this).show();
				}else{
					$(this).hide();
				}
			})
		});

		// table内编辑框
		$('.drop_down_editbtn').click(function(){
			$('.jsDropdownsList').hide();

			$(this).parents('.opclass').eq(0).find('.jsDropdownsList').toggle();
		})
		$('body').on('click','.dropdown_edit_cancel',function(){
			$(this).parents('.jsDropdownsList').hide();
		});

		// 自动选择单选框
		$('.frm_radio').each(function(){
			var $this = $(this);
			if($this.attr('checked')){
				$this.parents('.frm_radio_label').addClass('selected');
			}
		});
		$('.frm_checkbox').each(function(){
			var $this = $(this);
			if($this.attr('checked')){
				$this.parents('.frm_checkbox_label').addClass('selected');
			}
		});		

		// checkbox
		$('body').on('click','.frm_radio_label',function(e){
			e.preventDefault();
			if( $(this).hasClass('disabled') ) return false;
			var $this = $(this);
			var name = $this.find('input[type=radio]').prop('name');
			
			$('input[name='+name+']').each(function(){
				$(this).prop('checked',false);
				$(this).parents('.frm_radio_label').removeClass('selected');
			})
			$this.addClass('selected').find('input').prop('checked',true);
		});


		// 复选框 包括全选
		$('body').on('click','.frm_checkbox_label',function(e){
			var checkbox = $(this).find('input[type=checkbox]');
			if( $(this).hasClass('disabled') ) return false;
			
			var isall = $(this).prop('for') == 'selectAll';
			if( checkbox.prop("checked") ){
				checkbox.prop("checked",false);
				$(this).removeClass('selected');
				if(isall){
					$('.tbody input[type=checkbox],.checkall input[type=checkbox]').each(function(){
						$(this).parents('.frm_checkbox_label').removeClass('selected');
						$(this).prop("checked",false);
					})
				}
			}else{
				checkbox.prop("checked",true);
				$(this).addClass('selected');
				if(isall){					
					$('.tbody input[type=checkbox],.checkall input[type=checkbox]').each(function(){					
						$(this).parents('.frm_checkbox_label').addClass('selected');
						$(this).prop("checked",true);
					})
				}
			}
			e.preventDefault();
		});


		// 下拉
		$('body').on('click','.dropdown_menu',function(e){
			var $this = nowdropdown = $(this);
			if($this.hasClass('open')){
				$this.removeClass('open');
			}else{
				$this.addClass('open');
				$('.dropdown_menu').not(this).each(function(){
					if( $(this).hasClass('open') ) $(this).removeClass('open');
				})
			}
		});

		// 切换
		$('body').on('click','.change_item .frm_radio_label',function(){
			var item = $(this).parents('.change_item').attr('item');
			$('.'+item).hide();
			var show = $(this).attr('show');
			$('.'+show).show();		
		});
		
		// select
		$('.select_item').click(function(){
			var id = $(this).attr('id');
			var text = $(this).text();
			var parent = $(this).parents('.dropdown_menu');
			parent.find('input').val(id);
			parent.find('.jsBtLabel').text(text);
		})

	};


	function webAlert(str){
		if($('#webalert').length > 0){
			$('#webalert .alertcontent').text(str);
			alertShow();
		}else{
			var div = '<div id="webalert" style="position:fixed;z-index:99999;top:20px;left:50%;width:500px;height:35px;margin-left:-250px;background:red;">\
					<div class="alertcontent" style="font-size: 16px;color:#fff;text-align:center;line-height: 35px;">'+str+'</div></div>';
			$('body').append(div);
			alertShow();
		}
	};

	function alertShow() {
		$('#webalert').show('fast',function(){
			setTimeout(function(){$('#webalert').hide();},3000);
		})
	};

	//http请求
	 function Http(type,datatype,op,data,successCall,isloading,beforeCall,comCall){
		$.ajax({
			type: type,
			url: ajaxUrl(op),
			dataType: datatype,
			data : data,
			beforeSend:function(){
				if(beforeCall) beforeCall();
				if(isloading) loading();
			},
			success: function(data){
				if(successCall) successCall(data);
			},
			complete:function(){
				if(comCall) comCall();
				if(isloading) loaded();
			},				
			error: function(xhr, type){
				console.log(xhr);
			}
		});	
	};

	function ajaxUrl(op){
		return window.sysinfo.siteroot + 'web/index.php?c=site&a=entry&op='+op+'&do=ajax&m=zofui_taskself';
	};
	
	function loading(){
		var html = 
			'<div id="loading" class="loading">'+
			'<div class="load_mask"></div>'+
			'<div class="modal-loading">'+
			'	<div class="modal-loading-in" style="text-align:center;">'+
			'		<img style="width:48px; height:48px;display:inline-block;" src="../attachment/images/global/loading.gif"><p>处理中</p>'+
			'	</div>'+
			'</div>'+
			'</div>';
		$(document.body).append(html);
	};
	
	function loaded(){
		$('.loading').remove();
	};

	function serializeJson( elem ){
		
		var serializeArr = elem.serializeArray();
		var postdata={};
		for (i in serializeArr) {
			postdata[ serializeArr[i].name ] = serializeArr[i].value;
		}
		return postdata;
	}

	function arrval( elem ){
        var self = elem;
        var result = [];
        if(self.length > 0){
            self.each(function(i, o){
           	 	result.push($(o).val());
			});
        }
        return result;
	}

	/***地区选择***/
	function areaSelect (element) {
		//if(typeof areaSelect.areaobj === 'object') return areaSelect.areaobj;
		this.elem = element;
		//areaSelect.areaobj = this;
		this.bindEvent();
	};

	areaSelect.prototype.init = function(){
		var self = this;
		if ($('.delivery_privince .js_dd_list').html() == ''){		
			var province = '';
			for(var i=0;i<citydata.length;i++){
				province += '<dd>'
								+'<a href="javascript:;" class="jsLevel father_menu jsLevel1" data-name="'+citydata[i].text+'">'
									+'<span class="item_name">'+citydata[i].text+'</span>'
								+'</a>'
							+'</dd>';
			}
			$('.delivery_privince .js_dd_list').html(province);
		}
		
		var provincevalue = self.elem.find('.area_province_input').val();

		if(provincevalue != ''){
			var cityvalue = self.elem.find('.area_city_input').val();
			var countyvalue = self.elem.find('.area_county_input').val();
			
			$('.jsLevel1').each(function(){	
				if($(this).attr('data-name') == provincevalue) $(this).addClass('selected');
			});
			
			self.appendCityStr(provincevalue,cityvalue); //城市

			countyvalue = countyvalue.replace(/,$/,'');
			countyarray = countyvalue.split(","); //数组
			
			self.appendCountyStr(provincevalue,cityvalue,countyarray); //区县
		}else{
			$('.delivery_city .js_dd_list').empty();
			$('.delivery_county .js_dd_list').empty();
		}


	};

	areaSelect.prototype.bindEvent = function(){
		var self = this;
		//点击一级展开二级
		$('body').off('click','.delivery_box .jsLevel1').on('click','.delivery_box .jsLevel1',function(){
			var province = $(this).attr('data-name');
			$('.delivery_privince .selected').removeClass('selected');
			$(this).addClass('selected');
			self.appendCityStr(province,'');
		});		
		//点击二级展开三级
		$('body').off('click','.delivery_box .jsLevel2').on('click','.delivery_box .jsLevel2',function(){
			var province = $(this).attr('data-province'),
				city = $(this).attr('data-name');
			$('.delivery_city .selected').removeClass('selected');
			$(this).addClass('selected');
			self.appendCountyStr(province,city,[]);
		});	

		//选择区县
		$('body').off('click','.delivery_box .jsLevel3').on('click','.delivery_box .jsLevel3',function(){

			if($(this).hasClass('disabled')) return false;
			/*var province = $(this).attr('data-province'),
				city = $(this).attr('data-city'),
				county = $(this).attr('data-name');*/

			if($(this).hasClass('selected')){
				$(this).removeClass('selected');
			}else{
				$('.delivery_box .jsLevel3').removeClass('selected');
				$(this).addClass('selected');
			}
		});	
	
		//确定选择
		$('#confirm_indelivery').off('click').on('click',function(){

			var province = $('.delivery_privince .selected').attr('data-name'),
				city = $('.delivery_city .selected').attr('data-name'),
				county = '';
			$('.delivery_county .selected').each(function(){
				county += $(this).attr('data-name') + ',';
			});

			self.elem.find('.area_province_input').val(province).next().val(city).next().val(county);
			self.elem.find('.delivery_item_province').text(province).next().text(city).next().text(county);
			
			self.hideDeliveryTable();
		});
		
		
		//关闭操作框
		$('.delivery_close').off('click').on('click',function(){
			self.hideDeliveryTable();
		});			
		
	};
	
	//插入城市数据
	areaSelect.prototype.appendCityStr = function (province,city){
		
		for(var i=0;i<citydata.length;i++){
			if(citydata[i].text == province){
				var citystr = '';
				for(var j=0;j<citydata[i].children.length;j++){
					var selectstr = '';
					if(city == citydata[i].children[j].text) selectstr = 'selected';
					citystr += '<dd>'
									+'<a href="javascript:;" class="jsLevel father_menu jsLevel2 '+selectstr+'" data-province="'+province+'" data-name="'+citydata[i].children[j].text+'">'
										+'<span class="item_name">'+citydata[i].children[j].text+'</span>'
									+'</a>'
								+'</dd>';
					
				}
				$('.delivery_city .js_dd_list').html(citystr);
				$('.delivery_county .js_dd_list').empty();
				return false;
			}
		}		
		
	};
	
	areaSelect.prototype.appendCountyStr = function (province,city,countyarray){
		//已经选择了的地区，
		var selectedcountystr = '';
		$('.area_county_input').each(function(){
			selectedcountystr += $(this).val() + ',';
		});
		selectedcountystr = selectedcountystr.replace(/,$/,'');
		selectedcountystr = selectedcountystr.split(","); //数组
		
		for(var i=0;i<citydata.length;i++){
			if(citydata[i].text == province){
				for(var j=0;j<citydata[i].children.length;j++){			
					if(citydata[i].children[j].text == city){
						var county = '';
						for(var k=0;k<citydata[i].children[j].children.length;k++){
							if($.inArray(citydata[i].children[j].children[k], countyarray) >= 0  || $.inArray(citydata[i].children[j].children[k], selectedcountystr) < 0 ){
								var selectedstr = '';
								if( $.inArray(citydata[i].children[j].children[k], countyarray) >= 0 ) selectedstr = 'selected';
								
								county += '<dd>'
												+'<a href="javascript:;" class="jsLevel father_menu jsLevel3 '+selectedstr+'" data-province="'+province+'" data-city="'+city+'" data-name="'+citydata[i].children[j].children[k]+'">'
													+'<span class="item_name">'+citydata[i].children[j].children[k]+'</span>'
												+'</a>'
											+'</dd>';								
							}
						}
						
						$('.delivery_county .js_dd_list').html(county);
						return false;						
					}
				}
			}
		}
	};
	
	areaSelect.prototype.hideDeliveryTable = function (){
		$('.delivery_box .selected').removeClass('selected');
		$('.ui-draggable').hide();
		self = null;
	};
