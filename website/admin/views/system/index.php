<?php $this->widget('HeaderWidge',array('cssArray' => array('/themes/css/index.css')));?>
<?php $this->widget('LeftWidge', array('index' => 20)) ;?>
<div class="main_content_right col-lg-10 col-md-10 col-sm-12  col-xs-12">
	<?php $this->widget('RightHeaderWidge');?>
	<div class="main_right_content">
		<div class="main_right_content_title">
			<div class="main_right_content_title_font">用户管理</div>

			<div class="main_right_content_content_block_action_add">
				<a class="btn btn-success" href="<?php echo Yii::app()->createUrl("/system/createUser");?>">增加</a>
			</div>
		</div>
		<div class="main_right_content_content table-responsive">
			<table cellspacing=1 border="0" class="table table-hover">
				<tr class="main_right_content_content_title">
					<td width="15%">编号</td>
					<td width="20%">用户名称</td>
					<td width="20%">用户角色</td>
					<td width="25%">时间</td>
					<td width="20%">操作</td>
				</tr>
						<?php
						if (! empty ( $user )) {
							foreach ( $user as $item ) {
								?>
						<tr class="main_right_content_content_body">
					<td><?php echo $item->id; ?></td>
					<td><?php echo $item->username;?></td>
					<td><?php $role = getRole(); echo Frame::getStringFromArray($role, $item->role,'未知');?></td>
					<td><?php echo date('Y-m-d', $item->created_time); ?></td>
					<td>
						<a class="btn btn-primary btn-sm" href="<?php  echo Yii::app()->createUrl("/system/updateUser?id=".$item->id); ?>">编辑</a>
						<a class="btn btn-danger btn-sm action_del" data="<?php echo $item->id;?>" >删除</a>
					</td>
				</tr>
						<?php
							}
						}
						?>
					</table>
		</div>
	</div>

	<input id="del_baseurl" type="hidden" value="<?php echo Yii::app()->createUrl("/system/deleteUser/");?>" />
	<div class="main_footer <?php if($total <= 1) echo 'main_footer_hidden';?>">
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
// 					'pages' => $page 
// 			));
			?>
		</div>
	</div>
	


</div>
<?php $this->widget('FooterWidge') ;?>