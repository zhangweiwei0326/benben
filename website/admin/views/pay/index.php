<?php
/* @var $this PayController */
/* @var $dataProvider CActiveDataProvider */
Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl . "/themes/css/global.css");

?>

<div class="main_right_content">
    <div class="main_right_content_title">
        <div class="main_right_content_title_font">提现列表</div>
        <div class="main_right_content_content_block_action_add">
            <!-- <a class="btn btn-success" href="<?php echo Yii::app()->createUrl("storeOrderInfo/create"); ?>">增加</a> -->
        </div>
    </div>
    <div class="main_right_content_content">
        <form action="<?php echo Yii::app()->createUrl('pay/index') ?>" method="get">
            <ul>

                <li>
                    <div class="form-group" style="padding:0 0 0 5px;">
                        <label  for="order_sn" style="float:left;margin-top:4px;">提现号:　</label>
                        <div class="col-sm-8" style="margin-bottom:10px;">
                            <input type="text" class="form-control" name="pay_id" id="pay_id" value="<?php echo $result['pay_id'] ?>">
                        </div>
                    </div>
                </li>

                <li>
                    <div class="form-group" style="padding:0 0 0 5px;">
                        <label  for="sex" style="float:left;margin-top:4px;">提现状态:</label>
                        <div class="col-sm-8" style="margin-bottom:10px;">
                            <select class="form-control" name="status" id="status">
                                <option value="-1">--请选择--</option>
                                <option value="0" <?php if ($result['status'] == 0) {
                                    echo 'selected="selected"';
                                }
                                ?>>申请中</option>
                                <option value="1" <?php if ($result['status'] == 1) {
                                    echo 'selected="selected"';
                                }
                                ?>>处理成功</option>

                                <option value="2" <?php if ($result['status'] == 2) {
                                    echo 'selected="selected"';
                                }
                                ?>>处理失败</option>

                                <option value="3" <?php if ($result['status'] == 3) {
                                    echo 'selected="selected"';
                                }
                                ?>>转账中</option>

                            </select>
                        </div>
                    </div>
                </li>

                <li>
                    <div class="form-group" style="padding:0 0 0 5px;">
                        <label  for="sex" style="float:left;margin-top:4px;">支付类型:</label>
                        <div class="col-sm-8" style="margin-bottom:10px;">
                            <select class="form-control" name="type" id="type">
                                <option value="-1">--请选择--</option>
                                <option value="1" <?php if ($result['type'] == 1) {
                                    echo 'selected="selected"';
                                }
                                ?>>支付宝</option>
                            </select>
                        </div>
                    </div>
                </li>

                <li>
                    <div class="form-group" style="padding:0 0 0 5px;">
                        <label  for="nick_name" style="float:left;margin-top:4px;">用户名:</label>
                        <div class="col-sm-8" style="margin-bottom:10px;">
                            <input type="text" class="form-control" name="nick_name"  id="nick_name"  value="<?php echo $result['nick_name'] ?>">
                        </div>
                    </div>
                </li>
                <li>
                    <div class="form-group" style="padding:0 0 0 5px;">
                        <label  for="phone" style="float:left;margin-top:4px;">手机号:</label>
                        <div class="col-sm-8" style="margin-bottom:10px;">
                            <input type="text" class="form-control" name="phone"   id="phone"  value="<?php echo $result['phone'] ?>">
                        </div>
                    </div>
                </li>
                <li>
                    <div class="form-group" style="padding:0 0 0 5px;">
                        <label  for="phone" style="float:left;margin-top:4px;">支付账号:</label>
                        <div class="col-sm-8" style="margin-bottom:10px;">
                            <input type="text" class="form-control" name="account"   id="account"  value="<?php echo $result['account'] ?>">
                        </div>
                    </div>
                </li>
                <li style="float:right;text-align:right;width:60px;padding-right:10px;">
                    <input type="submit" class="btn btn-primary" value="查询" id="submit_search">
                </li>
                <li style="float:right;text-align:right;width:60px;padding-right:10px;">
                    <a class="btn btn-primary" id="pay">支付</a>
                </li>
                <li>
                    <div class="form-group" style="padding:0 0 0 5px;">
                        <label  for="created_time1" style="float:left;margin-top:4px;">申请日期:</label>
                        <div class="col-sm-8" style="margin-bottom:10px;">
                            <input type="text" class="form-control created_time" name="created_time1"  id="created_time1"  value="<?php echo $result['created_time1'] ?>">
                        </div>
                    </div>
                </li>
                <li>
                    <div class="form-group" style="padding:0 0 0 5px;">
                        <label  for="created_time1" style="float:left;margin-top:4px;">到:　</label>
                        <div class="col-sm-8" style="margin-bottom:10px;">
                            <input type="text" class="form-control created_time" name="created_time2"  id="created_time2"   value="<?php echo $result['created_time2'] ?>">
                        </div>
                    </div>
                </li>
            </ul>
        </form>
        <table cellspacing=1 border="0" class="table table-hover">
            <thead>
            <tr class="main_right_content_content_title">
                <td width="10%">支付编号</td><td width="10%">提现账号</td><td width="8%">提现状态</td><td width="15%">用户信息</td><td width="7%">支付方式</td><td width="10%">提现金额</td><td width="7%">累提金额</td><td width="15%">申请日期</td><td width="8%">操作</td>
            </tr>
            </thead>
            <tbody>
            <?php	$i = 0;
            foreach ($items as $item) {
                ?>
                <tr class="main_right_content_content_body">
                    <td><?php echo $item->id ?></td>
                    <td><?php echo $item->account?></td>
                    <td><?php switch ($item->status) {
                            case '0':
                                echo "申请中";
                                break;
                            case '1':
                                echo "处理成功";
                                break;
                            case '2':
                                echo "处理失败";
                                break;
                            case '3':
                                echo "转账中";
                                break;
                            default:
                                echo '未知状态';
                                break;
                        }?></td>

                    <td>
                        <?php
                            $model = Member::model()->find("id={$item->member_id}");
                            echo $model->nick_name.' | '.$model->phone;
                        ?>
                    </td>

                    <td><?php
                        switch ($item->type) {
                            case '1':
                                echo '支付宝';
                                break;
                            default:
                                echo '未知状态';
                                break;
                        }
                        ?></td>

                    <td><?php echo $item->fee; ?></td>
                    <td><?php
                        $model=Pay::model()->findBySql("select sum(fee) as fee from pay where status=1 and id=".$item->id);
                        echo $model['fee']?$model['fee']:0;
                        ?></td>
                    <td><?php echo date("Y/m/d H:i:s", $item->time); ?></td>
                    <td><?php if($item->status==0){ ?><a class="btn btn-primary btn-sm cancel">取消</a><?php }else{?><a class="btn btn-primary btn-sm disabled">已结束</a><?php }?></td>
                </tr>
            <?php }
            ?>				</tbody>
        </table>

    </div>
