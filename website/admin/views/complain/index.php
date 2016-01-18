<?php
/* @var $this ComplainController */
/* @var $dataProvider CActiveDataProvider */
Yii::app ()->clientScript->registerCssFile ( Yii::app ()->request->baseUrl . "/themes/css/global.css" );

?>

		<div class="main_right_content">
		<div class="main_right_content_title main_titleab">
			<div class="main_right_content_title_font">投诉/建议管理</div>
			
		</div>
		<div class="main_right_content_content" style="background:#F5F5F5;">
		<form
			action="<?php echo Yii::app()->createUrl('complain/index')?>"
			method="get">
			<ul>
				<li>
					<div class="form-group" style="padding: 0 0 0 5px;">
						<label for="benben_id" style="float: left; margin-top: 7px;">奔犇号:</label>
						<div class="col-sm-8" style="margin-bottom: 10px;">
							<input type="text" class="form-control" name="benben_id" id="benben_id"
								value="<?php echo $result['benben_id']?>">
						</div>
					</div>
				</li>
				<li>
					<div class="form-group" style="padding: 0 0 0 5px;">
						<label for="phone" style="float: left; margin-top: 7px;">手机号码:</label>
						<div class="col-sm-8" style="margin-bottom: 10px;">
							<input type="text" class="form-control" name="phone"
								id="phone" value="<?php echo $result['phone']?>">
						</div>
					</div>
				</li>
				<?php
if (! isset ( $_GET ['province'] )) {
	$_GET ['province'] = - 1;
}
if (! isset ( $_GET ['city'] )) {
	$_GET ['city'] = - 1;
}
if (! isset ( $_GET ['area'] )) {
	$_GET ['area'] = - 1;
}
if (! isset ( $_GET ['street'] )) {
	$_GET ['street'] = - 1;
}
$url = "member_id={$_GET['member_id']}&name={$_GET['name']}&created_time1={$_GET['created_time1']}&created_time2={$_GET['created_time2']}&province={$_GET['province']}&city={$_GET['city']}&area={$_GET['area']}&street={$_GET['street']}";
?>
					
		<!--  	<li
					style="float: right; text-align: right; width: 112px; padding-right: 20px;">
					<a href="/admin.php/friendLeague/phpexcel?<?php echo $url?>">
					<div class="btn btn-primary" id="putexcel">导出EXCEL</div></a>
				</li> -->
				<li style="float:right;text-align:right;width:70px;padding-right:16px;">
			<span type="submit" class="btn btn-primary" id="search_more">更多</span>
		</li>
		<li style="float:right;text-align:right;width:60px;padding-right:10px;">
			<span type="submit" class="btn btn-primary" id="search_clear">清空</span>
		</li>
				<li
					style="float: right; text-align: right; width: 70px; padding-right: 16px;">
					<input type="hidden" value="-2" name="backurl"> <input
					type="submit" class="btn btn-primary" value="查询" id="submit_search">
				</li>
				
				</ul>
				
<ul  class="member_ul">
				<li>
					<div class="form-group" style="padding: 0 0 0 5px;">
						<label for="sex" style="float: left; margin-top: 7px;">地　区:</label>
						<div class="col-sm-8" style="margin-bottom: 10px;">
							<select class="form-control" name="province" id="province">
								<option value="-1">--请选择--</option>
								<?php foreach ($province as $prv){?>
									<option value="<?php echo $prv->bid?>"
									<?php if($result['province'] == $prv->bid) echo 'selected = "selected"';?>><?php echo $prv->area_name?></option>
								<?php  }?>
							</select>
						</div>
						
					</div>
				</li>
				<li>
					<div class="form-group" style="padding: 0 0 0 5px;">
						<div class="col-sm-8" style="margin-bottom: 10px;margin-left:54px;">
							<select class="form-control" name="city" id="city">
								<option value="-1">--请选择市--</option>
								<?php if(isset($res)) {foreach ($res as $prv){?>
									<option value="<?php echo $prv['bid'];?>"
									<?php if($result['city'] == $prv['bid']) echo 'selected = "selected"';?>><?php echo $prv['area_name']?></option>
								<?php  }}?>
							</select>
						</div>
						
					</div>
				</li>
				<li>
					<div class="form-group" style="padding: 0 0 0 5px;">
						<div class="col-sm-8" style="margin-bottom: 10px;margin-left:30px;">
							<select class="form-control" name="area" id="area">
								<option value="-1">--请选择区--</option>
								<?php if(isset($res2)) {foreach ($res2 as $prv){?>
									<option value="<?php echo $prv['bid'];?>"
									<?php if($result['area'] == $prv['bid']) echo 'selected = "selected"';?>><?php echo $prv['area_name']?></option>
								<?php  }}?>

							</select>
						</div>
						
					</div>
				</li>
				<li>
					<div class="form-group" style="padding: 0 0 0 5px;">
						<div class="col-sm-8" style="margin-bottom: 10px;">
							<select class="form-control" name="street" id="street">
								<option value="-1">--请选择街道--</option>
								<?php if(isset($res3)) {foreach ($res3 as $prv){?>
									<option value="<?php echo $prv['bid'];?>"
									<?php if($result['street'] == $prv['bid']) echo 'selected = "selected"';?>><?php echo $prv['area_name']?></option>
								<?php  }}?>
							</select>
						</div>
						
					</div>
				</li>

			</ul>
		</form>
			<table cellspacing=1 border="0" class="table table-hover">
				<thead>
					<tr class="main_right_content_content_title">
						<td width="10%">奔犇号</td>
						<td width="10%">昵称</td>
						<td width="15%">手机号码</td>
						<td width="40%">反馈内容</td>
						<td width="15%">反馈时间</td>						
						<td width="10%">操作</td>
					</tr>
				</thead>
				<tbody>
					<?php					$i=0;
					foreach ( $items as $item ) {
					$edit_url = Yii::app()->createUrl('complain/update',array('id'=>$item->id,'page'=>$_REQUEST['page']));
					?>
						<tr class="main_right_content_content_body">
						                    <td><?php echo $item->benben_id ?></td>
											<td><?php echo $item->sname ?></td>
											<td><?php echo $item->phone ?></td>
										<td><?php 
											if(mb_strlen($item->info, 'utf-8') > 30){
												echo mb_substr($item->info, 0, 30, 'utf-8').'...';
											}else{
												echo $item->info;
											}
										 ?></td>
										<td><?php echo date('Y-m-d H:i:s', $item->created_time) ?></td>
										<td>
						<a class="btn btn-primary btn-sm" href="<?php echo $edit_url?>">查看</a>
					</td>
				</tr>
				<?php } ?>				</tbody>
			</table>

		</div>
	</div>
	
	<input id="del_baseurl" type="hidden" value="<?php echo Yii::app()->createUrl("complain/delete",array('page'=>$pages->currentPage +1));?>" />
	<div class="main_footer <?php //if($pages->pageCount <= 1) echo 'main_footer_hidden';?>">
		<div class="main_footer_page">
		<?php
		$path = substr ( dirname ( __FILE__ ), 0, - 8 ) . "layouts/searchPages.php";
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
	
