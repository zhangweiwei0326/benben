<?php
class TestController extends Controller
{
	public $layout = false;
	/**
	 * 通讯录匹配
	 */
	public function actionMatch(){
		$phoneHeader = array(130, 131, 132, 133, 134, 135, 136, 137, 138, 139, 150, 153, 156, 157, 158, 159, 188, 189, 177);
		echo time()."<br />";
		if (1) {		
			$group = "朋友,家人,未分组";

			$phone = "15256978325#15656978325+小明|18608976789#18607968709+小花|13592753478+小学|18752753478+小小,18098908998+小米|13978569048+花花";
			
			$arr_group = array("朋友","家人","未分组");
			for ($j=0; $j < count($arr_group); $j++) { 
				$rndInfo = array();
				for ($i=0; $i < 400; $i++) { 
					$rndHeader = rand(0, count($phoneHeader)-1);
					$header = $phoneHeader[$rndHeader];
					$center = rand(1000, 9999);
					$footer = rand(1000, 9999);
					$rndInfo[] = $header.$center.$footer."+". $header.$center.$footer;
				}
				$arr_group_all[] = implode($rndInfo, "|");
			}
			$group_id = array();
			if(is_array($arr_group)){
				
				foreach ($arr_group as $val){
					$group_u = new GroupContact();
					$group_u->group_name = $val;
					$group_u->member_id = 8;
					$group_u->created_time = time();
					if($group_u->save()){    //保存分组信息
						$group_id[] = $group_u->id;																					
					}
				}
                $regall =array();
				if(is_array($arr_group_all)){
					for ($i=0;$i<count($arr_group_all);$i++){
						$phones = "";
						$reg = array();
						$arr_group_c = explode("|", $arr_group_all[$i]);//获取分组下的所有联系人	(号码+姓名)
						//var_dump($arr_group_c);				
						if(is_array($arr_group_c)){
							//拼接所有手机号							
							foreach ($arr_group_c as $contact_one){
								$contact_info = explode("+", $contact_one);//(号码#号码)
								//var_dump($contact_info[0]);
								
								$gri = new GroupContactInfo();
								$gri->group_id = $group_id[$i];
								$gri->name = $contact_info[1];
								$gri->created_time = time();
								$gri->save();
								
								$reg = array(
										"id" => $gri->id,
										"group_id" => $gri->group_id,
										"name" => $gri->name
								);
								
								$contact_sin = explode("#", $contact_info[0]);
								//var_dump($contact_sin);
								$grp = "";
								$arr_ben = array();
								$arr_baixing = array();
								$arr_poster = array();
								foreach ($contact_sin as $ab){
									$is_benben = 0;
									$is_baixing = 0;
									$re1 = Member::model()->find("phone = '{$ab}'");
									$re2 = Bxapply::model()->find("phone = '{$ab}'");
									
									if(!empty($re1)){
										$is_benben = 1;
									}
									if(!empty($re2)){
										$is_baixing = 1;
									}
									$grp .=  "(".$gri->id.", '".$ab."' , ".$is_benben." , ".$is_baixing."),";
									$arr_poster[] = $re1->poster;
									$arr_ben[] = $is_benben;
									$arr_baixing[] = $is_baixing;
								}
								$reg['phone'] = $contact_sin;
								$reg['is_benben'] = $arr_ben;
								$reg['is_baixing'] = $arr_baixing;
								$reg['poster'] = $arr_poster;
								$sqlgrp = "insert into group_contact_phone (contact_info_id,phone,is_benben,is_baixing) values {$grp}";
								$sqlgrp = trim($sqlgrp);
								$sqlgrp =trim($sqlgrp,',');
								//var_dump($sqlgrp);
								$connection = Yii::app()->db;
								$command = $connection->createCommand($sqlgrp);
								$result1 = $command->execute();
								//echo "--------------";
								//var_dump($reg);
								$regall[] = $reg;
								
							}	
						}
					}

				}
				
				//返回分组信息
				$groupinfo = GroupContact::model()->findAll();
				$allgroup = array();
				if($groupinfo){
					foreach ($groupinfo as $value){
						$allgroup[]=array(
								'id' => $value->id,
								'group_name' => $value->group_name);
					}

					$result ['ret_num'] = 0;
					$result ['ret_msg'] = '操作成功';
					$result['group'] = $allgroup;
					$result['contact'] = $regall;
					// echo json_encode( $result );
				}else{
					$result ['ret_num'] = 100;
					$result ['ret_msg'] = '操作失败';
					echo json_encode( $result );
				}																			
		  }			
		}
		echo time()."<br />";
	}

