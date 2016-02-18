<?php
class EnterpriseAboutController extends EnterpriseBaseController{
	
	
	/**
	 * 投诉建议
	 * */
	public function actionAdvice(){
		$model=new Complain;
		$menuIndex = 1;
// 		var_dump($this->administrator_id);die();
		if(Yii::app()->request->isAjaxRequest){
			$content=Frame::getStringFromArray($_POST,content);
// 			var_dump($content);die();
			$model->info=htmlspecialchars($content);
			$model->created_time=time();
			if($this->administrator_id){
				$model->member_id=$this->administrator_id;
			}else{
				$model->apply_id=$this->apply_id;
			}
			if($model->save()){
				echo 1;
			}else{
				echo 0;
			}
			die();
		}
		$this->render(advice,array('model'=>$model,'menuIndex'=>$menuIndex));	
	}
	
	
	/**
	 * 关于我们
	 * */
	public function actionAboutus(){
		$model=Protocol::model();
		$menuIndex = 1;
//犇犇介绍$items1  法律声明$items2 联系我们$items3
		$items1=$model->findByPk(3);
		$items2=$model->findByPk(4);
		$items3=$model->findByPk(8);
		$content3=$items3->content;
		$content1=$items1->content;
// 		var_dump($content3);
// 		die();
		preg_match_all('/\d{4}-\d{8}|\d{4}-\{7,8}/', $content3,$email);
		preg_match_all('/\d{4}-\d{8}|\d{4}-\{7,8}/', $content3,$phone);
// 		$aa=preg_match('/^([0-9]{3,4}-)?[0-9]{7,8}$/', $content3,$phone);
// 		var_dump($phone);die();
		$this->render(aboutus,array(
				'item1'=>$items1,
				'item2'=>$items2,
				'item3'=>$items3,
				'content3'=>$content3,
				'content1'=>$content1,
				'menuIndex'=>$menuIndex
		));
		
		
	}
	
	
	
	
	
	
}