<?php
	Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/themes/css/content.css");
?>
<?php
/* @var $this StoreOrderInfoController */
/* @var $model StoreOrderInfo */
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
			'id'=>'store-order-info-form',
			'htmlOptions'=>array('enctype'=>'multipart/form-data', 'class'=>'form-horizontal'),
			'enableAjaxValidation'=>false,
		)); ?>
				
				<div class="form-group">
					<?php echo $form->labelEx($model,'order_sn', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'order_sn',array('class'=>'form-control','size'=>'60','maxlength'=>'255')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'member_id', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'member_id',array('class'=>'form-control','size'=>'11','maxlength'=>'11')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'order_status', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'order_status', array('class'=>'form-control')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'shipping_status', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'shipping_status', array('class'=>'form-control')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'pay_status', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'pay_status', array('class'=>'form-control')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'consignee', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'consignee',array('class'=>'form-control','size'=>'60','maxlength'=>'60')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'country', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'country', array('class'=>'form-control')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'province', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'province', array('class'=>'form-control')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'city', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'city',array('class'=>'form-control','size'=>'11','maxlength'=>'11')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'area', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'area',array('class'=>'form-control','size'=>'11','maxlength'=>'11')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'street', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'street',array('class'=>'form-control','size'=>'11','maxlength'=>'11')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'address', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'address',array('class'=>'form-control','size'=>'60','maxlength'=>'255')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'zipcode', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'zipcode',array('class'=>'form-control','size'=>'60','maxlength'=>'60')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'tel', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'tel',array('class'=>'form-control','size'=>'60','maxlength'=>'60')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'mobile', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'mobile',array('class'=>'form-control','size'=>'60','maxlength'=>'60')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'email', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'email',array('class'=>'form-control','size'=>'60','maxlength'=>'60')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'best_time', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'best_time',array('class'=>'form-control','size'=>'60','maxlength'=>'120')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'postscript', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'postscript',array('class'=>'form-control','size'=>'60','maxlength'=>'255')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'shipping_id', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'shipping_id', array('class'=>'form-control')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'shipping_sn', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'shipping_sn',array('class'=>'form-control','size'=>'60','maxlength'=>'100')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'shipping_name', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'shipping_name',array('class'=>'form-control','size'=>'60','maxlength'=>'120')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'pay_id', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'pay_id', array('class'=>'form-control')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'pay_name', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'pay_name',array('class'=>'form-control','size'=>'60','maxlength'=>'120')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'inv_payee', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'inv_payee',array('class'=>'form-control','size'=>'60','maxlength'=>'120')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'inv_content', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'inv_content',array('class'=>'form-control','size'=>'60','maxlength'=>'120')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'goods_amount', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'goods_amount',array('class'=>'form-control','size'=>'10','maxlength'=>'10')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'shipping_fee', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'shipping_fee',array('class'=>'form-control','size'=>'10','maxlength'=>'10')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'insure_fee', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'insure_fee',array('class'=>'form-control','size'=>'10','maxlength'=>'10')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'pay_fee', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'pay_fee',array('class'=>'form-control','size'=>'10','maxlength'=>'10')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'money_paid', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'money_paid',array('class'=>'form-control','size'=>'10','maxlength'=>'10')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'bonus', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'bonus',array('class'=>'form-control','size'=>'10','maxlength'=>'10')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'order_amount', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'order_amount',array('class'=>'form-control','size'=>'10','maxlength'=>'10')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'add_time', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'add_time',array('class'=>'form-control','size'=>'10','maxlength'=>'10')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'confirm_time', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'confirm_time',array('class'=>'form-control','size'=>'10','maxlength'=>'10')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'pay_time', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'pay_time',array('class'=>'form-control','size'=>'10','maxlength'=>'10')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'shipping_time', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'shipping_time',array('class'=>'form-control','size'=>'10','maxlength'=>'10')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'extend_shipping_time', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'extend_shipping_time', array('class'=>'form-control')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'bonus_id', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'bonus_id', array('class'=>'form-control')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'invoice_no', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'invoice_no',array('class'=>'form-control','size'=>'60','maxlength'=>'255')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'extension_code', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'extension_code', array('class'=>'form-control')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'extension_id', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'extension_id', array('class'=>'form-control')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'to_buyer', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'to_buyer',array('class'=>'form-control','size'=>'60','maxlength'=>'255')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'pay_note', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'pay_note',array('class'=>'form-control','size'=>'60','maxlength'=>'255')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'inv_type', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'inv_type',array('class'=>'form-control','size'=>'60','maxlength'=>'60')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'tax', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'tax',array('class'=>'form-control','size'=>'10','maxlength'=>'10')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'parent_id', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'parent_id', array('class'=>'form-control')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'discount', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'discount',array('class'=>'form-control','size'=>'10','maxlength'=>'10')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'qrcode', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'qrcode',array('class'=>'form-control','size'=>'60','maxlength'=>'255')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'store_comment_status', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'store_comment_status', array('class'=>'form-control')); ?>
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

