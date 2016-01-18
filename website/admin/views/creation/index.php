<?php
/* @var $this CreationController */
/* @var $dataProvider CActiveDataProvider */
Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/themes/css/global.css");
$status1 = array('0'=>'启用', '1'=>'禁用1周', '2'=>'禁用2周', '3'=>'禁用1个月', '4'=>'禁用3个月', '5'=>'无限期');
$status = array("0" => "正常", "1" => "屏蔽");
$type = array("0" => "图文", "1" => "音频");
$role_arr  = json_decode(Yii::app()->session['role_arr'],true);
?>

		<div class="main_right_content">
		<div class="main_right_content_title main_titleab">
			<div class="main_right_content_title_font">微创作管理</div>

		</div>
		<div class="main_right_content_content" style="background:#F5F5F5;">
				<?php if($result['msg']) {?>
			<div class="alert alert-danger alert-dismissible" role="alert">
			  <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
			  <strong>警告！</strong> <?php echo $result['msg'];?>
			</div>
			<?php }?>
			<form action="<?php echo Yii::app()->createUrl('creation/index')?>" method="get">
			<ul style="margin-top:0;">
				<li>
					<div class="form-group" style="padding:0 0 0 5px;">
						<label  for="benben_id" style="float:left;margin-top:7px;">奔犇号:</label>	
						<div class="col-sm-8" style="margin-bottom:10px;">
							<input type="text" class="form-control" name="benben_id" id="benben_id" value="<?php echo $result['benben_id']?>">
						</div>
					</div>					
				</li>
				
				<li  style="width:18%">
						<div class="form-group" style="padding:0 0 0 5px;">
							<label  for="created_time1" style="float:left;margin-top:7px;">发贴时段:</label>	
							<div class="col-sm-8" style="margin-bottom:10px;">
								<input type="text" class="form-control created_time" name="created_time1"  id="created_time1"  value="<?php echo $result['created_time1']?>">
							</div>
					</div>
					</li>
					<li style="width:18%">
						<div class="form-group" style="padding:0 0 0 5px;">
							<label  for="created_time1" style="float:left;margin-top:7px;">到:&nbsp;&nbsp;&nbsp;</label>	
							<div class="col-sm-8" style="margin-bottom:10px;">
								<input type="text" class="form-control created_time" name="created_time2"  id="created_time2"   value="<?php echo $result['created_time2']?>">
							</div>
					</div>
					</li>
					<li style="width:18%">
					<div class="form-group" style="padding:0 0 0 5px;">
						<label  for="benben_id" style="float:left;margin-top:7px;">类型:</label>	
						<div class="col-sm-8" style="margin-bottom:10px;">
							<select class="form-control" name="type" id="type">
								<option value="-1" >--请选择--</option>
								<option value="2" <?php if($result['type'] == 2) echo 'selected = "selected"';?>>图文</option>
								<option value="1" <?php if($result['type'] == 1) echo 'selected = "selected"';?>>音频</option>
							</select>
						</div>
					</div>					
				</li>
								<?php 
