		<link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/css/Personal_5.css" />
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
						<dt><span><a href="javascript:;">首页</a></span><font> > </font><i>数据统计</i></dt>
					</dl>
					<div class="clear"></div>
					   
					</div>
					<!--end-->
					<div class="count">
						<ul class="match">
							<li>
								<p class="match_p_1">当前人数/人数上限</p>
								<p class="match_p_2">2000/3000人</p>
								<p class="match_p_4" id="no-rem3">提升上限</p>
							</li>
							<li>
								<p class="match_p_1">成员激活情况</p>
								<span class="match_p_3">
									<p>已激活:<em>3000人</em></p>
									<p>未激活:<em>3000人</em></p>
								</span>
								<p class="match_p_4" id="no-rem">查看未激活人员</p>
							</li>
							<li>
								<p class="match_p_1">累计退出情况</p>
								<p class="match_p_2">30人</p>
								<p class="match_p_4" id="no-rem2">查看详情</p>
							</li>
						</ul>
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
						<p class="zqsy">大喇叭使用情况</p>
						<span class="table_span">
							<table>
								<tr>
									<td class="td4" colspan="4">10月</td>
								</tr>
								<tr>
									<td class="td1 td6">管理员</td>
									<td class="td2">已用</td>
									<td class="td1">可用</td>
									<td class="td2">使用率</td>
								</tr>
								<tr>
									<td class="td3 td6">吕小豪</td>
									<td class="td4">5</td>
									<td class="td3">5</td>
									<td class="td4">12%</td>
								</tr>
								<tr>
									<td class="td1 td6">吕小豪</td>
									<td class="td2">5</td>
									<td class="td1">5</td>
									<td class="td2">12%</td>
								</tr>
								<tr>
									<td class="td3 td6">吕小豪</td>
									<td class="td4">5</td>
									<td class="td3">5</td>
									<td class="td4">12%</td>
								</tr>
								<tr>
									<td class="td1 td6">吕小豪</td>
									<td class="td2">5</td>
									<td class="td1">5</td>
									<td class="td2">12%</td>
								</tr>
								<tr>
									<td class="td4 td6">总计</td>
									<td class="td4">23</td>
									<td class="td4">23</td>
									<td class="td4">30%</td>
								</tr>
							</table>
							<table>
								<tr>
									<td class="td4" colspan="4">10月</td>
								</tr>
								<tr>
									<td class="td1 td6">管理员</td>
									<td class="td2">已用</td>
									<td class="td1">可用</td>
									<td class="td2">使用率</td>
								</tr>
								<tr>
									<td class="td3 td6">吕小豪</td>
									<td class="td4">5</td>
									<td class="td3">5</td>
									<td class="td4">12%</td>
								</tr>
								<tr>
									<td class="td1 td6">吕小豪</td>
									<td class="td2">5</td>
									<td class="td1">5</td>
									<td class="td2">12%</td>
								</tr>
								<tr>
									<td class="td3 td6">吕小豪</td>
									<td class="td4">5</td>
									<td class="td3">5</td>
									<td class="td4">12%</td>
								</tr>
								<tr>
									<td class="td1 td6">吕小豪</td>
									<td class="td2">5</td>
									<td class="td1">5</td>
									<td class="td2">12%</td>
								</tr>
								<tr>
									<td class="td4 td6">总计</td>
									<td class="td4">23</td>
									<td class="td4">23</td>
									<td class="td4">30%</td>
								</tr>
							</table>
							<table>
								<tr>
									<td class="td4" colspan="4">10月</td>
								</tr>
								<tr>
									<td class="td1 td6">管理员</td>
									<td class="td2">已用</td>
									<td class="td1">可用</td>
									<td class="td2">使用率</td>
								</tr>
								<tr>
									<td class="td3 td6">吕小豪</td>
									<td class="td4">5</td>
									<td class="td3">5</td>
									<td class="td4">12%</td>
								</tr>
								<tr>
									<td class="td1 td6">吕小豪</td>
									<td class="td2">5</td>
									<td class="td1">5</td>
									<td class="td2">12%</td>
								</tr>
								<tr>
									<td class="td3 td6">吕小豪</td>
									<td class="td4">5</td>
									<td class="td3">5</td>
									<td class="td4">12%</td>
								</tr>
								<tr>
									<td class="td1 td6">吕小豪</td>
									<td class="td2">5</td>
									<td class="td1">5</td>
									<td class="td2">12%</td>
								</tr>
								<tr>
									<td class="td4 td6">总计</td>
									<td class="td4">23</td>
									<td class="td4">23</td>
									<td class="td4">30%</td>
								</tr>
							</table>
						</span>
						
					</div>
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
						<li><p>吕小豪</p><p>地主</p><p>斗地主</p><p>2</p></li>
						<li><p>吕小豪</p><p>地主</p><p>斗地主</p><p>2</p></li>
						<li><p>吕小豪</p><p>地主</p><p>斗地主</p><p>2</p></li>
						<li><p>吕小豪</p><p>地主</p><p>斗地主</p><p>2</p></li>
						<li><p>吕小豪</p><p>地主</p><p>斗地主</p><p>2</p></li>
						<li><p>吕小豪</p><p>地主</p><p>斗地主</p><p>2</p></li>
						<li><p>吕小豪</p><p>地主</p><p>斗地主</p><p>2</p></li>
						<li><p>吕小豪</p><p>地主</p><p>斗地主</p><p>2</p></li>
						<li><p>吕小豪</p><p>地主</p><p>斗地主</p><p>2</p></li>
						<li><p>吕小豪</p><p>地主</p><p>斗地主</p><p>2</p></li>
						<li><p>吕小豪</p><p>地主</p><p>斗地主</p><p>2</p></li>
						<li><p>吕小豪</p><p>地主</p><p>斗地主</p><p>2</p></li>
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
        <div class="export-add"  ></div>
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
        </div>
			</div>





























