<?php
/* @var $this FriendLeagueController */
/* @var $dataProvider CActiveDataProvider */
Yii::app ()->clientScript->registerCssFile ( Yii::app ()->request->baseUrl . "/themes/css/global.css" );
?>
<div class="main_right_content">
    <div class="main_right_content_title main_titleab">
        <div class="main_right_content_title_font">认证管理</div>
        <div class="main_right_content_content_block_action_add"></div>
    </div>
</div>
<div></div>
<div class="main_right_content_content" style="background: #F5F5F5;">
    <form
      action="<?php echo Yii::app()->createUrl('authentication/index')?>" method="get">
        <ul style="margin-top: 0">
            <li style="width:18%">
                <div class="form-group" style="padding: 0 0 0 5px;">
                    <label for="benben_id" style="float: left; margin-top: 7px;">状　　态:</label>
                    <div class="col-sm-8" style="margin-bottom: 10px;">
                        <select class="form-control" name="status" id="status">
                            <option value="-1">--请选择--</option>
                            <option value="0" <?php if(isset($result['status']) && $result['status'] == 0) echo 'selected="selected"';?>>待申核</option>
                            <option value="1" <?php if($result['status'] == 1) echo 'selected="selected"';?>>申核未通过</option>
                            <option value="2" <?php if($result['status'] == 1) echo 'selected="selected"';?>>审核通过</option>
                        </select>
                    </div>
                </div>
            </li>
            <li style="width:18%">
                <div class="form-group" style="padding: 0 0 0 5px;">
                    <label for="benben_id" style="float: left; margin-top: 7px;">类　　型:</label>
                    <div class="col-sm-8" style="margin-bottom: 10px;">
                        <select class="form-control" name="type" id="type">
                            <option value="-1" >--请选择--</option>
                            <option value="0" <?php if(isset($result['type']) && $result['type'] == 0) echo 'selected="selected"';?>>个人</option>
                            <option value="1" <?php if($result['type'] == 1) echo 'selected="selected"';?>>商家</option>
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
        <input type="button" style ="width:150px;height:30px;float:left;margin-left: 0px;border:1px #000000 solid;background-color: #FFFFFF;" value="批量同意" class="acceptAllAuth">
        <input type="button" style ="width:150px;height:30px;float:left;margin-left: 5px;border:1px #000000 solid;background-color: #FFFFFF;" value="批量拒绝" class="refuseAllAuth">

    </form>
    <table class="table table-bordered table-hover definewidth m10" >
        <thead>
        <tr class="main_right_content_content_title">
            <th width="4%">选择<input type="button" class="SelectAll" id="SelectAll" value="全选" /></th>
            <th width="5%">店铺号</th>
            <th width="4%">真实姓名</th>
            <th width="4%">身份证号</th>
            <th width="9%">身份证正面</th>
            <th width="9%">身份证反面</th>
            <th width="9%">营业执照</th>
            <th width="8%">单位名称</th>
            <th width="4%">类型</th>
            <th width="4%">状态</th>
            <th width="8%">提交时间</th>
            <th width="10%">审核</th>
        </tr>
        </thead>
        <tbody>
            <?php 
                $index = 0;
                foreach ( $items as $item ) {
                	$detail_url = Yii::app()->createUrl('authentication/detail',array('id' => $item->id));
             ?>
                <tr class="main_right_content_content_body">
                    <td><input type="checkbox" class="selectOne" name="<?php echo $item->id; ?>" /></td>
                    <td>13656241819</td>
                    <td><?php echo $item->real_name; ?></td>
                    <td><?php echo $item->id_card; ?></td>
                    <td><img src="<?php echo $item->poster_front; ?>"></td>
                    <td><img src="<?php echo $item->poster_back; ?>"></td>
                    <td><img src="<?php echo $item->poster_licence; ?>"></td>
                    <td><?php echo $item->company; ?></td>
                    <td class="type"><?php if($item ->type==1){echo "个人";}
                              else if($item ->type==2){echo "商家";} ?>
                    </td>
                    <td class="status"><?php if($item ->status==0){echo "待审核";}
                              if($item ->status==1){echo "申核未通过";}
                              if($item ->status==2){echo "审核通过";} ?>
                    </td>
                    <td><?php echo date('Y-m-d H:i:s',$item->time); ?></td>
                    <td>
                    	<a class="btn btn-primary authDetail" href="<?php echo $detail_url; ?>" style="margin-left: 5px;" name="<?php echo $item ->id; ?>">详情</a>
                    	<?php if($item ->status==0){?>
                                <a class="btn btn-success acceptAuth" style="margin-left: 5px;" name="<?php echo $item ->id; ?>">通过</a>
                        		<a class="btn btn-danger refuseAuth" style="margin-left: 5px;" name="<?php echo $item ->id; ?>">拒绝</a>
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
<script type="text/javascript">
	//点击通过
	$(".acceptAuth").on("click",function(){
        var _this=this;
        var id=_this.name;
        // var deal_time=new Date().getTime()/1000;
        var ob=confirm("您确定通过认证？");
        var url="<?php echo Yii::app()->createUrl("authentication/acceptAuthentication")?>";//json输出
        if(ob){
            $.post(url,{id:id
                        // deal_time:deal_time
                    },function(data){
                 if (data.status==1) {
                     $(_this).parent().parent().find(".status").text("审核通过");
                     $(_this).next().hide();
                     $(_this).hide();
                     alert("操作成功!");
                 }else{
                     alert("网络错误！");
                 }
            },'json');
        }
    });
    //点击拒绝	
	$(".refuseAuth").on("click",function(){
        var _this=this;
        var id=_this.name;
        //var deal_time=new Date().getTime()/1000;
        var ob=confirm("您确定拒绝认证？");
        var url="<?php echo Yii::app()->createUrl("authentication/refuseAuthentication")?>";//json输出
        if(ob){
            $.post(url,{id:id
                        //deal_time:deal_time
                    },function(data){
                 if (data.status==1) {
                     $(_this).parent().parent().find(".status").text("审核未通过");
                     $(_this).prev().hide();
                     $(_this).hide();
                     alert("操作成功!");
                 }else{
                     alert("网络错误！");
                 }
            },'json');
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
    //点击批量同意
    $(".acceptAllAuth").on("click",function(){
        var ob=confirm("您确定批量同意？");
        var url="<?php echo Yii::app()->createUrl("authentication/acceptAuthentication")?>";//json输出
        var deal_time=new Date().getTime()/1000;
        if(ob){
            //批量操作
            $(".selectOne").each(function(){
                if(this.checked){
                    var id=this.name;
                    $.post(url,{id:id
                        //deal_time:deal_time
                    },function(data){
                         if (data.status == 1) {
                             $(this).parent().parent().find(".status").text("商家拒绝退款");
                             $(this).siblings().hide();
                             $(this).hide();
                            
                         }else{
                            
                         }
                    },'json');
                }
             });
        }        
    });
    //点击批量拒绝
    $(".refuseAllAuth").on("click",function(){
        var ob=confirm("您确定批量拒绝？");
        var url="<?php echo Yii::app()->createUrl("authentication/refuseAuthentication")?>";//json输出
        //var deal_time=new Date().getTime()/1000;
        if(ob){
            //批量操作
            $(".selectOne").each(function(){
                if(this.checked){
                    var id=this.name;
                    $.post(url,{id:id
                        //deal_time:deal_time
                    },function(data){
                         if (data.status == 1) {
                             $(this).parent().parent().find(".status").text("商家拒绝退款");
                             $(this).siblings().hide();
                             $(this).hide();              
                         }else{
                    
                         }
                    },'json');
                }
             });
        }        
    });

    
</script>