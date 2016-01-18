<?php
/* @var $this MemberController */
/* @var $dataProvider CActiveDataProvider */

Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/themes/css/global.css");
$status = array('0'=>'启用', '1'=>'禁用1周', '2'=>'禁用2周', '3'=>'禁用1个月', '4'=>'禁用3个月', '5'=>'无限期');
$role_arr  = json_decode(Yii::app()->session['role_arr'],true);
?>

<div class="main_right_content">
	<div class="main_right_content_title main_titleab">
	<div class="main_right_content_title_font">用户编辑</div>
</div>

<div class="main_right_content_content" style="background:#F5F5F5;">
<?php if($msg) {?>
		<div class="alert alert-danger alert-dismissible" role="alert">
			  <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
			  <strong>警告！</strong> <?php echo $msg;?>
		</div>
		<?php }?>
		
<form action="<?php echo Yii::app()->createUrl('member/index')?>" method="get">
	<ul>
		<li>
			<div class="form-group" style="padding:0 0 0 5px;">
				<label  for="benben_id" style="float:left;margin-top:4px;">奔犇号:　</label>	
				<div class="col-sm-8" style="margin-bottom:10px;">
					<input type="text" class="form-control" name="benben_id" id="benben_id" value="<?php echo $result['benben_id']?>">
				</div>
			</div>
		</li>
		<li>
			<div class="form-group" style="padding:0 0 0 5px;">
				<label  for="phone" style="float:left;margin-top:4px;">手机:</label>	
				<div class="col-sm-8" style="margin-bottom:10px;">
					<input type="text" class="form-control" name="phone"   id="phone"  value="<?php echo $result['phone']?>">
				</div>
			</div>
		</li>
		<li  >
			<div class="form-group" style="padding:0 0 0 5px;">
				<label  for="name" style="float:left;margin-top:4px;">姓　　名:</label>	
				<div class="col-sm-8" style="margin-bottom:10px;">
					<input type="text" class="form-control" name="name"  id="name"  value="<?php echo $result['name']?>">
				</div>
			</div>
		</li>
		<li style="width: 19%">
			<div class="form-group" style="padding:0 0 0 5px;">
				<label  for="nick_name" style="float:left;margin-top:4px;">昵　　称:</label>	
				<div class="col-sm-8" style="margin-bottom:10px;">
					<input type="text" class="form-control" name="nick_name"  id="nick_name"  value="<?php echo $result['nick_name']?>">
				</div>
			</div>
		</li>
