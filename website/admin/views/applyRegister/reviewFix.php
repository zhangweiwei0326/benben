<?php
	Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/themes/css/content.css");
?>
<?php
/* @var $this ApplyRegisterController */
/* @var $model ApplyRegister */
/* @var $form CActiveForm */
?>




<div class="main_right_content">
	<ol class="breadcrumb main_right_content_breadcrumb">
		<li><a href="<?php echo $backUrl;?>">申请修改审核管理</a></li>
		<li><a href="javascript:void(0)">审核</a></li>
	</ol>

	<div class="main_right_content_content">
		<?php if($msg) {?>
				<div class="alert alert-danger alert-dismissible" role="alert">
				  <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				  <strong>警告！</strong> <?php echo $msg;?>
				</div>
				<?php }?>
		<?php $form=$this->beginWidget('CActiveForm', array(
			'id'=>'apply-register-form',
			'htmlOptions'=>array('enctype'=>'multipart/form-data', 'class'=>'form-horizontal'),
			'enableAjaxValidation'=>false,
		)); ?>
				
				<div class="form-group">
					<?php echo $form->labelEx($model,'政企名称', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'enterprise_name',array('class'=>'form-control','size'=>'60','maxlength'=>'255','readonly'=>'readonly')); ?>
					</div>
				</div>
				<div class="form-group">
				<?php   if($model->apply_type==2){								
										$label1="营业执照注册号/组织机构代码";$label2="营业执照注册号/组织机构代码(附件)";}else{
										$label1="办学许可证代码";$label2="办学许可证代码(附件)";
				                    }?>
					<?php echo $form->labelEx($model,$label1, array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'identity_num',array('class'=>'form-control','size'=>'60','maxlength'=>'255','readonly'=>'readonly')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,$label2, array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<img alt="附件" src="<?php echo $model->identity_attachment;?>" width="150"  height="100" id="attach_img">
					</div>
				</div>
			
				
				<div class="form-group">
					<?php echo $form->labelEx($model,'申请类型', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<input id="ApplyRegister_apply_type" class="form-control" type="text" value="<?php if($model->apply_type==2){echo '企业/组织';}else{echo '学校';}?>" name="ApplyRegister[apply_type]" readonly="readonly">
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'申请修改时间', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'created_time', array('class'=>'form-control','readonly'=>'readonly')); ?>
					</div>
				</div>


			<div class="form-group form-group-center">
			<?php $readOnly=$_GET['readOnly'];if($readOnly==0){?>
				<button id="agree_but" class="btn btn-success btn-lg" type="button" apply_id="<?php echo $_GET['id'];?>">　同意　</button>
				<a class="btn btn-danger btn-lg"  type="button" 
						href="<?php echo Yii::app()->createUrl("applyRegister/reviewFix",array('id'=>$_GET['id'],'status'=>'reject'));?>">
						　拒绝　
				</a>
				<a class="btn btn-default btn-lg" type="button" href="<?php echo $backUrl;?>">　取消　</a>
				<?php }else{ ?>
					<a class="btn btn-default btn-lg" type="button" href="<?php echo $backUrl;?>">　返回　</a>
				<?php } ?>
			</div>

		<?php
 $this->endWidget (); ?>
	</div>
</div>
<script type="text/javascript">
	$('#agree_but').click(function(){
		var e_name=$('#ApplyFixEnterprise_enterprise_name').val();
		var e_num=$('#ApplyFixEnterprise_identity_num').val();
		var e_attachment=$('#attach_img').attr('src');
		var apply_id=$(this).attr('apply_id');
// 		alert(e_name);alert(e_num);alert(e_attachment);
		$.ajax({
			type:"POST",
			url:"/admin.php/applyRegister/reviewFix/"+apply_id,
			data:{
    			'e_name': e_name,
    			'e_num':e_num,
    			'e_attachment':e_attachment,
    			'apply_id':apply_id
					},
			async:true,
			cache:false,
			dataType:"json",
			beforeSend:function(){	 			
			},
			success:function(data){
				if(data==200){
					window.location.href="/admin.php/applyRegister/index";
					}
				}
		});
	});
</script>


