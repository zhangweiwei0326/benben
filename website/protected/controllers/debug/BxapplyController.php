<?php
class BxapplyController extends PublicController
{
	public $layout = false;
	/**
	 * 自己加入百姓网
	 */
	public function actionJoin(){
		$user = $this->check_user();
		$this->check_key();
		$phone = Frame::getStringFromRequest('phone');
		$name = Frame::getStringFromRequest('name');
		
		$province = Frame::getIntFromRequest('province');
		$city = Frame::getIntFromRequest('city');
		$area = Frame::getIntFromRequest('area');
		$street = Frame::getIntFromRequest('street');
		
		$id_card = Frame::getStringFromRequest('id_card');
		$poster1 = Frame::saveImage('poster1');
		$poster2 = Frame::saveImage('poster2');
		
		if (empty( $name )) {
			$result['ret_num'] = 1105;
			$result['ret_msg'] = '姓名为空';
			echo json_encode( $result );
			die ();
		}
		if (empty( $id_card )) {
			$result['ret_num'] = 1005;
			$result['ret_msg'] = '证件号为空';
			echo json_encode( $result );
			die ();
		}
		if (empty( $poster1 )) {
			$result['ret_num'] = 1006;
			$result['ret_msg'] = '身份证正面照没有上传';
			echo json_encode( $result );
			die ();
		}
		if (empty( $poster2 )) {
			$result['ret_num'] = 1007;
			$result['ret_msg'] = '身份证反面照没有上传';
			echo json_encode( $result );
			die ();
		}
		
// 		$info = Bxapply::model()->find("phone = {$user->phone} and member_id = {$user->id}");
		$info = Bxapply::model()->find(" phone = {$user->phone} and status<>4");//不是已撤销的
		$update_flag = false;
		if($info){
			if($info->status == 1){
				//未通过，不能再申请
				$result['ret_num'] = 1267;
				$result['ret_msg'] = '您不符合加入百姓网条件';
				echo json_encode( $result );
				die ();
			}else if ($info->status == 3){
				//已通过
				$result['ret_num'] = 1227;
				$result['ret_msg'] = '你已经是百姓网用户，可以通过“将好友加入百姓网”提交好友';
				echo json_encode( $result );
				die ();
			}else if ($info->status == 0){
				//审核中，不能再申请
				$result['ret_num'] = 1268;
				$result['ret_msg'] = '资料正在审核中，不能修改';
				echo json_encode( $result );
				die ();
			}else {
				//2=退回重审
				$info->member_id = $user->id;
				$info->phone = $phone;
				$info->name = $name;
				$info->province = $province;
				$info->city = $city;
				$info->area = $area;
				$info->street = $street;
				$info->created_time = time();
				$update_flag = $info->update();
			}	
		}else{
			//不存在或者被撤销
			$info = new Bxapply();
			$info->member_id = $user->id;
			$info->phone = $phone;
			$info->name = $name;
			$info->province = $province;
			$info->city = $city;
			$info->area = $area;
			$info->street = $street;
			$info->created_time = time();
			$update_flag = $info->save();
		}
				
		if($update_flag){
			$this->addIntegral($user->id, 1);
			
			$applyc = ApplyComplete::model()->find("phone = {$phone} and type = 1");
			if (!$applyc) {
				$applyc = new ApplyComplete();
			}
			$applyc->apply_id = $info->id;
			$applyc->id_card = $id_card;
			$applyc->poster1 = $poster1;
			$applyc->poster2 = $poster2;
			$applyc->type = 1;
			$applyc->member_id = $info->member_id;
			$applyc->province = $province;
			$applyc->phone = $phone;
			$applyc->city = $city;
			$applyc->area = $area;
			$applyc->street = $street;
			$applyc->created_time = time();
			$applyc->name = $name;
			if($applyc->save()){
				//添加直通车资料
				if(($user->userinfo & 1) == 0){ 
					$apply_info = ApplyComplete::model()->find("member_id={$user->id} and type = 2");
					if (!$apply_info) {
						$apply_info = new ApplyComplete();
					}
					$apply_info->name = $name;
					//$apply_info->apply_id = 0;
					$apply_info->id_card = $id_card;
					$apply_info->member_id = $user->id;
					$apply_info->poster1 = $poster1;
					$apply_info->poster2 = $poster2;
					$apply_info->phone = $phone;
					$apply_info->province = $province;
					$apply_info->city = $city;
					$apply_info->area = $area;
					$apply_info->street = $street;
					$apply_info->type = 2;
					$apply_info->created_time = time();
					if($apply_info->save()){
						$user->userinfo = $user->userinfo + 1;
						$user->update();
					}
				}
				$user->name = $name;
				$user->province = $province;
				$user->city = $city;
				$user->area = $area;
				$user->street = $street;
				$user->userinfo = $user->userinfo + 2;
				$user->update();
				$result ['ret_num'] = 0;
				$result ['ret_msg'] = '操作成功';
				//$result ['number_info'] = $result0;
			}else{
				$result ['ret_num'] = 1205;
				$result ['ret_msg'] = '申请失败';
			}	
		}	
		echo json_encode( $result );
	}
	
