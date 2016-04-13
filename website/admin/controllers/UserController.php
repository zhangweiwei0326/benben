<?php

class UserController extends BaseController
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/admin';
	
	/**
	 * @var int the define the index for the menu
	 */
	 
	 public $menuIndex = 80;

	/**
	 * @var int the define the id for the bx
	 */
	public $ownbx=0;

	/**
	 * UserController constructor.
	 * @param $id
	 * @param null $module
	 */
	public function __construct($id, $module)
	{
		parent::__construct($id, $module);
		$this->ownbx = Yii::app()->user->getState('userInfo')->enterprise_id;
	}


	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new User;

		$crole = new Role();
		$role = $this->getRole("dosystem");
		$sql = "SELECT id, role_name FROM role where enterprise_id=".$this->ownbx." ORDER BY created_time DESC";
		$roles = $crole->findAllBySql($sql);
		
		$result = array();
		foreach ($roles as $value){
			$temp = array('id' => $value->id, 'role_name' => $value->role_name);
			$result[] = $temp;
		}
		
		if(isset($_POST['User']))
		{
			if($role & 1){
				$model->username=$_POST['User']['username'];
				$model->role = $_POST['role'];
				$model->password = md5($_POST['User']['password']);
				$model->created_time = time();
				$model->enterprise_id = $this->ownbx;
				if($model->save())
				$this->redirect($this->getBackListPageUrl());
			}else{
				echo '非法操作！';
				die;
			}
		}

		$this->render('create',array(
			'model'=>$model,
			'role' => $role,
			'result' => $result,
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
		$role = new Role();
		$sql = "SELECT id, role_name FROM role ORDER BY created_time DESC";
		$role = $role->findAllBySql($sql);
		$roles = $this->getRole("dosystem");
		$result = array();
		foreach ($role as $value){
			$temp = array('id' => $value->id, 'role_name' => $value->role_name);
			$result[] = $temp;
		}
		
		if(isset($_POST['User']))
		{
			if($roles & 1){
				$model->role=$_POST['role'];
				$password = $_POST['User']['password'];
				if($model->password != $password){
					$model->password = md5($password);
				}
				if(isset($_POST['User']['disable'])){
					$model->disable = $_POST['User']['disable'];
				}
				if($model->save())
				$this->redirect($this->getBackListPageUrl());
			}else{
				echo '非法操作！';
			}
			
		}
		$model->created_time = date('Y-m-d H:i:s', $model->created_time);
		$model->last_login = date('Y-m-d H:i:s', $model->last_login);
		$this->render('update',array(
			'model'=>$model,
			'result' => $result,
			'role' => $roles,
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
		$this->insert_log(80);
		$role = $this->getRole("dosystem");
		$model = User::model();
		$cri = new CDbCriteria();
		$cri->addSearchCondition("t.enterprise_id",$this->ownbx,true,'AND');
		$cri->select = "t.*, role.role_name as rname";
		$cri->join = "left join role on role.id = t.role";
		$cri->addSearchCondition("role.enterprise_id",$this->ownbx,true,'AND');
		$cri->order = "t.created_time desc";
		$pages = new CPagination();
		$pages->itemCount = $model->count($cri);
		$pages->pageSize = 12;
		$pages->applyLimit($cri);
		$items = $model->findAll($cri);
		$this->render('index',array('items'=>$items,'pages'=> $pages, 'role' => $role));
		
	}

	
	public function actionDelete($id)
	{
		$role = $this->getRole("dosystem");
		if(!($role & 1)){
			echo '非法操作！';
			die;
		}
		$id = Frame::getIntFromRequest('id');
		if ($id > 0) {
			$model = User::model ()->findByPk ( $id );
		}
		if ($model) {
			$model->delete();
		}
		$this->redirect ( Yii::app()->createUrl('user/index',array('page'=>intval($_REQUEST['page']))));
	}
	
	public function actionDisable($id)
	{
		$role = $this->getRole("dosystem");
		if(!($role & 1)){
			echo '非法操作！';
			die;
		}
		$id = Frame::getIntFromRequest('id');
		if ($id > 0) {
			$model = User::model ()->findByPk ( $id );
		}
		if ($model) {
			$model->disable = 1;
			$model->update();
		}
		$this->redirect ( Yii::app()->createUrl('user/index',array('page'=>intval($_REQUEST['page']))));
	}
	

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return User the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=User::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
	
	public function getBackListPageUrl()
	{
		return Yii::app()->createUrl("user/index",array('page'=>$_REQUEST['page']));
	}

	/**
	 * Performs the AJAX validation.
	 * @param User $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='user-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
