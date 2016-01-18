<?php
class HappyController extends  PublicController{
	public $layout = false;
	
	/**
	 * 开心一刻
	 */
	public  function actionHappyList(){
		$user = $this->check_user();
		$loginId = $user->id;

		$this->check_key();

		$last_time = Frame::getStringFromRequest("last_time");
		
		$cri = new CDbCriteria();
		if($last_time){
			$cri->addCondition("t.created_time <".$last_time);
		}
		$cri->select = "t.*, happy_like.status as hstatus";
		$cri->join = "left join happy_like on happy_like.happy_id = t.id and happy_like.member_id = $loginId";
		$cri->order = "t.created_time desc";
		$cri->limit = "1";
		
		$happy = Happy::model()->findAll($cri);
		$result = array();
		if($happy){
			foreach ($happy as $value){
				$temp = array("id" => $value->id, "description"=>$value->description, 
											"status" => $value->hstatus ?  $value->hstatus : "0",
											"created_time" => $value->created_time);				
			}
			$result ['ret_num'] = 0;
			$result ['ret_msg'] = '操作成功';
			$result['happy'] = $temp;
		}else{
			$result ['ret_num'] = 3000;
			$result ['ret_msg'] = '没有数据';
		}
		
		echo json_encode($result);
	}


	/**
	 * 开心一刻
	 */
	public  function actionNewHappynext(){
		$this->check_key();
		$user = $this->check_user();
		$loginId = $user->id;

		$daycount = 60;

		$lastid = Frame::getStringFromRequest("lastid");
		
		$count = Happy::model()->count();
		if ($count > $daycount) {
			//总是从最新的xx条开始
			$maxcri = new CDbCriteria();
			$maxcri->select = "id";
			$maxcri->order = "id desc";
			$maxcri->limit = 1;
			$maxcri->offset = $daycount;
			$maxModel = Happy::model()->find($maxcri);
			$maxid = $maxModel ? $maxModel->id : 0;
			$lastid = max($lastid, $maxid);
		}

		$cri = new CDbCriteria();
		$cri->addCondition("id >".$lastid);
		$cri->order = "id asc";
		$cri->limit = "1";
		$happy = Happy::model()->find($cri);
		$result = array();
		if($happy){
			$temp = array("id" => $happy->id, 
					"description"=>$happy->description,
					"status" => "0",
					"created_time" => $happy->created_time);

			$result ['ret_num'] = 0;
			$result ['ret_msg'] = '操作成功';
			$result['happy'] = $temp;
		}else{
			$result ['ret_num'] = 3000;
			$result ['ret_msg'] = '最后一条了，明天继续';
		}
		
		echo json_encode($result);
	}
	
	/**
	 * 开心一刻下一篇
	 */
	public  function actionHappynext(){
		$this->check_key();
		$user = $this->check_user();
		$loginId = $user->id;				
		$last_time = Frame::getStringFromRequest("last_time");
	
		$cri = new CDbCriteria();
		if($last_time){
			$cri->addCondition("t.created_time <".$last_time);
		}
		$cri->select = "t.*, happy_like.status as hstatus";
		$cri->join = "left join happy_like on happy_like.happy_id = t.id and happy_like.member_id = $loginId";
		$cri->order = "t.created_time desc";
		$cri->limit = "1";
	
		$happy = Happy::model()->findAll($cri);
		$result = array();
		if($happy){
			foreach ($happy as $value){
				$temp = array("id" => $value->id, "description"=>$value->description,
						"status" => $value->hstatus ?  $value->hstatus : "0",
						"created_time" => $value->created_time);
				
			}
			$result ['ret_num'] = 0;
			$result ['ret_msg'] = '操作成功';
			$result['happy'] = $temp;
		}else{
			$result ['ret_num'] = 3000;
			$result ['ret_msg'] = '最后一条了，明天继续';
		}
	
		echo json_encode($result);
	}
	
	/**
	 * 点赞或者吐槽
	 * Enter description here ...
	 */
	public function actionGoodOrBad(){
		$this->check_key();
		$userId = $this->check_user()->id;

		$happy_id = Frame::getIntFromRequest("happy_id");
		$status = Frame::getIntFromRequest("status");
		
		$result = array();
		if(empty($happy_id )|| empty($status)){
			$result ['ret_num'] = 3002;
			$result ['ret_msg'] = '非法操作';
			echo json_encode($result);
			die;
		}
		
		$model = new HappyLike();
		
		$cri = new CDbCriteria();
		$cri->addCondition("happy_id = ".$happy_id);
		$cri->addCondition("member_id = ".$userId);
		
		if($model->find($cri)){
// 			$model->status = $status;
// 			$model->update();
			$result ['ret_num'] = 3006;
			$result ['ret_msg'] = '不能重复操作';
			echo json_encode($result);
			die;
		}
		
		$model->happy_id = $happy_id;
		$model->member_id = $userId;
		$model->status = $status;
		$model->created_time = time();
		
		if($model->save()){
			$result ['ret_num'] = 0;
			$result ['ret_msg'] = '操作成功';
		}else{
			$result ['ret_num'] = 3001;
			$result ['ret_msg'] = '操作失败';
		}
		echo json_encode($result);
	}
	
	
	
}
