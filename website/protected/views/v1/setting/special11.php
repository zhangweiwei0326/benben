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
		<h1>设置</h1>
		<ul>
			<li>1. 通讯录同步
				<i class="iconfont">&#xe640;</i>
				<p>点击进入我的-设置-通讯录同步界面，显示的是你手机通讯录的全部联系人，你可以单选、多选或全选联系人，点击右上角的“同步”图标，系统就会把手机自带通讯录联系人同步到奔犇的通讯录里面。</p>
				<i class="iconfont" style="display: none">&#xe63d;</i>
				<div style="clear: both"></div>
			</li>
			<li>2. 变更注册手机号码
				<i class="iconfont">&#xe640;</i>
				<p>点击进入我的-设置-变更注册手机号码界面，请填写新的手机号，点击右上角的“下一步”图标，此奔犇号码就绑定了新的手机号，下次登录可使用绑定的新手机号登录。</p>
				<i class="iconfont" style="display: none">&#xe63d;</i>
				<div style="clear: both"></div>
			</li>
			<li>3. 修改密码
				<i class="iconfont">&#xe640;</i>
				<p>点击进入我的-设置-修改密码界面，请输入1次旧密码，再输入2次新密码，然后点击右上角的“提交”图标，即可完成密码修改。</p>
				<i class="iconfont" style="display: none">&#xe63d;</i>
				<div style="clear: both"></div>
			</li>
			<li>4. 清理缓存
				<i class="iconfont">&#xe640;</i>
				<p>点击进入我的-设置-清理缓存界面，只要点击底部“清理缓存”按钮，就会弹出一个提示框“缓存清理成功”。</p>
				<i class="iconfont" style="display: none">&#xe63d;</i>
				<div style="clear: both"></div>
			</li>
			<li>5. 投诉建议
				<i class="iconfont">&#xe640;</i>
				<p>点击进入我的-设置-投诉建议界面，点击白色底板后就可以输入文字，填写您的投诉建议，然后点击右上角的“提交”图标，就会弹出一个提示框“我们已收到您的建议，将会尽快处理！”。</p>
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