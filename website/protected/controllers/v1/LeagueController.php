<?php
class LeagueController extends PublicController
{
	public $layout = false;
	/**
	 * 我的好友联盟
	 */
	public function actionMyleague(){
		$this->check_key();
		$user = $this->check_user();
	
		$connection = Yii::app()->db;
		if($keyword){
			$sql = "select id,name,poster,number,created_time  from friend_league where member_id = {$user->id} and name like '%{$keyword}%'  order by created_time desc limit 50";
		}else{
		    $sql = "select id,name,poster,number,created_time  from friend_league where member_id = {$user->id}  order by created_time desc limit 50";
		}
		$command = $connection->createCommand($sql);
		$enterprise = $command->queryAll();
		$enterpriseall = array();
		if($enterprise){
		foreach ($enterprise as $value){
			$enterp = array(
			"id"=>$value['id'],
			"name"=>$value['name'],
			"number"=>$value['number'],
			"poster"=>$value['poster'] ? URL.$value['poster'] : "",
			"created_time"=>$value['created_time'],
					);
			$enterpriseall[] = $enterp;
		}
		}
	
		$result['ret_num'] = 0;
		$result['ret_msg'] = '操作成功';
		$result['enterprise_list'] = $enterpriseall;
		echo json_encode( $result );	
		
	}

	public function getLeagueCount($leagueid)
	{
		if (empty($leagueid)) {
			return 0;
		}
		$result = LeagueMember::model()->count("league_id=".$leagueid);
		return $result;
	}

	/*
	 * 小喇叭好友联盟消息显示，type=1工作联盟，type=2英雄联盟
	 * 工作联盟需要显示成员
	 */
	public function actionMyleagueinfo()
	{
		$this->check_key();
		$user = $this->check_user();
		$connection = Yii::app()->db;
		$sql = "select id,name,poster,number,type,created_time  from friend_league where member_id = {$user->id} and status=0 and is_delete = 0 order by created_time desc";
		$command = $connection->createCommand($sql);
		$enterprise = $command->queryAll();

		//好友联盟不存在的
		if (!$enterprise) {
			$result['ret_num'] = 0;
			$result['ret_msg'] = '操作成功';
			$result['info'] = array();
			echo json_encode( $result );
			die();
		}

		//区分1、2,1显示成员
		$chief=array();//普通成员数组
		$other_chief=array();//堂组成员数组
		foreach($enterprise as $k=>$v){
			if($v['type']==1){
				$sql_lm="select a.id,a.league_id,a.member_id,a.type,a.remark_content,a.nick_name,b.benben_id,b.poster from league_member as a
				left join member as b on a.member_id=b.id where a.league_id={$v['id']} and a.status=1 and a.type!=0";
				$command = $connection->createCommand($sql_lm);
				$leginfo[$v['id']] = $command->queryAll();
			}
		}

		//需要显示成员数据的处理
		if($leginfo) {
			$other_chief=array();
			$chief=array();
			//显示每个好友联盟
			foreach ($leginfo as $kl => $vl) {
				//修改昵称，组备注，通讯录昵称，注册昵称
				$nick_name_all = $this->getfriend($user['id'], 3);

				//获取堂主id
				foreach($vl as $kt=>$vt){
					if ($vt['type'] == 1){
						$tang[]=$vt['member_id'];
					}
				}

				//显示成员，每个好友联盟的信息
				foreach($vl as $km=>$vm) {
					//照片地址补全
					$leginfo[$kl][$km]['poster']=$vm['poster']?URL.$vm['poster']:"";

					if ($nick_name_all[$vm['member_id']]) {
						$nick_name = $nick_name_all[$vm['member_id']];
					} else {
						$userinfo = Member::model()->find("id={$vm['member_id']}");
						$nick_name = $userinfo['nick_name'];
					}
					$leginfo[$kl][$km]['nick_name'] = $vm['nick_name'] ? $vm['nick_name'] : $nick_name;
					//盟主成员，2
					if ($vm['type'] == 2 && $vm['remark_content']==$user['id']) {
						$leginfo[$kl][$km]['groupname']="盟主成员";
						$chief[$kl][] = $leginfo[$kl][$km];
					}
					//堂主及堂成员,1
					if($tang) {
						foreach ($tang as $ks) {
							if ($vm['remark_content'] == $ks) {
								$member[$ks][] = $leginfo[$kl][$km];
							}
						}
						if($vm['type'] == 1){
							$tpl_lm = LeagueMemberData::model()->find("data_id={$vm['id']}");
							$groupname[$vm['member_id']] = $tpl_lm['name'] ? $tpl_lm['name'] : "堂";
							$member[$vm['member_id']][]=$leginfo[$kl][$km];
						}
					}
				}
				//堂信息组织
				if($tang) {
					foreach ($tang as $tk) {
						$other_chief[$kl][] = array("member" => $member[$tk], "name" => $groupname[$tk]);
					}
				}
			}
		}
		$info = array();
		if ($enterprise) {
			foreach($enterprise as $kg=>$vg){
				$vg['chief_num']=count($chief) ? count($chief) : 0;
				$vg['other_chief_num']=count($member) ? count($member) : 0;
				$vg['chief']=$chief ? $chief[$vg['id']] : array();
				$vg['other_chief']=$other_chief ? $other_chief[$vg['id']] : array();
				$vg["poster"]=$vg["poster"]?URL.$vg["poster"]:"";
				$count = $this->getLeagueCount($vg['id']);
				$vg['number'] = $count-1;
				$info[]=$vg;
			}
		}
		$result['ret_num'] = 0;
		$result['ret_msg'] = '操作成功';
		$result['info'] = $info;
		echo json_encode( $result );
	}

	/*
	 *拒绝好友联盟请求
	 * 输入leg_id
	 * 涉及news表
	 */
	public function actionRejectleague(){
		$this->check_key();
		$user = $this->check_user();
		$legid = Frame::getIntFromRequest('legid');
		if(empty($legid)){
			$result['ret_num'] = 100;
			$result['ret_msg'] = '缺少参数';
			echo json_encode( $result );
			die();
		}
		$friendinfo=FriendLeague::model()->find("id={$legid} and is_delete=0 and status=0");
		if(empty($friendinfo)){
			$result['ret_num'] = 1050;
			$result['ret_msg'] = '该好友联盟可能被禁用或者删除';
			echo json_encode( $result );
			die();
		}
		//环信和消息提醒盟主
		//查询盟主环信号
		$member_id=$friendinfo['member_id'];
		$userinfo=Member::model()->find("id={$member_id} and id_enable=1");
		if(empty($userinfo['huanxin_username'])){
			$result['ret_num'] = 1055;
			$result['ret_msg'] = '该用户暂未开通聊天功能或已被禁用';
			echo json_encode( $result );
			die();
		}
		$content="{$user['nick_name']}拒绝成为联盟{$friendinfo['name']}的堂主";
		$t=time();
		$sql="insert into news (type,sender,member_id,content,status,created_time,display) VALUES (5,{$user->id},{$userinfo['id']},'{$content}',0,{$t},0)";
		$connection=Yii::app()->db;
		$command=$connection->createCommand($sql);
		$result1=$command->execute();

		//发送环信信息通知
		$other_arr=array(
			"nick_name"=>$user->nick_name,
			"user_poster"=>$user->poster?URL.$user->poster:"",
			"hxname"=>$user->huanxin_username,
			"time"=>time(),
			"t1"=>1,
			"t2"=>1,
			"t3"=>2,
			"t4"=>1,
		);
		$this->sendHXMessage(array(0=>$userinfo['huanxin_username']),$content,$other_arr);

		$result ['ret_num'] = 0;
		$result ['ret_msg'] = '操作成功';
		echo json_encode($result);
	}

