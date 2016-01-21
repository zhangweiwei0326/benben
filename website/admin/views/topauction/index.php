<?php
/* @var $this FriendLeagueController */
/* @var $dataProvider CActiveDataProvider */
Yii::app ()->clientScript->registerCssFile ( Yii::app ()->request->baseUrl . "/themes/css/global.css" );

if (! isset ( $_GET ['province'] )) {
$_GET ['province'] = - 1;
}
if (! isset ( $_GET ['city'] )) {
$_GET ['city'] = - 1;
}
if (! isset ( $_GET ['area'] )) {
$_GET ['area'] = - 1;
}
$url = "province={$_GET['province']}&city={$_GET['city']}&area={$_GET['area']}";

?>

<div class="main_right_content">
    <div class="main_right_content_title main_titleab">
        <div class="main_right_content_title_font">拍卖管理</div>
        <div class="main_right_content_content_block_action_add"></div>
    </div>
</div>
<div class="main_right_content_content" style="background: #F5F5F5;border: 1px">
<?php if($result['msg']) {?>
            <div class="alert alert-danger alert-dismissible" role="alert">
              <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
              <strong>警告！</strong> <?php echo $result['msg'];?>
            </div>
            <?php }?>
            <form action="<?php echo Yii::app()->createUrl('topAuction/index')?>" method="get">
                <div class="row" style="margin-left: 10px;">
                    <ul style="margin-top:0;">
                        <li>
                            <div class="form-group" style="padding:0 0 0 5px;">
                                <label  for="benben_id" style="float:left;margin-top:7px;">行　　业:</label>  
                                <div class="col-sm-8" style="margin-bottom:10px;">
                                    <select class="form-control" name="industry">
                                        <option value="-1">请选择</option>
                                        <?php
                                        foreach ($industryInfo as $key => $value) {
                                            if ($key == $result['industry']) {
                                                echo '<option selected="selected" value="'.$key.'">'.$value.'</option>';
                                            }else{
                                                echo '<option value="'.$key.'">'.$value.'</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>         
                            <li style="float:right;text-align:right;width:100px;padding-right:200px;">
                                 <span type="submit" class="btn btn-primary" id="search_clear">清空</span>
                            </li>
                            <li style="float:right;text-align:right;width:100px;padding-right:120px;">
                                 <input type="submit" class="btn btn-primary" value="查询" id="submit_search">
                            </li>
                             <li>
                                <div class="form-group" style="padding:0 0 0 5px;">
                                    <label  for="sex" style="float:left;margin-top:7px;">地　　区:</label>  
                                    <div class="col-sm-8" style="margin-bottom:10px;">
                                        <select class="form-control" name="province" id="province">
                                            <option value="-1">--请选择--</option>
                                            <?php foreach ($province as $prv){?>
                                                <option value="<?php echo $prv->bid?>"  <?php if($result['province'] == $prv->bid) echo 'selected = "selected"';?>><?php echo $prv->area_name?></option>
                                            <?php  }?>
                                        </select>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="form-group" style="padding:0 0 0 5px;">
                                    <div class="col-sm-8" style="margin-bottom:10px;margin-left:54px;">
                                        <select class="form-control" name="city" id="city">
                                            <option value="-1">--请选择市--</option>
                                            <?php if(isset($res)) {foreach ($res as $prv){?>
                                                <option value="<?php echo $prv['bid'];?>"
                                                <?php if($result['city'] == $prv['bid']) echo 'selected = "selected"';?>><?php echo $prv['area_name']?></option>
                                            <?php  }}?>                 
                                        </select>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="form-group" style="padding:0 0 0 5px;">
                                    <div class="col-sm-8" style="margin-bottom:10px;margin-left:54px;">
                                        <select class="form-control" name="area"  id="area">
                                            <option value="-1">--请选择区--</option>
                                                <?php if(isset($res2)) {foreach ($res2 as $prv){?>
                                                    <option value="<?php echo $prv['bid'];?>"
                                                    <?php if($result['area'] == $prv['bid']) echo 'selected = "selected"';?>><?php echo $prv['area_name']?></option>
                                                <?php  }}?>
                                        </select>
                                    </div>
                                
                                </div>
                            </li>
                            </li>
                    </ul>
                </div>
                    <div class="col-lg-1" style="float: left;">
                        <label style="margin-top: 7px;">状　　态:</label>
                    </div>
                    <div class="col-md-4" style="float: left;width: 150px;margin-left: 0px;">
                        <select class="form-control" name="status_num" id="status">
                            <option value="-1">--请选择--</option>
                            <option value="0" <?php if(isset($result['status']) && $result['status'] == 0) echo 'selected="selected"';?>>成交</option>
                            <option value="1" <?php if($result['status'] == 1) echo 'selected="selected"';?>>流拍</option>
                            <option value="2" <?php if($result['status'] == 2) echo 'selected="selected"';?>>进行中</option>
                            <option value="3" <?php if($result['status'] == 3) echo 'selected="selected"';?>>等待中</option>
                            <option value="4" <?php if($result['status'] == 4) echo 'selected="selected"';?>>未开始</option>
                        </select>
                    </div>
                    <input type="button" style ="width:150px;height:30px;float:left;margin-left: 25px;border:1px #000000 solid;background-color: #FFFFFF;" value="批量开始" class="startAllAuction">

                    <button type="submit" style ="width:150px;height:30px;float:left;margin-left: 25px;border:1px #000000 solid;background-color: #FFFFFF;" name="tenDay" value="1" id="submit_search">  10日内到期  </button>
       

            </form>
    <table class="table table-bordered table-condensed table-hover definewidth m10" >
        <thead>
            <tr class="main_right_content_content_title">
                <th width="5%">选择<input type="button" class="SelectAll" id="SelectAll" value="全选" /></th>
                <th width="12%">拍卖区域</th>
                <th width="12%">行业</th>
                <th width="6%">置顶区域</th>
                <th width="10%">置顶开始时间</th>
                <th width="10%">置顶结束时间</th>
                <th width="6%">起拍价</th>
                <th width="6%">成交价</th>
                <th width="10%">起拍时间</th>
                <th width="10%">结束时间</th>
                <th width="5%">状态</th>
                <th width="10%">操作</th>
            </tr>
        </thead>
        <tbody>
            <?php     
            $index = 0;
            $now =time();
            foreach ( $items as $item ) {
                $edit_url = Yii::app()->createUrl('topAuction/edit',array('auction_id'=>$item->auction_id));
                if($item -> is_close == "0"){
                        if($item -> is_paid == "0" && $now > $item ->start_time && $now < $item ->end_time){
                         $status_name ="进行中";
                    }
                    if($item -> is_paid == "0" && $now < $item ->start_time){
                            $status_name ="等待中";
                    }
                }
                if($item -> is_close == "1"){
                    if($item -> is_paid =="1" && $now > $item ->end_time){
                       $status_name ="成交";  
                    }
                    if($item -> is_paid =="0" && $now > $item ->end_time){
                    $status_name ="流拍";
                    }
                    if($item -> is_paid =="0" && $now < $item ->start_time){
                        $status_name ="未开始";
                    }
                }
                //时间转换
                $item ->start_time= date('Y-m-d H:i:s',$item ->start_time);
                $item ->end_time= date('Y-m-d H:i:s',$item ->end_time);
                $item ->top_start_period= date('Y-m-d H:i:s',$item ->top_start_period);
                $item ->top_end_period= date('Y-m-d H:i:s',$item ->top_end_period);
                //置顶区域
                if($item ->place =="1"){
                    $item ->place ="置顶一";
                }else if($item ->place =="2"){
                    $item ->place ="置顶二";
                }else if($item ->place =="3"){
                    $item ->place ="置顶三";
                }else{
                    $item ->place="置顶四";
                }    
            ?>
            <tr class="main_right_content_content_body">
                <td><input type="checkbox" class="selectOne" name="<?php echo $item->auction_id; ?>" /></td>
                <td><?php echo $areaInfo[$item->province].'-'.$areaInfo[$item->city] .'-'.$areaInfo[$item->area]?></td>
                <td><?php echo $industryInfo[$item ->industry] ?></td>
                <td><?php echo $item->place ?></td>
                <td><?php echo $item->top_start_period ?></td>
                <td><?php echo $item->top_end_period ?></td>
                <td><?php echo $item->start_price ?></td>
                <td><?php if($item->is_paid == 1){ ?><span><?php echo $item->price ?></span><?php } ?></td>
                <td><?php echo $item->start_time ?></td>
                <td><?php echo $item->end_time ?></td>
                <td class="status_name"><?php echo $status_name ?></td>
                <td>
                <?php  if($status_name == "进行中"){ ?> 
                        <a class="btn btn-danger btn-sm close-auction" type="submit" name="<?php echo $item ->auction_id ?>" 
                            >关闭</a>
                <?php } ?>
                <?php  if($status_name == "成交" || $status_name == "流拍"|| $status_name == "等待中" ){?>
                    <a class="btn btn-primary btn-sm" href="<?php echo $edit_url; ?>">编辑</a>
                <?php } ?>   
                <?php  if($status_name == "未开始"){ ?> 
                    <a class="btn btn-success btn-sm open-auction" type="submit" name="<?php echo $item ->auction_id ?>">开始</a>
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
    //关闭按钮
    $(".close-auction").on("click",function(){
        var _this=this;
        url="<?php echo Yii::app()->createUrl("topAuction/closeAuction")?>";//json输出
        $.post(url, {auction_id:_this.name}, function(data){
            if (data.status) {
                $(_this).after('<a class="btn btn-primary btn-sm" href='+data.url+'>编辑</a>');
                $(_this).css({
                    display: 'none',
                });
                $(_this).parent().parent().find(".status_name").text("流拍");
                alert("操作成功！");
            }else{
                alert("网络错误！");
            };
        },'json');
    });
    //开始按钮
    $(".open-auction").on("click",function(){
        var _this=this;
        url="<?php echo Yii::app()->createUrl("topAuction/openAuction")?>";//json输出
        $.post(url, {auction_id:_this.name}, function(data){
            if (data.status==1) {
                $(_this).after('<a class="btn btn-primary btn-sm" href='+data.url+'>编辑</a>');
                $(_this).css({
                    display: 'none',
                });
                $(_this).parent().parent().find(".status_name").text("等待中");
                alert("操作成功！");
            }else{
                alert("网络错误！");
            };
        },'json');
    });
    //点击批量开始
    $(".startAllAuction").on("click",function(){
        
        var ob=confirm("您确定批量开始？");
        if(ob==true){
            //批量操作
            $(".selectOne").each(function(){
                if(this.checked){
                    url="<?php echo Yii::app()->createUrl("topAuction/openAuction")?>";//json输出
                    $.post(url, {auction_id:this.name}, function(data){
                        if (data.status == 1) {
                            $(this).after('<a class="btn btn-primary btn-sm" href='+data.url+'>编辑</a>');
                            $(this).css({
                                display: 'none',
                            });
                            $(this).parent().parent().find(".status_name").text("等待中");
                                //alert("操作成功！"+$(this).name);
                        }else{
                                //alert("操作失败，不可执行该操作"+$(this).name);
                        };
                    },'json');
                }
            });
        }else{
           
        }
        
    });
    //点击全选
    $(".SelectAll").click(function(){
       if(this.value =="全选"){
            $(".selectOne").prop('checked', true); 
            this.value="取消";
       }else if(this.value =="取消"){
            $(".selectOne").prop('checked', false); 
            this.value="全选";
       }
    });


</script>