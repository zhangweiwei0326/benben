<?php
/* @var $this HappyController */
/* @var $dataProvider CActiveDataProvider */
Yii::app ()->clientScript->registerCssFile ( Yii::app ()->request->baseUrl . "/themes/css/global.css" );
$role_arr  = json_decode(Yii::app()->session['role_arr'],true);
?>

		<div class="main_right_content">
		<div class="main_right_content_title">
			<div class="main_right_content_title_font">开心一刻管理</div>
			<div class="main_right_content_content_block_action_add">
				<?php if($role_arr['dohappy'] & 2){?><a class="btn btn-success" href="<?php echo Yii::app()->createUrl("happy/create");?>">增加</a><?php }?>
			</div>
		</div>
		
		<div class="main_right_content_content" style="background:#F5F5F5;">
		<form action="<?php echo Yii::app()->createUrl('happy/index')?>" method="get">
			<ul style="margin-top:0;">
			<li>
						<div class="form-group" style="padding:0 0 0 5px;">
							<label  for="created_time1" style="float:left;margin-top:7px;">上传时段:</label>	
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
					<?php if($role_arr['dohappy'] & 4){?>
					<li style="float:right;text-align:right;width:112px;padding-right:20px;">
		<a href = "/admin.php/happy/inputexcel"><div class="btn btn-primary"  id="putexcel">批量上传</div></a>
	</li>
	<?php }?>	
	<li style="float:right;text-align:right;width:60px;padding-right:10px;">
			<span type="submit" class="btn btn-primary" id="search_clear">清空</span>
		</li>
					<li style="float:right;text-align:right;width:70px;padding-right:16px;">
		<input type="submit" class="btn btn-primary" value="查询" id="submit_search">
	</li>
	
	</ul>
			</form>
			<table cellspacing=1 border="0" class="table table-hover">
				<thead>
					<tr class="main_right_content_content_title">
						<td width="55%">简介</td><td width="15%">发布人</td><td width="15%">发布时间</td>						<td width="20%">操作</td>
					</tr>
				</thead>
				<tbody>
					<?php					$i=0;
					foreach ( $items as $item ) {
					$edit_url = Yii::app()->createUrl('happy/update',array('id'=>$item->id,'page'=>$_REQUEST['page']));
					?>
						<tr class="main_right_content_content_body">
											<td title = "<?php echo $item->description ?>"><?php 
												if(mb_strlen($item->description, 'utf-8') > 40){
													echo mb_substr($item->description, 0, 39, 'utf-8')."....";
												}else{
													echo $item->description;
												}
											?></td>
										<td><?php echo $item->uname ?></td>
										<td><?php echo date('Y-m-d H:i:s', $item->created_time) ?></td>
										<td>
						<?php if($role_arr['dohappy'] & 1){?><a class="btn btn-primary btn-sm" href="<?php echo $edit_url?>">编辑</a><?php }?>
						<a class="btn btn-danger btn-sm action_del" data="<?php echo $item->id;?>">删除</a>
					</td>
				</tr>
				<?php } ?>				</tbody>
			</table>

		</div>
	</div>
	
	<input id="del_baseurl" type="hidden" value="<?php echo Yii::app()->createUrl("happy/delete",array('page'=>$pages->currentPage +1));?>" />
	<div class="main_footer">
		<div class="main_footer_page">
		<?php
		$path = substr ( dirname ( __FILE__ ), 0, - 5 ) . "layouts/searchPages.php";
		require_once ($path);
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
	
