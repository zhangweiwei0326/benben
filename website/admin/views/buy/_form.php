<?php
	Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/themes/css/content.css");
?>
<?php
/* @var $this BuyController */
/* @var $model Buy */
/* @var $form CActiveForm */
?>




<div class="main_right_content">
	<ol class="breadcrumb main_right_content_breadcrumb">
		<li><a href="<?php echo Yii::app()->createUrl('buy/index')?>">我要买管理</a></li>
		<li><a href="javascript:void(0)">编辑我要买</a></li>
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
			'id'=>'buy-form',
			'htmlOptions'=>array('enctype'=>'multipart/form-data', 'class'=>'form-horizontal'),
			'enableAjaxValidation'=>false,
		)); ?>

				<div class="form-group">
					<?php echo $form->labelEx($model,'member_id', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<input id="Buy_member_id" class="form-control" type="text" value="<?php echo $member['name']?$member['name']:$member['nick_name']; ?>" name="Buy[member_id]" readonly="readonly">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label" for="Buy_benben_id">奔犇号</label>
					<div class="col-sm-8">
						<input id="Buy_benben_id" class="form-control" type="text" value="<?php echo  $member['benben_id']; ?>" name="Buy[member_id]" readonly="readonly">
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'member_phone', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<input id="Buy_member_phone" class="form-control" type="text" value="<?php echo $model->member_phone ?>" name="Buy[member_phone]" readonly="readonly">
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'地区', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<input id="Buy_member_phone" class="form-control" type="text" value="<?php echo $areaInfo[$memberInfo->province]." ".$areaInfo[$memberInfo->city]." ".$areaInfo[$memberInfo->area] ?>" name="Buy[member_phone]" readonly="readonly">
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'status', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php
						$statusInfo = array('0'=>'启用', '1'=>'禁用1周', '2'=>'禁用2周', '3'=>'禁用1个月', '4'=>'禁用3个月', '5'=>'无限期');
						foreach($statusInfo as $k => $e){
							echo '<input ';
							if ($member->buy_disable == $k) {
								echo 'checked="checked" ';
							}
							echo 'style="float:left;" id="Member_status_0" value="'.$k.'" type="radio" name="Member[status]">
							<label style="float:left;display:inline;margin-right:40px;" for="Member_status_'.$k.'">'.$e.'</label>';
						}
						?>
			
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label" for="Member_reason">	启/禁用原因</label>
					<div class="col-sm-8">
						<textarea id="Member_reason" class="form-control" name="Member[reason]" cols="50" rows="6"><?php echo $memberreason;?></textarea>
					</div>
				</div>

				
				<div class="form-group">
					<label class="col-sm-2 control-label" for="Buy_title">我要买标题</label>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'title',array('class'=>'form-control','size'=>'60','maxlength'=>'255', readonly => "readonly")); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'amount', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'amount',array('class'=>'form-control','size'=>'10','maxlength'=>'10', readonly => "readonly")); ?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label" for="Buy_description">购买描述</label>
					<div class="col-sm-8">
						<?php echo $form->textArea($model,'description',array('class'=>'form-control','rows'=>'6','cols'=>'50', readonly => "readonly")); ?>
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-sm-2 control-label" for="Buy_status">帖子状态</label>
					<div class="col-sm-8">
						<?php  echo $form->radioButtonList($model,'status', array('0'=>'正常', '1'=>'屏蔽'), 
												array('separator'=>'', 'checked'=>'checked', 'style'=>'float:left;', 'template'=>'{input} {label}',
												'labelOptions'=>array('style'=>'float:left;display:inline;margin-right:40px;')));?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label" for="Member_reason">屏蔽原因</label>
					<div class="col-sm-8">
						<textarea id="Member_reason1" class="form-control" name="Buy[reason]" cols="50" rows="6"><?php echo $reason?></textarea>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'quoted_number', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<input id="Buy_quoted_number" class="form-control" type="text" value="<?php echo $model->quoted_number?>" name="Buy[quoted_number]" readonly = "readonly" style="float:left;width:90%;margin-right:10px;">
						<a class="btn btn-success backurl"  href="<?php echo Yii::app()->createUrl('quote/index?id='.$model->id)?>">查看</a>
					</div>
					
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'deadline', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'deadline', array('class'=>'form-control', readonly => "readonly")); ?>
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
var input0 = $("input[name='Buy[status]']:checked").val();
var inputa0 = $("input[name='Member[status]']:checked").val();
$(".btn-lg1").click(function(){
	     var input = $("input[name='Buy[status]']:checked").val();
	     var inputa = $("input[name='Member[status]']:checked").val();	
		 var text = $("#Member_reason1").val();
		 var text2 = $("#Member_reason").val();
		 if((input != 0 && text == "") || (inputa != 0 && text2 == "")){
		      alert("请输入屏蔽原因");	
		      return false;
	     }else{
		     if((input0 != input) || (inputa0 != inputa)){
		    	 if(window.confirm('请确认对以上信息进行修改')){
		    		 $("#buy-form").submit();
		    		return true;
		    		}else{
		    		return false;
		    		} 
			 }	    	
		     $("#buy-form").submit();
             return true;
		     }
	 });
 
</script>
