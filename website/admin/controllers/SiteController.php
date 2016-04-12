<?php
class SiteController extends BaseController {
	/**
	 * Declares class-based actions.
	 */
	public $layout = false;
	public function actions() {
		return array (
				// captcha action renders the CAPTCHA image displayed on the contact page
				'captcha' => array (
						'class' => 'CCaptchaAction',
						'backColor' => 0xFFFFFF
				),
				// page action renders "static" pages stored under 'protected/views/site/pages'
				// They can be accessed via: index.php?r=site/page&view=FileName
				'page' => array (
						'class' => 'CViewAction'
				)
		);
	}

	//自动
	public function actionAutotop()
	{
		$model= NumberTrain::model()->findAll("istop > 0");
		if($model)
		{
			foreach ($model as $va){
				$toplog = NumberTrainTop::model()->find("train_id = {$va->id} order by created_time desc");

				if($toplog->istop > 0){
					$toptime = $toplog->created_time + $toplog->number*24*60*60;
					if($toptime < time()){
						$va->istop = 0;
						$log = new NumberTrainTop();
						$log->train_id = $va->id;
						$log->user_id = 1;
						$log->created_time = time();
						$log->istop = 0;
						$log->number = 0;
						if($log->save()){
							$va->update();
						}
					}
				}
			}
		}
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex() {
		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'
		// $this->render('index');
		if (Yii::app ()->user->isGuest) {
			// 游客
			$eninfo=Enterprise::model()->findAll("type=3 and status=0");
			$role = getRole ();
			$this->render ( 'index', array (
					'role' => $role,
					'eninfo'=>$eninfo
			) );
		} else {
			// 已登录用户
			$this->redirect ( array (
					'/index'
			) );
		}
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError() {
		if ($error = Yii::app ()->errorHandler->error) {
			if (Yii::app ()->request->isAjaxRequest)
				echo $error ['message'];
			else
				$this->render ( 'error', $error );
		}
	}

	/**
	 * Displays the login page
	 */
	public function actionLogin() {
		$model = new LoginForm ();

		$username = addslashes($_POST ['username']);
		$password = addslashes($_POST ['password']);
		$enterpriseId = addslashes($_POST ['enterprise_id']);

		$result ['status'] = 0;
		if (empty ( $username )) {
			$result ['message'] = '请输入登陆名称';
			echo json_encode ( $result );
			die ();
		}
		if (empty ( $password )) {
			$result ['message'] = '请输入登陆密码';
			echo json_encode ( $result );
			die ();
		}
		if(empty( $enterpriseId )){
			$result ['message'] = '请选择百姓网';
			echo json_encode ( $result );
			die ();
		}
		$identity = new UserIdentity ( $username, $password, $enterpriseId );
		if ($identity->authenticate ()) {
			$duration = 3600 * 24 * 1; // 30 days
			Yii::app ()->user->login ( $identity, $duration );
			$result ['status'] = 1;
			$status = 1;
			if($identity->disable){
				$result ['status'] = 0;
				$result ['message'] = '账号被禁用';
				$status = 0;
			}
		}else{
			$result ['message'] = '登录名或密码错误';
			$status = 0;
		}
		//$userid = $identity->userid;
		//$this->insert_log($username, "", $status, $userid);
		$this->insert_log($status);
		echo json_encode ( $result );
		die ();

		/*
		 * // if it is ajax validation request if(isset($_POST['ajax']) && $_POST['ajax']==='login-form') { echo CActiveForm::validate($model); Yii::app()->end(); } // collect user input data if(isset($_POST['LoginForm'])) { $model->attributes=$_POST['LoginForm']; // validate user input and redirect to the previous page if valid if($model->validate() && $model->login()) $this->redirect(Yii::app()->user->returnUrl); } // display the login form $this->render('login',array('model'=>$model));
		 */
	}

	public function actionTest()
	{
		$this->menuIndex = 21;
		$this->layout = "admin";
		$this->render("test");
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout() {
		Yii::app ()->user->logout ( false );
		$this->redirect ( Yii::app ()->homeUrl );
	}
}