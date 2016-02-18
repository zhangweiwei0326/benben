<script type="text/javascript"
	src="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/independent/js/personal_public.js"></script>
<script type="text/javascript"
	src="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/independent/js/tools.js"></script>
<script type="text/javascript"
	src="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/independent/js/Personal-3.js"></script>		
			<!--right-->
			<div class="p-main-r fl" id="p-main-r">
				<em class="pm-r-bnn"><a href="javascript:;"><img src="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/images/rbnner.jpg"/></a></em>
				<div class="p-mian-cont">
					<dl class="pm-r-dl">
						<dt><span><a href="<?php echo Yii::app()->createUrl("enterpriseIndex/index");?>">首页</a></span><font> > </font><span><a href="<?php echo Yii::app()->createUrl("enterpriseIndex/member");?>">成员管理</a></span><font> > </font><i><?php $tmp_id=intval($_GET['id']); if(empty($tmp_id)) echo '成员添加'; else  echo '成员编辑';?></i></dt>
					</dl>
					<div class="clear"></div>
					<div class="m-member-compile">
						<h1 class="m-m-coe-title"><span><?php $tmp_id=intval($_GET['id']); if(empty($tmp_id)) echo '成员添加'; else  echo '成员编辑';?></span></h1>
						<dl class="m-m-coe-dl">
						<input name="id"  type="hidden" value="<?php echo intval($_GET['id'])?intval($_GET['id']):0;?>" />
						<input name="enterprise_type"  type="hidden" value="<?php echo $this->enterprise_type;?>" />
						<input name="listUrl" type="hidden" value="<?php echo $lastUrl;?>" />
							<dt <?php if(empty($tmp_id)) echo 'style="display:none;"';?>><label>奔犇号：</label><input value="<?php echo $enterpriseMember->benben_id;?>" class="chang-colo"  readonly /><p><?php if(!empty($enterpriseMember->benben_id)) echo '该用户已激活';else echo '该用户未激活';?></p></dt>
							<?php if(empty($tmp_id)) echo '<dt>';else echo '<dd>';?>
								<label>姓名：</label>
								<input name="name" value="<?php echo $enterpriseMember->name;?>"  class="chang-colo"/>
							<?php if(empty($tmp_id)) echo '</dt>';else echo '</dd>';?>
						</dl>
						<dl class="m-m-coe-dl">
							<dt><label>备注：</label><input name="remark_name" value="<?php echo $enterpriseMember->remark_name;?>" /></dt>
							<dd <?php if($this->enterprise_type==2) echo 'style="display:none;"';?>>
								<label>手机号码：</label>
								<input name="phone" value="<?php echo $enterpriseMember->phone;?>" />
							</dd>
						</dl>
						<dl class="m-m-coe-dl">
							<dt><label>其它号码：</label><input name="short_phone"  value="<?php echo $enterpriseMember->short_phone;?>" /></dt>
						</dl>
						<dl class="m-m-coe-dl">
							<dt><label>分组：</label>
							<select class="chang-selt group_id">
							<option value="0">--请选择---</option>
							<?php if(!empty($group_last)){
												foreach ($group_last as $v){
								?>
								<option value="<?php echo $v->id;?>" <?php if($enterpriseMemberManage->group_id==$v->id) echo 'selected';?>><?php echo $v->name;?></option>
							<?php }}?>
							</select></dt>
							<dd><label>查阅等级：</label>
							<select class="chang-selt  access_level">
							<option value="1"  <?php if($enterpriseMember->access_level==1) echo 'selected';?>>1</option>
							<?php if($this->enterprise_access_level_set==1){for ($i=2;$i<=10;$i++){?>
							<option value="<?php echo $i;?>"  <?php if($enterpriseMember->access_level==$i) echo 'selected';?>><?php echo $i;?></option>
							<?php }}?>
							</select>
							</dd>
						</dl>
						<dl class="m-m-coe-dl">
							<dt>
								<label>是否管理员：</label>	
								<input type="hidden" name="is_manage"  value="<?php echo $enterpriseMember->is_manage;?>" />
								<i <?php if($enterpriseMember->is_manage==1) echo 'class="chang-yes"';?> data='1'>是</i>
								<i <?php if($enterpriseMember->is_manage==0) echo 'class="chang-yes"';?> data='0'>不是</i>
							</dt>
						</dl>
						<dl class="m-m-coe-dl" style="margin-bottom: 40px;">
							<dt><label>每月可发送大喇叭：</label><input name="broadcast_per_month" value="<?php echo $enterpriseMember->broadcast_per_month;?>" placeholder="不填表示可发送为0" /></dt>
							<dd>
								<label>本月剩余大喇叭：</label>
								<input name="broadcast_available_month" value="<?php echo $enterpriseMember->broadcast_available_month;?>" placeholder="不填表示剩余为0" />
							</dd>
						</dl>
						<dl class="m-m-coe-dl power_all" style="margin-bottom: 0px;<?php if(empty($enterpriseMember->is_manage)) echo 'display:none;';?>" >
							<dt style="width: 100%;">
								<label>设置管理权限：</label>
								<select class="chang-coe-1 power" data='1'>
								<option value="0">---请选择---</option>
								<?php if(!empty($group)){
												foreach ($group as $k=>$v){
									?>
									<option value="<?php echo  $v->id;?>"><?php echo $v->name;?></option>
								<?php }}?>
								</select>
								<em class="set_power"><img src="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/images/qu-4.jpg"/></em>
							</dt>
						</dl>
						<dl class="m-m-coe-dl power_all" style="margin-bottom: 37px;<?php if(empty($enterpriseMember->is_manage)) echo 'display:none;';?>" >
							<dt style="padding-top: 5px;"><label style="text-indent: 9999px;">s</label>
								<span class="coe-old" style="line-height: 25px;<?php if(empty($manage_role)) echo 'display:none;';?>">已选择，</span>
								<ol class="has_power">
								<?php if (!empty($manage_role)){
												foreach ($manage_role as $k=>$v){
									?>
								<li><input type="hidden" name="manage_role" value="<?php echo $k;?>"><?php echo $v;?><font class="delete_power">删除</font></li>
								<?php }}?>
								</ol>
								
							</dt>
						</dl>
						<dl class="m-m-coe-dl" style="margin-bottom: 0;">
							<dt><label style="text-indent: 9999px;">s</label>
								<em class="sure"><a href="javascript:;"><img src="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/images/qe_1.jpg"/></a></em>
								<em <?php if(empty($enterpriseMember)) echo 'style="display:none;"';?> class="delete"><a href="javascript:;"><img src="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/images/qe_2.jpg"/></a></em>
								<em><a href="<?php echo $lastUrl;?>"><img src="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/images/qe_3.jpg"/></a></em>
							</dt>
						</dl>
						
					</div>

					<!--end-->
				</div>
			</div>


