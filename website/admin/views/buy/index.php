<?php
/* @var $this BuyController */
/* @var $dataProvider CActiveDataProvider */
Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/themes/css/global.css");
$status1 = array('0'=>'启用', '1'=>'禁用1周', '2'=>'禁用2周', '3'=>'禁用1个月', '4'=>'禁用3个月', '5'=>'无限期');
$status = array("0" => "正常", "1" => "屏蔽");
$role_arr  = json_decode(Yii::app()->session['role_arr'],true);
?>

		<div class="main_right_content">
		<div class="main_right_content_title main_titleab">
			<div class="main_right_content_title_font">我要买管理</div>
			
		</div>
		<div class="main_right_content_content" style="background:#f5f5f5">
		<form action="<?php echo Yii::app()->createUrl('buy/index')?>" method="get">
			<ul style="margin-top:0;">
				<li  >
					<div class="form-group" style="padding:0 0 0 5px;">
						<label  for="benben_id" style="float:left;margin-top:7px;">发贴人:　</label>	
						<div class="col-sm-7" style="margin-bottom:10px;">
							<input type="text" class="form-control" name="name" id="name" value="<?php echo $_GET['name']?>">
						</div>
					</div>					
				</li>
				<li  >
					<div class="form-group" style="padding:0 0 0 5px;">
						<label  for="benben_id" style="float:left;margin-top:7px;">奔犇号:</label>	
						<div class="col-sm-7" style="margin-bottom:10px;">
							<input type="text" class="form-control" name="benben_id" id="benben_id" value="<?php echo $_GET['benben_id']?>">
						</div>
					</div>					
				</li>
				<li>
					<div class="form-group" style="padding: 0 0 0 5px;">
						<label for="sex" style="float: left; margin-top: 7px;">发帖人地区:</label>
						<div class="col-sm-7" style="margin-bottom: 10px;padding-right:0px">
							<select class="form-control" name="mprovince" id="mprovince">
								<option value="-1">--请选择--</option>
					<?php foreach ($province as $prv){?>
						<option value="<?php echo $prv->bid?>"
									<?php if($result['mprovince'] == $prv->bid) echo 'selected = "selected"';?>><?php echo $prv->area_name?></option>
					<?php  }?>
				</select>
						</div>
					</div>
				</li>
				<li style="width:10%">
					<div class="form-group" style="padding: 0 0 0 0px;">
						<div class="col-sm-12" style="margin-bottom: 10px;padding-right:0px;">
							<select class="form-control" name="mcity" id="mcity">
								<option value="-1">--请选择市--</option>
								<?php if(isset($mres)) {foreach ($mres as $prv){?>
									<option value="<?php echo $prv['bid'];?>"
									<?php if($result['mcity'] == $prv['bid']) echo 'selected = "selected"';?>><?php echo $prv['area_name']?></option>
								<?php  }}?>
							
							</select>
						</div>
						
					</div>
				</li>
				<li style="width:10%">
					<div class="form-group" style="padding: 0 0 0 0px;">
						<div class="col-sm-12" style="margin-bottom: 10px;padding-right:0px;">
							<select class="form-control" name="marea" id="marea">
								<option value="-1">--请选择区--</option>
								<?php if(isset($mres2)) {foreach ($mres2 as $prv){?>
									<option value="<?php echo $prv['bid'];?>"
									<?php if($result['marea'] == $prv['bid']) echo 'selected = "selected"';?>><?php echo $prv['area_name']?></option>
								<?php  }}?>
								
							</select>
						</div>
					</div>
				</li>
				<?php 
