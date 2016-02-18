<!DOCTYPE html>
<html>
<head>
    <title>抽奖</title>
    <meta charset="utf-8"/>
	<meta name="viewport" content="width=device-width,initial-scale=1,target-densitydpi=high-dpi" />
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/themes/css/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/themes/css/bootstrap/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/themes/css/bootstrap/bootstrap-theme.css">
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/themes/css/lottery.css">
    <script src="http://g.tbcdn.cn/mtb/lib-flexible/0.3.4/??flexible_css.js,flexible.js"></script>

</head>
<body style="margin: 0 auto;">
	<!-- banner start -->
	<div class="banner">
		<img src="<?php echo Yii::app()->request->baseUrl; ?>/themes/images/bg11.png">
	</div>
	<!-- active start -->
	<div class="active">
		<!-- overlay start -->
			<div class="overlay" id="overlay-prize">
			 	<div class="close"><a href="#" class="rule-close"><img src="<?php echo Yii::app()->request->baseUrl; ?>/themes/images/close.png"></a></div>
			 	<div class="info" >
			 		<img class="prize" id="prize-image" src="<?php echo Yii::app()->request->baseUrl; ?>/themes/images/money.png">
			 		<p class="prize-desc"><span id="prize-award">恭喜你获得1犇币!</span></p>
			 		<p>&nbsp</p>
			 		<button class="share-btn btn-success">赶紧炫耀一下</button>
			 	</div>
		</div>
		<!-- overlay end -->

		<!-- lottery-help start -->
		<div class="lottery-help">
				<span>本次抽奖将消耗<span>0.5</span>个犇币</span>
				<a href="">中奖记录</a>
		</div>
		<!-- lottery-help end -->
	<div class="container">
		<div class="lottery" id="lottery" >
	
			<div class="row">
				<div class="lottery-unit lottery-unit-0">
					<img id="first" class="front" src="<?php echo Yii::app()->request->baseUrl; ?>/themes/images/front5.png">
					<a class="back"  href="#"><img  src="<?php echo Yii::app()->request->baseUrl; ?>/themes/images/back.png"></a>
				</div>
				<div class="lottery-unit lottery-unit-1">
					<img class="front" src="<?php echo Yii::app()->request->baseUrl; ?>/themes/images/front5.png">
					<a class="back"  href="#"><img src="<?php echo Yii::app()->request->baseUrl; ?>/themes/images/back.png"></a>
				</div>
				<div class="lottery-unit lottery-unit-2">
					<img class="front" src="<?php echo Yii::app()->request->baseUrl; ?>/themes/images/front2.png">
					<a class="back"  href="#"><img src="<?php echo Yii::app()->request->baseUrl; ?>/themes/images/back.png"></a>
				</div>
			</div>
			<div class="row">
				<div class="lottery-unit lottery-unit-3">
					<img class="front" src="<?php echo Yii::app()->request->baseUrl; ?>/themes/images/front1.png">
					<a class="back"  href="#"><img src="<?php echo Yii::app()->request->baseUrl; ?>/themes/images/back.png"></a>
				</div>
				<div class="lottery-unit lottery-unit-4" >
				<div class="start">
					<img class="front" src="<?php echo Yii::app()->request->baseUrl; ?>/themes/images/middle.png">
						<img class="startbtn" src="<?php echo Yii::app()->request->baseUrl; ?>/themes/images/startbtn.png"><p class="money"><span>犇币余额:<span id="coin"></span></span></p>
				</div>
						
					<a class="back" href="#"><img src="<?php echo Yii::app()->request->baseUrl; ?>/themes/images/back.png"></a>
				</div>
				<div class="lottery-unit lottery-unit-5">
					<img class="front" src="<?php echo Yii::app()->request->baseUrl; ?>/themes/images/front05.png">
					<a class="back" href="#"><img src="<?php echo Yii::app()->request->baseUrl; ?>/themes/images/back.png"></a>
				</div>
			</div>
			<div class="row">
				<div class="lottery-unit lottery-unit-6">
					<img class="front" src="<?php echo Yii::app()->request->baseUrl; ?>/themes/images/front03.png">
					<a class="back"  href="#"><img src="<?php echo Yii::app()->request->baseUrl; ?>/themes/images/back.png"></a>
				</div>
				<div class="lottery-unit lottery-unit-7">
					<img class="front" src="<?php echo Yii::app()->request->baseUrl; ?>/themes/images/front02.png">
					<a class="back"  href="#"><img src="<?php echo Yii::app()->request->baseUrl; ?>/themes/images/back.png"></a>
				</div>
				<div class="lottery-unit lottery-unit-8">
					<img class="front" src="<?php echo Yii::app()->request->baseUrl; ?>/themes/images/front01.png">
					<a class="back"  href="#"><img src="<?php echo Yii::app()->request->baseUrl; ?>/themes/images/back.png"></a>
				</div>
			</div>
		</div>
	</div>
	<!-- activity explain start -->

	<div class="container">
	<div class="row explain">
		<div>
			<span>活动细则</span>
		</div>
		<div class="desc">
			<img src="<?php echo Yii::app()->request->baseUrl; ?>/themes/images/desc.png">
			<div class="desc-ul">
				<ul class="description">
					<li>每次抽奖消耗0.5个犇币</li>
					<li>虚拟奖品为犇币，中奖后由后台直接充值;实物奖品，中奖后联系后台工作人员领奖</li>
				</ul>
				<ul class="foot">本活动最终解释权归奔犇所有</ul>
			</div>

		</div>
	</div>
	</div>

	</div>
	

