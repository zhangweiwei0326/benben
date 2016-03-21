<?php
class LeftWidge extends CWidget {
	public $index = 10;
	function init() {
		// 此方法会被 CController::beginWidget() 调用
	}
	function run() {
		// 此方法会被 CController::endWidget() 调用
		// 加载主菜单
		$this->loadMenu ();
	}

	// 加载主菜单
	function loadMenu() {
		$menu = getMenu ();
		$subMenu = getSubMenu ();
		//	var_dump($subMenu);
		$role = new Role();
		$user_id = Yii::app ()->user->getState('userInfo')->id;
		$user = new User();
		$sql = "select role from user where id = ".$user_id;
		$user_role = $user->findBySql($sql);


		$sql = "SELECT * FROM role where id = ".$user_role->role;
		$roles = $role->findBySql($sql);
		$domember = $roles->domember;
		$dobaixing = $roles->dobaixing;
		$doenterprise = $roles->doenterprise;
		$doapplyregister = $roles->doapplyregister;     //申请审核
		$dogroup = $roles->dogroup;
		$doshop = $roles->doshop;
		$dolottery = $roles->dolottery;
		$dostore = $roles->dostore;
		$docreation = $roles->docreation;
		$dorelease = $roles->dorelease;
		$dofriend = $roles->dofriend;
		$dohappy = $roles->dohappy;
		$dosystem = $roles->dosystem;
		$dowebsite = $roles->dowebsite;
		$donews = $roles->donews;
		$doleague = $roles->doleague;
		$dofind = $roles->dofind;
		$doother = $roles->doother;
		$dopay = $roles->dopay;
		$doservice = $roles->doservice;
		$role_arr = array(
			"domember"=>$domember,
			"dobaixing"=>$dobaixing,
			"doenterprise"=>$doenterprise,
			"doapplyregister" =>$doapplyregister,       //申请审核
			"dogroup"=>$dogroup,
			"dostore"=>$dostore,
			"doshop"=>$doshop,
			"dolottery"=>$dolottery,
			"docreation"=>$docreation,
			"dorelease"=>$dorelease,
			"dofriend"=>$dofriend,
			"dohappy"=>$dohappy,
			"dosystem"=>$dosystem,
			"dowebsite"=>$dowebsite,
			"donews"=>$donews,
			"doleague"=>$doleague,
			"dofind"=>$dofind,
			"doother"=>$doother,
			"dopay"=>$dopay,
			"doservice"=>$doservice
		);
		session_start();
		session_regenerate_id(true);
		Yii::app()->session['role_arr']=json_encode($role_arr);
		//会员管理
		if($domember){
			$menu['1']['role'] = 1;
			if($domember & 3) $subMenu['1']['1']['role'] =1;
			if($domember & 4) $subMenu['1']['2']['role'] =1;
		}
		//百姓网
		if($dobaixing){
			$menu['2']['role'] = 1;
			if($dobaixing & 32) $subMenu['2']['1']['role'] =1;
			if($dobaixing & 4) $subMenu['2']['2']['role'] =1;
			if($dobaixing & 8) $subMenu['2']['3']['role'] =1;
			$subMenu['2']['4']['role'] =1;
			if($dobaixing & 16) $subMenu['2']['5']['role'] =1;
			if($dobaixing & 64) $subMenu['2']['6']['role'] =1;
		}

		//系统管理
		if($dosystem){
			$menu['10']['role'] = 1;
			if($dosystem & 1) $subMenu['10']['1']['role'] = 1;
			if($dosystem & 2) $subMenu['10']['2']['role'] = 1;
			if($dosystem & 4) $subMenu['10']['3']['role'] = 1;
			if($dosystem & 8) $subMenu['10']['4']['role'] = 1;
		}else{
			$menu['10']['role'] = 1;
			$subMenu['10']['3']['role'] = 1;
		}

		//其它网站管理
		if($doother){
			if($doother & 1) $subMenu['9']['1']['role'] = 1;
			if($doother & 2) $subMenu['9']['2']['role'] = 1;
			if($doother & 4) $subMenu['9']['3']['role'] = 1;
			if($doother & 8) $subMenu['9']['4']['role'] = 1;
			if($doother & 16) $subMenu['9']['5']['role'] = 1;
			$menu['9']['role'] = 1;
			//if($doother & 3) $subMenu['6']['1']['role'] = 1;
		}

		//消息管理
		if($donews){
			$menu['6']['role'] = 1;
			if($donews & 1) $subMenu['6']['1']['role'] = 1;
			if($donews & 2) $subMenu['6']['2']['role'] = 1;
			if($donews & 4) $subMenu['6']['3']['role'] = 1;
		}

		//通讯录管理
		if($doenterprise || $dogroup || $dostore){
			$menu['3']['role'] = 1;
			if($dostore){
				$menu['3']['uri'] = '/numberTrain';
				$subMenu['3']['3']['role'] =1;
			}
			if($dogroup){
				$menu['3']['uri'] = '/groups';
				$subMenu['3']['2']['role'] =1;
			}
			if($doenterprise){
				$subMenu['3']['1']['role'] =1;
				$menu['3']['uri'] = '/enterprise';
			}
		}
		//发现管理
		if($docreation || $dorelease || $dofriend  || $dohappy || $dofind){
			$menu['4']['role'] = 1;
			if($dohappy){
				$menu['4']['uri'] = '/happy';
				$subMenu['4']['4']['role'] =1;
			}
			if($dofriend){
				$menu['4']['uri'] = '/friend';
				$subMenu['4']['3']['role'] =1;
			}
			if($dorelease){
				$subMenu['4']['2']['role'] =1;
				$menu['4']['uri'] = '/buy';
			}
			if($docreation){
				$subMenu['4']['1']['role'] =1;
				$menu['4']['uri'] = '/creation';
			}
			if($dofind){
				$subMenu['4']['5']['role'] =1;
			}

		}
		if($doleague){
			$menu['5']['role'] = 1;
			$subMenu['5']['1']['role'] = 1;
		}
		if($dopay){
			$menu['8']['role'] = 1;
			$subMenu['8']['1']['role'] = 1;
		}
		if($doservice){
			$menu['7']['role'] = 1;
			$subMenu['7']['1']['role'] = 1;
			$subMenu['7']['2']['role'] = 1;
		}
		//商家管理
		if($doshop){
			$menu['11']['role'] = 1;
			$subMenu['11']['1']['role'] =1;
			$subMenu['11']['2']['role'] =1;
			$subMenu['11']['3']['role'] =1;
		}
		//抽奖管理
		if($dolottery){
			$menu['12']['role'] = 1;
			$subMenu['12']['1']['role'] =1;
			$subMenu['12']['2']['role'] =1;
		}
		$this->render ( 'leftView', array (
			"index" => $this->index,
			"menu" => $menu,
			"subMenu" => $subMenu
		) );
	}
}