<link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/css/Personal_6.css" />
<script type="text/javascript" src="<?php echo Yii::app()->baseUrl.'/themes/enterprise/js/jquery.JPlaceholder.js'?>"></script>
<script type="text/javascript" src="<?php echo Yii::app()->baseUrl.'/themes/enterprise/independent/js/aboutus.js'?>"></script>
<div class="p-main-r fl" id="p-main-r">
				
<!-- 				<div class="bb_int"> -->
<!-- 					<span class="bb_introduce">管理员必看</span> -->
<!-- 					<span class="bb_service tab_con">服务协议</span> -->
					
<!-- 					<div class="clear"></div> -->
					   
<!-- 				</div> -->
					<!--end-->
				<div class="main_con hide_con_1">
					<p class="bb_con">管理员必看</p>
<?php echo $content;?>
				</div>




				<div class="main_con hide_con_2 hide">
					<p class="bb_con">政企通讯录会员服务协议</p>
					<div style="margin:25px;"><?php echo $item2->content;?></div>
				</div>
			</div>

	