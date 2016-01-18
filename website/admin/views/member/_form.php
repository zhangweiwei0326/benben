<?php
	Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/themes/css/content.css");
	$sexArray = array('0'=>'未知', '1'=>'男', '2'=>'女');
?>
<?php
/* @var $this MemberController */
/* @var $model Member */
/* @var $form CActiveForm */
?>




<div class="main_right_content">
	<ol class="breadcrumb main_right_content_breadcrumb">
		<li><a href="<?php echo Yii::app()->createUrl("member/index");?>">会员管理</a></li>
		<li><a href="javascript:void(0)">编辑会员</a></li>
		<div class="main_right_content_content_block_action_add">
			<a class="btn btn-success backurl" href="javascript:goBack()" data="<?php //echo $_GET['back'] ? $_GET['back'] : -1;?>">返回</a>
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
			'id'=>'member-form',
			'htmlOptions'=>array('enctype'=>'multipart/form-data', 'class'=>'form-horizontal'),
			'enableAjaxValidation'=>false,
		)); ?>
				
				<div class="form-group">
					<?php echo $form->labelEx($model,'benben_id', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'benben_id', array('class'=>'form-control ', 'data'=>$model->benben_id)); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'name', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'name',array('class'=>'form-control','size'=>'20','maxlength'=>'20', 'readonly' => readonly,)); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'nick_name', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'nick_name',array('class'=>'form-control','size'=>'20','maxlength'=>'20', 'readonly' => readonly,)); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'poster', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<img src="<?php echo $model->poster ? $model->poster : Yii::app()->request->baseUrl.'/themes/images/poster.jpg';?>" width="50">
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'phone', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'phone',array('class'=>'form-control','size'=>'11','maxlength'=>'11', 'readonly' => readonly,)); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'phone_model', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'phone_model',array('class'=>'form-control','size'=>'11','maxlength'=>'11', 'readonly' => readonly,)); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'id_card', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'id_card',array('class'=>'form-control','size'=>'20','maxlength'=>'20', 'readonly' => readonly,)); ?>
					</div>
				</div>
				<!--
				<div class="form-group">
					<?php echo $form->labelEx($model,'card_poster', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'card_poster',array('class'=>'form-control','size'=>'60','maxlength'=>'255')); ?>
					</div>
				</div>
				-->
				<div class="form-group">
					<?php echo $form->labelEx($model,'sex', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $sexArray[$sex];?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'age', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<input class="form-control" readonly="readonly" name="Member[age]" id="Member_age" type="text" maxlength="40" value="<?php echo $this->age($model->age);?>">
					</div>
				</div>
				<div class="form-group">
				    <div style="width:192px;text-align: right;" class="col-sm-8">
				        <label for="Member_province" >地区</label>
				    </div>
					<?php //echo $form->labelEx($model,'province', array("class"=>"col-sm-2 control-label"));?>
					<div style="width:126px" class="col-sm-8">
						<label for="Member_province" ><?php echo $areas['province']; ?></label>
					</div>
					<?php //echo $form->labelEx($model,'city', array("style"=>"width:60px","class"=>"col-sm-2 control-label"));?>
					<div style="width:126px" class="col-sm-8">
					<label for="Member_province" ><?php echo $areas['city']; ?></label>
						<?php //echo $form->labelEx($model,'city',array(0=>$areas['city']),array('class'=>'form-control','size'=>1)); ?>
					</div>
					<?php //echo $form->labelEx($model,'area', array("style"=>"width:60px","class"=>"col-sm-2 control-label"));?>
					<div style="width:126px" class="col-sm-8">
					<label for="Member_province" ><?php echo $areas['area']; ?></label>
						<?php //echo $form->labelEx($model,'area',array(0=>$areas['area']),array('class'=>'form-control','size'=>1)); ?>
					</div>
					<?php //echo $form->labelEx($model,'street', array("style"=>"width:60px","class"=>"col-sm-2 control-label"));?>
					<div style="width:126px" class="col-sm-8">
					<label for="Member_province" ><?php echo $areas['street']; ?></label>
						<?php //echo $form->labelEx($model,'street',array(0=>$areas['street']),array('class'=>'form-control','size'=>1)); ?>
					</div>
					
				</div>
				<!-- <div class="form-group">
					<?php echo $form->labelEx($model,'city', array("class"=>"col-sm-2 control-label"));?>
					<div style="width:126px" class="col-sm-8">
						<?php echo $form->listBox($model,'city',array(0=>$areas['city']),array('class'=>'form-control','size'=>1)); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'area', array("class"=>"col-sm-2 control-label"));?>
					<div style="width:126px" class="col-sm-8">
						<?php echo $form->listBox($model,'area',array(0=>$areas['area']),array('class'=>'form-control','size'=>1)); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'street', array("class"=>"col-sm-2 control-label"));?>
					<div style="width:126px" class="col-sm-8">
						<?php echo $form->listBox($model,'street',array(0=>$areas['street']),array('class'=>'form-control','size'=>1)); ?>
					</div>
				</div>-->
				<div class="form-group">
					<?php echo $form->labelEx($model,'cornet', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'cornet',array('class'=>'form-control','size'=>'20','maxlength'=>'20', 'readonly' => readonly,)); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'integral', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'integral', array('class'=>'form-control', 'readonly'=>"readonly")); ?>
					</div>
				</div>
				<?php 
				$level = 0;
				$level_all = $this->getlevel();
				foreach ($level_all as $va){
					if($model->integral <= $va[1]){
						$level = $va[2];
						break;
					}
				}				
				?>
				<div class="form-group">
					<label for="Member_integral" class="col-sm-2 control-label">等级</label>
					<div class="col-sm-8">
						<input type="text" value="<?php echo $level?>" id="Member_level" name="Member[level]" class="form-control" readonly="readonly">
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'coin', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'coin', array('class'=>'form-control', 'readonly'=>"readonly")); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'status', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php  echo $form->radioButtonList($model,'status', array('0'=>'启用', '1'=>'禁用1周', '2'=>'禁用2周', '3'=>'禁用1个月', '4'=>'禁用3个月', '5'=>'无限期'), 
												array('separator'=>'', 'checked'=>'checked', 'style'=>'float:left;', 'template'=>'{input} {label}',
												'labelOptions'=>array('style'=>'float:left;display:inline;margin-right:40px;')));?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label" for="Member_reason">	启/禁用原因</label>
					<div class="col-sm-8">
						<textarea id="Member_reason" class="form-control" name="Member[reason]" cols="50" rows="6"><?php echo $reason?></textarea>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label" for="Member_reason">	启/禁用记录</label>
					<div class="col-sm-8">
					  <ul style="padding-top: 6px">
						<?php
						if($status_info){
                            foreach ($status_info as $val){
						?>
						    <li style="height: 25px"><?php echo date("Y-m-d H:i:s",$val['created_time'])?> &nbsp;&nbsp;<?php echo $val['status']?>&nbsp;&nbsp;<?php echo $val['name']?>&nbsp;&nbsp;<?php echo $val['reason']?></li>
						<?php
						  }	
						}else{
							echo "没有记录";
						} 
						?>
						</ul>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'created_time', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'created_time', array('class'=>'form-control', 'readonly' => readonly,)); ?>
					</div>
				</div>
			<div class="form-group form-group-center">
				<div class="btn btn-success btn-lg btn-lg1" type="">　确定　</div>
				<a class="btn btn-default btn-lg backurl" href="javascript:goBack()" data="<?php echo $_GET['back'] ? $_GET['back'] : -1;?>">　取消　</a>
			</div>

		<?php
 $this->endWidget (); ?>
	</div>
</div>
<script>
 var input0 = $("input[name='Member[status]']:checked").val();
 $(".btn-lg1").click(function(){
	     var input = $("input[name='Member[status]']:checked").val();	
		 var text = $("#Member_reason").val();
		 var oldbenben = $('#Member_benben_id').attr('data');
		 var newbenben = $('#Member_benben_id').val();
		 if(input != 0 && text == ""){
		      alert("请输入禁用原因");	
		      return false;
	     }else{
		     if(input0 != input || (oldbenben != newbenben)){
		    	 if(window.confirm('请确认对以上信息进行修改')){
		    		 $("#member-form").submit();
		    		return true;
		    		}else{
		    		return false;
		    		} 
			 }	    	
		     $("#member-form").submit();
              return true;
		     }
	 });
 
</script>
