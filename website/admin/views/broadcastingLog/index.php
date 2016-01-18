<?php
/* @var $this BroadcastingLogController */
/* @var $dataProvider CActiveDataProvider */

?>

		<div class="main_right_content">
		<div class="main_right_content_title">
			<div class="main_right_content_title_font">管理</div>
			<div class="main_right_content_content_block_action_add">
				<a class="btn btn-success" href="<?php echo Yii::app()->createUrl("broadcastingLog/create");?>">增加</a>
			</div>
		</div>
		<div class="main_right_content_content">
			<table cellspacing=1 border="0" class="table table-hover">
				<thead>
					<tr class="main_right_content_content_title">
						<td width="15%">发送人</td><td width="15%">好友联盟ID</td><td width="15%"></td><td width="15%">有多少我接受</td><td width="15%">描述</td><td width="15%"></td><td width="15%">发送内容</td><td width="15%"></td><td width="15%">小喇叭类型，1为直通车，否则为普通消息</td>						<td width="20%">操作</td>
					</tr>
				</thead>
				<tbody>
					<?php					$i=0;
					foreach ( $items as $item ) {
					$edit_url = Yii::app()->createUrl('broadcastingLog/update',array('id'=>$item->id,'page'=>$_REQUEST['page']));
					?>
						<tr class="main_right_content_content_body">
											<td><?php echo $item->member_id ?></td>
										<td><?php echo $item->league_id ?></td>
										<td><?php echo $item->friend_id ?></td>
										<td><?php echo $item->receive_count ?></td>
										<td><?php echo $item->description ?></td>
										<td><?php echo $item->created_time ?></td>
										<td><?php echo $item->content ?></td>
										<td><?php echo $item->is_del ?></td>
										<td><?php echo $item->type ?></td>
										<td>
						<a class="btn btn-primary btn-sm" href="<?php echo $edit_url?>">编辑</a>
						<a class="btn btn-danger btn-sm action_del" data="<?php echo $item->id;?>">删除</a>
					</td>
				</tr>
				<?php } ?>				</tbody>
			</table>

		</div>
	</div>
	
	<input id="del_baseurl" type="hidden" value="<?php echo Yii::app()->createUrl("broadcastingLog/delete",array('page'=>$pages->currentPage +1));?>" />
	<div class="main_footer <?php if($pages->pageCount <= 1) echo 'main_footer_hidden';?>">
		<div class="main_footer_page">
			<?php			$this->widget ( 'CLinkPager', array (
					'header' => '',
					'firstPageLabel' => '«首页',
					'lastPageLabel' => '尾页»',
					'prevPageLabel' => '«',
					'nextPageLabel' => '»',
					'maxButtonCount' => 6,
					'pages' => $pages
			) );
			?>
		</div>
	</div>
</div>
	
