<?php
/* @var $this BxapplyController */
/* @var $dataProvider CActiveDataProvider */
Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/themes/css/global.css");
$role_arr  = json_decode(Yii::app()->session['role_arr'],true);
$status = array("0"=>"等待审核", "1"=>"未通过", "2" => "退回重申", "3" => "已经通过", "4" => "撤消");
?>


<?php
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/themes/js/jquery-1.11.1.min.js");
?>
<script type="text/javascript">
	$(function(){
		var page = <?php echo $pages->pageCount?>;
		
	});
</script>

		<div class="main_right_content">
		<div class="main_right_content_title main_titleab">
			<div class="main_right_content_title_font">百姓网管理</div>
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
			<form action="<?php echo Yii::app()->createUrl('bxapply/index')?>" method="get">
			<ul style="margin-top:0;">
				<li>
					<div class="form-group" style="padding:0 0 0 5px;">
						<label  for="benben_id" style="float:left;margin-top:7px;">手 &nbsp;机 号:</label>	
						<div class="col-sm-8" style="margin-bottom:10px;">
							<input type="text" class="form-control" name="phone" id="phone" value="<?php echo $result['phone']?>">
						</div>
					</div>
				</li>
				<li>
					<div class="form-group" style="padding:0 0 0 5px;">
						<label  for="benben_id" style="float:left;margin-top:7px;">姓名:</label>	
						<div class="col-sm-8" style="margin-bottom:10px;">
							<input type="text" class="form-control" name="name" id="name" value="<?php echo $result['name']?>">
						</div>
					</div>
				</li>
				<li style="width:18%">
					<div class="form-group" style="padding:0 0 0 5px;">
						<label  for="benben_id" style="float:left;margin-top:7px;">短号:</label>	
						<div class="col-sm-8" style="margin-bottom:10px;">
							<input type="text" class="form-control" name="short_phone" id="short_phone" value="<?php echo $result['short_phone']?>">
						</div>
					</div>
				</li>
				<li style="width:18%">
					<div class="form-group" style="padding:0 0 0 5px;">
						<label  for="benben_id" style="float:left;margin-top:7px;">提交人:</label>	
						<div class="col-sm-8" style="margin-bottom:10px;">
							<input type="text" class="form-control" name="member_id" id="member_id" value="<?php echo $result['member_id']?>">
						</div>
					</div>
				</li>
				<?php $url = "short_phone={$_GET['short_phone']}&phone={$_GET['phone']}&name={$_GET['name']}&created_time1={$_GET['created_time1']}&created_time2={$_GET['created_time2']}&cancel_time1={$_GET['cancel_time1']}&cancel_time2={$_GET['cancel_time2']}&dj=-1&province={$_GET['province']}&city={$_GET['city']}&area={$_GET['area']}&street={$_GET['street']}&status={$_GET['status']}&member_id={$_GET['member_id']}";?>
	<?php if($role_arr['dobaixing'] & 2){?>
	<li style="float:right;text-align:right;width:70px;padding-right:20px;">
		<a href = "/admin.php/bxapply/phpexcel?<?php echo $url?>"><div class="btn btn-primary"  id="putexcel">导出</div></a>
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
				<ul class="member_ul">
				<li>
					<div class="form-group" style="padding:0 0 0 5px;">
						<label  for="benben_id" style="float:left;margin-top:7px;">申请时间:</label>	
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
				<li>
					<div class="form-group" style="padding:0 0 0 5px;">
						<label  for="sex" style="float:left;margin-top:7px;">状态:</label>	
						<div class="col-sm-8" style="margin-bottom:10px;">
							<select class="form-control" name="status" id="status">
								
								<option value="-1" >--请选择--</option>
								<option value="0" <?php if($result['status'] == 0) echo 'selected = "selected"';?>>等待审核</option>
								<option value="1" <?php if($result['status'] == 1) echo 'selected = "selected"';?>>未通过</option>
								<option value="2" <?php if($result['status'] == 2) echo 'selected = "selected"';?>>退回重申</option>
								<option value="3" <?php if($result['status'] == 3) echo 'selected = "selected"';?>>已经通过</option>
								<option value="4" <?php if($result['status'] == 4) echo 'selected = "selected"';?>>撤消</option>
							</select>
						</div>
					</div>
				</li>
				<li>
					<div class="form-group" style="padding:0 0 0 5px;">
						<label  for="benben_id" style="float:left;margin-top:7px;">撤消时间:</label>	
						<div class="col-sm-8" style="margin-bottom:10px;">
							<input type="text" class="form-control created_time" name="cancel_time1" id="cancel_time1" value="<?php echo $result['cancel_time1']?>">
						</div>
					</div>
				</li>
				<li>
					<div class="form-group" style="padding:0 0 0 5px;">
						<label  for="benben_id" style="float:left;margin-top:7px;">到:　</label>	
						<div class="col-sm-8" style="margin-bottom:10px;">
							<input type="text" class="form-control created_time" name="cancel_time2" id="cancel_time2" value="<?php echo $result['cancel_time2']?>">
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
								<?php if(isset($res)) {foreach ($res as $prv){?>
									<option value="<?php echo $prv['bid'];?>"
									<?php if($result['city'] == $prv['bid']) echo 'selected = "selected"';?>><?php echo $prv['area_name']?></option>
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
						<td width="10%">手机号码</td>
						<td width="10%">百姓网号</td>
						<td width="10%">姓名</td>
						<td width="10%">奔犇号</td>
						<td width="15%">身份证号码</td>
						<td width="15%">地区</td>
						<td width="15%">申请时间</td>
						<td width="10%">审核状态</td>						
						<td width="15%">操作</td>
					</tr>
				</thead>
				<tbody>
					<?php 
					//省市代码获取
					$pro = array();
					$pro_arr = array();
					foreach ($items as $value){
						$pro[] = $value['province'];
						$pro[] = $value['city'];
						$pro[] = $value['area'];
						$pro[] = $value['street'];
					}
					
					$pro_name = $this->allareas(implode(",", $pro));
					if($pro_name){
						foreach ($pro_name as $val){
							$pro_arr[$val['bid']] = $val['area_name'];
						}
					}
					
					$i=0;
					foreach ( $items as $item ) {
					$edit_url = Yii::app()->createUrl('bxapply/update',array('id'=>$item->id,'page'=>$_REQUEST['page']));
					?>
						<tr class="main_right_content_content_body <?php if($item->status == 4) echo 'danger';?>" >										
										<td><?php echo $item->phone ?></td>
										<td><?php echo $item->short_phone ?></td>
										<td><?php echo $item->name ?></td>
										<td><?php if( $item->id_enable == 1)echo $item->benben_id ?></td>
										<td ><?php if($item->backidcard) echo $item->backidcard; else echo $item->card ?></td>
										<td><?php echo $pro_arr[$item->province].''.$pro_arr[$item->city] ?></td>
										<td><?php echo date("Y-m-d H:i:s", $item->created_time) ?></td>
										<td><a href="<?php echo Yii::app()->createUrl('bxapplyRecord/index?id='.$item->id)?>"><?php echo $status[$item->status] ?></a></td>
										
										<td>
						<?php if($role_arr['dobaixing'] & 1){?><a class="btn btn-primary btn-sm" href="<?php echo $edit_url?>&back=<?php echo $result['goback']?>">编辑</a><?php }?>
						
					</td>
				</tr>
				<?php } ?>				</tbody>
			</table>

		</div>
	</div>
	<input id="del_baseurl" type="hidden" value="<?php echo Yii::app()->createUrl("bxapply/delete",array('page'=>$pages->currentPage +1));?>" />
	<div class="main_footer ">
		<div class="main_footer_page">
			<?php 
				$path = substr(dirname(__FILE__), 0, -7)."layouts/searchPages.php";
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

	
