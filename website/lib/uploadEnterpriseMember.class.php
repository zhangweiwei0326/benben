<?php
define(ROOT_DIR_NONE, dirname(dirname(__FILE__)));
class uploadEnterpriseMember{
	public $enterprise_id;
	public $count=0;
	public $enterprise_type;
	public $allRows=0;
	
	public function __construct($enterprise_id,$enterprise_type){
		$this->enterprise_id=$enterprise_id;
		$this->enterprise_type=$enterprise_type;
	}
	/**
	 * 统计导入文件的总行数
	 */
	public function countAllRows($file,$file_location){
		$PHPReader = new PHPExcel_Reader_Excel2007();
		
		if(!$PHPReader->canRead($file))
		{
			$PHPReader = new PHPExcel_Reader_Excel5();
			if(!$PHPReader->canRead($file))
			{
				echo '<script>$(".uploading-prompt").append("<span class=\'prompt-msg\'>文件中不包含Excel文件,请重新上传!</span>")</script>';
				exit();
			}
		}
		$PHPExcel = $PHPReader->load($file);
		$currentSheet = $PHPExcel->getSheet(0);
		$allColumn = $currentSheet->getHighestColumn();
		$allRow = $currentSheet->getHighestRow();
		$startRow=2;
		
		for($currentRow=$startRow; $currentRow<=$allRow; $currentRow++){
			$id = iconv('utf-8','utf-8', $currentSheet->getCellByColumnAndRow(ord('A') - 65,$currentRow)->getValue());
			if(!empty($id)){
				$this->allRows++;
			}else{
				break;
			}
		}
		return $this->allRows;
	}

	public function phpexcel($file, $file_location) {
		$PHPReader = new PHPExcel_Reader_Excel2007();

		if(!$PHPReader->canRead($file))
		{
			$PHPReader = new PHPExcel_Reader_Excel5();
			if(!$PHPReader->canRead($file))
			{
				echo '<script>$(".uploading-prompt").append("<span class=\'prompt-msg\'>文件中不包含Excel文件,请重新上传!</span>")</script>';
				exit();
			}
		}
		$PHPExcel = $PHPReader->load($file);
		$currentSheet = $PHPExcel->getSheet(0);
		$allColumn = $currentSheet->getHighestColumn();
		$allRow = $currentSheet->getHighestRow();
		$startRow=2;
		echo '<script>$(".uploading-prompt").append("<span class=\'prompt-msg\'>正在解析:'.$files[$i].'解析成功....</span>")</script>';

		$all = array();
		for($currentRow=$startRow; $currentRow<=$allRow; $currentRow++){
			ob_start();
			//为空不保存
			$id = iconv('utf-8','utf-8', $currentSheet->getCellByColumnAndRow(ord('A') - 65,$currentRow)->getValue());
			//姓名
			$name = iconv('utf-8','utf-8', $currentSheet->getCellByColumnAndRow(ord('B') - 65,$currentRow)->getValue());
			//备注
			$remark_name = iconv('utf-8','utf-8', $currentSheet->getCellByColumnAndRow(ord('C') - 65,$currentRow)->getValue());
			//手机号码
			$phone = iconv('utf-8','utf-8',$currentSheet->getCellByColumnAndRow(ord('D') - 65,$currentRow)->getValue());
			//其他号码
			$short_phone =  iconv('utf-8','utf-8', $currentSheet->getCellByColumnAndRow(ord('E') - 65,$currentRow)->getValue());
			//查阅等级
			$access_level = iconv('utf-8','utf-8', $currentSheet->getCellByColumnAndRow(ord('F') - 65,$currentRow)->getValue());
			//每月可发送喇叭数
			$broadcast_per_month = iconv('utf-8','utf-8', $currentSheet->getCellByColumnAndRow(ord('G') - 65,$currentRow)->getValue());
			//当月剩余喇叭数
			$broadcast_available_month = iconv('utf-8','utf-8', $currentSheet->getCellByColumnAndRow(ord('H') - 65,$currentRow)->getValue());

			if (empty($id)){
				break;
			}
			echo '<script>$(".uploading-prompt").append("<span class=\'prompt-msg\'>正在上传:节目名称为'.$res_name.'正被创建...</span>")</script>';
			
			$this->sql ( $name, $remark_name, $phone, $short_phone, $access_level, $broadcast_per_month,$broadcast_available_month );
		}
		return $this->count;
	}