	/*
	 * 好友联盟详情
	 * 传入legid
	 * 返回好友联盟详情信息
	 */
	public function actionLeaguedetail()
	{
		$this->check_key();
		$user = $this->check_user();
		$legid = Frame::getIntFromRequest('legid');
		$connection = Yii::app()->db;//b.status = 0 and
		$asql = "select b.id, b.name, b.poster, b.number, b.description, b.created_time, b.province, b.city, b.area, b.announcement, b.type as category,b.change_time,b.status
				from friend_league as b where b.is_delete = 0 and b.status =0 and  b.id = {$legid}";
		$command = $connection->createCommand($asql);
		$enterprise = $command->queryAll();
		if ($enterprise) {
			$lminfo=LeagueMember::model()->find("league_id={$legid} and member_id={$user->id}");
			$areaId = array();
			$idarray = array();
			foreach ($enterprise as $value) {
				$areaId[] = $value['province'];
				$areaId[] = $value['city'];
				$areaId[] = $value['area'];
				$idarray[] = $value['id'];
			}
			$areaInfo = array();
			if (count($areaId) > 0) {
				$sql = "select bid, area_name from area where bid in(" . implode(",", $areaId) . ")";
				$command = $connection->createCommand($sql);
				$areaResult = $command->queryAll();
				if ($areaResult) {
					foreach ($areaResult as $a) {
						$areaInfo[$a['bid']] = $a['area_name'];
					}
				}

			}
			$countInfo = array();
			if (count($idarray) > 0) {
				$sql = 'select count(*) as number,league_id from league_member where league_id in (' . implode(",", $idarray) . ') group by league_id';
				$command = $connection->createCommand($sql);
				$countResult = $command->queryAll();
				if ($countResult) {
					foreach ($countResult as $val) {
						$countInfo[$val['league_id']] = $val['number'];
					}
				}
			}

			foreach ($enterprise as $value) {
				$areaString = '';
				$full_areaString = '';
				if (isset($areaInfo[$value['province']])) {
					$areaString = $areaInfo[$value['city']] . "  " . $areaInfo[$value['area']];
					$full_areaString = $areaInfo[$value['province']] . "  " . $areaInfo[$value['city']] . "  " . $areaInfo[$value['area']];
				}
				$number = $value['number'];
				if (isset($countInfo[$value['id']])) {
					$number = $countInfo[$value['id']];
				}

				$enterp = array(
					"id" => $value['id'],
					"name" => $value['name'],
					"poster" => $value['poster'] ? URL . $value['poster'] : "",
					// "number"=>$value['number'],
					"number" => $number,
					"remark_content" => $lminfo['remark_content'],
					"nickname" =>"",
					"description" => $this->eraseNull($value['description']),
					"announcement" => $this->eraseNull($value['announcement']),
					"type" =>$lminfo['type'],
					"category" => $value['category'],
					"area" => $areaString,
					"full_area" => $full_areaString,
					"status" => $value['status'],
					"created_time" => $value['created_time'],
					"change_time" => $value['change_time'] ? $value['change_time'] : "" ,
				);
				$enterpriseall[] = $enterp;
			}
		}
		$result['ret_num'] = 0;
		$result['ret_msg'] = '操作成功';
		$result['enterprise_list'] = $enterpriseall;
		echo json_encode( $result );
	}

	/**
	 * 我加入的好友联盟{"ret_num":0,"ret_msg":"\u64cd\u4f5c\u6210\u529f","enterprise_list":[{"id":"2","name":"\u5927\u5bb6\u597d\u554a","poster":"","number":"1","created_time":"1429522792"},{"id":"1","name":"13333333334","poster":"","number":"1","created_time":"1429522509"}]}
	 */
	public function actionMyleaguein(){
		$this->check_key();
		$user = $this->check_user();
		$connection = Yii::app()->db;//b.status = 0 and
		// $asql = "select a.type, b.id, b.name, b.poster, b.number, a.remark_content,a.nick_name, b.description, b.created_time, b.province, b.city, b.area, b.announcement, b.type as category from league_member as a left join friend_league as b on a.league_id = b.id where b.status = 0 and a.status = 1 and  a.member_id = {$user->id} order by a.type";
		$asql = "select a.type, b.id, b.name, b.poster, b.number, a.remark_content,a.nick_name, b.description, b.created_time, b.province, b.city, b.area, b.announcement, b.type as category, b.change_time, b.status
				from league_member as a left join friend_league as b on a.league_id = b.id where b.is_delete = 0 and a.status = 1 and  a.member_id = {$user->id} order by a.type";
		$command = $connection->createCommand($asql);
		$enterprise = $command->queryAll();
		if($enterprise){
				$areaId = array();
				$idarray = array();
				foreach ($enterprise as $value){
					$areaId[] = $value['province'];
					$areaId[] = $value['city'];
					$areaId[] = $value['area'];
					$idarray[] = $value['id'];
				}
				$areaInfo = array();
				if (count($areaId) > 0) {
					$sql = "select bid, area_name from area where bid in(".implode(",", $areaId).")";
					$command = $connection->createCommand($sql);
					$areaResult = $command->queryAll();
					if ($areaResult) {
						foreach($areaResult as $a){
							$areaInfo[$a['bid']] = $a['area_name'];
						}
					}

				}
				$countInfo = array();
				if (count($idarray) > 0) {
					$sql = 'select count(*) as number,league_id from league_member where league_id in ('.implode(",", $idarray).') group by league_id';
					$command = $connection->createCommand($sql);
					$countResult = $command->queryAll();
					if ($countResult) {
						foreach($countResult as $val){
							$countInfo[$val['league_id']] = $val['number'];
						}
					}
				}

				foreach ($enterprise as $value){
					$areaString = '';
					$full_areaString = '';
					if (isset($areaInfo[$value['province']])) {
						$areaString = $areaInfo[$value['city']]."  ".$areaInfo[$value['area']];
						$full_areaString = $areaInfo[$value['province']]."  ".$areaInfo[$value['city']]."  ".$areaInfo[$value['area']];
					}
					$number = $value['number'];
					if (isset($countInfo[$value['id']])) {
						$number = $countInfo[$value['id']];
					}

					$enterp = array(
							"id"=>$value['id'],
							"name"=>$value['name'],
							"poster"=>$value['poster'] ? URL.$value['poster'] : "",
							// "number"=>$value['number'],
							"number"=>$number,
							"remark_content"=>$this->eraseNull($value['remark_content']),
							"nickname"=>$value['nick_name'],
							"description"=>$this->eraseNull($value['description']),
							"announcement"=>$this->eraseNull($value['announcement']),
							"type"=>$value['type'],
							"category"=>$value['category'],
							"area"=>$areaString,
							"full_area"=>$full_areaString,
							"status"=>$value['status'],
							"created_time"=>$value['created_time'],
							"change_time" => $value['change_time'] ? $value['change_time'] : "" ,
					);
					$enterpriseall[] = $enterp;
				}
				
			}
	
	
		$result['ret_num'] = 0;
		$result['ret_msg'] = '操作成功';
		$result['enterprise_list'] = $enterpriseall;
		echo json_encode( $result );
	
	}
	
