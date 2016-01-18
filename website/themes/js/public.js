function goBack(){
    if ((navigator.userAgent.indexOf('MSIE') >= 0) && (navigator.userAgent.indexOf('Opera') < 0)){ // IE
        if(history.length > 0){
            window.history.go( -1 );
        }else{
            window.opener=null;window.close();
        }
    }else{ //非IE浏览器
        if (navigator.userAgent.indexOf('Firefox') >= 0 ||
            navigator.userAgent.indexOf('Opera') >= 0 ||
            navigator.userAgent.indexOf('Safari') >= 0 ||
            navigator.userAgent.indexOf('Chrome') >= 0 ||
            navigator.userAgent.indexOf('WebKit') >= 0){

            if(window.history.length > 1){
                window.history.go( -1 );
            }else{
                window.opener=null;window.close();
            }
        }else{ //未知的浏览器
            window.history.go( -1 );
        }
    }
}

$(document).ready(function() {
	//左侧菜单超过高度可以自由滑动，设置其高度
	if ($('.main_content_left').length > 0) {
		$('.main_content_left').css('height', $(window).height());
	};
	//返回上级页面
	$('.backurl').click(function(){
		window.history.go($(this).attr('goback'));
	});
	
	$('.search_pages').click(function(){
		
		var page = $('.input_page').val();
		var baseUrl = $(this).attr('baseUrl');
		
		if(baseUrl.indexOf('?') >= 0){
			location.href=baseUrl+"&page="+page;
		}else{
			location.href=baseUrl+"?page="+page;
		}
		
	});

	$('.main_left_navi_home').click(function(){
		if ($(this).hasClass('main_left_navi_home_selected')) {
			$('.main_left_navi_home_selected').removeClass('main_left_navi_home_selected');
			$('.main_left_navi_subnav').hide();
			return;
		}
		var data = $(this).attr('data');
		$('.main_left_navi_home_selected').removeClass('main_left_navi_home_selected');
		$(this).addClass('main_left_navi_home_selected');
		$('.main_left_navi_subnav').hide();
		$('.main_left_navi_subnav[data='+data+']').slideDown();

	});

	$('#search_more').click(function(){
		var h = $(this).html();
		if (h == '更多') {
			$(this).html('收起');
			$('.member_ul').fadeIn();
		}else{
			$(this).html('更多');
			$('.member_ul').fadeOut();
		}
	});
	
	$("#search_clear").click(function(){
		$("select").val(-1);
		$("input[type='text']").val("");
	});
	
//	//会员管理页码
//	$('.red_page').click(function(){
//		
//		var page = $('.input_page').val();
//		var baseUrl = $(this).attr('baseUrl')
//		var benben_id = $('#benben_id').val();
//		var phone = $('#phone').val();
//		var name = $('#name').val();
//		var nick_name = $('#nick_name').val();
//		var sex = $('#sex').val();
//		var age1 = $('#age1').val();
//		var age2 = $('#age2').val();
//		var created_time1 = $('#created_time1').val();
//		var created_time2 = $('#created_time1').val();
//		var dj= $('#dj').val();
//		var province = $('#province').val();
//		var city = $('#city').val();
//		var area = $('#area').val();
//	
//		location.href=baseUrl+"/admin.php/member/index?page="+page+
//																								"&benben_id="+benben_id+
//																								"&phone="+phone+
//																								"&name="+name+
//																								"&nick_name="+nick_name+
//																								"&sex="+sex+
//																								"&age1="+age1+
//																								"&age2="+age2+
//																								"&created_time1="+created_time1+
//																								"&created_time2="+created_time2+
//																								"&dj="+dj+
//																								"&province="+province+
//																								"&city="+city+
//																								"&area="+area;
//																			
//	});
	
//	$('.red_page2').click(function(){
//
//		var page = $('.input_page').val();
//		var baseUrl = $(this).attr('baseUrl')
//		var phone = $('#phone').val();
//		var name = $('#name').val();
//		var short_phone = $('#short_phone').val();
//		var status = $('#status').val();
//		var created_time1 = $('#created_time1').val();
//		var created_time2 = $('#created_time2').val();
//		location.href = baseUrl+"/admin.php/bxapply/index?page="+page+
//																									"&phone="+phone+
//																									"&name="+name+
//																									"&short_phone="+short_phone+
//																									"&status="+status+
//																									"&created_time1="+created_time1+
//																									"&created_time2="+created_time2;
//	});
	
	
//	$('.enter_page').click(function(){
//		var page = $('.input_page').val();
//		var baseUrl = $(this).attr('baseUrl')
//		var name = $('#name').val();
//		var type = $('#type').val();
//		location.href = baseUrl+"/admin.php/enterprise/index?page="+page+
//																									   "&name="+name+
//																									   "&type="+type;
//	});
	
//	$('.enterm_page').click(function(){
//		var page = $('.input_page').val();
//		var baseUrl = $(this).attr('baseUrl')
//		location.href = baseUrl+"/admin.php/enterpriseMember/index?page="+page;
//	})
//	$('.main_footer_page_search_font').click(function() {
//
//		var current = parseInt($('#pages').val());
//		var total = parseInt($("#display_total").val());
//		var baseUrl = $("#page_baseurl").val();
//		if (current > 0 && current <= total) {
//			window.location.href = baseUrl + "?page=" + current;
//		} else {
//			alert("输入页码错误");
//		}
//	});
	//enter
	$('#pages').bind('keydown', function (e) {
		var key = e.which;
		if (key == 13) {
		e.preventDefault();
		$('.main_footer_page_search_font').trigger('click');
		}
		});

	$(".action_del").click(function() {
		if ($('#myModal').length <= 0) {
			$('body').append('<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"> <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button><h4 class="modal-title" id="myModalLabel">确定操作</h4></div><div class="modal-body">你确定要执行删除操作？</div><div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">取消</button><button type="button" class="btn btn-primary"  id="confirm_delete">删除</button></div></div></div></div>');
			$('#confirm_delete').on("click", function(){
				var id = $('#confirm_delete').attr('data');
				var parent_id = $('.action_del').attr('parent_id');
			
				$('#confirm_delete').attr('data', 0);
				if (id > 0) {
					var baseUrl = $("#del_baseurl").val();
					var token = baseUrl.indexOf('?')==-1 ? "?" : '&';
				
					if(parent_id > 0){
						window.location.href = baseUrl + token + "id=" + id + "&parent_id=" + id;
					}else{
						window.location.href = baseUrl + token + "id=" + id;
					}
					
				};
				
				$('#myModal').modal('hide');
			});
		};
		if ($('#confirm_delete').length > 0) {
			$('#confirm_delete').attr('data', $(this).attr('data'));
		}
		$('#myModal').modal('show');
	});


	//省市区数据调整
	$('#province').change(function(){
		var val = $(this).val();
		$.post('/admin.php/friendLeague/getarea/bid/'+val, function(e){
			if (e) {
				$('#city').html(e);
				$('#area').html('<option value="-1">请选择区</option>');
				$('#street').html('<option value="-1">请选择</option>');
			}	
		})
	});
	$('#city').change(function(){
		var val = $(this).val();
		$.post('/admin.php/friendLeague/getarea/bid/'+val, function(e){
			if (e) {
				$('#area').html(e);
				$('#street').html('<option value="-1">请选择</option>');
			}	
		})
	});
	$('#area').change(function(){
		var val = $(this).val();
		$.post('/admin.php/friendLeague/getarea/bid/'+val, function(e){
			if (e) {
				$('#street').html(e);
			}	
		})
	});
	

	
});
