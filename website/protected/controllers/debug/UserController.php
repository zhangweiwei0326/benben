<?php
class UserController extends PublicController
{
	public $layout = false;
	/**
	 发送手机验证码
	 */
	public function actionSendcode(){
		$this->check_key();	    
		$phone = Frame::getStringFromRequest('phone');
		$is_reset = Frame::getStringFromRequest('is_reset');
		if (empty ( $phone ) || strlen($phone) != 11) {
			$result ['ret_num'] = 2010;
			$result ['ret_msg'] = '请输入手机号';
			echo json_encode ( $result );
			die ();
		}
		//查看手机号码是否已注册
		$user = Member::model ()->find("phone = '{$phone}' and id_enable = 1");
		
// 		if(empty($user) && empty($is_reset)){//注册
// 			//返回验证码
// 			$codecontent = mt_rand(10000,99999);
// 			$content = "您的犇犇注册验证码为:".$codecontent."。请在页面填写验证码完成验证。如非本人操作，忽略本条短信。";
// 			$idcode = Frame::sendsns($phone,$content);
// 			if($idcode){
// 				$code = new Idcode();
// 				$code->phone = $phone;
// 				$code->idcode = $codecontent;
// 				$code->send_time = time();
// 				if($code->save()){
// 					$result ['ret_num'] = 0;
// 					$result ['ret_msg'] = '验证码发送成功';
// 					$result ['ret_code'] = $codecontent;
// 				}
// 			}else{
// 				$result ['ret_num'] = 2008;
// 				$result ['ret_msg'] = '获取验证码失败';
// 			}
// 		}else if($user && $is_reset == 1){//找回密码
// 			$content = "您的犇犇找回密码验证码为:".$codecontent."。请在页面填写验证码完成验证。如非本人操作，忽略本条短信。";
// 			$idcode = Frame::sendsns($phone,$content);
// 			if($idcode){
// 				$code = new Idcode();
// 				$code->phone = $phone;
// 				$code->idcode = $codecontent;
// 				$code->send_time = time();
// 				if($code->save()){
// 					$result ['ret_num'] = 0;
// 					$result ['ret_msg'] = '验证码发送成功';
// 					$result ['ret_code'] = $codecontent;
// 				}
// 			}else{
// 				$result ['ret_num'] = 2008;
// 				$result ['ret_msg'] = '获取验证码失败';
// 			}
// 		}else if(empty($user) && $is_reset == 2){//更换号码
// 			$content = "您的犇犇更换绑定验证码为:".$codecontent."。请在页面填写验证码完成验证。如非本人操作，忽略本条短信。";
// 			$idcode = Frame::sendsns($phone,$content);
// 			if($idcode){
// 				$code = new Idcode();
// 				$code->phone = $phone;
// 				$code->idcode = $codecontent;
// 				$code->send_time = time();
// 				if($code->save()){
// 					$result ['ret_num'] = 0;
// 					$result ['ret_msg'] = '验证码发送成功';
// 					$result ['ret_code'] = $codecontent;
// 				}
// 			}else{
// 				$result ['ret_num'] = 2008;
// 				$result ['ret_msg'] = '获取验证码失败';
// 			}
// 		}else{
// 	  	$result ['ret_num'] = 2007;
// 	  	$result ['ret_msg'] = '该手机号码已注册';
// 	  }
	  
	  if(!$user && $is_reset == 1 ){
		  	$result ['ret_num'] = 2007;
		  	$result ['ret_msg'] = '该手机号码未注册';
		  	echo json_encode ( $result );
		  	exit();
	  }
	  
		
		if((empty($user) && empty($is_reset)) || ($user && $is_reset == 1) || (empty($user) && $is_reset ==2)){ 
			//返回验证码
			$codecontent = mt_rand(10000,99999);
			//$content = "您的犇犇注册验证码为:".$codecontent."。请在页面填写验证码完成验证。如非本人操作，忽略本条短信。";
			$tempId = TEMPID;//注册
			if($is_reset == 1){
				//$content = "您的犇犇找回密码验证码为:".$codecontent."。请在页面填写验证码完成验证。如非本人操作，忽略本条短信。";
				$tempId = TEMPIDP;//找回密码
			}
			if($is_reset == 2){
				//$content = "您的犇犇更换绑定验证码为:".$codecontent."。请在页面填写验证码完成验证。如非本人操作，忽略本条短信。";
				$tempId = TEMPIDC;//更换绑定
			}
						
			$idcode = Frame::sendsns($phone,$codecontent,$tempId);
			if($idcode->statusCode!=0) {
				$result ['ret_num'] = 2008;
				$result ['ret_msg'] = '获取验证码失败';
			}else{
				$code = new Idcode();
				$code->phone = $phone;
				$code->idcode = $codecontent;
				$code->send_time = time();
				if($code->save()){
					$result ['ret_num'] = 0;
					$result ['ret_msg'] = '验证码发送成功';
					$result ['ret_code'] = $codecontent;
				}
			}
			
	  }else{
	  	$result ['ret_num'] = 2007;
	  	$result ['ret_msg'] = '该手机号码已注册';
	  }
	  echo json_encode ( $result );
	}
	
	/**
	 二维码信息
	 */
	public function actionGetqrcode(){
            $qr_name = Frame::getStringFromRequest('qr_name');
            $userid =  base64_decode(substr($qr_name,16));
            $user = $this->check_user();
            $userinfo = Member::model()->find("id = '{$userid}'");
            if (!$userinfo) {
            	$result ['ret_num'] = 1;
            	$result ['ret_msg'] = '用户不存在';
            	$result['name'] = "";
            	$result['phone'] = "";
            	$this->render("getqrcode",array(
            			"qrcode"=>$result
            	));
            }
            if(substr(md5($userinfo->phone),0,16) == substr($qr_name,0,16)){
            	$connection = Yii::app()->db;
            	$sqlf = "select friend_id1,friend_id2 from friend_relate where ((friend_id1 = {$userid} and friend_id2 = {$user->id}) or (friend_id1 = {$user->id} and friend_id2 = {$userid})) and status = 1";
            	$command = $connection->createCommand($sqlf);
            	$friend = $command->queryAll();
            	
            	$result ['ret_num'] = 2;
            	$result ['ret_msg'] = '用户存在';
            	$result['name'] = $userinfo->nick_name;
            	$result['phone'] = $userinfo->phone;
            	
            	$this->render("getqrcode",array(
            			"qrcode"=>$result
            	));
            }
            
	}
	
