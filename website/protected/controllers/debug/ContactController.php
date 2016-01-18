<?php
class ContactController extends PublicController
{
	public $layout = false;
	/**
	 * 修复百姓网数据
	 */
	// public function actionBaixing(){
	// 	$this->check_key();
	// 	$short = Bxapply::model()->findAll("short_phone >0 and status = 3");
	// 	$connection = Yii::app()->db;
	// 	foreach ($short as $value){
	// 		$sql = "update group_contact_phone set is_baixing={$value->short_phone} where phone = {$value->phone}";
	// 		echo $sql;
	// 		echo '<br><br><br>';
	// 		$command = $connection->createCommand($sql);
	// 		$re1 = $command->execute();
	// 	}
	// }
	/**
	 * 修复通讯录benben_id数据
	 */
	public function actionCon(){
		$this->check_key();
		$connection = Yii::app()->db;
		$sql = "select a.id,a.benben_id,b.is_benben from group_contact_info a left join group_contact_phone b on a.id = b.contact_info_id 
				where b.is_benben > 0";
		$command = $connection->createCommand($sql);
		$re1 = $command->queryAll();
		foreach ($re1 as $va){
			$info = GroupContactInfo::model()->find("id = {$va['id']}");
			if($info->benben_id){
				continue;
			}
			$sql1 = "update group_contact_info set benben_id = {$va['is_benben']} where id = {$va['id']}";
			$command = $connection->createCommand($sql1);echo $sql1."</br>";
			$re2 = $command->execute();
		}
	}
	
	public function actionMatch(){
		if ((Yii::app()->request->isPostRequest)) {
			$PinYin = new PYInitials('utf8');			
			$this->check_key();
			$group = Frame::getStringFromRequest('group');			
			$phone = Frame::getStringFromRequest('phone');
			//$group = "同学,朋友,学生,好友,公司,客户";
			//$phone = "15358105507+小明|18608976789#18607968709+小花|13592753478+小学|18752753478+小小,18098908998+小米|13978569048+花花";
			//$group = "";
			//$phone = "15256978325#15656978325+小明|18608976789+小花|18098908998+小米|13978569048+花花";
			//$phone ="15555142507#18888888888::你好你噶|13333333333#13333333334+测试";
			$user = $this->check_user();
			
			if(!$group){
				$arr_group = array("朋友","家人","同事","未分组");
				$arr_group_all = explode(",", ',,,'.$phone);
			}else{
				$arr_group = explode(",", $group);
				$arr_group_all = explode(",", $phone);
			}
			$connection = Yii::app()->db;
			//查询犇犇号码
			$currentDetail0 = explode("|", $phone);
			//取出名字和号码存2个数组
			foreach ($currentDetail0 as $v1){
				$person_info1 = explode("::", $v1);
				$allname[] = $person_info1[1];
				$allphone[] = $person_info1[0];
			}
			foreach ($allphone as $v2){
				$aphone = explode("#", $v2);
				foreach ($aphone as $v3){
					if(strstr($v3,"+86")){
						$err_phone[] = "'".substr($v3,3,strlen($v3))."'";
					}else{
						$err_phone[] = "'".$v3."'";
					}
				}
			}
			//根据号码获取信息
			$sql = "select benben_id,poster,huanxin_username,phone from member where phone in (".implode(",", $err_phone).")";
			$command = $connection->createCommand($sql);
			$res0 = $command->queryAll();
			$benben_array = array();
			if($res0){
				foreach ($res0 as $va){
					$benben_array[$va['phone']] = $va;
				}
			}
									
			$group_id = array();
			$return_group_info = array();
			if(is_array($arr_group)){
				
				$group_detail = array();
				$group_contact_info = array();
				$search_phone = array();
				foreach ($arr_group as $key => $val){
					$group_u = new GroupContact();
					$group_u->group_name = $val;
					$group_u->member_id = $user->id;
					$group_u->created_time = time();
					if($group_u->save()){    //保存分组信息
						$group_id[] = $group_u->id;	
						$return_group_info[] = array('id'=>$group_u->id, 'name'=>$val);																				
					}
					if($arr_group_all[$key]){
						$currentDetail = explode("|", $arr_group_all[$key]);
						foreach ($currentDetail as $cKey => $cValue) {
							$person_info = explode("::", $cValue);
							$per_ph = explode("#", $person_info[0]);
							if(!$person_info[1]){$person_info[1] = $per_ph[0];}
							$benben_id = 0;
							foreach ($per_ph as $key1=>$v){
								if(strstr($v,"+86")){
									$per_ph[$key1] = substr($v,3,strlen($v));
								}
								if(!$benben_id){
									$benben_id = $benben_array[$per_ph[$key1]]['benben_id'] ? $benben_array[$per_ph[$key1]]['benben_id'] : 0;
								}								
							}
														
							$group_contact_info[] = '('.$group_u->id.', "'.$person_info[1].'", "'.$PinYin->getInitials($person_info[1]).'", '.time().', '.$user->id.', '.$benben_id.')';
							$group_detail[$group_u->id][] = $person_info;				
														
							$search_phone =  array_merge($search_phone, $per_ph);							
						}
					}							
				}
				
				//将姓名插入到数据库
				if(count($group_contact_info)){
					$sqlgrp = "insert into group_contact_info (group_id,name,pinyin,created_time,member_id,benben_id) values ".implode($group_contact_info, ",");					
					$command = $connection->createCommand($sqlgrp);
					$result = $command->execute();
				}
				
					
				//查找出新增加的姓名，将姓名与ID之前的关系通过数据存放
				$criteria = new CDbCriteria;
				$criteria->select = 'id,group_id,name';
				$criteria->addInCondition('group_id', $group_id);
				$find_result = GroupContactInfo::model()->findAll($criteria);
				$relation_name_id = array();
				if (count($find_result)) {
					foreach ($find_result as $key => $value) {
						$relation_name_id[$value->name] = $value->id;
					}
				}
				
				//去查找是否是犇犇用户,并将用户信息通过数组关系存放
				$criteria2 = new CDbCriteria;
				$criteria2->select = 'id,phone, nick_name, benben_id,poster,huanxin_username';
				$criteria2->addInCondition('phone', $search_phone);
				$find_result2 = Member::model()->findAll($criteria2);
				$benben_phone_id = array();
				$t = time();
				$ve = "";
				if (count($find_result2)) {
					foreach ($find_result2 as $key => $value) {
						$poster = $value->poster ? URL.$value->poster:"";
						$value->huanxin_username = $value->huanxin_username ? $value->huanxin_username : "";
						$benben_phone_id[$value->phone] = array('id'=>$value->id, 'benben_id'=>$value->benben_id,'nick_name'=>$value->nick_name, 'poster'=>$poster, 'huanxin_username'=>$value->huanxin_username);
						$ve .= "({$user->id},{$value->id},1,{$t}),";
					}
				}				
				//添加好友记录(friend_relate)表
			/*	if($ve){
					$ve = trim($ve);
					$ve =trim($ve,',');
					$sqlf = "replace into friend_relate (friend_id1,friend_id2,status,created_time) values {$ve}";
					$command = $connection->createCommand($sqlf);
					$resul = $command->execute();
				}	*/							
				//去查找是否是百姓网用户,并将用户信息通过数组关系存放
				$criteria3 = new CDbCriteria;
				$criteria3->select = 'id,phone,short_phone';
				$criteria3->addInCondition('phone', $search_phone);
				$criteria3->addCondition('status=3');
				$find_result3 = Bxapply::model()->findAll($criteria3);
				$baixing_phone_id = array();
				if (count($find_result3)) {
					foreach ($find_result3 as $key => $value) {
						$baixing_phone_id[$value->phone] = array('id'=>$value->id,'baixing'=>$value->short_phone);
					}
				}
				
				//拼号码入库数据以及接口返回数据
				$insert_contact_phone = array();
				$return_person_info = array();
				foreach ($group_id as $key => $val){
					$currentDetail = explode("|", $arr_group_all[$key]);
					if(!$currentDetail[0]) continue;
					foreach ($currentDetail as $cKey => $cValue) {						
						$person_info = explode("::", $cValue);
						$person_name = $person_info[1];
						$person_phone = explode("#", $person_info[0]);
						if(!$person_name){$person_name = $person_phone[0];}
						$return_phone_info = array();
						$benben = 0;
						$baixing = 0;
						$hxn = "";
						$po = "";
						if (isset($relation_name_id[$person_name])) {
							$insert_info_id = $relation_name_id[$person_name];
							foreach($person_phone as $each_phone){
								if(!$each_phone) continue;
								if($benben_array[$each_phone]){
									if(!$benben){
										$benben = $benben_array[$each_phone]['benben_id'];
									}
									if(!$hxn){
										$hxn = $benben_array[$each_phone]['huanxin_username'];
									}
									if(!$po){
										$po = $benben_array[$each_phone]['poster'];
									}
								}
								if(strstr($each_phone,"+86")){
									$each_phone = substr($each_phone,3,strlen($each_phone));
								}
								$is_benben = 0;
								$is_baixing = 0;
								if (isset($baixing_phone_id[$each_phone])) {
									$is_baixing = $baixing_phone_id[$each_phone]['baixing'];
									$is_baixing = intval($is_baixing);
									if(!$baixing){
										$baixing = $is_baixing;
									}									
								}
								if (isset($benben_phone_id[$each_phone])) {
									$is_benben = $benben_phone_id[$each_phone]['benben_id'];																										
									$return_phone_info[] = array('phone'=>$each_phone, 'is_benben'=>$is_benben, 'is_baixing'=>$is_baixing, 'poster'=>$benben_phone_id[$each_phone]['poster'], 'nick_name'=>$benben_phone_id[$each_phone]['nick_name']);
								}else{
									$return_phone_info[] = array('phone'=>$each_phone, 'is_benben'=>$is_benben, 'is_baixing'=>$is_baixing, 'poster'=>'', 'nick_name'=>'');
								}
									
								$insert_contact_phone[] = '('.$insert_info_id.', "'.$each_phone.'", '.$is_benben.', '.$is_baixing.')';
							}
							if(!$return_phone_info){$return_phone_info = array();}
						}
						$py = substr($PinYin->getInitials($person_name),0,1);
						$py = strtoupper($py);
						$reg = '/[A-Z]{1}/s';						
						if(!(preg_match($reg, $py,$c) and $py==$c[0])){
							$py = "#";
						}
						$return_person_info[] = array('id'=>$insert_info_id, 'group_id'=>$val, 'name'=>$person_name, 'pinyin'=>$py, 'is_benben'=>$benben,'is_baixing'=>$baixing, 'huanxin_username'=>$hxn, 'poster'=>$po,'phone'=>$return_phone_info);
					}
				}
				//将手机号码插入到数据库
				if (count($insert_contact_phone)) {
					$sqlPhone = "insert into group_contact_phone (contact_info_id,phone,is_benben, is_baixing) values ".implode($insert_contact_phone, ",");
					$connection = Yii::app()->db;
					$command = $connection->createCommand($sqlPhone);
					$result = $command->execute();
				}
				
				$return_group_info[] = array('id'=>10000, 'name'=>'常用号码直通车');	
				$return['ret_num'] = 0;
				$return['ret_msg'] = '操作成功';
				$return['group'] = $return_group_info;
				$return['contact'] = $return_person_info;
				echo json_encode($return);																			
 		  }			
		}
	}
	
