<?php
/* @var $this FriendLeagueController */
/* @var $dataProvider CActiveDataProvider */
Yii::app ()->clientScript->registerCssFile ( Yii::app ()->request->baseUrl . "/themes/css/global.css" );
 $add_url = Yii::app()->createUrl('prizeSetting/add');
?>
<div class="main_right_content">
    <div class="main_right_content_title main_titleab">
        <div class="main_right_content_title_font">奖品设置</div>
        <div class="main_right_content_content_block_action_add"></div>
    </div>
</div>
<div ></div>
 <form  action="<?php echo Yii::app()->createUrl('prizeSetting/index')?>" method="get">
        <a style="float:left;margin-top: 7px; margin-left: 50px;" class="btn btn-success btn-sm" href="<?php echo $add_url; ?>">  添加奖品  </a>
        <div class="col-lg-1" style="float: left;">
                        <label style="margin-top: 7px;">状　　态:</label>
        </div>
        <div class="col-md-4" style="float: left;width: 150px;margin-left: 0px;">
            <select class="form-control" name="status_num" id="status">
                <option value="-1">--请选择--</option>
                <option value="0" <?php if(isset($result['status']) && $result['status'] == 0) echo 'selected="selected"';?>>下线</option>
                <option value="1" <?php if($result['status'] == 1) echo 'selected="selected"';?>>上线</option>
                <option value="2" <?php if($result['status'] == 2) echo 'selected="selected"';?>>删除</option>

            </select>
        </div>
        
        <ul style="margin-top:25px;">
             <li style="float:right;text-align:right;width:100px;padding-right:200px;">
                 <span type="submit" class="btn btn-primary" id="search_clear">清空</span>
            </li>
            <li style="float:right;text-align:right;width:100px;padding-right:120px;">
                 <input type="submit" class="btn btn-primary" value="查询" id="submit_search">
            </li>
        </ul>
 </form>

<div class="main_right_content_content" style="background: #F5F5F5;">
    <table class="table table-bordered table-hover definewidth m10" >

        <thead>
        <tr class="main_right_content_content_title">
            <th width="10%">奖项</th>
            <th width="10%">奖品名</th>
            <th width="10%">中奖次数</th>
            <th width="15%">最近一次中奖时间</th>
            <th width="15%">状态</th>
            <th width="20%">操作</th>
        </tr>
        </thead>
        <tbody>
            <?php
                foreach ( $items as $item ) {
             ?>
                <tr class="main_right_content_content_body">
                    <td><img src="/<?php echo $item->prize;?>" width="80" height="80"></td>
                    <td><?php echo $item->prize_name;?></td>
                    <td><?php echo $item->frequency;?></td>
                    <td><?php if($item->last_time){echo date('Y-m-d H:i:s',$item->last_time);} ?></td>
                    <td class="status">
                        <?php  if($item->statues== 0){echo "下线";}?>
                        <?php  if($item->statues== 1){echo "上线中";}?>
                        <?php  if($item->statues== 2){echo "已删除";}?>
                    </td>
                    <td>
                    <?php  if($item->statues== 1){ ?> 
                                <a class="btn btn-primary btn-sm down" type="submit" name="<?php echo $item ->id ?>">下线</a>
                        <?php } ?>
                        <?php  if($item->statues== 0){?>
                            <a class="btn btn-primary btn-sm up" type="submit" name="<?php echo $item ->id ?>">上线</a>
                            <a class="btn btn-primary btn-sm delete" type="submit" name="<?php echo $item ->id ?>">删除</a>
                        <?php } ?>                        
                        <?php  if($item->statues== 2){?>
                            <span class="btn btn-success btn-sm ">已删除</span>
                        <?php } ?> 
                    </td>
                </tr>
        </tbody>
        <?php } ?>
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
    //下线
    $(".down").on("click",function(){
        var _this=this;
        url="<?php echo Yii::app()->createUrl("prizeSetting/down")?>";//json输出
        $.post(url, {id:_this.name}, function(data){
            if (data.status==1) {
                // $(_this).after('<a class="btn btn-primary btn-sm delete" name='+data.id+'>删除</a>');
                // $(_this).after('<a class="btn btn-primary btn-sm up" name='+data.id+'>上线</a>');
                // $(_this).css({
                //     display: 'none',
                // });
                // $(_this).parent().parent().find(".status").text("下线");
                location.reload();
                alert("操作成功！");
            }else{
                alert("网络错误！");
            };
        },'json');
    });


    //上线
    $(".up").on("click",function(){
        var _this=this;
        url="<?php echo Yii::app()->createUrl("prizeSetting/up")?>";//json输出
        $.post(url, {id:_this.name}, function(data){
            if (data.status==1) {
                // $(_this).after('<a class="btn btn-primary down btn-sm" name='+data.id+'>下线</a>');
                // $(_this).css({
                //     display: 'none',
                // });
                // $(_this).parent().parent().find(".status").text("上线中");
                location.reload();
                alert("操作成功！");
            }else if (data.status==0){
                alert("网络错误！");
            }else if (data.status==2){
                alert("无可中奖次数！");
            };
        },'json');
    });
    //删除
    $(".delete").on("click",function(){
        var _this=this;
        url="<?php echo Yii::app()->createUrl("prizeSetting/delete")?>";//json输出
        $.post(url, {id:_this.name}, function(data){
            if (data.status==1) {
            location.reload();
                alert("操作成功！");
            }else{
                alert("网络错误！");
            };
        },'json');
    });
</script>