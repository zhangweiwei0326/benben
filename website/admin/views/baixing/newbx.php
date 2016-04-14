<?php
/* @var $this BaixingController */
/* @var $dataProvider CActiveDataProvider */
Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/themes/css/global.css");
$role_arr  = json_decode(Yii::app()->session['role_arr'],true);
//是否禁用，0为默认，启用、1为禁用1周、2为禁用2周、3为禁用1个月、4为禁用3个月、5为无限期
$status = array("0"=>"启用", "1"=>"禁用1周", "2" => "禁用2周", "3" => "禁用1个月", "4" => "禁用3个月","5"=>"无限期");
?>


<?php
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/themes/js/jquery-1.11.1.min.js");
?>
<script type="text/javascript">
    $(function(){
        var page = <?php echo $pages->pageCount?>;

    });
</script>

<div class="main_right_content">
    <div class="main_right_content_title main_titleab">
        <div class="main_right_content_title_font">百姓网新增</div>
        <div class="main_right_content_content_block_action_add">
        </div>
    </div>
    <div class="main_right_content_content" style="background:#F5F5F5;">
        <?php if($msg) {?>
            <div class="alert alert-danger alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <strong>警告！</strong> <?php echo $msg;?>
            </div>
        <?php }?>
        <form action="<?php echo Yii::app()->createUrl('baixing/newbx')?>" method="get">
            <ul style="margin-top:0;">
                <li>
                    <div class="form-group" style="padding:0 0 0 5px;">
                        <label  for="bx_name" style="float:left;margin-top:7px;">名称:</label>
                        <div class="col-sm-8" style="margin-bottom:10px;">
                            <input type="text" class="form-control" name="name" id="bx_name" value="<?php echo $result['name']?>">
                        </div>
                    </div>
                </li>
                <li>
                    <div class="form-group" style="padding:0 0 0 5px;">
                        <label  for="description" style="float:left;margin-top:7px;">描述:</label>
                        <div class="col-sm-8" style="margin-bottom:10px;">
                            <input type="text" class="form-control" name="description" id="description" value="<?php echo $result['description']?>">
                        </div>
                    </div>
                </li>
                <?php $url = "description={$_GET['description']}&name={$_GET['name']}&created_time1={$_GET['created_time1']}&created_time2={$_GET['created_time2']}&dj=-1&province={$_GET['province']}&city={$_GET['city']}&area={$_GET['area']}&street={$_GET['street']}&status={$_GET['status']}";?>
                <li style="float:right;text-align:right;width:60px;padding-right:10px;">
                    <span class="btn btn-primary" id="search_more">更多</span>
                </li>

                <li style="float:right;text-align:right;width:60px;padding-right:10px;">
                    <span class="btn btn-primary" id="search_clear">清空</span>
                </li>
                <li style="float:right;text-align:right;width:60px;padding-right:10px;">
                    <input type="submit" class="btn btn-primary" value="查询" id="submit_search">
                </li>
                <li style="float:right;text-align:right;width:60px;padding-right:10px;">
                    <a href="<?php echo Yii::app()->createUrl('baixing/createbx')?>" class="btn btn-primary" id="new_bx">新建</a>
                </li>
            </ul>
            <ul class="member_ul">
                <li>
                    <div class="form-group" style="padding:0 0 0 5px;">
                        <label  for="benben_id" style="float:left;margin-top:7px;">创建时间:</label>
                        <div class="col-sm-8" style="margin-bottom:10px;">
                            <input type="text" class="form-control created_time" name="created_time1" id="created_time1" value="<?php echo $result['created_time1']?>">
                        </div>
                    </div>
                </li>
                <li>
                    <div class="form-group" style="padding:0 0 0 5px;">
                        <label  for="benben_id" style="float:left;margin-top:7px;">到:　</label>
                        <div class="col-sm-8" style="margin-bottom:10px;">
                            <input type="text" class="form-control created_time" name="created_time2" id="created_time2" value="<?php echo $result['created_time2']?>">
                        </div>
                    </div>
                </li>
                <li>
                    <div class="form-group" style="padding:0 0 0 5px;">
                        <label  for="sex" style="float:left;margin-top:7px;">状态:</label>
                        <div class="col-sm-8" style="margin-bottom:10px;">
                            <select class="form-control" name="status" id="status">

                                <option value="-1" >--请选择--</option>
                                <option value="0" <?php if($result['status'] == 0) echo 'selected = "selected"';?>><?php echo $status[0]?></option>
                                <option value="1" <?php if($result['status'] == 1) echo 'selected = "selected"';?>><?php echo $status[1]?></option>
                                <option value="2" <?php if($result['status'] == 2) echo 'selected = "selected"';?>><?php echo $status[2]?></option>
                                <option value="3" <?php if($result['status'] == 3) echo 'selected = "selected"';?>><?php echo $status[3]?></option>
                                <option value="4" <?php if($result['status'] == 4) echo 'selected = "selected"';?>><?php echo $status[4]?></option>
                                <option value="5" <?php if($result['status'] == 5) echo 'selected = "selected"';?>><?php echo $status[5]?></option>
                            </select>
                        </div>
                    </div>
                </li>
            </ul>
            <ul class="member_ul">
                <li >
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
                        <div class="col-sm-8" style="margin-bottom:10px;margin-left:29px;">
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
                        <div class="col-sm-8" style="margin-bottom:10px;margin-left:29px;">
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
            </ul>
        </form>
        <table cellspacing=1 border="0" class="table table-hover">
            <thead>
            <tr class="main_right_content_content_title">
                <td width="15%">百姓网名称</td>
                <td width="20%">描述</td>
                <td width="20%">地区</td>
                <td width="10%">现有人数</td>
                <td width="15%">创建时间</td>
                <td width="10%">开通状态</td>
                <td width="10%">操作</td>
            </tr>
            </thead>
            <tbody>
            <?php
            //省市代码获取
            $pro = array();
            $pro_arr = array();
            foreach ($items as $value){
                $pro[] = $value['province'];
                $pro[] = $value['city'];
                $pro[] = $value['area'];
                $pro[] = $value['street'];
            }

            $pro_name = $this->allareas(implode(",", $pro));
            if($pro_name){
                foreach ($pro_name as $val){
                    $pro_arr[$val['bid']] = $val['area_name'];
                }
            }

            $i=0;
            foreach ( $items as $item ) {
                $edit_url = Yii::app()->createUrl('baixing/changeinfo',array('id'=>$item->id,'page'=>$_REQUEST['page']));
                ?>
                <tr class="main_right_content_content_body <?php if($item->status == 5) echo 'danger';?>" >
                    <td><?php echo $item->name ?></td>
                    <td><?php echo $item->description ?></td>
                    <td><?php echo $pro_arr[$item->province].''.$pro_arr[$item->city] ?></td>
                    <td><?php echo $item->number ?></td>
                    <td><?php echo date("Y-m-d H:i:s", $item->created_time) ?></td>
                    <td><?php echo $status[$item->status] ?></td>

                    <td>
                        <?php if($role_arr['donewbx'] & 1){?><a class="btn btn-primary btn-sm" href="<?php echo $edit_url?>&back=<?php echo $result['goback']?>">编辑</a><?php }?>

                    </td>
                </tr>
            <?php } ?>				</tbody>
        </table>

    </div>
</div>
<input id="del_baseurl" type="hidden" value="<?php echo Yii::app()->createUrl("bxapply/delete",array('page'=>$pages->currentPage +1));?>" />
<div class="main_footer ">
    <div class="main_footer_page">
        <?php
        $path = substr(dirname(__FILE__), 0, -7)."layouts/searchPages.php";
        require_once($path);
        ?>
        <?php
        $page = intval($_GET['page']) ?  intval($_GET['page']) : 1;
        $url = explode("?", Yii::app()->request->getUrl());
        $link = Yii::app()->request->hostInfo.$url[0]."?";
        echo '<ul class="yiiPager" id="yw0">'.$this->textPage($pages->pageCount , $page, $link).'</ul>';
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


