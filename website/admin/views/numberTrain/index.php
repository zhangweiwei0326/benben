<?php
/* @var $this NumberTrainController */
/* @var $dataProvider CActiveDataProvider */
$status = array('0'=>'启用', '1'=>'屏蔽'/*, '2'=>'禁用2周', '3'=>'禁用1个月', '4'=>'禁用3个月', '5'=>'无限期'*/);
$status1= array('0'=>'启用', '1'=>'禁用1周', '2'=>'禁用2周', '3'=>'禁用1个月', '4'=>'禁用3个月', '5'=>'无限期');
Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/themes/css/global.css");
$role_arr  = json_decode(Yii::app()->session['role_arr'],true);
?>

		<div class="main_right_content">
		<div class="main_right_content_title main_titleab">
			<div class="main_right_content_title_font">号码直通车管理</div>
			
		</div>
		<div class="main_right_content_content" style="background:#F5F5F5;">
						<?php if($result['msg']) {?>
			<div class="alert alert-danger alert-dismissible" role="alert">
			  <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
			  <strong>警告！</strong> <?php echo $result['msg'];?>
			</div>
			<?php }?>
			<form action="<?php echo Yii::app()->createUrl('numberTrain/index')?>" method="get">
			<ul style="margin-top:0;">
				<li>
					<div class="form-group" style="padding:0 0 0 5px;">
						<label  for="benben_id" style="float:left;margin-top:7px;">商家名称:</label>	
						<div class="col-sm-8" style="margin-bottom:10px;">
							<input type="text" class="form-control" name="name" id="name" value="<?php echo $result['name']?>">
						</div>
					</div>
				</li>
				<li>
					<div class="form-group" style="padding:0 0 0 5px;">
						<label  for="benben_id" style="float:left;margin-top:7px;">简　　称:</label>	
						<div class="col-sm-8" style="margin-bottom:10px;">
							<input type="text" class="form-control" name="short_name" id="short_name" value="<?php echo $result['short_name']?>">
						</div>
					</div>
				</li>
				<li>
					<div class="form-group" style="padding:0 0 0 5px;">
						<label  for="benben_id" style="float:left;margin-top:7px;">服务项目:</label>	
						<div class="col-sm-8" style="margin-bottom:10px;">
							<input type="text" class="form-control" name="tag" id="tag" value="<?php echo $result['tag']?>">
						</div>
					</div>
				</li>
				<li>
					<div class="form-group" style="padding:0 0 0 5px;">
						<label  for="benben_id" style="float:left;margin-top:7px;">行业:</label>	
						<div class="col-sm-8" style="margin-bottom:10px;">
							<select class="form-control" name="industry">
								<option value="-1">请选择</option>
								<?php
								foreach ($industryInfo as $key => $value) {
									if ($key == $result['industry']) {
										echo '<option selected="selected" value="'.$key.'">'.$value.'</option>';
									}else{
										echo '<option value="'.$key.'">'.$value.'</option>';
									}
									
								}
								?>
							</select>
							
						</div>
					</div>
				</li>
				<?php if($role_arr['dostore'] & 4){?>
				<li style="float:right;text-align:right;width:70px;padding-right:16px;">
					<a  href="<?php echo Yii::app()->createUrl('numberTrain/statistic')?>" class="btn btn-primary" >统计</a>
				</li>
				<?php }?>	
				<li style="float:right;text-align:right;width:70px;padding-right:16px;">
			<span type="submit" class="btn btn-primary" id="search_more">更多</span>
		</li>				
				
				<li style="float:right;text-align:right;width:60px;padding-right:10px;">
			<span type="submit" class="btn btn-primary" id="search_clear">清空</span>
		</li>
		<li style="float:right;text-align:right;width:70px;padding-right:12px;">
					<input type="hidden" value="-2" name="backurl">
					<input type="submit" class="btn btn-primary" value="查询" id="submit_search">
				</li>
				</ul>
				<ul class="member_ul">
				<li>
					<div class="form-group" style="padding:0 0 0 5px;">
						<label  for="benben_id" style="float:left;margin-top:7px;">手机号码:</label>	
						<div class="col-sm-8" style="margin-bottom:10px;">
							<input type="text" class="form-control" name="phone" id="phone" value="<?php echo $result['phone']?>">
						</div>
					</div>
				</li>
				<li>
						<div class="form-group" style="padding:0 0 0 5px;">
							<label  for="created_time1" style="float:left;margin-top:7px;">创建日期:</label>	
							<div class="col-sm-8" style="margin-bottom:10px;">
								<input type="text" class="form-control created_time" name="created_time1"  id="created_time1"  value="<?php echo $result['created_time1']?>">
							</div>
					</div>
					</li>
					<li>
						<div class="form-group" style="padding:0 0 0 5px;">
							<label  for="created_time1" style="float:left;margin-top:7px;">到:　　　</label>	
							<div class="col-sm-8" style="margin-bottom:10px;">
								<input type="text" class="form-control created_time" name="created_time2"  id="created_time2"   value="<?php echo $result['created_time2']?>">
							</div>
					</div>
					</li>
					<li>
						<div class="form-group" style="padding:0 0 0 5px;">
							<label  for="created_time1" style="float:left;margin-top:7px;">置顶天数:</label>	
							<div class="col-sm-8" style="margin-bottom:10px;">
								<input type="text" class="form-control" name="date1"  id="date1"  value="<?php echo $result['date1']?>">
							</div>
					</div>
					</li>
					<li>
						<div class="form-group" style="padding:0 0 0 5px;">
							<label  for="created_time1" style="float:left;margin-top:7px;">到:　　　</label>	
							<div class="col-sm-8" style="margin-bottom:10px;">
								<input type="text" class="form-control" name="date2"  id="date2"   value="<?php echo $result['date2']?>">
							</div>
					</div>
					</li>
					</ul>
					<ul  class="member_ul">
					<li>
					<div class="form-group" style="padding:0 0 0 5px;">
						<label  for="sex" style="float:left;margin-top:7px;">地　　区:</label>	
						<div class="col-sm-8" style="margin-bottom:10px;">
							<select class="form-control" name="province" id="province">
								<option value="-1">--请选择--</option>
								<?php foreach ($province as $prv){?>
									<option value="<?php echo $prv->bid?>"  <?php if($result['province'] == $prv->bid) echo 'selected = "selected"';?>><?php echo $prv->area_name?></option>
								<?php  }?>
							</select>
						</div>
					</div>
				</li>
				<li>
					<div class="form-group" style="padding:0 0 0 5px;">
						<div class="col-sm-8" style="margin-bottom:10px;margin-left:54px;">
							<select class="form-control" name="city" id="city">
								<option value="-1">--请选择市--</option>
								<?php if(isset($res)) {foreach ($res as $prv){?>
									<option value="<?php echo $prv['bid'];?>"
									<?php if($result['city'] == $prv['bid']) echo 'selected = "selected"';?>><?php echo $prv['area_name']?></option>
								<?php  }}?>					
							</select>
						</div>
					</div>
				</li>
					<li>
					<div class="form-group" style="padding:0 0 0 5px;">
					<div class="col-sm-8" style="margin-bottom:10px;margin-left:54px;">
						<select class="form-control" name="area"  id="area">
							<option value="-1">--请选择区--</option>
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
						<td width="5%">头像</td>
						<td width="8%">直通车名称</td>
						<td width="8%">简称</td>
						<td width="8%">行业</td>
						<td width="13%">地区</td>
						<td width="8%">创建人</td>
						<td width="8%">创建人状态</td>
						<td width="10%">手机号码</td>	
						<td width="5%">置顶</td>					
						<td width="7%">状态</td>
						<td width="12%">创建时间</td>						
						<td width="10%">操作</td>
					</tr>
				</thead>
				<tbody>
					<?php					
					//省市代码获取
					$pro = array();
					$pro_arr = array();
					foreach ($items as $value){
						$pro[] = $value['province'];
						$pro[] = $value['city'];
						$pro[] = $value['area'];
						$pro[] = $value['street'];
					}
					
					$pro_name = $this->allareas(implode(",", $pro));
					if($pro_name){
						foreach ($pro_name as $val){
							$pro_arr[$val['bid']] = $val['area_name'];
						}
					}
					$i=0;
					foreach ( $items as $item ) {
					$edit_url = Yii::app()->createUrl('numberTrain/update',array('id'=>$item->id,'page'=>$_REQUEST['page']));
						$log_url = Yii::app()->createUrl('numberTrain/log',array('id'=>$item->id,'page'=>$_REQUEST['page']));
					?>
						<tr class="main_right_content_content_body <?php if($item->is_close == 1) echo 'danger';?>">
										<td><img 
										src="<?php echo $item->poster ?  Yii::app()->request->hostInfo.$item->poster 
										:  Yii::app()->request->hostInfo.'/themes/images/poster.jpg';?>" width="32"></td>
											<td><?php echo $item->name ?></td>
										<td><?php echo $item->short_name ?></td>
										<td><?php echo $item->iname ?></td>
										<td><?php echo $pro_arr[$item->province].''.$pro_arr[$item->city] ?></td>
										<td><?php echo $item->mname? $item->mname: $item->nick_name; ?></td>
										<td><a title="查看禁用记录" href="<?php echo Yii::app()->createUrl('numberDisable/service?id='.$item->member_id)?>" style="text-decoration:underline"><?php echo $status1[$item->store_disable] ?></a></td>
										<td><?php echo $item->phone ?></td>
										<td><a style="text-decoration:underline" href="<?php echo $log_url;?>"><?php switch($item->istop){case 10: echo '置1';break;case 9: echo '置2';break;case 8: echo '置3';break;default: echo '否';break;}?></a></td>											
										<td><?php if($item->is_close == 1){echo "已关闭";}else{?><a title="查看禁用记录" href="<?php echo Yii::app()->createUrl('numberDisable/index?id='.$item->id)?>" style="text-decoration:underline"><?php echo $status[$item->status] ?></a><?php }?></td>
										<td><?php echo date("Y-m-d H:i:s", $item->created_time) ?></td>
										<td>
											<?php if($role_arr['dostore'] & 2){?><button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#exampleModal" data-info="<?php echo $item->id;?>">置顶</button><?php }?>
										<!-- <a class="btn btn-danger btn-sm"
						href="<?php echo Yii::app()->createUrl('numberTrain/top?id='.$item->id)?>">置顶</a> -->
						<?php if($role_arr['dostore'] & 1){?><a class="btn btn-primary btn-sm" href="<?php echo $edit_url?>">编辑</a><?php }?>
					
					</td>
				</tr>
				<?php } ?>				</tbody>
			</table>

		</div>
	</div>
	
	<input id="del_baseurl" type="hidden" value="<?php echo Yii::app()->createUrl("numberTrain/delete",array('page'=>$pages->currentPage +1));?>" />
	<div class="main_footer ">
		<div class="main_footer_page">
			<?php 
				$path = substr(dirname(__FILE__), 0, -11)."layouts/searchPages.php";
				require_once($path);  
			?>
			<?php		
			$page = intval($_GET['page']) ?  intval($_GET['page']) : 1;
			$url = explode("?", Yii::app()->request->getUrl());
	$link = Yii::app()->request->hostInfo.$url[0]."?";
			echo '<ul class="yiiPager" id="yw0">'.$this->textPage($pages->pageCount , $page, $link).'</ul>';
			?>
		</div>
	</div>
</div>


<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="exampleModalLabel">设置置顶</h4>
      </div>
      <div class="modal-body">
        <form>
          <div class="form-group">
            <label for="recipient-name" class="control-label">类型:</label>
            <select type="text" class="form-control" id="recipient-name">
            	<option value="0">取消置顶</option>
            	<option value="10">一级置顶</option>
            	<option value="9">二级置顶</option>
            	<option value="8">三级置顶</option>
            </select>
          </div>
          <div class="form-group">
            <label for="message-text" class="control-label">天数:</label>
            <input type="text" class="form-control" id="message-text" />
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
        <button type="button" class="btn btn-primary" id="modal-confirm" data-url="<?php echo Yii::app()->createUrl('numberTrain/top');?>">确认</button>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
	$('#exampleModal').on('show.bs.modal', function (event) {
		  var button = $(event.relatedTarget);
		  var info = button.attr('data-info');
		  $('#modal-confirm').attr('data-info', info);
		});

	$('#modal-confirm').click(function(){
		var top = $('#recipient-name').val();
		var number = $('#message-text').val();
		var info = $('#modal-confirm').attr('data-info');
		var url = $(this).attr('data-url');
		if (info && url) {
			$.get(url, {'id':info, 'top':top, 'number':number}, function(e){
				if(e == 2) alert("只能设置3个商家置顶");
				if(e == 3) alert("已经置顶，不能够再做其它置顶");
				if(e == 4) alert("该位置已经有商户置顶,请选择其他位置");					
				window.location.href = window.location.href;
			});
		};
	});
</script>
	
