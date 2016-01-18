<?php
/* @var $this QuoteController */
/* @var $dataProvider CActiveDataProvider */
$status = array("0"=>"默认", "1"=>"接受");
?>

		<div class="main_right_content">
		<div class="main_right_content_title">
			<div class="main_right_content_title_font">我要买报价管理</div>
			<div class="main_right_content_content_block_action_add">
				<a class="btn btn-success" href="javascript:goBack();<?php //echo Yii::app()->createUrl("buy/index");?>">返回</a>
			</div>
		</div>
		<div class="main_right_content_content">
			<table cellspacing=1 border="0" class="table table-hover">
				<thead>
					<tr class="main_right_content_content_title">
						<td width="15%">我要买</td>
						<td width="15%">报价者</td>
						<td width="15%">手机号码</td>
						<td width="10%">报价</td>
						<td width="20%">备注</td>
						<td width="10%">是否接受</td>
						<td width="15%">报价时间</td>
					</tr>
				</thead>
				<tbody>
					<?php					$i=0;
					foreach ( $items as $item ) {
					$edit_url = Yii::app()->createUrl('quote/update',array('id'=>$item->id,'page'=>$_REQUEST['page']));
					?>
						<tr class="main_right_content_content_body">
											<td title="<?php echo $item->btitle ?>"><?php
												if(mb_strlen($item->btitle, 'utf-8') >15){
													echo mb_substr($item->btitle, 0, 15, 'utf-8').'...';
												}else{
													echo $item->btitle;
												}
											
											?></td>
										<td><?php echo $item->nname ?></td>
										<td><?php echo $item->nphone ?></td>
										<td><?php echo $item->price ?></td>
										<td title="<?php echo $item->description ?>"><?php 
											if(mb_strlen($item->description, 'utf-8') >20){
													echo mb_substr($item->description, 0, 20, 'utf-8').'...';
												}else{
													echo $item->description;
												}
										 ?></td>
										<td><?php echo $status[$item->accept] ?></td>
										<td><?php echo date('Y-m-d H:i:s', $item->created_time) ?></td>
	
				</tr>
				<?php } ?>				</tbody>
			</table>

		</div>
	</div>
	
	<input id="del_baseurl" type="hidden" value="<?php echo Yii::app()->createUrl("quote/delete",array('page'=>$pages->currentPage +1));?>" />
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
	
