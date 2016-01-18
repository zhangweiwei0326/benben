<?php
class SplashController extends PublicController{
	public $layout = false;
	/**
	 * 开机页面
	 */
	
	public function actionGetSplash(){
		$this->check_key();
		
		$cri = new CDbCriteria();
		$cri->order = "created_time desc";
		$cri->limit = "1";
		$splash = Splash::model()->find($cri);
		$result = array();
		if(!$splash){
			$result ['ret_num'] = 3000;
			$result ['ret_msg'] = '获取开机页面失败';
		}else{
			$result ['ret_num'] = 0;
			$result ['ret_msg'] = 'OK';
			$result['splash'] = Yii::app()->request->getHostInfo().$splash->image;
		}
		echo json_encode($result);
	}
}