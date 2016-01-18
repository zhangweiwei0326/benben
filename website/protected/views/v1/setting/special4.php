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
		<h1>群组</h1>
		<ul>
			<li>1. 如何创建群组
				<i class="iconfont">&#xe640;</i>
				<p>进入“群组”界面，点击右上角三个小圆圈标志，选择“创建群”，跳转到“新建群组”界面，上传您的头像，填写群名称，选择所在地区，填写群组简介，点击右上角的“完成”，即可成功创建群组。</p>
				<i class="iconfont" style="display: none">&#xe63d;</i>
				<div style="clear: both"></div>
			</li>
			<li>2. 查找并加入群组
				<i class="iconfont">&#xe640;</i>
				<p>进入“群组”界面，点击右上角三个小圆圈标志，选择“查找群”，输入任意一个字或多个字即可搜索您需要的群组，然后选择“加入该群”。</p>
				<i class="iconfont" style="display: none">&#xe63d;</i>
				<div style="clear: both"></div>
			</li>
			<li>3. 添加或删除群成员
				<i class="iconfont">&#xe640;</i>
				<p>进入“群组”界面，点击进入已创建或已加入的群组，点击右上角三条横线的标志，进入群资料，点击“成员”一栏，可以查看已有成员，群主可以选择单个成员向左滑动该栏，右侧出现删除按钮点击可以删除该成员；在群资料中，点击“添加群成员”一栏，勾选手机中已有的奔犇好友，点击右下角的“添加”按钮，即可完成添加。</p>
				<i class="iconfont" style="display: none">&#xe63d;</i>
				<div style="clear: both"></div>
			</li>
			<li>4. 退出群组
				<i class="iconfont">&#xe640;</i>
				<p>进入“群组”界面，点击进入已创建或已加入的群组，点击右上角三条横线的标志，进入群资料，如果是群主就点击下方“解散群组”按钮即可退出该群，如果是群成员就点击正下方“退出该群”即可退出该群。</p>
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