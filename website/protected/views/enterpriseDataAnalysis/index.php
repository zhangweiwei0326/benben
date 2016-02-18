		<link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/css/Personal_5.css" />
		<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/js/jquery.JPlaceholder.js" ></script>
		<script>
			$(function(){
				$('#sure').on('click',function(){
					var names_money=$(".names_money").val();
					var duration_aa=$(".duration_aa").val();
					var money=$('.money').val();
					$.post('/index.php/enterpriseIndex/getInfo',{names_money:names_money,duration_aa:duration_aa,money:money},function(data){
						if(data==1)
						{
							alert('金额或参数不正确！');
							return false;

						}
						else
						{
							$('#frm').submit();
						}

					},'json');


				});
				
				//
				$('.pm-lul li').click(function(){
					$('.pm-lul li').removeClass('pm-li-ck');
					$('.pm-lul-li2').css('height','42px');
					$(this).addClass('pm-li-ck');
					if($(this).hasClass('pm-lul-li2')){
						$(this).css('height','127px');
					}
				});
				
				//end
				
				$('.g-grouping span').toggle(function(){
					$(this).addClass('g-gsp');
					$('.g-grouping').find('dl').show();
				},function(){
					$(this).removeClass('g-gsp');
					$('.g-grouping').find('dl').hide();
					$('.g-grouping dl dt').removeClass('g-grg-dt');
					$('.g-grg-dd1').hide();
					$('.g-grg-dd2').hide();
				}
				);
				//end
				$('.g-grouping dl dt').toggle(function(){
					$(this).addClass('g-grg-dt');
					$(this).next('.g-grg-dd1').show();
					
				},function(){
					$(this).removeClass('g-grg-dt');
					$(this).next('.g-grg-dd1').hide();
					$(this).next('.g-grg-dd1 p').removeClass('g-grg-ddp');
					$(this).next('.g-grg-dd2').hide();
				});
				//end
				$('.g-grg-dd1').toggle(function(){
					$(this).children('p').addClass('g-grg-ddp');
					$(this).next('.g-grg-dd2').show();
				},function(){
					$(this).children('p').removeClass('g-grg-ddp');
					$(this).next('.g-grg-dd2').hide();
				});


				$('#no-rem').click(function(){
					$('#no-men').show()
				});
				$('#no-men .member-dl dd').click(function(){
					
					$('#no-men').hide()
				});
				//退出详情
				$('#no-rem2').click(function(){
					$('#out-ing').show()
				});
				$('#out-ing .member-dl dd').click(function(){
					
					$('#out-ing').hide()
				});

		           //提升上限
		           var oExul =$('.export-add-ul1').children('.exone');
		           var oExul2 =$('.export-add-ul1').children('.extwo');

		           oExul.click(function(){
		           	oExul.removeClass('ex-add-clk');
		           	$(this).addClass('ex-add-clk')

		           });
		           oExul2.click(function(){
		           	oExul2.removeClass('ex-add-clk');
		           	$(this).addClass('ex-add-clk')

		           });

		           $('.export-into-add-dl dd').click(function(){
		           	$('#up-mone').hide()
		           });

		           $('#no-rem3').click(function(){
		           	$('#up-mone').show()
		           });

				//end
			})
		</script>
		<!--right-->
		<div class="p-main-r fl" id="p-main-r">

			<div class="p-mian-cont">
				<dl class="pm-r-dl">
					<dt><span><a href="<?php echo Yii::app()->createUrl("enterpriseIndex/index");?>">首页</a></span><font> > </font><i>数据统计</i></dt>
				</dl>
				<div class="clear"></div>

			</div>
			<!--end-->
			<div class="count">
				<ul class="match">
					<li>
						<p class="match_p_1">当前人数/人数上限</p>
						<p class="match_p_2"><?php echo count($enterprise_member_count) ?>/<?php echo $enterprise_member_limit?>人</p>
						<p <?php if( (!empty($this->administrator_id))||($this->apply_type==3) ) echo 'style="display:none;"';?> class="match_p_4"  id="no-rem3">提升上限</p>
					</li>
					<li>
						<p class="match_p_1">成员激活情况</p>
						<span class="match_p_3">
							<p>已激活:
								<em>
									<?php $i = 0?>
									<?php foreach ($enterprise_member_count as $value){?>
									<?php if($value->member_id != 0){?>
									<?php $i++; ?>
									<?php }?>
									<?php }?>
									<?php echo $i;?>
									人</em></p>
									<p>未激活:<em><?php echo count($enterprise_member_count) - $i?>人</em></p>
								</span>
								<p class="match_p_4" id="no-rem">查看未激活人员</p>
							</li>
						<!-- 
							<li>
								<p class="match_p_1">累计退出情况</p>
								<p class="match_p_2">30人</p>
								<p class="match_p_4" id="no-rem2">查看详情</p>
							</li>
						-->
					</ul>

						 <!-- 
						<p class="zqsy">政企使用情况</p>
						<span class="table_span">
							<table>
								<tr>
									<td class="td4" colspan="4">10月</td>
								</tr>
								<tr>
									<td class="td1 td6">拨打次数范围</td>
									<td class="td2">人数</td>
									<td class="td1">比例</td>
									<td class="td2">操作</td>
								</tr>
								<tr>
									<td class="td3 td6">0</td>
									<td class="td4">4500</td>
									<td class="td3">12%</td>
									<td class="td4 td5">导出</td>
								</tr>
								<tr>
									<td class="td1 td6">1-10</td>
									<td class="td2">5</td>
									<td class="td1">12%</td>
									<td class="td2 td5">导出</td>
								</tr>
								<tr>
									<td class="td3 td6">0</td>
									<td class="td4">4500</td>
									<td class="td3">12%</td>
									<td class="td4 td5">导出</td>
								</tr>
								<tr>
									<td class="td1 td6">1-10</td>
									<td class="td2">5</td>
									<td class="td1">12%</td>
									<td class="td2 td5">导出</td>
								</tr>
								<tr>
									<td class="td3 td6">0</td>
									<td class="td4">4500</td>
									<td class="td3">12%</td>
									<td class="td4 td5">导出</td>
								</tr>
							</table>
							<table>
								<tr>
									<td class="td4" colspan="4">10月</td>
								</tr>
								<tr>
									<td class="td1 td6">拨打次数范围</td>
									<td class="td2">人数</td>
									<td class="td1">比例</td>
									<td class="td2">操作</td>
								</tr>
								<tr>
									<td class="td3 td6">0</td>
									<td class="td4">4500</td>
									<td class="td3">12%</td>
									<td class="td4 td5">导出</td>
								</tr>
								<tr>
									<td class="td1 td6">1-10</td>
									<td class="td2">5</td>
									<td class="td1">12%</td>
									<td class="td2 td5">导出</td>
								</tr>
								<tr>
									<td class="td3 td6">0</td>
									<td class="td4">4500</td>
									<td class="td3">12%</td>
									<td class="td4 td5">导出</td>
								</tr>
								<tr>
									<td class="td1 td6">1-10</td>
									<td class="td2">5</td>
									<td class="td1">12%</td>
									<td class="td2 td5">导出</td>
								</tr>
								<tr>
									<td class="td3 td6">0</td>
									<td class="td4">4500</td>
									<td class="td3">12%</td>
									<td class="td4 td5">导出</td>
								</tr>
							</table>
							<table>
								<tr>
									<td class="td4" colspan="4">10月</td>
								</tr>
								<tr>
									<td class="td1 td6">拨打次数范围</td>
									<td class="td2">人数</td>
									<td class="td1">比例</td>
									<td class="td2">操作</td>
								</tr>
								<tr>
									<td class="td3 td6">0</td>
									<td class="td4">4500</td>
									<td class="td3">12%</td>
									<td class="td4 td5">导出</td>
								</tr>
								<tr>
									<td class="td1 td6">1-10</td>
									<td class="td2">5</td>
									<td class="td1">12%</td>
									<td class="td2 td5">导出</td>
								</tr>
								<tr>
									<td class="td3 td6">0</td>
									<td class="td4">4500</td>
									<td class="td3">12%</td>
									<td class="td4 td5">导出</td>
								</tr>
								<tr>
									<td class="td1 td6">1-10</td>
									<td class="td2">5</td>
									<td class="td1">12%</td>
									<td class="td2 td5">导出</td>
								</tr>
								<tr>
									<td class="td3 td6">0</td>
									<td class="td4">4500</td>
									<td class="td3">12%</td>
									<td class="td4 td5">导出</td>
								</tr>
							</table>
						</span>
					-->
					<p class="zqsy">大喇叭使用情况</p>
					<span class="table_span">
						<table>
							<tr>
								<td class="td4" colspan="4"><?php echo date("Y-m",mktime(0, 0 , 0,date("m")-2,1,date("Y")))?>月</td>
							</tr>

							<?php if($broadCastLastTwo){?>
							<tr>
								<td class="td1 td6">管理员</td>
								<td class="td2">已用</td>
								<td class="td1">可用</td>
								<td class="td2">使用率</td>
							</tr>
							<?php $i = 0; $m = 0; $n = 0;?>
							<?php foreach ($broadCastLastTwo as $value){?>
							<tr>
								<td class="td3 td6"><?php echo $value->bname? $value->bname : $value->bremark_name?></td>
								<td class="td4"><?php echo $value->broadcast_per_month; $i+=$value->broadcast_per_month;?></td>
								<td class="td3"><?php echo $value->broadcast_available_month; $m+=$value->broadcast_available_month;?></td>
								
								<?php $active = ($value->broadcast_per_month+$value->broadcast_available_month) == 0 ? 0 : ceil(($value->broadcast_available_month/($value->broadcast_per_month+$value->broadcast_available_month))*100);?>
								<td class="td4"><?php echo $active; $n+=$active;?>%</td>
							</tr>
							<?php }?>
							<tr>
								<td class="td4 td6">总计</td>
								<td class="td4"><?php echo $i?></td>
								<td class="td4"><?php echo $m?></td>
											<td class="td4"><?php   if(($i+$m) == 0)
								                                { 
								                                    echo 0; 
								                        }else{ 
								                                echo ceil($m/($i+$m)) *100;
								                        }?>%</td>
							</tr>
							<?php }else{?>
							<tr height="75%">
								<td colspan='4'>暂无数据</td>
							</tr>
							<?php }?>
						</table>
						<table>
							<tr>
								<td class="td4" colspan="4"> <?php echo date("Y-m",mktime(0, 0 , 0,date("m")-1,1,date("Y")))?>月</td>
							</tr>
							<?php if($broadCastLast){?>
							<tr>
								<td class="td1 td6">管理员</td>
								<td class="td2">已用</td>
								<td class="td1">可用</td>
								<td class="td2">使用率</td>
							</tr>
							<?php $i = 0; $m = 0; $n = 0;?>
							<?php foreach ($broadCastLast as $value){?>
							<tr>
								<td class="td3 td6"><?php echo $value->bname? $value->bname : $value->bremark_name?></td>
								<td class="td4"><?php echo $value->broadcast_per_month; $i+=$value->broadcast_per_month;?></td>
								<td class="td3"><?php echo $value->broadcast_available_month; $m+=$value->broadcast_available_month;?></td>
								<?php $active =  ($value->broadcast_per_month+$value->broadcast_available_month) == 0 ? 0 :  ceil(($value->broadcast_available_month/($value->broadcast_per_month+$value->broadcast_available_month))*100);?>
								<td class="td4"><?php echo $active; $n+=$active;?>%</td>
							</tr>
							<?php }?>
							<tr>
								<td class="td4 td6">总计</td>
								<td class="td4"><?php echo $i?></td>
								<td class="td4"><?php echo $m?></td>
											<td class="td4"><?php   if(($i+$m) == 0)
								                                { 
								                                    echo 0; 
								                        }else{ 
								                                echo ceil($m/($i+$m)) *100;
								                        }?>%</td>
							</tr>
							<?php }else{?>
							<tr height="75%">
								<td colspan='4'>暂无数据</td>
							</tr>
							<?php }?>
						</table>
						<table>
							<tr>
								<td class="td4" colspan="4"><?php echo date('Y-m', time())?>月</td>
							</tr>
							<?php if($broadCast){?>
							<tr>
								<td class="td1 td6">管理员</td>
								<td class="td2">已用</td>
								<td class="td1">可用</td>
								<td class="td2">使用率</td>
							</tr>
							<?php $i = 0; $m = 0; $n = 0;?>
							<?php foreach ($broadCast as $value){?>
							<tr>
							<td class="td3 td6"><?php echo $value->bname? $value->bname : $value->bremark_name?></td>
								<td class="td4"><?php echo $value->broadcast_per_month; $i+=$value->broadcast_per_month;?></td>
								<td class="td3"><?php echo $value->broadcast_available_month; $m+=$value->broadcast_available_month;?></td>
								<?php $active =   ($value->broadcast_per_month+$value->broadcast_available_month) == 0 ? 0 : ceil(($value->broadcast_available_month/($value->broadcast_per_month+$value->broadcast_available_month))*100);?>
								<td class="td4"><?php echo $active; $n+=$active;?>%</td>
							</tr>
							<?php }?>
							<tr>
								<td class="td4 td6">总计</td>
								<td class="td4"><?php echo $i?></td>
								<td class="td4"><?php echo $m?></td>
								<td class="td4"><?php   if(($i+$m) == 0)
								                                { 
								                                    echo 0; 
								                        }else{ 
								                                echo ceil($m/($i+$m)) *100;
								                        }?>%</td>
							</tr>
							<?php }else{?>
							<tr height="75%">
								<td colspan='4'>暂无数据</td>
							</tr>
							<?php }?>
						</table>
					</span>

				</div>
			</div>



			<!--未激活人员-->
			<div id="no-men" style="display: none; " >
				<div class="export-add"></div>
				<div class="menber-cont" >
					<div class="menber-mian">
						<dl class="member-dl">
							<dt>未激活人员</dt>
							<dd></dd>
						</dl>
						<ul class="menber-one">
							<li>姓名</li>
							<li>备注</li>
							<li>所在分组</li>
							<li>查阅等级</li>
						</ul>
						<ul class="menber-two">
							<?php foreach ($enterprise_no_active as $value){?>
							<li><p><?php echo $value->name?></p><p><?php echo $value->remark_name?></p><p><?php echo $value->cname?></p><p><?php echo $value->access_level?></p></li>
							<?php }?>
						</ul>
					</div>
				</div>		
			</div>
			<!--退出详情-->
			<div id="out-ing" style="display: none;">
				<div class="export-add"></div>
				<div class="menber-cont" >
					<div class="menber-moncunt">
						<dl class="member-dl">
							<dt>退出详情</dt>
							<dd></dd>
						</dl>
						<ul class="menber-one">
							<li>姓名</li>
							<li>备注</li>
							<li>所在分组</li>
							<li>查阅等级</li>
							<li>是否为管理员</li>
						</ul>
						<ul class="menber-two">
							<li><p>吕小豪</p><p>地主</p><p>斗地主</p><p>2</p><p>是</p></li>
							<li><p>吕小豪</p><p>地主</p><p>斗地主</p><p>2</p><p>是</p></li>
							<li><p>吕小豪</p><p>地主</p><p>斗地主</p><p>2</p><p>是</p></li>
							<li><p>吕小豪</p><p>地主</p><p>斗地主</p><p>2</p><p>是</p></li>
							<li><p>吕小豪</p><p>地主</p><p>斗地主</p><p>2</p><p>是</p></li>
							<li><p>吕小豪</p><p>地主</p><p>斗地主</p><p>2</p><p>是</p></li>
							<li><p>吕小豪</p><p>地主</p><p>斗地主</p><p>2</p><p>是</p></li>
							<li><p>吕小豪</p><p>地主</p><p>斗地主</p><p>2</p><p>是</p></li>
							<li><p>吕小豪</p><p>地主</p><p>斗地主</p><p>2</p><p>是</p></li>
							<li><p>吕小豪</p><p>地主</p><p>斗地主</p><p>2</p><p>是</p></li>
						</ul>
					</div>
				</div>
			</div>
			<div id="up-mone" style="display: none;">
				<!--提升上线-->
        <form method="post" id="frm" action="/index.php/EnterpriseIndex/Pay">
				<input class="names_money" name="names_money" type="hidden"/>
				<input class="duration_aa" name="duration_aa" type="hidden"/>
				<input class="money" name="money" type="hidden"/>
				<?php echo $enterservice->view?>
				</form>
        <!--<div class="export-add"  ></div>
        <div class="export-into-add">
        	<div class="export-into-add-mian">
        		<dl class="export-into-add-dl">
        			<dt>提升上限</dt>
        			<dd></dd>
        		</dl>
        		<ul class="export-add-ul1">
        			<li class="ex-add-oneli">选择套餐：</li>
        			<li class="ex-add-li exone">2000人</li>
        			<li class="ex-add-li exone ex-add-clk">5000人</li>
        			<li class="ex-add-li exone">30000人</li>
        		</ul>
        		<ul class="export-add-ul1 ">
        			<li class="ex-add-oneli">开通时长：</li>
        			<li class="ex-add-li extwo" style="padding: 0 20px;">1年</li>
        			<li class="ex-add-li extwo ex-add-clk" style="padding: 0 20px;">2年</li>
        			<li class="ex-add-li extwo" style="padding: 0 20px;">3年</li>
        			<li class="ex-add-li extwo" style="padding: 0 20px;">4年</li>
        			<li class="ex-add-li extwo" style="padding: 0 20px; margin-right: 0;">5年</li>
        			
        		</ul>
        		<ul class="export-add-ul1 ">
        			<li class="ex-add-oneli">开通时长：</li>
        			<li class="ex-add-oneli">2016-3-25</li>
        			
        		</ul>
        		<ul class="export-add-ul1 ">
        			<li class="ex-add-oneli">套餐内容：</li>
        			<li class="ex-add-li " style="background: #eaeaea;padding: 0 5px;">5000人容量</li>
        			<li class="ex-add-li " style="background: #eaeaea;padding: 0 5px;">政企电脑版客户端</li>
        			<li class="ex-add-li " style="background: #eaeaea;padding: 0 5px;">30个大喇叭</li>
        		</ul>
        		<dl class="export-into-add-dl2">
        		     <dt><span>应付金额：</span><font>200元</font></dt>
        		     <dd><a href="javascript:;"><img src="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/images/com-la_7.jpg"/></a></dd>
        		</dl>
        	</div>
        </div>-->
    </div>
    <script type="text/javascript" src="/themes/js/shopdate.js" ></script>
    <script type="text/javascript">
    	$(function(){
    		$(".export-add").show();
    		$(".export-into-add").show();
    		$(".exone").click(function(){
    			$(".names_money").val($(this).html());
    			$(".money").val($(this).attr("money"));
    			$(".num0").html($(this).html());
    			$(".num1").html($(this).attr("big_horn"));
    			var abc =$(this).attr("showtime");
    			if($(".shop-time").attr("data") !=0){
    				$(".shop-time").attr("data",abc);
    				$(".shop-time").html(new Date(parseInt(abc)*1000).format('yyyy-MM-dd'));
    			}
    			if($(".duration_aa").val()){
    				calculate_time($(".duration_aa").val());
    				calculate_jin($(".duration_aa").val(),$(".money").val());
    			}

    		});
    		$(".extwo").click(function(){
    			$(".duration_aa").val($(this).attr("data"));
    			calculate_time($(this).attr("data"));
    			calculate_jin($(".duration_aa").val(),$(".money").val())
    		});
    		$(".ex-add-clk").click();
    		function calculate_time(date){
    			date = parseInt(date);
    			var date1 = $(".shop-time").attr("data");
    			var myDate=new Date(parseInt(date1)*1000);
    			var interval = "m";
    			if(date%12 == 0){
    				var interval = "y";
    				date = date/12;
    			}
					var duration = DateAdd(interval,date,myDate);//alert(duration.format('yyyy-MM-dd'));
					$(".shop-time").html(duration.format('yyyy-MM-dd'));

				}
			function calculate_jin(date,va){
				date = parseInt(date);
				va = parseInt(va);
				date = date/12;
				var jin = date*va;
				var names_money=$(".names_money").val();
				var duration_aa=$(".duration_aa").val();
				var money=$('.money').val();
				$.post('/index.php/enterpriseIndex/getInfo',{names_money:names_money,duration_aa:duration_aa,money:money},function(data){
					if(data.vip_price>0&&data.price>0)
					{
						jin=jin+'-'+data.vip_price+'='+data.price;
						$(".all_money").html(jin+"元");
					}
					else
					{
						$(".all_money").html(jin+"元");

					}

				},'json');

			}
			});
    </script>

























