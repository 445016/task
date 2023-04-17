
var app = angular.module('myyapp',[]);
app.controller('ctr',['$scope',function($scope){
	
    /*poster*/
        $scope.pmodules = [
            {
                id : 0,
                name:'headimg',
                title : '头像',
                isedit : 0,
                params:{
                    img : './../addons/zofui_taskself/public/images/default_head.jpg',
                    width : 100,
                    height : 100,
                    left:100,
                    top:100
                },
            },
            {
                id : 1,
                name: "nick",
                title: "昵称",
                isedit : 1,
                params:{
                    txt: '会员昵称',
                    left:100,
                    top:100,
                    color : '#000000',
                    fontsize : 14,
                },
            },
            {
                id : 3,
                name:'qrcode',
                title:'二维码',
                params:{
                    img : './../addons/zofui_taskself/public/images/qrcode.png',
                    width : 100,
                    height : 100,
                    left:100,
                    top:100
                },
            },
        ];


    $scope.focus = '';
    $scope.pbg = poster.bgimg == '' ? '' : poster.bgimg;
    $scope.pitems =   pparams == '' ? [] : pparams;
    $scope.headitem = 1;

    $scope.changeItem = function(type){
        $scope.headitem = type;
    };

    $scope.addItem = function(name){
        angular.forEach($scope.pmodules, function(m,i){
            if(m.name== name){
                var newitem = $.extend(true,{},m);
                newitem.id = 'i'+ new Date().getTime();
                $scope.focus = newitem.id;
                $scope.pitems.push(newitem);
                return false;
            }
        });
    };

    $scope.deleteItem = function(id){
        if( confirm('确认要删除吗？') ){
            angular.forEach($scope.pitems,function(m,i){
                if(m.id == id){
                    $scope.pitems.splice(i,1);
                    return false;
                }
            })
        }
    };
    $scope.getFocus = function(id){
        $scope.focus = id;
    };
    /*poster end*/

    /*page*/
    $scope.pageid = 1;
    $scope.changePage = function(id){
        $scope.pageid = id;
    }

    

	$scope.uploadImage = function(type){
        require(['util'], function(util){
            util.image('',function(data){
            	if(type == 'pbg'){
                    
                    $scope.pbg = data['url'];
            	}else if( type == 'indexbg' ){
                    
                    $scope.indextemp.bg = data['url'];
                }else if( type == 'prizebg' ){
                    
                    $scope.prizetemp.bg = data['url'];
                }
				$scope.$apply();
            });
        });
	};
    $scope.defaultBg = function(type){
        if( type == 'index' ){
            $scope.indextemp.bg = './../addons/zofui_taskself/public/images/bg.png';
        }else if( type == 'prize' ){
            $scope.prizetemp.bg = './../addons/zofui_taskself/public/images/prize_bg.png';
        }
    }

    // 保存关键字
    $scope.saveKey = function(){
        var postdata = {
            key : $('input[name=key]').val(),
            content : $('textarea[name=content]').val(),
            ccontent : $('textarea[name=ccontent]').val(),
        };
        
        Http('post','json','saveposterkey',postdata,function(data){
            webAlert(data.res);
            if( data.status == '200'){
                setTimeout(function(){
                    //location.href = "";
                },500)
            }
        },true);
    };

    // 保存数据
    $scope.savePoster = function(){
        
        if( typeof $scope.pbg == 'undefined' || $scope.pbg == '' ){
            webAlert('请上传背景图片');
            return false;
        }

        var items = angular.toJson($scope.pitems);

        Http('post','json','savapostertemp',{data:items,bgimg:$scope.pbg},function(data){
            webAlert(data.res);
            if( data.status == '200'){
                setTimeout(function(){
                    location.href = "";
                },500)
            }
        },true);
    }

}]).directive('mySlider',function(){
    return {
        restrict : 'A',
        link : function(scope,elem,attr){
            require(['jquery.ui'],function(){   
                    $(elem).slider({
                        min: parseInt( attr.min, 10 ),
                        max: parseInt( attr.max, 10 ),
                        value : parseInt( attr.value, 10 ),
                        slide : function(event,ui){
                            if(attr.type == 1){
                                scope.indextemp.timetop = ui.value;
                            }else if(attr.type == 2){
                                scope.indextemp.othertop = ui.value;    
                            }else if(attr.type == 3){
                                scope.prizetemp.fonttop = ui.value;    
                            }else if(attr.type == 4){
                                scope.prizetemp.prizetop = ui.value;    
                            }else if(attr.type == 5){
                                scope.item.params[attr.name] = ui.value;    
                            }
                            scope.$apply();
                        }
                    });
            });
        }
    }
}).directive('onDraggable', function ($timeout) {
    return {
        restrict: 'A',
        link: function (scope, elem, attr) {
            
            require(['jquery.ui'],function(){   
                $( elem ).draggable({drag : function(e,o){

                    angular.forEach(scope.pitems,function(m,i){
                        if(m.id == attr.viewid){
                            $timeout(function(){
                                m.params.left = o.position.left;
                                m.params.top = o.position.top;
                            })
                            return false;
                        }
                    })
                }});
            });
        }
    }
}).directive('onResizable', function ($timeout) {
    return {
        restrict: 'A',
        link: function (scope, elem, attr) {
            
            require(['jquery.ui'],function(){   
                $( elem ).resizable({resize : function(e,o){
                    angular.forEach(scope.pitems,function(m,i){
                        if(m.id == attr.id){
                            $timeout(function(){
                                m.params.width = o.size.width;
                                m.params.height = o.size.height;
                            })
                            return false;
                        }
                    })
                    
                }});
            });
        }
    }
});