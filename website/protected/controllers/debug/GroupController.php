<?php
class GroupController extends PublicController
{
	public $layout = false;
	/**
	 * 我的群组
	 */
	public function actionMygroup(){		
		$this->check_key();	
		$user = $this->check_user();
		//$pinfo = $this->pcinfo();
		$group_id = GroupMember::model()->findAll("member_id = {$user->id}");
		if(empty($group_id)){
			$result['ret_num'] = 0;
			$result['ret_msg'] = '操作成功';
			$result['group_list'] = array();
			echo json_encode( $result );
			die();
		}
		$gid = "";
		foreach ($group_id as $val){
			$gid .= $val->contact_id.",";
		}
		$gid = trim($gid);
		$gid =trim($gid,',');
		if($gid){
			$sql = "select id,poster,name,description,province,city,bulletin,member_id,number,status,created_time,level,huanxin_groupid from groups where id in ({$gid}) and is_delete = 0";//  and status = 0
			$connection = Yii::app()->db;
			$command = $connection->createCommand($sql);
			$result1 = $command->queryAll();
			$pinfo = $this->ProCity($result1);
			foreach ($result1 as $key => $ginfo){
				$result1[$key]['poster'] = $ginfo['poster'] ? URL.$ginfo['poster']:"";
				$result1[$key]['description'] = $ginfo['description'] ? $ginfo['description']:"";
				$result1[$key]['bulletin'] = $ginfo['bulletin'] ? $ginfo['bulletin']:"";
				$result1[$key]['province'] = $pinfo[$ginfo['province']];
				$result1[$key]['city'] = $pinfo[$ginfo['city']];
				$result1[$key]['pro_city'] = $pinfo[$ginfo['province']]."-".$pinfo[$ginfo['city']];
			}		
			
			$result['ret_num'] = 0;
			$result['ret_msg'] = '操作成功';
			$result['group_list'] = $result1;
			echo json_encode( $result );
		}						
	}
	
	/**
	 * 搜索群组
	 */
	public function actionSearch(){
		$this->check_key();
		$keyword = Frame::getStringFromRequest('keyword');
		$user = $this->check_user();
		//$pinfo = $this->pcinfo();
		$group_id = GroupMember::model()->findAll("member_id = {$user->id}");		
		$gid = "";
		foreach ($group_id as $val){
			$gid .= $val->contact_id.",";
		}
		$gid = trim($gid);
		$gid =trim($gid,',');
		
		if($gid){
			$sql = "select * from groups where id not in ({$gid}) and is_delete = 0";
			if($keyword){
				$sql = "select * from groups where (id = '{$keyword}' or name like '%{$keyword}%') and id not in ({$gid}) and is_delete = 0";
			}
		}else{
			$sql = "select * from groups where is_delete = 0";
			if($keyword){
				$sql = "select * from groups where (id = '{$keyword}' or name like '%{$keyword}%') and is_delete = 0";
			}
		}
			
		$connection = Yii::app()->db;
		$command = $connection->createCommand($sql);
		$result1 = $command->queryAll();
		$pinfo = $this->ProCity($result1);
		foreach ($result1 as $key => $value) {
			$result1[$key]['poster'] = $result1[$key]['poster'] ? URL.$result1[$key]['poster']:"";
			$result1[$key]['description'] = $value['description'] ? $value['description']:"";
			$result1[$key]['bulletin'] = $value['bulletin'] ? $value['bulletin']:"";
			$result1[$key]['province'] = $pinfo[$value['province']];
			$result1[$key]['city'] = $pinfo[$value['city']];
			$result1[$key]['area'] = $pinfo[$value['area']];
			$result1[$key]['pro_city'] = $pinfo[$value['city']]."-".$pinfo[$value['area']];
		}
				
		$result['ret_num'] = 0;
		$result['ret_msg'] = '操作成功';
		$result['group_list'] = $result1;
		echo json_encode( $result );
		
	}
	
