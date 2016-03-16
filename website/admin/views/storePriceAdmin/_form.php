<?php
	Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/themes/css/content.css");
?>
<?php
/* @var $this StorePriceAdminController */
/* @var $model StorePriceAdmin */
/* @var $form CActiveForm */
$arr_type=array(11=>"会员号",0=>"促销",1=>"团购",10=>"我要开分店",12=>"小喇叭",13=>"大喇叭",14=>"好友联盟",15=>"政企通讯录");
?>




<div class="main_right_content">
	<ol class="breadcrumb main_right_content_breadcrumb">
		<li><a href="#">服务管理</a></li>
		<li><a href="#">服务详细</a></li>
	</ol>

	<div class="main_right_content_content">
		<?php if($msg) {?>
				<div class="alert alert-danger alert-dismissible" role="alert">
				  <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				  <strong>警告！</strong> <?php echo $msg;?>
				</div>
				<?php }?>
		<?php $form=$this->beginWidget('CActiveForm', array(
			'id'=>'store-price-admin-form',
			'htmlOptions'=>array('enctype'=>'multipart/form-data', 'class'=>'form-horizontal'),
			'enableAjaxValidation'=>false,
		)); ?>

				<div class="form-group">
					<?php echo $form->labelEx($model,'type', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->dropDownList($model,'type', $arr_type,array('class'=>'form-control')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'title', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'title',array('class'=>'form-control','size'=>'60','maxlength'=>'255')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'poster', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php if($model->poster){?>
							<img class="thumb" src="<?php echo $model->poster?>" style="width:26px;height:36px;float:left;margin-right:20px">
						<?php }else{?>
							<img class="thumb" src="" style="display:none;width:26px;height:36px;float:left;margin-right:20px">
						<?php }?>
						<?php echo $form->textField($model,'poster',array('class'=>'form-control','size'=>'60','maxlength'=>'255','style'=>'width:78%;float:left;margin-right:20px;')); ?>

						<a class="btn btn-success upload"  href="javascript:void(0)" style="float:left;">上传图片</a>
						<input type="file" id="upload_input" style="visibility:hidden;">
						<span>支持png,jpg格式,宽高为:46*46</span>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'names', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'names',array('class'=>'form-control','size'=>'60','maxlength'=>'255')); ?>
						<?php if($model->type == 11){echo "格式为 套餐人数:价格,套餐人数:价格";}
						else{echo "填写套餐名称,多个用英文逗号隔开";}
						?>
					</div>
				</div>
		<?php if(($model->type == 15)){?>
				<div class="form-group">
					<?php echo $form->labelEx($model,'person_num', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'person_num', array('class'=>'form-control')); ?>
					</div>
				</div>
		<?php }?>

				<div class="form-group">
					<label class="col-sm-2 control-label" for="StorePriceAdmin_numbers"><?php if(in_array($model->type,array(0,1,11,14))){echo "开通时长";}else{echo "数量";}?></label>
					<?php //echo $form->labelEx($model,'numbers', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'numbers',array('class'=>'form-control','size'=>'60','maxlength'=>'255')); ?>
						<?php if($model->type == 11){echo "填写数字单位为月份,用英文逗号隔开,和套餐名称一一对应";}
						else if($model->type == 10){echo "填写数字单位为家,用英文逗号隔开";}
						else if(in_array($model->type,array(12,13))){echo "填写数字单位为个,用英文逗号隔开";}
						else{echo "填写数字为月份,用英文逗号隔开";}
						?>
					</div>
				</div>
				<!--<div class="form-group">
					<?php echo $form->labelEx($model,'horn_num', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'horn_num', array('class'=>'form-control')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'sale_num', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'sale_num', array('class'=>'form-control')); ?>
					</div>
				</div>-->
		<?php if(($model->type == 10)||(($model->type >= 12))){?>
				<div class="form-group">
					<?php echo $form->labelEx($model,'price', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'price',array('class'=>'form-control','size'=>'10','maxlength'=>'10')); ?>
					</div>
				</div>
		<?php }?>
				<!--<div class="form-group">
					<?php echo $form->labelEx($model,'add_date', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'add_date', array('class'=>'form-control')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'update_date', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'update_date', array('class'=>'form-control')); ?>
					</div>
				</div>-->
				

			<div class="form-group form-group-center">
				<button class="btn btn-success btn-lg" type="submit">　确定　</button>
				<a class="btn btn-default btn-lg" type="button" href="/admin.php/storePriceAdmin">　取消　</a>
			</div>

		<?php
 $this->endWidget (); ?>
	</div>
</div>
<script>
	$(function(){
		$(".upload,#Splash_image").click(function() {
			$("#upload_input").trigger('click');
		});

		$('#upload_input').localResizeIMG({
			before: function() {

			},
			success: function(result) {
				var img = new Image();
				img.src = result.base64;
				var name = result.name;

				console.log(result);

				$('#formFile').html(result.base64.substr(22));
				//发送到服务端
				$.post('/admin.php/splash/upload', {'name': name, 'head': 1, formFile: result.base64.substr(22)}, function(data) {
					$('#StorePriceAdmin_poster').val(data);
					$('.thumb').show();
					$('.thumb').attr('src', data);
				});
			}
		});

		$("#StorePriceAdmin_type").change(function(){
			window.location.href = "/admin.php/storePriceAdmin/create?type="+$(this).val();
		});
	});

</script>
