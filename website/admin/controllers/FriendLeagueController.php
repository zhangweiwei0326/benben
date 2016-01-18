<?php
Yii::$enableIncludePath = false;
define('__ROOT__',dirname(dirname(__FILE__)));
require_once (__ROOT__.'/PHPExcel/PHPExcel.php');
class FriendLeagueController extends BaseController
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
		$model=new FriendLeague;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['FriendLeague']))
		{
			$model->attributes=$_POST['FriendLeague'];
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
		
		$member = new Member();
		$member = $member->findByPk($model->member_id);
		
		//显示创建人禁用原因
		$serviceDisable = new ServiceDisable();
		$sql = "SELECT status,reason FROM service_disable
					WHERE member_id = ".$model->member_id." and type = 6 ORDER BY created_time DESC LIMIT 1";
		$ereason2 = $serviceDisable->findAllBySql($sql);
		$reason2 = $ereason2[0]->reason;

		$province = $this->areas($model->province) ? $this->areas($model->province) : "未知";
		$city = $this->areas($model->city) ? $this->areas($model->city) : "未知";
		$area = $this->areas($model->area) ? $this->areas($model->area) : "未知";

		$areas = array();
		$areas = array("province" => $province, "city" => $city, "area" => $area, "street" => $street);
		$leagueDisable = new FriendLeagueDisable;
		//原因		
		$sql = "SELECT reason FROM friend_league_disable WHERE league_id = ".$model->id." ORDER BY created_time DESC";
		$reason = $leagueDisable->findBySql($sql);
		$reason1 = $reason->reason;
		
		if(isset($_POST['FriendLeague']))
		{
			if($model->status != $_POST['FriendLeague']['status'] || $reason1 != $_POST['FriendLeague']['reason']){
				$leagueDisable->league_id = $id;
				$leagueDisable->status = $_POST['FriendLeague']['status'];
				$leagueDisable->user_id = $this->getLoginId();
				$leagueDisable->reason = $_POST['FriendLeague']['reason'];
				$leagueDisable->created_time = time();
				$leagueDisable->save();
			}
			
			//改变创建人禁用状态
			$status2 = $_POST['FriendLeague']['status2'];
			$post_reason2 = $_POST['FriendLeague']['reason2'];
			if($status2 != $ereason2[0]->status || $reason2 != $post_reason2){
				$member->league_disable = $status2;
				if($member->update()){
					$service = new ServiceDisable();
					$service->member_id = $model->member_id;
					$service->user_id = $this->getLoginId();
					$service->status = $status2;
					$service->reason = $post_reason2;
					$service->type = 6;
					$service->created_time = time();
					$service->save();
				}
			}

			$model->status = $_POST['FriendLeague']['status'];	
			if($model->save())
			$this->redirect($this->getBackListPageUrl());
		}

// 		$result1 = $leagueDisable->findAll(array('select'=>array('id', 'created_time', 'status', 'reason'), 'order'=>'id desc', 'condition'=>'league_id='.$id));
// 		$status = array('0'=>'启用', '1'=>'屏蔽'/*, '2'=>'禁用2周', '3'=>'禁用1个月', '4'=>'禁用3个月', '5'=>'无限期禁用'*/);
// 		foreach ($result1 as $va){			
//                $info[] = array("reason"=>$va['reason'], "created_time"=>$va['created_time'],"status"=>$status[$va['status']]);							
// 		}

		$model->member_id = $member->name ? $member->name : $member->nick_name;
		$additional['benben_id'] = $member->benben_id;
		$additional['phone'] = $member->phone;
		$model->created_time = date('Y-m-d H:i:s', $model->created_time);
		$this->render('update',array(
			'model'=>$model,
			'info'=>$info,
			'reason1' => $reason1,
			'status2' => $member['league_disable'],
			'reason2' => $reason2,
			'additional'=>$additional,
			'backUrl' => $this->getBackListPageUrl(),
			'areas' => $areas,
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
		$this->insert_log(50);
		$model = FriendLeague::model();
		$cri = new CDbCriteria();

		$province = $this->getProvince();
		

		$result = array();
		$name = $_GET['name'];
		$post_province = intval($_GET['province']);
		if ($post_province > 0) {
			$res = $this->getCity($post_province);
		}
		$post_city = intval($_GET['city']);
		if ($post_city > 0) {
			$res2 = $this->getArea($post_city);
		}
		$post_area = intval($_GET['area']);
		$created_time1= $_GET['created_time1'];
		$created_time2= $_GET['created_time2'];
		$member_id = $_GET['member_id'];
		$number1 = intval($_GET['number1']);
		$number2 = intval($_GET['number2']);
		$status = intval($_GET['status']);
		$status1 = Frame::getIntFromRequest('status1');
		if(!empty($name)){
			$cri->addSearchCondition('t.name', $name, true, 'AND');
			$result['name'] = $name;
		}
		if(!empty($member_id)){
			$member = new Member();
			$sql = "select id from member where nick_name like '%".$member_id."%'";
			$id = $member->findAllBySql($sql);
			if(!$id){
				$sql = "select id from member where name like '%".$member_id."%'";
				$id = $member->findAllBySql($sql);
			}
			//if($id){
				$str = array();
				foreach ($id as $va){
					$str[] = $va->id;
				};
				$cri->addInCondition('member_id', $str);		
			//}
			$result['member_name'] = $member_id;
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
		if ($number1 > 0) {
			$cri->addCondition('t.number >= '.$number1,'AND');
			$result['number1'] = $_GET['number1'];
		}
		if ($number2 > 0) {
			$cri->addCondition('t.number <= '.$number2,'AND');
			$result['number2'] = $_GET['number2'];
		}
		if (isset($_GET['status']) && $status >= 0) {
			if($status == 2){
				$cri->addCondition('t.is_delete = 1');
			}else{
				$cri->addCondition('t.status = '.$status,'AND');
				$cri->addCondition('t.is_delete = 0','AND');
			}						
			$result['status'] = $_GET['status'];
		}
		if($status1 && $status1 != -1 ){
			if($status1 == 6){
				$status1 = 0;
			}
			$cri->addCondition("member.league_disable = {$status1} ");
		}
		$benben_id = Frame::getIntFromRequest('benben_id');
		if($benben_id){
			
			$cri->addCondition("member.benben_id =".$_GET['benben_id']);
			$result['benben_id'] = $_GET['benben_id'];
		}
		
		$cri->select = "t.*, member.name as mname, member.benben_id as mbenben_id,member.nick_name as nickname,member.league_disable";
		$cri->join = "left join member on member.id = t.member_id";
		$cri->order = "t.id desc";
		$pages = new CPagination();
		$pages->itemCount = $model->count($cri);
		$pages->pageSize = 50;
		$pages->applyLimit($cri);
		$items = $model->findAll($cri);
		$areaInfo = array();
		$chief_num = array();
		if ($items) {
			$areaItem = array();
			$league_id = array();
			foreach ($items as $key => $value) {
				$league_id[] = $value['id'];
				$areaItem[] = $value ['province'];
				$areaItem[] = $value ['city'];
				$areaItem[] = $value ['area'];
			}
			$area = new Area();
			$sql = "select bid,  area_name from area where bid in (".implode(",", $areaItem).")";
			$areaResult = $area->findAllBySql($sql);
			foreach ($areaResult as $key => $value) {
				$areaInfo[$value['bid']] = $value['area_name'];
			}
			
			$connection = Yii::app()->db;
			$sql = "SELECT league_id,count(id) chief_num FROM league_member where type = 1 and league_id in (".implode(",", $league_id).") group by league_id;";
			$command = $connection->createCommand($sql);
			$renu = $command->queryAll();
			foreach ($renu as $va){
				$chief_num[$va['league_id']] = $va['chief_num'];
			}
		}
				
		$url = Yii::app()->request->getUrl();
		$cookie = new CHttpCookie('benben-neverland',$url);
		$cookie->expire = time()+3600;  
		Yii::app()->request->cookies['benben-neverland']=$cookie;

		$this->render('index',array('items'=>$items,'pages'=> $pages,'result' => $result, 'province' => $province,
							'res' => $res, 'res2' => $res2,'areaInfo'=>$areaInfo,'chief_num'=>$chief_num));

	}

	//导出Excel
	public function actionPhpexcel(){
		$model = FriendLeague::model();
		$cri = new CDbCriteria();
	
		$result = array();
		$name = $_GET['name'];
		$post_province = intval($_GET['province']);
		$post_city = intval($_GET['city']);
		$post_area = intval($_GET['area']);
		$created_time1= $_GET['created_time1'];
		$created_time2= $_GET['created_time2'];
		$member_id = $_GET['member_id'];
		$number1 = intval($_GET['number1']);
		$number2 = intval($_GET['number2']);
		$status = intval($_GET['status']);
		$status1 = Frame::getIntFromRequest("status1");
		
		if(!empty($name)){
			$cri->addSearchCondition('t.name', $name, true, 'AND');
			$result['name'] = $name;
		}
		
		if(!empty($member_id)){
			$member = new Member();
			$sql = "select id from member where name like '%".$member_id."%'";
			$id = $member->findAllBySql($sql);
			if($id){
				foreach ($id as $va){
					$str .= $va->id.",";
				};
				$str = explode(",", substr($str, 0, -1));
				$cri->addInCondition('member_id', $str);
				$result['member_name'] = $member_id;
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
		if ($number1 > 0) {
			$cri->addCondition('t.number >= '.$number1,'AND');
			$result['number1'] = $_GET['number1'];
		}
		if ($number2 > 0) {
			$cri->addCondition('t.number <= '.$number2,'AND');
			$result['number2'] = $_GET['number2'];
		}
		if (isset($_GET['status']) && $status >= 0) {
			if($status == 2){
				$cri->addCondition('t.is_delete = 1');
			}else{
				$cri->addCondition('t.status = '.$status,'AND');
				$cri->addCondition('t.is_delete = 0','AND');
			}			
			$result['status'] = $_GET['status'];
		}
		
		if($status1 && $status1 != -1 ){
			if($status1 == 6){
				$status1 = 0;
			}
			$cri->addCondition("member.league_disable = {$status1} ");
		}
		
		$benben_id = Frame::getIntFromRequest('benben_id');
		if($benben_id){
				
			$cri->addCondition("member.benben_id =".$_GET['benben_id']);
			$result['benben_id'] = $_GET['benben_id'];
		}
		
		$cri->select = "t.*, member.name as mname, member.benben_id as mbenben_id,member.nick_name as nickname";
		$cri->join = "left join member on member.id = t.member_id";
		$cri->order = "t.id desc";
		$users = $model->findAll($cri);
		$objPHPExcel = new PHPExcel();
		/*--------------设置表头信息------------------*/
		//第一个sheet
		$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A1', '编号')
		->setCellValue('B1', '联盟名称')
		->setCellValue('C1', '盟主名称')
		->setCellValue('D1', '奔犇号')
		->setCellValue('E1', '地区')
		->setCellValue('F1', '成员数量')
		->setCellValue('G1', '堂主数量')
		->setCellValue('H1', '创建时间');
		
		if(!empty($users)){
			//省市代码获取
			$pro = array();
			$pro_arr = array();
			$league_id = array();
			$chief_num = array();
			foreach ($users as $value){
				$league_id[] = $value['id'];
				$pro[] = $value['province'];
				$pro[] = $value['city'];
				$pro[] = $value['area'];
				$pro[] = $value['street'];
			}
			
			$connection = Yii::app()->db;
			$sql = "SELECT league_id,count(id) chief_num FROM league_member where type = 1 and league_id in (".implode(",", $league_id).") group by league_id;";
			$command = $connection->createCommand($sql);
			$renu = $command->queryAll();
			foreach ($renu as $va){
				$chief_num[$va['league_id']] = $va['chief_num'];
			}
				
			$pro_name = $this->allareas(implode(",", $pro));
			if($pro_name){
				foreach ($pro_name as $val){
					$pro_arr[$val['bid']] = $val['area_name'];
				}
			}
					
			$i =2;
			foreach ($users as  $one){
				$pron = $pro_arr[$one->province].''.$pro_arr[$one->city];
				$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue("A$i", $one->id)
				->setCellValue("B$i", $one->name)
				->setCellValue("C$i", $one->mname ? $one->mname : $one->nickname)
				->setCellValue("D$i", $one->mbenben_id)
				->setCellValue("E$i", $pron)
				->setCellValue("F$i", $one->number)
				->setCellValue("G$i", $chief_num[$one->id]?$chief_num[$one->id]:0)
				->setCellValue("H$i", date("Y-m-d H:i:s", $one->created_time));
				//$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(100);
				$i++;
			}
		}
		//$objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
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
		$objPHPExcel->getActiveSheet()->setTitle('好友联盟信息');      //设置sheet的名称
		$objPHPExcel->setActiveSheetIndex(0);   //设置sheet的起始位置
		
		ob_end_clean();
		ob_start();
		header('Content-Type: application/vnd.ms-excel;charset=utf-8');
		header('Content-Disposition:attachment;filename=' . urlencode('friendLeague' . date("YmjHis") .'.xls') . '');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');   //通过PHPExcel_IOFactory的写函数将上面数据写出来
		
		$objWriter->save('php://output');
	}

	public function actionDelete($id)
	{
		$id = Frame::getIntFromRequest('id');
		if ($id > 0) {
			$model = FriendLeague::model ()->findByPk ( $id );
		}
		if ($model) {
			$model->delete();
		}
		$this->redirect ( Yii::app()->createUrl('friendLeague/index',array('page'=>intval($_REQUEST['page']))));
	}


	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return FriendLeague the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=FriendLeague::model()->findByPk($id);
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
			return Yii::app()->createUrl("friendLeague/index",array('page'=>$_REQUEST['page']));
		}
	}

	/**
	 * Performs the AJAX validation.
	 * @param FriendLeague $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='friend-league-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

	public function actionGetArea(){
		$parent_bid = intval($_GET['bid']);
		$parent_bid = intval($parent_bid);
		$area = new Area();
		$sqlc = "SELECT bid , parent_bid , area_name FROM area WHERE parent_bid = ".$parent_bid;
		$city = $area->findAllBySql($sqlc);
		$res = array();
		echo '<option value="-1">--请选择--</option>';
		foreach ($city as $c){
			$temp  = array("bid" => $c->bid, "parent_bid" => $c->parent_bid, "area_name" => $c->area_name);

			echo '<option value="'.$c->bid.'">'.$c->area_name.'</option>';
		}
	}
}
