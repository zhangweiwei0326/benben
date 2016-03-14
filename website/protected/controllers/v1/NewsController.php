<?php
class NewsController extends PublicController{
	public $layout = false;
	
	/**
	 * 消息列表
	 * 涉及news和newsrefresh表
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
		$cri->addCondition("member_id = 0 and t.display=1 and t.created_time>".$retime->refresh_time, 'OR');
		if(empty($last_time)){
			$last_time = 0;
		}
		$cri->addCondition("member_id=$user_id and t.status=0 and t.display=1 and t.created_time >".$last_time, 'OR');
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
			if($value->type == 1 || $value->type == 2 || $value->type == 8){
				$value->mname = "奔犇";
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
		$league = Frame::getIntFromRequest('league_id');//联盟号id
		$content = Frame::getStringFromRequest('content');
		$phone = Frame::getStringFromRequest('phone');//好友号benben_id
		$legphone=Frame::getStringFromRequest('legphone');//联盟中成员号benben_id

		$type = Frame::getIntFromRequest('type');
		$img1 = Frame::saveImage('img1',1);
		$img2 = Frame::saveImage('img2',1);
		$img3 = Frame::saveImage('img3',1);
		$img4 = Frame::saveImage('img4',1);
		$img5 = Frame::saveImage('img5',1);
		$img6 = Frame::saveImage('img6',1);
		$audio = Frame::saveAudio('audio');
		$audiotime = Frame::getIntFromRequest('audiotime');
		if (!$content&&!$img1&&!$img2&&!$img3&&!$img4&&!$img5&&!$img6&&(!$audio||!$audiotime)) {
			$result['ret_num'] = 5301;
			$result['ret_msg'] = '请提供完整信息';
			echo json_encode( $result );
			die();
		}
		if($content) {
			$content = "[小喇叭]" . $content;
		}
		$flag=0;//用于判断是否有图片
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
			$command = $connection->createCommand("select id from number_train where member_id = {$user->id} and status=0 and is_close=0");
			$identity = $command->queryAll();
			if ($identity) {
				$identity1 = strval($identity[0]['id']);
			}
		}else{
			$type = 0;
		}

		//1个月只能够发30天小喇叭
		//购买的小喇叭数量
		$is_exist_horn=PromotionManageAttach::model()->find("member_id={$user['id']}");
		if($is_exist_horn){
			$extra_samll_horn=$is_exist_horn['small_horn_num'];
		}else{
			$extra_samll_horn=0;
		}
		$command = $connection->createCommand("select count(*) as c from broadcasting_log where member_id = {$user->id} and created_time >= ".strtotime(date('Y-m-01', strtotime(date("Y-m-d")))));
		$authority = $command->queryAll();

		if ($authority && $authority[0]['c'] >= 30+$extra_samll_horn) {
			$result['ret_num'] = 5301;
			$result['ret_msg'] ="'你本月小喇叭已经用完";
			echo json_encode( $result );
			die();
		}
//		$time1=time();
		$tpl_attachment=array();//临时附件数组
		//上传图片至环信服务器
		if($img1){
			$img1_a=$this->upload($img1);
			$flag=$flag+1;
		}
		if($img2){
			$img2_a=$this->upload($img2);
			$flag=$flag+1;
		}
		if($img3){
			$img3_a=$this->upload($img3);
			$flag=$flag+1;
		}
		if($img4){
			$img4_a=$this->upload($img4);
			$flag=$flag+1;
		}
		if($img5){
			$img5_a=$this->upload($img5);
			$flag=$flag+1;
		}
		if($img6){
			$img6_a=$this->upload($img6);
			$flag=$flag+1;
		}

		//上传语音到环信服务器
		if($audio){
			$audio_a=$this->upload($audio);
		}
//		$time2=time();
		//如果有联盟成员单独选择，则优先处理
		if($legphone){
			$idinfo=Member::model()->findAll("benben_id in ({$legphone}) and benben_id>0 and id_enable=1");
			if($idinfo) {
				foreach ($idinfo as $ki => $vi) {
					$id[] = $vi['id'];
				}
				$sql = "select league_id, member_id, remark_content, type from league_member where status =1 and type > 0 and league_id = " . $league . " and member_id in (" . implode(",", $id) . ")";
			}else{
				$result['ret_num'] = 5211;
				$result['ret_msg'] = '你选择的用户已被禁用';
				echo json_encode( $result);
				die();
			}
		}else{
			$sql = "select league_id, member_id, remark_content, type from league_member where status =1 and type > 0 and league_id = ".$league;
		}
		$command = $connection->createCommand($sql);
		$info = $command->queryAll();
		//根据手机号查找好友
		$benbenName = $this->getBenbenName($user->id);
		$phoneMember = array();
		if ($phone) {
			$phoneArray = explode(",", $phone);
			if (count($phoneArray) > 0) {
				$sql = "select id, name, nick_name, benben_id,huanxin_username from member where benben_id in (".implode(",", $phoneArray).")";//phone
				$command = $connection->createCommand($sql);
				$phoneMember = $command->queryAll();
			}
			$phone_num=count($phoneMember);
			$allfriendphone = $phoneMember;
		}

		//取出通讯录中所有好友
		$tpl_benben=$this->getfriend($user->id);
		$tpl_minfo=Member::model()->findAll("benben_id in (".implode(",",$tpl_benben).")");
		foreach($tpl_minfo as $kk=>$vv){
			$tpl_fall[]=$vv['id'];
		}
//$time3=time();
		$memberId = array();//发送消息的好友名单
		$legMember = array();//发联盟好友人员消息
		$newsArray = array();
		if ($info || count($phoneMember) > 0) {
			if($league) {
				foreach ($info as $key => $value) {
					if ($value['type'] == 1) {
						$sender = $user->id;
					} else {
						$sender = $value['remark_content'];
					}
					if (in_array($value['member_id'], $tpl_fall)) {
						$memberId[] = $value['member_id'];
						continue;
					}
					$newsArray[] = array('league_id' => $value['league_id'], 'sender' => $sender, 'receive' => $value['member_id']);
				}

				if ($memberId) {
					//剔除发消息的重复好友
					$tpl_fout = array();//非重复好友数组
					foreach ($memberId as $mv) {
						$flagm = 0;
						foreach ($phoneMember as $kf => $vf) {
							if ($vf['id'] == $mv) {
								$flagm = 1;
							}
						}
						if ($flagm === 0) {
							$tpl_fout[] = $mv;
						}
					}
					if ($tpl_fout) {
						$sql_ext = "select id, name, nick_name, benben_id,huanxin_username from member where id in (" . implode(",", $tpl_fout) . ")";//phone
						$command = $connection->createCommand($sql_ext);
						$ext_member_info = $command->queryAll();
					}
					if ($ext_member_info) {
						$legMember = $ext_member_info;
						$allfriendphone = array_merge($phoneMember, $ext_member_info);//好友包含联盟中在通讯录的好友
//					    $phoneMember = array_merge($phoneMember, $ext_member_info);//把联盟中的通讯录好友加入到好友中发送
					}else{
						$allfriendphone = $phoneMember;
					}
				}
			}else {
				$allfriendphone = $phoneMember;
			}

			$newsType = 3;
			if ($type == 1) {
				$newsType = 7;
			}
//			$time4=time();
			$insertNews = array();
			if (count($newsArray) > 0) {
				//发送环信消息,图片、文字分开发
				$finfo=FriendLeague::model()->find("id={$league}");
				$poster=$finfo['poster'] ? URL.$finfo['poster'] : "";
				$other_arr = array("t1" => 1, "t2" => 0,"leg_id"=>$finfo['id'],"leg_poster"=>$poster,"leg_type"=>$finfo['type'],"train_id"=>($identity[0]['id']?$identity[0]['id']:""),"shop"=>($identity[0]['id']?"hz".$user['benben_id']:""),"type"=>$type);
				$receive_text_arr=array();
				$receive_aud_arr=array();
				$receive_img_arr=array();
				foreach ($newsArray as $key => $value) {
					$extra="";
					$minfo=Member::model()->find("id={$value['receive']}");
					if($content){
						$receive_text_arr[]=$minfo['huanxin_username'];
					}
					if($audio){
						$receive_aud_arr[]=$minfo['huanxin_username'];
						$extra.="[语音]";
					}
					if($flag){
						$receive_img_arr[]=$minfo['huanxin_username'];
						$extra.=" [图片]";
					}

					$insertNews[] = "(".$newsType.", ".$value['sender'].", ".$value['receive'].", '".$content.$extra."', 0, ".time().", ".$identity1.",0)";
				}

				//发送文本环信
				if($receive_text_arr) {
					$max=0;
					$count=0;
					$send_arr=array();
					$max=ceil(count($receive_text_arr)/50);
					foreach($receive_text_arr as $k=>$v){
						$count++;
						if(fmod($count,50)==0){
							$this->sendTextMessage($user['huanxin_username'], $send_arr, $content, $other_arr);
							$max--;
							unset($send_arr);
						}else{
							$send_arr[]=$v;
						}
						if($count==count($receive_text_arr) && count($send_arr)){
							$this->sendTextMessage($user['huanxin_username'], $send_arr, $content, $other_arr);
						}
					}
				}
				//发送语言环信
				if($receive_aud_arr) {
					$max=0;
					$count=0;
					$send_arr=array();
					$max=ceil(count($receive_aud_arr)/50);
					foreach($receive_aud_arr as $k=>$v){
						$count++;
						if(fmod($count,50)==0){
							$this->sendAudMessage($user['huanxin_username'],$send_arr,$audio_a,$audiotime,$other_arr);
							$max--;
							unset($send_arr);
						}else{
							$send_arr[]=$v;
						}
						if($count==count($receive_aud_arr) && count($send_arr)){
							$this->sendAudMessage($user['huanxin_username'],$send_arr,$audio_a,$audiotime,$other_arr);
						}
					}
				}
				//发送图片环信
				if($receive_img_arr) {
					$max=0;
					$count=0;
					$send_arr=array();
					$max=ceil(count($receive_img_arr)/50);
					foreach($receive_img_arr as $k=>$v){
						$count++;
						if(fmod($count,50)==0){
							if($img1) {
								$this->sendIMGMessage($user['huanxin_username'], $send_arr, $img1_a, $img1, $other_arr,1);
							}
							if($img2) {
								$this->sendIMGMessage($user['huanxin_username'], $send_arr, $img2_a, $img2, $other_arr,1);
							}
							if($img3) {
								$this->sendIMGMessage($user['huanxin_username'], $send_arr, $img3_a, $img3, $other_arr,1);
							}
							if($img4) {
								$this->sendIMGMessage($user['huanxin_username'], $send_arr, $img4_a, $img4, $other_arr,1);
							}
							if($img5) {
								$this->sendIMGMessage($user['huanxin_username'], $send_arr, $img5_a, $img5, $other_arr,1);
							}
							if($img6) {
								$this->sendIMGMessage($user['huanxin_username'], $send_arr, $img6_a, $img6, $other_arr,1);
							}
							$max--;
							unset($send_arr);
						}else{
							$send_arr[]=$v;
						}
						if($count==count($receive_img_arr) && count($send_arr)){
							if($img1) {
								$this->sendIMGMessage($user['huanxin_username'], $send_arr, $img1_a, $img1, $other_arr,1);
							}
							if($img2) {
								$this->sendIMGMessage($user['huanxin_username'], $send_arr, $img2_a, $img2, $other_arr,1);
							}
							if($img3) {
								$this->sendIMGMessage($user['huanxin_username'], $send_arr, $img3_a, $img3, $other_arr,1);
							}
							if($img4) {
								$this->sendIMGMessage($user['huanxin_username'], $send_arr, $img4_a, $img4, $other_arr,1);
							}
							if($img5) {
								$this->sendIMGMessage($user['huanxin_username'], $send_arr, $img5_a, $img5, $other_arr,1);
							}
							if($img6) {
								$this->sendIMGMessage($user['huanxin_username'], $send_arr, $img6_a, $img6, $other_arr,1);
							}
						}
					}
				}
			}
//			$time5=time();
			if (count($allfriendphone)) {
				//发送环信消息,图片、文字分开发
				$other_arr = array("t1" => 1, "t2" => 0,"train_id"=>($identity[0]['id']?$identity[0]['id']:0),"shop"=>($identity[0]['id']?"hz".$user['benben_id']:""),"type"=>$type);
				$receive_text_arr=array();
				$receive_aud_arr=array();
				$receive_img_arr=array();
				foreach($allfriendphone as $value){
					$extra="";
					if($content){
						$receive_text_arr[]=$value['huanxin_username'];
					}
					if($audio){
						$receive_aud_arr[]=$value['huanxin_username'];
						$extra.="[语音]";
					}
					if($flag){
						$receive_img_arr[]=$value['huanxin_username'];
						$extra.="[图片]";
					}
					$insertNews[] = "(".$newsType.", ".$user->id.", ".$value['id'].", '".$content.$extra."', 0, ".time().", ".$identity1.",0)";
				}

				//发送文本环信
				if($receive_text_arr) {
					$max=0;
					$count=0;
					$send_arr=array();
					$max=ceil(count($receive_text_arr)/50);
					foreach($receive_text_arr as $k=>$v){
						$count++;
						if(fmod($count,50)==0){
							$this->sendTextMessage($user['huanxin_username'], $send_arr, $content, $other_arr);
							$max--;
							unset($send_arr);
						}else{
							$send_arr[]=$v;
						}
						if($count==count($receive_text_arr) && count($send_arr)){
							$this->sendTextMessage($user['huanxin_username'], $send_arr, $content, $other_arr);
						}
					}
				}
				//发送语言环信
				if($receive_aud_arr) {
					$max=0;
					$count=0;
					$send_arr=array();
					$max=ceil(count($receive_aud_arr)/50);
					foreach($receive_aud_arr as $k=>$v){
						$count++;
						if(fmod($count,50)==0){
							$this->sendAudMessage($user['huanxin_username'],$send_arr,$audio_a,$audiotime,$other_arr);
							$max--;
							unset($send_arr);
						}else{
							$send_arr[]=$v;
						}
						if($count==count($receive_aud_arr) && count($send_arr)){
							$this->sendAudMessage($user['huanxin_username'],$send_arr,$audio_a,$audiotime,$other_arr);
						}
					}
				}
				//发送图片环信
				if($receive_img_arr) {
					$max=0;
					$count=0;
					$send_arr=array();
					$max=ceil(count($receive_img_arr)/50);
					foreach($receive_img_arr as $k=>$v){
						$count++;
						if(fmod($count,50)==0){
							if($img1) {
								$this->sendIMGMessage($user['huanxin_username'], $send_arr, $img1_a, $img1, $other_arr,1);
							}
							if($img2) {
								$this->sendIMGMessage($user['huanxin_username'], $send_arr, $img2_a, $img2, $other_arr,1);
							}
							if($img3) {
								$this->sendIMGMessage($user['huanxin_username'], $send_arr, $img3_a, $img3, $other_arr,1);
							}
							if($img4) {
								$this->sendIMGMessage($user['huanxin_username'], $send_arr, $img4_a, $img4, $other_arr,1);
							}
							if($img5) {
								$this->sendIMGMessage($user['huanxin_username'], $send_arr, $img5_a, $img5, $other_arr,1);
							}
							if($img6) {
								$this->sendIMGMessage($user['huanxin_username'], $send_arr, $img6_a, $img6, $other_arr,1);
							}
							$max--;
							unset($send_arr);
						}else{
							$send_arr[]=$v;
						}
						if($count==count($receive_img_arr) && count($send_arr)){
							if($img1) {
								$this->sendIMGMessage($user['huanxin_username'], $send_arr, $img1_a, $img1, $other_arr,1);
							}
							if($img2) {
								$this->sendIMGMessage($user['huanxin_username'], $send_arr, $img2_a, $img2, $other_arr,1);
							}
							if($img3) {
								$this->sendIMGMessage($user['huanxin_username'], $send_arr, $img3_a, $img3, $other_arr,1);
							}
							if($img4) {
								$this->sendIMGMessage($user['huanxin_username'], $send_arr, $img4_a, $img4, $other_arr,1);
							}
							if($img5) {
								$this->sendIMGMessage($user['huanxin_username'], $send_arr, $img5_a, $img5, $other_arr,1);
							}
							if($img6) {
								$this->sendIMGMessage($user['huanxin_username'], $send_arr, $img6_a, $img6, $other_arr,1);
							}
						}
					}
				}
			}
//			$time6=time();
//			$league_count = 0;
//			if ($league > 0&& $legphone>0) {
//				$league_count = max(0, $this->getLeagueCount($league)-1);
//			}else if($league > 0 && empty($legphone)){
//				$league_count=count();
//			}

			if (count($insertNews) > 0) {
//				$sql = "insert into news (type, sender, member_id, content, status, created_time, identity1,display) values ".implode(",", $insertNews);
//				$command = $connection->createCommand($sql);
//				$result1 = $command->execute();

				$phoneMemberName = array();//broadcastingLog中的description
				$phoneMemberId = array();
				foreach($phoneMember as $kp=>$vp){
					$phoneMemberId[] = $vp['id'];
					if (isset($benbenName[$vp['benben_id']])) {
						$phoneMemberName[] = $benbenName[$vp['benben_id']];
					}else{
						$phoneMemberName[] = $vp['nick_name'];
					}
				}
				//发送小喇叭历史记录
				$broadcastingLog = new BroadcastingLog();
				$broadcastingLog->member_id = $user->id;
				$broadcastingLog->league_id = $league;
				$broadcastingLog->friend_id = implode(",", $phoneMemberId);
				// $broadcastingLog->receive_count = count($newsArray)+count($phoneMember);
				$broadcastingLog->receive_count =count($allfriendphone)+count($info)-count($tpl_fout);
				$broadcastingLog->content = $content.$extra;
				$broadcastingLog->type = $type;
				if(count($phoneMemberName)>0){
					$description=implode(",", $phoneMemberName);
				}
				$broadcastingLog->description = $description;
				$broadcastingLog->created_time = time();

				if($broadcastingLog->save()){
					if($img1){
						$tpl_attachment[]="(".$broadcastingLog['id'].",'".$img1."',1)";
					}
					if($img2){
						$tpl_attachment[]="(".$broadcastingLog['id'].",'".$img2."',1)";
					}
					if($img3){
						$tpl_attachment[]="(".$broadcastingLog['id'].",'".$img3."',1)";
					}
					if($img4){
						$tpl_attachment[]="(".$broadcastingLog['id'].",'".$img4."',1)";
					}
					if($img5){
						$tpl_attachment[]="(".$broadcastingLog['id'].",'".$img5."',1)";
					}
					if($img6){
						$tpl_attachment[]="(".$broadcastingLog['id'].",'".$img6."',1)";
					}
					if($audio){
						$tpl_attachment[]="(".$broadcastingLog['id'].",'".$audio."',2)";
					}
					if($tpl_attachment) {
						$sqlb = "insert into broadcasting_attachment (broadcast_id, attachment, type) values " . implode(",", $tpl_attachment);
						$command = $connection->createCommand($sqlb);
						$resultb = $command->execute();
					}
				}
			}
//			$time7=time();
			$this->addIntegral($user->id, 10);
			//发完小喇叭检查购买喇叭数量是否需要递减
			if($authority[0]['c'] >=30 && $authority[0]['c']<=30+$extra_samll_horn){
				$is_exist_horn->small_horn_num=$is_exist_horn->small_horn_num-($authority[0]['c']-30)-1;
				$is_exist_horn->save();
			}
			$result['ret_num'] = 0;
			$result['ret_msg'] = '小喇叭发送成功';
//			$result['allTime']=array(
//				$time1,$time2,$time3,$time4,$time5,$time6,$time7
//			);
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
		$sql = "select a.* from broadcasting_log as a where a.is_del = 0 and a.member_id = ".$user->id." order by a.id desc";
		$command = $connection->createCommand($sql);
		$infoQuery = $command->queryAll();
		$broadid=array();
		foreach($infoQuery as $vv){
			$broadid[]=$vv['id'];
		}
		//取附件
		$tpl_att = array();
		if($broadid) {
			$sqla = "select * from broadcasting_attachment where broadcast_id in (" . implode(",", $broadid) . ")";
			$command = $connection->createCommand($sqla);
			$attachinfo = $command->queryAll();
			foreach ($attachinfo as $k => $v) {
				$attachinfo[$k]['attachment'] = $v['attachment'] ? URL . $v['attachment'] : "";
				//确定是图片
				if ($attachinfo[$k]['type'] == 1) {
					if ($v['attachment']) {
						$tpl_thumb = explode("/", $v['attachment']);
						$tpl_thumb[4] = "small" . $tpl_thumb[4];
						$thumb = implode("/", $tpl_thumb);
					}
					$attachinfo[$k]['thumb'] = $v['attachment'] ? URL . $thumb : "";
					$siz_info = getimagesize($attachinfo[$k]['thumb']);
					$attachinfo[$k]['Width'] = $siz_info[0];
					$attachinfo[$k]['Height'] = $siz_info[1];
				} else {
					$attachinfo[$k]['Width'] = "";
					$attachinfo[$k]['Height'] = "";
					$attachinfo[$k]['thumb'] = "";
				}
				$tpl_att[$v['broadcast_id']][] = $attachinfo[$k];
			}
		}

		$lists = array();
		$command = $connection->createCommand("select count(*) as c from broadcasting_log where member_id = ".$user->id." and created_time >= ".strtotime(date('Y-m-01', strtotime(date("Y-m-d")))));
		$authority = $command->queryAll();
		$authorityNumber = 30;
		$is_exist=PromotionManageAttach::model()->find("member_id={$user['id']}");
		if($is_exist['small_horn_num']){
			$all_small=$is_exist['small_horn_num'];
		}else{
			$all_small=0;
		}
		if ($authority) {
			$authorityNumber = 30 - $authority[0]['c'] + $all_small;
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
				$finfo=FriendLeague::model()->find("id=".$value['league_id']);
				$leg_type_name=$finfo['type']==1 ? "工作联盟" : "英雄联盟";
				if ($value['league_id'] && $value['description']) {
					$description .= $leg_type_name.','.$value['description'];
					$shortDescription .= $leg_type_name.',';
					$d = explode(",", $value['description']);
					if (count($d) > 2) {
						$shortDescription .= $d[0].",".$d[1]."等好友";
					}else{
						$shortDescription .= $value['description'];
					}
					
				}else if($value['league_id']){
					$description .= $leg_type_name;
					$shortDescription .= $leg_type_name;
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
				preg_match_all("/\](.+?)\[|\](.+)/",$this->eraseNull($value['content']),$matches);
//				var_dump($matches);
				$lists[] = array(
					'id'=>$value['id'],
					'created_time'=>$this->publicTimeDeal($value['created_time']),
					'contentdetail'=>$matches[1][0] ? $matches[1][0] : ($matches[2][0] ? $matches[2][0] : ""),
					'content'=>$this->eraseNull($value['content']),
					'description'=>$description,
					'short_description'=>$shortDescription,
					'league_id'=>$value['league_id'],
					'phone'=>$phoneString,
					'is_benben'=>$benbenString,
					'member_string'=>$value['description'],
					"attachment"=>$tpl_att[$value['id']] ? $tpl_att[$value['id']] : array()
				);
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
			//$sql = "select b.is_benben, a.name from group_contact_info as a left join group_contact_phone as b on a.id = b.contact_info_id where b.is_benben > 0 and a.group_id in (".implode(",", $groupId).") group by a.id";
			$sql = "select benben_id, name from group_contact_info  where member_id = {$user->id} and benben_id > 0";
			$command = $connection->createCommand($sql);
			$friend = $command->queryAll();
		}
		
		$fri = array();
		$benbenName = array();
		if (count($friend) > 0) {
			foreach ($friend as $v){
				$fri[] = $v['benben_id'];
				$benbenName[$v['benben_id']] = $v['name'];
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

	/**
	根据省、市、区获取好友联盟成员
	 */

