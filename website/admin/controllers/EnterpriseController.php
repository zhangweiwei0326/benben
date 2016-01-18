<?php
Yii::$enableIncludePath = false;
define('__ROOT__',dirname(dirname(__FILE__)));
require_once (__ROOT__.'/PHPExcel/PHPExcel.php');
class EnterpriseController extends BaseController
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/admin';
	
	/**
	 * @var int the define the index for the menu
	 */
	 
	 public $menuIndex = 30;
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Enterprise;
		$province = $this->getProvince();
		$p = intval($_POST['Enterprise']['province']);
		$c = intval($_POST['Enterprise']['city']);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
		if ((Yii::app()->request->isPostRequest)) {
			$model->attributes=$_POST['Enterprise'];
			//查询政企名称是否重复
			$name = $_POST['Enterprise']['name'];
			$info = Enterprise::model()->find("name = '{$name}'");
			if( $p > 0){
				$res = $this->getCity($p);
			}
			
			if($c > 0){
				$res2 = $this->getArea($c);
			}
			if($info){
				$this->render("create",array("msg"=>"通讯录名称已存在，请重填", 'model'=>$model,'province'=>$province,'res'=>$res,'res2'=>$res2));
				die();
			}
								
			$inputfile = dirname(__FILE__)."/../..".Frame::saveExcel("inputfile");
			if($_FILES['inputfile']['name']){
				$PHPReader = new PHPExcel_Reader_Excel2007();
			
				if(!$PHPReader->canRead($inputfile)){
					$PHPReader = new PHPExcel_Reader_Excel5();
					if(!$PHPReader->canRead($inputfile)){
						return 'no Excel';
					}
				}
			
				$PHPExcel = $PHPReader->load($inputfile); /**读取excel文件*/
				$currentSheet = $PHPExcel->getSheet(0);   /**取得最大的列号*/
				$allColumn = $currentSheet->getHighestColumn();   /**取得一共有多少行*/
				$allRow = $currentSheet->getHighestRow();
				
			    $userid = $this->getLoginId();
			    $t = time();
			    $con = array();
				for($currentRow = 2;$currentRow <= $allRow;$currentRow++){	
					$name = $currentSheet->getCellByColumnAndRow(1,$currentRow)->getValue();
					$phone = trim($currentSheet->getCellByColumnAndRow(2,$currentRow)->getValue());
					$otherphone = trim($currentSheet->getCellByColumnAndRow(3,$currentRow)->getValue());
						
					if($name){
						$con[] = array("name"=>$name,'phone'=>$phone,'other'=>$otherphone);											
					}		
				}
				if( $p > 0){
					$res = $this->getCity($p);
				}
		
				if($c > 0){
					$res2 = $this->getArea($c);
				}
				$this->render("create",array("msg"=>"成员数据解析完成，请确认", 'info'=>$con, 'model'=>$model,'province'=>$province,'res'=>$res,'res2'=>$res2));
				die();
			}else if(isset($_POST['Enterprise']))
			{
				$infoJson = $_POST['info_json'];
				$infoArray = array();
				if($infoJson){
					$infoArray = json_decode($infoJson, true);
				}
				if (count($infoArray) > 0) {
					$model->number = count($infoArray);
					if ($model->type == 2) {
						$model->short_length = strlen($infoArray[0]['other']);
					}
				}
				$model->created_time = time();
				
				if($model->save())
					
					if (count($infoArray) > 0) {
						
						$sqlArray = array();
						$phoneArray = array();
						foreach ($infoArray as $key => $value) {
							$phoneArray[] = $value['phone'];							
						}
						$phonestr = implode(",", $phoneArray);
						$memberinfo = Member::model()->findAll("phone in ($phonestr)");
						$phoneinfo = array();
						if($memberinfo){
							foreach ($memberinfo as $va){
								$phoneinfo[$va->phone] = $va->id;
							}
						}
						
						foreach ($infoArray as $key => $value) {							
							$sqlArray[] = '("'.$value['name'].'", "'.$phoneinfo[$value['phone']].'", "'.$value['phone'].'", "'.$value['other'].'", "'.$value['name'].'", '.$model->id.', '.time().')';
						}
						if (count($sqlArray) > 0) {
							$connection = Yii::app()->db;
							$sql = 'insert into enterprise_member(name, member_id, phone, short_phone, remark_name, contact_id, created_time) values'.implode(",", $sqlArray);
							$command = $connection->createCommand($sql);
							$resultc = $command->execute();
						}
					}
					$this->redirect($this->getBackListPageUrl());
			}
		}
		

		$this->render('create',array(
			'model'=>$model,
			'province'=>$province,
			'backUrl' => $this->getBackListPageUrl(),
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$type = array("1" => "企业通讯录", "2" => "虚拟通讯录");
		$model=$this->loadModel($id);
		
		//显示最后一条警用原因
		$enterpriseDisable = new EnterpriseDisable();
		$sql = "SELECT reason FROM enterprise_disable 
					WHERE contact_id = ".$model->id." ORDER BY created_time DESC LIMIT 1";
		$ereason = $enterpriseDisable->findAllBySql($sql);
		$reason = $ereason[0]->reason;
		
		//显示创建人禁用原因
		$serviceDisable = new ServiceDisable();
		$sql = "SELECT status,reason FROM service_disable
					WHERE member_id = ".$model->member_id." and type = 3 ORDER BY created_time DESC LIMIT 1";
		$ereason2 = $serviceDisable->findAllBySql($sql);
		$reason2 = $ereason2[0]->reason;
		
		$province = $this->areas($model->province) ? $this->areas($model->province) : "未知";
		$city = $this->areas($model->city) ? $this->areas($model->city) : "未知";
		$area = $this->areas($model->area) ? $this->areas($model->area) : "未知";
		$street = $this->areas($model->street) ? $this->areas($model->street) : "未知";

		$areas = array();
		$areas = array("province" => $province, "city" => $city, "area" => $area, "street" => $street);
		
		//创建人
		$member = new Member();
		$memberInfo = $member->findByPk($model->member_id);
		
		if(isset($_POST['Enterprise']))
		{
			$status = $_POST['Enterprise']['status'];
			$post_reason = $_POST['Enterprise']['reason'];
			
			//状态改变或者原因改变，写入禁用记录表
			if($status != $model->status || $reason != $post_reason){
				$enterpriseDisable->contact_id = $model->id;
				$enterpriseDisable->status = $status;
				$enterpriseDisable->user_id =  $this->getLoginId();
				$enterpriseDisable->reason = $post_reason;
				$enterpriseDisable->created_time = time();
				$enterpriseDisable->save();
			}
			//改变创建人禁用状态
			$status2 = $_POST['Enterprise']['status2'];
			$post_reason2 = $_POST['Enterprise']['reason2'];
			if($status2 != $ereason2[0]->status || $reason2 != $post_reason2){				
				$memberInfo->enterprise_disable = $status2;
				if($memberInfo->update()){
					$service = new ServiceDisable();
					$service->member_id = $model->member_id;
					$service->user_id = $this->getLoginId();
					$service->status = $status2;
					$service->reason = $post_reason2;
					$service->type = 3;
					$service->created_time = time();
					$service->save();				
				}
			}
			$model->status = $_POST['Enterprise']['status'];
			if($_POST['Enterprise']['name']){
				$model->name = $_POST['Enterprise']['name'];
			}
			if($_POST['Enterprise']['description']){
				$model->description =$_POST['Enterprise']['description'];
			}
			if($_POST['Enterprise']['province']){
				$model->province = $_POST['Enterprise']['province'];
			}
			if($_POST['Enterprise']['city']){
				$model->city = $_POST['Enterprise']['city'];
			}
			if($_POST['Enterprise']['area']){
				$model->area = $_POST['Enterprise']['area'];
			}
			if($_POST['Enterprise']['street']){
				$model->street = $_POST['Enterprise']['street'];
			}
			if($model->save())
				$this->redirect($this->getBackListPageUrl());
		}
		$model->type = $type[$model->type];
		$model->created_time = date('Y-m-d H:i:s', $model->created_time);
		$aprovince = array();
		$aprovince['province'] = $this->getProvince();
		if($model->province){$aprovince['city'] = $this->getCity($model->province);}else{$aprovince['city'] = array();};
		if($model->city){$aprovince['area'] = $this->getArea($model->city);}else{$aprovince['area'] = array();};
		if($model->area){$aprovince['street'] = $this->getStreet($model->area);}else{$aprovince['street'] = array();};
		
		$this->render('update',array(
			'model'=>$model,
			'member_name' => ($model->origin == 1) ? ($memberInfo['name']?$memberInfo['name']:$memberInfo['nick_name']) : "admin(后台)",
			'member_phone' => $memberInfo['phone'],
			'reason' => $reason,
			'province' => $aprovince,
			'status2' => $memberInfo['enterprise_disable'],
			'reason2' => $reason2,	
			'areas' => $areas,
			'backUrl' => $this->getBackListPageUrl(),
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	 /**
	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}
*/
	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$this->insert_log(30);
		$province = $this->getProvince();
		
		
		$model = Enterprise::model();
		$cri = new CDbCriteria();
		$cri->select = "t.*, a.name as mname, a.nick_name as nick_name, a.enterprise_disable ";
		$cri->join = "left join member a on a.id = t.member_id";
		$cri->order = "id desc";
		
		//检索
		$result = array();
		if(isset($_GET['name']) || !empty($_GET['name'])){
			$cri->addSearchCondition('t.name', $_GET['name'], true, 'AND');
			$result['name'] = $_GET['name'];
			$result['goback'] = -2;
		}
		
		if(isset($_GET['type']) || !empty($_GET['type'])){
			if($_GET['type'] != -1){
				$cri->addSearchCondition('type', $_GET['type'], true, 'AND');
				$result['type'] = $_GET['type'];
				$result['goback'] = -2;
			}
		}
		
		if(isset($_GET['status']) && $_GET['status'] != -1){			
			$cri->addCondition("t.status = ".$_GET['status'],'AND');		
			$result['status'] = $_GET['status'];
			$result['goback'] = -2;
		}
		
		if($_GET['created_time1'] && $_GET['created_time2']){
			$ct1 = strtotime($_GET['created_time1']);
			$ct2 = strtotime($_GET['created_time2'])+86399;
		
			if($ct1 >= $ct2){
				$msg = "申请日期第一个必须比第二个小!";
			}else{
				$cri->addCondition('t.created_time >= '.$ct1,'AND');
				$result['created_time1'] = $_GET['created_time1'];
				$cri->addCondition('t.created_time <= '.$ct2,'AND');
				$result['created_time2'] = $_GET['created_time2'];
				$result['goback'] = -2;
			}
		}else{
			if($_GET['created_time1']){
				$cri->addCondition('t.created_time >= '.strtotime($_GET['created_time1']),'AND');
				$result['created_time1'] = $_GET['created_time1'];
				$result['goback'] = -2;
			}
			if($_GET['created_time2']){
				$cri->addCondition('t.created_time <= '.strtotime($_GET['created_time2'])+86399,'AND');
				$result['created_time2'] = $_GET['created_time2'];
				$result['goback'] = -2;
			}
		}
		
		if($_GET['province'] && ($_GET['province'] != -1)){
			$cri->addCondition('t.province = '.$_GET['province'],'AND');
			$result['province'] = $_GET['province'];
			$result['goback'] = -2;
			$res = $this->getCity($_GET['province']);
		}
		
		if($_GET['city'] && ($_GET['city'] != -1)){
			$cri->addCondition('t.city = '.$_GET['city'],'AND');
		
			$result['goback'] = -2;
			$res2 = $this->getArea($post_city);
			$result['city'] = $_GET['city'];
		}
		
		if($_GET['area'] && ($_GET['area'] != -1)){
			$cri->addCondition('t.area = '.$_GET['area'],'AND');
				
			$result['goback'] = -2;
			$result['area'] = $_GET['area'];
		}
		$number1 = intval($_GET['number1']);
		$number2 = intval($_GET['number2']);
		if ($number1 > 0) {
			$cri->addCondition('t.number >= '.$number1,'AND');
			$result['number1'] = $number1;
 		}
 		if ($number2 > 0) {
			$cri->addCondition('t.number <= '.$number2,'AND');
			$result['number2'] = $number2;
 		}
			
		
		if($_GET['member_name'] && ($_GET['member_name'] != -1)){
			//$cri->addCondition("member.name = '{$_GET['member_name']}'",'AND');
			$cri->addCondition("(a.nick_name like '%{$_GET['member_name']}%' or a.name like '%{$_GET['member_name']}%')", 'AND');			

			$result['goback'] = -2;
			$result['member_name'] = $_GET['member_name'];
		}
		
		if($_GET['member_phone'] && ($_GET['member_phone'] != -1)){
			//$cri->addCondition("member.phone = '{$_GET['member_phone']}'",'AND');
			$cri->addSearchCondition("a.phone", $_GET['member_phone'], true, 'AND');
			$result['goback'] = -2;
			$result['member_phone'] = $_GET['member_phone'];
		}
		
		if($_GET['member_status'] && ($_GET['member_status'] != -1)){
			$cri->addCondition("a.enterprise_disable =".$_GET['member_status'], 'AND');
			$result['goback'] = -2;
			$result['member_status'] = $_GET['member_status'];
		}
		
		$pages = new CPagination();
		$pages->itemCount = $model->count($cri);
		$pages->pageSize = 50;
		$pages->applyLimit($cri);
		$items = $model->findAll($cri);
		$bid = array();
		if($items){
			foreach ($items as $va){
				$eid[] = $va->id;
			}
			$connection = Yii::app()->db;
			$sql = "select contact_id,count(contact_id) num from enterprise_member where contact_id in (".implode(",", $eid).") and member_id > 0 group by contact_id";
			//$bid = EnterpriseMember::model()->findAll("contact_id in ({".implode(",", $eid)."}) and member_id > 0");
			$command = $connection->createCommand($sql);
			$result2 = $command->queryAll();
			if($result2){
				foreach ($result2 as $va){
					$bid[$va['contact_id']] = $va['num'];
				}
			}
		}
		
		$url = Yii::app()->request->getUrl();
		$cookie = new CHttpCookie('benben-neverland',$url);
		$cookie->expire = time()+3600;  
		Yii::app()->request->cookies['benben-neverland']=$cookie;
		$this->render('index',array('items'=>$items,'pages'=> $pages, 'result' => $result,'bid'=>$bid, 
		'province' => $province, 'res' => $res, 'res2' => $res2));
		
	}

	
	public function actionDelete($id)
	{
		$id = Frame::getIntFromRequest('id');
		if ($id > 0) {
			$model = Enterprise::model ()->findByPk ( $id );
		}
		if ($model) {
			$model->delete();
		}
		$this->redirect ( Yii::app()->createUrl('enterprise/index',array('page'=>intval($_REQUEST['page']))));
	}
	
	//导出excel
	public function actionPhpexcel(){
		
		$model = Enterprise::model();
		$cri = new CDbCriteria();
		$cri->select = "t.*, a.name as mname";
		$cri->join = "left join member a on a.id = t.member_id";
		$cri->order = "id desc";
		
		//检索
		$result = array();
		if(isset($_GET['name']) || !empty($_GET['name'])){
			$cri->addSearchCondition('t.name', $_GET['name'], true, 'AND');
			$result['name'] = $_GET['name'];
			$result['goback'] = -2;
		}
		
		if(isset($_GET['type']) && intval($_GET['type']) > 0){
			if($_GET['type'] != -1){
				$cri->addCondition('type='.$_GET['type'], 'AND');
				$result['type'] = $_GET['type'];
				$result['goback'] = -2;
			}
		}
		
		if(isset($_GET['status']) && $_GET['status'] != -1){
			$cri->addCondition('t.status = '.$_GET['status'], 'AND');
			$result['status'] = $_GET['status'];
			$result['goback'] = -2;
		}
		
		if($_GET['created_time1'] && $_GET['created_time2']){
			$ct1 = strtotime($_GET['created_time1']);
			$ct2 = strtotime($_GET['created_time2'])+86399;
		
			if($ct1 >= $ct2){
				$msg = "申请日期第一个必须比第二个小!";
			}else{
				$cri->addCondition('t.created_time >= '.$ct1,'AND');
				$result['created_time1'] = $_GET['created_time1'];
				$cri->addCondition('t.created_time <= '.$ct2,'AND');
				$result['created_time2'] = $_GET['created_time2'];
				$result['goback'] = -2;
			}
		}else{
			if($_GET['created_time1']){
				$cri->addCondition('t.created_time >= '.strtotime($_GET['created_time1']),'AND');
				$result['created_time1'] = $_GET['created_time1'];
				$result['goback'] = -2;
			}
			if($_GET['created_time2']){
				$cri->addCondition('t.created_time <= '.strtotime($_GET['created_time2'])+86399,'AND');
				$result['created_time2'] = $_GET['created_time2'];
				$result['goback'] = -2;
			}
		}
		
		if($_GET['province'] && ($_GET['province'] != -1)){
			$cri->addCondition('t.province = '.$_GET['province'],'AND');
			$result['province'] = $_GET['province'];
			$result['goback'] = -2;
		}
		
		if($_GET['city'] && ($_GET['city'] != -1)){
			$cri->addCondition('t.city = '.$_GET['city'],'AND');
		
			$result['goback'] = -2;
		}
		
		if($_GET['area'] && ($_GET['area'] != -1)){
			$cri->addCondition('t.area = '.$_GET['area'],'AND');
		
			$result['goback'] = -2;
		}
			
		if($_GET['street'] && ($_GET['street'] != -1)){
			$cri->addCondition('t.street = '.$_GET['street'],'AND');
		
			$result['goback'] = -2;
		}
		
		$number1 = intval($_GET['number1']);
		$number2 = intval($_GET['number2']);
		if ($number1 > 0) {
			$cri->addCondition('t.number >= '.$number1,'AND');
			$result['number1'] = $number1;
		}
		if ($number2 > 0) {
			$cri->addCondition('t.number <= '.$number2,'AND');
			$result['number2'] = $number2;
		}
		
		if($_GET['member_name'] && ($_GET['member_name'] != -1)){
			//$cri->addCondition("member.name = '{$_GET['member_name']}'",'AND');
			$cri->addCondition("a.name like '%{$_GET['member_name']}%'", 'AND');
			$result['goback'] = -2;
		}
		
		if($_GET['member_phone'] && ($_GET['member_phone'] != -1)){
			//$cri->addCondition("member.phone = '{$_GET['member_phone']}'",'AND');
			$cri->addSearchCondition("a.phone", $_GET['member_phone'], true, 'AND');
			$result['goback'] = -2;
		}
		
		if($_GET['member_status'] && ($_GET['member_status'] != -1)){
			$cri->addCondition("a.enterprise_disable =".$_GET['member_status'], 'AND');
			$result['goback'] = -2;
			$result['member_status'] = $_GET['member_status'];
		}
		
		$users = $model->findAll($cri);
		$bid = array();
		if($users){
			foreach ($users as $va){
				$eid[] = $va->id;
			}
			$connection = Yii::app()->db;
			$sql = "select contact_id,count(contact_id) num from enterprise_member where contact_id in (".implode(",", $eid).") and member_id > 0 group by contact_id";
			//$bid = EnterpriseMember::model()->findAll("contact_id in ({".implode(",", $eid)."}) and member_id > 0");
			$command = $connection->createCommand($sql);
			$result2 = $command->queryAll();
			if($result2){
				foreach ($result2 as $va){
					$bid[$va['contact_id']] = $va['num'];
				}
			}
		}
		
		$objPHPExcel = new PHPExcel();
		/*--------------设置表头信息------------------*/
		//第一个sheet
		$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A1', '通讯录名称')
		->setCellValue('B1', '类型')
		->setCellValue('C1', '地区')
		->setCellValue('D1', '创建人')
		->setCellValue('E1', '加入人数')
		->setCellValue('F1', '奔犇成员数量')
		->setCellValue('G1', '是否禁用')
		->setCellValue('H1', '创建时间');
	
		if(!empty($users)){
			$type = array (
					"1" => "企业通讯录",
					"2" => "虚拟通讯录"
			);
			$status = array (
					"0" => "启用",
					"1" => "屏蔽",					
			);
			//省市代码获取
			$pro = array();
			$pro_arr = array();
			foreach ($users as $value){
				$pro[] = $value['province'];
				$pro[] = $value['city'];
				$pro[] = $value['area'];
				$pro[] = $value['street'];
			}
				
			$pro_name = $this->allareas(implode(",", $pro));
			if($pro_name){
				foreach ($pro_name as $val){
					$pro_arr[$val['bid']] = $val['area_name'];
				}
			}
	
			$i =2;
			foreach ($users as  $one){
				// 			var_dump($one);exit();
				$pron = $pro_arr[$one->province].''.$pro_arr[$one->city];
					
				$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue("A$i", $one->name)
				->setCellValue("B$i", $type[$one->type])
				->setCellValue("C$i", $pron)
				->setCellValue("D$i", $one->mname)
				->setCellValue("E$i", $one->number)
				->setCellValue("F$i", isset($bid[$one->id]) ? $bid[$one->id] : 0)
				->setCellValue("G$i", $status[$one->status])
				->setCellValue("H$i", date("Y-m-d H:i:s", $one->created_time));
				//$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(100);
				$i++;
			}
		}
		//$objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
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
	
		$objPHPExcel->getActiveSheet()->setTitle('政企通讯录信息');      //设置sheet的名称
		$objPHPExcel->setActiveSheetIndex(0);   //设置sheet的起始位置
	
		ob_end_clean();
		ob_start();
		header('Content-Type: application/vnd.ms-excel;charset=utf-8');
		header('Content-Disposition:attachment;filename=' . urlencode('enterprise' . date("YmjHis") .'.xls') . '');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');   //通过PHPExcel_IOFactory的写函数将上面数据写出来
	
		$objWriter->save('php://output');
	}
	
	public function actionDownload()
	{
		$objPHPExcel = new PHPExcel();
		/*--------------设置表头信息------------------*/
		//第一个sheet
		$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A1', '编号')
		->setCellValue('B1', '姓名')
		->setCellValue('C1', '手机号')
		->setCellValue('D1', '其它号码')
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
		$objPHPExcel->getActiveSheet()->setTitle('成员');      //设置sheet的名称
		$objPHPExcel->setActiveSheetIndex(0);   //设置sheet的起始位置
		
		ob_end_clean();
		ob_start();
		header('Content-Type: application/vnd.ms-excel;charset=utf-8');
		header('Content-Disposition:attachment;filename=' . urlencode('happy' . date("YmjHis") .'.xls') . '');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');   //通过PHPExcel_IOFactory的写函数将上面数据写出来
	
		$objWriter->save('php://output');
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Enterprise the loaded model
	 * @throws CHttpException
	 */

	public function loadModel($id)
	{
		$model=Enterprise::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
	
	public function getBackListPageUrl()
	{
		$cookie = Yii::app()->request->getCookies();
		$returnUrl = $cookie['benben-neverland']->value;
		if ($returnUrl) {
			return $returnUrl;
		}else{
			return Yii::app()->createUrl("enterprise/index",array('page'=>$_REQUEST['page']));
		}
	}

	/**
	 * Performs the AJAX validation.
	 * @param Enterprise $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='enterprise-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
