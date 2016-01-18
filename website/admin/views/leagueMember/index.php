<?php
/* @var $this LeagueMemberController */
/* @var $dataProvider CActiveDataProvider */
Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/themes/css/global.css");
$type = array('0' => '盟主', '1' => '堂主', '2' =>'普通成员');
$status =  array('0' => '未加入', '1' => '已加入');
?>

		<div class="main_right_content">
		<div class="main_right_content_title">
			<div class="main_right_content_title_font">好友联盟成员管理</div>
			<div class="main_right_content_content_block_action_add">
				<a class="btn btn-success" href="javascript:goBack();<?php //echo Yii::app()->createUrl("friendLeague/index");?>">返回</a>
			</div>
		</div>
		<div class="main_right_content_content">
			<table cellspacing=1 border="0" class="table table-hover">
				<thead>
					<tr class="main_right_content_content_title">
						<td width="15%">身份</td>
						<td width="15%">成员名称</td>
						<td width="15%">奔犇号</td>
						<td width="15%">手机</td>						
						<td width="15%">备注名称</td>
						<td width="15%">加入时间</td>		
					</tr>
				</thead>
				<tbody>
					<tr class="main_right_content_content_body info">
						<td>盟主</td>
						<td><?php echo $items['chief']['nick_name']; ?></td>
						<td><?php echo $items['chief']['benben_id']; ?></td>
						<td><?php echo $items['chief']['phone']; ?></td>
						<td><?php echo $items['chief']['remark_name']; ?></td>
						<td><?php echo date('Y-m-d H:i:s', $items['chief']['created_time']) ?></td>					
					</tr>

					<?php
						if(count($items['chief_member'])){
							foreach ($items['chief_member'] as $key => $value) {
								echo '<tr class="main_right_content_content_body"><td>&nbsp;</td>
								<td>'.$value['nick_name'].'</td>
								<td>'.$value['benben_id'].'</td>
								<td>'.$value['phone'].'</td>
								<td>'.$value['remark_name'].'</td>
								<td>'.date('Y-m-d H:i:s', $value['created_time']).'</td></tr>';
							}
						}
					?>

					<?php
						if(count($items['other_chief'])){
							foreach ($items['other_chief'] as $key => $item) {
								$member = $item['member'];
								if (count($member) > 0) {
									foreach($member as $value){
										if ($value['type']) {
											echo '<tr class="main_right_content_content_body info"><td>'.$item['name'];
										}else{
											echo '<tr class="main_right_content_content_body"><td>&nbsp;';
										}
										echo '</td>
										<td>'.$value['nick_name'].'</td>
										<td>'.$value['benben_id'].'</td>
										<td>'.$value['phone'].'</td>
										<td>'.$value['remark_name'].'</td>
										<td>'.date('Y-m-d H:i:s', $value['created_time']).'</td></tr>';
									}
								}
								
							}
						}
					?>

				</tbody>
			</table>

		</div>
	</div>
	
	<input id="del_baseurl" type="hidden" value="<?php echo Yii::app()->createUrl("LeagueMember/delete",array('page'=>$pages->currentPage +1));?>" />
	<div class="main_footer <?php //if($pages->pageCount <= 1) echo 'main_footer_hidden';?>">
		<div class="main_footer_page">
		<?php 
				$path = substr(dirname(__FILE__), 0, -12)."layouts/searchPages.php";
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
	
