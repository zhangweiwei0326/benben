<?php
	Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/themes/css/content.css");
?>
<?php
/* @var $this FriendController */
/* @var $model Friend */
/* @var $form CActiveForm */
?>




<div class="main_right_content">
	<ol class="breadcrumb main_right_content_breadcrumb">
		<li><a href="<?php echo Yii::app()->createUrl('friend/index')?>">朋友圈管理</a></li>
		<li><a href="javascript:void(0)">编辑朋友圈</a></li>
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
			'id'=>'creation-form',
			'htmlOptions'=>array('enctype'=>'multipart/form-data', 'class'=>'form-horizontal'),
			'enableAjaxValidation'=>false,
		)); ?>
				<div class="form-group">
					<?php echo $form->labelEx($model,'member_id', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<input id="Friend_member_id" class="form-control" type="text" value="<?php echo $index['member']?>" name="Friend[member_id]" readonly="readonly">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label" for="Friend_member_id">奔犇号</label>
					<div class="col-sm-8">
						<input id="Friend_member_id" class="form-control" type="text" value="<?php echo $index['benben_id']?>" name="Friend[member_id]" readonly="readonly">
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'member_phone', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<input id="Creation_member_phone" class="form-control" type="text" value="<?php echo $index['member_phone']?>" name="Friend[member_phone]" readonly="readonly">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label" for="Friend_description">帖子内容</label>
					<div class="col-sm-8">
						<?php echo $form->textArea($model,'description',array('class'=>'form-control','rows'=>'6','cols'=>'50', readonly => "readonly")); ?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label" for="Friend_status">帖子状态</label>
					<div class="col-sm-8">
						<?php  if($model->is_delete == 1){echo "已删除";}else{echo $form->radioButtonList($model,'status', array('0'=>'正常', '1'=>'屏蔽'), 
												array('separator'=>'', 'checked'=>'checked', 'style'=>'float:left;', 'template'=>'{input} {label}',
												'labelOptions'=>array('style'=>'float:left;display:inline;margin-right:40px;')));}?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label" for="Member_reason">屏蔽原因</label>
					<div class="col-sm-8">
						<textarea <?php if($model->is_delete == 1){echo 'readonly="readonly"';}?> id="Member_reason" class="form-control" name="Friend[reason]" cols="50" rows="6"><?php echo $index['reason']?></textarea>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'type', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php 
						
							if($model->type == '图文'){
								echo '<input id="Creation_type" class="form-control" type="text" value="图文" readonly="readonly">';
								foreach ($index['actire'] as $value){
						?>
							<img src="<?php echo $value?>" style="width:200px;margin-top:5px;margin-right:5px;">
						<?php }?>
						<?php }else{?>
					
							<input id="Creation_type" class="form-control" type="text" value="音频|地址:<?php echo $index['video']?>" name="Creation[type]" style="width:90%;float:left;margin-right:20px;" readonly="readonly">
							<a class="btn btn-success backurl" href="javascript:void(0)" goback="<?php echo $_GET['back'] ? $_GET['back'] : -1;?>">查看</a>
						<?php }?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'views', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'views', array('class'=>'form-control', readonly => "readonly")); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'goods', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'goods', array('class'=>'form-control', readonly => "readonly")); ?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label" for="Friend_comment">评论数</label>					
					<div class="col-sm-8">
						<input class="form-control" readonly="readonly" name="Friend[comment]" id="Friend_comment" type="text" value="<?php echo $index['comment']?>"></div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label" for="Friend_created_time">发帖时间</label>
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
 var input0 = $("input[name='Friend[status]']:checked").val();
 $(".btn-lg1").click(function(){
	     var input = $("input[name='Friend[status]']:checked").val();	
		 var text = $("#Member_reason").val();
		 if(input != 0 && text == ""){
		      alert("请输入屏蔽原因");	
		      return false;
	     }else{
		     if(input0 != input){
		    	 if(window.confirm('请确认对以上信息进行修改')){
		    		 $("#creation-form").submit();
		    		return true;
		    		}else{
		    		return false;
		    		} 
			 }	    	
		     $("#creation-form").submit();
              return true;
		     }
	 });
 
</script>