	/**
	 * 写日志，写在根目录下的log文件夹内
	 * 
	 * @param 日志内容 $msg
	 * @param 文件夹名 $folder
	 * @param 文件名（不带后缀） $file
	 */
	public function GameLog($logmsg, $folderName='log', $fileName='log') {
		$basdPath = dirname(dirname(__FILE__));
	    $arr_base = explode('/',$basdPath);
		//检查文件夹
	    $arr  = explode('/',$folderName);
	    $arr = array_merge($arr_base, $arr);
	    $dt = date("Y-m");
	    array_push($arr, $dt);
	    $path = implode('/',$arr);
	    $this->_mkdirs($path);
	    
	    //文件名-按日期
	    $fileName = $fileName.'_'.date("Ymd").".txt";
	    array_push($arr, $fileName);
	    $path = implode('/',$arr);

	    //写日志
		$fp = fopen($path,"a+");
		flock($fp, LOCK_EX) ;
		fwrite($fp,"日期：".date("Y-m-d h:i:s")."\n".$logmsg."\n\n");
		flock($fp, LOCK_UN);
		fclose($fp);
	}

	/**
	 * 按指定路径生成目录
	 *
	 * @paramstring $path路径
	 */
	public function _mkdirs($path)
	{
		$adir = explode('/',$path);
		$dirlist = '';
		$rootdir = array_shift($adir);
		if(($rootdir!='.' || $rootdir!='..') && !file_exists($rootdir)) {
			@mkdir($rootdir);
		}
		foreach($adir as $key=>$val)
		{
			if($val!='.'&&$val!='..')
			{
				$dirlist .= "/".$val;
				$dirpath = $rootdir.$dirlist;
				if(!file_exists($dirpath))
				{
					@mkdir($dirpath);
					@chmod($dirpath,0777);
				}
			}
		}
	}


