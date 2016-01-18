<?php $this->widget('HeaderWidge',array('cssArray' => array('/themes/css/index.css')));?>
<?php $this->widget('LeftWidge', array('index' => 0)) ;?>
<div class="main_content_right col-lg-10 col-md-10 col-sm-12 col-xs-12">
	<?php $this->widget('RightHeaderWidge');?>
	<div class="main_right_content">
		<div class="main_right_content_title">
			<div class="main_right_content_title_font">欢迎页面</div>
		</div>
		<div class="main_right_content_content_wel">
		<div class="welcome_msg">欢迎使用奔犇管理后台！</div>
		</div>
	</div>
</div>
<?php $this->widget('FooterWidge') ;?>