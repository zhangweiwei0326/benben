<?php

class ComplainController extends BaseController
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/admin';
	
	/**
	 * @var int the define the index for the menu
	 */
	 
	 public $menuIndex = 93;
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Complain;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

//		if(isset($_POST['Complain']))
//		{
//			$model->attributes=$_POST['Complain'];
//			if($model->save())
//				$this->redirect($this->getBackListPageUrl());
//		}

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
		$member = new Member();
		$apply1=ApplyRegister::model();
		if($model->member_id){
			$sql = "select * from member where id = ".$model->member_id;
			$member = $member->findBySql($sql);
			$area = "";
			if ($member->province > 0) {
				$area .= $this->areas($member->province)." ";			
			}
			if ($member->city > 0) {
				$area .= $this->areas($member->city)." ";		
			}		
			if ($member->area > 0) {
				$area .= $this->areas($member->area)." ";			
			}
			if ($member->street > 0) {
				$area .= $this->areas($member->street);
			}
			
			$model->member_id = $member->name;
			$model->benben_id = $member->benben_id;
			$model->phone = $member->phone;
			$model->sex = $member->sex;
			$model->area = $area;
			$model->created_time = date('Y-m-d H:i:s', $model->created_time);
		}else{		
			$sql = "select name,phone from apply_register where id = ".$model->apply_id;
			$apply2=$apply1->findBySql($sql);
			$model->member_id = $apply2->name;
			$model->phone = $apply2->phone;
			$model->created_time = date('Y-m-d H:i:s', $model->created_time);
		}
		$this->render('update',array(
			'name' => $member->nick_name,	
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
		$this->insert_log(93);
		$model = Complain::model();
		$cri = new CDbCriteria();
		
		$province = $this->getProvince();
		
		$result = array();
		$benben_id = intval($_GET['benben_id']);
		$phone = intval($_GET['phone']);
		$post_province = intval($_GET['province']);
		$post_city = intval($_GET['city']);
		$post_area = intval($_GET['area']);
		$post_street = intval($_GET['street']);
		if ($post_province > 0) {
			$res = $this->getCity($post_province);
			$cri->addCondition('a.province = '.$post_province,'AND');
			$result['province'] = $post_province;
		}
		if ($post_city > 0) {
			$res2 = $this->getArea($post_city);
			$cri->addCondition('a.city = '.$post_city,'AND');
			$result['city'] = $post_city;
		}
		
		if ($post_area > 0) {
			$res3 = $this->getStreet($post_area);
			$cri->addCondition('a.area = '.$post_street,'AND');
			$result['area'] = $post_area;
		}
		if ($post_street > 0) {
			$cri->addCondition('a.street = '.$post_street,'AND');
			$result['street'] = $post_street;
		}
		if($benben_id){
			$cri->addCondition('a.benben_id = '.$benben_id,'AND');
			$result['benben_id'] = $benben_id;
		}
		
		if($phone){
			$cri->addCondition('a.phone = '.$phone.' or b.phone='.$phone,'AND');
			$result['phone'] = $phone;
		}

		
		$cri->select = "t.*, b.phone as bphone, b.name as bname, a.nick_name as sname ,a.benben_id,a.phone";
		$cri->join = "left join member a on a.id = t.member_id
							 left join apply_register b on t.apply_id = b.id";
		$cri->order = "t.created_time desc";
		$pages = new CPagination();
		$pages->itemCount = $model->count($cri);
		$pages->pageSize = 50;
		$pages->applyLimit($cri);
		$items = $model->findAll($cri);
		$this->render('index',array('items'=>$items,'pages'=> $pages,'result' => $result, 
				'province' => $province,'res' => $res, 'res2' => $res2,
		        'res3' => $res3));
		
	}

	
	public function actionDelete($id)
	{
//		$id = Frame::getIntFromRequest('id');
//		if ($id > 0) {
//			$model = Complain::model ()->findByPk ( $id );
//		}
//		if ($model) {
//			$model->delete();
//		}
//		$this->redirect ( Yii::app()->createUrl('complain/index',array('page'=>intval($_REQUEST['page']))));
	}
	

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Complain the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Complain::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
	
	public function getBackListPageUrl()
	{
		return Yii::app()->createUrl("complain/index",array('page'=>$_REQUEST['page']));
	}

	/**
	 * Performs the AJAX validation.
	 * @param Complain $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='complain-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
