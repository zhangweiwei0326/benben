<?php
define(ROOT_DIR_NONE, dirname(dirname(__FILE__)));
class uploadzip{

	

	private $rar_path;   //解压文件路径
	private $tname; //上传临时文件
	private $dishesid;
	private $count;
	private $db;
	private $path2;
	private $ec_count ; //execl文件的数量 0:有 1:没有

	public function __construct($rar_path, $tname, $path2){
		require_once ('phpexcel/Classes/PHPExcel.php');
// 		include_once("../frame.php");
		$this->rar_path = $rar_path;               //初始化解压文件路径
		$this->tname = $tname;
		$this->count = 0;
		$this->path2 = $path2;
		$this->ec_count = 0;

	}

	//解压文件
	public function unzip(){
		if(!file_exists($this->rar_path)){  // 判断存放文件目录是否存在
			mkdir($this->rar_path,0777,true);
		}
		if(!exec('unzip '.$this->tname.' -d '.$this->rar_path)){
			echo '<script>$(".uploading-prompt").append("<span class=\'prompt-msg\'>正在上传,请勿操作!</span>")</script>';
			exit();
		}else{
			echo '<script>$(".uploading-prompt").append("<span class=\'prompt-msg\'>上传中:解压文件成功....</span>")</script>';
			$this->scandirFile($this->rar_path, $this->path2);  //遍历文件
				
			if($this->ec_count == 0){
				echo '<script>$(".uploading-prompt").append("<span class=\'prompt-msg\'>上传失败:压缩文件中不存在Excel文件....</span>")</script>';
			}
				
			return $this->count;
		}
	}

