<?php
/* @var $this BxapplyController */
/* @var $dataProvider CActiveDataProvider */
Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/themes/css/global.css");

$status = array("0"=>"等待审核", "1"=>"未通过", "2" => "退回重申", "3" => "已经通过", "4" => "撤消");
?>


<?php
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/themes/js/jquery-1.11.1.min.js");
?>
<script type="text/javascript">
	$(function(){
		var page = <?php echo $pages->pageCount?>;
		
	});
</script>

		<div class="main_right_content">
		<div class="main_right_content_title main_titleab">
			<div class="main_right_content_title_font">导入确认</div>
			<div class="main_right_content_content_block_action_add">
			</div>
		</div>
		<div class="main_right_content_content" style="background:#F5F5F5;">
			<form action="<?php echo Yii::app()->createUrl('bxapply/savedate')?>" method="post">
		<?php if($msg) {?>
				<div class="alert alert-danger alert-dismissible" role="alert">
					  <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					  <strong>警告！</strong> <?php echo $msg;?>
				</div>
				<?php }?>
			<table cellspacing=1 border="0" class="table table-hover">
				<thead>
					<tr class="main_right_content_content_title">	
						<td width="5%">编号</td>	
						<td width="5%">类型</td>					
						<td width="10%">手机号码</td>
						<td width="6%">百姓网号</td>
						<td width="7%">姓名</td>
						<td width="12%">身份证</td>
						<td width="12%">地区</td>
						<td width="7%">提交人</td>
						<td width="8%">提交人手机号</td>
						<td width="12%">申请时间</td>
						<td width="8%">审核状态</td>	
					</tr>
				</thead>
				<tbody>
					<?php 
					foreach ( $result  as $k => $item ) {
					
					?>
						<tr class="main_right_content_content_body <?php if($item['status'] == 4) echo 'danger';?>" >										
										<td><?php echo $k+1 ?></td>
										<td><?php if($item['type'] ==1 ) echo '更新';else echo '新增';?></td>
										<td><?php echo $item['phone'];if($item['phoneis_update'] == 0) echo "<span style='color:red'><br />长号重复</span>"; ?></td>
										<td><?php echo $item['short_phone'];if($item['is_update'] == 0) echo "<span style='color:red'><br />短号重复</span>"; ?></td>
										<td ><?php  echo $item['name'];  ?></td>
										<td ><?php  echo $item['idcard'];  ?></td>
										<td><?php echo $item['provinceName'].'-'.$item['cityName'].'-'.$item['areaName']; ?></td>
										<td ><?php  echo $item['putName'];  ?></td>
										<td ><?php  echo $item['putPhone'];  ?></td>
										<td><?php echo date("Y-m-d H:i:s", $item['submitTime']) ?></td>
										<td><?php echo $status[$item['status']]; ?></td>
				
				</tr>
				<?php } ?>				</tbody>
				
			</table>
			<div class="form-group form-group-center">
				<button class="btn btn-success btn-lg btn-lg1" type="submit">　确定　</button>
				<a class="btn btn-default btn-lg backurl" href="javascript:goBack()">　取消　</a>
			</div>
			<input type="hidden" name="data" value='<?php echo json_encode($result);?>'/>
		</form>
		</div>
	</div>
	
</div>

	
