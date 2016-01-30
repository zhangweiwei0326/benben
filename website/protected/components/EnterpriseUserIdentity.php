<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class EnterpriseUserIdentity extends CUserIdentity {
	/**
	 * Authenticates a user.
	 * The example implementation makes sure if the username and password
	 * are both 'demo'.
	 * In practical applications, this should be changed to authenticate
	 * against some persistent user identity storage (e.g. database).
	 * 
	 * @return boolean whether authentication succeeds.
	 */
// 	public $benben_id;
// 	public $login_name;
//     public $enterprise_id;
	// public $username;
	// public $email;
	public function authenticate() {
	
		$pwd = addslashes(md5 ( $this->password ));
		$username = addslashes($this->username);
		$loginByName = ApplyRegister::model ()->find ( "login_name = '{$username}' and login_password = '$pwd'" );
		if (empty ( $loginByName )) {
			$loginByEmail = ApplyRegister::model ()->find ( "email = '{$username}' and login_password = '$pwd'" );
			if(!empty($loginByEmail)){
// 				$this->benben_id = $loginById->benben_id;
// 				$this->nick_name = $loginById->nick_name;
//                 $this->member_id = $loginById->id;
				$this->setState ( 'Enterprise_memberInfo', $loginByEmail ); 
				return true;
			}else{
				return false;
			}
		} else {
// 			$this->benben_id = $loginByName->benben_id;
// 			$this->nick_name = $loginByName->nick_name;
//             $this->member_id = $loginByName->id;
			$this->setState ( 'Enterprise_memberInfo', $loginByName );
			return true;
		}
	}
}