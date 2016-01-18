<?php

class CreationCommentController extends BaseController
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
		$model=new CreationComment;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['CreationComment']))
		{
			$model->attributes=$_POST['CreationComment'];
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

		if(isset($_POST['CreationComment']))
		{
			$model->attributes=$_POST['CreationComment'];
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
		$model = CreationComment::model();
		$cri = new CDbCriteria();
		
		$id = intval($_GET['id']);
		$cri->select = "t.*, member.name as mname, member.benben_id,creation.description as cname";
		$cri->join = "left join member on member.id = t.member_id
								left join creation on creation.id = t.creation_id";
		$cri->order = "t.created_time desc";
		$cri->addCondition("t.creation_id = ".$id);
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
			$model = CreationComment::model ()->findByPk ( $id );
		}
		if ($model) {
			$model->delete();
		}
		$this->redirect ( Yii::app()->createUrl('creationComment/index',array('page'=>intval($_REQUEST['page']))));
	}
	

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return CreationComment the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=CreationComment::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
	
	public function getBackListPageUrl()
	{
		return Yii::app()->createUrl("creationComment/index",array('page'=>$_REQUEST['page']));
	}

	/**
	 * Performs the AJAX validation.
	 * @param CreationComment $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='creation-comment-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
