<?php
/* @var $this NumberTrainController */
/* @var $dataProvider CActiveDataProvider */
$status = array('0'=>'启用', '1'=>'禁用1周', '2'=>'禁用2周', '3'=>'禁用1个月', '4'=>'禁用3个月', '5'=>'无限期');
Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/themes/css/global.css");

?>

		<div class="main_right_content">
		<div class="main_right_content_title main_titleab">
			<div class="main_right_content_title_font">发现统计</div>
			
		</div>
		<div class="main_right_content_content" style="background:#F5F5F5;">
						<?php if($result['msg']) {?>
			<div class="alert alert-danger alert-dismissible" role="alert">
			  <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
			  <strong>警告！</strong> <?php echo $result['msg'];?>
			</div>
			<?php }?>
			<div style="width:100%;float:left;display:inline;">
			<form action="<?php echo Yii::app()->createUrl('/findstatistic')?>" method="get">
			<ul style="margin-top:0;">
				<li>
						<div class="form-group" style="padding:0 0 0 5px;">
							<label  for="created_time1" style="float:left;margin-top:7px;">发帖日期:</label>	
							<div class="col-sm-8" style="margin-bottom:10px;">
								<input type="text" class="form-control created_time" name="created_time1"  id="created_time1"  value="<?php echo $info['created_time1']?>">
							</div>
					</div>
					</li>
					<li>
						<div class="form-group" style="padding:0 0 0 5px;">
							<label  for="created_time1" style="float:left;margin-top:7px;">到:　　　</label>	
							<div class="col-sm-8" style="margin-bottom:10px;">
								<input type="text" class="form-control created_time" name="created_time2"  id="created_time2"   value="<?php echo $info['created_time2']?>">
							</div>
					</div>
					</li>
					
				<li style="float:right;text-align:right;width:60px;padding-right:10px;">
		</li>
				<li style="float:right;text-align:right;width:60px;padding-right:10px;">
			<span type="submit" class="btn btn-primary" id="search_clear">清空</span>
		</li>
		<li style="float:right;text-align:right;width:70px;padding-right:12px;">
					<input type="hidden" value="-2" name="backurl">
					<input type="submit" class="btn btn-primary" value="查询" id="submit_search">
				</li>
				</ul>
					
			</form>
		</div>
		<div style="width:100%">
			<table cellspacing=1 border="0" class="table table-hover" style="width:49%;float:left;border-right:1px solid #ddd;border-bottom:1px solid #ddd">
				<thead>
					<tr class="main_right_content_content_title">
						<td colspan=4>朋友圈统计</td>
					</tr>
					<tr class="main_right_content_content_title">
						<td width="8%">发帖数量</td>
						<td width="8%">数量(人)</td>
						<td width="8%">比例</td>
						<td width="8%">操作</td>
					</tr>
				</thead>
				<tbody>
					<?php	
					foreach ( $friend as  $key => $item ) {
						if ($key == 1) {
							continue;
						}
						if (isset($item['info'])) {
							$str = implode('|', $item['info']);
						}else{
							$str = 0;
						}
						
					?>
						<tr class="main_right_content_content_body">
							<td><?php echo $item['name'] ?></td>
							<td><?php echo $item['number']; ?></td>
							<td><?php if($friend[0]['number']){echo round(($item['number']/$friend[0]['number'])*100).'%';}else{echo '0%';}; ?></td>
							<td><a  target="_blank" class="btn btn-primary btn-sm" href="<?php  echo Yii::app()->createUrl('findstatistic/downloadFriend', array('str'=>$str,'code'=>md5($str.'excel'), 'key'=>$key, 'type'=>1, 'created_time1'=>$info['created_time1'], 'created_time2'=>$info['created_time2']))?>">报表</a></td>
						</tr>
					<?php } ?>
				</tbody>
			</table>

			<table cellspacing=1 border="0" class="table table-hover" style="width:49%;float:right;border-left:1px solid #ddd;border-bottom:1px solid #ddd">
				<thead>
					<tr class="main_right_content_content_title">
						<td colspan=4>微创作统计</td>
					</tr>
					<tr class="main_right_content_content_title">
						<td width="8%">发帖数量</td>
						<td width="8%">数量(人)</td>
						<td width="8%">比例</td>
						<td width="8%">操作</td>
					</tr>
				</thead>
				<tbody>
					<?php	
					foreach ( $creation as $key => $item ) {
						if ($key == 1) {
							continue;
						}
						if (isset($item['info'])) {
							$str = implode('|', $item['info']);
						}else{
							$str = 0;
						}
					?>
						<tr class="main_right_content_content_body">
							<td><?php echo $item['name'] ?></td>
							<td><?php echo $item['number']; ?></td>
							<td><?php if($creation[0]['number']){echo round(($item['number']/$creation[0]['number'])*100).'%';}else{echo '0%';} ?></td>
							<td><a target="_blank" class="btn btn-primary btn-sm" href="<?php  echo Yii::app()->createUrl('findstatistic/downloadCreation', array('str'=>$str,'code'=>md5($str.'excel'), 'key'=>$key, 'type'=>2, 'created_time1'=>$info['created_time1'], 'created_time2'=>$info['created_time2']))?>">报表</a></td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>

		<div style="width:100%">
			<table cellspacing=1 border="0" class="table table-hover" style="width:49%;float:left;border-right:1px solid #ddd;border-bottom:1px solid #ddd">
				<thead>
					<tr class="main_right_content_content_title">
						<td colspan=4>我要买统计</td>
					</tr>
					<tr class="main_right_content_content_title">
						<td width="8%">发帖数量</td>
						<td width="8%">数量(人)</td>
						<td width="8%">比例</td>
						<td width="8%">操作</td>
					</tr>
				</thead>
				<tbody>
					<?php	
					foreach ( $buy as  $key => $item ) {
						if ($key == 1) {
							continue;
						}
						if (isset($item['info'])) {
							$str = implode('|', $item['info']);
						}else{
							$str = 0;
						}
						
					?>
						<tr class="main_right_content_content_body">
							<td><?php echo $item['name'] ?></td>
							<td><?php echo $item['number']; ?></td>
							<td><?php if($buy[0]['number']){echo round(($item['number']/$buy[0]['number'])*100).'%';}else{echo '0%';} ?></td>
							<td><a  target="_blank" class="btn btn-primary btn-sm" href="<?php  echo Yii::app()->createUrl('findstatistic/downloadBuy', array('str'=>$str,'code'=>md5($str.'excel'), 'key'=>$key, 'type'=>3, 'created_time1'=>$info['created_time1'], 'created_time2'=>$info['created_time2']))?>">报表</a></td>
						</tr>
					<?php } ?>
				</tbody>
			</table>

			<table cellspacing=1 border="0" class="table table-hover" style="width:49%;float:right;border-left:1px solid #ddd;border-bottom:1px solid #ddd">
				<thead>
					<tr class="main_right_content_content_title">
						<td colspan=4>接受报价统计</td>
					</tr>
					<tr class="main_right_content_content_title">
						<td width="8%">授受次数</td>
						<td width="8%">数量(人)</td>
						<td width="8%">比例</td>
						<td width="8%">操作</td>
					</tr>
				</thead>
				<tbody>
					<?php	
					foreach ( $quote as $key => $item ) {
						if ($key == 1) {
							continue;
						}
						if (isset($item['info'])) {
							$str = implode('|', $item['info']);
						}else{
							$str = 0;
						}
					?>
						<tr class="main_right_content_content_body">
							<td><?php echo $item['name'] ?></td>
							<td><?php echo $item['number']; ?></td>
							<td><?php if($quote[0]['number']) echo round(($item['number']/$quote[0]['number'])*100).'%'; ?></td>
							<td><a target="_blank" class="btn btn-primary btn-sm" href="<?php  echo Yii::app()->createUrl('findstatistic/downloadQuote', array('str'=>$str,'code'=>md5($str.'excel'), 'key'=>$key, 'type'=>4, 'created_time1'=>$info['created_time1'], 'created_time2'=>$info['created_time2']))?>">报表</a></td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>

		</div>
	</div>
	
</div>


<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="exampleModalLabel">设置置顶</h4>
      </div>
      <div class="modal-body">
        <form>
          <div class="form-group">
            <label for="recipient-name" class="control-label">类型:</label>
            <select type="text" class="form-control" id="recipient-name">
            	<option value="0">取消置顶</option>
            	<option value="1">一级置顶</option>
            	<option value="2">二级置顶</option>
            	<option value="3">三级置顶</option>
            </select>
          </div>
          <div class="form-group">
            <label for="message-text" class="control-label">天数:</label>
            <input type="text" class="form-control" id="message-text" />
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
        <button type="button" class="btn btn-primary" id="modal-confirm" data-url="<?php echo Yii::app()->createUrl('numberTrain/top');?>">确认</button>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
	$('#exampleModal').on('show.bs.modal', function (event) {
		  var button = $(event.relatedTarget);
		  var info = button.attr('data-info');
		  $('#modal-confirm').attr('data-info', info);
		});

	$('#modal-confirm').click(function(){
		var top = $('#recipient-name').val();
		var number = $('#message-text').val();
		var info = $('#modal-confirm').attr('data-info');
		var url = $(this).attr('data-url');
		if (info && url) {
			$.get(url, {'id':info, 'top':top, 'number':number}, function(){
				window.location.href = window.location.href;
			});
		};
	});
</script>
	
