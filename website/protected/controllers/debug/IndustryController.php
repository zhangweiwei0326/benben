<?php
class IndustryController extends PublicController{
	public $layout = false;
	/**
	 * 行业字典
	 */
	
	public function actionGetIndustry(){
		$this->check_key();
		//$pid = Frame::getIntFromRequest('pid');
		
		$re = Industry::model()->findAll("parent_id = 0");
		$res = array();
		$res['ret_num'] = 0;
		$res['ret_msg'] = '操作成功';
		
// 		if($pid){
// 			$level = 2;
// 		}else{
// 			$level = 1;
// 		}
		
		
		foreach ($re as $value){
			$temp = array("id" => $value->id, "parent_id" => $value->parent_id, "name" => $value->name);
			$res['industry'][] = $temp;
		}
		
		echo json_encode($res);
		
		
		
	}
	
}