<?php
/* @var $this BxapplyController */
/* @var $dataProvider CActiveDataProvider */
Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/themes/css/jquery-ui-timepicker-addon.css");
Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/themes/css/global.css");


$path = substr(dirname(__FILE__), 0, -7)."layouts/area.php";
require_once($path);  

$status = array("0"=>"等待审核", "1"=>"未通过", "2" => "退回重申", "3" => "已经通过");
?>

		<div class="main_right_content">
		<div class="main_right_content_title">
			<div class="main_right_content_title_font">导出申请数据</div>
			<div class="main_right_content_content_block_action_add">
			</div>
		</div>
		<div class="main_right_content_content" style="background:#F5F5F5;">
		<?php if($msg) {?>
				<div class="alert alert-danger alert-dismissible" role="alert">
					  <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					  <strong>警告！</strong> <?php echo $msg;?>
				</div>
				<?php }?>
			<form action="<?php echo Yii::app()->createUrl('bxapply/phpexcel')?>" method="get">
			<ul style="margin-top:0;">							
				<li style="width:100%;">
					<div class="form-group" style="padding:0 0 0 5px;">
						<label  for="benben_id" style="float:left;margin-top:7px;">开始时间:</label>	
						<div class="col-sm-2" style="margin-bottom:10px;">
							<input type="text" class="form-control datetimepicker" name="created_time1" id="created_time1" value="<?php echo $result['created_time1']?>">
						</div>
					</div>
				</li>
				<li style="width:100%;">
					<div class="form-group" style="padding:0 0 0 5px;">
						<label  for="benben_id" style="float:left;margin-top:7px;">结束时间:</label>	
						<div class="col-sm-2" style="margin-bottom:10px;">
							<input type="text" class="form-control datetimepicker" name="created_time2" id="created_time2" value="<?php echo $result['created_time2']?>">
						</div>
					</div>
				</li>
				
				<li style="width:100%;">
					<div class="form-group" style="padding:0 0 0 5px;">
						<label  for="sex" style="float:left;margin-top:7px;">审核状态:</label>	
						<div class="col-sm-2" style="margin-bottom:10px;">
							<select class="form-control" name="status" id="status">								
								<option value="-1" >--请选择--</option>
								<option value="0" <?php if($result['status'] == 0 && isset($result['status'])) echo 'selected = "selected"';?>>等待审核</option>								
								<option value="1" <?php if($result['status'] == 1) echo 'selected = "selected"';?>>未通过</option>
								<option value="2" <?php if($result['status'] == 2) echo 'selected = "selected"';?>>退回重申</option>
								<option value="3" <?php if($result['status'] == 3) echo 'selected = "selected"';?>>已经通过</option>
								<option value="4" <?php if($result['status'] == 4) echo 'selected = "selected"';?>>撤销</option>
							</select>
						</div>
					</div>
				</li>
				
				<li style="width:100%;">
					<div class="form-group" style="padding:0 0 0 5px;">
						<label  for="sex" style="float:left;margin-top:7px;">有身份证照片:</label>	
						<div class="col-sm-2" style="margin-bottom:10px;margin-top:7px;">
							<input type="checkbox"  name="isposter" value="1">
						</div>
					</div>
				</li>
				
	<li style="text-align:right;width:180px;padding-right:16px;">
		<input type="submit" class="btn btn-primary" value="导出EXCEL" id="submit_search">
	</li>
			</ul>
			</form>
		

		</div>
	</div>
	
</div>

<script src='<?php echo Yii::app()->request->baseUrl."/themes/js/jquery-ui-timepicker-addon.js"?>'></script>
<script src='<?php echo Yii::app()->request->baseUrl."/themes/js/jquery-ui-timepicker-zh-CN.js"?>'></script>
<script>
$('.datetimepicker').datetimepicker({
    timeFormat: "HH:mm:ss",
    dateFormat: "yy-mm-dd"
});
</script>

	