	/**
	 注册会员信息
	 */
	public function actionRegister(){
	
		// if ((Yii::app()->request->isPostRequest)) {
		if (1) {
			$this->check_key();
			$key = Frame::getStringFromRequest('key');
			$nick_name = Frame::getStringFromRequest('nick_name');
			$phone = Frame::getStringFromRequest('phone');
			$age = Frame::getIntFromRequest('age');
			$sex = Frame::getIntFromRequest('sex');
			$pwd = Frame::getStringFromRequest('password');
			$repwd = Frame::getStringFromRequest('repassword');
			$idcode = Frame::getIntFromRequest('code');
			$phone_model = Frame::getStringFromRequest('phone_model');
			
			if (empty ( $idcode ) || empty ( $nick_name ) || empty ( $sex ) || empty ( $pwd ) || empty ( $repwd ) || empty ( $phone )) {
				$result ['ret_num'] = 2009;
				$result ['ret_msg'] = '输入信息不完整';
				echo json_encode ( $result );
				die ();
			}
			if ($pwd != $repwd) {
				$result ['ret_num'] = 2210;
				$result ['ret_msg'] = '两次密码输入不一致';
				echo json_encode ( $result );
				die ();
			}
			
			$criteria = new CDbCriteria() ;
			$criteria -> select = "idcode";
			$criteria -> condition = 'phone = :phone';
			$criteria -> order = 'send_time desc';
			$criteria ->params = array (':phone' => $phone) ;
			$code = Idcode::model ()->find($criteria);			
			if ($code->idcode != $idcode) {
				$result ['ret_num'] = 2005;
				$result ['ret_msg'] = '验证码错误';
				echo json_encode ( $result );
				die ();
			}
			$re = Member::model()->find("phone = {$phone}");
			if($re->id_enable == 1){
				$result ['ret_num'] = 2007;
				$result ['ret_msg'] = '该手机号码已注册';
				echo json_encode ( $result );
				die ();
			}
			if($re->id_enable == 2){
				$user = $re;
			}else{
				$user = new Member();
			}
						
			$user->nick_name = $nick_name;
			$user->phone = $phone;
			$user->age = $age;
			$user->sex = $sex;
			$user->password = md5($pwd);			
			$user->created_time = time();
			$user->phone_model = $this->getmodel($phone_model);
			$user->id_enable = 1;

			//注册环信用户
			$username = md5($phone);
			$password = $phone;
			$nickname = $nick_name;
			if ($this->checkUsername($username)) {
				$resulh = $this->openResiter($username, $password, $nickname);
				$reh = json_decode($resulh, true);
				if($reh['error']){
					$err = new HuanxinError();
					$err->error_info = $reh['error'];
					$err->created_time = time();
					$err->member_id = $user->id;
					$err->save();
					$result ['ret_num'] = 2008;
					$result ['ret_msg'] = $reh['error'];
					echo json_encode ( $result );
					die ();
				}else{						
					$user->huanxin_username = $reh['entities'][0]['username'];
					$user->huanxin_uuid = $reh['entities'][0]['uuid'];
					$user->huanxin_password = $phone;
					// $user->update();
				}					
			}



			$connection = Yii::app()->db;
			if($user->save()){
				//注册成功后进行登录
				//生成Token
				$token = md5($key.time()).md5($key.$user->id);
				$user->token = $token;
				//生成犇犇号
				$sql = "select max(benben_id) maxid from member";
				
				$command = $connection->createCommand($sql);
				$result1 = $command->queryAll();
				if($result1[0]['maxid']){
					$benben_id = $result1[0]['maxid']+1;
					while(true){
						$check = $this->checkbenben($benben_id);
						if($check){
							break;
						}
						$benben_id++;
					}
										
					$user->benben_id = $benben_id;										
				}else{
					$user->benben_id = 20003;
				}
				// //将通讯录中的该手机号设置为已经为犇犇用户--跟后面重复了
				// $command = $connection->createCommand("update group_contact_phone set is_benben = ".$benben_id." where phone = '".$phone."'");
				// $resultc = $command->execute();

				//生成二维码
				$pathinfo = "index.php/v1/user/getqrcode/qr_name/";
				$qrcodeinfo = URL."/".$pathinfo.substr(md5($phone),0,16).base64_encode($user->id);
				$qrcodename = "uploads/images/qrcode/".substr(md5($phone),0,16).base64_encode($user->id).".png";
				$user->qrcode = "/".$qrcodename;
				include('lib/phpqrcode/phpqrcode.php');
				QRcode::png($qrcodeinfo,$qrcodename);
// 					$url = URL."/index.php/v1/user/qrcode/key/{$key}/userid/{$user->id}/phone/{$phone}";
// 					$url = "http://127.0.0.1:999/index.php/v1/user/qrcode/key/{$key}/userid/{$user->id}/phone/{$phone}";
// 					$file = file_get_contents($url);var_dump($file);
// 					$user->qrcode = $file;
				    $user->update();
				
				//写session
				Yii::app()->session['memberid']=$token;					
				//写登录记录
				$member_login = new MemberLogin();
				$member_login->member_id = $user->id;
				$member_login->phone_model = $this->getmodel($phone_model);;
				$member_login->created_time = time();
				$member_login->save();
				//注册环信用户
				// $username = md5($phone);
				// $password = $phone;
				// $nickname = $nick_name;
				// if ($this->checkUsername($username)) {
				// 	$resulh = $this->openResiter($username, $password, $nickname);
				// 	$reh = json_decode($resulh, true);
				// 	if($reh['error']){
				// 		$err = new HuanxinError();
				// 		$err->error_info = $reh['error'];
				// 		$err->created_time = time();
				// 		$err->member_id = $user->id;
				// 		$err->save();
				// 	}else{						
				// 		$user->huanxin_username = $reh['entities'][0]['username'];
				// 		$user->huanxin_uuid = $reh['entities'][0]['uuid'];
				// 		$user->huanxin_password = $phone;
				// 		$user->update();
				// 	}					
				// }
				//更新通讯录表group_contact_phone
			    $sql = "update group_contact_phone set is_benben = {$user->benben_id} where phone = '{$phone}'";
				$command = $connection->createCommand($sql);
				$result0 = $command->execute();
				//更新group_contact_info
				//获取需要更新的用户
				$sql = "select a.id from group_contact_info a left join group_contact_phone b on a.id=b.contact_info_id where a.benben_id=0 and b.is_benben={$user->benben_id}";		
				$command = $connection->createCommand($sql);
				$result = $command->queryAll();
				if ($result) {
					$infoArray = array();
					foreach ($result as $value) {
						$infoArray[] = $value['id'];
					}
					//更新benben_id
					if (count($infoArray) > 0) {
						$idstring = implode(',', $infoArray);
						$sql = "update group_contact_info set benben_id={$user->benben_id} where id in ($idstring)";
						$command = $connection->createCommand($sql);
						$resultinfo = $command->execute();
					}
				}

				//更新表enterprise_member
				$sql = "update enterprise_member set member_id = {$user->id} where phone = '{$phone}'";
				$command = $connection->createCommand($sql);
				$result2 = $command->execute();
				
				//更新消息刷新记录表
				$newsre = new NewsRefresh();
				$newsre->member_id = $user->id;
				$newsre->refresh_time = time();
				$newsre->save();
				
				$result ['ret_num'] = 0;
				$result ['ret_msg'] = '操作成功';
				$result['user'] = array(
						"UserId"=>$user->id,
						"BenbenId"=>$user->benben_id,
						"name"=>"",
						"UserNickname"=>$user->nick_name,
						"UserSex"=>$user->sex,
						"UserPoster"=>$user->poster ? URL.$user->poster : "",
						"UserQrcode"=>$user->qrcode ? URL.$user->qrcode : "",
						"Age"=>$user->age,
						"Phone"=>$user->phone,
						"Token"	=>$token,
						"UserInfo"=>$user->userinfo,
						"integral"=>"",
						"level"=>"",
						"appellation" => "",
						"huanxin_username"=>$this->eraseNull($user->huanxin_username),
						"huanxin_password"=>$phone,
						"huanxin_uuid"=>$user->huanxin_uuid,
						"creation_disable"=>$user->creation_disable,
						"buy_disable"=>$user->buy_disable,
						"enterprise_disable"=>$user->enterprise_disable,
						"group_disable"=>$user->group_disable,
						"store_disable"=>$user->store_disable,
						"league_disable"=>$user->league_disable,
						"Address"=>$user->address ? $user->address : "",
						"ProCity"=>"",
						"ZhiTongChe"=>"",
						"group_list" => ""
				);
			}else{
				$result ['ret_num'] = 2003;
				$result ['ret_msg'] = '信息添加失败	';
			}
			echo json_encode ( $result );
		}
	}
	//修改二维码
	public function actionQr12(){
		include('lib/phpqrcode/phpqrcode.php');
		$re = Member::model()->findAll();
		foreach ($re as $v){
			//生成二维码
			$phone = $v->phone;
			$pathinfo = "index.php/v1/user/getqrcode/qr_name/";
			$qrcodeinfo = URL."/".$pathinfo.substr(md5($phone),0,16).base64_encode($v->id);
			$qrcodename = "uploads/images/qrcode/".substr(md5($phone),0,16).base64_encode($v->id).".png";
			$v->qrcode = "/".$qrcodename;			
			QRcode::png($qrcodeinfo,$qrcodename);			
			$v->update();
		}
	}
	/**
	 会员登录
	 */
	public function actionLogin(){
		$key = $this->check_key();
		//手机号，密码
		
		$phone = Frame::getStringFromRequest('phone');
		$password = Frame::getStringFromRequest('password');
		$phone_model = Frame::getStringFromRequest('phone_model');
		if (empty ( $phone )) {
			$result ['ret_num'] = 2010;
			$result ['ret_msg'] = '请输入手机号';
			echo json_encode ( $result );
			die ();
		}
		if (empty ( $password )) {
			$result ['ret_num'] = 2011;
			$result ['ret_msg'] = '请输入登陆密码';
			echo json_encode ( $result );
			die ();
		}
		//$pinfo = $this->pcinfo();
		$pwd = md5 ( $password );
		$user = Member::model ()->find("(phone = '{$phone}' or benben_id = '{$phone}') and password = '{$pwd}'");
		if (empty ( $user )) {
			$result ['ret_num'] = 2012;
			$result ['ret_msg'] = '手机号码或密码错误';
		}else{
			$status = array('0'=>'启用', '1'=>'禁用1周', '2'=>'禁用2周', '3'=>'禁用1个月', '4'=>'禁用3个月', '5'=>'无限期禁用');
			$timeArray = array(1=>7*24*60*60,2=>14*24*60*60,3=>30*24*60*60,4=>90*24*60*60,5=>'无限期');
			if($user->status != 0){
				$info = MemberDisable::model()->find("member_id = {$user->id} and status = {$user->status} order by created_time desc");
				if($info->status == 5){
					$opentime = "您的帐号被无限期禁用"; 
				}else{
					$opentime = "您的帐号被禁用,将于".(date("Y-m-d H:i:s",$info->created_time + $timeArray[$user->status]))."解禁";
				}
				$result ['ret_num'] = 2092;
				$result ['ret_msg'] = $opentime;
				echo json_encode ( $result );
				die ();
			}
			//生成Token
			
			$token = md5($key.time()).md5($key.$user->id);
			$user->token = $token;
			$user->phone_model = $this->getmodel($phone_model);;
			$user->update();
			//写session
			$time = 7*24*3600;
			session_set_cookie_params($time);
			session_start();
			session_regenerate_id(true);				
			Yii::app()->session['memberid']=$token;
			
			
			//写登录记录
			$member_login = new MemberLogin();
			$member_login->member_id = $user->id;
			$member_login->phone_model = $this->getmodel($phone_model);;
			$member_login->created_time = time();
			$member_login->save();
			//查询直通车信息
			$zhitongche = "";
			$nt = NumberTrain::model()->find("member_id = {$user->id}");
			if($nt){
				$zhitongche = array(
							"Id"=>$nt->id ? $nt->id : "",
							"Name"=>$nt->name ? $nt->name : "",
							"ShortName"=>$nt->short_name ? $nt->short_name : ""
					);
			}
			//查询等级
			$level = 0;
			$appellation = "";
			$level_all = getlevel();
			foreach ($level_all as $va){
				if($user->integral <= $va[1]){
					$level = $va[0];
					$appellation = $va[2];
					break;
				}
			}
			//查询是否拥有好友联盟
			$haveleague = LeagueMember::model()->find("member_id = {$user->id} and type < 2 order by type");
			if($haveleague){
				if ($haveleague->type == 1) {
					$league = 1;
				}else{
					$league = 2;
				}	
			}else{
				$league = 0;
			}
			//省市
			$pro = array("province"=>$user->province,"city"=>$user->city);
			$pro_arr = $this->ProCity(array($pro));
			//解禁时间
			$opentime = array();
			$timeArray = array(1=>7*24*60*60,2=>14*24*60*60,3=>30*24*60*60,4=>90*24*60*60,5=>'无限期');
			$typeArray = array(1=>'creation_disable',2=>'buy_disable',3=>'enterprise_disable',4=>'group_disable',5=>'store_disable',6=>'league_disable');
			$servicetime = ServiceDisable::model()->findAll("member_id = {$user->id} order by created_time desc");
			
			if($servicetime){
				$inArray = array();
				foreach ($servicetime as $va){
					if(in_array($va->type, $inArray)) continue;
					if($va->status == 5){
						$opentime[$typeArray[$va->type]] = 1;
					}else{
						$opentime[$typeArray[$va->type]] = $timeArray[$va->status]+$va->created_time;
					}
					$inArray[] = $va->type;
				}
			}
												
			$result ['ret_num'] = 0;
			$result ['ret_msg'] = '操作成功';
			$result['user'] = array(
					"UserId"=>$user->id,
					"BenbenId"=>$user->benben_id,
					"name"=>$user->name,
					"UserNickname"=>$user->nick_name,
					"UserSex"=>$user->sex,
					"UserPoster"=>$user->poster ? URL.$user->poster : "",
					"UserQrcode"=>$user->qrcode ? URL.$user->qrcode : "",
					"Age"=>$user->age,
					"Phone"=>$user->phone,
					"Token"	=>$token,
					"UserInfo"=>$user->userinfo,
					"integral"=>$user->integral,
					"level"=>$level,
					"appellation"=>$this->eraseNull($appellation),
					"huanxin_username"=>$this->eraseNull($user->huanxin_username),
					"huanxin_password"=>$user->huanxin_password,
					"huanxin_uuid"=>$user->huanxin_uuid,
					"creation_disable"=>$user->creation_disable ? $opentime['creation_disable']:$user->creation_disable,
					"buy_disable"=>$user->buy_disable ? $opentime['buy_disable']:$user->buy_disable,
					"enterprise_disable"=>$user->enterprise_disable ? $opentime['enterprise_disable']:$user->enterprise_disable,
					"group_disable"=>$user->group_disable ? $opentime['group_disable']:$user->group_disable,
					"store_disable"=>$user->store_disable ? $opentime['store_disable']:$user->store_disable,
					"league_disable"=>$user->league_disable ? $opentime['league_disable']:$user->league_disable,
					"Address"=>$user->address ? $user->address : "",
					"ProCity"=>$pro_arr[$user->province].' '.$pro_arr[$user->city],
					"ZhiTongChe"=>$zhitongche,
					"league"=>$league,
					"group_list" => $this->getmygroup($user)
			);		
						
		}
		echo json_encode ( $result );
		die ();
	}
	
