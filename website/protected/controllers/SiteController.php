<?php

class SiteController extends Controller {
	/**
	 * Declares class-based actions.
	 */
	public function actions() {
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha' => array(
				'class' => 'CCaptchaAction',
				'backColor' => 0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page' => array(
				'class' => 'CViewAction',
			),
		);
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex() {
		$session = new CHttpSession();
		$session->open();
		Yii::app()->session->add('backUrl',Yii::app()->request->url);
		if (!Yii::app()->user->getState("memberInfo")) {
			$this->redirect('/index.php/site/login');
		}
		$member_id=Yii::app()->user->getState("memberInfo")->id;
		$memberInfo=Member::model()->find("id={$member_id}");
		$vip = PromotionManage::model()->findByAttributes(array("member_id"=>$member_id));
		$vip_info = PromotionManageAttach::model()->findByAttributes(array("member_id"=>$member_id));
		$store_info = StoreRights::model()->findByAttributes(array("member_id"=>$member_id));
		$re = array();
		if($store_info){
			$re['vip'] = 0;
			$re['store_num1'] = $store_info->person_num;
			$re['store_used_num1'] = $store_info->person_used_num;
			$re['small_horn_num'] = $store_info->small_horn_num;
			$re['overdue_date'][2] = date("Y-m-d",$store_info->overdue_date);
		}
		if($vip){
			$re['vip'] = 1;
			$re['small_horn_num'] = $vip_info->small_horn_num;
			$re['big_horn_num'] = $vip_info->big_horn_num;
			if($vip_info->store_num){
				$re['store_num'] = $vip_info->store_num;
				$re['store_used_num'] = $vip_info->store_used_num;
				$re['service_type'][2] = 10;
			}
			switch($vip->store_type){
				case 0:
					$re['name'][0] = "促销";
					$re['overdue_date'][0] = date("Y-m-d",$vip->vip_time);
					$re['service_type'][0] = 0;
					break;
				case 1:
					$re['name'][0] = "团购";
					$re['overdue_date'][0] = date("Y-m-d",$vip->vip_time);
					$re['service_type'][0] = 1;
					if($vip_info->service_type == 11){
						$re['name'][1] = "会员人数";
						$re['overdue_date'][1] = date("Y-m-d",$vip_info->overdue_date);
						$re['service_type'][1] = 11;
						$re['person_num'] = $vip_info->person_num;
						$re['person_used_num'] = $vip_info->person_used_num;
					}

					break;
				default:;
			}
		}
		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'
		$this->render('index',array('memberInfo'=>$memberInfo,'re'=>$re));
	}



	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError() {
		$this->redirect('/index.php/site');
	}

	/**
	 * Displays the contact page
	 */
	public function actionContact() {
		$model = new ContactForm;
		if (isset($_POST['ContactForm'])) {
			$model->attributes = $_POST['ContactForm'];
			if ($model->validate()) {
				$name = '=?UTF-8?B?' . base64_encode($model->name) . '?=';
				$subject = '=?UTF-8?B?' . base64_encode($model->subject) . '?=';
				$headers = "From: $name <{$model->email}>\r\n" .
					"Reply-To: {$model->email}\r\n" .
					"MIME-Version: 1.0\r\n" .
					"Content-Type: text/plain; charset=UTF-8";

				mail(Yii::app()->params['adminEmail'], $subject, $model->body, $headers);
				Yii::app()->user->setFlash('contact', 'Thank you for contacting us. We will respond to you as soon as possible.');
				$this->refresh();
			}
		}
		$this->render('contact', array('model' => $model));
	}

	/**
	 * Displays the login page
	 */
	public function actionLogin() {
		//判断是否登录
		if (Yii::app()->user->getState("memberInfo")) {
			$this->redirect('/index.php/site');
		} else {
			if (isset($_POST['username']) && isset($_POST['password'])) {
				$identity = new UserIdentity($_POST['username'], $_POST['password']);
				if ($identity->authenticate()) {

                    $connection = Yii::app()->db;
                    $sql = "SELECT id FROM `number_train` where member_id={$identity->member_id}";
                    $command = $connection->createCommand($sql);
                    $result = $command->queryAll();
                    if(!$result){
                        echo 5;//无权登陆
                        exit;
                    }

					$duration = 3600*24; // 30 days
					Yii::app()->user->login($identity, $duration);
					echo 1;
					exit;
				} else {
					echo -1;
					exit;
				}

			}
			$this->renderPartial('login', array("last_url" => $lastUrl));}
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout() {
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}

	public function actionIndexinfo() {
		$this->render('info');
	}
}
