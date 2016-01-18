<?php
	Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/themes/css/content.css");
?>
<?php
/* @var $this FriendLeagueController */
/* @var $model FriendLeague */
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
		<li><a href="<?php echo Yii::app()->createUrl('friendLeague/index')?>">好友联盟管理</a></li>
		<li><a href="javascript:void(0)">好友联盟编辑</a></li>
		<div class="main_right_content_content_block_action_add">
			<a class="btn btn-success backurl" href="javascript:void(0)" goback="<?php echo $_GET['back'] ? $_GET['back'] : -1;?>">返回</a>
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
			'id'=>'friend-league-form',
			'htmlOptions'=>array('enctype'=>'multipart/form-data', 'class'=>'form-horizontal'),
			'enableAjaxValidation'=>false,
		)); ?>
				
				<div class="form-group">
					<?php echo $form->labelEx($model,'name', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<label style="padding-top:7px;"><?php echo $model->name;?></label>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label" for="FriendLeague_member_id">地区</label>
				    <div class="col-sm-8">
						<label style="padding:7px 20px 0 0"  for="Member_province" ><?php echo $areas['province']; ?></label>	
						<label style="padding:7px 20px 0 0"  for="Member_province" ><?php echo $areas['city']; ?></label>
						<label for="Member_province"  style="padding:7px 20px 0 0" ><?php echo $areas['area']; ?></label>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'description', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textArea($model,'description',array('class'=>'form-control','rows'=>'6','cols'=>'50','readonly' => 'readonly')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'member_id', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<label style="padding-top:7px;"><?php echo $model->member_id;?></label>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label" for="FriendLeague_member_id">奔犇号</label>
					<div class="col-sm-8">
						<label style="padding-top:7px;"><?php echo $additional['benben_id'];?></label>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label" for="FriendLeague_member_id">手机号</label>
					<div class="col-sm-8">
						<label style="padding-top:7px;"><?php echo $additional['phone'];?></label>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'number', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<label style="padding-top:7px;"><?php echo $model->number;?></label>
					</div>
				</div>
				
				
				<div class="form-group">
					<label class="col-sm-2 control-label" for="FriendLeague_member_id">联盟公告</label>
					<div class="col-sm-8">
						<?php echo $form->textArea($model,'announcement',array('class'=>'form-control','rows'=>'6','cols'=>'50','readonly' => 'readonly')); ?>
					</div>
				</div>

				<div class="form-group">
					<label class="col-sm-2 control-label" for="FriendLeague_member_id">状态</label>
					<div class="col-sm-8">
						<?php  if($model->is_delete){echo '<label style="padding-top:7px;">解散</label>';}else{echo $form->radioButtonList($model,'status', array('0'=>'启用', '1'=>'屏蔽'/*, '2'=>'禁用2周', '3'=>'禁用1个月', '4'=>'禁用3个月', '5'=>'无限期'*/), 
												array('separator'=>'', 'checked'=>'checked', 'style'=>'float:left;', 'template'=>'{input} {label}',
												'labelOptions'=>array('style'=>'float:left;display:inline;margin-right:40px;')));}?>
					</div>
				</div>
				<div <?php if($model->is_delete){echo 'style="display:none"';}?> class="form-group">
					<label class="col-sm-2 control-label" for="Member_reason">	启/禁用原因</label>
					<div class="col-sm-8">
						<textarea id="Member_reason" class="form-control" name="FriendLeague[reason]" cols="50" rows="6"><?php echo $reason1;//if($info)  echo $info[0]['reason'];?></textarea>
					</div>
				</div>
				<div class="form-group">
					<label for="Enterprise_member_id" class="col-sm-2 control-label">创建人是否禁用</label>
					<div class="col-sm-8">
						<span id="Enterprise_status">
						<?php foreach ($status as $key=>$value){?>						
							<input <?php if($status2 == $key && isset($status2)) echo 'checked = "checked"';?> style="float:left;" id="Enterprise_status2_<?php echo $key?>" value="<?php echo $key?>" type="radio" name="FriendLeague[status2]"> 
							<label style="float:left;display:inline;margin-right:40px;" for="Enterprise_status_0"><?php echo $value?></label>
						<?php }?>							
						</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label" for="Member_reason">	启/禁用原因</label>
					<div class="col-sm-8">
						<textarea id="Member_reason2" class="form-control" name="FriendLeague[reason2]" cols="50" rows="6"><?php echo $reason2?></textarea>
					</div>
				</div>
				<!--<div class="form-group">
					<label class="col-sm-2 control-label" for="Member_reason">	启/禁用记录</label>
					<div class="col-sm-8">
					  <ul style="padding-top: 6px">
						<?php
						if($info){
                            foreach ($info as $val){
						?>
						    <li style="height: 25px"><?php echo date("Y-m-d H:i:s",$val['created_time'])?> &nbsp;&nbsp;<?php echo $val['status']?></li>
						<?php
						  }	
						}else{
							echo "没有记录";
						} 
						?>
						</ul>
					</div>
				</div>-->
				
				<div class="form-group">
					<?php echo $form->labelEx($model,'created_time', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<label style="padding-top:7px;"><?php echo $model->created_time;?></label>
						
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
 var input0 = $("input[name='FriendLeague[status]']:checked").val();
 var inputa0 = $("input[name='FriendLeague[status2]']:checked").val();
 $(".btn-lg1").click(function(){
	     var input = $("input[name='FriendLeague[status]']:checked").val();
	     var inputa = $("input[name='FriendLeague[status2]']:checked").val();	
		 var text = $("#Member_reason").val();
		 var text2 = $("#Member_reason2").val();
		 if((input != 0 && text == "") || (inputa != 0 && text2 == "")){
		      alert("请输入禁用原因");	
		      return false;
	     }else{
		     if((input0 != input) || (inputa0 != inputa)){
		    	 if(window.confirm('请确认对以上信息进行修改')){
		    		 $("#friend-league-form").submit();
		    		return true;
		    		}else{
		    		return false;
		    		} 
			 }	    	
		     $("#friend-league-form").submit();
              return true;
		     }
	 });
 
</script>
