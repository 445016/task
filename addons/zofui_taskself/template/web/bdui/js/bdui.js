$(function(){



/*******************/
	myCommon();
	function myCommon(){

		$('.edit_bot').click(function(){
			$('.ui-table-editor').hide();
			$(this).next().show();
		});

		$('.ui-table-editor-cancel').click(function(){
			$('.ui-table-editor').hide();
		});

		$('body').on('click','.ui-select-out .ui-select',function(){

			var _this = $(this);
			if( _this.hasClass('ui-select-active') ){

				_this.removeClass('ui-select-active');
				_this.next().hide();
			}else{
				$('.ui-select-out .ui-select').each(function(){
					$(this).removeClass('ui-select-active');
					$(this).next().hide();
				});

				_this.addClass('ui-select-active');
				_this.next().show();
			}

		});

		$('body').on('click','.ui-select-out .ui-select-item',function(){
			var _this = $(this);
			var v = $.trim( _this.attr('data-value') );
			var name = $.trim( _this.find('span').text() );
			_this.parents('.ui-select-out').find('input.ui-select-input').val( v );
			if( name != '' && typeof name != 'undefined' ) _this.parents('.ui-select-out').find('.ui-select-text').text( name );
			
			_this.siblings().removeClass('ui-select-item-selected');
			_this.addClass('ui-select-item-selected');

			$('.ui-select-out .ui-select').each(function(){
				$(this).removeClass('ui-select-active');
				$(this).next().hide();
			});
		});


		$('[data-toggle="modal"]').click(function(){
			var tar = $(this).attr('data-target');
			$('.modal[id="'+tar+'"]').show();
		});
		$('[data-dismiss="modal"]').click(function(){
			$(this).parents('.form_model').hide();
		});


		$('.header-select').mouseenter(function(){
	  		$('#bce-content .header').css('z-index','12');
		});
		$('.header-select').mouseleave(function(){
	  		$('#bce-content .header').css('z-index','5');
		});	

		$('#main-sidebar .sidebar-item.showlist').mouseenter(function(){
	  		$(this).find('.service-group-list').css('width','180px').find('.service_group_list_wrap').css('width','180px');
		});
		$('#main-sidebar .sidebar-item.showlist').mouseleave(function(){
	  		$(this).find('.service-group-list').css('width','0px').find('.service_group_list_wrap').css('width','0px');
		});		

		// 全选
		$('.ui-table .ui-table-select-all').change(function(e){
			var _this = $(this);

			if( _this.prop("checked") ){
				_this.parents('.ui-table').find('.ui-table-multi-select').each(function(){
					$(this).prop("checked",true);
				})
				
			}else{
				_this.parents('.ui-table').find('.ui-table-multi-select').each(function(){
					$(this).prop("checked",false);
				})
			}
			e.preventDefault();
		})

		// table 下拉
		$('.hcell-filter').click(function(){
			$(this).find('.ui-table-filter-select').css('visibility','visible');
		});

		$('.hcell-filter').mouseleave(function(){
			$(this).find('.ui-table-filter-select').css('visibility','hidden');
		});		

	}

	function myAjax(type,datatype,op,data,successCall,isloading,beforeCall,comCall){
		myHttp(type,datatype,'home','ajax',op,data,successCall,isloading,beforeCall,comCall);
	}

	function appAjax(type,datatype,op,data,successCall,isloading,beforeCall,comCall){
		appHttp(type,datatype,'home','ajax',op,data,successCall,isloading,beforeCall,comCall);
	}

	//http请求
	function myHttp(type,datatype,c,a,op,data,successCall,isloading,beforeCall,comCall){
		$.ajax({
			type: type,
			url: myUrl(c,a,op),
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

	function myUrl(c,a,op){
		return window.sysinfo.siteroot + 'web/index.php?c='+c+'&a='+a+'&op='+op;
	};


	//http请求
	function appHttp(type,datatype,c,a,op,data,successCall,isloading,beforeCall,comCall){
		$.ajax({
			type: type,
			url: appUrl(c,a,op),
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
	function appUrl(c,a,op){
		return window.sysinfo.siteroot + 'app/index.php?c='+c+'&a='+a+'&op='+op;
	};	

	function loading(){
		var html = 
			'<div id="loading" class="loading" style="z-index:52111;position:relative">'+
			'<div class="load_mask"></div>'+
			'<div class="modal-loading">'+
			'	<div class="modal-loading-in">'+
			'		<img style="width:48px; height:48px;padding-top:20px;" src="../web/resource/images/loading.gif"><p>处理中</p>'+
			'	</div>'+
			'</div>'+
			'</div>';
		$(document.body).append(html);
	};
	
	function loaded(){
		$('.loading').remove();
	};

});