</body>
    <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/themes/js/jquery2.js"></script>
    <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/themes/js/jquery.flip.min.js"></script>
	
	<script type="text/javascript">
	$(document).ready(function(){
		// 获取benben_id
		// function GetRequest() {
		//   var url = location.search; //获取url中"?"符后的字串
		//    var theRequest = new Object();
		//    if (url.indexOf("?") != -1) {
		//       var str = url.substr(1);
		//       strs = str.split("&");
		//       for(var i = 0; i < strs.length; i ++) {
		//          theRequest[strs[i].split("=")[0]]=(strs[i].split("=")[1]);
		//       }
		//    }
		//    return theRequest;
		// }
		// var Request = new Object();
		// Request = GetRequest();
		// alert(GetRequest[]);
		
		//初始化 页面
		var initUrl="http://localhost:8080/wtz/benben/website/admin.php/lottery/InitLottery?benben_id=10006";
		$.post(initUrl,{},function(data){
			$("#coin").html(data.coin);
			$("#first").attr("src","http://localhost:8080/wtz/benben/website/"+data.prize);

		},'json');

		//点击开始抽奖
		$(".startbtn").click(function(){
			$(".start").hide();
			$(".front").flip({
				direction: 'rl',
				content: '',
				speed: 200,
				onEnd: function(){
					$(".front").hide();
					$(".back").show();
				}
			});

		});
		//点牌
		$(".back").on("click",function(){
			//发送请求 
			var url="http://localhost:8080/wtz/benben/website/admin.php/lottery/DrawLottery?benben_id=10006";
			$.post(url, {	"benben_id":10006,	
    				}, function(data){
			            if (data.status!=0) {
			            	//成功
			            	console.log(data.img_url);
			            	console.log(data.benben_id);
			            	console.log(data.coin);
			            	console.log(data.message);
			            	$("#prize-image").attr("src","<?php echo Yii::app()->request->baseUrl; ?>/themes/"+data.img_url);
			            	$("#prize-award").html(data.message);			                	
			            }else{
			               alert("犇币余额不足，请充值！");
			               location.reload();
			            };
        				},'json');
						//翻牌显示
						$(this).parent().flip({
							direction: 'rl',
							content: '',
							speed: 500,
							onEnd: function(){
								$("#overlay-prize").show();
							}
						});
        });
		//点击关闭
		$(".close").on("click",function(){
            $("#overlay-prize").hide();
            location.reload();
        });

	});
		
	</script>
</html>