<?php
	Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/themes/css/content.css");
?>
<?php
/* @var $this ApplyRegisterController */
/* @var $model ApplyRegister */
/* @var $form CActiveForm */
?>




<div class="main_right_content">
	<ol class="breadcrumb main_right_content_breadcrumb">
		<li><a href="<?php echo $backUrl;?>">申请审核管理</a></li>
		<li><a href="javascript:void(0)">审核</a></li>
	</ol>

	<div class="main_right_content_content">
		<?php if($msg) {?>
				<div class="alert alert-danger alert-dismissible" role="alert">
				  <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				  <strong>警告！</strong> <?php echo $msg;?>
				</div>
				<?php }?>
		<?php $form=$this->beginWidget('CActiveForm', array(
			'id'=>'apply-register-form',
			'htmlOptions'=>array('enctype'=>'multipart/form-data', 'class'=>'form-horizontal'),
			'enableAjaxValidation'=>false,
		)); ?>
				
				<div class="form-group">
					<?php echo $form->labelEx($model,'申请名称', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'name',array('class'=>'form-control','size'=>'60','maxlength'=>'255','readonly'=>'readonly')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'phone', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'phone',array('class'=>'form-control','size'=>'20','maxlength'=>'20','readonly'=>'readonly')); ?>
					</div>
				</div>
				<div class="form-group">
				<?php   if($model->apply_type==1){
										$label1="身份证号码";$label2="身份证号码(附件)";}elseif($model->apply_type==2){
										$label1="营业执照注册号/组织机构代码";$label2="营业执照注册号/组织机构代码(附件)";}else{
										$label1="办学许可证代码";$label2="办学许可证代码(附件)";
				                    }?>
					<?php echo $form->labelEx($model,$label1, array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'identity_num',array('class'=>'form-control','size'=>'60','maxlength'=>'255','readonly'=>'readonly')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,$label2, array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<img alt="附件" src="<?php echo $model->identity_attachment;?>" width="150"  height="100">
					</div>
				</div>
				<?php if($model->apply_type==1){?>
				<div class="form-group">
					<?php echo $form->labelEx($model,'identity_attachment_more', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<img alt="身份证反面" src="<?php echo $model->identity_attachment_more;?>" width="150"  height="100">
					</div>
				</div>
				<?php }?>
				<div class="form-group">
					<?php echo $form->labelEx($model,'enterprise_name', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'enterprise_name',array('class'=>'form-control','size'=>'60','maxlength'=>'255','readonly'=>'readonly')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'政企通讯录类型', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<input id="ApplyRegister_enterprise_type" class="form-control" type="text" value="<?php if($model->enterprise_type==1){echo '企业政企通讯录';}else{echo '虚拟网政企通讯录';}?>" name="ApplyRegister[enterprise_type]" readonly="readonly">
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'login_name', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'login_name',array('class'=>'form-control','size'=>'50','maxlength'=>'50','readonly'=>'readonly')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'email', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'email',array('class'=>'form-control','size'=>'50','maxlength'=>'50','readonly'=>'readonly')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'申请类型', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<input id="ApplyRegister_apply_type" class="form-control" type="text" value="<?php if($model->apply_type==1){echo '个人';}elseif($model->apply_type==2){echo '企业/组织';}else{echo '学校';}?>" name="ApplyRegister[apply_type]" readonly="readonly">
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'created_time', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'created_time', array('class'=>'form-control','readonly'=>'readonly')); ?>
					</div>
				</div>		

			<div class="form-group form-group-center">
			<?php $readOnly=$_GET['readOnly'];if($readOnly==0){?>
				<button class="btn btn-success btn-lg" type="submit">　同意　</button>
				<a class="btn btn-danger btn-lg"  type="button" href="<?php echo Yii::app()->createUrl("applyRegister/review",array('id'=>$_GET['id'],'page'=>$_REQUEST['page'],'status'=>'reject'));?>">　拒绝　</a>
				<a class="btn btn-default btn-lg" type="button" href="<?php echo $backUrl;?>">　取消　</a>
				<?php }else{ ?>
					<a class="btn btn-default btn-lg" type="button" href="<?php echo $backUrl;?>">　返回　</a>
				<?php } ?>
			</div>

		<?php
 $this->endWidget (); ?>
	</div>
</div>

