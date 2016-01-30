<link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/css/Personal_7.css" />
<script type="text/javascript" src="/themes/enterprise/js/LocalResizeIMG.js"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/themes/css/jquery.ui.css" />
	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/themes/js/jquery.ui.js"></script>	
	
<script>
			$(function(){
				
				//
// 				$('.pm-lul li').click(function(){
// 					$('.pm-lul li').removeClass('pm-li-ck');
// 					$('.pm-lul-li2').css('height','42px');
// 					$(this).addClass('pm-li-ck');
// 					if($(this).hasClass('pm-lul-li2')){
// 						$(this).css('height','127px');
// 					}
// 				});
				
				//end
				
				$('.m-m-coe-dl i').click(function(){
					$('.m-m-coe-dl i').removeClass('chang-yes');
					$(this).addClass('chang-yes');
				});

				$(".search").click(function(){
					$("form").submit();
				})

				$(".del").click(function(){
					$("input.created_time").val("");
					var access_level = document.getElementById("access_level"); 
					access_level.options[0].selected=true;   
				})

				$('.created_time').datepicker({
					dateFormat : 'yy-mm-dd',
					dayNamesMin : ['日','一','二','三','四','五','六'],
					monthNames : ['一月','二月','三月','四月','五月','六月','七月','八月','九月','十月','十一月','十二月'],
					monthNamesShort : ['一月','二月','三月','四月','五月','六月','七月','八月','九月','十月','十一月','十二月'],
					altField : '#abc',
					altFormat : 'dd-mm-yy',
					showWeek : true,
					weekHeader : '周',
					firstDay : 0,
					navigationAsDateFormat : true,
					showMonthAfterYear : true,
					closeText : '关闭',
					currentText : '今天dd',
					hideIfNoPrevNext : true,
					yearRange : '1950:2020',
					showOtherMonths : true,
//					onSelect: function(dateText,inst){
//						if($(this).attr('id') == 'created_time2'){
//							$(this).val(dateText + " 23:59:59");
//						}else{
//							$(this).val(dateText + " 00:00:00");
//						}
//					}

				});
				//end
			})
		</script>

