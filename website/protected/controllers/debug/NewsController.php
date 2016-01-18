<?php
class NewsController extends PublicController{
	public $layout = false;
	
	/**
	 * 消息列表
	 */
	public function actionNewsList(){
// 		$result ['ret_num'] = 0;
// 		$result ['ret_msg'] = '操作成功';
// 		$result['news'] = array();
		
// 		echo json_encode($result);die;
		$this->check_key();
		$user = $this->check_user();
		$user_id = $user->id;
		$last_time = Frame::getStringFromRequest("last_time");
		
		$model = News::model();
		$cri = new CDbCriteria();
		//查出上次刷新时间
		$retime = NewsRefresh::model()->find("member_id = {$user_id}");
		if(!$retime){
			$retime = new NewsRefresh();
			$retime->member_id = $user_id;
			$retime->refresh_time = $user->created_time;
			$retime->save();
		}
		
		$cri->select = "t.*, member.nick_name as mname, member.poster as mposter,member.huanxin_username as hxname";
		$cri->join = "left join member on member.id = t.sender";
		$cri->order = "t.created_time desc";
		$cri->addCondition("member_id = 0 and t.created_time>".$retime->refresh_time, 'OR');
		if(empty($last_time)){
			$last_time = 0;
		}
		$cri->addCondition("member_id=$user_id and t.status=0 and t.created_time >".$last_time, 'OR');
// 		else{
// 			$cri->addCondition("t.created_time >".$retime->refresh_time);
			$retime->refresh_time = time();
			$retime->update();
// 		}
		// $cri->addInCondition("t.status", array(0));
		$news = $model->findAll($cri);
		$result = array();
		$nlist = array();
		//获取政企类型
		$eid = array();
		$enid = array();
		foreach ($news as $va){
			if($va->identity1){
				$eid[$va->id] = $va->identity1;
				$enid[$va->identity1] = $va->id;
			}
		}
		$eid1 = implode(",", $eid);
		if($eid1){
			$connection = Yii::app()->db;
			$asql = "select id, type from enterprise where id in ({$eid1})";
			$command = $connection->createCommand($asql);
			$resultn = $command->queryAll();
			$eidtype = array();
			foreach ($resultn as $val){
				$eidtype[$enid[$val['id']]] = $val['type'];
			}
		}
		$benbenName = $this->getContactIdName($user_id);
		
		foreach ($news as $value){
			if($value->type == 1 || $value->type == 2){
				$value->mname = "犇犇";
				$value->mposter = "/themes/images/poster.jpg";
			}
			$senderName = $value->mname;
			if (isset($benbenName[$value->sender])) {
				$senderName = $benbenName[$value->sender];
			}

			
			$temp = array("id" => $value->id,
									  "type" => $value->type,
					                  "enterprisetype" => $eidtype[$value->id] ? $eidtype[$value->id] : "",
									  "sender" => $senderName,
					                  "huanxin_username"=>$value->hxname,
									  "sender_id"=>$value->sender,
									  "poster" => $value->mposter ? Yii::app()->request->getHostInfo().$value->mposter : "",
									  "content" => $value->content ? $value->content : "",
									  "identity1" => $value->identity1 ? $value->identity1 : "",
									  "identity2" => $value->identity2 ? $value->identity2 : "",
					                  "status" => $value->status,
									  "created_time" => $value->created_time);
			$nlist[] = $temp;
		}
		
		$result ['ret_num'] = 0;
		$result ['ret_msg'] = '操作成功';
		$result['news'] = $nlist;
		
		echo json_encode($result);
	}
	
	
	/**
	 * 改变消息为已读
	 */
	
	public function actionUpdateNews(){

		$this->check_user();
		$this->check_key();

		$id = Frame::getIntFromRequest("news_id");
		
		$result = array();
		if(!$id){
			$result ['ret_num'] = 3001;
			$result ['ret_msg'] = '非法操作';
			echo json_encode($result);
			die;
		}
		$news = News::model()->findByPk($id);
		if($news->type != 1 or $news->member_id>0){
			$news->status = 1;
			$news->save();
		}
		
		$result ['ret_num'] = 0;
		$result ['ret_msg'] = 'OK';
		
		echo json_encode($result);
		
	}
	