	/**
	 * 通讯录添加联系人
	 */
	public function actionNewmatch(){			
			$this->check_key();			
			$phone = Frame::getStringFromRequest('phone');
			//$phone = "15656976325#13335976090::小明26|18029950453::小花26|15305896768::小米26|18867946568#13978689048::花花26";
			$phone = "13506599361::阿三|15068814226::阿浩|13110971057::安利耐谭振强|15158056991::阿鲍|13506796627::阿姨|15366327888::安利耐卫建东|15226564530::阿闲|057987212408::陈元松|13758132519#13588665164::聪聪|15258986709::陈老师|18616121742::程小芮|15158017561::陈可可|15157967576::陈|13758149353::曹小芳|18858784665::程工
|+8615158017182::褚辰怡|13968024010::陈遥云|13758993902::大哥|13486111063::电话|15158017232::东东|15601820037::大雄|18969879288::大伯母|15158021740::戴其贞|18668190930::大姐|+8615068065562::杜婷婷|13506794743#15305896768::大伯|88236471#13735806683::大外婆|15356689978::戴菁|18958170900#18072332688::二姐|64595112::飞轮砂纸|15158013120#15057940818::夫|+8615021801679::发票|18266915665::方凯露|13506798608::方伟|15381711878::方金燕|13735629820::姑姑|13575729885::郭化林|045182483845::哈尔滨|18368361451::胡琛琛|+8618367232157::胡卫彬|13858902222::胡卓玉|15158017313#18869936333::胡芳旭|15057909268#728055::胡鹏|13646890901::胡红宇
|13758994078::胡建青|13506793535::胡朝阳|15158017540::欢欢|15258995390::胡汉康|18505892168::胡炎康|13967914692::胡俊礼|13738131766::黄翔|18758905640::哈开|15157972285::何冲|15258985494::胡周|4001183315#4009602620#4009603650#4006920508::回拨专线|18867946568::黄俊伟|18067629618::胡国栋|13588711532::舅宇|18019309368::舅|13641748980::舅妈|15925929682::金小雪|13738082066::解伟|18958171999#18066231999::姐夫|13506599623::俊杰|13101977531::舅公|057987523819::kkk|13391050820::快捷|13017986737::快递|13818077868::吕钢|18957905551::吕志鹏|+8618966055308::李海涛|057987153728::蓝鸟|13857906565#13858939998::李爽阳|13588068082#15705891218::吕豪|15188322681::吕崇|13967934339::林雄清|15760559908::吕经理|13906531615::李光辉|+8613641910030::刘永成|15857988517::六婶|13967938622::吕杰|+8613819129703::吕静|18329027051::李谌璐|18067650988::吕小布|15728002945::吕丽芳|13958453956::老翁|15000236914::林新琪|15857993678::卢思艺|13486994089::老爸|18026216567::林啸|13735606469::李姐|15618675029::黎亚宇|18267079882::李享|13916958641::李冰璠|18069983536::吕广|13858903854::马佩勋|13732217792::毛|15888919145::马可|13611807891::妈|18650930030::宁|15858198333::宁哥|15305797272::苹果|+8613901902100::全顺科达|13205792555::全叔|13506595199::施羚爽|13506596785::三伯|02134151293::上海|15957954288::舒俊心|02152633368::上海2|13918082010::顺发|18989319188#18006538787#790201::三姐|13858289012::施武梦|13665880611::舒忆|13705896057::邵庆
伟|15088626226::孙快|15857998878::铁|15268621067::田方亮|15158017479::陶荣芳|+8615658033273::童小波|15158006489#15372580728::王亚文|13058998156::吴强|13989492784::吴沈娟|13868959277::伟一|057987511942::外公家|13524508627::王旭仁|13735626033::吴陈宇|15372968800::王琳|15068560326::王执|13516980891::外婆|18867506118::王晗|1505851585::王雪燕|18057979664::外公|+8615067948861::吴冰洁|057188157855::未知|15158017280::夏玲娜|+8618757602987::小优|13730443015::夏竟|15057960878::徐丽莎|15158070154#13958457578::小程|15867168689::小舅妈|15988538998::项羽哲|+86 18986233037::小雨|15158030879#15888566796::徐华军|15158020487::徐骁|18729060285::翔宇|13588885165::小舅舅|02164061991::新店|13346187531::徐亚力|13484081838::夏云高|13120960978::小波|18768184298::夏一跳|15903615513::杨立志|15158017299::杨风|15601901917::运达|15088231883::叶嘉惠|13738183177::俞其|15933560865::姨婆|15888901608::英戈峰|13524218556::韵达|15268636148::杨洁颖|15924261123::杨健
华|057983981687::爷爷|13816005924::尹律师|13505818646::喻文婷|13626797922::燕|13336120333::银泰打折|+8615158017050::应文梁|13735457608::杨洁|13058988319::羽>斌|+8615158118532::涯叔|15158922159::叶宇超|13705894782::叶总|15258971199::姚亚云|17756064871::姚艳辉|13065737176::周工|15267538263::中华|15601895706::朱总|18664707586::朱志广|13868946616::智勇|13857959955::张磊|13819107753::张淼|15158017620::张宇|18958171188::周海东";
			$PinYin = new PYInitials('utf8');
			$user = $this->check_user();
			
			//记录同步历史	
			$log = $user->id.'  == '.$phone;
			// $this->GameLog($log);

			//查出未分组ID
			$connection = Yii::app()->db;
			$own = GroupContact::model()->find("group_name = '未分组' and member_id = {$user->id}");
			if(!$own){
				//添加分组
				$arr_group = array("朋友","家人","同事","未分组");
				$t1 = time();
				$in = array();
				foreach ($arr_group as $va){
					$in[] = "('{$va}',{$t1},{$user->id})";
				}
				
				$sql = "insert into group_contact (group_name,created_time,member_id) values ".implode(",", $in);
				$command = $connection->createCommand($sql);
				$re1 = $command->execute();
				$own = GroupContact::model()->find("group_name = '未分组' and member_id = {$user->id}");
			}
			
			$group_contact_info = array();
			$search_phone = array();
			$currentDetail = explode("|", $phone);
			//取出名字和号码存2个数组
			foreach ($currentDetail as $v1){
				$person_info1 = explode("::", $v1);
				$allname[] = $person_info1[1];
				$allphone[] = $person_info1[0];
			}			
			foreach ($allphone as $v2){
			     $aphone = explode("#", $v2);
			     foreach ($aphone as $v3){
			     	if(strstr($v3,"+86")){
			     		$err_phone[] = "'".substr($v3,3,strlen($v3))."'";
			     	}else{
			     		$err_phone[] = "'".$v3."'";
			     	}			     	
			     }	
			}
			//根据号码获取信息
			$sql = "select benben_id,poster,huanxin_username,phone from member where phone in (".implode(",", $err_phone).")";
			$command = $connection->createCommand($sql);
			$res0 = $command->queryAll();
			$benben_array = array();
			if($res0){
				foreach ($res0 as $va){
					$benben_array[$va['phone']] = $va;
				}
			}
			
			$friend_phone = array();
			if (count($err_phone) > 0) {
				$sql = "select phone from group_contact_phone a left join group_contact_info b on a.contact_info_id = b.id where b.member_id = {$user->id} and a.phone in (".implode(",", $err_phone).")";
				$command = $connection->createCommand($sql);
				$re2 = $command->queryAll();
				foreach ($re2 as $va){
					$friend_phone[] = $va['phone'];
				}
			}
			
			foreach ($currentDetail as $cKey => $cValue) {
				$person_info = explode("::", $cValue);
				$per_ph = explode("#", $person_info[0]);
				if(!$person_info[1]){$person_info[1] = $per_ph[0];}
				$benben_id = 0;
				foreach ($per_ph as $key1=>$v){
					if(strstr($v,"+86")){
						$per_ph[$key1] = substr($v,3,strlen($v));
					}
					if(!$benben_id){
						$benben_id = $benben_array[$per_ph[$key1]]['benben_id'] ? $benben_array[$per_ph[$key1]]['benben_id'] : 0;
					}
				}
				$flag = 0;
				foreach ($per_ph as $val){
					if(!in_array($val, $friend_phone)){
					       $flag = 1;
					}
				}
			    if($flag){
			    	$group_contact_info[] = '('.$own->id.', "'.$person_info[1].'", "'.$PinYin->getInitials($person_info[1]).'", '.time().', '.$user->id.', '.$benben_id.')';
			    	$search_phone =  array_merge($search_phone, $per_ph);
			    }				
				//$group_detail[$group_u->id][] = $person_info;								
			}
// 			var_dump($group_contact_info);echo "---------------";exit;
// 			var_dump($search_phone);exit();
			if(!$group_contact_info){
				$return['ret_num'] = 1802;
				$return['ret_msg'] = '联系人已在通讯录';				
				echo json_encode($return);
				exit();
			}
			//将姓名插入到数据库
			$sqlgrp = "insert into group_contact_info (group_id,name,pinyin,created_time,member_id,benben_id) values ".implode($group_contact_info, ",");
			$connection = Yii::app()->db;
			$command = $connection->createCommand($sqlgrp);
			$result = $command->execute();
				
			//查找出新增加的姓名，将姓名与ID之前的关系通过数据存放
			$criteria = new CDbCriteria;
			$criteria->select = 'id,group_id,name';
			$criteria->addCondition('group_id='.$own->id);
			$find_result = GroupContactInfo::model()->findAll($criteria);
			$relation_name_id = array();
			if (count($find_result)) {
				foreach ($find_result as $key => $value) {
					$relation_name_id[$value->name] = $value->id;
				}
			}
			//去查找是否是犇犇用户,并将用户信息通过数组关系存放
			$criteria2 = new CDbCriteria;
			$criteria2->select = 'id,phone, nick_name, benben_id,poster,huanxin_username';
			$criteria2->addInCondition('phone', $search_phone);
			$find_result2 = Member::model()->findAll($criteria2);
			$benben_phone_id = array();
			$t = time();
			$ve = "";
			if (count($find_result2)) {
				foreach ($find_result2 as $key => $value) {
					$poster = $value->poster ? URL.$value->poster:"";
					$value->huanxin_username = $value->huanxin_username ? $value->huanxin_username : "";
					$benben_phone_id[$value->phone] = array('id'=>$value->id, 'benben_id'=>$value->benben_id,'nick_name'=>$value->nick_name, 'poster'=>$poster, 'huanxin_username'=>$value->huanxin_username);
					$ve .= "({$user->id},{$value->id},1,{$t}),";
				}
			}
			//添加好友记录(friend_relate)表
		/*	if($ve){
				$ve = trim($ve);
				$ve =trim($ve,',');
				$sqlf = "replace into friend_relate (friend_id1,friend_id2,status,created_time) values {$ve}";
				$command = $connection->createCommand($sqlf);
				$resul = $command->execute();
			} */
			//去查找是否是百姓网用户,并将用户信息通过数组关系存放
			$criteria3 = new CDbCriteria;
			$criteria3->select = 'id,phone,short_phone';
			$criteria3->addInCondition('phone', $search_phone);
			$criteria3->addCondition('status=3');
			$find_result3 = Bxapply::model()->findAll($criteria3);
			$baixing_phone_id = array();
			if (count($find_result3)) {
				foreach ($find_result3 as $key => $value) {
					$baixing_phone_id[$value->phone] = array('id'=>$value->id,'baixing'=>$value->short_phone);
				}
			}
			//拼号码入库数据以及接口返回数据
			$insert_contact_phone = array();
			$return_person_info = array();
			
			$currentDetail = explode("|", $phone);
			if(!$currentDetail[0]) continue;
			foreach ($currentDetail as $cKey => $cValue) {
				$person_info = explode("::", $cValue);
				$person_name = $person_info[1];
				$person_phone = explode("#", $person_info[0]);
				if(!$person_name){$person_name = $person_phone[0];}
				$return_phone_info = array();
				$benben = 0;
				$baixing = 0;
				$hxn = "";
				$po = "";
				if (isset($relation_name_id[$person_name])) {
					$insert_info_id = $relation_name_id[$person_name];
					foreach($person_phone as $each_phone){
						if(!$each_phone || in_array($each_phone, $friend_phone)) continue;
						if($benben_array[$each_phone]){
							if(!$benben){
								$benben = $benben_array[$each_phone]['benben_id'];
							}
							if(!$hxn){
								$hxn = $benben_array[$each_phone]['huanxin_username'];
							}
							if(!$po){
								$po = $benben_array[$each_phone]['poster'] ? URL.$benben_array[$each_phone]['poster'] : "";
							}
						}
						if(strstr($each_phone,"+86")){
							$each_phone = substr($each_phone,3,strlen($each_phone));
						}
						$is_benben = 0;
						$is_baixing = 0;
						if (isset($baixing_phone_id[$each_phone])) {
							$is_baixing = $baixing_phone_id[$each_phone]['baixing'];
							$is_baixing = intval($is_baixing);
							if(!$baixing){
								$baixing = $is_baixing;
							}
						}
						if (isset($benben_phone_id[$each_phone])) {
							$is_benben = $benben_phone_id[$each_phone]['benben_id'];							
							$return_phone_info[] = array('phone'=>$each_phone, 'is_benben'=>$is_benben, 'is_baixing'=>$is_baixing, 'poster'=>$benben_phone_id[$each_phone]['poster'], 'nick_name'=>$benben_phone_id[$each_phone]['nick_name'], 'huanxin_username'=>$hxn);
						}else{
							$return_phone_info[] = array('phone'=>$each_phone, 'is_benben'=>$is_benben, 'is_baixing'=>$is_baixing, 'poster'=>'', 'nick_name'=>'', 'huanxin_username'=>$hxn);
						}
							
						$insert_contact_phone[] = '('.$insert_info_id.', "'.$each_phone.'", '.$is_benben.', '.$is_baixing.')';
					}
					if(!$return_phone_info){$return_phone_info = array();}
				
				$py = substr($PinYin->getInitials($person_name),0,1);
				$py = strtoupper($py);
				$reg = '/[A-Z]{1}/s';
				if(!(preg_match($reg, $py,$c) and $py==$c[0])){
					$py = "#";
				}
			
				$return_person_info[] = array('id'=>$insert_info_id, 'group_id'=>$own->id, 'name'=>$person_name, 'pinyin'=>$py, 'is_benben'=>$benben,'is_baixing'=>$baixing, 'huanxin_username'=>$hxn, 'poster'=>$po,'phone'=>$return_phone_info);
				
				}
			}
			//将手机号码插入到数据库
			$sqlPhone = "insert into group_contact_phone (contact_info_id,phone,is_benben, is_baixing) values ".implode($insert_contact_phone, ",");
			$connection = Yii::app()->db;
			$command = $connection->createCommand($sqlPhone);
			$result = $command->execute();
			
			$return['ret_num'] = 0;
			$return['ret_msg'] = '操作成功';
			$return['contact'] = $return_person_info;
			echo json_encode($return);
		
			
	}
	
