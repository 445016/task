// 分享	
	var wxJs = {};	
	
	wxJs.share = function(shareData){
		//分享朋友
		wx.onMenuShareAppMessage({
			title: shareData.sharetitle,
			desc: shareData.sharedesc,
			link: shareData.sharelink,
			imgUrl:shareData.shareimg,
			trigger: function (res) {
			},
			success: function (res) {
			},
			cancel: function (res) {
			},
			fail: function (res) {
				//alert(JSON.stringify(res));
				//alert(shareData.title)
			}
		});
		//分享QQ
		wx.onMenuShareQQ({
			title: shareData.sharetitle,
			desc: shareData.sharedesc,
			link: shareData.sharelink,
			imgUrl:shareData.shareimg,
			trigger: function (res) {
			},
			success: function (res) {
			},
			cancel: function (res) {
			},
			fail: function (res) {
				//alert(JSON.stringify(res));
				//alert(shareData.title)
			}
		});
		//分享QQ空间
		wx.onMenuShareQZone({
			title: shareData.sharetitle,
			desc: shareData.sharedesc,
			link: shareData.sharelink,
			imgUrl:shareData.shareimg,
			trigger: function (res) {
			},
			success: function (res) {
			},
			cancel: function (res) {
			},
			fail: function (res) {
				//alert(JSON.stringify(res));
			}
		});	
		//朋友圈
		wx.onMenuShareTimeline({
			title: shareData.sharetitle,
			link: shareData.sharelink,
			imgUrl:shareData.shareimg,
			trigger: function (res) {
			},
			success: function (res) {
			},
			cancel: function (res) {
			},
			fail: function (res) {
				//alert(JSON.stringify(res));
			}
		});
		
	}	

	function getLocation(call){
					
		common.showIndicator();
		
		if( sysinfo.openid.length < 10 ){
			var data = {latitude:22.72929,longitude:114.0113,country : settings.country};
			common.Http('post','json',common.createUrl('ajaxdeal','location'),data,function(res){
				if(res.status == 200){
					if( call ) call();
				}else{
					common._alert(res.res,function(){});
				}
				common.hideIndicator();
			});
		}else{
			wx.ready(function (){
			wx.getLocation({

				success: function (res) {
					
					var data = {latitude:res.latitude,longitude:res.longitude};
					common.Http('post','json',common.createUrl('ajaxdeal','location'),data,function(res){
						if(res.status == 200){
							if( call ) call();
						}else{
							common._alert(res.res,function(){});
						}
					})
					common.hideIndicator();
				},
				cancel: function (res) {
					common.hideIndicator();
					common._alert('请允许读取您的位置，否则无法接到任务',function(){
						getLocation(call);
					});
				},
				fail: function (res) {
					var str = JSON.stringify(res);
					common._alert('获取位置失败,请重新进入获取您的位置。原因：关闭了微信的定位功能或'+str,function(){
						//wx.closeWindow();
					});
				}
			});
			});
		}
		
	};

	function getLocationwap(call){
		
		if (navigator.geolocation){
		    navigator.geolocation.getCurrentPosition(showPosition,showError);
		}else{
		    common._alert("浏览器不支持地理定位。");
		}
		
		function showPosition(position){
			
			var data = {latitude:position.coords.latitude,longitude:position.coords.longitude};
			common.showIndicator();
			common.Http('post','json',common.createUrl('ajaxdeal','location'),data,function(res){
				if(res.status == 200){
					if( call ) call();
				}else{
					common._alert(res.res,function(){});
				}
				common.hideIndicator();
			})
		}

		function showError(error){ 
			console.log( error );
		  	switch(error.code) { 
			    case error.PERMISSION_DENIED: 
			      common._alert('请允许读取您的位置，否则无法接到任务，可重新进页面再试');
			      break; 
			    case error.POSITION_UNAVAILABLE: 
			      common._alert("定位失败,位置信息是不可用"); 
			      break; 
			    case error.TIMEOUT: 
			      common._alert("定位失败,请求获取用户位置超时"); 
			      break; 
			    case error.UNKNOWN_ERROR: 
			      common._alert("定位失败,定位系统失效"); 
			      break; 
		  	} 
		}
	}





