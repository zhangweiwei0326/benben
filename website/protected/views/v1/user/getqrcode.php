<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
       <title>benben</title>       
       <?php Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/themes/css/newstyle.css");?>
</head>

<body>
<!--header-->
<section>
<figure>
   <figcaption class="por"><img src="/themes/images/samll_1.png"/></figcaption>
   <dl>
        <dt>头像</dt>
        <dd><img src="<?php echo $qrcode['poster'];?>"/></dd>
   </dl>
</figure>
<figure>
   <figcaption class="por_t"><img src="/themes/images/samll_2.png"/></figcaption>
   <dl class="por_dl">
        <dt>奔犇号</dt>
        <dd><?php echo $qrcode['benben_id'];?></dd>
   </dl>
</figure>
<figure>
   <figcaption class="por_t"><img src="/themes/images/samll_3.png"/></figcaption>
   <dl class="por_dl">
        <dt>姓名</dt>
        <dd><?php echo $qrcode['name'];?></dd>
   </dl>
</figure>
<figure>
   <figcaption class="por_t"><img src="/themes/images/samll_4.png"/></figcaption>
   <dl class="por_dl">
        <dt>地区</dt>
        <dd><?php echo $qrcode['add'];?></dd>
   </dl>
</figure>
<figure>
   <figcaption class="por_t"><img src="/themes/images/samll_5.png"/></figcaption>
   <dl class="por_dl" style="border-bottom:none">
        <dt>性别</dt>
        <dd><?php echo $qrcode['sex'];?></dd>
   </dl>
</figure>
</section>
<footer>
<a href="http://112.124.101.177/download/"><img src="/themes/images/down.png"/></a>
</footer>
</body>
</html>