	/**
	 * 返回通讯录
	 */
	public function actionContactinfo(){
		$this->check_key();
		$user = $this->check_user();
		$connection = Yii::app()->db;
		$sql1 = "select id,group_name name from group_contact where member_id = {$user->id}";
		$command = $connection->createCommand($sql1);
		$result1 = $command->queryAll();
		if($result1){
			foreach ($result1 as $value){
				$gid .= $value['id'].",";
			}
			$gid = trim($gid);
			$gid =trim($gid,',');
			$result1[] = array("id"=>10000,"name"=>"常用号码直通车");
		}
		$result_group = $result1;
		// 		$result_group = array();
		// 		for($i=count($result1)-1;$i>=0;$i--){
		// 			$result_group[] = $result1[$i];
		// 		}
			$result2 = array();
			if($gid){
				$sql2 = "select id,group_id,name,pinyin,benben_id is_benben,created_time from group_contact_info where group_id in ({$gid})";
				$command = $connection->createCommand($sql2);
				$result2 = $command->queryAll();
				$benben_id = array();
				foreach ($result2 as $val){
					$aid .= $val['id'].",";
					$benben_id[] = $val['is_benben'];
				}
				$aid = trim($aid);
				$aid =trim($aid,',');
			}
			//根据benben_id获取信息
			$benben_info = array();
			if($benben_id){
				$sql6 = "select a.id,a.benben_id,a.poster,a.huanxin_username,b.short_phone from member a left join bxapply b on a.phone = b.phone  
						where a.benben_id in (".implode(",", $benben_id).") and b.status = 3";
				$command = $connection->createCommand($sql6);
				$result6 = $command->queryAll();
				foreach ($result6 as $v6){
					$benben_info[$v6['benben_id']] = $v6;
				}
			}
	
			if($aid){
				$contact_phone = array();
				$contact_phonea = array();
				$sql3 = "select a.id,a.contact_info_id,a.phone,a.is_benben,a.is_baixing ,b.poster,b.huanxin_username from
				group_contact_phone a left join member b on a.phone=b.phone
				where a.contact_info_id in ({$aid}) order by a.id asc";
				$command = $connection->createCommand($sql3);
				$result3 = $command->queryAll();
				foreach ($result3 as $va){
				$tmp_key = $va['contact_info_id'];
						$contact_phone[$tmp_key][] = $va;
				}
					
				foreach ($contact_phone as $k2 => $valu){
				$benben = 0;
				$baixing = 0;
				$poster = "";
				$hxn = "";
					
				foreach ($valu as $k =>$ue){
					$bid = $benben_info[$ue['benben_id']];
				    $contact_phone[$k2][$k]['poster'] = $bid['poster'] ? URL.$bid['poster'] : "";
					/* if(!$benben and $bid['benben_id']){
						$benben = $bid['benben_id'];
					} */
					if(!$baixing and $bid['is_baixing']){
						$baixing = $bid['is_baixing'];
					}
					/* if(!$poster and $bid['poster']){
						$poster = URL.$bid['poster'];
					}
					if(!$hxn and $bid['huanxin_username']){
						$hxn = $bid['huanxin_username'];
					} */
				}
				//$contact_phonea[$k2]['is_benben'] = $benben;
				$contact_phonea[$k2]['is_baixing'] = $baixing;
				//$contact_phonea[$k2]['poster'] = $poster;
				//$contact_phonea[$k2]['huanxin_username'] = $hxn;
			}
		
		}
		foreach ($result2 as $key => $v){
			$py = substr($v['pinyin'],0,1);
			$py = strtoupper($py);
			$reg = '/[A-Z]{1}/s';
			if(!(preg_match($reg, $py,$c) and $py==$c[0])){
				$py = "#";
			}
			$result2[$key]['pinyin'] = $py;
			//$result2[$key]['is_benben'] = $contact_phonea[$v['id']]['is_benben'] ? $contact_phonea[$v['id']]['is_benben'] : "0";
			//$result2[$key]['is_baixing'] = $contact_phonea[$v['id']]['is_baixing'] ? $contact_phonea[$v['id']]['is_baixing'] : "0";
			$result2[$key]['is_baixing'] = $benben_info[$v['is_benben']]['short_phone'] ? $benben_info[$v['is_benben']]['short_phone'] : "0";
			$result2[$key]['poster'] = $benben_info[$v['is_benben']]['poster'] ? URL.$benben_info[$v['is_benben']]['poster'] : "";
			$result2[$key]['huanxin_username'] = $benben_info[$v['is_benben']]['huanxin_username'] ? $benben_info[$v['is_benben']]['huanxin_username'] : "";
			$result2[$key]['phone'] = $contact_phone[$v['id']] ? $contact_phone[$v['id']] : array();
		}
			//获取收藏的号码直通车
			$sql = "select a.id,a.name,a.short_name,a.poster,a.phone,a.telephone from number_train a inner join number_train_collect b
			on a.id = b.number_train_id where a.is_close = 0 and b.member_id = {$user->id} order by a.istop desc,a.created_time desc,a.id desc ";
			$command = $connection->createCommand($sql);
			$result0 = $command->queryAll();
			foreach ($result0 as $v1){
			$phone = array(
				"id"=> "",
				"contact_info_id"=> $v1['id']+1000000,
				"phone"=> $v1['phone'],
						"is_benben"=> 0,
						"is_baixing"=> 0,
						"poster"=> $v1['poster'] ? URL.$v1['poster'] : "",
						"huanxin_username"=> ""
			);
			$short_phone = array(
				"id"=> "",
				"contact_info_id"=> $v1['id']+1000000,
						"phone"=> $v1['telephone'],
								"is_benben"=> 0,
								"is_baixing"=> 0,
								"poster"=> $v1['poster'] ? URL.$v1['poster'] : "",
								"huanxin_username"=> ""
			);
			$collect = array(
				"id"=>$v1['id']+1000000,
				"group_id"=> 10000,
				"name"=> $v1['short_name'],
				"short_name"=> $v1['name'],
					"pinyin"=> "",
						"created_time"=> "",
						"is_benben"=> 0,
						"is_baixing"=> 0,
						"poster"=> $v1['poster'] ? URL.$v1['poster'] : "",
						"huanxin_username"=> "",
					"phone"=> $v1['telephone']?array($phone,$short_phone):array($phone)
				);
				$result2[] = $collect;
			}
	
			$result ['ret_num'] = 0;
			$result ['ret_msg'] = '操作成功';
			$result['group'] = $result_group;
			$result['contact'] = $result2;
			echo json_encode( $result );
		}
	