	/**
	 * 新建群组
	 */
	public function actionAdd(){
		$this->check_key();
		$name = Frame::getStringFromRequest('name');
		$poster = Frame::saveImage('poster');	
		$province = Frame::getIntFromRequest('province');
		$city = Frame::getIntFromRequest('city');
		$region = Frame::getIntFromRequest('region');
		$street = Frame::getIntFromRequest('street');
		$description = Frame::getStringFromRequest('description');
		$notice = Frame::getStringFromRequest('notice');
		$user = $this->check_user();
		if(empty($name)){
			$result['ret_num'] = 400;
			$result['ret_msg'] = '群组名称为空';
			echo json_encode( $result );
			die();
		}
		if (empty($city)) {
			$result['ret_num'] = 404;
			$result['ret_msg'] = '所在地区不能为空';
			echo json_encode( $result );
			die();
		}
		if(empty($description)){
			$result['ret_num'] = 1400;
			$result['ret_msg'] = '群组描述为空';
			echo json_encode( $result );
			die();
		}
		$group_info = new Groups();
		//生成群组ID
		$connection = Yii::app()->db;
		$sql = "select max(show_id) maxid from groups";	
		$command = $connection->createCommand($sql);
		$result1 = $command->queryAll();
		
		if($result1[0]['maxid']){
			$id = $result1[0]['maxid']+1;
			while(true){
				$check = $this->checkbenben($id);
				if($check){
					break;
				}
				$id++;
			}
		
			$group_info->show_id = $id;
		}else{
			$group_info->show_id = 20003;
		}
		$group_info->name = $name;
		$group_info->member_id = $user->id;
		$group_info->poster = $poster;
		$group_info->province = $province;
		$group_info->city = $city;
		$group_info->area = $region;
		$group_info->street = $street;
		$group_info->description = $description;
		$group_info->bulletin = $notice;
		$group_info->status = 0;
		$group_info->created_time = time();
		if($group_info->save()){
			//注册环信群组
			$options = array(
					"client_id"=>CLIENT_ID,
					"client_secret"=>CLIENT_SECRET,
					"org_name"=>ORG_NAME,
					"app_name"=>APP_NAME
			      );
			$huanxin = new Easemob($options);
			$parameter['groupname'] = $name;
			$parameter['desc'] = $description;
			$parameter['public'] = true;
			$parameter['maxusers'] = 500;
			$parameter['approval'] = true;
			$parameter['owner'] = $user->huanxin_username;
			//$parameter['members'] = array($user->huanxin_username);
			$resulh = $huanxin->createGroups($parameter);
			//$header[] = 'Authorization: Bearer YWMtvuxexsiFEeSfroXS3Jae_QAAAU1ByDJ8NDGAs0F_Fl2t_bgRyR0tS55_XJk';
			//$resulh = $this->chatGroups($parameter,$header);
// 			$file = fopen("log.txt", "a+");
// 			fwrite($file, $resulh."\n");
// 			fclose($file);
						
			$reh = json_decode($resulh, true);
			if(!$reh['error']){
				$group_info->huanxin_groupid = $reh['data']['groupid'];				
				$group_info->update();
			}else {
				$group_info->delete();
				//环信注册失败，删除群组
				$result['ret_num'] = 118;
				$result['ret_msg'] = '群组申请失败:'.$reh['error'];
				echo json_encode( $result );
				die();
			}

			//加入自己的群组成为群主
			$sqla = "insert into group_member (contact_id,member_id,role,created_time,status) value({$group_info->id},{$user->id},1,".time().",1)";
			$connection = Yii::app()->db;
			$command = $connection->createCommand($sqla);
			$result1 = $command->execute();
			if($result1){
				$group_info->number = 1;
				$group_info->update();
			}

			//$pinfo = $this->pcinfo();
			$pro_city = "";
			if($group_info->province && $group_info->city){
				$pinfo = $this->getProCity("$group_info->province,$group_info->city");
				$pro_city = $pinfo[0]['area_name']."-".$pinfo[1]['area_name'];
			}
			$this->addIntegral($user->id, 5);	
			$result['ret_num'] = 0;
			$result['ret_msg'] = '操作成功';
			$postera = $group_info->poster ? URL.$group_info->poster:"";
			$result['group_info'] = array(
				    "id"=>$group_info->id,
					"show_id"=>$group_info->show_id,
					"name"=>$group_info->name,
					"poster"=>$postera,
					"province"=>$group_info->province,
					"city"=>$group_info->city,
					"area"=>$group_info->area,
					"street"=>$group_info->street,
					"description"=>$group_info->description,
					"bulletin"=>$group_info->bulletin,
					"number"=>$group_info->number,
					"level"=>$group_info->level,
					"created_time"=>$group_info->created_time,
					"huanxin_groupid"=>$group_info->huanxin_groupid,
					"pro_city"=>$pro_city
			);
		}else{			
			$result['ret_num'] = 118;
			$result['ret_msg'] = '新建群组失败';
		}
		echo json_encode( $result );
		
	}
	
	/**
	 * 获取群组详情
	 */
	public function actionDetail(){
		$this->check_key();
		$groupid = Frame::getIntFromRequest('groupid');
		if(empty($groupid)){
			$result['ret_num'] = 401;
			$result['ret_msg'] = '群组ID为空';
			echo json_encode( $result );
			die();
		}
		$user = $this->check_user();		
		$group_info = Groups::model()->find("id = {$groupid} and is_delete = 0");				
		if(empty($group_info)){
			$result['ret_num'] = 402;
			$result['ret_msg'] = '群组ID不存在';
			echo json_encode( $result );
			die();
		}else{
			//查询群主
			$is_admin = 0;
			$group_admin = GroupMember::model()->find("contact_id = {$groupid} and role = 1");
			if($group_admin->member_id == $user->id){
				$is_admin = 1;
			}
			//查询自己的群昵称
			$group_nick_name = GroupMember::model()->find("contact_id = {$groupid} and member_id = {$user->id}");
			
			//$pinfo = $this->pcinfo();
			$pro_city = "";
			if($group_info->province && $group_info->city){
				//省市
				$pro = array("province"=>$group_info->province,"city"=>$group_info->city,"area"=>$group_info->area);
				$pro_arr = $this->ProCity(array($pro));
				$pro_city = $pro_arr[$group_info->city].($group_info->area?"-":"").$pro_arr[$group_info->area];
			}
			$result['ret_num'] = 0;
			$result['ret_msg'] = '操作成功';
			$postera = $group_info->poster ? URL.$group_info->poster:"";
			$result['group_info'] = array(
					"id"=>$group_info->id,
					"show_id"=>$group_info->show_id,
					"group_admin"=>$group_admin->member_id,
					"is_admin" => $is_admin,
					"group_nick_name"=>$group_nick_name->nick_name ? $group_nick_name->nick_name :"",
					"name"=>$group_info->name,
					"member_id"=>$group_info->member_id,
					"poster"=>$postera,
					"province"=>$pro_arr[$group_info->province],
					"city"=>$pro_arr[$group_info->city],
					"area"=>$group_info->area,
					"street"=>$group_info->street,
					"description"=>$group_info->description,
					"bulletin"=>$group_info->bulletin,
					"number"=>$group_info->number,
					"maxuser"=>MAXUSER,
					"level"=>$group_info->level,
					"created_time"=>$group_info->created_time,
					"huanxin_groupid"=>$group_info->huanxin_groupid,
					"pro-city"=>$pro_city
			);
			echo json_encode( $result );
		}		
	}
	
