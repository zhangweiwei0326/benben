<?php
class ComplainController extends PublicController{
	public $layout = false;
	/**
	 * 投诉建议
	 */

	public function actionPostComplain(){
		$user = $this->check_user();
		$info = Frame::getStringFromRequest('info');
		$this->check_key();

		$result = array();

		if(empty($info)){
			$result['ret_num'] = 3001;
			$result['ret_msg'] = '反馈内容为空';
		}else{
			$complain = new Complain();
			$complain->member_id = $user->id;
			$complain->info = $info;
			$complain->created_time = time();

			if($complain->save()){
				$result['ret_num'] = 0;
				$result['ret_msg'] = 'OK';
			}else{
				$result['ret_num'] = 3002;
				$result['ret_msg'] = '反馈失败';
			}
		}
		
		echo json_encode($result);
	}

}