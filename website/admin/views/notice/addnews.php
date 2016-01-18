<?php
Yii::app ()->clientScript->registerCssFile ( Yii::app ()->request->baseUrl . "/themes/css/content.css" );
$path = dirname(__FILE__)."/../layouts/area.php";
//require_once($path);
?>
<?php
/* @var $this NoticeController */
/* @var $model News */
/* @var $form CActiveForm */
?>

<div class="main_right_content">
	<ol class="breadcrumb main_right_content_breadcrumb">
		<li><a href="<?php echo Yii::app()->createUrl('notice')?>">消息管理</a></li>
		<li><a href="#">定向通知</a></li>
	</ol>

	<div class="main_right_content_content">
	<?php if($msg) {?>
				<div class="alert alert-danger alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert">
				<span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
			</button>
			<strong>警告！</strong> <?php echo $msg;?>
				</div>
				<?php }?>
				<form method="post" action="/admin.php/notice/push"
			id="news-form" class="form-horizontal" enctype="multipart/form-data">

			<div class="form-group">
				<div class="col-sm-8" style="width: 192px; text-align: right;">
					<label for="Member_province">地区</label>
				</div>
				<div class="col-sm-8" style="width: 156px">
					<select style="width: 102px; display: inline" class="form-control"
						name="province" id="province">
						<option value="-1">--请选择--</option>
								<?php foreach ($province as $prv){?>
									<option value="<?php echo $prv->bid?>"
							<?php if($result['province'] == $prv->bid) echo 'selected = "selected"';?>><?php echo $prv->area_name?></option>
								<?php  }?>
							</select> <label style="display: inline" for="Member_province">省</label>
				</div>
				<div class="col-sm-8" style="width: 156px">
					<select style="width: 102px; display: inline" class="form-control" name="city" id="city">
						<option value="-1">--请选择--</option>
						<?php if(isset($res)) {foreach ($res as $prv){?>
									<option value="<?php echo $prv['bid'];?>"
									<?php if($result['city'] == $prv['bid']) echo 'selected = "selected"';?>><?php echo $prv['area_name']?></option>
								<?php  }}?>
					</select> <label style="display: inline" for="Member_province">市</label>
				</div>
				<div class="col-sm-8" style="width: 170px">
					<select style="width: 102px; display: inline" class="form-control" name="area" id="area">
						<option value="-1">--请选择--</option>
						<?php if(isset($res2)) {foreach ($res2 as $prv){?>
									<option value="<?php echo $prv['bid'];?>"
									<?php if($result['area'] == $prv['bid']) echo 'selected = "selected"';?>><?php echo $prv['area_name']?></option>
								<?php  }}?>
					</select> <label style="display: inline" for="Member_province">区县</label>
				</div>
				<div class="col-sm-8" style="width: 170px">
					<select style="width: 102px; display: inline" class="form-control" name="street" id="street">
						<option value="-1">--请选择--</option>
						<?php if(isset($res3)) {foreach ($res3 as $prv){?>
									<option value="<?php echo $prv['bid'];?>"
									<?php if($result['street'] == $prv['bid']) echo 'selected = "selected"';?>><?php echo $prv['area_name']?></option>
								<?php  }}?>
					</select> <label style="display: inline" for="Member_province">街道</label>
				</div>

			</div>
			<div class="form-group">
				<label for="News_创建时间" class="col-sm-2 control-label">性别</label>
				<div class="col-sm-8">
					<select style="width: 102px; display: inline" class="form-control" name="sex" id="sex">
						<option value="-1">--请选择--</option>
						<option value="1">男</option>
						<option value="2">女</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-8" style="width: 192px; text-align: right;">
					<label for="Member_province">年龄段</label>
				</div>
				<div class="col-sm-8" style="width: 156px">
					<input style="width: 102px; display: inline" name="age1" id="age1" type="text" class="form-control "/>												
					<label style="display: inline" for="Member_province">到</label>
				</div>
				<div class="col-sm-8" style="width: 156px;padding-left:0">
					<input style="width: 102px; display: inline" name="age2" id="age2" type="text" class="form-control"/>		
				</div>
				</div>
				<div class="form-group">
				<label for="News_创建时间" class="col-sm-2 control-label">手机号码</label>
				<div class="col-sm-8">
					<input type="text" class="form-control"
						name="phone" id="phone"
						value="<?php echo $result['phone']?>">(多个号码请用逗号隔开)
				</div>
			</div>
			<div class="form-group">
				<label for="News_创建时间" class="col-sm-2 control-label">号码直通车</label>
				<div class="col-sm-8">
					<select style="width: 102px; display: inline" class="form-control" name="is_store" id="is_store">
						<option value="-1">--请选择--</option>
						<option value="1">是</option>
						<option value="2">否</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label for="News_创建时间" class="col-sm-2 control-label">百姓网用户</label>
				<div class="col-sm-8">
					<select style="width: 102px; display: inline" class="form-control" name="is_baixing" id="is_baixing">
						<option value="-1">--请选择--</option>
						<option value="1">是</option>
						<option value="2">否</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label for="News_创建时间" class="col-sm-2 control-label">手机型号</label>
				<div class="col-sm-8">
					<input type="text" class="form-control"
						name="phone_model" id="phone_model"
						value="<?php echo $result['phone_model']?>">
				</div>
			</div>
			<div class="form-group">
				<label for="News_创建时间" class="col-sm-2 control-label">推送人数</label>
				<div class="col-sm-8">
					<input type="text" class="form-control"
						name="phone_num" id="phone_num"
						value="<?php echo $result['created_time2']?>"><div class="getnum">查看推送人数</div>
				</div>
			</div>
			<div class="form-group">
				<label for="News_创建时间" class="col-sm-2 control-label">推送单位</label>
				<div class="col-sm-8">
					<input type="text" class="form-control"
						name="unit" id="unit"
						value="<?php echo $result['unit']?>">
				</div>
			</div>
			<div class="form-group">
				<label for="News_内容" class="col-sm-2 control-label">推送内容</label>
				<div class="col-sm-8">
					<textarea id="News_content" name="content" cols="50" rows="6"
						class="form-control"></textarea>
				</div>
			</div>

			<div class="form-group form-group-center">
				<button type="" class="btn btn-success btn-lg btn-lg1">&#12288;确定&#12288;</button>
				<a href="/admin.php/notice/pushindex" type=""
					class="btn btn-default btn-lg">&#12288;取消&#12288;</a>
			</div>

		</form>
	</div>

