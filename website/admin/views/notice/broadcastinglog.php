<?php
Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/themes/css/global.css");
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/themes/js/jquery-1.11.1.min.js");

/* @var $this BroadcastingLogController */
/* @var $dataProvider CActiveDataProvider */
$type = array(0=>'否',1=>'是');
?>

		<div class="main_right_content">
		<div class="main_right_content_title main_titleab">
			<div class="main_right_content_title_font">小喇叭管理</div>
			<div class="main_right_content_content_block_action_add">
			</div>
		</div>
		<div class="main_right_content_content" style="background:#F5F5F5;">
		<?php if($msg) {?>
				<div class="alert alert-danger alert-dismissible" role="alert">
					  <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					  <strong>警告！</strong> <?php echo $msg;?>
				</div>
				<?php }?>
				
				<form action="<?php echo Yii::app()->createUrl('notice/broadcastingLog')?>" method="get">
			<ul style="margin-top:0;">
				<li>
					<div class="form-group" style="padding:0 0 0 5px;">
						<label  for="benben_id" style="float:left;margin-top:7px;">　奔犇号:</label>	
						<div class="col-sm-8" style="margin-bottom:10px;">
							<input type="text" class="form-control" name="benben_id" id="benben_id" value="<?php echo $result['benben_id']?>">
						</div>
					</div>
				</li>
				<li>
					<div class="form-group" style="padding:0 0 0 5px;">
						<label  for="benben_id" style="float:left;margin-top:7px;">喊话时段:</label>	
						<div class="col-sm-8" style="margin-bottom:10px;">
							<input type="text" class="form-control created_time" name="created_time1" id="created_time1" value="<?php echo $result['created_time1']?>">
						</div>
					</div>
				</li>
				<li>
					<div class="form-group" style="padding:0 0 0 5px;">
						<label  for="benben_id" style="float:left;margin-top:7px;">到:　</label>	
						<div class="col-sm-8" style="margin-bottom:10px;">
							<input type="text" class="form-control created_time" name="created_time2" id="created_time2" value="<?php echo $result['created_time2']?>">
						</div>
					</div>
				</li>
				
				<?php $url = "short_phone={$_GET['short_phone']}&phone={$_GET['phone']}&name={$_GET['name']}&created_time1={$_GET['created_time1']}&created_time2={$_GET['created_time2']}&dj=-1&province={$_GET['province']}&city={$_GET['city']}&area={$_GET['area']}&street={$_GET['street']}&status={$_GET['status']}";?>
	<li style="float:right;text-align:right;width:70px;padding-right:20px;">
		<a href = "/admin.php/notice/statistic"><div class="btn btn-primary"  id="putexcel">统计</div></a>
	</li>
	<li style="float:right;text-align:right;width:60px;padding-right:10px;">
			<span type="submit" class="btn btn-primary" id="search_more">更多</span>
		</li>
	
	<li style="float:right;text-align:right;width:60px;padding-right:10px;">
			<span type="submit" class="btn btn-primary" id="search_clear">清空</span>
		</li>
		<li style="float:right;text-align:right;width:60px;padding-right:10px;">
		<input type="submit" class="btn btn-primary" value="查询" id="submit_search">
	</li>				
				</ul>
				<ul class="member_ul">
				<li>
					<div class="form-group" style="padding:0 0 0 5px;">
						<label  for="benben_id" style="float:left;margin-top:7px;">手机号码:</label>	
						<div class="col-sm-8" style="margin-bottom:10px;">
							<input type="text" class="form-control" name="phone" id="phone" value="<?php echo $result['phone']?>">
						</div>
					</div>
				</li>
				<li>
					<div class="form-group" style="padding:0 0 0 5px;">
						<label  for="benben_id" style="float:left;margin-top:7px;">发送对象:</label>	
						<div class="col-sm-8" style="margin-bottom:10px;">
						<select class="form-control" name="obj" id="obj">
							<option value="-1">--请选择--</option>
							<option value="1" <?php if($result['obj'] ==1) echo "selected"?>>好友</option>
							<option value="2" <?php if($result['obj'] ==2) echo "selected"?>>好友联盟</option>
							<option value="3" <?php if($result['obj'] ==3) echo "selected"?>>好友和好友联盟</option>
						</select>
						</div>
					</div>
				</li>
				<li style="width:26%;">
					<div class="form-group" style="padding:0 0 0 5px;">
						<label  for="sex" style="float:left;margin-top:7px;">是否为号码直通车:</label>	
						<div class="col-sm-5" style="margin-bottom:10px;">
							<select class="form-control" name="is_type" id="is_type">
								<option value="-1">--请选择--</option>
								<option value="1" <?php if($result['is_type'] ==1) echo "selected"?>>是</option>
								<option value="2" <?php if($result['is_type'] ==2) echo "selected"?>>否</option>
							</select>
						</div>
					</div>
				</li><li>
					<div class="form-group" style="padding:0 0 0 5px;">
						<label  for="benben_id" style="float:left;margin-top:7px;">喊话次数:</label>	
						<div class="col-sm-8" style="margin-bottom:10px;">
							<input type="text" class="form-control" name="recive1"  value="<?php echo $result['recive1']?>">
						</div>
					</div>
				</li>
				<li>
					<div class="form-group" style="padding:0 0 0 5px;">
						<label  for="benben_id" style="float:left;margin-top:7px;">到:　</label>	
						<div class="col-sm-8" style="margin-bottom:10px;">
							<input type="text" class="form-control" name="recive2"  value="<?php echo $result['recive2']?>">
						</div>
					</div>
				</li>
				</ul>
				<ul class="member_ul">
				<li >
		<div class="form-group" style="padding:0 0 0 5px;">
			<label  for="sex" style="float:left;margin-top:7px;">地　　区:</label>	
			<div class="col-sm-8" style="margin-bottom:10px;">
				<select class="form-control" name="province" id="province">
					<option value="-1">--请选择--</option>
					<?php foreach ($province as $prv){?>
						<option value="<?php echo $prv->bid?>"  <?php if($result['province'] == $prv->bid) echo 'selected = "selected"';?>><?php echo $prv->area_name?></option>
					<?php  }?>
				</select>
			</div>
		</div>
	</li>
	<li>
		<div class="form-group" style="padding:0 0 0 5px;">
			<div class="col-sm-8" style="margin-bottom:10px;margin-left:29px;">
				<select class="form-control" name="city" id="city">
					<option value="-1">--请选择市--</option>
					<?php if(isset($res)) {foreach ($res as $prv){ ?>
									<option value="<?php echo $prv['bid'];?>"
									<?php if($result['city'] == $prv['bid']) echo 'selected = "selected"';?>><?php  echo $prv['area_name']?></option>
								<?php  }}?>	
				</select>
			</div>
			
		</div>
	</li>
	<li>
		<div class="form-group" style="padding:0 0 0 5px;">
		<div class="col-sm-8" style="margin-bottom:10px;margin-left:29px;">
			<select class="form-control" name="area"  id="area">
				<option value="-1">--请选择区--</option>
				<?php if(isset($res2)) {foreach ($res2 as $prv){?>
									<option value="<?php echo $prv['bid'];?>"
									<?php if($result['area'] == $prv['bid']) echo 'selected = "selected"';?>><?php echo $prv['area_name']?></option>
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
						<td width="10%">手机号码</td>
						<td width="15%">地区</td>
						<td width="10%">号码直通车</td>
						<td width="10%">发送对象</td>
						<td width="10%">发送人数</td>
						<td width="15%">发送时间</td>
						<td width="10%">累计喊话次数</td>
						<td width="10%">操作</td>
					</tr>
				</thead>
				<tbody>
					<?php					$i=0;
					foreach ( $items as $item ) {
					$edit_url = Yii::app()->createUrl('notice/broaddetail',array('id'=>$item->id,'page'=>$_REQUEST['page']));
					?>
						<tr class="main_right_content_content_body">
										<td><?php echo $item->m_benben_id ?></td>
										<td><?php echo $item->m_phone ?></td>
										<td><?php echo $areaInfo[$item->m_p].$areaInfo[$item->m_c].$areaInfo[$item->m_a]?></td>
										<td><?php echo $type[$item->type] ?></td>
										<td><?php if($item->league_id && empty($item->friend_id)){echo "好友联盟";}else if(empty($item->league_id) && $item->friend_id){ echo "好友";}else if($item->league_id && $item->friend_id){ echo "好友和好友联盟";}?></td>
										<td><?php echo $item->receive_count?></td>
										<td><?php echo  date("Y-m-d H:i:s",$item->created_time) ?></td>
										<td><?php echo $item['counts'];?></td>
										<td>
						<a class="btn btn-primary btn-sm" href="<?php echo $edit_url?>">查看</a>
						<!--  <a class="btn btn-danger btn-sm action_del" data="<?php echo $item->id;?>">删除</a>-->
					</td>
				</tr>
				<?php } ?>				</tbody>
			</table>

		</div>
	</div>
	
	<input id="del_baseurl" type="hidden" value="<?php echo Yii::app()->createUrl("broadcastingLog/delete",array('page'=>$pages->currentPage +1));?>" />
	<div class="main_footer <?php //if($pages->pageCount <= 1) echo 'main_footer_hidden';?>">
		<div class="main_footer_page">
		<?php 
				$path = substr(dirname(__FILE__), 0, -6)."layouts/searchPages.php";
				//var_dump(dirname(__FILE__));
				require_once($path);  
			?>
			<?php	
			$page = intval($_GET['page']) ?  intval($_GET['page']) : 1;
			$url = explode("?", Yii::app()->request->getUrl());
	$link = Yii::app()->request->hostInfo.$url[0]."?";
			echo '<ul class="yiiPager" id="yw0">'.$this->textPage($pages->pageCount , $page, $link).'</ul>';
		
// 			<?php			$this->widget ( 'CLinkPager', array (
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
	
