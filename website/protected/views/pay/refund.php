<script type="text/javascript" src="/themes/js/shoprefund.js" ></script>
<form id="refund" action="" method="post">
     <div class="into-mian">
     		<h1 class="into-title">转出</h1>
     		<dl class="into-dl">
     			<dt style="line-height: 30px;">奔犇账户余额：</dt>
     			<dd style="line-height: 30px;">
     				<span class="into-money"><font><?php echo $fee;?></font>元</span>
     			</dd>
     		</dl>
     		<dl class="into-dl">
     			<dt style="line-height: 59px;">转出金额：</dt>
     			<dd style="height: 60px;">
     				<span class="into-int1" style="float:left;" ><input name="money" id="money" /> 元</span>
                         <span class="error" style="display:none;float:left;line-height:56px;color:red;margin-left:50px;">金额格式不正确！</span>

     			</dd>
     		</dl>
     		<dl class="into-dl">
     			<dt style="line-height: 50px;">转出至：</dt>
     			<dd>
     				<div class="into-fs into-ck fl">
     					<em><img src="/themes/images/in_1.png"/></em>
     					<p>
     						<span>支付宝支付</span>
     						<!-- <font>账户：******345@qq.com</font> -->
                                   <font>账户:******<?php echo substr($buyer_email,6);?> </font>
     					</p>
     					<i><img src="/themes/images/in_2.png"/></i>
     				</div>
     				<div class="into-add fl"><img src="/themes/images/in_4.jpg"/></div>
     				<div class="clear"></div>
     				<!-- <p class="into-nx">银行卡</p>
     				<div class="clear"></div>
     				<div class="into-fs  into-bank fl">
     					<em><img src="/themes/images/in_5.png"/></em>
     					<p class="bank-p">
     						<span>招商银行</span>
     						<font>尾号：6502</font>
     					</p>
     					<b><img src="/themes/images/in_6.png"/></b>
     					<i><img src="/themes/images/in_2.png"/></i>
     				</div> -->
     				<!-- <div class="into-add fl"><img src="/themes/images/in_8.jpg"/></div> -->
     			</dd>
     		</dl>
     		<div class="clear"></div>
            <a id="refundnow" href="javascript:void(0);" class="into-a" ><img  src="/themes/images/in_7.png"/></a>
     	</div>
</form>






























