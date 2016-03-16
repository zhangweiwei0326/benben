<?php
/* @var $this StoreOrderInfoController */
/* @var $dataProvider CActiveDataProvider */
Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl . "/themes/css/global.css");

?>

<div class="main_right_content">
	<div class="main_right_content_title">
		<div class="main_right_content_title_font">充值列表</div>
		<div class="main_right_content_content_block_action_add">
			<!-- <a class="btn btn-success" href="<?php echo Yii::app()->createUrl("storeOrderInfo/create"); ?>">增加</a> -->
		</div>
	</div>
	<div class="main_right_content_content">
		<form action="<?php echo Yii::app()->createUrl('StoreChargeAdmin/index') ?>" method="get">
			<ul>

				<li>
					<div class="form-group" style="padding:0 0 0 5px;">
						<label  for="order_sn" style="float:left;margin-top:4px;">订单号:　</label>
						<div class="col-sm-8" style="margin-bottom:10px;">
							<input type="text" class="form-control" name="order_sn" id="order_sn" value="<?php echo $result['order_sn'] ?>">
						</div>
					</div>
				</li>
				<li>
					<div class="form-group" style="padding:0 0 0 5px;">
						<label  for="service_name" style="float:left;margin-top:4px;">服务名:</label>
						<div class="col-sm-8" style="margin-bottom:10px;">
							<select class="form-control" name="service_name" id="service_name">
								<option value="-1">--请选择--</option>
								<option value="0" <?php if ($result['service_name'] == 0) {
									echo 'selected="selected"';
								}
								?>>促销</option>
								<option value="1" <?php if ($result['service_name'] == 1) {
									echo 'selected="selected"';
								}
								?>>团购</option>
								<option value="4" <?php if ($result['service_name'] == 4) {
									echo 'selected="selected"';
								}
								?>>奔犇账户充值</option>
								<option value="10" <?php if ($result['service_name'] == 10) {
									echo 'selected="selected"';
								}
								?>>我要开分店</option>
								<option value="11" <?php if ($result['service_name'] == 11) {
									echo 'selected="selected"';
								}
								?>>会员号</option>
								<option value="12" <?php if ($result['service_name'] == 12) {
									echo 'selected="selected"';
								}
								?>>小喇叭</option>
								<option value="13" <?php if ($result['service_name'] == 13) {
									echo 'selected="selected"';
								}
								?>>大喇叭</option>
								<option value="14" <?php if ($result['service_name'] == 14) {
									echo 'selected="selected"';
								}
								?>>好友联盟</option>
								<option value="15" <?php if ($result['service_name'] == 15) {
									echo 'selected="selected"';
								}
								?>>政企通讯录</option>
							</select>
						</div>
					</div>
				</li>

				<li>
					<div class="form-group" style="padding:0 0 0 5px;">
						<label  for="sex" style="float:left;margin-top:4px;">订单状态:</label>
						<div class="col-sm-8" style="margin-bottom:10px;">
							<select class="form-control" name="order_status" id="order_status">
								<option value="-1">--请选择--</option>
								<option value="1" <?php if ($result['order_status'] == 1) {
									echo 'selected="selected"';
								}
								?>>等待付款</option>
								<option value="6" <?php if ($result['order_status'] == 6) {
									echo 'selected="selected"';
								}
								?>>交易成功</option>
								
							</select>
						</div>
					</div>
				</li>
				<li>
					<div class="form-group" style="padding:0 0 0 5px;">
						<label  for="nick_name" style="float:left;margin-top:4px;">用户名:</label>
						<div class="col-sm-8" style="margin-bottom:10px;">
							<input type="text" class="form-control" name="nick_name"   id="nick_name"  value="<?php echo $result['nick_name'] ?>">
						</div>
					</div>
				</li>
				<li>
					<div class="form-group" style="padding:0 0 0 5px;">
						<label  for="phone" style="float:left;margin-top:4px;">手机号:</label>
						<div class="col-sm-8" style="margin-bottom:10px;">
							<input type="text" class="form-control" name="phone"   id="phone"  value="<?php echo $result['phone'] ?>">
						</div>
					</div>
				</li>
				<li style="float:right;text-align:right;width:60px;padding-right:10px;">
					<input type="submit" class="btn btn-primary" value="查询" id="submit_search">
				</li>
				<li>
					<div class="form-group" style="padding:0 0 0 5px;">
						<label  for="created_time1" style="float:left;margin-top:4px;">下单日期:</label>
						<div class="col-sm-8" style="margin-bottom:10px;">
							<input type="text" class="form-control created_time" name="created_time1"  id="created_time1"  value="<?php echo $result['created_time1'] ?>">
						</div>
					</div>
				</li>
				<li>
					<div class="form-group" style="padding:0 0 0 5px;">
						<label  for="created_time1" style="float:left;margin-top:4px;">到:　</label>
						<div class="col-sm-8" style="margin-bottom:10px;">
							<input type="text" class="form-control created_time" name="created_time2"  id="created_time2"   value="<?php echo $result['created_time2'] ?>">
						</div>
					</div>
				</li>
			</ul>
		</form>
		<table cellspacing=1 border="0" class="table table-hover">
			<thead>
				<tr class="main_right_content_content_title">
					<td width="10%">订单号</td><td width="10%">服务名</td><td width="8%">订单状态</td><td width="15%">用户信息</td><td width="7%">支付状态</td><td width="10%">订单金额</td><td width="5%">已付款</td><td width="7%">使用犇币</td><td width="8%">使用余额</td><td width="15%">下单日期</td>
				</tr>
			</thead>
			<tbody>
				<?php	$i = 0;
				foreach ($items as $item) {
					$edit_url = Yii::app()->createUrl('storeOrderInfo/update', array('id' => $item->order_id, 'page' => $_REQUEST['page']));
					?>
					<tr class="main_right_content_content_body">
						<td><?php echo $item->order_sn ?></td>
						<td><?php switch ($item->type) {
							case '0':
							echo "促销";
							break;
							case '1':
							echo "团购";
							break;
							case '4':
							echo "奔犇账户充值";
							break;
							case '10':
							echo "我要开分店";
							break;
							case '11':
							echo "会员号";
							break;
							case '12':
							echo "小喇叭";
							break;
							case '13':
							echo "大喇叭";
							break;
							case '14':
							echo "好友联盟";
							break;
							case '15':
							echo "政企通讯录";
							break;
						}?></td>
						<td><?php
							switch ($item->order_status) {
								case '1':
								echo '等待付款';
								break;
								case '6':
								echo '交易成功';
								break;
								default:
								echo '未知状态';
								break;

							}
							?></td>
							<td>
								<?php
								if($item->from_enterprise==1)
								{
									$model=ApplyRegister::model()->find("enterprise_id={$item->member_id}");
								echo $model->name.' | '.$model->phone;


								}
							else
							{
								$model = Member::model()->find("id={$item->member_id}");
								echo $model->nick_name.' | '.$model->phone;
							}
							?>
						</td>

						<td><?php
							switch ($item->pay_status) {
								case '0':
								echo '未付款';
								break;
								case '1':
								echo '付款中';
								break;
								case '2':
								echo '已付款';
								break;

							}

							?></td>
							<td><?php echo $item->goods_amount; ?></td>
							<td><?php echo $item->money_paid ?></td>
							<td><?php echo $item->coin;?></td>
							<td><?php echo $item->fee;?></td>
							<td><?php echo date("Y/m/d H:i:s", $item->add_time); ?></td>
						</tr>
						<?php }
						?>				</tbody>
					</table>

				</div>
			</div>

			<input id="del_baseurl" type="hidden" value="<?php echo Yii::app()->createUrl("storeOrderInfo/delete", array('page' => $pages->currentPage + 1)); ?>" />
			<div class="main_footer ">
				<div class="main_footer_page">
					<?php
					$path = dirname(dirname(__FILE__)) . "/layouts/searchPages.php";
					require_once $path;
					?>
					<?php
					$page = intval($_GET['page']) ? intval($_GET['page']) : 1;
					$url = explode("?", Yii::app()->request->getUrl());
					$link = Yii::app()->request->hostInfo . $url[0] . "?";
					echo '<ul class="yiiPager" id="yw0">' . $this->textPage($pages->pageCount, $page, $link) . '</ul>';
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

