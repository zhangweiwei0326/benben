<?php

class GroupsController extends BaseController
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/admin';
	
	/**
	 * @var int the define the index for the menu
	 */
	 
	 public $menuIndex = 31;
	 
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Groups;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Groups']))
		{
			$model->attributes=$_POST['Groups'];
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
		
		//创建人
		$member = new Member();
		//$sql = "SELECT name,phone,group_disable FROM member WHERE id = ".$model->member_id;
		$member = $member->findByPk($model->member_id);//findBySql($sql);
		
		//原因
		$groupDisable = new GroupDisable();
		$sql = "SELECT reason FROM group_disable WHERE 
					group_id = ".$model->id." ORDER BY created_time DESC";
		$reason = $groupDisable->findBySql($sql);
		
		//显示创建人禁用原因
		$serviceDisable = new ServiceDisable();
		$sql = "SELECT status,reason FROM service_disable
					WHERE member_id = ".$model->member_id." and type = 4 ORDER BY created_time DESC LIMIT 1";
		$ereason2 = $serviceDisable->findAllBySql($sql);
		$reason2 = $ereason2[0]->reason;
		
		$province = $this->areas($model->province) ? $this->areas($model->province) : "未知";
		$city = $this->areas($model->city) ? $this->areas($model->city) : "未知";
		$area = $this->areas($model->area) ? $this->areas($model->area) : "未知";
		$street = $this->areas($model->street) ? $this->areas($model->street) : "未知";
		
		$areas = array();
		$areas = array("province" => $province, "city" => $city, "area" => $area, "street" => $street);

		if(isset($_POST['Groups']))
		{
			$status = $_POST['Groups']['status'];
			$post_reason = $_POST['Groups']['reason'];
			
			//群组禁用记录
			if($status != $model->status || $reason != $post_reason){
				$groupDisable->group_id = $model->id;
				$groupDisable->status = $status;
				$groupDisable->user_id = $this->getLoginId();
				$groupDisable->reason = $post_reason;
				$groupDisable->created_time = time();
				$groupDisable->save();
			}
			//改变创建人禁用状态
			$status2 = $_POST['Groups']['status2'];
			$post_reason2 = $_POST['Groups']['reason2'];
			if($status2 != $ereason2[0]->status || $reason2 != $post_reason2){
				$member->group_disable = $status2;
				if($member->update()){
					$service = new ServiceDisable();
					$service->member_id = $model->member_id;
					$service->user_id = $this->getLoginId();
					$service->status = $status2;
					$service->reason = $post_reason2;
					$service->type = 4;
					$service->created_time = time();
					$service->save();
				}
			}
			
			$model->status= $status;
			if($model->save())
				$this->redirect($this->getBackListPageUrl());
		}
		$model->created_time = date('Y-m-d H:i:s', $model->created_time);
		$this->render('update',array(
			'model'=>$model,
			'mname' => $member->name ? $member->name : $member->nick_name,
			'mphone' => $member->phone,
			'reason' => $reason->reason,
			'status2' => $member['group_disable'],
			'reason2' => $reason2,
			'areas' => $areas,
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
		$this->insert_log(31);
		$model = Groups::model();
		$cri = new CDbCriteria();
		
		$province = $this->getProvince();
		
		$result = array();
		$name = $_GET['name'];
		$member_id = $_GET['member_id'];
		$status = intval($_GET['status']);
		$post_province = intval($_GET['province']);
		$post_city = intval($_GET['city']);
		$post_area = intval($_GET['area']);
		$created_time1= $_GET['created_time1'];
		$created_time2= $_GET['created_time2'];
		$number1= intval($_GET['number1']);
		$number2= intval($_GET['number2']);
		$id= intval($_GET['id']);
		if ($id > 0) {
			$cri->addCondition('t.show_id = '.$id, 'AND');
			$result['id'] = $id;
		}
		
		if(!empty($name)){
			$cri->addSearchCondition('t.name', $name, true, 'AND');
			$result['name'] = $name;
		}
		if(!empty($member_id)){
			$member = new Member();
			$sql = "select distinct id from member where nick_name like '%".$member_id."%' or name like '%".$member_id."%'";
			$id = $member->findAllBySql($sql);
			if($id){
				foreach ($id as $va){
					$str .= $va->id.",";
				};			
				$str = explode(",", substr($str, 0, -1));
				$cri->addInCondition('t.member_id', $str);				
			}else{
				$cri->addInCondition('t.member_id', array(0));
			}
			$result['member_name'] = $member_id;
		}
		if(!empty($status) && $status != -1){
			//$cri->addCondition('t.is_delete = 0','AND');
			if($status == 6){
				$cri->addCondition('t.is_delete = 0','AND');
				$cri->addInCondition('t.status', array(0));
			}else if($status == 2){
				$cri->addCondition('t.is_delete = 1');
			}else{
				$cri->addCondition('t.is_delete = 0','AND');
				$cri->addCondition('t.status='.$status);
			}
			$result['status'] = $status;
		}
		
		if($post_province && ($post_province != -1)){
				$cri->addCondition('t.province = '.$post_province,'AND');
				$result['province'] = $post_province;
				$res = $this->getCity($_GET['province']);
		}
		
		if($post_city && ($post_city != -1)){
				$cri->addCondition('t.city = '.$post_city,'AND');
				$result['city'] = $post_city;
				$res2 = $this->getArea($post_city);
		}
		
		if($post_area && ($post_area != -1)){
				$cri->addCondition('t.area = '.$post_area,'AND');
				$result['area'] = $post_area;
		}
		if ($number1 > 0) {
			$cri->addCondition('t.number >= '.$number1,'AND');
			$result['number1'] = $number1;
		}
		if ($number2 > 0) {
			$cri->addCondition('t.number <= '.$number2,'AND');
			$result['number2'] = $number2;
		}
		
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
			
		
		$cri->select = "t.*, member.name as mname, member.nick_name, member.group_disable";
		$cri->join = "left join member on member.id = t.member_id";
		$cri->order = "t.created_time desc";
		$pages = new CPagination();
		$pages->itemCount = $model->count($cri);
		$pages->pageSize = 50;
		$pages->applyLimit($cri);
		$items = $model->findAll($cri);
		$url = Yii::app()->request->getUrl();
		$cookie = new CHttpCookie('benben-neverland',$url);
		$cookie->expire = time()+3600;  
		Yii::app()->request->cookies['benben-neverland']=$cookie;
		$this->render('index',array('items'=>$items,'pages'=> $pages,'result' => $result, 
				'province' => $province, 'res' => $res, 'res2' => $res2));
		
	}

	
	public function actionDelete($id)
	{
		$id = Frame::getIntFromRequest('id');
		if ($id > 0) {
			$model = Groups::model ()->findByPk ( $id );
		}
		if ($model) {
			$model->delete();
		}
		$this->redirect ( Yii::app()->createUrl('groups/index',array('page'=>intval($_REQUEST['page']))));
	}
	

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Groups the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Groups::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
	
	public function getBackListPageUrl()
	{
		$cookie = Yii::app()->request->getCookies();
		$returnUrl = $cookie['benben-neverland']->value;
		if ($returnUrl) {
			return $returnUrl;
		}else{
			return Yii::app()->createUrl("groups/index",array('page'=>$_REQUEST['page']));
		}
	}

	/**
	 * Performs the AJAX validation.
	 * @param Groups $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='groups-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