	/**
	 * 自动登录
	 */
	public function actionAutologin(){
		$key = $this->check_key();
		$token = Frame::getStringFromRequest('token');
		$phone_model = Frame::getStringFromRequest('phone_model');
		if (empty ( $token )) {
			$result ['ret_num'] = 2004;
			$result ['ret_msg'] = 'token为空';
			echo json_encode ( $result );
			die ();
		}
		$user = Member::model ()->find("token = '{$token}'");
		if ($user ) {
			$status = array('0'=>'启用', '1'=>'禁用1周', '2'=>'禁用2周', '3'=>'禁用1个月', '4'=>'禁用3个月', '5'=>'无限期禁用');
			$timeArray = array(1=>7*24*60*60,2=>14*24*60*60,3=>30*24*60*60,4=>90*24*60*60,5=>'无限期');
			if($user->status != 0){
				$info = MemberDisable::model()->find("member_id = {$user->id} and status = {$user->status} order by created_time desc");
				if($info->status == 5){
					$opentime = "您的帐号被无限期禁用"; 
				}else{
					$opentime = "您的帐号被禁用,将于".(date("Y-m-d H:i:s",$info->created_time + $timeArray[$user->status]))."解禁";
				}
				$result ['ret_num'] = 2092;
				$result ['ret_msg'] = $opentime;
				echo json_encode ( $result );
				die ();
			}
			//查询直通车信息
			$zhitongche = "";
			$nt = NumberTrain::model()->find("member_id = {$user->id}");
			if($nt){
				$zhitongche = array(
						"Id"=>$nt->id ? $nt->id : "",
						"Name"=>$nt->name ? $nt->name : "",
						"ShortName"=>$nt->short_name ? $nt->short_name : ""
				);
			}
			//生成Token
			$token = md5($key.time()).md5($key.$user->id);
			$user->token = $token;
			$user->phone_model = $this->getmodel($phone_model);;
			$user->update();
			//写session
			$time = 7*24*3600;
			session_set_cookie_params($time);
			session_start();
			session_regenerate_id(true);
			Yii::app()->session['memberid']=$token;
			//写登录记录
			$member_login = new MemberLogin();
			$member_login->member_id = $user->id;
			$member_login->phone_model = $this->getmodel($phone_model);;
			$member_login->created_time = time();
			$member_login->save();
			//查询等级
			$level = 0;
			$appellation = "";
			$level_all = getlevel();
			foreach ($level_all as $va){
				if($user->integral <= $va[1]){
					$level = $va[0];
					$appellation = $va[2];
					break;
				}
			}
			//查询是否拥有好友联盟
			$haveleague = LeagueMember::model()->find("member_id = {$user->id} and type < 2 order by type");
			if($haveleague){
				if ($haveleague->type == 1) {
					$league = 1;
				}else{
					$league = 2;
				}	
			}else{
				$league = 0;
			}
			//省市
			$pro = array("province"=>$user->province,"city"=>$user->city);
			$pro_arr = $this->ProCity(array($pro));
			//解禁时间
			$opentime = array();
			$timeArray = array(1=>7*24*60*60,2=>14*24*60*60,3=>30*24*60*60,4=>90*24*60*60,5=>'无限期');
			$typeArray = array(1=>'creation_disable',2=>'buy_disable',3=>'enterprise_disable',4=>'group_disable',5=>'store_disable',6=>'league_disable');
			$servicetime = ServiceDisable::model()->findAll("member_id = {$user->id} order by created_time desc");
			if($servicetime){
				$inArray = array();
				foreach ($servicetime as $va){
					if(in_array($va->type, $inArray)) continue;
					if($va->status == 5){
						$opentime[$typeArray[$va->type]] = 1;
					}else{
						$opentime[$typeArray[$va->type]] = $timeArray[$va->status]+$va->created_time;
					}
					$inArray[] = $va->type;
				}
			}
			
			$result ['ret_num'] = 0;
			$result ['ret_msg'] = '操作成功';
			$result['user'] = array(
					"UserId"=>$user->id,
					"BenbenId"=>$user->benben_id,
					"name"=>$user->name,
					"UserNickname"=>$user->nick_name,
					"UserSex"=>$user->sex,
					"UserPoster"=>$user->poster ? URL.$user->poster : "",
					"UserQrcode"=>$user->qrcode ? URL.$user->qrcode : "",
					"Age"=>$user->age,
					"Phone"=>$user->phone,
					"Token"	=>$token,
					"UserInfo"	=>$user->userinfo,
					"integral"=>$user->integral,
					"level"=>$level,
					"appellation"=>$this->eraseNull($appellation),
					"huanxin_username"=>$this->eraseNull($user->huanxin_username),
					"huanxin_password"=>$user->huanxin_password,
					"huanxin_uuid"=>$user->huanxin_uuid,
					"creation_disable"=>$user->creation_disable ? $opentime['creation_disable']:$user->creation_disable,
					"buy_disable"=>$user->buy_disable ? $opentime['buy_disable']:$user->buy_disable,
					"enterprise_disable"=>$user->enterprise_disable ? $opentime['enterprise_disable']:$user->enterprise_disable,
					"group_disable"=>$user->group_disable ? $opentime['group_disable']:$user->group_disable,
					"store_disable"=>$user->store_disable ? $opentime['store_disable']:$user->store_disable,
					"league_disable"=>$user->league_disable ? $opentime['league_disable']:$user->league_disable,
					"Address"=>$user->address ? $user->address : "",
					"ProCity"=>$pro_arr[$user->province].' '.$pro_arr[$user->city],
					"ZhiTongChe"=>$zhitongche,
					"league"=>$league,
					"group_list" => $this->getmygroup($user)
			);
		}else{
			$result ['ret_num'] = 2002;
			$result ['ret_msg'] = 'token非法';
		}
		echo json_encode ( $result );
		die ();
	}
	
	/**
	 * 更新用户信息
	 */
	public function actionUpdate(){
		
		if ((Yii::app()->request->isPostRequest)) {
			$this->check_key();
			//$member_id = Frame::getIntFromRequest('member_id');
			$name = Frame::getStringFromRequest('name');
			$nick_name = Frame::getStringFromRequest('nick_name');			
			$age = Frame::getIntFromRequest('age');
			$sex = Frame::getIntFromRequest('sex');
			$province = Frame::getIntFromRequest('province');
			$city = Frame::getIntFromRequest('city');
			$area = Frame::getIntFromRequest('area');
			$street = Frame::getIntFromRequest('street');
			$address = Frame::getStringFromRequest('address');
			$user = $this->check_user();			
			
			if($name){
				$user->name = $name;
			}
			if($nick_name){
				$user->nick_name = $nick_name;
			}
			if($age){
				$user->age = $age;
			}
			if($sex){
				$user->sex = $sex;
			}
			if($province){
				$user->province = $province;
			}
			if($city){
				$user->city = $city;
			}
			if($area){
				$user->area = $area;
			}
			if($street){
				$user->street = $street;
			}else if($province){
				//如果是修改地区，街道可以为空
				$user->street = 0;
			}	
			if($address){
				$user->address = $address;
			}
			if($user->update()){
				$result ['ret_num'] = 0;
				$result ['ret_msg'] = '操作成功';
				$result['user'] = array(
						"UserId"=>$user->id,
						"name"=>$user->name,
						"UserNickname"=>$user->nick_name,
						"UserSex"=>$user->sex,
						"Age"=>$user->age,
						"Phone"=>$user->phone,
						"Token"	=>$user->token
				);
			}else{
				$result ['ret_num'] = 2003;
				$result ['ret_msg'] = '信息添加失败	';
			}						
			echo json_encode ( $result );
		}
	}

