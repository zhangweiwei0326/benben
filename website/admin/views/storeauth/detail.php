<?php
	Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/themes/css/content.css");
?>
<?php

?>
<div class="main_right_content">
	<ol class="breadcrumb main_right_content_breadcrumb">
		<li><a href="<?php echo Yii::app()->createUrl('storeAuth/index')?>">认证管理</a></li>
		<li><a href="#">认证详情</a></li>
	</ol>
	<div class="main_right_content_content">
			<div style="margin-left: 200px;font-size: 15px;">			
                <div class="col-md-12" style="margin:5px 0 0 5px;float:left;">
                    <label  for="shopNum" style="float:left;margin-top:7px;font-size: larger;color: brown;">店铺号:</label>
					<label  for="shopNum" style="float:left;margin-left: 50px;font-family: cursive;color: cornflowerblue;font-size: x-large;"><?php echo "hz".$item[0]->store_no;?></label>
				</div>
				<div class="col-md-12" style="margin:5px 0 0 5px;">
                	<label  for="trueName" style="float:left;margin-top:7px;font-size: larger;color: brown;">真实姓名:</label>
                	<label  for="trueName" style="float:left;margin-left: 50px;font-family: cursive;color: cornflowerblue;font-size: x-large;"><?php echo $item[0]->real_name; ?></label>
	
                </div>
				<div class="col-md-12" style="margin:5px 0 0 5px;">
					<label for="id_card" style="float: left; margin-top: 7px;font-size: larger;color: brown;">身份证号:</label>						
					<label for="id_card" style="float: left;margin-left: 50px;font-family: cursive;color: cornflowerblue;font-size: x-large;"><?php echo $item[0]->id_card; ?></label>						
				</div>
				<div class="col-md-12" style="margin:5px 0 0 5px;">
					<label for="poster_front" style="float: left; margin-top: 7px;font-size: larger;color: brown;">身份证正面:</label>	
					<img style="margin-left: 50px;" src="<?php echo $item[0]->poster_front; ?>">					
				</div>	
				<div class="col-md-12" style="margin:5px 0 0 5px;">
					<label style="float: left;margin-top: 7px;font-size: larger;color: brown;">身份证反面:</label>
					<img style="margin-left: 50px;" src="<?php echo $item[0]->poster_back; ?>">
				</div>		
				<div class="col-md-12" style="margin:5px 0 0 5px;">
					<label style="float: left;margin-top: 7px;font-size: larger;color: brown;">营业执照:</label>
					<img style="margin-left: 50px;" src="<?php echo $item[0]->poster_licence; ?>">					
				</div>
				<div class="col-md-12" style="margin:5px 0 0 5px;">
                    <label  for="guarantee" style="float:left;margin-top:7px;font-size: larger;color: brown;">单位名称:</label>
                    <label for="id_card" style="float: left;margin-left: 50px;font-family: cursive;color: cornflowerblue;font-size: x-large;"><?php echo $item[0]->company; ?></label>	
                </div>		
  				<div class="col-md-12" style="margin:5px 0 0 5px;">
                    <label  for="place" style="float:left;font-size: larger;color: brown;">类型:</label>
                    <label for="id_card" style="float: left;margin-left: 50px;font-family: cursive;color: cornflowerblue;font-size: x-large;"><?php if($item[0]->type==1){echo "个人";}
                    if($item[0]->type==2){echo "商家";} ?></label>	   
                </div> 
				<div class="col-md-12" style="margin:5px 0 0 5px;">
					<label for="top_start_period" style="float: left;font-size: larger;color: brown;">状态:</label>
					<label for="id_card" style="float: left;margin-left: 50px;font-family: cursive;color: cornflowerblue;font-size: x-large;"><?php if($item[0]->status==0){echo "待审核";}
					if($item[0]->status==1){echo "审核未通过";}if($item[0]->status==2){echo "审核通过";}?></label>						
				</div>
				<div class="col-md-12" style="margin:5px 0 0 5px;">
					<label for="top_end_period" style="float: left;font-size: larger;color: brown;">提交时间:</label>
					<label for="id_card" style="float: left;margin-left: 50px;font-family: cursive;color: cornflowerblue;font-size: x-large;">
					<?php echo date('Y-m-d H:i:s',$item[0]->time); ?></label>						
				</div>
						
			<div class="form-group-center">
				<?php  
					if($item[0]->status==0){?>
						<a class="btn btn-success btn-lg acceptAuth"style="float:left;margin-left: 100px;" name="<?php echo $item[0]->id; ?>">通过</a>
						<a class="btn btn-danger btn-lg refuseAuth"style="float:left;margin-left: 100px;"name="<?php echo $item[0]->id; ?>">拒绝</a>
				<?php } ?>
				<a class="btn btn-primary btn-lg backurl" style="float:left;margin-left: 200px;" type="button" goback="<?php echo $_GET['back'] ? $_GET['back'] : -1;?>">　返回　</a>
			</div>
	</div>
</div>
<script type="text/javascript">
//点击通过
	$(".acceptAuth").on("click",function(){
        var _this=this;
        var id=_this.name;
        // var deal_time=new Date().getTime()/1000;
        var ob=confirm("您确定通过认证？");
        var url="<?php echo Yii::app()->createUrl("storeAuth/acceptAuthentication")?>";//json输出
        if(ob){
            $.post(url,{id:id
                        // deal_time:deal_time
                    },function(data){
                 if (data.status==1) {
                     $(_this).parent().parent().find(".status").text("审核通过");
                     $(_this).next().hide();
                     $(_this).hide();
                     alert("操作成功!");
                 }else{
                     alert("网络错误！");
                 }
            },'json');
        }
    });
    //点击拒绝	
	$(".refuseAuth").on("click",function(){
        var _this=this;
        var id=_this.name;
        //var deal_time=new Date().getTime()/1000;
        var ob=confirm("您确定拒绝认证？");
        var url="<?php echo Yii::app()->createUrl("storeAuth/refuseAuthentication")?>";//json输出
        if(ob){
            $.post(url,{id:id
                        //deal_time:deal_time
                    },function(data){
                 if (data.status==1) {
                     $(_this).parent().parent().find(".status").text("审核未通过");
                     $(_this).prev().hide();
                     $(_this).hide();
                     alert("操作成功!");
                 }else{
                     alert("网络错误！");
                 }
            },'json');
        }
    });
	
</script>


