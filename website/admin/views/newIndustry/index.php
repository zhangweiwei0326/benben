<?php
/* @var $this IndustryController */
/* @var $dataProvider CActiveDataProvider */
// Yii::app ()->clientScript->registerCssFile ( Yii::app ()->request->baseUrl . "/themes/css/global.css" );
$create_url=Yii::app()->createUrl('newIndustry/create');
?>


<div class="main_right_content">
	<div class="main_right_content_title">
			<div class="main_right_content_title_font">行业字典管理</div>
	</div>
	<span>
	</span> 
	<div id="main" style="margin:50px 0 0 50px;font-size: larger;">
		<div id="sidetree">
		  <div id="sidetreecontrol"> <a class="btn btn-success" style="font-size: x-large" href="#">收起所有行业</a> | <a class="btn btn-primary" style="font-size:x-large" href="#">展开所有行业</a> </div>
			 <ul class="treeview" id="tree">
			 	<?php 
			 		 foreach ($firstIndustry as $first) { ?>
				<!-- 一级行业-->
				<li class="expandable"><div class="hitarea expandable-hitarea"></div>
				<input type="checkbox" class="selectOne" id="<?php echo $first->id; ?>" name="<?php echo $first->level;?>" />
				<span><strong><?php echo $first->name;?></strong></span>
				<ul style="display: none;">
					<?php foreach ($secondIndustry as $second){ 
						if($second->parent_id == $first->id && $second->last==0){ 
							?>
							<!-- 二级行业-->
							<li class="expandable"><div class="hitarea expandable-hitarea"></div>
							<input type="checkbox" class="selectOne" id="<?php echo $second->id; ?>" name="<?php echo $second->level;?>"/> 
							<a ><?php echo $second->name ?> </a>
							<ul style="display: none;">
								<?php foreach ($thirdIndustry as $third){ 
									if($third->parent_id == $second->id){ 
									?>
										<!--三级行业-->
										<li>
										<input type="checkbox" class="selectOne" id="<?php echo $third->id; ?>" name="<?php echo $third->level;?>"/>
										<a ><?php echo $third->name ?></a></li>
										<!--三级行业end-->
									<?php } ?>
								<?php } ?>
							</ul>
							</li>
							<!-- 二级行业end-->
						<?php } ?>					
						<?php  if($second->parent_id == $first->id && $second->last==1){ ?>
							<li>
							<input type="checkbox" class="selectOne" name="<?php echo $second->id; ?>" level="<?php echo $second->level;?>"/> 
							<a ><?php echo $second->name ?></a></li>
						<?php } ?>
					<?php } ?>
				</ul>
				</li>
				<!-- 一级行业end-->
			 	<?php } ?>
			</ul>
		</div>
	</div>
	<div class="main_right_content" style="margin: 50px 0 50px 200px;">
		<a class="btn btn-primary" style="margin-left: 50px;height: 35px;" href="<?php echo $create_url ?>">添加主行业</a>
		<button class="btn btn-primary addChilen" style="margin-left: 50px;height: 35px;">添加子行业</button>
		<button class="btn btn-primary modifyIndu" style="margin-left: 50px;height: 35px;">修改行业名</button>
		<button class="btn btn-danger deleteIndu" style="margin-left: 50px;height: 35px;">删除该行业</button>
	</div>	
</div>
<script type="text/javascript">
	$(function() {
		$("#tree").treeview({
			collapsed: true,
			animated: "medium",
			control:"#sidetreecontrol",
			prerendered: true,
			persist: "location"
		});
		//增加子
		$(".addChilen").on('click',function(){
			if($(":checked").length!=1){
				alert("请选择一个父行业");
			}else{
				var parent_id=$(":checked")[0].id;
				var level=$(":checked")[0].name;
				if(level==3){
					alert("此为行业分支，不可再分!");
				}else{
					var url="<?php echo Yii::app()->createUrl('newIndustry/create') ?>"+"?parent_id="+parent_id+"&level="+level;
					window.location.href=url;
				}
				
			}
			
		});	
		//修改	
		$(".modifyIndu").on('click',function(){
			if($(":checked").length!=1){
				alert("请选择一个行业");
			}else{
				var id=$(":checked")[0].id;
				var url="<?php echo Yii::app()->createUrl('newIndustry/edit') ?>"+"?id="+id;
				window.location.href=url;
			}
			
		});	
		//删除
		$(".deleteIndu").on('click',function(){
			 $(".selectOne").each(function(){
			 	if(this.checked){ 
			 		var url="<?php echo Yii::app()->createUrl("newIndustry/delete")?>";
			 		$.post(url,{id:this.id,
			 					level:this.name,
			                },function(data){
			             if (data.status==1) {
			                 alert("操作成功!");
			                 location.reload();
			             }else{
			                 alert("网络错误！");
			             }
			         },'json');
			 	}
			 });
		});
	})	
</script>

