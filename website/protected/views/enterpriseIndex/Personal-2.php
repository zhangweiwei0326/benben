		<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/independent/js/personal_public.js"></script>
		<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/independent/js/Personal-2-public.js" ></script>
		<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/independent/js/Personal-2.js"></script>	
			<!--right-->
			<div class="p-main-r fl" id="p-main-r">
				<em class="pm-r-bnn"><a href="javascript:;"><img src="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/images/rbnner.jpg"/></a></em>
				<div class="p-mian-cont">
					<dl class="pm-r-dl">
						<dt><span><a href="<?php echo Yii::app()->createUrl("enterpriseIndex/index");?>">首页</a></span><font> > </font><i>成员管理</i></dt>
					</dl>
					<div class="clear"></div>
					<div class="g-member">
						<div class="g-m-title">
							<dl><dt><label>政企人数:</label><font><?php echo $enterprise->number;?>/<?php echo $enterprise_role->member_limit;?></font></dt><dd <?php if( (!empty($this->administrator_id))||($this->apply_type==3) ) echo 'style="display:none;"';?>><a href="javascript:;"><img src="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/images/t_1.jpg" class="ascension"/></a></dd></dl>
							<ul>
<!-- 								<li class="li-edit-groud">编辑分组</li> -->
								<li><a href="<?php echo Yii::app()->createUrl("enterpriseIndex/editGroup");?>">编辑分组</a></li>
								<li class="mem-line">|</li>
								<li><a href="<?php echo Yii::app()->createUrl("enterpriseIndex/memberEdit");?>">添加成员</a></li>
								<li class="mem-line">|</li>
								<li class="import-export">导入/导出成员</li>
							</ul>
						</div>
						<!--end-->
						<div class="g-m-message" id="g-m-message">
							<div class="g-mefl fl">
								<ul id="add-del-new">
									<li class="edat-add">
										<div class="add-ed-input"><em><img src="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/images/edit_3.png" class="edi-ok"/></em><i><img src="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/images/edit_4.png" class="edi-addor"/></i></div>
									</li>
									<?php 
									$member_group=MemberGroupNew::model();
									$cri = new CDbCriteria ();
									$cri->order="t.sort desc,t.id asc";
									$cri->condition=" (t.enterprise_id='".$this->enterprise_id."')and(parent_id='0') ";
									$level1=$member_group->findAll($cri);
									if(!empty($level1)){
										foreach ($level1 as $k1=>$v1){
											$sign='';
											if(array_key_exists($v1->id,$group_num )){
												$num=$group_num[$v1->id];$sign='href';
											}else{
												$allsub=$this->allSub($v1->id).'0';
												$allsub=explode(",", $allsub);
												$num=0;
												foreach ($allsub as $v){
													$num+=$group_num[$v];
												}
											}
									?>
									<li class="g-grouping">
										<div class=" add-new-up <?php if($group_id[0]!=$v1->id) echo ' add-del-cnt';?>">
										<div class="del-messge"><img src="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/images/edit_5.png"/></div>
										<input value="<?php echo $v1->name;?>"  class="del-input"/>
										<input name="id"  type="hidden"  value="<?php echo $v1->id;?>" />
										<input name="type"  type="hidden"  value="edit" />
										<input name="pid"  type="hidden"  value="0" />
										<span class="<?php if(empty($sign)) echo 'non_terminal'; if($group_id[0]==$v1->id) echo ' g-gsp';?>"><label><a href="<?php if(!empty($sign)) echo Yii::app()->createUrl("enterpriseIndex/member",array('group_id'=>$v1->id,)); else echo 'javascript:;';?>"><?php echo $v1->name;?></a></label><font>(<?php echo $num;?>)</font></span>
										<div class="add-childern" ><img src="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/images/edit_4.png"/></div>
										</div>
							
										<?php 
										$cri->condition=" (t.enterprise_id='".$this->enterprise_id."')and(parent_id='".$v1->id."') ";
										$level2=$member_group->findAll($cri);
										if(!empty($level2)){
											foreach ($level2 as $k2=>$v2){
												$sign='';
												if(array_key_exists($v2->id,$group_num )){
													$num=$group_num[$v2->id];$sign="href";
												}else{
													$allsub=$this->allSub($v2->id).'0';
													$allsub=explode(",", $allsub);
													$num=0;
													foreach ($allsub as $v){
														$num+=$group_num[$v];
													}
												}
										?>
										<dl <?php if($v1->id != $group_id['0']) echo 'style="display: none;"';else echo 'style="display: block;"';?>>
											<dt class="<?php if(empty($sign)) echo 'non_group_terminal'; if($v2->id == $group_id['1']) echo ' g-grg-dt';?>">
												<div class="add-dt-del add-new-up">
													<div class="dt-del-messge"><img src="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/images/edit_5.png"/></div>
													<input value="<?php echo $v2->name;?>"  class="del-input"/>
													<input name="id"  type="hidden"  value="<?php echo $v2->id;?>" />
													<input name="type"  type="hidden"  value="edit" />
													<input name="pid"  type="hidden"  value="<?php echo $v2->parent_id;?>" />
												    <p class=""><label><a href="<?php if(!empty($sign)) echo Yii::app()->createUrl("enterpriseIndex/member",array('group_id'=>$v1->id.'|'.$v2->id,)); else echo 'javascript:;';?>"><?php echo $v2->name;?></a></label><font>(<?php echo $num;?>)</font></p>
												    <div class="dt-add-childern" ><img src="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/images/edit_4.png"/></div>
												</div>
											</dt>
											<?php 
											$cri->condition=" (t.enterprise_id='".$this->enterprise_id."')and(parent_id='".$v2->id."') ";
											$level3=$member_group->findAll($cri);
											if(!empty($level3)){
												foreach ($level3 as $k3=>$v3){
													$sign='';
													if(array_key_exists($v3->id,$group_num )){
														$num=$group_num[$v3->id];$sign="href";
													}else{
														$allsub=$this->allSub($v3->id).'0';
														$allsub=explode(",", $allsub);
														$num=0;
														foreach ($allsub as $v){
															$num+=$group_num[$v];
														}
													}
											?>
											<dd class="g-grg-dd1 add-new-up <?php if(empty($sign)) echo 'g-grg-dd12';?>" <?php if($v2->id != $group_id['1']) echo 'style="display: none;"';?>>
												<div class="add-dt-del-dd">
													<div class="dd-del-messge"><img src="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/images/edit_5.png"/></div>
													<input value="<?php echo $v3->name;?>"  class="del-input"/>
													<input name="id"  type="hidden"  value="<?php echo $v3->id;?>" />
													<input name="type"  type="hidden"  value="edit" />
													<input name="pid"  type="hidden"  value="<?php echo $v3->parent_id;?>" />
												     <p class="<?php if($v3->id == $group_id['2']) echo 'g-grg-ddp';?>"><label><a href="<?php if(!empty($sign)) echo Yii::app()->createUrl("enterpriseIndex/member",array('group_id'=>$v1->id.'|'.$v2->id.'|'.$v3->id,)); else echo 'javascript:;';?>"><?php echo $v3->name;?></a></label><font>(<?php echo $num;?>)</font></p>
												     <div class="dd-add-childern" ><img src="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/images/edit_7.png"/></div>
												</div>
											</dd>
											<?php 
											$cri->condition=" (t.enterprise_id='".$this->enterprise_id."')and(parent_id='".$v3->id."') ";
											$level4=$member_group->findAll($cri);
											if(!empty($level4)){
												foreach ($level4 as $k4=>$v4){
													$sign='';
													if(array_key_exists($v4->id,$group_num )){
														$num=$group_num[$v4->id];$sign="href";
													}else{
														$allsub=$this->allSub($v4->id).'0';
														$allsub=explode(",", $allsub);
														$num=0;
														foreach ($allsub as $v){
															$num+=$group_num[$v];
														}
													}
											?>
											<dd class="g-grg-dd2" <?php if($v3->id != $group_id['2']) echo 'style="display: none;"';?>>
												<div class="add-dt-del-dd">
													<div class="dd2-del-messge"><img src="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/images/edit_5.png"/></div>
													<input value="<?php echo $v4->name;?>"  class="del-input"/>
													<input name="id"  type="hidden"  value="<?php echo $v4->id;?>" />
													<input name="type"  type="hidden"  value="edit" />
													<input name="pid"  type="hidden"  value="<?php echo $v4->parent_id;?>" />
												    <p><a href="<?php if(!empty($sign)) echo Yii::app()->createUrl("enterpriseIndex/member",array('group_id'=>$v1->id.'|'.$v2->id.'|'.$v3->id.'|'.$v4->id,)); else echo 'javascript:;';?>"><?php echo $v4->name;?></a><font>(<?php echo $num;?>)</font></p>
												</div>
											</dd>
											<?php }}}}?>
										</dl>
										<?php }}?>
									</li>
									<?php }}?>
									<li class="g-grouping ">
										<div class="add-del-cnt">
