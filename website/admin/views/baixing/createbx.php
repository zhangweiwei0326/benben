<?php
Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/themes/css/content.css");
?>
<?php
/* @var $this BaixingController */
/* @var $model Enterprise */
/* @var $form CActiveForm */
?>

<div class="main_right_content">
    <ol class="breadcrumb main_right_content_breadcrumb">
        <li><a href="<?php echo Yii::app()->createUrl('baixing/newbx')?>">百姓网新增</a></li>
        <li><a href="javascript:void(0)">新建百姓</a></li>
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
            'action'=>Yii::app()->createUrl('baixing/setbx'),
            'htmlOptions'=>array('enctype'=>'multipart/form-data', 'class'=>'form-horizontal'),
            'enableAjaxValidation'=>false,
        )); ?>

        <div class="form-group">
            <?php echo $form->labelEx($model,'name', array("class"=>"col-sm-2 control-label"));?>
            <div class="col-sm-8">
                <?php echo $form->textField($model,'name',array('class'=>'form-control','size'=>'11','maxlength'=>'11')); ?>
            </div>
        </div>
        <div class="form-group">
            <?php echo $form->labelEx($model,'description', array("class"=>"col-sm-2 control-label"));?>
            <div class="col-sm-8">
                <?php echo $form->textArea($model,'description',array('class'=>'form-control','cols'=>'50','rows'=>'6')); ?>
            </div>
        </div>

        <div class="form-group">
            <?php echo $form->labelEx($model,'province', array("class"=>"col-sm-2 control-label"));?>
            <div style="width:126px" class="col-sm-8">
                <select class="form-control" name="Enterprise[province]" id="province">
                    <option value="0">请选择</option>
                    <?php foreach ($province['province'] as $prv){?>
                        <option value="<?php echo $prv->bid?>"  <?php if($model->province == $prv->bid) echo 'selected = "selected"';?>><?php echo $prv->area_name?></option>
                    <?php  }?>
                </select>
                <?php //echo $form->listBox($model,'province',array(0=>$areas['province']),array('class'=>'form-control','size'=>1)); ?>
            </div>
            <?php echo $form->labelEx($model,'city', array("class"=>"col-sm-12 control-label","style"=>"width: 60px;"));?>
            <div style="width:126px" class="col-sm-8">
                <select class="form-control" name="Enterprise[city]" id="city">
                    <option value="0">请选择</option>
                    <?php foreach ($province['city'] as $prv){?>
                        <option value="<?php echo $prv['bid']?>"  <?php if($model->city == $prv['bid']) echo 'selected = "selected"';?>><?php echo $prv['area_name']?></option>
                    <?php  }?>
                </select>
                <?php //echo $form->listBox($model,'city',array(0=>$areas['city']),array('class'=>'form-control','size'=>1)); ?>
            </div>
            <?php echo $form->labelEx($model,'area', array("class"=>"col-sm-12 control-label","style"=>"width: 60px;"));?>
            <div style="width:126px" class="col-sm-8">
                <select class="form-control" name="Enterprise[area]" id="area">
                    <option value="0">请选择</option>
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

        <div class="form-group form-group-center">
            <button class="btn btn-success btn-lg btn-lg1" type="submit">　确定　</button>
            <a class="btn btn-default btn-lg backurl" href="javascript:goBack()" data="<?php echo $_GET['back'] ? $_GET['back'] : -1;?>">　取消　</a>
        </div>

        <?php
        $this->endWidget (); ?>
    </div>
</div>
<script>
    var name0 = $("#Enterprise_name").val();
    $(".btn-lg1").click(function(){
        var name = $("#Enterprise_name").val();
        if (name0 !=name && name!=undefined && name){
            if(window.confirm('请确认对以上信息进行保存')){
                $("#bxapply-form").submit();
                return true;
            }else{
                return false;
            }
        }else{
            alert("请输入百姓网名称！");
            return false;
        }
    });

</script>

