<?php

class UserController extends PublicController
{
    public $layout = false;

    /**
     * 发送手机验证码
     */
    public function actionSendcode()
    {
        $this->check_key();
        $phone = Frame::getStringFromRequest('phone');
        $is_reset = Frame::getStringFromRequest('is_reset');
        if (empty ($phone) || strlen($phone) != 11) {
            $result ['ret_num'] = 2010;
            $result ['ret_msg'] = '请输入手机号';
            echo json_encode($result);
            die ();
        }
        $code_hava = Yii::app()->session['code_time'];//Idcode::model()->find("phone = {$phone} order by send_time desc");
        if ($code_hava) {
            $code_info = explode("|", $code_hava);//结构：send_time|is_reset
            if (isset($code_info[0]) && isset($code_info[1]) && (time() - $code_info[0] < 60) && ($code_info[1] == $is_reset)) {
                $result ['ret_num'] = 2028;
                $result ['ret_msg'] = '请求过于频繁,请稍候再试';
                echo json_encode($result);
                die ();
            }
        }

        //查看手机号码是否已注册
        $user = Member::model()->find("phone = '{$phone}' and id_enable = 1");

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

        if (!$user && $is_reset == 1) {
            $result ['ret_num'] = 2007;
            $result ['ret_msg'] = '该手机号码未注册';
            echo json_encode($result);
            exit();
        }


        if ((empty($user) && empty($is_reset)) || ($user && $is_reset == 1) || (empty($user) && $is_reset == 2)) {
            //返回验证码
            $codecontent = mt_rand(10000, 99999);
            //$content = "您的奔犇注册验证码为:".$codecontent."。请在页面填写验证码完成验证。如非本人操作，忽略本条短信。";
            $tempId = TEMPID;//注册
            if ($is_reset == 1) {
                //$content = "您的奔犇找回密码验证码为:".$codecontent."。请在页面填写验证码完成验证。如非本人操作，忽略本条短信。";
                $tempId = TEMPIDP;//找回密码
            }
            if ($is_reset == 2) {
                //$content = "您的奔犇更换绑定验证码为:".$codecontent."。请在页面填写验证码完成验证。如非本人操作，忽略本条短信。";
                $tempId = TEMPIDC;//更换绑定
            }

            $idcode = Frame::sendsns($phone, $codecontent, $tempId);
            if ($idcode->statusCode != 0) {
                $result ['ret_num'] = 2008;
                $result ['ret_msg'] = '获取验证码失败';
            } else {
                $code = new Idcode();
                $code->phone = $phone;
                $code->idcode = $codecontent;
                $code->send_time = time();
                if ($code->save()) {
                    Yii::app()->session['code_time'] = $code->send_time . "|" . $is_reset;
                    $result ['ret_num'] = 0;
                    $result ['ret_msg'] = '验证码发送成功';
                    $result ['ret_code'] = $codecontent;
                }
            }

        } else {
            $result ['ret_num'] = 2007;
            $result ['ret_msg'] = '该手机号码已注册';
        }
        echo json_encode($result);
    }

    /**
     * 二维码信息
     */
    public function actionGetqrcode()
    {
        $qr_name = Frame::getStringFromRequest('qr_name');
        $userid = base64_decode(substr($qr_name, 16));
        //$user = $this->check_user();
        $userinfo = Member::model()->find("id = '{$userid}'");
        if (!$userinfo) {
            $result ['ret_num'] = 1;
            $result ['ret_msg'] = '用户不存在';
            $result['name'] = '';
            $result['benben_id'] = '';
            $result['poster'] = '';
            $result['sex'] = '';
            $result['add'] = '';
            $this->render("getqrcode", array(
                "qrcode" => $result
            ));
        }
        //if(substr(md5($userinfo->phone),0,16) == substr($qr_name,0,16)){
        if ($userinfo) {
            //$connection = Yii::app()->db;
            //$sqlf = "select friend_id1,friend_id2 from friend_relate where ((friend_id1 = {$userid} and friend_id2 = {$user->id}) or (friend_id1 = {$user->id} and friend_id2 = {$userid})) and status = 1";
            //$command = $connection->createCommand($sqlf);
            //$friend = $command->queryAll();
            $sex = array(0 => "未知", 1 => "男", 2 => "女");
            //省市
            $pro = array("province" => $userinfo->province, "city" => $userinfo->city, "area" => $userinfo->area, "street" => $userinfo->street);
            $pro_arr = $this->ProCity(array($pro));

            $result ['ret_num'] = 2;
            $result ['ret_msg'] = '用户存在';
            $result['name'] = $userinfo->nick_name;
            $result['benben_id'] = $userinfo->benben_id;
            $result['poster'] = $userinfo->poster;
            $result['sex'] = $sex[$userinfo->sex];
            $result['add'] = $pro_arr[$userinfo->province] . ' ' . $pro_arr[$userinfo->city] . ' ' . $pro_arr[$userinfo->area] . ' ' . $pro_arr[$userinfo->street];

            $this->render("getqrcode", array(
                "qrcode" => $result
            ));
        }

    }

