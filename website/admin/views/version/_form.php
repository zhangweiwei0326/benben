<?php
	Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/themes/css/content.css");
?>
<?php
/* @var $this VersionController */
/* @var $model Version */
/* @var $form CActiveForm */
?>




<div class="main_right_content">
	<ol class="breadcrumb main_right_content_breadcrumb">
		<li><a href="<?php echo Yii::app()->createUrl('version/index')?>">版本管理</a></li>
		<li><a href="javascript:void(0)">编辑版本</a></li>
		<div class="main_right_content_content_block_action_add">
			<a class="btn btn-success" href="<?php echo Yii::app()->createUrl('version/index');?>">返回</a>
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
			'id'=>'version-form',
			'htmlOptions'=>array('enctype'=>'multipart/form-data', 'class'=>'form-horizontal'),
			'enableAjaxValidation'=>false,
		)); ?>
				
				<div class="form-group">
					<?php echo $form->labelEx($model,'type', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'type', array('class'=>'form-control', readonly => 'readonly')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'version', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'version',array('class'=>'form-control','size'=>'20','maxlength'=>'20')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'info', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textArea($model,'info',array('class'=>'form-control','rows'=>'6','cols'=>'50')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'path', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'path',array('class'=>'form-control','size'=>'60','maxlength'=>'255')); ?>
					</div>
				</div>
				

			<div class="form-group form-group-center">
				<button class="btn btn-success btn-lg" type="">　确定　</button>
				<a class="btn btn-default btn-lg backurl" type="button" goback="-1">　取消　</a>
			</div>

		<?php
 $this->endWidget (); ?>
	</div>
</div>
<script>
 var input0 = $("#Version_version").val();
 var url0 = $("#Version_path").val();
 $(".btn-lg").click(function(){
	     var input = $("#Version_version").val();
	     var url = $("#Version_path").val();			 
		 if((input == "") || (url == "")){
		      alert("请输入版本号或下载地址");	
		      return false;
	     }else{
		     if((input0 != input) || (url0 != url)){
		    	 if(window.confirm('请确认对以上信息进行修改')){
		    		 $("#version-form").submit();
		    		return true;
		    		}else{
		    		return false;
		    		} 
			 }	    	
		     $("#version-form").submit();
              return true;
		     }
	 });
 
</script>

