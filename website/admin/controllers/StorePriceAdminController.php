<?php

class StorePriceAdminController extends BaseController
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/admin';
	
	/**
	 * @var int the define the index for the menu
	 */
	 
	 public $menuIndex = 70;
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new StorePriceAdmin;
		$type = Frame::getIntFromRequest("type");
		$model->type = $type;
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['StorePriceAdmin']['names']))
		{
			if($_POST['StorePriceAdmin']['names']){
				$model->attributes=$_POST['StorePriceAdmin'];
				$model->add_date = time();
				$model->update_date = time();
				if($model->save())
					$this->redirect($this->getBackListPageUrl());

			}else{
				$msg = "请输入套餐";
			}

		}

		$this->render('create',array(
			'model'=>$model,
			'msg'=>$msg,
			'backUrl' => $this->getBackListPageUrl(),
		));
	}

	public function actionUpload(){
		echo $this->Upload();
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

		if(isset($_POST['StorePriceAdmin']))
		{
			$model->attributes=$_POST['StorePriceAdmin'];
			if($model->type == 11){
				$arr_n = array();
				$names_arr = $_POST['StorePriceAdmin']['names'];
				foreach($names_arr as $va){
					if($va[0]){
						$va_str = implode(":",$va);
						if($va_str) $arr_n[] = $va_str;
					}

				}
				$model->names = implode(",",$arr_n);
			}

			$model->update_date = time();

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
		$model = StorePriceAdmin::model();
		$cri = new CDbCriteria();
		$cri->order = "id desc";
		$pages = new CPagination();
		$pages->itemCount = $model->count($cri);
		$pages->pageSize = 12;
		$pages->applyLimit($cri);
		$items = $model->findAll($cri);
		$this->render('index',array('items'=>$items,'pages'=> $pages));
		
	}

	
	public function actionDelete($id)
	{
		$id = Frame::getIntFromRequest('id');
		if ($id > 0) {
			$model = StorePriceAdmin::model ()->findByPk ( $id );
		}
		if ($model) {
			$model->delete();
		}
		$this->redirect ( Yii::app()->createUrl('storePriceAdmin/index',array('page'=>intval($_REQUEST['page']))));
	}
	

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return StorePriceAdmin the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=StorePriceAdmin::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
	
	public function getBackListPageUrl()
	{
		return Yii::app()->createUrl("storePriceAdmin/index",array('page'=>$_REQUEST['page']));
	}

	/**
	 * Performs the AJAX validation.
	 * @param StorePriceAdmin $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='store-price-admin-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
