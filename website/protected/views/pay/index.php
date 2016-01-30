<script type="text/javascript" src="/themes/js/shoppay.js" ></script>
<form id="charge" action="<?php echo Yii::app()->createUrl('/Pay/charge');?>" method="post">
<!--into-->
     <div class="into-mian">
     		<h1 class="into-title">转入到我的奔犇账户</h1>
     		<dl class="into-dl">
     			<dt style="line-height: 30px;">奔犇账户余额：</dt>
     			<dd style="line-height: 30px;">
     				<span class="into-money"><font><?php echo $fee;?></font>元</span>
     			</dd>
     		</dl>
     		<dl class="into-dl">
     			<dt style="line-height: 59px;">转入金额：</dt>
     			<dd style="height: 60px;">
     				<span class="into-int1" style="float:left;" ><input id="money" name="money"/> 元</span>
                         <span class="error" style="display:none;float:left;line-height:56px;color:red;margin-left:50px;">金额格式不正确！</span>
     			</dd>
     		</dl>
     		<dl class="into-dl">
     			<dt style="line-height: 50px;">转入方式：</dt>
     			<dd>
     				<div class="into-fs into-ck fl">
     					<em><img src="/themes/images/in_1.png"/></em>
     					<p>
     						<span>支付宝支付</span>
     						<font>账户：*****71188@189.cn</font>
     					</p>
     					<i><img src="/themes/images/in_2.png"/></i>
     				</div>
     				<div class="into-add fl"><img src="/themes/images/in_4.jpg"/></div>
     				<div class="clear"></div>
     				<p class="into-text">奔犇账户目前仅支持支付宝转入</p>
     			</dd>
     		</dl>
     		<div class="clear"></div>
            <a id="chargenow" href="javascript:void(0);" class="into-a"><img src="/themes/images/in_3.png"/></a>
     	</div>
</form>


























