    /**
     * 注册会员信息
     */
    public function actionRegister()
    {
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

            if (empty ($idcode) || empty ($nick_name) || empty ($sex) || empty ($pwd) || empty ($repwd) || empty ($phone)) {
                $result ['ret_num'] = 2009;
                $result ['ret_msg'] = '输入信息不完整';
                echo json_encode($result);
                die ();
            }
            if ($pwd != $repwd) {
                $result ['ret_num'] = 2210;
                $result ['ret_msg'] = '两次密码输入不一致';
                echo json_encode($result);
                die ();
            }

            $criteria = new CDbCriteria();
            $criteria->select = "idcode";
            $criteria->condition = 'phone = :phone';
            $criteria->order = 'send_time desc';
            $criteria->params = array(':phone' => $phone);
            $code = Idcode::model()->find($criteria);
            if ($code->idcode != $idcode) {
                $result ['ret_num'] = 2005;
                $result ['ret_msg'] = '验证码错误';
                echo json_encode($result);
                die ();
            }
            $re = Member::model()->find("phone = {$phone}");
            if ($re && ($re->id_enable == 1)) {
                $result ['ret_num'] = 2007;
                $result ['ret_msg'] = '该手机号码已注册';
                echo json_encode($result);
                die ();
            }
            if ($re && ($re->id_enable == 0)) {
                $user = $re;
            } else {
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

            $connection = Yii::app()->db;
            if ($user->save()) {
                //注册成功后进行登录
                //生成Token
                $token = md5($key . time()) . md5($key . $user->id);
                $user->token = $token;
                //生成犇犇号
                $sql = "select max(benben_id) maxid from member";

                $command = $connection->createCommand($sql);
                $result1 = $command->queryAll();
                if ($result1[0]['maxid']) {
                    $benben_id = $result1[0]['maxid'] + 1;
                    while (true) {
                        $check = $this->checkbenben($benben_id);
                        if ($check) {
                            break;
                        }
                        $benben_id++;
                    }

                    $user->benben_id = $benben_id;
                } else {
                    $user->benben_id = 20003;
                }
                $user->update();

                //注册环信用户
                $username = md5($benben_id. $user['created_time']);
                $password = $phone;
                $nickname = $nick_name;

                $resulh = $this->openResiter($username, $password, $nickname);
                $reh = json_decode($resulh, true);
                if ($reh['error']) {
                    if($reh['error']=="duplicate_unique_property_exists") {
                        $rename = $this->getAccount($username);
                        $user->huanxin_username = $rename['entities'][0]['username'];
                        $user->huanxin_uuid = $rename['entities'][0]['uuid'];
                        $user->huanxin_password = $phone;
                    }else {
                        //账户出错
                        $err = new HuanxinError();
                        $err->error_info = $reh['error'];
                        $err->created_time = time();
                        $err->member_id = $user->id;
                        $err->save();
                        $result ['ret_num'] = 2008;
                        $result ['ret_msg'] = $reh['error'];
                        echo json_encode($result);
                        die ();
                    }
                } else {
                    $user->huanxin_username = $reh['entities'][0]['username'];
                    $user->huanxin_uuid = $reh['entities'][0]['uuid'];
                    $user->huanxin_password = $phone;
                }


                //生成二维码
                $pathinfo = "index.php/v1/user/getqrcode/qr_name/";
                $qrcodeinfo = URL . "/" . $pathinfo . substr(md5($phone), 0, 16) . base64_encode($user->id);
                $qrcodename = "uploads/images/qrcode/" . substr(md5($phone), 0, 16) . base64_encode($user->id) . ".png";
                $user->qrcode = "/" . $qrcodename;
                include('lib/phpqrcode/phpqrcode.php');
                QRcode::png($qrcodeinfo, $qrcodename);
// 					$url = URL."/index.php/v1/user/qrcode/key/{$key}/userid/{$user->id}/phone/{$phone}";
// 					$url = "http://127.0.0.1:999/index.php/v1/user/qrcode/key/{$key}/userid/{$user->id}/phone/{$phone}";
// 					$file = file_get_contents($url);var_dump($file);
// 					$user->qrcode = $file;
                $user->update();

                //写session
                Yii::app()->session['memberid'] = $token;
                //写登录记录
                $member_login = new MemberLogin();
                $member_login->member_id = $user->id;
                $member_login->phone_model = $this->getmodel($phone_model);;
                $member_login->created_time = time();
                $member_login->save();
                //更新通讯录表group_contact_phone
                $sql = "update group_contact_phone set is_benben = {$user->benben_id} where phone = '{$phone}'";
                $command = $connection->createCommand($sql);
                $result0 = $command->execute();
                //更新group_contact_info
                //获取需要更新的用户
                $sql = "select a.id,a.member_id from group_contact_info a left join group_contact_phone b on a.id=b.contact_info_id where a.benben_id=0 and b.is_benben={$user->benben_id}";
                $command = $connection->createCommand($sql);
                $result = $command->queryAll();
                if ($result) {
                    $infoArray = array();
                    $memberArray=array();
                    $m = new Memcached();
                    $m->addServer('localhost', 11211);
                    foreach ($result as $value) {
                        $infoArray[] = $value['id'];
                        if(!in_array($value['member_id'],$memberArray)) {
                            $memberArray[] = $value['member_id'];
                        }
                    }
                    foreach($memberArray as $k=>$v){
                        $snapshot = $m->get("addrsversion:" . $v);
                        $m->set("addrsversion:" . $v,($snapshot+1));
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
                    "UserId" => $user->id,
                    "BenbenId" => $user->benben_id,
                    "name" => "",
                    "UserNickname" => $user->nick_name,
                    "UserSex" => $user->sex,
                    "UserPoster" => $user->poster ? URL . $user->poster : "",
                    "UserQrcode" => $user->qrcode ? URL . $user->qrcode : "",
                    "Age" => $user->age,
                    "Phone" => $user->phone,
                    "Token" => $token,
                    "UserInfo" => $user->userinfo,
                    "integral" => "",
                    "level" => "",
                    "appellation" => "",
                    "huanxin_username" => $this->eraseNull($user->huanxin_username),
                    "huanxin_password" => $phone,
                    "huanxin_uuid" => $user->huanxin_uuid,
                    "creation_disable" => $user->creation_disable,
                    "buy_disable" => $user->buy_disable,
                    "enterprise_disable" => $user->enterprise_disable,
                    "group_disable" => $user->group_disable,
                    "store_disable" => $user->store_disable,
                    "league_disable" => $user->league_disable,
                    "Address" => $user->address ? $user->address : "",
                    "ProCity" => "",
                    "ZhiTongChe" => "",
                    "group_list" => ""
                );
            } else {
                $result ['ret_num'] = 2003;
                $result ['ret_msg'] = '信息添加失败	';
            }
            echo json_encode($result);
        }
    }

    //修改二维码
    public function actionQr12()
    {
        include('lib/phpqrcode/phpqrcode.php');
        $re = Member::model()->findAll();
        foreach ($re as $v) {
            //生成二维码
            $phone = $v->phone;
            $pathinfo = "index.php/v1/user/getqrcode/qr_name/";
            $qrcodeinfo = URL . "/" . $pathinfo . substr(md5($phone), 0, 16) . base64_encode($v->id);
            $qrcodename = "uploads/images/qrcode/" . substr(md5($phone), 0, 16) . base64_encode($v->id) . ".png";
            $v->qrcode = "/" . $qrcodename;
            QRcode::png($qrcodeinfo, $qrcodename);
            $v->update();
        }
    }

    /**
     * 会员登录
     */
    public function actionLogin()
    {
        $key = $this->check_key();
        //手机号，密码

        $phone = Frame::getStringFromRequest('phone');
        $password = Frame::getStringFromRequest('password');
        $phone_model = Frame::getStringFromRequest('phone_model');
        if (empty ($phone)) {
            $result ['ret_num'] = 2010;
            $result ['ret_msg'] = '请输入手机号';
            echo json_encode($result);
            die ();
        }
        if (empty ($password)) {
            $result ['ret_num'] = 2011;
            $result ['ret_msg'] = '请输入登陆密码';
            echo json_encode($result);
            die ();
        }
        //$pinfo = $this->pcinfo();
        $pwd = md5($password);
        $user = Member::model()->find("(phone = '{$phone}' or benben_id = '{$phone}') and password = '{$pwd}'");
        if (empty ($user)) {
            $result ['ret_num'] = 2012;
            $result ['ret_msg'] = '手机号码或密码错误';
        } else {
            $status = array('0' => '启用', '1' => '禁用1周', '2' => '禁用2周', '3' => '禁用1个月', '4' => '禁用3个月', '5' => '无限期禁用');
            $timeArray = array(1 => 7 * 24 * 60 * 60, 2 => 14 * 24 * 60 * 60, 3 => 30 * 24 * 60 * 60, 4 => 90 * 24 * 60 * 60, 5 => '无限期');
            if ($user->status != 0) {
                $info = MemberDisable::model()->find("member_id = {$user->id} and status = {$user->status} order by created_time desc");
                if ($info->status == 5) {
                    $opentime = "您的帐号被无限期禁用";
                } else {
                    $opentime = "您的帐号被禁用,将于" . (date("Y-m-d H:i:s", $info->created_time + $timeArray[$user->status])) . "解禁";
                }
                $result ['ret_num'] = 2092;
                $result ['ret_msg'] = $opentime;
                echo json_encode($result);
                die ();
            }
            //生成Token

            $token = md5($key . time()) . md5($key . $user->id);
            $user->token = $token;
            $user->phone_model = $this->getmodel($phone_model);;
            $user->update();
            //写session
            $time = 7 * 24 * 3600;
            session_set_cookie_params($time);
            session_start();
            session_regenerate_id(true);
            //Yii::app()->session['memberid']=$token;
            $_SESSION['memberid'] = $token;

            //写登录记录
            $member_login = new MemberLogin();
            $member_login->member_id = $user->id;
            $member_login->phone_model = $this->getmodel($phone_model);;
            $member_login->created_time = time();
            $member_login->save();
            //查询直通车信息
            $zhitongche = "";
            $nt = NumberTrain::model()->find("member_id = {$user->id}");
            if ($nt) {
                $zhitongche = array(
                    "Id" => $nt->id ? $nt->id : "",
                    "Name" => $nt->name ? $nt->name : "",
                    "ShortName" => $nt->short_name ? $nt->short_name : "",
                    "status" => $nt->status ? $nt->status : "",
                    "is_close" => $nt->is_close ? $nt->is_close : ""
                );
            }
            //查询等级
            $level = 0;
            $appellation = "";
            $level_all = getlevel();
            foreach ($level_all as $va) {
                if ($user->integral <= $va[1]) {
                    $level = $va[0];
                    $appellation = $va[2];
                    break;
                }
            }
            //查询是否拥有好友联盟
            $haveleague = LeagueMember::model()->find("member_id = {$user->id} and type < 2 order by type");
            if ($haveleague) {
                if ($haveleague->type == 1) {
                    $league = 1;
                } else {
                    $league = 2;
                }
            } else {
                $league = 0;
            }
            //省市
            $pro = array("province" => $user->province, "city" => $user->city);
            $pro_arr = $this->ProCity(array($pro));
            //解禁时间
            $opentime = array();
            $timeArray = array(1 => 7 * 24 * 60 * 60, 2 => 14 * 24 * 60 * 60, 3 => 30 * 24 * 60 * 60, 4 => 90 * 24 * 60 * 60, 5 => '无限期');
            $typeArray = array(1 => 'creation_disable', 2 => 'buy_disable', 3 => 'enterprise_disable', 4 => 'group_disable', 5 => 'store_disable', 6 => 'league_disable');
            $servicetime = ServiceDisable::model()->findAll("member_id = {$user->id} order by created_time desc");

            if ($servicetime) {
                $inArray = array();
                foreach ($servicetime as $va) {
                    if (in_array($va->type, $inArray)) continue;
                    if ($va->status == 5) {
                        $opentime[$typeArray[$va->type]] = 1;
                    } else {
                        $opentime[$typeArray[$va->type]] = $timeArray[$va->status] + $va->created_time;
                    }
                    $inArray[] = $va->type;
                }
            }

            $result ['ret_num'] = 0;
            $result ['ret_msg'] = '操作成功';
            $result['user'] = array(
                "UserId" => $user->id,
                "BenbenId" => $user->benben_id,
                "name" => $user->name,
                "UserNickname" => $user->nick_name,
                "UserSex" => $user->sex,
                "UserPoster" => $user->poster ? URL . $user->poster : "",
                "UserQrcode" => $user->qrcode ? URL . $user->qrcode : "",
                "Age" => $user->age,
                "Phone" => $user->phone,
                "Token" => $token,
                "UserInfo" => $user->userinfo,
                "integral" => $user->integral,
                "level" => $level,
                "appellation" => $this->eraseNull($appellation),
                "huanxin_username" => $this->eraseNull($user->huanxin_username),
                "huanxin_password" => $user->huanxin_password,
                "huanxin_uuid" => $user->huanxin_uuid,
                "creation_disable" => $user->creation_disable ? $opentime['creation_disable'] : $user->creation_disable,
                "buy_disable" => $user->buy_disable ? $opentime['buy_disable'] : $user->buy_disable,
                "enterprise_disable" => $user->enterprise_disable ? $opentime['enterprise_disable'] : $user->enterprise_disable,
                "group_disable" => $user->group_disable ? $opentime['group_disable'] : $user->group_disable,
                "store_disable" => $user->store_disable ? $opentime['store_disable'] : $user->store_disable,
                "league_disable" => $user->league_disable ? $opentime['league_disable'] : $user->league_disable,
                "Address" => $user->address ? $user->address : "",
                "ProCity" => $pro_arr[$user->province] . ' ' . $pro_arr[$user->city],
                "ZhiTongChe" => $zhitongche,
                "league" => $league,
                "group_list" => $this->getmygroup($user)
            );

        }
        echo json_encode($result);
        die ();
    }

    /**
     * 自动登录
     */
    public function actionAutologin()
    {
        $key = $this->check_key();
        $token = Frame::getStringFromRequest('token');
        $phone_model = Frame::getStringFromRequest('phone_model');
        if (empty ($token)) {
            $result ['ret_num'] = 2004;
            $result ['ret_msg'] = 'token为空';
            echo json_encode($result);
            die ();
        }
        $user = Member::model()->find("token = '{$token}'");
        if ($user) {
            $status = array('0' => '启用', '1' => '禁用1周', '2' => '禁用2周', '3' => '禁用1个月', '4' => '禁用3个月', '5' => '无限期禁用');
            $timeArray = array(1 => 7 * 24 * 60 * 60, 2 => 14 * 24 * 60 * 60, 3 => 30 * 24 * 60 * 60, 4 => 90 * 24 * 60 * 60, 5 => '无限期');
            if ($user->status != 0) {
                $info = MemberDisable::model()->find("member_id = {$user->id} and status = {$user->status} order by created_time desc");
                if ($info->status == 5) {
                    $opentime = "您的帐号被无限期禁用";
                } else {
                    $opentime = "您的帐号被禁用,将于" . (date("Y-m-d H:i:s", $info->created_time + $timeArray[$user->status])) . "解禁";
                }
                $result ['ret_num'] = 2092;
                $result ['ret_msg'] = $opentime;
                echo json_encode($result);
                die ();
            }
            //查询直通车信息
            $zhitongche = "";
            $nt = NumberTrain::model()->find("member_id = {$user->id}");
            if ($nt) {
                $zhitongche = array(
                    "Id" => $nt->id ? $nt->id : "",
                    "Name" => $nt->name ? $nt->name : "",
                    "ShortName" => $nt->short_name ? $nt->short_name : "",
                    "status" => $nt->status ? $nt->status : "",
                    "is_close" => $nt->is_close ? $nt->is_close : ""
                );
            }
            //生成Token
            $token = md5($key . time()) . md5($key . $user->id);
            $user->token = $token;
            $user->phone_model = $this->getmodel($phone_model);;
            $user->update();
            //写session
            $time = 7 * 24 * 3600;
            session_set_cookie_params($time);
            session_start();
            session_regenerate_id(true);
            Yii::app()->session['memberid'] = $token;
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
            foreach ($level_all as $va) {
                if ($user->integral <= $va[1]) {
                    $level = $va[0];
                    $appellation = $va[2];
                    break;
                }
            }
            //查询是否拥有好友联盟
            $haveleague = LeagueMember::model()->find("member_id = {$user->id} and type < 2 order by type");
            if ($haveleague) {
                if ($haveleague->type == 1) {
                    $league = 1;
                } else {
                    $league = 2;
                }
            } else {
                $league = 0;
            }
            //省市
            $pro = array("province" => $user->province, "city" => $user->city);
            $pro_arr = $this->ProCity(array($pro));
            //解禁时间
            $opentime = array();
            $timeArray = array(1 => 7 * 24 * 60 * 60, 2 => 14 * 24 * 60 * 60, 3 => 30 * 24 * 60 * 60, 4 => 90 * 24 * 60 * 60, 5 => '无限期');
            $typeArray = array(1 => 'creation_disable', 2 => 'buy_disable', 3 => 'enterprise_disable', 4 => 'group_disable', 5 => 'store_disable', 6 => 'league_disable');
            $servicetime = ServiceDisable::model()->findAll("member_id = {$user->id} order by created_time desc");
            if ($servicetime) {
                $inArray = array();
                foreach ($servicetime as $va) {
                    if (in_array($va->type, $inArray)) continue;
                    if ($va->status == 5) {
                        $opentime[$typeArray[$va->type]] = 1;
                    } else {
                        $opentime[$typeArray[$va->type]] = $timeArray[$va->status] + $va->created_time;
                    }
                    $inArray[] = $va->type;
                }
            }

            $result ['ret_num'] = 0;
            $result ['ret_msg'] = '操作成功';
            $result['user'] = array(
                "UserId" => $user->id,
                "BenbenId" => $user->benben_id,
                "name" => $user->name,
                "UserNickname" => $user->nick_name,
                "UserSex" => $user->sex,
                "UserPoster" => $user->poster ? URL . $user->poster : "",
                "UserQrcode" => $user->qrcode ? URL . $user->qrcode : "",
                "Age" => $user->age,
                "Phone" => $user->phone,
                "Token" => $token,
                "UserInfo" => $user->userinfo,
                "integral" => $user->integral,
                "level" => $level,
                "appellation" => $this->eraseNull($appellation),
                "huanxin_username" => $this->eraseNull($user->huanxin_username),
                "huanxin_password" => $user->huanxin_password,
                "huanxin_uuid" => $user->huanxin_uuid,
                "creation_disable" => $user->creation_disable ? $opentime['creation_disable'] : $user->creation_disable,
                "buy_disable" => $user->buy_disable ? $opentime['buy_disable'] : $user->buy_disable,
                "enterprise_disable" => $user->enterprise_disable ? $opentime['enterprise_disable'] : $user->enterprise_disable,
                "group_disable" => $user->group_disable ? $opentime['group_disable'] : $user->group_disable,
                "store_disable" => $user->store_disable ? $opentime['store_disable'] : $user->store_disable,
                "league_disable" => $user->league_disable ? $opentime['league_disable'] : $user->league_disable,
                "Address" => $user->address ? $user->address : "",
                "ProCity" => $pro_arr[$user->province] . ' ' . $pro_arr[$user->city],
                "ZhiTongChe" => $zhitongche,
                "league" => $league,
                "group_list" => $this->getmygroup($user)
            );
        } else {
            $result ['ret_num'] = 2002;
            $result ['ret_msg'] = 'token非法';
        }
        echo json_encode($result);
        die ();
    }

    /**
     * 更新用户信息
     */
    public function actionUpdate()
    {

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

            if ($name) {
                $user->name = $name;
            }
            if ($nick_name) {
                $user->nick_name = $nick_name;
            }
            if ($age) {
                $user->age = $age;
            }
            if ($sex) {
                $user->sex = $sex;
            }
            if ($province) {
                $user->province = $province;
            }
            if ($city) {
                $user->city = $city;
            }
            if ($area) {
                $user->area = $area;
            }
            if ($street) {
                $user->street = $street;
            } else if ($province) {
                //如果是修改地区，街道可以为空
                $user->street = 0;
            }
            if ($address) {
                $user->address = $address;
            }
            if ($user->update()) {
                $result ['ret_num'] = 0;
                $result ['ret_msg'] = '操作成功';
                $result['user'] = array(
                    "UserId" => $user->id,
                    "name" => $user->name,
                    "UserNickname" => $user->nick_name,
                    "UserSex" => $user->sex,
                    "Age" => $user->age,
                    "Phone" => $user->phone,
                    "Token" => $user->token
                );
            } else {
                $result ['ret_num'] = 2003;
                $result ['ret_msg'] = '信息添加失败	';
            }
            echo json_encode($result);
        }
    }