	/**修改我的名片**/
	public function actionEditNickName(){
		$this->check_key();
		$user = $this->check_user();
		$nickname = Frame::getStringFromRequest('nickname');
		$leagueid = Frame::getIntFromRequest('leagueid');
		if(empty($leagueid)){
			$result['ret_num'] = 1503;
			$result['ret_msg'] = '好友联盟ID为空';
			echo json_encode( $result );
			die();
		}
		if (empty($nickname)) {
			// $result['ret_num'] = 1506;
			// $result['ret_msg'] = '名片不能够为空';
			// echo json_encode( $result );
			// die();
			$nickname = '';
		}

		$league_member = LeagueMember::model()->find("league_id = {$leagueid} and member_id = {$user->id}");
		if ($league_member) {
			$league_member->nick_name = $nickname;
			$league_member->update();

			$result['nickname'] = $nickname;
			$result['ret_num'] = 0;
			$result['ret_msg'] = '操作成功';
		}else {
			$result['ret_num'] = 1520;
			$result['ret_msg'] = '不在该联盟内';
		}
		echo json_encode( $result );
	}

	/**
	 * 修改好友联盟备注
	 */
	public function actionEditremark(){
		$this->check_key();
		$remarkname = Frame::getStringFromRequest('remarkname');	//堂主、盟主备注名
		$name = Frame::getStringFromRequest('name');  				//堂备注名
		$leagueid = Frame::getIntFromRequest('leagueid');
		$memberid = Frame::getIntFromRequest('memberid');			//修改主体的ID
		if(empty($leagueid)){
			$result['ret_num'] = 1503;
			$result['ret_msg'] = '好友联盟ID为空';
			echo json_encode( $result );
			die();
		}
		// if (!$remarkname && !$name) {
		// 	$result['ret_num'] = 1506;
		// 	$result['ret_msg'] = '修改内容不能够为空';
		// 	echo json_encode( $result );
		// 	die();
		// }
				
		$user = $this->check_user();
		$league_info = FriendLeague::model()->find("id = {$leagueid} and is_delete = 0");
		if(empty($league_info)){
			$result['ret_num'] = 1506;
			$result['ret_msg'] = '好友联盟不存在';
			echo json_encode( $result );
			die();
		}
		if (!$memberid) {
			$memberid  = $user->id;
		}
		$league_member = LeagueMember::model()->find("league_id = {$leagueid} and member_id = {$memberid} and type <= 1");
		if($league_member){
			$league_member_data = LeagueMemberData::model()->find('data_id = '.$league_member->id." and league_id = ".$leagueid);
			if ($league_member_data) {
				if ($remarkname) {
					$league_member_data->remark_name = $remarkname;
				}
				if ($name) {
					$league_member_data->name = $name;
				}
				$league_member_data->update();
			}else{
				$league_member_data = new LeagueMemberData();
				$league_member_data->data_id = $league_member->id;
				$league_member_data->remark_name = $remarkname;
				$league_member_data->name = $name;
				$league_member_data->league_id = $leagueid;
				$league_member_data->save();
			}
			
			
			$result['ret_num'] = 0;
			$result['ret_msg'] = '操作成功';
			// $result['remark_content'] = $remarkname;
		}else{
			$result['ret_num'] = 1520;
			$result['ret_msg'] = '不在该联盟内';
		}
		echo json_encode( $result );
	}
	
	/**
	 * 新建好友联盟{"ret_num":0,"ret_msg":"\u64cd\u4f5c\u6210\u529f","enterprise_info":{"id":"3","name":"\u5927\u5bb6\u597d\u554a","description":"\u8054\u7cfb","number":1,"created_time":1429522902}}
	 */
	public function actionAdd(){
		$this->check_key();
		$name = Frame::getStringFromRequest('name');
		$poster = Frame::saveImage("poster");
		$description = Frame::getStringFromRequest('description');
		$province = Frame::getIntFromRequest('province');
		$city = Frame::getIntFromRequest('city');
		$type = Frame::getIntFromRequest('type');
		$area = Frame::getIntFromRequest('area');
		
		$user = $this->check_user();
		$connection = Yii::app()->db;
		//只能够创建一个好友联盟
		$info = FriendLeague::model()->find("member_id = {$user->id} and is_delete = 0");
		if ($info) {
			$result['ret_num'] = 1501;
			$result['ret_msg'] = '您已经创建了好友联盟';
			echo json_encode( $result );
			die();
		}
		$sql = "select count(*) c from league_member a left join friend_league b on a.league_id = b.id where a.member_id =  {$user->id} and a.type = 1 and b.is_delete = 0";
		$command = $connection->createCommand($sql);
		$isLeagueMember = $command->queryAll();
		if ($isLeagueMember && $isLeagueMember[0]['c'] >= 1) {
			$result['ret_num'] = 1501;
			$result['ret_msg'] = '您已经加入了好友联盟';
			echo json_encode( $result );
			die();
		}
		if(empty($name)){
			$result['ret_num'] = 1501;
			$result['ret_msg'] = '好友联盟名称为空';
			echo json_encode( $result );
			die();
		}else if(!$province || !$city || !$area){
			$result['ret_num'] = 1505;
			$result['ret_msg'] = '省市区不能为空';
			echo json_encode( $result );
			die();
		}

		$enterprise_info = new FriendLeague();
		$enterprise_info->name = $name;
		$enterprise_info->poster = $poster;
		$enterprise_info->member_id = $user->id;
		$enterprise_info->description = $description;
		$enterprise_info->number = 1;
		$enterprise_info->province = $province;
		$enterprise_info->city = $city;
		$enterprise_info->area = $area;
		$enterprise_info->type = $type;
		$enterprise_info->created_time = time();
		if($enterprise_info->save()){
		
			//将自己加入通讯录成员
			$con = new LeagueMember();
			$con->league_id = $enterprise_info->id;
			$con->member_id = $user->id;
			$con->status = 1;
			$con->created_time = time();
			$con->save();

			// if($type == 2){
			// 	$benbenName = $this->getBenbenName($user->id);
			// 	$benbenId = array();
			// 	if (count($benbenName)) {
			// 		foreach($benbenName as $k =>$v){
			// 			$benbenId[]= $k;
			// 		}
			// 		$sql = "select id from member where benben_id in (".implode(",", $benbenId).")";
			// 		$command = $connection->createCommand($sql);
			// 		$memberResult = $command->queryAll();
			// 		$insertSql = array();
			// 		if ($memberResult) {
			// 			foreach($memberResult as $e){
			// 				$insertSql[] = "(".$enterprise_info->id.", ".$e['id'].", ".time().", 2, ".$user->id.", 1)";
			// 			}
			// 		}
			// 		if (count($insertSql)) {
			// 			$sql = "insert into league_member(league_id, member_id, created_time, type, remark_content, status) values ".implode(',', $insertSql);
			// 			$enterprise_info->number = $enterprise_info->number + count($insertSql);
			// 			$enterprise_info->save();
			// 			$command = $connection->createCommand($sql);
			// 			$resultn = $command->execute();
			// 		}
			// 	}
			// }
			

			
			$sql = "select bid, area_name from area where bid in(".$province.", ".$city.", ".$area.")";
			$command = $connection->createCommand($sql);
			$areaResult = $command->queryAll();
			$areaInfo = array();
			for ($i=0; $i < count($areaResult); $i++) { 
				$areaInfo[$areaResult[$i]['bid']] = $areaResult[$i]['area_name'];
			}
			$pname = $areaInfo[$enterprise_info->province];
			$cname = $areaInfo[$enterprise_info->city];
			$aname = $areaInfo[$enterprise_info->area];
			$this->addIntegral($user->id,11);	
			$result['ret_num'] = 0;
			$result['ret_msg'] = '操作成功';
			$result['enterprise_info'] = array(
					"id"=>$enterprise_info->id,
					"name"=>$enterprise_info->name,
					"poster"=>$enterprise_info->poster ? URL.$enterprise_info->poster : "",
					"description"=>$enterprise_info->description,					
					"number"=>$enterprise_info->number,
					"province"=>$pname,
					"city"=>$cname,
					"area"=>$cname.' '.$aname,
					"full_area"=>$pname.' '.$cname.' '.$aname,
					"announcement"=>"",
					"league"=>1,
					"created_time"=>$enterprise_info->created_time
			);
		}else{
			$result['ret_num'] = 1502;
			$result['ret_msg'] = '新建好友联盟失败';
		}
		echo json_encode( $result );
	
	}
	