	public function actionGetFriendLeagueWithArea()
	{
		$connection = Yii::app()->db;
		$this->check_key();
		$user = $this->check_user();
		$province = Frame::getIntFromRequest('p');
		$city = Frame::getIntFromRequest('c');
		$area = Frame::getIntFromRequest('a');

		$msql="select a.member_id,a.nick_name from league_member as a LEFT JOIN friend_league as b on a.league_id=b.id
		where b.member_id={$user['id']} and a.type!=0 and a.status=1 and b.status=0 and b.is_delete=0";
		$command = $connection->createCommand($msql);
		$resultm = $command->queryAll();
		foreach($resultm as $kk=>$vv){
			$id_arr[]=$vv['member_id'];
			$name_tpl[$vv['member_id']]=$vv['nick_name'];
		}

		//修改昵称，组备注，通讯录昵称，注册昵称
		$nick_name_all = $this->getfriend($user['id'], 3);

		$friendInfo = array();
		if (count($id_arr) > 0) {
			$sql = "select id, benben_id, poster, name, nick_name,phone from member where id in(".implode(",", $id_arr).")";
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
					if ($nick_name_all[$value['id']]) {
						$nick_name = $nick_name_all[$value['id']];
					}else{
						$nick_name=$value['nick_name'];
					}

					$name = $name_tpl[$value['id']]?$name_tpl[$value['id']]:$nick_name;

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

	/*
 	 * 请求与通知，详细信息查看
	 * 针对好友请求和好友联盟请求
	 * 使用到member，friendleague，numbertrain三张表
 	 * */
	public function actionNotification(){
		$this->check_key();
		$user = $this->check_user();
		$memberid = Frame::getIntFromRequest('memberid');
		$hxusername = Frame::getStringFromRequest('hxusername');
		$legid = Frame::getIntFromRequest('legid');

		if($memberid||$hxusername){
			if($hxusername){
				$tpl_huanxin=Member::model()->find("huanxin_username='{$hxusername}'");
				$memberid=$tpl_huanxin['id'];
			}
			//好友请求详细信息
			$senduser=Member::model()->find("id={$memberid}");
			if($senduser){
				$tpl_d=$this->ProCity(array(0=>$senduser));
				$province=$tpl_d[$senduser['province']];
				$city=$tpl_d[$senduser['city']];
				$area=$tpl_d[$senduser['area']];
				$sex=$senduser['sex'];
				$age=$senduser['age'] ? round((time()-$senduser['age'])/86400/365) : "";

				//直通车即商铺
				$tpl_t=NumberTrain::model()->find("member_id={$memberid} and is_close=0 and status=0");

				//好友联盟
				$tpl_l=FriendLeague::model()->find("member_id={$memberid} and is_delete=0 and status=0");
				$district=$this->ProCity(array(0=>$tpl_l));

				$result['ret_num'] = 0;
				$result['ret_msg'] = '操作成功';

				$result['nick_name']=$senduser['nick_name'];
				$result['benben_id']=$senduser['benben_id'];
				$result['sex']=$sex;
				$result['age']=$age;
				$result['district']=$province." ".$city." ".$area;
				$result['train_id']=$tpl_t['id'];
				$result['pic']=URL.$tpl_t['poster'];
				$result['short_name']=$tpl_t['short_name'];
				$result['tag']=$tpl_t['tag'];
				$result['legid']=$tpl_l['id'];
				$result['leg_poster']=URL.$tpl_l['poster'];
				$result['leg_name']=$tpl_l['name'];
				$result['leg_district']=$district[$tpl_l['province']]." ".$district[$tpl_l['city']]." ".$district[$tpl_l['area']];
				echo json_encode($result);
			}else{
				$result['ret_num'] = 1000;
				$result['ret_msg'] = '用户已被禁用';
				echo json_encode( $result);
			}
		}elseif($legid){
			//好友联盟请求详细信息
			$tpl=FriendLeague::model()->find("id={$legid} and is_delete=0 and status=0");
			$district=$this->ProCity(array(0=>$tpl));
			if($tpl){
				$result['ret_num'] = 0;
				$result['ret_msg'] = '操作成功';

				$result['leg_name']=$tpl['name'];
				$result['leg_number']=$tpl['number'] ? $tpl['number'] : 0;
				$result['leg_type']=$tpl['type']==1 ? "工作联盟" : "英雄联盟";
				$result['leg_poster']=URL.$tpl['poster'] ? URL.$tpl['poster']: "";
				$result['leg_announcement']=$tpl['announcement'] ? $tpl['announcement']: "";
				$result['leg_description']=$tpl['description'] ? $tpl['description']:"";
				$result['leg_district']=$district[$tpl['province']]." ".$district[$tpl['city']]." ".$district[$tpl['area']];
				echo json_encode($result);
			}else{
				$result['ret_num'] = 1000;
				$result['ret_msg'] = '该联盟不存在';
				echo json_encode( $result);
			}
		}else{
			$result['ret_num'] = 100;
			$result['ret_msg'] = '缺少参数';
			echo json_encode( $result );
		}
	}
}