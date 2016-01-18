<?php
/* @var $this FriendLeagueController */
/* @var $dataProvider CActiveDataProvider */
Yii::app ()->clientScript->registerCssFile ( Yii::app ()->request->baseUrl . "/themes/css/global.css" );
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
$url = "member_id={$_GET['member_id']}&name={$_GET['name']}&created_time1={$_GET['created_time1']}&created_time2={$_GET['created_time2']}&province={$_GET['province']}&city={$_GET['city']}&area={$_GET['area']}&benben_id={$_GET['benben_id']}&status={$_GET['status']}&status1={$_GET['status1']}&number2={$_GET['number2']}&number1={$_GET['number1']}";
$status = array('0'=>'启用', '1'=>'屏蔽', '2'=>'解散');
$status1 = array('6'=>'启用', '1'=>'禁用1周', '2'=>'禁用2周', '3'=>'禁用1个月', '4'=>'禁用3个月', '5'=>'无限期');
$role_arr  = json_decode(Yii::app()->session['role_arr'],true);
?>

<div class="main_right_content">
	<div class="main_right_content_title main_titleab">
		<div class="main_right_content_title_font">好友联盟管理</div>
		<div class="main_right_content_content_block_action_add"></div>
	</div>

	<div class="main_right_content_content" style="background: #F5F5F5;">
				<?php if($result['msg']) {?>
			<div class="alert alert-danger alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert">
				<span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
			</button>
			<strong>警告！</strong> <?php echo $result['msg'];?>
			</div>
			<?php }?>
			<form
			action="<?php echo Yii::app()->createUrl('friendLeague	/index')?>"
			method="get">
			<ul style="margin-top: 0">
				<li>
					<div class="form-group" style="padding: 0 0 0 5px;">
						<label for="benben_id" style="float: left; margin-top: 7px;">联盟名称:</label>
						<div class="col-sm-8" style="margin-bottom: 10px;">
							<input type="text" class="form-control" name="name" id="name"
								value="<?php echo $result['name']?>">
						</div>
					</div>
				</li>
				<li>
					<div class="form-group" style="padding: 0 0 0 5px;">
						<label for="benben_id" style="float: left; margin-top: 7px;">状　　态:</label>
						<div class="col-sm-8" style="margin-bottom: 10px;">
							<select class="form-control" name="status" id="status">
								<option value="-1">--请选择--</option>
								<option value="0" <?php if(isset($result['status']) && $result['status'] == 0) echo 'selected="selected"';?>>启用</option>
								<option value="1" <?php if($result['status'] == 1) echo 'selected="selected"';?>>屏蔽</option>
								<option value="2" <?php if($result['status'] == 2) echo 'selected="selected"';?>>解散</option>
								<!-- <option value="3" <?php if($result['status'] == 3) echo 'selected="selected"';?>>禁用1个月</option>
								<option value="4" <?php if($result['status'] == 4) echo 'selected="selected"';?>>禁用3个月</option>
								<option value="5" <?php if($result['status'] == 5) echo 'selected="selected"';?>>无限期</option> -->
							</select>
						</div>
					</div>
				</li>
				<li style="width:18%">
					<div class="form-group" style="padding: 0 0 0 5px;">
						<label for="number1" style="float: left; margin-top: 7px;">成员数量:</label>
						<div class="col-sm-8" style="margin-bottom: 10px;">
							<input type="text" class="form-control"
								name="number1" id="number1"
								value="<?php echo $result['number1']?>">
						</div>
					</div>
				</li>
				<li style="width:18%">
					<div class="form-group" style="padding: 0 0 0 5px;">
						<label for="number2" style="float: left; margin-top: 7px;">到:&nbsp;&nbsp;&nbsp;</label>
						<div class="col-sm-8" style="margin-bottom: 10px;">
							<input type="text" class="form-control"
								name="number2" 
								value="<?php echo $result['number2']?>">
						</div>
					</div>
				</li>
				
				<?php if($role_arr['doleague'] & 2){?>	
				<li
					style="float: right; text-align: right; width: 60px; padding-right: 10px;">
					<a href="/admin.php/friendLeague/phpexcel?<?php echo $url?>"><div
							class="btn btn-primary" id="putexcel">导出</div></a>
				</li>
				<?php }?>
				<li style="float:right;text-align:right;width:60px;padding-right:10px;">
			<span type="submit" class="btn btn-primary" id="search_more">更多</span>
		</li>
				
				<li style="float:right;text-align:right;width:60px;padding-right:10px;">
			<span type="submit" class="btn btn-primary" id="search_clear">清空</span>
		</li>
		<li
					style="float: right; text-align: right; width: 60px; padding-right: 10px;">
					<input type="hidden" value="-2" name="backurl"> <input
					type="submit" class="btn btn-primary" value="查询" id="submit_search">
				</li>
				</ul>
				<ul  class="member_ul">
				<li>
					<div class="form-group" style="padding: 0 0 0 5px;">
						<label for="sex" style="float: left; margin-top: 7px;">地　　区:</label>
						<div class="col-sm-8" style="margin:0px 0 10px;">
							<select class="form-control" name="province" id="province">
								<option value="-1">--请选择省-</option>
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
						<div class="col-sm-8" style="margin:0px 0 10px 55px;">
							<select class="form-control" name="city" id="city">
								<option value="-1">--请选择市-</option>
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
						<div class="col-sm-8" style="margin:0px 0 10px 56px;">
							<select class="form-control" name="area" id="area">
								<option value="-1">--请选择区-</option>
								<?php if(isset($res2)) {foreach ($res2 as $prv){?>
									<option value="<?php echo $prv['bid'];?>"
									<?php if($result['area'] == $prv['bid']) echo 'selected = "selected"';?>><?php echo $prv['area_name']?></option>
								<?php  }}?>
							</select>
						</div>
					</div>
				</li>
				<li >
					<div class="form-group" style="padding:0 0 0 5px;">
						<label  for="benben_id" style="float:left;margin-top:7px;">创建人状态:</label>	
						<div class="col-sm-7" style="margin-bottom:10px;">
							<select class="form-control" name="status1" id="status1">
								<option value="-1" >-请选择-</option>
								<?php foreach ($status1 as $key=>$value){?>
								<option <?php if($_GET['status1'] == $key && isset($_GET['status1'])) echo 'selected = "selected"';?> value="<?php echo $key?>"><?php echo $value?></option>
								<?php }?>	
							</select>
						</div>
					</div>					
				</li>
			</ul>
			<ul  class="member_ul">
				<li>
					<div class="form-group" style="padding: 0 0 0 5px;">
						<label for="benben_id" style="float: left; margin-top: 7px;">创建人:　</label>
						<div class="col-sm-8" style="margin-bottom: 10px;">
							<input type="text" class="form-control" name="member_id"
								id="member_id" value="<?php echo $result['member_name']?>">
						</div>
					</div>
				</li>
				<li style="width:18%">
					<div class="form-group" style="padding: 0 0 0 5px;">
						<label for="created_time1" style="float: left; margin-top: 7px;">创建时间:</label>
						<div class="col-sm-8" style="margin-bottom: 10px;">
							<input type="text" class="form-control created_time"
								name="created_time1" id="created_time1"
								value="<?php echo $result['created_time1']?>">
						</div>
					</div>
				</li>
				<li style="width:18%">
					<div class="form-group" style="padding: 0 0 0 5px;">
						<label for="created_time1" style="float: left; margin-top: 7px;">到:　　　</label>
						<div class="col-sm-8" style="margin-bottom: 10px;">
							<input type="text" class="form-control created_time"
								name="created_time2" id="created_time2"
								value="<?php echo $result['created_time2']?>">
						</div>
					</div>
				</li>
				<li style="width:20%">
					<div class="form-group" style="padding: 0 0 0 5px;">
						<label for="benben_id" style="float: left; margin-top: 7px;">创建人奔犇号:</label>
						<div class="col-sm-7" style="margin-bottom: 10px;">
							<input type="text" class="form-control" name="benben_id" id="benben_id"
								value="<?php echo $result['benben_id']?>">
						</div>
					</div>
				</li>
			</ul>

		</form>




		<!-- 		<div class="main_right_content_content"> -->
		<table cellspacing=1 border="0" class="table table-hover">
			<thead>
				<tr class="main_right_content_content_title">
					<td width="8%">头像</td>
					<td width="10%">联盟名称</td>
					<td width="8%">盟主名称</td>
					<td width="7%">奔犇号</td>
					<td width="15%">地区</td>
					<td width="8%">堂主数量</td>
					<td width="8%">成员数量</td>
					<td width="8%">创建人状态</td>
					<td width="5%">状态</td>
					<td width="14%">创建时间</td>
					<td width="10%">操作</td>
				</tr>
			</thead>
			<tbody>
					<?php
					
					$i = 0;
					foreach ( $items as $item ) {
						$edit_url = Yii::app ()->createUrl ( 'friendLeague/update', array (
								'id' => $item->id,
								'page' => $_REQUEST ['page'] 
						) );
						?>
						<tr class="main_right_content_content_body <?php if($item->is_delete) echo 'danger';?>">
					<td><img
						src="<?php echo  Yii::app()->request->baseUrl.$item->poster ?>"
						width="32"></td>
					<td><?php echo $item->name ?></td>
					<td><?php echo $item->mname ? $item->mname : $item->nickname ?></td>
					<td><?php echo $item->mbenben_id ?></td>
					<td><?php echo $areaInfo[$item->province].''.$areaInfo[$item->city] ?></td>
					<td><?php echo $chief_num[$item->id] ? $chief_num[$item->id] : 0?></td>
					<td><?php echo $item->number ?></td>
					<td><a title ="查看禁用记录" href="<?php echo Yii::app()->createUrl('friendDisable/service?id='.$item->member_id)?>" style="text-decoration:underline;"><?php if($item->league_disable == 0){ echo $status1[6]; }else{echo $status1[$item->league_disable];}?></a></td>
					<td><?php if($item->is_delete){echo "解散";}else{?><a title ="查看禁用记录" href="<?php echo Yii::app()->createUrl('friendDisable/lindex?id='.$item->id)?>" style="text-decoration:underline;"><?php echo $status[$item->status] ?></a><?php }?></td>
					<td><?php echo date('Y-m-d H:i', $item->created_time) ?></td>
					<td><a class="btn btn-danger btn-sm"
						href="<?php echo Yii::app()->createUrl('leagueMember/index', array('lid' => $item->id))?>">成员</a>
						<?php if($role_arr['doleague'] & 1){?><a class="btn btn-primary btn-sm" href="<?php echo $edit_url?>">编辑</a><?php }?>
						</td>
				</tr>
				<?php } ?>				</tbody>
		</table>

	</div>
</div>

<input id="del_baseurl" type="hidden"
	value="<?php echo Yii::app()->createUrl("friendLeague/delete",array('page'=>$pages->currentPage +1));?>" />
<div
	class="main_footer <?php //if($pages->pageCount <= 1) echo 'main_footer_hidden';?>">
	<div class="main_footer_page">
		<?php
		$path = substr ( dirname ( __FILE__ ), 0, - 12 ) . "layouts/searchPages.php";
		require_once ($path);
		?>
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
// 			) );
			?>
		</div>
</div>
</div>

