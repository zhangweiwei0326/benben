<?php

class LeagueMemberController extends BaseController
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/admin';

	/**
	 * @var int the define the index for the menu
	 */

	public $menuIndex = 50;
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new LeagueMember;


		if(isset($_POST['LeagueMember']))
		{
			$model->attributes=$_POST['LeagueMember'];
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
		$fiendLeague = new FriendLeague();
		$sql = "select name from friend_league where id = ".$model->league_id;

		$fiendLeague = $fiendLeague->findBySql($sql);

		$member = new Member();
		$sql = "select name from member where id = ".$model->member_id;
		$member  = $member->findBySql($sql);

		if(isset($_POST['LeagueMember']))
		{
			$model->attributes=$_POST['LeagueMember'];
			if($model->save())
			$this->redirect($this->getBackListPageUrl());
		}

		$model->league_id = $fiendLeague->name;
		$model->member_id = $member->name;
		$model->created_time = date('Y-m-d H:i:s', $model->created_time);

		$type = array('0' => '盟主', '1' => '堂主', '2' =>'普通成员');
		$status =  array('0' => '未加入', '1' => '已加入');
		$model->type = $type[$model->type];
		$model->status = $status[$model->status];

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
		$enterpriseid = Frame::getStringFromRequest("lid");
		// $model = LeagueMember::model();
		// $cri = new CDbCriteria();
		// $cri->select = "t.*, friend_league.name as fname, member.name as mname";
		// $cri->join = "left join friend_league on friend_league.id = t.league_id
		// 						left join member on member.id = t.member_id";
		// $cri->addCondition("t.league_id = ".$id);

		// $cri->order = "t.type asc";
		// $pages = new CPagination();
		// $pages->itemCount = $model->count($cri);
		// $pages->pageSize = 12;
		// $pages->applyLimit($cri);
		// $items = $model->findAll($cri);
		$connection = Yii::app()->db;
		$sql = "select a.id,a.league_id,a.member_id,a.created_time,c.nick_name, c.poster , a.type, a.remark_content, c.benben_id, c.phone from league_member a  left join member c on c.id = a.member_id where a.league_id = {$enterpriseid} order by a.type, a.id desc";
		$command = $connection->createCommand($sql);
		$result1 = $command->queryAll();
		$chief = array();
		$chiefLevel = array();
		$normalPerson = array();
		//备注名
		$sql = "select * from league_member_data where league_id = ".$enterpriseid;
		$command = $connection->createCommand($sql);
		$markResult = $command->queryAll();
		$markInfo;
		if ($markResult) {
			foreach ($markResult as $key => $value) {
				$markInfo[$value['data_id']] = $value;
			}
		}
		foreach ($result1 as $key=>$value){
			$poster = $value['poster'] ? URL.$value['poster'] : "";
			if ($value['type'] == 0) {
				$remark_name = '盟主';
				if (isset($markInfo[$value['id']])) {
					$remark_name = $markInfo[$value['id']]['remark_name'];
				}
				$chief = array('id'=>$value['id'], 'member_id'=>$value['member_id'], 'nick_name'=>$value['nick_name'], 'poster'=>$poster, 'benben_id'=>$value['benben_id'], 'phone'=>$value['phone'], 'remark_name'=>$remark_name, 'type'=>$value['type'], 'created_time'=>$value['created_time']);
			}else if($value['type'] == 1){
				$remark_name = '堂主';
				if (isset($markInfo[$value['id']])) {
					$remark_name = $markInfo[$value['id']]['remark_name'];
				}
				$chiefLevel[] = array('id'=>$value['id'], 'member_id'=>$value['member_id'], 'nick_name'=>$value['nick_name'], 'poster'=>$poster, 'benben_id'=>$value['benben_id'], 'remark_name'=>$remark_name, 'phone'=>$value['phone'], 'created_time'=>$value['created_time'], 'type'=>$value['type']);
			}else{
				$currentType = $value['remark_content'];
				if ($memberType > 0 && $memberRemark != $currentType) {
					continue;
				}
				$normalPerson[$currentType][] = array('id'=>$value['id'], 'member_id'=>$value['member_id'], 'nick_name'=>$value['nick_name'], 'poster'=>$poster, 'benben_id'=>$value['benben_id'], 'phone'=>$value['phone'], 'created_time'=>$value['created_time']);
			}
		}
		$returnArray['chief'] = $chief;
		$chiefMember = array();
		if (isset($normalPerson[$chief['member_id']])) {
			$chiefMember = $normalPerson[$chief['member_id']];
		}
		$returnArray['chief_member'] = $chiefMember;
		$level2 = array();
		if (count($chiefLevel)) {
			foreach ($chiefLevel as $key => $value) {
				$currentInfo = array();
				$currentLevelMember = array();
				$currentLevelMember[] = $value;
				if(isset($normalPerson[$value['member_id']])){
					$currentLevelMember = array_merge($currentLevelMember, $normalPerson[$value['member_id']]);
				}
				$name = '堂主';
				if (isset($markInfo[$value['id']]) && $markInfo[$value['id']]['name']) {
					$name = $markInfo[$value['id']]['name'];
				}
				$returnArray['other_chief'][] = array('name'=>$name, 'member'=>$currentLevelMember);
			}
		}
		$pages = new CPagination();
		$pages->itemCount = count($result1);
		$pages->pageSize = 50;
		//$pages->applyLimit($cri);
		$this->render('index',array('items'=>$returnArray,'pages'=>$pages));

	}


	public function actionDelete($id)
	{
		$id = Frame::getIntFromRequest('id');
		if ($id > 0) {
			$model = LeagueMember::model ()->findByPk ( $id );
		}
		if ($model) {
			$model->delete();
		}
		$this->redirect ( Yii::app()->createUrl('LeagueMember/index',array('page'=>intval($_REQUEST['page']))));
	}


	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return LeagueMember the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=LeagueMember::model()->findByPk($id);
		if($model===null)
		throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	public function getBackListPageUrl()
	{
		return Yii::app()->createUrl("LeagueMember/index",array('page'=>$_REQUEST['page']));
	}

	/**
	 * Performs the AJAX validation.
	 * @param LeagueMember $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='league-member-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
