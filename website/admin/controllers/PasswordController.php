<?php

class PasswordController extends BaseController
{
	public $layout='//layouts/admin';
	 public $menuIndex = 82;
	public function actionIndex()
	{
		$this->insert_log(82);
		$user = $this->loadModel($this->getLoginId());
		$password=Frame::getStringFromRequest('password');
		$repassword=Frame::getStringFromRequest('repassword');
		$old_password=Frame::getStringFromRequest('oldpassword');
		$msg="";
		$result="";
		$pass = array();
		if(isset($_POST['password'])){
			if(md5($old_password) != $user->password){
				$msg = "请输入正确的旧密码！";
			}else if ($password == ""){
				$msg = '密码不得为空！';
			}else if($password != $repassword){
				$msg = '两次密码输入不一致！';
			}else{
				$user->password = md5($_POST['password']);
				if($user->save())
					$msg = "密码修改成功！";
					$result = "success";
			}
		}
		
		
		$this->render('index', array('msg' => $msg, 'result' => $result));
	}
	
	public function loadModel($id)
	{
		$model=User::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
}