	/**
	 * 获取百姓网资料
	 */
	public function actionGetinfo(){
		$this->check_key();
		$phone = Frame::getStringFromRequest('phone');
		$user = $this->check_user();
		if (empty( $phone )) {
			$phone = $user->phone;
			if(!$phone){
				$result['ret_num'] = 2125;
				$result['ret_msg'] = '百姓网手机号码为空';
				echo json_encode( $result );
				die ();
			}		
		}
		
		$baixing = Bxapply::model()->find("phone = '{$phone}' and status<>4");
		if (empty( $baixing )) {
			$result['ret_num'] = 2126;
			$result['ret_msg'] = '百姓网不存在';
			echo json_encode( $result );
			die ();
		}
		//$pinfo = $this->pcinfo();
		$result1 = array();
		$connection = Yii::app()->db;
		$sql = "select a.id,a.phone,a.short_phone, a.name,a.status,a.province,a.city,a.area,a.street,b.id_card,b.poster1,b.poster2, c.reason from bxapply a left join apply_complete b on a.id = b.apply_id  left join bxapply_record c on a.id = c.apply_id where a.phone = '{$phone}' and a.status<>4 order by c.id desc";
		$command = $connection->createCommand($sql);
		$result1 = $command->queryAll();
		//省市
		$pinfo = $this->ProCity($result1);
		foreach ($result1 as $key => $value){
			$result1[$key]['poster1'] = $value['poster1'] ? URL.$value['poster1'] : "";
			$result1[$key]['poster2'] = $value['poster2'] ? URL.$value['poster2'] : "";
			$result1[$key]['reason'] = ($value['status'] == 1 || $value['status'] == 2)?$this->eraseNull($value['reason']):"";
			$result1[$key]['pro_city'] = $pinfo[$result1[$key]['province']]." ".$pinfo[$result1[$key]['city']];
		}
		if(isset($result1[0])){
			$result1[0]['short_phone'] = $result1[0]['short_phone'] ? $result1[0]['short_phone'] : "";
			$result1[0]['id_card'] = $result1[0]['id_card'] ? $result1[0]['id_card'] : "";
			if ($result1[0]['id_card'] && $result1[0]['status'] != 2) {
				$result1[0]['id_card'] = substr($result1[0]['id_card'], 0, 6)."******".substr($result1[0]['id_card'], strlen($result1[0]['id_card'])-4);
			}
			if ($result1[0]['phone'] && $result1[0]['status'] != 2) {
				$result1[0]['phone'] = substr($result1[0]['phone'], 0, 4)."***".substr($result1[0]['phone'], strlen($result1[0]['phone'])-4);
			}
		}
		$result['ret_num'] = 0;
		$result['ret_msg'] = '操作成功';
		$result['info'] = $result1[0];
		echo json_encode( $result );
	}
	
