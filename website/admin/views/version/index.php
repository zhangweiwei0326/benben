<?php
/* @var $this VersionController */
/* @var $dataProvider CActiveDataProvider */
$type = array("0" => "安卓", "1" => "iphone");
?>

		<div class="main_right_content">
		<div class="main_right_content_title">
			<div class="main_right_content_title_font">版本管理</div>
	
		</div>
		<div class="main_right_content_content">
			<table cellspacing=1 border="0" class="table table-hover">
				<thead>
					<tr class="main_right_content_content_title">
						<td width="8%">类型</td><td width="8%">版本号</td><td width="35%">版本说明</td><td width="35%">下载地址</td><td width="20%">操作</td>
					</tr>
				</thead>
				<tbody>
					<?php					$i=0;
					foreach ( $items as $item ) {
					$edit_url = Yii::app()->createUrl('version/update',array('id'=>$item->id,'page'=>$_REQUEST['page']));
					?>
						<tr class="main_right_content_content_body">
											<td><?php echo $type[$item->type] ?></td>
										<td><?php 
											if(mb_strlen($item->version, 'utf-8') > 25){
												echo mb_substr($item->version, 0, 25, 'utf-8').'...';
											}else{
												echo $item->version;
											}
										
										?></td>
										<td><?php 
								
											if(mb_strlen($item->info, 'utf-8') > 15){
												echo mb_substr($item->info, 0, 15, 'utf-8').'...';
											}else{
												echo $item->info;
											}
										
										?></td>
										<td><?php echo $item->path ?></td>
										<td>
						<a class="btn btn-primary btn-sm" href="<?php echo $edit_url?>">编辑</a>
					
					</td>
				</tr>
				<?php } ?>				</tbody>
			</table>

		</div>
	</div>
	
	<input id="del_baseurl" type="hidden" value="<?php echo Yii::app()->createUrl("version/delete",array('page'=>$pages->currentPage +1));?>" />
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
	