	/**
	 * 根据环信id获取群组详情
	 */
	public function actionDetailWithHXId(){
		$this->check_key();
		$huanxin_groupid = Frame::getIntFromRequest('huanxin_groupid');
	
		$groups = Groups::model()->find("huanxin_groupid = {$huanxin_groupid} and is_delete = 0");
		if(empty($groups)){
			$result['ret_num'] = 109;
			$result['ret_msg'] = '该群组不存在';
			echo json_encode( $result );
			die ();
		}
		//群组id
		$groupid = $groups->id;
		if(empty($groupid)){
			$result['ret_num'] = 401;
			$result['ret_msg'] = '群组ID为空';
			echo json_encode( $result );
			die();
		}
		$user = $this->check_user();		
		$group_info = Groups::model()->findByPk($groupid);				
		if(empty($group_info)){
			$result['ret_num'] = 402;
			$result['ret_msg'] = '群组ID不存在';
			echo json_encode( $result );
			die();
		}else{
			//查询群主
			$is_admin = 0;
			$group_admin = GroupMember::model()->find("contact_id = {$groupid} and role = 1");
			if($group_admin->member_id == $user->id){
				$is_admin = 1;
			}
			//查询自己的群昵称
			$group_nick_name = GroupMember::model()->find("contact_id = {$groupid} and member_id = {$user->id}");
			
			//$pinfo = $this->pcinfo();
			$pro_city = "";
			if($group_info->province && $group_info->city){
				//省市
				$pro = array("province"=>$group_info->province,"city"=>$group_info->city,"area"=>$group_info->area);
				$pro_arr = $this->ProCity(array($pro));
				$pro_city = $pro_arr[$group_info->city].($group_info->area?"-":"").$pro_arr[$group_info->area];
			}
			$result['ret_num'] = 0;
			$result['ret_msg'] = '操作成功';
			$postera = $group_info->poster ? URL.$group_info->poster:"";
			$result['group_info'] = array(
					"id"=>$group_info->id,
					"group_admin"=>$group_admin->member_id,
					"is_admin" => $is_admin,
					"group_nick_name"=>$group_nick_name->nick_name ? $group_nick_name->nick_name :"",
					"name"=>$group_info->name,
					"member_id"=>$group_info->member_id,
					"poster"=>$postera,
					"province"=>$pro_arr[$group_info->province],
					"city"=>$pro_arr[$group_info->city],
					"area"=>$group_info->area,
					"street"=>$group_info->street,
					"description"=>$group_info->description,
					"bulletin"=>$group_info->bulletin,
					"number"=>$group_info->number,
					"maxuser"=>MAXUSER,
					"level"=>$group_info->level,
					"created_time"=>$group_info->created_time,
					"huanxin_groupid"=>$group_info->huanxin_groupid,
					"pro-city"=>$pro_city
			);
			echo json_encode( $result );
		}		
	}

	/**
	 * 根据群组ID返回群组ID,名称,头像
	 */
	public function actionGetgroupinfo(){
		$this->check_key();
		$groupid = Frame::getStringFromRequest('groupid');
		if(empty($groupid)){
			$result['ret_num'] = 401;
			$result['ret_msg'] = '群组ID为空';
			echo json_encode( $result );
			die();
		}
		$user = $this->check_user();			
		
		if($groupid){
			$sql = "select id,poster,name from groups where id in ({$groupid})  and status = 0 and is_delete = 0";
			$connection = Yii::app()->db;
			$command = $connection->createCommand($sql);
			$result1 = $command->queryAll();
			foreach ($result1 as $key => $ginfo){
				$result1[$key]['poster'] = $ginfo['poster'] ? URL.$ginfo['poster']:"";
			}				
			$result['ret_num'] = 0;
			$result['ret_msg'] = '操作成功';
			$result['group_list'] = $result1;
			echo json_encode( $result );
		}
	}
	
	/**
	 * 修改群组信息
	 */
	public function actionEdit(){
		$this->check_key();
		$groupid = Frame::getIntFromRequest('groupid');
		$name = Frame::getStringFromRequest('name');
		$poster = Frame::saveImage('poster');
		$province = Frame::getIntFromRequest('province');
		$city = Frame::getIntFromRequest('city');
		$area = Frame::getIntFromRequest('region');
		$street = Frame::getIntFromRequest('street');
		$description = Frame::getStringFromRequest('description');
		if(empty($groupid)){
			$result['ret_num'] = 401;
			$result['ret_msg'] = '群组ID为空';
			echo json_encode( $result );
			die();
		}
		$user = $this->check_user();		
		$group_info = Groups::model()->find("id = {$groupid} and member_id = {$user->id} and is_delete = 0");		
		if(empty($group_info)){
			$result['ret_num'] = 402;
			$result['ret_msg'] = '群组ID不存在';
			echo json_encode( $result );
			die();
		}
		if($name){
			$group_info->name = $name;
			$parameter['groupname'] = $name;
		}
		if($poster){
			$group_info->poster = $poster;
		}
		if($province){
			$group_info->province = $province;
			$group_info->city = 0;
			$group_info->area = 0;
			$group_info->street = 0;
		}
		if($city){
			$group_info->city = $city;
			$group_info->area = 0;
			$group_info->street = 0;
		}
		if($area){
			$group_info->area = $area;
			$group_info->street = 0;
		}
		if($street){
			$group_info->street = $street;
		}
		if($description){
			$group_info->description = $description;
			$parameter['desc'] = $description;
		}
		if($group_info->update()){
			//修改环信群组名
			$options = array(
					"client_id"=>CLIENT_ID,
					"client_secret"=>CLIENT_SECRET,
					"org_name"=>ORG_NAME,
					"app_name"=>APP_NAME
			);
			$huanxin = new Easemob($options);								
			//$parameter['maxusers'] = 300;			
			$resulh = $huanxin->editGroupsInfo($group_info->huanxin_groupid, $parameter);
			$reh = json_decode($resulh, true);
			
			//$pinfo = $this->pcinfo();
			$pro_city = "";
			if($group_info->province && $group_info->city){
				//省市
				$pro = array("province"=>$group_info->province,"city"=>$group_info->city);
				$pro_arr = $this->ProCity(array($pro));
				$pro_city = $pro_arr[$group_info->province]."-".$pro_arr[$group_info->city];
			}			
			$postera = $group_info->poster ? URL.$group_info->poster:"";
			
			$result['ret_num'] = 0;
			$result['ret_msg'] = '操作成功';
			$result['group_info'] = array(
					"id"=>$group_info->id,
					"name"=>$group_info->name,
					"member_id"=>$group_info->member_id,
					"poster"=>$postera,
					"province"=>$pro_arr[$group_info->province],
					"city"=>$pro_arr[$group_info->city],
					"area"=>$group_info->area,
					"street"=>$group_info->street,
					"description"=>$group_info->description,
					"bulletin"=>$group_info->bulletin,
					"number"=>$group_info->number,
					"maxuser"=>MAXUSER,
					"level"=>$group_info->level,
					"created_time"=>$group_info->created_time,
					"huanxin_groupid"=>$group_info->huanxin_groupid,
					"pro-city"=>$pro_city
			);
			echo json_encode( $result );
		}
		
	}
	
