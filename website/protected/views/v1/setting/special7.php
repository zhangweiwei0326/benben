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
		<h1>我要买</h1>
		<ul>
			<li>1. 界面简介
				<i class="iconfont">&#xe640;</i>
				<p>“我要买”的界面，显示所有人发布的信息内容，内容也是按照发布日期由近及远的顺序排列。每条发布的信息会显示标题（我要买的物品及其数量），号码直通车商家的名字、报价和备注，还有显示多少人报价、“我来报价”的按钮、此信息的剩余有效时间（精确到秒）。界面的上面有一个“搜索信息”功能模块，右下方有一个“发布信息”功能模块。</p>
				<i class="iconfont" style="display: none">&#xe63d;</i>
				<div style="clear: both"></div>
			</li>
			<li>2. 发布“我要买”信息
				<i class="iconfont">&#xe640;</i>
				<p>在“我要买”的界面，点击右下角（黑色圆圈绿色铅笔）图标，进入“发布”页面，填写标题、描述和数量，然后选择地址和结束时间，最后点击右上角的“发布”按钮即可完成信息的发布。</p>
				<i class="iconfont" style="display: none">&#xe63d;</i>
				<div style="clear: both"></div>
			</li>
			<li>3. 搜索查看 “我要买”信息
				<i class="iconfont">&#xe640;</i>
				<p>在“我要买”的界面，点击上方的搜索模块，输入一个或多个关键字搜索，就会弹出相关信息列表，如果点击搜索栏右边的“范围”按钮还可以把信息范围精确到县市。点击发布信息后，会进入“我要买详情”界面，此界面上半部分显示发布者的头像、昵称和地区，发布的标题、描述、数量、发布时间和结束时间，下半部分显示多少人报价，以及报价者的头像、昵称、报价时间、报价金额和备注。在界面的底部是“我要报价”按钮，点击进入后可以报价。</p>
				<i class="iconfont" style="display: none">&#xe63d;</i>
				<div style="clear: both"></div>
			</li>
			<li>4. 我来报价
				<i class="iconfont">&#xe640;</i>
				<p>在“我要买”的界面，只要你已经是号码直通车商就可在任意页面点击“我要报价”按钮，都会弹出“我要报价”的窗口，您只要依次填写商家名、报价金额和备注后，点击确定后即可完成报价。</p>
				<i class="iconfont" style="display: none">&#xe63d;</i>
				<div style="clear: both"></div>
			</li>
			<li>5. 接受报价
				<i class="iconfont">&#xe640;</i>
				<p>在“我要买”的界面，点击自己发布的一栏，进入“我要买详情”界面，点击下方报价人右上角“。。。”图标，选择接受报价即可。</p>
				<i class="iconfont" style="display: none">&#xe63d;</i>
				<div style="clear: both"></div>
			</li>
			<li>6. 我的“我要买”信息
				<i class="iconfont">&#xe640;</i>
				<p>点击进入我的-我要买，你会看到所有自己在“我要买”发布的信息，内容也是按照发布日期由近及远的顺序排列。每条发布的信息会显示标题（我要买的物品及其数量），号码直通车商家的名字、报价和备注，还有显示多少人报价、“我来报价”的按钮、此信息的剩余有效时间（精确到秒）。界面的上面有一个“搜索信息”功能模块，右下方有一个“发布信息”功能模块。</p>
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