	/**
	 * 修改好友联盟{"ret_num":0,"ret_msg":"\u64cd\u4f5c\u6210\u529f","enterprise_info":{"id":"3","name":"\u5927\u5bb6\u597d\u554a","description":"\u8054\u7cfb","number":1,"created_time":1429522902}}
	 */
	public function actionEdit(){
		$this->check_key();
		$leagueid = Frame::getIntFromRequest('leagueid');
		$name = Frame::getStringFromRequest('name');
		$poster = Frame::saveImage("poster");
		$description = Frame::getStringFromRequest('description');
		$announcement = Frame::getStringFromRequest('announcement');
		$province = Frame::getIntFromRequest('province');
		$city = Frame::getIntFromRequest('city');
		$area = Frame::getIntFromRequest('area');
		if(empty($leagueid)){
			$result['ret_num'] = 1503;
			$result['ret_msg'] = '好友联盟ID为空';
			echo json_encode( $result );
			die();
		}
		

		$user = $this->check_user();
		$enterprise_info = FriendLeague::model()->find("id = {$leagueid} and is_delete = 0 ");
		if(empty($enterprise_info)){
			$result['ret_num'] = 1506;
			$result['ret_msg'] = '好友联盟不存在';
			echo json_encode( $result );
			die();
		}
		if($name){
			$enterprise_info->name = $name;
		}
		if($poster){
			$enterprise_info->poster = $poster;
		}
		if($description){
			$enterprise_info->description = $description;
		}
		if($announcement){
			$enterprise_info->announcement = $announcement;
			$enterprise_info->change_time = time();
		}
		if($province){
			$enterprise_info->province = $province;
		}
		if($city){
			$enterprise_info->city = $city;
		}
		if($area){
			$enterprise_info->area = $area;
		}

		if($enterprise_info->update()){
			$connection = Yii::app()->db;
			$sql = "select bid, area_name from area where bid in(".$enterprise_info->province.", ".$enterprise_info->city.", ".$enterprise_info->area.")";
			$command = $connection->createCommand($sql);
			$areaResult = $command->queryAll();
			$areaInfo = array();
			for ($i=0; $i < count($areaResult); $i++) { 
				$areaInfo[$areaResult[$i]['bid']] = $areaResult[$i]['area_name'];
			}

			$pname = $areaInfo[$enterprise_info->province];
			$cname = $areaInfo[$enterprise_info->city];
			$aname = $areaInfo[$enterprise_info->area];
			
			$result['ret_num'] = 0;
			$result['ret_msg'] = '操作成功';
			$result['enterprise_info'] = array(
					"name"=>$enterprise_info->name,
					"poster"=>$enterprise_info->poster ? URL.$enterprise_info->poster : "",
					"province"=>$pname,
					"city"=>$cname,
					"area"=>$cname.' '.$aname,
					"full_area"=>$pname.' '.$cname.' '.$aname,
					"announcement"=>$enterprise_info->announcement,
					"description"=>$enterprise_info->description,
					"change_time"=>$enterprise_info->change_time
			);
		}else{
			$result['ret_num'] = 1507;
			$result['ret_msg'] = '好友联盟修改失败';
		}
		echo json_encode( $result );
	
	}
	
