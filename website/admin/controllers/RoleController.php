<?php

class RoleController extends BaseController
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/admin';
	
	/**
	 * @var int the define the index for the menu
	 */
	 
	 public $menuIndex = 81;
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Role;
		if(isset($_POST['Role']))
		{
			$role_name = $_POST['Role']['role_name'];
			if(empty($role_name)){
				$msg = "角色名称不能为空！";
			}else{
				$model->role_name = $role_name;	
				//$model->domember= $_POST['Role']['domember'][0] ? 1 : 0 ;
				$member_arr = $_POST['Role']['domember'];
				if(1){
					$domember = 0;
					if($member_arr['editall']){
						$domember += 3;
					}else{
						if($member_arr['edit']) $domember += 1;
						if($member_arr['put']) $domember += 2;
					}
					if($member_arr['statistic']) $domember += 4;//var_dump($domember);exit;
					$model->domember= $domember;
				}
				//$model->dobaixing =  $_POST['Role']['dobaixing'][0] ? 1 : 0;
				//var_dump($_POST);
				$baixing_arr = $_POST['Role']['dobaixing'];
				if(1){
					$dobaixing = 0;
					if($baixing_arr['editall']){
						$dobaixing += 3;
					}else{
						if($baixing_arr['edit']) $dobaixing += 1;
						if($baixing_arr['put']) $dobaixing += 2;
					}
					if($baixing_arr['putapply']) $dobaixing += 4;
					if($baixing_arr['input']) $dobaixing += 8;
					if($baixing_arr['statistic']) $dobaixing += 16;
					//var_dump($dobaixing);exit;
					$model->dobaixing= $dobaixing;
				}
				//$model->doenterprise =  $_POST['Role']['doenterprise'][0] ? 1 : 0;
				$enterprise_arr = $_POST['Role']['doenterprise'];
				if(1){
					$doenterprise = 0;
					if($enterprise_arr['editall']){
						$doenterprise += 7;
					}else{
						if($enterprise_arr['edit']) $doenterprise += 1;
						if($enterprise_arr['put']) $doenterprise += 2;
						if($enterprise_arr['create']) $doenterprise += 4;
					}
					//var_dump($doenterprise);exit;
					$model->doenterprise= $doenterprise;
				}
				//$model->dogroup =  $_POST['Role']['dogroup'][0] ? 1 : 0;
				$group_arr = $_POST['Role']['dogroup'];
				if(1){
					$dogroup = 0;
					if($group_arr['editall']){
						$dogroup += 1;
					}
					//var_dump($dogroup);exit;
					$model->dogroup= $dogroup;
				}
				//$model->dostore =  $_POST['Role']['dostore'][0] ? 1 : 0;
				$store_arr = $_POST['Role']['dostore'];
				if(1){
					$dostore = 0;
					if($store_arr['editall']){
						$dostore += 7;
					}else{
						if($store_arr['edit']) $dostore += 1;
						if($store_arr['top']) $dostore += 2;
						if($store_arr['statistic']) $dostore += 4;
					}
					//var_dump($dostore);exit;
					$model->dostore= $dostore;
				}
				//$model->docreation =  $_POST['Role']['docreation'][0] ? 1 : 0;
				$creation_arr = $_POST['Role']['docreation'];
				if(1){
					$docreation = 0;
					if($creation_arr['editall']){
						$docreation += 3;
					}else{
						if($creation_arr['edit']) $docreation += 1;
						if($creation_arr['put']) $docreation += 2;
					}
					//var_dump($docreation);exit;
					$model->docreation= $docreation;
				}
				//$model->dorelease =  $_POST['Role']['dorelease'][0] ? 1 : 0;
				$release_arr = $_POST['Role']['dorelease'];
				if(1){
					$dorelease = 0;
					if($release_arr['editall']){
						$dorelease += 3;
					}else{
						if($release_arr['edit']) $dorelease += 1;
						if($release_arr['put']) $dorelease += 2;
					}
					//var_dump($dorelease);exit;
					$model->dorelease= $dorelease;
				}
				//$model->dofriend =  $_POST['Role']['dofriend'][0] ? 1 : 0;
				$friend_arr = $_POST['Role']['dofriend'];
				if(1){
					$dofriend = 0;
					if($friend_arr['editall']){
						$dofriend += 3;
					}else{
						if($friend_arr['edit']) $dofriend += 1;
						if($friend_arr['put']) $dofriend += 2;
					}
					//var_dump($dofriend);exit;
					$model->dofriend= $dofriend;
				}
				//$model->dohappy =  $_POST['Role']['dohappy'][0] ? 1 : 0;
				$happy_arr = $_POST['Role']['dohappy'];
				if(1){
					$dohappy = 0;
					if($happy_arr['editall']){
						$dohappy += 7;
					}else{
						if($happy_arr['edit']) $dohappy += 1;
						if($happy_arr['create']) $dohappy += 2;
						if($happy_arr['upload']) $dohappy += 4;
					}
					//var_dump($dohappy);exit;
					$model->dohappy= $dohappy;
				}
				
				$find_arr = $_POST['Role']['dofind'];
				if(1){
					$dofind = 0;
					if($find_arr['statistic']){
						$dofind += 1;
					}
					//var_dump($dofind);exit;
					$model->dofind= $dofind;
				}
				//$model->donews =  $_POST['Role']['donews'][0] ? 1 : 0;
				$news_arr = $_POST['Role']['donews'];
				if(1){
					$donews = 0;
					if($news_arr['system']) $donews += 1;
					if($news_arr['to']) $donews += 2;
					if($news_arr['broadcastingLog']) $donews += 4;
					//var_dump($donews);exit;
					$model->donews= $donews;
				}
				//$model->dowebsite =  $_POST['Role']['dowebsite'][0] ? 1 : 0;
				//$model->dosystem =  $_POST['Role']['dosystem'][0] ? 1 : 0;
				$system_arr = $_POST['Role']['dosystem'];
				if(1){
					$dosystem = 0;					
					if($system_arr['user']) $dosystem += 1;
					if($system_arr['role']) $dosystem += 2;
					if($system_arr['password']) $dosystem += 4;
					if($system_arr['log']) $dosystem += 8;
					//var_dump($dosystem);exit;
					$model->dosystem= $dosystem;
				}
				
				$other_arr = $_POST['Role']['doother'];
				if(1){
					$doother = 0;
					if($other_arr['protocol']) $doother += 1;
					if($other_arr['industry']) $doother += 2;
					if($other_arr['version']) $doother += 4;
					if($other_arr['complain']) $doother += 8;
					if($other_arr['splash']) $doother += 16;
					//var_dump($doother);exit;
					$model->doother= $doother;
				}
				//$model->doleague =  $_POST['Role']['doleague'][0] ? 1 : 0;
				$league_arr = $_POST['Role']['doleague'];
				if(1){					
					$doleague = 0;
					if($league_arr['editall']){
						$doleague += 3;
					}else{
						if($league_arr['edit']) $doleague += 1;
						if($league_arr['put']) $doleague += 2;						
					}					
					//var_dump($doleague);exit;
					$model->doleague= $doleague;
				}
				$model->created_time =  time();
				if($model->save())
				$this->redirect($this->getBackListPageUrl());
			}
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

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Role']))
		{
			$role_name = $_POST['Role']['role_name'];
			if(empty($role_name)){
				$msg = "角色名称不能为空！";
			}else{
				$model->role_name = $role_name;	
				//$model->domember= $_POST['Role']['domember'][0] ? 1 : 0 ;
				$member_arr = $_POST['Role']['domember'];
				if(1){
					$domember = 0;
					if($member_arr['editall']){
						$domember += 3;
					}else{
						if($member_arr['edit']) $domember += 1;
						if($member_arr['put']) $domember += 2;
					}
					if($member_arr['statistic']) $domember += 4;//var_dump($domember);exit;
					$model->domember= $domember;
				}
				//$model->dobaixing =  $_POST['Role']['dobaixing'][0] ? 1 : 0;
				//var_dump($_POST);
				$baixing_arr = $_POST['Role']['dobaixing'];
				if(1){
					$dobaixing = 0;
					if($baixing_arr['editall']){
						$dobaixing += 35;
					}else{
						if($baixing_arr['index']) $dobaixing += 32;
						if($baixing_arr['edit']) $dobaixing += 1;
						if($baixing_arr['put']) $dobaixing += 2;
					}
					if($baixing_arr['putapply']) $dobaixing += 4;
					if($baixing_arr['input']) $dobaixing += 8;
					if($baixing_arr['statistic']) $dobaixing += 16;
					//var_dump($dobaixing);exit;
					$model->dobaixing= $dobaixing;
				}
				//$model->doenterprise =  $_POST['Role']['doenterprise'][0] ? 1 : 0;
				$enterprise_arr = $_POST['Role']['doenterprise'];
				if(1){
					$doenterprise = 0;
					if($enterprise_arr['editall']){
						$doenterprise += 7;
					}else{
						if($enterprise_arr['edit']) $doenterprise += 1;
						if($enterprise_arr['put']) $doenterprise += 2;
						if($enterprise_arr['create']) $doenterprise += 4;
					}
					//var_dump($doenterprise);exit;
					$model->doenterprise= $doenterprise;
				}
				//$model->dogroup =  $_POST['Role']['dogroup'][0] ? 1 : 0;
				$group_arr = $_POST['Role']['dogroup'];
				if(1){
					$dogroup = 0;
					if($group_arr['editall']){
						$dogroup += 1;
					}
					//var_dump($dogroup);exit;
					$model->dogroup= $dogroup;
				}
				//$model->dostore =  $_POST['Role']['dostore'][0] ? 1 : 0;
				$store_arr = $_POST['Role']['dostore'];
				if(1){
					$dostore = 0;
					if($store_arr['editall']){
						$dostore += 7;
					}else{
						if($store_arr['edit']) $dostore += 1;
						if($store_arr['top']) $dostore += 2;
						if($store_arr['statistic']) $dostore += 4;
					}
					//var_dump($dostore);exit;
					$model->dostore= $dostore;
				}
				//$model->docreation =  $_POST['Role']['docreation'][0] ? 1 : 0;
				$creation_arr = $_POST['Role']['docreation'];
				if(1){
					$docreation = 0;
					if($creation_arr['editall']){
						$docreation += 3;
					}else{
						if($creation_arr['edit']) $docreation += 1;
						if($creation_arr['put']) $docreation += 2;
					}
					//var_dump($docreation);exit;
					$model->docreation= $docreation;
				}
				//$model->dorelease =  $_POST['Role']['dorelease'][0] ? 1 : 0;
				$release_arr = $_POST['Role']['dorelease'];
				if(1){
					$dorelease = 0;
					if($release_arr['editall']){
						$dorelease += 3;
					}else{
						if($release_arr['edit']) $dorelease += 1;
						if($release_arr['put']) $dorelease += 2;
					}
					//var_dump($dorelease);exit;
					$model->dorelease= $dorelease;
				}
				//$model->dofriend =  $_POST['Role']['dofriend'][0] ? 1 : 0;
				$friend_arr = $_POST['Role']['dofriend'];
				if(1){
					$dofriend = 0;
					if($friend_arr['editall']){
						$dofriend += 3;
					}else{
						if($friend_arr['edit']) $dofriend += 1;
						if($friend_arr['put']) $dofriend += 2;
					}
					//var_dump($dofriend);exit;
					$model->dofriend= $dofriend;
				}
				//$model->dohappy =  $_POST['Role']['dohappy'][0] ? 1 : 0;
				$happy_arr = $_POST['Role']['dohappy'];
				if(1){
					$dohappy = 0;
					if($happy_arr['editall']){
						$dohappy += 7;
					}else{
						if($happy_arr['edit']) $dohappy += 1;
						if($happy_arr['create']) $dohappy += 2;
						if($happy_arr['upload']) $dohappy += 4;
					}
					//var_dump($dohappy);exit;
					$model->dohappy= $dohappy;
				}
				
				$find_arr = $_POST['Role']['dofind'];
				if(1){
					$dofind = 0;
					if($find_arr['statistic']){
						$dofind += 1;
					}
					//var_dump($dofind);exit;
					$model->dofind= $dofind;
				}
				//$model->donews =  $_POST['Role']['donews'][0] ? 1 : 0;
				$news_arr = $_POST['Role']['donews'];
				if(1){
					$donews = 0;
					if($news_arr['system']) $donews += 1;
					if($news_arr['to']) $donews += 2;
					if($news_arr['broadcastingLog']) $donews += 4;
					//var_dump($donews);exit;
					$model->donews= $donews;
				}
				//$model->dowebsite =  $_POST['Role']['dowebsite'][0] ? 1 : 0;
				//$model->dosystem =  $_POST['Role']['dosystem'][0] ? 1 : 0;
				$system_arr = $_POST['Role']['dosystem'];
				if(1){
					$dosystem = 0;					
					if($system_arr['user']) $dosystem += 1;
					if($system_arr['role']) $dosystem += 2;
					if($system_arr['password']) $dosystem += 4;
					if($system_arr['log']) $dosystem += 8;
					//var_dump($dosystem);exit;
					$model->dosystem= $dosystem;
				}
				
				$other_arr = $_POST['Role']['doother'];
				if(1){
					$doother = 0;
					if($other_arr['protocol']) $doother += 1;
					if($other_arr['industry']) $doother += 2;
					if($other_arr['version']) $doother += 4;
					if($other_arr['complain']) $doother += 8;
					if($other_arr['splash']) $doother += 16;
					//var_dump($doother);exit;
					$model->doother= $doother;
				}
				//$model->doleague =  $_POST['Role']['doleague'][0] ? 1 : 0;
				$league_arr = $_POST['Role']['doleague'];
				if(1){					
					$doleague = 0;
					if($league_arr['editall']){
						$doleague += 3;
					}else{
						if($league_arr['edit']) $doleague += 1;
						if($league_arr['put']) $doleague += 2;						
					}					
					//var_dump($doleague);exit;
					$model->doleague= $doleague;
				}
				$model->created_time =  time();
				if($model->save())
				$this->redirect($this->getBackListPageUrl());
			}
		}
				
	
		$this->render('update',array(
			'model'=>$model,
			'msg' => $msg,
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
		$this->insert_log(81);
		$model = Role::model();
		$cri = new CDbCriteria();
		$cri->order = "id desc";
		$pages = new CPagination();
		$pages->itemCount = $model->count($cri);
		$pages->pageSize = 12;
		$pages->applyLimit($cri);
		$items = $model->findAll($cri);
		$this->render('index',array('items'=>$items,'pages'=> $pages));
		
	}

	
	public function actionDelete($id)
	{
		$id = Frame::getIntFromRequest('id');
		if ($id > 0) {
			$model = Role::model ()->findByPk ( $id );
		}
		if ($model) {
			$model->delete();
		}
		$this->redirect ( Yii::app()->createUrl('role/index',array('page'=>intval($_REQUEST['page']))));
	}
	

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Role the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Role::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
	
	public function getBackListPageUrl()
	{
		return Yii::app()->createUrl("role/index",array('page'=>$_REQUEST['page']));
	}

	/**
	 * Performs the AJAX validation.
	 * @param Role $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='role-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
