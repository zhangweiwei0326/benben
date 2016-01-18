<?php $this->widget('HeaderWidge',array('cssArray' => array('/themes/css/content.css')));?>
<?php $this->widget('LeftWidge', array('index' => 20)) ;?>
<div class="main_content_right  col-lg-10 col-md-9">
	<?php $this->widget('RightHeaderWidge');?>
	<div class="main_right_content">
		<div class="main_right_content_title">
			<div class="main_right_content_title_font">用户管理</div>
			<div class="main_right_content_title_navi">
				<div class="main_right_content_title_navi_root">
					<a href="<?php echo Yii::app()->createUrl("/system/index");?>">用户管理</a>
				</div>
				<div class="main_right_content_title_navi_symbol">></div>
				<div class="main_right_content_title_navi_this">
					<a href="<?php echo Yii::app()->request->baseUrl;?>">用户详细</a>
				</div>
			</div>
		</div>
		<div class=main_right_content_content>
			<div class="main_right_content_content_top"></div>
			<form action="" method="post" enctype="multipart/form-data">
				<div class="main_right_content_content_mid">
					<div class="main_right_content_content_form">
						<div
							class="main_right_content_content_msg <?php if (!empty($msg)) echo 'main_right_content_content_msg_display';?>"><?php echo $msg;?></div>
						<div class="main_right_content_content_form_block">
							<div class="main_right_content_content_form_block_font">用户名：</div>
							<div class="main_right_content_content_form_block_input">
								<input class="form_input" name="username" type="text"
									value="<?php echo Frame::getStringFromObject($user, 'username');?>" />
									<span class="required_info">*</span>
							</div>
						</div>
						<div class="main_right_content_content_form_block">
							<div class="main_right_content_content_form_block_font">设置密码：</div>
							<div class="main_right_content_content_form_block_input">
								<input class="form_input" name="password" type="password" />
							</div>

						</div>
						<div class="main_right_content_content_form_block">
							<div class="main_right_content_content_form_block_font">确认密码：</div>
							<div class="main_right_content_content_form_block_input">
								<input class="form_input" name="password2" type="password" />
							</div>
						</div>
						<div class="main_right_content_content_form_block">
							<div class="main_right_content_content_form_block_font">用户角色：</div>
							<div class="main_right_content_content_form_block_input">
								<select class="form_select" name="role">
									<option selected="selected">请选择分类</option>
									<?php
									$role = getRole ();
									foreach ( $role as $key => $item ) {
										?>
							<option value="<?php echo $key;?>"
										<?php if(($user->role) == $key) echo 'selected';?>><?php echo $item;?></option>
							<?php }?>
								</select>
								<span class="required_info">*</span>
							</div>
						</div>
						<div class="main_right_content_content_form_btn">
							<input class="main_right_content_content_form_btn_font"
								type="submit" value="确定" />
						</div>
					</div>
				</div>
			</form>
			<div class="main_right_content_content_bottom"></div>
		</div>
	</div>
</div>
<?php $this->widget('FooterWidge') ;?>