	/**
	 * 查看好友联盟所有成员{"ret_num":0,"ret_msg":"\u64cd\u4f5c\u6210\u529f","member_info":[{"id":"4","league_id":"2","member_id":"21","created_time":"1429522902","nick_name":"\u6211\u4eec\u81ea\u5df1","pinyin":"W"},{"id":"2","league_id":"2","member_id":"10","created_time":"1429522792","nick_name":"\u5728\u5317\u4eac","pinyin":"Z"}]}
	 * 工作联盟上限100，英雄联盟上限300，堂主上限3人
	 */
	public function actionAllmember(){
		$this->check_key();
		$user = $this->check_user();
		$max_chief=MAX_CHIEF;
		$max_work=MAX_WORK;
		$max_hero=MAX_HERO;
		$has_chief=0;//现有堂主数
		$has_num=0;//现有人数
		// $user->id = 42;
		$enterpriseid = Frame::getIntFromRequest('leagueid');
		if(empty($enterpriseid)){
			$result['ret_num'] = 1503;
			$result['ret_msg'] = '好友联盟ID为空';
			echo json_encode( $result );
			die();
		}
		$enterprise = FriendLeague::model()->find("id = {$enterpriseid} and is_delete = 0 ");
		if(empty($enterprise)){
			$result['ret_num'] = 1504;
			$result['ret_msg'] = '好友联盟ID不存在';
			echo json_encode( $result );
			die ();
		}
		$has_num=$enterprise['number'];
		$info = LeagueMember::model()->find("league_id = {$enterpriseid} and member_id = {$user->id}");
		if(empty($info)){
			$result['ret_num'] = 1226;
			$result['ret_msg'] = '该用户已退出了联盟';
			echo json_encode( $result );
			die ();
		}
		$memberType = $info['type'];
		$memberRemark = $info['remark_content']?$info['remark_content']:$info['member_id'];
		$connection = Yii::app()->db;
		$sql = "select a.id,a.league_id,a.member_id,a.created_time,c.nick_name, c.poster , a.type, a.remark_content,a.nick_name as cardname, c.benben_id,c.huanxin_username
 				from league_member a  left join member c on c.id = a.member_id where a.league_id = {$enterpriseid} order by a.type, a.id desc";
		$command = $connection->createCommand($sql);
		$result1 = $command->queryAll();
		$PinYin = new PYInitials('utf-8');
		$chief = array();
		$chiefLevel = array();
		$normalPerson = array();
		$totalNormal = array();
		//备注名
		$sql = "select * from league_member_data where league_id = ".$enterpriseid;
		$command = $connection->createCommand($sql);
		$markResult = $command->queryAll();
		$markInfo=array();
		if ($markResult) {
			foreach ($markResult as $key => $value) {
				$markInfo[$value['data_id']] = $value;
			}
		}
		$benbenName = $this->getBenbenName($user->id);
		foreach ($result1 as $key=>$value){
			$poster = $value['poster'] ? URL.$value['poster'] : "";
			$bName = $value['nick_name'];
			if (!empty($value['cardname'])) {
				$bName = $value['cardname'];
			}else if (isset($benbenName[$value['benben_id']])) {
				$bName = $benbenName[$value['benben_id']];
			}
			if ($value['type'] == 0) {
				$remark_name = '盟主';
				if (isset($markInfo[$value['id']])) {
					if (!empty($markInfo[$value['id']]['remark_name'])) {
						$remark_name = $markInfo[$value['id']]['remark_name'];
					}
				}
				
				$chief = array('id'=>$value['id'], 'member_id'=>$value['member_id'], 'nick_name'=>$bName, 'poster'=>$poster, 'benben_id'=>$value['benben_id'], 'huanxin_username'=>$value['huanxin_username'], 'type'=>$value['type'], 'remark_name'=>$remark_name, 'type'=>$value['type']);
			}else if($value['type'] == 1){
				$remark_name = '堂主';
				if (isset($markInfo[$value['id']])) {
					if (!empty($markInfo[$value['id']]['remark_name'])) {
						$remark_name = $markInfo[$value['id']]['remark_name'];
					}
				}
				$has_chief++;
				$right = 0;
				if (($memberType == 0) || ($memberType == 1 && $value['member_id'] == $user->id)) {
					$right = 1;
				}
				$chiefLevel[] = array('id'=>$value['id'], 'member_id'=>$value['member_id'], 'nick_name'=>$bName, 'poster'=>$poster, 'benben_id'=>$value['benben_id'], 'remark_name'=>$remark_name, 'type'=>$value['type'], 'right'=>$right, 'huanxin_username'=>$value['huanxin_username']);
			}else{
				$currentType = $value['remark_content'];
				//所有成员分组
				$totalNormal[$currentType][] = $value;
				if ($memberType > 0 && $memberRemark != $currentType) {
					continue;
				}
				//可展示成员分组
				$normalPerson[$currentType][] = array('id'=>$value['id'], 'member_id'=>$value['member_id'], 'nick_name'=>$bName, 'poster'=>$poster, 'benben_id'=>$value['benben_id'], 'type'=>$value['type'], 'huanxin_username'=>$value['huanxin_username']);
			}
		}
		$remain_chief=$max_chief-$has_chief;
		if($enterprise['type']==1){
			//工作联盟
			$remain_num=$max_work-$has_num;
		}elseif($enterprise['type']==2){
			//英雄联盟
			$remain_num=$max_hero-$has_num;
		}else{
			$remain_num=0;
		}
		$returnArray['remain_num']=$remain_num;
		$returnArray['remain_chief']=$remain_chief;
		$returnArray['chief'] = $chief;//盟主信息
		$chiefMember = array();
		if (isset($normalPerson[$chief['member_id']])) {
			$chiefMember = $normalPerson[$chief['member_id']];
		}
		$cmCount = count($chiefMember);
		if(isset($totalNormal[$chief['member_id']])){
			$cmCount = count($totalNormal[$chief['member_id']]);
		}
		$returnArray['chief_member_count'] = $cmCount;//普通成员数
		$returnArray['chief_member'] = $chiefMember;//普通成员信息
		$level2 = array();
		if (count($chiefLevel)) {
			foreach ($chiefLevel as $key => $value) {
				$currentInfo = array();
				$currentLevelMember = array();
				$currentLevelMember[] = $value;
				if(isset($normalPerson[$value['member_id']])){
					$currentLevelMember = array_merge($currentLevelMember, $normalPerson[$value['member_id']]);
				}
				//分组总人数
				$memberCount = 1;
				if(isset($totalNormal[$value['member_id']])){
					$memberCount += count($totalNormal[$value['member_id']]);
				}
				$name = '堂';
				if (isset($markInfo[$value['id']]) && $markInfo[$value['id']]['name']) {
					if (!empty($markInfo[$value['id']]['name'])) {
						$name = $markInfo[$value['id']]['name'];
					}
				}
				//堂主信息
				$returnArray['other_chief'][] = array('id'=>$value['member_id'],'right'=>$value['right'], 'name'=>$name, 'member'=>$currentLevelMember, 'huanxin_username'=>$value['huanxin_username'], 'member_count'=>$memberCount);
			}
		}
		$result['ret_num'] = 0;
		$result['ret_msg'] = '操作成功';
		$result['member_info'] = $returnArray;
		echo json_encode( $result );
	
	}
	
	/**
	 * 盟主、堂主邀请好友加入联盟
	 * 添加成员和邀请堂主
	 * 工作联盟（FriendLeague中type=1）允许手动添加成员
	 */
	public function actionJoin(){
		$this->check_key();
		$user = $this->check_user();
		$enterpriseid = Frame::getIntFromRequest('leagueid');
		$type = Frame::getIntFromRequest('type');	//1表示邀请堂主，默认0表示邀请成员
		$hand= Frame::getIntFromRequest('hand'); //1表示手动添加，默认0表示自动添加
		$phone=Frame::getStringFromRequest('phone');//用于手动添加
			$memberid = Frame::getStringFromRequest('benben_id');//多个以 , 分隔

		$enterprise = FriendLeague::model()->find("id = {$enterpriseid} and is_delete = 0 ");
		if(empty($enterprise)){
			$result['ret_num'] = 1052;
			$result['ret_msg'] = '该好友联盟不存在';
			echo json_encode( $result );
			die ();
		}
		if (($type == 1) && ($enterprise->member_id != $user->id)) {
			$result['ret_num'] = 1052;
			$result['ret_msg'] = '只有盟主才可以邀请堂主';
			echo json_encode( $result );
			die ();
		}

		//被动加入
		if($memberid){
			$connection = Yii::app()->db;
			//堂主可以加3个，工作联盟成员100个，英雄联盟成员300个
			if ($type == 1) {
				$sql = "select count(*) c from league_member where league_id = {$enterpriseid} and type = 1";
			}else{
				$sql = "select count(*) c from league_member where league_id = {$enterpriseid} and type = 2 and remark_content = ".$user->id;
			}
			$command = $connection->createCommand($sql);
			$countResult = $command->queryAll();
			$alreadyHave = 0;
			if ($countResult) {
				$alreadyHave = $countResult[0]['c'];
			}
			$newMemberArray = explode(",", $memberid);
			if ($type ==  1) {
				if ((count($newMemberArray) + $alreadyHave) > MAX_CHIEF) {
					$max_chief=MAX_CHIEF;
					$result['ret_num'] = 1052;
					$result['ret_msg'] = "堂主最多可以有{$max_chief}个，已经超过限制。";
					echo json_encode( $result );
					die ();
				}
				//发送邀请通知
				$sql = "select id,nick_name,huanxin_username from  member  where  benben_id in ({$memberid}) order by id desc";
				$command = $connection->createCommand($sql);
				$result1 = $command->queryAll();

				//添加记录到消息表
				$newinfo = array();
				$content = $user->nick_name.'邀请您成为好友联盟：'.$enterprise->name.'的堂主';

				//$user->nick_name."邀请您加入好友联盟:".$enterprise->name;
				foreach ($result1 as $value){
					$t = time();
					$newinfo = "(5,{$user->id},{$value['id']},'{$content}',{$t},{$enterpriseid},{$type},0)";
					$sqln = "insert into news (type,sender,member_id,content,created_time,identity1, identity2, display) values ".$newinfo;
					$command = $connection->createCommand($sqln);
					$resultn = $command->execute();
					$news_id=Yii::app()->db->getLastInsertID();
					//环信系统通知
					$arr=array(
						"nick_name"=>$user->nick_name,
						"hxname"=>$user->huanxin_username,
						"leg_id"=>$enterpriseid,
						"leg_name"=>$enterprise['name'],
						"leg_poster"=>(URL.$enterprise['poster']),
						"time"=>$t,
						"news_id"=>$news_id,
						"t1"=>1,
						"t2"=>1,
						"t3"=>0,
						"t4"=>1
					);
					$user_hx=$value['huanxin_username'];
					$re =$this->sendHXMessage(array(0=>$user_hx),$content,$arr);
				}
				//环信系统通知(群发废弃)
//				if(!empty($user_hx)){
//					$re =$this->sendHXMessage($user_hx,$content,arr());
//					foreach($re['data'] as $k=>$v){
//						if($v!="success"){
//							$this->sendHXMessage(array(0=>$k),$content);
//						}
//					}
//				}
			}else {
				$maxNumber = MAX_HERO;
				if ($enterprise->type == 1) {
					$maxNumber = MAX_WORK;
				}
				if ((count($newMemberArray) + $alreadyHave) > $maxNumber) {
					$result['ret_num'] = 1052;
					$result['ret_msg'] = '联盟成员最多' . $maxNumber . '个，已经超过限制。';
					echo json_encode($result);
					die ();
				}

				//普通成员的添加
				if ($hand && $enterprise->type == 1) {
					//手动添加且为工作联盟
					if ($phone && $memberid) {
						$phone = $this->standarize_phone($phone);
						$phone_cn = substr($phone, 2);//去除开头86的
						$telephone = substr($phone, 4);//去除开头带4位区号的
						$result_all = Member::model()->find("benben_id={$memberid} and (phone={$phone} or phone={$phone_cn} or phone={$telephone})");
						if (count($result_all)) {
							$exist=LeagueMember::model()->find("league_id={$enterpriseid} and member_id={$result_all['id']}");
							if($exist){
								$result['ret_num'] = 2015;
								$result['ret_msg'] = '该用户已添加过';
								echo json_encode($result);
								die ();
							}
							$enterprise->number = $enterprise->number + 1;
							$enterprise->save();
							//用户直接加入联盟
							$t = time();
							$sqladd = "insert into league_member(league_id,member_id,type,remark_content,status,created_time) values ({$enterpriseid},{$result_all['id']},2,{$user->id},1,{$t})";
							$command = $connection->createCommand($sqladd);
							$resultadd = $command->execute();
						} else {
							$result['ret_num'] = 1022;
							$result['ret_msg'] = '奔犇号与手机号不一致';
							echo json_encode($result);
							die ();
						}
					} else {
						$result['ret_num'] = 1002;
						$result['ret_msg'] = '请填写奔犇号和手机号';
						echo json_encode($result);
						die ();
					}
				} else {
					//普通成员，直接邀请
					$sql = "select id,nick_name from  member  where  benben_id in ({$memberid}) order by id desc";
					$command = $connection->createCommand($sql);
					$result1 = $command->queryAll();

					$newinfo = array();
					foreach ($result1 as $value) {
						$t = time();
						$newinfo[] = "(" . $enterpriseid . "," . $value['id'] . ",2," . $user->id . ",1," . $t . ")";
						//是否需要增加积分
					}
					$enterprise->number = $enterprise->number + count($newinfo);
					$enterprise->save();
					//用户直接加入联盟
					$sqln = "insert into league_member(league_id,member_id,type,remark_content,status,created_time) values " . implode(",", $newinfo);
					$command = $connection->createCommand($sqln);
					$resultn = $command->execute();
				}
			}
	
			
			$result['ret_num'] = 0;
			$result['ret_msg'] = '操作成功';			
		}
		echo json_encode( $result );
	}

