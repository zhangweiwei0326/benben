<?php
/* @var $this ProgramController */
/* @var $dataProvider CActiveDataProvider */

?>

		<div class="main_right_content">
		<div class="main_right_content_title">
			<div class="main_right_content_title_font"></div>
			<div class="main_right_content_content_block_action_add">
				<div class="main_right_content_content_block_action_font">
					<a class="action_add"
						href="<?php echo Yii::app()->createUrl("program/create");?>">增加</a>
				</div>
			</div>
		</div>
		<div class="main_right_content_content">
			<table cellspacing=1 border="0">
				<tr class="main_right_content_content_title">
									<td width="15%">节目名称</td>
									<td width="15%">状态 0：停用，1：启用</td>
									<td width="15%">创建人</td>
									<td width="15%">创建时间</td>
									<td width="15%">剧本数量</td>
									<td width="25%">操作</td>
				</tr>
					<?php					$i=0;
					foreach ( $items as $item ) {
					$edit_url = Yii::app()->createUrl('program/update',array('id'=>$item->id,'page'=>$_REQUEST['page']));
					?>
						<tr class="main_right_content_content_body">
											<td><?php echo $item->name ?></td>
										<td><?php echo $item->status ?></td>
										<td><?php echo $item->created_by ?></td>
										<td><?php echo $item->created_at ?></td>
										<td><?php echo $item->script_count ?></td>
										<td>
						<div class="main_right_content_content_block_action">
							<div class="main_right_content_content_block_action_edit" style="margin-left: 50px;">
								<div class="main_right_content_content_block_action_font">
									<a class="action_edit"
										href="<?php echo $edit_url?>">编辑</a>
								</div>
							</div>
							<div class="main_right_content_content_block_action_del">
								<div class="main_right_content_content_block_action_font">
									<a class="action_del">删除</a> <input id="display_id"
										type="hidden" value="<?php echo $item->id;?>" />
								</div>
							</div>
						</div>
					</td>
				</tr>
				<?php } ?>					</table>
		</div>
	</div>
	
		<input id="del_baseurl" type="hidden" value="<?php echo Yii::app()->createUrl("program/delete",array('page'=>$pages->currentPage +1));?>" />
	<div class="main_footer <?php if($pages->pageCount <= 1) echo 'main_footer_hidden';?>">
		<div class="main_footer_page">
			<?php			$this->widget ( 'CLinkPager', array (
					'header' => '',
					'cssFile' => 'false',
					'firstPageLabel' => '首页',
					'lastPageLabel' => '末页',
					'prevPageLabel' => '上一页',
					'nextPageLabel' => '下一页',
					'maxButtonCount' => 6,
					'pages' => $pages
			) );
			?>
			<div class="main_footer_page_search">
				<div class="main_footer_page_search_box">
					<input id="pages" type="text" />
				</div>
				<div class="main_footer_page_search_font">GO</div>
				<input id="display_total" type="hidden" value="<?php echo $pages->pageCount; ?>" />
				<input id="page_baseurl" type="hidden" value="<?php echo Yii::app()->createUrl('program/index');?>" />
			</div>
		</div>
	</div>
</div>
	