	/**
	 * 修改密码
	 */
	public function actionChangepwd(){
		if ((Yii::app()->request->isPostRequest)) {
			$this->check_key();
			//$member_id = Frame::getIntFromRequest('member_id');
			$pwd = Frame::getStringFromRequest('password');
			$oldpwd = Frame::getStringFromRequest('oldpassword');
			
			$token = Yii::app()->session['memberid'];
			if (empty ( $token )) {
				$result ['ret_num'] = 2001;
				$result ['ret_msg'] = '用户未登录';
				echo json_encode ( $result );
				die ();
			}
			if (empty ( $pwd ) || empty($oldpwd)) {
				$result ['ret_num'] = 2013;
				$result ['ret_msg'] = '密码不能为空';
				echo json_encode ( $result );
				die ();
			}
			$oldpwd = md5($oldpwd);
			$user = Member::model ()->find("token = '{$token}' and password = '{$oldpwd}'");
			if (empty ( $user )) {
				$result ['ret_num'] = 2000;
				$result ['ret_msg'] = '密码错误';
			}else{
				$user->password = md5($pwd);
				if($user->update()){
					$result ['ret_num'] = 0;
					$result ['ret_msg'] = '密码修改成功';
				}else{
					$result ['ret_num'] = 2014;
					$result ['ret_msg'] = '密码修改失败';
				}
			}
			echo json_encode ( $result );
		}
	}
	
	/**
	 * 忘记密码
	 */
	public function actionFogpwd(){
		if ((Yii::app()->request->isPostRequest)) {
			$this->check_key();			
			$phone = Frame::getStringFromRequest('phone');			
			$pwd = Frame::getStringFromRequest('password');
			$repwd = Frame::getStringFromRequest('repassword');
			$idcode = Frame::getIntFromRequest('code');
				
			if (empty ( $idcode ) || empty ( $pwd ) || empty ( $repwd ) || empty ( $phone )) {
				$result ['ret_num'] = 2009;
				$result ['ret_msg'] = '输入信息不完整';
				echo json_encode ( $result );
				die ();
			}
			if($pwd != $repwd){
				$result ['ret_num'] = 2109;
				$result ['ret_msg'] = '两次输入密码不一致';
				echo json_encode ( $result );
				die ();
			}
			$user = Member::model ()->find("phone = '{$phone}'");
			if (empty( $user )) {
				$result['ret_num'] = 2015;
				$result['ret_msg'] = '用户不存在';
				echo json_encode( $result );
				die ();
			}
			$status = array('0'=>'启用', '1'=>'禁用1周', '2'=>'禁用2周', '3'=>'禁用1个月', '4'=>'禁用3个月', '5'=>'无限期禁用');
			if($user->status != 0){
				$result ['ret_num'] = 2092;
				$result ['ret_msg'] = "您的帐号被".$status[$user->status];
				echo json_encode ( $result );
				die ();
			}	
			$criteria = new CDbCriteria() ;
			$criteria -> select = "idcode";
			$criteria -> condition = 'phone = :phone';
			$criteria -> order = 'send_time desc';
			$criteria ->params = array (':phone' => $phone) ;
			$code = Idcode::model ()->find($criteria);
			if ($code->idcode != $idcode) {
				$result ['ret_num'] = 2005;
				$result ['ret_msg'] = '验证码错误';
				echo json_encode ( $result );
				die ();
			}
						
			$user->password = md5($pwd);			
			if($user->update()){
				//注册成功后进行登录
				//查询直通车信息
				$zhitongche = "";
				$nt = NumberTrain::model()->find("member_id = {$user->id}");
				if($nt){
					$zhitongche = array(
							"Id"=>$nt->id ? $nt->id : "",
							"Name"=>$nt->name ? $nt->name : "",
							"ShortName"=>$nt->short_name ? $nt->short_name : ""
					);
				}
				//查询等级
				$level = 0;
				$appellation = "";
				$level_all = getlevel();
				foreach ($level_all as $va){
					if($user->integral <= $va[1]){
						$level = $va[0];
						$appellation = $va[2];
						break;
					}
				}
				//查询是否拥有好友联盟
				$haveleague = FriendLeague::model()->find("member_id = {$user->id}");
				if($haveleague){
					$league = 1;
				}else{
					$league = 0;
				}
				//生成Token
				$token = md5($key.time()).md5($key.$user->id);
				$user->token = $token;
				$user->update();
				//写session
				Yii::app()->session['memberid']=$token;
				//写登录记录
				$member_login = new MemberLogin();
				$member_login->member_id = $user->id;
				$member_login->phone_model = $this->getmodel($phone_model);;
				$member_login->created_time = time();
				$member_login->save();
				//省市
				$pro = array("province"=>$user->province,"city"=>$user->city);
				$pro_arr = $this->ProCity(array($pro));
				
				$result ['ret_num'] = 0;
				$result ['ret_msg'] = '操作成功';
				$result['user'] = array(
					"UserId"=>$user->id,
					"BenbenId"=>$user->benben_id,
					"name"=>$user->name,
					"UserNickname"=>$user->nick_name,
					"UserSex"=>$user->sex,
					"UserPoster"=>$user->poster ? URL.$user->poster : "",
					"UserQrcode"=>$user->qrcode ? URL.$user->qrcode : "",
					"Age"=>$user->age,
					"Phone"=>$user->phone,
					"Token"	=>$token,
					"UserInfo"=>$user->userinfo,
					"integral"=>$user->integral,
					"level"=>$level,
					"appellation"=>$this->eraseNull($appellation),
					"huanxin_username"=>$this->eraseNull($user->huanxin_username),
					"huanxin_password"=>$user->huanxin_password,
					"huanxin_uuid"=>$user->huanxin_uuid,
					"Address"=>$user->address ? $user->address : "",
					"ProCity"=>$pro_arr[$user->province].' '.$pro_arr[$user->city],
					"ZhiTongChe"=>$zhitongche,
					"league"=>$league,
					"group_list" => $this->getmygroup($user)
			);
			}else{
				$result ['ret_num'] = 2003;
				$result ['ret_msg'] = '重置密码失败	';
			}
			echo json_encode ( $result );
		}
	}
	
	/**
	 * 更新用户图像
	 */
	public function actionUpdateavatar(){
		if ((Yii::app()->request->isPostRequest)) {
			$this->check_key();
			//$member_id = Frame::getIntFromRequest('member_id');
			$poster = Frame::saveImage('poster');
			if(!$poster){
				$result['ret_num'] = 4100;
				$result['ret_msg'] = '图片没有上传';
				echo json_encode( $result );
				die();
			}
			$user = $this->check_user();
			
			$user->poster = $poster;
			if($user->update()){
				$result ['ret_num'] = 0;
				$result ['ret_msg'] = '操作成功';
				$result['poster'] = URL.$user->poster;
			}else{
				$result ['ret_num'] = 1999;
				$result ['ret_msg'] = '用户图像修改失败';
			}
			
			echo json_encode ( $result );
		}
	}
	
	/**
	 * 修改手机号码
	 */
	public function actionChangephone(){		
			$this->check_key();
			$phone = Frame::getStringFromRequest('phone');			
			$idcode = Frame::getIntFromRequest('code');
			if (empty ( $phone )) {
					$result ['ret_num'] = 2010;
					$result ['ret_msg'] = '请输入手机号';
					echo json_encode ( $result );
					die ();
				}
			$criteria = new CDbCriteria() ;
			$criteria -> select = "idcode";
			$criteria -> condition = 'phone = :phone';
			$criteria -> order = 'send_time desc';
			$criteria ->params = array (':phone' => $phone) ;
			$code = Idcode::model ()->find($criteria);
			if ($code->idcode != $idcode) {
				$result ['ret_num'] = 2005;
				$result ['ret_msg'] = '验证码错误';
				echo json_encode ( $result );
				die ();
			}
			$user = $this->check_user();
			//环信
// 			$options = array(
// 					"client_id"=>CLIENT_ID,
// 					"client_secret"=>CLIENT_SECRET,
// 					"org_name"=>ORG_NAME,
// 					"app_name"=>APP_NAME
// 			);
// 			$huanxin = new Easemob($options);
// 			$a = $huanxin->deleteUser($user->huanxin_username);//var_dump($a);
// 			$resulh = $huanxin->openRegister(md5($phone), $phone,$user->nick_name);//var_dump($b);
// 			$reh = json_decode($resulh, true);
// 			if($reh['error']){
// 				$err = new HuanxinError();
// 				$err->error_info = $reh['error'];
// 				$err->created_time = time();
// 				$err->save();
// 			}else{
// 				$user->huanxin_username = $reh['entities'][0]['username'];
// 				$user->huanxin_uuid = $reh['entities'][0]['uuid'];
// 				$user->update();
// 			}
			$re = Member::model()->find("phone ='{$phone}'");
			if($re){
				$result ['ret_num'] = 2007;
				$result ['ret_msg'] = '该手机号码已注册';
				echo json_encode ( $result );
				die ();
			}
			$oldPhone = $user->phone;
			$user->phone = $phone;
			if($user->update()){
				$connection = Yii::app()->db;
				//删除旧号码犇犇状态
				//$sql = "update group_contact_phone set is_benben=0 where phone ='{$oldPhone}'";
				$sql = "update group_contact_phone set phone ='{$phone}' where phone ='{$oldPhone}'";
				$command = $connection->createCommand($sql);
				$result1 = $command->execute();
				//修改通讯录group_contact_phone表is_benben
				$sql = "update group_contact_phone set is_benben={$user->benben_id} where phone='{$phone}'";
				$command = $connection->createCommand($sql);
				$result1 = $command->execute();
				//退出政企通讯录
				$sql = "select id,contact_id from enterprise_member where phone ='{$oldPhone}'";
				$command = $connection->createCommand($sql);
				$result1 = $command->queryAll();
				$eid = array();
				$emid = array();
				if($result1[0]){
					foreach ($result1 as $va){
						$eid[] = $va['contact_id'];
						$emid[] = $va['id'];
					}					
				}
				if($eid){
					$sql2 = "update enterprise set number = number - 1 where id in(".implode(",", $eid).")";
					$command = $connection->createCommand($sql2);
					$result1 = $command->execute();
				}
				if($emid){
					$sql2 = "delete from enterprise_member where id in(".implode(",", $emid).")";
					$command = $connection->createCommand($sql2);
					$result1 = $command->execute();
				}
				$sql3 = "delete from enterprise_display_member where member_id in(".implode(",", $emid).") and enterprise_id in(".implode(",", $eid).")";
								
				$result ['ret_num'] = 0;
				$result ['ret_msg'] = '操作成功';
				$result['phone'] = $phone;
			}else{
				$result ['ret_num'] = 999;
				$result ['ret_msg'] = '手机号码修改失败';
			}				
			echo json_encode ( $result );
	
	}
	
