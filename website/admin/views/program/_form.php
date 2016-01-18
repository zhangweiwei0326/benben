<?php
	Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/themes/css/content.css");
?>
<?php
/* @var $this ProgramController */
/* @var $model Program */
/* @var $form CActiveForm */
?>



<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'program-form',
	'htmlOptions'=>array('enctype'=>'multipart/form-data'),
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

<div class="main_right_content_title">
	<div class="main_right_content_title_font">用户管理</div>
	<div class="main_right_content_title_navi">
		<div class="main_right_content_title_navi_root">
			<a href="/admin.php/system/index">用户管理</a>
		</div>
		<div class="main_right_content_title_navi_symbol">></div>
		<div class="main_right_content_title_navi_this">
			<a href="">用户详细</a>
		</div>
	</div>
</div>

<div class="main_right_content_content">
	<div class="main_right_content_content_top"></div>
		<div class="main_right_content_content_mid">

			<div class="main_right_content_content_form">
				<div class="main_right_content_content_msg "></div>
				
								<div class="main_right_content_content_form_block">
					<div class="main_right_content_content_form_block_font"><?php echo $form->labelEx($model,'name'); ?>
</div>
					<div class="main_right_content_content_form_block_input">
						<?php echo $form->textField($model,'name',array('size'=>60,'maxlength'=>100)); ?>
<!-- 							<span class="required_info">*</span> -->
					</div>
				</div>
							<div class="main_right_content_content_form_block">
					<div class="main_right_content_content_form_block_font"><?php echo $form->labelEx($model,'status'); ?>
</div>
					<div class="main_right_content_content_form_block_input">
						<?php echo $form->textField($model,'status'); ?>
<!-- 							<span class="required_info">*</span> -->
					</div>
				</div>
							<div class="main_right_content_content_form_block">
					<div class="main_right_content_content_form_block_font"><?php echo $form->labelEx($model,'created_by'); ?>
</div>
					<div class="main_right_content_content_form_block_input">
						<?php echo $form->textField($model,'created_by'); ?>
<!-- 							<span class="required_info">*</span> -->
					</div>
				</div>
							<div class="main_right_content_content_form_block">
					<div class="main_right_content_content_form_block_font"><?php echo $form->labelEx($model,'created_at'); ?>
</div>
					<div class="main_right_content_content_form_block_input">
						<?php echo $form->textField($model,'created_at'); ?>
<!-- 							<span class="required_info">*</span> -->
					</div>
				</div>
							<div class="main_right_content_content_form_block">
					<div class="main_right_content_content_form_block_font"><?php echo $form->labelEx($model,'script_count'); ?>
</div>
					<div class="main_right_content_content_form_block_input">
						<?php echo $form->textField($model,'script_count'); ?>
<!-- 							<span class="required_info">*</span> -->
					</div>
				</div>
				
				<div class="main_right_content_content_form_btn">
					<input class="main_right_content_content_form_btn_font"
						type="submit" value="确定" />
				</div>
			</div>
		</div>
	<div class="main_right_content_content_bottom"></div>
</div>
<?php
	$this->endWidget ();
	?>