	public function actionMatch2(){
		$phoneHeader = array(130, 131, 132, 133, 134, 135, 136, 137, 138, 139, 150, 153, 156, 157, 158, 159, 188, 189, 177);
		$group = "朋友,家人,未分组";

		// $phone = "13978569048#15656978325+N1|18608976789#18607968709+N2|15256978325+N3,18752753478+N4,18098908998+N5|13978569048+N6";
		echo time()."<br />";
		$arr_group = array("0_0","*_*","-_-");
		for ($j=0; $j < count($arr_group); $j++) { 
			$rndInfo = array();
			for ($i=0; $i < 4; $i++) { 
				$rndHeader = rand(0, count($phoneHeader)-1);
				$header = $phoneHeader[$rndHeader];
				$center = rand(1000, 9999);
				$footer = rand(1000, 9999);
				$rndInfo[] = $header.$center.$footer."+". $header.$center.$footer;
			}
			$arr_group_all[] = implode($rndInfo, "|");
		}
		var_dump($arr_group_all);exit();
		$group_id = array();
		$return_group_info = array();
		if(is_array($arr_group)){
			$group_detail = array();
			$group_contact_info = array();
			$search_phone = array();
			foreach ($arr_group as $key => $val){
				$group_u = new GroupContact();
				$group_u->group_name = $val;
				$group_u->member_id = 9;
				$group_u->created_time = time();
				if($group_u->save()){    //保存分组信息
					$group_id[] = $group_u->id;	
					$return_group_info[] = array('id'=>$group_u->id, 'name'=>$val);																				
				}
				$currentDetail = explode("|", $arr_group_all[$key]);
				foreach ($currentDetail as $cKey => $cValue) {
					$person_info = explode("+", $cValue);var_dump($person_info);
					$group_contact_info[] = '('.$group_u->id.', "'.$person_info[1].'", '.time().')';
					$group_detail[$group_u->id][] = $person_info;
					$search_phone =  array_merge($search_phone, explode("#", $person_info[0]));
				}		
			}
			var_dump($search_phone);exit();
			//将姓名插入到数据库
			$sqlgrp = "insert into group_contact_info (group_id,name,created_time) values ".implode($group_contact_info, ",");
			$connection = Yii::app()->db;
			$command = $connection->createCommand($sqlgrp);
			$result = $command->execute();
			
			//查找出新增加的姓名，将姓名与ID之前的关系通过数据存放
			$criteria = new CDbCriteria;
			$criteria->select = 'id,group_id,name';
			$criteria->addInCondition('group_id', $group_id);
			$find_result = GroupContactInfo::model()->findAll($criteria);
			$relation_name_id = array();
			if (count($find_result)) {
				foreach ($find_result as $key => $value) {
					$relation_name_id[$value->name] = $value->id;
				}
			}

			//去查找是否是犇犇用户,并将用户信息通过数组关系存放
			$criteria2 = new CDbCriteria;
			$criteria2->select = 'id,phone, nick_name, poster';
			$criteria2->addInCondition('phone', $search_phone);
			$find_result2 = Member::model()->findAll($criteria2);
			$benben_phone_id = array();
			if (count($find_result2)) {
				foreach ($find_result2 as $key => $value) {
					$benben_phone_id[$value->phone] = array('id'=>$value->id, 'nick_name'=>$value->nick_name, 'poster'=>$value->poster);
				}
			}

			//去查找是否是百姓网用户,并将用户信息通过数组关系存放
			$criteria3 = new CDbCriteria;
			$criteria3->select = 'id,phone';
			$criteria3->addInCondition('phone', $search_phone);
			$criteria3->addCondition('status=3');
			$find_result3 = Bxapply::model()->findAll($criteria3);
			$baixing_phone_id = array();
			if (count($find_result3)) {
				foreach ($find_result3 as $key => $value) {
					$baixing_phone_id[$value->phone] = $value->id;
				}
			}

			//拼号码入库数据以及接口返回数据
			$insert_contact_phone = array();
			$return_person_info = array();
			foreach ($group_id as $key => $val){
				$currentDetail = explode("|", $arr_group_all[$key]);
				foreach ($currentDetail as $cKey => $cValue) {
					$person_info = explode("+", $cValue);
					$person_name = $person_info[1];
					$person_phone = explode("#", $person_info[0]);
					$return_phone_info = array();
					if (isset($relation_name_id[$person_name])) {
						$insert_info_id = $relation_name_id[$person_name];
						foreach($person_phone as $each_phone){
							$is_benben = 0;
							$is_baixing = 0;
							if (isset($baixing_phone_id[$each_phone])) {
								$is_baixing = 1;
							}
							if (isset($benben_phone_id[$each_phone])) {
								$is_benben = $benben_phone_id[$each_phone]['id'];
								$return_phone_info[] = array('phone'=>$each_phone, 'is_benben'=>$is_benben, 'is_baixing'=>$is_baixing, 'poster'=>$benben_phone_id[$each_phone]['poster'], 'nick_name'=>$benben_phone_id[$each_phone]['nick_name']);
							}else{
								$return_phone_info[] = array('phone'=>$each_phone, 'is_benben'=>$is_benben, 'is_baixing'=>$is_baixing, 'poster'=>'', 'nick_name'=>'');
							}
							
							$insert_contact_phone[] = '('.$insert_info_id.', "'.$each_phone.'", '.$is_benben.', '.$is_baixing.')';
						}	
					}
					$return_person_info[] = array('id'=>$insert_info_id, 'group_id'=>$val, 'name'=>$person_name, 'phone'=>$return_phone_info);
				}		
			}
			//将手机号码插入到数据库
			$sqlPhone = "insert into group_contact_phone (contact_info_id,phone,is_benben, is_baixing) values ".implode($insert_contact_phone, ",");
			$connection = Yii::app()->db;
			$command = $connection->createCommand($sqlPhone);
			$result = $command->execute();
			
		}
		
		$return['ret_num'] = 0;
		$return['ret_msg'] = '操作成功';
		$return['group'] = $return_group_info;
		$return['contact'] = $return_person_info;
		echo json_encode($return);
		echo "<br />".time()."<br />";
	}
}