<?php
	Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/themes/css/content.css");
?>
<?php
/* @var $this RoleController */
/* @var $model Role */
/* @var $form CActiveForm */
?>
<style>
table{background:none}
</style>



<div class="main_right_content">
	<ol class="breadcrumb main_right_content_breadcrumb">
		<li><a href="<?php echo Yii::app()->createUrl('role/index')?>">角色管理</a></li>
		<li><a href="javascript:void"><?php if($_GET['add']){echo "增加角色";}else{echo "编辑角色";}?></a></li>
	</ol>

	<div class="main_right_content_content">
		<?php if($msg) {?>
				<div class="alert alert-danger alert-dismissible" role="alert">
				  <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				  <strong>警告！</strong> <?php echo $msg;?>
				</div>
				<?php }?>
		<?php $form=$this->beginWidget('CActiveForm', array(
			'id'=>'role-form',
			'htmlOptions'=>array('enctype'=>'multipart/form-data', 'class'=>'form-horizontal'),
			'enableAjaxValidation'=>false,
		)); ?>
				
				<div class="form-group">
					<?php echo $form->labelEx($model,'role_name', array("class"=>"col-sm-2 control-label"));?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'role_name',array('class'=>'form-control','size'=>'20','maxlength'=>'20')); ?>
					</div>
				</div>
			
			<div class="form-group">
					<label class="col-sm-2 control-label " for="Role_role_name">角色权限</label>
					<div class="col-sm-8">
					<table class="table table-bordered table-condensed table-hover table-striped">
						<tbody>
							<tr>
								<td rowspan="2">用户管理</td>
								<td>
									<span id="Role_domember0">
										<input id="Role_domember_0" value="1" type="checkbox" <?php if(($model->domember & 1) && ($model->domember & 2)) echo "checked=checked ";?>name="Role[domember][editall]"> 
										<label for="Role_domember_0">用户编辑</label>
									</span>
								</td>
								<td>
									<span id="Role_domember1">
										<input class="domember_1" value="1" type="checkbox" <?php if($model->domember & 1) echo "checked=checked ";?>name="Role[domember][edit]"> 
										<label for="Role_domember_1">用户编辑权限</label>
									</span>
									<span id="Role_domember2">
										<input class="domember_1" value="1" type="checkbox" <?php if($model->domember & 2) echo "checked=checked ";?>name="Role[domember][put]"> 
										<label for="Role_domember_2">用户导出权限</label>
									</span>	
								</td>
							</tr>
							<tr>
								<td>
									<span id="Role_domember3">
										<input id="Role_domember_3" value="1" type="checkbox" <?php if($model->domember & 4) echo "checked=checked ";?>name="Role[domember][statistic]"> 
										<label for="Role_domember_3">用户统计</label>
									</span>
								</td>
								<td></td>
							</tr>
							<tr>
								<td rowspan="4">百姓网管理</td>
								<td>
									<span id="Role_dobaixing0">
										<input id="Role_dobaixing_0" value="1" type="checkbox" <?php if(($model->dobaixing & 1) && ($model->dobaixing & 2) && ($model->dobaixing & 32)) echo "checked=checked ";?>name="Role[dobaixing][editall]"> 
										<label for="Role_dobaixing_0">百姓网管理</label>
									</span>
								</td>
								<td>
									<span id="Role_dobaixing1">
										<input class="Role_dobaixing_1" value="1" type="checkbox" <?php if($model->dobaixing & 32) echo "checked=checked ";?>name="Role[dobaixing][index]"> 
										<label for="Role_dobaixing_1">用户查看权限</label>
									</span>
									<span id="Role_dobaixing1">
										<input class="Role_dobaixing_1" value="1" type="checkbox" <?php if($model->dobaixing & 1) echo "checked=checked ";?>name="Role[dobaixing][edit]"> 
										<label for="Role_dobaixing_1">用户编辑权限</label>
									</span>
									<span id="Role_dobaixing2">
										<input class="Role_dobaixing_1" value="1" type="checkbox" <?php if($model->dobaixing & 2) echo "checked=checked ";?>name="Role[dobaixing][put]"> 
										<label for="Role_dobaixing_2">用户导出权限</label>
									</span>	
								</td>
							</tr>
							<tr>
								<td>
									<span id="Role_dobaixing3">
										<input id="Role_dobaixing_3" value="1" type="checkbox" <?php if($model->dobaixing & 4) echo "checked=checked ";?>name="Role[dobaixing][putapply]"> 
										<label for="Role_dobaixing_3">导出申请数据</label>
									</span>
								</td>
								<td></td>
							</tr>
							<tr>
								<td>
									<span id="Role_dobaixing4">
										<input id="Role_dobaixing_4" value="1" type="checkbox" <?php if($model->dobaixing & 8) echo "checked=checked ";?>name="Role[dobaixing][input]"> 
										<label for="Role_dobaixing_4">批量录入数据</label>
									</span>
								</td>
								<td></td>
							</tr>
							<tr>
								<td>
									<span id="Role_dobaixing5">
										<input id="Role_dobaixing_5" value="1" type="checkbox" <?php if($model->dobaixing & 16) echo "checked=checked ";?>name="Role[dobaixing][statistic]"> 
										<label for="Role_dobaixing_5">百姓网统计</label>
									</span>
								</td>
								<td></td>
							</tr>							
							<tr>
								<td rowspan="3">通讯录管理</td>
								<td>
									<span id="Role_domember0">
										<input id="Role_doenterprise_0" value="1" type="checkbox" <?php if(($model->doenterprise & 1) && ($model->doenterprise & 2) && ($model->doenterprise & 4)) echo "checked=checked ";?>name="Role[doenterprise][editall]"> 
										<label for="Role_domember_0">政企通讯录</label>
									</span>
								</td>
								<td>
									<span id="Role_domember1">
										<input class="Role_doenterprise_1" value="1" type="checkbox" <?php if($model->doenterprise & 1) echo "checked=checked ";?>name="Role[doenterprise][edit]"> 
										<label for="Role_domember_1">用户编辑权限</label>
									</span>
									<span id="Role_domember2">
										<input class="Role_doenterprise_1" value="1" type="checkbox" <?php if($model->doenterprise & 2) echo "checked=checked ";?>name="Role[doenterprise][put]"> 
										<label for="Role_domember_2">用户导出权限</label>
									</span>
									<span id="Role_domember1">
										<input class="Role_doenterprise_1" value="1" type="checkbox" <?php if($model->doenterprise & 4) echo "checked=checked ";?>name="Role[doenterprise][create]"> 
										<label for="Role_domember_1">用户创建权限</label>
									</span>									
								</td>
							</tr>
							<tr>
								<td>
									<span id="Role_domember0">
										<input id="" value="1" type="checkbox" <?php if($model->dogroup & 1) echo "checked=checked ";?>name="Role[dogroup][editall]"> 
										<label for="Role_domember_0">群组</label>
									</span>
								</td>
								<td></td>
							</tr>
							<tr>
								<td>
									<span id="Role_domember0">
										<input id="Role_dostore_0" value="1" type="checkbox" <?php if(($model->dostore & 1) && ($model->dostore & 2) && ($model->dostore & 4)) echo "checked=checked ";?>name="Role[dostore][editall]"> 
										<label for="Role_domember_0">号码直通车</label>
									</span>
								</td>
								<td>
									<span id="Role_domember1">
										<input class="Role_dostore_1" value="1" type="checkbox" <?php if($model->dostore & 1) echo "checked=checked ";?>name="Role[dostore][edit]"> 
										<label for="Role_domember_1">用户编辑权限</label>
									</span>
									<span id="Role_domember2">
										<input class="Role_dostore_1" value="1" type="checkbox" <?php if($model->dostore & 2) echo "checked=checked ";?>name="Role[dostore][top]"> 
										<label for="Role_domember_2">用户置顶权限</label>
									</span>
									<span id="Role_domember1">
										<input class="Role_dostore_1" value="1" type="checkbox" <?php if($model->dostore & 4) echo "checked=checked ";?>name="Role[dostore][statistic]"> 
										<label for="Role_domember_1">用户统计权限</label>
									</span>									
								</td>
							</tr>
							<tr>
								<td rowspan="5">发现管理</td>
								<td>
									<span id="Role_domember0">
										<input id="Role_docreation_0" value="1" type="checkbox" <?php if(($model->docreation & 1) && ($model->docreation & 2)) echo "checked=checked ";?>name="Role[docreation][editall]"> 
										<label for="Role_domember_0">微创作</label>
									</span>
								</td>
								<td>
									<span id="Role_domember1">
										<input class="Role_docreation_1" value="1" type="checkbox" <?php if($model->docreation & 1) echo "checked=checked ";?>name="Role[docreation][edit]"> 
										<label for="Role_domember_1">用户编辑权限</label>
									</span>
									<span id="Role_domember2">
										<input class="Role_docreation_1" value="1" type="checkbox" <?php if($model->docreation & 2) echo "checked=checked ";?>name="Role[docreation][put]"> 
										<label for="Role_domember_2">用户导出权限</label>
									</span>	
								</td>
							</tr>
							<tr>
								<td>
									<span id="Role_domember1">
										<input id="Role_dorelease_0" value="1" type="checkbox" <?php if(($model->dorelease & 1) && ($model->dorelease & 2)) echo "checked=checked ";?>name="Role[dorelease][editall]"> 
										<label for="Role_domember_1">我要买</label>
									</span>
								</td>
								<td>
									<span id="Role_domember1">
										<input class="Role_dorelease_1" value="1" type="checkbox" <?php if($model->dorelease & 1) echo "checked=checked ";?>name="Role[dorelease][edit]"> 
										<label for="Role_domember_1">用户编辑权限</label>
									</span>
									<span id="Role_domember2">
										<input class="Role_dorelease_1" value="1" type="checkbox" <?php if($model->dorelease & 2) echo "checked=checked ";?>name="Role[dorelease][put]"> 
										<label for="Role_domember_2">用户导出权限</label>
									</span>	
								</td>
							</tr>
							<tr>
								<td>
									<span id="Role_domember1">
										<input id="Role_dofriend_0" value="1" type="checkbox" <?php if(($model->dofriend & 1) && ($model->dofriend & 2)) echo "checked=checked ";?>name="Role[dofriend][editall]"> 
										<label for="Role_domember_1">朋友圈</label>
									</span>
								</td>
								<td>
									<span id="Role_domember1">
										<input class="Role_dofriend_1" value="1" type="checkbox" <?php if($model->dofriend & 1) echo "checked=checked ";?>name="Role[dofriend][edit]"> 
										<label for="Role_domember_1">用户编辑权限</label>
									</span>
									<span id="Role_domember2">
										<input class="Role_dofriend_1" value="1" type="checkbox" <?php if($model->dofriend & 2) echo "checked=checked ";?>name="Role[dofriend][put]"> 
										<label for="Role_domember_2">用户导出权限</label>
									</span>	
								</td>
							</tr>
							<tr>
								<td>
									<span id="Role_domember1">
										<input id="Role_dohappy_0" value="1" type="checkbox" <?php if(($model->dohappy & 1) && ($model->dohappy & 2) && ($model->dohappy & 4)) echo "checked=checked ";?>name="Role[dohappy][editall]"> 
										<label for="Role_domember_1">开心一刻</label>
									</span>
								</td>
								<td>
									<span id="Role_domember1">
										<input class="Role_dohappy_1" value="1" type="checkbox" <?php if($model->dohappy & 1) echo "checked=checked ";?>name="Role[dohappy][edit]"> 
										<label for="Role_domember_1">用户编辑权限</label>
									</span>
									<span id="Role_domember2">
										<input class="Role_dohappy_1" value="1" type="checkbox" <?php if($model->dohappy & 2) echo "checked=checked ";?>name="Role[dohappy][create]"> 
										<label for="Role_domember_2">用户增加权限</label>
									</span>
									<span id="Role_domember2">
										<input class="Role_dohappy_1" value="1" type="checkbox" <?php if($model->dohappy & 4) echo "checked=checked ";?>name="Role[dohappy][upload]"> 
										<label for="Role_domember_2">用户上传权限</label>
									</span>		
								</td>
							</tr>
							<tr>
								<td>
									<span id="Role_domember1">
										<input id="" value="1" type="checkbox" <?php if($model->dofind & 1) echo "checked=checked ";?>name="Role[dofind][statistic]"> 
										<label for="Role_domember_1">发现统计</label>
									</span>
								</td>
								<td></td>
							</tr>
							<tr>
								<td>好友联盟</td>
								<td>
									<span id="Role_domember0">
										<input id="Role_doleague_0" value="1" type="checkbox" <?php if(($model->doleague & 1) && ($model->doleague & 2)) echo "checked=checked ";?>name="Role[doleague][editall]"> 
										<label for="Role_domember_0">好友联盟</label>
									</span>
								</td>
								<td>
									<span id="Role_domember1">
										<input class="Role_doleague_1" value="1" type="checkbox" <?php if($model->doleague & 1) echo "checked=checked ";?>name="Role[doleague][edit]"> 
										<label for="Role_domember_1">用户编辑权限</label>
									</span>
									<span id="Role_domember2">
										<input class="Role_doleague_1" value="1" type="checkbox" <?php if($model->doleague & 2) echo "checked=checked ";?>name="Role[doleague][put]"> 
										<label for="Role_domember_2">用户导出权限</label>
									</span>	
								</td>
							</tr>						
							<tr>
								<td rowspan="3">消息管理</td>
								<td>
									<span id="Role_domember0">
										<input id="" value="1" type="checkbox" <?php if($model->donews & 1) echo "checked=checked ";?>name="Role[donews][system]"> 
										<label for="Role_domember_0">系统通知</label>
									</span>
								</td>
								<td></td>
							</tr>
							<tr>
								<td>
									<span id="Role_domember3">
										<input id="" value="1" type="checkbox" <?php if($model->donews & 2) echo "checked=checked ";?>name="Role[donews][to]"> 
										<label for="Role_domember_3">定向通知</label>
									</span>
								</td>
								<td></td>
							</tr>
							<tr>
								<td>
									<span id="Role_domember3">
										<input id="" value="1" type="checkbox" <?php if($model->donews & 4) echo "checked=checked ";?>name="Role[donews][broadcastingLog]"> 
										<label for="Role_domember_3">小喇叭管理</label>
									</span>
								</td>
								<td></td>
							</tr>
							<tr>
								<td rowspan="5">其他管理</td>
								<td>
									<span id="Role_domember0">
										<input id="" value="1" type="checkbox" <?php if($model->doother & 1) echo "checked=checked ";?>name="Role[doother][protocol]"> 
										<label for="Role_domember_0">协议&说明</label>
									</span>
								</td>
								<td></td>
							</tr>
							<tr>
								<td>
									<span id="Role_domember1">
										<input id="" value="1" type="checkbox" <?php if($model->doother & 2) echo "checked=checked ";?>name="Role[doother][industry]"> 
										<label for="Role_domember_1">行业字典</label>
									</span>
								</td>
								<td></td>
							</tr>
							<tr>
								<td>
									<span id="Role_domember1">
										<input id="" value="1" type="checkbox" <?php if($model->doother & 4) echo "checked=checked ";?>name="Role[doother][version]"> 
										<label for="Role_domember_1">版本管理</label>
									</span>
								</td>
								<td></td>
							</tr>
							<tr>
								<td>
									<span id="Role_domember1">
										<input id="" value="1" type="checkbox" <?php if($model->doother & 8) echo "checked=checked ";?>name="Role[doother][complain]"> 
										<label for="Role_domember_1">投诉建议</label>
									</span>
								</td>
								<td></td>
							</tr>
							<tr>
								<td>
									<span id="Role_domember1">
										<input id="" value="1" type="checkbox" <?php if($model->doother & 16) echo "checked=checked ";?>name="Role[doother][splash]"> 
										<label for="Role_domember_1">开机页面</label>
									</span>
								</td>
								<td></td>
							</tr>
							<tr>
								<td rowspan="5">系统管理</td>
								<td>
									<span id="Role_domember0">
										<input id="" value="1" type="checkbox" <?php if($model->dosystem & 1) echo "checked=checked ";?>name="Role[dosystem][user]"> 
										<label for="Role_domember_0">用户管理</label>
									</span>
								</td>
								<td></td>
							</tr>
							<tr>
								<td>
									<span id="Role_domember1">
										<input id="" value="1" type="checkbox" <?php if($model->dosystem & 2) echo "checked=checked ";?>name="Role[dosystem][role]"> 
										<label for="Role_domember_1">角色管理</label>
									</span>
								</td>
								<td></td>
							</tr>
							<tr>
								<td>
									<span id="Role_domember1">
										<input id="" value="1" type="checkbox" <?php if($model->dosystem & 4) echo "checked=checked ";?>name="Role[dosystem][password]"> 
										<label for="Role_domember_1">个人密码修改</label>
									</span>
								</td>
								<td></td>
							</tr>
							<tr>
								<td>
									<span id="Role_domember1">
										<input id="" value="1" type="checkbox" <?php if($model->dosystem & 8) echo "checked=checked ";?>name="Role[dosystem][log]"> 
										<label for="Role_domember_1">登录日志</label>
									</span>
								</td>
								<td></td>
							</tr>
							
						</tbody>
					</table>
						
						
						<?php //echo$form->checkBoxList($model,'dobaixing',array(1=>'百姓网管理权限')); ?>
						<br>
						<?php //echo$form->checkBoxList($model,'doenterprise',array(1=>'政企通讯录管理')); ?>
						<?php //echo$form->checkBoxList($model,'dogroup',array(1=>'群组通讯录管理'), array('style'=>"margin-left:10px;")); ?>
						<?php //echo$form->checkBoxList($model,'dostore',array(1=>'号码直通车管理'), array('style'=>"margin-left:10px;")); ?>
						<br>
						<?php //echo$form->checkBoxList($model,'docreation',array(1=>'微创作管理')); ?>
						<?php //echo$form->checkBoxList($model,'dorelease',array(1=>'我要买管理'), array('style'=>"margin-left:10px;")); ?>
						<?php //echo$form->checkBoxList($model,'dofriend',array(1=>'朋友圈管理'), array('style'=>"margin-left:10px;")); ?>
						<?php //echo$form->checkBoxList($model,'dohappy',array(1=>'开心一刻管理'), array('style'=>"margin-left:10px;")); ?>
						<br>
						<?php //echo$form->checkBoxList($model,'doleague',array(1=>'好友联盟')); ?>
						<br>
						<?php //echo$form->checkBoxList($model,'donews',array(1=>'消息管理')); ?>
						<br>
						<?php //echo$form->checkBoxList($model,'dowebsite',array(1=>'网站维护')); ?>
						<br>
						<?php //echo$form->checkBoxList($model,'dosystem',array(1=>'系统管理')); ?>
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
$(function(){
	var input0 = $("#Role_role_name").val();
	 $(".btn-success").click(function(){
		     var input = $("#Role_role_name").val();	 
			 if(input == ""){
			      alert("请输入角色名");	
			      return false;
		     }else{
			     if(input0 != input){
			    	 if(window.confirm('请确认对以上信息进行修改')){
			    		 $("#role-form").submit();
			    		return true;
			    		}else{
			    		return false;
			    		} 
				 }	    	
			     $("#role-form").submit();
	              return true;
			     }
		 });
	 $(document).on('click','#Role_domember_0',function(){
		 var status = $(this).is(":checked");
		 if(status == true){
			 $(".domember_1").each(function(){
// 				$(this).prop("checked","checked");
				this.checked = true;
//	 			 $(this).is(":checked");
			 });
		 }else{
			 $(".domember_1").each(function(){
// 				$(this).prop("checked",false);
				this.checked = false;
			 });
			// $(".domember_1").removeAttr("checked");
		 }
		});
	 $(".domember_1").click(function(){		 
		 $("#Role_domember_0").prop("checked",false);
	 });

		$("#Role_dobaixing_0").click(function(){
			var status = $(this).is(":checked");
			if(status == true){
				 $(".Role_dobaixing_1").each(function(){	 				
					this.checked = true;		 			 
				 });
			 }else{
				 $(".Role_dobaixing_1").each(function(){
					this.checked = false;
				 });			
			 }
		});
		$(".Role_dobaixing_1").click(function(){		 
			 $("#Role_dobaixing_0").prop("checked",false);
			 var status = 0;
			 $(".Role_dobaixing_1").each(function(){	 				
				 if($(this).is(":checked")) status++;		 			 
			 });
			 if(status == 3) $("#Role_dobaixing_0").prop("checked",true);
		 });
		 
		$("#Role_doenterprise_0").click(function(){
			var status = $(this).is(":checked");
			if(status == true){
				 $(".Role_doenterprise_1").each(function(){	 				
					this.checked = true;		 			 
				 });
			 }else{
				 $(".Role_doenterprise_1").each(function(){
					this.checked = false;
				 });			
			 }
		});
		$(".Role_doenterprise_1").click(function(){		 
			 $("#Role_doenterprise_0").prop("checked",false);
		 });
		 
		$("#Role_dostore_0").click(function(){
			var status = $(this).is(":checked");
			if(status == true){
				 $(".Role_dostore_1").each(function(){	 				
					this.checked = true;		 			 
				 });
			 }else{
				 $(".Role_dostore_1").each(function(){
					this.checked = false;
				 });			
			 }
		});
		$(".Role_dostore_1").click(function(){		 
			 $("#Role_dostore_0").prop("checked",false);
		 });
		 
		$("#Role_docreation_0").click(function(){
			var status = $(this).is(":checked");
			if(status == true){
				 $(".Role_docreation_1").each(function(){	 				
					this.checked = true;		 			 
				 });
			 }else{
				 $(".Role_docreation_1").each(function(){
					this.checked = false;
				 });			
			 }
		});
		$(".Role_docreation_1").click(function(){		 
			 $("#Role_docreation_0").prop("checked",false);
		 });
		 
		$("#Role_dorelease_0").click(function(){
			var status = $(this).is(":checked");
			if(status == true){
				 $(".Role_dorelease_1").each(function(){	 				
					this.checked = true;		 			 
				 });
			 }else{
				 $(".Role_dorelease_1").each(function(){
					this.checked = false;
				 });			
			 }
		});
		$(".Role_dorelease_1").click(function(){		 
			 $("#Role_dorelease_0").prop("checked",false);
		 });
		 
		$("#Role_dofriend_0").click(function(){
			var status = $(this).is(":checked");
			if(status == true){
				 $(".Role_dofriend_1").each(function(){	 				
					this.checked = true;		 			 
				 });
			 }else{
				 $(".Role_dofriend_1").each(function(){
					this.checked = false;
				 });			
			 }
		});
		$(".Role_dofriend_1").click(function(){		 
			 $("#Role_dofriend_0").prop("checked",false);
		 });
		 
		$("#Role_dohappy_0").click(function(){
			var status = $(this).is(":checked");
			if(status == true){
				 $(".Role_dohappy_1").each(function(){	 				
					this.checked = true;		 			 
				 });
			 }else{
				 $(".Role_dohappy_1").each(function(){
					this.checked = false;
				 });			
			 }
		});
		$(".Role_dohappy_1").click(function(){		 
			 $("#Role_dohappy_0").prop("checked",false);
		 });
		 
		$("#Role_doleague_0").click(function(){
			var status = $(this).is(":checked");
			if(status == true){
				 $(".Role_doleague_1").each(function(){	 				
					this.checked = true;		 			 
				 });
			 }else{
				 $(".Role_doleague_1").each(function(){
					this.checked = false;
				 });			
			 }
		});
		$(".Role_doleague_1").click(function(){		 
			 $("#Role_doleague_0").prop("checked",false);
		 });
	
})
</script>