<!--right-->
<div class="p-main-r fl" id="p-main-r">
	<em class="pm-r-bnn"><a href="#"><img
			src="/themes/enterprise/images/rbnner.jpg" /></a></em>
	<div class="p-mian-cont">
		<dl class="pm-r-dl">

			<dt>
				<span><a
					href="<?php echo Yii::app()->createUrl('enterpriseIndex/index')?>">首页</a></span><font>
					> </font><i>大喇叭管理</i>
			</dt>
		</dl>
		<div class="clear"></div>
		<div class="m-member-compile">
			<h1 class="m-m-coe-title">
				<span>大喇叭管理</span>
							<?php if( ($user=='admin'&&$level->broadcast_per_month > 0&&$level1->broadcast_available>0)||($user=='supper'&&$level1->broadcast_available>0)) {?>
							<span class="new_lab">新建大喇叭</span>
							<?php }?>
						</h1>

			<form method="get"
				action="<?php echo Yii::app()->createUrl('enterpriseBroadcast/index')?>">				
    						<dl class="m-m-coe-dl">
    				<?php if($user == 'admin') {?>				
					<dt>
						<label>查阅等级：</label> <select class="chang-selt access_level" name="access_level" id="access_level">
							<option value="-1">--请选择--</option>
    									<?php for ($i = 1; $i <= $access_level; $i++){?>
    									<?php if($_REQUEST['access_level'] == $i){?>
    									<option selected="selected" value="<?php echo $i?>"><?php echo $i?></option>
    									<?php }else{ ?>
    									<option value="<?php echo $i?>"><?php echo $i?></option>
    									<?php }?>
    									<?php }?>
    								</select>
					</dt>
    							<?php }?>
    							<dd>
						<label>时间：</label><input  value="<?php echo $_REQUEST['created_time1']?>" name="created_time1" style="width:110px;" class="created_time">
					</dd>
					<dd>
						<label>至：</label><input  value="<?php echo $_REQUEST['created_time2']?>" name="created_time2" style="width:110px;" class="created_time">
					</dd>
					<dd class="del">清空选项</dd>
					<dd class="search">查询</dd>
				</dl>
			</form>
			<dl class="con_list">
				<dt class="con_list_dt">发送者</dt>
				<dd class="con_list_fdd">备注</dd>
				<dd class="con_list_sdd">大喇叭内容</dd>
				<dd class="con_list_tdd">查询等级</dd>
				<dd class="con_list_ldd">操作</dd>
			</dl>
			<?php if($user=='admin'){
					foreach ($items as $item){
					 if($access_level >= $item->level ){?>
								<dl class="con_list com">
		            				<dt class="con_list_dt"><?php if(empty($item->mname)&&empty($item->mremark_name)){echo '&nbsp';}else{echo $item->mname ? $item->mname:$item->mremark_name;}?></dt>
		            				<dd class="con_list_fdd"><?php echo $item->remarks ? $item->remarks : "　　　";?></dd>
		            				<dd class="con_list_sdd"><?php echo mb_strlen($item->content, 'utf8') > 20 ? mb_substr($item->content, 0, 20, 'utf8').'...' : $item->content;?></dd>
		            				<dd class="con_list_tdd"><?php echo $item->level;?></dd>
		            				<dd class="con_list_ldd col_bule"
		            					receiver="<?php echo $item->nname?$item->nname:$item->nremark_name?>"
		            					content="<?php echo $item->content?>"
		            					attachment="<?php if(!empty($item->attachment)){
				            							echo $item->attachment;
				            							}else{echo '';}?>">查看详情</dd>
		            			</dl>
						<?php } } }else{ ?>
							<?php foreach ($items as $item){?>
								<?php $this->widget('WidgetBroadCast', array('id' => $item->id, 'enterprise_id' => $item->enterprise_id, 'member_id' => $item->member_id, 'apply_id' => $item->apply_id))?>
							<?php }?>
					<?php }?>			
								
								
								
		         <div style="overflow: hidden; padding-right: 70px;">
    											<div class="g-mefr-bfy">
			<?php
			$page = intval ( $_GET ['page'] ) ? intval ( $_GET ['page'] ) : 1;
			$url = explode ( "?", Yii::app ()->request->getUrl () );
			$link = Yii::app ()->request->hostInfo . $url [0] . "?";
		   echo '<ul>' . $this->textPage ( $pages->pageCount , $page, $link ) . '</ul>';
			?>
			<?php
			$path = Yii::app()->basePath.'/views/layouts/searchPages.php';
			require_once ($path);
			?>
			</div>
			</div>
			<!--分页end-->
		</div>

		<!--end-->
	</div>
</div>
</div>
<div class="export-into  hide"></div>
<div class="export-into-cent hide">
	<div class="pop_up">
		<p class="pop_p_1 pop_p">
			内容详情<img class="back" src="/themes/enterprise/images/back.png">
		</p>
		<ul class="pop">
			<li class="first_li"><span>发送人： <font class="fon sender">吕小布</font>
			</span></li>
			<li class="last_li"><span>发送对象：<font class="fon receiver">吕小豪</font></span>
			</li>
		</ul>
		<p class="connect broad_cast_content">正文</p>
		<ul style="margin-left:22px;margin-top:10px" class="pic_attach">
				
		</ul>
		<a href="javascript:void(0);" class="comfirm broad_cast_confirm">确定</a>
	</div>
</div>


<div class="new_horn hide">
	<div class="pop_up">
		<p class="pop_p_1 pop_p">
			新建大喇叭<img class="back" src="/themes/enterprise/images/back.png">
		</p>
		<dl class="add_new_horn">
			<dt>
				<label>发送对象：</label> <select class="select_obj receiver_member">
								<?php foreach ($receiver as $value){ ?>
								<?php if($value->member_id != 0){?>
								<option value="<?php echo $value->member_id ?>"><?php echo $value->name ? $value->name : $value->remark_name?></option>
								<?php }?>
								<?php }?>
							</select>
			</dt>
			<dd>
				<span style="float: left">内容文字：</span>
				<textarea placeholder="200字以内" class="send_content"  onkeyup="checkLen(this)"></textarea>
			</dd>
			<dd style="height: 85px;">
				<span style="float: left">内容图片：</span> <i
					style="float: left; margin-right: 10px;" class="upload_img"> <img
					src="/themes/enterprise/images/add_pic.png">
					<p
						style="color: #4795e0; text-align: center; font-style: normal; padding-top: 4px; cursor: pointer; display: none;"
						class="img_del">删除</p>
				</i> <i class="upload_img" style="float: left; margin-right: 10px;">
					<img src="/themes/enterprise/images/add_pic.png">
					<p
						style="color: #4795e0; text-align: center; font-style: normal; padding-top: 4px; cursor: pointer; display: none;"
						class="img_del">删除</p>
				</i> <i class="upload_img" style="float: left; margin-right: 10px;">
					<img src="/themes/enterprise/images/add_pic.png">
					<p
						style="color: #4795e0; text-align: center; font-style: normal; padding-top: 4px; cursor: pointer; display: none;"
						class="img_del">删除</p>
				</i> <input type="file" id="file" style="display: none;"> <input
					type="hidden" name="images" value=""> <a href="javascript:void(0);"
					class="send">发送</a>
			</dd>

		</dl>
	</div>
