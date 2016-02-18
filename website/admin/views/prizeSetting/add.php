<?php
	Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/themes/css/content.css");
?>

<div class="main_right_content">
	<ol class="breadcrumb main_right_content_breadcrumb">
		<li><a href="<?php echo Yii::app()->createUrl('prizeSetting/index')?>">奖品设置</a></li>
		<li><a href="#">添加奖品</a></li>
	</ol>
	<div class="main_right_content_content">
			<div style="margin-left: 200px;font-size: 15px;">

				<div class="form-group">
                        <div class="form-group col-md-12" style="padding:0 0 0 5px;float:left;">
                            <label  for="sex" style="float:left;margin-top:7px;">奖项:</label>		            
						    	<!--dom结构部分-->
								<div id="uploader-demo">
								    <!--用来存放item-->
								    <div id="fileList" class="uploader-list"></div>
								    <div id="filePicker">选择图片</div>
								    <div id="rt_rt_1abn1q5n5sje1fks1t7gmfuok74" style="position: absolute; top: 0px; left: 0px; width: 94px; height: 43px; overflow: hidden; bottom: auto; right: auto;"><input type="file" name="file" class="webuploader-element-invisible" multiple="multiple" accept="image/*"><label style="opacity: 0; width: 100%; height: 100%; display: block; cursor: pointer; background: rgb(255, 255, 255);"></label></div>
								</div>
						</div>  
							<p id="picture">支持png,jpg格式,宽高为:194*122</p>	
				</div>
				<div class="form-group">
					    <div class="form-group" style="padding:0 0 0 5px;">
                        	<label  for="benben_id" style="float:left;margin-top:7px;">奖品名称:</label> 
							<input id="name" type="text" placeholder="奖品的名字..." style="margin-top:7px;margin-left: 50px;border:1px solid;">  
                    	</div>				
                 </div>		
                 <div class="form-group">
					    <div class="form-group" style="padding:0 0 0 5px;">
                        	<label  for="benben_id" style="float:left;margin-top:7px;">中奖次数:</label>
                        	<input id="frequency" type="text" placeholder="设置中奖的次数" style="margin-top:7px;margin-left: 50px;border:1px solid;"> 

                    	</div>				
                 </div>
			</div>	
			<div class="form-group form-group-center">
				<a class="btn btn-success btn-lg enter-save" name="<?php echo $item[0] ->auction_id ?>">保存</a>
				<a class="btn btn-default btn-lg backurl" type="button" goback="<?php echo $_GET['back'] ? $_GET['back'] : -1;?>">　取消　</a>
			</div>
	</div>
</div>
<script type="text/javascript">  

	// 初始化Web Uploader
	var uploader = WebUploader.create({

	    // 选完文件后，是否自动上传。
	    auto: true,

	    // swf文件路径
	    swf: '/wtz/benben/website/themes'+'/js/Uploader.swf',

	    // 文件接收服务端。
	    server: "http://localhost:8080/wtz/benben/website/admin.php/prizeSetting/upload",

	    // 选择文件的按钮。可选。
	    // 内部根据当前运行是创建，可能是input元素，也可能是flash.
	    pick: '#filePicker',

	    // 只允许选择图片文件。
	    accept: {
	        title: 'Images',
	        extensions: 'gif,jpg,jpeg,bmp,png',
	        mimeTypes: 'image/*'
	    }
	});
	// 当有文件添加进来的时候
	uploader.on( 'fileQueued', function( file ) {
	    var $li = $(
	            '<div id="' + file.id + '" class="file-item thumbnail">' +
	                '<img>' +
	                '<div class="info">' + file.name + '</div>' +
	            '</div>'
	            ),
	        $img = $li.find('img');


	    // $list为容器jQuery实例
	   	//$list.append( $li );
	   	$("#fileList").append( $li );

	    // 创建缩略图
	    // 如果为非图片文件，可以不用调用此方法。
	    // thumbnailWidth x thumbnailHeight 为 100 x 100
	    uploader.makeThumb( file, function( error, src ) {
	        if ( error ) {
	            $img.replaceWith('<span>不能预览</span>');
	            return;
	        }

	        $img.attr( 'src', src );
	    }, 246, 155 );
	});
	// 文件上传过程中创建进度条实时显示。
	uploader.on( 'uploadProgress', function( file, percentage ) {
	    var $li = $( '#'+file.id ),
	        $percent = $li.find('.progress span');

	    // 避免重复创建
	    if ( !$percent.length ) {
	        $percent = $('<p class="progress"><span></span></p>')
	                .appendTo( $li )
	                .find('span');
	    }

	    $percent.css( 'width', percentage * 100 + '%' );
	});
	
	// 文件上传成功，给item添加成功class, 用样式标记上传成功。
	uploader.on( 'uploadSuccess', function( file,data) {
	    $( '#'+file.id ).addClass('upload-state-done');
	    $("#picture").attr("name",data.path);
	});

	// 文件上传失败，显示上传出错。
	uploader.on( 'uploadError', function( file ) {
	    var $li = $( '#'+file.id ),
	        $error = $li.find('div.error');

	    // 避免重复创建
	    if ( !$error.length ) {
	        $error = $('<div class="error"></div>').appendTo( $li );
	    }

	    $error.text('上传失败');
	});

	// 完成上传完了，成功或者失败，先删除进度条。
	uploader.on( 'uploadComplete', function( file ) {
	    $( '#'+file.id ).find('.progress').remove();
	});

	//点击确定提交
    $(".enter-save").on("click",function(){
    	//获取数据
	    var _this=this;
	    // var img_url =$("#phphotoCover").val();
	    var name =$("#name").val();
	    var frequency =parseInt($("#frequency").val());//int型
	    // alert(img_url+" "+frequency+" "+name+" "+img_name);
	    var img_url =$("#picture").attr("name");
	    //保存数据，上传图片到服务器的文件夹
		if(img_url &&name!="" && !isNaN(frequency)){
		    	url="<?php echo Yii::app()->createUrl("prizeSetting/save")?>";//json输出
	    	$.post(url, {"prize_name":name,
	    					"prize":img_url,
	    					"frequency":frequency
	    	}, function(data){
	            if (data.status==1) {
	                alert("操作成功！");
	            }else{
	                alert("网络错误！");
	            };
	        },'json');
	    }else{
	    	alert("请上传奖品图片或填写奖品名或输入奖品次数!")
	    }
    	
    });
</script>

