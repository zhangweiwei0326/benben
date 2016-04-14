<?php
/* @var $this BxUserController */
/* @var $dataProvider CActiveDataProvider */
Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/themes/css/global.css");
?>

<div class="main_right_content">
    <div class="main_right_content_title">
        <div class="main_right_content_title_font">百姓网管理员管理</div>
        <div class="main_right_content_content_block_action_add">
            <?php if($role & 1){?>
                <a class="btn btn-success" href="<?php echo Yii::app()->createUrl("bxUser/create?add=add");?>">增加</a>
            <?php }?>
        </div>
    </div>
    <div class="main_right_content_content">
        <table cellspacing=1 border="0" class="table table-hover">
            <thead>
            <tr class="main_right_content_content_title">
                <td width="10%">用户名</td>
                <td width="10%">用户角色</td>
                <td width="20%">百姓网</td>
                <td width="20%">创建时间</td>
                <td width="20%">最近登录时间</td>
                <td width="20%">操作</td>
            </tr>
            </thead>
            <tbody>
            <?php					$i=0;
            foreach ( $items as $item ) {
                $edit_url = Yii::app()->createUrl('bxUser/update',array('id'=>$item->id,'page'=>$_REQUEST['page']));
                ?>
                <tr class="main_right_content_content_body">
                    <td><?php echo $item->username ?></td>
                    <td><?php echo $item->rname ?></td>
                    <td><?php echo $item->enterprise_name ?></td>
                    <td><?php echo date('	Y-m-d H:i:s', $item->created_time) ?></td>
                    <td><?php if ($item->last_login==0){echo "暂无记录";}else{echo date('	Y-m-d H:i:s', $item->last_login);} ?></td>
                    <td>
                        <a class="btn btn-primary btn-sm" href="<?php echo $edit_url?>">编辑</a>
                        <?php if($role & 1){
                            if($item->disable){?>
                                <a class="btn btn-danger btn-sm" data="<?php echo $item->id;?>">已禁用</a>
                            <?php }else{?>
                                <a class="btn btn-danger btn-sm action_disable" data="<?php echo $item->id;?>">禁用</a>
                            <?php }?>
                            <a class="btn btn-danger btn-sm action_del" data="<?php echo $item->id;?>">删除</a>
                        <?php }?>
                    </td>
                </tr>
            <?php } ?>				</tbody>
        </table>

    </div>
</div>

<input id="del_baseurl" type="hidden" value="<?php echo Yii::app()->createUrl("bxUser/delete",array('page'=>$pages->currentPage +1));?>" />
<input id="dis_baseurl" type="hidden" value="<?php echo Yii::app()->createUrl("bxUser/disable",array('page'=>$pages->currentPage +1));?>" />
<div class="main_footer <?php //if($pages->pageCount <= 1) echo 'main_footer_hidden';?>">
    <div class="main_footer_page">
        <?php
        $path = substr(dirname(__FILE__), 0, -6)."layouts/searchPages.php";
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