</div>

<input id="del_baseurl" type="hidden" value="<?php echo Yii::app()->createUrl("storeOrderInfo/delete", array('page' => $pages->currentPage + 1)); ?>" />
<div class="main_footer ">
    <div class="main_footer_page">
        <?php
        $path = dirname(dirname(__FILE__)) . "/layouts/searchPages.php";
        require_once $path;
        ?>
        <?php
        $page = intval($_GET['page']) ? intval($_GET['page']) : 1;
        $url = explode("?", Yii::app()->request->getUrl());
        $link = Yii::app()->request->hostInfo . $url[0] . "?";
        echo '<ul class="yiiPager" id="yw0">' . $this->textPage($pages->pageCount, $page, $link) . '</ul>';
        // 			$this->widget ( 'CLinkPager', array (
        // 					'header' => '',
        // 					'firstPageLabel' => '«首页',
        // 					'lastPageLabel' => '尾页»',
        // 					'prevPageLabel' => '«',
        // 					'nextPageLabel' => '»',
        // 					'maxButtonCount' => 6,
        // 					'pages' => $pages
        // 			) );
        ?>
    </div>
</div>
</div>
<script>
    $(".cancel").on("click",function(){
        var _this=this;
        var id=$($(this).parent().parent().children("td")[0]).text();
        var co=window.confirm("确定取消提现？");
        if(co) {
            $.ajax({
                    url: "<?php echo Yii::app()->createUrl("pay/delete"); ?>/" + id, method: "POST", dataType: "json"
                })
                .done(function (data) {
                    if (data.status) {
                        $($(_this).parent().parent().children("td")[2]).html("处理失败");
                        $(_this).parent().html('<a class="btn btn-primary btn-sm disabled">已结束</a>');
                    } else {
                        alert(data.msg);
                    }
                })
                .fail(function () {
                    alert("网络错误！");
                });
        }
    });

    $("#pay").on("click",function(){
        var co=window.confirm("你确定要支付？");
        if(co){
            window.location.href="<?php echo Yii::app()->createUrl('pay/cashOut') ?>";
        }
    });
</script>

