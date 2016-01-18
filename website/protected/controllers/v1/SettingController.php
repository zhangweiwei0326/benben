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
			if($type==5){
				$this->render("help");
			}else {
				$this->render("registerprotocol", array(
					"content" => $re->content
				));
			}
		}
	}

	public function actionSpecial1(){
		$key = Frame::getStringFromRequest('key');
		Frame::appkey($key);
		$this->render("special1");
	}
	public function actionSpecial2(){
		$key = Frame::getStringFromRequest('key');
		Frame::appkey($key);
		$this->render("special2");
	}
	public function actionSpecial3(){
		$key = Frame::getStringFromRequest('key');
		Frame::appkey($key);
		$this->render("special3");
	}
	public function actionSpecial4(){
		$key = Frame::getStringFromRequest('key');
		Frame::appkey($key);
		$this->render("special4");
	}
	public function actionSpecial5(){
		$key = Frame::getStringFromRequest('key');
		Frame::appkey($key);
		$this->render("special5");
	}
	public function actionSpecial6(){
		$key = Frame::getStringFromRequest('key');
		Frame::appkey($key);
		$this->render("special6");
	}
	public function actionSpecial7(){
		$key = Frame::getStringFromRequest('key');
		Frame::appkey($key);
		$this->render("special7");
	}
	public function actionSpecial8(){
		$key = Frame::getStringFromRequest('key');
		Frame::appkey($key);
		$this->render("special8");
	}
	public function actionSpecial9(){
		$key = Frame::getStringFromRequest('key');
		Frame::appkey($key);
		$this->render("special9");
	}
	public function actionSpecial10(){
		$key = Frame::getStringFromRequest('key');
		Frame::appkey($key);
		$this->render("special10");
	}
	public function actionSpecial11(){
		$key = Frame::getStringFromRequest('key');
		Frame::appkey($key);
		$this->render("special11");
	}
	
}