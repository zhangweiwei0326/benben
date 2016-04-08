<?php
Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/themes/css/content.css");
?>
<?php if($msg) {?>
    <div class="alert alert-danger alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <strong>警告！</strong> <?php echo $msg;?>
    </div>
<?php }?>
<div class="main_right_content">
    <ol class="breadcrumb main_right_content_breadcrumb">
        <li><a href="<?php echo Yii::app()->createUrl('topAuction/index')?>">拍卖管理</a></li>
        <li><a href="#">拍卖新增</a></li>
    </ol>
    <div class="main_right_content_content container">
        <form action="<?php echo Yii::app()->createUrl('topAuction/new')?>" method="post" id="auction-form">
            <div class="form-group">
                <div class="row">
                <label for="province" class="col-lg-2 control-label">拍卖地区</label>
                <div class="col-lg-3">
                    <select class="form-control" name="province" id="province">
                        <option value="-1">请选择</option>
                        <?php foreach ($province as $prv){?>
                            <option value="<?php echo $prv->bid?>"><?php echo $prv->area_name?></option>
                        <?php  }?>
                    </select>
                </div>
                <div class="col-lg-3">
                    <select class="form-control" name="city" id="city">
                        <option value="-1">请选择</option>
                    </select>
                </div>
                <div class="col-lg-3">
                    <select class="form-control" name="area" id="area">
                        <option value="-1">请选择</option>
                    </select>
                </div>
                </div>
                <div class="clearfix"></div>
                <span class="help-block" id="help-province"></span>
            </div>
            <div class="form-group">
                <div class="row">
                <label  for="industry" class="col-lg-2 control-label">行　　业</label>
                <div class="col-lg-10">
                    <select class="form-control" name="industry" id="industry">
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
                <div class="clearfix"></div>
                <span class="help-block" id="help-industry"></span>
            </div>
            <div class="form-group">
                <div class="row">
                <label class="control-label col-lg-2" for="start-time">开始时间</label>
                <input type="text" class="form-control datetimepicker col-lg-10" id="start-time" name="start_time">
                </div>
                <div class="clearfix"></div>
                <span class="help-block" id="help-start-time"></span>
            </div>
            <div class="form-group">
                <div class="row">
                <label class="control-label col-lg-2" for="end-time">结束时间</label>
                <input type="text" class="form-control datetimepicker col-lg-10" id="end-time" name="end_time">
                </div>
                <div class="clearfix"></div>
                <span class="help-block" id="help-end-time"></span>
            </div>
            <div class="form-group">
                <div class="row">
                <label class="control-label col-lg-2" for="start-price">起拍价</label>
                <input type="text" class="form-control col-lg-10" id="start-price" name="start_price" value="0.01">
                </div>
                <div class="clearfix"></div>
                <span class="help-block" id="help-start-price"></span>
            </div>
            <div class="form-group">
                <div class="row">
                <label class="control-label col-lg-2" for="add-step">最小加价</label>
                <input type="text" class="form-control col-lg-10" id="add-step" name="add_step" value="0.01">
                </div>
                <div class="clearfix"></div>
                <span class="help-block" id="help-add-step"></span>
            </div>
            <div class="form-group">
                <div class="row">
                <label class="control-label col-lg-2" for="guarantee">保证金</label>
                <input type="text" class="form-control col-lg-10" id="guarantee" name="guarantee" value="0.01">
                </div>
                <div class="clearfix"></div>
                <span class="help-block" id="help-guarantee"></span>
            </div>
            <div class="form-group">
                <div class="row">
                <label class="control-label col-lg-2" for="top-start-period">置顶起始时间</label>
                <input type="text" class="col-lg-10 form-control datetimepicker" id="top-start-period" name="top_start_period">
                </div>
                <div class="clearfix"></div>
                <span class="help-block" id="help-start-period"></span>
            </div>
            <div class="form-group">
                <div class="row">
                <label class="control-label col-lg-2" for="top-end-period">置顶结束时间</label>
                <input type="text" class="col-lg-10 form-control datetimepicker" id="top-end-period" name="top_end_period">
                </div>
                <div class="clearfix"></div>
                <span class="help-block" id="help-end-period"></span>
            </div>
            <div class="form-group">
                <div class="row">
                <label class="control-label col-lg-2">拍卖开启</label>
                <div class="form-control col-lg-10">
                    <label class="radio-inline" for="close">
                        <input type="radio" name="is_close" id="close" value="1">关闭
                    </label>
                    <label class="radio-inline" for="open">
                        <input type="radio" name="is_close" id="open" value="0" checked>开启
                    </label>
                </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="form-group">
                <div class="row">
                <label class="control-label col-lg-2">是否成功付款</label>
                <div class="form-control col-lg-10">
                    <label class="radio-inline" for="paid">
                        <input type="radio" name="is_paid" id="paid" value="1">已支付
                    </label>
                    <label class="radio-inline" for="will-pay">
                        <input type="radio" name="is_paid" id="will-pay" value="0" checked>未支付
                    </label>
                </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="form-group form-group-center">
                <a class="btn btn-success btn-lg enter-save">保存</a>
                <a class="btn btn-default btn-lg backurl" type="button" goback="<?php echo $_GET['back'] ? $_GET['back'] : -1;?>">　取消　</a>
            </div>
        </form>
    </div>
