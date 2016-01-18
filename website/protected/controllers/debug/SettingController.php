<?php
class SettingController extends Controller
{
	public $layout = false;
	/**
	 * 注册协议
	 */
	public function actionRegisterprotocol(){
		$key = Frame::getStringFromRequest('key');
		$type = Frame::getIntFromRequest('type');
		Frame::appkey($key);
		
		switch ($type){
			case 1:
				$re = Protocol::model()->find("type = 1");
				break;
			case 2:
				$re = Protocol::model()->find("type = 2");
				break;
			case 3:
				$re = Protocol::model()->find("type = 3");
				break;
			case 4:
				$re = Protocol::model()->find("type = 4");
				break;
			case 5:
				$re = Protocol::model()->find("type = 5");
				break;
			case 6:
				$re = Protocol::model()->find("type = 6");
				break;
		}
		
		
		if($re){
			$this->render("registerprotocol",array(
				"content"=>$re->content
			));
		}
	}
	
}