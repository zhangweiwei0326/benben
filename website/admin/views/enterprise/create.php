<?php
	Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/themes/css/content.css");
?>
<?php
/* @var $this EnterpriseController */
/* @var $model Enterprise */
/* @var $form CActiveForm */
?>

<style type="text/css">.enterprise-member-confirm{background: #FFF;}</style>

<div class="main_right_content">
	<ol class="breadcrumb main_right_content_breadcrumb">
		<li><a href="<?php echo Yii::app()->createUrl('Enterprise/index')?>">政企通讯录管理</a></li>
		<li><a href="#">创建政企</a></li>
		<div class="main_right_content_content_block_action_add">
			<a class="btn btn-success backurl" href="<?php echo Yii::app()->createUrl('Enterprise/download')?>" >成员模板</a>
			<a class="btn btn-success backurl" href="javascript:history.go(-1)" >返回</a>
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
			'id'=>'enterprise-form',
			'htmlOptions'=>array('enctype'=>'multipart/form-data', 'class'=>'form-horizontal'),
			'enableAjaxValidation'=>false,
		)); ?>
				
				<div class="form-group">
					<?php echo $form->labelEx($model,'name', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'name',array('class'=>'form-control','size'=>'60','maxlength'=>'255')); ?>
						<?php echo $form->textField($model,'origin',array('class'=>'form-control','style'=>'display:none','value'=>'2')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'type', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<select class="form-control" name="Enterprise[type]">
							<option value="1">政企网</option>
							<option value="2" <?php if($model->type == 2) echo 'selected="selected"';?>>虚拟网</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label" for="Enterprise_member_id">地区</label>
				    <div class="col-sm-2">
						<select class="form-control" name="Enterprise[province]" id="province">
								<option value="-1">--请选择--</option>
									<?php foreach ($province as $prv){?>
										<option value="<?php echo $prv->bid?>"
											<?php if($model->province == $prv->bid) echo 'selected = "selected"';?>><?php echo $prv->area_name?></option>
									<?php  }?>
								</select>
					</div>
					<div class="col-sm-2">
						<select class="form-control" name="Enterprise[city]" id="city">
								<option value="-1">--请选择市--</option>
								<?php if(isset($res)) {foreach ($res as $prv){?>
									<option value="<?php echo $prv['bid'];?>"
									<?php if($model->city == $prv['bid']) echo 'selected = "selected"';?>><?php echo $prv['area_name']?></option>
								<?php  }}?>
							</select>
					</div>
					<div class="col-sm-2">
						<select class="form-control" name="Enterprise[area]" id="area">
								<option value="-1">--请选择区--</option>
								<?php if(isset($res2)) {foreach ($res2 as $prv){?>
									<option value="<?php echo $prv['bid'];?>"
									<?php if($model->area == $prv['bid']) echo 'selected = "selected"';?>><?php echo $prv['area_name']?></option>
								<?php  }}?>
							</select>
					</div>
				</div>

	
				<div class="form-group">
					<?php echo $form->labelEx($model,'description', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textArea($model,'description',array('class'=>'form-control','rows'=>'6','cols'=>'50')); ?>
					</div>
				</div>


				
				<?php
					if($info){
						echo "<table class='table table-hover'> <tr><td>编号</td><td>姓名</td><td>手机号</td><td>其它号码</td></tr>";

						foreach($info  as $key => $each){
							echo '<tr class="enterprise-member-confirm"><td>'.($key+1).'</td><td>'.$each['name'].'</td><td>'.$each['phone'].'</td><td>'.$each['other'].'</td></tr>';
						}
						echo "</table>";
						echo '<input type="hidden" value=\''.json_encode($info).'\' name="info_json"/>';
					}else{
						echo '<div class="form-group">
					<label class="col-sm-2 control-label" for="Enterprise_member_id">上传成员</label>
					<div class="col-sm-8">
						<input name="inputfile" type="file" value="1"/>
					</div>
				</div>';
					}
					?>

				

			<div class="form-group form-group-center">
				<button class="btn btn-success btn-lg btn-lg1" type="submit">　确定　</button>
				<a class="btn btn-default btn-lg backurl" type="button" href="javascript:history.go(-1)" >　取消　</a>
			</div>

		<?php
 $this->endWidget (); ?>
	</div>
</div>



