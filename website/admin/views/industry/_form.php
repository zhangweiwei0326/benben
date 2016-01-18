<?php
	Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/themes/css/content.css");
?>
<?php
/* @var $this IndustryController */
/* @var $model Industry */
/* @var $form CActiveForm */
?>




<div class="main_right_content">
	<ol class="breadcrumb main_right_content_breadcrumb">
		<li><a href="<?php echo Yii::app()->createUrl('industry/index')?>">行业字典管理</a></li>
		<li><a href="javascript:void"><?php if($_GET['add']){echo '增加行业字典';}else{echo '编辑行业字典';}?></a></li>
		<div class="main_right_content_content_block_action_add">
			<a class="btn btn-success backurl" href="javascript:void(0)" goback=" <?php echo $goback ? $goback : -1;?>">返回</a>
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
			'id'=>'industry-form',
			'htmlOptions'=>array('enctype'=>'multipart/form-data', 'class'=>'form-horizontal'),
			'enableAjaxValidation'=>false,
		)); ?>
			<?php if($parent_name) {?>
				<div class="form-group">
					<label class="col-sm-2 control-label" for="Industry_name">父级行业</label>
					<div class="col-sm-8">
						<input id="Industry_name" class="form-control" value="<?php echo $parent_name?>" readonly = "readonly" type="text" name="Industry[name]" maxlength="255" size="60">
					</div>
				</div>
				<?php }?>
				<div class="form-group">
					<?php echo $form->labelEx($model,'name', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'name',array('class'=>'form-control','size'=>'60','maxlength'=>'255')); ?>
					</div>
				</div>
			<div class="form-group form-group-center">
				<button class="btn btn-success btn-lg" type="button">　确定　</button>
				<a class="btn btn-default btn-lg backurl" type="button" goback=" <?php echo $goback ? $goback : -1;?>">　取消　</a>
			</div>

		<?php
 $this->endWidget (); ?>
	</div>
</div>
<script>
 var input0 = $("#Industry_name").val();
 $(".btn-lg").click(function(){
	     var input = $("#Industry_name").val();			
		 if(input == ""){
		      alert("请输入名称");	
		      return false;
	     }else{
		     if(input0 != input){
		    	 if(window.confirm('请确认对以上信息进行修改')){
		    		 $("#industry-form").submit();
		    		return true;
		    		}else{
		    		return false;
		    		} 
			 }	    	
		     $("#industry-form").submit();
              return true;
		     }
	 });
 
</script>

