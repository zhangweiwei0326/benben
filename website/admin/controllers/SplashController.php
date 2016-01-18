<?php

class SplashController extends BaseController
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/admin';
	
	/**
	 * @var int the define the index for the menu
	 */
	 
	 public $menuIndex = 74;
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Splash;
		if(isset($_POST['Splash']))
		{
			$name = trim($_POST['Splash']['name']);
			$image = trim($_POST['Splash']['image']);
			if($name == "" || $image == ""){
				$back = -2;
				$msg = "图片名称和图片路径不能为空！";
			}else{
				$model->name=$_POST['Splash']['name'];
				$model->image = $_POST['Splash']['image'];
				$model->created_time = time();
				if($model->save())
					$this->redirect($this->getBackListPageUrl());
			}
		}

		$this->render('create',array(
			'model'=>$model,
			'msg' => $msg,
			'back' => $back,
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

		if(isset($_POST['Splash']))
		{
			$name = trim($_POST['Splash']['name']);
			$image = trim($_POST['Splash']['image']);
			if($name == "" || $image == ""){
				$back = -2;
				$msg = "图片名称和图片路径不能为空！";
			}else{
				@unlink (Yii::getPathOfAlias('webroot').$model->image); 
				$model->name=$_POST['Splash']['name'];
				$model->image = $_POST['Splash']['image'];
				$model->created_time = time();
				if($model->save())
					$this->redirect($this->getBackListPageUrl());
			}
		}

		$this->render('update',array(
			'model'=>$model,
			'msg' => $msg,
			'update' => 'update',
			'back' => $back,
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
		$this->insert_log(74);
		$model = Splash::model();
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
			$model = Splash::model ()->findByPk ( $id );
		}
		if ($model) {
			@unlink (Yii::getPathOfAlias('webroot').$model->image); 
			$model->delete();
		}
		$this->redirect ( Yii::app()->createUrl('splash/index',array('page'=>intval($_REQUEST['page']))));
	}
	

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Splash the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Splash::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
	
	public function getBackListPageUrl()
	{
		return Yii::app()->createUrl("splash/index",array('page'=>$_REQUEST['page']));
	}

	/**
	 * Performs the AJAX validation.
	 * @param Splash $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='splash-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
	public function actionUpload()
	{
		 	$base64 = $_POST['formFile'];
            $IMG = base64_decode($base64);
//            $is_head = $_POST['head'];
//            if ($is_head) {
//                $str = 'head/';
//            } else {
//                $str = 'images/';
//            }
			$str = date('Y-m-d', time()).'/';
			$tempFolder= Yii::getPathOfAlias('webroot').'/uploads/'.$str;
			if(!is_dir($tempFolder)){
				mkdir($tempFolder, 0777, TRUE);
			}
			
            if ($_POST['name']) {
                $exe = explode('.', $_POST['name']);
                $fn = generateNonceStr() . '.' . $exe[1];
            } else {
                $fn = false;
            }
            
            
            $file = Yii::getPathOfAlias('webroot').'/uploads/'.$str . $fn;
           
            file_put_contents(
                $file, $IMG
            );
            echo '/uploads/'.$str . $fn;
        }

    }
    
     function generateNonceStr($length = 16) {
        // 密码字符集，可任意添加你需要的字符
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $str;
		
	}

