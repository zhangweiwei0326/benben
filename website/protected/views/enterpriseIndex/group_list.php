<script type="text/javascript"
	src="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/independent/js/personal_public.js"></script>
	<style>
/* .m-m-coe-dl dt {width: 532px;} */
.m-m-coe-dl dd label {width: 80px;}
.m-m-coe-dl dd input {width:60px;}
.m-m-coe-dl dd {width:18%;}
.m-m-coe-dl dd label .edit,.delete{
cursor:pointer;width:104px;height:44px;color:#59BDF2;
}
</style>
			<!--right-->
			<div class="p-main-r fl" id="p-main-r">
				<em class="pm-r-bnn"><a href="javascript:;"><img src="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/images/rbnner.jpg"/></a></em>
				<div class="p-mian-cont">
					<dl class="pm-r-dl">
						<dt><span><a href="<?php echo Yii::app()->createUrl("enterpriseIndex/index");?>">首页</a></span><font> > </font><span><a href="<?php echo Yii::app()->createUrl("enterpriseIndex/member");?>">成员管理</a></span><font> > </font><i><?php switch ($level){case '1':echo '一级分组编辑';break;case '2':echo '二级分组编辑';break;case '3':echo '三级分组编辑';break;case '4':echo '四级分组编辑';break;}?></i></dt>
					</dl>
					<div class="clear"></div>
					<div class="m-member-compile">
						<h1 class="m-m-coe-title"><span><?php switch ($level){case '1':echo '一级分组编辑';break;case '2':echo $higher_group->name.'分组编辑';break;case '3':echo $higher_group->name.'分组编辑';break;case '4':echo $higher_group->name.'分组编辑';break;}?></span></h1>
						<div class="group_content" data="<?php echo $parent_id.'|'.$level;?>">
						<?php if(empty($groups)){?>
						<dl class="m-m-coe-dl">
							<dt><label><?php echo $level_name;?>分组名：</label><input  name="name" class="chang-colo"   /></dt>
							<dd>
								<label>排序：</label>
								<input name="sort" value="0" placeholder=""  class="chang-colo"/>
							</dd>
							<dd style="width: 9%;">
								<label><a class="delete" href="javascript:;">删除</a></label>
								<input name="id"  value="0" type="hidden"/>
								<input name="type"  value="add" type="hidden"/>
							</dd>
						</dl>
						<?php }?>
						<?php foreach ($groups as $v){?>
						<dl class="m-m-coe-dl">
							<dt><label><?php echo $level_name;?>分组名：</label><input value="<?php echo $v->name;?>" name="name" class="chang-colo"   /></dt>
							<dd>
								<label>排序：</label>
								<input name="sort" value="<?php echo $v->sort;?>" placeholder=""  class="chang-colo"/>
							</dd>
							<dd style="width:9%;">
								<label><a class="delete" href="javascript:;">删除</a></label>
								<input name="id"  value="<?php echo $v->id;?>" type="hidden"/>
								<input name="type"  value="edit" type="hidden"/>
							</dd>
							<dd <?php if($level==$this->enterprise_group_level) echo 'style="display:none;"';?>>
								<label><a class="edit" href="<?php echo Yii::app()->createUrl("enterpriseIndex/editGroup",array('id'=>$v->id));?>">进入子分组</a></label>
							</dd>
						</dl>
						<?php }?>
						</div>
						<dl class="m-m-coe-dl" style="margin-bottom: 0;">
							<dt style="width:700px;"><label style="text-indent: 9999px;">s</label>
								<em class="add" style="width:104px;height:44px;background:#31AAE1;line-height:44px;font-size:15px;border-radius:7px;color:#fff;text-align:center;">添加</em>
								<em class="sure"><a href="javascript:;"><img src="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/images/qe_1.jpg"/></a></em>
								<a href="<?php echo Yii::app()->createUrl("enterpriseIndex/member");?>"><em style="width:104px;height:44px;background:#31AAE1;line-height:44px;font-size:15px;border-radius:7px;color:#fff;text-align:center;">返回</em></a>
							</dt>
						</dl>
						
					</div>

					<!--end-->
				</div>
			</div>
<script type="text/javascript">
$(function(){
	//添加分组
	$(".add").click(function(){
		$(".group_content").append('<dl class="m-m-coe-dl"><dt><label>'+'<?php echo $level_name;?>'+'分组名：</label><input  name="name" class="chang-colo"   /></dt><dd><label>排序：</label><input name="sort" value="0" placeholder=""  class="chang-colo"/></dd><dd><label><a class="delete" href="javascript:;">删除</a></label><input name="id"  value="0" type="hidden"/><input name="type"  value="add" type="hidden"/></dd></dl>');
	});

	//删除分组
	$(document).on("click",'.delete',function(){
		var obj=$(this);
		layer.confirm('确定删除吗？不可恢复', {
		    btn: ['确定','取消'] //按钮
		}, function(){
			obj.parent("label").parent("dd").parent("dl").hide();
			obj	.parent("label").parent("dd").children("input[name='type']").val('delete');
			layer.msg('已删除', {icon: 1});
		}, function(){
		});
	});

	//点击确定修改
	$(".sure").click(function(){
		var data=new Array();
		var sign;
		$(".group_content").find(".m-m-coe-dl").each(function(){
			var id=$.trim($(this).find("input[name='id']").val());
			var type=$.trim($(this).find("input[name='type']").val());
			var name=$.trim($(this).find("input[name='name']").val());
			var sort=$.trim($(this).find("input[name='sort']").val());
			if(name=='' && type!='delete'){
				   layer.msg('有分组名没有添加',{icon:2,btn:['知道了'],time:10000,});sign=1;return false;
			 }
			 data.push(id+'|'+type+'|'+name+'|'+sort);
		});
		var parent_level=$(".group_content").attr('data');
		if(sign==1)return false;
		
		$.ajax({
			type : "POST",
			url : "/index.php/enterpriseIndex/editGroup",
			data : {'data' : data,'parent_level':parent_level,},
			async : true,
			cache : false,
			dataType : "json",
			beforeSend : function() {
			},
			success : function(data) {
					console.info(data);
				if (data['msg'] == "success") {
					layer.msg('编辑成功',{icon:2,btn:['知道了'],time:10000,});
					location.reload();
				}
				if (data['msg'] == "failed") {
					layer.msg('出错啦！再试一次',{icon:2,btn:['知道了'],time:10000,});
				}
			}
		});
	});
	
// 	$(".sure").click(function(){
// 			var name=$.trim($("input[name='name']").val());
// 			if(name==''){
// 					layer.msg('分组名称不能为空！',{icon:2,btn:['知道了'],time:10000,});
// 					$("input[name='name']").val('');
// 					return false;
// 			}
// 			$.ajax({
// 				type : "POST",
// 				url : "/index.php/enterpriseIndex/AddGroup",
// 				data : {'name' : name,},
// 				async : true,
// 				cache : false,
// 				dataType : "json",
// 				beforeSend : function() {
// 				},
// 				success : function(data) {
// // 					console.info(data);
// 					if (data['msg'] == "success") {
// 						layer.msg('分组添加成功！',{icon:2,btn:['知道了'],time:10000,});
// 						window.location.href="";
// 					}
// 					if (data['msg'] == "failed") {
// 						layer.msg('出错啦！再试一次',{icon:2,btn:['知道了'],time:10000,});
// 					}
// 				}
// 			});
// 	});



	
});
</script>