</div>
<script>
 var input0 = $("#News_content").val();
 $(".btn-lg1").click(function(){
	     var input = $("#News_content").val();			 
		 if(input == ""){
		      alert("请输入消息内容");	
		      return false;
	     }else{
		     if(input0 != input){
		    	 if(window.confirm('请确认对以上消息进行发送')){
		    		 $("#news-form").submit();
		    		return true;
		    		}else{
		    		return false;
		    		} 
			 }	    	
		     $("#news-form").submit();
              return true;
		     }
	 });

 $(".getnum").click(function(){
	 var province = $('#province').val();
	 var city = $('#city').val();
	 var area = $('#area').val();
	 var street = $('#street').val();
	 var sex = $('#sex').val();
	 var age1 = $('#age1').val();	
	 var age2 = $('#age2').val();
	 var phone = $('#phone').val();
	 var is_store = $('#is_store').val();
	 var is_baixing = $('#is_baixing').val();
	 var phone_model = $('#phone_model').val();
	 $.post("/admin.php/notice/push",{getnum:1,province:province,city:city,area:area,street:street
		 ,sex:sex,age1:age1,age2:age2,phone:phone,is_store:is_store,is_baixing:is_baixing,phone_model:phone_model},function(result){
			$("#phone_num").val(result);
		 });
	 });
 
 
</script>