if(!isset($_GET['type'])){
	$_GET['type'] = -1;
}
if(!isset($_GET['status'])){
	$_GET['status'] = -1;
}
if(!isset($_GET['province'])){
	$_GET['province'] = -1;
}
if(!isset($_GET['city'])){
	$_GET['city'] = -1;
}
if(!isset($_GET['area'])){
	$_GET['area'] = -1;
}
if(!isset($_GET['status1'])){
	$_GET['status1'] = -1;
}
$url = "type={$_GET['type']}&benben_id={$_GET['benben_id']}&status={$_GET['status']}&name={$_GET['name']}&created_time1={$_GET['created_time1']}&created_time2={$_GET['created_time2']}&province={$_GET['province']}&city={$_GET['city']}&area={$_GET['area']}&status1={$_GET['status1']}";?>
		<?php if($role_arr['docreation'] & 2){?>		
				<li style="float:right;text-align:right;width:70px;padding-right:20px;">
		<a href = "/admin.php/creation/phpexcel?<?php echo $url?>"><div class="btn btn-primary"  id="putexcel">导出</div></a>
	</li>
	<?php }?>
	<li style="float:right;text-align:right;width:70px;padding-right:16px;">
			<span type="submit" class="btn btn-primary" id="search_more">更多</span>
		</li>
	
	<li style="float:right;text-align:right;width:60px;padding-right:10px;">
			<span type="submit" class="btn btn-primary" id="search_clear">清空</span>
		</li>
		<li style="float:right;text-align:right;width:70px;padding-right:16px;">
		<input type="submit" class="btn btn-primary" value="查询" id="submit_search">
	</li>
				</ul>
				<ul  class="member_ul">
				<li  >
					<div class="form-group" style="padding:0 0 0 5px;">
						<label  for="benben_id" style="float:left;margin-top:7px;">发贴人:</label>	
						<div class="col-sm-8" style="margin-bottom:10px;">
							<input type="text" class="form-control" name="name" id="name" value="<?php echo $_GET['name']?>">
						</div>
					</div>					
				</li>
				<li >
					<div class="form-group" style="padding:0 0 0 5px;">
						<label  for="benben_id" style="float:left;margin-top:7px;">发帖人状态:</label>	
						<div class="col-sm-7" style="margin-bottom:10px;">
							<select class="form-control" name="status1" id="status1">
								<option value="-1" >--请选择--</option>
								<?php foreach ($status1 as $key=>$value){?>
								<option <?php if($result['status1'] == $key && isset($result['status1'])) echo 'selected = "selected"';?> value="<?php echo $key?>"><?php echo $value?></option>
								<?php }?>	
							</select>
						</div>
					</div>					
				</li>				
				<li >
					<div class="form-group" style="padding:0 0 0 5px;">
						<label  for="benben_id" style="float:left;margin-top:7px;">帖子状态:</label>	
						<div class="col-sm-7" style="margin-bottom:10px;">
							<select class="form-control" name="status" id="type">
								<option value="-1" >-请选择-</option>
								<option value="2" <?php if($_GET['status'] == 2) echo 'selected = "selected"';?>>正常</option>
								<option value="1" <?php if($_GET['status'] == 1) echo 'selected = "selected"';?>>屏蔽</option>
							</select>
						</div>
					</div>					
				</li>
				</ul>
				<ul  class="member_ul">
				<li>
					<div class="form-group" style="padding: 0 0 0 5px;">
						<label for="sex" style="float: left; margin-top: 7px;">地　　区:</label>
						<div class="col-sm-8" style="margin-bottom: 10px;">
							<select class="form-control" name="province" id="province">
								<option value="-1">--请选择省--</option>
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
						<div class="col-sm-8" style="margin-bottom: 10px;margin-left:25px;">
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
						<div class="col-sm-8" style="margin-bottom: 10px;margin-left:29px;">
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

			</ul>
			</form>
			<table cellspacing=1 border="0" class="table table-hover">
				<thead>
					<tr class="main_right_content_content_title">
						<td width="10%">发帖人</td>
						<td width="10%">奔犇号</td>
						<td width="10%">发帖人状态</td>
						<td width="9%">帖子状态</td>
						<td width="5%">类型</td>
						<td width="8%">浏览量</td>
						<td width="8%">点赞数</td>
						<td width="8%">关注数</td>
						<td width="8%">评论数</td>		
						<td width="13%">发帖时间</td>				
						<td width="11%">操作</td>
					</tr>
				</thead>
				<tbody>
					<?php					$i=0;
					foreach ( $items as $item ) {
					$edit_url = Yii::app()->createUrl('creation/update',array('id'=>$item->id,'page'=>$_REQUEST['page']));
					?>
						<tr class="main_right_content_content_body <?php if($item->is_delete == 1) echo 'danger';?>">
										
										<td><?php echo $item->mname ? $item->mname : $item->nick_name ?></td>
										<td><?php echo $item->benben_id ?></td>
										<td><a title ="查看禁用记录" href="<?php echo Yii::app()->createUrl('creation/log?id='.$item->member_id)?>" style="text-decoration:underline;"><?php echo $status1[$item->status1] ?></a></td>
										<td><?php if($item->is_delete){echo "已删除";}else{?><a title ="查看禁用记录" href="<?php echo Yii::app()->createUrl('creationDisable/index?id='.$item->id)?>" style="text-decoration:underline;"><?php echo $status[$item->status] ?></a><?php }?></td>
										<td><?php echo $type[$item->type] ?></td>
										<td><?php echo $item->views ?></td>
										<td><?php echo $item->goods ?></td>
										<td><?php echo count($attention[$item->id]) ?></td>
										<td><?php echo count($comment[$item->id]) ?></td>
										<td><?php echo date('Y-m-d H:i:s', $item->created_time) ?></td>
										<td>
										<a class="btn btn-danger btn-sm " href="<?php echo Yii::app()->createUrl('creationComment/index?id='.$item->id)?>">评论</a>
							<?php if($role_arr['docreation'] & 1){?><a <?php if($item->is_delete){echo 'style="display:none"';}?> class="btn btn-primary btn-sm" href="<?php echo $edit_url?>&back=<?php echo $result['goback']?>" >编辑</a><?php }?>
						
					</td>
				</tr>
				<?php } ?>				</tbody>
			</table>

		</div>
	</div>
	
	<input id="del_baseurl" type="hidden" value="<?php echo Yii::app()->createUrl("creation/delete",array('page'=>$pages->currentPage +1));?>" />
	<div class="main_footer">
		<div class="main_footer_page">
			<?php 
				$path = substr(dirname(__FILE__), 0, -8)."layouts/searchPages.php";
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
	
