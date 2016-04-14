<?php
	Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/themes/css/content.css");
?>
<?php
/* @var $this BxUserController */
/* @var $model User */
/* @var $form CActiveForm */
?>




<div class="main_right_content">
	<ol class="breadcrumb main_right_content_breadcrumb">
		<li><a href="<?php echo Yii::app()->createUrl('user/index')?>">用户管理</a></li>
		<li><a href="javascript:void(0)">百姓管理员</a></li>
	</ol>

	<div class="main_right_content_content">
		<?php if($msg) {?>
				<div class="alert alert-danger alert-dismissible" role="alert">
				  <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				  <strong>警告！</strong> <?php echo $msg;?>
				</div>
				<?php }?>
		<?php $form=$this->beginWidget('CActiveForm', array(
			'id'=>'user-form',
			'htmlOptions'=>array('enctype'=>'multipart/form-data', 'class'=>'form-horizontal'),
			'enableAjaxValidation'=>false,
		)); ?>
				
				<div class="form-group">
					<?php echo $form->labelEx($model,'username', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php if($_GET['add']){?>
						<?php echo $form->textField($model,'username',array('class'=>'form-control','size'=>'45','maxlength'=>'45')); ?>
						<?php }else{?>
						<?php echo $form->textField($model,'username',array('class'=>'form-control','size'=>'45','maxlength'=>'45', readonly=>'readonly')); ?>
						<?php }?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $form->labelEx($model,'enterprise_id', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php if($_GET['add']){?>
							<select class="form-control" name="enterprise_id" id="type">
						<?php }else{?>
							<select class="form-control" name="enterprise_id" id="type" disabled="disabled">
						<?php }?>
								<?php foreach ($enterprise_id as $value){?>
									<?php if($value['id'] == $model->enterprise_id){?>
										<option value="<?php echo $value['id']?>" selected="selected"><?php echo $value['name']?></option>
									<?php }else{?>
										<option value="<?php echo $value['id']?>" ><?php echo $value['name']?></option>
								<?php }}?>
						</select>
					</div>
				</div>

				<?php if(!$_GET['add']){?>
				<div class="form-group">
					<?php echo $form->labelEx($model,'role', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<select class="form-control" name="role" id="type" disabled="disabled">
							<?php foreach ($result as $value){?>
								<?php if($value['id'] == $model->role){?>
								<option value="<?php echo $value['id']?>" selected="selected"><?php echo $value['role_name']?></option>
								<?php }else{?>
									<option value="<?php echo $value['id']?>" ><?php echo $value['role_name']?></option>
							<?php }}?>
						</select>
					</div>
				</div>
				<?php }?>

				<div class="form-group">
					<?php echo $form->labelEx($model,'password', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->passwordField($model,'password', array('class'=>'form-control')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'disable', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<select class="form-control" name="User[disable]" id="disable">													
							<option value="0" <?php if($model->disable == 0) echo 'selected = "selected"';?>>否</option>	
							<option value="1" <?php if($model->disable == 1) echo 'selected = "selected"';?>>是</option>						    
						</select>
						<?php //echo $form->passwordField($model,'disable', array('class'=>'form-control')); ?>
					</div>
				</div>
				<?php if(!$_GET['add']){?>
				<div class="form-group">
					<?php echo $form->labelEx($model,'created_time', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php if($_GET['add']){?>
						<?php echo $form->textField($model,'created_time', array('class'=>'form-control')); ?>
						<?php }else{?>
						<?php echo $form->textField($model,'created_time', array('class'=>'form-control',readonly=>'readonly')); ?>
						<?php }?>
					</div>
				</div>
				
				<div class="form-group">
					<?php echo $form->labelEx($model,'last_login', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php if($_GET['add']){?>
						<?php echo $form->textField($model,'last_login', array('class'=>'form-control')); ?>
						<?php }else{?>
						<?php echo $form->textField($model,'last_login', array('class'=>'form-control',readonly=>'readonly')); ?>
						<?php }?>
					</div>
				</div>
				<?php }?>
				

			<div class="form-group form-group-center">
			
			<?php  if($role & 1){?>
				<button class="btn btn-success btn-lg btn-lg1" type="">　确定　</button>
				<?php }?>
				<a class="btn btn-default btn-lg backurl" type="button" goback="-1">　取消　</a>
			</div>

		<?php
 $this->endWidget (); ?>
	</div>
</div>
<script>
 var input0 = $("#type").val();
 var url0 = $("#User_password").val();
 $(".btn-lg1").click(function(){
	     var input = $("#type").val();
	     var url = $("#User_password").val();			 
		 if((url == "")){
		      alert("请输入用户密码");	
		      return false;
	     }else{
		     if((input0 != input) || (url0 != url)){
		    	 if(window.confirm('请确认对以上信息进行修改')){
		    		 $("#user-form").submit();
		    		return true;
		    		}else{
		    		return false;
		    		} 
			 }	    	
		     $("#user-form").submit();
              return true;
		     }
	 });
 
</script>