	/**
	 * 根据环信用户名查询用户资料
	 */
	public function actionHxmemberinfo(){
		$this->check_key();
		$hxname = Frame::getStringFromRequest('hxname');		
		$user = $this->check_user();
		if(!$hxname){
			$result['ret_num'] = 410;
			$result['ret_msg'] = '环信用户名为空';
			echo json_encode( $result );
			die();
		}

		$hxname = explode(",", $hxname);
		$hxn = "";
		foreach ($hxname as $val){
			$hxn .= "'".$val."',";
		}
		$hxn = trim($hxn);
		$hxn =trim($hxn,',');
		//查找好友
		$connection = Yii::app()->db;

		//通讯录里的犇犇好友
		$sqlf = "select a.phone,b.name from group_contact_phone a left join group_contact_info b on a.contact_info_id = b.id where b.member_id = {$user->id} and a.is_benben>0";
		$command = $connection->createCommand($sqlf);
		$fried_array = $command->queryAll();
		$farray = array();
		foreach ($fried_array as $key => $value) {
			$item_phone = $value['phone'];
			$item_name = $value['name'];
			if (empty($farray[$item_phone])) {
				//手机好重复，保留第一个名字
				$farray[$item_phone] = $item_name;
			}
		}
		$sql = "select id,name,nick_name,poster,phone,huanxin_username from member where huanxin_username in ({$hxn})";		
		$command = $connection->createCommand($sql);
		$result1 = $command->queryAll();

		foreach ($result1 as $key => $value){
			$is_friend = 0;
			$item_phone = $value['phone'];			
			if (!empty($farray[$item_phone])) {
				$is_friend = 1;
				$result1[$key]['name'] = $farray[$item_phone];
				$result1[$key]['nick_name'] = $farray[$item_phone];
			}

			$result1[$key]['is_friend'] =$is_friend;
			$result1[$key]['poster'] = $value['poster'] ? URL.$value['poster'] : "";
		}
		$result ['ret_num'] = 0;
		$result ['ret_msg'] = '操作成功';
		$result['user'] = $result1;
	
		echo json_encode ( $result );
	}
	
	/**
	 * 根据环信用户名查询该用户在我的通讯录信息
	 */
	public function actionHxcontactinfo(){
		$this->check_key();
		$user = $this->check_user();
		$hxname = Frame::getStringFromRequest('hxname');				
		if(!$hxname){
			$result['ret_num'] = 410;
			$result['ret_msg'] = '环信用户名为空';
			echo json_encode( $result );
			die();        
		}
		$userinfo = Member::model()->find("huanxin_username = '{$hxname}'");
		if(!$userinfo){
			$result['ret_num'] = 5206;
			$result['ret_msg'] = '环信用户名不存在';
			echo json_encode( $result );
			die();
		}
		
		$is_baixing = 0;
		$bxinfo = Bxapply::model()->find("member_id = {$userinfo->id}");
		if($bxinfo){
			$is_baixing = $bxinfo->short_phone;
		}
				
		$connection = Yii::app()->db;
		$sql0 = "select a.id,a.group_id,a.name,a.pinyin,a.created_time,b.poster,b.huanxin_username from group_contact_info a left join member b on a.benben_id = b.benben_id 
		where a.member_id = {$user->id} and a.benben_id = {$userinfo->benben_id}";
		$command = $connection->createCommand($sql0);
		$result1 = $command->queryAll();
		$phoneinfo = array();
		if($result1[0]['id']){
			$sql = "select a.*,b.poster,b.huanxin_username from group_contact_phone a left join member b on a.is_benben = b.benben_id
			where a.contact_info_id = {$result1[0]['id']}";
			$command = $connection->createCommand($sql);
			$phoneinfo = $command->queryAll();
	    }
		
		
		/*$sql = "select a.id,a.contact_info_id,a.phone,a.is_benben,a.is_baixing,b.group_id,b.name,b.pinyin,c.poster,c.huanxin_username 
		from group_contact_phone a inner join group_contact_info b on a.contact_info_id = b.id  
		inner join member c on a.phone = c.phone 
		where b.member_id = {$user->id} and c.huanxin_username = '{$hxname}' limit 1";		
		$command = $connection->createCommand($sql);
		$result1 = $command->queryAll();//var_dump($result1);exit();
		if($result1[0]){
			$sql = "select a.id,a.contact_info_id,a.phone,a.is_benben,a.is_baixing,c.poster,c.nick_name
			from group_contact_phone a left join member c on a.phone = c.phone
			where a.contact_info_id = '{$result1[0]['contact_info_id']}'";
			$command = $connection->createCommand($sql);
			$result2 = $command->queryAll();//var_dump($result2);exit();
			$additional_phone = array();
			foreach ($result2 as $value){
				$value['poster'] = $value['poster'] ? URL.$value['poster'] : "";
				$additional_phone[] = $value;
			}
		}*/
		
// 		$py = substr($result1[0]['pinyin'],0,1);
// 		$py = strtoupper($py);
// 		$reg = '/[A-Z]{1}/s';
// 		if(!(preg_match($reg, $py,$c) and $py==$c[0])){
// 			$py = "#";
// 		}
// 		 $result1[0]['pinyin'] = $py;
	     $result1[0]['is_benben'] = $userinfo->benben_id;
	     $result1[0]['is_baixing'] = 0;
		 $result1[0]['poster'] = $result1[0]['poster'] ? URL.$result1[0]['poster'] : "";
		 //$contact_info_id = $result1[0]['contact_info_id'];
		 //$result1[0]['contact_info_id'] = $result1[0]['id'];
		 //$result1[0]['id'] = $contact_info_id;
		 $result1[0]['huanxin_username'] = $hxname;
		 if($phoneinfo){
		 	foreach ($phoneinfo as $key=>$va){
		 		$phoneinfo[$key]['poster'] = $va['poster'] ? URL.$va['poster'] : "";
		 		$phoneinfo[$key]['huanxin_username'] = $va['huanxin_username'] ? $va['huanxin_username'] : "";
		 	}
		 }
		 
		 
		 $return_array = $result1[0];
		 $return_array['phone'] = $phoneinfo;
		 $return_array['is_friend'] = 1;
					
		$result ['ret_num'] = 0;
		$result ['ret_msg'] = '操作成功';
		$result['user'] = $return_array;
	
		echo json_encode ( $result );
	}
	
