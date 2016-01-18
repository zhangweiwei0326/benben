<?php
Yii::$enableIncludePath = false;
define('__ROOT__',dirname(dirname(__FILE__)));
require_once (__ROOT__.'/PHPExcel/PHPExcel.php');
require_once(__ROOT__.'/PHPExcel/PHPExcel/Cell.php');
class MemberController extends BaseController
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/admin';
	
	/**
	 * @var int the define the index for the menu
	 */
	 
	public $menuIndex = 10;


	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Member;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Member']))
		{
			$model->attributes=$_POST['Member'];
			if($model->save())
				$this->redirect($this->getBackListPageUrl());
		}

		$this->render('create',array(
			'model'=>$model,
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
		$model=$this->loadModel($id);
		$memberDisable = new MemberDisable;
		$sql = "SELECT reason FROM member_disable WHERE member_id = ".$model->id." ORDER BY created_time DESC LIMIT 1";
		$reason = $memberDisable->findAllBySql($sql);
		$reason = $reason[0]->reason;
				
		$province = $this->areas($model->province) ? $this->areas($model->province) : "未知";
		$city = $this->areas($model->city) ? $this->areas($model->city) : "未知";
		$area = $this->areas($model->area) ? $this->areas($model->area) : "未知";
		$street = $this->areas($model->street) ? $this->areas($model->street) : "未知";
		
		$areas = array();
		$areas = array("province" => $province, "city" => $city, "area" => $area, "street" => $street);
		$benben_id = $model->benben_id;
		if(isset($_POST['Member']))
		{				
			$binfo = Member::model()->count("benben_id = {$_POST['Member']['benben_id']}");			
			if($binfo && ($model->benben_id != $_POST['Member']['benben_id'])){
				$model->benben_id = $_POST['Member']['benben_id'];
				$model->integral = $_POST['Member']['integral'];
				$model->coin = $_POST['Member']['coin'];
				$model->status = 	$_POST['Member']['status'];
				$this->render('update',array(
						'model'=>$model,
						'sex' => $model->sex,
						'reason' => $reason,
						'areas' => $areas,
						'status_info' => $info,
						'msg' => "奔犇号已存在，请重新输入",
						'backUrl' => $this->getBackListPageUrl(),
				));
				exit;
			}
			
			if($_POST['Member']['name']){
				$model->name = $_POST['Member']['name'];
			}
			if($_POST['Member']['id_card']){
				$model->id_card = $_POST['Member']['id_card'];
			}
			if($_POST['Member']['sex']){
				$model->sex = $_POST['Member']['sex'];
			}
			if($_POST['Member']['age']){
				$model->age = $age1 = $this->birthday($_POST['Member']['age']+1);
			}
			if($_POST['Member']['province']){
				$model->province = $_POST['Member']['province'];
			}
			if($_POST['Member']['city']){
				$model->city = $_POST['Member']['city'];
			}
			if($_POST['Member']['area']){
				$model->area = $_POST['Member']['area'];
			}
			if($_POST['Member']['street']){
				$model->street = $_POST['Member']['street'];
			}
			
			if($model->status != $_POST['Member']['status'] || $reason != $_POST['Member']['reason']){
				$memberDisable->member_id = $id;
				$memberDisable->status = $_POST['Member']['status'];
				$memberDisable->user_id = $this->getLoginId();
				$memberDisable->reason = $_POST['Member']['reason'];
				$memberDisable->created_time = time();
				$memberDisable->save();
			}
			
			$model->benben_id = $_POST['Member']['benben_id'];
			$model->integral = $_POST['Member']['integral'];
			$model->coin = $_POST['Member']['coin'];		
			$model->status = 	$_POST['Member']['status'];						
											
			if($model->save())
				$connection = Yii::app()->db;
				$sql = "update group_contact_phone set is_benben = {$_POST['Member']['benben_id']} where is_benben = {$benben_id}";
				$command = $connection->createCommand($sql);
				$result1 = $command->execute();
				$this->redirect($this->getBackListPageUrl());
		}
		$model->created_time =  date('Y-m-d H:i:s', $model->created_time);
		$aprovince = array();
		$aprovince['province'] = $this->getProvince();
		if($model->province){$aprovince['city'] = $this->getCity($model->province);}else{$aprovince['city'] = array();};
		if($model->city){$aprovince['area'] = $this->getArea($model->city);}else{$aprovince['area'] = array();};
		if($model->area){$aprovince['street'] = $this->getStreet($model->area);}else{$aprovince['street'] = array();};
		
		//记录
		$connection = Yii::app()->db;
		$sql = "select a.*, b.username as name from member_disable as a left join user as b on a.user_id = b.id where a.member_id = {$id} order by a.created_time desc";
		$command = $connection->createCommand($sql);
		$result1 = $command->queryAll();//var_dump($result1);exit();
		$info = array();
		$status = array('0'=>'启用', '1'=>'禁用1周', '2'=>'禁用2周', '3'=>'禁用1个月', '4'=>'禁用3个月', '5'=>'无限期禁用', '6'=>'注销');
		foreach ($result1 as $va){			
               $info[] = array("created_time"=>$va['created_time'],"name"=>$va['name'],"reason"=>$va['reason'],"status"=>$status[$va['status']]);							
		}
		
		$this->render('update',array(
			'model'=>$model,
			'sex' => $model->sex,
			'reason' => $reason,
			'areas' => $areas,
			'province' => $aprovince,
			'status_info' => $info,
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
		$this->insert_log(10);
		$model = Member::model();
		$cri = new CDbCriteria();
		$cri->select = "t.*,b.short_phone";
		$cri->join = "left join bxapply b on t.id = b.member_id";
		$cri->group = "t.id";
		$cri->addCondition('t.id_enable = 1');
		$province = $this->getProvince();
		
		
		if(isset($_GET) && !empty($_GET)){
			$result = array();
			
			$search = $_GET['benben_id'];
			$phone = $_GET['phone'];
			$name = $_GET['name'];
			$nick_name = $_GET['nick_name'];
			$sex = $_GET['sex'];
			$age1 = $this->birthday($_GET['age1']);
			if($_GET['age2']){
				$age2 = $this->birthday($_GET['age2']+1);
			}			
			$created_time1 = $_GET['created_time1'];
			$created_time2 = $_GET['created_time2'];
			$coin1 = intval($_GET['coin1']);
			$coin2 = intval($_GET['coin2']);
			
			if($search){
				$cri->addCondition('t.benben_id = '.$search, 'AND');
				$result['benben_id'] = $search;
				$result['goback'] = -2;
			}
			if($phone){
				$cri->addSearchCondition('t.phone', $phone, true, 'AND');
				$result['phone'] = $phone;
				$result['goback'] = -2;
			}
			if($name){
				$cri->addSearchCondition('t.name', $name, true, 'AND');
				$result['name'] = $name;
				$result['goback'] = -2;
			}
			if($nick_name){
				$cri->addSearchCondition('t.nick_name', $nick_name, true, 'AND');
				$result['nick_name'] = $nick_name;
				$result['goback'] = -2;
			}
			if($sex){
				if($sex != -1){
					if($sex == 3) {
						$cri->addInCondition('t.sex', array(0));
					}else{
						$cri->addCondition("t.sex=".$sex);
					}
					$result['sex'] = $sex;
					$result['goback'] = -2;
				}
			}
			if($age1 && $age2){
				if($age1 < $age2){
					$msg = "年龄第一个必须比第二个小";	
				}else{
					if($age1){
						$cri->addCondition('t.age <= '.$age1,'AND');
						$result['age1'] = $_GET['age1'];
						$result['goback'] = -2;
					}
					if($age2){
						$cri->addCondition('t.age >= '.$age2,'AND');
						$result['age2'] = $_GET['age2'];
						$result['goback'] = -2;
					}
				}
			}else{
				if($age1){
					$cri->addCondition('t.age <= '.$age1,'AND');
					$result['age1'] = $_GET['age1'];
					$result['goback'] = -2;
				}
				if($age2){
					$cri->addCondition('t.age >= '.$age2,'AND');
					$result['age2'] = $_GET['age2'];
					$result['goback'] = -2;
				}
			}
			
			
			if($created_time1 && $created_time2){
				if(strtotime($created_time1) >= strtotime($created_time2)+86399){
						$msg = "注册日期第一个必须比第二个小";			
				}else{
					if($created_time1){
						$cri->addCondition('t.created_time >= '.strtotime($created_time1),'AND');
						$result['created_time1'] = $created_time1;
						$result['goback'] = -2;
					}
					if($created_time2){
						$cri->addCondition('t.created_time <= '.(strtotime($created_time2)+86399),'AND');
						$result['created_time2'] = $created_time2;
						$result['goback'] = -2;
					}
				}
			}else{
				if($created_time1){
					$cri->addCondition('t.created_time >= '.strtotime($created_time1),'AND');
					$result['created_time1'] = $created_time1;
					$result['goback'] = -2;
				}
				if($created_time2){
					$cri->addCondition('t.created_time <= '.(strtotime($created_time2)+86399),'AND');
					$result['created_time2'] = $created_time2;
					$result['goback'] = -2;
				}
			}
			
			if($coin1 && $coin2){
				if($coin1 >= $coin2){
					$msg = "犇币数量第一个必须比第二个小";
				}else{
					if($coin1){
						$cri->addCondition('t.coin >= '.$coin1,'AND');
						$result['coin1'] = $coin1;
						$result['goback'] = -2;
					}
					if($coin2){
						$cri->addCondition('t.coin <= '.$coin2,'AND');
						$result['coin2'] = $coin2;
						$result['goback'] = -2;
					}
				}
			}else{
				if($coin1){
					$cri->addCondition('t.coin >= '.$coin1,'AND');
					$result['coin1'] = $coin1;
					$result['goback'] = -2;
				}
				if($coin2){
					$cri->addCondition('t.coin <= '.$coin2,'AND');
					$result['coin2'] = $coin2;
					$result['goback'] = -2;
				}
			}
			if (isset($_GET['dj'])) {
				$dj = intval($_GET['dj']);
			}else{
				$dj = -1;
			}
			
			if ($dj >= 0) {
				$level_all = $this->getlevel();
				if ($dj > 0) {
					$lower = $level_all[$dj-1][1];
				}else{
					$lower = 0;
				}
				$higher = $level_all[$dj][1];
				$cri->addCondition('t.integral >= '.$lower,'AND');
				$cri->addCondition('t.integral <= '.$higher,'AND');

			}
			$result['dj'] = $dj;
			
			if($_GET['province'] && ($_GET['province'] != -1)){
				$cri->addCondition('t.province = '.$_GET['province'],'AND');
				$result['province'] = $_GET['province'];
				$result['goback'] = -2;
				$res = $this->getCity($_GET['province']);
			}
			
			if($_GET['city'] && ($_GET['city'] != -1)){
				$cri->addCondition('t.city = '.$_GET['city'],'AND');
				$res2 = $this->getArea($_GET['city']);
				$result['goback'] = -2;
			}
					
			if($_GET['area'] && ($_GET['area'] != -1)){
				$cri->addCondition('t.area = '.$_GET['area'],'AND');
				$res3 = $this->getStreet($_GET['area']);
				$result['goback'] = -2;
			}
			
			if($_GET['status'] && ($_GET['status'] != -1)){
				$cri->addCondition('t.status = '.$_GET['status'],'AND');
				$result['status'] = $_GET['status'];
				$result['goback'] = -2;
			}else{
				$result['status'] = -1;
			}
			if ($_GET['phone_model']) {
				$result['phone_model'] = $_GET['phone_model'];
				$cri->addSearchCondition('t.phone_model', $_GET['phone_model'], true, 'AND');
			}
		}
		if (!isset($_GET['dj'])) {
			$result['dj'] = -1;
		}
		$cri->order = "t.created_time desc";
		$pages = new CPagination();
		$pages->itemCount = $model->count($cri);
		$pages->pageSize = 50;
		$pages->applyLimit($cri);
		$items = $model->findAll($cri);
		if ($items) {
			$areaItem = array();
			foreach ($items as $key => $value) {
				$areaItem[] = $value ['province'];
				$areaItem[] = $value ['city'];
				$areaItem[] = $value ['area'];
			}
			$area = new Area();
			$sql = "select bid,  area_name from area where bid in (".implode(",", $areaItem).")";
			$areaResult = $area->findAllBySql($sql);
			foreach ($areaResult as $key => $value) {
				$areaInfo[$value['bid']] = $value['area_name'];
			}
		}
		$url = Yii::app()->request->getUrl();
		$cookie = new CHttpCookie('benben-neverland',$url);
		$cookie->expire = time()+3600;  
		Yii::app()->request->cookies['benben-neverland']=$cookie;

		// 'province' => $province,  'areas' => $areas
		$this->render('index',array('items'=>$items,'pages'=> $pages,
									'result'=>$result, 'msg' => $msg, 'province' => $province, 'res' => $res, 'res2' => $res2,
		                            'res3' => $res3, 'areaInfo'=>$areaInfo));
		
	}

	
	public function actionDelete($id)
	{
		$id = Frame::getIntFromRequest('id');
		if ($id > 0) {
			$model = Member::model ()->findByPk ( $id );
		}
		if ($model) {
			$model->delete();
		}
		$this->redirect ( Yii::app()->createUrl('member/index',array('page'=>intval($_REQUEST['page']))));
	}
	
	//导出Excel
	public function actionPhpexcel() {
		$model = Member::model();
		$cri = new CDbCriteria();
		$cri->select = "t.*,b.short_phone";
		$cri->join = "left join bxapply b on t.id = b.member_id";
		$cri->group = "t.id";
		$province = $this->getProvince();
		$res = $this->getCity();
		$res2 = $this->getArea();
		
		if(isset($_GET) && !empty($_GET)){
			$result = array();
				
			$search = $_GET['benben_id'];
			$phone = $_GET['phone'];
			$name = $_GET['name'];
			$nick_name = $_GET['nick_name'];
			$sex = $_GET['sex'];
			$age1 = $this->birthday($_GET['age1']);
			if($_GET['age2']){
					$age2 = $this->birthday($_GET['age2']+1);
			}	
			$created_time1 = $_GET['created_time1'];
			$created_time2 = $_GET['created_time2'];
			$coin1 = intval($_GET['coin1']);
			$coin2 = intval($_GET['coin2']);
				
			if($search){
				$cri->addSearchCondition('t.benben_id', $search, true, 'AND');
				$result['benben_id'] = $search;
				$result['goback'] = -2;
			}
			if($phone){
				$cri->addSearchCondition('t.phone', $phone, true, 'AND');
				$result['phone'] = $phone;
				$result['goback'] = -2;
			}
			if($name){
				$cri->addSearchCondition('t.name', $name, true, 'AND');
				$result['name'] = $name;
				$result['goback'] = -2;
			}
			if($nick_name){
				$cri->addSearchCondition('t.nick_name', $nick_name, true, 'AND');
				$result['nick_name'] = $nick_name;
				$result['goback'] = -2;
			}
			if($sex){
				if($sex != -1){
					if($sex == 3) {
						$cri->addInCondition('t.sex', array(0));
					}else{
						$cri->addCondition("t.sex=".$sex);
					}
					$result['sex'] = $sex;
					$result['goback'] = -2;
				}
			}
			if($age1 && $age2){
				if($age1 < $age2){
					$msg = "年龄第一个必须比第二个小";
				}else{
					if($age1){
						$cri->addCondition('t.age <= '.$age1,'AND');
						$result['age1'] = $age1;
						$result['goback'] = -2;
					}
					if($age2){
						$cri->addCondition('t.age >= '.$age2,'AND');
						$result['age2'] = $age2;
						$result['goback'] = -2;
					}
				}
			}else{
				if($age1){
					$cri->addCondition('t.age <= '.$age1,'AND');
					$result['age1'] = $age1;
					$result['goback'] = -2;
				}
				if($age2){
					$cri->addCondition('t.age >= '.$age2,'AND');
					$result['age2'] = $age2;
					$result['goback'] = -2;
				}
			}
				
				
			if($created_time1 && $created_time2){
				if(strtotime($created_time1) >= strtotime($created_time2)+86399){
					$msg = "注册日期第一个必须比第二个小";
				}else{
					if($created_time1){
						$cri->addCondition('t.created_time >= '.strtotime($created_time1),'AND');
						$result['created_time1'] = $created_time1;
						$result['goback'] = -2;
					}
					if($created_time2){
						$cri->addCondition('t.created_time <= '.(strtotime($created_time2)+86399),'AND');
						$result['created_time2'] = $created_time2;
						$result['goback'] = -2;
					}
				}
			}else{
				if($created_time1){
					$cri->addCondition('t.created_time >= '.strtotime($created_time1),'AND');
					$result['created_time1'] = $created_time1;
					$result['goback'] = -2;
				}
				if($created_time2){
					$cri->addCondition('t.created_time <= '.(strtotime($created_time2)+86399),'AND');
					$result['created_time2'] = $created_time2;
					$result['goback'] = -2;
				}
			}
			
			if($coin1 && $coin2){
				if($coin1 >= $coin2){
					$msg = "犇币数量第一个必须比第二个小";
				}else{
					if($coin1){
						$cri->addCondition('t.coin >= '.$coin1,'AND');
						$result['coin1'] = $coin1;
						$result['goback'] = -2;
					}
					if($coin2){
						$cri->addCondition('t.coin <= '.$coin2,'AND');
						$result['coin2'] = $coin2;
						$result['goback'] = -2;
					}
				}
			}else{
				if($coin1){
					$cri->addCondition('t.coin >= '.$coin1,'AND');
					$result['coin1'] = $coin1;
					$result['goback'] = -2;
				}
				if($coin2){
					$cri->addCondition('t.coin <= '.$coin2,'AND');
					$result['coin2'] = $coin2;
					$result['goback'] = -2;
				}
			}
			if (isset($_GET['dj']) && $_GET['dj'] != '') {
				$dj = intval($_GET['dj']);
			}else{
				$dj = -1;
			}
				
			if ($dj >= 0) {
				$level_all = $this->getlevel();
				if ($dj > 0) {
					$lower = $level_all[$dj-1][1];
				}else{
					$lower = 0;
				}
				$higher = $level_all[$dj][1];
				$cri->addCondition('t.integral >= '.$lower,'AND');
				$cri->addCondition('t.integral <= '.$higher,'AND');
			
			}
			$result['dj'] = $dj;
			
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
				
			if($_GET['status'] && ($_GET['status'] != -1)){
				$cri->addCondition('t.status = '.$_GET['status'],'AND');
					
				$result['goback'] = -2;
			}
			
			if ($_GET['phone_model']) {
				$result['phone_model'] = $_GET['phone_model'];
				$cri->addSearchCondition('t.phone_model', $_GET['phone_model'], true, 'AND');
			}
		}
		if (!isset($_GET['dj'])) {
			$result['dj'] = -1;
		}
		$cri->addCondition('t.id_enable = 1','AND');
		$cri->order = "t.created_time desc";
		$users = $model->findAll($cri);
	
		$objPHPExcel = new PHPExcel();
		/*--------------设置表头信息------------------*/
		//第一个sheet
		$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A1', '编号')
		->setCellValue('B1', '奔犇号')
		->setCellValue('C1', '手机号码')
		->setCellValue('D1', '姓名')
		->setCellValue('E1', '身份证号码')
		->setCellValue('F1', '性别')
		->setCellValue('G1', '年龄')
		->setCellValue('H1', '地区')
		->setCellValue('I1', '百姓网号')
		->setCellValue('J1', '等级')
		->setCellValue('K1', '积分');
		$objPHPExcel->getActiveSheet()->getStyle('E')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
		
		$level = 0;
		$level_all = $this->getlevel();
		if(!empty($users)){
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
				if($one->sex == 1){$sex = "男";}elseif($one->sex == 2){$sex = "女";}else{$sex = "未知";}
				$pron = $pro_arr[$one->province].''.$pro_arr[$one->city];			
				foreach ($level_all as $va){
					if($one->integral <= $va[1]){
						$level = $va[2];
						break;
					}
				}
				$age = $this->age($one->age);
								
				$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue("A$i", $i-1)
				->setCellValue("B$i", $one->benben_id)
				->setCellValue("C$i", $one->phone)
				->setCellValue("D$i", $one->name)
				// ->setCellValue("E$i", $one->id_card)
				->setCellValue("F$i", $sex)
				->setCellValue("G$i", $age)
				->setCellValue("H$i", $pron)
				->setCellValue("I$i", $one->short_phone)
				->setCellValue("J$i", $level)
				->setCellValue("K$i", $one->integral);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValueExplicit("E$i",
                                 $one->id_card, 
                                 PHPExcel_Cell_DataType::TYPE_STRING);
				$i++;
			} 
		}
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
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
		$objPHPExcel->getActiveSheet()->getStyle('I1')->applyFromArray($styleArray1);
		$objPHPExcel->getActiveSheet()->getStyle('J1')->applyFromArray($styleArray1);
		$objPHPExcel->getActiveSheet()->getStyle('K1')->applyFromArray($styleArray1);
		// $objPHPExcel->getActiveSheet()->getStyle('C')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
		
	
		$objPHPExcel->getActiveSheet()->setTitle('会员信息');      //设置sheet的名称
		$objPHPExcel->setActiveSheetIndex(0);   //设置sheet的起始位置
		
		ob_end_clean();
		ob_start();
		header('Content-Type: application/vnd.ms-excel;charset=utf-8');
		header('Content-Disposition:attachment;filename=' . urlencode('member' . date("YmjHis") .'.xls') . '');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');   //通过PHPExcel_IOFactory的写函数将上面数据写出来
	
		$objWriter->save('php://output');
	}


	public function actionLog()
	{
		$id = Frame::getIntFromRequest('id');
		$connection = Yii::app()->db;
		$sql = "select a.*, b.username as name from member_disable as a left join user as b on a.user_id = b.id where a.member_id = {$id} order by a.created_time desc";
		
		$command = $connection->createCommand($sql);
		$result1 = $command->queryAll();//var_dump($result1);exit();
		
		$this->render('log',array('items'=>$result1));
	}
	

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Member the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Member::model()->findByPk($id);
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
			return Yii::app()->createUrl("member/index",array('page'=>$_REQUEST['page']));
		}
	}

	/**
	 * Performs the AJAX validation.
	 * @param Member $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='member-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
