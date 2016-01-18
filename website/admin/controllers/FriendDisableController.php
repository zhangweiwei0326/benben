<?php

class FriendDisableController extends BaseController
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
		$model=new FriendDisable;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['FriendDisable']))
		{
			$model->attributes=$_POST['FriendDisable'];
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

		if(isset($_POST['FriendDisable']))
		{
			$model->attributes=$_POST['FriendDisable'];
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
		$model = FriendDisable::model();
		$cri = new CDbCriteria();
		$cri->select ="t.*, user.username as uname, friend.description as fname";
		$cri->join = "left join user on user.id = t.user_id 
								left join friend on friend.id = t.circle_id";
		$cri->order = "id desc";
		$cri->addCondition("t.circle_id = ".$_GET['id']);
		$pages = new CPagination();
		$pages->itemCount = $model->count($cri);
		$pages->pageSize = 12;
		$pages->applyLimit($cri);
		$items = $model->findAll($cri);
		$this->render('index',array('items'=>$items,'pages'=> $pages));
		
	}
	
	public function actionLindex()
	{
		$this->menuIndex = 50;
		$model = FriendLeagueDisable::model();
		$cri = new CDbCriteria();
		$cri->select ="t.*, user.username as uname,a.name fname";
		$cri->join = "left join user on user.id = t.user_id 
				left join friend_league a on a.id = t.league_id";
		$cri->order = "id desc";
		$cri->addCondition("t.league_id = ".$_GET['id']);
		$pages = new CPagination();
		$pages->itemCount = $model->count($cri);
		$pages->pageSize = 12;
		$pages->applyLimit($cri);
		$items = $model->findAll($cri);
		$this->render('friendindex',array('items'=>$items,'pages'=> $pages,'type'=>1));
	
	}
	
	public function actionService()
	{
		$this->menuIndex = 50;
		$model = ServiceDisable::model();
		$cri = new CDbCriteria();
		$cri->select ="t.*, user.username as uname,a.nick_name fname";
		$cri->join = "left join user on user.id = t.user_id 
				left join member a on a.id = t.member_id";
		$cri->order = "id desc";
		$cri->addCondition("t.member_id = ".$_GET['id']);
		$cri->addCondition('t.type = 6' ,'AND');
		$pages = new CPagination();
		$pages->itemCount = $model->count($cri);
		$pages->pageSize = 12;
		$pages->applyLimit($cri);
		$items = $model->findAll($cri);
		$this->render('friendindex',array('items'=>$items,'pages'=> $pages,'type'=>0));
	
	}

	
	public function actionDelete($id)
	{
		$id = Frame::getIntFromRequest('id');
		if ($id > 0) {
			$model = FriendDisable::model ()->findByPk ( $id );
		}
		if ($model) {
			$model->delete();
		}
		$this->redirect ( Yii::app()->createUrl('friendDisable/index',array('page'=>intval($_REQUEST['page']))));
	}
	

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return FriendDisable the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=FriendDisable::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
	
	public function getBackListPageUrl()
	{
		return Yii::app()->createUrl("friendDisable/index",array('page'=>$_REQUEST['page']));
	}

	/**
	 * Performs the AJAX validation.
	 * @param FriendDisable $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='friend-disable-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
