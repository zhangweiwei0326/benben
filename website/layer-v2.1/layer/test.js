$(function(){
	//全局配置文件
	layer.config({
	    extend: ['skin/yourskin/style.css'], //加载您的扩展样式
//	    skin: 'layer-ext-yourskin'
	});
	
	//alert初体验
	$(".1").click(function(){
		layer.alert('确定要删除吗？不可恢复');
	});
	//alert第三方扩展皮肤
	$(".2").click(function(){
		layer.alert('内容', {
			title:"迅傲提示",
		    icon: 3,
		    skin: 'layer-ext-yourskin' //该皮肤由layer.seaning.com友情扩展。关于皮肤的扩展规则，去这里查阅
		});
	});
	//confirm询问层
	$(".3").click(function(){
		layer.confirm('您是如何看待前端开发？', {
		    btn: ['重要','奇葩'] //按钮
		}, function(){
		    layer.msg('的确很重要', {icon: 1});
		}, function(){
		    layer.msg('也可以这样', {
		        time: 20000, //20s后自动关闭
		        btn: ['明白了', '知道了']
		    });
		});
	});
	//msg提示层
	$(".4").click(function(){
		layer.msg('您已登陆，请勿重复登陆',{
			time:1000,		//3秒后关闭，单位是ms	
//			skin:'layer-ext-yourskin',
		});
	});
	
	
	//墨绿深蓝风
	$(".5").click(function(){
		layer.alert('墨绿风格，点击确认看深蓝', {
		    skin: 'layui-layer-molv' //样式类名
		    ,closeBtn: 0
		}, function(){
		    layer.alert('偶吧深蓝style', {
		        skin: 'layui-layer-lan',
//		        closeBtn: 0,
		        shift: 4 //动画类型
		    });
		});
	});
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
});