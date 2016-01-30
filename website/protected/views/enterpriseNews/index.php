<script type="text/javascript"
	src="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/independent/js/news.js"></script>
	
	<div class="p-main-r fl" style="width: 1373px; height: 820px; background: #fff;" id="p-main-r">
				<div class="n-news-two">
					<ul class="news-t-title">
						<a href="<?php echo Yii::app()->createUrl('EnterpriseNews/index')?>"><li class="hover"  id="mess01">系统消息</li></a>
						<a href="<?php echo Yii::app()->createUrl('EnterpriseNews/notice')?>"><li  id="mess02">请求与通知</li></a>
						<li class="news-t-tst">
							<form action="<?php echo Yii::app()->createUrl('EnterpriseNews/index')?>" method="get">
								<select name="newsStatus" >
										<option value="0" <?php if($result==0) echo 'selected = "selected"';?>>未读</option>
										<option value="11" <?php if($result==11) echo 'selected = "selected"';?>>全部</option>
										<option value="1" <?php if($result ==1) echo 'selected = "selected"';?>>已读</option>
								</select>
								<input type="submit" value="查询" id="find-news" display="none">
							</form>
						</li>
					</ul>
					<div class="news-twoli-cont">
						<div id="conmess01">
							<h1 class="n-conmess-h1"><span>系统消息</span></h1>
							<ul class="n-conmess-ul">
							<?php foreach($items as $item ){?>
								<li class="n-mess-liadd">
									<em class=""></em><input type="hidden" value="<?php echo $item->id;?>" />
									<p><?php echo $item->content?></p>
									<i ></i>
									<span><?php echo date("Y-m-d",$item->created_time);?></span>
								</li>
								<?php }?>
							</ul>
							<dl class="n-conmess-dl">
								<dt><img class="select_del1" src="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/images/i-7.png"/></dt>
								<dd>
									<div class="g-mefr-bfy">
											<ul>
												<li>第<?php echo $pages->currentPage+1?>页/共<?php $totalPages=ceil($pages->itemCount / $pages->pageSize);echo $totalPages;?>页，<?php echo $pages->itemCount;?>条记录</li>
												<?php
														$page = intval ( $_GET ['page'] ) ? intval ( $_GET ['page'] ) : 1;
														$url = explode ( "?", Yii::app ()->request->getUrl () );
														$link = Yii::app ()->request->hostInfo . $url [0] . "?";
														echo '<ul>' . $this->textPage ( $pages->pageCount, $page, $link ) . '</ul>';
			                                    ?>
											</ul>
										</div>
								</dd>
							</dl>
						</div>
						<!--1end-->

						<!--2 end-->
					</div>
				</div>
			</div>
			
			</div>