</div>



<script type="text/javascript">
	$('.col_bule').on("click",function(){
		$('.export-into').removeClass('hide');
		$('.export-into-cent').removeClass('hide');
		var sender = $(this).parent().children('.con_list_dt').html();
		var receiver = $(this).attr("receiver");
		var content = $(this).attr('content');
		var attach=$(this).attr('attachment');
		if(attach!=''){
// 			attach='';
			var arr=attach.split('|');
			attach='';
			for(var i=0;i<arr.length-1;i++){
				attach+='<li style="float: left; margin-right: 10px;"><img src="'+arr[i]+'"  style="width:60px;height:60px;"></li>';
			}	
		}
		$(".sender").html(sender);
		$(".receiver").html(receiver);
		$(".broad_cast_content").html(content);
		$('.pic_attach').html(attach);
	});
	$('.new_lab').click(function(){
		$('.export-into').removeClass('hide');
		$('.new_horn').removeClass('hide');
	});
	$('.back, .broad_cast_confirm').click(function(){
		$('.export-into').addClass('hide');
		$('.export-into-cent').addClass('hide');
		$('.new_horn').addClass('hide');
		$(".upload_img img").attr("src", "/themes/enterprise/images/add_pic.png");
		$("input[name=images]").val("");
	});

	var upload_img = "";
	
	$(".upload_img img").click(function(){
		$("#file").click();
		upload_img = $(this);
	})
	
	    $('#file').localResizeIMG({
            before: function(){
            },
            success: function(result){
                var img = new Image();
                img.src = result.base64;
                var name = result.name;
                console.log(result);
                $('#formFile').html(result.base64.substr(22));
                //发送到服务端
                $.post('/index.php/enterpriseBroadcast/uploadImage', {
                    'name': name,
                    'head': 1,
                    formFile: result.base64.substr(22)
                }, function(data){
                    if(data == -1){
                    	layer.msg('图片上传失败', {icon: 2,time:10000,btn: ['知道了']});
                     }else{
                    	 upload_img.attr("src", data);
                    	 upload_img.next("p").show();
                    	 $("input[name=images]").val($("input[name=images]").val() + data+ "|");
                     }
                });
            }
    });

    $(".img_del").click(function(){
    	$("input[name=images]").val($("input[name=images]").val().replace($(this).prev().attr("src") + "|", ""));
		$(this).prev().attr("src", "/themes/enterprise/images/add_pic.png");
		$(this).hide();
     })
     
     $(".send").click(function(){
			$.ajax({
				url : '/index.php/enterpriseBroadcast/createBroadCast',
				type : 'POST',
				data : {
					receiver : $(".receiver_member").val(),
					content : $(".send_content").val(),
					images : $("input[name=images]").val()
				},
				success : function(data){
					if(data == 200){
						    layer.msg('新建大喇叭成功', {icon: 1,time:10000,btn: ['知道了']});
						setTimeout(function(){
							 location.reload();
						}, 1000);
					}else{
						layer.msg('新建大喇叭失败', {icon: 2,time:10000,btn: ['知道了']});
					}
				}
			})
      })

      function checkLen(obj) {  

            var maxChars = 200;//最多字符数  
            
            if (obj.value.length > maxChars)  obj.value = obj.value.substring(0,maxChars);  
            
            var curr = maxChars - obj.value.length;  
            
            document.getElementByIdx_x("count").innerHTML = curr.toString(); 

	} 
     
	</script>




























