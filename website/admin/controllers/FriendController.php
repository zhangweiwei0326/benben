<?php
Yii::$enableIncludePath = false;
define('__ROOT__',dirname(dirname(__FILE__)));
require_once (__ROOT__.'/PHPExcel/PHPExcel.php');
class FriendController extends BaseController
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/admin';
	
	/**
	 * @var int the define the index for the menu
	 */
	 
	 public $menuIndex = 42;
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Friend;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Friend']))
		{
			$model->attributes=$_POST['Friend'];
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
		
		//创建人
		$index = array();
		$member = new Member();
		$sql = "SELECT name,phone FROM member WHERE id = ".$model->member_id;
		$memberInfo = $member->findByPk($model->member_id);
		$index['member'] = $memberInfo['name']?$memberInfo['name']:$memberInfo['nick_name'];
		$index['member_phone'] = $memberInfo['phone'];
		$index['benben_id'] = $memberInfo['benben_id'];
		
		//评论数
		$connection = Yii::app()->db;
		if($id){
			$sql = "select count(*) num from friend_comment where circle_id = {$id}";
			$command = $connection->createCommand($sql);
			$result0 = $command->queryAll();
			$index['comment']= $result0[0]['num'] ? $result0[0]['num'] : 0;
		}
		//图文
		$creationAttachment = new CreationAttachment();
		$sql = "SELECT attachment FROM friend_attachment WHERE circle_id = ".$model->id;
		$creationAttachment = $creationAttachment->findAllBySql($sql);
		
		//原因
		$dreason = new FriendDisable();
		$sql = "SELECT reason FROM friend_disable WHERE circle_id = ".$model->id." ORDER BY created_time DESC";
		$reason = $dreason->findBySql($sql);
		$index['reason'] = $reason->reason;
		
		if($model->type == 1){
			$index['video'] =Yii::app()->request->getHostInfo().$creationAttachment[0]->attachment;
		}else{
			foreach ($creationAttachment as $value){
				$str .= $value->attachment.",";
			}
			
			$index['actire'] = explode(",", substr($str, 0, -1));
			
		}

		if(isset($_POST['Friend']))
		{
			$status = $_POST['Friend']['status'];
			$post_reason = $_POST['Friend']['reason'];
			
			if($status != $model->status || $post_reason != $reason->reason){
				$dreason->circle_id = $model->id;
				$dreason->user_id = $this->getLoginId();
				$dreason->reason = $post_reason;
				$dreason->created_time = time();
				$dreason->status = $status;
				$dreason->save();
			}
			
			$model->status=$status;
			if($model->save())
				$this->redirect($this->getBackListPageUrl());
		}
		
		
		$model->type = $model->type == 0 ? "图文" : "音频";
		$model->created_time = date('Y-m-d H:i:s', $model->created_time);

		$this->render('update',array(
			'model'=>$model,
			'index' => $index,
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
		$this->insert_log(42);
		$province = $this->getProvince();
		
		$model = Friend::model();
		$cri = new CDbCriteria();
		
		$name = $_GET['name'];
		$type = $_GET['type'];
		$status = $_GET['status'];
		$created_time1= $_GET['created_time1'];
		$created_time2= $_GET['created_time2'];
		
		if(!empty($name)){
			
			$member = new Member();
			//$sql = "SELECT id FROM member WHERE name LIKE '%".$name."%' ";
			$sql = "SELECT id FROM member WHERE benben_id = {$name} ";
			$member = $member->findAllBySql($sql);
			
			if($member){
				foreach ($member as $va){
					$str .= $va->id.",";
				};			
				$str = explode(",", substr($str, 0, -1));
				$cri->addInCondition('member_id', $str);
				$result['name'] = $name;
			}
		}
		
		if(!empty($type) && $type != -1){
			if($type == 2){
				$cri->addInCondition('t.type', array(0));
			}else{
				$cri->addCondition('t.type='.$type);
			}
			$result['type'] = $type;
		}
		
		if(!empty($status) && $status != -1){
			if($status == 2){
				$cri->addInCondition('t.status', array(0));
			}else{
				$cri->addCondition('t.status='.$status);
			}
			$result['status'] = $status;
		}
		
		if($created_time1 && $created_time2){
				$ct1 = strtotime($created_time1);
				$ct2 = strtotime($created_time2)+86399;

				if($ct1 >= $ct2){
					$result['msg'] = "注册日期第一个必须比第二个小!";
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
			if($_GET['province'] && ($_GET['province'] != -1)){
				$cri->addCondition('a.province = '.$_GET['province'],'AND');
				$result['province'] = $_GET['province'];
				$result['goback'] = -2;
				$res = $this->getCity($_GET['province']);
			}
			
			if($_GET['city'] && ($_GET['city'] != -1)){
				$cri->addCondition('a.city = '.$_GET['city'],'AND');			
				$result['goback'] = -2;
				$res2 = $this->getArea($post_city);
				$result['city'] = $_GET['city'];
			}
			
			if($_GET['area'] && ($_GET['area'] != -1)){
				$cri->addCondition('a.area = '.$_GET['area'],'AND');			
				$result['goback'] = -2;
				$result['area'] = $_GET['area'];
			}
			
			
			if(!empty($_GET['backurl'])){
				$result['goback'] = -2;
				$result['area'] = $_GET['area'];
			}
		
		$cri->select = "t.*, a.name as mname,a.benben_id,a.nick_name";
		$cri->join = "left join member a on a.id = t.member_id";
		$cri->order = "t.created_time desc";
		$cri->order = "id desc";
		$pages = new CPagination();
		$pages->itemCount = $model->count($cri);
		$pages->pageSize = 50;
		$pages->applyLimit($cri);
		$items = $model->findAll($cri);
		//查询评论数
		$cid = array();
		$comment = array();
		foreach ($items as $va){
			$cid[] = $va->id;
		}
		$circle_id = implode(",", $cid);
		$connection = Yii::app()->db;
		if($circle_id){
			$sql = "select id, circle_id from friend_comment where circle_id in ({$circle_id})";
			$command = $connection->createCommand($sql);
			$result0 = $command->queryAll();
			foreach ($result0 as $val){
				$comment[$val['circle_id']][] = $val['id'];
			}
		}	
		$url = Yii::app()->request->getUrl();
		$cookie = new CHttpCookie('benben-neverland',$url);
		$cookie->expire = time()+3600;  
		Yii::app()->request->cookies['benben-neverland']=$cookie;	
		
		$this->render('index',array('items'=>$items,'pages'=> $pages, 'result' => $result,
		'province' => $province, 'res' => $res, 'res2' => $res2,'comment'=>$comment));
		
	}
//导出excel
public function actionPhpexcel(){

	$model = Friend::model();
	$cri = new CDbCriteria();
	
	$name = $_GET['name'];
	$type = $_GET['type'];
	$status = $_GET['status'];
	$created_time1= $_GET['created_time1'];
	$created_time2= $_GET['created_time2'];
	
	if(!empty($name)){
			
		$member = new Member();
		//$sql = "SELECT id FROM member WHERE name LIKE '%".$name."%' ";
		$sql = "SELECT id FROM member WHERE benben_id = {$name} ";
		$member = $member->findAllBySql($sql);
			
		if($member){
			foreach ($member as $va){
				$str .= $va->id.",";
			};
			$str = explode(",", substr($str, 0, -1));
			$cri->addInCondition('member_id', $str);
			$result['name'] = $name;
		}
	}
	
	if(!empty($type) && $type != -1){
		if($type == 2){
			$cri->addInCondition('t.type', array(0));
		}else{
			$cri->addCondition('t.type='.$type);
		}
		$result['type'] = $type;
	}
	
	if(!empty($status) && $status != -1){
		if($status == 2){
			$cri->addInCondition('t.status', array(0));
		}else{
			$cri->addCondition('t.status='.$status);
		}
		$result['status'] = $status;
	}
	
	if($created_time1 && $created_time2){
		$ct1 = strtotime($created_time1);
		$ct2 = strtotime($created_time2)+86399;
	
		if($ct1 >= $ct2){
			$result['msg'] = "注册日期第一个必须比第二个小!";
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
	if($_GET['province'] && ($_GET['province'] != -1)){
		$cri->addCondition('a.province = '.$_GET['province'],'AND');
		$result['province'] = $_GET['province'];
		$result['goback'] = -2;
	}
		
	if($_GET['city'] && ($_GET['city'] != -1)){
		$cri->addCondition('a.city = '.$_GET['city'],'AND');
		$result['goback'] = -2;
	}
		
	if($_GET['area'] && ($_GET['area'] != -1)){
		$cri->addCondition('a.area = '.$_GET['area'],'AND');
		$result['goback'] = -2;
	}
	
	if($_GET['street'] && ($_GET['street'] != -1)){
		$cri->addCondition('a.street = '.$_GET['street'],'AND');
		$result['goback'] = -2;
	}
		
	if(!empty($_GET['backurl'])){
		$result['goback'] = -2;
	}
	
	$cri->select = "t.*, a.name as mname,a.benben_id,a.nick_name";
	$cri->join = "left join member a on a.id = t.member_id";
	$cri->order = "t.created_time desc";
	$cri->order = "id desc";
	$users = $model->findAll($cri);
	//查询评论数
	$cid = array();
	$comment = array();
	foreach ($users as $va){
		$cid[] = $va->id;
	}
	$circle_id = implode(",", $cid);
	$connection = Yii::app()->db;
	if($circle_id){
		$sql = "select id, circle_id from friend_comment where circle_id in ({$circle_id})";
		$command = $connection->createCommand($sql);
		$result0 = $command->queryAll();
		foreach ($result0 as $val){
			$comment[$val['circle_id']][] = $val['id'];
		}
	}
	$objPHPExcel = new PHPExcel();
	/*--------------设置表头信息------------------*/
	//第一个sheet
	$objPHPExcel->setActiveSheetIndex(0)
	->setCellValue('A1', '发贴人')
	->setCellValue('B1', '奔犇号')
	->setCellValue('C1', '状态')
	->setCellValue('D1', '类型')
	->setCellValue('E1', '浏览量')
	->setCellValue('F1', '点赞数')
	->setCellValue('G1', '评论数')
	->setCellValue('H1', '创建时间');
	
	if(!empty($users)){
		$status = array("0" => "正常", "1" => "屏蔽");
        $type = array("0" => "图文", "1" => "音频");
	
		$i =2;
		foreach ($users as  $one){
	
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue("A$i", $one->mname ? $one->mname : $one->nick_name)
			->setCellValue("B$i", $one->benben_id)
			->setCellValue("C$i", $status[$one->status])
			->setCellValue("D$i", $type[$one->type])
			->setCellValue("E$i", $one->views)
			->setCellValue("F$i", $one->goods)
			->setCellValue("G$i", count($comment[$one->id]))
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
	$objPHPExcel->getActiveSheet()->setTitle('朋友圈信息');      //设置sheet的名称
	$objPHPExcel->setActiveSheetIndex(0);   //设置sheet的起始位置
	
	ob_end_clean();
	ob_start();
	header('Content-Type: application/vnd.ms-excel;charset=utf-8');
	header('Content-Disposition:attachment;filename=' . urlencode('friend' . date("YmjHis") .'.xls') . '');
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');   //通过PHPExcel_IOFactory的写函数将上面数据写出来
	
	$objWriter->save('php://output');
}
	
	public function actionDelete($id)
	{
		$id = Frame::getIntFromRequest('id');
		if ($id > 0) {
			$model = Friend::model ()->findByPk ( $id );
		}
		if ($model) {
			$model->delete();
		}
		$this->redirect ( Yii::app()->createUrl('friend/index',array('page'=>intval($_REQUEST['page']))));
	}
	

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Friend the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Friend::model()->findByPk($id);
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
			return Yii::app()->createUrl("friend/index",array('page'=>$_REQUEST['page']));
		}
	}

	/**
	 * Performs the AJAX validation.
	 * @param Friend $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='friend-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
