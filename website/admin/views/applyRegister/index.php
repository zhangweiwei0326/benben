<?php
/* @var $this ApplyRegisterController */
/* @var $dataProvider CActiveDataProvider */
Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/themes/css/global.css");
?>

		<div class="main_right_content">
		<div class="main_right_content_title main_titleab">
			<div class="main_right_content_title_font">申请审核管理</div>

		</div>
		<div class="main_right_content_content" style="background:#F5F5F5;">
			<?php if($result['msg']) {?>
			<div class="alert alert-danger alert-dismissible" role="alert">
			  <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
			  <strong>警告！</strong> <?php echo $result['msg'];?>
			</div>
			<?php }?>
			<form action="<?php echo Yii::app()->createUrl('applyRegister/index')?>" method="get">
			<ul style="margin-top:0;">
					<li>
					<div class="form-group" style="padding:0 0 0 5px;">
						<label  for="benben_id" style="float:left;margin-top:7px;">申请名称:</label>	
						<div class="col-sm-8" style="margin-bottom:10px;">
							<input type="text" class="form-control" name="apply_name" id="apply_name" value="<?php echo $result['apply_name']?>">
						</div>
					</div>
				</li>
				<li>
					<div class="form-group" style="padding:0 0 0 5px;">
						<label  for="benben_id" style="float:left;margin-top:7px;">政企名称:</label>	
						<div class="col-sm-8" style="margin-bottom:10px;">
							<input type="text" class="form-control" name="enterprise_name" id="enterprise_name" value="<?php echo $result['enterprise_name']?>">
						</div>
					</div>
				</li>
				<li>
						<div class="form-group" style="padding:0 0 0 5px;">
							<label  for="sex" style="float:left;margin-top:7px;">申请状态:</label>	
							<div class="col-sm-8" style="margin-bottom:10px;">
								<select class="form-control" name="status" id="status">
									<option value="-1" >--请选择--</option>
									<option value="1" <?php if($result['status'] ==1) echo 'selected = "selected"';?>>待审核</option>
									<option value="2" <?php if($result['status'] == 2) echo 'selected = "selected"';?>>同意</option>
									<option value="3" <?php if($result['status'] == 3) echo 'selected = "selected"';?>>拒绝</option>
								</select>
							</div>
						</div>
				</li>
				<li>
						<div class="form-group" style="padding:0 0 0 5px;">
							<label  for="benben_id" style="float:left;margin-top:7px;">审核人:</label>	
							<div class="col-sm-8" style="margin-bottom:10px;">
								<input type="text" class="form-control" name="review_name" id="review_name" value="<?php echo $result['review_name']?>">
							</div>
						</div>
					</li>
				<li style="float:right;text-align:right;width:70px;padding-right:16px;">
					<span type="submit" class="btn btn-primary" id="search_more">更多</span>
				</li>
				
				<li style="float:right;text-align:right;width:60px;padding-right:10px;">
					<span type="submit" class="btn btn-primary" id="search_clear">清空</span>
				</li>
				<li style="float:right;text-align:right;width:70px;padding-right:12px;">
					<input type="hidden" value="-2" name="backurl">
					<input type="submit" class="btn btn-primary" value="查询" id="submit_search">
				</li>
				</ul>
				<ul class="member_ul">
						<li>
						<div class="form-group" style="padding:0 0 0 5px;">
							<label  for="created_time1" style="float:left;margin-top:7px;">申请时间:</label>	
							<div class="col-sm-8" style="margin-bottom:10px;">
								<input type="text" class="form-control created_time" name="created_time1"  id="created_time1"  value="<?php echo $result['created_time1']?>">
							</div>
					</div>
					</li>
					<li >
						<div class="form-group" style="padding:0 0 0 5px;">
							<label  for="created_time1" style="float:left;margin-top:7px;">到:　　　</label>	
							<div class="col-sm-8" style="margin-bottom:10px;">
								<input type="text" class="form-control created_time" name="created_time2"  id="created_time2"   value="<?php echo $result['created_time2']?>">
							</div>
					</div>
					</li>
						<li>
						<div class="form-group" style="padding:0 0 0 5px;">
							<label  for="created_time1" style="float:left;margin-top:7px;">审核时间:</label>	
							<div class="col-sm-8" style="margin-bottom:10px;">
								<input type="text" class="form-control created_time" name="review_time1"  id="review_time1"  value="<?php echo $result['review_time1']?>">
							</div>
					</div>
					</li>
					<li >
						<div class="form-group" style="padding:0 0 0 5px;">
							<label  for="created_time1" style="float:left;margin-top:7px;">到:　　　</label>	
							<div class="col-sm-8" style="margin-bottom:10px;">
								<input type="text" class="form-control created_time" name="review_time2"  id="review_time2"   value="<?php echo $result['review_time2']?>">
							</div>
					</div>
					</li>
			</ul>		
			
				<ul class="member_ul">
					<li>
						<div class="form-group" style="padding:0 0 0 5px;">
							<label  for="sex" style="float:left;margin-top:7px;">申请类型:</label>	
							<div class="col-sm-8" style="margin-bottom:10px;">
								<select class="form-control" name="apply_type" id="apply_type">
									<option value="-1" >--请选择--</option>
									<option value="1"  <?php if($result["apply_type"] == 1) echo 'selected = "selected"';?>>个人</option>
									<option value="2"  <?php if($result["apply_type"] == 2) echo 'selected = "selected"';?>>企业/组织</option>
									<option value="3"  <?php if($result["apply_type"] == 3) echo 'selected = "selected"';?>>学校</option>
								</select>
							</div>
						</div>
					</li>
					<li>
						<div class="form-group" style="padding:0 0 0 5px;">
							<label  for="sex" style="float:left;margin-top:7px;">政企类型:</label>	
							<div class="col-sm-8" style="margin-bottom:10px;">
								<select class="form-control" name="enterprise_type" id="enterprise_type">
									<option value="-1" >--请选择--</option>
									<option value="1"  <?php if($result["enterprise_type"] == 1) echo 'selected = "selected"';?>>企业政企</option>
									<option value="2"  <?php if($result["enterprise_type"] == 2) echo 'selected = "selected"';?>>虚拟网政企</option>
								</select>
							</div>
						</div>
					</li>
				</ul>
			</form>
		
			<table cellspacing=1 border="0" class="table table-hover">
				<thead>
					<tr class="main_right_content_content_title">
						<td width="13%">申请名称</td><td width="14%">政企通讯录名称</td><td width="10%">政企通讯录类型</td><td width="8%">申请类型</td><td width="7%">审核状态</td><td width="10%">审核人</td><td width="14%">审核时间</td><td width="14%">申请时间</td><td width="10%">操作</td>
					</tr>
				</thead>
				<tbody>
					<?php					$i=0;
					foreach ( $items as $item ) {
						if($item->status!=1&&$item->status!=2){
								$readOnly=0;}else{
									$readOnly=1;
								}
					$edit_url = Yii::app()->createUrl('applyRegister/review',array('id'=>$item->id,'page'=>$_REQUEST['page'],'readOnly'=>$readOnly));
					?>
						<tr class="main_right_content_content_body <?php if($item->status==0){echo "danger";}?>">
											<td><?php echo $item->name ?></td>
										<td><?php echo $item->enterprise_name ?></td>
										<td><?php if($item->enterprise_type==1){echo "企业政企" ;}elseif($item->enterprise_type==2){echo "虚拟网政企" ;}else{ echo "";}?></td>
										<td><?php if($item->apply_type==1){echo "个人" ;}elseif($item->apply_type==2){echo "企业/组织" ;}elseif($item->apply_type==3){ echo "学校";}else{echo "";} ?></td>
										<td><?php if($item->status==1){echo "同意" ;}elseif($item->status==2){echo "拒绝" ;}else{ echo "待审核";} ?></td>
										<td><?php if($item->status==0){echo "待审核";}else{echo $item->review_name;}?></td>
										<td><?php echo  $item->review_time?date('Y-m-d H:i',$item->review_time):"待审核";?></td>
										<td><?php echo date('Y-m-d H:i',$item->created_time) ?></td>
										<td>
										<?php if($item->status!=1&&$item->status!=2){?>
													<a class="btn btn-primary btn-sm" href="<?php echo $edit_url?>">审核</a>
										<?php }else{?>
													<a class="btn btn-success btn-sm" href="<?php echo $edit_url?>">查看</a>
										<?php }?>
					</td>
				</tr>
				<?php } ?>				</tbody>
			</table>

		</div>
	</div>
	
<input id="del_baseurl" type="hidden" value="<?php echo Yii::app()->createUrl("applyRegister/delete",array('page'=>$pages->currentPage +1));?>" />
	<div class="main_footer">
		<div class="main_footer_page">
			<?php 
				$path = substr(dirname(__FILE__), 0, -13)."layouts/searchPages.php";
				require_once($path);  
			?>
			<?php		
			$page = intval($_GET['page']) ?  intval($_GET['page']) : 1;
			$url = explode("?", Yii::app()->request->getUrl());
	$link = Yii::app()->request->hostInfo.$url[0]."?";
			echo '<ul class="yiiPager" id="yw0">'.$this->textPage($pages->pageCount , $page, $link).'</ul>';
			?>
		</div>
	</div>
</div>