	/**
	 * 修改群名片
	 */
	public function actionEditnickname(){
		$this->check_key();
		$groupid = Frame::getIntFromRequest('groupid');
		$nickname = Frame::getStringFromRequest('nickname');		
		if(empty($groupid)){
			$result['ret_num'] = 401;
			$result['ret_msg'] = '群组ID为空';
			echo json_encode( $result );
			die();
		}
		//昵称可以为空
		if(empty($nickname)){
			// $result['ret_num'] = 4010;
			// $result['ret_msg'] = '昵称为空';
			// echo json_encode( $result );
			// die();
			$nickname = '';
		}
		$user = $this->check_user();
		$group_info = Groups::model()->findByPk($groupid);
		if(empty($group_info)){
			$result['ret_num'] = 402;
			$result['ret_msg'] = '群组ID不存在';
			echo json_encode( $result );
			die();
		}
		$guser = GroupMember::model ()->find("contact_id = {$groupid} and member_id = {$user->id} and status = 1");
		if($guser){
			$guser->nick_name = $nickname;
			if($guser->update()){
				$result['ret_num'] = 0;
				$result['ret_msg'] = '操作成功';
				$result['nick_name'] = $nickname;
			}
		}else{
			$result['ret_num'] = 4011;
			$result['ret_msg'] = '不是该群组成员';
		}		
		echo json_encode( $result );
	}
	
	/**
	 * 加入群组
	 */
	public function actionIdentify(){
		$this->check_key();
		$memberid = Frame::getIntFromRequest('member_id');
		$groupid = Frame::getIntFromRequest('group_id');
		$user = $this->check_user();
				
		$groups = Groups::model()->findByPk($groupid);
		if(empty($groups)){
			$result['ret_num'] = 109;
			$result['ret_msg'] = '该群组不存在';
			echo json_encode( $result );
			die ();
		}
		
		$applyid = $user->id;
		if($memberid){
			$ouser = Member::model ()->findByPk($memberid);
			if (empty( $ouser )) {
				$result['ret_num'] = 1000;
				$result['ret_msg'] = '被邀请用户不存在';
				echo json_encode( $result );
				die ();
			}
			$applyid = $memberid;
		}
		$guser = GroupMember::model ()->find("contact_id = {$groupid} and member_id = {$applyid}");
		if($guser){
			$result['ret_num'] = 107;
			$result['ret_msg'] = '已加入该群组';
			echo json_encode( $result );
			die ();
		}
		$nuser = new GroupMember();
		$nuser->contact_id = $groupid;
		$nuser->member_id = $applyid;
		$nuser->status = 0;
		$nuser->created_time = time();
		if($nuser->save()){
// 			$groups->number = $groups->number + 1;
// 			$groups->update();
			$result['ret_num'] = 0;
			$result['ret_msg'] = '操作成功';
		}else{
			$result['ret_num'] = 108;
			$result['ret_msg'] = '加入群组失败';
		}
		echo json_encode( $result );
	
	}
	