	/**
	 * 添加分组
	 */
	public function actionAddgroup(){
		$this->check_key();
		$user = $this->check_user();
		$groupname = Frame::getStringFromRequest('group');
		if (empty ( $groupname )) {
			$result ['ret_num'] = 101;
			$result ['ret_msg'] = '请输入分组名';
			echo json_encode( $result );
			die ();
		}
		if ($groupname == "未分组") {
			$result ['ret_num'] = 182;
			$result ['ret_msg'] = '请输入其他的分组名';
			echo json_encode( $result );
			die ();
		}
		$info = GroupContact::model()->find("group_name = '{$groupname}' and member_id = {$user->id}");
		if($info){
			$result ['ret_num'] = 5207;
			$result ['ret_msg'] = '分组名已存在';
			echo json_encode( $result );
			die ();
		}
		$group = new GroupContact();
		$group->group_name = $groupname;
		$group->member_id = $user->id;
		$group->created_time = time();
		if($group->save()){
			$result ['ret_num'] = 0;
			$result ['ret_msg'] = '操作成功';
			$result['group_id'] = $group->id;
		}else{
			$result ['ret_num'] = 116;
			$result ['ret_msg'] = '分组添加失败';
		}
		echo json_encode( $result );
	}

	/**
	 * 编辑分组
	 */
	public function actionEditgroup(){
		$this->check_key();
		$user = $this->check_user();
		$groupname = Frame::getStringFromRequest('group');
		$groupid = Frame::getIntFromRequest('group_id');
		if (empty ( $groupname )) {
			$result ['ret_num'] = 101;
			$result ['ret_msg'] = '请输入分组名';
			echo json_encode( $result );
			die ();
		}
        $trimname = trim($groupname);
        if($trimname == '未分组'){
            $result ['ret_num'] = 100;
            $result ['ret_msg'] = '分组名已存在';
            echo json_encode( $result );
            die();
        }
		$info = GroupContact::model()->find("group_name = '{$groupname}' and member_id = {$user->id}");
		if($info){
			$result ['ret_num'] = 5207;
			$result ['ret_msg'] = '分组名已存在';
			echo json_encode( $result );
			die ();
		}
     	if (empty ( $groupid )) {
			$result ['ret_num'] = 102;
			$result ['ret_msg'] = '分组ID为空';
			echo json_encode( $result );
			die ();
		}
		$group = GroupContact::model()->find("id = {$groupid}");
		if(empty($group)){
			$result ['ret_num'] = 103;
			$result ['ret_msg'] = '分组不存在';			
		}else{
			$group->group_name = $groupname;
			if($group->update()){
				$result ['ret_num'] = 0;
				$result ['ret_msg'] = '操作成功';
			}else{
				$result ['ret_num'] = 104;
				$result ['ret_msg'] = '分组修改失败';
			}			
		}
		echo json_encode( $result );
	}
	
