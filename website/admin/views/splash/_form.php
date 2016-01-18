<?php
	Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/themes/css/content.css");

//	Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/themes/js/jquery.iframe-transport.js");
//	Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/themes/js/jquery.fileupload.js");
?>

<?php
/* @var $this SplashController */
/* @var $model Splash */
/* @var $form CActiveForm */
?>




<div class="main_right_content">
	<ol class="breadcrumb main_right_content_breadcrumb">
		<li><a href="<?php echo Yii::app()->createUrl('splash/index')?>">开机页面管理</a></li>
		<li><a href="javascript:void(0)">编辑开机页面</a></li>
		<div class="main_right_content_content_block_action_add">
			<a class="btn btn-success backurl" href="javascript:void(0)" goback="<?php echo $back ? $back : -1;?>">返回</a>
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
			'id'=>'splash-form',
			'htmlOptions'=>array('enctype'=>'multipart/form-data', 'class'=>'form-horizontal'),
			'enableAjaxValidation'=>false,
		)); ?>
				
				<div class="form-group">
					<?php echo $form->labelEx($model,'name', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'name',array('class'=>'form-control','size'=>'45','maxlength'=>'45')); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $form->labelEx($model,'image', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8" >
						<?php echo $form->textField($model,'image',array('class'=>'form-control','size'=>'60','maxlength'=>'255', 'style' => "width:80%;float:left;margin-bottom:5px;margin-right:10px;", readonly=>'readonly')); ?>
						 	
						 	<a class="btn btn-success upload"  href="javascript:void(0)" style="float:left;">上传图片</a>
						 	<?php if($update){?>
						 	<img class="thumb" src="<?php echo $model->image?>" style="width:300px;">
						 	<?php }else{?>
						 	<img class="thumb" src="" style="display:none;width:300px;">
						 	<?php }?>
						 	 <input type="file" id="upload_input" style="visibility:hidden;">
						 	 <span>支持png,jpg格式,宽高为:640*1136</span>
						   
				</div>

				

			<div class="form-group form-group-center" style="clear:both;">
				<button class="btn btn-success btn-lg" type="">　确定　</button>
				<a class="btn btn-default btn-lg" type="button" goback="<?php echo $back ? $back : -1;?>">　取消　</a>
			</div>

		<?php
 $this->endWidget (); ?>
	</div>
</div>
<script>  
$(function () {  
	$(".upload,#Splash_image").click(function() {
        $("#upload_input").trigger('click');
    });
    
	 $('#upload_input').localResizeIMG({
         before: function() {
          
         },
         success: function(result) {
             var img = new Image();
             img.src = result.base64;
             var name = result.name;

             console.log(result);

             $('#formFile').html(result.base64.substr(22));
             //发送到服务端
             $.post('/admin.php/splash/upload', {'name': name, 'head': 1, formFile: result.base64.substr(22)}, function(data) {
                $('#Splash_image').val(data);
                $('.thumb').show();
                $('.thumb').attr('src', data);
             });
         }
     });
});  

var input0 = $("#Splash_name").val();
var url0 = $("#Splash_image").val();
$(".btn-lg").click(function(){
	     var input = $("#Splash_name").val();
	     var url = $("#Splash_image").val();			 
		 if((input == "") || (url == "")){
		      alert("请输入图片名称或地址");	
		      return false;
	     }else{
		     if((input0 != input) || (url0 != url)){
		    	 if(window.confirm('请确认对以上信息进行修改')){
		    		 $("#splash-form").submit();
		    		return true;
		    		}else{
		    		return false;
		    		} 
			 }	    	
		     $("#splash-form").submit();
             return true;
		     }
	 });
</script>  

