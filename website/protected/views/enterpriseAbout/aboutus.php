<link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/css/Personal_6.css" />
<script type="text/javascript" src="<?php echo Yii::app()->baseUrl.'/themes/enterprise/js/jquery.JPlaceholder.js'?>"></script>
<script type="text/javascript" src="<?php echo Yii::app()->baseUrl.'/themes/enterprise/independent/js/aboutus.js'?>"></script>
<script type="text/javascript">
$(function(){
	$('.pm-lul-li2').trigger('click');
	$('.has_aboutus').css('background','#31AAE1');
	$('.has_aboutus').find('a').css('color','#fff');
})
</script>
<div class="p-main-r fl"  id="p-main-r">
				
				<div class="bb_int">
					<span class="bb_introduce">奔犇介绍</span>
					<span class="bb_service tab_con">服务协议</span>
					
					<div class="clear"></div>
					   
				</div>
					<!--end-->
				<div class="main_con hide_con_1">
					<p class="bb_con">奔犇介绍</p>
					<ul class="bb_con_ful">
						<li class="bb_f_li">
							<p><img src="<?php echo Yii::app()->baseUrl;?>/themes/enterprise/images/about_us_logo2.png"></p>
							<div class="bb_con_txt">
							<?php echo $item1->content;?>
							</div>
						</li>
						<li class="bb_l_li">
							<img src="<?php echo Yii::app()->baseUrl;?>/themes/enterprise/images/about_us_logo.png">
						</li>
					</ul>
					<p><img src="<?php echo Yii::app()->baseUrl;?>/themes/enterprise/images/about_us.png"></p>

<?php echo $content3;?>
				</div>




				<div class="main_con hide_con_2 hide">
					<p class="bb_con">政企通讯录会员服务协议</p>
					<div style="margin:25px;"><?php echo $item2->content;?></div>
				</div>
			</div>

	</div>