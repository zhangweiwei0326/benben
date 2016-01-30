<script type="text/javascript"
	src="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/independent/js/personal_public.js"></script>
<script type="text/javascript"
	src="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/independent/js/tools.js"></script>
			<!--right-->
			<div class="p-main-r fl" id="p-main-r">
				<em class="pm-r-bnn"><a href="javascript:;"><img src="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/images/rbnner.jpg"/></a></em>
				<div class="p-mian-cont">
					<dl class="pm-r-dl">
						<dt><span><a href="<?php echo Yii::app()->createUrl("enterpriseIndex/index");?>">首页</a></span><font> > </font><span><a href="<?php echo Yii::app()->createUrl("enterpriseIndex/member");?>">成员管理</a></span><font> > </font><i><?php if(empty(intval($_GET['id']))) echo '添加分组'; else  echo '编辑分组';?></i></dt>
					</dl>
					<div class="clear"></div>
					<div class="m-member-compile">
						<h1 class="m-m-coe-title"><span><?php if(empty(intval($_GET['id']))) echo '添加分组'; else  echo '编辑分组';?></span></h1>
						<dl class="m-m-coe-dl">
						<input name="id"  type="hidden" value="<?php echo intval($_GET['id'])?intval($_GET['id']):0;?>" />
						<input name="enterprise_type"  type="hidden" value="<?php echo $this->enterprise_type;?>" />
						<input name="listUrl" type="hidden" value="<?php echo $lastUrl;?>" />
							<dt><label>一级分组名：</label><input value="<?php echo $enterpriseMember->benben_id;?>" name="name" class="chang-colo"   /></dt>
						</dl>
						<dl class="m-m-coe-dl" style="margin-bottom: 0;">
							<dt><label style="text-indent: 9999px;">s</label>
								<em class="sure"><a href="javascript:;"><img src="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/images/qe_1.jpg"/></a></em>
								<em><a href="<?php echo $lastUrl;?>"><img src="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/images/qe_3.jpg"/></a></em>
							</dt>
						</dl>
						
					</div>

					<!--end-->
				</div>
			</div>
<script type="text/javascript">
$(function(){
	$(".sure").click(function(){
			var name=$.trim($("input[name='name']").val());
			if(name==''){
					layer.msg('分组名称不能为空！',{icon:2,btn:['知道了'],time:10000,});
					$("input[name='name']").val('');
					return false;
			}
			$.ajax({
				type : "POST",
				url : "/index.php/enterpriseIndex/AddGroup",
				data : {'name' : name,},
				async : true,
				cache : false,
				dataType : "json",
				beforeSend : function() {
				},
				success : function(data) {
// 					console.info(data);
					if (data['msg'] == "success") {
						layer.msg('分组添加成功！',{icon:2,btn:['知道了'],time:10000,});
						window.location.href="<?php echo $lastUrl;?>";
					}
					if (data['msg'] == "failed") {
						layer.msg('出错啦！再试一次',{icon:2,btn:['知道了'],time:10000,});
					}
				}
			});
	});
});
</script>