    /**
     * 修改密码
     */
    public function actionChangepwd()
    {
        if ((Yii::app()->request->isPostRequest)) {
            $this->check_key();
            //$member_id = Frame::getIntFromRequest('member_id');
            $pwd = Frame::getStringFromRequest('password');
            $oldpwd = Frame::getStringFromRequest('oldpassword');

            $token = Yii::app()->session['memberid'];
            if (empty ($token)) {
                $result ['ret_num'] = 2001;
                $result ['ret_msg'] = '用户未登录';
                echo json_encode($result);
                die ();
            }
            if (empty ($pwd) || empty($oldpwd)) {
                $result ['ret_num'] = 2013;
                $result ['ret_msg'] = '密码不能为空';
                echo json_encode($result);
                die ();
            }
            $oldpwd = md5($oldpwd);
            $user = Member::model()->find("token = '{$token}' and password = '{$oldpwd}'");
            if (empty ($user)) {
                $result ['ret_num'] = 2000;
                $result ['ret_msg'] = '密码错误';
            } else {
                $user->password = md5($pwd);
                if ($user->update()) {
                    $result ['ret_num'] = 0;
                    $result ['ret_msg'] = '密码修改成功';
                } else {
                    $result ['ret_num'] = 2014;
                    $result ['ret_msg'] = '密码修改失败';
                }
            }
            echo json_encode($result);
        }
    }

    /**
     * 忘记密码
     */
    public function actionFogpwd()
    {
        if ((Yii::app()->request->isPostRequest)) {
            $this->check_key();
            $phone = Frame::getStringFromRequest('phone');
            $pwd = Frame::getStringFromRequest('password');
            $repwd = Frame::getStringFromRequest('repassword');
            $idcode = Frame::getIntFromRequest('code');
            $key = Frame::getStringFromRequest('key');
            $phone_model = Frame::getStringFromRequest('phone_model');

            if (empty ($idcode) || empty ($pwd) || empty ($repwd) || empty ($phone)) {
                $result ['ret_num'] = 2009;
                $result ['ret_msg'] = '输入信息不完整';
                echo json_encode($result);
                die ();
            }
            if ($pwd != $repwd) {
                $result ['ret_num'] = 2109;
                $result ['ret_msg'] = '两次输入密码不一致';
                echo json_encode($result);
                die ();
            }
            $user = Member::model()->find("phone = '{$phone}'");
            if (empty($user)) {
                $result['ret_num'] = 2015;
                $result['ret_msg'] = '用户不存在';
                echo json_encode($result);
                die ();
            }
            $status = array('0' => '启用', '1' => '禁用1周', '2' => '禁用2周', '3' => '禁用1个月', '4' => '禁用3个月', '5' => '无限期禁用');
            if ($user->status != 0) {
                $result ['ret_num'] = 2092;
                $result ['ret_msg'] = "您的帐号被" . $status[$user->status];
                echo json_encode($result);
                die ();
            }
            $criteria = new CDbCriteria();
            $criteria->select = "idcode";
            $criteria->condition = 'phone = :phone';
            $criteria->order = 'send_time desc';
            $criteria->params = array(':phone' => $phone);
            $code = Idcode::model()->find($criteria);
            if ($code->idcode != $idcode) {
                $result ['ret_num'] = 2005;
                $result ['ret_msg'] = '验证码错误';
                echo json_encode($result);
                die ();
            }

            $user->password = md5($pwd);
            if ($user->update()) {
                //注册成功后进行登录
                //查询直通车信息
                $zhitongche = "";
                $nt = NumberTrain::model()->find("member_id = {$user->id}");
                if ($nt) {
                    $zhitongche = array(
                        "Id" => $nt->id ? $nt->id : "",
                        "Name" => $nt->name ? $nt->name : "",
                        "ShortName" => $nt->short_name ? $nt->short_name : "",
                        "status" => $nt->status ? $nt->status : "",
                        "is_close" => $nt->is_close ? $nt->is_close : ""
                    );
                }
                //查询等级
                $level = 0;
                $appellation = "";
                $level_all = getlevel();
                foreach ($level_all as $va) {
                    if ($user->integral <= $va[1]) {
                        $level = $va[0];
                        $appellation = $va[2];
                        break;
                    }
                }
                //查询是否拥有好友联盟
                $haveleague = FriendLeague::model()->find("member_id = {$user->id}");
                if ($haveleague) {
                    $league = 1;
                } else {
                    $league = 0;
                }
                //生成Token
                $token = md5($key . time()) . md5($key . $user->id);
                $user->token = $token;
                $user->update();
                //写session
                Yii::app()->session['memberid'] = $token;
                //写登录记录
                $member_login = new MemberLogin();
                $member_login->member_id = $user->id;
                if($phone_model) {
                    $member_login->phone_model = $this->getmodel($phone_model);
                }
                $member_login->created_time = time();
                $member_login->save();
                //省市
                $pro = array("province" => $user->province, "city" => $user->city);
                $pro_arr = $this->ProCity(array($pro));

                $result ['ret_num'] = 0;
                $result ['ret_msg'] = '操作成功';
                $result['user'] = array(
                    "UserId" => $user->id,
                    "BenbenId" => $user->benben_id,
                    "name" => $user->name,
                    "UserNickname" => $user->nick_name,
                    "UserSex" => $user->sex,
                    "UserPoster" => $user->poster ? URL . $user->poster : "",
                    "UserQrcode" => $user->qrcode ? URL . $user->qrcode : "",
                    "Age" => $user->age,
                    "Phone" => $user->phone,
                    "Token" => $token,
                    "UserInfo" => $user->userinfo,
                    "integral" => $user->integral,
                    "level" => $level,
                    "appellation" => $this->eraseNull($appellation),
                    "huanxin_username" => $this->eraseNull($user->huanxin_username),
                    "huanxin_password" => $user->huanxin_password,
                    "huanxin_uuid" => $user->huanxin_uuid,
                    "Address" => $user->address ? $user->address : "",
                    "ProCity" => $pro_arr[$user->province] . ' ' . $pro_arr[$user->city],
                    "ZhiTongChe" => $zhitongche,
                    "league" => $league,
                    "group_list" => $this->getmygroup($user)
                );
            } else {
                $result ['ret_num'] = 2003;
                $result ['ret_msg'] = '重置密码失败	';
            }
            echo json_encode($result);
        }
    }