<!-- 										<input value="未分组"  class="del-input"  readonly/> -->
										<a href="<?php if(!empty($final_group_id)) echo Yii::app()->createUrl("enterpriseIndex/member",array('ungroup'=>'ungroup',)); else echo 'javascript:;';?>"><span>未分组<font>(<?php echo $ungroup_num;?>)</font></span></a>
										</div>
									</li>
								</ul>
							</div>
							<!--end fl-->
							<div class="g-mefr fr" id="g-mefr">
								<div class="g-mefr-cont">
									<form action="<?php echo Yii::app()->createUrl("enterpriseIndex/member");?>" method="get">
										<div class="g-mefr-c-top">
											<div class="g-mefr-ofl fl">
												<dl>
													<dt>
														<p><label>奔犇号：</label><input name="member_id"  value="<?php echo $_GET['member_id'];?>" placeholder="输入奔犇号" /></p>
														<p><label>姓名：</label><input name="name" value="<?php echo $_GET['name'];?>" placeholder="请输入姓名" /></p>
														<p><label>备注：</label><input name="remark_name" value="<?php echo $_GET['remark_name'];?>" placeholder="请输入备注" /></p>
														<p style="margin: 0;"><label>号码：</label><input name="phone" value="<?php echo $_GET['phone'];?>" placeholder="请输入号码" /></p>
													</dt>
													<dd>
														<p>温馨提示：显示奔犇号的用户已经激活</p>
														<i><label>查阅等级：</label> <select name="access_level">
																<option value="0">---请选择---</option>
																<option value="1"  <?php if(intval($_GET['access_level'])==1) echo 'selected';?>>1</option>
																<?php if($this->enterprise_access_level_set==1){for ($i=2;$i<=10;$i++){?>
																<option value="<?php echo $i;?>"  <?php if(intval($_GET['access_level'])==$i) echo 'selected';?>><?php echo $i;?></option>
																<?php }}?>
														</select></i>
													</dd>
												</dl>
											</div>
											<input name="page" type="hidden" value="<?php echo intval($_GET['page']);?>" />
											<input name="group_id" type="hidden" value="<?php echo $_GET['group_id'];?>" />
											<input name="ungroup" type="hidden" value="<?php echo $_GET['ungroup'];?>" />
											<!--左边结束-->
											<div class="g-mefr-ofr fr">
												<dl>
													<dt>
														<a class="submit" href="javascript:;"><img src="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/images/t-5.jpg" /></a>
														<input name="submit_search" style="display: none;" type="submit" />
													</dt>
													<dd>
														<span class="clear_conditions">清空选项</span>
													</dd>
												</dl>
											</div>
											<!--右边结束-->
										</div>
									</form>
									<!--上部结束-->
									<div class="g-mefr-c-bo">
										<ul class="g-mefr-b-ulo">
											<li style="width: 10%;">奔犇号</li>
											<li style="width: <?php echo $this->enterprise_type==2 ? "20%":"15%";?>;">姓名</li>
											<li style="width: <?php echo $this->enterprise_type==2 ? "20%":"15%";?>;">备注</li>
											<li style="width: 15%;<?php if($this->enterprise_type==2) echo 'display:none;';?>">手机号码</li>
											<li style="width: <?php echo $this->enterprise_type==2 ? "20%":"15%";?>;">其它号码</li>
											<li style="width: 12%;">查阅等级</li>
											<li style="width: 12%; margin-right: 0;">操作</li>
										</ul>
										<div class="g-mefr-bo-cont">
											<?php
											if (! empty ( $items )) {
												foreach ( $items as $k => $v ) {
													?>
											<ul>
												<li style="width: 10%; height: 58px;"><?php echo $v->benben_id;?></li>
												<li style="width: <?php echo $this->enterprise_type==2 ? "20%":"15%";?>; height: 58px;"><?php echo $v->name;?></li>
												<li style="width:<?php echo $this->enterprise_type==2 ? "20%":"15%";?>; height: 58px;"><?php echo $v->remark_name;?></li>
												<li style="width: 15%; height: 58px;<?php if($this->enterprise_type==2) echo 'display:none;';?>"><?php echo $v->phone;?></li>
												<li style="width: <?php echo $this->enterprise_type==2 ? "20%":"15%";?>; height: 58px;"><?php echo $v->short_phone;?></li>
												<li style="width: 12%; height: 58px;"><?php echo $v->access_level;?></li>
												<li style="width: 12%; height: 58px; margin-right: 0;"><a
													href="<?php echo Yii::app()->createUrl("enterpriseIndex/memberEdit",array('id'=>$v->id,));?>">编辑</a></li>
											</ul>
											<?php }}?>
										</div>
										<!--end-->
										<div class="g-mefr-bfy">
											<?php
											$page = intval ( $_GET ['page'] ) ? intval ( $_GET ['page'] ) : 1;
											$url = explode ( "?", Yii::app ()->request->getUrl () );
											$link = Yii::app ()->request->hostInfo . $url [0] . "?";
											echo '<ul>' . $this->textPage ( $pages->pageCount, $page, $link ) . '</ul>';
											$path = Yii::app()->basePath.'/views/layouts/searchPages.php';
											require_once ($path);
											?>
										</div>
										<!--分页end-->
									</div>
								</div>
							</div>
						</div>
						<!--end-->
					</div>
					<!--end-->
				</div>
			</div>
		</div>
        <!--导入导出弹窗-->
        <div class="export-into" style="display: none;"></div>
        <div class="export-into-cent" style="display: none;">
        	<div class="export-into-main">
        		<dl class="export-into-main-dl">
        			<dt>导入/导出</dt>
        			<dd></dd>
        		</dl>
        		<dl class="export-into-main-dl2">
        		<form action="<?php echo Yii::app()->createUrl("enterpriseIndex/member");?>" method="post"  enctype='multipart/form-data'>
        			<input style="float: left;left: -2px;position: relative;width: 96px;" name="file" type="file"  placeholder="批量导入成员信息"/><input name="member_import"  type="submit"   style="display: none;"/>
        		</form>
        			<dt><span></span><a class="import_member" href="javascript:;"><img src="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/images/com-la_4.jpg"/></a></dt>
        			<dd>下载已有成员信息<a class="export_member" href="<?php echo Yii::app()->createUrl("enterpriseIndex/export",array('condition'=>$condition,));?>"><img src="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/images/com-la_5.jpg"/></a></dd>
        		</dl>
        	</div>
        </div>
        <!--提升上线-->
        <form method="post" id="frm" action="/index.php/EnterpriseIndex/Pay">
		<input class="names_money" name="names_money" type="hidden"/>
		<input class="duration_aa" name="duration_aa" type="hidden"/>
		<input class="money" name="money" type="hidden"/>
		</form>
		<?php echo $enterservice->view?>
        <!--<div class="export-add" style="display: none;" ></div>
        <div class="export-into-add" style="display: none;">
        	<div class="export-into-add-mian">
        		<dl class="export-into-add-dl">
        			<dt>提升上限</dt>
        			<dd></dd>
        		</dl>
        		<ul class="export-add-ul1">
        			<li class="ex-add-oneli">选择套餐：</li>
        			<li class="ex-add-li exone">2000人</li>
        			<li class="ex-add-li exone ex-add-clk">5000人</li>
        			<li class="ex-add-li exone">30000人</li>
        		</ul>
        		<ul class="export-add-ul1 ">
        			<li class="ex-add-oneli">开通时长：</li>
        			<li class="ex-add-li extwo" style="padding: 0 20px;">1年</li>
        			<li class="ex-add-li extwo ex-add-clk" style="padding: 0 20px;">2年</li>
        			<li class="ex-add-li extwo" style="padding: 0 20px;">3年</li>
        			<li class="ex-add-li extwo" style="padding: 0 20px;">4年</li>
        			<li class="ex-add-li extwo" style="padding: 0 20px; margin-right: 0;">5年</li>
        			
        		</ul>
        		<ul class="export-add-ul1 ">
        			<li class="ex-add-oneli">开通时长：</li>
        			<li class="ex-add-oneli">2016-3-25</li>
        			
        		</ul>
        		<ul class="export-add-ul1 ">
        			<li class="ex-add-oneli">套餐内容：</li>
        			<li class="ex-add-li " style="background: #eaeaea;padding: 0 5px;">5000人容量</li>
        			<li class="ex-add-li " style="background: #eaeaea;padding: 0 5px;">政企电脑版客户端</li>
        			<li class="ex-add-li " style="background: #eaeaea;padding: 0 5px;">30个大喇叭</li>
        		</ul>
        		<dl class="export-into-add-dl2">
        		     <dt><span>应付金额：</span><font>200元</font></dt>
        		     <dd><a href="javascript:;"><img src="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/images/com-la_7.jpg"/></a></dd>
        		</dl>
        	</div>
        </div>-->
