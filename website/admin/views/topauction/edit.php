<?php
	Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/themes/css/content.css");
?>
<?php

?>
	<?php if($msg) {?>
				<div class="alert alert-danger alert-dismissible" role="alert">
				  <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				  <strong>警告！</strong> <?php echo $msg;?>
				</div>
				<?php }?>
		<?php $form=$this->beginWidget('CActiveForm', array(
			'id'=>'news-form',
			'htmlOptions'=>array('enctype'=>'multipart/form-data', 'class'=>'form-horizontal'),
			'enableAjaxValidation'=>false,
		)); ?>
<div class="main_right_content">
	<ol class="breadcrumb main_right_content_breadcrumb">
		<li><a href="<?php echo Yii::app()->createUrl('topAuction/index')?>">拍卖管理</a></li>
		<li><a href="#">拍卖编辑</a><?php echo  strtotime("2009-1-22 11:22:00") ?></li>
	</ol>
	<div class="main_right_content_content">
			<div style="margin-left: 200px;font-size: 15px;">
				<div class="form-group">
                        <div class="form-group col-md-12" style="padding:0 0 0 5px;float:left;">
                            <label  for="sex" style="float:left;margin-top:7px;">拍卖区域:</label>		            
							<label style="padding:7px 20px 0 0;margin-left: 50px;"  for="Enterprise_province" ><?php echo $areaInfo[$item[0]->province]; ?></label>	
							<label style="padding:7px 20px 0 0"  for="Enterprise_city" ><?php echo $areaInfo[$item[0]->city]; ?></label>
							<label for="Enterprise_area"  style="padding:7px 20px 0 0" ><?php echo $areaInfo[$item[0]->area]; ?></label>
						</div>
				</div>
						
				<div class="form-group">
					    <div class="form-group" style="padding:0 0 0 5px;">
                        	<label  for="benben_id" style="float:left;margin-top:7px;">行　　业:</label>  
	                    	<label style="padding:7px 20px 0 0;margin-left: 50px;"  for="Enterprise_industry" ><?php echo $industryInfo[$item[0]->industry]; ?></label>	
                    	</div>				
                 </div>
				<div class="form-group">
					<div class="form-group" style="padding: 0 0 0 5px;">
						<label for="start_time" style="float: left; margin-top: 7px;">开始时间:</label>
						<div class="col-sm-8" style="margin-bottom: 10px;">
							<input type="text" class="form-control "
								name="start_time" id="start_time"
								value="<?php echo $item[0]->start_time ?>"onclick="SelectDate(this,'yyyy/MM/dd hh:mm:ss')">
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="form-group" style="padding: 0 0 0 5px;">
						<label for="end_time" style="float: left; margin-top: 7px;">结束时间:</label>
						<div class="col-sm-8" style="margin-bottom: 10px;">
							<input type="text" class="form-control"
								name="end_time" id="end_time"
								value="<?php echo $item[0]->end_time ?>" onclick="SelectDate(this,'yyyy/MM/dd hh:mm:ss')" >
						</div>
					</div>
				</div>	
				<div class="form-group" >
					<div style="padding: 0 0 0 5px;">
						<label style="float: left;margin-left: -15px;">起拍价格:</label>
						<input type="text" class="new-input" style="margin-left: 25px;border:1px solid #CCC;" name="start_price" id="start_price"
								value="<?php echo $item[0]->start_price ?>"/>
					</div>
					
				</div>	
				<div class="form-group">
					<div style="padding: 0 0 0 5px;">
						<label style="float: left;margin-left: -15px;">最小加价:</label>
						<input type="text" class="new-input" style="margin-left: 25px;border:1px solid #CCC;" name="add_step" id="add_step"
								value="<?php echo $item[0]->add_step ?>"/>
					</div>
				</div>
				<div class="form-group">
					    <div class="form-group" style="padding: 0 0 0 5px;">
                        	<label  for="guarantee" style="float:left;margin-top:7px;">保证金:</label>
                        	<input style="margin-left: 25px;border:1px solid #CCC;" name="guarantee" id="guarantee" value="<?php echo $item[0]->guarantee?>" />