	/**
	 * 政企通讯录加入消息确认
	 */
	public function actionConfirm(){
		$this->check_key();
		$user = $this->check_user();
		$newsid = Frame::getIntFromRequest('news_id');
		$shortphone = Frame::getStringFromRequest('short_phone');
		if(empty($newsid)){
			$result['ret_num'] = 600;
			$result['ret_msg'] = '消息ID为空';
			echo json_encode( $result );
			die ();
		}
		$news_info = News::model()->find("id = {$newsid}");
		if(empty($news_info)){
			$result['ret_num'] = 601;
			$result['ret_msg'] = '消息不存在';
			echo json_encode( $result );
			die ();
		}else if($news_info->status  == 2){
			$result['ret_num'] = 601;
			$result['ret_msg'] = '消息已处理';
			echo json_encode( $result );
			die();
		}
		$connection = Yii::app()->db;
		$asql = "select a.contact_id from enterprise_member as a left join enterprise as b on a.contact_id = b.id where b.id > 0 and b.member_id = {$news_info->member_id}";
		$command = $connection->createCommand($asql);
		$count = $command->queryAll();
		$joinContact = array();
		if ($count) {
			foreach ($count as $key => $value) {
				$joinContact[] = $value['contact_id'];
			}
		}
		
		if(count($joinContact) >= 6){
			$result['ret_num'] = 5203;
			$result['ret_msg'] = '您已加入6个政企通讯录';
// 			echo json_encode( $result );
// 			die();
		}else if(in_array($news_info->identity1, $joinContact)){			
			$result['ret_num'] = 0;
			$result['ret_msg'] = '您已加入该政企通讯录';
			$news_info->status = 2;
			$news_info->update();
			echo json_encode( $result );
			die();
		}

		$enterprise = Enterprise::model()->findByPk($news_info->identity1);
		if(empty($enterprise)){
			$result['ret_num'] = 1005;
			$result['ret_msg'] = '该政企通讯录不存在';
			echo json_encode( $result );
			die ();
		}
		if ($enterprise['type'] == 2 && empty($shortphone)) {
			$result['ret_num'] = 1005;
			$result['ret_msg'] = '加入虚拟通讯录需要输入短号';
			echo json_encode( $result );
			die ();
		}
		if ($enterprise['type'] == 2 && $shortphone) {
			$createdMember = EnterpriseMember::model()->find("contact_id = '{$news_info->identity1}' and member_id = {$enterprise['member_id']}");			
			if ($enterprise['short_length'] != strlen($shortphone)) {
				$result['ret_num'] = 1012;
				$result['ret_msg'] = '虚拟通讯录短号长度为'.$enterprise['short_length'].',请重新输入';
				echo json_encode( $result );
				die ();
			}			
		}
		$user = Member::model ()->findByPk($news_info->member_id);
		$guser = new EnterpriseMember();
		$guser->contact_id = $news_info->identity1;
		$guser->member_id = $news_info->member_id;
		$guser->invite_id = $news_info->sender;
		if($news_info->remark_name){
			$guser->name = $news_info->remark_name;
		}else{
			$guser->name = $user->name?$user->name:$user->nick_name;
		}		
		$guser->phone = $user->phone;
		$guser->short_phone = $shortphone;
		$guser->created_time = time();
		if($guser->save()){
			$news_info->status = 2;
			$news_info->update();
			//确认表更新
			$ein = EnterpriseInvite::model()->find("enterprise_id = {$news_info->identity1} and member_id = {$news_info->member_id} and status = 0 order by created_time desc");
			if($ein){
				$ein->status = 1;
				$ein->update();
			}
			$enterprise->number = $enterprise->number + 1;
			$enterprise->update();

			$this->addIntegral($user->id, 4);	
			$result['ret_num'] = 0;
			$result['ret_msg'] = '操作成功';
		}else{
			$result['ret_num'] = 602;
			$result['ret_msg'] = '添加失败';
		}
		echo json_encode( $result );
	
	}

