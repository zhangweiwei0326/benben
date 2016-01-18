<?php
	Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/themes/css/content.css");
?>
<?php
/* @var $this NoticeController */
/* @var $model News */
/* @var $form CActiveForm */
?>




<div class="main_right_content">
	<ol class="breadcrumb main_right_content_breadcrumb">
		<li><a href="<?php echo Yii::app()->createUrl('notice')?>">消息管理</a></li>
		<li><a href="#">消息详细</a></li>
	</ol>

	<div class="main_right_content_content">
		<?php if($msg) {?>
				<div class="alert alert-danger alert-dismissible" role="alert">
				  <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				  <strong>警告！</strong> <?php echo $msg;?>
				</div>
				<?php }?>
		<?php $form=$this->beginWidget('CActiveForm', array(
			'id'=>'news-form',
			'htmlOptions'=>array('enctype'=>'multipart/form-data', 'class'=>'form-horizontal'),
			'enableAjaxValidation'=>false,
		)); ?>
				
				
				
				<div class="form-group">
					<?php echo $form->labelEx($model,'内容', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textArea($model,'content',array('class'=>'form-control','rows'=>'6','cols'=>'50')); ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $form->labelEx($model,'创建时间', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'created_time', array('class'=>'form-control')); ?>
					</div>
				</div>
				
				

			<div class="form-group form-group-center">
				<button class="btn btn-success btn-lg" id="confirm" type="button">　确定　</button>
				<a class="btn btn-default btn-lg" type="button" href="javascript:goBack()">　取消　</a>
			</div>

		<?php
 $this->endWidget (); ?>
	</div>
</div>
<script>
 var input0 = $("#News_content").val();
 $("#confirm").click(function(){
	     var input = $("#News_content").val();			 
		 if(input == ""){
		      alert("请输入消息内容");	
		      return false;
	     }else{
		     if(input0 != input){
		    	 if(window.confirm('请确认对以上信息进行修改')){
		    		 $("#news-form").submit();
		    		return true;
		    		}else{
		    		return false;
		    		} 
			 }	    	
		     $("#news-form").submit();
              return true;
		     }
	 });
 
</script>
