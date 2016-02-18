		<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/independent/js/login.js" ></script>
		<style type="text/css">
			#loading,#loading1{background: #31aae1;border-radius: 6px;color: #fff;font-size: 21px;line-height: 44px;text-align: center;display:none;}
		 	#loading1{width:100%}
		</style>
	</head>
	<body style="background: #f6f6f6;">
		<div class="ay-top">
			<dl>
				<dt></dt>
				<dd><a href="<?php echo Yii::app()->createUrl("enterpriseSite/register");?>">没有账号，点此注册 >></a></dd>
			</dl>
		</div>
        <!--beg-->
        <div class="lading">
        	<div class="lad-main choice" >
        	     <h1>登录</h1>
        	     <ul class="lad-m-cont">
        	     	<li><input name="username" placeholder="请输入奔犇号/用户名"  tabindex="1"/></li>
        	     	<li><input name="password" placeholder="请输入密码" type="password" tabindex="2"/></li>
        	     	<li style="margin-bottom: 4px;"><font><a href="<?php echo Yii::app()->createUrl("enterpriseSite/register");?>">申请注册</a></font><i><a href="<?php echo Yii::app()->createUrl("enterpriseSite/retrieve");?>">忘记密码?</a></i></li>
        	     	<li class="lad-cod">
        	     	<input name="verify" placeholder="请输入图形验证码" tabindex="3"/>
        	     	<em>
        	     	<span class="code_2">
					 <?php
					       		$this->widget('CCaptcha',
											   array('showRefreshButton'=>false,
													 'clickableImage'=>true,
                                                     'imageOptions'=>array('alt'=>'点击刷新','title'=>'点击刷新','style'=>'cursor:pointer'))); 
                            ?>
					</span>
        	     	</em>
        	     	</li>
        	     	<li class="login" style="height: 50px;margin-bottom: 0px;"><a href="javascript:;"><img src="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/images/l_2.jpg"/></a></li>
        	     	<li id="loading">正在登录,请稍后...</li>
        	     </ul>
        	</div>
        	<div class="lad-main choices" style="display: none;">
        	     <span class="company-lad-span">请选择登录的政企</span>
        	     <ul class="company-lad-ul">
        	     </ul>
        	     <dl class="company-lad-dl">
        	     	<dt 	class="administrator_login login1"><a href="javascript:;"><img src="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/images/com-la_1.jpg"/></a></dt>
        	     	<dd class="login1"><a href="javascript:location.reload();"><img src="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/images/com-la_2.jpg"/></a></dd>
        	     	<dt id="loading1">正在登录,请稍后...</dt>
        	     </dl>
        	</div>
        </div>

	</body>
</html>





























