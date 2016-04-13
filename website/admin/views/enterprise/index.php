<?php
/* @var $this EnterpriseController */
/* @var $dataProvider CActiveDataProvider */
Yii::app ()->clientScript->registerCssFile ( Yii::app ()->request->baseUrl . "/themes/css/global.css" );
$path = substr(dirname(__FILE__), 0, -10)."layouts/area.php";
require_once($path);
$role_arr  = json_decode(Yii::app()->session['role_arr'],true);
$type = array (
		"1" => "企业通讯录",
		"2" => "虚拟通讯录" 
);
$status = array (
		"0" => "启用",
		"1" => "屏蔽",
		/*"2" => "禁用2周",
		"3" => "禁用1个月",
		"4" => "禁用3个月",
		"5" => "无限期"*/ 
);
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
		<div class="main_right_content_title_font">政企通讯录管理</div>
	</div>
	<div class="main_right_content_content" style="background: #F5F5F5;">
		<form action="<?php echo Yii::app()->createUrl('enterprise/index')?>"
			method="get">
			<ul style="margin-top: 0;">
				<li style="width: 25%;">
					<div class="form-group" style="padding: 0 0 0 5px;">
						<label for="benben_id" style="float: left; margin-top: 7px;">通讯录名称:</label>
						<div class="col-sm-7" style="margin-bottom: 10px;">
							<input type="text" class="form-control" name="name" id="name"
								value="<?php echo $result['name']?>">
						</div>
					</div>
				</li>
				<li style="width: 22%;">
					<div class="form-group" style="padding: 0 0 0 5px;">
						<label for="type" style="float: left; margin-top: 7px;">通讯录类型:　　</label>
						<div class="col-sm-7" style="margin-bottom: 10px;">
							<select class="form-control" name="type" id="type">

								<option value="-1">--请选择--</option>
								<option value="1"
									<?php if($result['type'] == 1) echo 'selected = "selected"';?>>企业通讯录</option>
								<option value="2"
									<?php if($result['type'] == 2) echo 'selected = "selected"';?>>虚拟通讯录</option>
							</select>
						</div>
					</div>
				</li>
				<li>
					<div class="form-group" style="padding: 0 0 0 5px;">
						<label for="sex" style="float: left; margin-top: 7px;">状态:　　</label>
						<div class="col-sm-8" style="margin-bottom: 10px;">
							<select class="form-control" name="status" id="status">
								<option value="-1">--请选择--</option>
								<?php foreach ($status as $key=>$value){?>
								<option <?php if($result['status'] == $key && isset($result['status'])) echo 'selected = "selected"';?> value="<?php echo $key?>"><?php echo $value?></option>
								<?php }?>								
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
if(!isset($_GET['street'])){
	$_GET['street'] = -1;
}
$url = "type={$_GET['type']}&member_name={$_GET['member_name']}&member_phone={$_GET['member_phone']}&member_status={$_GET['member_status']}&name={$_GET['name']}&created_time1={$_GET['created_time1']}&created_time2={$_GET['created_time2']}&province={$_GET['province']}&city={$_GET['city']}&area={$_GET['area']}&status={$_GET['status']}&number1={$_GET['number1']}&number2={$_GET['number2']}";?>
	<?php if($role_arr['doenterprise'] & 4){?>
	<li style="float:right;text-align:right;width:70px;padding-right:20px;">
		<a href = "/admin.php/enterprise/create"><div class="btn btn-primary" >创建</div></a>
	</li>
	<?php } if($role_arr['doenterprise'] & 2){?>
	<li style="float:right;text-align:right;width:70px;padding-right:20px;">
		<a href = "/admin.php/enterprise/phpexcel?<?php echo $url?>"><div class="btn btn-primary"  id="putexcel">导出</div></a>
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
				<ul class="member_ul">
				<li style="width:25%">
					<div class="form-group" style="padding: 0 0 0 5px;">
						<label for="benben_id" style="float: left; margin-top: 7px;">创　建　人:</label>
						<div class="col-sm-7" style="margin-bottom: 10px;">
							<input type="text" class="form-control" name="member_name"
								id="member_name" value="<?php echo $result['member_name']?>">
						</div>
					</div>
				</li>
				<li style="width:26%">
					<div class="form-group" style="padding: 0 0 0 5px;">
						<label for="benben_id" style="float: left; margin-top: 7px;">创建人手机号码:</label>
						<div class="col-sm-7" style="margin-bottom: 10px;">
							<input type="text" class="form-control" name="member_phone"
								id="member_phone" value="<?php echo $result['member_phone']?>">
						</div>
					</div>
				</li>
				<li style="width:26%">
					<div class="form-group" style="padding: 0 0 0 5px;">
						<label for="benben_id" style="float: left; margin-top: 7px;">创建人状态:</label>
						<div class="col-sm-7" style="margin-bottom: 10px;">
						<select class="form-control" name="member_status" id="member_status">
						<option value="-1">--请选择--</option>
							<?php foreach ($status1 as $key=>$val){?>
							<option value="<?php echo $key?>" <?php if($result['member_status']==$key && isset($result['member_status'])) echo  'selected = "selected"'?>><?php echo $val?></option>
							<?php }?>
						</select>
						</div>
					</div>
				</li>
				
				</ul>
				<ul class="member_ul">
				<li>
					<div class="form-group" style="padding: 0 0 0 5px;">
						<label for="benben_id" style="float: left; margin-top: 7px;">创建时间:</label>
						<div class="col-sm-8" style="margin-bottom: 10px;">
							<input type="text" class="form-control created_time"
								name="created_time1" id="created_time1"
								value="<?php echo $result['created_time1']?>">
						</div>
					</div>
				</li>
				<li>
					<div class="form-group" style="padding: 0 0 0 5px;">
						<label for="benben_id" style="float: left; margin-top: 7px;">到: </label>
						<div class="col-sm-8" style="margin-bottom: 10px;">
							<input type="text" class="form-control created_time"
								name="created_time2" id="created_time2"
								value="<?php echo $result['created_time2']?>">
						</div>
					</div>
				</li>
				<li>
					<div class="form-group" style="padding: 0 0 0 5px;">
						<label for="benben_id" style="float: left; margin-top: 7px;">加入人数:</label>
						<div class="col-sm-8" style="margin-bottom: 10px;">
							<input type="text" class="form-control"
								name="number1" id="number1"
								value="<?php echo $result['number1']?>">
						</div>
					</div>
				</li>
				<li>
					<div class="form-group" style="padding: 0 0 0 5px;">
						<label for="benben_id" style="float: left; margin-top: 7px;">到: </label>
						<div class="col-sm-8" style="margin-bottom: 10px;">
							<input type="text" class="form-control"
								name="number2" id="number2"
								value="<?php echo $result['number2']?>">
						</div>
					</div>
				</li>
				</ul>
                <ul class="member_ul">
				<li style="width: 22%;">
					<div class="form-group" style="padding: 0 0 0 5px;">
						<label for="sex" style="float: left; margin-top: 7px;">地区:　　　</label>
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
				<li style="width:15%">
					<div class="form-group" style="padding: 0 0 0 5px;">
						<div class="col-sm-12" style="margin-bottom: 10px;">
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
				<li style="width:11%;margin-right:3%;">
					<div class="form-group" style="padding: 0 0 0 5px;">
						<div class="" style="margin-bottom: 10px;">
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
		<table cellspacing=1 border="0" class="table table-hover" style="table-layout: fixed">
			<thead>
				<tr class="main_right_content_content_title">
					<td width="10%">通讯录名称</td>
					<td width="10%">类型</td>
					<td width="15%">地区</td>
					<td width="8%">创建人</td>
					<td width="10%">创建人状态</td>
					<td width="6%">加入人数</td>
					<td width="8%">奔犇成员数量</td>
					<td width="6%">是否禁用</td>
					<td width="15%">创建时间</td>
					<td width="12%">操作</td>
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
                    $i = 0;
					foreach ( $items as $item ) {
						$edit_url = Yii::app ()->createUrl ( 'enterprise/update', array (
								'id' => $item->id,
								'page' => $_REQUEST ['page'] 
						) );
						?>
						<tr class="main_right_content_content_body">
					<td><?php echo $item->name ?></td>
					<td><?php echo $type[$item->type] ?></td>
					<td><?php echo $pro_arr[$item->province].''.$pro_arr[$item->city] ?></td>
					<td><?php echo ($item->origin == 1) ? ($item->mname?$item->mname:$item->nick_name) : "admin(后台)"; ?></td>
					<td><?php if($item->origin == 1){echo ($item->enterprise_disable == null) ? "创建人已退出":'<a title="查看禁用记录" style="text-decoration: underline" href="'.Yii::app()->createUrl('enterpriseDisable/service?id='.$item->member_id).'">'.$status1[$item->enterprise_disable].'</a>'; }else{echo "启用";}?></td>
					<td><a title="查看成员" style="text-decoration: underline"
						; href="<?php echo Yii::app()->createUrl('enterpriseMember/index?id='.$item->id)?>"><?php echo $item->number ?></a></td>
					<td><?php echo isset($bid[$item->id]) ? $bid[$item->id] : 0;?></td>	
					<td><a title="查看禁用记录" style="text-decoration: underline"
						href="<?php echo Yii::app()->createUrl('enterpriseDisable/index?id='.$item->id)?>"><?php echo $status[$item->status] ?></a></td>
					<td><?php echo date('Y-m-d H:i:s', $item->created_time) ?></td>
					<td><a class="btn btn-danger btn-sm"
						href="<?php echo Yii::app()->createUrl('enterpriseMember/index?id='.$item->id)?>">成员</a>
						<?php if($role_arr['doenterprise'] & 1){?><a class="btn btn-primary btn-sm"
						href="<?php echo $edit_url?>&back=<?php echo $result['goback']?>">编辑</a><?php }?>
					</td>
				</tr>
				<?php } ?>				</tbody>
		</table>

	</div>
</div>

<input id="del_baseurl" type="hidden"
	value="<?php echo Yii::app()->createUrl("enterprise/delete",array('page'=>$pages->currentPage +1));?>" />
<div
	class="main_footer">
	<div class="main_footer_page">
			<?php
			$path = substr ( dirname ( __FILE__ ), 0, - 10 ) . "layouts/searchPages.php";
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

