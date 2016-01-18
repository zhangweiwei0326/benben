<?php
class IndustryController extends PublicController{
	public $layout = false;
	/**
	 * 行业字典
	 */
	
	public function actionGetIndustry(){
		$this->check_key();
		$time = Frame::getStringFromRequest('time');
		$rest = Industry::model()->find("level!=0 order by created_time desc");
		if(empty($time) && $time!=="0"){
			$res['ret_num'] = 100;
			$res['ret_msg'] = '缺少参数';
			echo json_encode($res);
			die();
		}
		if($rest['created_time']<=$time){
			$res['ret_num'] = 1000;
			$res['ret_msg'] = '无需更新';
			echo json_encode($res);
			die();
		}
		$re = $this->newindustryinfo();
		$res = array();
		$res['ret_num'] = 0;
		$res['ret_msg'] = '操作成功';
		
// 		if($pid){
// 			$level = 2;
// 		}else{
// 			$level = 1;
// 		}
		
		
		foreach ($re as $value){
//			$temp = array("id" => $value->id, "parent_id" => $value->parent_id, "name" => $value->name);
//			$res['industry'][] = $temp;
			$res['industry'][] = $value;
		}
		
		echo json_encode($res);

	}
	
}