	/**
	 * 删除分组
	 */
	public function actionDeletegroup(){
		$this->check_key();
		$user = $this->check_user();
		$target = Frame::getStringFromRequest('target');
		$groupid = Frame::getIntFromRequest('group_id');
		if (empty ( $groupid )) {
			$result ['ret_num'] = 102;
			$result ['ret_msg'] = '分组ID为空';
			echo json_encode( $result );
			die ();
		}
		$group = GroupContact::model()->find("id = {$groupid}");
		if(empty($group)){
			$result ['ret_num'] = 103;
			$result ['ret_msg'] = '分组不存在';			
		}else{
			$connection = Yii::app()->db;
			$sql = "select id from group_contact_info where group_id = {$groupid}";
			$command = $connection->createCommand($sql);
			$result0 = $command->queryAll();
			$phone_id = array();
			foreach ($result0 as $va){
				$phone_id[] = $va['id'];
			}
			$count = count($result0);			
		
			if($count && $target){				
				$sql = "update group_contact_info set group_id = {$target} where group_id = {$groupid}";
				$command = $connection->createCommand($sql);
				$result0 = $command->execute();
				
				if($result0){
					if($group->group_name != "未分组"){
						$group->delete();
					}
					$result ['ret_num'] = 0;
					$result ['ret_msg'] = '操作成功';
				}
			}else{
				if($count){
					$sql = "delete from group_contact_info where group_id = {$groupid}";
					$command = $connection->createCommand($sql);
					$result0 = $command->execute();
					//删除联系人号码
					if($phone_id){
						$sql = "delete from group_contact_phone where contact_info_id in(".implode(",", $phone_id).")";
						$command = $connection->createCommand($sql);
						$result0 = $command->execute();
					}
					if($group->group_name != "未分组"){
						$group->delete();
					}
					if($result0){
						$result ['ret_num'] = 0;
						$result ['ret_msg'] = '操作成功';
					}
			    }else{
			    	if($group->group_name != "未分组"){
			    		$group->delete();
			    	}			    	
			    	$result ['ret_num'] = 0;
			    	$result ['ret_msg'] = '操作成功';			    	
			    }
			}					
		}
		echo json_encode( $result );
	}
	
	/**
	 * 编辑分组成员
	 */
	public function actionEditmember(){
		$this->check_key();
		$user = $this->check_user();
		$userid = Frame::getStringFromRequest('user_id');
		$groupid = Frame::getIntFromRequest('group_id');
		$connection = Yii::app()->db;
		$sql = "select id from group_contact where group_name = '未分组' and member_id = {$user->id}";
		$command = $connection->createCommand($sql);
		$gid = $command->queryAll();
		$sql = "update group_contact_info set group_id = {$gid[0]['id']} where group_id = {$groupid}";		
		$command = $connection->createCommand($sql);
		$result0 = $command->execute();
		if($userid && $groupid){
			$sql = "update group_contact_info set group_id = {$groupid} where id in ({$userid})";
			$command = $connection->createCommand($sql);
			$result0 = $command->execute();

			// $result ['ret_num'] = 0;
			// $result ['ret_msg'] = '操作成功';
			
			// if($result0){
			// 	$result ['ret_num'] = 0;
			// 	$result ['ret_msg'] = '操作成功';
			// }else{
			// 	$result ['ret_num'] = 106;
			// 	$result ['ret_msg'] = '编辑分组成员失败';
			// }
		}

		$result ['ret_num'] = 0;
		$result ['ret_msg'] = '操作成功';
			
		echo json_encode( $result );
	}

	/**
	*  单个移动分组
	**/
	public function actionChangegroup(){
		$this->check_key();
		$user = $this->check_user();
		$userid = Frame::getStringFromRequest('user_id');
		$groupid = Frame::getIntFromRequest('group_id');
		$connection = Yii::app()->db;
		if($userid && $groupid){
			$sql = "update group_contact_info set group_id = {$groupid} where id in ({$userid})";
			$command = $connection->createCommand($sql);
			$result0 = $command->execute();	
			if($result0){
				$result ['ret_num'] = 0;
				$result ['ret_msg'] = '操作成功';
			}else{
				$result ['ret_num'] = 106;
				$result ['ret_msg'] = '编辑分组成员失败';
			}
		}else {
			$result ['ret_num'] = 106;
			$result ['ret_msg'] = '参数错误';
		}
			
		echo json_encode( $result );
	}

	/**
	 * 定时匹配
	 */
	public function actionIntervalmatch(){
		$this->check_key();
		$user = $this->check_user();
		$phone = Frame::getStringFromRequest('phone');
		if (empty ( $phone )) {
			
		}
		$aphone = explode(",", $phone);
		$re = array();
		$benbenp = array();
		$baixingp = array();
		$connection = Yii::app()->db;
		$sql1 = "select phone from member where phone in ({$phone})";
		$command = $connection->createCommand($sql1);
		$result1 = $command->queryAll();
		foreach ($result1 as $va){
			$benbenp[] = $va['phone'];
		}
		
		$sql2 = "select phone from bxapply where phone in ({$phone})";
		$command = $connection->createCommand($sql2);
		$result2 = $command->queryAll();
		foreach ($result2 as $va){
			$baixingp[] = $va['phone'];
		}
		
		foreach ($aphone as $val){
			if(in_array($val, $benbenp) && in_array($val, $baixingp)){
				$re[] = array(
						'phone'=>$val,
				        'is_benben'=>1,
				        'is_baixing'=>1
				);
			}elseif(in_array($val, $benbenp)){
				$re[] = array(
						'phone'=>$val,
				        'is_benben'=>1,
				        'is_baixing'=>0
				);
			}elseif(in_array($val, $baixingp)){
				$re[] = array(
						'phone'=>$val,
				        'is_benben'=>0,
				        'is_baixing'=>1
				);
			}else{
				$re[] = array(
						'phone'=>$val,
						'is_benben'=>0,
						'is_baixing'=>0
				);
			}									
		}
		$result ['ret_num'] = 0;
		$result ['ret_msg'] = '操作成功';
		$result ['info'] = $re;
		echo json_encode( $result );
	}
	
	/**
	 * 修改通讯录联系人姓名
	 */
	public function actionEditname(){
		$this->check_key();
		$user = $this->check_user();
		$id = Frame::getIntFromRequest('id');
		$name = Frame::getStringFromRequest('name');
		if(!$name){
			$result ['ret_num'] = 5238;
			$result ['ret_msg'] = '姓名为空';
			echo json_encode( $result );
			die();
		}
		$contact = GroupContactInfo::model()->findByPk($id);
		if($contact){
			if($contact->name == $name){
				$result ['ret_num'] = 5237;
				$result ['ret_msg'] = '姓名已存在';
				echo json_encode( $result );
				die();
			}
			$contact->name = $name;
			if($contact->update()){
				$result ['ret_num'] = 0;
				$result ['ret_msg'] = '操作成功';
				$result ['name'] = $name;
			}
		}else{
			$result ['ret_num'] = 1803;
			$result ['ret_msg'] = '联系人信息不存在';
		}
		echo json_encode( $result );
	}
	
