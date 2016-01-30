		<link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/independent/css/confirm.css" />
		<link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/independent/css/apply.css" />
		 <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/independent/js/register.js" ></script>
		<meta charset="utf-8" />
		<title>奔犇政企通讯录管理后台</title>
		<meta name="description" content="" />
        <meta name="keywords" content="" />
		<script>
			function setTab(name,cursel,n){
				  for(i=1;i<=n;i++){
				  var menu=document.getElementById(name+i);
				  var con=document.getElementById("con"+name+i);
				  menu.className=i==cursel?"hover":"";
				  con.style.display=i==cursel?"block":"none";
				} 
			  }
			$(function(){
				$('.check-comdd li i').click(function(){
					$('.check-comdd li i').removeClass('check-kk');
					$(this).addClass('check-kk');
					var id=$(this).attr("id");id=id.replace(/[^1-9]/ig,"");	
					$("input[name='enterprise_type']").val(id);
				});
			})
</script>
		<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/themes/js/LocalResizeIMG.js"></script>
	</head>
	<body style="background: #f6f6f6;">
		<div class="ay-top">
			<dl>
				<dt></dt>
				<dd><a href="<?php echo Yii::app()->createUrl("enterpriseSite/login");?>">已有账号，点此登录 >></a></dd>
			</dl>
		</div>
        <div class="ay-cont">
        	<ul class="ay-title-ul">
        		<li class="ay-one-li">申请类型</li>
        		<li onclick="setTab('exzj0',1,3)" id="exzj01" class="hover"><p>个人</p></li>
        		<li onclick="setTab('exzj0',2,3)" id="exzj02"><p>企业/组织</p></li>
        		<li onclick="setTab('exzj0',3,3)" id="exzj03"><p>学校</p></li>
        	</ul>
        	<div class="ay-main-c">
        		<div class="ay-ul-cont-o">
        			<div id="conexzj01">
        				<h1>申请者信息登记</h1>
        				<dl class="ay-inpt1">
        					<dt><label>*</label><input name="person_name" placeholder="姓名"/></dt>
        				</dl>
        				<dl class="ay-inpt1" style="margin-top: 20px; margin-bottom: 54px;">
        					<dt><label>*</label><input name="person_phone" placeholder="手机号码"/></dt>
        					<dd><p>客服将通过手机号码通知您审核结果，请务必确保真实填写</p></dd>
        				</dl>
        				<dl class="ay-inpt1">
        					<dt><label>*</label><input name="person_identity_num" placeholder="身份证号码"/></dt>
        				</dl>
        				<p class="sfz-title"><label>*</label><span>请上传身份证正反面照片</span></p>
        				<ul class="upload-sfz">
        					<li class="up-zf  resource_img_upload" style="margin-right: 35px; margin-left: 0;">
        					<img style="width:100%;height:100%" alt="身份证正面照片" id="person_identity_attachment_img" >
        					<input type="hidden" name="person_identity_attachment"   id="person_identity_attachment_input">
        					</li>
        					<li class="up-zf   resource_img_upload">
        					<img style="width:100%;height:100%" alt="身份证反面照片" id="person_identity_attachment_more_img">
        					<input type="hidden" name="person_identity_attachment_more"   id="person_identity_attachment_more_input">
        					</li>
        					<li class="up-ts">支持jpg格式，大小不超过2M</li>
        				</ul>
        			</div>
        			<div id="conexzj02" style="display: none;">
        				<h1>申请者信息登记</h1>
        				<dl class="ay-inpt1">
        					<dt><label>*</label><input name="enterprise_name" placeholder="企业/组织全称"/></dt>
        					<dd><p>需与营业执照或组织机构上的信息完全一致，默认为政企通讯录名称</p></dd>
        				</dl>
        				<dl class="ay-inpt1" style="margin-top: 20px; margin-bottom: 54px;">
        					<dt><label>*</label><input name="enterprise_phone" placeholder="手机号码"/></dt>
        					<dd><p>客服将通过手机号码通知您审核结果，请务必确保真实填写</p></dd>
        				</dl>
        				<dl class="ay-inpt1">
        					<dt><label>*</label><input name="enterprise_identity_num" placeholder="营业执照注册号/组织机构代码"/></dt>
        				</dl>
        				<p class="sfz-title"><label>*</label><span>请上传营业执照/组织机构代码扫描件：</span></p>
        				<ul class="upload-sfz">
        					<li class="up-zf  resource_img_upload" style=" margin-left: 0;">
        					<img style="width:100%;height:100%" alt="点此上传图片" id="enterprise_identity_attachment_img" >
        					<input type="hidden" name="enterprise_identity_attachment"   id="enterprise_identity_attachment_input">
        					</li>
        					<li class="up-ts">支持jpg格式，大小不超过2M</li>
        				</ul>
        			</div>
        			<div id="conexzj03" style="display: none;">
        				<h1>申请者信息登记</h1>
        				<dl class="ay-inpt1">
        					<dt><label>*</label><input name="school_name" placeholder="学校全称"/></dt>
        					<dd><p>需与办学许可证上信息完全一致，默认为政企通讯录名称</p></dd>
        				</dl>
        				<dl class="ay-inpt1" style="margin-top: 20px; margin-bottom: 54px;">
        					<dt><label>*</label><input name="school_phone" placeholder="手机号码"/></dt>
        					<dd><p>客服将通过手机号码通知您审核结果，请务必确保真实填写</p></dd>
        				</dl>
        				<dl class="ay-inpt1">
        					<dt><label>*</label><input name="school_identity_num" placeholder="办学许可证代码"/></dt>
        				</dl>
        				<p class="sfz-title"><label>*</label><span>请上传办学许可证扫描件：</span></p>
        				<ul class="upload-sfz">
        					<li class="up-zf  resource_img_upload" style=" margin-left: 0;">
        					<img style="width:100%;height:100%" alt="点此上传图片" id="school_identity_attachment_img" >
        					<input type="hidden" name="school_identity_attachment"   id="school_identity_attachment_input">
        					</li>
        					<li class="up-ts">支持jpg格式，大小不超过2M</li>
        				</ul>
        			</div>
        		</div>
        		<div class="ay-unified">
        			<h1>请填写申请的政企通讯录信息</h1>
        			<dl class="ay-inpt1">
        					<dt><label>*</label><input name="directory_name" placeholder="政企通讯录名称"/></dt>
        					<dd><p>申请通过之后，政企通讯录的名称不可以更改</p></dd>
        			</dl>
        			<dl class="ay-inpt1" style="margin-top: 25px; margin-bottom: 15px;">
        					<dt class="check_company"><label>*</label><span>选择政企通讯录类型：</span></dt>
        					<dd class="check-comdd">
        						<ol>
        							<li><i id="enterprise_type1">企业政企</i><font style="margin-left: 15px;">（每个成员可以添加1-2个号码，其中一个必须是手机长号）</font></li>
        							<input name="enterprise_type"  type="hidden"  value="0" />
        							<li><i id="enterprise_type2">虚拟网政企</i><font>（每个成员只可以添加一个短号）</font></li>
        						</ol>
                            </dd>
        			</dl>
        			<dl class="ay-inpt1">
        					<dt><label>*</label><input name="login_name" placeholder="登录名"/></dt>
        					<dd><p>不支持纯数字</p></dd>
        			</dl>
        			<dl class="ay-inpt1" style="margin-top: 20px; margin-bottom: 12px;">
        					<dt><label>*</label><input name="login_password" type="password" placeholder="设置登录密码，长度在6~20位"/></dt>
        			</dl>
        			<dl class="ay-inpt1">
        					<dt><label>*</label><input name="login_password_confirm" type="password" placeholder="确认密码"/></dt>
        			</dl>
        			<dl class="ay-inpt1" style="margin-top: 30px; margin-bottom: 20px;">
        					<dt><label>*</label><input name="email" placeholder="密保邮箱"/></dt>
        					<dd><p>邮箱可用来找回密码</p></dd>
        			</dl>
        			<dl class="ay-inpt1">
        					<dt class="ay-code"><label>*</label>
        					<input name="verify" placeholder="输入验证码"/>
        						<b style="display: none;"><img src="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/images/s-5.jpg"/></b>
        			<span class="code_2">
					 <?php
					       		$this->widget('CCaptcha',
											   array('showRefreshButton'=>false,
													 'clickableImage'=>true,
                                                     'imageOptions'=>array('alt'=>'点击刷新','title'=>'点击刷新','style'=>'cursor:pointer'))); 
                            ?>
					</span>
        					</dt>
        			</dl>
        			<ul class="confirm-zc">
        				<li class="czc-one">已阅读并同意《政企通讯录会员服务协议》<input type="hidden" name="read_agree"  value="3" /></li>
        				<li id="register" class="register"><a href="javascript:;"><img src="<?php echo Yii::app()->request->baseUrl;?>/themes/enterprise/images/s-6.png"/></a></li>
        				<li id="register_message" class="register_message  register" >正在注册中...</li>
        			</ul>
        		</div>
        		<!--end-->
        	</div>
        	<!--main end-->
        </div>
        <input type="file"  id="upload_input" style="visibility:hidden;"/>
	</body>
