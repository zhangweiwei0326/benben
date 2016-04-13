<?php
Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/themes/css/content.css");
?>
<?php
/* @var $this RoleController */
/* @var $model Role */
/* @var $form CActiveForm */
?>
<style>
    table{background:none}
</style>



<div class="main_right_content">
    <ol class="breadcrumb main_right_content_breadcrumb">
        <li><a href="<?php echo Yii::app()->createUrl('role/index')?>">角色管理</a></li>
        <li><a href="javascript:void"><?php if($_GET['add']){echo "增加角色";}else{echo "编辑角色";}?></a></li>
    </ol>

    <div class="main_right_content_content">
        <?php if($msg) {?>
            <div class="alert alert-danger alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <strong>警告！</strong> <?php echo $msg;?>
            </div>
        <?php }?>
        <?php $form=$this->beginWidget('CActiveForm', array(
            'id'=>'role-form',
            'htmlOptions'=>array('enctype'=>'multipart/form-data', 'class'=>'form-horizontal'),
            'enableAjaxValidation'=>false,
        )); ?>

        <div class="form-group">
            <?php echo $form->labelEx($model,'role_name', array("class"=>"col-sm-2 control-label"));?>
            <div class="col-sm-8">
                <?php echo $form->textField($model,'role_name',array('class'=>'form-control','size'=>'20','maxlength'=>'20')); ?>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label " for="Role_role_name">角色权限</label>
            <div class="col-sm-8">
                <table class="table table-bordered table-condensed table-hover table-striped">
                    <tbody>
                    <tr>
                        <td rowspan="4">百姓网管理</td>
                        <td>
									<span id="Role_dobaixing0">
										<input id="Role_dobaixing_0" value="1" type="checkbox" <?php if(($model->dobaixing & 1) && ($model->dobaixing & 2) && ($model->dobaixing & 32)) echo "checked=checked ";?>name="Role[dobaixing][editall]">
										<label for="Role_dobaixing_0">百姓网管理</label>
									</span>
                        </td>
                        <td>
									<span id="Role_dobaixing1">
										<input class="Role_dobaixing_1" value="1" type="checkbox" <?php if($model->dobaixing & 32) echo "checked=checked ";?>name="Role[dobaixing][index]">
										<label for="Role_dobaixing_1">用户查看权限</label>
									</span>
									<span id="Role_dobaixing1">
										<input class="Role_dobaixing_1" value="1" type="checkbox" <?php if($model->dobaixing & 1) echo "checked=checked ";?>name="Role[dobaixing][edit]">
										<label for="Role_dobaixing_1">用户编辑权限</label>
									</span>
									<span id="Role_dobaixing2">
										<input class="Role_dobaixing_1" value="1" type="checkbox" <?php if($model->dobaixing & 2) echo "checked=checked ";?>name="Role[dobaixing][put]">
										<label for="Role_dobaixing_2">用户导出权限</label>
									</span>
                        </td>
                    </tr>
                    <tr>
                        <td>
									<span id="Role_dobaixing3">
										<input id="Role_dobaixing_3" value="1" type="checkbox" <?php if($model->dobaixing & 4) echo "checked=checked ";?>name="Role[dobaixing][putapply]">
										<label for="Role_dobaixing_3">导出申请数据</label>
									</span>
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>
									<span id="Role_dobaixing4">
										<input id="Role_dobaixing_4" value="1" type="checkbox" <?php if($model->dobaixing & 8) echo "checked=checked ";?>name="Role[dobaixing][input]">
										<label for="Role_dobaixing_4">批量录入数据</label>
									</span>
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>
									<span id="Role_dobaixing5">
										<input id="Role_dobaixing_5" value="1" type="checkbox" <?php if($model->dobaixing & 16) echo "checked=checked ";?>name="Role[dobaixing][statistic]">
										<label for="Role_dobaixing_5">百姓网统计</label>
									</span>
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td rowspan="5">系统管理</td>
                        <td>
									<span id="Role_domember0">
										<input id="" value="1" type="checkbox" <?php if($model->dosystem & 1) echo "checked=checked ";?>name="Role[dosystem][user]">
										<label for="Role_domember_0">用户管理</label>
									</span>
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>
									<span id="Role_domember1">
										<input id="" value="1" type="checkbox" <?php if($model->dosystem & 2) echo "checked=checked ";?>name="Role[dosystem][role]">
										<label for="Role_domember_1">角色管理</label>
									</span>
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>
									<span id="Role_domember1">
										<input id="" value="1" type="checkbox" <?php if($model->dosystem & 4) echo "checked=checked ";?>name="Role[dosystem][password]">
										<label for="Role_domember_1">个人密码修改</label>
									</span>
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>
									<span id="Role_domember1">
										<input id="" value="1" type="checkbox" <?php if($model->dosystem & 8) echo "checked=checked ";?>name="Role[dosystem][log]">
										<label for="Role_domember_1">登录日志</label>
									</span>
                        </td>
                        <td></td>
                    </tr>

                    </tbody>
                </table>


                <?php //echo$form->checkBoxList($model,'dobaixing',array(1=>'百姓网管理权限')); ?>
                <br>
                <?php //echo$form->checkBoxList($model,'doenterprise',array(1=>'政企通讯录管理')); ?>
                <?php //echo$form->checkBoxList($model,'dogroup',array(1=>'群组通讯录管理'), array('style'=>"margin-left:10px;")); ?>
                <?php //echo$form->checkBoxList($model,'dostore',array(1=>'号码直通车管理'), array('style'=>"margin-left:10px;")); ?>
                <br>
                <?php //echo$form->checkBoxList($model,'docreation',array(1=>'微创作管理')); ?>
                <?php //echo$form->checkBoxList($model,'dorelease',array(1=>'我要买管理'), array('style'=>"margin-left:10px;")); ?>
                <?php //echo$form->checkBoxList($model,'dofriend',array(1=>'朋友圈管理'), array('style'=>"margin-left:10px;")); ?>
                <?php //echo$form->checkBoxList($model,'dohappy',array(1=>'开心一刻管理'), array('style'=>"margin-left:10px;")); ?>
                <br>
                <?php //echo$form->checkBoxList($model,'doleague',array(1=>'好友联盟')); ?>
                <br>
                <?php //echo$form->checkBoxList($model,'donews',array(1=>'消息管理')); ?>
                <br>
                <?php //echo$form->checkBoxList($model,'dowebsite',array(1=>'网站维护')); ?>
                <br>
                <?php //echo$form->checkBoxList($model,'dosystem',array(1=>'系统管理')); ?>
            </div>
        </div>




        <div class="form-group form-group-center">
            <button class="btn btn-success btn-lg" type="">　确定　</button>
            <a class="btn btn-default btn-lg backurl" type="button" goback="-1">　取消　</a>
        </div>

        <?php
        $this->endWidget (); ?>
    </div>