<script type="text/javascript" src="/themes/js/shopdate.js" ></script>
<script type="text/javascript">
$(function(){
	$('#sure').on('click',function(){
		var names_money=$(".names_money").val();
		var duration_aa=$(".duration_aa").val();
		var money=$('.money').val();
		$.post('/index.php/enterpriseIndex/getInfo',{names_money:names_money,duration_aa:duration_aa,money:money},function(data){
			if(data==1)
			{
				alert('金额或参数不正确！');
				return false;

			}
			else
			{
				$('#frm').submit();
			}

		},'json');

		
	});
	<?php 
		if(!empty($msg)){
			echo $msg;
		}
	?>
	$(".exone").click(function(){
		$(".names_money").val($(this).html());
		$(".money").val($(this).attr("money"));
		$(".num0").html($(this).html());
		$(".num1").html($(this).attr("big_horn"));
		var abc =$(this).attr("showtime");
		if($(".shop-time").attr("data") !=0){
			$(".shop-time").attr("data",abc);
			$(".shop-time").html(new Date(parseInt(abc)*1000).format('yyyy-MM-dd'));
		}
		if($(".duration_aa").val()){
			calculate_time($(".duration_aa").val());
			calculate_jin($(".duration_aa").val(),$(".money").val());

		}

	});
	$(".extwo").click(function(){
		$(".duration_aa").val($(this).attr("data"));
		calculate_time($(this).attr("data"));
		calculate_jin($(".duration_aa").val(),$(".money").val())
	});
	$(".ex-add-clk").click();
	function calculate_time(date){
		date = parseInt(date);
		var date1 = $(".shop-time").attr("data");
		var myDate=new Date(parseInt(date1)*1000);
		var interval = "m";
		if(date%12 == 0){
			var interval = "y";
			date = date/12;
		}
		var duration = DateAdd(interval,date,myDate);//alert(duration.format('yyyy-MM-dd'));
		$(".shop-time").html(duration.format('yyyy-MM-dd'));

	}
	function calculate_jin(date,va){
		date = parseInt(date);
		va = parseInt(va);
		date = date/12;
		var jin = date*va;
		var names_money=$(".names_money").val();
		var duration_aa=$(".duration_aa").val();
		var money=$('.money').val();
		$.post('/index.php/enterpriseIndex/getInfo',{names_money:names_money,duration_aa:duration_aa,money:money},function(data){
			if(data.vip_price>0&&data.price>0)
			{
				jin=jin+'-'+data.vip_price+'='+data.price;
				$(".all_money").html(jin+"元");
			}
			else
			{
				$(".all_money").html(jin+"元");

			}

		},'json');

	}
});
</script>




