    /**
     * 更新用户图像
     */
    public function actionUpdateavatar()
    {
        if ((Yii::app()->request->isPostRequest)) {
            $this->check_key();
            //$member_id = Frame::getIntFromRequest('member_id');
            $poster = Frame::saveImage('poster');
            if (!$poster) {
                $result['ret_num'] = 4100;
                $result['ret_msg'] = '图片没有上传';
                echo json_encode($result);
                die();
            }
            $user = $this->check_user();

            $user->poster = $poster;
            if ($user->update()) {
                $result ['ret_num'] = 0;
                $result ['ret_msg'] = '操作成功';
                $result['poster'] = URL . $user->poster;
            } else {
                $result ['ret_num'] = 1999;
                $result ['ret_msg'] = '用户图像修改失败';
            }

            echo json_encode($result);
        }
    }

    /**
     * 修改手机号码
     */
    public function actionChangephone()
    {
        $this->check_key();
        $phone = Frame::getStringFromRequest('phone');
        $idcode = Frame::getIntFromRequest('code');
        if (empty ($phone)) {
            $result ['ret_num'] = 2010;
            $result ['ret_msg'] = '请输入手机号';
            echo json_encode($result);
            die ();
        }
        $criteria = new CDbCriteria();
        $criteria->select = "idcode";
        $criteria->condition = 'phone = :phone';
        $criteria->order = 'send_time desc';
        $criteria->params = array(':phone' => $phone);
        $code = Idcode::model()->find($criteria);
        if ($code->idcode != $idcode) {
            $result ['ret_num'] = 2005;
            $result ['ret_msg'] = '验证码错误';
            echo json_encode($result);
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
        if ($re) {
            $result ['ret_num'] = 2007;
            $result ['ret_msg'] = '该手机号码已注册';
            echo json_encode($result);
            die ();
        }
        $oldPhone = $user->phone;
        $user->phone = $phone;
        if ($user->update()) {
            $connection = Yii::app()->db;
            //删除旧号码犇犇状态
            //$sql = "update group_contact_phone set is_benben=0 where phone ='{$oldPhone}'";				
            $sql = "update group_contact_phone set phone ='{$phone}' where phone ='{$oldPhone}'";
            $command = $connection->createCommand($sql);
            $result1 = $command->execute();
            //修改通讯录group_contact_phone表is_benben
            //是否是百姓网用户
            $short_phone = 0;
            $info = Bxapply::model()->find("phone = '{$phone}' and status = 3");
            if ($info && $info->short_phone) {
                $short_phone = $info->short_phone;
            }
            $sql = "update group_contact_phone set is_benben={$user->benben_id},is_baixing={$short_phone} where phone='{$phone}'";
            $command = $connection->createCommand($sql);
            $result1 = $command->execute();
            //退出政企通讯录
            $sql = "select id,contact_id from enterprise_member where phone ='{$oldPhone}'";
            $command = $connection->createCommand($sql);
            $result1 = $command->queryAll();
            $eid = array();
            $emid = array();
            if ($result1[0]) {
                foreach ($result1 as $va) {
                    $eid[] = $va['contact_id'];
                    $emid[] = $va['id'];
                }
            }
            if ($eid) {
                $sql2 = "update enterprise set number = number - 1 where id in(" . implode(",", $eid) . ")";
                $command = $connection->createCommand($sql2);
                $result1 = $command->execute();
            }
            if ($emid) {
                $sql2 = "delete from enterprise_member where id in(" . implode(",", $emid) . ")";
                $command = $connection->createCommand($sql2);
                $result1 = $command->execute();
            }
            $sql3 = "delete from enterprise_display_member where member_id in(" . implode(",", $emid) . ") and enterprise_id in(" . implode(",", $eid) . ")";

            $result ['ret_num'] = 0;
            $result ['ret_msg'] = '操作成功';
            $result['phone'] = $phone;
        } else {
            $result ['ret_num'] = 999;
            $result ['ret_msg'] = '手机号码修改失败';
        }
        echo json_encode($result);

    }

    /**
     * 根据环信用户名查询用户资料
     */
    public function actionHxmemberinfo()
    {
        $this->check_key();
        $hxname = Frame::getStringFromRequest('hxname');
        $user = $this->check_user();
        if (!$hxname) {
            $result['ret_num'] = 410;
            $result['ret_msg'] = '环信用户名为空';
            echo json_encode($result);
            die();
        }

        $hxname = explode(",", $hxname);
        $hxn = "";
        foreach ($hxname as $val) {
            $hxn .= "'" . $val . "',";
        }
        $hxn = trim($hxn);
        $hxn = trim($hxn, ',');
        //查找好友
        $connection = Yii::app()->db;

        //通讯录里的犇犇好友
        $sqlf = "select a.is_benben,a.phone,b.name,b.benben_id from group_contact_phone a right join group_contact_info b on a.contact_info_id = b.id where b.member_id = {$user->id} and (a.is_benben>0 or b.benben_id>0)";
        $command = $connection->createCommand($sqlf);
        $fried_array = $command->queryAll();
        $farray = array();
        foreach ($fried_array as $key => $value) {
            $item_phone = $value['phone'];
            $item_name = $value['name'];
            $item_benben = $value['is_benben'] ? $value['is_benben'] : $value['benben_id'];
            if (empty($farray[$item_benben])) {
                //手机好重复，保留第一个名字
                $farray[$item_benben] = $item_name;
            }
        }
        $sql = "select id,name,nick_name,poster,phone,huanxin_username,benben_id from member where huanxin_username in ({$hxn})";
        $command = $connection->createCommand($sql);
        $result1 = $command->queryAll();

        foreach ($result1 as $key => $value) {
            $is_friend = 0;
            $item_phone = $value['phone'];
            $is_benben = $value['benben_id'];
            if (!empty($farray[$is_benben])) {
                $is_friend = 1;
                $result1[$key]['name'] = $farray[$is_benben];
                $result1[$key]['nick_name'] = $farray[$is_benben];
            }

            $result1[$key]['is_friend'] = $is_friend;
            $result1[$key]['poster'] = $value['poster'] ? URL . $value['poster'] : "";
        }
        $result ['ret_num'] = 0;
        $result ['ret_msg'] = '操作成功';
        $result['user'] = $result1;

        echo json_encode($result);
    }

    /**
     * 根据环信用户名查询该用户在我的通讯录信息
     */
    public function actionHxcontactinfo()
    {
        $this->check_key();
        $user = $this->check_user();
        $hxname = Frame::getStringFromRequest('hxname');
        if (!$hxname) {
            $result['ret_num'] = 410;
            $result['ret_msg'] = '环信用户名为空';
            echo json_encode($result);
            die();
        }
        $userinfo = Member::model()->find("huanxin_username = '{$hxname}'");
        if (!$userinfo) {
            $result['ret_num'] = 5206;
            $result['ret_msg'] = '环信用户名不存在';
            echo json_encode($result);
            die();
        }

        $is_baixing = 0;
        $bxinfo = Bxapply::model()->find("member_id = {$userinfo->id}");
        if ($bxinfo) {
            $is_baixing = $bxinfo->short_phone;
        }

        $connection = Yii::app()->db;
        $sql0 = "select a.id,a.group_id,a.name,a.pinyin,a.created_time,b.poster,b.huanxin_username from group_contact_info a left join member b on a.benben_id = b.benben_id 
		where a.member_id = {$user->id} and a.benben_id = {$userinfo->benben_id}";
        $command = $connection->createCommand($sql0);
        $result1 = $command->queryAll();
        $phoneinfo = array();
        if ($result1[0]['id']) {
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
        $result1[0]['poster'] = $result1[0]['poster'] ? URL . $result1[0]['poster'] : "";
        //$contact_info_id = $result1[0]['contact_info_id'];
        //$result1[0]['contact_info_id'] = $result1[0]['id'];
        //$result1[0]['id'] = $contact_info_id;
        $result1[0]['huanxin_username'] = $hxname;
        if ($phoneinfo) {
            foreach ($phoneinfo as $key => $va) {
                $phoneinfo[$key]['poster'] = $va['poster'] ? URL . $va['poster'] : "";
                $phoneinfo[$key]['huanxin_username'] = $va['huanxin_username'] ? $va['huanxin_username'] : "";
            }
        }


        $return_array = $result1[0];
        $return_array['phone'] = $phoneinfo;
        $return_array['is_friend'] = 1;

        $result ['ret_num'] = 0;
        $result ['ret_msg'] = '操作成功';
        $result['user'] = $return_array;

        echo json_encode($result);
    }

    /**
     * 根据环信用户名查询该用户在我的通讯录信息(群组用)
     */
    public function actionHxgcontactinfo()
    {
        $this->check_key();
        $user = $this->check_user();
        $hxname = Frame::getStringFromRequest('hxname');
        if (!$hxname) {
            $result['ret_num'] = 410;
            $result['ret_msg'] = '环信用户名为空';
            echo json_encode($result);
            die();
        }
        $userinfo = Member::model()->find("huanxin_username = '{$hxname}'");
        if (!$userinfo) {
            $result['ret_num'] = 5206;
            $result['ret_msg'] = '环信用户名不存在';
            echo json_encode($result);
            die();
        }
        $userid = $userinfo->id;
        $connection = Yii::app()->db;

        //查出好友在自己通讯录里的名字			
        $sql2 = "select a.phone,a.is_benben,a.is_baixing,a.contact_info_id,b.name,b.pinyin,b.id,b.allpinyin,b.group_id from group_contact_phone a left join group_contact_info b on a.contact_info_id = b.id
		where b.member_id = {$user->id} and ((a.phone = {$userinfo->phone} and a.is_benben>0) or a.is_benben={$userinfo->benben_id})";//id may think again test!
        $command = $connection->createCommand($sql2);
        $res2 = $command->queryAll();
        $res3 = array();
        if ($res2 && count($res2) > 0) {
            //在自己通讯录里
            $is_friend = 1;
            //查询该用户其他号码
            $sql3 = "select a.id as phoneid,a.phone,a.is_benben,a.is_baixing,a.is_active,b.name,b.id from group_contact_phone a left join group_contact_info b on a.contact_info_id = b.id
			where b.member_id = {$user->id} and a.contact_info_id = {$res2[0]['contact_info_id']}";
            $command = $connection->createCommand($sql3);
            $res3 = $command->queryAll();

            //数据处理
            $benbenArray=array();
            $ids=array();
            $userid2benben=array();
            foreach($res3 as $kr=>$vr){
                if(!in_array($vr['is_benben'],$benbenArray)) {
                    $benbenArray[] = $vr['is_benben'];
                }
            }
            if($benbenArray) {
                $sqlmember = "select nick_name,poster,huanxin_username,benben_id,id from member where benben_id!=0 and benben_id in (" . implode(",", $benbenArray) . ")";
                $command = $connection->createCommand($sqlmember);
                $resmember = $command->queryAll();
                $contactsInfo=array();
                foreach($resmember as $km=>$vm){
                    $contactsInfo[$vm['benben_id']]=$vm;
                    $userid2benben[$vm['id']]=$vm['benben_id'];
                    $ids[]=$vm['id'];
                }
                foreach($res3 as $krr=>$vrr){
                    $res3[$krr]['nick_name']=$contactsInfo[$vrr['is_benben']]['nick_name']?$contactsInfo[$vrr['is_benben']]['nick_name']:"";
                    $res3[$krr]['poster']=$contactsInfo[$vrr['is_benben']]['poster']?$contactsInfo[$vrr['is_benben']]['poster']:"";
                    $res3[$krr]['huanxin_username']=$contactsInfo[$vrr['is_benben']]['huanxin_username']?$contactsInfo[$vrr['is_benben']]['huanxin_username']:"";
                }
            }

            //查询该用户的号码直通车
            $trainArr=array();
            $traininfo=NumberTrain::model()->findAll("member_id in (".implode(",",$ids).") and status=0 and is_close=0");
            foreach($traininfo as $k=>$v){
                if($userid2benben[$v['member_id']]) {
                    $trainArr[$userid2benben[$v['member_id']]] = $v;
                }
            }

            //查询该用户的好友联盟
            $friendArr=array();
            $friendinfo=FriendLeague::model()->findAll("member_id in (".implode(",",$ids).") and status=0 and is_delete=0");
            $districtinfo=$this->ProCity($friendinfo);
            foreach($friendinfo as $kk=>$vv){
                if($userid2benben[$vv['member_id']]) {
                    $friendArr[$userid2benben[$v['member_id']]] = $vv;
                    $districtArr[$userid2benben[$vv['member_id']]] = $districtinfo[$friendinfo['city']] . " " . $districtinfo[$friendinfo['area']];
                }
            }
        } else {
            $is_friend = 0;

            $sql0 = "select id,name,pinyin,allpinyin from group_contact_info where member_id = {$user->id} and benben_id = {$userinfo->benben_id}";
            $command = $connection->createCommand($sql0);
            $res2 = $command->queryAll();

            //查询该用户的号码直通车
            $traininfo=NumberTrain::model()->find("member_id={$userinfo['id']} and status=0 and is_close=0");

            //查询该用户的好友联盟
            $friendinfo=FriendLeague::model()->find("member_id={$userinfo['id']} and status=0 and is_delete=0");
            $districtinfo=$this->ProCity(array(0=>$friendinfo));
            $district=$districtinfo[$friendinfo['city']]." ".$districtinfo[$friendinfo['area']];
            if ($res2[0]) {
                $is_friend = 1;
            }

        }
        $phone = array();
        if ($is_friend==1&&count($res3)) {
            foreach ($res3 as $v) {
                $phone[] = array(
                    "infoid" => $v['id'],
                    "id"=>$v['phoneid'],
                    "nick_name" => $v['nick_name'] ? $v['nick_name'] : $userinfo->nick_name,
                    "poster" => $v['poster'] ? URL . $v['poster'] : "",
                    "is_benben" => $v['is_benben'] ? $v['is_benben'] : "0",
                    "is_baixing" => $v['is_baixing'] ? $v['is_baixing'] : "0",
                    "phone" => $v['phone'] ? $v['phone'] : "",
                    "huanxin_username" => $v['huanxin_username'] ? $v['huanxin_username'] : "",
                    "is_active"=>$v['is_active']?$v['is_active']:"0",
                    "train_id"=>$trainArr[$v['is_benben']]['id'] ? $trainArr[$v['is_benben']]['id'] : "",
                    "pic"=>$trainArr[$v['is_benben']]['poster'] ? URL.$trainArr[$v['is_benben']]['poster'] : "",
                    "short_name"=> $trainArr[$v['is_benben']]['short_name'] ? $trainArr[$v['is_benben']]['short_name'] : "",
                    "tag"=>$trainArr[$v['is_benben']]['tag'] ? $trainArr[$v['is_benben']]['tag'] : "",
                    "legid"=>$friendArr[$v['is_benben']]['id'] ? $friendArr[$v['is_benben']]['id'] : "",
                    "leg_district"=>$districtArr[$v['is_benben']] ? $districtArr[$v['is_benben']] : "",
                    "leg_poster"=> $friendArr[$v['is_benben']]['poster'] ? URL.$friendArr[$v['is_benben']]['poster'] : "",
                    "leg_name"=>$friendArr[$v['is_benben']]['name']?$friendArr[$v['is_benben']]['name']:"",
                    "type"=>$friendArr ? ($friendArr[$v['is_benben']]['type']==1 ? '工作联盟' : '英雄联盟') : ""
                );
            }
        }
        if($is_friend==0){
            $phone[]=array(
                "train_id"=>$traininfo ? $traininfo['id'] : "",
                "pic"=>$traininfo['poster'] ? URL.$traininfo['poster'] : "",
                "short_name"=> $traininfo['short_name'] ? $traininfo['short_name'] : "",
                "tag"=>$traininfo['tag'] ? $traininfo['tag'] : "",
                "legid"=>$friendinfo ? $friendinfo['id'] : "",
                "leg_district"=>$districtArr ? $district : "",
                "leg_poster"=> $friendinfo['poster'] ? URL.$friendinfo['poster'] : "",
                "leg_name"=>$friendinfo['name']?$friendinfo['name']:"",
                "type"=>$friendinfo ? ($friendinfo['type']==1 ? '工作联盟' : '英雄联盟') : ""
            );
        }

        $result ['ret_num'] = 0;
        $result ['ret_msg'] = '操作成功';
        $result['user'] = array(
            "member_id" => $userinfo->id,
            "infoid" => $res2[0]['contact_info_id'] ? $res2[0]['contact_info_id'] : ($res2[0]['id'] ? $res2[0]['id'] : ""),
            "name" => $is_friend ? $res2[0]['name'] : $userinfo->nick_name,
            "nick_name" => $userinfo->nick_name,
            "group_id" =>  $res2[0]['group_id'] ?  $res2[0]['group_id'] : "",
            "poster" => $userinfo->poster ? URL . $userinfo->poster : "",
            "is_benben" => $userinfo->benben_id,
            "is_baixing" => $res2[0]['is_baixing'] ? $res2[0]['is_baixing'] : "",
            "huanxin_username" => $userinfo->huanxin_username,
            "phone" => $phone,
            "is_friend" => $is_friend,
            "created_time" => date("Y-m-d", $user->created_time),
            "pinyin" => $res2[0]['pinyin'] ? $res2[0]['pinyin'] : "",
            "allpinyin" => $res2[0]['allpinyin'] ? $res2[0]['allpinyin'] : "",
        );

        echo json_encode($result);
    }

    /**
     * 根据二维码查询该用户在我的通讯录信息
     */
    public function actionQrcontactinfo()
    {
        $this->check_key();
        $user = $this->check_user();
        $qr_name = Frame::getStringFromRequest('qr_name');
        $userid = base64_decode(substr($qr_name, 16));
        $userinfo = Member::model()->find("id = '{$userid}'");
        $connection = Yii::app()->db;
        if (!$userinfo) {
            $result ['ret_num'] = 5204;
            $result ['ret_msg'] = '用户不存在';
            echo json_encode($result);
            die();
        }
        if (substr(md5($userinfo->phone), 0, 16) != substr($qr_name, 0, 16)) {
            $result ['ret_num'] = 5205;
            $result ['ret_msg'] = '二维码信息错误';
            echo json_encode($result);
            die();
        }

        //查出好友在自己通讯录里的名字			
        $sql2 = "select a.phone,a.is_benben,a.is_baixing,a.contact_info_id,b.name from group_contact_phone a left join group_contact_info b on a.contact_info_id = b.id
		where b.member_id = {$user->id} and a.phone = {$userinfo->phone} and a.is_benben>0";
        $command = $connection->createCommand($sql2);
        $res2 = $command->queryAll();
        $res3 = array();
        if ($res2 && count($res2) > 0) {
            //在自己通讯录里
            $is_friend = 1;
            //查询该用户其他号码
            $sql3 = "select a.phone,a.is_benben,a.is_baixing,b.name from group_contact_phone a left join group_contact_info b on a.contact_info_id = b.id
			where b.member_id = {$user->id} and a.contact_info_id = {$res2[0]['contact_info_id']}";
            $command = $connection->createCommand($sql3);
            $res3 = $command->queryAll();
        } else {
            $is_friend = 0;
            $sql0 = "select id as contact_info_id,name from group_contact_info where member_id = {$user->id} and benben_id = {$userinfo->benben_id}";
            $command = $connection->createCommand($sql0);
            $res2 = $command->queryAll();
            if ($res2[0]) {
                $is_friend = 1;
            }
        }

        if ($res3[0]) {
            foreach ($res3 as $v) {
                $phone[] = array(
                    "nick_name" => $userinfo->nick_name,
                    "benben_id" => $userinfo->benben_id,
                    "poster" => $userinfo->poster ? URL . $userinfo->poster : "",
                    "is_benben" => $userinfo->benben_id,
                    "is_baixing" => $v['is_baixing'],
                    "phone" => $v['phone'],
                    "huanxin_username" => $userinfo->huanxin_username,
                );
            }
        }

        $result ['ret_num'] = 0;
        $result ['ret_msg'] = '操作成功';
        $result['user'] = array(
            "name" => $is_friend ? $res2[0]['name'] : $userinfo->nick_name,
            "nick_name" => $userinfo->nick_name,
            "benben_id" => $userinfo->benben_id,
            "group_id" => "",
            "id"=>$res2[0]['contact_info_id'] ? $res2[0]['contact_info_id'] : 0,
            "poster" => $userinfo->poster ? URL . $userinfo->poster : "",
            "is_benben" => $userinfo->benben_id,
            "is_baixing" => $is_baixing?$is_baixing:0,
            "huanxin_username" => $userinfo->huanxin_username,
            "phone" => $phone,
            "is_friend" => $is_friend
        );
        echo json_encode($result);


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
    public function actionSearch()
    {
        $this->check_key();
        $keyword = Frame::getStringFromRequest('keyword');
        $last_time = Frame::getIntFromRequest('last_time');
        $user = $this->check_user();
        if (!$keyword) {
            $result['ret_num'] = 420;
            $result['ret_msg'] = '奔犇号或昵称为空';
            echo json_encode($result);
            die();
        }
        //查找好友
        $connection = Yii::app()->db;
// 		$sqlf = "select b.is_benben from group_contact_info a left join group_contact_phone b on a.id = b.contact_info_id where b.is_benben > 0 and a.member_id = ".$user->id;
        $sqlf = "select benben_id from group_contact_info where member_id = " . $user->id;
        $command = $connection->createCommand($sqlf);
        $friend = $command->queryAll();
        $memberid = "";
        $fri = array();
        foreach ($friend as $v) {
            $fri[] = $v['benben_id'];
        }
        $fri = array_flip(array_flip($fri));
        $sql = "select id,nick_name,poster,huanxin_username,created_time, benben_id from member where ((benben_id = '{$keyword}') or (nick_name like '%{$keyword}%')) ";
        if ($last_time) {
            $sql = "select id,nick_name,poster,huanxin_username,created_time, benben_id from member where ((benben_id = '{$keyword}') or (nick_name like '%{$keyword}%')) and created_time<{$last_time} ";
        }
        $sql .= "and id <> {$user->id} order by created_time desc";
        $command = $connection->createCommand($sql);
        $result1 = $command->queryAll();
        $re = array();
        foreach ($result1 as $key => $value) {
            //$is_friend = 0;
            if (in_array($value['benben_id'], $fri)) {
                //$is_friend = 1;
                continue;
            }
            $re[] = $value;
            //$result1[$key]['is_friend'] =$is_friend;
            //$result1[$key]['poster'] = $value['poster'] ? URL.$value['poster'] : "";
        }
        foreach ($re as $key => $value) {
            $re[$key]['poster'] = $value['poster'] ? URL . $value['poster'] : "";
        }
        $result ['ret_num'] = 0;
        $result ['ret_msg'] = '操作成功';
        $result['user'] = $re;

        echo json_encode($result);
    }

    /**
     * 查询用户资料
     */
    public function actionMemberinfo()
    {
        $this->check_key();
        //$member_id = Frame::getIntFromRequest('member_id');
        $user = $this->check_user();
        $pinfo = $this->pcinfo();
        $pro_city = "";
        if ($user->province && $user->city) {
            $pro_city = $pinfo[0][$user->province] . " " . $pinfo[1][$user->city];
        }
        $connection = Yii::app()->db;
        $sql = "select short_phone from bxapply a inner join member b on a.phone = b.phone where b.phone = '{$user->phone}' and a.status=3";
        $command = $connection->createCommand($sql);
        $baixing = $command->queryAll();
        //省市
        $pro = array("province" => $user->province, "city" => $user->city, "area" => $user->area);
        $pro_arr = $this->ProCity(array($pro));
        $appellation = "";
        //等级
        $level_all = getlevel();
        foreach ($level_all as $va) {
            if ($user->integral <= $va[1]) {
                $appellation = $va[2];
                break;
            }
        }

        $result ['ret_num'] = 0;
        $result ['ret_msg'] = '操作成功';
        $result['user'] = array(
            "UserId" => $user->id,
            "BenbenId" => $user->benben_id,
            "UserNickname" => $user->nick_name ? $user->nick_name : "",
            "Name" => $user->name ? $user->name : "",
            "Poster" => $user->poster ? URL . $user->poster : "",
            "UserSex" => $user->sex ? $user->sex : "",
            "Age" => $user->age ? $user->age : "",
            "Phone" => $user->phone ? $user->phone : "",
            "Cornet" => $user->cornet ? $user->cornet : "",
            "Integral" => $user->integral ? $user->integral : "",
            'appellation' => $this->eraseNull($appellation),
            "Coin" => $user->coin ? $user->coin : "",
            "Address" => $user->address ? $user->address : "",
            "BaiXing" => $baixing[0]['short_phone'] ? $baixing[0]['short_phone'] : "",
            "ProCity" => $pro_arr[$user->province] . ' ' . $pro_arr[$user->city] . ' ' . $pro_arr[$user->area],
            "QrCode" => $user->qrcode ? URL . $user->qrcode : "",
            "Token" => $user->token
        );

        echo json_encode($result);
    }

    /*
     * 好友申请备注信息
     * 涉及friend_apply_log
     */
    public function actionApplyfriend(){
        $this->check_key();
        $from_huanxin = Frame::getStringFromRequest('from_huanxin');
        $to_huanxin = Frame::getStringFromRequest('to_huanxin');
        $name = Frame::getStringFromRequest('name');
        $groupid = Frame::getIntFromRequest('group_id');
        $user = $this->check_user();
        if(!$from_huanxin||!$to_huanxin||!$name||!$groupid){
            $result ['ret_num'] = 5080;
            $result ['ret_msg'] = '缺少参数';
            echo json_encode($result);
            die ();
        }
        $friendlog=new FriendApplyLog();
        $friendlog->from_huanxin=$from_huanxin;
        $friendlog->to_huanxin=$to_huanxin;
        $friendlog->name=$name;
        $friendlog->group_id=$groupid;
        $friendlog->created_time=time();
        if($friendlog->save()){
            $result ['ret_num'] = 0;
            $result ['ret_msg'] = '操作成功';
            echo json_encode($result);
        }else{
            $result ['ret_num'] = 100;
            $result ['ret_msg'] = '网络不稳，请重新尝试';
            echo json_encode($result);
        }
    }

    /**
     * 添加好友
     */
    public function actionAddfriend()
    {
        $this->check_key();
        $huanxin_username = Frame::getStringFromRequest('huanxin_username');
        $name = Frame::getStringFromRequest('name');
        $groupid = Frame::getIntFromRequest('group_id');
        if (empty ($huanxin_username)) {
            $result ['ret_num'] = 5080;
            $result ['ret_msg'] = '奔犇ID为空';
            echo json_encode($result);
            die ();
        }
        $user = $this->check_user();
        $add_user = Member::model()->find("huanxin_username = '{$huanxin_username}'");
        $own_bx = Bxapply::model()->find("phone = '{$user->phone}' and status = 3");
        $add_bx = Bxapply::model()->find("phone = '{$add_user->phone}' and status = 3");
        $ownbxid = 0;
        $addbxid = 0;
        if ($own_bx) {
            $ownbxid = $own_bx->short_phone;
        }
        if ($add_bx) {
            $addbxid = $add_bx->short_phone;
        }

        if (empty ($add_user)) {
            $result ['ret_num'] = 5081;
            $result ['ret_msg'] = '待添加用户不存在';
            echo json_encode($result);
            die ();
        }
        //查未分组ID
        $own = GroupContact::model()->find("group_name = '未分组' and member_id = {$user->id}");
        $friend = GroupContact::model()->find("group_name = '未分组' and member_id = {$add_user->id}");
        $connection = Yii::app()->db;
        $PinYin = new tpinyin();//var_dump($add_user->id);
        $t = time();//echo "未分组ID";var_dump($own->id);var_dump($friend->id);exit;

        //查询待添加号码是否在自己的通讯录
        $sql = "select count(*) as num from (
						select c.contact_info_id,c.phone from 
						(select a.contact_info_id,a.phone from group_contact_phone a
						left join group_contact_info b
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
        $sql = "select count(*) as num from (
						select c.contact_info_id,c.phone from
						(select a.contact_info_id,a.phone from group_contact_phone a
						left join group_contact_info b
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
        $resulta1_a = $command->queryAll();
        if (($resulta[0]['num'] || $resulta_a[0]['id']) && ($resulta1[0]['num'] || $resulta1_a[0]['id'])) {
            $result ['ret_num'] = 5208;
            $result ['ret_msg'] = '已经是好友';
            echo json_encode($result);
            die ();
        }

        $flag = 0;
        if (!$resulta[0]['num'] && !$resulta_a[0]['id']) {
            //对方不在自己的通讯录
//			if($ownbxid){
            //我不是百姓用户，对方百姓用户置空
//				$addbxid=0;
//			}
            //好友联盟
            $leginfo = FriendLeague::model()->find("member_id={$add_user['id']} and status=0 and is_delete=0");
            $district = $this->ProCity(array(0 => $leginfo));
            $tpl_d = $district[$leginfo['city']] . " " . $district[$leginfo['area']];

            //号码直通车
            $traininfo = NumberTrain::model()->find("member_id={$add_user['id']} and status=0 and is_close=0");
            //添加姓名
            $own_result = new GroupContactInfo();
            $own_result->group_id = $groupid ? $groupid : $own->id;
            $own_result->name = $name ? $name : $add_user->nick_name;
            $own_result->pinyin = $PinYin->str2sort($add_user->nick_name);
            $own_result->allpinyin = $PinYin->str2py($add_user->nick_name);
            $own_result->member_id = $user->id;
            $own_result->benben_id = $add_user->benben_id;
            $own_result->created_time = $t;
            //添加号码
            if ($own_result->save()) {
                $own_phone = new GroupContactPhone();
                $own_phone->contact_info_id = $own_result->id;
                $own_phone->is_benben = $add_user->benben_id;
                $own_phone->is_baixing = $addbxid;
                $own_phone->save();
            }
            //返回加后的记录			
            $friend_info = array(
                "id" => $own_result->id,//group_contact_info表的ID
                "group_id" => $groupid ? $groupid : $own->id,//分组ID
                "name" => $own_result->name,
                "nick_name" => $add_user->nick_name,
                "pinyin" => $own_result->pinyin,
                "allpinyin" => $own_result->allpinyin,
                "created_time" => $own_result->created_time,
                "is_benben" => $add_user->benben_id,
                "is_baixing" => $addbxid,
                "poster" => $add_user->poster ? URL . $add_user->poster : "",
                "huanxin_username" => $add_user->huanxin_username,
                "phone" => array(
                    0 => array(
                        "is_active" => 0,
                        "huanxin_username" => $add_user->huanxin_username,
                        "contact_info_id" => $own_result->id,
                        "legid" => $leginfo['id'] ? $leginfo['id'] : "",
                        "leg_poster" => $leginfo['poster'] ? URL . $leginfo['poster'] : "",
                        "type" => $leginfo['type'] ? ($leginfo['type'] == 1 ? '工作联盟' : '英雄联盟') : "",
                        "leg_name" => $leginfo['name'],
                        "leg_district" => $tpl_d,
                        "train_id" => $traininfo['id'] ? $traininfo['id'] : "",
                        "pic" => $traininfo['poster'] ? URL . $traininfo['poster'] : "",
                        "short_name" => $traininfo['short_name'] ? $traininfo['short_name'] : "",
                        "tag" => $traininfo['tag'] ? $traininfo['tag'] : "",
                        "is_benben" => $add_user->benben_id,
                        "is_baixing" => $addbxid,
                        "nick_name" => $add_user->nick_name,
                        "phone" => "",
                        "id" => $own_phone['id']
                    )
                )/*array(
							"phone"=>$add_user->phone,
							"is_benben"=>$add_user->benben_id,
							"is_baixing"=> $addbxid,
							"poster"=>$add_user->poster ? URL.$add_user->poster : "",
							"nick_name"=>$add_user->nick_name
					)*/
            );
        } else {
            $flag = 1;
            //$friend_info = array();
        }

        $targetContactID = 0;
        if (!$resulta1[0]['num'] && !$resulta1_a[0]['id']) {
            //自己不在对方的通讯录
            //查询该申请者选择的分组
            $user_group="";
            $user_name="";
            $applyinfo=FriendApplyLog::model()->find("from_huanxin='{$huanxin_username}' and to_huanxin='{$user['huanxin_username']}' order by created_time DESC");
            if($applyinfo && $applyinfo['group_id']){
                //判断分组是否存在
                $exist_tpl=GroupContact::model()->count("member_id={$add_user['id']} and id={$applyinfo['group_id']}");
                if($exist_tpl){
                    $user_group=$applyinfo['group_id'];
                    $user_name=$applyinfo['name'];
                }
            }
            //添加姓名
            $friend_result = new GroupContactInfo();
            $friend_result->group_id = $user_group ? $user_group : $friend->id;
            $friend_result->name = $user_name ? $user_name : $user->nick_name;
            $friend_result->pinyin = $PinYin->str2sort($user->nick_name);
            $friend_result->allpinyin = $PinYin->str2py($user->nick_name);
            $friend_result->member_id = $add_user->id;
            $friend_result->benben_id = $user->benben_id;
            $friend_result->created_time = $t;
            $targetContactID = $friend_result->id;
            //添加号码
            if ($friend_result->save()) {
                $friend_phone = new GroupContactPhone();
                $friend_phone->contact_info_id = $friend_result->id;
                $friend_phone->is_benben = $user->benben_id;
                $friend_phone->is_baixing = $ownbxid;
                $friend_phone->save();
            }
        }

        //if($result1){
        if (1) {
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
            if ($flag) {
                $result ['ret_num'] = 5218;
                $result ['ret_msg'] = '添加成功';
                $result['targetContactID'] = $targetContactID;
                echo json_encode($result);
                die ();
            }
            $result ['ret_num'] = 0;
            $result ['ret_msg'] = '操作成功';
            $result['targetContactID'] = $targetContactID;
            $result['friend_info'] = $friend_info;
            $m = new Memcached();
            $m->addServer('localhost', 11211);
            $snapshot = $m->get("addrsversion:" . $user['id']);
            $m->set("addrsversion:" . $user['id'],($snapshot+1));
        } else {
            $result ['ret_num'] = 5082;
            $result ['ret_msg'] = '添加好友失败';
        }
        echo json_encode($result);
    }

    /*
	 * 拒绝好友请求
	 * 涉及表news
	 * 传入环信号huanxin_username，
	 */
    public function actionRejectfriend()
    {
        $this->check_key();
        $user = $this->check_user();
        $huanxin_username = Frame::getStringFromRequest('huanxin_username');
        if (empty ($huanxin_username)) {
            $result ['ret_num'] = 5080;
            $result ['ret_msg'] = '奔犇ID为空';
            echo json_encode($result);
            die ();
        }
        $userinfo = Member::model()->find("huanxin_username='{$huanxin_username}' and id_enable=1");
        if (empty($userinfo)) {
            $result ['ret_num'] = 5000;
            $result ['ret_msg'] = '该账号不存在';
            echo json_encode($result);
            die ();
        }
        $content = "{$user['nick_name']}拒绝了您的好友请求";
        $t = time();
        $sql = "insert into news (type,sender,member_id,content,status,created_time,display) VALUES (2,{$user->id},{$userinfo['id']},'{$content}',0,{$t},0)";
        $connection = Yii::app()->db;
        $command = $connection->createCommand($sql);
        $result1 = $command->execute();

        //发送环信信息通知
        $other_arr = array(
            "nick_name" => $user->nick_name,
            "user_poster" => $user->poster ? URL . $user->poster : "",
            "time" => time(),
            "t1" => 1,
            "t2" => 1,
            "t3" => 2,
            "t4" => 3);
        $this->sendHXMessage(array(0 => $huanxin_username), $content, $other_arr);

        $result ['ret_num'] = 0;
        $result ['ret_msg'] = '操作成功';
        echo json_encode($result);
    }

    public function actionDelete()
    {
        $id1 = Frame::getStringFromRequest("id");
        //$id = Member::model()->find("phone = '{$phone}'");
// 		$connection = Yii::app()->db;
// 		$sql = "delete from group_contact a
// 		inner join group_contact_info b on b.group_id = a.id inner join group_contact_phone c on c.contact_info_id = b.id		
// 		where a.member_id = {$id}";
        $ids = explode(",", $id1);
        foreach ($ids as $id) {
            $re = array();
            $re = GroupContact::model()->findAll("member_id = {$id}");

            $i = 0;
            foreach ($re as $va) {
                $re1 = GroupContactInfo::model()->findAll("group_id = {$va->id}");
                foreach ($re1 as $val) {
                    $val->member_id = $id;
                    $val->update();
                    $i++;
                    echo ">>>>>>" . $i;
                }
            }
        }

    }

    /**
     * 生成二维码
     */
    public function actionQrcode()
    {
        $key = Frame::getStringFromRequest('key');
        $userid = Frame::getStringFromRequest('userid');
        $phone = Frame::getStringFromRequest('phone');
        $pathinfo = "index.php/v1/user/getqrcode/key/{$key}/qr_name/";
        $qrcodeinfo = URL . "/" . $pathinfo . substr(md5($phone), 0, 16) . base64_encode($userid);
        $qrcodename = "uploads/images/qrcode/" . substr(md5($phone), 0, 16) . base64_encode($userid) . ".png";
        //$user->qrcode = $qrcodename;
        include('lib/phpqrcode/phpqrcode.php');
        QRcode::png($qrcodeinfo, $qrcodename);
        //$user->update();
        echo $qrcodename;
    }

    function checkUsername($username)
    {
        $rule = "#[a-zA-Z0-9_\-./ ]*#";
        preg_match($rule, $username, $result);
        if (count($result) > 0) {
            return $result[0] == $username;
        }
        return false;
    }

    function openResiter($username, $password, $nickname)
    {
        $option['client_id'] = CLIENT_ID;
        $option['client_secret'] = CLIENT_SECRET;
        $option['org_name'] = ORG_NAME;
        $option['app_name'] = APP_NAME;
        $parameter['username'] = $username;
        $parameter['password'] = $password;
        $parameter['nickname'] = $nickname;
        $url = 'https://a1.easemob.com/' . $option['org_name'] . '/' . $option['app_name'] . '/users';
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE); // 对认证证书来源的检查
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE); // 从证书中检查SSL加密算法是否存在
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0; Trident/4.0)'); // 模拟用户使用的浏览器
        if (!empty ($parameter)) {
            $options = json_encode($parameter);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $options); // Post提交的数据包
        }
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        // curl_setopt ( $curl, CURLOPT_HTTPHEADER, $header ); // 设置HTTP头
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        $result = curl_exec($curl); // 执行操作

        curl_close($curl); // 关闭CURL会话
        return $result;
    }

    /*
     * {
      *	"access_token":"YWMtWY779DgJEeS2h9OR7fw4QgAAAUmO4Qukwd9cfJSpkWHiOa7MCSk0MrkVIco",
       *	"expires_in":5184000,
       *	"application":"c03b3e30-046a-11e4-8ed1-5701cdaaa0e4"
     *	}
     */
    function getHXToken()
    {
        $option['client_id'] = CLIENT_ID;
        $option['client_secret'] = CLIENT_SECRET;
        $option['org_name'] = ORG_NAME;
        $option['app_name'] = APP_NAME;
        $url = 'https://a1.easemob.com/' . $option['org_name'] . '/' . $option['app_name'] . '/token';

        $parameter['grant_type'] = "client_credentials";
        $parameter['client_id'] = $option['client_id'];
        $parameter['client_secret'] = $option['client_secret'];

        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE); // 对认证证书来源的检查
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE); // 从证书中检查SSL加密算法是否存在
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0; Trident/4.0)'); // 模拟用户使用的浏览器
        if (!empty ($parameter)) {
            $options = json_encode($parameter);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $options); // Post提交的数据包
        }
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        // curl_setopt ( $curl, CURLOPT_HTTPHEADER, $header ); // 设置HTTP头
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        $result = curl_exec($curl); // 执行操作

        curl_close($curl); // 关闭CURL会话
        return $result;
    }

    /*
     * {
       *	"action" : "get",
       *	"application" : "4d7e4ba0-dc4a-11e3-90d5-e1ffbaacdaf5",
       *	"params" : { },
     *	"path" : "/users",
       *	"uri" : "https://a1.easemob.com/easemob-demo/chatdemoui/users/ywuxvxuir6",
       *	"entities" : [ {
     *		"uuid" : "628a88ba-dfce-11e3-8cac-51d3cb69b303",
     *		"type" : "user",
     *		"created" : 1400556326075,
     *		"modified" : 1400556326075,
     *		"username" : "ywuxvxuir6",
     *		"activated" : true
       *	} ],
       *	"timestamp" : 1409574716897,
       *	"duration" : 57,
       *	"organization" : "easemob-demo",
       *	"applicationName" : "chatdemoui"
     *	}
     */
    function getAccount($username)
    {
        $option['client_id'] = CLIENT_ID;
        $option['client_secret'] = CLIENT_SECRET;
        $option['org_name'] = ORG_NAME;
        $option['app_name'] = APP_NAME;
        $eb=new Easemob($option);
        $result=$eb->userDetails($username);
        return json_decode($result,true);
    }

    public function actiongethxname(){
        $username=Frame::getStringFromRequest('user');
        $re=$this->getAccount($username);
        echo json_encode($re);
    }

    function getmygroup($user)
    {
        $group_id = GroupMember::model()->findAll("member_id = {$user->id}");
        if (empty($group_id)) {
            return array();
        }
        $gid = "";
        foreach ($group_id as $val) {
            $gid .= $val->contact_id . ",";
        }
        $gid = trim($gid);
        $gid = trim($gid, ',');
        if ($gid) {
            $sql = "select id,poster,name,description,bulletin,member_id,number,status,created_time,level,huanxin_groupid from groups where id in ({$gid})  and status = 0";
            $connection = Yii::app()->db;
            $command = $connection->createCommand($sql);
            $result1 = $command->queryAll();
            foreach ($result1 as $key => $ginfo) {
                $result1[$key]['poster'] = $ginfo['poster'] ? URL . $ginfo['poster'] : "";
                $result1[$key]['description'] = $ginfo['description'] ? $ginfo['description'] : "";
                $result1[$key]['bulletin'] = $ginfo['bulletin'] ? $ginfo['bulletin'] : "";
            }

            return $result1;
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
            $result ['ret_msg'] = '没有手机号';
        } else {
            $sql = "select phone from benben_invite_log where member_id = " . $user->id . " and phone in (" . implode(",", $phoneArray) . ")";
            $connection = Yii::app()->db;
            $command = $connection->createCommand($sql);
            $result1 = $command->queryAll();
            if (count($result1)) {
                foreach ($result1 as $key => $value) {
                    $searchIndex = array_search($value['phone'], $phoneArray);
                    if ($searchIndex >= 0) {
                        unset($phoneArray[$searchIndex]);
                    }
                }
            }
            if (count($phoneArray) > 0) {
                $insertArray = array();
                foreach ($phoneArray as $key => $value) {
                    $insertArray[] = "(" . $user->id . ", " . $value . ", " . time() . ")";
                }
                $this->addIntegral($user->id, 13, count($insertArray));
                $sql = "insert into benben_invite_log(member_id, phone, created_time) values " . implode(",", $insertArray);
                $command = $connection->createCommand($sql);
                $result1 = $command->execute();
            }
            $result ['ret_num'] = 0;
            $result ['ret_msg'] = '操作成功';

        }
        echo json_encode($result);

    }

    /**
     * 用户拨号
     */
    public function actionMemberDialog()
    {
        $key = Frame::getStringFromRequest('key');
        $phone = Frame::getStringFromRequest('phone');
        $user = $this->check_user();
        $this->addIntegral($user->id, 14);
        $result ['ret_num'] = 0;
        $result ['ret_msg'] = '操作成功';
        echo json_encode($result);
    }

    /**
     * 推荐好友列表
     * 查询推荐好友的信息，即我在对方通讯录中，对方不在我的里面（犇犇号或者手机号）
     * 返回对方信息
     */
    function getRecommendFriends($userid)
    {
        //查出用户手机号
        $member_info = Member::model()->findByPk($userid);
        if (empty($member_info)) {
            return array();
        }
        $phone = $member_info->phone;
        $benben = $member_info->benben_id;
        if (empty($phone)) {
            return array();
        }
        //查出我在哪些人的通讯录中
        $connection = Yii::app()->db;
        $sqlf = "select b.member_id,c.poster,c.phone,c.nick_name,c.huanxin_username,c.benben_id
		from group_contact_info b
		left join group_contact_phone a on a.contact_info_id=b.id
		left join member c on b.member_id=c.id
		where (a.phone='{$phone}' or b.benben_id={$benben}) and c.id>0 and b.member_id!={$userid} group by c.id";
        $command = $connection->createCommand($sqlf);
        $fried_array = $command->queryAll();
        $phone_array = array();
        $benben_array = array();
        if (is_array($fried_array) && count($fried_array) > 0) {
            foreach ($fried_array as $value) {
                if (!empty($value['phone'])) {
                    //用来查询有号码的好友
                    $phone_array[] = "'" . $value['phone'] . "'";
                }
                if (!empty($value['benben_id'])) {
                    //用来查询没号码的好友
                    $benben_array[] = $value['benben_id'];
                }
            }
        }
        //哪些用户已经存在我的通讯录中
        $myfrieds_phone = array();
        $myfrieds_benben = array();
        $sqlwhere = '';
        $flag = 0;
        if (is_array($phone_array) && count($phone_array) > 0) {
            $phone_where = implode(',', $phone_array);
            $sqlwhere .= " a.phone in ($phone_where) ";
            $flag = 1;
        }
        if (is_array($benben_array) && count($benben_array) > 0) {
            if ($flag > 0) {
                $sqlwhere .= ' or ';
            }
            $benben_where = implode(',', $benben_array);
            $sqlwhere .= " a.is_benben in ($benben_where) ";
        }
        if ($flag > 0) {
            $sqlf = "select a.phone,a.is_benben as benben_id from group_contact_phone a
			left join group_contact_info b on a.contact_info_id=b.id
			where ($sqlwhere) and b.member_id={$userid}";
            $command = $connection->createCommand($sqlf);
            $query_result = $command->queryAll();
            if (is_array($query_result) && count($query_result) > 0) {
                foreach ($query_result as $value) {
                    if (!empty($value['phone'])) {
                        //有号码的好友
                        $myfrieds_phone[] = $value['phone'];
                    } else if (!empty($value['benben_id'])) {
                        //没号码的好友
                        $myfrieds_benben[] = $value['benben_id'];
                    }
                }
            }
        }
        //返回不在我通讯录中的用户
        $result = array();
        if (is_array($fried_array) && count($fried_array) > 0) {
            foreach ($fried_array as $value) {
                $pass = 0;
                //检查号码重复
                $phone = $value['phone'];
                if (in_array($phone, $myfrieds_phone)) {
                    $pass = 1;
                }
                //检查奔犇id重复
                $benben = $value['benben_id'];
                if (in_array($benben, $myfrieds_benben)) {
                    $pass = 1;
                }
                if ($pass == 0) {
                    $value["poster"] = empty($value["poster"]) ? @"" : URL . $value["poster"];
                    $result[] = $value;
                }
            }
        }
        return $result;
    }


    /**
     * 推荐好友列表
     */
    function actionRecommendFriends()
    {
        $this->check_key();
        $user = $this->check_user();
        $data = $this->getRecommendFriends($user->id);
        $result['data'] = $data;
        $result['data_count'] = $data ? count($data) : 0;
        $result['ret_num'] = 0;
        $result['ret_msg'] = '操作成功';
        echo json_encode($result);
    }

    /**
     * 添加推荐好友
     * 涉及group_contact_info和group_contact_phone
     */
    function actionAddRecommendFriend()
    {
        $this->check_key();
        $user = $this->check_user();

        $member_id = Frame::getIntFromRequest('member_id');
        $member_info = Member::model()->findByPk($member_id);

        $mybaixing = Bxapply::model()->count("member_id={$user->id} and status=3");
        if ($mybaixing) {
            $hisbaixing = Bxapply::model()->find("member_id=$member_id and status=3");
        } else {
            $hisbaixing = 0;
        }

        if (empty($member_info)) {
            $result['ret_num'] = 400;
            $result['ret_msg'] = '用户不存在';
            echo json_encode($result);
            die();
        }
        //对方是否在自己的通讯录
        $connection = Yii::app()->db;
        $sqlf = "select b.id from group_contact_phone a right join group_contact_info b on a.contact_info_id=b.id where (a.phone='{$member_info->phone}' or b.benben_id={$member_info->benben_id}) and b.member_id={$user->id}";
        $command = $connection->createCommand($sqlf);
        $fried_array = $command->queryAll();
        if (is_array($fried_array) && count($fried_array) > 0) {
            $result['ret_num'] = 410;
            $result['ret_msg'] = '对方已存在自己的通讯录中';
            echo json_encode($result);
            die();
        }
        //查未分组ID
        $own = GroupContact::model()->find("group_name='未分组' and member_id = {$user->id}");
        if ($own) {
            $PinYin = new tpinyin();
            //添加姓名
            $own_result = new GroupContactInfo();
            $own_result->group_id = $own->id;
            $own_result->name = $member_info->nick_name;
            $own_result->pinyin = $PinYin->str2sort($member_info->nick_name);
            $own_result->allpinyin = $PinYin->str2py($member_info->nick_name);
            $own_result->member_id = $user->id;
            $own_result->benben_id = $member_info->benben_id;
            $own_result->created_time = time();
            if ($own_result->save()) {
                $infoid = $own_result->attributes['id'];

                $own_phone = new GroupContactPhone();
                $own_phone->contact_info_id = $infoid;
                $own_phone->is_benben = $member_info->benben_id;
                $own_phone->is_baixing = $hisbaixing ? $hisbaixing['short_phone'] : 0;
                $own_phone->is_active = 0;
                $own_phone->save();


                $result['ret_num'] = 0;
                $result['ret_msg'] = '操作成功';

                $result['id'] = $infoid;
                $result['group_id'] = $own->id;
                $result['pinyin'] = $own_result->pinyin;
                $result['allpinyin'] = $own_result->allpinyin;
                $result['is_benben'] = $member_info->benben_id;
                $result['phone'] = array();
                $result['nick_name'] = $member_info->nick_name;
                $result['name'] = $member_info->name ? $member_info->name : "";
                $result['is_baixing'] = $hisbaixing ? $hisbaixing['short_phone'] : 0;
                $result['huanxin_username'] = $member_info->huanxin_username;
                $result['poster'] = URL . $member_info->poster;
                $result['created_time'] = time();
//				$own_bx=Bxapply::model()->find("status=3 and member_id={$user->id}");
//				if($own_bx){
//					$other_bx=Bxapply::model()->find("status=3 and member_id={$member_id}");
//				}
//				$result['is_baixing']=$other_bx ? $other_bx['short_phone'] : 0;


                echo json_encode($result);
                die();
            } else {
                $result['ret_num'] = 403;
                $result['ret_msg'] = '好友添加失败，请重试';
                echo json_encode($result);
                die();
            }
        } else {
            $result['ret_num'] = 404;
            $result['ret_msg'] = '通讯录错误，未分组不存在';
            echo json_encode($result);
            die();
        }
    }

    /*
     * 我的帐户
     */
    public function actionMyAccount(){
        $this->check_key();
        $user = $this->check_user();
        //商城等级
        $traininfo=NumberTrain::model()->find("member_id={$user['id']}");
        if($traininfo){
            $score=$traininfo['score'];
            $rank=$this->score2rank($score);
            //好评率
            $connection=Yii::app()->db;
            $sql12 = "select a.comment_rank from store_comment as a left join promotion as b on a.promotion_id=b.id
            left join promotion_manage as c on b.pm_id=c.id where c.store_id={$traininfo['id']} and a.parent_id=0 and a.is_seller=0";
            $command = $connection->createCommand($sql12);
            $result12 = $command->queryAll();
            $good=0;
            foreach($result12 as $kr=>$vr){
                if($vr['comment_rank']==3){
                    $good++;
                }
            }
            $mean_rate=count($result12) ? (number_format($good/count($result12),4,".","")*100)."%":"100%";
        }
        //账户余额
        $fee=$user['fee'];
        //我的订单（未发货，未收货）
        $order_num=StoreOrderInfo::model()->count("(shipping_status=0 or shipping_status=1) and member_id={$user['id']}");
        //我的收藏
        $collect_num=CollectGoods::model()->count("member_id={$user['id']}");
        $result['ret_num'] = 0;
        $result['ret_msg'] = '操作成功';
        $result['rank'] = $rank ? $rank:0;
        $result['mean_rate'] = $mean_rate?$mean_rate:"100%";
        $result['fee'] = $fee?$fee:0;
        $result['order_num'] = $order_num?$order_num:0;
        $result['collect_num'] = $collect_num?$collect_num:0;
        echo json_encode($result);
    }

    /*
     * 我的账单
     * pay_log
     */
    public function actionMyPayLog(){
        $this->check_key();
        $user = $this->check_user();
        $connection=Yii::app()->db;
        $sql="select a.*,c.goods_name,b.pay_time from pay_log as a left join store_order_info as b on a.order_id=b.order_id left join store_order_goods as c on c.order_id=a.order_id
         where b.member_id={$user['id']} and a.is_paid=1 order by log_id Desc";
        $command = $connection->createCommand($sql);
        $result1 = $command->queryAll();
        foreach($result1 as $k=>$v){
            $info[]=array(
                "time"=>$v['pay_time']?$v['pay_time']:0,
                "content"=>$v['goods_name']?$v['goods_name']:"",
                "fee"=>$v['order_amount']?$v['order_amount']:"",
                "order_type"=>$v['order_type']?$v['order_type']:0,
            );
        }
        $result['ret_num'] = 0;
        $result['ret_msg'] = '操作成功';
        $result['info'] = $info?$info:array();
        echo json_encode($result);
    }


}