	/**
	同意好友邀请，加入联盟
	*/
	public function actionAgreeJoin()
	{
		$this->check_key();
		$user = $this->check_user();
//		$sender = Frame::getIntFromRequest('sender');
		$new_id = Frame::getIntFromRequest('new_id');
//		$member_id = Frame::getIntFromRequest('member_id');
//		$enterpriseid = Frame::getIntFromRequest('identity');
		$connection = Yii::app()->db;
		if ($new_id > 0) {
			$news_info = News::model()->find("id=".$new_id." and status < 2");
			if ($news_info) {
				$sender = $news_info->sender;
				$member_id = $news_info->member_id;
				$enterpriseid = $news_info->identity1;
				$role = $news_info->identity2;
				$news_info->status = 2;
				$news_info->display = 0;
				$news_info->save();
				if (!$enterpriseid || !$sender) {
					$result['ret_num'] = 1056;
						$result['ret_msg'] = '请求数据有问题...';
						echo json_encode( $result );
						exit();
				}

				$enterprise = FriendLeague::model()->findByPk($enterpriseid);
				if(empty($enterprise)){
					$result['ret_num'] = 1052;
					$result['ret_msg'] = '该好友联盟不存在';
					echo json_encode( $result );
					die ();
				}
				if ($role == 1) { 
					//添加堂主,每个人只能加入一个
					$myLeagueCount = LeagueMember::model()->count("member_id=".$member_id.' and type<2');
					if ($myLeagueCount > 0) {
						$result['ret_num'] = 1056;
						$result['ret_msg'] = '您已经在其他联盟担任盟主或堂主';
						echo json_encode( $result );
						die ();
					}
				}

				$sql = "select * from league_member where league_id = ".$enterpriseid." and member_id = ".$sender;
				$command = $connection->createCommand($sql);
				$result1 = $command->queryAll();
				if ($result1) {
					$sql = "select member_id,type from league_member where league_id = ".$enterpriseid;
					$command = $connection->createCommand($sql);
					$allMember = $command->queryAll();
					//所有人id数组
					$allMemberInfo = array();
					//所有堂主id数组
					$allTangzhu = array();
					for ($i=0; $i < count($allMember); $i++) { 
						$type = $allMember[$i]['type'];
						if ($type == 1) {
							//堂主
							$allTangzhu[] = $allTangzhu[$i]['member_id'];
						}
						$allMemberInfo[] = $allMember[$i]['member_id'];
					}
					if (in_array($member_id,  $allMemberInfo)) {
						$result['ret_num'] = 1056;
						$result['ret_msg'] = '您已经在该好友联盟中';
						echo json_encode( $result );
						exit();
					}
					if ($role == 1) { //添加堂主
						if (count($allTangzhu) >= 3) {
							$result['ret_num'] = 1056;
							$result['ret_msg'] = '该好友联盟的堂主数量已经满员';
							echo json_encode( $result );
							die();
						}
						$con = new LeagueMember();
						$con->league_id = $enterpriseid;
						$con->member_id = $member_id;
						$con->type = 1;
						$con->status = 1;
						$con->created_time = time();
						if($con->save()){
							$result['ret_num'] = 0;
							$result['ret_msg'] = '加入好友联盟成功';
						}else{
							$result['ret_num'] = 1056;
							$result['ret_msg'] = '好友联盟失败';
						}

						if ($enterprise->type == 2) {
							//英雄联盟，好友自动加入
							$allMemberInfo[] = $member_id;
							$enterprise = FriendLeague::model()->findByPk($enterpriseid);
							$enterprise->number = $enterprise->number+1;
							$benbenName = $this->getBenbenName($user->id);
							if (count($benbenName) > 0) {
								$benben_id_array = array();
								foreach ($benbenName as $k => $v){
									$benben_id_array[] = $k;	
								}
								$sql = "select id from member where benben_id in (".implode(",", $benben_id_array).")";
								$command = $connection->createCommand($sql);
								$memberQuery = $command->queryAll();
								$newinfo = array();
								foreach ($memberQuery as $value){
									if (!in_array($value['id'], $allMemberInfo)) {
										$t = time();				
										$newinfo[] = "(".$enterpriseid.",".$value['id'].",2,".$member_id.",1,".$t.")";
									}
									
								}
								if (count($newinfo) > 0) {
									$enterprise->number = $enterprise->number+count($newinfo);
									$sqln = "insert into league_member(league_id,member_id,type,remark_content,status,created_time) values ".implode(",", $newinfo);
									$command = $connection->createCommand($sqln);
									$resultn = $command->execute();
								}
								$this->addIntegral($member_id, 12);	
								//查询是否拥有好友联盟
								$haveleague = LeagueMember::model()->find("member_id = {$user->id} and type < 2");
								if($haveleague){
									if ($haveleague->type == 1) {
										$league = 1;
									}else{
										$league = 2;
									}	
								}else{
									$league = 0;
								}
								$result['ret_num'] = 0;
								$result['league'] = $league;
								$result['ret_msg'] = '加入好友联盟成功';
							}
							$enterprise->save();
						}else {
							//工作联盟，只有堂主自己加入
							$this->addIntegral($member_id, 12);	
							//查询是否拥有好友联盟
							$haveleague = LeagueMember::model()->find("member_id = {$user->id} and type < 2");
							if($haveleague){
								if ($haveleague->type == 1) {
									$league = 1;
								}else{
									$league = 2;
								}	
							}else{
								$league = 0;
							}
						}		
						
					}else {	//如果是堂主，则是添加普通用户
						$maxNumber = 300;
						if ($enterprise->type == 1) {
							$maxNumber = 100;
						}
						$sql = "select count(*) as c from league_member where league_id = ".$enterpriseid." and remark_content = ".$sender;
						$command = $connection->createCommand($sql);
						$currentCountInfo = $command->queryAll();
						if ($currentCountInfo && $currentCountInfo[0]['c'] >= $maxNumber) {
							$result['ret_num'] = 1052;
							$result['ret_msg'] = '该好友联盟已经满员';
							echo json_encode( $result );
							die ();
						}
						$con = new LeagueMember();
						$con->league_id = $enterpriseid;
						$con->member_id = $member_id;
						$con->type = 2;
						$con->remark_content = $sender;
						$con->status = 1;
						$con->created_time = time();
						if($con->save()){
							$enterprise = FriendLeague::model()->findByPk($enterpriseid);
							$enterprise->number = $enterprise->number+1;
							$enterprise->save();
							$this->addIntegral($member_id, 12);	
							//查询是否拥有好友联盟
							$haveleague = LeagueMember::model()->find("member_id = {$user->id} and type < 2");
							if($haveleague){
								if ($haveleague->type == 1) {
									$league = 1;
								}else{
									$league = 2;
								}	
							}else{
								$league = 0;
							}
							$result['ret_num'] = 0;
							$result['league'] = $league;
							$result['ret_msg'] = '加入好友联盟成功';
						}
					}
				}else{
					$result['ret_num'] = 1052;
					$result['ret_msg'] = '该好友联盟不存在';
				}
			}else{
				$result['ret_num'] = 1062;
				$result['ret_msg'] = '消息已经处理';
				echo json_encode( $result );
				die ();
			}
		}
//		else if ($sender && $member_id) {
//			$result['ret_num'] = 1053;
//			$result['ret_msg'] = '参数错误';
			// $sql = "select * from league_member where league_id = ".$enterpriseid." and member_id = ".$sender;
			// $command = $connection->createCommand($sql);
			// $result1 = $command->queryAll();
			// if (count($result1)) {
			// 	$sql = "select member_id from league_member where league_id = ".$enterpriseid;
			// 	$command = $connection->createCommand($sql);
			// 	$allMember = $command->queryAll();
			// 	$allMemberInfo = array();
			// 	for ($i=0; $i < count($allMember); $i++) { 
			// 		$allMemberInfo[] = $allMember[$i]['member_id'];
			// 	}
			// 	$news_info = News::model()->find("sender = {$sender} and member_id = {$member_id} and identity1 = '{$enterpriseid}' and status < 2");
			// 	if ($news_info) {
			// 		$news_info->status = 2;
			// 		$news_info->save();
			// 	}else{
			// 		$result['ret_num'] = 1062;
			// 		$result['ret_msg'] = '消息已经处理';
			// 		echo json_encode( $result );
			// 		die ();
			// 	}

			// 	if (in_array($member_id,  $allMemberInfo)) {
			// 		$result['ret_num'] = 0;
			// 		$result['ret_msg'] = '该用户已经在好友联盟中';
			// 		echo json_encode( $result );
			// 		exit();
			// 	}
				
			// 	if ($news_info->identity2 == 1) { //添加堂主
			// 		$con = new LeagueMember();
			// 		$con->league_id = $enterpriseid;
			// 		$con->member_id = $member_id;
			// 		$con->type = 1;
			// 		$con->status = 1;
			// 		$con->created_time = time();
			// 		if($con->save()){
			// 			$result['ret_num'] = 0;
			// 			$result['ret_msg'] = '加入好友联盟成功';
			// 		}else{
			// 			$result['ret_num'] = 1056;
			// 			$result['ret_msg'] = '好友联盟失败';
			// 		}
			// 		$enterprise->number = $enterprise->number+1;
			// 		$sqlf = "select friend_id1,friend_id2 from friend_relate where (friend_id1 = {$member_id} or friend_id2 = {$member_id}) and status = 1";
			// 		$command = $connection->createCommand($sqlf);
			// 		$friend = $command->queryAll();
			// 		$fri = array();
			// 		if (count($friend) > 0) {
			// 			foreach ($friend as $v){
			// 				if ($v['friend_id1'] != $member_id && !in_array($v['friend_id1'] , $allMemberInfo)) {
			// 					$fri[]= $v['friend_id1'];
			// 				}
			// 				if ($v['friend_id2'] != $member_id && !in_array($v['friend_id2'] , $allMemberInfo)) {
			// 					$fri[]= $v['friend_id2'];
			// 				}	
			// 			}
			// 			$fri = array_unique($fri);
			// 			$newinfo = array();
			// 			foreach ($fri as $value){
			// 				$t = time();				
			// 				$newinfo[] = "(".$enterpriseid.",".$value.",2,".$member_id.",1,".$t.")";
			// 			}
			// 			if (count($newinfo) > 0) {
			// 				$enterprise->number = $enterprise->number+count($fri);
			// 				$sqln = "insert into league_member(league_id,member_id,type,remark_content,status,created_time) values ".implode(",", $newinfo);
			// 				$command = $connection->createCommand($sqln);
			// 				$resultn = $command->execute();
			// 			}
			// 			$this->addIntegral($member_id, 12);	
			// 			$result['ret_num'] = 0;
			// 			$result['ret_msg'] = '加入好友联盟成功';
			// 		}
			// 		$enterprise->save();		
					
			// 	}else {	//如果是堂主，则是添加普通用户
			// 		$con = new LeagueMember();
			// 		$con->league_id = $enterpriseid;
			// 		$con->member_id = $member_id;
			// 		$con->type = 2;
			// 		$con->remark_content = $sender;
			// 		$con->status = 1;
			// 		$con->created_time = time();
			// 		if($con->save()){
			// 			$enterprise->number = $enterprise->number+1;
			// 			$enterprise->save();
			// 			$this->addIntegral($member_id, 12);	
			// 			//查询是否拥有好友联盟
			// 			$haveleague = LeagueMember::model()->find("member_id = {$user->id} and type < 2");
			// 			if($haveleague){
			// 				if ($haveleague->type == 1) {
			// 					$league = 1;
			// 				}else{
			// 					$league = 2;
			// 				}	
			// 			}else{
			// 				$league = 0;
			// 			}
			// 			$result['ret_num'] = 0;
			// 			$result['league'] = $league;
			// 			$result['ret_msg'] = '加入好友联盟成功';
			// 		}
			// 	}
			// }else{
			// 	$result['ret_num'] = 1056;
			// 	$result['ret_msg'] = '该用户未加入好友联盟';
			// }
//		}
		else{
			$result['ret_num'] = 1052;
			$result['ret_msg'] = '该好友联盟不存在';
		}
		echo json_encode( $result );
	}

