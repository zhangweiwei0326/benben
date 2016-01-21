<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <!--允许全屏-->
    <meta content="yes" name="apple-mobile-web-app-capable">
    <meta content="yes" name="apple-touch-fullscreen">
    <meta content="telephone=no,email=no" name="format-detection">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="flexible" content="initial-dpr=2" />
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <link rel="stylesheet" href="http://cdn.amazeui.org/amazeui/2.5.0/css/amazeui.min.css">
    <title>团购详情</title>
</head>

<body>
<style type="text/css">
    h3.am-gallery-title.am-slider-desc {margin: 0;}
    h3 b {font-size: 14px;font-weight: 500;border-bottom: 1px solid white;}
    .price .promotion-price{text-decoration: line-through;}
    .allprice{border-bottom: 1px solid #eee;padding-bottom: 10px;}
    .price{font-size: 1.6rem;}
    .price .origion-price{font-weight: 600;}
    .price .origion-price>span{font-size: 32px;color: #ffae00;}
    .price .promotion-price{color:#b4b4b4;padding-top: 1.3rem;}
    .valid .valid-time{color: #b4b4b4;}
    .description{padding-top: 10px;color: #b4b4b4;font-size: 14px;}
    .methods{padding-top: 10px;color: #b4b4b4;font-size: 16px;border-bottom: 1px solid #eee;padding-bottom: 10px;}
    .methods i{color: #5eb95e;}
    .am-gallery-bordered>li,.am-gallery-default>li,.am-gallery-imgbordered>li,.am-gallery-overlay>li {padding: 0px;}
    .default-block{height: 10px;width: 100%;background: #eee;margin-top: 10px;}
    .mustknow header{font-size: 16px;padding-top: 10px;color: #b4b4b4;border-bottom: 1px solid #eee;padding-bottom: 10px;}
    .mustknow .content{font-size: 14px;padding-top: 10px;}
    .good i{font-size: 18px;}
    a.comment{display: block;padding-top: 10px;}
</style>
<div data-am-widget="slider" class="am-slider am-slider-c1" data-am-slider='{"directionNav":false}'>
    <ul data-am-widget="gallery" class="am-slides" data-am-gallery="{ pureview: true }">
        <?php foreach($result['poster'] as $k=>$v){?>
            <li>
                <div class="am-gallery-item">
                    <a href=<?php if($result['shownext']){echo("http://s.amazeui.org/media/i/demos/bing-1.jpg");}else{echo("javascript:void(0);");} ?>>
                        <img src="<?php echo $v['small_img'];?>" alt="<?php echo $result['name'];?>" data-rel="<?php echo $v['img_url'];?>" />
                        <h3 class="am-gallery-title am-slider-desc">
                            <div><?php echo $result['name'];?><?php if($result['shownext']) echo('<b style="float: right">下一组团购</b>');?></div>
                            <?php if($result['model']){echo ("<div>".$result['model']."</div>");}?>
                        </h3>
                    </a>
                </div>
            </li>
        <?php }?>
    </ul>
</div>
<div class="am-container">
    <div class="allprice">
        <div class="price am-g am-g-collapse">
            <div class="origion-price am-u-sm-5"><span><?php echo $result['promotion_price']?></span>元</div>
            <div class="promotion-price am-u-sm-4">原价<span><?php echo $result['origion_price']?></span>元</div>
        </div>
        <div class="valid am-g am-g-collapse">
            <div class="am-u-sm-3">有效期：</div>
            <div class="valid-time am-u-sm-9">
                <span><?php echo date("Y年m月d日",$result['valid_left'])?></span>-<span><?php echo date("Y年m月d日",$result['valid_right'])?></span>
            </div>
        </div>
    </div>
    <div class="methods">
        <?php foreach ($result['pay_methods'] as $key => $value) {?>
            <i class="am-icon-check-circle"></i>支持<?php echo ($value['pay_name']);?>
        <?php }?>
    </div>
    <div class="description">
        <?php echo $result['description']?>
    </div>
</div>
<div class="default-block"></div>
<div class="am-container">
    <div class="mustknow">
        <header>购买须知</header>
        <div class="content">
            <?php echo ($result['mustknow']);?>
        </div>
    </div>
</div>
<div class="default-block"></div>
<div class="am-container">
    <a class="comment am-g-collapse" href="<?php echo Yii::app()->request->baseUrl; ?>/index.php/v2/storeComment/Promotionlist?key=iphone&promotion_id=<?php echo($result['promotionid']);?>">
        <div class="title am-u-sm-4">查看所有评论</div>
        <div class="good am-u-sm-offset-2 am-u-sm-6">好评率：<?php echo ($result['good_rate']);?>&nbsp;&nbsp;<i class="am-icon-angle-right"></i></div>
    </a>
</div>
<div class="default-block"></div>
<input type="hidden" name="huanxin_username" value="<?php echo($result['huanxin_username']);?>">
<input type="hidden" name="tel" value="<?php echo($result['tel']);?>">
<input type="hidden" name="train_id" value="<?php echo($result['train_id']);?>">
<input type="hidden" name="type" value="<?php echo($result['type']);?>">
<!--[if (gte IE 9)|!(IE)]><!-->
<script src="http://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
<!--<![endif]-->
<!--[if lte IE 8 ]>
<script src="http://libs.baidu.com/jquery/1.11.3/jquery.min.js"></script>
<script src="http://cdn.staticfile.org/modernizr/2.8.3/modernizr.js"></script>
<script src="http://cdn.amazeui.org/amazeui/2.5.0/js/amazeui.ie8polyfill.min.js"></script>
<![endif]-->
<script src="<?php echo Yii::app()->request->baseUrl; ?>/protected/js/amazeui.min.js"></script>
</body>

</html>
