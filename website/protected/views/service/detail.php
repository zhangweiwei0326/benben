		<head>
			<style>
				.shop-time1{
					font-size: 16px;
					font-family: arial;
					color: #969696;
				}
				.dialog-box-content{text-align: center;height:auto;};
			</style>
			<script type="text/javascript" src="/themes/js/shopdetail.js" ></script>
			<script type="text/javascript" src="/themes/js/shopdate.js" ></script>
			<script type="text/javascript">
				var _price="";
				var _coin="";
				var _fee="";
				$(function(){

					var oh = $(window).height();
					var otop = $('.shop-mian').offset().top;
					var oDh = oh-otop-1;

					if(oDh>720){
						$('.shop-mian').css('height',oDh);
					}

					var oft = $('.duration .shop-num font');
					var oIput = $('.shop-num-other input');
					var va = <?php echo $detail->duration_price ?>;
					var va1 = va;
					var type = <?php echo $detail->info->type ? $detail->info->type : 0; ?>;
					oft.click(function(){
						oft.removeClass('shop-num-k');
						if(va == 0){
							va1 = $(".names_money").val();
						}
						if(!va1){
 					//va1 = 0;
 					//$(this).removeClass('shop-num-k');
 					alert("请选择套餐");return;
 				}
 				$(this).addClass('shop-num-k');
 				$(".duration_aa").val($(this).attr("data"));
 				$(".service_duration").val($(this).attr("data"));
 				<?php if (!in_array($detail->info->type, array(10, 12, 13))) {?>
 					calculate_time($(this).attr("data"));
 					<?php }
 					?>
 					calculate_jin($(this).attr("data"),va1);
 				});
					oIput.focus(function(){
						oft.removeClass('shop-num-k');
					});

					var oft1 = $('.names .shop-num font');
					oft1.click(function(){
						oft1.removeClass('shop-num-k');
						$(this).addClass('shop-num-k');
						var abc =$(this).attr("showtime");
						if($(".shop-time").attr("data") !=0){
							$(".shop-time").attr("data",abc);
							$(".shop-time").html(new Date(parseInt(abc)*1000).format('yyyy-MM-dd'));
						}

						var arr1 = new Array();
						arr1[0] = 10;
						arr1[1] = 12;
						arr1[2] = 13;
						if($.inArray(parseInt($(".service_type").val()),arr1)<0){
							if($(".duration_aa").val()){
								calculate_time($(".duration_aa").val());
							}
						}

						$(".service_name").val($(this).html());
						if(va == 0){
							$(".names_money").val($(this).attr("money"));
							$(".a_num").html($(this).html());
							$(".a_num1").html($(this).attr("big_horn"));
							if($(this).attr("sale_consultant")>0){
								$(".a_num2").html($(this).attr("sale_consultant"));
								$(".a_num2").parent("p").show();
							}else{
								$(".a_num2").parent("p").hide();
							}
						//$(".a_num2").html($(this).attr("sale_consultant"));
						var names = $(this).attr("money");
						var data = 0;
						if($(".duration_aa").val()){
							data = $(".duration_aa").val();
						}
						calculate_jin(data,names);
					}

				});

				
					function calculate_time(date){
						date = parseInt(date);
						var date1 = $(".shop-time").attr("data");
						var myDate=new Date(parseInt(date1)*1000);
						var interval = "m";
						if(date%12 == 0){
							var interval = "y";
							date = date/12;
						}
					var duration = DateAdd(interval,date,myDate);//alert(duration.format('yyyy-MM-dd'));
					$(".shop-time").html(duration.format('yyyy-MM-dd'));

				}

				function calculate_time1(date){
					var myDate=new Date();
					var date1 = $(".shop-time").attr("data");
					var duration0 = parseInt(date1)*1000 + (date*30*24*60*60*1000);
					var duration = myDate.setTime(duration0);
					var str = getLocalTime(duration).split(" ");
					$(".shop-time").html(str[0]);

				}
				function calculate_jin(date,va){
					date = parseInt(date);
					va = parseInt(va);
					if(type == 11){
						var nu = <?php echo $store_num ? $store_num : 0 ?>;
						var jin = date*(va+(nu*20));
					}else if(type == 10){
						var re2 = <?php echo $detail->re2 ? $detail->re2 : 1; ?>;
						var jin = date*va*re2;
						jin = jin.toFixed(2);
					}else{
						var jin = date*va;
					}
					$(".shop-money font").html(jin);

				}
				function getLocalTime(nS) {
					return new Date(parseInt(nS)).toLocaleDateString().replace(/年|月/g, "-").replace(/日/g, " ");
				}
			});

	</script>
	<script src="/jQuery-dialogBox/js/jquery.dialogBox.js"></script>
	<link rel="stylesheet" type="text/css" href="/jQuery-dialogBox/css/jquery.dialogbox.css">

