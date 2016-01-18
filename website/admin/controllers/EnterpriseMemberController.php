<?php
Yii::$enableIncludePath = false;
define('__ROOT__',dirname(dirname(__FILE__)));
require_once (__ROOT__.'/PHPExcel/PHPExcel.php');
class EnterpriseMemberController extends BaseController
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/admin';
	
	/**
	 * @var int the define the index for the menu
	 */
	 
	 public $menuIndex = 30;
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new EnterpriseMember;
		$contact_id = intval($_GET['id']);
		$model->contact_id = $contact_id;
		$en = Enterprise::model()->findByPk($contact_id);
		$ename = $en->name;
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['EnterpriseMember']))
		{
			//$model->attributes=$_POST['EnterpriseMember'];
			$name = $_POST['EnterpriseMember']['name'];
			$phone = $_POST['EnterpriseMember']['phone'];
			$short_phone = $_POST['EnterpriseMember']['short_phone'];
			$member_id = Member::model()->find("phone = '{$phone}'");
			$model->name = $name;
			$model->phone = $phone;
			$model->short_phone = $short_phone;
			$model->member_id = $member_id->id;
			$model->created_time = time();
			if($model->save())
				$en->number = $en->number + 1;
				$en->update();
				$this->redirect(Yii::app()->createUrl("enterpriseMember/index",array('id'=>$_GET['id'],'page'=>$_REQUEST['page'])));
				//$this->redirect($this->getBackListPageUrl());
		}

		$this->render('create',array(
			'model'=>$model,
			'ename'=>$ename,
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
		//更新备注名
		if(isset($_POST['EnterpriseMember']))
		{
			$name = $_POST['EnterpriseMember']['name'];
			
			if($model){
				$model->name = $name;				
			}						
			if($model->update())
				$burl = Yii::app()->createUrl("enterpriseMember/index",array('id'=>$model->contact_id,'page'=>$_REQUEST['page']));
				$this->redirect($burl);
		}
		//通讯录名称
		$enterprise = new Enterprise();
		$sql = "SELECT name FROM enterprise WHERE id = ".$model->contact_id;
		$ename = $enterprise->findBySql($sql);
		$name = $ename->name;

		$model->created_time = date('Y-m-d H:i:s', $model->created_time);
		$this->render('update',array(
			'model'=>$model,
			'ename' => $name,
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
		$model = EnterpriseMember::model();
		$cri = new CDbCriteria();
		$cri->select = "t.*, enterprise.name as ename, member.nick_name as mname,member.id as mid,enterprise.origin ";
		$cri->join = "left join enterprise on enterprise.id = t.contact_id left join member on member.id = t.member_id";

		$cri->order = "t.created_time desc";
		$cri->addCondition('t.contact_id = '.$_GET['id']);
		if(isset($_GET) && $_GET){
			$phone = $_GET['phone'];
			$short_phone = $_GET['short_phone'];
			if($phone){
				$cri->addSearchCondition('t.phone',$phone);
			}
			if($short_phone){
				$cri->addSearchCondition('t.short_phone',$short_phone);
			}
		}
		$pages = new CPagination();
		$pages->itemCount = $model->count($cri);
		$pages->pageSize = 50;
		$pages->applyLimit($cri);
		$items = $model->findAll($cri);
		$cookie = Yii::app()->request->getCookies();
		$returnUrl = $cookie['benben-neverland']->value;

		$this->render('index',array('items'=>$items,'pages'=> $pages,'returnUrl'=>$returnUrl));
		
	}

	public function actionIndexDownload()
	{
		$model = EnterpriseMember::model();
		$sql = "SELECT t.*, enterprise.name as ename, member.nick_name as mname,member.id as mid  from enterprise_member as t left join enterprise on enterprise.id = t.contact_id left join member on member.id = t.member_id where t.contact_id = ".$_GET['id']. " order by t.created_time desc" ;
		$ereason = $model->findAllBySql($sql);
		if ($ereason) {
			$title = "政企成员用户";
			$filename = "enterprisse";
			$objPHPExcel = new PHPExcel();
			/*--------------设置表头信息------------------*/
			//第一个sheet
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A1', '通讯录备注名')
			->setCellValue('B1', '手机号码')
			->setCellValue('C1', '其它号')
			->setCellValue('D1', '是否是奔犇用户')
			->setCellValue('E1', '加入时间');
			
			if(!empty($ereason)){			
				$i =2;
				foreach ($ereason as  $one){
					$isMember = '否';
					if ($one['member_id']) {
						$isMember = '是';
					}
					$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue("A$i", $one['name'])
					->setCellValue("B$i", $one['phone'])
					->setCellValue("C$i", $one['short_phone'])
					->setCellValue("D$i", $isMember)
					->setCellValue("E$i", date('Y-m-d H:i:s', $one['created_time']));
					//$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(100);
					$i++;
				}
			}
			$objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
			
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
			
			$objPHPExcel->getActiveSheet()->setTitle($title);      //设置sheet的名称
			$objPHPExcel->setActiveSheetIndex(0);   //设置sheet的起始位置
			
			ob_end_clean();
			ob_start();
			header('Content-Type: application/vnd.ms-excel;charset=utf-8');
			header('Content-Disposition:attachment;filename=' . urlencode($filename . date("YmjHis") .'.xls') . '');
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');   //通过PHPExcel_IOFactory的写函数将上面数据写出来
			
			$objWriter->save('php://output');
		}else{
			exit();
		}
	}

	
	public function actionDelete($id)
	{
		$id = Frame::getIntFromRequest('id');
		if ($id > 0) {
			$model = EnterpriseMember::model ()->findByPk ( $id );
		}
		if ($model) {
			$en = Enterprise::model()->findByPk($model->contact_id);			
			if($model->delete() && $en){
				$en->number = $en->number - 1;
				$en->update();
			}
		}
		$this->redirect ( Yii::app()->createUrl('enterpriseMember/index',array('id'=>$model->contact_id,'page'=>intval($_REQUEST['page']))));
	}
	

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return EnterpriseMember the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=EnterpriseMember::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
	
	public function getBackListPageUrl()
	{
		return Yii::app()->createUrl("enterpriseMember/index",array('page'=>$_REQUEST['page']));
	}

	/**
	 * Performs the AJAX validation.
	 * @param EnterpriseMember $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='enterprise-member-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
