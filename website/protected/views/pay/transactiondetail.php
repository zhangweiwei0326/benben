     <style>
img{display: block;float: left;margin-left:2px;}
     </style>
     <div class="into-mian">
       <h1 class="into-title">交易明细</h1>
       <ul class="trading-title">
        <li style="width: 100px; margin-left: 12px; text-align: center;">分类</li>
        <li style="width: 125px;">订单号</li>
        <li style="width: 140px;">名称</li>
        <li style="width: 85px;">时间</li>
        <li style="width: 70px;">操作</li>
        <li style="width: 110px;">金额</li>
        <li style="width: 70px;">犇币</li>
        <li style="width: 100px;">余额</li>
        <li style="width: 100px;">状态</li>
      </ul>
      <?php foreach ($items as $key => $item) {
	$currentItems = $key + 1;
	?>
       <ul class="trading-mian">
        <li style="margin-left: 12px;"><em style="display:block;width:100px;padding-top:20px;">
        <?php if ($item->order_amount>0) {?><img title="支付宝" style="width:30px;" src="/themes/images/in_1.png"/><?php }?>
        <?php if ($item->coin>0) {?><img title="犇币" style="width:30px;" src="/themes/images/coin.png"/><?php }?>
        <?php if ($item->fee>0) {?><img title="余额" style="width:30px;" src="/themes/images/fee.png"/><?php }?>
  </em></li>
        <li style="width: 125px;"><p style="font-size: 18px;"><?php echo $item->order_sn; ?></p></li>
        <li style="width: 140px;"><p><?php $goods = StoreOrderGoods::model()->find("order_id={$item->order_id}");
	echo $goods->goods_name;

	?></p></li>
          <li style="width: 85px; line-height: 16px;"><p style="margin-top: 24px;"><?php echo date("Y-m-d", $item->add_time); ?><font><?php echo date('H:i', $item->add_time) ?></font></p></li>
          <li style="width: 70px;"><p style="font-size: 14px;"><?php $model=StoreOrderGoods::model()->find("order_id={$item->order_id}");if($model->extension_code==4){echo '转入';}else{echo '购买服务';}?></p></li>
           <li style="width: 110px;"><p style="font-size: 18px;"><?php echo $item->goods_amount; ?></p></li>
           <li style="width: 70px;"><p style="font-size: 18px;"><?php echo $item->coin;?></p></li>
           <li style="width: 100px;"><p style="font-size: 18px;"><?php echo $item->fee;?></p></li>
           <li style="width: 100px;"><p><?php switch ($item->order_status) {
	case '1':
		echo "等待付款";
		break;
	case '6':
		echo "交易成功";
		break;
	default:
		echo "未知状态";
		break;
	}$item->pay_status?></p></li>
         </ul>
         <?php }
?>
         <div class="clear"></div>
         <p class="t-fanye"><?php if (isset($_GET['page']) && !empty($_GET['page'])&&$_GET['page']!=1) {?><a href="<?php echo Yii::app()->createUrl("pay/transactiondetail"); ?>"><<首页</a><a href="<?php echo Yii::app()->createUrl("pay/transactiondetail", array('page' => $beforePage)); ?>"><上一页</a><?php }
?><span>共<?php echo $pages->itemCount; ?>条记录 / 当前<?php echo $currentItems; ?>条</span><?php if (intval($_GET['page']) < $pages->pageCount) {?><a href="<?php echo Yii::app()->createUrl("pay/transactiondetail", array('page' => $nextPage)); ?>">下一页></a><a href="<?php echo Yii::app()->createUrl("pay/transactiondetail", array('page' => $pages->pageCount)); ?>">尾页>></a><?php }
?></p>

        </div>
