<?php
/* @var $this GroupsController */
/* @var $dataProvider CActiveDataProvider */
Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/themes/css/global.css");
$status = array('0'=>'启用', '1'=>'屏蔽', '2'=>'解散'/*, '3'=>'禁用1个月', '4'=>'禁用3个月', '5'=>'无限期'*/);
$status1 = array (
		"0" => "启用",
		"1" => "禁用1周",
		"2" => "禁用2周",
		"3" => "禁用1个月",
		"4" => "禁用3个月",
		"5" => "无限期"
)
?>

		<div class="main_right_content">
		<div class="main_right_content_title main_titleab">
			<div class="main_right_content_title_font">群组管理</div>

		</div>
		<div class="main_right_content_content" style="background:#F5F5F5;">
				<?php if($result['msg']) {?>
			<div class="alert alert-danger alert-dismissible" role="alert">
			  <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
			  <strong>警告！</strong> <?php echo $result['msg'];?>
			</div>
			<?php }?>
			<form action="<?php echo Yii::app()->createUrl('groups/index')?>" method="get">
			<ul style="margin-top:0;">
			<li>
					<div class="form-group" style="padding:0 0 0 5px;">
						<label  for="benben_id" style="float:left;margin-top:7px;">群组号:　</label>	
						<div class="col-sm-8" style="margin-bottom:10px;">
							<input type="text" class="form-control" name="id" id="id" value="<?php echo $result['id']?>">
						</div>
					</div>
				</li>
				<li>
					<div class="form-group" style="padding:0 0 0 5px;">
						<label  for="benben_id" style="float:left;margin-top:7px;">群组名称:</label>	
						<div class="col-sm-8" style="margin-bottom:10px;">
							<input type="text" class="form-control" name="name" id="name" value="<?php echo $result['name']?>">
						</div>
					</div>
				</li>
				<li>
					<div class="form-group" style="padding:0 0 0 5px;">
						<label  for="benben_id" style="float:left;margin-top:7px;">创建人:</label>	
						<div class="col-sm-8" style="margin-bottom:10px;">
							<input type="text" class="form-control" name="member_id" id="member_id" value="<?php echo $result['member_name']?>">
						</div>
					</div>
				</li>
				<li>
					<div class="form-group" style="padding:0 0 0 5px;">
						<label  for="sex" style="float:left;margin-top:7px;">状态:</label>	
						<div class="col-sm-8" style="margin-bottom:10px;">
							<select class="form-control" name="status" id="status">
								<option value="-1" >--请选择--</option>
								<option value="6" <?php if($result['status'] == 6 && isset($result['status'])) echo 'selected = "selected"';?>>启用</option>
								<option value="1" <?php if($result['status'] == 1) echo 'selected = "selected"';?>>屏蔽</option>
								<option value="2" <?php if($result['status'] == 2) echo 'selected = "selected"';?>>解散</option>
								<!-- <option value="3" <?php if($result['status'] == 3) echo 'selected = "selected"';?>>禁用1个月</option>
								<option value="4" <?php if($result['status'] == 4) echo 'selected = "selected"';?>>禁用3个月</option>
								<option value="5" <?php if($result['status'] == 5) echo 'selected = "selected"';?>>无限期</option> -->
							</select>
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
							<label  for="created_time1" style="float:left;margin-top:7px;">创建时间:</label>	
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
						<label  for="sex" style="float:left;margin-top:7px;">地　区:</label>	
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
						<div class="col-sm-8" style="margin-bottom:10px;margin-left:28px;">
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
					<div class="col-sm-8" style="margin-bottom:10px;">
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
			<ul class="member_ul">
					<li>
						<div class="form-group" style="padding:0 0 0 5px;">
							<label  for="created_time1" style="float:left;margin-top:7px;">加入人数:</label>	
							<div class="col-sm-8" style="margin-bottom:10px;">
								<input type="text" class="form-control" name="number1"  id="number1"  value="<?php echo $result['number1']?>">
							</div>
					</div>
					</li>
					<li >
						<div class="form-group" style="padding:0 0 0 5px;">
							<label  for="created_time1" style="float:left;margin-top:7px;">到:　　　</label>	
							<div class="col-sm-8" style="margin-bottom:10px;">
								<input type="text" class="form-control " name="number2"  id="number2"   value="<?php echo $result['number2']?>">
							</div>
					</div>
					</li>
				</ul>
			
			</form>
			<table cellspacing=1 border="0" class="table table-hover">
				<thead>
					<tr class="main_right_content_content_title">
						<td width="5%">头像</td>
						<td width="10%">群组名称</td>
						<td width="8%">群组号</td>
						<td width="18%">地区</td>
						<td width="8%">加入人数</td>
						<td width="8%">状态</td>
						<td width="8%">创建人</td>
						<td width="10%">创建人状态</td>
						<td width="15%">创建日期</td>						
						<td width="10%">操作</td>
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
					$edit_url = Yii::app()->createUrl('groups/update',array('id'=>$item->id,'page'=>$_REQUEST['page']));
					?>
						<tr class="main_right_content_content_body <?php if($item->is_delete) echo 'danger';?>">
										<td><img src="<?php echo $item->poster ?  Yii::app()->request->hostInfo.$item->poster 
																		:  Yii::app()->request->hostInfo.'/themes/images/poster.jpg';?>" height="32"></td>
										<td><?php echo $item->name ?></td>
										<td><?php echo $item->show_id ?></td>
										<td><?php echo $pro_arr[$item->province].''.$pro_arr[$item->city] ?></td>									
										<td><a title="查看成员" href="<?php echo Yii::app()->createUrl('groupMember/index?id='.$item->id)?>" style="text-decoration:underline"><?php echo $item->number ?></a></td>
										<td><?php if($item->is_delete){echo "解散";}else{?><a title="查看禁用记录" href="<?php echo Yii::app()->createUrl('groupDisable/index?id='.$item->id) ?>" style="text-decoration:underline"><?php echo $status[$item->status] ?></a><?php }?></td>										
										<td><?php echo $item->mname?$item->mname:$item->nick_name; ?></td>
										<td><a title="查看禁用记录" href="<?php echo Yii::app()->createUrl('groupDisable/service?id='.$item->member_id) ?>" style="text-decoration:underline"><?php echo $status1[$item->group_disable] ?></a></td>
										<td><?php echo date('Y-m-d H:i:s', $item->created_time) ?></td>
										<td>
							<a class="btn btn-danger btn-sm" href="<?php echo Yii::app()->createUrl('groupMember/index?id='.$item->id)?>">成员</a>				
						<a class="btn btn-primary btn-sm" href="<?php echo $edit_url?>">编辑</a>
					</td>
				</tr>
				<?php } ?>				</tbody>
			</table>

		</div>
	</div>
	
	<input id="del_baseurl" type="hidden" value="<?php echo Yii::app()->createUrl("groups/delete",array('page'=>$pages->currentPage +1));?>" />
	<div class="main_footer">
		<div class="main_footer_page">
			<?php 
				$path = substr(dirname(__FILE__), 0, -6)."layouts/searchPages.php";
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
	
