<?php
class EnterpriseSiteController extends Controller {
	public $layout='//layouts/enterpriseSiteLayout';
	public $email;
	/**
	 * Declares class-based actions.
	 */
	public function actions() {
		return array (
				// captcha action renders the CAPTCHA image displayed on the contact page
				'captcha' => array (
						'class' => 'CCaptchaAction',
						'backColor' => 0xE8E8E8,
						'padding' => 0,
						'height' => 40,
						'width' => 84,
						'maxLength' => 4,
						'minLength' => 4,
						'testLimit' => 999 
				),
				// page action renders "static" pages stored under 'protected/views/site/pages'
				// They can be accessed via: index.php?r=site/page&view=FileName
				'page' => array (
						'class' => 'CViewAction' 
				) 
		);
	}
	
	/**
	 * 注册
	 */
	public function actionRegister() {
		// 判断是否登录
		if (Yii::app ()->user->getState ( "Enterprise_memberInfo" )) {
			$this->redirect ( array (
					"/enterpriseIndex/index" 
			) );
			die ();
		}
		
		if (Yii::app ()->request->isAjaxRequest) {
			$data = $_POST ['data'];
			$apply_type = intval ( $data ['apply_type'] ); // 申请类型：1.个人，2.企业/组织，3.学校
			$name = htmlspecialchars ( trim ( $data ['name'] ) ); // 申请名称(姓名/企业/组织全称/学校全称)
			$phone = intval ( $data ['phone'] ); // 手机号码
			$identity_num = htmlspecialchars ( trim ( $data ['identity_num'] ) ); // 身份证号码/营业执照注册号/组织机构代码/办学许可证代码
			$identity_attachment = htmlspecialchars ( trim ( $data ['identity_attachment'] ) ); // (身份证号码/营业执照注册号/组织机构代码/办学许可证代码)附件
			$identity_attachment_more = htmlspecialchars ( trim ( $data ['identity_attachment_more'] ) ); // 个人申请:身份证反面信息
			
			$enterprise_name = htmlspecialchars ( trim ( $data ['enterprise_name'] ) ); // 政企通讯录名称
			$enterprise_type = intval ( $data ['enterprise_type'] ); // 政企通讯录类型:1.企业政企,2.虚拟网政企
			$login_name = htmlspecialchars ( trim ( $data ['login_name'] ) ); // 登录名
			$login_password = trim ( $data ['login_password'] ); // 登录密码
			$login_password_confirm = trim ( $data ['login_password_confirm'] ); // 确认密码
			$email = htmlspecialchars ( trim ( $data ['email'] ) ); // 邮箱
			$verify = trim ( $data ['verify'] ); // 验证码
			                                 
			// 判空
			if (($data ['apply_type'] == 1) && (empty ( $data ['identity_attachment_more'] ))) {
				echo json_encode ( array (
						'msg' => 'messageEmpty' 
				) );
				die ();
			}
			unset ( $data ['apply_type'] );
			unset ( $data ['identity_attachment_more'] );
			foreach ( $data as $v ) {
				if (empty ( $v )) {
					echo json_encode ( array (
							'msg' => 'messageEmpty' 
					) );
					die ();
				}
			}
			
			// 判断 身份证号码/营业执照注册号/组织机构代码/办学许可证代码 是否已注册过
			$identity_num = addslashes ( $identity_num );
			$enterprise = ApplyRegister::model ()->find ( " identity_num='" . $identity_num . "' " );
			if (! empty ( $enterprise )) {
				switch ($enterprise->status) {
					case 0 :echo json_encode ( array ('msg' => 'registered','status' => '0' ) );break;
					case 1 :echo json_encode ( array ('msg' => 'registered','status' => '1' ) );break;
					case 2 :echo json_encode ( array ('msg' => 'registered','status' => '2' ) );break;
				}
				die ();
			}
			//验证邮箱是否注册过
			$email = addslashes ( $email );
			$enterprise = ApplyRegister::model ()->find ( " email='" . $email . "' " );
			if(!empty($enterprise)){
				echo json_encode(array('msg'=>'registered_email'));die();
			}
			//验证登录名是否存在
			$login_name = addslashes ( $login_name );
			$enterprise = ApplyRegister::model ()->find ( " login_name='" . $login_name . "' " );
			if(!empty($enterprise)){
				echo json_encode(array('msg'=>'registered_login_name'));die();
			}
			// 验证验证码
			$code = $this->createAction ( 'captcha' )->getVerifyCode ();
			if (strtolower ( $verify ) != $code) {
				echo json_encode ( array (
						'msg' => 'verifyError' 
				) );
				die ();
			}
			
			// 注册新公司
			$enterprise = new ApplyRegister ();
			$enterprise->name = $name;
			$enterprise->phone = $phone;
			$enterprise->identity_num = $identity_num;
			$enterprise->identity_attachment = $identity_attachment;
			if ($apply_type == 1) {
				$enterprise->identity_attachment_more = $identity_attachment_more;
			}
			$enterprise->enterprise_name = $enterprise_name;
			$enterprise->enterprise_type = $enterprise_type;
			$enterprise->login_name = $login_name;
			$enterprise->login_password = md5 ( $login_password );
			$enterprise->email = $email;
			$enterprise->apply_type = $apply_type;
			$enterprise->status = 0;
			$enterprise->created_time = time ();
			if ($enterprise->save ()) { // 申请成功
					$identity = new EnterpriseUserIdentity ( $email, $login_password );
					if ($identity->authenticate ()) { // 登录成功
						$duration = 3600 * 24; // 30 days
						Yii::app ()->user->login ( $identity, $duration );
						// $id=Yii::app ()->user->getState ( "Enterprise_memberInfo" )->id;
						echo json_encode ( array (
								'msg' => 'success' 
						) );
						die ();
					}
			} else {
				echo json_encode ( array (
						'msg' => 'failed' 
				) );
				die ();
			}
		}
		$this->render ( "apply" );
	}
	
