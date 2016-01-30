<?php
class EnterpriseNewsController extends EnterpriseBaseController{
	

// 	public $news_type1=1;     //消息类型：政企系统消息
// 	public $news_type2=4;     //消息类型：政企请求通知
	/**
	 * 政企消息中心
	 * */
	public function actionIndex(){

		
		$model = News::model();
		//系统消息
		$cri = new CDbCriteria();
		$cri->addCondition("t.type = 1",'AND');
		$cri->addCondition("t.member_id =0 ",'AND');

		$newsStatus =$_GET['newsStatus'];
		$result=$newsStatus;
		if(!empty($newsStatus)){
			switch($newsStatus){
						case "11":  $status="0 or t.status=1";break;
						case "0": $status="0";break;
						case "1": $status="1";break;
						case "2": $status="2";break;
						case "3": $status="3";break;
						default:$status="0";
					}
			$cri->addCondition("t.status = ".$status,'AND');
		}else{
				$cri->addCondition("t.status = 0",'AND');
		}
		$cri->order = "id desc";	
		$pages = new CPagination();
		$pages->itemCount = $model->count($cri);
		$pages->pageSize = 18;
		$pages->applyLimit($cri);
		$items = $model->findAll($cri);
		if (Yii::app()->request->isAjaxRequest&&!empty($_POST['newsIds'])) {
					//删除消息记录
			$newsId = Frame::getArrayFromArray($_POST, newsIds);
			if(isset($newsId)){
				
				$tmp = array();
				foreach ($newsId as $val){
					$tmp[] = intval($val);
				}
				$idCon = "(".implode(',', $tmp).")";
				$re=$model->deleteAll('id in '.$idCon);
				if($re){
					echo 1;
				}else{
					echo 0;
				}
				exit();

		
			}
	    }
		$this->render('index',array(
				'items'=>$items,
				'pages'=> $pages,
				'result'=> $result
		));
		
			
	
	}
	
	/**
	 * 政企请求通知
	 * */
	public function actionNotice(){
	

		
		$model = News::model();
		//请求通知
		if(!empty($this->enterprise_id)){
			$cri1= new CDbCriteria();
			$cri1->addCondition("t.type = 4",'AND');
			$cri1->addCondition("t.identity1 = ".$this->enterprise_id,'AND');
			$newsStatus =$_GET['newsStatus'];
			$result=$newsStatus;
			if(!empty($newsStatus)){
				switch($newsStatus){
							case "11":  $status="0 or t.status=1 or t.status=2 or t.status=3";break;
							case "0": $status="0";break;
							case "1": $status="1";break;
							case "2": $status="2";break;
							case "3": $status="3";break;
							default:$status="0";
						}
				$cri1->addCondition("t.status = ".$status,'AND');
			}else{
					$cri1->addCondition("t.status = 0",'AND');
			}
			$cri1->select="t.*,m.nick_name";
			$cri1->order = "id desc";
			$cri1->join='left join Member as m on t.sender =m.id';
			$pages1 = new CPagination();
			$pages1->itemCount = $model->count($cri1);
			$pages1->pageSize = 18;
			$pages1->applyLimit($cri1);
			$items1 = $model->findAll($cri1);
// 			p($pages1);die();
		}else{
			$items1=array();
			$pages1=array();
		}

		if (Yii::app()->request->isAjaxRequest) {
			$currId=intval(Frame::getStringFromArray($_POST, currId));
			$news=$this->loadModel($currId);
			$member_id=$news->sender;
			$review=Frame::getStringFromArray($_POST, review);	
			
			$memberModel=Member::model();
			$member1Model=new EnterpriseMember();
			if(isset($review)){
				if($review=="agree"){
					$news->status=2;
					
					$member=$memberModel->findByPk($member_id);
					$member1Model->contact_id=$this->enterprise_id;
					$member1Model->member_id=$member->id;
					$member1Model->short_phone=$member->cornet;
					$member1Model->remark_name=$member->nick_name;
					$member1Model->created_time=time();
					$member1Model->phone=$member->phone;
					$member1Model->name=$member->name;
					$member1Model->firstin=1;

// 发送环信
					$content="恭喜，您已通过申请！";
					$huanxin_username=$member->huanxin_username;
					$username=array('0'=>$huanxin_username);
					$enterprise_id=$news->identity1;
					$enterprise_name=Enterprise::model()->findByPk($enterprise_id)->name;
					
					$arr=array('t1'=>1,'t2'=>1,'t3'=>1,'t4'=>7,'t5'=>$enterprise_id,'t6'=>$enterprise_name,'t7'=>time());
					Frame::sendHXMessage($username, $content, $arr, $from_user = "admin");
					if($news->save()&&$member1Model->save()){
							
						$this->redirect($this->getBackListPageUrl());
					}
				}elseif($review=="disagree"){
					$news->status=3;		
					if($news->save()){		
						$this->redirect($this->getBackListPageUrl());
					}
				}
			}
		
			//删除消息记录
			$newsId = Frame::getArrayFromArray($_POST, newsIds);
			if(isset($newsId)){
				
				$tmp = array();
				foreach ($newsId as $val){
					$tmp[] = intval($val);
				}
				$idCon = "(".implode(',', $tmp).")";
				$re=$model->deleteAll('id in '.$idCon);			
				if($re){
					echo 1;
				}else{
					echo 0;
				}
				exit();
		
			}
		}
		
		$this->render('notice',array(
				'items1'=>$items1,
				'pages1'=> $pages1,
				'result'=> $result,
// 				'items2'=> $items2
		));
	
			
	
	}
	
	public function getBackListPageUrl()
	{
		return Yii::app()->createUrl("enterpriseNews/notice",array('page'=>$_REQUEST['page']));
	}
	
	public function loadModel($id)
	{
		$model=News::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
// 	public function actionDelete($id)
// 	{
// 		$id = Frame::getIntFromRequest('id');
// 		if ($id > 0) {
// 			$model = News::model ()->findByPk ( $id );
// 		}
// 		if ($model) {
// 			$model->delete();
// 		}
// 		$this->redirect ( Yii::app()->createUrl('EnterpriseNews/index',array('page1'=>intval($_REQUEST['page1']))));
// 	}

	
}