	/**
	 * 退出群组
	 */
	public function actionQuit(){
// 		$header[] = 'Authorization: Bearer YWMtvuxexsiFEeSfroXS3Jae_QAAAU1ByDJ8NDGAs0F_Fl2t_bgRyR0tS55_XJk';
// 		$re = $this->chatGroups("",$header);var_dump($re);exit();
// 		$options = array(
// 				"client_id"=>CLIENT_ID,
// 				"client_secret"=>CLIENT_SECRET,
// 				"org_name"=>ORG_NAME,
// 				"app_name"=>APP_NAME
// 		);
// 		$huanxin = new Easemob($options);
// 		$resulh = $huanxin->delGroupsUser('1426484600206523', 'd0058b825ad4871070bd13e88613761a');
// 		$reh = json_decode($resulh, true);var_dump($reh);exit();
		
		$this->check_key();
		$memberid = Frame::getIntFromRequest('member_id');
		$groupid = Frame::getIntFromRequest('group_id');
		$user = $this->check_user();
				
		$groups = Groups::model()->find("id = {$groupid} and is_delete = 0");
		if(empty($groups)){
			$result['ret_num'] = 109;
			$result['ret_msg'] = '该群组不存在';
			echo json_encode( $result );
			die ();
		}
		
		$applyid = $user->id;
		$nick_name = $user->nick_name;
		$huanxin_user = $user->huanxin_username;
		if($memberid){
			$ouser = Member::model ()->findByPk($memberid);
			if (empty( $ouser )) {
				$result['ret_num'] = 1001;
				$result['ret_msg'] = '该用户不存在';
				echo json_encode( $result );
				die ();
			}
			//是否是群主
			$aguser = GroupMember::model ()->find("contact_id = {$groupid} and member_id = {$applyid} and status = 1 and role = 1");
			if(!$aguser){
				$result['ret_num'] = 5260;
				$result['ret_msg'] = '不是群主';
				echo json_encode( $result );
				die ();
			}
			$applyid = $memberid;
			$nick_name = $ouser->nick_name;
			$huanxin_user = $ouser->huanxin_username;
		}
		
		$guser = GroupMember::model ()->find("contact_id = {$groupid} and member_id = {$applyid} and status = 1");
		if(empty($guser)){
			$result['ret_num'] = 112;
			$result['ret_msg'] = '已退出该群组';
			echo json_encode( $result );
			die ();
		}
		$is_admin = $guser->role;
		//群主退出
		if($is_admin == 1){
			//删除环信群组
			$options = array(
					"client_id"=>CLIENT_ID,
					"client_secret"=>CLIENT_SECRET,
					"org_name"=>ORG_NAME,
					"app_name"=>APP_NAME
			);
			$huanxin = new Easemob($options);
			$resulh = $huanxin->deleteGroups($groups->huanxin_groupid);
			$reh = json_decode($resulh, true);//var_dump($reh);
			if(!$reh['error']){
				$connection = Yii::app()->db;
				$sql = "delete from group_member where contact_id = {$groupid}";
				$command = $connection->createCommand($sql);
				$result1 = $command->execute();
				if($result1){
					//$sql1 = "delete from groups where id = {$groupid}";
					$sql1 = "update groups set is_delete = 1 where id = {$groupid}";
					$command = $connection->createCommand($sql1);
					$result2 = $command->execute();
				}
			}
			
			if($result2){						
				$result['ret_num'] = 0;
				$result['ret_msg'] = '操作成功';
				echo json_encode( $result );
				die();
			}else{
				$result['ret_num'] = 113;
				$result['ret_msg'] = '退出群组失败';
				echo json_encode( $result );
				die();
			}
		}
		
		$groups->number = $groups->number - 1;
		if($groups->number == 0){			
			//删除环信群组
			$options = array(
					"client_id"=>CLIENT_ID,
					"client_secret"=>CLIENT_SECRET,
					"org_name"=>ORG_NAME,
					"app_name"=>APP_NAME
			);
			$huanxin = new Easemob($options);
			$resulh = $huanxin->deleteGroups($groups->huanxin_groupid);
			$reh = json_decode($resulh, true);//var_dump($reh);
			if(!$reh['error']){
				$guser->delete();
				$groups->is_delete = 1;
				$groups->update();
			}else{
				$fail = 1;
			}
		}else{			
			//退出环信群组
			$options = array(
					"client_id"=>CLIENT_ID,
					"client_secret"=>CLIENT_SECRET,
					"org_name"=>ORG_NAME,
					"app_name"=>APP_NAME
			);
			$huanxin = new Easemob($options);
			$resulh = $huanxin->delGroupsUser($groups->huanxin_groupid, $huanxin_user);
			$reh = json_decode($resulh, true);//var_dump($reh);
			if(!$reh['error']){
				$fail = 0;
				$guser->delete();
				$groups->update();
				//犇犇，退出群组的时候，后台加一个发送消息的功能
				//发送群组信息
				$info = $huanxin->yy_hxSend('admin', array($groups->huanxin_groupid), $nick_name.'已退出该群&XUNAOEXIT','chatgroups', array('benben'=>'benben'));
			}else{
				$fail = 1;
			}
			if($fail){
				$result['ret_num'] = 113;
				$result['ret_msg'] = '退出群组失败';
			}else{
				$result['ret_num'] = 0;
				$result['ret_msg'] = '操作成功';
			}						
		}
		
		/*if($guser->delete()){						
			$groups->number = $groups->number - 1;
			if($groups->number == 0){
				$groups->delete();
				//删除环信群组
				$options = array(
						"client_id"=>CLIENT_ID,
						"client_secret"=>CLIENT_SECRET,
						"org_name"=>ORG_NAME,
						"app_name"=>APP_NAME
				);
				$huanxin = new Easemob($options);
				$resulh = $huanxin->deleteGroups($groups->huanxin_groupid);
				$reh = json_decode($resulh, true);//var_dump($reh);
			}else{
				$groups->update();
				//退出环信群组
				$options = array(
						"client_id"=>CLIENT_ID,
						"client_secret"=>CLIENT_SECRET,
						"org_name"=>ORG_NAME,
						"app_name"=>APP_NAME
				);
				$huanxin = new Easemob($options);
				$resulh = $huanxin->delGroupsUser($groups->huanxin_groupid, $huanxin_user);
				$reh = json_decode($resulh, true);var_dump($reh);
				//犇犇，退出群组的时候，后台加一个发送消息的功能
				//发送群组信息
				$info = $huanxin->yy_hxSend('admin', array($groups->huanxin_groupid), $nick_name.'已退出该群&XUNAOEXIT','chatgroups', array('benben'=>'benben'));				
			}
									
			$result['ret_num'] = 0;
			$result['ret_msg'] = '操作成功';
		}else{
			$result['ret_num'] = 113;
			$result['ret_msg'] = '退出群组失败';
		}*/
		echo json_encode( $result );
	
	}
	
