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
		<h1>账号与登录</h1>
		<ul>
			<li>1. 首次登录注册奔犇用户
				<i class="iconfont">&#xe640;</i>
				<p>在奔犇首页点击左下角“新用户”，接着输入您的手机号，然后收到提示“验证码发送成功”，您的手机会收到一个注册奔犇的验证码，将验证码输入您的手机后点击右上角“下一步”。</p>
				<i class="iconfont" style="display: none">&#xe63d;</i>
				<div style="clear: both"></div>
			</li>
			<li>2. 完善用户资料
				<i class="iconfont">&#xe640;</i>
				<p>请输入您的昵称和年龄，设置6-16位登录密码，再重复设置一次登录密码，选择性别，最后点击右上角“完成”。</p>
				<i class="iconfont" style="display: none">&#xe63d;</i>
				<div style="clear: both"></div>
			</li>
			<li>3. 用户登录
				<i class="iconfont">&#xe640;</i>
				<p>回到首页后，按提示输入手机号或奔犇号，输入密码，点击“登录”按钮即可登录。</p>
				<i class="iconfont" style="display: none">&#xe63d;</i>
				<div style="clear: both"></div>
			</li>
			<li>4. 忘记密码
				<i class="iconfont">&#xe640;</i>
				<p>如果您忘记了密码，请点击登录界面右下角“忘记密码？”，然后输入您的手机号码，收到验证码后输入点击下一步，重新设置6-16位的新密码，重复设置2次，点击完成即可回到登录界面。</p>
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