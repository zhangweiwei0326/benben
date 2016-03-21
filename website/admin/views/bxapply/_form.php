<?php
	Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/themes/css/content.css");
?>
<?php
/* @var $this BxapplyController */
/* @var $model Bxapply */
/* @var $form CActiveForm */
?>




<div class="main_right_content">
	<ol class="breadcrumb main_right_content_breadcrumb">
		<li><a href="<?php echo Yii::app()->createUrl('bxapply/index')?>">百姓网管理</a></li>
		<li><a href="javascript:void(0)">编辑申请</a></li>
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
			'id'=>'bxapply-form',
			'htmlOptions'=>array('enctype'=>'multipart/form-data', 'class'=>'form-horizontal'),
			'enableAjaxValidation'=>false,
		)); ?>
				
				<div class="form-group">
					<?php echo $form->labelEx($model,'phone', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'phone',array('class'=>'form-control','size'=>'11','maxlength'=>'11',  'readonly' => readonly)); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'short_phone', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'short_phone',array('class'=>'form-control','size'=>'20','maxlength'=>'6')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'name', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'name',array('class'=>'form-control','size'=>'20','maxlength'=>'20',)); ?>
					</div>
				</div>
				 <div class="form-group">
					<?php echo $form->labelEx($model,'province', array("class"=>"col-sm-2 control-label"));?>
					<div style="width:126px" class="col-sm-8">
					<select class="form-control" name="Bxapply[province]" id="province">
						<option value="0">未知</option>
					<?php foreach ($province['province'] as $prv){?>
						<option value="<?php echo $prv->bid?>"  <?php if($model->province == $prv->bid) echo 'selected = "selected"';?>><?php echo $prv->area_name?></option>
					<?php  }?>
					</select>
						<?php //echo $form->listBox($model,'province',array(0=>$areas['province']),array('class'=>'form-control','size'=>1)); ?>
					</div>
					<?php echo $form->labelEx($model,'city', array("class"=>"col-sm-12 control-label","style"=>"width: 60px;"));?>
					<div style="width:126px" class="col-sm-8">
					<select class="form-control" name="Bxapply[city]" id="city">
						<option value="0">未知</option>
					<?php foreach ($province['city'] as $prv){?>			
						<option value="<?php echo $prv['bid']?>"  <?php if($model->city == $prv['bid']) echo 'selected = "selected"';?>><?php echo $prv['area_name']?></option>
					<?php  }?>
					</select>
						<?php //echo $form->listBox($model,'city',array(0=>$areas['city']),array('class'=>'form-control','size'=>1)); ?>
					</div>
					<?php echo $form->labelEx($model,'area', array("class"=>"col-sm-12 control-label","style"=>"width: 60px;"));?>
					<div style="width:126px" class="col-sm-8">
					<select class="form-control" name="Bxapply[area]" id="area">
						<option value="0">未知</option>
					<?php foreach ($province['area'] as $prv){?>
						<option value="<?php echo $prv['bid']?>"  <?php if($model->area == $prv['bid']) echo 'selected = "selected"';?>><?php echo $prv['area_name']?></option>
					<?php  }?>
					</select>
						<?php //echo $form->listBox($model,'area',array(0=>$areas['area']),array('class'=>'form-control','size'=>1)); ?>
					</div>
					<!--<?php echo $form->labelEx($model,'street', array("class"=>"col-sm-12 control-label","style"=>"width: 60px;"));?>
					<div style="width:126px" class="col-sm-8">
					<select class="form-control" name="Bxapply[street]" id="street">
						<option value="0">未知</option>
					<?php foreach ($province['street'] as $prv){?>
						<option value="<?php echo $prv['bid']?>"  <?php if($model->street == $prv['bid']) echo 'selected = "selected"';?>><?php echo $prv['area_name']?></option>
					<?php  }?>
					</select>
						<?php //echo $form->listBox($model,'street',array(0=>$areas['street']),array('class'=>'form-control','size'=>1)); ?>
					</div>-->
				</div>
				<!-- <div class="form-group">
				    <div style="width:192px;text-align: right;" class="col-sm-8">
				        <label for="Member_province" >地区</label>
				    </div>
					<div style="width:126px" class="col-sm-8">
						<label for="Member_province" ><?php echo $areas['province']; ?></label>
					</div>
					<div style="width:126px" class="col-sm-8">
					<label for="Member_province" ><?php echo $areas['city']; ?></label>
					</div>
					<div style="width:126px" class="col-sm-8">
					<label for="Member_province" ><?php echo $areas['area']; ?></label>
					</div>
					<div style="width:126px" class="col-sm-8">
					<label for="Member_province" ><?php echo $areas['street']; ?></label>
					</div>					
				</div> -->
				
				<div class="form-group">
					<label class="col-sm-2 control-label" for="Bxapply_card_id">身份证号</label>
					<div class="col-sm-8">
						<input id="Bxapply_card_id" class="form-control" type="text" value="<?php echo $id_card?>" name="Bxapply[id_card]">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label" for="Bxapply_member_id">身份证照片</label>
					<div class="col-sm-8">
						<img src="<?php echo $item->poster ? $item->poster : Yii::app()->request->baseUrl.$poster1;?>" width="300" height="300">
						<img src="<?php echo $item->poster ? $item->poster : Yii::app()->request->baseUrl.$poster2;?>" width="300" height="300">
					</div>
				</div>
				
				<div class="form-group">
					<?php echo $form->labelEx($model,'status', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php  echo $form->radioButtonList($model,'status', array('0'=>'等待审核', '1'=>'未通过', '2'=>'退回重申', '3'=>'已经通过', '4'=>'已撤销'), 
												array('separator'=>'', 'checked'=>'checked', 'style'=>'float:left;', 'template'=>'{input} {label}',
												'labelOptions'=>array('style'=>'float:left;display:inline;margin-right:45px;')));?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label" for="Member_reason">审核原因</label>
					<div class="col-sm-8">
						<textarea id="Member_reason" class="form-control" name="Bxapply[reason]" cols="50" rows="6"><?php echo $reason?></textarea>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'member_id', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<input id="Bxapply_member_id" class="form-control" type="text" value="<?php if($member_phone == $model->phone)  echo $model->name; else  echo $member_name;?>" name="Bxapply[member_id]" readonly="readonly">
					</div>
				</div>
				<div class="form-group">
					<label for="Bxapply_phone" class="col-sm-2 control-label">提交人手机号码</label>					
					<div class="col-sm-8">
						<input id="Bxapply_member_id" class="form-control" type="text" value="<?php echo $member_phone;?>" name="member[phone]" readonly="readonly">				
						</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'created_time', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'created_time', array('class'=>'form-control',   'readonly' => readonly)); ?>
					</div>
				</div>
				

			<div class="form-group form-group-center">
				<button class="btn btn-success btn-lg btn-lg1" type="submit">　确定　</button>
				<a class="btn btn-default btn-lg backurl" href="javascript:goBack()" data="<?php echo $_GET['back'] ? $_GET['back'] : -1;?>">　取消　</a>
			</div>

		<?php
 $this->endWidget (); ?>
	</div>
