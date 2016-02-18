<!--sever-->
<div class="s-sever-cont">
	<em class="s-sever-title"><img src="/themes/images/ser_1.png"/></em>
</div>
<div class="s-sever-main">
	<dl style="margin-left: 30px;">
		<dt><a href="<?php echo $url."0";?>"><img src="/themes/images/ser_2.png" alt=""/></a></dt>
		<dd>
			<span><a href="<?php echo $url."0";?>">促销</a></span>
			<p>让全世界都知道你在做促销</p>
		</dd>
	</dl>
	<dl>
		<dt><a href="<?php echo $url."1";?>"><img src="/themes/images/ser_3.png" alt=""/></a></dt>
		<dd>
			<span><a href="<?php echo $url."1";?>">团购</a></span>
			<p>团购在手，生意暴走</p>
		</dd>
	</dl>
	<dl>
		<dt><a href="<?php echo $url."11";?>"><img src="/themes/images/ser_4.png" alt=""/></a></dt>
		<dd>
			<span><a href="<?php echo $url."11";?>">会员号</a></span>
			<p>老用户不会走，新用户不用愁</p>
		</dd>
	</dl>
	<dl>
		<dt><a href="<?php echo $url."10";?>"><img src="/themes/images/ser_5.png" alt=""/></a></dt>
		<dd>
			<span><a href="<?php echo $url."10";?>">我要开分店</a></span>
			<p>连锁保障，任性扩张</p>
		</dd>
	</dl>
	<!--2 列-->
	<!--<dl style="margin-left: 30px;">
		<dt><a href="#"><img src="/themes/images/ser_6.png" alt=""/></a></dt>
		<dd>
			<span><a href="#">政企通讯录</a></span>
			<p>再也不用担心小伙伴们失联</p>
		</dd>
	</dl>-->
	<dl style="margin-left: 30px;">
		<dt><a href="<?php echo $url."14";?>"><img src="/themes/images/ser_7.png" alt=""/></a></dt>
		<dd>
			<span><a href="<?php echo $url."14";?>">好友联盟</a></span>
			<p style="width: 180px;">联盟扩张利器</p>
		</dd>
	</dl>
	<dl>
		<dt><a href="<?php echo $url."12";?>"><img src="/themes/images/ser_8.png" alt=""/></a></dt>
		<dd>
			<span><a href="<?php echo $url."12";?>">小喇叭</a></span>
			<p style="width: 180px;">我要给小伙伴们喊话</p>
		</dd>
	</dl>
	<dl>
		<dt><a href="<?php echo $url."13";?>"><img src="/themes/images/ser_9.png" alt=""/></a></dt>
		<dd>
			<span><a href="<?php echo $url."13";?>">大喇叭</a></span>
			<p>推广新产品，一分钟搞定</p>
		</dd>
	</dl>
	<!--3 列-->
	<dl>
		<dt><img src="/themes/images/ser_10.png" alt=""/></dt>
		<dd>
			<span>敬请期待</span>
			<p style="width: 192px;">更多有趣功能</p>
		</dd>
	</dl>
</div>
<script>
	$(function(){
		var store = <?php echo $store?$store:intval($_GET["store"]);?>;
		var url = "<?php echo $url;?>";
		if(store == 1){
			alert("请开通商家会员！");
			window.history.back(-1);
			//$(".s-sever-main a").attr("href","");
		}else if(store == 2){
			alert("您已是团购会员，无需购买促销！");
			window.location.href = "/index.php/service";
			//$(".s-sever-main a").attr("href",url);
		}
	});
</script>
