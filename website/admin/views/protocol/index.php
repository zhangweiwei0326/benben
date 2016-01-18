<?php
/* @var $this ProtocolController */
/* @var $dataProvider CActiveDataProvider */
$type = array("1" => "奔犇使用协议", "2" => "东阳百姓网入网声明", "3" =>"关于我们" ,"4" => "法律声明", "5" => "使用帮助", "6" => "积分说明");
?>

<div class="main_right_content">
<div class="main_right_content_title">
<div class="main_right_content_title_font">协议&说明管理</div>
</div>
<div class="main_right_content_content">
<table cellspacing=1 border="0" class="table table-hover">
	<thead>
		<tr class="main_right_content_content_title">
			<td width="15%">协议类型</td>
			<td width="15%">编辑时间</td>
			<td width="10%">操作</td>
		</tr>
	</thead>
	<tbody>
	<?php					$i=0;
	foreach ( $items as $item ) {
		$edit_url = Yii::app()->createUrl('protocol/update',array('id'=>$item->id,'page'=>$_REQUEST['page']));
		?>
		<tr class="main_right_content_content_body">
			<td><?php echo $type[$item->type] ?></td>
			<td><?php echo date('Y-m-d H:i:s', $item->created_time) ?></td>
			<td><a class="btn btn-primary btn-sm" href="<?php echo $edit_url?>">编辑</a>
			</td>
		</tr>
		<?php } ?>
	</tbody>
</table>

</div>
</div>

<input
	id="del_baseurl" type="hidden"
	value="<?php echo Yii::app()->createUrl("protocol/delete",array('page'=>$pages->currentPage +1));?>" />
<div
	class="main_footer <?php if($pages->pageCount <= 1) echo 'main_footer_hidden';?>">
<div class="main_footer_page">
<?php		
$page = intval($_GET['page']) ?  intval($_GET['page']) : 1;
$url = explode("?", Yii::app()->request->getUrl());
	$link = Yii::app()->request->hostInfo.$url[0]."?";
echo '<ul class="yiiPager" id="yw0">'.$this->textPage($pages->pageCount , $page, $link).'</ul>';	
// $this->widget ( 'CLinkPager', array (
// 					'header' => '',
// 					'firstPageLabel' => '«首页',
// 					'lastPageLabel' => '尾页»',
// 					'prevPageLabel' => '«',
// 					'nextPageLabel' => '»',
// 					'maxButtonCount' => 6,
// 					'pages' => $pages
// ) );
?></div>
</div>
</div>