$(function(){

	if( settings.dev == 'wx' ) {
		wx.ready(function (){
			wxJs.share(settings);
			if( settings.islimit == 1 ){
				getLocation();
			}
		});
		if( sysinfo.siteroot == 'http://127.0.0.6/' ){
			if( settings.islimit == 1 ){
				getLocation();
			}
		}
	}else{
		if( settings.islimit == 1 ){ 
			getLocationwap(function(){
				location.href = "";
			}); 
		}
	}

});
	
	

	
	var voice = {
		localId: '',
		serverId: ''
	};
	var imagesurl = [];	
	
	//录音
	wxJs.startRecord = function (callback) {
		wx.startRecord();
		if( callback ) callback();
	};

	//停止
	wxJs.stopRecord = function (callback) {
		wx.stopRecord({
		  success: function (res) {
			voice.localId = res.localId;
			if( callback ) callback(res);
		  },
		  fail: function (res) {
			alert(JSON.stringify(res));
		  }
		});
	};

	//预览图片
	wxJs.preViewsImages = function(current,urls){
		wx.previewImage({
			current: current, // 当前显示图片的http链接
			urls: urls // 需要预览的图片http链接列表
		});
	};	
	
  //监听录音自动停止
	wxJs.onVoiceRecordEnd = function (callback) {
		wx.onVoiceRecordEnd({
			complete: function (res) {
				voice.localId = res.localId;
				if( callback ) callback(res);
			}
		});
	};

	//选择图片
	wxJs.chooseImage = function (num,callback) {
		wx.chooseImage({
			count: num, // 默认9
			sizeType: ['compressed'], // 可以指定是原图还是压缩图，默认二者都有
			sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
			success: function (res) {
				var localIds = res.localIds; // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
				wxJs.uploadImage(localIds,callback);
			}
		});
	};

	//上传图片
	wxJs.uploadImage =  function(localIds,callback) {
		var i = 0,length = localIds.length;
		var imagesurl = [];
		function upload() {
			wx.uploadImage({
				localId: localIds[i], // 需要上传的图片的本地ID，由chooseImage接口获得
				isShowProgressTips: 1, // 默认为1，显示进度提示
				success: function (res) {
					i ++;
					common.Http('post','json',common.createUrl('ajaxdeal','uploadimages'),{serverId:res.serverId},function(data){
						if( data.status == 201 ){
							common._alert(data.res);
						}else{
							var imagearr = [data.obj.url,data.obj.attachment];
							imagesurl.push(imagearr);
							if(i < length){
								upload();
							}else{
								callback(imagesurl);
							}
						}						
					});
				}
			});
		}
		upload();
    }
	
	
	//播放音频
	wxJs.playVoice = function (callback) {
		if (voice.localId == '') {
			common.alert('请先录制一段声音');
			return;
		}
		wx.playVoice({
			localId: voice.localId
		});
		if( callback ) callback();
	};

	//暂停播放音频
	wxJs.pauseVoice = function (callback) {
		wx.pauseVoice({
			localId: voice.localId
		});
		if( callback ) callback();
	};

	//停止播放音频
	wxJs.stopVoice = function (callback) {
		wx.stopVoice({
			localId: voice.localId,
		});
		if( callback ) callback();
	};

	//监听录音播放停止
	wxJs.onVoicePlayEnd = function (callback) {
		wx.onVoicePlayEnd({
			complete: function (res) {
				if( callback ) callback(res);
			}
		});
	};
	
	//上传语音
	wxJs.uploadVoice = function (callback) {
		if (voice.localId == '') {
			common.alert('请先录制一段声音');
			return;
		}
		wx.uploadVoice({
			localId: voice.localId,
			success: function (res) {
				voice.serverId = res.serverId;
				if( callback ) callback(res);
			}
		});
	};
	wxJs.translateVoice = function(callback){
		wx.translateVoice({
		   localId: voice.localId, // 需要识别的音频的本地Id，由录音相关接口获得
			isShowProgressTips: 1, // 默认为1，显示进度提示
			success: function (res) {
				//alert(res.translateResult); // 语音识别的结果
				if( callback ) callback(res);
			}
		});
	};
	
	wxJs.openAddress = function(callback){

		/*callback({
			userName : '好好好',
			telNumber : '13112345678',
			provinceName : '上海市',
			cityName : '上海市',
			countryName : '金山区',
			detailInfo	: '好人路55号'
		});
		return;*/

		wx.openAddress({
			success : function(result){
				callback(result);
			},
			fail: function (res) {
                //alert(JSON.stringify(res));
            }
		});
	};

	
