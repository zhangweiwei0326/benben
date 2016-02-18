<?php

class ApplyRegisterController extends BaseController
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/admin';
	
	/**
	 * @var int the define the index for the menu
	 */
	 
	 public $menuIndex = 33;
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new ApplyRegister;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['ApplyRegister']))
		{
			$model->attributes=$_POST['ApplyRegister'];
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

		if(isset($_POST['ApplyRegister']))
		{
			$model->attributes=$_POST['ApplyRegister'];
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
		
		$model = ApplyRegister::model();
		$cri = new CDbCriteria();
		
//检索
		$result = array();
		$apply_name = $_GET['apply_name'];
		$enterprise_name = $_GET['enterprise_name'];
		$status = intval($_GET['status']);
		$review_name=$_GET['review_name'];
		$created_time1= $_GET['created_time1'];
		$created_time2= $_GET['created_time2'];
		$review_time1= $_GET['review_time1'];
		$review_time2= $_GET['review_time2'];
		$apply_type=$_GET['apply_type'];
		$enterprise_type=$_GET['enterprise_type'];
// 		if($_GET){
// 			p($_GET);die();
// 		}
		//申请名称
		if(isset($_GET['$apply_name']) ||!empty($apply_name)){
		$cri->addSearchCondition('t.name', $apply_name, true, 'AND');
			$result['apply_name'] = $apply_name;
		}
		//政企通讯录名称
		if(isset($_GET['$enterprise_name']) ||!empty($enterprise_name)){
			$cri->addSearchCondition('t.enterprise_name', $enterprise_name, true, 'AND');
			$result['enterprise_name'] = $enterprise_name;
		}
		//审核状态
		if(isset($status) && $status != 0&&$status != -1){
			$status1=$status-1;
			$cri->addCondition("t.status = ".$status1,'AND');
			$result['status'] = $status;
		}
		//审核人
		if(isset($_GET['$review_name']) ||!empty($review_name)){
			$cri->addSearchCondition('t.review_name', $review_name, true, 'AND');
			$result['review_name'] = $review_name;
		}
		
		//申请时间
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
		//审核时间
		if($review_time1 && $review_time2){
			$ct1 = strtotime($review_time1);
			$ct2 = strtotime($review_time2)+86399;
		
			if($ct1 >= $ct2){
				$result['msg'] = "注册日期第一个必须比第二个小!";
			}else{
				$cri->addCondition('t.created_time >= '.$ct1,'AND');
				$result['created_time1'] = $review_time1;
				$cri->addCondition('t.created_time <= '.$ct2,'AND');
				$result['created_time2'] = $review_time2;
			}
		}else{
			if($review_time1){
				$cri->addCondition('t.created_time >= '.strtotime($review_time1),'AND');
				$result['created_time1'] = $review_time1;
					
			}
			if($review_time2){
				$cri->addCondition('t.created_time <= '.strtotime($review_time2)+86399,'AND');
				$result['created_time2'] = $review_time2;
			}
		}
		//申请类型
		if(isset($apply_type) && $apply_type != -1){
			$cri->addCondition("t.apply_type = ".$apply_type,'AND');
			$result['apply_type'] = $apply_type;
		}
		//政企类型
		if(isset($enterprise_type) && $enterprise_type != -1){
			$cri->addCondition("t.enterprise_type = ".$enterprise_type,'AND');
			$result['enterprise_type'] = $enterprise_type;
		}
		
	
		$cri->order = "id desc";
		$pages = new CPagination();
		$pages->itemCount = $model->count($cri);
		$pages->pageSize = 50;
		$pages->applyLimit($cri);
		$items = $model->findAll($cri);
		$this->render('index',array('items'=>$items,'pages'=> $pages,'result' => $result));
		
	}
	/**
	 * 申请审核
	 * */
	public function actionReview($id)
	{
		$model=$this->loadModel($id);
	
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
		
	//审核通过
		if(isset($_POST['ApplyRegister']))
		{
			$model1=new Enterprise();
			$model1->name=$model->enterprise_name;
			$model1->type=$model->enterprise_type;
			$model1->created_time=$model->created_time;
			//政企通讯录创建方式：后台
			$model1->origin=2;
// 			if($model->apply_type==3){
// 				$model1->number=50000;}else{
// 					$model1->number=500;
// 				}	
			
			if($model1->save())
				//审核通过修改申请表状态，政企通讯录id
				$model->status=1;
				$model->enterprise_id=$model1->id;
				$model->review_name=Yii::app()->user->id;
				$model->review_time=time();
				
				//审核通过添加政企权限
				$role=new EnterpriseRole();
				$role->enterprise_id=$model1->id;				
				$role->enterprise_apply=1;				
				$role->member_add=1;
				$role->access_level=1;
				if($model->apply_type==3){
					$role->member_limit=50000;
					$role->broadcast_num=200;
					$role->broadcast_available=200;
					$role->group_level=4;
					$role->manage_num=5;
					$role->access_level_set=1;
				}else{
					$role->member_limit=500;
					$role->broadcast_num=10;
					$role->broadcast_available=10;
					$role->group_level=0;
					$role->manage_num=1;
					$role->access_level_set=0;
				}
				$role->created_time=time();
				if($model->save()&&$role->save()){
				$this->redirect($this->getBackListPageUrl());
				}else{
				    var_dump($model->getErrors());
				}
		}
		
		//审核拒绝
		if(isset($_GET['status'])){
				if($_GET['status']=='reject'){
					$model->status=2;
					$model->review_name=Yii::app()->user->id ;
					$model->review_time=time();
					if($model->save())
					      $this->redirect('/admin.php/applyRegister');
				}
			}
			
		$model->created_time = date('Y-m-d H:i:s', $model->created_time);
		$this->render('review',array(
				'model'=>$model,
				'backUrl' => $this->getBackListPageUrl(),
		));
	}
	
	
	public function actionDelete($id)
	{
		$id = Frame::getIntFromRequest('id');
		if ($id > 0) {
			$model = ApplyRegister::model ()->findByPk ( $id );
		}
		if ($model) {
			$model->delete();
		}
		$this->redirect ( Yii::app()->createUrl('applyRegister/index',array('page'=>intval($_REQUEST['page']))));
	}
	

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return ApplyRegister the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=ApplyRegister::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
	
	public function getBackListPageUrl()
	{
		return Yii::app()->createUrl("applyRegister/index",array('page'=>$_REQUEST['page']));
	}

	/**
	 * Performs the AJAX validation.
	 * @param ApplyRegister $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='apply-register-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}