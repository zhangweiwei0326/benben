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
		<h1>政企通讯录</h1>
		<ul>
			<li>1. 创建企业通讯录
				<i class="iconfont">&#xe640;</i>
				<p>进入“政企通讯录”界面，点击右上角三个小圆圈图标，选择“创建通讯录”，输入通讯录名称，选择所在地区，选择通讯录类型“企业”，再填写通讯录简介，点击右上角“完成”。</p>
				<i class="iconfont" style="display: none">&#xe63d;</i>
				<div style="clear: both"></div>
			</li>
			<li>2. 创建虚拟网通讯录
				<i class="iconfont">&#xe640;</i>
				<p>进入“政企通讯录”界面，点击右上角三个小圆圈标志，选择“创建通讯录”，输入通讯录名称，选择所在地区，选择通讯录类型“虚拟网”，然后填写“您的短号”，再填写通讯录简介，点击右上角“完成”。</p>
				<i class="iconfont" style="display: none">&#xe63d;</i>
				<div style="clear: both"></div>
			</li>
			<li>3. 查找政企通讯录
				<i class="iconfont">&#xe640;</i>
				<p>进入“政企通讯录”界面，点击右上角三个小圆圈标志，选择“查找通讯录”，输入任意一个字或多个字即可搜索您需要的企业通讯录或者虚拟网通讯录。</p>
				<i class="iconfont" style="display: none">&#xe63d;</i>
				<div style="clear: both"></div>
			</li>
			<li>4. 添加或删除常用联系人
				<i class="iconfont">&#xe640;</i>
				<p>进入“政企通讯录”界面，选择一个已创建或已加入的通讯录，进入后点“搜索”，输入手机号码或名字后会跳出搜索结果，选择并长按您所需要的联系人，点完成即可成功添加为您的常用联系人。如果要删除常用联系人，只要长按选择的联系人，跳出提示框选择完成即可。</p>
				<i class="iconfont" style="display: none">&#xe63d;</i>
				<div style="clear: both"></div>
			</li>
			<li>5. 常用联系人的分组管理
				<i class="iconfont">&#xe640;</i>
				<p>进入“政企通讯录”界面，选择一个已创建或已加入的通讯录，点击右上角“详情”，在详情页选择“分组管理”一栏，进入分组管理主界面，点击右上角“+”按钮是增加新的分组，点击每列分组左侧“-”按钮是删除该分组，点击分组名称右边“铅笔”按钮是修改分组的名称，点击分组右侧的“+添加新成员”就是可以从其他分组选择成员加入这个分组。</p>
				<i class="iconfont" style="display: none">&#xe63d;</i>
				<div style="clear: both"></div>
			</li>
			<li>6. 添加新成员
				<i class="iconfont">&#xe640;</i>
				<p>进入“政企通讯录”界面，选择一个已创建或已加入的通讯录，点击右上角“详情”，在详情页选择“添加成员”，你可以搜索您要的成员，也可以单选或全选手机自带通讯录的成员，勾选后点击右下角“添加”，然后将每个成员添加备注，最后点击右上角“完成”。</p>
				<i class="iconfont" style="display: none">&#xe63d;</i>
				<div style="clear: both"></div>
			</li>
			<li>7. 退出通讯录
				<i class="iconfont">&#xe640;</i>
				<p>进入“政企通讯录”界面，选择一个已创建或已加入的通讯录，点击右上角“详情”，点击页面正下方“退出通讯录”。</p>
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