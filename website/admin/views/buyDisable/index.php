<?php
/* @var $this BuyDisableController */
/* @var $dataProvider CActiveDataProvider */
$status = array("0" => "正常", "1" => "屏蔽");
?>

		<div class="main_right_content">
		<div class="main_right_content_title">
			<div class="main_right_content_title_font">我要买启/禁用管理</div>
			<div class="main_right_content_content_block_action_add">
				<a class="btn btn-success" href="javascript:goBack();<?php //echo Yii::app()->createUrl("buy/index");?>">返回</a>
			</div>
		</div>
		<div class="main_right_content_content">
			<table cellspacing=1 border="0" class="table table-hover">
				<thead>
					<tr class="main_right_content_content_title">
						<td width="20%">我要买标识</td><td width="8%">状态</td><td width="8%">操作人</td><td width="49%">操作原因</td><td width="15%">操作时间</td>					
					</tr>
				</thead>
				<tbody>
					<?php					$i=0;
					foreach ( $items as $item ) {
					$edit_url = Yii::app()->createUrl('buyDisable/update',array('id'=>$item->id,'page'=>$_REQUEST['page']));
					?>
						<tr class="main_right_content_content_body">
										<td title="<?php echo $item->btitle ?>"><?php 
											if(mb_strlen($item->btitle, 'utf-8') > 15){
												echo mb_substr($item->btitle, 0, 15, 'utf-8').'...';
											}else{
												echo $item->btitle;
											}
										?></td>
										<td><?php echo $status[$item->status] ?></td>
										<td><?php echo $item->uname ?></td>
										<td title="<?php echo $item->reason  ?>"><?php 
										if(mb_strlen($item->reason, 'utf-8') > 30){
												echo mb_substr($item->reason, 0, 30, 'utf-8').'...';
											}else{
												echo $item->reason;
											}
										?></td>
										<td><?php echo date('Y-m-d H:i:s', $item->created_time) ?></td>
						
				</tr>
				<?php } ?>				</tbody>
			</table>

		</div>
	</div>
	
	<input id="del_baseurl" type="hidden" value="<?php echo Yii::app()->createUrl("buyDisable/delete",array('page'=>$pages->currentPage +1));?>" />
	<div class="main_footer <?php if($pages->pageCount <= 1) echo 'main_footer_hidden';?>">
		<div class="main_footer_page">
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
	