	/**
	 * 根据环信用户名查询该用户在我的通讯录信息(群组用)
	 */
	public function actionHxgcontactinfo(){
		$this->check_key();
		$user = $this->check_user();
		$hxname = Frame::getStringFromRequest('hxname');
		if(!$hxname){
			$result['ret_num'] = 410;
			$result['ret_msg'] = '环信用户名为空';
			echo json_encode( $result );
			die();
		}
		$userinfo = Member::model()->find("huanxin_username = '{$hxname}'");
		if(!$userinfo){
			$result['ret_num'] = 5206;
			$result['ret_msg'] = '环信用户名不存在';
			echo json_encode( $result );
			die();
		}
		$userid = $userinfo->id;
		$connection = Yii::app()->db;

		//查出好友在自己通讯录里的名字			
		$sql2 = "select a.phone,a.is_benben,a.is_baixing,a.contact_info_id,b.name from group_contact_phone a left join group_contact_info b on a.contact_info_id = b.id
		where b.member_id = {$user->id} and a.phone = {$userinfo->phone} and a.is_benben>0";
		//$sql2 = "select * from group_contact_info where member_id = {$user->id} and benben_id = {$userinfo->benben_id}";
		$command = $connection->createCommand($sql2);
		$res2 = $command->queryAll();
		$res3 = array();
		if ($res2 && count($res2)>0) {
			//在自己通讯录里
			$is_friend = 1;
			//查询该用户其他号码
			$sql3 = "select a.phone,a.is_benben,a.is_baixing,b.name from group_contact_phone a left join group_contact_info b on a.contact_info_id = b.id
			where b.member_id = {$user->id} and a.contact_info_id = {$res2[0]['contact_info_id']}";
			$command = $connection->createCommand($sql3);
			$res3 = $command->queryAll();
		}else {
			$is_friend = 0;
			$sql0 = "select id,name from group_contact_info where member_id = {$user->id} and benben_id = {$userinfo->benben_id}";
			$command = $connection->createCommand($sql0);
			$res2 = $command->queryAll();
			if($res2[0]){
				$is_friend = 1;
			}
			
		}
		$phone = array();
		if($res3[0]){
			foreach ($res3 as $v){
				$phone[] = array(
						"nick_name"=> $userinfo->nick_name,
						"benben_id"=> $userinfo->benben_id,
						"poster"=> $userinfo->poster ? URL.$userinfo->poster : "",
						"is_benben"=>$userinfo->benben_id,
						"is_baixing"=>$v['is_baixing'],
						"phone"=>$v['phone'],
						"huanxin_username"=>$userinfo->huanxin_username,
				);
			}			
		}

		$result ['ret_num'] = 0;
		$result ['ret_msg'] = '操作成功';
		$result['user'] = array(
				"name"=> $is_friend ? $res2[0]['name'] : $userinfo->nick_name,
				"nick_name"=> $userinfo->nick_name,
				"benben_id"=> $userinfo->benben_id,
				"group_id"=> "",
				"poster"=> $userinfo->poster ? URL.$userinfo->poster : "",
				"is_benben"=>$userinfo->benben_id,
				"is_baixing"=>$is_baixing,
				"huanxin_username"=>$userinfo->huanxin_username,
				"phone"=>$phone,
				"is_friend"=>$is_friend
		);
		echo json_encode ( $result );
		
		
		/*
		$sqlf = "select friend_id1,friend_id2 from friend_relate where ((friend_id1 = {$userid} and friend_id2 = {$user->id}) or (friend_id1 = {$user->id} and friend_id2 = {$userid})) and status = 1";
		$command = $connection->createCommand($sqlf);
		$friend = $command->queryAll();//var_dump($friend);exit();
			
		$sql = "select a.id,a.contact_info_id,a.phone,a.is_benben,a.is_baixing,b.group_id,b.name,b.pinyin,c.poster,c.huanxin_username
		from group_contact_phone a inner join group_contact_info b on a.contact_info_id = b.id
		inner join group_contact d on d.id = b.group_id inner join member c on a.phone = c.phone
		where d.member_id = {$user->id} and c.id = '{$userid}' limit 1";
		$command = $connection->createCommand($sql);
		$result1 = $command->queryAll();//var_dump($result1);exit();
	
		if( !$result1){
		$is_baixing = 0;
		$bxinfo = Bxapply::model()->find("member_id = {$userid}");
		if($bxinfo){
		$is_baixing = $bxinfo->id;
		}
		$phone[] = array(
		        "nick_name"=> $userinfo->nick_name,
				"benben_id"=> $userinfo->benben_id,
				"poster"=> $userinfo->poster ? URL.$userinfo->poster : "",
				"is_benben"=>$userinfo->benben_id,
			    "is_baixing"=>$is_baixing,
				"phone"=>$userinfo->phone,
				"huanxin_username"=>$userinfo->huanxin_username,
		);
		$result ['ret_num'] = 0;
		$result ['ret_msg'] = '操作成功';
		$result['user'] = array(
				"name"=> $userinfo->nick_name,
				"nick_name"=> $userinfo->nick_name,
				"benben_id"=> $userinfo->benben_id,
				"group_id"=> "",
				"poster"=> $userinfo->poster ? URL.$userinfo->poster : "",
				"is_benben"=>$userinfo->benben_id,
				"is_baixing"=>$is_baixing,
				"huanxin_username"=>$userinfo->huanxin_username,
				"phone"=>$phone,
				"is_friend"=>0
		);
		echo json_encode ( $result );
		exit();
		}
	
		$sql = "select a.id,a.contact_info_id,a.phone,a.is_benben,a.is_baixing,c.poster,c.nick_name 
		from group_contact_phone a left join member c on a.phone = c.phone 
		where a.contact_info_id = '{$result1[0]['contact_info_id']}'";
		$command = $connection->createCommand($sql);
		$result2 = $command->queryAll();//var_dump($result2);exit();
		// 				$py = substr($value['pinyin'],0,1);
		// 				$py = strtoupper($py);
		// 				$reg = '/[A-Z]{1}/s';
		// 				if(!(preg_match($reg, $py,$c) and $py==$c[0])){
		// 					$py = "#";
		// 				}
		// 				$result1[$key]['pinyin'] = $py;
		$result1[0]['poster'] = $result1[0]['poster'] ? URL.$result1[0]['poster'] : "";
		$additional_phone = array();
		foreach ($result2 as $value){
		$value['poster'] = $value['poster'] ? URL.$value['poster'] : "";
			$additional_phone[] = $value;
		}
		$return_array = $result1[0];
		$return_array['phone'] = $additional_phone;
		$return_array['is_friend'] = 1;
			
		// var_dump($return_array);exit();
	
		$result ['ret_num'] = 0;
				$result ['ret_msg'] = '操作成功';
		$result['user'] = $return_array;
	
		echo json_encode ( $result );
	
	*/
	}
	
	/**
	 * 根据二维码查询该用户在我的通讯录信息
	 */
	public function actionQrcontactinfo(){
		$this->check_key();
		$user = $this->check_user();
		$qr_name = Frame::getStringFromRequest('qr_name');	
		$userid =  base64_decode(substr($qr_name,16));
		$userinfo = Member::model()->find("id = '{$userid}'");
		$user = $this->check_user();
		$connection = Yii::app()->db;
		if (!$userinfo) {
			$result ['ret_num'] = 5204;
			$result ['ret_msg'] = '用户不存在';
			echo json_encode( $result );
			die();
		}
		if(substr(md5($userinfo->phone),0,16) != substr($qr_name,0,16)){
			$result ['ret_num'] = 5205;
			$result ['ret_msg'] = '二维码信息错误';
			echo json_encode( $result );
			die();
		}

		//查出好友在自己通讯录里的名字			
		$sql2 = "select a.phone,a.is_benben,a.is_baixing,a.contact_info_id,b.name from group_contact_phone a left join group_contact_info b on a.contact_info_id = b.id
		where b.member_id = {$user->id} and a.phone = {$userinfo->phone} and a.is_benben>0";
		$command = $connection->createCommand($sql2);
		$res2 = $command->queryAll();
		$res3 = array();
		if ($res2 && count($res2)>0) {
			//在自己通讯录里
			$is_friend = 1;
			//查询该用户其他号码
			$sql3 = "select a.phone,a.is_benben,a.is_baixing,b.name from group_contact_phone a left join group_contact_info b on a.contact_info_id = b.id
			where b.member_id = {$user->id} and a.contact_info_id = {$res2[0]['contact_info_id']}";
			$command = $connection->createCommand($sql3);
			$res3 = $command->queryAll();
		}else {
			$is_friend = 0;
			$sql0 = "select id,name from group_contact_info where member_id = {$user->id} and benben_id = {$userinfo->benben_id}";
			$command = $connection->createCommand($sql0);
			$res2 = $command->queryAll();
			if($res2[0]){
				$is_friend = 1;
			}
		}

		if($res3[0]){
			foreach ($res3 as $v){
				$phone[] = array(
						"nick_name"=> $userinfo->nick_name,
						"benben_id"=> $userinfo->benben_id,
						"poster"=> $userinfo->poster ? URL.$userinfo->poster : "",
						"is_benben"=>$userinfo->benben_id,
						"is_baixing"=>$v['is_baixing'],
						"phone"=>$v['phone'],
						"huanxin_username"=>$userinfo->huanxin_username,
				);
			}			
		}

		$result ['ret_num'] = 0;
		$result ['ret_msg'] = '操作成功';
		$result['user'] = array(
				"name"=> $is_friend ? $res2[0]['name'] : $userinfo->nick_name,
				"nick_name"=> $userinfo->nick_name,
				"benben_id"=> $userinfo->benben_id,
				"group_id"=> "",
				"poster"=> $userinfo->poster ? URL.$userinfo->poster : "",
				"is_benben"=>$userinfo->benben_id,
				"is_baixing"=>$is_baixing,
				"huanxin_username"=>$userinfo->huanxin_username,
				"phone"=>$phone,
				"is_friend"=>$is_friend
		);
		echo json_encode ( $result );


	 //    $sqlf = "select count(*) as num from friend_relate where ((friend_id1 = {$userid} and friend_id2 = {$user->id}) or (friend_id1 = {$user->id} and friend_id2 = {$userid})) and status = 1";
		// $command = $connection->createCommand($sqlf);
		// $friend_result = $command->queryAll();//var_dump($friend);exit();
		// if($friend_result[0]['num']>0){
		// 	$is_friend = 1;
		// }else {
		// 	$is_friend = 0;
		// }
		
		// $is_baixing = 0;
		// $bxinfo = Bxapply::model()->find("member_id = {$userid}");
		// if($bxinfo){
		// 	$is_baixing = $bxinfo->id;
		// }
		// $phone[] = array(
		// 		"nick_name"=> $userinfo->nick_name,
		// 		"benben_id"=> $userinfo->benben_id,
		// 		"poster"=> $userinfo->poster ? URL.$userinfo->poster : "",
		// 		"is_benben"=>$userinfo->benben_id,
		// 		"is_baixing"=>$is_baixing,
		// 		"phone"=>$userinfo->phone,
		// 		"huanxin_username"=>$userinfo->huanxin_username,
		// );
		// $result ['ret_num'] = 0;
		// $result ['ret_msg'] = '操作成功';
		// $result['user'] = array(
		// 		"name"=> $userinfo->nick_name,
		// 		"nick_name"=> $userinfo->nick_name,
		// 		"benben_id"=> $userinfo->benben_id,
		// 		"group_id"=> "",
		// 		"poster"=> $userinfo->poster ? URL.$userinfo->poster : "",
		// 		"is_benben"=>$userinfo->benben_id,
		// 		"is_baixing"=>$is_baixing,
		// 		"huanxin_username"=>$userinfo->huanxin_username,
		// 		"phone"=>$phone,
		// 		"is_friend"=>$is_friend
		// );
		// echo json_encode ( $result );




		/*
		$sql = "select a.id,a.contact_info_id,a.phone,a.is_benben,a.is_baixing,b.group_id,b.name,b.pinyin,c.poster,c.huanxin_username 
		from group_contact_phone a inner join group_contact_info b on a.contact_info_id = b.id 
		inner join group_contact d on d.id = b.group_id inner join member c on a.phone = c.phone 
		where d.member_id = {$user->id} and c.id = '{$userid}' limit 1";
		$command = $connection->createCommand($sql);
		$result1 = $command->queryAll();//var_dump($result1);exit();
		
		if(!$friend || !$result1){
			$is_baixing = 0;
			$bxinfo = Bxapply::model()->find("member_id = {$userid}");
			if($bxinfo){
				$is_baixing = $bxinfo->id;
			}
			$phone[] = array(
					"nick_name"=> $userinfo->nick_name,
					"benben_id"=> $userinfo->benben_id,
					"poster"=> $userinfo->poster ? URL.$userinfo->poster : "",
					"is_benben"=>$userinfo->benben_id,
					"is_baixing"=>$is_baixing,
					"phone"=>$userinfo->phone,
					"huanxin_username"=>$userinfo->huanxin_username,
			);
			$result ['ret_num'] = 0;
			$result ['ret_msg'] = '操作成功';
			$result['user'] = array(
					"name"=> $userinfo->nick_name,
					"nick_name"=> $userinfo->nick_name,
					"benben_id"=> $userinfo->benben_id,
					"group_id"=> "",
					"poster"=> $userinfo->poster ? URL.$userinfo->poster : "",
					"is_benben"=>$userinfo->benben_id,
					"is_baixing"=>$is_baixing,
					"huanxin_username"=>$userinfo->huanxin_username,
					"phone"=>$phone,
					"is_friend"=>0
			);
			echo json_encode ( $result );
			exit();
		}
		
		$sql = "select a.id,a.contact_info_id,a.phone,a.is_benben,a.is_baixing,c.poster,c.nick_name from group_contact_phone a left join member c on a.phone = c.phone where a.contact_info_id = '{$result1[0]['contact_info_id']}'";
				$command = $connection->createCommand($sql);
				$result2 = $command->queryAll();//var_dump($result2);exit();
// 				$py = substr($value['pinyin'],0,1);
// 				$py = strtoupper($py);
// 				$reg = '/[A-Z]{1}/s';
// 				if(!(preg_match($reg, $py,$c) and $py==$c[0])){
// 					$py = "#";
// 				}
// 				$result1[$key]['pinyin'] = $py;
		$result1[0]['poster'] = $result1[0]['poster'] ? URL.$result1[0]['poster'] : "";
		$additional_phone = array();
				foreach ($result2 as $value){
				$value['poster'] = $value['poster'] ? URL.$value['poster'] : "";
		 		$additional_phone[] = $value;
				}
				$return_array = $result1[0];
				$return_array['phone'] = $additional_phone;
				$return_array['is_friend'] = 1;
					
				// var_dump($return_array);exit();
		
				$result ['ret_num'] = 0;
				$result ['ret_msg'] = '操作成功';
				$result['user'] = $return_array;
		
				echo json_encode ( $result );
	*/
	
	}
	