	/**
	 * 获取百姓网资料
	 */
	public function actionGetinfoWithID(){
		$this->check_key();
		$id = Frame::getIntFromRequest('id');
		$user = $this->check_user();
		if (empty($id)) {
			$result['ret_num'] = 2125;
			$result['ret_msg'] = '参数错误';
			echo json_encode( $result );
			die ();	
		}
		
		$baixing = Bxapply::model()->findbypk($id);
		if (empty( $baixing )) {
			$result['ret_num'] = 2126;
			$result['ret_msg'] = '百姓网不存在';
			echo json_encode( $result );
			die ();
		}
		//$pinfo = $this->pcinfo();
		$result1 = array();
		$connection = Yii::app()->db;
		$sql = "select a.id,a.phone,a.short_phone, a.name,a.status,a.province,a.city,a.area,a.street,b.id_card,b.poster1,b.poster2, c.reason from bxapply a left join apply_complete b on a.id = b.apply_id  left join bxapply_record c on a.id = c.apply_id where a.id = '{$id}' order by c.id desc";
		$command = $connection->createCommand($sql);
		$result1 = $command->queryAll();
		//省市
		$pinfo = $this->ProCity($result1);
		foreach ($result1 as $key => $value){
			$result1[$key]['poster1'] = $value['poster1'] ? URL.$value['poster1'] : "";
			$result1[$key]['poster2'] = $value['poster2'] ? URL.$value['poster2'] : "";
			$result1[$key]['reason'] = ($value['status'] == 1 || $value['status'] == 2)?$this->eraseNull($value['reason']):"";
			$result1[$key]['pro_city'] = $pinfo[$result1[$key]['province']]." ".$pinfo[$result1[$key]['city']];
		}
		if(isset($result1[0])){
			$result1[0]['short_phone'] = $result1[0]['short_phone'] ? $result1[0]['short_phone'] : "";
			$result1[0]['id_card'] = $result1[0]['id_card'] ? $result1[0]['id_card'] : "";
			if ($result1[0]['id_card'] && $result1[0]['status'] != 2) {
				$result1[0]['id_card'] = substr($result1[0]['id_card'], 0, 6)."******".substr($result1[0]['id_card'], strlen($result1[0]['id_card'])-4);
			}
			if ($result1[0]['phone'] && $result1[0]['status'] != 2) {
				$result1[0]['phone'] = substr($result1[0]['phone'], 0, 4)."***".substr($result1[0]['phone'], strlen($result1[0]['phone'])-4);
			}
		}
		$result['ret_num'] = 0;
		$result['ret_msg'] = '操作成功';
		$result['info'] = $result1[0];
		echo json_encode( $result );
	}

