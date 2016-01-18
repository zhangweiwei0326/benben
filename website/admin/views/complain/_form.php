<?php
	Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/themes/css/content.css");
	$sex = array(1=>"男",2=>"女");
?>
<?php
/* @var $this ComplainController */
/* @var $model Complain */
/* @var $form CActiveForm */
?>




<div class="main_right_content">
	<ol class="breadcrumb main_right_content_breadcrumb">
		<li><a href="<?php echo Yii::app()->createUrl('complain/index')?>">投诉/建议管理</a></li>
		<li><a href="javascript:void(0)">查看详细</a></li>
		<div class="main_right_content_content_block_action_add">
			<a class="btn btn-success backurl" href="javascript:goBack();" goback="<?php //echo $_GET['back'] ? $_GET['back'] : -1;?>">返回</a>
		</div>
	</ol>

	<div class="main_right_content_content">
		<?php if($msg) {?>
				<div class="alert alert-danger alert-dismissible" role="alert">
				  <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				  <strong>警告！</strong> <?php echo $msg;?>
				</div>
				<?php }?>
		<?php $form=$this->beginWidget('CActiveForm', array(
			'id'=>'complain-form',
			'htmlOptions'=>array('enctype'=>'multipart/form-data', 'class'=>'form-horizontal'),
			'enableAjaxValidation'=>false,
		)); ?>
				
				<div class="form-group">
					<?php echo $form->labelEx($model,'member_id', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<input id="Complain_member_id" class="form-control" type="text" name="Complain[member_id]" readonly="readonly" value="<?php echo $name?>">
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'benben_id', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<input id="Complain_member_id" class="form-control" type="text" name="Complain[member_id]" readonly="readonly" value="<?php echo $model->benben_id?>">
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'phone', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<input id="Complain_member_id" class="form-control" type="text" name="Complain[member_id]" readonly="readonly" value="<?php echo $model->phone?>">
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'sex', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<input id="Complain_member_id" class="form-control" type="text" name="Complain[member_id]" readonly="readonly" value="<?php echo $sex[$model->sex]?>">
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'area', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<input id="Complain_member_id" class="form-control" type="text" name="Complain[member_id]" readonly="readonly" value="<?php echo $model->area?>">
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'info', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textArea($model,'info',array('class'=>'form-control','rows'=>'6','cols'=>'50', readonly=>"readonly")); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'created_time', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'created_time', array('class'=>'form-control', readonly=>"readonly")); ?>
					</div>
				</div>
				

			<div class="form-group form-group-center">
				
				<a class="btn btn-default btn-lg backurl" type="button" goback="-1">　取消　</a>
			</div>

		<?php
 $this->endWidget (); ?>
	</div>
</div>

