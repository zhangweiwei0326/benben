<?php
/* @var $this NoticeController */
/* @var $dataProvider CActiveDataProvider */
Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/themes/css/global.css");
?>

		<div class="main_right_content">
		<div class="main_right_content_title">
			<div class="main_right_content_title_font">我要买管理/推送记录</div>
			<div class="main_right_content_content_block_action_add">
				<a class="btn btn-success" href="javascript:goBack();<?php //echo Yii::app()->createUrl("enterprise/index");?>">返回</a>
			</div>	
		</div>
		<div class="main_right_content_content" style="background:#F5F5F5;">
		<form action="<?php echo Yii::app()->createUrl('buy/pushlog')?>" method="get">
			<ul style="margin-top:0;">						
					<li>
						<div class="form-group" style="padding:0 0 0 5px;">
							<label  for="created_time1" style="float:left;margin-top:7px;">推送时间:</label>	
							<div class="col-sm-8" style="margin-bottom:10px;">
								<input type="text" class="form-control created_time" name="created_time1"  id="created_time1"  value="<?php echo $result['created_time1']?>">
							</div>
					</div>
					</li>
					<li>
						<div class="form-group" style="padding:0 0 0 5px;">
							<label  for="created_time1" style="float:left;margin-top:7px;">到:&nbsp;&nbsp;&nbsp;</label>	
							<div class="col-sm-8" style="margin-bottom:10px;">
								<input type="text" class="form-control created_time" name="created_time2"  id="created_time2"   value="<?php echo $result['created_time2']?>">
							</div>
					</div>
					</li>
					<li>
						<div class="form-group" style="padding:0 0 0 5px;">
							<label  for="created_time1" style="float:left;margin-top:7px;">推送行业:</label>	
							<div class="col-sm-8" style="margin-bottom:10px;">
								<select style="width: 102px; display: inline" class="form-control"
									name="industry" id="industry">
									<option value="-1">--请选择--</option>
											<?php foreach ($industryall as $prv){?>
												<option value="<?php echo $prv->id?>"
										<?php if($result['industry'] == $prv->id) echo 'selected = "selected"';?>><?php echo $prv->name?></option>
											<?php  }?>
								</select>
							</div>
					</div>
					</li>
					<li style="float:right;text-align:right;width:60px;padding-right:10px;">
			<span type="submit" class="btn btn-primary" id="search_clear">清空</span>
		</li>	
	<li style="float:right;text-align:right;width:60px;padding-right:10px;">
			<span type="submit" class="btn btn-primary" id="search_more">更多</span>
		</li>
	<li style="float:right;text-align:right;width:70px;padding-right:16px;">
	    <input type="hidden" value="-2" name="backurl">
		<input type="submit" class="btn btn-primary" value="查询" id="submit_search">
	</li>
				
			</ul>
			
			<ul  class="member_ul">
				<li>
					<div class="form-group" style="padding: 0 0 0 5px;">
						<label for="sex" style="float: left; margin-top: 7px;">地　　区:</label>
						<div class="col-sm-8" style="margin:0px 0 10px;">
							<select class="form-control" name="province" id="province">
								<option value="-1">--请选择省-</option>
								<?php foreach ($province as $prv){?>
									<option value="<?php echo $prv->bid?>"
									<?php if($result['province'] == $prv->bid) echo 'selected = "selected"';?>><?php echo $prv->area_name?></option>
								<?php  }?>
							</select>
						</div>
					</div>
				</li>
				<li>
					<div class="form-group" style="padding: 0 0 0 5px;">
						<div class="col-sm-8" style="margin:0px 0 10px 55px;">
							<select class="form-control" name="city" id="city">
								<option value="-1">--请选择市-</option>
								<?php if(isset($res)) {foreach ($res as $prv){?>
									<option value="<?php echo $prv['bid'];?>"
									<?php if($result['city'] == $prv['bid']) echo 'selected = "selected"';?>><?php echo $prv['area_name']?></option>
								<?php  }}?>
							</select>
						</div>
					</div>
				</li>
				<li>
					<div class="form-group" style="padding: 0 0 0 5px;">
						<div class="col-sm-8" style="margin:0px 0 10px 56px;">
							<select class="form-control" name="area" id="area">
								<option value="-1">--请选择区-</option>
								<?php if(isset($res2)) {foreach ($res2 as $prv){?>
									<option value="<?php echo $prv['bid'];?>"
									<?php if($result['area'] == $prv['bid']) echo 'selected = "selected"';?>><?php echo $prv['area_name']?></option>
								<?php  }}?>
							</select>
						</div>
					</div>
				</li>				
			</ul>			
			</form>
			
			<table cellspacing=1 border="0" class="table table-hover">
				<thead>
					<tr class="main_right_content_content_title">
						<td width="10%">标题</td>
						<td width="10%">发帖人</td>
						<td width="8%">奔犇号</td>
						<td width="10%">地区</td>
						<td width="10%">推送行业</td>
						<td width="10%">推送地区</td>
						<td width="10%">推送商家数量</td>
						<td width="8%">推送次数</td>
						<td width="14%">推送时间</td>
						<td width="10%">操作</td>
					</tr>
				</thead>
				<tbody>
					<?php					$i=0;
					foreach ( $items as $item ) {
					$edit_url = Yii::app()->createUrl('buy/pushlogdetail',array('id'=>$item->id,'page'=>$_REQUEST['page']));
					?>
						<tr class="main_right_content_content_body">
										<td><?php echo $item->title ?></td>
										<td><?php echo $item->name ? $item->name : $item->nick_name ?></td>
										<td><?php echo $item->benben_id;?></td>
										<td><?php echo $areaInfo[$item->bprovince].''.$areaInfo[$item->bcity] ?></td>
										<td><?php echo $industry[$item->industry] ?></td>
										<td><?php echo $areaInfo[$item->province].''.$areaInfo[$item->city] ?></td>
										<td><?php echo $item->number ?></td>
										<td><?php echo count($pushnum[$item->buyid]) ?></td>		
										<td><?php echo date("Y-m-d H:i:s", $item->created_time); ?></td>
										<td>
						<a class="btn btn-primary btn-sm" href="<?php echo $edit_url?>">明细</a>
						<!--  <a class="btn btn-danger btn-sm action_del" data="<?php echo $item->id;?>">删除</a>-->
					</td>
				</tr>
				<?php } ?>				</tbody>
			</table>

		</div>
	</div>
	
	<input id="del_baseurl" type="hidden" value="<?php echo Yii::app()->createUrl("notice/delete",array('page'=>$pages->currentPage +1));?>" />
	<div class="main_footer <?php //if($pages->pageCount <= 1) echo 'main_footer_hidden';?>">
		<div class="main_footer_page">
		<?php 
				$path = substr(dirname(__FILE__), 0, -3)."layouts/searchPages.php";
				require_once($path);  
			?>
			<?php	
			$page = intval($_GET['page']) ?  intval($_GET['page']) : 1;
			$url = explode("?", Yii::app()->request->getUrl());
	$link = Yii::app()->request->hostInfo.$url[0]."?";
			echo '<ul class="yiiPager" id="yw0">'.$this->textPage($pages->pageCount , $page, $link).'</ul>';
// 			$this->widget ( 'CLinkPager', array (
// 					'header' => '',
// 					'firstPageLabel' => '«首页',
// 					'lastPageLabel' => '尾页»',
// 					'prevPageLabel' => '«',
// 					'nextPageLabel' => '»',
// 					'maxButtonCount' => 6,
// 					'pages' => $pages
// 			) );
			?>
		</div>
	</div>
</div>
	
