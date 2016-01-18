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
		<h1>百姓网进度查询</h1>
		<ul>
			<li>1. 申请加入百姓网
				<i class="iconfont">&#xe640;</i>
				<p>在本页面，填写手机号（默认注册手机号码）、姓名、身份证号码和地址，然后上传身份证原件的正面照和反面照（照片可以立刻拍照，也可以从相册中选择），查看《东阳百姓网入网协议》后点击右上角的“提交”按钮，会弹出提示框“提交成功，请等待我们的审核”。提交申请后，在本页面会多出一个 “状态”栏目，显示“待审核”，如果已经通过，就会显示“已通过”。</p>
				<i class="iconfont" style="display: none">&#xe63d;</i>
				<div style="clear: both"></div>
			</li>
			<li>2. 将好友加入百姓网
				<i class="iconfont">&#xe640;</i>
				<p>在本页面，显示的是你手机通讯录的全部联系人，你可以单选、多选或全选联系人，点击右上角的“下一步”图标，进入“将好友加入百姓网”页面，点击联系人名字输入真实姓名，再选择改联系人所在地区，接着对每个联系人重复操作以上2个步骤，最后点击右上角的“完成”图标，会弹出提示框“我们已收到您的申请，将尽快处理！”。</p>
				<i class="iconfont" style="display: none">&#xe63d;</i>
				<div style="clear: both"></div>
			</li>
			<li>3. 百姓网进度查询
				<i class="iconfont">&#xe640;</i>
				<p>在本页面，共有5个模块选择查看，当您处在某个模块时，该模块标题就会以蓝色显示，其余4个模块标题就会以灰色显示。5个模块分别是：全部、待审核、未通过、退回重申和通过。在“全部”模块界面下，会显示所有你已提交的百姓网申请信息，每个信息内容包括联系人名字、手机号码、所在地址、提交时间和审核状态（审核状态包括待审核、未通过、退回重申和通过）。在其余4个模块界面下，就是显示相应审核状态的已提交的百姓网申请信息，每个信息内容与“全部”模块下的信息以同样方式显示。</p>
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