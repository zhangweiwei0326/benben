<div class="main_content_left col-lg-2 col-md-2 hidden-sm hidden-xs">
	<div class="main_left_logo">
		<a href="<?php echo Yii::app()->createUrl('/index');?>"><img
			src="<?php echo Yii::app()->request->baseUrl; ?>/themes/images/left_logo.png"></a>
	</div>
	<div class="main_left_navi">
	<?php
	if ($menu) {
		$serial = 10;
		foreach ( $menu as $key => $item ) {
			if($item['role'] == 1){
			?>
		<div  data="menu_<?php echo $serial;?>" class="main_left_navi_home <?php if ($index >= $serial  && $index < ($serial +10) ) echo 'main_left_navi_home_selected';?>">
			<div class="main_left_navi_home_font_unselected <?php if ($index >= $serial  && $index < ($serial +10) ) echo 'main_left_navi_home_font_selected';?>">
				<span><?php echo $item['name'];?></span>
			</div>
		</div>
	<?php }
			$display = 'none';
			if ($index >= $serial && $index < ($serial + 10)) { $display = 'block';}
				$current = 0;
				foreach ( $subMenu [$key] as $key => $item ) {
					
					if($item['role'] ==1){
					?>
						<div class="main_left_navi_subnav" style="display:<?php echo $display;?>" data="menu_<?php echo $serial;?>">
							<div class="main_left_navi_subnav_font_unselected  <?php  if($index == $serial+$current){	 echo 'main_left_navi_subnav_font_selected'; } ?>">
								<a href="<?php echo Yii::app()->createUrl($item['uri']);?>"><?php echo $item['name'];?></a>
							</div>
						</div>
							<?php }
					$current ++;
				}
			
			$serial += 10;

		}
	}
	?>
	</div>
</div>

