
	<script type="text/javascript"
	src="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/independent/js/news.js"></script>
	
	<div class="p-main-r fl" style="width: 1373px; height: 820px; background: #fff;" id="p-main-r">
				<div class="n-news-two">
					<ul class="news-t-title">
						<a href="<?php echo Yii::app()->createUrl('EnterpriseNews/index')?>"><li class="<?php if(Yii::app()->getController()->getAction()->id=="index"){echo 'hover';}?>"  id="mess01">系统消息</li></a>
						<a href="<?php echo Yii::app()->createUrl('EnterpriseNews/notice')?>"><li  class="<?php if(Yii::app()->getController()->getAction()->id=="notice"){echo 'hover';}?>" id="mess02">请求与通知</li></a>
						<li class="news-t-tst">
							<form action="<?php echo Yii::app()->createUrl('EnterpriseNews/notice')?>" method="get">
								<select name="newsStatus" >
										<option value="0" <?php if($result==0) echo 'selected = "selected"';?>>未读</option>
										<option value="11" <?php if($result==11) echo 'selected = "selected"';?>>全部</option>
										<option value="2" <?php if($result==2) echo 'selected = "selected"';?>>同意</option>
										<option value="3" <?php if($result ==3) echo 'selected = "selected"';?>>拒绝</option>
								</select>
								<input type="submit" value="查询" id="find-news" display="none">
							</form>
						</li>
					</ul>
					<div class="news-twoli-cont">
					
						<div id="conmess02" >
							<h1 class="n-conmess-h1"><span>请求通知</span></h1>
							<ul class="n-conmess-ul">
								<?php 	foreach($items1 as $item1 ){?>
								<li>
									<em></em><input type="hidden" value="<?php echo $item1->id;?>" />
									<p><b><?php echo $item1->nick_name;?>  申请加入政企</b><font><?php echo $item1->content?></font></p>
									<i class="n-conmess-li-i">
										<?php if($item1->status==2){
												echo "已同意";}elseif($item1->status==3){ echo "已拒绝";}else{?>
										<samp>同意</samp>
										<dl class="n-conmess-dl-c" style="display: none;">
											<dt></dt>
											<dd></dd>
										</dl>
										<?php }?>
									</i>
									<input class="hidden_input" type="hidden" value="<?php echo $item1->id;?>" />
									<span><?php echo date("Y-m-d",$item1->created_time);?></span>
								</li>
						<?php }?>
							</ul>
							<dl class="n-conmess-dl">
								<dt><img class="select_del2" src="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/images/i-7.png"/></dt>
								<dd>
								<?php  if(!empty($pages1)){?>
									<div class="g-mefr-bfy">
											<ul>
												<li>第<?php echo $pages1->currentPage+1?>页/共<?php $totalPages1=ceil($pages1->itemCount / $pages1->pageSize);echo $totalPages1;?>页，<?php echo $pages1->itemCount;?>条记录</li>
												<?php
														$page = intval ( $_GET ['page'] ) ? intval ( $_GET ['page'] ) : 1;
														$url = explode ( "?", Yii::app ()->request->getUrl () );
														$link = Yii::app ()->request->hostInfo . $url [0] . "?";
														echo '<ul>' . $this->textPage ( $pages1->pageCount, $page, $link ) . '</ul>';
			                                    ?>
											</ul>
										</div>
										<?php }else{echo "";}?>
								</dd>
							</dl>
						</div>
					</div>
				</div>
			</div>
			
			</div>