	/**
	 * 添加联系人到通讯录
	 */
	public function actionAddcontact(){
		$this->check_key();
		$user = $this->check_user();
		$name = Frame::getStringFromRequest('name');
		$phone = Frame::getStringFromRequest('phone');
		$groupid = Frame::getStringFromRequest('group_id');
		if(!$name || !$phone){
			$result ['ret_num'] = 1692;
			$result ['ret_msg'] = '姓名或手机号码为空';
			echo json_encode( $result );
			die();
		}
		
		$connection = Yii::app()->db;
		//是否与自己通讯录好友的号码、或者百姓网短号重复
		$sql = "select a.id from group_contact_info a inner join group_contact_phone b on a.id = b.contact_info_id
				     where a.member_id = {$user->id} and (b.phone = '{$phone}' or b.is_baixing = '{$phone}')";
		$command = $connection->createCommand($sql);
		$result1 = $command->queryAll();
		if($result1[0]){
			$result ['ret_num'] = 1693;
			$result ['ret_msg'] = '该联系人号码已存在';
			echo json_encode( $result );
			die();
		}

		$group = GroupContact::model()->find("id = {$groupid} and member_id = {$user->id}");
		if(!$group){
			$result ['ret_num'] = 1695;
			$result ['ret_msg'] = '分组ID不正确';
			echo json_encode( $result );
			die();
		}
		//该号码是否是犇犇用户
			$info = Member::model()->find("phone = '{$phone}'");
			if($info){
				$is_benben = $info->benben_id;
				$poster = $info->poster ? URL.$info->poster : "";
				$huanxin_username = $this->eraseNull($info->huanxin_username);
			}else{
				$is_benben = 0;
				$poster = "";
				$huanxin_username = "";
			}
			//该号码是否是百姓网用户
			$binfo = Bxapply::model()->find("phone = '{$phone}' and status = 3");
			if($binfo){
				$is_baixing = $binfo->short_phone;
			}else{
				$is_baixing = 0;
			}
			$PinYin = new PYInitials('utf8');
			$py = substr($PinYin->getInitials($name),0,1);
			$py = strtoupper($py);
			
			$contact = new GroupContactInfo();
			$contact->group_id = $groupid;
			$contact->name = $name;
			$contact->pinyin = $py;
			$contact->created_time = time();
			$contact->member_id = $user->id;
			$contact->benben_id = $is_benben;
			if($contact->save()){
				$contactphone = new GroupContactPhone();
				$contactphone->phone = $phone;
				$contactphone->contact_info_id = $contact->id;
				$contactphone->is_benben = $is_benben;
				$contactphone->is_baixing = $is_baixing;
				$contactphone->save();
// 				$phone = array(
// 						"nick_name"=> $info ? $info->nick_name : "",
// 						"benben_id"=> $info ? $info->benben_id : "",
// 						"poster"=> $poster,
// 						"is_benben"=>$is_benben,
// 						"is_baixing"=>$is_baixing,
// 						"phone"=>$phone,
// 						"huanxin_username"=>$info ? $info->huanxin_username : "",
// 				);
				$result ['ret_num'] = 0;
				$result ['ret_msg'] = '操作成功';
				$result['user'] = array(
						"name"=> $name,
						"nick_name"=> $info ? $info->nick_name : "",
						"benben_id"=> $info ? $info->benben_id : "",
						"group_id"=> $groupid,
						"contact_info_id"=> $contact->id,
						"poster"=> $poster,
						"pinyin"=> $py,
						"is_benben"=>$is_benben,
						"is_baixing"=>$is_baixing,
						"huanxin_username"=>$info ? $info->huanxin_username : "",
						"phone"=>$phone
				);				
			}else{
				$result ['ret_num'] = 1694;
				$result ['ret_msg'] = '添加失败';
			}
			echo json_encode( $result );
		
	}
	
	/**
	 * 添加通讯录联系人手机号
	 */
	public function actionAddphone(){
		$this->check_key();
		$user = $this->check_user();
		$id = Frame::getIntFromRequest('id');		
		$phone = Frame::getStringFromRequest('phone');
		
		if (empty($phone) || strlen($phone)<3 || strlen($phone)>17) {
			$result ['ret_num'] = 1692;
			$result ['ret_msg'] = '号码最少3位,最多17位';
			echo json_encode( $result );
			die();
		}
		$contact = GroupContactInfo::model()->find("id = {$id} and member_id = {$user->id}");
		if($contact){
			// $re = GroupContactPhone::model()->find("contact_info_id = {$id} and phone = '{$phone}'");
			// if($re){
			// 	$result ['ret_num'] = 5230;
			// 	$result ['ret_msg'] = '手机号码已存在';
			// 	echo json_encode( $result );
			// 	die();
			// }
			
			$connection = Yii::app()->db;
			//是否与自己通讯录好友的号码、或者百姓网短号重复
			$sql = "select a.id from group_contact_info a inner join group_contact_phone b on a.id = b.contact_info_id
					     where a.member_id = {$user->id} and (b.phone = '{$phone}' or b.is_baixing = '{$phone}')";
			$command = $connection->createCommand($sql);
			$result1 = $command->queryAll();

			if($result1[0]){
				$result ['ret_num'] = 5230;
				$result ['ret_msg'] = '该号码已存在你的通讯录中';
				echo json_encode( $result );
				die();
			}
			
			//该号码是否是犇犇用户
			$info = Member::model()->find("phone = '{$phone}'");
			if($contact->benben_id){
				$or_info = Member::model()->find("benben_id = {$contact->benben_id}");
			}
			if($info){
				$nick_name = $info->nick_name;
				$is_benben = $info->benben_id;
				$poster = $info->poster ? URL.$info->poster : "";
				$huanxin_username = $this->eraseNull($info->huanxin_username);
				if(!$contact->benben_id){
					$contact->benben_id = $info->benben_id;
					$contact->update();
				}
			}else{
				$nick_name = "";
				$is_benben = 0;
				$poster = "";
				$huanxin_username = "";
			}
			//该号码是否是百姓网用户
			$binfo = Bxapply::model()->find("phone = '{$phone}' and status = 3");
			if($binfo){
				$is_baixing = $binfo->short_phone;
			}else{
				$is_baixing = 0;
			}
			$contactphone = new GroupContactPhone();
			$contactphone->phone = $phone;
			$contactphone->contact_info_id = $id;
			$contactphone->is_benben = $is_benben;
			$contactphone->is_baixing = $is_baixing;
			if($contactphone->save()){
				$result ['ret_num'] = 0;
				$result ['ret_msg'] = '操作成功';
				$result['phone_info'] = array(
					"id"=>$contactphone->id,
					"contact_info_id"=>$contactphone->contact_info_id,		
					"phone"=>$phone,
					"is_benben"=>$is_benben,
					"is_baixing"=>$is_baixing,
					"poster"=>$poster,
					"huanxin_username"=>$huanxin_username,		
				);
				$result['contact_info'] = array(
					"id"=>$id,//group_contact_info表的ID
					"group_id"=>$contact->group_id,//分组ID
					"name"=>$contact->name,
					"pinyin"=>$contact->pinyin,
					"created_time"=>$contact->created_time,
					"is_benben"=>$or_info ? $or_info->benben_id : $is_benben,
					"is_baixing"=>$is_baixing,
					"poster"=>$or_info ? ($or_info->poster ? URL.$or_info->poster : "") : $poster,
					"huanxin_username"=>$or_info ? $or_info->huanxin_username : $huanxin_username,	
					/*"phone"=>array(
							"phone"=>$phone,
							"is_benben"=>$is_benben,
							"is_baixing"=> $is_baixing,
							"poster"=>$poster,
							"nick_name"=>$nick_name
					)*/
			);
			}else{
				$result ['ret_num'] = 1803;
				$result ['ret_msg'] = '保存失败，换一个手机号试试';
			}
		}else{
			$result ['ret_num'] = 1803;
			$result ['ret_msg'] = '联系人信息不存在';
		}
		echo json_encode( $result );		
	}
	