</div>
<script>
    $(function(){
        var input0 = $("#Role_role_name").val();
        $(".btn-success").click(function(){
            var input = $("#Role_role_name").val();
            if(input == ""){
                alert("请输入角色名");
                return false;
            }else{
                if(input0 != input){
                    if(window.confirm('请确认对以上信息进行修改')){
                        $("#role-form").submit();
                        return true;
                    }else{
                        return false;
                    }
                }
                $("#role-form").submit();
                return true;
            }
        });
        $(document).on('click','#Role_domember_0',function(){
            var status = $(this).is(":checked");
            if(status == true){
                $(".domember_1").each(function(){
// 				$(this).prop("checked","checked");
                    this.checked = true;
//	 			 $(this).is(":checked");
                });
            }else{
                $(".domember_1").each(function(){
// 				$(this).prop("checked",false);
                    this.checked = false;
                });
                // $(".domember_1").removeAttr("checked");
            }
        });
        $(".domember_1").click(function(){
            $("#Role_domember_0").prop("checked",false);
        });

        $("#Role_dobaixing_0").click(function(){
            var status = $(this).is(":checked");
            if(status == true){
                $(".Role_dobaixing_1").each(function(){
                    this.checked = true;
                });
            }else{
                $(".Role_dobaixing_1").each(function(){
                    this.checked = false;
                });
            }
        });
        $(".Role_dobaixing_1").click(function(){
            $("#Role_dobaixing_0").prop("checked",false);
            var status = 0;
            $(".Role_dobaixing_1").each(function(){
                if($(this).is(":checked")) status++;
            });
            if(status == 3) $("#Role_dobaixing_0").prop("checked",true);
        });

        $("#Role_doenterprise_0").click(function(){
            var status = $(this).is(":checked");
            if(status == true){
                $(".Role_doenterprise_1").each(function(){
                    this.checked = true;
                });
            }else{
                $(".Role_doenterprise_1").each(function(){
                    this.checked = false;
                });
            }
        });
        $(".Role_doenterprise_1").click(function(){
            $("#Role_doenterprise_0").prop("checked",false);
        });

        $("#Role_dostore_0").click(function(){
            var status = $(this).is(":checked");
            if(status == true){
                $(".Role_dostore_1").each(function(){
                    this.checked = true;
                });
            }else{
                $(".Role_dostore_1").each(function(){
                    this.checked = false;
                });
            }
        });
        $(".Role_dostore_1").click(function(){
            $("#Role_dostore_0").prop("checked",false);
        });

        $("#Role_docreation_0").click(function(){
            var status = $(this).is(":checked");
            if(status == true){
                $(".Role_docreation_1").each(function(){
                    this.checked = true;
                });
            }else{
                $(".Role_docreation_1").each(function(){
                    this.checked = false;
                });
            }
        });
        $(".Role_docreation_1").click(function(){
            $("#Role_docreation_0").prop("checked",false);
        });

        $("#Role_dorelease_0").click(function(){
            var status = $(this).is(":checked");
            if(status == true){
                $(".Role_dorelease_1").each(function(){
                    this.checked = true;
                });
            }else{
                $(".Role_dorelease_1").each(function(){
                    this.checked = false;
                });
            }
        });
        $(".Role_dorelease_1").click(function(){
            $("#Role_dorelease_0").prop("checked",false);
        });

        $("#Role_dofriend_0").click(function(){
            var status = $(this).is(":checked");
            if(status == true){
                $(".Role_dofriend_1").each(function(){
                    this.checked = true;
                });
            }else{
                $(".Role_dofriend_1").each(function(){
                    this.checked = false;
                });
            }
        });
        $(".Role_dofriend_1").click(function(){
            $("#Role_dofriend_0").prop("checked",false);
        });

        $("#Role_dohappy_0").click(function(){
            var status = $(this).is(":checked");
            if(status == true){
                $(".Role_dohappy_1").each(function(){
                    this.checked = true;
                });
            }else{
                $(".Role_dohappy_1").each(function(){
                    this.checked = false;
                });
            }
        });
        $(".Role_dohappy_1").click(function(){
            $("#Role_dohappy_0").prop("checked",false);
        });

        $("#Role_doleague_0").click(function(){
            var status = $(this).is(":checked");
            if(status == true){
                $(".Role_doleague_1").each(function(){
                    this.checked = true;
                });
            }else{
                $(".Role_doleague_1").each(function(){
                    this.checked = false;
                });
            }
        });
        $(".Role_doleague_1").click(function(){
            $("#Role_doleague_0").prop("checked",false);
        });

    })
</script>
