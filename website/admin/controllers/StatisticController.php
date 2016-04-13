<?php
Yii::$enableIncludePath = false;
define('__ROOT__',dirname(dirname(__FILE__)));
require_once (__ROOT__.'/PHPExcel/PHPExcel.php');
class StatisticController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/admin';
	
	/**
	 * @var int the define the index for the menu
	 */
	 
	 public $menuIndex = 11;

	/**
	 * @var int the define the bx for multibx
	 */
	public $ownbx=0;

	/**
	 * @return mixed
	 */
	protected function setOwnbx()
	{
		return $this->ownbx = Yii::app()->user->getState('userInfo')->enterprise_id;
	}


	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionMember()
	{
		$this->insert_log(11);
		$connection = Yii::app()->db;
		//总人数
		$sql = "select count(*) as c from member where id_enable = 1";
		$command = $connection->createCommand($sql);
		$totalQuery = $command->queryAll();
		$totalNumber = $totalQuery[0]['c'];

		//统计手机型号
		$asql = "SELECT count(*) as c , phone_model FROM member where id_enable = 1  group by phone_model order by c desc limit 10 ";
		$command = $connection->createCommand($asql);
		$result = $command->queryAll();
		$phoneModel = array();
		$otherNumber = 0;
		foreach ($result as $key => $value) {
			if ($value['phone_model']) {
				$phoneModel[] = $value['phone_model'];	
				$PhoneValue[] = $value['c'];
			}else{
				$otherNumber += $value['c'];
			}
			
  		}
  		$phoneModel[] = "其它";
  		$PhoneValue[] = $otherNumber;

  		//统计有身份证的人数
  		$hsql = "select member_id from apply_complete group by member_id";
  		$command = $connection->createCommand($hsql);
		$result = $command->queryAll();
		$haveId = count($result);

		//统计通讯录中奔犇好友
		$csql = "select count(a.id) c, b.member_id from group_contact_phone a left join group_contact_info b on a.contact_info_id = b.id where a.is_benben > 0 group by b.member_id";
		$command = $connection->createCommand($csql);
		$friendQuery = $command->queryAll();
		$friendInfo = array(
			array('number'=>0, 'name'=>'0-5'),
			array('number'=>0, 'name'=>'6-10'),
			array('number'=>0, 'name'=>'11-20'),
			array('number'=>0, 'name'=>'21-50'),
			array('number'=>0, 'name'=>'50以上')
		);
		if ($friendQuery) {
			foreach ($friendQuery as $key => $value) {
				if ($value['c'] <= 5) {
					$friendInfo[0]['number']++;
					$friendInfo[0]['info'][] = $value['member_id'];
				}else if ($value['c'] <= 10){
					$friendInfo[1]['number']++;
					$friendInfo[1]['info'][] = $value['member_id'];
				}else if ($value['c'] <= 20){
					$friendInfo[2]['number']++;
					$friendInfo[2]['info'][] = $value['member_id'];
				}else if ($value['c'] <= 50){
					$friendInfo[3]['number']++;
					$friendInfo[3]['info'][] = $value['member_id'];
				}else {
					$friendInfo[4]['number']++;
					$friendInfo[4]['info'][] = $value['member_id'];
				}
			}
		}
		$friendInfo[0]['number'] += ($totalNumber - count($friendQuery));


		//统计邀请加入奔犇人数
		$isql = "select count(*) c, member_id from benben_invite_log group by member_id";
		$command = $connection->createCommand($isql);
		$inviteQuery = $command->queryAll();
		$inviteInfo = array(
			array('number'=>0, 'name'=>'0-10'),
			array('number'=>0, 'name'=>'11-20'),
			array('number'=>0, 'name'=>'21-40'),
			array('number'=>0, 'name'=>'40以上')
		);
		if ($inviteQuery) {
			foreach ($inviteQuery as $key => $value) {
				if ($value['c'] <= 10) {
					$inviteInfo[0]['number']++;
					$inviteInfo[0]['info'][] = $value['member_id'];
				}else if ($value['c'] <= 20){
					$inviteInfo[1]['number']++;
					$inviteInfo[1]['info'][] = $value['member_id'];
				}else if ($value['c'] <= 40){
					$inviteInfo[2]['number']++;
					$inviteInfo[2]['info'][] = $value['member_id'];
				}else {
					$inviteInfo[3]['number']++;
					$inviteInfo[3]['info'][] = $value['member_id'];
				}
			}
		}
		$inviteInfo[0]['number'] += ($totalNumber - count($inviteQuery));
  		

		$this->render('member',array(
			'totalNumber'=>$totalNumber,
			'haveId'=>$haveId,
			'phone'=>$phoneModel,
			'phonevalue' => $PhoneValue,
			'friendInfo' => $friendInfo,
			'inviteInfo' => $inviteInfo
		));
	

	}

	public function actionBx()
	{
		$this->setOwnbx();
		$this->menuIndex = 24;
		$this->insert_log(24);
		$connection = Yii::app()->db;

		//总人数
		$sql = "select count(*) as c from member where id_enable = 1";
		$command = $connection->createCommand($sql);
		$totalQuery = $command->queryAll();
		$totalNumber = $totalQuery[0]['c'];

		//主动加入
		$asql = "SELECT count(*) c FROM bxapply a left join apply_complete b on a.id = b.apply_id where a.status = 3 and b.id > 0 and a.enterprise_id=".$this->ownbx;
		$command = $connection->createCommand($asql);
		$result1 = $command->queryAll();
		$selfAdd = $result1[0]['c'];
		//全部人数
		$asql = "SELECT count(*) c FROM bxapply where status = 3 and enterprise_id=".$this->ownbx;
		$command = $connection->createCommand($asql);
		$result2 = $command->queryAll();
		$allAdd = $result2[0]['c'];

		//百姓网用户是否是奔犇用户
		$isql = "select distinct(b.id) from bxapply a left join member b on a.phone = b.phone where a.status = 3 and a.enterprise_id=".$this->ownbx." and b.id>0 and id_enable = 1";
		$command = $connection->createCommand($isql);
		$resulti = $command->queryAll();
		$isBenben = count($resulti);
		$benbenMember = array();
		foreach($resulti as $e){
			$benbenMember[] = $e['id'];
		}

		//所有本百姓网用户
		$allOwnBx=Bxapply::model()->findAll("enterprise_id=".$this->ownbx." and short_phone>0 and status=3");
		$bxNo=array();
		if($allOwnBx){
			foreach ($allOwnBx as $ak=>$av){
				$bxNo[]=$av['short_phone'];
			}
		}

		//统计通讯录中有百姓网的数量
		if (count($benbenMember) && count($bxNo)) {
			$csql = "select count(a.id) c, b.member_id from group_contact_phone a 
			left join group_contact_info b on a.contact_info_id = b.id
			where a.is_baixing in (".implode(",",$bxNo).") and a.is_baixing > 0 and b.member_id  in (".implode(",", $benbenMember).") group by b.member_id";
			$command = $connection->createCommand($csql);
			$friendQuery = $command->queryAll();
		}

		$friendInfo = array(
			array('number'=>0, 'name'=>'0'),
			array('number'=>0, 'name'=>'1-5'),
			array('number'=>0, 'name'=>'6-10'),
			array('number'=>0, 'name'=>'11-20'),
			array('number'=>0, 'name'=>'21-30'),
			array('number'=>0, 'name'=>'31-40'),
			array('number'=>0, 'name'=>'41-50'),
			array('number'=>0, 'name'=>'50以上')
		);
		if ($friendQuery) {
			foreach ($friendQuery as $key => $value) {
				if ($value['c'] <= 5) {
					$friendInfo[1]['number']++;
					$friendInfo[1]['info'][] = $value['member_id'];
				}else if ($value['c'] <= 10){
					$friendInfo[2]['number']++;
					$friendInfo[2]['info'][] = $value['member_id'];
				}else if ($value['c'] <= 20){
					$friendInfo[3]['number']++;
					$friendInfo[3]['info'][] = $value['member_id'];
				}else if ($value['c'] <= 30){
					$friendInfo[4]['number']++;
					$friendInfo[4]['info'][] = $value['member_id'];
				}else if ($value['c'] <= 40){
					$friendInfo[5]['number']++;
					$friendInfo[5]['info'][] = $value['member_id'];
				}else if ($value['c'] <= 50){
					$friendInfo[6]['number']++;
					$friendInfo[6]['info'][] = $value['member_id'];
				}else {
					$friendInfo[7]['number']++;
					$friendInfo[7]['info'][] = $value['member_id'];
				}
			}
		}
		$friendInfo[0]['number'] += ($isBenben - count($friendQuery));

		//统计在网时长 小于3个月的人数
		$month = 3600*24*90;
		$dateline = time()-$month;
		$asql = "SELECT count(*) c FROM bxapply where status = 3 and enterprise_id=".$this->ownbx." and join_time > ".$dateline;
		$command = $connection->createCommand($asql);
		$query1 = $command->queryAll();
		$asql = "SELECT count(*) c FROM bxapply where status = 4 and enterprise_id=".$this->ownbx." and (cancel_time - join_time) < ".$month;
		$command = $connection->createCommand($asql);
		$query2 = $command->queryAll();
		$lessTime = $query1[0]['c'] + $query2[0]['c'];

		//统计在网时长 大于3个月的人数
		$asql = "SELECT count(*) c FROM bxapply where status = 3 and enterprise_id=".$this->ownbx." and join_time <= ".$dateline;
		$command = $connection->createCommand($asql);
		$query3 = $command->queryAll();
		$asql = "SELECT count(*) c FROM bxapply where status = 4 and enterprise_id=".$this->ownbx." and (cancel_time - join_time) >= ".$month;
		$command = $connection->createCommand($asql);
		$query4 = $command->queryAll();
		$moreTime = $query3[0]['c'] + $query4[0]['c'];
		


	

		$this->render('bx',array(
			'totalNumber'=>$totalNumber,
			'status'=>$statusArr,
			'invite'=>array('["主动加入", '.$result1[0]['c'].']', '["邀请加入", '.($result2[0]['c']-$result1[0]['c']).']', ),
			'isBenben' => $isBenben,
			'selfAdd' => $selfAdd,
			'allAdd' => $allAdd,
			'lessTime' => $lessTime,
			'moreTime' => $moreTime,
			'friendInfo' => $friendInfo
		));
		

	}

	public function actionDownload()
	{
		ini_set('memory_limit','1024M');
		$connection = Yii::app()->db;
		$type = intval($_GET['type']);
		$key = intval($_GET['key']);
		$this->setOwnbx();
		if($type == 2){
			if ($key == 0) {
				$asql = "SELECT a.phone, a.name, a.short_phone,a.province, a.city, a.area FROM bxapply a left join apply_complete b on a.id = b.apply_id where a.enterprise_id=".$this->ownbx." and a.status = 3 and b.id > 0";
			
			}else{
				$asql = "SELECT a.phone, a.name, a.short_phone, a.province, a.city, a.area FROM bxapply a left join apply_complete b on a.id = b.apply_id where a.enterprise_id=".$this->ownbx." and a.status = 3 and b.id is null";
			}
			
			$command = $connection->createCommand($asql);
			$result1 = $command->queryAll();
			if (!$result1) {
				exit();
			}
			$areaId = array();
			foreach ($result1 as $e) {
				if ($e['province']) {$areaId[] = $e['province'];}
				if ($e['city']) {$areaId[] = $e['city'];}
				if ($e['area']) {$areaId[] = $e['area'];}
			}
			$areaInfo = $this->getArea($areaId);
			$title = "百姓网奔犇用户";
			$filename = "bxmember_benben";
			$objPHPExcel = new PHPExcel();
			/*--------------设置表头信息------------------*/
			//第一个sheet
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A1', '姓名')
			->setCellValue('B1', '手机号码')
			->setCellValue('C1', '百姓网号')
			->setCellValue('D1', '省')
			->setCellValue('E1', '市')
			->setCellValue('F1', '区');
			
			if(!empty($result1)){			
				$i =2;
				foreach ($result1 as  $one){
					$cP = ''; $cC = ''; $cA = '';
					if ($one['province'] && isset($areaInfo[$one['province']])) {
						$cP = $areaInfo[$one['province']];
					}
					if ($one['city'] && isset($areaInfo[$one['city']])) {
						$cC = $areaInfo[$one['city']];
					}
					if ($one['area'] && isset($areaInfo[$one['area']])) {
						$cA = $areaInfo[$one['area']];
					}
					$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue("A$i", $one['name'])
					->setCellValue("B$i", $one['phone'])
					->setCellValue("C$i", $one['short_phone'])
					->setCellValue("D$i", $cP)
					->setCellValue("E$i", $cC)
					->setCellValue("F$i", $cA);
					//$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(100);
					$i++;
				}
			}
			$objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
			
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
			
			$objPHPExcel->getActiveSheet()->setTitle($title);      //设置sheet的名称
			$objPHPExcel->setActiveSheetIndex(0);   //设置sheet的起始位置
			
			ob_end_clean();
			ob_start();
			header('Content-Type: application/vnd.ms-excel;charset=utf-8');
			header('Content-Disposition:attachment;filename=' . urlencode($filename . date("YmjHis") .'.xls') . '');
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');   //通过PHPExcel_IOFactory的写函数将上面数据写出来
			
			$objWriter->save('php://output');
			

		}else if($type == 3){
			//百姓网用户是否是奔犇用户
			$isql = "select a.phone,a.name,a.short_phone,b.id, b.benben_id, a.member_id, c.nick_name sname, c.phone sphone,a.province,a.city,a.area from bxapply a left join member b on a.phone = b.phone left join member c on a.member_id = c.id where a.enterprise_id=".$this->ownbx." and a.status = 3";
			if($key == 1){
				$isql .=" and b.id>0 and b.id_enable = 1 group by b.id";
			}
			$command = $connection->createCommand($isql);
			$resulti = $command->queryAll();
			if($key){
				$users = $resulti;
				$title = "百姓网奔犇用户";
				$filename = "bxmember_benben";
			}else{				
				foreach ($resulti as $value){
					if($value['id'] == NULL){
						$users[] = $value;
					}
				}
				$title = "百姓网非奔犇用户";
				$filename = "bxmember_notbenben";
			}
			$areaId = array();
			foreach ($resulti as $e) {
				if ($e['province']) {$areaId[] = $e['province'];}
				if ($e['city']) {$areaId[] = $e['city'];}
				if ($e['area']) {$areaId[] = $e['area'];}
			}
			$areaInfo = $this->getArea($areaId);

			$objPHPExcel = new PHPExcel();
			/*--------------设置表头信息------------------*/
			//第一个sheet
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A1', '姓名')
			->setCellValue('B1', '手机号码')
			->setCellValue('C1', '百姓网号')
			->setCellValue('D1', '省')
			->setCellValue('E1', '市')
			->setCellValue('F1', '区')
			->setCellValue('G1', '奔犇号')->setCellValue('H1', '提交人')->setCellValue('I1', '提交人号码');
			
			if(!empty($users)){			
				$i =2;
				foreach ($users as  $one){
					$cP = ''; $cC = ''; $cA = '';
					if ($one['province'] && isset($areaInfo[$one['province']])) {
						$cP = $areaInfo[$one['province']];
					}
					if ($one['city'] && isset($areaInfo[$one['city']])) {
						$cC = $areaInfo[$one['city']];
					}
					if ($one['area'] && isset($areaInfo[$one['area']])) {
						$cA = $areaInfo[$one['area']];
					}
					$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue("A$i", $one['name'])
					->setCellValue("B$i", $one['phone'])
					->setCellValue("C$i", $one['short_phone'])
					->setCellValue("D$i", $cP)
					->setCellValue("E$i", $cC)
					->setCellValue("F$i", $cA)
					->setCellValue("G$i", $one['benben_id'])
					->setCellValue("H$i", $one['sname'])
					->setCellValue("I$i", $one['sphone']);
					//$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(100);
					$i++;
				}
			}
			$objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
			
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
			
			$objPHPExcel->getActiveSheet()->setTitle($title);      //设置sheet的名称
			$objPHPExcel->setActiveSheetIndex(0);   //设置sheet的起始位置
			
			ob_end_clean();
			ob_start();
			header('Content-Type: application/vnd.ms-excel;charset=utf-8');
			header('Content-Disposition:attachment;filename=' . urlencode($filename . date("YmjHis") .'.xls') . '');
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');   //通过PHPExcel_IOFactory的写函数将上面数据写出来
			
			$objWriter->save('php://output');
		}
		
		if($type == 4){
			//统计在网时长 小于3个月的人数
			$month = 3600*24*90;
			$dateline = time()-$month;
			$asql0 = "SELECT id,name,phone,short_phone,cancel_time,created_time , province, city, area FROM bxapply where enterprise_id=".$this->ownbx." and status = 3 and join_time > ".$dateline;
			$asql = "SELECT a.id,a.name,a.phone,a.short_phone,a.cancel_time,b.reason,b.created_time , a.province, a.city, a.area FROM bxapply a inner join bxapply_record b on a.id = b.apply_id
			where a.enterprise_id=".$this->ownbx." and a.status = 4 and (a.cancel_time - a.join_time) < {$month} and b.status = 4 order by b.created_time desc ";
			if($key==1){//统计在网时长 大于3个月的人数
				$asql0 = "SELECT id,name,phone,short_phone,cancel_time,created_time, province, city, area FROM bxapply where enterprise_id=".$this->ownbx." and status = 3 and join_time <= ".$dateline;
				$asql = "SELECT a.id,a.name,a.phone,a.short_phone,a.cancel_time,b.reason,b.created_time, a.province, a.city, a.area  FROM bxapply a inner join bxapply_record b on a.id = b.apply_id
				where a.enterprise_id=".$this->ownbx." and a.status = 4 and (a.cancel_time - a.join_time) >= {$month} and b.status = 4 order by b.created_time desc ";
			}
			
			$command = $connection->createCommand($asql0);
			$query1 = $command->queryAll();
						
			$command = $connection->createCommand($asql);
			$query2 = $command->queryAll();
			$re = array();
			foreach ($query2 as $va){
				if($re[$va['id']]){
					continue;
				}
				$re[$va['id']] = $va;
			}
			//var_dump($query1);
			//var_dump($query2);//exit();
			$users = array_merge($query1,$re);
			//var_dump($re);exit();
			if($key){				
				$title = "3个月以上时长百姓网用户";
				$filename = "bxmember_more";
			}else{				
				$title = "0-3个月时长百姓网用户";
				$filename = "bxmember_less";
			}
			$areaId = array();
			foreach ($users as $e) {
				if ($e['province']) {$areaId[] = $e['province'];}
				if ($e['city']) {$areaId[] = $e['city'];}
				if ($e['area']) {$areaId[] = $e['area'];}
			}
			$areaInfo = $this->getArea($areaId);

			$objPHPExcel = new PHPExcel();
			/*--------------设置表头信息------------------*/
			//第一个sheet
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A1', '姓名')
			->setCellValue('B1', '手机号码')
			->setCellValue('C1', '短号')
			->setCellValue('D1', '申请时间')
			->setCellValue('E1', '撤销原因')
			->setCellValue('F1', '撤销时间')
			->setCellValue('G1', '撤销时间')
			->setCellValue('H1', '撤销时间')
			->setCellValue('I1', '撤销时间');
				
			if(!empty($users)){
				$i =2;
				foreach ($users as  $one){
					$cP = ''; $cC = ''; $cA = '';
					if ($one['province'] && isset($areaInfo[$one['province']])) {
						$cP = $areaInfo[$one['province']];
					}
					if ($one['city'] && isset($areaInfo[$one['city']])) {
						$cC = $areaInfo[$one['city']];
					}
					if ($one['area'] && isset($areaInfo[$one['area']])) {
						$cA = $areaInfo[$one['area']];
					}
					$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue("A$i", $one['name'])
					->setCellValue("B$i", $one['phone'])
					->setCellValue("C$i", $one['short_phone'])
					->setCellValue("D$i", date("Y-m-d H:i:s",$one['created_time']))
					->setCellValue("E$i", $one['reason'])
					->setCellValue("F$i", $one['cancel_time']?date("Y-m-d H:i:s",$one['cancel_time']):"")
					->setCellValue("G$i", $cP)
					->setCellValue("H$i", $cC)
					->setCellValue("I$i", $cA);
					//$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(100);
					$i++;
				}
			}
			$objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
				
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
				
			$objPHPExcel->getActiveSheet()->setTitle($title);      //设置sheet的名称
			$objPHPExcel->setActiveSheetIndex(0);   //设置sheet的起始位置
				
			ob_end_clean();
			ob_start();
			header('Content-Type: application/vnd.ms-excel;charset=utf-8');
			header('Content-Disposition:attachment;filename=' . urlencode($filename . date("YmjHis") .'.xls') . '');
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');   //通过PHPExcel_IOFactory的写函数将上面数据写出来
				
			$objWriter->save('php://output');
		}
		ini_set('memory_limit','128M');
		/*if ($type == 1) {
			$csql = "select count(a.id) c, b.member_id from group_contact_phone a left join group_contact_info b on a.contact_info_id = b.id where a.is_baixing > 0 ";
			switch ($key) {
				case '0':
					$csql .= " and c <=5 ";break;
				case '2':
					$csql .= " and c <=10 and c > 5 ";break;
				case '3':
					$csql .= " and c <=20 and c > 11 ";break;
				case '4':
					$csql .= " and c <=35 and c > 21 ";break;
				case '5':
					$csql .= " and c <=50 and c > 36 ";break;
				case '6':
					$csql .= " and c > 50 ";break;
			}
			$csql .= " group by b.member_id";
			$command = $connection->createCommand($csql);
			$friendQuery = $command->queryAll();
			$friendInfo = array();
			$memberId = array();
			if ($friendQuery) {
				foreach ($friendQuery as $key => $value) {
					$friendInfo[$value['member_id']] = $value['c'];
					$memberId[] = $value['member_id'];
				}
			}
		}*/

	}

	public function actionDetail()
	{
		$this->menuIndex = 24;
		$connection = Yii::app()->db;
		$this->setOwnbx();
		$keyValue = intval($_GET['key']);
		$type = intval($_GET['type']);
		$download = intval($_GET['download']);
		//百姓网用户是否是奔犇用户
		$isql = "select distinct(b.id) from bxapply a left join member b on a.phone = b.phone where a.status = 3 and a.enterprise_id=".$this->ownbx." and b.id>0 and b.benben_id>0 and id_enable = 1";
		$command = $connection->createCommand($isql);
		$resulti = $command->queryAll();
		$isBenben = count($resulti);
		$benbenMember = array();
		foreach($resulti as $e){
			$benbenMember[] = $e['id'];
		}

		//统计通讯录中有百姓网的数量
		// $csql = "select count(a.id) c, b.member_id from group_contact_phone a left join group_contact_info b on a.contact_info_id = b.id where a.is_baixing > 0 and b.member_id  in (".implode(",", $benbenMember).") group by b.member_id";

		//所有本百姓网用户
		$allOwnBx=Bxapply::model()->findAll("enterprise_id=".$this->ownbx." and short_phone>0 and status=3");
		$bxNo=array();
		if($allOwnBx){
			foreach ($allOwnBx as $ak=>$av){
				$bxNo[]=$av['short_phone'];
			}
		}

		$csql = "select count(a.id) c, b.member_id,a.is_baixing from group_contact_phone a left join group_contact_info b on a.contact_info_id = b.id where a.is_baixing > 0 and a.is_baixing in (".implode(",",$bxNo).") and b.member_id  in (".implode(",", $benbenMember).") ";
		$csql .= ' group by b.member_id order by c desc';
		$memberCount = array();
		$command = $connection->createCommand($csql);
		$friendQuery = $command->queryAll();
		$totalNumber = 0;
		$friendInfo = array();
		$haveMemberId = array();
		$haveInfoMember = array();

		if ($friendQuery) {
			foreach ($friendQuery as $key => $value) {
				$memberCount[$value['member_id']] = $value['c'];
				$haveMemberId[] = $value['member_id'];
				if ($value['c'] <= 5) {
					$friendInfo[0]['number']++;
					$friendInfo[0]['info'][] = $value['member_id'];
				}else if ($value['c'] <= 10){
					$friendInfo[1]['number']++;
					$friendInfo[1]['info'][] = $value['member_id'];
				}else if ($value['c'] <= 20){
					$friendInfo[2]['number']++;
					$friendInfo[2]['info'][] = $value['member_id'];
				}else if ($value['c'] <= 30){
					$friendInfo[3]['number']++;
					$friendInfo[3]['info'][] = $value['member_id'];
				}else if ($value['c'] <= 40){
					$friendInfo[4]['number']++;
					$friendInfo[4]['info'][] = $value['member_id'];
				}else if ($value['c'] <= 50){
					$friendInfo[5]['number']++;
					$friendInfo[5]['info'][] = $value['member_id'];
				}else {
					$friendInfo[6]['number']++;
					$friendInfo[6]['info'][] = $value['member_id'];
				}
			}
			$memberHaveContact = array();
			if (count($haveMemberId) > 0) {
				$sql = "select count(*) c, member_id from group_contact_info where member_id in (".implode(",", $haveMemberId).") group by member_id";
				$command = $connection->createCommand($sql);
				$contactQuery = $command->queryAll();
				foreach($contactQuery as $each){
					$memberHaveContact[$each['member_id']] = $each['c'];
				}
			}
			$arrayInfo = array();
			$bxapplyArea = array();
			if ($keyValue == -1) {
				$sql = "select a.id, a.phone, a.nick_name,a.benben_id, a.name from member a left join bxapply b on a.phone = b.phone where b.enterprise_id=".$this->ownbx." and b.status = 3 and a.id_enable = 1 and a.benben_id>0 and a.id not in (".implode(",", $haveMemberId).")";
				$command = $connection->createCommand($sql);
				$personList = $command->queryAll();
				$allShortphone = array();
				foreach ($personList as $v) {
					if ($v['phone']) {
						$allShortphone[] = $v['phone'];
					}
				}
				if (count($allShortphone)>0) {
					$sql = "select phone, province,city, area, short_phone, name from bxapply where enterprise_id=".$this->ownbx." and phone in (".implode(",", $allShortphone).")";
					$command = $connection->createCommand($sql);
					$BxaplyQuery = $command->queryAll();
					foreach($BxaplyQuery as $each){
						$bxapplyArea[$each['phone']] = array('province'=>$each['province'],'city'=>$each['city'],'area'=>$each['area'], 'short_phone'=>$each['short_phone']);
						if ($each['province']) {$areaId[] = $each['province'];}
						if ($each['city']) {$areaId[] = $each['city'];}
						if ($each['area']) {$areaId[] = $each['area'];}
					}
					$areaInfo = $this->getArea($areaId);
				}
				$totalNumber = $friendInfo[$keyValue]['number'];
			}else if (isset($friendInfo[$keyValue])) {
				$sql = "select id, phone, nick_name,benben_id from member where benben_id>0 and id in (".implode(",", $friendInfo[$keyValue]['info']).")";
				$command = $connection->createCommand($sql);
				$personList = $command->queryAll();
				$allShortphone = array();
				foreach ($personList as $v) {
					if ($v['phone']) {
						$allShortphone[] = "'".$v['phone']."'";
					}
				}
				if (count($allShortphone)>0) {
					$sql = "select phone, province,city, area, short_phone from bxapply where enterprise_id=".$this->ownbx." and phone in (".implode(",", $allShortphone).")";
					$command = $connection->createCommand($sql);
					$BxaplyQuery = $command->queryAll();
					foreach($BxaplyQuery as $each){
						$bxapplyArea[$each['phone']] = array('province'=>$each['province'],'city'=>$each['city'],'area'=>$each['area'], 'short_phone'=>$each['short_phone']);
						if ($each['province']) {$areaId[] = $each['province'];}
						if ($each['city']) {$areaId[] = $each['city'];}
						if ($each['area']) {$areaId[] = $each['area'];}
					}
					$areaInfo = $this->getArea($areaId);
				}
				$totalNumber = $friendInfo[$keyValue]['number'];
			}
			
		}
		if ($download) {
			$objPHPExcel = new PHPExcel();
			/*--------------设置表头信息------------------*/
			//第一个sheet
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A1', '编号')
			->setCellValue('B1', '手机')
			->setCellValue('C1', '姓名/昵称')
			->setCellValue('D1', '省')
			->setCellValue('E1', '市')
			->setCellValue('F1', '区')
			->setCellValue('G1', '百姓网号')
			->setCellValue('H1', '奔犇号')
			->setCellValue('I1', '百姓网数量')
			->setCellValue('J1', '联系人数量')
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
			$objPHPExcel->getActiveSheet()->getStyle('E1')->applyFromArray($styleArray1);
			$objPHPExcel->getActiveSheet()->setTitle('百姓网');      //设置sheet的名称
			$objPHPExcel->setActiveSheetIndex(0);   //设置sheet的起始位置
	
			$i =2;
			foreach ($personList as  $one){
				// 			var_dump($one);exit();
		
				// $sql = "select name, short_phone from bxapply  where  phone = '".$one['phone']."' and status = 3";
				// $command = $connection->createCommand($sql);
				// $bxapplyInfo = $command->queryAll();
				// $bx = '';
				// $current_nickName = '';
				// if ($bxapplyInfo) {
				// 	$bx = $bxapplyInfo[0]['short_phone'];
				// 	$current_nickName = $bxapplyInfo[0]['name'];
				// }
				$cP = ''; $cC = ''; $cA = '';
				if (isset($bxapplyArea[$one['phone']])) {
					$bx = $bxapplyArea[$one['phone']]['short_phone'];
					$cPid = $bxapplyArea[$one['phone']]['province'];
					$cCid = $bxapplyArea[$one['phone']]['city'];
					$cAid = $bxapplyArea[$one['phone']]['area'];
					
					if ($cPid && isset($areaInfo[$cPid])) {
						$cP = $areaInfo[$cPid];
					}
					if ($cCid && isset($areaInfo[$cCid])) {
						$cC = $areaInfo[$cCid];
					}
					if ($cAid && isset($areaInfo[$cAid])) {
						$cA = $areaInfo[$cAid];
					}
				}
				
				$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue("A$i", $i-1)
				->setCellValue("B$i", $one['phone'])
				->setCellValue("C$i", $one['name']."/".$one['nick_name'])
				->setCellValue("D$i",  $cP)
				->setCellValue("E$i",  $cC)
				->setCellValue("F$i",  $cA)
				->setCellValue("G$i", $bx)
				->setCellValue("H$i", $one['benben_id'])
				->setCellValue("I$i",  $memberCount[$one['id']])
				->setCellValue("J$i",  $memberHaveContact[$one['id']]);
				//$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(100);
				$i++;
			}
			
			ob_end_clean();
			ob_start();
			header('Content-Type: application/vnd.ms-excel;charset=utf-8');
			header('Content-Disposition:attachment;filename=' . urlencode('info' . date("YmjHis") .'.xls') . '');
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');   //通过PHPExcel_IOFactory的写函数将上面数据写出来
		
			$objWriter->save('php://output');
		}else{
			$this->render('detail',array(
				'totle'=>$totalNumber,
				'items'=>$personList,
				'memberHaveContact'=>$memberHaveContact,
				'number'=>$memberCount
			));
		}
		
	}

	public function actionBxdownload()
	{
		$connection = Yii::app()->db;
		$id = intval($_GET['key']);
		$csql = "select  a.phone, b.name, a.is_baixing,a.is_benben from group_contact_phone a left join group_contact_info b on a.contact_info_id = b.id where a.is_baixing > 0 and b.member_id = ".$id;
		$command = $connection->createCommand($csql);
		$friendQuery = $command->queryAll();
		if (!$friendQuery) {
			exit();
		}
		foreach ($friendQuery as $key => $value) {
			if($value['is_benben']){
				$haveMemberId[] = $value['is_benben'];
			}
			if ($value['is_baixing']) {
				$haveBxId[] = $value['is_baixing'];
			}
			
		}
		
		$memberHaveContact = array();
		$memberInfo = array();
		$areaInfo = array();
		if (count($haveMemberId) > 0) {
			$sql = "select id, benben_id from member where benben_id in (".implode(",", $haveMemberId).") ";
			$command = $connection->createCommand($sql);
			$memberQuery = $command->queryAll();
			$allMemberId = array();
			$meberIdWithBenben = array();
			foreach($memberQuery as $each){
				$allMemberId[] = $each['id'];
				$meberIdWithBenben[$each['id']]=$each['benben_id'];
			}

			
			// $sql = "select bid, area_name from area where bid in (".implode(",", $areaId).")";
			// $command = $connection->createCommand($sql);
			// $areaQuery = $command->queryAll();
			// foreach($areaQuery as $each){
			// 	$areaInfo[$each['bid']] = $each['area_name'];
			// }
			

			$sql = "select count(*) c, member_id from group_contact_info where member_id in (".implode(",", $allMemberId).") group by member_id";
			$command = $connection->createCommand($sql);
			$contactQuery = $command->queryAll();
			foreach($contactQuery as $each){
				$memberHaveContact[$meberIdWithBenben[$each['member_id']]] = $each['c'];
			}
		}
		if (count($haveBxId) > 0) {
			$sql = "select short_phone, province,city, area from bxapply where short_phone in (".implode(",", $haveBxId).")";
			$command = $connection->createCommand($sql);
			$BxaplyQuery = $command->queryAll();
			foreach($BxaplyQuery as $each){
				$memberInfo[$each['short_phone']] = array('province'=>$each['province'],'city'=>$each['city'],'area'=>$each['area']);
				$areaId[] = $each['province'];
				$areaId[] = $each['city'];
				$areaId[] = $each['area'];
			}
			$areaInfo = $this->getArea($areaId);
		}

		$objPHPExcel = new PHPExcel();
			/*--------------设置表头信息------------------*/
			//第一个sheet
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A1', '编号')
			->setCellValue('B1', '手机')
			->setCellValue('C1', '姓名')
			->setCellValue('D1', '省')
			->setCellValue('E1', '市')
			->setCellValue('F1', '区')
			->setCellValue('G1', '百姓网号')
			->setCellValue('H1', '联系人数量')
			
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
			$objPHPExcel->getActiveSheet()->getStyle('E1')->applyFromArray($styleArray1);
			$objPHPExcel->getActiveSheet()->setTitle('百姓网');      //设置sheet的名称
			$objPHPExcel->setActiveSheetIndex(0);   //设置sheet的起始位置
	
			$i =2;
			foreach ($friendQuery as  $one){
				$number = '0';
				$cP = ''; $cC = ''; $cA = '';
				if($one['is_baixing'] && isset($memberInfo[$one['is_baixing']])){
					$cPid = $memberInfo[$one['is_baixing']]['province'];
					$cCid = $memberInfo[$one['is_baixing']]['city'];
					$cAid = $memberInfo[$one['is_baixing']]['area'];
					
					if ($cPid && isset($areaInfo[$cPid])) {
						$cP = $areaInfo[$cPid];
					}
					if ($cCid && isset($areaInfo[$cCid])) {
						$cC = $areaInfo[$cCid];
					}
					if ($cAid && isset($areaInfo[$cAid])) {
						$cA = $areaInfo[$cAid];
					}

				}
				if (isset($memberHaveContact[$one['is_benben']])) {
					$number = $memberHaveContact[$one['is_benben']];
				}
				
					
				$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue("A$i", $i-1)
				->setCellValue("B$i", $one['phone'])
				->setCellValue("C$i", $one['name'])

				->setCellValue("D$i", $cP)
				->setCellValue("E$i", $cC)
				->setCellValue("F$i", $cA)
				->setCellValue("G$i", $one['is_baixing'])
				->setCellValue("H$i", $number);
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


	public function actionBxContactDownload(){
		$connection = Yii::app()->db;
		$keyValue = intval($_GET['key']);
		$type = intval($_GET['type']);
		$download = intval($_GET['download']);
		//百姓网用户是否是奔犇用户
		$isql = "select distinct(b.id) from bxapply a left join member b on a.phone = b.phone where a.status = 3 and b.id>0 and id_enable = 1";
		$command = $connection->createCommand($isql);
		$resulti = $command->queryAll();
		$isBenben = count($resulti);
		$benbenMember = array();
		foreach($resulti as $e){
			$benbenMember[] = $e['id'];
		}
		
		$csql = "select count(a.id) c, b.member_id from group_contact_phone a left join group_contact_info b on a.contact_info_id = b.id where a.is_baixing > 0 and b.member_id  in (".implode(",", $benbenMember).") ";
		$csql .= ' group by b.member_id order by c desc';
		$memberCount = array();
		$command = $connection->createCommand($csql);
		$friendQuery = $command->queryAll();
		$totalNumber = 0;
		$friendInfo = array();
		$haveMemberId = array();
		$haveInfoMember = array();

		if ($friendQuery) {
			foreach ($friendQuery as $key => $value) {
				$memberCount[$value['member_id']] = $value['c'];
				$haveMemberId[] = $value['member_id'];
				if ($value['c'] <= 5) {
					$friendInfo[0]['number']++;
					$friendInfo[0]['info'][] = $value['member_id'];
				}else if ($value['c'] <= 10){
					$friendInfo[1]['number']++;
					$friendInfo[1]['info'][] = $value['member_id'];
				}else if ($value['c'] <= 20){
					$friendInfo[2]['number']++;
					$friendInfo[2]['info'][] = $value['member_id'];
				}else if ($value['c'] <= 30){
					$friendInfo[3]['number']++;
					$friendInfo[3]['info'][] = $value['member_id'];
				}else if ($value['c'] <= 40){
					$friendInfo[4]['number']++;
					$friendInfo[4]['info'][] = $value['member_id'];
				}else if ($value['c'] <= 50){
					$friendInfo[5]['number']++;
					$friendInfo[5]['info'][] = $value['member_id'];
				}else {
					$friendInfo[6]['number']++;
					$friendInfo[6]['info'][] = $value['member_id'];
				}
			}
			$memberHaveContact = array();
			//计算联系人数量
			if (count($haveMemberId) > 0) {
				$sql = "select count(*) c, member_id from group_contact_info where member_id in (".implode(",", $haveMemberId).") group by member_id";
				$command = $connection->createCommand($sql);
				$contactQuery = $command->queryAll();
				foreach($contactQuery as $each){
					$memberHaveContact[$each['member_id']] = $each['c'];
				}
			}
			if ($keyValue == -1) {
				$sql = "select a.id, a.phone, a.nick_name,a.benben_id, b.member_id,a.name, b.short_phone, a.province, a.city,a.area from member a left join bxapply b on a.phone = b.phone where a.id_enable = 1 and a.id not in (".implode(",", $haveMemberId).")";
				$command = $connection->createCommand($sql);
				$personList = $command->queryAll();
				$totalNumber = $friendInfo[$keyValue]['number'];
			}else if (isset($friendInfo[$keyValue])) {
				$sql = "select a.id, a.phone, a.nick_name,a.benben_id, b.member_id,a.name, b.short_phone,a.province, a.city, a.area from member a left join bxapply b on a.phone = b.phone  where a. id in (".implode(",", $friendInfo[$keyValue]['info']).") group by a.id";
				$command = $connection->createCommand($sql);
				$personList = $command->queryAll();
				$totalNumber = $friendInfo[$keyValue]['number'];
			}
			$areaInfo = array();
			$areaId = array();
			foreach ($personList as $v) {
				if ($v['phone']) {
					$allShortphone[] = "'".$v['phone']."'";
				}
			}
			
				

			
			
			$personFriendArray = array();
			$submitId = array();
			$personListId = array();
			if (count($personList)) {
				foreach ($personList as $key => $value) {
					$personListId[] = $value['id'];
					if ($value['member_id']) {
						$submitId[] = $value['member_id'];
					}
					
				}
				$csql = "select  a.phone, b.name, a.is_baixing,b.member_id, c.member_id as submit_id,is_benben from group_contact_phone a left join group_contact_info b on a.contact_info_id = b.id left join bxapply c on c.short_phone = a.is_baixing where a.is_baixing > 0 and b.member_id  in (".implode(",", $personListId).")";
				$command = $connection->createCommand($csql);
				$personContact = $command->queryAll();
				
				foreach ($personContact as $key => $value) {
					$personFriendArray[$value['member_id']][] = array('phone'=>$value['phone'], 'name'=>$value['name'], 'baixing'=>$value['is_baixing'],'is_benben'=>$value['is_benben'], 'submit_id'=>$value['submit_id']);
					if($value['submit_id']){
						$submitId[] = $value['submit_id'];
					}
					if ($value['phone']) {
						$allShortphone[] = "'".$value['phone']."'";
					}
					
				}
				$sql = "select id, nick_name, phone from member where id in (".implode(",", $submitId).")";
				$command = $connection->createCommand($sql);
				$submitQuery = $command->queryAll();
				$submitInfo = array();
				if ($submitQuery) {
					foreach($submitQuery as $e){
						$submitInfo[$e['id']] = array('nick_name'=>$e['nick_name'], 'phone'=>$e['phone']);
					}
					
				}
				
				if (count($allShortphone)>0) {
					$sql = "select phone, province,city, area, short_phone from bxapply where phone in (".implode(",", $allShortphone).")";
					$command = $connection->createCommand($sql);
					$BxaplyQuery = $command->queryAll();
					foreach($BxaplyQuery as $each){
						$bxapplyArea[$each['phone']] = array('province'=>$each['province'],'city'=>$each['city'],'area'=>$each['area'], 'short_phone'=>$each['short_phone']);
						if ($each['province']) {$areaId[] = $each['province'];}
						if ($each['city']) {$areaId[] = $each['city'];}
						if ($each['area']) {$areaId[] = $each['area'];}
					}
					
				}

				$areaInfo = $this->getArea($areaId);
				$objPHPExcel = new PHPExcel();
				/*--------------设置表头信息------------------*/
				//第一个sheet
				$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A1', '编号')
				->setCellValue('B1', '手机')
				->setCellValue('C1', '姓名')
				->setCellValue('D1', '省')
				->setCellValue('E1', '市')
				->setCellValue('F1', '区')
				->setCellValue('G1', '百姓网号')
				->setCellValue('H1', '提交人')
				->setCellValue('I1', '提交人号码')
				->setCellValue('J1', '奔犇号')
				->setCellValue('K1', '联系人数量');
				
				$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
				$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
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
				$objPHPExcel->getActiveSheet()->getStyle('H1')->applyFromArray($styleArray1);
				$objPHPExcel->getActiveSheet()->setTitle('百姓网');      //设置sheet的名称
				$objPHPExcel->setActiveSheetIndex(0);   //设置sheet的起始位置
		
				$i =2;
				foreach ($personList as $key => $one) {
					$currentSubmitName = '';
					$currentSubmitPhone = '';
					if (isset($submitInfo[$one['member_id']])) {
						$currentSubmitName = $submitInfo[$one['member_id']]['nick_name'];
						$currentSubmitPhone = $submitInfo[$one['member_id']]['phone'];
					}
					$cP = ''; $cC = ''; $cA = '';
					if (isset($bxapplyArea[$one['phone']])) {
						$bx = $bxapplyArea[$one['phone']]['short_phone'];
						$cPid = $bxapplyArea[$one['phone']]['province'];
						$cCid = $bxapplyArea[$one['phone']]['city'];
						$cAid = $bxapplyArea[$one['phone']]['area'];
						
						if ($cPid && isset($areaInfo[$cPid])) {
							$cP = $areaInfo[$cPid];
						}
						if ($cCid && isset($areaInfo[$cCid])) {
							$cC = $areaInfo[$cCid];
						}
						if ($cAid && isset($areaInfo[$cAid])) {
							$cA = $areaInfo[$cAid];
						}
					}

					
					
					$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue("A$i", $i-1)
					->setCellValue("B$i", $one['phone'])
					->setCellValue("C$i", $one['name']."/".$one['nick_name'])
					->setCellValue("D$i", $cP)
					->setCellValue("E$i", $cC)
					->setCellValue("F$i", $cA)
					->setCellValue("G$i", $one['short_phone'])
					->setCellValue("H$i", $currentSubmitName)
					->setCellValue("I$i", $currentSubmitPhone)
					->setCellValue("J$i", $one['benben_id'])
					->setCellValue("K$i", $memberHaveContact[$one['id']]);
					// $objPHPExcel->setActiveSheetIndex(0)->getStyle("A$i")->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);
					$objPHPExcel->setActiveSheetIndex(0)->getStyle( "A$i:K$i")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
					$objPHPExcel->setActiveSheetIndex(0)->getStyle( "A$i:K$i")->getFill()->getStartColor()->setARGB('FF808080');
					$i++;
					if(isset($personFriendArray[$one['id']])){
						foreach ($personFriendArray[$one['id']] as $k => $v) {
							$currentSubmitName = '';$currentSubmitPhone = '';
							if (isset($submitInfo[$v['submit_id']])) {
								$currentSubmitName = $submitInfo[$v['submit_id']]['nick_name'];
								$currentSubmitPhone = $submitInfo[$v['submit_id']]['phone'];
							}
							$sCP = ''; $sCC = ''; $sCA = '';
							if (isset($bxapplyArea[$v['phone']])) {
								$subPId = $bxapplyArea[$v['phone']]['province'];
								$subCId = $bxapplyArea[$v['phone']]['city'];
								$subAId = $bxapplyArea[$v['phone']]['area'];
								if ($subPId && isset($areaInfo[$subPId])) {
									$sCP = $areaInfo[$subPId];
								}
								if ($subCId && isset($areaInfo[$subCId])) {
									$sCC = $areaInfo[$subCId];
								}
								if ($subAId && isset($areaInfo[$subAId])) {
									$sCA = $areaInfo[$subAId];
								}
							}
							
							$objPHPExcel->setActiveSheetIndex(0)
							->setCellValue("A$i", $i-1)
							->setCellValue("B$i", $v['phone'])
							->setCellValue("C$i", $v['name'])
							->setCellValue("D$i", $sCP)
							->setCellValue("E$i", $sCC)
							->setCellValue("F$i", $sCA)
							->setCellValue("G$i", $v['baixing'])
							->setCellValue("H$i", $currentSubmitName)
							->setCellValue("I$i", $currentSubmitPhone)
							->setCellValue("J$i", $v['is_benben'])
							->setCellValue("K$i", '');
							$i++;
						}
					}

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


	function getArea($areaId){
		global $connection;
		if (!$connection) {
			$connection = Yii::app()->db;
		}
		$sql = "select bid, area_name from area where bid in (".implode(",", array_unique($areaId)).")";
			$command = $connection->createCommand($sql);
			$areaQuery = $command->queryAll();
			foreach($areaQuery as $each){
				$areaInfo[$each['bid']] = $each['area_name'];
			}
			return $areaInfo;
	}

	function textPage($total,$page,$dolink){
		Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/themes/css/pager.css");
		$line = 8;
		//$totalpage = ceil($total/6);
		$totalpage = $total;
		if($totalpage==1)
		{
			return '';
		}
		$pages = $totalpage;
	
		$line = $line - 1;
		$page = $page <= 0 ? 1 : $page;
		$page = $page > $pages ? $pages : $page;
		$prev = '';
		$next = '';
		if (($line + 1) > $pages) {
			for ($i = 1; $i <= $pages; $i++) {
				$apclass = $i == $page ? "selected" :'';
				$tmp = ($i-1)==1 ?'': 'page='.($i-1);
				$href = $dolink.'page='.$i;
				if($i == 1){
					$prev ='<li class="previous hidden"><a></a></li>';
					$next ='<li class="next"><a href="'.$dolink.'page='.($i+1).'">下一页</a></li>';
					//$href = $dolink;
				}elseif($i == $pages and $i==$page){
					$prev ='<li class="previous"><a href="'.$dolink.$tmp.'">上一页</a></li>';
					$next ='<li class="next hidden"><a></a></li>';
				}elseif($i==$page){
					$prev ='<li class="previous"><a href="'.$dolink.$tmp.'">上一页</a></li>';
					$next ='<li class="next"><a href="'.$dolink.'page='.($i+1).'">下一页</a></li>';
				}
				$conpage .= "<li class='page $apclass'><a href='$href'>$i</a></li>";
			}
		} else {
			$unit = ceil($line / 2);
			$s_show = $page - $unit;
			$e_show = $page + $unit;
	
			$s_show = $s_show <= 0 ? 1 : $s_show;
			$e_show = $e_show < ($line + 1) ? ($line + 1) : $e_show;
	
			if ($e_show > $pages) {
				$s_show = $pages - $line;
				$e_show = $pages;
			}
	
			if ($s_show > 1)
				$conpage .= '<li class="page"><a href="'.$dolink.'">1</a></li><li class="page"><a style="padding:0">...</a></li>';
	
			for ($i = 1; $i <= $pages; $i++) {
				if ($i >= $s_show and $i <= $e_show) {
					$apclass = $i == $page ? "selected" :'';
					$tmp = ($i-1)==1 ?'': 'page='.($i-1);
	
					$href = $dolink.'page='.$i;
					if($i == 1){
						$prev ='<li class="previous hidden"><a></a></li>';
						$next ='<li class="next"><a href="'.$dolink.'page='.($i+1).'">下一页</a></li>';
						//$href = $dolink;
					}elseif($i == $pages and $i==$page){
						$prev ='<li class="previous"><a href="'.$dolink.$tmp.'">上一页</a></li>';
						$next ='<li class="next hidden"><a></a></li>';
					}elseif($i==$page){
						$prev ='<li class="previous"><a href="'.$dolink.$tmp.'">上一页</a></li>';
						$next ='<li class="next"><a href="'.$dolink.'page='.($i+1).'">下一页</a></li>';
					}
					$conpage .= "<li class='page $apclass'><a href='$href'>$i</a></li>";
				}
			}
			if ($e_show < $pages){
				$conpage .= '<li class="page"><a style="padding:0">...</a></li><li class="page"><a href="'.$dolink.'page='.$totalpage.'">'.$totalpage.'</a></li>';
			}
		}
		$returnstr = $prev.$conpage.$next;
		return $returnstr;
	}
	
	//登录日志
	function insert_log($status){
		$userid = Yii::app ()->user->getState('userInfo')->id;
		$username = Yii::app ()->user->getState('userInfo')->username;
		$log = new LoginLog();
		$log->username = $username;
		$log->logintime = time();
		$log->loginip = $this->egetip();
		$log->ipport = $this->egetipport();
		$log->status = $status;
		$log->userid = $userid;
		$log->save();
	}
	
	//取得IP
	function egetip(){
		if(getenv('HTTP_CLIENT_IP')&&strcasecmp(getenv('HTTP_CLIENT_IP'),'unknown'))
		{
			$ip=getenv('HTTP_CLIENT_IP');
		}
		elseif(getenv('HTTP_X_FORWARDED_FOR')&&strcasecmp(getenv('HTTP_X_FORWARDED_FOR'),'unknown'))
		{
			$ip=getenv('HTTP_X_FORWARDED_FOR');
		}
		elseif(getenv('REMOTE_ADDR')&&strcasecmp(getenv('REMOTE_ADDR'),'unknown'))
		{
			$ip=getenv('REMOTE_ADDR');
		}
		elseif(isset($_SERVER['REMOTE_ADDR'])&&$_SERVER['REMOTE_ADDR']&&strcasecmp($_SERVER['REMOTE_ADDR'],'unknown'))
		{
			$ip=$_SERVER['REMOTE_ADDR'];
		}
		$ip=(preg_replace("/^([\d\.]+).*/","\\1",$ip));
		return $ip;
	}
	
	//取得端口
	function egetipport(){
		$ipport=(int)$_SERVER['REMOTE_PORT'];
		return $ipport;
	}
}