<?php
/* @var $this BxapplyRecordController */
/* @var $dataProvider CActiveDataProvider */
$status = array("0"=>"等待审核", "1"=>"未通过", "2"=>"退回重申", "3"=>"已经通过", '4'=>'已撤销');
?>

		<div class="main_right_content">
		<div class="main_right_content_title">
			<div class="main_right_content_title_font">百姓网禁用管理</div>
			<div class="main_right_content_content_block_action_add">
				<a class="btn btn-success" href="javascript:goBack();<?php //echo Yii::app()->createUrl("bxapply/index");?>">返回</a>
			</div>
		</div>
		<div class="main_right_content_content">
			<?php foreach ( $items as $item ) {?>
			<div class="jl" style="width:96%;padding:2% 2% 2% 2%;border-bottom:2px solid #ccc;margin-left:2%;">
				<span style="font-size:16px;"><strong>操作时间: </strong><?php echo date('Y-m-d H:i:s', $item->created_time)?></span>
				<br>
				<span style="font-size:16px;"><strong>操作人: </strong><?php echo $item->name ?></span>
				<br>
				<span style="font-size:16px;"><strong>状态: </strong><?php echo $status[$item->status] ?></span>
				<br>
				<span style="font-size:16px;"><strong>操作原因: </strong><?php echo $item->reason ?></span>
				<br>
				<span style="font-size:16px;"><strong>百姓网短号: </strong><?php echo $item->short_phone ?></span>

			</div>
			<?php }?>
		</div>
	</div>
	
	<input id="del_baseurl" type="hidden" value="<?php echo Yii::app()->createUrl("bxapplyRecord/delete",array('page'=>$pages->currentPage +1));?>" />
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
	
