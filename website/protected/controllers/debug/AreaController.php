<?php
class AreaController extends PublicController{
	public $layout = false;
	/**
	 * 省市
	 */
	public function actionGetArea(){
		$this->check_key();
		
		$bid = Frame::getIntFromRequest('bid');
			
		$cri = new CDbCriteria();
		$cri->select = "bid, parent_bid, area_name, level";
		$cri->addCondition('parent_bid = '.$bid);
		$cri->order = "bid asc";
		$area = Area::model()->findAll($cri);
		$res = array();
		if($area){
			$res['ret_num'] = 0;
			$res['ret_msg'] = "OK";
			foreach ($area as $value){
				$temp = array("bid" => $value->bid, "parent_bid" => $value->parent_bid,
										 "level" => $value->level, "area_name" => $value->area_name);
				$res['area'][] = $temp;
			}
		}else{
			$res['ret_num'] = 3000;
			$res['ret_msg'] = "没有更多数据了";
		}
		
		echo $res = json_encode($res);
	}

}