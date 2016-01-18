<?php
/* @var $this UserController */
/* @var $dataProvider CActiveDataProvider */

?>

		<div class="main_right_content">
		<div class="main_right_content_title">
			<div class="main_right_content_title_font">用户详情</div>
			<div class="main_right_content_content_block_action_add">
				<div class="main_right_content_content_block_action_add">
					<a class="btn btn-success " target="_blank" href="<?php echo Yii::app()->createUrl('numberTrain/invitedetail',array('key'=>intval($_GET['key']),'type'=>1));?>">导出列表用户</a>
					
				<a class="btn btn-success backurl" href="javascript:goBack()" data="<?php echo $_GET['back'] ? $_GET['back'] : -1;?>">返回</a>
		</div>
			</div>
		</div>
		<div class="main_right_content_content">
			<table cellspacing=1 border="0" class="table table-hover">
				<thead>
					<tr class="main_right_content_content_title">
						<td width="10%">姓名</td><td width="10%">手机号</td><td width="5%">奔犇号</td><td width="10%">固话</td><td width="10%">简称</td><td width="6%">行业</td><td width="16%">地区</td><td width="8%">邀请奔犇数量</td><td width="8%">通讯录好友数</td><td width="7%">通讯录奔犇数量</td><td width="7%">发送小喇叭数量</td>
					</tr>
				</thead>
				<tbody>
					<?php					$i=0;
					if(count($items)){
					foreach ( $items as $item ) {
					$invite = 0;
					if(isset($memberInvite[$item['id']])){
						$invite = $memberInvite[$item['id']];
					}
					$cP = '';$cC = '';$cA = '';
					if (isset($areaInfo[$item['province']])) {
						$cP = $areaInfo[$item['province']];
					}
					if (isset($areaInfo[$item['city']])) {
						$cC = $areaInfo[$item['city']];
					}
					if (isset($areaInfo[$item['area']])) {
						$cA = $areaInfo[$item['area']];
					}
					$currentIndustry = '';
					if(isset($industryInfo[$item['industry']])){
						$currentIndustry = $industryInfo[$item['industry']];
					}
					$friend = 0;
					if (isset($friendInfo[$item['id']])) {
						$friend = $friendInfo[$item['id']];
					}
					$allfriend = 0;
					if (isset($allFriendInfo[$item['id']])) {
						$allfriend = $allFriendInfo[$item['id']];
					}
					$broad = 0;
					if (isset($broadInfo[$item['id']])) {
						$broad = $broadInfo[$item['id']];
					}
					?>
						<tr class="main_right_content_content_body">
										<td><?php echo $item['name']?$item['name']:$item['nick_name']; ?></td>
										<td><?php echo $item['phone'] ?></td>
										<td><?php echo $item['benben_id'] ?></td>
										<td><?php echo $item['telephone'];?></td>
										<td><?php echo $item['short_name'];?></td>
										<td><?php echo $currentIndustry;?></td>
										<td><?php echo $cP.$cC.$cA;?></td>
										<td><?php echo $invite;?></td>
										<td><?php echo $allfriend;?></td>
										<td><?php echo $friend;?></td>
										<td><?php echo $broad;?></td>
				</tr>
				<?php } }?>				</tbody>
			</table>

		</div>
	</div>
	
	<input id="del_baseurl" type="hidden" value="<?php echo Yii::app()->createUrl("user/delete",array('page'=>$pages->currentPage +1));?>" />
	<div class="main_footer">
		<div class="main_footer_page">
			<?php
			$page = intval($_GET['page']) ?  intval($_GET['page']) : 1;
			$url = explode("?", Yii::app()->request->getUrl());
	        $link = Yii::app()->request->hostInfo.$url[0]."?";

			echo '<ul class="mypageul" id="yw0"><li class="pageli">第<strong>1</strong>页/共<strong class="pagecount">1</strong>页,
			 				<strong class="pagecount">'.count($items).'</strong>条记录
		</li></ul>';

			?>
			
		</div>
	</div>
</div>
	