	/**
	 * 删除通讯录联系人手机号
	 */
	public function actionDelphone(){
		$this->check_key();
		$user = $this->check_user();
		$id = Frame::getIntFromRequest('id');
		$phone = Frame::getStringFromRequest('phone');
		if (empty ( $phone )) {
			$result ['ret_num'] = 1802;
			$result ['ret_msg'] = '联系人号码为空';
			echo json_encode( $result );
			die ();
		}
		$connection = Yii::app()->db;
		//$contact = GroupContactInfo::model()->findByPk($id);
		$contact = GroupContactInfo::model()->find("id = {$id} and member_id = {$user->id}");
		if($contact){
			$contactphone = GroupContactPhone::model()->find("phone = '{$phone}' and contact_info_id = {$id} ");
			if($contactphone){
				$is_benben = $contactphone->is_benben;
				if($contactphone->delete()){
					
					//删除好友关系
					if($is_benben){
						$sql = "select c.contact_info_id,c.phone from 
									(select a.contact_info_id,a.phone from benben.group_contact_phone a 
									left join benben.group_contact_info b 
									on a.contact_info_id=b.id
									where b.member_id={$user->id} and
									is_benben>0 order by a.id asc
									) c group by c.contact_info_id;";
						$command = $connection->createCommand($sql);
						$result1 = $command->queryAll();
						$flag = 1;
						if($result1[0]){
							foreach ($result1 as $va){
								if($va['phone'] == $phone){
									$flag = 0;
									break;
								}
							}
						}
						/*if($flag){
							$friend1 = Member::model()->find("benben_id = {$is_benben}");
							$id1 = $friend1->id;
							$id2 = $contact->member_id;
							$re = FriendRelate::model()->find("(friend_id1 = {$id1} and friend_id2 = {$id2}) or (friend_id1 = {$id2} and friend_id2 = {$id1})");
							if($re){
								$re->delete();
							}
						}*/						
						
					}
					//看是否有第二个号码
			/*	    if($result1[0]){
							foreach ($result1 as $val){
								if(($val['contact_info_id'] == $id) && $val['phone']){
									//添加好友关系
									$f1 = Member::model()->find("benben_id = {$contactphone->is_benben}");
									$t = time();									
									$sql = "insert into friend_relate (friend_id1,friend_id2,created_time) values ({$contact->member_id},{$f1->id},{$t})";
									$command = $connection->createCommand($sql);
									$result2 = $command->execute();
									break;
								}
							}
					}*/					
				}
				$result ['ret_num'] = 0;
				$result ['ret_msg'] = '操作成功';
			}else{
				$result ['ret_num'] = 1802;
				$result ['ret_msg'] = '联系人号码为空';
			}
		}else{
			$result ['ret_num'] = 1803;
			$result ['ret_msg'] = '联系人信息不存在';
		}
		echo json_encode( $result );
	}
	
	/**
	 * 删除通讯录联系人
	 */
	public function actionDelcontact(){
		$this->check_key();
		$user = $this->check_user();
		$id = Frame::getIntFromRequest('id');
		$connection = Yii::app()->db;
		if(!$id && $bid){
			$userid = Member::model()->find("benben_id = {$bid}");
			$sql1 = "select id,member_id from group_contact where member_id = {$userid->id}";
			$command = $connection->createCommand($sql1);
			$result1 = $command->queryAll();
			
			$sql2 = "select a.id,a.contact_info_id 
			from group_contact_phone a inner join group_contact_info b on a.contact_info_id = b.id
			inner join group_contact d on d.id = b.group_id inner join member c on a.phone = c.phone
			where d.member_id = {$userid->id} and c.phone = '{$userid->phone}' limit 1";
			$command = $connection->createCommand($sql2);
			$result2 = $command->queryAll();
		}
		
		$sql = "select a.id from group_contact_info a inner join group_contact b on a.group_id = b.id 
		where a.id = {$id} and b.member_id = {$user->id}";
		$command = $connection->createCommand($sql);
		$r1 = $command->queryAll();//var_dump($r1);exit();
		if(!$r1){
			$result ['ret_num'] = 0;
			$result ['ret_msg'] = '操作成功';
			echo json_encode( $result );
			die();
		}
		$contact = GroupContactInfo::model()->findByPk($id);
		if($contact){ 
			$connection = Yii::app()->db;
			$sql = "select id, huanxin_username from member where phone in (select phone from group_contact_phone where contact_info_id = {$contact->id})";
			$command = $connection->createCommand($sql);
			$re1 = $command->queryAll();//var_dump($re1);exit();
			if($re1){
				foreach ($re1 as $value){
					$sqlf = "delete from friend_relate where ((friend_id1 = {$value['id']} and friend_id2 = {$user->id}) or (friend_id1 = {$user->id} and friend_id2 = {$value['id']})) and status = 1";
					$command = $connection->createCommand($sqlf);
					$re2 = $command->execute();
					//删除环信好友
					$options = array(
							"client_id"=>CLIENT_ID,
							"client_secret"=>CLIENT_SECRET,
							"org_name"=>ORG_NAME,
							"app_name"=>APP_NAME
					);
					$huanxin = new Easemob($options);
					$resulh = $huanxin->deleteFriend($user->huanxin_username, $value['huanxin_username']);
					$reh = json_decode($resulh, true);
				}
			}
			
			$sql = "delete from group_contact_phone where contact_info_id = {$contact->id}";
			$command = $connection->createCommand($sql);
			$re = $command->execute();			
			if($contact->delete()){
				$result ['ret_num'] = 0;
				$result ['ret_msg'] = '操作成功';
			}
		}else{
			$result ['ret_num'] = 0;
			$result ['ret_msg'] = '操作成功';
		}
		echo json_encode( $result );
	}
	
	/**
	 * 用户查找
	 */
	public function actionSearch(){
		$this->check_key();
		$user = $this->check_user();
		$keyword = Frame::getStringFromRequest('keyword');
		$connection = Yii::app()->db;
		if($keyword){
			$sql = "select id,nick_name,poster,phone,sex from member where nick_name like '%{$keyword}%' or phone like '%{$keyword}%' or benben_id like '%{$keyword}%' order by id desc";
		}else{
			$sql = "select id,nick_name,poster,phone,sex from member order by id desc";
		}		
		$command = $connection->createCommand($sql);
		$result0 = $command->queryAll();
		foreach ($result0 as $key => $valu){
			if($valu['poster']){
				$result0[$key]['poster'] = URL.$valu['poster'];
			}else{
				$result0[$key]['poster'] = "";
			}
		}		
		$result ['ret_num'] = 0;
		$result ['ret_msg'] = '操作成功';
		$result ['member_info'] = $result0;					
		echo json_encode( $result );
	}
	
}