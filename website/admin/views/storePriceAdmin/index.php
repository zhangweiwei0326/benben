<?php
/* @var $this StorePriceAdminController */
/* @var $dataProvider CActiveDataProvider */
$arr_type=array(11=>"会员号",0=>"促销",1=>"团购",10=>"我要开分店",12=>"小喇叭",13=>"大喇叭",14=>"好友联盟",15=>"政企通讯录");
?>

		<div class="main_right_content">
		<div class="main_right_content_title">
			<div class="main_right_content_title_font">服务管理</div>
			<?php if($pages->itemCount < 7){?>
			<div class="main_right_content_content_block_action_add">
				<a class="btn btn-success" href="<?php echo Yii::app()->createUrl("storePriceAdmin/create");?>">增加</a>
			</div>
			<?php }?>
		</div>
		<div class="main_right_content_content">
			<table cellspacing=1 border="0" class="table table-hover">
				<thead>
					<tr class="main_right_content_content_title">
						<td width="10%">编号</td>
						<td width="20%">名称</td>
						<td width="20%">服务类型</td>
						<td width="20%">修改时间</td>
						<td width="30%">操作</td>
					</tr>
				</thead>
				<tbody>
					<?php					$i=0;
					foreach ( $items as $item ) {
					$edit_url = Yii::app()->createUrl('storePriceAdmin/update',array('id'=>$item->id,'page'=>$_REQUEST['page']));
					?>
						<tr class="main_right_content_content_body">
										<td><?php echo $item->id ?></td>
										<td><?php echo $item->title ?></td>
										<td><?php echo $arr_type[$item->type] ?></td>
										<td><?php echo date("Y-m-d H:i:s",$item->update_date) ?></td>
										<td>
						<a class="btn btn-primary btn-sm" href="<?php echo $edit_url?>">编辑</a>
						<a class="btn btn-danger btn-sm action_del" data="<?php echo $item->id;?>">删除</a>
					</td>
				</tr>
				<?php } ?>				</tbody>
			</table>

		</div>
	</div>
	
	<input id="del_baseurl" type="hidden" value="<?php echo Yii::app()->createUrl("storePriceAdmin/delete",array('page'=>$pages->currentPage +1));?>" />
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
	
