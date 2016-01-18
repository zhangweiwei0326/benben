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
		<h1>手机好友</h1>
		<ul>
			<li>1. 手机好友分组
				<i class="iconfont">&#xe640;</i>
				<p>“手机好友”默认分为5个分组，其中包括我的同事、家人、朋友、未分组及常用号码直通车，手机首次注册登录后，手机自带的通讯录成员默认同步到未分组下。</p>
				<i class="iconfont" style="display: none">&#xe63d;</i>
				<div style="clear: both"></div>
			</li>
			<li>2. 成员管理
				<i class="iconfont">&#xe640;</i>
				<p>点击一位通讯录成员，进入联系人详情页面，里面会有成员的名字、奔犇号、手机号码，还能发送短信、拨打电话和发奔犇消息，点击右上角“编辑”按钮，会进入编辑联系人界面，可以实施选择分组、添加手机号和删除联系人等操作。</p>
				<i class="iconfont" style="display: none">&#xe63d;</i>
				<div style="clear: both"></div>
			</li>
			<li>3. 分组管理
				<i class="iconfont">&#xe640;</i>
				<p>在通讯录主界面，在家人、同事、好友、未分组等分组栏目中任意一栏长按，会弹出“分组管理”窗口，点击进入分组管理主界面，点击右上角“+”按钮是增加新的分组，点击每列分组左侧“-”按钮是删除该分组，点击分组名称右边“铅笔”按钮是修改分组的名称，点击分组右侧的“+添加新成员”就是可以从其他分组选择成员加入这个分组。</p>
				<i class="iconfont" style="display: none">&#xe63d;</i>
				<div style="clear: both"></div>
			</li>
			<li>4. 搜索添加手机好友
				<i class="iconfont">&#xe640;</i>
				<p>进入“通讯录”界面，点击右上角“添加”，搜索好友的奔犇号或昵称的一个或多个字，点击昵称的右边”加为好友”，系统会发送添加奔犇好友消息给对方，只要对方确认通过，即可成为好友，好友号码会自动添加到奔犇手机好友的未分组里面。</p>
				<i class="iconfont" style="display: none">&#xe63d;</i>
				<div style="clear: both"></div>
			</li>
			<li>5. 手动添加手机好友
				<i class="iconfont">&#xe640;</i>
				<p>进入“通讯录”界面，点击右上角“添加”按钮，进入下一界面后再点击右上角的“手动添加”按钮，输入好友手机号和好友名字，选择分组，点击右上角“添加”，即可成功添加联系人到奔犇手机好友里面。</p>
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