	public function tool($titlepic, $file_location, $name){
		$images_name = '';
		$img = explode('.', $titlepic);
		if(empty($img[1]))
		{
			$pic = '';
			//$info_error .= '-----'.$name.'图片上传不成功！<br />';
			echo '<script>$(".uploading-prompt").append("<span class=\'prompt-msg\'>正在上传:节目名称为'.$res_name.'图片上传失败...</span>")</script>';
			$result = array('pic' => $pic);
			return $result;
		}else{
			$img_name = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
			for($x=0; $x < 10; $x++){
				$images_name .= $img_name{mt_rand(0, 20)};
			}
			if(!file_exists($file_location.'/'.$titlepic)){
				$pic = '';
				echo '<script>$(".uploading-prompt").append("<span class=\'prompt-msg\'>正在上传:节目名称为'.$res_name.'图片不存在...</span>")</script>';
				$result = array('pic' => $pic);
				return $result;
			}else{
				$path_time = date('Y-m', time())."/";
				$dest_path = ROOT_DIR_NONE.'/uploads/images/'. $path_time;
				if (!is_dir($dest_path)){
					mkdir($dest_path, 0777,true);
				} 
				if(copy($file_location.'/'.$titlepic, $dest_path.$images_name.'.'.$img[1])){
					$pic = '/uploads/images/'. $path_time. $images_name.'.'.$img[1];
					$result = array('pic' => $pic);
					return $result;
// 					$load = new image_handler_class();
// 					$load->load("../upload/images/" . $images_name.'.'.$img[1]);
// 					$dst_img = ROOT_DIR_NONE."/upload/small/".$images_name.'.'.$img[1];
// 					$load->set_dest($dst_img);
// 					if($load->createImg(110, 78))
// 					{
// 						$poster = '/upload/images/'. $images_name.'.'.$img[1];
// 						$poster_s = "/upload/small/".$images_name.'.'.$img[1];
// 						$result = array('poster' => $poster, 'poster_s' => $poster_s);
// 						return $result;
					
// 					}else{
// 						$poster = '/upload/images/'. $images_name.'.'.$img[1];
// 						$poster_s = '';
// 						echo '<script>alert("正在上传:节目名称为'.$res_name.'缩略图上传失败...<br />")</script>';
// 						$result = array('poster' => $poster, 'poster_s' => $poster);
// 						return $result;
// 					}
				}else{
					$pic = '';
					echo '<script>$(".uploading-prompt").append("<span class=\'prompt-msg\'>正在上传:节目名称为'.$res_name.'图片上传失败...</span>")</script>';
					$result = array('pic' => $pic);
					return $result;
				}
			}
			
		}
}

/**
 * 移动收视表现文件
 */
public function tool_file($titlepic, $file_location, $res_name){
	$images_name = '';
	$img = explode('.', $titlepic);
	if(empty($img[1]))
	{
		$pic = '';
		//$info_error .= '-----'.$name.'图片上传不成功！<br />';
		echo '<script>$(".uploading-prompt").append("<span class=\'prompt-msg\'>正在上传:节目名称为'.$res_name.'收视表现文件上传失败...</span>")</script>';
		$result = array('pic' => $pic);
		return $result;
	}else{
		$img_name = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		for($x=0; $x < 10; $x++){
			$images_name .= $img_name{mt_rand(0, 20)};
		}
		if(!file_exists($file_location.'/'.$titlepic)){
			$pic = '';
			echo '<script>$(".uploading-prompt").append("<span class=\'prompt-msg\'>正在上传:节目名称为'.$res_name.'收视表现文件不存在...</span>")</script>';
			$result = array('pic' => $pic);
			return $result;
		}else{
			$path_time = date('Y-m', time())."/";
			$dest_path = ROOT_DIR_NONE.'/uploads/file/'. $path_time;
			if (!is_dir($dest_path)){
				mkdir($dest_path, 0777,true);
			}
			if(copy($file_location.'/'.$titlepic, $dest_path.$images_name.'.'.$img[1])){
				$pic = '/uploads/file/'. $path_time. $images_name.'.'.$img[1];
				$result = array('pic' => $pic);
				return $result;
				}else{
					$pic = '';
					echo '<script>$(".uploading-prompt").append("<span class=\'prompt-msg\'>正在上传:节目名称为'.$res_name.'收视表现文件上传失败...</span>")</script>';
					$result = array('pic' => $pic);
					return $result;
				}
			}
		}
}



public function sql ( $name, $remark_name, $phone, $short_phone, $access_level, $broadcast_per_month,$broadcast_available_month ){
	if($this->enterprise_type==1){		//企业政企
		$member_id = Member::model ()->find ( "phone='" . $phone . "'" )->id;
	}else{		//虚拟网政企
		$member_id = Member::model ()->find ( "cornet='" . $short_phone . "'" )->id;
	}
	$model1 = new EnterpriseMember();
	$model1->contact_id=$this->enterprise_id;
	$model1->member_id=$member_id;
	$model1->name=$name;
	$model1->remark_name=$remark_name;
	if($this->enterprise_type==1){
		$model1->phone=$phone;
	}
	$model1->short_phone=$short_phone;
	$model1->created_time=time();
	if($model1->save()){
		$model2=new EnterpriseMemberManage();
		$model2->member_id=$model1->attributes['id'];
		$model2->group_id=0;
		$model2->access_level=$access_level;
		$model2->is_manage=0;
		$model2->broadcast_per_month=$broadcast_per_month;
		$model2->broadcast_available_month=$broadcast_available_month;
		$model2->created_time=time();
		if($model2->save()){
			$this->count = $this->count + 1;
		}
	}
// 	if($model->save()){
// 		$this->count = $this->count + 1;
// 		#将权益描述插入权益表中
// 		$profits = trim($profits);
// 		if(!empty($profits)){
// 			$profits=explode("|", $profits);
// 			foreach ($profits as $v){
// 				$profit_price=explode("+", $v);
// 				$profitModel=new SmgProfit;
// 				$profitModel->name=$profit_price[0];
// 				$profitModel->price=$profit_price[1];
// 				$profitModel->resource_id=$model->attributes['id'];
// 				$profitModel->sale_status=1;
// 				$profitModel->created_time=time();
// 				$profitModel->save();
// 			}
// 		}
// 		echo '<script>$(".uploading-prompt").append("<span class=\'prompt-msg\'>正在上传:节目名称为'.$res_name.'创建成功...</span>")</script>';
// // 		$this->redirect($this->getBackListPageUrl());
// 	}else{
// // 		var_dump($model->getErrors());
// 		echo '<script>$(".uploading-prompt").append("<span class=\'prompt-msg\'>正在上传:节目名称为'.$res_name.'创建失败...</span>")</script>';
// // 	}
// 	return 	$this->count;
}

}
?>