	/**
	 * 退出好友联盟
	 */
	public function actionExit(){
		$connection = Yii::app()->db;
		$this->check_key();
		$user = $this->check_user();
		$enterpriseid = Frame::getIntFromRequest('leagueid');
		$memberid = Frame::getIntFromRequest('member_id');
		$type = Frame::getIntFromRequest('type');//1表是左滑删除
	
		$enterprise = FriendLeague::model()->find("id = {$enterpriseid} and is_delete = 0 ");
		if(empty($enterprise)){
			$result['ret_num'] = 1052;
			$result['ret_msg'] = '该好友联盟不存在';
			echo json_encode( $result );
			die ();
		}
		if(!$memberid){
			$memberid = $user->id;
		}

		//判断用户角色
		$info = LeagueMember::model()->find("league_id = {$enterpriseid} and member_id = {$memberid}");
		if(empty($info)){
			$result['ret_num'] = 1226;
			$result['ret_msg'] = '该用户已退出了联盟';
			echo json_encode( $result );
			die ();
		}
		$result['league'] = 2;
		if ($info->type == 0) { //盟主退出删除联盟
			if($type == 1){
				$result['ret_num'] = 1926;
				$result['ret_msg'] = '不能删除自己';
				echo json_encode( $result );
				die ();
			}
			$enterprise->is_delete = 1;
			//$enterprise->number = 0;
			$enterprise->update();
			$dSql = "delete from league_member where league_id = {$enterpriseid}";
			$command = $connection->createCommand($dSql);
			$resultn = $command->execute();
			
		}else if($info->type == 1){ //堂主退出
			$info->delete();
			$dSql = "delete from league_member where league_id = {$enterpriseid} and remark_content = '{$memberid}'";
			$command = $connection->createCommand($dSql);
			$resultn = $command->execute();
			$enterprise->number = $enterprise->number - (1+$resultn);
			$enterprise->save();
		}else{
			$info->delete();
			$enterprise->number = $enterprise->number - 1;
			$enterprise->save();
		}
		//查询是否拥有好友联盟
		$haveleague = LeagueMember::model()->find("member_id = {$user->id} and type < 2");
		if($haveleague){
			if ($haveleague->type == 1) {
				$league = 1;
			}else{
				$league = 2;
			}	
		}else{
			$league = 0;
		}
		$result['ret_num'] = 0;
		$result['league'] = $league;
		$result['ret_msg'] = '操作成功';
		echo json_encode( $result );
	}