</html>
<script type="text/javascript">
$(function(){
	/*上传图片*/
	$(document).on('click', '.resource_img_upload', function() {
			inClass = $(this).children('input').attr('id');
			imClass = $(this).children('img').attr('id');
	   $("#upload_input").trigger('click');
	});
	var value;
	$('#upload_input').localResizeIMG({
	    width: 1000,
	    quality: 0.5,
	    before: function() {
	    	var image_file=$("#upload_input").val();
	    	image_file=image_file.split('.');
	    	var image_type=image_file.pop();
	    	var type_arr=['jpg','png','bmp','jpeg'];
	    	value=$.inArray(image_type,type_arr);
	    	if(value == -1){
		    	layer.msg('请上传正确的图片格式！支持jpg、png、bmp、jpeg',{icon:2,time:10000,btn:['知道了']});
	    		return false;
		    }
	    	$("#"+imClass).attr('src', '/themes/enterprise/independent/images/loading2.gif');
	    },
	    success: function(result) {
	        var img = new Image();
	        img.src = result.base64;
	        var name = result.name;
//	         console.log(result);
	        $('#formFile').html(result.base64.substr(22));
			if(value != -1){
				//发送到服务端
		        $.post('<?php echo Yii::app()->createUrl("/enterpriseSite/upload");?>', {'name': name, 'head': 0, formFile: result.base64.substr(22)}, function(data) {
		            var loadSrc = '<?php echo Yii::app()->baseUrl?>'+data;
		        	$("#"+imClass).attr("src", loadSrc);
		        	$("#"+inClass).val(data);
		        });
			}
	    }
	});
});


</script>





