<!--                   			<label  for="guarantee" style="margin-top:7px;margin-top:7px;font-family: cursive;color: cadetblue;">(1,2,3分别代表保证金的类型)</label>-->
                    	</div>				
                 </div>
				<div class="form-group">
					    <div class="form-group" style="padding: 0 0 0 5px;">
                        	<label  for="place" style="float:left;margin-top:7px;">置顶区域:</label>
                        	<input style="margin-left: 25px;border:1px solid #CCC;" name="place" id="place" value="<?php echo $item[0]->place?>" />
							<label  for="place" style="margin-top:7px;font-family: cursive;color: cadetblue;">(1,2,3分别代表置顶区域一,二,三)</label>
                        </div>    
    				
                 </div>
				<div class="form-group">
					<div class="form-group" style="padding: 0 0 0 5px;">
						<label for="top_start_period" style="float: left; margin-top: 7px;">置顶开始时间:</label>
						
						<div class="col-sm-8" style="margin-bottom: 10px;">
							<input type="text" class="form-control "
								name="top_start_period" id="top_start_period"
								value="<?php echo $item[0]->top_start_period ?>"onclick="SelectDate(this,'yyyy/MM/dd hh:mm:ss')">
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="form-group" style="padding: 0 0 0 5px;">
						<label for="top_end_period" style="float: left; margin-top: 7px;">置顶结束时间:</label>
						<div class="col-sm-8" style="margin-bottom: 10px;">
							<input type="text" class="form-control"
								name="top_end_period" id="top_end_period"
								value="<?php echo $item[0]->top_end_period ?>" onclick="SelectDate(this,'yyyy/MM/dd hh:mm:ss')">

						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="form-group" style="padding: 0 0 0 5px;">
						<label for="is_close" style="float: left; margin-top: 7px;">开关:</label>
						<div class="col-sm-8" style="margin-bottom: 10px;">
							<input type="text" class="form-control is_close" id="is_close"
								name="is_close" id="is-close" placeholder="打开为0，关闭为1"
								value="<?php echo $item[0]->is_close ?>">
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="form-group" style="padding: 0 0 0 5px;">
						<label for="is_paid" style="float: left; margin-top: 7px;">更改是否付款:</label>
						<div class="col-sm-8" style="margin-bottom: 10px;">
							<input type="text" class="form-control is_paid" id="is_paid"
								name="is_paid" id="is-paid" placeholder="未付款为0，已付款为1"
								value="<?php echo $item[0]->is_paid ?>">
						</div>
					</div>
				</div>
				
			</div>						
			<div class="form-group form-group-center">
				<a class="btn btn-success btn-lg enter-save" name="<?php echo $item[0] ->auction_id ?>">保存</a>
				<a class="btn btn-default btn-lg backurl" type="button" goback="<?php echo $_GET['back'] ? $_GET['back'] : -1;?>">　取消　</a>
			</div>
		<?php
 $this->endWidget (); ?>
	</div>
</div>
<script type="text/javascript">
		//点击确定提交
	    $(".enter-save").on("click",function(){
	    	//获取数据
		    var _this=this;
		    var auction_id=_this.name;
		    var start_time =Date.parse(new Date($("#start_time").val()))/1000;
		    var end_time =Date.parse(new Date($("#end_time").val()))/1000;
		    var start_price =$("#start_price").val();
		    var add_step =$("#add_step").val();
		    var guarantee =$("#guarantee").val();
		    var place =$("#place").val();
		    var top_start_period =Date.parse(new Date($("#top_start_period").val()))/1000;
		    var top_end_period =Date.parse(new Date($("#top_end_period").val()))/1000;
		    var is_close =$("#is_close").val();
		    var is_paid =$("#is_paid").val();
		    
	    url="<?php echo Yii::app()->createUrl("topAuction/saveAuction")?>";//json输出

	            if(start_time > end_time || top_start_period > top_end_period){
	            	alert("时间选择错误，请重新选择!");
	            }else{
	            	 $.post(url, {	"auction_id":auction_id,
        				"start_time":start_time,
        				"end_time":end_time,
        				"start_price":start_price,
        				"add_step":add_step,
        				"guarantee":guarantee,
        				"place":place,
        				"top_start_period":top_start_period,
        				"top_end_period":top_end_period,
        				"is_close":is_close,
        				"is_paid":is_paid
    				}, function(data){
			            if (data.status==1) {
			               alert("保存成功");
			            }else{
			               alert("网络错误！");
			            };
        				},'json');
        		}

    });
</script>