	/**
	 * 修改百姓网资料
	 */
	public function actionEdit(){
		$this->check_key();
		$name = Frame::getStringFromRequest('name');
		$id_card = Frame::getStringFromRequest('id_card');
		$poster1 = Frame::saveImage('poster1');
		$poster2 = Frame::saveImage('poster2');
		$phone = Frame::getStringFromRequest('phone');
		$province = Frame::getIntFromRequest('province');
		$city = Frame::getIntFromRequest('city');
		$area = Frame::getIntFromRequest('area');
		$street = Frame::getIntFromRequest('street');
		$user = $this->check_user();
		// $apply_info = ApplyComplete::model()->find("member_id = {$user->id} and type = 1");
		$apply_info = ApplyComplete::model()->find("phone = {$phone} and type = 1");
		if($apply_info){
			//查找关联申请信息
			$apply_in = Bxapply::model()->find("id = {$apply_info->apply_id} and (status = 1 or status = 2)");
			if(!$apply_in){
				//修复关联信息
				$apply_in = Bxapply::model()->find("phone={$phone} and (status = 1 or status = 2)");
				if (!$apply_in) {
					$result['ret_num'] = 1606;
					$result['ret_msg'] = '不能操作';
					echo json_encode( $result );
					exit();
				}
				//重新关联资料和申请的关系
				$apply_info->apply_id = $apply_in->id;
			}
			if ($phone == $user->phone) {
				//修改自己的资料
				if (empty($poster1) && empty($apply_info->poster1)) {
					$result['ret_num'] = 1006;
					$result['ret_msg'] = '身份证正面照没有上传';
					echo json_encode( $result );
					die ();
				}
				if (empty($poster2) && empty($apply_info->poster2)) {
					$result['ret_num'] = 1007;
					$result['ret_msg'] = '身份证反面照没有上传';
					echo json_encode( $result );
					die ();
				}
			}

			if($name){
				$apply_info->name = $name;
				$apply_in->name = $name;
			}
			if($id_card){
				$apply_info->id_card = $id_card;	
			}
			if($poster1){
				$apply_info->poster1 = $poster1;
			}
			if($poster2){
				$apply_info->poster2 = $poster2;
			}
			if($phone){
				$apply_info->phone = $phone;
			}
			if($province){
				$apply_info->province = $province;
				$apply_in->province = $province;
			}
			if($city){
				$apply_info->city = $city;
				$apply_in->city = $city;
			}
			if($area){
				$apply_info->area = $area;
				$apply_in->area = $area;
			}
			if($street){
				$apply_info->street = $street;
				$apply_in->street = $street;
			}
			//$apply_info->status = 0;
			if($apply_info->update()){
				$apply_in->status = 0;
				$apply_in->update();

			if ($phone == $user->phone) {
				//补全直通车资料
				$ztc_info = ApplyComplete::model()->find("member_id={$user->id} and type = 2");
				if(!$ztc_info){ 
					$apply_ztc = new ApplyComplete();
					$apply_ztc->name = $name;
					$apply_ztc->id_card = $id_card;
					$apply_ztc->member_id = $memberid;
					$apply_ztc->poster1 = $apply_info->poster1;
					$apply_ztc->poster2 = $apply_info->poster2;
					$apply_ztc->phone = $phone;
					$apply_ztc->province = $province;
					$apply_ztc->city = $city;
					$apply_ztc->area = $area;
					$apply_ztc->street = $street;
					$apply_ztc->type = 2;
					$apply_ztc->created_time = time();
					if($apply_ztc->save()){
						if (($user->userinfo & 1) == 0) {
							$user->userinfo = $user->userinfo + 1;
							$user->update();
						}
					}
				}
				if ($user->userinfo < 2) {
					$user->userinfo = $user->userinfo + 2;
					$user->update();
				}
				$result['userinfo'] = $user->userinfo;
			}
				
				$result['ret_num'] = 0;
				$result['ret_msg'] = '操作成功';
				$result['apply_info'] = array(
						"ApplyId"=>$apply_info->id,
						"ApplyName"=>$apply_info->name,
						"ApplyPhone"=>$apply_info->phone,
						"ApplyCreated_time"=>$apply_info->created_time
				);
			}else{
				$result['ret_num'] = 1603;
				$result['ret_msg'] = '百姓网信息完善失败';
			}
		}else{
			//没有完善过资料，直接修改申请信息
			$apply_in = Bxapply::model()->find("phone = {$phone} and (status = 1 or status = 2)");
			if (!$apply_in) {
				$result['ret_num'] = 1606;
				$result['ret_msg'] = '没有操作权限';
				echo json_encode( $result );
				exit();
			}
			if ($phone == $user->phone) {
				//修改自己的资料、补全信息
				if (empty($poster1)) {
					$result['ret_num'] = 1006;
					$result['ret_msg'] = '身份证正面照没有上传';
					echo json_encode( $result );
					die ();
				}
				if (empty($poster2)) {
					$result['ret_num'] = 1007;
					$result['ret_msg'] = '身份证反面照没有上传';
					echo json_encode( $result );
					die ();
				}

				$memberid = $user->id;//$this->getMemberIdWithPhone($phone);
				if ($memberid == 0) {
					$result['ret_num'] = 1008;
					$result['ret_msg'] = '获取用户信息失败';
					echo json_encode( $result );
					die ();
				}
				
				//补全百姓资料
				$applyc = new ApplyComplete();
				$applyc->apply_id = $apply_in->id;
				$applyc->id_card = $id_card;
				$applyc->poster1 = $poster1;
				$applyc->poster2 = $poster2;
				$applyc->type = 1;
				$applyc->member_id = $memberid;
				$applyc->province = $province;
				$applyc->phone = $phone;
				$applyc->city = $city;
				$applyc->area = $area;
				$applyc->street = $street;
				$applyc->created_time = time();
				$applyc->name = $name;
				if($applyc->save()){
					$ztc_info = ApplyComplete::model()->find("member_id={$user->id} and type = 2");
					//补全直通车资料
					if(!$ztc_info){ 
						$apply_ztc = new ApplyComplete();
						$apply_ztc->name = $name;
						$apply_ztc->id_card = $id_card;
						$apply_ztc->member_id = $memberid;
						$apply_ztc->poster1 = $poster1;
						$apply_ztc->poster2 = $poster2;
						$apply_ztc->phone = $phone;
						$apply_ztc->province = $province;
						$apply_ztc->city = $city;
						$apply_ztc->area = $area;
						$apply_ztc->street = $street;
						$apply_ztc->type = 2;
						$apply_ztc->created_time = time();
						if($apply_ztc->save()){
							if (($user->userinfo & 1) == 0) {
								$user->userinfo = $user->userinfo + 1;
								$user->update();
							}
						}
					}
					if ($user->userinfo < 2) {
						$user->userinfo = $user->userinfo + 2;
						$user->update();
					}
					$result['userinfo'] = $user->userinfo;
				}else{
					$result['ret_num'] = 1603;
					$result['ret_msg'] = '百姓网信息保存失败';
					echo json_encode( $result );
					die ();
				}
			}


			if($name){
				$apply_in->name = $name;
			}
			if($province){
				$apply_in->province = $province;
			}
			if($city){
				$apply_in->city = $city;
			}
			if($area){
				$apply_in->area = $area;
			}
			if($street){
				$apply_in->street = $street;
			}

			$apply_in->status = 0;
			$apply_in->update();
			
			$result['ret_num'] = 0;
			$result['ret_msg'] = '操作成功';
			$result['apply_info'] = array(
					"ApplyId"=>$apply_in->id,
					"ApplyName"=>$apply_in->name,
					"ApplyPhone"=>$apply_in->phone,
					"ApplyCreated_time"=>$apply_in->created_time
			);
				
			// $result['ret_num'] = 1604;
			// $result['ret_msg'] = '百姓网信息未完善';
		}
		
		echo json_encode( $result );
	
	}
	