	/**
	 * Displays the login page
	 */
	public function actionLogin() {
		$lastUrl = Yii::app ()->request->urlReferrer;
		// 判断是否登录
		if (Yii::app ()->user->getState ( "Enterprise_memberInfo" )) {
			$this->redirect ( array ("/enterpriseIndex/index" ) );die ();
		} else {
			if (Yii::app ()->request->isAjaxRequest) {
				$username = trim ( $_POST ['username'] );
				$password = trim ( $_POST ['password'] );
				$verify = trim ( $_POST ['verify'] );
				$enterprise_id=intval($_POST['enterprise_id']);
				// 验证验证码
				$code = $this->createAction ( 'captcha' )->getVerifyCode ();
				if (strtolower ( $verify ) != $code) {
					echo json_encode ( array ('msg' => 'verifyError' ) );die ();
				}
				//判断登录人员角色，超级管理员、管理员
				$duration = 3600*24; // 30 days
				if(!preg_match("/^\d+$/", $username)){		//超级管理员登录
					$identity = new EnterpriseUserIdentity ( $username, $password );
					if ($identity->authenticate ()) { // 登录成功
						Yii::app ()->user->login ( $identity, $duration );
						echo json_encode ( array ("msg" => "success","lastUrl" => $lastUrl) );die ();
					} else {
						echo json_encode ( array ("msg" => "failed","lastUrl" => $lastUrl) );die ();
					}
				}else{		//普通管理员登录
					$pwd = addslashes(md5 ( $password ));
					$user = addslashes($username);
					$member=Member::model()->find("(benben_id='".$user."')and(password='".$pwd."')");
					if(empty($member)){		//用户名或密码错误
						echo json_encode ( array ("msg" => "failed","lastUrl" => $lastUrl) );die ();
					}else{
						//寻找该会员是哪些政企通讯录的管理员
						$enterpriseMember=EnterpriseMemberNew::model();
						$cri = new CDbCriteria();
						$cri->join="left join enterprise_member_manage a on t.id=a.member_id";
						$cri->select="t.*,a.is_manage";
						$cri->condition="(t.member_id='".$member->id."')and(a.is_manage='1')";
						$enterprise=$enterpriseMember->findAll($cri);
						if(empty($enterprise)){		//该会员不是任何政企通讯录的管理员
							echo json_encode ( array ("msg" => "failed","lastUrl" => $lastUrl) );die ();
						}else{
							if(!empty($enterprise_id)){
								$identity=new EnterpriseAdministratorIdentity($username, $password,$enterprise_id);
								if($identity->authenticate()){		//普通管理员登录成功
									Yii::app ()->user->login ( $identity, $duration );
									echo json_encode ( array ("msg" => "success","lastUrl" => $lastUrl) );die ();
								}else{
									echo json_encode ( array ("msg" => "failed","lastUrl" => $lastUrl) );die ();
								}
							}
							if(count($enterprise)>1){		//该会员是多个政企通讯录的管理员
								$html='';
								foreach ($enterprise as $k=>$v){
									$id=$v->contact_id;
									$name=ApplyRegister::model()->find("enterprise_id='".$v->contact_id."'")->name;
									if($k==0){
										$html.='<li class="com-la-clk"><input name="enterprise_id" type="hidden"  value="'.$id.'"/>'.$name.'</li>';
									}else{
										$html.='<li><input name="enterprise_id" type="hidden"  value="'.$id.'"/>'.$name.'</li>';
									}
									
								}
// 								var_dump($id);var_dump($name);die;
								echo json_encode ( array ("msg" => "chioces","lastUrl" => $lastUrl,'html'=>$html,) );die ();
							}else{		//该会员仅仅是一家政企通讯录的管理员
								$enterprise_id=$enterprise[0]->contact_id;		//政企通讯录id
								$identity=new EnterpriseAdministratorIdentity($username, $password,$enterprise_id);
								if($identity->authenticate()){		//普通管理员登录成功
									Yii::app ()->user->login ( $identity, $duration );
									echo json_encode ( array ("msg" => "success","lastUrl" => $lastUrl) );die ();
								}else{
									echo json_encode ( array ("msg" => "failed","lastUrl" => $lastUrl) );die ();
								}
							}
						}
					}
				}
			}
			$this->render ( 'lad' );
		}
	}
	/**
	 * 找回密码
	 */
	public function actionRetrieve() {
		if (Yii::app ()->request->isAjaxRequest) {
			$email = trim ( Frame::getStringFromRequest ( "email" ) );
			$verify = trim ( Frame::getStringFromRequest ( "verify" ) );
			$regEmail = "/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/";
			if (! preg_match ( $regEmail, $email )) {
				echo json_encode ( array (
						'msg' => 'emailError' 
				) );
				die ();
				// $this->render("re-pw",array('msg'=>'emailError','email'=>$email,'verify'=>$verify,));die();
			}
			// 验证验证码
			$code = $this->createAction ( 'captcha' )->getVerifyCode ();
			if (strtolower ( $verify ) != $code) {
				echo json_encode ( array (
						'msg' => 'verifyError' 
				) );
				die ();
				// $this->render("re-pw",array('msg'=>'verifyError','email'=>$email,'verify'=>$verify,));die();
			}
			// 验证邮箱是否已注册
			$enterprise = ApplyRegister::model ()->find ( "email='" . $email . "'" )->email;
			if (empty ( $enterprise )) {
				echo json_encode ( array (
						'msg' => 'emailUnregister' 
				) );
				die ();
				// $this->render("re-pw",array('msg'=>'emailUnregister','email'=>$email,'verify'=>$verify,));die();
			}
			
			// 生成token
			$token = md5 ( $email . 'benben'.uniqid() );
			// 设置session
			Yii::app ()->session [$email] = array (
					'token' => $token,
					'time' => time () 
			);
			
			$mailer = Yii::createComponent ( 'application.extension.mailer.EMailer' );
			$mailer->IsSMTP ();
			$mailer->CharSet = 'UTF-8'; // 设置邮件的字符编码，这很重要，不然中文乱码
			$mailer->SMTPAuth = true; // 开启认证
			$mailer->Port = 25;
			$mailer->Host = "smtp.163.com";
			$mailer->Username = "fly19920923@163.com";
			$mailer->Password = "zhangyanfei";
			$mailer->AddReplyTo ( "fly19920923@163.com", "zhangyanfei" ); // 回复地址
			$mailer->From = "fly19920923@163.com";
			$mailer->FromName = "奔犇";
			$mailer->AddAddress ( $email );
			$mailer->Subject = "奔犇";
			$mailer->Body = "<div style='width:70%;margin:40px auto;padding:30px;background:#F7F6F2;'>" . "<div style='width:100%;text-ailgn:center;font-size:30px;margin:20px 0;color:#000;'><img src='http://aimovie.xun-ao.com/themes/home/images/zx_logo.png' width='80' style='margin:10px 0  0 10px;display:block;float:left;'/>(找回密码)</div><hr>" . "<div style='width:100%;color:#FD6013;margin-top:50px;font-size:17px;'>" . "您正在使用奔犇找回密码功能，以下为找回密码的验证码(如非本人操作请忽略):" . "<br>" . $token;
			$mailer->WordWrap = 80; // 设置每行字符串的长度
			$mailer->IsHTML ( true );
			
			if ($mailer->Send ()) {
				echo json_encode ( array (
						'msg' => 'success',
						'email' => $email 
				) );
				die ();
				// $this->email=$email;
				// $this->redirect(Yii::app()->createUrl("enterpriseSite/reset"));die();
			} else {
				echo json_encode ( array (
						'msg' => 'fail' 
				) );
				die ();
				// $this->render("re-pw",array('msg'=>'fail','email'=>$email,'verify'=>$verify,));die();
			}
		}
		$email = Frame::getStringFromRequest ( "email" );
		$this->render ( "re-pw", array (
				'email' => $email,
		) );
	}
	/**
	 * 重置密码
	 */
	public function actionReset() {
		if (Yii::app ()->request->isAjaxRequest) {
			$email = trim ( Frame::getStringFromRequest ( "email" ) );
			$verify = trim ( Frame::getStringFromRequest ( "verify" ) );
			$password = trim ( Frame::getStringFromRequest ( "password" ) );
			$repassword = trim ( Frame::getStringFromRequest ( "repassword" ) );
			
			$session_token = Yii::app ()->session [$email];
			if (($session_token ['token'] != $verify) || (($session_token ['time'] + 1800) < time ())) {
				echo json_encode ( array (
						'msg' => 'tokenError' 
				) );
				die ();
			}
			if ($password != $repassword) {
				echo json_encode ( array (
						'msg' => 'passwordError' 
				) );
				die ();
			}
			// 重置密码
			$enterprise = ApplyRegister::model ()->find ( "email='" . $email . "'" );
			$model = ApplyRegister::model ()->findByPk ( $enterprise->id );
			$model->login_password = md5 ( $password );
			if ($model->save ()) {
				echo json_encode ( array (
						'msg' => 'success' 
				) );
				die ();
			} else {
				echo json_encode ( array (
						'msg' => 'failed' 
				) );
				die ();
			}
		}
		
		$email = Frame::getStringFromRequest ( "email" );
		$code_email=explode("@", $email);
		$first=substr($code_email[0], 0,2);
		$second=substr($code_email[0], -2,2);
		$code_email[0]=$first.'******'.$second;
		$code_email=implode("@", $code_email);
		$this->render ( "re-pw-2", array (
				'email' => $email,
				'code_email'=>$code_email, 
		) );
	}
	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout() {
		Yii::app ()->user->logout ();
		$this->redirect ( array (
				"/enterpriseSite/login" 
		) );
	}
	
	// 上传图片
	public function actionUpload() {
		$base64 = $_POST ['formFile'];
		$IMG = base64_decode ( $base64 );
		$is_head = $_POST ['head'];
		if ($is_head) {
			$str = 'head/';
		} else {
			$str = 'images/' . date ( 'Y-m', time () ) . '/';
		}
		if ($_POST ['name']) {
			$exe = explode ( '.', $_POST ['name'] );
			$fn = $this->generateNonceStr () . '.' . end ( $exe );
		} else {
			$fn = false;
		}
		// $fn = (isset($_POST['name']) ? $_POST['name'] : false);
		$file = 'uploads/' . $str . $fn;
		if (! is_dir ( 'uploads/' . $str )) {
			mkdir ( 'uploads/' . $str, 0777, true );
		}
		file_put_contents ( $file, $IMG );
		echo '/' . $file;
	}
	protected function generateNonceStr($length = 16) {
		// 密码字符集，可任意添加你需要的字符
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$str = "";
		for($i = 0; $i < $length; $i ++) {
			$str .= $chars [mt_rand ( 0, strlen ( $chars ) - 1 )];
		}
		return $str;
	}
}