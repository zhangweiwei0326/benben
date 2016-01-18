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
		<li><a href="<?php echo Yii::app()->createUrl('buy')?>">我要买管理</a></li>
		<li><a href="#">推送管理</a></li>
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
				<form method="post" action="/admin.php/buy/pushbuy"
			id="news-form" class="form-horizontal" enctype="multipart/form-data">

			<div class="form-group">
				<div class="col-sm-8" style="width: 192px; text-align: right;">
					<label for="Member_province" class="control-label">行业</label>
				</div>
				<div class="col-sm-8" style="width: 156px">
					<select style="width: 102px; display: inline" class="form-control"
						name="industry" id="industry">
						<option value="-1">--请选择--</option>
								<?php foreach ($industry as $prv){?>
									<option value="<?php echo $prv->id?>"
							<?php if($result['industry'] == $prv->id) echo 'selected = "selected"';?>><?php echo $prv->name?></option>
								<?php  }?>
							</select>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-8" style="width: 192px; text-align: right;">
					<label for="Member_province" class="control-label">地区</label>
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
				
			</div>
																			
			<div class="form-group">
				<div class="col-sm-8" style="width: 192px; text-align: right;">
					<label for="News_创建时间" class="control-label">推送人数</label>
				</div>
				
				<div class="col-sm-8">
					<input type="text" class="form-control"
						name="phone_num" id="phone_num"
						value="<?php echo $result['phone_num']?>"><div class="getnum">查看推送人数</div>
				</div>
				<input type="hidden" name="buyid" value="<?php echo intval($_GET['id'])?>">
			</div>			

			<div class="form-group form-group-center">
				<button type="" class="btn btn-success btn-lg btn-lg1">&#12288;确定&#12288;</button>
				<a href="javascript:goBack();" type=""
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
	 var industry = $('#industry').val();
	 
	 $.post("/admin.php/buy/pushbuy",{getnum:1,province:province,city:city,area:area,industry:industry
		 },function(result){
			$("#phone_num").val(result);
		 });
	 });
 
 
</script>