	/**
	 * 获取非百姓网用户
	 */
	public function actionGetnotbx(){
		$this->check_key();
		$user = $this->check_user();
		$connection = Yii::app()->db;
		$sql1 = "select id,group_name name from group_contact where member_id = {$user->id}";
		$command = $connection->createCommand($sql1);
		$result1 = $command->queryAll();
		if($result1){
			foreach ($result1 as $value){
				$gid .= $value['id'].",";
			}
			$gid = trim($gid);
			$gid =trim($gid,',');
		}
		$result2 = array();
		if($gid){
			$sql2 = "select id,group_id,name,pinyin,created_time from group_contact_info where group_id in ({$gid}) order by pinyin asc";
			$command = $connection->createCommand($sql2);
			$result2 = $command->queryAll();
			foreach ($result2 as $val){
				$aid .= $val['id'].",";
			}
			$aid = trim($aid);
			$aid =trim($aid,',');
		}
		if($aid){
			$contact_phone = array();
			$contact_phonea = array();
			$sql3 = "select id,contact_info_id,phone from group_contact_phone  where contact_info_id in ({$aid}) and length(phone) = 11";
			$command = $connection->createCommand($sql3);
			$result3 = $command->queryAll();//var_dump($result3);exit();
			foreach ($result3 as $va){
				$aphone .= "'".$va['phone']."',";
			}
			$aphone = trim($aphone);
			$aphone =trim($aphone,',');
			if($aphone){
				$sql4 = "select id,phone from bxapply  where phone in ({$aphone}) and (status = 3 or status = 1 or status = 0)";
				$command = $connection->createCommand($sql4);
				$result4 = $command->queryAll();
				foreach ($result4 as $ve){
					$bxphone .= $ve['phone'].",";
				}
				$bxphone = trim($bxphone);
				$bxphone =trim($bxphone,',');
				
				//$arr_aphone = explode(",", $aphone);
				$arr_bxphone = explode(",", $bxphone);
// 				echo $aphone."=======".$bxphone;
// 				$notbx = array_diff($arr_aphone, $arr_bxphone);
                foreach ($result3 as $k=>$re){
                	if(in_array($re['phone'], $arr_bxphone)){
                		unset($result3[$k]);continue;
                	}
                	foreach ($result2 as $res){
                		if($res['id'] == $re['contact_info_id']){
                			$result3[$k]['name'] = $res['name'];
                			$result3[$k]['pinyin'] = strtoupper($res['pinyin']);
                		}
                	}               	
                }            				
			}			
		}
		$tmp =array();
        if($result3){       	
        	foreach($result3 as  $v){
        		$tmp[] =$v;
        	}
        }
		
		$result ['ret_num'] = 0;
		$result ['ret_msg'] = '操作成功';
		$result['contact'] = $tmp;
		
		echo json_encode( $result );
	}
	
