<?php
	Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/themes/css/content.css");
//	Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/ckeditor/ckeditor.js');
Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/ueditor1_4_3-utf8-php/ueditor.config.js');
Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/ueditor1_4_3-utf8-php/ueditor.all.min.js');
?>
<?php
/* @var $this ProtocolController */
/* @var $model Protocol */
/* @var $form CActiveForm */
?>




<div class="main_right_content">
	<ol class="breadcrumb main_right_content_breadcrumb">
		<li><a href="<?php echo Yii::app()->createUrl('protocol/index')?>">协议&说明管理</a></li>
		<li><a href="javascript:void(0)">编辑协议</a></li>
		<div class="main_right_content_content_block_action_add">
			<a class="btn btn-success backurl" href="javascript:void(0)" goback="<?php echo $_GET['back'] ? $_GET['back'] : -1;?>">返回</a>
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
			'id'=>'protocol-form',
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
					<?php echo $form->labelEx($model,'content', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8" style="height:500px;">
						<script id="editor" type="text/plain" style="width: 100%; height: 800px;"><?php echo $model->content?></script>
							<?php echo $form->hiddenField($model,'content',array('id'=>'area_content')); ?>
					</div>
				</div>

				

			<div class="form-group form-group-center">
				<button class="btn btn-success btn-lg" type="button">　确定　</button>
				<a class="btn btn-default btn-lg backurl" type="button" goback="-1">　取消　</a>
			</div>

		<?php
 $this->endWidget (); ?>
	</div>
</div>

<script type="text/javascript">
$(function(){
	var ue = UE.getEditor('editor');
	var content0 = $('#area_content').val();
	$('.btn-success').click(function(){
		var content = UE.getEditor('editor').getContent();
		$('#area_content').val(content);
		var content = $('#area_content').val();
		if(content0 != content){
			if(window.confirm('请确认对以上信息进行修改')){
				$("#protocol-form").submit();
	    		return true;
	    		}else{
	    		return false;
	    		} 
			}
		$("#protocol-form").submit();
	})
})
</script>

