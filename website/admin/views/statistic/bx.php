<div class="main_right_content">
	<div class="main_right_content_title main_titleab">
		<div class="main_right_content_title_font">百姓网统计</div>
		<div class="main_right_content_content_block_action_add">
			
		</div>
</div>
<div class="main_right_content_content">
			<div style="width:100%;float:left;">
            <table cellspacing=1 border="0" class="table table-hover" style="width:49%;float:left;border-right:1px solid #ddd;border-bottom:1px solid #ddd">
                <thead>
                    <tr class="main_right_content_content_title">
                        <td colspan=4>好友有百姓网数量</td>
                    </tr>
                    <tr class="main_right_content_content_title">
                        <td width="8%">百姓网数量范围</td>
                        <td width="8%">数量(人)</td>
                        <td width="8%">比例</td>
                        <td width="8%">操作</td>
                    </tr>
                </thead>
                <tbody>
                    <?php   
                    foreach ( $friendInfo as  $key => $item ) {

                        
                    ?>
                        <tr class="main_right_content_content_body">
                            <td><?php echo $item['name']; ?></td>
                            <td><?php echo $item['number']; ?></td>
                            <td><?php if($isBenben) echo round(($item['number']/$isBenben)*100).'%'; ?></td>
                            <td><a  class="btn btn-primary btn-sm" href="<?php  echo Yii::app()->createUrl('statistic/detail', array('key'=>$key-1, 'type'=>1))?>">详情</a></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <table cellspacing=1 border="0" class="table table-hover" style="width:49%;float:right;border-left:1px solid #ddd;border-bottom:1px solid #ddd">
                <thead>
                    <tr class="main_right_content_content_title">
                        <td colspan=4>用户来源统计</td>
                    </tr>
                    <tr class="main_right_content_content_title">
                        <td width="8%">状态</td>
                        <td width="8%">数量(人)</td>
                        <td width="8%">比例</td>
                        <td width="8%">操作</td>
                    </tr>
                </thead>
                <tbody>
    
                        <tr class="main_right_content_content_body">
                            <td>主动加入</td>
                            <td><?php echo $selfAdd; ?></td>
                            <td><?php if($allAdd) echo round(($selfAdd/$allAdd)*100).'%'; ?></td>
                            <td><a  target="_blank" class="btn btn-primary btn-sm" href="<?php  echo Yii::app()->createUrl('statistic/download', array('key'=>0, 'type'=>2))?>">报表</a></td>
                        </tr>
                         <tr class="main_right_content_content_body">
                            <td>被动加入</td>
                            <td><?php echo $allAdd- $selfAdd; ?></td>
                            <td><?php if($allAdd) echo round((($allAdd- $selfAdd)/$allAdd)*100).'%'; ?></td>
                            <td><a  target="_blank" class="btn btn-primary btn-sm" href="<?php  echo Yii::app()->createUrl('statistic/download', array('key'=>1, 'type'=>2))?>">报表</a></td>
                        </tr>
                </tbody>
            </table>
        </div>

        <div style="width:100%;float:left;">
            <table cellspacing=1 border="0" class="table table-hover" style="width:49%;float:left;border-right:1px solid #ddd;border-bottom:1px solid #ddd">
                <thead>
                    <tr class="main_right_content_content_title">
                        <td colspan=4>百姓网中是否为奔犇用户</td>
                    </tr>
                    <tr class="main_right_content_content_title">
                        <td width="8%">状态</td>
                        <td width="8%">数量(人)</td>
                        <td width="8%">比例</td>
                        <td width="8%">操作</td>
                    </tr>
                </thead>
                <tbody>
                     <tr class="main_right_content_content_body">
                            <td>是</td>
                            <td><?php echo $isBenben; ?></td>
                            <td><?php if($allAdd) echo round(($isBenben/$allAdd)*100).'%'; ?></td>
                            <td><a  target="_blank" class="btn btn-primary btn-sm" href="<?php  echo Yii::app()->createUrl('statistic/download', array('key'=>1, 'type'=>3))?>">报表</a></td>
                        </tr>
                         <tr class="main_right_content_content_body">
                            <td>否</td>
                            <td><?php echo $allAdd- $isBenben; ?></td>
                            <td><?php if($allAdd) echo round((($allAdd- $isBenben)/$allAdd)*100).'%'; ?></td>
                            <td><a  target="_blank" class="btn btn-primary btn-sm" href="<?php  echo Yii::app()->createUrl('statistic/download', array('key'=>0, 'type'=>3))?>">报表</a></td>
                        </tr>
                </tbody>
            </table>

            <table cellspacing=1 border="0" class="table table-hover" style="width:49%;float:right;border-left:1px solid #ddd;border-bottom:1px solid #ddd">
                <thead>
                    <tr class="main_right_content_content_title">
                        <td colspan=4>百姓网在网时长</td>
                    </tr>
                    <tr class="main_right_content_content_title">
                        <td width="8%">在网时长</td>
                        <td width="8%">数量(人)</td>
                        <td width="8%">比例</td>
                        <td width="8%">操作</td>
                    </tr>
                </thead>
                <tbody>
    
                        <tr class="main_right_content_content_body">
                            <td>0-3个月</td>
                            <td><?php echo $lessTime; ?></td>
                            <td><?php if($lessTime + $moreTime) echo round(($lessTime/($lessTime + $moreTime))*100).'%'; ?></td>
                            <td><a  target="_blank" class="btn btn-primary btn-sm" href="<?php  echo Yii::app()->createUrl('statistic/download', array('key'=>0, 'type'=>4))?>">报表</a></td>
                        </tr>
                         <tr class="main_right_content_content_body">
                            <td>3个月以上</td>
                            <td><?php echo $moreTime;?></td>
                            <td><?php if($lessTime + $moreTime) echo round(($moreTime/($lessTime + $moreTime))*100).'%'; ?></td>
                            <td><a  target="_blank" class="btn btn-primary btn-sm" href="<?php  echo Yii::app()->createUrl('statistic/download', array('key'=>1, 'type'=>4))?>">报表</a></td>
                        </tr>
                </tbody>
            </table>
        </div>

</div>