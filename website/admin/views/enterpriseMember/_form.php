<?php
	Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/themes/css/content.css");
?>
<?php
/* @var $this EnterpriseMemberController */
/* @var $model EnterpriseMember */
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
			'id'=>'enterprise-member-form',
			'htmlOptions'=>array('enctype'=>'multipart/form-data', 'class'=>'form-horizontal'),
			'enableAjaxValidation'=>false,
		)); ?>
				
				<div class="form-group">
					<?php echo $form->labelEx($model,'contact_id', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<input id="EnterpriseMember_contact_id" class="form-control" type="text" value="<?php echo $ename?>" name="EnterpriseMember[ename]" readonly="readonly">
					</div>
				</div>
			<div class="form-group">
					<?php echo $form->labelEx($model,'name', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'name',array('class'=>'form-control','size'=>'60','maxlength'=>'255')); ?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label" for="EnterpriseMember_phone">手机号码</label>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'phone',array('class'=>'form-control','size'=>'20','maxlength'=>'20', readonly=>'readonly')); ?>
					</div>
				</div>	
				<div class="form-group">
					<label class="col-sm-2 control-label" for="EnterpriseMember_short_phone">其它号码</label>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'short_phone',array('class'=>'form-control','size'=>'20','maxlength'=>'20', readonly=>'readonly')); ?>
					</div>
				</div>				
				<div class="form-group">
					<label class="col-sm-2 control-label" for="is_member_id">是否是奔犇用户</label>
					<div class="col-sm-8">
						<input id="EnterpriseMember_is_contact_id" class="form-control" type="text" value="<?php echo $model->member_id ? "是":"否"?>" name="EnterpriseMember[is_member_name]" readonly="readonly">
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'created_time', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'created_time', array('class'=>'form-control', readonly=>'readonly')); ?>
					</div>
				</div>
				

			<div class="form-group form-group-center">
				<div class="btn btn-success btn-lg btn-lg1" type="">　确定　</div>
				<a class="btn btn-default btn-lg backurl" type="button" goback="-1">　返回　</a>
			</div>

		<?php
 $this->endWidget (); ?>
	</div>
</div>
<script>
 var input0 = $("input[name='EnterpriseMember[name]']").val();
 $(".btn-lg1").click(function(){
	 	 var input = $("input[name='EnterpriseMember[name]']").val();
	     if(input0 != input){
		    	 if(window.confirm('请确认对以上信息进行修改')){
		    		 $("#enterprise-member-form").submit();
		    		return true;
		    		}else{
		    		return false;
		    		} 
			 }
	     $("#enterprise-member-form").submit();
 		return true;	
	 });
 
</script>

