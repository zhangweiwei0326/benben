<?php
Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/themes/css/global.css");
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/themes/js/jquery-1.11.1.min.js");

/* @var $this BroadcastingLogController */
/* @var $dataProvider CActiveDataProvider */
$type = array(0=>'否',1=>'是');
?>

		<div class="main_right_content">
		<div class="main_right_content_title main_titleab">
			<div class="main_right_content_title_font">小喇叭管理 / 统计 </div>
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
				
				<form action="<?php echo Yii::app()->createUrl('notice/statistic')?>" method="get">
			<ul style="margin-top:0;">
				<li>
					<div class="form-group" style="padding:0 0 0 5px;">
						<label  for="benben_id" style="float:left;margin-top:7px;">时间段:</label>	
						<div class="col-sm-8" style="margin-bottom:10px;">
							<input type="text" class="form-control created_time" name="created_time1" id="created_time1" value="<?php echo $result['created_time1']?>">
						</div>
					</div>
				</li>
				<li>
					<div class="form-group" style="padding:0 0 0 5px;">
						<label  for="benben_id" style="float:left;margin-top:7px;">到:　</label>	
						<div class="col-sm-8" style="margin-bottom:10px;">
							<input type="text" class="form-control created_time" name="created_time2" id="created_time2" value="<?php echo $result['created_time2']?>">
						</div>
					</div>
				</li>
				
		<li style="float:right;text-align:right;width:70px;padding-right:20px;">
		<a href = "/admin.php/notice/BroadcastingLog"><div class="btn btn-primary"  id="putexcel">返回</div></a>
	</li>		
		<li style="float:right;text-align:right;width:60px;padding-right:10px;">
		<input type="submit" class="btn btn-primary" value="查询" id="submit_search">
	</li>				
				</ul>
				
			</form>
			<table cellspacing=1 border="0" class="table table-hover">
				<thead>
					<tr class="main_right_content_content_title">
						<td width="10%">累积喊话次数</td>
						<td width="10%">累积喊话人数</td>
						<td width="15%">平均每次喊话人数</td>
						<td width="10%">向好友喊话次数</td>
						<td width="15%">平均每次向好友喊话人数</td>
						<td width="15%">向好友联盟喊话次数</td>
						<td width="20%">平均每次向好友联盟喊话人数</td>
					</tr>
				</thead>
				<tbody>
						<tr class="main_right_content_content_body">
										<td><?php echo $result['count'] ?></td>
										<td><?php echo $result['receive_count']; ?></td>
										<td><?php  if($result['count'] > 0) echo number_format($result['receive_count']/$result['count'], 2); else echo 0;?></td>
										<td><?php echo $result['friend_number'];?></td>
										<td><?php if($result['friend_number'] > 0) echo number_format($result['friend_count']/$result['friend_number'], 2);else echo 0;?></td>
										<td><?php echo $result['league_number'];?></td>
										<td><?php if($result['league_number'] > 0) echo number_format($result['league_count']/$result['league_number'], 2);else echo 0;?></td>
					</tr>
				</tbody>
			</table>

		</div>
	</div>
	
	<input id="del_baseurl" type="hidden" value="<?php echo Yii::app()->createUrl("broadcastingLog/delete",array('page'=>$pages->currentPage +1));?>" />
	<div class="main_footer <?php //if($pages->pageCount <= 1) echo 'main_footer_hidden';?>">
		<div class="main_footer_page">

		</div>
	</div>
</div>
	