	/**
	 * 搜索会员
	 */
	public function actionSearch(){
		$this->check_key();
		$keyword = Frame::getStringFromRequest('keyword');
		$last_time = Frame::getIntFromRequest('last_time');
		$user = $this->check_user();
		if(!$keyword){
			$result['ret_num'] = 420;
			$result['ret_msg'] = '犇犇号或昵称为空';
			echo json_encode( $result );
			die();
		}
		//查找好友
		$connection = Yii::app()->db;
// 		$sqlf = "select b.is_benben from group_contact_info a left join group_contact_phone b on a.id = b.contact_info_id where b.is_benben > 0 and a.member_id = ".$user->id;
		$sqlf = "select benben_id from group_contact_info where member_id = ".$user->id;
		$command = $connection->createCommand($sqlf);
		$friend = $command->queryAll();
		$memberid = "";
		$fri = array();
		foreach ($friend as $v){
			$fri[]= $v['benben_id'];
		}
		$fri = array_flip(array_flip($fri));		
		$sql = "select id,nick_name,poster,huanxin_username,created_time, benben_id from member where ((benben_id = '{$keyword}') or (nick_name like '%{$keyword}%')) ";	
		if($last_time){
			$sql = "select id,nick_name,poster,huanxin_username,created_time, benben_id from member where ((benben_id = '{$keyword}') or (nick_name like '%{$keyword}%')) and created_time<{$last_time} ";
		}
		$sql .= "and id <> {$user->id} order by created_time desc";
		$command = $connection->createCommand($sql);
		$result1 = $command->queryAll();
		$re = array();
		foreach ($result1 as $key => $value){
			//$is_friend = 0;
			if(in_array($value['benben_id'], $fri)){
				//$is_friend = 1;
				continue;
			}
			$re[] = $value;
			//$result1[$key]['is_friend'] =$is_friend;
			//$result1[$key]['poster'] = $value['poster'] ? URL.$value['poster'] : "";
		}
		foreach ($re as $key => $value){
			$re[$key]['poster'] = $value['poster'] ? URL.$value['poster'] : "";
		}
		$result ['ret_num'] = 0;
		$result ['ret_msg'] = '操作成功';
		$result['user'] = $re;
	
		echo json_encode ( $result );
	}
	
	/**
	 * 查询用户资料
	 */
	public function actionMemberinfo(){
		$this->check_key();
		//$member_id = Frame::getIntFromRequest('member_id');
		$user = $this->check_user();
		$pinfo = $this->pcinfo();
		$pro_city = "";
		if($user->province && $user->city){
			$pro_city = $pinfo[0][$user->province]." ".$pinfo[1][$user->city];
		}
		$connection = Yii::app()->db;
		$sql = "select short_phone from bxapply a inner join member b on a.phone = b.phone where b.phone = '{$user->phone}' and a.status=3";
		$command = $connection->createCommand($sql);
		$baixing = $command->queryAll();
		//省市
		$pro = array("province"=>$user->province,"city"=>$user->city,"area"=>$user->area);
		$pro_arr = $this->ProCity(array($pro));
		
		$result ['ret_num'] = 0;
		$result ['ret_msg'] = '操作成功';
		$result['user'] = array(
				"UserId"=>$user->id,
				"BenbenId"=>$user->benben_id,
				"UserNickname"=>$user->nick_name ? $user->nick_name : "",
				"Name"=>$user->name ? $user->name : "",
				"Poster"=>$user->poster ? URL.$user->poster : "",
				"UserSex"=>$user->sex ? $user->sex : "",
				"Age"=>$user->age ? $user->age : "",
				"Phone"=>$user->phone ? $user->phone : "",
				"Cornet"=>$user->cornet ? $user->cornet : "",
				"Integral"=>$user->integral ? $user->integral : "",
				"Coin"=>$user->coin ? $user->coin : "",
				"Address"=>$user->address ? $user->address : "",
				"BaiXing"=>$baixing[0]['short_phone'] ? $baixing[0]['short_phone'] : "",
				"ProCity"=>$pro_arr[$user->province].' '.$pro_arr[$user->city].' '.$pro_arr[$user->area],
				"QrCode"=>$user->qrcode ? URL.$user->qrcode : "",
				"Token"	=>$user->token
		);
		
		echo json_encode ( $result );
	}

	/**
	 * 添加好友
	 */
	public function actionAddfriend(){
		$this->check_key();      		
		$huanxin_username = Frame::getStringFromRequest('huanxin_username');		
		if (empty ( $huanxin_username )) {
			$result ['ret_num'] = 5080;
			$result ['ret_msg'] = '犇犇ID为空';
			echo json_encode( $result );
			die ();
		}
		$user = $this->check_user();
		$add_user = Member::model()->find("huanxin_username = '{$huanxin_username}'");
		$own_bx = Bxapply::model()->find("phone = '{$user->phone}' and status = 3");
		$add_bx = Bxapply::model()->find("phone = '{$add_user->phone}' and status = 3");
		$ownbxid = 0;
		$addbxid = 0;
		if($own_bx){
			$ownbxid = $own_bx->short_phone;
		}
		if($add_bx){
			$addbxid = $add_bx->short_phone;
		}
		if (empty ( $add_user )) {
			$result ['ret_num'] = 5081;
			$result ['ret_msg'] = '待添加用户不存在';
			echo json_encode( $result );
			die ();
		}
		//查未分组ID
		$own = GroupContact::model()->find("group_name = '未分组' and member_id = {$user->id}");
		$friend = GroupContact::model()->find("group_name = '未分组' and member_id = {$add_user->id}");
		$connection = Yii::app()->db;
		$PinYin = new PYInitials('utf8');//var_dump($add_user->id);
		$t = time();//echo "未分组ID";var_dump($own->id);var_dump($friend->id);exit;
				
		//查询待添加号码是否在自己的通讯录
// 		$sql = "select phone from group_contact_phone a inner join group_contact_info b on a.contact_info_id = b.id 
// 		where b.group_id in (select id from group_contact where member_id = {$user->id}) and a.phone = '{$add_user->phone}'";
		$sql = "select count(*) as num from (
						select c.contact_info_id,c.phone from 
						(select a.contact_info_id,a.phone from benben.group_contact_phone a 
						left join benben.group_contact_info b 
						on a.contact_info_id=b.id
						where b.member_id={$user->id} and
						is_benben>0 order by a.id asc
						) c 
						group by c.contact_info_id
						) tb where tb.phone={$add_user->phone}";
		$command = $connection->createCommand($sql);
		$resulta = $command->queryAll();//var_dump($resulta);exit();
		$sql = "select id from group_contact_info where member_id = {$user->id} and benben_id = {$add_user->benben_id}";
		$command = $connection->createCommand($sql);
		$resulta_a = $command->queryAll();//var_dump($resulta_a);exit();
		
