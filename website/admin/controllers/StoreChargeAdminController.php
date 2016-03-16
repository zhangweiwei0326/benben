<?php

class StoreChargeAdminController extends BaseController {
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout = '//layouts/admin';

	/**
	 * @var int the define the index for the menu
	 */

	public $menuIndex = 71;
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate() {
		$model = new StoreOrderInfo;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if (isset($_POST['StoreOrderInfo'])) {
			$model->attributes = $_POST['StoreOrderInfo'];
			if ($model->save()) {
				$this->redirect($this->getBackListPageUrl());
			}

		}

		$this->render('create', array(
			'model' => $model,
			'backUrl' => $this->getBackListPageUrl(),
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id) {
		$model = $this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if (isset($_POST['StoreOrderInfo'])) {
			$model->attributes = $_POST['StoreOrderInfo'];
			if ($model->save()) {
				$this->redirect($this->getBackListPageUrl());
			}

		}

		$this->render('update', array(
			'model' => $model,
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
	public function actionIndex() {
		$model = StoreOrderInfo::model();
		$cri = new CDbCriteria();
		$cri->select="t.*,b.goods_number,b.extension_code as type";
		$cri->join.="left join store_order_goods b on t.order_id=b.order_id ";
		$cri->order = "t.order_id desc";
		$cri->addCondition("t.extension_code =3");
		$result['order_status'] = -1;
		$result['service_name'] = -1;
		if (isset($_GET) && !empty($_GET)) {
			$result = array();
			$order_sn = $_GET['order_sn'];
			$order_status = $_GET['order_status'];
			$service_name = $_GET['service_name'];
			$nick_name = $_GET['nick_name'];
			$phone = $_GET['phone'];
			$created_time1 = $_GET['created_time1'];
			$created_time2 = $_GET['created_time2'];

			if ($order_sn) {
				$cri->addSearchCondition('t.order_sn', $order_sn, true, 'AND');
				$result['order_sn'] = $order_sn;
			}
			if ($order_status > -1) {
				$cri->addSearchCondition('t.order_status', $order_status, true, 'AND');
				$result['order_status'] = $order_status;
			} else {
				$result['order_status'] = -1;

			}
			if ($service_name > -1) {
				$cri->addCondition("b.extension_code ={$service_name}");
				$result['service_name'] = $service_name;
			} else {
				$result['service_name'] = -1;

			}
			if ($nick_name) {
				$cri->join .= "left join member m on t.member_id=m.id ";
				$cri->addCondition("m.nick_name like '%$nick_name%'");
				$result['nick_name'] = $nick_name;

			}
			if ($phone) {
				$cri->join .= "left join member m on t.member_id=m.id ";
				$cri->addCondition("m.phone like '%$phone%'");
				$result['phone'] = $phone;

			}
			if ($created_time1) {
				$created_time1_tmp=strtotime($created_time1);
				$cri->addCondition("t.add_time > '".$created_time1_tmp."' ");
				$result['created_time1']=$created_time1;
			}
			if ($created_time2) {
				$created_time2_tmp=strtotime($created_time2);
				$cri->addCondition("t.add_time < '".$created_time2_tmp."' ");
				$result['created_time2']=$created_time2;
			}
		}

		$pages = new CPagination();
		$pages->itemCount = $model->count($cri);
		$pages->pageSize = 12;
		$pages->applyLimit($cri);
		$items = $model->findAll($cri);
		$this->render('index', array('items' => $items, 'pages' => $pages, 'result' => $result));

	}

	public function actionDelete($id) {
		$id = Frame::getIntFromRequest('id');
		if ($id > 0) {
			$model = StoreOrderInfo::model()->findByPk($id);
		}
		if ($model) {
			$model->delete();
		}
		$this->redirect(Yii::app()->createUrl('storeOrderInfo/index', array('page' => intval($_REQUEST['page']))));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return StoreOrderInfo the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id) {
		$model = StoreOrderInfo::model()->findByPk($id);
		if ($model === null) {
			throw new CHttpException(404, 'The requested page does not exist.');
		}

		return $model;
	}

	public function getBackListPageUrl() {
		return Yii::app()->createUrl("storeOrderInfo/index", array('page' => $_REQUEST['page']));
	}

	/**
	 * Performs the AJAX validation.
	 * @param StoreOrderInfo $model the model to be validated
	 */
	protected function performAjaxValidation($model) {
		if (isset($_POST['ajax']) && $_POST['ajax'] === 'store-order-info-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
