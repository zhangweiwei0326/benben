<div class="main_right_content">
	<div class="main_right_content_title main_titleab">
		<div class="main_right_content_title_font">用户统计</div>
<!-- 		<div class="main_right_content_content_block_action_add"> -->
<!-- 			<a href="https://www.umeng.com/sso/login" target="_blank" class="btn btn-primary">友盟统计</a> -->
<!-- 		</div> -->
</div>
<div class="main_right_content_title1">
		<div class="main_right_content_content_block_action_add">
			<a style="margin-top: 6px" href="https://www.umeng.com/sso/login" target="_blank" class="btn btn-primary">友盟统计</a>
		</div>
</div>
<div class="main_right_content_content">
			<div style="width:100%;float:left;">
            <table cellspacing=1 border="0" class="table table-hover" style="width:49%;float:left;border-right:1px solid #ddd;border-bottom:1px solid #ddd">
                <thead>
                    <tr class="main_right_content_content_title">
                        <td colspan=4>型号统计</td>
                    </tr>
                    <tr class="main_right_content_content_title">
                        <td width="8%">型号</td>
                        <td width="8%">数量(人)</td>
                        <td width="8%">比例</td>
                    </tr>
                </thead>
                <tbody>
                    <?php   
                    foreach ( $phone as  $key => $item ) {

                        
                    ?>
                        <tr class="main_right_content_content_body">
                            <td><?php echo $item; ?></td>
                            <td><?php echo $phonevalue[$key]; ?></td>
                            <td><?php echo round(($phonevalue[$key]/$totalNumber)*100).'%'; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <table cellspacing=1 border="0" class="table table-hover" style="width:49%;float:right;border-left:1px solid #ddd;border-bottom:1px solid #ddd">
                <thead>
                    <tr class="main_right_content_content_title">
                        <td colspan=4>用户实名统计</td>
                    </tr>
                    <tr class="main_right_content_content_title">
                        <td width="8%">状态</td>
                        <td width="8%">数量(人)</td>
                        <td width="8%">比例</td>
                    </tr>
                </thead>
                <tbody>
    
                        <tr class="main_right_content_content_body">
                            <td>有身份证</td>
                            <td><?php echo $haveId; ?></td>
                            <td><?php echo round(($haveId/$totalNumber)*100).'%'; ?></td>
                        </tr>
                         <tr class="main_right_content_content_body">
                            <td>无身份证</td>
                            <td><?php echo $totalNumber- $haveId; ?></td>
                            <td><?php echo round((($totalNumber- $haveId)/$totalNumber)*100).'%'; ?></td>
                        </tr>
                </tbody>
            </table>
        </div>

        <div style="width:100%">
            <table cellspacing=1 border="0" class="table table-hover" style="width:49%;float:left;border-right:1px solid #ddd;border-bottom:1px solid #ddd">
                <thead>
                    <tr class="main_right_content_content_title">
                        <td colspan=4>通讯录中奔犇好友数量</td>
                    </tr>
                    <tr class="main_right_content_content_title">
                        <td width="8%">奔犇好友范围</td>
                        <td width="8%">数量(人)</td>
                        <td width="8%">比例</td>
                    </tr>
                </thead>
                <tbody>
                    <?php   
                    foreach ( $friendInfo as  $key => $item ) {

                        
                    ?>
                        <tr class="main_right_content_content_body">
                            <td><?php echo $item['name']; ?></td>
                            <td><?php echo $item['number']; ?></td>
                            <td><?php echo round(($item['number']/$totalNumber)*100).'%'; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

             <table cellspacing=1 border="0" class="table table-hover" style="width:49%;float:right;border-left:1px solid #ddd;border-bottom:1px solid #ddd">
                <thead>
                    <tr class="main_right_content_content_title">
                        <td colspan=4>邀请奔犇好友数量</td>
                    </tr>
                    <tr class="main_right_content_content_title">
                        <td width="8%">邀请范围</td>
                        <td width="8%">数量(人)</td>
                        <td width="8%">比例</td>
                    </tr>
                </thead>
                <tbody>
                    <?php   
                    foreach ( $inviteInfo as  $key => $item ) {

                        
                    ?>
                        <tr class="main_right_content_content_body">
                            <td><?php echo $item['name']; ?></td>
                            <td><?php echo $item['number']; ?></td>
                            <td><?php echo round(($item['number']/$totalNumber)*100).'%'; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

</div>