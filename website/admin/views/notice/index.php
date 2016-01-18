<?php
/* @var $this NoticeController */
/* @var $dataProvider CActiveDataProvider */
Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/themes/css/global.css");
?>

		<div class="main_right_content">
		<div class="main_right_content_title">
			<div class="main_right_content_title_font">消息管理/系统通知</div>
			<div class="main_right_content_content_block_action_add">
				<a class="btn btn-success" href="<?php echo Yii::app()->createUrl("notice/create");?>">发送通知/公告</a>
			</div>
		</div>
		<div class="main_right_content_content" style="background:#F5F5F5;">
		<form action="<?php echo Yii::app()->createUrl('notice/index')?>" method="get">
			<ul style="margin-top:0;">			
				<li>
					<div class="form-group" style="padding:0 0 0 5px;">
						<label  for="benben_id" style="float:left;margin-top:7px;">发布账号:</label>	
						<div class="col-sm-8" style="margin-bottom:10px;">
							<input type="text" class="form-control" name="name" id="member_id" value="<?php echo $result['name']?>">
						</div>
					</div>
				</li>
					<li>
						<div class="form-group" style="padding:0 0 0 5px;">
							<label  for="created_time1" style="float:left;margin-top:7px;">发布时间:</label>	
							<div class="col-sm-8" style="margin-bottom:10px;">
								<input type="text" class="form-control created_time" name="created_time1"  id="created_time1"  value="<?php echo $result['created_time1']?>">
							</div>
					</div>
					</li>
					<li>
						<div class="form-group" style="padding:0 0 0 5px;">
							<label  for="created_time1" style="float:left;margin-top:7px;">到:&nbsp;&nbsp;&nbsp;</label>	
							<div class="col-sm-8" style="margin-bottom:10px;">
								<input type="text" class="form-control created_time" name="created_time2"  id="created_time2"   value="<?php echo $result['created_time2']?>">
							</div>
					</div>
					</li>
					<li style="float:right;text-align:right;width:60px;padding-right:10px;">
			<span type="submit" class="btn btn-primary" id="search_clear">清空</span>
		</li>	
	
	<li style="float:right;text-align:right;width:70px;padding-right:16px;">
	    <input type="hidden" value="-2" name="backurl">
		<input type="submit" class="btn btn-primary" value="查询" id="submit_search">
	</li>
				
			</ul>			
			</form>
			
			<table cellspacing=1 border="0" class="table table-hover">
				<thead>
					<tr class="main_right_content_content_title">
						<td width="5%">编号</td><td width="10%">发送者</td><td width="55%">消息内容</td><td width="20%">发布时间</td><td width="10%">操作</td>
					</tr>
				</thead>
				<tbody>
					<?php					$i=0;
					foreach ( $items as $item ) {
					$edit_url = Yii::app()->createUrl('notice/update',array('id'=>$item->id,'page'=>$_REQUEST['page']));
					?>
						<tr class="main_right_content_content_body">
										<td><?php echo $item->id ?></td>
										<td><?php echo $item->sender ?></td>
										<td><?php if(mb_strlen($item->content, 'utf-8') > 40){
													echo mb_substr($item->content, 0, 39, 'utf-8')."....";
												}else{
													echo $item->content;
												}?></td>
										<td><?php echo date("Y-m-d H:i:s", $item->created_time); ?></td>
										<td>
						<a class="btn btn-primary btn-sm" href="<?php echo $edit_url."&type=2"?>">查看详情</a>
						<!--  <a class="btn btn-danger btn-sm action_del" data="<?php echo $item->id;?>">删除</a>-->
					</td>
				</tr>
				<?php } ?>				</tbody>
			</table>

		</div>
	</div>
	
	<input id="del_baseurl" type="hidden" value="<?php echo Yii::app()->createUrl("notice/delete",array('page'=>$pages->currentPage +1));?>" />
	<div class="main_footer <?php if($pages->pageCount <= 1) echo 'main_footer_hidden';?>">
		<div class="main_footer_page">
		<?php 
				$path = substr(dirname(__FILE__), 0, -6)."layouts/searchPages.php";
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
	
