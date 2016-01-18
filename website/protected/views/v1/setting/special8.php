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
		<h1>我的好友联盟</h1>
		<ul>
			<li>1. 创建我的好友联盟
				<i class="iconfont">&#xe640;</i>
				<p>在“好友联盟”的界面，可以查看到你已创建或已加入的好友联盟列表，点击右上角三个小圆圈的按钮，然后选择“创建好友联盟”，会跳转到“新建好友联盟”的页面。接着上传您的好友联盟头像（照片可以立刻拍照，也可以从相册中选择），填写好友联盟名字（2-10字），选择地区，填写联盟简介，点击右上角“完成”按钮即可</p>
				<i class="iconfont" style="display: none">&#xe63d;</i>
				<div style="clear: both"></div>
			</li>
			<li>2. 解散我的好友联盟
				<i class="iconfont">&#xe640;</i>
				<p>在“好友联盟”的界面，选择自己创建的好友联盟，点击后进入“好友联盟详情”页面，可以选择修改您的好友联盟头像、名字和联盟简介，可以发布联盟公告，管理联盟成员，也可以点击页面下方的“解散联盟”红色按钮来解散联盟。</p>
				<i class="iconfont" style="display: none">&#xe63d;</i>
				<div style="clear: both"></div>
			</li>
			<li>3. 添加成员和邀请堂主
				<i class="iconfont">&#xe640;</i>
				<p>选择创建的好友联盟，点击“联盟成员”，进入后该页面显示盟主（您本人）、堂主和普通成员。点击右上角“。。。”按钮，会跳出提示框让你选择“添加成员”还是“邀请堂主”。“添加成员”界面会显示通讯录内所有的奔犇用户，你可以单选、多选或全选用户，然后点击右下方“添加”按钮，即可添加成员。“邀请堂主”界面会显示通讯录内所有的奔犇用户，你可以单选、多选或全选用户，然后点击右下方“邀请”按钮，即可邀请堂主，目前只能邀请3个堂主。</p>
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