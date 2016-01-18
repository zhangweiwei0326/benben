<?php
/* @var $this UserController */
/* @var $dataProvider CActiveDataProvider */

?>

		<div class="main_right_content">
		<div class="main_right_content_title">
			<div class="main_right_content_title_font">用户详情</div>
			<div class="main_right_content_content_block_action_add">
				<div class="main_right_content_content_block_action_add">
					<a class="btn btn-success " target="_blank" href="<?php echo Yii::app()->createUrl('statistic/detail',array('key'=>intval($_GET['key']),'download'=>1));?>">导出列表用户</a>
					<a class="btn btn-success " target="_blank" href="<?php echo Yii::app()->createUrl('statistic/bxcontactdownload',array('key'=>intval($_GET['key']),'download'=>1));?>">导出列表用户及通讯录</a>
				<a class="btn btn-success backurl" href="javascript:goBack()" data="<?php echo $_GET['back'] ? $_GET['back'] : -1;?>">返回</a>
		</div>
			</div>
		</div>
		<div class="main_right_content_content">
			<table cellspacing=1 border="0" class="table table-hover">
				<thead>
					<tr class="main_right_content_content_title">
						<td width="20%">奔犇号</td><td width="20%">昵称</td><td width="20%">手机号</td><td width="20%">百姓网数量</td><td width="20%">联系人数量</td><td width="20%">操作</td>
					</tr>
				</thead>
				<tbody>
					<?php					$i=0;
					if(count($items)){
					foreach ( $items as $item ) {
					$edit_url = Yii::app()->createUrl('user/update',array('id'=>$item->id,'page'=>$_REQUEST['page']));
					?>
						<tr class="main_right_content_content_body">
										<td><?php echo $item['benben_id'] ?></td>
										<td><?php echo $item['nick_name'] ?></td>
										<td><?php echo $item['phone'] ?></td>
										<td><?php echo $number[$item['id']];?></td>
										<td><?php if(isset($memberHaveContact[$item['id']])) echo $memberHaveContact[$item['id']]; else echo '0';?></td>
										<td>
											<?php if($_GET['key'] > -1){
												echo '<a class="btn btn-primary btn-sm" href="'.Yii::app()->createUrl('statistic/bxdownload',array('key'=>$item['id'],'download'=>1)).'">导出通讯录用户</a>';
											}
										?>
						
					</td>
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
	
