<?php
/* @var $this BxapplyController */
/* @var $dataProvider CActiveDataProvider */
Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/themes/css/global.css");

?>

		<div class="main_right_content">
		<ol class="breadcrumb main_right_content_breadcrumb">
		<li style="width:auto"><a href="<?php echo Yii::app()->createUrl('happy/index')?>">开心一刻管理</a></li>
		<li style="width:auto"><a href="javascript:void(0)">批量上传</a></li>
			<div class="main_right_content_content_block_action_add">
				<a class="btn btn-success backurl"  href="<?php echo Yii::app()->createUrl('happy/download')?>">模板下载</a>
			<a class="btn btn-success backurl" href="javascript:void(0)" goback=" -1">返回</a>
		</div>
	</ol>
		<div class="main_right_content_content" style="background:#F5F5F5;">
		<?php if($msg) {?>
				<div class="alert alert-danger alert-dismissible" role="alert">
					  <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					  <strong>提示！</strong> <?php echo $msg;?>
				</div>
				<?php }?>
			<form action="<?php echo Yii::app()->createUrl('happy/inputexcel')?>" method="post" enctype="multipart/form-data">
			<ul style="margin-top:0;">							
	
				<li style="width:100%;">
					<div class="form-group" style="padding:0 0 0 5px;">
						<label  for="sex" style="float:left;margin-top:7px;">文件:</label>	
						<div class="col-sm-2" style="margin-bottom:10px;margin-top:7px;">
							<input type="file"  name="inputfile" value="1">
						</div>
					</div>
				</li>
				
	<li style="text-align:right;width:180px;padding-right:16px;">
		<input type="submit" class="btn btn-primary" value="上传EXCEL" id="submit_search">
	</li>
			</ul>
			</form>
			
		<table cellspacing=1 border="0" class="table table-hover" style="float: left;margin-top:10px">
			<thead>
				<tr class="main_right_content_content_title">
					<td width="10%">内容(Excel格式：)</td>
				</tr>
			</thead>
			<tbody>
			<tr class="main_right_content_content_body">					
					<td>具体内容</td>
				</tr>
			</tbody>
		</table>

		</div>
	</div>
	
</div>

	