	/**
		发送小喇叭
	*/
	public function actionBroadcasting()
	{
		$this->check_key();
		$user = $this->check_user();
		$league = Frame::getIntFromRequest('league_id');
		$content = Frame::getStringFromRequest('content');
		$phone = Frame::getStringFromRequest('phone');
		$type = Frame::getIntFromRequest('type');
		if (!$content) {
			$result['ret_num'] = 5301;
			$result['ret_msg'] = '请提供完整信息';
			echo json_encode( $result );
			die();
		}
		$identity1 = 0;
		$connection = Yii::app()->db;
		//如果是发送直通车，只能发送2次
		if ($type == 1) {
			$command = $connection->createCommand("select count(*) as c from broadcasting_log where member_id = {$user->id}  and type = 1");
			$authority = $command->queryAll();
			if ($authority && $authority[0]['c'] > 1) {
				$result['ret_num'] = 5301;
				$result['ret_msg'] = '你已经发送了2条直通车小喇叭';
				echo json_encode( $result );
				die();
			}
			if (!$content) {
				$content = '我创建了直通车，来看看吧～';
			}
			$command = $connection->createCommand("select id from number_train where member_id = {$user->id}");
				$identity = $command->queryAll();
				if ($identity) {
					$identity1 = $identity[0]['id'];
				}
		}else{
			$type = 0;
		}

		//1个月只能够发30天小喇叭
		$command = $connection->createCommand("select count(*) as c from broadcasting_log where member_id = {$user->id} and created_time >= ".strtotime(date('Y-m-01', strtotime(date("Y-m-d")))));
		$authority = $command->queryAll();
		if ($authority && $authority[0]['c'] > 30) {
			$result['ret_num'] = 5301;
			$result['ret_msg'] = '你本月已经发送了30条小喇叭';
			echo json_encode( $result );
			die();
		}

		
		$sql = "select league_id, member_id, remark_content, type from league_member where status =1 and type > 0 and league_id = ".$league;
		$command = $connection->createCommand($sql);
		$info = $command->queryAll();
		//根据手机号查找好友
		$benbenName = $this->getBenbenName($user->id);
		$phoneMember = array();
		if ($phone) {
			$phoneArray = explode(",", $phone);
			if (count($phoneArray) > 0) {
				$sql = "select id, name, nick_name, benben_id from member where benben_id in (".implode(",", $phoneArray).")";//phone
				$command = $connection->createCommand($sql);
				$phoneMember = $command->queryAll();
			}
			
		}
		$memberId = array();
		$leagueId = array();
		$newsArray = array();
		$leagueAllMember = array();
		if ($info || count($phoneMember) > 0) {
			foreach ($info as $key => $value) {
				if ($value['type'] == 1) {
					$sender = $user->id;
				}else{
					$sender = $value['remark_content'];
				}

				if (!in_array($value['league_id'], $leagueId)) {
					$leagueId[] = $value['league_id'];
				}
				$leagueAllMember[] = $value['member_id'];
				if (!in_array($value['member_id'], $memberId)) {
					$memberId[] = $value['member_id'];
				}
				if (!in_array($sender, $memberId)) {
					$memberId[] = $sender;
				}
				$newsArray[] = array('league_id'=>$value['league_id'], 'sender'=>$sender, 'receive'=>$value['member_id']);	
			}
			if (count($memberId) > 0) {
				$sql = "select id, name, nick_name from member where id in (".implode(",", $memberId).")";
				$command = $connection->createCommand($sql);
				$memberQuery = $command->queryAll();
			}
			
			$memberInfo = array();
			if ($memberQuery) {
				foreach ($memberQuery as $key => $value) {
					$memberInfo[$value['id']] = $value['name']?$value['name']:$value['nick_name'];
				}
			}
			if (count($leagueId) > 0) {
				$sql = "select id, name from friend_league where id in (".implode(",", $leagueId).")";
				$command = $connection->createCommand($sql);
				$leagueQuery = $command->queryAll();
			}
			
			$leagueInfo = array();
			if ($leagueQuery) {
				foreach ($leagueQuery as $key => $value) {
					$leagueInfo[$value['id']] = $value['name'];
				}
			}
			$newsType = 3;
			if ($type == 1) {
				$newsType = 7;
			}
			$insertNews = array();
			if (count($newsArray) > 0) {
				foreach ($newsArray as $key => $value) {
					
					$contentAdd = "好友".$memberInfo[$value['sender']]."向您发送一条小喇叭:".$content;
					$insertNews[] = "(".$newsType.", ".$value['sender'].", ".$value['receive'].", '".$content."', 0, ".time().", ".$identity1.")";
				}
			}
			$phoneMemberId = array();
			$phoneMemberName = array();
			if (count($phoneMember)) {
				$currentSenderName = $user->name?$user->name:$user->nick_name;
				foreach($phoneMember as $value){
					$phoneMemberId[] = $value['id'];
					// if (in_array($value['id'], $leagueAllMember)) {
					// 	continue;
					// }
					
					$contentAdd = "好友".$currentSenderName."向您发送一条小喇叭:".$content;
					$insertNews[] = "(".$newsType.", ".$user->id.", ".$value['id'].", '".$content."', 0, ".time().", ".$identity1.")";
					if (isset($benbenName[$value['benben_id']])) {
						$phoneMemberName[] = $benbenName[$value['benben_id']];
					}else{
						$phoneMemberName[] = $value['nick_name'];
					}
					
				}
			}

			// $sql = "select number from friend_league where member_id={$user->id} limit 1";
			// $command = $connection->createCommand($sql);
			// $enterprise = $command->queryAll();
			// $league_count = 0;
			// if ($league) {
			// 	$league_count = $enterprise[0]["number"];
			// }
			$league_count = 0;
			if ($league > 0) {
				$league_count = max(0, $this->getLeagueCount($league)-1);
			}

			if (count($insertNews) > 0) {
				$sql = "insert into news (type, sender, member_id, content, status, created_time, identity1) values ".implode(",", $insertNews);
				$command = $connection->createCommand($sql);
				$result1 = $command->execute();

				//发送小喇叭历史记录
				$broadcastingLog = new BroadcastingLog();
				$broadcastingLog->member_id = $user->id;
				$broadcastingLog->league_id = $league;
				$broadcastingLog->friend_id = implode(",", $phoneMemberId);
				// $broadcastingLog->receive_count = count($newsArray)+count($phoneMember);
				$broadcastingLog->receive_count = $league_count+count($phoneMember);
				$broadcastingLog->content = $content;
				$broadcastingLog->type = $type;
				if(count($phoneMemberName)>0){
					$description=implode(",", $phoneMemberName);
				}
				$broadcastingLog->description = $description;
				$broadcastingLog->created_time = time();
				$broadcastingLog->save();
				$error = $broadcastingLog->geterrors();
				var_dump($error);
			}

			$this->addIntegral($user->id, 10);	
			$result['ret_num'] = 0;
			$result['ret_msg'] = '小喇叭发送成功';
			echo json_encode( $result );

		}else{
			$result['ret_num'] = 5302;
			$result['ret_msg'] = '联盟中没有可选的成员来发送消息';
			echo json_encode( $result );
		}

	}

