<?php
	Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/themes/css/content.css");
?>
<?php
/* @var $this NumberTrainController */
/* @var $model NumberTrain */
/* @var $form CActiveForm */
$status = array (
		"0" => "启用",
		"1" => "禁用1周",
		"2" => "禁用2周",
		"3" => "禁用1个月",
		"4" => "禁用3个月",
		"5" => "无限期"
)
?>




<div class="main_right_content">
	<ol class="breadcrumb main_right_content_breadcrumb">
		<li><a href="<?php  echo Yii::app()->createUrl("numberTrain/index");?>">直通车管理</a></li>
		<li><a href="javascript:void(0)">编辑直通车</a></li>
		<div class="main_right_content_content_block_action_add">
			<a class="btn btn-success backurl" href="javascript:goBack();" goback="<?php //echo $_GET['back'] ? $_GET['back'] : -1;?>">返回</a>
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
			'id'=>'number-train-form',
			'htmlOptions'=>array('enctype'=>'multipart/form-data', 'class'=>'form-horizontal'),
			'enableAjaxValidation'=>false,
		)); ?>
				
				<div class="form-group">
					<?php echo $form->labelEx($model,'name', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'name',array('class'=>'form-control','size'=>'60','maxlength'=>'255', readonly => "readonly")); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'short_name', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'short_name',array('class'=>'form-control','size'=>'60','maxlength'=>'255', readonly => "readonly")); ?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label" for="NumberTrain_views">浏览数</label>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'views',array('class'=>'form-control', readonly => "readonly")); ?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label" for="NumberTrain_views">收藏数</label>
					<div class="col-sm-8">
						<input class="form-control" readonly="readonly" name="NumberTrain[views]" id="NumberTrain_views" type="text" value="<?php echo $additional['collect'];?>">
						
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label" for="NumberTrain_views">报价数</label>
					<div class="col-sm-8">
						<input class="form-control" readonly="readonly" name="NumberTrain[views]" id="NumberTrain_views" type="text" value="<?php echo $additional['buy'];?>">
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'poster', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'poster',array('class'=>'form-control','size'=>'60','maxlength'=>'255', readonly => "readonly")); ?>
						<img src="<?php echo Yii::app()->request->hostInfo.$model->poster ?>" style="margin-top:10px;width:300px;">
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'phone', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'phone',array('class'=>'form-control','size'=>'11','maxlength'=>'11' , readonly => "readonly")); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'telephone', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'telephone',array('class'=>'form-control','size'=>'13','maxlength'=>'13', readonly => "readonly")); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'member_id', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<input id="NumberTrain_member_id" class="form-control" type="text" value="<?php echo $member_id?>" name="NumberTrain[member_id]" readonly = "readonly">
					</div>
				</div>

				<div class="form-group">
					<label class="col-sm-2 control-label" for="Enterprise_member_id">创建人身份证</label>
					<div class="col-sm-8">
						<input id="NumberTrain_member_id" class="form-control" type="text" value="<?php echo $apply_info->id_card;?>" name="NumberTrain[member_id]" readonly = "readonly">
						<img src="<?php echo $apply_info->poster1;?>"  width="200" height="180"/>
						<img src="<?php echo $apply_info->poster2;?>" width="200" height="180"/>
					</div>
				</div>


				<div class="form-group">
					<?php echo $form->labelEx($model,'industry', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<input id="NumberTrain_industry" class="form-control" type="text" value="<?php echo $industry?>" name="NumberTrain[industry]" readonly="readonly">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label" for="Enterprise_member_id">地区</label>
				    <div class="col-sm-8">
						<label style="padding:7px 20px 0 0"  for="Enterprise_province" ><?php echo $areas['province']; ?></label>	
						<label style="padding:7px 20px 0 0"  for="Enterprise_city" ><?php echo $areas['city']; ?></label>
						<label for="Enterprise_area"  style="padding:7px 20px 0 0" ><?php echo $areas['area']; ?></label>
					</div>
				</div>
				
				<div class="form-group">
					<?php echo $form->labelEx($model,'lat', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'lat', array('class'=>'form-control', readonly => "readonly")); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'lng', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'lng', array('class'=>'form-control', readonly => "readonly")); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'address', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textArea($model,'address',array('class'=>'form-control','rows'=>'6','cols'=>'50', readonly => "readonly")); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'tag', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textArea($model,'tag',array('class'=>'form-control','rows'=>'6','cols'=>'50', readonly => "readonly")); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'description', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textArea($model,'description',array('class'=>'form-control','rows'=>'6','cols'=>'50', readonly => "readonly")); ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $form->labelEx($model,'istop', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<input id="NumberTrain_istop" class="form-control" type="text" value="<?php echo $model->istop == 1 ? "是" : "否"?>" name="NumberTrain[istop]" readonly = "readonly">
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'status', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php  if($model->is_close == 1){echo "已关闭";}else{echo $form->radioButtonList($model,'status', array('0'=>'启用', '1'=>'屏蔽'/*, '2'=>'禁用2周', '3'=>'禁用1个月', '4'=>'禁用3个月', '5'=>'无限期'*/), 
												array('separator'=>'', 'checked'=>'checked', 'style'=>'float:left;', 'template'=>'{input} {label}',
												'labelOptions'=>array('style'=>'float:left;display:inline;margin-right:40px;')));}?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label" for="Member_reason">	启/禁用原因</label>
					<div class="col-sm-8">
						<textarea <?php if($model->is_close == 1){echo 'readonly="readonly"';}?>id="Member_reason" class="form-control" name="NumberTrain[reason]" cols="50" rows="6"><?php echo $reason?></textarea>
					</div>
				</div>
				<div class="form-group">
					<label for="Enterprise_member_id" class="col-sm-2 control-label">创建人是否禁用</label>
					<div class="col-sm-8">
						<span id="Enterprise_status">
						<?php foreach ($status as $key=>$value){?>						
							<input <?php if($status2 == $key && isset($status2)) echo 'checked = "checked"';?> style="float:left;" id="Enterprise_status2_<?php echo $key?>" value="<?php echo $key?>" type="radio" name="NumberTrain[status2]"> 
							<label style="float:left;display:inline;margin-right:40px;" for="Enterprise_status_0"><?php echo $value?></label>
						<?php }?>							
						</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label" for="Member_reason">	启/禁用原因</label>
					<div class="col-sm-8">
						<textarea id="Member_reason2" class="form-control" name="NumberTrain[reason2]" cols="50" rows="6"><?php echo $reason2?></textarea>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'created_time', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'created_time', array('class'=>'form-control', readonly => "readonly")); ?>
					</div>
				</div>
				

			<div class="form-group form-group-center">
				<div class="btn btn-success btn-lg btn-lg1" type="">　确定　</div>
				<a class="btn btn-default btn-lg backurl" type="button" goback="<?php echo $_GET['back'] ? $_GET['back'] : -1;?>">　取消　</a>
			</div>

		<?php
 $this->endWidget (); ?>
	</div>
</div>
<script>
 var input0 = $("input[name='NumberTrain[status]']:checked").val();
 var inputa0 = $("input[name='NumberTrain[status2]']:checked").val();
 $(".btn-lg1").click(function(){
	     var input = $("input[name='NumberTrain[status]']:checked").val();
	     var inputa = $("input[name='NumberTrain[status2]']:checked").val();	
		 var text = $("#Member_reason").val();
		 var text2 = $("#Member_reason2").val();
		 if((input != 0 && text == "") || (inputa != 0 && text2 == "")){
		      alert("请输入禁用原因");	
		      return false;
	     }else{
		     if((input0 != input) || (inputa0 != inputa)){
		    	 if(window.confirm('请确认对以上信息进行修改')){
		    		 $("#number-train-form").submit();
		    		return true;
		    		}else{
		    		return false;
		    		} 
			 }	    	
		     $("#number-train-form").submit();
              return true;
		     }
	 });
 
</script>