	/**
	 * 获取非百姓网用户(分组)
	 */
	public function actionGetnotbxg(){
		$this->check_key();
		$user = $this->check_user();
		$connection = Yii::app()->db;
		$sql1 = "select id,group_name name from group_contact where member_id = {$user->id} order by id asc";
		$command = $connection->createCommand($sql1);
		$result1 = $command->queryAll();
		$result_group = array();
		if($result1){
			$temp = "";
			foreach ($result1 as $value){
				$gid .= $value['id'].",";				
				if($value['name'] != "未分组"){					
					$result_group[$value['id']] = $value['name'];
				}else{
					$temp=$value;
				}
			}
			$result_group[$temp['id']]="未分组";
			$gid = trim($gid);
			$gid =trim($gid,',');
		}
		$result2 = array();
	
		if($gid){
			$contact_phone = array();
			$contact_phonea = array();
			$sql = "select a.id,b.phone,b.is_benben, a.name,a.group_id,a.pinyin,c.poster from group_contact_info as a 
			left join group_contact_phone as b on a.id = b.contact_info_id left join member c on c.phone = b.phone 
			where  a.group_id in ({$gid}) and length(b.phone) = 11 order by a.pinyin asc";//group by a.id
			$command = $connection->createCommand($sql);
			$result3 = $command->queryAll();
			foreach ($result3 as $key=>$va){
				if($va['poster']){
					$result3[$key]['poster'] = URL.$va['poster'];
				}else{
					$result3[$key]['poster'] = "";
				}
				$aphone .= "'".$va['phone']."',";
			}
			$aphone = trim($aphone);
			$aphone =trim($aphone,',');
			if($aphone){
				$sql4 = "select id,phone from bxapply  where phone in ({$aphone}) and (status = 3 or status = 1 or status = 0)";
				$command = $connection->createCommand($sql4);
				$result4 = $command->queryAll();
				foreach ($result4 as $ve){
					$arr_bxphone[] = $ve['phone'];
				}
			
			}
		}
				
		$member_list = array();
		foreach ($result_group as $key => $value) {
			$currentMember = array();
			foreach ($result3 as $va){
				if(($va['group_id'] == $key) && (!in_array($va['phone'], $arr_bxphone))){
					$currentMember[] = $va;
				}
			}
			$member_list[] = array('id'=>$key, 'name'=>$value."(".count($currentMember)."人)", 'member'=>$currentMember);
		}
		
		$result ['ret_num'] = 0;
		$result ['ret_msg'] = '操作成功';
		$result['contact'] = $member_list;
	
		echo json_encode( $result );
	}
	
