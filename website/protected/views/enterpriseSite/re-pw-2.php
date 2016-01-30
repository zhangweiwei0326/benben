		<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/independent/js/reset.js" ></script>
		<script>
			

			$(function(){
// 				var oHz = $(window).height();
//                var oh =  $(window).height()-118;
//                if(oHz>540){
//                	$('.ay-pw').height(oh);
//                }else{
//                	$('.ay-pw').height('540px');
//                }


			})
</script>
		<style type="text/css">
			#loading{
					width:333px;height:55px;line-height:55px;color:#fff;background:#31AAE1;
					margin-left:11px;border-radius:5px;font-size: 22px;text-align: center;display:none;
			}
		</style>
	</head>
	<body style="background: #f6f6f6;">
		<div class="ay-top">
			<dl>
				<dt></dt>
				<dd><a href="<?php echo Yii::app()->createUrl("enterpriseSite/login");?>">已有账号，点此登录 >></a></dd>
			</dl>
		</div>
        <div class="ay-pw">
        	  <div class="ay-pw-cot pwtow">
        	  	<h1>找回密码</h1>
        	  	<form action="<?php echo Yii::app()->createUrl("enterpriseSite/retrieve");?>" method="post" >
        	  	<p  class="forgot-up laststep"><a href="javascript:;">&lt;上一步</a></p>
        	  	<input name="email"  type="hidden"  value="<?php echo $email;?>" />
        	  	<input name="submit"  type="submit"  style="display: none;"/>
        	  	</form>
        	  	<p class="forgot-up2">我们已经给地址为 <?php echo $code_email;?>的密保邮箱发送了一个验证码，此验证码30分钟内有效</p>
        	  	<ul>
        	  		<li><label>*</label><font>验证码：</font><input name="verify" placeholder="输入验证码" style="width: 320px;"/></li>
        	  		<li style="margin-top: 57px;"><label>*</label><font>重置密码：</font><input name="password" placeholder="需要找回密码的账号" type="password"/></li>
        	  		<li style="margin-top: 17px;"><label>*</label><font>确认密码：</font><input name="repassword" placeholder="需要找回密码的账号" type="password"/></li>
        	  		<li class="reset" style="height: 55px;padding-left: 11px;"><a href="javascript:;"><img src="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/images/qu-5.jpg"/></a></li>
        	  		<li id="loading">正在重置密码...</li>
        	  	</ul>
        	  </div>

        		<!--end-->
        	</div>
        	
        
	</body>
</html>





























