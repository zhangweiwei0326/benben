<?php
class BaseController extends Controller {
	
	public $menuIndex;
	public $isRoles = array("member" => "domember", "bxapply" => "dobaixing", "topAuction"=>"doshop","backOrder"=>"doshop","storeAuth"=>"doshop",
												"enterprise" => "doenterprise", 'groups' => "dogroup",
												"numberTrain" => "dostore", "creation" => "docreation",
												"buy" => "dorelease", "friend" => "dofriend", "happy" => "dohappy", "findstatistic" => "dohappy",
												"user" => "dosystem", "role" => "dosystem","loginLog" => "dosystem","friendDisable" => "dofriend","friendComment"=> "dofriend",
												"memberDisable" =>"domember", "bxapplyRecord" => "dobaixing",
												"groupDisable" => "dogroup", "groupMember"=>"dogroup",
												"numberDisable" => "dostore","protocol" => "doother",
												'splash' => 'doother',"quote"=>"dorelease",
												'leagueMember'=>'dofriend','findstatistic'=>'dofind',
												"industry" => "doother","version" => "doother",'complain'=>'doother',
													"enterpriseDisable" =>'doenterprise', 'enterpriseMember' => 'doenterprise',
												"buyDisable" => "dorelease", "creationDisable"  => "docreation", "creationComment" => "docreation", "notice" => "donews", "friendLeague" => "doleague");
	

	protected function beforeAction($action) {	
		if(Yii::app()->controller->id != "site"){
			if(!$this->getLoginId()){
				$this->redirect(Yii::app()->createUrl("site/index"));
				return;
			}
		}
		$array = array('index', 'site', 'password');
		if(!in_array(Yii::app()->controller->id, $array)){
			$this->isRoles[Yii::app()->controller->id];
			$role = $this->getRole($this->isRoles[Yii::app()->controller->id]);	
			if(!$role){
				echo 'system error!';
				return false;
			}		
		}
		
		if (Yii::app ()->user->isGuest && !in_array($action->id, array('login','test'))) {
			// 游客
// 			$this->redirect ( array ('/' ) );
			return true;
		} else {
			// 更新用户信息
			if (isset($userid )){
			
			$userid = Yii::app ()->user->getState ( "userInfo" )->id;
			$userData = User::model ()->findByPk ( $userid );
			}
			if (! empty ( $userData )) {
				Yii::app ()->user->setState ( "userInfo", $userData );
				
			}
			return true;
		}
	}
	
	public function getLoginId(){
		return Yii::app ()->user->getState('userInfo')->id;
	}
	
	public function getProvince(){
		$area = new Area();
		$sql = "SELECT bid ,area_name FROM area WHERE level = 1 AND last = 0 AND parent_bid = 0 ORDER BY bid ASC";
		$province = $area->findAllBySql($sql);
		return $province;
	}
	
	public function getCity($parent_bid = 0){
		$area = new Area();
		$sqlc = "SELECT bid , parent_bid , area_name FROM area WHERE level = 2";
		if ($parent_bid > 0) {
			$sqlc .= " and parent_bid = ".$parent_bid;
		}
		$city = $area->findAllBySql($sqlc);
		$res = array();
		foreach ($city as $c){
			$temp  = array("bid" => $c->bid, "parent_bid" => $c->parent_bid, "area_name" => $c->area_name);
			$res[] = $temp;
		}
		return $res;
	}
	
	public function getArea($parent_bid = 0){
		$area = new Area();
		$sqlc = "SELECT bid , parent_bid , area_name FROM area WHERE level = 3";
		if ($parent_bid > 0) {
			$sqlc .= " and parent_bid = ".$parent_bid;
		}
		$ares = $area->findAllBySql($sqlc);
		$res = array();
		foreach ($ares as $c){
			$temp  = array("bid" => $c->bid, "parent_bid" => $c->parent_bid, "area_name" => $c->area_name);
			$res[] = $temp;
		}
		return $res;
	}
	
	public function getStreet($parent_bid = 0){
		$area = new Area();
		$sqlc = "SELECT bid , parent_bid , area_name FROM area WHERE level = 4";
		if ($parent_bid > 0) {
			$sqlc .= " and parent_bid = ".$parent_bid;
		}
		$ares = $area->findAllBySql($sqlc);
		$res = array();
		foreach ($ares as $c){
			$temp  = array("bid" => $c->bid, "parent_bid" => $c->parent_bid, "area_name" => $c->area_name);
			$res[] = $temp;
		}
		return $res;
	}
	
