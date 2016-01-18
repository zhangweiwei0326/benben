<?php
/* @var $this BxapplyController */
/* @var $dataProvider CActiveDataProvider */
Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/themes/css/global.css");

// $path = substr(dirname(__FILE__), 0, -7)."layouts/area.php";
// require_once($path);  

$bx_status = array("0"=>"等待审核", "1"=>"未通过", "2" => "退回重申", "3" => "已经通过", "4" => "撤消");
?>

		<div class="main_right_content">
		<div class="main_right_content_title main_titleab">
			<div class="main_right_content_title_font">导入数据记录</div>
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
			<form action="<?php echo Yii::app()->createUrl('bxapply/log')?>" method="get" enctype="multipart/form-data">
			<ul style="margin-top:0;">
				<li style="width:25%">
					<div class="form-group" style="padding: 0 0 0 5px;">
						<label for="benben_id" style="float: left; margin-top: 7px;">导入时间:</label>
						<div class="col-sm-8" style="margin-bottom: 10px;">
							<input type="text" class="form-control datetimepicker"
								name="created_time1" id="created_time1"
								value="<?php echo $result['created_time1']?>">
						</div>
					</div>
				</li>
				<li style="width:25%">
					<div class="form-group" style="padding: 0 0 0 5px;">
						<label for="benben_id" style="float: left; margin-top: 7px;">到: </label>
						<div class="col-sm-8" style="margin-bottom: 10px;">
							<input type="text" class="form-control datetimepicker"
								name="created_time2" id="created_time2"
								value="<?php echo $result['created_time2']?>">
						</div>
					</div>
				</li>
			
	
				<li style="float:right;text-align:right;width:60px;padding-right:10px;">
						<span type="submit" class="btn btn-primary" id="search_clear">清空</span>
					</li>
					<li style="float:right;text-align:right;width:60px;padding-right:10px;">
					<input type="submit" class="btn btn-primary" value="查询" id="submit_search">
				</li>				
				</ul>
			</form>
		<table cellspacing=1 border="0" class="table table-hover">
				<thead>
					<tr class="main_right_content_content_title">						
						<td width="15%">手机号码</td>
						<td width="10%">姓名</td>
						<td width="5%">审核状态</td>
						<td width="20%">反馈信息</td>
						<td width="15%">导入时间</td>
						<td width="10%">导入人</td>
						<td width="10%">状态</td>						
					</tr>
				</thead>
				<tbody>
					<?php $i=0;
					if(!$item){
						
					}
					$status = array("0"=>"失败","1"=>"成功");
					foreach ( $items as $item ) {					
					?>
						<tr class="main_right_content_content_body">										
										<td><?php echo $item->phone ?></td>
										<td><?php echo $item->name ?></td>
										<td><?php echo $bx_status[$item->bx_status] ?></td>
										<td><?php echo $item->reason ?></td>																				
										<td><?php echo date("Y-m-d H:i:s", $item->created_time) ?></td>
										<td ><?php echo $item->rname ?></td>
										<td><?php echo $status[$item->status] ?></td>
				</tr>
				<?php } ?>				</tbody>
			</table>

		</div>
	</div>
	<input id="del_baseurl" type="hidden" value="<?php echo Yii::app()->createUrl("creation/delete",array('page'=>$pages->currentPage +1));?>" />
	<div class="main_footer">
		<div class="main_footer_page">
			<?php 
				$path = substr(dirname(__FILE__), 0, -8)."/layouts/searchPages.php";
				require_once($path);  
			?>
			<?php	
			$page = intval($_GET['page']) ?  intval($_GET['page']) : 1;
			$url = explode("?", Yii::app()->request->getUrl());
	$link = Yii::app()->request->hostInfo.$url[0]."?";
			echo '<ul class="yiiPager" id="yw0">'.$this->textPage($pages->pageCount , $page, $link).'</ul>';
// 			$this->widget ( 'CLinkPager', array (
// 					'header' => '',
// 					'firstPageLabel' => '«首页',
// 					'lastPageLabel' => '尾页»',
// 					'prevPageLabel' => '«',
// 					'nextPageLabel' => '»',
// 					'maxButtonCount' => 6,
// 					'pages' => $pages
// 			) );
			?>
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
	