	/**
	 * 邀请好友加入百姓网
	 */
	public function actionInvite(){
		$this->check_key();
		$namephone = Frame::getStringFromRequest('namephone');
		//$namephone = "王中军::15636283333::34::3401::340102::02|王中磊::18900000000::34::3401::340106::02";
		if (empty( $namephone )) {
			$result['ret_num'] = 1105;
			$result['ret_msg'] = '姓名为空';
			echo json_encode( $result );
			die ();
		}		
		$user = $this->check_user();
		$arr_namephone = explode("|", $namephone);
		$sqlinfo = array();
		$invite_phone = array();
		$invite_update_data = array();
		foreach ($arr_namephone as $value){
			if($value){
				$bxinfo = explode("::", $value);
				if (count($bxinfo) > 6) {
					$result['ret_num'] = 1105;
					$result['ret_msg'] = '参数错误';
					echo json_encode( $result );
					die ();
				}
				$invite_phone[] = $bxinfo[1];
				$invite_update_data[$bxinfo[1]] = $bxinfo;
			}
		}
		$connection = Yii::app()->db;
		//判断邀请中，是否已经有用户在申请中
		$sql = "select id,phone,status,name,province,city,area,street from bxapply where status<>4 and phone in (".implode(",", $invite_phone).")";
		$command = $connection->createCommand($sql);
		$info = $command->queryAll();
		$back_phone_array = array();
		$back_phone = array();
		if ($info) {
			$apply_array = array();
			//查出退回重申的号码
			foreach ($info as $va){
				if($va['status'] == 2){
					$itenphone = $va['phone'];
					$new_data = $invite_update_data[$itenphone];
					if (count($new_data) >=6 ) {
						$va['name'] = $new_data[0];
						$va['province'] = $new_data[2];
						$va['city'] = $new_data[3];
						$va['area'] = $new_data[4];
						$va['street'] = $new_data[5];
					}
					$va['status'] = 0;

					$back_phone[] = $va;
					$back_phone_array[] = $itenphone;
				}else {
					//待审核、未通过、已通过
					$apply_array[] = $va;
				}
			}
			if(count($apply_array) > 0){
				$result ['ret_num'] = 1215;
				$result ['ret_msg'] = '好友'.$apply_array[0]['phone'].'已经在申请中';
				echo json_encode( $result );
				die();
			}			
		}
		
		
		foreach ($arr_namephone as $value){
			if($value){
				$bxinfo = explode("::", $value);
				if (count($bxinfo) > 6) {
					$result['ret_num'] = 1105;
					$result['ret_msg'] = '参数错误';
					echo json_encode( $result );
					die ();
				}
				//$invite_phone[] = $bxinfo[1];
				if($back_phone_array && $bxinfo[1] && in_array($bxinfo[1], $back_phone_array)) continue;
				foreach ($bxinfo as $key=>$v){
					$bxinfo[$key] = "'".$v."'";
				}
				$ainfo = implode(",",$bxinfo);
				// $sqlinfo .= "(".$user->id.",".$ainfo.",".time()."),";
				$sqlinfo[] = "(".$user->id.",".$ainfo.",".time().")";
			}			
		}
		

		// $sqlinfo = trim($sqlinfo);
	//	$sqlinfo =trim($sqlinfo,',');
		// $sqlinfo = substr($sqlinfo, 0, -1);
		// $sql = "insert into bxapply (member_id,name,phone,province,city,area,street,created_time) values {$sqlinfo}";
		
		$flag = true;
		if(count($back_phone) > 0){
			foreach ($back_phone as $value) {
				$upstring = 'status='.$value['status'];
				$upstring .= ',name='."'".$value['name']."'";
				$upstring .= ',province='.$value['province'];
				$upstring .= ',city='.$value['city'];
				$upstring .= ',area='.$value['area'];
				$upstring .= ',street='.$value['street'];

				$sql2 = "update bxapply set {$upstring} where id={$value['id']}";
				$command = $connection->createCommand($sql2);
				$flag = $command->execute();
			}
		}
		
		if (count($sqlinfo) > 0) {
			$sql = "insert into bxapply (member_id,name,phone,province,city,area,street,created_time) values ".implode(",", $sqlinfo);
			$command = $connection->createCommand($sql);
			$flag = $command->execute() && $flag;
		}
										
		if($flag){
			$bxapplyInviteLog = new BxapplyInviteLog();
			$bxapplyInviteLog->member_id = $user->id;
			$bxapplyInviteLog->number = $result1;
			$bxapplyInviteLog->created_time = time();
			$bxapplyInviteLog->save();
			$result ['ret_num'] = 0;
			$result ['ret_msg'] = '操作成功';
			//$result ['number_info'] = $result0;
		}else{
			$result ['ret_num'] = 1215;
			$result ['ret_msg'] = '邀请好友失败';
		}			
			
		echo json_encode( $result );
	}
	
