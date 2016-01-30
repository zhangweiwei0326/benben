		<link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/css/Personal_5.css" />
		<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/js/jquery.JPlaceholder.js" ></script>
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
								<p class="match_p_2">2000/3000人</p>
								<p class="match_p_4">提升上限</p>
							</li>
							<li>
								<p class="match_p_1">成员激活情况</p>
								<span class="match_p_3">
									<p>已激活:<em>3000人</em></p>
									<p>未激活:<em>3000人</em></p>
								</span>
								<p class="match_p_4">查看未激活人员</p>
							</li>
							<li>
								<p class="match_p_1">累计退出情况</p>
								<p class="match_p_2">30人</p>
								<p class="match_p_4">查看详情</p>
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





