	/**
	 * 群组成员加入确认
	 * 受邀请加群，只需群组ID，由自己确认
	 * 自己申请加群，需要群组ID，会员ID，由群主确认
	 */
	public function actionJoin(){
		//加入环信群组
// 		$huanxin_groupid = "1429153059844123";
// 		$huanxin_username = "cbd41c6103064d3f0af848208c20ece2";
// 		$options = array(
// 				"client_id"=>CLIENT_ID,
// 				"client_secret"=>CLIENT_SECRET,
// 				"org_name"=>ORG_NAME,
// 				"app_name"=>APP_NAME
// 		);
// 		$huanxin = new Easemob($options);
// 		$resulh = $huanxin->addGroupsUser($huanxin_groupid, $huanxin_username);
// 		$reh = json_decode($resulh, true);var_dump($reh);var_dump($resulh);
// 		exit();
		$this->check_key();
		//$groupid = Frame::getIntFromRequest('group_id');
		//$memberid = Frame::getIntFromRequest('member_id');
		$huanxin_groupid = Frame::getStringFromRequest('huanxin_groupid');
		$huanxin_username = Frame::getStringFromRequest('huanxin_username');
		$user = $this->check_user();
		
		//$groups = Groups::model()->findByPk($groupid);
		$groups = Groups::model()->find("huanxin_groupid = '{$huanxin_groupid}' and is_delete = 0");
		if(empty($groups)){
			$result['ret_num'] = 109;
			$result['ret_msg'] = '该群组不存在';
			echo json_encode( $result );
			die ();
		}
		if($groups->number >= 500){
			$result['ret_num'] = 5234;
			$result['ret_msg'] = '群组成员已加满';
			echo json_encode( $result );
			die ();
		}
		
		$applyid = $user;
		//$applyid = $user->huanxin_username;
		if($huanxin_username){
			//$ouser = Member::model ()->findByPk($memberid);
			$ouser = Member::model ()->find("huanxin_username = '{$huanxin_username}'");
			if (empty( $ouser )) {
				$result['ret_num'] = 1001;
				$result['ret_msg'] = '该用户不存在';
				echo json_encode( $result );
				die ();
			}
			$applyid = $ouser;
		}
		$guser = GroupMember::model ()->find("contact_id = {$groups->id} and member_id = {$applyid->id}");				
		if(!$guser){
			$nuser = new GroupMember();
			$nuser->contact_id = $groups->id;
			$nuser->member_id = $applyid->id;
			$nuser->status = 1;
			if($huanxin_username){
				$nuser->invite_member = $user->id;
			}			
			$nuser->created_time = time();
			
			if($nuser->save()){
				$groups->number = $groups->number + 1;
				$groups->update();
				//加入环信群组
				$options = array(
						"client_id"=>CLIENT_ID,
						"client_secret"=>CLIENT_SECRET,
						"org_name"=>ORG_NAME,
						"app_name"=>APP_NAME
				);
				$huanxin = new Easemob($options);
				$resulh = $huanxin->addGroupsUser($groups->huanxin_groupid, $applyid->huanxin_username);
				$reh = json_decode($resulh, true);//var_dump($resulh);
// 				$file = fopen("log.txt", "a+");
// 				fwrite($file, $resulh."\n");
// 				fclose($file);
				//犇犇，退出群组的时候，后台加一个发送消息的功能
				//发送群组信息
				$info = $huanxin->yy_hxSend('admin', array($groups->huanxin_groupid), $applyid->nick_name.'已经加入群组&XUNAOEXIT','chatgroups', array('benben'=>'benben'));
				$this->addIntegral($user->id, 5);
				$result['ret_num'] = 0;
				$result['ret_msg'] = '操作成功';
			}else{
				$result['ret_num'] = 108;
				$result['ret_msg'] = '加入群组失败';
			}
		}else{
			$result['ret_num'] = 107;
			$result['ret_msg'] = '已加入该群组';				
		}
		echo json_encode( $result );
		
	}
	
	/**
	 * 群组成员加入(批量)
	 */
	public function actionJoinmore(){
		
		$this->check_key();
		$member_id = Frame::getStringFromRequest('member_id');		
		$huanxin_groupid = Frame::getStringFromRequest('huanxin_groupid');
		if(empty($member_id)){
			$result['ret_num'] = 1090;
			$result['ret_msg'] = '邀请的用户ID为空';
			echo json_encode( $result );
			die ();
		}
		$user = $this->check_user();
		$member_id_arr = explode(",", $member_id);
		$member_id = implode(",", $member_id_arr);
		$connection = Yii::app()->db;
		$sql = "select nick_name,huanxin_username from member where id in ({$member_id})";
		$command = $connection->createCommand($sql);
		$result0 = $command->queryAll();
		if($result0){
			$huanxin_username = array();
			$nick_name = array();
			foreach ($result0 as $value){
				$huanxin_username[] = $value['huanxin_username'];
				$nick_name[] = $value['nick_name'];
			}
		}else{
			$result['ret_num'] = 1091;
			$result['ret_msg'] = '邀请的用户信息不存在';
			echo json_encode( $result );
			die ();
		}
		
		$groups = Groups::model()->find("id = '{$huanxin_groupid}' and is_delete = 0");
		if(empty($groups)){
			$result['ret_num'] = 109;
			$result['ret_msg'] = '该群组不存在';
			echo json_encode( $result );
			die ();
		}
		if($groups->number >= 500){
			$result['ret_num'] = 5234;
			$result['ret_msg'] = '群组成员已加满';
			echo json_encode( $result );
			die ();
		}
		//是否群主
		// $guser = GroupMember::model ()->find("contact_id = {$groups->id} and member_id = {$user->id} and role = 1");
		// if(!$guser){
		// 	$result['ret_num'] = 107;
		// 	$result['ret_msg'] = '不是群主';
		// 	echo json_encode( $result );
		// 	die ();
		// }
		
		$t = time();
		$con = array();
		foreach ($member_id_arr as $val){
			$con[] = "({$groups->id},{$val},0,{$t},1,{$user->id})";
		}
		$sqla = "insert into group_member (contact_id,member_id,role,created_time,status,invite_member) values ".implode(",", $con);
		$command = $connection->createCommand($sqla);
		$result1 = $command->execute();
		
		if($result1){		//$t1 = time();
				$groups->number = $groups->number + count($member_id_arr);
				$groups->update();
				//加入环信群组				
				$options = array(
						"client_id"=>CLIENT_ID,
						"client_secret"=>CLIENT_SECRET,
						"org_name"=>ORG_NAME,
						"app_name"=>APP_NAME
				);
				$huanxin = new Easemob($options);
				$resulh = $huanxin->addGroupsUserA($groups->huanxin_groupid, $huanxin_username);
				$reh = json_decode($resulh, true);//var_dump($resulh);
				// 				$file = fopen("log.txt", "a+");
				// 				fwrite($file, $resulh."\n");
				// 				fclose($file);
				//$t2 = time();
	//var_dump($t2-$t1);
				//发送群组信息
				$info = $huanxin->yy_hxSend('admin', array($groups->huanxin_groupid), implode(",", $nick_name).'已经加入群组&XUNAOEXIT','chatgroups', array('benben'=>'benben'));				
				$result['ret_num'] = 0;
				$result['ret_msg'] = '操作成功';
			}else{
				$result['ret_num'] = 108;
				$result['ret_msg'] = '加入群组失败';
			}
	
		echo json_encode( $result );
	
	}
	
