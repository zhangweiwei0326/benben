<?php
Yii::$enableIncludePath = false;
define('__ROOT__',dirname(dirname(__FILE__)));
require_once (__ROOT__.'/PHPExcel/PHPExcel.php');
class LoginLogController extends BaseController
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/admin';
	
	/**
	 * @var int the define the index for the menu
	 */
	 
	public $menuIndex = 83;


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
		
		if(isset($_POST['Member']))
		{	
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
				$this->redirect($this->getBackListPageUrl());
		}
		$model->created_time =  date('Y-m-d H:i:s', $model->created_time);
		//记录
		$connection = Yii::app()->db;
		$sql = "select a.*, b.username as name from member_disable as a left join user as b on a.user_id = b.id where a.member_id = {$id} order by a.created_time desc";
		$command = $connection->createCommand($sql);
		$result1 = $command->queryAll();//var_dump($result1);exit();
		$info = array();
		$status = array('0'=>'启用', '1'=>'禁用1周', '2'=>'禁用2周', '3'=>'禁用1个月', '4'=>'禁用3个月', '5'=>'无限期禁用');
		foreach ($result1 as $va){			
               $info[] = array("created_time"=>$va['created_time'],"name"=>$va['name'],"reason"=>$va['reason'],"status"=>$status[$va['status']]);							
		}
		
		$this->render('update',array(
			'model'=>$model,
			'sex' => $model->sex,
			'reason' => $reason,
			'areas' => $areas,
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
		$this->insert_log(83);
		$model = LoginLog::model();
		$cri = new CDbCriteria();
		$cri->select = "t.*";
		//$cri->join = "left join user b on t.id = b.member_id";
		//$cri->group = "t.id";
		$cri->addCondition('t.type = 0');
						
		if(isset($_GET) && !empty($_GET)){
			$result = array();						
			$name = addslashes($_GET['name']);
			$loginip = addslashes($_GET['loginip']);
			$created_time1 = $_GET['created_time1'];
			$created_time2 = $_GET['created_time2'];			
			
			if($name){
				$cri->addSearchCondition('t.username', $name, true, 'AND');
				$result['name'] = $name;
				$result['goback'] = -2;
			}
			if($loginip){
				$cri->addCondition("t.loginip like '{$loginip}%'",'AND');
				$result['loginip'] = $loginip;
				$result['goback'] = -2;
			}
															
			if($created_time1 && $created_time2){
				if(strtotime($created_time1) >= (strtotime($created_time2)+86399)){
						$msg = "注册日期第一个必须比第二个小";			
				}else{
					if($created_time1){
						$cri->addCondition('t.logintime >= '.strtotime($created_time1),'AND');
						$result['created_time1'] = $created_time1;
						$result['goback'] = -2;
					}
					if($created_time2){
						$cri->addCondition('t.logintime <= '.(strtotime($created_time2)+86399),'AND');
						$result['created_time2'] = $created_time2;
						$result['goback'] = -2;
					}
				}
			}else{
				if($created_time1){
					$cri->addCondition('t.logintime >= '.strtotime($created_time1),'AND');
					$result['created_time1'] = $created_time1;
					$result['goback'] = -2;
				}
				if($created_time2){
					$cri->addCondition('t.logintime <= '.(strtotime($created_time2)+86399),'AND');
					$result['created_time2'] = $created_time2;
					$result['goback'] = -2;
				}
			}
																		
			
			if(isset($_GET['status']) && ($_GET['status'] != -1)){
				$cri->addCondition('t.status = '.$_GET['status'],'AND');
				$result['status'] = $_GET['status'];
				$result['goback'] = -2;
			}else{
				$result['status'] = -1;
			}
			
		}
		
		$cri->order = "t.logintime desc";
		$pages = new CPagination();
		$pages->itemCount = $model->count($cri);
		$pages->pageSize = 50;
		$pages->applyLimit($cri);
		$items = $model->findAll($cri);				
		
		$this->render('index',array('items'=>$items,'pages'=> $pages,
									'result'=>$result, 'msg' => $msg));
		
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
		$model = LoginLog::model();
		$cri = new CDbCriteria();
		$cri->select = "t.*";
		//$cri->join = "left join user b on t.id = b.member_id";
		//$cri->group = "t.id";
		$cri->addCondition('t.type = 0');
		
		if(isset($_GET) && !empty($_GET)){
			$result = array();
			$name = addslashes($_GET['name']);
			$loginip = addslashes($_GET['loginip']);
			$created_time1 = $_GET['created_time1'];
			$created_time2 = $_GET['created_time2'];
				
			if($name){
				$cri->addSearchCondition('t.username', $name, true, 'AND');
				$result['name'] = $name;
				$result['goback'] = -2;
			}
			if($loginip){
				$cri->addCondition("t.loginip like '{$loginip}%'",'AND');
				$result['loginip'] = $loginip;
				$result['goback'] = -2;
			}
				
			if($created_time1 && $created_time2){
				if(strtotime($created_time1) >= (strtotime($created_time2)+86399)){
					$msg = "注册日期第一个必须比第二个小";
				}else{
					if($created_time1){
						$cri->addCondition('t.logintime >= '.strtotime($created_time1),'AND');
						$result['created_time1'] = $created_time1;
						$result['goback'] = -2;
					}
					if($created_time2){
						$cri->addCondition('t.logintime <= '.(strtotime($created_time2)+86399),'AND');
						$result['created_time2'] = $created_time2;
						$result['goback'] = -2;
					}
				}
			}else{
				if($created_time1){
					$cri->addCondition('t.logintime >= '.strtotime($created_time1),'AND');
					$result['created_time1'] = $created_time1;
					$result['goback'] = -2;
				}
				if($created_time2){
					$cri->addCondition('t.logintime <= '.(strtotime($created_time2)+86399),'AND');
					$result['created_time2'] = $created_time2;
					$result['goback'] = -2;
				}
			}
		
				
			if(isset($_GET['status']) && ($_GET['status'] != -1)){
				$cri->addCondition('t.status = '.$_GET['status'],'AND');
				$result['status'] = $_GET['status'];
				$result['goback'] = -2;
			}else{
				$result['status'] = -1;
			}
				
		}
		
		$cri->order = "t.logintime desc";
		
		$users = $model->findAll($cri);
	
		$objPHPExcel = new PHPExcel();
		/*--------------设置表头信息------------------*/
		//第一个sheet
		$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A1', '编号')
		->setCellValue('B1', '用户名')
		->setCellValue('C1', '登录IP')
		->setCellValue('D1', '登录时间')
		->setCellValue('E1', '状态');
		$level = 0;
		$level_all = $this->getlevel();
		if(!empty($users)){
												
			$i =2;		
			foreach ($users as  $one){
												
				$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue("A$i", $one->id)
				->setCellValue("B$i", $one->username)
				->setCellValue("C$i", $one->loginip)
				->setCellValue("D$i", date("Y-m-d H:i:s",$one->logintime))
				->setCellValue("E$i", $one->status ? "登录成功" : "登录失败");
				$i++;
			} 
		}
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
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
	
		$objPHPExcel->getActiveSheet()->setTitle('登录信息');      //设置sheet的名称
		$objPHPExcel->setActiveSheetIndex(0);   //设置sheet的起始位置
		
		ob_end_clean();
		ob_start();
		header('Content-Type: application/vnd.ms-excel;charset=utf-8');
		header('Content-Disposition:attachment;filename=' . urlencode('loginlog' . date("YmjHis") .'.xls') . '');
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
