<?php
header("Content-type:text/html; charset=utf-8");
require_once 'Classes/PHPExcel.php';
include "../frame.php";

$action = intval($_GET['action']);
$file = "./dianping.xls";
$PHPReader = new PHPExcel_Reader_Excel2007();
if(!$PHPReader->canRead($file)){
	$PHPReader = new PHPExcel_Reader_Excel5();
	if(!$PHPReader->canRead($file)){
		echo 'no Excel';
		die();
	}
}

$PHPExcel = $PHPReader->load($file);
$currentSheet = $PHPExcel->getSheet(0);
$allColumn = $currentSheet->getHighestColumn();
$allRow = $currentSheet->getHighestRow();

$db = get_db();
for($currentRow=2; $currentRow<=$allRow; $currentRow++){
	

	$name = iconv('utf-8','utf-8', $currentSheet->getCellByColumnAndRow(ord('A') - 65,$currentRow)->getValue());
	$poster =  $currentSheet->getCellByColumnAndRow(ord('X') - 65,$currentRow)->getValue();
	$address = iconv('utf-8','utf-8', $currentSheet->getCellByColumnAndRow(ord('E') - 65,$currentRow)->getValue());
	$tel = iconv('utf-8','utf-8', $currentSheet->getCellByColumnAndRow(ord('F') - 65,$currentRow)->getValue());
	$bussiness_hours = iconv('utf-8','utf-8', $currentSheet->getCellByColumnAndRow(ord('G') - 65,$currentRow)->getValue());
	$description = iconv('utf-8','utf-8', $currentSheet->getCellByColumnAndRow(ord('I') - 65,$currentRow)->getValue());
	$latitude =  $currentSheet->getCellByColumnAndRow(ord('J') - 65,$currentRow)->getValue();
	$longitude =  $currentSheet->getCellByColumnAndRow(ord('K') - 65,$currentRow)->getValue();
	$city = iconv('utf-8','utf-8', $currentSheet->getCellByColumnAndRow(ord('L') - 65,$currentRow)->getValue());
	$region = iconv('utf-8','utf-8', $currentSheet->getCellByColumnAndRow(ord('M') - 65,$currentRow)->getValue());
	$business_area = iconv('utf-8','utf-8', $currentSheet->getCellByColumnAndRow(ord('N') - 65,$currentRow)->getValue());
	$traffic = iconv('utf-8','utf-8', $currentSheet->getCellByColumnAndRow(ord('O') - 65,$currentRow)->getValue());
	$created_time = time();
	if(!$latitude) $latitude = 0;
	if(!$longitude) $longitude = 0;
	var_dump($name);
	echo "<br />";
	$sql = 'insert into YY_store(name, industry, poster, address, tel, bussiness_hours, description, latitude, longitude, city, region, business_area, traffic, created_time, status) value ("'.$name.'", 6, "'.$poster.'", "'.$address.'", "'.$tel.'", "'.$bussiness_hours.'", "'.$description.'", '.$latitude.', '.$longitude.', "'.$city.'", "'.$region.'", "'.$business_area.'", "'.$traffic.'", '.$created_time.', 1)';
	echo $sql;
	if($action === 1123){
		$db->execute($sql);
	}
	
	echo "<hr>";
}
?>