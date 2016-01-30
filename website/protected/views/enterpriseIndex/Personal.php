<style>
	.pm-r-notice textarea{
		font-size: 18px;
		color: #969696;
		margin-left: 22px;
		margin-top: 30px;
		line-height: 30px;
		height:90px;
	}
	.p-main-r .pm-r-ul {width:100%;height:100%;}
	.p-main-r .pm-r-ul li {width:23%;height:100%;margin-right:2.6666666666%}
	.p-main-r .pm-r-ul li em {width:40%;height:100%;}
	.p-main-r .pm-r-ul li em img {width:100%;}
	.p-main-r .pm-r-ul li p {width:60%;height:100%;}
	.p-main-r .pm-r-ul li p span {padding-top:17%;font-size: 150%;}
 	.p-main-r .pm-r-ul li p i {padding:25% 0;line-height: inherit;font-size: 150%;} 
 	.p-main-r .pm-r-ul li p font {font-size: 120%;} 
</style>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/independent/js/tools.js"></script>
<script>
			$(function(){

				//历史公告
// 				$('#notice-con').click(function(){
// 					$('#no-men').show()
// 				});
// 				$('#no-men .member-dl dd').click(function(){
					
// 					$('#no-men').hide()
// 				});
				//公告修改
				$('.modify-notice').click(function(){
					var con = $("#notice-detail").text();
					var conBody = '<textarea id="notice-detail"  cols="103" rows="1" style="width:95%;">'+con+'</textarea>';
					$('#notice-detail').replaceWith(conBody);
// 					$(this).removeClass("modify-notice");
// 					$(this).addClass("confirm-notice");
					var modBody='<em class="confirm-notice" style="font-size: 18px;color: #3AB2FF; cursor:pointer;">确认修改</em>'
					$(this).replaceWith(modBody);
					$('.cancel-notice').show();
					$('.new-pub').hide();
					});

				$('.cancel-notice').click(function(){
// 					var con = $("#notice-detail").text();
// 					var conBody = '<p id="notice-detail">'+con+'</p>';
// 					$('#notice-detail').replaceWith(conBody);
// 					$(this).hide();
// 					$('.new-pub').show();
// // 					$('.confirm-notice').removeClass("confirm-notice");
// // 					$(this).prev().addClass("modify-notice");
					
// 					var modBody='<a class="modify-notice" href="javascript:;">修改</a>';
// 					$('.confirm-notice').replaceWith(modBody);
					location.href="/index.php/enterpriseIndex/index";
					});

				//修改当前公告
				$(document).on('click','.confirm-notice',function(){
						var notice_content=$('#notice-detail').val();
						var notice_content=$.trim(notice_content);

						if($("p").hasClass("notice_detail")){
							if(new_content==''){
// 								normalDialog.dialog('什么都没有，不能发布哦！');
								layer.msg('什么都没有了，不能发布哦！',{icon:2,time:10000,btn:['知道了']});
								}else{
									$.ajax({
				            			type:"POST",
				            			url:"/index.php/enterpriseIndex/index",
				            			data:{
				                			'new_content': new_content
				            					},
				            			async:true,
				            			cache:false,
				            			dataType:"json",
				            			beforeSend:function(){	
				            			},
				            			success:function(data){
				            				if(data==1){
				            					location.href="/index.php/enterpriseIndex/index";
				            				}else{
// 				            					normalDialog.dialog('发布失败！');
				            					layer.msg('发布失败！',{icon:2,time:10000,btn:['知道了']});
				            					}
				            				
				            			}
				            		});
								}
						}else{
							if(notice_content==''){
//	 							normalDialog.dialog('什么都没有了，不行哦！');
								layer.msg('什么都没有了，不行哦！',{icon:2,time:10000,btn:['知道了']});
								}else{
									$.ajax({
				            			type:"POST",
				            			url:"/index.php/enterpriseIndex/index",
				            			data:{
				                			'notice_content': notice_content
				                			
				            					},
				            			async:true,
				            			cache:false,
				            			dataType:"json",
				            			beforeSend:function(){	
				            			},
				            			success:function(data){
				            				if(data==1){
				            					location.href="/index.php/enterpriseIndex/index";
				            				}else{
//	 			            					normalDialog.dialog('修改失败！');
				            					layer.msg('修改失败！',{icon:2,time:10000,btn:['知道了']});
				            					}
				            				
				            			}
				            		});
								}
						}
						
						
					});

				//发布新公告
				$('.new-pub').click(function(){
						var conBody = '<textarea id="notice-detail"  cols="103" style="width:95%;" rows="1"></textarea>';
					if($("p").hasClass("notice_detail")){
						$('#notice-detail').replaceWith(conBody);
						var modBody='<em class="confirm-pub" style="font-size: 18px;color: #3AB2FF; cursor:pointer;">确认发布</em>';
					}else{
						$("#first_clear").after(conBody);
						var modBody='<em class="confirm-pub" style="font-size: 18px;color: #3AB2FF; cursor:pointer;">确认发布</em>';
					}
				
					$('.modify-notice').replaceWith(modBody);
					$('.modify-notice').show();
					$('.cancel-notice').show();
					$('.new-pub').hide();
					
					});		
				$(document).on('click','.confirm-pub',function(){
							var new_content=$('#notice-detail').val();
							var new_content=$.trim(new_content);
							if(new_content==''){
// 								normalDialog.dialog('什么都没有，不能发布哦！');
								layer.msg('什么都没有了，不能发布哦！',{icon:2,time:10000,btn:['知道了']});
								}else{
									$.ajax({
				            			type:"POST",
				            			url:"/index.php/enterpriseIndex/index",
				            			data:{
				                			'new_content': new_content
				            					},
				            			async:true,
				            			cache:false,
				            			dataType:"json",
				            			beforeSend:function(){	
				            			},
				            			success:function(data){
				            				if(data==1){
				            					location.href="/index.php/enterpriseIndex/index";
				            				}else{
// 				            					normalDialog.dialog('发布失败！');
				            					layer.msg('发布失败！',{icon:2,time:10000,btn:['知道了']});
				            					}
				            				
				            			}
				            		});
								}
					});
			})
		</script>

