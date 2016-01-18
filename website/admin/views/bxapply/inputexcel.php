<?php
/* @var $this BxapplyController */
/* @var $dataProvider CActiveDataProvider */
Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/themes/css/global.css");

// $path = substr(dirname(__FILE__), 0, -7)."layouts/area.php";
// require_once($path);  

$bx_status = array("0"=>"等待审核", "1"=>"未通过", "2" => "退回重申", "3" => "已经通过");
?>

		<div class="main_right_content">
		<div class="main_right_content_title main_titleab">
			<div class="main_right_content_title_font">导入数据</div>
			<div class="main_right_content_content_block_action_add">
			</div>
		</div>
		<div class="main_right_content_content" style="background:#F5F5F5;">
		<?php if($msg) {?>
				<div class="alert alert-danger alert-dismissible" role="alert">
					  <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					  <strong>提示！</strong> <?php echo $msg;?>
				</div>
				<?php }?>
			
			
			
			<form action="<?php echo Yii::app()->createUrl('bxapply/inputexcel')?>" method="post" enctype="multipart/form-data">
			<table cellspacing=1 border="0" class="table table-hover" style="margin-bottom:35px;">
				<thead>
					<tr class="main_right_content_content_title">						
						<td colspan="3" >批量导入</td>	
					</tr>	
					</thead>
					<tbody>				
					<tr align="center" class="main_right_content_content_body">
						<td align="right" width="50%"><input type="file"  name="inputfile" value="1"></td>
						<td align="left"><input type="submit" class="btn btn-primary" value="上传EXCEL" id="submit_search"></td>
						<td align="right"><a href="/admin.php/bxapply/download" class="btn btn-primary">下载模板</a></td>
					</tr>
					
					</tbody>
					</table>
					<table cellspacing=1 border="0" class="table table-hover">
					<thead>
					<tr>
						<td colspan="2">单条录入</td>
					</tr>
					</thead>
					<tr align="center" class="main_right_content_content_body">
						<td align="right" width="30%">手机号</td>
						<td align="left" >
							<input style="width:200px;float:left;margin-right:20px;" id="input_phone"  name="phone" class="form-control" type="text" value="<?php echo $re['phone']?>" >
							<span id="tips" style="color:red"></span>
						</td>
					</tr>
					<tr align="center" class="main_right_content_content_body">
						<td align="right" width="30%">状态</td>
						<td align="left" ><select class="form-control" name="status"  style="width:200px" >
								
								<option value="-1">--请选择--</option>
								<option value="0" <?php if($re['status'] == 0) echo 'selected=selected';?>>等待审核</option>
								<option value="1" <?php if($re['status'] == 1) echo 'selected=selected';?>>未通过</option>
								<option value="2" <?php if($re['status'] == 2) echo 'selected=selected';?>>退回重申</option>
								<option value="3" <?php if($re['status'] == 3) echo 'selected=selected';?>>已经通过</option>
								<option value="4" <?php if($re['status'] == 4) echo 'selected=selected';?>>撤消</option>
							</select></td>
					</tr>
					<tr align="center" class="main_right_content_content_body">
						<td align="right" width="30%">百姓网号</td>
						<td align="left" ><input style="width:200px" class="form-control" type="text" name="short_phone" value="<?php echo $re['short_phone']?>"></td>
					</tr>
					<tr align="center" class="main_right_content_content_body">
						<td align="right" width="30%">原因</td>
						<td align="left" ><textarea style="width:400px"  id="reason" class="form-control" name="reason" cols="50" rows="6"><?php echo $re['reason']?></textarea></td>
					</tr>
					<tr align="center" class="main_right_content_content_body">
						<td colspan="2"><input type="submit" class="btn btn-primary" value="提交" id="submit_search"></td>
					</tr>
					

					
				
				
				
			</table>
			</form>

		</div>
	</div>
	<input id="del_baseurl" type="hidden" value="<?php echo Yii::app()->createUrl("creation/delete",array('page'=>$pages->currentPage +1));?>" />
</div>
<script type="text/javascript">
$(function(){
	$('#input_phone').blur(function(){
		var phone = $(this).val();
		$.get('/admin.php/bxapply/getinfo', {'phone':phone},function(e){
			$('#tips').html(e);
		});
	})
})
</script>

	
