<div class="main_right_content">
	<ol class="breadcrumb main_right_content_breadcrumb">
		<li><a href="<?php  echo Yii::app()->createUrl("numberTrain/index");?>">个人密码管理</a></li>
		
	</ol>
	<div class="main_right_content_content">
			<?php if($msg) {?>
				<div class="alert alert-danger alert-dismissible" role="alert">
				  <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				  <strong><?php if($result){echo '恭喜！';}else{echo "警告！";}?></strong> <?php echo $msg;?>
				</div>
				<?php }?>
				
				
	<?php $form=$this->beginWidget('CActiveForm', array(
			'id'=>'number-train-form',
			'htmlOptions'=>array('enctype'=>'multipart/form-data', 'class'=>'form-horizontal'),
			'enableAjaxValidation'=>false,
		)); ?>
		<div class="form-group">
					<label class="col-sm-2 control-label" for="Creation_member_id">旧密码</label>
					<div class="col-sm-8">
						<input id="Creation_member_id0" class="form-control" type="password" name="oldpassword" >
					</div>
		</div>
		<div class="form-group">
					<label class="col-sm-2 control-label" for="Creation_member_id">新密码</label>
					<div class="col-sm-8">
						<input id="Creation_member_id1" class="form-control" type="password" name="password" >
					</div>
		</div>
		<div class="form-group">
					<label class="col-sm-2 control-label" for="Creation_member_id">确认密码</label>
					<div class="col-sm-8">
						<input id="Creation_member_id2" class="form-control" type="password" name="repassword" >
					</div>
		</div>
		<div class="form-group form-group-center">
			
			
				<button class="btn btn-success btn-lg" type="">　确定　</button>
			
				<a class="btn btn-default btn-lg backurl" type="button" goback="-1">　取消　</a>
			</div>
		<?php
 $this->endWidget (); ?>
	</div>
</div>
<script>
 $(".btn-success").click(function(){
	     var input0 = $("#Creation_member_id0").val();
	     var input1 = $("#Creation_member_id1").val();
	     var input2 = $("#Creation_member_id2").val();			 			 
		 if((input0 == "") || (input1 == "") || (input2 == "")){
		      alert("请输入旧密码、新密码或确认密码");	
		      return false;
	     }else{
		     if((input1 != input2)){
		    	alert("新密码和确认密码不一致");
		    	return false;
			 }
		     if(window.confirm('请确认对以上信息进行修改')){
	    		 $("#number-train--form").submit();
	    		return true;
	    		}else{
	    		return false;
	    		} 	    			    
		     }
	 });
 
</script>