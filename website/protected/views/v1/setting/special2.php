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
		<h1>消息中心</h1>
		<ul>
			<li>1. 查看消息
				<i class="iconfont">&#xe640;</i>
				<p>进入“通讯录”界面，点击左上角“消息”，进入消息中心，即可查看下方“公告”、“小喇叭”等各类消息。</p>
				<i class="iconfont" style="display: none">&#xe63d;</i>
				<div style="clear: both"></div>
			</li>
			<li>2. 新建“小喇叭”发送消息
				<i class="iconfont">&#xe640;</i>
				<p>进入“通讯录”界面，点击左上角“消息”，进入消息中心，点击右上角“小喇叭”，输入内容后，选择要发送的联盟或者好友即可发送消息。</p>
				<i class="iconfont" style="display: none">&#xe63d;</i>
				<div style="clear: both"></div>
			</li>
			<li>3. 使用“小喇叭”再发一条
				<i class="iconfont">&#xe640;</i>
				<p>如果你想给曾经发送过消息的好友们再次发送一条消息，你就在消息中心-小喇叭主页，查找你发送过的历史消息，点击进入后点击：“再发一条”按钮，输入你想发的内容点发送即可。</p>
				<i class="iconfont" style="display: none">&#xe63d;</i>
				<div style="clear: both"></div>
			</li>
			<li>4. 删除已发送的“小喇叭”
				<i class="iconfont">&#xe640;</i>
				<p>你可以在消息中心-小喇叭主页点击右上角点击垃圾桶图标，会弹出提示框“是否清除所有小喇叭”，点击确认即可全部删除；如果你想删除单条消息，请你点击进入该条消息，点击右上角点击垃圾桶图标，会弹出提示框“是否删除本条小喇叭”，点击确认即可全部删除。</p>
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