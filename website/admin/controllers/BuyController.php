<?php
Yii::$enableIncludePath = false;
define('__ROOT__',dirname(dirname(__FILE__)));
require_once (__ROOT__.'/PHPExcel/PHPExcel.php');
class BuyController extends BaseController
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/admin';

	/**
	 * @var int the define the index for the menu
	 */

	public $menuIndex = 41;
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Buy;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Buy']))
		{
			$model->attributes=$_POST['Buy'];
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
		$member_id = Frame::getIntFromRequest('member_id');
		
		$memberInfo = Member::model()->findByPk($member_id);
		$areaItem[] = $memberInfo->province;
		$areaItem[] = $memberInfo->city;
		$areaItem[] = $memberInfo->area;
		if($areaItem){
		$area = new Area();
		$sql = "select bid,  area_name from area where bid in (".implode(",", $areaItem).")";
		$areaResult = $area->findAllBySql($sql);
		foreach ($areaResult as $key=>$list){
			$areaInfo[$list['bid']] = $list['area_name'];
		}
		}
		//发布人
		$member = new Member();
		$member = $member->findByPk($model->member_id);
// 		$memberDisable = new MemberDisable;
// 		$sql = "SELECT reason FROM member_disable WHERE member_id = ".$member->id." ORDER BY created_time DESC LIMIT 1";
// 		$memberreason = $memberDisable->findAllBySql($sql);
// 		$memberreason = $memberreason[0]->reason;
		
		//显示创建人禁用原因
		$serviceDisable = new ServiceDisable();
		$sql = "SELECT status,reason FROM service_disable
					WHERE member_id = ".$model->member_id." and type = 2 ORDER BY created_time DESC LIMIT 1";
		$ereason2 = $serviceDisable->findAllBySql($sql);
		$memberreason = $ereason2[0]->reason;		

		//原因
		$buyDisable = new BuyDisable();
		$sql = "SELECT reason FROM buy_disable WHERE item_id = ".$model->id." ORDER BY created_time DESC";
		$reason = $buyDisable->findBySql($sql);
		$reason = $reason->reason;

		if (isset($_POST['Member'])) {
			//改变创建人禁用状态
			$status2 = $_POST['Member']['status'];
			$post_reason2 = $_POST['Member']['reason'];
			if($member->buy_disable != $status2 || $memberreason != $post_reason2){
				$member->buy_disable = $status2;
				if($member->update()){
					$service = new ServiceDisable();
					$service->member_id = $model->member_id;
					$service->user_id = $this->getLoginId();
					$service->status = $status2;
					$service->reason = $post_reason2;
					$service->type = 2;
					$service->created_time = time();
					$service->save();
				}
			}
			
// 			if($member->status != $_POST['Member']['status'] && $memberreason != $_POST['Member']['reason']){
// 				$memberDisable->member_id = $model->member_id;
// 				$memberDisable->status = $_POST['Member']['status'];
// 				$memberDisable->user_id = $this->getLoginId();
// 				$memberDisable->reason = $_POST['Member']['reason'];
// 				$memberDisable->created_time = time();
// 				$memberDisable->save();

// 				$member->status = 	$_POST['Member']['status'];	
											
// 				$member->save();
// 			}
		}

		if(isset($_POST['Buy']))
		{
			$status = $_POST['Buy']['status'];
			$post_reason = $_POST['Buy']['reason'];
				
			if($status != $model->status || $post_reason != $reason){
				$buyDisable->created_time = time();
				$buyDisable->item_id = $model->id;
				$buyDisable->user_id = $this->getLoginId();
				$buyDisable->reason = $post_reason;
				$buyDisable->status = $status;
				$buyDisable->save();
			}
				
			$model->status= $status;
			if($model->save())
			$this->redirect($this->getBackListPageUrl());
		}
		$model->deadline = date('Y-m-d H:i:s', $model->deadline);
		$model->created_time = date('Y-m-d H:i:s', $model->created_time);
		$model->member_id = $member->name;
		$model->member_phone = $member->phone;
		$this->render('update',array(
			'model'=>$model,
			'reason' => $reason,
			'memberreason' => $memberreason,
			'member'=>$member,
			'backUrl' => $this->getBackListPageUrl(),
			'areaInfo'=>$areaInfo,
			'memberInfo'=>$memberInfo
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
		$this->insert_log(41);
		$province = $this->getProvince();
	
		
		$model = Buy::model();
		$cri = new CDbCriteria();
		$name = Frame::getStringFromRequest("name");
		$benben_id = Frame::getStringFromRequest("benben_id");
		$status = Frame::getIntFromRequest("status");
		$status1 = Frame::getIntFromRequest("status1");
		$created_time1= $_GET['created_time1'];
		$created_time2= $_GET['created_time2'];
		$deadline1= $_GET['deadline1'];
		$deadline2= $_GET['deadline2'];
		$is_accept = -1;
		if (isset($_GET['is_accept'])) {
			$is_accept = intval($_GET['is_accept']);
		}

		if($name){
			$member = new Member();
			$sql = "select id From member where name like '%".$name."%' or nick_name like '%".$name."%'";
			$member = $member->findAllBySql($sql);
				
			$str = array();
			if (count($member)>0) {
				foreach ($member as $value){
					$str[] = $value->id;
				}
				$cri->addCondition("member_id in(".implode(",", $str).")");
			}else{
				$cri->addCondition("member_id < 0");
			}
			
		}
		if($benben_id){
			$member = new Member();
			$sql = "select id From member where benben_id = {$benben_id}";
			$member = $member->findAllBySql($sql);
			if ($member) {
				$cri->addCondition("member_id = {$member[0]->id} ");
			}else{
				$cri->addCondition("member_id < 0");
			}
			
		}
		
		if(isset($_GET['status1']) && $status1 != -1){			
			$cri->addCondition("a.buy_disable = {$status1} ");			
		}

		if($status && $status != -1){
			if($status == 2){
				$cri->addInCondition('t.status', array(0));
			}else{
				$cri->addCondition("t.status = 1");
			}
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
		if($deadline1 && $deadline2){
			$ct1 = strtotime($deadline1);
			$ct2 = strtotime($deadline2)+86399;

			if($ct1 >= $ct2){
				$result['msg'] = "截止日期第一个必须比第二个小!";
			}else{
				$cri->addCondition('t.deadline >= '.$ct1,'AND');
				$result['created_time1'] = $created_time1;
				$cri->addCondition('t.deadline <= '.$ct2,'AND');

			}
		}else{
			if($deadline1){
				$cri->addCondition('t.deadline >= '.strtotime($deadline1),'AND');
			}
			if($deadline2){
				$cri->addCondition('t.deadline <= '.strtotime($deadline2)+86399,'AND');
			}
		}
		if ($is_accept > -1) {
			$cri->addCondition('t.is_accept = '.$is_accept,'AND');
		}
		$result['is_accept'] = $is_accept;

		if($_GET['province'] && ($_GET['province'] != -1)){
			$cri->addCondition('t.province = '.$_GET['province'],'AND');
			$result['province'] = $_GET['province'];
			$result['goback'] = -2;
			$res = $this->getCity($_GET['province']);
		}
			
		if($_GET['city'] && ($_GET['city'] != -1)){
			$cri->addCondition('t.city = '.$_GET['city'],'AND');
			$result['goback'] = -2;
			$res2 = $this->getArea($post_city);
			$result['city'] = $_GET['city'];
		}
			
		if($_GET['area'] && ($_GET['area'] != -1)){
			$cri->addCondition('t.area = '.$_GET['area'],'AND');
			$result['goback'] = -2;
			$result['area'] = $_GET['area'];
		}
		
		if($_GET['mprovince'] && ($_GET['mprovince'] != -1)){
			$cri->addCondition('a.province = '.$_GET['mprovince'],'AND');
			$result['mprovince'] = $_GET['mprovince'];
			$result['goback'] = -2;
			$mres = $this->getCity($_GET['mprovince']);
		}
			
		if($_GET['mcity'] && ($_GET['mcity'] != -1)){
			$cri->addCondition('a.city = '.$_GET['mcity'],'AND');
			$result['goback'] = -2;
			$mres2 = $this->getArea($_GET['mcity']);
			$result['mcity'] = $_GET['mcity'];
		}
			
		if($_GET['marea'] && ($_GET['marea'] != -1)){
			$cri->addCondition('a.area = '.$_GET['marea'],'AND');
			$result['goback'] = -2;
			$result['marea'] = $_GET['marea'];
		}
		
		

		$cri->select = "t.*,a.id as member_id, a.name as mname,a.buy_disable status1,a.phone member_phone,a.nick_name, a.benben_id,t.province province,t.city city,t.area area";
		$cri->join = "left join member a on a.id = t.member_id";
		$cri->order = "t.created_time desc";
		$pages = new CPagination();
		$pages->itemCount = $model->count($cri);
		$pages->pageSize = 50;
		$pages->applyLimit($cri);
		$items = $model->findAll($cri);
		if($items){
			foreach ($items as $list){
				$areaItem[] = $list->province;
				$areaItem[] = $list->city;
				$areaItem[] = $list->area;
 			}
 			$area = new Area();
 			$sql = "select bid,area_name from area where bid in (".implode(",", $areaItem).")";
 			$areaResult = $area->findAllBySql($sql);
 			foreach ($areaResult as $key=>$value){
 				$areaInfo[$value['bid']]=$value['area_name'];
 			}
		}
		$url = Yii::app()->request->getUrl();
		$cookie = new CHttpCookie('benben-neverland',$url);
		$cookie->expire = time()+3600;  
		Yii::app()->request->cookies['benben-neverland']=$cookie;
		$this->render('index',array('items'=>$items,'pages'=> $pages,
		'province' => $province, 'res' => $res, 'res2' => $res2, 'mres' => $mres, 'mres2' => $mres2,'result'=>$result,'areaInfo'=>$areaInfo));

	}

	public function actionPushbuy(){
		$this->menuIndex =41;
		$provinceall = $this->getProvince();
		$industryall = $this->getIndustry();
		$buyid = intval($_GET['id']);
		if ((Yii::app()->request->isPostRequest)) {
			$province = intval($_POST['province']);
			$city = intval($_POST['city']);
			$area = intval($_POST['area']);					
			$industry = intval($_POST['industry']);
			$getnum = intval($_POST['getnum']);
			$buyid = intval($_POST['buyid']);
			if ($province > 0) {
				$res = $this->getCity($province);
			}
			if ($city > 0) {
				$res2 = $this->getArea($city);
			}
			if ($area > 0) {
				$res3 = $this->getStreet($area);
			}
			
			$connection = Yii::app()->db;
			$all = array();			
			//根据条件查出会员			
			$model = NumberTrain::model();
			$cri = new CDbCriteria();
			$cri->select = "t.member_id,t.phone";
			if($industry && ($industry != -1)){
				$cri->addCondition('t.industry = '.$industry,'AND');
				$result['industry'] = $industry;
			}
			
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
			$cri->addCondition('t.is_close = 0','AND');
			$cri->addCondition('t.status = 0','AND');
			$items = $model->findAll($cri);
			foreach ($items as $va){
				$all[] = $va->member_id;				
			}
											
			if($getnum){
				echo count($all);
				exit;
			}	
			//var_dump($all);exit;				
			if(!count($all)){
				$this->render("addpush",array('msg' => "没有符合条件的用户",'province' => $provinceall,'industry'=>$industryall,
						'res' => $res, 'res2' => $res2,'result' => $result,
						'res3' => $res3));
					
				exit();
			}
			//发消息
			$buyinfo = Buy::model()->findByPk($buyid);
			$buyinfotitle = '有用户购买'.$buyinfo->title;
			$t = time();
			$userid = $this->getLoginId();
			if($all[0]){
				foreach ($all as $val){
					$sqla[] = "(8,{$userid},{$val},'$buyinfotitle','$buyid',{$t})";
				}
				$sql = "insert into news (type,sender,member_id,content,identity1,created_time) values ".implode(",", $sqla);
				$command = $connection->createCommand($sql);
				$re = $command->execute();
			}
			//保存到记录表
			$pushlog = new PushLog();
			$pushlog->sender = $userid;			
			$pushlog->number = count($all);
			$pushlog->industry = $industry;
			$pushlog->buyid = $buyid;
			$pushlog->province = ($province == -1) ? 0 : $province;
			$pushlog->city = ($city == -1) ? 0 : $city;
			$pushlog->area = ($area == -1) ? 0 : $area;
			$pushlog->street = ($street == -1) ? 0 : $street;
			$pushlog->member_id = implode(",", $all);
			$pushlog->created_time = time();
			//$sql = "insert into news_log (sender,content,number,unit,province,city,area,street,member_id,created_time) values ".implode(",", $sqla);
			$pushlog->save();
			if($re){
				$this->redirect($this->getBackListPageUrl());
			}else{
				$this->render("addpush",array('msg' => "发送失败",'province' => $provinceall,'industry'=>$industryall,
						'res' => $res, 'res2' => $res2,'result' => $result,
						'res3' => $res3));
					
				exit();
			}	
			
				
		}
	
		$this->render("addpush",array('province' => $provinceall,'industry'=>$industryall,
				'res' => $res, 'res2' => $res2,
				'res3' => $res3));
	}
	
	public function actionPushlog(){		
		$this->menuIndex =41;
		$result = array();
		$industry = intval($_GET['industry']);
		$province = $this->getProvince();
		$industryall = $this->getIndustry();
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
		
		$model = PushLog::model();
		$cri = new CDbCriteria();
						
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
		
		if($industry && ($industry != -1)){
			$cri->addCondition('t.industry = '.$industry,'AND');
			$result['industry'] = $industry;
		}
		
		$cri->select = "t.*, a.title,a.province bprovince,a.city bcity,a.area barea,b.benben_id,b.name,b.nick_name";
		$cri->join = "left join buy a on t.buyid = a.id 
					  left join member b on b.id = a.member_id";
		// 		$cri->addCondition('t.type = 1');
		$cri->order = "t.id desc";
		$pages = new CPagination();
		$pages->itemCount = $model->count($cri);
		$pages->pageSize = 50;
		$pages->applyLimit($cri);
		$items = $model->findAll($cri);
		$industry = array();
		$pushnum = array();
		if($industryall){
			foreach ($industryall as $va){
				$industry[$va->id] = $va->name;
			}
		}
		if ($items) {			
			$areaItem = array();
			foreach ($items as $key => $value) {
				$pushnum[$value->buyid][] = $value->id;
				if($value ['province']) $areaItem[] = $value ['province'];
				if($value ['city']) $areaItem[] = $value ['city'];
				if($value ['area']) $areaItem[] = $value ['area'];
				if($value ['street']) $areaItem[] = $value ['street'];
				if($value ['bprovince']) $areaItem[] = $value ['bprovince'];
				if($value ['bcity']) $areaItem[] = $value ['bcity'];
				if($value ['barea']) $areaItem[] = $value ['barea'];				
			}
			$area = new Area();
			$sql = "select bid,  area_name from area where bid in (".implode(",", $areaItem).")";
			$areaResult = $area->findAllBySql($sql);
			foreach ($areaResult as $key => $value) {
				$areaInfo[$value['bid']] = $value['area_name'];
			}
		}		
		$this->render("pushlog",array('items'=>$items,'province' => $province,'pages'=> $pages,
				'res' => $res, 'res2' => $res2,'areaInfo'=>$areaInfo,'industry'=>$industry,
				'res3' => $res3,'pushnum'=>$pushnum,'result'=>$result,'industryall'=>$industryall));
	}
	
	public function actionPushlogdetail(){
		$id = Frame::getIntFromRequest('id');
		$this->menuIndex =41;
		$result = array();
		$connection = Yii::app()->db;
		$sql = "select buyid,member_id from push_log where id = {$id}";	
		$command = $connection->createCommand($sql);
		$result1 = $command->queryAll();
		if($result1[0]){
			$storeid = explode(",", $result1[0]['member_id']);
			$model = NumberTrain::model();
			$cri = new CDbCriteria();
			$cri->select = "t.*, u.name mname,u.nick_name,u.store_disable ";
			$cri->join = "left join member u on t.member_id = u.id ";			
			$cri->order = "t.id desc";
			$cri->addInCondition("t.member_id", $storeid);
			$pages = new CPagination();
			$pages->itemCount = $model->count($cri);
			$pages->pageSize = 50;
			$pages->applyLimit($cri);
			$items = $model->findAll($cri);
			$industryall = $this->getIndustry();
			if($industryall){
				foreach ($industryall as $va){
					$industry[$va->id] = $va->name;
				}
			}
			if ($items) {
				$areaItem = array();
				foreach ($items as $key => $value) {					
					if($value ['province']) $areaItem[] = $value ['province'];
					if($value ['city']) $areaItem[] = $value ['city'];
					if($value ['area']) $areaItem[] = $value ['area'];
					if($value ['street']) $areaItem[] = $value ['street'];					
				}
				$area = new Area();
				$sql = "select bid,  area_name from area where bid in (".implode(",", $areaItem).")";
				$areaResult = $area->findAllBySql($sql);
				foreach ($areaResult as $key => $value) {
					$areaInfo[$value['bid']] = $value['area_name'];
				}
			}
			$this->render('pushlogdetail',array('items'=>$items,'pages'=> $pages,'areaInfo'=>$areaInfo,'industry'=>$industry));
		}
		
		
	}
	
	public function actionLog()
	{
		$id = Frame::getIntFromRequest('id');
		$connection = Yii::app()->db;
		$sql = "select a.*, b.username as name from service_disable as a left join user as b on a.user_id = b.id where a.member_id = {$id} and a.type = 2 order by a.created_time desc";
	
		$command = $connection->createCommand($sql);
		$result1 = $command->queryAll();//var_dump($result1);exit();
	
		$this->render('log',array('items'=>$result1));
	}

	//导出excel
	public function actionPhpexcel(){
		$model = Buy::model();
		$cri = new CDbCriteria();
		$name = Frame::getStringFromRequest("name");
		$benben_id = Frame::getStringFromRequest("benben_id");
		$status = Frame::getIntFromRequest("status");
		$status1 = Frame::getIntFromRequest("status1");
		$created_time1= $_GET['created_time1'];
		$created_time2= $_GET['created_time2'];
		$deadline1= $_GET['deadline1'];
		$deadline2= $_GET['deadline2'];
		$is_accept = -1;
		if (isset($_GET['is_accept'])) {
			$is_accept = intval($_GET['is_accept']);
		}
		
		if($name){
			$member = new Member();
			$sql = "select id From member where name like '%".$name."%'";
			$member = $member->findAllBySql($sql);
		
			$str = "";
			foreach ($member as $value){
				$str .= $value->id.",";
			}
			$str = substr($str, 0, -1);
			$cri->addCondition("member_id in(".$str.")");
		}
		if($benben_id){
			$member = new Member();
			$sql = "select id From member where benben_id = {$benben_id}";
			$member = $member->findAllBySql($sql);
			$cri->addCondition("member_id = {$member[0]->id} ");
		}
		
		if(isset($_GET['status1']) && $status1 != -1){
			$cri->addCondition("a.buy_disable = {$status1} ");
		}
		
		if($status && $status != -1){
			if($status == 2){
				$cri->addInCondition('t.status', array(0));
			}else{
				$cri->addCondition("t.status = 1");
			}
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
		if($deadline1 && $deadline2){
			$ct1 = strtotime($deadline1);
			$ct2 = strtotime($deadline2)+86399;
		
			if($ct1 >= $ct2){
				$result['msg'] = "截止日期第一个必须比第二个小!";
			}else{
				$cri->addCondition('t.deadline >= '.$ct1,'AND');
				$result['created_time1'] = $created_time1;
				$cri->addCondition('t.deadline <= '.$ct2,'AND');
		
			}
		}else{
			if($deadline1){
				$cri->addCondition('t.deadline >= '.strtotime($deadline1),'AND');
			}
			if($deadline2){
				$cri->addCondition('t.deadline <= '.strtotime($deadline2)+86399,'AND');
			}
		}
		
		if ($is_accept > -1) {
			$cri->addCondition('t.is_accept = '.$is_accept,'AND');
		}
		$result['is_accept'] = $is_accept;
		
		if($_GET['province'] && ($_GET['province'] != -1)){
			$cri->addCondition('t.province = '.$_GET['province'],'AND');
			$result['province'] = $_GET['province'];
			$result['goback'] = -2;
		}
			
		if($_GET['city'] && ($_GET['city'] != -1)){
			$cri->addCondition('t.city = '.$_GET['city'],'AND');
			$result['goback'] = -2;
		}
			
		if($_GET['area'] && ($_GET['area'] != -1)){
			$cri->addCondition('t.area = '.$_GET['area'],'AND');
			$result['goback'] = -2;
		}
		
		if($_GET['street'] && ($_GET['street'] != -1)){
			$cri->addCondition('t.street = '.$_GET['street'],'AND');
			$result['goback'] = -2;
		}
		
		if($_GET['mprovince'] && ($_GET['mprovince'] != -1)){
			$cri->addCondition('a.province = '.$_GET['mprovince'],'AND');
			$result['mprovince'] = $_GET['mprovince'];
			$result['goback'] = -2;
			$mres = $this->getCity($_GET['mprovince']);
		}
			
		if($_GET['mcity'] && ($_GET['mcity'] != -1)){
			$cri->addCondition('a.city = '.$_GET['mcity'],'AND');
			$result['goback'] = -2;
			$mres2 = $this->getArea($_GET['mcity']);
			$result['mcity'] = $_GET['mcity'];
		}
			
		if($_GET['marea'] && ($_GET['marea'] != -1)){
			$cri->addCondition('a.area = '.$_GET['marea'],'AND');
			$result['goback'] = -2;
			$result['marea'] = $_GET['marea'];
		}
		
		$cri->select = "t.*, a.name as mname,a.buy_disable status1,a.phone member_phone,a.nick_name,a.benben_id";
		$cri->join = "left join member a on a.id = t.member_id";
		$cri->order = "t.created_time desc";
		$users = $model->findAll($cri);
		$objPHPExcel = new PHPExcel();
		/*--------------设置表头信息------------------*/
		//第一个sheet
		$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A1', '标题')
		->setCellValue('B1', '数量')
		->setCellValue('C1', '发贴人')
		->setCellValue('D1', '奔犇号')
		->setCellValue('E1', '发帖人状态')
		->setCellValue('F1', '状态')
		->setCellValue('G1', '报价人数')
		->setCellValue('H1', '截止时间')
		->setCellValue('I1', '发布时间')
		->setCellValue('J1', '地区');
		
		if(!empty($users)){
			//省市代码获取
			$pro = array();
			$pro_arr = array();
			foreach ($users as $value){
				$pro[] = $value['province'];
				$pro[] = $value['city'];
				$pro[] = $value['area'];
				$pro[] = $value['street'];
			}
				
			$pro_name = $this->allareas(implode(",", $pro));
			if($pro_name){
				foreach ($pro_name as $val){
					$pro_arr[$val['bid']] = $val['area_name'];
				}
			}
			$status1 = array('0'=>'启用', '1'=>'禁用1周', '2'=>'禁用2周', '3'=>'禁用1个月', '4'=>'禁用3个月', '5'=>'无限期');
			$status = array("0" => "正常", "1" => "屏蔽");
		
			$i =2;
			foreach ($users as  $one){
				$pron = $pro_arr[$one->province].''.$pro_arr[$one->city];
				$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue("A$i", $one->title)
				->setCellValue("B$i", $one->amount)
				->setCellValue("C$i", $one->mname ? $one->mname : $one->nick_name)
				->setCellValue("D$i", $one->benben_id)
				->setCellValue("E$i", $status1[$one->status1])
				->setCellValue("F$i", $status[$one->status])
				->setCellValue("G$i", $one->quoted_number)
				->setCellValue("H$i", date('Y-m-d H:i:s', $one->deadline))
				->setCellValue("I$i", date('Y-m-d H:i:s', $one->created_time))
				->setCellValue("J$i", $pron);
				//$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(100);
				$i++;
			}
		}
		//$objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
		
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
		$objPHPExcel->getActiveSheet()->getStyle('I1')->applyFromArray($styleArray1);
		$objPHPExcel->getActiveSheet()->getStyle('J1')->applyFromArray($styleArray1);
		
		$objPHPExcel->getActiveSheet()->setTitle('我要买信息');      //设置sheet的名称
		$objPHPExcel->setActiveSheetIndex(0);   //设置sheet的起始位置
		
		ob_end_clean();
		ob_start();
		header('Content-Type: application/vnd.ms-excel;charset=utf-8');
		header('Content-Disposition:attachment;filename=' . urlencode('buy' . date("YmjHis") .'.xls') . '');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');   //通过PHPExcel_IOFactory的写函数将上面数据写出来
		
		$objWriter->save('php://output');
	}

	public function actionDelete($id)
	{
		$id = Frame::getIntFromRequest('id');
		if ($id > 0) {
			$model = Buy::model ()->findByPk ( $id );
		}
		if ($model) {
			$model->delete();
		}
		$this->redirect ( Yii::app()->createUrl('buy/index',array('page'=>intval($_REQUEST['page']))));
	}


	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Buy the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Buy::model()->findByPk($id);
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
			return Yii::app()->createUrl("buy/index",array('page'=>$_REQUEST['page']));
		}
		
	}

	/**
	 * Performs the AJAX validation.
	 * @param Buy $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='buy-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
