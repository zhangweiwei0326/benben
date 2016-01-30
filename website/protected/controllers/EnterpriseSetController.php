<?php
class EnterpriseSetController extends EnterpriseBaseController {
	
	/**
	 * 政企设置
	 */
	public function actionIndex() {
		$applyInfo = ApplyRegister::model ()->findByPk ( $this->apply_id );
		$enterpriseRole = EnterpriseRole::model()->find('enterprise_id='.$this->enterprise_id);

		if(Yii::app()->request->isAjaxRequest){
			
			//申请修改政企信息～
			$eName=Frame::getStringFromArray($_POST, enterprise_name);
			$eName=htmlspecialchars($eName);
			$login_name=Frame::getStringFromArray($_POST, login_name);
			$login_name=htmlspecialchars($login_name);
			$email=Frame::getStringFromArray($_POST, email);
			$email=htmlspecialchars($email);
			$hadnameResult=ApplyRegister::model()->findByAttributes(array('login_name'=>$login_name));
			if($hadnameResult&&$login_name!=$applyInfo->login_name){
				echo 205;die();
			}
			if($eName){
				$applyInfo->enterprise_name=$eName;
		        $applyInfo->login_name=$login_name;
		        $applyInfo->email=$email;
		        if(!empty($this->enterprise_id)){
		        	$enterpriseInfo=Enterprise::model()->findByPk($this->enterprise_id);
		        	$enterpriseInfo->name=$eName;
		        }
				if($applyInfo->save()&&$enterpriseInfo->save()){
						echo 1;		
				}
				die();
			}
			//修改密码
			$old_pw=Frame::getStringFromArray($_POST, old_pw);
			$old_pw=htmlspecialchars($old_pw);
			$new_pw=Frame::getStringFromArray($_POST, new_pw);
			$new_pw=htmlspecialchars($new_pw);
			$re_pw=Frame::getStringFromArray($_POST, re_pw);
			$re_pw=htmlspecialchars($re_pw);		
			if($old_pw){
				if(md5($old_pw)==$applyInfo->login_password){
				$applyInfo->login_password=md5($new_pw);
				if($applyInfo->save()){
					echo 1;
				}
			}else{
						echo 0;						
				}
			die();	
		}
		
		//修改权限
		$enterprise_apply=intval(Frame::getStringFromArray($_POST, enterprise_apply));
		$member_add=intval(Frame::getStringFromArray($_POST, member_add));
		$access_level=intval(Frame::getStringFromArray($_POST, access_level));
		$member_add_other=intval(Frame::getStringFromArray($_POST, member_add_other));
		if($enterprise_apply){
			$enterpriseRole->enterprise_apply=$enterprise_apply;
			$enterpriseRole->member_add=$member_add;
			$enterpriseRole->member_add_other=$member_add_other;
			$enterpriseRole->access_level=$access_level;
			if($enterpriseRole->save()){
				echo 1;
			}else{
						echo 0;						
				}
			die();	
		}
		
		//解散政企
		$disband=Frame::getStringFromArray($_POST, disband);
		if($disband){
			$model1=ApplyRegister::model();
			$model2=Enterprise::model();
			$model3=EnterpriseMember::model();
			$re1=$model1->deleteByPk($this->apply_id);
			$re2=$model2->deleteByPk($this->enterprise_id);
			$re3=$model3->deleteAll('contact_id='.$this->enterprise_id);
			if($re1){
				echo 1;
			}else{
						echo 0;						
				}
			die();	
		}
	}
		
		$this->render ( "index", array (
				'applyInfo' => $applyInfo ,
				'enterpriseRole'=>$enterpriseRole
		) );
	}



	
	
}