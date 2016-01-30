

<link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/css/Personal_8.css" />
<script type="text/javascript" src="<?php echo Yii::app()->baseUrl;?>/themes/enterprise/js/jquery.JPlaceholder.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/independent/js/tools.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/independent/js/set.js"></script>


			<!--right-->
			<div class="p-main-r fl" id="p-main-r">
				
				<div class="bb_int">
					<span class="bb_span gov_msg">政企信息</span>
					<span class="bb_span pwd_change tab_con">密码修改</span>
					<span class="bb_span per_set tab_con">权限设置</span>	
					<font class="js_zq">解散政企</font>		
					<div class="clear"></div>				   
				</div>
					<!--end-->

				<!-- 政企信息-->
				<div class="main_con hide_con_1">
					<p class="bb_con">政企信息</p>
					<div>
						<span>政企名称：</span>
						<input value="<?php echo $applyInfo->enterprise_name?>" name="e_name"  class="e_name"  disabled= "true" >
						<em style="cursor:pointer;" class="apply_fix">申请修改</em>
					</div>
					<div>
						<span>政企类型：</span>
						<input value="<?php if($applyInfo->enterprise_type==1){echo "企业政企";}else{echo '虚拟网政企';} ?>"  disabled= "true" >
						<!-- <span>申请修改</span> -->
					</div>
					<div>
						<span>登录名：</span>
						<input value="<?php echo $applyInfo->login_name?>"  name="e_loginName"  class="e_loginName" disabled= "true" >
						<em style="cursor:pointer;" class="apply_fix">申请修改</em>
					</div>
					<div>
						<span>密保邮箱：</span>
						<input value="<?php echo $applyInfo->email?>" name="e_email"  class="e_email" disabled= "true" >
						<em style="cursor:pointer;" class="apply_fix">申请修改</em>
					</div>
					<div>
						<button class="butt1">确认修改</button>
					</div>
				</div>
				
				<!-- 密码修改 -->
				<div class="main_con hide_con_2 hide">
					<p class="bb_con">密码修改</p>
					<div>
						<span>旧密码：</span>
						<input type="password" value="" name="old_pw" class="old_pw">
						
					</div>
					<div class="new_pwd">
						<span>新密码：</span>
						<input type="password" value="" name="new_pw" class="new_pw">
							
					</div>
					<div>
						<span>确认密码：</span>
						<input type="password" value="" name="re_pw" class="re_pw">
							
					</div>
					<div>
						<button class="butt2">确认修改</button>
					</div>
					
				</div>

				<!-- 权限设置 -->
				<div class="main_con hide_con_3 hide">
					<p class="bb_con">权限设置</p>
					<form class="first_form" action="" method=""> 
						<span class="zq_jr" >手机端加入政企方式：</span>
						<label>
							<input class="checkbox1" type="radio" name='checkbox1' value="1"  <?php if($enterpriseRole->enterprise_apply=="1"){echo 'checked = "checked"';}?>/>
							<span>允许其他用户自由加入政企</span>
							<font class="sty">(系统默认)</font>
						</label> 
						<label><input class="checkbox1" name='checkbox1' type="radio" value="2"  <?php if($enterpriseRole->enterprise_apply=="2"){echo 'checked = "checked"';}?>/><span>允许其他用户申请加入政企</span></label> 
						<label><input class="checkbox1" name='checkbox1' type="radio" value="3" <?php if($enterpriseRole->enterprise_apply=="3"){echo 'checked = "checked"';}?>/><span>禁止其他用户加入政企</span></label> 
					</form>

					<form class="last_form" action="" method=""> 
						<span class="zq_jr">手机端添加成员方式：</span>
						<label>	
							<input class="checkbox2 checkbox21" name='checkbox21'  type="checkbox" value="1" <?php if($enterpriseRole->member_add==1){echo 'checked = "checked"';}?>/>
							<span>允许政企成员使用添加成员和我添加的联系人</span>
							<font class="sty">（系统默认）</font></label> 
						<label>
							<input class="checkbox2 checkbox22" name='checkbox22' type="checkbox" value="1" <?php if($enterpriseRole->member_add_other==1){echo 'checked = "checked"';}?>/>
							<span>允许管理员使用添加成员和我添加的联系人</span>
						</label> 
<!-- 						<label><input class="checkbox"type="checkbox" value="" /><span>禁止允许政企成员使用添加成员和我添加的联系人</span></label>  -->
					</form>
					<div class="dj_sz">
						<span class="zq_jr zq_jl" style="width:139px;">查阅等级设置：</span>
						<select>
							
							<?php for($i=1;$i<=10;$i++){ ?>
								<option value="<?php echo $i?>" <?php if($enterpriseRole->access_level==$i){echo 'selected = "selected"';}?>><?php echo $i?></option>
								
							<?php } ?>
						</select>
						<font class="sty" style="margin-left:10px;">(1-10级，默认1级)</font>
						<p class="dj_p">如果你的查看权限等级是2级，那么他能在政企中查看或搜索到3级以下的号码，即n级能看到n+1以及以下的成员</p>
					</div>
					<div class="com_but">
						<button class="butt3">确认修改</button>
					</div>
				</div>
			</div>
				
		</div>
	
	


	<script type="text/javascript">
		$('.gov_msg').click(function(){
			$('.main_con').addClass('hide');
			$('.bb_span').addClass('tab_con');

			$('.hide_con_1').removeClass('hide');
			$('.gov_msg').removeClass('tab_con');
		});
		$('.pwd_change').click(function(){
			$('.main_con').addClass('hide');
			$('.bb_span').addClass('tab_con');

			$('.hide_con_2').removeClass('hide');
			$('.pwd_change').removeClass('tab_con');
		});
		$('.per_set').click(function(){
			$('.main_con').addClass('hide');
			$('.bb_span').addClass('tab_con');

			$('.hide_con_3').removeClass('hide');
			$('.per_set').removeClass('tab_con');
		});


		// $('.bb_service').click(function(){
		// 	$('.hide_con_1').addClass('hide');
		// 	$('.bb_introduce').addClass('tab_con');
		// 	$('.hide_con_2').removeClass('hide');
		// 	$('.bb_service').removeClass('tab_con');
		// });


	</script>

</html>




