	/**
	 * 查看群组成员
	 */
	public function actionMember(){
	    $this->check_key();
		$groupid = Frame::getIntFromRequest('group_id');
		$user = $this->check_user();
		
		$groups = Groups::model()->find("id = {$groupid} and is_delete = 0");
		if(empty($groups)){
			$result['ret_num'] = 109;
			$result['ret_msg'] = '该群组不存在';
			echo json_encode( $result );
			die ();
		}
		
		$guser = GroupMember::model ()->find("contact_id = {$groupid} and member_id = {$user->id} and status = 1");
		if(empty($guser)){
			$result['ret_num'] = 112;
			$result['ret_msg'] = '已退出该群组';
			echo json_encode( $result );
			die ();
		}
		
		$connection = Yii::app()->db;
		$sql = "select a.nick_name group_nick_name,a.role,a.status,a.invite_member,b.id,b.poster,b.nick_name,b.name,b.sex,b.age,b.phone,b.huanxin_username from group_member a inner join member b on a.member_id=b.id where contact_id = {$groupid} order by a.role desc";
		$command = $connection->createCommand($sql);
		$result0 = $command->queryAll();
		$contactName = $this->getContactIdName($user->id);
		foreach ($result0 as $key => $value){
			if($value['role'] && ($value['status'] == 1)){
				$is_admin = 1;
			}else{
				$is_admin = 0;
			}
			
			$result0[$key]['poster'] = $value['poster'] ? URL.$value['poster']:"";
			$result0[$key]['name'] = $value['name'] ? $value['name']:"";
			$groupNickname = $value['group_nick_name'];
			if (!empty($contactName[$value['id']])) {
				//最优先通讯录名字
				$groupNickname = $contactName[$value['id']];
			}elseif (empty($groupNickname)) {
				//没有备注，最后显示昵称
				$groupNickname = $value['nick_name'];
			}
			// if (!$groupNickname) {
			// 	if (isset($contactName[$value['id']])) {
			// 		$groupNickname = $contactName[$value['id']];
			// 	}else{
			// 		$groupNickname = $value['nick_name'];
			// 	}
			// }
			$result0[$key]['group_nick_name'] = $groupNickname;
			$result0[$key]['is_admin'] = $is_admin;
		}
		$result['ret_num'] = 0;
		$result['ret_msg'] = '操作成功';
		$result['member_info'] = $result0;					
		echo json_encode( $result );
	
	}
	
	/**
	 * 根据环信群组ID查看群组成员
	 */
	public function actionMemberh(){
		$this->check_key();
		$huanxin_groupid = Frame::getIntFromRequest('huanxin_groupid');
		$user = $this->check_user();
	
		$groups = Groups::model()->find("huanxin_groupid = {$huanxin_groupid} and is_delete = 0");
		if(empty($groups)){
			$result['ret_num'] = 109;
			$result['ret_msg'] = '该群组不存在';
			echo json_encode( $result );
			die ();
		}
	
		$guser = GroupMember::model ()->find("contact_id = {$groups->id} and member_id = {$user->id} and status = 1");
		if(empty($guser)){
			$result['ret_num'] = 112;
			$result['ret_msg'] = '已退出该群组';
			echo json_encode( $result );
			die ();
		}
	
		$connection = Yii::app()->db;
		//通讯录里的犇犇好友
		$sqlf = "select a.is_benben,b.benben_id,b.name from group_contact_phone a right join group_contact_info b on a.contact_info_id = b.id where b.member_id = {$user->id} and (a.is_benben>0 or b.benben_id>0)";
		$command = $connection->createCommand($sqlf);
		$fried_array = $command->queryAll();
		$farray = array();
		foreach ($fried_array as $key => $value) {
			$item_phone = $value['is_benben'] ? $value['is_benben'] : $value['benben_id'];
			$item_name = $value['name'];
			if (empty($farray[$item_phone])) {
				//手机好重复，保留第一个名字
				$farray[$item_phone] = $item_name;
			}
		}

		$sql = "select a.nick_name group_nick_name,a.invite_member,b.id,b.poster,b.nick_name,b.name,b.sex,b.age,b.phone,b.huanxin_username,b.benben_id from group_member a inner join member b on a.member_id=b.id where a.contact_id = {$groups->id}";
		$command = $connection->createCommand($sql);
		$result0 = $command->queryAll();
		$phone_friend = array();
		foreach ($result0 as $key => $value){
			$result0[$key]['poster'] = $value['poster'] ? URL.$value['poster']:"";
			$result0[$key]['name'] = $value['name'] ? $value['name']:"";
			//用来取通讯录名称
			if (!empty($farray[$value['benben_id']])) {
				//通讯录名称最优先--名片--昵称
				$result0[$key]['group_nick_name'] = $farray[$value['phone']];
			}else {
				$result0[$key]['group_nick_name'] = $value['group_nick_name'] ? $value['group_nick_name']:$value['nick_name'];
				$phone_friend[$key] = $value['benben_id'];
			}
		}

		if (count($phone_friend) > 0) {
			$sql_content = "select a.phone,b.name from group_contact_phone a left join group_contact_info b on a.contact_info_id = b.id where b.member_id = {$user->id} and (a.is_benben in (".implode(",",$phone_friend).") or b.benben_id in (".implode(",",$phone_friend)."))";
			$command = $connection->createCommand($sql_content);
			$res2 = $command->queryAll();
			$pname = array();
			foreach ($res2 as $val){
				$item_name = $val['name'];
				$item_phone = $val['phone'];
				$pname[$item_phone] = $item_name;
			}
			foreach ($phone_friend as $key => $value) {
				$result0[$key]['group_nick_name'] = empty($pname[$value]) ? $result0[$key]['group_nick_name']:$pname[$value];
			}
		}

		$result['ret_num'] = 0;
		$result['ret_msg'] = '操作成功';
		$result['member_info'] = $result0;
		echo json_encode( $result );
	
	}
	
	/**
	 * 发送信息
	 */
	public function actionSend(){
		$from_user = "admin";
		$username[] = "123456"; 
		$content = "苹果刚刚对JDI投资了近10亿美元的资助，后者将为iPhone提供基于LTPS的LCD面板，而这才是接下来iPhone要使用的";
		$target_type = "users";
		$ext = array("attr1"=>"v1");
		$options = array(
						"client_id"=>CLIENT_ID,
						"client_secret"=>CLIENT_SECRET,
						"org_name"=>ORG_NAME,
						"app_name"=>APP_NAME
				);
		$huanxin = new Easemob($options);
		$re = $huanxin->yy_hxSend($from_user, $username, $content, $target_type, $ext);
		var_dump($re);
		
	}
	
