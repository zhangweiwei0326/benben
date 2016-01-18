<!DOCTYPE html>
<html>
<head>
    <title>评论列表</title>
    <meta charset="utf-8">
    <!--允许全屏-->
    <meta content="yes" name="apple-mobile-web-app-capable">
    <meta content="yes" name="apple-touch-fullscreen">
    <meta content="telephone=no,email=no" name="format-detection">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="flexible" content="initial-dpr=2" />
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <link rel="stylesheet" href="http://cdn.amazeui.org/amazeui/2.5.0/css/amazeui.min.css">
</head>
<body>
<style type="text/css">
    .comments-info{
        width: 100%;
        font-size: 16px;
        margin-top: 10px;
        line-height: 25px;
        position: relative;
    }
    .comments-num{
        float: right;
    }
    .line{
        clear: both;
        margin: 10px 0;
        border-top: 1px solid #eee;
    }
    .ds-highlight{
        color: #d32;
    }
    .comment-main{
        position: relative;
        margin-left: 42px;
        border-radius: 0;
    }
    .comment-hd{
        display: flex;
    }
    .comment-meta{
        -webkit-box-flex: 1;
        -webkit-flex: 1;
        -ms-flex: 1;
        flex: 1;
        padding: 10px 15px;
        line-height: 1.2;
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
        font-size: 18px;
    }
    .comment-bd{
        padding: 15px;
        overflow: hidden;
    }
    .comment-footer{
        padding: 0 15px 5px;
        color: #999;
    }
    .comment-meta div.mean-rate{
        font-size: 14px;
        float: right;
        color: #999;
    }
    .comment-success{
        color: #5eb95e;
    }
    .comment-mean{
        color: #f3d21d;
    }
    .comment-danger{
        color: #dd514c;
    }
    .am-comment{
        border-bottom: 1px dashed #eee;
    }
    .reply{
        background-color: #e7e6e2;
        position: relative;
        margin: 0 15px 10px;
        padding: 5px 8px;
        font-size: 14px;
    }
    .am-comments-list>li .reply:before {
        position: absolute;
        left: 8%;
        margin-left: -7px;
        top: -3px;
        content: "";
        display: inline-block;
        width: 0;
        height: 0;
        vertical-align: middle;
        border-bottom: 7px solid #e7e6e2;
        border-right: 7px solid transparent;
        border-left: 7px solid transparent;
        border-top: 0 dotted;
        -webkit-transform: rotate(360deg);
        -ms-transform: rotate(360deg);
        transform: rotate(360deg);
        opacity: 1;
        -webkit-transition: opacity .1s;
        transition: opacity .1s;
    }
</style>
<div class="comments-info am-container">
    <div class="comments-num">
        共<span class="ds-highlight"><?php echo $result['num'] ?></span>条评价
    </div>
    <div class="comments-tabs">
        好评率：<span class="ds-highlight"><?php echo $result['mean_rate'] ?></span>
    </div>
    <div class="line"></div>
    <ul class="am-comments-list am-comments-list-flip">
        <?php foreach ($result['info'] as $k => $v) {?>
            <li class="am-comment">
                <a href="#">
                    <img src=<?php echo $v['poster']; ?> alt="" class="am-comment-avatar" width="48" height="48">
                </a>
                <div class="comment-main">
                    <header class="comment-hd">
                        <div class="comment-meta">
                            <div class="mean-rate">综合评分：
                                <?php if ($v['comment_rank']==3) {
                                    echo ('<span class="comment-success">好评</span>');
                                }else if($v['comment_rank']==2){
                                    echo ('<span class="comment-mean">中评</span>');
                                }else if($v['comment_rank']==1){
                                    echo ('<span class="comment-danger">差评</span>');
                                }?>
                            </div>
                            <span><?php echo $v['nick_name']; ?></span>
                        </div>
                    </header>
                    <div class="comment-bd">
                        <div><?php echo $v['content']; ?></div>
                    </div>
                    <footer class="comment-footer">
                        <div><?php echo date("Y-m-d",$v['add_time']); ?></div>
                    </footer>
                    <div class="reply" <?php if(!$v['reply']){echo("style='display:none;'");}?>>
                        <?php foreach ($v['reply'] as $kk => $vv) {?>
                            <div><span><?php echo $vv['nick_name'];?></span>回复：<span><?php echo $vv['content'];?></span></div>
                        <?php }?>
                    </div>
                </div>
            </li>
        <?php } ?>
    </ul>
</div>
</body>
</html>