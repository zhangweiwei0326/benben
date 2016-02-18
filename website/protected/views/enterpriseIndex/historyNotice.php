<link rel="stylesheet"
	href="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/css/Personal_7.css" />
<script type="text/javascript"
	src="/themes/enterprise/js/LocalResizeIMG.js"></script>
<link rel="stylesheet" type="text/css"
	href="<?php echo Yii::app()->request->baseUrl; ?>/themes/css/jquery.ui.css" />
<script type="text/javascript"
	src="<?php echo Yii::app()->request->baseUrl; ?>/themes/js/jquery.ui.js"></script>

<script>
			$(function(){
				
				$('.m-m-coe-dl i').click(function(){
					$('.m-m-coe-dl i').removeClass('chang-yes');
					$(this).addClass('chang-yes');
				});

				$(".search").click(function(){
					$("form").submit();
				})

				$(".del").click(function(){
					var access_level = document.getElementById("access_level"); 
					access_level.options[0].selected=true;   
					$("input.created_time").val("");
				})

				

			})
		</script>

<!--right-->
<div class="p-main-r fl" id="p-main-r">
	<em class="pm-r-bnn"><a href="#"><img
			src="/themes/enterprise/images/rbnner.jpg" /></a></em>
	<div class="p-mian-cont">
		<dl class="pm-r-dl">

			<dt>
				<span><a
					href="<?php echo Yii::app()->createUrl('enterpriseIndex/index')?>">首页</a></span><font>
					> </font><i>历史公告</i>
			</dt>
		</dl>
		<div class="clear"></div>
		<div class="m-member-compile">
			<h1 class="m-m-coe-title">
				<span>历史公告</span>
			</h1>


			<dl class="con_list">
				<dt class="con_list_dt1">姓名</dt>
				<dd class="con_list_sdd1">内容</dd>
				<dd class="con_list_tdd1">创建时间</dd>
				<dd class="con_list_tdd1">更新时间</dd>
				<dd class="con_list_ldd1">操作</dd>
			</dl>
			<?php
			
foreach ( $notice as $key => $con ) {
// 				if ($key > 0) {
					?>
						<dl class="con_list com">
				<dt class="con_list_dt1"><?php if(empty($con->bname)&&empty($con->sname)&&empty($con->sremark_name)){echo '&nbsp';}else{if(empty($con->bname)){echo $con->sname ?$con->sname:$con->sremark_name;}else{echo $con->bname;}}?></dt>
				<dd class="con_list_sdd1"><?php echo mb_strlen($con->content, 'utf8') > 20 ? mb_substr($con->content, 0, 36, 'utf8').'...' : $con->content;?></dd>
				<dd class="con_list_tdd1"><?php echo date('Y-m-d',$con->created_time);?></dd>
				<dd class="con_list_tdd1"><?php echo date('Y-m-d',$con->update_time);?></dd>
				<dd class="con_list_ldd1 col_bule" sender="<?php if(empty($con->bname)&&empty($con->sname)&&empty($con->sremark_name)){echo '&nbsp';}else{if(empty($con->bname)){echo $con->sname ?$con->sname:$con->sremark_name;}else{echo $con->bname;}}?>"
					content="<?php echo $con->content?>"
					created_time="<?php echo date("Y-m-d H:i",$con->created_time);?>">查看详情</dd>
			</dl>
				
				<?php } ?>
		         <div style="overflow: hidden; padding-right: 70px;">
				<div class="g-mefr-bfy">
			<?php
			$page = intval ( $_GET ['page'] ) ? intval ( $_GET ['page'] ) : 1;
			$url = explode ( "?", Yii::app ()->request->getUrl () );
			$link = Yii::app ()->request->hostInfo . $url [0] . "?";
			echo '<ul>' . $this->textPage ( $pages->pageCount, $page, $link ) . '</ul>';
			?>
			<?php
			$path = Yii::app ()->basePath . '/views/layouts/searchPages.php';
			require_once ($path);
			?>
			</div>
			</div>
			<!--分页end-->
		</div>

		<!--end-->
	</div>
</div>
</div>
<div class="export-into  hide"></div>
<div class="export-into-cent hide">
	<div class="pop_up">
		<p class="pop_p_1 pop_p">
			内容详情<img class="back" src="/themes/enterprise/images/back.png">
		</p>
		<ul class="pop">
			<li class="first_li"><span>发布人： <font class="fon sender">吕小布</font>
			</span></li>
			<li class="last_li"><span>创建时间：<font class="fon receiver">吕小豪</font></span>
			</li>
		</ul>
		<p class="connect broad_cast_content">正文</p>
		<a href="javascript:void(0);" class="comfirm broad_cast_confirm">确定</a>
	</div>
</div>
<script type="text/javascript">
$('.col_bule').click(function(){
	$('.export-into').removeClass('hide');
	$('.export-into-cent').removeClass('hide');
	var sender =$(this).attr("sender");;
	var created_time = $(this).attr("created_time");
	var content = $(this).attr('content');
	$(".sender").html(sender);
	$(".receiver").html(created_time);
	$(".broad_cast_content").html(content);
});
$('.back, .broad_cast_confirm').click(function(){
	$('.export-into').addClass('hide');
	$('.export-into-cent').addClass('hide');
	$('.new_horn').addClass('hide');
	$(".upload_img img").attr("src", "/themes/enterprise/images/add_pic.png");
	$("input[name=images]").val("");
});
</script>





























