<?php
/* @var $this FriendLeagueController */
/* @var $dataProvider CActiveDataProvider */
Yii::app ()->clientScript->registerCssFile ( Yii::app ()->request->baseUrl . "/themes/css/global.css" );
?>
<div class="main_right_content">
    <div class="main_right_content_title main_titleab">
        <div class="main_right_content_title_font">中奖名单</div>
        <div class="main_right_content_content_block_action_add"></div>
    </div>
</div>
<div></div>
                <form action="<?php echo Yii::app()->createUrl('winningList/index')?>" method="get">
                     <button type="submit" style ="margin-top:25px;width:150px;height:30px;float:left;margin-left: 25px;border:1px #000000 solid;background-color: #FFFFFF;" name="is_send" value="0" id="submit_search">  已发奖  </button>
                      <button type="submit" style ="margin-top:25px;width:150px;height:30px;float:left;margin-left: 25px;border:1px #000000 solid;background-color: #FFFFFF;" name="is_send" value="1" id="submit_search">  未发奖  </button>
            </form>
<div class="main_right_content_content" style="background: #F5F5F5;">
    <table class="table table-bordered table-hover definewidth m10" >
        <thead>
        <tr class="main_right_content_content_title">
            <th width="10%">奔犇号</th>
            <th width="10%">手机号</th>
            <th width="10%">姓名</th>
            <th width="15%">中奖时间</th>
            <th width="10%">奖项</th>
            <th width="20%">操作</th>
        </tr>
        </thead>
        <tbody>
            <?php 
                $index = 0;
                foreach ( $items as $item ) {
             ?>
                <tr class="main_right_content_content_body">
                    <td><?php echo $item->benben_id; ?></td>
                    <td><?php echo $item->phone; ?></td>
                    <td><?php echo $item->name; ?></td>
                    <td><?php echo date('Y-m-d H:i:s',$item->lottery_time); ?></td>
                    <td><?php if($item->lottery_num== 6){echo "1犇币!";}
                                if($item->lottery_num== 7){echo "2犇币!";}
                                if($item->lottery_num== 8){echo "5犇币!";}
                                if($item->lottery_num== 5){echo "0.5犇币!";}
                                if($item->lottery_num== 4){echo "0.4犇币!";}
                                if($item->lottery_num== 3){echo "0.3犇币!";}
                                if($item->lottery_num== 2){echo "0.2犇币!";}
                                if($item->lottery_num== 1){echo "0.1犇币!";}
                                if($item->lottery_num== 9){echo "特等奖!";}
                                ?></td>
                    <td>
                        <?php  if($item->is_send== 1){ ?> 
                                <a class="btn btn-primary btn-sm send" type="submit" name="<?php echo $item ->id ?>">发放奖品</a>
                        <?php } ?>
                        <?php  if($item->is_send== 0){?>
                            <span class="btn btn-success btn-sm ">奖品已发放</span>
                        <?php } ?> 
                    </td>
                </tr>
               <?php } ?>
        </tbody>
    </table>
    <div class="main_footer_page">
          <?php 
                // $path = substr(dirname(__FILE__), 0, -11)."layouts/searchPages.php";
                // require_once($path);  
            ?> 
            <?php       
            	$page = intval($_GET['page']) ?  intval($_GET['page']) : 1;
            	$url = explode("?", Yii::app()->request->getUrl());
            	$link = Yii::app()->request->hostInfo.$url[0]."?";
            	echo '<ul class="yiiPager" id="yw0">'.$this->textPage($pages->pageCount , $page, $link).'</ul>';
            ?>
    </div>
</div>
<script type="text/javascript">
    //开始发奖
    $(".send").on("click",function(){
        var _this=this;
        url="<?php echo Yii::app()->createUrl("winningList/send")?>";//json输出
        $.post(url, {id:_this.name}, function(data){
            if (data.status==1) {
                $(_this).after('<a class="btn btn-success btn-sm" href='+data.url+'>奖品已发放</a>');
                $(_this).css({
                    display: 'none',
                });
                alert("操作成功！");
            }else{
                alert("网络错误！");
            };
        },'json');
    });
</script>