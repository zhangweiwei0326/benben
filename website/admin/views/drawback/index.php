<?php
/* @var $this FriendLeagueController */
/* @var $dataProvider CActiveDataProvider */
Yii::app ()->clientScript->registerCssFile ( Yii::app ()->request->baseUrl . "/themes/css/global.css" );
?>

<div class="main_right_content">
    <div class="main_right_content_title main_titleab">
        <div class="main_right_content_title_font">退款管理</div>
        <div class="main_right_content_content_block_action_add"></div>
    </div>
</div>

<div class="main_right_content_content" style="background: #F5F5F5;">
    <form
            action="<?php echo Yii::app()->createUrl('drawback/index')?>"
            method="get">
        <ul style="margin-top: 0">
            <li style="width:18%">
                <div class="form-group" style="padding: 0 0 0 5px;">
                    <label for="benben_id" style="float: left; margin-top: 7px;">状　　态:</label>
                    <div class="col-sm-8" style="margin-bottom: 10px;">
                        <select class="form-control" name="status" id="status">
                            <option value="-1">--请选择--</option>
                            <option value="0" <?php if(isset($result['status']) && $result['status'] == 0) echo 'selected="selected"';?>>退款申请中</option>
                            <option value="1" <?php if($result['status'] == 1) echo 'selected="selected"';?>>已退款</option>
                            <option value="2" <?php if($result['status'] == 2) echo 'selected="selected"';?>>商家拒绝退款</option>
                            <option value="3" <?php if($result['status'] == 3) echo 'selected="selected"';?>>退款中</option>
                            <option value="4" <?php if($result['status'] == 4) echo 'selected="selected"';?>>商家同意退款</option>
                        </select>
                    </div>
                </div>
            </li>
            <li style="width:18%">
                <div class="form-group" style="padding: 0 0 0 5px;">
                    <label for="created_time1" style="float: left; margin-top: 7px;">申请时间:</label>
                    <div class="col-sm-8" style="margin-bottom: 10px;">
                        <span><input type="text" class="form-control"
                            name="created_time1" id="created_time1"
                            value="<?php echo $result['created_time1']?>" onclick="SelectDate(this,'yyyy-MM-dd hh:mm:ss')"></span>
                    </div>
                </div>
            </li>
            <li style="width:18%;">
                <div class="form-group" style="padding: 0 0 0 5px;">
                    <label for="created_time1" style="float: left; margin-top: 7px;">到:　　　</label>
                    <div class="col-sm-8" style="margin-bottom: 10px;">
                        <input type="text" class="form-control"
                            name="created_time2" id="created_time2"
                            value="<?php echo $result['created_time2']?>" onclick="SelectDate(this,'yyyy-MM-dd hh:mm:ss')">
                    </div>
                </div>
            </li>
            <li style="float:right;text-align:right;width:60px;padding-right:10px;">
                <span type="submit" class="btn btn-primary" id="search_clear">清空</span>
            </li>
            <li style="float: right; text-align: right; width: 60px; padding-right: 10px;">
                <input
                type="submit" class="btn btn-primary" value="查询" id="submit_search">
            </li>
        </ul>
        <input type="button" style ="width:150px;height:30px;float:left;margin-left: 25px;border:1px #000000 solid;background-color: #FFFFFF;" value="批量退款" class="startAllAuction">
        <input type="button" style ="width:150px;height:30px;float:left;margin-left: 25px;border:1px #000000 solid;background-color: #FFFFFF;" value="批量拒绝" class="startAllAuction">

    </form>
    <table class="table table-bordered table-hover definewidth m10" >
        <thead>
        <tr class="main_right_content_content_title">
            <th width="5%">选择<input type="button" class="SelectAll" id="SelectAll" value="全选" /></th>
            <th width="5%">订单号</th>
            <th width="5%">申请人</th>
            <th width="8%">商家</th>
            <th width="5%">手机号</th>
            <th width="8%">支付宝帐号</th>
            <th width="15%">申请原因</th>
            <th width="15%">拒绝原因</th>
            <th width="6%">状态</th>
            <th width="10%">申请时间</th>
            <th width="10%">处理时间</th>
            <th width="10%">操作</th>

        </tr>
        </thead>
        <tbody>
            <?php     
                $index = 0;
                foreach ( $items as $item ) {
                ?>
                <tr class="main_right_content_content_body">
                    <td><input type="checkbox" class="selectOne" name="<?php echo $item->back_id; ?>" /><?php echo $item->back_id; ?></td>
                    <td><?php echo $item ->order_id; ?></td>
                    <td><?php echo $item ->name; ?></td>
                    <td><?php echo $item ->shop_name; ?></td>
                    <td><?php echo $item ->phone; ?></td>
                    <td><?php echo $item ->account; ?></td>
                    <td><?php echo $item ->apply_reason; ?></td>
                    <td><?php echo $item ->refuse_reason; ?></td>
                    <td><?php if($item ->status==1){echo "退款申请中";}
                              if($item ->status==2){echo "已退款";}
                              if($item ->status==3){echo "商家拒绝退款";}
                              if($item ->status==4){echo "退款中";} 
                              if($item ->status==5){echo "商家同意退款";} ?>
                    </td>
                    <td><?php echo date('Y-m-d h:i:s',$item ->apply_time); ?></td>
                    <td><?php echo date('Y-m-d h:i:s',$item ->deal_time); ?></td>
                    <td><?php if($item ->status==5){?>
                                <div class="btn btn-primary" onclick="payBack()">同意</div>
                                <div class="btn btn-danger" style="margin-left: 5px;" name="<?php echo $item ->back_id; ?>" onclick="refuseBack()">拒绝</div>
                        <?php }?>
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
<script>
    //同意退款
    var payBack=function(){
        var ob=confirm("您确定同意退款？");
        
        if(ob){
            location.href="";
        }
    }
    //拒绝退款
    var refuseBack=function(){
        var _this=this;
        var back_id=_this.name;
        alert(back_id);
        var ob=confirm("您确定拒绝退款？");
        url="<?php echo Yii::app()->createUrl("drawback/refuseDrawBack")?>";//json输出
        if(ob){
            alert("发送拒绝请求");
            $.post(url,{back_id:back_id},function(data){

            },'json');
        }

    }
    //批量同意

    //批量拒绝

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