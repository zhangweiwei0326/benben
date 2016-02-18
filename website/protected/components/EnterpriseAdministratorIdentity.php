<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class EnterpriseAdministratorIdentity extends CUserIdentity {
	/**
	 * Authenticates a user.
	 * The example implementation makes sure if the username and password
	 * are both 'demo'.
	 * In practical applications, this should be changed to authenticate
	 * against some persistent user identity storage (e.g. database).
	 * 
	 * @return boolean whether authentication succeeds.
	 */
	public $enterprise_id;
	
	public function __construct($username, $password,$enterprise_id){
		parent::__construct($username, $password);
		$this->enterprise_id=$enterprise_id;
	}
	
	public function authenticate() {
		$pwd = addslashes(md5 ( $this->password ));
		$username = addslashes($this->username);
		$member = Member::model ()->find ( "benben_id = '{$username}' and password = '$pwd'" );
		if (empty ( $member )) {
			return false;
		} else {
			$apply=ApplyRegister::model()->find("(enterprise_id='".$this->enterprise_id."')");
			if(empty($apply)){
				return false;
			}else{
				$this->setState ( 'Enterprise_administrator', $member );
				$this->setState ( 'Enterprise_memberInfo', $apply );
				return true;
			}
		}
	}
	
		
	
}