	public function getIndustry($id=0){
		$industry = new Industry();
		if($id){
			$sql = "SELECT id ,name FROM industry WHERE  id = {$id} ";
		}else{
			$sql = "SELECT id ,name FROM industry WHERE  parent_id = 0 ORDER BY id ASC";
		}		
		$re = $industry->findAllBySql($sql);
		return $re;
	}
	
	public function getRole($what){
		$model = new User();
		$user_id = $this->getLoginId();
		$sql = "SELECT role  FROM user where id = ".$user_id;
		$role = $model->findBySql($sql);
		
		$crole = New Role();
		$sql = "SELECT ".$what." FROM role WHERE id = ".$role->role;
		$do = $crole->findBySql($sql);
		return $do->$what;
	}
	


	public function areas($bid){
		if (empty($bid)) {
			return '未知';
		}
		$model = new Area();
		$sql = "SELECT area_name FROM area WHERE bid = ".$bid;
		$area = $model->findBySql($sql);
		return $area->area_name;
	}
	
	public function allareas($bid){
		if(!$bid){
			return  false;
		}
		$bidArray = explode(',', $bid);
		$bidNew = array();
		if (count($bidArray) > 0) {
			foreach($bidArray as $e){
				if ($e) {
					$bidNew[] = $e;
				}
			}
		}
		$connection = Yii::app()->db;
		if (count($bidNew)) {
			$sql = "SELECT bid,area_name FROM area WHERE bid in (".implode(",", $bidNew).")";
			$command = $connection->createCommand($sql);
			$area = $command->queryAll();
		}
		
		return $area;	
	}
	
	//计算年龄
	function age($YTD){
		//$YTD = strtotime($YTD);//int strtotime ( string $time [, int $now ] )
		$year = date('Y', $YTD);
		if(($month = (date('m') - date('m', $YTD))) < 0){
			$year++;
		}else if ($month == 0 && date('d') - date('d', $YTD) < 0){
			$year++;
		}
		return date('Y') - $year;
	}
	//计算出生日期
	function birthday($age){
		if(!$age){
			return "";
		}
		$age_int = $age*365*24*60*60;
		$day = time()-$age_int;
		return $day;
	}
	//积分和等级
	function getlevel(){
		$level_all = array(
				array(1,150,"游民"),
				array(2,310,"佃户"),
				array(3,480,"贫农"),
				array(4,660,"中农"),
				array(5,850,"富农"),
				array(6,1050,"地主"),
				array(7,1260,"县令"),
				array(8,1480,"知府"),
				array(9,1710,"巡抚"),
				array(10,1950,"总督"),
				array(11,2200,"丞相"),
				array(12,2460,"皇帝"),
				array(13,2730,"牛"),
				array(14,6750,"牛牛"),
				array(15,12210,"犇")
		);
		return $level_all;
	}
	
