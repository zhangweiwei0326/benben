<?php
	Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/themes/css/content.css");
?>
<?php
/* @var $this HappyController */
/* @var $model Happy */
/* @var $form CActiveForm */
?>




<div class="main_right_content">
	<ol class="breadcrumb main_right_content_breadcrumb">
		<li><a href="<?php echo Yii::app()->createUrl('happy/index')?>">开心一刻钟管理</a></li>
		<li><a href="javascript:void(0)">编辑开心一刻</a></li>
			<div class="main_right_content_content_block_action_add">
			<a class="btn btn-success backurl" href="javascript:goBack();" goback="">返回</a>
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
			'id'=>'happy-form',
			'htmlOptions'=>array('enctype'=>'multipart/form-data', 'class'=>'form-horizontal'),
			'enableAjaxValidation'=>false,
		)); ?>
				
				<div class="form-group">
					<?php echo $form->labelEx($model,'description', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textArea($model,'description',array('class'=>'form-control','rows'=>'6','cols'=>'50')); ?>
					</div>
				</div>
			<div class="form-group form-group-center">
				<button class="btn btn-success btn-lg btn-lg1" type="submit">　确定　</button>
				<a class="btn btn-default btn-lg backurl" type="button" goback="<?php echo $_GET['back'] ? $_GET['back'] : -1;?>">　取消　</a>
			</div>

		<?php
 $this->endWidget (); ?>
	</div>
</div>
<script>
var text0 = $("#Happy_description").val();
 $(".btn-lg1").click(function(){
		 var text = $("#Happy_description").val();
		 if(text == ""){
		      alert("请输入内容");	
		      return false;
	     }else{
		     if(text0 != text){
		    	 if(window.confirm('请确认对以上信息进行修改')){
		    		 $("#happy-form").submit();
		    		return true;
		    		}else{
		    		return false;
		    		} 
			 }	    	
		     $("#happy-form").submit();
              return true;
		     }
	 });
 
</script>

