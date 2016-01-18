$(function(){
//	 var offset = $('.created_time').offset(); 
//	alert(offset.top);
	$('.created_time').click(function(){
		var offset = $('.created_time').offset(); 
		$('#ui-datepicker-div').css('top', offset);
	})
	$('.created_time').datepicker({
		dateFormat : 'yy/mm/dd',
		dayNamesMin : ['日','一','二','三','四','五','六'],
		monthNames : ['一月','二月','三月','四月','五月','六月','七月','八月','九月','十月','十一月','十二月'],
		monthNamesShort : ['一月','二月','三月','四月','五月','六月','七月','八月','九月','十月','十一月','十二月'],
		altField : '#abc',
		altFormat : 'dd-mm-yy',
		showWeek : true,
		weekHeader : '周',
		firstDay : 0,
		navigationAsDateFormat : true,
		showMonthAfterYear : true,
		closeText : '关闭',
		currentText : '今天dd',
		hideIfNoPrevNext : true,
		yearRange : '1950:2020',
		showOtherMonths : true,
//		onSelect: function(dateText,inst){
//			if($(this).attr('id') == 'created_time2'){
//				$(this).val(dateText + " 23:59:59");
//			}else{
//				$(this).val(dateText + " 00:00:00");
//			}
//		}

	});
	
	
	

});