	function chatGroups($parameter, $header){
		$option['client_id'] = 'YXA6hYUeUMCoEeSLzs9YqkHScQ';
		$option['client_secret'] = 'YXA6fC_v-if7CLg62Ti-kt9zqsOzdDo';
		$option['org_name'] = 'benben2015';
		$option['app_name'] = 'benben';
		
		//"groupname":"testrestgrp12", //群组名称, 此属性为必须的
		//"desc":"server create group", //群组描述, 此属性为必须的
		//"public":true, //是否是公开群, 此属性为必须的
		//"maxusers":300, //群组成员最大数(包括群主), 值为数值类型,默认值200,此属性为可选的
		//"approval":true, //加入公开群是否需要批准, 没有这个属性的话默认是true（不需要群主批准，直接加入）, 此属性为可选的
		//"owner":"jma1", //群组的管理员, 此属性为必须的
		//"members":["jma2","jma3"] //群组成员,此属性为可选的,但是如果加了此项,数组元素至少一个（注：群主jma1不需要写入到members里面）
		$url = 'https://a1.easemob.com/' . $option['org_name'] . '/' . $option['app_name'] . '/chatgroups/1427966775641948/users/1b9273c879ebca98d66a8f273d66de47';
		$curl = curl_init (); // 启动一个CURL会话
		curl_setopt ( $curl, CURLOPT_URL, $url ); // 要访问的地址
		curl_setopt ( $curl, CURLOPT_SSL_VERIFYPEER, FALSE ); // 对认证证书来源的检查
		curl_setopt ( $curl, CURLOPT_SSL_VERIFYHOST, FALSE ); // 从证书中检查SSL加密算法是否存在
		curl_setopt ( $curl, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0; Trident/4.0)' ); // 模拟用户使用的浏览器
		if (! empty ( $parameter )) {
			$options = json_encode ( $parameter );
			curl_setopt ( $curl, CURLOPT_POSTFIELDS, $options ); // Post提交的数据包
		}
		curl_setopt ( $curl, CURLOPT_TIMEOUT, 30 ); // 设置超时限制防止死循环
	    curl_setopt ( $curl, CURLOPT_HTTPHEADER, $header ); // 设置HTTP头
		curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, 1 ); // 获取的信息以文件流的形式返回
		curl_setopt ( $curl, CURLOPT_CUSTOMREQUEST, 'POST' );
		$result = curl_exec ( $curl ); // 执行操作
	
		curl_close ( $curl ); // 关闭CURL会话
		return $result;
	}
	
	/**
	邀请成员列表
	*/
	public function actionInviteMember()
	{
		$connection = Yii::app()->db;
		$this->check_key();
		$user = $this->check_user();
		$group_id = Frame::getIntFromRequest('group_id');
		$type = Frame::getIntFromRequest('type');
		$member_id = $user->id;
		if(empty($group_id)){
			$result['ret_num'] = 1052;
			$result['ret_msg'] = '群组为空';
			echo json_encode( $result );
			die ();
		}
		//获取分组
		$sql1 = "select id,group_name name from group_contact where member_id = {$user->id} order by id asc";
		$command = $connection->createCommand($sql1);
		$result1 = $command->queryAll();
		$result_group = array();
		$groupId = array();
		if($result1){
			$temp = "";
			foreach ($result1 as $value){
				if($value['name'] != "未分组"){
					$groupId[] = $value['id'];
					$result_group[$value['id']] = $value['name'];
				}else{
					$temp=$value;
				}
			}
			$groupId[] = $temp['id'];
			$result_group[$temp['id']]="未分组";
		}
		$sql = "select member_id from group_member where status = 1 and contact_id  = ".$group_id;
		$command = $connection->createCommand($sql);
		$allMember = $command->queryAll();
		$allMemberInfo = array();
		if (count($allMember) > 0) {
			foreach($allMember as $e){
				$allMemberInfo[] = $e['member_id'];
			}
		}

		//$benbenName = $this->getContactIdName($user->id,1);
		$benbenName = $this->myfriend($user->id,1);
		$fri = array();
		if (count($benbenName) > 0) {
			foreach($benbenName as $k => $e){
				if (!in_array($k, $allMemberInfo) && $k) {
					$fri[] = $k;
				}
				
			}
		}
		
		$PinYin = new PYInitials('utf8');
		$member_list = array();
		if (count($fri)) {
			$sql = "select id, name, poster, nick_name, phone, benben_id from member where id in (".implode(",", $fri).")";
			$command = $connection->createCommand($sql);
			$info = $command->queryAll();
			if ($info) {
				foreach ($info as $key => $value) {
					$name = $value['name']?$value['name']:$value['nick_name'];
					if (isset($benbenName[$value['id']][0])) {
						$name = $benbenName[$value['id']][0];
					}
					$member_list[] = array(
						'id'=>$value['id'], 
						'group_id'=>$benbenName[$value['id']][1],
						// 'phone'=>$value['phone'], 
						'phone'=>$value['benben_id'], 
						'name'=>$name, 
						'benben_id'=>$value['benben_id'], 
						'pinyin'=>substr($PinYin->getInitials($name), 0, 1),
						'poster'=>$value['poster'] ? URL.$value['poster'] : ""
						);
				}
			}
			
			$member_list1 = array();
			foreach ($result_group as $key => $value) {
				$currentMember = array();
				foreach ($member_list as $va){
					if($va['group_id'] == $key){
						$currentMember[] = $va;
					}
				}
				$member_list1[] = array('id'=>$key, 'name'=>$value."(".count($currentMember)."人)", 'member'=>$currentMember);											
			}
			
		}
		$result['ret_num'] = 0;
		$result['ret_msg'] = '操作成功';
		$result['member_list'] = $type ? $member_list1: $member_list;
		echo json_encode( $result );

	}
}