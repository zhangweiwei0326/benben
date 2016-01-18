<?php
	Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/themes/css/content.css");
?>
<?php
/* @var $this BroadcastingLogController */
/* @var $model BroadcastingLog */
/* @var $form CActiveForm */
$type = array(0=>'否',1=>'是');
$sex = array(0=>'未知',1=>'男',2=>'女');
?>

<style>
.a{ width:12%;}
.b{ width:14%;}
</style>


<div class="main_right_content">
	<ol class="breadcrumb main_right_content_breadcrumb">
		<li><a href="<?php echo Yii::app()->createUrl('notice/broadcastinglog')?>">小喇叭管理</a></li>
		<li><a href="#">编辑小喇叭</a></li>
		<a href="javascript:goBack();" style="float: right;" class="btn btn-success btn-lg"> 返回 </a>
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
					<?php echo $form->labelEx($items,'benben_id', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($items,'benben_id', array('class'=>'form-control','readonly'=>'readonly')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($items,'phone', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($items,'phone', array('class'=>'form-control','readonly'=>'readonly')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'是否为号码直通车', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'type',array('class'=>'form-control','value'=>$type[$model->type],'readonly'=>'readonly')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($items,'地区', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'receive_count', array('class'=>'form-control','value'=>$areaInfo[$items->province].$areaInfo[$items->city].$areaInfo[$items->area],'readonly'=>'readonly')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($items,'sex', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($items,'sex',array('class'=>'form-control','value'=>$sex[$items->sex],'readonly'=>'readonly')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($items,'name', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($items,'name', array('class'=>'form-control','readonly'=>'readonly')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($items,'nick_name', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($items,'nick_name',array('class'=>'form-control','readonly'=>'readonly')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'本次发送小喇叭人数', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'is_del', array('class'=>'form-control','readonly'=>'readonly','value'=>$model->receive_count)); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'发送时间', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'created_time', array('class'=>'form-control','value'=>date("Y-m-d H:i:s",$model->created_time),'readonly'=>'readonly')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'累计喊话次数', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'created_time', array('class'=>'form-control','value'=>$counts,'readonly'=>'readonly')); ?>
					</div>
				</div>
				
				<div class="form-group">
					<?php echo $form->labelEx($model,'向好友喊话累计次数', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-1 a">
						<?php echo $form->textField($model,'created_time', array('class'=>'form-control','value'=>$f_counts,'readonly'=>'readonly')); ?>
					</div>
					<?php echo $form->labelEx($model,'总计人数', array("class"=>"col-sm-2 b control-label"));?>
					<div class="col-sm-1 a">
						<?php echo $form->textField($model,'created_time', array('class'=>'form-control','value'=>$f_num,'readonly'=>'readonly')); ?>
					</div>
					<?php echo $form->labelEx($model,'平均喊话人数', array("class"=>"col-sm-2 b control-label"));?>
					<div class="col-sm-1 a">
						<?php echo $form->textField($model,'created_time', array('class'=>'form-control','value'=>round($f_num/($f_counts ? $f_counts :1),2),'readonly'=>'readonly')); ?>
					</div>
				</div>
				
				<div class="form-group">
					<?php echo $form->labelEx($model,'向好友联盟喊话累计次数', array("class"=>"col-sm-2  control-label"));?>
					<div class="col-sm-1 a">
						<?php echo $form->textField($model,'created_time', array('class'=>'form-control','value'=>$l_counts,'readonly'=>'readonly')); ?>
					</div>
					<?php echo $form->labelEx($model,'总计人数', array("class"=>"col-sm-2 b control-label"));?>
					<div class="col-sm-1 a">
						<?php echo $form->textField($model,'created_time', array('class'=>'form-control','value'=>$l_num,'readonly'=>'readonly')); ?>
					</div>
					<?php echo $form->labelEx($model,'平均喊话人数', array("class"=>"col-sm-2 b control-label"));?>
					<div class="col-sm-1 a">
						<?php echo $form->textField($model,'created_time', array('class'=>'form-control','value'=>round($l_num/($l_counts ? $l_counts :1),2),'readonly'=>'readonly')); ?>
					</div>
				</div>
			<!--  <div class="form-group form-group-center">
				<button class="btn btn-success btn-lg" type="submit">　确定　</button>
				<a class="btn btn-default btn-lg" type="button">　取消　</a>
			</div>-->

		<?php
 $this->endWidget (); ?>
	</div>
</div>

