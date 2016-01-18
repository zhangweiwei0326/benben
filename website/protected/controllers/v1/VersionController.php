<?php
class VersionController extends Controller{
	public $layout = false;
	/**
	 * 版本管理
	 */
	
	public function actionGetVersion(){
		$key = Frame::getStringFromRequest('key');
		Frame::appkey($key);		
		
		if($key == "iphone"){
			$version = Version::model()->find("type = 1");
		}else if($key == "android"){
			$version = Version::model()->find("type = 0");
		}
		$result = array();
		if($version){
			$result['ret_num'] = 0;
			$result['ret_msg'] = "OK";
			$result['version'] = array("version" => $version->version, "info" => $version->info, "path" => $version->path);
		}else{
			$result['ret_num'] = 3000;
			$result['ret_msg'] = '暂无版本管理数据';
		}
		
		echo json_encode($result);
		
		
	}
}