<?php
Yii::$enableIncludePath = false;
define('__ROOT__',dirname(dirname(__FILE__)));
require_once (__ROOT__.'/PHPExcel/PHPExcel.php');
class FindstatisticController extends BaseController
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/admin';

	/**
	 * @var int the define the index for the menu
	 */

	public $menuIndex = 44;
	/**

	*/
	public function actionIndex()
	{
		$this->insert_log(44);
		$created_time1 = Frame::getStringFromRequest('created_time1');
		$created_time2 = Frame::getStringFromRequest('created_time2');
		$connection = Yii::app()->db;
		$sql = "select count(*) as c from member where id_enable = 1";
		$command = $connection->createCommand($sql);
		$totalQuery = $command->queryAll();
		$totalNumber = $totalQuery[0]['c'];
		//朋友圈
		$sql = "SELECT member_id, count(*) c FROM friend where 1 = 1 ";
		if (strtotime($created_time1) > 0) {
			$sql .= ' and created_time >= '.strtotime($created_time1);
			$info['created_time1'] = $created_time1;
		}
		if (strtotime($created_time2) > 0) {
			$sql .= ' and created_time <= '.(strtotime($created_time2)+3600*24);
			$info['created_time2'] = $created_time2;
		}

		$sql .= ' group by member_id ';
		$command = $connection->createCommand($sql);
		$friendQuery = $command->queryAll();
		$friendInfo = array(
			array('number'=>count($friendQuery), 'name'=>'全部'),
			array('number'=>$totalNumber - count($friendQuery), 'name'=>'0'),
			array('number'=>0, 'name'=>'1-5'),
			array('number'=>0, 'name'=>'6-15'),
			array('number'=>0, 'name'=>'16-50'),
			array('number'=>0, 'name'=>'51-100'),
			array('number'=>0, 'name'=>'101-200'),
			array('number'=>0, 'name'=>'201-500'),
			array('number'=>0, 'name'=>'500以上')
		);
		if ($friendQuery) {
			foreach ($friendQuery as $key => $value) {
				if ($value['c'] <= 5) {
					$friendInfo[2]['number']++;
					$friendInfo[2]['info'][] = $value['member_id'];
				}else if ($value['c'] <= 15){
					$friendInfo[3]['number']++;
					$friendInfo[3]['info'][] = $value['member_id'];
				}else if ($value['c'] <= 50){
					$friendInfo[4]['number']++;
					$friendInfo[4]['info'][] = $value['member_id'];
				}else if ($value['c'] <= 100){
					$friendInfo[5]['number']++;
					$friendInfo[5]['info'][] = $value['member_id'];
				}else if ($value['c'] <= 200){
					$friendInfo[6]['number']++;
					$friendInfo[6]['info'][] = $value['member_id'];
				}else if ($value['c'] <= 500){
					$friendInfo[7]['number']++;
					$friendInfo[7]['info'][] = $value['member_id'];
				}else {
					$friendInfo[8]['number']++;
					$friendInfo[8]['info'][] = $value['member_id'];
				}
			}
		}

		//微创作
		$sql = "SELECT member_id, count(*) c FROM creation where 1 = 1 ";
		if (strtotime($created_time1) > 0) {
			$sql .= ' and created_time >= '.strtotime($created_time1);
			$info['created_time1'] = $created_time1;
		}
		if (strtotime($created_time2) > 0) {
			$sql .= ' and created_time <= '.(strtotime($created_time2)+3600*24);
			$info['created_time2'] = $created_time2;
		}

		$sql .= ' group by member_id ';
		$command = $connection->createCommand($sql);
		$collectQuery = $command->queryAll();
		$collectInfo = array(
			array('number'=>count($collectQuery), 'name'=>'全部'),
			array('number'=>$totalNumber - count($collectQuery), 'name'=>'0'),
			array('number'=>0, 'name'=>'1-10'),
			array('number'=>0, 'name'=>'11-20'),
			array('number'=>0, 'name'=>'21-50'),
			array('number'=>0, 'name'=>'51-100'),
			array('number'=>0, 'name'=>'101-200'),
			array('number'=>0, 'name'=>'201-500'),
			array('number'=>0, 'name'=>'500以上')
			);
		if ($collectQuery) {
			foreach ($collectQuery as $key => $value) {
				if ($value['c'] <= 10) {
					$collectInfo[2]['number']++;
					$collectInfo[2]['info'][] = $value['member_id'];
				}else if ($value['c'] <= 20){
					$collectInfo[3]['number']++;
					$collectInfo[3]['info'][] = $value['member_id'];
				}else if ($value['c'] <= 50){
					$collectInfo[4]['number']++;
					$collectInfo[4]['info'][] = $value['member_id'];
				}else if ($value['c'] <= 100){
					$collectInfo[5]['number']++;
					$collectInfo[5]['info'][] = $value['member_id'];
				}else if ($value['c'] <= 200){
					$collectInfo[6]['number']++;
					$collectInfo[6]['info'][] = $value['member_id'];
				}else if ($value['c'] <= 500){
					$collectInfo[7]['number']++;
					$collectInfo[7]['info'][] = $value['member_id'];
				}else {
					$collectInfo[8]['number']++;
					$collectInfo[8]['info'][] = $value['member_id'];
				}
			}
		}

		//我要买
		$sql = "SELECT member_id, count(*) c FROM buy where 1 = 1 ";
		if (strtotime($created_time1) > 0) {
			$sql .= ' and created_time >= '.strtotime($created_time1);
			$info['created_time1'] = $created_time1;
		}
		if (strtotime($created_time2) > 0) {
			$sql .= ' and created_time <= '.(strtotime($created_time2)+3600*24);
			$info['created_time2'] = $created_time2;
		}

		$sql .= ' group by member_id ';
		$command = $connection->createCommand($sql);
		$buyQuery = $command->queryAll();
		$buyInfo = array(
			array('number'=>count($buyQuery), 'name'=>'全部'),
			array('number'=>$totalNumber - count($buyQuery), 'name'=>'0'),
			array('number'=>0, 'name'=>'1-10'),
			array('number'=>0, 'name'=>'11-20'),
			array('number'=>0, 'name'=>'21-50'),
			array('number'=>0, 'name'=>'51-100'),
			array('number'=>0, 'name'=>'101-200'),
			array('number'=>0, 'name'=>'201-500'),
			array('number'=>0, 'name'=>'500以上')
			);
		if ($buyQuery) {
			foreach ($buyQuery as $key => $value) {
				if ($value['c'] <= 10) {
					$buyInfo[2]['number']++;
					$buyInfo[2]['info'][] = $value['member_id'];
				}else if ($value['c'] <= 20){
					$buyInfo[3]['number']++;
					$buyInfo[3]['info'][] = $value['member_id'];
				}else if ($value['c'] <= 50){
					$buyInfo[4]['number']++;
					$buyInfo[4]['info'][] = $value['member_id'];
				}else if ($value['c'] <= 100){
					$buyInfo[5]['number']++;
					$buyInfo[5]['info'][] = $value['member_id'];
				}else if ($value['c'] <= 200){
					$buyInfo[6]['number']++;
					$buyInfo[6]['info'][] = $value['member_id'];
				}else if ($value['c'] <= 500){
					$buyInfo[7]['number']++;
					$buyInfo[7]['info'][] = $value['member_id'];
				}else {
					$buyInfo[8]['number']++;
					$buyInfo[8]['info'][] = $value['member_id'];
				}
			}
		}

		//我要买接受报价次数
		$sql = "SELECT member_id, count(*) c FROM buy where is_accept = 1 ";
		if (strtotime($created_time1) > 0) {
			$sql .= ' and created_time >= '.strtotime($created_time1);
			$info['created_time1'] = $created_time1;
		}
		if (strtotime($created_time2) > 0) {
			$sql .= ' and created_time <= '.(strtotime($created_time2)+3600*24);
			$info['created_time2'] = $created_time2;
		}

		$sql .= ' group by member_id ';
		$command = $connection->createCommand($sql);
		$quoteQuery = $command->queryAll();
		$quoteInfo = array(
			array('number'=>count($quoteQuery), 'name'=>'全部'),
			array('number'=>$totalNumber - count($quoteQuery), 'name'=>'0'),
			array('number'=>0, 'name'=>'1-10'),
			array('number'=>0, 'name'=>'11-20'),
			array('number'=>0, 'name'=>'21-50'),
			array('number'=>0, 'name'=>'51-100'),
			array('number'=>0, 'name'=>'101-200'),
			array('number'=>0, 'name'=>'201-500'),
			array('number'=>0, 'name'=>'500以上')
			);
		if ($quoteQuery) {
			foreach ($quoteQuery as $key => $value) {
				if ($value['c'] <= 10) {
					$quoteInfo[2]['number']++;
					$quoteInfo[2]['info'][] = $value['member_id'];
				}else if ($value['c'] <= 20){
					$quoteInfo[3]['number']++;
					$quoteInfo[3]['info'][] = $value['member_id'];
				}else if ($value['c'] <= 50){
					$quoteInfo[4]['number']++;
					$quoteInfo[4]['info'][] = $value['member_id'];
				}else if ($value['c'] <= 100){
					$quoteInfo[5]['number']++;
					$quoteInfo[5]['info'][] = $value['member_id'];
				}else if ($value['c'] <= 200){
					$quoteInfo[6]['number']++;
					$quoteInfo[6]['info'][] = $value['member_id'];
				}else if ($value['c'] <= 500){
					$quoteInfo[7]['number']++;
					$quoteInfo[7]['info'][] = $value['member_id'];
				}else {
					$quoteInfo[8]['number']++;
					$quoteInfo[8]['info'][] = $value['member_id'];
				}
			}
		}
		
		$this->render('index',array('friend'=>$friendInfo, 'creation'=>$collectInfo,'buy'=>$buyInfo,'quote'=>$quoteInfo, 'info'=>$info));
	}

	public function actionDownloadFriend()
	{
		$str = Frame::getStringFromRequest('str');
		$code = Frame::getStringFromRequest('code');
		$key = Frame::getIntFromRequest('key');
		$type = Frame::getIntFromRequest('type');
		$created_time1 = Frame::getStringFromRequest('created_time1');
		$created_time2 = Frame::getStringFromRequest('created_time2');
		$connection = Yii::app()->db;
		if ($code != md5($str.'excel')) {
			die('unvalid');
		}
		$allInfo = array();
		if ( $key == 1) {
			$sql = "select id, benben_id, province, city, area from member";
			$command = $connection->createCommand($sql);
			$totalQuery = $command->queryAll();
			//朋友圈
			$sql = "SELECT member_id, count(*) c FROM friend where 1 = 1 ";
			if (strtotime($created_time1) > 0) {
				$sql .= ' and created_time >= '.strtotime($created_time1);
				$info['created_time1'] = $created_time1;
			}
			if (strtotime($created_time2) > 0) {
				$sql .= ' and created_time <= '.(strtotime($created_time2)+3600*24);
				$info['created_time2'] = $created_time2;
			}

			$sql .= ' group by member_id ';
			$command = $connection->createCommand($sql);
			$friendQuery = $command->queryAll();
			$memberRelation = array();
			$haveMember = array();
			if ($friendQuery) {
				foreach ($friendQuery as $k => $value) {
					$memberRelation[$value['member_id']] = $value['c'];
					$haveMember[] = $value['member_id'];
				}
			}
			foreach ($totalQuery as $value){
				$pro[] = $value['province'];
				$pro[] = $value['city'];
				$pro[] = $value['area'];
			}
				
			$pro_name = $this->allareas(implode(",", $pro));
			if($pro_name){
				foreach ($pro_name as $val){
					$pro_arr[$val['bid']] = $val['area_name'];
				}
			}
			foreach ($totalQuery as $k => $value) {
				$number = 0;
				
				if(isset($memberRelation[$value['id']])){
					$number = $memberRelation[$value['id']];
				}
				$pron = $pro_arr[$value['province']].''.$pro_arr[$value['city']];
				if ($key == 0) {
					$allInfo[] = array('id'=>$value['id'], 'benben_id'=>$value['benben_id'],'area'=>$pron, 'number'=>$number);
				}else{
					if (!in_array($value['id'], $haveMember)) {
						$allInfo[] = array('id'=>$value['id'],'benben_id'=>$value['benben_id'],'area'=>$pron, 'number'=>$number);
					}
				}
			}

		}else{
			//朋友圈
			$sql = "SELECT member_id, count(*) c FROM friend where 1 = 1 ";
			if (strtotime($created_time1) > 0) {
				$sql .= ' and created_time >= '.strtotime($created_time1);
				$info['created_time1'] = $created_time1;
			}
			if (strtotime($created_time2) > 0) {
				$sql .= ' and created_time <= '.(strtotime($created_time2)+3600*24);
				$info['created_time2'] = $created_time2;
			}
			$sql .= ' group by member_id ';
			if ($key == 2) {
				$sql .= ' having count(*) >=1 and count(*) <= 5';
			}else if ($key == 3) {
				$sql .= ' having count(*) >=6 and count(*) <= 15';
			}else if ($key == 4) {
				$sql .= ' having count(*) >=16 and count(*) <= 50';
			}else if ($key == 5) {
				$sql .= ' having count(*) >=51 and count(*) <= 100';
			}else if ($key == 6) {
				$sql .= ' having count(*) >=101 and count(*) <= 200';
			}else if ($key == 7) {
				$sql .= ' having count(*) >=201 and count(*) <= 500';
			}else if ($key == 8) {
				$sql .= ' having count(*) >=501';
			}
			$command = $connection->createCommand($sql);
			$friendQuery = $command->queryAll();
			$memberRelation = array();
			$haveMember = array();
			if ($friendQuery) {
				foreach ($friendQuery as $k => $value) {
					$memberRelation[$value['member_id']] = $value['c'];
					$haveMember[] = $value['member_id'];
				}
			}
			$sql = "select id, benben_id, province, city, area from member where id in (".implode($haveMember, ",").")";
			$command = $connection->createCommand($sql);
			$totalQuery = $command->queryAll();
			foreach ($totalQuery as $value){
				$pro[] = $value['province'];
				$pro[] = $value['city'];
				$pro[] = $value['area'];
			}
				
			$pro_name = $this->allareas(implode(",", $pro));
			if($pro_name){
				foreach ($pro_name as $val){
					$pro_arr[$val['bid']] = $val['area_name'];
				}
			}
			foreach ($totalQuery as $k => $value) {
				$number = 0;
				
				if(isset($memberRelation[$value['id']])){
					$number = $memberRelation[$value['id']];
				}
				$pron = $pro_arr[$value['province']].''.$pro_arr[$value['city']];
				$allInfo[] = array('id'=>$value['id'], 'benben_id'=>$value['benben_id'],'area'=>$pron, 'number'=>$number);
			}

		}
		if ($allInfo) {
			$objPHPExcel = new PHPExcel();
			/*--------------设置表头信息------------------*/
			//第一个sheet
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A1', '编号')
			->setCellValue('B1', '奔犇号')
			->setCellValue('C1', '地区')
			->setCellValue('D1', '发帖次数')
			;
			
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
			$styleArray1 = array(
					'font' => array(
							'bold' => true,
							'color'=>array(
									'argb' => '00000000',
							),
					),
					'alignment' => array(
							'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					),
			);
			$objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray1);
			$objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($styleArray1);
			$objPHPExcel->getActiveSheet()->getStyle('C1')->applyFromArray($styleArray1);
			$objPHPExcel->getActiveSheet()->getStyle('D1')->applyFromArray($styleArray1);
			$objPHPExcel->getActiveSheet()->setTitle('朋友圈');      //设置sheet的名称
			$objPHPExcel->setActiveSheetIndex(0);   //设置sheet的起始位置
			
	
			$i =2;
			foreach ($allInfo as  $one){	
				$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue("A$i", $i-1)
				->setCellValue("B$i", $one['benben_id'])
				->setCellValue("C$i", $one['area'])
				->setCellValue("D$i", $one['number']);
				$i++;
			}
			
			ob_end_clean();
			ob_start();
			header('Content-Type: application/vnd.ms-excel;charset=utf-8');
			header('Content-Disposition:attachment;filename=' . urlencode('info' . date("YmjHis") .'.xls') . '');
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');   //通过PHPExcel_IOFactory的写函数将上面数据写出来
		
			$objWriter->save('php://output');
		}
	}


	public function actionDownloadBuy()
	{
		$str = Frame::getStringFromRequest('str');
		$code = Frame::getStringFromRequest('code');
		$key = Frame::getIntFromRequest('key');
		$type = Frame::getIntFromRequest('type');
		$created_time1 = Frame::getStringFromRequest('created_time1');
		$created_time2 = Frame::getStringFromRequest('created_time2');
		$connection = Yii::app()->db;
		if ($code != md5($str.'excel')) {
			die('unvalid');
		}
		$allInfo = array();
		if ($key == 1) {
			$sql = "select id, benben_id, province, city, area, phone, nick_name from member";
			$command = $connection->createCommand($sql);
			$totalQuery = $command->queryAll();
			$sql = "SELECT member_id, count(*) c FROM buy where 1 = 1 ";
			if (strtotime($created_time1) > 0) {
				$sql .= ' and created_time >= '.strtotime($created_time1);
				$info['created_time1'] = $created_time1;
			}
			if (strtotime($created_time2) > 0) {
				$sql .= ' and created_time <= '.(strtotime($created_time2)+3600*24);
				$info['created_time2'] = $created_time2;
			}

			$sql .= ' group by member_id ';
			$command = $connection->createCommand($sql);
			$friendQuery = $command->queryAll();
			$memberRelation = array();
			$haveMember = array();
			if ($friendQuery) {
				foreach ($friendQuery as $k => $value) {
					$memberRelation[$value['member_id']] = $value['c'];
					$haveMember[] = $value['member_id'];
				}
			}
			foreach ($totalQuery as $value){
				$pro[] = $value['province'];
				$pro[] = $value['city'];
				$pro[] = $value['area'];
			}
				
			$pro_name = $this->allareas(implode(",", $pro));
			if($pro_name){
				foreach ($pro_name as $val){
					$pro_arr[$val['bid']] = $val['area_name'];
				}
			}
			foreach ($totalQuery as $k => $value) {
				$number = 0;
				
				if(isset($memberRelation[$value['id']])){
					$number = $memberRelation[$value['id']];
				}
				$pron = $pro_arr[$value['province']].''.$pro_arr[$value['city']];
				if ($key == 0) {
					$allInfo[] = array('id'=>$value['id'], 'benben_id'=>$value['benben_id'],'area'=>$pron, 'number'=>$number, 'phone'=>$value['phone'], 'nick_name'=>$value['nick_name']);
				}else{
					if (!in_array($value['id'], $haveMember)) {
						$allInfo[] = array('id'=>$value['id'],'benben_id'=>$value['benben_id'],'area'=>$pron, 'number'=>$number, 'phone'=>$value['phone'], 'nick_name'=>$value['nick_name']);
					}
				}
			}

		}else{
			$sql = "SELECT member_id, count(*) c FROM buy where 1 = 1 ";
			if (strtotime($created_time1) > 0) {
				$sql .= ' and created_time >= '.strtotime($created_time1);
				$info['created_time1'] = $created_time1;
			}
			if (strtotime($created_time2) > 0) {
				$sql .= ' and created_time <= '.(strtotime($created_time2)+3600*24);
				$info['created_time2'] = $created_time2;
			}
			$sql .= ' group by member_id ';
			if ($key == 2) {
				$sql .= ' having count(*) >=1 and count(*) <= 10';
			}else if ($key == 3) {
				$sql .= ' having count(*) >=11 and count(*) <= 20';
			}else if ($key == 4) {
				$sql .= ' having count(*) >=21 and count(*) < 50';
			}else if ($key == 5) {
				$sql .= ' having count(*) >=51 and count(*) <= 100';
			}else if ($key == 6) {
				$sql .= ' having count(*) >100 and count(*) <= 200';
			}else if ($key == 7) {
				$sql .= ' having count(*) >=201 and count(*) <= 500';
			}else if ($key == 8) {
				$sql .= ' having count(*) >500';
			}
			$command = $connection->createCommand($sql);
			$friendQuery = $command->queryAll();
			$memberRelation = array();
			$haveMember = array();
			if ($friendQuery) {
				foreach ($friendQuery as $k => $value) {
					$memberRelation[$value['member_id']] = $value['c'];
					$haveMember[] = $value['member_id'];
				}
			}
			$sql = "select id, benben_id, province, city, area, phone, nick_name from member where id in (".implode($haveMember, ",").")";
			$command = $connection->createCommand($sql);
			$totalQuery = $command->queryAll();
			foreach ($totalQuery as $value){
				$pro[] = $value['province'];
				$pro[] = $value['city'];
				$pro[] = $value['area'];
			}
				
			$pro_name = $this->allareas(implode(",", $pro));
			if($pro_name){
				foreach ($pro_name as $val){
					$pro_arr[$val['bid']] = $val['area_name'];
				}
			}
			foreach ($totalQuery as $k => $value) {
				$number = 0;
				
				if(isset($memberRelation[$value['id']])){
					$number = $memberRelation[$value['id']];
				}
				$pron = $pro_arr[$value['province']].''.$pro_arr[$value['city']];
				$allInfo[] = array('id'=>$value['id'], 'benben_id'=>$value['benben_id'],'area'=>$pron, 'number'=>$number, 'phone'=>$value['phone'], 'nick_name'=>$value['nick_name']);
			}

		}
		if ($allInfo) {
			$objPHPExcel = new PHPExcel();
			/*--------------设置表头信息------------------*/
			//第一个sheet
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A1', '编号')
			->setCellValue('B1', '姓名')
			->setCellValue('C1', '手机号')
			->setCellValue('D1', '奔犇号')
			->setCellValue('E1', '地区')
			->setCellValue('F1', '发帖次数')
			;
			
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
			$styleArray1 = array(
					'font' => array(
							'bold' => true,
							'color'=>array(
									'argb' => '00000000',
							),
					),
					'alignment' => array(
							'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					),
			);
			$objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray1);
			$objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($styleArray1);
			$objPHPExcel->getActiveSheet()->getStyle('C1')->applyFromArray($styleArray1);
			$objPHPExcel->getActiveSheet()->getStyle('D1')->applyFromArray($styleArray1);
			$objPHPExcel->getActiveSheet()->getStyle('E1')->applyFromArray($styleArray1);
			$objPHPExcel->getActiveSheet()->getStyle('F1')->applyFromArray($styleArray1);
			$objPHPExcel->getActiveSheet()->setTitle('我要买');      //设置sheet的名称
			$objPHPExcel->setActiveSheetIndex(0);   //设置sheet的起始位置
			
	
			$i =2;
			foreach ($allInfo as  $one){	
				$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue("A$i", $i-1)
				->setCellValue("B$i", $one['nick_name'])
				->setCellValue("C$i", $one['phone'])
				->setCellValue("D$i", $one['benben_id'])
				->setCellValue("E$i", $one['area'])
				->setCellValue("F$i", $one['number']);
				$i++;
			}
			
			ob_end_clean();
			ob_start();
			header('Content-Type: application/vnd.ms-excel;charset=utf-8');
			header('Content-Disposition:attachment;filename=' . urlencode('info' . date("YmjHis") .'.xls') . '');
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');   //通过PHPExcel_IOFactory的写函数将上面数据写出来
		
			$objWriter->save('php://output');
		}
	}


	public function actionDownloadQuote()
	{
		$str = Frame::getStringFromRequest('str');
		$code = Frame::getStringFromRequest('code');
		$key = Frame::getIntFromRequest('key');
		$type = Frame::getIntFromRequest('type');
		$created_time1 = Frame::getStringFromRequest('created_time1');
		$created_time2 = Frame::getStringFromRequest('created_time2');
		$connection = Yii::app()->db;
		if ($code != md5($str.'excel')) {
			die('unvalid');
		}
		$allInfo = array();
		if ($key == 1) {
			$sql = "select id, benben_id, province, city, area, phone, nick_name from member";
			$command = $connection->createCommand($sql);
			$totalQuery = $command->queryAll();
			$sql = "SELECT member_id, count(*) c FROM buy where is_accept = 1 ";
			if (strtotime($created_time1) > 0) {
				$sql .= ' and created_time >= '.strtotime($created_time1);
				$info['created_time1'] = $created_time1;
			}
			if (strtotime($created_time2) > 0) {
				$sql .= ' and created_time <= '.(strtotime($created_time2)+3600*24);
				$info['created_time2'] = $created_time2;
			}

			$sql .= ' group by member_id ';
			$command = $connection->createCommand($sql);
			$friendQuery = $command->queryAll();
			$memberRelation = array();
			$haveMember = array();
			if ($friendQuery) {
				foreach ($friendQuery as $k => $value) {
					$memberRelation[$value['member_id']] = $value['c'];
					$haveMember[] = $value['member_id'];
				}
			}
			foreach ($totalQuery as $value){
				$pro[] = $value['province'];
				$pro[] = $value['city'];
				$pro[] = $value['area'];
			}
				
			$pro_name = $this->allareas(implode(",", $pro));
			if($pro_name){
				foreach ($pro_name as $val){
					$pro_arr[$val['bid']] = $val['area_name'];
				}
			}
			foreach ($totalQuery as $k => $value) {
				$number = 0;
				
				if(isset($memberRelation[$value['id']])){
					$number = $memberRelation[$value['id']];
				}
				$pron = $pro_arr[$value['province']].''.$pro_arr[$value['city']];
				if ($key == 0) {
					$allInfo[] = array('id'=>$value['id'], 'benben_id'=>$value['benben_id'],'area'=>$pron, 'number'=>$number, 'phone'=>$value['phone'], 'nick_name'=>$value['nick_name']);
				}else{
					if (!in_array($value['id'], $haveMember)) {
						$allInfo[] = array('id'=>$value['id'],'benben_id'=>$value['benben_id'],'area'=>$pron, 'number'=>$number, 'phone'=>$value['phone'], 'nick_name'=>$value['nick_name']);
					}
				}
			}

		}else{
			$sql = "SELECT member_id, count(*) c FROM buy where is_accept = 1 ";
			if (strtotime($created_time1) > 0) {
				$sql .= ' and created_time >= '.strtotime($created_time1);
				$info['created_time1'] = $created_time1;
			}
			if (strtotime($created_time2) > 0) {
				$sql .= ' and created_time <= '.(strtotime($created_time2)+3600*24);
				$info['created_time2'] = $created_time2;
			}
			$sql .= ' group by member_id ';
			if ($key == 2) {
				$sql .= ' having count(*) >=1 and count(*) <= 10';
			}else if ($key == 3) {
				$sql .= ' having count(*) >=11 and count(*) <= 20';
			}else if ($key == 4) {
				$sql .= ' having count(*) >=21 and count(*) < 50';
			}else if ($key == 5) {
				$sql .= ' having count(*) >=51 and count(*) <= 100';
			}else if ($key == 6) {
				$sql .= ' having count(*) >100 and count(*) <= 200';
			}else if ($key == 7) {
				$sql .= ' having count(*) >=201 and count(*) <= 500';
			}else if ($key == 8) {
				$sql .= ' having count(*) >500';
			}
			$command = $connection->createCommand($sql);
			$friendQuery = $command->queryAll();
			$memberRelation = array();
			$haveMember = array();
			if ($friendQuery) {
				foreach ($friendQuery as $k => $value) {
					$memberRelation[$value['member_id']] = $value['c'];
					$haveMember[] = $value['member_id'];
				}
			}
			$sql = "select id, benben_id, province, city, area, phone, nick_name from member where id in (".implode($haveMember, ",").")";
			$command = $connection->createCommand($sql);
			$totalQuery = $command->queryAll();
			foreach ($totalQuery as $value){
				$pro[] = $value['province'];
				$pro[] = $value['city'];
				$pro[] = $value['area'];
			}
				
			$pro_name = $this->allareas(implode(",", $pro));
			if($pro_name){
				foreach ($pro_name as $val){
					$pro_arr[$val['bid']] = $val['area_name'];
				}
			}
			foreach ($totalQuery as $k => $value) {
				$number = 0;
				
				if(isset($memberRelation[$value['id']])){
					$number = $memberRelation[$value['id']];
				}
				$pron = $pro_arr[$value['province']].''.$pro_arr[$value['city']];
				$allInfo[] = array('id'=>$value['id'], 'benben_id'=>$value['benben_id'],'area'=>$pron, 'number'=>$number, 'phone'=>$value['phone'], 'nick_name'=>$value['nick_name']);
			}

		}
		if ($allInfo) {
			$objPHPExcel = new PHPExcel();
			/*--------------设置表头信息------------------*/
			//第一个sheet
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A1', '编号')
			->setCellValue('B1', '姓名')
			->setCellValue('C1', '手机号')
			->setCellValue('D1', '奔犇号')
			->setCellValue('E1', '地区')
			->setCellValue('F1', '接受报价次数')
			;
			
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
			$styleArray1 = array(
					'font' => array(
							'bold' => true,
							'color'=>array(
									'argb' => '00000000',
							),
					),
					'alignment' => array(
							'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					),
			);
			$objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray1);
			$objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($styleArray1);
			$objPHPExcel->getActiveSheet()->getStyle('C1')->applyFromArray($styleArray1);
			$objPHPExcel->getActiveSheet()->getStyle('D1')->applyFromArray($styleArray1);
			$objPHPExcel->getActiveSheet()->getStyle('E1')->applyFromArray($styleArray1);
			$objPHPExcel->getActiveSheet()->getStyle('F1')->applyFromArray($styleArray1);
			$objPHPExcel->getActiveSheet()->setTitle('我要买接受报价');      //设置sheet的名称
			$objPHPExcel->setActiveSheetIndex(0);   //设置sheet的起始位置
			
	
			$i =2;
			foreach ($allInfo as  $one){	
				$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue("A$i", $i-1)
				->setCellValue("B$i", $one['nick_name'])
				->setCellValue("C$i", $one['phone'])
				->setCellValue("D$i", $one['benben_id'])
				->setCellValue("E$i", $one['area'])
				->setCellValue("F$i", $one['number']);
				$i++;
			}
			
			ob_end_clean();
			ob_start();
			header('Content-Type: application/vnd.ms-excel;charset=utf-8');
			header('Content-Disposition:attachment;filename=' . urlencode('info' . date("YmjHis") .'.xls') . '');
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');   //通过PHPExcel_IOFactory的写函数将上面数据写出来
		
			$objWriter->save('php://output');
		}
	}

	public function actionDownloadCreation()
	{
		$str = Frame::getStringFromRequest('str');
		$code = Frame::getStringFromRequest('code');
		$key = Frame::getIntFromRequest('key');
		$type = Frame::getIntFromRequest('type');
		$created_time1 = Frame::getStringFromRequest('created_time1');
		$created_time2 = Frame::getStringFromRequest('created_time2');
		$connection = Yii::app()->db;
		if ($code != md5($str.'excel')) {
			die('unvalid');
		}
		$allInfo = array();
		if ($key == 1) {
			$sql = "select id, benben_id, province, city, area from member";
			$command = $connection->createCommand($sql);
			$totalQuery = $command->queryAll();
			$sql = "SELECT member_id, count(*) c FROM creation where 1 = 1 ";
			if (strtotime($created_time1) > 0) {
				$sql .= ' and created_time >= '.strtotime($created_time1);
				$info['created_time1'] = $created_time1;
			}
			if (strtotime($created_time2) > 0) {
				$sql .= ' and created_time <= '.(strtotime($created_time2)+3600*24);
				$info['created_time2'] = $created_time2;
			}

			$sql .= ' group by member_id ';
			$command = $connection->createCommand($sql);
			$friendQuery = $command->queryAll();
			$memberRelation = array();
			$haveMember = array();
			if ($friendQuery) {
				foreach ($friendQuery as $k => $value) {
					$memberRelation[$value['member_id']] = $value['c'];
					$haveMember[] = $value['member_id'];
				}
			}
			foreach ($totalQuery as $value){
				$pro[] = $value['province'];
				$pro[] = $value['city'];
				$pro[] = $value['area'];
			}
				
			$pro_name = $this->allareas(implode(",", $pro));
			if($pro_name){
				foreach ($pro_name as $val){
					$pro_arr[$val['bid']] = $val['area_name'];
				}
			}
			foreach ($totalQuery as $k => $value) {
				$number = 0;
				
				if(isset($memberRelation[$value['id']])){
					$number = $memberRelation[$value['id']];
				}
				$pron = $pro_arr[$value['province']].''.$pro_arr[$value['city']];
				if ($key == 0) {
					$allInfo[] = array('id'=>$value['id'], 'benben_id'=>$value['benben_id'],'area'=>$pron, 'number'=>$number);
				}else{
					if (!in_array($value['id'], $haveMember)) {
						$allInfo[] = array('id'=>$value['id'],'benben_id'=>$value['benben_id'],'area'=>$pron, 'number'=>$number);
					}
				}
			}

		}else{
			$sql = "SELECT member_id, count(*) c FROM creation where 1 = 1 ";
			if (strtotime($created_time1) > 0) {
				$sql .= ' and created_time >= '.strtotime($created_time1);
				$info['created_time1'] = $created_time1;
			}
			if (strtotime($created_time2) > 0) {
				$sql .= ' and created_time <= '.(strtotime($created_time2)+3600*24);
				$info['created_time2'] = $created_time2;
			}
			$sql .= ' group by member_id ';
			if ($key == 2) {
				$sql .= ' having count(*) >=1 and count(*) <= 10';
			}else if ($key == 3) {
				$sql .= ' having count(*) >=11 and count(*) <= 20';
			}else if ($key == 4) {
				$sql .= ' having count(*) >=21 and count(*) < 50';
			}else if ($key == 5) {
				$sql .= ' having count(*) >=51 and count(*) <= 100';
			}else if ($key == 6) {
				$sql .= ' having count(*) >100 and count(*) <= 200';
			}else if ($key == 7) {
				$sql .= ' having count(*) >=201 and count(*) <= 500';
			}else if ($key == 8) {
				$sql .= ' having count(*) >500';
			}
			$command = $connection->createCommand($sql);
			$friendQuery = $command->queryAll();
			$memberRelation = array();
			$haveMember = array();
			if ($friendQuery) {
				foreach ($friendQuery as $k => $value) {
					$memberRelation[$value['member_id']] = $value['c'];
					$haveMember[] = $value['member_id'];
				}
			}
			$sql = "select id, benben_id, province, city, area from member where id in (".implode($haveMember, ",").")";
			$command = $connection->createCommand($sql);
			$totalQuery = $command->queryAll();
			foreach ($totalQuery as $value){
				$pro[] = $value['province'];
				$pro[] = $value['city'];
				$pro[] = $value['area'];
			}
				
			$pro_name = $this->allareas(implode(",", $pro));
			if($pro_name){
				foreach ($pro_name as $val){
					$pro_arr[$val['bid']] = $val['area_name'];
				}
			}
			foreach ($totalQuery as $k => $value) {
				$number = 0;
				
				if(isset($memberRelation[$value['id']])){
					$number = $memberRelation[$value['id']];
				}
				$pron = $pro_arr[$value['province']].''.$pro_arr[$value['city']];
				$allInfo[] = array('id'=>$value['id'], 'benben_id'=>$value['benben_id'],'area'=>$pron, 'number'=>$number);
			}

		}
		if ($allInfo) {
			$objPHPExcel = new PHPExcel();
			/*--------------设置表头信息------------------*/
			//第一个sheet
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A1', '编号')
			->setCellValue('B1', '奔犇号')
			->setCellValue('C1', '地区')
			->setCellValue('D1', '发帖次数')
			;
			
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
			$styleArray1 = array(
					'font' => array(
							'bold' => true,
							'color'=>array(
									'argb' => '00000000',
							),
					),
					'alignment' => array(
							'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					),
			);
			$objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray1);
			$objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($styleArray1);
			$objPHPExcel->getActiveSheet()->getStyle('C1')->applyFromArray($styleArray1);
			$objPHPExcel->getActiveSheet()->getStyle('D1')->applyFromArray($styleArray1);
			$objPHPExcel->getActiveSheet()->setTitle('微创作');      //设置sheet的名称
			$objPHPExcel->setActiveSheetIndex(0);   //设置sheet的起始位置
			
	
			$i =2;
			foreach ($allInfo as  $one){	
				$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue("A$i", $i-1)
				->setCellValue("B$i", $one['benben_id'])
				->setCellValue("C$i", $one['area'])
				->setCellValue("D$i", $one['number']);
				$i++;
			}
			
			ob_end_clean();
			ob_start();
			header('Content-Type: application/vnd.ms-excel;charset=utf-8');
			header('Content-Disposition:attachment;filename=' . urlencode('info' . date("YmjHis") .'.xls') . '');
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');   //通过PHPExcel_IOFactory的写函数将上面数据写出来
		
			$objWriter->save('php://output');
		}
	}


	public function actionDownload(){
		$str = Frame::getStringFromRequest('str');
		$code = Frame::getStringFromRequest('code');
		$key = Frame::getIntFromRequest('key');
		$type = Frame::getIntFromRequest('type');
		$created_time1 = Frame::getStringFromRequest('created_time1');
		$created_time2 = Frame::getStringFromRequest('created_time2');
		$connection = Yii::app()->db;
		if ($code != md5($str.'excel')) {
			die('unvalid');
		}
		if ($key == 0) {
			if ($type == 1) {
				# code...
			}
			
		}else if($key == 1){
			if ($type == 1) {
				$sql = "SELECT store_id as item_id FROM quote where 1 = 1 ";
			}else{
				$sql = "SELECT number_train_id as item_id  FROM number_train_collect where 1 = 1 ";
			}
			
			if (strtotime($created_time1) > 0) {
				$sql .= ' and created_time >= '.strtotime($created_time1);
				$info['created_time1'] = $created_time1;
			}
			if (strtotime($created_time2) > 0) {
				$sql .= ' and created_time <= '.(strtotime($created_time2)+3600*24);
				$info['created_time2'] = $created_time2;
			}
			$sql .= ' group by item_id ';
			$command = $connection->createCommand($sql);
			$totalQuery = $command->queryAll();
			$totalId = array();
			if ($totalQuery) {
				foreach($totalQuery as $each){
					$totalId[] = $each['item_id'];
				}
				$finalsql = "select  t.short_name, industry.name as iname, t.province, t.city, t.area,  t.phone, member.nick_name as mname, t.created_time from number_train t  left join member on member.id = t.member_id
								left join industry on industry.id = t.industry where t.id not in (".implode(",", $totalId).")";
			}else{
				$finalsql = "select t.short_name, industry.name as iname, t.province, t.city, t.area,  t.phone, member.nick_name as mname, t.created_time from number_train t left join member on member.id = t.member_id
								left join industry on industry.id = t.industry";
			}

		}else if($str){
			$totalId = explode("|", $str);
			if (count($totalId) > 0) {
				$finalsql = "select  t.short_name, industry.name as iname, t.province, t.city, t.area,  t.phone, member.nick_name as mname, t.created_time from number_train t left join member on member.id = t.member_id
								left join industry on industry.id = t.industry where t.id in (".implode(",", $totalId).")";
			}else{
				$finalsql = "select t.short_name, industry.name as iname, t.province, t.city, t.area,  t.phone, member.nick_name as mname, t.created_time from number_train t left join member on member.id = t.member_id
								left join industry on industry.id = t.industry";
			}

		}
		if ($finalsql) {
			$command = $connection->createCommand($finalsql);
			$allInfo = $command->queryAll();
		}
		
		if ($allInfo) {
			$objPHPExcel = new PHPExcel();
			/*--------------设置表头信息------------------*/
			//第一个sheet
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A1', '编号')
			->setCellValue('B1', '简称')
			->setCellValue('C1', '地区')
			->setCellValue('D1', '行业')
			->setCellValue('E1', '创建人')
			->setCellValue('F1', '手机号')
			->setCellValue('G1', '创建日期')
			;
			
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
			$styleArray1 = array(
					'font' => array(
							'bold' => true,
							'color'=>array(
									'argb' => '00000000',
							),
					),
					'alignment' => array(
							'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					),
			);
			$objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray1);
			$objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($styleArray1);
			$objPHPExcel->getActiveSheet()->getStyle('C1')->applyFromArray($styleArray1);
			$objPHPExcel->getActiveSheet()->getStyle('D1')->applyFromArray($styleArray1);
			$objPHPExcel->getActiveSheet()->getStyle('E1')->applyFromArray($styleArray1);
			$objPHPExcel->getActiveSheet()->getStyle('F1')->applyFromArray($styleArray1);
			$objPHPExcel->getActiveSheet()->getStyle('G1')->applyFromArray($styleArray1);
			$objPHPExcel->getActiveSheet()->setTitle('直通车');      //设置sheet的名称
			$objPHPExcel->setActiveSheetIndex(0);   //设置sheet的起始位置
			$pro = array();
			$pro_arr = array();
			foreach ($allInfo as $value){
				$pro[] = $value['province'];
				$pro[] = $value['city'];
				$pro[] = $value['area'];
			}
				
			$pro_name = $this->allareas(implode(",", $pro));
			if($pro_name){
				foreach ($pro_name as $val){
					$pro_arr[$val['bid']] = $val['area_name'];
				}
			}
	
			$i =2;
			foreach ($allInfo as  $one){
				// 			var_dump($one);exit();
				$pron = $pro_arr[$one['province']].''.$pro_arr[$one['city']];
					
				$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue("A$i", $i-1)
				->setCellValue("B$i", $one['short_name'])
				->setCellValue("C$i", $pron)
				->setCellValue("D$i", $one['iname'])
				->setCellValue("E$i", $one['mname'])				
				->setCellValue("F$i", $one['phone'])
				->setCellValue("G$i", date("Y-m-d H:i:s", $one['created_time']));
				//$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(100);
				$i++;
			}
			
			ob_end_clean();
			ob_start();
			header('Content-Type: application/vnd.ms-excel;charset=utf-8');
			header('Content-Disposition:attachment;filename=' . urlencode('info' . date("YmjHis") .'.xls') . '');
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');   //通过PHPExcel_IOFactory的写函数将上面数据写出来
		
			$objWriter->save('php://output');
		}
	}



}