<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity {
	/**
	 * Authenticates a user.
	 * The example implementation makes sure if the username and password
	 * are both 'demo'.
	 * In practical applications, this should be changed to authenticate
	 * against some persistent user identity storage (e.g. database).
	 * 
	 * @return boolean whether authentication succeeds.
	 */
	public $userid;
	public $role;
	public $username;
	public $disable;
	public function authenticate() {
		$pwd = md5 ( $this->password );
		$user = User::model ()->find ( "username = '{$this->username}' and password = '$pwd'" );
		if (empty ( $user )) {
			return false;
		} else {
			$model=User::model()->findBySql("select id,username,role,disable from user where username = '".$this->username."' and password = '$pwd'");			
			$model->last_login = time();
			$en = $model->id.$model->last_login;
			$model->login_id = md5($en);
			$model->save();
			
			$this->userid = $user->id;
			$this->role = $user->role;
			$this->username = $user->username;
			$this->disable = $model->disable;
			$this->setState ( 'userInfo', $model );
			return true;
		}
	}
}