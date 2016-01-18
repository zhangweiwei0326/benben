<?php

class NoticeController extends BaseController
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/admin';
	
	/**
	 * @var int the define the index for the menu
	 */
	 
	 public $menuIndex =60;
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new News;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['News']))
		{
			$model->sender = $this->getLoginId();
			$model->type = 1;
			$model->content = $_POST['News']['content'];
			$model->member_id = 0;
			$model->status = 0;
			$model->created_time = strtotime($_POST['News']['created_time']);
			if($model->save())
				//保存到记录表
				$newslog = new NewsLog();
				$newslog->sender = $this->getLoginId();;
				$newslog->content = $_POST['News']['content'];				
				$newslog->member_id = 0;
				$newslog->type = 2;
				$newslog->created_time = strtotime($_POST['News']['created_time']);				
				$newslog->save();
				$this->redirect($this->getBackListPageUrl());
		}

		if ($model->created_time) {
			$model->created_time = date("Y-m-d H:i:s", $model->created_time);
		}else{
			$model->created_time = date("Y-m-d H:i:s");
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
		$type = addslashes($_GET['type']);
		if($type){
			$this->menuIndex =61;
			$model=NewsLog::model()->findByPk($id);
			if($model===null)
				throw new CHttpException(404,'The requested page does not exist.');			
		}else{
			$model=$this->loadModel($id);
		}
		
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['News']))
		{
			$model->content = $_POST['News']['content'];
			$model->created_time = strtotime($_POST['News']['created_time']);
			if($model->save())
				$this->redirect($this->getBackListPageUrl());
		}
		if ($model->created_time) {
			$model->created_time = date("Y-m-d H:i:s", $model->created_time);
		}else{
			$model->created_time = date("Y-m-d H:i:s");
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
		$this->insert_log(60);
		$result = array();
		$name = $_GET['name'];
		$created_time1= $_GET['created_time1'];
		$created_time2= $_GET['created_time2'];
		
		$model = NewsLog::model();
		$cri = new CDbCriteria();
		
		if(!empty($name)){
			$cri->addSearchCondition('u.username', $name, true, 'AND');
			$result['name'] = $name;
		}
		
		if($created_time1 && $created_time2){
			$ct1 = strtotime($created_time1);
			$ct2 = strtotime($created_time2)+86399;
		
			if($ct1 >= $ct2){
				$result['msg'] = "创建时间第一个必须比第二个小!";
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
		
		$cri->select = "t.id, t.content,t.created_time, u.username as sender";
		$cri->join = "left join user u on t.sender = u.id ";
		$cri->addCondition('t.type = 2');
		$cri->order = "t.id desc";
		$pages = new CPagination();
		$pages->itemCount = $model->count($cri);
		$pages->pageSize = 50;
		$pages->applyLimit($cri);
		$items = $model->findAll($cri);
		$this->render('index',array('items'=>$items,'pages'=> $pages,'result' => $result));
		
	}
	
	public function actionPushindex()
	{
		$this->insert_log(61);
		$this->menuIndex =61;
		$result = array();
		$name = $_GET['name'];
		$unit = $_GET['unit'];
		$province = $this->getProvince();
		$post_province = intval($_GET['province']);
		if ($post_province > 0) {
			$res = $this->getCity($post_province);
		}
		$post_city = intval($_GET['city']);
		if ($post_city > 0) {
			$res2 = $this->getArea($post_city);
		}
		$post_area = intval($_GET['area']);
		if ($post_area > 0) {
			$res3 = $this->getStreet($post_area);
		}
		$created_time1= $_GET['created_time1'];
		$created_time2= $_GET['created_time2'];
	
		$model = NewsLog::model();
		$cri = new CDbCriteria();
	
		if(!empty($name)){
			$cri->addSearchCondition('u.username', $name, true, 'AND');
			$result['name'] = $name;
		}
		
		if(!empty($unit)){
			$cri->addSearchCondition('t.unit', $unit, true, 'AND');
			$result['unit'] = $unit;
		}
	
		if($created_time1 && $created_time2){
			$ct1 = strtotime($created_time1);
			$ct2 = strtotime($created_time2)+86399;
	
			if($ct1 >= $ct2){
				$result['msg'] = "创建时间第一个必须比第二个小!";
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
		if($post_province && ($post_province != -1)){
			$cri->addCondition('t.province = '.$post_province,'AND');
			$result['province'] = $post_province;
		}
		
		if($post_city && ($post_city != -1)){
			$cri->addCondition('t.city = '.$post_city,'AND');
			$result['city'] = $post_city;
		}
		
		if($post_area && ($post_area != -1)){
			$cri->addCondition('t.area = '.$post_area,'AND');
			$result['area'] = $post_area;
		}
		
		if($_GET['street'] && ($_GET['street'] != -1)){
			$cri->addCondition('t.street = '.$_GET['street'],'AND');
			$result['street'] = $_GET['street'];
		}
	
		$cri->select = "t.*, u.username as sender";
		$cri->join = "left join user u on t.sender = u.id ";
 		$cri->addCondition('t.type = 1');
		$cri->order = "t.id desc";
		$pages = new CPagination();
		$pages->itemCount = $model->count($cri);
		$pages->pageSize = 50;
		$pages->applyLimit($cri);
		$items = $model->findAll($cri);
		if ($items) {
			$areaItem = array();
			foreach ($items as $key => $value) {
				$areaItem[] = $value ['province'];
				$areaItem[] = $value ['city'];
				$areaItem[] = $value ['area'];
				$areaItem[] = $value ['street'];
			}
			$area = new Area();
			$sql = "select bid,  area_name from area where bid in (".implode(",", $areaItem).")";
			$areaResult = $area->findAllBySql($sql);
			foreach ($areaResult as $key => $value) {
				$areaInfo[$value['bid']] = $value['area_name'];
			}
		}
		$this->render('pushindex',array('items'=>$items,'pages'=> $pages,'result' => $result, 
				'province' => $province,'res' => $res, 'res2' => $res2, 'res3' => $res3,'areaInfo'=>$areaInfo));
	
	}
	
	public function actionPush(){
		$this->menuIndex =61;
		$provinceall = $this->getProvince();
		
		if ((Yii::app()->request->isPostRequest)) {
			$condition = array();
			$province = intval($_POST['province']);
			$city = intval($_POST['city']);
			$area = intval($_POST['area']);
			$street = intval($_POST['street']);
			$sex = intval($_POST['sex']);
			$age1 = $this->birthday($_POST['age1']);
			if($_POST['age2']){
				$age2 = $this->birthday($_POST['age2']+1);
			}
			$phone = addslashes($_POST['phone']);
			$is_store = addslashes($_POST['is_store']);
			$is_baixing = addslashes($_POST['is_baixing']);
			$phone_model = addslashes($_POST['phone_model']);
			$phone_num = addslashes($_POST['phone_num']);
			$content = addslashes($_POST['content']);
			$unit = addslashes($_POST['unit']);
			$getnum = intval($_POST['getnum']);						
			if ($province > 0) {
				$condition[] = "省: ".$this->areas($province);
				$res = $this->getCity($province);
			}
			if ($city > 0) {
				$condition[] = "市: ".$this->areas($city);
				$res2 = $this->getArea($city);
			}
			if ($area > 0) {
				$condition[] = "区县: ".$this->areas($area);
				$res3 = $this->getStreet($area);
			}
			if($street > 0){
				$condition[] = "街道: ".$this->areas($street);
			}
			if($sex > 0){
				$condition[] = "性别: ".($sex == 1 ? "男":"女");
			}
			if($_POST['age1']){
				$condition[] = "年龄段: ".$_POST['age1'].($_POST['age2'] ? " 到 ".$_POST['age2']:"");
			}			
			if($phone){
				$condition[] = "手机号码: ".$phone;
			}
			if($is_store > 0){
				$condition[] = "号码直通车: ".($is_store == 1 ? "是":"否");
			}
			if($is_baixing > 0){
				$condition[] = "百姓网用户: ".($is_baixing == 1 ? "是":"否");
			}
			if($phone_model){
				$condition[] = "手机型号: ".$phone_model;
			}
			if($unit){
				$condition[] = "推送单位: ".$unit;
			}
			if(!$content && !$getnum){				
				$this->render("addnews",array('msg' => "内容不能为空",'province' => $province,
						'res' => $res, 'res2' => $res2,
						'res3' => $res3));
			
				exit();
			}
			$connection = Yii::app()->db;
			$all = array();
			$phone_id = array();
			$phoneArr = array();
			//根据条件查出会员
			if(!$phone){
				$model = Member::model();
				$cri = new CDbCriteria();
				$cri->select = "t.id,t.phone";
				if($province && ($province != -1)){
					$cri->addCondition('t.province = '.$province,'AND');
					$result['province'] = $province;
				}
				
				if($city && ($city != -1)){
					$cri->addCondition('t.city = '.$city,'AND');
					$result['city'] = $city;
				}
				
				if($area && ($area != -1)){
					$cri->addCondition('t.area = '.$area,'AND');
					$result['area'] = $area;
				}
				
				if($street && ($street != -1)){
					$cri->addCondition('t.street = '.$street,'AND');
					$result['street'] = $street;
				}
				
				if($sex && ($sex != -1)){
					$cri->addCondition('t.sex = '.$sex,'AND');
					$result['sex'] = $sex;
				}
				
				if($age1 && $age2){
					if($age1 < $age2){
						$msg = "年龄第一个必须比第二个小";
					}else{
						if($age1){
							$cri->addCondition('t.age <= '.$age1,'AND');
							$result['age1'] = $_POST['age1'];
							$result['goback'] = -2;
						}
						if($age2){
							$cri->addCondition('t.age >= '.$age2,'AND');
							$result['age2'] = $_POST['age2'];
							$result['goback'] = -2;
						}
					}
				}else{
					if($age1){
						$cri->addCondition('t.age <= '.$age1,'AND');
						$result['age1'] = $_POST['age1'];
						$result['goback'] = -2;
					}
					if($age2){
						$cri->addCondition('t.age >= '.$age2,'AND');
						$result['age2'] = $_POST['age2'];
						$result['goback'] = -2;
					}
				}
				if($phone_model){					
					$cri->addCondition("t.phone_model like '%{$phone_model}%'", 'AND');
					$result['phone_model'] = $_POST['phone_model'];
					$result['goback'] = -2;
				}
				$items = $model->findAll($cri);
				foreach ($items as $va){
					$all[] = $va->id;
					$phone_id["'".$va->phone."'"] = $va->id;
					$phoneArr[] = "'".$va->phone."'";
				}								
			
			}else{
				$province = 0;
				$city = 0;
				$area = 0;
				$street = 0;
				$sql = "select id,phone from member where phone in ({$phone}) ";
				$command = $connection->createCommand($sql);
				$items = $command->queryAll();
				foreach ($items as $va){
					$all[] = $va['id'];
					$phone_id["'".$va['phone']."'"] = $va['id'];
					$phoneArr[] = "'".$va['phone']."'";
				}
			}
											
			$phoneArr_baixing = array();
			$phoneArr_notbaixing = array();
			$phoneArr_store = array();
			$phoneArr_notstore = array();
			//是否是直通车用户
			if($all && ($is_store == 1 || $is_store == 2)){
				$sql = "select member_id from number_train where member_id in (".implode(",", $all).")";
				$command = $connection->createCommand($sql);
				$items = $command->queryAll();
				foreach ($items as $va){
					$phoneArr_store[] = $va['member_id'];
				}
				foreach ($all as $va){
					if(!in_array($va, $phoneArr_store)){
						$phoneArr_notstore[] = $va;
					}
				}
				if($is_store == 1){
					$all = $phoneArr_store;
				}
				if($is_store == 2){
					$all = $phoneArr_notstore;
				}
			}
							
 			
			//是否是百姓网用户
			if($all && ($is_baixing == 1 || $is_baixing == 2)){
				$sql = "select phone from bxapply where phone in (".implode(",", $phoneArr).") and status = 3"; 
				$command = $connection->createCommand($sql);
				$items = $command->queryAll();
				foreach ($items as $va){
					$phoneArr_baixing[] = $phone_id["'".$va['phone']."'"];
				}
				foreach ($all as $va){
					if(!in_array($va, $phoneArr_baixing)){
						$phoneArr_notbaixing[] = $va;
					}
				}
				if($is_baixing == 1){
					$all = $phoneArr_baixing;
				}
				if($is_baixing == 2){
					$all = $phoneArr_notbaixing;
				}
			}
			
			if($getnum){
				echo count($all);
				exit;
			}
										
			//var_dump($all);exit;
			
			if(!count($all)){			
				$this->render("addnews",array('msg' => "没有符合条件的用户",'province' => $provinceall,
						'res' => $res, 'res2' => $res2,'result' => $result,
						'res3' => $res3));
					
				exit();
			}
			//发消息
			$t = time();
			$userid = $this->getLoginId();
			if($all[0]){
				foreach ($all as $val){
					$sqla[] = "(1,{$userid},{$val},'$content',{$t})";
				}
				$sql = "insert into news (type,sender,member_id,content,created_time) values ".implode(",", $sqla);
				$command = $connection->createCommand($sql);
				$re = $command->execute();
			}
			//保存到记录表
			$newslog = new NewsLog();
			$newslog->sender = $userid;
			$newslog->content = $content;
			$newslog->number = count($all);
			$newslog->unit = $unit;
			$newslog->province = ($province == -1) ? 0 : $province;
			$newslog->city = ($city == -1) ? 0 : $city;
			$newslog->area = ($area == -1) ? 0 : $area;
			$newslog->street = ($street == -1) ? 0 : $street;
			$newslog->member_id = implode(",", $all);
			$newslog->created_time = time();
			$newslog->condition = implode("||", $condition);
			//$sql = "insert into news_log (sender,content,number,unit,province,city,area,street,member_id,created_time) values ".implode(",", $sqla);
			$newslog->save();
			
			$this->redirect(array("/notice/pushindex"));
			
		}
		
		$this->render("addnews",array('province' => $provinceall,
							'res' => $res, 'res2' => $res2,
		                    'res3' => $res3));
	}
	//小喇叭
	public function actionBroadcastingLog()
	{
		$this->insert_log(62);
		$this->menuIndex =62;
		$model = BroadcastingLog::model();
		$cri = new CDbCriteria();
		$cri->order = "id desc";
		if(isset($_GET) && !empty($_GET)){
			$result = array();
			
			$benben_id = Frame::getIntFromRequest('benben_id');
			if($benben_id){
				$cri->addCondition('benben_id ='.$benben_id,'AND');
				$result['benben_id'] = $benben_id;
				$result['goback'] = -2;
			}
			$created_time1 = Frame::getStringFromRequest('created_time1');
			$created_time2 = Frame::getStringFromRequest('created_time2');
			if($created_time1 && $created_time2){
				
					$ct1 = strtotime($created_time1);
					$ct2 = strtotime($created_time2)+86399;
	
					if($ct1 >= $ct2){
						$msg = "申请日期第一个必须比第二个小!";
					}else{
						$cri->addCondition('t.created_time >= '.$ct1,'AND');
						$result['created_time1'] = $created_time1;
						$cri->addCondition('t.created_time <= '.$ct2,'AND');
						$result['created_time2'] = $created_time2;
						$result['goback'] = -2;
					}
			}else{
				if($_GET['created_time1']){
					$cri->addCondition('t.created_time >= '.strtotime($_GET['created_time1']),'AND');
					$result['created_time1'] = $_GET['created_time1'];
					$result['goback'] = -2;
				}
				if($_GET['created_time2']){
					$cri->addCondition('t.created_time <= '.strtotime($_GET['created_time2'])+86399,'AND');
					$result['created_time2'] = $_GET['created_time2'];
					$result['goback'] = -2;
				}
			}
			$phone = $_GET['phone'];
			if(is_numeric($phone)){
				$cri->addSearchCondition('phone', $phone, true, 'AND');
				$result['phone'] = $phone;
				$result['goback'] = -2;
			}
			
// 			//喊话次数

			if($_GET['recive1'] || $_GET['recive2']){
				if ($_GET['recive1']) {
					$con[] = "c >= ".intval($_GET['recive1']);
				}
				if ($_GET['recive2']) {
					$con[] = "c <= ".intval($_GET['recive2']);
				}
				$sql = "select member_id, count(*) c from broadcasting_log group by member_id having ".implode(' and ', $con);
				$apply = new BroadcastingLog();
				$res = $apply->findAllBySql($sql);
				if ($res) {
					$allmember = array();
					foreach($res as $e){
						$allmember[] = $e['member_id'];
					}
					$cri->addInCondition('member_id', $allmember);
				}else{
					$cri->addCondition('id = 0');
				}
				// $rec = $model->findAll();
				// foreach ($rec as $list){
				// 	$member_id[] = $list->member_id;
				// }
				// $cs = array_count_values($member_id);
				// foreach($cs as $val){
				// 	$cri->addCondition("$val <= ".$_GET['recive1'],'and');
				// 	$cri->addCondition("$val <= ".$_GET['recive2'],'and');
				// }
				$result['recive1'] = $_GET['recive1'];
				$result['recive2'] = $_GET['recive2'];
				
			}
			if($_GET['province'] && ($_GET['province'] != -1)){
				$cri->addCondition('province = '.$_GET['province'],'AND');
				$result['province'] = $_GET['province'];
				$result['goback'] = -2;
				$res = $this->getCity($_GET['province']);
			}
				
			if($_GET['city'] && ($_GET['city'] != -1)){
				$cri->addCondition('city = '.$_GET['city'],'AND');
				//$result['city']= $_GET['city'];
				$result['goback'] = -2;
				$res2 = $this->getArea($_GET['city']);
			}
				
			if($_GET['area'] && ($_GET['area'] != -1)){
				$cri->addCondition('area = '.$_GET['area'],'AND');
					
				$result['goback'] = -2;
			}
			
			if($_GET['obj'] && ($_GET['obj'] != -1)){
				if($_GET['obj'] == 1){
					$cri->addCondition("league_id = '' and friend_id != ''",'AND');
					$result['obj'] = $_GET['obj'];
				}
				if($_GET['obj'] == 2){
					$cri->addCondition("league_id != '' and friend_id = ''",'AND');
					$result['obj'] = $_GET['obj'];
				}
				if($_GET['obj'] == 3){
					$cri->addCondition("league_id != '' and friend_id != ''",'AND');
					$result['obj'] = $_GET['obj'];
				}
			}
			
			$is_type = $_GET['is_type'];
			if(isset($is_type)){
				if($is_type == 1){
					$cri->addCondition("type = 1");
				}else if($is_type ==2){
					$cri->addCondition("type = 0");
				}
				$result['is_type'] = $is_type;
				$result['goback'] = -2;
			}
		}
		$cri->select = "t.*,m.id as m_id,m.benben_id as m_benben_id ,m.phone as m_phone, m.province as m_p,m.city as m_c,m.area as m_a";
		$cri->join = "left join member m on t.member_id = m.id";
		
		$pages = new CPagination();
		$pages->itemCount = $model->count($cri);
		$pages->pageSize = 50;
		$pages->applyLimit($cri);
		$items = $model->findAll($cri);
		$province = $this->getProvince();
		//$area = $this->getCity();
		//发送人
		if($items){
		foreach ($items as $list){
// 			$member = Member::model();
// 			$criMember = new CDbCriteria();
// 			$criMember->condition = "id =".$list->member_id;
// 			$itemsMember = $member->find($criMember);
			//喊话次数
			$cri1 = new CDbCriteria();
			$cri1->condition = "member_id =".$list->m_id;
			$counts = count($model->findAll($cri1));
			$list['counts'] = $counts;
			
			//个人信息
			
			$areaItem[] = $list->m_p;
			$areaItem[] = $list->m_c;
			$areaItem[] = $list->m_a;
			
		}
		
		$area = new Area();
		$sql = "select bid,  area_name from area where bid in (".implode(",", $areaItem).")";
		$areaResult = $area->findAllBySql($sql);
		foreach ($areaResult as $key => $value) {
			$areaInfo[$value['bid']] = $value['area_name'];
		}
		
// 		if( $_GET['recive1'] && $_GET['recive2']){
// 			foreach ($items as $val){
// 				if($val['counts'] >= $_GET['recive1'] && $val['counts'] <=$_GET['recive2']){
// 					$items1[] = $val;
// 				}
// 			}
// 		}
 		}
// 		if($items1){
// 			$items = $items1;
// 			//$pages->itemCount = count($items);
// 		}
		$this->render('broadcastinglog',array(
					'items'=>$items,
					'pages'=> $pages,
					'province'=>$province,
					'areaInfo'=>$areaInfo,
					'counts'=>$counts,
					'result'=>$result
		));
	}
	public function actionStatistic()
	{
		$this->menuIndex =62;
		$con = array();
		if($_GET['created_time1'] || $_GET['created_time2']){
			if ($_GET['created_time1']) {
				$con[] = "created_time >= ".strtotime($_GET['created_time1']);
			}
			if ($_GET['created_time2']) {
				$con[] = "created_time <= ".strtotime($_GET['created_time2'])+86399;
			}
			$result['created_time1'] = $_GET['created_time1'];
			$result['created_time2'] = $_GET['created_time2'];
		}
		$sql = "select member_id, league_id, friend_id, receive_count from broadcasting_log";
		if (count($con)) {
			$sql .= ' where '.implode(' and ', $con);
		}
		$apply = new BroadcastingLog();
		$res = $apply->findAllBySql($sql);
		$result['count'] = count($res);
		$result['receive_count'] = 0;
		$result['friend_count'] = 0;
		$result['league_count'] = 0;
		$result['friend_number'] = 0;
		$result['league_number'] = 0;
		if ($res) {
			foreach($res as $e){
				$result['receive_count'] += $e['receive_count'];
				$currentFriend = array();
				if ($e['friend_id']) {
					$currentFriend = explode(",", $e['friend_id']);
					$result['friend_count'] += count($currentFriend);
					$result['friend_number'] ++;
				}
				if ($e['league_id']) {
					$result['league_count'] += ($e['receive_count']-count($currentFriend));
					$result['league_number'] ++;
				}
			}
		}
		$this->render('statistic', array('result'=>$result));
	}

	//小喇叭详细页面
	public function actionBroadDetail($id)
	{
		
		$this->menuIndex =62;
			$model= BroadcastingLog::model()->findByPk($id);
			// Uncomment the following line if AJAX validation is needed
			// $this->performAjaxValidation($model);
			$countModel = BroadcastingLog::model();
			$cri = new CDbCriteria();
			$cri->condition = "member_id=".$model->member_id;
			//累计喊话次数
			$counts = count($countModel->findAll($cri));
			//向好友喊话次数
			$cri1 = new CDbCriteria();
			$cri1->condition = "member_id=$model->member_id and  friend_id != '' and league_id = '' ";
			$frends =$countModel->findAll($cri1);
			foreach ($frends as $val){
				$f_num += $val->receive_count;
			}
			$f_counts = count($frends);
			//向联盟喊话
			$cri2 = new CDbCriteria();
			$cri2->condition = "member_id=$model->member_id and  league_id != ''";
			$league = $countModel->findAll($cri2);
			foreach ($league as $val){
				$l_num += $val->receive_count;
			}
			$l_counts = count($league);
			//用户信息
			$member = Member::model();
			$cri = new CDbCriteria();
			$cri->condition = "id =".$model->member_id;
			$items = $member->find($cri);
			
			$areaItem[] = $items->province;
			$areaItem[] = $items->city;
			$areaItem[] = $items->area;
			$area = new Area();
			$sql = "select bid,  area_name from area where bid in (".implode(",", $areaItem).")";
			$areaResult = $area->findAllBySql($sql);
			foreach ($areaResult as $key => $value) {
				$areaInfo[$value['bid']] = $value['area_name'];
			}
			$this->render('broaddetail',array(
				'model'=>$model,
				'backUrl' => $this->getBackListPageUrl(),
				'items'=>$items,
				'member'=>$member,
				'areaInfo'=>$areaInfo,
				'counts'=>$counts,
					'f_counts'=>$f_counts,
					'l_counts'=>$l_counts,
					'f_num'=>$f_num,
					'l_num'=>$l_num
			));
	}
	public function actionDelete($id)
	{
		$id = Frame::getIntFromRequest('id');
		if ($id > 0) {
			$model = News::model ()->findByPk ( $id );
		}
		if ($model) {
			$model->delete();
		}
		$this->redirect ( Yii::app()->createUrl('notice/index',array('page'=>intval($_REQUEST['page']))));
	}
	
	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return News the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=News::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
	
	public function getBackListPageUrl()
	{
		return Yii::app()->createUrl("notice/index",array('page'=>$_REQUEST['page']));
	}

	/**
	 * Performs the AJAX validation.
	 * @param News $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='news-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
