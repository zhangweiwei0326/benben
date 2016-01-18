<?php
class SystemController extends BaseController {
	public $layout = '//layouts/home';
	 public $menuIndex = 41;
	public function actionIndex() {
		$cCriteria = new CDbCriteria ();
		$cCriteria->select = 't.id,t.username,t.role,t.created_time';
		$cCriteria->order = 't.created_time desc';
		
		$count = User::model ()->count ( $cCriteria );
		$page = new CPagination ( $count );
		$page->pageSize = 15;
		$page->applyLimit ( $cCriteria );
		$total = ceil ( $count / ($page->pageSize) );
		
		$user = User::model ()->findAll ( $cCriteria );
		
		$this->render ( 'index', array (
				'user' => $user,
				'page' => $page,
				'total' => $total 
		) );
	}
	public function actionCreateUser() {
		$param = array ();
		$msg = '';
		if ((Yii::app ()->request->isPostRequest)) {
			$param ['username'] = Frame::getStringFromRequest ( 'username' );
			$param ['role'] = Frame::getIntFromRequest ( 'role' );
			$param ['password'] = Frame::getStringFromRequest ( 'password' );
			$param ['password2'] = Frame::getStringFromRequest ( 'password2' );
			$user = new User ();
			
			$nowtime = time ();
			$error = 0;
			$user->created_time = $nowtime;
			$user->role = $param ['role'];
			if (empty ( $user->role )) {
				$msg = '用户角色没有选择！';
				$error = 1;
			}
			
			$user->password = $param ['password'];
			if (empty ( $user->password ) || ($user->password) != ($param ['password2'])) {
				$msg = '密码不能为空且不能低于6位，两次输入密码必须一致！';
				$error = 1;
			} elseif ((strlen ( $user->password )) < 6) {
				$msg = '密码不能低于6位！';
				$error = 1;
			}
			$user->username = $param ['username'];
			if (empty ( $user->username )) {
				$msg = '用户名不能为空！';
				$error = 1;
			}
			if (empty ( $error )) {
				if ($user->validate ()) {
					$user->password = md5 ( $user->password );
					$user->save ();
					$this->redirect ( array (
							'/system/index' 
					) );
				} else {
					// var_dump ( $user->getErrors () );
					$msg = '保存错误!';
				}
			}
		}
		$this->render ( 'createUser', array (
				'param' => $param,
				'msg' => $msg 
		) );
	}
	public function actionUpdateUser() {
		$param = array ();
		$msg = '';
		$id = Frame::getIntFromRequest ( 'id' );
		$user = User::model ()->findByPk ( $id );
		if ((Yii::app ()->request->isPostRequest) && ! empty ( $user )) {
			$nowtime = time ();
			$error = 0;
			$param ['password'] = Frame::getStringFromRequest ( 'password' );
			$param ['password2'] = Frame::getStringFromRequest ( 'password2' );
			$user->created_time = $nowtime;
			$user->username = Frame::getStringFromRequest ( 'username' );
			if (empty ( $user->username )) {
				$msg = '用户名不能为空！';
				$error = 1;
			}
			if (! empty ( $param ['password'] ) || ! empty ( $param ['password2'] )) {
				$user->password = md5 ( $param ['password'] );
				if (($user->password) != md5 ( ($param ['password2']) )) {
					$msg = '如果重新设置密码则两次输入密码必须一致且不低于6位！';
					$error = 1;
				} elseif ((strlen ( $user->password )) < 6) {
					$msg = '密码不能低于6位！';
					$error = 1;
				}
			}
				
			$user->role = Frame::getIntFromRequest ( 'role' );
			if (empty ( $user->role )) {
				$msg = '用户角色没选择！';
				$error = 1;
			}
				if (empty ( $error )) {
					if ($user->validate ()) {
						$user->update ();
						$this->redirect ( array (
								'/system/index'
						) );
					} else {
// 						var_dump ( $user->getErrors () );
						$msg = '保存错误!';
					}
				}
			}
			$this->render ( 'updateUser', array (
					'user' => $user,
					'msg' => $msg,
					'param' => $param
			) );
		}
		public function actionDeleteUser() {
			$user = array ();
			$id = Frame::getIntFromRequest ( 'id' );
			if ($id > 0) {
				$user = User::model ()->findByPk ( $id );
			}
			$user->delete ();
			$this->redirect ( array (
					'/system/index'
			) );
		}
}