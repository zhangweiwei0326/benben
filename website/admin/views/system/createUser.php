<?php $this->widget('HeaderWidge',array('cssArray' => array('/themes/css/content.css')));?>
<?php $this->widget('LeftWidge', array('index' => 20)) ;?>
<div class="main_content_right  col-lg-10 col-md-9">
	<?php $this->widget('RightHeaderWidge');?>
	<div class="main_right_content">
		<ol class="breadcrumb main_right_content_breadcrumb">
		  <li><a href="#">用户管理</a></li>
		  <li><a href="#">用户详细</a></li>
		</ol>
		<div class="main_right_content_content">
			<?php if($msg) {?>
			<div class="alert alert-danger alert-dismissible" role="alert">
			  <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
			  <strong>警告！</strong> <?php echo $msg;?>
			</div>
			<?php }?>
			<div class="main_right_content_content_top"></div>
			<form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
				<div class="form-group">
				    <label for="inputUsername" class="col-sm-2 control-label">用户名:</label>
				    <div class="col-sm-8">
				      <input id="inputUsername" type="text" name="username" class="form-control" placeholder="用户名" value="<?php echo Frame::getStringFromArray($param, 'username');?>">
				    </div>
				</div>

				<div class="form-group">
				    <label for="inputPassword" class="col-sm-2 control-label">密码:</label>
				    <div class="col-sm-8">
				      <input id="inputPassword" type="password" name="password" class="form-control" placeholder="密码">
				    </div>
				</div>

				<div class="form-group">
				    <label for="inputPassword2" class="col-sm-2 control-label">确认密码:</label>
				    <div class="col-sm-8">
				      <input id="inputPassword2" type="password" name="password2" class="form-control" placeholder="密码">
				    </div>
				</div>

				<div class="form-group">
				    <label for="inputRole" class="col-sm-2 control-label">用户角色:</label>
				    <div class="col-sm-8">
					    <select class="form-control" id="inputRole" name="role">
						  <option selected="selected">请选择分类</option>
							<?php
							$role = getRole ();
							foreach ( $role as $key => $item ) {
								?>
							<option
										<?php if ($key == (Frame::getStringFromArray($param, 'role'))) echo 'selected';?>
										value="<?php echo $key;?>"><?php echo $item?></option>
							<?php }?>
						</select>
				    </div>
				</div>

				<div class="form-group form-group-center">
					<button class="btn btn-info btn-lg" type="submit">确定</button>
					<a class="btn btn-default btn-lg" type="button">取消</a>
				</div>

			</form>
			<div class="main_right_content_content_bottom"></div>
		</div>
	</div>
</div>
<?php $this->widget('FooterWidge') ;?>