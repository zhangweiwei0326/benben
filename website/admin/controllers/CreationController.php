<?php
Yii::$enableIncludePath = false;
define('__ROOT__',dirname(dirname(__FILE__)));
require_once (__ROOT__.'/PHPExcel/PHPExcel.php');
class CreationController extends BaseController
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/admin';

	/**
	 * @var int the define the index for the menu
	 */

	public $menuIndex = 40;
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Creation;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Creation']))
		{
			$model->attributes=$_POST['Creation'];
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
		$member = $member->findByPk($model->member_id);
		$index['member'] = $member->name?$member->name:$member->nick_name;
		$index['phone'] = $member->phone;
		$index['status'] = $member->creation_disable;
		$index['benben_id'] = $member->benben_id;
		$connection = Yii::app()->db;
// 		$memberDisable = new MemberDisable;
// 		$sql = "SELECT reason FROM member_disable WHERE member_id = ".$member->id." ORDER BY created_time DESC LIMIT 1";
// 		$reason = $memberDisable->findAllBySql($sql);
// 		$reason = $reason[0]->reason;
// 		$index['reason'] = $reason;
		
		//显示创建人禁用原因
		$serviceDisable = new ServiceDisable();
		$sql = "SELECT status,reason FROM service_disable
					WHERE member_id = ".$model->member_id." and type = 1 ORDER BY created_time DESC LIMIT 1";
		$ereason2 = $serviceDisable->findAllBySql($sql);
		$reason2 = $ereason2[0]->reason;
		$index['reason2'] = $reason2;

		//关注数
		if($id){
			$sql = "select count(*) num from creation_attention where creation_auth_id = {$id}";
			$command = $connection->createCommand($sql);
			$result0 = $command->queryAll();
			$index['attention']= $result0[0]['num'] ? $result0[0]['num'] : 0;
		}
		//评论数
		if($id){
			$sql = "select count(*) num from creation_comment where creation_id = {$id}";
			$command = $connection->createCommand($sql);
			$result0 = $command->queryAll();
			$index['comment']= $result0[0]['num'] ? $result0[0]['num'] : 0;
		}
		//图文
		$creationAttachment = new CreationAttachment();
		$sql = "SELECT attachment FROM creation_attachment WHERE creation_id = ".$model->id;
		$creationAttachment = $creationAttachment->findAllBySql($sql);

		if($model->type == 1){
			$index['video'] =Yii::app()->request->getHostInfo().$creationAttachment[0]->attachment;
		}else{
			foreach ($creationAttachment as $value){
				$str .= $value->attachment.",";
			}
				
			$index['actire'] = explode(",", substr($str, 0, -1));
				
		}

		//原因
		$dreason = new CreationDisable();
		$sql = "SELECT reason FROM creation_disable WHERE creation_id = ".$model->id." ORDER BY created_time DESC";
		$reason = $dreason->findBySql($sql);
		$index['reason'] = $reason->reason;


		if (isset($_POST['Member'])) {
			//改变创建人禁用状态
			$status2 = $_POST['Member']['status'];
			$post_reason2 = $_POST['Member']['reason'];
			if($index['status'] != $status2 || $reason2 != $post_reason2){
				$member->creation_disable = $status2;
				if($member->update()){
					$service = new ServiceDisable();
					$service->member_id = $model->member_id;
					$service->user_id = $this->getLoginId();
					$service->status = $status2;
					$service->reason = $post_reason2;
					$service->type = 1;
					$service->created_time = time();
					$service->save();
				}
			}
// 			if($index['status'] != $_POST['Member']['status'] && $reason != $_POST['Member']['reason']){
// 				$memberDisable->member_id = $model->member_id;
// 				$memberDisable->status = $_POST['Member']['status'];
// 				$memberDisable->user_id = $this->getLoginId();
// 				$memberDisable->reason = $_POST['Member']['reason'];
// 				$memberDisable->created_time = time();
// 				$memberDisable->save();

// 				$member->status = 	$_POST['Member']['status'];	
											
// 				$member->save();
// 			}
		}

		if(isset($_POST['Creation']))
		{
			$status = $_POST['Creation']['status'];
			$post_reason = $_POST['Creation']['reason'];
				
			if($status != $model->status || $post_reason != $reason->reason){
				$dreason->creation_id = $model->id;
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
		$this->insert_log(40);
		$province = $this->getProvince();
		
		$model = Creation::model();
		$cri = new CDbCriteria();

		$name = $_GET['name'];
		$type = $_GET['type'];
		$benben_id = Frame::getStringFromRequest("benben_id");
		$status = Frame::getIntFromRequest("status");
		$status1 = Frame::getIntFromRequest("status1");
		$created_time1= $_GET['created_time1'];
		$created_time2= $_GET['created_time2'];
		
		if($name){
			$member = new Member();
			$sql = "select id From member where name like '%".$name."%' or nick_name like '%".$name."%'";
			$member = $member->findAllBySql($sql);
		
			$str = array();
			if (count($member)>0) {
				foreach ($member as $value){
					$str[] = $value->id;
				}
				$cri->addCondition("member_id in(".implode(",", $str).")");//member_id in(".implode(",", $str).")
			}else{
				$cri->addCondition("member_id < 0");
			}
				
		}
		if(!empty($benben_id)){
				
			$member = new Member();
			//$sql = "SELECT id FROM member WHERE name LIKE '%".$name."%' ";
			$sql = "SELECT id FROM member WHERE benben_id = {$benben_id} ";
			$member = $member->findAllBySql($sql);
			$str1 = array();	
			if(count($member)>0){
				foreach ($member as $va){
					//$str .= $va->id.",";
					$str1[] = $va->id;
				};
				//$str = explode(",", substr($str, 0, -1));
				$cri->addCondition("member_id in(".implode(",", $str1).")");
			}else{
				$cri->addCondition("member_id < 0");
			}
			
			$result['benben_id'] = $benben_id;
		}

		if(!empty($type) && $type != -1){
			if($type == 2){
				$cri->addInCondition('t.type', array(0));
			}else{
				$cri->addCondition('t.type='.$type);
			}
			$result['type'] = $type;
		}

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
		if(isset($_GET['status1']) && ($_GET['status1'] != -1)){
			$cri->addCondition('a.creation_disable = '.$_GET['status1'],'AND');
			$result['status1'] = $_GET['status1'];
			$result['goback'] = -2;
		}
		if($status && $status != -1){
			if($status == 2){
				$cri->addInCondition('t.status', array(0));
			}else{
				$cri->addCondition("t.status = 1");
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
		
		
					
		$cri->select = "t.*, a.name as mname,a.benben_id,a.creation_disable status1,a.nick_name";
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
		$attention = array();
		foreach ($items as $va){
			$cid[] = $va->id;
		}
		$circle_id = implode(",", $cid);
		$connection = Yii::app()->db;
		if($circle_id){
			$sql = "select id, creation_id from creation_comment where creation_id in ({$circle_id})";
			$command = $connection->createCommand($sql);
			$result0 = $command->queryAll();
			foreach ($result0 as $val){
				$comment[$val['creation_id']][] = $val['id'];
			}
			//查询关注数
			$sql = "select id, creation_auth_id from creation_attention where creation_auth_id in ({$circle_id})";
			$command = $connection->createCommand($sql);
			$result1 = $command->queryAll();
			foreach ($result1 as $val){
				$attention[$val['creation_auth_id']][] = $val['id'];
			}
		}
		$url = Yii::app()->request->getUrl();
		$cookie = new CHttpCookie('benben-neverland',$url);
		$cookie->expire = time()+3600;  
		Yii::app()->request->cookies['benben-neverland']=$cookie;

		$this->render('index',array('items'=>$items,'pages'=> $pages, 'result' => $result,
		'province' => $province, 'res' => $res, 'res2' => $res2,'comment'=>$comment,'attention'=>$attention));

	}
//导出excel
public function actionPhpexcel(){
	$model = Creation::model();
	$cri = new CDbCriteria();
	
	$name = $_GET['name'];
	$type = $_GET['type'];
	$benben_id = Frame::getStringFromRequest("benben_id");
	$status = Frame::getIntFromRequest("status");
	$status1 = Frame::getIntFromRequest("status1");
	$created_time1= $_GET['created_time1'];
	$created_time2= $_GET['created_time2'];
	
	if($name){
		$member = new Member();
		$sql = "select id From member where name like '%".$name."%' or nick_name like '%".$name."%'";
		$member = $member->findAllBySql($sql);
	
		$str = array();
		if (count($member)>0) {
			foreach ($member as $value){
				$str[] = $value->id;
			}
			$cri->addCondition("member_id in(".implode(",", $str).")");//member_id in(".implode(",", $str).")
		}else{
			$cri->addCondition("member_id < 0");
		}
			
	}
	if(!empty($benben_id)){
			
		$member = new Member();
		//$sql = "SELECT id FROM member WHERE name LIKE '%".$name."%' ";
		$sql = "SELECT id FROM member WHERE benben_id = {$benben_id} ";
		$member = $member->findAllBySql($sql);
		$str1 = array();	
		if(count($member)>0){
			foreach ($member as $va){
				//$str .= $va->id.",";
				$str1[] = $va->id;
			};
		}
		
		//$str = explode(",", substr($str, 0, -1));
		$cri->addCondition("member_id in(".implode(",", $str1).")");
		$result['benben_id'] = $benben_id;
	}
	
	if(!empty($type) && $type != -1){
		if($type == 2){
			$cri->addInCondition('t.type', array(0));
		}else{
			$cri->addCondition('t.type='.$type);
		}
		$result['type'] = $type;
	}
	
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
	if(isset($_GET['status1']) && ($_GET['status1'] != -1)){
			$cri->addCondition('a.creation_disable = '.$_GET['status1'],'AND');
			$result['status1'] = $_GET['status1'];
			$result['goback'] = -2;
		}
	if($status && $status != -1){
		if($status == 2){
			$cri->addInCondition('t.status', array(0));
		}else{
			$cri->addCondition("t.status = 1");
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
		
	$cri->select = "t.*, a.name as mname,a.benben_id,a.status status1,a.nick_name";
	$cri->join = "left join member a on a.id = t.member_id";
	$cri->order = "t.created_time desc";
	$cri->order = "id desc";
	
	$users = $model->findAll($cri);
	//查询评论数
	$cid = array();
	$comment = array();
	$attention = array();
	foreach ($users as $va){
		$cid[] = $va->id;
	}
	$circle_id = implode(",", $cid);
	$connection = Yii::app()->db;
	if($circle_id){
		$sql = "select id, creation_id from creation_comment where creation_id in ({$circle_id})";
		$command = $connection->createCommand($sql);
		$result0 = $command->queryAll();
		foreach ($result0 as $val){
			$comment[$val['creation_id']][] = $val['id'];
		}
		//查询关注数
		$sql = "select id, creation_auth_id from creation_attention where creation_auth_id in ({$circle_id})";
		$command = $connection->createCommand($sql);
		$result1 = $command->queryAll();
		foreach ($result1 as $val){
			$attention[$val['creation_auth_id']][] = $val['id'];
		}
	}
	$objPHPExcel = new PHPExcel();
	/*--------------设置表头信息------------------*/
	//第一个sheet
	$objPHPExcel->setActiveSheetIndex(0)
	->setCellValue('A1', '发贴人')
	->setCellValue('B1', '奔犇号')
	->setCellValue('C1', '发帖人状态')
	->setCellValue('D1', '状态')
	->setCellValue('E1', '类型')
	->setCellValue('F1', '浏览量')
	->setCellValue('G1', '点赞数')
	->setCellValue('H1', '关注数')
	->setCellValue('I1', '评论数')
	->setCellValue('J1', '地区')
	->setCellValue('K1', '创建时间');
	
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
		$status1 = array('0'=>'启用', '1'=>'禁用1周', '2'=>'禁用2周', '3'=>'禁用1个月', '4'=>'禁用3个月', '5'=>'无限期');
		$status = array("0" => "正常", "1" => "屏蔽");
		$type = array("0" => "图文", "1" => "音频");
	
		$i =2;
		foreach ($users as  $one){
			$pron = $pro_arr[$one->province].''.$pro_arr[$one->city];
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue("A$i", $one->mname ? $one->mname : $one->nick_name)
			->setCellValue("B$i", $one->benben_id)
			->setCellValue("C$i", $status1[$one->status1])
			->setCellValue("D$i", $status[$one->status])
			->setCellValue("E$i", $type[$one->type])
			->setCellValue("F$i", $one->views)
			->setCellValue("G$i", $one->goods)
			->setCellValue("H$i", count($attention[$one->id]))
			->setCellValue("I$i", count($comment[$one->id]))
			->setCellValue("J$i", $pron)
			->setCellValue("K$i", date("Y-m-d H:i:s", $one->created_time));
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
	$objPHPExcel->getActiveSheet()->setTitle('微创作信息');      //设置sheet的名称
	$objPHPExcel->setActiveSheetIndex(0);   //设置sheet的起始位置
	
	ob_end_clean();
	ob_start();
	header('Content-Type: application/vnd.ms-excel;charset=utf-8');
	header('Content-Disposition:attachment;filename=' . urlencode('creation' . date("YmjHis") .'.xls') . '');
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');   //通过PHPExcel_IOFactory的写函数将上面数据写出来
	
	$objWriter->save('php://output');
}

	public function actionDelete($id)
	{
		$id = Frame::getIntFromRequest('id');
		if ($id > 0) {
			$model = Creation::model ()->findByPk ( $id );
		}
		if ($model) {
			$model->delete();
		}
		$this->redirect ( Yii::app()->createUrl('creation/index',array('page'=>intval($_REQUEST['page']))));
	}

	public function actionLog()
	{
		$id = Frame::getIntFromRequest('id');
		$connection = Yii::app()->db;
		$sql = "select a.*, b.username as name from service_disable as a left join user as b on a.user_id = b.id where a.member_id = {$id} and a.type = 1 order by a.created_time desc";
		
		$command = $connection->createCommand($sql);
		$result1 = $command->queryAll();//var_dump($result1);exit();
		
		$this->render('log',array('items'=>$result1));
	}


	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Creation the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Creation::model()->findByPk($id);
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
			return Yii::app()->createUrl("creation/index",array('page'=>$_REQUEST['page']));
		}
	}

	/**
	 * Performs the AJAX validation.
	 * @param Creation $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='creation-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