if(!isset($_GET['status1'])){
	$_GET['status1'] = -1;
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
if(!isset($_GET['street'])){
	$_GET['street'] = -1;
}
if(!isset($_GET['mprovince'])){
	$_GET['mprovince'] = -1;
}
if(!isset($_GET['mcity'])){
	$_GET['mcity'] = -1;
}
if(!isset($_GET['marea'])){
	$_GET['marea'] = -1;
}
if(!isset($_GET['is_accept'])){
	$_GET['is_accept'] = -1;
}
$url = "status1={$_GET['status1']}&status={$_GET['status']}&is_accept={$_GET['is_accept']}&benben_id={$_GET['benben_id']}&name={$_GET['name']}&created_time1={$_GET['created_time1']}&created_time2={$_GET['created_time2']}&deadline1={$_GET['deadline1']}&deadline2={$_GET['deadline2']}&province={$_GET['province']}&city={$_GET['city']}&area={$_GET['area']}&street={$_GET['street']}&mprovince={$_GET['mprovince']}&mcity={$_GET['mcity']}&marea={$_GET['marea']}";?>
		<?php if($role_arr['dorelease'] & 2){?>		
				<li style="float:right;text-align:right;width:70px;padding-right:20px;">
		<a href = "/admin.php/buy/phpexcel?<?php echo $url?>"><div class="btn btn-primary"  id="putexcel">导出</div></a>
	</li>
	<?php }?>
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
				<ul style="float: left">
				<li >
					<div class="form-group" style="padding:0 0 0 5px;">
						<label  for="benben_id" style="float:left;margin-top:7px;">发帖人状态:</label>	
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
				<li >
					<div class="form-group" style="padding:0 0 0 5px;">
						<label  for="benben_id" style="float:left;margin-top:7px;">接受报价:</label>	
						<div class="col-sm-7" style="margin-bottom:10px;">
							<select class="form-control" name="is_accept" id="is_accept">
								<option value="-1" >-请选择-</option>
								<option value="0" <?php if($result['is_accept'] == 0) echo 'selected="selected"';?>>-否-</option>
								<option value="1" <?php if($result['is_accept'] == 1) echo 'selected="selected"';?>>-是-</option>
								
							</select>
						</div>
					</div>					
				</li>
				<li style="float:right;text-align:right;width:78px;margin-right:20px;">
					<a href = "/admin.php/buy/pushlog"><div class="btn btn-primary"  id="putexcel">推送记录</div></a>
				</li>
				</ul>
				<ul  class="member_ul">
				<li >
				<div class="form-group" style="padding:0 0 0 5px;">
							<label  for="created_time1" style="float:left;margin-top:7px;">发贴时间:</label>	
							<div class="col-sm-7" style="margin-bottom:10px;">
								<input type="text" class="form-control created_time" name="created_time1"  id="created_time1"  value="<?php echo $_GET['created_time1']?>">
							</div>
					</div>
					</li>
					<li >
						<div class="form-group" style="padding:0 0 0 5px;">
							<label  for="created_time1" style="float:left;margin-top:7px;">到:　　</label>
							<div class="col-sm-7" style="margin-bottom:10px;">
								<input type="text" class="form-control created_time" name="created_time2"  id="created_time2"   value="<?php echo $_GET['created_time2']?>">
							</div>
					</div>
					</li>
					<li>
				<div class="form-group" style="padding:0 0 0 5px;">
							<label  for="created_time1" style="float:left;margin-top:7px;">截止时间　:</label>	
							<div class="col-sm-7" style="margin-bottom:10px;">
								<input type="text" class="form-control created_time" name="deadline1"  value="<?php echo $_GET['deadline1']?>">
							</div>
					</div>
					</li>
					<li >
						<div class="form-group" style="padding:0 0 0 5px;">
							<label  for="created_time1" style="float:left;margin-top:7px;">到:　　　</label>	
							<div class="col-sm-7" style="margin-bottom:10px;">
								<input type="text" class="form-control created_time" name="deadline2"   value="<?php echo $_GET['deadline2']?>">
							</div>
					</div>
					</li>				
				</ul>
				<ul  class="member_ul">
					<li>
					<div class="form-group" style="padding: 0 0 0 5px;">
						<label for="sex" style="float: left; margin-top: 7px;">地　　区:</label>
						<div class="col-sm-7" style="margin-bottom: 10px;">
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
						<div class="col-sm-7" style="margin-bottom: 10px;margin-left:40px;">
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
						<div class="col-sm-7" style="margin-bottom: 10px;margin-left:67px;">
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
						<td width="12%">标题</td>
						<td width="5%">数量</td>
						<td width="7%">发帖人</td>
						<td width="5%">奔犇号</td>
						<td width="12%">地区</td>
						<td width="7%">发帖人状态</td>
						<td width="6%">帖子状态</td>
						<td width="6%">报价人数</td>
						<td width="6%">接受报价</td>
						<td width="8%">截止时间</td>
						<td width="8%">发布时间</td>						
						<td width="15%">操作</td>
					</tr>
				</thead>
				<tbody>
					<?php					$i=0;
					foreach ( $items as $item ) {
					$edit_url = Yii::app()->createUrl('buy/update',array('id'=>$item->id,'member_id'=>$item->member_id,'page'=>$_REQUEST['page']));
					$push_url = Yii::app()->createUrl('buy/pushbuy',array('id'=>$item->id,'member_id'=>$item->member_id,'page'=>$_REQUEST['page']));
					?>
						<tr class="main_right_content_content_body">
										<td><?php echo $item->title ?></td>
										<td><?php echo $item->amount ?></td>
										<td><?php echo $item->mname ? $item->mname : $item->nick_name ?></td>
										<td><?php echo $item->benben_id; ?></td>
										<td><?php echo $areaInfo[$item->province].''.$areaInfo[$item->city].''.$areaInfo[$item->area];?></td>
										<td><a title ="查看禁用记录" href="<?php echo Yii::app()->createUrl('buy/log?id='.$item->member_id)?>" style="text-decoration:underline;"><?php echo $status1[$item->status1] ?></a></td>
										
										<td><a href="<?php echo Yii::app()->createUrl('buyDisable/index?id='.$item->id)?>" style="text-decoration:underline"><?php echo $status[$item->status] ?></a></td>
										<td><?php echo $item->quoted_number ?></td>
										<td><?php echo $item->is_accept == 1?'是':'否'; ?></td>
										<td><?php echo date('m-d H:i', $item->deadline) ?></td>
										<td><?php echo date('m-d H:i', $item->created_time)  ?></td>
										<td>
										<a class="btn btn-danger btn-sm " href="<?php echo Yii::app()->createUrl('quote/index?id='.$item->id)?>">报价</a>
						<?php if($role_arr['dorelease'] & 1){?><a class="btn btn-primary btn-sm" href="<?php echo $edit_url?>">编辑</a><?php }?>
						<a class="btn btn-primary btn-sm" href="<?php echo $push_url?>">推送</a>
					</td>
				</tr>
				<?php } ?>				</tbody>
			</table>

		</div>
	</div>
	
	<input id="del_baseurl" type="hidden" value="<?php echo Yii::app()->createUrl("buy/delete",array('page'=>$pages->currentPage +1));?>" />
	<div class="main_footer">
		<div class="main_footer_page">
		<?php 
				$path = substr(dirname(__FILE__), 0, -3)."layouts/searchPages.php";
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
<script>
//省市区数据调整
$('#mprovince').change(function(){
	var val = $(this).val();
	$.post('/admin.php/friendLeague/getarea/bid/'+val, function(e){
		if (e) {
			$('#mcity').html(e);
			$('#marea').html('<option value="-1">请选择区</option>');
			$('#mstreet').html('<option value="-1">请选择</option>');
		}	
	})
});
$('#mcity').change(function(){
	var val = $(this).val();
	$.post('/admin.php/friendLeague/getarea/bid/'+val, function(e){
		if (e) {
			$('#marea').html(e);
			$('#mstreet').html('<option value="-1">请选择</option>');
		}	
	})
});
$('#marea').change(function(){
	var val = $(this).val();
	$.post('/admin.php/friendLeague/getarea/bid/'+val, function(e){
		if (e) {
			$('#mstreet').html(e);
		}	
	})
});
</script>	
