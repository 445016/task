$(function () {
  'use strict';
		
	var config = {
		page : {
			op : settings.do+settings.op,
			page : 1,
			type : settings.pagetype,
			pid : settings.pid,
			isinit : false,
			isdeal : false,
			loading : false,
			isend : false,
			icantake : settings.icantake == 1 ? 1 : 0,
		},
		message : {
			op : 'getmeesage',
			page : 1,
			from : settings.do,
			tasktype : settings.tasktype,
			type : settings.taskid,
			isinit : false,
			isdeal : false,
			loading : false,
			isend : false
		},
		conlist : {
			op : 'getconlist',
			page : 1,
			type : settings.continueid,
			taskid : settings.taskid,
		},
		model : function(title,text,totext,call){
		    $.modal({
		      	title: title,
		      	text: text,
		      	buttons: [{text: '关闭',onClick: function() {}},{text: totext,onClick: function() {if( call ) call();}}]
			});
		}
	}
	
	var getpage = {
		init : function(){
			if( !config.page.isinit ){
				common.getPage(config.page,function(data){
					if(data.status == 'ok'){
						config.page.isinit = true;
					}else{
						getpage.nodata();
					}
				});
			}
		},
		reset : function(){
			config.page.page = 1;
			config.page.isend = false;
			$('.list_container').empty();
			common.getPage(config.page,function(data){
				if( data.status != 'ok' ){
					getpage.nodata();
				}
			});
		},
		nodata : function(){
			$('.no_data_notice,.no_data').remove();
			$('.list_container').prepend('<div class="no_data"><p></p><span>没有找到数据</span></div>');
		},
		initdata : function(){
			config.page = {
				op : settings.do+settings.op,
				page : 1,
				type : settings.pagetype,
				pid : settings.pid,
				isinit : false,
				isdeal : false,
				loading : false,
				isend : false,
			};
		}
	}

	// ///////发布
	var pub = {
		params : {},
		init: {
			init : function(){
				common.uploadImageByWxJs('.uploader_input_images',9,'images');
				common.uploadImageByWxJs('.uploader_input_head',1,'head','replace');
				common.uploadImageByWxJs('.uploader_input_step',9,'stepimg');


				if( settings.do == 'pubuse' ){
					common.uploadImageByWxJs('.uploader_input_goodimg',1,'goodimg');
					common.uploadImageByWxJs('.uploader_input_useimages',1,'gpic');
				}
				if( settings.do == 'pub' ) pub.fn.scity();

				// 认证
				if( settings.isauth > 0 ){
					if( settings.verifystatus == 0 ) {
						common._alert('需要提交认证资料审核后才能发任务',function(){
							location.href = common.createUrl('set');
						})
					}else if( settings.verifystatus == 1 ){
						common._alert('您的认证资料还没审核，暂不能发任务');
					}
				}

				$('.ic_item').each(function(){
					if( $(this).find('input').prop('checked') ){
						$(this).addClass('pri-bg');
					}else{
						$(this).removeClass('pri-bg');	
					}
				})

			}
		},
		events:{
			'#showrule' :{
				click : function(){
					$.popup('.popup-pubrule');
				}
			},
			'.ic_item' : {
				click : function(){
					var _this = $(this);
					if( _this.find('input').prop('checked') ){
						_this.removeClass('pri-bg');
						_this.find('input').prop('checked',false);
					}else{
						_this.addClass('pri-bg');
						_this.find('input').prop('checked',true);
					}
				}
			},
			'#into_link' : { //选择规格
				click : function(){
					$.popup('.popup-link');
					//$.closeModal('.popup-about');
				}
			},
			'#add_hide' : {
				click : function(){
					var nowcontent = $('.hide_box_in').html();
					$('.pub_hide_content').html( nowcontent );
					$.popup('.popup-hide');
				}
			},
			'#confirm_hide' : {
				click : function(){
					var content = $('.pub_hide_content').html();
					if( content == '' ){
						common.alert('还没填写内容');
						return false;
					}
					$('.hide_box').html( '<div class="hide_box_in">' + content + '</div>' );

					$.closeModal('.popup-hide');
				}
			},
			'#remove_all' : {
				click : function(){
					common.confirm('提示','确定要清空任务内容吗？',function(){
						$('.pub_task_content').empty();
					});
				}
			},
			'.toadd_click' : {
				click : function(){
					location.href = common.createUrl('deposit','in');
				}
			},
			'#pub_setka' : {
				click : function(){
					$.popup('.popup-ka');
					$('.ka_show_list').show();
					pub.fn.countMoney();
				}
			},
			'#pub_noka' : {
				click : function(){
					$('.ka_show_list').hide();
				}
			},
			'#pub_form' : {
				click : function(){
					$.popup('.popup-taskform');
				}
			},
			'#no_form' : {
				click : function(){
					$('#isformlimit').hide().find('.isform_view').html('');
				}
			},
			'.showform_view' : {
				click : function(){
					var id = $(this).attr('fid');
					common.Http('post','json',common.createUrl('ajaxdeal','viewform'),{fid:id},function(data){
						if(data.status == 200){
							$('.popup-viewform .form_list').html(data.obj.str);
							$.popup('.popup-viewform');
						}else{
							common._alert(data.res);
						}
					});
				}
			},
			'.viewform_close' : {
				click : function(){
					$('.popup-viewform').removeClass('modal-in').hide();
				}
			},
			'.showform_select' : {
				click : function(){
					var id = $(this).attr('fid');
					var name = $(this).attr('name');
					var str = '<span>'+name+'</span><input name="formid" type="hidden" value="'+id+'">';
					$('#isformlimit').show().find('#isform_view').html(str);
					$.closeModal('.popup-taskform');
				}
			},
			'#pub_continue' : {
				click : function(){
					$.popup('.popup-continue');
					pub.fn.initContinue();
				}
			},
			'#no_continue' : {
				click : function(){
					$('#continue_show').text( '设置连续发布' );
					$('#continueday').text('x0');
					$('#continuemoney').text('0');
				}
			},
			'#confirm_continue' : {
				click : function(){
					var day = $('input[name="continueday"]').val();
					var ewai = $('input[name="continuemoney"]').val();
					if( day == '' || !common.verify('number','int',day) ){
						common.alert('连续天数必须填正整数'); return false;
					}
					if( ewai != '' && !common.verify('number','money',ewai) ){
						common.alert('额外奖励必须填数字'); return false;
					}					
					$.closeModal('.popup-continue');
					pub.fn.initContinue();
				}
			},
			'.popup_ka_add' : {
				click : function(){
					var num = $('.popup_ka_item').length;
					if( num >= 9 ) {
						common._alert('最多添加9个关键词');return false;
					}
					var str = '<div class="mb05 form_group item_cell_box popup_ka_item">'
								+'<div class="form_title">'
									+'关键词'
								+'</div>'
								+'<div class="form_right item_cell_flex item_cell_box">'
									+'<li class="item_cell_flex">'
										+'<input type="text" name="ka[]" class="form_input form_into" value="" placeholder="请输入关键词">'
									+'</li>'
									+'<li class="form_per delete_ka">删除</li>'
								+'</div>'
							+'</div>';
					$('.popup_ka_list').append(str);
				}
			},
			'.delete_ka' : {
				click : function(){
					var _this = $(this);
					common.confirm('提示','确定要删除此关键词吗？',function(){
						_this.parents('.popup_ka_item').remove();
					});
				}
			},
			'#confirm_ka' : { // 确定关键字
				click : function(){
					var iscan = true;
					var arr = [];
					console.log(231)
					if( $('input[name="goodid"]').val() == '' && settings.do == 'pub' ){
						common.alert('请填写宝贝ID'); return false;
					}
					if( $('input[name="ka[]"]').length <= 0 ){
						common.alert('您还没添加关键词'); return false;
					}					
					$('input[name="ka[]"]').each(function(){
						var value = $(this).val();
						if( value == '' ){
							common.alert('请填写完整的关键词');
							$(this).focus();
							iscan = false;
							return false;
						}
						arr.push( value );
					});
					if( iscan ){
						if( arr.length > 0 ){
							var showstr = '';
							for (var i = 0; i < arr.length; i++) {
								showstr += '<span><input name="katrue[]" type="hidden" value="'+arr[i]+'">'+arr[i]+'</span>';
							}
							$('.ka_show_list').html(showstr).show();
						}else{
							$('.ka_show_list').hide();
						}
						if( settings.do == 'pub' ){
							$.closeModal('.popup-ka');
						}else if( settings.do == 'pubuse' ){
							$.closeModal('.popup-usekey');
						}
						
					}
				}
			},
			'.pub_task_content' : { //获取焦点 清理默认提示
				click : function(e){
					$(this).find('.form_tips').remove();
				}
			},
			'#confirm_link' : { //增加链接确定
				click : function(){
					var linkname = $('input[name=linkname]').val();
					var linkurl = $('input[name=linkurl]').val();
					
					var Expression=/^http(s)?:\/\/+/;
					var objExp=new RegExp(Expression);
					if(linkname == '' || linkurl == ''){
						common.alert('请输入名称和链接');return false;
					}
					if(objExp.test(linkurl) == false){
						common.alert('超链接需以http或https格式');return false;
					}

					var inputname = encodeURI(linkname );
					var inputurl = encodeURI(linkurl );
					if( pub.params.iseditlink ){
						pub.params.linkitem.find('font').text(inputname);
						pub.params.linkitem.find('input[name="linktext[]"]').val(inputname);
						pub.params.linkitem.find('input[name="linkurl[]"]').val(inputurl);
					}else{
						
						if( pub.params.linktype == 'step' ){
							var str = '<span class="link_item"><font>'+linkname+'</font><input type="hidden" name="stepurl[]" value="'+inputurl+'"><input type="hidden" name="steptext[]" value="'+inputname+'"></span>';
							pub.params.linktypeelem.prev().append(str);
						}else{
							var str = '<span class="link_item"><font>'+linkname+'</font><input type="hidden" name="linkurl[]" value="'+inputurl+'"><input type="hidden" name="linktext[]" value="'+inputname+'"></span>';
							$('.link_box').append(str);	
						}
					}				
					
					$.closeModal('.popup-link');
					$('.pub_task_content .form_tips').remove();
					$('input[name=linkname]').val('');
					$('input[name=linkurl]').val('');
					pub.params.iseditlink = false;
				}
			},
			'.pub_task_content a' : {
				click : function(){
					pub.params.aclass = $(this);
					pub.params.isurledit = true; //是编辑
					var linkname = $(this).text();
					var linkurl = $(this).attr('href');
					$('input[name=linkname]').val(linkname);
					$('input[name=linkurl]').val(linkurl);
					$.popup('.popup-link');
					$('.pub_task_content').blur();
					return false;
				}
			},
			'.link_item' : {
				click : function(){
					var _this = $(this);
					/*common.confirm('提示','要删除此超链接吗？',function(){
						_this.remove();
					},function(){
						console.log(123)
					})*/
					$.modal({
			            text: '请选择操作方式',
			            title: '提示',
			            buttons: [
			                {text: '编辑', onClick: function(){
			                	pub.params.iseditlink = true;
			                	pub.params.linkitem = _this;
								$('input[name=linkname]').val( _this.find('input[name="linktext[]"]').val() );
								$('input[name=linkurl]').val( _this.find('input[name="linkurl[]"]').val() );
			                	$.popup('.popup-link');
			                }},
			                {text: '删除', bold: true, onClick: function(){
			                	common.confirm('提示','要删除此超链接吗？',function(){
									_this.remove();
								})
			                }}
			            ]
			        });


				}
			},
			'input[name="num"],input[name="money"]' : {
				'input propertychange' : function(){
					pub.fn.countMoney();
				}
			},
			'input[name=istop],input[name=iska],input[name=continue]' : {
				change : function(){
					pub.fn.countMoney();
				}
			},
			'#pub_btn' : {
				click : function(){

					if( settings.rdrule*1 == 1 && $('input[name="readit"]:checked').val() != 1 ){
						common.alert('未同意协议不可发布任务');return false;
					}

					var postdata = pub.fn.returnData();
					var money = pub.fn.countMoney();
					if( postdata.title == '' ){
						common.alert('请输入任务标题');return false;
					}
					if( postdata.content == '' ){
						//common.alert('请输入任务内容');return false;
					}
					if(  !common.verify('number','int',postdata.num) ){
						common.alert('任务总量必须是正整数');return false;
					}
					if(  postdata.money*1 < settings.leastcommoney*1 ){
						common.alert('任务赏金至少'+settings.leastcommoney);return false;
					}	
					if( postdata.money == '' || !common.verify('number','money',postdata.money) ){
						common.alert('任务赏金必须大于0');return false;
					}
					if( ( !common.verify('number','int',postdata.replytime) && postdata.replytime != 0 ) || postdata.replytime > settings.endtime/2 ){
						common.alert('等待时间必须是整数,且小于'+settings.endtime/2);return false;
					}					
					if(  !common.verify('number','int',postdata.limitnum) ){
						common.alert('限制回复必须是正整数');return false;
					}
					if( postdata.isform == 1 && ( typeof postdata.formid == 'undefined' || postdata.formid == '' ) ){
						common.alert('还没选择回复模板');return false;
					}
					if( settings.isaddress == 1 && ( typeof postdata.address == 'undefined' || postdata.address == '' ) ){
						common.alert('还没填写任务地址');return false;
					}
					if( settings.anwleast*1 > 0 && settings.readprice*1 < settings.anwleast*1 ){
						common.alert('查看答案价格至少设置'+settings.anwleast);return false;
					}

					common.confirm('提示','确定要发布任务吗？需扣除您'+money.total+settings.cname,function(){
						if( pub.params.ispaying ) return false;
						pub.params.ispaying = true;
						common.Http('post','json',common.createUrl('ajaxdeal','addtask'),postdata,function(data){
							if(data.status == 200){
								common._alert(data.res,function(){
									location.href = common.createUrl('task','op',{id:data.obj.taskid});
								});
							}else if( data.status == 210 ){
							    $.modal({
							      title:  '提示',
							      text: data.res,
							      buttons: [
							        {
							          text: '关闭',
							          onClick: function() {
							            
							          }
							        },
							        {
							          text: '去充值',
							          onClick: function() {
							            location.href = data.obj.url;
							          }
							        },
							      ]
							    });
							}else if( data.status == 220 ){
								common._alert(data.res,function(){
									location.href = data.obj.url;
								});
							}else if( data.status == 230 ){
								common._alert(data.res,function(){
									location.href = common.createUrl('set');
								});
							}else{
								common._alert(data.res);
							}
							pub.params.ispaying = false;
						});		
					});
				}
			},
			'#get_tao_good' : { // 试用任务
				click : function(){
					var url = $('input[name=taourl]').val();
					if( url == '' ){
						common.alert('请输入链接');
						return false;
					}
					common.Http('post','json',common.createUrl('ajaxdeal','gettao'),{url:url},function(data){
						if( data.status == 200 ){
							//$('input[name=paymoney]').val(data.obj.nowprice);
							//$('input[name=taourl]').val(data.obj.taourl);
							//$('.pub_showgood_box img').attr('src',data.obj.pic);
							//$('.pub_showgood_box .pub_show_goodtitle').text(data.obj.title);
							//$('.pub_showgood_box').show();
							if( url.indexOf('http') > 0 ){
								$('input[name=taourl]').val(data.obj.taourl);
							}
							
							$('input[name=gtitle]').val( data.obj.title );
							$('.gpic .upload_images_views').html('<li class="fl upload_image_item"><img src="'+data.obj.pic+'"><input value="'+data.obj.pic+'" type="hidden" name="gpic[]"></li>');

							$('#pubuse_notice').hide().next().show();
							pub.params.taogood = data.obj;
						}else{
							common._alert( data.res );
							//$('input[name=taourl]').val('');
						}
					});
					
				}
			},
			'input[name=prizetype]' : {
				change : function(){
					var type = $('input[name=prizetype]:checked').val();
					if( type == 0){
						$('.pubuse_prizegood').hide();
					}else{
						$('.pubuse_prizegood').show();
					}
				}
			}, // 
			'input[name=usenum],input[name=usemoney]' : {
				'input propertychange' : function(){
					pub.fn.countUseMoney();
				}
			},
			'input[name=usetop],input[name=findtype]' : {
				change : function(){
					pub.fn.countUseMoney();
				}
			},
			'#use_usetask_good' : {
				click : function(){
					pub.params.taogood
					$('input[name=prizetitle]').val( pub.params.taogood.title );
					var appstr = '<li class="fl upload_image_item"><img src="'+pub.params.taogood.pic+'"><input value="'+pub.params.taogood.pic+'" type="hidden" name="goodimg[]"></li>';
					$('.pubuse_prizegood .upload_images_views').html( appstr );
				}
			},
			'.keyfind' : {
				click : function(){
					$.popup('.popup-usekey');
					$('.ka_show_list').show();
				}
			},
			'.notkeyfind' : {
				click : function(){
					$('.ka_show_list').hide();
				}
			},
			'.ka_show_list span' : { // 删除关键字
				click : function(){
					var ele = $(this);
					common.confirm('提示','确定要删除此关键词吗？',function(){
						ele.remove();
					})
				}
			},
			'#pubuse_btn' : {
				click : function(){
					var postdata = pub.fn.returnUseData();
					var money = pub.fn.countUseMoney();
					
					if( postdata.gtitle == '' ){
						common.alert('请输入商品标题');return false;
					}
					if( postdata.gpic == '' || typeof postdata.gpic == 'undefined' ){
						common.alert('请设置商品图片');return false;
					}					
					if( postdata.taourl == '' ){
						common.alert('请输入商品链接');return false;
					}
					if( postdata.title == '' ){
						common.alert('请输入任务标题');return false;
					}
					if(  !common.verify('number','int',postdata.num) ){
						common.alert('试用总量必须是正整数');return false;
					}
					if(  postdata.money*1 < settings.leastusemoney*1 ){
						common.alert('返还赏金不能小于'+settings.leastusemoney);return false;
					}	
					if(  postdata.paymoney*1 <= 0 ){
						common.alert('拍下金额不能小于0');return false;
					}
					if( postdata.findtype == 1 && postdata.findkey.length <= 0 ){
						common.alert('还没设置搜索商品的关键词');return false;
					}					
					common.confirm('提示','确定要发布任务吗？需扣除您'+money.total+settings.cname,function(){
						if( pub.params.ispaying ) return false;
						pub.params.ispaying = true;
						common.Http('post','json',common.createUrl('ajaxdeal','addusetask'),postdata,function(data){
							if(data.status == 200){
								common._alert(data.res,function(){
									location.href = common.createUrl('task','op',{id:data.obj.taskid});
								});
							}else if( data.status == 210 ){
							    $.modal({
							      title:  '提示',
							      text: data.res,
							      buttons: [
							        {
							          text: '关闭',
							          onClick: function() {}
							        },
							        {
							          text: '去充值',
							          onClick: function() {
							            location.href = data.obj.url;
							          }
							        },
							      ]
							    });
							}else if( data.status == 220 ){
								common._alert(data.res,function(){
									location.href = data.obj.url;
								});
							}else{
								common._alert(data.res);
							}
							pub.params.ispaying = false;
						});		
					});
				}
			},
			'#arealimita,#arealimitb,#arealimitc' : { // 区域限制
				click : function(){
					$('#isarealimit').show();
					$("#city-picker").val('');
				}
			},
			'#notarealimit' : { // 区域限制
				click : function(){
					$('#isarealimit').hide();
					$("#city-picker").val('');
				}
			},
			'input[name="ishide"]' : { // 区域限制
				change : function(){
					if( $(this).prop('checked') ){
						$('#readprice').css({'display':'-webkit-box'});
					}else{
						$('#readprice').css({'display':'none'});
					}
				}
			},
			'#step1' : { 
				click : function(){
					$('.taskstep').hide();
					$('#steptemp').hide().find('.steptemp_view').html('');
				}
			},
			'#step2' : {
				click : function(){
					$.popup('.popup-steptemp');
					$('.taskstep').hide();
				}
			},
			'#step3' : {
				click : function(){
					$('.taskstep').show();
					$('#steptemp').hide().find('.steptemp_view').html('');
				}
			},
			'.addastep' : {
				click : function(){
					var t = (new Date()).getTime();
					var html = '<div class="taskstep_item">'
						+'<a href="jacascript:;" class="delstep">删除</a>'
						+'<div class="item_cell_box step_itemin">'
							+'<div class="step_name">步骤内容</div>'
							+'<div class="step_input item_cell_flex step_list">'
								+'<textarea name="stepname" class="" placeholder="填写步骤详细内容"></textarea>'
							+'</div>'
						+'</div>'
						+'<div class="item_cell_box step_itemin">'
							+'<div class="step_name">跳转链接</div>'
							+'<div class="item_cell_flex step_list">'
							+'</div>'
							+'<a class="step_btn addtourl" href="jacascript:;">添加</a>'
						+'</div>'
						+'<div class="item_cell_box step_itemin">'
							+'<div class="step_name">一键复制</div>'
							+'<div class="item_cell_flex step_list">'
							+'</div>'
							+'<a href="jacascript:;" class="addcopy">添加</a>'
						+'</div>'
						+'<div class="item_cell_box step_itemin">'
							+'<div class="step_name">提示图片</div>'
							+'<div class="item_cell_flex step_list">'
								+'<div class="upload_images_views miniup">'
								+'</div>'
								+'<div class="uploader_input miniup up'+t+'"></div>	'
							+'</div>'
						+'</div>'
					+'</div>';
					$(this).before(html);
					common.uploadImageByWxJs('.up'+t,9,'stepimg');
				}
			},
			'.delstep' : {
				click : function(){
					var _this = $(this);
					common.confirm('提示','确定删除此步骤吗？',function(){
						_this.parent().remove();
					})
				}
			},
			'.addtourl' : {
				click : function(){
					pub.params.linktype = 'step';
					pub.params.linktypeelem = $(this);
					$.popup('.popup-link');
				}
			},
			'.addcopy' : {
				click : function(){
					var _this = $(this);
					$.prompt('请填入需要复制的内容', function (value) {
						if( !value || value == '' ) {
							common.alert('请输入内容');
							return false;
						}
						var str = '<span class="copy_item"><font>'+value+'</font><input type="hidden" name="copyitem[]" value="'+value+'"></span>';
						_this.prev().append(str);
					});
				}
			},
			'.copy_item' : {
				click : function(){
					$(this).remove();
				}
			},
			'.steptemp_view' : {
				click : function(){
					var id = $(this).attr('fid');
					common.Http('post','json',common.createUrl('ajaxdeal','viewstep'),{fid:id},function(data){
						if(data.status == 200){
							$('.popup-viewform .form_list').html(data.obj.str);
							$.popup('.popup-viewform');
						}else{
							common._alert(data.res);
						}
					});
				}
			},
			'.steptemp_select' : {
				click : function(){
					var id = $(this).attr('fid');
					var name = $(this).attr('name');
					var str = '<span>'+name+'</span><input name="stepid" type="hidden" value="'+id+'">';
					$('#steptemp').show().find('#steptemp_view').html(str);
					$.closeModal('.popup-steptemp');
				}
			},


		},
		fn:{
			countMoney : function(){
				var data = pub.fn.returnData();
				var money = data.num*data.money;
				var first = money*settings.commonserver/100;
				var server = Math.max.apply(Math,[first,settings.commonserverleast]);
				var ka = 0;
				if( data.iska == 1 ){
					ka = settings.kaserver*1;
				}
				var topserver = 0;
				if( data.istop == 1 ){
					topserver = settings.topserver*1;
				}
				var ccc = 1;
				var ewai = 0;
				var continueday = 0;
				if( data.continue == 1 ){
					ccc = data.continueday <= 0 ? 1 : data.continueday+1;
					ewai = data.continuemoney*data.num;
					continueday = data.continueday;
				}
				
				var total = ( (money+server+ka+topserver)*ccc + ewai ).toFixed(2);
				
				$('#total_money').text('('+total+')');
				$('#taskmoney').text( money.toFixed(2) );
				$('#kamoney').text(ka);
				$('#servermoney').text( server.toFixed(2) );
				$('#servertop').text(topserver);
				$('#continueday').text('x'+continueday);
				$('#continuemoney').text( ewai.toFixed(2) );
				return {money:money,server:server,ka:ka,total:total};
			},
			returnData : function(){
				var data = {
					title : $('input[name="title"]').val(),
					hidecontent : $('.hide_box_in').html(),
					content : $('.pub_task_content').html(),
					images : $('input[name="images[]"]').arrval(),
					iska : $('input[name=iska]:checked').val(),
					num : $('input[name=num]').val()*1,
					money : $('input[name=money]').val()*1,
					replytime : $('input[name=replytime]').val()*1,
					limitnum : $('input[name=limitnum]').val()*1,
					sex : $('input[name=sex]:checked').val(),
					ishide : $('input[name=ishide]:checked').val(),
					continue : $('input[name=continue]:checked').val(),
					isimage : $('input[name=isimage]:checked').val(),
					istop : $('input[name=istop]:checked').val(),
					continueday : $('input[name=continueday]').val()*1,
					continuemoney : $('input[name=continuemoney]').val()*1,
					kagood : $('input[name=goodid]').val(),
					kakey : $('input[name="katrue[]"]').arrval(),
					sortid : settings.sortid,
					linkname : $('input[name="linkurl[]"]').arrval(),
					linkurl : $('input[name="linktext[]"]').arrval(),
					ic : $('input[name="ic[]"]:checked').arrval(),
					isarealimit : $('input[name=isarealimit]:checked').val(),
					area : $('input[name=area]').val(),
					address : $('input[name=address]').val(),
					isform : $('input[name=isform]:checked').val(),
					formid : $('input[name=formid]').val(),
					levellim : $('input[name=levellim]:checked').val(),
					readprice : $('input[name=readprice]').val(),
					mark : $('input[name=mark]').val(),
					head : $('input[name="head[]"]').val(),
					gid : $('input[name=gid]').val(),
					isstep : $('input[name=isstep]:checked').val(),
					stepid : $('input[name=stepid]').val(),
					//step : JSON.stringify({}),
				}
				var step = [];
				$('.taskstep_item').each(function(){
					var stepname = $(this).find('textarea[name="stepname"]').val();

					var url = [];
					$(this).find('.link_item').each(function(){					
						url.push({text:decodeURI($(this).find('input[name="steptext[]"]').val()),url:$(this).find('input[name="stepurl[]"]').val()});
					})

					var copy = [];
					$(this).find('.copy_item').each(function(){
						copy.push($(this).find('input[name="copyitem[]"]').val());
					})

					var img = [];
					$(this).find('input[name="stepimg[]"]').each(function(){
						img.push($(this).val());
					})
					step.push( {name:stepname,url:url,copy:copy,img:img} );
				});
				if( step.length > 0 ){
					data.step = JSON.stringify(step);
				}		

				if( typeof data.continueday == 'undefined' || data.continueday == '' ) data.continueday = 0;
				if( typeof data.continuemoney == 'undefined' || data.continuemoney == '' ) data.continuemoney = 0;
				return data;
			},
			initContinue : function(){
				var data = pub.fn.returnData();
				$('#continue_show').html('连续发布<font class="font_ff5f27">'+data.continueday+'</font>天');
				$('#continueday').text('x'+data.continueday);
				$('#continuemoney').text( ( data.continuemoney*data.num ).toFixed(2) );
				pub.fn.countMoney();
			},
			returnUseData : function(){
				var data = {
					link : $('input[name="taourl"]').val(),
					content : $('.pub_task_content').html(),
					images : $('input[name="images[]"]').arrval(),
					title : $('input[name="title"]').val(),
					gtitle : $('input[name="gtitle"]').val(),
					gpic : $('input[name="gpic[]"]').val(),
					prizetype : $('input[name=prizetype]:checked').val(),
					prizetitle : $('input[name="prizetitle"]').val(),
					prizeimg : $('input[name="goodimg[]"]').arrval(),
					num : $('input[name=usenum]').val()*1,
					money : $('input[name=usemoney]').val()*1,
					paymoney : $('input[name=paymoney]').val()*1,
					sex : $('input[name=sex]:checked').val(),
					istop : $('input[name=usetop]:checked').val(),
					findtype : $('input[name=findtype]:checked').val(),
					findkey : $('input[name="katrue[]"]').arrval(),
					isform : $('input[name=isform]:checked').val(),
					address : $('input[name="address"]').val(),
				}
				//if( data.conten.indexOf('') )
				if( data.content.indexOf('请在此输入任务说明') >= 0 ){
					data.content = '';
				}
				return data;
			},
			countUseMoney : function(){
				var data = pub.fn.returnUseData();
				var money = data.num*data.money;
				var first = money*settings.usetaskserver/100;

				var server = Math.max.apply(Math,[first,settings.leastuseserver]);
				var topserver = 0;
				if( data.istop == 1 ){
					topserver = settings.usetopserver*1;
				}
				var findserver = 0;
				if( data.findtype == 1 && settings.findkeyserver > 0 ){
					findserver = settings.findkeyserver;
				}
				var total = ( money+server+topserver+findserver ).toFixed(2);
				
				$('#total_money').text('('+total+')');
				$('#taskmoney').text( money.toFixed(2) );
				$('#findmoney').text( findserver );
				$('#servermoney').text( server.toFixed(2) );
				$('#servertop').text(topserver);
				return {money:money,server:server,total:total};
			},
			scity : function(){
				
				$("#city-picker").cityPicker({
				    toolbarTemplate: '<header class="bar bar-nav">\
				    <button class="button button-link pull-right close-picker">确定</button>\
				    <h1 class="title">选择能接任务的区域</h1>\
				    </header>',
				    onOpen : function(){
				    	var type = $('input[name=isarealimit]:checked').val();
				    	if( type  == 1 ){
				    		$('.col-district').show();

				    	}else if( type == 2 ){
				    		$('.col-district').hide();
				    	}else if( type == 3 ){
				    		$('.col-district,.col-city').hide();
				    	}
				    },
				    formatValue : function(a,b){
				    	var type = $('input[name=isarealimit]:checked').val();
				    	if( type  == 1 ){
				    		var value = b[0] +','+b[1]+','+b[2];
				    	}else if( type == 2 ){
				    		var value = b[0] +','+b[1];
				    	}else if( type == 3 ){
				    		var value = b[0];
				    	}
				    	return value;
				    }
				});
			}
		}
		
	};		
	
	// ///////发布担保
	var pubtb = {
		params : {},
		init: {
			init : function(){
				common.uploadImageByWxJs('#uploader_input',9,'images');
				common.uploadImageByWxJs('#uploader_hide',9,'hideimages');

				pub.fn.scity();
			}
		},
		events:{
			'#showrule' :{
				click : function(){
					$.popup('.popup-pubrule');
				}
			},
			'#into_link' : { //选择规格
				click : function(){
					$.popup('.popup-link');
					//$.closeModal('.popup-about');
				}
			},
			'#add_hide' : {
				click : function(){
					var nowcontent = $('.hide_box_in').html();
					$('.pub_hide_content').html( nowcontent );
					$.popup('.popup-hide');
				}
			},
			'#confirm_hide' : {
				click : function(){
					var content = $('.pub_hide_content').html();
					if( content == '' ){
						common.alert('还没填写内容');
						return false;
					}
					$('.hide_box').html( '<div class="hide_box_in">' + content + '</div>' );

					$.closeModal('.popup-hide');
				}
			},
			'#edit_stepname' : {
				click : function(){
					$.popup('.popup-stepname');
				}
			},
			'#confirm_editstep' : {
				click : function(){
					var postdata = {
						step : $('input[name=steptempname]').arrval(),
					};
					common.Http('post','json',common.createUrl('ajaxdeal','savestep'),postdata,function(data){
						if( data.status == 200 ) {
							$('input[name=steptempname]').each(function(i){
								i++;
								var v = $(this).val();
								var _this = $('.weui_check_label[step="'+i+'"]');
								_this.find('input[name="stepname"]').val( $(this).val() );
								if( v == '' ) {
									//_this.find('.form_tips').text( i+'.忽略此步骤' );
									_this.find('input[name="step"][value="'+i+'"]').prop('checked',false);
								}else{
									_this.find('.form_tips').text( i+'.'+$(this).val() );
								}
								
							});
							$.closeModal('.popup-stepname');
						}else{
							common.alert(data.res);
						}
					});
				}		
			},
			'.toadd_click' : {
				click : function(){
					location.href = common.createUrl('deposit','in');
				}
			},
			'#remove_all' : {
				click : function(){
					common.confirm('提示','确定要清空任务内容吗？',function(){
						$('.pub_task_content').empty();
					});
				}
			},
			'#pub_setka' : {
				click : function(){
					$.popup('.popup-ka');
					$('.ka_show_list').show();
					pubtb.fn.countMoney();
				}
			},
			'#pub_noka' : {
				click : function(){
					$('.ka_show_list').hide();
				}
			},
			'.popup_ka_add' : {
				click : function(){
					var num = $('.popup_ka_item').length;
					if( num >= 9 ) {
						common._alert('最多添加9个关键词');return false;
					}
					var str = '<div class="mb05 form_group item_cell_box popup_ka_item">'
								+'<div class="form_title">'
									+'关键词'
								+'</div>'
								+'<div class="form_right item_cell_flex item_cell_box">'
									+'<li class="item_cell_flex">'
										+'<input type="text" name="ka[]" class="form_input form_into" value="" placeholder="请输入关键词">'
									+'</li>'
									+'<li class="form_per delete_ka">删除</li>'
								+'</div>'
							+'</div>';
					$('.popup_ka_list').append(str);
				}
			},
			'.delete_ka' : {
				click : function(){
					var _this = $(this);
					common.confirm('提示','确定要删除此关键词吗？',function(){
						_this.parents('.popup_ka_item').remove();
					});
				}
			},
			'#confirm_ka' : { // 确定关键字
				click : function(){
					var iscan = true;
					var arr = [];
					var goodid = $('input[name="goodid"]').val();
					if( goodid == '' ){
						common.alert('请填写宝贝ID'); return false;
					}
					
					var R = /^([0-9,]+)$/;
					if( !R.test( goodid ) ){
						common.alert('宝贝ID只能填数字和,'); return false;
					}
					if( $('input[name="ka[]"]').length <= 0 ){
						common.alert('您还没添加关键词'); return false;
					}					
					$('input[name="ka[]"]').each(function(){
						var value = $(this).val();
						if( value == '' ){
							common.alert('请填写完整的关键词');
							$(this).focus();
							iscan = false;
							return false;
						}
						arr.push( value );
					});
					if( iscan ){
						if( arr.length > 0 ){
							var showstr = '';
							for (var i = 0; i < arr.length; i++) {
								showstr += '<span><input name="katrue[]" type="hidden" value="'+arr[i]+'">'+arr[i]+'</span>';
							}
							$('.ka_show_list').html(showstr).show();
						}else{
							$('.ka_show_list').hide();
						}
						$.closeModal('.popup-ka');
					}
				}
			},
			'.pub_task_content' : { //获取焦点 清理默认提示
				click : function(e){
					$(this).find('.form_tips').remove();
				}
			},
			'#confirm_link' : { //增加链接确定
				click : function(){
					var linkname = $('input[name=linkname]').val();
					var linkurl = $('input[name=linkurl]').val();
					
					var Expression=/^http(s)?:\/\/+/;
					var objExp=new RegExp(Expression);
					if(linkname == '' || linkurl == ''){
						common.alert('请输入名称和链接');return false;
					}
					if(objExp.test(linkurl) == false){
						common.alert('超链接需以http或https格式');return false;
					}

					var inputname = encodeURI(linkname );
					var inputurl = encodeURI(linkurl );
					var str = '<span class="link_item"><font>'+linkname+'</font><input type="hidden" name="linkurl[]" value="'+inputurl+'"><input type="hidden" name="linktext[]" value="'+inputname+'"></span>';
					$('.link_box').append(str);					
					
					$.closeModal('.popup-link');
					$('.pub_task_content .form_tips').remove();
					$('input[name=linkname]').val('');
					$('input[name=linkurl]').val('');
				}
			},
			'.link_item' : {
				click : function(){
					var _this = $(this);
					common.confirm('提示','要删除此超链接吗？',function(){
						_this.remove();
					})
				}
			},
			'input[name=istop],input[name=iska]' : {
				change : function(){
					pubtb.fn.countMoney();
				}
			},
			'#pub_isstop1' : {
				click : function(){
					//$.popup('.popup-toptime');
					var tbtopserver = settings.tbtopserver*100/100;
					var text = '';
					if( tbtopserver > 0 ) text = '('+tbtopserver+settings.cname+'/小时)'
					$('#toptime_notice').text(text);
					$('input[name=toptime]').val( 0 );
				}
			},			
			'#pub_isstop2' : {
				click : function(){
					//$.popup('.popup-toptime');
					var maxtime = settings.tasktime*100/100;
					$.prompt('请填入置顶时间,最长'+maxtime+'小时', function (value) {
						if( !common.verify('number','int',value) ) {
							if( $('input[name=toptime]').val() <= 0 ){
								$('#pub_isstop1').prop('checked','checked');
							}
							common.alert('请填正整数');
							return false;
						}
						if( maxtime < value*1 ){
							if( $('input[name=toptime]').val() <= 0 ){
								$('#pub_isstop1').prop('checked','checked');
							}
							common.alert('最长设置'+maxtime+'小时');return false;
						}
						$('#toptime_notice').text('(置顶'+value+'小时)');
						$('input[name=toptime]').val( value );
						pubtb.fn.countMoney();
					},function(){
						if( $('input[name=toptime]').val() <= 0 ){
							$('#pub_isstop1').prop('checked','checked');
						}
					});
				}
			},
			'#pub_btn' : {
				click : function(){

					if( settings.rdrule*1 == 1 && $('input[name="readit"]:checked').val() != 1 ){
						common.alert('未同意协议不可发布任务');return false;
					}

					var postdata = pubtb.fn.returnData();
					var money = pubtb.fn.countMoney();
					
					if( postdata.title == '' ){
						common.alert('请输入任务标题');return false;
					}
					if( postdata.content == '' ){
						common.alert('请输入任务内容');return false;
					}
					if(  !common.verify('number','int',postdata.num) ){
						common.alert('任务总量必须是正整数');return false;
					}
					if(  postdata.money*1 < settings.tbminmoney*1 ){
						common.alert('任务赏金至少'+settings.tbminmoney+settings.cname);return false;
					}
					if( postdata.money == '' || !common.verify('number','money',postdata.money) ){
						common.alert('任务赏金必须大于0');return false;
					}
					if( postdata.step.length < 1 ) {
						common.alert('至少要选择一个任务步骤');return false;
					}
					if( settings.maxtbmoney*1 > 0 && postdata.tbmoney > settings.maxtbmoney*1 ){
						common.alert('担保金额最大填'+settings.maxtbmoney);return false;
					}
					if( settings.mintbmoney*1 > 0 && postdata.tbmoney < settings.mintbmoney*1 ){
						common.alert('担保金额最小填'+settings.mintbmoney);return false;
					}

					common.confirm('提示','确定要发布任务吗？需扣除您'+money.total+settings.cname,function(){
						if( pubtb.params.ispaying ) return false;
						pubtb.params.ispaying = true;
						common.Http('post','json',common.createUrl('ajaxdeal','addtbtask'),postdata,function(data){
							if(data.status == 200){
								common._alert(data.res,function(){
									location.href = common.createUrl('tbtask','op',{id:data.obj.taskid});
								});
							}else if( data.status == 210 ){
							    $.modal({
							      title:  '提示',
							      text: data.res,
							      buttons: [
							        {
							          text: '关闭',
							          onClick: function() {}
							        },
							        {
							          text: '去充值',
							          onClick: function() {
							            location.href = data.obj.url;
							          }
							        },
							      ]
							    });
							}else if( data.status == 220 ){
								common._alert(data.res,function(){
									location.href = data.obj.url;
								});
							}else if( data.status == 230 ){
								common._alert(data.res,function(){
									location.href = common.createUrl('set');
								});
							}else{
								common._alert(data.res);
							}
							pubtb.params.ispaying = false;
						});
					});
				}
			},
			'#arealimita,#arealimitb,#arealimitc' : { // 区域限制
				click : function(){
					$('#isarealimit').show();
					$("#city-picker").val('');
				}
			},
			'#notarealimit' : { // 区域限制
				click : function(){
					$('#isarealimit').hide();
					$("#city-picker").val('');
				}
			},
			'.tbform' : {
				click : function(){
					if( !pubtb.params.formlist ){
						common.Http('post','json',common.createUrl('pagelist','tbform'),{},function(data){
							$('.tbform_list').html(data.data);
							pubtb.params.formlist = data.obj;
						})
					}
					$.popup('.popup-tbform');
				}
			},
			'.stbform' : {
				click : function(){
					var id = $(this).attr('fid');
					for (var i = 0; i < pubtb.params.formlist.length; i++) {
						if( pubtb.params.formlist[i].id == id ){
							var item = pubtb.params.formlist[i];
							
							$('input[name="title"]').val(item.title);
							$('input[name="num"]').val(item.num);
							$('input[name="money"]').val(item.money);
							$('input[name="tbmoney"]').val(item.tbmoney);
							$('.pub_task_content').html(item.content);

							if( item.step.step1 != '' ){
								$('label[step="1"]').find('input[name="step"]').prop('checked',true);
								$('label[step="1"]').find('.form_tips').text('1.'+item.step.step1);
								$('label[step="1"]').find('input[name="stepname"]').val(item.step.step1);
							}
							if( item.step.step2 != '' ){
								$('label[step="2"]').find('input[name="step"]').prop('checked',true);
								$('label[step="2"]').find('.form_tips').text('2.'+item.step.step2);
								$('label[step="2"]').find('input[name="stepname"]').val(item.step.step2);
							}
							if( item.step.step3 != '' ){
								$('label[step="3"]').find('input[name="step"]').prop('checked',true);
								$('label[step="3"]').find('.form_tips').text('3.'+item.step.step3);
								$('label[step="3"]').find('input[name="stepname"]').val(item.step.step3);
							}
							if( item.step.step4 != '' ){
								$('label[step="4"]').find('input[name="step"]').prop('checked',true);
								$('label[step="4"]').find('.form_tips').text('4.'+item.step.step4);
								$('label[step="4"]').find('input[name="stepname"]').val(item.step.step4);
							}
							if( item.step.step5 != '' ){
								$('label[step="5"]').find('input[name="step"]').prop('checked',true);
								$('label[step="5"]').find('.form_tips').text('5.'+item.step.step5);
								$('label[step="5"]').find('input[name="stepname"]').val(item.step.step5);
							}

							$.closeModal('.popup-tbform');
							return false;
						}
					}
				}
			}

		},
		fn:{
			countMoney : function(){
				var data = pubtb.fn.returnData();

				var money = settings.tbpubserver*1;
				var ka = 0;
				if( data.iska == 1 ){
					ka = settings.tbkaserver*1;
				}
				var topserver = 0;
				if( data.istop == 1 ){
					topserver = settings.tbtopserver*data.toptime;
				}
				
				var total = ( ( money+ka+topserver )*1 ).toFixed(2);
				
				$('#total_money').text('('+total+settings.cname+')');
				$('#kamoney').text(ka);
				$('#servertop').text(topserver);
				return {money:money,top:topserver,ka:ka,total:total};
			},
			returnData : function(){
				var data = {
					gid : $('input[name="gid"]').val(),
					title : $('input[name="title"]').val(),
					content : $('.pub_task_content').html(),
					hidecontent : $('.hide_box_in').html(),
					images : $('input[name="images[]"]').arrval(),
					hideimages : $('input[name="hideimages[]"]').arrval(),
					iska : $('input[name=iska]:checked').val(),
					num : $('input[name=num]').val()*1,
					money : $('input[name=money]').val()*1,
					tbmoney : $('input[name=tbmoney]').val()*1,
					limitnum : $('input[name=limitnum]').val()*1,
					sex : $('input[name=sex]:checked').val(),
					istop : $('input[name=istop]:checked').val(),
					toptime : $('input[name=toptime]').val(),
					kagood : $('input[name=goodid]').val(),
					kakey : $('input[name="katrue[]"]').arrval(),
					linkname : $('input[name="linkurl[]"]').arrval(),
					linkurl : $('input[name="linktext[]"]').arrval(),
					skiptype : $('input[name=skiptype]:checked').val(),
					step : $('input[name="step"]:checked').arrval(),
					stepname : $('input[name="stepname"]').arrval(),
					isarealimit : $('input[name=isarealimit]:checked').val(),
					area : $('input[name=area]').val(),
					tkl : $('input[name=tkl]').val(),
					address : $('input[name=address]').val(),
				}
				return data;
			},
		}
		
	};

	var tbtask = {
		params : {

		},
		init: {
			initfun : function(){
				common.goToTop('content'); //回到顶部
				common.updateTime();
				common.updateTime2(function(){
					location.href = common.createUrl('tbtask','task',{'id':settings.taskid});
				});
				common.uploadImageByWxJs('#uploader_reply',9,'images','append','.reply_imgbox .upload_image_item');
				common.uploadImageByWxJs('#uploader_complain',9,'complainimages','append','.complain_imgbox .upload_image_item');
				common.uploadImageByWxJs('#uploader_taketbtask',9,'takeimages','append','.taketbtask_imgbox .upload_image_item');
				common.viewImages();
				task.fn.copyit();
				tbtask.fn.copyita();
				common.lazyLoad('.content','');
			}
		},
		events:{
			'.guy_contact_wx' : {
				click : function(){
					guy.fn.showdia( $('#contact_me') );
				}
			},
			'#task_message' : {
				click : function(){
					if( !config.message.isinit ){
						task.fn.getmessage();
					}
					$.popup('.popup-message');
				}
			},
			'#more_message' : {
				click : function(){
					task.fn.getmessage();
				}
			},
			'.pub_show_tao' : {
				click : function(){
					var tao = $(this).attr('tao');
					$('#tao_box').show();
					$('.copybox').text(tao);
					$('#copy_it').attr('data-clipboard-text',tao);
				}
			},
			'.close_tao' : {
				click : function(){
					$(this).parents('.tao_box').hide();

					$('#copy_it').css({'background':'#ed414a'}).text('一键复制');
				}
			},			
			'.reply_message' : { // 回复留言
				click : function(){
					var elem = $(this);
					task.fn.replymessage( elem );
				}
			},
			'#task_complain' : {
				click : function(){
					$.popup('.popup-complain');
				}
			},
			'.task_reply_top' : {
				click : function(){
					$.popup('.popup-filter-tbtasklist');
				}
			},
			'.filter_replay' : { // 筛选回复
				click : function(){
					var type = $(this).attr('type');
					var name = $(this).attr('name');
					config.page.type = type;
					getpage.reset();
					$.closeModal('.popup-filter-tbtasklist');
					$('.task_reply_topl').text( name );
				}
			},
			'#tbtake_btn' : { // 抢
				click : function(){
					if( settings.deposit*1 < settings.tbtakeneedposit*1 ){
						config.model('提示','账户留存'+settings.tbtakeneedposit+'保证金才能接任务','去充值',function(){
							location.href = common.createUrl('deposit','in');
						});
						return false;
					}

					$.popup('.popup-taketbtask');
				}
			},
			'#confirmtaketbtask' : {
				click : function(){
					var postdata = {
						taskid : settings.taskid,
						images : $('input[name="takeimages[]"]').arrval(),
						content : $('textarea[name="taketbcontent"]').val(),
					}
					common.confirm('提示','提交后无法修改，确定提交接任务吗？',function(){
						if( task.params.ispaying ) return false;
						task.params.ispaying = true;
						common.Http('post','json',common.createUrl('ajaxdeal','taketbtask'),postdata,function(data){
							if(data.status == 200){
								common._alert(data.res,function(){
									window.location.reload();
								});
							}else if( data.status == 220 ){
								common._alert(data.res,function(){
									location.href = data.obj.url;
								});
							}else if( data.status == 230 ){
								common._alert(data.res,function(){
									location.href = common.createUrl('set');
								});
							}else{
								common._alert(data.res);
							}
							task.params.ispaying = false;
						});
					})
				}
			},
			'#reply_confirm' : { // 确认回复
				click : function(){
					var postdata = {
						taskid : settings.taskid,
						images : $('input[name="images[]"]').arrval(),
						content : $('textarea[name="content"]').val(),
					}
					common.confirm('提示','确定要回复吗？',function(){
						if( task.params.ispaying ) return false;
						task.params.ispaying = true;
						common.Http('post','json',common.createUrl('ajaxdeal','reply'),postdata,function(data){
							if(data.status == 200){
								common._alert(data.res,function(){
									location.href = common.createUrl('task','task',{'id':settings.taskid});
								});
							}else if( data.status == 220 ){
								common._alert(data.res,function(){
									location.href = common.createUrl('regist','op');
								});
							}else{
								common._alert(data.res);
							}
							task.params.ispaying = false;
						});
					})
				}
			},
			'#reply_btn' : { // 回复
				click : function(){
					$.popup('.popup-reply');
				}
			},
			
			'input[name=isall]' : {
				change : function(){
					if( $(this).prop('checked') ){
						$('input[name="reply[]"]').prop('checked',true);
					}else{
						$('input[name="reply[]"]').prop('checked',false);
					}
				}
			},
			'input[name="reply[]"]' : {
				change : function(){
					var all = $('input[name="reply[]"]').length;
					var checked = $('input[name="reply[]"]:checked').length;
					if( all == checked ){
						$('input[name=isall]').prop('checked',true);
					}else{
						$('input[name=isall]').prop('checked',false);
					}
				}
			},
			'.pass' : {
				click : function(){
					var _this = $(this);
					var reid = _this.attr('reid');
					var server = settings.tbgivetaskserver*settings.money/100;
					var total = server + settings.money*1 + settings.tbmoney*1;
					tbtask.fn.deal( {idlist : [reid]},'将扣除您'+total+'余额(服务费'+server+'，保证金'+settings.tbmoney+'，赏金'+settings.money+')，确定通过吗？' ,'pass',function(data){
						for (var i = 0; i < data.obj.suc.length; i++) {
							var id = data.obj.suc[i];
							var parent = $('.pass[reid="'+id+'"]').parents('.task_replay_bottom');
							parent.find('.puber_deal_btn,.puber_deal_check').remove();
							parent.append('<span class="task_replay_status font_green">任务进行中</span>');
						}
					});
				}
			},
			'.nopass' : {
				click : function(){
					var _this = $(this);
					var reid = _this.attr('reid');
					$.prompt('请填入不通过原因', function (value) {
						var postdata = {
							idlist : [reid],
							reason : value,
						}
						tbtask.fn.deal( postdata,'确定设为不通过吗？' ,'nopass',function(){
							var parent = _this.parent();
							parent.find('.puber_deal_btn,.puber_deal_check').remove();
							parent.append('<span class="task_replay_status">未通过审核</span>');
						});
					});
				}
			},
			'#passall' : {
				click : function(){
					var all = $('input[name="reply[]"]:checked').arrval();
					if( all.length <= 0 ){
						common.alert('您还没选择');
						return false;
					}
					tbtask.fn.deal( {idlist : all},'确定通过选择的吗？' ,'pass',function(data){
						for (var i = 0; i < data.obj.suc.length; i++) {
							var id = data.obj.suc[i];
							var parent = $('.pass[reid="'+id+'"]').parents('.task_replay_bottom');
							parent.find('.puber_deal_btn,.puber_deal_check').remove();
							parent.append('<span class="task_replay_status font_green">任务进行中</span>');
						}
					});
				}
			},
			'#nopassall' : {
				click : function(){
					var all = $('input[name="reply[]"]:checked').arrval();
					if( all.length <= 0 ){
						common.alert('您还没选择');
						return false;
					}
					$.prompt('请填入未通过原因', function (value) {
						var postdata = {
							idlist : all,
							reason : value,
						}
						tbtask.fn.deal( postdata,'确定将选择的设为未通过吗？' ,'nopass',function(){
							$('input[name="reply[]"]:checked').each(function(){
								var parent = $(this).parents('.task_replay_bottom');
								parent.find('.puber_deal_btn,.puber_deal_check').remove();
								parent.append('<span class="task_replay_status">未通过审核</span>');
							})
						});
					});
				}
			},
			'.failtbtask' : {
				click : function(){
					var _this = $(this);
					var takedid = $(this).attr('reid');
					$.prompt('请填入失败原因', function (value) {
						var postdata = {
							takedid : takedid,
							reason : value,
						}
						tbtask.fn.deal( postdata,'确定提交设为失败吗？','tbtaskfail',function(){
							var parent = _this.parent();
							parent.prev().remove();
							parent.find('.puber_deal_btn,.puber_deal_check').remove();
							parent.append('<span class="task_replay_status">待对方确认</span>');
						});
					});
				}
			},
			'.comtbtask' : {
				click : function(){
					var _this = $(this);
					var postdata = {
						takedid : $(this).attr('reid'),
					}
					tbtask.fn.deal( postdata,'设为完成后将为对方发放赏金和担保金，确定设为完成吗？','tbtaskcom',function(){
						var parent = _this.parents('.task_replay_bottom');
						parent.find('.puber_deal_btn,.puber_deal_check').remove();
						parent.append('<span class="task_replay_status font_ff5f27">已完成</span>');
					});
				}
			},
			'#confirm_fail' : { // 确认失败
				click : function(){
					var postdata = {
						takedid : settings.takedid,
					}
					tbtask.fn.deal( postdata,'提交后无法修改，赏金将退回给雇主。确定任务失败吗？','tbconfirmfail');
				}
			},
			'#tbtask_conplain' : { // 申诉
				click : function(){
					$('#confirmtaketbtask').attr('id','confirmtbcomplain');
					$.popup('.popup-taketbtask');
				}
			},
			'#confirmtbcomplain' : { // 申诉
				click : function(){
					var postdata = {
						takedid : settings.takedid,
						images : $('input[name="takeimages[]"]').arrval(),
						content : $('textarea[name="taketbcontent"]').val(),
					}
					tbtask.fn.deal( postdata,'提交后无法修改，确定申诉吗？','tbcomplain');
				}
			},
			'.sub_cert' : {
				click : function(){
					tbtask.params.certtakedid = $(this).attr('reid');
					tbtask.params.certtype = $(this).attr('type');
					$('#confirmtaketbtask').attr('id','subtbcomplain');
					$.popup('.popup-taketbtask');
				}
			},
			'#subtbcomplain' : { // 上传凭证
				click : function(){
					var postdata = {
						takedid : tbtask.params.certtakedid,
						type : tbtask.params.certtype,
						images : $('input[name="takeimages[]"]').arrval(),
						content : $('textarea[name="taketbcontent"]').val(),
					}
					tbtask.fn.deal( postdata,'确定提交吗？','subtbcert',function(){
						$('#subtbcomplain').attr('id','confirmtaketbtask');
						$('textarea[name="taketbcontent"]').val('');
						$('.upload_images_views').empty();
						$.closeModal('.popup-taketbtask');
					});
				}
			},
			'.deal_tbtaked' : { 
				click : function(){
					tbtask.params.dealstep = $(this).attr('type');
					$('#confirmtaketbtask').attr('id','confirmdealstep');
					$.popup('.popup-taketbtask');
				}
			},
			'#confirmdealstep' : {
				click : function(){
					var postdata = {
						takedid : settings.takedid,
						dealstep : tbtask.params.dealstep,
						images : $('input[name="takeimages[]"]').arrval(),
						content : $('textarea[name="taketbcontent"]').val(),
					}
					tbtask.fn.deal( postdata,'确定提交吗？' ,'tbtaskstep');
				}
			},
			'.remind' : {
				click : function(){
					var _this = $(this);
					var takedid = _this.attr('reid');
					$.prompt('请填入提醒内容', function (value) {
						var postdata = {
							content : value,
							type : 1,
							takedid : takedid,
						}
						tbtask.fn.deal( postdata,'确定提醒对方吗？' ,'tbremind',function(data){
							var parent = _this.parent();
							parent.find('.puber_deal_btn').remove();
							parent.append('<span class="task_replay_status font_green">任务进行中</span>');
						});
					});
				}
			},
			'#dealtask' : {
				click : function(){
					$.popup('.popup-filter-dealtask');
				}
			},
			'#count_task' : {
				click : function(){
					var postdata = {
						taskid : settings.taskid,
					};
					common.confirm('提示','结算后未处理的将自动处理，确定要将此任务结算吗？',function(){
						if( task.params.ispaying ) return false;
						task.params.ispaying = true;
						common.Http('post','json',common.createUrl('ajaxdeal','counttbtask'),postdata,function(data){
							if(data.status == 200){
								common._alert(data.res,function(){
									window.location.reload();
								});
							}else{
								common._alert(data.res);
							}
							task.params.ispaying = false;
						});
					})
				}
			},
			'#addtask' : { // 追加
				click : function(){
			      $.prompt('请填入追加的数量', function (value) {
			      	var postdata = {
			      		taskid : settings.taskid,
			      		value : value,
			      	}
			      	if( !common.verify('number','int',value) || value <= 0 ){
			      		common.alert('请输入正整数值');
			      		return false;
			      	}
					common.confirm('提示','确定要追加吗？',function(){
						if( task.params.ispaying ) return false;
						task.params.ispaying = true;
						common.Http('post','json',common.createUrl('ajaxdeal','subaddtbtask'),postdata,function(data){
							if(data.status == 200){
								common._alert(data.res,function(){
									window.location.reload();
								});
							}else{
								common._alert(data.res);
							}
							task.params.ispaying = false;
						});
					})
			      });
				}
			},
			'#addtoptime' : { // 追加置顶
				click : function(){
					if( settings.cantoptime*1 <= 0 ) {
				      	common.alert('任务不能再置顶'); return false;
					}
					var lasttimestr = '';
					if( settings.lasttoptime*1 > 0 ) {
						lasttimestr = '，当前任务置顶还剩'+settings.lasttoptime+'小时';
					}
			      	$.prompt('请填入置顶的时间'+lasttimestr+'，还可增加置顶'+settings.cantoptime+'小时', function (value) {
				      	var postdata = {
				      		taskid : settings.taskid,
				      		value : value,
				      	}
				      	if( !common.verify('number','int',value) || value <= 0 ){
				      		common.alert('请输入正整数值');
				      		return false;
				      	}
				      	if( value*1 > settings.cantoptime*1 ){
				      		common.alert('最大填入'+settings.cantoptime);
				      		return false;
				      	}				      	
				      	var server = value*settings.tbtopserver;
						common.confirm('提示','将扣除您'+server+settings.cname+',确定要置顶吗？',function(){
							if( task.params.ispaying ) return false;
							task.params.ispaying = true;
							common.Http('post','json',common.createUrl('ajaxdeal','subaddtasktoptime'),postdata,function(data){
								if(data.status == 200){
									common._alert(data.res,function(){
										window.location.href=window.location.href+"&temptime="+10000*Math.random();
									});
								}else if( data.status == 210 ){
									config.model('提示',data.res,'去充值',function(){
										window.location.href = data.obj.url;
									});
								}else{
									common._alert(data.res);
								}
								task.params.ispaying = false;
							});
						})
			      	});
				}
			},
			'#confirm_message' : { // 留言
				click : function(){
					task.fn.confirmmessage();
				}
			},
			'#complain_confirm' : { // 投诉
				click : function(){
					var postdata = {
						taskid : settings.taskid,
						content : $('textarea[name=commplain]').val(),
						images : $('input[name="complainimages[]"]').arrval(),
					};
					if( postdata.content == '' ){
						common.alert('请填写投诉内容');
						return false;
					}
					task.fn.deal( postdata,'确定要提交投诉吗？' ,'complain' );
				}
			},
			'#task_verify' : {
				click : function(){
					$.popup('.popup-filter-verify');
				}
			},
			'.filter_verify' : { // 审核任务
				click : function(){
					var type = $(this).attr('type');
					if( type == 1 ){
				      	var postdata = {
				      		taskid : settings.taskid,
				      		type : 1,
				      	}
						tbtask.fn.deal( postdata,'确定审核通过吗？' ,'verifytbtask' );
					}else if( type == 2 ){
				      $.prompt('请填入不通过原因', function (value) {
				      	var postdata = {
				      		taskid : settings.taskid,
				      		type : 2,
				      		value : value,
				      	}
				      	tbtask.fn.deal( postdata,'确定要不通过审核吗？' ,'verifytbtask' );
				      });
					}else if( type == 3 ){
				      	var postdata = {
				      		taskid : settings.taskid,
				      		type : 1,
				      	}
						tbtask.fn.deal( postdata,'确定下架任务吗？' ,'downanduptbtask' );
					}else if( type == 4 ){
				      	var postdata = {
				      		taskid : settings.taskid,
				      		type : 2,
				      	}
						tbtask.fn.deal( postdata,'确定上架任务吗？' ,'downanduptbtask' );
					}
				}
			},
			'#task_continue' : {
				click : function(){
					$.popup('.popup-continue');
					if( config.conlist.isdeal ) return false;
					config.conlist.isdeal = true;
					common.Http('post','json',common.createUrl('pagelist','getconlist'),config.conlist,function(data){
						$('.popup_continue_list').append(data.data);
					});
				}
			},
			'#task_autoadd' : {
				click : function(){
					$.popup('.popup-autoaddlist');
					if( task.params.isgetautoadd ) return false;
					task.params.isgetautoadd = true;
					common.Http('post','json',common.createUrl('pagelist','getautoadd'),{page:1,taskid:settings.taskid},function(data){
						$('.popup_autoadd_list').append(data.data);
					});
				}
			},
			'.end_show_box' : {
				click : function(){
					var _this = $(this);
					var height = _this.siblings('.end_hide_content').height();
					_this.hide();
					_this.parent().height(0).animate({'height':height},800,'ease',function(){
						_this.siblings('.end_hide_box').show();
					})
					
				}
			},
			'.end_hide_box' : {
				click : function(){
					var _this = $(this);
					_this.hide();
					_this.parent().animate({'height':'3rem'},800,'ease',function(){
						_this.siblings('.end_show_box').show();
					});
				}
			},
		},
		fn : {
			deal : function(postdata,notice,op,call){
				common.confirm('提示',notice,function(){
					if( task.params.ispaying ) return false;
					task.params.ispaying = true;
					common.Http('post','json',common.createUrl('ajaxdeal',op),postdata,function(data){
						if(data.status == 200){
							common._alert(data.res,function(){
								if( call ) {
									call(data);
								}else{
									window.location.reload();
								}
							});
						}else if( data.status == 210 || data.status == 220 ){
							config.model('提示',data.res,'去充值',function(){
								location.href = data.obj.url;
							});
							if( call ) call(data);
						}else{
							common._alert(data.res);
						}
						task.params.ispaying = false;
					});
				})
			},
			copyita : function(){
				var clipboard = new Clipboard('.copy_it,.tbtask_tklbtn');
				clipboard.on('success', function(e) {
				    common._alert('已复制成功，打开手机淘宝');

				});
				clipboard.on('error', function(e) {
			      	common._alert('复制失败,请手动复制');
			      	$(e.trigger).hide().next().show();
				});
			},			
			copyit : function(down){
				var clipboard = new Clipboard('#copy_it');

				clipboard.on('success', function(e) {
				    //console.info('Action:', e.action);
				    //console.info('Text:', e.text);
				    //console.info('Trigger:', e.trigger);
				    e.trigger.innerHTML = down ? "已复制成功" : "已复制成功，打开手机淘宝";
			        e.trigger.innerHTML="已复制成功，打开手机淘宝";
			        e.trigger.style.backgroundColor="#9ED29E";
				    e.clearSelection();
				});

				clipboard.on('error', function(e) {
			      	e.trigger.innerHTML="复制失败,请手动复制";
			      	e.trigger.style.backgroundColor="#666";
				    console.error('Action:', e.action);
				    console.error('Trigger:', e.trigger);
				});

				var isapple = task.fn.isApple();
				if(!isapple){
					$('#android_code').show().prev().hide();
					//$('.popwtitle').html('长按框内口令&gt;全选&gt;复制');
				}else{
					task.fn.select('copybox');
				}
			},
			isApple : function(){
				var ua = navigator.userAgent.toLowerCase();
				if (ua.match(/iphone/i) == "iphone" || ua.match(/ipad/i) == "ipad"){
					return true;
				}
				return false;
			},
			select : function(classname){
				//return false;

		        document.addEventListener("selectionchange",
		        function(e) {
		            if (window.getSelection().anchorNode.parentNode.id == 'iphone_code' && document.getElementById('iphone_code').innerText != window.getSelection()) {
		              var key = document.getElementById('iphone_code');
		              window.getSelection().selectAllChildren(key);
		            }
		        },false);

				
			},
		}
	};	

	var taskmessage = {
		events : {
			'#task_message' : {
				click : function(){
					if( !config.message.isinit ){
						taskmessage.fn.getmessage();
					}
					$.popup('.popup-message');
				}
			},
			'#more_message' : {
				click : function(){
					taskmessage.fn.getmessage();
				}
			},
			'#confirm_message' : { // 留言
				click : function(){
					var postdata = {
						message : $('textarea[name=taskmessage]').val(),
						taskid : settings.taskid,
					};
					if( postdata.message == '' ){
						common.alert('请填写留言');
						return false;
					}
					common.confirm('提示','确定要留言吗？',function(){
						if( task.params.ispaying ) return false;
						task.params.ispaying = true;
						common.Http('post','json',common.createUrl('ajaxdeal','pubmessage'),postdata,function(data){
							if(data.status == 200){
								common._alert(data.res,function(){
									$('.popup_message_list').prepend(data.obj.str);
									$('textarea[name=taskmessage]').val('');
									$.closeModal('.popup-message');
									$('#task_message .font_ff5f27').text( $('#task_message .font_ff5f27').text()*1 + 1 );
								});
							}else{
								common._alert(data.res);
							}
							task.params.ispaying = false;
						});
					});
				}
			},
			'.reply_message' : {
				click : function(){
					var elem = $(this);
					var mid = elem.attr('mid');
					$.prompt('请填入回复内容', function (value) {
						var postdata = {
							mid : mid,
							value : value,
						}
						if( task.params.ispaying ) return false;
						task.params.ispaying = true;
						common.Http('post','json',common.createUrl('ajaxdeal','replymessage'),postdata,function(data){
							if(data.status == 200){
								elem.parent().next().append(data.obj.str);
							}else{
								common._alert(data.res);
							}
							task.params.ispaying = false;
						});
					});
				}
			},
			'#task_verify' : { // 审核任务
				click : function(){
					$.popup('.popup-filter-verify');
				}
			},
			'.filter_verify' : { // 审核任务
				click : function(){
					var type = $(this).attr('type');
					if( type == 1 ){
				      	var postdata = {
				      		taskid : settings.taskid,
				      		type : 1,
				      	}
						task.fn.deal( postdata,'确定审核通过吗？' ,'verifytask' );
					}else{
				      $.prompt('请填入不通过原因', function (value) {
				      	var postdata = {
				      		taskid : settings.taskid,
				      		type : 2,
				      		value : value,
				      	}
				      	task.fn.deal( postdata,'确定要不通过审核吗？' ,'verifytask' );
				      });
					}
				}
			},
		},
		fn : {
			getmessage : function(){
				if( config.message.isdeal ) return false;
				config.message.isdeal = true;
				common.Http('post','json',common.createUrl('pagelist','getmeesage'),config.message,function(data){
					if(data.status == 'ok'){
						config.message.page ++;
						$('.popup_message_list').append(data.data);
						$('.more_message').show();
					}else{
						$('.more_message').hide();
					}
					config.message.isinit = true;
					config.message.isdeal = false;
				});
			},
		}
	}

	var task = {
		params : {

		},
		init: {
			initfun : function(){
				//getpage.initdata();
				
				common.goToTop('content'); //回到顶部
				//common.chageTitle(settings.title);
				common.updateTime();
				common.updateTime2(function(){
					window.location.reload();
				});
				common.updateTime3(function(){
					window.location.reload();
				});
				common.uploadImageByWxJs('.uploader_reply',9,'images');
				common.uploadImageByWxJs('.uploader_complain',9,'complainimages');
				common.uploadImageByWxJs('.uploader_addcontent',9,'addimages');
				common.viewImages();
				common.init(taskmessage);

				$('.subform_img').each(function(){
					var id = $(this).attr('key');
					var num = $(this).attr('num') * 1;
					num = num > 0 ? num : 9;
					common.uploadImageByWxJs('.'+id,num,id);
				});
				

				var clipboard = new Clipboard('.copythis');
				clipboard.on('success', function(e) {
					common.alert('复制成功');
				});
				clipboard.on('error', function(e) {
			      	common.alert('复制失败');
				});	

			}
		},
		events:{
			'.tourl' : {
				click : function(){
					var _this = $(this);
					var url = _this.attr('url');				

					$.modal({
			            text: '请选择操作',
			            //title: '请选择操作类型',
			            buttons: [
			                {text: '<text class="copythis" data-clipboard-text="'+url+'" >复制链接</text>', onClick: function(){
								
			                }},
			                {text: '跳转链接', bold: true, onClick: function(){
			                	location.href = url;
			                }}
			            ]
			        })
				}
			},
			'.guy_contact_wx' : {
				click : function(){
					guy.fn.showdia( $('#contact_me') );
				}
			},
			'#task_complain' : {
				click : function(){
					$.popup('.popup-complain');
				}
			},
			'.task_reply_top' : {
				click : function(){
					$.popup('.popup-filter-replay');
				}
			},
			'.filter_replay' : { // 筛选回复
				click : function(){
					var type = $(this).attr('type');
					var name = $(this).attr('name');
					config.page.type = type;
					getpage.reset();
					$.closeModal('.popup-filter-replay');
					$('.task_reply_topl').text( name );
				}
			},
			'#take_btn' : { // 抢
				click : function(){
					var isread = common.cookie.get('takerule');
					if( !isread && settings.taketip == 1 ){
						$.popup('.popup-filter-takerule');
					}else{
						task.fn.takecomtask();
					}
				}
			},
			'#confirmtake' : {
				click : function(){
					if( $('input[name="notips"]:checked').val() == 1 ){
						common.cookie.set('takerule',1,45899999);
					}
					task.fn.takecomtask();
				}
			},
			'#reply_btn' : { // 回复
				click : function(){
					$.popup('.popup-reply');
				}
			},
			'#reply_confirm' : { // 确认回复
				click : function(){
					var postdata = {
						taskid : settings.taskid,
						images : $('input[name="images[]"]').arrval(),
						content : $('textarea[name="content"]').val(),
					}

					var form = [];
					$('.formitem').each(function(){
						var value = $(this).val();
						var id = $(this).attr('fid');
						form.push( {id:id,value:value} );
					});
					$('.formitemimg').each(function(){
						var id = $(this).attr('fid');
						var value = $('input[name="'+id+'[]"]').arrval();
						form.push( {id:id,value:value} );
					});					
					postdata.form = JSON.stringify(form);

					common.confirm('提示','确定要回复吗？',function(){
						if( task.params.ispaying ) return false;
						task.params.ispaying = true;
						common.Http('post','json',common.createUrl('ajaxdeal','reply'),postdata,function(data){
							if(data.status == 200){
								common._alert(data.res,function(){
									location.href = "";
								});
							}else if( data.status == 220 ){
								common._alert(data.res,function(){
									location.href = common.createUrl('regist','op');
								});
							}else{
								common._alert(data.res);
							}
							task.params.ispaying = false;
						});
					})
				}
			},
			'input[name=isall]' : {
				change : function(){
					if( $(this).prop('checked') ){
						$('input[name="reply[]"]').prop('checked',true);
					}else{
						$('input[name="reply[]"]').prop('checked',false);
					}
				}
			},
			'input[name="reply[]"]' : {
				change : function(){
					var all = $('input[name="reply[]"]').length;
					var checked = $('input[name="reply[]"]:checked').length;
					if( all == checked ){
						$('input[name=isall]').prop('checked',true);
					}else{
						$('input[name=isall]').prop('checked',false);
					}
				}
			},
			'.agree' : {
				click : function(){
					var reid = $(this).attr('reid');
					task.fn.deal( {idlist : [reid]},'确定要采纳这条回复吗？' ,'agree',$(this) );
				}
			},
			'.show' : {
				click : function(){
					var reid = $(this).attr('reid');
					task.fn.deal( {idlist : [reid]},'确定要显示这条回复吗？' ,'show',$(this) );
				}
			},
			'.hide' : {
				click : function(){
					var reid = $(this).attr('reid');
					task.fn.deal( {idlist : [reid]},'确定要隐藏这条回复吗？' ,'hide',$(this) );
				}
			},
			'.remind' : {
				click : function(){
					var reid = $(this).attr('reid');
					$.prompt('请填入提醒内容', function (value) {
						var postdata = {
							idlist : [reid],
							content : value,
						}
						if( value == '' ) {
							common.alert('请输入内容');return false;
						}
						task.fn.deal( postdata,'10分钟内只能提醒对方一次，确定要提醒对方吗？' ,'remind' );
					});
				}
			},			
			'.refuse' : {
				click : function(){
					var reid = $(this).attr('reid');
					var _this = $(this);
					$.prompt('请填入拒绝理由', function (value) {
						var postdata = {
							idlist : [reid],
							reason : value,
						}
						task.fn.deal( postdata,'确定要拒绝这条回复吗？' ,'refuse',_this );
					});
				}
			},
			'#agreeall' : {
				click : function(){
					var all = $('input[name="reply[]"]:checked').arrval();
					if( all.length <= 0 ){
						common.alert('您还没选择');
						return false;
					}
					task.fn.deal( {idlist : all},'确定要采纳选择的吗？' ,'agree' );
				}
			},
			'#refuseall' : {
				click : function(){
					var all = $('input[name="reply[]"]:checked').arrval();
					if( all.length <= 0 ){
						common.alert('您还没选择');
						return false;
					}
					$.prompt('请填入拒绝理由', function (value) {
						var postdata = {
							idlist : all,
							reason : value,
						}
						task.fn.deal( postdata,'确定要拒绝选择的吗？' ,'refuse' );
					});
				}
			},
			'#dealtask' : {
				click : function(){
					$.popup('.popup-filter-dealtask');
				}
			},
			'#count_task' : {
				click : function(){
					var postdata = {
						taskid : settings.taskid,
					};
					common.confirm('提示','结算后未采纳的回复都将被采纳，确定要将此任务结算吗？',function(){
						if( task.params.ispaying ) return false;
						task.params.ispaying = true;
						common.Http('post','json',common.createUrl('ajaxdeal','counttask'),postdata,function(data){
							if(data.status == 200){
								common._alert(data.res,function(){
									location.href = "";
								});
							}else{
								common._alert(data.res);
							}
							task.params.ispaying = false;
						});
					})
				}
			},
			'.pause_task' : {
				click : function(){
					var postdata = {
						taskid : settings.taskid,
						type : $(this).attr('type'),
					};
					var str = '关闭任务后，其他会员不能再接此任务，确定要关闭吗？';
					if( postdata.type == 1 ) str = '开启后会员可接此任务，确定要开启吗？';
					common.confirm('提示',str,function(){
						if( task.params.ispaying ) return false;
						task.params.ispaying = true;
						common.Http('post','json',common.createUrl('ajaxdeal','pause'),postdata,function(data){
							if(data.status == 200){
								common._alert(data.res,function(){
									location.href = "";
								});
							}else{
								common._alert(data.res);
							}
							task.params.ispaying = false;
						});
					})
				}
			},
			'#down_task' : {
				click : function(){
					common._alert( '请在电脑浏览器上输入以下链接下载：'+settings.downurl );
				}
			},
			'#addtask' : { // 追加
				click : function(){
			      $.prompt('请填入追加的数量', function (value) {

			      	var postdata = {
			      		taskid : settings.taskid,
			      		value : value,
			      	}

			      	if( !common.verify('number','int',value) || value <= 0 ){
			      		common.alert('请输入正整数值');
			      		return false;
			      	}
			      	var taskmoney = settings.money*value;
			      	var server = taskmoney*settings.commonserver/100;
			      	var ewai = 0;

			      	if( settings.continue == 1 ){
			      		ewai = value*settings.continuemoney;
			      	}

			      	var total = taskmoney+server+ewai;
					common.confirm('提示','追加任务将扣除'+total+settings.cname+'，其中额外奖励'+ewai+'，服务费'+server+'。确定要追加吗？',function(){
						if( task.params.ispaying ) return false;
						task.params.ispaying = true;
						common.Http('post','json',common.createUrl('ajaxdeal','subaddtask'),postdata,function(data){
							if(data.status == 200){
								common._alert(data.res,function(){
									location.href = "";
								});
							}else{
								common._alert(data.res);
							}
							task.params.ispaying = false;
						});
					})
			      });
				}
			},
			
			'#complain_confirm' : { // 投诉
				click : function(){
					var postdata = {
						taskid : settings.taskid,
						content : $('textarea[name=commplain]').val(),
						images : $('input[name="complainimages[]"]').arrval(),
					};
					if( postdata.content == '' ){
						common.alert('请填写投诉内容');
						return false;
					}
					task.fn.deal( postdata,'确定要提交投诉吗？' ,'complain' );
				}
			},
			'#task_continue' : {
				click : function(){
					$.popup('.popup-continue');
					if( config.conlist.isdeal ) return false;
					config.conlist.isdeal = true;
					common.Http('post','json',common.createUrl('pagelist','getconlist'),config.conlist,function(data){
						$('.popup_continue_list').append(data.data);
					});
				}
			},
			'.addcontent' : {
				click : function(){
					task.params.takedid = $(this).attr('reid');
					task.params.addbtn = $(this);
					$.popup('.popup-addcontent');
				}
			},
			'#addcontent_confirm' : {
				click : function(){
					var postdata = {
						takedid : task.params.takedid,
						content : $('textarea[name=addcontent]').val(),
						images : $('input[name="addimages[]"]').arrval(),
					};
					if( postdata.content == '' && postdata.images.length <= 0 ) {
						common.alert('请填写内容或上传图片');return false;
					}
					common.confirm('提示','补充后不能修改，且半个小时内只能补充一次，确定补充内容吗？',function(){
						if( task.params.ispaying ) return false;
						task.params.ispaying = true;
						common.Http('post','json',common.createUrl('ajaxdeal','addcontenttask'),postdata,function(data){
							if(data.status == 200){
								common._alert(data.res,function(){
									task.params.addbtn.parent().empty().removeClass('item_cell_box').html('<span class="task_replay_status">待采纳</span>');
									$.closeModal('.popup-addcontent');
									$('textarea[name=addcontent]').val('');
									$('.popup-addcontent .upload_images_views').empty();
								});
							}else{
								common._alert(data.res);
							}
							task.params.ispaying = false;
						});
					})
				}
			},
			'#readanswer' : {
				click : function(){
					common.confirm('提示','查看答案需要花费'+settings.readprice+'，确定查看吗？',function(){
						if( pub.params.ispaying ) return false;
						pub.params.ispaying = true;
						common.Http('post','json',common.createUrl('ajaxdeal','readanswer'),{id:settings.taskid},function(data){
							if(data.status == 200){
								common._alert(data.res,function(){
									location.href = common.createUrl('task','op',{id:settings.taskid});
								});
							}else if( data.status == 210 ){
							    $.modal({
							      title:  '提示',
							      text: data.res,
							      buttons: [
							        {
							          text: '关闭',
							          onClick: function() {}
							        },
							        {
							          text: '去充值',
							          onClick: function() {
							            location.href = data.obj.url;
							          }
							        },
							      ]
							    });
							}else{
								common._alert(data.res);
							}
							pub.params.ispaying = false;
						});		
					});
				}
			},
			'.zanbtn' : {
				click : function(){
					var reid = $(this).attr('reid');
					var _this = $(this);
					$.prompt('请填入打赏给对方的银票', function (value) {
						var postdata = {
							reid : reid,
							num : value,
						}
						if( !common.verify('number','int',value) ){
							common.alert('请填写正整数');return false;
						}

						task.fn.deal( postdata,'确定要打赏吗？' ,'dashang',_this );
					});
				}
			},
			'#restart' : {
				click : function(){
					var postdata = {
						tid : settings.taskid,
					};
					common.confirm('提示','恢复任务将扣除'+settings.backed+settings.cname+'，确定恢复吗？',function(){
						if( task.params.ispaying ) return false;
						task.params.ispaying = true;
						common.Http('post','json',common.createUrl('ajaxdeal','restart'),postdata,function(data){
							if(data.status == 200){
								common._alert(data.res,function(){
									location.href = "";
								});
							}else if(data.status == 210){
							    $.modal({
							      title:  '提示',
							      text: data.res,
							      buttons: [
							        {
							          text: '关闭',
							          onClick: function() {}
							        },
							        {
							          text: '去充值',
							          onClick: function() {
							            location.href = data.obj.url;
							          }
							        },
							      ]
							    });
							}else{
								common._alert(data.res);
							}
							task.params.ispaying = false;
						});
					})
				}
			}
		},
		fn : {
			deal : function(postdata,notice,op,elem){
				common.confirm('提示',notice,function(){
					if( task.params.ispaying ) return false;
					task.params.ispaying = true;
					common.Http('post','json',common.createUrl('ajaxdeal',op),postdata,function(data){
						if(data.status == 200){
							if( elem ){
								if( op == 'refuse' || op == 'agree' ){
									var str = '已采纳';
									if( op == 'refuse' ) str = '已拒绝';
									elem.parents('.task_replay_bottom').html('<li class="task_replay_status tr" style="width:100%">'+str+'</li>');
								}else if(op == 'show' || op == 'hide'){
									if(op == 'show') elem.text('隐藏').addClass('hide').removeClass('show');
									if(op == 'hide') elem.text('显示').addClass('show').removeClass('hide');
									common.alert(data.res);
								}else if(op == 'dashang'){
									var now = elem.prev().text()*1;
									var next = postdata.num*1 + now;
									elem.prev().text(next);
									common.alert(data.res);
								}
							}else{
								common._alert(data.res,function(){
									location.href = "";
								});
							}
						}else{
							common._alert(data.res);
						}
						task.params.ispaying = false;
					});
				})
			},
			getmessage : function(){
				if( config.message.isdeal ) return false;
				config.message.isdeal = true;
				common.Http('post','json',common.createUrl('pagelist','getmeesage'),config.message,function(data){
					if(data.status == 'ok'){
						config.message.page ++;
						$('.popup_message_list').append(data.data);
						$('.more_message').show();
					}else{
						$('.more_message').hide();
					}
					config.message.isinit = true;
					config.message.isdeal = false;
				});
			},
			copyit : function(down){
				var clipboard = new Clipboard('#copy_it');

				clipboard.on('success', function(e) {
				    //console.info('Action:', e.action);
				    //console.info('Text:', e.text);
				    //console.info('Trigger:', e.trigger);

			        e.trigger.innerHTML = down ? "已复制成功" : "已复制成功";
			        e.trigger.style.backgroundColor="#9ED29E";
				    e.clearSelection();
				});

				clipboard.on('error', function(e) {
			      	e.trigger.innerHTML="复制失败,请手动复制";
			      	e.trigger.style.backgroundColor="#666";
				    console.error('Action:', e.action);
				    console.error('Trigger:', e.trigger);
				});

				var isapple = task.fn.isApple();
				if(!isapple){
					$('#android_code').show().prev().hide();
					//$('.popwtitle').html('长按框内口令&gt;全选&gt;复制');
				}else{
					task.fn.select('copybox');
				}
			},
			isApple : function(){
				var ua = navigator.userAgent.toLowerCase();
				if (ua.match(/iphone/i) == "iphone" || ua.match(/ipad/i) == "ipad"){
					return true;
				}
				return false;
			},
			select : function(classname){
				//return false;

		        document.addEventListener("selectionchange",
		        function(e) {
		            if (window.getSelection().anchorNode.parentNode.id == 'iphone_code' && document.getElementById('iphone_code').innerText != window.getSelection()) {
		              var key = document.getElementById('iphone_code');
		              window.getSelection().selectAllChildren(key);
		            }
		        },false);
			},
			confirmmessage : function(){
				var postdata = {
					message : $('textarea[name=taskmessage]').val(),
					taskid : settings.taskid,
					from : settings.do,
				};
				if( postdata.message == '' ){
					common.alert('请填写留言');
					return false;
				}
				common.confirm('提示','确定要留言吗？',function(){
					if( task.params.ispaying ) return false;
					task.params.ispaying = true;
					common.Http('post','json',common.createUrl('ajaxdeal','pubmessage'),postdata,function(data){
						if(data.status == 200){
							common._alert(data.res,function(){
								$('.popup_message_list').prepend(data.obj.str);
								$('textarea[name=taskmessage]').val('');
								$.closeModal('.popup-message');
								$('#task_message .font_ff5f27').text( $('#task_message .font_ff5f27').text()*1 + 1 );
							});
						}else{
							common._alert(data.res);
						}
						task.params.ispaying = false;
					});
				});
			},
			replymessage : function( elem ){
				var mid = elem.attr('mid');
				$.prompt('请填入回复内容', function (value) {
					var postdata = {
						mid : mid,
						value : value,
						from : settings.do,
					}
					if( task.params.ispaying ) return false;
					task.params.ispaying = true;
					common.Http('post','json',common.createUrl('ajaxdeal','replymessage'),postdata,function(data){
						common._alert(data.res);
						if(data.status == 200){
							elem.parent().next().append(data.obj.str);

						}
						task.params.ispaying = false;
					});
				});
			},
			takecomtask : function(){
				var postdata = {
					taskid : settings.taskid,
				}
				if( task.params.ispaying ) return false;
				task.params.ispaying = true;
				common.Http('post','json',common.createUrl('ajaxdeal','taketask'),postdata,function(data){
					if(data.status == 200){
						common._alert(data.res,function(){
							location.href = "";
						});
					}else if( data.status == 220 ){
						common._alert(data.res,function(){
							location.href = data.obj.url;
						});
					}else if( data.status == 211 ){
						$('#take_btn .pub_btn').text('您所在地区不能接此任务').addClass('pub_btn_disabled');
						common._alert(data.res);
					}else{
						common._alert(data.res);
					}
					task.params.ispaying = false;
				});
			}
		}
	};	


	var money = {
		params : {

		},
		init: {
			initfun : function(){
				common.goToTop('content'); //回到顶部
				common.uploadImageByWxJs('.uploader_input_qrcode',1,'images','replace');
			}
		},
		events:{
			'#confirm_addmoney' : {
				click : function(){
					var value = $('input[name=money]').val()*1;
					if( value <= 0 || !common.verify('number','money',value) ){
						common.alert('金额必须是数字,最多2位小数');return false;
					}
					money.fn.changeParamsFunc();
				}
			},
			'#money_getout' : {
				click : function(){
					var postdata = {
						money : $('input[name=money]').val()*1,
						type : settings.do,
						code : $('input[name=code]').val(),
					};
					if( postdata.money <= 0 || !common.verify('number','money',postdata.money) ){
						common.alert('金额必须是数字,最多2位小数');return false;
					}
					var server = (postdata.money*settings.server/100).toFixed(2);
					var total = ( postdata.money*1 + server*1 ).toFixed(2);
					var strstr = settings.cname;
					if( settings.do == 'deposit' ) strstr = '保证金';

					common.confirm('提示','将扣除'+total+strstr+'，其中手续费'+server+'，确定要提现吗？',function(){
						if( money.params.ispaying ) return false;
						money.params.ispaying = true;
						common.Http('post','json',common.createUrl('ajaxdeal','outmoney'),postdata,function(data){
							if(data.status == 200){
								common._alert(data.res,function(){
									location.href = "";
								});
							}else{
								common._alert(data.res);
							}
							money.params.ispaying = false;
						});
					});
				}
			},
			'#save_alipay' : {
				click : function(){
					var postdata = {
						name : $('input[name=name]').val(),
						account : $('input[name=alipay]').val(),
						type : $(this).attr('type'),
						images : $('input[name="images[]"]').val(),
					};
					if( postdata.type == 1 && ( postdata.name == '' || postdata.account == '' ) ){
						common.alert('请填写完整');return false;
					}
					if( postdata.type == 2 && typeof postdata.images == 'undefined' ){
						common.alert('请上传微信收款二维码');return false;
					}					
					
					common.confirm('提示','确定要保存吗？',function(){
						if( money.params.ispaying ) return false;
						money.params.ispaying = true;
						common.Http('post','json',common.createUrl('ajaxdeal','savealipay'),postdata,function(data){
							if(data.status == 200){
								common._alert(data.res,function(){
									location.href = common.createUrl(settings.do,'out');
								});
							}else{
								common._alert(data.res);
							}
							money.params.ispaying = false;
						});
					});
				}
			},
			'#getvertify' : {
				click :function(){
					set.fn.sendcode();
				}
			}
		},
		fn : {
			changeParamsFunc : function(){ //选择地址，卡券等重置参数
				if( money.params.ispaying ) return false;
				money.params.ispaying = true;
				var postdata = {
					money : $('input[name=money]').val(),
					type : settings.do,
				};
				
				common.Http('post','json',common.createUrl('ajaxdeal','addmoney'),postdata,function(data){
					if(data.status == 200){
						var agent = common.agent();
						
						if( agent == 'wxapp' ) {
                            wx.miniProgram.navigateTo({
                                url: '/zofui_taskwxapp/pay/pay?oid='+data.obj.oid,
                            });
						}else{
							$('input[name=params]').val( data.obj.params );
							$('#weixin').submit();
						}
					}else{
						common.alert(data.res);
					}
					money.params.ispaying = false;
				});
			}
		}
	};		

	var login = {
		params : {

		},
		init: {
			initfun : function(){
				var account = common.cookie.get('loginacciunt');
				if( account ){
					$('input[name="account"]').val(account);
				}
			}
		},
		events:{
			'#login' : {
				click :function(){
					if( login.params.ispaying ) return false;
					login.params.ispaying = true;
					var postdata = {
						account : $('input[name="account"]').val(),
						pass : $('input[name="pass"]').val(),
						key : common.cookie.get('loginzf'),
					};
					
					common.Http('post','json',common.createUrl('ajaxdeal','login'),postdata,function(data){
						if(data.status == 200){
							common._alert('登录成功',function(){
								location.reload();
								common.cookie.set('loginacciunt',postdata.account,3600*24*100);
							})
						}else{
							common.alert(data.res);
						}
						login.params.ispaying = false;
					});

				}
			},
			'.logincode' : {
				click :function(){
					var type = $(this).attr('type');
					set.fn.sendcode(type);
				}
			},
			'#register' : {
				click :function(){
					if( login.params.ispaying ) return false;
					login.params.ispaying = true;
					var postdata = {
						account : $('input[name="tel"]').val(),
						pass1 : $('input[name="pass1"]').val(),
						pass2 : $('input[name="pass2"]').val(),
						code : $('input[name="code"]').val(),
						qq : $('input[name="qq"]').val(),
						key : common.cookie.get('loginzf'),
					};
					
					common.Http('post','json',common.createUrl('ajaxdeal','register'),postdata,function(data){
						if(data.status == 200){
							common._alert('注册成功',function(){
								if( settings.regurl ){
									location.href = settings.regurl;
								}else{
									location.href = $('#logina').attr('href');
								}
							})
						}else{
							common.alert(data.res);
						}
						login.params.ispaying = false;
					});

				}
			},
			'#next' : {
				click :function(){
					if( login.params.ispaying ) return false;
					login.params.ispaying = true;
					var postdata = {
						account : $('input[name="tel"]').val(),
						code : $('input[name="code"]').val(),
						key : common.cookie.get('loginzf'),
					};
					
					common.Http('post','json',common.createUrl('ajaxdeal','checkcode'),postdata,function(data){
						if(data.status == 200){
							$('.pre,#next').hide();
							$('.next,#pre,#resetpass').show();
						}else{
							common.alert(data.res);
						}
						login.params.ispaying = false;
					});

				}
			},
			'#pre' : {
				click :function(){
					$('.pre,#next').show();
					$('.next,#pre,#resetpass').hide();
				}
			},
			'#resetpass' : {
				click :function(){
					if( login.params.ispaying ) return false;
					login.params.ispaying = true;
					var postdata = {
						account : $('input[name="tel"]').val(),
						pass1 : $('input[name="pass1"]').val(),
						pass2 : $('input[name="pass2"]').val(),
						code : $('input[name="code"]').val(),
						key : common.cookie.get('loginzf'),
					};
					
					common.Http('post','json',common.createUrl('ajaxdeal','resetpass'),postdata,function(data){
						if(data.status == 200){
							common._alert('已修改，请登录',function(){
								location.href = $('#logina').attr('href');
							})
						}else{
							common.alert(data.res);
						}
						login.params.ispaying = false;
					});

				}
			},

		},
		fn : {

		}
	};	

	var level = {
		params : {},
		events:{
			'.level_paybtn' : {
				click : function(){
					if( level.params.ispaying ) return false;
					level.params.ispaying = true;
					var postdata = {
						utype : $(this).attr('type') 
					};
					
					common.Http('post','json',common.createUrl('ajaxdeal','addulevel'),postdata,function(data){
						if(data.status == 200){
							var agent = common.agent();
							if( agent == 'wxapp' ) {
	                            wx.miniProgram.navigateTo({
	                                url: '/zofui_taskwxapp/pay/pay?oid='+data.obj.oid,
	                            });
							}else{
								$('input[name=params]').val( data.obj.params );
								$('#weixin').submit();
							}
						}else{
							common.alert(data.res);
						}
						level.params.ispaying = false;
					});
				}
			},
		}
	}

	/////////////index
	var index = {
		init: {
			initfun : function(){

				common.goToTop('content'); //回到顶部
				//common.chageTitle(settings.title);
				if( settings.adtype == 0 ){
					common.scrollNotice($('.index_ad_r'),600,3000,1);
				}else if( settings.adtype == 1 ){
					common.scrollNotice2(31);
				}
				index.fn.act();
			}
		},
		events:{
			'.index_filter' : {
				click : function(){
					$.popup('.popup-filter-indexnav');
				}
			},
			'.focus_me' : {
				click : function(){
					config.page.type = $(this).attr('sort');
					$('.sort_name').removeClass('pri-color');
					$(this).find('p').addClass('pri-color');
					window.detail2listobj.destroy();
					window.detail2listobj = new h5Detail2list();
					getpage.reset();
				}
			},
			'.tasktab_sorti' : {
				click : function(){
					config.page.type = $(this).attr('sort');
					$('.tasktab_sorti').removeClass('pri-color').removeClass('pri-border');
					$(this).addClass('pri-color').addClass('pri-border');
					window.detail2listobj.destroy();
					window.detail2listobj = new h5Detail2list();
					getpage.reset();
				}
			},
			'#more_btn' : {
				click : function(){
					$('.more_menu').toggle();
				}
			},
			'#icantake' : {
				click : function(){
					config.page.icantake = 1;
					window.detail2listobj.destroy();
					window.detail2listobj = new h5Detail2list();
					getpage.reset();
				}
			},
			'#confirmsearch' : {
				click : function(){
					var v = $('#search').val();
					if( v == '' || !v ) {
						common.alert('请输入搜索内容');
						return false;
					}
					config.page.search = v;
					window.detail2listobj.destroy();
					window.detail2listobj = new h5Detail2list();
					getpage.reset();
				}
			}
		},
		fn : {
			act : function(){
				if( detail2listobj.getLocalStorage()['extraData']['type'] != undefined ){
					$('.sort_name').css('color','#333333');
					//console.log( $('.sort_name[sort="'+detail2listobj.getLocalStorage()['extraData']['type']+'"]').find('p') )
					$('.focus_me[sort="'+detail2listobj.getLocalStorage()['extraData']['type']+'"]').find('p').css('color','#ed414a');
				}
			}
		}
	};
	

	var set = {
		params : {
			waitTime : 60,
		},
		init: {
			initfun : function(){
				
				common.uploadImageByWxJs('#uploader_input_qrcode',1,'images','replace');
				common.uploadImageByWxJs('#uploader_input_payqr',1,'payqr','replace');
				common.uploadImageByWxJs('#uploader_input_headimgurl',1,'headimgurl','replace');
				
				$(".datetime-picker").datetimePicker({});
				$(".city-picker").each(function(){
					var city = $(this).val();
					
					$(this).cityPicker({
					    toolbarTemplate: '<header class="bar bar-nav">\
					    <button class="button button-link pull-right close-picker">确定</button>\
					    <h1 class="title">选择城市</h1>\
					    </header>',
					    onOpen : function(){},
					    formatValue : function(a,b){
					    	var value = b[0] +' '+b[1]+' '+b[2];
					    	return value;
					    },
					});
				});

				$('.form_upload').each(function(){
					var name = $(this).attr('name');
					common.uploadImageByWxJs('.form_upload[name="'+name+'"]',1,'form','replace');
				});
			}
		},
		events:{
			'.get_code' : {
				click : function(){
					set.fn.sendcode();
				}
			},
			'input[name="isauth"]' : {
				change : function(){
					if( $(this).prop('checked') ){
						$('.isauth').show();
					}else{
						$('.isauth').hide();
					}
				}
			},
			'.show_explain' : {
				click : function(){
					$.popup('.popup-privatetask-set');
				}
			},
			'textarea[name="guydesc"]' : {
				'input propertychange' : function(){
					var _this = $(this),
	                _val = _this.val(),
	                count = "";
			        if (settings.maxguydc > 0 && _val.length > settings.maxguydc ) {
			            _this.val(_val.substring(0, settings.maxguydc));
			            common.alert('最多'+settings.maxguydc+'个字符');
			        }
				}
			},
			'#save_set' : {
				click : function(){
					var postdata = $('form').serializeJson();
					if( !common.verify('mobile',postdata.tel) ){
						common.alert('请输入正确的手机号');return false;
					}

					if( postdata.isauth == 1 ){
						var ispass = 1;
						$('input[name="form[]"]').each(function(){
							var v = $(this).val();
							if( v == '' || v == null || v == undefined ) {
								var name = $(this).parents('.form_list').find('.form_title,.pub_content_title').text();
								ispass = 0;
								common.alert('请设置'+name);return false;
							}
						});
						if( !ispass ) return false;
					}

					postdata.formarr = $('input[name="form[]"]').arrval();
					postdata.formidarr = $('input[name="formid[]"]').arrval();
					postdata.guysorta = $('input[name="guysort[]"]:checked').arrval();

					if( postdata.isauth == 1 && settings.authneed > 0 ){
						var authtimestr = '';
						if( settings.authtime > 0 ) authtimestr = '认证有效期为'+settings.authtime+'天，';
						common.confirm('提示','认证需要扣除'+settings.authneed+'余额，'+authtimestr+'确定认证吗？',function(){
							set.fn.saveset(postdata);
						});
					}else{
						set.fn.saveset(postdata);
					}
				}
			},
			'.messinput' : {
				change : function(){
					var postdata = {
						taked : $('input[name="taked"]:checked').val(),
						messaged : $('input[name="messaged"]:checked').val(),
						count : $('input[name="count"]:checked').val(),
						reply : $('input[name="reply"]:checked').val(),
						getmessage : $('input[name="getmessage"]:checked').val(),
						getpri : $('input[name="getpri"]:checked').val(),
						newdown : $('input[name="newdown"]:checked').val(),
						downmoney : $('input[name="downmoney"]:checked').val(),
						downact : $('input[name="downact"]:checked').val(),
						usesuborder : $('input[name="usesuborder"]:checked').val(),
						useaddcontent : $('input[name="useaddcontent"]:checked').val(),
						newtask : $('input[name="newtask"]:checked').val(),
					}

					for( t in postdata  ){
						if( typeof postdata[t] == 'undefined'){
							postdata[t] = 2;
						}
					}
					common.Http('POST','json',common.createUrl('ajaxdeal','setmess'),postdata,function(data){
						if(data.status == 200){
							common.alert('设置成功');
						}else{
							common._alert(data.res);
						}			
					});
				}
			}
		},
		fn : {
			timeDesc : function(elem){ //倒计时效果
				var thisele = $(elem);
				if (set.params.waitTime <= 0) {
					thisele.text('获取验证码');
					set.params.ing = false;
					set.params.waitTime = 60;
				} else {  			
					thisele.text("重新发送(" + set.params.waitTime + ")");  
					set.params.waitTime--; 
					setTimeout(function() {  
						set.fn.timeDesc(elem);
					},1000);
				}
			},
			sendcode : function(type){
				var mobile = $('input[name=tel]').val();
				if(!common.verify('mobile',mobile)){		
					common.alert('请输入正确的手机号');return false;
				}
				var postdata = {
					mobile:mobile,
					key : common.cookie.get('loginzf'),
					type : type,
				};
				
				if( set.params.ing ) return false;
				set.params.ing = true;
				common.Http('POST','json',common.createUrl('ajaxdeal','tovertify'),postdata,function(data){
					if(data.status == 200){
						common.alert('验证码已发送，请填入');
						set.fn.timeDesc('#getvertify');
					}else{
						set.params.ing = false;
						common.alert(data.res);
					}
				});
			},
			saveset : function(postdata){
				common.Http('POST','json',common.createUrl('ajaxdeal','saveset'),postdata,function(data){
					if(data.status == 200){
						common._alert('已保存',function(){
							location.href = "";
						});
					}else if( data.status == 210 ){
					    $.modal({
					      title:  '提示',
					      text: data.res,
					      buttons: [
					        {
					          text: '关闭',
					          onClick: function() {
					            
					          }
					        },
					        {
					          text: '去充值',
					          onClick: function() {
				          		common.cookie.set('oldurl',data.obj.url,3600);
					            location.href = data.obj.url;
					          }
					        },
					      ]
					    });
					}else{
						common._alert(data.res);
					}			
				});
			}
		}
	};
	
	var regist = {
		init: {},
		events:{
			'#confirm_regist' : {
				click : function(){
					var postdata = {
						mobile : $('input[name=tel]').val(),
						code : $('input[name=code]').val(),
					};
					if( !common.verify('mobile',postdata.mobile) ){
						common.alert('请输入正确的手机号');return false;
					}
					if( postdata.code == '' ){
						common.alert('请填写验证码');return false;
					}
					common.Http('POST','json',common.createUrl('ajaxdeal','regist'),postdata,function(data){
						if(data.status == 200){
							common._alert('已保存',function(){
								window.history.go(-1);
							});
						}else{
							common._alert(data.res);
						}			
					});
				}
			},
			'.confirm_bind' : {
				click : function(){
					var postdata = {
						mobile : $('input[name=bindtel]').val(),
						pass1 : $('input[name=bindpass1]').val(),
						pass2 : $('input[name=bindpass2]').val(),
					};
					if( !common.verify('mobile',postdata.mobile) ){
						common.alert('请输入正确的手机号');return false;
					}
					if( postdata.pass1 == '' || postdata.pass2 == '' ){
						common.alert('请填写密码');return false;
					}
					if( postdata.pass1 != postdata.pass2 ){
						common.alert('两次密码不一致');return false;
					}
					common.Http('POST','json',common.createUrl('ajaxdeal','bindaccount'),postdata,function(data){
						if(data.status == 200){
							if( settings.do == 'regist' ){
								common._alert('已绑定',function(){
									window.history.go(-1);
								});
							}else{
								common._alert('已绑定',function(){
									location.href = "";
								});
							}

						}else{
							common._alert(data.res,function(){
								location.href = "";
							});
						}			
					});
				}
			},
			'#getvertify' : {
				click :function(){
					set.fn.sendcode();
				}
			}
		},
		fn : {}
	};
	
	var find = {
		init: {
			initfun : function(){
				common.goToTop('content'); //回到顶部
				index.fn.act();
			}
		},
		events:{
			'.focus_me' : {
				click : function(){
					config.page.type = $(this).attr('sort');
					$('.sort_name').css('color','#333333');
					$(this).find('p').css('color','#ed414a');
					window.detail2listobj.destroy();
					window.detail2listobj = new h5Detail2list();
					getpage.reset();
				}
			},
			'.imess_readit' : {
				click : function(){
					var _this = $(this);
					var postdata = {
						mid : _this.attr('mid'),
						type : 'single',
					}
					common.Http('POST','json',common.createUrl('ajaxdeal','readimess'),postdata,function(data){
						common.alert(data.res);
						if( data.status == 200 ){
							_this.parent().html('<span class="imess_readed">已阅</span>');
							var now = $('.nownum').text() * 1;
							$('.nownum').text( now - 1 );
						}
					});
					return false;
				}
			},
			'.imgess_readall' : {
				click : function(){
					common.Http('POST','json',common.createUrl('ajaxdeal','readimess'),{type:'all'},function(data){
						common.alert(data.res);
						if( data.status == 200 ){
							$('.imess_status').html('<span class="imess_readed">已阅</span>');	
							$('.nownum').text('0');							
						}
					});
					return false;
				}
			}
		},
		fn : {}
	};

	var guy = {
		init: {
			initfun : function(){
				common.uploadImageByWxJs('.uploader_input_sub',9,'images');
				common.viewImages();
			}
		},
		events:{
			'.guy_contact_wx' : {
				click : function(){
					guy.fn.showdia( $('#contact_me') );
				}
			},
			'.givetask_btn' : {
				click : function(){
					$.popup('.popup-guytask');
				}
			},
			'#guy_confirm' : {
				click : function(){
					
					var postdata = {
						tasktitle : $('textarea[name=tasktitle]').val(),
						taskmoney : $('input[name=taskmoney]').val(),
						tasktime : $('input[name=tasktime]').val(),
						guyid : settings.guyid,
						images : $('input[name="images[]"]').arrval(),
						type : $(this).attr('type'),
					};
					
					if(postdata.tasktitle == '') common.alert('请输入任务内容');
					if( postdata.taskmoney < settings.leastprimoney*1 ) {common.alert('赏金必须大于等于'+settings.leastprimoney);return false}
					if(!common.verify('number','money',postdata.taskmoney)) {common.alert('请输入正确的金额,最多2位小数');return false}
					if(!common.verify('number','int',postdata.tasktime)) {common.alert('请输入正确时限,必须是整数');return false}
					if( postdata.taskmoney <= 0.01 ) {common.alert('任务赏金不能低于0.01');return false}
					
					common.confirm('提示','确定发给对方吗？',function(){
						common.Http('POST','json',common.createUrl('ajaxdeal','pubprivatetask'),postdata,function(data){
							if( data.status == 200 ){
								common._alert('发起私包任务成功',function(){
									location.href = common.createUrl('privatetask','',{'id':data.obj.id});
								});									
							}else if( data.status == 210 ){
								common.confirm('提示','您的'+settings.cname+'不足，点击确定去充值。',function(){
									location.href = common.createUrl('money','in');
								});	
							}else{
								common._alert(data.res);
							}
						});
						
					});
				}
			},
			'input[name=taskmoney]' : { // 计算金额
				'input propertychange' : function(){
					var taskmoney = $('input[name=taskmoney]').val()*1; // 任务金额

					if( settings.op =='puber' ){
						var first = taskmoney*settings.priserverend/100; // 服务费
						var server = Math.max.apply(Math,[first,settings.prileastend]);
						if( server >= taskmoney ){
							var server = 0;
						}
						$('.popup-guytask .server').text(server.toFixed(2)).siblings('.income').text((taskmoney*1-server).toFixed(2));
					}else{
						var first = taskmoney*settings.priserver/100; // 服务费
						var server = Math.max.apply(Math,[first,settings.prileast]);
						$('.popup-guytask .server').text(server.toFixed(2)).siblings('.income').text((taskmoney*1+server).toFixed(2));
					}
					
				}
			}
		},
		fn : {
			showdia : function( elem ){
				elem.show();
				elem.find('.atom-dialog').css({'top':'-100%'}).animate({
					'top' : '15%'
				},500,'ease',function(){
					elem.find('.atom-dialog').addClass('elastic');
				});
			} 
		}
	};

	var down = {
		init : {
			initfun : function(){
				task.fn.copyit(1);

			}
		},
		events : {
			'.sharepriend' : {
				click : function(){
					if( settings.dev == 'wx' ){
						$('.down_sharep').show();
					}else{
						common._alert('可选择链接分享，将链接发给微信内的好友');
					}
				}
			},
			'.down_sharep .mask' : {
				click : function(){
					$('.down_sharep').hide();
				}
			},
			'#showshare' : {
				click : function(){
					$.popup('.popup-filter-sharep');
				}
			},
			'.show_explain' : {
				click : function(){
					$.popup('.popup-privatetask-down');
				}
			},
			'#down_showposter .mask' : {
				click : function(){
					$(this).parents('.down_showposter').hide();
				}
			},
			'.linkshare' : {
				click : function(){
					$('.down_copybox').show();
				}
			},
			'.down_copybox .mask' : {
				click : function(){
					$(this).parents('.down_copybox').hide();
				}
			},
			'.createPoset' : {
				click : function(){
					var appacid = common.cookie.get('appaciddsa');

					var postdata = {
						appacid : appacid
					}
					common.Http('POST','json',common.createUrl('ajaxdeal','getmyposter'),postdata,function(data){
						if( data.status == 200 ){
							$('.down_imgbox img').attr('src',data.obj.url);
							$('.down_showposter').show();
							common._alert('发送给您的朋友即可邀请加入');
						}else{
							common._alert(data.res);
						}
					});
				}
			},
			'.share_typebtn' : {
				click : function(){
					var type = $(this).attr('type');
					$('.share_typebtn').removeClass('share_typeact');
					$(this).addClass('share_typeact');
					$('.sharetype').hide();
					$('.sharetype[type="'+type+'"]').show();
				}
			},
			'.twodown,.threedown' : {
				click : function(){
					var postdata = {
						type : $(this).hasClass('twodown') ? 2 : 3,
						id : $(this).attr('data-id'),
					}
					if( settings.downnum < 1 ) return false;
					if( postdata.type == 3 && settings.downnum < 2 ) return false;

					common.Http('POST','json',common.createUrl('pagelist','getdown'),postdata,function(data){
						if( data.status == 201 ){
							common._alert(data.res);
						}else{
							$('.popup-downa .content-body').html( data.data );
							$.popup('.popup-downa');
						}
					});
				}
			},
			'.down_tabin a' : {
				click : function(){
					var _this = $(this);
					if( _this.hasClass('active') ) return false;
					$('.down_tabin .button').removeClass('active').removeClass('pri-color').removeClass('pri-border');
					_this.addClass('active').addClass('pri-color').addClass('pri-border');
					config.page.type = $(this).attr('type');
					config.page.isend = false;
					config.page.page = 1;
					$('.list_container').empty();
					common.getPage(config.page,function(data){
						if(data.status == 'ok'){
							config.page.isinit = true;
						}else{
							getpage.nodata();
						}
					});
				}
			},
		}
	}


	var tblist = {
		params: {},
		init: {
			initfun : function(){
				common.goToTop('content'); //回到顶部
				//index.fn.act();
			}
		},
		events:{
			'.focus_me' : {
				click : function(){
					config.page.type = $(this).attr('sort');
					config.page.op = 'findaccer';
					$('.sort_name').css('color','#333333');
					$(this).find('p').css('color','#ed414a');
					window.detail2listobj.destroy();
					window.detail2listobj = new h5Detail2list();
					getpage.reset();
				}
			},
			'#find_search' : {
				click : function(){
					$.popup('.popup-findsearch');
				}
			},
			'#confirm_search' : {
				click : function(){
					var postdata = {
						value : $('#searchfor').val(),
						type : settings.op,
					};
					if( postdata.value == '' ){
						common.alert('请输入内容');
						return false;
					}
					common.Http('POST','json',common.createUrl('pagelist','searchguy'),postdata,function(data){
						if(data.status == 'ok'){
							$.closeModal('.popup-findsearch');
							config.page.isend = true;
							$('.list_container').html(data.data);
						}else{
							$('#searchfor').val('');
							common.alert('没有找到数据');
						}			
					});
				}
			},
			'#find_top' : {
				click : function(){
					$.popup('.popup-findtop');
				}
			},
			'#confirm_findtop' : {
				click : function(){
					var postdata = {
						time : $('#toptime').val()*1,
					};
					if( postdata.time == '' || !common.verify('number','int',postdata.time) ){
						common.alert('请输入正确的整数时间');
						return false;
					}
					if( postdata.time < settings.leasttoptime*1 ){
						common.alert('请输入正确的整数时间');
						return false;
					}					
					var total = postdata.time*settings.findtopserver;
					common.confirm('提示','将扣除您'+total+settings.cname+'，确定要置顶吗？',function(){
						if( task.params.ispaying ) return false;
						task.params.ispaying = true;
						common.Http('POST','json',common.createUrl('ajaxdeal','findtotop'),postdata,function(data){
							if(data.status == 200){
								common._alert(data.res,function(){
									location.href = common.createUrl('find',settings.op);
								})
							}else if( data.status == 210 ){
								guy.fn.modal(data.res,data.obj.totext,function(){
									location.href = data.obj.url;
								});
							}else{
								common._alert(data.res);
							}
							task.params.ispaying = false;		
						});
					})					
				}
			},
			'#gettbtasktotop' : {
				click : function(){
					if( tblist.params.isgettoplist == 1 ){
						$.popup('.popup-needtoplist');
					}else if( tblist.params.isgettoplist == 2 ){
						common._alert('您没有可置顶的任务');
					}else{
						common.Http('POST','json',common.createUrl('pagelist','gettbtasktotop'),{},function(data){
							if( data.status == 'ok' ){
								tblist.params.isgettoplist = 1;
								$('.needtoplist_box').html(data.data);
								$.popup('.popup-needtoplist');
							}else{
								tblist.params.isgettoplist = 2;
								common._alert('您没有可置顶的任务');
							}
						});
					}
				}
			},
			'.addtoptime' : {
				click : function(){
					var _this = $(this);
					var tskid = _this.attr('taskid');
					var cantoptime = _this.parent().find('.add_toptime').text();
					if( cantoptime*1 <= 0 ){
			      		common.alert('此任务不能再追加置顶');
			      		return false;
					}
			      	$.prompt('请填入置顶的时间，此任务还可增加置顶'+cantoptime+'小时', function (value) {
				      	var postdata = {
				      		taskid : tskid,
				      		value : value,
				      	}
				      	if( !common.verify('number','int',value) || value <= 0 ){
				      		common.alert('请输入正整数值');
				      		return false;
				      	}
				      	if( value*1 > cantoptime*1 ){
				      		common.alert('最大填入'+cantoptime);
				      		return false;
				      	}				      	
				      	var server = value*settings.tbtopserver;
						common.confirm('提示','将扣除您'+server+settings.cname+',确定要置顶吗？',function(){
							if( task.params.ispaying ) return false;
							task.params.ispaying = true;
							common.Http('post','json',common.createUrl('ajaxdeal','subaddtasktoptime'),postdata,function(data){
								if(data.status == 200){
									var nowlast = _this.parent().find('.last_toptime').text()*1;
									var nowtimes = _this.parent().find('.add_toptime').text()*1;
									_this.parent().find('.last_toptime').text( nowlast+value*1 );
									_this.parent().find('.add_toptime').text( nowtimes-value*1 );
									common._alert(data.res);
								}else if( data.status == 210 ){
									config.model('提示',data.res,'去充值',function(){
										window.location.href = data.obj.url;
									});
								}else{
									common._alert(data.res);
								}
								task.params.ispaying = false;
							});
						})
			      	});
				}
			}
		},
		fn : {}
	};

	var privatetask = {
		init : {
			initTimeDesc : function(){ //倒计时
				common.updateTime();
				common.uploadImageByWxJs('.uploader_input_sub',9,'images');
				common.viewImages();
			},
		},
		events : {
			'#workertaketask' : {
				click : function(){
					privatetask.fn.dealfunc('workertaketask','确定要接受此任务吗？',common.createUrl('ajaxdeal','workertaketask'),'您已接受此任务，请尽快完成');
				}
			},
			'#workerrefusetask' : {
				click : function(){
					privatetask.fn.dealfunc('workerrefusetask','确定要拒绝此任务吗？',common.createUrl('ajaxdeal','workerrefusetask'),'您已拒绝了此任务');
				}
			},			
			'#paythetaskmoney' : { //支付赏金让对方执行任务
				click : function(){
					var postdata = privatetask.fn.getData();
					common.confirm('提示','确定支付让对方执行任务吗？您需支付'+postdata.total+'，其中服务费'+postdata.server+' ',function(){
						common.Http('POST','json',common.createUrl('ajaxdeal','paytaskmoney'),postdata,function(data){
							if( data.status == 200 ){
								common._alert('您已支付，等待对方执行任务',function(){
									location.href = "";
								});
							}else if( data.status == 210 ){
								common.confirm('提示','您的'+settings.cname+'不足，点击确定去充值，点击取消停留在当前页面',function(){
									location.href = common.createUrl('money','in');
								});
							}else{
								common._alert(data.res);
							}
						});
					});
				}
			},
			'#refusegeivetask' : { //拒绝支付任务
				click : function(){
					privatetask.fn.dealfunc('refusegeivetask','确定要拒绝此任务吗？',common.createUrl('ajaxdeal','refusegeivetask'),'拒绝成功');
				}
			},
			'#completethetask' : { //完成任务
				click : function(){
					$.popup('.popup-privatetask-com');
				}
			},
			'#confirmcomplete' : {  //雇员提交完成任务
				click : function(){
					privatetask.fn.dealfunc('confirmcomplete','确定要提交完成吗？',common.createUrl('ajaxdeal','completetask'),'任务已提交完成');
				}
			},
			'#cancelthetask' : { //雇员主动取消任务
				click : function(){
					privatetask.fn.dealfunc('cancelthetask','确定要取消任务吗？',common.createUrl('ajaxdeal','canceltask'),'任务已取消了');
				}
			},
			'#confirmtaskresult' : { //雇主确认完成任务
				click : function(){
					privatetask.fn.dealfunc('completethetask','确定要完成任务吗？确定后将为对方发放任务收益。',common.createUrl('ajaxdeal','confirmtask'),'任务已完成');
				}
			},
			'#refusetaskresult' : { //雇主拒绝任务结果呼出上拉框
				click : function(){
					$.popup('.popup-privatetask-refuse');
				}
			},
			'#confirmrefuse' : { //确定拒绝
				click : function(){
					privatetask.fn.dealfunc('confirmrefuse','确定要拒绝任务结果吗？若虚假拒绝会被受到最严重封号处罚！',common.createUrl('ajaxdeal','confirmrefuse'),'任务结果已被拒绝，请等待对方确认。');
				}
			},
			'#acceptrefuse' : {  //雇员接受雇主对结果的拒绝
				click : function(){
					privatetask.fn.dealfunc('acceptrefuse','确定要接受对方的拒绝吗？',common.createUrl('ajaxdeal','acceptrefuse'),'已经接受对方的拒绝，任务已结束。');
				}
			},
			'#complainboss' : { // 投诉对方
				click : function(){
					$.popup('.popup-privatetask-explain');
				}
			},
			'#confirmcomplain' : {
				click : function(){
					privatetask.fn.dealfunc('confirmcomplain','确定要投诉对方的拒绝吗？',common.createUrl('ajaxdeal','omplainboss'),'已经投诉成功，请等待客服处理。');					
				}
			},
			'.guy_contact_wx' : {
				click : function(){
					guy.fn.showdia( $('#contact_me') );
				}
			},
		},
		fn : {
			getData : function(){
				var first = settings.taskmoney*settings.priserver/100; // 服务费
				var server = Math.max.apply(Math,[first,settings.prileast]);
				var data = {
					server : server,
					total : server + settings.taskmoney*1,
					taskid : settings.taskid,
					refusereason : $('textarea[name=refusereasona]').val(),
					explainreason : $('textarea[name=refusereasonb]').val(),
					completecontent : $('textarea[name="completecontenta"]').val(),
					images : $('input[name="images[]"]').arrval(),
				};
				return data;
			},
			dealfunc : function(from,notice,url,resultstr){
				var postdata = privatetask.fn.getData();
				
				if(( postdata.refusereason == '' && from == 'confirmrefuse') || ( from == 'confirmcomplain' && postdata.explainreason == '' ) ){
					common.alert('请输入理由');return false;
				}
				common.confirm('提示',notice,function(){
					common.Http('POST','json',url,postdata,function(data){
						if(data.status == 200) {
							common._alert(resultstr,function(){
								location.href = common.createUrl('privatetask',settings.op,{id:settings.id});
							});
						}else{
							common._alert(data.res);
						}
					});
				});					
			}
		}
	};

	var usetask = {
		params : {
			page : 1,
			op : 'getusetask',
		},
	};

	var usetaskinfo = {
		init : {
			initfun : function(){
				common.goToTop('content'); //回到顶部
				//common.chageTitle(settings.title);
				common.uploadImageByWxJs('.uploader_reply',9,'images');
				common.uploadImageByWxJs('.uploader_complain',9,'complainimages');
				common.uploadImageByWxJs('.uploader_take',9,'takeimg');
				common.viewImages();
				common.init(taskmessage);
				common.updateTime();
				
				usetaskinfo.fn.copyit();
				usetaskinfo.fn.copyita();
			}
		},
		params : {
			page : 1,
			op : 'getusetaskreply',
			taskid : settings.taskid,
		},
		events : {
			'.guy_contact_wx' : {
				click : function(){
					guy.fn.showdia( $('#contact_me') );
				}
			},
			'#take_btn' : { // 申请试用
				click : function(){
					if( settings.isform == 1 ){
						$.popup('.popup-takeuse');return false;
					}
					usetaskinfo.fn.applytask();
				}
			},
			'#takeuse_confirm' : {
				click : function(){
					usetaskinfo.fn.applytask();
				}
			},
			'#cancel_taked' : {
				click : function(){
					var postdata = {
						taskid : settings.taskid,
					}
					common.confirm('提示','确定放弃此任务吗？',function(){
						common.Http('POST','json',common.createUrl('ajaxdeal','cancelusetask'),postdata,function(data){
							if( data.status == 200 ){
								common._alert('已放弃',function(){
									location.href = data.obj.url;
								});	
							}else{
								common._alert(data.res);
							}
						});
						
					});
				}
			},
			'.passor' : {
				click : function(){
					var thiselem = $(this);
					var postdata = {
						reid : thiselem.attr('reid'),
						type : thiselem.attr('type'),
					}
					var strr = '确定要通过对方的申请吗？';
					var strr2 = '已通过';
					if( postdata.type == 2 ) {
						strr = '确定要拒绝对方的申请吗？';
						strr2 = '已拒绝';
					}
					
					common.confirm('提示',strr,function(){
						common.Http('POST','json',common.createUrl('ajaxdeal','passorapply'),postdata,function(data){
							if( data.status == 200 ){
								common._alert(strr2,function(){
									var html = '<div class="task_replay_bottom"><span class="task_replay_status">等待提交订单</span></div>';
									if( postdata.type == 2 ){
										html = '<div class="task_replay_bottom"><span class="task_replay_status font_ff5f27">已拒绝</span></div>';
									}
									thiselem.parents('.task_reply_in').append(html);
									thiselem.parents('.task_replay_bottom').remove();
									
									//location.href = data.obj.url;
								});
							}else{
								common._alert(data.res);
							}
						});
						
					});
				}
			},
			'.fail' : { // 失败notice
				click : function(){
					var thiselem = $(this);
					var reid = thiselem.attr('reid');
					$.prompt('请填入失败原因', function (value) {
						var postdata = {
							reid : reid,
							value : value,
						}
						if( usetaskinfo.params.ispaying ) return false;
						usetaskinfo.params.ispaying = true;
						common.Http('post','json',common.createUrl('ajaxdeal','failusetask'),postdata,function(data){
							if(data.status == 200){
								var html = '<div class="puber_deal_btn tosuccess" reid="'+reid+'" type="2">转为完成</div>';
								thiselem.parent().append(html);
								thiselem.prev().remove();
								thiselem.prev().remove();
								thiselem.remove();
								common._alert('设置成功',function(){});
							}else{
								common._alert(data.res);
							}
							usetaskinfo.params.ispaying = false;
						});
					});
				}
			},
			'.notice' : { // 提醒对方
				click : function(){
					var thiselem = $(this);
					var reid = thiselem.attr('reid');
					$.prompt('请填入提醒内容', function (value) {
						var postdata = {
							reid : reid,
							value : value,
						}
						common.confirm('提示','提醒是告诉对方纠正任务内容，每10分钟只能提醒对方一次，确定要提醒对方吗？',function(){
							if( usetaskinfo.params.ispaying ) return false;
							usetaskinfo.params.ispaying = true;
							common.Http('post','json',common.createUrl('ajaxdeal','noticeusetask'),postdata,function(data){
								if(data.status == 200){
									common._alert('已提醒对方',function(){});
								}else{
									common._alert(data.res);
								}
								usetaskinfo.params.ispaying = false;
							});
						})
					});
				}
			},			
			'.success,.tosuccess' : { // 完成
				click : function(){
					var thiselem = $(this);
					var postdata = {
						reid : thiselem.attr('reid'),
						type : thiselem.attr('type'),
					}
					common.confirm('提示','确定要将此任务设为完成吗？',function(){
						common.Http('POST','json',common.createUrl('ajaxdeal','successusetask'),postdata,function(data){
							if( data.status == 200 ){
								common._alert('已设为完成',function(){
									var html = '<div class="task_replay_bottom"><span class="task_replay_status">已完成</span></div>';
									
									thiselem.parents('.task_reply_in').append(html);
									thiselem.parents('.task_replay_bottom').remove();
									
									//location.href = data.obj.url;
								});
							}else{
								common._alert(data.res);
							}
						});
						
					});
				}
			},					
			'#sub_order,#add_content' : {
				click : function(){
					usetaskinfo.params.type = $(this).attr('type');
					$.popup('.popup-reply');
				}
			},
			'#reply_confirm' : { // 回复内容,补充内容
				click : function(){
					var postdata = {
						taskid : settings.taskid,
						type : usetaskinfo.params.type,
						images : $('input[name="images[]"]').arrval(),
						content : $('textarea[name="content"]').val(),
					}
					var strr = '提交后无法修改，确定提交吗？';
					if( postdata.type == 'add' ){
						strr = '提交后无法修改，最多能补充3次内容。确定提交吗？';
					}
					common.confirm('提示','提交后无法修改，确定提交吗？',function(){
						if( usetaskinfo.params.ispaying ) return false;
						usetaskinfo.params.ispaying = true;
						common.Http('post','json',common.createUrl('ajaxdeal','replyusetask'),postdata,function(data){
							if(data.status == 200){
								common._alert(data.res,function(){
									location.href = data.obj.url;
								});
							}else{
								common._alert(data.res);
							}
							usetaskinfo.params.ispaying = false;
						});
					})
				}
			},
			'.task_reply_top' : {
				click : function(){
					$.popup('.popup-filter-usetaskreplay');
				}
			},
			'.filter_replay' : { // 筛选任务
				click : function(){
					usetaskinfo.fn.resetPage();
					usetaskinfo.params.status = $(this).attr('type');

					var name = $(this).attr('name');
					$.closeModal('.popup-filter-usetaskreplay');
					$('.task_reply_topl').text( name );
					common.getPage(usetaskinfo.params,function(data){
						if(data.status == 'ok'){
							usetaskinfo.params.isinit = true;
						}else{
							getpage.nodata();
						}
					});
				}
			},
			'#count_usetask' : { // 结算
				click : function(){
					common.confirm('提示','请确定所有申请都处理完了再结算，确定要结算任务吗？',function(){
						if( usetaskinfo.params.ispaying ) return false;
						usetaskinfo.params.ispaying = true;
						common.Http('post','json',common.createUrl('ajaxdeal','countusetask'),{taskid:settings.taskid},function(data){
							if(data.status == 200){
								common._alert(data.res,function(){
									location.href = data.obj.url;
								});
							}else{
								common._alert(data.res);
							}
							usetaskinfo.params.ispaying = false;
						});
					});
				}
			},
			'#go_buy' : {
				click : function(){
					if( settings.findtype == 0 ){
						if( settings.gotype == 0 || settings.taokey == null || settings.taokey == '' ){
							location.href = common.createUrl('taob','usetask',{url : settings.taourl});
						}else{
							$('#tao_box').show();
						}
					}else if( settings.findtype == 1 ){
						$('#taokey_box').show();
					}else if( settings.findtype == 2 ){
						common._alert( '请根据雇主备注内容提示下单' );
					}
				}
			},
			'.close_tao' : {
				click : function(){
					$(this).parents('.tao_box').hide();
					$('#copy_it').css({'background':'#ed414a'}).text('一键复制');
				}
			},

		},
		fn : {
			resetPage : function(){
				$('.list_container').empty();
				usetaskinfo.params.taskid = settings.taskid;
				usetaskinfo.params.isinit = false;
				usetaskinfo.params.isend = false;
				usetaskinfo.params.page = 1;
			},
			copyita : function(){
				var clipboard = new Clipboard('.taokey_item');
				
				clipboard.on('success', function(e) {
					common.alert('已复制成功,请打开手机淘宝');
				});

				clipboard.on('error', function(e) {
					common.alert('复制失败,手机不支持');
				    console.error('Action:', e.action);
				    console.error('Trigger:', e.trigger);
				});

				
			},
			copyit : function(down){
				if( settings.gotype == 0 || settings.taokey == null || settings.taokey == '' ) return;
				var clipboard = new Clipboard('#copy_it');

				clipboard.on('success', function(e) {
				    //console.info('Action:', e.action);
				    //console.info('Text:', e.text);
				    //console.info('Trigger:', e.trigger);
				    console.log(11);
			        e.trigger.innerHTML = down ? "已复制成功" : "已复制成功，打开手机淘宝";
			        e.trigger.style.backgroundColor="#9ED29E";
				    e.clearSelection();
				});

				clipboard.on('error', function(e) {
			      	e.trigger.innerHTML="复制失败,请手动复制";
			      	e.trigger.style.backgroundColor="#666";
				    console.error('Action:', e.action);
				    console.error('Trigger:', e.trigger);
				});

				var isapple = usetaskinfo.fn.isApple();
				if(!isapple){
					$('#android_code').show().prev().hide();
					//$('.popwtitle').html('长按框内口令&gt;全选&gt;复制');
				}else{
					usetaskinfo.fn.select('copybox');
				}

			},
			isApple : function(){
				var ua = navigator.userAgent.toLowerCase();
				if (ua.match(/iphone/i) == "iphone" || ua.match(/ipad/i) == "ipad"){
					return true;
				}
				return false;
			},
			select : function(classname){
				//return false;
		        document.addEventListener("selectionchange",
		        function(e) {
		            if (window.getSelection().anchorNode.parentNode.id == 'iphone_code' && document.getElementById('iphone_code').innerText != window.getSelection()) {
		              var key = document.getElementById('iphone_code');
		              window.getSelection().selectAllChildren(key);
		            }
		        },false);
			},
			applytask : function(){
				var postdata = {
					taskid : settings.taskid,
					content : $('textarea[name="takeuse"]').val(),
					images : $('input[name="takeimg[]"]').arrval(),
				}
				common.confirm('提示','确定申请此任务吗？',function(){
					common.Http('POST','json',common.createUrl('ajaxdeal','applyusetask'),postdata,function(data){
						if( data.status == 200 || data.status == 220 ){
							common._alert(data.res,function(){
								location.href = data.obj.url;
							});	
						}else{
							common._alert(data.res);
						}
					});
					
				});
			},
		}		
	};

	var mypub = {
		init: {
			initfun : function(){
				common.goToTop('content'); //回到顶部
			}
		},
		events:{
			'#filter' : {
				click : function(){
					$.popup('.popup-filter-mypubfilter');
				}
			},
			'.filter_replay' : {
				click : function(){
					config.page.pid = $(this).attr('type');
					$('.popup_filter_btn.pri-color').removeClass('pri-color');
					$(this).addClass('pri-color');
					config.page.sortid = 0;
					window.detail2listobj.destroy();
					window.detail2listobj = new h5Detail2list();
					getpage.reset();
					$.closeModal('.popup-filter-mypubfilter');
				}
			},
			'.filter_replaysort' : { // 筛选回复
				click : function(){
					var sortid = $(this).attr('sortid');
					$('.popup_filter_btn.pri-color').removeClass('pri-color');
					$(this).addClass('pri-color');
					config.page.sortid = sortid;
					config.page.pid = 1;
					window.detail2listobj.destroy();
					window.detail2listobj = new h5Detail2list();
					getpage.reset();
					$.closeModal('.popup-filter-mypubfilter');
				}
			},
			'#confirmsearch' : {
				click : function(){
					var v = $('#search').val();
					if( v == '' || !v ) {
						config.page.search = '';
					}else{
						config.page.search = v;
					}
					window.detail2listobj.destroy();
					window.detail2listobj = new h5Detail2list();
					getpage.reset();
				}
			}
		},
		fn : {}
	};

	var user = {
		init: {
			initfun : function(){
				common.goToTop('content'); //回到顶部
			}
		},
		events:{
			'#showalert' : {
				click : function(){
					common._alert('传说后山有个神秘之地藏了很多宝石，但想寻找到它，可不容易！据说满足了'+settings.groupnum+'人寻找到宝石可能性会更容易些，你是否愿意报名去寻找这批宝石呢？');
				}
			},
			'.anwobxbtn' : {
				click : function(){
					$('.anw_boxbb').show();
				}
			},
			'#closebi' : {
				click : function(){
					$('.anw_boxbb').hide();
				}
			},
			'.getbox' : {
				click : function(){
					var _this = $(this);
					var postdata = {
						bid : $(this).attr('bid'),
					};
					common.Http('POST','json',common.createUrl('ajaxdeal','getbox'),postdata,function(data){
						if(data.status == 200){
							common.alert(data.res);
							_this.parents('.anwbox_item').find('.anwbox_m').text(data.obj.m);
							_this.addClass('disabled').removeClass('getbox').removeClass('pri-bg').text('已领');
						}else{
							common._alert(data.res);
						}
					});
				}
			},
			'.user_update' : {
				click : function(){
					common.Http('POST','json',common.createUrl('ajaxdeal','userupdate'),{},function(data){
						common.alert(data.res);
					});
				}
			},
			'#getanwback' : {
				click : function(){
					var _this = $(this);
					common.Http('POST','json',common.createUrl('ajaxdeal','getanwback'),{},function(data){
						if(data.status == 200){
							common.alert('已领取，发放到余额');
						}else{
							common._alert(data.res);
						}			
					});
				}
			},
			'#pin' : {
				click :function(){
					common.confirm('提示','参与报名需要消耗'+settings.groupin+'张银票，一经报名，不可辙销哦',function(){
						common.Http('POST','json',common.createUrl('ajaxdeal','joinpin'),{},function(data){
							if(data.status == 200){
								common.alert('已参与');
								var num = $('#groupnum').text()*1;
								$('#groupnum').text(num+1);
							}else{
								common._alert(data.res);
							}
						});
					})
				}
			},
			'.getbaoshi' : {
				click :function(){

					$.prompt('请填入兑换的宝石数量' + (settings.minbs > 0 ? '('+settings.minbs+'个宝石的整倍数)' : '') , function (value) {
						var postdata = {
							baoshi : value,
						}
						if( value*1 == '' || value*1 < 0.01 ) {
							common.alert('请输入宝石数量');return false;
						}
						if( !common.verify('number','int',value) ){
							common.alert('请填写正整数');return false;
						}

						common.confirm('提示','兑换将扣除'+value+'宝石，兑换的奖励将根据实时的数据计算，因此，存在很多不确定因素，您现在是否需要兑换？',function(){
							common.Http('POST','json',common.createUrl('ajaxdeal','getbaoshi'),postdata,function(data){
								if(data.status == 200){
									common._alert(data.res,function(){
										location.href = "";
									});
								}else{
									common._alert(data.res);
								}
							});
						})
					});
				}
			},
			'.signbtn' : {
				click :function(){
					$('.sign_box').show();
				}
			},
			'.sign_box .mask' : {
				click : function(){
					$('.sign_box').hide();
				}
			},
			'#todaysign' : {
				click : function(){
					var _this = $(this);
					common.Http('POST','json',common.createUrl('ajaxdeal','todaysign'),{},function(data){
						if(data.status == 200){
							_this.text('已签到').addClass('signed');
							common._alert(data.res,function(){
								location.href = "";
							});
						}else{
							common._alert(data.res);
						}
					});
				}
			}
		},
		fn : {}
	};

	var activity = {
		init: {
			initfun : function(){
				
			}
		},
		events:{
			'#confirm_addact' : {
				click : function(){
					var postdata = {
						act : $('input[name="act"]').val(),
					};
					common.confirm('提示','确定兑换'+postdata.act+'活跃度吗？',function(){
						common.Http('POST','json',common.createUrl('ajaxdeal','addactivity'),postdata,function(data){
							if(data.status == 200){
								common.alert(data.res);
								var now = $('.nowactivity').text() * 1;
								$('.nowactivity').text( now+postdata.act*1 );
							}else if( data.status == 210 ){
							    $.modal({
							      title:  '提示',
							      text: data.res,
							      buttons: [
							        {
							          text: '关闭',
							          onClick: function() {
							            
							          }
							        },
							        {
							          text: '去充值',
							          onClick: function() {
							            location.href = data.obj.url;
							          }
							        },
							      ]
							    });
							}else{
								common._alert(data.res);
							}			
						});
					})
				}
			},
		}
	};

	$(document).on("pageInit", "#page_down", function(e, id, page) {
		common.init(down);
		getpage.init();
		$.initInfiniteScroll2();
		$(page).on('infinite', function() {
			common.getPage(config.page,function(data){});	
		});
	});
	$(document).on("pageInit", "#page_level", function(e, id, page) {
		common.init(level);
	});

	$(document).on("pageInit", "#page_usetask", function(e, id, page) {
		common.init(usetask);
		if(!usetask.params.isinit){
			common.getPage(usetask.params,function(data){
				if(data.status == 'ok'){
					usetask.params.isinit = true;
				}
			});
		}
		$.initInfiniteScroll2();
		$(page).on('infinite', function() {
			common.getPage(usetask.params,function(data){});	
		});
	});

	$(document).on("pageInit", "#page_privatetask", function(e, id, page) {
		common.init(privatetask);
	});	

	$(document).on("pageInit", "#page_privatelist", function(e, id, page) {
		getpage.init();
		$.initInfiniteScroll2();
		$(page).on('infinite', function() {
			common.getPage(config.page,function(data){});	
		});
	});	
	$(document).on("pageInit", "#page_pub", function(e, id, page) {
		common.init(pub);
	});	
	$(document).on("pageInit", "#page_pubtb", function(e, id, page) {
		common.init(pubtb);
	});
	$(document).on("pageInit", "#page_tblist", function(e, id, page) {
		common.init(tblist);
		getpage.init();
		$.initInfiniteScroll2();
		$(page).on('infinite', function() {
			common.getPage(config.page,function(data){});	
		});
	});	

	$(document).on("pageInit", "#page_tbtask", function(e, id, page) {
		common.init(tbtask);
		getpage.init();
		$.initInfiniteScroll2();
		$(page).on('infinite', function() {
			common.getPage(config.page,function(data){});
		});
	});

	$(document).on("pageInit", "#page_regist", function(e, id, page) {
		common.init(regist);
	});
	$(document).on("pageInit", "#page_login", function(e, id, page) {
		common.init(login);
	});				
	$(document).on("pageInit", "#page_set", function(e, id, page) {
		common.init(set);
	});
	$(document).on("pageInit", "#page_activity", function(e, id, page) {
		common.init(activity);
	});
	$(document).on("pageInit", "#page_guy", function(e, id, page) {
		common.init(guy);
	});	
	$(document).on("pageInit", "#page_task", function(e, id, page) {
		common.init(task);
		//task.fn.initpage();
		getpage.init();
		$.initInfiniteScroll2();
		$(page).on('infinite', function() {
			common.getPage(config.page,function(data){});
		});
	});	
	
	$(document).on("pageInit", "#page_find", function(e, id, page) {
		common.init(find);
		getpage.init();
		$.initInfiniteScroll2();
		$(page).on('infinite', function() {
			common.getPage(config.page,function(data){});	
		});
	});		
	$(document).on("pageInit", "#page_mypub", function(e, id, page) {
		common.init(mypub);
		
		getpage.init();
		$.initInfiniteScroll2();
		$(page).on('infinite', function() {
			common.getPage(config.page,function(data){});	
		});
	});	
	//confirm页面
	$(document).on("pageInit", "#page_money", function(e, id, page) {
		common.init(money);
		getpage.init();
		$.initInfiniteScroll2();
		$(page).on('infinite', function() {
			common.getPage(config.page,function(data){});	
		});
	});		
	
	//index页面
	$(document).on("pageInit", "#page_index", function(e, id, page) {
		common.init(index);

		if( settings.itlist == 0 || !settings.itlist ){
			getpage.init();
			$.initInfiniteScroll2();
			$(page).on('infinite', function() {
				common.getPage(config.page,function(data){});
			});
		}
	});

	//index页面
	$(document).on("pageInit", "#page_anw", function(e, id, page) {

		getpage.init();
		$.initInfiniteScroll2();
		$(page).on('infinite', function() {
			common.getPage(config.page,function(data){});
		});
	});	

	$(document).on("pageInit", "#page_user", function(e, id, page) {
		common.init(user);

		$('#loginout').click(function(){
			common.Http('POST','json',common.createUrl('ajaxdeal','loginout'),{},function(data){
				if( data.status == 200 || data.status == 220 ){
					common._alert(data.res,function(){
						location.href = data.obj.url;
					});
				}else{
					common._alert(data.res);
				}
			});
		});
	});
	
	//index页面
	$(document).on("pageInit", "#page_usetaskinfo", function(e, id, page) {
		
		common.init(usetaskinfo);
		usetaskinfo.fn.resetPage();

		if( settings.scroll == 1 ){
			if(!usetaskinfo.params.isinit){
				common.getPage(usetaskinfo.params,function(data){
					if(data.status == 'ok'){
						usetaskinfo.params.isinit = true;
					}else{
						getpage.nodata();
					}
				});
			}
			$.initInfiniteScroll2();
			$(page).on('infinite', function() {
				common.getPage(usetaskinfo.params,function(data){});	
			});
		}
		
	});	
	


	$.init();
	FastClick.attach(document.body);
	common.init(regist);

	$('body').off('click','.nav_pub,.pubtask').on('click','.nav_pub,.pubtask',function(){
		$.popup('.popup-filter-pub');
	});
	$('body').off('click','.top_left').on('click','.top_left',function(){
		if( document.referrer ){
			window.history.go(-1);
		}else{
			var url = common.createUrl('index','index');
			location.href = url;
		}
		
	});
	$('body').off('click','.sub_notice,.sub_click').on('click','.sub_notice,.sub_click',function(){
		var elem = $('#sub_me');
		elem.show();
		elem.find('.atom-dialog').css({'top':'-100%'}).animate({
			'top' : '15%'
		},500,'ease',function(){
			elem.find('.atom-dialog').addClass('elastic');
		});
	});
	// 客服 show_kefuimg
	$('body').off('click','#show_kefuimg').on('click','#show_kefuimg',function(){
		var elem = $('#kefu_qrcode');
		elem.show();
		elem.find('.atom-dialog').css({'top':'-100%'}).animate({
			'top' : '15%'
		},500,'ease',function(){
			elem.find('.atom-dialog').addClass('elastic');
		});
	});

	$('body').off('click','.close_sub').on('click','.close_sub',function(){
		$('.sub_item').hide();
	});
	$('body').off('click','.close_token').on('click','.close_token',function(){
		var elem = $(this).parents('.atom-dialog');
		elem.animate({
			'top' : '-70%'
		},500,'ease',function(){
			elem.parents('.atom-dialog-wrap').hide();
			elem.removeClass('elastic');
		});
	});
	
	$('body').on('click','.form_group .quest',function(){
		var cc = $(this).attr('content');
		if(cc && cc != '') common._alert(cc);
	});

	if( settings.dev != 'wap' ){
		wx.miniProgram.postMessage({ data: {url:settings.sharelink} });
	}

	var agent = common.agent();
	if( agent == 'wxapp' ) {
		var arg = common.getQuery( location.href );
		if(arg.appacid){
			common.cookie.set('appacidasd',arg.appacid,99999);
		}
	}else{
		common.cookie.set('appacidasd','',-999);
	}

	if( settings.do == 'user' || settings.do == 'pub' ){
		common.Http('post','json',common.createUrl('ajaxdeal','queue'),{},function(data){},'',true);
	}

	if( $('.setnick_headbtn').length > 0 ) common.uploadImageByWxJs('.setnick_headbtn',1,'headimg','replace');
	$('#confirm_setnick').click(function(){
		var postdata = {
			nick : $('input[name="nick"]').val(),
			headimg : $('input[name="headimg[]"]').val(),
		};
		common.Http('post','json',common.createUrl('ajaxdeal','setnick'),postdata,function(data){
			if( data.status == 200 ){
				$('.setnick_box').hide();
			}
			common._alert(data.res);
		},'',true);
	});
	

});
