<?php
/* @var $this CreationCommentController */
/* @var $dataProvider CActiveDataProvider */
Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/themes/css/global.css");
?>

		<div class="main_right_content">
		<div class="main_right_content_title">
			<div class="main_right_content_title_font">微创作评论管理</div>
			<div class="main_right_content_content_block_action_add">
				<a class="btn btn-success" href="javascript:goBack();<?php //echo Yii::app()->createUrl("creation/index");?>">返回</a>
			</div>
		</div>
		<div class="main_right_content_content">
			<table cellspacing=1 border="0" class="table table-hover">
				<thead>
					<tr class="main_right_content_content_title">
						<td width="20%">微创作描述</td>
						<td width="10%">用户</td>
						<td width="10%">评论人奔犇号</td>
						<td width="40%">评论内容</td>
						<td width="20%">发布时间</td>
					</tr>
				</thead>
				<tbody>
					<?php					$i=0;
					foreach ( $items as $item ) {
					$edit_url = Yii::app()->createUrl('creationComment/update',array('id'=>$item->id,'page'=>$_REQUEST['page']));
					?>
						<tr class="main_right_content_content_body">
											<td title="<?php echo $item->cname ?>"><?php 
																	if(mb_strlen($item->cname, 'utf-8') > 30){
																		echo mb_substr($item->cname, 0, 10 ,'utf-8').'...';
																	} else {
																		echo $item->cname;
																	}
											 ?></td>
										<td><?php echo $item->mname; ?></td>
										<td><?php echo $item->benben_id; ?></td>
										<td title="<?php echo $item->review ?>"><?php 
											if(mb_strlen($item->review, 'utf-8') > 30){
												echo mb_substr($item->review, 0, 30 ,'utf-8').'...';
											} else {
												echo $item->review;
											} ?></td>
										<td><?php echo date('Y-m-d H:i:s', $item->created_time) ?></td>
									
				</tr>
				<?php } ?>				</tbody>
			</table>

		</div>
	</div>
	
	<input id="del_baseurl" type="hidden" value="<?php echo Yii::app()->createUrl("creationComment/delete",array('page'=>$pages->currentPage +1));?>" />
	<div class="main_footer <?php if($pages->pageCount <= 1) echo 'main_footer_hidden';?>">
		<div class="main_footer_page">
					<?php 
				$path = substr(dirname(__FILE__), 0, -15)."layouts/searchPages.php";
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
	