	public function getLeagueCount($leagueid)
	{
		if (empty($leagueid)) {
			return 0;
		}
		$result = LeagueMember::model()->count("league_id=".$leagueid);
		return $result;
	}

	/**
		小喇叭历史记录
	*/
	public function actionBroadcastingList()
	{
		$this->check_key();
		$user = $this->check_user();
		$connection = Yii::app()->db;
		$sql = "select * from broadcasting_log where is_del = 0 and member_id = ".$user->id." order by id desc";
		$command = $connection->createCommand($sql);
		$infoQuery = $command->queryAll();
		$lists = array();
		$command = $connection->createCommand("select count(*) as c from broadcasting_log where member_id = ".$user->id." and created_time >= ".strtotime(date('Y-m-01', strtotime(date("Y-m-d")))));
		$authority = $command->queryAll();
		$authorityNumber = 30;
		if ($authority) {
			$authorityNumber = 30 - $authority[0]['c'];
		}
		if ($infoQuery) {
			$friendIds = array();
			foreach ($infoQuery as $key => $value) {
				if ($value['friend_id']) {
					$friendIds[] = $value['friend_id'];
				}
			}
			$phoneWithId = array();
			if (count($friendIds)) {
				$sql = "select id, phone, benben_id from member where id in (".implode(',', $friendIds).")";
				$command = $connection->createCommand($sql);
				$info = $command->queryAll();
				if ($info) {
					foreach($info as $each){
						$phoneWithId[$each['id']] = array('phone'=>$each['phone'], 'benben_id'=>$each['benben_id']);
					}
				}
			}

			foreach ($infoQuery as $key => $value) {
				$shortDescription = $description = $value['receive_count']."位收件人:";
				if ($value['league_id'] && $value['description']) {
					$description .= '好友联盟和'.$value['description'];
					$shortDescription .= '好友联盟和';
					$d = explode(",", $value['description']);
					if (count($d) > 2) {
						$shortDescription .= $d[0].",".$d[1]."等好友";
					}else{
						$shortDescription .= $value['description'];
					}
					
				}else if($value['league_id']){
					$description .= '好友联盟';
					$shortDescription .= '好友联盟';
				}else if($value['description']){
					$description .= $value['description'];
					$d = explode(",", $value['description']);
					if (count($d) > 2) {
						$shortDescription .= $d[0].",".$d[1]."等好友";
					}else{
						$shortDescription .= $value['description'];
					}
				}
				$friend = $value['friend_id'];
				$currentPhone = array();
				$currentBenben = array();
				if($friend){
					$currentFriend = explode(',', $friend);
					if (count($currentFriend) > 0) {
						foreach($currentFriend as $tmpFriend){
							if(isset($phoneWithId[$tmpFriend])){
								$currentPhone[] = $phoneWithId[$tmpFriend]['phone'];
								$currentBenben[] = $phoneWithId[$tmpFriend]['benben_id'];
							}	
						}
					}
				}
				$phoneString = '';
				$benbenString = '';
				if (count($currentPhone)) {
					$phoneString = implode(",", $currentPhone);
					$benbenString = implode(",", $currentBenben);
				}
				$lists[] = array('id'=>$value['id'], 'created_time'=>$this->publicTimeDeal($value['created_time']), 'content'=>$this->eraseNull($value['content']), 'description'=>$description, 'short_description'=>$shortDescription,  'league_id'=>$value['league_id'], 'phone'=>$phoneString, 'is_benben'=>$benbenString, 'member_string'=>$value['description']);
			}
		}
		$return['ret_num'] = 0;
		$return['ret_msg'] = '成功';
		$return['authority'] = $authorityNumber;
		$return['lists'] = $lists;
		echo json_encode( $return );
	}

