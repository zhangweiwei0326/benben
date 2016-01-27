<div class="main_right_content">
	<ol class="breadcrumb main_right_content_breadcrumb">
		<li><a href="<?php echo Yii::app()->createUrl('industry/index')?>">行业字典管理</a></li>
		<li><a href="javascript:void"><?php echo '增加行业字典';?></a></li>
		<div class="main_right_content_content_block_action_add">
			<a class="btn btn-success backurl" href="javascript:void(0)" goback=" <?php echo $goback ? $goback : -1;?>">返回</a>
		</div>
	</ol>

	<div class="main_right_content_content">
			<div class="form-group">
					<label class="col-sm-2 control-label" for="Industry_name">行业名称</label>
					<div class="col-sm-8">
						<input id="Industry_name" class="form-control" value="<?php echo $parent_name?>"  type="text" name="Industry[name]" maxlength="255" size="60">
					</div>
			</div>
			<div class="form-group form-group-center">
				<button class="btn btn-success btn-lg" type="button">　确定　</button>
				<a class="btn btn-default backurl" type="button" goback=" <?php echo $goback ? $goback : -1;?>">　取消　</a>
			</div>
	</div>
		
</div>
<script>
	//获取参数
	function GetQueryString(name)
	{
	     var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
	     var r = window.location.search.substr(1).match(reg);
	     if(r!=null)return  unescape(r[2]); return null;
	};
 $(".btn-lg").click(function(){
	     var name = $("#Industry_name").val().trim();
	     var parent_id = GetQueryString("parent_id");
	     var level = GetQueryString("level");			
		 if(name == ""){
		      alert("请输入名称");	
		      return false;
	     }else{
     		var url="<?php echo Yii::app()->createUrl("industry/save")?>";//json输出
     		$.post(url,{name:name, 
     					parent_id:parent_id,
     					level:level,   					
                },function(data){
             if (data.status==1) {
                 alert("操作成功!");
             }else{
                 alert("网络错误！");
             }
         },'json');

       } 
	 });
 
</script>