</div>
<script src='<?php echo Yii::app()->request->baseUrl."/themes/js/jquery-ui-timepicker-addon.js"?>'></script>
<script src='<?php echo Yii::app()->request->baseUrl."/themes/js/jquery-ui-timepicker-zh-CN.js"?>'></script>
<script>
    $('.datetimepicker').datetimepicker({
        timeFormat: "HH:mm:ss",
        dateFormat: "yy-mm-dd"
    });
</script>
<script type="text/javascript">
    //点击确定提交
    $(".enter-save").on("click",function(){
        $(".form-group").removeClass("has-error");
        $(".help-block").text("");
        //获取数据
        var _this=this;
        var province =$("#province").val();
        var city =$("#city").val();
        var area =$("#area").val();
        var industry=$("#industry").val();
        var startTime=$("#start-time").val();
        var endTime=$("#end-time").val();
        var startPrice=$("#start-price").val();
        var addStep=$("#add-step").val();
        var guarantee=$("#guarantee").val();
        var topStartPeriod=$("#top-start-period").val();
        var topEndPeriod=$("#top-end-period").val();

        if(province<=0||city<=0||area<=0){
            $("#help-province").text("必选！");
            $("#help-province").parents(".form-group").addClass("has-error");
            return false;
        }

//        if(industry<=0){
//            $("#help-industry").text("必选！");
//            $("#help-industry").parents(".form-group").addClass("has-error");
//            return false;
//        }

        if(!startTime){
            $("#help-start-time").text("必选！");
            $("#help-start-time").parents(".form-group").addClass("has-error");
            return false;
        }

        if(!endTime){
            $("#help-end-time").text("必选！");
            $("#help-end-time").parents(".form-group").addClass("has-error");
            return false;
        }

        if(!startPrice){
            $("#help-start-price").text("必选！");
            $("#help-start-price").parents(".form-group").addClass("has-error");
            return false;
        }

        if(!addStep){
            $("#help-add-step").text("必选！");
            $("#help-add-step").parents(".form-group").addClass("has-error");
            return false;
        }

        if(!guarantee){
            $("#help-guarantee").text("必选！");
            $("#help-guarantee").parents(".form-group").addClass("has-error");
            return false;
        }

        if(!topEndPeriod){
            $("#help-top-end-period").text("必选！");
            $("#help-top-end-period").parents(".form-group").addClass("has-error");
            return false;
        }

        if(!topStartPeriod){
            $("#help-top-start-period").text("必选！");
            $("#help-top-start-period").parents(".form-group").addClass("has-error");
            return false;
        }

        $("form").submit();
    });
</script>