</head>
<body style="background: #e4e7ee;">
	<!--sever-->
	<div class="i-shop">
		<h2 class="shop-title">
			<span><a href="/index.php/service">服务开通</a></span>
			<font>></font>
			<i><a href=""><?php echo $detail->info->title ?></a></i>
		</h2>
		<div class="shop-mian">

			<dl class="i-shop-topt">
				<dt><img width="140px" height="140px" src="<?php echo $detail->info->poster ?>"/></dt>
				<dd>
					<span><?php echo $detail->info->title ?></span>
					<p><?php echo $detail->content_title ?></p>
				</dd>
			</dl>
			<div class="i-shop-check">
				<dl class="names">
					<dt>选择套餐：</dt>
					<dd>
						<p class="shop-num">
							<?php $i = 1;foreach ($detail->names as $va) {
								if ($va['show']) {
									?>
									<font <?php if ($i == 6) {
										echo 'style="margin-top: 20px;"';
									}
									?>big_horn="<?php echo $va['big_horn'] ?>" sale_consultant="<?php echo $va['sale_consultant'] ?>" money="<?php echo $va['money'] ?>" data="<?php echo $detail->info->type ?>" class="<?php echo $va['class'] ?>" show="<?php echo $va['show1'] ?>" showtime="<?php echo $va['show_time'] ?>"><?php echo $va['num'] ?></font>
									<?php $i++;}}
									?>
								</p>
							</dd>
						</dl>
						<?php if (in_array($detail->info->type, array(1, 11))) {?>
						<dl>
							<dt>门店数量：</dt>
							<dd>
								<p class="shop-time1" ><?php echo $detail->vip_info->store_num ? $detail->vip_info->store_num : 0 ?></p>
							</dd>
						</dl>
						<?php }
						?>
						<input class="names_money" type="hidden"/>
						<input class="duration_aa" type="hidden"/>
						<dl class="duration">
							<dt><?php if (in_array($detail->info->type, array(12, 13))) {echo "购买数量：";} else if ($detail->info->type == 10) {echo "开通数量：";} else {echo "开通时长：";}
								?></dt>
								<dd>
									<p class="shop-num">
										<?php foreach ($detail->duration as $key => $va) {?>
										<font data="<?php echo $key ?>"><?php echo $va ?></font>
										<?php }

										?>
									</p>
								</dd>
							</dl>
							<dl>
								<dt>有 效 期：</dt>
								<dd>
									<p class="shop-time" data="<?php echo $duration1 ?>"><?php echo $duration ?></p>
								</dd>
							</dl>
							<dl style="margin-bottom: 18px;">
								<dt><?php if (in_array($detail->info->type, array(12, 13, 14))) {echo "适用人群：";} else {echo "套餐内容：";}
									?></dt>
									<dd>
										<?php foreach ($detail->content as $key => $va) {
											?>
											<p <?php if (($detail->info->type == 11) && ($key == 2)) {
												echo 'style="display:none"';
											}
											?> class="shop-content"><?php echo $va ?></p>
											<?php }
											?>
										</dd>
									</dl>
									<dl>
										<dt style="padding-top: 10px;">应付金额：</dt>
										<dd style="height: 41px; line-height: 41px;"><span class="shop-money"><font>0</font>元</span></dd>
									</dl>
									<form class="pay_detail" action="/index.php/pay/servicedetail" method="post">
										<input name="service_type" type="hidden" class="service_type" value="<?php echo $detail->info->type ?>"/>
										<input name="service_name" type="hidden" class="service_name"/>
										<input name="service_duration" type="hidden" class="service_duration"/>
										<!-- <input class="vip_price" type="hidden" value="<?php echo $detail->vip_price; ?>" > -->
										<input  name="use_coin" class="use_coin" type="hidden" value="0">
										<input  name="use_fee" class="use_fee" type="hidden" value="0">
										<!-- <input class="coin" type="hidden" value="<?php echo $coin; ?>"> -->
									</form>
									<div id="btn-dialogBox"></div>
									<em class="shop-open"><a href="javascript:void(0);"><img src="/themes/images/shop_7.jpg"/></a></em>
								</div>

							</div>
						</div>

					</div>
					<script>
						$(function(){
							var store = <?php echo intval($_GET["store"]); ?>;
							if(store ==2){
								alert("犇币或余额填写错误！");
								window.history.back(-1);
							}
							if(store ==1){
								alert("请选择套餐或时长！");
								
							}
							if(store ==3){
								alert("应付金额小于当前拥有服务的折算金额，不能购买该服务，请购买其他服务套餐！");
								window.history.back(-1);

							}
							$('.shop-num-k').click();
						});
					</script>
				</body>