<?php $url = "benben_id={$_GET['benben_id']}&phone={$_GET['phone']}&name={$_GET['name']}&nick_name={$_GET['nick_name']}&sex={$_GET['sex']}&age1={$_GET['age1']}&age2={$_GET['age2']}&coin1={$_GET['coin1']}&coin2={$_GET['coin2']}&created_time1={$_GET['created_time1']}&created_time2={$_GET['created_time2']}&dj={$_GET['dj']}&phone_model={$_GET['phone_model']}&province={$_GET['province']}&city={$_GET['city']}&area={$_GET['area']}&status={$_GET['status']}";?>
		<?php if($role_arr['domember'] & 2){?>
		<li style="float:right;text-align:right;width:60px;padding-right:10px;">
			<a href = "/admin.php/member/phpexcel?<?php echo $url?>"><div class="btn btn-primary"  id="putexcel">导出</div></a>
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
				<label  for="sex" style="float:left;margin-top:4px;">性　　别:</label>	
				<div class="col-sm-8" style="margin-bottom:10px;">
					<select class="form-control" name="sex" id="sex">
						
						<option value="-1" >--请选择--</option>
						<option value="3" <?php if($result['sex'] == 3 && isset($result['sex'])) echo 'selected = "selected"';?>>未知</option>
						<option value="1" <?php if($result['sex'] == 1) echo 'selected = "selected"';?>>男</option>
						<option value="2" <?php if($result['sex'] == 2) echo 'selected = "selected"';?>>女</option>
					</select>
				</div>
			</div>
		</li>
		<li>
			<div class="form-group" style="padding:0 0 0 5px;">
				<label  for="" style="float:left;margin-top:4px;">等级:</label>	
				<div class="col-sm-8" style="margin-bottom:10px;">
					<select class="form-control" name="dj"  id="dj">
						<option value="-1">--请选择--</option>
						<option value="0" <?php if($result['dj']==0) echo 'selected="selected"';?>>游民</option>
						<option value="1" <?php if($result['dj']==1) echo 'selected="selected"';?>>佃户</option>
						<option value="2" <?php if($result['dj']==2) echo 'selected="selected"';?>>贫农</option>
						<option value="3" <?php if($result['dj']==3) echo 'selected="selected"';?>>中农</option>
						<option value="4" <?php if($result['dj']==4) echo 'selected="selected"';?>>富农</option>
						<option value="5" <?php if($result['dj']==5) echo 'selected="selected"';?>>地主</option>
						<option value="6" <?php if($result['dj']==6) echo 'selected="selected"';?>>县令</option>
						<option value="7" <?php if($result['dj']==7) echo 'selected="selected"';?>>知府</option>
						<option value="8" <?php if($result['dj']==8) echo 'selected="selected"';?>>巡抚</option>
						<option value="9" <?php if($result['dj']==9) echo 'selected="selected"';?>>总督</option>
						<option value="10" <?php if($result['dj']==10) echo 'selected="selected"';?>>丞相</option>
						<option value="11" <?php if($result['dj']==11) echo 'selected="selected"';?>>皇帝</option>
					</select>
				</div>
			</div>
		</li>

		<li>
			<div class="form-group" style="padding:0 0 0 5px;">
				<label  for="sex" style="float:left;margin-top:4px;">是否禁用:</label>	
				<div class="col-sm-8" style="margin-bottom:10px;">
					<select class="form-control" name="status" id="status">
						<option value="-1">--请选择--</option>
						<option value="0" <?php if($result['status']==0) echo 'selected="selected"';?>>启用</option>
						<option value="1" <?php if($result['status']==1) echo 'selected="selected"';?>>禁用1周</option>
						<option value="2" <?php if($result['status']==2) echo 'selected="selected"';?>>禁用2周</option>
						<option value="3" <?php if($result['status']==3) echo 'selected="selected"';?>>禁用1个月</option>
						<option value="4" <?php if($result['status']==4) echo 'selected="selected"';?>>禁用3个月</option>
						<option value="5" <?php if($result['status']==5) echo 'selected="selected"';?>>无限期</option>
					</select>
				</div>
			</div>
		</li>
		<li style="width: 19%">
			<div class="form-group" style="padding:0 0 0 5px;">
				<label  for="phone_model" style="float:left;margin-top:4px;">手机型号:</label>	
				<div class="col-sm-8" style="margin-bottom:10px;">
					<input type="text" class="form-control" name="phone_model"  id="phone_model"  value="<?php echo $result['phone_model']?>">
				</div>
			</div>
		</li>

	</ul>
<ul class="member_ul">
	
	
	<li>
		<div class="form-group" style="padding:0 0 0 5px;">
			<label  for="age1" style="float:left;margin-top:4px;">年　　龄:</label>	
			<div class="col-sm-8" style="margin-bottom:10px;">
				<input type="text" class="form-control" name="age1"  id="age1"  value="<?php echo $result['age1']?>" >
			</div>
		</div>
	</li>
	<li>
		<div class="form-group" style="padding:0 0 0 5px;">
			<label  for="age2" style="float:left;margin-top:4px;">到:　</label>	
				<div class="col-sm-8" style="margin-bottom:10px;">
					<input type="text" class="form-control" name="age2"  id="age2"  value="<?php echo $result['age2']?>" >
				</div>
		</div>
	</li>
	
</ul>
<ul class="member_ul">
<li>
		<div class="form-group" style="padding:0 0 0 5px;">
			<label  for="created_time1" style="float:left;margin-top:4px;">注册日期:</label>	
			<div class="col-sm-8" style="margin-bottom:10px;">
				<input type="text" class="form-control created_time" name="created_time1"  id="created_time1"  value="<?php echo $result['created_time1']?>">
			</div>
	</div>
	</li>
	<li>
		<div class="form-group" style="padding:0 0 0 5px;">
			<label  for="created_time1" style="float:left;margin-top:4px;">到:　</label>	
			<div class="col-sm-8" style="margin-bottom:10px;">
				<input type="text" class="form-control created_time" name="created_time2"  id="created_time2"   value="<?php echo $result['created_time2']?>">
			</div>
	</div>
	</li>
</ul>
<ul class="member_ul">
	<li>
		<div class="form-group" style="padding:0 0 0 5px;">
			<label  for="coin1" style="float:left;margin-top:4px;">犇币数量:</label>	
			<div class="col-sm-8" style="margin-bottom:10px;">
				<input type="text" class="form-control coin" name="coin1"  id="coin1"  value="<?php echo $result['coin1']?>">
			</div>
	</div>
	</li>
	<li>
		<div class="form-group" style="padding:0 0 0 5px;">
			<label  for="coin2" style="float:left;margin-top:4px;">到:　</label>	
			<div class="col-sm-8" style="margin-bottom:10px;">
				<input type="text" class="form-control coin" name="coin2"  id="coin2"   value="<?php echo $result['coin2']?>">
			</div>
	</div>
	</li>
</ul>
<ul class="member_ul">
	<li>
		<div class="form-group" style="padding:0 0 0 5px;">
			<label  for="sex" style="float:left;margin-top:4px;">地　　区:</label>	
			<div class="col-sm-8" style="margin-bottom:10px;">
				<select class="form-control" name="province" id="province">
					<option value="-1">--请选择省--</option>
					<?php foreach ($province as $prv){?>
						<option value="<?php echo $prv->bid?>"  <?php if($result['province'] == $prv->bid) echo 'selected = "selected"';?>><?php echo $prv->area_name?></option>
					<?php  }?>
				</select>
			</div>
		</div>
	</li>
	<li>
		<div class="form-group" style="padding:0 0 0 5px;">
			<div class="col-sm-8" style="margin:0 0 10px 28px;">
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
<div class="clear">
</div>
</form>
<table cellspacing=1 border="0" class="table table-hover" style="border-top:1px solid #ccc;">
	<thead>
		<tr class="main_right_content_content_title">
			<td width="4%">头像</td>
			<td width="5%">奔犇号</td>
			<td width="8%">手机号码</td>
			<td width="5%">姓名</td>
			<td width="9%">昵称</td>
			<td width="13%">身份证号码</td>
			<td width="4%">性别</td>
			<td width="4%">年龄</td>
			<td width="10%">地区</td>
			<td width="6%">百姓网号</td>
			<td width="4%">等级</td>
			<td width="4%">积分</td>
			<td width="4%">犇币</td>
			<td width="8%">注册时间</td>
			<td width="7%">状态</td>			
			<td width="5%">操作</td>
		</tr>
	</thead>
	<tbody>
	<?php	$i=0;
	$level = 0;
	$level_all = $this->getlevel();
	//省市代码获取
	$pro = array();
	$pro_arr = array();
	foreach ( $items as $item ) {
		$edit_url = Yii::app()->createUrl('member/update',array('id'=>$item->id,'page'=>$_REQUEST['page']));
		$log_url = Yii::app()->createUrl('member/log',array('id'=>$item->id,'page'=>$_REQUEST['page']));
		if($item->card_poster){
			$css = "style='background:#fbeffd'";
		}else{
			$css = "";
		}
		?>
		<tr <?php echo $css?>class="main_right_content_content_body">
			<td><img
				src="<?php echo $item->poster ? Yii::app()->request->baseUrl.$item->poster : Yii::app()->request->baseUrl.'/themes/images/poster.jpg';?>"
				width="32"></td>
			<td><?php echo $item->benben_id ?></td>
			<td><?php echo $item->phone ?></td>
			<td><?php echo $item->name ?></td>
			<td><?php echo $item->nick_name ?></td>
			<td><?php echo $item->id_card ?></td>
			<td><?php if($item->sex == 1){echo "男";}elseif($item->sex == 2){echo "女";}else{echo "未知";}?></td>
			<td><?php echo $this->age($item->age) ?></td>
			<td><?php echo $areaInfo[$item->province].''.$areaInfo[$item->city] ?></td>
			<td><?php $bx = Bxapply::model()->find("member_id = {$item->id}");
			                      echo $bx->short_phone; 
			?></td>
			<td><?php foreach ($level_all as $va){
									if($item->integral <= $va[1]){
										$level = $va[2];										
										break;
									}
								 }
								 echo $level;
	         ?></td>
			<td><?php echo $item->integral ?></td>
			<td><?php echo $item->coin ?></td>
			<td><?php echo date("Y-m-d",$item->created_time) ?></td>
			<td><?php echo '<a href="'.$log_url.'">'.$status[$item->status].'</a>'; ?></td>
			<!-- <td><a href="<?php echo Yii::app()->createUrl("memberDisable/index?id=".$item->id);?>"><?php echo $status[$item->status] ?></a></td>-->
			<td><?php if($role_arr['domember'] & 1){?><a class="btn btn-primary btn-sm" href="<?php echo $edit_url?>&back=<?php echo $result['goback']?>">编辑</a><?php }?>
			</td>
		</tr>
		<?php } ?>
	</tbody>
</table>

</div>
</div>
<input
	id="del_baseurl" type="hidden"
	value="<?php echo Yii::app()->createUrl("member/delete",array('page'=>$pages->currentPage +1));?>" />
<div
	class="main_footer">
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
	//$this->widget ( 'CLinkPager', array (
// 					'header' => '',
// 					'firstPageLabel' => '«首页',
// 					'lastPageLabel' => '尾页»',
// 					'prevPageLabel' => '«',
// 					'nextPageLabel' => '»',
// 					'maxButtonCount' => 6,
// 			        'itemCount'=>$count,
// 					'pages' => $pages
// 	) );
	?>
</div>
</div>
</div>
<script>
$("#putexcel1").click(function(){
	var search = $(".benben_id").val();
	var phone = $(".phone").val();
	var name = $(".name").val();
	var nick_name = $(".nick_name").val();
	var sex = $(".sex").val();
	var age1 = $(".age1").val();
	var age2 = $(".age2").val();
	var created_time1 = $(".created_time1").val();
	var created_time2 = $(".created_time2").val();
	var province = $(".province").val();
	var city = $(".city").val();
	var area = $(".area").val();
	var status = $(".status").val();

	$.get("/admin.php/member/phpexcel", { name: "", status: status });
	
});
</script>