	//分页
	function textPage($total,$page,$dolink){
		Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/themes/css/pager.css");
		$line = 8;
		//$totalpage = ceil($total/6);
		$totalpage = $total;
		$dolink = '?';
		parse_str($_SERVER['QUERY_STRING'], $params);
			unset($params['p']);

			foreach ($params as $k => $v) {
				$dolink .= "&" .$k . "=" . urlencode($v);
			}
			$dolink .= '&';
		if($totalpage==1)
		{
			return '';
		}
		$pages = $totalpage;
		
	
		$line = $line - 1;
		$page = $page <= 0 ? 1 : $page;
		$page = $page > $pages ? $pages : $page;
		$prev = '';
		$next = '';
		if (($line + 1) > $pages) {
			for ($i = 1; $i <= $pages; $i++) {
				$apclass = $i == $page ? "selected" :'';
				$tmp = ($i-1)==1 ?'page=1': 'page='.($i-1);
				$href = $dolink.'page='.$i;
				if($i == 1){
					$prev ='<li class="previous hidden"><a></a></li>';
					$next ='<li class="next"><a href="'.$dolink.'page='.($i+1).'">下一页</a></li>';
					//$href = $dolink;
				}elseif($i == $pages and $i==$page){
					$prev ='<li class="previous"><a href="'.$dolink.$tmp.'">上一页</a></li>';
					$next ='<li class="next hidden"><a></a></li>';
				}elseif($i==$page){
					$prev ='<li class="previous"><a href="'.$dolink.$tmp.'">上一页</a></li>';
					$next ='<li class="next"><a href="'.$dolink.'page='.($i+1).'">下一页</a></li>';
				}
				$conpage .= "<li class='page $apclass'><a href='$href'>$i</a></li>";
			}
		} else {
			$unit = ceil($line / 2);
			$s_show = $page - $unit;
			$e_show = $page + $unit;
	
			$s_show = $s_show <= 0 ? 1 : $s_show;
			$e_show = $e_show < ($line + 1) ? ($line + 1) : $e_show;
	
			if ($e_show > $pages) {
				$s_show = $pages - $line;
				$e_show = $pages;
			}
	
			if ($s_show > 1)
				$conpage .= '<li class="page"><a href="'.$dolink.'page=1">1</a></li><li class="page"><a style="padding:0">...</a></li>';
	
			for ($i = 1; $i <= $pages; $i++) {
				if ($i >= $s_show and $i <= $e_show) {
					$apclass = $i == $page ? "selected" :'';
					$tmp = ($i-1)==1 ?'page=1': 'page='.($i-1);
	
					$href = $dolink.'page='.$i;
					if($i == 1){
						$prev ='<li class="previous hidden"><a></a></li>';
						$next ='<li class="next"><a href="'.$dolink.'page='.($i+1).'">下一页</a></li>';
						//$href = $dolink;
					}elseif($i == $pages and $i==$page){
						$prev ='<li class="previous"><a href="'.$dolink.$tmp.'">上一页</a></li>';
						$next ='<li class="next hidden"><a></a></li>';
					}elseif($i==$page){
						$prev ='<li class="previous"><a href="'.$dolink.$tmp.'">上一页</a></li>';
						$next ='<li class="next"><a href="'.$dolink.'page='.($i+1).'">下一页</a></li>';
					}
					$conpage .= "<li class='page $apclass'><a href='$href'>$i</a></li>";
				}
			}
			if ($e_show < $pages){
				$conpage .= '<li class="page"><a style="padding:0">...</a></li><li class="page"><a href="'.$dolink.'page='.$totalpage.'">'.$totalpage.'</a></li>';
			}
		}
		$returnstr = $prev.$conpage.$next;
		return $returnstr;
	}
	
	//登录日志
	function insert_log($status){
		$userid = Yii::app ()->user->getState('userInfo')->id;
		$username = Yii::app ()->user->getState('userInfo')->username;
		$log = new LoginLog();
		$log->username = $username;
		$log->logintime = time();
		$log->loginip = $this->egetip();
		$log->ipport = $this->egetipport();
		$log->status = $status;
		$log->userid = $userid;
		$log->save();
	}
	
	//取得IP
	function egetip(){
		if(getenv('HTTP_CLIENT_IP')&&strcasecmp(getenv('HTTP_CLIENT_IP'),'unknown'))
		{
			$ip=getenv('HTTP_CLIENT_IP');
		}
		elseif(getenv('HTTP_X_FORWARDED_FOR')&&strcasecmp(getenv('HTTP_X_FORWARDED_FOR'),'unknown'))
		{
			$ip=getenv('HTTP_X_FORWARDED_FOR');
		}
		elseif(getenv('REMOTE_ADDR')&&strcasecmp(getenv('REMOTE_ADDR'),'unknown'))
		{
			$ip=getenv('REMOTE_ADDR');
		}
		elseif(isset($_SERVER['REMOTE_ADDR'])&&$_SERVER['REMOTE_ADDR']&&strcasecmp($_SERVER['REMOTE_ADDR'],'unknown'))
		{
			$ip=$_SERVER['REMOTE_ADDR'];
		}
		$ip=(preg_replace("/^([\d\.]+).*/","\\1",$ip));
		return $ip;
	}
	
	//取得端口
	function egetipport(){
		$ipport=(int)$_SERVER['REMOTE_PORT'];
		return $ipport;
	}
	
	//获取权限
// 	function getrole(){
// 		$role_arr = Yii::app()->session['role_arr'];
// 		return json_decode($role_arr,true);
// 	}
	
}

?>
