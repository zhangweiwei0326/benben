<?php
/* @var $this IndustryController */
/* @var $dataProvider CActiveDataProvider */
Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/themes/css/global.css");
?>

		<div class="main_right_content">
		<div class="main_right_content_title">
			<div class="main_right_content_title_font">行业字典管理</div>
			<div class="main_right_content_content_block_action_add">
				<?php if($_GET['children']){?>
				<a class="btn btn-success" href="<?php echo Yii::app()->createUrl("industry/create?add=add&parent_id=".$_REQUEST['id']);?>">增加</a>
				<a class="btn btn-primary" href="<?php echo Yii::app()->createUrl("industry/index");?>">返回</a>
				<?php }else{?>
				<a class="btn btn-success" href="<?php echo Yii::app()->createUrl("industry/create?add=add");?>">增加</a>
				<?php }?>
			</div>
		</div>
		<div class="main_right_content_content">
			<table cellspacing=1 border="0" class="table table-hover">
				<thead>
					<tr class="main_right_content_content_title">
						<td width="15%">行业名称</td><td width="15%">创建时间</td><td width="20%">操作</td>
					</tr>
				</thead>
				<tbody>
					<?php					$i=0;
					foreach ( $items as $item ) {
					$edit_url = Yii::app()->createUrl('industry/update',array('id'=>$item->id,'page'=>$_REQUEST['page'], 'parent_id' => $_REQUEST['id']));
					$child_url = Yii::app()->createUrl('industry/index',array('id'=>$item->id,'children'=>'children'));
					?>
						<tr class="main_right_content_content_body">
											<td><?php echo $item->name ?></td>
									
										<td><?php echo date('Y-m-d H:i:s', $item->created_time) ?></td>
							
										<td>
						<?php if(!$_GET['children']){?>
						<a class="btn btn-primary btn-sm" href="<?php echo $edit_url?>">编辑</a>
						<!-- <a class="btn btn-success btn-sm" href="<?php echo $child_url ?>">查看子行业</a> -->
						<a class="btn btn-danger btn-sm action_del" data="<?php echo $item->id?>" parent_id="<?php echo $item->id?>">删除</a>
						<?php } else{?>
							<a class="btn btn-primary btn-sm" href="<?php echo $edit_url ?>">编辑</a>
							<a class="btn btn-danger btn-sm action_del" data="<?php echo $item->id?>">删除</a>
						<?php }?>
						
						
						
					</td>
				</tr>
				<?php } ?>				</tbody>
			</table>

		</div>
	</div>
	
	<input id="del_baseurl" type="hidden" value="<?php echo Yii::app()->createUrl("industry/delete",array('page'=>$pages->currentPage +1));?>" />
	<div class="main_footer <?php if($pages->pageCount <= 1) echo 'main_footer_hidden';?>">
		<div class="main_footer_page">
			<?php 
				$path = substr(dirname(__FILE__), 0, -8)."layouts/searchPages.php";
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
	
