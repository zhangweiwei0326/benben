<?php
/* @var $this EnterpriseMemberController */
/* @var $dataProvider CActiveDataProvider */
Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/themes/css/global.css");
?>

		<div class="main_right_content">
		<div class="main_right_content_title">
			<div class="main_right_content_title_font">通讯录成员</div>
			<div class="main_right_content_content_block_action_add">
				<a class="btn btn-success" href="javascript:goBack();<?php //echo Yii::app()->createUrl("enterprise/index");?>">返回</a>
				<a class="btn btn-success" href="<?php echo Yii::app()->createUrl("enterpriseMember/indexDownload", array('id'=>$_GET['id']));?>">导出</a>
				

			</div>
		</div>
		<div class="main_right_content_content">
			<table cellspacing=1 border="0" class="table table-hover">
				<thead>
					<tr class="main_right_content_content_title">
						<td width="20%">通讯录备注名</td><td width="15%">手机号</td><td width="15%">其它号</td><td width="15%">是否是奔犇用户</td><td width="15%">加入时间</td><td width="20%">操作</td>
					</tr>
				</thead>
				<tbody>
					<?php					$i=0;
					foreach ( $items as $item ) {
					$edit_url = Yii::app()->createUrl('enterpriseMember/update',array('id'=>$item->id,'page'=>$_REQUEST['page']));
					?>
						<tr class="main_right_content_content_body">
										
										<td><?php echo $item->name ?></td>
										<td><?php echo $item->phone ?></td>
										<td><?php echo $item->short_phone ?></td>
										<td><?php echo $item->member_id ? "是":"否" ?></td>
										<td><?php echo date('Y-m-d H:i:s', $item->created_time) ?></td>
										<td>
						<a class="btn btn-primary btn-sm" href="<?php echo $edit_url?>">编辑</a>
					</td>
				</tr>
				<?php } ?>				</tbody>
			</table>

		</div>
	</div>
	
	<input id="del_baseurl" type="hidden" value="<?php echo Yii::app()->createUrl("enterpriseMember/delete",array('page'=>$pages->currentPage +1));?>" />
	<div class="main_footer <?php if($pages->pageCount <= 1) echo 'main_footer_hidden';?>">
		<div class="main_footer_page">
		<ul class="mypageul">
				<li class="pageli">
					<input type="text" value="" class="input_page">	
					<a href="javascript:void(0)" style="" class="enterm_page" controller="member" baseUrl="<?php echo Yii::app()->request->baseUrl?>">GO</a>
				</li>
				<li class="pageli">
					 第<strong><?php 
					 				if($_GET['page']){
					 					if($_GET['page'] > ceil($pages->itemCount/$pages->pageSize)){
					 						echo ceil($pages->itemCount/$pages->pageSize);
					 					}else if(!is_numeric($_GET['page'])){
					 						echo 1;
					 					}else if($_GET['page'] <= 0){
					 						echo 1;
					 					}else{
					 						echo $_GET['page'];
					 					}
					 				}else{
					 					echo 1;
					 				}
					 				?></strong>页/共<strong class="pagecount"><?php echo ceil($pages->itemCount/$pages->pageSize);?></strong>页,
					 				<strong class="pagecount"><?php echo $pages->itemCount;?></strong>条记录
				</li>
			</ul>
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
	
