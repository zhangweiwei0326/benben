<?php
	Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/themes/css/content.css");
?>
<?php
/* @var $this BroadcastingLogController */
/* @var $model BroadcastingLog */
/* @var $form CActiveForm */
?>




<div class="main_right_content">
	<ol class="breadcrumb main_right_content_breadcrumb">
		<li><a href="#">用户管理</a></li>
		<li><a href="#">用户详细</a></li>
	</ol>

	<div class="main_right_content_content">
		<?php if($msg) {?>
				<div class="alert alert-danger alert-dismissible" role="alert">
				  <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				  <strong>警告！</strong> <?php echo $msg;?>
				</div>
				<?php }?>
		<?php $form=$this->beginWidget('CActiveForm', array(
			'id'=>'broadcasting-log-form',
			'htmlOptions'=>array('enctype'=>'multipart/form-data', 'class'=>'form-horizontal'),
			'enableAjaxValidation'=>false,
		)); ?>
				
				<div class="form-group">
					<?php echo $form->labelEx($model,'member_id', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'member_id', array('class'=>'form-control')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'league_id', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'league_id', array('class'=>'form-control')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'friend_id', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textArea($model,'friend_id',array('class'=>'form-control','rows'=>'6','cols'=>'50')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'receive_count', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'receive_count', array('class'=>'form-control')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'description', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'description',array('class'=>'form-control','size'=>'60','maxlength'=>'255')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'created_time', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'created_time', array('class'=>'form-control')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'content', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textArea($model,'content',array('class'=>'form-control','rows'=>'6','cols'=>'50')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'is_del', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'is_del', array('class'=>'form-control')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'type', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'type', array('class'=>'form-control')); ?>
					</div>
				</div>
				

			<div class="form-group form-group-center">
				<button class="btn btn-success btn-lg" type="submit">　确定　</button>
				<a class="btn btn-default btn-lg" type="button">　取消　</a>
			</div>

		<?php
 $this->endWidget (); ?>
	</div>
</div>