<!--right-->
<div class="p-main-r fl" style="height: 820px" id="p-main-r">
	<em class="pm-r-bnn"><a href="javascript:;"><img
			src="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/images/rbnner.jpg" /></a></em>
	<div class="p-mian-cont">
		<dl class="pm-r-dl"
			<?php if($this->apply_status==0 || empty($news)) echo 'style="display:none;"';?>>
			<dt>
				<span><a href="<?php echo Yii::app()->createUrl("enterpriseNews/notice");?>">系统消息</a></span><font> > </font><i><?php echo $applyer->name?$applyer->name:$applyer->nick_name;?>申请加入政企</i>
			</dt>
			<dd style="display: none;">
				<span>有效期至: 2016-6-1</span><a href="javascript:;"><img
					src="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/images/s_1.jpg" /></a>
			</dd>
		</dl>
		<div class="clear"></div>
		<ul class="pm-r-ul"
			<?php if($this->apply_status==0) echo 'style="display:none;"';?>>
			<a
				href="<?php echo Yii::app()->createUrl("enterpriseIndex/member");?>"><li
				style="margin-left: 0;"><em><img
						src="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/images/s_2.jpg" /></em>
				<p>
						<span>成员管理</span><font><?php echo $enterprise->number?$enterprise->number:0;?>/<?php echo $role->member_limit;?></font>
					</p></li></a>
			<a href="<?php echo Yii::app()->createUrl("enterpriseBroadcast/index");?>"><li><em><img
					src="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/images/s_2.jpg" /></em>
			<p>
					<span>大喇叭管理</span><font>剩余<?php echo $role->broadcast_available?$role->broadcast_available:'0';?>个</font>
				</p></li></a>
			<a href="<?php echo Yii::app()->createUrl("enterpriseDataAnalysis/index");?>"><li><em><img
					src="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/images/s_2.jpg" /></em>
			<p>
					<i>数据统计</i>
				</p></li></a>
			<a href="<?php echo Yii::app()->createUrl("enterpriseIndex/mustRead");?>"><li><em><img
					src="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/images/s_2.jpg" /></em>
			<p>
					<i>管理员必读</i>
				</p></li></a>
		</ul>
		<div class="clear"></div>
		<div class="pm-r-notice"
			<?php if($this->apply_status==0) echo 'style="display:none;"';?>>
			<dl>
				<dt>公告栏</dt>
				<dd id="notice-con">
					<a href="<?php echo Yii::app()->createUrl("enterpriseIndex/historyNotice");?>">历史公告</a>
				</dd>
			</dl>
			<div class="clear" id="first_clear"></div>
			<?php 
			if(!empty($notice)){
			foreach ($notice as $key=>$val){
					if($key == 0){
						$curTime = $val->update_time;
				?>
			<p id="notice-detail" class="notice_detail"><?php echo $val->content;?></p>
			<?php }
					}
					}?>
			<div class="clear"></div>
			<ul>
				<li><a href="javascript:;"  class="new-pub"><img
						src="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/images/s_3.jpg" /></a></li>
				<li><a class="modify-notice" href="javascript:;" ><?php if(!empty($notice)){ echo '修改';} ?></a></li>
				<li class="cancel-notice" style="display: none;"><a id="cancel-notice" href="javascript:;">取消</a></li>
			</ul>
			<?php if(!empty($notice)){?><span><?php echo date('Y-m-d',$curTime);?></span><?php }?>
		</div>
		<div class="clear"></div>
		<div class="review"
			<?php if($this->apply_status==1) echo 'style="display:none;"';?>>
			<img
				src="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/images/s_4.png" />
		</div>
		<!--历史公告-->
			<div id="no-men" style="display: none; " >
			<div class="export-add"></div>
			<div class="menber-cont" >
				<div class="menber-mian">
					<dl class="member-dl">
						<dt>历史公告</dt>
						<dd></dd>
					</dl>
					<ul class="menber-one">
						<li>姓名</li>
						<li>内容</li>
						<li>更新时间</li>
						<li>创建时间</li>
					</ul>
					<?php if(!empty($notice[1])){?>
					<ul class="menber-two">
					<?php foreach ($notice as $key=>$con){
						if($key > 0){
						?>
					<li>
					<p><?php echo $con->sname;?></p>
					<p title="<?php echo $con->content;?>"><?php echo substr($con->content,0,18)."…";?></p>
					<p ><?php echo date('Y-m-d',$con->update_time);?></p>
					<p style="width:120px;margin-left:0px;"><?php echo date('Y-m-d',$con->created_time);?></p>
					</li>
					<?php }
						}?>
					</ul>
					<?php }else{?>
					<li style="height:100%;list-style: none;text-align: center;line-height: 100px;">
					暂无相关数据
					</li>
					<?php }?>
				</div>
		</div>		
			</div>
	</div>
</div>




