</div>
<script>
 var shortphone0 = $("#Bxapply_short_phone").val();
 var input0 = $("input[name='Bxapply[status]']:checked").val();
 var name = $("input[name='Bxapply[name]']").val();
 var id_card = $("input[name='Bxapply[id_card]']").val();
 var province = $("select[name='Bxapply[province]']").val();
 var city = $("select[name='Bxapply[city]']").val();
 var area = $("select[name='Bxapply[area]']").val();
 var street = $("select[name='Bxapply[street]']").val();
 $(".btn-lg1").click(function(){
	     var shortphone2 = $("#Bxapply_short_phone").val();
	     var shortphone = $("#Bxapply_short_phone").val();
	     var name1 = $("input[name='Bxapply[name]']").val();		 
		 var id_card1 = $("input[name='Bxapply[id_card]']").val();
		 var province1 = $("select[name='Bxapply[province]']").val();
		 var city1 = $("select[name='Bxapply[city]']").val();
		 var area1 = $("select[name='Bxapply[area]']").val();
		 var street1 = $("select[name='Bxapply[street]']").val();
	     // shortphone = parseInt(shortphone);
	     var input = $("input[name='Bxapply[status]']:checked").val();	
		 var text = $("#Member_reason").val();
		 if((input != 0 && input != 3) && text == ""){
		      alert("请输入审核原因");	
		      return false;
	     }else if(input == 3 && shortphone.length != 6){
	     	alert("请输入百姓网号码且必须为6位数字");	
		    return false;
	     }else if(input != 3 && shortphone.length  > 0){
		    alert("请先删除百姓网号");	
			return false;
		 }else{
		     if((input0 != input) || (shortphone0 !=shortphone)|| (name != name1) ||
		    		 (id_card != id_card1) || (province != province1) || (city != city1) || (area != area1) || (street != street1)){
		    	 if(window.confirm('请确认对以上信息进行修改')){
		    		 $("#bxapply-form").submit();
		    		return true;
		    		}else{
		    		return false;
		    		} 
			 }	    	
		     $("#bxapply-form").submit();
              return true;
		     }
	 });

 $('#Bxapply_short_phone').keydown(function(){
 	var shortphone = $("#Bxapply_short_phone").val();
 	if (shortphone.length == 6) {
 		$("input[name='Bxapply[status]']").eq(4).attr("checked",'true');
 	};
 })
 
</script>

