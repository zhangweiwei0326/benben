<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>帮助与反馈</title>
    <meta name="viewport" content="width=device-width,height=device-height,initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/protected/css/main.css">
</head>
<body>
    <div class="main">
        <h1>常见问题</h1>
        <ul>
            <li>1. 忘记密码怎么办？
                <i class="iconfont">&#xe640;</i>
                <p>如果您忘记了密码，请点击登录界面右下角“忘记密码？”，然后输入您的手机号码，收到验证码后输入点击下一步，重新设置6-16位的新密码，重复设置2次，点击完成即可回到登录界面。</p>
                <i class="iconfont" style="display: none">&#xe63d;</i>
                <div style="clear: both"></div>
            </li>
            <li>2. 如何修改个人资料？
                <i class="iconfont">&#xe640;</i>
                <p>在奔犇主界面，点击“我的”，选择最上方个人信息一栏，显示有头像、昵称、奔犇号、手机号、我的二维码、积分、犇币、百姓网短号和地址，其中头像、昵称和地址可以修改。</p>
                <i class="iconfont" style="display: none">&#xe63d;</i>
                <div style="clear: both"></div>
            </li>
            <li>3. 验证码为什么收不到?
                <i class="iconfont">&#xe640;</i>
                <p>通常收不到验证码有以下几种原因：<br>
                    1.手机信息被拦截，请到手机设置-信息-黑名单中查看是否有拦截短信；<br>
                    2.被某些手机安全软件拦截，到软件信息拦截相关设置查看是否有拦截短信；<br>
                    3.手机短信设置有问题，无法接收短信，请联系当地运营商解决；<br>
                    4.手机SIM卡已损坏，请到营业厅换新的SIM卡
                </p>
                <i class="iconfont" style="display: none">&#xe63d;</i>
                <div style="clear: both"></div>
            </li>
            <li>4. 如何同步手机通讯录？
                <i class="iconfont">&#xe640;</i>
                <p>点击我的-设置-通讯录同步，进入联系人选择列表，选择需要同步的联系人后，点击右上角“同步”按钮，完成同步。</p>
                <i class="iconfont" style="display: none">&#xe63d;</i>
                <div style="clear: both"></div>
            </li>
            <li>5. 通讯录中手机好友怎么分组？
                <i class="iconfont">&#xe640;</i>
                <p>在通讯录主界面，在家人、同事、好友、未分组等分组栏目中任意一栏长按，会弹出“分组管理”窗口，点击进入分组管理主界面，点击右上角“+”按钮是增加新的分组，点击每列分组左侧“-”按钮是删除该分组，点击分组名称右边“铅笔”按钮是修改分组的名称，点击分组右侧的“+添加新成员”就是可以从其他分组选择成员加入这个分组。</p>
                <i class="iconfont" style="display: none">&#xe63d;</i>
                <div style="clear: both"></div>
            </li>
            <li>6. 怎么开启定位功能？
                <i class="iconfont">&#xe640;</i>
                <p>大多数手机打开定位的方式基本相同，请点击手机设置-定位服务，勾选GPS选项即可（有些手机还有其他GPS辅助选项，可以一并勾选打开）。</p>
                <i class="iconfont" style="display: none">&#xe63d;</i>
                <div style="clear: both"></div>
            </li>
            <li>7. 如何扫别人的二维码名片加为好友？
                <i class="iconfont">&#xe640;</i>
                <p>奔犇用户A想加用户B为好友，首先用户B点击选择我的-个人信息，在我的二维码一栏点击二维码图片，此时二维码图片是放大高清显示的，接着用户A点击选择发现-扫一扫，用手机摄像头扫描已打开的二维码图片，马上会弹出加为好友的提示框，选择确定即可。</p>
                <i class="iconfont" style="display: none">&#xe63d;</i>
                <div style="clear: both"></div> </li>
        </ul>
    </div>
    <div class="main">
        <h1>问题分类</h1>
        <ul>
            <li><a href="<?php echo $this->createUrl('special1',array("key"=>"iphone")); ?>"><div>1.账号与登录</div></a></li>
            <li><a href="<?php echo $this->createUrl('special2',array("key"=>"iphone")); ?>"><div>2.消息中心</div></a></li>
            <li><a href="<?php echo $this->createUrl('special3',array("key"=>"iphone")); ?>"><div>3.政企通讯录</div></a></li>
            <li><a href="<?php echo $this->createUrl('special4',array("key"=>"iphone")); ?>"><div>4.群组</div></a></li>
            <li><a href="<?php echo $this->createUrl('special5',array("key"=>"iphone")); ?>"><div>5.号码直通车</div></a></li>
            <li><a href="<?php echo $this->createUrl('special6',array("key"=>"iphone")); ?>"><div>6.手机好友</div></a></li>
            <li><a href="<?php echo $this->createUrl('special7',array("key"=>"iphone")); ?>"><div>7.我要买</div></a></li>
            <li><a href="<?php echo $this->createUrl('special8',array("key"=>"iphone")); ?>"><div>8.我的好友联盟</div></a></li>
            <li><a href="<?php echo $this->createUrl('special9',array("key"=>"iphone")); ?>"><div>9.百姓网</div></a></li>
            <li><a href="<?php echo $this->createUrl('special10',array("key"=>"iphone")); ?>"><div>10.将好友加入奔犇</div></a></li>
            <li><a href="<?php echo $this->createUrl('special11',array("key"=>"iphone")); ?>"><div>11.设置</div></a></li>
        </ul>
    </div>
</body>
<script src="http://apps.bdimg.com/libs/zepto/1.1.4/zepto.min.js"></script>
<script>
    $("ul>li").on("click",function(e){
        var _this=this;
        $.each($("ul>li p"),function(kk,vv){
            if (vv!=$(_this).children("p")[0]) {
                $(vv).hide();
                $(vv).next(".iconfont").hide();
                $(vv).prev(".iconfont").show();
            }
        });
        $(this).children("p").toggle();
        $(this).children(".iconfont").toggle();
    });
</script>
</html>