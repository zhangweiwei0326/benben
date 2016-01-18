<?php
Yii::$enableIncludePath = false;
define('__ROOT__',dirname(dirname(__FILE__)));
require_once (__ROOT__.'/PHPExcel/PHPExcel.php');
class NumberTrainController extends BaseController
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/admin';

	/**
	 * @var int the define the index for the menu
	 */

	public $menuIndex = 32;
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new NumberTrain;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['NumberTrain']))
		{
			$model->attributes=$_POST['NumberTrain'];
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

		//行业
		$industry  = new Industry();
		$sql = "SELECT name FROM industry WHERE id = ".$model->industry;
		$industry = $industry->findBySql($sql);

		$area  = array();
		$area['province'] = $this->areas($model->province);
		$area['city'] = $this->areas($model->city);
		$area['area'] = $this->areas($model->area);	
		$area['street'] = $this->areas($model->street);
		
		//创建人
		$member = new Member();
		//$sql = "SELECT nick_name as name From member where id = ".$model->member_id;
		$member = $member->findByPk($model->member_id);//findBySql($sql);

		//原因
		$numberDisable = new NumberDisable();
		$sql = "SELECT reason FROM number_disable WHERE train_id = ".$model->id." ORDER BY created_time DESC LIMIT 1";
		$reason = $numberDisable->findBySql($sql);
		
		//显示创建人禁用原因
		$serviceDisable = new ServiceDisable();
		$sql = "SELECT status,reason FROM service_disable
					WHERE member_id = ".$model->member_id." and type = 5 ORDER BY created_time DESC LIMIT 1";
		$ereason2 = $serviceDisable->findAllBySql($sql);
		$reason2 = $ereason2[0]->reason;

		if(isset($_POST['NumberTrain']))
		{
			$status = intval($_POST['NumberTrain']['status']);
			$post_reason = $_POST['NumberTrain']['reason'];
				
			if($model->status != $status || $reason->reason != $post_reason){
				$numberDisable->train_id = $id;
				$numberDisable->status = $status;
				$numberDisable->user_id = $this->getLoginId();
				$numberDisable->reason = $post_reason;
				$numberDisable->created_time = time();
				$numberDisable->save();
			}
			//改变创建人禁用状态
			$status2 = $_POST['NumberTrain']['status2'];
			$post_reason2 = $_POST['NumberTrain']['reason2'];
			if($status2 != $ereason2[0]->status || $reason2 != $post_reason2){
				$member->store_disable = $status2;
				if($member->update()){
					$service = new ServiceDisable();
					$service->member_id = $model->member_id;
					$service->user_id = $this->getLoginId();
					$service->status = $status2;
					$service->reason = $post_reason2;
					$service->type = 5;
					$service->created_time = time();
					$service->save();
				}
			}
			if($status == 6){
				$model->is_close = 1;
			}else{
				$model->status=$status;
			}	
			
			if($model->save())
			$this->redirect($this->getBackListPageUrl());
		}
		//收藏数
		$connection = Yii::app()->db;
		$command = $connection->createCommand("select count(*) as c from number_train_collect where number_train_id = ".$id);
		$info = $command->queryAll();
		$collectNumber = 0;
		if ($info) {
			$collectNumber = $info[0]['c'];
		}
		$additional['collect'] = $collectNumber;
		//报价次数
		$command = $connection->createCommand("select count(*) as c from quote where store_id = ".$id);
		$info2 = $command->queryAll();
		$buyNumber = 0;
		if ($info2) {
			$buyNumber = $info2[0]['c'];
		}
		$additional['buy'] = $buyNumber;

		//申请资料
		$apply_info = ApplyComplete::model()->find("member_id = {$model->member_id} and type = 2");


		$model->created_time = date('Y-m-d H:i:s', $model->created_time);
		$this->render('update',array(
			'model'=>$model,
			'areas' => $area,
			'additional' => $additional,
			'apply_info' => $apply_info,
			'industry' => $industry->name,
			'member_id' => $member->name ? $member->name : $member->nick_name,
			'status2' => $member['store_disable'],
			'reason2' => $reason2,
			'reason' =>$reason->reason,
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
		$this->insert_log(32);
		$model = NumberTrain::model();

		$province = $this->getProvince();


		$cri = new CDbCriteria();
		$result = array();
		$name = $_GET['name'];
		$short_name = $_GET['short_name'];
		$tag = $_GET['tag'];
		$industry = intval($_GET['industry']);
		$phone = $_GET['phone'];
		$created_time1= $_GET['created_time1'];
		$created_time2= $_GET['created_time2'];
		$date1= intval($_GET['date1']);
		$date2= intval($_GET['date2']);
		$post_province = intval($_GET['province']);
		$post_city = intval($_GET['city']);
		$post_area = intval($_GET['area']);
		$status = intval($_GET['status']);

		if(!empty($name)){
			$cri->addSearchCondition('t.name', $name, true, 'AND');
			$result['name'] = $name;
		}
		if(!empty($short_name)){
			$cri->addSearchCondition('t.short_name', $short_name, true, 'AND');
			$result['short_name'] = $short_name;
		}
		if(!empty($tag)){
			$cri->addSearchCondition('t.tag', $tag, true, 'AND');
			$result['tag'] = $tag;
		}
		if($industry > 0){
			$cri->addSearchCondition('t.industry', $industry, true, 'AND');
			$result['industry'] = $industry;
		}
		if(!empty($phone)){
			$cri->addSearchCondition('t.phone', $phone, true, 'AND');
			$result['phone'] = $phone;
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
		if($date1 && $date2){			
			if($date1 >= $date2){
				$result['msg'] = "置顶天数第一个必须比第二个小!";
			}else{
				$cri->addCondition('number_train_top.number >= '.$date1,'AND');
				$result['date1'] = $date1;
				$cri->addCondition('number_train_top.number <= '.$date2,'AND');
				$result['date2'] = $date2;
			}
		}else{
			if($date1){
				$cri->addCondition('number_train_top.number >= '.$date1,'AND');
				$result['date1'] = $date1;
					
			}
			if($date2){
				$cri->addCondition('number_train_top.number <= '.$date2,'AND');
				$result['date2'] = $date2;
			}			
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
		if(isset($_GET['status']) && ($status != -1)){
			if($status == 6){
				$cri->addCondition('t.is_close = 1','AND');
			}else{
				$cri->addCondition('t.status = '.$status,'AND');
				$cri->addCondition('t.is_close = 0','AND');
			}			
			$result['status'] = $status;
		}



		$cri->select = "t.*, member.name as mname, industry.name as iname, member.nick_name, member.store_disable, number_train_top.number";
		$cri->join = "left join member on member.id = t.member_id
								left join industry on industry.id = t.industry 
				                left join number_train_top on t.id = number_train_top.train_id";
		$cri->group = "t.id";
		$cri->order = "istop desc, created_time desc ";

		$pages = new CPagination();
		$pages->itemCount = $model->count($cri);
		$pages->pageSize = 50;
		$pages->applyLimit($cri);
		$items = $model->findAll($cri);
		$info =  Industry::model ()->findAll ('parent_id = 0');
		$industryInfo = array();
		foreach ($info as $key => $value) {
			$industryInfo[$value['id']] = $value['name'];
		}

		$url = Yii::app()->request->getUrl();
		$cookie = new CHttpCookie('benben-neverland',$url);
		$cookie->expire = time()+3600;  
		Yii::app()->request->cookies['benben-neverland']=$cookie;
		$this->render('index',array('items'=>$items,'pages'=> $pages,'result' => $result, 
				'province' => $province,'res' => $res, 'res2' => $res2, 'industryInfo'=>$industryInfo));

	}


	public function actionDelete($id)
	{
		$id = Frame::getIntFromRequest('id');
		if ($id > 0) {
			$model = NumberTrain::model ()->findByPk ( $id );
		}
		if ($model) {
			$model->delete();
		}
		$this->redirect ( Yii::app()->createUrl('numberTrain/index',array('page'=>intval($_REQUEST['page']))));
	}
	
	public function actionTop()
	{
		$id = Frame::getIntFromRequest('id');
		$top = Frame::getIntFromRequest('top');
		$number = Frame::getIntFromRequest('number');
		if ($id > 0) {
			$model = NumberTrain::model ()->findByPk ( $id );
			if($top > 0 && $model->istop > 0){
				echo 3;
				exit;
			}
			$count = NumberTrain::model()->count("status = 0 and istop = {$top}");
			if($top && ($count > 0)){
				echo 4;
				exit;
			}
			$count = NumberTrain::model()->count("status = 0 and istop > 0");
			if($top && ($count >= 3)){
				echo 2;
				exit;
			}			
		}
		if ($model) {
			$model->istop = $top;
			if($model->update()){
				$ntop = new NumberTrainTop();
				$ntop->train_id = $id;
				$ntop->user_id = $this->getLoginId();
				$ntop->created_time = time();
				$ntop->istop = $top;
				$ntop->number = $number;
				$ntop->save();
			}
		}
		echo "1";
		// $this->redirect ( Yii::app()->createUrl('numberTrain/index',array('page'=>intval($_REQUEST['page']))));
	}


	public function actionLog()
	{
		$id = Frame::getIntFromRequest('id');
		$connection = Yii::app()->db;
		$sql = "select a.*, b.username as name from number_train_top as a left join user as b on a.user_id = b.id where a.train_id = {$id} order by a.created_time desc";
		
		$command = $connection->createCommand($sql);
		$result1 = $command->queryAll();//var_dump($result1);exit();
		
		$this->render('log',array('items'=>$result1));
	}

	public function actionStatistic()
	{
		$created_time1 = Frame::getStringFromRequest('created_time1');
		$created_time2 = Frame::getStringFromRequest('created_time2');
		$connection = Yii::app()->db;
		$sql = "select count(*) as c from number_train";
		$command = $connection->createCommand($sql);
		$totalQuery = $command->queryAll();
		$totalNumber = $totalQuery[0]['c'];
		//查看报价次数
		$sql = "SELECT store_id, count(*) c FROM quote where 1 = 1 ";
		if (strtotime($created_time1) > 0) {
			$sql .= ' and created_time >= '.strtotime($created_time1);
			$info['created_time1'] = $created_time1;
		}
		if (strtotime($created_time2) > 0) {
			$sql .= ' and created_time <= '.(strtotime($created_time2)+3600*24);
			$info['created_time2'] = $created_time2;
		}

		$sql .= ' group by store_id ';
		$command = $connection->createCommand($sql);
		$quoteQuery = $command->queryAll();
		$quoteInfo = array(
			array('number'=>count($quoteQuery), 'name'=>'全部'),
			array('number'=>$totalNumber - count($quoteQuery), 'name'=>'0'),
			array('number'=>0, 'name'=>'1-5'),
			array('number'=>0, 'name'=>'6-15'),
			array('number'=>0, 'name'=>'16-50'),
			array('number'=>0, 'name'=>'51-100'),
			array('number'=>0, 'name'=>'101-300'),
			array('number'=>0, 'name'=>'301-500'),
			array('number'=>0, 'name'=>'500以上')
		);
		if ($quoteQuery) {
			foreach ($quoteQuery as $key => $value) {
				if ($value['c'] <= 5) {
					$quoteInfo[2]['number']++;
					$quoteInfo[2]['info'][] = $value['store_id'];
				}else if ($value['c'] <= 15){
					$quoteInfo[3]['number']++;
					$quoteInfo[3]['info'][] = $value['store_id'];
				}else if ($value['c'] <= 50){
					$quoteInfo[4]['number']++;
					$quoteInfo[4]['info'][] = $value['store_id'];
				}else if ($value['c'] <= 100){
					$quoteInfo[5]['number']++;
					$quoteInfo[5]['info'][] = $value['store_id'];
				}else if ($value['c'] <= 300){
					$quoteInfo[6]['number']++;
					$quoteInfo[6]['info'][] = $value['store_id'];
				}else if ($value['c'] <= 500){
					$quoteInfo[7]['number']++;
					$quoteInfo[7]['info'][] = $value['store_id'];
				}else {
					$quoteInfo[8]['number']++;
					$quoteInfo[8]['info'][] = $value['store_id'];
				}
			}
		}

		//查看收藏次数
		$sql = "SELECT number_train_id, count(*) c FROM number_train_collect where 1 = 1 ";
		if (strtotime($created_time1) > 0) {
			$sql .= ' and created_time >= '.strtotime($created_time1);
			$info['created_time1'] = $created_time1;
		}
		if (strtotime($created_time2) > 0) {
			$sql .= ' and created_time <= '.(strtotime($created_time2)+3600*24);
			$info['created_time2'] = $created_time2;
		}

		$sql .= ' group by number_train_id ';
		$command = $connection->createCommand($sql);
		$collectQuery = $command->queryAll();
		$collectInfo = array(
			array('number'=>count($collectQuery), 'name'=>'全部'),
			array('number'=>$totalNumber - count($collectQuery), 'name'=>'0'),
			array('number'=>0, 'name'=>'1-10'),
			array('number'=>0, 'name'=>'11-20'),
			array('number'=>0, 'name'=>'21-50'),
			array('number'=>0, 'name'=>'51-100'),
			array('number'=>0, 'name'=>'101-200'),
			array('number'=>0, 'name'=>'201-500'),
			array('number'=>0, 'name'=>'500以上')
			);
		if ($collectQuery) {
			foreach ($collectQuery as $key => $value) {
				if ($value['c'] <= 10) {
					$collectInfo[2]['number']++;
					$collectInfo[2]['info'][] = $value['number_train_id'];
				}else if ($value['c'] <= 20){
					$collectInfo[3]['number']++;
					$collectInfo[3]['info'][] = $value['number_train_id'];
				}else if ($value['c'] <= 50){
					$collectInfo[4]['number']++;
					$collectInfo[4]['info'][] = $value['number_train_id'];
				}else if ($value['c'] <= 100){
					$collectInfo[5]['number']++;
					$collectInfo[5]['info'][] = $value['number_train_id'];
				}else if ($value['c'] <= 200){
					$collectInfo[6]['number']++;
					$collectInfo[6]['info'][] = $value['number_train_id'];
				}else if ($value['c'] <= 500){
					$collectInfo[7]['number']++;
					$collectInfo[7]['info'][] = $value['number_train_id'];
				}else {
					$collectInfo[8]['number']++;
					$collectInfo[8]['info'][] = $value['number_train_id'];
				}
			}
		}

		//奔犇用户中是否开通号码直通车
		//总人数
		$sql = "select count(*) as c from member where id_enable = 1";
		$command = $connection->createCommand($sql);
		$totalQuery = $command->queryAll();
		$totalNumber = $totalQuery[0]['c'];
		$sql = "select count(distinct(member_id)) as c from number_train ";
		$command = $connection->createCommand($sql);
		$totalQuery = $command->queryAll();
		$totalStore = $totalQuery[0]['c'];

		//统计发送过小喇叭的号码直通车数量
		$sql = "select count(*) c, member_id  from broadcasting_log where type = 1 group by member_id ";
		$command = $connection->createCommand($sql);
		$totalQuery = $command->queryAll();
		$sendBroad = array(1=>0, 2=>0);
		foreach($totalQuery as $e){
			$sendBroad[$e['c']]++;
		}

		//号码直通车邀请好友数量
		$sql = "select member_id from number_train ";
		$command = $connection->createCommand($sql);
		$totalQuery = $command->queryAll();
		$allMember = array();
		if ($totalQuery) {
			foreach($totalQuery as $e){
				$allMember[] = $e['member_id'];
			}
		}
		$sql = "select member_id, count(*) c from benben_invite_log where member_id in (".implode(",", $allMember).") group by member_id";
		$command = $connection->createCommand($sql);
		$broadQuery = $command->queryAll();
		$broadInfo = array(
			array('number'=>$totalNumber - count($broadQuery), 'name'=>'0'),
			array('number'=>0, 'name'=>'1-10'),
			array('number'=>0, 'name'=>'11-20'),
			array('number'=>0, 'name'=>'21-30'),
			array('number'=>0, 'name'=>'31-40'),
			array('number'=>0, 'name'=>'40以上')
			);
		$broadCount = count($broadQuery);
		if ($broadQuery) {
			foreach ($broadQuery as $key => $value) {
				if ($value['c'] <= 10) {
					$broadInfo[1]['number']++;
					$broadInfo[1]['info'][] = $value['member_id'];
				}else if ($value['c'] <= 20){
					$broadInfo[2]['number']++;
					$broadInfo[2]['info'][] = $value['member_id'];
				}else if ($value['c'] <= 30){
					$broadInfo[3]['number']++;
					$broadInfo[3]['info'][] = $value['member_id'];
				}else if ($value['c'] <= 40){
					$broadInfo[4]['number']++;
					$broadInfo[4]['info'][] = $value['member_id'];
				}else {
					$broadInfo[5]['number']++;
					$broadInfo[5]['info'][] = $value['member_id'];
				}
			}
		}

		
		$this->render('statistic',array('quote'=>$quoteInfo, 'collect'=>$collectInfo, 'info'=>$info, 'totalNumber'=>$totalNumber, 'totalStore'=>$totalStore, 'totalBroad'=>$totalBroad, 'sendBroad'=>$sendBroad, 'broadInfo'=>$broadInfo, 'broadCount'=>$broadCount));
	}

	public function actionStatisticDownload()
	{
		$str = Frame::getStringFromRequest('str');
		$code = Frame::getStringFromRequest('code');
		$key = Frame::getIntFromRequest('key');
		$type = Frame::getIntFromRequest('type');
		$created_time1 = Frame::getStringFromRequest('created_time1');
		$created_time2 = Frame::getStringFromRequest('created_time2');
		$connection = Yii::app()->db;
		if ($code != md5($str.'excel')) {
			die();
		}
		if ($key == 0) {
			if ($type == 1) {
				$sql = "SELECT store_id as item_id FROM quote where 1 = 1 ";
			}else{
				$sql = "SELECT number_train_id as item_id  FROM number_train_collect where 1 = 1 ";
			}
			
			if (strtotime($created_time1) > 0) {
				$sql .= ' and created_time >= '.strtotime($created_time1);
				$info['created_time1'] = $created_time1;
			}
			if (strtotime($created_time2) > 0) {
				$sql .= ' and created_time <= '.(strtotime($created_time2)+3600*24);
				$info['created_time2'] = $created_time2;
			}
			$sql .= ' group by item_id ';
			$command = $connection->createCommand($sql);
			$totalQuery = $command->queryAll();
			$totalId = array();
			if ($totalQuery) {
				foreach($totalQuery as $each){
					$totalId[] = $each['item_id'];
				}
				$finalsql = "select  t.short_name, industry.name as iname, t.province, t.city, t.area,  t.phone, member.nick_name as mname, t.created_time,t.id from number_train t  left join member on member.id = t.member_id
								left join industry on industry.id = t.industry where t.id  in (".implode(",", $totalId).")";
			}else{
				$finalsql = "select t.short_name, industry.name as iname, t.province, t.city, t.area,  t.phone, member.nick_name as mname, t.created_time ,t.id, from number_train t left join member on member.id = t.member_id
								left join industry on industry.id = t.industry";
			}
			
		}else if($key == 1){
			if ($type == 1) {
				$sql = "SELECT store_id as item_id FROM quote where 1 = 1 ";
			}else{
				$sql = "SELECT number_train_id as item_id  FROM number_train_collect where 1 = 1 ";
			}
			
			if (strtotime($created_time1) > 0) {
				$sql .= ' and created_time >= '.strtotime($created_time1);
				$info['created_time1'] = $created_time1;
			}
			if (strtotime($created_time2) > 0) {
				$sql .= ' and created_time <= '.(strtotime($created_time2)+3600*24);
				$info['created_time2'] = $created_time2;
			}
			$sql .= ' group by item_id ';
			$command = $connection->createCommand($sql);
			$totalQuery = $command->queryAll();
			$totalId = array();
			if ($totalQuery) {
				foreach($totalQuery as $each){
					$totalId[] = $each['item_id'];
				}
				$finalsql = "select  t.short_name, industry.name as iname, t.province, t.city, t.area,  t.phone, member.nick_name as mname, t.created_time,t.id from number_train t  left join member on member.id = t.member_id
								left join industry on industry.id = t.industry where t.id not in (".implode(",", $totalId).")";
			}else{
				$finalsql = "select t.short_name, industry.name as iname, t.province, t.city, t.area,  t.phone, member.nick_name as mname, t.created_time ,t.id, from number_train t left join member on member.id = t.member_id
								left join industry on industry.id = t.industry";
			}

		}else if($str){
			$totalId = explode("|", $str);
			if (count($totalId) > 0) {
				$finalsql = "select  t.short_name, industry.name as iname, t.province, t.city, t.area,  t.phone, member.nick_name as mname, t.created_time, t.id from number_train t left join member on member.id = t.member_id
								left join industry on industry.id = t.industry where t.id in (".implode(",", $totalId).")";
			}else{
				$finalsql = "select t.short_name, industry.name as iname, t.province, t.city, t.area,  t.phone, member.nick_name as mname, t.created_time, t.id from number_train t left join member on member.id = t.member_id
								left join industry on industry.id = t.industry";
			}

		}
		$countInfo = array();
		if ($finalsql) {
			$command = $connection->createCommand($finalsql);
			$allInfo = $command->queryAll();
			$allInfoid = array();
			if ($allInfo) {
				foreach($allInfo as $e){
					if($e['id']) $allInfoid[] = $e['id'];
				}
			}
			if (count($allInfoid)) {
				if ($type == 1) {
					$sql = "select store_id  as item_id , count(*) as c FROM quote where store_id in (".implode(",", $allInfoid).") group by store_id";
				}else{
					$sql = "SELECT number_train_id as item_id, count(*) as c  FROM number_train_collect where number_train_id in (".implode(",", $allInfoid).") group by number_train_id";
				}
				$command = $connection->createCommand($sql);
				$queryResult = $command->queryAll();
				if ($queryResult) {
					foreach($queryResult as $e){
						$countInfo[$e['item_id']] = $e['c'];
					}
				}
			}
			
		}

		
		if ($allInfo) {
			$objPHPExcel = new PHPExcel();
			/*--------------设置表头信息------------------*/
			//第一个sheet
			$countName = '报价次数';
			if ($type == 2) {
				$countName = '收藏次数';
			}
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A1', '编号')
			->setCellValue('B1', '简称')
			->setCellValue('C1', $countName)
			->setCellValue('D1', '地区')
			->setCellValue('E1', '行业')
			->setCellValue('F1', '创建人')
			->setCellValue('G1', '手机号')
			->setCellValue('H1', '创建日期')
			;
			
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
			$styleArray1 = array(
					'font' => array(
							'bold' => true,
							'color'=>array(
									'argb' => '00000000',
							),
					),
					'alignment' => array(
							'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					),
			);
			$objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray1);
			$objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($styleArray1);
			$objPHPExcel->getActiveSheet()->getStyle('C1')->applyFromArray($styleArray1);
			$objPHPExcel->getActiveSheet()->getStyle('D1')->applyFromArray($styleArray1);
			$objPHPExcel->getActiveSheet()->getStyle('E1')->applyFromArray($styleArray1);
			$objPHPExcel->getActiveSheet()->getStyle('F1')->applyFromArray($styleArray1);
			$objPHPExcel->getActiveSheet()->getStyle('G1')->applyFromArray($styleArray1);
			$objPHPExcel->getActiveSheet()->getStyle('H1')->applyFromArray($styleArray1);
			$objPHPExcel->getActiveSheet()->setTitle('直通车');      //设置sheet的名称
			$objPHPExcel->setActiveSheetIndex(0);   //设置sheet的起始位置
			$pro = array();
			$pro_arr = array();
			foreach ($allInfo as $value){
				$pro[] = $value['province'];
				$pro[] = $value['city'];
				$pro[] = $value['area'];
			}
				
			$pro_name = $this->allareas(implode(",", $pro));
			if($pro_name){
				foreach ($pro_name as $val){
					$pro_arr[$val['bid']] = $val['area_name'];
				}
			}
	
			$i =2;
			foreach ($allInfo as  $one){
				// 			var_dump($one);exit();
				$pron = $pro_arr[$one['province']].''.$pro_arr[$one['city']];
				$currentCount = 0;
				if(isset($countInfo[$one['id']])){
					$currentCount = $countInfo[$one['id']];
				}
				$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue("A$i", $i-1)
				->setCellValue("B$i", $one['short_name'])
				->setCellValue("C$i", $currentCount)
				->setCellValue("D$i", $pron)
				->setCellValue("E$i", $one['iname'])
				->setCellValue("F$i", $one['mname'])				
				->setCellValue("G$i", $one['phone'])
				->setCellValue("H$i", date("Y-m-d H:i:s", $one['created_time']));
				//$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(100);
				$i++;
			}
			
			ob_end_clean();
			ob_start();
			header('Content-Type: application/vnd.ms-excel;charset=utf-8');
			header('Content-Disposition:attachment;filename=' . urlencode('info' . date("YmjHis") .'.xls') . '');
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');   //通过PHPExcel_IOFactory的写函数将上面数据写出来
		
			$objWriter->save('php://output');
		}

		

	}

	public function actionBenbendownload()
	{
		$type = intval($_GET['type']);
		$connection = Yii::app()->db;
		$sql = "select member_id from number_train ";
		$command = $connection->createCommand($sql);
		$totalQuery = $command->queryAll();
		$allMember = array();
		if ($totalQuery) {
			foreach($totalQuery as $e){
				$allMember[] = $e['member_id'];
			}
		}
		if ($type == 1) {
			$sql = "select id, benben_id, name, nick_name, phone, province,city,sex, area from member where id in (".implode(",", $allMember).")";
		}else{
			$sql = "select id, benben_id, name, nick_name, phone, province,city,sex, area from member where id not in (".implode(",", $allMember).")";
		}
		$command = $connection->createCommand($sql);
		$totalQuery = $command->queryAll();
		$areaId = array();
		foreach($totalQuery as $each){
			if ($each['province']) {$areaId[] = $each['province'];}
			if ($each['city']) {$areaId[] = $each['city'];}
			if ($each['area']) {$areaId[] = $each['area'];}
		}
		$areaInfo = $this->currentGetArea($areaId);
		if ($totalQuery) {
			$objPHPExcel = new PHPExcel();
			/*--------------设置表头信息------------------*/
			
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A1', '编号')
			->setCellValue('B1', '奔犇号')
			->setCellValue('C1', '昵称')
			->setCellValue('D1', '手机号码')
			->setCellValue('E1', '地区')
			->setCellValue('F1', '性别')
			;
			
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
			$styleArray1 = array(
					'font' => array(
							'bold' => true,
							'color'=>array(
									'argb' => '00000000',
							),
					),
					'alignment' => array(
							'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					),
			);
			$objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray1);
			$objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($styleArray1);
			$objPHPExcel->getActiveSheet()->getStyle('C1')->applyFromArray($styleArray1);
			$objPHPExcel->getActiveSheet()->getStyle('D1')->applyFromArray($styleArray1);
			$objPHPExcel->getActiveSheet()->getStyle('E1')->applyFromArray($styleArray1);
			$objPHPExcel->getActiveSheet()->getStyle('F1')->applyFromArray($styleArray1);
			$objPHPExcel->getActiveSheet()->setTitle('奔犇用户中是否开通号码直通车');      //设置sheet的名称
			$objPHPExcel->setActiveSheetIndex(0);   //设置sheet的起始位置
			
	
			$i =2;
			foreach ($totalQuery as  $one){
				$cP = '';$cC = '';$cA = '';
				if (isset($areaInfo[$one['province']])) {
					$cP = $areaInfo[$one['province']];
				}
				if (isset($areaInfo[$one['city']])) {
					$cC = $areaInfo[$one['city']];
				}
				if (isset($areaInfo[$one['area']])) {
					$cA = $areaInfo[$one['area']];
				}
				
				if ($one['sex']== 1) {
					$gender = '男';
				}else if ($one['sex']== 2) {
					$gender = '女';
				}else{
					$gender = '未知';
				}
				$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue("A$i", $i-1)
				->setCellValue("B$i", $one['benben_id'])
				->setCellValue("C$i",  $one['nick_name'])
				->setCellValue("D$i", $one['phone'])
				->setCellValue("E$i",$cP.$cC.$cA)
				->setCellValue("F$i", $gender)	;
				//$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(100);
				$i++;
			}
			
			ob_end_clean();
			ob_start();
			header('Content-Type: application/vnd.ms-excel;charset=utf-8');
			header('Content-Disposition:attachment;filename=' . urlencode('info' . date("YmjHis") .'.xls') . '');
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');   //通过PHPExcel_IOFactory的写函数将上面数据写出来
		
			$objWriter->save('php://output');
		}
	}

	public function actionBroaddownload()
	{
		$type = intval($_GET['type']);
		$connection = Yii::app()->db;
		$sql = "select count(*) c, member_id  from broadcasting_log where type = 1 group by member_id ";
		$command = $connection->createCommand($sql);
		$totalQuery = $command->queryAll();
		$sendBroad = array(1=>0, 2=>0);
		foreach($totalQuery as $e){
			if ($type == 0) {
				$allMember[] = $e['member_id'];
			}else if($type == $e['c']){
				$allMember[] = $e['member_id'];
			}
		}
		if ($type == 0) {
			$sql = "select member_id from number_train where member_id not in (".implode(",", $allMember).")";
			$command = $connection->createCommand($sql);
			$totalQuery = $command->queryAll();
			$allMember = array();
			if ($totalQuery) {
				foreach($totalQuery as $e){
					$allMember[] = $e['member_id'];
				}
			}
		}

		$sql = "select a.id, a.nick_name, a.name, b.phone, b.province,b.city, b.area, b.telephone, b.short_name, b.industry from member as a left join number_train as b on a.id = b.member_id where a.id in (".implode(",", $allMember).")";
		$command = $connection->createCommand($sql);
		$memberQuery = $command->queryAll();
		$areaId = array();
		foreach($memberQuery as $each){
			if ($each['province']) {$areaId[] = $each['province'];}
			if ($each['city']) {$areaId[] = $each['city'];}
			if ($each['area']) {$areaId[] = $each['area'];}
		}
		$areaInfo = $this->currentGetArea($areaId);
		$sql = "select id, name from industry ";
		$command = $connection->createCommand($sql);
		$totalQuery = $command->queryAll();
		foreach($totalQuery as $each){
			$industryInfo[$each['id']] = $each['name'];
		}
		if ($memberQuery) {
			$objPHPExcel = new PHPExcel();
			/*--------------设置表头信息------------------*/
			
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A1', '编号')
			->setCellValue('B1', '姓名')
			->setCellValue('C1', '手机号码')
			->setCellValue('D1', '固话')
			->setCellValue('E1', '简称')
			->setCellValue('F1', '行业')
			->setCellValue('G1', '地区')
			;
			
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
			$styleArray1 = array(
					'font' => array(
							'bold' => true,
							'color'=>array(
									'argb' => '00000000',
							),
					),
					'alignment' => array(
							'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					),
			);
			$objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray1);
			$objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($styleArray1);
			$objPHPExcel->getActiveSheet()->getStyle('C1')->applyFromArray($styleArray1);
			$objPHPExcel->getActiveSheet()->getStyle('D1')->applyFromArray($styleArray1);
			$objPHPExcel->getActiveSheet()->getStyle('E1')->applyFromArray($styleArray1);
			$objPHPExcel->getActiveSheet()->getStyle('F1')->applyFromArray($styleArray1);
			$objPHPExcel->getActiveSheet()->setTitle('发送过小喇叭的直通车用户');      //设置sheet的名称
			$objPHPExcel->setActiveSheetIndex(0);   //设置sheet的起始位置
			
	
			$i =2;
			foreach ($memberQuery as  $one){
				$cP = '';$cC = '';$cA = '';
				if (isset($areaInfo[$one['province']])) {
					$cP = $areaInfo[$one['province']];
				}
				if (isset($areaInfo[$one['city']])) {
					$cC = $areaInfo[$one['city']];
				}
				if (isset($areaInfo[$one['area']])) {
					$cA = $areaInfo[$one['area']];
				}
				
				$currentIndustry = '';
				if(isset($industryInfo[$one['industry']])){
					$currentIndustry = $industryInfo[$one['industry']];
				}
				
				$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue("A$i", $i-1)
				->setCellValue("B$i", $one['name']?$one['name']:$one['nick_name'])
				->setCellValue("C$i",  $one['phone'])
				->setCellValue("D$i", $one['telephone'])
				->setCellValue("E$i",$one['short_name'])
				->setCellValue("F$i", $currentIndustry)	
				->setCellValue("G$i", $cP.$cC.$cA)	;
				//$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(100);
				$i++;
			}
			
			ob_end_clean();
			ob_start();
			header('Content-Type: application/vnd.ms-excel;charset=utf-8');
			header('Content-Disposition:attachment;filename=' . urlencode('info' . date("YmjHis") .'.xls') . '');
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');   //通过PHPExcel_IOFactory的写函数将上面数据写出来
		
			$objWriter->save('php://output');
		}
	}

	public function actionInvitedetail()
	{
		$connection = Yii::app()->db;
		$key = intval($_GET['key']);
		$type = intval($_GET['type']);
		$sql = "select member_id from number_train ";
		$command = $connection->createCommand($sql);
		$totalQuery = $command->queryAll();
		$allMember = array();
		if ($totalQuery) {
			foreach($totalQuery as $e){
				$allMember[] = $e['member_id'];
			}
		}
		$sql = "select member_id, count(*) c from benben_invite_log where member_id in (".implode(",", $allMember).") group by member_id";
		$command = $connection->createCommand($sql);
		$broadQuery = $command->queryAll();
		$matchMember = array();
		$memberInvite = array();
		if ($broadQuery) {
			foreach ($broadQuery as $k => $value) {
				$memberInvite[$value['member_id']] = $value['c'];
				if ($value['c'] <= 10 && $key == 1) {
					$matchMember[] = $value['member_id'];
				}else if ($value['c']  > 10 && $value['c'] <= 20  && $key == 2){
					$matchMember[] = $value['member_id'];
				}else if ($value['c']  > 20 && $value['c'] <= 30 && $key == 3){
					$matchMember[] = $value['member_id'];
				}else if ($value['c']  > 30 && $value['c'] <= 40  && $key == 4){
					$matchMember[] = $value['member_id'];
				}else if  ($value['c'] > 40  && $key == 5){
					$matchMember[] = $value['member_id'];
				}
			}
		}
		$areaInfo = array();
		$friendInfo = array();
		$allFriendInfo = array();
		$sendBroad = array();
		if (count($matchMember)) {
			$sql = "select a.id, a.name, a.nick_name, a.phone, a.benben_id, b.short_name, b.industry, b.telephone, b.city, b.province, b.area from member a left join number_train b on a.id = b.member_id where a.id in (".implode(",", $matchMember).")";
			$command = $connection->createCommand($sql);
			$matchQuery = $command->queryAll();
			$matchId = array();
			if ($matchQuery) {
				foreach($matchQuery as $each){
					$matchId[] = $each['id'];
					if ($each['province']) {$areaId[] = $each['province'];}
					if ($each['city']) {$areaId[] = $each['city'];}
					if ($each['area']) {$areaId[] = $each['area'];}
				}
				$areaInfo = $this->currentGetArea($areaId);
			}
			if (count($matchId)) {
				$csql = "select count(a.id) c, b.member_id,a.is_baixing from group_contact_phone a left join group_contact_info b on a.contact_info_id = b.id where a.is_benben > 0 and b.member_id  in (".implode(",", $matchId).")";
				$csql .= ' group by b.member_id order by c desc';
				$memberCount = array();
				$command = $connection->createCommand($csql);
				$friendQuery = $command->queryAll();
				if ($friendQuery) {
					foreach($friendQuery as $e){
						$friendInfo[$e['member_id']] = $e['c'];
					}
				}

				$csql = "select count(*) c, member_id from group_contact_info  where member_id  in (".implode(",", $matchId).")";
				$csql .= ' group by member_id order by c desc';
				$command = $connection->createCommand($csql);
				$friendQuery = $command->queryAll();
				if ($friendQuery) {
					foreach($friendQuery as $e){
						$allFriendInfo[$e['member_id']] = $e['c'];
					}
				}

				$sql = "select member_id, count(*) c from broadcasting_log where type = 1 and member_id in (".implode(",", $matchId).") group by member_id";
				$command = $connection->createCommand($sql);
				$broadQuery = $command->queryAll();
				if ($broadQuery) {
					foreach($broadQuery as $e){
						$broadInfo[$e['member_id']] = $e['c'];
					}
				}
			}
		}
		$sql = "select id, name from industry ";
		$command = $connection->createCommand($sql);
		$totalQuery = $command->queryAll();
		foreach($totalQuery as $each){
			$industryInfo[$each['id']] = $each['name'];
		}
		if ($type == 1) {
			if ($matchQuery) {
				$objPHPExcel = new PHPExcel();
				/*--------------设置表头信息------------------*/
				
				$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A1', '编号')
				->setCellValue('B1', '姓名')
				->setCellValue('C1', '手机号码')
				->setCellValue('D1', '奔犇号')
				->setCellValue('E1', '固话')
				->setCellValue('F1', '简称')
				->setCellValue('G1', '行业')
				->setCellValue('H1', '地区')
				->setCellValue('I1', '邀请奔犇数量')
				->setCellValue('J1', '通讯录中好友数量')
				->setCellValue('K1', '通讯录中奔犇数量')
				->setCellValue('L1', '发送小喇叭数量')
				;
				
				$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
				$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(30);
				$styleArray1 = array(
						'font' => array(
								'bold' => true,
								'color'=>array(
										'argb' => '00000000',
								),
						),
						'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
						),
				);
				$objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray1);
				$objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($styleArray1);
				$objPHPExcel->getActiveSheet()->getStyle('C1')->applyFromArray($styleArray1);
				$objPHPExcel->getActiveSheet()->getStyle('D1')->applyFromArray($styleArray1);
				$objPHPExcel->getActiveSheet()->getStyle('E1')->applyFromArray($styleArray1);
				$objPHPExcel->getActiveSheet()->getStyle('F1')->applyFromArray($styleArray1);
				$objPHPExcel->getActiveSheet()->setTitle('号码直通车邀请好友数量');      //设置sheet的名称
				$objPHPExcel->setActiveSheetIndex(0);   //设置sheet的起始位置
				
		
				$i =2;
				foreach ($matchQuery as  $one){
					$cP = '';$cC = '';$cA = '';
					if (isset($areaInfo[$one['province']])) {
						$cP = $areaInfo[$one['province']];
					}
					if (isset($areaInfo[$one['city']])) {
						$cC = $areaInfo[$one['city']];
					}
					if (isset($areaInfo[$one['area']])) {
						$cA = $areaInfo[$one['area']];
					}
					
					$currentIndustry = '';
					if(isset($industryInfo[$one['industry']])){
						$currentIndustry = $industryInfo[$one['industry']];
					}
					$invite = 0;
					if(isset($memberInvite[$one['id']])){
						$invite = $memberInvite[$one['id']];
					}
					$friend = 0;
					if (isset($friendInfo[$one['id']])) {
						$friend = $friendInfo[$one['id']];
					}
					$allfriend = 0;
					if (isset($allFriendInfo[$one['id']])) {
						$allfriend = $allFriendInfo[$one['id']];
					}
					$broad = 0;
					if (isset($broadInfo[$one['id']])) {
						$broad = $broadInfo[$one['id']];
					}
					
					$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue("A$i", $i-1)
					->setCellValue("B$i", $one['name']?$one['name']:$one['nick_name'])
					->setCellValue("C$i",  $one['phone'])
					->setCellValue("D$i",  $one['benben_id'])
					->setCellValue("E$i", $one['telephone'])
					->setCellValue("F$i",$one['short_name'])
					->setCellValue("G$i", $currentIndustry)	
					->setCellValue("H$i", $cP.$cC.$cA)	
					->setCellValue("I$i", $invite)
					->setCellValue("J$i", $allfriend)	
					->setCellValue("K$i", $friend)	
					->setCellValue("L$i", $broad)	;
					//$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(100);
					$i++;
				}
				
				ob_end_clean();
				ob_start();
				header('Content-Type: application/vnd.ms-excel;charset=utf-8');
				header('Content-Disposition:attachment;filename=' . urlencode('info' . date("YmjHis") .'.xls') . '');
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');   //通过PHPExcel_IOFactory的写函数将上面数据写出来
			
				$objWriter->save('php://output');
			}
			die();
		}
		$this->render('detail',array('items'=>$matchQuery, 'memberInvite'=>$memberInvite, 'areaInfo'=>$areaInfo, 'industryInfo'=>$industryInfo, 'friendInfo'=>$friendInfo, 'broadInfo'=>$broadInfo, 'allFriendInfo'=>$allFriendInfo));

	}

	function currentGetArea($areaId){
		global $connection;
		if (!$connection) {
			$connection = Yii::app()->db;
		}
		$sql = "select bid, area_name from area where bid in (".implode(",", array_unique($areaId)).")";
			$command = $connection->createCommand($sql);
			$areaQuery = $command->queryAll();
			foreach($areaQuery as $each){
				$areaInfo[$each['bid']] = $each['area_name'];
			}
			return $areaInfo;
	}


	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return NumberTrain the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=NumberTrain::model()->findByPk($id);
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
			return Yii::app()->createUrl("numberTrain/index",array('page'=>$_REQUEST['page']));
		}
	}

	/**
	 * Performs the AJAX validation.
	 * @param NumberTrain $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='number-train-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