	public function scandirFile($rar_path, $file_name){
		$result = array();
		$files = scandir($rar_path);
		//遍历文件夹
		foreach ($files as $file){
			$file_location=$rar_path."/".$file;//生成路径
				
			//判断是不是文件夹
			if(is_dir($file_location) && $file!="." && $file!=".."){
				echo '<script>$(".uploading-prompt").append("<span class=\'prompt-msg\'>正在解析:压缩文件中'.$file_name.'/'.$file.'文件夹....</span>")</script>';
				$this->scandirFile($file_location, $file);
			}else {
				if(preg_match('/(resource)(\.)xl(s[xmb]|t[xm]|am)$/i',$file)){         //如果不是文件夹
					$result[] = $file;
					$files = $rar_path.'/'.$file;
					echo '<script>$(".uploading-prompt").append("<span class=\'prompt-msg\'>正在解析:压缩文件中'.$file.'....</span>")</script>';
					$this->ec_count = 1;
					return $this->phpexcel($files, $rar_path);
				}
			}
		}

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
		echo '<script>$(".uploading-prompt").append("<span class=\'prompt-msg\'>正在解析:'.$files[$i].'解析成功....</span>")</script>';
			
		$all = array();
		for($currentRow=3; $currentRow<=$allRow; $currentRow++){
			ob_start();
			//为空不保存
			$id = iconv('utf-8','utf-8', $currentSheet->getCellByColumnAndRow(ord('A') - 65,$currentRow)->getValue());
			//节目名称
			$res_name = iconv('utf-8','utf-8', $currentSheet->getCellByColumnAndRow(ord('B') - 65,$currentRow)->getValue());
			//首播时间
			$first_time = iconv('utf-8','utf-8', $currentSheet->getCellByColumnAndRow(ord('C') - 65,$currentRow)->getValue());
			//首播时间段
// 			$first_time_area = iconv('utf-8','utf-8', gmdate("H:i",PHPExcel_Shared_Date::ExcelToPHP($currentSheet->getCellByColumnAndRow(ord('D') - 65,$currentRow)->getValue())));
			$first_time_area = iconv('utf-8','utf-8',$currentSheet->getCellByColumnAndRow(ord('D') - 65,$currentRow)->getValue());
			//播出周期
			$air_cycle =  iconv('utf-8','utf-8', $currentSheet->getCellByColumnAndRow(ord('E') - 65,$currentRow)->getValue());
			//播出平台
			$air_platform = iconv('utf-8','utf-8', $currentSheet->getCellByColumnAndRow(ord('F') - 65,$currentRow)->getValue());
			//节目类型
			$res_type = iconv('utf-8','utf-8', $currentSheet->getCellByColumnAndRow(ord('G') - 65,$currentRow)->getValue());
			//详细类型
			$res_type_detail = iconv('utf-8','utf-8', $currentSheet->getCellByColumnAndRow(ord('H') - 65,$currentRow)->getValue());
			//节目亮点
			$res_highlights = iconv('utf-8','utf-8', $currentSheet->getCellByColumnAndRow(ord('I') - 65,$currentRow)->getValue());
			//主持人
			$host = iconv('utf-8','utf-8', $currentSheet->getCellByColumnAndRow(ord('J') - 65,$currentRow)->getValue());
			//节目图片
			$res_pics = iconv('utf-8','utf-8', $currentSheet->getCellByColumnAndRow(ord('K') - 65,$currentRow)->getValue());
			//收视率
			$ratings = iconv('utf-8','utf-8', $currentSheet->getCellByColumnAndRow(ord('L') - 65,$currentRow)->getValue());
			//权益描述
			$profits = iconv('utf-8','utf-8', $currentSheet->getCellByColumnAndRow(ord('M') - 65,$currentRow)->getValue());
			//三十秒刊例价
			$basic_price=iconv('utf-8','utf-8', $currentSheet->getCellByColumnAndRow(ord('N') - 65,$currentRow)->getValue());
			//权益图集
			$profits_pics = iconv('utf-8','utf-8', $currentSheet->getCellByColumnAndRow(ord('O') - 65,$currentRow)->getValue());
			//价格
			$price = iconv('utf-8','utf-8', $currentSheet->getCellByColumnAndRow(ord('P') - 65,$currentRow)->getValue());
			//期数/周期
			$periods = iconv('utf-8','utf-8', $currentSheet->getCellByColumnAndRow(ord('Q') - 65,$currentRow)->getValue());
			//出售情况
			$sale_status = iconv('utf-8','utf-8', $currentSheet->getCellByColumnAndRow(ord('R') - 65,$currentRow)->getValue());
			//视频链接
			$video_url = iconv('utf-8','utf-8', $currentSheet->getCellByColumnAndRow(ord('S') - 65,$currentRow)->getValue());
			//男性数量
			$male_num = iconv('utf-8','utf-8', $currentSheet->getCellByColumnAndRow(ord('T') - 65,$currentRow)->getValue());
			//女性数量
			$female_num = iconv('utf-8','utf-8', $currentSheet->getCellByColumnAndRow(ord('U') - 65,$currentRow)->getValue());
			//年龄段
			$age_group = iconv('utf-8','utf-8', $currentSheet->getCellByColumnAndRow(ord('V') - 65,$currentRow)->getValue());
			//封面图片
			$cover_pic = iconv('utf-8','utf-8', $currentSheet->getCellByColumnAndRow(ord('W') - 65,$currentRow)->getValue());
			//视频表现
			$air_performance = iconv('utf-8','utf-8', $currentSheet->getCellByColumnAndRow(ord('X') - 65,$currentRow)->getValue());
			//可在售日期
			$resale = iconv('utf-8','utf-8', $currentSheet->getCellByColumnAndRow(ord('Y') - 65,$currentRow)->getValue());
			//合作品牌描述
			$cooperative_brand= iconv('utf-8','utf-8', $currentSheet->getCellByColumnAndRow(ord('Z') - 65,$currentRow)->getValue());
			//合作品牌图集
			$cooperative_brand_pic = iconv('utf-8','utf-8', $currentSheet->getCellByColumnAndRow(ord(substr('AA', 1)) - 39,$currentRow)->getValue());
			//节目介绍
			$introduce=iconv('utf-8','utf-8', $currentSheet->getCellByColumnAndRow(ord(substr('AB', 1)) - 39,$currentRow)->getValue());
			if (empty($id)){
				break;
			}
			echo '<script>$(".uploading-prompt").append("<span class=\'prompt-msg\'>正在上传:节目名称为'.$res_name.'正被创建...</span>")</script>';
			#最低价格处理
			if(!empty($profits)){
				$profits_temp=explode("|", $profits);
				$temp=array();
				foreach ($profits_temp as $v){
					$v=explode("+", $v);
					$temp[]=$v[1];
				}
				if(!empty($temp)){
					$min=$temp[0];
					foreach ($temp as $v){
						$min=($v < $min)?$v:$min;
					}
					if(!empty($min)){
						$price=$min * $basic_price;
					}
				}
			}
			#收视表现文件处理
			$file=$this->tool_file($air_performance, $file_location,$res_name);
			$air_performance=$file['pic'];
			#封面图片字段处理
			$image = $this->tool($cover_pic, $file_location,$res_name);
			$cover_pic = $image['pic'];
			#可在售日期字段处理
			$resale=intval(strtotime($resale));
			#节目图片字段处理
			$res_pics=explode("|", $res_pics);
			$data = array();
			foreach ($res_pics as $v){
				$pic_url=explode("+", $v);
				$image=$this->tool($pic_url[0], $file_location,$res_name);
				$data[]=$image['pic']."+".$pic_url[1];
			}
			$res_pics=implode("|", $data);
			#三十秒刊例价字段处理
			$basic_price=intval($basic_price);
			#权益描述字段
			if(!empty($profits)){
				$profits=explode("|", $profits);
				$data=array();
				foreach ($profits as $k=>$v){
					$temp=explode("+", $v);
					$data[]=$temp[0]."+".floatval($temp[1])*$basic_price;
				}
				$profits=implode("|", $data);
			}
			#权益图集字段处理
			$profits_pics=explode("|", $profits_pics);
			$data=array();
			foreach ($profits_pics as $v){
				$image=$this->tool($v, $file_location,$res_name);
				$data[]=$image['pic'];
			}
			$profits_pics=implode("|", $data);
			#播出平台管理
			$air_platform=explode("|", trim($air_platform));
			$data=array();
			foreach ($air_platform as $v){
				$rs=SmgMedia::model()->find("name='".$v."'");
				if(!empty($rs->id)){
					$data[]=$rs->id;
				}else{
					$model=new SmgMedia;  $model->name=$v;	$model->created_time=time();	$model->save();	$data[]=$model->attributes['id'];
				}
			}
			$air_platform=implode("|", $data);
			//合作品牌图集处理
			$cooperative_brand_pic = explode('|', $cooperative_brand_pic);
			$tmp = array();
			foreach ($cooperative_brand_pic as $value){
				$image=$this->tool($value, $file_location,$res_name);
				$tmp[]=$image['pic'];
			}
			$cooperative_brand_pic = implode('|', $tmp);
			
			$this->sql ( $res_name, $first_time, $first_time_area, $air_cycle, $air_platform, $res_type, $res_type_detail, $res_highlights, $host, $res_pics, $ratings, $profits, $profits_pics, $price, $periods, $sale_status, $video_url, $male_num, $female_num, $age_group, $cover_pic, $air_performance, $resale, $cooperative_brand, $cooperative_brand_pic,$introduce,$basic_price );
			
		}
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



public function sql ( $res_name, $first_time, $first_time_area, $air_cycle, $air_platform, $res_type, $res_type_detail, $res_highlights, $host, $res_pics, $ratings, $profits, $profits_pics, $price, $periods, $sale_status, $video_url, $male_num, $female_num, $age_group, $cover_pic, $air_performance, $resale, $cooperative_brand, $cooperative_brand_pic,$introduce,$basic_price  ){
	$model = new SmgResource();
	$model->sort = 0;
	$model->name = $res_name;
	$model->first_time = $first_time;
	$model->time = $first_time_area;
	$model->cycle = $air_cycle;
	$model->media = $air_platform;
	$model->type = $res_type; 
	$model->detail_type = $res_type_detail;
	$model->lights = $res_highlights;
	$model->host = $host;
	$model->show_pic = $res_pics;
	$model->ratings = $ratings;
	$model->profit_description = $profits;
	$model->profit_description_pic = $profits_pics;
	$model->price = $price;
	$model->num_cycle = $periods;
	$model->sale_status = $sale_status;
	$model->video_href = $video_url;
	$model->man_num = $male_num;
	$model->woman_num = $female_num;
	$model->age_group = $age_group;
	$model->cover_pic = $cover_pic;
	$model->view_performance = $air_performance;
	$model->cooperative_brand = $cooperative_brand;
	$model->cooperative_brand_pic = $cooperative_brand_pic;
	$model->introduce = $introduce;
	$model->sale_again = $resale;
	$model->is_show = 1;
	$model->created_time = time();
	$model->basic_price = $basic_price;
	if($model->save()){
		$this->count = $this->count + 1;
		#将权益描述插入权益表中
		$profits = trim($profits);
		if(!empty($profits)){
			$profits=explode("|", $profits);
			foreach ($profits as $v){
				$profit_price=explode("+", $v);
				$profitModel=new SmgProfit;
				$profitModel->name=$profit_price[0];
				$profitModel->price=$profit_price[1];
				$profitModel->resource_id=$model->attributes['id'];
				$profitModel->sale_status=1;
				$profitModel->created_time=time();
				$profitModel->save();
			}
		}
		echo '<script>$(".uploading-prompt").append("<span class=\'prompt-msg\'>正在上传:节目名称为'.$res_name.'创建成功...</span>")</script>';
// 		$this->redirect($this->getBackListPageUrl());
	}else{
// 		var_dump($model->getErrors());
		echo '<script>$(".uploading-prompt").append("<span class=\'prompt-msg\'>正在上传:节目名称为'.$res_name.'创建失败...</span>")</script>';
	}
	return 	$this->count;
}

}
?>