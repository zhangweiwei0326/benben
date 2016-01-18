<?php
Yii::$enableIncludePath = false;
define('__ROOT__',dirname(dirname(__FILE__)));
require_once (__ROOT__.'/PHPExcel/PHPExcel.php');
class HappyController extends BaseController
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/admin';
	
	/**
	 * @var int the define the index for the menu
	 */
	 
	 public $menuIndex = 43;
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Happy;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Happy']))
		{
			$model->description=$_POST['Happy']['description'];
			$model->user_id= $this->getLoginId();
			$model->created_time = time();
			
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

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Happy']))
		{
			$model->attributes=$_POST['Happy'];
			if($model->save())
				$this->redirect($this->getBackListPageUrl());
		}

		$this->render('update',array(
			'model'=>$model,
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
		$this->insert_log(43);
		$model = Happy::model();
		$cri = new CDbCriteria();
		
		$created_time1= addslashes($_GET['created_time1']);
		$created_time2= addslashes($_GET['created_time2']);
		if($created_time1 && $created_time2){
			$ct1 = strtotime($created_time1);
			$ct2 = strtotime($created_time2)+86399;
		
			if($ct1 >= $ct2){
				$result['msg'] = "发布日期第一个必须比第二个小!";
			}else{
				$cri->addCondition('t.created_time >= '.$ct1,'AND');
				$result['created_time1'] = $created_time1;
				$cri->addCondition('t.created_time <= '.$ct2,'AND');
				$result['created_time2'] = $created_time2;
			}
		}else{
			if($created_time1){
				$cri->addCondition('t.created_time >= '.strtotime($created_time1),'AND');
				$result['created_time1'] = $created_time1;
					
			}
			if($created_time2){
					
				$cri->addCondition('t.created_time <= '.strtotime($created_time2)+86399,'AND');
				$result['created_time2'] = $created_time2;
			}
		}
		
		$cri->select = "t.*, user.username as uname";
		$cri->join  = "left join user on user.id = t.user_id";
		$cri->order = "id desc";
		$pages = new CPagination();
		$pages->itemCount = $model->count($cri);
		$pages->pageSize = 50;
		$pages->applyLimit($cri);
		$items = $model->findAll($cri);
		$this->render('index',array('items'=>$items,'pages'=> $pages));
		
	}

	public function actionInputexcel(){
		$this->menuIndex = 43;
		if ((Yii::app()->request->isPostRequest)) {
		//$inputfile = dirname(__FILE__)."/../../uploads/excel/2015-05/1432029514-1770201150.xls";//Frame::saveExcel("inputfile");
		$inputfile = dirname(__FILE__)."/../..".Frame::saveExcel("inputfile");
		if($_FILES['inputfile']['name']){
			$this->readExcel($inputfile);
			$this->render("inputexcel",array("msg"=>"数据录入成功"));
			exit();
		}else{
			$this->render("inputexcel",array("msg"=>"请选择文件"));
			exit();
		}
				
	}
	$this->render("inputexcel");
	}

	public function actionDownload()
	{
		$objPHPExcel = new PHPExcel();
		/*--------------设置表头信息------------------*/
		//第一个sheet
		$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A1', '编号')
		->setCellValue('B1', '内容');
		
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(50);
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
	
		$objPHPExcel->getActiveSheet()->setTitle('开心一刻');      //设置sheet的名称
		$objPHPExcel->setActiveSheetIndex(0);   //设置sheet的起始位置
		
		ob_end_clean();
		ob_start();
		header('Content-Type: application/vnd.ms-excel;charset=utf-8');
		header('Content-Disposition:attachment;filename=' . urlencode('happy' . date("YmjHis") .'.xls') . '');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');   //通过PHPExcel_IOFactory的写函数将上面数据写出来
	
		$objWriter->save('php://output');
	}
	
	public function actionDelete($id)
	{
		$id = Frame::getIntFromRequest('id');
		if ($id > 0) {
			$model = Happy::model ()->findByPk ( $id );
		}
		if ($model) {
			$model->delete();
		}
		$this->redirect ( Yii::app()->createUrl('happy/index',array('page'=>intval($_REQUEST['page']))));
	}
	

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Happy the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Happy::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
	
	public function getBackListPageUrl()
	{
		return Yii::app()->createUrl("happy/index",array('page'=>$_REQUEST['page']));
	}

	/**
	 * Performs the AJAX validation.
	 * @param Happy $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='happy-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
	function readExcel($filePath){
	
		/**默认用excel2007读取excel，若格式不对，则用之前的版本进行读取*/
		$PHPReader = new PHPExcel_Reader_Excel2007();
	
		if(!$PHPReader->canRead($filePath)){
			$PHPReader = new PHPExcel_Reader_Excel5();
			if(!$PHPReader->canRead($filePath)){
				return 'no Excel';
			}
		}
	
		$PHPExcel = $PHPReader->load($filePath); /**读取excel文件*/
		$currentSheet = $PHPExcel->getSheet(0);   /**取得最大的列号*/
		$allColumn = $currentSheet->getHighestColumn();   /**取得一共有多少行*/
		$allRow = $currentSheet->getHighestRow();
		
	    $userid = $this->getLoginId();
	    $t = time();
	    $con = array();
		for($currentRow = 2;$currentRow <= $allRow;$currentRow++){	
			$description = $this->check_input($currentSheet->getCellByColumnAndRow(1,$currentRow)->getValue());//内容
				
			if($description){
				$con[] ="('{$description}',{$userid},{$t})";											
			}				
		}
		$connection = Yii::app()->db;
		$sql = "insert into happy (description,user_id,created_time) values ".implode(",", $con);
		$command = $connection->createCommand($sql);
		$re = $command->execute();
	}
	//拼写检查
	function check_input($value = '')
	{
		// 去除斜杠
		if (get_magic_quotes_gpc())
		{
			$value = stripslashes($value);
		}
		// 如果不是数字则加引号
		if (!is_numeric($value))
		{
			//$value = mysql_real_escape_string($value);
		}
		$value = htmlspecialchars($value);
		return $value;
	}
	
}
