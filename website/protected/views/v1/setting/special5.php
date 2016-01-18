<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>帮助与反馈</title>
	<meta name="viewport" content="width=device-width,height=device-height,initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/protected/css/main.css">
</head>
<body>
	<div class="main">
		<div class="btn btn-default" onclick="history.back()" style="float: right;margin: 2px 2px 0 0;">返回</div>
		<h1>号码直通车</h1>
		<ul>
			<li>1. 列表搜索号码直通车商家及服务项目
				<i class="iconfont">&#xe640;</i>
				<p>进入“号码直通车”界面，输入任意一个字或多个字进行搜索，也可以点击搜索框右边“范围”选择省市按地区搜索，在搜索结果列表中选择所需的号码直通车商家及服务项目。</p>
				<i class="iconfont" style="display: none">&#xe63d;</i>
				<div style="clear: both"></div>
			</li>
			<li>2. 地图搜索号码直通车商家及服务项目
				<i class="iconfont">&#xe640;</i>
				<p>进入“号码直通车”界面，点击右上角圆环针头标志，进入地图模式搜索界面，输入任意一个字或多个字进行搜索，会显示相应的号码直通车商家的地图信息，点击地图中间的标志可以查看商家的具体信息及服务项目。</p>
				<i class="iconfont" style="display: none">&#xe63d;</i>
				<div style="clear: both"></div>
			</li>
			<li>3. 收藏商家或取消收藏商家
				<i class="iconfont">&#xe640;</i>
				<p>不管是列表模式还是地图模式只要你点击选择一个商家都可以进入其详情页面，右上方有个白色爱心标志，点击后会变成红色爱心同时提示“店铺已收藏至通讯录”，再次点击又会变成白色爱心同时提示“已取消收藏”。</p>
				<i class="iconfont" style="display: none">&#xe63d;</i>
				<div style="clear: both"></div>
			</li>
			<li>4. 联系商家
				<i class="iconfont">&#xe640;</i>
				<p>联系商家有2种方式，一是奔犇发消息联系，二是拨打电话联系。在商家详情页面，下方左侧是蓝色“发消息”按钮，点击即可通过奔犇聊天，下方右侧是绿色“拨号”按钮，点击会自动拨打该商家预留的联系电话。</p>
				<i class="iconfont" style="display: none">&#xe63d;</i>
				<div style="clear: both"></div>
			</li>
			<li>5. 我的号码直通车创建
				<i class="iconfont">&#xe640;</i>
				<p>在“我的-我的号码直通车”界面，上传头像（照片可以立刻拍照，也可以从相册中选择），填写商铺名称、商铺简称、手机号、固话、行业、地区、设置地址、详细地址、服务项目和业务介绍，点击提交完成创建。其中设置地址是指设置商铺在地图上的具体位置，可以通过双指滑动放大缩小地图和单指拖动地图来确定您的商铺位置。</p>
				<i class="iconfont" style="display: none">&#xe63d;</i>
				<div style="clear: both"></div>
			</li>
		</ul>
	</div>
</body>
<script src="http://apps.bdimg.com/libs/zepto/1.1.4/zepto.min.js"></script>
<script>
	$("ul>li").on("click",function(e){
		var _this=this;
		$.each($("ul>li p"),function(kk,vv){
			if (vv!=$(_this).children("p")[0]) {
				$(vv).hide();
				$(vv).next(".iconfont").hide();
				$(vv).prev(".iconfont").show();
			}
		});
		$(this).children("p").toggle();
		$(this).children(".iconfont").toggle();
	});
</script>
</html>