<script type="text/javascript">
$(function(){
	var city = eval('(<?php echo $res; ?>)');
	var area = eval('(<?php echo $res2; ?>)');
	var street = eval('(<?php echo $res3; ?>)');
	var city_select = "";
	var area_select = "";
	var street_select = "";
	<?php 
		if(isset($_GET['city'])){	?>
			var city_select = <?php echo $_GET['city']  ?>;

	<?php }?>

	<?php 
			if(isset($_GET['area'])){	?>
				var area_select = <?php echo $_GET['area']  ?>;

	<?php }?>

	<?php 
			if(isset($_GET['street'])){	?>
				var street_select = <?php echo $_GET['street']  ?>;

	<?php }?>

	
	$("#province").change(function(){
		
		var bid = $(this).val();

		if(bid == -1){
			$('#area').html('');		
			$('#area').append('<option value="-1">--请选择--</option>');
		}
		$('#city').html('');
		$('#area').html('');
		$('#street').html('');
		$('#city').append('<option value="-1">--请选择--</option>');
		$('#area').append('<option value="-1">--请选择--</option>');
		$('#street').append('<option value="-1">--请选择--</option>');
     	for(var i = 0; i < city.length; i++){
			if(city[i]['parent_bid'] == bid){
				$('#city').append('<option value="'+city[i]['bid']+'">'+city[i]['area_name']+'</option>');
			}
         }    
	});

	var bid = $('#province').val();
	$('#city').html('');		
	$('#city').append('<option value="-1">--请选择--</option>');
 	for(var i = 0; i < city.length; i++){
		if(city[i]['parent_bid'] == bid){
			if(city_select == city[i]['bid']){
				$('#city').append('<option value="'+city[i]['bid']+'" selected="selected">'+city[i]['area_name']+'</option>');
			}else{
				$('#city').append('<option value="'+city[i]['bid']+'">'+city[i]['area_name']+'</option>');
			}
			
		}
     }    
	
	$("#city").change(function(){
		var bid = $(this).val();
		$('#area').html('');
		$('#street').html('');
		$('#area').append('<option value="-1">--请选择--</option>');
		$('#street').append('<option value="-1">--请选择--</option>');
     	for(var i = 0; i < area.length; i++){
			if(area[i]['parent_bid'] == bid){
				$('#area').append('<option value="'+area[i]['bid']+'">'+area[i]['area_name']+'</option>');
			}
         }    
	});

	var city_bid = $('#city').val();
	
	$('#area').html('');
	$('#area').append('<option value="-1">--请选择--</option>');
 	for(var i = 0; i < area.length; i++){
		if(area[i]['parent_bid'] == city_bid){
			if(area_select == area[i]['bid']){
				$('#area').append('<option value="'+area[i]['bid']+'" selected="selected">'+area[i]['area_name']+'</option>');
			}else{
				$('#area').append('<option value="'+area[i]['bid']+'">'+area[i]['area_name']+'</option>');
			}
			
		}
     }    

 	$("#area").change(function(){
		var bid = $(this).val();
		$('#street').html('');
		$('#street').append('<option value="-1">--请选择--</option>');
     	for(var i = 0; i < street.length; i++){
			if(street[i]['parent_bid'] == bid){
				$('#street').append('<option value="'+street[i]['bid']+'">'+street[i]['area_name']+'</option>');
			}
         }    
	});

	var area_bid = $('#area').val();
	
	$('#street').html('');
	$('#street').append('<option value="-1">--请选择--</option>');
 	for(var i = 0; i < street.length; i++){
		if(street[i]['parent_bid'] == area_bid){
			if(street_select == street[i]['bid']){
				$('#street').append('<option value="'+street[i]['bid']+'" selected="selected">'+street[i]['area_name']+'</option>');
			}else{
				$('#street').append('<option value="'+street[i]['bid']+'">'+street[i]['area_name']+'</option>');
			}
			
		}
     }    

	
})

</script>
