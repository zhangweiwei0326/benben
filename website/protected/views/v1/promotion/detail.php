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
    <title>促销详情</title>
</head>

<body>
    <style type="text/css">
    h3.am-gallery-title.am-slider-desc {margin: 0;}
    h3 b {font-size: 14px;font-weight: 500;border-bottom: 1px solid white;}
    .price .promotion-price{text-decoration: line-through;}
    .allprice{border-bottom: 1px solid #eee;padding-bottom: 10px;}
    .price{font-size: 20px;}
    .price .origion-price{font-weight: 600;}
    .price .origion-price>span{font-size: 32px;color: #ffae00;}
    .price .promotion-price{color:#b4b4b4;padding-top: 1.3rem;}
    .valid .valid-time{color: #b4b4b4;}
    .description{padding-top: 10px;color: #b4b4b4;font-size: 14px;}
    .am-gallery-bordered>li,.am-gallery-default>li,.am-gallery-imgbordered>li,.am-gallery-overlay>li {padding: 0px;}
    </style>
    <div data-am-widget="slider" class="am-slider am-slider-c1" data-am-slider='{"directionNav":false}'>
        <ul data-am-widget="gallery" class="am-slides" data-am-gallery="{ pureview: true }">
            <?php foreach($result['pic'] as $k=>$v){?>
            <li>
                <div class="am-gallery-item">
                    <a href=<?php if($result['shownext']){echo("http://s.amazeui.org/media/i/demos/bing-1.jpg");}else{echo("javascript:void(0);");} ?>>
                        <img src="<?php echo $result['smallpic'][$k];?>" alt="<?php echo $result['name'];?>" data-rel="<?php echo $v;?>" />
                        <h3 class="am-gallery-title am-slider-desc">
                            <div><?php echo $result['name'];?><?php if($result['shownext']) echo('<b style="float: right">下一组促销</b>');?></div>
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
                <div class="origion-price am-u-sm-6"><span><?php echo $result['promotion_price']?></span>元</div>
                <div class="promotion-price am-u-sm-6">原价<span><?php echo $result['origion_price']?></span>元</div>
            </div>
            <div class="valid am-g am-g-collapse" style="font-size: 1.4rem;">
                <div class="am-u-sm-3">有效期：</div>
                <div class="valid-time am-u-sm-9">
                    <span><?php echo date("Y年m月d日",$result['valid_left']);?></span>-<span><?php echo date("Y年m月d日",$result['valid_right']);?></span>
                </div>
            </div>
        </div>
        <div class="description">
                    <?php echo $result['description']?>
        </div>
    </div>
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
