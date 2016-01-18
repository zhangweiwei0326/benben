<?php
/* @var $this EnterpriseMemberController */
/* @var $dataProvider CActiveDataProvider */
Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/themes/css/global.css");
$status = array('0'=>'启用', '1'=>'屏蔽');
$status1= array('0'=>'启用', '1'=>'禁用1周', '2'=>'禁用2周', '3'=>'禁用1个月', '4'=>'禁用3个月', '5'=>'无限期');
?>

		<div class="main_right_content">
		<div class="main_right_content_title">
			<div class="main_right_content_title_font">我要买管理/推送记录/明细</div>
			<div class="main_right_content_content_block_action_add">
				<a class="btn btn-success" href="javascript:goBack();<?php //echo Yii::app()->createUrl("enterprise/index");?>">返回</a>
			</div>
		</div>
		<div class="main_right_content_content">
			<table cellspacing=1 border="0" class="table table-hover">
				<thead>
					<tr class="main_right_content_content_title">
						<td width="5%">头像</td>
						<td width="10%">直通车名称</td>
						<td width="10%">简称</td>
						<td width="8%">行业</td>
						<td width="12%">地区</td>
						<td width="10%">创建人</td>
						<td width="8%">创建人状态</td>
						<td width="10%">手机号码</td>
						<td width="5%">置顶</td>
						<td width="8%">状态</td>
						<td width="14%">创建时间</td>
					</tr>
				</thead>
				<tbody>
					<?php					$i=0;
					foreach ( $items as $item ) {
					
					?>
						<tr class="main_right_content_content_body">
										
										<td><img src="<?php echo $item->poster?>" width="32"></td>
										<td><?php echo $item->name ?></td>
										<td><?php echo $item->short_name ?></td>
										<td><?php echo $industry[$item->industry]?></td>
										<td><?php echo $areaInfo[$item->province].''.$areaInfo[$item->city] ?></td>
										<td><?php echo $item->mname? $item->mname: $item->nick_name ?></td>
										<td><?php echo $status1[$item->store_disable] ?></td>
										<td><?php echo $item->phone ?></td>
										<td><?php switch($item->istop){case 10: echo '置1';break;case 9: echo '置2';break;case 8: echo '置3';break;default: echo '否';break;} ?></td>
										<td><?php echo $status[$item->status]; ?></td>
										<td><?php echo date('Y-m-d H:i:s', $item->created_time) ?></td>
				</tr>
				<?php } ?>				</tbody>
			</table>

		</div>
	</div>
	
	<input id="del_baseurl" type="hidden" value="<?php echo Yii::app()->createUrl("enterpriseMember/delete",array('page'=>$pages->currentPage +1));?>" />
	<div class="main_footer <?php //if($pages->pageCount <= 1) echo 'main_footer_hidden';?>">
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
	