		//查询我的号码是否在他的通讯录
// 		$sql = "select phone from group_contact_phone a inner join group_contact_info b on a.contact_info_id = b.id 
// 		where b.group_id in (select id from group_contact where member_id = {$add_user->id}) and a.phone = '{$user->phone}'";
		$sql = "select count(*) as num from (
						select c.contact_info_id,c.phone from
						(select a.contact_info_id,a.phone from benben.group_contact_phone a
						left join benben.group_contact_info b
						on a.contact_info_id=b.id
						where b.member_id={$add_user->id} and
						is_benben>0 order by a.id asc
						) c
						group by c.contact_info_id
						) tb where tb.phone={$user->phone}";
		$command = $connection->createCommand($sql);
		$resulta1 = $command->queryAll();//var_dump($resulta1);exit();
		$sql = "select id from group_contact_info where member_id = {$add_user->id} and benben_id = {$user->benben_id}";
		$command = $connection->createCommand($sql);
		$resulta1_a = $command->queryAll();//var_dump($resulta1_a);exit();
		//array(2) { [0]=> array(1) { ["phone"]=> string(11) "13916958641" } 
		//[1]=> array(1) { ["phone"]=> string(11) "13916958641" } } 
		//if($resulta[0] && $resulta1[0]){
		if(($resulta[0]['num'] || $resulta_a[0]['id']) && ($resulta1[0]['num'] || $resulta1_a[0]['id'])){	
			$result ['ret_num'] = 5208;
			$result ['ret_msg'] = '已经是好友';
			echo json_encode( $result );
			die ();
		}
		
		$flag = 0;
		if(!$resulta[0]['num'] && !$resulta_a[0]['id']){
			//添加姓名
			$own_result = new GroupContactInfo();
			$own_result->group_id = $own->id;
			$own_result->name = $add_user->nick_name;
			$own_result->pinyin = $PinYin->getInitials($add_user->nick_name);
			$own_result->member_id = $user->id;
			$own_result->benben_id = $add_user->benben_id;
			$own_result->created_time = $t;
			$own_result->save();
			//添加号码
			/* $own_phone = "({$own_result->id},'{$add_user->phone}',{$add_user->benben_id},{$addbxid})";
			$sql = "insert into group_contact_phone (contact_info_id,phone,is_benben,is_baixing) values {$own_phone}";
			$command = $connection->createCommand($sql);
			$result1 = $command->execute();//var_dump($sql); */
			//返回加后的记录			
			$friend_info = array(
					"id"=>$own_result->id,//group_contact_info表的ID
					"group_id"=>$own->id,//分组ID
					"name"=>$own_result->name,
					"pinyin"=>$own_result->pinyin,
					"created_time"=>$own_result->created_time,
					"is_benben"=>$add_user->benben_id,
					"is_baixing"=>$addbxid,
					"poster"=>$add_user->poster ? URL.$add_user->poster : "",
					"huanxin_username"=>$add_user->huanxin_username,
					"phone"=>array()/*array(
							"phone"=>$add_user->phone,
							"is_benben"=>$add_user->benben_id,
							"is_baixing"=> $addbxid,
							"poster"=>$add_user->poster ? URL.$add_user->poster : "",
							"nick_name"=>$add_user->nick_name
					)*/
			);
		}else{
			$flag = 1;
			//$friend_info = array();
		}
		
		if(!$resulta1[0]['num'] && !$resulta1_a[0]['id']){
			//添加姓名
			$friend_result = new GroupContactInfo();
			$friend_result->group_id = $friend->id;
			$friend_result->name = $user->nick_name;
			$friend_result->pinyin = $PinYin->getInitials($user->nick_name);
			$friend_result->member_id = $add_user->id;
			$friend_result->benben_id = $user->benben_id;
			$friend_result->created_time = $t;
			$friend_result->save();
			//echo "信息";var_dump($own_result);var_dump($friend_result);
			//添加号码
			/* $friend_phone = "({$friend_result->id},'{$user->phone}',{$user->benben_id},{$ownbxid})";
			$sql = "insert into group_contact_phone (contact_info_id,phone,is_benben,is_baixing) values {$friend_phone}";
			$command = $connection->createCommand($sql);
			$result2 = $command->execute(); */
		}
			
		//if($result1){
		if(1){	
			//添加环信好友
// 			$options = array(
// 					"client_id"=>CLIENT_ID,
// 					"client_secret"=>CLIENT_SECRET,
// 					"org_name"=>ORG_NAME,
// 					"app_name"=>APP_NAME
// 			);
// 			$huanxin = new Easemob($options);
// 			$resulh = $huanxin->addFriend($user->huanxin_username, $huanxin_username);
// 			$reh = json_decode($resulh, true);
			if($flag){
				$result ['ret_num'] = 5218;
				$result ['ret_msg'] = '添加成功';
				echo json_encode( $result );
				die ();
			}
			$result ['ret_num'] = 0;
			$result ['ret_msg'] = '操作成功';
			$result['friend_info'] = $friend_info;
		}else{
			$result ['ret_num'] = 5082;
			$result ['ret_msg'] = '添加好友失败';
		}
		echo json_encode( $result );
	}
	
	public function actionDelete(){
		$id1 = Frame::getStringFromRequest("id");
		//$id = Member::model()->find("phone = '{$phone}'");
// 		$connection = Yii::app()->db;
// 		$sql = "delete from group_contact a
// 		inner join group_contact_info b on b.group_id = a.id inner join group_contact_phone c on c.contact_info_id = b.id		
// 		where a.member_id = {$id}";
			$ids = explode(",", $id1);
			foreach ($ids as $id){
				$re = array();
				$re = GroupContact::model()->findAll("member_id = {$id}");
					
				$i=0;
				foreach ($re as $va){
					$re1 = GroupContactInfo::model()->findAll("group_id = {$va->id}");
					foreach ($re1 as $val){
						$val->member_id = $id;
						$val->update();$i++;
						echo ">>>>>>".$i;
					}
				}
			}
					
	}
	
	/**
	 * 生成二维码
	 */
	public function actionQrcode(){
		$key = Frame::getStringFromRequest('key');
		$userid = Frame::getStringFromRequest('userid');
		$phone = Frame::getStringFromRequest('phone');
		$pathinfo = "index.php/v1/user/getqrcode/key/{$key}/qr_name/";
		$qrcodeinfo = URL."/".$pathinfo.substr(md5($phone),0,16).base64_encode($userid);
		$qrcodename = "uploads/images/qrcode/".substr(md5($phone),0,16).base64_encode($userid).".png";
		//$user->qrcode = $qrcodename;
		include('lib/phpqrcode/phpqrcode.php');
		QRcode::png($qrcodeinfo,$qrcodename);
		//$user->update();
		echo $qrcodename;
	}
	
	function checkUsername($username)
	{
		$rule  = "#[a-zA-Z0-9_\-./ ]*#";
		preg_match($rule, $username, $result);
		if (count($result) > 0) {
			return $result[0] == $username;
		}
		return false;
	}
	
	function openResiter($username, $password, $nickname){
		$option['client_id'] = 'YXA6hYUeUMCoEeSLzs9YqkHScQ';
		$option['client_secret'] = 'YXA6fC_v-if7CLg62Ti-kt9zqsOzdDo';
		$option['org_name'] = 'benben2015';
		$option['app_name'] = 'benben';
		$parameter['username'] = $username;
		$parameter['password'] = $password;
		$parameter['nickname'] = $nickname;
		$url = 'https://a1.easemob.com/' . $option['org_name'] . '/' . $option['app_name'] . '/users';
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
		// curl_setopt ( $curl, CURLOPT_HTTPHEADER, $header ); // 设置HTTP头
		curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, 1 ); // 获取的信息以文件流的形式返回
		curl_setopt ( $curl, CURLOPT_CUSTOMREQUEST, 'POST' );
		$result = curl_exec ( $curl ); // 执行操作
	
		curl_close ( $curl ); // 关闭CURL会话
		return $result;
	}
	
	function getmygroup($user){
		$group_id = GroupMember::model()->findAll("member_id = {$user->id}");
		if(empty($group_id)){			
			return array();			
		}
		$gid = "";
		foreach ($group_id as $val){
			$gid .= $val->contact_id.",";
		}
		$gid = trim($gid);
		$gid =trim($gid,',');
		if($gid){
			$sql = "select id,poster,name,description,bulletin,member_id,number,status,created_time,level,huanxin_groupid from groups where id in ({$gid})  and status = 0";
			$connection = Yii::app()->db;
			$command = $connection->createCommand($sql);
			$result1 = $command->queryAll();
			foreach ($result1 as $key => $ginfo){
				$result1[$key]['poster'] = $ginfo['poster'] ? URL.$ginfo['poster']:"";
				$result1[$key]['description'] = $ginfo['description'] ? $ginfo['description']:"";
				$result1[$key]['bulletin'] = $ginfo['bulletin'] ? $ginfo['bulletin']:"";
			}							
			
			return  $result1;
		}
	}

	/*接收用户邀请加入犇犇*/
	public function actionInviteLog()
	{
		$key = Frame::getStringFromRequest('key');
		$phone = Frame::getStringFromRequest('phone');
		$user = $this->check_user();
		$phoneArray = explode(",", $phone);
		if (!$phone || count($phoneArray) == 0) {
			$result ['ret_num'] = 5322;
			$result ['ret_msg'] =  '没有手机号';
		}else{
			$sql = "select phone from benben_invite_log where member_id = ".$user->id." and phone in (".implode(",", $phoneArray).")";
			$connection = Yii::app()->db;
			$command = $connection->createCommand($sql);
			$result1 = $command->queryAll();
			if (count($result1)) {
				foreach ($result1 as $key => $value) {
					$searchIndex = array_search($value['phone'], $phoneArray) ;
					if ($searchIndex >= 0) {
						unset($phoneArray[$searchIndex]);
					}
				}
			}
			if (count($phoneArray) > 0) {
				$insertArray = array();
				foreach ($phoneArray as $key => $value) {
					$insertArray[] = "(".$user->id.", ".$value.", ".time().")";
				}

				$sql = "insert into benben_invite_log(member_id, phone, created_time) values ".implode(",", $insertArray);
				$command = $connection->createCommand($sql);
				$result1 = $command->execute();
			}
			$result ['ret_num'] = 0;
			$result ['ret_msg'] =  '操作成功';

		}
		echo json_encode( $result );	

	}

	/**
	用户拨号
	*/
	public function actionMemberDialog()
	{
		$key = Frame::getStringFromRequest('key');
		$phone = Frame::getStringFromRequest('phone');
		$user = $this->check_user();
		$this->addIntegral($user->id, 14);
		$result ['ret_num'] = 0;
		$result ['ret_msg'] =  '操作成功';
		echo json_encode( $result );
	}	
}