	/**
	邀请成员列表
	*/
	public function actionInviteMember()
	{
		$connection = Yii::app()->db;
		$this->check_key();
		$user = $this->check_user();
		// $user->id = 42;
		$enterpriseid = Frame::getIntFromRequest('leagueid');
		//邀请类型1堂主，0成员
		$inviteType = Frame::getIntFromRequest('type');
		$member_id = $user->id;
		if(empty($enterpriseid)){
			$result['ret_num'] = 1052;
			$result['ret_msg'] = '该好友联盟不存在';
			echo json_encode( $result );
			die ();
		}
		$sql = "select member_id from league_member where league_id = ".$enterpriseid;
		if ($inviteType > 0) {
			$sql = $sql." or type in (0, 1)";
		}
		$command = $connection->createCommand($sql);
		$allMember = $command->queryAll();
		$allMemberInfo = array();
		foreach ($allMember as $va){
			$allMemberInfo[] = $va['member_id'];
		}
		//获取分组
		$sql1 = "select id,group_name name from group_contact where member_id = {$user->id}";
		$command = $connection->createCommand($sql1);
		$result1 = $command->queryAll();
		$result_group = array();
		$groupId = array();
		if($result1){
			foreach ($result1 as $value){
				if ($value['name'] != '未分组') {
					$groupId[] = $value['id'];
					$result_group[$value['id']] = $value['name'];
				}else {
					$wfz_group = $value;
				}
			}
		}
		//未分组放在最后
		if ($wfz_group) {
			$groupId[] = $wfz_group['id'];
			$result_group[$wfz_group['id']] = $wfz_group['name'];
		}
		$sql1 = "select a.name,a.benben_id,b.phone, a.group_id, b.is_benben from group_contact_info as a left join group_contact_phone as b on a.id = b.contact_info_id where a.group_id in (".implode(",", $groupId).") group by a.id";//b.is_benben > 0 and
		$command = $connection->createCommand($sql1);
		$info = $command->queryAll();
		$searchPhone = array();
		$groupPhone = array();
		if ($info) {
			foreach ($info as $key => $value) {
// 				if ($value['phone']) {
// 				// ?$searchPhone[] = "'".$value['phone']."'";
// 					$groupPhone[$value['group_id']][] = $value['phone'];
// 				}
// 				if ($value['is_benben']) {
// 					$benbenName[$value['is_benben']] = $value['name'];
// 					$searchPhone[] = $value['is_benben'];
// 				}
				if ($value['benben_id']) {
					$groupPhone[$value['group_id']][] = $value['benben_id'];
					$benbenName[$value['benben_id']] = $value['name'];
					$searchPhone[] = $value['benben_id'];
				}
			}
		}
		$searchPhone = array_unique($searchPhone);
		//根据手机号查找犇犇用户
		$searchMemberInfo = array();
		if (count($searchPhone) > 0) {
			$sql1 = "select id, name, poster, nick_name, phone, benben_id  from member where benben_id in (".implode(",", $searchPhone).")";
			$command = $connection->createCommand($sql1);
			$mInfo = $command->queryAll();
			if ($mInfo) {
				$PinYin = new PYInitials('utf8');
				foreach ($mInfo as $key => $value) {
					if (!in_array($value['id'], $allMemberInfo)) {
						$name = $value['name']?$value['name']:$value['nick_name'];
						if (isset($benbenName[$value['benben_id']])) {
							$name = $benbenName[$value['benben_id']];
						}
						//$searchMemberInfo[$value['phone']] = array(
						$searchMemberInfo[$value['benben_id']] = array(
							'id'=>$value['id'], 
							'phone'=>$value['phone'], 
							'is_benben'=>$value['benben_id'], 
							'name'=>$name, 
							'pinyin'=>substr($PinYin->getInitials($name), 0, 1),
							'poster'=>$value['poster'] ? URL.$value['poster'] : ""
							);		
					}
					
				}
			}
		}

		$member_list = array();
		foreach ($result_group as $key => $value) {
			$currentGroupPhone = array();
			$currentMember = array();
			if (isset($groupPhone[$key])) {
				$currentGroupPhone = $groupPhone[$key];
			}
			if (count($currentGroupPhone)) {
				foreach($currentGroupPhone as $p){
					if(isset($searchMemberInfo[$p])){
						$currentMember[] = $searchMemberInfo[$p];
					}
				}
			}
			$member_list[] = array('id'=>$key, 'name'=>$value."(".count($currentMember)."人)", 'member'=>$currentMember);
		}
		$result['ret_num'] = 0;
		$result['ret_msg'] = '操作成功';
		$result['member_list'] = $member_list;
		echo json_encode( $result );

	}
	
}