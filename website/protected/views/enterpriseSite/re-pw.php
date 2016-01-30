		<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/independent/js/retrieve.js" ></script>
		<script>
			

			$(function(){
				
               var oh =  $(window).height()-118;
               $('.ay-pw').height(oh);


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
        	  <div class="ay-pw-cot">
        	  	<h1>找回密码</h1>
        	  	<form action="<?php echo Yii::app()->createUrl("enterpriseSite/reset");?>" method="post">
        	  	<ul>
        	  		<li><label>*</label><font>密保邮箱：</font><input name="email" value="<?php echo $email;?>" placeholder="密保邮箱"/></li>
        	  		<li style="position: relative;">
        	  		<label>*</label><font>验证码：</font>
        	  		<input name="verify"  value="<?php echo $verify;?>" placeholder="验证码"/>
        	  		<em style="margin-left: 4px;">
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
        	  		<li class="retrieve" style="height: 55px;padding-left: 11px;"><a href="javascript:;"><img src="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/images/s-7.jpg"/></a></li>
        	  		<li id="loading" >邮件正在发送中...</li>
        	  		<input name="submit" type="submit"  style="display: none;" />
        	  	</ul>
        	  	</form>
        	  </div>

        		<!--end-->
        	</div>
        	
        
	</body>
</html>




























