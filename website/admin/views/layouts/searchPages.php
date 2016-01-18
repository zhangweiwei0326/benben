		<ul class="mypageul">
		<li class="pageli">
			<input type="text" value="" class="input_page">	
			<a href="javascript:void(0)" class="search_pages" baseUrl="<?php echo Yii::app()->request->baseUrl.Yii::app()->request->getUrl()?>">GO</a>
		</li>
		<li class="pageli">
			 第<strong><?php 
			 				if($_GET['page']){
			 					if($_GET['page'] > ceil($pages->itemCount/$pages->pageSize)){
			 						echo ceil($pages->itemCount/$pages->pageSize);
			 					}else if(!is_numeric($_GET['page'])){
			 						echo 1;
			 					}else if($_GET['page'] <= 0){
			 						echo 1;
			 					}else{
			 						echo $_GET['page'];
			 					}
			 				}else{
			 					echo 1;
			 				}
			 				?></strong>页/共<strong class="pagecount"><?php echo ceil($pages->itemCount/$pages->pageSize);?></strong>页,
			 				<strong class="pagecount"><?php echo $pages->itemCount;?></strong>条记录
		</li>
	</ul>
