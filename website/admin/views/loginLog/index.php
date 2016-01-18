<?php
/* @var $this MemberController */
/* @var $dataProvider CActiveDataProvider */

Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/themes/css/global.css");
//$status = array('0'=>'启用', '1'=>'禁用1周', '2'=>'禁用2周', '3'=>'禁用1个月', '4'=>'禁用3个月', '5'=>'无限期');
$status = array('0'=>'登录失败', '1'=>'登录成功', '10'=>'用户编辑', '11'=>'用户统计', '20'=>'百姓网管理', 
		'21'=>'导出申请数据','22'=>'批量录入数据','23'=>'录入数据记录','24'=>'百姓网统计','30'=>'政企通讯录',
		'31'=>'群组','32'=>'号码直通车','40'=>'微创作','41'=>'我要买','42'=>'朋友圈',
		'43'=>'开心一刻','44'=>'统计','50'=>'好友联盟','60'=>'系统通知','61'=>'定向通知',
		'62'=>'小喇叭管理','70'=>'协议&说明','71'=>'行业字典','72'=>'版本管理','73'=>'投诉建议','74'=>'开机页面',
		'80'=>'用户管理','81'=>'角色管理','82'=>'个人密码修改','83'=>'系统日志'
);
?>

<div class="main_right_content">
	<div class="main_right_content_title main_titleab">
	<div class="main_right_content_title_font">登录日志</div>
</div>

<div class="main_right_content_content" style="background:#F5F5F5;">
<?php if($msg) {?>
		<div class="alert alert-danger alert-dismissible" role="alert">
			  <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
			  <strong>警告！</strong> <?php echo $msg;?>
		</div>
		<?php }?>
		
<form action="<?php echo Yii::app()->createUrl('loginLog/index')?>" method="get">
	<ul>				
		<li  >
			<div class="form-group" style="padding:0 0 0 5px;">
				<label  for="name" style="float:left;margin-top:4px;">用　　户:</label>	
				<div class="col-sm-8" style="margin-bottom:10px;">
					<input type="text" class="form-control" name="name"  id="name"  value="<?php echo $result['name']?>">
				</div>
			</div>
		</li>
		<li style="width: 19%">
			<div class="form-group" style="padding:0 0 0 5px;">
				<label  for="nick_name" style="float:left;margin-top:4px;">登录IP:</label>	
				<div class="col-sm-8" style="margin-bottom:10px;">
					<input type="text" class="form-control" name="loginip"  id="loginip"  value="<?php echo $result['loginip']?>">
				</div>
			</div>
		</li>
		<li>
			<div class="form-group" style="padding:0 0 0 5px;">
				<label  for="sex" style="float:left;margin-top:4px;">状　　态:</label>	
				<div class="col-sm-8" style="margin-bottom:10px;">
					<select class="form-control" name="status" id="status">
						
						<option value="-1" >--请选择--</option>						
						<option value="1" <?php if($result['status'] == 1) echo 'selected = "selected"';?>>登录成功</option>
						<option value="0" <?php if(isset($result['status']) && $result['status'] == 0) echo 'selected = "selected"';?>>登录失败</option>
					</select>
				</div>
			</div>
		</li>
<?php $url = "name={$_GET['name']}&loginip={$_GET['loginip']}&created_time1={$_GET['created_time1']}&created_time2={$_GET['created_time2']}&status=".$_GET['status']?$_GET['status']:-1;?>
		<li style="float:right;text-align:right;width:60px;padding-right:10px;">
			<a href = "/admin.php/loginLog/phpexcel?<?php echo $url?>"><div class="btn btn-primary"  id="putexcel">导出</div></a>
		</li>
		<li style="float:right;text-align:right;width:60px;padding-right:10px;">
			<span type="submit" class="btn btn-primary" id="search_more">更多</span>
		</li>
		
		<li style="float:right;text-align:right;width:60px;padding-right:10px;">
			<span type="submit" class="btn btn-primary" id="search_clear">清空</span>
		</li>
		<li style="float:right;text-align:right;width:60px;padding-right:10px;">
			<input type="submit" class="btn btn-primary" value="查询" id="submit_search">
		</li>

	</ul>
	
<ul class="member_ul">
<li>
		<div class="form-group" style="padding:0 0 0 5px;">
			<label  for="created_time1" style="float:left;margin-top:4px;">登录日期:</label>	
			<div class="col-sm-8" style="margin-bottom:10px;">
				<input type="text" class="form-control created_time" name="created_time1"  id="created_time1"  value="<?php echo $result['created_time1']?>">
			</div>
	</div>
	</li>
	<li>
		<div class="form-group" style="padding:0 0 0 5px;">
			<label  for="created_time1" style="float:left;margin-top:4px;">到:　</label>	
			<div class="col-sm-8" style="margin-bottom:10px;">
				<input type="text" class="form-control created_time" name="created_time2"  id="created_time2"   value="<?php echo $result['created_time2']?>">
			</div>
	</div>
	</li>
</ul>


<div class="clear">
</div>
</form>
<table cellspacing=1 border="0" class="table table-hover" style="border-top:1px solid #ccc;">
	<thead>
		<tr class="main_right_content_content_title">
			<td width="10%">编号</td>
			<td width="20%">用户名</td>			
			<td width="20%">登录IP</td>
			<td width="20%">登录时间</td>
			<td width="20%">状态</td>			
			<td width="10%">操作</td>
		</tr>
	</thead>
	<tbody>
	<?php
	foreach ( $items as $item ) {		
		?>
		<tr class="main_right_content_content_body">						
			<td><?php echo $item->id ?></td>
			<td><?php echo $item->username ?></td>
			<td><?php echo $item->loginip ?></td>			
			<td><?php echo date("Y-m-d H:i:s",$item->logintime) ?></td>
			<td><?php echo $status[$item->status]; ?></td>			
			<td><!-- <a class="btn btn-primary btn-sm" href="<?php echo $edit_url?>&back=<?php echo $result['goback']?>">编辑</a> -->
			</td>
		</tr>
		<?php } ?>
	</tbody>
</table>

</div>
</div>
<input
	id="del_baseurl" type="hidden"
	value="<?php echo Yii::app()->createUrl("member/delete",array('page'=>$pages->currentPage +1));?>" />
<div
	class="main_footer">
<div class="main_footer_page">
			<?php
				$path = substr(dirname(__FILE__), 0, -8)."layouts/searchPages.php";
				require_once($path);  
			?>
	<?php
	$page = intval($_GET['page']) ?  intval($_GET['page']) : 1;
	$url = explode("?", Yii::app()->request->getUrl());
	$link = Yii::app()->request->hostInfo.$url[0]."?";
	echo '<ul class="yiiPager" id="yw0">'.$this->textPage($pages->pageCount , $page, $link).'</ul>';
	//$this->widget ( 'CLinkPager', array (
// 					'header' => '',
// 					'firstPageLabel' => '«首页',
// 					'lastPageLabel' => '尾页»',
// 					'prevPageLabel' => '«',
// 					'nextPageLabel' => '»',
// 					'maxButtonCount' => 6,
// 			        'itemCount'=>$count,
// 					'pages' => $pages
// 	) );
	?>
</div>
</div>
</div>



