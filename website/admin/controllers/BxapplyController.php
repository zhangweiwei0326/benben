<?php
Yii::$enableIncludePath = false;
define('__ROOT__',dirname(dirname(__FILE__)));
require_once (__ROOT__.'/PHPExcel/PHPExcel.php');
require_once(__ROOT__.'/PHPExcel/PHPExcel/Cell.php');
class BxapplyController extends BaseController
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/admin';

	/**
	 * @var int the define the index for the menu
	 */

	public $menuIndex = 20;
	public $dongyang = 136;
	
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Bxapply;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Bxapply']))
		{
			$model->attributes=$_POST['Bxapply'];
			if($model->save())
			$this->redirect($this->getBackListPageUrl());
		}

		$this->render('create',array(
			'model'=>$model,
			'backUrl' => $this->getBackListPageUrl(),
		));
	}

	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		//获取身份证
		$apply = new ApplyComplete();
		$sql = "SELECT id_card, poster1, poster2 from apply_complete where apply_id = $id  and type = 1 limit 1";
		$res = $apply->findAllBySql($sql);
		if($res){
			$id_card = $res[0]->id_card;
			$poster1 = $res[0]->poster1;
			$poster2 = $res[0]->poster2;
		}

		if($model->province){
			$province = $this->areas($model->province) ? $this->areas($model->province) : "未知";
		}else{
			$province = '未知';
		}
		
		if ($model->city) {
			$city = $this->areas($model->city) ? $this->areas($model->city) : "未知";
		}else{
			$city = '未知';
		}
		if ($model->area) {
			$area = $this->areas($model->area) ? $this->areas($model->area) : "未知";
		}else{
			$area = '未知';
		}
		
		if ($model->street) {
			$street = $this->areas($model->street) ? $this->areas($model->street) : "未知";
		}else{
			$street = '未知';
		}

		$areas = array();
		$areas = array("province" => $province, "city" => $city, "area" => $area, "street" => $street);

		

		//创建人
		$member = new Member();
		$sql = "SELECT name,nick_name,phone from member where id = ".$model->member_id;
		$mres = $member->findAllBySql($sql);
		if($mres){
			$member_name = $mres[0]->name?$mres[0]->name:$mres[0]->nick_name;
			$member_phone = $mres[0]->phone;
		}

		//获取审核原因
		$bxapply_record = new BxapplyRecord();
		$sql = "SELECT reason from bxapply_record where apply_id = ".$model->id." order by created_time desc limit 1";
		$reason = $bxapply_record->findAllBySql($sql);
		if(isset($_POST['Bxapply']))
		{
			if($_POST['Bxapply']['name']){
				$model->name = $_POST['Bxapply']['name'];
			}
			if($id_card && $_POST['Bxapply']['id_card']){
				$bxin = ApplyComplete::model()->find("apply_id = {$model->id} and type = 1");
				$bxin->id_card = $_POST['Bxapply']['id_card'];
				$bxin->update();
			}
			if($_POST['Bxapply']['province']){
				$model->province = $_POST['Bxapply']['province'];
			}
			if($_POST['Bxapply']['city']){
				$model->city = $_POST['Bxapply']['city'];
			}
			if($_POST['Bxapply']['area']){
				$model->area = $_POST['Bxapply']['area'];
			}
			if($_POST['Bxapply']['street']){
				$model->street = $_POST['Bxapply']['street'];
			}
			$member_id = $this->getLoginId();
			if($model->status != $_POST['Bxapply']['status'] || $reason[0]->reason != $_POST['Bxapply']['reason'] || $model->short_phone != $_POST['Bxapply']['short_phone']){
				if(intval($_POST['Bxapply']['status']) == 3 && strlen($_POST['Bxapply']['short_phone']) != 6){
					$msg = '百姓网号长度必须为6位';
				}else{
					$connection = Yii::app()->db;
					$bxapply_record->apply_id = $id;
					$bxapply_record->status = $_POST['Bxapply']['status'];
					$bxapply_record->user_id = $member_id;
					if ((intval($_POST['Bxapply']['status']) == 3)&&($model->short_phone != $_POST['Bxapply']['short_phone'])) {
						$have = Bxapply::model()->count("short_phone = {$_POST['Bxapply']['short_phone']}");
						if($have){
							$msg = '百姓网号不能重复';
							$model->status = 3;
							$model->short_phone = $_POST['Bxapply']['short_phone'];
							$model->created_time = date('Y-m-d H:i:s', $model->created_time);
							$this->render('update',array(
									'model'=>$model,
									'id_card' => $id_card,
									'poster1' => $poster1,
									'poster2' => $poster2,
									'reason' => $_POST['Bxapply']['reason'],
									'member_name' => $member_name,
									'member_phone'=>$member_phone,
									'areas' => $areas,
									'msg'=>$msg,
									'backUrl' => $this->getBackListPageUrl(),
							));
							exit;
						}
						$bxapply_record->short_phone = $_POST['Bxapply']['short_phone'];
						//更新member表userinfo
						$info = Member::model()->find("phone = {$model->phone}");
						$info_id = 0;
						if($info){
							/*if(($info->userinfo & 2) == 0){
								$info->userinfo = $info->userinfo + 2;
								$info->update();
							}*/
							$info_id = $info->id;
						}
						//更新enterprise_member表short_phone,136	
						$in = EnterpriseMember::model()->count("phone = '{$model->phone}' and contact_id = ".BXID);
						if($in){
							$sql = "update enterprise_member set short_phone = {$_POST['Bxapply']['short_phone']} where phone = '{$model->phone}' and contact_id = ".BXID;
							$command = $connection->createCommand($sql);
							$result2 = $command->execute();
						}else{
							$t = time();
							$sql = "insert into enterprise_member (contact_id,member_id,short_phone,remark_name,created_time,phone,name,invite_id)
									values(".BXID.",{$info_id},{$_POST['Bxapply']['short_phone']},'',{$t},{$model->phone},'{$model->name}',0)";
							$command = $connection->createCommand($sql);
							$result2 = $command->execute();
							if($result2){
								$einfo = Enterprise::model()->findByPk(BXID);
								$einfo->number = $einfo->number + 1;
								$einfo->update();
							}
						}											
						
					}
					$bxapply_record->reason = $_POST['Bxapply']['reason'];
					$bxapply_record->created_time = time();
					$bxapply_record->save();

					$model->short_phone = $_POST['Bxapply']['short_phone'];
					$model->status = $_POST['Bxapply']['status'];
					if ($model->status == 4) {
						//获取id
						$sql2 = "select id,member_id from enterprise_member where phone = '{$model->phone}' and contact_id = ".BXID;
						$command = $connection->createCommand($sql2);
						$re2 = $command->queryAll();

						//更新enterprise_member表,136
						$sql = "delete from enterprise_member where phone = '{$model->phone}' and contact_id = ".BXID;
						$command = $connection->createCommand($sql);
						$result2 = $command->execute();
						if($result2){
							//更新enterprise_display_member表
							$sql = "delete from enterprise_display_member where member_id = {$re2[0]['id']} and enterprise_id = ".BXID;
							$command = $connection->createCommand($sql);
							$result2 = $command->execute();

							//删除enterprise_membe_log记录
							$sql33 = "delete from enterprise_display_member_log where member_id = {$re2[0]['member_id']} and enterprise_id = ".BXID;
							$command = $connection->createCommand($sql33);
							$result33 = $command->execute();

							//更新group_contact_phone，根据手机号将百姓号置空
							$sql44="update group_contact_phone set is_baixing = 0 where phone = '{$model->phone}'";
							$command = $connection->createCommand($sql44);
							$result44	 = $command->execute();

							//更新group_contact_phone，根据奔犇号将百姓号置空
							$benbeninfo=Member::model()->find("id={$re2[0]['member_id']} and benben_id>0");
							if($benbeninfo) {
								$benbenid = $benbeninfo['benben_id'];
								$sql55="update group_contact_phone set is_baixing = 0 where is_benben = {$benbenid} and is_benben>0";
								$command = $connection->createCommand($sql55);
								$result55	 = $command->execute();
							}

							$sql22 = "delete from enterprise_display_member where user_id = {$re2[0]['member_id']} and enterprise_id = ".BXID;
							$command = $connection->createCommand($sql22);
							$result22 = $command->execute();

							$einfo = Enterprise::model()->findByPk(BXID);
							$einfo->number = $einfo->number - 1;
							$einfo->update();
						}
						
						$model->cancel_time = time();
						$model->short_phone = '';
					}else if($model->status == 3){
						$model->join_time = time();
					}	
					
				}
				
			}
			if($model->save())
				$this->redirect($this->getBackListPageUrl());
			
		}
		$aprovince = array();
		$aprovince['province'] = $this->getProvince();
		if($model->province){$aprovince['city'] = $this->getCity($model->province);}else{$aprovince['city'] = array();};
		if($model->city){$aprovince['area'] = $this->getArea($model->city);}else{$aprovince['area'] = array();};
		if($model->area){$aprovince['street'] = $this->getStreet($model->area);}else{$aprovince['street'] = array();};
		$model->created_time = date('Y-m-d H:i:s', $model->created_time);
		$this->render('update',array(
			'model'=>$model,
			'id_card' => $id_card,
			'poster1' => $poster1,
			'poster2' => $poster2,
			'reason' => $reason[0]->reason,
			'member_name' => $member_name,
			'member_phone'=>$member_phone,
			'areas' => $areas,
			'province' => $aprovince,
			'msg'=>$msg,
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
		$this->insert_log(20);
		$model = Bxapply::model();
		$cri = new CDbCriteria();

		$province = $this->getProvince();

		if(isset($_GET) && !empty($_GET)){
			$result = array();
			

			if($_GET['phone']){
				$cri->addSearchCondition('t.phone', $_GET['phone'], true, 'AND');
				$result['phone'] = $_GET['phone'];
				$result['goback'] = -2;
			}
			if($_GET['name']){
				$cri->addSearchCondition('t.name', $_GET['name'], true, 'AND');
				$result['name'] = $_GET['name'];
				$result['goback'] = -2;
			}
			if($_GET['short_phone']){
				$cri->addSearchCondition('short_phone', $_GET['short_phone'], true, 'AND');
				$result['short_phone'] = $_GET['short_phone'];
				$result['goback'] = -2;
			}
			

			if($_GET['created_time1'] && $_GET['created_time2']){
				$ct1 = strtotime($_GET['created_time1']);
				$ct2 = strtotime($_GET['created_time2'])+86399;

				if($ct1 >= $ct2){
					$msg = "申请日期第一个必须比第二个小!";
				}else{
					$cri->addCondition('t.created_time >= '.$ct1,'AND');
					$result['created_time1'] = $_GET['created_time1'];
					$cri->addCondition('t.created_time <= '.$ct2,'AND');
					$result['created_time2'] = $_GET['created_time2'];
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
			$cancel_time1 = $_GET['cancel_time1'];
			$cancel_time2 = $_GET['cancel_time2'];
			if ($cancel_time1) {
				$cri->addCondition('t.cancel_time >= '.strtotime($cancel_time1),'AND');
				$result['cancel_time1'] = $cancel_time1;
				$_GET['status']=4;
			}
			if ($cancel_time2) {
				$cri->addCondition('t.cancel_time <= '.strtotime($cancel_time2)+86399,'AND');
				$result['cancel_time2'] = $cancel_time2;
				$_GET['status']=4;
			}


			if(isset($_GET['status']) && $_GET['status'] != -1){
				$cri->addCondition('t.status = '.intval($_GET['status']),'AND');
				$result['status'] = $_GET['status'];
				$result['goback'] = -2;
			}else{
				$result['status'] = -1;
			}
				
			if($_GET['province'] && ($_GET['province'] != -1)){
				$cri->addCondition('t.province = '.$_GET['province'],'AND');
				$result['province'] = $_GET['province'];
				$result['goback'] = -2;
				$res = $this->getCity($_GET['province']);
			}
				
			if($_GET['city'] && ($_GET['city'] != -1)){
				$cri->addCondition('t.city = '.$_GET['city'],'AND');
				$res2 = $this->getArea($_GET['city']);
				$result['goback'] = -2;
			}
				
			if($_GET['area'] && ($_GET['area'] != -1)){
				$cri->addCondition('t.area = '.$_GET['area'],'AND');
					
				$result['goback'] = -2;
			}
				
		}
		$member_id = $_GET['member_id'];
		if ($member_id) {
			$result['member_id'] = $member_id;	
			  $re = Member::model()->findAll("nick_name like '%{$member_id}%' or name like '%{$member_id}%'");
			  $member_id_array = array();
			  if ($re) {
			  	foreach($re as $each){
			  		$member_id_array[] = $each['id'];
				}
		  	}	
		  	if (count($member_id_array) > 0) {
		  		$cri->addInCondition('t.member_id ', $member_id_array);
		  	}else{
		  		$cri->addCondition('t.member_id = -1');
		  	}
		}
		if(!isset($_GET['status'])){
			$result['status'] = -1;
		}
		$cri->select = "t.*, apply_complete.id_card as card, member.benben_id as benben_id, member.id_enable as id_enable";
		$cri->join = "left join apply_complete on t.id = apply_complete.apply_id and apply_complete.type=1 left join member on t.phone = member.phone";
		$cri->order = "t.id desc";

		$pages = new CPagination();
		$pages->itemCount = $model->count($cri);
		$pages->pageSize = 50;
		$pages->applyLimit($cri);
		$items = $model->findAll($cri);

		$url = Yii::app()->request->getUrl();
		$cookie = new CHttpCookie('benben-neverland',$url);
		$cookie->expire = time()+3600;  
		Yii::app()->request->cookies['benben-neverland']=$cookie;

		$this->render('index',array('items'=>$items,'pages'=> $pages, 'result'=>$result,'msg'=>$msg,
									'province' => $province, 'res' => $res, 'res2' => $res2));

	}
	
	//导出excel
public function actionPhpexcel(){
	$model = Bxapply::model();
	$cri = new CDbCriteria();
	
	if(isset($_GET) && !empty($_GET)){
		$result = array();
	
		if($_GET['phone']){
			$cri->addSearchCondition('t.phone', $_GET['phone'], true, 'AND');
			$result['phone'] = $_GET['phone'];
			$result['goback'] = -2;
		}
		if($_GET['name']){
			$cri->addSearchCondition('t.name', $_GET['name'], true, 'AND');
			$result['name'] = $_GET['name'];
			$result['goback'] = -2;
		}
		if($_GET['short_phone']){
			$cri->addSearchCondition('t.short_phone', $_GET['short_phone'], true, 'AND');
			$result['short_phone'] = $_GET['short_phone'];
			$result['goback'] = -2;
		}
		if(isset($_GET['status']) &&  ($_GET['status'] != '') &&intval($_GET['status']) > -1){
			$cri->addCondition('t.status = '.intval($_GET['status']),'AND');
			$result['status'] = $_GET['status'];
			$result['goback'] = -2;
		}
	
		if($_GET['created_time1'] && $_GET['created_time2']){
			$add = $add ? $add : 0;
			$ct1 = strtotime($_GET['created_time1']);
			$ct2 = strtotime($_GET['created_time2'])+$add;
	
			if($ct1 >= $ct2){
				$msg = "申请日期第一个必须比第二个小!";
			}else{
				$cri->addCondition('t.created_time >= '.$ct1,'AND');
				$result['created_time1'] = $_GET['created_time1'];
				$cri->addCondition('t.created_time <= '.$ct2,'AND');
				$result['created_time2'] = $_GET['created_time2'];
				$result['goback'] = -2;
			}
		}else{
			if($_GET['created_time1']){
				$cri->addCondition('t.created_time >= '.strtotime($_GET['created_time1']),'AND');
				$result['created_time1'] = $_GET['created_time1'];
				$result['goback'] = -2;
			}
			if($_GET['created_time2']){
				$cri->addCondition('t.created_time <= '.strtotime($_GET['created_time2'])+$add,'AND');
				$result['created_time2'] = $_GET['created_time2'];
				$result['goback'] = -2;
			}
		}
		
		$cancel_time1 = $_GET['cancel_time1'];
		$cancel_time2 = $_GET['cancel_time2'];
		if ($cancel_time1) {
			$cri->addCondition('t.cancel_time >= '.strtotime($cancel_time1),'AND');
			$result['cancel_time1'] = $cancel_time1;
			$_GET['status']=4;
		}
		if ($cancel_time2) {
			$cri->addCondition('t.cancel_time <= '.strtotime($cancel_time2)+86399,'AND');
			$result['cancel_time2'] = $cancel_time2;
			$_GET['status']=4;
		}
	
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
		
		if($_GET['isposter'] && ($_GET['isposter'] == 1)){
			$cri->addCondition('b.poster1 !=""  AND b.poster2 !="" ');
			$result['goback'] = -2;
		}
	}
	
	$member_id = $_GET['member_id'];
	if ($member_id) {
		$result['member_id'] = $member_id;
		$re = Member::model()->findAll("nick_name like '%{$member_id}%' or name like '%{$member_id}%'");
		$member_id_array = array();
		if ($re) {
			foreach($re as $each){
				$member_id_array[] = $each['id'];
			}
		}
		if (count($member_id_array) > 0) {
			$cri->addInCondition('t.member_id ', $member_id_array);
		}else{
			$cri->addCondition('t.member_id = -1');
		}
	}
	if(!isset($_GET['status'])){
		$result['status'] = -1;
	}
	$getpage = intval($_GET['page']);
	$pagesize = 10000;
	ini_set('memory_limit','1024M');
	$cri->select = "t.*, b.id_card as card,b.poster1,b.poster2,c.name mname,c.phone mphone, c.benben_id as benben_id, c.id_enable as id_enable";
	$cri->join = "left join apply_complete b on (t.id = b.apply_id and b.type=1) left join member c on c.id = t.member_id";
	$cri->order = "t.id desc";
	$cri->offset = (($getpage?$getpage:1)-1)*$pagesize;
	$cri->limit = $pagesize;
	$count = $model->count($cri);
	if(!$getpage && ($count > $pagesize)){
		//echo $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];exit;
		$page = ceil($count/$pagesize);
		$this->render("putpage",array("page"=>$page));
		exit;
	}
	$users = $model->findAll($cri); 
	$objPHPExcel = new PHPExcel();
	/*--------------设置表头信息------------------*/
	//第一个sheet
	$objPHPExcel->setActiveSheetIndex(0)
	->setCellValue('A1', '手机号码')
	->setCellValue('B1', '百姓网号')
	->setCellValue('C1', '姓名')
	->setCellValue('D1', '奔犇号')
	->setCellValue('E1', '身份证号码')
	->setCellValue('F1', '地区')
	->setCellValue('G1', '提交人')
	->setCellValue('H1', '提交人手机号码')
	->setCellValue('I1', '照片(正面)')
	->setCellValue('J1', '照片(反面)')
	->setCellValue('K1', '申请时间')
	->setCellValue('L1', '审核状态(0为默认等待审核，1未通过，2退回重申，3已经通过,4为撤销)')
	->setCellValue('M1', '反馈信息');
	$objPHPExcel->getActiveSheet()->getStyle('D')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
	
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
		
		$i =2;
		foreach ($users as  $one){
// 			var_dump($one);exit();
			$pron = $pro_arr[$one->province].'-'.$pro_arr[$one->city];
			//添加图片
			$poster1 =  substr($one->poster1,1,(strlen($one->poster1)-1));
			$poster2 =  substr($one->poster2,1,(strlen($one->poster2)-1));
			if($one->poster1 && $one->poster2 && file_exists($poster1) && file_exists($poster2)){
				
				$objDrawing = new PHPExcel_Worksheet_Drawing();
				$objDrawing->setName("poster".$i);
				$objDrawing->setDescription("zhao".$i);
				$objDrawing->setPath($poster1);
				$objDrawing->setHeight(100);
				$objDrawing->setWidth(100);
				$objDrawing->setCoordinates("I$i");
				$objDrawing->setOffsetX(6);
				//$objDrawing->setRotation(6);
				//$objDrawing->getShadow()->setVisible(true);
				//$objDrawing->getShadow()->setDirection(36);
				$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
					
				$objDrawing1 = new PHPExcel_Worksheet_Drawing();
				$objDrawing1->setName("poster".$i);
				$objDrawing1->setDescription("zhao".$i);
				$objDrawing1->setPath($poster2);
				$objDrawing1->setHeight(100);
				$objDrawing1->setWidth(100);
				$objDrawing1->setCoordinates("J$i");
				$objDrawing1->setOffsetX(6);
				$objDrawing1->setWorksheet($objPHPExcel->getActiveSheet());
			}
			
			if ($one->status == 1) {
				$currentStatus = '未通过';
			}else if ($one->status == 2) {
				$currentStatus = '退回重申';
			}else if ($one->status == 3) {
				$currentStatus = '已经通过';
			}else if ($one->status == 4) {
				$currentStatus = '撤销';
			}else{
				$currentStatus = '等待审核';
			}
			$current_benben = '';
			if ($one->id_enable == 1) {
				$current_benben = $one->benben_id;
			}
						
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue("A$i", $one->phone)
			->setCellValue("B$i", $one->short_phone)			
			->setCellValue("C$i", $one->name)
			->setCellValue("D$i", $current_benben)
			->setCellValueExplicit("E$i", $one->card,PHPExcel_Cell_DataType::TYPE_STRING)
			->setCellValue("F$i", $pron)
			->setCellValue("G$i", $one->mname)
			->setCellValue("H$i", $one->mphone)
			->setCellValue("K$i", date("Y-m-d H:i:s", $one->created_time))
			->setCellValue("L$i", $currentStatus)
			->setCellValue("M$i", "");
			$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(100);
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
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
	
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
	$objPHPExcel->getActiveSheet()->getStyle('K1')->applyFromArray($styleArray1);
	$objPHPExcel->getActiveSheet()->getStyle('L1')->applyFromArray($styleArray1);
	
	$objPHPExcel->getActiveSheet()->setTitle('百姓网申请信息');      //设置sheet的名称
	$objPHPExcel->setActiveSheetIndex(0);   //设置sheet的起始位置
	
	ob_end_clean();
	ob_start();
	header('Content-Type: application/vnd.ms-excel;charset=utf-8');
	header('Content-Disposition:attachment;filename=' . urlencode('bxapplymember' . date("YmjHis") .'.xls') . '');
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');   //通过PHPExcel_IOFactory的写函数将上面数据写出来
	
	$objWriter->save('php://output');
	ini_set('memory_limit','128M');
}

public function actionPutexcel(){
	$this->insert_log(21);
	$this->menuIndex = 21;
	$this->render("putexcel",array("add"=>86399));
}

public function actionLog()
{
	$this->insert_log(23);
	$this->menuIndex = 23;
	$model = BxapplyStatus::model();
	$cri = new CDbCriteria();
	$cri->select = "t.*, a.username as rname, c.name";
	$cri->join = "left join user a on a.id = t.user_id left join bxapply c on t.apply_id = c .id";
	$cri->order = "t.id desc";
	if($_GET['created_time1'] && $_GET['created_time2']){
		$ct1 = strtotime($_GET['created_time1']);
		$ct2 = strtotime($_GET['created_time2'])+86399;
		if($ct1 >= $ct2){
			$msg = "申请日期第一个必须比第二个小!";
		}else{
			$cri->addCondition('t.created_time >= '.$ct1,'AND');
			$result['created_time1'] = $_GET['created_time1'];
			$cri->addCondition('t.created_time <= '.$ct2,'AND');
			$result['created_time2'] = $_GET['created_time2'];
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
	$pages = new CPagination();
	$pages->itemCount = $model->count($cri);
	$pages->pageSize = 50;
	$pages->applyLimit($cri);
	$items = $model->findAll($cri);
	$this->render("log",array('items'=>$items,'pages'=> $pages, 'result' => $result));
}

public function actionInputexcel(){
	$this->menuIndex = 22;
	$this->insert_log(22);
	
	if ((Yii::app()->request->isPostRequest)) {
		//$inputfile = dirname(__FILE__)."/../../uploads/excel/2015-05/1432029514-1770201150.xls";//Frame::saveExcel("inputfile");
		if ($_POST['phone']&&$_POST['status']) {
			$phone = trim($_POST['phone']);
			$status = intval($_POST['status']);
			$short_phone = trim($_POST['short_phone']);
			$reason = $_POST['reason'];
			$model = Bxapply::model();
			$info = $model->find("phone = '{$phone}' and status <> 4 order by id desc ");
			// var_dump($info);die();
			if ($info) {
				$info->status = $status;
				
				if ($status == 3) {
						$info->short_phone = $short_phone;
						$have = Bxapply::model()->count("short_phone = {$short_phone}");
						if($have){
							$msg = '百姓网号不能重复';
							$re['status'] = $status;
							$re['phone'] = $phone;
							$re['short_phone'] = $short_phone;
							$re['reason'] = $reason;
							$this->render("inputexcel",array("msg"=>$msg, 're' => $re));						
							exit;
						}
						$info->join_time =time();
						$enterpriseInfo = EnterpriseMember::model()->find('contact_id = '.$this->dongyang.' and phone = '.$phone);
						if (!$enterpriseInfo) {
							$memberFind = Member::model()->find("phone = {$phone}");
							$info_id = 0;
							if($memberFind){
								$info_id = $memberFind->id;
							}
							$enterpriseM = new EnterpriseMember();
							$enterpriseM->contact_id = $this->dongyang;
							$enterpriseM->member_id = $info_id;
							$enterpriseM->short_phone = $short_phone;
							$enterpriseM->phone = $phone;
							$enterpriseM->name = $info->name?$info->name:$phone;
							$enterpriseM->created_time = time();
							$enterpriseM->invite_id = 0;
							$enterpriseM->save();
							$enterprise = Enterprise::model()->findByPk($this->dongyang);
							$enterprise->number = $enterprise->number+1;
							$enterprise->save();
						}
						
					}else{
						$info->short_phone = '';
						$enterpriseInfo = EnterpriseMember::model()->find('contact_id = '.$this->dongyang.' and phone = '.$phone);
						if ($enterpriseInfo) {
							$enterpriseInfo->delete();
							$enterprise = Enterprise::model()->findByPk($this->dongyang);
							$enterprise->number = $enterprise->number-1;
							$enterprise->save();
						}
					}
				$info->update();
				$member_id = $this->getLoginId();
				$bxapply_record = new BxapplyRecord();
				$bxapply_record->apply_id = $info->id;
				$bxapply_record->status = $status;
				$bxapply_record->user_id = $member_id;
				$bxapply_record->reason = $reason;
				$bxapply_record->short_phone = $short_phone;
				$bxapply_record->created_time = time();
				$bxapply_record->save();
				
				$applyStatus = new BxapplyStatus();
				$applyStatus->apply_id = $info->id;
				$applyStatus->bx_status = $status;
				$applyStatus->status = 1;
				$applyStatus->user_id = $member_id;
				$applyStatus->reason = $reason;
				$applyStatus->phone = $phone;
				$applyStatus->created_time = time();
				$applyStatus->save();
			}else{
				$this->render("inputexcel",array("msg"=>"未找到该用户"));
			}

		}else{
			$inputfile = dirname(__FILE__)."/../..".Frame::saveExcel("inputfile");
			if(!$_FILES [inputfile] ['name']){			
				$this->render("inputexcel",array('items'=>$items,'pages'=>$pages,"msg"=>"请输入文件"));
				exit();
			}
			$returnArray = $this->readExcel($inputfile);
			$this->render("showexcel",array( 'result' => $returnArray));
			die();
		}
		
		
		$this->render("inputexcel",array("msg"=>"数据录入成功",'items'=>$items,'pages'=> $pages, 'result' => $result));
		exit();		
	}
	$this->render("inputexcel",array('items'=>$items,'pages'=> $pages, 'result' => $result));
}

	public function actionDownload()
	{
		$objPHPExcel = new PHPExcel();
		/*--------------设置表头信息------------------*/
		//第一个sheet
		$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A1', '手机号码')
		->setCellValue('B1', '百姓网号')
		->setCellValue('C1', '姓名')
		->setCellValue('D1', '身份证号码')
		->setCellValue('E1', '地区(省-市-区，以-区分)')
		->setCellValue('F1', '提交人')
		->setCellValue('G1', '提交人手机号码')
		->setCellValue('H1', '照片(正面)')
		->setCellValue('I1', '照片(反面)')
		->setCellValue('J1', '申请时间')
		->setCellValue('K1', '审核状态(0为默认等待审核，1未通过，2退回重申，3已经通过,4为撤销)')
		->setCellValue('L1', '反馈信息');
		
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
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
		$objPHPExcel->getActiveSheet()->getStyle('K1')->applyFromArray($styleArray1);
		$objPHPExcel->getActiveSheet()->getStyle('L1')->applyFromArray($styleArray1);
		$objPHPExcel->getActiveSheet()->getStyle('D')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
	
		$objPHPExcel->getActiveSheet()->setTitle('百姓网申请信息导入');      //设置sheet的名称
		$objPHPExcel->setActiveSheetIndex(0);   //设置sheet的起始位置
		
		ob_end_clean();
		ob_start();
		header('Content-Type: application/vnd.ms-excel;charset=utf-8');
		header('Content-Disposition:attachment;filename=' . urlencode('bxapply' . date("YmjHis") .'.xls') . '');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');   //通过PHPExcel_IOFactory的写函数将上面数据写出来
	
		$objWriter->save('php://output');
	}


	public function actionDelete($id)
	{
		$id = Frame::getIntFromRequest('id');
		if ($id > 0) {
			$model = Bxapply::model ()->findByPk ( $id );
		}
		if ($model) {
			$model->delete();
		}
		$this->redirect ( Yii::app()->createUrl('bxapply/index',array('page'=>intval($_REQUEST['page']))));
	}

	public function actionGetinfo(){
		$phone = addslashes($_GET['phone']);
		if ($phone) {
			$info = Bxapply::model()->find("phone = '{$phone}' and status <> 4 order by id desc ");
			if ($info) {
				$status = array("0"=>"等待审核", "1"=>"未通过", "2" => "退回重申", "3" => "已经通过", "4" => "撤消");
				echo '手机用户当前状态为'.$status[$info->status]."。确认要调整？？";
			}else{
				echo '没有找到该手机用户';
			}
		}
	}

	public function loadModel($id)
	{
		$model=Bxapply::model()->findByPk($id);
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
			return Yii::app()->createUrl("bxapply/index",array('page'=>$_REQUEST['page']));
		}
	}

	/**
	 * Performs the AJAX validation.
	 * @param Bxapply $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='bxapply-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
	function readExcel($filePath){
		
		/**默认用excel2007读取excel，若格式不对，则用之前的版本进行读取*/
		$PHPReader = new PHPExcel_Reader_Excel2007();
	
		if(!$PHPReader->canRead($filePath)){
			$PHPReader = new PHPExcel_Reader_Excel5();
			if(!$PHPReader->canRead($filePath)){
				return 'no Excel';
			}
		}
	
		$PHPExcel = $PHPReader->load($filePath); /**读取excel文件*/
		$currentSheet = $PHPExcel->getSheet(0);   /**取得最大的列号*/
		$allColumn = $currentSheet->getHighestColumn();   /**取得一共有多少行*/
		$allRow = $currentSheet->getHighestRow();
		$returnArray = array();
		for($currentRow = 2;$currentRow <= $allRow;$currentRow++){
	
			$phone = trim($this->check_input($currentSheet->getCellByColumnAndRow(0,$currentRow)->getValue()));//手机号码
			$short_phone = trim($this->check_input($currentSheet->getCellByColumnAndRow(1,$currentRow)->getValue()));//百姓网号
			$name = trim($this->check_input($currentSheet->getCellByColumnAndRow(2,$currentRow)->getValue()));//姓名
			$idcard = trim($this->check_input($currentSheet->getCellByColumnAndRow(3,$currentRow)->getFormattedValue()));//身份证
			$areaString = trim($this->check_input($currentSheet->getCellByColumnAndRow(4,$currentRow)->getValue()));//地区
			$putName = trim($this->check_input($currentSheet->getCellByColumnAndRow(5,$currentRow)->getValue()));//提交人
			$putPhone = trim($this->check_input($currentSheet->getCellByColumnAndRow(6,$currentRow)->getValue()));//提交人号码
			$submitTime = (trim($this->check_input($currentSheet->getCellByColumnAndRow(9,$currentRow)->getValue())));//提交时间 
			$submitTime = $submitTime?strtotime($this->excelTime($submitTime)):time();
			$statusCN = trim($this->check_input($currentSheet->getCellByColumnAndRow(10,$currentRow)->getValue()));  //状态
			if ($statusCN == '未通过') {
				$status = 1;
			}else if ($statusCN == '退回重申') {
				$status = 2;
			}else if ($statusCN == '已经通过') {
				$status = 3;
			}else if ($statusCN == '撤消'||$statusCN == '撤销') {
				$status = 4;
			}else{
				$status = 0;
			}
			$info = $this->check_input($currentSheet->getCellByColumnAndRow(11,$currentRow)->getValue());//反馈信息
			if($phone){
                $flag = 0;
                //查询提交人，如果没有提交人，则在数据库中添加一条数据
     //            $putInfo = Member::model()->find("phone = '{$putPhone}'");
     //            if ($putInfo) {
     //            	$submitId =  $putInfo->id;
     //            }else{
     //            	$putInfo = new Member();
     //            	$putInfo->nick_name = $putName;
					// $putInfo->phone = $putPhone;
					// $putInfo->id_enable = 0;			
					// $putInfo->created_time = time();
					// $putInfo->save();
					// $submitId =  $putInfo->id;
     //            }
                $re = Bxapply::model()->find("phone = '{$phone}' order by id desc");	
                $currentInfo = array();	
                $currentInfo['putPhone'] = $putPhone;
				$currentInfo['putName'] = $putName;
				$currentInfo['phone'] = $phone;
				$currentInfo['submitTime'] = $submitTime;
				$currentInfo['short_phone'] = $short_phone;
				$currentInfo['name'] = $name;
				$currentInfo['status'] = $status;	
				$currentInfo['reason'] = $info;
				$currentInfo['idcard'] = $idcard;
				$currentInfo['apply_id'] = $re->id;
				$currentInfo['is_update'] = 1;
				$currentInfo['phoneis_update'] = 1;
				if(($status == 3) && !$short_phone){
					$currentInfo['is_update'] = 0;
				}
				if ($short_phone) {
					$re2 = Bxapply::model()->find("short_phone = '{$short_phone}'");
					if ($re2) {
						$currentInfo['is_update'] = 0;
					}
				}			
				if ($phone) {
					$re2 = Bxapply::model()->find("phone = '{$phone}' order by id desc");
					$currentInfo['phoneis_update'] = 0;
					if((($re2->status == 0) && (($status == 1)||($status == 2)||($status == 3)))){
						$currentInfo['phoneis_update'] = 1;
					}else if((($re2->status == 4)&&(($re2->phone == $phone)||($re2->short_phone == $short_phone)||(($re2->phone == $phone)&&($re2->short_phone == $short_phone))))){
						$currentInfo['phoneis_update'] = 1;
					}else if(($re2->status == 3)&&($status == 3)&&($re2->phone == $phone)){
						$currentInfo['phoneis_update'] = 0;
					}					
					$res2 = Bxapply::model()->find("phone = '{$phone}' or short_phone = '{$short_phone}' order by id desc");
					if(($status == 4)&&(($res2->phone == $phone) || ($res2->short_phone == $short_phone))){
						$currentInfo['apply_id'] = $re2->id;
						$currentInfo['phoneis_update'] = 1;
						$currentInfo['is_update'] = 1;
					}					
					
					if ($re2) {
						//$currentInfo['phoneis_update'] = 0;
					}
				}
				
				
				
				if($re){
					$currentInfo['type'] = '1';
					if(($re2->status == 4)&&(($re2->phone == $phone)||($re2->short_phone == $short_phone)||(($re2->phone == $phone)&&($re2->short_phone == $short_phone)))){
						$currentInfo['type'] = 2;
					}
					if(($status == 4)&&($res2->status == 4)){
						$currentInfo['type'] = 3;
					}
					if($re->short_phone == $short_phone){
						//$currentInfo['is_update'] = 1;
						//$currentInfo['phoneis_update'] = 1;
					}
					$areaArray = explode('-', $areaString);
					$province = 0;$city = 0; $area=0;
					$provinceName = ''; $cityName = ''; $areaName = '';
					if (count($areaArray)>0) {
						$provinceInfo = Area::model()->find('parent_bid = 0 and area_name = "'.$areaArray[0].'"');
						if ($provinceInfo) {
							$province = $provinceInfo->bid;
							$provinceName = $provinceInfo->area_name;
							if (count($areaArray)>1) {
								$cityInfo = Area::model()->find('parent_bid = '.$province.' and area_name = "'.$areaArray[1].'"');
								if ($cityInfo) {
									$city = $cityInfo->bid;
									$cityName = $cityInfo->area_name;
									if (count($areaArray) > 2) {
										$areaInfo = Area::model()->find('parent_bid = '.$city.' and area_name = "'.$areaArray[2].'"');
										if ($areaInfo) {
											$area = $areaInfo->bid;
											$areaName = $areaInfo->area_name;
										}
									}
								}
							}
								
						}
					}
					$currentInfo['province'] = $province;
					$currentInfo['city'] = $city;
					$currentInfo['area'] = $area;
					$currentInfo['provinceName'] = $provinceName;
					$currentInfo['cityName'] = $cityName;
					$currentInfo['areaName'] = $areaName;
					// $re->short_phone = $short_phone;
					// if ($status == 3) {
					// 	$re->join_time = time();
					// }else if($status == 4){
					// 	$re->short_phone = '';
					// 	$re->cancel_time = time();
					// }
					// $re->status = $status;
					// if($re->update()){
					// 	$bxrecord = new BxapplyRecord();
					// 	$bxrecord->apply_id = $re->id;
					// 	$bxrecord->status = $status;
					// 	$bxrecord->user_id = $re->member_id;
					// 	$bxrecord->reason = $info;
					// 	$bxrecord->created_time = time();
					// 	$bxrecord->short_phone = $short_phone;
					// 	$bxrecord->save();
					// 	//记录Excel文件导入结果
					// 	$bx_status = new BxapplyStatus();
					// 	$bx_status->apply_id = $re->id;
					// 	$bx_status->phone = $phone;
					// 	$bx_status->reason = $info;
					// 	$bx_status->bx_status = $status;
					// 	$bx_status->status = 1;
					// 	$bx_status->user_id = $this->getLoginId();
					// 	$bx_status->created_time = time();
					// 	$bx_status->save();
					// }else{
					// 	$flag = 1;						
					// }	
				}else{
					$currentInfo['is_update'] = 1;
					$currentInfo['phoneis_update'] = 1;
					$areaArray = explode('-', $areaString);
					$province = 0;$city = 0; $area=0;
					$provinceName = ''; $cityName = ''; $areaName = '';
					if (count($areaArray)>0) {
						$provinceInfo = Area::model()->find('parent_bid = 0 and area_name = "'.$areaArray[0].'"');
						if ($provinceInfo) {
							$province = $provinceInfo->bid;
							$provinceName = $provinceInfo->area_name;
							if (count($areaArray)>1) {
								$cityInfo = Area::model()->find('parent_bid = '.$province.' and area_name = "'.$areaArray[1].'"');
								if ($cityInfo) {
									$city = $cityInfo->bid;
									$cityName = $cityInfo->area_name;
									if (count($areaArray) > 2) {
										$areaInfo = Area::model()->find('parent_bid = '.$city.' and area_name = "'.$areaArray[2].'"');
										if ($areaInfo) {
											$area = $areaInfo->bid;
											$areaName = $areaInfo->area_name;
										}
									}
								}
							}
							
						}
					}

					$currentInfo['type'] = '2';
					$currentInfo['province'] = $province;
					$currentInfo['city'] = $city;
					$currentInfo['area'] = $area;
					$currentInfo['provinceName'] = $provinceName;
					$currentInfo['cityName'] = $cityName;
					$currentInfo['areaName'] = $areaName;

					// $model=new Bxapply;
					// $model->phone = $phone;
					// $model->name = $name;
					// $model->short_phone = $short_phone;
					// $model->status = $status;
					// $model->backidcard= $idcard;
					// $model->province = $province;
					// $model->city = $city;
					// $model->area = $area;
					// $model->created_time =$submitTime;
					// $model->member_id = $submitId;
					// if ($status == 3) {
					// 	$model->join_time = time();
					// }
					// if ($model->save()) {
					// 	$bx_status = new BxapplyStatus();
					// 	$bx_status->apply_id = $model->id;
					// 	$bx_status->phone = $phone;
					// 	$bx_status->reason = $info;
					// 	$bx_status->bx_status = $status;
					// 	$bx_status->status = 1;
					// 	$bx_status->user_id = $this->getLoginId();
					// 	$bx_status->created_time = time();
					// 	$bx_status->save();

					// 	$bxrecord = new BxapplyRecord();
					// 	$bxrecord->apply_id = $model->id;
					// 	$bxrecord->status = $status;
					// 	$bxrecord->short_phone = $short_phone;
					// 	$bxrecord->user_id = $this->getLoginId();
					// 	$bxrecord->reason = $info;
					// 	$bxrecord->created_time = time();
					// 	$bxrecord->save();
					// }
				}
				$returnArray[] = $currentInfo;
					
			}else{
				break;
			}
						
		}
		return $returnArray;
	}

	public function actionSavedate()
	{
		$result = json_decode($_POST['data'], true);
		if (count($result) > 0) {
			//将所有批量导入的数据加入到东阳百姓网中
			$addEnterprisePhone = array();
			$removeEnterprisePhone = array();
			$addEnterpriseInfo = array();

			foreach ($result as $key => $value) {
				$putPhone = $value['putPhone'];
				$putName = $value['putName'];
				$phone = $value['phone'];
				$submitTime = $value['submitTime'] ;
				$short_phone = $value['short_phone'];
				$name = $value['name'];
				$status = $value['status'];	
				$info = $value['reason'];
				$apply_id = $value['apply_id'];
				$idcard = $value['idcard'];
				if ($value['is_update'] == 0) {
					continue;
				}
				if ($value['phoneis_update'] == 0) {
					continue;
				}
				
				//更新
				if ($value['type'] == 1) {
					$re = Bxapply::model()->findByPk($value['apply_id']);	
					$re->short_phone = $short_phone;
					if ($status == 3) {
						$re->join_time = time();
						$addEnterprisePhone[] = $phone;
						$addEnterpriseInfo[$phone] = array('short_phone'=>$short_phone, 'name'=>$name);
					}else if($status == 4){
						$re->short_phone = '';
						$re->cancel_time = time();
						$removeEnterprisePhone[] = $phone;
					}
					$re->status = $status;
					if($re->update()){
						$bxrecord = new BxapplyRecord();
						$bxrecord->apply_id = $re->id;
						$bxrecord->status = $status;
						$bxrecord->user_id = $re->member_id;
						$bxrecord->reason = $info;
						$bxrecord->created_time = time();
						$bxrecord->short_phone = $short_phone;
						$bxrecord->save();
						//记录Excel文件导入结果
						$bx_status = new BxapplyStatus();
						$bx_status->apply_id = $re->id;
						$bx_status->phone = $phone;
						$bx_status->reason = $info;
						$bx_status->bx_status = $status;
						$bx_status->status = 1;
						$bx_status->user_id = $this->getLoginId();
						$bx_status->created_time = time();
						$bx_status->save();
					}
				}else if($value['type'] == 2){
					//查询提交人，如果没有提交人，则在数据库中添加一条数据
	                $putInfo = Member::model()->find("phone = '{$putPhone}'");
	                if ($putInfo) {
	                	$submitId =  $putInfo->id;
	                }else{
	                	$putInfo = new Member();
	                	$putInfo->nick_name = $putName;
						$putInfo->phone = $putPhone;
						$putInfo->id_enable = 0;
						$putInfo->benben_id = 0;			
						$putInfo->created_time = time();
						$putInfo->save();
						$submitId =  $putInfo->id;
	                }
					$model=new Bxapply;
					$model->phone = $phone;
					$model->name = $name;
					$model->short_phone = $short_phone;
					$model->status = $status;
					$model->backidcard= $idcard;
					$model->province = $value['province'];
					$model->city = $value['city'];
					$model->area = $value['area'];
					$model->created_time =$submitTime;
					$model->member_id = $submitId;
					if ($status == 3) {
						$model->join_time = time();
						$addEnterprisePhone[] = $phone;
						$addEnterpriseInfo[$phone] = array('short_phone'=>$short_phone, 'name'=>$name);
					}else{
						$removeEnterprisePhone[] = $phone;
					}
					if ($model->save()) {
						$bx_status = new BxapplyStatus();
						$bx_status->apply_id = $model->id;
						$bx_status->phone = $phone;
						$bx_status->reason = $info;
						$bx_status->bx_status = $status;
						$bx_status->status = 1;
						$bx_status->user_id = $this->getLoginId();
						$bx_status->created_time = time();
						$bx_status->save();

						$bxrecord = new BxapplyRecord();
						$bxrecord->apply_id = $model->id;
						$bxrecord->status = $status;
						$bxrecord->short_phone = $short_phone;
						$bxrecord->user_id = $this->getLoginId();
						$bxrecord->reason = $info;
						$bxrecord->created_time = time();
						$bxrecord->save();
					}
				}
				
			}

			if (count($addEnterprisePhone)) {
				$connection = Yii::app()->db;
				$sql = "select phone from enterprise_member where contact_id=".$this->dongyang." and phone in (".implode(",", $addEnterprisePhone).")";
				$command = $connection->createCommand($sql);
				$resultc = $command->queryAll();
				$havePhone = array();
				if ($resultc && count($resultc)>0) {
					foreach($resultc as $e){
						$havePhone[] = $e['phone'];
					}
				}
				$addNumber = 0;
				foreach($addEnterpriseInfo as $k => $v){
					if (in_array($k, $havePhone)) {
						continue;
					}
					$enterpriseM = new EnterpriseMember();
					$enterpriseM->contact_id = $this->dongyang;
					$memberFind = Member::model()->find("phone = {$k}");
					$info_id = 0;
					if($memberFind){
						$info_id = $memberFind->id;
					}
					$enterpriseM->member_id = $info_id;
					$enterpriseM->short_phone = $v['short_phone'];
					$enterpriseM->phone = $k;
					$enterpriseM->name = $v['name'];
					$enterpriseM->created_time = time();
					$enterpriseM->invite_id = 0;
					if($enterpriseM->save()){
						$addNumber ++;
					}
					
				}
				$enterprise = Enterprise::model()->findByPk($this->dongyang);
				$enterprise->number = $enterprise->number+$addNumber;
				$enterprise->save();
				

			}
			if ($removeEnterprisePhone) {
				$deleteNumber = 0;
				foreach($removeEnterprisePhone as $e){
					$haveData = EnterpriseMember::model()->find("phone = '{$e}' and contact_id = ".$this->dongyang);
					if ($haveData) {
						$haveData->delete();
						$deleteNumber ++;
					}
				}
				$enterprise = Enterprise::model()->findByPk($this->dongyang);
				$enterprise->number = $enterprise->number-$deleteNumber;
				$enterprise->save();
			}
		}
		$this->redirect($this->getBackListPageUrl());
	}

	//PHPexcel时间格式处理
	function excelTime($date, $time = false) {  
	    if(function_exists('GregorianToJD')){  
	        if (is_numeric( $date )) {  
	        $jd = GregorianToJD( 1, 1, 1970 );  
	        $gregorian = JDToGregorian( $jd + intval ( $date ) - 25569 );  
	        $date = explode( '/', $gregorian );  
	        $date_str = str_pad( $date [2], 4, '0', STR_PAD_LEFT )  
	        ."-". str_pad( $date [0], 2, '0', STR_PAD_LEFT )  
	        ."-". str_pad( $date [1], 2, '0', STR_PAD_LEFT )  
	        . ($time ? " 00:00:00" : '');  
	        return $date_str;  
	        }  
	    }else{  
	        $date=$date>25568?$date+1:25569;  
	        /*There was a bug if Converting date before 1-1-1970 (tstamp 0)*/  
	        $ofs=(70 * 365 + 17+2) * 86400;  
	        $date = date("Y-m-d",($date * 86400) - $ofs).($time ? " 00:00:00" : '');  
	    }  
	  return $date;  
	} 
	
	//拼写检查
	function check_input($value = '')
	{
		// 去除斜杠
		if (get_magic_quotes_gpc())
		{
			$value = stripslashes($value);
		}
		// 如果不是数字则加引号
		if (!is_numeric($value))
		{
			//$value = mysql_real_escape_string($value);
		}
		$value = htmlspecialchars($value);
		return $value;
	}
	
}
