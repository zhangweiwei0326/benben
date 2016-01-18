<?php
/* @var $this NewsController */
/* @var $dataProvider CActiveDataProvider */

?>

		<div class="main_right_content">
		<div class="main_right_content_title">
			<div class="main_right_content_title_font">管理</div>
			<div class="main_right_content_content_block_action_add">
				<a class="btn btn-success" href="<?php echo Yii::app()->createUrl("news/create");?>">增加</a>
			</div>
		</div>
		<div class="main_right_content_content">
			<table cellspacing=1 border="0" class="table table-hover">
				<thead>
					<tr class="main_right_content_content_title">
						<td width="15%">标题</td><td width="15%">作者</td><td width="15%">简单描述</td><td width="15%">内容</td><td width="15%">创建时间</td>						<td width="20%">操作</td>
					</tr>
				</thead>
				<tbody>
					<?php					$i=0;
					foreach ( $items as $item ) {
					$edit_url = Yii::app()->createUrl('news/update',array('id'=>$item->id,'page'=>$_REQUEST['page']));
					?>
						<tr class="main_right_content_content_body">
											<td><?php echo $item->title ?></td>
										<td><?php echo $item->author ?></td>
										<td><?php echo $item->description ?></td>
										<td><?php echo $item->content ?></td>
										<td><?php echo $item->created_time ?></td>
										<td>
						<a class="btn btn-primary btn-sm" href="<?php echo $edit_url?>">编辑</a>
						<a class="btn btn-danger btn-sm action_del" data="<?php echo $item->id;?>">删除</a>
					</td>
				</tr>
				<?php } ?>				</tbody>
			</table>

		</div>
	</div>
	
	<input id="del_baseurl" type="hidden" value="<?php echo Yii::app()->createUrl("news/delete",array('page'=>$pages->currentPage +1));?>" />
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
	
