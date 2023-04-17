

	var common = {};
	common.params = {
		scrolltotop : '' //显示隐藏sheet用的滚动条高度
	};


	common.getQuery = function(url) {
		var theRequest = {};
		if (url.indexOf("?") != -1) {
			var str = url.split('?')[1];
			var strs = str.split("&");
			for (var i = 0; i < strs.length; i++) {
				if (strs[i].split("=")[0] && unescape(strs[i].split("=")[1])) {
					theRequest[strs[i].split("=")[0]] = unescape(strs[i].split("=")[1]);
				}
			}
		}
		return theRequest;
	}

	common.agent = function(){
        var ua = window.navigator.userAgent.toLowerCase();
        if (ua.match(/microMessenger/i) == 'micromessenger') { // 微信浏览器
            if(window.__wxjs_environment === 'miniprogram'){ // 小程序
                return 'wxapp';
            }else{
                return 'wx';
            }
        }else{
            return 'wap';
        }
	}

	//警告框
	common.alert = function(message){
		$.toast(message);
	};	
	common._alert = function(message,call){
		$.alert(message,call);
	};

    common.showIndicator = function () {
        if ($('.my-preloader-indicator-modal')[0]) return;
        $('body').append('<div class="my-preloader-indicator-overlay"></div><div class="my-preloader-indicator-modal"><span class="my-preloader my-preloader-white"></span></div>');
    };
    common.hideIndicator = function () {
        $('.my-preloader-indicator-overlay, .my-preloader-indicator-modal').remove();
    };


	/*
		使用微信jssdk上传图片依赖以下html
		<div class="upload_images_wxjs">
			<div class="upload_images_views">
			</div>
			<span class="upload_btn">+</span>
		</div>
		num是限定的图片数量。
	*/
	/*common.uploadImageByWxJs = function(elem,num,name,type){
		$('body').off('click',elem).on('click',elem,function(){
			var elemt = $(this).parent();
			var nownumber = 0;
			if( type != 'replace' ){
				var nownumber = elemt.find('.upload_images_views img').length*1;		
				if(nownumber >=num){
					common._alert('图片已达最大数量,点击图片可删除部分图片');return false;
				}
			}

			if( sysinfo.siteroot == 'http://127.0.0.5/' ){
				var imgstr = '';
				for (var i = 0; i < num; i++) {
					imgstr += '<li class="fl upload_image_item"><img src="'+sysinfo.siteroot+'addons/zofui_taskself/public/images/1.jpg"><input value="'+sysinfo.siteroot+'addons/zofui_taskself/public/images/1.jpg" type="hidden" name="'+name+'[]"></li>';
				}
				if( type == 'replace' ){
					elemt.find('.upload_images_views').html(imgstr);
				}else{
					elemt.find('.upload_images_views').append(imgstr);
				}
			}else{
				wxJs.chooseImage(num-nownumber,function(data){
					var imgstr = '';
					for(var i= 0;i<data.length;i++){
						imgstr += '<li class="fl upload_image_item"><img src="'+data[i][0]+'"><input value="'+data[i][1]+'" type="hidden" name="'+name+'[]"></li>';
					}
					if( type == 'replace' ){
						elemt.find('.upload_images_views').html(imgstr);
					}else{
						elemt.find('.upload_images_views').append(imgstr);
					}
				});
			}

		});
		$('body').off('click','.upload_image_item').on('click','.upload_image_item',function(){
			var _this = $(this);
			common.confirm('提示','确定要删除此图片吗？',function(){
				_this.remove();
			});
		});

	};*/
	var isbind = false;
	common.uploadImageByWxJs = function(elem,num,name,type,box){

		if( typeof settings.dev == 'undefined' || settings.dev == 'wx' ){
			$('body').off('click',elem).on('click',elem,function(){

				var elemt = $(this).parent();
				nownumber = 0;
				if( type != 'replace' ){
					var nownumber = elemt.find('.upload_images_views img').length*1;		
					if(nownumber >=num){
						common._alert('图片已达最大数量,点击图片可删除部分图片');return false;
					}
				}

				if( sysinfo.siteroot == 'http://127.0.0.4/' ){
					var imgstr = '';
					for (var i = 0; i < num; i++) {
						imgstr += '<li class="fl upload_image_item"><img src="'+sysinfo.siteroot+'addons/zofui_tasktb/public/images/3.jpg"><input value="'+sysinfo.siteroot+'addons/zofui_tasktb/public/images/3.jpg" type="hidden" name="'+name+'[]"></li>';
					}
									
					if( type == 'replace' ){
						elemt.find('.upload_images_views').html(imgstr);
					}else{
						elemt.find('.upload_images_views').append(imgstr);
					}
				}else{
					wxJs.chooseImage(num-nownumber,function(data){
						var imgstr = '';
						for(var i= 0;i<data.length;i++){
							imgstr += '<li class="fl upload_image_item"><img src="'+data[i][0]+'"><input value="'+data[i][1]+'" type="hidden" name="'+name+'[]"></li>';
						}
						if( type == 'replace' ){
							elemt.find('.upload_images_views').html(imgstr);
						}else{
							elemt.find('.upload_images_views').append(imgstr);
						}
					});

				}
			});
			$('body').off('click','.upload_image_item').on('click','.upload_image_item',function(){
				var _this = $(this);
				common.confirm('提示','确定要删除此图片吗？',function(){
					_this.remove();
				});
			});

		}else{
			
			var agent = navigator.userAgent;
			var isAndroid = agent.indexOf("Android") > -1 || agent.indexOf("Linux") > -1;			
			var defaultOptions = {
				pick: {
					id: elem,
					multiple : true,
				},
				auto: true,
				swf: "/web/resource/componets/webuploader/Uploader.swf",
				server: "./index.php?i="+ sysinfo.uniacid+"&j=&c=utility&a=file&do=upload&type=image",
				chunked: false,
				compress: {compressSize: 0},
				fileNumLimit: num,
				duplicate :true,
				fileSizeLimit: 8 * 1024 * 1024,
				fileSingleSizeLimit: 8 * 1024 * 1024,
			    accept: {
			        title: 'Images',
			        extensions: 'gif,jpg,jpeg,bmp,png',
			        mimeTypes: 'image/*'
			    }	
			};
			if (isAndroid) {
				defaultOptions.sendAsBinary = true;
			}

			var options = $.extend({}, defaultOptions);
			var uploader = WebUploader.create(options);

			uploader.on( "fileQueued", function( file ) {
				common.showIndicator();
			});
			uploader.on( 'uploadProgress', function( file, percentage ) {
			    common.showIndicator();
			});
			
			uploader.on("uploadSuccess", function(file, result) {			
				
				if(result.error && result.error.message){
					alert(result.error.message);
				} else {
					var elemt = $(elem).parent();
					var imgstr = '<li fid="'+file.id+'" class="fl upload_image_item"><img src="'+result.url+'"><input value="'+result.attachment+'" type="hidden" name="'+name+'[]"></li>';
					if( type == 'replace' ){
						elemt.find('.upload_images_views').html(imgstr);
						uploader.reset();
					}else{
						elemt.find('.upload_images_views').append(imgstr);
					}
				}
			});
			
			uploader.onError = function( code,b,c ) {
				uploader.stop();
				if(code == "Q_EXCEED_SIZE_LIMIT"){
					common._alert("错误信息: 图片大于 8M 无法上传.");
					return
				}else if( code == 'Q_EXCEED_NUM_LIMIT' ){
					common._alert('超过限制数量，不能再上传');
				}else{
					common._alert("错误信息: " + code );
				}
			};
			uploader.on( 'uploadComplete', function( file ) {
			    common.hideIndicator();
			});
			if( !isbind ) {
				$('body').on('click',box ? box : '.upload_image_item',function(){
					var id = $(this).attr('fid');
					//if( id ) uploader.removeFile( id,true );
					
					var _this = $(this);
					common.confirm('提示','确定要删除此图片吗？',function(){
						_this.remove();
					});
				});
				isbind = true;	
			}
		}
	};	


	// 预览图片
	common.viewImages = function(){
		$('body').off('click','.need_show_images_item').on('click','.need_show_images_item',function(){
			var photos = [];
			var thiselem = $(this);
			thiselem.parent().find('.need_show_images_item').each(function(i){
				photos.push( $(this).find('img').attr('src') );
			});

		  	var photoBrowser = $.photoBrowser({
		      	photos : photos,
		      	navbarTemplate: '<header class="bar bar-nav"><a class="icon icon-left pull-left photo-browser-close-link"></a><h1 class="title"><div class="center sliding"><span class="photo-browser-current">2</span> <span class="photo-browser-of">/</span> <span class="photo-browser-total">9</span></div></h1></header>',
		      	theme : 'dark',
		      	loop : true,
		      	swipeToClose : false,
		      	initialSlide : thiselem.index(),
		  	});
			photoBrowser.open();

		});
	}


	//删除图片
	common.deleteImagesInWxJs = function(){
		$('body').on('click','.upload_images_views img',function(){
			var thisimg = $(this);
			common.confirm('重要提示','确定要删除此图片吗？',function(){
				thisimg.parent().remove();
			});
		});
	};
	
	common.squareImage = function(elemt){ //处理正方形图片
		$(elemt).each(function(){
			var thiswidth = $(this).width();
			if(thiswidth > 5)
			$(this).css({'height':thiswidth});
		});
	};	
	
	
	//确定框
	common.confirm = function(title,msg,okcall,cancelcall){
		$.confirm(msg,title,
			function () {
				if(okcall) okcall();
			},
			function () {
				if(cancelcall) cancelcall();
			}
		);	
	};
	
	
	
	//http请求
	common.Http = function(type,datatype,url,data,successCall,beforeCall,isLoading,comCall){
		isLoading = !isLoading;
		var data = data;
		data.open = settings.open;
		$.ajax({
			type: type,
			url: url,
			dataType: datatype,
			data : data,
			beforeSend:function(){
				if(isLoading) common.loading(true);
				if(beforeCall) beforeCall();
			},
			success: function(data){
				if(successCall) successCall(data);
			},
			complete:function(){					
				if(isLoading) common.loading(false);
				if(comCall) comCall();
			},				
			error: function(xhr, type){
				console.log(xhr);
			}
		});	
	};
	
	//加载中提示
	common.loading = function(bool) {	
		if(bool){
			$.showIndicator();			
		}else{
			$.hideIndicator();
		}
	};
	
	//返回顶部
	common.goToTop = function(classname){
		var topStr = '<div id="gotoTop" style="display: none;">'
			+'<div class="arrow"></div>'
			+'<div class="stick"></div>'
			+'</div>';
		if($('#gotoTop').length == 0){
			$('.page-group').append(topStr);			
		}
		var wheight = $(window).height();
		$('.'+classname).scroll(function() {
			var s = $('.'+classname).scrollTop();	
			if( s > wheight*1.5) {
				$("#gotoTop").show();
			} else {
				$("#gotoTop").hide();
			};				
        });
		$('.page-group').on('click','#gotoTop',function(){
			$('.'+classname).scrollTop(0);	
		});
    };
		
	
	
	//操作cookie
	common.cookie = {
		'prefix' : '',
		// 保存 Cookie
		'set' : function(name, value, seconds) {
			expires = new Date();
			value = encodeURI(value);
			expires.setTime(expires.getTime() + (1000 * seconds));
			document.cookie = this.name(name) + "=" + escape(value) + "; expires=" + expires.toGMTString() + "; path=/";
		},
		// 获取 Cookie
		'get' : function(name) {
			cookie_name = this.name(name) + "=";
			cookie_length = document.cookie.length;
			cookie_begin = 0;
			while (cookie_begin < cookie_length)
			{
				value_begin = cookie_begin + cookie_name.length;
				if (document.cookie.substring(cookie_begin, value_begin) == cookie_name)
				{
					var value_end = document.cookie.indexOf ( ";", value_begin);
					if (value_end == -1)
					{
						value_end = cookie_length;
					}
					return decodeURI(unescape(document.cookie.substring(value_begin, value_end)));
				}
				cookie_begin = document.cookie.indexOf ( " ", cookie_begin) + 1;
				if (cookie_begin == 0)
				{
					break;
				}
			}
			return null;
		},
		// 清除 Cookie
		'del' : function(name) {
			var expireNow = new Date();
			document.cookie = this.name(name) + "=" + "; expires=Thu, 01-Jan-70 00:00:01 GMT" + "; path=/";
		},
		'name' : function(name) {
			return this.prefix + name;
		}
	};	
	
	//图片上传
	common.uploadImage = function(elem,uniacid,callback){
		require(['webuploader'], function(webuploader){
			var agent = navigator.userAgent;
			var isAndroid = agent.indexOf("Android") > -1 || agent.indexOf("Linux") > -1;			
			defaultOptions = {
				pick: {
					id: elem,
					multiple : false
				},			
				auto: true,
				swf: "/web/resource/componets/webuploader/Uploader.swf",
				server: "./index.php?i="+uniacid+"&j=&c=utility&a=file&do=upload&type=image",
				chunked: false,
				compress: false,
				fileNumLimit: 2,
				fileSizeLimit: 4 * 1024 * 1024,
				fileSingleSizeLimit: 4 * 1024 * 1024,
				accept: {
					title: "Images",
					extensions: "gif,jpg,jpeg,bmp,png",
					mimeTypes: "image/*"
				}				
			};
			if (isAndroid) {
				defaultOptions.sendAsBinary = true;
			}
			options = $.extend({}, defaultOptions);
			var uploader = webuploader.create(options);
			uploader.on( "fileQueued", function( file ) {			
				common.loading(true);					
			});
			
			uploader.on("uploadSuccess", function(file, result) {			
				common.loading(false);				
				if(result.error && result.error.message){
					alert(result.error.message);
				} else {
					callback(result,elem);
					//console.log(result);
					uploader.reset();	
				}
			});
			
			uploader.onError = function( code ) {
				uploader.reset();
				if(code == "Q_EXCEED_SIZE_LIMIT"){
					alert("错误信息: 图片大于 4M 无法上传.");
					return
				}
				alert("错误信息: " + code );
			};		
		})		
	};
		
	
	//倒计时
	common.updateTime = function (call){
		var date = new Date();
		var time = date.getTime();  //当前时间距1970年1月1日之间的毫秒数 
		$(".lasttime").each(function(i){
			var endTime = $(this).attr('data-time') + '000'; //结束时间字符串
			var lag = (endTime - time); //当前时间和结束时间之间的秒数	
			if(lag > 0){
				var second = Math.floor(lag/1000%60);     
				var minite = Math.floor(lag/1000/60%60);
				var hour = Math.floor(lag/1000/60/60%24);
				var day = Math.floor(lag/1000/60/60/24);
				second = second.toString().length == 2 ? second : second;
				minite = minite.toString().length == 2 ? minite : minite;
				hour = hour.toString().length == 2 ? hour : hour;
				day = day.toString().length == 2 ? day : day;
			}else{
				var second = '0';     
				var minite = '0';
				var hour = '0';
				var day = '0';	
				if( call ) call();	
			}
			$(this).find('.day').text(day);
			$(this).find('.hour').text(hour);
			$(this).find('.minite').text(minite);
			$(this).find('.second').text(second);				
		});
		
		setTimeout(function(){common.updateTime()},1000);
	};	
	
	//倒计时 只到分钟
	common.updateTime2 = function (callback){
		var date = new Date();
		var time = date.getTime();  //当前时间距1970年1月1日之间的毫秒数 
		$(".lasttime2").each(function(i){
			var endTime = $(this).attr('data-time') + '000'; //结束时间字符串
			var lag = (endTime - time); //当前时间和结束时间之间的秒数	
			if(lag >= 1000){
				var second = Math.floor(lag/1000%60);     
				var minite = Math.floor(lag/1000/60);
				
			}else{
				var second = '0';
				var minite = '0';
				setTimeout(function(){
					if( callback ) callback();
				},1500);
			}
			$(this).find('.minite').text(minite);
			$(this).find('.second').text(second);			
		});
		
		setTimeout(function(){common.updateTime2(callback)},1000);
	};		
	
	// 到小时
	common.updateTime3 = function (call){
		var date = new Date();
		var time = date.getTime();  //当前时间距1970年1月1日之间的毫秒数 
		$(".lasttime3").each(function(i){
			var endTime = $(this).attr('data-time') + '000'; //结束时间字符串
			var lag = (endTime - time); //当前时间和结束时间之间的秒数	
			if(lag > 0){
				var second = Math.floor(lag/1000%60);     
				var minite = Math.floor(lag/1000/60%60);
				var hour = Math.floor(lag/1000/60/60);
				second = second.toString().length == 2 ? second : second;
				minite = minite.toString().length == 2 ? minite : minite;
				hour = hour.toString().length == 2 ? hour : hour;
			}else{
				var second = '0';     
				var minite = '0';
				var hour = '0';
				if( call ) call();	
			}
			$(this).find('.hour').text(hour);
			$(this).find('.minite').text(minite);
			$(this).find('.second').text(second);				
		});
		
		setTimeout(function(){common.updateTime3()},1000);
	};	

	//绑定事件
	common.bind = function(bindelem,config){
		var events = config.events || {};
		for(t in events){
			for(tt in events[t]){
				$(bindelem).on(t,events[t],events[t][tt]);
			}
		}
	};
	
	//初始化绑定页面事件方法
	common.init = function(config){
		config = config || {};
		
		for(t in config.events){
			for(tt in config.events[t]){
				//$(document).off(tt,t);
				//$('.page-group').off(tt,t,config.events[t][tt]);
				//$('.page-group').on(tt,t,config.events[t][tt]);
				$(document).off(tt,t,config.events[t][tt]);
				$(document).on(tt,t,config.events[t][tt]);				
			}
		}
		for(func in config.init){	
			config.init[func]();
		}
	}
	
	//创建url
	common.createUrl = function(dostr,opstr,obj){
		var str = '&do='+dostr+'&op='+opstr;
		for(t in obj){
			str += '&'+t+'='+obj[t];
		}
		return window.sysinfo.siteroot+'app/index.php?i='+window.sysinfo.uniacid+'&c=entry'+str+'&m=zofui_taskself';
	};
	
	
	//打印
	common.log = function(data){
		console.log(data);
	};	
	

	
	
	//将字符串转实体html代码
	common .htmlspecialchars_decode = function (str){           
          str = str.replace(/&amp;/g, '&'); 
          str = str.replace(/&lt;/g, '<');
          str = str.replace(/&gt;/g, '>');
          str = str.replace(/&quot;/g, '"');  
          str = str.replace(/&#039;/g, "'");  
          return str;  
	}	
	
	//加载更多
	common.getPage = function(params,succall,comcall){
		if(params.loading || params.isend) return;
		
		if( settings.issetpage == 1 ){
			
			var giveoage = params.page;
			params.page = $.isEmptyObject(detail2listobj.getLocalStorage()['extraData']) ? params.page : detail2listobj.getLocalStorage()['extraData']['page'];	
			if( 
				detail2listobj.getLocalStorage()['extraData']['isend'] || 
				( !$.isEmptyObject(detail2listobj.getLocalStorage()['extraData']) || 
				detail2listobj.isBack() ) && ( detail2listobj.getLocalStorage()['extraData']['isinit'] && giveoage == 1 )

			) return;

		}

		common.Http('post','json',common.createUrl('pagelist',params.op),params,function(data){
			//处理图片
			$('.list_container').append(data.data);
			if( data.status == 'ok' ){
				common.lazyLoad('.content','');
			}else{
				params.isend = true;
			}
			params.isinit = true;
			params.page ++;
			if( settings.issetpage == 1 )  detail2listobj.insertHtmlStr($(".list_container").html(),params);
			if(succall) succall(data);
		},function(){
			$('.preloader').show();
			params.loading = true;
		},true,function(){
			$('.preloader').hide();
			if(comcall) comcall();
			params.loading = false;
		});
	};	
	
	// speed 滚动速度，timer 滚动间隔 ,line 滚动行数
	common.scrollNotice = function(elemt,speed,timer,line){
		var num = elemt.find(".scitem").length;
		if( num <= 1 ) return false;
		var lineH=elemt.find(".scitem").first().height(); //获取行高		
		if(line==0) var line=1;
		var upHeight = 0-line*lineH;
		
		var scrollUp = function(elemt){ //滚动函数
			elemt.animate({
				'margin-top':upHeight
			},speed,function(){
				for(var i=1;i<=line;i++){
					elemt.find(".scitem").first().appendTo(elemt);
				}
				elemt.css({marginTop:'-0.5rem'});
			});
		};
		setInterval(function(){scrollUp(elemt)},timer);
	};
	
	common.scrollNotice2 = function(speed){
	  	var MyMar = null;
	 	var scroll_begin = $('.index_ad_begin').get(0);
	 	var scroll_end = $('.index_ad_end').get(0);
	 	var scroll_div = $('.index_ad_r2').get(0);
	 	if( !scroll_begin ) return false;
	 	scroll_end.innerHTML=scroll_begin.innerHTML; 
	 	function Marquee(){ 

	  	if(scroll_end.offsetWidth-scroll_div.scrollLeft<=0) 
	   		scroll_div.scrollLeft-=scroll_begin.offsetWidth; 
	  	else 
	   		scroll_div.scrollLeft++; 
	  	} 
	  	MyMar=setInterval(Marquee,speed); 
	};

	//验证 
	common.verify = function(type,parama,paramb){
		if(type == 'number'){
			if(parama == 'int'){ // 正整数
				var R = /^[1-9]*[1-9][0-9]*$/;
			}else if(parama == 'intAndLetter'){ //数字和字母
				var R = /^[A-Za-z0-9]*$/;
			}else if(parama == 'money'){ //金额,最多2个小数
				var R = /^\d+\.?\d{0,2}$/;
			}
			return R.test(paramb);
		}else if(type == 'mobile'){ //手机
			var R = /^1[2|3|4|5|6|7|8|9]\d{9}$/;
			return R.test(parama);
		}else if(type == 'cn'){ //中文
			var R = /^[\u2E80-\u9FFF]+$/;
			return R.test(parama);
		}
		
	};	
	
	common.chageTitle = function(title){
	  	var $body = $('body');
	  	document.title = title;
	  	var $iframe = $("<iframe style='display:none;' src='/favicon.ico'></iframe>");
	  	$iframe.on('load',function() {
	    	setTimeout(function() {
	      		$iframe.off('load').remove();
	    	}, 0);
	  	}).appendTo($body);
	};

    common.lazyLoad = function (container,params,callback) {
        var defaults = {
            offset: 20,
            delay: 0,
            placeholder: ""
        };

        var self = this;
        self.params = $.extend({}, defaults, params || {});
        self.container = $(container);
        var offset = self.params.offset || 0;
        self.params.offsetVertical = self.params.offsetVertical || offset;
        self.params.offsetHorizontal = self.params.offsetHorizontal || offset;
		
        self.params.delay = self.container.data('lazydelay') || self.params.delay;
        self.timer = null;
        self.toInt = function (str, defaultValue) {
            return parseInt(str || defaultValue, 10);
        };
        self.offset = {
            top: self.toInt(self.params.offsetTop, self.params.offsetVertical),
            bottom: self.toInt(self.params.offsetBottom, self.params.offsetVertical),
            left: self.toInt(self.params.offsetLeft, self.params.offsetHorizontal),
            right: self.toInt(self.params.offsetRight, self.params.offsetHorizontal)
        };

        self.inView = function (element, view) {
            var box = element.getBoundingClientRect();
            return (box.right >= view.left && box.bottom >= view.top && box.left <= view.right && box.top <= view.bottom);
        };

        self.run = function () {

            clearTimeout(self.timer);
            self.timer = setTimeout(self.render, self.params.delay);
        };

        self.render = function (ratio) {

            self.images = self.container.find('img[data-lazy], [data-lazy-background]');

            var view = {  //屏幕区域宽度
                left: 0 - self.offset.left,
                top: 0 - self.offset.top,
                bottom: (container.innerHeight || document.documentElement.clientHeight) + self.offset.bottom,
                right: (container.innerWidth || document.documentElement.clientWidth) + self.offset.right
            };

            $.each(self.images, function (i) {
                var $this = $(this);
                var inview = self.inView(this, view);		
                if (inview) {
                    if ($this.attr('data-lazyloaded')) {
                        return;
                    }
                    if ($this.attr('data-lazy-background')) {
                        $this.css({
                            'background-image': "url('" + $this.data('lazy-background') + "')"
                        });
                        $this.removeAttr('data-lazy-background');
                    } else {
                        var lazy = $this.attr('data-lazy');						
                        $this.removeAttr('data-lazy');
                        if (lazy) {
                            this.src = lazy;
                            this.onload = function () {
                                if (!$(this).height()) {
                                    this.style.height = "auto";
                                }
                                this.onload = null;
                            };
                        }
						
                    }
                    $this.attr('data-lazyloaded', true);

                } else {
                    var placeholder = $this.attr('lazy-placeholder') || self.params.placeholder;

                    if (placeholder && !$this.attr('data-lazyloaded')) {		
                        if ($this.data('lazy-background') !== undefined && $this.data('lazy-background') === '') {
                            this.style.backgroundImage = "url('" + placeholder + "')";
                            $this.removeAttr('data-lazy-background');
                        } else {
                            this.src = placeholder;
                        }
                    }
					
                    $this.removeAttr('lazy-placeholder');
                }
                if (self.params.onLoad) {
                    self.params.onLoad(self, this);
                }
				
				if(callback) callback(i);
            });
        };
        self.container.off('scroll', self.run);
        self.container.on('scroll', self.run).transitionEnd(self.run);
        self.run();
    }; 
	
	//返回收货地址html
	common.getWeixinAdreesWithHtml = function(callback){
		wxJs.openAddress(function(res){
			var data = {
				name : res.userName,
				tel : res.telNumber,						
				province : res.provinceName,
				city : res.cityName,
				dist : res.countryName,
				street : res.detailInfo	
			}
			var str = '<p>'+data.name+' '+data.tel+'</p>'
					  +'<p class="font_13px_999">'+data.province+' '+data.city+' '+data.dist+' '+data.street+'</p>'
					  +'<input name="address" value="'+data.province+','+data.city+','+data.dist+','+data.street+'" type="hidden"><input name="tel" type="hidden" value="'+data.tel+'"><input name="name" type="hidden" value="'+data.name+'">';
			callback(str);
		});
	};
	

	common.stopDrop = function () { 
		var lastY,lastX;//最后一次y坐标点 
		$('.content').on('touchstart', function(event) { 
			lastY = event.changedTouches[0].clientY;//点击屏幕时记录最后一次Y度坐标。 
			lastX = event.changedTouches[0].clientX;//
		}); 
		$('.content').on('touchmove', function(event) { 
			var y = event.changedTouches[0].clientY;
			var x = event.changedTouches[0].clientX;
			var st = $(this).scrollTop(); //滚动条高度 
			var viewheight = $(window).height();
			var pageheight = $('.drop_class').height();
			
			var forward = Math.abs((x-lastX)) - Math.abs((y-lastY));
			
			// 排除左右拉，上下拉的 && 到底或到顶了。
			if ( (forward < 0 && y >= lastY && st <= 5) || (forward <0 && y <= lastY && (viewheight + st >= pageheight - 5) ) ) { 
				
				event.preventDefault();
			}
		}); 
	};


;(function($, undefined){
	$.fn.serializeJson= function (){
		var serializeObj={};
		$( this.serializeArray() ).each( function (){
			serializeObj[ this.name ]= this.value;
		});
		return serializeObj;
	};
	
    $.fn.arrval = function(){
        var self = $(this);
        var result = [];
        if(self.length > 0){
            self.each(function(i, o){
           	 	result.push($(o).val());
			});
        }
        return result;
	};

})(Zepto)

/*fx*/
;(function($, undefined){
  var prefix = '', eventPrefix,
    vendors = { Webkit: 'webkit', Moz: '', O: 'o' },
    testEl = document.createElement('div'),
    supportedTransforms = /^((translate|rotate|scale)(X|Y|Z|3d)?|matrix(3d)?|perspective|skew(X|Y)?)$/i,
    transform,
    transitionProperty, transitionDuration, transitionTiming, transitionDelay,
    animationName, animationDuration, animationTiming, animationDelay,
    cssReset = {}

  function dasherize(str) { return str.replace(/([A-Z])/g, '-$1').toLowerCase() }
  function normalizeEvent(name) { return eventPrefix ? eventPrefix + name : name.toLowerCase() }

  if (testEl.style.transform === undefined) $.each(vendors, function(vendor, event){
    if (testEl.style[vendor + 'TransitionProperty'] !== undefined) {
      prefix = '-' + vendor.toLowerCase() + '-'
      eventPrefix = event
      return false
    }
  })

  transform = prefix + 'transform'
  cssReset[transitionProperty = prefix + 'transition-property'] =
  cssReset[transitionDuration = prefix + 'transition-duration'] =
  cssReset[transitionDelay    = prefix + 'transition-delay'] =
  cssReset[transitionTiming   = prefix + 'transition-timing-function'] =
  cssReset[animationName      = prefix + 'animation-name'] =
  cssReset[animationDuration  = prefix + 'animation-duration'] =
  cssReset[animationDelay     = prefix + 'animation-delay'] =
  cssReset[animationTiming    = prefix + 'animation-timing-function'] = ''

  $.fx = {
    off: (eventPrefix === undefined && testEl.style.transitionProperty === undefined),
    speeds: { _default: 400, fast: 200, slow: 600 },
    cssPrefix: prefix,
    transitionEnd: normalizeEvent('TransitionEnd'),
    animationEnd: normalizeEvent('AnimationEnd')
  }

  $.fn.animate = function(properties, duration, ease, callback, delay){
    if ($.isFunction(duration))
      callback = duration, ease = undefined, duration = undefined
    if ($.isFunction(ease))
      callback = ease, ease = undefined
    if ($.isPlainObject(duration))
      ease = duration.easing, callback = duration.complete, delay = duration.delay, duration = duration.duration
    if (duration) duration = (typeof duration == 'number' ? duration :
                    ($.fx.speeds[duration] || $.fx.speeds._default)) / 1000
    if (delay) delay = parseFloat(delay) / 1000
    return this.anim(properties, duration, ease, callback, delay)
  }

  $.fn.anim = function(properties, duration, ease, callback, delay){
    var key, cssValues = {}, cssProperties, transforms = '',
        that = this, wrappedCallback, endEvent = $.fx.transitionEnd,
        fired = false

    if (duration === undefined) duration = $.fx.speeds._default / 1000
    if (delay === undefined) delay = 0
    if ($.fx.off) duration = 0

    if (typeof properties == 'string') {
      // keyframe animation
      cssValues[animationName] = properties
      cssValues[animationDuration] = duration + 's'
      cssValues[animationDelay] = delay + 's'
      cssValues[animationTiming] = (ease || 'linear')
      endEvent = $.fx.animationEnd
    } else {
      cssProperties = []
      // CSS transitions
      for (key in properties)
        if (supportedTransforms.test(key)) transforms += key + '(' + properties[key] + ') '
        else cssValues[key] = properties[key], cssProperties.push(dasherize(key))

      if (transforms) cssValues[transform] = transforms, cssProperties.push(transform)
      if (duration > 0 && typeof properties === 'object') {
        cssValues[transitionProperty] = cssProperties.join(', ')
        cssValues[transitionDuration] = duration + 's'
        cssValues[transitionDelay] = delay + 's'
        cssValues[transitionTiming] = (ease || 'linear')
      }
    }

    wrappedCallback = function(event){
      if (typeof event !== 'undefined') {
        if (event.target !== event.currentTarget) return // makes sure the event didn't bubble from "below"
        $(event.target).unbind(endEvent, wrappedCallback)
      } else
        $(this).unbind(endEvent, wrappedCallback) // triggered by setTimeout

      fired = true
      $(this).css(cssReset)
      callback && callback.call(this)
    }
    if (duration > 0){
      this.bind(endEvent, wrappedCallback)
      // transitionEnd is not always firing on older Android phones
      // so make sure it gets fired
      setTimeout(function(){
        if (fired) return
        wrappedCallback.call(that)
      }, ((duration + delay) * 1000) + 25)
    }

    // trigger page reflow so new elements can animate
    this.size() && this.get(0).clientLeft

    this.css(cssValues)

    if (duration <= 0) setTimeout(function() {
      that.each(function(){ wrappedCallback.call(this) })
    }, 0)

    return this
  }

  testEl = null
})(Zepto);

(function ($,undefined) {
    $.extend($.fn, {
        fadeIn: function (speed, easing, complete) {
            if (typeof(speed) === 'undefined') speed = 400;
            if (typeof(easing) === 'undefined' || typeof(easing) !== 'string') easing = 'swing';

            $(this).css({
                opacity: 0,
                display: '-webkit-box',
            }).animate({
                opacity: 1,
            }, speed, easing, function () {
                // complete callback
                if (typeof(easing) === 'function') {
                    easing();
                } else if (typeof(complete) === 'function') {
                    complete();
                }
            });

            return this;
        },
        fadeOut: function (speed, easing, complete) {
            if (typeof(speed) === 'undefined') speed = 400;
            if (typeof(easing) === 'undefined' || typeof(easing) !== 'string') easing = 'swing';

            $(this).css({
                opacity: 1
            }).animate({
                opacity: 0
            }, speed, easing, function () {
                $(this).css('display', 'none');
                // complete callback
                if (typeof(easing) === 'function') {
                    easing();
                } else if (typeof(complete) === 'function') {
                    complete();
                }
            });

            return this;
        },
        fadeToggle: function (speed, easing, complete) {
            return this.each(function () {
                var el = $(this);
                el[(el.css('opacity') === 0 || el.css('display') === 'none') ? 'fadeIn' : 'fadeOut'](speed, easing, complete)
            })
        }
    })
})(Zepto);


;(function($){
  var slice = Array.prototype.slice

  function Deferred(func) {
    var tuples = [
          // action, add listener, listener list, final state
          [ "resolve", "done", $.Callbacks({once:1, memory:1}), "resolved" ],
          [ "reject", "fail", $.Callbacks({once:1, memory:1}), "rejected" ],
          [ "notify", "progress", $.Callbacks({memory:1}) ]
        ],
        state = "pending",
        promise = {
          state: function() {
            return state
          },
          always: function() {
            deferred.done(arguments).fail(arguments)
            return this
          },
          then: function(/* fnDone [, fnFailed [, fnProgress]] */) {
            var fns = arguments
            return Deferred(function(defer){
              $.each(tuples, function(i, tuple){
                var fn = $.isFunction(fns[i]) && fns[i]
                deferred[tuple[1]](function(){
                  var returned = fn && fn.apply(this, arguments)
                  if (returned && $.isFunction(returned.promise)) {
                    returned.promise()
                      .done(defer.resolve)
                      .fail(defer.reject)
                      .progress(defer.notify)
                  } else {
                    var context = this === promise ? defer.promise() : this,
                        values = fn ? [returned] : arguments
                    defer[tuple[0] + "With"](context, values)
                  }
                })
              })
              fns = null
            }).promise()
          },

          promise: function(obj) {
            return obj != null ? $.extend( obj, promise ) : promise
          }
        },
        deferred = {}

    $.each(tuples, function(i, tuple){
      var list = tuple[2],
          stateString = tuple[3]

      promise[tuple[1]] = list.add

      if (stateString) {
        list.add(function(){
          state = stateString
        }, tuples[i^1][2].disable, tuples[2][2].lock)
      }

      deferred[tuple[0]] = function(){
        deferred[tuple[0] + "With"](this === deferred ? promise : this, arguments)
        return this
      }
      deferred[tuple[0] + "With"] = list.fireWith
    })

    promise.promise(deferred)
    if (func) func.call(deferred, deferred)
    return deferred
  }

  $.when = function(sub) {
    var resolveValues = slice.call(arguments),
        len = resolveValues.length,
        i = 0,
        remain = len !== 1 || (sub && $.isFunction(sub.promise)) ? len : 0,
        deferred = remain === 1 ? sub : Deferred(),
        progressValues, progressContexts, resolveContexts,
        updateFn = function(i, ctx, val){
          return function(value){
            ctx[i] = this
            val[i] = arguments.length > 1 ? slice.call(arguments) : value
            if (val === progressValues) {
              deferred.notifyWith(ctx, val)
            } else if (!(--remain)) {
              deferred.resolveWith(ctx, val)
            }
          }
        }

    if (len > 1) {
      progressValues = new Array(len)
      progressContexts = new Array(len)
      resolveContexts = new Array(len)
      for ( ; i < len; ++i ) {
        if (resolveValues[i] && $.isFunction(resolveValues[i].promise)) {
          resolveValues[i].promise()
            .done(updateFn(i, resolveContexts, resolveValues))
            .fail(deferred.reject)
            .progress(updateFn(i, progressContexts, progressValues))
        } else {
          --remain
        }
      }
    }
    if (!remain) deferred.resolveWith(resolveContexts, resolveValues)
    return deferred.promise()
  }

  $.Deferred = Deferred;
})(Zepto);

;(function($){
  // Create a collection of callbacks to be fired in a sequence, with configurable behaviour
  // Option flags:
  //   - once: Callbacks fired at most one time.
  //   - memory: Remember the most recent context and arguments
  //   - stopOnFalse: Cease iterating over callback list
  //   - unique: Permit adding at most one instance of the same callback
  $.Callbacks = function(options) {
    options = $.extend({}, options)

    var memory, // Last fire value (for non-forgettable lists)
        fired,  // Flag to know if list was already fired
        firing, // Flag to know if list is currently firing
        firingStart, // First callback to fire (used internally by add and fireWith)
        firingLength, // End of the loop when firing
        firingIndex, // Index of currently firing callback (modified by remove if needed)
        list = [], // Actual callback list
        stack = !options.once && [], // Stack of fire calls for repeatable lists
        fire = function(data) {
          memory = options.memory && data
          fired = true
          firingIndex = firingStart || 0
          firingStart = 0
          firingLength = list.length
          firing = true
          for ( ; list && firingIndex < firingLength ; ++firingIndex ) {
            if (list[firingIndex].apply(data[0], data[1]) === false && options.stopOnFalse) {
              memory = false
              break
            }
          }
          firing = false
          if (list) {
            if (stack) stack.length && fire(stack.shift())
            else if (memory) list.length = 0
            else Callbacks.disable()
          }
        },

        Callbacks = {
          add: function() {
            if (list) {
              var start = list.length,
                  add = function(args) {
                    $.each(args, function(_, arg){
                      if (typeof arg === "function") {
                        if (!options.unique || !Callbacks.has(arg)) list.push(arg)
                      }
                      else if (arg && arg.length && typeof arg !== 'string') add(arg)
                    })
                  }
              add(arguments)
              if (firing) firingLength = list.length
              else if (memory) {
                firingStart = start
                fire(memory)
              }
            }
            return this
          },
          remove: function() {
            if (list) {
              $.each(arguments, function(_, arg){
                var index
                while ((index = $.inArray(arg, list, index)) > -1) {
                  list.splice(index, 1)
                  // Handle firing indexes
                  if (firing) {
                    if (index <= firingLength) --firingLength
                    if (index <= firingIndex) --firingIndex
                  }
                }
              })
            }
            return this
          },
          has: function(fn) {
            return !!(list && (fn ? $.inArray(fn, list) > -1 : list.length))
          },
          empty: function() {
            firingLength = list.length = 0
            return this
          },
          disable: function() {
            list = stack = memory = undefined
            return this
          },
          disabled: function() {
            return !list
          },
          lock: function() {
            stack = undefined
            if (!memory) Callbacks.disable()
            return this
          },
          locked: function() {
            return !stack
          },
          fireWith: function(context, args) {
            if (list && (!fired || stack)) {
              args = args || []
              args = [context, args.slice ? args.slice() : args]
              if (firing) stack.push(args)
              else fire(args)
            }
            return this
          },
          fire: function() {
            return Callbacks.fireWith(this, arguments)
          },
          fired: function() {
            return !!fired
          }
        }

    return Callbacks
  }
})(Zepto);