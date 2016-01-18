<?php

class IndustryController extends BaseController
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/admin';
	
	/**
	 * @var int the define the index for the menu
	 */
	 
	 public $menuIndex = 71;
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Industry;
		$parent_id = intval($_REQUEST['parent_id']);
		if($parent_id){
			$sql = "SELECT name FROM industry WHERE id = ".$parent_id;
			$parent_name = $model->findBySql($sql);
			$parent_name = $parent_name->name;
		}

		if(isset($_POST['Industry']))
		{
			$name = trim($_POST['Industry']['name']);
			if(!$parent_id){
				if($name != ""){
					$model->parent_id = 0;
					$model->name = $name;
					$model->created_time = time();
					if($model->save())
						$this->redirect($this->getBackListPageUrl());
				}else{
					$msg = "行业名称不能为空！";
					$goback = "-2";
				}
			}else{
				if($name != ""){
					$model->parent_id = 0;
					$model->name = $name;
					$model->created_time = time();
					$model->parent_id = $parent_id;
					if($model->save())
						$this->redirect($this->getBackListPageUrl2($parent_id));
				}else{
					$msg = "行业名称不能为空！";
					$goback = "-2";
				}
			}
		}

		$this->render('create',array(
			'model'=>$model,
			'msg' => $msg,
			'goback' => $goback,
			'parent_name' => $parent_name,
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
		$parent_id = intval($_REQUEST['parent_id']);
		
		if($parent_id){
			$sql = "SELECT name FROM industry WHERE id = ".$parent_id;
			$parent_name = $model->findBySql($sql);
			$parent_name = $parent_name->name;
		}
		
		if(isset($_POST['Industry']))
		{
			$name = trim($_POST['Industry']['name']);
			if(!$parent_id){
				if($name != ""){
					$model->parent_id = 0;
					$model->name = $name;
					$model->created_time = time();
					if($model->save())
						$this->redirect($this->getBackListPageUrl());
				}else{
					$msg = "行业名称不能为空！";
					$goback = "-2";
				}
			}else{
				if($name != ""){
					$model->parent_id = 0;
					$model->name = $name;
					$model->created_time = time();
					$model->parent_id = $parent_id;
					if($model->save())
						$this->redirect($this->getBackListPageUrl2($parent_id));
				}else{
					$msg = "行业名称不能为空！";
					$goback = "-2";
				}
			}
		}

		$this->render('update',array(
			'model'=>$model,
			'msg' => $msg,
			'goback' => $goback,
			'parent_name' => $parent_name,
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
		$this->insert_log(71);
		$model = Industry::model();
		$cri = new CDbCriteria();
		
		if($_GET['children'] == "children"){
			$id = intval($_REQUEST['id']);
			$cri->addCondition("parent_id = ".$id);
		}else{
			$cri->addCondition("parent_id = 0");
		}
		

		$cri->order = "created_time desc";
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
			$model = Industry::model ()->findByPk ( $id );
		}
		if ($model) {
			
			if($_GET['parent_id']){
				$model->deleteAll('parent_id in ('.$id.')');
			}
			$model->delete();
		}
		$this->redirect ( Yii::app()->createUrl('industry/index',array('page'=>intval($_REQUEST['page']))));
	}
	

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Industry the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Industry::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
	
	public function getBackListPageUrl()
	{
		return Yii::app()->createUrl("industry/index",array('page'=>$_REQUEST['page']));
	}
	public function getBackListPageUrl2($parent_id)
	{
		return Yii::app()->createUrl("industry/index",array('page'=>$_REQUEST['page'], 'id'=>$parent_id, 'children' => 'children'));
	}

	/**
	 * Performs the AJAX validation.
	 * @param Industry $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='industry-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