	/**
		删除小喇叭
	*/
	public function actionBroadcastingdel()
	{
		$this->check_key();
		$user = $this->check_user();
		$broadcasting = Frame::getIntFromRequest('id');
		$all = Frame::getIntFromRequest('all');
		$connection = Yii::app()->db;
		//单条删除
		if ($broadcasting > 0) {
			$sql = "update  broadcasting_log set is_del = 1 where member_id = ".$user->id." and id = ".$broadcasting;
			$command = $connection->createCommand($sql);
			$result = $command->execute();
		}else if($all == 1){		//清空
			
			$sql = "update broadcasting_log set is_del = 1 where member_id = ".$user->id;
			$command = $connection->createCommand($sql);
			$result = $command->execute();
		}
		$return['ret_num'] = 0;
		$return['ret_msg'] = '删除成功';
		echo json_encode( $return );
		
	}

	/**
	根据省、市、区获取好友
	*/

	public function actionGetFriendWithArea()
	{
		$connection = Yii::app()->db;
		$this->check_key();
		$user = $this->check_user();
		$member_id = $user->id;
		$province = Frame::getIntFromRequest('p');
		$city = Frame::getIntFromRequest('c');
		$area = Frame::getIntFromRequest('a');

		$sql1 = "select id,group_name name from group_contact where member_id = {$user->id}";
		$command = $connection->createCommand($sql1);
		$result1 = $command->queryAll();
		$groupId = array();
		if ($result1) {
			foreach ($result1 as $key => $value) {
				$groupId[] = $value['id'];
			}
		}
		
		if (count($groupId) > 0) {
			$sql = "select b.is_benben, a.name from group_contact_info as a left join group_contact_phone as b on a.id = b.contact_info_id where b.is_benben > 0 and a.group_id in (".implode(",", $groupId).") group by a.id";
			$command = $connection->createCommand($sql);
			$friend = $command->queryAll();
		}
		
		$fri = array();
		$benbenName = array();
		if (count($friend) > 0) {
			foreach ($friend as $v){
				$fri[] = $v['is_benben'];
				$benbenName[$v['is_benben']] = $v['name'];
			}
		}

		$friendInfo = array();
		if (count($fri) > 0) {
			$sql = "select id, benben_id, poster, name, nick_name,phone from member where benben_id in(".implode(",", $fri).")";
			if ($province) {
				$sql .= ' and province = '.$province;
			}
			if ($city) {
				$sql .= ' and city = '.$city;
			}
			if ($area) {
				$sql .= ' and area = '.$area;
			}
			$command = $connection->createCommand($sql);
			$friendResult = $command->queryAll();
			if ($friendResult) {
				$PinYin = new PYInitials('utf8');
				foreach ($friendResult as $key => $value) {

					$name = $value['name']?$value['name']:$value['nick_name'];
					if (isset($benbenName[$value['benben_id']])) {
						$name = $benbenName[$value['benben_id']];
					}
					$friendInfo[] = array(
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
		$result['ret_num'] = 0;
		$result['ret_msg'] = '操作成功';
		$result['member_list'] = $friendInfo;
		echo json_encode( $result );
	}

}