	/**
	 * 申请进度查询
	 */
	public function actionApply(){
		$this->check_key();
		$phone = Frame::getStringFromRequest('phone');
		if (empty( $phone )) {
			$result['ret_num'] = 1220;
			$result['ret_msg'] = '号码为空';
			echo json_encode( $result );
			die ();
		}
		$user = $this->check_user();
		$connection = Yii::app()->db;
		$sql = "select status from bxapply where phone = '{$phone}'";
		$command = $connection->createCommand($sql);
		$result1 = $command->queryAll();
		if(!$result1){
			$result['ret_num'] = 1221;
			$result['ret_msg'] = '未查询到该号码';
		}else{
			$result['ret_num'] = 0;
			$result ['ret_msg'] = '操作成功';
			switch ($result1[0]['status']) {
				case 0:
							$result ['status'] = '等待审核';
							break;
			    case 1:
							$result ['status'] = '审核未通过';
							break;
			    case 2:
							$result ['status'] = '退回重申';
							break;
				case 3:
							$result ['status'] = '审核已经通过';
							break;				
				default:
					        $result ['status'] = '未查询到该号码';
							break;	
			}
		}
		echo json_encode( $result );		
	}
	
	/**
	 * 申请进度查询(所有)
	 */
	public function actionApplyall(){
		$this->check_key();
		//$memberid = Frame::getStringFromRequest('memberid');
// 		if (empty( $memberid )) {
// 			$result['ret_num'] = 1221;
// 			$result['ret_msg'] = '创建人ID为空';
// 			echo json_encode( $result );
// 			die ();
// 		}
		$user = $this->check_user();
		$connection = Yii::app()->db;
		$sql = "select id,name,phone,short_phone,province,city,area,status,created_time from bxapply where member_id = {$user->id} and status<>4 order by created_time desc";
		$command = $connection->createCommand($sql);
		$result1 = $command->queryAll();
		$pinfo = $this->ProCity($result1);
		foreach ($result1 as $key =>$value){
			$short_phone = '';
			if ($value['status'] == 3) {
				//已通过
				$short_phone = $result1[$key]['short_phone']?$result1[$key]['short_phone']:"";
			}
			$result1[$key]['short_phone'] = $short_phone;
			$result1[$key]['address'] = $pinfo[$value['province']]." ".$pinfo[$value['city']];
			$result1[$key]['date'] = date("Y-m-d H:i:s",$value['created_time']);			
		}
				
		$result['ret_num'] = 0;
		$result['ret_msg'] = '操作成功';
		$result['status'] = $result1;
		echo json_encode( $result );
	}

}